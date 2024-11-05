<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$lib_company_name=return_library_array( "select company_name,id from lib_company", "id", "company_name");
	$arr = array(0=>$lib_company_name,2=>$month,5=>$row_status);	
	echo  create_list_view ( "list_view", "Company Name,Year Name,Year Start,Year End,Period Name,Is Current", "120,100,100,100,100,100","700","250",0, "select  company_id,year_start,year_start_date,year_end_date,period_name,status_active,id from lib_ac_period_mst  where status_active=1 and is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"company_id,0,0,0,0,status_active", $arr ,"company_id,year_start,year_start_date,year_end_date,period_name,status_active","../accounts/requires/accounting_period_controller", 'setFilterGrid("list_view",-1);','0,0,3,3,0,0' ) ;
	exit();
}

if ($action=="load_php_data_to_form")
{
	
	$nameArray=sql_select( "select a.id,a.company_id,a.year_start,a.year_start_date,a.year_end_date,a.period_name,a.status_active, b.id as bid,b.mst_id,b.period_starting_date,b.period_ending_date,b.financial_period,b.period_locked  from lib_ac_period_mst a, lib_ac_period_dtls b where a.id='$data' and a.id=b.mst_id " );
	$i=0;
	$data_count=count($nameArray);
	foreach ($nameArray as $inf)
	{
		$i++;
		if ($i==$data_count)
		{
			echo "document.getElementById('txt_acc_dates_".$i."').value = '".($inf[csf("year_start_date")])."__".($inf[csf("year_end_date")])."';\n"; 

			echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";  
			echo "document.getElementById('cbo_starting_year').value = '".($inf[csf("year_start")])."';\n";    
			echo "document.getElementById('cbo_starting_month').value = '".date("M",strtotime($inf[csf("year_start_date")]))."';\n"; 
			echo "document.getElementById('cbo_ending_month').value = '".date("M",strtotime($inf[csf("year_end_date")]))."';\n"; 
			echo "document.getElementById('txt_period_name').value = '".($inf[csf("period_name")])."';\n"; 
			echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
			echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		 	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_accounting_period',1);\n"; 
		}
		echo "document.getElementById('txt_acc_dates_".$i."').value = '".($inf[csf("period_starting_date")])."__".($inf[csf("period_ending_date")])."';\n"; 
		
		echo "document.getElementById('update_id_dtls".$i."').value = '".($inf[csf("bid")])."';\n"; 
		echo "document.getElementById('accounting_period_starting_date_".$i."').value = '".date("F d",strtotime($inf[csf("period_starting_date")]))."';\n"; 
		echo "document.getElementById('accounting_period_ending_date_".$i."').value = '".date("F d",strtotime($inf[csf("period_ending_date")]))."';\n"; 
		echo "document.getElementById('accounting_period_title_".$i."').value = '".($inf[csf("financial_period")])."';\n";
	
		if ($inf[csf("period_locked")]==1 )
		{
			echo "document.getElementById('accounting_period_locked_".$i."').checked = '".($inf[csf("period_locked")])."';\n"; 
		}
		else 
		{
			echo "document.getElementById('accounting_period_locked_".$i."').checked = false\n"; 
		}
	}
	exit();
}

if ($action=="save_update_delete")
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
		$txt_acc_dates=explode("__",str_replace("'","",$txt_acc_dates_1));
		$txt_acc_dateLast=explode("__",str_replace("'","",$txt_acc_dates_15));
		$mst_id=return_next_id( "id", "lib_ac_period_mst", 1 ) ; 
		$field_array="id,company_id,year_start,year_start_date,year_end_date,period_name,inserted_by,insert_date,status_active,is_deleted";
		
		$data_array="(".$mst_id.",".$cbo_company_name.",".$cbo_starting_year.",'".date("j-M-Y",strtotime($txt_acc_dates[0]))."','".date("j-M-Y",strtotime($txt_acc_dateLast[0]))."',".$txt_period_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
		
		
		
		//================Insert in to second table or details table===========================	
	 
		$id=return_next_id( "id", "lib_ac_period_dtls", 1 ); 
		$field_array1="id,mst_id,month_id,period_starting_date,period_ending_date,financial_period,period_locked,inserted_by,insert_date,status_active,is_deleted";
			 
		for($i=1; $i<=15; $i++)
		{
			$k=$i-1;
			//$accounting_period_id = "accounting_period_id_".$i;
			$accounting_period_starting_date="accounting_period_starting_date_".$i;
			$account_period_start_date="account_period_start_date_".$i;
			$accounting_period_ending_date="accounting_period_ending_date_".$i;
			$accounting_period_title="accounting_period_title_".$i;
			$accounting_period_locked="accounting_period_locked_".$i;
			$txt_acc_dates="txt_acc_dates_".$i;
			$txt_acc_dates=explode("__",str_replace("'","",$$txt_acc_dates));
			
			if (str_replace("'","",$$accounting_period_title)!="")
			{
				if(str_replace("'","",$$accounting_period_locked)==1) $year_lock=1; else $year_lock=0;
				
				
				if ($i!=1) $data_array1 .=",";
				$data_array1.="(".$id.",".$mst_id.",".$k.",'".date("j-M-Y",strtotime($txt_acc_dates[0]))."','".date("j-M-Y",strtotime($txt_acc_dates[1]))."',".$$accounting_period_title.",".$year_lock.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
				$id=$id+1;
			}
		}
		$rID=sql_insert("lib_ac_period_mst",$field_array,$data_array,0);
		// echo "0**"."insert into lib_ac_period_dtls (".$field_array1.") values ".$data_array1; die;	 
		$rID1=sql_insert("lib_ac_period_dtls",$field_array1,$data_array1,1);
		//check_table_status( $_SESSION['menu_id'],0);

		if($db_type==0)
		{
			if($rID && $rID1){
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
			if($rID && $rID1)
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
	
	else if ($operation==1)   // Update Here===========================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$txt_acc_dates=explode("__",str_replace("'","",$txt_acc_dates_1));
		$txt_acc_dateLast=explode("__",str_replace("'","",$txt_acc_dates_15));

		$field_array="company_id*year_start*year_start_date*year_end_date*period_name*updated_by*update_date*status_active*is_deleted";
		$data_array="".$cbo_company_name."*".$cbo_starting_year."*'".date("j-M-Y",strtotime($txt_acc_dates[0]))."'*'".date("j-M-Y",strtotime($txt_acc_dateLast[0]))."'*".$txt_period_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
		$rID=sql_update("lib_ac_period_mst",$field_array,$data_array,"id","".$update_id."",0);
		
		$id_arr=array();
		$data_array_up=array();
		$field_array1="period_starting_date*period_ending_date*financial_period*period_locked*updated_by*update_date*status_active*is_deleted";
		for($i=1; $i<=15; $i++)
		{
			$k=$i-1;
			//$accounting_period_id = "accounting_period_id_".$i;
			$accounting_period_starting_date="accounting_period_starting_date_".$i;
			$accounting_period_ending_date="accounting_period_ending_date_".$i;
			$accounting_period_title="accounting_period_title_".$i;
			$accounting_period_locked="accounting_period_locked_".$i;
			$update_id_dtls="update_id_dtls".$i;
			
			$txt_acc_dates="txt_acc_dates_".$i;
			$txt_acc_dates=explode("__",str_replace("'","",$$txt_acc_dates));
			
			if (str_replace("'","",$$accounting_period_title)!="")
			{ 
			   if(str_replace("'","",$$accounting_period_locked)==1) $year_lock=1; else $year_lock=0;
				$id_arr[]=str_replace("'",'',$$update_id_dtls);
				$data_array_up[str_replace("'",'',$$update_id_dtls)] =explode(",",("'".date("j-M-Y",strtotime($txt_acc_dates[0]))."','".date("j-M-Y",strtotime($txt_acc_dates[1]))."',".$$accounting_period_title.",".$year_lock.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0"));
			 
			}
		}
		$rID=sql_update("lib_ac_period_mst",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=execute_query(bulk_update_sql_statement( "lib_ac_period_dtls", "id", $field_array1, $data_array_up, $id_arr ),1);
		
		//$rID=sql_update("lib_ac_period_dtls",$field_array1,$data_array1,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID)
			{
			mysql_query("COMMIT");  
			echo 0;
			}
			else
			{
			mysql_query("ROLLBACK"); 
			echo 10;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			 if($rID && $rID1)
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
		die;
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
		
		$rID=sql_delete("lib_ac_period_mst",$field_array,$data_array,"id","".$update_id."",1);
		
		$field_array1="updated_by*update_date*status_active*is_deleted";
	    $data_array1="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID1=sql_delete("lib_ac_period_dtls",$field_array1,$data_array1,"id","".$$update_id_dtls."",1);

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
			 if($rID)
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
		die;
	}
}

?>
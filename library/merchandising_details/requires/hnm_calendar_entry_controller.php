<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$weekArr=array();
for($i=1;$i<=53;$i++){
 $weekArr[$i]="week-".$i;
}
if ($action=="sub_department_list_view")
{
	for($i=1;$i<=53;$i++){
		$weekArr[$i]="week-".$i;
	}
	$arr=array(2=>$weekArr);
	echo  create_list_view ( "list_view", "Buyer Name,Year, Week", "120,80,100","450","220",1, "SELECT a.year, a.week, b.buyer_name, a.id FROM lib_hnm_calendar a, lib_buyer b WHERE a.buyer_id = b.id AND a.status_active =1 AND a.is_deleted =0 order by id", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"0,0,week", $arr, "buyer_name,year,week", "../merchandising_details/requires/hnm_calendar_entry_controller", 'setFilterGrid("list_view",-1);',''); 
}

if ($action=="load_drop_down_week")
{


	echo create_drop_down( "cbo_week_id", 100, $weekArr, 0, 1, "-Select-",$data, "", "", "" );
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "SELECT year, week,buyer_id, id,from_date,to_date FROM lib_hnm_calendar WHERE id='$data'" );
	
	foreach ($nameArray as $inf)
	{
		
		echo "document.getElementById('cbo_year').value = '".($inf[csf("year")])."';\n";    
		echo "document.getElementById('cbo_week_id').value  = '".($inf[csf("week")])."';\n";
		echo "document.getElementById('txt_from_date').value  = '".change_date_format($inf[csf("from_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('txt_to_date').value  = '".change_date_format($inf[csf("to_date")],"dd-mm-yyyy","-")."';\n";
		echo "document.getElementById('cbo_buyer_id').value = ".($inf[csf("buyer_id")]).";\n";    
		echo "document.getElementById('update_id').value = ".($inf[csf("id")]).";\n";    
		// echo "load_drop_down('requires/hnm_calendar_entry_controller', '$str', 'load_drop_down_week', 'week_id' );\n";    
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_hnm_calender',1);\n";  
	}
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here============================================
	{
		// if (is_duplicate_field( "sub_department_name", "lib_pro_sub_deparatment", "sub_department_name=$txt_sub_dep_name and buyer_id=$cbo_buyer_id and department_id=$cbo_department_id  and is_deleted=0" ) == 1)
		// {
		// 	echo 11; die;
		// }
		// else
		// {

			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
	
		$id=return_next_id( "id", "lib_hnm_calendar", 1 ) ;
		$field_array="id,buyer_id,year,from_date,to_date,week,inserted_by,insert_date,status_active,is_deleted";
		$data_array="(".$id.",".$cbo_buyer_id.",".$cbo_year.",".$txt_from_date.",".$txt_to_date.",".$cbo_week_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";
	
	  $rID=sql_insert("lib_hnm_calendar",$field_array,$data_array,1);
			
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
	//	}
	}
	
	else if ($operation==1)   // Update Here=========================================================
	{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			// if (is_duplicate_field( "sub_department_name", "lib_pro_sub_deparatment", "sub_department_name=$txt_sub_dep_name and buyer_id=$cbo_buyer_id and department_id=$cbo_department_id  and is_deleted=0 and id <> $update_id " ) == 1)
			// {
			// 	echo 11; disconnect($con); die;
			// }
			
			
		
			
			$field_array="buyer_id*year*from_date*to_date*week*update_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_buyer_id."*".$cbo_year."*".$txt_from_date."*".$txt_to_date."*".$cbo_week_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'1'*'0'";	
			$rID=sql_update("lib_hnm_calendar",$field_array,$data_array,"id","".$update_id."",1);
		
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
	
	
	else if ($operation==2)   // Delete Here===================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="update_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_hnm_calendar",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");  
				echo "2**".$rID;
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
}


?>
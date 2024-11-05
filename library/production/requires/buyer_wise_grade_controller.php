<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	if ($operation==0)  // Insert Here
	{
			$check_dup_sql="SELECT a.buyer_id, b.range_serial from buyer_wise_grade_mst a ,buyer_wise_grade_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
			$check_dup_arr=array();
			foreach(sql_select($check_dup_sql) as $vals)		
			{
				$check_dup_arr[$vals[csf("buyer_id")]][$vals[csf("range_serial")]]=$vals[csf("range_serial")];
			}
			$id=return_next_id( "id", "buyer_wise_grade_mst", 1 ) ;
			$dtls_id=return_next_id( "id", "buyer_wise_grade_dtls", 1 ) ;
			$field_array="id,buyer_id, range_from,range_to,grade,inserted_by,insert_date, status_active, is_deleted";
			$data_array="(".$id.",".$cbo_buyer_id.",".$txt_from.",".$txt_to.",".$cbo_grade.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			
			$field_array_dtls = "id, mst_id, range_serial, grade,  inserted_by, insert_date, status_active, is_deleted";
			$data_array_dtls = "";
			$from=str_replace("'","", $txt_from);
			$to=str_replace("'","", $txt_to);
			$kk=0;
			$serial=$from;
			for ($i =$from; $i <= $to; $i++)
			 {
			 	if($check_dup_arr[str_replace("'", "", $cbo_buyer_id)][$i])$kk++;
				 
				if ($data_array_dtls != "") $data_array_dtls .= ",";

				$data_array_dtls .= "(" . $dtls_id . "," . $id . ",'" . $serial. "',".$cbo_grade."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
				$dtls_id++;
				$serial++;
			}
			if($kk>0) {echo "11**0";disconnect($con);die;}
			$rID=sql_insert("buyer_wise_grade_mst",$field_array,$data_array,1);
			$rID_dtls=sql_insert("buyer_wise_grade_dtls",$field_array_dtls,$data_array_dtls,1);
			 
			if($db_type==0)
			{
				if($rID && $rID_dtls ){
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
				 if($rID && $rID_dtls )
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
	else if ($operation==1)   // Update Here
	{
			$dtls_id=return_next_id( "id", "buyer_wise_grade_dtls", 1 ) ;
			$check_dup_sql="SELECT a.buyer_id, b.range_serial from buyer_wise_grade_mst a ,buyer_wise_grade_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id <> $update_id  ";
			$check_dup_arr=array();
			foreach(sql_select($check_dup_sql) as $vals)		
			{
				$check_dup_arr[$vals[csf("buyer_id")]][$vals[csf("range_serial")]]=$vals[csf("range_serial")];
			}
			$field_array="buyer_id*range_from*range_to*grade*updated_by*update_date";
			$data_array="".$cbo_buyer_id."*".$txt_from."*".$txt_to."*".$cbo_grade."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("buyer_wise_grade_mst",$field_array,$data_array,"id","".$update_id."",1);
			if($rID)
			{
				

				$field_array_dtls = "id, mst_id, range_serial, grade,  inserted_by, insert_date, status_active, is_deleted";
				$data_array_dtls = "";
				$from=str_replace("'","", $txt_from);
				$to=str_replace("'","", $txt_to);
				$kk=0;
				for ($i =$from; $i <= $to; $i++)
				{
					if($check_dup_arr[str_replace("'", "", $cbo_buyer_id)][$i])$kk++;

					if ($data_array_dtls != "") $data_array_dtls .= ",";

					$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",'" . $i. "',".$cbo_grade."," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
					$dtls_id++;
				}
				if($kk>0) {echo "11**0";disconnect($con);die;}
				else
				{
					$dtls_del= execute_query("DELETE from buyer_wise_grade_dtls where mst_id=$update_id ",0);
				}
				$rID_dtls=sql_insert("buyer_wise_grade_dtls",$field_array_dtls,$data_array_dtls,1);


			}
			//echo "10**insert into buyer_wise_grade_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			//echo "10**$rID && $dtls_del && $rID_dtls";
			if($db_type==0)
			{
				if($rID && $dtls_del && $rID_dtls ){
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
			 if($rID && $dtls_del && $rID_dtls )
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
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("buyer_wise_grade_mst",$field_array,$data_array,"id","".$update_id."",1);
		$rID2=sql_delete("buyer_wise_grade_dtls",$field_array,$data_array,"mst_id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID && $rID2 ){
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
			 if($rID  && $rID2)
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

if ($action=="penalty_list_view")
{ 
	$buyer_arr = return_library_array("select id, buyer_name from  lib_buyer", "id", "buyer_name");
	$arr=array(0=>$buyer_arr, 3=>$fabric_shade);
	echo  create_list_view ( "list_view", "Buyer Name,From,To,Grade", "150,100,100,50","600","220",1, "SELECT id, buyer_id, range_from, range_to, grade from buyer_wise_grade_mst where status_active=1 and is_deleted=0 order by id desc", "get_php_form_data", "id","'load_php_data_to_form'", 1, "buyer_id,0,0,grade", $arr , "buyer_id,range_from,range_to,grade", "../production/requires/buyer_wise_grade_controller", 'setFilterGrid("list_view",-1);' ) ;
                	 	
}

if ($action=="load_php_data_to_form")//load list view data to the form
{
	$nameArray=sql_select( "SELECT id, buyer_id, range_from, range_to, grade  from buyer_wise_grade_mst where id='$data'" );
	foreach ($nameArray as $inf)
	{
		 
		echo "document.getElementById('cbo_buyer_id').value = '".($inf[csf("buyer_id")])."';\n";    
		echo "document.getElementById('txt_from').value  = '".($inf[csf("range_from")])."';\n"; 
 		echo "document.getElementById('txt_to').value  = '".($inf[csf("range_to")])."';\n";
		echo "document.getElementById('cbo_grade').value  = '".($inf[csf("grade")])."';\n";		 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_buyer_penalty_entry',1);\n";  
	}
}


?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//for load_drop_down_party
if ($action=="load_drop_down_party")
{
	$sql="select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b,lib_supplier_tag_company c where a.id=b.supplier_id and c.supplier_id=b.supplier_id and c.tag_company=$data and b.party_type in(9,20) and a.status_active=1  group by a.id, a.supplier_name order by a.supplier_name";
	echo create_drop_down( "cbo_party_id", 120, $sql, "id,supplier_name", 1, "--Select--", $selected );
}

//for save_update_delete
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	//echo $tot_row_buyer;
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN");}
		
		
		if(str_replace("'","",$update_id)=="")
		{
			$mst_id= return_next_id("id","lib_capacity_allocation_mst",1);
			$field_array_mst="id,company_id,location_id,year_id,month_id,inserted_by,insert_date,status_active,is_deleted";
			$data_array_mst="(".$mst_id.",".$cbo_company_name.",".$cbo_location_id.",".$cbo_year_name.",".$cbo_month.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//	$rID=sql_insert("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,0);
			//$return_no=str_replace("'",'',$txt_system_id);
			//echo "10**"."insert into lib_capacity_allocation_mst (".$field_array_mst.") values ".$data_array_mst; die;
		}
		else
		{
			$mst_id=str_replace("'",'',$update_id);
			$field_array_mst="location_id*year_id*month_id*updated_by*update_date*status_active*is_deleted";
			$data_array_mst="".$cbo_location_id."*".$cbo_year_name."*".$cbo_month."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			//$rID=sql_update("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		}
		$dtls_id=return_next_id( "id", "lib_capacity_allocation_dtls",1);
		$field_array_dtls="id,mst_id,buyer_id,allocation_percentage,inserted_by,insert_date,status_active,is_deleted";
		
		for($i=1; $i<=$tot_row_buyer; $i++)
		{
			$txt_buyer= "buyer_id_".$i;
			$txt_allocation_parcentage="txt_allocation_".$i;
			$update_id_dtls="update_id_dtls_".$i;
			
			if((str_replace("'",'',$$update_id_dtls)=="")||(str_replace("'",'',$$update_id_dtls)==0)&&(str_replace("'","",$cbo_year_name))!=0)
			{
			if ($i!=1) $data_array_dtls .=",";
			$data_array_dtls.="(".$dtls_id.",".$mst_id.",".$$txt_buyer.",".$$txt_allocation_parcentage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$dtls_id=$dtls_id+1;
			}
		}
		
	   	if(str_replace("'","",$update_id)=="")
			{
			$rID=sql_insert("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,0);
			
			}
		else
			{
			$rID=sql_update("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);	
			}
		$rID1=sql_insert("lib_capacity_allocation_dtls",$field_array_dtls,$data_array_dtls,1);
	//echo "10**".$rID.'='.$rID1;die;
	
		if($db_type==0)
		{
			if( $rID && $rID1)
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
				if( $rID & $rID1)
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
		
		$field_array_mst="location_id*year_id*month_id*updated_by*update_date*status_active*is_deleted";
		$data_array_mst="".$cbo_location_id."*".$cbo_year_name."*".$cbo_month."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
		//$rID=sql_update("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		
		$id_arr=array();
		$data_array_dtls=array();
		$field_array_dtls="allocation_percentage*updated_by*update_date";
		$dtls_id=return_next_id( "id", "lib_capacity_allocation_dtls", 1 );
		$mst_id=str_replace("'",'',$update_id);
		$field_array_dtls_in="id,mst_id,buyer_id,allocation_percentage,inserted_by,insert_date,status_active,is_deleted";
		$coma=0;
		for($i=1; $i<=$tot_row_buyer; $i++)
		{
			$txt_buyer= "buyer_id_".$i;
			$txt_allocation_percentage="txt_allocation_".$i;
			$update_id_dtls="update_id_dtls_".$i;
			
			if(str_replace("'",'',$$update_id_dtls)!="")
			{
				$id_arr[]=str_replace("'",'',$$update_id_dtls);
				$data_array_dtls[str_replace("'",'',$$update_id_dtls)] =explode(",",("".$$txt_allocation_percentage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
			}
			else
			{
					if ($coma!=0) $data_array_dtls_in.=",";
						$data_array_dtls_in.="(".$dtls_id.",".$mst_id.",".$$txt_buyer.",".$$txt_allocation_percentage.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
						$dtls_id=$dtls_id+1;
						$coma++;
				
			}
		}
		
		
		$rID=sql_update("lib_capacity_allocation_mst",$field_array_mst,$data_array_mst,"id",$update_id,0);
		$rID1=execute_query(bulk_update_sql_statement("lib_capacity_allocation_dtls","id", $field_array_dtls,$data_array_dtls,$id_arr),1);
		if($data_array_dtls_in !="")
		{
		   $rID2=sql_insert("lib_capacity_allocation_dtls",$field_array_dtls_in,$data_array_dtls_in,1);
	
		}
		
		
		if($db_type==0)
		{
			if($rID && $rID1||$rID2)
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
			 if($rID && $rID1|| $rID2)
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
?>
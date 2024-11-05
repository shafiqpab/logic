<?
/*******************************************************************
|	Purpose			:	This controller is for Fabric Composition Entry which is alred saved  in detarmination page Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Zakaria
|	Creation date 	:	04.01.2021
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
*********************************************************************/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');
$con=connect();
$sel_sql="SELECT id, fab_nature_id, LOWER(fab_composition) as composition, fabric_composition_id from  lib_yarn_count_determina_mst where is_deleted=0 and entry_form=184 and fabric_composition_id=0 and fab_composition is not null order by id";
 $data=sql_select($sel_sql);
 $field_array="id,fabric_composition_name,status_active,inserted_by,insert_date";
 if(count($data)>0){
	foreach($data as $row)
	{
		$all_composition_arr=return_library_array( "select id, fabric_composition_name as fabric_composition_name from lib_fabric_composition",'fabric_composition_name','id');
		$fabric_composition_id=$all_composition_arr[$row[csf('composition')]];
		if($fabric_composition_id!=''){
			$id=$row[csf('id')];
			$update_dtls=execute_query("UPDATE  lib_yarn_count_determina_mst set fabric_composition_id='$fabric_composition_id' where id='$id'",1);
			if($update_dtls)
			{
				oci_commit($con);
				echo "Data update Successfully<br>";
			}
			else{
				oci_rollback($con);
				echo $update_id."Data not update<br>";
			}
		}
		else{
			$update_id=$row[csf('id')];
			$id = return_next_id( "id", "lib_fabric_composition", 1 );
			$data_array="(".$id.",'".$row[csf('composition')]."',1,1,'".$pc_date_time."')";
			$rID=sql_insert("lib_fabric_composition",$field_array,$data_array,1);
			if($rID)
			{
				$update_dtls=execute_query("UPDATE  lib_yarn_count_determina_mst set fabric_composition_id='$id' where id='$update_id'",1);
			}		
			if($update_dtls && $rID)
			{
				oci_commit($con);
				echo $update_id."Data Saved Successfully<br>";
			}
			else{
				oci_rollback($con);
				echo $update_id."Data not update<br>";
			}
		}
	}
}
else{
	echo "No data Found";
}
 
 
 

  
?>
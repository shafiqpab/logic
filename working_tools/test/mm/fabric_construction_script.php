<?
/*******************************************************************
|	Purpose			:	This controller is for Fabric Construction Entry which is alred saved  in  detarmination page Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Md. Zakaria
|	Creation date 	:	17.07.2021
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
//$sel_sql="SELECT a.id,a.construction,b.copmposition_id,b.percent from  lib_yarn_count_determina_mst a ,LIB_YARN_COUNT_DETERMINA_DTLS b where a.id=b.mst_id    order by b.id";
/*$all_composition_sql=sql_select("SELECT id,fabric_construction_name from lib_fabric_construction where status_active=1 and is_deleted=0");
$all_composition_arr=array();
foreach ($all_composition_sql as $value) {
	$all_composition_arr[$value[csf('fabric_construction_name')]]=$value[csf('id')];
}*/
$sel_sql="SELECT id, fab_nature_id, LOWER(construction) as construction, fabric_construction_id from  lib_yarn_count_determina_mst where is_deleted=0 and entry_form=184 and fabric_construction_id is null order by id";
 $data=sql_select($sel_sql);
 $field_array="id,fabric_construction_name,status_active,inserted_by,insert_date";
 foreach($data as $row)
 {
	//$fabric_construction_id=$all_composition_arr[$row[csf('construction')]];
	//echo __LINE__.'#'.$fabric_construction_id; die;
	$all_composition_arr=return_library_array( "select id, fabric_construction_name as fabric_construction_name from lib_fabric_construction",'fabric_construction_name','id');
	$fabric_construction_id=$all_composition_arr[$row[csf('construction')]];
	if($fabric_construction_id!=''){
		$id=$row[csf('id')];
		$update_dtls=execute_query("UPDATE  lib_yarn_count_determina_mst set fabric_construction_id='$fabric_construction_id' where id='$id'",1);
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
		$id = return_next_id( "id", "lib_fabric_construction", 1 );
		//echo __LINE__.$id; die;
		$data_array="(".$id.",'".$row[csf('construction')]."',1,1,'".$pc_date_time."')";
		//echo "INSERT into lib_fabric_construction ($field_array) value $data_array"; die;
		$rID=sql_insert("lib_fabric_construction",$field_array,$data_array,1);
		//echo "UPDATE  lib_yarn_count_determina_mst set fabric_construction_id='$id' where id='$update_id'"; die;
		if($rID)
		{
			$update_dtls=execute_query("UPDATE  lib_yarn_count_determina_mst set fabric_construction_id='$id' where id='$update_id'",1);
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
 
 
 

  
?>
<?
// die;
/*
Created by 	: Shafiq
Date 		: 03-06-2023
Comments 	: This script is applicable for new entry of sewing rej,dft and spt data from array
*/
include('../includes/common.php');
$con = connect();

$field_array = "id,defect_type,full_name,short_name,entry_page_id,status,inserted_by,insert_date,status_active,is_deleted,defect_point_id";
$field_array_dtls = "id,mst_id,entry_page_id,inserted_by,insert_date,status_active,is_deleted";
$data_array="";
$data_array_dtls="";
// $id = 143;
$user_id = 1;
$id = return_next_id("id", "lib_sewing_defect_mst", 1);
$dtls_id = return_next_id( "id", "lib_sewing_defect_dtls", 1 );
// $sew_fin_reject_type_arr
// $sew_fin_alter_defect_type
// $sew_fin_spot_defect_type
if(count($sew_fin_spot_defect_type)>0)
{
	foreach ($sew_fin_spot_defect_type as $defect_point_id => $value) 
	{
		

	    if($data_array=="") $data_array=""; else $data_array.=",";
	    $data_array.="(".$id.",2,'" . $value . "','" . $value . "',460,1," . $user_id . ",'" . $pc_date_time . "',1,0,".$defect_point_id.")";
	    $id++;

		
		if($data_array_dtls=="") $data_array_dtls=""; else $data_array_dtls.=",";
            $data_array_dtls.="(".$dtls_id.",".$id.",460," . $user_id . ",'" . $pc_date_time . "',1,0)";
		
		$dtls_id++;
			
	}
}
// echo "10**=INSERT INTO lib_sewing_defect_mst (".$field_array.") VALUES ".$data_array.""; die;
// die();
$rID = sql_insert("lib_sewing_defect_mst",$field_array,$data_array,1);
$rID2 = sql_insert("lib_sewing_defect_dtls",$field_array_dtls,$data_array_dtls,1);
// echo "10**".$rID."=".$rID2;die;
if($rID && $rID2)
{
	oci_commit($con); 
	echo "Successfully Data inserted.";
}
else
{
	oci_rollback($con); 
	echo "Failed to insert data.";
}
disconnect($con);
?>
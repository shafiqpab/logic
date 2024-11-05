<?php
$start = microtime(true);

require_once('../includes/common.php');
extract($_REQUEST);
session_start();
if($_SESSION['logic_erp']['user_id']==''){exit('Please Login First.');}

$actual_link = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
if($action == ""){
    echo "Note:<br>
    $actual_link?action=lib_yarn_type <br>
    ";
}


if($action == "lib_yarn_type"){
    //API Link: http://localhost/platform-v3.5/sync/menual_sync.php?action=lib_yarn_type
   
    $con = connect();
    $id=return_next_id( "id","approval_history", 1 ) ;
    $field_array="id,yarn_type_id,yarn_type_short_name,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted";
	
    $yarn_type_arr = return_library_array("select YARN_TYPE_ID,YARN_TYPE_SHORT_NAME from lib_yarn_type where is_deleted=0 and status_active=1 order by YARN_TYPE_SHORT_NAME", "YARN_TYPE_ID", "YARN_TYPE_SHORT_NAME");


    foreach($yarn_type_for_entry as $key => $value){
        if($value && $yarn_type_arr[$key]==''){
            if($data_array!="") $data_array.=",";
            $data_array.="(".$id.",".$key.",'".$value."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,'',1,0)";
            $id++;
        }

    }

   //echo $data_array;die;

    $rID1=sql_insert("lib_yarn_type",$field_array,$data_array,0);
    if($rID1==1)
	{
		oci_commit($con);
		echo "Success";
	}
	else
	{
		oci_rollback($con);
		echo "Sorry! Data not inserted.";
	}
    disconnect($con); 

}
 

 



 
 



?> 
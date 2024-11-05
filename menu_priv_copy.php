<?php

include('includes/common.php');
$con=connect();

$new_main_menu_id=5000;
$old_main_menu_id=5;



$check_new_main_menu=return_field_value("main_menu_id","user_priv_mst","main_menu_id=$new_main_menu_id","main_menu_id");
if($check_new_main_menu!=$new_main_menu_id){


$loca=sql_select( "select id,user_id,main_menu_id,show_priv,delete_priv,save_priv,edit_priv,approve_priv,entry_date,user_only,last_updated_by,inserted_by,last_update_date,valid from user_priv_mst where main_menu_id=$old_main_menu_id and valid=1" );
$data_id = return_next_id("id","user_priv_mst",1);
 
$table_field_menu="id,user_id,main_menu_id,show_priv,delete_priv,save_priv,edit_priv,approve_priv,entry_date,user_only,last_updated_by,inserted_by,last_update_date,valid";
foreach($loca as $row)
{
	 $data_fld_menu ="($data_id,".$row[csf('user_id')].",'".$new_main_menu_id."','".$row[csf('show_priv')]."','".$row[csf('delete_priv')]."','".$row[csf('save_priv')]."','".$row[csf('edit_priv')]."','".$row[csf('approve_priv')]."','".$row[csf('entry_date')]."','999','".$row[csf('last_updated_by')]."','".$row[csf('inserted_by')]."','".$row[csf('last_update_date')]."','".$row[csf('valid')]."')";
	 $rID=sql_insert( "user_priv_mst", $table_field_menu, $data_fld_menu,1);
	 $data_id++;
}
 
$data_id = return_next_id("id","user_priv_mst",1);
 
 
if($rID)
{
	oci_commit();
	echo "Copied Successfully";
	
}
else
{
	oci_rollback();
	echo "Copy Failed";
}
}
else
{
	echo "New Menu Id ".$check_new_main_menu." Found";
}
 

die;
?>
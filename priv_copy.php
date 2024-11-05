<?php

include('includes/common.php');


$new_id=241;
$old_user=1;


$con=connect();
$loca=sql_select( "select id,user_id,module_id,user_only,valid,entry_date from user_priv_module where user_id=$old_user and valid=1" );
$data_id = return_next_id("id","user_priv_module",1);
foreach($loca as $row)
{
	if($data_fld!='') $data_fld .=",";
	 $data_fld .="($data_id,".$new_id .",'".$row[csf('module_id')]."','999','".$row[csf('valid')]."','".$row[csf('entry_date')]."')";
	 $data_id++;
}
 
$table_field="id,user_id,module_id,user_only,valid,entry_date";
$rID=sql_insert( "user_priv_module", $table_field, $data_fld,1);

$loca=sql_select( "select id,user_id,main_menu_id,show_priv,delete_priv,save_priv,edit_priv,approve_priv,entry_date,user_only,last_updated_by,inserted_by,last_update_date,valid from user_priv_mst where user_id=$old_user and valid=1" );
$data_id = return_next_id("id","user_priv_mst",1);
 
$table_field_menu="id,user_id,main_menu_id,show_priv,delete_priv,save_priv,edit_priv,approve_priv,entry_date,user_only,last_updated_by,inserted_by,last_update_date,valid";
//$con=connect();
foreach($loca as $row)
{
	 //if($data_fld_menu!='') $data_fld_menu .=",";
	 $data_fld_menu ="($data_id,".$new_id .",'".$row[csf('main_menu_id')]."','".$row[csf('show_priv')]."','".$row[csf('delete_priv')]."','".$row[csf('save_priv')]."','".$row[csf('edit_priv')]."','".$row[csf('approve_priv')]."','".$row[csf('entry_date')]."','999','".$row[csf('last_updated_by')]."','".$row[csf('inserted_by')]."','".$row[csf('last_update_date')]."','".$row[csf('valid')]."')";
	 $rID1=sql_insert( "user_priv_mst", $table_field_menu, $data_fld_menu,1);
	 //oci_commit();
	 $data_id++;
}
 
$data_id = return_next_id("id","user_priv_mst",1);
 
 
if($rID && $rID1)
{
	oci_commit();
	echo "Copied Successfully";
	
}
else
{
	oci_rollback();
	echo "Copy Failed";
}

 

die;
?>
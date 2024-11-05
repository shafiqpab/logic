<?
include('../includes/common.php');
$con = connect();
$company_array=return_library_array("select id, company_name from lib_company where status_active=1 order by id","id","company_name");
$sql_cat="select id, category_id, actual_category_name, short_name, category_type, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_inventory, ac_period_dtls_id, period_ending_date from lib_item_category_list";
$sql_cat_result=sql_select($sql_cat);
$insertCatID=true;
$id=return_next_id("id","lib_item_category_comp_wise",1);
$i=1;
foreach($company_array as $com_id=>$com_name)
{
	foreach($sql_cat_result as $row)
	{
		//short_name, category_type, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_inventory, ac_period_dtls_id, period_ending_date
		$insertCatID=execute_query("insert into lib_item_category_comp_wise (id, company_id, category_id, actual_category_name, short_name, category_type, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_inventory, ac_period_dtls_id, period_ending_date) values (".$id.",".$com_id.",'".$row[csf("category_id")]."','".$row[csf("actual_category_name")]."','".$row[csf("short_name")]."','".$row[csf("category_type")]."','1','".$pc_date_time."','','',1,0,'".$row[csf("is_inventory")]."','".$row[csf("ac_period_dtls_id")]."','".$row[csf("period_ending_date")]."')");
	
		if($insertCatID){ $insertCatID=1; } else {echo "insert into lib_item_category_comp_wise (id, company_id, category_id, actual_category_name, short_name, category_type, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted, is_inventory, ac_period_dtls_id, period_ending_date) values (".$id.",".$com_id.",'".$row[csf("category_id")]."','".$row[csf("actual_category_name")]."','".$row[csf("short_name")]."','".$row[csf("category_type")]."','1','".$pc_date_time."','','',1,0,'".$row[csf("is_inventory")]."','".$row[csf("ac_period_dtls_id")]."','".$row[csf("period_ending_date")]."')";oci_rollback($con);die;}
		$id++;$i++;
	}
}

//echo count($tst_data)."<pre>";print_r($tst_data);die;
if($db_type==2)
{
	if($insertCatID)
	{
		oci_commit($con); 
		echo "Insert Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Insert Failed";
		die;
	}
}
?>
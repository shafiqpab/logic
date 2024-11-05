<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$id_dtls_child = return_next_id("id", "dyes_chem_issue_requ_dtls_child", 1);
$sql= "select ID, MST_ID, BATCH_ID, RECIPE_ID from DYES_CHEM_ISSUE_REQU_DTLS order by ID";
$sql_result = sql_select($sql);
$data_array_dtls_child="";
foreach ($sql_result as $val) 
{
	$txt_recipe_id_arr = explode(",", $val["RECIPE_ID"]);
	$txt_batch_id_arr = explode(",", $val["BATCH_ID"]);
	$p=0;
	foreach($txt_recipe_id_arr as $recp_id)
	{
		echo "insert into dyes_chem_issue_requ_dtls_child (id, mst_id, dtls_id, batch_id, recipe_id, inserted_by, insert_date) values (" . $id_dtls_child . "," . $val["MST_ID"] . "," . $val["ID"] . ",'" . $txt_batch_id_arr[$p] . "'," . $recp_id . ",1,'" . $pc_date_time."')";oci_rollback($con);disconnect($con);die;
		$rID=execute_query("insert into dyes_chem_issue_requ_dtls_child (id, mst_id, dtls_id, batch_id, recipe_id, inserted_by, insert_date) values (" . $id_dtls_child . "," . $val["MST_ID"] . "," . $val["ID"] . ",'" . $txt_batch_id_arr[$p] . "'," . $recp_id . ",1,'" . $pc_date_time."')");
		if($rID==false)
		{
			echo "insert into dyes_chem_issue_requ_dtls_child (id, mst_id, dtls_id, batch_id, recipe_id, inserted_by, insert_date) values (" . $id_dtls_child . "," . $val["MST_ID"] . "," . $val["ID"] . ",'" . $txt_batch_id_arr[$p] . "'," . $recp_id . ",1,'" . $pc_date_time."')";oci_rollback($con);disconnect($con);die;
		}
		$id_dtls_child++;$p++;
	}
}

if ($rID) 
{
	oci_commit($con);
	echo "0**" . $rID;
} else {
	oci_rollback($con);
	echo "10**" . $rID;
}
disconnect($con);
die;


?>
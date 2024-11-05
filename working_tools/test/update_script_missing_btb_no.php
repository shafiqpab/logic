<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
mysql_query("BEGIN");

$sql="select LC_NUMBER, BANK_CODE, LC_YEAR, LC_CATEGORY, LC_SERIAL from COM_BTB_LC_MASTER_DETAILS where status_active=1";

$row_data=sql_select($sql);
$kk=1;
$update_issue_prefix=true;
foreach($row_data as $val)
{
	$inc=0;
	$ids_ref=explode(",",$val[csf("ids")]);
	sort($ids_ref);
	//echo "<pre>";print_r($ids_ref);die;
	for($i=1; $i<=$val[csf("tot_row")]; $i++)
	{
		$mst_id=$ids_ref[$inc];
		$sys_number=$val[csf("prefix")].str_pad($i,5,"0",STR_PAD_LEFT);
		$update_issue_prefix=execute_query("UPDATE inv_receive_master set recv_number_prefix='".$val[csf("prefix")]."',  recv_number_prefix_num=".$i.", recv_number='".$sys_number."'  where id=$mst_id");
		if(!$update_issue_prefix)
		{
			echo "UPDATE inv_receive_master set recv_number_prefix='".$val[csf("prefix")]."',  recv_number_prefix_num=".$i.", recv_number='".$sys_number."'  where id=$mst_id";
			if($db_type==0)
			{
				mysql_query("ROLLBACK");die;
			}
			else
			{
				oci_rollback($con);die;
			}
			
		}
		$inc++;
	}
	
}
if($db_type==0)
{
	if($update_issue_prefix)
	{
		mysql_query("COMMIT");
		echo "Success";
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
}
else
{
	if($update_issue_prefix)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con);
		echo "failed";
	}
}

?>
<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$permitted_user_id_arr = array(1,6);

if(!in_array($user_id, $permitted_user_id_arr))
{
	die('You are not authenticated');
	disconnect($con);
}

$sql_main ="SELECT a.JOB_NO, a.ITEM_ID, a.qnty AS allocation_qnty, SUM (b.qnty) AS history_qnty, LISTAGG (b.id || '_' || b.qnty, ',') WITHIN GROUP (ORDER BY b.id DESC) AS LOG_STRING FROM INV_MATERIAL_ALLOCATION_MST a, INV_MAT_ALLOCATION_MST_LOG b WHERE     a.item_id = b.item_id AND a.job_no = b.job_no AND a.is_sales = 1 and a.status_active=1 and b.status_active=1 and a.job_no='NAZBL-FSOE-23-02389' GROUP BY a.JOB_NO, a.ITEM_ID, a.qnty HAVING round(a.qnty,2) != round(SUM (b.qnty),2)";
//echo $sql_main; die;	
$main_sql_result = sql_select($sql_main);

//echo "<pre>";
//print_r($main_sql_result);

$update_histoty_data = array();
if(!empty($main_sql_result))
{
	$check_duplicate = array();
	foreach($main_sql_result as $row)
	{
		$log_string_arr = explode(",",$row['LOG_STRING']); //32029_-635,29171_1325,29170_1870

		foreach($log_string_arr as $value)
		{
			$history_qty_arr = explode("_",$value);

			if( $row['ALLOCATION_QNTY']>$row['HISTORY_QNTY'] )
			{
				if($check_duplicate[$row['JOB_NO']][$row['ITEM_ID']]=="")
				{
					if($history_qty_arr[1]>0)
					{
						$update_id_array[] = $history_qty_arr[0];
						$update_qnty = ($history_qty_arr[1]+($row['ALLOCATION_QNTY']-$row['HISTORY_QNTY']));
						$update_histoty_data[$history_qty_arr[0]] = explode("*", $update_qnty );
						$check_duplicate[$row['JOB_NO']][$row['ITEM_ID']] = $row['ITEM_ID']; 	
						
					}		
				}
			}
			else
			{
				if($check_duplicate[$row['JOB_NO']][$row['ITEM_ID']]=="")
				{
					if($history_qty_arr[1]>0)
					{
						$update_id_array[] = $history_qty_arr[0];
						$update_qnty =  ($history_qty_arr[1]-($row['HISTORY_QNTY']-$row['ALLOCATION_QNTY']));
						$update_histoty_data[$history_qty_arr[0]] = explode("*", $update_qnty );
						$check_duplicate[$row['JOB_NO']][$row['ITEM_ID']] = $row['ITEM_ID']; 		
					}		
				}
			}
		}			
	}
}

/* echo "<pre>";
print_r($update_histoty_data); die; */

if(!empty($update_id_array))
{
	$con = connect();

	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	$update_log_field = "qnty"; 
	$rID = execute_query(bulk_update_sql_statement("inv_mat_allocation_mst_log", "id", $update_log_field, $update_histoty_data, $update_id_array),0);
	$rID2 = execute_query(bulk_update_sql_statement("inv_mat_allocation_dtls_log", "mst_id", $update_log_field, $update_histoty_data, $update_id_array),0);

}

//echo $sql_main; die;
echo "10**".$rID ."&&". $rID2 ; die;

if($db_type==0)
{
	if($rID && $rID2)
	{
		mysql_query("COMMIT");
		echo "0**Data Synchronize is completed successfully";
	}
	else
	{
		mysql_query("ROLLBACK");
		echo "10**Data Synchronize is not completed successfully";
	}

	disconnect($con);
	die;
}
else if($db_type==2 || $db_type==1 )
{
	if($rID && $rID2 )
	{
		oci_commit($con);
		echo "0**Data Synchronize is completed successfully";
	}
	else
	{
		oci_rollback($con);
		echo "10**Data Synchronize is not completed successfully**$data_array_prod_update";
	}

	disconnect($con);
	die;
}

	

?>

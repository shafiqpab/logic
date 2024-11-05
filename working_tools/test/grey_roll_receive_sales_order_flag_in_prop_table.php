<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$trans_id_sql="SELECT a.id as trans_id
from inv_transaction a, inv_receive_master b,  pro_grey_prod_entry_dtls c,  pro_roll_details d, order_wise_pro_details e
where a.mst_id = b.id and b.id = c.mst_id and a.id = c.trans_id and d.mst_id = b.id and c.id = d.dtls_id and e.trans_id = a.id and e.dtls_id = c.id
and b.entry_form = 58 and d.entry_form = 58 and a.transaction_type = 1 and a.item_category = 13 
and d.is_sales = 1 
and a.status_active = 1 and d.status_active = 1
group by a.id";

$trans_id_res=sql_select($trans_id_sql);

foreach($trans_id_res as $val)
{
	$trans_id_arr[$val[csf("trans_id")]] = $val[csf("trans_id")];
}

if($db_type !=0)
{
	if(count($trans_id_arr)>999)
	{
		$arr_trans_id=array_chunk($trans_id_arr, 999);
		$arr_trans_id_cond=" and (";
		foreach ($arr_trans_id as $value) 
		{
			$arr_trans_id_cond .="trans_id in (".implode(",", $value).") or ";
		}
		$arr_trans_id_cond=chop($arr_trans_id_cond,"or ");
		$arr_trans_id_cond.=")";
	}
	else
	{
		$arr_trans_id_cond=" and trans_id in (".implode(",", $trans_id_arr).")";
	}
}
else
{
	$arr_trans_id_cond=" and trans_id in (".implode(",", $trans_id_arr).")";
}
//echo $arr_trans_id_cond;

$successRet = execute_query("update order_wise_pro_details 
set is_sales= 1
where $arr_trans_id_cond");

if($db_type==0)
{
	if($successRet)
	{
		mysql_query("COMMIT");  
		echo "Success";
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "Failed";
	}
}
else if($db_type==2 || $db_type==1 )
{
	if($successRet)
	{
		oci_commit($con);  
		echo "Success";  
	}
	else
	{
		oci_rollback($con); 
		echo "Failed";
	}
}

disconnect($con);
die;
 
?>
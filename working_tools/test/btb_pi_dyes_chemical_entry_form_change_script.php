<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
/*
	First Execute below script
	
	//update com_pi_master_details set entry_form=227 where item_category_id  in (5,6,7,23) and entry_form = 172 and version in (0,1);
*/

$sqls= "select  a.id, b.pi_id,c.entry_form
from com_btb_lc_master_details a, com_btb_lc_pi  b, com_pi_master_details c
where a.id = b.com_btb_lc_master_details_id and b.pi_id = c.id and c.entry_form = 227 
and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active= 1 and c.is_deleted=0 ";
$row_data=sql_select($sqls);

foreach ($row_data as $val) 
{
	$all_btb_arr[$val[csf("id")]] = $val[csf("id")];
}


	$all_btb_arr = array_filter($all_btb_arr);

	if(count($all_btb_arr)>0)
	{
		$all_btb_id_cond=""; $bokIds_cond=""; 
		$all_btb_ids = implode(",", $all_btb_arr);

		if($db_type==2 && count($all_btb_arr)>999)
		{
			$all_btb_chunk_arr=array_chunk($all_btb_arr,999) ;
			foreach($all_btb_chunk_arr as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$bokIds_cond.=" id in($chunk_arr_value) or ";	
			}
			
			$all_btb_id_cond.=" and (".chop($bokIds_cond,'or ').")";			
		}
		else
		{
			$all_btb_id_cond=" and id in($all_btb_ids)";	 
		}
	}


	$update_delivery_mst=execute_query("UPDATE com_btb_lc_master_details set pi_entry_form=227,updated_by=999 where  status_active=1 $all_btb_id_cond ");

	if($update_delivery_mst==1)
	{
		oci_commit($con); 
		echo "Success";

	}
	else
	{
		oci_rollback($con);
		echo "failed";
	}
 
?>
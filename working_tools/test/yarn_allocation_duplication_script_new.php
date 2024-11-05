<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
if ($db_type == 0) {
	mysql_query("BEGIN");
}

$user_id=$_SESSION['logic_erp']['user_id'];

$duplicate_in_master_sql="SELECT  listagg(b.id, ',') within group (order by b.id asc) as mst_id, b.item_id, b.job_no, b.po_break_down_id, b.booking_no, (sum(b.qnty) || '_' || b.po_break_down_id ||'_' || b.job_no) as qntity_brek_down, sum(b.qnty) as total_qty FROM product_details_master a, inv_material_allocation_mst b WHERE a.id = b.item_id AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  AND a.item_category_id = 1 AND b.booking_no is not null  and b.item_id in(652800) GROUP BY b.item_id, b.job_no, b.po_break_down_id, b.booking_no HAVING COUNT (b.id) > 1";// -- and 

//echo $duplicate_in_master_sql;die();
//AND a.current_stock > 0  


$duplicate_in_master_data = sql_select($duplicate_in_master_sql);
if(!empty($duplicate_in_master_data))
{
	$delete_id_arr = array(); 
	foreach ($duplicate_in_master_data as $row) 
	{
		$job_no					= $row[csf("job_no")];
		$po_break_down_id 		= $row[csf("po_break_down_id")];
		$booking_no 			= $row[csf("booking_no")];
		$item_id 				= $row[csf("item_id")];
		$qntity_brek_down_str 	= $row[csf("qntity_brek_down")];
		$total_qty 			    = $row[csf("total_qty")];
		
		$all_mst_id_arr     = explode(",", $row[csf("mst_id")]);
		$min_mst_id      = $all_mst_id_arr[0];

		$update_mst_table_sql =execute_query("update inv_material_allocation_mst set qnty=$total_qty,qnty_break_down='$qntity_brek_down_str' WHERE id = $min_mst_id");

		if($update_mst_table_sql) $update_mst_table_sql=1; else {"update inv_material_allocation_mst set qnty=$total_qty,qnty_break_down='$qntity_brek_down_str' WHERE id = $min_mst_id";oci_rollback($con);die;}
	    
	    foreach($all_mst_id_arr as $mst_id)
	    {
	    	if($min_mst_id != $mst_id)
		    {
		    	$delete_mst_table_sql=execute_query("update inv_material_allocation_mst set status_active=0,is_deleted=1 where id = $mst_id");
		    	if($delete_mst_table_sql) $delete_mst_table_sql=1; else {"update inv_material_allocation_mst set status_active=0,is_deleted=1 where id = $mst_id";oci_rollback($con);die;}
		    }
            
            $update_mst_table_sql =execute_query("update inv_material_allocation_dtls set mst_id=$min_mst_id,qnty=$total_qty where item_id=$item_id and job_no='$job_no' and booking_no='$booking_no' and po_break_down_id = $po_break_down_id");

            if($update_mst_table_sql) $update_mst_table_sql=1; else {"update inv_material_allocation_dtls set mst_id=$min_mst_id,qnty=$total_qty where item_id=$item_id and job_no='$job_no' and booking_no='$booking_no' and po_break_down_id = $po_break_down_id";oci_rollback($con);die;}

            $delete_dtls_table_sql =execute_query("update inv_material_allocation_dtls set status_active=0,is_deleted=1 where id = (select max(id) as id from inv_material_allocation_dtls where item_id=$item_id and job_no='$job_no' and booking_no='$booking_no' and po_break_down_id = $po_break_down_id) ");

            if($delete_dtls_table_sql) $delete_dtls_table_sql=1; else {"update inv_material_allocation_dtls set status_active=0,is_deleted=1 where id = (select max(id) as id from inv_material_allocation_dtls where item_id=$item_id and job_no='$job_no' and booking_no='$booking_no' and po_break_down_id = $po_break_down_id) ";oci_rollback($con);die;}
	    }
	}
}

if ($update_mst_table_sql && $delete_mst_table_sql && $update_mst_table_sql && $delete_dtls_table_sql) 
{
	oci_commit($con); 
    echo "Success";
    die; 
}
?>
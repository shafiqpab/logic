<?php
session_start();
include('includes/common.php');
$db_type=0;
$con = connect();
mysql_query("BEGIN");


$allocation_data = sql_select("select a.job_no, sum(a.cons_quantity) as allo_qty, b.booking_id, c.id prod_id, c.supplier_id, c.lot, c.current_stock, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_count_id, c.yarn_type, c.color,b.receive_basis,b.receive_purpose,b.receive_date from inv_transaction a, inv_receive_master b, product_details_master c where a.mst_id=b.id and a.prod_id=c.id and a.company_id=1 and c.current_stock>0 and b.entry_form=1 and b.receive_basis=2 and b.receive_purpose=2 and a.transaction_type=1 and a.item_category=1 and b.status_active=1 and b.is_deleted=0 and a.job_no not in (select d.job_no from inv_material_allocation_mst d where c.id = d.item_id and a.job_no=d.job_no and d.is_dyied_yarn=1) group by a.job_no, b.booking_id, c.id, c.supplier_id, c.lot, c.current_stock, c.yarn_comp_type1st, c.yarn_comp_percent1st, c.yarn_comp_type2nd, c.yarn_comp_percent2nd, c.yarn_count_id, c.yarn_type, c.color,b.receive_basis,b.receive_purpose,b.receive_date");

// echo "<pre>";
// print_r($allocation_data); die;
$i=1;
$field_allocation 		= "id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
$field_allocation_dtls 	= "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
$allocation_id 			= return_next_id("id", "inv_material_allocation_mst", 1);
$allocation_dtls_id 	= return_next_id("id", "inv_material_allocation_dtls", 1);	
if(!empty($allocation_data)){
	foreach ($allocation_data as $row) {
		if($row[csf("job_no")] != ''){	

			$prod_id 			= $row[csf("prod_id")];
			$allo_qty 				= $row[csf("allo_qty")];
			$job_no 				= $row[csf("job_no")];
			$receive_date 			= change_date_format($row[csf("receive_date")],'yyyy-mm-dd');
			$insert_date_time 		= change_date_format($pc_date_time,'yyyy-mm-dd');	
			if($data_allocation!='') $data_allocation .=",";
			if($data_allocation_dtls!='') $data_allocation_dtls .=",";
			
			$data_allocation 		.= "(".$allocation_id.",'".$job_no."',1".",'".$receive_date."',".$prod_id.",".$allo_qty.",1,8888,'".$insert_date_time."')";				
			$data_allocation_dtls  	.= "(".$allocation_dtls_id.",".$allocation_id.",'".$job_no."',1,'".$receive_date."',".$prod_id.",".$allo_qty.",1,8888,'".$insert_date_time."')";			
			
			$i++;
			$allocation_id++;
			$allocation_dtls_id++;

		}
	}
}else{
	echo "Empty";
}
$allocation_mst_insert  = sql_insert("inv_material_allocation_mst",$field_allocation,$data_allocation,0);
if($allocation_mst_insert){
	$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls",$field_allocation_dtls,$data_allocation_dtls,0);
}

if( $allocation_mst_insert && $allocation_dtls_insert)
{
	mysql_query("COMMIT");  
	echo "0**".$i."_y";
}
else
{
	mysql_query("ROLLBACK"); 
	echo "10**".$i."_n";
}

?>
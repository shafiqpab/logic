<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 
 $flag=1;
$print_bill_sql="select id from subcon_inbound_bill_mst where entry_form=395";
$print_bill_sql_res=sql_select($print_bill_sql);
if(count($print_bill_sql_res)>0){
	foreach($print_bill_sql_res as $row)
	{
		$id="'".$row[csf("id")]."'";
		
		$bill_mst_update=execute_query("update subcon_inbound_bill_mst set status_active=0, is_deleted=1 where id=$id and entry_form=395");
		$bill_dtls_update=execute_query("update subcon_inbound_bill_dtls set status_active=0, is_deleted=1 where mst_id=$id");
		if($bill_mst_update==1 && $bill_dtls_update==1)
		{
			$flag=1; 
		} 
		else
		{
			$flag=0;
			oci_rollback($con);
			echo "failed";
		} 
	}
}

if($flag){
	$print_delv_sql="select id from subcon_delivery_mst where entry_form=254";
	$print_delv_sql_res=sql_select($print_delv_sql);
	if(count($print_delv_sql_res)>0){
		foreach($print_delv_sql_res as $row)
		{
			$id="'".$row[csf("id")]."'";
			
			$delv_mst_update=execute_query("update subcon_delivery_mst set status_active=0, is_deleted=1 where id=$id and entry_form=254");
			$delv_dtls_update=execute_query("update subcon_delivery_dtls set status_active=0, is_deleted=1 where mst_id=$id");
			if($delv_mst_update==1 && $delv_dtls_update==1 )
			{
				$flag=1; 
			} 
			else
			{
				$flag=0;
				oci_rollback($con);
				echo "failed";
			} 
		}
	}
}

if($flag){
	$print_recipe_sql="select id from pro_recipe_entry_mst where entry_form=220";
	$print_recipe_sql_res=sql_select($print_recipe_sql);
	if(count($print_recipe_sql_res)>0){
		foreach($print_recipe_sql_res as $row)
		{
			$id="'".$row[csf("id")]."'";
			
			$recipe_mst_update=execute_query("update pro_recipe_entry_mst set status_active=0, is_deleted=1 where id=$id and entry_form=220");
			$recipe_dtls_update=execute_query("update pro_recipe_entry_dtls set status_active=0, is_deleted=1 where mst_id=$id");
			if($recipe_mst_update==1 && $recipe_dtls_update==1)
			{
				$flag=1; 
			} 
			else
			{
				$flag=0;
				oci_rollback($con);
				echo "failed";
			} 
		}
	}
}

if($flag){
	$print_qc_sql="select id from subcon_embel_production_mst where entry_form=223";
	$print_qc_sql_res=sql_select($print_qc_sql);
	if(count($print_qc_sql_res)>0){
		foreach($print_qc_sql_res as $row)
		{
			$id="'".$row[csf("id")]."'";
			
			$qc_mst_update=execute_query("update subcon_embel_production_mst set status_active=0, is_deleted=1 where id=$id and entry_form=223");
			$qc_dtls_update=execute_query("update subcon_embel_production_dtls set status_active=0, is_deleted=1 where mst_id=$id");
			if($qc_mst_update==1 && $qc_dtls_update==1)
			{
				$flag=1; 
			} 
			else
			{
				$flag=0;
				oci_rollback($con);
				echo "failed";
			} 
		}
	}
}

if($flag){
	$print_production_sql="select id from subcon_embel_production_mst where entry_form=222";
	$print_production_sql_res=sql_select($print_production_sql);
	if(count($print_production_sql_res)>0){
		foreach($print_production_sql_res as $row)
		{
			$id="'".$row[csf("id")]."'";
			
			$production_mst_update=execute_query("update subcon_embel_production_mst set status_active=0, is_deleted=1 where id=$id and entry_form=222");
			$production_dtls_update=execute_query("update subcon_embel_production_dtls set status_active=0, is_deleted=1 where mst_id=$id");
			if($production_mst_update==1 && $production_dtls_update==1)
			{
				$flag=1; 
			} 
			else
			{
				$flag=0;
				oci_rollback($con);
				echo "failed";
			} 
		}
	}
}

if($flag){
	$print_production_sql="select id from subcon_embel_production_mst where entry_form=222";
	$print_production_sql_res=sql_select($print_production_sql);
	if(count($print_production_sql_res)>0){
		foreach($print_production_sql_res as $row)
		{
			$id="'".$row[csf("id")]."'";
			
			$production_mst_update=execute_query("update subcon_embel_production_mst set status_active=0, is_deleted=1 where id=$id and entry_form=222");
			$production_dtls_update=execute_query("update subcon_embel_production_dtls set status_active=0, is_deleted=1 where mst_id=$id");
			if($production_mst_update==1 && $production_dtls_update==1)
			{
				$flag=1; 
			} 
			else
			{
				$flag=0;
				oci_rollback($con);
				echo "failed";
			} 
		}
	}
}

if($flag){
	$print_req_sql="select id from dyes_chem_issue_requ_mst where entry_form=221";
	$print_req_sql_res=sql_select($print_req_sql);
	if(count($print_req_sql_res)>0){
		foreach($print_req_sql_res as $row)
		{
			$id="'".$row[csf("id")]."'";
			
			$req_mst_update=execute_query("update dyes_chem_issue_requ_mst set status_active=0, is_deleted=1 where id=$id and entry_form=221");
			$req_dtls_update=execute_query("update dyes_chem_requ_recipe_att set status_active=0, is_deleted=1 where mst_id=$id");
			if($req_mst_update==1 && $req_dtls_update==1)
			{
				$flag=1; 
			} 
			else
			{
				$flag=0;
				oci_rollback($con);
				echo "failed";
			} 
		}
	}
}


if($flag){
	$print_mat_recv_sql="select id from sub_material_mst where entry_form=205";
	$print_mat_recv_sql_res=sql_select($print_mat_recv_sql);
	if(count($print_mat_recv_sql_res)>0){
		foreach($print_mat_recv_sql_res as $row)
		{
			$id="'".$row[csf("id")]."'";
			
			$mat_recv_mst_update=execute_query("update sub_material_mst set status_active=0, is_deleted=1 where id=$id and entry_form=205");
			$mat_recv_dtls_update=execute_query("update sub_material_dtls set status_active=0, is_deleted=1 where mst_id=$id");
			if($mat_recv_mst_update==1 && $mat_recv_dtls_update==1)
			{
				$flag=1; 
			} 
			else
			{
				$flag=0;
				oci_rollback($con);
				echo "failed";
			} 
		}
	}
}

if($flag){
	$print_mat_issue_sql="select id from sub_material_mst where entry_form=207";
	$print_mat_issue_sql_res=sql_select($print_mat_issue_sql);
	if(count($print_mat_issue_sql_res)>0){
		foreach($print_mat_issue_sql_res as $row)
		{
			$id="'".$row[csf("id")]."'";
			
			$mat_issue_mst_update=execute_query("update sub_material_mst set status_active=0, is_deleted=1 where id=$id and entry_form=207");
			$mat_issue_dtls_update=execute_query("update sub_material_dtls set status_active=0, is_deleted=1 where mst_id=$id");
			if($mat_issue_mst_update==1 && $mat_issue_dtls_update==1)
			{
				$flag=1; 
			} 
			else
			{
				$flag=0;
				oci_rollback($con);
				echo "failed";
			} 
		}
	}
}

if($flag){
	$print_sql="select id,within_group,order_id,order_no,embellishment_job from subcon_ord_mst where entry_form=204";
	$print_sql_res=sql_select($print_sql);
	if(count($print_sql_res)>0){
		foreach($print_sql_res as $row)
		{
			$booking_no="'".$row[csf("order_no")]."'";
			$emb_job_no="'".$row[csf("embellishment_job")]."'";
			$id="'".$row[csf("id")]."'";
			$within_group=$row[csf("within_group")];
			if($within_group==1){
				//echo "update wo_booking_mst set lock_another_process=0 where booking_no=$booking_no and lock_another_process=1***";
				$booking_update=execute_query("update wo_booking_mst set lock_another_process=0 where booking_no=$booking_no and lock_another_process=1");
				if($booking_update==1)
				{
					$flag=1; 
				} 
				else
				{
					$flag=0;
					oci_rollback($con);
					echo "failed";
				} 
			}
			
			
			$breakdown_update=execute_query("update subcon_ord_breakdown set status_active=0, is_deleted=1 where job_no_mst=$emb_job_no");
			$dtls_update=execute_query("update subcon_ord_dtls set status_active=0, is_deleted=1 where mst_id=$id and job_no_mst=$emb_job_no");
			$mst_update=execute_query("update subcon_ord_mst set status_active=0, is_deleted=1 where id=$id and embellishment_job=$emb_job_no");
			//echo $mst_update.'**'.$dtls_update.'**'.$breakdown_update.'==';
			if($mst_update==1 && $dtls_update==1 && $breakdown_update==1)
			{
				$flag=1; 
			} 
			else
			{
				//echo "update subcon_ord_breakdown set status_active=0, is_deleted=1 where job_no_mst=$emb_job_no****";
				$flag=0;
				oci_rollback($con); 
				echo "failed";
			}
		}
	}
}
//die;
//oci_rollback($con); 
//echo "kkf"; die;

//echo $test_data;die;
if($flag)
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
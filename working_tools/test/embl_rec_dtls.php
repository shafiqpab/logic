<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

/*$issue_sql="select a.emb_name_id, sum(a.quantity) as quantity, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id, a.rec_challan, c.id, c.inserted_by, c.insert_date from sub_material_dtls a, sub_material_mst b, sub_material_mst c 
 where b.entry_form=207 and a.mst_id=b.id
and a.rec_challan=c.sys_no and c.entry_form=205 and c.subcon_date between '01-Feb-2019' and '28-Feb-2019'
group by  a.emb_name_id, a.uom, a.job_dtls_id, a.job_break_id, a.buyer_po_id, a.rec_challan, c.id, c.inserted_by, c.insert_date";

$apply_sql_res=sql_select($issue_sql); $i=0;
$field_array2="id, mst_id, emb_name_id, quantity, uom, job_dtls_id, job_break_id, buyer_po_id, inserted_by, insert_date, status_active, is_deleted";
$id1=return_next_id("id","sub_material_dtls",1) ;
foreach($apply_sql_res as $row)
{
	$i++;
	//$up=execute_query("update wo_booking_mst set is_apply_last_update=0 where booking_no=$booking_no and is_apply_last_update=1");
	
	$data_array2.="(".$id1.",'".$row[csf('id')]."','".$row[csf('emb_name_id')]."','".$row[csf('quantity')]."','".$row[csf('job_dtls_id')]."','".$row[csf('job_break_id')]."','".$row[csf('buyer_po_id')]."','".$row[csf('inserted_by')]."','".$row[csf('insert_date')]."',1,0)";
	
	$id1++;
	//echo "update wo_booking_mst set is_apply_last_update=0 where booking_no=$booking_no and is_apply_last_update=1";
}*/
//print_r($data_array2);

$sql_ppl="select id, fabric_desc from ppl_planning_info_entry_mst";
$sql_ppl_res=sql_select($sql_ppl);
foreach($sql_ppl_res as $row)
{
	$d=explode(",",$row[csf('fabric_desc')]);
	
	
}

//oci_commit($con); 
	echo "Success".$i;
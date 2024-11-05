<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
if ($db_type == 0) {
	mysql_query("BEGIN");
}
//$duplicate_in_master_sql="select job_no,po_break_down_id,booking_no,item_id,is_dyied_yarn,rn from ( select t.job_no,t.po_break_down_id,t.booking_no,t.item_id,t.is_dyied_yarn, row_number() over (partition by job_no, po_break_down_id,booking_no,item_id order by job_no) as rn from inv_material_allocation_mst t where t.status_active=1 and t.job_no='MFG-FSOE-18-00020') where rn > 1 order by job_no asc"; // and t.job_no='MFG-FSOE-18-00019'

$duplicate_in_master_sql="select t.job_no,t.po_break_down_id,t.booking_no,t.item_id,t.is_dyied_yarn,count(*)
from inv_material_allocation_mst t
where t.status_active=1
group by t.job_no,t.po_break_down_id,t.booking_no,t.item_id,t.is_dyied_yarn";// and t.job_no='AOPL-17-00301'
$duplicate_in_master_data = sql_select($duplicate_in_master_sql);

foreach ($duplicate_in_master_data as $row) {
	$job_no				= $row[csf("job_no")];
	$po_break_down_id 	= $row[csf("po_break_down_id")];
	$booking_no 		= $row[csf("booking_no")];
	$item_id 			= $row[csf("item_id")];
	$is_dyied_yarn 		= $row[csf("is_dyied_yarn")];

	$po_break_down_cond = ($po_break_down_id !="")?" and po_break_down_id='$po_break_down_id'":" and po_break_down_id is null";
	$booking_cond = ($booking_no !="")?" and booking_no='$booking_no'":" and booking_no is null";
	if ($db_type == 0) {
		$master_sql = "select id,sum(qnty) mst_qnty,group_concat(allocation_date) as allocation_date,group_concat(inserted_by) as inserted_by, group_concat(insert_date) as insert_date from inv_material_allocation_mst where status_active=1 and job_no='$job_no' $po_break_down_cond $booking_cond and item_id=$item_id group by id order by id asc";
	}else{
		$master_sql = "select id,sum(qnty) mst_qnty,listagg(allocation_date, ',') within group (order by id) as allocation_date,listagg(inserted_by, ',') within group (order by id) as inserted_by, listagg(insert_date, ',') within group (order by id) as insert_date from inv_material_allocation_mst where status_active=1 and job_no='$job_no' $po_break_down_cond $booking_cond and item_id=$item_id group by id order by id asc";
	}
	

	$master_data = sql_select($master_sql);
	$all_mst_ids = $master_qnty_arr = array();
	$i = 0;
	$master_qnty = 0;
	foreach ($master_data as $mst_row) {
		if($i == 0){
			$mst_id = $mst_row[csf("id")];
			$allocation_date	= explode(",",$mst_row[csf("allocation_date")]);
			$inserted_by 		= explode(",",$mst_row[csf("inserted_by")]);
			$insert_date 		= explode(",",$mst_row[csf("insert_date")]);
		}
		$master_qnty += $mst_row[csf("mst_qnty")];
		$all_mst_ids[] = $mst_row[csf("id")];
		$i++;
	}
	
	$dtls_sql="select id,mst_id,job_no,po_break_down_id,item_id,qnty dtls_qnty from inv_material_allocation_dtls where mst_id in (".implode(",",$all_mst_ids).") order by mst_id,id asc ";

	$dtls_data=sql_select($dtls_sql);
	$all_dtsl=$dtls_id_arr=array();
	$j = 0;
	if(!empty($dtls_data)){
		foreach ($dtls_data as $dtls_row) {
			if($mst_id==$dtls_row[csf("mst_id")]) {
				$dtls_id_arr[$mst_id][$dtls_row[csf("po_break_down_id")]]['id']= $dtls_row[csf("id")];
			}
			$dtls_id_arr[$mst_id][$dtls_row[csf("po_break_down_id")]]['qnty'] += $dtls_row[csf("dtls_qnty")];
		}
		
		foreach($dtls_id_arr as $mst=>$po)
		{
			foreach($po as $po_id=>$po_dt)
			{
				$dtls_id=$dtls_id_arr[$mst][$po_id]['id'];
				$dtls_qnty = $dtls_id_arr[$mst][$po_id]['qnty'];
				
				$insert_dtls=execute_query("insert into inv_material_allocation_dtls_b(id,mst_id,job_no,po_break_down_id,item_category,allocation_date,booking_no,item_id,qnty,is_dyied_yarn,status_active,is_deleted,inserted_by,insert_date) select id,mst_id,job_no,po_break_down_id,item_category,allocation_date ,booking_no,item_id,$dtls_qnty,is_dyied_yarn,status_active,is_deleted,inserted_by,insert_date from inv_material_allocation_dtls where id=$dtls_id");
			}
		}

		$insert_mst=execute_query("insert into INV_MATERIAL_ALLOCATION_MST_b(id,job_no,po_break_down_id,item_category,entry_form,allocation_date ,booking_no,item_id,qnty,qnty_break_down,is_dyied_yarn,status_active,is_deleted,inserted_by,insert_date) 
		select id,job_no,po_break_down_id,item_category,9999,allocation_date ,booking_no,item_id,$master_qnty,qnty_break_down,is_dyied_yarn,status_active,is_deleted,inserted_by,insert_date from 
			inv_material_allocation_mst where id=$mst_id");
	}
}
oci_commit($con); 
echo "Success";
die;
?>
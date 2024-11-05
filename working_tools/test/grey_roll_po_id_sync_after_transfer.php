<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_roll_sql="select x.id,x.barcode_no,x.po_breakdown_id,x.transfer_po,x.entry_form,x.trans_roll from (
select a.id,a.po_breakdown_id,b.po_breakdown_id transfer_po,a.entry_form,a.barcode_no,b.ID trans_roll
from pro_roll_details a
left join pro_roll_details b on b.barcode_no=a.barcode_no and b.entry_form=133 and b.re_transfer=0 and b.barcode_no>0 and b.status_active=1
where a.entry_form > 58 and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0 and a.barcode_no>0
and b.ID is not null
) x where x.po_breakdown_id!= x.transfer_po and x.barcode_no=18020351677
order by x.barcode_no,x.id asc";
$mismatch_roll_data = sql_select($mismatch_roll_sql);
$transfered_barcode_po=array();
foreach ($mismatch_roll_data as $row) {
	$roll_id = $row[csf("id")];
	$transfer_po_arr[$row[csf("barcode_no")]] +=1;
	if($row[csf("entry_form")]==133){
		$transfer_po_arr[$row[csf("barcode_no")]] = $row[csf("transfer_po")];
		break;
	}else{
		$transfer_po_arr[$row[csf("barcode_no")]] = $row[csf("transfer_po")];
	}
	//echo "UPDATE pro_roll_details set po_breakdown_id_bk=$transfer_po where id=$roll_id <br />";
	//$update_allocation_dtls=execute_query("UPDATE pro_roll_details set po_breakdown_id=$transfer_po,updated_by=999 where id=$roll_id");
}

foreach ($mismatch_roll_data as $row) {
	$roll_id = $row[csf("id")];
	$transfer_po =$transfer_po_arr[$row[csf("barcode_no")]];
	if($row[csf("entry_form")]!=133){
		echo "UPDATE pro_roll_details set po_breakdown_id_bk=$transfer_po where id=$roll_id <br />";
	}
	//$update_allocation_dtls=execute_query("UPDATE pro_roll_details set po_breakdown_id=$transfer_po,updated_by=999 where id=$roll_id");
}

/*$mismatch_batch_roll_sql="select a.batch_against, a.batch_for, a.booking_no, a.re_dyeing_from, a.color_id, a.booking_without_order,a.is_sales, b.id, b.program_no,
b.po_id,c.po_breakdown_id, b.prod_id, b.item_description, b.body_part_id, b.width_dia_type, b.roll_no, b.roll_id, b.barcode_no, b.batch_qnty, b.po_batch_no
from pro_batch_create_mst a,pro_batch_create_dtls b,pro_roll_details c
where a.id=b.mst_id and b.barcode_no=c.barcode_no
and c.entry_form =64
and b.status_active=1 and b.is_deleted=0 and c.status_active=1
and b.po_id!=c.po_breakdown_id";
$mismatch_batch_roll_data = sql_select($mismatch_batch_roll_sql);
foreach ($mismatch_batch_roll_data as $row) {
	$dtls_id = $row[csf("id")];
	$po_breakdown_id = $row[csf("po_breakdown_id")];
	//echo "UPDATE pro_roll_details set po_breakdown_id_bk=$transfer_po where id=$roll_id <br />";
	//$update_allocation_dtls=execute_query("UPDATE pro_batch_create_dtls set po_id=$po_breakdown_id where id=$dtls_id");
}*/
//echo "<pre>";
//print_r(max($transfered_barcode_po));
//oci_commit($con);
//update pro_roll_details set po_breakdown_id_bk=po_breakdown_id
echo "Success";
die;
?>
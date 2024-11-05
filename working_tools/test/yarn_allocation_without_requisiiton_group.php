<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mismatch_roll_sql="select x.mst_id,z.company_name,x.job_no,y.buyer_name, x.booking_no,
listagg(cast(x.po_number as varchar2(4000)), ',') within group (order by x.po_number) as po_number,
x.shipment_date ship_date,x.item_id product_id,x.lot,
x.product_name_details,x.brand_name,x.qnty
from(select a.mst_id,a.item_id,c.company_name,a.job_no,c.buyer_name,d.shipment_date, a.booking_no,a.po_break_down_id,d.po_number,e.lot,sum(a.qnty) qnty,
e.product_name_details,f.brand_name
from inv_material_allocation_dtls a,wo_po_details_master c,wo_po_break_down d,product_details_master e,lib_brand f
where a.job_no = c.job_no and a.po_break_down_id=d.id and a.item_id=e.id and e.brand=f.id
and a.item_id not in(select b.prod_id from ppl_yarn_requisition_entry b where a.item_id=b.prod_id and b.status_active=1)
and a.status_active=1 and c.status_active=1 and d.status_active=1 and a.qnty > 0
group by a.mst_id,a.item_id,c.company_name,a.job_no,c.buyer_name,d.shipment_date, a.booking_no,a.po_break_down_id,d.po_number,e.lot,e.product_name_details,f.brand_name
)x ,lib_buyer y,lib_company z where x.buyer_name=y.id and x.company_name=z.id
group by x.mst_id,x.item_id,z.company_name,x.job_no,y.buyer_name,x.booking_no,x.shipment_date,x.lot,x.qnty,x.product_name_details,x.brand_name";
$mismatch_roll_data = sql_select($mismatch_roll_sql);

foreach ($mismatch_roll_data as $row) {
	$product_id = $row[csf("product_id")];
	$mst_id = $row[csf("mst_id")];
	//echo "update product_details_master set allocated_qnty=0,available_qnty=current_stock where id=$product_id; <br />";
	//echo "update inv_material_allocation_mst set status_active=0,is_deleted=1 where id=$mst_id; <br />";
	//echo "update inv_material_allocation_dtls set status_active=0,is_deleted=1 where mst_id=$mst_id; <br />";

	//execute_query("update product_details_master set allocated_qnty=0,available_qnty=current_stock where id=$product_id");
	//execute_query("update inv_material_allocation_mst set status_active=0,is_deleted=1 where id=$mst_id");
	//execute_query("update inv_material_allocation_dtls set status_active=0,is_deleted=1 where mst_id=$mst_id");
}

//echo "<pre>";
//print_r(max($transfered_barcode_po));
//oci_commit($con); 
//update pro_roll_details set po_breakdown_id_bk=po_breakdown_id
echo "Success";
die;
?>
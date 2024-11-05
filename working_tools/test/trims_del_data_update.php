<?

header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 

/*$sql = "SELECT
         b.id AS break_id,
         a.buyer_po_id
    FROM subcon_ord_dtls a, subcon_ord_breakdown b, trims_delivery_dtls c, subcon_ord_mst d
   WHERE     a.id = b.mst_id
   			and d.id = a.mst_id
         AND b.id = c.break_down_details_id
         and d.within_group=1
         and d.entry_form=255
        --AND a.buyer_po_id IS NOT NULL
        -- AND a.buyer_po_id != 0
         AND a.order_quantity <> 0
         AND a.booked_qty <> 0
         AND a.status_active = 1
         AND a.is_deleted = 0
         AND C.BUYER_PO_ID = '0'
ORDER BY a.id ASC";

$order_result=sql_select($sql); 
foreach ($order_result as $rows)
{
	$order_dtls_arr[$rows[csf("break_id")]]			=$rows[csf("buyer_po_id")];
}

//$delsql = "select a.break_down_details_id from trims_delivery_dtls a,trims_delivery_mst b,subcon_ord_breakdown c where b.id=a.mst_id and b.entry_form=208 and b.within_group=1 and c.id=a.break_down_details_id and a.buyer_po_id ='0' and a.status_active=1 and a.is_deleted=0  order by a.id ASC";

$delsql = "SELECT
         c.break_down_details_id
    FROM subcon_ord_dtls a, subcon_ord_breakdown b, trims_delivery_dtls c, subcon_ord_mst d
   WHERE     a.id = b.mst_id
   			and d.id = a.mst_id
         AND b.id = c.break_down_details_id
         and d.within_group=1
         and d.entry_form=255
        AND a.buyer_po_id IS NOT NULL
         AND a.buyer_po_id != 0
         AND a.order_quantity <> 0
         AND a.booked_qty <> 0
         AND a.status_active = 1
         AND a.is_deleted = 0
         AND C.BUYER_PO_ID = '0'
ORDER BY a.id ASC";

$field_array="buyer_po_id";
$del_result=sql_select($delsql); 
foreach ($del_result as $rows)
{
	$del_del_id_arr[]			=$rows[csf("break_down_details_id")];
	$data_array[$rows[csf("break_down_details_id")]]=explode("*",("'".$order_dtls_arr[$rows[csf("break_down_details_id")]]."'"));
}
//echo "10**".bulk_update_sql_statement( "trims_delivery_dtls", "break_down_details_id",$field_array,$data_array,$del_del_id_arr); die;
$rID=execute_query(bulk_update_sql_statement( "trims_delivery_dtls", "break_down_details_id",$field_array,$data_array,$del_del_id_arr),1);*/

//--AND a.buyer_po_id IS NOT NULL --AND a.buyer_po_id != 0 and a.order_quantity <> 0 and a.booked_qty <> 0 and and c.buyer_po_id = '0' 

$ord_sql ="select b.id as break_id,b.description,b.color_id,b.size_id,a.item_group,a.order_uom, a.buyer_po_id, a.order_id,a.order_no
    from subcon_ord_dtls a, subcon_ord_breakdown b, trims_delivery_dtls c, subcon_ord_mst d
    where a.id = b.mst_id and d.id = a.mst_id and b.id = c.break_down_details_id and d.within_group=1 and d.entry_form=255 and a.status_active = 1 and a.is_deleted = 0
    order by a.id asc";
$order_result=sql_select($ord_sql); $order_dtls_arr=array();
foreach ($order_result as $rows)
{
    // echo "nazim </br>";
    $order_key=$rows[csf('order_no')].'_'.$rows[csf('item_group')].'_'.$rows[csf('description')].'_'.$rows[csf('order_uom')].'_'.$rows[csf('color_id')].'_'.$rows[csf('size_id')];
    if($rows[csf("buyer_po_id")]!=0)  $rec_order_id=$rows[csf("buyer_po_id")]; else $rec_order_id=$rows[csf("order_id")];
    $order_dtls_arr[$order_key]         = $rec_order_id;

}
//print_r($order_dtls_arr);
$rcv_sql ="select  a.receive_date,b.id,b.booking_no, b.item_group_id, b.item_description, b.brand_supplier, b.order_uom,b.item_color,b.gmts_size_id from inv_receive_master a,  inv_trims_entry_dtls b where a.id =b.mst_id and a.entry_form=24 and a.receive_basis=12 and b.status_active=1 and b.is_deleted=0 and b.order_id ='0' and a.receive_date between '29-FEB-2020' and  '05-MAR-2020' and b.id < 547466 ";
$rcv_result=sql_select($rcv_sql);
foreach ($rcv_result as $rows)
{
    //$rec_dtls_arr[$rows[csf("id")]]         =$order_dtls_arr[$order_key];

    $key=$rows[csf('booking_no')].'_'.$rows[csf('item_group_id')].'_'.$rows[csf('item_description')].'_'.$rows[csf('order_uom')].'_'.$rows[csf('item_color')].'_'.$rows[csf('gmts_size_id')];
   
    $orderID=$order_dtls_arr[$key];
     //echo $key."</br>";
    if($orderID!='')  $order_id=$orderID; else $orderID=0;

    $rec_id_arr[]           =$rows[csf("id")];
    $data_array[$rows[csf("id")]]=explode("*",("'".$order_id."'"));
}
 //die;
$field_array="order_id";
//echo bulk_update_sql_statement("inv_trims_entry_dtls","id", $field_array,$data_array,$rec_id_arr); die;

$rID1=execute_query(bulk_update_sql_statement("inv_trims_entry_dtls","id", $field_array,$data_array,$rec_id_arr),1);

if($rID1) $flag=1; else $flag=0;

if($rID1 && $flag)
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
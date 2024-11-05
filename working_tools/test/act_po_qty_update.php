<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../includes/common.php');
$con=connect();
 
$sel_sql=sql_select("SELECT id, mst_id, po_qty from wo_po_acc_po_info_dtls where is_deleted=0 and to_char(insert_date,'YYYY')=2022");

foreach($sel_sql as $row){
    $dtls_data[$row[csf('mst_id')]]['po_qty'] +=$row[csf('po_qty')];
}

foreach($dtls_data as $id=>$data){
    $qty=$data["po_qty"];
    $update_query=execute_query("Update WO_PO_ACC_PO_INFO set ACC_PO_QTY=$qty, status_active=1 , is_deleted=0 where id= $id");
    if($update_query)
    {
        oci_commit($con);
        echo $id." Updated<br>";
    }
    else
    {
        oci_rollback($con);
        echo $id." Data Not update<br>";
    }
}
disconnect($con);
die;
   
?>
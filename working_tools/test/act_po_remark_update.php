<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../includes/common.php');
$con=connect();
 
$sel_sql=sql_select("SELECT po_break_down_id, acc_po_no, acc_ship_date, remarks from wo_po_acc_po_info_10_8_22 where is_deleted=0 and remarks is not null");

foreach($sel_sql as $row){
    $dtls_data[$row[csf('po_break_down_id')]][$row[csf('acc_po_no')]][$row[csf('acc_ship_date')]] =$row[csf('remarks')];
}

foreach($dtls_data as $poid=>$podata){
    foreach($podata as $pono=>$actdata){
        foreach($actdata as $shipdate=>$remark){
            $update_query=execute_query("Update WO_PO_ACC_PO_INFO set remarks='$remark' where po_break_down_id= $poid and acc_po_no='$pono' 
            and acc_ship_date='$shipdate'");
            if($update_query)
            {
                oci_commit($con);
                echo $pono." Updated<br>";
            }
            else
            {
                oci_rollback($con);
                echo $pono." Data Not update<br>";
            }
        }
    }
}
disconnect($con);
die;
   
?>
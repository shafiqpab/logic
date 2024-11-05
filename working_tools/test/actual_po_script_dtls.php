<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../includes/common.php');
$con=connect();
 
$sel_sql="select id, mst_id, po_break_down_id, country_id, gmts_item, gmts_color_id, gmts_size_id, po_qty, unit_price, unit_value, inserted_by, insert_date, updated_by, update_date, status_active, is_deleted FROM wo_po_acc_po_info_dtls_10_8_22 WHERE insert_date between '05-Aug-2022' and '11-Aug-2022' order by id ASC";

 $previous_data=sql_select($sel_sql);
 $dtls_id = return_next_id( "id", "wo_po_acc_po_info_dtls", 1 );
 $dtls_field_array="id,mst_id,po_break_down_id,country_id,gmts_item,gmts_color_id,gmts_size_id,po_qty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted";
 $new_mst_id_arr=return_library_array( "select id,old_mst_id from wo_po_acc_po_info", "old_mst_id", "id");
 $add_comma=0;
 foreach($previous_data as $row){
    if ($add_comma!=0) $mst_data_array .=",";
    $dtls_data_array .="(".$dtls_id.",".$new_mst_id_arr[$row[csf('mst_id')]].",'".$row[csf('po_break_down_id')]."',".$row[csf('country_id')].",".$row[csf('gmts_item')].",'".$row[csf('gmts_color_id')]."','".$row[csf('gmts_size_id')]."','".$row[csf('po_qty')]."',".$row[csf('inserted_by')].",'".$row[csf('insert_date')]."','".$row[csf('updated_by')]."','".$row[csf('update_date')]."',".$row[csf('status_active')].",".$row[csf('is_deleted')].")";
    
    $dtls_id=$dtls_id+1;
    $add_comma++;
 }
    echo "Insert INTO wo_po_acc_po_info_dtls ($dtls_field_array) values $dtls_data_array"; die;
    $rID=sql_insert("wo_po_acc_po_info_dtls",$dtls_field_array,$dtls_data_array,0);
    if($rID)
    {
        oci_commit($con);
        echo $add_comma." Total Actual PO<br>";
    }
    else
    {
        oci_rollback($con);
        echo $add_comma." Data Not insert<br>";
    }

disconnect($con);
die;
   
?>
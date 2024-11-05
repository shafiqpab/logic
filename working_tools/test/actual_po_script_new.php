<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../includes/common.php');
$con=connect();
 
$sel_sql="SELECT id, job_no, job_id, 
po_break_down_id, acc_po_no, acc_rcv_date, 
acc_ship_date, acc_revise_ship_date, acc_ship_mode, 
acc_po_status, acc_po_qty, acc_po_value, 
inserted_by, insert_date, updated_by, 
update_date, status_active, is_deleted, 
remarks
FROM wo_po_acc_po_info_10_8_22 where INSERT_DATE between '10-AUG-2022' AND '11-AUG-2022' 
ORDER BY ID ASC";

 $previous_data=sql_select($sel_sql);
 $mst_id = return_next_id( "id", "wo_po_acc_po_info", 1 );
 $mst_field_array="id,job_no,job_id,po_break_down_id,acc_po_no,acc_rcv_date,acc_ship_date,acc_ship_mode,acc_po_status,acc_po_qty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted,old_mst_id";
 //$dtls_field_array="id,mst_id,po_break_down_id,country_id,gmts_item,gmts_color_id,gmts_size_id,po_qty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted";
/*  echo "<pre>";
 print_r($previous_data); die; */
 $add_comma=0;
 foreach($previous_data as $row){
    if ($add_comma!=0) $mst_data_array .=",";
    $mst_data_array .="(".$mst_id.",'".$row[csf('job_no')]."',".$row[csf('job_id')].",".$row[csf('po_break_down_id')].",'".$row[csf('acc_po_no')]."','".$row[csf('cc_rcv_date')]."','".$row[csf('acc_ship_date')]."',".$row[csf('acc_ship_mode')].",".$row[csf('acc_po_status')].",".$row[csf('acc_po_qty')].",".$row[csf('inserted_by')].",'".$row[csf('insert_date')]."','".$row[csf('updated_by')]."','".$row[csf('update_date')]."',".$row[csf('status_active')].",".$row[csf('is_deleted')].",".$row[csf('id')].")";
    
    $mst_id=$mst_id+1;
    $add_comma++;
 }
    //echo "Insert INTO WO_PO_ACC_PO_INFO ($mst_field_array) values $mst_data_array"; die;
    $rID=sql_insert("WO_PO_ACC_PO_INFO",$mst_field_array,$mst_data_array,0);
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
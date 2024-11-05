<?
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../includes/common.php');
$con=connect();
 
$sel_sql="SELECT a.id, b.mst_id,a.PO_BREAK_DOWN_ID as mst_po,b.PO_BREAK_DOWN_ID as dtls_po from WO_PO_ACC_PO_INFO a join WO_PO_ACC_PO_INFO_DTLS b
on a.PO_BREAK_DOWN_ID=b.PO_BREAK_DOWN_ID where b.INSERT_DATE between '05-AUG-2022' AND '21-AUG-2022' and a.id<>b.mst_id 
group by a.id,b.mst_id,a.PO_BREAK_DOWN_ID,b.PO_BREAK_DOWN_ID";

$previous_data=sql_select($sel_sql);


disconnect($con);
die;
   
?>
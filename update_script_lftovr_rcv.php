<?
include('includes/common.php');

$sql_rcv=sql_select("SELECT c.DTLS_ID, sum(c.production_qnty) as QTY FROM pro_leftover_gmts_rcv_mst a, pro_leftover_gmts_rcv_dtls b, PRO_LEFTOVER_GMTS_RCV_CLR_SZ c WHERE a.id = b.mst_id AND a.id = c.mst_id AND b.id = c.dtls_id AND c.status_active = 1 AND b.status_active = 1 and a.status_active=1 and b.TOTAL_LEFT_OVER_RECEIVE is null group by c.dtls_id ");

foreach($sql_rcv as $row)
{
	$update_data_arr[$row[csf('DTLS_ID')]]=$row[csf('QTY')];
}

$con = connect();
foreach ($update_data_arr as $id => $qty) 
{
	$sql = execute_query("UPDATE pro_leftover_gmts_rcv_dtls set TOTAL_LEFT_OVER_RECEIVE=$qty where id=$id",1);
}

if($sql) echo "done";
































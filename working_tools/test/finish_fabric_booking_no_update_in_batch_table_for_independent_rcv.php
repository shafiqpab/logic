<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select(" select a.id, a.batch_no, c.booking_no, d.id as booking_id
    from pro_batch_create_mst a, pro_batch_create_dtls b, wo_booking_dtls c, wo_booking_mst d
    where a.id = b.mst_id and a.entry_form = 37 and b.po_id = c.po_break_down_id and c.booking_type =1 and c.status_active =1 and b.status_active =1 and a.status_active =1 and a.booking_no is null  and c.is_short =2 and c.booking_no = d.booking_no 
    group by a.id, a.batch_no, c.booking_no, d.id
    order by a.id desc ");
	//and a.id not in (45044,45602,45045,44844,49606,50548,49504,50542,48802,50560,50170,49499,51602,51603)

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($mis_match_sql as  $row) 
{
	$row[csf("booking_no")];
	$row[csf("booking_id")];

	//echo "update pro_batch_create_mst set booking_no = '".$row[csf("booking_no")]. "', booking_no_id = '".$row[csf("booking_id")]. "'  where id = ".$row[csf("id")]." <br>";
	execute_query("update pro_batch_create_mst set booking_no = '".$row[csf("booking_no")]. "', booking_no_id = '".$row[csf("booking_id")]. "'  where id = ".$row[csf("id")],0);
}


oci_commit($con);
echo "Success"; 
die;


?>
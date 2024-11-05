<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();
//http://localhost/platform-v3.5/working_tools/test/recipe_entry_missing_buyer_update_for_sample_booking.php

$mis_match_sql=sql_select("SELECT b.id, a.buyer_id as book_buyer_id
from wo_non_ord_samp_booking_mst a, pro_recipe_entry_mst b
where a.booking_no=b.style_or_order and b.buyer_id is null
and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 order by a.id desc");

if(empty($mis_match_sql))
{
	echo "Data Not Found";
	die;
}

foreach($mis_match_sql as $val)
{
	$recipe_id_arr[$val[csf("id")]] = $val[csf("id")];
	$booking_buyer_id_data[$val[csf("id")]]['book_buyer_id'] = $val[csf("book_buyer_id")];
}

$recipe_id_arr = array_filter($recipe_id_arr);

foreach ($recipe_id_arr as  $recipe_id) 
{
	$book_buyer_id = $booking_buyer_id_data[$recipe_id]['book_buyer_id'];

	//echo "update pro_recipe_entry_mst set buyer_id = ".$book_buyer_id." where id = ".$recipe_id." <br>";
	execute_query("update pro_recipe_entry_mst set buyer_id = ".$book_buyer_id." where id = ".$recipe_id,0);
}

oci_commit($con);
echo "Success"; 
disconnect($con);
die;


?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$delivery_sql=sql_select("select  b.grey_sys_id, a.id as delivery_id
from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c
where a.id = b.mst_id and  b.grey_sys_id = c.id  and a.entry_form = 54
group by b.grey_sys_id, a.id order by a.id");

if(empty($delivery_sql))
{
	echo "Data Not Found";
	die;
}

foreach($delivery_sql as $val)
{
	$grey_sys_arr[$val[csf("grey_sys_id")]] = $val[csf("grey_sys_id")];
	$delivery_id_arr[$val[csf("delivery_id")]] = $val[csf("delivery_id")];
	$deli_grey_ref_arr[$val[csf("delivery_id")]] = $val[csf("grey_sys_id")];
}


$grey_sys_arr = array_filter($grey_sys_arr);
$grey_sys_ids = implode(",", $grey_sys_arr);
$gsysCond = $all_grey_sys_cond = ""; 
if($db_type==2 && count($grey_sys_arr)>999)
{
	$grey_sys_arr_chunk=array_chunk($grey_sys_arr,999) ;
	foreach($grey_sys_arr_chunk as $chunk_arr)
	{
		$gsysCond.=" a.id in(".implode(",",$chunk_arr).") or ";	
	}
	$all_grey_sys_cond.=" and (".chop($gsysCond,'or ').")";
}
else
{ 	
	$all_grey_sys_cond=" and a.id in($grey_sys_ids)";  
}


$production_sql = sql_select("select a.id, a.company_id, a.location_id, a.knitting_location_id, a.knitting_company,knitting_source from inv_receive_master a where a.entry_form in (7) and a.status_active =1 $all_grey_sys_cond");

foreach ($production_sql as $val) 
{
	$production_ref_arr[$val[csf("id")]]["company_id"] =$val[csf("company_id")];
	$production_ref_arr[$val[csf("id")]]["location_id"] =$val[csf("location_id")];
	$production_ref_arr[$val[csf("id")]]["knitting_location_id"] =$val[csf("knitting_location_id")];
	$production_ref_arr[$val[csf("id")]]["knitting_company"] =$val[csf("knitting_company")];
	$production_ref_arr[$val[csf("id")]]["knitting_source"] =$val[csf("knitting_source")];
}


$delivery_id_arr = array_filter($delivery_id_arr);
$production_id = "";
foreach ($delivery_id_arr as  $deli_id) 
{
	$production_id =  $deli_grey_ref_arr[$deli_id];

	//echo "update pro_grey_prod_delivery_mst set location_id = '".$production_ref_arr[$production_id]["location_id"]."', knitting_source = '".$production_ref_arr[$production_id]["knitting_source"]."', knitting_company = '".$production_ref_arr[$production_id]["knitting_company"]."', knitting_location = '".$production_ref_arr[$production_id]["knitting_location_id"]."',  updated_by = 999 where id = ".$deli_id." <br>";
	
	execute_query("update pro_grey_prod_delivery_mst set location_id = '".$production_ref_arr[$production_id]["location_id"]."', knitting_source = '".$production_ref_arr[$production_id]["knitting_source"]."', knitting_company = '".$production_ref_arr[$production_id]["knitting_company"]."', knitting_location = '".$production_ref_arr[$production_id]["knitting_location_id"]."',  updated_by = 999 where id = ".$deli_id,0);
}


oci_commit($con);
echo "Success"; 
die;



?>
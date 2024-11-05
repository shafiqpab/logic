<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

/*$delivery_sql=sql_select("select  b.grey_sys_id, a.id as delivery_id
from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, inv_receive_master c
where a.id = b.mst_id and  b.grey_sys_id = c.id  and a.entry_form = 54
group by b.grey_sys_id, a.id order by a.id");*/

$delivery_sql=sql_select("select  b.id, b.fabric_shade as b_fs,a.fabric_shade as a_fs
from  pro_finish_fabric_rcv_dtls a, inv_transaction b
where a.trans_id = b.id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
");
//and b.id=3855503


if(empty($delivery_sql))
{
	echo "Data Not Found";
	die;
}

foreach($delivery_sql as $val)
{
	$trans_id_arr[$val[csf("id")]] = $val[csf("id")];
	$issue_dtls_fab_shade_arr[$val[csf("id")]] = $val[csf("a_fs")];
}

/*$trans_id_arr = array_filter($trans_id_arr);*/
$trans_ids = implode(",", $trans_id_arr);
$transCond = $all_trans_id_cond = ""; 
if($db_type==2 && count($trans_id_arr)>999)
{
	$trans_id_arr_chunk=array_chunk($trans_id_arr,999) ;
	foreach($trans_id_arr_chunk as $chunk_arr)
	{
		$transCond.=" a.trans_id in(".implode(",",$chunk_arr).") or ";	
	}
	$all_trans_id_cond.=" and (".chop($transCond,'or ').")";
}
else
{ 	
	$all_trans_id_cond=" and a.trans_id in($trans_ids)";  
}

//echo $all_trans_id_cond;die;

$inv_finish_fabric_issue_dtls_sql = sql_select("select a.trans_id, a.fabric_shade from pro_finish_fabric_rcv_dtls a where  a.status_active=1 and a.is_deleted=0 and a.fabric_shade is not NULL $all_trans_id_cond");
foreach ($inv_finish_fabric_issue_dtls_sql as $val) 
{
	$issue_dtls_fabric_shade_arr[$val[csf("trans_id")]]["fabric_shade"] =$val[csf("fabric_shade")];
	
}
//echo $issue_dtls_fabric_shade_arr[3855503]["fabric_shade"].'**'; die;
//print_r($issue_dtls_fabric_shade_arr); 
//die;
//$delivery_id_arr = array_filter($delivery_id_arr);
$trans_id = "";

foreach ($trans_id_arr as  $tId) 
{
	$trans_id =  $issue_dtls_fab_shade_arr[$tId];

	//echo "update inv_transaction set fabric_shade = '".$issue_dtls_fabric_shade_arr[3855503]["fabric_shade"]."',  updated_by = 999 where id = ".$tId." <br>";
	//die;
	execute_query("update inv_transaction set fabric_shade = '".$issue_dtls_fabric_shade_arr[$tId]["fabric_shade"]."',  updated_by = 999 where id = ".$tId,0);
}


oci_commit($con);
echo "Success"; 
die;



?>
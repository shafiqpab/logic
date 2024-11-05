<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$iss_ret_sql=sql_select("select entry_form, booking_id, booking_no, knitting_source,knitting_company, id 
from inv_receive_master 
where entry_form in (233) and status_active =1");

foreach($iss_ret_sql as $val)
{	
	$issue_return_ref_arr[$val[csf("id")]] = $val[csf("booking_id")];

	$issue_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
	$issue_return_id_arr[$val[csf("id")]] = $val[csf("id")];
}

$issue_id_arr = array_filter($issue_id_arr);
$issue_ids = implode(",", $issue_id_arr); 
$issCond = $all_issue_id_cond = ""; 
if($db_type==2 && count($issue_id_arr)>999)
{
	$issue_id_arr_chunk=array_chunk($issue_id_arr,999) ;
	foreach($issue_id_arr_chunk as $chunk_arr)
	{
		$issCond.=" id in(".implode(",",$chunk_arr).") or ";	
	}
	$all_issue_id_cond.=" and (".chop($issCond,'or ').")";	
}
else
{ 	
	$all_issue_id_cond=" and id in($issue_ids)";  
}


$issue_sql = sql_select("select entry_form, knit_dye_source, knit_dye_company,id from inv_issue_master where entry_form in (224) and status_active =1  $all_issue_id_cond ");

foreach ($issue_sql as $val) 
{
	$issue_ref_arr[$val[csf("id")]]["source"] = $val[csf("knit_dye_source")];
	$issue_ref_arr[$val[csf("id")]]["company"] = $val[csf("knit_dye_company")];
}


// receive return start ------------------

$rcv_return_sql = sql_select("select entry_form, received_id,received_mrr_no, id from inv_issue_master where entry_form in (287) and status_active =1");


foreach($rcv_return_sql as $val)
{	

	$rcv_return_ref_arr[$val[csf("id")]] = $val[csf("received_id")];

	$rcv_id_arr[$val[csf("received_id")]] = $val[csf("received_id")];
	$rcv_return_id_arr[$val[csf("id")]] = $val[csf("id")];
}

$rcv_id_arr = array_filter($rcv_id_arr);
$receive_ids = implode(",", $rcv_id_arr);
$rcvCond = $all_rcv_id_cond = ""; 
if($db_type==2 && count($rcv_id_arr)>999)
{
	$rcv_id_arr_chunk=array_chunk($rcv_id_arr,999) ;
	foreach($rcv_id_arr_chunk as $chunk_arr)
	{
		$rcvCond.=" id in(".implode(",",$chunk_arr).") or ";	
	}
	$all_rcv_id_cond.=" and (".chop($rcvCond,'or ').")";	
}
else
{ 	
	$all_rcv_id_cond=" and id in($receive_ids)";  
}


$receive_sql = sql_select("select entry_form, knitting_source,knitting_company, id from inv_receive_master where entry_form in (225) and status_active =1");

foreach ($receive_sql as $val) 
{
	$rcv_ref_arr[$val[csf("id")]]["source"] = $val[csf("knitting_source")];
	$rcv_ref_arr[$val[csf("id")]]["company"] = $val[csf("knitting_company")];
}



$issue_return_id_arr = array_filter($issue_return_id_arr);
foreach ($issue_return_id_arr as  $issRetId) 
{
	$issId =  $issue_return_ref_arr[$issRetId];

	$issue_knit_source = $issue_ref_arr[$issId]["source"];
	$issue_knit_company = $issue_ref_arr[$issId]["company"];

	if($issue_knit_source)
	{
		//echo "update inv_receive_master set knitting_source = '".$issue_knit_source. "', knitting_company = '".$issue_knit_company."', updated_by = 999 where id = ".$issRetId." <br>";
		execute_query("update inv_receive_master set knitting_source = '".$issue_knit_source. "', knitting_company = '".$issue_knit_company."', updated_by = 999 where id = ".$issRetId,0);
	}
}

//echo "<br><br>---------------------------------------<br><br>";

$rcv_return_id_arr = array_filter($rcv_return_id_arr);
foreach ($rcv_return_id_arr as  $rcvRetId) 
{
	$rcvId =  $rcv_return_ref_arr[$rcvRetId];

	$rcv_knit_source = $rcv_ref_arr[$rcvId]["source"];
	$rcv_knit_company = $rcv_ref_arr[$rcvId]["company"];
	if($rcv_knit_source)
	{
		//echo "update inv_issue_master set knit_dye_source = '".$rcv_knit_source. "', knit_dye_company = '".$rcv_knit_company."', updated_by = 999 where id = ".$rcvRetId." <br>";
		execute_query("update inv_issue_master set knit_dye_source = '".$rcv_knit_source. "', knit_dye_company = '".$rcv_knit_company."', updated_by = 999 where id = ".$rcvRetId,0);
	}
}




oci_commit($con);
echo "Success"; 
die;


?>
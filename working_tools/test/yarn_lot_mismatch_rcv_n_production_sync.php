<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$mis_match_sql=sql_select("select a.barcode_no,b.id as prod_dtls, b.yarn_lot as  production_yarn, d.yarn_lot receive_yarn, d.id as rcv_dtls from pro_grey_prod_entry_dtls b, pro_roll_details a, pro_roll_details c, pro_grey_prod_entry_dtls d where b.id=a.dtls_id and a.entry_form = 2 and a.barcode_no = c.barcode_no and c.dtls_id = d.id and c.entry_form  = 58 and a.status_active =1 and b.status_active =1 and c.status_active =1 and d.status_active =1 and b.yarn_lot != d.yarn_lot ");

if(empty($mis_match_sql))
{
	echo "Mismatch Data Not Found";
	die;
}

foreach($mis_match_sql as $val)
{
	$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	$production_ref[$val[csf("barcode_no")]]["production_yarn"] = $val[csf("production_yarn")];
	$production_ref[$val[csf("barcode_no")]]["prod_dtls"] = $val[csf("prod_dtls")];
	$production_ref[$val[csf("barcode_no")]]["rcv_dtls"] = $val[csf("rcv_dtls")];
}

$barcode_arr = array_filter($barcode_arr);
$barcode_nos = implode(",", $barcode_arr); 
$barCond = $all_barcode_no_cond = ""; 
if($db_type==2 && count($barcode_arr)>999)
{
    $barcode_arr_chunk=array_chunk($barcode_arr,999) ;
    foreach($barcode_arr_chunk as $chunk_arr)
    {
        $barCond.=" barcode_no in(".implode(",",$chunk_arr).") or ";  
    }
    $all_barcode_no_cond.=" and (".chop($barCond,'or ').")";    
}
else
{   
    $all_barcode_no_cond=" and barcode_no in($barcode_nos)";  
}


$issue_sql = sql_select("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted =0   $all_barcode_no_cond");

foreach ($issue_sql as $val) 
{
    $issue_arr[$val[csf("barcode_no")]]["barcode"] = $val[csf("barcode_no")];
}


foreach ($mis_match_sql as  $row) 
{
	if($issue_arr[$row[csf("barcode_no")]]["barcode"] == "")
	{
		$rcv_dtls = $row[csf("rcv_dtls")];
		$production_yarn = $row[csf("production_yarn")];

		//echo "update pro_grey_prod_entry_dtls set yarn_lot = '".$production_yarn. "', updated_by = 999 where id = ".$rcv_dtls." <br>";
		execute_query("update pro_grey_prod_entry_dtls set yarn_lot = '".$production_yarn. "', updated_by = 999 where id = ".$rcv_dtls,0);
	}
	
}

oci_commit($con);
echo "Success"; 
die;


?>
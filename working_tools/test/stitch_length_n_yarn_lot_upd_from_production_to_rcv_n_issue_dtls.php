<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$mismatch_sql=sql_select("select a.barcode_no, b.yarn_lot as prod_lot, b.stitch_length as prod_stitch, a.entry_form as prod_entry_form 
    from pro_roll_details a , pro_grey_prod_entry_dtls b
    where a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and a.dtls_id=b.id and b.status_active=1 and b.is_deleted=0 and b.trans_id=0 ");

if(empty($mismatch_sql))
{
    echo "Mismatch Not Found";
    die;
}

foreach($mismatch_sql as $val)
{   
    $prod_data[$val[csf("barcode_no")]]["stitch_length"] = $val[csf("prod_stitch")];
    $prod_data[$val[csf("barcode_no")]]["prod_lot"] = $val[csf("prod_lot")];
    $barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
}



$barcode_arr = array_filter($barcode_arr);
$barcode_nos = implode(",", $barcode_arr); 
$barCond = $all_barcode_no_cond = ""; 
if($db_type==2 && count($barcode_arr)>999)
{
    $barcode_arr_chunk=array_chunk($barcode_arr,999) ;
    foreach($barcode_arr_chunk as $chunk_arr)
    {
        $barCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";  
    }
    $all_barcode_no_cond.=" and (".chop($barCond,'or ').")";    
}
else
{   
    $all_barcode_no_cond=" and c.barcode_no in($barcode_nos)";  
}


/*$rcv_sql = sql_select("select c.barcode_no, d.yarn_lot, d.stitch_length, c.entry_form, d.id 
    from pro_roll_details c,pro_grey_prod_entry_dtls d 
    where c.entry_form=58 and c.status_active=1 and c.dtls_id=d.id and d.status_active=1");

foreach ($rcv_sql as $val) 
{
    $recv_data[$val[csf("barcode_no")]]["stitch_length"] = $val[csf("prod_stitch")];
    $recv_data[$val[csf("barcode_no")]]["prod_lot"] = $val[csf("prod_lot")];
    $recv_data[$val[csf("barcode_no")]]["dtls_id"] = $val[csf("id")];
    $recv_data[$val[csf("barcode_no")]]["barcode_no"] = $val[csf("barcode_no")];
}*/

/*echo "<pre>";
print_r($recv_data);die;*/


//echo "select c.barcode_no, d.yarn_lot, d.stitch_length, c.entry_form, d.id    from  pro_roll_details c, inv_grey_fabric_issue_dtls d  where  c.entry_form = 61 and c.status_active =1 and d.status_active=1 and c.dtls_id = d.id    ";die;

$issue_sql = sql_select("select c.barcode_no, d.yarn_lot, d.stitch_length, c.entry_form, d.id 
    from  pro_roll_details c, inv_grey_fabric_issue_dtls d 
    where  c.entry_form = 61 and c.status_active =1 and d.status_active=1 and c.dtls_id = d.id ");

foreach($issue_sql as $val)
{   
    $issue_data[$val[csf("barcode_no")]]["stitch_length"] = $val[csf("prod_stitch")];
    $issue_data[$val[csf("barcode_no")]]["prod_lot"] = $val[csf("prod_lot")];
    $issue_data[$val[csf("barcode_no")]]["dtls_id"] = $val[csf("id")];
    $issue_data[$val[csf("barcode_no")]]["barcode_no"] = $val[csf("barcode_no")];
}

foreach($mismatch_sql as $val)
{
    $stitch_length = $prod_data[$val[csf("barcode_no")]]["stitch_length"];
    $prod_lot = $prod_data[$val[csf("barcode_no")]]["prod_lot"];

/*  if($recv_data[$val[csf("barcode_no")]]["barcode_no"])
    {
        if(($stitch_length != $recv_data[$val[csf("barcode_no")]]["stitch_length"]) || ($prod_lot != $recv_data[$val[csf("barcode_no")]]["prod_lot"]))
        {
            $rcv_dtls_id = $recv_data[$val[csf("barcode_no")]]["dtls_id"];
            //echo "update pro_grey_prod_entry_dtls set yarn_lot = '".$prod_lot. "', stitch_length = '".$stitch_length."', updated_by = 999 where id = ".$rcv_dtls_id." <br>";

            execute_query("update pro_grey_prod_entry_dtls set yarn_lot = '".$prod_lot. "', stitch_length = '".$stitch_length."', updated_by = 999 where id = ".$rcv_dtls_id,0);
        }
    }*/

    if($issue_data[$val[csf("barcode_no")]]["barcode_no"])
    {
        if(($stitch_length != $issue_data[$val[csf("barcode_no")]]["stitch_length"]) || ($prod_lot != $issue_data[$val[csf("barcode_no")]]["prod_lot"]))
        {
            $issue_dtls_id = $issue_data[$val[csf("barcode_no")]]["dtls_id"];

            //echo "update inv_grey_fabric_issue_dtls set yarn_lot = '".$prod_lot. "', stitch_length = '".$stitch_length."', updated_by = 999 where id = ".$issue_dtls_id." <br>";

            execute_query("update inv_grey_fabric_issue_dtls set yarn_lot = '".$prod_lot. "', stitch_length = '".$stitch_length."', updated_by = 999 where id =".$issue_dtls_id,0);
        }
    }
}





oci_commit($con);
echo "Success"; 
die;


?>
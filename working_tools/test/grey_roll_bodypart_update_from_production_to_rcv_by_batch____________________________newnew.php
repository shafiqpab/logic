<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$rcv_by_batch_sql=sql_select("select c.barcode_no, d.id as  dtls_id
from  pro_roll_details c, pro_grey_batch_dtls d 
 where  c.dtls_id = d.id and c.mst_id=d.mst_id and  c.entry_form = 62 and c.status_active =1 and d.status_active=1 and d.BODY_PART_ID=0 and c.barcode_no !=0
 order by d.id desc");

if(empty($rcv_by_batch_sql))
{
    echo "Mismatch Not Found";
    die;
}

foreach($rcv_by_batch_sql as $val)
{ 
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



$rcv_sql = sql_select("select c.barcode_no, d.body_part_id
    from pro_roll_details c,pro_grey_prod_entry_dtls d 
    where c.entry_form=58 and c.status_active=1 and c.dtls_id=d.id and d.status_active=1 $all_barcode_no_cond");

foreach ($rcv_sql as $val) 
{
    $recv_data[$val[csf("barcode_no")]]["body_part_id"] = $val[csf("body_part_id")];
}

foreach($rcv_by_batch_sql as $val)
{
    $body_part_id = $recv_data[$val[csf("barcode_no")]]["body_part_id"] ;

    if($body_part_id)
    {
        echo "update pro_grey_batch_dtls set body_part_id = $body_part_id where id =".$val[csf("dtls_id")]." <br>";

       //execute_query("update pro_grey_batch_dtls set body_part_id = $body_part_id where id =".$val[csf("dtls_id")],0);
    }else{
        echo "update pro_grey_batch_dtls set body_part_id = $body_part_id where id =".$val[csf("dtls_id")]."=====".$val[csf("barcode_no")]." <br>";
    }
    
    
}



/*oci_commit($con);
echo "Success"; 
die;*/


?>
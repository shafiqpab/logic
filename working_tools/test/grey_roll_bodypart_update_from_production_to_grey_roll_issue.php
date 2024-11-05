<?
header('Content-type:text/html; charset=utf-8');
session_start();

include('../includes/common.php');
$con=connect();

$user_name = $_SESSION['logic_erp']['user_id'];
/*$rcv_by_batch_sql=sql_select("select c.barcode_no, d.id as  dtls_id
from  pro_roll_details c, pro_grey_batch_dtls d 
 where  c.dtls_id = d.id and c.mst_id=d.mst_id and  c.entry_form = 62 and c.status_active =1 and d.status_active=1 and d.BODY_PART_ID=0 and c.barcode_no !=0
 order by d.id desc");*/

 $rcv_by_batch_sql=sql_select("select c.barcode_no, d.id as  dtls_id
 from  pro_roll_details c, inv_grey_fabric_issue_dtls d 
 where  c.dtls_id = d.id and c.mst_id=d.mst_id and  c.entry_form = 61 and c.status_active =1 and d.status_active=1 and (d.body_part_id is null or d.body_part_id=0) and c.barcode_no !=0
 order by d.id desc");

if(empty($rcv_by_batch_sql))
{
    echo "Mismatch Not Found";
    die;
}


 $r_id3=execute_query("delete from tmp_barcode_no where userid=$user_name");

if($r_id3)
{
    oci_commit($con);
}


foreach($rcv_by_batch_sql as $val)
{ 
    //$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];


    if(!$barcode_arr[$val[csf('barcode_no')]])
    {
        $barcode_arr[$val[csf('barcode_no')]] = $val[csf('barcode_no')];
        $rID5=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_name,".$val[csf('barcode_no')].")");
    }
}

oci_commit($con);


$rcv_sql = sql_select("select c.barcode_no, d.body_part_id
    from pro_roll_details c,pro_grey_prod_entry_dtls d, tmp_barcode_no e
    where c.entry_form=58 and c.status_active=1 and c.dtls_id=d.id and d.status_active=1 and c.barcode_no=e.barcode_no and e.userid=$user_name");

foreach ($rcv_sql as $val) 
{
    $recv_data[$val[csf("barcode_no")]]["body_part_id"] = $val[csf("body_part_id")];
}



foreach($rcv_by_batch_sql as $val)
{
    $body_part_id = $recv_data[$val[csf("barcode_no")]]["body_part_id"];

    if($body_part_id)
    {
        //echo "update inv_grey_fabric_issue_dtls set body_part_id = $body_part_id where id =".$val[csf("dtls_id")]." <br>";
        execute_query("update inv_grey_fabric_issue_dtls set body_part_id = $body_part_id where id =".$val[csf("dtls_id")],0);
    }else{
        echo "update inv_grey_fabric_issue_dtls set body_part_id = $body_part_id where id =".$val[csf("dtls_id")]."=====".$val[csf("barcode_no")]." <br>";
    }
    // echo "update pro_grey_batch_dtls set body_part_id = $body_part_id where id =".$val[csf("dtls_id")]."=====".$val[csf("barcode_no")]." <br>";
    
}


$r_id3=execute_query("delete from tmp_barcode_no where userid=$user_name");


oci_commit($con);

//oci_commit($con);
echo "Success"; 
die;


?>
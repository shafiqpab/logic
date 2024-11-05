<?php
date_default_timezone_set("Asia/Dhaka");
require_once('includes/common.php');

$user_id=99999;
if($job_no!="") { $jobCond="and a.job_no='$job_no'"; $jobCondS="and job_no='$job_no'"; } else { $jobCond=""; $jobCondS="";}
$sqlpo="SELECT a.id AS new_id, b.id AS old_id
            FROM wo_po_acc_po_info a, wo_po_acc_po_info_bk b, wo_po_acc_po_info_dtls c
            WHERE     a.status_active = 1
                AND a.is_deleted = 0
                AND a.id = c.mst_id
                AND a.po_break_down_id = b.po_break_down_id
                AND a.acc_po_no = b.acc_po_no
                AND a.acc_ship_date = b.acc_ship_date
                AND c.country_id = b.country_id
                AND c.gmts_item = b.gmts_item
                AND c.gmts_color_id = b.gmts_color_id
                AND c.gmts_size_id = b.gmts_size_id
                AND c.status_active = 1
                AND c.is_deleted = 0
                AND b.is_deleted = 0
                AND b.status_active = 1
                AND (a.po_break_down_id || '-' || c.country_id || '-' || c.gmts_item) IN
                        (SELECT    m.po_break_down_id || '-' || m.country_id || '-' || m.item_number_id
                            FROM pro_ex_factory_mst m
                            WHERE m.actual_po IS NOT NULL )
            GROUP BY a.id,
                b.id
            ORDER BY a.id";
//echo $sqlpo; die; //and a.job_no='$job_no'
$sqlpoRes = sql_select($sqlpo);
//print_r($sqlpoRes); die;
$actual_po_arr=array(); 
foreach($sqlpoRes as $row)
{
	$actual_po_arr[$row[csf('OLD_ID')]]=$row[csf('NEW_ID')];
}
unset($sqlpoRes);

$exfactory_sql="SELECT m.id as ID, m.actual_po AS ACTUAL_PO
                        FROM pro_ex_factory_mst m
                        WHERE m.actual_po IS NOT NULL
                        ";

$exfactory_res=sql_select($exfactory_sql);

$act_poid = return_next_id("id", "pro_ex_factory_actual_po_details", 1);
$act_data=array();
$i=0;
foreach( $exfactory_res as $row)
{
    $actual_po=explode(",",$row['ACTUAL_PO']);
    $actual_pos=array();
    foreach($actual_po as $a_po)
    {
        $new_po=$a_po;
        if(!empty($actual_po_arr[$a_po]))
        {
            $new_po=$actual_po_arr[$a_po];
        }
        $act_data[$act_poid]= "(" . $act_poid . "," .$row['ID'] . "," . $new_po . ",'" . $user_id . "','" . $pc_date_time . "',1,0)";
        $act_poid = $act_poid + 1;
        $i++;
    }
}
print_r($act_data);die;
$con = connect();
if($db_type==0) mysql_query("BEGIN");
$data_chunk=array_chunk($act_data,50);
$field_actual_po = "id,mst_id,actual_po_id,updated_by,update_date,status_active,is_deleted";
$msgsucc="$i  Actual Po Process Success.";
$msgfail="$i  Actual Po Process Fail.";
$flag=1;
foreach( $data_chunk as $chunkRows)
{

	$rID=sql_insert("pro_ex_factory_actual_po_details",$field_actual_po,implode(",",$chunkRows),1);
	if($rID==1 && $flag==1) $flag=1; //else $flag=0;
	else if($rID==0) 
	{
		$flag=0;
		oci_rollback($con); 
		echo "10**".$msgfail."**INSERT INTO pro_ex_factory_actual_po_details (".$field_actual_po.") VALUES ".implode(",",$chunkRows); 
		disconnect($con); die;
	}
}
if($db_type==0)
{
	if($flag==1)
	{
		mysql_query("COMMIT");  
		echo "0**".$msgsucc;
	}
	else
	{
		mysql_query("ROLLBACK"); 
		echo "10**".$msgfail;
	}
}
else if($db_type==2)
{
	if($flag==1)
	{
		oci_commit($con);
		echo "0**".$msgsucc;
	}
	else
	{
		oci_rollback($con);
		echo "10**".$msgfail;
	}
}
disconnect($con);
die;


?> 
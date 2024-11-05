<?php
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();



$blank_challan_barcode = sql_select("select id,date_name from PPL_ORDER_ALLOCATION_DTLS where is_deleted = 0 and status_active = 1");

foreach ($blank_challan_barcode as $val) 
{
	$dtls_no_arr[$val[csf("id")]] = $val[csf("id")];
}

//print_r($dtls_no_arr);die;
$barcode_nos = implode(",", array_filter(array_unique($dtls_no_arr)));

if($barcode_nos=="") {echo "No Blank Challan Found"; die;}

$barCond = $barcode_no_cond = ""; 
$dtls_no_arr=explode(",",$barcode_nos);
if($db_type==2 && count($dtls_no_arr)>999)
{
	$barcode_no_chunk=array_chunk($dtls_no_arr,999) ;
	foreach($barcode_no_chunk as $chunk_arr)
	{
		$barCond.=" id in(".implode(",",$chunk_arr).") or ";	
	}

	$barcode_no_cond.=" and (".chop($barCond,'or ').")";			
	
}
else
{ 	
	
	$barcode_no_cond=" and id in($barcode_nos)";
}

//echo $barcode_no_cond; die;

$ref_sql_from_issue = sql_select("select id,date_name from PPL_ORDER_ALLOCATION_DTLS where is_deleted = 0 and status_active = 1 $barcode_no_cond");

foreach ($ref_sql_from_issue as $row) 
{
	//$issue_number_prefix=explode('-', $row[csf("issue_number_prefix")]);
	//$issue_number_prefix_string=$issue_number_prefix[0].'-'.'WFFI-'.$issue_number_prefix[2].'-';
	//if($db_type==0)	$date_format_name=$data[0]; else $date_format_name=date("d-M-Y",strtotime($data[0]));


	if($db_type==0)	$issue_number_string=$row[csf("date_name")]; else $issue_number_string=date("d-M-Y",strtotime($row[csf("date_name")]));
	//if($db_type==0)	$issue_number_string=$row[csf("date_name")]; else $issue_number_string=$row[csf("date_name")];


	//$issue_number=explode('-', $row[csf("date_name")]);
	//$issue_number_string=$issue_number[0].'-'.'WFFI-'.$issue_number[2].'-'.$issue_number[3];

	//$ref_data_arr[$row[csf("id")]]["issue_number_prefix"] =$issue_number_prefix_string; 
	$ref_data_arr[$row[csf("id")]]["issue_number"] = $issue_number_string;
}
//print_r($ref_data_arr[106]["issue_number"] ); die;

$flag=1;
foreach ($blank_challan_barcode as $val) 
{	

	$deleted_roll_id[]=$val[csf("id")];
	$data_array_roll_deleted[$val[csf("id")]]=explode("*",("'".$ref_data_arr[$row[csf("id")]]["issue_number"]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

	if($flag==1)
	{
		//$prefix = $ref_data_arr[$val[csf("id")]]["issue_number_prefix"];
		$issue_number = $ref_data_arr[$val[csf("id")]]["issue_number"];
		//$issue_number = $ref_data_arr[106]["issue_number"];
		$id=$val[csf("id")];
			//echo "update inv_issue_master set issue_number_prefix='$prefix',issue_number ='$issue_number',updated_by=9 where id=$id <br />";
		//echo "update PPL_ORDER_ALLOCATION_DTLS set date_format_name ='$issue_number',updated_by=9 where id=$id"; die;
		execute_query("update PPL_ORDER_ALLOCATION_DTLS set date_format_name ='$issue_number',updated_by=9 where id=$id",0);
		//die;
	}
}

if($db_type==0)
	{
		mysql_query("COMMIT");  
		echo "10** Success";		
	}
if($db_type==2 || $db_type==1 )
	{
		oci_commit($con);   
		echo "10** Success";
	}




//oci_commit($con); 
//echo "Success";
disconnect($con);
die;


?>
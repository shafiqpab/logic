<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();


$blank_challan_barcode = sql_select("select b.barcode_no,a.id from inv_receive_mas_batchroll a, pro_roll_details b where b.entry_form = 62 and a.entry_form = 62 and a.id = b.mst_id and (a.challan_no = '' or a.challan_no is null) and b.is_deleted = 0 and b.status_active = 1");

foreach ($blank_challan_barcode as $val) 
{
	$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
}


$barcode_nos = implode(",", array_filter(array_unique($barcode_no_arr)));

if($barcode_nos=="") {echo "No Blank Challan Found"; die;}

$barCond = $barcode_no_cond = ""; 
$barcode_no_arr=explode(",",$barcode_nos);
if($db_type==2 && count($barcode_no_arr)>999)
{
	$barcode_no_chunk=array_chunk($barcode_no_arr,999) ;
	foreach($barcode_no_chunk as $chunk_arr)
	{
		$barCond.=" d.barcode_no in(".implode(",",$chunk_arr).") or ";	
	}
			
	$barcode_no_cond.=" and (".chop($barCond,'or ').")";			
	
}
else
{ 	
	
	$barcode_no_cond=" and d.barcode_no in($barcode_nos)";
}


	$ref_sql_from_issue = sql_select("select c.issue_number, d.barcode_no, c.knit_dye_source,c.knit_dye_company
	from inv_issue_master c, pro_roll_details d
	where c.id = d.mst_id and c.entry_form = 61 and d.entry_form =61 and d.status_active = 1 and d.is_deleted=0 $barcode_no_cond ");

	foreach ($ref_sql_from_issue as $row) 
	{
		$ref_data_arr[$row[csf("barcode_no")]]["challan_no"] = $row[csf("issue_number")];
		$ref_data_arr[$row[csf("barcode_no")]]["knit_dye_source"] = $row[csf("knit_dye_source")];
		$ref_data_arr[$row[csf("barcode_no")]]["knit_dye_company"] = $row[csf("knit_dye_company")];
	}



	$flag=1;
	foreach ($blank_challan_barcode as $val) 
	{	
		
		if($flag==1)
		{
		execute_query("update inv_receive_mas_batchroll set challan_no='".$ref_data_arr[$val[csf("barcode_no")]]["challan_no"]."',dyeing_source =". $ref_data_arr[$val[csf("barcode_no")]]["knit_dye_source"] .",dyeing_company=". $ref_data_arr[$val[csf("barcode_no")]]["knit_dye_company"] . ",updated_by=9999 where id=".$val[csf("id")],0);
		}
	}

echo "Success";
disconnect($con);
die;
 
?>
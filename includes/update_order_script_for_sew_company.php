<?
include('common.php');
 $field_data=sql_select("select job_no,company_name,location_name from wo_po_details_master ");
$ok="";
$nok="";
foreach($field_data as $row)
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN"); 
	}
	$rID = execute_query("UPDATE wo_po_break_down SET sewing_company_id=".$row[csf('company_name')].", sewing_location_id
=".$row[csf('location_name')]." WHERE job_no_mst='".$row[csf('job_no')]."' ");

	if($db_type==2)
	{
		if($rID)
		{
			oci_commit($con); 
			$ok.= $row[csf('job_no')].", "; 
			//echo "0**".$rID."<br/>";
		}
		else{
			oci_rollback($con);
			$nok.= $row[csf('job_no')].", "; 
			//echo "10**".$rID;
		}
	}
	if($db_type==0) //Mysql DB
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				$ok.= $row[csf('job_no')].", "; 
			}
			else
			{	mysql_query("ROLLBACK"); 
				$nok.= $row[csf('job_no')].", "; 
			}
		}

     disconnect($con);
}

$mst_ok=rtrim($ok,', ');
$mst_nok=rtrim($nok,', ');
$row_count_ok=count(explode(', ',$mst_ok));
$row_count_nok=count(explode(', ',$mst_nok));
echo "OK- Total Job=".$row_count_ok.', Job No='.$mst_ok;
echo "<br/>";
echo "NOK- Total Job=".$row_count_nok.', Job No='.$mst_nok;

?>

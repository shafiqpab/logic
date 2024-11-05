<?
include('common.php');
 $field_data=sql_select('select id as mst_id,sample_type from inv_issue_master where entry_form in (18) and item_category=2');
$ok="";
$nok="";
foreach($field_data as $row)
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID = execute_query("UPDATE inv_finish_fabric_issue_dtls SET sample_type=".$row[csf('sample_type')]." WHERE mst_id=".$row[csf('mst_id')]." ");
	if($db_type==2)
	{
		if($rID)
		{
			oci_commit($con); 
			$ok.= $row[csf('mst_id')].","; 
			//echo "0**".$rID."<br/>";
		}
		else{
			oci_rollback($con);
			$nok.= $row[csf('mst_id')].","; 
			//echo "10**".$rID;
		}
	}
	if($db_type==0) //Mysql DB
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				$ok.= $row[csf('mst_id')].","; 
			}
			else
			{	mysql_query("ROLLBACK"); 
				$nok.= $row[csf('mst_id')].","; 
			}
		}

     disconnect($con);
}
$mst_ok=$ok;
$mst_nok=$nok;
$ok=rtrim($ok,',');
$nok=rtrim($nok,',');
$row_count_ok=count(explode(',',$ok));
$row_count_nok=count(explode(',',$nok));
echo "OK- Tot rows=".$row_count_ok.' MST ID='.$mst_ok;
echo "<br/>";
echo "NOK- Tot rows=".$row_count_nok.' MST ID='.$mst_nok;

?>

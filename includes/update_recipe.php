<?
include('common.php');
 $field_data=sql_select('select id,liquor_ratio,total_liquor from pro_recipe_entry_mst where entry_form in(59,60)');
$ok="";
$nok="";
foreach($field_data as $row)
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$rID = execute_query("UPDATE pro_recipe_entry_dtls SET liquor_ratio=".$row[csf('liquor_ratio')].",total_liquor=".$row[csf('total_liquor')]." WHERE mst_id=".$row[csf('id')]." ");
	
	if($db_type==2)
	{
		if($rID)
		{
			oci_commit($con); 
			$ok.= $row[csf('id')].","; 
			//echo "0**".$rID."<br/>";
		}
		else{
			oci_rollback($con);
			$nok.= $row[csf('id')].","; 
			//echo "10**".$rID;
		}
	}
	if($db_type==0) //Mysql DB
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				$ok.= $row[csf('id')].","; 
			}
			else
			{	mysql_query("ROLLBACK"); 
				$nok.= $row[csf('id')].","; 
			}
		}

     disconnect($con);
}
echo "OK- ".$ok;
echo "<br/>";
echo "NOK- ".$nok;
 

?>

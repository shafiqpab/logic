<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');
$con=connect();


$getData_sql =  sql_select("select id,size_wise_prog_string from ppl_color_wise_break_down where size_wise_prog_string is not null");


if(empty($getData_sql))
{
	echo "Data Not Found";
	die;
}

foreach ($getData_sql as $val) 
{
	$color_wise_arr[$val[csf("id")]]['size_wise_prog_string']=str_replace(",","##",$val[csf("size_wise_prog_string")]);
}

$dtlsIDChk=array();
foreach ($color_wise_arr as $dtlsID => $row) 
{
	/*foreach ($dtlsData as $poid => $row) 
	{*/
		
		//execute_query("update ppl_color_wise_break_down set size_wise_prog_string='".$row["size_wise_prog_string"]."'  where id=$dtlsID and  size_wise_prog_string is not null",0);
		echo "update ppl_color_wise_break_down set size_wise_prog_string='".$row["size_wise_prog_string"]."' where id=$dtlsID and  size_wise_prog_string is not null <br>";	

		
		
	//}

}

/*oci_commit($con); 
echo "Success";
disconnect($con);
die;*/
 
?>
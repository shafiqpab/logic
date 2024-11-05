<?
include('common.php');
$file = fopen("cuncsv.csv","r");
$i=0;
$id=return_next_id( "id", "lib_country", 1 ) ;
//$id=1 ;
$field_array='id,country_name,short_name';
while(! feof($file))
{
	$data=fgetcsv($file);
	if($i==0){
		$i++;
	}
	else{
	if ($i!=1) $data_array .=",";
	$data_array ="(".$id.",'".$data[1]."','".$data[3]."')";
	$con = connect();
	$rID=sql_insert("lib_country",$field_array,$data_array,1);
	if($rID )
	{
		oci_commit($con);   
		echo "0**".$rID."<br/>";
	}
	else{
		oci_rollback($con);
		echo "10**".$rID;
	}

     disconnect($con);
	$id=$id+1;
	$i++;
	}
}
  //echo "insert into lib_machine_name (".$field_array.") values ". $data_array;
  //die;


//echo $data_array;
fclose($file);
?>

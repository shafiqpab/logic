<?
include('common.php');
$file = fopen("datacsv.csv","r");
$i=0;
$id=return_next_id( "id", "lib_machine_name", 1 ) ;
//$id=1 ;
$field_array='id,company_id,machine_no,machine_group,remark,brand,capacity_uom_id,category_id,inserted_by,is_locked';
while(! feof($file))
{
	$data=fgetcsv($file);
	if($i==0){
		$i++;
	}
	else{
	if ($i!=1) $data_array .=",";
	$data_array ="(".$id.",3,'".$data[1]."','".$data[2]."','".$data[3]."','".$data[4]."',1,8,999,999)";
	$con = connect();
	$rID=sql_insert("lib_machine_name",$field_array,$data_array,1);
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

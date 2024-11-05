<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

$color_range = array(1 => "Dark Color", 2 => "Light Color", 3 => "Black Color", 4 => "White Color", 5 => "Average Color", 6 => "Melange", 7 => "Wash", 8 => "Scouring", 9 => "Extra Dark", 10 => "Medium Color", 11 => "Super Dark", 12 => "Royal color",13 => "Average-Double Dyeing",14 => "Dark - Double Dyeing",15 => "Black-Double Dyeing",16 => "Light-Double Dyeing",17 => "Medium-Double Dyeing",18 => "Extra Dark-Double Dyeing",19 => "Peroxide Wash");

$field_array='id,name,status_active';
foreach($color_range as $id=>$name){
	$data_array ="(".$id.",'".$name."',1)";
	$con = connect();
	$rID=sql_insert("color_range_riaz",$field_array,$data_array,1);
	if($rID ){
		oci_commit($con);   
		echo $id."==Inserted".$rID."<br/>";
	}
	else{
		oci_rollback($con);
		echo $id."== Not Inserted".$rID;
	}
    disconnect($con);
}
die("ok");
?>
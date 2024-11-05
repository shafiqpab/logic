<?

include('includes/common.php');
//print_r($entry_form);
$array_name = ${$_GET["array_name"]};
foreach($array_name as $row)
{
	echo $row."<br />";
}
die;
?>
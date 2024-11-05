<?
$name_of_file = $_GET["download_file"];
 header("Content-Type: text/plain");
    header("Content-Disposition: attachment; filename=$name_of_file");
	
	echo $name_of_file;
?>
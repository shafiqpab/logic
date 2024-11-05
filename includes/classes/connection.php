<?php
function pdo_connect(){
	try {
	$dbh = new PDO('oci:dbname=//192.168.11.72:1521/TEST', 'LOGIC3RDVERSION', 'LOGIC3RDVERSION');
	//$dbh = new PDO('mysql:dbname=db_xframejs;host=localhost', 'root', '');
	return $dbh;
	}
	catch (PDOException $e) {
		echo "Failed to obtain database handle: " . $e->getMessage();
		exit;
	}
}
function pdo_select($sql,$index){
	$dbh=pdo_connect();
	$s = $dbh->prepare($sql,array(PDO::ATTR_PREFETCH => 0));
	$s->execute();
	$row=$s->fetchAll(PDO::FETCH_ASSOC);
	/*while (($r = $s->fetch(PDO::FETCH_ASSOC)) != false) {
		$row[$r[$index]]=$r;
	}*/
	return $row;
}
//$row=pdo_select();
//print_r($row[35]);
?>
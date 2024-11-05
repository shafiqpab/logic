<?
include('common.php');
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$season=array();
$sql=sql_select("SELECT buyer_id , season from wo_quotation_inquery");
foreach($sql as $row){
	if($row[csf('season')]){
	$season[$row[csf('buyer_id')]][$row[csf('season')]]=$row[csf('season')];
	}
}
$sql=sql_select("SELECT buyer_id , season from wo_price_quotation");
foreach($sql as $row){
	if($row[csf('season')]){
	$season[$row[csf('buyer_id')]][$row[csf('season')]]=$row[csf('season')];
	}
}
$sql=sql_select("SELECT buyer_name , season from wo_po_details_master");
foreach($sql as $row){
	if($row[csf('season')]){
	$season[$row[csf('buyer_name')]][$row[csf('season')]]=$row[csf('season')];
	}
}
$sql=sql_select("SELECT buyer_name , season from sample_development_mst");
foreach($sql as $row){
	if($row[csf('season')]){
	$season[$row[csf('buyer_name')]][$row[csf('season')]]=$row[csf('season')];
	}
}

$sql=sql_select("SELECT buyer_name , season from sample_development_mst");
foreach($sql as $row){
	if($row[csf('season')]){
	$season[$row[csf('buyer_name')]][$row[csf('season')]]=$row[csf('season')];
	}
}

foreach($season as $buyerID=>$buyerArr){
	foreach($buyerArr as $season=>$value){
		echo $buyer_library[$buyerID]." = ".$season."</br>";
	}
}
//print_r($season);

?>

<?
	$datediff=30; 
	$today=date('Y-m-d'); //$today='2014-06-04';
	
	$firstDate = date("Y-m-d", strtotime("-29 day", strtotime($today)));
	
	$data=array();
	for($j=0;$j<$datediff;$j++)
	{
		$data[$j]=array(
				'Date'	=> $j."ss",
				'sessions'  => $j+10,
				'users'     => $j+20,
				'pageviews' => $j+25
		);
	} 
	
	echo json_encode( $data );
	
	
function add_date($orgDate,$days){
  $cd = strtotime($orgDate);
  $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$days,date('Y',$cd)));
  return $retDAY;
}

?>
<?
header('Content-type:text/html; charset=utf-8');
//session_start();
//if( $_SESSION['logic_crm']['user_id'] == "" ) header("location:login.php");

include('includes/common.php');
//$data=$_REQUEST['data'];
//$action=$_REQUEST['action'];

$con = connect();
function week_of_year($year,$week_start_day)
{
	$week_array=array();
	$week=0;
	for($i=1;$i<=12; $i++)
	{
		$month=str_pad($i, 2, '0', STR_PAD_LEFT);
		$year=$year;
		$first_date_of_year=$year."-01-01";
		$first_day_of_year=date('l', strtotime($first_date_of_year));
		if($i==1)
		{
			if(date('l', strtotime($first_day_of_year))==$week_start_day)
			{
				$week=0;
			}
			else
			{
				$week=1;
			}
		}
		$days_in_month = cal_days_in_month(0, $month, $year) ;
		
		foreach (range(1, $days_in_month) as $day) 
		{
			$test_date = $year."-".$month."-" . str_pad($day, 2, '0', STR_PAD_LEFT);
			global $db_type;
			if($db_type==2)
			{
				$test_date=change_date_format($test_date,'dd-mm-yyyy','-',1);
			}
			if(date('l', strtotime($test_date))==$week_start_day)
			{
				$week++;
			}
			$week_day=date('l', strtotime($test_date));
			$week_array[$test_date]=$week;
			//$con = connect();//the connection have to be called out of function
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "week_of_year", 1 );
			$field_array="id, year, month, week, week_start_day, week_date,week_day";
			$data_array="(".$id.",".$year.",".$month.",".$week.",'".$week_start_day."','".$test_date."','".$week_day."')";
			$rID=sql_insert("week_of_year",$field_array,$data_array,0);
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");  
				}
				else{
					mysql_query("ROLLBACK"); 
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID){
					oci_commit($con); 
				}
				else{
					oci_rollback($con); 
				}
			}
		}
	}
	return $week_array ;
}
//$weekarr=week_of_year('2025',"Monday");
//print_r($weekarr);
?>
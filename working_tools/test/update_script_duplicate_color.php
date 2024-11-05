<?
	include('../includes/common.php');
	//connect to ERPTEST
	//identified by "erptest"
	//using '203.202.252.62:1521/mkdldb'		
	//$con = oci_connect('ERPTEST', 'ERPTEST', '203.202.252.62/mkdldb');
	$con=connect();
	//echo $con;die;
	$tableNameArr=array();
	
	//echo count($tableNameArr)."<pre>";die;
	
	//$tableNameArr=array("YD_ORD_DTLS"=> "YD_COLOR_ID,ITEM_COLOR_ID");
	$duplicate_sql ="select color_name, count(id) as tot_row, listagg(cast(id as varchar(4000)),',') within group (order by id) as ids_all from LIB_COLOR 
	where status_active=1 and is_deleted=0 and color_name is not null
	group by color_name
	having count(id) > 1
	order by tot_row desc";
	//echo $duplicate_sql;die;
	$duplicate_sql_result=sql_select($duplicate_sql);
	//echo count($duplicate_sql_result);print_r($duplicate_sql_result);die;
	$rID=true;
	foreach($duplicate_sql_result as $row)
	{
		$ids_all_arr=explode(",",$row[csf("ids_all")]);
		$replace_id=array_shift($ids_all_arr);
		//echo $replace_id."<br>".count($ids_all_arr)."<br>";
		//print_r($ids_all_arr);die;
		$up_script="update LIB_COLOR set status_active=7, is_deleted=8 where id in(".implode(",",$ids_all_arr).") and id <> $replace_id ";
		//echo $up_script;die;
		$rID=execute_query($up_script);
		if($rID==false)
		{
			echo $up_script;oci_rollback($con);die;
		}
	}
	
	
	if($db_type==2)
	{
		if($rID)
		{
			oci_commit($con); 
			echo " Update Successful. <br>";die;
		}
		else
		{
			oci_rollback($con);
			echo " Update Failed";
			die;
		}
	}
?>
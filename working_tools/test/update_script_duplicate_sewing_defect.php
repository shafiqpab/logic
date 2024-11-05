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


	$duplicate_sql ="SELECT 
	MST_ID, PRODUCTION_TYPE, 
	PO_BREAK_DOWN_ID, DEFECT_TYPE_ID, DEFECT_POINT_ID, 
	DEFECT_QTY, STATUS_ACTIVE, 
	IS_DELETED, IS_LOCKED, BUNDLE_NO, 
	PROD_DATE, LINE_ID, BODYPART_ID, 
	COLOR_SIZE_BREAK_DOWN_ID, EMBEL_NAME, DTLS_ID,count(id) as tot_row,listagg(cast(id as varchar(4000)),',') within group (order by id) as ids_all
 FROM PRO_GMTS_PROD_DFT
 WHERE
 production_type=5 and status_active=1
 group by MST_ID, PRODUCTION_TYPE, 
	PO_BREAK_DOWN_ID, DEFECT_TYPE_ID, DEFECT_POINT_ID, 
	DEFECT_QTY, STATUS_ACTIVE, 
	IS_DELETED, IS_LOCKED, BUNDLE_NO, 
	PROD_DATE, LINE_ID, BODYPART_ID, 
	COLOR_SIZE_BREAK_DOWN_ID, EMBEL_NAME, DTLS_ID
 having count(id)>1 order by tot_row desc";

	//echo $duplicate_sql;die;
	$duplicate_sql_result=sql_select($duplicate_sql);
	//echo count($duplicate_sql_result);print_r($duplicate_sql_result);die;
	$rID=true;
	foreach($duplicate_sql_result as $row)
	{
		$ids_all_arr=explode(",",$row[csf("ids_all")]);
		$replace_id=array_shift(explode(",",$row[csf("ids_all")]));

		$up_script="update PRO_GMTS_PROD_DFT set status_active=0, is_deleted=1, updated_by=9999 where id in(".implode(",",$ids_all_arr).") and id != $replace_id; ";
		echo $up_script . "<br />"; //die;
		//$rID=execute_query($up_script);
		if($rID==false)
		{
			echo $up_script;oci_rollback($con);die;
		}
	}
	
	
	if($db_type==2)
	{
		if($rID)
		{
			//oci_commit($con); 
			echo " Update Successful. <br>";die;
		}
		else
		{
			//oci_rollback($con);
			echo " Update Failed";
			die;
		}
	}
?>
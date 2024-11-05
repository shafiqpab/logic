<?
	include('../../includes/common.php');
	//connect to ERPTEST
	//identified by "erptest"
	//using '203.202.252.62:1521/mkdldb'		
	//$con = oci_connect('ERPTEST', 'ERPTEST', '203.202.252.62/mkdldb');
	$con=connect();
	//echo $con;die;
	
	/*$sql_color_table="select owner, table_name, column_name
	from all_tab_columns
	where column_name like '%COLOR%'  and owner='URMILIVE'
	order by table_name";
	$sql_color_table_result=sql_select($sql_color_table);
	//echo "<pre>";print_r($sql_color_table_result);die;
	$tableNameArr=array();
	foreach($sql_color_table_result as $row)
	{
		$tableNameArr[$row[csf("table_name")]]=$row[csf("column_name")];
	}
	unset($sql_color_table_result);
	echo count($tableNameArr)."<pre>";print_r($tableNameArr);die;*/
	
	$tableNameArr=array();
/*	 
COM_EXPORT_INVOICE_SHIP_DTLS	COLOR_SIZE_RATE_DATA 
SAMPLE_DEVELOPMENT_FABRIC_ACC	COLOR_SIZE_BREAKDOWN
SAMPLE_DEVELOPMENT_FABRIC_ACC	COLOR_DATA
WO_BOOKING_DTLS	COLOR_ALL_DATA
WO_NON_ORD_SAMP_BOOKING_DTLS	COLOR_ALL_DATA
WO_PRE_COST_FAB_CONV_COST_DTLS	COLOR_BREAK_DOWN
WO_PRE_COST_FAB_CONV_COST_DTLS	COLOR_BREAK_DOWN_AOP
WO_PRE_COST_FABRIC_COST_DTLS	COLOR_BREAK_DOWN


DEBIT_NOTE_ENTRY_DTLS	COLOR_ID
DYEING_WORK_ORDER_DTLS	COLOR_ID
INV_GREY_FABRIC_ISSUE_DTLS	COLOR_ID
INV_ITEM_TRANSFER_DTLS	COLOR_NAMES
INV_ITEM_TRANSFER_REQU_DTLS	COLOR_NAMES
INV_ITEM_TRANSFER_STYLE_WISE	COLOR_NAMES
PPL_PLANNING_INFO_ENTRY_DTLS	COLOR_ID
PPL_SEWING_PLAN_BOARD	COLOR_NUMBER_ID_H
PPL_SEWING_PLAN_BOARD	COLOR_NUMBER_H
PPL_SEWING_PLAN_BOARD	COLOR_NUMBER_ID
PPL_SIZE_SET_CONSUMPTION	SAMPLE_COLOR_IDS
PPL_SIZE_SET_CONSUMPTION	STRIPE_COLOR_IDS
 
PRO_FAB_REQN_FOR_BATCH_DTLS	COLOR_ID
PRO_FAB_REQN_FOR_BATCH_WOVEN_DTLS	COLOR_ID
PRO_GMTS_KNITTING_ISSUE_DTLS	SAMPLE_COLOR_IDS
PRO_GREY_BATCH_DTLS	COLOR_ID
PRO_GREY_PROD_ENTRY_DTLS	COLOR_ID
SAMPLE_DEVELOPMENT_DTLS	SAMPLE_COLOR

SUBCON_PLANNING_DTLS	COLOR_ID
TRIMS_JOB_CARD_DTLS	MATERIAL_COLOR
 */
	/*$tableNameArr=array("DEBIT_NOTE_ENTRY_DTLS"=> "COLOR_ID","DYEING_WORK_ORDER_DTLS"=> "COLOR_ID","INV_GREY_FABRIC_ISSUE_DTLS"=> "COLOR_ID","INV_ITEM_TRANSFER_DTLS"=> "COLOR_NAMES","INV_ITEM_TRANSFER_REQU_DTLS"=> "COLOR_NAMES","INV_ITEM_TRANSFER_STYLE_WISE"=> "COLOR_NAMES","PPL_PLANNING_INFO_ENTRY_DTLS"=> "COLOR_ID", "PPL_SEWING_PLAN_BOARD"=> "COLOR_NUMBER_ID_H,COLOR_NUMBER_H,COLOR_NUMBER_ID","PPL_SIZE_SET_CONSUMPTION"=> "SAMPLE_COLOR_IDS,STRIPE_COLOR_IDS","PRO_FAB_REQN_FOR_BATCH_DTLS"=> "COLOR_ID","PRO_FAB_REQN_FOR_BATCH_WOVEN_DTLS"=> "COLOR_ID","PRO_GMTS_KNITTING_ISSUE_DTLS"=> "SAMPLE_COLOR_IDS","PRO_GREY_BATCH_DTLS"=> "COLOR_ID","PRO_GREY_PROD_ENTRY_DTLS"=> "COLOR_ID","SAMPLE_DEVELOPMENT_DTLS"=> "SAMPLE_COLOR","SUBCON_PLANNING_DTLS"=> "COLOR_ID","TRIMS_JOB_CARD_DTLS"=> "MATERIAL_COLOR");
 */
	
 
	$tableNameArr=array("DYEING_WORK_ORDER_DTLS"=> "COLOR_ID_BK");//,YARN_COLOR
	
 
	
	//$tableNameArr=array("YD_ORD_DTLS"=> "YD_COLOR_ID,ITEM_COLOR_ID");
	/*$col_sql="select a.id, a.FABRIC_COLOR_ID  from WO_BOOKING_DTLS a, LIB_COLOR b where a.FABRIC_COLOR_ID=b.id and b.status_active <> 1 and a.status_active=1 and a.is_deleted=0
	order by a.id desc";
	$col_sql_result=sql_select($col_sql);
	$color_arr=array();
	foreach($col_sql_result as $row) // and entry_form=90
	{
	}*/
	$colorid="7,7669,7670,7678,7680,7684,7694,7695,7705,7709,7799,7800,7820,7830,8078,8119,8128,8306,8307,8651,8652,8653,9714,9718,9734,9740,9767,9824,9828,9834,9853,12686,14225,15,7659,7660,7691,7710,7711,7728,7743,7779,7780,7781,7782,7801,7804,7805,7855,7876,7877,7878,8246,8304,11666,13074,13993";
	$duplicate_sql ="select color_name, count(id) as tot_row, listagg(cast(id as varchar(4000)),',') within group (order by id) as ids_all 
	from LIB_COLOR where status_active in(1,7) and is_deleted in(0,8) and color_name is not null and id in($colorid)
	group by color_name
	having count(id) > 1
	order by tot_row desc";
	//echo $duplicate_sql;die;
	// $duplicate_sql ="select color_name, count(id) as tot_row,rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') as ids_all
	// from LIB_COLOR where status_active in(1,7) and is_deleted in(0,8) (and color_name is not null  or color_name='0')
	// group by color_name
	// having count(id) > 1
	// order by tot_row desc";
	$duplicate_sql_result=sql_select($duplicate_sql);
	//echo count($duplicate_sql_result);print_r($duplicate_sql_result);die;
	$rID=true;$tt=1;
	foreach($duplicate_sql_result as $row)
	{
		$ids_all_arr=explode(",",$row[csf("ids_all")]);
		//$ids_all= $row[csf("ids_all")]->load();
		$replace_id=array_shift($ids_all_arr);
		//echo $replace_id."<br>".count($ids_all_arr)."<br>";
		//print_r($ids_all_arr);die;
		//$ids_all_arr_cond=where_con_using_array($ids_all_arr,0,$tbl_col);
		foreach($tableNameArr as $tbl_name=>$tbl_colmn)
		{
			$up_script="";
			// $tbl_colmn_ref=explode(",",$tbl_colmn);
			// $up_script=" update $tbl_name set";
			// foreach($tbl_colmn_ref as $tbl_col)
			// {
			// 	$up_script.=" $tbl_col=$replace_id,";
			// }
			// $up_script=chop($up_script,",");
			// $up_script.=" where 1=1  and (";
			// foreach($tbl_colmn_ref as $tbl_col)
			// {
			// 	$up_script.="  $tbl_col in(".implode(",",$ids_all_arr).") or";
			// }
			// $up_script=chop($up_script,"or");
			// $up_script.=" )";
			echo $replace_id.'='.$tbl_name.'='.$tbl_colmn.'<br>';
			$tt++;
		}
		 //echo $up_script;die;
		//$rID=execute_query($up_script);
		if($rID==false)
		{
			//echo $up_script;oci_rollback($con);die;
		}
	}
	//die;
	
	if($db_type==2)
	{
		if($rID)
		{
			oci_commit($con); 
			echo $tbl_name.',Tot='.$tt." Update Successful. <br>";die;
		}
		else
		{
			oci_rollback($con);
			echo " Update Failed";
			die;
		}
	}
?>
<?
	include('../includes/common.php');
	//connect to ERPTEST
	//identified by "erptest"
	//using '203.202.252.62:1521/mkdldb'		
	//$con = oci_connect('ERPTEST', 'ERPTEST', '203.202.252.62/mkdldb');
	$con=connect();
	//echo $con;die;
// 	select col.owner as schema_name,
// 	col.table_name,
// 	column_name,
// 	data_type,
// 	data_precision,
// 	data_scale
// from sys.all_tab_cols col
// join sys.all_tables tab on col.owner = tab.owner
// 					 and col.table_name = tab.table_name
// where column_name like '%SIZE%' and col.owner='PLATFORMERPV3' and data_type in ('NUMBER')
	
// order by col.owner,
// 	  col.table_name,
// 	  column_id;
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
	
	/*$tableNameArr=array("BARCODE_ISSUE_TO_FINISHING_DTLS"=>"SIZE_ID","BARCODE_RECEIVE_DTLS"=>"SIZE_ID","BH_WO_PO_COLOR_SIZE_BREAKDOWN"=>"SIZE_NUMBER_ID","BOND_PRODUCT_DETAILS_MASTER"=>"SIZE_ID", "BUNDLE_DATA_RECV_ISSUE_FOR_CUT"=>"SIZE_ID","COM_EXPORT_LC_ORDER_INFO"=>"SIZE_ID", "COM_EXPORT_PI_DTLS"=>"ITEM_SIZE", "COM_IMPORT_INVOICE_MST"=>"CONTAINER_SIZE", "COM_PI_ITEM_DETAILS"=>"SIZE_ID" "FINISH_BARCODE"=>"SIZE_ID", "GMT_FINISHING_RECEIVE_DTLS"=>"SIZE_ID", "INV_TRIMS_ENTRY_DTLS"=>"GMTS_SIZE_ID", "INV_TRIMS_ISSUE_DTLS"=>"GMTS_SIZE_ID", "PIECE_RATE_WO_QTY_DTLS"=>"SIZE_ID", "PPL_CONT_CUT_LAY_BUNDLE"=>"SIZE_ID", "PPL_CONT_CUT_LAY_ROLL_DTLS"=>"SIZE_ID",  "PPL_CONT_CUT_LAY_SIZE"=>"SIZE_ID,MANUAL_SIZE_NAME", "PPL_CONT_CUT_LAY_SIZE_DTLS"=>"SIZE_ID","PPL_CUT_LAY_BUNDLE"=>"SIZE_ID", "PPL_CUT_LAY_ROLL_DTLS"=>"SIZE_ID", "PPL_CUT_LAY_SIZE"=>"SIZE_ID", "PPL_CUT_LAY_SIZE"=>"MANUAL_SIZE_NAME", "PPL_CUT_LAY_SIZE_DTLS"=>"SIZE_ID", "PPL_GSD_ENTRY_DTLS"=>"NEEDLE_SIZE", "PPL_SEWING_PLAN_BOARD_PO_ARC"=>"SIZE_NUMBER_ID", "PPL_SEWING_PLAN_BOARD_POWISE"=>"SIZE_NUMBER_ID", "PPL_SIZE_SET_DTLS"=>"GMT_SIZE_ID" "PPL_SIZE_WISE_BREAK_DOWN"=>"GREY_SIZE_ID",  "PRNTING_BARCODE"=>"SIZE_ID", "PRO_BUNDLE_BATCH_DTLS"=>"SIZEID","PRO_BUYER_INSPECTION_BREAKDOWN"=>"SIZE_ID", "PRO_FAB_REQN_FOR_CUTTING_DTLS"=>"SIZE_ID", "PRO_GARMENTS_PROD_DTLS_PIECE"=>"SIZE_ID", "PRO_GARMENTS_PRODUCTION_DTLS"=>"SIZE_ID", "PRO_GMTS_CUTTING_QC_DTLS"=>"SIZE_ID", "PRO_GMTS_OPERATION_TRACKING"=>"SIZE_ID","PRO_LINKING_OPERATION_DTLS"=>"SIZE_ID", "PRO_OPERATION_TRACK_DTLS"=>"SIZE_ID", "PROD_RESOURCE_COLOR_SIZE"=>"SIZE_ID", "PRODUCT_DETAILS_MASTER"=>"GMTS_SIZE", "QC_MST"=>"SAMPLE_SIZE", "QC_MST_HISTORY"=>"SAMPLE_SIZE", "READY_TO_SEWING_DTLS"=>"SIZE_ID", "READY_TO_SEWING_REQSN"=>"SIZE_ID", "REJECT_DELIVERY_CHALLAN_TO_RECOVERY_DTLS"=>"SIZE_ID", "SAMPLE_DEVELOP_EMBL_COLOR_SIZE"=>"SIZE_ID", "SAMPLE_DEVELOPMENT_DTLS"=>"SIZE_ID", "SAMPLE_DEVELOPMENT_SIZE"=>"SIZE_ID", "SAMPLE_EX_FACTORY_COLORSIZE"=>"SIZE_ID","SAMPLE_RECEIVE_DTLS"=>"SIZE_ID", "SAMPLE_REQUISITION_COLLER_CUFF"=>"SIZE_ID", "SAMPLE_SEWING_OUTPUT_COLORSIZE"=>"SIZE_ID","SUB_ISSUE_REQUISITION_DTLS"=>"SIZE_ID", "SUB_MATERIAL_DTLS"=>"SIZE_ID", "SUBCON_INBOUND_BILL_DTLS"=>"SIZE_ID", "SUBCON_ORD_BREAKDOWN"=>"SIZE_ID,GMTS_SIZE_ID", "SUBCON_ORD_DTLS"=>"GMTS_SIZE_ID", "SUBCON_ORD_DTLS_HISTORY"=>"GMTS_SIZE_ID", "TRIMS_BILL_DTLS"=>"SIZE_ID,GMTS_SIZE_ID", "TRIMS_DELIVERY_BREAKDOWN"=>"SIZE_ID", "TRIMS_DELIVERY_DTLS"=>"SIZE_ID,GMTS_SIZE_ID", "TRIMS_FINISH_PURCHASE_REQ_DTLS"=>"SIZE_ID", "TRIMS_ITEM_TRANSFER_DTLS"=>"SIZE_ID", "TRIMS_JOB_CARD_BREAKDOWN"=>"GMTS_SIZE_ID", "TRIMS_JOB_CARD_DTLS"=>"SIZE_ID", "TRIMS_JOB_CARD_DTLS"=>"GMTS_SIZE_ID", "TRIMS_PRODUCTION_DTLS"=>"SIZE_ID", "TRIMS_PRODUCTION_DTLS"=>"GMTS_SIZE_ID", "TRIMS_PRODUCTION_QC_BREAKDOWN"=>"SIZE_ID", "TRIMS_SUBCON_ORD_BREAKDOWN"=>"SIZE_ID", "WO_BOM_PROCESS"=>"SIZE_NUMBER_ID", "WO_BOOKING_COLAR_CULFF_DTLS"=>"SIZE_NUMBER_ID", "WO_BOOKING_DTLS"=>"GMTS_SIZE", "WO_EMB_BOOK_CON_DTLS"=>"GMTS_SIZES",  "WO_NON_ORD_EMBL_BOOK_CONS_DTLS"=>"GMTS_SIZES", "WO_NON_ORD_SAMP_BK_DTLS_HIS"=>"GMTS_SIZE", "WO_NON_ORD_SAMP_BOOKING_DTLS"=>"GMTS_SIZE", "WO_PO_ACC_PO_INFO_BK"=>"GMTS_SIZE_ID", "WO_PO_ACC_PO_INFO_DTLS"=>"GMTS_SIZE_ID", "WO_PO_ACT_PACK_FINISH_INFO"=>"GMTS_SIZE_ID", "WO_PO_COLOR_SIZE_BREAKDOWN"=>"SIZE_NUMBER_ID,SIZE_NUMBER_ID_PREV", "WO_PO_COLOR_SIZE_HIS"=>"SIZE_NUMBER_ID", "WO_PO_RATIO_BREAKDOWN"=>"SIZE_ID", "WO_PRE_COS_EMB_CO_AVG_CON_DTLS"=>"SIZE_NUMBER_ID", "WO_PRE_COS_FAB_CO_AVG_CON_DTLS"=>"GMTS_SIZES","WO_PRE_COST_TRIM_CO_CONS_DTLS"=>"SIZE_NUMBER_ID", "WO_PRE_STRIPE_COLOR"=>"SIZE_NUMBER_ID", "WO_TRIM_BOOK_CON_DTLS"=>"GMTS_SIZES");
	 */
	
	
	$tableNameArr=array("BARCODE_ISSUE_TO_FINISHING_DTLS"=> "SIZE_ID"); 
	
	//echo count($tableNameArr)."<pre>";die;
	 
	 $duplicate_sql ="select size_name, count(id) as tot_row, listagg(cast(id as varchar(4000)),',') within group (order by id) as ids_all 
	from LIB_SIZE where status_active in(1,7) and is_deleted in(0,8) and size_name is not null 
	group by size_name
	having count(id) > 1
	order by tot_row desc";
	 echo $duplicate_sql;die; 
	// $duplicate_sql ="select size_name, count(id) as tot_row,rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') as ids_all
	// from LIB_COLOR where status_active in(1,7) and is_deleted in(0,8) (and size_name is not null  or color_name='0')
	// group by size_name
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
			$tbl_colmn_ref=explode(",",$tbl_colmn);
			$up_script=" update $tbl_name set";
			foreach($tbl_colmn_ref as $tbl_col)
			{
				$up_script.=" $tbl_col=$replace_id,";
			}
			$up_script=chop($up_script,",");
			$up_script.=" where 1=1  and (";
			foreach($tbl_colmn_ref as $tbl_col)
			{
				$up_script.="  $tbl_col in(".implode(",",$ids_all_arr).") or";
			}
			$up_script=chop($up_script,"or");
			$up_script.=" )";
			$tt++;
		}
		//echo $up_script;die;
		$rID=execute_query($up_script);
		if($rID==false)
		{
			echo $up_script;oci_rollback($con);die;
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
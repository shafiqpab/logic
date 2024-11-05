<?
	include('../includes/common.php');
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
	
	/*$tableNameArr=array("BARCODE_ISSUE_TO_FINISHING_DTLS"=> "COLOR_ID","BARCODE_RECEIVE_DTLS"=> "COLOR_ID","COM_EXPORT_LC_ORDER_INFO"=> "COLOR_ID,AOP_COLOR","COM_EXPORT_PI_DTLS"=> "COLOR_ID,AOP_COLOR_ID","COM_PI_ITEM_DETAILS"=> "COLOR_ID,ITEM_COLOR","COM_YARN_BAG_STICKER_BARCODE"=> "COLOR_ID","COM_YARN_BAG_STICKER_DTLS"=> "COLOR_ID", "COMMERCIAL_OFFICE_NOTE_DTLS"=> "COLOR_ID","DYES_CHEM_ISSUE_REQU_DTLS"=> "MULTICOLOR_ID","FABRIC_SALES_ORDER_DTLS"=> "COLOR_ID","FABRIC_SALES_ORDER_YARN_DTLS"=> "COLOR_ID","FINISH_BARCODE"=> "COLOR_ID","INV_GREY_FAB_SERVICE_MST_DTLS"=> "COLOR_ID","INV_ISSUE_MASTER"=> "ITEM_COLOR","INV_ITEM_TRANSFER_DTLS"=> "COLOR_ID","INV_ITEM_TRANSFER_DTLS_AC"=> "COLOR_ID","INV_ITEM_TRANSFER_REQU_DTLS"=> "COLOR_ID","INV_MATERIAL_ALLOCATION_DTLS"=> "COLOR_ID","INV_MATERIAL_ALLOCATION_DTLS_A"=> "COLOR_ID","INV_MATERIAL_ALLOCATION_MST"=> "COLOR_ID","INV_MATERIAL_ALLOCATION_MST_A"=> "COLOR_ID","INV_PURCHASE_REQUISITION_DTLS"=> "COLOR_ID","INV_SCRAP_RECEIVE_DTLS"=> "COLOR","INV_TRANSACTION"=> "DYEING_COLOR_ID","INV_TRIMS_ENTRY_DTLS"=> "ITEM_COLOR,GMTS_COLOR_ID,ITEM_COLOR_TEMP","INV_TRIMS_ISSUE_DTLS"=> "GMTS_COLOR_ID,ITEM_COLOR_ID","INV_YARN_BAG_RECEIVE_BARCODE"=> "COLOR_ID","INV_YARN_TEST_MST"=> "COLOR","LAB_COLOR_REFERENCE"=> "COLOR_ID","LAB_LABDIP_REQUEST"=> "COLOR_ID","LIB_COLOR"=> "GREY_COLOR","LIB_COLOR_TAG_BUYER"=> "COLOR_ID","LIB_SUBCON_CHARGE"=> "COLOR_ID","ORDER_WISE_PRO_DETAILS"=> "COLOR_ID","PIECE_RATE_WO_QTY_DTLS"=> "COLOR_ID","PPL_COLOR_WISE_BREAK_DOWN"=> "COLOR_ID","PPL_CUT_LAY_DTLS"=> "COLOR_ID","PPL_CUT_LAY_PROD_DTLS"=> "COLOR_ID","PPL_CUT_LAY_SIZE"=> "COLOR_ID","PPL_CUT_LAY_SIZE_DTLS"=> "COLOR_ID","PPL_PLANNING_ENTRY_PLAN_DTLS"=> "COLOR_ID","PPL_PLANNING_FEEDER_DTLS"=> "COLOR_ID,STRIPE_COLOR_ID","PPL_SEWING_PLAN_BOARD_PO_ARC"=> "COLOR_NUMBER_ID","PPL_SEWING_PLAN_BOARD_POWISE"=> "COLOR_NUMBER_ID","PPL_SIZE_SET_CONSUMPTION"=> "YARN_COLOR_ID,SAMPLE_COLOR_ID,COLOR_ID","PPL_SIZE_SET_DTLS"=> "COLOR_ID","PPL_COLOR_WISE_BREAK_DOWN"=>"COLOR_ID","PRO_BATCH_CREATE_MST"=> "COLOR_ID","PRO_BATCH_PLAN"=> "COLOR_ID","PRO_BUNDLE_BATCH_DTLS"=> "COLORID","PRO_BUNDLE_BATCH_MST"=> "COLOR_ID","PRO_BUYER_INSPECTION_BREAKDOWN"=> "COLOR_ID","PRO_DYEING_UPDATE_DTLS"=> "COLOR_ID","PRO_FAB_REQN_FOR_CUTTING_DTLS"=> "COLOR_ID","PRO_FIN_DELI_MULTY_CHALLA_DTLS"=> "COLOR_ID","PRO_FINISH_FABRIC_RCV_DTLS"=> "COLOR_ID","PRO_GMTS_CUTTING_QC_DTLS"=> "COLOR_ID","PRO_GMTS_KNITTING_ISSUE_DTLS"=> "SAMPLE_COLOR,GMTS_COLOR,YARN_COLOR","PRO_GREY_PROD_DELIVERY_DTLS"=> "COLOR_ID","PRO_LINKING_OPERATION_DTLS"=> "COLOR_ID","PRO_OPERATION_TRACK_DTLS"=> "COLOR_ID","PRO_RECIPE_ENTRY_DTLS"=> "COLOR_ID","PRO_RECIPE_ENTRY_MST"=> "COLOR_ID","PROD_RESOURCE_COLOR_SIZE"=> "COLOR_ID","PRODUCT_DETAILS_MASTER"=> "ITEM_COLOR,COLOR","QC_FINAL_INSPECTION_MST"=> "COLOR_ID","READY_TO_SEWING_DTLS"=> "COLOR_ID","READY_TO_SEWING_REQSN"=> "COLOR_ID","SAMPLE_DEVELOP_EMBL_COLOR_SIZE"=> "COLOR_ID","SAMPLE_DEVELOPMENT_DTLS"=> "sample_color,COLOR_ID","SAMPLE_DEVELOPMENT_FABRIC_ACC"=> "SAMPLE_COLOR","SAMPLE_DEVELOPMENT_RF_COLOR"=> "FABRIC_COLOR,BODYCOLOR,COLOR_ID","SAMPLE_EX_FACTORY_COLORSIZE"=> "COLOR_ID","SAMPLE_RECEIVE_DTLS"=> "COLOR_ID","SAMPLE_SEWING_OUTPUT_COLORSIZE"=> "COLOR_ID","SUB_MATERIAL_DTLS"=> "COLOR_ID","SUBCON_DELIVERY_DTLS"=> "COLOR_ID","SUBCON_EMBEL_PRODUCTION_DTLS"=> "COLOR_ID","subcon_ord_color_breakdown"=>"COLOR_ID","trims_subcon_ord_breakdown"=>"COLOR_ID","SUBCON_INBOUND_BILL_DTLS"=> "COLOR_ID","SUBCON_ORD_BREAKDOWN"=> "COLOR_ID","SUBCON_ORD_DTLS"=> "AOP_COLOR_ID,GMTS_COLOR_ID,ITEM_COLOR_ID","SUBCON_OUTBOUND_BILL_DTLS"=> "COLOR_ID","SUBCON_PLANNING_FEEDER_DTLS"=> "STRIPE_COLOR_ID,COLOR_ID","SUBCON_PLANNING_PLAN_DTLS"=> "COLOR_ID","SUBCON_PRODUCTION_DTLS"=> "COLOR_ID","TRIMS_BILL_DTLS"=> "COLOR_ID","TRIMS_DELIVERY_BREAKDOWN"=> "COLOR_ID","TRIMS_DELIVERY_DTLS"=> "COLOR_ID","TRIMS_JOB_CARD_DTLS"=> "COLOR_ID","TRIMS_PRODUCTION_DTLS"=> "COLOR_ID,MATERIAL_COLOR_ID","WO_BOOKING_COLAR_CULFF_DTLS"=> "GMTS_COLOR_ID","WO_BOOKING_DTLS"=> "PRINTING_COLOR_ID,FABRIC_COLOR_ID,GMTS_COLOR_ID","WO_DYE_TO_MATCH"=> "FABRIC_COLOR,ITEM_COLOR","WO_EMB_BOOK_CON_DTLS"=> "COLOR_NUMBER_ID,ITEM_COLOR","WO_LABTEST_DTLS"=> "COLOR","WO_NON_ORD_AOP_BOOKING_DTLS"=> "PRINTING_COLOR_ID","WO_NON_ORD_EMBL_BOOK_CONS_DTLS"=> "ITEM_COLOR,COLOR_NUMBER_ID","WO_NON_ORD_KNITDYE_BOOKING_DTL"=> "GMTS_COLOR","WO_PRE_COS_CONV_COLOR_DTLS"=>"GMTS_COLOR_ID,FABRIC_COLOR_ID",WO_NON_ORD_SAMP_BOOKING_DTLS"=> "FABRIC_COLOR,GMTS_COLOR","WO_NON_ORDER_INFO_DTLS"=> "COLOR_NAME","WO_PO_COLOR_SIZE_BREAKDOWN"=> "COLOR_NUMBER_ID","WO_PO_DESTINATION_INFO"=> "COLOR_ID","WO_PO_EMBELL_APPROVAL"=> "COLOR_NAME_ID","WO_PO_LAPDIP_APPROVAL_INFO"=> "COLOR_NAME_ID","WO_PO_RATIO_BREAKDOWN"=> "COLOR_ID","WO_PO_ACC_PO_INFO_DTLS"=>"GMTS_COLOR_ID","WO_PO_ACT_PACK_FINISH_INFO"=>"GMTS_COLOR_ID",WO_PRE_COS_EMB_CO_AVG_CON_DTLS"=> "COLOR_NUMBER_ID","WO_PRE_COS_FAB_CO_AVG_CON_DTLS"=> "COLOR_NUMBER_ID","WO_PRE_COS_FAB_CO_COLOR_DTLS"=> "GMTS_COLOR_ID,CONTRAST_COLOR_ID","WO_PRE_COST_FAB_YARN_COST_DTLS"=> "COLOR","WO_PRE_COST_FAB_YARNBREAKDOWN"=> "COLOR","WO_PRE_COST_FABRIC_COST_DTLS"=> "COLOR","WO_PRE_COST_TRIM_CO_CONS_DTLS"=> "ITEM_COLOR_NUMBER_ID,COLOR_NUMBER_ID","WO_SAMPLE_STRIPE_COLOR"=> "COLOR_NUMBER_ID,STRIPE_COLOR","WO_PRE_STRIPE_COLOR"=> "COLOR_NUMBER_ID,SAMPLE_COLOR,STRIPE_COLOR","WO_QUOTATION_INQUERY"=> "COLOR_ID","WO_SAMPLE_STRIPE_COLOR"=> "COLOR_NUMBER_ID,STRIPE_COLOR","WO_SHORT_STRIPE_COLOR"=> "GMTS_COLOR_ID,STRIPE_COLOR,FABRIC_COLOR_ID","WO_TRIM_BOOK_CON_DTLS"=> "ITEM_COLOR,BOM_ITEM_COLOR,BOOKING_ITEM_COLOR,COLOR_NUMBER_ID","WO_YARN_DYEING_DTLS"=> "COLOR,YARN_COLOR","WO_YARN_DYEING_DTLS_FIN_PROD"=> "YARN_COLOR","YD_BATCH_MST"=> "BATCH_COLOR_ID","YD_MATERIAL_DTLS"=> "COLOR_ID","YD_ORD_DTLS"=> "YD_COLOR_ID,ITEM_COLOR_ID");
	//WO_PO_ACC_PO_INFO_DTLS - GMTS_COLOR_ID,WO_PO_ACT_PACK_FINISH_INFO - GMTS_COLOR_ID,WO_PRE_COST_BOMOFFABRIC - COLOR,YD_ORD_DTLS - ITEM_COLOR_ID,
YD_ORD_DTLS - YD_COLOR_ID,
YD_RECIPE_ENTRY_DTLS - COLOR_ID,
YD_RECIPE_ENTRY_MST - COLOR_ID*/
	
 
		
	
	//$tableNameArr=array("PRO_GMTS_CUTTING_QC_DTLS"=> "COLOR_ID");
	/*$tableNameArr=array(,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,);*/
	
	
	
	//,,,,
	$tableNameArr=array("FABRIC_SALES_ORDER_DTLS"=> "COLOR_ID");//,YARN_COLOR
	
	//echo count($tableNameArr)."<pre>";die;
	
	//$tableNameArr=array("YD_ORD_DTLS"=> "YD_COLOR_ID,ITEM_COLOR_ID");
	/*$col_sql="select a.id, a.FABRIC_COLOR_ID  from WO_BOOKING_DTLS a, LIB_COLOR b where a.FABRIC_COLOR_ID=b.id and b.status_active <> 1 and a.status_active=1 and a.is_deleted=0
	order by a.id desc";
	$col_sql_result=sql_select($col_sql);
	$color_arr=array();
	foreach($col_sql_result as $row) // and entry_form=90
	{
	}*/
	/*$duplicate_sql ="select color_name, count(id) as tot_row, listagg(cast(id as varchar(4000)),',') within group (order by id) as ids_all 
	from LIB_COLOR where status_active in(1,7) and is_deleted in(0,8) and color_name is not null 
	group by color_name
	having count(id) > 1
	order by tot_row desc";
	echo $duplicate_sql;die;*/
	$duplicate_sql ="select color_name, count(id) as tot_row,rtrim(xmlagg(xmlelement(e,id,',').extract('//text()') order by id).GetClobVal(),',') as ids_all
	from LIB_COLOR where status_active in(1,7) and is_deleted in(0,8) (and color_name is not null  or color_name='0')
	group by color_name
	having count(id) > 1
	order by tot_row desc";
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
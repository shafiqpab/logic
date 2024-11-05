<?
$new_con = oci_connect('ERPDB', 'ERPDB', '//175.29.168.60:1521/orcl.localdomain');
function sql_select2($strQuery, $is_single_row, $new_conn, $un_buffered, $connection)
{
	if ( $new_conn!="" )
	{
		$new_conn=explode("*",$new_conn);
		$con_select = oci_connect($new_conn[1], $new_conn[2], $new_conn[0]);
	}
	else
	{
		if($connection==""){
			$con_select = connect();
		}else{
			$con_select = $connection;
		}
	}
	//echo  $strQuery;die;
	$result = oci_parse($con_select, $strQuery);
	oci_execute($result);
	$rows = array();
	 while($summ=oci_fetch_assoc($result))
	 {
		if($is_single_row==1)
		{
			$rows[] = $summ;
			if($connection=="") disconnect($con_select);
			return $rows;

			die;
		}
		else
		{
		$rows[] = $summ;
		}
	 }
	if($connection=="")  disconnect($con_select);
	return $rows;
	 //echo $row['mychars']->load(); for clob data type, mychars is clob
	die;
}

//$con = oci_connect('ERPDB', 'ERPDB', '//175.29.168.60:1521/orcl');
//echo $new_con.test;die;
////59.152.60.147:15252/ORCL*LOGIC3RDVERSION*LOGIC3RDVERSION*LOGIC3RDVERSION**2
$con_string="//175.29.168.60:1521/orcl.localdomain*ERPDB*ERPDB";

$sql_prod="SELECT ID, COMPANY_ID, SUPPLIER_ID, STORE_ID, ITEM_CATEGORY_ID, DETARMINATION_ID, SUB_GROUP_CODE, SUB_GROUP_NAME, ITEM_GROUP_ID, ITEM_DESCRIPTION, PRODUCT_NAME_DETAILS, LOT, ITEM_CODE, UNIT_OF_MEASURE, RE_ORDER_LABEL, MINIMUM_LABEL, MAXIMUM_LABEL, ITEM_ACCOUNT, PACKING_TYPE, AVG_RATE_PER_UNIT, LAST_PURCHASED_QNTY, CURRENT_STOCK, LAST_ISSUED_QNTY, STOCK_VALUE, YARN_COUNT_ID, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_TYPE, COLOR, ITEM_COLOR, GMTS_SIZE, GSM, BRAND, BRAND_SUPPLIER, DIA_WIDTH, ITEM_SIZE, WEIGHT, ALLOCATED_QNTY, AVAILABLE_QNTY, INSERTED_BY, INSERT_DATE, UPDATED_BY, UPDATE_DATE, STATUS_ACTIVE, IS_DELETED, ENTRY_FORM, ITEM_RETURN_QTY, ORIGIN, MODEL, CAPACITY, OTHERINFO, BRAND_NAME, TEST_AVAILABLE, DYED_TYPE, IS_BUYER_SUPPLIED, IS_GMTS_PRODUCT, IS_TWISTED, FIXED_ASSET, ORDER_UOM, CONVERSION_FACTOR, BOND_STATUS, IS_SUPP_COMP, SECTION_ID, ITEM_NUMBER, IS_POSTED_SQL, CUMULATIVE_BALANCE FROM PRODUCT_DETAILS_MASTER WHERE item_category_id in(59,65,70,90,94,99,37,4,8,9,10,15,16,17,18,19,21,22,32,33,34,35,36,38,39,41,43,45,46,47,48,49,50,51,52,53,54,55,56,57,58,60,61,62,63,64,66,67,68,69,89,91,92,93,11,20,40,44)
and entry_form <> 24
and status_active=1";
$sql_prod_result=sql_select2($sql_prod,"","","",$new_con);
//echo $sql_prod_result;die;
//echo count($sql_prod_result);die;
//disconnect($new_con);
include('../includes/common.php');
$con = connect();
//echo $con.check;die;
$upTransID=true;
foreach($sql_prod_result as $row)
{
	//$row["SUPPLIER_ID"]."','".$row["STORE_ID"]."','".$row["ITEM_CATEGORY_ID"]."','".$row["DETARMINATION_ID"]."','".$row["SUB_GROUP_CODE"]."','".$row["SUB_GROUP_NAME"]."','".$row["ITEM_GROUP_ID"]."','".$row["ITEM_DESCRIPTION"]."','".$row["PRODUCT_NAME_DETAILS"]."','".$row["LOT"]."','".$row["ITEM_CODE"]."','".$row["UNIT_OF_MEASURE"]."','".$row["RE_ORDER_LABEL"]."','".$row["MINIMUM_LABEL"]."','".$row["MAXIMUM_LABEL"]."','".$row["ITEM_ACCOUNT"]."','".$row["PACKING_TYPE"]."','".$row["AVG_RATE_PER_UNIT"]."','".$row["LAST_PURCHASED_QNTY"]."','".$row["CURRENT_STOCK"]."','".$row["LAST_ISSUED_QNTY"]."','".$row["STOCK_VALUE"]."','".$row["YARN_COUNT_ID"]."','".$row["YARN_COMP_TYPE1ST"]."','".$row["YARN_COMP_PERCENT1ST"]."','".$row["YARN_COMP_TYPE2ND"]."','".$row["YARN_COMP_PERCENT2ND"]."','".$row["YARN_TYPE"]."','".$row["COLOR"]."','".$row["ITEM_COLOR"]."','".$row["GMTS_SIZE"]."','".$row["GSM"]."','".$row["BRAND"]."','".$row["BRAND_SUPPLIER"]."','".$row["DIA_WIDTH"]."','".$row["ITEM_SIZE"]."','".$row["WEIGHT"]."','".$row["ALLOCATED_QNTY"]."','".$row["AVAILABLE_QNTY"]."','".$row["INSERTED_BY"]."','".$row["INSERT_DATE"]."','".$row["UPDATED_BY"]."','".$row["UPDATE_DATE"]."','".$row["STATUS_ACTIVE"]."','".$row["IS_DELETED"]."','".$row["ENTRY_FORM"]."','".$row["ITEM_RETURN_QTY"]."','".$row["ORIGIN"]."','".$row["MODEL"]."','".$row["CAPACITY"]."','".$row["OTHERINFO"]."','".$row["BRAND_NAME"]."','".$row["TEST_AVAILABLE"]."','".$row["DYED_TYPE"]."','".$row["IS_BUYER_SUPPLIED"]."','".$row["IS_GMTS_PRODUCT"]."','".$row["IS_TWISTED"]."','".$row["FIXED_ASSET"]."','".$row["ORDER_UOM"]."','".$row["CONVERSION_FACTOR"]."','".$row["BOND_STATUS"]."','".$row["IS_SUPP_COMP"]."','".$row["SECTION_ID"]."','".$row["ITEM_NUMBER"]."','".$row["IS_POSTED_SQL"]."','".$row["CUMULATIVE_BALANCE"]
	$id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
	$upTransID=execute_query("insert into inv_store_wise_qty_dtls (ID, COMPANY_ID, SUPPLIER_ID, STORE_ID, ITEM_CATEGORY_ID, DETARMINATION_ID, SUB_GROUP_CODE, SUB_GROUP_NAME, ITEM_GROUP_ID, ITEM_DESCRIPTION, PRODUCT_NAME_DETAILS, LOT, ITEM_CODE, UNIT_OF_MEASURE, RE_ORDER_LABEL, MINIMUM_LABEL, MAXIMUM_LABEL, ITEM_ACCOUNT, PACKING_TYPE, AVG_RATE_PER_UNIT, LAST_PURCHASED_QNTY, CURRENT_STOCK, LAST_ISSUED_QNTY, STOCK_VALUE, YARN_COUNT_ID, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_TYPE, COLOR, ITEM_COLOR, GMTS_SIZE, GSM, BRAND, BRAND_SUPPLIER, DIA_WIDTH, ITEM_SIZE, WEIGHT, ALLOCATED_QNTY, AVAILABLE_QNTY, INSERTED_BY, INSERT_DATE, UPDATED_BY, UPDATE_DATE, STATUS_ACTIVE, IS_DELETED, ENTRY_FORM, ITEM_RETURN_QTY, ORIGIN, MODEL, CAPACITY, OTHERINFO, BRAND_NAME, TEST_AVAILABLE, DYED_TYPE, IS_BUYER_SUPPLIED, IS_GMTS_PRODUCT, IS_TWISTED, FIXED_ASSET, ORDER_UOM, CONVERSION_FACTOR, BOND_STATUS, IS_SUPP_COMP, SECTION_ID, ITEM_NUMBER, IS_POSTED_SQL, CUMULATIVE_BALANCE) values (".$id.",'2','".$row["SUPPLIER_ID"]."','".$row["STORE_ID"]."','".$row["ITEM_CATEGORY_ID"]."','".$row["DETARMINATION_ID"]."','".$row["SUB_GROUP_CODE"]."','".$row["SUB_GROUP_NAME"]."','".$row["ITEM_GROUP_ID"]."','".$row["ITEM_DESCRIPTION"]."','".$row["PRODUCT_NAME_DETAILS"]."','".$row["LOT"]."','".$row["ITEM_CODE"]."','".$row["UNIT_OF_MEASURE"]."','".$row["RE_ORDER_LABEL"]."','".$row["MINIMUM_LABEL"]."','".$row["MAXIMUM_LABEL"]."','".$row["ITEM_ACCOUNT"]."','".$row["PACKING_TYPE"]."','0','0','0','0','0','".$row["YARN_COUNT_ID"]."','".$row["YARN_COMP_TYPE1ST"]."','".$row["YARN_COMP_PERCENT1ST"]."','".$row["YARN_COMP_TYPE2ND"]."','".$row["YARN_COMP_PERCENT2ND"]."','".$row["YARN_TYPE"]."','".$row["COLOR"]."','".$row["ITEM_COLOR"]."','".$row["GMTS_SIZE"]."','".$row["GSM"]."','".$row["BRAND"]."','".$row["BRAND_SUPPLIER"]."','".$row["DIA_WIDTH"]."','".$row["ITEM_SIZE"]."','".$row["WEIGHT"]."','0','0','".$row["INSERTED_BY"]."','".$row["INSERT_DATE"]."','".$row["UPDATED_BY"]."','".$row["UPDATE_DATE"]."','".$row["STATUS_ACTIVE"]."','".$row["IS_DELETED"]."','".$row["ENTRY_FORM"]."','".$row["ITEM_RETURN_QTY"]."','".$row["ORIGIN"]."','".$row["MODEL"]."','".$row["CAPACITY"]."','".$row["OTHERINFO"]."','".$row["BRAND_NAME"]."','".$row["TEST_AVAILABLE"]."','".$row["DYED_TYPE"]."','".$row["IS_BUYER_SUPPLIED"]."','".$row["IS_GMTS_PRODUCT"]."','".$row["IS_TWISTED"]."','".$row["FIXED_ASSET"]."','".$row["ORDER_UOM"]."','".$row["CONVERSION_FACTOR"]."','".$row["BOND_STATUS"]."','".$row["IS_SUPP_COMP"]."','".$row["SECTION_ID"]."','".$row["ITEM_NUMBER"]."','".$row["IS_POSTED_SQL"]."','".$row["CUMULATIVE_BALANCE"]."' ) ");
	
	if($upTransID){ $upTransID=1; } else {echo "insert into inv_store_wise_qty_dtls (ID, COMPANY_ID, SUPPLIER_ID, STORE_ID, ITEM_CATEGORY_ID, DETARMINATION_ID, SUB_GROUP_CODE, SUB_GROUP_NAME, ITEM_GROUP_ID, ITEM_DESCRIPTION, PRODUCT_NAME_DETAILS, LOT, ITEM_CODE, UNIT_OF_MEASURE, RE_ORDER_LABEL, MINIMUM_LABEL, MAXIMUM_LABEL, ITEM_ACCOUNT, PACKING_TYPE, AVG_RATE_PER_UNIT, LAST_PURCHASED_QNTY, CURRENT_STOCK, LAST_ISSUED_QNTY, STOCK_VALUE, YARN_COUNT_ID, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_TYPE, COLOR, ITEM_COLOR, GMTS_SIZE, GSM, BRAND, BRAND_SUPPLIER, DIA_WIDTH, ITEM_SIZE, WEIGHT, ALLOCATED_QNTY, AVAILABLE_QNTY, INSERTED_BY, INSERT_DATE, UPDATED_BY, UPDATE_DATE, STATUS_ACTIVE, IS_DELETED, ENTRY_FORM, ITEM_RETURN_QTY, ORIGIN, MODEL, CAPACITY, OTHERINFO, BRAND_NAME, TEST_AVAILABLE, DYED_TYPE, IS_BUYER_SUPPLIED, IS_GMTS_PRODUCT, IS_TWISTED, FIXED_ASSET, ORDER_UOM, CONVERSION_FACTOR, BOND_STATUS, IS_SUPP_COMP, SECTION_ID, ITEM_NUMBER, IS_POSTED_SQL, CUMULATIVE_BALANCE) values (".$id.",'2','".$row["SUPPLIER_ID"]."','".$row["STORE_ID"]."','".$row["ITEM_CATEGORY_ID"]."','".$row["DETARMINATION_ID"]."','".$row["SUB_GROUP_CODE"]."','".$row["SUB_GROUP_NAME"]."','".$row["ITEM_GROUP_ID"]."','".$row["ITEM_DESCRIPTION"]."','".$row["PRODUCT_NAME_DETAILS"]."','".$row["LOT"]."','".$row["ITEM_CODE"]."','".$row["UNIT_OF_MEASURE"]."','".$row["RE_ORDER_LABEL"]."','".$row["MINIMUM_LABEL"]."','".$row["MAXIMUM_LABEL"]."','".$row["ITEM_ACCOUNT"]."','".$row["PACKING_TYPE"]."','0','0','0','0','0','".$row["YARN_COUNT_ID"]."','".$row["YARN_COMP_TYPE1ST"]."','".$row["YARN_COMP_PERCENT1ST"]."','".$row["YARN_COMP_TYPE2ND"]."','".$row["YARN_COMP_PERCENT2ND"]."','".$row["YARN_TYPE"]."','".$row["COLOR"]."','".$row["ITEM_COLOR"]."','".$row["GMTS_SIZE"]."','".$row["GSM"]."','".$row["BRAND"]."','".$row["BRAND_SUPPLIER"]."','".$row["DIA_WIDTH"]."','".$row["ITEM_SIZE"]."','".$row["WEIGHT"]."','0','0','".$row["INSERTED_BY"]."','".$row["INSERT_DATE"]."','".$row["UPDATED_BY"]."','".$row["UPDATE_DATE"]."','".$row["STATUS_ACTIVE"]."','".$row["IS_DELETED"]."','".$row["ENTRY_FORM"]."','".$row["ITEM_RETURN_QTY"]."','".$row["ORIGIN"]."','".$row["MODEL"]."','".$row["CAPACITY"]."','".$row["OTHERINFO"]."','".$row["BRAND_NAME"]."','".$row["TEST_AVAILABLE"]."','".$row["DYED_TYPE"]."','".$row["IS_BUYER_SUPPLIED"]."','".$row["IS_GMTS_PRODUCT"]."','".$row["IS_TWISTED"]."','".$row["FIXED_ASSET"]."','".$row["ORDER_UOM"]."','".$row["CONVERSION_FACTOR"]."','".$row["BOND_STATUS"]."','".$row["IS_SUPP_COMP"]."','".$row["SECTION_ID"]."','".$row["ITEM_NUMBER"]."','".$row["IS_POSTED_SQL"]."','".$row["CUMULATIVE_BALANCE"]."' ) ";oci_rollback($con);die;}
	$i++;
}
//echo "<pre>";print_r($rcv_data);die;
if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID)
	{
		oci_commit($con); 
		echo "Store Wise Data Insert Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Store Wise Data Insert Failed";
		die;
	}
}
?>
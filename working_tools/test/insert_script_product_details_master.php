<?
include('../includes/common.php');
$con = connect();
//echo $con;die;
$sql_prod="SELECT a.ID, a.COMPANY_ID, a.SUPPLIER_ID, a.STORE_ID, a.ITEM_CATEGORY_ID, a.DETARMINATION_ID, a.SUB_GROUP_CODE, a.SUB_GROUP_NAME, a.ITEM_GROUP_ID, a.ITEM_DESCRIPTION, a.PRODUCT_NAME_DETAILS, a.LOT, a.ITEM_CODE, a.UNIT_OF_MEASURE, a.RE_ORDER_LABEL, a.MINIMUM_LABEL, a.MAXIMUM_LABEL, a.ITEM_ACCOUNT, a.PACKING_TYPE, a.AVG_RATE_PER_UNIT, a.LAST_PURCHASED_QNTY, a.CURRENT_STOCK, a.LAST_ISSUED_QNTY, a.STOCK_VALUE, a.YARN_COUNT_ID, a.YARN_COMP_TYPE1ST, a.YARN_COMP_PERCENT1ST, a.YARN_COMP_TYPE2ND, a.YARN_COMP_PERCENT2ND, a.YARN_TYPE, a.COLOR, a.ITEM_COLOR, a.GMTS_SIZE, a.GSM, a.BRAND, a.BRAND_SUPPLIER, a.DIA_WIDTH, a.ITEM_SIZE, a.WEIGHT, a.ALLOCATED_QNTY, a.AVAILABLE_QNTY, a.INSERTED_BY, a.INSERT_DATE, a.UPDATED_BY, a.UPDATE_DATE, a.STATUS_ACTIVE, a.IS_DELETED, a.ENTRY_FORM, a.ITEM_RETURN_QTY, a.ORIGIN, a.MODEL, a.CAPACITY, a.OTHERINFO, a.BRAND_NAME, a.TEST_AVAILABLE, a.DYED_TYPE, a.IS_BUYER_SUPPLIED, a.IS_GMTS_PRODUCT, a.IS_TWISTED, a.FIXED_ASSET, a.ORDER_UOM, a.CONVERSION_FACTOR, a.BOND_STATUS, a.IS_SUPP_COMP, a.SECTION_ID, a.ITEM_NUMBER, a.IS_POSTED_SQL, a.CUMULATIVE_BALANCE 
from product_details_master a, lib_item_category_list b 
where a.item_category_id=b.CATEGORY_ID and b.CATEGORY_TYPE=1 and a.company_id=3 and a.entry_form<>24 and a.status_active=1 
order by a.item_category_id";
$sql_prod_result=sql_select($sql_prod);
$upTransID=true;
foreach($sql_prod_result as $row)
{
	$id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
	$upTransID=execute_query("insert into PRODUCT_DETAILS_MASTER (ID, COMPANY_ID, SUPPLIER_ID, STORE_ID, ITEM_CATEGORY_ID, DETARMINATION_ID, SUB_GROUP_CODE, SUB_GROUP_NAME, ITEM_GROUP_ID, ITEM_DESCRIPTION, PRODUCT_NAME_DETAILS, LOT, ITEM_CODE, UNIT_OF_MEASURE, RE_ORDER_LABEL, MINIMUM_LABEL, MAXIMUM_LABEL, ITEM_ACCOUNT, PACKING_TYPE, AVG_RATE_PER_UNIT, LAST_PURCHASED_QNTY, CURRENT_STOCK, LAST_ISSUED_QNTY, STOCK_VALUE, YARN_COUNT_ID, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_TYPE, COLOR, ITEM_COLOR, GMTS_SIZE, GSM, BRAND, BRAND_SUPPLIER, DIA_WIDTH, ITEM_SIZE, WEIGHT, ALLOCATED_QNTY, AVAILABLE_QNTY, INSERTED_BY, INSERT_DATE, UPDATED_BY, UPDATE_DATE, STATUS_ACTIVE, IS_DELETED, ENTRY_FORM, ITEM_RETURN_QTY, ORIGIN, MODEL, CAPACITY, OTHERINFO, BRAND_NAME, TEST_AVAILABLE, DYED_TYPE, IS_BUYER_SUPPLIED, IS_GMTS_PRODUCT, IS_TWISTED, FIXED_ASSET, ORDER_UOM, CONVERSION_FACTOR, BOND_STATUS, IS_SUPP_COMP, SECTION_ID, ITEM_NUMBER, IS_POSTED_SQL, CUMULATIVE_BALANCE) values (".$id.",'7','".$row["SUPPLIER_ID"]."','".$row["STORE_ID"]."','".$row["ITEM_CATEGORY_ID"]."','".$row["DETARMINATION_ID"]."','".$row["SUB_GROUP_CODE"]."','".$row["SUB_GROUP_NAME"]."','".$row["ITEM_GROUP_ID"]."','".$row["ITEM_DESCRIPTION"]."','".$row["PRODUCT_NAME_DETAILS"]."','".$row["LOT"]."','".$row["ITEM_CODE"]."','".$row["UNIT_OF_MEASURE"]."','".$row["RE_ORDER_LABEL"]."','".$row["MINIMUM_LABEL"]."','".$row["MAXIMUM_LABEL"]."','".$row["ITEM_ACCOUNT"]."','".$row["PACKING_TYPE"]."','0','0','0','0','0','".$row["YARN_COUNT_ID"]."','".$row["YARN_COMP_TYPE1ST"]."','".$row["YARN_COMP_PERCENT1ST"]."','".$row["YARN_COMP_TYPE2ND"]."','".$row["YARN_COMP_PERCENT2ND"]."','".$row["YARN_TYPE"]."','".$row["COLOR"]."','".$row["ITEM_COLOR"]."','".$row["GMTS_SIZE"]."','".$row["GSM"]."','".$row["BRAND"]."','".$row["BRAND_SUPPLIER"]."','".$row["DIA_WIDTH"]."','".$row["ITEM_SIZE"]."','".$row["WEIGHT"]."','0','0','".$row["INSERTED_BY"]."','".$row["INSERT_DATE"]."','".$row["UPDATED_BY"]."','".$row["UPDATE_DATE"]."','".$row["STATUS_ACTIVE"]."','".$row["IS_DELETED"]."','".$row["ENTRY_FORM"]."','".$row["ITEM_RETURN_QTY"]."','".$row["ORIGIN"]."','".$row["MODEL"]."','".$row["CAPACITY"]."','".$row["OTHERINFO"]."','".$row["BRAND_NAME"]."','".$row["TEST_AVAILABLE"]."','".$row["DYED_TYPE"]."','".$row["IS_BUYER_SUPPLIED"]."','".$row["IS_GMTS_PRODUCT"]."','".$row["IS_TWISTED"]."','".$row["FIXED_ASSET"]."','".$row["ORDER_UOM"]."','".$row["CONVERSION_FACTOR"]."','".$row["BOND_STATUS"]."','".$row["IS_SUPP_COMP"]."','".$row["SECTION_ID"]."','".$row["ITEM_NUMBER"]."','".$row["IS_POSTED_SQL"]."','".$row["CUMULATIVE_BALANCE"]."' ) ");
	
	if($upTransID){ $upTransID=1; } else {echo "insert into PRODUCT_DETAILS_MASTER (ID, COMPANY_ID, SUPPLIER_ID, STORE_ID, ITEM_CATEGORY_ID, DETARMINATION_ID, SUB_GROUP_CODE, SUB_GROUP_NAME, ITEM_GROUP_ID, ITEM_DESCRIPTION, PRODUCT_NAME_DETAILS, LOT, ITEM_CODE, UNIT_OF_MEASURE, RE_ORDER_LABEL, MINIMUM_LABEL, MAXIMUM_LABEL, ITEM_ACCOUNT, PACKING_TYPE, AVG_RATE_PER_UNIT, LAST_PURCHASED_QNTY, CURRENT_STOCK, LAST_ISSUED_QNTY, STOCK_VALUE, YARN_COUNT_ID, YARN_COMP_TYPE1ST, YARN_COMP_PERCENT1ST, YARN_COMP_TYPE2ND, YARN_COMP_PERCENT2ND, YARN_TYPE, COLOR, ITEM_COLOR, GMTS_SIZE, GSM, BRAND, BRAND_SUPPLIER, DIA_WIDTH, ITEM_SIZE, WEIGHT, ALLOCATED_QNTY, AVAILABLE_QNTY, INSERTED_BY, INSERT_DATE, UPDATED_BY, UPDATE_DATE, STATUS_ACTIVE, IS_DELETED, ENTRY_FORM, ITEM_RETURN_QTY, ORIGIN, MODEL, CAPACITY, OTHERINFO, BRAND_NAME, TEST_AVAILABLE, DYED_TYPE, IS_BUYER_SUPPLIED, IS_GMTS_PRODUCT, IS_TWISTED, FIXED_ASSET, ORDER_UOM, CONVERSION_FACTOR, BOND_STATUS, IS_SUPP_COMP, SECTION_ID, ITEM_NUMBER, IS_POSTED_SQL, CUMULATIVE_BALANCE) values (".$id.",'7','".$row["SUPPLIER_ID"]."','".$row["STORE_ID"]."','".$row["ITEM_CATEGORY_ID"]."','".$row["DETARMINATION_ID"]."','".$row["SUB_GROUP_CODE"]."','".$row["SUB_GROUP_NAME"]."','".$row["ITEM_GROUP_ID"]."','".$row["ITEM_DESCRIPTION"]."','".$row["PRODUCT_NAME_DETAILS"]."','".$row["LOT"]."','".$row["ITEM_CODE"]."','".$row["UNIT_OF_MEASURE"]."','".$row["RE_ORDER_LABEL"]."','".$row["MINIMUM_LABEL"]."','".$row["MAXIMUM_LABEL"]."','".$row["ITEM_ACCOUNT"]."','".$row["PACKING_TYPE"]."','0','0','0','0','0','".$row["YARN_COUNT_ID"]."','".$row["YARN_COMP_TYPE1ST"]."','".$row["YARN_COMP_PERCENT1ST"]."','".$row["YARN_COMP_TYPE2ND"]."','".$row["YARN_COMP_PERCENT2ND"]."','".$row["YARN_TYPE"]."','".$row["COLOR"]."','".$row["ITEM_COLOR"]."','".$row["GMTS_SIZE"]."','".$row["GSM"]."','".$row["BRAND"]."','".$row["BRAND_SUPPLIER"]."','".$row["DIA_WIDTH"]."','".$row["ITEM_SIZE"]."','".$row["WEIGHT"]."','0','0','".$row["INSERTED_BY"]."','".$row["INSERT_DATE"]."','".$row["UPDATED_BY"]."','".$row["UPDATE_DATE"]."','".$row["STATUS_ACTIVE"]."','".$row["IS_DELETED"]."','".$row["ENTRY_FORM"]."','".$row["ITEM_RETURN_QTY"]."','".$row["ORIGIN"]."','".$row["MODEL"]."','".$row["CAPACITY"]."','".$row["OTHERINFO"]."','".$row["BRAND_NAME"]."','".$row["TEST_AVAILABLE"]."','".$row["DYED_TYPE"]."','".$row["IS_BUYER_SUPPLIED"]."','".$row["IS_GMTS_PRODUCT"]."','".$row["IS_TWISTED"]."','".$row["FIXED_ASSET"]."','".$row["ORDER_UOM"]."','".$row["CONVERSION_FACTOR"]."','".$row["BOND_STATUS"]."','".$row["IS_SUPP_COMP"]."','".$row["SECTION_ID"]."','".$row["ITEM_NUMBER"]."','".$row["IS_POSTED_SQL"]."','".$row["CUMULATIVE_BALANCE"]."' ) ";oci_rollback($con);die;}
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
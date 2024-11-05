<?
include('../includes/common.php');
$con = connect();

$prod_id_arrs=explode(",","15942,15943,15944,15942,15943,15944,16707");
foreach($prod_id_arrs as $prod_id)
{
	$prod_id_arr[$prod_id]=$prod_id;
}

$yarn_without_dyeing_charge_aql="select A.RECV_NUMBER,A.BOOKING_ID,A.BOOKING_NO,B.PROD_ID,C.YARN_COUNT_ID,C.YARN_COMP_PERCENT1ST,C.YARN_TYPE,C.YARN_COMP_TYPE1ST,C.COLOR, C.PRODUCT_NAME_DETAILS, B.CONS_RATE,B.ORDER_RATE,B.DYE_CHARGE
from inv_receive_master a,inv_transaction b,product_details_master c
where a.id=b.mst_id and b.prod_id=c.id and a.item_category=1 and b.item_category=1 and b.status_active=1 
and a.receive_basis=2 and a.receive_purpose=2
and (b.dye_charge=0 or b.dye_charge is null) and c.dyed_type=1";

//echo $yarn_without_dyeing_charge_aql;die;
$result=sql_select($yarn_without_dyeing_charge_aql);
$wo_no_arr=array();
foreach($result as $row)
{
	$wo_no_arr[$row["BOOKING_ID"]] = $row["BOOKING_ID"];
}

if(!empty($wo_no_arr))
{
	$yarn_wo_sql="select A.ID,A.YDW_NO,B.COUNT,B.YARN_COMP_PERCENT1ST,B.YARN_COMP_TYPE1ST,B.YARN_COLOR,B.YARN_TYPE,B.YARN_DESCRIPTION,B.DYEING_CHARGE
	from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls b
	where a.id=b.mst_id and b.status_active=1";
	$yarn_wo_data=sql_select($yarn_wo_sql);
	if(!empty($yarn_wo_data))
	{
		$dyeing_charge_arr=array();
		foreach($yarn_wo_data as $row)
		{
			$dyeing_charge_arr[$row["ID"]][$row["COUNT"]][$row["YARN_COMP_PERCENT1ST"]][$row["YARN_COMP_TYPE1ST"]][$row["YARN_TYPE"]][$row["YARN_COLOR"]] = $row["DYEING_CHARGE"];
		}
	}
}


if($db_type==2)
{
	//$upTransID=execute_query(bulk_update_sql_statement2("inv_transaction","id",$update_field,$update_data,$updateID_array));
	if($upTransID && $upProdID)
	{
		oci_commit($con); 
		echo "Transaction Data Update Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "Transaction Data Update Failed";
		die;
	}
}
?>
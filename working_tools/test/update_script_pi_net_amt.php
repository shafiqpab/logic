<?
include('../includes/common.php');
$con = connect();

$pi_sql="select ID, TOTAL_AMOUNT, UPCHARGE, DISCOUNT, NET_TOTAL_AMOUNT from com_pi_master_details where id in(5050) and status_active=1 and DISCOUNT >0 or upcharge >0 and id not in(select pi_id from COM_BTB_LC_PI ) 
";
//and DISCOUNT >0 or upcharge >0 and id not in(select pi_id from COM_BTB_LC_PI ) 
//echo $pi_sql;
$pi_sql_result=sql_select($pi_sql);
$pi_data=array();
foreach($pi_sql_result as $row)
{
	$pi_data[$row["ID"]]["TOTAL_AMOUNT"]=$row["TOTAL_AMOUNT"];
	$pi_data[$row["ID"]]["UPCHARGE"]=$row["UPCHARGE"];
	$pi_data[$row["ID"]]["DISCOUNT"]=$row["DISCOUNT"];
	$pi_data[$row["ID"]]["NET_TOTAL_AMOUNT"]=$row["NET_TOTAL_AMOUNT"];
}
unset($pi_sql_result);

$sql_pi_item="select ID, PI_ID, QUANTITY, RATE, AMOUNT, NET_PI_RATE, NET_PI_AMOUNT 
from com_pi_item_details where pi_id in(5050) and status_active=1 and pi_id in(select id from com_pi_master_details where status_active=1 and DISCOUNT >0 or upcharge >0 and id not in(select pi_id from COM_BTB_LC_PI ))
";
//and pi_id in(select id from com_pi_master_details where status_active=1 and DISCOUNT >0 or upcharge >0 and id not in(select pi_id from COM_BTB_LC_PI ))  
echo $sql_pi_item;die;
$result=sql_select($sql_pi_item);
$i=1;$k=1;
$upTransID=$upmstID=true;
foreach($result as $row)
{
	$item_amt=$row["AMOUNT"];
	$item_quantity=$row["QUANTITY"];
	$txt_total_amount=$pi_data[$row["PI_ID"]]["TOTAL_AMOUNT"];
	$txt_total_upcharge=$pi_data[$row["PI_ID"]]["UPCHARGE"];
	if($txt_total_upcharge=="") $txt_total_upcharge=0;
	$txt_total_discount=$pi_data[$row["PI_ID"]]["DISCOUNT"];
	if($txt_total_discount=="") $txt_total_discount=0;
	$txt_total_amount_net=($txt_total_amount+$txt_total_upcharge)-$txt_total_discount;
	
	
	$perc=($item_amt/$txt_total_amount)*100;
	$net_pi_amount=($perc*$txt_total_amount_net)/100;
	if($item_quantity>0 && $net_pi_amount >0) $net_pi_rate=number_format($net_pi_amount/$item_quantity,6,'.',''); else $net_pi_rate=0;
	$net_pi_amount=number_format($net_pi_amount,6,'.','');
	
	if($txt_total_amount>0)
	{
		if($pi_check[$row["PI_ID"]]=="")
		{
			$pi_check[$row["PI_ID"]]=$row["PI_ID"];
			$upmstID=execute_query("update com_pi_master_details set NET_TOTAL_AMOUNT='".$txt_total_amount_net."' where id=".$row["PI_ID"]." ");
			if($upmstID){ $upmstID=1; } else {echo "update com_pi_master_details set NET_TOTAL_AMOUNT='".$net_pi_amount."' where id=".$row["PI_ID"]." ";oci_rollback($con);die;}
		}
		
		$upTransID=execute_query("update com_pi_item_details set NET_PI_RATE='".$net_pi_rate."', NET_PI_AMOUNT='".$net_pi_amount."' where id=".$row["ID"]." ");
		if($upTransID){ $upTransID=1; } else {echo"update com_pi_item_details set NET_PI_RATE='".$net_pi_rate."', NET_PI_AMOUNT='".$net_pi_amount."' where id=".$row["ID"]." ";oci_rollback($con);die;}
	}
}



if($db_type==2)
{
	if($upTransID && $upmstID)
	{
		oci_commit($con); 
		echo "PI Data Update Successfully. <br>";die;
	}
	else
	{
		oci_rollback($con);
		echo "PI Data Update Failed";
		die;
	}
}
?>
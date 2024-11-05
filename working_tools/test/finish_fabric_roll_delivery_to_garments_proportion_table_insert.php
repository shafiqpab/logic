<?
header('Content-type:text/html; charset=utf-8');
session_start();

include('../includes/common.php');
$con=connect();

$delivery_to_store_sql = "SELECT b.id as dtls_id, c.id as prop_id, b.issue_qnty, d.color, b.prod_id, b.order_id, b.trans_id
from inv_issue_master a, inv_finish_fabric_issue_dtls b left join order_wise_pro_details c on b.trans_id= c.trans_id and c.entry_form=318, product_details_master d
where a.id=b.mst_id and b.prod_id=d.id and a.entry_form=318 and a.status_active=1 and b.status_active=1 and c.id is null ";

$delivery_to_store_data = sql_select($delivery_to_store_sql);

if(empty($delivery_to_store_data)){
	echo "Data not Found";
	disconnect($con);
	die;
}

if(!empty($delivery_to_store_data))
{
	$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date,is_sales";
	foreach($delivery_to_store_data  as $val)
	{
		$trans_id 				= $val[csf("trans_id")];
		$dtls_id 				= $val[csf("dtls_id")];
		$order_id 				= $val[csf("order_id")];
		$prod_id 				= $val[csf("prod_id")];
		$color 					= $val[csf("color")];
		$issue_qnty 			= $val[csf("issue_qnty")];

		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date,is_sales";

		$data_array_prop ="(".$id_prop.",".$trans_id.",2,318,".$dtls_id.",".$order_id.",".$prod_id.",'".$color."','".$issue_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','1')";

		//echo "Failed== insert into order_wise_pro_details (".$field_array_prop.") values ".$data_array_prop . ";<br />";
		$rID = sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,1);
		if($rID ==0){
			echo "Failed== insert into order_wise_pro_details (".$field_array_prop.") values ".$data_array_prop . ";<br />";
			oci_rollback($con);
			disconnect($con);
			die;
		}

		
	}
}


oci_commit($con);
echo "success";
disconnect($con);
die;
?>
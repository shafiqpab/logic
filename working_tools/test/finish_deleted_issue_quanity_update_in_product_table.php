<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();

//update inv_transaction set transaction_type_bk = transaction_type;

$transaction_id_sql =  sql_select("select prod_id, sum(quantity) as quantity from order_wise_pro_details where entry_form =18 and status_active =1 and  TRANS_ID  in (15226,15225,15224,15223,15222,15221,15220,15219,15218,15217,15216,15215, 15214,15213, 15212, 15211, 15210,15209,15208, 15207, 15206, 15205,15204, 15203,15202,15201,15200,15199,15198, 15197,15196,15195,15194,15193,15192, 15191,15190, 15119,15118, 15117,15116, 15115, 15114, 15112, 15111,15110,15109, 15108,15107,15106,15105,15104,15103,15102,15101, 15100,15099,15098,15097,15096,15095,15094, 15093,15092, 15091,15090,15089, 15088,15087, 15086, 15085, 15084,15083,15081, 15080,15078, 15077,15076, 15074,15073,15072) and prod_id in (5325,5324,2278,5320,5322,5315,5317,5297,5329,5319,5318,5316,5282,5272,5296,5290,5276,5271,5284,5327,5286,5323,5283,5227,5281,5289,5279,5274,4703,5255,5273,5287,5270,5278,5321,2252,5294,5277,5288,5280,5285,5295,5275,5330,5328,5326) group by prod_id ");
if(empty($transaction_id_sql))
{
	echo "Data Not Found";
	die;
}

$prod_ref_sql = sql_select("select id, avg_rate_per_unit, current_stock, stock_value from product_details_master where id in (5325,5324,2278,5320,5322,5315,5317,5297,5329,5319,5318,5316,5282,5272,5296,5290,5276,5271,5284,5327,5286,5323,5283,5227,5281,5289,5279,5274,4703,5255,5273,5287,5270,5278,5321,2252,5294,5277,5288,5280,5285,5295,5275,5330,5328,5326)");


foreach ($prod_ref_sql as $val) 
{
	$prod_data[$val[csf("id")]]['avg_rate_per_unit'] = $val[csf("avg_rate_per_unit")];
	$prod_data[$val[csf("id")]]['current_stock'] = $val[csf("current_stock")];
	$prod_data[$val[csf("id")]]['stock_value'] = $val[csf("stock_value")];
}




foreach ($transaction_id_sql as $val) 
{
	$product_stock = $prod_data[$val[csf("prod_id")]]['current_stock'] + $val[csf("quantity")];
	$product_value = $product_stock * $prod_data[$val[csf("prod_id")]]['avg_rate_per_unit'];

	$product_stock = number_format($product_stock,2,".","");
	$product_value = number_format($product_value,4,".","");
	
	echo "update product_details_master set current_stock='".$product_stock."',stock_value='".$product_value."' where id=".$val[csf("prod_id")]."<br>";

	//execute_query("update order_wise_pro_details set trans_type='6',updated_by=999 where id=".$val[csf("prod_id")],0);
}

/*oci_commit($con); 
echo "Success";
disconnect($con);
die;*/
 
?>
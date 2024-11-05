<?
	include('includes/common.php');
	
	$con = connect();
	
	$sql_bal=sql_select("select prod_id,(sum(case when a.transaction_type in(1,4) then a.cons_quantity else 0 end) - sum(case when a.transaction_type in(2,3) then a.cons_quantity else 0 end)) as balance_stock from inv_transaction a where a.status_active=1 and a.item_category=11 and a.prod_id>0 group by a.prod_id");
	$update_field="current_stock";
	foreach($sql_bal as $row)
	{
		$i++;
		$update_id_arr[]=$row[csf("prod_id")];
		$update_data_arr[$row[csf("prod_id")]]=explode("*",("'".$row[csf("balance_stock")]."'"));
	}
	$upsubDtlsID=bulk_update_sql_statement("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	echo $upsubDtlsID;
	//$rID=execute_query($upsubDtlsID);
	
	
	//if($rID) echo "Success"; else {echo "Failed";echo $upsubDtlsID;die;}
	
	//die;
	
	?>
	
<?
/*-------------------------------------------- Comments
Purpose			: 	This Page is use for delete wash module all data 
Functionality	:	
JS Functions	:
Created by		:	K.M Nazim Uddin
Creation date 	: 	22-10-2019
Updated by 		: 		
Update date		: 
Oracle Convert 	:		
Convert date	: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
$con=connect();
//die; 


/* *****************************      FOR ORDER RECEIVE      **********************         */

/*$wash_sql="select id,order_id,order_no,subcon_job from subcon_ord_mst where entry_form=295";
$apply_sql_res=sql_select($wash_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	
	$booking_no="'".$row[csf("order_no")]."'";
	$wash_job_no="'".$row[csf("subcon_job")]."'";
	$id="'".$row[csf("id")]."'";
	
	$booking_update=execute_query("update wo_booking_mst set lock_another_process=0 where booking_no=$booking_no and lock_another_process=1");
	if($booking_update==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	} 
	//echo "delete from subcon_ord_breakdown  where job_no_mst= $wash_job_no";
	$breakdown_delete = execute_query("DELETE FROM subcon_ord_breakdown WHERE job_no_mst=$wash_job_no");
	//$breakdown_delete=execute_query("delete from subcon_ord_breakdown  where job_no_mst= $wash_job_no");
	if($breakdown_delete==1)
	{
		$flag=1;
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	} 
	
	$dtls_delete = execute_query("UPDATE subcon_ord_dtls set status_active=5, is_deleted=6 WHERE mst_id=$id and job_no_mst=$wash_job_no");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		//oci_rollback($con); 
		mysql_query("ROLLBACK");
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE subcon_ord_mst set status_active=5, is_deleted=6  WHERE id=$id and entry_form=295");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		//oci_rollback($con);
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
}*/

/* *****************************      FOR MATERIAL RECEIVE    **********************         */
/*$wash_mat_rec_sql="select id,embl_job_no,sys_no from sub_material_mst where entry_form=296 and trans_Type=1";
$apply_sql_res=sql_select($wash_mat_rec_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_job_no="'".$row[csf("embl_job_no")]."'";
	$id="'".$row[csf("id")]."'";
	
	$dtls_delete = execute_query("UPDATE sub_material_dtls set status_active=5, is_deleted=6  WHERE mst_id=$id ");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE sub_material_mst set status_active=5, is_deleted=6  WHERE id=$id and entry_form=296 and trans_Type=1");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	}
}*/

/* *****************************      FOR MATERIAL ISSUE    **********************         */

/*$wash_mat_iss_sql="select id,embl_job_no,sys_no from sub_material_mst where entry_form=297 and trans_Type=2";
$apply_sql_res=sql_select($wash_mat_iss_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_job_no="'".$row[csf("embl_job_no")]."'";
	$id="'".$row[csf("id")]."'";
	
	$dtls_delete = execute_query("UPDATE sub_material_dtls set status_active=5, is_deleted=6  WHERE mst_id=$id");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE sub_material_mst set status_active=5, is_deleted=6  WHERE id=$id and entry_form=297 and trans_Type=2");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
}*/

/*$wash_batch_sql="select id,batch_no from pro_batch_create_mst where entry_form=316";
$apply_sql_res=sql_select($wash_batch_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_batch_no="'".$row[csf("batch_no")]."'";
	$id="'".$row[csf("id")]."'";
	
	$dtls_delete = execute_query("UPDATE  pro_batch_create_dtls set status_active=5, is_deleted=6 WHERE mst_id=$id");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE  pro_batch_create_mst set status_active=5, is_deleted=6  WHERE id=$id and entry_form=316");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
}*/


/* *****************************      FOR RECEPIE    **********************         */
/*
$wash_recepie_sql="select id,recipe_no from pro_recipe_entry_mst where entry_form=300";
$apply_sql_res=sql_select($wash_recepie_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_recipie_no="'".$row[csf("recipe_no")]."'";
	$id="'".$row[csf("id")]."'";
	
	$dtls_delete = execute_query("UPDATE  pro_recipe_entry_dtls set status_active=5, is_deleted=6   WHERE mst_id=$id");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");  
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE  pro_recipe_entry_mst set status_active=5, is_deleted=6   WHERE id=$id and entry_form=300");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
}
*/

/* *****************************      FOR PRODUCTION    **********************         */
// entry_form= 301 Wet production // 342 Dry production

/*
$wash_production_sql="select id,sys_no from subcon_embel_production_mst where entry_form in (301,342)";
$apply_sql_res=sql_select($wash_production_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_prod_no="'".$row[csf("sys_no")]."'";
	$id="'".$row[csf("id")]."'";
	
	$dtls_delete = execute_query("UPDATE  subcon_embel_production_dtls set status_active=5, is_deleted=6  WHERE mst_id=$id");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE  subcon_embel_production_mst set status_active=5, is_deleted=6   WHERE id=$id and entry_form in (301,342)");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
}
*/

/* *****************************      FOR QC    **********************         */

/*$wash_qc_sql="select id,sys_no from subcon_embel_production_mst where entry_form in (302)";
$apply_sql_res=sql_select($wash_qc_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_qc_no="'".$row[csf("sys_no")]."'";
	$id="'".$row[csf("id")]."'";
	
	$dtls_delete = execute_query("UPDATE  subcon_embel_production_dtls set status_active=5, is_deleted=6   WHERE mst_id=$id");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE  subcon_embel_production_mst set status_active=5, is_deleted=6  WHERE id=$id and entry_form in (302)");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	}
}
*/

/* *****************************      FOR Delivery    ***************************** */

/*$wash_delv_sql="select id,delivery_no from subcon_delivery_mst where entry_form in (303)";
$apply_sql_res=sql_select($wash_delv_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_delv_no="'".$row[csf("delivery_no")]."'";
	$id="'".$row[csf("id")]."'";
	
	$dtls_delete = execute_query("UPDATE  subcon_delivery_dtls set status_active=5, is_deleted=6    WHERE mst_id=$id");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE  subcon_delivery_mst set status_active=5, is_deleted=6    WHERE id=$id and entry_form in (303)");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	}
}
*/

/* *****************************      FOR BILL ISSUE    ***************************** */
//  process_id=304
/*$wash_bill_sql="select id,bill_no from subcon_inbound_bill_mst where process_id=304";
$apply_sql_res=sql_select($wash_bill_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_bill_no="'".$row[csf("bill_no")]."'";
	$id="'".$row[csf("id")]."'";
	
	$dtls_delete = execute_query("UPDATE  subcon_inbound_bill_dtls set status_active=5, is_deleted=6  WHERE  mst_id=$id and process_id=304");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK"); 
		echo "failed";
	}
	$mst_delete = execute_query("UPDATE  subcon_inbound_bill_mst set status_active=5, is_deleted=6   WHERE id=$id and process_id=304");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		mysql_query("ROLLBACK");
		echo "failed";
	}
}*/


/* *****************************      FOR DYES AND CHEMICAL ISSUE REQUISITION    ***************************** */

//$sql = "select color_id, new_prod_id from pro_recipe_entry_dtls where mst_id=$recipe_id and status_active=1 and is_deleted=0 order by id ASC";
/*$sql_prod="select id, current_stock from product_details_master where company_id=$cbo_company_name and item_category_id in(5,6,7) and status_active=1 and is_deleted=0";

if( $nprod_id_all !="")
{
	$sql_prod="select id, current_stock from product_details_master where company_id=$cbo_company_name and item_category_id in(5,6,7) and status_active=1 and is_deleted=0";
	$current_stock_arr=array();
	$sql_prod_res=sql_select( $sql_prod );
	foreach($sql_prod_res as $row)
	{
		$current_stock_arr[$row[csf('id')]]=$row[csf('current_stock')];
	}
	unset($sql_prod_res);
	
	$exnprod_id=explode(",",$nprod_id_all);
	
	foreach($exnprod_id as $npid)
	{
		$stock=$nprod_qty_arr[$npid]+($current_stock_arr[$npid]*1);
		execute_query( "update product_details_master set current_stock='$stock' where id ='".$npid."'",1);
	}
}*/



$wash_dyes_req_sql="select id,requ_no,recipe_id from dyes_chem_issue_requ_mst where entry_form=299";
$apply_sql_res=sql_select($wash_dyes_req_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_requ_no="'".$row[csf("requ_no")]."'";
	$id="'".$row[csf("id")]."'";
	$recipe_id="'".$row[csf("recipe_id")]."'";
	
	$dtls_delete = execute_query("DELETE FROM dyes_chem_issue_requ_dtls WHERE  mst_id=$id and requ_no=$wash_requ_no");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con); 
		echo "failed";
	}
	$att_delete = execute_query("DELETE FROM dyes_chem_requ_recipe_att WHERE  mst_id=$id and recipe_id=$recipe_id ");
	//echo $att_delete;
	if($att_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con); 
		echo "failed";
	}
	$mst_delete = execute_query("DELETE FROM dyes_chem_issue_requ_mst WHERE id=$id and entry_form=299");
	if($mst_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con); 
		echo "failed";
	}
}



/* *****************************      FOR DYES AND CHEMICAL ISSUE   ***************************** */

//product_details_master  last_issued_qnty*current_stock*stock_value


//$wash_dyes_issue_sql="select id,issue_number,req_id from inv_issue_master where entry_form=298";
/*$wash_dyes_issue_sql="SELECT a.id,a.issue_number,b.id as tranID,b.store_id, b.cons_uom, b.cons_quantity,b.cons_amount, b.prod_id,b.cons_rate,b.cons_amount,c.item_description, c.item_group_id, c.sub_group_name, c.item_size, d.sub_process, d.item_category, d.required_qnty, d.req_qny_edit 
from inv_issue_master a, inv_transaction b, product_details_master c, dyes_chem_issue_dtls d 
where a.id=d.mst_id and b.id =d.trans_id and d.product_id=c.id  and b.transaction_type=2 and a.entry_form=298 and b.item_category in (5,6,7,23) 
order by d.sub_process";*/

$wash_dyes_issue_sql="select a.id, a.issue_number, a.req_id, b.id as tranID, b.prod_id from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=298";


$apply_sql_res=sql_select($wash_dyes_issue_sql); $flag=1;
foreach($apply_sql_res as $row)
{
	$wash_issue_number="'".$row[csf("issue_number")]."'";
	$id 		="'".$row[csf("id")]."'";
	$req_id 	="'".$row[csf("req_id")]."'";
	$tranID 	="'".$row[csf("tranID")]."'";
	$prod_id 	="'".$row[csf("prod_id")]."'";
	$deleted_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
	
	$tran_delete = execute_query("update inv_transaction set status_active=5, is_deleted=6 WHERE  id=$tranID");
	//echo $tran_delete;
	if($tran_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con); 
		echo "failed";
	}
	$dtls_delete = execute_query("update dyes_chem_issue_dtls set status_active=5, is_deleted=6 WHERE  trans_id=$tranID");
	//echo $dtls_delete;
	if($dtls_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con); 
		echo "failed";
	}
	//recv_trans_id,issue_trans_id,entry_form,prod_id
	$mrr_delete = execute_query("update inv_mrr_wise_issue_details set status_active=5, is_deleted=6 WHERE entry_form=298 and issue_trans_id=$tranID ");
	//echo $mrr_delete;
	if($mrr_delete==1)
	{
		$flag=1; 
	} 
	else
	{
		$flag=0;
		oci_rollback($con); 
		echo "failed";
	}
	if($issue_id_check[$id]=="")
	{
		$issue_id_check[$id]=$id;
		$mst_delete = execute_query("update inv_issue_master set status_active=5, is_deleted=6 WHERE id=$id and entry_form=298");
		if($mst_delete==1)
		{
			$flag=1; 
		} 
		else
		{
			$flag=0;
			oci_rollback($con); 
			echo "failed";
		}
	}
}

/*
insert into inv_store_wise_qty_dtls (company_id, location_id, store_id, category_id, prod_id, cons_qty, rate, amount, inserted_by, insert_date) 
select company_id, 0 as location_id, store_id, item_category, prod_id,
sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty,
avg(cons_rate) as rate,
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt, 1 as inserted_by, NOW() as insert_date
from inv_transaction where item_category in(5,6,7,23) and status_active=1 
group by company_id,  store_id, item_category, prod_id
order by company_id

inser script into 2 part

part 1

insert into inv_store_wise_qty_dtls (company_id, location_id, store_id, category_id, prod_id, cons_qty, rate, amount, inserted_by, insert_date) 
select company_id, 0 as location_id, store_id, item_category, prod_id,
sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty,
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end))/sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as rate,
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt, 1 as inserted_by, NOW() as insert_date
from inv_transaction where item_category in(5,6,7,23) and status_active=1 
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) >0 
and sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) >0
group by company_id,  store_id, item_category, prod_id
order by company_id

part 2

insert into inv_store_wise_qty_dtls (company_id, location_id, store_id, category_id, prod_id, cons_qty, rate, amount, inserted_by, insert_date) 
select company_id, 0 as location_id, store_id, item_category, prod_id,
sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal_qnty,
0 as rate,
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) as bal_amt, 1 as inserted_by, NOW() as insert_date
from inv_transaction where item_category in(5,6,7,23) and status_active=1 
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) >0 
and sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) >0
group by company_id,  store_id, item_category, prod_id
having 
sum((case when transaction_type in(1,4,5) then cons_amount else 0 end)-(case when transaction_type in(2,3,6) then cons_amount else 0 end)) <= 0
order by company_id

if ($action=="synchronize_stock")
{
	extract($_REQUEST);
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	
	$row_prod=sql_select("select a.id,a.avg_rate_per_unit, a.allocated_qnty ,sum(case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-sum(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end) as stock_qty from product_details_master a , inv_transaction b where a.id= b.prod_id and a.id in(".implode(",",$deleted_prod_id).") group by a.id,a.avg_rate_per_unit, a.allocated_qnty");
	
	$field_array_prod_update="current_stock*stock_value";
	foreach($row_prod as $row)
	{
		$prodID=$row[csf("id")];
		$stock_qty=$row[csf("stock_qty")];
		$stock_qty=$row[csf("stock_qty")];
		
		$id_arr[]=$row[csf("id")];
		$data_array_prod[$row[csf("id")]]=explode(",",("".$row[csf("stock_qty")].",".$currentStock.",".$StockValue.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'"));
		$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,1);
				
		$auxChemDataArr[$row[csf("prod_id")]]=$row[csf("id")]."**".$row[csf("cons_rate")]."**".$row[csf("balance_qnty")]."**".$row[csf("balance_amount")].",";
		
		$data_array_prod_update=$curr_stock_qnty."*".$stock_value."*".$available_qnty;
	}
	
	 $rID4=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod, $data_array_prod, $id_arr ),1);
	 
	 
	 
	 
	$curr_stock_qnty=return_field_value("sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end)-sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as stock_qty","inv_transaction","status_active=1 and is_deleted=0 and prod_id=$prod_id","stock_qty");
	$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')]; 
	$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
	$available_qnty=$curr_stock_qnty-$row_prod[0][csf('allocated_qnty')];
	
	$field_array_prod_update="current_stock*stock_value*available_qnty";
	$data_array_prod_update=$curr_stock_qnty."*".$stock_value."*".$available_qnty;
	
	$rID=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$prod_id,1);
	
	//$row_tran_prod=sql_select("select prod_id, sum(case when transaction_type in(1,4,5) then cons_quantity else 0 end)-sum(case when transaction_type in(2,3,6) then cons_quantity else 0 end) as stock_qty from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".implode(",",$deleted_prod_id).") group by prod_id");
	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");  
			echo "Data Synchronize is completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK"); 
			echo "Data Synchronize is not completed successfully";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);  
			echo "Data Synchronize is completed successfully";
		}
		else
		{
			oci_rollback($con);
			echo "Data Synchronize is not completed successfully**$data_array_prod_update";
		}
	}
	disconnect($con);
	die;

}

*/

//oci_rollback($con); 
//echo $flag."kkf"; die;

//echo $test_data;die;
if($flag==1)
{
	mysql_query("COMMIT");
	echo "Success";

}
else
{
	//oci_rollback($con);
	mysql_query("ROLLBACK"); 
	echo "failed";
}



 
?>
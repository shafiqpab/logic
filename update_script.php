<?
include('includes/common.php');
$con = connect();

echo $item_cate_credential_cond="".implode(",",array_flip($general_item_category))."";die;

$recv_id_array=array(); $iss_id_array=array(); $trans_id_array=array(); $trans_array=array(); $prod_id_array=array();
$sql="select a.id as mst_id, b.* from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=1 and a.item_category=1 and b.item_category=1 and a.receive_purpose=2 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=1214";
$result=sql_select($sql);
foreach($result as $row)
{
	$trans_array['recv'][$row[csf('id')]]=$row[csf('id')];
	if(!in_array($row[csf('prod_id')],$prod_id_array))
	{
		$prod_id_array[]=$row[csf('prod_id')];
	}
	
	if(!in_array($row[csf('mst_id')],$recv_id_array))
	{
		$recv_id_array[]=$row[csf('mst_id')];
	}
	
	if(!in_array($row[csf('id')],$trans_id_array))
	{
		$trans_id_array[]=$row[csf('id')];
	}
}

$sql2="select a.id as mst_id, b.* from inv_issue_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=3 and a.item_category=1 and b.item_category=1 and a.issue_purpose=2 and b.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id=1214";
$result2=sql_select($sql2);
foreach($result2 as $row2)
{
	$trans_array['iss'][$row2[csf('id')]]=$row2[csf('id')];
	
	if(!in_array($row2[csf('prod_id')],$prod_id_array))
	{
		$prod_id_array[]=$row2[csf('prod_id')];
	}
	
	if(!in_array($row2[csf('mst_id')],$iss_id_array))
	{
		$iss_id_array[]=$row2[csf('mst_id')];
	}
	
	if(!in_array($row2[csf('id')],$trans_id_array))
	{
		$trans_id_array[]=$row2[csf('id')];
	}
}

asort($prod_id_array);
$prod_id_all=implode(",",$prod_id_array);
$updateID_array = $update_data = array();// avg_rate_per_unit <=0 and
$update_array	= "current_stock*stock_value"; 

$dataArray=sql_select("select id, current_stock, stock_value, avg_rate_per_unit from product_details_master where item_category_id=1 and id in (1214)");
foreach($dataArray as $row)
{
	$recvData=sql_select("select sum(case when transaction_type=1 then cons_quantity end) as recvqnty, sum(case when transaction_type=2 then cons_quantity end) as issqnty, sum(case when transaction_type=3 then cons_quantity end) as recvretqnty, sum(case when transaction_type=4 then cons_quantity end) as issretqnty from inv_transaction where prod_id='$row[id]' and status_active=1 and is_deleted=0");
	$stock=$recvData[0][csf('recvqnty')]+$recvData[0][csf('issretqnty')]-$recvData[0][csf('issqnty')]-$recvData[0][csf('recvretqnty')];
	
	$amnt=$stock*$row[csf('avg_rate_per_unit')];
	$updateID_array[]=$row[csf('id')];
	$update_data[$row[csf('id')]]=explode("*",($stock."*".$amnt));
}

$query=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array,$update_data,$updateID_array));//execute_query
$deleteRecv=$deleteRecv=$deleteTrans=$deletemrr=$deletemrr2=1;

if(!empty( $deleteRecv )){
	$deleteRecv=execute_query("delete from inv_receive_master  where id in (".implode(",",$recv_id_array).")");
}

 
if(!empty( $iss_id_array )){
$deleteIss=execute_query("delete from inv_issue_master where id in (".implode(",",$iss_id_array).")");
}

if(!empty( $trans_id_array )){
$deleteTrans=execute_query("delete from inv_transaction where id in (".implode(",",$trans_id_array).")");
}

if(!empty( $trans_array['recv'] )){
$deletemrr=execute_query("delete from inv_mrr_wise_issue_details where recv_trans_id in (".implode(",",$trans_array['recv']).")");
}

if(!empty( $trans_array['iss'] )){
$deletemrr2=execute_query("delete from inv_mrr_wise_issue_details where issue_trans_id in (".implode(",",$trans_array['iss']).")");
}

if($query && $deleteRecv && $deleteIss && $deleteTrans && $deletemrr && $deletemrr2) echo "Success"; else "Invalid";die;


$allocated_qnty_arr = return_library_array( "select item_id, sum(qnty) as qnty from inv_material_allocation_dtls where status_active=1 and is_deleted=0 group by item_id",'item_id','qnty');
$receive_purpose_arr = return_library_array( "select id, receive_purpose from inv_receive_master",'id','receive_purpose');
$issue_purpose_arr = return_library_array( "select id, issue_purpose from inv_issue_master",'id','issue_purpose');

$recvDataArr=array();
$recvData=sql_select("select a.id, b.prod_id, sum(b.cons_quantity) as recvqnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=1 and a.entry_form=1 and a.item_category=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by a.id, b.prod_id");
foreach($recvData as $row)
{
	$recvDataArr[$row[csf('id')]][$row[csf('prod_id')]]=$row[csf('recvqnty')];
	$prod_recvid_arr[$row[csf('prod_id')]].=$row[csf('id')].",";
}
//echo $prod_recvid_arr[13708];
$issRtnDataArr=array();
$issRtnData=sql_select("select b.prod_id, sum(b.cons_quantity) as issrtnqnty from inv_receive_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=4 and a.entry_form=9 and a.item_category=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=1095 group by b.prod_id");
foreach($issRtnData as $row)
{
	$issRtnDataArr[$row[csf('prod_id')]]=$row[csf('issrtnqnty')];
}

$issDataArr=array();
$issData=sql_select("select a.id, b.prod_id, sum(b.cons_quantity) as issqnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=2 and a.entry_form=3 and a.item_category=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=1095 group by a.id, b.prod_id");
foreach($issData as $row)
{
	$issDataArr[$row[csf('id')]][$row[csf('prod_id')]]=$row[csf('issqnty')];
	$prod_issid_arr[$row[csf('prod_id')]].=$row[csf('id')].",";
}

$recvRtnDataArr=array();
$recvRtnData=sql_select("select a.received_id, b.prod_id, sum(b.cons_quantity) as recvrtnqnty from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=3 and a.entry_form=8 and a.item_category=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=1095 group by a.received_id, b.prod_id");
foreach($recvRtnData as $row)
{ 
	$recvRtnDataArr[$row[csf('received_id')]][$row[csf('prod_id')]]=$row[csf('recvrtnqnty')];
	$prod_recvRtnid_arr[$row[csf('prod_id')]].=$row[csf('received_id')].",";
}

$updateID_array = $update_data = array();
$update_array	= "allocated_qnty*available_qnty";

$i=0;
$dataArray=sql_select("select id, current_stock, allocated_qnty, available_qnty from product_details_master where item_category_id=1 and id=1095");

foreach($dataArray as $row)
{
	//echo $row['id']."**".$row['current_stock']."**".$row['allocated_qnty']."**".$allocated_qnty_arr[$row['id']]."**".$row['available_qnty']."<br>";	
	//echo $row['id']."**".$row['allocated_qnty']."**".$allocated_qnty_arr[$row['id']]."<br>";
	$allocated_qnty=$allocated_qnty_arr[$row[csf('id')]]; 
	$available_qnty=0;
	
	$available_qnty-=$allocated_qnty;
	
	$recvid=explode(",",substr($prod_recvid_arr[$row[csf('id')]],0,-1));
	$recvRtnid=explode(",",substr($prod_recvRtnid_arr[$row[csf('id')]],0,-1));
	$issid=explode(",",substr($prod_issid_arr[$row[csf('id')]],0,-1));
	$issRtnid=explode(",",substr($prod_issRtnid_arr[$row[csf('id')]],0,-1));
	
	foreach($recvid as $rid)
	{
		$recv_pr=$receive_purpose_arr[$rid];
		if($recv_pr==2)
		{
			$allocated_qnty+=$recvDataArr[$rid][$row[csf('id')]];
		}
		else
		{
			$available_qnty+=$recvDataArr[$rid][$row[csf('id')]];
		}
	}
	
	foreach($recvRtnid as $rtcid)
	{
		$ret_pr=$issue_purpose_arr[$rtcid];
		if($ret_pr==2)
		{
			$allocated_qnty-=$recvRtnDataArr[$rtcid][$row[csf('id')]];
		}
		else
		{
			$available_qnty-=$recvRtnDataArr[$rtcid][$row[csf('id')]];
		}
	}
	
	foreach($issid as $isid)
	{
		$iss_pr=$issue_purpose_arr[$isid];
		if($iss_pr==1 || $iss_pr==2)
		{
			$allocated_qnty-=$issDataArr[$isid][$row[csf('id')]];
		}
		else
		{
			$available_qnty-=$issDataArr[$isid][$row[csf('id')]];
		}
	}
	
	$issRtn=$issRtnDataArr[$row[csf('id')]];
	$available_qnty+=$issRtn;
	///echo $allocated_qnty."**".$available_qnty."<br>";
	//echo $row['id']."**".$row['current_stock']."**".$allocated_qnty."**".$available_qnty."<br>";
	
	//if($allocated_qnty<0) $allocated_qnty=0;
	//$available_qnty=$row[csf('current_stock')]-$allocated_qnty;
	$updateID_array[]=$row[csf('id')];
	$update_data[$row[csf('id')]]=explode("*",($allocated_qnty."*".$available_qnty));
	
}
//echo $i;
//echo bulk_update_sql_statement("product_details_master","id",$update_array,$update_data,$updateID_array);die;
$query=execute_query(bulk_update_sql_statement("product_details_master","id",$update_array,$update_data,$updateID_array));

if($query) echo "Success"; else "Invalid";

disconnect($con);

/*$sql_trans=sql_select("SELECT 
prod_id,
(sum(case when transaction_type in(1,4) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3) then cons_quantity else 0 end)) as current_stock
from inv_transaction
where status_active=1 and is_deleted=0 and prod_id>0 and item_category in(8,9,10,11,15,16,17,18,19,20,21,22)
group by prod_id");*/

/*$sql_trans=sql_select("SELECT 
prod_id,
(sum(case when transaction_type=1 then cons_quantity else 0 end) - sum(case when transaction_type=2 then cons_quantity else 0 end)) as current_stock
from inv_transaction
where status_active=1 and is_deleted=0 and prod_id>0 and item_category=4
group by prod_id");

//$update_prod_sql="update product_details_master set";
//"current_stock='".$row[csf('current_stock')]."' where id=".$row[csf('prod_id')]." ";


$update_field="current_stock";
foreach($sql_trans as $row)
{
	$update_id_arr[]=$row[csf('prod_id')];
	$update_data_arr[$row[csf('prod_id')]]=explode("*",($row[csf('current_stock')]));
}

$upsubDtlsID=bulk_update_sql_statement("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
echo $upsubDtlsID;
*/
$id=return_next_id( "id", "product_details_master", 1 ) ;
$only_gi_arr=array(); $only_trims_arr=array(); $both_arr=array();

$sql="select b.prod_id from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=24 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by prod_id order by prod_id";
$result=sql_select($sql);
foreach($result as $row)
{
	$only_trims_arr[]=$row[csf('prod_id')];
}

$sql="select b.prod_id from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=20 and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by prod_id order by prod_id";
$result=sql_select($sql);
foreach($result as $row)
{
	if(in_array($row[csf('prod_id')],$only_trims_arr))
	{
		$both_arr[$id]=$row[csf('prod_id')];
		$id++;
	}
	else
	{
		$only_gi_arr[]=$row[csf('prod_id')];
	}
}

//echo implode(",",$both_arr)."<br>";
//echo count($both_arr)."<br>";
//var_dump($both_arr);
//var_dump($only_gi_arr);
if(count($both_arr)>0)
{
	$id_string=""; $previds=''; $newids='';
	foreach($both_arr as $newId=>$previd)
	{
		$id_string.=" WHEN $previd THEN $newId";
		$previds.=$previd.",";
		$newids.=$newId.",";
	}
	
	$id_string_prod="CASE id ".$id_string." END";
	$id_string_trans="CASE b.prod_id ".$id_string." END";
	$id_string_nonOrder="CASE item_id ".$id_string." END";
	$id_string_mrr="CASE prod_id ".$id_string." END";
	
	$sql_insert="insert into product_details_master(id, company_id,supplier_id,store_id, item_category_id,entry_form, detarmination_id,sub_group_code, sub_group_name, item_group_id, item_description, product_name_details, lot, item_code, unit_of_measure, re_order_label,minimum_label,maximum_label, item_account,packing_type,avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color,gmts_size,gsm,brand, brand_supplier, dia_width, item_size, weight,allocated_qnty,available_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted) 
				select	
					$id_string_prod, company_id,supplier_id,store_id, item_category_id,20,detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code,unit_of_measure, re_order_label,minimum_label,maximum_label, item_account,packing_type,avg_rate_per_unit, last_purchased_qnty, current_stock, last_issued_qnty, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, item_color,gmts_size,gsm,brand, brand_supplier, dia_width, item_size, weight,allocated_qnty,available_qnty,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted from product_details_master where id in (".substr($previds,0,-1).")";
	
	$trans_sql="update inv_transaction b set b.prod_id=".$id_string_trans." where b.id in(select b.id from inv_receive_master a where a.id=b.mst_id and a.entry_form=20 
and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".substr($previds,0,-1).") and b.transaction_type=1 and b.item_category=4)";

	$trans_sql2="update inv_transaction b set b.prod_id=".$id_string_trans." where b.id in(select b.id from inv_issue_master a where a.id=b.mst_id and a.entry_form in (21,26)
and b.item_category=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.prod_id in(".substr($previds,0,-1).") and b.transaction_type in (2,3) and b.item_category=4)";

	$purc_sql="update inv_purchase_requisition_dtls set product_id=".$id_string_prod." where product_id in(".substr($previds,0,-1).")";
	$nonOrder_sql="update wo_non_order_info_dtls set item_id=".$id_string_nonOrder." where item_id in(".substr($previds,0,-1).")";
	$mrr_sql="update inv_mrr_wise_issue_details set prod_id=".$id_string_mrr." where prod_id in(".substr($previds,0,-1).") and entry_form=21";
	//$serial_sql="update inv_serial_no_details set product_id=".$id_string_prod." where product_id in(".substr($previds,0,-1).")";$serial=execute_query($serial_sql,0);

	$both_ids=$previds.substr($newids,0,-1);
	
	$sql_trans=sql_select("SELECT prod_id, (sum(case when transaction_type in(1,4) then cons_quantity else 0 end) - sum(case when transaction_type in(2,3) then cons_quantity else 0 end)) as current_stock from inv_transaction where status_active=1 and is_deleted=0 and prod_id in(".$both_ids.") and item_category=4 group by prod_id");
	$update_field="current_stock";
	foreach($sql_trans as $row)
	{
		$update_id_arr[]=$row[csf('prod_id')];
		$update_data_arr[$row[csf('prod_id')]]=explode("*",($row[csf('current_stock')]));
	}
	$upsubDtlsID=bulk_update_sql_statement("product_details_master","id",$update_field,$update_data_arr,$update_id_arr);
	
	$rID=sql_multirow_update("product_details_master","entry_form","24","id",implode(",",$only_trims_arr),0);
	$rID2=sql_multirow_update("product_details_master","entry_form","20","id",implode(",",$only_gi_arr),0);
	$rID3=execute_query($sql_insert,0);
	
	$inv_trans=execute_query($trans_sql,0);
	$inv_trans2=execute_query($trans_sql2,0);
	$inv_purc=execute_query($purc_sql,0);
	$non_order=execute_query($nonOrder_sql,0);
	$mrr=execute_query($mrr_sql,0);
	$rID4=execute_query($upsubDtlsID);
	
	if($rID && $rID2 && $rID3 && $inv_trans && $inv_trans2 && $inv_purc && $non_order && $mrr && $rID4)
	{
		oci_commit($con); 
		echo "Success";
	}
	else
	{
		oci_rollback($con); 
		echo "Failed";
	}
}


?>
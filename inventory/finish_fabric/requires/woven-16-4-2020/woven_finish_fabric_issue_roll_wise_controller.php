<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_store")
{	
	$data=explode("***",$data);
	echo create_drop_down( "cbo_store_name", 152, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0  
and company_id='$data[0]'  and (item_category_id  like '%,2,%' or item_category_id='2')","id,store_name", 1, "--- Select Store ---", 1, "" );
	exit();
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_dyeing_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where  b.party_type in(9,21,24) and a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 152, $blank_array,"",1, "-- Select --", 0, "" );
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	for($j=1;$j<=$tot_row;$j++)
	{ 	
			$productId="productId_".$j;  
			$prod_ids .= $$productId.",";
	}
	$prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,","))));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$issue_date = date("Y-m-d", strtotime(str_replace("'","",$txt_issue_date)));
	if ($issue_date < $max_recv_date) 
    {
         echo "20**Issue Date Can not Be Less Than Last Receive Date Of These Lot";
         die;
	}
	
	if ($operation==0)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FFRI', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=195 and $year_cond=".date('Y',time())." order by id desc ", "issue_number_prefix","issue_number_prefix_num"));
		//$id=return_next_id( "id", "inv_issue_master", 1 ) ;
		
		$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'FFRI',195,date("Y",time())));
				 
		$field_array="id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_purpose,entry_form,item_category,company_id,batch_no,issue_date,knit_dye_source, knit_dye_company,req_no,store_id,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_issue_purpose.",195,3,".$cbo_company_id.",".$txt_batch_id.",".$txt_issue_date.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",'".str_replace("'","",$txt_rqn_no)."',".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		
		$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity, cons_rate,cons_amount, inserted_by,insert_date";
		
		//$dtls_id=return_next_id("id", "inv_finish_fabric_issue_dtls", 1);
		
		$field_array_dtls="id,mst_id,trans_id,basis,prod_id,body_part_id,gmt_item_id,order_id,issue_qnty,inserted_by,insert_date";		
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty,rate,amount, roll_no, roll_id,reprocess,prev_reprocess, inserted_by, insert_date,is_sales";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
				
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id,color_id,quantity, inserted_by, insert_date,is_sales";
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );		 
		
		$barcodeNos=''; $all_prod_id='';
		for($j=1;$j<=$tot_row;$j++)
		{
			$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$dtls_id=return_next_id_by_sequence( "INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con) ;
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			 	
			$recvBasis="recvBasis_".$j;
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$colorId="colorId_".$j;
			$batchId="batchId_".$j;
			$rollNo="rollNo_".$j;
			$rollRate="rollRate_".$j;
			$body_part="bodyPartId_".$j;
			$garments_item_id="gmtItemId_".$j;
			$reProcess="reProcess_".$j;
			$IsSalesId = "IsSalesId_".$j;
			
			if(str_replace("'","",$cbo_issue_purpose)==44) $reprocess_id=$$reProcess+1;
			else $reprocess_id=$$reProcess;
			
			$amount=$$rollRate*$$rollWgt;
			
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$batchId."',".$cbo_company_id.",'".$$productId."',3,2,".$txt_issue_date.",'".$$rollWgt."','".$$rollRate."','".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$transactionID.",'".$$recvBasis."','".$$productId."','".$$body_part."','".$$garments_item_id."','".$$orderId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",'".$$orderId."',195,'".$$rollWgt."','".$$rollWgt."','".$$rollRate."','".$amount."','".$$rollNo."','".$$rollId."',".$reprocess_id.",".$$reProcess.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "')";
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$transactionID.",2,195,'".$dtls_id."','".$$orderId."','".$$productId."','".$$colorId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "')";

			$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$transactionID."__".$id_roll.",";
			$prodData_array[$$productId]+=$$rollWgt;
			$prodData_amoutArray[$$productId]+=$amount;
			$all_prod_id.=$$productId.",";
			//$id_roll = $id_roll+1;
			//$transactionID = $transactionID+1;
			//$dtls_id = $dtls_id+1;
			//$id_prop = $id_prop+1;
		}
		//echo $data_array_roll;
		
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]-$issue_qty;
			$stock_value=$row[csf('stock_value')]-$prodData_amoutArray[$row[csf('id')]];
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		
		//echo "10**insert into inv_finish_fabric_issue_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
 		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rID5=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
		//echo"10**". bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
	   
	   //echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$prodUpdate;die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="issue_purpose*batch_no*issue_date*knit_dye_source*store_id*knit_dye_company*req_no*updated_by*update_date";
		$data_array=$cbo_issue_purpose."*".$txt_batch_id."*".$txt_issue_date."*".$cbo_dyeing_source."*".str_replace("'","",$cbo_store_name)."*".$cbo_dyeing_company."*'".str_replace("'","",$txt_rqn_no)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity,cons_rate,cons_amount,inserted_by,insert_date";
		$field_array_updatetrans="transaction_date*cons_quantity*cons_rate*cons_amount*updated_by*update_date";
		
		//$dtls_id=return_next_id("id", "inv_finish_fabric_issue_dtls", 1);

		$field_array_dtls="id,mst_id,trans_id,basis,prod_id,body_part_id,gmt_item_id,order_id,issue_qnty,inserted_by,insert_date";	
		$field_array_updatedtls="issue_qnty*body_part_id*gmt_item_id*updated_by*update_date";		
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty,rate,amount, roll_no, roll_id,reprocess,prev_reprocess, inserted_by, insert_date,is_sales";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_updateroll="qnty*qc_pass_qnty*rate*amount*reprocess*prev_reprocess*updated_by*update_date";
		
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id,color_id,quantity, inserted_by, insert_date,is_sales";
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );

		$barcodeNos=''; $all_prod_id=''; $all_roll_id=''; $all_barcode_no='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			
			$recvBasis="recvBasis_".$j;
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$colorId="colorId_".$j;
			$batchId="batchId_".$j;
			$rollNo="rollNo_".$j;
			$body_part="bodyPartId_".$j;
			$garments_item_id="gmtItemId_".$j;
			$dtlsId="dtlsId_".$j;
			$transId="transId_".$j;
			$rolltableId="rolltableId_".$j;
			$rollRate="rollRate_".$j;
			$reProcess="reProcess_".$j;
			$preRerocess="preRerocess_".$j;
			$IsSalesId = "IsSalesId_".$j;
			
			if(str_replace("'","",$cbo_issue_purpose)==44) $reprocess_id=$$preRerocess+1;
			else $reprocess_id=$$preRerocess;
			
			$amount=$$rollRate*$$rollWgt;
			
			if($$rolltableId>0)
			{
				$transId_arr[]=$$transId;
				$data_array_update_trans[$$transId]=explode("*",($txt_issue_date."*'".$$rollWgt."'*'".$$rollRate."'*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$dtlsId_arr[]=$$dtlsId;
				$data_array_update_dtls[$$dtlsId]=explode("*",($$rollWgt."*'".$$body_part."'*'".$$garments_item_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$rollId_arr[]=$$rolltableId;
				$data_array_update_roll[$$rolltableId]=explode("*",("'".$$rollWgt."'*'".$$rollWgt."'*'".$$rollRate."'*'".$amount."'*'".$reprocess_id."'*'".$$preRerocess."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$barcodeNos.=$$barcodeNo."__".$$dtlsId."__".$$transId."__".$$rolltableId.",";
				$dtlsId_prop=$$dtlsId;
				$transId_prop=$$transId;
				$all_roll_id.=$$rolltableId.",";
			}
			else
			{
				
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$dtls_id = return_next_id_by_sequence("INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con);
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				
				
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$batchId."',".$cbo_company_id.",'".$$productId."',3,2,".$txt_issue_date.",'".$$rollWgt."','".$$rollRate."','".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$transactionID.",'".$$recvBasis."','".$$productId."','".$$body_part."','".$$garments_item_id."','".$$orderId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",'".$$orderId."',195,'".$$rollWgt."','".$$rollWgt."','".$$rollRate."','".$amount."','".$$rollNo."','".$$rollId."','".$reprocess_id."','".$$preRerocess."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "')";
			
				
				$dtlsId_prop=$dtls_id;
				$transId_prop=$transactionID;
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$transactionID."__".$id_roll.",";
				//$id_roll = $id_roll+1;
				//$transactionID = $transactionID+1;
				//$dtls_id = $dtls_id+1;
				
				
			}
			
			$all_barcode_no.=$$barcodeNo.",";
			
			$prodData_array[$$productId]+=$$rollWgt;
			$prodData_amoutArray[$$productId]+=$amount;
			$all_prod_id.=$$productId.",";
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$transId_prop.",2,195,'".$dtlsId_prop."','".$$orderId."','".$$productId."','".$$colorId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "')";
			//$id_prop = $id_prop+1;
		}
		
		$txt_deleted_id=str_replace("'","",$txt_deleted_id); $adj_prod_array=array(); $update_dtls_id=''; $update_trans_id=''; $update_delete_dtls_id='';
		if($txt_deleted_id!="") $all_roll_id=$all_roll_id.$txt_deleted_id; else $all_roll_id=substr($all_roll_id,0,-1);
		$deleted_id_arr=explode(",",$txt_deleted_id);
		$all_barcode_no=chop($all_barcode_no,",");
		if($all_barcode_no!="")
		{
			$rollData=sql_select("select a.id, a.qnty,a.rate, b.id as dtls_id, b.trans_id, b.prod_id from pro_roll_details a, inv_finish_fabric_issue_dtls b where a.dtls_id=b.id and a.barcode_no in($all_barcode_no) and a.entry_form=195");
			foreach($rollData as $row)
			{
				$adj_prod_array[$row[csf('prod_id')]]+=$row[csf('qnty')];
				$prodData_amoutArray[$row[csf('prod_id')]]-=$row[csf('rate')]*$row[csf('qnty')];
				$all_prod_id.=$row[csf('prod_id')].",";
				$update_dtls_id.=$row[csf('dtls_id')].",";
			}
		}
		
		//echo $all_prod_id;die;
		
		
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			if(str_replace("'","",$prodData_array[$row[csf('id')]])=="") $prodData_array[$row[csf('id')]]=0;
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]+$adj_prod_array[$row[csf('id')]]-$issue_qty;
			$stock_value=$row[csf('stock_value')]-$prodData_amoutArray[$row[csf('id')]];
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		//echo "10**";
		//print_r($data_array_prod_update);die;
		//echo "10**delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=195";die;
		$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$update_id,0);
		$rID2=true; $rID3=true; $rID4=true; $rID5=true; $rID6=true; $rID7=true; $statusChangeTrans=true; $statusChangeDtls=true; $statusChangeRoll=true;
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			$rID3=sql_insert("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}
		//echo "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr );die;
		//echo "10**insert into order_wise_pro_details (".$field_array_prop.") values ".$data_array_prop;die;
		if(count($data_array_update_dtls)>0)
		{
			$rID5=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_updatetrans, $data_array_update_trans, $transId_arr ));
			$rID6=execute_query(bulk_update_sql_statement( "inv_finish_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
			$rID7=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr ));
		}
		
		$update_trans_id=$update_delete_dtls_id="";

		if($txt_deleted_id!="")
		{
			$rollDelete=sql_select("select b.id as dtls_id, b.trans_id  from pro_roll_details a, inv_finish_fabric_issue_dtls b where a.dtls_id=b.id and a.id in($txt_deleted_id) and a.entry_form=195");
			foreach($rollDelete as $row)
			{
				$update_trans_id.=$row[csf('trans_id')].",";
				$update_delete_dtls_id.=$row[csf('dtls_id')].",";
			}
			
			$update_trans_id=substr($update_trans_id,0,-1);
			$update_delete_dtls_id=substr($update_delete_dtls_id,0,-1);
			
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$update_trans_id,0);
			$statusChangeDtls=sql_multirow_update("inv_finish_fabric_issue_dtls",$field_array_status,$data_array_status,"id",$update_delete_dtls_id,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}
		//echo "10**delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=195";die;
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=195",0);
		$rID8=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
		//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));

		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$rID7."&&".$rID8."&&".$delete_prop."&&".$prodUpdate."&&".$statusChangeTrans."&&".$statusChangeDtls."&&".$statusChangeRoll; die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $delete_prop && $prodUpdate && $statusChangeTrans && $statusChangeDtls && $statusChangeRoll)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_issue_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $delete_prop && $prodUpdate && $statusChangeTrans && $statusChangeDtls && $statusChangeRoll)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_issue_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="issue_popup")
{
	echo load_html_head_contents("Issue Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(id)
		{
			$('#hidden_system_id').val(id);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Issue Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Issue No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Issue No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_issue_search_list_view', 'search_div', 'woven_finish_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_issue_search_list_view") 
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	$sql_batch_data=sql_select("SELECT a.id, b.batch_id,c.barcode_no,c.reprocess,c.prev_reprocess  FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0");
	
	// 7=>"Finish Fabric Production Entry"
	//37=>"Finish Fabric Receive Entry"
	// 68=>"Finish Fabric Roll Receive By Store"
	
	$batch_barcode_arr=array();
	foreach ($sql_batch_data as $val)
	{
		$batch_barcode_arr[$val[csf('barcode_no')]][$val[csf('prev_reprocess')]]=$val[csf('batch_id')];
	}
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and a.issue_number like '$search_string'";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	//if($db_type==0) $batch_field="group_concat(b.sub_process_id)  as sub_process_id ";
	$sql = "select a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_date, a.batch_no, a.issue_purpose,b.barcode_no,b.prev_reprocess from inv_issue_master a,pro_roll_details b where a.id=b.mst_id and a.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond order by a.id"; 
	//echo $sql;die;
	$result = sql_select($sql);
	$issue_challan_arr=array();
	foreach ($result as $val)
	{
		$issue_challan_arr[$val[csf('id')]]['year']									=$val[csf('year')];
		$issue_challan_arr[$val[csf('id')]]['issue_number_prefix_num']				=$val[csf('issue_number_prefix_num')];
		$issue_challan_arr[$val[csf('id')]]['issue_number']							=$val[csf('issue_number')];
		$issue_challan_arr[$val[csf('id')]]['knit_dye_source']						=$val[csf('knit_dye_source')];
		$issue_challan_arr[$val[csf('id')]]['knit_dye_company']						=$val[csf('knit_dye_company')];
		$issue_challan_arr[$val[csf('id')]]['issue_date']							=$val[csf('issue_date')];
		$issue_challan_arr[$val[csf('id')]]['issue_purpose']						=$val[csf('issue_purpose')];
		$issue_challan_batch_arr[$val[csf('id')]][$batch_barcode_arr[$val[csf('barcode_no')]][$val[csf('prev_reprocess')]]]=$batch_arr[$batch_barcode_arr[$val[csf('barcode_no')]][$val[csf('prev_reprocess')]]];
		//echo $batch_barcode_arr[$val[csf('barcode_no')]][$val[csf('prev_reprocess')]];
		/*
		$issue_challan_arr[$val[csf('id')]]['knit_dye_source']=$val[csf('knit_dye_source')];
		$issue_challan_arr[$val[csf('id')]]['knit_dye_company']=$val[csf('knit_dye_company')];
		$issue_challan_arr[$val[csf('id')]]['issue_date']=$val[csf('issue_date')];
		$issue_challan_arr[$val[csf('id')]]['issue_purpose']=$val[csf('issue_purpose')];*/
		
	}
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Issue No</th>
            <th width="60">Year</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th width="110">Issue Purpose</th>
            <th width="140">Batch</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:760px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($issue_challan_arr as $issue_id=>$issue_data)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				 
				$dye_comp="&nbsp;";
                if($issue_data['knit_dye_source']==1)
					$dye_comp=$company_arr[$issue_data['knit_dye_company']]; 
				else
					$dye_comp=$supllier_arr[$row['knit_dye_company']];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $issue_id; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $issue_data['issue_number_prefix_num']; ?></p></td>
                    <td width="60" align="center"><p><? echo $issue_data['year']; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$issue_data['knit_dye_source']]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $yarn_issue_purpose[$issue_data['issue_purpose']]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo implode(",",$issue_challan_batch_arr[$issue_id]); ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($issue_data['issue_date']); ?></td>
                </tr>
        	<?
            $i++;
            }
        	?>
        </table>
    </div>
<?	
exit();
}

if($action=="populate_data_for_validation")
{
	$exp_data=explode(",",$data);
	$repprocess_barcode_arr=array();
	foreach($exp_data as $barcode_data)
	{
		$barcode_info=explode("_",$barcode_data);
		foreach($barcode_info as $barcode_vaue)
		{
			$barcodeNos.=$barcode_vaue[0].",";
			$repprocess_barcode_arr[$barcode_vaue[0]]=$barcode_vaue[1];
		}
		
	}
	$barcodeNos=chop($barcodeNos,",");
	$sql = "select barcode_no,reprocess from pro_roll_details where barcode_no in($barcodeNos) and entry_form=67 and status_active=1 and is_deleted=0";
	//echo $sql; // 67=>"Finish Fabric Roll Delevery To Store"
	$res = sql_select($sql);
	$flag=0;	
	foreach($res as $row)
	{	
		if($repprocess_barcode_arr[$row[csf("barcode_no")]]==$row[csf("reprocess")])
		{
			$flag=1;
		}
  	}
	
	if($flag==1) echo "$('#cbo_issue_purpose').attr('disabled','true')".";\n";
	exit();	
}

if($action=="populate_data_from_data")
{
	$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no,req_no, issue_purpose from inv_issue_master where id=$data and entry_form=195 and status_active=1 and is_deleted=0";
	//echo $sql;
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_issue_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_issue_purpose').val(".$row[csf("issue_purpose")].");\n";
		echo "$('#txt_rqn_no').val('".$row[csf("req_no")]."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#cbo_dyeing_source').val(".$row[csf("knit_dye_source")].");\n";
		echo "load_drop_down( 'requires/woven_finish_fabric_issue_roll_wise_controller', ".$row[csf("knit_dye_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_dyeing_company').val(".$row[csf("knit_dye_company")].");\n";
		
		$batchno = return_field_value("batch_no","pro_batch_create_mst","id='".$row[csf("batch_no")]."'");
		echo "$('#txt_batch_no').val('".$batchno."');\n";	
		echo "$('#txt_batch_id').val(".$row[csf("batch_no")].");\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
  	}
	exit();	
}

if($action=="barcode_nos")
{
	if($db_type==0) 
	{
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","pro_roll_details","entry_form=195 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2) 
	{
		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","pro_roll_details","entry_form=195 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	echo $barcode_nos;
	exit();	
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
	?> 

	<script>
	
		var selected_id = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}

        function check_batch(value){
            var searchBy= $("#cbo_search_by").val();
            if( searchBy == 2){
                var batch_id=return_global_ajax_value( value+"**"+<? echo $company_id; ?>, 'check_batch_no', '', 'woven_finish_fabric_issue_roll_wise_controller');  
               var phpbatchid = "<? echo $batch_id; ?>";
                batch_id= parseInt(batch_id);
                if(batch_id != 0){
                      
                      if(phpbatchid != ""){
                          phpbatchid= parseInt(phpbatchid)
                            if(batch_id != phpbatchid ){
                                alert("Batch differs from previous one");
                                $("#txt_search_common").val("");
                                $("#hidden_batch_id").val("");
                            }else{
                                $("#hidden_batch_id").val(batch_id);
                            }
                        }
                        $("#hidden_batch_id").val(batch_id);

                }else{
                    alert("Batch No Found");
                     $("#txt_search_common").val("");
                     $("#hidden_batch_id").val("");
                }
            }
        }
               
	    function change_search_event_barPopup( mst_type, field_type, qry_array, path ) 
	    {
            var fld = document.getElementById('cbo_search_by');
            var fld_data  =fld.options[fld.selectedIndex].text;		
            var msg_text="";
            field_type=field_type.split('*');
            qry_array=qry_array.split('*');
            var cntrl_type= field_type[mst_type*1-1];//qry_array[mst_type*1-1];
            if (cntrl_type==0)	msg_text="Please Enter "+fld_data; else  msg_text="Select "+fld_data;

            document.getElementById('search_by_td_up').innerHTML=msg_text;
            //alert(cntrl_type);
            if (cntrl_type==0)
                    document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="text_boxes" onchange="check_batch(this.value)"	id="txt_search_common"/>';
            else if (cntrl_type==1) // Drop Down query
                    document.getElementById('search_by_td').innerHTML=return_global_ajax_value(qry_array[mst_type*1-1], "search_by_drop_down",path);
            else if (cntrl_type==2) // Drop Down array
                    document.getElementById('search_by_td').innerHTML=return_global_ajax_value(qry_array[mst_type*1-1], "search_by_drop_down_from_array",path);	
            else  
                    document.getElementById('search_by_td').innerHTML='<input	type="text"	name="txt_search_common" style="width:130px " class="datepicker" onfocus="datepicker_()"	id="txt_search_common"/>';	
	    }
	
    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Order No</th>
                    <th>Barcode No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>'Order No', 2=>'Batch No', 3=>'Sales Order No');
							$dd="change_search_event_barPopup(this.value, '0*0*0*0', '0*0*0*0', '') ";							
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>                   
                    </td> 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onChange="check_batch(this.value)"/>	
                        
                    </td> 			
                    <td><input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" /></td>    			
            		<td align="center">
                        <input type="hidden" class="text_boxes" id="hidden_batch_id" name="hidden_batch_id">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company_id; ?>+'_'+'<? echo $batch_id; ?>'+'_'+document.getElementById('barcode_no').value+'_'+document.getElementById('hidden_batch_id').value, 'create_barcode_search_list_view', 'search_div', 'woven_woven_finish_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);	    
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$barcode_no =trim($data[4]);
	$batch_id=trim($data[3]);
    $search_batchNo =trim($data[5]);
	$search_field_cond="";
    $batch_cond="";
	if(trim($data[0])!="")
	{
            if($search_by==1) $search_field_cond="and d.po_number like '$search_string'";
            else if($search_by==2) 
            {
                 if($batch_id!=""){
                    if( $search_batchNo != $batch_id){
                     
                        return;
                    }
                }
                $search_field_cond=" and b.batch_id=$search_batchNo ";
            }else if($search_by==3) 
            {
                 $search_field_cond="and d.job_no like '$search_string'";
            }
        
    }else {
        if($batch_id!="") $batch_cond=" and b.batch_id=$batch_id ";
    }

	if($barcode_no!="")
	{
		$barcode_cond="and c.barcode_no='$barcode_no'";
	}
	
	$scanned_barcode_arr=array();

	$barcodeData=sql_select( "select barcode_no,prev_reprocess from pro_roll_details where entry_form=195 and status_active=1 and is_deleted=0");

	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]][$row[csf('prev_reprocess')]]=$row[csf('barcode_no')];
	}	

	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=3",'id','product_name_details');

	$job_arr=array();
	$sql_job=sql_select("select a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.booking_type in(1,4) group by b.job_no,b.booking_no,a.buyer_id,b.po_break_down_id,c.po_number,c.shipment_date");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_mst')];
		$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 			= $job_row[csf('buyer_id')];		
		$job_arr[$job_row[csf('booking_no')]]["po_number"] 			= $job_row[csf('po_number')];
				
		$job_arr[$job_row[csf('po_break_down_id')]]["job_no_mst"] 	= $job_row[csf('job_no_mst')];
		$job_arr[$job_row[csf('po_break_down_id')]]["buyer_id"] 	= $job_row[csf('buyer_id')];
		$job_arr[$job_row[csf('po_break_down_id')]]["po_number"] 	= $job_row[csf('po_number')];
		$job_arr[$job_row[csf('po_break_down_id')]]["shipment_date"]= $job_row[csf('shipment_date')];
	}
	
	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,buyer_id,sales_booking_no,delivery_date from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["job_no_mst"] 		= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["buyer_id"] 			= $sales_row[csf('buyer_id')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
		$sales_arr[$sales_row[csf('id')]]["delivery_date"] 		= $sales_row[csf('delivery_date')];
	}

	if($search_by == 3){
		$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.reprocess,c.is_sales,c.po_breakdown_id,d.job_no FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, fabric_sales_order_mst d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond $batch_cond
		";
		
		/*$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.reprocess,c.is_sales,c.po_breakdown_id,d.job_no FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, fabric_sales_order_mst d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond $batch_cond
		union all
		select a.transfer_system_id as recv_number,b.from_prod_id as prod_id,c.barcode_no, c.roll_no, c.qnty,c.reprocess,c.is_sales,c.po_breakdown_id,d.job_no
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , fabric_sales_order_mst d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id  and a.entry_form in(134) and c.entry_form in(134) and c.status_active=1 and c.is_deleted=0  $barcode_cond $search_field_cond $batch_cond
		order by barcode_no";*/
	}else{
		$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.reprocess,c.is_sales,c.po_breakdown_id FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond $batch_cond";
		
		/*echo $sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.reprocess,c.is_sales,c.po_breakdown_id FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(17,7,68) and c.entry_form in(17,7,68) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond $batch_cond
		union all
		select a.transfer_system_id as recv_number,b.from_prod_id as prod_id,c.barcode_no, c.roll_no, c.qnty,c.reprocess,c.is_sales,c.po_breakdown_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id  and a.entry_form in(134) and c.entry_form in(134) and c.status_active=1 and c.is_deleted=0 $search_field_cond $barcode_cond $batch_cond
		order by barcode_no";*/
	}
	// 68=>"Finish Fabric Roll Receive By Store"
	//	7=>"Finish Fabric Production Entry"
	// 195=>"Woven Finish Fabric Roll Issue"
	
	
	// 134=>"Roll wise Finish Fabric Order To Order Transfer Entry"
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order/FSO No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:750px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {
				if($scanned_barcode_arr[$row[csf('barcode_no')]][$row[csf('reprocess')]]=="")
				{
					$is_sales = $row[csf('is_sales')];
					if($search_by == 3){
						$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
	            		if($within_group == 1){
	            			$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
	            			$job_no 			= $job_arr[$sales_booking_no]["job_no_mst"];		            			
	            		}else{
	            			$job_no 			= "";
	            		}
	            		$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
	            		$shipment 	= change_date_format($sales_arr[$row[csf('po_breakdown_id')]]["delivery_date"]);
		            }else{
		            	if($is_sales == 1){
		            		$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
		            		if($within_group == 1){
		            			$sales_booking_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
		            			$job_no 	= $job_arr[$sales_booking_no]["job_no_mst"];		            			
		            		}else{
		            			$job_no 	= "";
		            		}
		            		$order_no 	= $sales_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
		            		$shipment 	= change_date_format($job_arr[$row[csf('po_breakdown_id')]]["shipment_date"]);          		
		            	}else{
		            		$job_no 	= $job_arr[$row[csf('po_breakdown_id')]]["job_no_mst"];
		            		$order_no 	= $job_arr[$row[csf('po_breakdown_id')]]["po_number"];
		            		$shipment 	= change_date_format($job_arr[$row[csf('po_breakdown_id')]]["shipment_date"]);          		
		            	}
		            }

					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $job_no; ?></p></td>
						<td width="110"><p><? echo $order_no; ?></p></td>
						<td width="80" align="center"><? echo $shipment; ?></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
exit();
}



if($action=="reqsn_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
?> 
	<script>
		var selected_id = new Array();
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			$('#hidden_barcode_nos').val( id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Order No</th>
                    <th>Barcode No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td> 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 			
                    <td><input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" /></td>    			
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'woven_finish_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="populate_barcode_data")
{
	$barcodeData=''; 
	$po_ids_arr=array(); 
	$po_details_array=array(); 
	$barcodeDataArr=array(); 
	$barcodeBuyerArr=array(); 
	$transRollIds=''; 
	$transPoIdsArr=array();
	
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	
	$scanned_barcode_sql=sql_select("select barcode_no,prev_reprocess from pro_roll_details where entry_form=195 and is_returned!=1 and barcode_no in( $data ) and status_active=1 and is_deleted=0");
	foreach($scanned_barcode_sql as $row)
	{
		 $scanned_barcode_data[$row[csf("barcode_no")]][$row[csf("prev_reprocess")]]=$row[csf("barcode_no")] ;
	}
	unset( $scanned_barcode_sql);
	
	$jsscanned_barcode_array= json_encode($scanned_barcode_array);
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no,a.gmts_item_id, a.insert_date, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.garments_nature=3 and a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
	}
	
	$without_order_buyer=return_library_array( "select c.barcode_no, a.buyer_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=17 and c.entry_form=17 and a.booking_without_order=1 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)","barcode_no","buyer_id");
	
	$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no,c.qnty, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales,max(c.reprocess) as  reprocess FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data) group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no,c.qnty,c.po_breakdown_id, c.qc_pass_qnty, c.roll_id,c.rate, c.booking_no, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales");
	
	// 17,7,68
	$is_sales_arr = array();
	foreach($data_array as $row)
	{
		if($scanned_barcode_data[$row[csf("barcode_no")]][$row[csf("reprocess")]]=="")
		{
			$is_sales_arr[$row[csf('barcode_no')]] = $row[csf("is_sales")];
			$booking_no_id = $row[csf('po_breakdown_id')];
			
			$receive_basis=$receive_basis_arr[$row[csf("receive_basis")]];
			$receive_basis_id=$row[csf("receive_basis")];

			$rate=$row[csf("rate")];
			if($row[csf("entry_form")]==68) // 68=>"Finish Fabric Roll Receive By Store"
			{
				$roll_id=$row[csf("roll_id_prev")];
				$row[csf("booking_no")]=$row[csf("recv_number")];
				$row[csf("booking_id")]=$row[csf("id")];
			}
			else
			{
				$roll_id=$row[csf("roll_id")];
			}
			
			$buyer_id='';
			if($row[csf("booking_without_order")]==1)
			{
				$buyer_id=$without_order_buyer[$row[csf("barcode_no")]];
			}
			else
			{
				if ($row[csf("is_sales")] == 1) {
					$is_salesOrder = 1;
					// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
					//echo $salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "ID='$booking_no_id'");
					$within_group = return_field_value("WITHIN_GROUP", "FABRIC_SALES_ORDER_MST", "ID='$booking_no_id'");
					$sales_order_no = $booking_no_id;
				}else{
					$is_salesOrder = 0;
					$po_ids_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
				}
			}
			
			/*$is_transfer=0;
			if($row[csf("is_transfer")]==6 && $row[csf("transfer_criteria")]==4 && $row[csf("entry_form")]==68)
			{
				$transRollIds.=$row[csf("roll_id_prev")].",";
				$is_transfer=1;
			}
			elseif($row[csf("is_transfer")]==6 && $row[csf("transfer_criteria")]==4 && $row[csf("entry_form")]!=68)
			{
				$transRollIds.=$row[csf("roll_id")].",";
				$is_transfer=1;
			}*/
			
			$color='';
			$color_id=explode(",",$row[csf('color_id')]);
			foreach($color_id as $val)
			{
				if($val>0) $color.=$color_arr[$val].",";
			}
			
			$gmts_item_id = $po_details_array[$row[csf("po_breakdown_id")]]['gmts_item_id'];
			
			$color=chop($color,',');
			$barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("barcode_no")]."**".$row[csf("company_id")]."**".$row[csf("roll_no")]."**".$roll_id."**".$row[csf("body_part_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("color_id")]."**".$row[csf("batch_id")]."**".$batch_name_array[$row[csf("batch_id")]]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("width")]."**".number_format($row[csf("qnty")],2,'.','')."**".$rate."**".$row[csf("booking_without_order")]."**".$row[csf("reprocess")]."**".$gmts_item_id;
			$barcodeBuyerArr[$row[csf('barcode_no')]]=$row[csf("booking_without_order")]."__".$row[csf("po_breakdown_id")]."__".$is_transfer."__".$buyer_id;
		}
	}
	
	if(count($barcodeDataArr)<1)
	{
		echo "99";
		die;
	}
	
	/*$transRollIds=chop($transRollIds,',');
	if($transRollIds!="")
	{
		$transPoIds=sql_select("select barcode_no, po_breakdown_id from pro_roll_details where entry_form=134 and status_active=1 and is_deleted=0 and re_transfer=0 and roll_id in($transRollIds) and re_transfer=0"); // 134=>"Roll wise Finish Fabric Order To Order Transfer Entry"
		foreach($transPoIds as $rowP)
		{
			$transPoIdsArr[$rowP[csf("barcode_no")]]=$rowP[csf("po_breakdown_id")];
			$po_ids_arr[$rowP[csf("po_breakdown_id")]]=$rowP[csf("po_breakdown_id")];
		}
	}*/
	
	if(count($po_ids_arr)>0)
	{
		if ($within_group == 1) {
			$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in(".implode(",",$po_ids_arr).")");
			$po_details_array=array();
			foreach($data_array as $row)
			{
				if ($is_salesOrder == 1) {
					$po_details_array[$row[csf("po_id")]]['po_number'] = $sales_order_no;
				} else {
					$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
					$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
					$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
				}
			}
		}
	}

	$job_arr=array();
	$sql_job=sql_select("select b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date from wo_booking_dtls b,wo_po_break_down c, wo_po_details_master e where b.po_break_down_id=c.id and c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_type in(1,4) group by b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
		$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
	}

	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
	}

	if(count($barcodeDataArr)>0)
	{
		foreach($barcodeDataArr as $barcode_no=>$value)
		{
			$barcodeDatas=explode("__",$barcodeBuyerArr[$barcode_no]);
			$booking_without_order=$barcodeDatas[0];
			$is_transfer=$barcodeDatas[2];
			
			if($is_transfer==1) 
			{
				$po_id=$transPoIdsArr[$barcode_no];
			}
			else
			{
				$po_id=$barcodeDatas[1];
			}

			if($is_salesOrder == 1){
				$sales_booking_no 	= $sales_arr[$po_id]["sales_booking_no"];
				$within_group 		= $sales_arr[$po_id]["within_group"];
				if($within_group==1){
					$po_no 				= $sales_arr[$po_id]["sales_order_no"];
		        	$job_no 			= $job_arr[$sales_booking_no]["job_no_mst"];
		        	$buyer_id 			= $job_arr[$sales_booking_no]["buyer_name"];
	        	}else{
	        		$po_no 				= $sales_arr[$po_id]["sales_order_no"];
		        	$job_no 			= "";
		        	$buyer_id 			= $job_arr[$sales_booking_no]["buyer_name"];
	        	}
			}else{
				if($booking_without_order==1) 
				{
					$buyer_id=$barcodeDatas[3];
					$po_no='';
					$job_no='';
				}
				else
				{
					$buyer_id=$po_details_array[$po_id]['buyer_name'];
					$po_no=$po_details_array[$po_id]['po_number'];
					$job_no=$po_details_array[$po_id]['job_no'];
				}
			}
			
			if($po_id=='') { $po_id=0; }
			$is_sales = $is_sales_arr[$barcode_no];
			
			$barcodeData.=$value."**".$po_id."**".$buyer_id."**".$po_no."**".$job_no . "**" . $is_sales ."_";
		}
		echo substr($barcodeData,0,-1);
	}
	else
	{
		echo "0";
	}
	
	exit();	
}

if($action=="populate_barcode_data_update")
{
	$barcodeData=''; 
	$po_ids_arr=array(); 
	$po_details_array=array(); 
	$barcodeDataArr=array(); 
	$barcodeBuyerArr=array(); 
	$transRollIds=''; 
	$transPoIdsArr=array();
	
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$issued_data_arr=array(); $barcode_nos='';
	$issued_barcode_data=sql_select("select a.id, a.barcode_no, a.dtls_id, a.roll_id, a.rate, a.qnty, a.po_breakdown_id, a.booking_without_order, b.trans_id,a.reprocess,a.prev_reprocess from pro_roll_details a, inv_finish_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data");

	foreach($issued_barcode_data as $row)
	{
		$issued_data_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['po_id']=$row[csf('po_breakdown_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['id']=$row[csf('id')];
		$issued_data_arr[$row[csf('barcode_no')]]['roll_id']=$row[csf('roll_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['rate']=$row[csf('rate')];
		$issued_data_arr[$row[csf('barcode_no')]]['reprocess']=$row[csf('reprocess')];
		$issued_data_arr[$row[csf('barcode_no')]]['prev_reprocess']=$row[csf('prev_reprocess')];
		$issued_data_arr[$row[csf('barcode_no')]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		
		$barcode_nos.=$row[csf('barcode_no')].',';
		
		if($row[csf("booking_without_order")]!=1)
		{
			$po_ids_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		}
	}
	$barcode_nos=chop($barcode_nos,',');
	
	
	/*$scanned_barcode_sql=sql_select("select barcode_no,reprocess from pro_roll_details where entry_form=195 and is_returned!=1 and barcode_no in( $data ) and status_active=1 and is_deleted=0");
	foreach($scanned_barcode_sql as $row)
	{
		 $scanned_barcode_data[$row[csf("barcode_no")]][$row[csf("reprocess")]] ;
	}
	unset( $scanned_barcode_sql);
	
	$jsscanned_barcode_array= json_encode($scanned_barcode_array);*/
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.insert_date, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.garments_nature=3 and a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['gmts_item_id']=$row[csf("gmts_item_id")];
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
	}
	
	
	$without_order_buyer=return_library_array( "select c.barcode_no, a.buyer_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=17 and c.entry_form=17 and a.booking_without_order=1 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)","barcode_no","buyer_id");
	

	$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales,c.reprocess,c.prev_reprocess  FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)");
	
	// 7 = Finish Fabric Production Entry
	// 37 = Finish Fabric Receive Entry
	// 68 = Finish fabric Receive by Store
	
	$is_sales_arr = array();
	foreach($data_array as $row)
	{
		$is_sales_arr[$row[csf('barcode_no')]] = $row[csf("is_sales")];
		$booking_no_id = $row[csf('po_breakdown_id')];
		
		$receive_basis=$receive_basis_arr[$row[csf("receive_basis")]];
		$receive_basis_id=$row[csf("receive_basis")];

		$rate=$row[csf("rate")];
		if($row[csf("entry_form")]==68) // 68=>"Finish Fabric Roll Receive By Store"
		{
			$roll_id=$row[csf("roll_id_prev")];
			$row[csf("booking_no")]=$row[csf("recv_number")];
			$row[csf("booking_id")]=$row[csf("id")];
		}
		else
		{
			$roll_id=$row[csf("roll_id")];
		}
		
		$buyer_id='';
		if($row[csf("booking_without_order")]==1)
		{
			$buyer_id=$without_order_buyer[$row[csf("barcode_no")]];
		}
		else
		{
			if ($row[csf("is_sales")] == 1) {
				$is_salesOrder = 1;
				// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
				//echo $salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "ID='$booking_no_id'");
				$within_group = return_field_value("WITHIN_GROUP", "FABRIC_SALES_ORDER_MST", "ID='$booking_no_id'");
				$sales_order_no = $booking_no_id;
			}else{
				$is_salesOrder = 0;
				$po_ids_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
				
			}			
		}
		
		$is_transfer=0;
		if($row[csf("is_transfer")]==6 && $row[csf("transfer_criteria")]==4 && $row[csf("entry_form")]==68)
		{
			$transRollIds.=$row[csf("roll_id_prev")].",";
			$is_transfer=1;
		}
		elseif($row[csf("is_transfer")]==6 && $row[csf("transfer_criteria")]==4 && $row[csf("entry_form")]!=68)
		{
			$transRollIds.=$row[csf("roll_id")].",";
			$is_transfer=1;
		}
		
		$color='';
		$color_id=explode(",",$row[csf('color_id')]);
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');		
		
		$gmts_item_id = $po_details_array[$row[csf("po_breakdown_id")]]['gmts_item_id']; 
		
		$barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("barcode_no")]."**".$row[csf("company_id")]."**".$row[csf("roll_no")]."**".$roll_id."**".$row[csf("body_part_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("color_id")]."**".$row[csf("batch_id")]."**".$batch_name_array[$row[csf("batch_id")]]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("width")]."**".number_format($row[csf("qnty")],2,'.','')."**".$rate."**".$row[csf("booking_without_order")]."**".$row[csf("reprocess")]."**".$gmts_item_id;	
		
		$barcodeBuyerArr[$row[csf('barcode_no')]]=$row[csf("booking_without_order")]."__".$row[csf("po_breakdown_id")]."__".$is_transfer."__".$buyer_id;
	}
	
	if(count($barcodeDataArr)<1)
	{
		echo "99";
		die;
	}
	
	/*$transRollIds=chop($transRollIds,',');
	if($transRollIds!="")
	{
		$transPoIds=sql_select("select barcode_no, po_breakdown_id from pro_roll_details where entry_form=134 and status_active=1 and is_deleted=0  and roll_id in($transRollIds) and re_transfer=0"); // 134 =Roll wise Finish Fabric Order To Order Transfer Entry 
		foreach($transPoIds as $rowP)
		{
			$transPoIdsArr[$rowP[csf("barcode_no")]]=$rowP[csf("po_breakdown_id")];
			$po_ids_arr[$rowP[csf("po_breakdown_id")]]=$rowP[csf("po_breakdown_id")];
		}
	}
	
	if(count($po_ids_arr)>0)
	{
		if ($within_group == 1) {
			$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in(".implode(",",$po_ids_arr).")");
			$po_details_array=array();
			foreach($data_array as $row)
			{
				if ($is_salesOrder == 1) {
					$po_details_array[$row[csf("po_id")]]['po_number'] = $sales_order_no;
				} else {
					$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
					$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
					$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
				}
			}
		}
	}*/

	$job_arr=array();
	$sql_job=sql_select("select b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date from wo_booking_dtls b,wo_po_break_down c, wo_po_details_master e where b.po_break_down_id=c.id and c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_type in(1,4) group by b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
		$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
	}

	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
	}

	if(count($barcodeDataArr)>0)
	{
		foreach($barcodeDataArr as $barcode_no=>$value)
		{
			$barcodeDatas=explode("__",$barcodeBuyerArr[$barcode_no]);
			$booking_without_order=$barcodeDatas[0];
			$is_transfer=$barcodeDatas[2];
			
			if($is_transfer==1) 
			{
				$po_id=$transPoIdsArr[$barcode_no];
			}
			else
			{
				$po_id=$barcodeDatas[1];
			}
			
			if($booking_without_order==1) 
			{
				$buyer_id=$barcodeDatas[3];
				$po_no='';
				$job_no='';
			}
			else
			{
				$buyer_id=$po_details_array[$po_id]['buyer_name'];
				$po_no=$po_details_array[$po_id]['po_number'];
				$job_no=$po_details_array[$po_id]['job_no'];
			}
			
			if($po_id=='') { $po_id=0; }
			$is_sales = $is_sales_arr[$barcode_no];
			
			$barcodeData.=$value."**".$po_id."**".$buyer_id."**".$po_no."**".$job_no . "**" . $is_sales ."_";
		}
		//echo substr($barcodeData,0,-1);
	}
	$i=count($barcodeDataArr);
	foreach($barcodeDataArr as $barcode_no=>$value)
	{
		
		$barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("barcode_no")]."**".$row[csf("company_id")]."**".$row[csf("roll_no")]."**".$roll_id."**".$row[csf("body_part_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("color_id")]."**".$row[csf("batch_id")]."**".$batch_name_array[$row[csf("batch_id")]]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("width")]."**".number_format($row[csf("qnty")],2,'.','')."**".$rate."**".$row[csf("booking_without_order")]."**".$row[csf("reprocess")]."**".$gmts_item_id;	
		
		$barcodeDatas=explode("**",$value);
		$roll_no=$barcodeDatas[2];
		$body_part_id=$barcodeDatas[4];
		$body_part_name=$barcodeDatas[5];
		$bwo=$barcodeDatas[6];
		$receive_basis=$barcodeDatas[7];
		$receive_basis_id=$barcodeDatas[8];
		$booking_no=$barcodeDatas[9];
		$booking_id=$barcodeDatas[10];
		$color=$barcodeDatas[11];
		$color_id=$barcodeDatas[12];
		$batch_id=$barcodeDatas[13];
		$batchName=$barcodeDatas[14];
		$prod_id=$barcodeDatas[15];
		$febric_description_id=$barcodeDatas[16];
		$width=$barcodeDatas[17];
		$gmts_item_id = $barcodeDatas[22];
		$booking_without_order=$barcodeDatas[20];
		
		$reprocess=$issued_data_arr[$barcode_no]['reprocess'];
		$previous_re=$issued_data_arr[$barcode_no]['prev_reprocess'];
		$cons_comp=$constructtion_arr[$febric_description_id].", ".$composition_arr[$febric_description_id];
		$dtls_id=$issued_data_arr[$barcode_no]['dtls_id'];
		$trans_id=$issued_data_arr[$barcode_no]['trans_id'];
		
		
		$po_id=$issued_data_arr[$barcode_no]['po_id'];
		$roll_table_id=$issued_data_arr[$barcode_no]['id'];
		$roll_id=$issued_data_arr[$barcode_no]['roll_id'];
		$rate=$issued_data_arr[$barcode_no]['rate'];
		$qnty=$issued_data_arr[$barcode_no]['qnty'];

		if($booking_without_order==1) 
		{
			if($sample_without_order==1)
			{
				$buyer_id=$order_to_sample_data[$barcode_no]["buyer_id"];
				$buyer_name=$buyer_name_array[$order_to_sample_data[$barcode_no]["buyer_id"]];
			}
			else
			{
				$buyer_id=$without_order_buyer[$barcode_no];
				$buyer_name=$buyer_name_array[$without_order_buyer[$barcode_no]];
			}
			
			
			$job_no='';
			if($is_salesOrder == 1){
				$po_no=$sales_order_no;
			}else{
				$po_no='';
			}
		}
		else
		{
			if($is_salesOrder == 1){
				$sales_booking_no 	= $sales_arr[$po_id]["sales_booking_no"];
				$within_group 		= $sales_arr[$po_id]["within_group"];
					if($within_group==1){
						$po_no 				= $sales_arr[$po_id]["sales_order_no"];
			        	$job_no 			= $job_arr[$sales_booking_no]["job_no_mst"];
			        	$buyer_name 		= $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
		        	}else{
		        		$po_no 				= $sales_arr[$po_id]["sales_order_no"];
			        	$job_no 			= "";
			        	$buyer_name 		= $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
		        	}
				//$po_no=$sales_order_no;
			}else{
				$buyer_id=$po_details_array[$po_id]['buyer_name'];
				$buyer_name=$buyer_name_array[$po_details_array[$po_id]['buyer_name']];
				$po_no=$po_details_array[$po_id]['po_number'];
				$job_no=$po_details_array[$po_id]['job_no'];
			}
		}
		//echo $po_details_array[$po_id]['buyer_name'];
		$receive_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");
		?>
        <tr id="tr_<? echo $i; ?>" align="center" valign="middle">
            <td width="35" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
            <td width="80" id="barcode_<? echo $i; ?>"><? echo $barcode_no; ?></td>
            <td width="50" id="roll_<? echo $i; ?>"><? echo $roll_no; ?></td>
            <td width="60" id="batch_<? echo $i; ?>"><? echo $batchName; ?></td>
            <td width="60" id="prodId_<? echo $i; ?>"><? echo $prod_id; ?></td>
            <td id="gmtItemTd_<? echo $i; ?>" width="140" >
                <?
                    echo create_drop_down( "cboItemName_$i", 130, $garments_item,"", 1, "-- Select Gmt. Item --", "$gmts_item_id", "change_garments_item(this.id,this.value)",1,0,"","","","","","cboItemName[]" );	
                ?>
            </td>
            <td style="word-break:break-all;" width="80" id="bodyPart_<? echo $i; ?>"><? echo $body_part_name; ?></td>
            <td style="word-break:break-all;" width="130" id="cons_<? echo $i; ?>" align="left"><? echo $cons_comp; ?></td>
            <td style="word-break:break-all;" width="50" id="dia_<? echo $i; ?>"><? echo $width; ?></td>
            <td style="word-break:break-all;" width="70" id="color_<? echo $i; ?>"><? echo $color; ?></td>
            <td width="70" align="right" id="rollWeight_<? echo $i; ?>"><? echo $qnty; ?></td>
            <td style="word-break:break-all;" width="60" id="buyer_<? echo $i; ?>"><? echo $buyer_name; ?></td>
            <td style="word-break:break-all;" width="80" id="job_<? echo $i; ?>"><? echo $job_no; ?></td>
            <td style="word-break:break-all;" width="80" id="order_<? echo $i; ?>" align="left"><? echo $po_no; ?></td>
            <td style="word-break:break-all;" width="100" id="progBookPiNo_<? echo $i; ?>"><? echo $booking_no; ?></td>
            <td style="word-break:break-all;" width="70" id="basis_<? echo $i; ?>"><? echo $receive_basis; ?></td>
            <td id="button_<? echo $i; ?>" align="center">
                <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
                <input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<?php echo $barcode_no;?>"/>
                <input type="hidden" name="recvBasis[]" id="recvBasis_<? echo $i; ?>" value="<?php echo $receive_basis_id;?>"/>
                <input type="hidden" name="progBookPiId[]" id="progBookPiId_<? echo $i; ?>" value="<?php echo $booking_id;?>"/>
                <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<?php echo $prod_id;?>"/>
                <input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<?php echo $po_id;?>"/>
                <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<?php echo $roll_id;?>"/>
                <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<?php echo $qnty;?>"/>
                <input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<?php echo $color_id;?>"/>
                <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<?php echo $body_part_id;?>"/>
                <input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<?php echo $batch_id;?>"/>
                <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<?php echo $dtls_id;?>"/>
                <input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<?php echo $trans_id;?>"/>
                <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<?php echo $roll_table_id;?>"/>
                <input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<?php echo $febric_description_id;?>"/>
                <input type="hidden" name="rollRate[]" id="rollRate_<? echo $i; ?>" value="<?php echo $rate;?>"/>
                <input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<?php echo $job_no;?>"/>
                <input type="hidden" name="reProcess[]" id="reProcess_<? echo $i; ?>" value="<?php echo $reprocess;?>"/>
                <input type="hidden" name="preRerocess[]" id="preRerocess_<? echo $i; ?>" value="<?php echo $previous_re;?>"/>
                <input type="hidden" name="IsSalesId[]" id="IsSalesId_<? echo $i; ?>" value="<?php echo $is_salesOrder;?>"/>
            </td>
        </tr>
			
		<?
		$i--;
	}
	
	exit();	
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$barcode_no =trim($data[3]);

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and d.po_number like '$search_string'";
	}
	
	if($barcode_no!="")
	{
		$barcode_cond="and c.barcode_no='$barcode_no'";
	}
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	
	$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond"; 
	//echo $sql;//die;
	//2=>"Grey Receive"
	//22=>'Knit Grey Fabric Receive'
	//58=>"Knit Grey Fabric Receive Roll"
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:750px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="730" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
exit();
}




if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id,batch_no)
		{
			$('#hidden_batch_id').val(id);
			$('#hidden_batch_no').val(batch_no);
			parent.emailwindow.hide();
		}
    </script>
</head>

<body>
<div align="center" style="width:800px;">
    <form name="searchbatchnofrm"  id="searchbatchnofrm">
        <fieldset style="width:790px; margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" border="1" rules="all" width="770" class="rpt_table">
                <thead>
                    <th width="240">Batch Date Range</th>
                    <th width="170">Search By</th>
                    <th id="search_by_td_up" width="200">Please Enter Batch No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
                        <input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
                        <input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;">To
                        <input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px;">
                    </td>
                    <td>
						<?
							$search_by_arr=array(0=>"Batch No",1=>"Fabric Booking no.",2=>"Color");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                      ?>
                    </td>
                    <td align="center" id="search_by_td" width="140px">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_batch_search_list_view', 'search_div', 'woven_finish_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
            </table>
            <div style="width:100%; margin-top:5px" id="search_div" align="left"></div>
        </fieldset>
    </form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_batch_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and batch_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==0)
			$search_field_cond="and batch_no like '$search_string'";
		else if($search_by==1)
			$search_field_cond="and booking_no like '$search_string'";
		else
			$search_field_cond="and color_id in(select id from lib_color where color_name like '$search_string')";
	}
	else
	{
		$search_field_cond="";
	}
	
	$po_arr=array();
	$po_data=sql_select("select id, po_number, job_no_mst from wo_po_break_down");	
	foreach($po_data as $row)
	{
		$po_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
	}
	
	if($db_type==0)
	{
		$order_id_arr=return_library_array( "select mst_id, group_concat(po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','order_id');
	}
	else
	{
		$order_id_arr=return_library_array( "select mst_id, LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','po_id');
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	$sql = "select id, batch_no, extention_no, batch_date, batch_weight, booking_no, color_id, batch_against, booking_without_order, re_dyeing_from from pro_batch_create_mst where entry_form in (0,17) and batch_for=1 and batch_against<>4 and company_id=$company_id and status_active=1 and is_deleted=0 $search_field_cond $date_cond"; 
	//echo $sql;die; 
	// 7 = Finish Fabric Production Entry
	// 37 = Finish Fabric Receive Entry
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" >
        <thead>
            <th width="40">SL</th>
            <th width="90">Batch No</th>
            <th width="80">Extention No</th>
            <th width="80">Batch Date</th>
            <th width="80">Batch Qnty</th>
            <th width="115">Booking No</th>
            <th width="110">Color</th>
            <th>Po No</th>
        </thead>
    </table>
    <div style="width:780px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search" >
        <?
            $i=1;
            $nameArray=sql_select( $sql );
            foreach ($nameArray as $selectResult)
            {
                $po_no=''; $job_array=array();
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_id=array_unique(explode(",",$order_id_arr[$selectResult[csf('id')]]));
				foreach($order_id as $value)
				{
					if($po_no=='') $po_no=$po_arr[$value]['po_no']; else $po_no.=",".$po_arr[$value]['po_no'];
					$job_no=$po_arr[$value]['job_no'];
					if(!in_array($job_no,$job_array))
					{
						$job_array[]=$job_no;
					}
				}
				$job_no=implode(",",$job_array);
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>')"> 
					<td width="40" align="center"><? echo $i; ?></td>	
					<td width="90"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
					<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?>&nbsp;</p></td>
					<td width="80" align="center"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
					<td width="80" align="right"><? echo $selectResult[csf('batch_weight')]; ?>&nbsp;</td> 
					<td width="115"><p><? echo $selectResult[csf('booking_no')]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
					<td><p><? echo $po_no; ?>&nbsp;</p></td>	
				</tr>
				<?
				$i++;
            }
        ?>
        </table>
    </div>
<?
exit();
}

if($action=="check_batch_no")
{
	$data=explode("**",$data);
	$sql="select id, batch_no from pro_batch_create_mst where batch_no='".trim($data[0])."' and company_id='".$data[1]."' and is_deleted=0 and status_active=1 and entry_form in (0,17) and batch_for=1 and batch_against<>4 order by id desc";
	// 7 = Finish Fabric Production Entry
	// 37 = Finish Fabric Receive Entry
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo $data_array[0][csf('id')];
	}
	else
	{
		echo "0";
	}
	exit();	
}


if($action=="requisition_popup")
{
	echo load_html_head_contents("Requisition Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(data)
		{
		$('#hidden_reqn_id').val(data);
		parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Requisition Date Range</th>
                    <th id="search_by_td_up" width="180">Requisition No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_reqn_id" id="hidden_reqn_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" readonly>
					</td>
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_reqn_no" id="txt_reqn_no" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>, 'create_reqn_search_list_view', 'search_div', 'woven_finish_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                	<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
                </tr>
           </table>
           <div style="width:100%; margin-top:5px;" id="search_div" align="center"></div>
		</fieldset>
	</form>
</div>
</body>           
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
<script>
	$('#cbo_location_id').val(0);
</script>
</html>
<?
}

if($action=="create_reqn_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$start_date =$data[1];
	$end_date =$data[2];
	$company_id =$data[3];

	$lay_plan_arr=return_library_array( "select id, cutting_no from ppl_cut_lay_mst",'id','cutting_no');
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and reqn_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and reqn_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and reqn_number like '$search_string'";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(insert_date) as year,";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(insert_date,'YYYY') as year,";
	}
	else $year_field="";//defined Later
	
	$sql = "select id, $year_field reqn_number_prefix_num, reqn_number, lay_plan_id, reqn_date from pro_fab_reqn_for_cutting_mst where status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $location_cond $date_cond"; 
	$arr=array(3=>$lay_plan_arr);
	
	echo create_list_view("tbl_list_search", "Year, Requisition No, Requisition Date, Lay Plan Cutting No", "80,150,150","700","200",0, $sql, "js_set_value", "id,reqn_number", "", 1, "0,0,0,lay_plan_id", $arr, "year,reqn_number_prefix_num,reqn_date,lay_plan_id","","",'0,0,3,0','');
	
	exit();
}


if($action=="check_reqn_no")
{
	$sql = sql_select("select id from pro_fab_reqn_for_cutting_mst where status_active=1 and is_deleted=0 and reqn_number='$data' ");
    if(count($sql)>0) echo $sql[0][csf('id')];
	else{ echo 0; }
	exit();	
}





if( $action=='populate_list_view' ) 
{	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	if($db_type==2) 
	{
		 $year=" TO_CHAR(a.insert_date,'YYYY') as year ";
		 $null_cond="NVL";
	}
    else if($db_type==0) 
	{ 
		$year=" year(a.insert_date) as year ";
		$null_cond="IFNULL";
	}
	
	$all_po_id='';
	$sql="select id, buyer_id, po_id, job_no, item_id, body_part, determination_id, gsm, dia, color_id, size_id, reqn_qty from pro_fab_reqn_for_cutting_dtls where status_active=1 and is_deleted=0 and mst_id=$data";
	$result=sql_select($sql);

	?>
    &nbsp;&nbsp;&nbsp;
    <fieldset>
	<table cellpadding="0" width="400" cellspacing="0" border="1" id="scanning_tbl_top" class="rpt_table" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="50">Product Id</th>
            <th width="80">Body Part</th>
            <th width="130">Construction/ Composition</th>
            <th width="50">GSM</th>
            <th width="50">Dia</th>
        </thead>
     </table>
     <div style="width:415px; max-height:250px; margin-left:18px; overflow-y:scroll" align="left">
     <table cellpadding="0" cellspacing="0" width="390" border="1" id="scanning_tbl_req" rules="all" class="rpt_table">
        <tbody>
	<?
	$i=1;
	foreach($result as $row)
	{
		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>"> 
            <td width="40"><? echo $i; ?></td>
            <td width="50"><p><? echo $row[csf('po_id')]; ?></p></td>
            <td width="80" style="word-break:break-all;"><? echo $body_part[$row[csf('body_part')]]; ?></td>
            <td width="130" style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('determination_id')]].", ".$composition_arr[$row[csf('determination_id')]]; ?></td>
            <td width="50" style="word-break:break-all;" id="gsm<? echo $i; ?>"><? echo $row[csf('gsm')]; ?></td>
            <td width="50" style="word-break:break-all;" id="dia<? echo $i; ?>"><? echo $row[csf('dia')]; ?>
                <input type="hidden" value="<? echo $row[csf('po_id')]; ?>" id="poId<? echo $i; ?>" name="poId[]"/>
                <input type="hidden" value="<? echo $row[csf('determination_id')]; ?>" id="deterId<? echo $i; ?>" name="deterId[]"/>
                <input type="hidden" value="<? echo $row[csf('color_id')]; ?>" id="colorId<? echo $i; ?>" name="colorId[]"/>
                <input type="hidden" value="<? echo $row[csf('body_part')]; ?>" id="bodyPartId<? echo $i; ?>" name="bodyPartId[]"/>
                <input type="hidden" value="<? echo $row[csf('job_no')]; ?>" id="jobNo<? echo $i; ?>" name="jobNo[]"/>
                <input type="hidden" value="<? echo $row[csf('size_id')]; ?>" id="sizeId<? echo $i; ?>" name="sizeId[]"/>
                <input type="hidden" value="<? echo $row[csf('id')]; ?>" id="dtlsId<? echo $i; ?>" name="dtlsId[]"/>
            </td>
        </tr>
	<?		
		$i++;
	}
	?>
    </tbody>
  </table>
</fieldset>
    <?			
	exit();
}


if($action=="finish_issue_print")
{
	extract($_REQUEST);
	
	$data=explode('*',$data);
	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];
	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company where id=$company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	$dataArray=sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company,req_no, batch_no from inv_issue_master where id=$update_id");
	
	$job_array=array();
	$job_sql="select a.buyer_name, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
	}
	
	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=3");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
?>

<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
?>
    <div>
        <table width="1100" cellspacing="0">
            <tr>
            	<td rowspan="3" width="70">
            		<img src="../../../<? echo $image_location; ?>" height="70" width="200">
            	</td>
                <td colspan="6" align="center" style="font-size:22px">
                	<strong><? echo $company_array[$company]['name']; ?></strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    <?
                        $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=1"); 
                        foreach ($nameArray as $result)
                        { 
                        ?>
                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                            Level No: <? echo $result[csf('level_no')]?>
                            Road No: <? echo $result[csf('road_no')]; ?> 
                            Block No: <? echo $result[csf('block_no')];?> 
                            City No: <? echo $result[csf('city')];?> 
                            Zip Code: <? echo $result[csf('zip_code')]; ?><br> 
                            Province No: <?php echo $result[csf('province')];?> 
                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?> 
                            Email Address: <? echo $result[csf('email')];?> 
                            Website No: <? echo $result[csf('website')];
                        }
                    ?> 
                </td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
            </tr>
            <tr>
                 <td width="130"><strong>Dyeing Company:</strong> </td>
                 <td>
                    <?
                  		if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name']; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]]; 
					?>
                </td>
                <td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $txt_issue_no; ?></td>
                <td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            </tr>
            <tr>
            	<td><strong>Dyeing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
                <td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
                <td><strong>Batch Number:</strong></td><td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
            </tr>
           
            <tr>
                 <td><strong>Reqsn No:</strong></td><td colspan="5"><? echo $dataArray[0][csf('req_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1230"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="20">SL</th>
                <th width="90">Barcode</th>
                <th width="50">Product Id</th>
                <th width="60">Batch No</th>
                <th width="70">Order No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="60">Basis</th>
                <th width="100">Prog/Book/ PI No</th>
                <th width="80">Body Part</th>
                <th width="130">Construction/ Composition</th> 	
                <th width="40">Dia</th>
                <th width="70">Color</th>
                <th width="40">Roll</th>
                <th>Issue Qty</th> 
            </thead>
            <?
			
			
			$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id,
			a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id,
			b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0
			and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0");
			$roll_details_array=array(); $barcode_array=array(); 
			foreach($data_array as $row)
			{
				$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$body_part[$row[csf("body_part_id")]];
				$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$receive_basis_arr[$row[csf("receive_basis")]];
				$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
				if(str_replace("'","",$row[csf('entry_form')])==68)
				{
				$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("recv_number")];
				}
				else
				{
				$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
				}
				$roll_details_array[$row[csf("barcode_no")]]['color']=$color_arr[$row[csf("color_id")]];
				$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
				$roll_details_array[$row[csf("barcode_no")]]['dia_width_type']=$fabric_typee[$row[csf("dia_width_type")]];
				$roll_details_array[$row[csf("barcode_no")]]['body_part']=$body_part[$row[csf("body_part_id")]];
				$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
				$roll_details_array[$row[csf("barcode_no")]]['batch_name']=$batch_name_array[$row[csf("batch_id")]];
				
				if($row[csf("knitting_source")]==1)
				{
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_array[$row[csf('knitting_company')]]['name'];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
				}
				$roll_details_array[$row[csf("barcode_no")]]['trans_id']=$row[csf("trans_id")];
				$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
				$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
				$roll_details_array[$row[csf("barcode_no")]]['deter_d']=$row[csf("fabric_description_id")];
				$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
				$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
				$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
				$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
			}
			
				$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
				$i=1; $tot_qty=0; 
            	$sql = "select a.batch_id, a.issue_qnty, a.prod_id, a.issue_qnty, a.knitting_company,b.roll_no,b.roll_id,b.barcode_no,b.po_breakdown_id 	 from inv_finish_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by roll_no";
				$result=sql_select($sql);
				foreach($result as $row)
				{
				?>
                <tr>
                    <td><? echo $i; ?></td>
                    <td><? echo $row[csf('barcode_no')]; ?></td>
                    <td align="center"><? echo $row[csf('prod_id')]; ?></td>
                    <td align="center" style="word-break:break-all;"><? echo $batch_arr[$roll_details_array[$row[csf("barcode_no")]]['batch_id']]; ?></td>
                    <td style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
                    <td style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['buyer_name']."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['receive_basis']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['booking_no']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['body_part']; ?></td>
                    <td style="word-break:break-all;"><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']];?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['width'] ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['color'] ?></td>
                    <td  align="center"><? echo $row[csf('roll_no')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
                </tr>
                <?
					$tot_qty+=$row[csf('issue_qnty')];
					$i++;
				}
			?>
            <tr> 
                <td align="right" colspan="13"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
		</table>
	</div>
    <? echo signature_table(21, $company, "900px"); ?>
   	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
exit();
}


if($action=="finish_issue_print2")
{
	extract($_REQUEST);
	
	$data=explode('*',$data);
	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];
	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company where id=$company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	$buyer_arr=return_library_array( "select id, buyer_name from  lib_buyer", "id", "buyer_name"  );
	
	$dataArray=sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company,buyer_id,buyer_job_no from inv_issue_master where id=$update_id");
	
	
	
	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=3");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
	
	
	$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id,
			a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id,
			b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty			
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
			WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0
			and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0");
			$roll_details_array=array(); $barcode_array=array(); 
			foreach($data_array as $row)
			{
				$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$body_part[$row[csf("body_part_id")]];
				$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$receive_basis_arr[$row[csf("receive_basis")]];
				$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
				if(str_replace("'","",$row[csf('entry_form')])==68)
				{
				$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("recv_number")];
				}
				else
				{
				$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
				}
				$roll_details_array[$row[csf("barcode_no")]]['color']=$color_arr[$row[csf("color_id")]];
				$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
				$roll_details_array[$row[csf("barcode_no")]]['dia_width_type']=$fabric_typee[$row[csf("dia_width_type")]];
				$roll_details_array[$row[csf("barcode_no")]]['body_part']=$body_part[$row[csf("body_part_id")]];
				$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
				$roll_details_array[$row[csf("barcode_no")]]['batch_name']=$batch_name_array[$row[csf("batch_id")]];
				
				if($row[csf("knitting_source")]==1)
				{
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_array[$row[csf('knitting_company')]]['name'];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
				}
				$roll_details_array[$row[csf("barcode_no")]]['trans_id']=$row[csf("trans_id")];
				$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
				$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
				$roll_details_array[$row[csf("barcode_no")]]['deter_d']=$row[csf("fabric_description_id")];
				$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
				$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
				$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
				$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
			}
	
			$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
			$i=1; $tot_qty=0; 
        	$sql = "select a.batch_id, a.issue_qnty, a.prod_id, a.issue_qnty, a.knitting_company, a.remarks, b.roll_no,b.roll_id,b.barcode_no,b.po_breakdown_id from inv_finish_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by roll_no";
			$result_dtils=sql_select($sql);
			
			foreach($result_dtils as $val)
			{
				$po_id_arr[]=$val[csf('po_breakdown_id')];
			}
			$all_po_id=implode(",",$po_id_arr);
			
			$job_array=array();		
			$job_sql="select a.buyer_name,a.style_ref_no, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in (".$all_po_id.")";
			$job_sql_result=sql_select($job_sql);
			foreach($job_sql_result as $row)
			{
				$job_array['job'][]=$row[csf('job_no')];
				$job_array['style_ref_no'][]=$row[csf('style_ref_no')];
				$job_array['buyer_name'][]=$row[csf('buyer_name')];
				$job_array['po'][]=$row[csf('po_number')];
			}
			
			//print_r($job_array);die;
			
?>

<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
?>
    <div>
        <table width="1100" cellspacing="0">
            <tr>
            	<td rowspan="3" width="70">
            		<img src="../../../<? echo $image_location; ?>" height="70" width="200">
            	</td>
                <td colspan="6" align="center" style="font-size:22px">
                	<strong><? echo $company_array[$company]['name']; ?></strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    <?
                        $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=1"); 
                        foreach ($nameArray as $result)
                        { 
                        ?>
                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                            Level No: <? echo $result[csf('level_no')]?>
                            Road No: <? echo $result[csf('road_no')]; ?> 
                            Block No: <? echo $result[csf('block_no')];?> 
                            City No: <? echo $result[csf('city')];?> 
                            Zip Code: <? echo $result[csf('zip_code')]; ?><br> 
                            Province No: <?php echo $result[csf('province')];?> 
                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?> 
                            Email Address: <? echo $result[csf('email')];?> 
                            Website No: <? echo $result[csf('website')];
                        }
                    ?> 
                </td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><u>Finish Fabric Delivery to Cutting Challan</u></strong></td>
            </tr>
            <tr>
     
                <td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $txt_issue_no; ?></td>
                <td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
            	<td><strong>Sewing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
            </tr>
            <tr>
                <td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
                <td><strong>Style No:</strong></td><td width="175px"><? echo implode(",",$job_array['style_ref_no']); ?></td>
                <td><strong>Job No:</strong></td><td width="175px"><? echo implode(",",$job_array['job']); ?></td>
            </tr>
            <tr>
                <td><strong>Buyer:</strong></td><td><? echo $buyer_arr[implode(",",$job_array['buyer_name'])]; ?></td>
                <td><strong>Order No:</strong></td><td><? echo implode(",",$job_array['po']); ?></td>
            </tr>
            <tr>
                <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
        	</tr>
        </table>
        <br>
        <table cellspacing="0" width="1050"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80">Batch No</th>
                <th width="120">Body Part</th>
                <th width="180">Construction/ Composition</th> 	
                <th width="80">Dia</th>
                <th width="80">Color</th>
                <th width="80">Roll</th>
                <th width="80">Issue Qty</th> 
                <th>Remarks</th> 
            </thead>
            
			<?
			
			foreach($result_dtils as $row)
			{
				?>
                <tr>
                    <td><? echo $i; ?></td>
                    <td align="center" style="word-break:break-all;"><? echo $batch_arr[$roll_details_array[$row[csf("barcode_no")]]['batch_id']]; ?></td>                   

                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['body_part']; ?></td>
                    <td style="word-break:break-all;"><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']];?></td>                   
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['width'] ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['color'] ?></td>
                    <td  align="right"><? echo $row[csf('roll_no')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
                    <td align="right"><? $roll_details_array[$row[csf("barcode_no")]]['remarks'];?></td>
                </tr>
                <?
					$tot_qty+=$row[csf('issue_qnty')];
					$tot_roll_qty+=$row[csf('roll_no')];

					$i++;
				}
			?>
            <tr> 
                <td align="left" colspan="6"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_roll_qty,2,'.',''); ?></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
		</table>
	</div>
    <? echo signature_table(21, $company, "900px"); ?>
   	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
exit();
}




if($action=="fabric_details_print")
{
	extract($_REQUEST);
	
	$data=explode('*',$data);
	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];
	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company where id=$company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	$dataArray=sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company,req_no, batch_no from inv_issue_master where id=$update_id");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$job_array=array();
	$job_sql="select a.buyer_name, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
	}
	
	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=3");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
	}
?>

<?
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
?>
    <div>
         <table width="1100" cellspacing="0">
            <tr>
            	<td rowspan="3" width="70">
            		<img src="../../../<? echo $image_location; ?>" height="70" width="200">
            	</td>
                <td colspan="6" align="center" style="font-size:22px">
                	<strong><? echo $company_array[$company]['name']; ?></strong>
                </td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    <?
                        $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=1"); 
                        foreach ($nameArray as $result)
                        { 
                        ?>
                            Plot No: <? echo $result[csf('plot_no')]; ?> 
                            Level No: <? echo $result[csf('level_no')]?>
                            Road No: <? echo $result[csf('road_no')]; ?> 
                            Block No: <? echo $result[csf('block_no')];?> 
                            City No: <? echo $result[csf('city')];?> 
                            Zip Code: <? echo $result[csf('zip_code')]; ?><br> 
                            Province No: <?php echo $result[csf('province')];?> 
                            Country: <? echo $country_arr[$result[csf('country_id')]]; ?> 
                            Email Address: <? echo $result[csf('email')];?> 
                            Website No: <? echo $result[csf('website')];
                        }
                    ?> 
                </td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
            </tr>
            <tr>
                 <td width="130"><strong>Dyeing Company:</strong> </td>
                 <td>
                    <?
                  		if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name']; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]]; 
					?>
                </td>
                <td width="120"><strong>Issue ID :</strong></td><td width="175px"><? echo $txt_issue_no; ?></td>
                <td width="125"><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            </tr>
            <tr>
            	<td><strong>Dyeing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
                <td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
                <td><strong>Batch Number:</strong></td><td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
            </tr>
           
            <tr>
                 <td><strong>Reqsn No:</strong></td><td colspan="5"><? echo $dataArray[0][csf('req_no')]; ?></td>
            </tr>
            <tr>
                <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
            </tr>
        </table>
        <br>
        <br>
        <table cellspacing="0" width="1230"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="20">SL</th>
                <th width="50">Product Id</th>
                <th width="60">Batch No</th>
                <th width="70">Order No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="80">Body Part</th>
                <th width="130">Fabric Desc</th> 	
                <th width="40">Dia</th>
                <th width="70">Color</th>
                <th width="40">Roll</th>
                <th width="60">Reject Qty</th>
                <th width="60">Issue Qty</th> 
            </thead>
            <?
			
			$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id,
			a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id,
			b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0
			and a.entry_form in(17) and c.entry_form in(17) and c.status_active=1 and c.is_deleted=0");
			$roll_details_array=array(); $barcode_array=array(); 
			foreach($data_array as $row)
			{
				$roll_details_array[$row[csf("po_breakdown_id")]]['body_part_id']=$body_part[$row[csf("body_part_id")]];
				$roll_details_array[$row[csf("po_breakdown_id")]]['receive_basis']=$receive_basis_arr[$row[csf("receive_basis")]];
				$roll_details_array[$row[csf("po_breakdown_id")]]['receive_date']=change_date_format($row[csf("receive_date")]);
				if(str_replace("'","",$row[csf('entry_form')])==68)
				{
				$roll_details_array[$row[csf("po_breakdown_id")]]['booking_no']=$row[csf("recv_number")];
				}
				else
				{
				$roll_details_array[$row[csf("po_breakdown_id")]]['booking_no']=$row[csf("booking_no")];
				}
				$roll_details_array[$row[csf("prod_id")]]['color']=$color_arr[$row[csf("color_id")]];
				$roll_details_array[$row[csf("prod_id")]]['knitting_source_id']=$row[csf("knitting_source")];
				$roll_details_array[$row[csf("prod_id")]]['dia_width_type']=$fabric_typee[$row[csf("dia_width_type")]];
				$roll_details_array[$row[csf("prod_id")]]['body_part']=$body_part[$row[csf("body_part_id")]];
				$roll_details_array[$row[csf("prod_id")]]['batch_id']=$row[csf("batch_id")];
				$roll_details_array[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$row[csf("barcode_no")]]['batch_name']=$batch_arr[$row[csf("batch_id")]];
				//$roll_details_array[$row[csf("barcode_no")]]['batch_name'].=$batch_arr[$row[csf("batch_id")]].',';
				
				if($row[csf("knitting_source")]==1)
				{
					$roll_details_array[$row[csf("prod_id")]]['knitting_company']=$company_array[$row[csf('knitting_company')]]['name'];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$roll_details_array[$row[csf("prod_id")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
				}
				$roll_details_array[$row[csf("po_breakdown_id")]]['trans_id']=$row[csf("trans_id")];
				$roll_details_array[$row[csf("po_breakdown_id")]]['dtls_id']=$row[csf("dtls_id")];
				$roll_details_array[$row[csf("po_breakdown_id")]]['prod_id']=$row[csf("prod_id")];
				$roll_details_array[$row[csf("po_breakdown_id")]]['deter_d']=$row[csf("fabric_description_id")];
				$roll_details_array[$row[csf("prod_id")]]['gsm']=$row[csf("gsm")];
				$roll_details_array[$row[csf("prod_id")]]['width']=$row[csf("width")];
				$roll_details_array[$row[csf("po_breakdown_id")]]['roll_id']=$row[csf("roll_id")];
				$roll_details_array[$row[csf("po_breakdown_id")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("po_breakdown_id")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
				$roll_details_array[$row[csf("po_breakdown_id")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
				$barcode_array[$row[csf("po_breakdown_id")]]=$row[csf("barcode_no")];
			}
			
				$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
				$i=1; $tot_qty=0; 
            /*  echo  $sql = "select a.batch_id, sum(a.issue_qnty) as issue_qnty, a.prod_id, a.knitting_company,count(b.roll_id) as no_of_roll,
sum(b.reject_qnty) as reject_qnty,b.po_breakdown_id 
from inv_finish_fabric_issue_dtls a, pro_roll_details b
where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
and b.is_deleted=0 group by a.batch_id, a.prod_id, a.knitting_company,b.po_breakdown_id order by a.prod_id DESC";*/
  				
				if($db_type==0)
				{
   				 	$sql = "select a.batch_id, sum(a.issue_qnty) as issue_qnty, a.prod_id, a.knitting_company,a.body_part_id,count(b.roll_id) as no_of_roll, 
sum(b.reject_qnty) as reject_qnty,b.po_breakdown_id,group_concat(b.barcode_no) as barcode_nos from inv_finish_fabric_issue_dtls a, pro_roll_details b  
where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
and b.is_deleted=0 group by a.batch_id, a.prod_id, a.knitting_company,a.body_part_id,b.po_breakdown_id"; 
				}
				else
				{
					$sql = "select a.batch_id, sum(a.issue_qnty) as issue_qnty, a.prod_id, a.knitting_company,a.body_part_id,count(b.roll_id) as no_of_roll, 
sum(b.reject_qnty) as reject_qnty,b.po_breakdown_id,LISTAGG(b.barcode_no, ',')  WITHIN GROUP (ORDER BY b.id desc) as barcode_nos from inv_finish_fabric_issue_dtls a, pro_roll_details b  
where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
and b.is_deleted=0 group by a.batch_id, a.prod_id, a.knitting_company,a.body_part_id,b.po_breakdown_id"; 
				}


				$result=sql_select($sql);
				foreach($result as $row)
				{
					$barcode=explode(",",$row[csf("barcode_nos")]);
					//print_r($barcode);
					$barcodeData="";
					foreach($barcode as $barCode)
					{
						$barcodeData = $barCode;
					}
				?>
                <tr>
                    <td><? echo $i; ?></td>
                    <td align="center"><? echo $row[csf('prod_id')]; ?></td>
                    <td align="center" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$barcodeData]['batch_name']; ?></td>
                    <td style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
                    <td style="word-break:break-all;"><? echo $buyer_library[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("prod_id")]]['body_part'];//echo $body_part[$row[csf("body_part_id")]]; ?></td>
                    <td style="word-break:break-all;"><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']];?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("prod_id")]]['width'] ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("prod_id")]]['color'] ?></td>
                    <td  align="center"><? echo $row[csf('no_of_roll')]; ?></td>
                    <td  align="center"><? echo $row[csf('reject_qnty')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
                </tr>
                <?
					$tot_qty+=$row[csf('issue_qnty')];
					$i++;
				}

			
			?>
            <tr> 
                <td align="right" colspan="11"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
		</table>
	</div>
    <? echo signature_table(21, $company, "900px"); ?>
    <script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
    
    <?
exit();
}


if($action=="issue_challan_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];
	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company where id=$company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$supplier_arr = return_library_array("select id,short_name from lib_supplier","id","short_name");

	$dataArray=sql_select("select count(b.id) as total_roll,sum(b.qnty) as total_qty,a.issue_date,a.knit_dye_source,a.knit_dye_company from inv_issue_master a, pro_roll_details b where a.id=$update_id and  a.id=b.mst_id and b.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_date,a.knit_dye_source,a.knit_dye_company");
	
?>
    <div align="center">
        <table width="350" cellspacing="0">
             <tr>
                <td colspan="2" align="left" id="barcode_img_id"></td>
            </tr>
            <tr>  
                <td width="130"><strong>Issue No :</strong></td><td width="200"><? echo $txt_issue_no; ?></td>
            </tr>
            
           <tr>
            	<td><strong>Issue Date:</strong></td><td width="200"><? echo change_date_format($dataArray[0][csf('issue_date')]); ?></td>
            </tr>
            <tr>
            	<td><strong>No of roll:</strong></td><td width="200"><? echo $dataArray[0][csf('total_roll')]; ?></td>
            </tr>
            <tr>
            	<td><strong>Total Quantity:</strong></td><td width="200"><? echo $dataArray[0][csf('total_qty')]; ?></td>
            </tr>
            <tr>
            	<td><strong>Dyeing Source:</strong></td><td width="200"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
            </tr>
            <tr>
                 <td width="130"><strong>Dyeing Company:</strong> </td>
                 <td width="200">
                    <?
                  		if ($dataArray[0][csf('knit_dye_source')]==1) echo $company_array[$dataArray[0][csf('knit_dye_company')]]['name']; else if ($dataArray[0][csf('knit_dye_source')]==3) echo $supplier_arr[$dataArray[0][csf('knit_dye_company')]]; 
					?>
           		</td>
            </tr>
          
        </table>
       
	</div>

   	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',

			  barWidth: 1,
			  barHeight: 30,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_issue_no; ?>');
	</script>
<?
exit();
}



?>

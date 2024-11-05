<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

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
		echo create_drop_down( "cbo_dyeing_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
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
	//$avg_product_rate_arr=return_library_array( "select id, avg_rate_per_unit from product_details_master",'id','avg_rate_per_unit');
	
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
		
		$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KGIR', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=61 and $year_cond=".date('Y',time())." order by id desc ", "issue_number_prefix","issue_number_prefix_num"));
		$id=return_next_id( "id", "inv_issue_master", 1 ) ;
				 
		$field_array="id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_basis,issue_purpose,entry_form,item_category,company_id,batch_no,issue_date,knit_dye_source, knit_dye_company,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',0,".$cbo_issue_purpose.",61,13,".$cbo_company_id.",".$txt_batch_id.",".$txt_issue_date.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity, cons_rate, cons_amount, brand_id,location_id,machine_id,stitch_length,rack,self,inserted_by,insert_date";
		
		$dtls_id=return_next_id("id", "inv_grey_fabric_issue_dtls", 1);
		$field_array_dtls="id,mst_id,trans_id,basis,program_no,prod_id,issue_qnty,rate, amount,color_id,location_id,machine_id,stitch_length,yarn_lot, yarn_count,brand_id,rack,self,inserted_by,insert_date";		
		
		$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,rate,amount, roll_no, roll_id, booking_without_order, inserted_by, insert_date";
		
		
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); 
		
		$barcodeNos=''; $all_prod_id='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
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
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$locationId="locationId_".$j;
			$machineId="machineId_".$j;
			$roll_rate="rollRate_".$j;
			$issueRtnRollId="issueRtnRollId_".$j;
			$bookWithoutOrder="bookWithoutOrder_".$j;
			
			$cons_rate=$$roll_rate;
			$cons_amount=$cons_rate*$$rollWgt;
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,2,".$txt_issue_date.",'".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$brandId."','".$$locationId."','".$$machineId."','".$$stichLn."','".$$rack."','".$$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$transactionID.",'".$$recvBasis."','".$$progBookPiId."','".$$productId."','".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$colorId."','".$$locationId."','".$$machineId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$rack."','".$$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",'".$$orderId."',61,'".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($$bookWithoutOrder!=1)
			{
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transactionID.",2,61,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_prop = $id_prop+1;
			}

			$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$transactionID."__".$id_roll.",";
			$prodData_array[$$productId]+=$$rollWgt;
			$prodData_amount[$$productId]+=$cons_amount;
			$all_prod_id.=$$productId.",";
			$id_roll = $id_roll+1;
			$transactionID = $transactionID+1;
			$dtls_id = $dtls_id+1;
			
		}
		//echo $data_array_dtls."***".$data_array_trans."***".$data_array_roll;die;
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$issue_amount=$prodData_amount[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]-$issue_qty;
			$stock_value=$row[csf('stock_value')]-$issue_amount;
			$avg_rate=$stock_value/$current_stock;
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*'".$stock_value."'*'".$avg_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		/*$barcodeNos=''; $po_array=array(); $prodData_array=array(); $dtlsData_array=array(); $dtls_qty_array=array(); $all_prod_id='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
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
			$stichLn="stichLn_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			
			$dtlsData=$$recvBasis."**".$$progBookPiId."**".$$productId."**".$$yarnLot."**".$$yarnCount."**".$$colorId."**".$$stichLn."**".$$rack."**".$$shelf;
			if(!in_array($dtlsData, $dtlsData_array))
			{
				$dtls_id = $dtls_id+1;
				$dtlsData_array[$dtls_id]=$dtlsData;
			}
			
			$dtls_qty_array[$dtls_id]+=$$rollWgt;

			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",'".$$orderId."',61,'".$$rollWgt."','".$$rollId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$id_roll = $id_roll+1;
			
			$barcodeNos.=$$barcodeNo."__".$dtls_id.",";
			$po_array[$order_id][$$productId]+=$$rollWgt;
			$prodData_array[$$productId]+=$$rollWgt;
			$all_prod_id.=$$productId.",";
		}

		foreach($dtlsData_array as $dtls_id=>$data)
		{
			$dtlsData=explode("**",$data);
			$recvBasis=$dtlsData[0];
			$progBookPiId=$dtlsData[1];
			$productId=$dtlsData[2];
			$yarnLot=$dtlsData[3];
			$yarnCount=$dtlsData[4];
			$colorId=$dtlsData[5];
			$stichLn=$dtlsData[6];
			$rack=$dtlsData[7];
			$shelf=$dtlsData[8];
			$issue_qty=$dtls_qty_array[$dtls_id];
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$transactionID.",".$id.",'".$recvBasis."','".$progBookPiId."',".$cbo_company_id.",'".$productId."',13,2,".$txt_issue_date.",'".$issue_qty."','".$rack."','".$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$transactionID.",'".$recvBasis."','".$progBookPiId."','".$productId."','".$issue_qty."','".$colorId."','".$stichLn."','".$yarnLot."','".$yarnCount."','".$rack."','".$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$transactionID = $transactionID+1;
		}
		
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		foreach($po_array as $order_id=>$val)
		{
			foreach($val as $prod_id=>$order_qnty)
			{ 
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transactionID.",2,61,'".$id_dtls."','".$order_id."','".$prod_id."','".$order_qnty."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_prop = $id_prop+1;
			}
		}*/
		
	 	//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rID5=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
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
		
		$field_array="issue_purpose*batch_no*issue_date*knit_dye_source*knit_dye_company*updated_by*update_date";
		$data_array=$cbo_issue_purpose."*".$txt_batch_id."*".$txt_issue_date."*".$cbo_dyeing_source."*".$cbo_dyeing_company."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity,cons_rate, cons_amount,brand_id,location_id,machine_id,stitch_length,rack,self,inserted_by,insert_date";
		$field_array_updatetrans="transaction_date*cons_quantity*cons_rate*cons_amount*brand_id*location_id*machine_id*stitch_length*rack*self*updated_by *update_date";
		
		$dtls_id=return_next_id("id", "inv_grey_fabric_issue_dtls", 1);
		$field_array_dtls="id,mst_id,trans_id,basis,program_no,prod_id, issue_qnty,rate, amount, color_id,location_id, machine_id, stitch_length, yarn_lot, yarn_count, brand_id,rack,self ,inserted_by,insert_date";
		//$field_array_dtls="id,mst_id,trans_id,basis,program_no,prod_id,issue_qnty,rate, amount,color_id,location_id,machine_id,inserted_by,insert_date";
		$field_array_updatedtls="issue_qnty*rate*amount*color_id*location_id*machine_id*stitch_length*yarn_lot*yarn_count*brand_id*rack*self*updated_by* update_date";		
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,rate,amount, roll_no, roll_id, booking_without_order, inserted_by, insert_date";
		$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_updateroll="qnty*rate*amount*roll_no*updated_by*update_date";
		
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		$id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); 
		
		$barcodeNos=''; $all_prod_id=''; $all_roll_id='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
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
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$dtlsId="dtlsId_".$j;
			$transId="transId_".$j;
			$rolltableId="rolltableId_".$j;
			$rollNo="rollNo_".$j;
			$locationId="locationId_".$j;
			$machineId="machineId_".$j;
			$roll_rate="rollRate_".$j;
			$bookWithoutOrder="bookWithoutOrder_".$j;
			$cons_rate=$$roll_rate;
			$cons_amount=$cons_rate*$$rollWgt;
			
			if($$rolltableId>0)
			{
				$transId_arr[]=$$transId;
				$data_array_update_trans[$$transId]=explode("*",($txt_issue_date."*'".$$rollWgt."'*'".$cons_rate."'*'".$cons_amount."'*'".$$brandId."'*'".$$locationId."'*'".$$machineId."'*'".$$stichLn."'*'".$$rack."'*'".$$shelf."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$dtlsId_arr[]=$$dtlsId;
				$data_array_update_dtls[$$dtlsId]=explode("*",($$rollWgt."*'".$cons_rate."'*'".$cons_amount."'*'".$$colorId."'*'".$$locationId."'*'".$$machineId."'*'".$$stichLn."'*'".$$yarnLot."'*'".$$yarnCount."'*'".$$brandId."'*'".$$rack."'*'".$$shelf."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$rollId_arr[]=$$rolltableId;
				$data_array_update_roll[$$rolltableId]=explode("*",("'".$$rollWgt."'*'".$cons_rate."'*'".$cons_amount."'*'".$$rollNo."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$barcodeNos.=$$barcodeNo."__".$$dtlsId."__".$$transId."__".$$rolltableId.",";
				$dtlsId_prop=$$dtlsId;
				$transId_prop=$$transId;
				$all_roll_id.=$$rolltableId.",";
			}
			else
			{
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,2,".$txt_issue_date.",'".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$brandId."','".$$locationId."','".$$machineId."','".$$stichLn."','".$$rack."','".$$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$transactionID.",'".$$recvBasis."','".$$progBookPiId."','".$$productId."','".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$colorId."','".$$locationId."','".$$machineId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$rack."','".$$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",'".$$orderId."',61,'".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				$dtlsId_prop=$dtls_id;
				$transId_prop=$transactionID;
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$transactionID."__".$id_roll.",";
				$id_roll = $id_roll+1;
				$transactionID = $transactionID+1;
				$dtls_id = $dtls_id+1;
			}
			
			$prodData_array[$$productId]+=$$rollWgt;
			$prodData_amount[$$productId]+=$cons_amount;
			$all_prod_id.=$$productId.",";
			
			if($$bookWithoutOrder!=1)
			{
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transId_prop.",2,61,'".$dtlsId_prop."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id_prop = $id_prop+1;
			}
		}
		
		$txt_deleted_id=str_replace("'","",$txt_deleted_id); $adj_prod_array=array(); $update_dtls_id=''; $update_trans_id=''; $update_delete_dtls_id='';
		if($txt_deleted_id!="") $all_roll_id=$all_roll_id.$txt_deleted_id; else $all_roll_id=substr($all_roll_id,0,-1);
		$deleted_id_arr=explode(",",$txt_deleted_id);
		
		if($all_roll_id!="")
		{
			//echo "10**select a.id, a.qnty, b.id as dtls_id, b.trans_id, b.prod_id from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.id in($all_roll_id)";die;
			$rollData=sql_select("select a.id, a.qnty, b.id as dtls_id, b.trans_id, b.prod_id,a.amount from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.id in($all_roll_id)");
			foreach($rollData as $row)
			{
				$adj_prod_array[$row[csf('prod_id')]]+=$row[csf('qnty')];
				$adj_prod_amount[$row[csf('prod_id')]]+=$row[csf('amount')];
				$all_prod_id.=$row[csf('prod_id')].",";
				$update_dtls_id.=$row[csf('dtls_id')].",";
				
				if(in_array($row[csf('id')],$deleted_id_arr))
				{
					$update_trans_id.=$row[csf('trans_id')].",";
					$update_delete_dtls_id.=$row[csf('dtls_id')].",";
				}
			}
		}
			
		$update_trans_id=substr($update_trans_id,0,-1);
		$update_delete_dtls_id=substr($update_delete_dtls_id,0,-1);
		
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]+$adj_prod_array[$row[csf('id')]]-$issue_qty;
			//$stock_value=$current_stock*$row[csf('avg_rate_per_unit')];
			$stock_value=$row[csf('stock_value')]+$adj_prod_amount[$row[csf('id')]]-$prodData_amount[$row[csf('id')]];
			$avg_rate=$stock_value/$current_stock;
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$prodData_array[$row[csf('id')]]."'*'".$current_stock."'*'".$stock_value."'*'".$avg_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
	
		
		$delete_prop=execute_query("delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=61",0);
		
		$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$update_id,0);
		$rID2=true; $rID3=true; $rID4=true; $rID5=true; $rID6=true; $rID7=true; $statusChangeTrans=true; $statusChangeDtls=true; $statusChangeRoll=true;
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			$rID3=sql_insert("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}
		//echo "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr );die;
		
		if(count($data_array_update_dtls)>0)
		{
			$rID5=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_updatetrans, $data_array_update_trans, $transId_arr ));
			$rID6=execute_query(bulk_update_sql_statement( "inv_grey_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
			$rID7=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr ));
		}

		if($txt_deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$update_trans_id,0);
			$statusChangeDtls=sql_multirow_update("inv_grey_fabric_issue_dtls",$field_array_status,$data_array_status,"id",$update_delete_dtls_id,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}
		
		$rID8=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
		$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		//oci_rollback($con);
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$rID7."&&".$rID8."&&".$delete_prop."&&".$prodUpdate."&&".$statusChangeTrans."&&".$statusChangeDtls."&&".$statusChangeRoll;die;
		//echo "10**".$statusChangeTrans."--".$statusChangeDtls."--".$statusChangeRoll;die;
		//echo bulk_update_sql_statement("inv_grey_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr );die;
		
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

if($action=="roll_used_check")
{
	$roll_id=return_field_value("id","pro_roll_details","entry_form=62 and status_active=1 and is_deleted=0 and barcode_no=$data");
	if($roll_id=="")
	{
		echo "0";
	}
	else
	{
		echo "1";
	}
	
	exit();	
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_challan_search_list_view', 'search_div', 'grey_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and issue_number like '$search_string'";
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
	
	$sql = "select id, $year_field issue_number_prefix_num, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where entry_form=61 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Issue No</th>
            <th width="60">Year</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th width="110">Issue Purpose</th>
            <th width="100">Batch</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				 
				$dye_comp="&nbsp;";
                if($row[csf('knit_dye_source')]==1)
					$dye_comp=$company_arr[$row[csf('knit_dye_company')]]; 
				else
					$dye_comp=$supllier_arr[$row[csf('knit_dye_company')]];
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $dye_comp; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $batch_arr[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
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

if($action=="populate_data_from_data")
{
	$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where id=$data and entry_form=61";
	//echo $sql;
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_issue_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_issue_purpose').val(".$row[csf("issue_purpose")].");\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#cbo_dyeing_source').val(".$row[csf("knit_dye_source")].");\n";
		echo "load_drop_down( 'requires/grey_fabric_issue_roll_wise_controller', ".$row[csf("knit_dye_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
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
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","pro_roll_details","entry_form=61 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2) 
	{
		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","pro_roll_details","entry_form=61 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
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
	
    </script>

</head>

<body>
<div align="center" style="width:960px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:960px; margin-left:2px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
                <thead>
                    <th>Location</th>
                    <th>Order No</th>
                    <th>File No</th>
                    <th>Internal Ref No</th>
                    <th>Barcode No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                    </th> 
                </thead>
                <tr class="general">
                    <td>
                    <?
						echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
					?>
                    </td>
                    <td align="center">				
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />	
                    </td> 
                    <td align="center">				
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />	
                    </td>			
                    <td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" /></td> 
                       			
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'grey_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
                     </td>
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

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	
	$location_id=trim($data[0]);
	$order_no=$data[1];
	$company_id =$data[2];
	$file_no =trim($data[3]);
	$ref_no =trim($data[4]);
	$barcode_no =trim($data[5]);
	

	$search_field_cond="";
	if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	
	$location_cond="";
	if($location_id>0) $location_cond=" and a.location_id=$location_id";
	
	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$sql="SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping 
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond $location_cond"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order No</th>
            <th width="110">Location</th>
            <th width="70">File NO</th>
            <th width="70">Ref No</th>
            <th width="70">Shipment Date</th>
            <th width="90">Barcode No</th>
            <th width="50">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:960px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="30" align="center">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="110" align="center"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('file_no')]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('grouping')]; ?></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="90"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="50" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="940">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_batch_search_list_view', 'search_div', 'grey_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
	$sql = "select id, batch_no, extention_no, batch_date, batch_weight, booking_no, color_id, batch_against, booking_without_order, re_dyeing_from from pro_batch_create_mst where entry_form=0 and batch_for=1 and batch_against<>4 and company_id=$company_id and status_active=1 and is_deleted=0 $search_field_cond $date_cond"; 
	//echo $sql;die; 
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
	$sql="select id, batch_no from pro_batch_create_mst where batch_no='".trim($data[0])."' and company_id='".$data[1]."' and is_deleted=0 and status_active=1 and entry_form=0 order by id desc";
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

if($action=="grey_issue_print")
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
	$location_arr=return_library_array("select id, location_name from  lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');	
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");
	$production_arr=array();
	$production_del_sql=sql_select("select id, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company");
	foreach($production_del_sql as $row)
	{
		$production_arr[$row[csf('id')]]['sys']=$row[csf('sys_number')];
		$production_arr[$row[csf('id')]]['unit']=$row[csf('location_id')];
		$production_arr[$row[csf('id')]]['knit_sou']=$row[csf('knitting_source')];
		$production_arr[$row[csf('id')]]['knit_com']=$row[csf('knitting_company')];
	}
	
	$mc_id_arr=return_library_array( "select b.id, a.machine_no_id from	pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2", "id", "machine_no_id");
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	//echo "";
	
	$dataArray=sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no from inv_issue_master where id=$update_id");
	$job_array=array();
	$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
	}
	
	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
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
	
	$grey_issue_basis=array(0=>"Independent",1=>"PI",2=>"Booking",3=>"Knitting Plan",9=>"Delivery");
?>
    <div>
        <table width="1030" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    <? echo show_company($company,'',''); ?> 
                </td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
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
               <td colspan="3" id="barcode_img_id"></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1030"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:12px">
                <th width="20">SL</th>
                <th width="70">File /Ref./ Order</th>
                <th width="80">Job/ Buyer. /Style</th>
                <th width="50">Basis</th>
                <th width="95">Prog/Book/ PI No</th>
                <th width="110">Item Description</th>
                <th width="65">Bar Code</th>
                <th width="50">Stich Length</th>
                <th width="50">GSM/ Fin. Dia</th>
                <th width="60">MC No / Dia X Gauge</th>
                <th width="60">Color</th>
                <th width="35">Brand /UOM</th>
                <th width="50">Count /Y. Lot</th>
                <th width="55">K. Party</th>
                <th width="30">Rack/ Shelf</th>
                <th width="35">Roll No</th>
                <th>Issue Qty</th> 
            </thead>
            <? 
				//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
				$i=1; $tot_qty=0; 
            	$sql = "select a.basis, a.program_no, a.prod_id, a.issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, b.roll_no, b.barcode_no, b.po_breakdown_id, c.quantity from inv_grey_fabric_issue_dtls a, pro_roll_details b, order_wise_pro_details c where a.id=b.dtls_id and a.id=c.dtls_id and a.mst_id=$update_id and b.entry_form=61 and c.entry_form=61 and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by b.roll_no";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}
					$mc_id=$mc_id_arr[$row[csf("roll_id")]];
					if($row[csf('basis')]==1) $pi_book_plan=$pi_arr[$row[csf("program_no")]];
					else if($row[csf('basis')]==2) $pi_book_plan=$booking_arr[$row[csf("program_no")]];	
					else if($row[csf('basis')]==3) $pi_book_plan=$row[csf("program_no")];
					else if($row[csf('basis')]==9) $pi_book_plan=$production_arr[$row[csf("program_no")]]['sys'];//$production_del_arr[$row[csf("program_no")]];	
					else $pi_book_plan="&nbsp;";
					
					$file_ref_ord="";
					$file_ref_ord='F : '.$job_array[$row[csf('po_breakdown_id')]]['file_no'].'<br>R : '.$job_array[$row[csf('po_breakdown_id')]]['grouping'].'<br>O : '.$job_array[$row[csf('po_breakdown_id')]]['po'];
					$job_buyer_style="";
					$job_buyer_style='J: '.$job_array[$row[csf('po_breakdown_id')]]['job'].'<br>B : '.$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']].'<br>S : '.$job_array[$row[csf('po_breakdown_id')]]['style_ref'];
					$knit_party="";
					$knit_source=$production_arr[$row[csf("program_no")]]['knit_sou'];
					if($knit_source==1) $knit_party=$location_arr[$production_arr[$row[csf("program_no")]]['unit']];
					else if($knit_source==3) $knit_party=$supplier_arr[$production_arr[$row[csf("program_no")]]['knit_com']];
						
				?>
                	<tr style="font-size:11px">
                        <td><? echo $i; ?></td>
                        <td style="word-break:break-all;"><? echo $file_ref_ord; ?></td>
                        <td style="word-break:break-all;"><? echo $job_buyer_style; ?></td>
                        <td><? echo $grey_issue_basis[$row[csf('basis')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $pi_book_plan; ?></td>
                        <td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
                        <td><? echo $row[csf('barcode_no')]; ?></td>
                        <td><? echo $row[csf('stitch_length')]; ?></td>
                        <td style="word-break:break-all;"><? echo 'G : '.$product_array[$row[csf("prod_id")]]['gsm'].'<br>D : '.$product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
                        <td style="word-break:break-all;"><? echo 'N : '.$lib_mc_arr[$mc_id]['no'].'<br> D : '.$lib_mc_arr[$mc_id]['dia'].' X '.$lib_mc_arr[$mc_id]['gauge']; ?></td>
                        <td style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td style="word-break:break-all;"><? echo 'B :'.$brand_arr[$row[csf("brand_id")]].'<br>U :'.$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></td>
                        <td style="word-break:break-all;"><? echo 'C : '.$count.'<br>L : '.$row[csf('yarn_lot')]; ?></td>
                        <td style="word-break:break-all;"><? echo $knit_party; ?></td>
                        <td style="word-break:break-all;"><? echo 'R : '.$row[csf('rack')].'<br>S : '.$row[csf('self')]; ?></td>
                        <td style="word-break:break-all;" align="right"><? echo $row[csf('roll_no')] ?></td>
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                    </tr>
                <?
					$tot_roll+=$row[csf('roll_no')];
					$tot_qty+=$row[csf('quantity')];
					$i++;
				}
			?>
            <tr style="font-size:12px"> 
                <td align="right" colspan="15"><strong>Total</strong></td>
                <td align="right"><? //echo number_format($tot_roll); ?></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
		</table>
	</div>
    <? echo signature_table(17, $company, "930px"); ?>
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

if($action=="mc_wise_print")
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
	$location_arr=return_library_array("select id, location_name from  lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');	
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");
	$production_arr=array();
	$production_del_sql=sql_select("select id, sys_number, knitting_source, knitting_company, location_id from pro_grey_prod_delivery_mst where entry_form=56 and company_id=$company");
	foreach($production_del_sql as $row)
	{
		$production_arr[$row[csf('id')]]['sys']=$row[csf('sys_number')];
		$production_arr[$row[csf('id')]]['unit']=$row[csf('location_id')];
		$production_arr[$row[csf('id')]]['knit_sou']=$row[csf('knitting_source')];
		$production_arr[$row[csf('id')]]['knit_com']=$row[csf('knitting_company')];
	}
	
	$mc_id_arr=return_library_array( "select b.id, a.machine_no_id from	pro_grey_prod_entry_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=2", "id", "machine_no_id");
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	
	$dataArray=sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no from inv_issue_master where id=$update_id");
	$job_array=array();
	$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
	}
	
	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
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
	
	$grey_issue_basis=array(0=>"Independent",1=>"PI",2=>"Booking",3=>"Knitting Plan",9=>"Delivery");
	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    <? echo show_company($company,'',''); ?> 
                </td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
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
               <td colspan="3" id="barcode_img_id"></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center" style="font-size:12px">
                <th width="20">SL</th>
                <th width="80">File /Ref./ Order</th>
                <th width="90">Job/ Buyer. /Style</th>
                <th width="50">Basis</th>
                <th width="100">Prog/Book/ PI No</th>
                <th width="110">Item Description</th>
                <th width="50">Stich Length</th>
                <th width="50">GSM/ Fin. Dia</th>
                <th width="70">MC No / Dia X Gauge</th>
                <th width="70">Color</th>
                <th width="35">Brand /UOM</th>
                <th width="60">Count /Y. Lot</th>
                <th width="60">K. Party</th>
                <th width="30">Rack/ Shelf</th>
                <th width="35">Total Roll</th>
                <th>Issue Qty</th> 
            </thead>
            <?
			
				//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
				$i=1; $tot_qty=0; 
            	$sql = "select a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id, count(b.roll_no) as tot_roll, b.po_breakdown_id, sum(c.quantity) as quantity from inv_grey_fabric_issue_dtls a, pro_roll_details b, order_wise_pro_details c where a.id=b.dtls_id and a.id=c.dtls_id and a.mst_id=$update_id and b.entry_form=61 and c.entry_form=61 and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by  a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id, b.roll_id,  b.po_breakdown_id ";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}
					$mc_id=$mc_id_arr[$row[csf("roll_id")]];
					if($row[csf('basis')]==1) $pi_book_plan=$pi_arr[$row[csf("program_no")]];
					else if($row[csf('basis')]==2) $pi_book_plan=$booking_arr[$row[csf("program_no")]];	
					else if($row[csf('basis')]==3) $pi_book_plan=$row[csf("program_no")];
					else if($row[csf('basis')]==9) $pi_book_plan=$production_arr[$row[csf("program_no")]]['sys'];//$production_del_arr[$row[csf("program_no")]];	
					else $pi_book_plan="&nbsp;";
					
					$file_ref_ord="";
					$file_ref_ord='F : '.$job_array[$row[csf('po_breakdown_id')]]['file_no'].'<br>R : '.$job_array[$row[csf('po_breakdown_id')]]['grouping'].'<br>O : '.$job_array[$row[csf('po_breakdown_id')]]['po'];
					$job_buyer_style="";
					$job_buyer_style='J: '.$job_array[$row[csf('po_breakdown_id')]]['job'].'<br>B : '.$buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer']].'<br>S : '.$job_array[$row[csf('po_breakdown_id')]]['style_ref'];
					$knit_party="";
					$knit_source=$production_arr[$row[csf("program_no")]]['knit_sou'];
					if($knit_source==1) $knit_party=$location_arr[$production_arr[$row[csf("program_no")]]['unit']];
					else if($knit_source==3) $knit_party=$supplier_arr[$production_arr[$row[csf("program_no")]]['knit_com']];
					
					$print_dt[$file_ref_ord][$job_buyer_style][$grey_issue_basis[$row[csf('basis')]]][$pi_book_plan][$composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]]['G : '.$product_array[$row[csf("prod_id")]]['gsm'].'<br>D : '.$product_array[$row[csf("prod_id")]]['dia_width']]['N : '.$lib_mc_arr[$mc_id]['no'].'<br> D : '.$lib_mc_arr[$mc_id]['dia'].' X '.$lib_mc_arr[$mc_id]['gauge']][$color_arr[$row[csf("color_id")]]]['B :'.$brand_arr[$row[csf("brand_id")]].'<br>U :'.$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]]['C : '.$count.'<br>L : '.$row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['roll_no']+=$row[csf('tot_roll')];
					$print_dt[$file_ref_ord][$job_buyer_style][$grey_issue_basis[$row[csf('basis')]]][$pi_book_plan][$composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]]['G : '.$product_array[$row[csf("prod_id")]]['gsm'].'<br>D : '.$product_array[$row[csf("prod_id")]]['dia_width']]['N : '.$lib_mc_arr[$mc_id]['no'].'<br> D : '.$lib_mc_arr[$mc_id]['dia'].' X '.$lib_mc_arr[$mc_id]['gauge']][$color_arr[$row[csf("color_id")]]]['B :'.$brand_arr[$row[csf("brand_id")]].'<br>U :'.$unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]]['C : '.$count.'<br>L : '.$row[csf('yarn_lot')]][$knit_party][$row[csf('stitch_length')]]['qnty']+=$row[csf('quantity')];
					
				 }
			 	//	print_r($print_dt);
				 foreach($print_dt as $file=>$fdet)
				 {	
				 	 foreach($fdet as $job_buyers=>$jbdet)
					 {
						 foreach($jbdet as $basis=>$basbdet)
					 	 {
							 foreach($basbdet as $pi_book_p=>$pibookdet)
					 	 	 {
								 foreach($pibookdet as $compos=>$composdet)
					 	 	 	 {
									 foreach($composdet as $diawid=>$diawiddet)
					 	 	 	 	 {
										 foreach($diawiddet as $gaugewid=>$gaugewiddet)
					 	 	 	 	 	 {
											 foreach($gaugewiddet as $colo=>$colodet)
					 	 	 	 	 		 {
												 foreach($colodet as $brandde=>$branddedet)
					 	 	 	 	 		 	 {
													 foreach($branddedet as $countde=>$dedet)
					 	 	 	 	 		 		 {
														 foreach($dedet as $partys=>$partysdet)
					 	 	 	 	 		 		 	 {
															 foreach($partysdet as $slength=>$slength_val)
					 	 	 	 	 		 		 	 	{ 
															?>
																<tr style="font-size:11px">
																	<td><? echo $i; ?></td>
																	<td style="word-break:break-all;"><? echo $file; ?></td>
																	<td style="word-break:break-all;"><? echo $job_buyers; ?></td>
																	<td><? echo $basis; ?></td>
																	<td style="word-break:break-all;"><? echo $pi_book_p; ?></td>
																	<td><? echo $compos; ?></td>
																	<td><? echo $slength; ?></td>
																	<td style="word-break:break-all;"><? echo $diawid; ?></td>
																	<td style="word-break:break-all;"><? echo $gaugewid; ?></td>
																	<td style="word-break:break-all;"><? echo $colo; ?></td>
																	<td style="word-break:break-all;"><? echo $brandde; ?></td>
																	<td style="word-break:break-all;"><? echo $countde; ?></td>
																	<td style="word-break:break-all;"><? echo $partys; ?></td>
																	<td style="word-break:break-all;"><? echo 'R : '.$row[csf('rack')].'<br>S : '.$row[csf('self')]; ?></td>
																	<td style="word-break:break-all;" align="right"><? echo $slength_val['roll_no']; ?></td>
																	<td align="right"><? echo number_format($slength_val['qnty'],2); ?></td>
																</tr>
															<?
																$tot_roll+=$slength_val['roll_no'];
																$tot_qty+=$slength_val['qnty'];
																$i++;
															}
														 }
													 }
												 }
											 }
										 }
									 }
								 }
							 }
						}
					 }
				}
			?>
            <tr style="font-size:12px"> 
                <td align="right" colspan="14"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_roll); ?></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
		</table>
	</div>
    <? echo signature_table(17, $company, "930px"); ?>
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
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');	
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no"  );
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number"  );
	$production_arr=return_library_array( "select id, sys_number from pro_grey_prod_delivery_mst where entry_form=56", "id", "sys_number"  );
	$dataArray=sql_select("select issue_purpose, issue_date, knit_dye_source, knit_dye_company, batch_no from inv_issue_master where id=$update_id");
	
	$job_array=array();
	$job_sql="select a.style_ref_no, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
	}
	
	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
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
	
	$grey_issue_basis=array(0=>"Independent",1=>"PI",2=>"Booking",3=>"Knitting Plan",9=>"Delivery");
?>
    <div>
        <table width="900" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    <?
                        $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
                        foreach ($nameArray as $result)
                        { 
                        ?>
                            Plot No: <? echo $result['plot_no']; ?> 
                            Level No: <? echo $result['level_no']?>
                            Road No: <? echo $result['road_no']; ?> 
                            Block No: <? echo $result['block_no'];?> 
                            City No: <? echo $result['city'];?> 
                            Zip Code: <? echo $result['zip_code']; ?> 
                            Province No: <?php echo $result['province'];?> 
                            Country: <? echo $country_arr[$result['country_id']]; ?><br> 
                            Email Address: <? echo $result['email'];?> 
                            Website No: <? echo $result['website'];
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
                <td>
                <?
                    if($db_type==0)
                    {
                        $po_id=return_field_value("group_concat(po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
                    }
                    else
                    {
                        $po_id=return_field_value("LISTAGG(b.po_breakdown_id, ',') WITHIN GROUP (ORDER BY b.po_breakdown_id) as po_id","inv_grey_fabric_issue_dtls a, order_wise_pro_details b","a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and b.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","po_id");
                    }
                   // echo $po_id;
                    $po_exp=array_unique(explode(',',$po_id));
                    $po_no=''; $job='';  $style_ref='';
                    foreach($po_exp as $id)
                    { 
                        if($po_no=='') $po_no=$job_array[$id]['po']; else $po_no.=', '.$job_array[$id]['po'];
                        if($job=='') $job=$job_array[$id]['job']; else $job.=','.$job_array[$id]['job'];
						if($style_ref=='') $style_ref=$job_array[$id]['style_ref']; else $style_ref.=','.$job_array[$id]['style_ref'];
                    }
                    $job=implode(",",array_unique(explode(',',$job)));
					$style_ref=implode(",",array_unique(explode(',',$style_ref)));
                ?>
                <strong>Job No:</strong></td>
                <td width="175px" colspan="3"><? echo $job; ?></td>
                <td><strong>Style Ref.:</strong></td><td width="175px"><? echo $style_ref; ?></td>
            </tr>
            <tr>
                 <td><strong>Order No:</strong></td><td colspan="5"><? echo $po_no; ?></td>
            </tr>
            <tr>
                <td colspan="3" id="barcode_img_id"></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="930"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="20">SL</th>
                <th width="50">Basis</th>
                <th width="80">Prog/Book/ PI No</th>
                <th width="130">Item Description</th>
                <th width="50">Stich Length</th>
                <th width="40">GSM</th>
                <th width="40">Fin. Dia</th>
                <th width="70">Color</th>
                <th width="40">No of Roll</th>
                <th width="40">UOM</th>
                <th width="50">Count</th>
                <th width="50">Brand</th>
                <th width="50">Yarn Lot</th>
                <th width="30">Rack</th>
                <th width="30">Shelf</th>
                <th>Issue Qty</th> 
            </thead>
            <?
				//$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
				$i=1; $tot_qty=0; 
            	$sql = "select a.basis, a.program_no, a.prod_id, sum(a.issue_qnty) as issue_qnty, a.color_id, a.stitch_length, a.yarn_lot, 
				a.yarn_count, a.rack, a.self, a.brand_id, count(b.roll_id) as no_of_roll from inv_grey_fabric_issue_dtls a, pro_roll_details b
			    where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
				and b.is_deleted=0 
				group by a.basis, a.program_no, a.prod_id, a.color_id, a.stitch_length, a.yarn_lot, a.yarn_count, a.rack, a.self, a.brand_id";
				$result=sql_select($sql);
				foreach($result as $row)
				{
					$count='';
					$yarn_count=explode(",",$row[csf('yarn_count')]); 
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}
					
					if($row[csf('basis')]==1) $pi_book_plan=$pi_arr[$row[csf("program_no")]];
					else if($row[csf('basis')]==2) $pi_book_plan=$booking_arr[$row[csf("program_no")]];	
					else if($row[csf('basis')]==3) $pi_book_plan=$row[csf("program_no")];
					else if($row[csf('basis')]==9) $pi_book_plan=$production_arr[$row[csf("program_no")]];	
					else $pi_book_plan="&nbsp;";	
				?>
                	<tr>
                        <td><? echo $i; ?></td>
                        <td><? echo $grey_issue_basis[$row[csf('basis')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $pi_book_plan; ?></td>
                        <td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
                        <td><? echo $row[csf('stitch_length')]; ?></td>
                        <td style="word-break:break-all;"><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
                        <td style="word-break:break-all;"><? echo $product_array[$row[csf("prod_id")]]['dia_width']; ?></td>
                        <td style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("roll_no")]; ?></td>
                        <td style="word-break:break-all;"><? echo $unit_of_measurement[$product_array[$row[csf("prod_id")]]['uom']]; ?></td>
                        <td style="word-break:break-all;"><? echo $count; ?></td>
                        <td style="word-break:break-all;"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
                        <td style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
                        <td style="word-break:break-all;"><? echo $row[csf('rack')]; ?></td>
                        <td style="word-break:break-all;"><? echo $row[csf('self')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
                    </tr>
                <?
					$tot_qty+=$row[csf('issue_qnty')];
					$i++;
				}

			?>
            <tr> 
                <td align="right" colspan="15"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
		</table>
	</div>
    <? echo signature_table(17, $company, "900px"); ?>
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

	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	
	$dataArray=sql_select("select count(b.id) as number_of_roll,sum(b.qnty) as total_qnty,a.issue_date, a.knit_dye_source, a.knit_dye_company from inv_issue_master a, pro_roll_details b where a.id=$update_id  and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by a.issue_date, a.knit_dye_source, a.knit_dye_company");
	
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
            	<td><strong>No of roll:</strong></td><td width="200"><? echo $dataArray[0][csf('number_of_roll')]; ?></td>
            </tr>
            <tr>
            	<td><strong>Total Quantity:</strong></td><td width="200"><? echo $dataArray[0][csf('total_qnty')]; ?></td>
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

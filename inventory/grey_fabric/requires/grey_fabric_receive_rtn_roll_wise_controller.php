
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
        for($k=1;$k<=$tot_row;$k++)
        {
            $productId="productId_".$k;
            $prod_ids.=$$productId.",";
        }
        $prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,',')))); 
        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and store_id = $cbo_to_store and status_active = 1 and is_deleted = 0 ", "max_date");      
		if($max_issue_date != "")
        {
            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
            $issue_rtn_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_rtn_date)));
            if ($issue_rtn_date < $max_issue_date) 
            {
                echo "20**Return Date Can not Be Less Than Last Transaction Date Of This Lot";
                die;
            }
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
		
		//$new_receive_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GFRIR', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from  inv_receive_master where company_id=$cbo_company_id and entry_form=84 and $year_cond=".date('Y',time())." order by id desc ", "recv_number_prefix", "recv_number_prefix_num" ));
		
		//$id=return_next_id( "id", "inv_receive_master", 1 ) ;
		
		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
		$new_receive_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'GFRIR',84,date("Y",time()),13 ));
				 
		$field_array="id, entry_form, item_category, recv_number_prefix, recv_number_prefix_num, recv_number, company_id, receive_date, challan_no, store_id, inserted_by, insert_date";
		
		$data_array="(".$id.",84,13,'".$new_receive_system_id[1]."',".$new_receive_system_id[2].",'".$new_receive_system_id[0]."',".$cbo_company_id.",".$txt_issue_rtn_date.",".$txt_challan_no.",".$cbo_to_store.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, machine_id, rack, self, inserted_by, insert_date";
		
		
		//$dtls_id=return_next_id( "id", "pro_grey_prod_entry_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, prod_id, febric_description_id, gsm, width, order_id, grey_receive_qnty, rate, amount, uom, yarn_lot, yarn_count, brand_id, machine_no_id, rack, self, color_id, color_range_id, stitch_length, inserted_by, insert_date";
		
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, booking_without_order, entry_form, barcode_no, roll_id, roll_no, qnty, rate, amount, inserted_by, insert_date";
		$field_array_roll_update="roll_used*is_returned*updated_by*update_date";
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); 
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		

		
		for($j=1;$j<=$tot_row;$j++)
		{
			/*$productId="productId_".$j;
			$rollWgt="rollWgt_".$j;
			$all_prod_id.=$$productId.",";
			$prodData_array[$$productId]+=$$rollWgt;*/
			
			
			$recvBasis="recvBasis_".$j;
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$BookWithoutOrd="BookWithoutOrd_".$j;
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
			$febDescripId="febDescripId_".$j;
			$machineNoId="machineNoId_".$j;
			$gsm="gsm_".$j;
			$diaWidth="diaWidth_".$j;
			$rollRate="rollRate_".$j;
			$rollAmt="rollAmt_".$j;
			$colorRange="colorRange_".$j;
			
			$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,4,".$txt_issue_rtn_date.",".$cbo_to_store.",'".$$brandId."',12,'".$$rollWgt."','".$$rollRate."','".$$rollAmt."',12,'".$$rollWgt."','".$$rollRate."','".$$rollAmt."','".$$rollWgt."','".$$rollAmt."','".$$machineNoId."','".$$rack."','".$$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$transactionID.",4,84,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$transactionID.",'".$$productId."','".$$febDescripId."','".$$gsm."','".$$diaWidth."','".$$orderId."','".$$rollWgt."','".$$rollRate."','".$$rollAmt."',12,'".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$machineNoId."','".$$rack."','".$$shelf."','".$$colorId."','".$$colorRange."','".$$stichLn."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$id.",".$dtls_id.",'".$$orderId."','".$$BookWithoutOrd."',84,'".$$barcodeNo."','".$$rollId."','".$$rollNo."','".$$rollWgt."','".$$rollRate."','".$$rollAmt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			

			$barcodeNos.=$$barcodeNo."__".$dtls_id.",";
			$prodData_array[$$productId]+=$$rollWgt;
			$all_prod_id.=$$productId.",";
			$all_roll_id.=$$rolltableId.",";
			
			//$transactionID = $transactionID+1;
			//$dtls_id = $dtls_id+1;
			//$id_roll = $id_roll+1;
			//$id_prop = $id_prop+1;
			
		}
		
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",chop($all_prod_id,','))));
		$prodIssueResult=sql_select("select id, current_stock,avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id) and company_id=$cbo_company_id");
		$field_array_prod_update = "last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		
		foreach($prodIssueResult as $row)
		{
			$issue_rtn_qty=$prodData_array[$row[csf('id')]];
			$issue_rtn_value=$issue_rtn_qty*$row[csf('avg_rate_per_unit')];
			$current_stock=$row[csf('current_stock')]+$issue_rtn_qty;
			$current_stock_value=$row[csf('stock_value')]+$issue_rtn_value;
			$avg_rate_per_unit=$current_stock_value/$current_stock;
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($issue_rtn_qty."*'".$current_stock."'*'".$avg_rate_per_unit."'*'".$current_stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		$all_roll_id_arr=array_unique(explode(",",chop($all_roll_id,',')));
		foreach($all_roll_id_arr as $roll_id)
		{
			$roll_id_array[]=$roll_id;
			$data_array_roll_update[$roll_id]=explode("*",("1*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
		}
			
		
		
		
		//	echo "insert into order_wise_pro_details (".$field_array_prop.") values ".$data_array_prop;die;
		
		$rID=$rID2=$rID3=$rID4=$rID5=$prodUpdate=$rollUpdate=true;
		$rID=sql_insert("inv_receive_master",$field_array,$data_array,0);
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));
		//echo "10** $rollUpdate";die;
		//echo "10** insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
		//oci_rollback($con);
	  	//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$prodUpdate."&&".$rollUpdate;die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $rollUpdate)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_receive_system_id[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $rollUpdate)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_receive_system_id[0]."**".substr($barcodeNos,0,-1);
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
		$all_prod_id="";
		$field_array="receive_date*challan_no*updated_by*update_date";
		$data_array=$txt_issue_rtn_date."*".$txt_challan_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans="id, mst_id, receive_basis, pi_wo_batch_no, company_id, prod_id, item_category, transaction_type, transaction_date, store_id, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, machine_id, rack, self, inserted_by, insert_date";
		
		
		//$dtls_id=return_next_id( "id", "pro_grey_prod_entry_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, prod_id, febric_description_id, gsm, width, order_id, grey_receive_qnty, rate, amount, uom, yarn_lot, yarn_count, brand_id, machine_no_id, rack, self, color_id, color_range_id, stitch_length, inserted_by, insert_date";
		
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, booking_without_order, entry_form, barcode_no, roll_id, roll_no, qnty, rate, amount, inserted_by, insert_date";
		$field_array_roll_update="roll_used*is_returned*updated_by*update_date";
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 ); 
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		for($j=1;$j<=$tot_row;$j++)
		{
			$recvBasis="recvBasis_".$j;
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$BookWithoutOrd="BookWithoutOrd_".$j;
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
			$febDescripId="febDescripId_".$j;
			$machineNoId="machineNoId_".$j;
			$gsm="gsm_".$j;
			$diaWidth="diaWidth_".$j;
			$rollRate="rollRate_".$j;
			$rollAmt="rollAmt_".$j;
			$colorRange="colorRange_".$j;
			
			if(str_replace("'","",$$dtlsId)=="")
			{
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,4,".$txt_issue_rtn_date.",".$cbo_to_store.",'".$$brandId."',12,'".$$rollWgt."','".$$rollRate."','".$$rollAmt."',12,'".$$rollWgt."','".$$rollRate."','".$$rollAmt."','".$$rollWgt."','".$$rollAmt."','".$$machineNoId."','".$$rack."','".$$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transactionID.",4,84,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$transactionID.",'".$$productId."','".$$febDescripId."','".$$gsm."','".$$diaWidth."','".$$orderId."','".$$rollWgt."','".$$rollRate."','".$$rollAmt."',12,'".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$machineNoId."','".$$rack."','".$$shelf."','".$$colorId."','".$$colorRange."','".$$stichLn."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$update_id.",".$dtls_id.",'".$$orderId."','".$$BookWithoutOrd."',84,'".$$barcodeNo."','".$$rollId."','".$$rollNo."','".$$rollWgt."','".$$rollRate."','".$$rollAmt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
	
				$barcodeNos.=$$barcodeNo."__".$dtls_id.",";
				$prodData_array[$$productId]+=$$rollWgt;
				$all_prod_id.=$$productId.",";
				$all_roll_id.=$$rolltableId.",";
				
				//$transactionID = $transactionID+1;
				//$dtls_id = $dtls_id+1;
				//$id_roll = $id_roll+1;
				//$id_prop = $id_prop+1;
			}
		}
		
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",chop($all_prod_id,','))));
		if($all_prod_id=="") $all_prod_id=0;
		//echo "10**"."select id, current_stock,avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id) and company_id=$cbo_company_id";die;
		$prodIssueResult=sql_select("select id, current_stock,avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id) and company_id=$cbo_company_id");
		$field_array_prod_update = "last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		
		foreach($prodIssueResult as $row)
		{
			$issue_rtn_qty=$prodData_array[$row[csf('id')]];
			$issue_rtn_value=$issue_rtn_qty*$row[csf('avg_rate_per_unit')];
			$current_stock=$row[csf('current_stock')]+$issue_rtn_qty;
			$current_stock_value=$row[csf('stock_value')]+$issue_rtn_value;
			$avg_rate_per_unit=$current_stock_value/$current_stock;
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($issue_rtn_qty."*'".$current_stock."'*'".$avg_rate_per_unit."'*'".$current_stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		$all_roll_id=chop($all_roll_id,',');
		if(!empty($all_roll_id))
		{
			$all_roll_id_arr=array_unique(explode(",",$all_roll_id));
			foreach($all_roll_id_arr as $roll_id)
			{
				$roll_id_array[]=$roll_id;
				$data_array_roll_update[$roll_id]=explode("*",("1*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		
		$rID=$rID2=$rID3=$rID4=$rID5=$prodUpdate=$rollUpdate=true;
		$rID=sql_update("inv_receive_master",$field_array,$data_array,"id",$update_id,0);
		if($data_array_trans!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		}
		if($data_array_dtls!="")
		{
			$rID3=sql_insert("pro_grey_prod_entry_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		if($data_array_roll!="")
		{
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}
		if($data_array_prop!="")
		{
			$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		}
		
		if(count($prod_id_array)>0)
		{
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		}
		
		if(count($roll_id_array)>0)
		{
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));
		}
		
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$prodUpdate."&&".$rollUpdate."&&".$data_array_roll;die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $rollUpdate)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $prodUpdate && $rollUpdate)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1);
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



if($action=="populate_issue_data")
{
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	/*$inserted_roll=sql_select("select b.barcode_no from inv_item_transfer_dtls a,pro_roll_details b where a.id=b.dtls_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=82");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[$inf[csf('barcode_no')]]=$inf[csf('barcode_no')];
	}
	
	$scanned_barcode_data=sql_select("select a.id, a.barcode_no, a.dtls_id, b.trans_id from pro_roll_details a, inv_item_transfer_dtls b where a.dtls_id=b.id and a.entry_form=82 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($scanned_barcode_data as $row)
	{
		$scanned_barcode_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$barcode_dtlsId_array[$row[csf('barcode_no')]]=$row[csf('dtls_id')];
		$barcode_trnasId_array[$row[csf('barcode_no')]]=$row[csf('trans_id')];
		$barcode_rollTableId_array[$row[csf('barcode_no')]]=$row[csf('id')];
	}*/
	
	
	
	$issue_data=sql_select("select a.id, a.barcode_no from pro_roll_details a, inv_grey_fabric_issue_dtls b, inv_issue_master c
	where a.dtls_id=b.id and b.mst_id=c.id and a.entry_form in(61) and c.entry_form in(61) and a.status_active=1 and a.is_deleted=0 and a.roll_used=0 and a.is_returned=0 and c.issue_number='$data'");
	$issue_barcode="";
	foreach($issue_data as $row)
	{
		if($row[csf('barcode_no')]!="") $issue_barcode.=$row[csf('barcode_no')].",";
		$issue_data_array[$row[csf('barcode_no')]]['barcode_no']=$row[csf('barcode_no')];
		$issue_data_array[$row[csf('barcode_no')]]['id']=$row[csf('id')];
	}
	$issue_barcode=chop($issue_barcode,",");
	
	
	$batch_rcv_barcode=return_library_array("select barcode_no from pro_roll_details  where entry_form=62 and roll_split_from=0 and status_active=1 and is_deleted=0 and barcode_no in($issue_barcode)","barcode_no","barcode_no");

	$transPoIds=sql_select("select barcode_no, po_breakdown_id,entry_form from pro_roll_details where entry_form=83 and status_active=1 and is_deleted=0 and barcode_no in($issue_barcode) and re_transfer=0");
	$transPoIdsArr=array(); $transferedRollStatus=array();
	foreach($transPoIds as $row)
	{
		$transPoIdsArr[$row[csf("barcode_no")]]=$row[csf("po_breakdown_id")];
		$transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
		$transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
	}
	
	
	$transBoIds=sql_select("select c.barcode_no, c.po_breakdown_id, c.entry_form, b.buyer_id, b.id, b.booking_no from wo_non_ord_samp_booking_mst b, pro_roll_details c where b.id=c.po_breakdown_id and c.entry_form=110 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode)");
	$transBoIdsArr=array();
	foreach($transBoIds as $row)
	{
		$transBoIdsArr[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]=$row[csf("buyer_id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];

		$transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
		$transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
	}

	
	/*$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.color_range_id) as color_range_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.rate, c.amount
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode) 
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty, c.rate, c.amount");*/
	
	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.id as dtls_id, b.prod_id as prod_id, b.body_part_id as body_part_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.rack as rack, b.self as self, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.rate, c.amount, c.booking_without_order
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode)");
	
	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		
		foreach($data_array as $row)
		{
			if($batch_rcv_barcode[$row[csf('barcode_no')]]=="")
			{
				if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==22 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
				{
					$receive_basis="Independent";
					$receive_basis_id=0;
				}
				else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==2)) 
				{
					$receive_basis="Booking";
					$receive_basis_id=2;
				}
				else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
				{
					$receive_basis="Knitting Plan";
					$receive_basis_id=3;
				}
				else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==1) 
				{
					$receive_basis="PI";
					$receive_basis_id=1;
				}
				
				if($row[csf("roll_id")]==0) 
				{
					$roll_id=$row[csf("roll_tbl_id")];
				}
				else
				{
					$roll_id=$row[csf("roll_id")];
				}
				
				$color='';
				$color_id=explode(",",$row[csf('color_id')]);
				foreach($color_id as $val)
				{
					if($val>0) $color.=$color_arr[$val].",";
				}
				$color=chop($color,',');
				if($row[csf("knitting_source")]==1)
				{
					$knitting_company_name=$company_name_array[$row[csf("knitting_company")]];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$knitting_company_name=$supplier_arr[$row[csf("knitting_company")]];;
				}
				
				$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];//entry_form $machine_arr
				
				$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];
				

				if($transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] !="")
				{
					if($transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] == "83")
					{
						$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$transPoIdsArr[$row[csf("barcode_no")]]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['job_no']."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['buyer_name']."**".$buyer_arr[$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['buyer_name']]."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['po_number']."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['file_no']."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$row[csf('booking_without_order')]."__";
					}
					else
					{
						$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$transBoIdsArr[$row[csf("barcode_no")]]["booking_no"]."**".$transBoIdsArr[$row[csf("barcode_no")]]["po_breakdown_id"]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$transBoIdsArr[$row[csf("barcode_no")]]["po_breakdown_id"]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."** **".$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]."**".$buyer_arr[$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]]."** ** **".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**1__";
					}
				}
				else
				{
					$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$row[csf("po_breakdown_id")]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$row[csf("po_breakdown_id")]]['job_no']."**".$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']."**".$buyer_arr[$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']]."**".$po_details_array[$row[csf("po_breakdown_id")]]['po_number']."**".$po_details_array[$row[csf("po_breakdown_id")]]['file_no']."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$row[csf('booking_without_order')]."__";
				}
			}
			else
			{
				$barcodeData.=-1 ."__";
			}
		}
		$barcodeData=implode("__",array_unique(explode("__",chop($barcodeData,"__"))));
		echo trim($barcodeData);
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="populate_barcode_data")
{
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	
	
	
	$issue_data=sql_select("select a.id, a.barcode_no from pro_roll_details a where a.entry_form in(61) and a.status_active=1 and a.is_deleted=0 and a.roll_used=0 and a.is_returned=0 and a.barcode_no in($data)");
	$issue_barcode="";
	foreach($issue_data as $row)
	{
		if($row[csf('barcode_no')]!="") $issue_barcode.=$row[csf('barcode_no')].",";
		$issue_data_array[$row[csf('barcode_no')]]['barcode_no']=$row[csf('barcode_no')];
		$issue_data_array[$row[csf('barcode_no')]]['id']=$row[csf('id')];
	}
	$issue_barcode=chop($issue_barcode,",");
	
	
	$batch_rcv_barcode=return_library_array("select barcode_no from pro_roll_details  where entry_form=62 and roll_split_from=0 and status_active=1 and is_deleted=0 and barcode_no in($issue_barcode)","barcode_no","barcode_no");
	
	$transPoIds=sql_select("select barcode_no, po_breakdown_id, entry_form from pro_roll_details where entry_form=83 and status_active=1 and is_deleted=0 and barcode_no in($issue_barcode) and re_transfer=0");
	$transPoIdsArr=array();
	foreach($transPoIds as $row)
	{
		$transPoIdsArr[$row[csf("barcode_no")]]=$row[csf("po_breakdown_id")];
		$transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
		$transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
	}
	
	
	$transBoIds=sql_select("select c.barcode_no, c.po_breakdown_id,c.entry_form, b.buyer_id, b.id, b.booking_no from wo_non_ord_samp_booking_mst b, pro_roll_details c where b.id=c.po_breakdown_id and c.entry_form=110 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode)");
	$transBoIdsArr=array();
	foreach($transBoIds as $row)
	{
		$transBoIdsArr[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]=$row[csf("buyer_id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];

		$transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
		$transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
	}
	
	/*$transBoIds=sql_select("select barcode_no, po_breakdown_id from pro_roll_details where entry_form=110 and status_active=1 and is_deleted=0 and barcode_no in($issue_barcode)");
	$transBoIdsArr=array();
	foreach($transBoIds as $row)
	{
		$transBoIdsArr[$row[csf("barcode_no")]]=$row[csf("po_breakdown_id")];
	}*/
	
	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.id as dtls_id, b.prod_id as prod_id, b.body_part_id as body_part_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.rack as rack, b.self as self, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.rate, c.amount, c.booking_without_order
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode)");
	
	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		
		foreach($data_array as $row)
		{
			if($batch_rcv_barcode[$row[csf('barcode_no')]]=="")
			{
				if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==22 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
				{
					$receive_basis="Independent";
					$receive_basis_id=0;
				}
				else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==2)) 
				{
					$receive_basis="Booking";
					$receive_basis_id=2;
				}
				else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
				{
					$receive_basis="Knitting Plan";
					$receive_basis_id=3;
				}
				else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==1) 
				{
					$receive_basis="PI";
					$receive_basis_id=1;
				}
				else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==11) 
				{
					$receive_basis="Service Booking Outbound";
					$receive_basis_id=11;
				}
				
				
				if($row[csf("roll_id")]==0) 
				{
					$roll_id=$row[csf("roll_tbl_id")];
				}
				else
				{
					$roll_id=$row[csf("roll_id")];
				}
				
				$color='';
				$color_id=explode(",",$row[csf('color_id')]);
				foreach($color_id as $val)
				{
					if($val>0) $color.=$color_arr[$val].",";
				}
				$color=chop($color,',');
				if($row[csf("knitting_source")]==1)
				{
					$knitting_company_name=$company_name_array[$row[csf("knitting_company")]];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$knitting_company_name=$supplier_arr[$row[csf("knitting_company")]];;
				}
				
				//need to develop transfer order and transfer sample
				
				
				
				//rate, c.amount
				
				$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];//entry_form $machine_arr
				$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];
				

				if($transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] !="")
				{
					if($transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] == "83")
					{
						$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$transPoIdsArr[$row[csf("barcode_no")]]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['job_no']."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['buyer_name']."**".$buyer_arr[$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['buyer_name']]."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['po_number']."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['file_no']."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$row[csf('booking_without_order')]."__";
					}
					else
					{
						$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$transBoIdsArr[$row[csf("barcode_no")]]["booking_no"]."**".$transBoIdsArr[$row[csf("barcode_no")]]["po_breakdown_id"]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$transBoIdsArr[$row[csf("barcode_no")]]["po_breakdown_id"]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."** **".$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]."**".$buyer_arr[$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]]."** ** **".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**1__";
					}
				}
				else
				{
					$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$row[csf("po_breakdown_id")]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$row[csf("po_breakdown_id")]]['job_no']."**".$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']."**".$buyer_arr[$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']]."**".$po_details_array[$row[csf("po_breakdown_id")]]['po_number']."**".$po_details_array[$row[csf("po_breakdown_id")]]['file_no']."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$row[csf('booking_without_order')]."__";
				}				
			}
			else
			{
				$barcodeData.=-1 ."__";
			}
		}
		$barcodeData=implode("__",array_unique(explode("__",chop($barcodeData,"__"))));
		echo trim($barcodeData);
	}
	else
	{
		echo "0";
	}
	exit();
}





if($action=="populate_barcode_data_update")
{
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$scanned_barcode_update_data=sql_select("select a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id
	from pro_roll_details a
	where a.entry_form in(84) and a.status_active=1 and a.is_deleted=0 and  a.mst_id=$data");
	foreach($scanned_barcode_update_data as $row)
	{
		if($row[csf('barcode_no')]!="") $issue_barcode.=$row[csf('barcode_no')].",";
		$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
	}
	$issue_barcode=chop($issue_barcode,",");
	//echo $issue_barcode;die;
	$issue_data=sql_select("select a.id, a.barcode_no from pro_roll_details a where a.entry_form in(61) and a.status_active=1 and a.is_deleted=0 and a.roll_used=1 and a.barcode_no in($issue_barcode)");
	foreach($issue_data as $row)
	{
		$issue_data_array[$row[csf('barcode_no')]]['barcode_no']=$row[csf('barcode_no')];
		$issue_data_array[$row[csf('barcode_no')]]['id']=$row[csf('id')];
	}
	
	$transPoIds=sql_select("select barcode_no, po_breakdown_id, entry_form from pro_roll_details where entry_form=83 and status_active=1 and is_deleted=0 and barcode_no in($issue_barcode) and re_transfer=0");
	$transPoIdsArr=array();
	foreach($transPoIds as $row)
	{
		$transPoIdsArr[$row[csf("barcode_no")]]=$row[csf("po_breakdown_id")];

		$transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
		$transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
	}
	
	
	$transBoIds=sql_select("select c.barcode_no, c.po_breakdown_id, c.entry_form, b.buyer_id, b.id, b.booking_no from wo_non_ord_samp_booking_mst b, pro_roll_details c where b.id=c.po_breakdown_id and c.entry_form=110 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode)");
	$transBoIdsArr=array();
	foreach($transBoIds as $row)
	{
		$transBoIdsArr[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]=$row[csf("buyer_id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
		$transBoIdsArr[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];

		$transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] = $row[csf("barcode_no")];
		$transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] = $row[csf("entry_form")];
	}

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.id as dtls_id, b.prod_id as prod_id, b.body_part_id as body_part_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, b.rack as rack, b.self as self, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.rate, c.amount, c.booking_without_order
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_barcode)");
	
	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		
		foreach($data_array as $row)
		{
			if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==22 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
			{
				$receive_basis="Independent";
				$receive_basis_id=0;
			}
			else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==2)) 
			{
				$receive_basis="Booking";
				$receive_basis_id=2;
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
			{
				$receive_basis="Knitting Plan";
				$receive_basis_id=3;
			}
			else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==1) 
			{
				$receive_basis="PI";
				$receive_basis_id=1;
			}
			else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==11) 
			{
				$receive_basis="Service Booking Outbound";
				$receive_basis_id=11;
			}
			
			if($row[csf("roll_id")]==0) 
			{
				$roll_id=$row[csf("roll_tbl_id")];
			}
			else
			{
				$roll_id=$row[csf("roll_id")];
			}
			
			$color='';
			$color_id=explode(",",$row[csf('color_id')]);
			foreach($color_id as $val)
			{
				if($val>0) $color.=$color_arr[$val].",";
			}
			$color=chop($color,',');
			if($row[csf("knitting_source")]==1)
			{
				$knitting_company_name=$company_name_array[$row[csf("knitting_company")]];
			}
			else if($row[csf("knitting_source")]==3)
			{
				$knitting_company_name=$supplier_arr[$row[csf("knitting_company")]];
			}
			
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];//entry_form $machine_arr
			$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];
			

			if($transferedRollStatus[$row[csf("barcode_no")]]["barcode_no"] !="")
			{
				if($transferedRollStatus[$row[csf("barcode_no")]]["entry_form"] == "83")
				{
					$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$transPoIdsArr[$row[csf("barcode_no")]]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['job_no']."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['buyer_name']."**".$buyer_arr[$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['buyer_name']]."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['po_number']."**".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['file_no']."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$row[csf('booking_without_order')]."__";
				}
				else
				{
					$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$transBoIdsArr[$row[csf("barcode_no")]]["booking_no"]."**".$transBoIdsArr[$row[csf("barcode_no")]]["id"]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$transBoIdsArr[$row[csf("barcode_no")]]["po_breakdown_id"]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."** **".$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]."**".$buyer_arr[$transBoIdsArr[$row[csf("barcode_no")]]["buyer_id"]]."** ** **".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**1__";
				}
			}
			else
			{
				$barcodeData.=$row[csf('id')]."**".$row[csf("company_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("store_id")]."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$row[csf("po_breakdown_id")]."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$row[csf("po_breakdown_id")]]['job_no']."**".$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']."**".$buyer_arr[$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']]."**".$po_details_array[$row[csf("po_breakdown_id")]]['po_number']."**".$po_details_array[$row[csf("po_breakdown_id")]]['file_no']."**".$row[csf("color_id")]."**".$store_arr[$row[csf("store_id")]]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$machine_arr[$row[csf("machine_no_id")]]."**".$row[csf("machine_no_id")]."**".$row[csf("entry_form")]."**".$issue_data_array[$row[csf('barcode_no')]]['id']."**".$row[csf('rate')]."**".$row[csf('amount')]."**".$row[csf('color_range_id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$row[csf('booking_without_order')]."__";
			}
		}
		$barcodeData=implode("__",array_unique(explode("__",chop($barcodeData,"__"))));
		echo trim($barcodeData);
	}
	else
	{
		echo "0";
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
                    <th>Company</th>
                    <th>Date Range</th>
                    <th>Issue Return No</th>
                    <th>Barcode No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                    	<input type="hidden" name="hidden_system_id" id="hidden_system_id">  
                    </th> 
                </thead>
                <tr class="general">
                    
                    <td align="center">	
                    	<? 
							echo create_drop_down( "cbo_company_id", 160, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
						?>
                    </td> 
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>    
                    <td align="center">				
                        <input type="text" style="width:120px" class="text_boxes"  name="txt_issue_no" id="txt_issue_no" />	
                    </td>
                    <td align="center">				
                        <input type="text" style="width:110px" class="text_boxes"  name="txt_barcode_no" id="txt_barcode_no" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_barcode_no').value, 'create_challan_search_list_view', 'search_div', 'grey_fabric_issue_rtn_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
	$company_id=$data[0];
	$issue_no=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$barcode_no =$data[4];
	$str_cond="";
	if($company_id>0){ $str_cond=" and a.company_id=$company_id";} else { echo "Please Select Company"; die;}
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$str_cond.=" and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd","-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$str_cond.=" and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	
	
	$search_field_cond="";
	if(trim($issue_no)!="")
	{
		$str_cond.=" and a.recv_number like '%$issue_no'";
	}
	if(trim($barcode_no)!="")
	{
		$str_cond.=" and c.barcode_no=$barcode_no";
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
	
	$sql = "select a.id, $year_field a.recv_number_prefix_num, a.recv_number, a.company_id, a.store_id, a.challan_no, a.receive_date
	from   inv_receive_master a, pro_roll_details c
	where a.id=c.mst_id and a.entry_form=84 and c.entry_form=84 and a.status_active=1 and a.is_deleted=0 $str_cond
	group by a.id, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.company_id, a.store_id, a.challan_no, a.receive_date
	order by a.id desc"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="150">Company</th>
            <th width="140">Issue No</th>
            <th width="50">Year</th>
            <th width="150">To Store</th>
            <th width="100">Challan</th>
            <th>Transfer date</th>
        </thead>
	</table>
	<div style="width:760px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>');"> 
                    <td width="50" align="center"><? echo $i; ?></td>
                    <td width="150" align="center"><p>&nbsp;<? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="140" align="center"><p><? echo $row[csf('recv_number')]; ?></p></td>
                    <td width="50"><p><? echo $row[csf('year')]; ?>&nbsp;</p></td>
                    <td width="150"><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
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
	//$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, batch_no, issue_purpose from inv_issue_master where id=$data and entry_form=61";
	$sql = "select a.id, a.recv_number, a.company_id, a.receive_date, a.challan_no, a.store_id 
	from   inv_receive_master a
	where a.entry_form=84 and a.id=$data"; 
	//echo $sql;die;
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_system_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_to_store').val(".$row[csf("store_id")].");\n";
		echo "$('#txt_issue_rtn_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		
		
  	}
	exit();	
}


if($action=="populate_master_from_data")
{
	$data_ref=explode("__",$data);
	if($data_ref[1]==1) $sql_cond=" and c.barcode_no in(".$data_ref[0].")"; else  $sql_cond=" and a.issue_number="."'".$data_ref[0]."'";
	$sql = "select a.company_id
	from  inv_issue_master a,  inv_grey_fabric_issue_dtls b, pro_roll_details c 
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 $sql_cond
	group by a.company_id"; 
	//echo $sql;die;
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
  	}
	exit();	
}

if($action=="barcode_nos")
{
	if($db_type==0) 
	{
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","pro_roll_details","entry_form=84 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2) 
	{
		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","pro_roll_details","entry_form=84 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
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
                    <th>Company</th>
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
						echo create_drop_down( "cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $company_id, "",$disable );
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view', 'search_div', 'grey_fabric_issue_rtn_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
	
	$company_id=trim($data[0]);
	$order_no=$data[1];
	$file_no =trim($data[2]);
	$ref_no =trim($data[3]);
	$barcode_no =trim($data[4]);
	
	
	//echo $store_id.jahid;die;
	

	$search_field_cond="";
	if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	if($company_id!=0) $search_field_cond.=" and a.company_id=$company_id";
	
	
	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}
	$batch_rcv_barcode=return_library_array("select barcode_no from pro_roll_details  where entry_form=62 and roll_split_from=0 and status_active=1 and is_deleted=0","barcode_no","barcode_no");
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	
	$sql="SELECT a.issue_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping
		FROM inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and c.roll_used=0 and a.entry_form in(61) and c.entry_form in(61) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond";
	 
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="250">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order No</th>
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
            if($batch_rcv_barcode[$row[csf('barcode_no')]]=="")
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
                    <td width="30" align="center">
                        <? echo $i; ?>
                         <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
                    </td>
                    <td width="250"><p><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
                    <td width="110"><p><? echo $row[csf('po_number')]; ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo $row[csf('file_no')]; ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo $row[csf('grouping')]; ?>&nbsp;</p></td>
                    <td width="70" align="center"><p><? echo change_date_format($row[csf('pub_shipment_date')]); ?>&nbsp;</p></td>
                    <td width="90"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
                    <td width="50" align="center"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
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


if($action=="challan_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;
	?> 
	<script>
		function js_set_value(str) 
		{
			$('#hidden_issue_id').val(str);
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
<div align="center" style="width:860px;">
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:860px; margin-left:2px;">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="820" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="220">Company</th>
                    <th width="160">Issue No</th>
                    <th width="300">Issue Date Range</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_issue_id" id="hidden_issue_id">  
                    </th> 
                </thead>
                <tr class="general">
                    <td>
                    <?
						echo create_drop_down( "cbo_company_id", 200, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", $company_id, "", $disable );
					?>
                    </td>
                    <td align="center">				
                        <input type="text" style="width:140px" class="text_boxes"  name="txt_issue_no" id="txt_issue_no" />	
                    </td> 
                    <td align="center">				
                        <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:80px" placeholder="From Date" readonly>&nbsp;To&nbsp;
                    	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px"placeholder="To Date" readonly>	
                    </td>
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_issue_search_list_view', 'search_div', 'grey_fabric_issue_rtn_roll_wise_controller', 'setFilterGrid(\'tbl_issue_list\',-1);')" style="width:100px;" />
                     </td>
                </tr>
                <tr>
                    <td colspan="4" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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
	
	$company_id=trim($data[0]);
	$issue_no=$data[1];
	$from_date =trim($data[2]);
	$to_date =trim($data[3]);
	
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'YYYY-MM-DD');
		$to_date=change_date_format($to_date,'YYYY-MM-DD');
	}
	else
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	
	//echo $store_id.jahid;die;
	

	$search_field_cond="";
	if($company_id!=0)
	{
		$search_field_cond.=" and a.company_id=$company_id";
	} 
	else
	{
		echo "Select Company First .";die;
	}
	
	if($issue_no!="") $search_field_cond.=" and a.issue_number like '%$issue_no%'";
	if($from_date!="" && $to_date!="") $search_field_cond.=" and a.issue_date between '$from_date' and '$to_date'";
	//echo $search_field_cond;die;
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	$sql="SELECT a.id, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_purpose, a.batch_no, a.issue_date, a.company_id
		FROM inv_issue_master a
		WHERE a.entry_form in(61) and a.status_active=1 and a.is_deleted=0  $search_field_cond order by a.id desc";
	 
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table">
        <thead>
            <th width="50">SL</th>
            <th width="140">Issue Number</th>
            <th width="120">Dyeing Source</th>
            <th width="150">Dyeing Company</th>
            <th width="150">Issue Purpose</th>
            <th width="100">Batch No</th>
            <th >Issue Date</th>
        </thead>
	</table>
	<div style="width:860px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="840" class="rpt_table" id="tbl_issue_list">  
		<?
        $i=1;
        foreach ($result as $row)
        {  
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('issue_number')]; ?>,<? echo $row[csf('company_id')]; ?>,<? echo $row[csf('id')]; ?>')"> 
                <td width="50" align="center"><? echo $i; ?></td>
                <td width="140"><p><? echo $row[csf('issue_number')]; ?>&nbsp;</p></td>
                <td width="120"><p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?>&nbsp;</p></td>
                <td width="150"><p><? if($row[csf('knit_dye_source')]==1) echo $company_arr[$row[csf('knit_dye_company')]]; else echo $supplier_arr[$row[csf('knit_dye_company')]]; ?>&nbsp;</p></td>
                <td width="150"><p><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?>&nbsp;</p></td>
                <td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</p></td>
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


if($action=="grey_issue_return_print")
{
	echo load_html_head_contents("Grey Fabric Transfer Entry Report","../../../", 1, 1, $unicode,'',''); 
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
	$store_library=return_library_array("select id, store_name from  lib_store_location","id","store_name");
	$supplier_library=return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no, b.grouping as int_ref_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
		$po_details_array[$row[csf("po_id")]]['int_ref_no']=$row[csf("int_ref_no")];
	}
	
	/*$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}*/
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
        $issue_rtn_barcode_no="";
        $scanned_barcode_update_data=sql_select("select a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id
	from pro_roll_details a
	where a.entry_form in(84) and a.status_active=1 and a.is_deleted=0 and  a.mst_id=$update_id");
	foreach($scanned_barcode_update_data as $row)
	{
		if($row[csf('barcode_no')]!="") $issue_rtn_barcode_no.=$row[csf('barcode_no')].",";
		//$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		//$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		//$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
	}
	$issue_rtn_barcode_no=chop($issue_rtn_barcode_no,",");
        $transPoIds=sql_select("select barcode_no, po_breakdown_id from pro_roll_details where entry_form=83 and status_active=1 and is_deleted=0 and barcode_no in($issue_rtn_barcode_no) and re_transfer=0");
	$transPoIdsArr=array();
	foreach($transPoIds as $row)
	{
		$transPoIdsArr[$row[csf("barcode_no")]]=$row[csf("po_breakdown_id")];
	}
//	$issue_return_barcode_sql=sql_select("select c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id");
//	$issue_rtn_barcode_no="";
//	foreach($issue_return_barcode_sql as $row)
//	{
//		$issue_rtn_barcode_no.=$row[csf("barcode_no")].",";
//	}
//	
//	$issue_rtn_barcode_no=implode(",",array_unique(explode(",",chop($issue_rtn_barcode_no,","))));
	
	/*$dtls_data=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.id as dtls_id, b.prod_id as prod_id, b.body_part_id as body_part_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id as po_id, c.qnty, c.rate, c.amount
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_rtn_barcode_no)");*/
	
			$dtls_data=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.id as dtls_id, b.prod_id as prod_id, b.body_part_id as body_part_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id as po_id, c.qnty, c.rate, c.amount
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_id=0 and c.barcode_no in($issue_rtn_barcode_no)");
	
	

	$dataArray=sql_select("select id, recv_number, company_id, store_id, receive_date, challan_no from inv_receive_master where id=$update_id");
	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Issue Return</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>System No:</strong></td>
                <td width="180"><? echo $dataArray[0][csf('recv_number')]; ?></td>
                <td width="130"><strong>Company:</strong></td>
                <td width="180"><? echo $company_array[$dataArray[0][csf('company_id')]]['shortname']; ?></td>
                <td width="130"><strong>Store Name:</strong></td>
                <td ><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Receive Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
                <td><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1450"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">Order <br />File/ Reference</th>
                <th width="75">Buyer <br />Job</th>
                <th width="80">Program/ Booking No</th>
                <th width="90">Production Basis</th>
                <th width="60">Knitting Company</th>
                <th width="50">Yarn Count</th>
                <th width="90">Yarn Brand</th>
                <th width="50">Lot No</th>
                <th width="100">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="260">Fabric Type</th>
                <th width="40">Stich Lenth</th>
                <th width="40">Fin GSM</th>
                <th width="40">Fab. Dia</th>
                <th width="60">MC No <br />Dia</th>
                <th width="50">Roll No</th>
                <th width="70">Barcode No</th>
                <th>QC Pass Qty</th>
            </thead>
            <tbody>
            <?
			$i=0;
			$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
			foreach($dtls_data as $row)
			{
				$i++;
				?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td style="word-break:break-all;"><? echo $po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['po_number']."<br> F: ".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['file_no']." R: ".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['int_ref_no']; ?></td>
					<td style="word-break:break-all;"><? echo $buyer_library[$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['buyer_name']]. "<br>".$po_details_array[$transPoIdsArr[$row[csf("barcode_no")]]]['job_no']; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf('booking_no')]; ?></td>
					
					<td style="word-break:break-all;" align="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
					<td style="word-break:break-all;" align="center">
					<? 
					if($row[csf('knitting_source')]==1) echo $company_array[$row[csf('knitting_company')]]['shortname']; 
					else echo  $supplier_library[$row[csf('knitting_company')]]; 
					?></td>
                    <td style="word-break:break-all;" align="center">
					<? 
					//echo $yarn_count_arr[$row[csf('y_count')]]; 
					$yarn_count_array=array_unique(explode(",",$row[csf('yarn_count')]));
					$all_count="";
					foreach($yarn_count_array as $count_id)
					{
						$all_count.=$yarn_count_arr[$count_id].",";
					}
					$all_count=chop($all_count,",");
					echo $all_count;
					?></td>
					<td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf('yarn_lot')]; ?></td>
					<td style="word-break:break-all;">
					<? 
					$all_color_arr=array_unique(explode(",",$row[csf('color_id')]));
					$all_color="";
					foreach($all_color_arr as $color_id)
					{
						$all_color.=$color_library[$color_id].",";
					}
					$all_color=chop($all_color,",");
					echo $all_color;
					?></td>
					<td style="word-break:break-all;" align="center"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
					<td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('febric_description_id')]]." ".$composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
					<td style="word-break:break-all;"  align="center"><? echo $row[csf('gsm')]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf("width")]; ?></td>
					<td style="word-break:break-all;"><? echo "N :".$lib_mc_arr[$row[csf("machine_no_id")]]['no'].'<br />D :'.$lib_mc_arr[$row[csf("machine_no_id")]]['dia']; ?></td>
					<td align="center"><? echo $row[csf("roll_no")];?></td>
					<td style="word-break:break-all;"><?  echo $row[csf("barcode_no")]; ?></td>
					<td align="right"><? echo number_format($row[csf('qnty')],2,'.','');?></td>
				</tr>
				<?
				$tot_qty+=$row[csf('qnty')];
				
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th align="right" colspan="18"><strong>Total</strong></th>
                    <th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
            <br>
            <?
            echo signature_table(87, $data[0], "1450px");
         ?>
		</table>
	</div>
<?
exit();
}



if($action=="grey_issue_return_print_grouping")
{
	echo load_html_head_contents("Grey Fabric Transfer Entry Report","../../../", 1, 1, $unicode,'',''); 
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
	$store_library=return_library_array("select id, store_name from  lib_store_location","id","store_name");
	$supplier_library=return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	
	$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.file_no, b.grouping as int_ref_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($data_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
		$po_details_array[$row[csf("po_id")]]['int_ref_no']=$row[csf("int_ref_no")];
	}
	
	/*$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}*/
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	/*$issue_return_barcode_sql=sql_select("select c.barcode_no from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=84 and c.entry_form=84");*/
	
	
	$issue_return_barcode_sql=sql_select("select a.barcode_no from pro_roll_details a where a.entry_form in(84) and a.status_active=1 and a.is_deleted=0 and  a.mst_id=$update_id");
	
	$issue_rtn_barcode_no="";
	foreach($issue_return_barcode_sql as $row)
	{
		$issue_rtn_barcode_no.=$row[csf("barcode_no")].",";
	}
	
	$issue_rtn_barcode_no=implode(",",array_unique(explode(",",chop($issue_rtn_barcode_no,","))));
	if($db_type==0)
	{
		$dtls_data=sql_select("SELECT  a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, count(c.id) as total_roll, group_concat(c.id) as roll_id, group_concat(c.roll_no) as roll_no, c.po_breakdown_id as po_id, sum(c.qnty) as qnty, sum(c.amount) as amount
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.roll_id=0 and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_rtn_barcode_no)
		group by a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, c.po_breakdown_id");
	}
	else
	{
		$dtls_data=sql_select("SELECT  a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.febric_description_id as febric_description_id, b.machine_no_id as machine_no_id, b.gsm as gsm, b.width as width, b.color_id as color_id, b.color_range_id as color_range_id, b.yarn_lot as yarn_lot, b.yarn_count as yarn_count, b.stitch_length as stitch_length, b.brand_id as brand_id, count(c.id) as total_roll, listagg(cast(c.id as varchar(4000)),',') within group (order by c.id) as roll_id, listagg(cast(c.roll_no as varchar(4000)),',') within group (order by c.roll_no) as roll_no, c.po_breakdown_id as po_id, sum(c.qnty) as qnty, sum(c.amount) as amount
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.roll_id=0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($issue_rtn_barcode_no)
		group by a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, c.po_breakdown_id");
	}
	
	
	
	$dataArray=sql_select("select id, recv_number, company_id, store_id, receive_date, challan_no from inv_receive_master where id=$update_id");
	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Issue Return</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>System No:</strong></td>
                <td width="180"><? echo $dataArray[0][csf('recv_number')]; ?></td>
                <td width="130"><strong>Company:</strong></td>
                <td width="180"><? echo $company_array[$dataArray[0][csf('company_id')]]['shortname']; ?></td>
                <td width="130"><strong>Store Name:</strong></td>
                <td ><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Receive Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
                <td><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1380"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">Order <br />File/ Reference</th>
                <th width="75">Buyer <br />Job</th>
                <th width="80">Program/ Booking No</th>
                <th width="90">Production Basis</th>
                <th width="60">Knitting Company</th>
                <th width="50">Yarn Count</th>
                <th width="90">Yarn Brand</th>
                <th width="50">Lot No</th>
                <th width="100">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="260">Fabric Type</th>
                <th width="40">Stich Lenth</th>
                <th width="40">Fin GSM</th>
                <th width="40">Fab. Dia</th>
                <th width="60">MC No <br />Dia</th>
                <th width="50">Total Roll</th>
                <th>QC Pass Qty</th>
            </thead>
            <tbody>
            <?
			$i=0;
			$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
			foreach($dtls_data as $row)
			{
				$i++;
				?>
				<tr>
					<td align="center"><? echo $i; ?></td>
					<td style="word-break:break-all;"><? echo $po_details_array[$row[csf("po_id")]]['po_number']."<br> F: ".$po_details_array[$row[csf("po_id")]]['file_no']." R: ".$po_details_array[$row[csf("po_id")]]['int_ref_no']; ?></td>
					<td style="word-break:break-all;"><? echo $buyer_library[$po_details_array[$row[csf("po_id")]]['buyer_name']]. "<br>".$po_details_array[$row[csf("po_id")]]['job_no']; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf('booking_no')]; ?></td>
					
					<td style="word-break:break-all;" align="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
					<td style="word-break:break-all;" align="center">
					<? 
					if($row[csf('knitting_source')]==1) echo $company_array[$row[csf('knitting_company')]]['shortname']; 
					else echo  $supplier_library[$row[csf('knitting_company')]]; 
					?></td>
                    <td style="word-break:break-all;" align="center">
					<? 
					//echo $yarn_count_arr[$row[csf('y_count')]]; 
					$yarn_count_array=array_unique(explode(",",$row[csf('yarn_count')]));
					$all_count="";
					foreach($yarn_count_array as $count_id)
					{
						$all_count.=$yarn_count_arr[$count_id].",";
					}
					$all_count=chop($all_count,",");
					echo $all_count;
					?></td>
					<td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf('yarn_lot')]; ?></td>
					<td style="word-break:break-all;">
					<? 
					$all_color_arr=array_unique(explode(",",$row[csf('color_id')]));
					$all_color="";
					foreach($all_color_arr as $color_id)
					{
						$all_color.=$color_library[$color_id].",";
					}
					$all_color=chop($all_color,",");
					echo $all_color;
					?></td>
					<td style="word-break:break-all;" align="center"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
					<td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('febric_description_id')]]." ".$composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
					<td style="word-break:break-all;"  align="center"><? echo $row[csf('gsm')]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $row[csf("width")]; ?></td>
					<td style="word-break:break-all;"><? echo "N :".$lib_mc_arr[$row[csf("machine_no_id")]]['no'].'<br />D :'.$lib_mc_arr[$row[csf("machine_no_id")]]['dia']; ?></td>
					<td align="center"><? echo $row[csf("total_roll")];?></td>
					<td align="right"><? echo number_format($row[csf('qnty')],2,'.','');?></td>
				</tr>
				<?
				$tot_qty+=$row[csf('qnty')];
				
			}
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th align="right" colspan="17"><strong>Total</strong></th>
                    <th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
		</table>
	</div>
<?
exit();
}
?>

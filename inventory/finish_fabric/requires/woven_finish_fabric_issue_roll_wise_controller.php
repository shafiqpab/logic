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
	$store_sql="SELECT id,store_name,item_category_id from lib_store_location 
	where status_active=1 and is_deleted=0 and company_id='$data[0]' and (item_category_id  like '%2%' or item_category_id='2')";
	$store_result=sql_select($store_sql);
	$store_ids_arr=array();
	foreach ($store_result as $key => $row) 
	{
		$all_item_category_ids=$row[csf('item_category_id')];
		$all_item_category_arr=explode(',', $all_item_category_ids);		
		//echo "<pre>";print_r($all_item_category_arr);
		foreach ($all_item_category_arr as $key => $value) 
		{
			if ($value==2) 
			{
				// $store_ids_arr[$row[csf('id')]][$value]=$row[csf('id')];
				$store_ids_arr[$row[csf('id')]]=$row[csf('id')];
			}
		}
	}
	//echo "<pre>";print_r($store_ids_arr);
	$store_ids=implode(',', $store_ids_arr);

	echo create_drop_down( "cbo_store_name", 152, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id='$data[0]' and id in($store_ids)","id,store_name", 1, "--- Select Store ---", 1, "fnc_details_row_blank();" );
	//echo create_drop_down( "cbo_store_name", 152, "select id,store_name from lib_store_location where status_active=1 and is_deleted=0 and company_id='$data[0]'  and (item_category_id  like '%,2,%' or item_category_id='2')","id,store_name", 1, "--- Select Store ---", 1, "" );
	exit();
}
if ($action == "load_drop_down_location")
{
	echo create_drop_down("cbo_location_name", 152, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select Location --", 0, "");
	exit();
}
if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	$company_id=$data[1];
	
	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", 0, "","" );
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

if($action=="varible_inv_issue_requisition_madatory")
{
	$sql_issue_requisition_madatory=sql_select("select id, user_given_code_status  from variable_settings_inventory where company_name=$data and variable_list=24 and status_active=1 and is_deleted=0 and item_category_id = 3");
 	echo $sql_issue_requisition_madatory[0][csf("user_given_code_status")];
	die;
}

if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	for($j=1;$j<=$tot_row;$j++)
	{ 	
		$productId="productId_".$j;  
		$prod_ids .= $$productId.",";
		$barcodeNO="barcodeNo_".$j;
		$barcodeNOS.=$$barcodeNO.",";
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
	$barcodeNOS=chop($barcodeNOS,',');
	$barcodeNOS_array =  array_unique(explode(",", $barcodeNOS));
	$barcodeNOS_cond=""; $barcodeNOSCond="";
	if($db_type==2 && count($barcodeNOS_array)>999)
	{
		$barcodeNOS_array_chunk=array_chunk($barcodeNOS_array,999) ;
		foreach($barcodeNOS_array_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$barcodeNOSCond.=" barcode_no in($chunk_arr_value) or ";
		}
		$barcodeNOS_cond.=" and (".chop($barcodeNOSCond,'or ').")";
	}
	else
	{
		$barcodeNOS_cond=" and barcode_no in($barcodeNOS)";
	}

	$trans_check_sql = sql_select("select barcode_no,entry_form,po_breakdown_id,qnty from pro_roll_details where entry_form in ( 564 ) $barcodeNOS_cond and re_transfer =0 and status_active = 1 and is_deleted = 0");
	// union all select a.barcode_no,a.entry_form,a.po_breakdown_id, a.qnty from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in (7) and b.trans_id<>0 and a.re_transfer =0 and a.barcode_no in($barcodeNOS) and a.status_active = 1 and a.is_deleted = 0

	if($trans_check_sql[0][csf("barcode_no")] !="")
	{
		foreach ($trans_check_sql as $val)
		{
			$trans_po_barcode_check_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] = $val[csf("barcode_no")]."__".$val[csf("po_breakdown_id")];
			$actual_wgt_arr[$val[csf("barcode_no")]] = $val[csf("qnty")];
		}
	}
	else
	{
		echo "20**Sorry valid barcode no. not found for issue";
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
		
		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FFRI', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=71 and $year_cond=".date('Y',time())." order by id desc ", "issue_number_prefix","issue_number_prefix_num"));
		//$id=return_next_id( "id", "inv_issue_master", 1 ) ;
		
		$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),'WFIR',195,date("Y",time())));
				 
		$field_array="id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_purpose,entry_form,item_category,company_id,location_id,issue_date,knit_dye_source, knit_dye_company,req_no,store_id,inserted_by,insert_date";


		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_issue_purpose.",195,3,".$cbo_company_id.",".$cbo_location_name.",".$txt_issue_date.",".$cbo_dyeing_source.",".$cbo_dyeing_company.",'".str_replace("'","",$txt_rqn_no)."',".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$transactionID = return_next_id("id", "inv_transaction", 1);

		$field_array_trans = "id,mst_id,issue_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,entry_form,transaction_date, store_id, floor_id, room, rack, self,  bin_box,fabric_shade,body_part_id,order_uom,order_qnty, order_rate,order_amount, order_ile,order_ile_cost,cons_ile, cons_ile_cost,  cons_uom, cons_quantity, cons_rate, cons_amount,roll,batch_lot,batch_id,fabric_ref,rd_no,weight_type,cutable_width,weight_editable,width_editable,booking_no,wo_id, inserted_by,insert_date";
		
		//$dtls_id=return_next_id("id", "inv_finish_fabric_issue_dtls", 1);


		$field_array_dtls="id,mst_id,trans_id,prod_id,order_id,issue_qnty,store_id,batch_id,no_of_roll,fabric_description_id,original_gsm,original_width,inserted_by,insert_date";


		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, roll_no, batch_no, manual_roll_no,reprocess,prev_reprocess,rf_id,shrinkage_shade, inserted_by, insert_date,booking_no,booking_without_order,is_booking,job_id";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id,color_id,quantity,no_of_roll, inserted_by, insert_date";
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		
		$barcodeNos=''; $all_prod_id='';
		for($j=1;$j<=$tot_row;$j++)
		{
			$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$dtls_id=return_next_id_by_sequence( "INV_WV_FIN_FAB_ISS_DTLS_PK_SEQ", "inv_wvn_finish_fab_iss_dtls", $con) ;
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			 	
			$recvBasis="recvBasis_".$j;
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$colorId="colorId_".$j;
			$batchId="batchId_".$j;
			$hdnBatchName="hdnBatchName_".$j;
			$deterId="deterId_".$j;
			
			$rollNo="rollNo_".$j;
			$manualRollNo="manualRollNo_".$j;
			$knittingcomId="knittingcomId_".$j;
			$body_part="bodyPartId_".$j;
			$reProcess="reProcess_".$j;
			$bwoNo = "bwoNo_".$j;
			$booking_without_order_status = "booking_without_order_status_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			$binBox="binBox_".$j;
			$shade_id="shadeId_".$j;
			$jobId="jobId_".$j;

			$bookingIdOriginal="bookingIdOriginal_".$j;
			$bookingNoOriginal="bookingNoOriginal_".$j;
			$hdnRfId="hdnRfId_".$j;
			$hdnManualRollNo="hdnManualRollNo_".$j;
			$hdnFabricRef="hdnFabricRef_".$j;
			$hdnRdNo="hdnRdNo_".$j;
			$hdnOriginalGsm="hdnOriginalGsm_".$j;
			$hdnOriginalDia="hdnOriginalDia_".$j;  
			$hdnWeightEditable="hdnWeightEditable_".$j;
			$hdnCutWidth="hdnCutWidth_".$j;
			$weightTypeId="weightTypeId_".$j;
			$orderUomId="orderUomId_".$j;
			$hdnOrdRate="hdnOrdRate_".$j;
			$hdnOrdAmnt="hdnOrdAmnt_".$j;
			$hdnConsAmnt="hdnConsAmnt_".$j;
			$hdnConsRate="hdnConsRate_".$j;
			//$txtRemarks="txtRemarks_".$j;
			$hdnOrderIle="hdnOrderIle_".$j;
			$hdnOrderIleCost="hdnOrderIleCost_".$j;
			$hdnConsIle="hdnConsIle_".$j;
			$hdnConsIleCost="hdnConsIleCost_".$j; 
			$txt_roll=1;
			if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
			{
				if($booking_without_order_status == 1)
				{
					echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this booking no";
				}
				else{
					echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this order no";
				}
				disconnect($con);
				die;
			}

			if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format($$rollWgt,2,".",""))
			{
				echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".number_format($$rollWgt,2,".","") ."";
				disconnect($con);
				die;
			}
			
			if(str_replace("'","",$cbo_issue_purpose)==44) $reprocess_id=$$reProcess+1;
			else $reprocess_id=$$reProcess;
			
			$amount=$$hdnConsRate*$$rollWgt;
			
			
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$transactionID.",".$id.",".$cbo_issue_purpose.",'".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',3,2,195,".$txt_issue_date.",".$cbo_store_name.",'".$$floor."','".$$room."','".$$rack."','".$$self."','".$$binBox."','".$$shade_id."','".$$body_part."','".$$orderUomId."','".$$rollWgt."','".$$hdnOrdRate."','".$$hdnOrdAmnt."','".$$hdnOrderIle."','".$$hdnOrderIleCost."','".$$hdnConsIle."','".$$hdnConsIleCost."','".$$orderUomId."','".$$rollWgt."','".$$hdnConsRate."','".$$hdnConsAmnt."','".$txt_roll."','".$$hdnBatchName."','".$$batchId."','".$$hdnFabricRef."','".$$hdnRdNo."','".$$weightTypeId."','".$$hdnCutWidth."','".$$hdnWeightEditable."','".$$hdnOriginalDia."','".$$bookingNoOriginal."','".$$bookingIdOriginal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$transactionID.",'".$$productId."','".$$orderId."','".$$rollWgt."',".$cbo_store_name.",'".$$batchId."','".$$rollNo."','".$$deterId."','".$$hdnOriginalGsm."','".$$hdnOriginalDia."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",'".$$bookingIdOriginal."',195,'".$$rollWgt."','".$$rollWgt."','".$$rollNo."','".$$hdnBatchName."','".$$hdnManualRollNo."',".$reprocess_id.",".$$reProcess.",'".$$hdnRfId."','".$$shade_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','".$$bwoNo."',".$$booking_without_order_status.",1,".$$jobId.")";
			

			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$transactionID.",3,195,'".$dtls_id."','".$$orderId."','".$$productId."','".$$colorId."','".$$rollWgt."','".$$rollNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "')";

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
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]-$issue_qty;
			$stock_value=$row[csf('stock_value')]-$prodData_amoutArray[$row[csf('id')]];
			if($current_stock >0){
				$stock_rate = $stock_value/$current_stock;
			}else{
				$stock_rate =0;
				$stock_value=0;
			}

			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*'".$stock_rate."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		
		//echo "10**insert into inv_wvn_finish_fab_iss_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);die;
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;oci_rollback($con);die;
		$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
 		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		$rID3=sql_insert("inv_wvn_finish_fab_iss_dtls",$field_array_dtls,$data_array_dtls,0);
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rID5=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
		//echo"10**". bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
	    
		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$prodUpdate;oci_rollback($con);die;

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

		
		$field_array="issue_purpose*location_id*issue_date*knit_dye_source*store_id*knit_dye_company*req_no*updated_by*update_date";
		$data_array=$cbo_issue_purpose."*".$cbo_location_name."*".$txt_issue_date."*".$cbo_dyeing_source."*".str_replace("'","",$cbo_store_name)."*".$cbo_dyeing_company."*'".str_replace("'","",$txt_rqn_no)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		//$transactionID = return_next_id("id", "inv_transaction", 1);
		$field_array_trans = "id,mst_id,issue_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,entry_form,transaction_date, store_id, floor_id, room, rack, self,  bin_box,fabric_shade,body_part_id,order_uom,order_qnty, order_rate,order_amount, order_ile,order_ile_cost,cons_ile, cons_ile_cost,  cons_uom, cons_quantity, cons_rate, cons_amount,roll,batch_lot,batch_id,fabric_ref,rd_no,weight_type,cutable_width,weight_editable,width_editable,booking_no,wo_id, inserted_by,insert_date";

		$field_array_updatetrans="transaction_date*cons_quantity*cons_rate*cons_amount*floor_id*room*rack*self*bin_box*batch_id*fabric_shade*updated_by*update_date";
		
		//$dtls_id=return_next_id("id", "inv_finish_fabric_issue_dtls", 1);

		$field_array_dtls="id,mst_id,trans_id,prod_id,order_id,issue_qnty,store_id,batch_id,no_of_roll,fabric_description_id,original_gsm,original_width,inserted_by,insert_date";

		$field_array_updatedtls="issue_qnty*prod_id*order_id*store_id*batch_id*no_of_roll*fabric_description_id*original_gsm*original_width*updated_by*update_date";
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, roll_no, batch_no, manual_roll_no,reprocess,prev_reprocess,rf_id,shrinkage_shade, inserted_by, insert_date,booking_no,booking_without_order,is_booking,job_id";
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_updateroll="qnty*qc_pass_qnty*reprocess*prev_reprocess*booking_no*booking_without_order*shrinkage_shade*updated_by*update_date";
		
		$field_array_prop="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id,color_id,quantity,no_of_roll, inserted_by, insert_date";
		
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
			$colorId="colorId_".$j;
			$batchId="batchId_".$j;
			$hdnBatchName="hdnBatchName_".$j;
			$deterId="deterId_".$j;

			$dtlsId="dtlsId_".$j;
			$transId="transId_".$j;
			$rolltableId="rolltableId_".$j;
			
			$rollNo="rollNo_".$j;
			$manualRollNo="manualRollNo_".$j;
			$knittingcomId="knittingcomId_".$j;
			$body_part="bodyPartId_".$j;
			$reProcess="reProcess_".$j;
			$bwoNo = "bwoNo_".$j;
			$booking_without_order_status = "booking_without_order_status_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			$binBox="binBox_".$j;
			$shade_id="shadeId_".$j;
			$jobId="jobId_".$j;

			$bookingIdOriginal="bookingIdOriginal_".$j;
			$bookingNoOriginal="bookingNoOriginal_".$j;
			$hdnRfId="hdnRfId_".$j;
			$hdnManualRollNo="hdnManualRollNo_".$j;
			$hdnFabricRef="hdnFabricRef_".$j;
			$hdnRdNo="hdnRdNo_".$j;
			$hdnOriginalGsm="hdnOriginalGsm_".$j;
			$hdnOriginalDia="hdnOriginalDia_".$j;
			$hdnWeightEditable="hdnWeightEditable_".$j;
			$hdnCutWidth="hdnCutWidth_".$j;
			$weightTypeId="weightTypeId_".$j;
			$orderUomId="orderUomId_".$j;
			$hdnOrdRate="hdnOrdRate_".$j;
			$hdnOrdAmnt="hdnOrdAmnt_".$j;
			$hdnConsAmnt="hdnConsAmnt_".$j;
			$hdnConsRate="hdnConsRate_".$j;
			//$txtRemarks="txtRemarks_".$j;
			$hdnOrderIle="hdnOrderIle_".$j;
			$hdnOrderIleCost="hdnOrderIleCost_".$j;
			$hdnConsIle="hdnConsIle_".$j;
			$hdnConsIleCost="hdnConsIleCost_".$j;
			$txt_roll=1;

			if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
			{
				if($booking_without_order_status == 1)
				{
					echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this booking no";
				}
				else{
					echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this order no";
				}
				disconnect($con);
				die;
			}

			if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format($$rollWgt,2,".",""))
			{
				echo "20**Sorry! This barcode (". str_replace("'","", $$barcodeNo) .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".number_format($$rollWgt,2,".","") ."";
				disconnect($con);
				die;
			}
			
			if(str_replace("'","",$cbo_issue_purpose)==44) $reprocess_id=$$preRerocess+1;
			else $reprocess_id=$$preRerocess;
			
			$amount=$$rollRate*$$rollWgt;
			
			if($$rolltableId>0)
			{
				$transId_arr[]=$$transId;
				$data_array_update_trans[$$transId]=explode("*",($txt_issue_date."*'".$$rollWgt."'*'".$$hdnConsRate."'*'".$$hdnConsAmnt."'*'".$$floor."'*'".$$room."'*'".$$rack."'*'".$$self."'*'".$$binBox."'*'".$$batchId."'*'".$$shade_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));


				$dtlsId_arr[]=$$dtlsId;
				$data_array_update_dtls[$$dtlsId]=explode("*",($$rollWgt."*'".$$productId."'*'".$$orderId."'*".$cbo_store_name."*'".$$batchId."'*'".$$rollNo."'*'".$$deterId."'*'".$$hdnOriginalGsm."'*'".$$hdnOriginalDia."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$rollId_arr[]=$$rolltableId;
				$data_array_update_roll[$$rolltableId]=explode("*",("'".$$rollWgt."'*'".$$rollWgt."'*'".$reprocess_id."'*'".$$preRerocess."'*'".$$bwoNo."'*'".$$booking_without_order_status."'*'".$$shade_id."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$barcodeNos.=$$barcodeNo."__".$$dtlsId."__".$$transId."__".$$rolltableId.",";
				$dtlsId_prop=$$dtlsId;
				$transId_prop=$$transId;
				$all_roll_id.=$$rolltableId.",";
			}
			else
			{
				
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$dtls_id = return_next_id_by_sequence("INV_WV_FIN_FAB_ISS_DTLS_PK_SEQ", "inv_wvn_finish_fab_iss_dtls", $con);
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				
				
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$transactionID.",".$update_id.",".$cbo_issue_purpose.",'".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',3,2,195,".$txt_issue_date.",".$cbo_store_name.",'".$$floor."','".$$room."','".$$rack."','".$$self."','".$$binBox."','".$$shade_id."','".$$body_part."','".$$orderUomId."','".$$rollWgt."','".$$hdnOrdRate."','".$$hdnOrdAmnt."','".$$hdnOrderIle."','".$$hdnOrderIleCost."','".$$hdnConsIle."','".$$hdnConsIleCost."','".$$orderUomId."','".$$rollWgt."','".$$hdnConsRate."','".$$hdnConsAmnt."','".$txt_roll."','".$$hdnBatchName."','".$$batchId."','".$$hdnFabricRef."','".$$hdnRdNo."','".$$weightTypeId."','".$$hdnCutWidth."','".$$hdnWeightEditable."','".$$hdnOriginalDia."','".$$bookingNoOriginal."','".$$bookingIdOriginal."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				//if($data_array_dtls!="") $data_array_dtls.=",";
				//$data_array_dtls.="(".$dtls_id.",".$update_id.",".$transactionID.",'".$$productId."','".$$orderId."','".$$rollWgt."',".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$update_id.",".$transactionID.",'".$$productId."','".$$orderId."','".$$rollWgt."',".$cbo_store_name.",'".$$batchId."','".$$rollNo."','".$$deterId."','".$$hdnOriginalGsm."','".$$hdnOriginalDia."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",'".$$bookingIdOriginal."',195,'".$$rollWgt."','".$$rollWgt."','".$$rollNo."','".$$hdnBatchName."','".$$hdnManualRollNo."','".$reprocess_id."','".$$reProcess."','".$$hdnRfId."','".$$shade_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','".$$bwoNo."',".$$booking_without_order_status.",1,".$$jobId.")";


			
				
				$dtlsId_prop=$dtls_id;
				$transId_prop=$transactionID;
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$transactionID."__".$id_roll.",";

			}
			
			$all_barcode_no.=$$barcodeNo.",";
			
			$prodData_array[$$productId]+=$$rollWgt;
			$prodData_amoutArray[$$productId]+=$amount;
			$all_prod_id.=$$productId.",";
			
			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$transId_prop.",3,195,'".$dtlsId_prop."','".$$orderId."','".$$productId."','".$$colorId."','".$$rollWgt."','".$$rollNo."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "')";


			//$id_prop = $id_prop+1;
		}
		
		$txt_deleted_id=str_replace("'","",$txt_deleted_id); 
		$adj_prod_array=array(); 
		$update_dtls_id=''; 
		$update_trans_id=''; 
		$update_delete_dtls_id='';

		if($txt_deleted_id!="") 
		{
			$all_roll_id=$all_roll_id.$txt_deleted_id;
		} 
		else 
		{
			$all_roll_id=substr($all_roll_id,0,-1);
		}
		
		$deleted_id_arr=explode(",",$txt_deleted_id);
		
		$all_barcode_no=chop($all_barcode_no,",");


		$all_roll_id_arr = array_filter(explode(",",$all_roll_id));
		$roll_id_cond="";
		$roll_cond="";
		if($db_type==2 && count($all_roll_id_arr)>999)
		{
			$all_roll_id_chunk=array_chunk($all_roll_id_arr,999) ;
			foreach($all_roll_id_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$roll_cond.="  a.id in($chunk_arr_value) or ";
			}

			$roll_id_cond.=" and (".chop($roll_cond,'or ').")";
		}
		else
		{
			$roll_id_cond=" and a.id in($all_roll_id)";
		}

		if($all_roll_id!="")
		{
			$rollData=sql_select("select a.id, a.qnty,c.cons_rate, b.id as dtls_id, b.trans_id, b.prod_id from pro_roll_details a, inv_wvn_finish_fab_iss_dtls b, inv_transaction c where a.dtls_id=b.id and b.trans_id=c.id and a.mst_id=c.mst_id and c.entry_form=195 and c.item_category=3 and c.transaction_type=2 $roll_id_cond and b.mst_id=$update_id and a.entry_form=195");
			foreach($rollData as $row)
			{
				$adj_prod_array[$row[csf('prod_id')]]+=$row[csf('qnty')];
				$prodData_amoutArray[$row[csf('prod_id')]]-=$row[csf('cons_rate')]*$row[csf('qnty')];
				$all_prod_id.=$row[csf('prod_id')].",";
				$update_dtls_id.=$row[csf('dtls_id')].",";

				if(in_array($row[csf('id')], $deleted_id_arr))
				{
					$update_trans_id.=$row[csf('trans_id')].",";
					$update_delete_dtls_id.=$row[csf('dtls_id')].",";
				}
			}
		}
		$update_trans_id=substr($update_trans_id,0,-1);
		$update_delete_dtls_id=substr($update_delete_dtls_id,0,-1);

		
		/*if($all_barcode_no!="")
		{
			$rollData=sql_select("select a.id, a.qnty,a.rate, b.id as dtls_id, b.trans_id, b.prod_id from pro_roll_details a, inv_finish_fabric_issue_dtls b where a.dtls_id=b.id and a.barcode_no in($all_barcode_no) and b.mst_id=$update_id and a.entry_form=71");
			foreach($rollData as $row)
			{
				$adj_prod_array[$row[csf('prod_id')]]+=$row[csf('qnty')];
				$prodData_amoutArray[$row[csf('prod_id')]]-=$row[csf('rate')]*$row[csf('qnty')];
				$all_prod_id.=$row[csf('prod_id')].",";
				$update_dtls_id.=$row[csf('dtls_id')].",";
			}
		}*/
		
		//echo $all_prod_id;die;
		
		
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			if(str_replace("'","",$prodData_array[$row[csf('id')]])=="") $prodData_array[$row[csf('id')]]=0;
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]+$adj_prod_array[$row[csf('id')]]-$issue_qty;
			$stock_value=$row[csf('stock_value')]-$prodData_amoutArray[$row[csf('id')]];

			if($current_stock >0){
				$stock_rate = $stock_value/$current_stock;
			}else{
				$stock_rate =0;
				$stock_value=0;
			}

			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*'".$stock_value."'*'".$stock_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		//echo "10**";
		//print_r($data_array_prod_update);die;
		//echo "10**delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=195";die;

		$rID2=true; $rID3=true; $rID4=true; $rID5=true; $rID6=true; $rID7=true; $statusChangeTrans=true; $statusChangeDtls=true; $statusChangeRoll=true;
		
		if($txt_deleted_id!="")
		{
			/*$rollDelete=sql_select("select b.id as dtls_id, b.trans_id  from pro_roll_details a, inv_finish_fabric_issue_dtls b where a.dtls_id=b.id and a.id in($txt_deleted_id) and b.mst_id=$update_id and a.entry_form=195");
			foreach($rollDelete as $row)
			{
				$update_trans_id.=$row[csf('trans_id')].",";
				$update_delete_dtls_id.=$row[csf('dtls_id')].",";
			}
			
			$update_trans_id=substr($update_trans_id,0,-1);
			$update_delete_dtls_id=substr($update_delete_dtls_id,0,-1);*/
			
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$update_trans_id,0);
			$statusChangeDtls=sql_multirow_update("inv_wvn_finish_fab_iss_dtls",$field_array_status,$data_array_status,"id",$update_delete_dtls_id,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}

		$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$update_id,0);		
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			$rID3=sql_insert("inv_wvn_finish_fab_iss_dtls",$field_array_dtls,$data_array_dtls,0);
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}
		//echo "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr );die;
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		if(count($data_array_update_dtls)>0)
		{
			//echo "10**".bulk_update_sql_statement( "inv_transaction", "id", $field_array_updatetrans, $data_array_update_trans, $transId_arr );oci_rollback($con);die;

			$rID5=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_updatetrans, $data_array_update_trans, $transId_arr ));
			$rID6=execute_query(bulk_update_sql_statement( "inv_wvn_finish_fab_iss_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
			$rID7=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr ));
		}

		//echo "10**select a.id, a.qnty,a.rate, b.id as dtls_id, b.trans_id, b.prod_id from pro_roll_details a, inv_wvn_finish_fab_iss_dtls b where a.dtls_id=b.id and a.barcode_no in($all_barcode_no) and b.mst_id=$update_id and a.entry_form=195";oci_rollback($con); die;
		//echo "10**delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=195"; oci_rollback($con); die;
		$delete_prop=execute_query( "delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=195",0);
		$rID8=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);

		//echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );oci_rollback($con);die;

		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));

		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$rID7."&&".$rID8."&&".$delete_prop."&&".$prodUpdate."&&".$statusChangeTrans."&&".$statusChangeDtls."&&".$statusChangeRoll; oci_rollback($con);die;
		
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
	                       		$search_by_arr=array(1=>"Issue No", 2=>"Batch No");
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
	
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst and entry_form=559",'id','batch_no');
	$sql_batch_data=sql_select("SELECT a.id, b.batch_id,c.barcode_no,c.reprocess,c.prev_reprocess  FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(564) and c.entry_form in(564) and c.status_active=1 and c.is_deleted=0");
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
		if($search_by==2) $search_field_cond="and d.batch_no like '$search_string'";
	}
	
	if($db_type==0) 
	{
		$year_field="YEAR(a.insert_date) as year,";
		$year_field_group="YEAR(a.insert_date),";
	}
	else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year,";
		$year_field_group="to_char(a.insert_date,'YYYY'),";
	}
	else $year_field="";//defined Later
	//if($db_type==0) $batch_field="group_concat(b.sub_process_id)  as sub_process_id ";
	/*$sql="SELECT a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_date, a.batch_no, a.issue_purpose,b.barcode_no,b.prev_reprocess 
	from inv_issue_master a,pro_roll_details b 
	where a.id=b.mst_id and a.entry_form=71 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond 
	order by a.id"; */

	$sql="SELECT a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_date, b.batch_no, a.issue_purpose,b.barcode_no,b.prev_reprocess,c.fabric_shade  
	from inv_issue_master a,pro_roll_details b, inv_transaction c, pro_batch_create_mst d 
	where a.id=b.mst_id and a.id=c.mst_id and c.batch_id=d.id and a.entry_form=195 and a.item_category=3 and c.item_category=3 and b.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond
	group by a.id, $year_field_group a.issue_number_prefix_num, a.issue_number, a.knit_dye_source, a.knit_dye_company, a.issue_date, b.batch_no, a.issue_purpose, b.barcode_no, b.prev_reprocess,c.fabric_shade 
	order by a.id";
	//echo $sql;//die;
	$result = sql_select($sql);
	$issue_challan_arr=array();
	foreach ($result as $val)
	{
		$issue_challan_arr[$val[csf('id')]]['year']								=$val[csf('year')];
		$issue_challan_arr[$val[csf('id')]]['issue_number_prefix_num']			=$val[csf('issue_number_prefix_num')];
		$issue_challan_arr[$val[csf('id')]]['issue_number']						=$val[csf('issue_number')];
		$issue_challan_arr[$val[csf('id')]]['knit_dye_source']					=$val[csf('knit_dye_source')];
		$issue_challan_arr[$val[csf('id')]]['knit_dye_company']					=$val[csf('knit_dye_company')];
		$issue_challan_arr[$val[csf('id')]]['issue_date']						=$val[csf('issue_date')];
		$issue_challan_arr[$val[csf('id')]]['issue_purpose']					=$val[csf('issue_purpose')];
		$issue_challan_shade_arr[$val[csf('id')]]['fabric_shades'].=$fabric_shade[$val[csf('fabric_shade')]].",";
		$issue_challan_batch_arr[$val[csf('id')]]['batch_name'].=$val[csf('batch_no')].",";
		
		//$issue_challan_batch_arr[$val[csf('id')]][$batch_barcode_arr[$val[csf('barcode_no')]][$val[csf('prev_reprocess')]]]=$batch_arr[$batch_barcode_arr[$val[csf('barcode_no')]][$val[csf('prev_reprocess')]]];
		//echo $batch_barcode_arr[$val[csf('barcode_no')]][$val[csf('prev_reprocess')]];
		/*
		$issue_challan_arr[$val[csf('id')]]['knit_dye_source']=$val[csf('knit_dye_source')];
		$issue_challan_arr[$val[csf('id')]]['knit_dye_company']=$val[csf('knit_dye_company')];
		$issue_challan_arr[$val[csf('id')]]['issue_date']=$val[csf('issue_date')];
		$issue_challan_arr[$val[csf('id')]]['issue_purpose']=$val[csf('issue_purpose')];*/		
	}
	/*echo "<pre>";
	print_r($issue_challan_batch);*/
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Issue No</th>
            <th width="60">Year</th>
            <th width="110">Issue Purpose</th>
            <th width="200">Batch No</th>
            <th width="60">Shade</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:660px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" id="tbl_list_search">  
        	<?
            $i=1;
            foreach ($issue_challan_arr as $issue_id=>$issue_data)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	

                $fabricShade=implode(",",array_unique(explode(",",$issue_challan_shade_arr[$issue_id]['fabric_shades'])));
                $batchNos=implode(",",array_unique(explode(",",$issue_challan_batch_arr[$issue_id]['batch_name'])));
				 
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
                    <td width="110"><p><? echo $yarn_issue_purpose[$issue_data['issue_purpose']]; ?>&nbsp;</p></td>
                    <td width="200" style="word-break:break-all;"><p><? echo  chop($batchNos,","); ?>&nbsp;</p></td>
                    <td width="60" align="center"><p><? echo chop($fabricShade,","); ?>&nbsp;</p></td>
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
	//echo $sql;
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
	$sql = "select id, company_id, issue_number, knit_dye_source, knit_dye_company, issue_date, location_id,req_no, issue_purpose, store_id from inv_issue_master where id=$data and entry_form=195 and status_active=1 and is_deleted=0";
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
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";
		
		//$batchno = return_field_value("batch_no","pro_batch_create_mst","id='".$row[csf("batch_no")]."'");
		//echo "$('#txt_batch_no').val('".$batchno."');\n";	
		//echo "$('#txt_batch_id').val(".$row[csf("batch_no")].");\n";

		echo "load_drop_down('requires/woven_finish_fabric_issue_roll_wise_controller', " .$row[csf("company_id")] . ", 'load_drop_down_location','location_td');\n";
		echo "$('#cbo_location_name').val(".$row[csf("location_id")].");\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		
  	}
	exit();	
}

if($action=="barcode_nos")
{
	if($db_type==0) 
	{
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","pro_roll_details","entry_form=71 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2) 
	{
		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","pro_roll_details","entry_form=71 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
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
		var selected_roll_qnty = new Array();
		
		function toggle( x, origColor, overQnty ) {
			var newColor = 'yellow';
			if(overQnty==1){return;}
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_roll_qnty.push( $('#hdn_roll_qnty' + str).val()*1 );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_roll_qnty.splice( i, 1 );
			}
			var id = '';var id_qnty = 0;var overQnty=0;
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				id_qnty += selected_roll_qnty[i];

				if("<? echo $hdn_issue_requisition_madatory_vari ?>"==1 && "<? echo $txt_rqn_qnty ?>"<id_qnty)
				{
					alert("Requsition Quantity Exceed.\n Total Requsition Quantity ="+<? echo $txt_rqn_qnty ?>);
					overQnty=1;
					return;
				}
			}
			if(overQnty==1)
			{
				return;
			}
			id = id.substr( 0, id.length - 1 );
			//id_qnty = id_qnty.substr( 0, id_qnty.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
			$('#hidden_barcode_qnty').val( id_qnty );
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' , overQnty );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
			hidden_barcode_qnty = new Array();
		}
        function check_batch(value)
        {
			return; //Here code returned for temporarily 
            var searchBy= $("#cbo_search_by").val();
            if( searchBy == 2)
            {
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
                     return;
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
	    function check_all_data()
		{
			var row_num=$('#tbl_list_search tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#search"+i).click();
			}
			
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
                    <th id="search_by_td_up" width="180">Please Enter Style Ref No</th>
                    <th>Barcode No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                        <input type="hidden" name="hidden_barcode_qnty" id="hidden_barcode_qnty">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">	
                    	<?
                       		//$search_by_arr=array(1=>'Order No', 2=>'Batch No', 3=>'Job No');
                       		$search_by_arr=array(1=>'Style Ref No', 3=>'Job No');
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company_id; ?>+'_'+'<? echo $batch_id; ?>'+'_'+document.getElementById('barcode_no').value+'_'+document.getElementById('hidden_batch_id').value+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $txt_rqn_id; ?>+'_'+<? echo $txt_rqn_qnty; ?>+'_'+<? echo $hdn_issue_requisition_madatory_vari; ?>, 'create_barcode_search_list_view', 'search_div', 'woven_finish_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
    $cbo_store_name =trim($data[6]);
    $txt_rqn_id =trim($data[7]);
    $txt_rqn_qnty =trim($data[8]);
    $hdn_issue_requisition_madatory_vari =trim($data[9]);
	$search_field_cond="";
    $batch_cond="";

	if(trim($data[0])!="")
	{
        if($search_by==1) 
		{
			//$search_field_cond="and d.po_number like '$search_string'";
			$search_field_cond="and g.style_ref_no like '$search_string'";
		}
        else if($search_by==2) 
        {
             if($batch_id!=""){
                if( $search_batchNo != $batch_id){
                 
                    return;
                }
            }
            //$search_field_cond=" and d.batch_id=$search_batchNo ";
            $search_field_cond=" and e.batch_no like '$search_string'";
        }
    }else {
            
        if($batch_id!="") $batch_cond=" and b.batch_id=$batch_id ";
    }
        
	if($barcode_no!="")
	{
		$barcode_cond="and e.barcode_no='$barcode_no'";
	}

	if($cbo_store_name)
	{
		$store_cond="and a.store_id='$cbo_store_name'";
		$store_cond_trans="and d.to_store='$cbo_store_name'";
	}

	if($search_by ==3){
		$job_no_cond = "and f.job_no_mst like '$search_string' ";
	}

	if($txt_rqn_id>0)
	{
		$requisitionData= sql_select("select job_id,booking_no from pro_fab_reqn_for_cutting_dtls where mst_id=$txt_rqn_id and status_active=1 and is_deleted=0 group by job_id,booking_no");
		$reqJobIds="";$bookNos="";
		foreach ($requisitionData as $rowData) {
			$reqJobIds.=$rowData[csf('job_id')].",";
			$bookNos.="'".$rowData[csf('booking_no')]."',";
		}
		$reqJobIds=chop($reqJobIds,",");
		$bookNos=chop(implode(",",array_unique(explode(",",$bookNos))),",");
		$requistionJobDataCond="and e.job_id in($reqJobIds)";
		$requistionBookDataCond="and e.booking_no in($bookNos)";

	}
	
	/*$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.reprocess,c.is_sales,c.po_breakdown_id, e.batch_no, e.color_id, f.product_name_details FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c left join wo_po_break_down d on c.po_breakdown_id=d.id, pro_batch_create_mst e, product_details_master f  WHERE a.id=b.mst_id and b.id=c.dtls_id  and b.batch_id=e.id and b.prod_id=f.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(37,7,68,126) and c.entry_form in(37,7,68,126) and c.status_active=1 and c.is_deleted=0 and c.is_sales=0 and c.re_transfer = 0 $search_field_cond $barcode_cond $batch_cond $job_no_cond $store_cond
		union all
		select a.transfer_system_id as recv_number,b.from_prod_id as prod_id,c.barcode_no, c.roll_no, c.qnty,c.reprocess,c.is_sales,c.po_breakdown_id, e.batch_no, e.color_id, f.product_name_details
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c left join wo_po_break_down d on c.po_breakdown_id=d.id, pro_batch_create_mst e, product_details_master f 
		WHERE a.id=b.mst_id and b.id=c.dtls_id  and b.batch_id=e.id and b.from_prod_id=f.id and a.entry_form in(134,216,219,214) and c.entry_form in(134,216,219,214) and c.status_active=1 and c.is_deleted=0 and c.is_sales=0 and c.re_transfer = 0 $search_field_cond $barcode_cond $batch_cond $job_no_cond $store_cond_trans
		order by barcode_no";*/
		//and c.roll_no>0


		$sql= "SELECT a.recv_number,a.company_id,a.location_id,b.store_id, a.currency_id, a.exchange_rate, a.booking_without_order, a.booking_no, b.id as trans_id,b.cutting_unit_no,b.roll,b.remarks, b.receive_basis, b.pi_wo_batch_no, b.prod_id, b.brand_id,b.batch_lot as batch_no, c.detarmination_id,c.product_name_details,c.color as color_id,c.dia_width,c.weight, b.order_uom, b.order_qnty,b.body_part_id, b.order_rate,b.cons_rate, b.order_ile_cost,b.batch_id, b.order_amount,b.cons_amount,b.no_of_bags,b.product_code,b.floor_id,b.room,b.rack, b.self,b.bin_box, d.id as details_id,d.buyer_id, d.booking_no as pi_booking_no, d.booking_id as pi_booking_id,d.original_width,d.original_gsm, d.fabric_description_id, d.width, b.fabric_ref,b.rd_no,b.weight_type,b.cutable_width,b.weight_editable,b.width_editable, e.id as roll_table_id, e.po_breakdown_id, e.barcode_no, e.roll_no,e.shrinkage_shade, e.manual_roll_no, e.rf_id, e.reject_qnty,e.qnty,g.style_ref_no,e.job_id FROM inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d,pro_roll_details e left join wo_po_details_master g on e.job_id=g.id  where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and a.id=e.mst_id and d.id=e.dtls_id and e.entry_form=564 and a.entry_form=564 $requistionJobDataCond $requistionBookDataCond $search_field_cond $barcode_cond $batch_cond $job_no_cond $store_cond  and e.barcode_no not in(select f.barcode_no from pro_roll_details f where e.barcode_no=f.barcode_no  and f.entry_form=195 and f.is_deleted=0 and f.status_active=1)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and g.status_active=1 and g.is_deleted=0"; 
	

	//echo $sql;//die;
	$result = sql_select($sql);

	foreach ($result as $row)
	{
		$job_id_arr[$row[csf('job_id')]] = $row[csf('job_id')];
		$barcode_arr[$row[csf('barcode_no')]] =$row[csf('barcode_no')];
	}

	if(!empty($barcode_arr))
	{
		$all_barcodeNo_arr = array_filter($barcode_arr);
		if(count($all_barcodeNo_arr)>0)
		{
			$barcod_NOs = implode(",", $all_barcodeNo_arr);
			$all_barcode_no_cond=""; $barCond="";
			if($db_type==2 && count($all_barcodeNo_arr)>999)
			{
				$all_barcodeNo_arr_chunk=array_chunk($all_barcodeNo_arr,999) ;
				foreach($all_barcodeNo_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barCond.=" barcode_no in($chunk_arr_value) or ";
				}

				$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
			}
			else
			{
				$all_barcode_no_cond=" and barcode_no in($barcod_NOs)";
			}
		}
		$scanned_barcode_arr=array();
		$barcodeData=sql_select( "select barcode_no,prev_reprocess from pro_roll_details where entry_form=195 and status_active=1 and is_deleted=0 and is_returned=0 $all_barcode_no_cond");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]][$row[csf('prev_reprocess')]]=$row[csf('barcode_no')];
		}
	}

	if(!empty($job_id_arr))
	{
		$job_id_arr = array_filter($job_id_arr);
		if(count($job_id_arr)>0)
		{
			$job_ids = implode(",", $job_id_arr);
			$all_job_id_cond=""; $poCond="";
			if($db_type==2 && count($job_id_arr)>999)
			{
				$po_breakdown_arr_chunk=array_chunk($job_id_arr,999) ;
				foreach($po_breakdown_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$poCond.=" d.id in($chunk_arr_value) or ";
				}

				$all_job_id_cond.=" and (".chop($poCond,'or ').")";
			}
			else
			{
				$all_job_id_cond=" and d.id in($job_ids)";
			}
		}
		$job_arr=array();
	
		$sql_job=sql_select("select a.buyer_id, b.job_no as job_no_mst,b.booking_no,b.po_break_down_id,c.po_number, c.shipment_date,d.id as job_id,d.style_ref_no from wo_booking_mst a, wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.booking_type in(1,4) $all_job_id_cond group by b.job_no,b.booking_no,a.buyer_id,b.po_break_down_id,c.po_number,c.shipment_date,d.id,d.style_ref_no");

		foreach ($sql_job as $job_row) {
			$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_mst')];
			$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 			= $job_row[csf('buyer_id')];		
			$job_arr[$job_row[csf('booking_no')]]["po_number"] 			= $job_row[csf('po_number')];		
			
			$job_arr[$job_row[csf('job_id')]]["job_no_mst"] 	= $job_row[csf('job_no_mst')];
			$job_arr[$job_row[csf('job_id')]]["buyer_id"] 	= $job_row[csf('buyer_id')];
			$job_arr[$job_row[csf('job_id')]]["po_number"] 	= $job_row[csf('po_number')];
			$job_arr[$job_row[csf('job_id')]]["shipment_date"]= $job_row[csf('shipment_date')];
			$job_arr[$job_row[csf('job_id')]]["style_ref_no"]= $job_row[csf('style_ref_no')];
		}
	}

	

	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Style</th>
            <!-- <th width="70">Batch No</th> -->
            <th width="70">Shade</th>
            <th width="100">Color No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:830px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="810" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {
				if($scanned_barcode_arr[$row[csf('barcode_no')]][$row[csf('reprocess')]]=="")
				{	
            		$job_no 	= $job_arr[$row[csf('job_id')]]["job_no_mst"];
            		$order_no 	= $job_arr[$row[csf('job_id')]]["po_number"];
            		$styleNo 	= $job_arr[$row[csf('job_id')]]["style_ref_no"];
            		$shipment 	= change_date_format($job_arr[$row[csf('job_id')]]["shipment_date"]);          		
		           
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="150"><p><? echo $row[csf('product_name_details')]; ?></p></td>
						<td width="100"><p><? echo $job_no; ?></p></td>
						<td width="110"><p><? echo $styleNo; ?></p></td>
						<td width="70"><p><? echo $fabric_shade[$row[csf('shrinkage_shade')]];//$row[csf('batch_no')]; ?></p></td>
						<td width="100"><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
						<td width="80" align="center"><? echo $shipment; ?></td>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?>
							<input type="hidden" name="hdn_roll_qnty" id="hdn_roll_qnty<?php echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>">
						</td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
    	<br/>
    	<div style="width:50%; float:left" align="left">
			<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"> Check / Uncheck All
		</div>
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('barcode_no').value, 'create_barcode_search_list_view_requisition', 'search_div', 'woven_finish_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
if($action=="create_barcode_search_list_view_requisition")
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
	
	$sql="SELECT a.recv_number, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 $search_field_cond $barcode_cond"; 
	//echo $sql;//die;
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

if($action=="populate_barcode_data")
{
	$data_arr = explode("_", $data);
	$BARCODE_NO = $data_arr[0];
	$store_id = $data_arr[1];
	$txt_rqn_id = $data_arr[2];

	$barcodeData=''; $po_ids_arr=array(); $po_details_array=array(); $barcodeDataArr=array(); $barcodeBuyerArr=array(); $transRollIds=''; $transPoIdsArr=array();
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	$scanned_barcode_sql=sql_select("select barcode_no,prev_reprocess from pro_roll_details where entry_form=195 and is_returned!=1 and barcode_no in( $BARCODE_NO ) and status_active=1 and is_deleted=0");
	foreach($scanned_barcode_sql as $row)
	{
		 $scanned_barcode_data[$row[csf("barcode_no")]][$row[csf("prev_reprocess")]]=$row[csf("barcode_no")] ;
	}
	unset( $scanned_barcode_sql);
	
	$jsscanned_barcode_array= json_encode($scanned_barcode_array);
	
	

	//============issue return =====================================================================

	// echo "SELECT a.barcode_no, b.company_id, b.store_id, c.prod_id, c.floor, c.room, c.rack_no, c.shelf_no, c.bin,c.body_part_id, a.po_breakdown_id, a.booking_without_order from pro_roll_details a, inv_receive_master b, pro_finish_fabric_rcv_dtls c where a.barcode_no in($BARCODE_NO) and a.mst_id=b.id and a.dtls_id=c.id and a.entry_form=126 and b.entry_form=126 and a.re_transfer=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";die;
	

	/*$issue_return_sql = sql_select("SELECT a.barcode_no, b.company_id, b.store_id, c.prod_id, c.floor, c.room, c.rack_no, c.shelf_no, c.bin,c.body_part_id, a.po_breakdown_id, a.booking_without_order from pro_roll_details a, inv_receive_master b, pro_finish_fabric_rcv_dtls c where a.barcode_no in($BARCODE_NO) and a.mst_id=b.id and a.dtls_id=c.id and a.entry_form=126 and b.entry_form=126 and a.re_transfer=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

	$issue_return_data=array();
	foreach($issue_return_sql as $row)
	{
		$current_store_floor_room_rack[$row[csf("barcode_no")]]["company_id"]=$row[csf("company_id")];
		$current_store_floor_room_rack[$row[csf("barcode_no")]]["prod_id"]=$row[csf("prod_id")];
		$current_store_floor_room_rack[$row[csf("barcode_no")]]["floor"]=$row[csf("floor")];
		$current_store_floor_room_rack[$row[csf("barcode_no")]]["room"]=$row[csf("room")];
		$current_store_floor_room_rack[$row[csf("barcode_no")]]["rack_no"]=$row[csf("rack_no")];
		$current_store_floor_room_rack[$row[csf("barcode_no")]]["shelf_no"]=$row[csf("shelf_no")];
		$current_store_floor_room_rack[$row[csf("barcode_no")]]["bin"]=$row[csf("bin")];
		$current_store_floor_room_rack[$row[csf("barcode_no")]]["store_id"] = $row[csf("store_id")];
		
	}
	unset($issue_return_sql);*/

	if($txt_rqn_id>0)
	{
		$requisitionData= sql_select("select job_id from pro_fab_reqn_for_cutting_dtls where mst_id=$txt_rqn_id and status_active=1 and is_deleted=0 group by job_id");
		$reqJobIds="";
		foreach ($requisitionData as $rowData) {
			$reqJobIds.=$rowData[csf('job_id')].",";
		}
		$reqJobIds=chop($reqJobIds,",");
		$requistionDataCond="and c.job_id in($reqJobIds)";
	}

	$sql_trans_data = sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, 
	b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, 
	c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,
	c.is_sales,c.reprocess,c.prev_reprocess FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE  a.id=b.mst_id and b.id=c.dtls_id  and c.barcode_no in($BARCODE_NO)
	and b.trans_id<>0 and a.entry_form in(564) and c.entry_form in(564) and c.status_active=1 
	and c.is_deleted=0 $requistionDataCond");

	$transfer_data=array();
	$transfer_data_one=array();
	foreach($sql_trans_data as $row)
	{
		$transfer_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		$transfer_data[$row[csf("barcode_no")]]["color_id"] = $row[csf("color_id")];
		$transfer_data[$row[csf("barcode_no")]]["knitting_source"] = $row[csf("knitting_source")];
		$transfer_data[$row[csf("barcode_no")]]["dia_width_type"] = $row[csf("dia_width_type")];
		$transfer_data_one[$row[csf("barcode_no")]]["knitting_company"]=$row[csf("knitting_company")];
		$transfer_data[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
		// $transfer_data[$row[csf("barcode_no")]]["color_id"] = $row[csf("color_id")];
		if($row[csf("knitting_source")]==1)
		{
			$transfer_data[$row[csf("barcode_no")]]["knitting_company"]=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$transfer_data[$row[csf("barcode_no")]]["knitting_company"]=$supplier_arr[$row[csf("knitting_company")]];;
		}

		
		
	}
	unset($sql_trans_data);

	

	//=============================
	
	// echo "SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,b.id as dtls_id,b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,0 as from_order_id,c.barcode_no,b.dia_width_type, c.id as roll_id,c.roll_no, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales,max(c.reprocess) as reprocess, a.store_id, b.floor, b.room, b.rack_no, b.shelf_no, b.bin 
	// FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	// WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68,126) and c.entry_form in(37,7,68,126) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($BARCODE_NO) and a.store_id=$store_id and c.re_transfer = 0 group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,b.id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id, c.qc_pass_qnty, c.roll_id,c.rate, c.booking_no, 
	// c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales, a.store_id, b.floor, b.room, b.rack_no, b.shelf_no, b.bin 
	// union all
	// select a.id,a.entry_form,a.transfer_system_id as recv_number,a.company_id,c.receive_basis, null as booking_no,
	// 0 as booking_id, 0 as knitting_source,0 as knitting_company,b.id as dtls_id, b.from_prod_id as prod_id,b.body_part_id,b.trans_id,b.feb_description_id as fabric_description_id,b.gsm,b.dia_width as width,b.batch_id,b.color_id,b.from_order_id,c.barcode_no,b.dia_width_type,c.id as roll_id, c.roll_no,c.po_breakdown_id, c.qnty as qc_pass_qnty,c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales,max(c.reprocess) as reprocess,b.to_store as store_id,b.to_floor_id as floor, b.to_room as room, b.to_rack as rack_no,b.to_shelf as shelf_no, b.to_bin_box as bin from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c left join wo_po_break_down d on c.po_breakdown_id=d.id, 
	// pro_batch_create_mst e WHERE a.id=b.mst_id and b.id=c.dtls_id and b.batch_id=e.id and a.entry_form in(134,214) and c.entry_form in(134,214) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($BARCODE_NO) and b.to_store=$store_id  and c.is_sales=0  and c.re_transfer = 0 group by a.id, a.entry_form,a.transfer_system_id, a.company_id, c.receive_basis, b.id, b.from_prod_id, b.body_part_id,b.trans_id, b.feb_description_id,b.gsm, b.dia_width,b.batch_id, b.color_id,b.from_order_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id,c.rate, c.booking_no, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales, b.to_store, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.to_bin_box";die;
	
	$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,b.id as dtls_id,b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,0 as from_order_id,c.barcode_no,b.dia_width_type, c.id as roll_id,c.roll_no, c.po_breakdown_id,c.job_id, sum(case when c.entry_form in(564) then c.qnty else c.qc_pass_qnty end) as qc_pass_qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,max(c.reprocess) as reprocess,c.prev_reprocess, a.store_id, b.floor, cast(b.room as varchar2(4000)) as room, cast(b.rack_no as varchar2(4000)) as rack_no, b.shelf_no, b.bin,c.shrinkage_shade,b.booking_no as pi_booking_no, b.booking_id as pi_booking_id,c.rf_id,c.manual_roll_no, d.fabric_ref ,d.rd_no,b.original_width,b.original_gsm,d.weight_editable,d.width_editable,d.weight_type,d.cutable_width,d.order_uom , d.order_rate,d.cons_rate, d.order_ile,d.order_ile_cost,d.cons_ile, d.cons_ile_cost, d.order_amount,d.cons_amount  FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=d.id and a.id=d.mst_id and b.trans_id<>0 and a.entry_form in(564) and c.entry_form in(564) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($BARCODE_NO) and a.store_id=$store_id and c.re_transfer = 0 $requistionDataCond and c.barcode_no not in(select f.barcode_no from pro_roll_details f where c.barcode_no=f.barcode_no  and f.entry_form=195 and f.is_deleted=0 and f.status_active=1)  group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,b.id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id,c.job_id, c.roll_id,c.rate, c.booking_no, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.prev_reprocess, a.store_id, b.floor, b.room, b.rack_no, b.shelf_no, b.bin,c.shrinkage_shade,b.booking_no,b.booking_id,c.rf_id,c.manual_roll_no, d.fabric_ref ,d.rd_no,b.original_width,b.original_gsm,d.weight_editable,d.width_editable,d.weight_type,d.cutable_width,d.order_uom , d.order_rate,d.cons_rate, d.order_ile,d.order_ile_cost,d.cons_ile, d.cons_ile_cost, d.order_amount,d.cons_amount");

	/*$sql= "SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,a.currency_id, a.exchange_rate, a.booking_without_order,a.store_id,b.id as trans_id,b.cutting_unit_no,b.roll,b.remarks, b.receive_basis, b.pi_wo_batch_no, b.prod_id, b.brand_id,b.batch_lot, c.detarmination_id,d.color_id,0 as from_order_id,c.dia_width,c.weight, b.order_uom, sum(case when e.entry_form in(564) then e.qnty else e.qc_pass_qnty end) as qc_pass_qnty,b.body_part_id, b.order_rate, b.order_ile_cost,b.batch_id, b.order_amount,b.cons_amount,b.no_of_bags,b.product_code,b.floor_id as floor,b.room,b.rack as rack_no, b.self as shelf_no,b.bin_box as bin, d.id as dtls_id,d.buyer_id, d.booking_no as pi_booking_no, d.booking_id as pi_booking_id,d.original_width as width,d.original_gsm as gsm, d.fabric_description_id, d.width, b.fabric_ref,b.rd_no,b.weight_type as dia_width_type,b.cutable_width,b.weight_editable,b.width_editable, e.id as roll_table_id, e.po_breakdown_id, e.barcode_no, e.id as roll_id,e.roll_id as roll_id_prev, e.roll_no,e.shrinkage_shade, e.manual_roll_no, e.rf_id, e.reject_qnty FROM inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d,pro_roll_details e where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and a.id=e.mst_id and d.id=e.dtls_id and e.entry_form=564 and a.id=$RCV_ID and a.entry_form=564 and a.entry_form=564 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  ";*/

	/*$data_array=sql_select("SELECT a.company_id,a.location_id,b.store_id, a.currency_id, a.exchange_rate, a.booking_without_order, a.booking_no, b.id as trans_id,b.cutting_unit_no,b.roll,b.remarks, b.receive_basis, b.pi_wo_batch_no, b.prod_id, b.brand_id,b.batch_lot, c.detarmination_id,c.color,c.dia_width,c.weight, b.order_uom, e.qnty as recv_qnty,b.body_part_id, b.order_rate, b.order_ile_cost,b.batch_id, b.order_amount,b.cons_amount,b.no_of_bags,b.product_code,b.floor_id,b.room,b.rack, b.self,b.bin_box, d.id as details_id,d.buyer_id, d.booking_no as pi_booking_no, d.booking_id as pi_booking_id,d.original_width,d.original_gsm, d.fabric_description_id, d.width, b.fabric_ref,b.rd_no,b.weight_type,b.cutable_width,b.weight_editable,b.width_editable, e.id as roll_table_id, e.po_breakdown_id, e.barcode_no, e.roll_no,e.shrinkage_shade, e.manual_roll_no, e.rf_id, e.reject_qnty,max(e.reprocess) as reprocess FROM inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d,pro_roll_details e where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and a.id=e.mst_id and d.id=e.dtls_id and e.entry_form=564 and d.trans_id<>0  and e.barcode_no in($BARCODE_NO) and a.store_id=$store_id and e.re_transfer = 0 and a.entry_form=564 and a.entry_form=564 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  group by  a.company_id,a.location_id,b.store_id, a.currency_id, a.exchange_rate, a.booking_without_order, a.booking_no, b.id ,b.cutting_unit_no,b.roll,b.remarks, b.receive_basis, b.pi_wo_batch_no, b.prod_id, b.brand_id,b.batch_lot, c.detarmination_id,c.color,c.dia_width,c.weight, b.order_uom,
 e.qnty ,b.body_part_id, b.order_rate, b.order_ile_cost,b.batch_id, b.order_amount,b.cons_amount,b.no_of_bags,b.product_code,b.floor_id,b.room,b.rack, b.self,b.bin_box, d.id,d.buyer_id, d.booking_no , d.booking_id,d.original_width,d.original_gsm,
  d.fabric_description_id, d.width, b.fabric_ref,b.rd_no,b.weight_type,b.cutable_width,b.weight_editable,b.width_editable, e.id, e.po_breakdown_id, e.barcode_no, e.roll_no,e.shrinkage_shade, e.manual_roll_no, e.rf_id, e.reject_qnty");*/


	if(empty($data_array))
	{
		echo "990";
		die;
	}

	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			$from_order_ids .= $row[csf("from_order_id")].",";
			if($row[csf('booking_without_order')]==0)
			{
				$all_job_id[$row[csf('job_id')]] = $row[csf('job_id')];
			}

				$source = $row[csf('source')];
				$company_id = $row[csf('company_id')];
				$exchange_rate = $row[csf('exchange_rate')];
				$currency_id = $row[csf('currency_id')];
				$store_id = $row[csf('store_id')];
			}
	}

	$from_order_ids = chop($from_order_ids,",");

	if(!empty($all_job_id))
	{
		$job_sql = sql_select("select a.id, a.po_number, a.job_no_mst, a.job_id, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.id in (".implode(',',$all_job_id).")");
		foreach ($job_sql as  $row) 
		{
			$po_ref_arr[$row[csf('job_id')]]['po_number']=$row[csf('po_number')];
			$po_ref_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no_mst')];
			$po_ref_arr[$row[csf('job_id')]]['job_id']=$row[csf('job_id')];
			$po_ref_arr[$row[csf('job_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}


	
	$book_booking_arr=return_library_array("select po_break_down_id, booking_no from wo_booking_dtls where booking_type=1 and po_break_down_id in ($from_order_ids) ",'po_break_down_id','booking_no');

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name , e.floor_room_rack_name bin_name 
		from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		left join lib_floor_room_rack_mst e on b.bin_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		where b.status_active=1 and b.is_deleted=0 and b.store_id=$store_id";
		$lib_floor_arr=sql_select($lib_room_rack_shelf_sql);
		foreach ($lib_floor_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
				$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
				$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}
		}


	foreach($data_array as $row)
	{
		if($scanned_barcode_data[$row[csf("barcode_no")]][$row[csf("reprocess")]]=="")
		{
			//$booking_no_id = $row[csf('po_breakdown_id')];
			if($row[csf("receive_basis")] > 0)
			{	
				$receive_basis=$receive_basis_arr[$row[csf("receive_basis")]];
				$receive_basis_id=$row[csf("receive_basis")];
			}
			else
			{
				$receive_basis_id = $transfer_data[$row[csf("barcode_no")]]["receive_basis"];
				$receive_basis = $receive_basis_arr[$receive_basis_id];
			}
			
			
			if($row[csf("knitting_source")]==1)
			{
				$knit_company=$company_name_array[$row[csf("knitting_company")]];
			}
			else if($row[csf("knitting_source")]==3)
			{
				$knit_company=$supplier_arr[$row[csf("knitting_company")]];
			}
			
			$rate=$row[csf("rate")];
			
			$roll_id=$row[csf("roll_id")];
				
			
			
			/*if($row[csf("booking_without_order")]==1)
			{
				$non_order_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
			}
			else
			{
				$po_ids_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
			}*/

			
			$po_breakdown_id = $row[csf("po_breakdown_id")];
			$job_ids = $row[csf("job_id")];
			$booking_without_order = $row[csf("booking_without_order")];

			if($booking_without_order==1)
			{
				$non_order_arr[$po_breakdown_id]=$po_breakdown_id;
			}
			else
			{
				$job_ids_arr[ $row[csf("job_id")]]= $row[csf("job_id")];
			}
	
			$company_id =$row[csf("company_id")];
			$prod_id =$row[csf("prod_id")];
			$store_id =$row[csf("store_id")];
			$floor_id =$row[csf("floor")];
			$room_id =$row[csf("room")];
			$rack_id =$row[csf("rack_no")];
			$shelf_id =$row[csf("shelf_no")];
			$bin_id =$row[csf("bin")];	
			$body_part_id =$row[csf("body_part_id")];
			

			$color='';
			/*$color_id=explode(",",$row[csf('color_id')]);
			foreach($color_id as $val)
			{
				if($val>0) $color.=$color_arr[$val].",";
			}
			$color=chop($color,',');*/

			if($transfer_data[$row[csf("barcode_no")]]["color_id"]){
				$color_id = $transfer_data[$row[csf("barcode_no")]]["color_id"];
				$color .= $color_arr[$color_id];
			}else{
				$color_id =$row[csf("color_id")];
				$color .=$color_arr[$color_id];
			}

			if($transfer_data[$row[csf("barcode_no")]]["knitting_company"])
			{
				$knit_company = $transfer_data[$row[csf("barcode_no")]]["knitting_company"];
			}

			if($transfer_data_one[$row[csf("barcode_no")]]["knitting_company"])
			{
				$knit_company_one = $transfer_data_one[$row[csf("barcode_no")]]["knitting_company"];
			}else{
				$knit_company_one = $row[csf("knitting_company")];
			}			

			if($transfer_data[$row[csf("barcode_no")]]["knitting_source"])
			{
				$knitting_source_one = $transfer_data[$row[csf("barcode_no")]]["knitting_source"];
			}else{
				$knitting_source_one =$row[csf("knitting_source")];
			}

			if($transfer_data[$row[csf("barcode_no")]]["dia_width_type"])
			{
				$dia_width_type = $transfer_data[$row[csf("barcode_no")]]["dia_width_type"];
			}else{
				$dia_width_type =$row[csf("dia_width_type")];
			}

			if($row[csf("booking_no")] > 0 )
			{
				$book_booking = $row[csf("booking_no")];
			}
			else{
				$book_booking = $book_booking_arr[$row[csf("from_order_id")]];
			}

			$floor_no 	= $lib_floor_arr[$company_id][$floor_id];
			$room_no 	= $lib_room_arr[$company_id][$floor_id][$room_id];
			$rack_no	= $lib_rack_arr[$company_id][$floor_id][$room_id][$rack_id];
			$shelf_no 	= $lib_shelf_arr[$company_id][$floor_id][$room_id][$rack_id][$shelf_id];
			$bin_no 	= $lib_bin_arr[$company_id][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id];

			//."**".$floor_no."**".$room_no."**".$rack_no."**".$shelf_no."**".$bin_no


			$barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("barcode_no")]."**".$company_id."**".$row[csf("roll_no")]."**".$roll_id."**".$body_part_id."**".$body_part[$body_part_id]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("recv_number")]."**".$row[csf("booking_id")]."**".$color."**".$color_id."**".$knitting_source_one."**".$knitting_source[$knitting_source_one]."**".$knit_company_one."**".$knit_company."**".$row[csf("batch_id")]."**".$dia_width_type."**".$fabric_typee[$dia_width_type]."**".$batch_name_array[$row[csf("batch_id")]]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".number_format($row[csf("qc_pass_qnty")],2,'.','')."**".$rate."**".$booking_without_order."**".$row[csf("reprocess")]."**".$book_booking."**".$floor_id."**".$room_id."**".$rack_id."**".$shelf_id."**".$bin_id."**".$floor_no."**".$room_no."**".$rack_no."**".$shelf_no."**".$bin_no."**".$store_id."**".$row[csf('shrinkage_shade')]."**".$fabric_shade[$row[csf('shrinkage_shade')]]."**".$row[csf('pi_booking_no')]."**".$row[csf('pi_booking_id')]."**".$row[csf('rf_id')]."**".$row[csf('manual_roll_no')]."**".$row[csf('fabric_ref')]."**".$row[csf('rd_no')]."**".$row[csf('original_gsm')]."**".$row[csf('weight_editable')]."**".$row[csf('weight_type')]."**".$fabric_weight_type[$row[csf('weight_type')]]."**".$row[csf('order_uom')]."**".$unit_of_measurement[$row[csf('order_uom')]]."**".$row[csf('cutable_width')]."**".$row[csf('order_rate')]."**".$row[csf('cons_rate')]."**".$row[csf('order_amount')]."**".$row[csf('cons_amount')]."**".$row[csf('original_width')]."**".$row[csf('order_ile')]."**".$row[csf('order_ile_cost')]."**".$row[csf('cons_ile')]."**".$row[csf('cons_ile_cost')]."**".$row[csf("prev_reprocess")];       
			//$barcodeBuyerArr[$row[csf('barcode_no')]]=$booking_without_order."__".$po_breakdown_id;
			$barcodeBuyerArr[$row[csf('barcode_no')]]=$booking_without_order."__".$po_breakdown_id."__".$job_ids;
		}

	}

	if(count($barcodeDataArr)<1)
	{
		echo "99";
		die;
	}

    $job_ids_arr = array_filter($job_ids_arr);
    $all_job_ids = implode(",", $job_ids_arr);
    $all_job_ids_arr_cond=""; $poCond="";
    if($db_type==2 && count($job_ids_arr)>999)
    {
    	$all_po_ids_arr_chunk=array_chunk($job_ids_arr,999) ;
    	foreach($all_po_ids_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$poCond.=" a.id in($chunk_arr_value) or ";
    	}

    	$all_job_ids_arr_cond.=" and (".chop($poCond,'or ').")";
    }
    else
    {
    	$all_job_ids_arr_cond=" and a.id in($all_job_ids)";
    }

	if(count($job_ids_arr)>0)
	{
		$po_sql=sql_select("SELECT a.id as job_id,a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, a.style_ref_no, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst $all_job_ids_arr_cond");

		$po_details_array=array();
		foreach($po_sql as $row)
		{
			$po_details_array[$row[csf("job_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("job_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("job_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("job_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("job_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		}
	}

	$non_order_arr = array_filter($non_order_arr);
    $non_order_ids = implode(",", $non_order_arr);
    $all_non_order_cond=""; $nonOrderCond="";
    if($db_type==2 && count($non_order_arr)>999)
    {
    	$all_non_order_arr_chunk=array_chunk($non_order_arr,999) ;
    	foreach($all_non_order_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$nonOrderCond.=" id in($chunk_arr_value) or ";
    	}

    	$all_non_order_cond.=" and (".chop($nonOrderCond,'or ').")";
    }
    else
    {
    	$all_non_order_cond=" and id in($non_order_ids)";
    }

    $non_order_sql = sql_select("select buyer_id, id from wo_non_ord_samp_booking_mst where status_active=1 $all_non_order_cond");
	foreach ($non_order_sql as  $val) 
	{
		$non_order_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
	}
	//echo count($barcodeDataArr);die;
	if(count($barcodeDataArr)>0)
	{
		foreach($barcodeDataArr as $barcode_no=>$value)
		{
			$barcodeDatas=explode("__",$barcodeBuyerArr[$barcode_no]);
			$booking_without_order=$barcodeDatas[0];
			$po_id=$barcodeDatas[1];
			$job_id=$barcodeDatas[2];

			

			if($booking_without_order==1) 
			{
				//$buyer_id= $non_order_ref[$po_id]["buyer_name"];
				$buyer_id= $non_order_ref[$job_id]["buyer_name"];
				$po_no='';
				$job_no='';
			}
			else
			{
				/*$buyer_id=$po_details_array[$po_id]['buyer_name'];
				$po_no=$po_details_array[$po_id]['po_number'];
				$job_no=$po_details_array[$po_id]['job_no'];
				$style_ref_no=$po_details_array[$po_id]['style_ref_no'];*/

				$buyer_id=$po_details_array[$job_id]['buyer_name'];
				$po_no=$po_details_array[$job_id]['po_number'];
				$job_no=$po_details_array[$job_id]['job_no'];
				$style_ref_no=$po_details_array[$job_id]['style_ref_no'];
				

			}
			
			if($po_id=='') { $po_id=0; }
			if($job_id=='') { $job_id=0; }
			
			//$barcodeData.=$value."**".$po_id."**".$buyer_id."**".$po_no."**".$job_no."**".$style_ref_no ."____";
			$barcodeData.=$value."**".$po_id."**".$buyer_id."**".$po_no."**".$job_no."**".$style_ref_no."**".$job_id ."____";
		}
		echo chop($barcodeData,"____");
		//echo substr($barcodeData,0,-1);
	}
	else
	{
		echo "0";
	}
	
	exit();	
}

if($action=="populate_barcode_data_update")
{
	$barcodeData=''; $po_ids_arr=array(); $po_details_array=array(); $barcodeDataArr=array(); $barcodeBuyerArr=array(); $transRollIds=''; $transPoIdsArr=array();
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_arrays=sql_select($sql_deter);
	foreach( $data_arrays as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$issued_data_arr=array(); $barcode_nos='';
	$issued_barcode_data=sql_select("select a.id, a.barcode_no, a.dtls_id, a.roll_id, a.rate, a.qnty, a.po_breakdown_id, a.booking_without_order, b.trans_id,a.reprocess,a.prev_reprocess,b.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box from pro_roll_details a, inv_wvn_finish_fab_iss_dtls b, inv_transaction c where a.dtls_id=b.id and b.trans_id=c.id and c.transaction_type=2 and a.entry_form=195 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.mst_id=$data");

	foreach($issued_barcode_data as $row)
	{
		$issued_data_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['po_id']=$row[csf('po_breakdown_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['booking_without_order']=$row[csf('booking_without_order')];
		$issued_data_arr[$row[csf('barcode_no')]]['id']=$row[csf('id')];
		$issued_data_arr[$row[csf('barcode_no')]]['roll_id']=$row[csf('roll_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['rate']=$row[csf('rate')];
		$issued_data_arr[$row[csf('barcode_no')]]['reprocess']=$row[csf('reprocess')];
		$issued_data_arr[$row[csf('barcode_no')]]['prev_reprocess']=$row[csf('prev_reprocess')];
		$issued_data_arr[$row[csf('barcode_no')]]['qnty']=number_format($row[csf("qnty")],2,'.','');

		$issued_data_arr[$row[csf('barcode_no')]]['store_id']=$row[csf('store_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['floor_id']=$row[csf('floor_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['room']=$row[csf('room')];
		$issued_data_arr[$row[csf('barcode_no')]]['rack']=$row[csf('rack')];
		$issued_data_arr[$row[csf('barcode_no')]]['self']=$row[csf('self')];
		$issued_data_arr[$row[csf('barcode_no')]]['bin_box']=$row[csf('bin_box')];

		$barcode_nos.=$row[csf('barcode_no')].',';
		
		if($row[csf("booking_without_order")]==1)
		{
			$non_order_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		}
		else
		{
			$po_ids_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		}
	}
	$barcode_nos=chop($barcode_nos,',');


	$sql_trans_data = sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, 
	b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, 
	c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,
	c.is_sales,c.reprocess,c.prev_reprocess FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE  a.id=b.mst_id and b.id=c.dtls_id  and c.barcode_no in($barcode_nos)
	and b.trans_id<>0 and a.entry_form in(564) and c.entry_form in(564) and c.status_active=1 
	and c.is_deleted=0 ");

	$transfer_data=array();
	$transfer_data_one=array();
	foreach($sql_trans_data as $row)
	{
		$transfer_data[$row[csf("barcode_no")]]["body_part_id"] = $row[csf("body_part_id")];
		$transfer_data[$row[csf("barcode_no")]]["color_id"] = $row[csf("color_id")];
		$transfer_data[$row[csf("barcode_no")]]["knitting_source"] = $row[csf("knitting_source")];
		$transfer_data[$row[csf("barcode_no")]]["dia_width_type"] = $row[csf("dia_width_type")];
		$transfer_data_one[$row[csf("barcode_no")]]["knitting_company"]=$row[csf("knitting_company")];
		$transfer_data[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
		// $transfer_data[$row[csf("barcode_no")]]["color_id"] = $row[csf("color_id")];
		if($row[csf("knitting_source")]==1)
		{
			$transfer_data[$row[csf("barcode_no")]]["knitting_company"]=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$transfer_data[$row[csf("barcode_no")]]["knitting_company"]=$supplier_arr[$row[csf("knitting_company")]];;
		}
		
	}
	unset($sql_trans_data);
	
	
	// $data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales,c.reprocess,c.prev_reprocess  FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)");

	/*$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,b.id as dtls_id,b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,0 as from_order_id,c.barcode_no,b.dia_width_type, c.id as roll_id,c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales,max(c.reprocess) as reprocess, a.store_id, b.floor, b.room, cast(b.rack_no as varchar2(4000)) as rack_no, b.shelf_no, b.bin,c.shrinkage_shade  
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(564) and c.entry_form in(564) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,b.id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id,c.rate, c.booking_no, 
	c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales, a.store_id, b.floor, b.room, b.rack_no, b.shelf_no, b.bin,c.shrinkage_shade ");*/

	$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,b.id as dtls_id,b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,0 as from_order_id,c.barcode_no,b.dia_width_type, c.id as roll_id,c.roll_no, c.po_breakdown_id, sum(case when c.entry_form in(564) then c.qnty else c.qc_pass_qnty end) as qc_pass_qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,max(c.reprocess) as reprocess, a.store_id, b.floor, cast(b.room as varchar2(4000)) as room, cast(b.rack_no as varchar2(4000)) as rack_no, b.shelf_no, b.bin,c.shrinkage_shade,b.booking_no as pi_booking_no, b.booking_id as pi_booking_id,c.rf_id,c.manual_roll_no, d.fabric_ref ,d.rd_no,b.original_width,b.original_gsm,d.weight_editable,d.width_editable,d.weight_type,d.cutable_width,d.order_uom , d.order_rate,d.cons_rate, d.order_ile,d.order_ile_cost,d.cons_ile, d.cons_ile_cost, d.order_amount,d.cons_amount  FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction d, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id=d.id and a.id=d.mst_id and b.trans_id<>0 and a.entry_form in(564) and c.entry_form in(564) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos) and c.re_transfer = 0 group by a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis,a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,b.id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id, c.roll_no, c.po_breakdown_id, c.roll_id,c.rate, c.booking_no, c.booking_without_order, c.is_transfer, c.transfer_criteria, a.store_id, b.floor, b.room, b.rack_no, b.shelf_no, b.bin,c.shrinkage_shade,b.booking_no,b.booking_id,c.rf_id,c.manual_roll_no, d.fabric_ref ,d.rd_no,b.original_width,b.original_gsm,d.weight_editable,d.width_editable,d.weight_type,d.cutable_width,d.order_uom , d.order_rate,d.cons_rate, d.order_ile,d.order_ile_cost,d.cons_ile, d.cons_ile_cost, d.order_amount,d.cons_amount");

	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			$from_order_ids .= $row[csf("from_order_id")].",";
		}
	}

	$from_order_ids = chop($from_order_ids,",");

	$book_booking_arr=return_library_array("select po_break_down_id, booking_no from wo_booking_dtls where booking_type=1 and po_break_down_id in ($from_order_ids) ",'po_break_down_id','booking_no');
	
	$is_sales_arr = array();
	foreach($data_array as $row)
	{
		$from_order_ids .= $row[csf("from_order_id")].",";
		$is_sales_arr[$row[csf('barcode_no')]] = $row[csf("is_sales")];
		$booking_no_id = $row[csf('po_breakdown_id')];
		if($row[csf("receive_basis")] > 0)
		{	
			$receive_basis=$receive_basis_arr[$row[csf("receive_basis")]];
			$receive_basis_id=$row[csf("receive_basis")];
		}
		else
		{
			$receive_basis_id = $transfer_data[$row[csf("barcode_no")]]["receive_basis"];
			$receive_basis = $receive_basis_arr[$receive_basis_id];
		}
		
		// $receive_basis=$receive_basis_arr[$row[csf("receive_basis")]];
		// $receive_basis_id=$row[csf("receive_basis")];
		if($row[csf("knitting_source")]==1)
		{
			$knit_company=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$knit_company=$supplier_arr[$row[csf("knitting_company")]];
		}
		
		/*$rate=$row[csf("rate")];
		if($row[csf("entry_form")]==68)
		{
			$roll_id=$row[csf("roll_id_prev")];
			//$row[csf("booking_no")]=$row[csf("recv_number")];
			$row[csf("booking_id")]=$row[csf("id")];
		}
		else
		{*/
		$roll_id=$row[csf("roll_id")];
		//}
		
		$booking_without_order = $issued_data_arr[$row[csf('barcode_no')]]['booking_without_order'];
		$po_id = $issued_data_arr[$row[csf('barcode_no')]]['po_id'];
		
		
		if($transfer_data[$row[csf("barcode_no")]]["body_part_id"]){
			$body_part_id = $transfer_data[$row[csf("barcode_no")]]["body_part_id"];
		}else{
			$body_part_id =$row[csf("body_part_id")];
		}

		
		$color='';
		// $color_id=explode(",",$row[csf('color_id')]);
		// foreach($color_id as $val)
		// {
		// 	if($val>0) $color.=$color_arr[$val].",";
		// }
		// $color=chop($color,',');	

		if($transfer_data[$row[csf("barcode_no")]]["color_id"]){
			$color_id = $transfer_data[$row[csf("barcode_no")]]["color_id"];
			$color .= $color_arr[$color_id];
		}else{
			$color_id =$row[csf("color_id")];
			$color .=$color_arr[$color_id];
		}

		if($transfer_data[$row[csf("barcode_no")]]["knitting_company"])
		{
			$knit_company = $transfer_data[$row[csf("barcode_no")]]["knitting_company"];
		}

		if($transfer_data_one[$row[csf("barcode_no")]]["knitting_company"])
		{
			$knit_company_one = $transfer_data_one[$row[csf("barcode_no")]]["knitting_company"];
		}else{
			$knit_company_one = $row[csf("knitting_company")];
		}			

		if($transfer_data[$row[csf("barcode_no")]]["knitting_source"])
		{
			$knitting_source_one = $transfer_data[$row[csf("barcode_no")]]["knitting_source"];
		}else{
			$knitting_source_one =$row[csf("knitting_source")];
		}

		if($transfer_data[$row[csf("barcode_no")]]["dia_width_type"])
		{
			$dia_width_type = $transfer_data[$row[csf("barcode_no")]]["dia_width_type"];
		}else{
			$dia_width_type =$row[csf("dia_width_type")];
		}

		if($row[csf("booking_no")] > 0 )
		{
			$book_booking = $row[csf("booking_no")];
		}
		else{
			$book_booking = $book_booking_arr[$row[csf("from_order_id")]];
		}

		//$barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("barcode_no")]."**".$row[csf("company_id")]."**".$row[csf("roll_no")]."**".$roll_id."**".$body_part_id."**".$body_part[$body_part_id]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("recv_number")]."**".$row[csf("booking_id")]."**".$color."**".$color_id."**".$knitting_source_one."**".$knitting_source[$knitting_source_one]."**".$knit_company_one."**".$knit_company."**".$row[csf("batch_id")]."**".$dia_width_type."**".$fabric_typee[$dia_width_type]."**".$batch_name_array[$row[csf("batch_id")]]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".number_format($row[csf("qnty")],2,'.','')."**".$rate."**".$booking_without_order."**".$book_booking."**".$row[csf("shrinkage_shade")]."**".$fabric_shade[$row[csf("shrinkage_shade")]];
		$barcodeBuyerArr[$row[csf('barcode_no')]]=$booking_without_order."__".$po_id;


		$barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("barcode_no")]."**".$company_id."**".$row[csf("roll_no")]."**".$roll_id."**".$body_part_id."**".$body_part[$body_part_id]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("recv_number")]."**".$row[csf("booking_id")]."**".$color."**".$color_id."**".$knitting_source_one."**".$knitting_source[$knitting_source_one]."**".$knit_company_one."**".$knit_company."**".$row[csf("batch_id")]."**".$dia_width_type."**".$fabric_typee[$dia_width_type]."**".$batch_name_array[$row[csf("batch_id")]]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".number_format($row[csf("qc_pass_qnty")],2,'.','')."**".$rate."**".$booking_without_order."**".$row[csf("reprocess")]."**".$book_booking."**".$floor_id."**".$room_id."**".$rack_id."**".$shelf_id."**".$bin_id."**".$floor_no."**".$room_no."**".$rack_no."**".$shelf_no."**".$bin_no."**".$store_id."**".$row[csf('shrinkage_shade')]."**".$fabric_shade[$row[csf('shrinkage_shade')]]."**".$row[csf('pi_booking_no')]."**".$row[csf('pi_booking_id')]."**".$row[csf('rf_id')]."**".$row[csf('manual_roll_no')]."**".$row[csf('fabric_ref')]."**".$row[csf('rd_no')]."**".$row[csf('original_gsm')]."**".$row[csf('weight_editable')]."**".$row[csf('weight_type')]."**".$fabric_weight_type[$row[csf('weight_type')]]."**".$row[csf('order_uom')]."**".$unit_of_measurement[$row[csf('order_uom')]]."**".$row[csf('cutable_width')]."**".$row[csf('order_rate')]."**".$row[csf('cons_rate')]."**".$row[csf('order_amount')]."**".$row[csf('cons_amount')]."**".$row[csf('original_width')]."**".$row[csf('order_ile')]."**".$row[csf('order_ile_cost')]."**".$row[csf('cons_ile')]."**".$row[csf('cons_ile_cost')];       
		$barcodeBuyerArr[$row[csf('barcode_no')]]=$booking_without_order."__".$po_id;



		// $barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("barcode_no")]."**".$row[csf("company_id")]."**".$row[csf("roll_no")]."**".$roll_id."**".$row[csf("body_part_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("recv_number")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("color_id")]."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("knitting_company")]."**".$knit_company."**".$row[csf("batch_id")]."**".$row[csf("dia_width_type")]."**".$fabric_typee[$row[csf("dia_width_type")]]."**".$batch_name_array[$row[csf("batch_id")]]."**".$row[csf("prod_id")]."**".$row[csf("fabric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".number_format($row[csf("qnty")],2,'.','')."**".$rate."**".$booking_without_order."**".$row[csf("booking_no")];
		// $barcodeBuyerArr[$row[csf('barcode_no')]]=$booking_without_order."__".$po_id;
	}
	
	if(count($barcodeDataArr)<1)
	{
		echo "99";
		die;
	}
	
	$po_ids_arr = array_filter($po_ids_arr);
	if(count($po_ids_arr)>0)
	{	
	    $all_po_ids = implode(",", $po_ids_arr);
	    $all_po_ids_arr_cond=""; $poCond="";
	    if($db_type==2 && count($po_ids_arr)>999)
	    {
	    	$all_po_ids_arr_chunk=array_chunk($po_ids_arr,999) ;
	    	foreach($all_po_ids_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$poCond.=" b.id in($chunk_arr_value) or ";
	    	}

	    	$all_po_ids_arr_cond.=" and (".chop($poCond,'or ').")";
	    }
	    else
	    {
	    	$all_po_ids_arr_cond=" and b.id in($all_po_ids)";
	    }
		$data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, b.po_number, b.id as po_id,a.style_ref_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst $all_po_ids_arr_cond");
		$po_details_array=array();
		foreach($data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		}
	}

	$non_order_arr = array_filter($non_order_arr);
    $non_order_ids = implode(",", $non_order_arr);
    $all_non_order_cond=""; $nonOrderCond="";
    if($db_type==2 && count($non_order_arr)>999)
    {
    	$all_non_order_arr_chunk=array_chunk($non_order_arr,999) ;
    	foreach($all_non_order_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$nonOrderCond.=" id in($chunk_arr_value) or ";
    	}

    	$all_non_order_cond.=" and (".chop($nonOrderCond,'or ').")";
    }
    else
    {
    	$all_non_order_cond=" and id in($non_order_ids)";
    }

    $non_order_sql = sql_select("select buyer_id, id from wo_non_ord_samp_booking_mst where status_active=1 $all_non_order_cond");
	foreach ($non_order_sql as  $val) 
	{
		$non_order_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
	}

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name , e.floor_room_rack_name bin_name 
		from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		left join lib_floor_room_rack_mst e on b.bin_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		where b.status_active=1 and b.is_deleted=0 --and b.store_id=$store_id";
		$lib_floor_arr=sql_select($lib_room_rack_shelf_sql);
		foreach ($lib_floor_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_floor_arr[$company][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_room_arr[$company][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
				$lib_rack_arr[$company][$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
				$lib_shelf_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$company][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}
		}

	if(count($barcodeDataArr)>0)
	{
		foreach($barcodeDataArr as $barcode_no=>$value)
		{
			$barcodeDatas=explode("__",$barcodeBuyerArr[$barcode_no]);
			$booking_without_order=$barcodeDatas[0];
			$po_id=$barcodeDatas[1];
			
			if($booking_without_order==1) 
			{
				$buyer_id=$non_order_ref[$po_id]["buyer_name"];
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
			$is_sales = 0;
			
			$barcodeData.=$value."**".$po_id."**".$buyer_id."**".$po_no."**".$job_no . "**" . $is_sales ."##";
		}
		//echo substr($barcodeData,0,-1);
	}
	$i=count($barcodeDataArr);
	foreach($barcodeDataArr as $barcode_no=>$value)
	{
		$barcodeDatas=explode("**",$value);
	    $company_id=$barcodeDatas[1];
		$roll_no=$barcodeDatas[2];
		$roll_id=$barcodeDatas[3];
		$body_part=$barcodeDatas[5];
		$body_part_id=$barcodeDatas[4];
		$bwo=$barcodeDatas[6];
		$receive_basis=$barcodeDatas[7];
		$receive_basis_id=$barcodeDatas[8];
		$booking_no=$barcodeDatas[9];
		$booking_id=$barcodeDatas[10];
		$color=$barcodeDatas[11];
		$color_id=$barcodeDatas[12];
		$knitting_source_id=$barcodeDatas[13];
		$knitting_source=$barcodeDatas[14];
		$knitting_company_id=$barcodeDatas[15];
		$knit_company=$barcodeDatas[16];
		$batch_id=$barcodeDatas[17];
		$diawidth_type_id=$barcodeDatas[18];
		$diawidth_type=$barcodeDatas[19];
		$batch_name=$barcodeDatas[20];	
		
		$prod_id=$barcodeDatas[21];
		$deter_id=$barcodeDatas[22];
		//alert(data[22])
		$cons_comp=$constructtion_arr[$deter_id].", ".$composition_arr[$deter_id];
		$gsm=$barcodeDatas[23];
		$width=$barcodeDatas[24];
		$qnty=$barcodeDatas[25];
		$rate=$barcodeDatas[26];
		$booking_without_order=$barcodeDatas[27];
		$reprocess=$barcodeDatas[28];
		$woPiNo=$barcodeDatas[29];

		$floor_id=$barcodeDatas[30];
		$room_id=$barcodeDatas[31];
		$rack_id=$barcodeDatas[32];
		$shelf_id=$barcodeDatas[33];
		$bin_id=$barcodeDatas[34];

		$floor=$barcodeDatas[35];
		$room=$barcodeDatas[36];
		$rack=$barcodeDatas[37];
		$shelf=$barcodeDatas[38];
		$bin=$barcodeDatas[39];
		$barcode_store_id=$barcodeDatas[40];
		$shade_id=$barcodeDatas[41];
		$shade_name=$barcodeDatas[42];

		//------------------
		$bookingNoOriginal=$barcodeDatas[43];
		$bookingIdOriginal=$barcodeDatas[44];
		$rfId=$barcodeDatas[45];
		$manual_roll_no=$barcodeDatas[46];
		$fabric_ref=$barcodeDatas[47];
		$rd_no=$barcodeDatas[48];
		$original_gsm=$barcodeDatas[49];
		$weight_editable=$barcodeDatas[50];
		$weight_type_id=$barcodeDatas[51];
		$weight_type=$barcodeDatas[52];
		$order_uom_id=$barcodeDatas[53];
		$order_uom_name=$barcodeDatas[54];
		$cutable_width=$barcodeDatas[55];

		$order_rate=$barcodeDatas[56];
		$cons_rate=$barcodeDatas[57];
		$order_amount=$barcodeDatas[58];
		$cons_amount=$barcodeDatas[59];
				
		$original_dia=$barcodeDatas[60];	

		$order_ile=$barcodeDatas[61];	
		$order_ile_cost=$barcodeDatas[62];	
		$cons_ile=$barcodeDatas[63];	
		$cons_ile_cost=$barcodeDatas[64];	
		//----------------

		$reprocess=$issued_data_arr[$barcode_no]['reprocess'];
		$previous_re=$issued_data_arr[$barcode_no]['prev_reprocess'];
	
	
		$dtls_id=$issued_data_arr[$barcode_no]['dtls_id'];
		$trans_id=$issued_data_arr[$barcode_no]['trans_id'];
		$po_id=$issued_data_arr[$barcode_no]['po_id'];
		$roll_table_id=$issued_data_arr[$barcode_no]['id'];
		$roll_id=$issued_data_arr[$barcode_no]['roll_id'];
		$rate=$issued_data_arr[$barcode_no]['rate'];
		$qnty=$issued_data_arr[$barcode_no]['qnty'];
		$store_id=$issued_data_arr[$barcode_no]['store_id'];

		
		if($booking_without_order==1) 
		{
			$buyer_id=$non_order_ref[$po_id]["buyer_name"];
			$buyer_name=$buyer_name_array[$buyer_id];
			$job_no='';
			$po_no='';
			$styleRef='';
		}
		else
		{
			$buyer_id=$po_details_array[$po_id]['buyer_name'];
			$buyer_name=$buyer_name_array[$po_details_array[$po_id]['buyer_name']];
			$po_no=$po_details_array[$po_id]['po_number'];
			$job_no=$po_details_array[$po_id]['job_no'];
			$styleRef=$po_details_array[$po_id]['style_ref_no'];
		}

		/*$floor = $issued_data_arr[$barcode_no]['floor_id'];
		$room = $issued_data_arr[$barcode_no]['room'];
		$rack = $issued_data_arr[$barcode_no]['rack'];
		$shelf = $issued_data_arr[$barcode_no]['self'];
		$bin = $issued_data_arr[$barcode_no]['bin_box'];

		$floor_no 	= $lib_floor_arr[$company_name][$floor];
		$room_no 	= $lib_room_arr[$company_name][$floor][$room];
		$rack_no	= $lib_rack_arr[$company_name][$floor][$room][$rack];
		$shelf_no 	= $lib_shelf_arr[$company_name][$floor][$room][$rack][$shelf];
		$bin_no 	= $lib_bin_arr[$company_name][$floor][$room][$rack][$shelf][$bin];*/

		?>
        <tr id="tr_<? echo $i; ?>" align="center" valign="middle">
			<td style="word-break:break-all;" width="35" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
			<td style="word-break:break-all;" width="100" id="bookingNo_<? echo $i; ?>"><? echo $bookingNoOriginal; ?></td>
			<td style="word-break:break-all;" width="80" id="job_<? echo $i; ?>"><? echo $job_no; ?></td>
			<td style="word-break:break-all;" width="100" id="styleRef_<? echo $i; ?>"><? echo $styleRef; ?></td>
			<td style="word-break:break-all;" width="80" id="order_<? echo $i; ?>" align="left"><? echo $po_no; ?></td>
			<td style="word-break:break-all;" width="80" id="barcode_<? echo $i; ?>"><? echo $barcode_no; ?></td>
			<td style="word-break:break-all;" width="100" id="rfId_<? echo $i; ?>"><? echo $rfId; ?></td>
			<td style="word-break:break-all;" width="70" id="shade_<? echo $i; ?>"><? echo $shade_name; ?></td>
			<td style="word-break:break-all;" width="50" id="manualRollNo_<? echo $i; ?>"><? echo $manual_roll_no; ?></td>

			<td style="word-break:break-all;" width="60" id="batch_<? echo $i; ?>"><? echo $batch_name; ?></td> 
			<td style="word-break:break-all;" width="80" id="bodyPart_<? echo $i; ?>"><? echo $body_part; ?></td>
			<td style="word-break:break-all;" width="130" id="cons_<? echo $i; ?>" align="left"><? echo $cons_comp; ?></td>
			<td style="word-break:break-all;" width="100" id="fabricRef_<? echo $i; ?>"><? echo $fabric_ref; ?></td>
			<td style="word-break:break-all;" width="100" id="rdNo_<? echo $i; ?>"><? echo $rd_no; ?></td>
			<td style="word-break:break-all;" width="70" id="color_<? echo $i; ?>"><? echo $color; ?></td>
			<td style="word-break:break-all;" width="50" id="originalGsm_<? echo $i; ?>"><? echo $original_gsm; ?></td>
			<td style="word-break:break-all;" width="50" id="weightEditable_<? echo $i; ?>"><? echo $weight_editable; ?></td>
			<td style="word-break:break-all;" width="50" id="weightType_<? echo $i; ?>"><? echo $weight_type; ?></td>
			<td style="word-break:break-all;" width="50" id="orderUom_<? echo $i; ?>"><? echo $order_uom_name; ?></td>
			<td style="word-break:break-all;" width="50" id="dia_<? echo $i; ?>"><? echo $width; ?></td>
			<td style="word-break:break-all;" width="50" id="cutWidth_<? echo $i; ?>"><? echo $cutable_width; ?></td>
			<td style="word-break:break-all;" width="70" align="right" id="rollWeight_<? echo $i; ?>"><? echo $qnty; ?></td>
			<td style="word-break:break-all;" width="50" align="right" id="txtBalancePI_<? echo $i; ?>"><?  ?></td>
			<td style="word-break:break-all;" width="50" align="right" id="txtIle_<? echo $i; ?>"><? echo $order_ile_cost; ?></td>

			<td style="word-break:break-all;" width="70" align="right" id="floor_<? echo $i; ?>"><? echo $floor; ?></td>
			<td style="word-break:break-all;" width="70" align="right" id="room_<? echo $i; ?>"><? echo $room; ?></td>
			<td style="word-break:break-all;" width="70" align="right" id="rack_<? echo $i; ?>"><? echo $rack; ?></td>
			<td style="word-break:break-all;" width="70" align="right" id="shelf_<? echo $i; ?>"><? echo $shelf; ?></td>
			<td style="word-break:break-all;" width="70" align="right" id="bin_<? echo $i; ?>"><? echo $bin; ?></td>
            <td id="button_<? echo $i; ?>" align="center"  width="50">
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
				<input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<?php echo $deter_id;?>"/>
				<input type="hidden" name="diatypeId[]" id="diatypeId_<? echo $i; ?>" value="<?php echo $diawidth_type_id;?>"/>
				<input type="hidden" name="knittingcomId[]" id="knittingcomId_<? echo $i; ?>" value="<?php echo $knitting_company_id;?>"/>
				<input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<?php echo $job_no;?>"/>
				<input type="hidden" name="reProcess[]" id="reProcess_<? echo $i; ?>" value="<?php echo $reprocess;?>"/>
				<input type="hidden" name="preRerocess[]" id="preRerocess_<? echo $i; ?>" value="<?php echo $previous_re;?>"/>
				<input type="hidden" name="bwoNo[]" id="bwoNo_<? echo $i; ?>" value="<?php echo $bwo;?>"/>
				<input type="hidden" name="bookingWithoutOrderStatus[]" id="bookingWithoutOrderStatus_<? echo $i; ?>" value="<?php echo $booking_without_order;?>"/> 
				<input type="hidden" value="<? echo $floor_id;?>" name="floorId[]" id="floorId_<? echo $i; ?>"/>
				<input type="hidden" value="<? echo $room_id;?>" name="roomId[]" id="roomId_<? echo $i; ?>"/>
				<input type="hidden" value="<? echo $rack_id;?>" name="rackId[]" id="rackId_<? echo $i; ?>"/>
				<input type="hidden" value="<? echo $shelf_id;?>" name="shelfId[]" id="shelfId_<? echo $i; ?>"/>
				<input type="hidden" value="<? echo $bin_id;?>" name="binId[]" id="binId_<? echo $i; ?>"/>
				<input type="hidden" value="<? echo $store_id;?>" name="hdnStoreId[]" id="hdnStoreId_<? echo $i; ?>"/> 
				<input type="hidden" value="<? echo $shadeId;?>" name="hdnShadeId[]" id="hdnShadeId_<? echo $i; ?>"/> 

				<input type="hidden" name="hdnBookingId[]" id="hdnBookingId_<? echo $i; ?>" value="<?php echo $bookingIdOriginal;?>"/>
				<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<? echo $i; ?>" value="<?php echo $bookingNoOriginal;?>"/>
				<input type="hidden" name="hdnRfId[]" id="hdnRfId_<? echo $i; ?>" value="<?php echo $rfId;?>"/>
				<input type="hidden" name="hdnManualRollNo[]" id="hdnManualRollNo_<? echo $i; ?>" value="<?php echo $manual_roll_no;?>"/>
				<input type="hidden" name="hdnFabricRef[]" id="hdnFabricRef_<? echo $i; ?>" value="<?php echo $fabric_ref;?>"/>
				<input type="hidden" name="hdnRdNo[]" id="hdnRdNo_<? echo $i; ?>" value="<?php echo $rd_no;?>"/>
				<input type="hidden" name="hdnRollNo[]" id="hdnRollNo_<? echo $i; ?>" value="<?php echo $roll_no;?>"/>
				<input type="hidden" name="weightTypeId[]" id="weightTypeId_<? echo $i; ?>" value="<?php echo $weight_type_id;?>"/>
				<input type="hidden" name="orderUomId[]" id="orderUomId_<? echo $i; ?>" value="<?php echo $order_uom_id;?>"/>
				<input type="hidden" name="hdnOriginalGsm[]" id="hdnOriginalGsm_<? echo $i; ?>" value="<?php echo $original_gsm;?>"/>
				<input type="hidden" name="hdnOriginalDia[]" id="hdnOriginalDia_<? echo $i; ?>" value="<?php echo $original_dia;?>"/>
				<input type="hidden" name="hdnWeightEditable[]" id="hdnWeightEditable_<? echo $i; ?>" value="<?php echo $weight_editable;?>"/>
				<input type="hidden" name="hdnCutWidth[]" id="hdnCutWidth_<? echo $i; ?>" value="<?php echo $cutable_width;?>"/>
				<input type="hidden" name="hdnOrdRate[]" id="hdnOrdRate_<? echo $i; ?>" value="<?php echo $order_rate;?>"/>
				<input type="hidden" name="hdnConsRate[]" id="hdnConsRate_<? echo $i; ?>" value="<?php echo $cons_rate;?>"/>
				<input type="hidden" name="hdnOrdAmnt[]" id="hdnOrdAmnt_<? echo $i; ?>" value="<?php echo $order_amount;?>"/>
				<input type="hidden" name="hdnConsAmnt[]" id="hdnConsAmnt_<? echo $i; ?>" value="<?php echo $cons_amount;?>"/>
				<input type="hidden" name="hdnBatchName[]" id="hdnBatchName_<? echo $i; ?>" value="<?php echo $batch_name;?>"/>
				<input type="hidden" name="hdnOrderIle[]" id="hdnOrderIle_<? echo $i; ?>" value="<?php echo $order_ile;?>"/>
				<input type="hidden" name="hdnOrderIleCost[]" id="hdnOrderIleCost_<? echo $i; ?>" value="<?php echo $order_ile_cost;?>"/>
				<input type="hidden" name="hdnConsIle[]" id="hdnConsIle_<? echo $i; ?>" value="<?php echo $cons_ile;?>"/>
				<input type="hidden" name="hdnConsIleCost[]" id="hdnConsIleCost_<? echo $i; ?>" value="<?php echo $cons_ile_cost;?>"/>              
            </td>
        </tr>
			
		<?
		$i--;
	}
	
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
	
	$sql = "select id, batch_no, extention_no, batch_date, batch_weight, booking_no, color_id, batch_against, booking_without_order, re_dyeing_from from pro_batch_create_mst where entry_form in (564)  and batch_against<>4 and company_id=$company_id and status_active=1 and is_deleted=0 $search_field_cond $date_cond"; 
	//and batch_for=1
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
	$sql="select id, batch_no from pro_batch_create_mst where batch_no='".trim($data[0])."' and company_id='".$data[1]."' and is_deleted=0 and status_active=1 and entry_form in (0,7,37) and batch_against<>4 order by id desc";
	//and batch_for=1

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

if($action=="check_report_button")
{
	$sql="select format_id from lib_report_template where template_name='".trim($data)."' and report_id=164 and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql);
	if(count($data_array)>0)
	{
		echo $data_array[0][csf('format_id')];
	}
	else
	{
		echo "";
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_reqn_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_reqn_search_list_view', 'search_div', 'woven_finish_fabric_issue_roll_wise_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$cbo_year =$data[4];

	if($db_type==2) 
	{
		 $year_cond=" and TO_CHAR(a.insert_date,'YYYY')=$cbo_year"; 
	}
    else if($db_type==0) 
	{ 
		$year_cond=" and year(a.insert_date)=$cbo_year"; 
	}



	$lay_plan_arr=return_library_array( "select id, cutting_no from ppl_cut_lay_mst",'id','cutting_no');
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.reqn_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.reqn_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond="and a.reqn_number like '$search_string'";
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
	
	$sql = "select a.id, $year_field a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date,sum(reqn_qty) as reqn_qty from pro_fab_reqn_for_cutting_mst a,pro_fab_reqn_for_cutting_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in(619) and a.company_id=$company_id $search_field_cond $location_cond $date_cond $year_cond group by  a.id, a.insert_date, a.reqn_number_prefix_num, a.reqn_number, a.lay_plan_id, a.reqn_date  order by a.id DESC"; 

	$arr=array(3=>$lay_plan_arr);
	
	echo create_list_view("tbl_list_search", "Year, Requisition No, Requisition Date, Lay Plan Cutting No", "80,150,150","700","200",0, $sql, "js_set_value", "id,reqn_number,reqn_qty", "", 1, "0,0,0,lay_plan_id", $arr, "year,reqn_number_prefix_num,reqn_date,lay_plan_id","","",'0,0,3,0','');
	
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
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$dataArray=sql_select("select issue_purpose, issue_date, insert_date, knit_dye_source, knit_dye_company,req_no, batch_no,store_id,remarks from inv_issue_master where id=$update_id");

	$store_id = $dataArray[0][csf('store_id')];
	
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
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=2");
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

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	?>
    <div>
        <table width="1200" cellspacing="0">
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
				<td width="100"><strong>Insert Date:</strong></td><td width="175px"><? echo date('d-m-Y h:i:s a', strtotime($dataArray[0][csf('insert_date')]));//change_date_format($dataArray[0][csf('insert_date')]);$insert_date ?></td>
            </tr>
            <tr>
            	<td><strong>Dyeing Source:</strong></td><td width="175px"><? echo $knitting_source[$dataArray[0][csf('knit_dye_source')]]; ?></td>
                <td><strong>Issue Purpose:</strong></td><td width="175px"><? echo $yarn_issue_purpose[$dataArray[0][csf('issue_purpose')]]; ?></td>
                <td><strong>Batch Number:</strong></td><td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_no')]]; ?></td>
				<td>&nbsp;</td>
            </tr>
           
            <tr>
                 <td><strong>Reqsn No:</strong></td><td ><? echo $dataArray[0][csf('req_no')]; ?></td>
                 <td><strong>Remarks:</strong></td><td colspan="3"><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
            <tr>
                <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1630"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="20">SL</th>
                <th width="90">Barcode</th>
                <th width="50">Product Id</th>
                <th width="60">Batch No</th>
                <th width="70">Order No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="60">Basis</th>
                <th width="100">Prog/Book/ PI No</th>
                <th width="100">Knit Company</th>
                <th width="80">Body Part</th>
                <th width="130">Construction/ Composition</th> 	
                <th width="70">Width Type</th>
                <th width="40">GSM</th>
                <th width="40">Dia</th>
                <th width="70">Color</th>
				<th width="80">Floor</th>
				<th width="80">Room</th>
				<th width="80">Rack</th>
				<th width="80">Self</th>
				<th width="80">Bin</th>
                <th width="40">Roll</th>
                <th>Issue Qty</th> 
            </thead>
            <?
			
			
			$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id,
			a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id,
			b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0
			and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0");
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

		
				$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name,
				e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
				from lib_floor_room_rack_dtls b 
				left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
				left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0 
				left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0 
				left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0 
				left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0 
				where b.status_active=1 and b.is_deleted=0 and b.company_id=$company and b.store_id=$store_id
				order by a.floor_room_rack_name , c.floor_room_rack_name , d.floor_room_rack_name , e.floor_room_rack_name , f.floor_room_rack_name";
				//echo $lib_room_rack_shelf_sql;die;
				$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
				if(!empty($lib_rrsb_arr))
				{
					foreach ($lib_rrsb_arr as $room_rack_shelf_row) {
						$company  = $room_rack_shelf_row[csf("company_id")];
						$floor_id = $room_rack_shelf_row[csf("floor_id")];
						$room_id  = $room_rack_shelf_row[csf("room_id")];
						$rack_id  = $room_rack_shelf_row[csf("rack_id")];
						$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
						$bin_id   = $room_rack_shelf_row[csf("bin_id")];
			
						if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
							$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
						}
			
						if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
							$lib_room_arr[$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
						}
			
						if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
							$lib_rack_arr[$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
						}
			
						if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
							$lib_shelf_arr[$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
						}
						if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
							$lib_bin_arr[$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
						}
			
			
					}
				}
			
				$roll_arr=return_library_array( "select id, roll_no from pro_roll_details where entry_form in(2,22)", "id", "roll_no");
				$i=1; $tot_qty=0; 
            	$sql = "select a.batch_id, a.issue_qnty, a.prod_id, a.issue_qnty, a.knitting_company,a.floor, a.room, a.rack_no, a.shelf_no, a.bin_box,b.roll_no,b.roll_id,b.barcode_no,b.po_breakdown_id from inv_finish_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=71 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by roll_no";
				//echo $sql;
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
                    <td style="word-break:break-all;"><? echo $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['receive_basis']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['booking_no']; ?></td>
                    <td><? echo $roll_details_array[$row[csf("barcode_no")]]['knitting_company']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['body_part']; ?></td>
                    <td style="word-break:break-all;"><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']];?></td>
                    
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['dia_width_type']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['gsm'] ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['width'] ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['color'] ?></td>
					<td style="word-break:break-all;" align="center"><? echo $lib_floor_arr[$row[csf("floor")]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $lib_room_arr[$row[csf("floor")]][$row[csf("room")]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $lib_rack_arr[$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $lib_shelf_arr[$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf('shelf_no')]]; ?></td>
					<td style="word-break:break-all;" align="center"><? echo $lib_bin_arr[$row[csf("floor")]][$row[csf("room")]][$row[csf("rack_no")]][$row[csf('shelf_no')]][$row[csf('bin_box')]]; ?></td>
                    <td  align="center"><? echo $row[csf('roll_no')]; ?></td>
                    <td align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
                </tr>
                <?
					$tot_qty+=$row[csf('issue_qnty')];
					$i++;
				}
			?>
            <tr> 
                <td align="right" colspan="21"><strong>Total</strong></td>
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
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=2");
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
			and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0");
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
        	$sql = "select a.batch_id, a.issue_qnty, a.prod_id, a.issue_qnty, a.knitting_company, a.remarks, b.roll_no,b.roll_id,b.barcode_no,b.po_breakdown_id from inv_finish_fabric_issue_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=71 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by roll_no";
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
                <th width="80">GSM</th>
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
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['gsm'] ?></td>
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
                <td align="left" colspan="7"><strong>Total</strong></td>
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
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=2");
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
                <th width="100">Knit Company</th>
                <th width="100">Prod. Source</th>
                <th width="80">Body Part</th>
                <th width="130">Fabric Type</th> 	
                <th width="70">Width Type</th>
                <th width="40">GSM</th>
                <th width="40">Dia</th>
                <th width="70">Color</th>
                <th width="40">Roll</th>
                <th width="60">Reject Qty</th>
                <th>Issue Qty</th> 
            </thead>
            <?
			
			$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id,
			a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id,
			b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id,c.qnty
			FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0
			and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0");
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
where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=71 and a.status_active=1 and a.is_deleted=0 and b.status_active=1
and b.is_deleted=0 group by a.batch_id, a.prod_id, a.knitting_company,b.po_breakdown_id order by a.prod_id DESC";*/
  				
				if($db_type==0)
				{
   				 	$sql = "select c.pi_wo_batch_no as batch_id, sum(a.issue_qnty) as issue_qnty, a.prod_id, a.knitting_company,a.body_part_id,count(b.roll_id) as no_of_roll, sum(b.reject_qnty) as reject_qnty,b.po_breakdown_id,group_concat(b.barcode_no) as barcode_nos from inv_finish_fabric_issue_dtls a, pro_roll_details b,  inv_transaction c where a.id=b.dtls_id and a.trans_id= c.id and a.mst_id=$update_id and b.entry_form=71 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.pi_wo_batch_no, a.prod_id, a.knitting_company,a.body_part_id,b.po_breakdown_id"; 
				}
				else
				{
					$sql = "select c.pi_wo_batch_no as batch_id, sum(a.issue_qnty) as issue_qnty, a.prod_id, a.knitting_company,a.body_part_id,count(b.roll_id) as no_of_roll, sum(b.reject_qnty) as reject_qnty,b.po_breakdown_id,LISTAGG(b.barcode_no, ',')  WITHIN GROUP (ORDER BY b.id desc) as barcode_nos from inv_finish_fabric_issue_dtls a, pro_roll_details b, inv_transaction c where a.id=b.dtls_id and a.trans_id= c.id and a.mst_id=$update_id and b.entry_form=71 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by c.pi_wo_batch_no, a.prod_id, a.knitting_company,a.body_part_id,b.po_breakdown_id"; 
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
                    <td align="center" style="word-break:break-all;">
                    	<? 
                    		echo $batch_arr[$row[csf("batch_id")]];
                    		//echo $roll_details_array[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]][$barcodeData]['batch_name']; 
                    	?>
                    </td>
                    <td style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
                    <td style="word-break:break-all;"><? echo $buyer_library[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']]."<br>".$job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
                    <td><? echo $roll_details_array[$row[csf("prod_id")]]['knitting_company']; ?></td>
                    <td><? echo  $knitting_source[$roll_details_array[$row[csf("prod_id")]]['knitting_source_id']]; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("prod_id")]]['body_part'];//echo $body_part[$row[csf("body_part_id")]]; ?></td>
                    <td style="word-break:break-all;"><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']];?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("prod_id")]]['dia_width_type']; ?></td>
                    <td style="word-break:break-all;"><? echo $roll_details_array[$row[csf("prod_id")]]['gsm'] ?></td>
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
                <td align="right" colspan="15"><strong>Total</strong></td>
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

	$dataArray=sql_select("select count(b.id) as total_roll,sum(b.qnty) as total_qty,a.issue_date,a.knit_dye_source,a.knit_dye_company from inv_issue_master a, pro_roll_details b where a.id=$update_id and  a.id=b.mst_id and b.entry_form=71 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.issue_date,a.knit_dye_source,a.knit_dye_company");
	
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

//actn_print_button_3
if($action=="actn_print_button_3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];
	
	//for issue purpose
	$sqlIssue = "SELECT a.issue_purpose AS ISSUE_PURPOSE, a.issue_date AS ISSUE_DATE, a.insert_date as INSERT_DATE, a.knit_dye_source AS KNIT_DYE_SOURCE, a.knit_dye_company AS KNIT_DYE_COMPANY, a.req_no AS REQ_NO, a.batch_no AS BATCH_NO, a.FLOOR_ID AS CUTTING_FLOOR, a.store_id AS STORE_ID, a.remarks AS REMARKS, b.batch_id AS BATCH_ID, b.issue_qnty AS ISSUE_QNTY, b.prod_id AS PROD_ID, b.issue_qnty AS ISSUE_QNTY, b.knitting_company AS KNITTING_COMPANY, b.floor AS FLOOR, b.room AS ROOM, b.rack_no AS RACK_NO, b.shelf_no AS SHELF_NO, b.bin_box AS BIN_BOX, c.roll_no AS ROLL_NO, c.roll_id AS ROLL_ID, c.barcode_no AS BARCODE_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID FROM inv_issue_master a INNER JOIN inv_finish_fabric_issue_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE a.id = ".$update_id." AND c.entry_form=71 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 ORDER BY c.roll_no";
	//echo $sql;
	$rsltIssue = sql_select($sqlIssue);
	$poBreakdownIdArr = array();
	$barcodeNoArr = array();
	$productIdArr = array();

	foreach($rsltIssue as $row)
	{
		$poBreakdownIdArr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
		$barcodeNoArr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
		$productIdArr[$row['PROD_ID']] = $row['PROD_ID'];
		$issue_purpose = $row['ISSUE_PURPOSE'];
		$issue_date = $row['ISSUE_DATE'];
		$insert_date = $row['INSERT_DATE'];
		$store_id = $row['STORE_ID'];
		$req_no = $row['REQ_NO'];
		$remarks = $row['REMARKS'];
		$cutting_floor_id = $row['CUTTING_FLOOR'];
		$knit_dye_company = $row['KNIT_DYE_COMPANY'];
		$knit_dye_companyArr[$row['KNIT_DYE_COMPANY']] = $row['KNIT_DYE_COMPANY'];
		$knit_dye_sourceArr[$row['KNIT_DYE_SOURCE']] = $row['KNIT_DYE_SOURCE'];
	}

	//for order details
	$poNoArr=array();
	$sqlPo="SELECT a.buyer_name AS BUYER_NAME,a.job_no AS JOB_NO, b.id AS ID, b.po_number AS PO_NUMBER, b.grouping AS GROUPING, b.file_no AS FILE_NO FROM wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst AND a.status_active = 1 AND a.is_deleted = 0 ".where_con_using_array($poBreakdownIdArr, '0', 'b.id');
	$rsltPo=sql_select($sqlPo);
	$buyerIdArr = array();
	foreach($rsltPo as $row)
	{
		$buyerIdArr[$row['BUYER_NAME']] = $row['BUYER_NAME'];
		$poNoArr[$row['ID']]['po_number'] = $row['PO_NUMBER'];
		$poNoArr[$row['ID']]['buyer_name'] = $row['BUYER_NAME'];
		$poNoArr[$row['ID']]['internal_reference'] = $row['GROUPING'];
		$poNoArr[$row['ID']]['file_no'] = $row['FILE_NO'];
		$poNoArr[$row['ID']]['job_no'] = $row['JOB_NO'];
	}
	
	//for detarmination
	$product_array=array();
	$detarminationIdArr = array();
	$product_sql = sql_select("SELECT id AS ID, detarmination_id AS DETARMINATION_ID, gsm AS GSM, dia_width AS DIA_WIDTH, unit_of_measure AS UNIT_OF_MEASURE FROM product_details_master WHERE item_category_id=2 ".where_con_using_array($productIdArr, '0', 'id'));
	foreach($product_sql as $row)
	{
		$detarminationIdArr[$row['DETARMINATION_ID']]=$row['DETARMINATION_ID'];
		$product_array[$row['ID']]['deter_id']=$row['DETARMINATION_ID'];
		/*
		$product_array[$row['ID']]['gsm']=$row[csf("GSM")];
		$product_array[$row['ID']]['dia_width']=$row[csf("DIA_WIDTH")];
		$product_array[$row['ID']]['uom']=$row[csf("UNIT_OF_MEASURE")];
		*/
	}
	//echo "<pre>";
	//print_r($product_array);
	
	
	
	//for roll details
	$sqlRcv = "SELECT a.id AS ID, a.entry_form AS ENTRY_FORM, a.recv_number AS RECV_NUMBER, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.booking_no AS BOOKING_NO, a.booking_id AS BOOKING_ID, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, a.buyer_id AS BUYER_ID, b.id AS DTLS_ID, b.prod_id AS PROD_ID, b.body_part_id AS BODY_PART_ID, b.trans_id AS TRANS_ID, b.fabric_description_id AS FABRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.batch_id AS BATCH_ID, b.color_id AS COLOR_ID, c.barcode_no AS BARCODE_NO, b.dia_width_type AS DIA_WIDTH_TYPE, c.id AS ROLL_ID, c.roll_no AS ROLL_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.qnty AS QNTY FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id AND b.id=c.dtls_id AND b.trans_id<>0 AND a.entry_form IN(37,7,68) AND c.entry_form IN(37,7,68) AND c.status_active=1 AND c.is_deleted=0 ".where_con_using_array($barcodeNoArr, '0', 'c.barcode_no');
	//echo $sqlRcv;
	$data_array=sql_select($sqlRcv);
	$colorIdArr = array();
	$supplierIdArr = array();
	$batchIdArr = array();
	foreach($data_array as $row)
	{
		$colorIdArr[$row['COLOR_ID']] = $row['COLOR_ID'];
		$supplierIdArr[$row['KNITTING_COMPANY']] = $row['KNITTING_COMPANY'];
		$batchIdArr[$row['BATCH_ID']] = $row['BATCH_ID'];
		$detarminationIdArr[$row['FABRIC_DESCRIPTION_ID']]=$row['FABRIC_DESCRIPTION_ID'];
		$barcodeDeterRef[$row['BARCODE_NO']] = $row['FABRIC_DESCRIPTION_ID'];
	}

	//for composition
	$composition_arr=array();
	$sql_deter="SELECT a.id AS ID, a.construction AS CONSTRUCTION, b.copmposition_id AS COMPOSITION_ID, b.percent AS PERCENT FROM lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b WHERE a.id = b.mst_id ".where_con_using_array($detarminationIdArr, '0', 'a.id');
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		if(array_key_exists($row['ID'],$composition_arr))
		{
			$composition_arr[$row['ID']]=$composition_arr[$row['ID']]." ".$composition[$row['COMPOSITION_ID']]." ".$row['PERCENT']."%";
		}
		else
		{
			$composition_arr[$row['ID']]=$row['CONSTRUCTION'].", ".$composition[$row['COMPOSITION_ID']]." ".$row['PERCENT']."%";
		}
	}
	//echo "<pre>";
	//print_r($composition_arr);


	//echo "<pre>";
	//print_r($buyerIdArr);
	
	//for buyer details
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1 ".where_con_using_array($buyerIdArr,'0','id'),'id','buyer_name');
	//for color details
	$color_arr = return_library_array("select id, color_name from lib_color where 1=1 ".where_con_using_array($colorIdArr,'0','id'),'id','color_name');
	//for supplier details
	$supplier_arr = return_library_array("select id, short_name from lib_supplier where 1=1 ".where_con_using_array($supplierIdArr,'0','id'),"id","short_name");
	//for batch details
	$batch_arr = return_library_array( "select id, batch_no from  pro_batch_create_mst where 1=1 ".where_con_using_array($batchIdArr,'0','id'), "id", "batch_no");

	$cutting_floor_arr = return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=1 and company_id=$knit_dye_company","id", "floor_name");

	$service_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$service_arr_sup = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where  b.party_type in(9,21,24) and a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id","supplier_name");

	$roll_details_array=array();
	$barcode_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row['BARCODE_NO']]['body_part_id']=$row['BODY_PART_ID'];
		$roll_details_array[$row['BARCODE_NO']]['color_id']=$row['COLOR_ID'];
		$roll_details_array[$row['BARCODE_NO']]['roll_no']=$row['ROLL_NO'];
		$roll_details_array[$row['BARCODE_NO']]['qnty']=number_format($row['QNTY'],2,'.','');
		$roll_details_array[$row['BARCODE_NO']]['batch_id']=$row['BATCH_ID'];
		$roll_details_array[$row['BARCODE_NO']]['gsm']=$row['GSM'];
		$roll_details_array[$row['BARCODE_NO']]['width']=$row['WIDTH'];
	}

	//for company details
	$company_array=array();
	$sql_com="SELECT id AS ID, company_name AS COMPANY_NAME, company_short_name AS COMPANY_SHORT_NAME, plot_no AS PLOT_NO, level_no AS LEVEL_NO, road_no AS ROAD_NO, block_no AS BLOCK_NO, country_id AS COUNTRY_ID, province AS PROVINCE, city AS CITY, zip_code AS ZIP_CODE, email AS EMAIL, website AS WEBSITE FROM lib_company WHERE id=".$company."";
	
	$company_data=sql_select($sql_com);
	foreach($company_data as $row)
	{
		$company_array['name']=$row['COMPANY_NAME'];
		$company_array['shortname']=$row['COMPANY_SHORT_NAME'];
		$company_array['plot_no']=$row['PLOT_NO'];
		$company_array['level_no']=$row['LEVEL_NO'];
		$company_array['road_no']=$row['ROAD_NO'];
		$company_array['block_no']=$row['BLOCK_NO'];
		$company_array['city']=$row['CITY'];
		$company_array['zip_code']=$row['ZIP_CODE'];
		$company_array['province']=$row['PROVINCE'];
		$company_array['country_id']=$row['COUNTRY_ID'];
		$company_array['email']=$row['EMAIL'];
		$company_array['website']=$row['WEBSITE'];
	}
	
	//for company logo
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name,
	e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
	from lib_floor_room_rack_dtls b 
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0 
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0 
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0 
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0 
	where b.status_active=1 and b.is_deleted=0 and b.company_id=".$company." and b.store_id=$store_id
	order by a.floor_room_rack_name , c.floor_room_rack_name , d.floor_room_rack_name , e.floor_room_rack_name , f.floor_room_rack_name";
	//echo $lib_room_rack_shelf_sql;die;
	$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
	if(!empty($lib_rrsb_arr))
	{
		foreach ($lib_rrsb_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
				$lib_room_arr[$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
				$lib_rack_arr[$floor_id][$room_id][$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
				$lib_shelf_arr[$floor_id][$room_id][$rack_id][$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}


		}
	}
	
	//report data
	$rptDataArr = array();
	foreach($rsltIssue as $row)
	{
		//$composition = $product_array[$row['PROD_ID']]['deter_id'];
		$composition = $barcodeDeterRef[$row['BARCODE_NO']];
		$gsm = $roll_details_array[$row['BARCODE_NO']]['gsm'];
		$dia = $roll_details_array[$row['BARCODE_NO']]['width'];
		$batch_id = $roll_details_array[$row['BARCODE_NO']]['batch_id'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['buyer_id'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['buyer_name'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['internal_reference'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['internal_reference'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['file_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['file_no'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['job_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['job_no'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['body_part_id'] = $roll_details_array[$row['BARCODE_NO']]['body_part_id'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['color_id'] = $roll_details_array[$row['BARCODE_NO']]['color_id'];
		//$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['roll_no'] = $row['ROLL_NO'];
		$noOfRoll = $rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['roll_no']*1;
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['roll_no'] = $noOfRoll+1;
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['issue_qnty'] += $row['ISSUE_QNTY'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['floor'] = $row['FLOOR'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['room'] = $row['ROOM'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['rack_no'] = $row['RACK_NO'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['shelf_no'] = $row['SHELF_NO'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia]['bin_box'] = $row['BIN_BOX'];
	}
	
	//echo "<pre>";
	//print_r($rptDataArr);


	?>
    <div>
        <table width="1150" cellspacing="0">
            <tr>
            	<td width="200" rowspan="3">
            		<img src="../../../<? echo $image_location; ?>" height="70" width="200" />
            	</td>
                <td colspan="6" align="center" style="font-size:22px">
                	<strong><? echo $company_array['name']; ?></strong>
                </td>
                <td width="200"></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    Plot No: <? echo $company_array['plot_no']; ?> 
                    Level No: <? echo $company_array['level_no']?>
                    Road No: <? echo $company_array['road_no']; ?> 
                    Block No: <? echo $company_array['block_no'];?> 
                    City No: <? echo $company_array['city'];?> 
                    Zip Code: <? echo $company_array['zip_code']; ?><br> 
                    Province No: <?php echo $company_array['province'];?> 
                    Country: <? echo $country_arr[$company_array['country_id']]; ?> 
                    Email Address: <? echo $company_array['email'];?> 
                    Website No: <? echo $company_array['website'];?>
                </td>
                <td></td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
                <td></td>
            </tr>
            <tr>
            	<td colspan="8">&nbsp;</td>
            </tr>
			<tr>
			<td><strong> Service Source </strong></td>
                <td width="10">:</td>
                <td width="150px"><? echo $knitting_source[$knit_dye_sourceArr[$row['KNIT_DYE_SOURCE']]]; ?></td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100"><strong>Service Company</strong></td>
                <td width="10">:</td>
                <td><?echo $row['KNIT_DYE_SOURCE'] == 1 ? $service_arr[$knit_dye_company] :  $service_arr_sup[$knit_dye_company];?></td>

			</tr>
            <tr>
                <td><strong>Issue ID</strong></td>
                <td width="10">:</td>
                <td width="150px"><? echo $txt_issue_no; ?></td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100"><strong>Issue Purpose</strong></td>
                <td width="10">:</td>
                <td><? echo $yarn_issue_purpose[$issue_purpose]; ?></td>
            </tr>
            <tr>
                <td><strong>Issue Date</strong></td>
                <td>:</td>
                <td><? echo change_date_format($issue_date); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><strong>Reqsn No</strong></td>
                <td>:</td>
                <td><? echo $req_no; ?></td>
            </tr>
            <tr>
				<td><strong>Insert Date</strong></td>
                <td>:</td>
                <td><? echo date('d-m-Y h:i:s a', strtotime($insert_date));//change_date_format($insert_date); ?></td>
				<td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><strong>Cutting Unit</strong></td>
                <td>:</td>
                <td><? echo $cutting_floor_arr[$cutting_floor_id]; ?></td>
            </tr>
			<tr>
				<td><strong>Barcode</strong></td>
                <td>:</td>
                <td id="barcode_img_id"></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td><strong>Remarks</strong></td>
                <td>:</td>
                <td><? echo $remarks; ?></td>
			</tr>
        </table>
        <br>
        <table cellspacing="0" width="1550"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="120">Buyer</th>
                <th width="80">Order No</th>
                <th width="80">File No<br> Job</th>
                <th width="80">Ref. No</th>
                <th width="80">Batch No</th>
                <th width="100">Body Part</th>
                <th width="130">Construction/ Composition</th> 	
                <th width="60">GSM</th>
                <th width="60">Dia</th>
                <th width="120">Color</th>
				<th width="80">Floor</th>
				<th width="80">Room</th>
				<th width="80">Rack</th>
				<th width="80">Self</th>
				<th width="80">Bin</th>
                <th width="60">Total No Of Roll</th>
                <th>Issue Qty</th> 
            </thead>
            <?php
			$i=0;
			$total_roll=0; 
			$total_qty=0; 
			foreach($rptDataArr as $poId=>$poArr)
			{
				foreach($poArr as $batchId=>$batchArr)
				{
					foreach($batchArr as $compositionId=>$compositionArr)
					{
						foreach($compositionArr as $gsm=>$gsmArr)
						{
							foreach($gsmArr as $dia=>$row)
							{
								$i++;
								?>
								<tr valign="middle">
									<td align="center"><? echo $i; ?></td>
									<td><? echo $buyer_arr[$row['buyer_id']]; ?></td>
									<td align="center"><? echo $poNoArr[$poId]['po_number']; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $row['file_no'].'<br>'.$row['job_no']; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $row['internal_reference']; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $batch_arr[$batchId]; ?></td>
									<td style="word-break:break-all;"><? echo $body_part[$row['body_part_id']]; ?></td>
									<td style="word-break:break-all;"><? echo $composition_arr[$compositionId]; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $gsm; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $dia; ?></td>
									<td style="word-break:break-all;"><? echo $color_arr[$row['color_id']]; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $lib_floor_arr[$row["floor"]]; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $lib_room_arr[$row["floor"]][$row["room"]]; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $lib_rack_arr[$row["floor"]][$row["room"]][$row["rack_no"]]; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $lib_shelf_arr[$row["floor"]][$row["room"]][$row["rack_no"]][$row['shelf_no']]; ?></td>
									<td style="word-break:break-all;" align="center"><? echo $lib_bin_arr[$row["floor"]][$row["room"]][$row["rack_no"]][$row['shelf_no']][$row['bin_box']]; ?></td>
									<td style="word-break:break-all;" align="right"><? echo $row['roll_no']; ?></td>
									<td style="word-break:break-all;" align="right"><? echo number_format($row['issue_qnty'], 2);?></td>
								</tr>
								<?
								$total_roll+=$row['roll_no'];
								$total_qty+=$row['issue_qnty'];
							}
						}
					}
				}
			}
			?>
            <tr> 
                <td align="right" colspan="16"><strong>Total</strong></td>
                <td align="right"><? echo $total_roll; ?></td>
                <td align="right"><? echo number_format($total_qty,2); ?></td>
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

//actn_print_button_4
if($action=="actn_print_button_4")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_issue_no=$data[1];
	$update_id=$data[2];
	
	//for issue purpose
	$sqlIssue = "SELECT a.issue_purpose AS ISSUE_PURPOSE, a.issue_date AS ISSUE_DATE, a.knit_dye_source AS KNIT_DYE_SOURCE, a.knit_dye_company AS KNIT_DYE_COMPANY, a.req_no AS REQ_NO, a.batch_no AS BATCH_NO, a.FLOOR_ID AS CUTTING_FLOOR, b.batch_id AS BATCH_ID, b.issue_qnty AS ISSUE_QNTY, b.prod_id AS PROD_ID, b.issue_qnty AS ISSUE_QNTY, b.knitting_company AS KNITTING_COMPANY, c.roll_no AS ROLL_NO, c.roll_id AS ROLL_ID, c.barcode_no AS BARCODE_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID FROM inv_issue_master a INNER JOIN inv_finish_fabric_issue_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE a.id = ".$update_id." AND c.entry_form=71 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 ORDER BY c.roll_no";
	//echo $sql;
	$rsltIssue = sql_select($sqlIssue);
	$poBreakdownIdArr = array();
	$barcodeNoArr = array();
	$productIdArr = array();
	foreach($rsltIssue as $row)
	{
		$poBreakdownIdArr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
		$barcodeNoArr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
		$productIdArr[$row['PROD_ID']] = $row['PROD_ID'];
		$issue_purpose = $row['ISSUE_PURPOSE'];
		$issue_date = $row['ISSUE_DATE'];
		$cutting_floor_id = $row['CUTTING_FLOOR'];
		$knit_dye_company = $row['KNIT_DYE_COMPANY'];
		$knit_dye_companyArr[$row['KNIT_DYE_COMPANY']] = $row['KNIT_DYE_COMPANY'];
	}

	//for order details
	$poNoArr=array();
	$sqlPo="SELECT a.buyer_name AS BUYER_NAME, b.id AS ID, b.po_number AS PO_NUMBER, b.grouping AS GROUPING, b.file_no AS FILE_NO FROM wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst AND a.status_active = 1 AND a.is_deleted = 0 ".where_con_using_array($poBreakdownIdArr, '0', 'b.id');
	$rsltPo=sql_select($sqlPo);
	$buyerIdArr = array();
	foreach($rsltPo as $row)
	{
		$buyerIdArr[$row['BUYER_NAME']] = $row['BUYER_NAME'];
		$poNoArr[$row['ID']]['po_number'] = $row['PO_NUMBER'];
		$poNoArr[$row['ID']]['buyer_name'] = $row['BUYER_NAME'];
		$poNoArr[$row['ID']]['internal_reference'] = $row['GROUPING'];
		$poNoArr[$row['ID']]['file_no'] = $row['FILE_NO'];
	}
	
	//for detarmination
	$product_array=array();
	$detarminationIdArr = array();
	$product_sql = sql_select("SELECT id AS ID, detarmination_id AS DETARMINATION_ID, gsm AS GSM, dia_width AS DIA_WIDTH, unit_of_measure AS UNIT_OF_MEASURE FROM product_details_master WHERE item_category_id=2 ".where_con_using_array($productIdArr, '0', 'id'));
	foreach($product_sql as $row)
	{
		$detarminationIdArr[$row['DETARMINATION_ID']]=$row['DETARMINATION_ID'];
		$product_array[$row['ID']]['deter_id']=$row['DETARMINATION_ID'];
		/*
		$product_array[$row['ID']]['gsm']=$row[csf("GSM")];
		$product_array[$row['ID']]['dia_width']=$row[csf("DIA_WIDTH")];
		$product_array[$row['ID']]['uom']=$row[csf("UNIT_OF_MEASURE")];
		*/
	}
	//echo "<pre>";
	//print_r($product_array);
	
	
	/*
	//for actual
	$data_array = sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.finish_production_source, a.finish_production_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.qnty
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(66) and c.entry_form in(66) and c.status_active=1 and c.is_deleted=0 and c.barcode_no=$data");

	//for r
	$data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev,c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria,c.is_sales,c.reprocess,c.prev_reprocess  FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)");
	
	*/
	
	//for roll details
	$sqlRcv = "SELECT a.id AS ID, a.entry_form AS ENTRY_FORM, a.recv_number AS RECV_NUMBER, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.booking_no AS BOOKING_NO, a.booking_id AS BOOKING_ID, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, a.buyer_id AS BUYER_ID, b.id AS DTLS_ID, b.prod_id AS PROD_ID, b.body_part_id AS BODY_PART_ID, b.trans_id AS TRANS_ID, b.fabric_description_id AS FABRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.batch_id AS BATCH_ID, b.color_id AS COLOR_ID, c.barcode_no AS BARCODE_NO, b.dia_width_type AS DIA_WIDTH_TYPE, c.id AS ROLL_ID, c.roll_no AS ROLL_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.qnty AS QNTY 
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id AND b.id=c.dtls_id AND a.entry_form IN(37,7,68,66) AND c.entry_form IN(37,7,68,66) AND c.status_active=1 AND c.is_deleted=0 ".where_con_using_array($barcodeNoArr, '0', 'c.barcode_no');
	// AND b.trans_id<>0
	//echo $sqlRcv;
	$data_array=sql_select($sqlRcv);
	$colorIdArr = array();
	$supplierIdArr = array();
	$batchIdArr = array();
	foreach($data_array as $row)
	{
		$colorIdArr[$row['COLOR_ID']] = $row['COLOR_ID'];
		$supplierIdArr[$row['KNITTING_COMPANY']] = $row['KNITTING_COMPANY'];
		$batchIdArr[$row['BATCH_ID']] = $row['BATCH_ID'];
		$barcodeDeterRef[$row['BARCODE_NO']] = $row['FABRIC_DESCRIPTION_ID'];
		$detarminationIdArr[$row['FABRIC_DESCRIPTION_ID']]=$row['FABRIC_DESCRIPTION_ID'];
	}

	//for composition
	$composition_arr=array();
	$sql_deter="SELECT a.id AS ID, a.construction AS CONSTRUCTION, b.copmposition_id AS COMPOSITION_ID, b.percent AS PERCENT FROM lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b WHERE a.id = b.mst_id ".where_con_using_array($detarminationIdArr, '0', 'a.id');
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		if(array_key_exists($row['ID'],$composition_arr))
		{
			$composition_arr[$row['ID']]=$composition_arr[$row['ID']]." ".$composition[$row['COMPOSITION_ID']]." ".$row['PERCENT']."%";
		}
		else
		{
			$composition_arr[$row['ID']]=$row['CONSTRUCTION'].", ".$composition[$row['COMPOSITION_ID']]." ".$row['PERCENT']."%";
		}
	}
	//echo "<pre>";
	//print_r($composition_arr);
	//echo "<pre>";
	//print_r($buyerIdArr);
	
	//for buyer details
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer where 1=1 ".where_con_using_array($buyerIdArr,'0','id'),'id','buyer_name');
	//for color details
	$color_arr = return_library_array("select id, color_name from lib_color where 1=1 ".where_con_using_array($colorIdArr,'0','id'),'id','color_name');
	//for supplier details
	$supplier_arr = return_library_array("select id, short_name from lib_supplier where 1=1 ".where_con_using_array($supplierIdArr,'0','id'),"id","short_name");
	//for batch details
	$batch_arr = return_library_array( "select id, batch_no from  pro_batch_create_mst where 1=1 ".where_con_using_array($batchIdArr,'0','id'), "id", "batch_no");

	$cutting_floor_arr = return_library_array( "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=1 and company_id=$knit_dye_company","id", "floor_name");
	
	$roll_details_array=array();
	$barcode_array=array(); 
	foreach($data_array as $row)
	{
		if($row['ENTRY_FORM'] != 66 && $row['TRANS_ID'] != 0)
		{
		$roll_details_array[$row['BARCODE_NO']]['body_part_id']=$row['BODY_PART_ID'];
		$roll_details_array[$row['BARCODE_NO']]['color_id']=$row['COLOR_ID'];
		$roll_details_array[$row['BARCODE_NO']]['roll_no']=$row['ROLL_NO'];
		$roll_details_array[$row['BARCODE_NO']]['qnty']=number_format($row['QNTY'],2,'.','');
		$roll_details_array[$row['BARCODE_NO']]['batch_id']=$row['BATCH_ID'];
		$roll_details_array[$row['BARCODE_NO']]['gsm']=$row['GSM'];
		$roll_details_array[$row['BARCODE_NO']]['width']=$row['WIDTH'];
		}
		else
		{
			$roll_details_array[$row['BARCODE_NO']]['r_gsm']=$row['GSM'];
			$roll_details_array[$row['BARCODE_NO']]['r_dia']=$row['WIDTH'];
		}
	}

	//for company details
	$company_array=array();
	$company_data=sql_select("SELECT id AS ID, company_name AS COMPANY_NAME, company_short_name AS COMPANY_SHORT_NAME, plot_no AS PLOT_NO, level_no AS LEVEL_NO, road_no AS ROAD_NO, block_no AS BLOCK_NO, country_id AS COUNTRY_ID, province AS PROVINCE, city AS CITY, zip_code AS ZIP_CODE, email AS EMAIL, website AS WEBSITE FROM lib_company WHERE id=".$company."");
	foreach($company_data as $row)
	{
		$company_array['name']=$row['COMPANY_NAME'];
		$company_array['shortname']=$row['COMPANY_SHORT_NAME'];
		$company_array['plot_no']=$row['PLOT_NO'];
		$company_array['level_no']=$row['LEVEL_NO'];
		$company_array['road_no']=$row['ROAD_NO'];
		$company_array['block_no']=$row['BLOCK_NO'];
		$company_array['city']=$row['CITY'];
		$company_array['zip_code']=$row['ZIP_CODE'];
		$company_array['province']=$row['PROVINCE'];
		$company_array['country_id']=$row['COUNTRY_ID'];
		$company_array['email']=$row['EMAIL'];
		$company_array['website']=$row['WEBSITE'];
	}
	
	//for company logo
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	//report data
	$rptDataArr = array();
	foreach($rsltIssue as $row)
	{
		//$composition = $product_array[$row['PROD_ID']]['deter_id'];
		$composition = $barcodeDeterRef[$row['BARCODE_NO']];
		$gsm = $roll_details_array[$row['BARCODE_NO']]['gsm'];
		$dia = $roll_details_array[$row['BARCODE_NO']]['width'];
		$batch_id = $roll_details_array[$row['BARCODE_NO']]['batch_id'];
		
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['buyer_id'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['buyer_name'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['internal_reference'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['internal_reference'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['file_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['file_no'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['body_part_id'] = $roll_details_array[$row['BARCODE_NO']]['body_part_id'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['color_id'] = $roll_details_array[$row['BARCODE_NO']]['color_id'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['roll_no'] = $row['ROLL_NO'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['issue_qnty'] = $row['ISSUE_QNTY'];
		
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['r_gsm'] = $roll_details_array[$row['BARCODE_NO']]['r_gsm'];
		$rptDataArr[$row['PO_BREAKDOWN_ID']][$batch_id][$composition][$gsm][$dia][$row['BARCODE_NO']]['r_dia'] = $roll_details_array[$row['BARCODE_NO']]['r_dia'];
		
	}
	//echo "<pre>";
	//print_r($rptDataArr);
	?>
    <div>
        <table width="1300" cellspacing="0">
            <tr>
            	<td width="200" rowspan="3">
            		<img src="../../../<? echo $image_location; ?>" height="70" width="200" />
            	</td>
                <td colspan="6" align="center" style="font-size:22px">
                	<strong><? echo $company_array['name']; ?></strong>
                </td>
                <td width="200"></td>
            </tr>
            <tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
                    Plot No: <? echo $company_array['plot_no']; ?> 
                    Level No: <? echo $company_array['level_no']?>
                    Road No: <? echo $company_array['road_no']; ?> 
                    Block No: <? echo $company_array['block_no'];?> 
                    City No: <? echo $company_array['city'];?> 
                    Zip Code: <? echo $company_array['zip_code']; ?><br> 
                    Province No: <?php echo $company_array['province'];?> 
                    Country: <? echo $country_arr[$company_array['country_id']]; ?> 
                    Email Address: <? echo $company_array['email'];?> 
                    Website No: <? echo $company_array['website'];?>
                </td>
                <td></td>  
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
                <td></td>
            </tr>
            <tr>
            	<td colspan="8">&nbsp;</td>
            </tr>
            <tr>
                <td><strong>Issue ID</strong></td>
                <td width="10">:</td>
                <td width="150px"><? echo $txt_issue_no; ?></td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100"><strong>Issue Purpose</strong></td>
                <td width="10">:</td>
                <td><? echo $yarn_issue_purpose[$issue_purpose]; ?></td>
            </tr>
            <tr>
                <td><strong>Issue Date</strong></td>
                <td>:</td>
                <td><? echo change_date_format($issue_date); ?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><strong>Barcode</strong></td>
                <td>:</td>
                <td id="barcode_img_id"></td>
            </tr>
            <tr>
                <td><strong>Cutting Unit</strong></td>
                <td>:</td>
                <td><? echo $cutting_floor_arr[$cutting_floor_id]; ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="120">Buyer</th>
                <th width="80">Order No</th>
                <th width="80">File No</th>
                <th width="80">Ref. No</th>
                <th width="80">Batch No</th>
                <th width="100">Body Part</th>
                <th width="130">Construction/ Composition</th>
                <th width="120">Color</th>
                <th width="60">R GSM</th>
                <th width="60">Actual GSM</th>
                <th width="60">R Dia</th>
                <th width="60">Actual Dia</th>
                <th width="100">Barcode No</th>
                <th>Issue Qty</th> 
            </thead>
            <?php
			$i=0;
			$total_roll=0; 
			$total_qty=0; 
			foreach($rptDataArr as $poId=>$poArr)
			{
				foreach($poArr as $batchId=>$batchArr)
				{
					foreach($batchArr as $compositionId=>$compositionArr)
					{
						foreach($compositionArr as $gsm=>$gsmArr)
						{
							foreach($gsmArr as $dia=>$diaArr)
							{
								foreach($diaArr as $barcode=>$row)
								{
									$i++;
									?>
									<tr valign="middle">
										<td align="center"><? echo $i; ?></td>
										<td><? echo $buyer_arr[$row['buyer_id']]; ?></td>
										<td align="center"><? echo $poNoArr[$poId]['po_number']; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $row['file_no']; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $row['internal_reference']; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $batch_arr[$batchId]; ?></td>
										<td style="word-break:break-all;"><? echo $body_part[$row['body_part_id']]; ?></td>
										<td style="word-break:break-all;"><? echo $composition_arr[$compositionId]; ?></td>
										<td style="word-break:break-all;"><? echo $color_arr[$row['color_id']]; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $row['r_gsm']; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $gsm; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $row['r_dia']; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $dia; ?></td>
										<td style="word-break:break-all;" align="right"><? echo $barcode; ?></td>
										<td style="word-break:break-all;" align="right"><? echo number_format($row['issue_qnty'], 2);?></td>
									</tr>
									<?
									//$total_roll+=$row['roll_no'];
									$total_qty+=$row['issue_qnty'];
								}
							}
						}
					}
				}
			}
			?>
            <tr> 
                <td align="right" colspan="14"><strong>Total</strong></td>
                <td align="right"><? echo number_format($total_qty,2); ?></td>
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

//for norban
if ($action == "roll_issue_no_of_copy_print") // Print 5, created by Tipu
{
	extract($_REQUEST);
	echo load_html_head_contents("Roll Wise Finish Issue", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);
	//var_dump($data);

	$company   		= $data[0];
	$system_no 		= $data[1];
	$report_title 	= $data[2];
	$mst_id     	= $data[3];
	$knit_source    = $data[4];
	$no_copy 		= $data[5];
	$dyeing_company = $data[6];
	$service_source = $data[7];

	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	//$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	// $color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type=return_library_array("select id, body_part_type from lib_body_part where status_active=1",'id','body_part_type');
	$composition_arr = array();$yarn_composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}

		if (array_key_exists($row[csf('id')], $yarn_composition_arr)) {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		} else {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		}
	}
	


	$store_location_id=return_field_value("location_id","lib_store_location","id=$store_id and is_deleted=0","location_id");	
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');	
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$country_name_arr = return_library_array("select id, country_name from lib_country where is_deleted=0  and status_active=1 order by country_name", "id", "country_name");
	$cutting_floor_arr = return_library_array("select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and company_id='$company' and production_process=1 order by floor_name", "id", "floor_name");
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	
	$company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
	foreach($company_info as $row)
	{
		$company_library[$row['ID']] = $row['COMPANY_NAME'];
		$company_address_arr[$row['ID']] = 'Plot No:'.$row['PLOT_NO'].', Road No:'.$row['ROAD_NO'].', City / Town:'.$row['CITY'].', Country:'.$country_name_arr[$row['COUNTRY_ID']].', Contact No:'.$row['CONTACT_NO'];
	}
	unset($company_info);

	//for supplier
	$sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME, ADDRESS_1 from lib_supplier where id=$dyeing_company");
	foreach($sqlSupplier as $row)
	{
		$supplier_arr[$row['ID']] = $row['SHORT_NAME'];
		$supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
		$supplier_address_arr[$row['ID']] = $row['ADDRESS_1'];
	}
	unset($sqlSupplier);
	
	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company." AND a.basis = 4 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		// echo "<pre>"; print_r($exp);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];
				
				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				
				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}
				
				//for gate pass info
				$gatePassDataArr[$val]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$val]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$val]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$val]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$val]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$val]['est_return_date'] = $row['EST_RETURN_DATE'];
				
				$gatePassDataArr[$val]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$val]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$val]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$val]['delivery_bag'] += $row['NO_OF_BAGS'];
				
				$gatePassDataArr[$val]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$val]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$val]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$val]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$val]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$val]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$val]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$val]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$val]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$val]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}
	// echo "<pre>";print_r($gatePassDataArr);

	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	//for issue purpose
	$sqlIssue = "SELECT a.ISSUE_NUMBER, a.REMARKS, a.ATTENTION, a.STORE_ID, a.issue_purpose AS ISSUE_PURPOSE, a.issue_date AS ISSUE_DATE, a.knit_dye_source AS KNIT_DYE_SOURCE, a.knit_dye_company AS KNIT_DYE_COMPANY, a.req_no AS REQ_NO, a.batch_no AS BATCH_NO, a.FLOOR_ID AS CUTTING_FLOOR, b.batch_id AS BATCH_ID, b.issue_qnty AS ISSUE_QNTY, b.prod_id AS PROD_ID, b.knitting_company AS KNITTING_COMPANY, c.roll_no AS ROLL_NO, c.roll_id AS ROLL_ID, c.barcode_no AS BARCODE_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID
	FROM inv_issue_master a INNER JOIN inv_finish_fabric_issue_dtls b ON a.id = b.mst_id INNER JOIN pro_roll_details c ON b.id = c.dtls_id WHERE a.id = ".$mst_id." AND c.entry_form=71 AND b.status_active=1 AND b.is_deleted=0 AND c.status_active=1 AND c.is_deleted=0 ORDER BY c.roll_no";
	// echo $sqlIssue;die; //  and c.barcode_no=21020001547
	$rsltIssue = sql_select($sqlIssue);
	$poBreakdownIdArr = array();
	$barcodeNoArr = array();
	$productIdArr = array();
	foreach($rsltIssue as $row)
	{
		$poBreakdownIdArr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
		$barcodeNoArr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
		$productIdArr[$row['PROD_ID']] = $row['PROD_ID'];

		$issue_number = $row['ISSUE_NUMBER'];
		$issue_date = $row['ISSUE_DATE'];
		// $knit_dye_company = $row[csf('knit_dye_company')];
		$knit_dye_source = $row['KNIT_DYE_SOURCE'];
		$issue_purpose = $yarn_issue_purpose[$row['ISSUE_PURPOSE']];
		$attention = $row['ATTENTION'];
		$remarks = $row['REMARKS'];
		$req_no = $row['REQ_NO'];
		
		//for issue to
		$knit_dye_company = '';
		if ($row['KNIT_DYE_SOURCE'] == 1)
			$knit_dye_company = $company_library[$row['KNIT_DYE_COMPANY']];
		else
			$knit_dye_company = $supplier_dtls_arr[$row['KNIT_DYE_COMPANY']];

		$store_name=$store_arr[$row['STORE_ID']];
		$cutting_floor=$cutting_floor_arr[$row['CUTTING_FLOOR']];

		$barcode_nums .= $row["BARCODE_NO"].",";
	}
	$barcode_nums = chop($barcode_nums,",");

	//for order details
	$poNoArr=array();
	$sqlPo="SELECT a.buyer_name AS BUYER_NAME, b.id AS ID, b.po_number AS PO_NUMBER, b.grouping AS GROUPING, b.file_no AS FILE_NO, a.job_no as JOB_NO, a.style_ref_no as STYLE_REF_NO FROM wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst AND a.status_active = 1 AND a.is_deleted = 0 ".where_con_using_array($poBreakdownIdArr, '0', 'b.id');
	$rsltPo=sql_select($sqlPo);
	$buyerIdArr = array();
	foreach($rsltPo as $row)
	{
		$buyerIdArr[$row['BUYER_NAME']] = $row['BUYER_NAME'];
		$poNoArr[$row['ID']]['po_number'] = $row['PO_NUMBER'];
		$poNoArr[$row['ID']]['buyer_name'] = $row['BUYER_NAME'];
		$poNoArr[$row['ID']]['internal_reference'] = $row['GROUPING'];
		$poNoArr[$row['ID']]['file_no'] = $row['FILE_NO'];
		$poNoArr[$row['ID']]['job_no'] = $row['JOB_NO'];
		$poNoArr[$row['ID']]['job_no'] = $row['JOB_NO'];
		$poNoArr[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
	}

	//for detarmination
	$product_array=array();
	$detarminationIdArr = array();
	$product_sql = sql_select("SELECT id AS ID, detarmination_id AS DETARMINATION_ID, gsm AS GSM, dia_width AS DIA_WIDTH, unit_of_measure AS UNIT_OF_MEASURE FROM product_details_master WHERE item_category_id=2 ".where_con_using_array($productIdArr, '0', 'id'));
	foreach($product_sql as $row)
	{
		$detarminationIdArr[$row['DETARMINATION_ID']]=$row['DETARMINATION_ID'];
		$product_array[$row['ID']]['deter_id']=$row['DETARMINATION_ID'];
	}
	//echo "<pre>"; print_r($product_array);

	//for roll details
	$sqlRcv = "SELECT a.id AS ID, a.entry_form AS ENTRY_FORM, a.recv_number AS RECV_NUMBER, a.company_id AS COMPANY_ID, a.receive_basis AS RECEIVE_BASIS, a.booking_no AS BOOKING_NO, a.booking_id AS BOOKING_ID, a.knitting_source AS KNITTING_SOURCE, a.knitting_company AS KNITTING_COMPANY, a.buyer_id AS BUYER_ID, b.id AS DTLS_ID, b.prod_id AS PROD_ID, b.body_part_id AS BODY_PART_ID, b.trans_id AS TRANS_ID, b.fabric_description_id AS FABRIC_DESCRIPTION_ID, b.gsm AS GSM, b.width AS WIDTH, b.batch_id AS BATCH_ID, b.color_id AS COLOR_ID, c.barcode_no AS BARCODE_NO, b.dia_width_type AS DIA_WIDTH_TYPE, c.id AS ROLL_ID, c.roll_no AS ROLL_NO, c.po_breakdown_id AS PO_BREAKDOWN_ID, c.qnty AS QNTY, C.REJECT_QNTY 
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id AND b.id=c.dtls_id AND a.entry_form IN(66) AND c.entry_form IN(66) AND c.status_active=1 AND c.is_deleted=0 ".where_con_using_array($barcodeNoArr, '0', 'c.barcode_no');
	// AND b.trans_id<>0 // AND a.entry_form IN(37,7,68,66)
	//echo $sqlRcv;
	$data_array=sql_select($sqlRcv);
	$colorIdArr = array();
	$supplierIdArr = array();
	$batchIdArr = array();
	foreach($data_array as $row)
	{
		$colorIdArr[$row['COLOR_ID']] = $row['COLOR_ID'];
		$supplierIdArr[$row['KNITTING_COMPANY']] = $row['KNITTING_COMPANY'];
		$batchIdArr[$row['BATCH_ID']] = $row['BATCH_ID'];
		$barcodeDeterRef[$row['BARCODE_NO']] = $row['FABRIC_DESCRIPTION_ID'];
		$detarminationIdArr[$row['FABRIC_DESCRIPTION_ID']]=$row['FABRIC_DESCRIPTION_ID'];
	}

	//for composition
	$composition_arr=array();
	$sql_deter="SELECT a.id AS ID, a.construction AS CONSTRUCTION, b.copmposition_id AS COMPOSITION_ID, b.percent AS PERCENT FROM lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b WHERE a.id = b.mst_id ".where_con_using_array($detarminationIdArr, '0', 'a.id');
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		if(array_key_exists($row['ID'],$composition_arr))
		{
			$composition_arr[$row['ID']]=$composition_arr[$row['ID']]." ".$composition[$row['COMPOSITION_ID']]." ".$row['PERCENT']."%";
		}
		else
		{
			$composition_arr[$row['ID']]=$row['CONSTRUCTION'].", ".$composition[$row['COMPOSITION_ID']]." ".$row['PERCENT']."%";
		}
	}
	//echo "<pre>"; print_r($composition_arr);

	//for color details
	$color_arr = return_library_array("select id, color_name from lib_color where 1=1 ".where_con_using_array($colorIdArr,'0','id'),'id','color_name');

	//for batch details	
	$batch_arr=array();
	$batch_sql = sql_select("SELECT ID, BATCH_NO, BOOKING_NO, COLOR_RANGE_ID FROM pro_batch_create_mst WHERE 1=1 ".where_con_using_array($batchIdArr, '0', 'id'));
	foreach($batch_sql as $row)
	{
		$batch_arr[$row['ID']]['batch_no']=$row['BATCH_NO'];
		$batch_arr[$row['ID']]['booking_no']=$row['BOOKING_NO'];
		$batch_arr[$row['ID']]['color_range_id']=$row['COLOR_RANGE_ID'];
	}
	// echo "<pre>";print_r($batch_arr);die;
	
	$roll_details_array=array();
	$barcode_array=array(); 
	foreach($data_array as $row)
	{
		/*if($row['ENTRY_FORM'] != 66 && $row['TRANS_ID'] != 0)
		{
			$roll_details_array[$row['BARCODE_NO']]['body_part_id']=$row['BODY_PART_ID'];
			$roll_details_array[$row['BARCODE_NO']]['color_id']=$row['COLOR_ID'];
			$roll_details_array[$row['BARCODE_NO']]['roll_no']=$row['ROLL_NO'];
			$roll_details_array[$row['BARCODE_NO']]['qnty']=number_format($row['QNTY'],2,'.','');
			$roll_details_array[$row['BARCODE_NO']]['batch_id']=$row['BATCH_ID'];
			$roll_details_array[$row['BARCODE_NO']]['gsm']=$row['GSM'];
			$roll_details_array[$row['BARCODE_NO']]['width']=$row['WIDTH'];
			$roll_details_array[$row['BARCODE_NO']]['reject_qnty']=$row['REJECT_QNTY'];
		}
		else
		{
			$roll_details_array[$row['BARCODE_NO']]['r_gsm']=$row['GSM'];
			$roll_details_array[$row['BARCODE_NO']]['r_dia']=$row['WIDTH'];
		}*/
		$roll_details_array[$row['BARCODE_NO']]['body_part_id']=$row['BODY_PART_ID'];
		$roll_details_array[$row['BARCODE_NO']]['color_id']=$row['COLOR_ID'];
		$roll_details_array[$row['BARCODE_NO']]['roll_no']=$row['ROLL_NO'];
		$roll_details_array[$row['BARCODE_NO']]['qnty']=number_format($row['QNTY'],2,'.','');
		$roll_details_array[$row['BARCODE_NO']]['batch_id']=$row['BATCH_ID'];
		$roll_details_array[$row['BARCODE_NO']]['gsm']=$row['GSM'];
		$roll_details_array[$row['BARCODE_NO']]['width']=$row['WIDTH'];
		$roll_details_array[$row['BARCODE_NO']]['reject_qnty']=$row['REJECT_QNTY'];
	}

	// Kniting production
	$production_sql = "SELECT A.BUYER_ID,A.RECEIVE_BASIS, A.BOOKING_NO, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.LOCATION_ID, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.YARN_COUNT, B.YARN_LOT, B.COLOR_ID, B.COLOR_RANGE_ID, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_DIA, B.MACHINE_GG, C.BOOKING_NO AS BWO, C.BOOKING_WITHOUT_ORDER,C.IS_SALES, B.BODY_PART_ID, B.PROD_ID, D.DETARMINATION_ID, C.BARCODE_NO, sum(c.qc_pass_qnty_pcs) as ISSUE_QTY_PCS
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form=2 and c.entry_form=2 and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0 and c.barcode_no in ($barcode_nums)
	group by  A.BUYER_ID,A.RECEIVE_BASIS, A.BOOKING_NO, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.LOCATION_ID, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.YARN_COUNT, B.YARN_LOT, B.COLOR_ID, B.COLOR_RANGE_ID, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_DIA, B.MACHINE_GG, C.BOOKING_NO, C.BOOKING_WITHOUT_ORDER, C.IS_SALES, B.BODY_PART_ID, B.PROD_ID, D.DETARMINATION_ID, C.BARCODE_NO
	ORDER BY A.BOOKING_NO";
	// echo $production_sql;die;
	$production_data=sql_select($production_sql);
	$production_roll_array=array();
	foreach($production_data as $row)
	{
		$production_roll_array[$row['BARCODE_NO']]['stitch_length']=$row['STITCH_LENGTH'];
		$production_roll_array[$row['BARCODE_NO']]['yarn_count']=$row['YARN_COUNT'];
		$production_roll_array[$row['BARCODE_NO']]['yarn_lot']=$row['YARN_LOT'];
		$production_roll_array[$row['BARCODE_NO']]['brand_id']=$row['BRAND_ID'];
		$production_roll_array[$row['BARCODE_NO']]['machine_dia']=$row['MACHINE_DIA'];
		$production_roll_array[$row['BARCODE_NO']]['machine_gg']=$row['MACHINE_GG'];
		$production_roll_array[$row['BARCODE_NO']]['issue_qty_pcs']=$row['ISSUE_QTY_PCS'];
	}

	//report data
	$rptDataArr = array();
	$issue_qnty_array = array();
	foreach($rsltIssue as $row)
	{
		$composition = $barcodeDeterRef[$row['BARCODE_NO']];
		$gsm = $roll_details_array[$row['BARCODE_NO']]['gsm'];
		$dia = $roll_details_array[$row['BARCODE_NO']]['width'].'<br>';
		$batch_id = $roll_details_array[$row['BARCODE_NO']]['batch_id'];
		$body_part_id=$roll_details_array[$row['BARCODE_NO']]['body_part_id'];
		$job_no = $poNoArr[$row['PO_BREAKDOWN_ID']]['po_number'];
		$booking_no = $batch_arr[$batch_id]['booking_no'];
		$color_range_id = $batch_arr[$batch_id]['color_range_id'];
		// echo $booking_no.'<br>';

		$booking_no_arr=explode('-', $booking_no);
		// echo $booking_no_arr[1].'<br>';
		if ($booking_no_arr[1]=='SMN') 
		{
			$smn_booking_no_arr[$booking_no]=$booking_no;
		}
		else
		{
			$order_booking_no_arr[$booking_no]=$booking_no;
		}	

		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['buyer_id'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['buyer_name'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['job_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['job_no'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['style_ref_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['style_ref_no'];

		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['color_id'] = $roll_details_array[$row['BARCODE_NO']]['color_id'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['num_of_roll'] += count($row['BARCODE_NO']);
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['issue_qnty'] += $row['ISSUE_QNTY'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['reject_qnty'] += $roll_details_array[$row['BARCODE_NO']]['reject_qnty'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['issue_qty_pcs'] += $production_roll_array[$row['BARCODE_NO']]['issue_qty_pcs'];
		
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['r_gsm'] = $roll_details_array[$row['BARCODE_NO']]['r_gsm'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['r_dia'] = $roll_details_array[$row['BARCODE_NO']]['r_dia'];	
		
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['stitch_length'] = $production_roll_array[$row['BARCODE_NO']]['stitch_length'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['yarn_count'] = $production_roll_array[$row['BARCODE_NO']]['yarn_count'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['yarn_lot'] = $production_roll_array[$row['BARCODE_NO']]['yarn_lot'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['brand_id'] = $production_roll_array[$row['BARCODE_NO']]['brand_id'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['machine_dia'] = $production_roll_array[$row['BARCODE_NO']]['machine_dia'];
		$rptDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_range_id][$gsm][$dia]['machine_gg'] = $production_roll_array[$row['BARCODE_NO']]['machine_gg'];
	}
	// echo "<pre>"; print_r($order_booking_no_arr);die;
	$smn_booking_no = "'" . implode("','", $smn_booking_no_arr) . "'";
	$order_booking_no = "'" . implode("','", $order_booking_no_arr) . "'";

	$booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping as ref_no,d.style_ref_no,d.sustainability_standard,d.fab_material 
	from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d 
	where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 and a.booking_no in($order_booking_no)
	group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,d.style_ref_no,d.sustainability_standard,d.fab_material");
    foreach ($booking_details as $booking_row)
    {
		$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
		$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
		$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
		$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["style_ref_no"] = $booking_row[csf("style_ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["internal_ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["sustainability_standard"] = $booking_row[csf("sustainability_standard")];
		$booking_arr[$booking_row[csf("booking_no")]]["fab_material"] = $booking_row[csf("fab_material")];
    }

    // Non Order Booking
    $bookings_without_order=chop($bookings_without_order,',');
	$non_order_booking_sql= sql_select("SELECT a.booking_no,a.buyer_id,a.grouping, b.style_id 
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no in($smn_booking_no) group by  a.booking_no,a.buyer_id,a.grouping, b.style_id");
	foreach ($non_order_booking_sql as $row)
	{
	 	$style_id=$row[csf("style_id")];
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_id')];
		$nonOrderBookingData_arr[$row[csf('booking_no')]]['sustainability_std_id']=return_field_value("sustainability_std_id", "sample_development_mst", "id=$style_id");
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['fabric_material_id']=return_field_value("fabric_material_id", "sample_development_mst", "id=$style_id");
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['style_id']=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
	}
	// echo "<pre>";print_r($nonOrderBookingData_arr);die;
    // $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");

    $colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
	foreach($colarCupArr as $row)
	{
		$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
		$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];
	}

	// For Coller and Cuff data
	$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.barcode_no
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
	// echo $sql_coller_cuff;
	$sql_coller_cuff_result = sql_select($sql_coller_cuff);
	foreach ($sql_coller_cuff_result as $row2)
	{
		if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
		{
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll'] += count($row2[csf('barcode_no')]);
		}
	}
	//echo "<pre>"; print_r($coller_data_arr);//die;

	$com_dtls = fnc_company_location_address($company, $store_location_id, 2);
	?>
	<style type="text/css">
		table tr td {
			font-size: 16px;
		}
		.rpt_table thead th{
			font-size: 16px;
		}
		.rpt_table tfoot th{
			font-size: 16px;
		}
	</style>
    <?php
	$data_array = sql_select("select image_location  from common_photo_library where master_tble_id='".$data[0]."' and form_name='company_details' and is_deleted=0 and file_type=1");

	$noOfCopy = "";
	for ($x = 1; $x <= $no_copy; $x++)
	{
		if($x==1)
		{
			$sup = 'st';
		}
		else if($x==2)
		{
			$sup = 'nd';
		}
		else if($x==3)
		{
			$sup = 'rd';
		}
		else
		{
			$sup = 'th';
		}
		
		$noOfCopy ="<span style='font-size:x-large;font-weight:bold'>".$x."<sup>".$sup."</sup> Copy</span>";
		?>
    
		<div style="width:1240px;">
			<table width="1240" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row)
						{
							?>
							<img src='../../../<? echo $com_dtls[2]; ?>' height='50' width='50' align="middle"/>
							<?
						}
						?>
					</td>
                    <td align="center" style="font-size:30px" colspan="3"><strong><? echo $com_dtls[0]."<br><span style=\"font-size:14px;\">".$com_dtls[1]."</span>"; ?></strong></td>
					<td width="110" align="right"><?php echo $noOfCopy.($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong>Finish Fabric Delivery Challan</strong>
						<?php
						if ($data[4] == 1)
						{
							?>
							<!-- <span style="color:#0F0; font-weight:bold;">[Approved]</span> -->
							<?php
						}
						?>
					</td>
				</tr>
			</table>
			<div style="width:100%;">
				<div style="clear:both;">
		            <table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<tr>
							<td width="125"><strong>Company:</strong></td>
							<td width="250px"><? echo $company_library[$company]; ?></td>
							<td width="125"><strong>Attention:</strong></td>
							<td width="150px"><? echo $attention; ?></td>
							<td width="130"><strong>Delivery Challan No:</strong></td>
							<td width="130"><? echo $issue_number; ?></td>					
						</tr>
						<tr>
							<td><strong>Service Company:</strong></td>
							<td><? echo $knit_dye_company; ?></td>
							<td><strong>Issue Purpose:</strong></td>
							<td><? echo $issue_purpose; ?></td>
							<td><strong>Issue Date:</strong></td>
							<td><? echo change_date_format($issue_date); ?></td>                
						</tr>
						<tr>
							<td><strong>Store Name:</strong></td>
							<td><? echo $store_name; ?></td>
							<td><strong>Cutting Floor:</strong></td>
							<td><? echo $cutting_floor; ?></td>
							<td><strong>Requisition No:</strong></td>
							<td><? echo $req_no; ?></td>
						</tr>
						<tr>
							<td><strong>Remarks:</strong></td>
							<td colspan="5"><? echo $remarks; ?></td>
						</tr>
						<tr>
							<td align="center" colspan="6" id="barcode_img_id_<?php echo $x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
						</tr>
					</table>
			
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd">
							<tr>
								<th rowspan="2" width="20">SL</th>
								<th rowspan="2" width="120">Buyer, Job, <br>Style and<br>Booking</th>
								<th rowspan="2" width="120">Batch Number::Color</th>
								<th rowspan="2" width="60">Body Part</th>
								<th rowspan="2" width="210">Fabric Details</th>
								<th rowspan="2" width="65">Color Range</th>
								<th rowspan="2" width="180">Yarn Details</th>
								<th rowspan="2" width="60">Fab. Dia<br>& GSM</th>
								<th rowspan="2" width="50">MC DIA <br/> X <br/> M.GAUGE</th>
								<th rowspan="2" width="60">S.L</th>
								<th colspan="2" width="120">Delivery Qty</th>
								<th rowspan="2" width="80">Roll Qty</th>
								<th rowspan="2">Reject Qty</th>
							</tr>
							<tr>
								<th width="60">KG</th>
								<th width="60">PCS</th>
							</tr>
						</thead>
                        <tbody>
							<?
							$i=1;$k=0;	
							$grand_tot_qty_fabric=$grand_tot_issue_qty_pcs=$grand_tot_num_of_roll=$grand_tot_reject_qnty=0;	
							ksort($rptDataArr);					
							foreach($rptDataArr as $booking=>$bookingArr)
							{
								$job_tot_qty_fabric=$job_tot_issue_qty_pcs=$job_tot_num_of_roll=$job_tot_reject_qnty=0;
								foreach($bookingArr as $batchId=>$batchArr)
								{
									$batch_tot_qty_fabric=$batch_tot_issue_qty_pcs=$batch_tot_num_of_roll=$batch_tot_reject_qnty=0;
									foreach($batchArr as $compositionId=>$compositionArr)
									{
										$fab_tot_issue_qnty=$fab_tot_issue_qty_pcs=$fab_tot_num_of_roll=$fab_tot_reject_qnty=0;
										foreach($compositionArr as $body_part_ids=>$body_partArr)
										{
											foreach($body_partArr as $color_range_id=>$color_rangeArr)
											{
												foreach($color_rangeArr as $gsm=>$gsmArr)
												{
													foreach($gsmArr as $dia=>$row)
													{
														if ($i % 2 == 0)
															$bgcolor = "#E9F3FF";
														else
															$bgcolor = "#FFFFFF";
														$fab_material=array(1=>"Organic",2=>"BCI");
														$booking_no_arr=explode('-', $booking);
														$style=$buyer='';
														if ($booking_no_arr[1]=='SMN') 
														{
															$buyer=$nonOrderBookingData_arr[$booking]['buyer_id'];
															$style=$nonOrderBookingData_arr[$booking]['style_id'];
															$sustainability = $sustainability_standard[$nonOrderBookingData_arr[$booking]["sustainability_std_id"]];
															$material = $fab_material[$nonOrderBookingData_arr[$booking]["fabric_material_id"]];
														}
														else
														{
															$buyer=$row['buyer_id'];
															$style=$row['style_ref_no'];
															$sustainability = $sustainability_standard[$booking_arr[$booking]["sustainability_standard"]]; 
															$material = $fab_material[$booking_arr[$booking]["fab_material"]]; 
														}
														
														?>
							                            <tr bgcolor="<? echo $bgcolor; ?>">
							                                <td style="font-size: 15px"><? echo $i; ?></td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:130px"><? 
																if($knit_dye_source==3 && $service_source==0)
																{
																	$bayer_data = "WHS";
																	$style_data = "WHS";
																}
																else
																{
																	$bayer_data = $buyer_array[$buyer];
																	$style_data = $style;
																}
																// $bayer_data = $buyer_array[$buyer];
																// $style_data = $style;

							                                    echo $bayer_data.' ::<br>'.$row['job_no'].' ::<br>'.$style_data.' ::<br>'.$booking.' ::<br>'.$sustainability.' ::'.$material; 
																?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:130px"><? 
							                                    echo $batch_arr[$batchId]['batch_no'].' ::<br>'.$color_arr[$row['color_id']]; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:60px"><? echo $body_part[$body_part_ids]; ?></div>
							                                </td>
							                                <td style="font-size: 15px" title="<? echo $row['febric_description_id']; ?>">
							                                    <div style="word-wrap:break-word; width:210px">
							                                        <?
																	$color_id_arr = array_unique(explode(",", $row['color_id']));
																	$all_color_name = "";
																	foreach ($color_id_arr as $c_id) {
																		$all_color_name .= $color_arr[$c_id] . ",";
																	}
																	$all_color_name = chop($all_color_name, ",");
																	echo $all_color_name.' :: '.$composition_arr[$compositionId]; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:65px"><? echo $color_range[$color_range_id]; ?></div>
							                                </td>
							                                <td style="font-size: 15px" title="Yarn Dtls:<? echo $compositionId; ?>">
							                                    <div style="word-wrap:break-word; width:180">
							                                        <? 
							                                        $yarn_count = explode(",", $row['yarn_count']);
																	$ppl_count_id="";
																	foreach ($yarn_count as $count_id) {
																		if ($ppl_count_id == '') $ppl_count_id = $yarn_count_details[$count_id]; else $ppl_count_id .= "," . $yarn_count_details[$count_id];
																	}
							                                        echo $ppl_count_id.', '.$yarn_composition_arr[$compositionId].', '.$row['yarn_lot'].', '.$brand_details[$row['brand_id']]; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px; text-align: center;">
							                                    <div style="word-wrap:break-word; width:60px">
							                                        <? echo $dia.' & '.$gsm; ?>
							                                    </div>
							                                </td>
							                                <td style="font-size: 15px">
							                                    <div style="word-wrap:break-word; width:65px;text-align: center;"><? echo $row['machine_dia'].'X'.$row['machine_gg']; ?></div>
							                                </td>
							                                <td style="font-size: 15px; text-align: center;">
							                                    <div style="word-wrap:break-word; width:60px"><? echo $row['stitch_length']; ?></div>
							                                </td>
							                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? echo number_format($row['issue_qnty'], 2, '.', ''); ?></td>
							                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? 
							                                	if ($row['issue_qty_pcs']=="") 
							                                	{echo 0;} 
							                                	else{echo $row['issue_qty_pcs'];} ?>		
							                                </td>
							                                <td style="font-size: 15px" align="right"><? echo $row['num_of_roll']; ?></td>
							                                <td style="font-size: 15px" align="right">
							                                    <div style="word-wrap:break-word; width:60px"><? echo number_format($row['reject_qnty'], 2, '.', ''); ?></div>
							                                </td>
							                            </tr>
														<?
														$i++;
														$fab_tot_issue_qnty+=$row['issue_qnty'];
														$fab_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
														$fab_tot_num_of_roll+=$row['num_of_roll'];
														$fab_tot_reject_qnty+=$row['reject_qnty'];

														$batch_tot_qty_fabric+=$row['issue_qnty'];
														$batch_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
														$batch_tot_num_of_roll+=$row['num_of_roll'];
														$batch_tot_reject_qnty+=$row['reject_qnty'];

														$job_tot_qty_fabric+=$row['issue_qnty'];
														$job_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
														$job_tot_num_of_roll+=$row['num_of_roll'];
														$job_tot_reject_qnty+=$row['reject_qnty'];

														$grand_tot_qty_fabric+=$row['issue_qnty'];
														$grand_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
														$grand_tot_num_of_roll+=$row['num_of_roll'];
														$grand_tot_reject_qnty+=$row['reject_qnty'];
													}
												}
											}
										}
										?>
										<tr class="tbl_bottom">
											<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Fabric Type Total</strong></td>
											<td align="right" style="font-size: 14px;">
												<b><? echo number_format($fab_tot_issue_qnty, 2, '.', ''); ?></b>
											</td>
											<td align="right" style="font-size: 14px;"><? echo $fab_tot_issue_qty_pcs; ?></td>
											<td align="right" style="font-size: 14px;"><? echo number_format($fab_tot_num_of_roll, 2, '.', ''); ?></td>
											<td align="right" style="font-size: 14px;"><? echo number_format($fab_tot_reject_qnty, 2, '.', ''); ?></td>
										</tr>
										<?
									}
									?>
									<tr class="tbl_bottom">
										<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Batch Total</strong></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $batch_tot_qty_fabric; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $batch_tot_issue_qty_pcs; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($batch_tot_num_of_roll,2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($batch_tot_reject_qnty,2); ?></td>
									</tr>
									<?
								}
								$i=$k++;
								?>
								<tr class="tbl_bottom">
									<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Job Total</strong></td>
									<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $job_tot_qty_fabric; ?></td>
									<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $job_tot_issue_qty_pcs; ?></td>
									<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($job_tot_num_of_roll,2); ?></td>
									<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($job_tot_reject_qnty,2); ?></td>
								</tr>
								<?
							}
							?>
							<tr class="tbl_bottom">
								<td style="font-size: 16px;" colspan="2" align="center"><b>Total Job: 
	                            <?php echo " ".$i+1; ?></b></td>
								<td align="right" style="font-size: 16px;" colspan="8"><strong>Grand Total</strong></td>
								<td align="center" style="font-weight: bold; font-size: 16px;"><? echo number_format($grand_tot_qty_fabric, 2, '.', ''); ?></td>
								<td align="right" style="font-size: 16px;"><strong><? echo $grand_tot_issue_qty_pcs; ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grand_tot_num_of_roll, 2, '.', ''); ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grand_tot_reject_qnty, 2, '.', ''); ?></strong></td>
							</tr>
	                    </tbody>	
                    </table>
                    <br>
                    <!-- =========== Collar and Cuff Details Start ============= -->
                    <?
			    	//echo '<pre>';print_r($coller_cuff_data_arr);
					$CoCu=1;
					foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
					{
						if( count($booking_data_arr)>0)
						{
						    ?>
			                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px;">
			                	<thead bgcolor="#dddddd">
				                    <tr>
				                        <th colspan="3"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
				                    </tr>
				                    <tr>
				                        <th>Size</th>
				                        <th>Qty Pcs</th>
				                        <th>No. of Roll</th>
				                    </tr>
			                	</thead>
			                    <?
			                    $coller_cuff_qty_total=$coller_cuff_roll_total=0;
			                    foreach($booking_data_arr as $bookingId => $bookingData )
			                    {
			                        foreach($bookingData as $jobId => $jobData )
			                        {
			                            foreach($jobData as $size => $row )
			                            {
			                                ?>
			                                <tr>
			                                    <td align="center"><? echo $size;?></td>
			                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
			                                    <td align="center"><? echo $row['no_of_roll'];?></td>
			                                </tr>
			                                <?
			                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
			                                $coller_cuff_roll_total += $row['no_of_roll'];
			                            }
			                        }
			                    }
			                    ?>
			                    <tr>
			                        <td align="right"><b>Total</b></td>
			                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
			                        <td align="center"><b><? echo $coller_cuff_roll_total; ?></b></td>
			                    </tr>
			                </table>
						    <?
							if($CoCu==1){
								echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"3\">&nbsp;</td></tr></table>";
							}
							$CoCu++;
						}
					}
					?>
					<!-- =========== Collar and Cuff Details End ============= -->
					
                    <!-- ============= Gate Pass Info Start ========= -->
					<table style="margin-right:-40px;" cellspacing="0" width="1260" border="1" rules="all" class="rpt_table">
                        <tr>
                        	<td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: center;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
                        </tr>
                        <tr>
                        	<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                            <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
                        </tr>
                        <tr>
                        	<td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
                        	<td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

                        	<td colspan="2"><strong>To Company:</strong></td>
                        	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

                        	<td colspan="3"><strong>Carried By:</strong></td>
                        	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>From Location:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
                        	<td colspan="2"><strong>To Location:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
                        	<td colspan="3"><strong>Driver Name:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>Gate Pass ID:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
                        	<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                        	<td align="center"><strong>Kg</strong></td>
                        	<td align="center"><strong>Roll</td>
                        	<td align="center"><strong>PCS</td>
                        	<td colspan="3"><strong>Vehicle Number:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Gate Pass Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
                        	<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
                        	<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td>
                        	<td align="center"><?php 
                        	if ($gatePassDataArr[$system_no]['gate_pass_id'] !="") 
                        	{
                        		if ($grand_tot_issue_qty_pcs>0) {
                        		 	echo $grand_tot_issue_qty_pcs;
                        		 } 
                        	}
                        	?></td>
                        	<td colspan="3"><strong>Driver License No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Out Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
                        	<td colspan="2"><strong>Dept. Name:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
                        	<td colspan="3"><strong>Mobile No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Out Time:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
                        	<td colspan="2"><strong>Attention:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
                        	<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Returnable:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
                        	<td colspan="2"><strong>Purpose:</strong></td>
                        	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
                        </tr>						
                        <tr>
                        	<td colspan="2"><strong>Est. Return Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
                        	<td colspan="2"><strong>Remarks:</strong></td>
                        	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
                        </tr>
                    </table>
                    <!-- ============= Gate Pass Info End =========== -->
				</div>
				<br>
				<? echo signature_table(21, $company, "1200px"); ?>
			</div>
		</div>
		<script type="text/javascript" src="../../../js/jquery.js"></script>
        <script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess)
			{
				var zs = '<?php echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id_"+zs).html('11');
				value = {code: value, rect: false};
				$("#barcode_img_id_"+zs).show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');
			
			//for gate pass barcode
			function generateBarcodeGatePass(valuess)
			{
				var zs = '<?php echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_"+zs).html('11');
				value = {code: value, rect: false};
				$("#gate_pass_barcode_img_id_"+zs).show().barcode(value, btype, settings);
			}
			
			if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
			{
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
        <div style="page-break-after:always;"></div>
    	<?php
	}
    exit();
}

?>
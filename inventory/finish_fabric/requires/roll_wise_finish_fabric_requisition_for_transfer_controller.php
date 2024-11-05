<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_store_to")
{
	$data= explode("_", $data);
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.company_id=$data[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "store_on_change(this.value);" );
}
if($action=="load_drop_store_balnk")
{
	echo create_drop_down( "cbo_store_name", 152, $blank_array,"", 1, "--Select store--", 0, "" );
}
if($action=="load_drop_store_from")
{
	$data_ref= explode("_", $data);
	
	echo create_drop_down( "cbo_store_name_from", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=2 and a.company_id=$data_ref[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "store_on_change(this.value)" );
}
if($action=="bodypart_list")
{
	$bodyPart_arr=array();
	if($data)
	{
		$body_part_sql = sql_select("SELECT x.body_part_id from ( SELECT a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id=a.id and b.po_break_down_id =$data and b.booking_type =1 union all select b.body_part_id from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id=a.id and c.po_break_down_id =$data and a.fabric_description = b.id and c.booking_type=4 ) x group by x.body_part_id");


		foreach($body_part_sql as $row)
		{
			$bodyPart_arr[$row[csf('body_part_id')]]=$body_part[$row[csf('body_part_id')]];
		}
	}
	$jsBodyPart_arr= json_encode($bodyPart_arr);
	echo $jsBodyPart_arr;
	die();
}
/*if($action=="load_drop_store")
{
	// print_r($data);
	$data= explode("_", $data);
	if ($data[0]==1 && $data[2]>0)
	{	
		echo create_drop_down( "cbo_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[2] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	}
	else if (($data[0]==2 || $data[0]==4) && $data[2]==0)
	{
		echo create_drop_down( "cbo_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "" );
	}
}*/

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
    
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($trans_date < $max_recv_date) 
    {
        echo "20**Transfer Date Can not Be Less Than Last Receive Date Of These Lot";die;
	}
	
	/*
	|--------------------------------------------------------------------------
	| Insert
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		if($db_type==0) $year_cond="YEAR(insert_date)"; 
		else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
		else $year_cond="";//defined Later
		
		/*
		|--------------------------------------------------------------------------
		| inv_item_transfer_requ_mst
		| data preparing here
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$id = return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst", $con);
		$new_transfer_system_id = explode("*", return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst",$con,1,$cbo_to_company_id,'FFTRE',506,date("Y",time()),2 ));
		$data_array="(".$id.",506,'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_to_company_id.",0,0,".$cbo_item_category.",".$txt_remarks.",".$cbo_ready_to_approved.",".$cbo_store_name_from.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		$totalRollId="";
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
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
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$batchId="batchId_".$j;
				$colorId="colorId_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$rollNo="rollNo_".$j;
				$fromStoreId="fromStoreId_".$j;
				
				$febDescripId="febDescripId_".$j;
				$machineNoId="machineNoId_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$knitDetailsId="knitDetailsId_".$j;
				$transferEntryForm="transferEntryForm_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$rollMstId="rollMstId_".$j;
				$totalRollId.=$$rollMstId.",";
				$toOrderId="toOrderId_".$j;
				$tobookingNo="tobookingNo_".$j;
				$toBookingMstId="toBookingMstId_".$j;
				$constructCompo="constructCompo_".$j;
				$rollAmount="rollAmount_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
				$rejectQtyVal="rejectQtyVal_".$j;
				$bodyPartId="bodyPartId_".$j;
				$cboToBodyPart="cboToBodyPart_".$j;

				/*
				|--------------------------------------------------------------------------
				| inv_item_transfer_requ_dtls
				| data preparing here
				| $data_array_dtls
				|--------------------------------------------------------------------------
				|
				*/
				
				$dtls_id = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
				
				if($$toOrderId=="")
					$toOrderIdRef=$$orderId;
				else
					$toOrderIdRef=$$toOrderId;		

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",0,0,'".$$productId."',0,'".$$orderId."','".$toOrderIdRef."','".$$tobookingNo."','".$$toBookingMstId."','".$$febDescripId."','".$$machineNoId."','".$$batchId."','".str_replace(" ","0",$$rack)."','".$$shelf."','".$$fromStoreId."',".$cbo_store_name.",".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$barcodeNo."','".$$rollId."','".$$rollNo."','".$$rejectQtyVal."','".$$bodyPartId."','".$$cboToBodyPart."',506,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$$colorId.")";
				
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__0__0__0,";
			}
		}
		else
		{
			for($j=1;$j<=$tot_row;$j++)
			{
				
				$recvBasis="recvBasis_".$j;
				$barcodeNo="barcodeNo_".$j;
				$progBookPiId="progBookPiId_".$j;
				$productId="productId_".$j;
				$orderId="orderId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$batchId="batchId_".$j;
				$colorId="colorId_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$rollNo="rollNo_".$j;
				$fromStoreId="fromStoreId_".$j;
				$febDescripId="febDescripId_".$j;
				$machineNoId="machineNoId_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$knitDetailsId="knitDetailsId_".$j;
				$transferEntryForm="transferEntryForm_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$rollMstId="rollMstId_".$j;
				$totalRollId.=$$rollMstId.",";
				$toOrderId="toOrderId_".$j;
				$tobookingNo="tobookingNo_".$j;
				$toBookingMstId="toBookingMstId_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
				$rejectQtyVal="rejectQtyVal_".$j;
				$bodyPartId="bodyPartId_".$j;
				$cboToBodyPart="cboToBodyPart_".$j;

				/*
				|--------------------------------------------------------------------------
				| inv_item_transfer_requ_dtls
				| data preparing here
				| $data_array_dtls
				|--------------------------------------------------------------------------
				|
				*/
				if($$toOrderId=="")
					$toOrderIdRef=$$orderId;
				else
					$toOrderIdRef=$$toOrderId;
				
				$dtls_id = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",0,0,'".$$productId."','".$$productId."','".$$orderId."','".$toOrderIdRef."','".$$tobookingNo."','".$$toBookingMstId."','".$$febDescripId."','".$$machineNoId."','".$$batchId."','".str_replace(" ","0",$$rack)."','".$$shelf."','".$$fromStoreId."',".$cbo_store_name.",".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$barcodeNo."','".$$rollId."','".$$rollNo."','".$$rejectQtyVal."','".$$bodyPartId."','".$$cboToBodyPart."',506,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$$colorId.")";
				
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__0__0__0,";
			}
		}
		
		
		$rID=true;
		$rID2=true;
		/*
		|--------------------------------------------------------------------------
		| inv_item_transfer_requ_mst
		| data inserting here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="id, entry_form, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_order_id, to_order_id, item_category, remarks,ready_to_approve,from_store_id,to_store_id, inserted_by, insert_date";
		$rID=sql_insert("inv_item_transfer_requ_mst",$field_array,$data_array,0);
		//echo "10** insert into inv_item_transfer_requ_mst ($field_array) values $data_array";die;
		
		/*
		|--------------------------------------------------------------------------
		| inv_item_transfer_requ_dtls
		| data inserting here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id,to_booking_no,to_booking_id, feb_description_id, machine_no_id, batch_id, rack, shelf, from_store, to_store, item_category, transfer_qnty, knit_program_id, prod_detls_id, from_trans_entry_form, from_booking_without_order, gsm, dia_width, barcode_no, roll_id, roll,reject_qty,body_part_id,to_body_part, entry_form, inserted_by, insert_date,qty_in_pcs,color_id";
		$rID2=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
		//echo "10** insert into inv_item_transfer_requ_dtls ($field_array_dtls) values $data_array_dtls";die;
	  	//echo "10**".$rID."&&".$rID2;die;

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_transfer_system_id[0]."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_transfer_system_id[0]."**".substr($barcodeNos,0,-1);
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
	
	/*
	|--------------------------------------------------------------------------
	| Update
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="transfer_date*challan_no*remarks*ready_to_approve*from_store_id*to_store_id*updated_by*update_date";
		$data_array=$txt_transfer_date."*".$txt_challan_no."*".$txt_remarks."*".$cbo_ready_to_approved."*".$cbo_store_name_from."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id,to_booking_no,to_booking_id, feb_description_id, machine_no_id, batch_id, rack, shelf, from_store, to_store, item_category, transfer_qnty, knit_program_id, prod_detls_id, from_trans_entry_form, from_booking_without_order, gsm, dia_width, barcode_no, roll_id, roll,reject_qty,body_part_id,to_body_part, entry_form, inserted_by, insert_date,qty_in_pcs,color_id";
		
		$field_array_dtls_up="from_order_id*to_order_id*to_booking_no*to_booking_id*to_body_part*to_store*knit_program_id*prod_detls_id*from_trans_entry_form*gsm*dia_width*updated_by*update_date";
		$field_array_dtls_deleted="status_active*is_deleted*updated_by*update_date";
		
		$totalRollId=""; $update_to_prod_id = array(); $deleted_prod_id_arr = array(); $update_from_prod_id_arr = array();
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			for($j=1;$j<=$tot_row;$j++)
			{				
				$recvBasis="recvBasis_".$j;
				$barcodeNo="barcodeNo_".$j;
				$progBookPiId="progBookPiId_".$j;
				$productId="productId_".$j;
				$orderId="orderId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$batchId="batchId_".$j;
				$colorId="colorId_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$rollNo="rollNo_".$j;
				$fromStoreId="fromStoreId_".$j;
				$dtlsId="dtlsId_".$j;
				$transId="transId_".$j;
				$transIdTo="transIdTo_".$j;
				$rolltableId="rolltableId_".$j;
				$febDescripId="febDescripId_".$j;
				$machineNoId="machineNoId_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$knitDetailsId="knitDetailsId_".$j;
				$transferEntryForm="transferEntryForm_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$rollMstId="rollMstId_".$j;
				$totalRollId.=$$rollMstId.",";
				$toOrderId="toOrderId_".$j;
				$tobookingNo="tobookingNo_".$j;
				$toBookingMstId="toBookingMstId_".$j;
				$constructCompo="constructCompo_".$j;
				$rollAmount="rollAmount_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;	
				$rejectQtyVal="rejectQtyVal_".$j;	
				$bodyPartId="bodyPartId_".$j;	
				$cboToBodyPart="cboToBodyPart_".$j;	
			
				//if($$bookWithoutOrder==1) $toOrderId="orderId_".$j; else $toOrderId="toOrderId_".$j;
				if($$toOrderId=="")
					$toOrderIdRef=$$orderId;
				else
					$toOrderIdRef=$$toOrderId;
				
				if($$dtlsId>0)
				{
					/*
					|--------------------------------------------------------------------------
					| inv_item_transfer_requ_dtls
					| data preparing here
					| $data_array_dtls_up
					|--------------------------------------------------------------------------
					|
					*/
					$dtls_id_array_up[]=$$dtlsId;
					$data_array_dtls_up[$$dtlsId]=explode("*",($$orderId."*".$toOrderIdRef."*'".$$tobookingNo."'*'".$$toBookingMstId."'*'".$$cboToBodyPart."'*".$cbo_store_name."*'".$$progBookPiId."'*'".$$knitDetailsId."'*'".$$transferEntryForm."'*'".$$gsm."'*'".$$diaWidth."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else
				{
					/*
					|--------------------------------------------------------------------------
					| inv_item_transfer_requ_dtls
					| data preparing here
					| $data_array_dtls
					|--------------------------------------------------------------------------
					|
					*/
					$dtls_id = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
					if($data_array_dtls!="")
						$data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$update_id.",0,0,'".$$productId."',0,'".$$orderId."','".$toOrderIdRef."','".$$tobookingNo."','".$$toBookingMstId."','".$$febDescripId."','".$$machineNoId."','".$$batchId."','".str_replace(" ","0",$$rack)."','".$$shelf."','".$$fromStoreId."',".$cbo_store_name.",".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$barcodeNo."','".$$rollId."','".$$rollNo."','".$$rejectQtyVal."','".$$bodyPartId."','".$$cboToBodyPart."',506,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$$colorId.")";

					$barcodeNos.=$$barcodeNo."__".$dtls_id."__0__0__0,";
				}
			}	
		}
		else
		{
			for($j=1;$j<=$tot_row;$j++)
			{				
				$recvBasis="recvBasis_".$j;
				$barcodeNo="barcodeNo_".$j;
				$progBookPiId="progBookPiId_".$j;
				$productId="productId_".$j;
				$orderId="orderId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$batchId="batchId_".$j;
				$colorId="colorId_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$rollNo="rollNo_".$j;
				$fromStoreId="fromStoreId_".$j;
				$toOrderId="toOrderId_".$j;
				$tobookingNo="tobookingNo_".$j;
				$toBookingMstId="toBookingMstId_".$j;
				$dtlsId="dtlsId_".$j;
				$transId="transId_".$j;
				$transIdTo="transIdTo_".$j;
				$rolltableId="rolltableId_".$j;
				$febDescripId="febDescripId_".$j;
				$machineNoId="machineNoId_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$knitDetailsId="knitDetailsId_".$j;
				$transferEntryForm="transferEntryForm_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$rollMstId="rollMstId_".$j;
				$totalRollId.=$$rollMstId.",";
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
				$rejectQtyVal="rejectQtyVal_".$j;
				$bodyPartId="bodyPartId_".$j;	
				$cboToBodyPart="cboToBodyPart_".$j;	
				//if($$bookWithoutOrder==1) $toOrderId="orderId_".$j; else $toOrderId="toOrderId_".$j;
				if($$toOrderId=="")
					$toOrderIdRef=$$orderId;
				else
					$toOrderIdRef=$$toOrderId;
				
				if($$dtlsId>0)
				{
					/*
					|--------------------------------------------------------------------------
					| inv_item_transfer_requ_dtls
					| data preparing here
					| $data_array_dtls_up
					|--------------------------------------------------------------------------
					|
					*/
					$dtls_id_array_up[]=$$dtlsId;
					$data_array_dtls_up[$$dtlsId]=explode("*",($$orderId."*".$toOrderIdRef."*'".$$tobookingNo."'*'".$$toBookingMstId."'*'".$$cboToBodyPart."'*".$cbo_store_name."*'".$$progBookPiId."'*'".$$knitDetailsId."'*'".$$transferEntryForm."'*'".$$gsm."'*'".$$diaWidth."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else
				{
					/*
					|--------------------------------------------------------------------------
					| inv_item_transfer_requ_dtls
					| data preparing here
					| $data_array_dtls
					|--------------------------------------------------------------------------
					|
					*/
					$dtls_id = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
					
					if($data_array_dtls!="")
						$data_array_dtls.=",";
					
					$data_array_dtls.="(".$dtls_id.",".$update_id.",0,0,'".$$productId."','".$$productId."','".$$orderId."','".$toOrderIdRef."','".$$tobookingNo."','".$$toBookingMstId."','".$$febDescripId."','".$$machineNoId."','".$$batchId."','".str_replace(" ","0",$$rack)."','".$$shelf."','".$$fromStoreId."',".$cbo_store_name.",".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$barcodeNo."','".$$rollId."','".$$rollNo."','".$$rejectQtyVal."','".$$bodyPartId."','".$$cboToBodyPart."',506,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$$colorId.")";

					$barcodeNos.=$$barcodeNo."__".$dtls_id."__0__0__0,";
				}
			}
		}

		if ($txt_deleted_trnsf_dtls_id!="") 
		{
			$deleted_trnsf_dtls_id=explode(",", $txt_deleted_trnsf_dtls_id);
			$deleted_roll_id=explode(",", $txt_deleted_id);

			$txt_deleted_barcode = str_replace("'", "", $txt_deleted_barcode);
			$deleted_barcode_no_arr=explode(",", $txt_deleted_barcode);

			$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($txt_deleted_barcode)");

			foreach($splited_roll_sql as $bar)
			{ 
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($txt_deleted_barcode) and entry_form = 505 order by barcode_no");
			foreach($child_split_sql as $bar)
			{ 
				$child_split_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}


			for($inc=1;$inc <= count($deleted_trnsf_dtls_id);$inc++)
			{
				$trnsfDtlsId=trim($deleted_trnsf_dtls_id[$inc-1],"'");
				$rollDtlsId=trim($deleted_roll_id[$inc-1],"'");
				$BarcodeNO=trim($deleted_barcode_no_arr[$inc-1],"'");

				if($splited_roll_ref[$BarcodeNO][$rollDtlsId] !="" || $child_split_arr[$BarcodeNO][$rollDtlsId] != "")
				{
					echo "20**"."Split Found. barcode no: ".$BarcodeNO;
					disconnect($con);
					die;
				}

				$dtls_id_array_deleted[]=$trnsfDtlsId;
				$data_array_dtls_deleted[$trnsfDtlsId]=explode("*",("0"."*"."1"."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}

		//$all_dtls_id=chop($all_dtls_id,",");
		//$rollUpdate=bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array );
		//echo "10**$data_array_dtls";die;
		// echo "10**$rID && $rID2 && $dtls_data_upd && $deleted_dtls_data_upd"; die;

		$rID=true;
		$rID2=true;
		$dtls_data_upd=true;
		$deleted_dtls_data_upd=true;

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			/*
			|--------------------------------------------------------------------------
			| inv_item_transfer_requ_mst
			| data updating for
			|--------------------------------------------------------------------------
			|
			*/
			$rID=sql_update("inv_item_transfer_requ_mst",$field_array,$data_array,"id",$update_id,0);

			/*
			|--------------------------------------------------------------------------
			| inv_item_transfer_requ_dtls
			| data inserting for
			|--------------------------------------------------------------------------
			|
			*/
			if($data_array_dtls_up != "")
			{
				$dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_requ_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up ));
			}
			
			/*
			|--------------------------------------------------------------------------
			| inv_item_transfer_requ_dtls
			| data inserting for
			|--------------------------------------------------------------------------
			|
			*/
			if(trim($txt_deleted_trnsf_dtls_id,"'")!="")
			{
				$deleted_dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_requ_dtls", "id", $field_array_dtls_deleted, $data_array_dtls_deleted, $dtls_id_array_deleted ));

			}

			/*
			|--------------------------------------------------------------------------
			| inv_item_transfer_requ_dtls
			| data inserting for
			|--------------------------------------------------------------------------
			|
			*/
			if($data_array_dtls!="")
			{
				$rID2=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
			}
		}
		else
		{
			/*
			|--------------------------------------------------------------------------
			| inv_item_transfer_requ_mst
			| data updating for
			|--------------------------------------------------------------------------
			|
			*/
			$rID=sql_update("inv_item_transfer_requ_mst",$field_array,$data_array,"id",$update_id,0);
			
			/*
			|--------------------------------------------------------------------------
			| inv_item_transfer_requ_dtls
			| data updating for
			|--------------------------------------------------------------------------
			|
			*/
			$dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_requ_dtls", "id", $field_array_dtls_up, $data_array_dtls_up, $dtls_id_array_up ));
			//echo "10**".$dtls_data_upd; die;
			
			/*
			|--------------------------------------------------------------------------
			| inv_item_transfer_requ_dtls
			| data updating for
			|--------------------------------------------------------------------------
			|
			*/
			if(trim($txt_deleted_trnsf_dtls_id,"'")!="")
			{
				$deleted_dtls_data_upd=execute_query(bulk_update_sql_statement( "inv_item_transfer_requ_dtls", "id", $field_array_dtls_deleted, $data_array_dtls_deleted, $dtls_id_array_deleted ));
			}

			/*
			|--------------------------------------------------------------------------
			| inv_item_transfer_requ_dtls
			| data inserting for
			|--------------------------------------------------------------------------
			|
			*/
			if($data_array_dtls!="")
			{
				$rID2=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
			}
		}
		//$totalRollId=chop($totalRollId,',');
		//echo "10**$totalRollId"; die;
		//echo "10**$rID && $rID2 && $dtls_data_upd && $deleted_dtls_data_upd"; die;
		
		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if($rID && $rID2 && $dtls_data_upd && $deleted_dtls_data_upd)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_transfer_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $dtls_data_upd && $deleted_dtls_data_upd)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_transfer_no)."**".substr($barcodeNos,0,-1);
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

if($action=="populate_barcode_data")
{
	$data=explode("**",$data);
	$bar_code=$data[0];
	$sys_id=$data[1];

	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');

	//$issue_roll_mst_arr=return_library_array( "SELECT a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b  where a.mst_id=b.id and a.entry_form=61 and a.barcode_no in($bar_code)",'barcode_no','issue_number');


	$scanned_barcode_issue_data=sql_select("SELECT a.id, a.barcode_no,a.entry_form, b.issue_number 
	from pro_roll_details a, inv_issue_master b 
	where a.mst_id = b.id and b.entry_form = 71 and a.entry_form =71 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($bar_code)");

	foreach($scanned_barcode_issue_data as $row)
	{
		$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
		$issue_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('issue_number')];
	}

	/*$scanned_barcode_update_data=sql_select("SELECT a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.to_store, b.to_prod_id, b.from_prod_id 
	from pro_roll_details a, inv_item_transfer_dtls b  
	where a.dtls_id=b.id and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id");*/

	$scanned_barcode_update_data=sql_select("SELECT b.id as roll_upid, b.to_order_id, b.barcode_no, b.roll_id, a.id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.to_store, b.to_prod_id, b.from_prod_id 
    from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
    where a.id=b.mst_id and a.entry_form in(506) and a.status_active=1 and a.is_deleted=0 and b.mst_id=$sys_id");

	/*if($sys_id != "")
	{*/
		if($sys_id != ""){$sysIdCond="and b.mst_id=$sys_id";}
		if($bar_code != ""){$barcodeCond="and b.barcode_no=$bar_code";}
		$scanned_barcode_update_data=sql_select("SELECT b.barcode_no, b.roll_id, a.transfer_system_id, a.entry_form 
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
		where a.id=b.mst_id and a.entry_form=506 and b.entry_form=506 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $sysIdCond $barcodeCond");
		foreach($scanned_barcode_update_data as $row)
		{
			$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
			$transfer_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('transfer_system_id')];
		}
	//}
	// print_r($scanned_barcode_issue_array);die;

	$order_to_order_trans_sql=sql_select("SELECT a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order 
		from pro_roll_details a where a.entry_form in(68,126) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0 and a.barcode_no in($bar_code)");
	$order_to_order_trans_data=array();
	foreach($order_to_order_trans_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"]=$row[csf("id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"]=$row[csf("booking_without_order")];
	}
	unset($order_to_order_trans_sql);

	$trans_store_sql=sql_select("SELECT a.company_id, a.to_company, a.transfer_criteria, to_prod_id as prod_id, c.barcode_no, c.entry_form, b.to_store
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	where a.id = b.mst_id and a.entry_form=505 and b.id=c.dtls_id and c.entry_form in(505) 
	and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.barcode_no in($bar_code)
	order by c.barcode_no desc");

	/*$trans_store_sql=sql_select("SELECT a.company_id, a.to_company, a.transfer_criteria, to_prod_id as prod_id, b.barcode_no, b.entry_form, b.to_store
	from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
	where a.id=b.mst_id and a.entry_form=339
	and a.status_active=1 and a.is_deleted=0 and b.barcode_no in($bar_code)
	order by b.barcode_no desc");*/

	foreach($trans_store_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"]=$row[csf("to_store")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"]=$row[csf("prod_id")];

		if($row[csf("transfer_criteria")] == 1)
		{
			$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("to_company")];
		}
		else
		{
			$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("company_id")];
		}
	}

	unset($trans_store_sql);

	
	$data_sql="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, 
	max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.fabric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, 
	max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.rack_no) as rack, max(b.shelf_no) as self,max(b.batch_id) as batch_id, b.reject_qty,  c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id, c.qc_pass_qnty_pcs	
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(68) and c.entry_form in(68) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code) 
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, b.reject_qty, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id, c.qc_pass_qnty_pcs";

	//echo $data_sql;die;

	$data_array=sql_select($data_sql);

	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
			{
				$po_id=$row[csf("po_breakdown_id")];
			}
			else
			{
				$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
			}


			if($row[csf("booking_without_order")]==1)
			{
				$non_order_booking_buyer_po_arr[$po_id] = $po_id;

			}
			else
			{
				$po_arr_book_booking_arr[$po_id] = $po_id;
			}


			if($order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"] == 1)
			{
				$non_order_booking_buyer_po_arr[$po_id] = $po_id;
			}
			else
			{
				$po_arr_book_booking_arr[$po_id] = $po_id;
			}


			if($row[csf("booking_without_order")]==1)
			{
				$non_order_booking_buyer_po_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}
			else
			{
				$po_arr_book_booking_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}


			$color_id_ref_arr[$row[csf("color_id")]] = $row[csf("color_id")];

			$company_ids .= $row[csf("company_id")].",";
			$store_ids .= $row[csf("store_id")].",";
			$febric_description_ids .= $row[csf("febric_description_id")].",";
		}

		$company_ids = chop($company_ids,",");
		$store_ids = chop($store_ids,",");
		$febric_description_ids = chop($febric_description_ids,",");

		$production_basis_sql = sql_select("SELECT a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($bar_code)");
		foreach ($production_basis_sql as $val) 
		{
			$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

			if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
			{
				$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}
			
		}


		$company_name_array=return_library_array("SELECT id, company_name from  lib_company where status_active=1 and is_deleted=0 and id in($company_ids)",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
		$store_arr=return_library_array("SELECT id, store_name from lib_store_location where status_active=1 and is_deleted=0 and id in($store_ids)",'id','store_name');

		$composition_arr=array(); 
		$constructtion_arr=array();
		$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.id in ($febric_description_ids)";

		$deter_data_array=sql_select($sql_deter);
		foreach( $deter_data_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
		}
		unset($deter_data_array);
	}
	$po_id="";

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

	if(count($non_order_booking_buyer_po_arr)>0)
	{
		$non_order_sql = sql_select("select id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")");
		foreach ($non_order_sql as  $val) 
		{
			$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
			$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}
	}


	$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);

	if(count($po_arr_book_booking_arr) >0 )
	{
		// if(!empty($program_with_order_arr))
		// {
		// 	$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in =1 and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

		// 	foreach ($book_booking_sql as $val) 
		// 	{
		// 		$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
		// 	}
		// }
		// else
		// {
			$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where booking_type=1 and po_break_down_id in (".implode(",", $po_arr_book_booking_arr).") ",'po_break_down_id','booking_no');
		//}

		$sql_del_arr = "SELECT a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num, c.batch_no, c.id as batch_id
		from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id in (".implode(",", $po_arr_book_booking_arr).") group by a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num,c.id, c.batch_no order by a.id";

		//echo $sql_del_arr;die;

		$sql_del_data=sql_select($sql_del_arr);
		
		$roll_delivery_challan=array();
		foreach($sql_del_data as $row)
		{
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan']=$row[csf("sys_number")];	
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan_id']=$row[csf("id")];	
		}
		unset($sql_del_data);

		$po_ref_data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.grouping FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active =1 and b.status_active=1 and b.id in (".implode(",", $po_arr_book_booking_arr).") ");

		$po_details_array=array();
		foreach($po_ref_data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['grouping']=$row[csf("grouping")];
		}
		unset($po_ref_data_array);
	}



	$color_id_ref_arr = array_filter(array_unique($color_id_ref_arr));
	if(count($color_id_ref_arr)>0)
	{
		$all_color_ids = implode(",", $color_id_ref_arr);
		$all_color_id_cond=""; $colorCond=""; 
		if($db_type==2 && count($color_id_ref_arr)>999)
		{
			$color_id_ref_chunk=array_chunk($color_id_ref_arr,999) ;
			foreach($color_id_ref_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$colorCond.=" id in($chunk_arr_value) or ";	
			}
			
			$all_color_id_cond.=" and (".chop($colorCond,'or ').")";	
		}
		else
		{
			$all_color_id_cond=" and id in($all_color_ids)";	 
		}

		$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active =1 and is_deleted=0 $all_color_id_cond","id","color_name");
	}


	
	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		
		foreach($data_array as $row)
		{
			// echo $scanned_barcode_issue_array[$row[csf('barcode_no')]].'Test';
			if($scanned_barcode_issue_array[$row[csf('barcode_no')]]=="")
			{
				if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==37 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
				{
					$receive_basis="Independent";
					$receive_basis_id=0;

				}
				else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==37 && $row[csf("receive_basis")]==2)) 
				{
					$receive_basis="Booking";
					$receive_basis_id=2;
				}
				else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
				{
					$receive_basis="Knitting Plan";
					$receive_basis_id=3;
				}
				else if($row[csf("entry_form")]==37 && $row[csf("receive_basis")]==1) 
				{
					$receive_basis="PI";
					$receive_basis_id=1;
				}
				else if($row[csf("entry_form")]==68) 
				{
					$receive_basis="Delivery";
					$receive_basis_id=9;
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
				
				if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
				{
					$po_id=$row[csf("po_breakdown_id")];
					$roll_mst_id=$row[csf("roll_mst_id")];
					$entry_form=$row[csf("entry_form")];
					$booking_without_order="";
				}
				else
				{
					$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
					$roll_mst_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"];
					$entry_form=$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"];
					$booking_without_order=$order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"];

				}
				// echo $entry_form.'Entry Form';die;
				if($entry_form == 505)
				{
					$to_store = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"];
				}
				else
				{
					$to_store = $row[csf("store_id")];
				}
				
				if($row[csf("booking_without_order")]==1)
				{
					//$buyer_name=$buyer_arr[$book_buyer_arr[$po_id]];
					//$booking_no_fab="";
					
					$booking_no_fab=$non_booking_arr[$row[csf("po_breakdown_id")]];
				}
				else
				{
					//$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
					//$booking_no_fab=$book_booking_arr[$po_id];

					// if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
					// {
					// 	$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
					// 	$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("po_breakdown_id")]][$plan_id];
					// }
					// else
					// {
					// 	$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
					// }

					$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
					$roll_delivery_challan_no = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan'];
					$roll_delivery_challan_id = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan_id'];
				}
				
				if($entry_form == 134 || $entry_form == 505)
				{
					$booking_no_fab = $booking_no_fab ." (T)";
					if($booking_without_order == 1)
					{
						$buyer_name=$buyer_arr[$book_buyer_arr[$po_id]];
					}
					else
					{
						$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
					}
				}
				else
				{
					if($row[csf("booking_without_order")]==1)
					{
						$buyer_name=$buyer_arr[$book_buyer_arr[$row[csf("po_breakdown_id")]]];
					}
					else
					{
						$buyer_name=$buyer_arr[$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']];
					}
				}
				
				
				$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];//entry_form
				$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]].",".$composition_arr[$row[csf("febric_description_id")]];
				if($order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"])
				{
					$barcode_company_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"];
					$to_prod_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"];
				}
				else
				{
					$barcode_company_id = $row[csf("company_id")];
					$to_prod_id = $row[csf("prod_id")];
				}

				$barcodeData=$row[csf('id')]."**".$barcode_company_id."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$roll_delivery_challan_no."**".$roll_delivery_challan_id."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$to_store."**".$row[csf("knitting_company")]."**".$row[csf("batch_id")]."**".$batch_arr[$row[csf("batch_id")]]."**".$row[csf("reject_qty")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$to_prod_id."**".$row[csf("FEBRIC_DESCRIPTION_ID")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$po_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$po_id]['job_no']."**".$po_details_array[$po_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$po_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$to_store]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$booking_without_order."**".($row[csf("qc_pass_qnty_pcs")]*1)."**".$po_details_array[$po_id]['grouping'];//$row[csf("roll_mst_id")];				
			}
			else
			{
				if($scanned_barcode_entry_form_array[$row[csf('barcode_no')]]==506)
				{
					$barcodeData="-1**".$transfer_roll_mst_arr[$row[csf('barcode_no')]];
				}
				else
				{
					$barcodeData="-1**".$issue_roll_mst_arr[$row[csf('barcode_no')]];
				}
				
			}
		}
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
	$data=explode("**",$data);
	$bar_code=$data[0];
	$sys_id=$data[1];
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);

	$scanned_barcode_update_data=sql_select("SELECT a.company_id, b.id as roll_upid, b.to_order_id, b.barcode_no, b.id as dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, b.from_booking_without_order, b.roll_id,b.reject_qty,b.body_part_id,b.to_body_part,b.to_booking_no,b.to_booking_id  
	from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
	where a.id=b.mst_id and b.entry_form=506 and a.entry_form=506 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id=$sys_id");

	foreach($scanned_barcode_update_data as $row)
	{
		$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('to_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form']=$row[csf('from_trans_entry_form')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_store']=$row[csf('to_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_store']=$row[csf('from_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']=$row[csf('to_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']=$row[csf('from_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_order_id']=$row[csf('from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];
		$barcode_update_data[$row[csf('barcode_no')]]['reject_qty']=$row[csf('reject_qty')];
		$barcode_update_data[$row[csf('barcode_no')]]['body_part_id']=$row[csf('body_part_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_body_part']=$row[csf('to_body_part')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_booking_no']=$row[csf('to_booking_no')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_booking_id']=$row[csf('to_booking_id')];

		if($row[csf('from_booking_without_order')] == 1)
		{
			$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		else
		{
			$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
	}

	$data_sql="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, 
	max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.fabric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, 
	max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.rack_no) as rack, max(b.shelf_no) as self,max(b.batch_id) as batch_id, b.reject_qty,  c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id, c.qc_pass_qnty_pcs	
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(68,126) and c.entry_form in(68,126) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code) 
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, b.reject_qty, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id, c.qc_pass_qnty_pcs";
	
	// $data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id as roll_mst_id, c.qc_pass_qnty_pcs
	// FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	// WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	// group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id, c.qc_pass_qnty_pcs");

	$data_array=sql_select($data_sql);
	
	if(count($data_array)>0)
	{		
		foreach($data_array as $val)
		{
			$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			if($val[csf("booking_without_order")] == 1 )
			{
				$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}else{
				$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}

			/*if($val[csf("receive_basis")] == 2){
				$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}*/
		}

		$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
		$nxProcessedBarcode = array();
		if($splited_barcode)
		{
			$nxtProcessSql = sql_select("SELECT a.id,a.barcode_no,a.roll_no from  pro_roll_details a where a.barcode_no in (".$splited_barcode.") and a.entry_form in (71) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1");
			foreach ($nxtProcessSql as $val2) 
			{
				$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
			}
			//print_r($nxProcessedBarcode);
			

			$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($splited_barcode)");

			foreach($splited_roll_sql as $bar)
			{ 
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($splited_barcode) and entry_form = 505 order by barcode_no");
			foreach($child_split_sql as $bar)
			{ 
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}

			//print_r($splited_roll_ref);die;

		}

		$production_basis_sql = sql_select("SELECT a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id 
			from pro_roll_details a, inv_receive_master b 
			where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($bar_code)");
		foreach ($production_basis_sql as $val) 
		{
			$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

			if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
			{
				$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}
			
		}
	}
	
	$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id, b.grouping FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($po_data_sql as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		$po_details_array[$row[csf("po_id")]]['grouping']=$row[csf("grouping")];
	}

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

	if(count($non_order_booking_buyer_po_arr)>0)
	{
		$non_order_sql = sql_select("SELECT id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")");
		foreach ($non_order_sql as  $val) 
		{
			$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
			$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}
	}

	$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
	if(count($po_arr_book_booking_arr)>0)
	{
		// if(!empty($program_with_order_arr))
		// {
		// 	$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

		// 	foreach ($book_booking_sql as $val) 
		// 	{
		// 		$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
		// 	}
		// }
		// else
		// {
			$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where booking_type=1 and po_break_down_id in (". implode(',', $po_arr_book_booking_arr) .")",'po_break_down_id','booking_no');
		//}

		$sql_del_arr = "SELECT a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num, c.batch_no, c.id as batch_id
		from pro_grey_prod_delivery_mst a,  pro_grey_prod_delivery_dtls b, pro_batch_create_mst c
		where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=67 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.order_id in (".implode(",", $po_arr_book_booking_arr).") group by a.id, a.sys_number_prefix_num, a.sys_number,b.barcode_num,c.id, c.batch_no order by a.id";

		//echo $sql_del_arr;die;

		$sql_del_data=sql_select($sql_del_arr);
		
		$roll_delivery_challan=array();
		foreach($sql_del_data as $row)
		{
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan']=$row[csf("sys_number")];	
			$roll_delivery_challan[$row[csf("barcode_num")]][$row[csf("batch_id")]]['roll_delivery_challan_id']=$row[csf("id")];	
		}
		unset($sql_del_data);

	}

	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==37 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
			{
				$receive_basis="Independent";
				$receive_basis_id=0;
			}
			else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==37 && $row[csf("receive_basis")]==2)) 
			{
				$receive_basis="Booking";
				$receive_basis_id=2;
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
			{
				$receive_basis="Knitting Plan";
				$receive_basis_id=3;
			}
			else if($row[csf("entry_form")]==37 && $row[csf("receive_basis")]==1) 
			{
				$receive_basis="PI";
				$receive_basis_id=1;
			}
			else if($row[csf("entry_form")]==68) 
			{
				$receive_basis="Delivery";
				$receive_basis_id=9;
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
			
			if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
			{
				$po_id=$row[csf("po_breakdown_id")];
				$roll_mst_id= $row[csf("roll_mst_id")];
			}
			else
			{
				$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
				$roll_mst_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"];
			}
			
			//echo $po_id;die;
			
			if($row[csf("booking_without_order")]==1)
			{
				//$buyer_name=$buyer_arr[$book_buyer_arr[$po_id]];
				//$booking_no_fab="";
				$booking_no_fab=$non_booking_arr[$row[csf("po_breakdown_id")]];
			}
			else
			{
				//$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
				//$booking_no_fab=$book_booking_arr[$po_id];
				// if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
				// {
				// 	$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
				// 	$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("po_breakdown_id")]][$plan_id];
				// }
				// else
				// {
				// 	$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
				// }

				$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
				$roll_delivery_challan_no = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan'];
				$roll_delivery_challan_id = $roll_delivery_challan[$row[csf("barcode_no")]][$row[csf("batch_id")]]['roll_delivery_challan_id'];
			}

			$from_order_id =  $barcode_update_data[$row[csf('barcode_no')]]['from_order_id'];
			$from_booking_without_order = $barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order'];

			if($from_booking_without_order == 1)
			{
				$buyer_name=$buyer_arr[$book_buyer_arr[$from_order_id]];
			}
			else
			{
				$buyer_name=$buyer_arr[$po_details_array[$from_order_id]['buyer_name']];
			}

			if($barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 134 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 505)
			{
				$booking_no_fab = $booking_no_fab . " (T)";
			}
			
			$store_id = $barcode_update_data[$row[csf('barcode_no')]]['from_store'];
			$entry_form = $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];
			
			$barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$roll_delivery_challan_no."**".$roll_delivery_challan_id."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$store_id."**".$row[csf("knitting_company")]."**".$row[csf("batch_id")]."**".$batch_arr[$row[csf("batch_id")]]."**".$barcode_update_data[$row[csf('barcode_no')]]['reject_qty']."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$from_order_id]['job_no']."**".$po_details_array[$from_order_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$from_order_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$store_id]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number']."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."**".($row[csf("qc_pass_qnty_pcs")]*1)."**".$po_details_array[$from_order_id]['grouping']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no']."**".$barcode_update_data[$row[csf('barcode_no')]]['body_part_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_body_part']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_booking_no']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_booking_id']."__";
		
		}
		//echo $test_str;die;
		echo chop($barcodeData,"__");
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
	                    <th>Requisition Date Range</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="100">Please Enter Requisition No</th>
	                    <th id="booking_td_up" width="120">Booking No</th>
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
	                       		$search_by_arr=array(1=>"Requisition No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
	                    </td>     
	                    <td align="center" id="search_by_td">				
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
	                    </td> 
	                     <td align="center" id="booking_td">				
	                        <input type="text" style="width:90px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />	
	                    </td> 						
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_to_company_id; ?>, 'create_challan_search_list_view', 'search_div', 'roll_wise_finish_fabric_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$bookingNo =$data[5];
	$transfer_criteria =$data[6];
	$cbo_to_company_id =$data[7];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.transfer_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.transfer_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and a.transfer_system_id like '$search_string'";
	}

	$criteria_cond="";
	if($transfer_criteria!=0)
	{
		$criteria_cond=" and a.transfer_criteria='$transfer_criteria'";
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
	
	

	if($bookingNo !=""){
		//"query from plan to get program_no";
		$program_arr=array();$programIds="";
		$finish_plan=sql_select( "SELECT a.booking_no,b.id as progran_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no like '%$bookingNo%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach ($finish_plan as $row) 
		{
			$program_arr[$row[csf('progran_no')]]["progran_no"]=$row[csf('progran_no')];
			$programIds.="'".$row[csf('progran_no')]."'".",";
		}
		$programIds=chop($programIds,",");
	}
	
	if(!empty($program_arr)) $cond_for_booking=" and b.knit_program_id in($programIds)";

	$sql = "SELECT a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store 
	from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
	where a.id=b.mst_id and a.entry_form=506 and a.status_active=1 and a.is_deleted=0 and a.to_company=$company_id $criteria_cond $search_field_cond $date_cond $cond_for_booking 
	group by a.id, a.insert_date, a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="40">Trans. No</th>
            <th width="40">Year</th>
            <th width="125">Transfer Criteria</th>
            <th width="120">From Company</th>
            <th width="120">To Company</th>
            <th width="120">To Store</th>
            <th width="70">Challan</th>
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
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="40" align="center"><p>&nbsp;<? echo $row[csf('transfer_prefix_number')]; ?></p></td>
                    <td width="40" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="125"><p><? echo $item_transfer_criteria[$row[csf('transfer_criteria')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $company_arr[$row[csf('to_company')]]; ?>&nbsp;</p></td>
                    <td width="120"><p><? echo $store_arr[$row[csf('to_store')]]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
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
	$sql = "SELECT a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no,max(b.from_store) as from_store, max(b.to_store) as to_store, a.remarks, a.ready_to_approve, a.is_approved  
	from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
	where a.id=b.mst_id and a.entry_form=506 and a.id=$data 
	group by a.id, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, a.remarks, a.ready_to_approve, a.is_approved "; 
	// echo $sql;
	$res = sql_select($sql);	
	foreach($res as $row)
	{
		if ($row[csf("transfer_criteria")]==2 || $row[csf("transfer_criteria")]==4)
		{
			$to_company_id=$row[csf("company_id")];
			echo "$('#cbo_to_company_id').val(".$row[csf("company_id")].");\n";
		}
		else{ // company to company
			$to_company_id=$row[csf("to_company")];
			echo "$('#cbo_to_company_id').val(".$row[csf("to_company")].");\n";
		}

		echo "load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_store_from', 'from_store_td' );\n";
		echo "load_drop_down( 'requires/roll_wise_finish_fabric_requisition_for_transfer_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store_to', 'store_td' );\n";
		echo "$('#cbo_store_name').val(".$row[csf("to_store")].");\n";
		echo "$('#cbo_store_name_from').val(".$row[csf("from_store")].");\n";
		echo "$('#cbo_store_name_from').attr('disabled','true')".";\n";
		if($row[csf("transfer_criteria")] != 4)
		{
			echo "$('#cbo_store_name').attr('disabled','true')".";\n";
		}

		echo "$('#txt_transfer_no').val('".$row[csf("transfer_system_id")]."');\n";
		echo "$('#cbo_transfer_criteria').val(".$row[csf("transfer_criteria")].");\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','true')".";\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		//echo "$('#cbo_to_company_id').val(".$row[csf("to_company")].");\n";
		echo "$('#cbo_to_company_id').attr('disabled','true')".";\n";
		echo "$('#txt_transfer_date').val('".change_date_format($row[csf("transfer_date")])."');\n";
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_ready_to_approved').val(".$row[csf("ready_to_approve")].");\n";
		echo "$('#is_approved').val(".$row[csf("is_approved")].");\n";
		

		if($row[csf("is_approved")] == 1)	
		{
			echo "$('#approved').text('Approved');\n";
		}
		elseif($row[csf("is_approved")] == 3)	
		{
			echo "$('#approved').text('Partial Approved');\n";
		}
		else
		{
			echo "$('#approved').text('');\n";
	  	}
		
		echo "$('#update_id').val(".$row[csf("id")].");\n";
  	}
	exit();	
}

if($action=="barcode_nos")
{
	if($db_type==0) 
	{
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","inv_item_transfer_requ_dtls","entry_form=506 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2) 
	{
		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","inv_item_transfer_requ_dtls","entry_form=506 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	echo $barcode_nos;
	exit();	
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
	if($productId=="") $productId=0; else $productId="'$productId'";
	?> 
	<script>
	
		var selected_id = new Array();var product_id_arr_chk = new Array;
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str)
		{
			//=========Fabrication Mixing validation Start=====================
			var any_selected = $('#hidden_barcode_nos').val();
			if(any_selected=="")
			{
				product_id_arr_chk = [];
			}
			//alert(product_id_arr_chk);
			var product_id = $('#hidden_product_id' + str).val();
			if(product_id_arr_chk.length==0)
			{
				product_id_arr_chk.push( product_id );
			}
			else if( jQuery.inArray( product_id, product_id_arr_chk )==-1 &&  product_id_arr_chk.length>0)
			{
				alert("Fabrication Mixed is Not Allowed");
				return;
			}
			//=======Fabrication Mixing validation End ========================

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
			var txt_qty = $('#txt_qty').val()*1;
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count-1;
			// alert(tbl_row_count);return;
			var blanceQty=0;var flag=1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				var individual_qty=$("#txt_individual_qty"+i).val()*1;
				var blanceQty=(blanceQty==0 && flag==1)?txt_qty:blanceQty;
				if (individual_qty <= blanceQty) 
				{	
					blanceQty = blanceQty - individual_qty;
					js_set_value( i );
					flag=0;
				}
			}
			// return;
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}

		function openmypage_fabric() // Fabric Description
		{
			var company_id=<? echo $company_id; ?>;
			var job_no=$('#txt_job_no').val();
			var order_no=$('#txt_order_no').val();
			var file_no=$('#txt_file_no').val();
			var ref_no=$('#txt_ref_no').val();
			var barcode_no=$('#barcode_no').val();
			var booking_no=$('#txt_booking_no').val();
			var page_link='roll_wise_finish_fabric_requisition_for_transfer_controller.php?action=item_desc_popup&company_id='+company_id+'&job_no='+job_no+'&order_no='+order_no+'&file_no='+file_no+'&ref_no='+ref_no+'&barcode_no='+barcode_no+'&booking_no='+booking_no;

			var title="Search Item Popup";
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=520px,height=300px,center=1,resize=0,scrolling=0','../')
			emailwindow.onclose=function()
			{
				var theform=this.contentDoc.forms[0];
				var hide_data=this.contentDoc.getElementById("hide_data").value;
				if(hide_data!="")
				{
					var fabric_info=hide_data.split("_");
					$('#txt_product_id').val( fabric_info[0] );
					$('#txt_fab_desc').val( fabric_info[1] );
				}
			}
		}
    </script>

	</head>

	<body>
	<div align="center" style="width:1005px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:1005px; margin-left:2px;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" width="895" border="1" rules="all" class="rpt_table">
	                <thead>
						<th>Year</th>
	                    <th>Location</th>
						<th>Buyer</th>
	                    <th>Job</th>
	                    <th>Order No</th>
	                    <th>File No</th>
	                    <th>Internal Ref No</th>
						<th>Style Ref</th>
	                    <th>Barcode No</th>
	                    <th>Booking No</th>
	                    <th>Fabric Description</th>
	                    <th>Quantity</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
	                    </th> 
	                </thead>
	                <tr class="general">
						<td>
						<?php
						echo create_drop_down( "cbo_year_selection", 65, create_year_array(),"", 0,"-- --", date("Y",time()), "",0,"" );
						?>
						</td>
	                    <td>
	                    <?
							echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
						?>
	                    </td>
						<td>
						<?
							echo create_drop_down( "cbo_buyer_name", 100, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "","" );
						?>
                    	</td>
	                    <td align="center">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:60" class="text_boxes"  name="txt_order_no" id="txt_order_no" />	
	                    </td> 
	                    <td align="center">				
	                        <input type="text" style="width:60" class="text_boxes"  name="txt_file_no" id="txt_file_no" />	
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:60" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />	
	                    </td>	
						<td align="center">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_style" id="txt_style" />
	                    </td>		
	                    <td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:50" class="text_boxes" /></td> 
	                    <td align="center">				
	                        <input type="text" style="width:60" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />	
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_fab_desc" id="txt_fab_desc" ondblclick="openmypage_fabric();" placeholder="Dubble Click For Item" readonly/>
	                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_qty" id="txt_qty" />	
	                    </td>
	                       			
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_product_id').value+'_'+<? echo $productId; ?>+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $cbo_store_name_from; ?>, 'create_barcode_search_list_view', 'search_div', 'roll_wise_finish_fabric_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
	//var_dump($data);
	$location_id=trim($data[0]);
	$order_no=$data[1];
	$company_id =$data[2];
	$file_no =trim($data[3]);
	$ref_no =trim($data[4]);
	$barcode_no =trim($data[5]);
	$transfer_cateria =trim($data[6]);
	$store_id=trim($data[7]);
	$bookingNo=trim($data[8]);
	$hiden_product_id=trim($data[9]);
	$productId=trim($data[10]);
	$txt_job_no=trim($data[11]);
	$cbo_year=trim($data[12]);
	$cbo_buyer_name=trim($data[13]);
	$style_ref_no=trim($data[14]);
	$cbo_store_name_from=trim($data[15]);

	$cbo_color_range=trim($data[10]);

	if( $txt_job_no=='' && $order_no=='' && $file_no=='' && $ref_no=='')
	{
		echo "Select Order No/File No/Internal Ref No";	die;
	}

	$search_field_cond="";
	if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	if($txt_job_no!="") $search_field_cond.=" and d.job_no_mst like '%$txt_job_no%'";

	if($hiden_product_id!="") $prod_id_cond=" and prod_id = '$hiden_product_id'";
	if($hiden_product_id!="") $prod_id_cond2=" and to_prod_id = '$hiden_product_id'";
	
	if($productId!=0) $prod_id_cond3=" and prod_id = '$productId'";
	if($productId!=0) $prod_id_cond4=" and to_prod_id = '$productId'";

	if($cbo_color_range!="") $color_range_cond=" and b.color_range_id = '$cbo_color_range'";
	// if($barcodeDataString!=0) $notIn_barcode_cond=" and c.barcode_no not in ($barcodeDataString)";

	$location_cond="";
	if($location_id>0) $location_cond=" and a.location_id=$location_id";
	
	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}

	if($bookingNo !="")
	{
		
		if($db_type==0)
		{
			$booking_year_cond = " and year(d.insert_date) =$cbo_year";
		}
		else{
			$booking_year_cond = " and to_char(d.insert_date,'YYYY') =$cbo_year";
		}

		$sample_booking_cond = " and d.booking_no like '%$bookingNo%' $booking_year_cond";
	}

	if($db_type==0)
	{
		if($cbo_year!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if ($db_type==2)
	{
		if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}

	if($cbo_buyer_name > 0) 
	{
		$buyer_name_cond =" and e.buyer_name = '$cbo_buyer_name'";
	}
	if($style_ref_no !="") 
	{
		$style_ref_cond =" and e.style_ref_no = '$style_ref_no'";
	}
	
	
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form in(71) and is_returned=0 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	if($bookingNo !="")
	{
		//"query from plan to get program_no";
		$program_arr=array();$programIds="";
		$finish_plan=sql_select( "select a.booking_no,b.id as progran_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no like '%$bookingNo%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach ($finish_plan as $row) {
			$program_arr[$row[csf('progran_no')]]["progran_no"]=$row[csf('progran_no')];
			$programIds.="'".$row[csf('progran_no')]."'".",";

		}
		$programIds=chop($programIds,",");
	}

	$po_sql = sql_select("select d.id as po_id, b.booking_no from wo_po_break_down d, wo_booking_dtls b where d.id = b.po_break_down_id and d.status_active = 1 and b.status_active = 1 and b.booking_no like '%$bookingNo%' $search_field_cond");


	foreach ($po_sql as $val) 
	{
		$trans_po_arr[$val[csf("po_id")]] = $val[csf("po_id")];
	}

	$trans_po_arr = array_filter(array_unique($trans_po_arr));
	if(count($trans_po_arr)>0)
	{
		$all_po_nos = implode(",", $trans_po_arr);
		$all_po_cond=""; $poCond=""; 
		if($db_type==2 && count($trans_po_arr)>999)
		{
			$trans_po_arr_chunk=array_chunk($trans_po_arr,999) ;
			foreach($trans_po_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$poCond.=" c.po_breakdown_id in($chunk_arr_value) or ";	
			}
			
			$all_po_cond.=" and (".chop($poCond,'or ').")";	
		}
		else
		{
			$all_po_cond=" and c.po_breakdown_id in($all_po_nos)";	 
		}
	}

	if(!empty($program_arr))
	{
		//"query from knitting production with entry form 2 to get the barcodes";
		$barcode_arr=array();$barcodeAllNo="";
		$qry_roll_dtls=sql_select( "select b.barcode_no,b.booking_no from inv_receive_master a,pro_roll_details b where a.id=b.mst_id and a.receive_basis=2 and  a.booking_id in($programIds) and b.entry_form in(2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
		foreach ($qry_roll_dtls as $row) {

			$barcode_arr[$row[csf('booking_no')]]["barcode_no"]=$row[csf('barcode_no')];
			$barcodeAllNo.="'".$row[csf('barcode_no')]."'".",";

		}
		$barcodeAllNo=chop($barcodeAllNo,",");
	}
	
	if(!empty($barcode_arr)) $barcode_cond_for_booking=" and c.barcode_no in($barcodeAllNo)";

	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.id in ($febric_description_ids)";
	$deter_data_array=sql_select($sql_deter);
	foreach( $deter_data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	// echo $all_fab_barcode_cond;die;
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2",'id','product_name_details');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name",'id','buyer_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$batch_arr=return_library_array( "select id,batch_no from  pro_batch_create_mst",'id','batch_no');
	// if($store_id>0) $store_cond=" and a.store_id!=$store_id"; else $store_cond="";
	if($cbo_store_name_from>0) $store_cond=" and a.store_id=$cbo_store_name_from"; else $store_cond="";

	
	$sql= "SELECT a.recv_number, a.location_id, b.prod_id,b.fabric_description_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,a.store_id as to_store, b.gsm, b.width as dia, d.status_active, e.buyer_name as buyer_id
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(68,126) and c.entry_form in(68,126) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 $search_field_cond $barcode_cond $location_cond $store_cond $barcode_cond_for_booking $prod_id_cond3 $prod_id_cond $all_fab_barcode_cond $year_cond $buyer_name_cond $style_ref_cond"; // order by  qnty

	
	// UNION ALL
	// SELECT a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, b.gsm , b.dia_width as dia, d.status_active, e.buyer_name as buyer_id
	// FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d, wo_po_details_master e 
	// where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.entry_form in(134) and c.entry_form in(134) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0  $barcode_cond $all_po_cond $prod_id_cond4 $prod_id_cond2 $all_fab_barcode_cond
	// UNION ALL
	// SELECT a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, b.gsm , b.dia_width as dia, d.status_active, e.buyer_name as buyer_id
	// FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d, wo_po_details_master e 
	// where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.entry_form in(505) and c.entry_form in(505) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 and a.transfer_criteria =1 and a.to_company = $company_id $barcode_cond $all_po_cond $prod_id_cond4 $prod_id_cond2 $all_fab_barcode_cond

	//echo $sql;
	$result = sql_select($sql);
	foreach ($result as $row) 
	{
		$recv_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$fabric_description_ids .= $row[csf("fabric_description_id")].",";
	}
	$fabric_description_ids = chop($fabric_description_ids,",");
	$recv_barcode_arr = array_filter(array_unique($recv_barcode_arr));
	if(count($recv_barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $recv_barcode_arr);
		$BarCond = $all_barcode_cond = "";

		if($db_type==2 && count($recv_barcode_arr)>999)
		{
			$barcode_arr_chunk=array_chunk($recv_barcode_arr,999) ;
			foreach($barcode_arr_chunk as $chunk_arr)
			{
				$BarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$all_barcode_cond.=" and (".chop($BarCond,'or ').")";

		}
		else
		{
			$all_barcode_cond=" and a.barcode_no in($all_barcode_nos)";
		}
	}
	if(!empty($recv_barcode_arr))
	{
		//echo "SELECT a.barcode_no, a.receive_basis, a.booking_no, b.width,b.dia_width_type, b.body_part_id, b.color_id, b.gsm, b.batch_id from pro_roll_details a, pro_finish_fabric_rcv_dtls b where a.dtls_id = b.id and a.entry_form = 68 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond";
		$stitch_lot_sql = sql_select("SELECT a.barcode_no, a.receive_basis, a.booking_no, b.width,b.dia_width_type, b.body_part_id, b.color_id, b.gsm, b.batch_id from pro_roll_details a, pro_finish_fabric_rcv_dtls b where a.dtls_id = b.id and a.entry_form = 68 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");
		foreach ($stitch_lot_sql as $row)
		{
			$production_ref_arr[$row[csf("barcode_no")]]['width'] = $row[csf("width")];
			$production_ref_arr[$row[csf("barcode_no")]]['dia_width_type'] = $row[csf("dia_width_type")];
			$production_ref_arr[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
			$production_ref_arr[$row[csf("barcode_no")]]['body_part_id'] = $row[csf("body_part_id")];
			$production_ref_arr[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$production_ref_arr[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$production_ref_arr[$row[csf("barcode_no")]]['color_id'] = $color_name_arr[$row[csf("color_id")]];
			$production_ref_arr[$row[csf("barcode_no")]]['batch_id'] = $batch_arr[$row[csf("batch_id")]];
		}

		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in ($fabric_description_ids)";
		//echo $sql_deter;
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
		//var_dump($composition_arr);
	}
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1590" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Body Part</th>
            <th width="30">Dia</th>
            <th width="30">Gsm</th>
            <th width="60">Color</th>
            <th width="60">Width Type</th>
            <th width="100">Batch No</th>
            <th width="100">Buyer</th>
            <th width="70">Job No</th>
            <th width="110">Order No</th>
            <th width="90">Barcode No</th>
            <th width="50">Roll No</th>
            <th width="50">Roll Qty.</th>
            <th width="50">Qty. In Pcs</th>
            <th width="70">File NO</th>
            <th width="70">Int. Ref No</th>
            <th width="70">Shipment Date</th>
            <th width="110">Location</th>
            <th width="100">Store</th>
            <th>Status</th>
        </thead>
	</table>
	<div style="width:1610px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1590" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					$trans_flag = "";
					if($row[csf('entry_form')] == 134 || $row[csf('entry_form')] == 505)
					{
						$trans_flag = " (T)";
					}

					$batch_no = $production_ref_arr[$row[csf("barcode_no")]]['batch_id'];
					$yarn_lot = $production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'];
					$yarnCount = $production_ref_arr[$row[csf("barcode_no")]]['yarn_count'];
					$dia_width = $production_ref_arr[$row[csf("barcode_no")]]['width'];
					$gsm = $production_ref_arr[$row[csf("barcode_no")]]['gsm'];
					$body_part_id = $production_ref_arr[$row[csf("barcode_no")]]['body_part_id'];
					$receive_basis = $production_ref_arr[$row[csf("barcode_no")]]['receive_basis'];
					$colorName = $production_ref_arr[$row[csf("barcode_no")]]['color_id'];
					$dia_width_type = $production_ref_arr[$row[csf("barcode_no")]]['dia_width_type'];
					if($receive_basis == 2)
					{
						$program_no = $production_ref_arr[$row[csf("barcode_no")]]['booking_no'];
					}else{
						$program_no = "";
					}

					$yarn_count_array=array_unique(explode(",",$yarnCount));
					$all_count="";
					foreach($yarn_count_array as $count_id)
					{
						$all_count.=$yarn_count_arr[$count_id].",";
					}
					$all_count=chop($all_count,",");
					
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_individual_qty" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
							<input type="hidden" name="hidden_product_id" id="hidden_product_id<?php echo $i; ?>" value="<?php echo $row[csf('prod_id')]; ?>"/>
						</td>
						<td width="150" title="<? echo $row[csf('prod_id')]; ?>" align="center"><p><? echo $composition_arr[$row[csf('fabric_description_id')]]; ?></p></td>
						<td width="100" align="center"><p><? echo $body_part[$body_part_id]; ?></p></td>
						<td width="30" align="center"><p><? echo $dia_width; ?></p></td>
						<td width="30" align="center"><p><? echo $gsm; ?></p></td>
						<td width="60" align="center"><p><? echo $colorName; ?></p></td>
						<td width="60" align="center"><p><? echo $dia_width_type; ?></p></td>
						<td width="100" align="center"><p><? echo $batch_no; ?></p></td>
						<td width="100" align="center"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="70" align="center"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110" align="center"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="90" align="center"><p><? echo $row[csf('barcode_no')].$trans_flag; ?>&nbsp;</p></td>
						<td width="50" align="center"><? echo $row[csf('roll_no')]; ?></td>
                        <td width="50" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						<td width="50" align="right"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
                        <td width="70" align="center"><? echo $row[csf('file_no')]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('grouping')]; ?></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        <td width="110" align="center"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
                        <td width="100" align="center"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
						<td align="center"><? echo $row_status[$row[csf('status_active')]]; ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="1610">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();
}

//item search Start------------------------------//
if($action=="item_desc_popup")
{
	echo load_html_head_contents("Item Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);	
	// echo $job_no.'=====';						
	?>
	<script>
	
		function js_set_value(data)
		{
			// alert(data);
			$('#hide_data').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:475px;">
	            <table width="470" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Search By</th>
	                    <th width="170">Fabric Details</th>
	                    <th>
	                    	<input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');">
	                        <input type="hidden" name="hide_data" id="hide_data" value="" />
	                    </th> 					
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">	
	                    	<?
								$search_by_arr=array(1=>"Product Details");					
								echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", $selected,$dd,$disable );
							?>
	                        </td>     
	                        <td align="center">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="" />	
	                        </td> 	
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $order_no; ?>'+'**'+'<? echo $file_no; ?>'+'**'+'<? echo $ref_no; ?>'+'**'+'<? echo $barcode_no; ?>'+'**'+'<? echo $booking_no; ?>', 'create_product_search_list_view', 'search_div', 'roll_wise_finish_fabric_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                    	</td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:5px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if($action=="create_product_search_list_view")
{
	// print_r($data);die;
	$data=explode('**',$data);
	$company_id=$data[0];
	$txt_search_by=$data[1];
	$fabric_dtls=$data[2];
	$txt_job_no=$data[3];
	$order_no=$data[4];
	$file_no=$data[5];
	$ref_no=$data[6];
	$barcode_no=$data[7];
	$bookingNo=$data[8];

	$sql_cond="";
	if(trim($fabric_dtls)!="")
	{
		if(trim($txt_search_by)==1)
		{
			$sql_cond= " and product_name_details LIKE '%$fabric_dtls%'";	 
		}
	}
	
	$search_field_cond="";
	if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	if($txt_job_no!="") $search_field_cond.=" and d.job_no_mst like '%$txt_job_no%'";
	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}
	if($bookingNo !="")
	{
		//"query from plan to get program_no";
		$program_arr=array();$programIds="";
		$finish_plan=sql_select( "select a.booking_no,b.id as progran_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no like '%$bookingNo%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach ($finish_plan as $row) {
			$program_arr[$row[csf('progran_no')]]["progran_no"]=$row[csf('progran_no')];
			$programIds.="'".$row[csf('progran_no')]."'".",";

		}
		$programIds=chop($programIds,",");
	}
	if(!empty($program_arr))
	{
		//"query from knitting production with entry form 2 to get the barcodes";
		$barcode_arr=array();$barcodeAllNo="";
		$qry_roll_dtls=sql_select( "select b.barcode_no,b.booking_no from inv_receive_master a,pro_roll_details b where a.id=b.mst_id and a.receive_basis=2 and  a.booking_id in($programIds) and b.entry_form in(2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
		foreach ($qry_roll_dtls as $row) {

			$barcode_arr[$row[csf('booking_no')]]["barcode_no"]=$row[csf('barcode_no')];
			$barcodeAllNo.="'".$row[csf('barcode_no')]."'".",";

		}
		$barcodeAllNo=chop($barcodeAllNo,",");
	}
	if(!empty($barcode_arr)) $barcode_cond_for_booking=" and c.barcode_no in($barcodeAllNo)";

	$po_sql = sql_select("select d.id as po_id, b.booking_no from wo_po_break_down d, wo_booking_dtls b where d.id = b.po_break_down_id and d.status_active = 1 and b.status_active = 1 and b.booking_no like '%$bookingNo%' $search_field_cond");

	foreach ($po_sql as $val) 
	{
		$trans_po_arr[$val[csf("po_id")]] = $val[csf("po_id")];
	}

	$trans_po_arr = array_filter(array_unique($trans_po_arr));
	if(count($trans_po_arr)>0)
	{
		$all_po_nos = implode(",", $trans_po_arr);
		$all_po_cond=""; $poCond=""; 
		if($db_type==2 && count($trans_po_arr)>999)
		{
			$trans_po_arr_chunk=array_chunk($trans_po_arr,999) ;
			foreach($trans_po_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$poCond.=" c.po_breakdown_id in($chunk_arr_value) or ";	
			}
			
			$all_po_cond.=" and (".chop($poCond,'or ').")";	
		}
		else
		{
			$all_po_cond=" and c.po_breakdown_id in($all_po_nos)";	 
		}
	}

	$sql= "SELECT e.id, d.po_number, a.entry_form, e.product_name_details,b.fabric_description_id as fabric_description_id 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, wo_po_break_down d, product_details_master e 
	where a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id = d.id and b.prod_id=e.id and a.company_id = $company_id and b.trans_id <> 0 and a.entry_form in (68,126) and c.entry_form in (68,126) and c.status_active = 1 and c.is_deleted = 0 and c.roll_no > 0 and c.is_sales <> 1 and c.re_transfer = 0 $search_field_cond $barcode_cond $barcode_cond_for_booking $sql_cond
	group by e.id, d.po_number, a.entry_form, e.product_name_details, b.fabric_description_id 
	";

	//echo $sql;die;

	$result =sql_select($sql);

	foreach ($result as $row)
	{ 
		$fabric_description_ids .= $row[csf("fabric_description_id")].",";
	}

	$fabric_description_ids = chop($fabric_description_ids,",");


	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

	// echo "SELECT b.prod_id, b.color_id
	// from pro_roll_details c, pro_finish_fabric_rcv_dtls b
	// where c.dtls_id=b.id and c.entry_form=68 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 $all_po_cond $barcode_cond
	// group by b.prod_id, b.color_id";die;

	$fabric_info_sql = sql_select("SELECT b.prod_id, b.color_id
	from pro_roll_details c, pro_finish_fabric_rcv_dtls b
	where c.dtls_id=b.id and c.entry_form=68 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 $all_po_cond $barcode_cond
	group by b.prod_id, b.color_id");
	$color_name_arr2=array(); $color_range2=array();
	foreach ($fabric_info_sql as $row)
	{
		$color_name_arr2[$row[csf("prod_id")]] = $color_name_arr[$row[csf("color_id")]];
		$color_range2[$row[csf("prod_id")]] = $color_range[$row[csf("color_range_id")]];
	}
	unset($fabric_info_sql);


	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in ($fabric_description_ids)";
	//echo $sql_deter;
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
	//var_dump($composition_arr);
	unset($data_array);

	//$sql = "select id,product_name_details,gsm,dia_width from product_details_master where company_id=$company_id and item_category_id=13 $sql_cond";


	// $arr=array(2=>$color_name_arr2,3=>$color_range2);
	// echo create_list_view("tbl_list_search", "Product Id,Product Details,Fabric Color,Po Number", "80,250,80,80","570","260",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,0,id,0", $arr , "id,product_name_details,id,po_number", "",'','0,0','',0);
	?>

	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="590" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Product Id</th>
            <th width="230">Product Details</th>
            <th width="80">Fabric Color</th>
            <th width="">Po Number</th>
        </thead>
	</table>
	<div style="width:590px; max-height:230px; overflow-y:scroll" id="" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="570" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$febric_des = $composition_arr[$row[csf('fabric_description_id')]];
        		?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>_<? echo $febric_des; ?>');"> 
                    <td width="30" align="center" style="word-break: break-all;"><? echo $i; ?></td>
                    <td width="80" align="center" style="word-break: break-all;"><p><? echo $row[csf('id')]; ?></p></td>
                    <td width="230" align="center" style="word-break: break-all;"><p><? echo $febric_des; ?></p></td>
                    <td width="80" style="word-break: break-all;"><p><? echo $color_name_arr2[$row[csf('id')]]; ?></p></td>
                    <td width="" style="word-break: break-all;"><p><? echo $row[csf('po_number')]; ?></p></td>
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
//item search End------------------------------//

if ($action=="to_order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	// $item_desc_ids="'9,10'";
	$item_desc_ids="'$item_desc_ids'";
	$item_gsm="'$item_gsm'";
	$item_dia="'$item_dia'";
	$color_id="'$color_id'";
	?> 
	<script>
        function js_set_value(data)
        {
            $('#order_id').val(data);
            parent.emailwindow.hide();
        }
    </script>
	</head>
	<body>
	<div align="center" style="width:1040px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:1030px;margin-left:10px">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="1100" class="rpt_table">
					<thead>
						<th>Buyer Name</th>
						<th>Job</th>
						<th>Order No</th>
						<th>Style Ref.</th>
						<th>Internal Ref.</th>
						<th width="180">Shipment Date Range</th>
						<th width="100">Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
								echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_to_company_id' order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",'' );
								//echo "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_to_company_id' order by buy.buyer_name";
							?>
						</td>
						<td align="center">
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
	                    </td>
						<td>
							<input type="text" style="width:150px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
						</td>
						<td align="center">				
	                        <input type="text" style="width:60" class="text_boxes"  name="txt_style_ref_no" id="txt_style_ref_no" />	
	                    </td>
						<td align="center">				
	                        <input type="text" style="width:60" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />	
	                    </td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" readonly>
						</td>
						<td>
							<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:80px" placeholder="Booking No">
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_to_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $item_desc_ids; ?>+'_'+<? echo $item_gsm; ?>+'_'+<? echo $item_dia; ?>+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_ref_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+<? echo $color_id; ?>, 'create_po_search_list_view', 'search_div', 'roll_wise_finish_fabric_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="8" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="margin-top:10px" id="search_div"></div> 
			</fieldset>
		</form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_po_search_list_view')
{	
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string=trim($data[1]);
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$shipment_date ="";
	
	$type=$data[5];

	$item_desc_ids=$data[7];
	$item_gsm=$data[8];
	$item_dia=$data[9];
	$item_Job=$data[10];
	$item_style_ref=$data[11];
	$item_internal_ref=$data[12];
	$color_id=$data[13];

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	$item_desc_id_arr=array_filter(array_unique(explode(',', $item_desc_ids)));// 9,10
	$item_gsm=implode(",", array_filter(array_unique(explode(',', $item_gsm))));
	$item_dia_arr=array_filter(array_unique(explode(',', $item_dia)));
	// print_r($item_desc_id_arr);
	// echo $item_gsm_arr;
	$item_dia="";
    foreach ($item_dia_arr as $key => $value) 
    {
        if ($item_dia=="") 
        {
            $item_dia.= $value;
        }
        else 
        {
            $item_dia.= "','".$value;
        }
    }
    // echo $item_dia;

    $fabric_construction="";$fabric_composition="";
    foreach ($item_desc_id_arr as $key => $value) 
    {
        if ($fabric_construction=="") 
        {
            $fabric_construction.= $constructtion_arr[$value];
        }
        else 
        {
            $fabric_construction.= "','".$constructtion_arr[$value];
        }
        if ($fabric_composition=="") 
        {
            $fabric_composition.= $composition_arr[$value];
        }
        else 
        {
            $fabric_composition.= "','".$composition_arr[$value];
        }
    }
    $fabric_composition=rtrim($fabric_composition,' ');
    // echo $fabric_composition;die;

	/*$composition_val='';$constructtion="";
	foreach ($item_desc_id_arr as $key => $value) 
	{
		$constructtion.=$constructtion_arr[$value].',';
		$composition_val.=$composition_arr[$value].',';
	}
	$fabric_constructtion=rtrim($constructtion,',');
	$fabric_composition=rtrim($composition_val,',');
	echo $fabric_constructtion;*/
    

		
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$status_cond=" and b.status_active=1";

	$bookingNo= trim($data[5]);
	if($bookingNo!="") $booking_cond=" and c.booking_no like '%$bookingNo%'";

	if($fabric_construction!="") $fabric_construction_cond=" and c.construction in ('$fabric_construction')";
	if($fabric_composition!="") $fabric_composition_cond=" and c.copmposition like '%$fabric_composition'";
	if($item_dia!="") $item_dia_cond=" and c.dia_width in ('$item_dia')";
	if($item_gsm!="") $item_gsm_cond=" and c.gsm_weight in ($item_gsm)";
	if($item_Job!="") $job_cond=" and a.job_no like '%$item_Job%'";
	if($item_style_ref!="") $style_ref_cond=" and a.style_ref_no like '%$item_style_ref%'";
	if($item_internal_ref!="") $internal_ref_cond=" and b.grouping like '%$item_internal_ref%'";
	if($color_id!="") $color_id_cond=" and c.fabric_color_id in ($color_id)";	

	$po_cond="";
	if($search_string!="")
	$po_cond=" and b.po_number ='$search_string'";

	$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,b.grouping, c.booking_no,c.booking_mst_id, c.construction, c.copmposition, c.fabric_description, c.gsm_weight, c.dia_width 
	from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name like '$buyer' $po_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.booking_type = 1 $status_cond $shipment_date $booking_cond $year_field_cond $item_dia_cond $item_gsm_cond $fabric_construction_cond $fabric_composition_cond $job_cond $style_ref_cond $internal_ref_cond $color_id_cond
	group by a.job_no,to_char(a.insert_date,'YYYY'), a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date,b.grouping, c.booking_no,c.booking_mst_id, c.construction, c.copmposition, c.fabric_description, c.gsm_weight, c.dia_width order by b.id, b.pub_shipment_date";
	// echo $sql;die;
	

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$arr=array(2=>$company_arr,3=>$buyer_arr);
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,PO Quantity,Shipment Date,Booking No", "70,60,70,80,120,90,110,90,80,80","950","200",0, $sql , "js_set_value", "id,po_number,job_no,booking_no,booking_mst_id", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,po_quantity,shipment_date,booking_no", "",'','0,0,0,0,0,1,0,1,3,0');
	
	exit();
}
if($action=="bodypart_list_order_wise")
{
	$bodyPart_arr=array();

	$data = chop($data,",");

	if($data)
	{
		$body_part_sql = sql_select("SELECT x.po_break_down_id, x.body_part_id from ( SELECT b.po_break_down_id, a.body_part_id from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls b where a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted = 0 and b.pre_cost_fabric_cost_dtls_id=a.id and b.po_break_down_id in ($data) and b.booking_type =1 union all select c.po_break_down_id, b.body_part_id from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c where b.job_no=c.job_no and a.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id=a.id and c.po_break_down_id in ($data) and a.fabric_description = b.id and c.booking_type=4 ) x group by x.po_break_down_id, x.body_part_id");


		foreach($body_part_sql as $row)
		{
			$bodyPart_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]]=$body_part[$row[csf('body_part_id')]];
		}
	}
	$jsBodyPart_arr= json_encode($bodyPart_arr);
	echo $jsBodyPart_arr;
	die();
}

if($action=="bodypart_list_sample_wise")
{
	$bodyPart_arr=array();

	$data = chop($data,",");

	if($data)
	{
		$body_part_sql = sql_select("SELECT b.body_part as body_part_id, a.id as po_break_down_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and  a.id in ($data)");

		foreach($body_part_sql as $row)
		{
			$bodyPart_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]]=$body_part[$row[csf('body_part_id')]];
		}
	}
	$jsBodyPart_arr= json_encode($bodyPart_arr);
	echo $jsBodyPart_arr;
	die();
}

if ($action == "roll_issue_no_of_copy_print1") // Print 
{
	extract($_REQUEST);
	echo load_html_head_contents("Grey Roll Issue to Process", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$requ_company   	= $data[0];
	$system_no 			= $data[1];
	$report_title 		= $data[2];
	$mst_id     		= $data[3];
	$show_report_format = $data[4];
	
	$from_company_id 	= $data[5];
	$transfer_date 		= $data[6];
	$challan_no 		= $data[7];
	$store_name_from 	= $data[8];
	$store_name_to 		= $data[9];
	$item_category_id 	= $data[10];
	$transfer_criteria 	= $data[11];
	$remarks 			= $data[12];


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
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");
	
	$company_info = sql_select("select ID, COMPANY_NAME, PLOT_NO, ROAD_NO, CITY, CONTACT_NO, COUNTRY_ID from lib_company where status_active=1 and is_deleted=0 order by company_name");
	foreach($company_info as $row)
	{
		$company_library[$row['ID']] = $row['COMPANY_NAME'];
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
	
	$mainQquery="select a.id, a.transfer_system_id,b.from_order_id,b.to_order_id ,b.feb_description_id,b.gsm,b.dia_width,b.batch_id,b.color_id,b.transfer_qnty,b.qty_in_pcs,b.from_prod_id,b.barcode_no from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and a.id=$mst_id";

	$mainQquerySql=sql_select($mainQquery);
	$poBreakdownIdArr=array();$batchIdArr=array();$productIdArr=array();$barcodeArr=array();
	foreach($mainQquerySql as $row)
	{
		$poBreakdownIdArr[$row[csf("from_order_id")]] = $row[csf("from_order_id")].",".$row[csf("to_order_id")];
		$batchIdArr[$row[csf("batch_id")]] = $row[csf("batch_id")];
		$productIdArr[$row[csf("from_prod_id")]] = $row[csf("from_prod_id")];
		$barcodeArr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		//$poBreakdownIdArr.=$row[csf("from_order_id")].",";

	}
	//$poBreakdownIdArr=chop(",",$poBreakdownIdArr);
	//for order details

	$batch_sql = sql_select("select id, batch_no,color_range_id from pro_batch_create_mst where status_active=1 and is_deleted=0". where_con_using_array($batchIdArr, '0', 'id'));
	foreach($batch_sql as $row)
	{
		$batch_arr[$row[csf("id")]]=$row[csf("batch_no")];
		$colorRange_arr[$row[csf("id")]]=$row[csf("color_range_id")];
	}


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
		$poNoArr[$row['ID']]['style_ref_no'] = $row['STYLE_REF_NO'];
	}

	//for detarmination
	$product_array=array();
	$product_sql = sql_select("SELECT id AS ID, detarmination_id AS DETARMINATION_ID, gsm AS GSM, dia_width AS DIA_WIDTH, unit_of_measure AS UNIT_OF_MEASURE FROM product_details_master WHERE item_category_id=2 ".where_con_using_array($productIdArr, '0', 'id'));
	foreach($product_sql as $row)
	{
		$detarminationIdArr[$row['DETARMINATION_ID']]=$row['DETARMINATION_ID'];

		$product_array[$row[csf("id")]]['gsm']=$row["GSM"];
		$product_array[$row[csf("id")]]['dia_width']=$row["DIA_WIDTH"];
		$product_array[$row[csf("id")]]['deter_id']=$row["DETARMINATION_ID"];
		$product_array[$row[csf("id")]]['uom']=$row["UNIT_OF_MEASURE"];
	}
	//echo "<pre>"; print_r($product_array);

	//for composition

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($detarminationIdArr, '0', 'a.id');

	$data_array_deter=sql_select($sql_deter);
	foreach( $data_array_deter as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$production_sql = "SELECT A.BUYER_ID,A.RECEIVE_BASIS, A.BOOKING_NO, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.LOCATION_ID, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.YARN_COUNT, B.YARN_LOT, B.COLOR_ID, B.COLOR_RANGE_ID, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_DIA, B.MACHINE_GG, C.BOOKING_NO AS BWO, C.BOOKING_WITHOUT_ORDER,C.IS_SALES, B.BODY_PART_ID, B.PROD_ID, D.DETARMINATION_ID, C.BARCODE_NO,B.NO_OF_ROLL , sum(c.qc_pass_qnty_pcs) as ISSUE_QTY_PCS
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form=2 and c.entry_form=2 and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0" .where_con_using_array($barcodeArr, '0', 'c.barcode_no')."
	group by  A.BUYER_ID,A.RECEIVE_BASIS, A.BOOKING_NO, A.KNITTING_SOURCE, A.KNITTING_COMPANY, A.LOCATION_ID, B.FEBRIC_DESCRIPTION_ID, B.GSM, B.WIDTH, B.YARN_COUNT, B.YARN_LOT, B.COLOR_ID, B.COLOR_RANGE_ID, B.STITCH_LENGTH, B.BRAND_ID, B.MACHINE_DIA, B.MACHINE_GG, C.BOOKING_NO, C.BOOKING_WITHOUT_ORDER, C.IS_SALES, B.BODY_PART_ID, B.PROD_ID, D.DETARMINATION_ID, C.BARCODE_NO,B.NO_OF_ROLL 
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
		$production_roll_array[$row['BARCODE_NO']]['no_of_roll']=$row['NO_OF_ROLL'];
	}
	if ($show_report_format==0) // barcode wise start
	{
		foreach($mainQquerySql as $row)
		{
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['buyer']=$buyer_array[$poNoArr[$row[csf("from_order_id")]]['buyer_name']];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['job']=$poNoArr[$row[csf("from_order_id")]]['job_no'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['color']=$color_name_arr[$row[csf("color_id")]];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['batch_no']=$batch_arr[$row[csf("batch_id")]];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['from_order_id']=$poNoArr[$row[csf("from_order_id")]]['po_number'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['to_order_id']=$poNoArr[$row[csf("to_order_id")]]['po_number'];
			
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['color_range']=$colorRange_arr[$row[csf("batch_id")]];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['feb_description_id']=$row[csf("feb_description_id")];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['gsm']=$row[csf("gsm")];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['dia_width']=$row[csf("dia_width")];
			
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['count']=$production_roll_array[$row[csf('barcode_no')]]['yarn_count'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['yarn_brand']=$production_roll_array[$row[csf('barcode_no')]]['brand_id'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['yarn_lot']=$production_roll_array[$row[csf('barcode_no')]]['yarn_lot'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['stitch_length']=$production_roll_array[$row[csf('barcode_no')]]['stitch_length'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['mc_dia']=$production_roll_array[$row[csf('barcode_no')]]['machine_dia'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['guage']=$production_roll_array[$row[csf('barcode_no')]]['machine_gg'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['uom']=$product_array[$row[csf("from_prod_id")]]['uom'];
			
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['qty_in_pcs']+=$row[csf("qty_in_pcs")];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['no_of_roll']=$production_roll_array[$row[csf("barcode_no")]][$row[csf('barcode_no')]]['no_of_roll'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['transfer_qnty']+=$row[csf("transfer_qnty")];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']][$row[csf('barcode_no')]]['barcode_no']=$row[csf("barcode_no")];

			//$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['to_order_id']=$row[csf("to_order_id")];
		}
	}
	else
	{
		foreach($mainQquerySql as $row)
		{
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['buyer']=$buyer_array[$poNoArr[$row[csf("from_order_id")]]['buyer_name']];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['job']=$poNoArr[$row[csf("from_order_id")]]['job_no'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['color']=$color_name_arr[$row[csf("color_id")]];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['batch_no']=$batch_arr[$row[csf("batch_id")]];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['from_order_id']=$poNoArr[$row[csf("from_order_id")]]['po_number'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['to_order_id']=$poNoArr[$row[csf("to_order_id")]]['po_number'];
			
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['color_range']=$colorRange_arr[$row[csf("batch_id")]];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['feb_description_id']=$row[csf("feb_description_id")];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['gsm']=$row[csf("gsm")];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['dia_width']=$row[csf("dia_width")];
			
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['count']=$production_roll_array[$row[csf('barcode_no')]]['yarn_count'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['yarn_brand']=$production_roll_array[$row[csf('barcode_no')]]['brand_id'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['yarn_lot']=$production_roll_array[$row[csf('barcode_no')]]['yarn_lot'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['stitch_length']=$production_roll_array[$row[csf('barcode_no')]]['stitch_length'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['mc_dia']=$production_roll_array[$row[csf('barcode_no')]]['machine_dia'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['guage']=$production_roll_array[$row[csf('barcode_no')]]['machine_gg'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['uom']=$product_array[$row[csf("from_prod_id")]]['uom'];
			
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['qty_in_pcs']+=$row[csf("qty_in_pcs")];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['no_of_roll']=$production_roll_array[$row[csf("barcode_no")]]['no_of_roll'];
			$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['transfer_qnty']+=$row[csf("transfer_qnty")];

			//$mainArr[$poNoArr[$row[csf("from_order_id")]]['job_no']][$row[csf('color_id')]][$row[csf('from_order_id')]][$row[csf('to_order_id')]][$row[csf('feb_description_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$production_roll_array[$row[csf('barcode_no')]]['stitch_length']]['to_order_id']=$row[csf("to_order_id")];
		}
	}
	

	/*echo "<pre>";
	print_r($mainArr);
	echo "</pre>";*/
	/*$composition_arr=array();
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
	}*/
	//echo "<pre>"; print_r($composition_arr);



	

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
    $com_dtls = fnc_company_location_address($requ_company, $store_location_id, 2);
	$data_array = sql_select("select image_location  from common_photo_library where master_tble_id='".$data[0]."' and form_name='company_details' and is_deleted=0 and file_type=1");

		
		?>
    
		<div style="width:1140px;">
			<table width="1140" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
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
                    <td align="center" id="barcode_img_id" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong><? echo $report_title; ?></strong>
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
		            <table style="margin-right:-40px;" cellspacing="0" width="1160" border="1" rules="all" class="rpt_table">
						<tr>
							<td width="125"><strong>Transfer ID:</strong></td>
							<td width="250px"><? echo $system_no; ?></td>
							<td width="125"><strong>Transfer Date:</strong></td>
							<td width="150px"><? echo $transfer_date; ?></td>
							<td width="130"><strong>Challan No:</strong></td>
							<td width="130"><? echo $challan_no; ?></td>					
						</tr>
						<tr>
							<td><strong>Transfer Criteria:</strong></td>
							<td><? echo $item_transfer_criteria[$transfer_criteria]; ?></td>
							<td><strong>From Company:</strong></td>
							<td><? echo $company_library[$from_company_id]; //$location; ?></td>
							<td><strong>To Company:</strong></td> 
							<td><? echo $company_library[$requ_company]; ?></td>                
						</tr>
						<tr>
							<td><strong>Item Category:</strong></td>
							<td><? echo $item_category[$item_category_id];?></td>
							<td><strong>From Store:</strong></td>
							<td><? echo $store_arr[$store_name_from];?></td>
							<td><strong>To Store:</strong></td>
							<td><? echo $store_arr[$store_name_to];?></td>

						</tr>
						<tr>
							<td><strong>Remarks:</strong></td>
							<td colspan="3"><? echo $remarks; ?></td>
						</tr>
						
					</table>
					<?
                    if ($show_report_format==0) // barcode wise start
                    {
                    	?>
						<table style="margin-right:-40px;" cellspacing="0" width="1960" border="1" rules="all" class="rpt_table">
							<thead bgcolor="#dddddd">
								<tr>
									<th width="20">SL</th>
									<th width="120">Buyer<br/><br/>Job
									<th width="120">Prog<br/><br/> Booking
									<th width="120">Fab Color<br/><br/> Batch Number</th>
									<th width="100">From Order</th>
									<th width="100">To Order</th>
									<th width="100">Color Range</th>
									<th width="100">Fab. Construction</th>
									<th width="100">Fab. Compositon	</th>
									<th width="100">Fin. GSM<br/><br/>Fin Dia</th>
									<th width="100">Count</th>
									<th width="100">Yarn Brand</th>
									<th width="100">Yarn Lot</th>
									<th width="100">Stich Lenth</th>
									<th width="100">MC Dia X<br/><br/> Gauge</th>
									<th width="100">Barcode</th>
									<th width="100">UOM</th>
									<th width="100">Qty. In Pcs</th>
									<th width="100">Total Roll</th>
									<th>Transfered Req. Qnty</th>
								</tr>
								
							</thead>
	                        <tbody>
								<?
									$i=1;	
									foreach ($mainArr as $jobNo => $job_data) 
									{
										foreach ($job_data as $colorId => $color_data) 
										{
											foreach ($color_data as $fromOrd => $fromOrd_data) 
											{
												foreach ($fromOrd_data as $toOrd => $toOrd_data) 
												{
													foreach ($toOrd_data as $fabDescId => $fabDescId_data) 
													{
														foreach ($fabDescId_data as $gsm => $gsm_data) 
														{
															foreach ($gsm_data as $dia => $dia_data) 
															{
																foreach ($dia_data as $stitch_length => $stitch_length_data) 
																{
																	foreach ($stitch_length_data as $barcode_no => $row) 
																	{
																		if ($i % 2 == 0)
																			$bgcolor = "#E9F3FF";
																		else
																			$bgcolor = "#FFFFFF";	

																		?>
											                            <tr bgcolor="<? echo $bgcolor; ?>">
											                                <td style="font-size: 15px"><? echo $i; ?></td>
											                                <td style="font-size: 15px">
											                                    <div style="word-wrap:break-word; width:120px;text-align: center;"><? 
											                                    echo $row['buyer'].'<br><br>'.$row['job']; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:120px;text-align: center;"><? 
											                                    echo $prog.'<br><br>'.$booking; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:120px;text-align: center;"><? 
											                                    echo $row['color'].'<br><br>'.$row['batch_no']; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['from_order_id']; ?>
											                                	</div>
											                                </td>
									 										<td style="font-size: 15px">
									 											<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['to_order_id']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $colorRange_arr[$row['color_range']]; ?>
											                                	</div>
											                                </td>
																			<td style="font-size: 15px">
																				<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $constructtion_arr[$row['feb_description_id']]; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $composition_arr[$row['feb_description_id']]; ?>
											                                	</div>
											                                </td>

											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px;text-align: center;"><? 
											                                    echo $row['gsm'].'<br/><br/>'.$row['dia_width']; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $yarn_count_details[$row['count']]; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $brand_details[$row['yarn_brand']]; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['yarn_lot']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['stitch_length']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px;text-align: center;">
											                                	<div style="word-wrap:break-word; width:100px"><? 
											                                    	echo $row['mc_dia'].'<br><br>'.$row['guage']; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['barcode_no']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $unit_of_measurement[$row['uom']]; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['qty_in_pcs']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['no_of_roll']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; text-align: center;">
											                                		<? echo $row['transfer_qnty'];  ?>
											                                	</div>
											                                </td>	

											                               
											                            </tr>
																		<?
																		$i++;
																		$fab_tot_qty_in_pcs+=$row['qty_in_pcs'];
																		$fab_tot_no_of_roll+=$row['no_of_roll'];
																		$fab_tot_transfer_qnty+=$row['transfer_qnty'];
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
									<tr class="tbl_bottom">
										<td colspan="17" style=" text-align:right;font-size: 14px;"><strong>Total</strong></td>
										<td align="right" style="font-size: 14px;">
											<b><? echo number_format($fab_tot_qty_in_pcs, 2, '.', ''); ?></b>
										</td>
										<td align="right" style="font-size: 14px;"><? echo $fab_tot_no_of_roll; ?></td>
										<td align="right" style="font-size: 14px;"><? echo number_format($fab_tot_transfer_qnty, 2, '.', ''); ?></td>
									</tr>			
											
											
									
		                    </tbody>	
	                    </table>
                    	<?
                	} // barcode wise End
                    else // summary Start
                    {
                    	?>
						<table style="margin-right:-40px;" cellspacing="0" width="1860" border="1" rules="all" class="rpt_table">
							<thead bgcolor="#dddddd">
								<tr>
									<th width="20">SL</th>
									<th width="120">Buyer<br/><br/>Job
									<th width="120">Prog<br/><br/> Booking
									<th width="120">Fab Color<br/><br/> Batch Number</th>
									<th width="100">From Order</th>
									<th width="100">To Order</th>
									<th width="100">Color Range</th>
									<th width="100">Fab. Construction</th>
									<th width="100">Fab. Compositon	</th>
									<th width="100">Fin. GSM<br/><br/>Fin Dia</th>
									<th width="100">Count</th>
									<th width="100">Yarn Brand</th>
									<th width="100">Yarn Lot</th>
									<th width="100">Stich Lenth</th>
									<th width="100">MC Dia X<br/><br/> Gauge</th>
									<th width="100">UOM</th>
									<th width="100">Qty. In Pcs</th>
									<th width="100">Total Roll</th>
									<th>Transfered Req. Qnty</th>
								</tr>
								
							</thead>
	                        <tbody>
								<?
									$i=1;	
									foreach ($mainArr as $jobNo => $job_data) 
									{
										foreach ($job_data as $colorId => $color_data) 
										{
											foreach ($color_data as $fromOrd => $fromOrd_data) 
											{
												foreach ($fromOrd_data as $toOrd => $toOrd_data) 
												{
													foreach ($toOrd_data as $fabDescId => $fabDescId_data) 
													{
														foreach ($fabDescId_data as $gsm => $gsm_data) 
														{
															foreach ($gsm_data as $dia => $dia_data) 
															{
																foreach ($dia_data as $stitch_length => $row) 
																{
																	if ($i % 2 == 0)
																		$bgcolor = "#E9F3FF";
																	else
																		$bgcolor = "#FFFFFF";	

																		?>
											                            <tr bgcolor="<? echo $bgcolor; ?>">
											                                <td style="font-size: 15px"><? echo $i; ?></td>
											                                <td style="font-size: 15px">
											                                    <div style="word-wrap:break-word; width:120px;text-align: center;"><? 
											                                    echo $row['buyer'].'<br><br>'.$row['job']; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:120px;text-align: center;"><? 
											                                    echo $prog.'<br><br>'.$booking; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:120px;text-align: center;"><? 
											                                    echo $row['color'].'<br><br>'.$row['batch_no']; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['from_order_id']; ?>
											                                	</div>
											                                </td>
									 										<td style="font-size: 15px">
									 											<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['to_order_id']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $colorRange_arr[$row['color_range']]; ?>
											                                	</div>
											                                </td>
																			<td style="font-size: 15px">
																				<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $constructtion_arr[$row['feb_description_id']]; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $composition_arr[$row['feb_description_id']]; ?>
											                                	</div>
											                                </td>

											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px;text-align: center;"><? 
											                                    echo $row['gsm'].'<br/><br/>'.$row['dia_width']; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $yarn_count_details[$row['count']]; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $brand_details[$row['yarn_brand']]; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['yarn_lot']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['stitch_length']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px;text-align: center;">
											                                	<div style="word-wrap:break-word; width:100px"><? 
											                                    	echo $row['mc_dia'].'<br><br>'.$row['guage']; ?>
											                                    </div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $unit_of_measurement[$row['uom']]; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['qty_in_pcs']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; width:100px; text-align: center;">
											                                		<? echo $row['no_of_roll']; ?>
											                                	</div>
											                                </td>
											                                <td style="font-size: 15px">
											                                	<div style="word-wrap:break-word; text-align: center;">
											                                		<? echo $row['transfer_qnty'];  ?>
											                                	</div>
											                                </td>	

											                               
											                            </tr>
																		<?
																		$i++;
																		$fab_tot_qty_in_pcs+=$row['qty_in_pcs'];
																		$fab_tot_no_of_roll+=$row['no_of_roll'];
																		$fab_tot_transfer_qnty+=$row['transfer_qnty'];
																}
																			
															}
																		
														}
																	
													}
																
												}
															
											}	
														
										}			
									}			
								?>			
									<tr class="tbl_bottom">
										<td colspan="16" style=" text-align:right;font-size: 14px;"><strong>Total</strong></td>
										<td align="right" style="font-size: 14px;">
											<b><? echo number_format($fab_tot_qty_in_pcs, 2, '.', ''); ?></b>
										</td>
										<td align="right" style="font-size: 14px;"><? echo $fab_tot_no_of_roll; ?></td>
										<td align="right" style="font-size: 14px;"><? echo number_format($fab_tot_transfer_qnty, 2, '.', ''); ?></td>
									</tr>			
											
											
									
		                    </tbody>	
	                    </table>
                    	<?
                	} // summary Start
                    ?>
				</div>
				<br>
				<? echo signature_table(72, $company, "1200px"); ?>
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
				$("#barcode_img_id").html('11');
				value = {code: value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');
		
			
		</script>
        <div style="page-break-after:always;"></div>
    	<?php
	
    exit();
}


?>

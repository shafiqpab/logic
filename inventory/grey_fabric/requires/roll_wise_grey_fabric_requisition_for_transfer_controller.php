<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
$company_id = $userCredential[0][csf('company_id')];
$supplier_id = $userCredential[0][csf('supplier_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];
$item_cate_id = $userCredential[0][csf('item_cate_id')];
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}


if($action=="load_drop_store_from")
{
	$data_ref= explode("_", $data);
	
	echo create_drop_down( "cbo_store_name_from", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data_ref[1] $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "store_on_change(this.value)" );
}

if($action=="load_drop_store")
{
	$data= explode("_", $data);
	echo create_drop_down( "cbo_to_store", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[1] $store_location_credential_cond and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "store_on_change(this.value);" );
}
if($action=="load_drop_store_balnk")
{
	echo create_drop_down( "cbo_to_store", 152, $blank_array,"", 1, "--Select store--", 0, "" );
}

if($action=="check_report_button")
{
	$sql="select format_id from lib_report_template where template_name='".trim($data)."' and report_id=199 and is_deleted=0 and status_active=1";
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

/*if($action=="load_drop_store")
{
	// print_r($data);
	$data= explode("_", $data);
	if ($data[0]==1 && $data[2]>0)
	{	
		echo create_drop_down( "cbo_to_store", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[2] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	}
	else if (($data[0]==2 || $data[0]==4) && $data[2]==0)
	{
		echo create_drop_down( "cbo_to_store", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=13 and a.company_id=$data[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_to_store", 160, $blank_array,"", 1, "--Select store--", 0, "" );
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
		$new_transfer_system_id = explode("*", return_next_id_by_sequence("ITEM_TRAN_REQU_MST_PK_SEQ", "inv_item_transfer_requ_mst",$con,1,$cbo_company_id,'GFTRE',339,date("Y",time()),13 ));
		$data_array="(".$id.",339,'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_to_company_id.",0,0,".$cbo_item_category.",".$txt_remarks.",".$cbo_ready_to_approved.",".$cbo_store_name_from.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

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
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$colorId="colorId_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;
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
				$constructCompo="constructCompo_".$j;
				$rollAmount="rollAmount_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
				$toColorId="toColorId_".$j;

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
					{$toOrderIdRef=$$orderId;}
				else
					{$toOrderIdRef=$$toOrderId;}	
				if ($$toColorId=="") 
				{
					$toColorId=0;
				}
				else{
					$toColorId=$$toColorId;
				}			

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",0,0,'".$$productId."',0,'".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".str_replace(" ","0",$$rack)."','".$$shelf."','".$$fromStoreId."',".$cbo_to_store.",".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$barcodeNo."','".$$rollId."','".$$rollNo."',339,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$toColorId.")";
				
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
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$colorId="colorId_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;
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
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
				$toColorId="toColorId_".$j;

				/*
				|--------------------------------------------------------------------------
				| inv_item_transfer_requ_dtls
				| data preparing here
				| $data_array_dtls
				|--------------------------------------------------------------------------
				|
				*/
				if($$toOrderId=="")
					{$toOrderIdRef=$$orderId;}
				else
					{$toOrderIdRef=$$toOrderId;}
				if ($$toColorId=="") 
				{
					$toColorId=0;
				}
				else{
					$toColorId=$$toColorId;
				}
				
				$dtls_id = return_next_id_by_sequence("ITEM_TRAN_REQU_DTLS_PK_SEQ", "inv_item_transfer_requ_dtls", $con);
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",0,0,'".$$productId."','".$$productId."','".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".str_replace(" ","0",$$rack)."','".$$shelf."','".$$fromStoreId."',".$cbo_to_store.",".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$barcodeNo."','".$$rollId."','".$$rollNo."',339,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$toColorId.")";
				
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
		$field_array="id, entry_form, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_order_id, to_order_id, item_category, remarks, ready_to_approve,from_store_id, inserted_by, insert_date";
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
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id, feb_description_id, machine_no_id, stitch_length, yarn_lot, y_count, brand_id, rack, shelf, from_store, to_store, item_category, transfer_qnty, knit_program_id, prod_detls_id, from_trans_entry_form, from_booking_without_order, gsm, dia_width, barcode_no, roll_id, roll, entry_form, inserted_by, insert_date,qty_in_pcs,to_color_id";
		$rID2=sql_insert("inv_item_transfer_requ_dtls",$field_array_dtls,$data_array_dtls,0);
		// echo "10** insert into inv_item_transfer_requ_dtls ($field_array_dtls) values $data_array_dtls";die;
	  	// echo "10**".$rID."&&".$rID2;die;

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

		$field_array="transfer_date*challan_no*remarks*ready_to_approve*from_store_id*updated_by*update_date";
		$data_array=$txt_transfer_date."*".$txt_challan_no."*".$txt_remarks."*".$cbo_ready_to_approved."*".$cbo_store_name_from."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, from_order_id, to_order_id, feb_description_id, machine_no_id, stitch_length, yarn_lot, y_count, brand_id, rack, shelf, from_store, to_store, item_category, transfer_qnty, knit_program_id, prod_detls_id, from_trans_entry_form, from_booking_without_order, gsm, dia_width, barcode_no, roll_id, roll, entry_form, inserted_by, insert_date,qty_in_pcs,to_color_id";
		
		$field_array_dtls_up="from_order_id*to_order_id*to_color_id*to_store*knit_program_id*prod_detls_id*from_trans_entry_form*gsm*dia_width*updated_by*update_date";
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
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$colorId="colorId_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;
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
				$constructCompo="constructCompo_".$j;
				$rollAmount="rollAmount_".$j;
				$fromBookingWithoutOrder="fromBookingWithoutOrder_".$j;
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;			
				$toColorId="toColorId_".$j;			
			
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
					if ($$toColorId=="") 
					{
						$toColorId=0;
					}
					else{
						$toColorId=$$toColorId;
					}
					$dtls_id_array_up[]=$$dtlsId;
					$data_array_dtls_up[$$dtlsId]=explode("*",($$orderId."*".$toOrderIdRef."*".$toColorId."*".$cbo_to_store."*'".$$progBookPiId."'*'".$$knitDetailsId."'*'".$$transferEntryForm."'*'".$$gsm."'*'".$$diaWidth."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
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
						{$data_array_dtls.=",";}
					if ($$toColorId=="") 
					{
						$toColorId=0;
					}
					else{
						$toColorId=$$toColorId;
					}
					$data_array_dtls.="(".$dtls_id.",".$update_id.",0,0,'".$$productId."',0,'".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".str_replace(" ","0",$$rack)."','".$$shelf."','".$$fromStoreId."',".$cbo_to_store.",".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$barcodeNo."','".$$rollId."','".$$rollNo."',339,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$toColorId.")";

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
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$colorId="colorId_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$rollNo="rollNo_".$j;
				$fromStoreId="fromStoreId_".$j;
				$toOrderId="toOrderId_".$j;
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
				$toColorId="toColorId_".$j;
				
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
					if ($$toColorId=="") 
					{
						$toColorId=0;
					}
					else{
						$toColorId=$$toColorId;
					}
					$dtls_id_array_up[]=$$dtlsId;
					$data_array_dtls_up[$$dtlsId]=explode("*",($$orderId."*".$toOrderIdRef."*".$toColorId."*".$cbo_to_store."*'".$$progBookPiId."'*'".$$knitDetailsId."'*'".$$transferEntryForm."'*'".$$gsm."'*'".$$diaWidth."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
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
						{$data_array_dtls.=",";}
					if ($$toColorId=="") 
					{
						$toColorId=0;
					}
					else{
						$toColorId=$$toColorId;
					}
					$data_array_dtls.="(".$dtls_id.",".$update_id.",0,0,'".$$productId."','".$$productId."','".$$orderId."','".$toOrderIdRef."','".$$febDescripId."','".$$machineNoId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".str_replace(" ","0",$$rack)."','".$$shelf."','".$$fromStoreId."',".$cbo_to_store.",".$cbo_item_category.",'".$$rollWgt."','".$$progBookPiId."','".$$knitDetailsId."','".$$transferEntryForm."','".$$fromBookingWithoutOrder."','".$$gsm."','".$$diaWidth."','".$$barcodeNo."','".$$rollId."','".$$rollNo."',339,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",".$toColorId.")";

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

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($txt_deleted_barcode) and entry_form = 82 order by barcode_no");
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

	//$issue_roll_mst_arr=return_library_array( "SELECT a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b  where a.mst_id=b.id and a.entry_form=61 and a.barcode_no in($bar_code)",'barcode_no','issue_number');

	$scanned_barcode_issue_data=sql_select("SELECT a.id, a.barcode_no,a.entry_form, b.issue_number 
	from pro_roll_details a, inv_issue_master b 
	where a.mst_id = b.id and b.entry_form = 61 and a.entry_form =61 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($bar_code)");

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
    where a.id=b.mst_id and a.entry_form in(339) and a.status_active=1 and a.is_deleted=0 and b.mst_id=$sys_id");

	if($sys_id != "")
	{
		$scanned_barcode_update_data=sql_select("SELECT b.barcode_no, b.roll_id, a.transfer_system_id, a.entry_form 
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
		where a.id=b.mst_id and a.entry_form=339 and b.entry_form=339 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.mst_id=$sys_id");
		foreach($scanned_barcode_update_data as $row)
		{
			$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
			$transfer_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('transfer_system_id')];
		}
	}
	// print_r($scanned_barcode_issue_array);die;

	$order_to_order_trans_sql=sql_select("SELECT a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order 
		from pro_roll_details a where a.entry_form in(83,82,58) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0 and a.barcode_no in($bar_code)");
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
	where a.id = b.mst_id and a.entry_form=82 and b.id=c.dtls_id and c.entry_form in(82) 
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

	$issue_return_sql=sql_select("SELECT b.company_id, a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order, b.store_id, c.floor_id, c.room, c.rack, c.self, c.bin_box, c.prod_id, c.body_part_id from pro_roll_details a, inv_receive_master b, pro_grey_prod_entry_dtls c where a.mst_id=b.id and a.dtls_id=c.id and b.id=c.mst_id and a.entry_form in(84) and b.entry_form in(84) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.re_transfer=0 and a.barcode_no in ($bar_code)");
	foreach($issue_return_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"]=$row[csf("id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"]=$row[csf("booking_without_order")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("company_id")];				
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"]=$row[csf("prod_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"]=$row[csf("store_id")];
	}
	unset($issue_return_sql);
	
	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id, c.qc_pass_qnty_pcs
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code) 
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id, c.qc_pass_qnty_pcs");


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
		if(!empty($program_with_order_arr))
		{
			$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val) 
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
		}
		else
		{
			$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 and po_break_down_id in (".implode(",", $po_arr_book_booking_arr).") ",'po_break_down_id','booking_no');
		}

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
				else if($row[csf("entry_form")]==58) 
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
				if($entry_form == 82)
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

					if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
					{
						$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
						$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("po_breakdown_id")]][$plan_id];
					}
					else
					{
						$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
					}
				}
				
				if($entry_form == 82 || $entry_form == 83)
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

				$barcodeData=$row[csf('id')]."**".$barcode_company_id."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$to_store."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$to_prod_id."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$po_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$po_id]['job_no']."**".$po_details_array[$po_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$po_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$to_store]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$booking_without_order."**".($row[csf("qc_pass_qnty_pcs")]*1)."**".$po_details_array[$po_id]['grouping'];//$row[csf("roll_mst_id")];				
			}
			else
			{
				if($scanned_barcode_entry_form_array[$row[csf('barcode_no')]]==339)
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
	
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);
	
	/*$scanned_barcode_update_data=sql_select("select c.company_id, a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, b.from_booking_without_order from pro_roll_details a, inv_item_transfer_dtls b , inv_item_transfer_mst c  where a.dtls_id=b.id and b.mst_id = c.id and c.entry_form = 82 and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id");*/

	$scanned_barcode_update_data=sql_select("SELECT a.company_id, b.id as roll_upid, b.to_order_id, b.barcode_no, b.id as dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, b.from_booking_without_order, b.roll_id, b.to_color_id
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
		where a.id=b.mst_id and b.entry_form=339 and a.entry_form=339 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.mst_id=$sys_id");
	foreach($scanned_barcode_update_data as $row)
	{
		$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('to_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_color_id']=$row[csf('to_color_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form']=$row[csf('from_trans_entry_form')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_store']=$row[csf('to_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_store']=$row[csf('from_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']=$row[csf('to_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']=$row[csf('from_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_order_id']=$row[csf('from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];

		if($row[csf('from_booking_without_order')] == 1)
		{
			$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		else
		{
			$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
	}
	
	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id as roll_mst_id, c.qc_pass_qnty_pcs
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id, c.qc_pass_qnty_pcs");
	
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
			$nxtProcessSql = sql_select("SELECT a.id,a.barcode_no,a.roll_no from  pro_roll_details a where a.barcode_no in (".$splited_barcode.") and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1");
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

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($splited_barcode) and entry_form = 82 order by barcode_no");
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
		if(!empty($program_with_order_arr))
		{
			$book_booking_sql=sql_select("SELECT a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val) 
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
		}
		else
		{
			$book_booking_arr=return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 and po_break_down_id in (". implode(',', $po_arr_book_booking_arr) .")",'po_break_down_id','booking_no');
		}
	}
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');

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
			else if($row[csf("entry_form")]==58) 
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
				if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
				{
					$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
					$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("po_breakdown_id")]][$plan_id];
				}
				else
				{
					$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
				}
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

			if($barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 82 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 83)
			{
				$booking_no_fab = $booking_no_fab . " (T)";
			}
			
			$store_id = $barcode_update_data[$row[csf('barcode_no')]]['from_store'];
			$entry_form = $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];
			
			$barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$store_id."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$from_order_id]['job_no']."**".$po_details_array[$from_order_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$from_order_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$store_id]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number']."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."**".($row[csf("qc_pass_qnty_pcs")]*1)."**".$po_details_array[$from_order_id]['grouping']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['job_no']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_color_id']."**".$color_name_arr[$barcode_update_data[$row[csf('barcode_no')]]['to_color_id']]."__";
			//$row[csf("roll_mst_id")]."__";				
			//$test_str .= $splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."__";
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
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_challan_search_list_view', 'search_div', 'roll_wise_grey_fabric_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
		$qry_plan=sql_select( "SELECT a.booking_no,b.id as progran_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no like '%$bookingNo%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach ($qry_plan as $row) 
		{
			$program_arr[$row[csf('progran_no')]]["progran_no"]=$row[csf('progran_no')];
			$programIds.="'".$row[csf('progran_no')]."'".",";
		}
		$programIds=chop($programIds,",");
	}
	
	if(!empty($program_arr)) $cond_for_booking=" and b.knit_program_id in($programIds)";

	$sql = "SELECT a.id, $year_field a.transfer_prefix_number, a.transfer_system_id, a.transfer_criteria, a.company_id, a.to_company, a.transfer_date, a.challan_no, max(b.to_store) as to_store 
	from  inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
	where a.id=b.mst_id and a.entry_form=339 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $criteria_cond $search_field_cond $date_cond $cond_for_booking 
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
	where a.id=b.mst_id and a.entry_form=339 and a.id=$data 
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
		echo "load_drop_down( 'requires/roll_wise_grey_fabric_requisition_for_transfer_controller','".$row[csf("transfer_criteria")].'_'.$to_company_id."', 'load_drop_store', 'store_td' );\n";
		echo "$('#cbo_to_store').val(".$row[csf("to_store")].");\n";

		echo "load_drop_down( 'requires/roll_wise_grey_fabric_requisition_for_transfer_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_store_from', 'from_store_td' );\n";
		echo "$('#cbo_store_name_from').val(".$row[csf("from_store")].");\n";
		echo "$('#cbo_store_name_from').attr('disabled','true')".";\n";

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
			// echo "$('#approved').text('Partial Approved');\n";
			$company_id=$row[csf("company_id")];
			$approval_allow=sql_select("select b.id, b.page_id, b.approval_need, b.allow_partial, b.validate_page,a.setup_date from approval_setup_mst a,approval_setup_dtls b where a.id=b.mst_id and a.company_id='$company_id' and a.status_active=1 and b.page_id=42 and b.status_active=1 and b.is_deleted=0 order by b.id desc ");
					if($approval_allow[0][csf("approval_need")]==1 && $approval_allow[0][csf("allow_partial")]==1){
						// $ap_msg="This Job Is Approved.";
						echo "$('#approved').text('This Job Is Approved.');\n";
					}else{
						// $ap_msg="This Job Partially Approved.";
						echo "$('#approved').text('This Job Partially Approved.');\n";
					}
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
	$barcode_nos_sql=sql_select("SELECT barcode_no as barcode_nos FROM inv_item_transfer_requ_dtls WHERE entry_form=339 and status_active=1 and is_deleted=0 and mst_id=$data");
	foreach ($barcode_nos_sql as $key => $row) 
	{
		if($row[csf("barcode_nos")]!="") $all_barcode.=$row[csf("barcode_nos")].",";
	}
	$barcode_nos=implode(",",array_unique(explode(",",chop($all_barcode,","))));
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
			var page_link='roll_wise_grey_fabric_requisition_for_transfer_controller.php?action=item_desc_popup&company_id='+company_id+'&job_no='+job_no+'&order_no='+order_no+'&file_no='+file_no+'&ref_no='+ref_no+'&barcode_no='+barcode_no+'&booking_no='+booking_no;

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
					$('#hidden_po_id').val( fabric_info[2] );
				}
			}
		}
    </script>

	</head>

	<body>
	<div align="center" style="width:760px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:760px; margin-left:2px;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" width="650" border="1" rules="all" class="rpt_table">
	                <thead>
	                	<tr>
		                	<th width="150" colspan="4"> </th>
		                    <th><? echo create_drop_down( "cbo_string_search_type", 100, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
		                	<th width="150" colspan="7"> </th>
		                </tr>
	                    <th>Location</th>
	                    <th>Job Year</th>
	                    <th>Job</th>
	                    <th>Order No</th>
	                    <th>File No</th>
	                    <th>Internal Ref No</th>
	                    <th>Barcode No</th>
	                    <th>Booking No</th>
	                    <th>Fabric Description</th>
	                    <th>Color Range</th>
	                    <th>Quantity</th>
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
	                    <td> 
                            <?
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
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
	                    <td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:50" class="text_boxes" /></td> 
	                    <td align="center">				
	                        <input type="text" style="width:60" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />	
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_fab_desc" id="txt_fab_desc" onDblClick="openmypage_fabric();" placeholder="Dubble Click For Item" readonly/>
	                        <input type="hidden" name="txt_product_id" id="txt_product_id"/>
	                        <input type="hidden" name="hidden_po_id" id="hidden_po_id"/>
	                    </td>
	                    <td align="center">
	                        <?
							echo create_drop_down( "cbo_color_range", 132, $color_range,"",1, "-- Select --", 0, "" );
							?>	
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:80px" class="text_boxes"  name="txt_qty" id="txt_qty" />	
	                    </td>
	                       			
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_to_store; ?>+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_product_id').value+'_'+document.getElementById('cbo_color_range').value+'_'+<? echo $productId; ?>+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_year').value+'_'+<? echo $cbo_store_name_from; ?>+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('hidden_po_id').value, 'create_barcode_search_list_view', 'search_div', 'roll_wise_grey_fabric_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
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
	$transfer_cateria =trim($data[6]);
	$store_id=trim($data[7]);
	$bookingNo=trim($data[8]);
	$hiden_product_id=trim($data[9]);
	$cbo_color_range=trim($data[10]);
	$productId=trim($data[11]);
	$txt_job_no=trim($data[12]);
	$cbo_year=trim($data[13]);
	$from_store=trim($data[14]);
	$search_type=trim($data[15]);
	$hidden_po_id=trim($data[16]);

	$job_cond=""; $order_cond=""; $booking_cond="";
	if($search_type==1)
	{
		if (str_replace("'","",$txt_job_no)!="") $job_cond=" and d.job_no_mst='$txt_job_no'";
		if (str_replace("'","",$order_no)!="") $order_cond=" and d.po_number = '$order_no'  ";
		if (trim($bookingNo)!="") $booking_cond=" and b.booking_no ='$bookingNo'";
	}
	else if($search_type==2)
	{
		if (str_replace("'","",$txt_job_no)!="") $job_cond=" and d.job_no_mst like '$txt_job_no%'";
		if (str_replace("'","",$order_no)!="") $order_cond=" and d.po_number like '$order_no%'  ";
		if (trim($bookingNo)!="") $booking_cond=" and b.booking_no like '$bookingNo%'  ";
	}
	else if($search_type==3)
	{
		if (str_replace("'","",$txt_job_no)!="") $job_cond=" and d.job_no_mst like '%$txt_job_no'";
		if (str_replace("'","",$order_no)!="") $order_cond=" and d.po_number like '%$order_no'  ";
		if (trim($bookingNo)!="") $booking_cond=" and b.booking_no like '%$bookingNo'";
	}
	else if($search_type==4 || $search_type==0)
	{
		if (str_replace("'","",$txt_job_no)!="") $job_cond=" and d.job_no_mst like '%$txt_job_no%'";
		if (str_replace("'","",$order_no)!="") $order_cond=" and d.po_number like '%$order_no%'  ";
		if (trim($bookingNo)!="") $booking_cond=" and b.booking_no like '%$bookingNo%'";
	}

	if($db_type==0)
	{
		if($cbo_year!=0) $job_year_cond=" and year(d.insert_date)=$cbo_year"; else $job_year_cond="";
	}
	else if($db_type==2)
	{
		if($cbo_year!=0) $job_year_cond=" and TO_CHAR(d.insert_date,'YYYY')=$cbo_year"; else $job_year_cond="";
	}

	if( $txt_job_no=='' && $order_no=='' && $file_no=='' && $ref_no=='')
	{
		echo "Select Order No/File No/Internal Ref No";	die;
	}

	if (trim($hidden_po_id)!="") $po_id_cond=" and d.id=$hidden_po_id";
	// echo $po_id_cond;die;

	$search_field_cond="";
	// if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	// if($txt_job_no!="") $search_field_cond.=" and d.job_no_mst like '%$txt_job_no%'";

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
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form in(61) and is_returned=0 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	if($bookingNo !="")
	{
		//"query from plan to get program_no";
		$program_arr=array();$programIds="";
		$qry_plan=sql_select( "SELECT b.booking_no,c.id as progran_no from ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c where b.id=c.mst_id $booking_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

		foreach ($qry_plan as $row) {
			$program_arr[$row[csf('progran_no')]]["progran_no"]=$row[csf('progran_no')];
			$programIds.="'".$row[csf('progran_no')]."'".",";

		}
		$programIds=chop($programIds,",");
	}

	$po_sql = sql_select("SELECT d.id as po_id, b.booking_no from wo_po_break_down d, wo_booking_dtls b where d.id = b.po_break_down_id and d.status_active = 1 and b.status_active = 1 $booking_cond $job_cond $order_cond $job_year_cond");


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
	// echo $cbo_color_range;die;
	if ($cbo_color_range !=0)
	{
		$fabric_info_sql = sql_select("SELECT c.barcode_no, c.receive_basis, c.booking_no, b.stitch_length, b.yarn_lot, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.color_range_id
		from pro_roll_details c, pro_grey_prod_entry_dtls b
		where c.dtls_id=b.id and c.entry_form=2 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 $color_range_cond $all_po_cond");
		$fabric_ref_arr=array();
		foreach ($fabric_info_sql as $row)
		{
			$fabric_ref_arr[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
		}

		$fabric_ref_arr = array_filter(array_unique($fabric_ref_arr));
		if(count($fabric_ref_arr)>0)
		{
			$all_fabric_barcode = implode(",", $fabric_ref_arr);
			$all_fab_barcode_cond=""; $barcodeCond=""; 
			if($db_type==2 && count($fabric_ref_arr)>999)
			{
				$barcode_arr_chunk=array_chunk($fabric_ref_arr,999) ;
				foreach($barcode_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$barcodeCond.=" c.barcode_no in($chunk_arr_value) or ";	
				}
				
				$all_fab_barcode_cond.=" and (".chop($barcodeCond,'or ').")";	
			}
			else
			{
				$all_fab_barcode_cond=" and c.barcode_no in($all_fabric_barcode)";	 
			}
		}

		if (count($fabric_ref_arr) == 0) 
		{
			echo "Data not found"; die;
		}
	}
	// echo $all_fab_barcode_cond;die;
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$buyer_arr=return_library_array( "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name",'id','buyer_name');
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
	$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
	// if($store_id>0) $store_cond=" and a.store_id!=$store_id"; else $store_cond="";
	$store_cond="";
	if($from_store>0) 
	{
		$store_cond=" and a.store_id=$from_store";
		$store_cond2=" and b.to_store=$from_store";
	}

	$sql= "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,a.store_id as to_store, b.gsm, b.width as dia, d.status_active, e.buyer_name as buyer_id
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 $job_cond $order_cond $barcode_cond $location_cond $barcode_cond_for_booking $prod_id_cond3 $prod_id_cond $all_fab_barcode_cond $store_cond $job_year_cond $po_id_cond
	UNION ALL
	SELECT a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, b.gsm , b.dia_width as dia, d.status_active, e.buyer_name as buyer_id
	FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d, wo_po_details_master e 
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.entry_form in(83) and c.entry_form in(83) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 and a.to_company = $company_id $barcode_cond $all_po_cond $prod_id_cond4 $prod_id_cond2 $all_fab_barcode_cond $store_cond2 $job_year_cond  $job_cond $order_cond $po_id_cond
	UNION ALL
	SELECT a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form,b.to_store, b.gsm , b.dia_width as dia, d.status_active, e.buyer_name as buyer_id
	FROM inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d, wo_po_details_master e 
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and d.job_id=e.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 and a.transfer_criteria in (1,2,4) and a.to_company = $company_id $barcode_cond $all_po_cond $prod_id_cond4 $prod_id_cond2 $all_fab_barcode_cond $store_cond2 $job_year_cond  $job_cond $order_cond $po_id_cond 
	UNION ALL 
	select a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty_pcs, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form, a.store_id as to_store, 
	b.gsm , b.width as dia, d.status_active, e.buyer_name as buyer_id 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, wo_po_details_master e 
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and d.job_id=e.id and a.company_id=1 and b.trans_id<>0 and a.entry_form in(84) and c.entry_form in(84) and c.status_active=1 and c.is_deleted=0 
	and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 and a.company_id = $company_id $barcode_cond $all_po_cond $prod_id_cond4 $prod_id_cond $all_fab_barcode_cond $store_cond $job_year_cond  $job_cond $order_cond $po_id_cond 
	order by prod_id, qnty"; // order by  qnty

	// echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $row) 
	{
		$recv_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}
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
		$stitch_lot_sql = sql_select("SELECT a.barcode_no, a.receive_basis, a.booking_no,  b.stitch_length, b.yarn_lot, b.yarn_count, b.width, b.body_part_id, b.color_id, b.color_range_id, b.gsm from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id = b.id and a.entry_form = 2 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");
		foreach ($stitch_lot_sql as $row)
		{
			$production_ref_arr[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
			$production_ref_arr[$row[csf("barcode_no")]]['yarn_count'] = $row[csf("yarn_count")];
			$production_ref_arr[$row[csf("barcode_no")]]['width'] = $row[csf("width")];
			$production_ref_arr[$row[csf("barcode_no")]]['gsm'] = $row[csf("gsm")];
			$production_ref_arr[$row[csf("barcode_no")]]['body_part_id'] = $row[csf("body_part_id")];
			$production_ref_arr[$row[csf("barcode_no")]]['receive_basis'] = $row[csf("receive_basis")];
			$production_ref_arr[$row[csf("barcode_no")]]['booking_no'] = $row[csf("booking_no")];
			$production_ref_arr[$row[csf("barcode_no")]]['color_id'] = $color_name_arr[$row[csf("color_id")]];
			$production_ref_arr[$row[csf("barcode_no")]]['color_range_id'] = $color_range[$row[csf("color_range_id")]];
		}
	}
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Body Part</th>
            <th width="30">Dia</th>
            <th width="30">Gsm</th>
            <th width="60">Color</th>
            <th width="60">Color Range</th>
            <th width="100">Lot</th>
            <th width="100">Yarn Count</th>
            <th width="50">Stitch Ln.</th>
            <th width="50">Program No</th>
            <th width="100">Buyer</th>
            <th width="70">Job No</th>
            <th width="110">Order No</th>
            <th width="90">Barcode No</th>
            <th width="50">Roll No</th>
            <th width="50">Roll Qty.</th>
            <th width="50">Qty. In Pcs</th>
            <th width="70">File NO</th>
            <th width="70">Ref No</th>
            <th width="70">Shipment Date</th>
            <th width="110">Location</th>
            <th width="100">Store</th>
            <th>Status</th>
        </thead>
	</table>
	<div style="width:1810px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1790" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					$trans_flag = "";
					if($row[csf('entry_form')] == 82 || $row[csf('entry_form')] == 83)
					{
						$trans_flag = " (T)";
					}

					$stitch_length = $production_ref_arr[$row[csf("barcode_no")]]['stitch_length'];
					$yarn_lot = $production_ref_arr[$row[csf("barcode_no")]]['yarn_lot'];
					$yarnCount = $production_ref_arr[$row[csf("barcode_no")]]['yarn_count'];
					$dia_width = $production_ref_arr[$row[csf("barcode_no")]]['width'];
					$gsm = $production_ref_arr[$row[csf("barcode_no")]]['gsm'];
					$body_part_id = $production_ref_arr[$row[csf("barcode_no")]]['body_part_id'];
					$receive_basis = $production_ref_arr[$row[csf("barcode_no")]]['receive_basis'];
					$colorName = $production_ref_arr[$row[csf("barcode_no")]]['color_id'];
					$colorRange = $production_ref_arr[$row[csf("barcode_no")]]['color_range_id'];
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
						<td width="150" title="<? echo $row[csf('prod_id')]; ?>"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $body_part[$body_part_id]; ?></p></td>
						<td width="30"><p><? echo $dia_width; ?></p></td>
						<td width="30"><p><? echo $gsm; ?></p></td>
						<td width="60"><p><? echo $colorName; ?></p></td>
						<td width="60"><p><? echo $colorRange; ?></p></td>
						<td width="100"><p><? echo $yarn_lot; ?></p></td>
						<td width="100"><p><? echo $all_count; ?></p></td>
						<td width="50"><p><? echo $stitch_length; ?></p></td>
						<td width="50"><p><? echo $program_no; ?></p></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="70"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
						<td width="90"><p><? echo $row[csf('barcode_no')].$trans_flag; ?>&nbsp;</p></td>
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
    <table width="1810">
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
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $company_id; ?>'+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $job_no; ?>'+'**'+'<? echo $order_no; ?>'+'**'+'<? echo $file_no; ?>'+'**'+'<? echo $ref_no; ?>'+'**'+'<? echo $barcode_no; ?>'+'**'+'<? echo $booking_no; ?>', 'create_product_search_list_view', 'search_div', 'roll_wise_grey_fabric_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	// print_r($data);
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
		$qry_plan=sql_select( "select a.booking_no,b.id as progran_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no like '%$bookingNo%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach ($qry_plan as $row) {
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

	$sql= "SELECT e.id, d.id as po_id, d.po_number, a.entry_form, e.product_name_details 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d, product_details_master e 
	where a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id = d.id and b.prod_id=e.id and a.company_id = $company_id and b.trans_id <> 0 and a.entry_form in (2, 22, 58) and c.entry_form in (2, 22, 58) and c.status_active = 1 and c.is_deleted = 0 and c.roll_no > 0 and c.is_sales <> 1 and c.re_transfer = 0 $search_field_cond $barcode_cond $barcode_cond_for_booking $sql_cond
	group by e.id, d.id, d.po_number, a.entry_form, e.product_name_details 
	UNION ALL 
	SELECT e.id, d.id as po_id, d.po_number, a.entry_form, e.product_name_details 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, product_details_master e 
	where     a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id = d.id and b.to_prod_id=e.id and a.entry_form in (83) and c.entry_form in (83) and c.re_transfer = 0 and c.status_active = 1 and c.is_deleted = 0 and c.is_sales <> 1 and c.re_transfer = 0 $barcode_cond $all_po_cond $sql_cond
	group by e.id, d.id, d.po_number, a.entry_form, e.product_name_details 
	UNION ALL 
	SELECT e.id, d.id as po_id, d.po_number, a.entry_form, e.product_name_details 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, product_details_master e 
	where a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id = d.id and b.to_prod_id=e.id and a.entry_form in (82) and c.entry_form in (82) and c.re_transfer = 0 and c.status_active = 1 and c.is_deleted = 0 and c.is_sales <> 1 and c.re_transfer = 0 and a.transfer_criteria = 1  and a.company_id = $company_id  $barcode_cond $all_po_cond $sql_cond
	group by e.id, d.id, d.po_number,  a.entry_form, e.product_name_details 
	UNION ALL 
	SELECT e.id, d.id as po_id, d.po_number, a.entry_form, e.product_name_details 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, wo_po_break_down d, product_details_master e 
	where a.id = b.mst_id and b.id = c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in (82) and c.entry_form in (82) and c.re_transfer = 0 and c.status_active = 1 and c.is_deleted = 0 and c.is_sales <> 1 and c.re_transfer = 0 and a.transfer_criteria = 2 and a.company_id = $company_id  $barcode_cond $all_po_cond $sql_cond
	group by e.id, d.id, d.po_number, a.entry_form, e.product_name_details order by id";

	// echo $sql;


	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');



	$fabric_info_sql = sql_select("SELECT b.prod_id, b.color_id, b.color_range_id
	from pro_roll_details c, pro_grey_prod_entry_dtls b
	where c.dtls_id=b.id and c.entry_form=2 and c.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.is_deleted=0 $all_po_cond $barcode_cond
	group by b.prod_id, b.color_id, b.color_range_id");
	$color_name_arr2=array(); $color_range2=array();
	foreach ($fabric_info_sql as $row)
	{
		$color_name_arr2[$row[csf("prod_id")]] = $color_name_arr[$row[csf("color_id")]];
		$color_range2[$row[csf("prod_id")]] = $color_range[$row[csf("color_range_id")]];
	}

	//$sql = "select id,product_name_details,gsm,dia_width from product_details_master where company_id=$company_id and item_category_id=13 $sql_cond";

	$arr=array(2=>$color_name_arr2,3=>$color_range2);
	echo create_list_view("tbl_list_search", "Product Id,Product Details,Fabric Color,Color Range,Po Number", "80,250,80,80,80","650","260",0, $sql , "js_set_value", "id,product_name_details,po_id", "", 1, "0,0,id,id,0", $arr , "id,product_name_details,id,id,po_number", "",'','0,0','',0);

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
	<div align="center" style="width:980px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:970px;margin-left:10px">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="900" class="rpt_table">
					<thead>
						<th>Buyer Name</th>
						<th>Order No</th>
						<th>Job No</th>
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
						<td>
							<input type="text" style="width:150px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
						</td>
						<td>
							<input type="text" style="width:80px;" class="text_boxes" name="txt_job_no" id="txt_job_no" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px" placeholder="To Date" readonly>
						</td>
						<td>
							<input name="txt_booking_no" id="txt_booking_no" class="text_boxes" style="width:80px" placeholder="Booking No">
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_to_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $item_desc_ids; ?>+'_'+<? echo $item_gsm; ?>+'_'+<? echo $item_dia; ?>+'_'+document.getElementById('txt_job_no').value, 'create_po_search_list_view', 'search_div', 'roll_wise_grey_fabric_requisition_for_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	// print_r($data);
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
	$txt_job_no=trim($data[10]);;

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0   and b.status_active=1 and b.is_deleted=0";
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

	if($txt_job_no!="") $job_cond=" and a.job_no like '%$txt_job_no%'";

	$bookingNo= trim($data[5]);
	if($bookingNo!="") $booking_cond=" and c.booking_no like '%$bookingNo%'";

	/*if($fabric_construction!="") $fabric_construction_cond=" and c.construction in ('$fabric_construction')";
	if($fabric_composition!="") $fabric_composition_cond=" and c.copmposition like '%$fabric_composition'";
	if($item_gsm!="") $item_gsm_cond=" and c.gsm_weight in ($item_gsm)";*/
	if($fabric_construction!="") $fabric_construction_cond=" and d.construction in ('$fabric_construction')";
	if($fabric_composition!="") $fabric_composition_cond=" and d.composition like '%$fabric_composition'";
	if($item_gsm!="") $item_gsm_cond=" and d.gsm_weight in ($item_gsm)";
	if($item_dia!="") $item_dia_cond=" and c.dia_width in ('$item_dia')";
	// echo $fabric_composition_cond;die;

	$po_cond="";
	if($search_string!="")
	$po_cond=" and b.po_number ='$search_string'";

	/*// comment for c.gsm_weight, c.construction, c.copmposition data unavailable in wo_booking_dtls c table for sample with order
	$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, c.booking_no, sum(c.grey_fab_qnty) as required_qty
	from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_id and a.buyer_name like '$buyer' $po_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.booking_type in (1,4) $status_cond $shipment_date $booking_cond $year_field_cond $item_gsm_cond $fabric_construction_cond $fabric_composition_cond $job_cond 
	group by a.job_no,to_char(a.insert_date,'YYYY'), a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, c.booking_no order by b.id, b.pub_shipment_date";*/

	// use for Sample Fabric Booking -With order >  d.construction, d.composition, d.gsm_weight data available wo_pre_cost_fabric_cost_dtls d table sample with order and bulk order
	$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, c.booking_no, sum(c.grey_fab_qnty) as required_qty
	from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c,  wo_pre_cost_fabric_cost_dtls d
	where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.pre_cost_fabric_cost_dtls_id=d.id and a.company_name=$company_id and a.buyer_name like '$buyer' $po_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and c.booking_type in (1,4) $status_cond $shipment_date $booking_cond $year_field_cond $item_gsm_cond $fabric_construction_cond $fabric_composition_cond $job_cond 
	group by a.job_no,to_char(a.insert_date,'YYYY'), a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, c.booking_no order by b.id, b.pub_shipment_date";
	// $item_dia_cond 
	// echo $sql;//die;//, c.construction, c.copmposition, c.fabric_description, c.gsm_weight, c.dia_width 


	/*=IF THIS CONDITON VARIABLE $fabric_construction_cond $fabric_composition_cond PROBLEM FOUND TRY TO USE BELOW SQL=
	"SELECT a.job_no, to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id as po_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, 
	d.id as booking_dtls_id, d.booking_no, d.grey_fab_qnty as required_qty
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fabric_cost_dtls c, wo_booking_dtls d 
	where c.job_no=d.job_no and c.status_active=1 and c.is_deleted=0  and d.status_active = 1 and d.is_deleted = 0 and d.pre_cost_fabric_cost_dtls_id = c.id --and d.po_break_down_id =4412
	and a.job_no=b.job_no_mst and b.id=d.po_break_down_id  and d.gsm_weight in (380)  and c.LIB_YARN_COUNT_DETER_ID=364
	and d.booking_type =1  and a.company_name=1 and a.buyer_name like '%%' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0
	group by a.job_no,to_char(a.insert_date,'YYYY'), a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, d.id, d.booking_no, d.grey_fab_qnty 
	union all 
	select a.job_no, to_char(a.insert_date,'YYYY') as year, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id as po_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date, 
	c.id as booking_dtls_id, c.booking_no, c.grey_fab_qnty as required_qty
	from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_conv_cost_dtls d, wo_pre_cost_fabric_cost_dtls e, wo_booking_dtls c 
	where e.job_no=c.job_no  and d.is_deleted=0 and e.status_active=1 and c.is_deleted=0 and c.pre_cost_fabric_cost_dtls_id = d.id --and c.po_break_down_id =4412
	and a.job_no=b.job_no_mst and b.id=c.po_break_down_id  and c.gsm_weight in (380) and e.LIB_YARN_COUNT_DETER_ID=364
	and d.fabric_description = e.id and c.booking_type = 4  and a.company_name=1 and a.buyer_name like '%%' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0
	group by a.job_no,to_char(a.insert_date,'YYYY'), a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, c.id, c.booking_no, c.grey_fab_qnty ";*/
	

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$arr=array(2=>$company_arr,3=>$buyer_arr);
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,Req. Grey Fab,PO Quantity,Shipment Date,Booking No", "70,60,70,80,120,90,110,80,90,80,80","1030","200",0, $sql , "js_set_value", "id,po_number,job_no", "", 1, "0,0,company_name,buyer_name,0,0,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,required_qty,po_quantity,shipment_date,booking_no", "",'','0,0,0,0,0,1,0,1,1,3,0');
	
	exit();
}


if ($action=="to_color_popup")	
{	
	echo load_html_head_contents("Color Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
  	<script>
  	function js_set_value(id)
  	{
  		// alert(id);
		document.getElementById('color_id').value=id;
		parent.emailwindow.hide();
  	}
  	</script>
  	</head>	
	<body>
	    <div align="center" style="width:230px" >
		    <fieldset style="width:230px"> 
		        <form name="order_popup_1"  id="order_popup_1">
		            <?
		            if ($toOrderId!=0) $toOrderId_cond=" and d.id='$toOrderId'"; else { echo "Please Select To Order."; die; }
		            if ($toJobNo!="") $job_cond=" and d.job_no_mst='$toJobNo'";
		            // echo $job_cond; die;
					$sql="SELECT  b.color_id, b.color_range_id, d.id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, wo_po_break_down d
					where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.entry_form=2 and a.receive_basis=2 and c.entry_form=2 and a.item_category=13 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $toOrderId_cond $job_cond group by b.color_id, b.color_range_id, d.id";
		            // echo $sql;die;
		            $result = sql_select($sql);
		            $color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1 and is_deleted=0",'id','color_name');
		            //$arr=array (0=>$color_name_arr,1=>$color_range);
		            //echo  create_list_view ( "list_view", "Fabric Color,Color Range", "100,100","250","320",0, $sql, "js_set_value", "id,color_id", "", 1, "color_id,color_range_id", $arr , "color_id,color_range_id", "roll_wise_grey_fabric_requisition_for_transfer_controller", 'setFilterGrid("list_view",-1);','0,0' );
		            ?>
				    <div>
				        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="250" class="rpt_table">
				            <thead>
				                <th width="40">SL</th>
				                <th width="100">Fabric Color</th>
				                <th width="100">Color Range</th>
				            </thead>
				        </table>
				        <div style="width:250px; max-height:270px;overflow-y:scroll;" >     
				            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="230" class="rpt_table" id="tbl_po_list">
				                <?
				                $i=1;
				                foreach( $result as $row )
				                {
				                	if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                	$color_name=$color_name_arr[$row[csf("color_id")]];
				                    ?>  
				                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"  onclick="js_set_value('<? echo $row[csf("id")].'_'.$row[csf("color_id")].'_'.$color_name;?>');"> 
				                            <td width="40" align="center"><? echo $i; ?></td>
				                            <td width="100" align="center"><? echo $color_name; ?></td>
				                            <td align="center"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
				                        </tr>
				                    <? 
				                    $i++;
				                }
				                ?>
				            </table>
				        </div>
				    </div>
				    <?
		            ?>

		        <input type="hidden" id="color_id" />
		        </form>
		    </fieldset>
	    </div>
  	</body>
  	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  	</html>                                  
	<?	
}

if($action=="grey_issue_print")
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
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	
	/*$location_arr=return_library_array("select id, location_name from  lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');	
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");*/
	
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	
	$dataArray=sql_select("select id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no, remarks from inv_item_transfer_mst where id=$update_id");
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
             <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Report</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>Transfer ID :</strong></td>
                <td width="180"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
                <td width="130"><strong>Transfer Criteria:</strong></td>
                <td width="180"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
                <td width="130"><strong>Item Category:</strong></td>
                <td ><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Transfer Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
                <td ><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td ><strong>Remarks:</strong></td>
                <td ><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1900"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">From Store</th>
                <th width="100">To Store</th>
                <th width="50">File No</th>
                <th width="50">Reff No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="80">Production Basis</th>
                <th width="100">Delivery Challan No</th>
                <th width="90">Prog./ Book. No</th>
                <th width="80">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="100">Fab. Constraction</th>
                <th width="100">Fab. Compositon</th>
                <th width="50">Fin GSM</th>
                <th width="50">Fab. Dia</th>
                <th width="100">Count</th>
                <th width="100">Yarn Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="50">Stich Lenth</th>
                <th width="80">Machine No</th>
                <th width="60">MC Dia X Gauge</th>
                <th width="100">Barcode No</th>
                <th width="40">UOM</th>
                <th width="50">Roll No</th>
                <th>Transfered Qnty</th>
            </thead>
            <?
				$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');
				$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
				$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
				$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
				$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
				$i=1; $tot_qty=0; 
				$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
				$sql="SELECT a.id as mst_id, b.id as dtls_id, b.from_store, b.to_store, b.from_order_id, b.to_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.knit_program_id, b.prod_detls_id, b.from_trans_entry_form, b.gsm, b.dia_width, b.transfer_qnty as qnty, b.roll as roll_no, b.roll_id, b.barcode_no, b.to_order_id as po_breakdown_id
				from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
				where a.id=b.mst_id and a.id=$update_id and a.entry_form=339 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  order by b.roll";
            	//echo $sql;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				if($all_roll_id!="")
				{
					$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
					from inv_receive_master b, pro_grey_prod_entry_dtls c , pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.roll_id=0 and d.id in($all_roll_id)");
					
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("roll_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_dia"]=$row[csf("machine_dia")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_gg"]=$row[csf("machine_gg")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]=$row[csf("machine_no_id")];
						$production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]=$row[csf("yarn_lot")];

						if($production_delivery_data[$row[csf("roll_id")]]["receive_basis"] == 2)
						{
							$program_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
						}
					}
				}
				if(!empty($program_id_arr))
				{
					$booking_from_program = return_library_array("select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id  = b.mst_id and b.status_active = 1 and b.is_deleted = 0 and b.id in (".implode(",", $program_id_arr).") ","id","booking_no");
				}

				if($all_po_id!="")
				{
					$job_array=array();
					$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)";
					//echo $job_sql;
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
				}
				
				$i=0;
				foreach($result as $row)
				{
					$i++;
					?>
                    <tr>
                        <td align="center"><? echo $i; ?></td>
                        <td style="word-break:break-all;"><? echo $store_arr[$row[csf('from_store')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						if($row[csf('booking_without_order')]==1) echo "&nbsp;"; else echo $job_array[$row[csf('from_order_id')]]['file_no'] ;
						?></td>
                        
                        <td style="word-break:break-all;" align="center"><? if($row[csf('booking_without_order')]==1) echo "&nbsp;"; else echo $job_array[$row[csf('from_order_id')]]['grouping']; ?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						if($row[csf('booking_without_order')]==1) 
						{
							echo $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]]; 
						}
						else 
						{
							echo $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job']; 
						}
						?></td>
                        <td style="word-break:break-all;"><? echo $prod_basis_arr[$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? if($row[csf("from_trans_entry_form")]==58) echo $delivery_challan_arr[$row[csf("knit_program_id")]]; else echo "";?></td>
                        <td style="word-break:break-all;" align="left">
                        	<? 
                        	if($production_delivery_data[$row[csf("roll_id")]]["receive_basis"] == 2)
                        	{
                        		$program_no = $production_delivery_data[$row[csf("roll_id")]]["booking_no"];
                        		$booking_number = $booking_from_program[$program_no];
                        	}
                        	else
                        	{
                        		$booking_number = $production_delivery_data[$row[csf("roll_id")]]["booking_no"];
                        	}
                        	echo  "P: ".$program_no."<br />B: ".$booking_number;
                        	?>
                        </td>
                        <td style="word-break:break-all;" align="center">
						<? 
						$all_color_arr=array_unique(explode(",",$production_delivery_data[$row[csf("roll_id")]]["color_id"]));
						$all_color="";
						foreach($all_color_arr as $color_id)
						{
							$all_color.=$color_library[$color_id].",";
						}
						$all_color=chop($all_color,",");
						echo $all_color;
						?></td>
                        <td style="word-break:break-all;" align="center"><? echo $color_range[$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]]; ?></td>
                        <td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("gsm")]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("dia_width")]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						//echo $yarn_count_arr[$row[csf('y_count')]]; 
						$yarn_count_array=array_unique(explode(",",$row[csf('y_count')]));
						$all_count="";
						foreach($yarn_count_array as $count_id)
						{
							$all_count.=$yarn_count_arr[$count_id].",";
						}
						$all_count=chop($all_count,",");
						echo $all_count;
						?></td>
                        <td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
                        <td style="word-break:break-all;"><? echo $production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $lib_mc_arr[$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]]['no']; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						echo $production_delivery_data[$row[csf("roll_id")]]["machine_dia"].'X'.$production_delivery_data[$row[csf("roll_id")]]["machine_gg"];
						?></td>
                        <td style="word-break:break-all;"><?  echo $row[csf("barcode_no")]; ?></td>
                        <td align="center">Kg</td>
                        <td align="center"><? echo $row[csf("roll_no")];?></td>
                        <td align="right"><? echo number_format($row[csf('qnty')],2,'.','');?></td>
                    </tr>
                	<?
					$tot_qty+=$row[csf('qnty')];
				}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="23"><strong>Total</strong></th>
                    <th><? echo $i; ?></th>
                    <th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
		</table>
        <br>
        <?
        	echo signature_table(94, $company, "1900px");
        ?>
	</div>
	<?
	exit();
}

if($action=="grey_issue_print_2")
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
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	
	
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	
	
	$dataArray=sql_select("select id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no, remarks from inv_item_transfer_mst where id=$update_id");
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);
	
	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
             <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Report</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>Transfer ID :</strong></td>
                <td width="180"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
                <td width="130"><strong>Transfer Criteria:</strong></td>
                <td width="180"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
                <td width="130"><strong>Item Category:</strong></td>
                <td ><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Transfer Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
                <td ><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td ><strong>Remarks:</strong></td>
                <td ><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1900"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">Delivery Challan No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="90">Prog./ Book. No</th>
                <th width="100">Fab. Constraction</th>
                <th width="100">Fab. Compositon</th>
                <th width="50">Fin GSM</th>
                <th width="80">Fab Color</th>
                <th width="60">MC Dia X Gauge</th>
                <th width="50">Fab. Dia</th>
                <th width="50">Stich Length</th>
                <th width="100">Count</th>
                <th width="100">Yarn Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="80">Machine No</th>
                <th width="50">Total Roll</th>
                <th width="50">Transfered Qnty</th>
                <th width="100">From Store</th>
                <th width="100">To Store</th>
                
                
            </thead>
            <?
				$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');
				$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
				$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
				$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
				$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
				$i=1; $tot_qty=0; 
				$transfer_criteria=$dataArray[0][csf('transfer_criteria')];

				$sql = "SELECT a.id as mst_id, b.id as dtls_id, b.from_store, b.to_store, b.from_order_id, b.to_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.knit_program_id, b.prod_detls_id, b.from_trans_entry_form, b.gsm, b.dia_width, b.transfer_qnty as qnty, b.roll as roll_no, b.roll_id, b.barcode_no, b.from_booking_without_order as booking_without_order, b.to_order_id as po_breakdown_id
				from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
				where a.id=b.mst_id and a.id=$update_id and a.entry_form=339 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  order by b.roll";
            	// echo $sql;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("barcode_no")]!="") $all_barcode_no.=$row[csf("barcode_no")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_barcode_no=implode(",",array_unique(explode(",",chop($all_barcode_no,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				if($all_roll_id!="")
				{
					$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
					from inv_receive_master b, pro_grey_prod_entry_dtls c, pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.roll_id=0 and d.barcode_no in($all_barcode_no) and d.entry_form in (2,22)");  //and d.id in($all_roll_id)
					
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("barcode_no")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("barcode_no")]]["color_range_id"]=$row[csf("color_range_id")];
						$production_delivery_data[$row[csf("barcode_no")]]["machine_dia"]=$row[csf("machine_dia")];
						$production_delivery_data[$row[csf("barcode_no")]]["machine_gg"]=$row[csf("machine_gg")];
						$production_delivery_data[$row[csf("barcode_no")]]["machine_no_id"]=$row[csf("machine_no_id")];
						$production_delivery_data[$row[csf("barcode_no")]]["yarn_lot"]=$row[csf("yarn_lot")];

						if($production_delivery_data[$row[csf("barcode_no")]]["receive_basis"] == 2)
						{
							$program_id_arr[$row[csf("booking_id")]]=$row[csf("booking_id")];
						}
					}
				}



				if(!empty($program_id_arr))
				{
					$booking_from_program = return_library_array("select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id  = b.mst_id and b.status_active = 1 and b.is_deleted = 0 and b.id in (".implode(",", $program_id_arr).") ","id","booking_no");
				}

				if($all_po_id!="")
				{
					$job_array=array();
					$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)";
					//echo $job_sql;
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
				}
				
				foreach($result as $row)
				{
					$all_color_arr=array_unique(explode(",",$production_delivery_data[$row[csf("barcode_no")]]["color_id"]));
					$all_color="";
					foreach($all_color_arr as $color_id)
					{
						$all_color.=$color_library[$color_id].",";
					}

					$all_color=chop($all_color,",");

					if($row[csf("from_trans_entry_form")]==58) 
						$delivery_challan_no =  $delivery_challan_arr[$row[csf("knit_program_id")]]; 
					else $delivery_challan_no = "";

					if($row[csf('booking_without_order')]==1) 
					{
						$job_buyer_no =  $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
					}
					else 
					{
						$job_buyer_no = $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job'];
					}

					if($production_delivery_data[$row[csf("barcode_no")]]["receive_basis"] == 2)
                	{
                		$program_no = $production_delivery_data[$row[csf("barcode_no")]]["booking_no"];
                		$booking_number = $booking_from_program[$program_no];
                	}
                	else
                	{
                		$booking_number = $production_delivery_data[$row[csf("barcode_no")]]["booking_no"];
                	}

                	$program_booking_no =   "P: ".$program_no."<br />B: ".$booking_number;

					$machine_dia_gg = $production_delivery_data[$row[csf("barcode_no")]]["machine_dia"].'X'.$production_delivery_data[$row[csf("barcode_no")]]["machine_gg"];

					$yarn_count_array=array_unique(explode(",",$row[csf('y_count')]));
					$all_count="";
					foreach($yarn_count_array as $count_id)
					{
						$all_count.=$yarn_count_arr[$count_id].",";
					}
					$all_count=chop($all_count,",");

					$brand_no =  $brand_library[$row[csf("brand_id")]]; 
                    $lot_no =  $production_delivery_data[$row[csf("barcode_no")]]["yarn_lot"];
                    $machine_no =  $lib_mc_arr[$production_delivery_data[$row[csf("barcode_no")]]["machine_no_id"]]['no']; 
                    $from_store_no =  $store_arr[$row[csf('from_store')]];
                    $to_store_no = $store_arr[$row[csf('to_store')]];


					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$program_booking_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no]["qnty"] += $row[csf('qnty')];

					$data_array[$all_color][$program_booking_no][$delivery_challan_no."**".$job_buyer_no."**".$program_booking_no."**".$row[csf('feb_description_id')]."**".$row[csf("gsm")]."**".$machine_dia_gg."**".$row[csf("dia_width")]."**".$row[csf("stitch_length")]."**".$all_count."**".$brand_no."**".$lot_no."**".$machine_no."**".$from_store_no."**".$to_store_no]["roll_count"]++;
				}

				$i=1;
				foreach ($data_array as $color_no => $color_data) 
				{
					foreach ($color_data as $prog_book_no => $prog_book_arr) 
					{
						foreach ($prog_book_arr as $data_key_string => $value) 
						{
							$key_arr = explode("**", $data_key_string);

							?>
							<tr>
		                        <td align="center"><? echo $i; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $key_arr[0];?></td>
		                        <td style="word-break:break-all;" align="center"><?	echo $key_arr[1];?></td>
								<td style="word-break:break-all;" align="left"><? echo $key_arr[2];?></td>

		                        <td style="word-break:break-all;" align="center"><? echo $constructtion_arr[$key_arr[3]]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $composition_arr[$key_arr[3]]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $key_arr[4]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $color_no;?>	</td>
								<td style="word-break:break-all;" align="center"><? echo $key_arr[5];?>	</td>

								<td style="word-break:break-all;" align="center"><? echo $key_arr[6]; ?></td>
								<td style="word-break:break-all;" align="center"><? echo $key_arr[7]; ?></td>
								<td style="word-break:break-all;" align="center"><? echo $key_arr[8];?></td>

		                        <td style="word-break:break-all;" align="center"><?  echo $key_arr[9]; ?></td>

		                        <td style="word-break:break-all;" align="center"><? echo $key_arr[10]; ?></td>
		                        <td style="word-break:break-all;" align="center"><? echo $key_arr[11]; ?>  </td>

		                        <td align="right"><? echo $value['roll_count'];?></td>
		                        <td align="right"><? echo number_format($value['qnty'],2,'.','');?></td>
		                        <td style="word-break:break-all;" align="center"><p><? echo $key_arr[12]; ?></p></td>
		                        <td style="word-break:break-all;" align="center"><p><? echo $key_arr[13]; ?></p></td>
	                    	</tr>
							<?
							$i++;
							$sub_color_tot_roll += $value['roll_count'];
							$sub_booking_tot_roll += $value['roll_count'];
							$grand_tot_roll += $value['roll_count'];

							$sub_color_tot_qnty += $value['qnty'];
							$sub_booking_tot_qnty += $value['qnty'];
							$grand_tot_qnty += $value['qnty'];
						}
						?>
						<tr bgcolor="#eeeded" style="font-weight: bold;">
	                        <td colspan="15" align="right">Booking Color wise Total</td>
	                        <td align="right"><? echo $sub_color_tot_roll;?></td>
	                        <td align="right"><? echo number_format($sub_color_tot_qnty,2,'.','');?></td>
	                        <td colspan="2">&nbsp;</td>
	                	</tr>
						<?
						$sub_color_tot_roll =0;$sub_color_tot_qnty=0;
					}
					?>
					<tr bgcolor="#eeeded" style="font-weight: bold;">
                        <td colspan="15" align="right">Booking wise Total</td>
                        <td align="right"><? echo $sub_booking_tot_roll;?></td>
                        <td align="right"><? echo number_format($sub_booking_tot_qnty,2,'.','');?></td>
                        <td colspan="2">&nbsp;</td>
                	</tr>
					<?
					$sub_booking_tot_roll =0;$sub_booking_tot_qnty=0;
					
				}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="15"><strong>Total</strong></th>
                    <th align="center"><? echo $grand_tot_roll; ?></th>
                    <th align="right"><? echo number_format($grand_tot_qnty,2,'.',''); ?></th>
                    <th colspan="2">&nbsp;</th>
                </tr>
            </tfoot>
		</table>
        <br>
        <?
        	echo signature_table(94, $company, "1900px");
        ?>
	</div>
	<?
	exit();
}

if($action=="grey_issue_print_gropping_2")
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
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');

	
	/*$location_arr=return_library_array("select id, location_name from  lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');	
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");*/
	
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	
	$ppl_mc_arr=array();
	if($db_type==0) $machine_cond=" machine_id<>''"; else $machine_cond=" machine_id is not null"; 
	
	$dataArray=sql_select("select id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no from inv_item_transfer_mst where id=$update_id");
	
	
	$dataArray_dtls=sql_select("select mst_id,from_store,to_store from inv_item_transfer_dtls where mst_id=$update_id group by mst_id,from_store,to_store");
	$toFrom_store=array();
	foreach ($dataArray_dtls as $row) {
		$toFrom_store[$row[csf('mst_id')]]["from_store"]=$row[csf('from_store')];
		$toFrom_store[$row[csf('mst_id')]]["to_store"]=$row[csf('to_store')];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
             <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Report</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>Transfer ID :</strong></td>
                <td width="180"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
                <td width="130"><strong>Transfer Criteria:</strong></td>
                <td width="180"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
                <td width="130"><strong>Item Category:</strong></td>
                <td ><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Transfer Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
                <td ><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
			<tr>
            	<td ><strong>From Store</strong></td>
                <td ><? echo $store_arr[$toFrom_store[$dataArray[0][csf('id')]]["from_store"]] ; ?></td>
                <td ><strong>To Store</strong></td>
                <td ><? echo $store_arr[$toFrom_store[$dataArray[0][csf('id')]]["to_store"]]; ?></td>
                <td >&nbsp;</td>
                <td >&nbsp;</td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1500"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">Buyer <br> Job</th>
                <th width="80">Production Basis</th>
                <th width="100">Delivery Challan No</th>
                <th width="90">Prog./ Book. No</th>
                <th width="80">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="100">Fab. Constraction</th>
                <th width="100">Fab. Compositon</th>
                <th width="50">Fin GSM</th>
                <th width="50">Fab. Dia</th>
                <th width="100">Count</th>
                <th width="100">Yarn Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="50">Stich Lenth</th>
                <th width="80">Machine No</th>
                <th width="60">MC Dia X Gauge</th>
                <th width="40">UOM</th>
                <th width="50">Total Roll</th>
                <th>Transfered Qnty</th>
            </thead>
            <?
				$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
				$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
				$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
				$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
				$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');
				
				$i=1; $tot_qty=0; 
				$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
				if($db_type==0)
				{
					$sql = "SELECT a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(b.transfer_qnty) as transfer_qnty, count(b.roll_id) as tot_roll, group_concat(b.roll_id) as roll_id, b.from_booking_without_order as booking_without_order, b.to_order_id as po_breakdown_id, b.from_trans_entry_form, b.knit_program_id  
					from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
					where a.id=b.mst_id and a.id=$update_id and a.entry_form=339 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
					group by a.id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, b.from_booking_without_order, b.to_order_id, b.from_trans_entry_form, b.knit_program_id";
				}
				else
				{
					$sql = "SELECT a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(b.transfer_qnty) as transfer_qnty, count(b.roll_id) as tot_roll, LISTAGG(CAST(b.roll_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.roll_id) as roll_id, b.from_booking_without_order as booking_without_order, b.to_order_id as po_breakdown_id, b.from_trans_entry_form, b.knit_program_id  
					from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
					where a.id=b.mst_id and a.id=$update_id and a.entry_form=339 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
					group by a.id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, b.from_booking_without_order, b.to_order_id, b.from_trans_entry_form, b.knit_program_id";
				}
            	//echo $sql;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id=$all_entry_form="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				$all_entry_form=array_unique(explode(',',chop($all_entry_form,",")));
				if($all_roll_id!="")
				{
					/*$prod_data_sql=sql_select("SELECT a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_count, b.stitch_length, b.febric_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, c.barcode_no, c.po_breakdown_id
					FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
					WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($all_po_id)");
					$prod_data_array=array();
					foreach($prod_data_sql as $row)
					{
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["entry_form"]=$row[csf("entry_form")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["booking_no"]=$row[csf("booking_no")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["booking_id"]=$row[csf("booking_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["knitting_source"]=$row[csf("knitting_source")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["knitting_company"]=$row[csf("knitting_company")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["dtls_id"]=$row[csf("dtls_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["gsm"]=$row[csf("gsm")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["width"]=$row[csf("width")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["color_id"]=$row[csf("color_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["barcode_no"]=$row[csf("barcode_no")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["roll_no"]=$row[csf("roll_no")];
					}*/
					
					/*$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id
					from  inv_receive_master b, pro_grey_prod_entry_dtls c 
					where b.id=c.mst_id and b.entry_form in(2,22,58) and c.id in($all_details_id)");
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("prod_detls_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["color_range_id"]=$row[csf("color_range_id")];
					}*/
					
					$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
					from inv_receive_master b, pro_grey_prod_entry_dtls c , pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.id in($all_roll_id)"); //and d.roll_id=0
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("roll_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_dia"]=$row[csf("machine_dia")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_gg"]=$row[csf("machine_gg")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]=$row[csf("machine_no_id")];
						$production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]=$row[csf("yarn_lot")];
					}
				}
				
				if($all_po_id!="")
				{
					$job_array=array();
					$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)";
					//echo $job_sql;
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
				}
				
				
				/*if($all_roll_id!="")
				{
					$book_no_arr=return_library_array("select a.booking_no, c.id from  inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in($all_roll_id) and c.roll_id=0", "id","booking_no");
					
				}*/
				
				$i=0;//$delivery_challan_arr
				foreach($result as $row)
				{
					$i++;
					?>
                    <tr>
                        <td align="center"><? echo $i; ?></td>
                        
                        <td style="word-break:break-all;" align="center">
						<?
						if($row[csf('booking_without_order')]==1)
						{
							echo $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
						}
						else
						{
							echo $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job']; 
						}
						
						?></td>
                        <td style="word-break:break-all;">
						<? 
						$roll_id_arr=array_unique(explode(",",$row[csf("roll_id")]));
						$all_recv_basis=$rcv_basis_id=$machine_dia=$machine_gg=$machine_no="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_recv_basis.=$prod_basis_arr[$production_delivery_data[$rol_id]["receive_basis"]].",";
							$rcv_basis_id.=$production_delivery_data[$rol_id]["receive_basis"].",";
							$machine_dia.=$production_delivery_data[$rol_id]["machine_dia"].",";
							$machine_gg.=$production_delivery_data[$rol_id]["machine_gg"].",";
							$machine_no.=$lib_mc_arr[$production_delivery_data[$rol_id]["machine_no_id"]]['no'].",";
							$yarn_lot.=$production_delivery_data[$rol_id]["yarn_lot"].",";
						}
						$all_recv_basis=implode(",",array_unique(explode(",",chop($all_recv_basis,","))));
						$rcv_basis_id=implode(",",array_unique(explode(",",chop($rcv_basis_id,","))));
						$machine_dia=implode(",",array_unique(explode(",",chop($machine_dia,","))));
						$machine_gg=implode(",",array_unique(explode(",",chop($machine_gg,","))));
						$machine_no=implode(",",array_unique(explode(",",chop($machine_no,","))));
						$yarn_lot=implode(",",array_unique(explode(",",chop($yarn_lot,","))));
						echo $all_recv_basis;
						
						?></td>
                       <td style="word-break:break-all;" align="center"><? if($row[csf('from_trans_entry_form')]==58) echo $delivery_challan_arr[$row[csf("knit_program_id")]]; else echo ""; ?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						$all_booking="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_booking.=$production_delivery_data[$rol_id]["booking_no"].",";
						}
						$all_booking=implode(",",array_unique(explode(",",chop($all_booking,","))));
						echo $all_booking;
						?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						$all_color="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_color_arr=array_unique(explode(",",$production_delivery_data[$rol_id]["color_id"]));
							foreach($all_color_arr as $color_id)
							{
								$all_color.=$color_library[$color_id].",";
							}
						}
						$all_color=implode(",",array_unique(explode(",",chop($all_color,","))));
						echo $all_color;
						?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						$all_color_range="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_color_range.=$color_range[$production_delivery_data[$rol_id]["color_range_id"]].",";
						}
						$all_color_range=implode(",",array_unique(explode(",",chop($all_color_range,","))));
						echo $all_color_range; 
						//echo $color_range[$production_delivery_data[$row[csf("prod_detls_id")]]["color_range_id"]]; 
						?>
                        </td>
                        <td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("gsm")]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("dia_width")]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						//echo $row[csf('y_count')]; 
						$yarn_count_array=array_unique(explode(",",$row[csf('y_count')]));
						$all_count="";
						foreach($yarn_count_array as $y_count_id)
						{
							$all_count.=$yarn_count_arr[$y_count_id].",";
						}
						$all_count=chop($all_count,",");
						echo $all_count;
						?>
                        </td>
                        <td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
                        <td style="word-break:break-all;" title="<? echo implode(",",array_unique(explode(",",$row[csf("roll_id")])));?>"><? echo $yarn_lot;  $yarn_lot=""; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $machine_no; $machine_no="";//$lib_mc_arr[$row[csf("machine_no_id")]]['no']; ?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						/*if($rcv_basis_id==2)
						{
							echo $ppl_mc_arr[$all_booking]['dia'].'X'.$ppl_mc_arr[$all_booking]['gauge']; 
						}
						else
						{
							echo $lib_mc_arr[$row[csf("machine_no_id")]]['dia'].'X'.$lib_mc_arr[$row[csf("machine_no_id")]]['gauge']; 
						}*/
						echo $machine_dia.'X'.$machine_gg;
						$machine_dia=$machine_gg="";
						?>
                        </td>
                        <td align="center">Kg</td>
                        <td align="center"><? echo $row[csf("tot_roll")]; ?></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2,'.','');?></td>
                    </tr>
                	<?
					$tot_qty+=$row[csf('transfer_qnty')];
					$gt_total_roll+=$row[csf("tot_roll")];
					
				}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="18"><strong>Total</strong></th>
                    <th align="center"><? echo $gt_total_roll; ?></th>
                    <th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
		</table>
        <br>
		<?
        	echo signature_table(94, $company, "1800px");
        ?>
	</div>
	<?
	exit();
}
if($action=="grey_issue_print_gropping") // Total Roll Wise
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
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$brand_library=return_library_array("select id, brand_name from lib_brand","id","brand_name");
	
	/*$location_arr=return_library_array("select id, location_name from  lib_location",'id','location_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$brand_arr = return_library_array("select id, brand_name from lib_brand",'id','brand_name');	
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$booking_arr=return_library_array( "select id, booking_no from wo_booking_mst where item_category in(2,13)", "id", "booking_no");
	$pi_arr=return_library_array( "select id, pi_number from com_pi_master_details where item_category_id in(2,13)", "id", "pi_number");*/
	
	$lib_mc_arr=array();
	$mc_sql=sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$lib_mc_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$lib_mc_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$lib_mc_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	
	$ppl_mc_arr=array();
	if($db_type==0) $machine_cond=" machine_id<>''"; else $machine_cond=" machine_id is not null"; 
	
	$dataArray=sql_select("select id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no, remarks from inv_item_transfer_mst where id=$update_id");
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	?>
    <div>
        <table width="1000" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
             <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Grey Fabric Transfer Entry Report</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
            <tr>
            	<td width="130"><strong>Transfer ID :</strong></td>
                <td width="180"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
                <td width="130"><strong>Transfer Criteria:</strong></td>
                <td width="180"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
                <td width="130"><strong>Item Category:</strong></td>
                <td ><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
            </tr>
            <tr>
            	<td ><strong>Transfer Date:</strong></td>
                <td ><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
                <td ><strong>Challan No:</strong></td>
                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
                <td ><strong>Remarks:</strong></td>
                <td ><? echo $dataArray[0][csf('remarks')]; ?></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="1800"  border="1" rules="all" class="rpt_table" >
            <thead>
                <th width="20">SL</th>
                <th width="100">From Store</th>
                <th width="100">To Store</th>
                <th width="50">File No</th>
                <th width="50">Reff No</th>
                <th width="100">Buyer <br> Job</th>
                <th width="80">Production Basis</th>
                <!--<th width="100">Delivery Challan No</th>-->
                <th width="90">Prog./ Book. No</th>
                <th width="80">Fab Color</th>
                <th width="80">Color Range</th>
                <th width="100">Fab. Constraction</th>
                <th width="100">Fab. Compositon</th>
                <th width="50">Fin GSM</th>
                <th width="50">Fab. Dia</th>
                <th width="100">Count</th>
                <th width="100">Yarn Brand</th>
                <th width="80">Yarn Lot</th>
                <th width="50">Stich Lenth</th>
                <th width="80">Machine No</th>
                <th width="60">MC Dia X Gauge</th>
                <th width="40">UOM</th>
                <th width="50">Total Roll</th>
                <th>Transfered Qnty</th>
            </thead>
            <?
				$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
				$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
				$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
				$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
				//$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');
				
				$i=1; $tot_qty=0; 
				$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
				if($db_type==0)
				{
					$sql = "SELECT a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(b.transfer_qnty) as transfer_qnty, count(b.roll_id) as tot_roll, group_concat(b.roll_id) as roll_id, b.to_order_id as po_breakdown_id
					from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
					where a.id=b.mst_id and a.id=$update_id and a.entry_form=339 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
					group by a.id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, b.to_order_id";//b.knit_program_id,
				}
				else
				{
					//LISTAGG(CAST(brand AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY brand) as brand
					$sql = "SELECT a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, sum(b.transfer_qnty) as transfer_qnty, count(b.roll_id) as tot_roll, LISTAGG(CAST(b.roll_id AS VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY b.roll_id) as roll_id, b.to_order_id as po_breakdown_id
					from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
					where a.id=b.mst_id and a.id=$update_id and a.entry_form=339 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0
					group by a.id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, b.to_order_id";//b.knit_program_id,
				}
            	// echo $sql;
				$result=sql_select($sql);
				$all_roll_id=$all_po_id=$all_details_id=$all_entry_form="";
				foreach($result as $row)
				{
					if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
					if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
					if($row[csf("prod_detls_id")]!="") $all_details_id.=$row[csf("prod_detls_id")].",";
				}
				$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
				$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
				$all_details_id=implode(",",array_unique(explode(",",chop($all_details_id,","))));
				$all_entry_form=array_unique(explode(',',chop($all_entry_form,",")));
				if($all_roll_id!="")
				{
					/*$prod_data_sql=sql_select("SELECT a.id, a.entry_form, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.gsm, b.width, b.color_id, b.color_range_id, b.yarn_count, b.stitch_length, b.febric_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, c.barcode_no, c.po_breakdown_id
					FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
					WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2) and c.entry_form in(2) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($all_po_id)");
					$prod_data_array=array();
					foreach($prod_data_sql as $row)
					{
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["entry_form"]=$row[csf("entry_form")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["booking_no"]=$row[csf("booking_no")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["booking_id"]=$row[csf("booking_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["knitting_source"]=$row[csf("knitting_source")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["knitting_company"]=$row[csf("knitting_company")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["dtls_id"]=$row[csf("dtls_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["gsm"]=$row[csf("gsm")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["width"]=$row[csf("width")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["color_id"]=$row[csf("color_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["barcode_no"]=$row[csf("barcode_no")];
						$prod_data_array[$row[csf("po_breakdown_id")]][$row[csf("yarn_count")]][$row[csf("stitch_length")]][$row[csf("febric_description_id")]][$row[csf("machine_no_id")]][$row[csf("yarn_lot")]][$row[csf("brand_id")]]["roll_no"]=$row[csf("roll_no")];
					}*/
					
					/*$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id
					from  inv_receive_master b, pro_grey_prod_entry_dtls c 
					where b.id=c.mst_id and b.entry_form in(2,22,58) and c.id in($all_details_id)");
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("prod_detls_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("prod_detls_id")]]["color_range_id"]=$row[csf("color_range_id")];
					}*/
					
					$production_sql=sql_select("select b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
					from inv_receive_master b, pro_grey_prod_entry_dtls c , pro_roll_details d
					where b.id=c.mst_id and c.id=d.dtls_id and d.id in($all_roll_id)"); //and d.roll_id=0
					$production_delivery_data=array();
					foreach($production_sql as $row)
					{
						$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]=$row[csf("receive_basis")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_id"]=$row[csf("booking_id")];
						$production_delivery_data[$row[csf("roll_id")]]["booking_no"]=$row[csf("booking_no")];
						$production_delivery_data[$row[csf("roll_id")]]["color_id"]=$row[csf("color_id")];
						$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]=$row[csf("color_range_id")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_dia"]=$row[csf("machine_dia")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_gg"]=$row[csf("machine_gg")];
						$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]=$row[csf("machine_no_id")];
						$production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]=$row[csf("yarn_lot")];

						if($row[csf("receive_basis")] == 2)
						{
							$program_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
						}
					}
				}

				if(!empty($program_id_arr))
				{
					$booking_from_program = return_library_array("select b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id  = b.mst_id and b.status_active = 1 and b.is_deleted = 0 and b.id in (".implode(",", $program_id_arr).") ","id","booking_no");
				}
				
				if($all_po_id!="")
				{
					$job_array=array();
					$job_sql="select a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)";
					//echo $job_sql;
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
				}
				
				
				/*if($all_roll_id!="")
				{
					$book_no_arr=return_library_array("select a.booking_no, c.id from  inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in($all_roll_id) and c.roll_id=0", "id","booking_no");
					
				}*/
				
				$i=0;//$delivery_challan_arr
				foreach($result as $row)
				{
					$i++;
					?>
                    <tr>
                        <td align="center"><? echo $i; ?></td>
                        <td style="word-break:break-all;"><? echo $store_arr[$row[csf('from_store')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $store_arr[$row[csf('to_store')]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? if($row[csf('booking_without_order')]==1) echo "&nbsp;"; else echo $job_array[$row[csf('from_order_id')]]['file_no'] ; ?></td>
                        
                        <td style="word-break:break-all;" align="center"><? if($row[csf('booking_without_order')]==1) echo "&nbsp;"; else echo $job_array[$row[csf('from_order_id')]]['grouping'];//$row[csf('grouping')]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						if($row[csf('booking_without_order')]==1)
						{
							echo $buyer_library[$book_buyer_arr[$row[csf('po_breakdown_id')]]];
						}
						else
						{
							echo $buyer_library[$job_array[$row[csf('from_order_id')]]['buyer']]. "<br>".$job_array[$row[csf('from_order_id')]]['job']; 
						}
						
						?></td>
                        <td style="word-break:break-all;">
						<? 
						$roll_id_arr=array_unique(explode(",",$row[csf("roll_id")]));
						$all_recv_basis=$rcv_basis_id=$machine_dia=$machine_gg=$machine_no=$program_nos="";$all_booking="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_recv_basis.=$prod_basis_arr[$production_delivery_data[$rol_id]["receive_basis"]].",";
							$rcv_basis_id.=$production_delivery_data[$rol_id]["receive_basis"].",";
							$machine_dia.=$production_delivery_data[$rol_id]["machine_dia"].",";
							$machine_gg.=$production_delivery_data[$rol_id]["machine_gg"].",";
							$machine_no.=$lib_mc_arr[$production_delivery_data[$rol_id]["machine_no_id"]]['no'].",";
							$yarn_lot.=$production_delivery_data[$rol_id]["yarn_lot"].",";

							if($production_delivery_data[$rol_id]["receive_basis"] == 2)
							{
								$all_booking .= $booking_from_program[$production_delivery_data[$rol_id]["booking_id"]].",";
								
								$program_nos .= $production_delivery_data[$rol_id]["booking_no"].",";
							}else{
								$all_booking.=$production_delivery_data[$rol_id]["booking_no"].",";
							}
							
						}

						$program_nos=implode(",",array_unique(explode(",",chop($program_nos,","))));
						$all_booking=implode(",",array_unique(explode(",",chop($all_booking,","))));
						$all_recv_basis=implode(",",array_unique(explode(",",chop($all_recv_basis,","))));
						$rcv_basis_id=implode(",",array_unique(explode(",",chop($rcv_basis_id,","))));
						$machine_dia=implode(",",array_unique(explode(",",chop($machine_dia,","))));
						$machine_gg=implode(",",array_unique(explode(",",chop($machine_gg,","))));
						$machine_no=implode(",",array_unique(explode(",",chop($machine_no,","))));
						$yarn_lot=implode(",",array_unique(explode(",",chop($yarn_lot,","))));
						echo $all_recv_basis;
						
						?></td>
                        <!--<td style="word-break:break-all;" align="center"><? //if($row[csf('from_trans_entry_form')]==58) echo $delivery_challan_arr[$row[csf("knit_program_id")]]; else echo ""; ?></td>-->
                        <td style="word-break:break-all;" align="center">
						<? 
						
						/*foreach($roll_id_arr as $rol_id)
						{
							$all_booking.=$production_delivery_data[$rol_id]["booking_no"].",";
						}
						$all_booking=implode(",",array_unique(explode(",",chop($all_booking,","))));
						echo $all_booking;*/
						echo "P: ".$program_nos."<br >B: ".$all_booking;
						?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						$all_color="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_color_arr=array_unique(explode(",",$production_delivery_data[$rol_id]["color_id"]));
							foreach($all_color_arr as $color_id)
							{
								$all_color.=$color_library[$color_id].",";
							}
						}
						$all_color=implode(",",array_unique(explode(",",chop($all_color,","))));
						echo $all_color;
						?></td>
                        <td style="word-break:break-all;" align="center">
						<?
						$all_color_range="";
						foreach($roll_id_arr as $rol_id)
						{
							$all_color_range.=$color_range[$production_delivery_data[$rol_id]["color_range_id"]].",";
						}
						$all_color_range=implode(",",array_unique(explode(",",chop($all_color_range,","))));
						echo $all_color_range; 
						//echo $color_range[$production_delivery_data[$row[csf("prod_detls_id")]]["color_range_id"]]; 
						?>
                        </td>
                        <td style="word-break:break-all;"><? echo $constructtion_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;"><? echo $composition_arr[$row[csf('feb_description_id')]]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("gsm")]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf("dia_width")]; ?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						//echo $row[csf('y_count')]; 
						$yarn_count_array=array_unique(explode(",",$row[csf('y_count')]));
						$all_count="";
						foreach($yarn_count_array as $y_count_id)
						{
							$all_count.=$yarn_count_arr[$y_count_id].",";
						}
						$all_count=chop($all_count,",");
						echo $all_count;
						?>
                        </td>
                        <td style="word-break:break-all;"><?  echo $brand_library[$row[csf("brand_id")]]; ?></td>
                        <td style="word-break:break-all;" title="<? echo implode(",",array_unique(explode(",",$row[csf("roll_id")])));?>"><? echo $yarn_lot;  $yarn_lot=""; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
                        <td style="word-break:break-all;" align="center"><? echo $machine_no; $machine_no="";//$lib_mc_arr[$row[csf("machine_no_id")]]['no']; ?></td>
                        <td style="word-break:break-all;" align="center">
						<? 
						/*if($rcv_basis_id==2)
						{
							echo $ppl_mc_arr[$all_booking]['dia'].'X'.$ppl_mc_arr[$all_booking]['gauge']; 
						}
						else
						{
							echo $lib_mc_arr[$row[csf("machine_no_id")]]['dia'].'X'.$lib_mc_arr[$row[csf("machine_no_id")]]['gauge']; 
						}*/
						echo $machine_dia.'X'.$machine_gg;
						$machine_dia=$machine_gg="";
						?>
                        </td>
                        <td align="center">Kg</td>
                        <td align="center"><? echo $row[csf("tot_roll")]; ?></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2,'.','');?></td>
                    </tr>
                	<?
					$tot_qty+=$row[csf('transfer_qnty')];
					$gt_total_roll+=$row[csf("tot_roll")];
					
				}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="21"><strong>Total</strong></th>
                    <th align="center"><? echo $gt_total_roll; ?></th>
                    <th align="right"><? echo number_format($tot_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
		</table>
        <br>
		<?
        	echo signature_table(94, $company, "1800px");
        ?>
	</div>
	<?
	exit();
}

if($action=="grey_issue_print_gropping3") // Print 3
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
	$buyer_library=return_library_array("select id, short_name from lib_buyer","id","short_name");
	$approved_date_arr=return_library_array("select mst_id, approved_date from approval_history where entry_form=37 and mst_id=$update_id","mst_id","approved_date");
	
	$dataArray=sql_select("SELECT id, transfer_system_id, transfer_criteria, transfer_date, item_category, challan_no, remarks, is_approved from inv_item_transfer_requ_mst where id=$update_id");
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	?>
	<span style="color: red;"></span>
    <div>
        <table width="1880" cellspacing="0">
            <tr>
                <td colspan="6" align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
            </tr>
            <tr>
                <td colspan="6" align="center" style="font-size:16px; font-weight:bold">Greige Fabric Transfer Requisition [Roll Wise]</td>
            </tr>
            <tr>
                <td colspan="6">&nbsp;</td>
            </tr>
        </table>
        <div style="width: 1880px;">
	        <div style="margin-bottom: 2px; float: right;">	
				<table width="100%" cellspacing="5" border="1" rules="all" class="rpt_table">			
					</tr>
					<tr style="font-size: 18px;">
						<td width="100"><strong>Approve Status</strong></td>
						<td width="160">
							<? 
								if($dataArray[0][csf('is_approved')]==1)
								{
									echo "<span style='color: green;'><strong>Approved</strong></span>";
								}
								else{
									echo "<span style='color: red;'><strong>Unapproved</strong></span>";
								}
							?>
						</td>
					</tr>
					<tr style="font-size: 18px;">
						<td width="80"><strong>Approve Date</strong></td>
						<td width="160"><? echo change_date_format($approved_date_arr[$dataArray[0][csf('id')]]); ?></td>
					</tr>
				</table>
			</div>
			<!-- ====== -->
			<div style="margin-right: 80px;margin-bottom: 2px; float: left;">
				<table width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
					<tr style="font-size: 18px;">
						<td width="130"><strong>Transfer Requisition No :</strong></td>
		                <td width="180"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
		                <td width="130"><strong>Transfer Criteria:</strong></td>
		                <td width="180"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
					</tr>
					<tr style="font-size: 18px;">
						<td ><strong>Transfer Requisition Date :</strong></td>
		                <td ><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
		                <td ><strong>Challan No:</strong></td>
		                <td ><? echo $dataArray[0][csf('challan_no')]; ?></td>
					</tr>
					<tr style="font-size: 18px;">
						<td ><strong>Remarks:</strong></td>
		                <td colspan="4"><? echo $dataArray[0][csf('remarks')]; ?></td>
					</tr>
				</table>
			</div>
		</div>
		<table width="1880" cellspacing="0">
            <tr colspan="2" align="center" class="header">
				<td><strong></strong></td><td colspan="2" id="barcode_img_id"></td>
			</tr>
        </table>
        <br>
        <table cellspacing="0" width="1880"  border="1" rules="all" class="rpt_table" >
            <thead>
            	<tr>
            		<th colspan="10">Greige Fabric Information</th>
            		<th colspan="6">Transfer From Job [Greige Fabric Out]</th>
            		<th colspan="7">Transfer To Job [Greige Fabric In]</th>
            	</tr>
            	<tr>
            		<th width="20">SL</th>
	                <th width="100">Fab. Construction</th>
	                <th width="100">Fab. Composition</th>
	                <th width="50">Fab. Dia</th>
	                <th width="50">Fin GSM</th>
	                <th width="80">Fab Color</th>
	                <th width="60">MC Dia X Gauge</th>
	                <th width="50">Stich Lenth</th>
	                <th width="100">Yarn Count</th>
	                <th width="80">Transfer Requisition Qnty</th>

	                <th width="100">Buyer</th>
	                <th width="80">Style</th>
	                <th width="100">Job No</th>
	                <th width="100">Order</th>
	                <th width="90">Fabric Booking No</th>
	                <th width="100">From Store</th>						

	                <th width="100">Buyer</th>
	                <th width="80">Style</th>
	                <th width="100">Job No</th>
	                <th width="100">To Order</th>
	                <th width="100">To Color</th>
	                <th width="90">Fabric Booking No</th>
	                <th width="100">To Store</th>
            	</tr>
            </thead>
            <?
			$color_library=return_library_array( "select id, color_name from  lib_color", "id", "color_name");
			$prod_basis_arr=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
			$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
			$yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count",'id','yarn_count');
			$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');
			//$delivery_challan_arr=return_library_array( "select id,sys_number from pro_grey_prod_delivery_mst where entry_form=56",'id','sys_number');
			
			$i=1; $tot_qty=0; 
			$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
			$sql = "SELECT a.id as mst_id, b.from_store, b.to_store, b.from_order_id, b.y_count, b.stitch_length, b.feb_description_id, b.machine_no_id, b.yarn_lot, b.brand_id, b.gsm, b.dia_width, b.transfer_qnty, b.roll_id, b.to_order_id, b.to_color_id
			from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b
			where a.id=b.mst_id and a.id=$update_id and a.entry_form=339 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0";//b.knit_program_id,
        	// echo $sql;
			$result=sql_select($sql);
			$all_roll_id=$all_po_id="";
			foreach($result as $row)
			{
				if($row[csf("roll_id")]!="") $all_roll_id.=$row[csf("roll_id")].",";
				if($row[csf("from_order_id")]!="") $all_po_id.=$row[csf("from_order_id")].",";
				if($row[csf("to_order_id")]!="") $all_po_id.=$row[csf("to_order_id")].",";
			}
			$all_roll_id=implode(",",array_unique(explode(",",chop($all_roll_id,","))));
			$all_po_id=implode(",",array_unique(explode(",",chop($all_po_id,","))));
			if($all_roll_id!="")
			{
				$production_sql=sql_select("SELECT b.receive_basis, b.booking_id, b.booking_no, c.id as prod_detls_id, c.color_id, c.color_range_id, c.machine_dia, c.machine_gg, c.machine_no_id, c.yarn_lot, d.id as roll_id, d.roll_no, d.barcode_no
				from inv_receive_master b, pro_grey_prod_entry_dtls c , pro_roll_details d
				where b.id=c.mst_id and c.id=d.dtls_id and d.id in($all_roll_id)"); //and d.roll_id=0
				$production_delivery_data=array();
				foreach($production_sql as $row)
				{
					$production_delivery_data[$row[csf("roll_id")]]["receive_basis"]=$row[csf("receive_basis")];
					$production_delivery_data[$row[csf("roll_id")]]["booking_id"]=$row[csf("booking_id")];
					$production_delivery_data[$row[csf("roll_id")]]["booking_no"]=$row[csf("booking_no")];
					$production_delivery_data[$row[csf("roll_id")]]["color_id"]=$row[csf("color_id")];
					$production_delivery_data[$row[csf("roll_id")]]["color_range_id"]=$row[csf("color_range_id")];
					$production_delivery_data[$row[csf("roll_id")]]["machine_dia"]=$row[csf("machine_dia")];
					$production_delivery_data[$row[csf("roll_id")]]["machine_gg"]=$row[csf("machine_gg")];
					$production_delivery_data[$row[csf("roll_id")]]["machine_no_id"]=$row[csf("machine_no_id")];
					$production_delivery_data[$row[csf("roll_id")]]["yarn_lot"]=$row[csf("yarn_lot")];

					if($row[csf("receive_basis")] == 2)
					{
						$program_id_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
					}
				}
			}

			if(!empty($program_id_arr))
			{
				$booking_from_program = return_library_array("SELECT b.id, a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id  = b.mst_id and b.status_active = 1 and b.is_deleted = 0 and b.id in (".implode(",", $program_id_arr).") ","id","booking_no");
			}
			
			if($all_po_id!="")
			{
				$job_array=array();
				$job_sql="SELECT a.style_ref_no, a.job_no, a.buyer_name, b.id, b.po_number, b.file_no, b.grouping, c.booking_no from wo_po_details_master a, wo_po_break_down b, wo_booking_mst c where a.job_no=b.job_no_mst and b.job_no_mst=c.job_no and b.id in($all_po_id)";
				// echo $job_sql;
				$job_sql_result=sql_select($job_sql);
				foreach($job_sql_result as $row)
				{
					$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
					$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
					$job_array[$row[csf('id')]]['style_ref']=$row[csf('style_ref_no')];
					$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
					$job_array[$row[csf('id')]]['grouping']=$row[csf('grouping')];
					$job_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
				}
			}
			
			$data_array=array();
			foreach ($result as $key => $row) 
			{
				/*$machine_dia.=$production_delivery_data[$row[csf("roll_id")]]["machine_dia"].",";
				$machine_gg.=$production_delivery_data[$row[csf("roll_id")]]["machine_gg"].",";
				$machine_dia=implode(",",array_unique(explode(",",chop($machine_dia,","))));
				$machine_gg=implode(",",array_unique(explode(",",chop($machine_gg,","))));*/
				
				$machine_dia=$production_delivery_data[$row[csf("roll_id")]]["machine_dia"];
				$machine_gg=$production_delivery_data[$row[csf("roll_id")]]["machine_gg"];
				$color_id=$production_delivery_data[$row[csf("roll_id")]]["color_id"];
				$mc_dia_gauge=$machine_dia.'x'.$machine_gg;
				
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['transfer_qnty']+=$row[csf("transfer_qnty")];
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['from_order_id']=$row[csf("from_order_id")];
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['to_order_id']=$row[csf("to_order_id")];
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['from_store']=$row[csf("from_store")];
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['to_store']=$row[csf("to_store")];
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['roll_id'].=$row[csf("roll_id")].',';
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['y_count'].=$row[csf("y_count")].',';
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['yarn_lot'].=$row[csf("yarn_lot")].',';
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['brand_id'].=$row[csf("brand_id")].',';
				$data_array[$row[csf("feb_description_id")]][$row[csf("dia_width")]][$row[csf("gsm")]][$color_id][$mc_dia_gauge][$row[csf("stitch_length")]]['to_color_id'].=$row[csf("to_color_id")].',';
			}
			// echo "<pre>";print_r($data_array);die;


			$i=0;
			foreach ($data_array as $feb_desc_id => $feb_desc_id_arr) 
			{
				foreach ($feb_desc_id_arr as $dia_width => $dia_width_arr) 
				{
					foreach ($dia_width_arr as $gsm => $gsm_arr) 
					{
						foreach ($gsm_arr as $color_id => $color_id_arr) 
						{
							foreach ($color_id_arr as $mc_dia_gauge => $mc_dia_gauge_arr) 
							{
								foreach ($mc_dia_gauge_arr as $stitch_length => $row) 
								{
									$i++;
									$roll_id_arr=array_filter(array_unique(explode(",",$row["roll_id"])));
									// echo "<pre>";print_r($roll_id_arr);
									$all_booking="";
									foreach($roll_id_arr as $rol_id)
									{
										// echo $rol_id.'<br>';
										if($production_delivery_data[$rol_id]["receive_basis"] == 2)
										{
											$all_booking .= $booking_from_program[$production_delivery_data[$rol_id]["booking_id"]].",";
										}
										else
										{
											$all_booking.=$production_delivery_data[$rol_id]["booking_no"].",";
										}
									}
									// $program_nos=implode(",",array_unique(explode(",",chop($program_nos,","))));
									$all_booking=implode(",",array_unique(explode(",",chop($all_booking,","))));
									?>
				                    <tr>
				                        <td align="center"><? echo $i; ?></td>
				                        <td style="word-break:break-all;"><? echo $constructtion_arr[$feb_desc_id]; ?></td>
				                        <td style="word-break:break-all;"><? echo $composition_arr[$feb_desc_id]; ?></td>
				                        <td style="word-break:break-all;" align="center"><? echo $gsm; ?></td>
				                        <td style="word-break:break-all;" align="center"><? echo $dia_width; ?></td>
				                        <td style="word-break:break-all;" align="center"><? echo $color_library[$color_id]; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $mc_dia_gauge; ?></td>
				                        <td style="word-break:break-all;" align="center"><? echo $stitch_length; ?></td>
				                        <td style="word-break:break-all;" align="center">
										<? 
										// $yarn_lot=implode(",", array_unique(explode(",",$row['yarn_lot'])));
										$yarn_count_array=array_unique(explode(",",$row['y_count']));
										$all_count="";
										foreach($yarn_count_array as $y_count_id)
										{
											$all_count.=$yarn_count_arr[$y_count_id].",";
										}
										$all_count=chop($all_count,",");
										echo $all_count;
										?>
				                        </td>
				                        <td align="right"><? echo number_format($row['transfer_qnty'],2,'.','');?></td>
				                        

				                        <td style="word-break:break-all;" align="center">
										<?
										if($row[csf('booking_without_order')]==1)
										{
											$from_buyer=$buyer_library[$book_buyer_arr[$row['from_order_id']]];
											$from_style="";
											$from_po="";
											$from_job_no="";
										}
										else
										{
											$from_buyer=$buyer_library[$job_array[$row['from_order_id']]['buyer']]; 
											$from_style=$job_array[$row['from_order_id']]['style_ref'];
											$from_po=$job_array[$row['from_order_id']]['po']; 
											$from_job_no=$job_array[$row['from_order_id']]['job'];
										}
										echo $from_buyer;
										?></td>
										<td align="center"><? echo $from_style; ?></td>
										<td align="center"><? echo $from_job_no; ?></td>
										<td align="center" title="<?echo $row['from_order_id'];?>"><? echo $from_po; ?></td>
										<td style="word-break:break-all;" align="center"><? echo $all_booking; ?></td>
										<td style="word-break:break-all;"><? echo $store_arr[$row['from_store']]; ?></td>


										<td style="word-break:break-all;" align="center">
										<?
										if($row[csf('booking_without_order')]==1)
										{
											$to_buyer=$buyer_library[$book_buyer_arr[$row['to_order_id']]];
											$to_style="";
											$to_po="";
											$to_job_no="";
										}
										else
										{
											$to_buyer=$buyer_library[$job_array[$row['to_order_id']]['buyer']]; 
											$to_style=$job_array[$row['to_order_id']]['style_ref'];
											$to_po=$job_array[$row['to_order_id']]['po']; 
											$to_job_no=$job_array[$row['to_order_id']]['job'];
											$to_booking_no=$job_array[$row['to_order_id']]['booking_no'];
										}
										echo $to_buyer;
										?></td>
										<td align="center"><? echo $to_style; ?></td>
										<td align="center"><? echo $to_job_no; ?></td>
										<td align="center" title="<?echo $row['to_order_id'];?>"><? echo $to_po; ?></td>
										<td align="center">
											<? 
											// $yarn_lot=implode(",", array_unique(explode(",",$row['yarn_lot'])));
											$to_color_id_array=array_unique(explode(",",$row['to_color_id']));
											$to_color_name="";
											foreach($to_color_id_array as $to_color)
											{
												$to_color_name.=$color_library[$to_color].",";
											}
											$to_color_name=chop($to_color_name,",");
											echo $to_color_name;
											?>
										</td>
										<td style="word-break:break-all;" align="center"><? echo $to_booking_no; ?></td>
										<td style="word-break:break-all;"><? echo $store_arr[$row['to_store']]; ?></td>
				                    </tr>
				                	<?
									$tot_qty+=$row['transfer_qnty'];
								}
							}
						}
					}
				}
			}
			?>
            <tfoot>
            	<tr>
                	<th align="right" colspan="9"><strong>Total</strong></th>
                    <th align="center"><? echo $tot_qty; ?></th>
                    <th align="right" colspan="13"></th>
                </tr>
            </tfoot>
		</table>
        <br>
		<?
        	echo signature_table(261, $company, "1800px");
        ?>
	</div>
	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
			// alert(value)
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
			$("#barcode_img_id").html('11');
			value = {code:value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $dataArray[0][csf('transfer_system_id')]; ?>');
	</script>
	<?
	exit();
}
?>

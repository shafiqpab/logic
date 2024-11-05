<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 $com_location_credential_cond order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down('requires/roll_wise_grey_fabric_delivery_controller', this.value+'_'+$data[0], 'load_drop_down_store','store_td');" );
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);
	if ($data[0] != "" && $data[0] > 0) {$location_cond = "and a.location_id='$data[0]'";} else { $location_cond = "";}
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and b.category_type=13 and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond $location_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"");
	exit();
}

if ($action == "load_drop_down_company") 
{
	$data = explode("_",$data);
	$company_cond = ($data[0] != "" && $data[0] == 1)?" and id=$data[0]":"";
	if($data[1] == 1)
	{
		echo create_drop_down("cbo_party", 152, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "--Select--", $data[0], "");
	}
	else
	{
		echo create_drop_down("cbo_party", 152, "select a.id,a.buyer_name from lib_buyer a,lib_buyer_party_type b where a.id=b.buyer_id and b.party_type=3 and a.status_active=1", "id,buyer_name", 1, "-- Select Party --", $selected, "", 0);
	}
	exit();
}

if ($action == "load_drop_down_buyer") 
{
	$data = explode("_",$data);
	echo create_drop_down("cbo_party", 152, "select id, buyer_name from lib_buyer where id=$data[0] and status_active=1", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	exit();
}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("**",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_dyeing_comp", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
	}
	else if($data[0]==3)
	{
		echo create_drop_down( "cbo_dyeing_comp", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_comp", 152, $blank_array,"",1, "-- Select --", 0, "" );
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//$avg_product_rate_arr=return_library_array( "select id, avg_rate_per_unit from product_details_master",'id','avg_rate_per_unit');
	
	for($k=1;$k<=$tot_row;$k++)
	{
		$productId="productId_".$k;
		$prod_ids.=$$productId.",";
	}
	
	$prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,","))));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in( $prod_ids) and transaction_type in (1,4,5) and status_active=1 and is_deleted=0", "max_date");
	if($max_recv_date != "")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$issue_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_issue_date)));
		if ($issue_date < $max_recv_date)
		{
			echo "20**Issue Date Can not Be Less Than Last Receive Date Of These Lot";
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

		// ================Start
		for($k=1;$k<=$tot_row;$k++)
		{
			$barcodeNo="barcodeNo_".$k;
			$all_barcodeNo.=$$barcodeNo.",";
		}
		// $barcodeNOS=implode(",",array_unique(explode(",",chop($all_barcodeNo,','))));
		$all_barcodeNo=chop($all_barcodeNo,',');
		$all_barcodeNo_arr=explode(",", $all_barcodeNo);

		if($all_barcodeNo!="")
		{
			$all_barcodeNo_arr = array_filter($all_barcodeNo_arr);
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
						$barCond.=" a.barcode_no in($chunk_arr_value) or ";
					}

					$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
				}
				else
				{
					$all_barcode_no_cond=" and a.barcode_no in($barcod_NOs)";
				}
			}
		}

		$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 $all_barcode_no_cond and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

		if($check_if_already_scanned[0][csf("barcode_no")]!="")
		{
			echo "20**Sorry! Barcode already Scanned. Challan No: ".$check_if_already_scanned[0][csf("issue_number")]." Barcode No ".$$barcodeNo;
			die;
		}

		$trans_check_sql = sql_select("select a.barcode_no, a.entry_form, a.po_breakdown_id, a.qnty from pro_roll_details a where a.entry_form in ( 22,58,83,133,82,180,110,183,84) $all_barcode_no_cond and a.re_transfer =0 and a.status_active = 1 and a.is_deleted = 0 union all select a.barcode_no, a.entry_form, a.po_breakdown_id, a.qnty from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in (2) and b.trans_id<>0 and a.re_transfer =0 $all_barcode_no_cond and a.status_active = 1 and a.is_deleted = 0");

		if($trans_check_sql[0][csf("barcode_no")] !="")
		{
			foreach ($trans_check_sql as $val)
			{
				$trans_po_barcode_check_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] = $val[csf("barcode_no")]."__".$val[csf("po_breakdown_id")];
				$actual_wgt_arr[$val[csf("barcode_no")]] = $val[csf("qnty")];
			}
		}
		// ======================end

		if($db_type==0)
			$year_cond="YEAR(insert_date)";
		else if($db_type==2)
			$year_cond="to_char(insert_date,'YYYY')";
		else
			$year_cond="";//defined Later

		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'KGIR', date("Y",time()), 5, "select issue_number_prefix,issue_number_prefix_num from inv_issue_master where company_id=$cbo_company_id and entry_form=61 and $year_cond=".date('Y',time())." order by id desc ", "issue_number_prefix","issue_number_prefix_num"));
		//$id=return_next_id( "id", "inv_issue_master", 1 ) ;

		/*
		|--------------------------------------------------------------------------
		| inv_issue_master
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,$cbo_company_id,"KGIR",61,date("Y",time()),13 ));
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',1,".$cbo_location.",".$cbo_store_name.",61,13,".$cbo_company_id.",".$txt_fso_no.",".$hdn_fso_id.",".$txt_issue_date.",".$cbo_party.",".$txt_booking_no.",".$hdn_booking_id.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		/*echo "10**Failed";
		print_r($new_mrr_number);
		die;*/

		$barcodeNos='';
		$all_prod_id='';
		for($j=1;$j<=$tot_row;$j++)
		{
			$recvBasis="recvBasis_".$j;
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
			
			$yarnLot="yarnLot_".$j;
			$yarnCount="yarnCount_".$j;
			$colorId="colorId_".$j;
			$stichLn="stichLn_".$j;
			$brandId="brandId_".$j;

			$floor="floorId_".$j;
			$room="roomId_".$j;

			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$bin="bin_".$j;

			$rollNo="rollNo_".$j;
			$locationId="locationId_".$j;
			$machineId="machineId_".$j;
			$roll_rate="rollRate_".$j;
			$issueRtnRollId="issueRtnRollId_".$j;
			$bookWithoutOrder="bookWithoutOrder_".$j;
			$smnBooking="smnBooking_".$j;
			$isSalesOrder="isSalesOrder_".$j;
			$orderNo="orderNo_".$j;
			$storeId="storeId_".$j;
			$bodyPartId="bodyPartId_".$j;

			$yarnRate="yarnRate_".$j;
			$knittingCharge="knittingCharge_".$j;

			$cons_rate = str_replace("'", "", $$roll_rate);
			$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

			//$cons_rate=$$roll_rate;
			//$cons_amount=$cons_rate*$$rollWgt;
			
			// ==============================start
			if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
			{
				if($$bookWithoutOrder == 1)
				{
					echo "20**Sorry! This barcode ". $$barcodeNo ." doesn't belong to this booking ".$$smnBooking ."";
				}
				else{
					echo "20**Sorry! This barcode ". $$barcodeNo ." doesn't belong to this order/FSO ".$$orderNo ."";
				}
				disconnect($con);
				die;
			}
			if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format($$rollWgt,2,".",""))
			{
				echo "20**Sorry! This barcode (". $$barcodeNo .") is split. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".$$rollWgt ."";
				disconnect($con);
				die;
			}
			// =============================end
			// echo "20**Failed";die;
			/*
			|--------------------------------------------------------------------------
			| inv_transaction
			| data preparing for
			| $data_array_trans
			|--------------------------------------------------------------------------
			|
			*/
			$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			if($data_array_trans!="") $data_array_trans.=",";
			$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,2,".$txt_issue_date.",'".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$brandId."','".$$locationId."','".$$machineId."','".$$stichLn."','".$$floor."','".$$room."','".$$rack."','".$$shelf."','".$$bin."','".$$storeId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$bodyPartId."')";

			/*
			|--------------------------------------------------------------------------
			| inv_grey_fabric_issue_dtls
			| data preparing for
			| $data_array_dtls
			|--------------------------------------------------------------------------
			|
			*/
			$dtls_id = return_next_id_by_sequence("INV_GREY_FAB_ISS_DTLS_PK_SEQ", "inv_grey_fabric_issue_dtls", $con);
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$transactionID.",'".$$recvBasis."','".$$progBookPiId."','".$$productId."','".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$colorId."','".$$locationId."','".$$machineId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$floor."','".$$room."','".$$rack."','".$$shelf."','".$$bin."','".$$storeId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",'".$$bodyPartId."','".$$yarnRate."','".$$knittingCharge."')";

			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data preparing for
			| $data_array_roll
			|--------------------------------------------------------------------------
			|
			*/
			$is_service=1;
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",'".$$orderId."',61,'".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$smnBooking."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$isSalesOrder.",".$$hiddenQtyInPcs.",".$is_service.")";

			/*
			|--------------------------------------------------------------------------
			| order_wise_pro_details
			| data preparing for
			| $data_array_prop
			|--------------------------------------------------------------------------
			|
			*/
			if($$bookWithoutOrder!=1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transactionID.",2,61,'".$dtls_id."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$isSalesOrder.",".$$hiddenQtyInPcs.")";
				//$id_prop = $id_prop+1;
			}

			$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$transactionID."__".$id_roll.",";
			$prodData_array[$$productId]+=$$rollWgt;
			$prodData_amount[$$productId]+=$cons_amount;
			$all_prod_id.=$$productId.",";

			$inserted_roll_id_arr[$id_roll] =  $id_roll;
			$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
		}
		//echo $data_array_dtls."***".$data_array_trans."***".$data_array_roll;die;
		//echo "10**insert into inv_grey_fabric_issue_dtls  ($field_array_dtls) values ($data_array_dtls)";die;
		
		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data preparing for
		| $data_array_prod_update
		|--------------------------------------------------------------------------
		|
		*/
		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(explode(",",substr($all_prod_id,0,-1))));
		$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$issue_amount=$prodData_amount[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]-$issue_qty;
			$stock_value=$row[csf('stock_value')]-$issue_amount;
			if($current_stock > 0)
			{
				$avg_rate=$stock_value/$current_stock;
			}
			else
			{
				$avg_rate=0;
			}		

			if(is_nan($avg_rate))
				$avg_rate=0;
			// if Qty is zero then rate & value will be zero
			if ($current_stock<=0) 
			{
				$stock_value=0;
				$avg_rate=0;
			}

			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*'".$stock_value."'*'".$avg_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		/*
		|--------------------------------------------------------------------------
		| inv_issue_master
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="id,issue_number_prefix,issue_number_prefix_num,issue_number,issue_basis,location_id,store_id,entry_form,item_category,company_id,fso_no,fso_id,issue_date,supplier_id,booking_no,booking_id,remarks,inserted_by,insert_date";

		// echo "10**insert into inv_issue_master  ($field_array) values ($data_array)";die;
		$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		
		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity,cons_rate,cons_amount,brand_id,location_id,machine_id,stitch_length,floor_id,room,rack,self,bin_box,store_id,inserted_by,insert_date,body_part_id";
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		
		/*
		|--------------------------------------------------------------------------
		| inv_grey_fabric_issue_dtls
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls="id,mst_id,trans_id,basis,program_no,prod_id,issue_qnty,rate, amount,color_id,location_id,machine_id,stitch_length,yarn_lot,yarn_count,brand_id,floor_id,room,rack,self,bin_box,store_name,inserted_by,insert_date,qty_in_pcs,body_part_id,yarn_rate,kniting_charge";
		$rID3=sql_insert("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_roll="id,barcode_no,mst_id,dtls_id, po_breakdown_id,entry_form,qnty,rate,amount,roll_no,roll_id,booking_without_order,booking_no,inserted_by,insert_date,is_sales,qc_pass_qnty_pcs,is_service";
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$rID5=true;
		if($data_array_prop!="")
		{
			$field_array_prop="id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,quantity,inserted_by,insert_date,is_sales,quantity_pcs";
			$rID5=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
		}
		
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| is_returned data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$rID6=execute_query("update pro_roll_details set is_returned=1 where barcode_no in (". implode(',', $new_inserted).") and id not in (".implode(',', $inserted_roll_id_arr).")");

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));

		// echo "10**insert into pro_roll_details  ($field_array_roll) values ($data_array_roll)";die;
	    // echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$prodUpdate;die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate)
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
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $prodUpdate)
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
		$check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no in($txt_issue_no) and status_active=1 and is_deleted=0", "sys_number");
		if ($check_in_gate_pass != "")
		{
			echo "20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";disconnect($con);
			die;
		}

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
        /*
         * List of fields that will not change/update on update button event
         * fields=> issue_purpose*knit_dye_source*knit_dye_company*
         * data=> $cbo_issue_purpose."*".$cbo_dyeing_source."*".$cbo_dyeing_comp."*".
         */

        /*$sql_nxt_process = sql_select("select count(b.barcode_no)  rcv_by_batch from pro_roll_details b,
        	(select barcode_no from pro_roll_details where mst_id = $update_id and entry_form = 61 and status_active =1) a
        	where b.barcode_no = a.barcode_no and b.entry_form =62 and status_active =1");*/
		$sql_get_barcode = sql_select("select barcode_no from pro_roll_details where mst_id = $update_id and entry_form = 61 and status_active =1 and is_service=1");

		$allBarcode="";
		foreach ($sql_get_barcode as $row) {
			$allBarcode.=$row[csf("barcode_no")].",";
		}
		$allBarcode=rtrim($allBarcode,",");
		$allBarcodes=explode(",",$allBarcode);
		$allBarcodes=array_chunk($allBarcodes,999);
		$barcode_cond=" and";
		foreach($allBarcodes as $all_barcodes)
		{
			if($barcode_cond==" and")  $barcode_cond.="(barcode_no in(".implode(',',$all_barcodes).")"; else $barcode_cond.=" or barcode_no in(".implode(',',$all_barcodes).")";
		}
		$barcode_cond.=")";
		//echo $pi_qnty_cond;die;
		/*$sql_nxt_process = sql_select("select count(b.barcode_no) rcv_by_batch from pro_roll_details b where b.entry_form =62 and b.status_active =1 $barcode_cond");

		if($sql_nxt_process[0][csf("rcv_by_batch")] > 0)
		{
			echo "Next process found"; die;
		}*/

        /*
         * List of fields that will not change/update on update event
         * fields=>company_id,
         * data=> $cbo_company_id.",'".
         */

        $barcodeNos='';
		$all_prod_id='';
		$all_roll_id='';
        $all_scanned_barcode_no = chop($new_barcode_nos,",");
        $all_scanned_barcode_arr = array_filter(explode(",",$all_scanned_barcode_no));
        $scannedNewBarcodeCond="";
		$barCond="";
        if($db_type==2 && count($all_scanned_barcode_arr)>999)
        {
        	$all_scanned_barcode_chunk=array_chunk($all_scanned_barcode_arr,999) ;
        	foreach($all_scanned_barcode_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$barCond.="  a.barcode_no in($chunk_arr_value) or ";
        	}

        	$scannedNewBarcodeCond.=" and (".chop($barCond,'or ').")";
        }
        else
        {
        	$scannedNewBarcodeCond=" and a.barcode_no in($all_scanned_barcode_no)";
        }

        if($all_scanned_barcode_no)
        {
        	$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 $scannedNewBarcodeCond and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0 and a.is_service=1");

        	if($check_if_already_scanned[0][csf("barcode_no")]!="")
        	{
        		echo "20**Sorry! Barcode already Scanned. Challan No: ".$check_if_already_scanned[0][csf("issue_number")]." Barcode No ".$check_if_already_scanned[0][csf("barcode_no")];
				disconnect($con);
        		die;
        	}
        }

        if($all_scanned_barcode_no)
        {
        	$trans_check_sql = sql_select("select a.barcode_no,a.entry_form,a.po_breakdown_id, a.qnty from pro_roll_details a where a.entry_form in (22,58,83,133,82,180,110,183,84) and a.re_transfer =0 $scannedNewBarcodeCond and a.status_active = 1 and a.is_deleted = 0 union all select a.barcode_no,a.entry_form,a.po_breakdown_id, a.qnty from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in (2) and b.trans_id<>0 and a.re_transfer =0 $scannedNewBarcodeCond and a.status_active = 1 and a.is_deleted = 0");

        	if($trans_check_sql[0][csf("barcode_no")] !="")
        	{
        		foreach ($trans_check_sql as $val)
        		{
        			$trans_po_barcode_check_arr[$val[csf("barcode_no")]][$val[csf("po_breakdown_id")]] = $val[csf("barcode_no")]."__".$val[csf("po_breakdown_id")];
        			$actual_wgt_arr[$val[csf("barcode_no")]] = $val[csf("qnty")];
        		}
        	}
        }

		//echo "10**";print_r($all_scanned_barcode_no);die;

        for($j=1;$j<=$tot_row;$j++)
        {
        	$recvBasis="recvBasis_".$j;
        	$barcodeNo="barcodeNo_".$j;
        	$progBookPiId="progBookPiId_".$j;
        	$productId="productId_".$j;
        	$orderId="orderId_".$j;
        	$rollId="rollId_".$j;
        	$rollWgt="rollWgt_".$j;
			$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
        	$yarnLot="yarnLot_".$j;
        	$yarnCount="yarnCount_".$j;
        	$colorId="colorId_".$j;
        	$stichLn="stichLn_".$j;
        	$brandId="brandId_".$j;

        	$floor="floorId_".$j;
			$room="roomId_".$j;
        	$rack="rack_".$j;
        	$shelf="shelf_".$j;
        	$bin="bin_".$j;

        	$dtlsId="dtlsId_".$j;
        	$transId="transId_".$j;
        	$rolltableId="rolltableId_".$j;
        	$rollNo="rollNo_".$j;
        	$locationId="locationId_".$j;
        	$machineId="machineId_".$j;
        	$roll_rate="rollRate_".$j;
        	$bookWithoutOrder="bookWithoutOrder_".$j;
        	$orderNo="orderNo_".$j;
        	$storeId="storeId_".$j;
        	$bodyPartId="bodyPartId_".$j;
        	$smnBooking="smnBooking_".$j;
        	$isSalesOrder="isSalesOrder_".$j;
        	$yarnRate="yarnRate_".$j;
			$knittingCharge="knittingCharge_".$j;

        	$cons_rate = str_replace("'", "", $$roll_rate);
			$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

        	//$cons_rate=$$roll_rate;
        	//$cons_amount=$cons_rate*$$rollWgt;

        	if($$rolltableId>0)
        	{
        		$transId_arr[$$transId]=$$transId;
        		$data_array_update_trans[$$transId]=explode("*",($txt_issue_date."*'".$$rollWgt."'*'".$cons_rate."'*'".$cons_amount."'*'".$$brandId."'*'".$$locationId."'*'".$$machineId."'*'".$$stichLn."'*'".$$floor."'*'".$$room."'*'".$$rack."'*'".$$shelf."'*'".$$bin."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

        		$dtlsId_arr[]=$$dtlsId;
        		$data_array_update_dtls[$$dtlsId]=explode("*",($$rollWgt."*'".$cons_rate."'*'".$cons_amount."'*'".$$colorId."'*'".$$locationId."'*'".$$machineId."'*'".$$stichLn."'*'".$$yarnLot."'*'".$$yarnCount."'*'".$$brandId."'*'".$$floor."'*'".$$room."'*'".$$rack."'*'".$$shelf."'*'".$$bin."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$hiddenQtyInPcs));

        		$rollId_arr[]=$$rolltableId;
        		$data_array_update_roll[$$rolltableId]=explode("*",("'".$$rollWgt."'*'".$cons_rate."'*'".$cons_amount."'*'".$$rollNo."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$$isSalesOrder."*".$$hiddenQtyInPcs));

        		$barcodeNos.=$$barcodeNo."__".$$dtlsId."__".$$transId."__".$$rolltableId.",";
        		$dtlsId_prop=$$dtlsId;
        		$transId_prop=$$transId;
        		$all_roll_id.=$$rolltableId.",";
        	}
        	else
        	{
				if($all_scanned_barcode_no)
				{

					if($trans_po_barcode_check_arr[str_replace("'", "", $$barcodeNo)][str_replace("'", "", $$orderId)] != str_replace("'", "", $$barcodeNo)."__".str_replace("'", "", $$orderId))
					{
						if($$bookWithoutOrder == 1)
						{
							echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this booking no =  ".$$smnBooking ."";
						}
						else{
							echo "20**Sorry! This barcode =". $$barcodeNo ." doesn't belong to this order/fso no =  ".$$orderNo ."";
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
				}

				/*
				|--------------------------------------------------------------------------
				| inv_transaction
				| data preparing for
				| $data_array_trans
				|--------------------------------------------------------------------------
				|
				*/
				$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans[$transactionID] ="(".$transactionID.",".$update_id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",".$$productId.",13,2,".$txt_issue_date.",'".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$brandId."','".$$locationId."','".$$machineId."','".$$stichLn."','".$$floor."','".$$room."','".$$rack."','".$$shelf."','".$$bin."','".$$storeId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$bodyPartId."')";

				/*
				|--------------------------------------------------------------------------
				| inv_grey_fabric_issue_dtls
				| data preparing for
				| $data_array_dtls
				|--------------------------------------------------------------------------
				|
				*/
				$dtls_id = return_next_id_by_sequence("INV_GREY_FAB_ISS_DTLS_PK_SEQ", "inv_grey_fabric_issue_dtls", $con);
				$data_array_dtls[$dtls_id] ="(".$dtls_id.",".$update_id.",".$transactionID.",'".$$recvBasis."','".$$progBookPiId."','".$$productId."','".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$colorId."','".$$locationId."','".$$machineId."','".$$stichLn."','".$$yarnLot."','".$$yarnCount."','".$$brandId."','".$$floor."','".$$room."','".$$rack."','".$$shelf."','".$$bin."','".$$storeId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.",'".$$bodyPartId."','".$$yarnRate."','".$$knittingCharge."')";

				/*
				|--------------------------------------------------------------------------
				| pro_roll_details
				| data preparing for
				| $data_array_roll
				|--------------------------------------------------------------------------
				|
				*/
				$is_service=1;
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$data_array_roll[$id_roll] ="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",'".$$orderId."',61,'".$$rollWgt."','".$cons_rate."','".$cons_amount."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."','".$$smnBooking."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$isSalesOrder.",".$$hiddenQtyInPcs.",".$is_service.")";

				$dtlsId_prop=$dtls_id;
				$transId_prop=$transactionID;
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$transactionID."__".$id_roll.",";

				$new_array_inserted[str_replace("'", "", $$barcodeNo)] = $id_roll;
			}

			$prodData_array[$$productId]+=$$rollWgt;
			$prodData_amount[$$productId]+=$cons_amount;
			$all_prod_id.=$$productId.",";

			/*
			|--------------------------------------------------------------------------
			| order_wise_pro_details
			| data preparing for
			| $data_array_prop
			|--------------------------------------------------------------------------
			|
			*/
			if($$bookWithoutOrder!=1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop[$id_prop] ="(".$id_prop.",".$transId_prop.",2,61,'".$dtlsId_prop."','".$$orderId."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$isSalesOrder.",".$$hiddenQtyInPcs.")";
				//$id_prop = $id_prop+1;
			}
		}


		$txt_deleted_id=str_replace("'","",$txt_deleted_id);
		$adj_prod_array=array();
		$update_dtls_id='';
		$update_trans_id='';
		$update_delete_dtls_id='';
		
		if($txt_deleted_id!="")
			$all_roll_id=$all_roll_id.$txt_deleted_id;
		else
			$all_roll_id=substr($all_roll_id,0,-1);
		
		$deleted_id_arr=explode(",",$txt_deleted_id);
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
			$rollData=sql_select("select a.id, a.qnty, b.id as dtls_id, b.trans_id, b.prod_id,a.amount from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=61 $roll_id_cond"); //and a.id in($all_roll_id)
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
		$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]+$adj_prod_array[$row[csf('id')]]-$issue_qty;
			//$stock_value=$current_stock*$row[csf('avg_rate_per_unit')];
			$stock_value=$row[csf('stock_value')]+$adj_prod_amount[$row[csf('id')]]-$prodData_amount[$row[csf('id')]];
			$avg_rate=$stock_value/$current_stock;
			$prod_id_array[$row[csf('id')]]=$row[csf('id')];
			if(is_nan($avg_rate) || is_infinite($avg_rate) ) $avg_rate=0;
			// if Qty is zero then rate & value will be zero
			if ($current_stock<=0) 
			{
				$stock_value=0;
				$avg_rate=0;
			}

			$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$prodData_array[$row[csf('id')]]."'*'".$current_stock."'*'".$stock_value."'*'".$avg_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}
		
		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data deleting here
		|--------------------------------------------------------------------------
		|
		*/
		$update_dtls_id = chop($update_dtls_id,",");
		$update_dtls_id_arr = array_filter(explode(",",$update_dtls_id));
		$update_dtls_id_cond=""; $upDtlsIdCond="";
		if($db_type==2 && count($update_dtls_id_arr)>999)
		{
			$update_dtls_id_chunk=array_chunk($update_dtls_id_arr,999) ;
			foreach($update_dtls_id_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$upDtlsIdCond.="  dtls_id in($chunk_arr_value) or ";
			}

			$update_dtls_id_cond.=" and (".chop($upDtlsIdCond,'or ').")";
		}
		else
		{
			$update_dtls_id_cond=" and dtls_id in($update_dtls_id)";
		}

		if($update_dtls_id != "")
		{
			//$delete_prop=execute_query("delete from order_wise_pro_details where dtls_id in(".substr($update_dtls_id,0,-1).") and entry_form=61",0);
			$delete_prop=execute_query("delete from order_wise_pro_details where entry_form=61 $update_dtls_id_cond",0);
		}
		
		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="location_id*issue_date*remarks*store_id*updated_by*update_date";
		$data_array=$cbo_location."*".$txt_issue_date."*".$txt_remarks."*".$cbo_store_name."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("inv_issue_master",$field_array,$data_array,"id",$update_id,0);
		
		$rID2=1;
		$rID3=1;
		$rID4=1;
		$rID5=1;
		$rID6=1;
		$rID7=1;
		$rID8=1;
		$statusChangeTrans=1;
		$statusChangeDtls=1;
		$statusChangeRoll=1;
		$isReturnedFlag=1;

		if(count($data_array_dtls)>0)
		{
			/*
			|--------------------------------------------------------------------------
			| inv_transaction
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,prod_id,item_category,transaction_type,transaction_date, cons_quantity,cons_rate, cons_amount,brand_id,location_id,machine_id,stitch_length,floor_id,room,rack,self,bin_box,store_id,inserted_by,insert_date,body_part_id";
			$data_array_trans_set=array_chunk($data_array_trans,200);
			foreach( $data_array_trans_set as $setRows)
			{
				//echo "10** insert into inv_transaction ($field_array_trans) values ".implode(",",$setRows);oci_rollback($con);die;
				$rID2=sql_insert("inv_transaction",$field_array_trans,implode(",",$setRows),0);
				if($rID2==1)
					$flag=1;
				else if($rID2==0)
				{
					$flag=0;
					oci_rollback($con);
					echo "10**";
					disconnect($con);
					die;
				}
			}

			/*
			|--------------------------------------------------------------------------
			| inv_grey_fabric_issue_dtls
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_dtls="id,mst_id,trans_id,basis,program_no,prod_id,issue_qnty,rate,amount,color_id,location_id,machine_id,stitch_length,yarn_lot,yarn_count,brand_id,floor_id,room,rack,self,bin_box,store_name,inserted_by,insert_date,qty_in_pcs,body_part_id,yarn_rate,kniting_charge";
			$data_array_dtls_set=array_chunk($data_array_dtls,200);
			foreach( $data_array_dtls_set as $setRows)
			{
				$rID3=sql_insert("inv_grey_fabric_issue_dtls",$field_array_dtls,implode(",",$setRows),0);
				if($rID3==1) $flag=1;
				else if($rID3==0)
				{
					$flag=0;
					oci_rollback($con);
					echo "10**";
					disconnect($con);
					die;
				}
			}

			
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data inserting here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_roll="id,barcode_no,mst_id,dtls_id,po_breakdown_id,entry_form,qnty,rate,amount,roll_no,roll_id,booking_without_order,booking_no,inserted_by,insert_date,is_sales,qc_pass_qnty_pcs,is_service";
			$data_array_roll_set=array_chunk($data_array_roll,200);
			foreach($data_array_roll_set as $setRows)
			{
				$rID4=sql_insert("pro_roll_details",$field_array_roll,implode(",",$setRows),0);
				if($rID4==1)
					$flag=1;
				else if($rID4==0)
				{
					$flag=0;
					oci_rollback($con);
					echo "10**";
					disconnect($con);
					die;
				}
			}

			if(!empty($new_array_inserted))
			{
				foreach($new_array_inserted as $nBarcode => $nRollId)
				{
					$isReturnedFlag=execute_query("update pro_roll_details set is_returned=1 where barcode_no =$nBarcode and id <> $nRollId");
					if ($flag == 1)
					{
						if ($isReturnedFlag)
						{
							$flag = 1;
						}
						else
						{
							$flag = 0;
							oci_rollback($con);
							echo "10**";
							disconnect($con);
							die;
						}
					}
				}
			}

		}

		/*if($data_array_dtls!="")
		{
			$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			$rID3=sql_insert("inv_grey_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}*/
		//echo "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr );die;
		//echo "10**".print_r($data_array_update_trans);die;

		if(count($data_array_update_dtls)>0)
		{
			/*
			|--------------------------------------------------------------------------
			| inv_transaction
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
        	$field_array_updatetrans="transaction_date*cons_quantity*cons_rate*cons_amount*brand_id*location_id*machine_id*stitch_length*floor_id*room*rack*self*bin_box*updated_by*update_date";
			$data_array_update_trans_chunk=array_chunk($data_array_update_trans,50,true);
			$transId_up_arr=array_chunk($transId_arr,50,true);
			$count_up_trans=count($transId_up_arr);
			for ($i=0;$i<$count_up_trans;$i++)
			{
				$rID5=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_updatetrans, $data_array_update_trans_chunk[$i], array_values($transId_up_arr[$i] )),1);

				if($rID5 != "1" )
				{
					oci_rollback($con);
					echo "6**0**1";
					disconnect($con);
					die;
				}
			}
			
			if($rID5)
				$flag=1;
			else
				$flag=0;

			/*
			|--------------------------------------------------------------------------
			| inv_grey_fabric_issue_dtls
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_updatedtls="issue_qnty*rate*amount*color_id*location_id*machine_id*stitch_length*yarn_lot*yarn_count*brand_id*floor_id*room*rack*self*bin_box*updated_by*update_date*qty_in_pcs";
			$data_array_update_dtls_chunk=array_chunk($data_array_update_dtls,50,true);
			$dtlsId_up_arr=array_chunk($dtlsId_arr,50,true);
			$count_up=count($dtlsId_up_arr);
			for ($i=0;$i<$count_up;$i++)
			{
				$rID6=execute_query(bulk_update_sql_statement( "inv_grey_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls_chunk[$i], array_values($dtlsId_up_arr[$i] )),1);
				if($rID6 != "1" )
				{
					oci_rollback($con);
					echo "6**0**1";
					disconnect($con);
					die;
				}
			}
			if($rID6)
				$flag=1;
			else
				$flag=0;
			
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_updateroll="qnty*rate*amount*roll_no*updated_by*update_date*is_sales*qc_pass_qnty_pcs";
			$data_array_update_roll_chunk=array_chunk($data_array_update_roll,50,true);
			$rollId_up_arr=array_chunk($rollId_arr,50,true);
			$count_up_rolls=count($rollId_up_arr);
			for ($i=0;$i<$count_up_rolls;$i++)
			{
				$rID7=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll_chunk[$i], array_values($rollId_up_arr[$i] )),1);
				if($rID7 != "1" )
				{
					oci_rollback($con);
					echo "6**0**1";
					disconnect($con);
					die;
				}
			}
			
			if($rID7)
				$flag=1;
			else
				$flag=0;
		}

		/*if(count($data_array_update_dtls)>0)
		{
			$rID5=execute_query(bulk_update_sql_statement( "inv_transaction", "id", $field_array_updatetrans, $data_array_update_trans, $transId_arr ));
			$rID6=execute_query(bulk_update_sql_statement( "inv_grey_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
			$rID7=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr ));
		}*/

		/*
		|--------------------------------------------------------------------------
		| inv_transaction
		| inv_grey_fabric_issue_dtls
		| pro_roll_details
		| data delete @ updating here
		|--------------------------------------------------------------------------
		|
		*/
		if($txt_deleted_id!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$update_trans_id,0);
			$statusChangeDtls=sql_multirow_update("inv_grey_fabric_issue_dtls",$field_array_status,$data_array_status,"id",$update_delete_dtls_id,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}

		/*if($data_array_prop!="")
		{
			$rID8=sql_insert("order_wise_pro_details",$field_array_prop,$data_array_prop,0);
		}*/

		/*
		|--------------------------------------------------------------------------
		| order_wise_pro_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		if(count($data_array_prop)>0)
		{
			$field_array_prop="id,trans_id,trans_type,entry_form,dtls_id,po_breakdown_id,prod_id,quantity,inserted_by,insert_date,is_sales,quantity_pcs";
			$data_array_prop_set=array_chunk($data_array_prop,200);
			foreach($data_array_prop_set as $setRows)
			{
				$rID8=sql_insert("order_wise_pro_details",$field_array_prop,implode(",",$setRows),0);
				if($rID8==1)
					$flag=1;
				else if($rID8==0)
				{
					$flag=0;
					if($db_type==0)
					{
						mysql_query("ROLLBACK");
						echo "10**";
						disconnect($con);
						die;
					}
					else
					{
						oci_rollback($con);
						echo "10**";
						disconnect($con);
						die;
					}
					
				}
			}
		}

		/*
		|--------------------------------------------------------------------------
		| product_details_master
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
		$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, array_values($prod_id_array )));

		//oci_rollback($con);
		// echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$rID7."&&".$rID8."&&".$delete_prop."&&".$prodUpdate."&&".$statusChangeTrans."&&".$statusChangeDtls."&&".$statusChangeRoll."&&".$isReturnedFlag; oci_rollback($con);die;
		//echo "10**".$statusChangeTrans."--".$statusChangeDtls."--".$statusChangeRoll."--".$prodUpdate;die;
		//echo bulk_update_sql_statement("inv_grey_fabric_issue_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr );die;

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $delete_prop && $prodUpdate && $statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $isReturnedFlag)
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
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $rID7 && $rID8 && $delete_prop && $prodUpdate && $statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $isReturnedFlag)
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
	$data=explode("_",$data);
	$roll_id=return_field_value("id","pro_roll_details","entry_form=62 and status_active=1 and is_deleted=0 and barcode_no=$data[0]");
	if($roll_id=="")
	{
		$roll_id=return_field_value("id","pro_roll_details","entry_form=61 and is_returned=1 and roll_used=1 and dtls_id=$data[1] and status_active=1 and is_deleted=0 and barcode_no=$data[0]");
		if($roll_id=="") echo "0"; else echo "2";
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

		function js_set_value(id,posted_account,within_group,buery_id,po_company_id)
		{
			$('#hidden_system_id').val(id);
			$('#hidden_posted_account').val(posted_account);
			$('#hidden_within_group').val(within_group);
			$('#hidden_buery_id').val(buery_id);
			$('#hidden_po_company_id').val(po_company_id);


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
							<input type="hidden" name="hidden_posted_account" id="hidden_posted_account">
							<input type="hidden" name="hidden_within_group" id="hidden_within_group">
							<input type="hidden" name="hidden_buery_id" id="hidden_buery_id">
							<input type="hidden" name="hidden_po_company_id" id="hidden_po_company_id">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
						<td align="center">
							<?
							$search_by_arr=array(1=>"Issue No",2=>"Barcode No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_challan_search_list_view', 'search_div', 'roll_wise_grey_fabric_delivery_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
exit();
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
	if($db_type==2)
	{
		$group_con="LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id desc) as mst_id";
	}
	else
	{
		$group_con="group_concat(mst_id) as mst_id";
	}
	if($search_by==2)
	{
		$barcode_no=trim($data[0]);
		if($barcode_no!='')
		{
			$mst_id= return_field_value("$group_con","pro_roll_details","barcode_no=$barcode_no and entry_form=61 and status_active=1 and is_deleted=0 and is_service=1","mst_id");
		}
	}

	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and a.issue_number like '$search_string'";
		else if($search_by==2)
		{
			if($mst_id!="")
			{
				$search_field_cond="and a.id in($mst_id)";
			}
			else
			{
				$search_field_cond="and a.id in(0)";
			}
		}
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

	$sql = "SELECT a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.location_id, a.store_id, a.issue_date, a.fso_no, a.booking_no, a.supplier_id , a.is_posted_account,sum(c.qnty) as issue_qnty, d.within_group, d.po_buyer, d.buyer_id 
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c, fabric_sales_order_mst d
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.fso_id=d.id and c.entry_form =61 and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.issue_basis=1 and c.is_service=1
	group by a.id, a.insert_date, a.issue_number_prefix_num, a.issue_number, a.location_id, a.store_id, a.issue_date, a.fso_no, a.booking_no, a.supplier_id, a.is_posted_account, d.within_group, d.po_buyer, d.buyer_id order by a.id"; // and c.is_returned=0
	//echo $sql;//die;
	$result = sql_select($sql);

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$store_name_arr=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="70">Issue No</th>
			<th width="60">Year</th>
			<th width="120">Location</th>
			<th width="140">Store</th>
			<th width="110">FSO No</th>
			<th width="100">Issue Quantity</th>
			<th width="100">Booking No</th>
			<th>Issue date</th>
		</thead>
	</table>
	<div style="width:840px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="820" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$buyer_id = ($row[csf('within_group')]==1)?$row[csf('po_buyer')]:$row[csf('buyer_id')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $row[csf('is_posted_account')]; ?>','<? echo $row[csf('within_group')]; ?>','<? echo $buyer_id; ?>','<? echo $row[csf('supplier_id')]; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="70"><p>&nbsp;<? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="120"><p><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</p></td>
					<td width="140"><p><? echo $store_name_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $row[csf('fso_no')]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $row[csf('issue_qnty')]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
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
	$sql = "select id, company_id, issue_number, location_id, store_id, fso_no, fso_id, supplier_id, booking_no, booking_id, issue_date, remarks from inv_issue_master where id=$data and entry_form=61";
	//echo $sql;
	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#txt_issue_no').val('".$row[csf("issue_number")]."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf("issue_date")])."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";

		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";
		echo "$('#cbo_location').attr('disabled','true')".";\n";

		echo "load_drop_down('requires/roll_wise_grey_fabric_delivery_controller', '".$row[csf('location_id')]."_".$row[csf('company_id')]."', 'load_drop_down_store','store_td');\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("store_id")]."';\n";
		echo "$('#cbo_store_name').attr('disabled','true')".";\n";		
		
		//echo "load_drop_down( 'requires/roll_wise_grey_fabric_delivery_controller', ".$row[csf("knit_dye_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_company', 'dyeing_company_td' );\n";
		echo "$('#cbo_party').val(".$row[csf("supplier_id")].");\n";

		echo "$('#txt_fso_no').val('".$row[csf("fso_no")]."');\n";
		echo "$('#hdn_fso_id').val('".$row[csf("fso_id")]."');\n";
		echo "$('#txt_booking_no').val('".$row[csf("booking_no")]."');\n";
		echo "$('#hdn_booking_id').val('".$row[csf("booking_id")]."');\n";

		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
	}

	$sql_get_barcode = sql_select("select barcode_no from pro_roll_details where mst_id =  $data and entry_form = 61 and status_active =1");

	$allBarcode="";
	foreach ($sql_get_barcode as $row) {
		$allBarcode.=$row[csf("barcode_no")].",";
	}
	$allBarcode=rtrim($allBarcode,",");
	$allBarcodes=explode(",",$allBarcode);
	$allBarcodes=array_chunk($allBarcodes,999);
	$barcode_cond=" and";
	foreach($allBarcodes as $all_barcodes)
	{
		if($barcode_cond==" and")  $barcode_cond.="(barcode_no in(".implode(',',$all_barcodes).")"; else $barcode_cond.=" or barcode_no in(".implode(',',$all_barcodes).")";
	}
	$barcode_cond.=")";
	//echo $pi_qnty_cond;die;
	$sql_nxt_process = sql_select("select count(b.barcode_no) rcv_by_batch from pro_roll_details b where b.entry_form =62 and b.status_active =1 $barcode_cond");

	/*$sql_nxt_process = sql_select("select count(b.barcode_no)  rcv_by_batch from pro_roll_details b,
	(select barcode_no from pro_roll_details where mst_id = $data and entry_form = 61 and status_active =1) a
	where b.barcode_no = a.barcode_no and b.entry_form =62 and status_active =1");*/
	if($sql_nxt_process[0][csf("rcv_by_batch")] > 0)
	{
		echo "$('#cbo_dyeing_source').attr('disabled','true')".";\n";
		echo "$('#cbo_dyeing_comp').attr('disabled','true')".";\n";
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

			var total_selected_val=$('#hidden_selected_row_total').val()*1;// txt_individual_qty

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				total_selected_val=total_selected_val+$('#txt_individual_qty' + str).val()*1;
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				total_selected_val=total_selected_val-$('#txt_individual_qty' + str).val()*1;
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#hidden_barcode_nos').val( id );
			$('#hidden_selected_row_total').val( total_selected_val.toFixed(2));

			if(id!=""){
				var no_of_roll = id.split(',').length;
			}else{
				var no_of_roll = "0";
			}
			$('#hidden_selected_row_count').val(no_of_roll);
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


		function change_booking_placeholder()
		{
			if(document.getElementById('chkIsSales').checked)
			{
				$("#txt_booking_no").attr("placeholder", "Full Booking No");
			}
			else
			{
				$("#txt_booking_no").attr("placeholder", "Booking No Prefix");
			}
		}

		var tableFilters =
		{
			col_operation: {
				id: ["total_selected_value_td"],
				//col: [7,14,16,17,18,19,20,21,22,24,25,26],
				col: [20],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ )
			{
				if($("#search"+i).css("display") != "none")
				{
					js_set_value( i );
				}
			}
		}
	</script>

</head>

<body>
	<div align="center" style="width:960px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:960px; margin-left:2px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="1090" border="1" rules="all" class="rpt_table">
					<thead>
						<th  colspan="13">
							<?
							echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",4 );
							?>
						</th>
					</thead>
					<thead>
						<th>Year</th>
						<th>Location</th>
						<th>Job No</th>
						<th>Order No</th>
						<th>File No</th>
						<th>Internal Ref. No</th>
						<th>Barcode No</th>
						<th>Sales Order No</th>
						<th>Transfer Id</th>
						<th>Booking No</th>
						<th>Store Name</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:50px" class="formbutton" />
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
							echo create_drop_down( "cbo_location_name", 120, "select id,location_name from lib_location where company_id=$company_id and id=$cbo_location and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:60px" class="text_boxes"  name="txt_job_no" id="txt_job_no" placeholder="Job No Prefix" />
						</td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
						</td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />
						</td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />
						</td>
						<td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:100px" class="text_boxes" /></td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_sales_order_no" id="txt_sales_order_no" />
						</td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_trans_id" id="txt_trans_id" />
						</td>
						<td align="center">
							<input type="text" style="width:80px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" placeholder=" Booking Prefix" />
							<input type="checkbox" name="chkIsSales" id="chkIsSales" onChange="change_booking_placeholder()"/> <label for="chkIsSales">Is sales order </label>
						</td>
						<td id="store_td">
							<?
							echo create_drop_down("cbo_store_name", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and a.id=$store_id and b.category_type in(13)  order by a.store_name", "id,store_name", 1, "-- All Store--", 0, "");
							?>
						</td>

						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_sales_order_no').value+'_'+document.getElementById('chkIsSales').checked+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_trans_id').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_store_name').value, 'create_barcode_search_list_view', 'search_div', 'roll_wise_grey_fabric_delivery_controller', 'setFilterGrid(\'tbl_list_search\',-1,tableFilters);reset_hide_field();')" style="width:50px;" />
						</td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>

<script>
	var tableFilters =
	{
		col_operation: {
			id: ["value_total_selected_value_td"],
			//col: [7,14,16,17,18,19,20,21,22,24,25,26],
			col: [23],
			operation: ["sum"],
			write_method: ["innerHTML"]
		}
	}
	setFilterGrid("tbl_list_search",-1,tableFilters);
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);

	$location_id=trim($data[0]);
	$order_no=trim($data[1]);
	$company_id =$data[2];
	$file_no =trim($data[3]);
	$ref_no =trim($data[4]);
	$barcode_no =trim($data[5]);
	$booking_no =trim($data[6]);
	$sales_order_no = trim($data[7]);
	$is_sales = trim($data[8]);
	$job_no = trim($data[9]);
	$year = trim($data[10]);
	$trans_id = trim($data[11]);
	$search_category = trim($data[12]);
	$cbo_store_name = trim($data[13]);

	if($cbo_store_name)
	{
		$store_cond_rcv = " and a.store_id =".$cbo_store_name;
		$store_cond_trans = " and b.to_store=".$cbo_store_name;
	}

	//print_r($data);die;
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$store_arr=return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(13)","id","store_name");
	$po_cancel_status_arr=return_library_array( "select id, status_active from wo_po_break_down where status_active =3 and is_deleted=0",'id','status_active');

	$lib_company_arr=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id", "company_name");

	$machine_array=return_library_array("select id, machine_no from lib_machine_name where company_id = $company_id and category_id=1 and status_active=1 and is_deleted=0", "id", "machine_no");

	$lib_supplier_arr=return_library_array( "SELECT a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name");

	$search_field_cond=$search_field_cond2=$booking_cond="";$reference_po_book_cond = "";
	if($order_no!="")
	{
		if($search_category==1) {
			$search_field_cond=" and d.po_number = '$order_no'";
			$reference_po_book_cond=" and d.po_number = '$order_no'";
		}
		else if ($search_category==0 || $search_category==4) {
			$search_field_cond=" and d.po_number like '%$order_no%'";
			$reference_po_book_cond=" and d.po_number like '%$order_no%'";
		}
		else if($search_category==2) {
			$search_field_cond=" and d.po_number like '$order_no%'";
			$reference_po_book_cond=" and d.po_number like '$order_no%'";
		}
		else if($search_category==3) {
			$search_field_cond=" and d.po_number like '%$order_no'";
			$reference_po_book_cond=" and d.po_number like '%$order_no'";
		}
		else {$search_field_cond="";}

	}
	else if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}
	else if($job_no!="")
	{
		$search_field_cond.=" and d.job_no_mst like '%-".substr($year,-2)."-%'";
		if($search_category==1) {
			$search_field_cond.=" and d.job_no_mst = '$job_no'";
			$reference_po_book_cond.=" and d.job_no_mst = '$job_no'";
		}
		else if($search_category==0 || $search_category==4) {
			$search_field_cond.=" and d.job_no_mst like '%$job_no%'";
			$reference_po_book_cond.=" and d.job_no_mst like '%$job_no%'";
		}
		else if($search_category==2) {
			$search_field_cond.=" and d.job_no_mst like '$job_no%'";
			$reference_po_book_cond.=" and d.job_no_mst like '$job_no%'";
		}
		else if($search_category==3) {
			$search_field_cond.=" and d.job_no_mst like '%$job_no'";
			$reference_po_book_cond.=" and d.job_no_mst like '%$job_no'";
		}
		else {$search_field_cond.="";}
	}
	else if($sales_order_no!="")
	{
		if($search_category==1) $sales_order_cond=" and d.job_no = '$sales_order_no'";
		else if($search_category==0 || $search_category==4) $sales_order_cond=" and d.job_no like '%$sales_order_no%'";
		else if($search_category==2) $sales_order_cond=" and d.job_no like '$sales_order_no%'";
		else if($search_category==3) $sales_order_cond=" and d.job_no like '%$sales_order_no'";
		else $sales_order_cond="";
	}
	else if($booking_no!="")
	{
		if($search_category==1) {
			$search_field_cond.=" and e.booking_no  ='$booking_no'";
			$reference_po_book_cond.=" and e.booking_no  ='$booking_no'";
		}
		else if($search_category==0 || $search_category==4) {
			$search_field_cond.=" and e.booking_no  like '%$booking_no%'";
			$reference_po_book_cond.=" and e.booking_no  like '%$booking_no%'";
		}
		else if($search_category==2) {
			$search_field_cond.=" and e.booking_no  like '$booking_no%'";
			$reference_po_book_cond.=" and e.booking_no  like '$booking_no%'";
		}
		else if($search_category==3) {
			$search_field_cond.=" and e.booking_no  like '%$booking_no'";
			$reference_po_book_cond.=" and e.booking_no  like '%$booking_no'";
		}
		else {
			$search_field_cond.="";
		}

		if($search_category==1) $non_order_booking=" and d.booking_no  ='$booking_no'";
		else if($search_category==0 || $search_category==4) $non_order_booking=" and d.booking_no  like '%$booking_no%'";
		else if($search_category==2) $non_order_booking=" and d.booking_no  like '$booking_no%'";
		else if($search_category==3) $non_order_booking=" and d.booking_no  like '%$booking_no'";
		else $non_order_booking="";

		if($db_type == 0){
			$non_order_booking .=	" and year(d.booking_date) = $year";
			$order_year =	" and year(d.insert_date) = $year";

		}else{
			$non_order_booking .=	" and to_char(d.booking_date,'YYYY') = $year";
			$order_year = "and to_char(d.insert_date,'yyyy') = $year";
		}

	}
	else if($trans_id!="" || $ref_no!="")
	{

	}
	else
	{
		echo "<div style='color:red; font-weight:bold; text-align:center;'>Please enter Order No</div>";
		die;
	}

	if($trans_id!="")
	{
		if($trans_id!="")
		{
			$trans_id_cond=" and a.transfer_system_id like '%$trans_id%' and a.transfer_system_id like '%-".substr($year,-2)."-%'";
			$trans_id_cond2=" and a.id =0";
		}
		else
		{
			$trans_id_cond ="";
			$trans_id_cond2="";
		}
	}

	if($db_type == 0)
	{
		$booking_without_order_null_cond =	" c.booking_without_order = '' ";
	}
	else
	{
		$booking_without_order_null_cond =	" c.booking_without_order is null ";
	}

	if($file_no!="")
	{
		if($search_category==1) {
			$search_field_cond.=" and d.file_no = '$file_no'";
			$reference_po_book_cond.=" and d.file_no = '$file_no'";
		}
		else if($search_category==0 || $search_category==4) {
			$search_field_cond.=" and d.file_no like '%$file_no%'";
			$reference_po_book_cond.=" and d.file_no like '%$file_no%'";
		}
		else if($search_category==2) {
			$search_field_cond.=" and d.file_no like '$file_no%'";
			$reference_po_book_cond.=" and d.file_no like '$file_no%'";
		}
		else if($search_category==3) {
			$search_field_cond.=" and d.file_no like '%$file_no'";
			$reference_po_book_cond.=" and d.file_no like '%$file_no'";
		}
		else {
			$search_field_cond.="";
		}
	}
	if($ref_no!="")
	{
		if($search_category==1) {
			$search_field_cond.=" and d.grouping = '$ref_no'";
			$reference_po_book_cond.=" and d.grouping = '$ref_no'";
		}
		else if($search_category==0 || $search_category==4) {
			$search_field_cond.=" and d.grouping like '%$ref_no%'";
			$reference_po_book_cond.=" and d.grouping like '%$ref_no%'";
		}
		else if($search_category==2) {
			$search_field_cond.=" and d.grouping like '$ref_no%'";
			$reference_po_book_cond.=" and d.grouping like '$ref_no%'";
		}
		else if($search_category==3) {
			$search_field_cond.=" and d.grouping like '%$ref_no'";
			$reference_po_book_cond.=" and d.grouping like '%$ref_no'";
		}
		else {
			$search_field_cond.="";
		}
	}

	if($reference_po_book_cond != "")
	{
		if($booking_no!="")
		{
			$sql_book_to_ord = sql_select("select d.id as po_id, e.booking_no from wo_po_break_down d, wo_booking_dtls e, wo_booking_mst f where d.id = e.po_break_down_id and e.booking_no = f.booking_no and f.company_id = $company_id and e.status_active = 1 and e.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 and e.booking_type=1 $reference_po_book_cond and e.booking_no like '%-".substr($year,-2)."-%' ");
		}
		else
		{
			$sql_book_to_ord = sql_select("select d.id as po_id from wo_po_break_down d, wo_po_details_master g where d.job_no_mst = g.job_no and g.status_active = 1 and g.is_deleted = 0 and d.status_active=1 and d.is_deleted=0 and g.company_name = $company_id $reference_po_book_cond $order_year");
		}

		foreach ($sql_book_to_ord as $val)
		{
			$po_ref_arr[$val[csf("po_id")]] = $val[csf("po_id")];
		}

		$all_po_ref_arr = array_filter(array_unique($po_ref_arr));

		if(count($all_po_ref_arr)>0)
		{
			$all_po_ref_no = implode(",", $all_po_ref_arr);
			$poCond = $all_po_ref_cond = "";

			if($db_type==2 && count($all_barcode_no_arr)>999)
			{
				$all_barcode_no_chunk=array_chunk($all_barcode_no_arr,999) ;
				foreach($all_barcode_no_chunk as $chunk_arr)
				{
					$poCond.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
				}

				$all_po_ref_cond.=" and (".chop($poCond,'or ').")";

			}
			else
			{
				$all_po_ref_cond=" and c.po_breakdown_id in($all_po_ref_no)";
			}
		}
	}

	$location_cond="";
	if($location_id>0) $location_cond=" and a.location_id=$location_id";


	$product_arr=return_library_array("select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');

	if ($sales_order_no != '' || $is_sales == 'true')
	{
		//echo "string";
		if($search_category==1)
		{
			if($booking_no!="") $booking_no_cond =" and d.sales_booking_no='$booking_no'"; else $booking_no_cond="";
			if($job_no!="") $job_no_cond =" and d.po_job_no like '%".$job_no."%'";
		}
		else if($search_category==0 || $search_category==4)
		{
			if($booking_no!="") $booking_no_cond =" and d.sales_booking_no like '%$booking_no%'"; else $booking_no_cond="";
			if($job_no!="") $job_no_cond =" and d.po_job_no like '%".$job_no."%'";
		}
		else if($search_category==2)
		{
			if($booking_no!="") $booking_no_cond =" and d.sales_booking_no like '$booking_no%'"; else $booking_no_cond="";
			if($job_no!="") $job_no_cond =" and d.po_job_no like '".$job_no."%'";
		}
		else if($search_category==3)
		{
			if($booking_no!="") $booking_no_cond =" and d.sales_booking_no like '%$booking_no'"; else $booking_no_cond="";
			if($job_no!="") $job_no_cond =" and d.po_job_no like '%".$job_no."'";
		}

		$sales_order = 1;
		$sql="SELECT a.recv_number,a.knitting_source,a.knitting_company, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.booking_no, d.sales_booking_no,d.job_no  sales_order_no,d.within_group,c.is_sales , d.id as po_id, d.po_job_no,b.color_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.re_transfer=0 and c.roll_no>0  $booking_no_cond $job_no_cond $sales_order_cond $location_cond $barcode_cond $trans_id_cond2 $store_cond_rcv  and c.is_service=1
		group by a.recv_number, a.knitting_source,a.knitting_company, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty,c.booking_no,d.sales_booking_no,d.job_no,d.within_group,c.is_sales ,d.id,d.po_job_no,b.color_id";

	}
	else
	{
		//echo "else";
		if(count($all_po_ref_arr)>0 || $barcode_cond != "" || $trans_id!="")
		{
			//echo "if";
			$sql="SELECT a.recv_number,a.knitting_source,a.knitting_company,a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, b.color_id, null as booking_no, c.po_breakdown_id, a.store_id, b.body_part_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.re_transfer=0 and c.roll_no>0 and (c.booking_without_order =0 or $booking_without_order_null_cond) $location_cond $trans_id_cond2 $barcode_cond $all_po_ref_cond $store_cond_rcv  and c.is_service=1
			group by a.recv_number, a.knitting_source,a.knitting_company,a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty,c.booking_no, c.booking_without_order,b.color_id, c.po_breakdown_id, a.store_id, b.body_part_id";
		}

		if($booking_no!="" || $barcode_cond != "" || $trans_id!="")
		{
			// echo "if2";
			if($sql != "")
			{
				$sql .= " union all ";
			}
			$sql .= "SELECT a.recv_number,a.knitting_source,a.knitting_company, a.location_id, b.prod_id, c.barcode_no, c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order, b.color_id , d.booking_no, c.po_breakdown_id, a.store_id, b.body_part_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c , wo_non_ord_samp_booking_mst d
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.re_transfer=0
			and c.booking_without_order=1 $barcode_cond $non_order_booking $location_cond $trans_id_cond2 $store_cond_rcv  and c.is_service=1
			group by a.recv_number,a.knitting_source,a.knitting_company, a.location_id, b.prod_id, c.barcode_no,c.entry_form, c.receive_basis, c.roll_no, c.qnty, c.booking_without_order,b.color_id, d.booking_no, c.po_breakdown_id , a.store_id, b.body_part_id
			";
		}
	}
	//echo $sql; //die;
	$result = sql_select($sql);
	$barcode_arr = array(); $po_nos_arr =array();
	foreach ($result as $row)
	{

		if($po_cancel_status_arr[$row[csf('po_breakdown_id')]]==3 && $row[csf('booking_without_order')] != 1)
		{
			echo "<div style='color:red; font-weight:bold; text-align:center;'>Not Allow Cancelled Order</div>";
			die;
		}


		if ($sales_order == 1 && $row[csf('within_group')] == 1) {
			$sales_within_group = true;
		} else {
			$sales_within_group = false;
		}

		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];

		if($row[csf('booking_without_order')] != 1)
		{
			$po_nos_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
	}

	if(!empty($barcode_arr))
	{

		$barcode_nos = implode(",", $barcode_arr);
		$barCond = $all_barcode_for_program_cond = "";
		if($db_type==2 && count($barcode_arr)>999)
		{
			$barcode_arr_chunk=array_chunk($barcode_arr,999) ;
			foreach($barcode_arr_chunk as $chunk_arr)
			{
				$barCond.=" barcode_no in(".implode(",",$chunk_arr).") or ";
			}
			$all_barcode_for_program_cond.=" and (".chop($barCond,'or ').")";
		}
		else
		{
			$all_barcode_for_program_cond=" and barcode_no in($barcode_nos)";
		}

		$sqlprog = sql_select("select barcode_no,booking_no from pro_roll_details where entry_form=2 and receive_basis=2 and status_active=1 $all_barcode_for_program_cond ");
		$programNoArr = array();

		foreach($sqlprog as $row)
		{
			$programNoArr[$row[csf('barcode_no')]] = $row[csf('booking_no')];
		}

	}

	$po_nos_arr = array_filter(array_unique($po_nos_arr));

	if(count($po_nos_arr)>0)
	{
		$po_nos = implode(",", $po_nos_arr);
		$poCond = $all_po_cond = "";
		if($db_type==2 && count($po_nos_arr)>999)
		{
			$po_nos_arr_chunk=array_chunk($po_nos_arr,999) ;
			foreach($po_nos_arr_chunk as $chunk_arr)
			{
				$poCond.=" d.id in(".implode(",",$chunk_arr).") or ";
			}
			$all_po_cond.=" and (".chop($poCond,'or ').")";
		}
		else
		{
			$all_po_cond=" and d.id in($po_nos)";
		}

		$po_info = sql_select("select d.id as po_id,d.po_number, d.job_no_mst, d.file_no, d.grouping,d.shipment_date, e.booking_no from wo_po_break_down d left join  wo_booking_dtls e on d.id = e.po_break_down_id and e.booking_type in (1,4) and e.status_active = 1 and e.is_deleted = 0 where d.status_active=1 and d.is_deleted=0 $all_po_cond");
		foreach ($po_info as $po_row) {
			$po_no_ref_arr[$po_row[csf('po_id')]]["po_number"] = $po_row[csf('po_number')];
			$po_no_ref_arr[$po_row[csf('po_id')]]["job_no"] = $po_row[csf('job_no_mst')];
			$po_no_ref_arr[$po_row[csf('po_id')]]["book"] .= $po_row[csf('booking_no')].",";
			$po_no_ref_arr[$po_row[csf('po_id')]]["file_no"] = $po_row[csf('file_no')];
			$po_no_ref_arr[$po_row[csf('po_id')]]["grouping"] = $po_row[csf('grouping')];
			$po_no_ref_arr[$po_row[csf('po_id')]]["shipment_date"] = $po_row[csf('shipment_date')];
		}
	}

	$barcode_arr = array_filter(array_unique($barcode_arr));

	if(count($barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $all_barcode_cond = "";

		if($db_type==2 && count($barcode_arr)>999)
		{
			$barcode_arr_chunk=array_chunk($barcode_arr,999) ;
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

	if(!empty($barcode_arr))
	{
		$scanned_barcode_arr=array();
		$barcodeData=sql_select("select a.barcode_no from pro_roll_details a where a.entry_form=61 and a.status_active=1 and a.is_deleted=0
			$all_barcode_cond and a.is_returned <>1 ");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}

		$stitch_lot_sql = sql_select("select a.barcode_no, b.stitch_length, b.yarn_lot, b.machine_no_id from pro_roll_details a,pro_grey_prod_entry_dtls b where a.dtls_id = b.id and a.entry_form in (2,22) and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");
		foreach ($stitch_lot_sql as $row)
		{
			$stitch_lot_arr[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['machine_id'] = $row[csf("machine_no_id")];
		}
	}

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1980" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="120">System Number</th>
			<th width="50">Source</th>
			<th width="100">Party Name</th>
			<th width="100">Body Part</th>
			<th width="160">Fabric Description</th>
			<th width="40">Gsm</th>
			<th width="40">Dia</th>
			<th width="60">Stitch L.</th>
			<th width="90">Yarn Lot</th>
			<th width="90">Machine No</th>
			<th width="90">Job No</th>
			<th width="110">Booking No</th>
			<th width="110">Order/FSO No</th>
			<th width="100">Program No</th>
			<th width="50">Within Group</th>
			<th width="70">Color Name</th>
			<th width="105">Location</th>
			<th width="70">File No</th>
			<th width="70">Ref No</th>
			<th width="65">Shipment Date</th>
			<th width="75">Barcode No</th>
			<th width="40">Roll No</th>
			<th width="50">Roll Qty.</th>
			<th>Store Name</th>
		</thead>
	</table>
	<div style="width:1990px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1972" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;$total_roll_weight=0;
			foreach ($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					$within_group_con=($row[csf('within_group')] == 1)?"Yes":"No";

					$within_group = $row[csf('within_group')];
					$is_sales = $row[csf('is_sales')];
					if ($sales_order == 1)
					{
						$sales_order_order = $row[csf('sales_order_no')];
						$sales_booking_no = $row[csf('sales_booking_no')];
						if ($within_group == 1) {
							$job_no = $row[csf("po_job_no")];
						} else {
							$job_no = '';
							$po_shipdate_no = '';
						}
					}
					else
					{
						if($row[csf('booking_without_order')] == 1)
						{
							$sales_order_order = "";
							$job_no = "";
							$sales_booking_no = $row[csf('booking_no')];
							$po_shipdate_no = "";
							$file_no = "";
							$group_no = "";
						}
						else
						{
							$sales_order_order = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["po_number"];
							$job_no = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["job_no"];
							$sales_booking_no = implode(",",array_unique(explode(",",chop($po_no_ref_arr[$row[csf('po_breakdown_id')]]["book"],","))));
							$file_no = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["file_no"];
							$group_no = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["grouping"];
							$po_shipdate_no = $po_no_ref_arr[$row[csf('po_breakdown_id')]]["shipment_date"];
						}

					}
					$color='';
					$color_id=explode(",",$row[csf('color_id')]);
					foreach($color_id as $val)
					{
						if($val>0) $color.=$color_arr[$val].",";
					}
					$color=chop($color,',');

					$product_data=explode(",",$product_arr[$row[csf('prod_id')]]);

					if ($row[csf('knitting_source')]==1)
					{
						$knitting_comp = $lib_company_arr[$row[csf('knitting_company')]];
					}
					elseif ($row[csf('knitting_source')]==3)
					{
						$knitting_comp = $lib_supplier_arr[$row[csf('knitting_company')]];
					}
					else{
						$knitting_comp = "";
					}

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_individual_qty[]" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
						</td>
						<td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="50"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
						<td width="100"><p><? echo $knitting_comp; ?></p></td>
						<td width="100"><p><? echo $body_part[$row[csf('body_part_id')]]; ?></p></td>
						<td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="40"><p><? echo $product_data[2]; ?></p></td>
						<td width="40"><p><? echo $product_data[3]; ?></p></td>

						<td width="60"><p><? echo $stitch_lot_arr[$row[csf("barcode_no")]]['stitch_length'];//$product_data[3]; ?></p></td>
						<td width="90"><p><? echo $stitch_lot_arr[$row[csf("barcode_no")]]['yarn_lot'];//$product_data[3]; ?></p></td>
						<td width="90"><p><? echo $machine_array[$stitch_lot_arr[$row[csf("barcode_no")]]['machine_id']]; ?></p></td>

						<td width="90"><p><? echo $job_no; ?></p></td>
						<td width="110"><p><? echo $sales_booking_no; ?></p></td>
						<td width="110"><p><? echo $sales_order_order; ?></p></td>
						<td width="100" align="center"><p><? echo $programNoArr[$row[csf('barcode_no')]]; ?></p></td>
						<td width="50" align="center"><p><? echo $within_group_con; ?></p></td>
						<td width="70"><p><? echo $color; ?></p></td>
						<td width="105"><? echo $location_arr[$row[csf('location_id')]]; ?>&nbsp;</td>
						<td width="70"><? echo $file_no; ?>&nbsp;</td>
						<td width="70"><? echo $group_no; ?>&nbsp;</td>
						<td width="65" align="center"><? if($row[csf('booking_without_order')]==1) echo '&nbsp;'; else echo change_date_format($po_shipdate_no); ?></td>
						<td width="75"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						<td align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
					</tr>
					<?
					$i++;
					$total_roll_weight += $row[csf('qnty')];
				}
			}
			?>
		</table>
	</div>
	<table width="1970" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" >
		<tr class="tbl_bottom">
			<td  width="30"></td>
			<td  width="120"></td>
			<td  width="50"></td>
			<td  width="100"></td>
			<td  width="100"></td>
			<td  width="160"></td>
			<td  width="40"></td>
			<td  width="40"></td>
			<td  width="60"></td>
			<td  width="90"></td>
			<td  width="90"></td>
			<td  width="90"></td>
			<td  width="110"></td>
			<td  width="110"></td>
			<td  width="100"></td>
			<td  width="50"></td>
			<td  width="70"></td>
			<td  width="105"></td>
			<td  width="70"></td>
			<td  width="70"></td>
			<td  width="65"></td>
			<td  width="75" ></td>
			<td  width="40" >Total</td>
			<td  width="50" id="value_total_selected_value_td" align="right"><?php echo number_format($total_roll_weight,2); ?></td>
			<td  width="60" ></td>
		</tr>
		<tr class="tbl_bottom">
			<td  width="30"></td>
			<td  width="120"></td>
			<td  width="50"></td>
			<td  width="100"></td>
			<td  width="100"></td>
			<td  width="160"></td>
			<td  width="40"></td>
			<td  width="40"></td>
			<td  width="60"></td>
			<td  width="90"></td>
			<td  width="90"></td>
			<td  width="90"></td>
			<td  width="110"></td>
			<td  width="110"></td>
			<td  width="100"></td>
			<td  width="50"></td>
			<td  width="175"colspan="2">Count of Selected Row =</td>
			<td  width="70"><input type="text"  style="width:50px" class="text_boxes_numeric" name="hidden_selected_row_count" id="hidden_selected_row_count" readonly value="0"></td>
			<td  width="70"></td>

			<td width="150" colspan="2"> Selected Row Total=</td>
			<td colspan="2" align="right"  >
				<input type="text"  style="width:70px" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly value="0">
			</td>
			<td width="60"></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check all
			</td>
			<td align="center" colspan="22" >
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
	    </tr>
	</table>
	<?
	exit();
}

if($action=="populate_barcode_datas")
{
	if($db_type==0)
	{
		$poIds=return_field_value("group_concat(po_breakdown_id order by po_breakdown_id desc) as po_breakdown_id","pro_roll_details","entry_form=83 and status_active=1 and is_deleted=0 and from_roll_id in($data) and re_transfer=0","po_breakdown_id");
	}
	else if($db_type==2)
	{
		$poIds=return_field_value("LISTAGG(po_breakdown_id, ',') WITHIN GROUP (ORDER BY po_breakdown_id desc) as po_breakdown_id","pro_roll_details","entry_form=83 and status_active=1 and is_deleted=0 and from_roll_id in($data) and re_transfer=0","po_breakdown_id");
	}
	echo $poIds;
	exit();
}

if($action=="check_barcode_for_delete")
{
	//echo $data;die;
	$data=explode("_",$data);
	$update_id=$data[0];
	$barcode_nos=rtrim($data[1],",");
	$is_posted_accounts=sql_select("select is_posted_account,issue_number  from inv_issue_master  where id=$update_id");
	if($is_posted_accounts[csf('is_posted_account')]==1)
	{
		echo "1_".$is_posted_accounts[csf('issue_number')];die;
	}
	else
	{


		if($db_type==0)
		{
			$barcode_in_RBbatch=return_field_value("group_concat(barcode_no order by barcode_no desc) as barcode_no","pro_roll_details","entry_form=62 and status_active=1 and is_deleted=0 and barcode_no in ($barcode_nos)","barcode_no");
		}
		else if($db_type==2)
		{
			$barcode_in_RBbatch=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY barcode_no desc) as barcode_no","pro_roll_details","entry_form=62 and status_active=1 and is_deleted=0 and barcode_no in ($barcode_nos)","barcode_no");
		}
		//echo $barcode_in_RBbatch;die;
		//$barcode_in_RBbatch=sql_select(" select barcode_no   from pro_roll_details   where entry_form=62 and barcode_no in ($barcode_nos) ");
		if($barcode_in_RBbatch!="")
		{
			echo "2_".$barcode_in_RBbatch;die;
		}
		else
		{
			echo 0;die;
		}
	}
	exit();
}


if($action=="populate_poIds")
{
	$poIdsData='';
	$dataArray=sql_select("select po_breakdown_id, barcode_no from pro_roll_details where entry_form=83 and status_active=1 and is_deleted=0 and barcode_no in($data) and re_transfer=0");
	foreach($dataArray as $row)
	{
		$poIdsData.=$row[csf('barcode_no')]."_".$row[csf('po_breakdown_id')].",";
	}
	echo substr($poIdsData,0,-1);
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

if($action=="check_report_button")
{
	$sql="select format_id from lib_report_template where template_name='".trim($data)."' and report_id=27 and is_deleted=0 and status_active=1";
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

if($action=="load_scanned_barcode_nos")
{
	$scanned_arr=array();
	//$dataArr=sql_select("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0");
	$issued_barcode_data=sql_select("select a.barcode_no from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data "); // and is_returned !=1 
	foreach($issued_barcode_data as $row)
	{
		$scanned_arr[]=$row[csf('barcode_no')];
	}
	$jsbarcode_array= json_encode($scanned_arr);
	echo $jsbarcode_array;
	exit();

}

if($action=="populate_barcode_data")
{
	$data=explode("_",$data);
	$barcode=$data[0];
	$location_id=$data[1];
	$store_id=$data[2];

	$barcodeData='';
	$po_ids_arr=array();
	$po_details_array=array();
	$barcodeDataArr=array();
	$barcodeBuyerArr=array();
	$transRollIds='';
	$transPoIdsArr=array();
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$po_cancel_status_arr=return_library_array( "select id, status_active from wo_po_break_down where status_active =3 and is_deleted=0",'id','status_active');

	//$tmp_data=explode(",",$data);
	//$tmp_data=array_flip($tmp_data);
	//$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where entry_form=61 and is_returned!=1 and barcode_no in( $data ) and status_active=1 and is_deleted=0");

	$requisition_arr=sql_select( "select a.requisition_status,b.barcode_no from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id = b.mst_id and a.entry_form in (110,180,183)  and b.barcode_no in ($barcode) and a.requisition_status=1 and b.status_active=1");

	if(!empty($requisition_arr))
	{
		echo "999!!barcode is in requision";
		die;
	}
	//$scanned_barcode_data=sql_select("select a.barcode_no, a.entry_form, a.mst_id, a.dtls_id, a.is_returned, a.po_breakdown_id, a.re_transfer, a.booking_without_order from pro_roll_details a where a.barcode_no in($data) and entry_form !=56 and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");

	$scanned_barcode_data=sql_select("SELECT a.barcode_no, a.entry_form, a.mst_id, a.dtls_id, a.is_returned, a.po_breakdown_id, a.re_transfer, a.booking_without_order, b.trans_id
	from pro_roll_details a left join pro_grey_prod_entry_dtls b on a.dtls_id =b.id and entry_form in (2,22,58) and b.trans_id <> 0 
	where a.barcode_no in($barcode) and entry_form !=56 and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0  and a.is_service=1");
	
	$pre_sys_ref = array();
	$sample_to_order_check=0;
	foreach($scanned_barcode_data as $row)
	{
		if($row[csf("entry_form")]==61 && $row[csf("is_returned")] !=1)
		{
			$issue_number=return_field_value("issue_number as issue_number","inv_issue_master","status_active=1 and id='".$row[csf("mst_id")]."'","issue_number");
			echo "99!!".$issue_number;die;
		}
	}
	if(empty($scanned_barcode_data))
	{
		echo "0";die;
	}

	$data_array_receive_basis = sql_select("select a.id, a.company_id, a.recv_number, a.booking_no, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty, c.coller_cuff_size
		from inv_receive_master a, pro_roll_details c
		where c.barcode_no in($barcode) and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and a.entry_form=2 and a.receive_basis in(1,2) and a.status_active=1 and a.is_deleted=0 and a.id=c.mst_id ");
	foreach ($data_array_receive_basis as $row)
	{
		$receive_basis_arr[$row[csf('barcode_no')]]['plan_id'] = $row[csf('booking_no')];
		$receive_basis_arr[$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
		$receive_basis_arr[$row[csf('barcode_no')]]['receive_basis'] = $row[csf('receive_basis')];
		if($row[csf('receive_basis')] == 2)
		{
			$program_no_plan_basis_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
	}
	unset($data_array_receive_basis);


	$program_no_plan_basis_ar = array_filter($program_no_plan_basis_ar);
	if(count($program_no_plan_basis_arr)>0)
	{
		$all_program_id=implode(",",$program_no_plan_basis_arr);
		$program_id_cond=""; $progIds_cond="";
		if($db_type==2 && count($program_no_plan_basis_arr)>999)
		{
			$program_no_plan_basis_chunk=array_chunk($program_no_plan_basis_arr,999) ;
			foreach($program_no_plan_basis_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$progIds_cond.="  a.id in($chunk_arr_value) or ";
			}

			$program_id_cond.=" and (".chop($bokIds_cond,'or ').")";
			//echo $booking_id_cond;die;
		}
		else
		{
			$program_id_cond=" and a.id in($all_program_id)";
		}

		$booking_no_planbasis_sql = sql_select("select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 $program_id_cond");
		foreach ($booking_no_planbasis_sql as  $val)
		{
			$booking_no_plan_basis_arr[$val[csf("id")]] =  $val[csf("booking_no")];
		}
	}

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.location_id, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.machine_no_id, b.brand_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.roll_id as roll_id_prev, b.yarn_rate, b.kniting_charge, c.rate, c.booking_no as bwo, c.booking_without_order, c.is_transfer, c.transfer_criteria, c.is_sales, c.qc_pass_qnty_pcs, a.store_id, d.job_no, d.job_no_prefix_num, d.po_job_no, d.po_company_id, d.sales_booking_no, d.buyer_id, d.within_group, d.booking_id as fso_booking_id
	FROM pro_roll_details c, pro_grey_prod_entry_dtls b, inv_receive_master a, fabric_sales_order_mst d
	WHERE c.barcode_no in($barcode) and c.po_breakdown_id = d.id and c.entry_form in(2,22,58) and a.location_id=$location_id and a.store_id=$store_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.trans_id<>0 and a.entry_form in(2,22,58) and b.id=c.dtls_id and a.id=b.mst_id and c.is_service=1");
	foreach($data_array as $row)
	{
		$color_id_ref_arr[$row[csf("color_id")]] = chop($row[csf("color_id")],",");
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

		$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0 $all_color_id_cond","id","color_name");
	}


	$is_sales_arr = array();
	foreach($data_array as $row)
	{
		$is_sales_arr[$row[csf('barcode_no')]] = $row[csf("is_sales")];
		$booking_no_id = $row[csf('po_breakdown_id')];
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

		if($row[csf("knitting_source")]==1)
		{
			$knit_company=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$knit_company=$supplier_arr[$row[csf("knitting_company")]];
		}

		if($row[csf("entry_form")]==58)
		{
			$roll_id=$row[csf("roll_id_prev")];
		}
		else
		{
			$roll_id=$row[csf("roll_id")];
			//$rate='';
		}

		$color='';
		$color_id=explode(",",$row[csf('color_id')]);
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');

		$count='';
		$yarn_count=explode(",",$row[csf('yarn_count')]);
		foreach($yarn_count as $count_id)
		{
			if($count=='')
				$count=$yarn_count_details[$count_id];
			else
				$count.=",".$yarn_count_details[$count_id];
		}

		$coller_cuff_size = $receive_basis_arr[$barcode_no]['coller_cuff_size'];
		if($row[csf("within_group")] == 1)
		{
			$buyer_id = $row[csf("po_buyer")];
		}else{
			$buyer_id = $row[csf("buyer_id")];
		}
		$buyer_name = $buyer_arr[$buyer_id];

		$barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("barcode_no")]."**".$row[csf("company_id")]."**".$row[csf("roll_no")]."**".$roll_id."**".$row[csf("location_id")]."**".$row[csf("machine_no_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("color_id")]."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("knitting_company")]."**".$knit_company."**".$row[csf("yarn_lot")]."**".$row[csf('yarn_count')]."**".$row[csf("stitch_length")]."**".$row[csf("brand_id")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".number_format($row[csf("qnty")],2,'.','')."**".$row[csf("rate")]."**".$row[csf("booking_without_order")]."**".$count."**".$row[csf("floor_id")]."**".$row[csf("room")]."**".$row[csf("bin_box")]."**".$row[csf("po_breakdown_id")]."**".$buyer_id."**".$row[csf("job_no")]."**".$row[csf("po_job_no")] . "**" . $row[csf("is_sales")] ."**".$row[csf("sales_booking_no")] ."**" .$row[csf("store_id")] ."**" .'0'."**" .$row[csf("qc_pass_qnty_pcs")]."**".$coller_cuff_size."**".$row[csf("body_part_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("yarn_rate")]."**".$row[csf("kniting_charge")]."**".$row[csf("rate")]."**".$row[csf("within_group")]."**".$po_company_id."**".$row[csf("fso_booking_id")];

		// $barcodeBuyerArr[$row[csf('barcode_no')]]=$row[csf("booking_without_order")]."__".$row[csf("po_breakdown_id")]."__".$is_transfer."__".$buyer_id."__".$row[csf("qc_pass_qnty_pcs")];

		$all_barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}
	unset($data_array);

	$all_barcode_no_arr = array_filter(array_unique($all_barcode_no_arr));

	if(count($barcodeDataArr)>0)
	{
		foreach($barcodeDataArr as $barcode_no=>$value)
		{
			$barcodeData.=$value."#";
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
	$po_ids_arr=array(); $po_details_array=array(); $barcodeDataArr=array();
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$machine_array=return_library_array( "select id, machine_no from lib_machine_name where category_id=1", "id", "machine_no");
	$location_array=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$issued_data_arr=$split_from=array();
	$barcode_nos='';
	$issued_barcode_data=sql_select("SELECT a.id, a.barcode_no, a.dtls_id, a.roll_id, a.rate, a.qnty, a.po_breakdown_id, a.booking_without_order, a.is_sales, b.trans_id, a.roll_split_from, b.store_name, b.floor_id, b.room, b.rack, b.self, b.bin_box, a.qc_pass_qnty_pcs, a.is_returned, b.body_part_id, b.yarn_rate, b.kniting_charge from pro_roll_details a, inv_grey_fabric_issue_dtls b where a.dtls_id=b.id and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data and a.is_service=1 "); //and is_returned!=1

	$all_po_breake_id=="";
	foreach($issued_barcode_data as $row)
	{
		$issued_data_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['po_id']=$row[csf('po_breakdown_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['booking_without_order']=$row[csf('booking_without_order')];
		$issued_data_arr[$row[csf('barcode_no')]]['id']=$row[csf('id')];
		$issued_data_arr[$row[csf('barcode_no')]]['roll_id']=$row[csf('roll_id')];
		$issued_data_arr[$row[csf('barcode_no')]]['rate']=$row[csf('rate')];
		$issued_data_arr[$row[csf('barcode_no')]]['yarn_rate']=$row[csf('yarn_rate')];
		$issued_data_arr[$row[csf('barcode_no')]]['kniting_charge']=$row[csf('kniting_charge')];
		$issued_data_arr[$row[csf('barcode_no')]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		$issued_data_arr[$row[csf('barcode_no')]]['qty_in_pcs']=$row[csf("qc_pass_qnty_pcs")]*1;
		$issued_data_arr[$row[csf('barcode_no')]]['store_name']=$row[csf("store_name")];
		$issued_data_arr[$row[csf('barcode_no')]]['body_part_id']=$row[csf("body_part_id")];
		
		$issued_data_arr[$row[csf('barcode_no')]]['is_returned']=$row[csf("is_returned")];

		$barcode_nos.=$row[csf('barcode_no')].',';
		if($row[csf('is_sales')] == 1){
			$sales_ids_arr[] = $row[csf("po_breakdown_id")];
		}else{
			$po_ids_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
		}

		if($row[csf("roll_split_from")] > 0){
			$split_from[$row[csf("barcode_no")]]=$row[csf("roll_id")];
		}

		if($all_po_breake_id=="")
		{
			$all_po_breake_id .=  $row[csf("po_breakdown_id")];
		}else {
			$all_po_breake_id .=  ",".$row[csf("po_breakdown_id")];
		}
	}

	$barcode_nos=chop($barcode_nos,',');
	$barcode_nos_arr =  array_filter(explode(",", $barcode_nos));
	if(count($barcode_nos_arr)>0)
	{
		$all_barcode_nos=implode(",",$barcode_nos_arr);
		$all_barcode_nos_cond=""; $barCond="";
		if($db_type==2 && count($barcode_nos_arr)>999)
		{
			$barcode_nos_chunk=array_chunk($barcode_nos_arr,999) ;
			foreach($barcode_nos_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$barCond.="  c.barcode_no in($chunk_arr_value) or ";
			}
			$all_barcode_nos_cond.=" and (".chop($barCond,'or ').")";
		}
		else
		{
			$all_barcode_nos_cond=" and c.barcode_no in($all_barcode_nos)";
		}
	}
	else
	{
		echo "<p style='font-weight:bold;align:center;width:350px'>Data Not Found</p>";
		die;
	}


	$all_sales_ids = implode(",",array_unique($sales_ids_arr));

	$all_po_breake_id = implode(",",array_unique(explode(",", $all_po_breake_id)));

	if($all_po_breake_id!="")
	{
		$sqlplan = sql_select("SELECT po_id,dtls_id,booking_no FROM ppl_planning_entry_plan_dtls WHERE po_id in ($all_po_breake_id) AND status_active=1 AND is_deleted=0");

		$planDetails = array();
		foreach($sqlplan as $row)
		{
			$planDetails[$row[csf("po_id")]][$row[csf("dtls_id")]]['booking_no'] = $row[csf("booking_no")];
			$planDetails[$row[csf("po_id")]][$row[csf("dtls_id")]]['program_no'] = $row[csf("dtls_id")];
		}
	}

	$without_order_buyer=return_library_array( "SELECT c.barcode_no, a.buyer_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and a.booking_without_order=1 and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0 $all_barcode_nos_cond ","barcode_no","buyer_id"); //and c.barcode_no in($barcode_nos)

	$data_array_receive_basis = sql_select("SELECT a.id, a.company_id, a.recv_number, a.booking_no, a.receive_basis, a.receive_date, a.booking_no,a.knitting_source, a.knitting_company, c.roll_no, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty, c.coller_cuff_size
		from inv_receive_master a, pro_roll_details c
		where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.receive_basis in(1,2,4) and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_nos_cond "); //and c.barcode_no in($barcode_nos)
	foreach ($data_array_receive_basis as $row)
	{
		$receive_basis_arr[$row[csf('barcode_no')]]['plan_id'] = $row[csf('booking_no')];
		$receive_basis_arr[$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
		$receive_basis_arr[$row[csf('barcode_no')]]['receive_basis'] = $row[csf('receive_basis')];
		if($row[csf('receive_basis')]==2)
		{
			$program_no_plan_basis_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
	}

	if(count($program_no_plan_basis_arr)>0)
	{
		$all_program_id=implode(",",$program_no_plan_basis_arr);
		$program_id_cond=""; $progIds_cond="";
		if($db_type==2 && count($program_no_plan_basis_arr)>999)
		{
			$program_no_plan_basis_chunk=array_chunk($program_no_plan_basis_arr,999) ;
			foreach($program_no_plan_basis_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$progIds_cond.="  a.id in($chunk_arr_value) or ";
			}

			$program_id_cond.=" and (".chop($bokIds_cond,'or ').")";
			//echo $booking_id_cond;die;
		}
		else
		{
			$program_id_cond=" and a.id in($all_program_id)";
		}

		$booking_no_planbasis_sql = sql_select("select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 $program_id_cond");
		foreach ($booking_no_planbasis_sql as  $val)
		{
			$booking_no_plan_basis_arr[$val[csf("id")]] =  $val[csf("booking_no")];
		}
	}

	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.location_id, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.machine_no_id, b.brand_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.po_breakdown_id, c.barcode_no, c.roll_no, c.booking_no as bwo, c.booking_without_order, c.is_sales, a.store_id, d.job_no, d.job_no_prefix_num, d.po_job_no, d.po_company_id, d.sales_booking_no, d.buyer_id, d.within_group, d.booking_id as fso_booking_id
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.is_service=1  $all_barcode_nos_cond"); //and c.barcode_no in($barcode_nos)


	foreach($data_array as $row)
	{
		$is_salesOrder=$row[csf("is_sales")];
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

		if($row[csf("knitting_source")]==1)
		{
			$knit_company=$company_name_array[$row[csf("knitting_company")]];
		}
		else if($row[csf("knitting_source")]==3)
		{
			$knit_company=$supplier_arr[$row[csf("knitting_company")]];
		}

		$color='';
		$color_id=array_unique(explode(",",$row[csf('color_id')]));
		foreach($color_id as $val)
		{
			if($val>0) $color.=$color_arr[$val].",";
		}
		$color=chop($color,',');
		// $plan_id = $receive_basis_arr[$row[csf('barcode_no')]]['plan_id'];
		$receive_basis = $receive_basis_arr[$row[csf('barcode_no')]]['receive_basis'];
		$coller_cuff_size = $receive_basis_arr[$row[csf('barcode_no')]]['coller_cuff_size'];

		// $barcode_store_arr[$row[csf("barcode_no")]]["rcv"]=$row[csf("store_id")];

		if($row[csf("within_group")] == 1)
		{
			$buyer_id = $row[csf("po_buyer")];
		}else{
			$buyer_id = $row[csf("buyer_id")];
		}
		$buyer_name = $buyer_name_array[$buyer_id];

		$barcodeDataArr[$row[csf('barcode_no')]]=$row[csf("roll_no")]."**".$row[csf("location_id")]."**".$row[csf("machine_no_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("bwo")]."**".$receive_basis."**".$receive_basis_id."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("color_id")]."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$row[csf("knitting_company")]."**".$knit_company."**".$row[csf("yarn_lot")]."**".$row[csf('yarn_count')]."**".$row[csf("stitch_length")]."**".$row[csf("brand_id")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$row[csf("prod_id")]."**".$row[csf("febric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("booking_without_order")]."**0**".$row[csf("is_sales")]."**".$row[csf("floor_id")]."**".$row[csf("room")]."**".$row[csf("bin_box")]."**".$buyer_id."**".$buyer_name."**".$row[csf("po_breakdown_id")]."**".$row[csf("job_no")]."**".$row[csf("po_job_no")]."**".$row[csf("sales_booking_no")]."**".$row[csf("fso_booking_id")]."**".$row[csf("within_group")];

	}

	// ADJUST SPLITTED ROLL
	$split_from= array_filter($split_from);
	if(count($split_from)>0)
	{
		$splited_barcode_parent=return_library_array( "select id,barcode_no from pro_roll_details where id in(".implode(",",$split_from).")","id","barcode_no");
		foreach ($split_from as $key=>$split_barcode) {
			if($barcodeDataArr[$splited_barcode_parent[$split_barcode]] != ""){
				$barcodeDataArr[$key]=$barcodeDataArr[$splited_barcode_parent[$split_barcode]];
			}
		}
	}

	$i=count($issued_barcode_data);
	foreach($barcodeDataArr as $barcode_no=>$value)
	{
		$barcodeDatas=explode("**",$value);
		$roll_no=$barcodeDatas[0];
		$location_id=$barcodeDatas[1];
		$machine_no_id=$barcodeDatas[2];
		$body_part_name=$barcodeDatas[3];
		$bwo=$barcodeDatas[4];
		$receive_basis=$barcodeDatas[5];
		$receive_basis_id=$barcodeDatas[6];
		$booking_no=$barcodeDatas[7];
		$booking_id=$barcodeDatas[8];
		$color=$barcodeDatas[9];
		$color_id=$barcodeDatas[10];
		$knitting_source_id=$barcodeDatas[11];
		$knitting_source_name=$barcodeDatas[12];
		$knitting_company_id=$barcodeDatas[13];
		$knit_company=$barcodeDatas[14];
		$yarn_lot=$barcodeDatas[15];
		$yarn_count=$barcodeDatas[16];
		$stitch_length=$barcodeDatas[17];
		$brand_id=$barcodeDatas[18];
		//$rack_id=$barcodeDatas[19];
		//$self_id=$barcodeDatas[20];
		$prod_id=$barcodeDatas[21];
		$febric_description_id=$barcodeDatas[22];
		$gsm=$barcodeDatas[23];
		$width=$barcodeDatas[24];
		$booking_without_order=$barcodeDatas[25];
		$sample_without_order=$barcodeDatas[26];
		$is_salesOrder=$barcodeDatas[27];
		//$floor_id=$barcodeDatas[28];
		//$room_id=$barcodeDatas[29];
		//$bin_id=$barcodeDatas[30];
		$buyer_id=$barcodeDatas[31];
		$buyer_name=$barcodeDatas[32];
		// $po_id=$barcodeDatas[33];
		$po_no=$barcodeDatas[34];
		$job_no=$barcodeDatas[35];
		$fso_booking_no=$barcodeDatas[36];
		$fso_booking_id=$barcodeDatas[37];
		$within_group=$barcodeDatas[38];

		$cons_comp=$constructtion_arr[$febric_description_id].", ".$composition_arr[$febric_description_id];

		$dtls_id=$issued_data_arr[$barcode_no]['dtls_id'];
		$trans_id=$issued_data_arr[$barcode_no]['trans_id'];
		$po_id=$issued_data_arr[$barcode_no]['po_id'];
		$roll_table_id=$issued_data_arr[$barcode_no]['id'];
		$roll_id=$issued_data_arr[$barcode_no]['roll_id'];
		$rate=$issued_data_arr[$barcode_no]['rate'];
		$qnty=$issued_data_arr[$barcode_no]['qnty'];
		$qty_in_pcs=$issued_data_arr[$barcode_no]['qty_in_pcs'];
		$coller_cuff_size=$receive_basis_arr[$barcode_no]['coller_cuff_size'];

		$is_returned=$issued_data_arr[$barcode_no]['is_returned'];

		if($is_returned ==1)
		{
			$bgcolor="background-color: #ffa490"; 
			$add_css = "display: none";
			$title = "Returned Barcode";
		}else{
			$bgcolor="";
			$add_css ="";
		}

		// $buyer_id=$without_order_buyer[$barcode_no];
		// $buyer_name=$buyer_name_array[$without_order_buyer[$barcode_no]];

		$programNo = $bwo;
		$store_id = $issued_data_arr[$barcode_no]['store_name'];
		$body_part_id = $issued_data_arr[$barcode_no]['body_part_id'];
		$yarn_rate = $issued_data_arr[$barcode_no]['yarn_rate'];
		$kniting_charge = $issued_data_arr[$barcode_no]['kniting_charge'];
		?>
		<tr id="tr_<? echo $i; ?>" align="center" valign="middle" title="<? echo $title;?>">
			<td width="30" id="sl_<? echo $i; ?>" style="<? echo $bgcolor;?>"><? echo $i; ?></td>
			<td width="70" id="barcode_<? echo $i; ?>" style="<? echo $bgcolor;?>"><? echo $barcode_no; ?></td>
			<td width="50" id="roll_<? echo $i; ?>" style="<? echo $bgcolor;?>"><? echo $roll_no; ?></td>
			<td width="70" id="location_<? echo $i; ?>" style="<? echo $bgcolor;?>"><? echo $location_array[$location_id]; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="70" id="bodyPart_<? echo $i; ?>"><? echo $body_part[$body_part_id]; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="100" id="cons_<? echo $i; ?>" align="left"><? echo $cons_comp; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="50" id="gsm_<? echo $i; ?>"><? echo $gsm; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="50" id="dia_<? echo $i; ?>"><? echo $width; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="50" id="stL_<? echo $i; ?>"><? echo $stitch_length; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="70" id="color_<? echo $i; ?>"><? echo $color; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="70" id="lot_<? echo $i; ?>"><? echo $yarn_lot; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="70" id="count_<? echo $i; ?>">
				<?
				$Ycount='';
				$yarn_count_arr=explode(",",$yarn_count);
				foreach($yarn_count_arr as $count_id)
				{
					if($Ycount=='') $Ycount=$yarn_count_details[$count_id]; else $Ycount.=",".$yarn_count_details[$count_id];
				}
				echo $Ycount;
				?>
			</td>
			<td width="60" align="right" id="rollWeight_<? echo $i; ?>" style="<? echo $bgcolor;?>"><? echo $qnty; ?></td>
			<td width="60" align="right" id="qtyInPcs_<? echo $i; ?>" style="<? echo $bgcolor;?>"><? echo $qty_in_pcs; ?></td>
			<td width="60" align="right" id="collarCuffSize_<? echo $i; ?>" style="<? echo $bgcolor;?>"><? echo $coller_cuff_size; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="60" id="buyer_<? echo $i; ?>"><? echo $buyer_name; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="60" id="bookingNo_<? echo $i; ?>">
				<? echo $fso_booking_no; ?> <br/>------<br/><? echo $programNo; ?>
			</td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="70" id="job_<? echo $i; ?>"><? echo $job_no; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="70" id="order_<? echo $i; ?>" align="left"><? echo $po_no; ?></td>
			<td style="word-break:break-all;<? echo $bgcolor;?>" width="90" id="knitCompany_<? echo $i; ?>"><? echo $knit_company; ?></td>
			<td id="button_<? echo $i; ?>" align="center" style="<? echo $bgcolor;?>">
				<input type="button" id="decrease_<? echo $i;?>" name="decrease[]" style="width:30px;<? echo $add_css;?>" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i;?>);"/>
				<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $barcode_no; ?>"/>
				<input type="hidden" name="recvBasis[]" id="recvBasis_<? echo $i; ?>" value="<? echo $receive_basis_id; ?>"/>
				<input type="hidden" name="progBookPiId[]" id="progBookPiId_<? echo $i; ?>" value="<? echo $booking_id; ?>"/>
				<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $prod_id; ?>"/>
				<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $po_id; ?>"/>
				<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $roll_id; ?>"/>
				<input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $qnty; ?>"/>
                <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $i; ?>" value="<? echo $qty_in_pcs; ?>"/>
				<input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $yarn_lot; ?>"/>
				<input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $yarn_count; ?>"/>
				<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $color_id; ?>"/>
				<input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $stitch_length; ?>"/>
				<input type="hidden" name="locationId[]" id="locationId_<? echo $i; ?>" value="<? echo $location_id; ?>"/>
				<input type="hidden" name="machineId[]" id="machineId_<? echo $i; ?>" value="<? echo $machine_no_id; ?>"/>
				<input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $brand_id; ?>"/>
				<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
				<input type="hidden" name="transId[]" id="transId_<? echo $i; ?>" value="<? echo $trans_id; ?>"/>
				<input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $roll_table_id; ?>"/>
				<input type="hidden" name="rollRate[]" id="rollRate_<? echo $i; ?>" value="<? echo $rate; ?>"/>
				<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $i; ?>" value="<? echo $booking_without_order; ?>"/>
				<input type="hidden" name="smnBooking[]" id="smnBooking_<? echo $i; ?>" value="<? echo $bwo; ?>"/>
				<input type="hidden" name="isSalesOrder[]" id="isSalesOrder_<? echo $i; ?>" value="<? echo $is_salesOrder; ?>"/>
				<input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $store_id; ?>"/>
				<input type="hidden" name="isReturned[]" id="isReturned_<? echo $i; ?>" value="<? echo $is_returned; ?>"/>
				<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $body_part_id; ?>"/>
				<input type="hidden" name="yarnRate[]" id="yarnRate_<? echo $i; ?>" value="<? echo $yarn_rate;?>"/>
                <input type="hidden" name="knittingCharge[]" id="knittingCharge_<? echo $i; ?>" value="<? echo $kniting_charge;?>"/>
			</td>
		</tr>
		<?
		$i--;
	}
	exit();
}

if($action=="sales_roll_issue_challan_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];
	$storeId=$data[4];
	$location=$data[5];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	?>
    <div style="width:1010px;">
    	<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
			<?
				$image_data_array=sql_select("select image_location from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td  align="left" colspan="2" rowspan="4">
				<?
				foreach($image_data_array as $img_row)
				{
					?>
					<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='90' width='100' align="middle" />
					<!-- <span style="height: 90px; width: 100px;"></span> -->
					<?
				}
				?>
			</td>
			<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>

			<tr>
				<td align="center">
					<?
 					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
					foreach ($nameArray as $result)
					{ 
											 
						 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? echo $result[csf('email')];?> 
						 <? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			
			<tr>				
				<td align="center" style="font-size:16px"><strong>Grey Fabric Delivery</strong></td>
			</tr>
        </table> 
        <br>
        <?
            $sql_data= sql_select("SELECT a.challan_no, a.issue_number,a.company_id, a.knit_dye_source, a.knit_dye_company, a.issue_date,a.store_id,a.location_id, b.within_group, b.po_company_id, b.buyer_id, a.coure_tube, a.remarks
            from  inv_issue_master a, fabric_sales_order_mst b
            where a.entry_form=61 and a.fso_id=b.id and a.company_id=$company and a.issue_number='$txt_challan_no' and a.id=$update_id");
		?>
        
		<table width="1250" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">DV TO</td>
                <td width="200">:&nbsp;<strong>
                <? 
                if($sql_data[0][csf('within_group')]==1) 
                {
                	$po_company_id=$sql_data[0][csf('po_company_id')];
                	echo $company_array[$po_company_id]['name'];               	
                }
				else  
				{
					$fso_buyer_id=$sql_data[0][csf('buyer_id')];
					echo $buyer_arr[$fso_buyer_id];
				}
                ?></strong>
                </td>
                
                
                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td style="font-size:16px; font-weight:bold;" width="300">Delivery Challan No.</td>
                <td width="250" align="left">:&nbsp;<strong><? echo $sql_data[0][csf('issue_number')]; ?></strong></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Address</td>
                <td width="800">:&nbsp;
                <? 
                if($sql_data[0][csf('within_group')]==1) 
                {
                	$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$po_company_id"); 
					foreach ($nameArray as $result)
					{
						echo $result[csf('city')].', '.$country_arr[$result[csf('country_id')]].'.'; ?>
						<?
					}
                }
				else  
				{
					$buyerNameArray=sql_select( "select address_1, address_2, country_id from lib_buyer where id=$fso_buyer_id"); 
					foreach ($buyerNameArray as $result)
					{					 
						echo $result[csf('address_1')].', '.$country_arr[$result[csf('country_id')]].'.'; ?>
						<?
					}
				}
                ?>
                </td>
                
                
                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
                <td width="200" align="left">:&nbsp;<? echo change_date_format($sql_data[0][csf('issue_date')]); ?></td>
			</tr>
			<tr>
				<td width="">&nbsp;</td>
		    </tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Buyer</th>
                    <th width="120">Job No</th>
                    <th width="120">FSO No</th>
                    <th width="120">Booking No</th>
                    <th width="70">Color</th>
                    <th width="150">Fabric Type</th>
                    <th width="40">Dia</th>
                    <th width="40">Gsm</th>
                    <th width="40">UOM</th>
                    <th width="70">Delivery Qty</th>         
                    <th width="50">No of Roll</th>
                </tr>
            </thead>
            <?
			$i=1;
			$sql_update=sql_select("SELECT a.id as roll_table_id, a.barcode_no, a.dtls_id, b.trans_id, a.roll_id,a.roll_no, a.po_breakdown_id, a.booking_without_order, a.is_sales, a.reprocess, a.prev_reprocess, 
			b.body_part_id, b.trans_id, b.prod_id, d.gsm, d.dia_width as dia, d.detarmination_id as deter_id, b.color_id, a.qnty ,a.reject_qnty, c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, 
			c.buyer_id, c.po_buyer, c.po_job_no, c.po_company_id, c.within_group, c.style_ref_no, d.unit_of_measure as uom, null as recv_number 
			from pro_roll_details a, inv_grey_fabric_issue_dtls b, fabric_sales_order_mst c , product_details_master d 
			where a.dtls_id=b.id and a.po_breakdown_id=c.id and b.prod_id=d.id and a.entry_form=61 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$update_id and a.is_returned!=1 and a.is_service=1");

			$barcode_NOs="";
			foreach($sql_update as $row)
			{
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['within_group']=$row[csf("within_group")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['po_buyer']=$row[csf("po_buyer")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['buyer_id']=$row[csf("buyer_id")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['po_job_no']=$row[csf("po_job_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['job_no']=$row[csf("job_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['sales_booking_no']=$row[csf("sales_booking_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['style_ref_no']=$row[csf("style_ref_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['batch_no']=$row[csf("batch_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['color_id']=$row[csf("color_id")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['uom']=$row[csf("uom")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['delivery_qnty']+=$row[csf("qnty")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['roll_count']++;

				$data_grouping[$row[csf("fso_id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['barcode_no'].=$row[csf("barcode_no")].',';
				$barcode_NOs.=$row[csf("barcode_no")].",";
				$fso_ids.=$row[csf("fso_id")].",";
			}
			// echo "<pre>";print_r($data_grouping);

			$tot_roll=$tot_qty=0;
			foreach ($data_grouping as $fso_idkey => $fso_idArr) 
			{
				foreach ($fso_idArr as $deter_id => $deter_idArr) 
				{
					foreach ($deter_idArr as $dia => $diaArr) 
					{
						foreach ($diaArr as $gsm => $row) 
						{
							if($row["within_group"] == 1)
							{
								$buyer_id = $row["po_buyer"];
							}else{
								$buyer_id = $row["buyer_id"];
							}
							$buyer_name = $buyer_arr[$buyer_id];
							?>
			            	<tr>
			                    <td width="30"><? echo $i; ?></td>
			                    <td width="100" style="word-break:break-all;" align="center"><? echo $buyer_name; ?></td>
			                    <td width="120" align="center"><? echo $row["po_job_no"]; ?></td>
			                    <td width="60" style="word-break:break-all;" align="center" title="<?=$fso_idkey;?>"><? echo $row["job_no"]; ?></td>
			                    <td width="70" style="word-break:break-all;" align="center"><? echo $row["sales_booking_no"]; ?></td>
			                    <td width="70" style="word-break:break-all;" align="center" title="<?=$row["color_id"];?>"><? echo $color_arr[$row["color_id"]]; ?></td>
			                    <td width="40" align="center" title="<?=$deter_id;?>"><? echo $constructtion_arr[$deter_id].','.$composition_arr[$deter_id]; ?></td>
			                    <td width="70" align="center"><? echo $dia; ?></td>
			                    <td width="60" style="word-break:break-all;" align="center"><? echo $gsm; ?></td>
			                    <td width="70" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
			            		<td width="120" align="right"><? echo number_format($row['delivery_qnty'],2); ?></td>
			                    <td width="50" align="center"><? echo $row['roll_count']; ?></td>
			                </tr>
			            	<?
							$tot_roll+=$row['roll_count'];
							$tot_qty+=$row['delivery_qnty'];
							$i++;
						}
					}
				}
			}
			?>
            <tr> 
                <td align="right" colspan="10"><strong>Total</strong></td>
                <td align="right"><strong><? echo number_format($tot_qty,2,'.',''); ?></strong></td>
                <td align="right"><strong><? echo $tot_roll; ?></strong></td>
			</tr>
		</table>

		<table width="1250" cellspacing="0" align="center" border="0">
			<tr>
				<td width="">&nbsp;</td>
		    </tr>
			<tr style="line-height: 40px;">
				<td style="font-size:16px; font-weight:bold; border: 1px solid; border-right:none;" width="100">Remarks:</td>
                <td width="200" colspan="6" style="border: 1px solid;"><? echo $sql_data[0][csf('remarks')]; ?></td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>
			</tr>
		</table>
	</div>
    <? echo signature_table(124, $company, "1210px"); ?>
   	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<?
	exit();
}
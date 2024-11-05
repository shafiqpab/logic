<?php
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
		echo create_drop_down( "cbo_service_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "","" );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_service_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", $company_id, "" );
	}
	else
	{
		echo create_drop_down( "cbo_service_company", 152, $blank_array,"",1, "-- Select --", 0, "" );
	}
	exit();
}

/* if($action=="check_report_button")
{
	$sql="select format_id from lib_report_template where template_name='".trim($data)."' and report_id=169 and is_deleted=0 and status_active=1";
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
} */

if($action=="company_wise_load")
{
	$company_id = $data;
	$sql="select format_id from lib_report_template where template_name='".$company_id."' and report_id=169 and is_deleted=0 and status_active=1";
	$data_array=sql_select($sql);
	$format_ids = $data_array[0][csf('format_id')];
	$format_id_arr = explode(",",$format_ids);

	if(!empty($format_id_arr))
	{
		foreach ($format_id_arr as $val) 
		{
			if($val==84) 
			{
				echo "$('#print2').show().css('width', '80px');\n";
			}
			if($val==86) 
			{
				echo "$('#print1').show().css('width', '80px');\n";
			}
		}
	}
	else
	{
		echo "$('#print1').hide();\n";
		echo "$('#print2').hide();\n";
	}

	$vari_sql = sql_select("select production_entry from variable_settings_production where company_name = $company_id and variable_list in (66) and status_active=1");
	$variable_textile_sales_maintain=0;
	foreach ($vari_sql as $val) 
	{
		if($val[csf("production_entry")]==2)
		{
			$variable_textile_sales_maintain=1;
		}
	}
	echo "document.getElementById('textile_sales_maintain').value 					= '" . $variable_textile_sales_maintain . "';\n";
	
}



if($action=="load_process")
{
	echo create_drop_down( "cbo_process", 152, $conversion_cost_head_array,"", 1, "-- Select Process --", 11, "","","$data" );
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if (str_replace("'", "", $txt_issue_no) != "") // Gate Pass check
	{
		$check_in_gate_pass = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no=$txt_issue_no and status_active=1 and is_deleted=0", "sys_number");
		if ($check_in_gate_pass != "") {
			echo "20**Gate Pass found.\nGate Pass ID = $check_in_gate_pass";die;
		}
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
		
		if($db_type==0)
			$year_cond="YEAR(insert_date)"; 
		else if($db_type==2)
			$year_cond="to_char(insert_date,'YYYY')";
		else
			$year_cond="";//defined Later
		
		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GIRS', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_mas_batchroll where company_id=$cbo_company_id and entry_form=63 and $year_cond=".date('Y',time())." order by id desc","recv_number_prefix","recv_number_prefix_num"));
		//	$id=return_next_id( "id", "inv_receive_mas_batchroll", 1 ) ;
		//
		
		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$id = return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'GIRS',63,date("Y",time()),13 ));
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',63,".$cbo_company_id.",".$cbo_service_source.",".$cbo_service_company.",".$txt_batch_id.",".$txt_issue_date.",".$cbo_process.",".$txt_wo_no.",".$txt_attention.",".$txt_remarks.",".$hdn_is_sales.",".$hidden_wo_entry_form.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$barcodeNos=''; 
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$batchId="batchId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$colorId="colorId_".$j;
			$rollNo="rollNo_".$j;
			$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
			$bodyPartId="bodyPartId_".$j;
			$widthDiaType="widthDiaType_".$j;
			$serviceCompany="serviceCompany_".$j;
			$gsm="gsm_".$j;
			$diaWidth="diaWidth_".$j;
			$jobNo="jobNo_".$j;
			$bookingWithoutOrder="bookingWithoutOrder_".$j;
			
			$bookingNo="bookingNo_".$j;
			$determinationId="determinationId_".$j;
			$buyerId="buyerId_".$j;
			$dtlsIsSales="dtlsIsSales_".$j;
			
			$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			
			/*
			|--------------------------------------------------------------------------
			| pro_grey_batch_dtls
			| data preparing for
			| $data_array_dtls
			|--------------------------------------------------------------------------
			|
			*/
			if($data_array_dtls!="")
				$data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",'".$$progBookPiId."','".$$productId."','".$$rollWgt."','".$$rollId."','".$$orderId."','".$$colorId."','".$$batchId."',".$cbo_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$hiddenQtyInPcs."','".$$bodyPartId."','".$$widthDiaType."','".$$serviceCompany."','".$$gsm."','".$$diaWidth."','".$$jobNo."','".$$rollNo."','".$$bookingWithoutOrder."','".$$bookingNo."','".$$determinationId."','".$$buyerId."','".$$dtlsIsSales."')";
			
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data preparing for
			| $data_array_roll
			|--------------------------------------------------------------------------
			|
			*/
			if($$bookingWithoutOrder==1)
			{
				$$orderId=$$progBookPiId;
			}
			
			if($data_array_roll!="")
				$data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",'".$$orderId."',63,'".$$rollWgt."','".$$rollNo."','".$$rollId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$hiddenQtyInPcs."','".$$bookingWithoutOrder."','".$$bookingNo."',".$$dtlsIsSales.")";
			
			$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$id_roll.",";

			$all_barcode_nos .= $$barcodeNo.",";
		}

		$all_barcode_nos = chop($all_barcode_nos,",");

		$barcodeData=sql_select("SELECT a.barcode_no, b.recv_number from pro_roll_details a, inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form in(63) and b.entry_form in(63) and a.is_returned=0 and a.is_rcv_done=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($all_barcode_nos)");
		
		if(!empty($barcodeData))
		{
			echo "20**Grey issue to process found.\nIssue no: ".$barcodeData[0][csf("recv_number")]."\nBarcode no: ".$barcodeData[0][csf("barcode_no")];
			oci_rollback($con);
			disconnect($con);
			die;
		}
		
		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
				 
		$field_array="id,recv_number_prefix,recv_number_prefix_num,recv_number,entry_form,company_id,dyeing_source,dyeing_company,batch_id,receive_date,process_id,wo_no,attention,remarks,is_sales,wo_entry_form,inserted_by,insert_date";
		//echo "10**INSERT INTO inv_receive_mas_batchroll(".$field_array.") VALUES".$data_array;oci_rollback($con); die;
		$rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);
		/*
		|--------------------------------------------------------------------------
		| pro_grey_batch_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls="id,mst_id,booking_id,prod_id,roll_wgt,roll_id,order_id,color_id,batch_id,process_id,inserted_by,insert_date,qty_in_pcs,body_part_id,width_dia_type,knitting_company,gsm,width,job_no,roll_no,booking_without_order,booking_no,febric_description_id,buyer_id,is_sales";		
		//echo "10**INSERT INTO pro_grey_batch_dtls(".$field_array_dtls.") VALUES".$data_array_dtls;oci_rollback($con); die;
		$rID2=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting for
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, inserted_by, insert_date,qc_pass_qnty_pcs,booking_without_order,booking_no,is_sales";
		//echo "10**INSERT INTO pro_roll_details(".$field_array_roll.") VALUES".$data_array_roll;oci_rollback($con); disconnect($con);die;
		$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);


		//$rID4=execute_query("update pro_roll_details set re_issued=1 where entry_form=65 and barcode_no in (".$all_barcode_nos.")");
	  	//echo "10**".$rID."=".$rID2."=".$rID3."=".$rID4; oci_rollback($con);disconnect($con);die;

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 )
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
	
		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
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
		
		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$data_array=$cbo_service_source."*".$cbo_service_company."*".$txt_batch_id."*".$txt_issue_date."*".$cbo_process."*".$txt_wo_no."*".$txt_attention."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$barcodeNos='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$barcodeNo="barcodeNo_".$j;
			$progBookPiId="progBookPiId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$batchId="batchId_".$j;
			$rollId="rollId_".$j;
			$rollWgt="rollWgt_".$j;
			$colorId="colorId_".$j;
			$dtlsId="dtlsId_".$j;
			$rolltableId="rolltableId_".$j;
			$rollNo="rollNo_".$j;
			$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
			$bodyPartId="bodyPartId_".$j;
			$widthDiaType="widthDiaType_".$j;
			$serviceCompany="serviceCompany_".$j;
			$gsm="gsm_".$j;
			$diaWidth="diaWidth_".$j;
			$jobNo="jobNo_".$j;
			$bookingWithoutOrder="bookingWithoutOrder_".$j;

			$bookingNo="bookingNo_".$j;
			$determinationId="determinationId_".$j;
			$buyerId="buyerId_".$j;
			$dtlsIsSales="dtlsIsSales_".$j;
			
			if($$rolltableId>0)
			{
				/*
				|--------------------------------------------------------------------------
				| pro_grey_batch_dtls
				| data preparing for
				| $data_array_update_dtls
				|--------------------------------------------------------------------------
				|
				*/
				$dtlsId_arr[]=$$dtlsId;
				$data_array_update_dtls[$$dtlsId]=explode("*",($$rollWgt."*'".$$colorId."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$$hiddenQtyInPcs."'"));
				
				/*
				|--------------------------------------------------------------------------
				| pro_roll_details
				| data preparing for
				| $data_array_update_roll
				|--------------------------------------------------------------------------
				|
				*/
				$rollId_arr[]=$$rolltableId;
				$data_array_update_roll[$$rolltableId]=explode("*",("'".$$rollWgt."'*'".$$rollNo."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$$hiddenQtyInPcs."'"));
				
				$barcodeNos.=$$barcodeNo."__".$$dtlsId."__".$$rolltableId.",";
			}
			else
			{
				/*
				|--------------------------------------------------------------------------
				| pro_grey_batch_dtls
				| data preparing for
				| $data_array_dtls
				|--------------------------------------------------------------------------
				|
				*/
				$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
				if($data_array_dtls!="")
					$data_array_dtls.=",";
				//$data_array_dtls.="(".$dtls_id.",".$update_id.",'".$$progBookPiId."','".$$productId."','".$$rollWgt."','".$$rollId."','".$$orderId."','".$$colorId."','".$$batchId."',".$cbo_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$hiddenQtyInPcs."')";

				$data_array_dtls.="(".$dtls_id.",".$update_id.",'".$$progBookPiId."','".$$productId."','".$$rollWgt."','".$$rollId."','".$$orderId."','".$$colorId."','".$$batchId."',".$cbo_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$hiddenQtyInPcs."','".$$bodyPartId."','".$$widthDiaType."','".$$serviceCompany."','".$$gsm."','".$$diaWidth."','".$$jobNo."','".$$rollNo."','".$$bookingWithoutOrder."','".$$bookingNo."','".$$determinationId."','".$$buyerId."')";
				
				/*
				|--------------------------------------------------------------------------
				| pro_grey_batch_dtls
				| data preparing for
				| $data_array_roll
				|--------------------------------------------------------------------------
				|
				*/
				if($$bookingWithoutOrder==1)
				{
					$$orderId=$$progBookPiId;
				}
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if($data_array_roll!="")
					$data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$update_id.",".$dtls_id.",'".$$orderId."',63,'".$$rollWgt."','".$$rollNo."','".$$rollId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$hiddenQtyInPcs."','".$$bookingWithoutOrder."','".$$bookingNo."',".$$dtlsIsSales.")";
				
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$id_roll.",";
			}
		}
		//echo "10**".$data_array_dtls."==".$data_array_roll; die;
		
		$txt_deleted_id=str_replace("'","",$txt_deleted_id); $update_dtls_id='';
		if($txt_deleted_id!="")
		{
			$rollData=sql_select("select dtls_id from pro_roll_details where id in($txt_deleted_id)");
			foreach($rollData as $row)
			{
				$update_dtls_id.=$row[csf('dtls_id')].",";
			}
		}
		$update_dtls_id=substr($update_dtls_id,0,-1);
		
		$rID2=true;
		$rID3=true;
		$rID4=true;
		$rID5=true;
		$rID6=true;
		$statusChangeDtls=true;
		$statusChangeRoll=true;
		
		if($txt_deleted_id!="" && $txt_deleted_id!=0)
		{
			$delete_sql = sql_select("SELECT a.barcode_no, b.recv_number 
			from pro_roll_details a, inv_receive_mas_batchroll b 
			where a.mst_id=b.id and a.entry_form in(65) and b.entry_form in(65) and a.status_active=1 and a.is_deleted=0 and a.issue_roll_id in ($txt_deleted_id)");

			if(!empty($delete_sql))
			{
				echo "20**AOP receive found.\nReceive no: ".$delete_sql[0][csf("recv_number")]."\nBarcode no: ".$delete_sql[0][csf("barcode_no")];
				oci_rollback($con);
				disconnect($con);
				die;
			}

			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeDtls=sql_multirow_update("pro_grey_batch_dtls",$field_array_status,$data_array_status,"id",$update_dtls_id,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$txt_deleted_id,0);
		}

		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="dyeing_source*dyeing_company*batch_id*receive_date*process_id*wo_no*attention*remarks*updated_by*update_date";
		$rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
		
		if($data_array_dtls!="")
		{
			/*
			|--------------------------------------------------------------------------
			| pro_grey_batch_dtls
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_dtls="id,mst_id,booking_id,prod_id,roll_wgt,roll_id,order_id,color_id,batch_id,process_id,inserted_by,insert_date,qty_in_pcs,body_part_id,width_dia_type,knitting_company,gsm,width,job_no,roll_no,booking_without_order,booking_no,febric_description_id,buyer_id";

			//$field_array_dtls="id,mst_id,booking_id,prod_id,roll_wgt,roll_id,order_id,color_id,batch_id,process_id,inserted_by,insert_date,qty_in_pcs";	
			$rID3=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
			
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_roll="id,barcode_no,mst_id,dtls_id,po_breakdown_id,entry_form,qnty,roll_no,roll_id,inserted_by,insert_date,qc_pass_qnty_pcs,booking_without_order,booking_no,is_sales";
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		}
		//echo "10**insert into pro_grey_batch_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//echo bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr );die;
		if(count($data_array_update_roll)>0)
		{
			/*
			|--------------------------------------------------------------------------
			| pro_grey_batch_dtls
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_updatedtls="roll_wgt*color_id*updated_by*update_date*qty_in_pcs";		
			$rID5=execute_query(bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
			
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data updating for
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_updateroll="qnty*roll_no*updated_by*update_date*qc_pass_qnty_pcs";
			$rID6=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr ));
			//echo  "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_updateroll, $data_array_update_roll, $rollId_arr );die;
		}

		
		//echo "10**insert into pro_grey_batch_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		//echo "10**".$update_dtls_id."##".$txt_deleted_id."##".$rID."&&".$rID2."&&=".$rID3."&&".$rID4."&&".$rID5."&&".$rID6."&&".$statusChangeDtls."&&".$statusChangeRoll."**".substr($barcodeNos,0,-1); oci_rollback($con); die;
		
		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $statusChangeDtls && $statusChangeRoll)
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
	
		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6 && $statusChangeDtls && $statusChangeRoll)
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
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>, 'create_challan_search_list_view', 'search_div', 'grey_fabric_roll_issue_to_subcon_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
			$date_cond="and receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1) $search_field_cond="and recv_number like '$search_string'";
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
	
	$sql = "SELECT id, $year_field recv_number_prefix_num, recv_number, dyeing_source, dyeing_company, receive_date, process_id, batch_id, wo_no, wo_entry_form from inv_receive_mas_batchroll where entry_form=63 and status_active=1 and is_deleted=0 and company_id=$company_id $search_field_cond $date_cond order by id"; 
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="70">Issue No</th>
            <th width="60">Year</th>
            <th width="120">Service Source</th>
            <th width="140">Service Company</th>
            <th width="110">Process</th>
            <th width="110">WO No.</th>
            <th width="100">Batch</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:850px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="830" class="rpt_table" id="tbl_list_search">  
        <?
		$i=1;
		foreach ($result as $row)
		{  
			if ($i%2==0)
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";	
			 
			$dye_comp="&nbsp;";
			if($row[csf('dyeing_source')]==1)
				$dye_comp=$company_arr[$row[csf('dyeing_company')]]; 
			else
				$dye_comp=$supllier_arr[$row[csf('dyeing_company')]];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer;" onClick="js_set_value('<? echo $row[csf('id')]; ?>');"> 
				<td width="40"><? echo $i; ?></td>
				<td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
				<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
				<td width="140"><p><? echo $dye_comp; ?>&nbsp;</p></td>
				<td width="110"><p><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?>&nbsp;</p></td>
				<td width="110"><p><? echo $row[csf('wo_no')]; ?>&nbsp;</p></td>
				<td width="100"><p><? echo $batch_arr[$row[csf('batch_id')]]; ?>&nbsp;</p></td>
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
	$sql = "select id, company_id, recv_number, dyeing_source, dyeing_company, receive_date, batch_id, process_id,wo_no,attention,remarks, is_sales, wo_entry_form from inv_receive_mas_batchroll where id=$data and entry_form=63";
	//echo $sql;
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_issue_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#txt_wo_no').val('".$row[csf("wo_no")]."');\n";
		echo "$('#txt_wo_no').attr('disabled','true')".";\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_process').val(".$row[csf("process_id")].");\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#cbo_service_source').val(".$row[csf("dyeing_source")].");\n";
		echo "load_drop_down( 'requires/grey_fabric_roll_issue_to_subcon_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_service_company').val(".$row[csf("dyeing_company")].");\n";
		
		$batchno = return_field_value("batch_no","pro_batch_create_mst","id='".$row[csf("batch_id")]."'");
		echo "$('#txt_batch_no').val('".$batchno."');\n";	
		echo "$('#txt_batch_id').val(".$row[csf("batch_id")].");\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";

		echo "$('#txt_attention').val('".$row[csf("attention")]."');\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#hdn_is_sales').val('".$row[csf("is_sales")]."');\n";
		echo "$('#hidden_wo_entry_form').val('".$row[csf("wo_entry_form")]."');\n";

		$wo_no = $row[csf("wo_no")];
  	}

  	$wo_po_sql = sql_select("SELECT d.po_break_down_id from wo_booking_mst c, wo_booking_dtls d where c.booking_no=d.booking_no and c.booking_type=3 and d.booking_no='$wo_no' and d.status_active=1 and c.status_active=1 union all select a.po_breakdown_id from dyeing_work_order_mst a, dyeing_work_order_dtls b where a.id=b.mst_id and a.entry_form=418 and a.status_active=1 and b.status_active=1 and a.do_no='$wo_no'");

  	if(!empty($wo_po_sql))
  	{
  		foreach ($wo_po_sql as $val) {
  			$po_id_arr[$val[csf('po_break_down_id')]] = $val[csf('po_break_down_id')];
  		}
  		$po_ids = implode(',', $po_id_arr);
  		echo "$('#txt_po_ids').val('".$po_ids."');\n";
  	}

	exit();	
}

//newly
if($action=="update_greyRollIssueToProcess_details")
{
	$expData=explode("_",$data);
	$company_id=$expData[1];
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$sql="select a.id,a.company_id,a.dyeing_source,b.id as dtls_id,b.batch_id,b.prod_id,b.body_part_id,b.gsm,b.width,b.color_id,b.width_dia_type,b.job_no,b.buyer_id,b.order_id,b.booking_id,b.booking_no,b.febric_description_id,c.mst_id, c.barcode_no, c.id as roll_table_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.booking_without_order  from inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.id = ".$expData[0]." and a.entry_form = 63 and c.entry_form = 63 AND c.roll_no>0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.mst_id = ".$expData[0]."";

	$data_array=sql_select($sql);
	$barcode_nos="";
	foreach($data_array as $row)
	{
		$barcode_nos.=$row[csf('barcode_no')].",";
	}
	$barcode_nos=chop($barcode_nos,",");


	$variable_set_finish=return_field_value("company_name","variable_settings_production","company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3","company_name");

	if ($variable_set_finish) 
	{
		$sql_data="select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
		from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
		where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=33 and f.booking_without_order=0 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id,a.company_id,a.service_source, 
		a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
		union all 
		select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,null as po_breakdown_id,null as po_number, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
		from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
		where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=33 and f.booking_without_order=1 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by a.id,a.company_id,a.service_source, 
		a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no ";
		$data_arrays=sql_select($sql_data);
		$processID=33;
		if(empty($data_arrays) && $processID==33)
		{
			$sql_special_finish="select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=34 and f.booking_without_order=0 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id, null as po_number, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=34 and f.booking_without_order=1 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no"; 
			$data_arrays=sql_select($sql_special_finish);
			$processID=34;
		}
		if(empty($data_arrays) && $processID==34)
		{
			$sql_drying="select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=31 and f.booking_without_order=0 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,null as po_breakdown_id,null as po_number,null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=31 and f.booking_without_order=1 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no"; 
			$data_arrays=sql_select($sql_drying);
			$processID=31;
		}
		if(empty($data_arrays) && $processID==31)
		{
			$sql_stentering="select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=48 and f.booking_without_order=0 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id,null as po_number, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=48 and f.booking_without_order=1 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no"; 
			$data_arrays=sql_select($sql_stentering);
			$processID=48;
		}
		if(empty($data_arrays) && $processID==48)
		{
			$sql_slitting="select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=30 and f.booking_without_order=0 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id, null po_number,null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=30 and f.booking_without_order=1 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no"; 
			$data_arrays=sql_select($sql_slitting);
			$processID=30;
		}
		if(empty($data_arrays) && $processID==30)
		{
			$sql_heat="select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=32 and f.booking_without_order=0 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id,null as po_number, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=32 and f.booking_without_order=1 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no"; 
			$data_arrays=sql_select($sql_heat);
			$processID=32;
		}
		if(empty($data_arrays) && $processID==32)
		{

			$sql="select 0 as id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,0 as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and e.entry_form=64 and f.booking_without_order=0 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
			group by a.company_id,a.service_source, 
			a.service_company,a.entry_form, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no  
			union all 
			select  a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no    
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=61 and f.booking_without_order=0 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form=64)
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id,null as po_number,null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=64 and f.booking_without_order=1 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no  
			union all 
			select  a.id,a.company_id,null as receive_basis,a.service_source as knitting_source, a.service_company as knitting_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id,null as po_number, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no    
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=61 and f.booking_without_order=1 and a.company_id=$company_id and b.barcode_no in(".$barcode_nos.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form=64)
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no";

			$data_arrays = sql_select($sql);
			if(empty($data_arrays))
			{

				$product_array=array();
				$product_sql = sql_select("select id, detarmination_id, gsm, dia_width,item_description, unit_of_measure from product_details_master where item_category_id=13");
				foreach($product_sql as $row)
				{
					$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
					$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
					$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
					$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
					$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
				}

					$data_array_info=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.location_id, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.machine_no_id, b.brand_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.po_breakdown_id, c.barcode_no, c.roll_no, c.booking_no as bwo, c.booking_without_order, c.is_sales,a.store_id FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
					WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in(".$barcode_nos.")"); //and c.barcode_no in($barcode_nos)

					foreach($data_array_info as $row)
					{
						$dataArr[$row[csf('barcode_no')]]['body_part_id']=$row[csf('body_part_id')];
						$dataArr[$row[csf('barcode_no')]]['fabric_desc_id']=$row[csf('febric_description_id')];
					}

					$sql="select b.id,b.company_id,null as receive_basis,b.knit_dye_source as knitting_source,b.knit_dye_company as knitting_company,a.entry_form,c.id as dtls_id, c.prod_id as prod_id,f.body_part_id as  body_part_id,null as febric_description_id,null as gsm, null as width, null as width_dia_type,e.barcode_no as barcode_no, e.roll_no as roll_no,
					c.issue_qnty as qnty ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst, e.booking_without_order,c.color_id as color_id,null as booking_id,null as booking_no,null as batch_id,null as batch_no
					from inv_issue_master b,inv_grey_fabric_issue_dtls c,inv_transaction f, order_wise_pro_details a, wo_po_break_down d,pro_roll_details e 
					where b.id=c.mst_id and c.trans_id=f.id and  f.id=a.trans_id and c.trans_id=a.trans_id and a.po_breakdown_id =d.id and d.id=e.po_breakdown_id and b.id=e.mst_id and c.id=e.dtls_id and e.entry_form=61 and b.entry_form=61 and b.company_id=$company_id and b.status_active=1 
					and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no in(".$barcode_nos.") 
					group by  b.id,b.company_id,b.knit_dye_source,b.knit_dye_company,a.entry_form,c.id, c.prod_id,f.body_part_id,e.barcode_no, e.roll_no,
					c.issue_qnty, d.id,d.po_number, d.job_no_mst, e.booking_without_order,c.color_id 
					union all 
					select b.id,b.company_id,null as receive_basis,b.knit_dye_source as knitting_source,b.knit_dye_company as knitting_company,null as entry_form,c.id as dtls_id, c.prod_id as prod_id,f.body_part_id as  body_part_id,null as febric_description_id,null as gsm, null as width, null as width_dia_type,e.barcode_no as barcode_no, e.roll_no as roll_no,
					c.issue_qnty as qnty ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id,null as po_number, null as job_no_mst, e.booking_without_order,c.color_id as color_id,e.po_breakdown_id as booking_id,null as booking_no,null as batch_id,null as batch_no
					from inv_issue_master b,inv_grey_fabric_issue_dtls c,inv_transaction f,pro_roll_details e 
					where b.id=c.mst_id and c.trans_id=f.id and b.id=e.mst_id and c.id=e.dtls_id and e.entry_form=61 and b.entry_form=61 and b.company_id=$company_id and b.status_active=1 
					and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and e.barcode_no in(".$barcode_nos.") 
					group by  b.id,b.company_id,b.knit_dye_source,b.knit_dye_company,c.id, c.prod_id,f.body_part_id,e.barcode_no, e.roll_no,
					c.issue_qnty, e.booking_without_order,c.color_id,e.po_breakdown_id";
					$data_arrays = sql_select($sql);	
					$issueQuryeStatus=101;			
			}
		}

	}
	else
	{
		$sql_data="select f.id,f.company_id,null as receive_basis,null as knitting_source, f.working_company_id as knitting_company,c.id as dtls_id,c.prod_id as prod_id,c.body_part_id,c.item_description as febric_description_id,null as gsm,null as width,c.width_dia_type,c.barcode_no, c.roll_no as roll_no, c.batch_qnty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst,e.booking_without_order,e.entry_form,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no 
		from pro_batch_create_mst f,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e 
		where f.id=c.mst_id and c.po_id =d.id and  d.id=e.po_breakdown_id and f.id=e.mst_id and c.id=e.dtls_id  and c.barcode_no=e.barcode_no and e.entry_form=64 and f.company_id=$company_id  and c.barcode_no in(".$barcode_nos.")  and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 
		group by f.id,f.company_id,f.working_company_id,c.id,c.prod_id,c.body_part_id,c.item_description,c.width_dia_type,c.barcode_no, c.roll_no, c.batch_qnty,d.id,d.po_number, d.job_no_mst,e.booking_without_order,e.entry_form,f.color_id,f.booking_no_id, f.booking_no,f.id,f.batch_no 
		union all 
		select f.id,f.company_id,null as receive_basis,null as knitting_source, f.working_company_id as knitting_company,c.id as dtls_id,c.prod_id as prod_id,c.body_part_id,c.item_description as febric_description_id,null as gsm,null as width,c.width_dia_type,c.barcode_no, c.roll_no as roll_no, c.batch_qnty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,d.id as po_breakdown_id,d.po_number, d.job_no_mst as job_no_mst,e.booking_without_order,e.entry_form,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no    
		from pro_batch_create_mst f,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e
		where f.id=c.mst_id and c.po_id =d.id and d.id=e.po_breakdown_id and c.barcode_no=e.barcode_no and e.entry_form=61 and f.company_id=$company_id  and c.barcode_no in(".$barcode_nos.") and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form=64)
		group by f.id,f.company_id,f.working_company_id,c.id,c.prod_id,c.body_part_id,c.item_description,c.width_dia_type,c.barcode_no, c.roll_no, c.batch_qnty,d.id,d.po_number, d.job_no_mst,e.booking_without_order,e.entry_form,f.color_id,f.booking_no_id, f.booking_no,f.id,f.batch_no 
		";
		$data_arrays=sql_select($sql_data);
	}

	foreach($data_arrays as $row)
	{
		$data_arr[$row[csf('barcode_no')]]['entry_form'] 			=$row[csf('entry_form')];
		$data_arr[$row[csf('barcode_no')]]['receive_basis']	 		=$row[csf('receive_basis')];
		$data_arr[$row[csf('barcode_no')]]['booking_no']			=$row[csf('booking_no')];
		$data_arr[$row[csf('barcode_no')]]['booking_id']			=$row[csf('booking_id')];
		$data_arr[$row[csf('barcode_no')]]['knitting_source']		=$row[csf('knitting_source')];
		$data_arr[$row[csf('barcode_no')]]['knitting_company']		=$row[csf('knitting_company')];
		$data_arr[$row[csf('barcode_no')]]['prod_id']				=$row[csf('prod_id')];
		$data_arr[$row[csf('barcode_no')]]['body_part_id']			=$row[csf('body_part_id')];
		$data_arr[$row[csf('barcode_no')]]['febric_description_id']	=$row[csf('febric_description_id')];
		$data_arr[$row[csf('barcode_no')]]['gsm']					=$row[csf('gsm')];
		$data_arr[$row[csf('barcode_no')]]['width']					=$row[csf('width')];
		$data_arr[$row[csf('barcode_no')]]['color_id']				=$row[csf('color_id')];
		$data_arr[$row[csf('barcode_no')]]['po_number']				=$row[csf('po_number')];
		$data_arr[$row[csf('barcode_no')]]['job_no_mst']			=$row[csf('job_no_mst')];
		$data_arr[$row[csf('barcode_no')]]['batch_id']				=$row[csf('batch_id')];
		$data_arr[$row[csf('barcode_no')]]['batch_no']				=$row[csf('batch_no')];
		$data_arr[$row[csf('barcode_no')]]['width_dia_type']		=$row[csf('width_dia_type')];
		$data_arr[$row[csf('barcode_no')]]['qnty']					=$row[csf('qnty')];
		

		$data_arr_info[$row[csf('barcode_no')]][$row[csf('batch_id')]]['batch_no']				=$row[csf('batch_no')];
		$data_arr_info[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['po_number']		=$row[csf('po_number')];
		$data_arr_info[$row[csf('barcode_no')]][$row[csf('booking_id')]]['booking_no']			=$row[csf('booking_no')];
		if($issueQuryeStatus==101)
		{
			$data_arr[$row[csf('barcode_no')]]['febric_description_id']	=$product_array[$row[csf("prod_id")]]['item_description'];
		}

	}

	/*echo "<pre>";
	print_r($data_arr_info);
	echo "</pre>";*/
	/*$sql="
	SELECT 
		a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, 
		b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, 
		c.mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.booking_without_order 
	FROM 
		inv_receive_master a 
		INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
		INNER JOIN pro_roll_details c ON b.id = c.dtls_id
	WHERE 
		a.receive_basis<>9
		AND a.entry_form IN(2,22)
		AND c.entry_form IN(2,22)
		AND c.roll_no>0 
		AND c.status_active = 1 
		AND c.is_deleted = 0 
		AND c.barcode_no IN(".$sqlBatchBarcode.")";*/
	
	
	$barCode=array();
	$poBreakdownId=array();
	//$yarnCountDeterminId=array();
	//$bookingId=array();
	$nonOrderbookingId=array();
	foreach($data_array as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if($row[csf('booking_without_order')] != 1)
		{
			$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		else
		{
			$nonOrderbookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		//$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		//$bookingId[$row[csf('booking_id')]]=$row[csf('booking_id')];
	}
	
	//for batch
	//$batchArray = get_batchFor_GreyRollIssueToProcess($barCode);
	//for Yarn Count Determin
	//$yarnCountDeterminArray = get_constructionComposition($yarnCountDeterminId);
	//echo "<pre>";
	//print_r($yarnCountDeterminArray);
	//for buyer
	$poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);
	$nonOrderBuyerArray = get_nonOrderBookingBuyerFor_GreyRollIssueToProcess($nonOrderbookingId);
	
	//for dia type
	$diaTypeArray = get_dia_type($bookingId);

	if($issueQuryeStatus==101)
	{
		$barcodeNOS="";
		foreach($data_array as $row)
		{
			$barcodeNOS.=$row[csf('barcode_no')].",";
		}
		$barcodeNOS=chop($barcodeNOS,",");

		$nonOrderBookingBatchNo=sql_select("select a.id, a.batch_no,b.width_dia_type,b.barcode_no from pro_batch_create_mst a,pro_batch_create_dtls b  where a.id=b.mst_id and b.barcode_no in($barcodeNOS) and a.booking_without_order=1");

		foreach($nonOrderBookingBatchNo as $rowData){
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['batch_no']=$rowData[csf("batch_no")];
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['batch_id']=$rowData[csf("id")];
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['width_dia_type']=$rowData[csf("width_dia_type")];
		}
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 order by b.id";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
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
	}

	
	$i = 0;
	foreach($data_array as $row)
	{
		$i++;
		//$row['batch_id']=$batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_id'];
		//$row['batch_no']=$batchArray[$row[csf('barcode_no')]][$row[csf('po_breakdown_id')]]['batch_no'];


		if($row[csf('booking_without_order')]==1)
		{
			$row['batch_no']=$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['batch_no'];
		}
		else
		{
			$row['batch_no']=$data_arr_info[$row[csf('barcode_no')]][$row[csf('batch_id')]]['batch_no'];
		}


		$row['order_no']=$data_arr_info[$row[csf('barcode_no')]][$row[csf('order_id')]]['po_number'];
		//$row['booking_no']=$data_arr_info[$row[csf('barcode_no')]][$row[csf('booking_id')]]['booking_no'];
		
		//$row['construction']=$yarnCountDeterminArray[$row[csf('febric_description_id')]];
		$row['construction']=$data_arr[$row[csf('barcode_no')]]['febric_description_id'];


		if($row['construction'] == "" && $row[csf('febric_description_id')] !="")
		{
			$row['construction'] = $composition_arr[$row[csf('febric_description_id')]];
		}
		
		//for buyer
		if($row[csf('booking_without_order')] != 1)
		{
			$row['buyer']=$poArray[$row[csf('po_breakdown_id')]]['buyer_name'];
			$row['order_no']=$poArray[$row[csf('po_breakdown_id')]]['po_number'];
		}
		else
		{
			$row['buyer']=$nonOrderBuyerArray[$row[csf('po_breakdown_id')]]['buyer_name'];
		}

		//$row['job_no']=$poArray[$row[csf('po_breakdown_id')]]['job_no'];
		//$row['order_no']=$poArray[$row[csf('po_breakdown_id')]]['po_number'];
		//$row['order_id']=$row[csf('po_breakdown_id')];
		$rollWeight=number_format($row[csf('qnty')],2);
		$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
		//$row['dia_type']=$fabric_typee[$diaTypeArray[$row[csf('booking_id')]]];
		$row['dia_type']=$fabric_typee[$row[csf('width_dia_type')]];
		//for color
		//$row['color']=get_color_details($row[csf('color_id')]);
		//knitting_company
		//$row['knitting_company']=get_knitting_company_details($row[csf('knitting_source')],$row[csf('knitting_company')]);
		//receive_basis
		//$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
		//$receive_basis_id=$receiveBasisArray['id'];
		//$receive_basis_dtls=$receiveBasisArray['dtls'];
		?>
		<tr id="tr_<?php echo $i; ?>" align="center" valign="middle">
			<td width="30" title="<? echo $row[csf('mst_id')];?>" id="sl_<?php echo $i; ?>"><?php echo $i; ?></td>
			<td width="80" id="barcode_<?php echo $i; ?>"><?php echo $row[csf('barcode_no')]; ?></td>
			<td width="50" id="roll_<?php echo $i; ?>"><?php echo $row[csf('roll_no')]; ?></td>
			<td width="70" id="batchNo_<?php echo $i; ?>"><?php echo $row['batch_no']; ?></td>
			<td width="60" id="prodId_<?php echo $i; ?>"><?php echo $row[csf('prod_id')]; ?></td>
			<td width="80" style="word-break:break-all;" id="bodyPart_<?php echo $i; ?>"><?php echo $body_part[$row[csf('body_part_id')]]; ?></td>
			<td width="150" style="word-break:break-all;" id="cons_<?php echo $i; ?>" align="left"><?php echo $row['construction']; ?></td>
			<td width="50" style="word-break:break-all;" id="gsm_<?php echo $i; ?>"><?php echo $row[csf('gsm')]; ?></td>
			<td width="50" style="word-break:break-all;" id="dia_<?php echo $i; ?>"><?php echo $row[csf('width')]; ?></td>
			<td width="70" style="word-break:break-all;" id="color_<?php echo $i; ?>"><?php echo $color_arr[$row[csf('color_id')]]; ?></td>
			<td width="70" style="word-break:break-all;" id="diaType_<?php echo $i; ?>"><?php echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
			<td width="70" align="right" id="rollWeight_<?php echo $i; ?>">		
				<input class="text_boxes_numeric" style="width:60px; text-align:right;" onBlur="fnc_qnty_check(<?php echo $i; ?>);" type="text" name="rollWeightInput[]" id="rollWeightInput_<?php echo $i; ?>" value="<?php echo $rollWeight; ?>" />
			</td>
			<td width="70" align="right" id="qtyInPcs_<?php echo $i; ?>"><?php echo $qtyInPcs; ?></td>
			<td width="60" style="word-break:break-all;" id="buyer_<?php echo $i; ?>"><?php echo $row['buyer']; ?></td>
			<td width="80" style="word-break:break-all;" id="job_<?php echo $i; ?>"><?php echo $row[csf('job_no')]; ?></td><!-- -->
			<td width="80" style="word-break:break-all;" id="order_<?php echo $i; ?>" align="left"><?php echo $row['order_no']; ?></td>
			<td width="100" style="word-break:break-all;" id="progBookPiNo_<?php echo $i; ?>"><?php echo $row[csf('booking_no')]; ?></td>
			<td id="button_<?php echo $i; ?>" align="center">
            <input type="button" id="decrease_<?php echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?php echo $i; ?>);" />
            <input type="hidden" name="barcodeNo[]" id="barcodeNo_<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
            <input type="hidden" name="progBookPiId[]" id="progBookPiId_<?php echo $i; ?>" value="<?php echo $row[csf('booking_id')]; ?>" />
            <input type="hidden" name="productId[]" id="productId_<?php echo $i; ?>" value="<?php echo $row[csf('prod_id')]; ?>" />
            <input type="hidden" name="orderId[]" id="orderId_<?php echo $i; ?>" value="<?php echo $row[csf('order_id')]; ?>" />
            <input type="hidden" name="batchId[]" id="batchId_<?php echo $i; ?>" value="<?php echo $row[csf('batch_id')]; ?>" />
            <input type="hidden" name="rollId[]" id="rollId_<?php echo $i; ?>" value="<?php echo $row[csf('roll_id')]; ?>" />
            <input type="hidden" name="rollWgt[]" id="rollWgt_<?php echo $i; ?>" value="<?php echo $data_arr[$row[csf('barcode_no')]]['qnty']; ?>" />
            <input type="hidden" name="colorId[]" id="colorId_<?php echo $i; ?>" value="<?php echo $row[csf('color_id')]; ?>" />
            <input type="hidden" name="dtlsId[]" id="dtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('dtls_id')]; ?>" />
            <input type="hidden" name="rolltableId[]" id="rolltableId_<?php echo $i; ?>" value="<?php echo $row[csf('roll_table_id')]; ?>" />
            <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<?php echo $i; ?>" value="<?php echo $qtyInPcs; ?>" />


			<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i; ?>" value="<?php echo $row[csf('body_part_id')]; ?>" />
			<input type="hidden" name="widthDiaType[]" id="widthDiaType_<?php echo $i; ?>" value="<?php echo $row[csf('width_dia_type')]; ?>" />
			<input type="hidden" name="serviceCompany[]" id="serviceCompany_<?php echo $i; ?>" value="<?php echo $data_arr[$row[csf('barcode_no')]]['knitting_company']; ?>" />
			<input type="hidden" name="hiddenGsm[]" id="hiddenGsm_<?php echo $i; ?>" value="<?php echo $row[csf('gsm')]; ?>" />
			<input type="hidden" name="hiddenDiaWidth[]" id="hiddenDiaWidth_<?php echo $i; ?>" value="<?php echo $row[csf('width')]; ?>" />
			<input type="hidden" name="hiddenJob[]" id="hiddenJob_<?php echo $i; ?>" value="<?php echo $row[csf('job_no')]; ?>" />
			<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<?php echo $i; ?>" value="<?php echo $row[csf('booking_without_order')]; ?>" />
			<input type="hidden" name="bookingNo[]" id="bookingNo_<?php echo $i; ?>" value="<?php echo $row[csf('booking_no')]; ?>" />
			<input type="hidden" name="determinationId[]" id="determinationId_<?php echo $i; ?>" value="<?php echo $row[csf('febric_description_id')]; ?>" />
			<input type="hidden" name="buyerId[]" id="buyerId_<?php echo $i; ?>" value="<?php echo $row[csf('buyer_id')]; ?>" />
			<input type="hidden" name="rollNo[]" id="rollNo_<?php echo $i; ?>" value="<?php echo $row[csf('roll_no')]; ?>" />
			</td>
		</tr>
		<?php
	}
	die;
}

if($action=="update_greyRollIssueToProcess_detailsAop_sales")
{
	$expData=explode("_",$data);
	$company_id=$expData[1];
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$sql="SELECT a.id,a.company_id,a.dyeing_source, a.process_id, b.id as dtls_id, b.batch_id, e.batch_no, e.color_id as batch_color, b.prod_id, b.body_part_id,b.gsm,b.width,b.color_id, b.width_dia_type, b.job_no, b.buyer_id, b.order_id,b.booking_id, b.booking_no, b.febric_description_id, c.mst_id, c.barcode_no, c.id as roll_table_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.booking_without_order, d.job_no as fso_no, c.is_sales as dtls_is_sales from inv_receive_mas_batchroll a,pro_grey_batch_dtls b left join pro_batch_create_mst e on b.batch_id=e.id and b.batch_id!=0, pro_roll_details c left join fabric_sales_order_mst d on c.po_breakdown_id=d.id and c.is_sales=1 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.id = ".$expData[0]." and a.entry_form = 63 and c.entry_form = 63 AND c.roll_no>0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.mst_id = ".$expData[0]."";

	$data_array=sql_select($sql);
	$barcode_nos="";
	foreach($data_array as $row)
	{
		$barcode_nos.=$row[csf('barcode_no')].",";
	}
	$barcode_nos=chop($barcode_nos,",");

	$barCode=array();
	$poBreakdownId=array();
	$nonOrderbookingId=array();
	foreach($data_array as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if($row[csf('booking_without_order')] != 1)
		{
			$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		else
		{
			$nonOrderbookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
	}
	

	$poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);
	$nonOrderBuyerArray = get_nonOrderBookingBuyerFor_GreyRollIssueToProcess($nonOrderbookingId);
	
	//for dia type
	$diaTypeArray = get_dia_type($bookingId);

	if($issueQuryeStatus==101)
	{
		$barcodeNOS="";
		foreach($data_array as $row)
		{
			$barcodeNOS.=$row[csf('barcode_no')].",";
		}
		$barcodeNOS=chop($barcodeNOS,",");

		$nonOrderBookingBatchNo=sql_select("select a.id, a.batch_no,b.width_dia_type,b.barcode_no from pro_batch_create_mst a,pro_batch_create_dtls b  where a.id=b.mst_id and b.barcode_no in($barcodeNOS) and a.booking_without_order=1");

		foreach($nonOrderBookingBatchNo as $rowData){
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['batch_no']=$rowData[csf("batch_no")];
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['batch_id']=$rowData[csf("id")];
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['width_dia_type']=$rowData[csf("width_dia_type")];
		}
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1 order by b.id";
	$deter_array=sql_select($sql_deter);
	if(count($deter_array)>0)
	{
		foreach( $deter_array as $row )
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
	}

	
	$i = 0;
	foreach($data_array as $row)
	{
		$i++;
		if($row[csf('booking_without_order')]==1)
		{
			$row['batch_no']=$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['batch_no'];
		}
		else
		{
			$row['batch_no']=$data_arr_info[$row[csf('barcode_no')]][$row[csf('batch_id')]]['batch_no'];
		}

		if($row[csf('batch_id')] !="")
		{
			$row['batch_no'] = $row[csf('batch_no')];
			$row['batch_id'] = $row[csf('batch_id')];
			$row['color_id'] = $row[csf('batch_color')];
		}
		else
		{
			$row['color_id'] = $row[csf('color_id')];
		}

		$row['order_no']=$data_arr_info[$row[csf('barcode_no')]][$row[csf('order_id')]]['po_number'];
		
		//$row['construction']=$yarnCountDeterminArray[$row[csf('febric_description_id')]];
		$row['construction']=$data_arr[$row[csf('barcode_no')]]['febric_description_id'];

		if($row['construction'] == "" && $row[csf('febric_description_id')] !="")
		{
			$row['construction'] = $composition_arr[$row[csf('febric_description_id')]];
		}
		
		//for buyer
		if($row[csf('booking_without_order')] != 1)
		{
			$row['buyer']=$buyer_name_array[$row[csf('buyer_id')]];
			$row['order_no']=$row[csf('fso_no')];
		}
		else
		{
			$row['buyer']=$nonOrderBuyerArray[$row[csf('po_breakdown_id')]]['buyer_name'];
		}

		$rollWeight=number_format($row[csf('qnty')],2);
		$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;

		$row['dia_type']=$fabric_typee[$row[csf('width_dia_type')]];

		?>
		<tr id="tr_<?php echo $i; ?>" align="center" valign="middle">
			<td width="30" title="<? echo $row[csf('mst_id')];?>" id="sl_<?php echo $i; ?>"><?php echo $i; ?></td>
			<td width="80" id="barcode_<?php echo $i; ?>"><?php echo $row[csf('barcode_no')]; ?></td>
			<td width="50" id="roll_<?php echo $i; ?>"><?php echo $row[csf('roll_no')]; ?></td>
			<td width="70" id="batchNo_<?php echo $i; ?>"><?php echo $row['batch_no']; ?></td>
			<td width="60" id="prodId_<?php echo $i; ?>"><?php echo $row[csf('prod_id')]; ?></td>
			<td width="80" style="word-break:break-all;" id="bodyPart_<?php echo $i; ?>"><?php echo $body_part[$row[csf('body_part_id')]]; ?></td>
			<td width="150" style="word-break:break-all;" id="cons_<?php echo $i; ?>" align="left"><?php echo $row['construction']; ?></td>
			<td width="50" style="word-break:break-all;" id="gsm_<?php echo $i; ?>"><?php echo $row[csf('gsm')]; ?></td>
			<td width="50" style="word-break:break-all;" id="dia_<?php echo $i; ?>"><?php echo $row[csf('width')]; ?></td>
			<td width="70" style="word-break:break-all;" id="color_<?php echo $i; ?>"><?php echo $color_arr[$row['color_id']]; ?></td>
			<td width="70" style="word-break:break-all;" id="diaType_<?php echo $i; ?>"><?php echo $fabric_typee[$row[csf('width_dia_type')]]; ?></td>
			<td width="70" align="right" id="rollWeight_<?php echo $i; ?>">		
				<input class="text_boxes_numeric" style="width:60px; text-align:right;" onBlur="fnc_qnty_check(<?php echo $i; ?>);" type="text" name="rollWeightInput[]" id="rollWeightInput_<?php echo $i; ?>" disabled="disabled" value="<?php echo $rollWeight; ?>" />
			</td>
			<td width="70" align="right" id="qtyInPcs_<?php echo $i; ?>"><?php echo $qtyInPcs; ?></td>
			<td width="60" style="word-break:break-all;" id="buyer_<?php echo $i; ?>"><?php echo $row['buyer']; ?></td>
			<td width="80" style="word-break:break-all;" id="job_<?php echo $i; ?>"><?php echo $row[csf('job_no')]; ?></td>
			<td width="80" style="word-break:break-all;" id="order_<?php echo $i; ?>" align="left"><?php echo $row[csf('fso_no')]; ?></td>
			<td width="100" style="word-break:break-all;" id="progBookPiNo_<?php echo $i; ?>"><?php echo $row[csf('booking_no')]; ?></td>
			<td id="button_<?php echo $i; ?>" align="center">
            <input type="button" id="decrease_<?php echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<?php echo $i; ?>);" />
            <input type="hidden" name="barcodeNo[]" id="barcodeNo_<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>" />
            <input type="hidden" name="progBookPiId[]" id="progBookPiId_<?php echo $i; ?>" value="<?php echo $row[csf('booking_id')]; ?>" />
            <input type="hidden" name="productId[]" id="productId_<?php echo $i; ?>" value="<?php echo $row[csf('prod_id')]; ?>" />
            <input type="hidden" name="orderId[]" id="orderId_<?php echo $i; ?>" value="<?php echo $row[csf('order_id')]; ?>" />
            <input type="hidden" name="batchId[]" id="batchId_<?php echo $i; ?>" value="<?php echo $row[csf('batch_id')]; ?>" />
            <input type="hidden" name="rollId[]" id="rollId_<?php echo $i; ?>" value="<?php echo $row[csf('roll_id')]; ?>" />
            <input type="hidden" name="rollWgt[]" id="rollWgt_<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>" />
            <input type="hidden" name="colorId[]" id="colorId_<?php echo $i; ?>" value="<?php echo $row['color_id']; ?>" />
            <input type="hidden" name="dtlsId[]" id="dtlsId_<?php echo $i; ?>" value="<?php echo $row[csf('dtls_id')]; ?>" />
            <input type="hidden" name="rolltableId[]" id="rolltableId_<?php echo $i; ?>" value="<?php echo $row[csf('roll_table_id')]; ?>" />
            <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<?php echo $i; ?>" value="<?php echo $qtyInPcs; ?>" />


			<input type="hidden" name="bodyPartId[]" id="bodyPartId_<?php echo $i; ?>" value="<?php echo $row[csf('body_part_id')]; ?>" />
			<input type="hidden" name="widthDiaType[]" id="widthDiaType_<?php echo $i; ?>" value="<?php echo $row[csf('width_dia_type')]; ?>" />
			<input type="hidden" name="serviceCompany[]" id="serviceCompany_<?php echo $i; ?>" value="<?php echo $data_arr[$row[csf('barcode_no')]]['knitting_company']; ?>" />
			<input type="hidden" name="hiddenGsm[]" id="hiddenGsm_<?php echo $i; ?>" value="<?php echo $row[csf('gsm')]; ?>" />
			<input type="hidden" name="hiddenDiaWidth[]" id="hiddenDiaWidth_<?php echo $i; ?>" value="<?php echo $row[csf('width')]; ?>" />
			<input type="hidden" name="hiddenJob[]" id="hiddenJob_<?php echo $i; ?>" value="<?php echo $row[csf('job_no')]; ?>" />
			<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<?php echo $i; ?>" value="<?php echo $row[csf('booking_without_order')]; ?>" />
			<input type="hidden" name="bookingNo[]" id="bookingNo_<?php echo $i; ?>" value="<?php echo $row[csf('booking_no')]; ?>" />
			<input type="hidden" name="determinationId[]" id="determinationId_<?php echo $i; ?>" value="<?php echo $row[csf('febric_description_id')]; ?>" />
			<input type="hidden" name="buyerId[]" id="buyerId_<?php echo $i; ?>" value="<?php echo $row[csf('buyer_id')]; ?>" />
			<input type="hidden" name="rollNo[]" id="rollNo_<?php echo $i; ?>" value="<?php echo $row[csf('roll_no')]; ?>" />
			<input type="hidden" name="dtlsIsSales[]" id="dtlsIsSales_<?php echo $i; ?>" value="<?php echo $row[csf('dtls_is_sales')]; ?>" />
			</td>
		</tr>
		<?php
	}
	die;
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;
	?> 
	<script>
		var service_source = '<? echo $cbo_service_source; ?>';
		var cbo_process = '<? echo $cbo_process; ?>';
		var textile_sales_maintain = '<? echo $textile_sales_maintain; ?>';
		var hdn_is_sales = '<? echo $hdn_is_sales; ?>';
		var hidden_wo_entry_form = '<? echo $hidden_wo_entry_form; ?>';
		var selected_id = new Array();
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 )
			{
				selected_id.push( $('#txt_individual_id' + str).val() );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ )
				{
					if( selected_id[i] == $('#txt_individual_id' + str).val() )
						break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ )
			{
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
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
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}

		function fnc_barcode_search_list(){
			if(textile_sales_maintain ==1 && hdn_is_sales==2)
			{
				//sales barcode but merchandising aop service wo
				var show_action ='create_barcode_search_list_view_sales';
			}
			else if((service_source == 3 && cbo_process ==31) || hidden_wo_entry_form==696)
			{
				var show_action ='create_outbound_barcode_search_list_view';
			}
			else
			{
				var show_action ='create_barcode_search_list_view';
			}

			/*
				else if(service_source == 1){
				var show_action ='create_barcode_search_list_view';
			}
			*/



			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('barcode_no').value+'_'+'<? echo $po_ids; ?>'+'_'+<? echo $batch_id; ?>+'_'+'<? echo $txt_wo_no; ?>'+'_'+<? echo $cbo_service_company; ?>+'_'+<? echo $hdn_is_sales; ?>+'_'+<? echo $textile_sales_maintain; ?>+'_'+ hidden_wo_entry_form+'_'+ cbo_process, show_action, 'search_div', 'grey_fabric_roll_issue_to_subcon_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();');
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
						if($hdn_is_sales == 1 || ($hdn_is_sales == 2 && $textile_sales_maintain==1))
						{
							$search_by_arr=array(1=>"Sales Order No",2=>"Issue Challan No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";	
						}
						else if($cbo_service_source == 3){
							$search_by_arr=array(1=>"Order No", 2=>"Batch No", 3=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";	
						}else{
							$search_by_arr=array(1=>"Order No", 2=>"Batch No");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";	
						}
												
						echo create_drop_down( "cbo_search_by", 120, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td> 
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 			
                    <td><input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" /></td>    			
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_barcode_search_list();" style="width:100px;" /> 
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
	$barcode_no =trim($data[3]);
	$po_ids =trim($data[4]);
	$batch_id =trim($data[5]);
	$textile_sales_maintain =trim($data[9]);
	// echo $po_ids.'DFD';
	$variable_set_finish=return_field_value("company_name","variable_settings_production","company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3","company_name");
	
	$search_field_cond=""; $search_field_batch_subpro_cond=$search_field_batch_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1)
		{
			$search_field_cond="and d.po_number like '$search_string'";
		}
		else
		{
			$search_field_batch_subpro_cond="and a.batch_no like '$search_string'";
			$search_field_batch_cond="and b.batch_no like '$search_string'";
		}
	}
	
	if($barcode_no!="")
	{
		if ($variable_set_finish) 
		{
			$barcode_cond="and b.barcode_no='$barcode_no' and c.barcode_no='$barcode_no'";
			$barcode_cond2="and e.barcode_no='$barcode_no' and c.barcode_no='$barcode_no'";
			$barcode_cond3="and e.barcode_no='$barcode_no'";
		}
		else
		{
			$barcode_cond="and e.barcode_no='$barcode_no' and c.barcode_no='$barcode_no'";
		}

	}
	$barcode_cond4="and e.barcode_no='$barcode_no'";
	if($po_ids!="")
	{
		$po_cond="and d.id in($po_ids)";
	}
	else
	{
		$po_cond="";
	}
	
	if($batch_id>0)
	{
		$batch_cond="and a.batch_id in($batch_id) and c.mst_id in($batch_id)";
	}
	else
	{
		$batch_cond="";
	}
	
	if ($variable_set_finish) 
	{
		if($search_by==2)
		{
			$nonOrderBatch="union all 
			SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c ,pro_roll_details e 
			where a.id=b.mst_id and a.batch_id =c.mst_id  and c.barcode_no=e.barcode_no and b.prod_id=c.prod_id and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=33 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form";
		}
		//e.qc_pass_qnty_pcs, 
		$sql_compacting="SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
		from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
		where a.id=b.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=33 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
		group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst $nonOrderBatch";
		$result = sql_select($sql_compacting);
		$processID=33;
		if(empty($result) && $processID==33)
		{
			if($search_by==2)
			{
				$nonOrderBatch="union all 
				SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
				from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c ,pro_roll_details e 
				where a.id=b.mst_id and a.batch_id =c.mst_id  and c.barcode_no=e.barcode_no and b.prod_id=c.prod_id and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=34 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form";
			}
			$sql_special_finish="SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=34 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst $nonOrderBatch";
			$result = sql_select($sql_special_finish);
			$processID=34;
		}
		if(empty($result) && $processID==34)
		{
			if($search_by==2)
			{
				$nonOrderBatch="union all 
				SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
				from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c ,pro_roll_details e 
				where a.id=b.mst_id and a.batch_id =c.mst_id  and c.barcode_no=e.barcode_no and b.prod_id=c.prod_id and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=31 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form";
			}
			$sql_drying="SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=31 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst $nonOrderBatch";
			$result = sql_select($sql_drying);
			$processID=31;
		}
		if(empty($result) && $processID==31)
		{
			if($search_by==2)
			{
				$nonOrderBatch="union all 
				SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
				from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c ,pro_roll_details e 
				where a.id=b.mst_id and a.batch_id =c.mst_id  and c.barcode_no=e.barcode_no and b.prod_id=c.prod_id and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=48 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form";
			}
			$sql_stentering="SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=48 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst $nonOrderBatch";
			$result = sql_select($sql_stentering);
			$processID=48;
		}
		if(empty($result) && $processID==48)
		{
			if($search_by==2)
			{
				$nonOrderBatch="union all 
				SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
				from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c ,pro_roll_details e 
				where a.id=b.mst_id and a.batch_id =c.mst_id  and c.barcode_no=e.barcode_no and b.prod_id=c.prod_id and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=30 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form";
			}
			$sql_slitting="SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=30 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst $nonOrderBatch";
			$result = sql_select($sql_slitting);
			$processID=30;
		}
		/*if(empty($result) && $processID==30)
		{
			$sql_dyeing="SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=35 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst";
			$result = sql_select($sql_dyeing);
			$processID=35;
		}*/
		if(empty($result) && $processID==30)
		{

			if($search_by==2)
			{
				$nonOrderBatch="union all 
				SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
				from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c ,pro_roll_details e 
				where a.id=b.mst_id and a.batch_id =c.mst_id  and c.barcode_no=e.barcode_no and b.prod_id=c.prod_id and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=32 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form";
			}

			$sql_heat="SELECT b.prod_id as prod_id, b.barcode_no as barcode_no, b.roll_no as roll_no, b.production_qty as qnty, a.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=32 and a.company_id=$company_id $search_field_cond $search_field_batch_subpro_cond $barcode_cond $po_cond $batch_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by b.prod_id, b.barcode_no, b.roll_no, b.production_qty, a.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst $nonOrderBatch";
			$result = sql_select($sql_heat);
			 $processID=32;
		}
		if(empty($result) && $processID==32)
		{

			if($search_by==2)
			{
				$nonOrderBatch="union all 
				SELECT c.prod_id as prod_id, c.barcode_no as barcode_no, c.roll_no as roll_no, c.batch_qnty as qnty, e.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst
				,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
				from pro_batch_create_mst b,pro_batch_create_dtls c,pro_roll_details e 
				where b.id=c.mst_id  and c.barcode_no=e.barcode_no and b.id=e.mst_id and c.id=e.dtls_id and e.entry_form=64 and b.company_id=$company_id $search_field_batch_cond and b.status_active=1
				and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and e.status_active=1 and e.is_deleted=0 
				group by c.prod_id, c.barcode_no, c.roll_no, c.batch_qnty, e.entry_form  
				union all
				select c.prod_id as prod_id, c.barcode_no as barcode_no, c.roll_no as roll_no, c.batch_qnty as qnty, e.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs
				from pro_batch_create_mst b,pro_batch_create_dtls c,pro_roll_details e 
				where b.id=c.mst_id and c.barcode_no=e.barcode_no  and e.entry_form=61 and b.company_id=$company_id $search_field_batch_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 
				and c.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=64)
				group by c.prod_id, c.barcode_no, c.roll_no, c.batch_qnty, e.entry_form order by prod_id";
			}
			if($batch_id>0)
			{
				$batch_cond2="and b.id in($batch_id) and c.mst_id in($batch_id)";
			}
			else
			{
				$batch_cond2="";
			}
			$sql="SELECT  c.prod_id as prod_id, c.barcode_no as barcode_no, c.roll_no as roll_no, c.batch_qnty as qnty, e.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
			from pro_batch_create_mst b,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e 
			where b.id=c.mst_id and c.po_id =d.id and  d.id=e.po_breakdown_id and b.id=e.mst_id and c.id=e.dtls_id  and c.barcode_no=e.barcode_no and e.entry_form=64 
			and b.company_id=$company_id  $search_field_cond $search_field_batch_cond $barcode_cond2 $po_cond $batch_cond2  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 
			group by c.prod_id, c.barcode_no, c.roll_no, c.batch_qnty, e.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst 
			union all 
			select c.prod_id as prod_id, c.barcode_no as barcode_no, c.roll_no as roll_no, c.batch_qnty as qnty, e.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs  
			from pro_batch_create_mst b,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e
			where b.id=c.mst_id and c.po_id =d.id and d.id=e.po_breakdown_id 

			and c.barcode_no=e.barcode_no and e.entry_form=61
			and b.company_id=$company_id  $search_field_cond $search_field_batch_cond $barcode_cond2 $po_cond $batch_cond2 
			and b.status_active=1 and b.is_deleted=0 
			and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form=64)
			group by c.prod_id, c.barcode_no, c.roll_no, c.batch_qnty, e.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst $nonOrderBatch
			";
			$result = sql_select($sql);
			if(empty($result))
			{
				/*if($search_by==2)
				{
					$nonOrderBatch="union all select c.prod_id as prod_id, e.barcode_no as barcode_no, c.roll_no as roll_no, c.issue_qnty as qnty, e.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs
					from inv_issue_master b,inv_grey_fabric_issue_dtls c, order_wise_pro_details a, wo_po_break_down d,pro_roll_details e 
					where b.id=c.mst_id and c.trans_id=a.trans_id and a.po_breakdown_id =d.id and d.id=e.po_breakdown_id and b.id=e.mst_id and c.id=e.dtls_id  and e.entry_form=61  and b.entry_form=61 and b.company_id=$company_id  $search_field_cond $search_field_batch_cond $barcode_cond3 $po_cond $batch_cond  and b.status_active=1 and b.is_deleted=0 and c.status_active=1
					and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=64)
					group by c.prod_id, e.barcode_no, c.roll_no, c.issue_qnty, e.entry_form";
				}*/


				if($search_by==2)
				{
					$nonOrderBatch="union all 
					select c.prod_id as prod_id, e.barcode_no as barcode_no, c.roll_no as roll_no, c.issue_qnty as qnty, e.entry_form as entry_form,null as po_number, null as pub_shipment_date, null as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs
					from inv_issue_master b,inv_grey_fabric_issue_dtls c,pro_roll_details e 
					where b.id=c.mst_id  and b.id=e.mst_id and c.id=e.dtls_id  and e.entry_form=61  and b.entry_form=61 and b.company_id=$company_id  $search_field_cond $search_field_batch_cond $barcode_cond3 $po_cond $batch_cond  and b.status_active=1 and b.is_deleted=0 and c.status_active=1
					and c.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=64)
					group by c.prod_id, e.barcode_no, c.roll_no, c.issue_qnty, e.entry_form";
					//and c.trans_id=a.trans_id and a.po_breakdown_id =d.id and d.id=e.po_breakdown_id
				}

				$sql="SELECT c.prod_id as prod_id, e.barcode_no as barcode_no, c.roll_no as roll_no, c.issue_qnty as qnty, e.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs
				from inv_issue_master b,inv_grey_fabric_issue_dtls c, order_wise_pro_details a, wo_po_break_down d,pro_roll_details e 
				where b.id=c.mst_id and c.trans_id=a.trans_id and a.po_breakdown_id =d.id and d.id=e.po_breakdown_id and b.id=e.mst_id and c.id=e.dtls_id  and e.entry_form=61  and b.entry_form=61 and b.company_id=$company_id  $search_field_cond $search_field_batch_cond $barcode_cond3 $po_cond $batch_cond  and b.status_active=1 and b.is_deleted=0 and c.status_active=1
				and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=64)
				group by c.prod_id, e.barcode_no, c.roll_no, c.issue_qnty, e.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst $nonOrderBatch"; 
				$result = sql_select($sql);				
			}
		}
	}
	else
	{
		$sql="SELECT  c.prod_id as prod_id, c.barcode_no as barcode_no, c.roll_no as roll_no, c.batch_qnty as qnty, e.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
		from pro_batch_create_mst b,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e 
		where b.id=c.mst_id and c.po_id =d.id and  d.id=e.po_breakdown_id and b.id=e.mst_id and c.id=e.dtls_id  and c.barcode_no=e.barcode_no and e.entry_form=64 
		and b.company_id=$company_id  $search_field_cond $search_field_batch_cond $barcode_cond $po_cond $batch_cond  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 
		group by c.prod_id, c.barcode_no, c.roll_no, c.batch_qnty, e.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst 
		union all 
		select c.prod_id as prod_id, c.barcode_no as barcode_no, c.roll_no as roll_no, c.batch_qnty as qnty, e.entry_form as entry_form,d.po_number as po_number, d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs  
		from pro_batch_create_mst b,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e
		where b.id=c.mst_id and c.po_id =d.id and d.id=e.po_breakdown_id 
		and c.barcode_no=e.barcode_no and e.entry_form=61
		and b.company_id=$company_id  $search_field_cond $search_field_batch_cond $barcode_cond $po_cond $batch_cond 
		and b.status_active=1 and b.is_deleted=0 
		and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form=64)
		group by c.prod_id, c.barcode_no, c.roll_no, c.batch_qnty, e.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst 
		union all
		select c.prod_id as prod_id, e.barcode_no as barcode_no, c.roll_no as roll_no, c.ROLL_WGT as qnty, e.entry_form as entry_form,d.po_number as po_number, 
		d.pub_shipment_date as pub_shipment_date, d.job_no_mst as job_no_mst,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs 
		from inv_receive_mas_batchroll b,pro_grey_batch_dtls c,pro_roll_details e, wo_po_break_down d
		where b.id=c.mst_id and c.order_id=d.id and c.id=e.dtls_id and e.po_breakdown_id=d.id and e.entry_form=65 and b.company_id=$company_id  $search_field_cond $barcode_cond4 $po_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 group by c.prod_id, e.barcode_no, c.roll_no, c.ROLL_WGT, e.entry_form,d.po_number, d.pub_shipment_date, d.job_no_mst";
		$result = sql_select($sql);
	}
	//echo  $processID;
	//echo $sql;
	
	$barCode = array();
	$prodId = array();
	// $scanned_barcode_arr=array();
	foreach($result as $row)
	{
		$barCode[] = $row[csf('barcode_no')];
		//$prodId[] = $row[csf('prod_id')];
		$prodId[$row[csf('prod_id')]] = $row[csf('prod_id')];
		
		/*if($row[csf('entry_form')] == '63') // 63 entry form not found for ref RpC-GIRS-22-00001
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}*/
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}
	$barcode_arr = array_filter(array_unique($barcode_arr));

	if(count($barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $all_barcode_cond= "";

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
		$barcodeData=sql_select("SELECT a.barcode_no from pro_roll_details a where a.entry_form in(63) and a.is_returned=0 and a.is_rcv_done=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
	}
	//echo count($prodId);
	//echo "<pre>";
	// print_r($scanned_barcode_arr);
	
	//already scan checking
	/*$scanned_barcode_arr=array();
	$barcodeData=sql_select( "SELECT barcode_no FROM pro_roll_details WHERE entry_form=63 AND status_active=1 AND is_deleted=0 AND barcode_no IN(".implode(",",$barCode).")");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}*/
	
	//product_details_master
	$product_arr=return_library_array( "SELECT id, product_name_details FROM product_details_master WHERE item_category_id=13 AND id IN(".implode(",",$prodId).")",'id','product_name_details');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order No</th>
            <th width="80">Shipment Date</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th width="70">Qty In Pcs</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:820px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]] != $row[csf('barcode_no')])
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
						<td align="right"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
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
        	<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check all
			</td>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
exit();
}

if($action=="create_outbound_barcode_search_list_view")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$barcode_no =trim($data[3]);
	$po_ids =trim($data[4]);
	$batch_id =trim($data[5]);
	$txt_wo_no =trim($data[6]);
	$cbo_service_company =trim($data[7]);
	$is_sales =$data[8];
	$hidden_wo_entry_form =$data[10];
	$cbo_process =$data[11];
	// echo $cbo_service_company.'DFD';die;
	if ($po_ids=="") 
	{
		$po_ids=0;
	}
	//echo "select company_name from variable_settings_production where company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3";
	$variable_set_finish=return_field_value("company_name","variable_settings_production","company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3","company_name");
	
	$search_field_cond=$search_field_issue_cond=$search_field_issue_challan_cond=""; $search_field_batch_subpro_cond=$search_field_batch_cond="";
	if(trim($data[0])!="")
	{
		if($is_sales==1)
		{
			if($search_by==1)
			{
				$search_field_cond="and d.job_no like '$search_string'";
			}
			else if($search_by==2)
			{
				$search_field_issue_cond="and g.issue_number like '$search_string'";
				$search_field_issue_challan_cond="and g.challan_no like '$search_string'";
			}
		}
		else
		{
			if($search_by==1)
			{
				$search_field_cond="and d.po_number like '$search_string'";
			}
			else if($search_by==2)
			{
				$search_field_batch_subpro_cond="and a.batch_no like '$search_string'";
				$search_field_batch_cond="and b.batch_no like '$search_string'";
			}
			else{
				$search_field_job_cond="and d.job_no_mst like '$search_string'";
			}
		}
		
	}
	
	if($barcode_no!="")
	{
		$barcode_cond=" and b.barcode_no='$barcode_no'";
	}

	if($hidden_wo_entry_form==696)
	{
		//N.B. Issue to process Source for Heat Settings [1=>grey roll issue (default), 2=> roll receive for batch]
		$variable_set_source_arr =  sql_select("select distribute_qnty from variable_settings_production where variable_list=85 and company_name=$company_id and status_active=1 and is_deleted=0 order by id desc");
		$variable_set_source = $variable_set_source_arr[0][csf("distribute_qnty")];

		if($variable_set_source==2)
		{
			$variable_set_source=2; // roll receive by batch
		}
		else{
			$variable_set_source=1; //default grey roll issue
		}

		$wo_fso_sql =sql_select("SELECT a.id, a.do_no, a.wo_basis, b.issue_no, b.issue_id, b.fso_id, a.dyeing_source, a.dyeing_compnay_id
		from dyeing_work_order_mst a, dyeing_work_order_dtls b
		where a.id=b.mst_id and a.entry_form=696 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and a.do_no='$txt_wo_no'
		order by a.id, b.id");

		foreach ($wo_fso_sql as $val) {
			if($val[csf("wo_basis")]==1)
			{
				$issue_id_arr[$val[csf("issue_id")]]=$val[csf("issue_id")];
				$issue_no_arr[$val[csf("issue_no")]]= "'".$val[csf("issue_no")]."'";
			}
			else
			{
				$fso_id_arr[$val[csf("fso_id")]]=$val[csf("fso_id")];
			}

			$dyeing_source=$val[csf("dyeing_source")];
			$dyeing_compnay_id=$val[csf("dyeing_compnay_id")];
		}

		if(!empty($issue_id_arr))
		{
			$issue_id_cond = " and g.id in (".implode(",",$issue_id_arr).")";
			$issue_no_cond = " and g.challan_no in (".implode(",",$issue_no_arr).")";
		}

		if(!empty($fso_id_arr))
		{
			$fso_cond = " and d.id in (".implode(",",$fso_id_arr).")";
		}

		// N.B. 33=>Heat Setting, 100=> Back Sewing, 476=> Heat Setting + Back Sewing

		$heat_settings_business_processes=array(33=>33,100=>100,476=>476);

		if($heat_settings_business_processes[$cbo_process]!="" && $variable_set_source==2)
		{
			$sql="SELECT g.challan_no as issue_number, b.barcode_no, c.id as prod_id, b.roll_no, d.job_no as fso_no, sum(b.qnty) qnty, b.qc_pass_qnty_pcs 
			from inv_receive_mas_batchroll g, pro_grey_batch_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d 
			where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=62 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.is_sales=1 $issue_no_cond $fso_cond $barcode_cond $search_field_cond $search_field_issue_challan_cond
			group by g.challan_no, b.barcode_no, c.id, b.roll_no, d.job_no, b.qc_pass_qnty_pcs order by g.challan_no";
		}
		else
		{
			$sql="SELECT g.issue_number, b.barcode_no, c.id as prod_id, b.roll_no, d.job_no as fso_no, sum(b.qnty) qnty, b.qc_pass_qnty_pcs
			from inv_issue_master g, inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d 
			where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.is_sales=1 $issue_id_cond $fso_cond  $barcode_cond $search_field_cond $search_field_issue_cond
			group by  g.issue_number, b.barcode_no, c.id, b.roll_no, d.job_no, b.qc_pass_qnty_pcs order by g.issue_number";
		}
	}
	else if($is_sales==1)
	{
		$sql ="SELECT g.issue_number, b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no as fso_no, d.within_group, d.po_buyer, d.buyer_id, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, null as pub_shipment_date
		from inv_issue_master g, inv_grey_fabric_issue_dtls a, pro_roll_details b,product_details_master c, fabric_sales_order_mst d, dyeing_work_order_mst e, dyeing_work_order_dtls f
		where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and d.id in ($po_ids) $barcode_cond $search_field_cond $search_field_issue_cond and b.booking_without_order =0 and b.is_sales=1 
		and d.id=e.po_breakdown_id and e.id=f.mst_id and e.do_no='$txt_wo_no' and ((f.issue_no=g.issue_number and e.wo_basis=1) or e.wo_basis=2) group by g.issue_number, b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no, d.within_group, d.po_buyer, d.buyer_id, b.qc_pass_qnty_pcs order by g.issue_number";
	}
	else
	{
		$sql="SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no_mst, d.po_number, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, d.pub_shipment_date from inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, wo_po_break_down d where a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and d.id in ($po_ids) $barcode_cond $search_field_cond $search_field_job_cond and b.booking_without_order =0 and b.is_sales=0
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no_mst,d.po_number, b.qc_pass_qnty_pcs, d.pub_shipment_date
		union all
		SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, 
		b.roll_no, b.booking_without_order, b.po_breakdown_id, null job_no_mst, null po_number, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, null pub_shipment_date 
		from inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, wo_non_ord_samp_booking_mst d 
		where a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.booking_without_order=1 and d.booking_no ='$txt_wo_no' $barcode_cond group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, b.qc_pass_qnty_pcs
		union all
		SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, 
		null job_no_mst, null po_number, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, null pub_shipment_date 
		from INV_ISSUE_MASTER e, inv_grey_fabric_issue_dtls a, pro_roll_details b, product_details_master c, WO_NON_ORD_KNITDYE_BOOKING_MST d 
		where e.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.FAB_BOOKING_ID and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.booking_without_order=1 and e.knit_dye_source=3
		and d.booking_no ='$txt_wo_no' and e.knit_dye_company=$cbo_service_company $barcode_cond
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, b.qc_pass_qnty_pcs";
	}
	//echo $sql;
	$result = sql_select($sql); 
	
	$barCode = array();
	$prodId = array();
	// $scanned_barcode_arr=array();
	foreach($result as $row)
	{
		$barCode[] = $row[csf('barcode_no')];
		//$prodId[] = $row[csf('prod_id')];
		$prodId[$row[csf('prod_id')]] = $row[csf('prod_id')];
		
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}
	$barcode_arr = array_filter(array_unique($barcode_arr));

	if(count($barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $all_barcode_cond= "";

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
		$barcodeData=sql_select("SELECT a.barcode_no from pro_roll_details a where a.entry_form in(63) and a.is_returned=0 and a.is_rcv_done=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
	}

	if(!empty($barcode_arr))
	{
		$rcv_by_batch_arr = sql_select("select a.barcode_no, a.entry_form from pro_roll_details a where a.entry_form in (62,66) and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($rcv_by_batch_arr as $row)
		{
			if($row[csf('entry_form')]==62)
			{
				$rcv_by_batch_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			}
			else
			{
				$fin_production_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			}
		}
	}
	
	//echo count($prodId);
	//echo "<pre>";
	// print_r($scanned_barcode_arr);
	
	//product_details_master
	$product_arr=return_library_array( "SELECT id, product_name_details FROM product_details_master WHERE item_category_id=13 AND id IN(".implode(",",$prodId).")",'id','product_name_details');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="150">Fabric Description</th>
			<? 
				if($is_sales==1)
				{
					echo '<th width="100">Sales_order No</th>';
					echo '<th width="100">Issue Challan No</th>';
				}
				else{
					echo '<th width="100">Job No</th>
					<th width="110">Order No</th>
					<th width="80">Shipment Date</th>';
				}
			?>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th width="100">Qty In Pcs</th>
            <th width="100">Roll Qty.</th>
        </thead>
	</table>
	<div style="width:820px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="" && $fin_production_barcode_arr[$row[csf('barcode_no')]] =="" )
				{
					//if( ($rcv_by_batch_barcode_arr[$row[csf('barcode_no')]] =="" && ( ($cbo_process==33 && $variable_set_source==1) || $cbo_process==31) ) ||  ($cbo_process !=31 || ($cbo_process==33 && $variable_set_source!=1) ) )

					/*
					|	---------------------N.B.---------Conditions below--------------------------
					|	1.if recv by batch not found then 
					|		i. heat setting business processes with variable source is grey roll issue.
					|		ii. Fabric dyeing process
					|	2.if heat setting business processes with variable source is recv for batch.
					|	----------------------------------------------------------------------------
					*/

					if( ($rcv_by_batch_barcode_arr[$row[csf('barcode_no')]] =="" && (($heat_settings_business_processes[$cbo_process]!="" && $variable_set_source==1)|| $cbo_process==31)) 	||  ($heat_settings_business_processes[$cbo_process]!="" && $variable_set_source==2)  )
					{
						//echo $row[csf('barcode_no')].'<br>';
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
							<td width="40">
								<? echo $i; ?>
								<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							</td>
							<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

							<?
								if($is_sales==1)
								{
									echo '<td width="100"><p>'.$row[csf('fso_no')].'</p></td>';
									echo '<td width="100"><p>'.$row[csf('issue_number')].'</p></td>';
								}
								else{
									echo '<td width="100"><p>'.$row[csf('job_no_mst')].'</p></td>'.
									'<td width="110"><p>'.$row[csf('po_number')].'</p></td>'.
									'<td width="80" align="center">'.change_date_format($row[csf('pub_shipment_date')]).'</td>';
								}

							?>
							<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
							<td width="60"><? echo $row[csf('roll_no')]; ?></td>
							<td align="right" width="100"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
							<td align="right" width="100"><? echo number_format($row[csf('qnty')],2); ?></td>
						</tr>
						<?
						$i++;
					}
				}
			}
        	?>
        </table>
    </div>
    <table width="720">
        <tr>
        	<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check all
			</td>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
exit();
}

if($action=="create_barcode_search_list_view_sales")
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$barcode_no =trim($data[3]);
	$po_ids =trim($data[4]);
	$batch_id =trim($data[5]);
	$txt_wo_no =trim($data[6]);
	$cbo_service_company =trim($data[7]);
	$is_sales =$data[8];
	// echo $cbo_service_company.'DFD';die;
	//echo $txt_wo_no;die;

	if ($po_ids=="") 
	{
		$po_ids=0;
	}
	//echo "select company_name from variable_settings_production where company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3";
	$variable_set_finish=return_field_value("company_name","variable_settings_production","company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3","company_name");
	
	$search_field_cond=""; $search_field_batch_subpro_cond=$search_field_batch_cond="";
	if(trim($data[0])!="")
	{
		if($search_by==1)
		{
			$search_field_cond="and d.job_no like '$search_string'";
		}
		else if($search_by==2)
		{
			$search_field_cond="and g.issue_number like '$search_string'";
		}
	}
	
	if($barcode_no!="")
	{
		$barcode_cond=" and b.barcode_no='$barcode_no'";
	}


	$wo_fso_sql =sql_select("SELECT c.job_no, c.id from wo_booking_dtls a, wo_booking_dtls b, fabric_sales_order_mst c where a.po_break_down_id=b.po_break_down_id and a.booking_type=3 and b.booking_type=1 and b.booking_no=c.sales_booking_no 
	and a.status_active=1 and b.status_active=1 and a.booking_no='$txt_wo_no' and c.company_id = $company_id
	group by c.job_no, c.id");

	foreach ($wo_fso_sql as $val) {
		$fso_id_arr[$val[csf("id")]]=$val[csf("id")];
	}


	if(!empty($fso_id_arr))
	{
		$sql ="SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no as fso_no, d.within_group, d.po_buyer, d.buyer_id, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, null as pub_shipment_date 
from inv_issue_master g, inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d
where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and g.company_id=$company_id and b.is_returned=0  and b.booking_without_order =0 and b.is_sales=1 and d.id in (".implode(',',$fso_id_arr).") 
group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no, d.within_group, d.po_buyer, d.buyer_id, b.qc_pass_qnty_pcs order by b.barcode_no";
	}

	//echo $sql;
	$result = sql_select($sql); 
	
	$barCode = array();
	$prodId = array();
	// $scanned_barcode_arr=array();
	foreach($result as $row)
	{
		$barCode[] = $row[csf('barcode_no')];
		//$prodId[] = $row[csf('prod_id')];
		$prodId[$row[csf('prod_id')]] = $row[csf('prod_id')];
		
		/*if($row[csf('entry_form')] == '63') // 63 entry form not found for ref RpC-GIRS-22-00001
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}*/
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}
	$barcode_arr = array_filter(array_unique($barcode_arr));

	if(count($barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $all_barcode_cond= "";

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
		$barcodeData=sql_select("SELECT a.barcode_no from pro_roll_details a where a.entry_form in(63) and a.is_returned=0 and a.is_rcv_done=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
	}

	if(!empty($barcode_arr))
	{
		$rcv_by_batch_arr = sql_select("select a.barcode_no from pro_roll_details a where a.entry_form in (62,66) and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($rcv_by_batch_arr as $row)
		{
			$rcv_by_batch_n_fin_production_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
	}
	
	//echo count($prodId);
	//echo "<pre>";
	// print_r($scanned_barcode_arr);
	
	//product_details_master
	$product_arr=return_library_array( "SELECT id, product_name_details FROM product_details_master WHERE item_category_id=13 AND id IN(".implode(",",$prodId).")",'id','product_name_details');
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="150">Fabric Description</th>
			<th width="100">Sales_order No</th>
            <th width="100">Barcode No</th>
            <th width="60">Roll No</th>
            <th width="100">Qty In Pcs</th>
            <th width="100">Roll Qty.</th>
        </thead>
	</table>
	<div style="width:820px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="" && $rcv_by_batch_n_fin_production_barcode_arr[$row[csf('barcode_no')]] =="" )
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td align="center" width="40">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>

						<?
							echo '<td width="100"><p>'.$row[csf('fso_no')].'</p></td>';
						?>
						<td width="100"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td align="center" width="60"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right" width="100"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
                        <td align="right" width="100"><? echo number_format($row[csf('qnty')],2); ?></td>
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
        	<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check all
			</td>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
<?	
exit();
}

if($action=="populateBarcodeData")
{

	$data = explode("__",$data);
	$barcode_no = $data[0];
	$company_id=$data[1];
	//already scan checking
	/*
	$sql="
	SELECT 
		c.barcode_no
	FROM 
		inv_receive_master a 
		INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
		INNER JOIN pro_roll_details c ON b.id = c.dtls_id
	WHERE 
		c.entry_form = 63 
		AND c.status_active = 1 
		AND c.is_deleted = 0 
		AND c.barcode_no IN (".$barcode_no.")";
	*/
	/*
	$sql="
	SELECT 
		c.barcode_no
	FROM 
		pro_roll_details c 
	WHERE 
		c.entry_form = 63 
		AND c.status_active = 1 
		AND c.is_deleted = 0 
		AND c.barcode_no IN (".$barcode_no.")";
	
	$scanArray=sql_select($sql);
	if(!empty($scanArray))
	{
		echo '101__';
		die;
	}*/

	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	/*$sql="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, 
    b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, 
    c.id as mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs 
	FROM inv_receive_master a 
   	INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
   	INNER JOIN pro_roll_details c ON b.id = c.dtls_id
	WHERE a.receive_basis<>9
	AND a.entry_form IN(2,22)
	AND c.entry_form in(2,22)
	AND c.status_active = 1 
	AND c.is_deleted = 0 
	AND c.barcode_no IN(".$barcode_no.")";*/
	
	/*$sql="SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, 
    b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, 
    c.id as mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs 
	FROM inv_receive_master a 
   	INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
   	INNER JOIN pro_roll_details c ON b.id = c.dtls_id
	WHERE c.entry_form = 64
	AND c.roll_no>0 
	AND c.status_active = 1 
	AND c.is_deleted = 0 
	AND c.barcode_no IN(".$barcode_no.")";*/
	
	//06.01.2020
	/*
	$sql="
	SELECT 
		a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, 
		b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, 
		c.id as mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs 
    FROM 
		inv_receive_master a 
       INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
       INNER JOIN pro_roll_details c ON b.id = c.dtls_id
   	WHERE 
		c.entry_form = 64
		AND c.roll_no>0 
		AND c.status_active = 1 
		AND c.is_deleted = 0 
		AND c.barcode_no IN(".$barcode_no.")
		AND (c.mst_id, c.barcode_no) IN(
			SELECT 
				MAX(bm.id) AS id, bd.barcode_no 
			FROM 
				pro_batch_create_mst bm 
				INNER JOIN pro_batch_create_dtls bd ON bm.id = bd.mst_id 
			WHERE bd.barcode_no IN(".$barcode_no.") 
			GROUP BY bd.barcode_no)";
	*/
	
	/*
	$sqlBatch=sql_select("SELECT a.id, a.batch_no, a.color_id, b.po_id, b.barcode_no  
	FROM pro_batch_create_mst a 
	INNER JOIN pro_batch_create_dtls b ON a.id = b.mst_id
	WHERE a.status_active=1 
	AND a.is_deleted = 0 
	AND b.barcode_no IN(".implode(",",$barCode).")");
	*/
	

	$variable_set_finish=return_field_value("company_name","variable_settings_production","company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3","company_name");

	if ($variable_set_finish) 
	{
		//e.id as mst_id, e.id as roll_id
		$sql="select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
		from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
		where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=33 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
		group by a.id,a.company_id,a.service_source, 
		a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, d.id, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
		union all 
		select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id,null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
		from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
		where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=33 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=1 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
		group by a.id,a.company_id,a.service_source, 
		a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty,e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no "; 
		$data_array=sql_select($sql);
		$processID=33;
		if(empty($data_array) && $processID==33)
		{
			$sql_special_finish="select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=34 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, d.id, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=34 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=1 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty,e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no "; 
			$data_array=sql_select($sql_special_finish);
			$processID=34;
		}
		if(empty($data_array) && $processID==34)
		{
			$sql_drying="select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=31 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=0  and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, d.id, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,null as po_breakdown_id, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id   and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=31 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=1 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty,e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no "; 
			$data_array=sql_select($sql_drying);
			$processID=31;
		}
		if(empty($data_array) && $processID==31)
		{
			$sql_stentering="select max(x.id) as id,x.company_id,x.receive_basis,x.service_source, x.service_company,x.entry_form,max(x.dtls_id) as dtls_id, x.prod_id,x.body_part_id,x.febric_description_id, x.gsm, x.width,x.width_dia_type, x.barcode_no, x.roll_no, x.roll_id, x.qnty,x.qc_pass_qnty_pcs, x.po_breakdown_id, x.job_no_mst, x.booking_without_order,x.color_id,x.booking_id, x.booking_no,x.batch_id,x.batch_no from(select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=48 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, d.id, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=48 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=1 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no ) x group by x.company_id,x.receive_basis,x.service_source, x.service_company,x.entry_form, x.prod_id,x.body_part_id,x.febric_description_id, x.gsm, x.width,x.width_dia_type, x.barcode_no, x.roll_no, x.roll_id, x.qnty,x.qc_pass_qnty_pcs, x.po_breakdown_id, x.job_no_mst, x.booking_without_order,x.color_id,x.booking_id, x.booking_no,x.batch_id,x.batch_no";  
			$data_array=sql_select($sql_stentering);
			$processID=48;
		}
		if(empty($data_array) && $processID==48)
		{
			$sql_slitting="select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=30 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, d.id, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id   and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=30 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=1 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no "; 
			$data_array=sql_select($sql_slitting);
			$processID=30;
		}
		if(empty($data_array) && $processID==30)
		{
			$sql_heat="select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=32 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, d.id, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=32 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=1 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no "; 
			$data_array=sql_select($sql_heat);
			$processID=32;
		}
		if(empty($data_array) && $processID==32)
		{

			$sql="select 0 as id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,0 as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and e.entry_form=64 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.company_id,a.service_source, 
			a.service_company,a.entry_form, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, d.id, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c,wo_po_break_down d  ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id and c.po_id = d.id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=61 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form=64)
			group by a.company_id,a.service_source, 
			a.service_company,a.entry_form, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, d.id, d.job_no_mst, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, null as po_breakdown_id, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id   and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=64 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=1 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no 
			union all 
			select a.id,a.company_id,null as receive_basis,a.service_source, a.service_company as service_company,a.entry_form,b.id as dtls_id,  b.prod_id as prod_id,c.body_part_id,b.const_composition as febric_description_id, b.gsm, b.dia_width as width,b.width_dia_type, b.barcode_no as barcode_no, b.roll_no as roll_no, b.roll_id as roll_id, b.production_qty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,null as po_breakdown_id, null as job_no_mst, e.booking_without_order,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no   
			from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst f, pro_batch_create_dtls c ,pro_roll_details e
			where a.id=b.mst_id and a.batch_id =f.id and f.id=c.mst_id and a.batch_id =c.mst_id  and b.prod_id=c.prod_id and c.barcode_no=e.barcode_no and c.po_id=e.po_breakdown_id and b.barcode_no=c.barcode_no and a.entry_form=61 and a.company_id=$company_id and b.barcode_no in(".$barcode_no.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.booking_without_order=1 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form=64)
			group by a.id,a.company_id,a.service_source, 
			a.service_company,a.entry_form,b.id, b.prod_id,c.body_part_id,b.const_composition , b.gsm, b.dia_width,b.width_dia_type, b.barcode_no, b.roll_no, b.roll_id, b.production_qty, e.booking_without_order,f.color_id ,f.booking_no, f.booking_no_id,f.id,f.batch_no";

			$data_array = sql_select($sql);
			if(empty($data_array))
			{

				$product_array=array();
				$product_sql = sql_select("select id, detarmination_id, gsm, dia_width,item_description, unit_of_measure from product_details_master where item_category_id=13");
				foreach($product_sql as $row)
				{
					$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
					$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
					$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
					$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
					$product_array[$row[csf("id")]]['item_description']=$row[csf("item_description")];
				}

					$batchNoSql=sql_select("SELECT b.barcode_no,a.id,a.batch_no from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no in(".$barcode_no.")");
					foreach($batchNoSql as $row)
					{
						$dataArr_batchNo[$row[csf('barcode_no')]]['batch_no']=$row[csf('batch_no')];
						$dataArr_batchNo[$row[csf('barcode_no')]]['batch_id']=$row[csf('id')];
					}

					$data_array_info=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.location_id, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.machine_no_id, b.brand_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.po_breakdown_id, c.barcode_no, c.roll_no, c.booking_no as bwo, c.booking_without_order, c.is_sales,a.store_id FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
					WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in(".$barcode_no.")"); //and c.barcode_no in($barcode_nos)

					foreach($data_array_info as $row)
					{
						$dataArr[$row[csf('barcode_no')]]['body_part_id']=$row[csf('body_part_id')];
						$dataArr[$row[csf('barcode_no')]]['fabric_desc_id']=$row[csf('febric_description_id')];
					}

					$sql="select b.id,b.company_id,null as receive_basis,b.knit_dye_source as service_source,b.knit_dye_company as service_company,a.entry_form,c.id as dtls_id, c.prod_id as prod_id,null as febric_description_id,null as gsm, null as width, null as width_dia_type,e.barcode_no as barcode_no, e.roll_no as roll_no, e.roll_id as roll_id,
					c.issue_qnty as qnty ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs, d.id as po_breakdown_id, d.job_no_mst as job_no_mst, e.booking_without_order,c.color_id as color_id,null as booking_id,null as booking_no,null as batch_id,null as batch_no,f.body_part_id as  body_part_id
					from inv_issue_master b,inv_grey_fabric_issue_dtls c,inv_transaction f, order_wise_pro_details a, wo_po_break_down d,pro_roll_details e 
					where b.id=c.mst_id and c.trans_id=f.id and  f.id=a.trans_id and c.trans_id=a.trans_id and a.po_breakdown_id =d.id and d.id=e.po_breakdown_id and b.id=e.mst_id and c.id=e.dtls_id and e.entry_form=61 and b.entry_form=61 and b.company_id=$company_id and b.status_active=1 and e.booking_without_order=0 
					and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no in(".$barcode_no.") and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0) 
					group by  b.id,b.company_id,b.knit_dye_source,b.knit_dye_company,a.entry_form,c.id, c.prod_id,e.barcode_no, e.roll_no,e.roll_id,
					c.issue_qnty , d.id, d.job_no_mst, e.booking_without_order,c.color_id,f.body_part_id 
					union all 
					select b.id,b.company_id,null as receive_basis,b.knit_dye_source as service_source,b.knit_dye_company as service_company,null as entry_form,c.id as dtls_id, c.prod_id as prod_id,null as febric_description_id,null as gsm, null as width, null as width_dia_type,e.barcode_no as barcode_no, 
					e.roll_no as roll_no, e.roll_id as roll_id, c.issue_qnty as qnty ,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,null as po_breakdown_id, null as job_no_mst, e.booking_without_order,c.color_id as color_id,e.po_breakdown_id as booking_id,null as booking_no,null as batch_id,null as batch_no,f.body_part_id as body_part_id
					from inv_issue_master b,inv_grey_fabric_issue_dtls c,inv_transaction f,pro_roll_details e
					where b.id=c.mst_id and c.trans_id=f.id 
					and b.id=e.mst_id and c.id=e.dtls_id and e.entry_form=61 and b.entry_form=61 and b.company_id=$company_id and b.status_active=1  and b.is_deleted=0 and e.booking_without_order=1 
					and c.status_active=1 and c.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and e.barcode_no in(".$barcode_no.") and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0 and is_returned=0 and is_rcv_done=0)
					group by b.id,b.company_id,b.knit_dye_source,b.knit_dye_company,c.id, c.prod_id,e.barcode_no, e.roll_no, e.roll_id, c.issue_qnty , e.booking_without_order,c.color_id,e.po_breakdown_id,f.body_part_id";
					$data_array = sql_select($sql);	
					$issueQuryeStatus=101;			
			}
		}
	}
	else
	{
		$sql="select f.id,f.company_id,null as receive_basis,null as service_source, f.working_company_id as service_company,c.id as dtls_id,c.prod_id as prod_id,c.body_part_id,c.item_description as febric_description_id,null as gsm,null as width,c.width_dia_type,c.barcode_no, c.roll_no as roll_no, c.roll_id as roll_id, c.batch_qnty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,d.id as po_breakdown_id, d.job_no_mst as job_no_mst,e.booking_without_order,e.entry_form,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no 
		from pro_batch_create_mst f,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e 
		where f.id=c.mst_id and c.po_id =d.id and  d.id=e.po_breakdown_id and f.id=e.mst_id and c.id=e.dtls_id  and c.barcode_no=e.barcode_no and e.entry_form=64 and f.company_id=$company_id  and c.barcode_no in(".$barcode_no.")  and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0) 
		group by f.id,f.company_id,f.working_company_id,c.id,c.prod_id,c.body_part_id,c.item_description,c.width_dia_type,c.barcode_no, c.roll_no, c.roll_id, c.batch_qnty,d.id, d.job_no_mst,e.booking_without_order,e.entry_form,f.color_id,f.booking_no_id, f.booking_no,f.id,f.batch_no 
		union all 
		select f.id,f.company_id,null as receive_basis,null as service_source, f.working_company_id as service_company,c.id as dtls_id,c.prod_id as prod_id,c.body_part_id,c.item_description as febric_description_id,null as gsm,null as width,c.width_dia_type,c.barcode_no, c.roll_no as roll_no, c.roll_id as roll_id, c.batch_qnty as qnty,sum(e.qc_pass_qnty_pcs) as qc_pass_qnty_pcs,d.id as po_breakdown_id, d.job_no_mst as job_no_mst,e.booking_without_order,e.entry_form,f.color_id,f.booking_no_id as booking_id, f.booking_no,f.id as batch_id,f.batch_no    
		from pro_batch_create_mst f,pro_batch_create_dtls c, wo_po_break_down d,pro_roll_details e
		where f.id=c.mst_id and c.po_id =d.id and d.id=e.po_breakdown_id and c.barcode_no=e.barcode_no and e.entry_form=61 and f.company_id=$company_id  and c.barcode_no in(".$barcode_no.") and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.barcode_no not in(select barcode_no from  pro_roll_details where  entry_form in(63,64) and status_active=1 and is_deleted=0)
		group by f.id,f.company_id,f.working_company_id,c.id,c.prod_id,c.body_part_id,c.item_description,c.width_dia_type,c.barcode_no, c.roll_no, c.roll_id, c.batch_qnty,d.id, d.job_no_mst,e.booking_without_order,e.entry_form,f.color_id,f.booking_no_id, f.booking_no,f.id,f.batch_no 
		";
		$data_array=sql_select($sql);
	}

	//print_r($data_array);die;
	//echo $processID;die;
	// and e.barcode_no not in(select barcode_no from pro_roll_details where entry_form=63 and status_active=1 and is_deleted=0) 

	/*$sqlBatchBarcode="SELECT r.barcode_no FROM pro_roll_details r WHERE r.entry_form = 64 AND r.roll_no>0 AND r.status_active = 1 AND r.is_deleted = 0 AND r.barcode_no IN(".$barcode_no.") AND r.mst_id IN(SELECT MAX(bm.id) AS id FROM pro_batch_create_mst bm INNER JOIN pro_batch_create_dtls bd ON bm.id = bd.mst_id WHERE bd.barcode_no IN(".$barcode_no.") GROUP BY bd.barcode_no)";
	
	$sql="
	SELECT 
		a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, 
		b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.color_id, 
		c.id as mst_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.booking_without_order 
    FROM 
		inv_receive_master a 
       INNER JOIN pro_grey_prod_entry_dtls b ON a.id = b.mst_id
       INNER JOIN pro_roll_details c ON b.id = c.dtls_id
   	WHERE 
		a.receive_basis<>9
		AND a.entry_form IN(2,22)
		AND c.entry_form IN(2,22)
		AND c.roll_no>0 
		AND c.status_active = 1 
		AND c.is_deleted = 0 
		AND c.barcode_no IN(".$sqlBatchBarcode.")";*/

	//echo $sql;
	
	if(empty($data_array))
	{
		$rtnData='0__';
		echo $rtnData;
		die;
	}
	
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$nonOrderbookingId=array();
	$prodIds="";
	$jobNos="";
	$bookingIds="";
	foreach($data_array as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if($row[csf('booking_without_order')] != 1)
		{
			$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		else
		{
			$bookingIds.=$row[csf('booking_id')].",";
			$nonOrderbookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		$bookingId[$row[csf('booking_id')]]=$row[csf('booking_id')];


		$prodIds.=$row[csf('prod_id')].",";
		$jobNos.="'".$row[csf('job_no_mst')]."',";
	}
	$prodIds=chop($prodIds,",");
	$jobNos=chop($jobNos,",");
	$bookingIds=chop($bookingIds,",");
	//echo "<pre>";
	//print_r($poBreakdownId);
	$nonOrderBookingLib=return_library_array("select id, booking_no from WO_NON_ORD_SAMP_BOOKING_MST where id in($bookingIds)",'id','booking_no');
	//for buyer
	$poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);
	$nonOrderBuyerArray = get_nonOrderBookingBuyerFor_GreyRollIssueToProcess($nonOrderbookingId);
	
	$po_dtls_data=sql_select("select job_no,buyer_name from wo_po_details_master where status_active=1 and is_deleted=0 and job_no in(".$jobNos.")  group by buyer_name,job_no");
 	foreach($po_dtls_data as $row){
 		$po_dtls_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
 	}
	$prod_determination=sql_select("select a.id, a.detarmination_id,b.barcode_no from product_details_master a,pro_batch_create_dtls b where a.id=b.prod_id and a.status_active=1 and a.is_deleted=0 and a.id in(".$prodIds.") and b.barcode_no in(".$barcode_no.") group by a.id, a.detarmination_id,b.barcode_no");
 	foreach($prod_determination as $row){
 		$prod_arr[$row[csf("barcode_no")]]['deter_d']=$row[csf("detarmination_id")];
 	}
	//print_r($prod_arr);

	if($issueQuryeStatus==101)
	{
		$nonOrderBookingBatchNo=sql_select("select a.id, a.batch_no,b.width_dia_type,b.barcode_no from pro_batch_create_mst a,pro_batch_create_dtls b  where a.id=b.mst_id and a.booking_no_id in($bookingIds) and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach($nonOrderBookingBatchNo as $rowData){
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['batch_no']=$rowData[csf("batch_no")];
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['batch_id']=$rowData[csf("id")];
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['width_dia_type']=$rowData[csf("width_dia_type")];
		}
	}


	$i = 0;
	$rtnData='';
	foreach($data_array as $row)
	{
		$i++;	
		//for buyer
		if($row[csf('booking_without_order')] != 1)
		{
			$row['buyer']=$poArray[$row[csf('po_breakdown_id')]]['buyer_name'];
		}
		else
		{
			$row[csf('booking_no')]=$nonOrderBookingLib[$row[csf('booking_id')]];
			$row['buyer']=$nonOrderBuyerArray[$row[csf('po_breakdown_id')]]['buyer_name'];
		}
		
		
		$row['order_no']=$poArray[$row[csf('po_breakdown_id')]]['po_number'];
		$row['order_id']=$row[csf('po_breakdown_id')];
		
		$rollWeight=number_format($row[csf('qnty')],2);
		$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
		
			
		$row['service_company']=$company_name_array[$row[csf('service_company')]];
	
		//$row['service_company']=get_knitting_company_details($row[csf('service_source')],$row[csf('service_company')]);
		
		$row['dia_type']=$fabric_typee[$row[csf('width_dia_type')]];
		$row['color']=$color_arr[$row[csf('color_id')]];
		$row['construction']=$row[csf('febric_description_id')];
		$row['job_no']=$row[csf('job_no_mst')];
		$row['batch_id']=$row[csf('batch_id')];
		$row['batch_no']=$row[csf('batch_no')];
		$row['deter_d']=$prod_arr[$row[csf("barcode_no")]]['deter_d'];
		$row['buyer_id']=$po_dtls_arr[$row[csf("job_no_mst")]]['buyer_name'];

		//receive_basis
		$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
		$receive_basis_id=$receiveBasisArray['id'];
		$receive_basis_dtls=$receiveBasisArray['dtls'];
		
		$row['mst_id'] = 0;
		$row['dtls_id'] = 0;

		if($issueQuryeStatus==101)
		{
			$row[csf('body_part_id')]=$dataArr[$row[csf('barcode_no')]]['body_part_id'];
			$row['construction']=$product_array[$row[csf("prod_id")]]['item_description'];
			$row['deter_d']=$dataArr[$row[csf('barcode_no')]]['fabric_desc_id'];
			$row[csf('gsm')]=$product_array[$row[csf("prod_id")]]['gsm'];
			$row[csf('width')]=$product_array[$row[csf("prod_id")]]['dia_width'];
			
			$row['batch_no']=$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['batch_no'];
			$row['batch_id']=$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['batch_id'];
			$row['dia_type']=$fabric_typee[$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['width_dia_type']];
			$row[csf('width_dia_type')]=$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['width_dia_type'];
			if($row['batch_no']=="" && $row['batch_id']=="")
			{
				$row['batch_no']=$dataArr_batchNo[$row[csf('barcode_no')]]['batch_no'];
				$row['batch_id']=$dataArr_batchNo[$row[csf('barcode_no')]]['batch_id'];
			}

		}
		
		$rtnData .=	$row[csf('barcode_no')]."**".
					$row[csf('roll_no')]."**".
					$row['batch_no']."**".
					$row[csf('prod_id')]."**".
					$body_part[$row[csf('body_part_id')]]."**".
					$row['construction']."**".
					$row[csf('gsm')]."**".
					$row[csf('width')]."**".
					$row['color']."**".
					$row['dia_type']."**".
					$rollWeight."**".
					$qtyInPcs."**".
					$row['buyer']."**".
					$row['job_no']."**".
					$row['order_no']."**".
					$row['service_company']."**".
					$receive_basis_dtls."**".
					$row[csf('booking_no')]."**".
					$row['mst_id']."**".
					$row['dtls_id']."**".
					$row[csf('color_id')]."**".
					$row[csf('company_id')]."**".
					$receive_basis_id."**".
					$row['order_id']."**".
					$row['batch_id']."**".
					$row[csf('booking_id')]."**".
					$row[csf('body_part_id')]."**".
					$row[csf('width_dia_type')]."**".
					$row[csf('service_company')]."**".
					$row[csf('booking_without_order')]."**".
					$row['deter_d']."**".
					$row['buyer_id']."**".
					$row[csf('roll_no')]."__";
	}
	$rtnData=chop($rtnData,'__');
	echo $rtnData;
	die;
}

if($action=="populateBarcodeDataOutbound")
{
	$data = explode("__",$data);
	$barcode_no = $data[0];
	$company_id=$data[1];
	$txt_wo_no=$data[2];
	$po_ids=$data[3];
	$cbo_service_company=$data[4];
	$is_sales=$data[5];
	if ($po_ids=="") 
	{
		$po_ids=0;
	}
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	
	//$variable_set_finish=return_field_value("company_name","variable_settings_production","company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3","company_name");

	if($is_sales==1)
	{
		$sql = "SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, null as job_no_mst, d.job_no as po_number, d.within_group, d.buyer_id, d.po_buyer, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, b.is_sales from inv_issue_master g, inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d, dyeing_work_order_mst e, dyeing_work_order_dtls f where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.barcode_no in($barcode_no) and d.id in ($po_ids) and b.is_sales=1 and d.id=e.po_breakdown_id and e.id=f.mst_id and ((f.issue_no=g.issue_number and e.wo_basis=1) or e.wo_basis=2) and e.do_no='$txt_wo_no' group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no, d.within_group, d.buyer_id, d.po_buyer, b.qc_pass_qnty_pcs, b.is_sales";
	}
	else
	{

		$sql="SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no_mst, d.po_number, sum(b.qnty) qnty, b.qc_pass_qnty_pcs 
		from inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, wo_po_break_down d 
		where a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.barcode_no in($barcode_no) and d.id in ($po_ids) and b.is_sales !=1
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, d.job_no_mst,d.po_number, b.qc_pass_qnty_pcs 
		union all
		SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, null job_no_mst, null po_number, sum(b.qnty) qnty, b.qc_pass_qnty_pcs 
		from inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, wo_non_ord_samp_booking_mst d 
		where a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.booking_without_order=1
		and b.barcode_no in($barcode_no) and d.booking_no ='$txt_wo_no' and b.is_sales!=1
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, b.qc_pass_qnty_pcs
		union all
		SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, null job_no_mst, null po_number, sum(b.qnty) qnty, b.qc_pass_qnty_pcs 
		from inv_issue_master e, inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, wo_non_ord_knitdye_booking_mst d 
		where e.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.fab_booking_id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.booking_without_order=1 and b.is_sales!=1
		and b.barcode_no in($barcode_no) and d.booking_no ='$txt_wo_no' and e.knit_dye_company=$cbo_service_company
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_without_order, b.po_breakdown_id, b.qc_pass_qnty_pcs";
	}
	// echo $sql;die;
	$data_array=sql_select($sql);
	
	
	if(empty($data_array))
	{
		$rtnData='0__';
		echo $rtnData.$sql;
		die;
	}
	
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$nonOrderbookingId=array();
	$prodIds="";
	$jobNos="";
	$bookingIds="";
	foreach($data_array as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if($row[csf('booking_without_order')] != 1)
		{
			$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		else
		{
			$bookingIds.=$row[csf('po_breakdown_id')].",";
			$nonOrderbookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		$bookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];


		$prodIds.=$row[csf('prod_id')].",";
		$jobNos.="'".$row[csf('job_no_mst')]."',";
	}
	$prodIds=chop($prodIds,",");
	$jobNos=chop($jobNos,",");
	$bookingIds=chop($bookingIds,",");

	if(!empty($barCode))
	{
		$rcv_by_batch_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form=62 and b.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}
	
	if(!empty($rcv_by_batch_arr))
	{
		$rtnData="1__Receive for batch entry found.\nBarcode no: ".$rcv_by_batch_arr[0][csf("barcode_no")]."\nReceive for Batch No. :".$rcv_by_batch_arr[0][csf("recv_number")];;
		echo $rtnData;
		die;
	}

	if(!empty($barCode))
	{
		$roll_issue_to_process_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form=63 and b.entry_form=63 and a.is_rcv_done=0 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}

	if(!empty($roll_issue_to_process_arr))
	{
		$rtnData="1__Grey Fab. issue to process found.\nBarcode no: ".$roll_issue_to_process_arr[0][csf("barcode_no")]."\nIssue to process No. :".$roll_issue_to_process_arr[0][csf("recv_number")];
		echo $rtnData;
		die;
	}

	if(!empty($barCode))
	{
		$roll_production_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and a.entry_form=66 and b.entry_form=66 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}

	if(!empty($roll_production_arr))
	{
		$rtnData="1__Finish fabric roll wise production found.\nBarcode no: ".$roll_production_arr[0][csf("barcode_no")]."\nProduction No. :".$roll_production_arr[0][csf("recv_number")];
		echo $rtnData;
		die;
	}

	if($is_sales !=1)
	{
		$nonOrderBookingLib=return_library_array("select id, booking_no from wo_non_ord_samp_booking_mst where id in($bookingIds)",'id','booking_no');
		//for buyer
		$poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);

		$po_dtls_data=sql_select("select job_no,buyer_name from wo_po_details_master where status_active=1 and is_deleted=0 and job_no in(".$jobNos.")  group by buyer_name,job_no");
		foreach($po_dtls_data as $row){
			$po_dtls_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		}
	}
	
 	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
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
	unset($deter_array);
	//print_r($prod_arr);

	/* if($issueQuryeStatus==101)
	{
		$nonOrderBookingBatchNo=sql_select("select a.id, a.batch_no,b.width_dia_type,b.barcode_no from pro_batch_create_mst a, pro_batch_create_dtls b  where a.id=b.mst_id and a.booking_no_id in($bookingIds) and a.booking_without_order=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach($nonOrderBookingBatchNo as $rowData){
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['batch_no']=$rowData[csf("batch_no")];
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['batch_id']=$rowData[csf("id")];
	 		$nonOrderBookingBatchNoArr[$rowData[csf("barcode_no")]]['width_dia_type']=$rowData[csf("width_dia_type")];
		}
	} */


	$i = 0;
	$rtnData='';
	foreach($data_array as $row)
	{
		$i++;	
		//for buyer
		if($row[csf('booking_without_order')] != 1)
		{
			if($is_sales==1)
			{
				//d.within_group, d.buyer_id, d.po_buyer
				if($row[csf('within_group')] ==1)
				{
					$row['buyer'] = $buyer_name_array[$row[csf('po_buyer')]];
					$row['buyer_id']=$row[csf('po_buyer')];
				}
				else{
					$row['buyer'] = $buyer_name_array[$row[csf('buyer_id')]];
					$row['buyer_id']=$row[csf('buyer_id')];
				}
				$row['order_no']=$row[csf('po_number')];
			}
			else
			{
				$row['buyer']=$poArray[$row[csf('po_breakdown_id')]]['buyer_name'];
				$row['buyer']=$buyer_name_array[$po_dtls_arr[$row[csf("job_no_mst")]]['buyer_name']];
				$row['buyer_id']=$po_dtls_arr[$row[csf("job_no_mst")]]['buyer_name'];
				$row['order_no']=$poArray[$row[csf('po_breakdown_id')]]['po_number'];
			}
			
		}
		else
		{
			$row[csf('booking_id')]=$row[csf('po_breakdown_id')];//new add
			$row[csf('booking_no')]=$nonOrderBookingLib[$row[csf('po_breakdown_id')]];
			$row['buyer']=$nonOrderBuyerArray[$row[csf('po_breakdown_id')]]['buyer_name'];
		}
		
		$row['order_id']=$row[csf('po_breakdown_id')];
		
		$rollWeight=number_format($row[csf('qnty')],2);
		$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
			
		$row['service_company']=$company_name_array[$row[csf('service_company')]];
	
		//$row['service_company']=get_knitting_company_details($row[csf('service_source')],$row[csf('service_company')]);
		
		$row['dia_type']=$fabric_typee[$row[csf('width_dia_type')]];
		$row['color']=$color_arr[$row[csf('color_id')]];
		$row['construction']=$composition_arr[$row[csf('febric_description_id')]];
		$row['job_no']=$row[csf('job_no_mst')];
		$row['batch_id']=$row[csf('batch_id')];
		$row['batch_no']=$row[csf('batch_no')];
		$row['deter_d']=$row[csf("febric_description_id")];//$prod_arr[$row[csf("barcode_no")]]['deter_d'];

		//receive_basis
		$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
		$receive_basis_id=$receiveBasisArray['id'];
		$receive_basis_dtls=$receiveBasisArray['dtls'];
		
		$row['mst_id'] = 0;
		$row['dtls_id'] = 0;

		if($issueQuryeStatus==101)
		{
			$row[csf('body_part_id')]=$dataArr[$row[csf('barcode_no')]]['body_part_id'];
			$row['construction']=$product_array[$row[csf("prod_id")]]['item_description'];
			$row['deter_d']=$dataArr[$row[csf('barcode_no')]]['fabric_desc_id'];
			$row[csf('gsm')]=$product_array[$row[csf("prod_id")]]['gsm'];
			$row[csf('width')]=$product_array[$row[csf("prod_id")]]['dia_width'];
			$row['batch_no']=$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['batch_no'];
			$row['batch_id']=$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['batch_id'];
			$row['dia_type']=$fabric_typee[$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['width_dia_type']];
			$row[csf('width_dia_type')]=$nonOrderBookingBatchNoArr[$row[csf("barcode_no")]]['width_dia_type'];

		}
		
		$rtnData .=	$row[csf('barcode_no')]."**".
					$row[csf('roll_no')]."**".
					$row['batch_no']."**".
					$row[csf('prod_id')]."**".
					$body_part[$row[csf('body_part_id')]]."**".
					$row['construction']."**".
					$row[csf('gsm')]."**".
					$row[csf('width')]."**".
					$row['color']."**".
					$row['dia_type']."**".
					$rollWeight."**".
					$qtyInPcs."**".
					$row['buyer']."**".
					$row['job_no']."**".
					$row['order_no']."**".
					$row['service_company']."**".
					$receive_basis_dtls."**".
					$row[csf('booking_no')]."**".
					$row['mst_id']."**".
					$row['dtls_id']."**".
					$row[csf('color_id')]."**".
					$row[csf('company_id')]."**".
					$receive_basis_id."**".
					$row['order_id']."**".
					$row['batch_id']."**".
					$row[csf('booking_id')]."**".
					$row[csf('body_part_id')]."**".
					$row[csf('width_dia_type')]."**".
					$row[csf('service_company')]."**".
					$row[csf('booking_without_order')]."**".
					$row['deter_d']."**".
					$row['buyer_id']."**".
					$row['roll_id']."**".
					$is_sales."__";
	}
	$rtnData=chop($rtnData,'__');
	echo $rtnData;
	die;
}

if($action=="populateBarcodeDataAopSales")
{
	$data = explode("__",$data);
	$barcode_no = $data[0];
	$company_id=$data[1];
	$txt_wo_no=$data[2];
	$po_ids=$data[3];
	$cbo_service_company=$data[4];
	$is_sales=1;
	if ($po_ids=="") 
	{
		$po_ids=0;
	}
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	
	//$variable_set_finish=return_field_value("company_name","variable_settings_production","company_name = $company_id and fabric_roll_level = 1 and item_category_id = 2 and variable_list = 3","company_name");


	$wo_fso_sql =sql_select("SELECT c.job_no, c.id from wo_booking_dtls a, wo_booking_dtls b, fabric_sales_order_mst c where a.po_break_down_id=b.po_break_down_id and a.booking_type=3 and b.booking_type=1 and b.booking_no=c.sales_booking_no and a.status_active=1 and b.status_active=1 and a.booking_no='$txt_wo_no' and c.company_id=$company_id group by c.job_no, c.id");

	foreach ($wo_fso_sql as $val) {
		$fso_id_arr[$val[csf("id")]]=$val[csf("id")];
	}

	$sql="SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, null as job_no_mst, d.job_no as po_number, d.within_group, d.buyer_id, d.po_buyer, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, b.is_sales 
	from inv_issue_master g, inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d 
	where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.barcode_no in($barcode_no) and d.id in (".implode(",",$fso_id_arr).") and b.is_sales=1
	group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, d.job_no, d.within_group, d.buyer_id, d.po_buyer, b.qc_pass_qnty_pcs, b.is_sales";

	// echo $sql;die;
	$data_array=sql_select($sql);
	
	
	if(empty($data_array))
	{
		$rtnData='0__';
		echo $rtnData.$sql;
		die;
	}
	
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$nonOrderbookingId=array();
	$prodIds="";
	$jobNos="";
	$bookingIds="";
	foreach($data_array as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if($row[csf('booking_without_order')] != 1)
		{
			$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		else
		{
			$bookingIds.=$row[csf('po_breakdown_id')].",";
			$nonOrderbookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		$bookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];


		$prodIds.=$row[csf('prod_id')].",";
		$jobNos.="'".$row[csf('job_no_mst')]."',";
	}
	$prodIds=chop($prodIds,",");
	$jobNos=chop($jobNos,",");
	$bookingIds=chop($bookingIds,",");


	if(!empty($barCode))
	{
		$rcv_by_batch_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form=62 and b.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}
	
	if(!empty($rcv_by_batch_arr))
	{
		$rtnData="1__Receive for batch entry found.\nBarcode no: ".$rcv_by_batch_arr[0][csf("barcode_no")]."\nReceive for Batch No. :".$rcv_by_batch_arr[0][csf("recv_number")];;
		echo $rtnData;
		die;
	}

	if(!empty($barCode))
	{
		$roll_issue_to_process_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form=63 and b.entry_form=63 and a.is_rcv_done=0 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}

	if(!empty($roll_issue_to_process_arr))
	{
		$rtnData="1__Grey Fab. issue to process found.\nBarcode no: ".$roll_issue_to_process_arr[0][csf("barcode_no")]."\nIssue to process No. :".$roll_issue_to_process_arr[0][csf("recv_number")];
		echo $rtnData;
		die;
	}

	if(!empty($barCode))
	{
		$roll_production_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and a.entry_form=66 and b.entry_form=66 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}

	if(!empty($roll_production_arr))
	{
		$rtnData="1__Finish fabric roll wise production found.\nBarcode no: ".$roll_production_arr[0][csf("barcode_no")]."\nProduction No. :".$roll_production_arr[0][csf("recv_number")];
		echo $rtnData;
		die;
	}

	if($is_sales !=1)
	{
		$nonOrderBookingLib=return_library_array("select id, booking_no from wo_non_ord_samp_booking_mst where id in($bookingIds)",'id','booking_no');
		//for buyer
		$poArray = get_buyerFor_GreyRollIssueToProcess($poBreakdownId);

		$po_dtls_data=sql_select("select job_no,buyer_name from wo_po_details_master where status_active=1 and is_deleted=0 and job_no in(".$jobNos.")  group by buyer_name,job_no");
		foreach($po_dtls_data as $row){
			$po_dtls_arr[$row[csf("job_no")]]['buyer_name']=$row[csf("buyer_name")];
		}
	}

	
 	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
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
	unset($deter_array);
	//print_r($prod_arr);


	$i = 0;
	$rtnData='';
	foreach($data_array as $row)
	{
		$i++;	
		//for buyer

		if($row[csf('within_group')] ==1)
		{
			$row['buyer'] = $buyer_name_array[$row[csf('po_buyer')]];
			$row['buyer_id']=$row[csf('po_buyer')];
		}
		else{
			$row['buyer'] = $buyer_name_array[$row[csf('buyer_id')]];
			$row['buyer_id']=$row[csf('buyer_id')];
		}

		$row['order_no']=$row[csf('po_number')];
		$row['order_id']=$row[csf('po_breakdown_id')];
		
		$rollWeight=number_format($row[csf('qnty')],2);
		$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
			
		$row['service_company_name']=$company_name_array[$row[csf('service_company')]];
		
		$row['dia_type']=$fabric_typee[$row[csf('width_dia_type')]];
		$row['color']=$color_arr[$row[csf('color_id')]];
		$row['construction']=$composition_arr[$row[csf('febric_description_id')]];
		$row['job_no']=$row[csf('job_no_mst')];
		$row['batch_id']=$row[csf('batch_id')];
		$row['batch_no']=$row[csf('batch_no')];
		$row['deter_d']=$row[csf("febric_description_id")];

		//receive_basis
		$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
		$receive_basis_id=$receiveBasisArray['id'];
		$receive_basis_dtls=$receiveBasisArray['dtls'];
		
		$row['mst_id'] = 0;
		$row['dtls_id'] = 0;
		
		$rtnData .=	$row[csf('barcode_no')]."**".
					$row[csf('roll_no')]."**".
					$row['batch_no']."**".
					$row[csf('prod_id')]."**".
					$body_part[$row[csf('body_part_id')]]."**".
					$row['construction']."**".
					$row[csf('gsm')]."**".
					$row[csf('width')]."**".
					$row['color']."**".
					$row['dia_type']."**".
					$rollWeight."**".
					$qtyInPcs."**".
					$row['buyer']."**".
					$row['job_no']."**".
					$row['order_no']."**".
					$row['service_company_name']."**".
					$receive_basis_dtls."**".
					$row[csf('booking_no')]."**".
					$row['mst_id']."**".
					$row['dtls_id']."**".
					$row[csf('color_id')]."**".
					$row[csf('company_id')]."**".
					$receive_basis_id."**".
					$row['order_id']."**".
					$row['batch_id']."**".
					$row[csf('booking_id')]."**".
					$row[csf('body_part_id')]."**".
					$row[csf('width_dia_type')]."**".
					$row[csf('service_company')]."**".
					$row[csf('booking_without_order')]."**".
					$row['deter_d']."**".
					$row['buyer_id']."**".
					$row[csf('roll_id')]."**".
					$row[csf('is_sales')]."__";
	}
	$rtnData=chop($rtnData,'__');
	echo $rtnData;
	die;
}

if($action=="wo_no_issue_popup")
{
	echo load_html_head_contents("WO No Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_company_id;
?> 
	<script>
		function js_set_value(bookingNo,data,id,comp,source,supplier,isSales,wo_entry_form)
		{
			$('#hidden_challan_no').val(data);
			$('#hidden_challan_id').val(id);
			$('#hidden_booking_no').val(bookingNo);
			$('#hidden_source').val(source);
			$('#hidden_suppplier').val(supplier);
			//alert(comp);
			$('#hidden_comp_id').val(comp);
			$('#hidden_is_sales').val(isSales);
			$('#hidden_wo_entry_form').val(wo_entry_form);
			parent.emailwindow.hide();
		}
    </script>
</head>
<body>
<div align="center" style="width:860px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:860px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
                    <th>Is Sales</th>
                    <th>Booking Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Booking No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">  
                        <input type="hidden" name="hidden_challan_id" id="hidden_challan_id">  
                         <input type="hidden" name="hidden_booking_no" id="hidden_booking_no"> 
                         <input type="hidden" name="hidden_comp_id" id="hidden_comp_id">  
                         <input type="hidden" name="hidden_source" id="hidden_source">  
                         <input type="hidden" name="hidden_suppplier" id="hidden_suppplier">  
                         <input type="hidden" name="hidden_is_sales" id="hidden_is_sales">  
                         <input type="hidden" name="hidden_wo_entry_form" id="hidden_wo_entry_form">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',$cbo_company_id,"",0); ?>        
                    </td>
					<td>
						<?
						echo create_drop_down( "cbo_is_sales", 100, $yes_no,"", 0, "--Select--", 1,"",0,"" );
						?>
					</td>
                    <td align="center">
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
					</td>
                    <td align="center">	
                    	<?
                       		$search_by_arr=array(1=>"Booking No",2=>"Job No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
						?>
                    </td>     
                    <td align="center" id="search_by_td">				
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
                    </td> 						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_is_sales').value, 'create_wo_no_search_list_view', 'search_div', 'grey_fabric_roll_issue_to_subcon_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=="create_wo_no_search_list_view")
{
	$data = explode("_",$data);
	$search_string=$data[0];
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$year_id =$data[5];
	$is_sales =$data[6];
	if($company_id==0) { echo "Please Select Company First."; die; }
	
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and c.booking_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			$date_cond2="and a.booking_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			$date_cond3="and c.wo_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and c.booking_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			$date_cond2="and a.booking_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			$date_cond3="and c.wo_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	if($db_type==0) 
	{
		$year_field=" YEAR(c.insert_date) as year"; $year_search=" and YEAR(c.insert_date)=$year_id ";
		$year_field2=" YEAR(a.insert_date) as year"; $year_search=" and YEAR(a.insert_date)=$year_id ";
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(c.insert_date,'YYYY') as year";$year_search="  to_char(c.insert_date,'YYYY')=$year_id ";
		$year_field2=" to_char(a.insert_date,'YYYY') as year";$year_search="  to_char(a.insert_date,'YYYY')=$year_id ";
	}
	
	$search_field_cond="";
	$search_field_cond2="";
	if(trim($data[0])!="")
	{
		if($search_by==1)
		{
			$search_field_cond="and c.booking_no like '%$search_string%' ";
			$search_field_cond2="and a.wo_no like '%$search_string%' ";
			$search_field_cond3="and c.do_no like '%$search_string%' ";
		}
		else if($search_by==2)
		{
			$search_field_cond3="and c.fabric_sales_order_no like '%$search_string%' and $year_search";
		}
	}

	if($is_sales ==2) //sales order NO
	{
		$sql="SELECT $year_field, c.booking_no, c.buyer_id, c.booking_date, c.company_id as book_company, c.supplier_id, c.pay_mode, d.po_break_down_id as po_id, d.job_no, d.process,3 as source, c.booking_type 
		from wo_booking_mst c, wo_booking_dtls d where c.booking_no=d.booking_no and c.booking_type=3 and  c.company_id=$company_id and d.status_active=1 and c.status_active=1 $search_field_cond $date_cond
		union all
		select $year_field2, a.fab_booking_no as booking_no, a.buyer_id, a.booking_date, a.company_id as book_company, a.supplier_id, a.pay_mode, null as po_id, null as job_no, 35 as process,a.aop_source as source, 0 as booking_type  
		from wo_non_ord_aop_booking_mst a, wo_non_ord_aop_booking_dtls b where a.wo_no=a.wo_no and a.fab_booking_no=b.fab_booking_no and a.company_id=$company_id and b.status_active=1 and a.status_active=1 $search_field_cond2 $date_cond2
		union all
		SELECT to_char(c.insert_date,'YYYY') as year, c.booking_no, c.buyer_id, c.booking_date, c.company_id as book_company, c.supplier_id, c.pay_mode, null as po_id, null as job_no, d.process_id as process, c.source, 0 as booking_type
		from wo_non_ord_knitdye_booking_mst c, wo_non_ord_knitdye_booking_dtl d
		where c.booking_no=d.booking_no and c.company_id=$company_id and c.source=3 and d.process_id=31 and d.status_active=1 and c.status_active=1 $search_field_cond $date_cond order by booking_no";
	} 
	else if($is_sales==1) //sales order Yes
	{
		$sql="SELECT to_char(c.insert_date,'YYYY') as year, c.do_no as booking_no, c.buyer_id, c.wo_date as booking_date, c.company_id as book_company, c.dyeing_compnay_id as supplier_id, c.pay_mode, c.po_breakdown_id as po_id, c.fabric_sales_order_no as job_no, 31 as process,3 as source, 0 as booking_type, c.entry_form 
		from dyeing_work_order_mst c, dyeing_work_order_dtls d 
		where c.entry_form!=696 and c.id=d.mst_id and c.company_id=$company_id and d.status_active=1 and c.status_active=1 $date_cond3 $search_field_cond3 

		union all 
		
		SELECT to_char(c.insert_date,'YYYY') as year, c.do_no as booking_no, c.buyer_id, c.wo_date as booking_date, c.company_id as book_company, c.dyeing_compnay_id as supplier_id, c.pay_mode, c.po_breakdown_id as po_id, c.fabric_sales_order_no as job_no, c.process_id as process,3 as source, 0 as booking_type, c.entry_form 
		from dyeing_work_order_mst c, dyeing_work_order_dtls d 
		where c.entry_form=696 and c.id=d.mst_id and c.company_id=$company_id and d.status_active=1 and c.status_active=1 $date_cond3 $search_field_cond3 order by booking_no
		";
	}

	//echo $sql;
	$result = sql_select($sql);
	//print_r($result);

	// ================================= CRM 27503 This concept by Aziz bhai ==========================
	foreach ($result as $row)
	{
		if ($row[csf('booking_type')]==3) 
		{
			$job_no_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		}		
	}
    $all_job_no="";
    foreach ($job_no_arr as $key => $job) 
    {
        if ($all_job_no=="") 
        {
            $all_job_no.= $job;
        }
        else 
        {
            $all_job_no.= "','".$job;
        }
    }
    //echo $all_job_no;
	if (count($job_no_arr)>0) 
	{
		$booking_sql="SELECT job_no, po_break_down_id from wo_booking_dtls where job_no in('$all_job_no') and booking_type=1 and status_active=1 and is_deleted=0 group by job_no, po_break_down_id";
		$booking_sql_result = sql_select($booking_sql);
		foreach ($booking_sql_result as $row)
		{
			$job_wise_po_arr[$row[csf('job_no')]].=$row[csf('po_break_down_id')].',';
		}
		// echo "<pre>";print_r($job_wise_po_arr);
	}
	// ================================= CRM 27503 This concept by Aziz bhai ==========================

	foreach ($result as $row)
	{ 
		$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['company_id']=$row[csf('book_company')];
		if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5){
			$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['dye_comp']=$row[csf('supplier_id')];
		}
		else
		{
			$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['dye_comp']=$row[csf('supplier_id')];
		}
		$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['booking_no']=$row[csf('booking_no')];
		$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['process'].=$row[csf('process')].',';
		$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['job_no']=$row[csf('job_no')];
		$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['buyer_id']=$row[csf('buyer_id')];
		$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['booking_date']=$row[csf('booking_date')];
		$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['year']=$row[csf('year')];
		$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['entry_form']=$row[csf('entry_form')];
		
		if ($row[csf('booking_type')]==3) 
		{
			$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['po_id'].=$job_wise_po_arr[$row[csf('job_no')]].',';
		}
		else
		{
			$service_wo_arr[$row[csf('booking_no')]][$row[csf('pay_mode')]]['po_id'].=$row[csf('po_id')].',';
		}		

		$service_wo_arr2[$row[csf('booking_no')]][$row[csf('pay_mode')]]['source']=$row[csf('source')];
		$service_wo_arr2[$row[csf('booking_no')]][$row[csf('pay_mode')]]['supplier_id']=$row[csf('supplier_id')];
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
        <thead>
            <th width="20">SL</th>
            <th width="120">Company</th>
            <th width="110">Booking No</th>
            <th width="100">Job No</th>
             <th width="110">Buyer</th>
           	<th  width="80">Booking date</th>
            <th width="80">Pay Mode</th>
            <th width="">Supplier</th>
        </thead>
	</table>
	<div style="width:820px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($service_wo_arr as $bookingNo=>$book_data)
            { 
			 foreach ($book_data as $pay_mode_id=>$row)
             { 
			
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$knit_comp="&nbsp;";
                if($pay_mode_id==3 || $pay_mode_id==5)
				$knit_comp=$company_arr[$row[('dye_comp')]]; 
				else
				$knit_comp=$supllier_arr[$row[('dye_comp')]];
				$process_ids=implode(",",array_unique(explode(",",rtrim($row[('process')],','))));
				$po_ids=implode(",",array_filter(array_unique(explode(",",chop($row['po_id'],',')))));


				$source = $service_wo_arr2[$bookingNo][$pay_mode_id]['source'];
				$supplier_id = $service_wo_arr2[$bookingNo][$pay_mode_id]['supplier_id'];

        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $bookingNo; ?>','<? echo $po_ids; ?>','<? echo $process_ids; ?>','<? echo $row[('company_id')]; ?>','<? echo $source; ?>','<? echo $supplier_id; ?>','<? echo $is_sales;?>','<? echo $row[('entry_form')]?>');"> 
                    <td width="20"><? echo $i; ?></td>
                    <td width="120" align="center"><p><? echo $company_arr[$row[('company_id')]]; ?></p></td>
                    <td width="110" align="center"><p>&nbsp;<? echo $bookingNo; ?></p></td>
                    <td width="100" align="center"><p>&nbsp;<? echo $row[('job_no')]; ?></p></td>
                    <td width="110" align="center"><p>&nbsp;<? echo $buyer_array[$row[('buyer_id')]]; ?></p></td>
                    <td  width="80"  align="center"><? echo change_date_format($row[('booking_date')]); ?></td>
                    <td width="80" align="center"><p><? echo $pay_mode[$pay_mode_id]; ?>&nbsp;</p></td>
                    <td width="" align="center"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                </tr>
				<?
                $i++;
			 }
			}
        	?>
        </table>
    </div>
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
                        <input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id."**".$po_ids; ?>">
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value, 'create_batch_search_list_view', 'search_div', 'grey_fabric_roll_issue_to_subcon_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$comp_data =explode("**",$data[4]);
	$company_id=$comp_data[0];
	$po_ids=$comp_data[1];
	//echo $company_id.'=='.$po_ids;

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.batch_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.batch_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==0)
			$search_field_cond="and a.batch_no like '$search_string'";
		else if($search_by==1)
			$search_field_cond="and a.booking_no like '$search_string'";
		else
			$search_field_cond="and a.color_id in(select id from lib_color where color_name like '$search_string')";
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
	if($po_ids=="") $po_ids_cond="";else $po_ids_cond="and b.po_id in($po_ids)";
	$sql = "select a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=0 and a.batch_for=1 and a.batch_against<>4 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 $search_field_cond $date_cond $po_ids_cond group by  a.id, a.batch_no, a.extention_no, a.batch_date, a.batch_weight, a.booking_no, a.color_id, a.batch_against, a.booking_without_order, a.re_dyeing_from "; 
	//echo $sql;
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

if($action=="subcon_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_issue_no=$data[3];
	$update_id=$data[1];

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
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	
	$dataArray=sql_select("select process_id,receive_date,dyeing_source,dyeing_company,batch_id from  inv_receive_mas_batchroll where id=$update_id");
	
	$job_array=array();
	$job_sql="select a.buyer_name, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
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
                 <td width="110"><strong>Company:</strong> </td>
                 <td width="100">
                   <? echo $company_array[$company]['name'] ;  ?>
                </td>
                <td width="120"><strong>Service Source :</strong></td><td width="185px"><? echo $knitting_source[$dataArray[0][csf('dyeing_source')]]; ?></td>
                <td width="125"><strong>Service Company:</strong></td><td width="175px">
                 <?
                  	if ($dataArray[0][csf('dyeing_source')]==1) echo $company_array[$dataArray[0][csf('dyeing_company')]]['name']; else if ($dataArray[0][csf('dyeing_source')]==3) echo $supplier_arr[$dataArray[0][csf('dyeing_company')]]; 
				 ?>
                </td>
            </tr>
            <tr>
            	<td><strong>Issue Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
                <td><strong>Process:</strong></td><td width="185px"><? echo $conversion_cost_head_array[$dataArray[0][csf('process_id')]]; ?></td>
                <td><strong>Batch No:</strong></td><td width="175px"><? echo $batch_arr[$dataArray[0][csf('batch_id')]]; ?></td>
            </tr>
            <tr>
                <td><strong>Bar Code:</strong></td><td colspan="3" id="barcode_img_id"></td>
            </tr>
        </table>
        <br>
        <table cellspacing="0" width="980"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="50">Roll No </th>
                <th width="90">Barcode No</th>
                 <th width="90">Body Part</th>
                <th width="130">Const./compo</th>
                <th width="50">Gsm</th>
                <th width="50">Dia</th>
                <th width="70">Color</th>
                <th width="50">Dia/width Type</th>
                <th width="50">Wgt.</th>
                <th width="50">Pcs</th>
                <th width="100">Buyer/Job/Order</th>
            </thead>
            <?
			   $bodypart_arr=array();
			   $data_array=sql_select("SELECT  b.body_part_id,c.barcode_no FROM pro_grey_prod_entry_dtls b, pro_roll_details c WHERE  b.id=c.dtls_id  and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0");
			    foreach($data_array as $inf)
				{
					$bodypart_arr[$inf[csf('barcode_no')]]['bodypart']=$inf[csf('body_part_id')];
				}
			
            	$sql = "select  a.prod_id,a.order_id, a.color_id, b.roll_id, b.barcode_no, b.qnty, b.qc_pass_qnty_pcs, b.roll_no from pro_grey_batch_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$update_id and b.entry_form=63 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				//echo $sql;
				$result=sql_select($sql);
				foreach($result as $row)
				{
					
				?>
                	<tr>
                        <td><? echo $i; ?></td>
                        <td><? echo $row[csf('roll_no')]; ?></td>
                        <td style="word-break:break-all;"><? echo $row[csf('barcode_no')]; ?></td>
                        <td style="word-break:break-all;"><? echo $body_part[$bodypart_arr[$row[csf('barcode_no')]]['bodypart']]; ?></td>
                        <td><? echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; ?></td>
                        <td><? echo $product_array[$row[csf("prod_id")]]['gsm']; ?></td>
                        <td style="word-break:break-all;"><? echo $product_array[$row[csf("prod_id")]]['dia_width'] ?></td>
                        <td style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]];?></td>
                        <td style="word-break:break-all;"><? //echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td align="right" style="word-break:break-all;"><? echo $row[csf("qnty")]; ?></td>
                        <td align="right" style="word-break:break-all;"><? echo $row[csf("qc_pass_qnty_pcs")]*1; ?></td>
                        <td style="word-break:break-all;"><? echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer_id']]."<hr/>".$job_array[$row[csf('order_id')]]['job']."<hr/>".$job_array[$row[csf('order_id')]]['po']; ?></td>
                    </tr>
                <?
				}
			?>
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

//for norban
if ($action == "roll_issue_no_of_copy_print") // Print 2, created by Tipu
{
	extract($_REQUEST);
	echo load_html_head_contents("Grey Roll Issue to Process", "../../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$company   			= $data[0];
	$system_no 			= $data[1];
	$report_title 		= $data[2];
	$mst_id     		= $data[3];
	$knit_source    	= $data[4];
	$no_copy 			= $data[5];
	$dyeing_company 	= $data[6];
	$show_report_format = $data[7];

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
	
	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company." AND a.basis = 58 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";
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
	$sqlIssue = "SELECT A.ID, A.DYEING_SOURCE, A.DYEING_COMPANY, A.RECEIVE_DATE, A.RECV_NUMBER, A.WO_NO, A.PROCESS_ID, A.ATTENTION, A.REMARKS, A.COMPANY_ID, B.ID AS DTLS_ID, B.BATCH_ID, B.PROD_ID, B.BODY_PART_ID, B.GSM, B.WIDTH, B.COLOR_ID, B.WIDTH_DIA_TYPE, B.JOB_NO, B.BUYER_ID, B.ORDER_ID, B.BOOKING_ID, B.BOOKING_NO,B.FEBRIC_DESCRIPTION_ID, C.MST_ID, C.BARCODE_NO, C.ID AS ROLL_ID, C.ROLL_NO, C.PO_BREAKDOWN_ID, C.QNTY AS ISSUE_QTY, C.QC_PASS_QNTY_PCS, C.BOOKING_WITHOUT_ORDER
	from inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.id=$mst_id and a.entry_form=63 and c.entry_form=63 AND c.roll_no>0 AND c.status_active=1 AND c.is_deleted=0 AND c.mst_id=$mst_id";
	// echo $sqlIssue;die; 
	$rsltIssue = sql_select($sqlIssue);
	$poBreakdownIdArr = array();
	$barcodeNoArr = array();$batchIdArr = array();$colorIdArr = array();
	$productIdArr = array();$detarminationIdArr = array();
	foreach($rsltIssue as $row)
	{
		$wo_no_arr=explode('-', $row['WO_NO']);
		if ($wo_no_arr[1]!='SBKD' && $wo_no_arr[1]!='SMN') 
		{
			$poBreakdownIdArr[$row['PO_BREAKDOWN_ID']] = $row['PO_BREAKDOWN_ID'];
		}
		
		$barcodeNoArr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
		$productIdArr[$row['PROD_ID']] = $row['PROD_ID'];

		$challan_number = $row['RECV_NUMBER'];
		$issue_date = $row['RECEIVE_DATE'];
		$knit_dye_source = $row['DYEING_SOURCE'];
		$attention = $row['ATTENTION'];
		$remarks = $row['REMARKS'];
		$process=$conversion_cost_head_array[$row['PROCESS_ID']];
		//for issue to
		$knit_dye_company = '';
		if ($row['DYEING_SOURCE'] == 1)
			$knit_dye_company = $company_library[$row['DYEING_COMPANY']];
		else
			$knit_dye_company = $supplier_dtls_arr[$row['DYEING_COMPANY']];
		if($detarminationIdArr[$row['FEBRIC_DESCRIPTION_ID']]!="")
		{
			$detarminationIdArr[$row['FEBRIC_DESCRIPTION_ID']]=$row['FEBRIC_DESCRIPTION_ID'];
		}
		$batchIdArr[$row['BATCH_ID']] = $row['BATCH_ID'];
		$colorIdArr[$row['COLOR_ID']] = $row['COLOR_ID'];
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
	$product_sql = sql_select("SELECT id AS ID, detarmination_id AS DETARMINATION_ID, gsm AS GSM, dia_width AS DIA_WIDTH, unit_of_measure AS UNIT_OF_MEASURE FROM product_details_master WHERE item_category_id=13 ".where_con_using_array($productIdArr, '0', 'id'));
	foreach($product_sql as $row)
	{
		$detarminationIdArr[$row['DETARMINATION_ID']]=$row['DETARMINATION_ID'];

		$product_array[$row[csf("id")]]['gsm']=$row["GSM"];
		$product_array[$row[csf("id")]]['dia_width']=$row["DIA_WIDTH"];
		$product_array[$row[csf("id")]]['deter_id']=$row["DETARMINATION_ID"];
		$product_array[$row[csf("id")]]['uom']=$row["UNIT_OF_MEASURE"];
	}
	//echo "<pre>"; print_r($product_array);

	// for body part
	$bodypart_arr=array();
   	$data_array=sql_select("SELECT  B.BODY_PART_ID, C.BARCODE_NO FROM pro_grey_prod_entry_dtls b, pro_roll_details c WHERE  b.id=c.dtls_id  and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in ($barcode_nums)");
    foreach($data_array as $inf)
	{
		$bodypart_arr[$inf['BARCODE_NO']]['bodypart']=$inf['BODY_PART_ID'];
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
	$rptDataArr = array(); $rptSummaryDataArr=array();
	foreach($rsltIssue as $row)
	{
		//$composition = $row['FEBRIC_DESCRIPTION_ID'];
		$composition = $product_array[$row["PROD_ID"]]['deter_id'];
		$gsm = $product_array[$row["PROD_ID"]]['gsm'];
		$dia = $product_array[$row["PROD_ID"]]['dia_width'];
		$body_part_id=$bodypart_arr[$row['BARCODE_NO']]['bodypart'];
		//$gsm = $row['GSM'];
		//$dia = $row['WIDTH'];
		$batch_id = $row['BATCH_ID'];
		// $body_part_id=$row['BODY_PART_ID'];
		$job_no = $poNoArr[$row['PO_BREAKDOWN_ID']]['po_number'];
		// $booking_no = $row['BOOKING_NO'];
		$color_id = $row['COLOR_ID'];

		$wo_no_arr=explode('-', $row['WO_NO']);
		if ($wo_no_arr[1]=='SBKD') 
		{
			$booking_no = $row['BOOKING_NO'];
		}
		else
		{
			$booking_no = $batch_arr[$row['BATCH_ID']]['booking_no'];
		}
		
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
		

		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['buyer_id'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['buyer_name'];
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['job_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['job_no'];
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['style_ref_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['style_ref_no'];
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['body_part_id'] = $body_part_id; //$row['BODY_PART_ID'];
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['color_id'] = $row['COLOR_ID'];
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['gsm'] = $gsm;
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['dia'] = $dia;
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['width_dia_type'] = $row['WIDTH_DIA_TYPE'];
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['roll_no'] = $row['ROLL_NO'];
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['num_of_roll'] += count($row['BARCODE_NO']);
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['issue_qnty'] += $row['ISSUE_QTY'];
		$rptDataArr[$booking_no][$batch_id][$composition][$row['BARCODE_NO']]['issue_qty_pcs'] += $production_roll_array[$row['BARCODE_NO']]['issue_qty_pcs'];

		// fro Summary
		$rptSummaryDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_id][$gsm][$dia]['buyer_id'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['buyer_name'];
		$rptSummaryDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_id][$gsm][$dia]['job_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['job_no'];
		$rptSummaryDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_id][$gsm][$dia]['style_ref_no'] = $poNoArr[$row['PO_BREAKDOWN_ID']]['style_ref_no'];
		$rptSummaryDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_id][$gsm][$dia]['color_id'] = $color_id;
		$rptSummaryDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_id][$gsm][$dia]['num_of_roll'] += count($row['BARCODE_NO']);
		$rptSummaryDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_id][$gsm][$dia]['issue_qnty'] += $row['ISSUE_QTY'];
		$rptSummaryDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_id][$gsm][$dia]['width_dia_type'] = $row['WIDTH_DIA_TYPE'];
		$rptSummaryDataArr[$booking_no][$batch_id][$composition][$body_part_id][$color_id][$gsm][$dia]['issue_qty_pcs'] += $production_roll_array[$row['BARCODE_NO']]['issue_qty_pcs'];
	}
	// echo "<pre>"; print_r($rptDataArr);die;

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
					<td width="110" align="right"><?php echo $noOfCopy.($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong>Roll Issue to Finishing Process Challan</strong>
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
							<td width="125"><strong>Company:</strong></td>
							<td width="250px"><? echo $company_library[$company]; ?></td>
							<td width="125"><strong>Process:</strong></td>
							<td width="150px"><? echo $process; ?></td>
							<td width="130"><strong>Challan No:</strong></td>
							<td width="130"><? echo $challan_number; ?></td>					
						</tr>
						<tr>
							<td><strong>Service Company:</strong></td>
							<td><? echo $knit_dye_company; ?></td>
							<td><strong>Attention:</strong></td>
							<td><? echo $attention; //$location; ?></td>
							<td><strong>Challan Date:</strong></td>
							<td><? echo change_date_format($issue_date); ?></td>                
						</tr>
						<tr>
							<td><strong>Service Source:</strong></td>
							<td><? echo $knitting_source[$knit_dye_source];?></td>
							<td><strong>Remarks:</strong></td>
							<td colspan="3"><? echo $remarks; ?></td>
						</tr>
						<tr>
							<td align="center" colspan="6" id="barcode_img_id_<?php echo $x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
						</tr>
					</table>
					<?
                    if ($show_report_format==0) // barcode wise start
                    {
                    	?>
						<table style="margin-right:-40px;" cellspacing="0" width="1160" border="1" rules="all" class="rpt_table">
							<thead bgcolor="#dddddd">
								<tr>
									<th rowspan="2" width="20">SL</th>
									<th rowspan="2" width="120">Buyer, Job, <br>Style and<br>Booking</th>
									<th rowspan="2" width="120">Batch Number</th>
									<th rowspan="2" width="210">Const./compo</th>
									<th rowspan="2" width="100">Body Part</th>
									<th rowspan="2" width="65">Color</th>
									<th rowspan="2" width="60">GSM</th>
									<th rowspan="2" width="60">Dia</th>
									<th rowspan="2" width="100">Dia/width Type</th>
									<th rowspan="2" width="100">Barcode No</th>
									<th rowspan="2" width="80">Roll No.</th>
									<th colspan="2" width="120">Delivery Qty</th>
								</tr>
								<tr>
									<th width="60">KG</th>
									<th width="60">PCS</th>
								</tr>
							</thead>
	                        <tbody>
								<?
								$i=1;$k=0;	
								$grand_tot_qty_fabric=$grand_tot_issue_qty_pcs=$grand_tot_num_of_roll=0;	
								ksort($rptDataArr);					
								foreach($rptDataArr as $booking=>$bookingArr)
								{
									$job_tot_qty_fabric=$job_tot_issue_qty_pcs=$job_tot_num_of_roll=0;
									foreach($bookingArr as $batchId=>$batchArr)
									{
										$batch_tot_qty_fabric=$batch_tot_issue_qty_pcs=$batch_tot_num_of_roll=0;
										foreach($batchArr as $compositionId=>$compositionArr)
										{
											$fab_tot_issue_qnty=$fab_tot_issue_qty_pcs=$fab_tot_num_of_roll=0;
											foreach($compositionArr as $barCode=>$row)
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
												$sustainabilityData = ($sustainability) ? ' :: '.$sustainability : '' ;
												$materialData = ($material) ? ' :: '.$material : '' ;

												if($knit_dye_source==3)
												{
													$buyerName="WHS";
													$jobNO=$row['job_no'];
													$styleName="WHS";
													$bookingAndOters=$booking.$sustainabilityData.$materialData;
												}
												else
												{
													$buyerName=$buyer_array[$buyer];
													$jobNO=$row['job_no'];
													$styleName=$style;
													$bookingAndOters=$booking.$sustainabilityData.$materialData;
												}
												?>
					                            <tr bgcolor="<? echo $bgcolor; ?>">
					                                <td style="font-size: 15px"><? echo $i; ?></td>
					                                <td style="font-size: 15px">
					                                    <div style="word-wrap:break-word; width:130px"><? 
					                                    echo $buyerName.' ::<br/> '.$jobNO.' ::<br/> '.$styleName.' ::<br/> '.$bookingAndOters; ?>
					                                    </div>
					                                </td>
					                                <td style="font-size: 15px">
					                                    <div style="word-wrap:break-word; width:130px"><? 
					                                    echo $batch_arr[$batchId]['batch_no']; ?>
					                                    </div>
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
					                                    <div style="word-wrap:break-word; width:100px;  text-align: center;"><? echo $body_part[$row['body_part_id']]; ?></div>
					                                </td>
					                                
					                                <td style="font-size: 15px">
					                                    <div style="word-wrap:break-word; width:65px;  text-align: center;"><? echo $color_arr[$row['color_id']]; ?></div>
					                                </td>
					                                
					                                <td style="font-size: 15px; text-align: center;">
					                                    <div style="word-wrap:break-word; width:60px">
					                                        <? echo $row['gsm']; ?>
					                                    </div>
					                                </td>
					                                <td style="font-size: 15px; text-align: center;">
					                                    <div style="word-wrap:break-word; width:60px">
					                                        <? echo $row['dia']; ?>
					                                    </div>
					                                </td>
					                                <td style="font-size: 15px; text-align: center;">
					                                    <div style="word-wrap:break-word; width:100px"><? echo $fabric_typee[$row['width_dia_type']]; ?></div>
					                                </td>
					                                <td style="font-size: 15px">
					                                    <div style="word-wrap:break-word; width:100px;  text-align: center;">
					                                        <? echo $barCode; ?>
					                                    </div>
					                                </td>
					                                <td style="font-size: 15px" align="right"><? echo $row['roll_no']; ?></td>
					                                
					                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? echo number_format($row['issue_qnty'], 2, '.', ''); ?></td>
					                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? 
					                                	if ($row['issue_qty_pcs']=="") 
					                                	{echo 0;} 
					                                	else{echo $row['issue_qty_pcs'];} ?>
					                                </td>
					                            </tr>
												<?
												$i++;
												$fab_tot_issue_qnty+=$row['issue_qnty'];
												$fab_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
												$fab_tot_num_of_roll+=$row['num_of_roll'];

												$batch_tot_qty_fabric+=$row['issue_qnty'];
												$batch_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
												$batch_tot_num_of_roll+=$row['num_of_roll'];

												$job_tot_qty_fabric+=$row['issue_qnty'];
												$job_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
												$job_tot_num_of_roll+=$row['num_of_roll'];

												$grand_tot_qty_fabric+=$row['issue_qnty'];
												$grand_tot_issue_qty_pcs+=$row['issue_qty_pcs'];
												$grand_tot_num_of_roll+=$row['num_of_roll'];
											}
											?>
											<tr class="tbl_bottom">
												<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Fabric Type Total</strong></td>
												<td align="right" style="font-size: 14px;"><? echo number_format($fab_tot_num_of_roll, 2, '.', ''); ?></td>
												<td align="right" style="font-size: 14px;">
													<b><? echo number_format($fab_tot_issue_qnty, 2, '.', ''); ?></b>
												</td>
												<td align="right" style="font-size: 14px;"><? echo $fab_tot_issue_qty_pcs; ?></td>
											</tr>
											<?
										}
										?>
										<tr class="tbl_bottom">
											<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Batch Total</strong></td>
											<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($batch_tot_num_of_roll,2); ?></td>
											<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $batch_tot_qty_fabric; ?></td>
											<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $batch_tot_issue_qty_pcs; ?></td>
										</tr>
										<?
									}
									$job_total=$k++;
									?>
									<tr class="tbl_bottom">
										<td colspan="10" style=" text-align:right;font-size: 14px;"><strong>Job Total</strong></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($job_tot_num_of_roll,2); ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $job_tot_qty_fabric; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $job_tot_issue_qty_pcs; ?></td>
									</tr>
									<?
								}
								?>
								<tr class="tbl_bottom">
									<td style="font-size: 16px;" colspan="2" align="center"><b>Total Job: 
		                            <?php echo $job_total+1; ?></b></td>
									<td align="right" style="font-size: 16px;" colspan="8"><strong>Grand Total</strong></td>
									<td align="right" style="font-size: 16px;"><strong><? echo number_format($grand_tot_num_of_roll, 2, '.', ''); ?></strong></td>
									<td align="center" style="font-weight: bold; font-size: 16px;"><? echo number_format($grand_tot_qty_fabric, 2, '.', ''); ?></td>
									<td align="right" style="font-size: 16px;"><strong><? echo $grand_tot_issue_qty_pcs; ?></strong></td>
								</tr>
		                    </tbody>	
	                    </table>
	                    <?
                	} // barcode wise End
                    else // summary Start
                    {
                    	?>
						<table style="margin-right:-40px;" cellspacing="0" width="1160" border="1" rules="all" class="rpt_table">
							<thead bgcolor="#dddddd">
								<tr>
									<th rowspan="2" width="20">SL</th>
									<th rowspan="2" width="120">Buyer, Job, <br>Style and<br>Booking</th>
									<th rowspan="2" width="120">Batch Number</th>
									<th rowspan="2" width="250">Const./compo</th>
									<th rowspan="2" width="100">Body Part</th>
									<th rowspan="2" width="65">Color</th>
									<th rowspan="2" width="60">GSM</th>
									<th rowspan="2" width="60">Dia</th>
									<th rowspan="2" width="100">Dia/width Type</th>
									<th colspan="2" width="160">Delivery Qty</th>
									<th rowspan="2" >Total Roll</th>
								</tr>
								<tr>
									<th width="80">KG</th>
									<th width="80">PCS</th>
								</tr>
							</thead>
	                        <tbody>
								<?
								$i=1;$k=0;	
								$grand_tot_qty_fabric=$grand_tot_issue_qty_pcs=$grand_tot_num_of_roll=$grand_tot_reject_qnty=0;	
								ksort($rptSummaryDataArr);					
								foreach($rptSummaryDataArr as $booking=>$bookingArr)
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
															if($knit_dye_source==3)
															{
																$buyerName="WHS";
																$jobNO=$row['job_no'];
																$styleName="WHS";
																$bookingAndOters=$booking.' ::<br>'.$sustainability.' ::'.$material;

															}
															else
															{
																$buyerName=$buyer_array[$buyer];
																$jobNO=$row['job_no'];
																$styleName=$style;
																$bookingAndOters=$booking.' ::<br>'.$sustainability.' ::'.$material;
															}
															?>
								                            <tr bgcolor="<? echo $bgcolor; ?>">
								                                <td style="font-size: 15px"><? echo $i; ?></td>
								                                <td style="font-size: 15px">
								                                    <div style="word-wrap:break-word; width:130px"><? 
								                                    echo $buyerName.' ::<br>'.$jobNO.' ::<br>'.$styleName.' ::<br>'.$bookingAndOters; ?>
								                                    </div>
								                                </td>
								                                <td style="font-size: 15px">
								                                    <div style="word-wrap:break-word; width:130px"><? 
								                                    echo $batch_arr[$batchId]['batch_no']; ?>
								                                    </div>
								                                </td>
								                                <td style="font-size: 15px" title="<? echo $row['febric_description_id']; ?>">
								                                    <div style="word-wrap:break-word; width:250px">
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
								                                    <div style="word-wrap:break-word; width:100px; text-align: center;"><? echo $body_part[$body_part_ids]; ?></div>
								                                </td>
								                                
								                                <td style="font-size: 15px">
								                                    <div style="word-wrap:break-word; width:65px; text-align: center;"><? echo $color_arr[$row['color_id']]; ?></div>
								                                </td>
								                                
								                                <td style="font-size: 15px; text-align: center;">
								                                    <div style="word-wrap:break-word; width:60px; text-align: center;">
								                                        <? echo $gsm; ?>
								                                    </div>
								                                </td>
								                                <td style="font-size: 15px; text-align: center;">
								                                    <div style="word-wrap:break-word; width:60px; text-align: center;">
								                                        <? echo $dia; ?>
								                                    </div>
								                                </td>
								                                <td style="font-size: 15px; text-align: center;">
								                                    <div style="word-wrap:break-word; width:100px"><? echo $fabric_typee[$row['width_dia_type']]; ?></div>
								                                </td>
								                                
								                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? echo number_format($row['issue_qnty'], 2, '.', ''); ?></td>
								                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? 
								                                	if ($row['issue_qty_pcs']=="") 
								                                	{echo 0;} 
								                                	else{echo $row['issue_qty_pcs'];} ?>		
								                                </td>
								                                <td style="font-size: 15px" align="right"><? echo $row['num_of_roll']; ?></td>
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
												<td colspan="9" style=" text-align:right;font-size: 14px;"><strong>Fabric Type Total</strong></td>
												<td align="right" style="font-size: 14px;">
													<b><? echo number_format($fab_tot_issue_qnty, 2, '.', ''); ?></b>
												</td>
												<td align="right" style="font-size: 14px;"><? echo $fab_tot_issue_qty_pcs; ?></td>
												<td align="right" style="font-size: 14px;"><? echo number_format($fab_tot_num_of_roll, 2, '.', ''); ?></td>
											</tr>
											<?
										}
										?>
										<tr class="tbl_bottom">
											<td colspan="9" style=" text-align:right;font-size: 14px;"><strong>Batch Total</strong></td>
											<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $batch_tot_qty_fabric; ?></td>
											<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $batch_tot_issue_qty_pcs; ?></td>
											<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($batch_tot_num_of_roll,2); ?></td>
										</tr>
										<?
									}
									$job_total=$k++;
									?>
									<tr class="tbl_bottom">
										<td colspan="9" style=" text-align:right;font-size: 14px;"><strong>Job Total</strong></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $job_tot_qty_fabric; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo $job_tot_issue_qty_pcs; ?></td>
										<td align="right" style="font-weight: bold;font-size: 14px;"><? echo number_format($job_tot_num_of_roll,2); ?></td>
									</tr>
									<?
								}
								?>
								<tr class="tbl_bottom">
									<td style="font-size: 16px;" colspan="2" align="center"><b>Total Job: 
		                            <?php echo $job_total+1; ?></b></td>
									<td align="right" style="font-size: 16px;" colspan="7"><strong>Grand Total</strong></td>
									<td align="center" style="font-weight: bold; font-size: 16px;"><? echo number_format($grand_tot_qty_fabric, 2, '.', ''); ?></td>
									<td align="right" style="font-size: 16px;"><strong><? echo $grand_tot_issue_qty_pcs; ?></strong></td>
									<td align="right" style="font-size: 16px;"><strong><? echo number_format($grand_tot_num_of_roll, 2, '.', ''); ?></strong></td>
								</tr>
		                    </tbody>	
	                    </table>
                    	<?
                	} // summary Start
                    ?>
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
					<table style="margin-right:-40px;" cellspacing="0" width="1160" border="1" rules="all" class="rpt_table">
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
                        	<td align="center"><?php echo $grand_tot_num_of_roll; ?></td>
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
				<? echo signature_table(329, $company, "1200px"); ?>
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

//all function
//batch
function get_batchFor_GreyRollIssueToProcess($barCode)
{
	$data=array();
	$sqlBatch=sql_select("SELECT a.id, a.batch_no, a.color_id, a.booking_no_id, a.booking_without_order, b.po_id, b.barcode_no  
	FROM pro_batch_create_mst a 
	INNER JOIN pro_batch_create_dtls b ON a.id = b.mst_id
	WHERE a.status_active=1 
	AND a.is_deleted = 0 
	AND b.barcode_no IN(".implode(",",$barCode).")");

	foreach($sqlBatch as $row)
	{
		if($row[csf('booking_without_order')] != 1)
		{
			$data[$row[csf('barcode_no')]][$row[csf('po_id')]]['batch_id']=$row[csf("id")];
			$data[$row[csf('barcode_no')]][$row[csf('po_id')]]['batch_no']=$row[csf("batch_no")];
		}
		else
		{
			$data[$row[csf('barcode_no')]][$row[csf('booking_no_id')]]['batch_id']=$row[csf("id")];
			$data[$row[csf('barcode_no')]][$row[csf('booking_no_id')]]['batch_no']=$row[csf("batch_no")];
		}
	}
	
	return $data;
}

//Yarn Count Determin
function get_constructionComposition($yarnCountDeterminId)
{
	$i = 0;
	$id = '';
	$data = array();
	$construction = '';
	$composition_name = '';
	$sqlYarnCount = sql_select("SELECT a.id, a.construction, b.percent, c.composition_name 
	FROM lib_yarn_count_determina_mst a 
	INNER JOIN lib_yarn_count_determina_dtls b ON a.id = b.mst_id
	INNER JOIN lib_composition_array c ON b.copmposition_id = c.id 
	WHERE a.id IN(".implode(",",$yarnCountDeterminId).")");
	foreach( $sqlYarnCount as $row )
	{
		$id=$row[csf('id')];
		if($i==0)
		{
			$construction.= $row[csf('construction')].", ";
			$i++;
		}
		
		if($composition_name != '')
		{
			$composition_name .= ', ';
		}
		$composition_name .= $row[csf('composition_name')]." ".$row[csf('percent')]."%";
	}
	$data[$id] = $construction.$composition_name;
	return $data;
}

//buyer
function get_buyerFor_GreyRollIssueToProcess($poBreakdownId)
{
	global $buyer_name_array;
	$data=array();
	$sqlPo=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id 
	FROM wo_po_details_master a 
	INNER JOIN wo_po_break_down b ON a.id = b.job_id
	WHERE b.id IN(".implode(",",$poBreakdownId).")");
	foreach($sqlPo as $row)
	{
		$data[$row[csf('id')]]['job_no']=$row[csf('job_no')];
		$data[$row[csf('id')]]['buyer_name']=$buyer_name_array[$row[csf('buyer_name')]];
		$data[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$data[$row[csf('id')]]['year']=date('Y',strtotime($row[csf('insert_date')]));
		$data[$row[csf('id')]]['po_number']=$row[csf('po_number')];
	}
	return $data;
}

function get_nonOrderBookingBuyerFor_GreyRollIssueToProcess($bookingId)
{
	global $buyer_name_array;
	$data=array();
	$sqlBooking=sql_select("SELECT a.id, a.buyer_id FROM wo_non_ord_samp_booking_mst a WHERE a.id IN(".implode(",",$bookingId).")");
	foreach($sqlBooking as $row)
	{
		$data[$row[csf('id')]]['buyer_name']=$buyer_name_array[$row[csf('buyer_id')]];
	}
	return $data;
}

function get_color_details($colorId)
{
	global $color_arr;
	$colorName='';
	$expColorId=explode(",",$colorId);
	foreach($expColorId as $id)
	{
		if($id>0)
			$colorName.=$color_arr[$id].",";
	}
	$colorName=chop($colorName,',');
	return $colorName;
}

//knitting_company
function get_knitting_company_details($knittingSource, $knittingCompany)
{ 
	global $company_name_array;
	global $supplier_arr;
	$data='';
	if($knittingSource == 1)
	{
		$data=$company_name_array[$knittingCompany];
	}
	else if($knittingSource ==3 )
	{
		$data=$supplier_arr[$knittingCompany];
	}
	return $data;
}

//receive_basis
function get_receive_basis($entryForm, $receiveBasis)
{
	$data=array();
	if(($entryForm==2 && $receiveBasis==0) || ($entryForm==22 && ($receiveBasis==4 || $receiveBasis==6)))
	{
		$data['id']=0;
		$data['dtls']='Independent';
	}
	else if(($entryForm==2 && $receiveBasis==1) || ($entryForm==22 && $receiveBasis==2)) 
	{
		$data['id']=2;
		$data['dtls']="Booking";
	}
	else if($entryForm==2 && $receiveBasis==2) 
	{
		$data['id']=3;
		$data['dtls']="Knitting Plan";
	}
	else if($entryForm==22 && $receiveBasis==1) 
	{
		$data['id']=1;
		$data['dtls']="PI";
	}
	return $data;
}

//dia type
function get_dia_type($bookingId)
{
	$sqlDiaType="SELECT id, width_dia_type 
		FROM ppl_planning_info_entry_dtls 
		WHERE id IN(".implode(",",$bookingId).")";
	$resultdiaType=sql_select($sqlDiaType);
	$data_diaType = array();
	foreach($resultdiaType as $row)
	{
		$data_diaType[$row[csf('id')]]=$row[csf('width_dia_type')];
	}
	return $data_diaType;
}

if($action=="populate_barcode_fab_fso_service_booking_wo")
{
	$data = explode("__",$data);


	//cbo_company_id+"__"+$("#txt_wo_no").val()+"__"+$("#cbo_service_source").val()+"__"+$("#cbo_service_company").val()+"__"+newBarcode+"__"+cbo_process


	$cbo_company_id = $data[0];
	$txt_wo_no=$data[1];
	$cbo_service_source=$data[2];
	$cbo_service_company=$data[3];
	$cbo_process=$data[4];

	$is_sales=1;

	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	
	//N.B. Issue to process Source for Heat Settings [1=>grey roll issue (default), 2=> roll receive for batch]
	$variable_set_source_arr =  sql_select("select distribute_qnty from variable_settings_production where variable_list=85 and company_name=$cbo_company_id and status_active=1 and is_deleted=0 order by id desc");
	$variable_set_source = $variable_set_source_arr[0][csf("distribute_qnty")];

	if($variable_set_source==2)
	{
		$variable_set_source=2; // roll receive by batch
	}
	else{
		$variable_set_source=1; //default grey roll issue
	}

	$wo_fso_sql =sql_select("SELECT a.id, a.do_no, a.wo_basis, b.issue_no, b.issue_id, b.fso_id, a.dyeing_source, a.dyeing_compnay_id
	from dyeing_work_order_mst a, dyeing_work_order_dtls b
	where a.id=b.mst_id and a.entry_form=696 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and a.do_no='$txt_wo_no'
	order by a.id, b.id");

	foreach ($wo_fso_sql as $val) {
		if($val[csf("wo_basis")]==1)
		{
			$issue_id_arr[$val[csf("issue_id")]]=$val[csf("issue_id")];
			$issue_no_arr[$val[csf("issue_no")]]= "'".$val[csf("issue_no")]."'";
		}
		else
		{
			$fso_id_arr[$val[csf("fso_id")]]=$val[csf("fso_id")];
		}

		$dyeing_source=$val[csf("dyeing_source")];
		$dyeing_compnay_id=$val[csf("dyeing_compnay_id")];
	}
	//a.dyeing_source, a.dyeing_compnay_id

	if(!empty($issue_id_arr))
	{
		$issue_id_cond = " and g.id in (".implode(",",$issue_id_arr).")";
		$issue_no_cond = " and g.challan_no in (".implode(",",$issue_no_arr).")";
	}

	if(!empty($fso_id_arr))
	{
		$fso_cond = " and d.id in (".implode(",",$fso_id_arr).")";
	}


	if($cbo_process==33 && $variable_set_source==2)
	{
		$sql="SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, null as job_no_mst, d.job_no as po_number, d.within_group, d.buyer_id, d.po_buyer, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, b.is_sales 
		from inv_receive_mas_batchroll g, pro_grey_batch_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d 
		where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=62 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.is_sales=1 $issue_no_cond $fso_cond 
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, d.job_no, d.within_group, d.buyer_id, d.po_buyer, b.qc_pass_qnty_pcs, b.is_sales";
	}
	else{
		$sql="SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, null as job_no_mst, d.job_no as po_number, d.within_group, d.buyer_id, d.po_buyer, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, b.is_sales 
		from inv_issue_master g, inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d 
		where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.is_sales=1 $issue_id_cond $fso_cond 
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, d.job_no, d.within_group, d.buyer_id, d.po_buyer, b.qc_pass_qnty_pcs, b.is_sales";
	}
	
	
	//and g.knit_dye_source=$dyeing_source and g.knit_dye_company=$dyeing_compnay_id
	
	//echo $sql;die;
	$data_array=sql_select($sql);
	
	
	if(empty($data_array))
	{
		$rtnData='0__';
		echo $rtnData.$sql;
		die;
	}
	
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$nonOrderbookingId=array();
	$prodIds="";
	$jobNos="";
	$bookingIds="";
	foreach($data_array as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if($row[csf('booking_without_order')] != 1)
		{
			$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		else
		{
			$bookingIds.=$row[csf('po_breakdown_id')].",";
			$nonOrderbookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		$bookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];


		$prodIds.=$row[csf('prod_id')].",";
		$jobNos.="'".$row[csf('job_no_mst')]."',";
	}
	$prodIds=chop($prodIds,",");
	$jobNos=chop($jobNos,",");
	$bookingIds=chop($bookingIds,",");


	$barCode = array_filter($barCode);
	if (!empty($barCode)) 
	{
		if (count($barCode) > 0) {
			$barCode_NOs = implode(",", $barCode);
			$all_barcode_no_cond = "";
			$barCond = "";
			if ($db_type == 2 && count($barCode) > 999) {
				$barCode_chunk = array_chunk($barCode, 999);
				foreach ($barCode_chunk as $chunk_arr) {
					$chunk_arr_value = implode(",", $chunk_arr);
					$barCond .= " a.barcode_no in($chunk_arr_value) or ";
				}

				$all_barcode_no_cond .= " and (" . chop($barCond, 'or ') . ")";
			} else {
				$all_barcode_no_cond = " and a.barcode_no in($barCode_NOs)";
			}
		}
	}

	/*if(!empty($barCode))
	{
		$rcv_by_batch_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form=62 and b.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
		foreach ($rcv_by_batch_arr as $row) {
			$next_process_barcode[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
			$next_process_entry['entry'] ="Receive for batch entry";
			$next_process_entry['system_no'] =$row[csf("recv_number")];
		}
	}
	unset($rcv_by_batch_arr);
	 if(!empty($rcv_by_batch_arr))
	{
		
		$rtnData="1__Receive for batch entry found.\nBarcode no: ".$rcv_by_batch_arr[0][csf("barcode_no")]."\nReceive for Batch No. :".$rcv_by_batch_arr[0][csf("recv_number")];;
		echo $rtnData;
		die;
	} */

	if(!empty($barCode))
	{
		$roll_issue_to_process_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form=63 and b.entry_form=63 and a.is_rcv_done=0 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 $all_barcode_no_cond");
		//and a.barcode_no in (".implode(',',$barCode).")

		foreach ($roll_issue_to_process_arr as $row) {
			$next_process_barcode[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
			$next_process_entry['entry'] ="Grey Fab. issue to process";
			$next_process_entry['system_no'] =$row[csf("recv_number")];
		}
	}
	unset($roll_issue_to_process_arr);
	/* if(!empty($roll_issue_to_process_arr))
	{
		//$roll_i
		$rtnData="1__Grey Fab. issue to process found.\nBarcode no: ".$roll_issue_to_process_arr[0][csf("barcode_no")]."\nIssue to process No. :".$roll_issue_to_process_arr[0][csf("recv_number")];
		echo $rtnData;
		die;
	} */

	if(!empty($barCode))
	{
		$roll_production_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and a.entry_form=66 and b.entry_form=66 and a.status_active=1 and a.is_deleted=0 $all_barcode_no_cond ");
		//and a.barcode_no in (".implode(',',$barCode).")

		foreach ($roll_production_arr as $row) {
			$next_process_barcode[$row[csf("barcode_no")]] =$row[csf("barcode_no")];
			$next_process_entry['entry'] ="Finish fabric roll wise production";
			$next_process_entry['system_no'] =$row[csf("recv_number")];
		}
	}
	unset($roll_production_arr);


	if(!empty($barCode))
	{
		$dyeing_arr = sql_select("SELECT a.barcode_no, b.color_id, b.batch_no, b.id as batch_id from pro_roll_details a, pro_batch_create_mst b, pro_fab_subprocess c where a.mst_id=b.id and b.id=c.batch_id and a.entry_form=64 and c.entry_form=35 and c.load_unload_id=2 and c.result=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $all_barcode_no_cond");
		foreach ($dyeing_arr as $row) 
		{
			$dyeing_batch_info[$row[csf("barcode_no")]]['color_id'] =$row[csf("color_id")];
			$dyeing_batch_info[$row[csf("barcode_no")]]['batch_no'] =$row[csf("batch_no")];
			$dyeing_batch_info[$row[csf("barcode_no")]]['batch_id'] =$row[csf("batch_id")];
		}
	}
	
 	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
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
	unset($deter_array);
	//print_r($prod_arr);


	$i = 0;
	$rtnData='';
	foreach($data_array as $row)
	{
		if($next_process_barcode[$row[csf("barcode_no")]]=="")
		{
			if($cbo_process==33 || ($cbo_process!= 33 && $dyeing_batch_info[$row[csf("barcode_no")]]['batch_no'] !="") )
			{
				$i++;

				if($row[csf('within_group')] ==1)
				{
					$row['buyer'] = $buyer_name_array[$row[csf('po_buyer')]];
					$row['buyer_id']=$row[csf('po_buyer')];
				}
				else{
					$row['buyer'] = $buyer_name_array[$row[csf('buyer_id')]];
					$row['buyer_id']=$row[csf('buyer_id')];
				}

				$row['order_no']=$row[csf('po_number')];
				$row['order_id']=$row[csf('po_breakdown_id')];
				
				$rollWeight=number_format($row[csf('qnty')],2);
				$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
					
				$row['service_company_name']=$company_name_array[$row[csf('service_company')]];
				
				$row['dia_type']=$fabric_typee[$row[csf('width_dia_type')]];
				//$row['color']=$color_arr[$row[csf('color_id')]];
				//$row['color_id']=$row[csf('color_id')];
				$row['construction']=$composition_arr[$row[csf('febric_description_id')]];
				$row['job_no']=$row[csf('job_no_mst')];
				//$row['batch_id']=$row[csf('batch_id')];
				//$row['batch_no']=$row[csf('batch_no')];
				$row['deter_d']=$row[csf("febric_description_id")];

				if($dyeing_batch_info[$row[csf("barcode_no")]]['batch_no']!="")
				{
					//Replace Batch and Color for other processes (without heat setting)
					$row['batch_id']=$dyeing_batch_info[$row[csf("barcode_no")]]['batch_id'];
					$row['batch_no']=$dyeing_batch_info[$row[csf("barcode_no")]]['batch_no'];
					$row['color_id']=$dyeing_batch_info[$row[csf("barcode_no")]]['color_id'];
					$row['color']=$color_arr[$dyeing_batch_info[$row[csf("barcode_no")]]['color_id']];
				}
				else
				{
					//echo $row[csf("barcode_no")];die;
					$row['batch_id']=$row[csf('batch_id')];
					$row['batch_no']=$row[csf('batch_no')];
					$row['color_id']=$row[csf('color_id')];
					$row['color']=$color_arr[$row[csf('color_id')]];
				}



				//receive_basis
				$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
				$receive_basis_id=$receiveBasisArray['id'];
				$receive_basis_dtls=$receiveBasisArray['dtls'];
				
				$row['mst_id'] = 0;
				$row['dtls_id'] = 0;
				
				$rtnData .=	$row[csf('barcode_no')]."**".
							$row[csf('roll_no')]."**".
							$row['batch_no']."**".
							$row[csf('prod_id')]."**".
							$body_part[$row[csf('body_part_id')]]."**".
							$row['construction']."**".
							$row[csf('gsm')]."**".
							$row[csf('width')]."**".
							$row['color']."**".
							$row['dia_type']."**".
							$rollWeight."**".
							$qtyInPcs."**".
							$row['buyer']."**".
							$row['job_no']."**".
							$row['order_no']."**".
							$row['service_company_name']."**".
							$receive_basis_dtls."**".
							$row[csf('booking_no')]."**".
							$row['mst_id']."**".
							$row['dtls_id']."**".
							$row['color_id']."**".
							$row[csf('company_id')]."**".
							$receive_basis_id."**".
							$row['order_id']."**".
							$row['batch_id']."**".
							$row[csf('booking_id')]."**".
							$row[csf('body_part_id')]."**".
							$row[csf('width_dia_type')]."**".
							$row[csf('service_company')]."**".
							$row[csf('booking_without_order')]."**".
							$row['deter_d']."**".
							$row['buyer_id']."**".
							$row[csf('roll_id')]."**".
							$row[csf('is_sales')]."__";
						?>
							<tr id="tr_<? echo $i;?>" align="center" valign="middle">
							<td width="30" id="sl_<? echo $i;?>"><? echo $i;?></td>
							<td width="80" id="barcode_<? echo $i;?>"><? echo $row[csf('barcode_no')];?></td>
							<td width="50" id="roll_<? echo $i;?>"><? echo $row[csf('roll_no')];?></td>
							<td width="70" id="batchNo_<? echo $i;?>"><? echo $row['batch_no'];?></td>
							<td width="60" id="prodId_<? echo $i;?>"><? echo $row[csf('prod_id')];?></td>
							<td width="80" style="word-break:break-all;" id="bodyPart_<? echo $i;?>"><? echo $body_part[$row[csf('body_part_id')]];?></td>
							<td width="150" style="word-break:break-all;" id="cons_<? echo $i;?>" align="left"><? echo $row['construction'];?></td>
							<td width="50" style="word-break:break-all;" id="gsm_<? echo $i;?>"><? echo $row[csf('gsm')];?></td>
							<td width="50" style="word-break:break-all;" id="dia_<? echo $i;?>"><? echo $row[csf('width')];?></td>
							<td width="70" style="word-break:break-all;" id="color_<? echo $i;?>"><? echo $row['color'];?></td>
							<td width="70" style="word-break:break-all;" id="diaType_<? echo $i;?>"><? echo $row['dia_type'];?></td>
							<td width="70" id="rollWeight_<? echo $i;?>" align="right">
								<input style="width: 60px;text-align: right;" onBlur="fnc_qnty_check(<? echo $i;?>);" class="text_boxes_numeric" type="text" name="rollWeightInput[]" id="rollWeightInput_<? echo $i;?>" value="<? echo $rollWeight;?>"/>
							</td>
							<td width="70" align="right" id="qtyInPcs_<? echo $i;?>"><? echo $qtyInPcs;?></td>
							<td width="60" style="word-break:break-all;" id="buyer_<? echo $i;?>"><? echo $row['buyer'];?></td>
							<td width="80" style="word-break:break-all;" id="job_<? echo $i;?>"><? echo $row['job_no']?></td>
							<td width="80" style="word-break:break-all;" id="order_<? echo $i;?>" align="left"><? echo $row['order_no'];?></td>
							
							<td width="100" style="word-break:break-all;" id="progBookPiNo_<? echo $i;?>"><? echo $row[csf('booking_no')];?></td>
							<td id="button_<? echo $i;?>" align="center">
								<input type="button" id="decrease_<? echo $i;?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i;?>);" />
								<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i;?>" value="<? echo $row[csf('barcode_no')];?>"/>
								<input type="hidden" name="progBookPiId[]" id="progBookPiId_<? echo $i;?>" value="<? echo $row[csf('booking_id')];?>"/>
								<input type="hidden" name="productId[]" id="productId_<? echo $i;?>" value="<? echo $row[csf('prod_id')];?>"/>
								<input type="hidden" name="orderId[]" id="orderId_<? echo $i;?>" value="<? echo $row['order_id'];?>"/>
								<input type="hidden" name="batchId[]" id="batchId_<? echo $i;?>" value="<? echo $row['batch_id'];?>"/>
								<input type="hidden" name="rollId[]" id="rollId_<? echo $i;?>" value="<? echo $row[csf('roll_id')];?>"/>
								<input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i;?>" value="<? echo $rollWeight;?>"/>
								<input type="hidden" name="colorId[]" id="colorId_<? echo $i;?>" value="<? echo $row['color_id'];?>"/>
								<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i;?>" value="<? echo $row['dtls_id'];?>"/>
								<input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i;?>" value="<? echo $row['mst_id'];?>"/>
								<input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $i;?>" value="<? echo $qtyInPcs;?>"/>
								<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i;?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
								<input type="hidden" name="widthDiaType[]" id="widthDiaType_<? echo $i;?>" value="<? echo $row[csf('width_dia_type')];?>"/>
								<input type="hidden" name="serviceCompany[]" id="serviceCompany_<? echo $i;?>" value="<? echo $row[csf('service_company')];?>"/>
								<input type="hidden" name="hiddenGsm[]" id="hiddenGsm_<? echo $i;?>" value="<? echo $row[csf('gsm')];?>"/>
								<input type="hidden" name="hiddenDiaWidth[]" id="hiddenDiaWidth_<? echo $i;?>" value="<? echo $row[csf('width')];?>"/>
								<input type="hidden" name="hiddenJob[]" id="hiddenJob_<? echo $i;?>" value="<? echo $row['job_no'];?>"/>
								<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i;?>" value="<? echo $row[csf('booking_without_order')];?>"/>
								<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i;?>" value="<? echo $row[csf('booking_no')];?>"/>
								<input type="hidden" name="determinationId[]" id="determinationId_<? echo $i;?>" value="<? echo $row['deter_d'];?>"/>
								<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i;?>" value="<? echo $row['buyer_id'];?>"/>
								<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i;?>" value="<? echo $row[csf('roll_no')];?>"/>
								<input type="hidden" name="dtlsIsSales[]" id="dtlsIsSales_<? echo $i;?>" value="<? echo $row[csf('is_sales')];?>"/>
							</td>
						</tr>
						<?

				$barcodezzzz = 1;
			}
		}
		else
		{
			$barcodessss .= $row[csf('barcode_no')].',';
		}




	}

	if($barcodezzzz=="")
	{
		echo "Barcode not found for issue";
		//$barcodessss;
		die;
	}
	$rtnData=chop($rtnData,'__');
	//echo $rtnData;
	

	?>

	

	<?


}

if($action=="populateBarcode_Data_FabFsoServiceWO")
{
	$data = explode("__",$data);

	$cbo_company_id = $data[0];
	$txt_wo_no=$data[1];
	$cbo_service_source=$data[2];
	$cbo_service_company=$data[3];
	$barcode_no = $data[4];
	$cbo_process_no = $data[5];

	$is_sales=1;

	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
	$company_name_array=return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	
	//N.B. Issue to process Source for Heat Settings [1=>grey roll issue (default), 2=> roll receive for batch]
	$variable_set_source_arr =  sql_select("select distribute_qnty from variable_settings_production where variable_list=85 and company_name=$cbo_company_id and status_active=1 and is_deleted=0 order by id desc");
	$variable_set_source = $variable_set_source_arr[0][csf("distribute_qnty")];

	if($variable_set_source==2)
	{
		$variable_set_source=2; // roll receive by batch
	}
	else{
		$variable_set_source=1; //default grey roll issue
	}

	$wo_fso_sql =sql_select("SELECT a.id, a.do_no, a.wo_basis, b.issue_no, b.issue_id, b.fso_id, a.dyeing_source, a.dyeing_compnay_id
	from dyeing_work_order_mst a, dyeing_work_order_dtls b
	where a.id=b.mst_id and a.entry_form=696 and b.status_active=1 and b.is_deleted=0 and a.company_id=$cbo_company_id and a.do_no='$txt_wo_no'
	order by a.id, b.id");

	foreach ($wo_fso_sql as $val) {
		if($val[csf("wo_basis")]==1)
		{
			$issue_id_arr[$val[csf("issue_id")]]=$val[csf("issue_id")];
			$issue_no_arr[$val[csf("issue_no")]]= "'".$val[csf("issue_no")]."'";
		}
		else
		{
			$fso_id_arr[$val[csf("fso_id")]]=$val[csf("fso_id")];
		}

		$dyeing_source=$val[csf("dyeing_source")];
		$dyeing_compnay_id=$val[csf("dyeing_compnay_id")];
	}
	//a.dyeing_source, a.dyeing_compnay_id


	if(!empty($issue_id_arr))
	{
		$issue_id_cond = " and g.id in (".implode(",",$issue_id_arr).")";
		$issue_no_cond = " and g.challan_no in (".implode(",",$issue_no_arr).")";
	}

	if(!empty($fso_id_arr))
	{
		$fso_cond = " and d.id in (".implode(",",$fso_id_arr).")";
	}

	// N.B. 33=>Heat Setting, 100=> Back Sewing, 476=> Heat Setting + Back Sewing
	$heat_settings_business_processes=array(33=>33,100=>100,476=>476);

	if($heat_settings_business_processes[$cbo_process]!="" && $variable_set_source==2)
	{
		$sql="SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, null as job_no_mst, d.job_no as po_number, d.within_group, d.buyer_id, d.po_buyer, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, b.is_sales 
		from inv_receive_mas_batchroll g, pro_grey_batch_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d 
		where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=62 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.is_sales=1 $issue_no_cond $fso_cond 
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, d.job_no, d.within_group, d.buyer_id, d.po_buyer, b.qc_pass_qnty_pcs, b.is_sales";
	}
	else
	{
		$sql="SELECT b.barcode_no, c.id as prod_id, a.body_part_id, c.detarmination_id as febric_description_id, c.gsm, c.dia_width as width, a.color_id as color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, null as job_no_mst, d.job_no as po_number, d.within_group, d.buyer_id, d.po_buyer, sum(b.qnty) qnty, b.qc_pass_qnty_pcs, b.is_sales 
		from inv_issue_master g, inv_grey_fabric_issue_dtls a,pro_roll_details b,product_details_master c, fabric_sales_order_mst d 
		where g.id=a.mst_id and a.id= b.dtls_id and b.entry_form=61 and a.prod_id=c.id and b.po_breakdown_id=d.id and a.status_active=1 and a.is_deleted=0 and b.is_returned=0 and b.is_sales=1 $issue_id_cond $fso_cond  and b.barcode_no in ($barcode_no)
		group by b.barcode_no, c.id, a.body_part_id, c.detarmination_id, c.gsm, c.dia_width, a.color_id, b.roll_id, b.roll_no, b.booking_no, b.booking_without_order, b.po_breakdown_id, d.job_no, d.within_group, d.buyer_id, d.po_buyer, b.qc_pass_qnty_pcs, b.is_sales";
	}

	//and g.knit_dye_source=$dyeing_source and g.knit_dye_company=$dyeing_compnay_id

	//echo $sql;die;
	$data_array=sql_select($sql);
	
	
	if(empty($data_array))
	{
		$rtnData='0__';
		echo $rtnData.$sql;
		die;
	}
	
	$barCode=array();
	$poBreakdownId=array();
	$yarnCountDeterminId=array();
	$bookingId=array();
	$nonOrderbookingId=array();
	$prodIds="";
	$jobNos="";
	$bookingIds="";
	foreach($data_array as $row)
	{
		$barCode[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		if($row[csf('booking_without_order')] != 1)
		{
			$poBreakdownId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		else
		{
			$bookingIds.=$row[csf('po_breakdown_id')].",";
			$nonOrderbookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];
		}
		$yarnCountDeterminId[$row[csf('febric_description_id')]]=$row[csf('febric_description_id')];
		$bookingId[$row[csf('po_breakdown_id')]]=$row[csf('po_breakdown_id')];


		$prodIds.=$row[csf('prod_id')].",";
		$jobNos.="'".$row[csf('job_no_mst')]."',";
	}
	$prodIds=chop($prodIds,",");
	$jobNos=chop($jobNos,",");
	$bookingIds=chop($bookingIds,",");


	/* if(!empty($barCode))
	{
		$rcv_by_batch_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form=62 and b.entry_form=62 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}
	
	if(!empty($rcv_by_batch_arr))
	{
		$rtnData="1__Receive for batch entry found.\nBarcode no: ".$rcv_by_batch_arr[0][csf("barcode_no")]."\nReceive for Batch No. :".$rcv_by_batch_arr[0][csf("recv_number")];;
		echo $rtnData;
		die;
	}
	unset($rcv_by_batch_arr); */

	if(!empty($barCode))
	{
		$roll_issue_to_process_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and a.entry_form=63 and b.entry_form=63 and a.is_rcv_done=0 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}
	
	if(!empty($roll_issue_to_process_arr))
	{
		$rtnData="1__Grey Fab. issue to process found.\nBarcode no: ".$roll_issue_to_process_arr[0][csf("barcode_no")]."\nIssue to process No. :".$roll_issue_to_process_arr[0][csf("recv_number")];
		echo $rtnData;
		die;
	}
	unset($roll_issue_to_process_arr);

	if(!empty($barCode))
	{
		$roll_production_arr = sql_select("select a.barcode_no, b.recv_number from pro_roll_details a,inv_receive_master b where a.mst_id=b.id and a.entry_form=66 and b.entry_form=66 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in (".implode(',',$barCode).")");
	}
	
	if(!empty($roll_production_arr))
	{
		$rtnData="1__Finish fabric roll wise production found.\nBarcode no: ".$roll_production_arr[0][csf("barcode_no")]."\nProduction No. :".$roll_production_arr[0][csf("recv_number")];
		echo $rtnData;
		die;
	}
	unset($roll_production_arr);
	
 	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and b.status_active=1";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
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
	unset($deter_array);
	//print_r($prod_arr);


	$i = 0;
	$rtnData='';
	foreach($data_array as $row)
	{
			$i++;	
			//for buyer

			if($row[csf('within_group')] ==1)
			{
				$row['buyer'] = $buyer_name_array[$row[csf('po_buyer')]];
				$row['buyer_id']=$row[csf('po_buyer')];
			}
			else{
				$row['buyer'] = $buyer_name_array[$row[csf('buyer_id')]];
				$row['buyer_id']=$row[csf('buyer_id')];
			}

			$row['order_no']=$row[csf('po_number')];
			$row['order_id']=$row[csf('po_breakdown_id')];
			
			$rollWeight=number_format($row[csf('qnty')],2);
			$qtyInPcs=$row[csf('qc_pass_qnty_pcs')]*1;
				
			$row['service_company_name']=$company_name_array[$row[csf('service_company')]];
			
			$row['dia_type']=$fabric_typee[$row[csf('width_dia_type')]];
			$row['color']=$color_arr[$row[csf('color_id')]];
			$row['construction']=$composition_arr[$row[csf('febric_description_id')]];
			$row['job_no']=$row[csf('job_no_mst')];
			$row['batch_id']=$row[csf('batch_id')];
			$row['batch_no']=$row[csf('batch_no')];
			$row['deter_d']=$row[csf("febric_description_id")];

			//receive_basis
			$receiveBasisArray=get_receive_basis($row[csf('entry_form')],$row[csf('receive_basis')]);
			$receive_basis_id=$receiveBasisArray['id'];
			$receive_basis_dtls=$receiveBasisArray['dtls'];
			
			$row['mst_id'] = 0;
			$row['dtls_id'] = 0;
			
		$rtnData .=	$row[csf('barcode_no')]."**".
		$row[csf('roll_no')]."**".
		$row['batch_no']."**".
		$row[csf('prod_id')]."**".
		$body_part[$row[csf('body_part_id')]]."**".
		$row['construction']."**".
		$row[csf('gsm')]."**".
		$row[csf('width')]."**".
		$row['color']."**".
		$row['dia_type']."**".
		$rollWeight."**".
		$qtyInPcs."**".
		$row['buyer']."**".
		$row['job_no']."**".
		$row['order_no']."**".
		$row['service_company_name']."**".
		$receive_basis_dtls."**".
		$row[csf('booking_no')]."**".
		$row['mst_id']."**".
		$row['dtls_id']."**".
		$row[csf('color_id')]."**".
		$row[csf('company_id')]."**".
		$receive_basis_id."**".
		$row['order_id']."**".
		$row['batch_id']."**".
		$row[csf('booking_id')]."**".
		$row[csf('body_part_id')]."**".
		$row[csf('width_dia_type')]."**".
		$row[csf('service_company')]."**".
		$row[csf('booking_without_order')]."**".
		$row['deter_d']."**".
		$row['buyer_id']."**".
		$row[csf('roll_id')]."**".
		$row[csf('is_sales')]."__";

	}

	$rtnData=chop($rtnData,'__');
	echo $rtnData;
	?>
	<?
}
?>
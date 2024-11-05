<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=get_color_array();
//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
//print_r($color_arr);die;

if ($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

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
			
		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$id = return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'GIRSR',539,date("Y",time()),13 ));
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',539,".$txt_return_date.",".$cbo_company_id.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$txt_wo_no.",".$txt_issue_id.",".$cbo_process.",".$txt_issue_challan_no.",".$txt_return_challan.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		
		$chk_rcv_barcode = "";
		for($k=1;$k<=$tot_row;$k++)
		{ 
			$activeId="activeId_".$k;
			if($$activeId==1)
			{
				$barcodeNo="barcodeNo_".$k;
				if ($chk_rcv_barcode == "")
					$chk_rcv_barcode = $$barcodeNo;
				else
					$chk_rcv_barcode .= ",".$$barcodeNo;
			}
		}

		if ($chk_rcv_barcode != "")
		{
			$issue_challan = return_library_array("select a.barcode_no,a.barcode_no from pro_roll_details a where a.status_active=1 and a.is_deleted=0 and a.entry_form=63 and a.is_returned=0 and a.barcode_no in ($chk_rcv_barcode) and a.mst_id=$txt_issue_id and a.is_rcv_done=0","barcode_no","barcode_no");
			
			$rcved_challan = sql_select("select b.recv_number,a.barcode_no from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=539 and b.entry_form=539 and a.barcode_no in ($chk_rcv_barcode) and b.issue_id=$txt_issue_id");
			if($rcved_challan[0][csf("recv_number")])
			{
				echo "20**Selected Barcode Found in Issue to Process Return.\nBarcode =" .$rcved_challan[0][csf("barcode_no")] ."\nReceive No=".$rcved_challan[0][csf("recv_number")];
				disconnect($con);
				die;
			}
		}

		$barcodeNos=''; 
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$activeId="activeId_".$j;
			if($$activeId==1)
			{
				$rollId="rollId_".$j;
				$buyerId="buyerId_".$j;
				$bodyPart="bodyPart_".$j;
				$colorId="colorId_".$j;
				$deterId="deterId_".$j;
				$productId="productId_".$j;
				$orderId="orderId_".$j;
				$rollGsm="rollGsm_".$j;
				$knittingSource="knittingSource_".$j;
				$knittingComp="knittingComp_".$j;
				$fabricId="fabricId_".$j;
				$receiveBasis="receiveBasis_".$j;
				$job_no="job_no_".$j;
				$rollwgt="rollwgt_".$j;
				$rolldia="rolldia_".$j;
				$bookingNo="bookingNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$rollNo="rollNo_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$isSales="isSales_".$j;
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
				$issueRollId="issueRollId_".$j;
				

				//------issue validation 61-------------
			
				if($issue_challan[str_replace("'","",$$barcodeNo)]=="")
				{
					echo "20**Selected Barcode Not Found at Issue Challan.\nBarcode =" .$$barcodeNo ;
					disconnect($con);
					die;
				}


				/*
				|--------------------------------------------------------------------------
				| pro_roll_details
				| data preparing for
				| $data_array_roll
				|--------------------------------------------------------------------------
				|
				*/
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
				if($data_array_roll!="")
					$data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$id.",".$dtls_id.",'".$$orderId."',539,'".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rollwgt."','".$$rollwgt."','".$$bookWithoutOrder."','".$$bookingNo."','".$$isSales."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$hiddenQtyInPcs."',".$$issueRollId.")";

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

				$data_array_dtls.="(".$dtls_id.",".$id.",".$$productId.",".$$rollwgt.",'".$$rollId."','".$$orderId."','".$$colorId."',".$cbo_process.",".$$hiddenQtyInPcs.",'".$$bodyPart."','".$$knittingSource."','".$$knittingComp."','".$$rollGsm."','".trim($$rolldia)."','".$$job_no."','".$$rollNo."','".$$bookWithoutOrder."','".$$bookingNo."','".$$deterId."','".$$buyerId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				

				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$id_roll.",";

				$all_barcode_nos .= $$barcodeNo.",";
				$all_issue_roll_id.= $$issueRollId.",";
			}
		}
		$all_barcode_nos = chop($all_barcode_nos,",");
		$all_issue_roll_id = chop($all_issue_roll_id,",");
		//echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll";oci_rollback($con);die;
		
		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="id,recv_number_prefix,recv_number_prefix_num,recv_number,entry_form,receive_date,company_id,dyeing_source,dyeing_company,wo_no, issue_id, process_id,gray_issue_challan_no,challan_no,remarks,inserted_by,insert_date";
		$rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);
		
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_id,roll_no,barcode_no,qnty,qc_pass_qnty,booking_without_order,booking_no,is_sales,inserted_by,insert_date,qc_pass_qnty_pcs,issue_roll_id";
		$rID2=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		
		/*
		|--------------------------------------------------------------------------
		| pro_grey_batch_dtls
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls="id,mst_id,prod_id,roll_wgt,roll_id,order_id,color_id,process_id,qty_in_pcs,body_part_id,knitting_source,knitting_company,gsm,width,job_no,roll_no,booking_without_order,booking_no,febric_description_id,buyer_id,inserted_by,insert_date";

		$rID3=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,1);


		$rID4 = execute_query("update pro_roll_details set is_returned=1 where entry_form=63 and mst_id = $txt_issue_id and id in (". $all_issue_roll_id .")  and barcode_no in (".$all_barcode_nos.")");


		//echo "10**"."update pro_roll_details set is_returned=1 where entry_form=63 and mst_id = $txt_issue_id and id in (". $all_issue_roll_id .")  and barcode_no in (".$all_barcode_nos.")";oci_rollback($con);die;
		//echo "10**insert into pro_grey_batch_dtls ($field_array_dtls) values $data_array_dtls";oci_rollback($con);die;
		//echo "10**$rID && $rID2 && $rID3 && $rID4"; oci_rollback($con); die;
		

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4)
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
			if($rID && $rID2 && $rID3 && $rID4)
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

		if(str_replace("'","",$update_id) == "")
		{
			echo "6**0**0";
			disconnect($con);
			die;
		}

		$chk_rcv_barcode = "";
		for($k=1;$k<=$tot_row;$k++)
		{ 
			$activeId="activeId_".$k;
			$updateDetailsId="updateDetailsId_".$k;
			if($$activeId==1)
			{
				if($$updateDetailsId =="" || $$updateDetailsId ==0 )
				{
					$barcodeNo="barcodeNo_".$k;
					if ($chk_rcv_barcode == "") $chk_rcv_barcode = $$barcodeNo; else  $chk_rcv_barcode .= ",".$$barcodeNo;
				}
			}
			
			if($$updateDetailsId)
			{
				if($$activeId==0)
				{
					$barcodeNo="barcodeNo_".$k;
					$updateRollId="updateRollId_".$k;
					if ($nxt_trans_roll_check == "") $nxt_trans_roll_check = $$barcodeNo; else  $nxt_trans_roll_check .= ",".$$barcodeNo;

					$del_varcode_ref[$$barcodeNo] = $$updateRollId;
				}
			}
		}

		if ($chk_rcv_barcode != "")
		{
			$rcved_challan = sql_select("select b.recv_number,a.barcode_no from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=539 and b.entry_form=539 and a.barcode_no in ($chk_rcv_barcode) and a.issue_id = $txt_issue_id");
			

			if($rcved_challan[0][csf("recv_number")])
			{
				echo "20**Selected Barcode Found in Issue to process return page.\nBarcode =" .$rcved_challan[0][csf("barcode_no")] ."\nReceive No=".$rcved_challan[0][csf("recv_number")];
				disconnect($con);
				die;
			}
		}

		if ($nxt_trans_roll_check != "") // Next process roll entry check  
		{			
			$nxt_process_chk = sql_select("select max(id) as max_id, barcode_no from pro_roll_details where entry_form in (63, 65) and status_active=1 and is_deleted=0 and barcode_no in ($nxt_trans_roll_check) group by barcode_no");

			foreach ($nxt_process_chk as  $val) 
			{
				$max_barcode_roll_id[$val[csf("barcode_no")]]  = $val[csf("max_id")];
			}

			foreach ($del_varcode_ref as $barCode => $val) 
			{
				if($max_barcode_roll_id[$barCode] >  $val)
				{
					echo "20**Update Restricted. Next Transaction Found.\nBarcode =" .$barCode ;
					disconnect($con);
					die;
				}
			}
		}
		
		$barcodeNos=''; $all_del_issue_id="";
		for($j=1;$j<=$tot_row;$j++)
		{ 
			$activeId="activeId_".$j;
			$updateDetailsId="updateDetailsId_".$j;
			$updateRollId="updateRollId_".$j;
			$issueRollId="issueRollId_".$j;
			
			if($$activeId==0 )
			{
				if($$updateDetailsId!="")
				{
					$updateDetailsId_arr[]=$$updateDetailsId;
					$data_array_delete[$$updateDetailsId] = explode("*", ("0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					$all_del_issue_id .= $$issueRollId.",";
				}
			}
			
			if($$activeId==1)
			{
				$rollId="rollId_".$j;
				$buyerId="buyerId_".$j;
				$bodyPart="bodyPart_".$j;
				$colorId="colorId_".$j;
				$deterId="deterId_".$j;
				$productId="productId_".$j;
				$orderId="orderId_".$j;
				$rollGsm="rollGsm_".$j;
				$knittingSource="knittingSource_".$j;
				$knittingComp="knittingComp_".$j;
				$fabricId="fabricId_".$j;
				$receiveBasis="receiveBasis_".$j;
				$job_no="job_no_".$j;
				$rollwgt="rollwgt_".$j;
				$rolldia="rolldia_".$j;
				$bookingNo="bookingNo_".$j;
				$barcodeNo="barcodeNo_".$j;
				$rollNo="rollNo_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$isSales="isSales_".$j;
				$hiddenQtyInPcs="hiddenQtyInPcs_".$j;

				
				if(str_replace("'","",$$updateDetailsId)>0)
				{
					/*
					|--------------------------------------------------------------------------
					| pro_roll_details
					| data preparing for
					| $update_array_roll
					|--------------------------------------------------------------------------
					|
					*/
					$update_roll_id[]=str_replace("'","",$$updateRollId);
					$update_array_roll[str_replace("'","",$$updateRollId)]=explode("*",("".$$rollwgt."*".$$hiddenQtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					/*
					|--------------------------------------------------------------------------
					| pro_grey_batch_dtls
					| data preparing for
					| $update_array_dtls
					|--------------------------------------------------------------------------
					|
					*/
					$update_dtls_id[]=str_replace("'","",$$updateDetailsId);
					$update_array_dtls[str_replace("'","",$$updateDetailsId)]=explode("*",("".$$rollwgt."*".$$hiddenQtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));	
					$barcodeNos.=$$barcodeNo."__".str_replace("'","",$$updateDetailsId)."__".str_replace("'","",$$updateRollId).",";	
				}
				else
				{
					/*
					|--------------------------------------------------------------------------
					| pro_roll_details
					| data preparing for
					| $data_array_roll
					|--------------------------------------------------------------------------
					|
					*/
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
					if($data_array_roll!="")
						$data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",".$update_id.",".$dtls_id.",'".$$orderId."',539,'".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rollwgt."','".$$rollwgt."','".$$bookWithoutOrder."','".$$bookingNo."','".$$isSales."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$hiddenQtyInPcs."',".$$issueRollId.")";
					
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
					//$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$rollId.",'".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."','".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rolldia."','".$$rollwgt."','".$$buyerId."','".$$job_no."','".$$orderId."','".$$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.")";

					$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$productId.",".$$rollwgt.",'".$$rollId."','".$$orderId."','".$$colorId."',".$cbo_process.",".$$hiddenQtyInPcs.",'".$$bodyPart."','".$$knittingSource."','".$$knittingComp."','".$$rollGsm."','".trim($$rolldia)."','".$$job_no."','".$$rollNo."','".$$bookWithoutOrder."','".$$bookingNo."','".$$deterId."','".$$buyerId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$id_roll.",";

					$all_new_issue_roll_id.= $$issueRollId.",";
				}
			}
		}

		$all_del_issue_id = chop($all_del_issue_id,",");
		$all_new_issue_roll_id = chop($all_new_issue_roll_id,",");
		
		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$flag=0;
		$rID2=true;
		$rID3=true;
		$rID3_1=true;
		$statusChange=true;
		$field_array="receive_date*challan_no*remarks*updated_by*update_date";
		$data_array="".$txt_return_date."*'".str_replace("'","",$txt_return_challan)."'*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
		if($rID)
			$flag=1;
		else
			$flag=0;
		

		//echo "10**".$field_array."<br>".$data_array;oci_rollback($con); disconnect($con); die;
		$updateDetailsId_arr = array_filter($updateDetailsId_arr);
		if(count($updateDetailsId_arr)>0)
		{
			/*
			|--------------------------------------------------------------------------
			| pro_grey_batch_dtls
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			//$rID2=execute_query("delete from pro_grey_batch_dtls where id in (".implode(",",$updateDetailsId_arr).")");
			//if($flag==1) { if($rID2) $flag=1; else $flag=0; }
			//$rID3=execute_query("delete from pro_roll_details where dtls_id in (".implode(",",$updateDetailsId_arr).") and entry_form=62");
			//if($flag==1) { if($rID3) $flag=1; else $flag=0; }
			$field_array_delete = "status_active*is_deleted*updated_by*update_date";
			$rID2 = execute_query(bulk_update_sql_statement("pro_grey_batch_dtls","id", $field_array_delete, $data_array_delete, $updateDetailsId_arr), 1);
			if($flag==1)
			{
				if($rID2)
					$flag=1;
				else
					$flag=0;
			}

			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			$rID3=execute_query("update pro_roll_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where dtls_id in(".implode(",",$updateDetailsId_arr).") and entry_form=539");
			if($flag==1)
			{
				if($rID3)
					$flag=1;
				else
					$flag=0;
			}

			$rID3_1=execute_query("update pro_roll_details set is_returned=0 where id in(".$all_del_issue_id.") and entry_form=63");
			if($flag==1)
			{
				if($rID3_1)
					$flag=1;
				else
					$flag=0;
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$rID4=true;
		if($data_array_roll!="")
		{
			$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_id,roll_no,barcode_no,qnty,qc_pass_qnty,booking_without_order,booking_no,is_sales,inserted_by,insert_date,qc_pass_qnty_pcs,issue_roll_id";
			//echo "10**insert into pro_roll_details  (".$field_array_roll.") values ".$data_array_roll;die;
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID4)
					$flag=1;
				else
					$flag=0;
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| pro_grey_batch_dtls
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$rID5=true;
		if($data_array_dtls!="")
		{
			//$field_array_insert="id,mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,gsm,width,roll_wgt,buyer_id,job_no,order_id,color_id,inserted_by,insert_date,qty_in_pcs";
			$field_array_insert="id,mst_id,prod_id,roll_wgt,roll_id,order_id,color_id,process_id,qty_in_pcs,body_part_id,knitting_source,knitting_company,gsm,width,job_no,roll_no,booking_without_order,booking_no,febric_description_id,buyer_id,inserted_by,insert_date";
			$rID5=sql_insert("pro_grey_batch_dtls",$field_array_insert,$data_array_dtls,1);
			if($flag==1)
			{
				if($rID5)
					$flag=1;
				else
					$flag=0;
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$update_roll=true;
		if(count($update_array_roll)>0)
		{
			$field_array_roll_update="qc_pass_qnty*qc_pass_qnty_pcs*updated_by*update_date";
			$update_roll=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_array_roll_update,$update_array_roll,$update_roll_id),1);
			if($flag==1) 
			{
				if($update_roll)
					$flag=1;
				else
					$flag=0; 
			} 
		}
		
		/*
		|--------------------------------------------------------------------------
		| pro_grey_batch_dtls
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$update_dtls=true;
		if(count($update_array_dtls)>0)
		{
			$field_array_dtls_update="roll_wgt*qty_in_pcs*updated_by*update_date";
			$update_dtls=execute_query(bulk_update_sql_statement("pro_grey_batch_dtls","id",$field_array_dtls_update,$update_array_dtls,$update_dtls_id),1);
			if($flag==1) 
			{
				if($update_dtls)
					$flag=1;
				else
					$flag=0; 
			} 
		}


		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| new data issue updating here
		|--------------------------------------------------------------------------
		|
		*/

		if($all_new_issue_roll_id !="")
		{
			$rID4 = execute_query("update pro_roll_details set is_returned=1 where entry_form=63 and mst_id = $txt_issue_id and id in (". $all_new_issue_roll_id .")");
		}
		//echo "10**".$flag."&&".$rID3_1;oci_rollback($con); disconnect($con); die;
		
		
		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**0";
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
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**0";
			}
		}
		disconnect($con);
		die;
	}
	/*
	|--------------------------------------------------------------------------
	| delete
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation==2)
	{ 
		die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		if(str_replace("'","",$update_id) == "")
		{
			echo "6**0**0";
			disconnect($con);
			die;
		}

		$saved_barcode_no_arr = sql_select("select barcode_no from pro_roll_details where entry_form = 62 and status_active =1 and is_deleted =0 and mst_id = $update_id");
		foreach ($saved_barcode_no_arr as $val) 
		{
			$barcode_no_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		}

		$barcode_no_arr = array_filter($barcode_no_arr);
		if(count($barcode_no_arr)>0)
		{
			$barcode_nos = implode(",", $barcode_no_arr);
			$all_barcode_no_cond=""; $barCond=""; 
			if($db_type==2 && count($barcode_no_arr)>999)
			{
				$all_barcode_no_arr_chunk=array_chunk($barcode_no_arr,999) ;
				foreach($all_barcode_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$barCond.="  b.barcode_no in($chunk_arr_value) or ";	
				}
				
				$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";	
			}
			else
			{
				$all_barcode_no_cond=" and b.barcode_no in($barcode_nos)";	 
			}
		}

		if(!empty($barcode_no_arr))
		{
			$batch_sql = sql_select("select a.batch_no,b.barcode_no from  pro_batch_create_mst a, pro_roll_details b where a.id=b.mst_id and b.entry_form=64 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $all_barcode_no_cond");

			foreach ($batch_sql as $row) 
			{
				$batch_name_array[$row[csf("barcode_no")] . "_". $row[csf("batch_no")]] = $row[csf("barcode_no")] . "_". $row[csf("batch_no")];
			}

			if(!empty($batch_name_array))
			{
				echo "7**0**0**".implode(",",$batch_name_array);
				disconnect($con);
				die;
			}

			$sql_split_roll="select b.barcode_no from pro_roll_details b where b.entry_form=62 and b.status_active=1 and b.is_deleted=0 and b.roll_split_from > 0 $all_barcode_no_cond";
			$split_sql=sql_select($sql_split_roll);
			$splite_roll_arr=array();
			foreach($split_sql as $inv)
			{
				$splite_roll_arr[$inv[csf('barcode_no')]]=$inv[csf('barcode_no')];  
			}

			$sql_split_roll="select b.barcode_no from pro_roll_split b  where b.entry_form=75 and b.status_active=1 and b.is_deleted=0 $all_barcode_no_cond";
			$split_sql=sql_select($sql_split_roll);
			foreach($split_sql as $inv)
			{
				$splite_roll_arr[$inv[csf('barcode_no')]]=$inv[csf('barcode_no')];  
			}

			if($splite_roll_arr[$inv[csf('barcode_no')]])
			{
				// .$splite_roll_arr[$inv[csf('barcode_no')]]
				echo "20**Update Restricted. Because Following Barcode Are Inserted in Splitting Page.\nBarcode = ".implode(",",$splite_roll_arr);
				disconnect($con);
				die;
			}

			/*
			|--------------------------------------------------------------------------
			| pro_grey_batch_dtls
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			$rID=execute_query("update pro_grey_batch_dtls set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where mst_id =$update_id");
			if($flag==1)
			{
				if($rID)
					$flag=1;
				else
					$flag=0;
			}

			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data updating here
			|--------------------------------------------------------------------------
			|
			*/
			$rID2=execute_query("update pro_roll_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where mst_id =$update_id and entry_form=62");
			if($flag==1)
			{
				if($rID2)
					$flag=1;
				else
					$flag=0;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$rID3=execute_query("update inv_receive_mas_batchroll set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where id =$update_id");
		if($rID3)
			$flag=1;
		else
			$flag=0;

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**0**0**".implode(",",$batchInserted_arr);
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
			if($flag==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".substr($barcodeNos,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "7**0**0**".implode(",",$batchInserted_arr);
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="grey_item_details_update")
{
	$data = explode("_",$data);
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$machine_arr=return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

	//MANI QUERY
	$sql_update=sql_select("select  b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id,b.width_dia_type, b.febric_description_id,b.gsm,b.width,b.roll_wgt as roll_wgt_curr,b.roll_id,b.buyer_id,b.order_id,b.color_id,a.challan_no,c.id as update_roll_id, c.barcode_no, c.qnty as roll_wgt,c.roll_no,c.is_sales,c.booking_without_order,c.qc_pass_qnty_pcs, b.job_no, c.issue_roll_id, a.wo_no from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=539 and a.id=$data[0] and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");//and $null_cond(c.roll_split_from,0)<=0

	$barcode_arr=$order_id_arr=$color_id_arr=$gsm_id_arr=$sales_order_id_arr=$sales_color_id_arr=$sales_gsm_id_arr=array();
	foreach($sql_update as $val)
	{
		$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		if($val[csf("is_sales")] == 1){
			$sales_order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
			$sales_color_id_arr[$val[csf("color_id")]] = $val[csf("color_id")];
			$sales_gsm_id_arr[$val[csf("gsm")]] = $val[csf("gsm")];
		}else{
			$order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
			$color_id_arr[$val[csf("color_id")]] = $val[csf("color_id")];
			$gsm_id_arr[$val[csf("gsm")]] = "'".$val[csf("gsm")]."'";
		}
	}


	//unsaved sql == >>
	$inserted_roll=sql_select("select c.barcode_no from pro_grey_batch_dtls a,inv_receive_mas_batchroll b, pro_roll_details c  where a.mst_id=b.id and a.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=539 and c.entry_form=539 and b.gray_issue_challan_no='$data[1]'");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[$inf[csf('barcode_no')]]=$inf[csf('barcode_no')];
	}

	$nxt_process_chk = sql_select("select max(id) as max_id, barcode_no from pro_roll_details where entry_form in (63, 65) and status_active=1 and is_deleted=0 and barcode_no in (".implode(',',$barcode_arr).") group by barcode_no");

	foreach ($nxt_process_chk as  $val) 
	{
		$max_barcode_roll_id[$val[csf("barcode_no")]]  = $val[csf("max_id")];
	}

	if(count($inserted_roll_arr)>0) $roll_cond=" and c.barcode_no not in (".implode(",",array_unique($inserted_roll_arr)).") ";

	//$unsaved_sql = sql_select("select  b.id, a.issue_number, a.challan_no, a.order_id, a.issue_purpose, b.trans_id, b.distribution_method, b.program_no, b.no_of_roll, b.roll_no, b.roll_po_id, b.roll_wise_issue_qnty, b.prod_id, b.issue_qnty, b.color_id, b.yarn_lot, b.yarn_count, b.store_name, b.rack, b.self, b.stitch_length, b.remarks,c.roll_id, c.barcode_no, c.roll_no,c.po_breakdown_id, c.is_sales, c.booking_without_order,c.qc_pass_qnty_pcs from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 and c.is_returned!=1 and a.issue_number='".$data[1]."' and c.status_active=1 and c.is_deleted=0 $roll_cond ");	


	$unsaved_sql = sql_select("SELECT c.barcode_no, a.wo_no, a.recv_number, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width,c.qnty, c.qc_pass_qnty_pcs, b.buyer_id, b.job_no, c.po_breakdown_id, c.po_breakdown_id as order_id, b.color_id, b.process_id, c.roll_no, c.roll_id, c.id as issue_roll_id, c.booking_without_order, c.is_sales, a.dyeing_source, a.dyeing_company FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form=63 and c.entry_form=63 and c.is_returned!=1 and c.is_rcv_done=0 and a.recv_number='$data[1]' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $roll_cond ");

	foreach ($unsaved_sql as $val) 
	{
		$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		if($val[csf("is_sales")] == 1){
			$sales_order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
		}

		if($val[csf("booking_without_order")] == 1)
		{
			$non_ord_book_arr[$row[csf("order_id")]] = $row[csf("order_id")];
		}
		else
		{
			$order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
		}



	}

	//<<   ==   unsaved sql


	$barcode_arr = array_filter($barcode_arr);
	$order_id_arr = array_filter($order_id_arr);
	$color_id_arr = array_filter($color_id_arr);
	$gsm_id_arr = array_filter($gsm_id_arr);
	$sales_order_id_arr = array_filter($sales_order_id_arr);


	if(!empty($order_id_arr)){
		$order_cond = (!empty($order_id_arr))?" and b.id in(".implode(",",$order_id_arr).")":"";
		
		$data_array=sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.file_no, b.id as po_id, c.booking_no FROM wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on b.id = c.po_break_down_id and c.booking_type in (1,4) WHERE a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_cond $color_cond $gsm_cond  group by a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.file_no, b.id, c.booking_no");
		$po_details_array=array();
		foreach($data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];

			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];

		}

		$job_arr=array();

	}

	if(!empty($sales_order_id_arr)){
		$salesorder_cond = (!empty($sales_order_id_arr))?" and id in(".implode(",",$sales_order_id_arr).")":"";
		$sales_order_result = sql_select("select id,job_no,po_job_no,buyer_id,po_buyer,within_group,sales_booking_no,insert_date from fabric_sales_order_mst where status_active=1 and is_deleted=0 $salesorder_cond");
		$sales_arr = array();
		foreach ($sales_order_result as $sales_row) 
		{
			$sales_arr[$sales_row[csf("id")]]["within_group"] 		= $sales_row[csf("within_group")];
			$sales_arr[$sales_row[csf("id")]]["po_number"] 			= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("id")]]["sales_booking_no"] 	= $sales_row[csf("sales_booking_no")];		
			$sales_arr[$sales_row[csf("id")]]["po_job_no"] 			= $sales_row[csf("po_job_no")];
			$sales_arr[$sales_row[csf("id")]]["job_no"] 			= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("id")]]["buyer_id"] 			= $sales_row[csf("buyer_id")];
			$sales_arr[$sales_row[csf("id")]]["po_buyer"] 			= $sales_row[csf("po_buyer")];
			$sales_arr[$sales_row[csf("id")]]["year"] 				= date("Y",strtotime($sales_row[csf("insert_date")]));

			$salesbookingNos .= "'".$sales_row[csf("sales_booking_no")]."',";
		}
	}

	$salesbookingNos = chop($salesbookingNos,',');

	$salesBookinginfo = array();
	if($salesbookingNos!="")
	{

		$data_array_info=sql_select("SELECT b.job_no, a.buyer_id, a.booking_date, b.booking_no, c.grouping 
		from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c
		where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no in($salesbookingNos) 
		group by b.job_no, a.buyer_id, a.booking_date, b.booking_no, c.grouping");
		foreach($data_array_info as $row)
		{
			$salesBookinginfo[$row[csf("booking_no")]]['year'] = date("Y",strtotime($row[csf("booking_date")]));
			$po_details_arr[$row[csf("booking_no")]]['file_no']=$row[csf("file_no")];
			$po_details_arr[$row[csf("booking_no")]]['int_ref']=$row[csf("grouping")];
		}
	}

	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$barcode_cond = (!empty($barcode_arr))?" and c.barcode_no in(".implode(",",$barcode_arr).")":"";
	

	$total_roll_wight=0;
	$total_roll_qtyInPcs=0;
	$issue_details_arr=array();
	$j=1;
	foreach($sql_update as $val)
	{
		$inserted_roll_arr[]=$val[csf('roll_id')];
		$subcon_cond="";
		//if(in_array($val[csf('barcode_no')],$subcontact_roll)){ $subcon_cond="disabled";}

		if($max_barcode_roll_id[$val[csf("barcode_no")]] > $val[csf("update_roll_id")])
		{
			$subcon_cond="disabled";
		}

		$color='';
		$color_ids=explode(",",$val[csf('color_id')]);
		foreach($color_ids as $color_id)
		{
			if($color_id>0) $color.=$color_arr[$color_id].",";
		}
		$color=chop($color,',');
		$is_sales = $val[csf('is_sales')];
		
		if($is_sales == 1)
		{
			$sales_booking=$sales_arr[$val[csf('order_id')]]["sales_booking_no"];
			$within_group 	= $sales_arr[$val[csf('order_id')]]["within_group"]; 
			if($within_group==1)
			{
				$po_number = $sales_arr[$val[csf('order_id')]]["po_number"];

				$buyer_id = $sales_arr[$val[csf('order_id')]]["po_buyer"];
			}
			else
			{
				$po_number = $sales_arr[$val[csf('order_id')]]["po_number"];
				$buyer_id = $sales_arr[$val[csf('order_id')]]["buyer_id"];
			}
		}
		else
		{
			$po_number = $po_details_array[$val[csf('order_id')]]['po_number'];
			$jobNo = $po_details_array[$val[csf('order_id')]]['job_no'];
			$job_no_full = $po_details_array[$val[csf("order_id")]]['job_no_full'];

			$buyer_id = $val[csf('buyer_id')];

			if($val[csf('booking_without_order')]==1)
			{
				$jobNo = "";
				$job_no_full = "";
				$po_number ="";

				$buyer_id = $val[csf('buyer_id')];
			}
		}
		?>
        <tr id="tr_1" align="center" valign="middle">
            <td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" onClick="totalCalculation('<? echo $j; ?>');" checked="checked"  <? echo $subcon_cond;?> > &nbsp; &nbsp;<? echo $j; ?></td>
            <td width="40" id="rollId_<? echo $j; ?>"> <? echo $val[csf('roll_no')]; ?></td>
            <td width="70" id="barcode_<? echo $j; ?>"> <? echo $val[csf('barcode_no')]; ?></td>
            <td width="80" id="systemId_<? echo $j; ?>"> <? echo $body_part[$val[csf('body_part_id')]]; ?></td>
            <td width="120" id="progBookId_<? echo $j; ?>" style="word-break: break-all;"> <? echo $composition_arr[$val[csf('febric_description_id')]]; ?></td>
            <td width="50" id="basis_<? echo $j; ?>"> <? echo $val[csf('gsm')]; ?></td>
            <td width="50" id="width_<? echo $j; ?>"> <? echo $val[csf('width')]; ?></td>
            <td width="100" id="color_<? echo $j; ?>" style="word-break: break-all;"> <? echo $color; ?></td>

            <td width="60" id="rollWgt_<? echo $j; ?>" name="rollWgt[]" align="right"><? echo $val[csf('roll_wgt_curr')]; ?></td>
            <td width="60" id="qtyInPcs_<? echo $j; ?>" name="qtyInPcs[]" align="right"><? echo $val[csf('qc_pass_qnty_pcs')]*1; ?></td>
            <td width="100" id="job_<? echo $j; ?>"><? echo $val[csf('job_no')]; ?></td>

            <td width="65" id="buyer_<? echo $j; ?>"><? echo $buyer_name_array[$buyer_id]; ?></td>
            <td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo $po_number; ?></td>

            <td width="90" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $val[csf('wo_no')]; ?></td>
                <? 
                $total_roll_wight+=$val[csf('roll_wgt_curr')];
                $total_roll_qtyInPcs+=$val[csf('qc_pass_qnty_pcs')]*1;
                ?> 
			<input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="<? echo $val[csf('id')]; ?>" />
			<input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $j; ?>" value="<? echo $val[csf('update_roll_id')]; ?>" />
			<input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $val[csf('roll_id')]; ?>" />
			<input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $val[csf('roll_no')]; ?>" />
			<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $val[csf("body_part_id")]; ?>"/>
			<input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $val[csf("color_id")]; ?>" />
			<input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
			<input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $val[csf("prod_id")]; ?>" />
			<input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $val[csf("roll_wgt_curr")]; ?>"/>
			<input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $j; ?>" value="<? echo $val[csf("qc_pass_qnty_pcs")]*1; ?>"/>
			<input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $val[csf("width")]; ?>"/>
			<input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $val[csf("gsm")]; ?>"/>
			<input type="hidden" name="fabricId[]" id="fabricId_<? echo $j; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>

			<input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $val[csf("knitting_source")]; ?>"/>
			<input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $val[csf("knitting_company")]; ?>"/>
			<input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $val[csf('barcode_no')]; ?>"/>
			<input type="hidden" name="isSales[]" id="isSales_<? echo $j; ?>" value="<? echo $val[csf('is_sales')]; ?>"/>
			<input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $val[csf("order_id")]; ?>" />
			<input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $buyer_id ?>"/>
			<?
			if($val[csf("booking_without_order")]==1)
			{
				?>
				<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $val[csf("wo_no")]; ?>"/>
				<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? ?>"/>
				<?
			}
			else
			{
				?>
				<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $val[csf("wo_no")]; ?>"/>
				<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $val[csf('job_no')]; ?>"/>
				<?
			}
			?>
			<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $j; ?>" value="<? echo $val[csf("booking_without_order")]; ?>"/>
			<input type="hidden" name="issueRollId[]" id="issueRollId_<? echo $j; ?>" value="<? echo $val[csf('issue_roll_id')]; ?>"/>
		</tr>
    <?
    $j++;
}
//unsaved data below here=====================================================================================
if(count($unsaved_sql)>0)
{
	foreach($unsaved_sql as $inf)
	{
		$color='';
		$color_ids=explode(",",$inf[csf('color_id')]);
		foreach($color_ids as $color_id)
		{
			if($color_id>0) $color.=$color_arr[$color_id].",";
		}
		$color=chop($color,',');

		?>
		<tr id="tr_1" align="center" valign="middle">
			<td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" > &nbsp; &nbsp;<? echo $j; ?></td>
			<td width="40" id="rollId_<? echo $j; ?>"> <? echo $inf[csf('roll_no')]; ?></td>
			<td width="70" id="barcode_<? echo $j; ?>"> <? echo $inf[csf('barcode_no')]; ?></td>
			<td width="80" id="systemId_<? echo $j; ?>"> <? echo $body_part[$inf[csf('body_part_id')]]; ?></td>
			<td width="120" id="progBookId_<? echo $j; ?>"> <? echo $composition_arr[$inf[csf('febric_description_id')]]; ?></td>
			<td width="50" id="basis_<? echo $j; ?>"> <? echo $inf[csf('gsm')]; ?></td>
			<td width="50" id="knitSource_<? echo $j; ?>"> <? echo $inf[csf('width')]; ?></td>
			<td width="100" id="prodDate_<? echo $j; ?>" style="word-break: break-all;"> <? echo $color; ?></td>

			<td width="60" id="rollWgt_<? echo $j; ?>" name="rollWgt[]" align="right"><? echo $inf[csf('qnty')]; ?></td> 
			<td width="60" id="qtyInPcs_<? echo $j; ?>" name="qtyInPcs[]" align="right"><? echo $inf[csf('qc_pass_qnty_pcs')]; ?></td>
			
			<td width="50" id="job_<? echo $j; ?>"><? echo $inf[csf('job_no')]; ?></td>
			<td width="65" id="buyer_<? echo $j; ?>"><? echo $buyer_name_array[$inf[csf('buyer_id')]]; ?></td>
			<?
			if($inf[csf('booking_without_order')]==1)
			{
				?>
				<td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><?  ?></td>

				<?
			}
			else
			{
				?>
				<td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo $po_details_array[$inf[csf('po_breakdown_id')]]['po_number']; ?></td>
				<?
			}

			?>

			<td width="90" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $inf[csf('wo_no')]; ?></td>
				<?
					$total_roll_wight+=$inf[csf('qnty')];
					$total_roll_qtyInPcs+=$inf[csf('qc_pass_qnty_pcs')]; 
				?>  
				<input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="0" /> 
				<input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $j; ?>" value="0" />                          
				<input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $inf[csf('roll_id')]; ?>" />
				<input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $inf[csf('roll_no')]; ?>" />
				<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $inf[csf('body_part_id')]; ?>"/>
				<input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $inf[csf('color_id')]; ?>" />
				<input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $inf[csf('febric_description_id')]; ?>"/>
				<input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $inf[csf('prod_id')];  ?>" />

				<input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $inf[csf('po_breakdown_id')]; ?>" />
				<input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $inf[csf('buyer_id')]; ?>"/> 
				<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $inf[csf('job_no')]; ?>"/>

				<input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $inf[csf('qnty')]; ?>"/>
				<input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $j; ?>" value="<? echo $inf[csf('qc_pass_qnty_pcs')]; ?>"/>
                <input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $inf[csf('width')]; ?>"/>
				<input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $inf[csf('gsm')]; ?>"/>

				<input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $inf[csf('dyeing_source')]; ?>"/>
				<input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $inf[csf('dyeing_company')]; ?>"/>

				<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $inf[csf('wo_no')]; ?>"/>
				<input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $inf[csf('barcode_no')]; ?>"/>
				<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $j; ?>" value="<? echo $inf[csf('booking_without_order')]; ?>"/>
				<input type="hidden" name="isSales[]" id="isSales_<? echo $j; ?>" value="<? echo $inf[csf('is_sales')]; ?>"/>
				<input type="hidden" name="issueRollId[]" id="issueRollId_<? echo $j; ?>" value="<? echo $inf[csf('issue_roll_id')]; ?>"/>
			</tr>
			<?
			$j++;
		}
	}
	?>
	<table cellpadding="0" cellspacing="0" width="1360" border="1" id="scanning_tbl" rules="all" class="rpt_table">
		<tfoot>
			<tr>
				<th colspan="9">Total</th>
				<th id="total_calculate_qty_id"><? echo number_format($total_roll_wight,2); ?></th>
				<th id="total_calculate_qtyInPcs_id"><? echo $total_roll_qtyInPcs; ?></th>
				<th colspan="10"></th>

			</tr>
		</tfoot>
	</table>
	<?
	exit();
}

if($action=="check_challan_no")
{
	$data_array = sql_select("SELECT c.barcode_no from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form=63 and c.entry_form=63 and c.is_returned!=1 and a.recv_number='$data' and c.status_active=1 and c.is_deleted=0 and c.is_rcv_done=0");

	//$received_roll=sql_select("select c.barcode_no from pro_grey_batch_dtls a,inv_receive_mas_batchroll b, pro_roll_details c  where a.mst_id=b.id and a.id=c.dtls_id and b.id=c.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=65 and c.entry_form=65 and a.challan_no='$data'");

	$inserted_roll=sql_select("select c.barcode_no from pro_grey_batch_dtls a,inv_receive_mas_batchroll b, pro_roll_details c  
	where a.mst_id=b.id and a.id=c.dtls_id and b.id=c.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 
	and b.entry_form=439 and c.entry_form=439 and b.challan_no='$data'");
	
	if(empty($data_array))
	{
		echo 2;
	}
	else if(count($data_array)-count($inserted_roll)>0)
	{ 
		echo 1;
	}
	else
	{ 
		echo 0; 
	}
	exit();	
}

//need for this
if($action=="grey_item_details")
{
	$buyer_name_array = return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array = return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

	$inserted_roll=sql_select("select c.barcode_no from pro_grey_batch_dtls a,inv_receive_mas_batchroll b, pro_roll_details c  where a.mst_id=b.id and a.id=c.dtls_id and b.id=c.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=539 and c.entry_form=539 and b.gray_issue_challan_no='$data'");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[$inf[csf('barcode_no')]]=$inf[csf('barcode_no')];
	}

	if(count($inserted_roll_arr)>0) $roll_cond=" and c.barcode_no not in (".implode(",",array_unique($inserted_roll_arr)).") ";

	$sql = sql_select("SELECT c.barcode_no, a.wo_no, a.recv_number, b.prod_id, b.body_part_id, b.febric_description_id, d.gsm, d.dia_width as width, c.qnty, b.buyer_id, b.job_no, c.po_breakdown_id,b.color_id, b.process_id, c.roll_no, c.roll_id, c.id as issue_roll_id, c.booking_without_order, c.is_sales, a.dyeing_source, a.dyeing_company FROM inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c, product_details_master d WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.prod_id=d.id and a.entry_form=63 and c.entry_form=63 and c.is_returned!=1 and c.is_rcv_done=0 and a.recv_number='$data' and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $roll_cond ");

	foreach($sql as $row)
	{
		$issue_barcode.=$row[csf("barcode_no")].",";

		//$issue_po_id.=$row[csf("po_breakdown_id")].",";
		if($row[csf("is_sales")] == 1)
		{
			$issue_sales_id_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}

		if($row[csf("booking_without_order")] == 1)
		{
			$non_ord_book_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}
		else
		{
			$issue_po_id_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}

	}

	$issue_po_id_arr = array_filter(array_unique($issue_po_id_arr));
	$issue_po_id = implode(",",$issue_po_id_arr);


	$issue_barcode=chop($issue_barcode,",");

	$barcode_cond="";
	if($issue_barcode!="") $barcode_cond=" and c.barcode_no in($issue_barcode)";

	if($barcode_cond!="")
	{
		$booking_sql=sql_select("select a.booking_id, a.booking_no, c.barcode_no, c.entry_form, a.receive_basis from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 $barcode_cond
			union all
			select c.po_breakdown_id as booking_id, c.booking_no, c.barcode_no, c.entry_form, c.receive_basis from pro_roll_details c where  c.entry_form=58 and c.booking_without_order=1 and c.roll_split_from>0 $barcode_cond");
		$booking_data=array();
		foreach($booking_sql as $row)
		{
			$booking_data[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			$booking_data[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")]; 

			if($row[csf("entry_form")] == 2 || $row[csf("receive_basis")] == 2)
			{
				$program_from_barcode[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")]; 
			}

			$booking_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
			
		}

		$booking_arr = array_filter($booking_arr);


		$sql_split_roll="select c.roll_id,c.barcode_no from pro_roll_split c where c.entry_form=75 and c.status_active=1 and c.is_deleted=0 $barcode_cond";
		$split_sql=sql_select($sql_split_roll);
		$splite_roll_arr=array();
		foreach($split_sql as $inv)
		{
			$splite_roll_arr[$inv[csf('barcode_no')]]=$inv[csf('barcode_no')];  
		}

		$data_array=sql_select("SELECT  a.id, a.company_id, a.recv_number, a.booking_no, a.receive_basis, a.receive_date, a.booking_id, a.booking_no, a.knitting_source, a.knitting_company, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, c.qnty, b.width, b.body_part_id, b.yarn_lot, b.brand_id, b.shift_name, b.floor_id, b.machine_no_id, b.yarn_count, b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.booking_without_order,c.is_sales, c.qc_pass_qnty_pcs 
			FROM inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c 
			WHERE  a.id=b.mst_id and b.id=c.dtls_id and c.entry_form in(2,22,58) and a.entry_form in(2,22,58) and b.trans_id>0 and c.status_active=1 and c.is_deleted=0 $barcode_cond");

		$roll_details_array=array(); 
		foreach($data_array as $row)
		{
			$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
			$roll_details_array[$row[csf("barcode_no")]]['qty_in_pcs']=$row[csf("qc_pass_qnty_pcs")]*1;

			$roll_details_array[$row[csf("barcode_no")]]['barcode_no']=$row[csf("barcode_no")];

			$split_barcode_no=$splite_roll_arr[$row[csf('barcode_no')]];
			if($split_barcode_no==$row[csf("barcode_no")])
			{
				$splite_roll_arr2[$split_barcode_no]['booking_no']=$booking_data[$row[csf("barcode_no")]]["booking_no"]; 
			}
		}
	}

	$issue_po_id=chop($issue_po_id,",");
	$po_id_cond="";
	if($issue_po_id!="") 
	{
		$po_id_cond =" and b.id in($issue_po_id)";
		$po_id_cond2 =" and c.id in($issue_po_id)";
	}

	if($po_id_cond!="")
	{
		//$data_array = sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, b.file_no, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst $po_id_cond");

		$po_sql = sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, b.file_no, b.grouping, a.style_ref_no, b.id as po_id, c.booking_no FROM wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on b.id = c.po_break_down_id and c.booking_type in (1,4) WHERE a.id=b.job_id $po_id_cond group by a.job_no_prefix_num, a.job_no, a.buyer_name, a.insert_date, b.po_number, b.file_no, b.grouping, a.style_ref_no, b.id, c.booking_no");
		$po_details_array=array();
		foreach($po_sql as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
			$po_details_array[$row[csf("po_id")]]['job_no_full']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
		}
	}

	$non_ord_book_arr = array_filter(array_unique($non_ord_book_arr));
	$non_ord_book_id = implode(",",$non_ord_book_arr);

	$order_to_sample_data=array();
	if(count($non_ord_book_arr)>0)
	{
		$order_to_sample_sql=sql_select("select c.barcode_no, c.po_breakdown_id, b.buyer_id, b.id, b.booking_no from wo_non_ord_samp_booking_mst b, pro_roll_details c where b.id=c.po_breakdown_id and c.entry_form in(58,84,110,180) and c.booking_without_order=1 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and b.id in ($non_ord_book_id) $barcode_cond");
		
		foreach($order_to_sample_sql as $row)
		{
			$order_to_sample_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["buyer_id"]=$row[csf("buyer_id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
		}
	}

	$issue_sales_id_arr = array_filter(array_unique($issue_sales_id_arr));

	if(count($issue_sales_id_arr)>0)
	{
		$sales_arr=array();
		$sql_sales=sql_select("select b.id,b.job_no,b.po_job_no,b.within_group,b.buyer_id,b.po_buyer,b.sales_booking_no,b.delivery_date,b.insert_date,b.booking_date,b.booking_without_order from fabric_sales_order_mst b where b.status_active=1 and b.is_deleted=0 and b.id in (".implode(",",$issue_sales_id_arr).")");
		
		foreach ($sql_sales as $sales_row) 
		{					
			$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["po_number"] 			= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["po_job_no"] 			= $sales_row[csf('po_job_no')];
			$sales_arr[$sales_row[csf('id')]]["po_buyer"] 			= $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]["year"] 				= date("Y", strtotime($sales_row[csf("insert_date")]));		
			$sales_arr[$sales_row[csf('id')]]["booking_date"] 		= date("Y", strtotime($sales_row[csf("booking_date")]));		
			$sales_arr[$sales_row[csf('id')]]["booking_without_order"] 	= $sales_row[csf("booking_without_order")];	
			$sales_booking_no .= "'".$sales_row[csf("sales_booking_no")]."',";
		}
		$all_sales_booking_nos = rtrim($sales_booking_no,", ");
		$data_array_info=sql_select("SELECT b.job_no, a.buyer_id, b.booking_no, c.grouping, d.style_ref_no 
		from wo_booking_mst a,wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
		where a.booking_no=b.booking_no and b.po_break_down_id=c.id  and d.id=c.job_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no in($all_sales_booking_nos) 
		group by b.job_no,a.buyer_id,b.booking_no, c.grouping, d.style_ref_no");
		foreach($data_array_info as $row)
		{
			$po_details_arr[$row[csf("booking_no")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_arr[$row[csf("booking_no")]]['file_no']=$row[csf("file_no")];
			$po_details_arr[$row[csf("booking_no")]]['int_ref']=$row[csf("grouping")];
		}
	}

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	if(count($sql)>0)
	{
		$issue_details_arr=array();
		$j=1;
		foreach($sql as $val)
		{	
			$sales_booking_no = $sales_arr[$val[csf('po_breakdown_id')]]["sales_booking_no"];
			$within_group 	= $sales_arr[$val[csf('po_breakdown_id')]]["within_group"]; 
			$is_salesOrder = $val[csf('is_sales')];
			if($is_salesOrder == 1)
			{
				if($within_group==1)
				{
					$order_no = $sales_arr[$val[csf('po_breakdown_id')]]["po_number"];
					$job_no = $sales_arr[$val[csf('po_breakdown_id')]]["po_job_no"];
					$buyer_name = $sales_arr[$val[csf('po_breakdown_id')]]['po_buyer'];
				}
				else 
				{
					$order_no = $sales_arr[$val[csf('po_breakdown_id')]]["po_number"];
					$job_no = $sales_arr[$val[csf('po_breakdown_id')]]["job_no"];
					$buyer_name = $sales_arr[$val[csf('po_breakdown_id')]]['buyer_id'];
					$year = $sales_arr[$val[csf('po_breakdown_id')]]["year"];
					$int_ref='';
				}
			}
			else
			{
				$order_no = $po_details_array[$val[csf('po_breakdown_id')]]['po_number'];
				if($val[csf('booking_without_order')]==1)
				{
					$order_no ="";
				}
			}
			$color='';
			$color_ids=explode(",",$val[csf('color_id')]);
			foreach($color_ids as $color_id)
			{
				if($color_id>0) $color.=$color_arr[$color_id].",";
			}
			$color=chop($color,',');
			?>
			<tr id="tr_<? echo $j; ?>" align="center" valign="middle">
				<td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked" onClick="totalCalculation('<? echo $j; ?>');"> &nbsp; &nbsp;<? echo $j; ?></td>
				<td width="40" id="rollId_<? echo $j; ?>"> <? echo $val[csf('roll_no')]; ?></td>
				<td width="70" id="barcode_<? echo $j; ?>"> <? echo $val[csf('barcode_no')]; ?></td>
				<td width="80" id="systemId_<? echo $j; ?>"> <? echo $body_part[$val[csf('body_part_id')]]; ?></td>
				<td width="120" id="progBookId_<? echo $j; ?>"> <? echo $composition_arr[$val[csf('febric_description_id')]]; ?></td>
				<td width="50" id="basis_<? echo $j; ?>"> <? echo $val[csf('gsm')]; ?></td>
				<td width="50" id="width_<? echo $j; ?>"> <? echo $val[csf('width')]; ?></td>
				<td width="100" id="color_<? echo $j; ?>"><p> <? echo $color; ?></p></td>
				<td width="60" id="rollWgt_<? echo $j; ?>" name="rollWgt[]" align="right"><? echo $val[csf('qnty')];  ?></td>
				<td width="60" id="qtyInPcs_<? echo $j; ?>" name="qtyInPcs[]" align="right"><? echo $val[csf('qc_pass_qnty_pcs')]*1;  ?></td>

				<td width="100" id="job_<? echo $j; ?>"><? echo $val[csf('job_no')] ?></td>
				<td width="65" id="buyer_<? echo $j; ?>"><? echo $buyer_name_array[$val[csf('buyer_id')]]; ?></td>
				<td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $order_no;?></td>
				<td width="90" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $val[csf('wo_no')]; ?></td>
					<input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="0" /> 
					<input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $j; ?>" value="0" />           
					<input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $val[csf('roll_id')]; ?>" />
					<input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $val[csf('roll_no')]; ?>" />
					<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $val[csf('body_part_id')]; ?>"/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $val[csf('color_id')]; ?>" />
					<input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $val[csf('febric_description_id')]; ?>"/>
					<input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $val[csf('prod_id')]; ?>" />
					<input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $val[csf('po_breakdown_id')]; ?>" />
					<input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $val[csf('buyer_id')]; ?>"/> 
					<?
					if($val[csf('booking_without_order')]==1)
					{
						?>
						<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<?  ?>"/>
						<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $val[csf('wo_no')]; ?>"/>
						<?
					}
					else
					{
						?>
						<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $val[csf('job_no')]; ?>"/>
						<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $val[csf('wo_no')]; ?>"/>
						<?
					}
					?>
					<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $j; ?>" value="<? echo $val[csf('booking_without_order')]; ?>"/>
					<input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $val[csf('qnty')]; ?>"/>
                    <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $j; ?>" value="<? echo $val[csf('qc_pass_qnty_pcs')]*1; ?>"/>
					<input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $val[csf('width')]; ?>"/>
					<input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $val[csf('gsm')]; ?>"/>

					<input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $val[csf('dyeing_source')]; ?>"/>
					<input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $val[csf('dyeing_company')]; ?>"/>
					<input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $val[csf('barcode_no')]; ?>"/>
					<input type="hidden" name="isSales[]" id="isSales_<? echo $j; ?>" value="<? echo $val[csf('is_sales')]; ?>"/>
					<input type="hidden" name="issueRollId[]" id="issueRollId_<? echo $j; ?>" value="<? echo $val[csf('issue_roll_id')]; ?>"/>
				</tr>
				<?
				$j++;

				$total_roll_wight +=$val[csf('qnty')];
				$total_roll_qtyInPcs +=$val[csf('qc_pass_qnty_pcs')];
			}
			?>
			<table cellpadding="0" cellspacing="0" width="1410" border="1" id="scanning_tbl" rules="all" class="rpt_table">
				<tfoot>
					<tr>
						<th colspan="8">Total</th>
						<th id="total_calculate_qty_id"><? echo number_format($total_roll_wight,2); ?></th>
						<th id="total_calculate_qtyInPcs_id"><? echo $total_roll_qtyInPcs; ?></th>
                        <th colspan="10"></th>
					</tr>
				</tfoot>
			</table>
			<?
		}
		exit();
	}

if($action=="load_drop_down_knitting_com")
{
	$data = explode("_",$data);
	$company_id=$data[1];

	if($data[0]==1)
	{
		echo create_drop_down( "cbo_knitting_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "--Select Knit Company--", "$company_id", "",1 );
	}
	else if($data[0]==3)
	{	
		echo create_drop_down( "cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type in (21,24,25) and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Knit Company--", 1, "",1 );
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "",1 );
	}
	exit();
}

if($action=="load_php_form_update")
{
	$sql=sql_select("select  a.id,a.recv_number,a.receive_date, a.challan_no, a.issue_id, a.wo_no,a.company_id, a.dyeing_source,a.dyeing_company,a.receive_date, a.process_id,a.gray_issue_challan_no, a.remarks from inv_receive_mas_batchroll a where a.id=$data ");
	//echo $sql;die;
	foreach($sql as $val)
	{
		echo "document.getElementById('txt_issue_challan_no_show').value  = '".($val[csf("gray_issue_challan_no")])."';\n";
		echo "document.getElementById('txt_issue_challan_no').value  	= '".($val[csf("gray_issue_challan_no")])."';\n";
		echo "document.getElementById('txt_issue_id').value  = '".($val[csf("issue_id")])."';\n"; 
		echo "document.getElementById('txt_wo_no').value  = '".($val[csf("wo_no")])."';\n";
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('cbo_process').value  = '".($val[csf("process_id")])."';\n"; 
		echo "document.getElementById('txt_return_date').value  ='".change_date_format($val[csf("receive_date")])."';\n";
		 
		echo "document.getElementById('txt_return_challan').value  = '".($val[csf("challan_no")])."';\n"; 
		echo "document.getElementById('txt_remarks').value  = '".($val[csf("remarks")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("dyeing_source")])."';\n"; 
		echo "load_drop_down( 'requires/grey_fabric_roll_issue_to_process_return_controller', '".$val[csf("dyeing_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n"; 
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("dyeing_company")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n"; 

		exit();
	}
}

if($action=="load_php_form")
{
	//$sql=sql_select("select  a.issue_number, a.challan_no, a.order_id,a.company_id, a.batch_no,a.issue_purpose,a.issue_basis,a.knit_dye_source,a.knit_dye_company,a.issue_number_prefix_num,a.issue_date from inv_receive_mas_batchroll a where  a.entry_form=63 and a.recv_number='$data' ");//and a.knit_dye_source=1

	$sql=sql_select("select a.id, a.wo_no, a.company_id, a.batch_no, a.process_id, a.dyeing_source, a.dyeing_company, a.recv_number, a.receive_date from inv_receive_mas_batchroll a where a.entry_form=63 and recv_number ='$data' and a.status_active=1 and a.is_deleted=0");

	foreach($sql as $val)
	{
		echo "document.getElementById('txt_issue_challan_no_show').value  	= '".($val[csf("recv_number")])."';\n"; 
		echo "document.getElementById('txt_issue_challan_no').value  		= '".($val[csf("recv_number")])."';\n"; 
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('txt_wo_no').value  = '".($val[csf("wo_no")])."';\n"; 
		echo "document.getElementById('txt_issue_id').value  = '".($val[csf("id")])."';\n"; 
		echo "document.getElementById('cbo_process').value  = '".($val[csf("process_id")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("dyeing_source")])."';\n"; 
		echo "load_drop_down( 'requires/grey_fabric_roll_issue_to_process_return_controller', '".$val[csf("dyeing_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n"; 
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("dyeing_company")])."';\n";  
		
		//echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
		exit();
	}
}

if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
		
		function fn_disable_com(str){
			if(str==2){$("#cbo_lc_company_name").attr('disabled','disabled');}
			else{ $('#cbo_lc_company_name').removeAttr("disabled");}
			if(str==1){$("#cbo_company_id").attr('disabled','disabled');}
			else{ $('#cbo_company_id').removeAttr("disabled");}
			
			if(str==1)
			{
				if($('#cbo_lc_company_name').val()==0){$("#cbo_company_id").removeAttr('disabled');}
			} else {
				if($('#cbo_company_id').val()==0){$("#cbo_lc_company_name").removeAttr('disabled');}
			}
			
		}

		function js_set_value(data,id)
		{

			$('#hidden_challan_no').val(data);
			$('#hidden_challan_id').val(id);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center" style="width:910px;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:910px; margin-left:2px">
				<legend>Enter search words</legend>           
				<table cellpadding="0" cellspacing="0" width="880" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<th>Company</th>
						<th>Service Company</th>
						<th>Isuue to process Date</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="180">Please Enter Challan No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_challan_no" id="hidden_challan_no">  
							<input type="hidden" name="hidden_challan_id" id="hidden_challan_id">  
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
						 <?
							echo create_drop_down( "cbo_lc_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fn_disable_com(1)" );
						?>
						</td>
						
						<td align="center">
							<? echo create_drop_down( "cbo_company_id", 130,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"fn_disable_com(2)",0); ?>        
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
						</td>
						<td align="center">	
							<?
							$search_by_arr=array(1=>"Issue to process No.",2=>"WO No.");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
						</td>     
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_lc_company_name').value, 'create_challan_search_list_view', 'search_div', 'grey_fabric_roll_issue_to_process_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$lc_company_id = $data[5];
	
	if($lc_company_id!=0 && $lc_company_id!="" )
	{
		$lc_company_cond = "and a.company_id=$lc_company_id";
	}
	
	if($company_id!=0 && $company_id!="" )
	{
		$working_company_cond = "and a.dyeing_company=$company_id";
	}
	
	if($company_id==0 && $lc_company_id == 0) { echo "Please Select Working Company or Lc Company."; die; }

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
		if($search_by==2) $search_field_cond="and wo_no like '$search_string'";
	}

	if($db_type==0) 
	{
		$year_field=" YEAR(insert_date) as year";
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(insert_date,'YYYY') as year";
	}
	else $year_field="";

	$sql="select a.id, a.recv_number, a.receive_date, a.challan_no, a.company_id,a.dyeing_source,a.dyeing_company,
	$year_field
	from inv_receive_mas_batchroll a
	where a.entry_form=63 and a.status_active=1 $working_company_cond $lc_company_cond  $search_field_cond $date_cond ";
	//and a.knit_dye_source=1
	$result = sql_select($sql);
	foreach ($result as $row)
	{ 
		$issue_id_arr[] = $row[csf("id")];
		$issue_number_arr[] = "'".$row[csf("recv_number")]."'";
		
	}

	$iss_qty_arr=array();
	$challan_barcode=array();
	$inserted_barcode=array();
	if(!empty($issue_number_arr)){
		$issue_cond = (!empty($issue_id_arr))?" and a.id in(".implode(",",$issue_id_arr).")":"";
		$data_array=sql_select("SELECT a.id, a.recv_number, c.barcode_no, c.qnty FROM inv_receive_mas_batchroll a, pro_roll_details c 
			WHERE a.id=c.mst_id and c.entry_form=63 and a.entry_form=63 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_rcv_done=0 and c.is_returned=0 $issue_cond");
		foreach($data_array as $val)
		{
			$challan_barcode[$val[csf('recv_number')]][]=$val[csf('barcode_no')];
			$iss_qty_arr[$val[csf('id')]]+=$val[csf('qnty')];
		}

		$challan_cond = (!empty($issue_number_arr))?" and b.challan_no in(".implode(",",$issue_number_arr).")":"";
		$inserted_roll=sql_select("select b.gray_issue_challan_no,a.barcode_no from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=539 and b.entry_form=539 $challan_cond");
		foreach($inserted_roll as $b_id)
		{
			$inserted_barcode[$b_id[csf('gray_issue_challan_no')]][]=$b_id[csf('barcode_no')];	
		}
	}


	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="110">Company</th>
			<th width="140">Working Company</th>
			<th width="120">System No</th>
			<th width="120">Dyeing Source</th>
			<th width="140">Dyeing Company</th>
			<th width="75">Issue date</th>
			<th>Issue Qty.</th>
		</thead>
	</table>
	<div style="width:910px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{ 
				if(count($challan_barcode[$row[csf('recv_number')]])-count($inserted_barcode[$row[csf('recv_number')]])>0)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$knit_comp="&nbsp;";
					if($row[csf('dyeing_source')]==1) $knit_comp=$company_arr[$row[csf('dyeing_company')]]; 
					else $knit_comp=$supllier_arr[$row[csf('dyeing_company')]];

					$iss_qty=$iss_qty_arr[$row[csf('id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('recv_number')]; ?>','<? echo $row[csf('id')]; ?>');"> 
						<td width="40"><? echo $i; ?></td>
						<td width="110"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
						<td width="140"><p><? echo $knit_comp; ?></p></td>
						<td width="120"><p>&nbsp;<? echo $row[csf('recv_number')]; ?></p></td>
						<td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
						<td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
						<td align="center" width="75"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td align="right"><? echo number_format($iss_qty,2,'.',''); ?>&nbsp;</td>
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

if($action=="update_system_popup")
{
	echo load_html_head_contents("Challan Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data,id,challan,receive_date)
		{
			$('#hidden_receive_no').val(data);
			$('#hidden_update_id').val(id);
			$('#hidden_challan_no').val(challan);
			$('#hidden_rec_date').val(receive_date);
			parent.emailwindow.hide();
		}
		function fnc_check_company()
		{
			var company_id= $('#cbo_company_id').val();
			var dyeing_company= $('#cbo_dyeing_company').val();
			if(company_id==0 && dyeing_company==0)
			{
				if (form_validation('cbo_company_id','Company')==false)
				{
					return;
				}
			}

		}
	</script>

</head>

<body>
	<div align="center" style="width:980px;" >
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:980px; margin-left:2px;">
				<legend>Receive Number Popup</legend>           
				<table cellpadding="0" cellspacing="0" width="970" border="1" rules="all" class="rpt_table" align="center">
					<thead>
						<th>Company</th>
						<th>Dyeing Source</th>
						<th>Dyeing Company</th>
						<th>Receive No</th>
						<th width="250">Receive Date</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_receive_no" id="hidden_receive_no">  
							<input type="hidden" name="hidden_update_id" id="hidden_update_id">
							<input type="hidden" name="hidden_challan_no" id="hidden_challan_no">    
							<input type="hidden" name="hidden_rec_date" id="hidden_rec_date">    
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down( "cbo_company_id", 170,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
						</td>
						<td align="center">
							<?  echo create_drop_down( "cbo_dyeing_source", 100, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'grey_fabric_roll_issue_to_process_return_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_knit_com', 'dyeing_company_td' );","","1,3" ); ?>        
						</td>
						<td align="center" id="dyeing_company_td">
							<? echo create_drop_down( "cbo_dyeing_company", 120, $blank_array,"", 1, "-- Select --", $selected, "","","" ); ?>        
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes"  name="txt_receive_number" id="txt_receive_number" />
						</td>
						<td align="center">	
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" readonly>
						</td>     

						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_check_company();show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_receive_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_dyeing_source').value+'_'+document.getElementById('cbo_dyeing_company').value, 'create_update_search_list_view', 'search_div', 'grey_fabric_roll_issue_to_process_return_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=="create_update_search_list_view")
{

	$data = explode("_",$data);
	//$search_string="%".trim($data[0]);
	$receive_number=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[0];
	$year_id =$data[4];
	$source_id =str_replace("'","",$data[5]);
	$dyeing_company_id =str_replace("'","",$data[6]);

	if($company_id==0 && $dyeing_company_id==0)
	{
		echo "Select any company";	die;
	}

	if($dyeing_company_id!=0)
	{
		$knit_company_con="and a.dyeing_company=$dyeing_company_id";
	}
	else
	{
		$knit_company_con="";	
	}
	//echo $knit_company_con;
	if($company_id!=0)
	{ 
		$company_condi="and a.company_id=$company_id";
	}
	else
	{
		$company_condi=""; 
	}


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

	if(trim($year_id)!=0) 
	{
		if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$year_id";
		else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$year_id";
		else $year_cond="";
	}
	else $year_cond="";

	$search_field_cond="";
	if(trim($receive_number)!="")
	{
		$receiv_cond="and a.recv_number like '%$receive_number%' ";
	}

	if($db_type==0) 
	{
		$year_field=" YEAR(a.insert_date) as year";
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";

	}
else $year_field="";//defined Later

//$sql="select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date, $year_field from  inv_receive_mas_batchroll a where a.entry_form=62 and a.is_deleted=0  and a.status_active=1 $company_condi $receiv_cond $date_cond $year_cond $knit_company_con order by a.recv_number_prefix_num, a.receive_date";
//$sql="select a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date, $year_field,sum(b.qc_pass_qnty) as qc_pass_qnty from inv_receive_mas_batchroll a,pro_roll_details b where a.id=b.mst_id and a.entry_form=62 and b.entry_form=62 and a.is_deleted=0 and a.status_active=1 $company_condi $receiv_cond $date_cond $year_cond $knit_company_con group by a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date,a.insert_date order by a.recv_number_prefix_num, a.receive_date";
$sql="select a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.gray_issue_challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date, $year_field,sum(b.qc_pass_qnty) as qc_pass_qnty from inv_receive_mas_batchroll a,pro_roll_details b,pro_grey_batch_dtls c where a.id=b.mst_id and a.id=c.mst_id and c.id=b.dtls_id and a.entry_form=539 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1 $company_condi $receiv_cond $date_cond $year_cond $knit_company_con group by a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.gray_issue_challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date,a.insert_date order by a.recv_number_prefix_num, a.receive_date";

$result = sql_select($sql);

//$qc_pass_qnty=return_field_value("sum(qc_pass_qnty) as qc_pass_qnty","pro_roll_details","mst_id='".$row[csf('id')]."' and entry_form=62 and status_active=1 and is_deleted=0","qc_pass_qnty");

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

?>
<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table">
	<thead>
		<th width="40">SL</th>
		<th width="140">Company</th>
		<th width="80">Receive No</th>
		<th width="70">Year</th>
		<th width="120">Dyeing Source</th>
		<th width="140">Dyeing Company</th>
		<th width="130">Receive date</th>
		<th width="100">Recv Qty</th>
		<th width="">Issue Challan</th>
	</thead>
</table>
<div style="width:970px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="tbl_list_search">  
		<?
		$i=1;
		foreach ($result as $row)
		{  
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	

			$knit_comp="&nbsp;";
			if($row[csf('dyeing_source')]==1)
				$knit_comp=$company_arr[$row[csf('dyeing_company')]]; 
			else
				$knit_comp=$supllier_arr[$row[csf('dyeing_company')]];

			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('recv_number')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('gray_issue_challan_no')]; ?>','<? echo change_date_format($row[csf('receive_date')]); ?>');"> 
				<td width="40"><? echo $i; ?></td>
				<td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
				<td width="80"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
				<td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
				<td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
				<td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
				<td width="130" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
				<td width="100" align="right"><p><? echo number_format($row[csf('qc_pass_qnty')],2); ?>&nbsp;</p></td>
				<td width="" align="right"><p><? echo $row[csf('gray_issue_challan_no')]; ?>&nbsp;</p></td>
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

//load drop down knitting company
if ($action=="load_drop_down_knit_com")
{
	$exDataArr = explode("**",$data);	
	$knit_source=$exDataArr[0];
	$company=$exDataArr[1];
		//$issuePurpose=$exDataArr[2];

	if($company=="" || $company==0) $company_cond2 = ""; else $company_cond2 = "and c.tag_company=$company";

	if($knit_source==0 || $knit_source=="")
	{
		echo create_drop_down( "cbo_dyeing_company", 120, $blank_array,"", 1, "-- Select --", 0, "",0 );	
	}
	else if($knit_source==1)
	{
		echo create_drop_down( "cbo_dyeing_company", 120, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "-- Select --", $company, "" );
	}
	else if($knit_source==3)
	{
		echo create_drop_down( "cbo_dyeing_company", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and b.party_type in(21,24,25,26) and a.status_active=1 $company_cond2 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	}
	else
	{
		echo create_drop_down( "cbo_dyeing_company", 170, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and b.supplier_id=c.supplier_id and a.status_active=1 $company_cond2 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	}
	exit();	
}

if($action=="grey_delivery_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}

	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ","id","store_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	
	$machine_arr=array();
	$mc_sql=sql_select( "select id, machine_no, dia_width from lib_machine_name where status_active=1");
	foreach($mc_sql as $row)
	{
		$machine_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

	$sql_update=sql_select("SELECT b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id, b.febric_description_id,b.gsm,b.width,b.roll_wgt as roll_wgt_curr,b.roll_id , b.buyer_id,b.order_id,b.color_id,a.challan_no,c.id as update_roll_id, c.po_breakdown_id,c.barcode_no, c.qnty as roll_wgt,c.roll_no,c.is_sales,c.booking_without_order from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62 and a.id=$update_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

	foreach($sql_update as $row)
	{
		$is_sales = $row[csf("is_sales")];
		if($is_sales == 1){
			$sales_order_id_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}else{
			$po_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}
		$barcode_arr[] = $row[csf("barcode_no")];
		$barcode_ref_arr[$row[csf("barcode_no")]]['booking_without_order'] = $row[csf("booking_without_order")];
	}

	if(!empty($barcode_arr)){
		$data_array=sql_select("SELECT a.id,a.receive_basis, a.booking_no,a.booking_id, c.barcode_no,b.stitch_length,b.yarn_count,b.yarn_lot,b.brand_id,
			b.machine_no_id,b.color_range_id,a.entry_form FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) and c.barcode_no in(".implode(",",$barcode_arr).") order by c.id desc");
		$roll_details_array=array(); $barcode_array=array(); 
		foreach($data_array as $row)
		{
			$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['stitch_length']=$row[csf("stitch_length")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
			$roll_details_array[$row[csf("barcode_no")]]['color_range_id']=$row[csf("color_range_id")];
			$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
			$roll_details_array[$row[csf("barcode_no")]]['yarn_lot']=$row[csf("yarn_lot")];
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			$roll_details_array[$row[csf("barcode_no")]]['yarn_count']=$row[csf("yarn_count")];
			$roll_details_array[$row[csf("barcode_no")]]['entry_form']=$row[csf("entry_form")];

			if($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 2)
			{
				$booking_no_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
			}
			
		}
	}
	
	$job_array=array();

	if(!empty($sales_order_id_arr)){
		$salesorder_cond = (!empty($sales_order_id_arr))?" and id in(".implode(",",$sales_order_id_arr).")":"";
		$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0 $salesorder_cond");
		$sales_arr = array();
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("id")]]["po_number"] 			= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("id")]]["sales_booking_no"] 	= $sales_row[csf("sales_booking_no")];
			$sales_arr[$sales_row[csf("id")]]["within_group"] 		= $sales_row[csf("within_group")];
			$booking_arr[] = "'".$sales_row[csf("sales_booking_no")]."'";
		}
	}

	$job_arr=array();
	if(!empty($po_arr)){
		$order_cond = " and c.id in(".implode(",",$po_arr).")";
	}else{
		$order_cond = " and a.booking_no in(".implode(",",$booking_arr).")";
	}
	$sql_job=sql_select("SELECT a.booking_no,a.po_break_down_id,b.job_no_prefix_num, b.job_no, b.buyer_name, b.insert_date,c.file_no,c.po_number, c.grouping FROM wo_booking_dtls a, wo_po_details_master b, wo_po_break_down c WHERE a.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $order_cond");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 	= $job_row[csf('job_no_prefix_num')];
		$job_arr[$job_row[csf('booking_no')]]["job_no_full"] 	= $job_row[csf('job_no')];
		$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 	= $job_row[csf('buyer_name')];
		$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 		= $job_row[csf('buyer_name')];
		$job_arr[$job_row[csf('booking_no')]]["year"] 			= date("Y",strtotime($job_row[csf("insert_date")]));
		$job_arr[$job_row[csf('booking_no')]]["file_no"] 		= $job_row[csf('file_no')];
		$job_arr[$job_row[csf('booking_no')]]["int_ref"] 		= $job_row[csf('grouping')];

		$job_arr[$job_row[csf('po_break_down_id')]]["job_no_mst"] 	= $job_row[csf('job_no_prefix_num')];
		$job_arr[$job_row[csf('po_break_down_id')]]["job_no_full"] 	= $job_row[csf('job_no')];
		$job_arr[$job_row[csf('po_break_down_id')]]["buyer_name"] 	= $job_row[csf('buyer_name')];
		$job_arr[$job_row[csf('po_break_down_id')]]["buyer_id"] 	= $job_row[csf('buyer_name')];
		$job_arr[$job_row[csf('po_break_down_id')]]["year"] 		= date("Y",strtotime($job_row[csf("insert_date")]));
		$job_arr[$job_row[csf('po_break_down_id')]]["file_no"] 		= $job_row[csf('file_no')];
		$job_arr[$job_row[csf('po_break_down_id')]]["int_ref"] 		= $job_row[csf('grouping')];
		$job_arr[$job_row[csf('po_break_down_id')]]["po_no"] 		= $job_row[csf('po_number')];
	}

	$booking_array=array();
	if(!empty($booking_no_arr))
	{
		$booking_sql="select b.id,a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in (".implode(",",$booking_no_arr).")";
		$booking_result=sql_select($booking_sql);
		foreach($booking_result as $row)
		{
			$booking_array[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		}
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


	$order_to_sample_sql=sql_select("select c.barcode_no, c.po_breakdown_id, b.buyer_id, b.id, b.booking_no from wo_non_ord_samp_booking_mst b, pro_roll_details c where b.id=c.po_breakdown_id and c.entry_form in(110,180) and c.booking_without_order=1  and c.status_active=1 and c.is_deleted=0 and c.barcode_no in (".implode(",",$barcode_arr).") and c.re_transfer =0");

	$order_to_sample_data=array();
	foreach($order_to_sample_sql as $row)
	{
		$order_to_sample_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$order_to_sample_data[$row[csf("barcode_no")]]["buyer_id"]=$row[csf("buyer_id")];
		$order_to_sample_data[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
		$order_to_sample_data[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
	}
	
	?>
	<div style="width:1110px;">
		<table width="1110" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>			
			<tr>
				<td align="center" style="font-size:14px"><strong><u>Grey Roll Receive By Batch</u></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Receive No :<? echo $txt_challan_no; ?></u></strong></td>
			</tr>
		</table> 
		<br>
		<?
		$sql_data= sql_select("select  a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date, a.challan_no, a.recv_number,a.company_id,
			a.batch_no, a.receive_basis,a.dyeing_source,a.dyeing_company,a.receive_date
			from  inv_receive_mas_batchroll a
			where  a.id=$update_id");		
			?>
			<table width="1310" cellspacing="0" align="center" border="0">
				<tr>
					<td style="font-size:18px; font-weight:bold;" width="150"> Challan No</td>
					<td width="200" style="font-size:18px; font-weight:bold;">:&nbsp;<? echo $sql_data[0][csf('challan_no')]; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Company </td>
					<td width="200"  align=""><? echo $company_array[$company]['name']; ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Dyeing Source</td>
					<td width="200">:&nbsp;<? echo $knitting_source[$sql_data[0][csf('dyeing_source')]]; ?></td>
				</tr>
				<tr>

					<td style="font-size:16px; font-weight:bold;" width="150">Dyeing Company </td>
					<td width="200">:&nbsp;
						<?
						if($sql_data[0][csf('dyeing_source')]==1) echo  $company_array[$sql_data[0][csf('dyeing_company')]]['name'];
						else  echo $supplier_arr[$sql_data[0][csf('dyeing_company')]];
						?>
					</td>
					<td style="font-size:16px; font-weight:bold;" width="150">Receive Date </td>
					<td width="200">:&nbsp;<? echo change_date_format($sql_data[0][csf('receive_date')]); ?></td>
					<td style="font-size:16px; font-weight:bold;" width="150">Purpose</td>
					<td width="200" id="" align=""><? echo  $yarn_issue_purpose[$sql_data[0][csf('receive_basis')]];?></td>
				</tr>
				<tr>
					<td style="font-size:16px; font-weight:bold;" width="150">Batch No</td>
					<td width="200">:&nbsp;  <? echo $sql_data[0][csf('batch_no')]; ?>

					</td>

					<td width="" id="barcode_img_id"  colspan="2"></td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1350" class="rpt_table" >
				<thead>
					<tr style="font-size:13px">
						<th width="30">SL</th>
						<th width="80">Order /File</th>
						<th width="100">Ref No</th>
						<th width="60">Buyer/Job</th>
						<th width="90">Program/ Booking No</th>
						<th width="90">Production Basis</th>
						<th width="70">Knitting Company</th>
						<th width="50">Yarn Count</th>
						<th width="70">Yarn Brand</th>
						<th width="60">Lot No</th>
						<th width="70">Fab Color</th>
						<th width="70">Color Range</th>
						<th width="120">Fabric Type</th>
						<th width="50">Stich</th>
						<th width="50">Fin GSM</th>
						<th width="40">Fab. Dia</th>
						<th width="40">MC No/ Dia</th>
						<th width="40">Roll No</th>
						<th width="60">Barcode</th>
						<th>QC Pass Qty</th>
					</tr>
				</thead>
				<?
				$i=1; 
				$tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
				foreach($sql_update as $row)
				{
					$is_sales = $row[csf("is_sales")];
					$knit_company="&nbsp;";
					if($row[csf("knitting_source")]==1)
					{
						$knit_company=$company_array[$row[csf("knitting_company")]]['shortname'];
					}
					else if($row[csf("knitting_source")]==3)
					{
						$knit_company=$supplier_arr[$row[csf("knitting_company")]];
					}
					
					$count='';
					$yarn_count=explode(",",$roll_details_array[$row[csf("barcode_no")]]['yarn_count']);
					foreach($yarn_count as $count_id)
					{
						if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
					}

					$mc_no_dia="";
					$mc_no_dia='N :'.$machine_arr[$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']]['no'].'<br> D :'.$machine_arr[$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']]['dia'];

					if($is_sales == 1)
					{
						$without_order=$row[csf('booking_without_order')];
						$within_group = $sales_arr[$row[csf("po_breakdown_id")]]["within_group"];
						$booking_no = $sales_arr[$row[csf("po_breakdown_id")]]["sales_booking_no"];
						$order_file='O :'.$sales_arr[$row[csf("po_breakdown_id")]]["po_number"].'<br> F :';
						if($without_order == 2)
						{
							$buyer_id=return_field_value("buyer_id","wo_non_ord_samp_booking_mst","booking_no='$booking_no'");
							$order_file="";
							$order_file='O :'.'<br> F :';	
							$buyer_job="";
							$buyer_job='B :'.$buyer_array[$buyer_id].'<br> J :';
							$int_ref='';
						}
						else
						{
							if($within_group == 1)
							{
								$buyer_job='B :'.$buyer_array[$job_arr[$booking_no]["buyer_name"]].'<br> J :'.$job_arr[$booking_no]["job_no_full"];	
								$int_ref = $job_arr[$booking_no]["int_ref"];
							}
							else
							{
								$buyer_job='B :'.$buyer_array[$job_arr[$row[csf("po_breakdown_id")]]["buyer_name"]].'<br> J :'.$job_arr[$row[csf("po_breakdown_id")]]["job_no_full"];
								$int_ref='';
							}
						}						
					}
					else
					{
						if($barcode_ref_arr[$row[csf("barcode_no")]]['booking_without_order'] ==0 || $barcode_ref_arr[$row[csf("barcode_no")]]['booking_without_order'] =='')
						{
							$order_file="";
							$order_file='O :'.$job_arr[$row[csf('po_breakdown_id')]]['po_no'].'<br> F :'.$job_arr[$row[csf('po_breakdown_id')]]['file_no'];
							$buyer_job="";
							$buyer_job='B :'.$buyer_array[$job_arr[$row[csf("po_breakdown_id")]]["buyer_name"]].'<br> J :'.$job_arr[$row[csf("po_breakdown_id")]]["job_no_full"];
							$int_ref=$job_arr[$row[csf('po_breakdown_id')]]['int_ref'];
						}
					}


					

					if($roll_details_array[$row[csf("barcode_no")]]['receive_basis']== 2 && $roll_details_array[$row[csf("barcode_no")]]['entry_form'] ==2)
					{
						$program_no = $roll_details_array[$row[csf("barcode_no")]]['booking_no'];
						if($barcode_ref_arr[$row[csf("barcode_no")]]['booking_without_order'] == 1)
						{
							$booking_number = $order_to_sample_data[$row[csf("barcode_no")]]["booking_no"];
						}
						else
						{
							$booking_number = $booking_array[$program_no]['booking_no'];
						}


						$book_program_str =  "P: ".$program_no."<br>B: ".$booking_number; 
					}
					else if($roll_details_array[$row[csf("barcode_no")]]['receive_basis']== 1 && $roll_details_array[$row[csf("barcode_no")]]['entry_form'] ==2)
					{
						if($barcode_ref_arr[$row[csf("barcode_no")]]['booking_without_order'] == 1)
						{
							if($order_to_sample_data[$row[csf("barcode_no")]]["booking_no"] =="")
							{
								$booking_number = $roll_details_array[$row[csf("barcode_no")]]['booking_no'];
							}
							else
							{
								$booking_number = $order_to_sample_data[$row[csf("barcode_no")]]["booking_no"];
							}
						}
						else
						{
							$booking_number = $roll_details_array[$row[csf("barcode_no")]]['booking_no'];
						}

						$book_program_str = "B: ".$booking_number;
					}

					?>
					<tr style="font-size:13px">
						<td width="30"><? echo $i; ?></td>
						<td width="80" style="word-break:break-all;" title="<? echo "==".$row[csf('po_breakdown_id')];?>"><? echo $order_file; ?></td>
						<td width="100" style="word-break:break-all;"><? echo $int_ref; ?></td>
						<td width="60"><? echo $buyer_job; ?></td>
						<td width="90" style="word-break:break-all;">
							<? 
								echo $book_program_str;							
							?>								
						</td>
						<td width="90"><? echo $receive_basis[$roll_details_array[$row[csf("barcode_no")]]['receive_basis']]; ?></td>
						<td width="70"><? echo $knit_company; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $brand_details[$roll_details_array[$row[csf("barcode_no")]]['brand_id']]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['yarn_lot']; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $color_range[$roll_details_array[$row[csf("barcode_no")]]['color_range_id']]; ?></td>
						<td width="120" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $roll_details_array[$row[csf("barcode_no")]]['stitch_length']; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center"><? echo $mc_no_dia; ?></td>
						<td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
						<td width="60" align="center"><? echo $row[csf("barcode_no")]; ?></td>
						<td align="right"><? echo number_format($row[csf('roll_wgt')],2); ?></td>
					</tr>
					<?
					$tot_qty+=$row[csf('roll_wgt')];
					$i++;
				}
				?>
				<tr style="font-size:13px"> 
					<td align="right" colspan="19"><strong>Total</strong></td>
					<td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
				</tr>
			</table>
		</div>
		<? echo signature_table(71, $company, "1210px"); ?>
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
				barHeight: 40,
				moduleSize:5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if($action=="fabric_details_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	
	$location_arr=return_library_array( "select id, location_name from  lib_location",'id','location_name');
	$store_arr = return_library_array("select a.id, a.store_name  from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company and b.category_type=13 and a.status_active=1 and a.is_deleted=0 ","id","store_name");
	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_arr=array();
	$mc_sql=sql_select( "select id, machine_no, dia_width from lib_machine_name");
	foreach($mc_sql as $row)
	{
		$machine_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
	}
	
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$job_array=array();
	$job_sql="select a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
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
	<div style="width:1260px;">
		<table width="1250" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:18px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Receive No <? echo $txt_challan_no; ?></u></strong></td>
			</tr>
		</table> 

		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
			<thead>
				<tr style="font-size:13px">
					<th width="30">SL</th>
					<th width="80">Order/ File</th>
					<th width="60">Buyer /Job</th>
					<th width="90">Program /Booking No</th>
					<th width="90">Prod. Basis</th>
					<th width="70">Knit. Company</th>
					<th width="50">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="60">Lot No</th>
					<th width="70">Fab Color</th>
					<th width="70">Color Range</th>
					<th width="120">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40" >MC No/ Dia</th>
					<th width="40">Roll No</th>
					<th width="60">Bar Code</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$data_array=sql_select("SELECT   a.id,a.receive_basis, a.booking_no,c.barcode_no,b.stitch_length,b.yarn_count,b.yarn_lot,b.brand_id,
				b.machine_no_id,b.color_range_id
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2
				and c.entry_form=2  and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) order by c.id desc");
			$roll_details_array=array(); $barcode_array=array(); 
			foreach($data_array as $row)
			{
				$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
				$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
				$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
				$roll_details_array[$row[csf("barcode_no")]]['stitch_length']=$row[csf("stitch_length")];
				$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
				$roll_details_array[$row[csf("barcode_no")]]['color_range_id']=$row[csf("color_range_id")];
				$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$row[csf("machine_no_id")];
				$roll_details_array[$row[csf("barcode_no")]]['yarn_lot']=$row[csf("yarn_lot")];
				$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
				$roll_details_array[$row[csf("barcode_no")]]['yarn_count']=$row[csf("yarn_count")];
				
			}
			$i=1; $tot_qty=0; $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
			
			
			$sql_update=sql_select("select  b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id,
				b.febric_description_id,b.gsm,b.width,b.roll_wgt,b.roll_id,b.buyer_id,b.order_id,b.color_id,a.challan_no,c.roll_no,c.barcode_no,c.booking_without_order as without_order
				from inv_receive_mas_batchroll a,pro_grey_batch_dtls b,pro_roll_details c
				where a.id=b.mst_id and b.id=c.dtls_id and a.id=$update_id and a.entry_form=62 and c.mst_id=$update_id and c.entry_form=62 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0   and c.status_active=1 and c.is_deleted=0 order by c.roll_no");

			foreach($sql_update as $row)
			{
				$knit_company="&nbsp;";
				if($row[csf("knitting_source")]==1)
				{
					$knit_company=$company_array[$row[csf("knitting_company")]]['shortname'];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$knit_company=$supplier_arr[$row[csf("knitting_company")]];
				}
				
				$count='';
				$yarn_count=explode(",",$roll_details_array[$row[csf("barcode_no")]]['yarn_count']);
				foreach($yarn_count as $count_id)
				{
					if($count=='') $count=$yarn_count_details[$count_id]; else $count.=",".$yarn_count_details[$count_id];
				}
				
				$mc_no_dia="";
				$mc_no_dia='N :'.$machine_arr[$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']]['no'].'<br> D :'.$machine_arr[$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']]['dia'];
				
				$without_order=$row[csf("without_order")];
				if($without_order==1)
				{
					$buyer_id=return_field_value("buyer_id","wo_non_ord_samp_booking_mst","booking_no='".$roll_details_array[$row[csf("barcode_no")]]['booking_no']."'");
					$order_file="";
					$order_file='O :'.'<br> F :';	
					$buyer_job="";
					$buyer_job='B :'.$buyer_array[$buyer_id].'<br> J :';		
				}
				else
				{
					$order_file="";
					$order_file='O :'.$job_array[$row[csf('order_id')]]['po'].'<br> F :'.$job_array[$row[csf('order_id')]]['file'];
					$buyer_job="";
					$buyer_job='B :'.$buyer_array[$job_array[$row[csf('order_id')]]['buyer']].'<br> J :'.$job_array[$row[csf('order_id')]]['job'];			
				}
				?>
				<tr style="font-size:13px">
					<td width="30"><? echo $i; ?></td>
					<td width="80" style="word-break:break-all;"><? echo $order_file; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $buyer_job; ?></td>
					<td width="90" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['booking_no']; ?></td>
					<td width="90"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
					<td width="70"><? echo $knit_company; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $brand_details[$roll_details_array[$row[csf("barcode_no")]]['brand_id']]; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $roll_details_array[$row[csf("barcode_no")]]['yarn_lot']; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $color_range[$roll_details_array[$row[csf("barcode_no")]]['color_range_id']]; ?></td>
					<td width="120" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $roll_details_array[$row[csf("barcode_no")]]['stitch_length']; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $mc_no_dia; ?></td>
					<td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
					<td width="60" align="center"><? echo $row[csf("barcode_no")]; ?></td>
					<td align="right"><? echo number_format($row[csf('roll_wgt')],2); ?></td>
				</tr>
				<?
				$tot_qty+=$row[csf('roll_wgt')];
				$i++;
			}
			?>
			<tr> 
				<td align="right" colspan="18"><strong>Total</strong></td>
				<td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>

		</table>
	</div>
	<? echo signature_table(71, $company, "1210px"); 
	exit();
}
?>

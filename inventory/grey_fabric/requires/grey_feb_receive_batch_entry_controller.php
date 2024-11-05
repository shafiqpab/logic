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

		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GRRB', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from  inv_receive_mas_batchroll where company_id=$cbo_company_id and entry_form=62 and $year_cond=".date('Y',time())." order by id desc ", "recv_number_prefix","recv_number_prefix_num"));
		//$id=return_next_id( "id", "inv_receive_mas_batchroll", 1 ) ;

		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data preparing for
		| $data_array
		|--------------------------------------------------------------------------
		|
		*/
		$id = return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'GRRB',62,date("Y",time()),13 ));
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',62,".$txt_delivery_date.",".$cbo_company_id.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$txt_issue_challan_no.",'".str_replace($txt_batch_no)."',".$cbo_basis.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

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
			$issue_challan = return_library_array("select a.barcode_no,a.barcode_no from pro_roll_details a where a.status_active=1 and a.is_deleted=0  and a.entry_form=61 and a.is_returned=0 and a.barcode_no in ($chk_rcv_barcode)","barcode_no","barcode_no");

			$rcved_challan = sql_select("select b.recv_number,a.barcode_no from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=62 and b.entry_form=62 and a.barcode_no in ($chk_rcv_barcode)");
			if($rcved_challan[0][csf("recv_number")])
			{
				echo "20**Selected Barcode Found in Receive By Batch.\nBarcode =" .$rcved_challan[0][csf("barcode_no")] ."\nReceive No=".$rcved_challan[0][csf("recv_number")];
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
				$data_array_roll.="(".$id_roll.",".$id.",".$dtls_id.",'".$$orderId."',62,'".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rollwgt."','".$$rollwgt."','".$$bookWithoutOrder."','".$$bookingNo."','".$$isSales."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.")";

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
				$data_array_dtls.="(".$dtls_id.",".$id.",".$$rollId.",'".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."','".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".trim($$rolldia)."','".$$rollwgt."','".$$buyerId."','".$$job_no."','".$$orderId."','".$$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.")";
				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$id_roll.",";
				//$id_roll=$id_roll+1;
				//$dtls_id = $dtls_id+1;
			}
		}
		//echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll";oci_rollback($con);die;

		/*
		|--------------------------------------------------------------------------
		| inv_receive_mas_batchroll
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="id,recv_number_prefix,recv_number_prefix_num,recv_number,entry_form,receive_date,company_id,dyeing_source,dyeing_company,challan_no,batch_no,receive_basis,inserted_by,insert_date";
		$rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_id,roll_no,barcode_no,qnty,qc_pass_qnty,booking_without_order,booking_no,is_sales,inserted_by,insert_date,qc_pass_qnty_pcs";
		$rID2=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);

		/*
		|--------------------------------------------------------------------------
		| pro_grey_batch_dtls
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls="id, mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,gsm,width,roll_wgt,buyer_id,job_no,order_id,color_id,inserted_by,insert_date,qty_in_pcs";
		$rID3=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,1);

		//echo "10**$rID && $rID2 && $rID3";
		//oci_rollback($con); die;

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
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
					if ($splitting_roll_check == "") $splitting_roll_check = $$barcodeNo; else  $splitting_roll_check .= ",".$$barcodeNo;
				}
			}
		}

		if ($chk_rcv_barcode != "")
		{
			$rcved_challan = sql_select("select b.recv_number,a.barcode_no from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=62 and b.entry_form=62 and a.barcode_no in ($chk_rcv_barcode)");


			if($rcved_challan[0][csf("recv_number")])
			{
				echo "20**Selected Barcode Found in Receive By Batch.\nBarcode =" .$rcved_challan[0][csf("barcode_no")] ."\nReceive No=".$rcved_challan[0][csf("recv_number")];
				disconnect($con);
				die;
			}
		}

		if ($splitting_roll_check != "") // splitting roll check
		{
			$sql_split_roll="select barcode_no from pro_roll_details where entry_form=62 and status_active=1 and is_deleted=0 and roll_split_from > 0 and barcode_no in ($splitting_roll_check)";
			$split_sql=sql_select($sql_split_roll);
			$splite_roll_arr=array();
			foreach($split_sql as $inv)
			{
				$splite_roll_arr[$inv[csf('barcode_no')]]=$inv[csf('barcode_no')];
			}

			$sql_split_roll="select barcode_no from pro_roll_split  where entry_form=75 and status_active=1 and is_deleted=0 and barcode_no in ($splitting_roll_check)";
			$split_sql=sql_select($sql_split_roll);
			foreach($split_sql as $inv)
			{
				$splite_roll_arr[$inv[csf('barcode_no')]]=$inv[csf('barcode_no')];
			}

			if(!empty($splite_roll_arr))
			{
				echo "20**Update Restricted. Because Following Barcode Are Inserted in Splitting Page.\nBarcode =" .implode(",", $splite_roll_arr);
				disconnect($con);
				die;
			}

			//check Batch creation entry
			$batch_sql = sql_select("select a.batch_no,b.barcode_no from  pro_batch_create_mst a, pro_roll_details b where a.id=b.mst_id and b.entry_form=64 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.barcode_no in ($splitting_roll_check)");

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
		}

		$barcodeNos='';
		for($j=1;$j<=$tot_row;$j++)
		{
			$activeId="activeId_".$j;
			$updateDetailsId="updateDetailsId_".$j;
			$updateRollId="updateRollId_".$j;

			if($$activeId==0 )
			{
				if($$updateDetailsId!="")
				{
					$updateDetailsId_arr[]=$$updateDetailsId;
					$data_array_delete[$$updateDetailsId] = explode("*", ("0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
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
					$data_array_roll.="(".$id_roll.",".$update_id.",".$dtls_id.",'".$$orderId."',62,'".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rollwgt."','".$$rollwgt."','".$$bookWithoutOrder."','".$$bookingNo."','".$$isSales."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.")";

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
					$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$rollId.",'".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."','".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rolldia."','".$$rollwgt."','".$$buyerId."','".$$job_no."','".$$orderId."','".$$colorId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$hiddenQtyInPcs.")";
					$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$id_roll.",";
					//$id_roll=$id_roll+1;
					//$dtls_id = $dtls_id+1;
				}
			}
		}

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
		$statusChange=true;
		$field_array="receive_date*batch_no*updated_by*update_date";
		$data_array="".$txt_delivery_date."*'".str_replace("'","",$txt_batch_no)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
		if($rID)
			$flag=1;
		else
			$flag=0;

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
			$rID3=execute_query("update pro_roll_details set status_active=0, is_deleted=1, updated_by=". $_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where dtls_id in(".implode(",",$updateDetailsId_arr).") and entry_form=62");
			if($flag==1)
			{
				if($rID3)
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
			$field_array_roll="id,mst_id,dtls_id,po_breakdown_id,entry_form,roll_id,roll_no,barcode_no,qnty,qc_pass_qnty,booking_without_order,booking_no,is_sales,inserted_by,insert_date,qc_pass_qnty_pcs";
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
			$field_array_insert="id,mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,gsm,width,roll_wgt,buyer_id,job_no,order_id,color_id,inserted_by,insert_date,qty_in_pcs";
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
		//echo "10**".$flag;die;

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

			if(!empty($splite_roll_arr))
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
	$sql_update=sql_select("SELECT a.company_id, b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id,b.width_dia_type,	b.febric_description_id,b.gsm,b.width,b.roll_wgt as roll_wgt_curr,b.roll_id,b.buyer_id,b.order_id,b.color_id,a.challan_no,c.id as update_roll_id, c.barcode_no, c.qnty as roll_wgt,c.roll_no,c.is_sales,c.booking_without_order,c.qc_pass_qnty_pcs
	from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62 and a.id=$data[0] and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");//and $null_cond(c.roll_split_from,0)<=0

	$barcode_arr=$order_id_arr=$color_id_arr=$gsm_id_arr=$sales_order_id_arr=$sales_color_id_arr=$sales_gsm_id_arr=$all_po_arr=array();
	foreach($sql_update as $val)
	{
		$company_id = $val[csf("company_id")];
		$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		if($val[csf("is_sales")] == 1)
		{
			$sales_order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
			$sales_color_id_arr[$val[csf("color_id")]] = $val[csf("color_id")];
			$sales_gsm_id_arr[$val[csf("gsm")]] = $val[csf("gsm")];
			// $order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
		}
		else
		{
			$order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
			$color_id_arr[$val[csf("color_id")]] = $val[csf("color_id")];
			$gsm_id_arr[$val[csf("gsm")]] = "'".$val[csf("gsm")]."'";
		}
		$all_po_arr[$val[csf("order_id")]] = $val[csf("order_id")];
	}


	//unsaved sql == >>
	$inserted_roll=sql_select("select c.barcode_no from pro_grey_batch_dtls a,inv_receive_mas_batchroll b, pro_roll_details c  where a.mst_id=b.id and a.id=c.dtls_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=62 and c.entry_form=62 and b.challan_no='$data[1]'");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[$inf[csf('barcode_no')]]=$inf[csf('barcode_no')];
	}

	if(count($inserted_roll_arr)>0) $roll_cond=" and c.barcode_no not in (".implode(",",array_unique($inserted_roll_arr)).") ";

	// unsaved sql >>
	$unsaved_sql = sql_select("SELECT  b.id, a.issue_number, a.challan_no, a.order_id, a.issue_purpose, b.trans_id, b.distribution_method, b.program_no, b.no_of_roll, b.roll_no, b.roll_po_id, b.roll_wise_issue_qnty, b.prod_id, b.issue_qnty, b.color_id, b.yarn_lot, b.yarn_count, b.store_name, b.rack, b.self, b.stitch_length, b.remarks,c.roll_id, c.barcode_no, c.roll_no,c.po_breakdown_id, c.is_sales, c.booking_without_order,c.qc_pass_qnty_pcs
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=61 and c.entry_form=61 and c.is_returned!=1 and a.issue_number='".$data[1]."' and c.status_active=1 and c.is_deleted=0 $roll_cond ");

	foreach ($unsaved_sql as $val)
	{
		$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		if($val[csf("is_sales")] == 1)
		{
			$sales_order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
		}
		else
		{
			if($val[csf("booking_without_order")] == 1)
			{
				$non_ord_book_arr[$row[csf("order_id")]] = $row[csf("order_id")];
			}
			else
			{
				$order_id_arr[$val[csf("order_id")]] = $val[csf("order_id")];
			}
		}
		$all_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
	}
	//<<   ==   unsaved sql
	// echo "<pre>";print_r($order_id_arr);

	$barcode_arr = array_filter($barcode_arr);
	$order_id_arr = array_filter($order_id_arr);
	$color_id_arr = array_filter($color_id_arr);
	$gsm_id_arr = array_filter($gsm_id_arr);
	$sales_order_id_arr = array_filter($sales_order_id_arr);
	$order_id_arr = array_filter($order_id_arr);


	if(!empty($order_id_arr))
	{
		$order_cond = (!empty($order_id_arr))?" and b.id in(".implode(",",$order_id_arr).")":"";

		$data_array=sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.file_no, b.id as po_id, c.booking_no FROM wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on b.id = c.po_break_down_id and c.booking_type in (1,4) WHERE a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 $order_cond $color_cond $gsm_cond  group by a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.file_no, b.id, c.booking_no");
		$po_details_array=array();
		foreach($data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no_prefix_num")];
			$po_details_array[$row[csf("po_id")]]['job_no_full']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
			$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
			$po_details_array[$row[csf("po_id")]]['booking_no'] .=$row[csf("booking_no")].",";
		}

		$job_arr=array();
		/*$order_cond = (!empty($order_id_arr))?" and b.id in(".implode(",",$order_id_arr).")":"";
		$sql_job=sql_select("SELECT a.booking_no,b.job_no_prefix_num, b.job_no, b.buyer_name, b.insert_date,c.file_no FROM wo_booking_dtls a, wo_po_details_master b, wo_po_break_down c WHERE a.po_break_down_id=c.id and b.job_no=c.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 $order_cond "); // $order_cond

		foreach ($sql_job as $job_row) {
			$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 	= $job_row[csf('job_no_prefix_num')];
			$job_arr[$job_row[csf('booking_no')]]["job_no_full"] 	= $job_row[csf('job_no')];
			$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 	= $buyer_name_array[$job_row[csf('buyer_name')]];
			$job_arr[$job_row[csf('booking_no')]]["buyer_id"] 		= $job_row[csf('buyer_name')];
			$job_arr[$job_row[csf('booking_no')]]["year"] 			= date("Y",strtotime($job_row[csf("insert_date")]));
			$job_arr[$job_row[csf('booking_no')]]["file_no"] 		= $job_row[csf('file_no')];
		}*/
	}

	// echo "<pre>";print_r($all_po_arr);
	if(!empty($all_po_arr))
	{
		$dia_with=sql_select("SELECT booking_no, po_id, width_dia_type, id as plan_id, dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and po_id in (".implode(",",$all_po_arr).")");
		$dia_with_arr=array();
		foreach($dia_with as $row)
		{
			//$dia_with_arr[$row[csf('po_id')]]=$row[csf('width_dia_type')];
			// $dia_with_arr[$row[csf('plan_id')]]=$row[csf('width_dia_type')];
			$dia_with_arr[$row[csf('dtls_id')]]=$row[csf('width_dia_type')];
		}
	}

	if(!empty($sales_order_id_arr))
	{
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
		/*$salesBookingResult = sql_select("SELECT booking_no, booking_date from wo_booking_mst where status_active=1 and is_deleted=0 and booking_no in($salesbookingNos) group by booking_no, booking_date");
		foreach ($salesBookingResult as $row)
		{
			$salesBookinginfo[$row[csf("booking_no")]]['year'] = date("Y",strtotime($row[csf("booking_date")]));
		}*/
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

	if($barcode_cond!="")
	{
		$booking_sql=sql_select("SELECT a.booking_id, a.booking_no, c.barcode_no, c.entry_form, a.receive_basis, c.coller_cuff_size from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 $barcode_cond
		union all
		select c.po_breakdown_id as booking_id, c.booking_no, c.barcode_no, c.entry_form, c.receive_basis, c.coller_cuff_size from pro_roll_details c where  c.entry_form=58 and c.booking_without_order=1 and c.roll_split_from>0 $barcode_cond"); // and a.receive_basis=1

		$booking_data=array();
		foreach($booking_sql as $row)
		{
			$booking_data[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			$booking_data[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
			$booking_data[$row[csf("barcode_no")]]["coller_cuff_size"]=$row[csf("coller_cuff_size")];

			if($row[csf("entry_form")] == 2 || $row[csf("receive_basis")] == 2)
			{
				$program_from_barcode[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			}

			$booking_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
			if($row[csf("entry_form")] == 2){
				$basis_arr[$row[csf("barcode_no")]]=$row[csf("receive_basis")];
			}
		}
		//echo "<pre>";print_r($booking_data);die;

		$booking_arr = array_filter($booking_arr);
		if(!empty($booking_arr))
		{
			$all_non_booking_cond = "'".implode("','", $booking_arr)."'";
			$dia_width_non_ord = return_library_array("select a.dia_width, a.booking_no from wo_non_ord_samp_booking_dtls a where a.status_active = 1 and a.booking_no in ($all_non_booking_cond)","booking_no","dia_width");
		}

		$sql_split_roll="select c.roll_id,c.barcode_no from pro_roll_split c where c.entry_form=75 and c.status_active=1 and c.is_deleted=0 $barcode_cond";
		$split_sql=sql_select($sql_split_roll);
		$splite_roll_arr=array();
		foreach($split_sql as $inv)
		{
			$splite_roll_arr[$inv[csf('barcode_no')]]=$inv[csf('barcode_no')];
		}

		$data_array=sql_select("SELECT  a.id, a.company_id, a.recv_number,a.booking_no, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm,  c.qnty, b.width, b.body_part_id, b.yarn_lot, b.brand_id, b.shift_name, b.floor_id, b.machine_no_id, b.yarn_count ,b.color_id, b.color_range_id, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.booking_without_order,c.is_sales,c.qc_pass_qnty_pcs
		FROM inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE  a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and b.trans_id>0 and c.status_active=1 and c.is_deleted=0 $barcode_cond");

		$roll_details_array=array();
		foreach($data_array as $row)
		{
			$receive_basis = $basis_arr[$row[csf("barcode_no")]];
			/*if($receive_basis == 2)
			{
				$dia_width_type =  $dia_with_arr[$program_from_barcode[$row[csf("barcode_no")]]["booking_id"]];
			}
			else if($receive_basis == 1)
			{
				$dia_width_type = $dia_width_non_ord[$booking_data[$row[csf("barcode_no")]]["booking_no"]];
			}*/
			$dia_width_type =  $dia_with_arr[$program_from_barcode[$row[csf("barcode_no")]]["booking_id"]];


			$roll_details_array[$row[csf("barcode_no")]]['dia_width_type']=$dia_width_type;
			$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("body_part_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
			$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("febric_description_id")];
			$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
			$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];
			$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
			$roll_details_array[$row[csf("barcode_no")]]['color_range_id']=$row[csf("color_range_id")];
			$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
			$roll_details_array[$row[csf("barcode_no")]]['barcode_no']=$row[csf("barcode_no")];
			$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$machine_arr[$row[csf("machine_no_id")]];
			$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$row[csf("knitting_source")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
			$roll_details_array[$row[csf("barcode_no")]]['buyer_id']=$row[csf("buyer_id")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_without_order']=$row[csf("booking_without_order")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$booking_data[$row[csf("barcode_no")]]["booking_no"];
			$roll_details_array[$row[csf("barcode_no")]]['coller_cuff_size']=$booking_data[$row[csf("barcode_no")]]["coller_cuff_size"];
			$roll_details_array[$row[csf("barcode_no")]]['is_sales']=$row[csf("is_sales")];
			$roll_details_array[$row[csf("barcode_no")]]['qty_in_pcs']=$row[csf("qc_pass_qnty_pcs")]*1;

			$split_barcode_no=$splite_roll_arr[$row[csf('barcode_no')]];

			if($split_barcode_no==$row[csf("barcode_no")])
			{
				$splite_roll_arr2[$split_barcode_no]['booking_no']=$booking_data[$row[csf("barcode_no")]]["booking_no"];
			}

			$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
			$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
			$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];
			if($row[csf("knitting_source")]==1)
			{
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
			}
			else if($row[csf("knitting_source")]==3)
			{
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
			}

		}
		//echo "<pre>";print_r($roll_details_array);die;

		$sql_sub_roll=sql_select("select barcode_no from pro_roll_details where barcode_no in(".implode(",",$barcode_arr).") and entry_form=64 and status_active=1 and is_deleted=0");
		$subcontact_roll=array();
		foreach($sql_sub_roll as $inv)
		{
			$subcontact_roll[$inv[csf('barcode_no')]]=$inv[csf('barcode_no')];
		}

		if($db_type==0)
		{
			$null_cond="IFNULL";
		}
		else if($db_type==2)
		{
			$null_cond="NVL";
		}
		else
		{
			$null_cond="ISNULL";
		}

		//$order_to_sample_sql=sql_select("select c.barcode_no, c.po_breakdown_id, b.buyer_id, b.id, b.booking_no from wo_non_ord_samp_booking_mst b, pro_roll_details c where b.id=c.po_breakdown_id and c.entry_form in(110,180) and c.booking_without_order=1 and c.status_active=1 and c.is_deleted=0  $barcode_cond");

		$order_to_sample_sql=sql_select("select c.barcode_no, c.po_breakdown_id, b.buyer_id, b.id, b.booking_no from wo_non_ord_samp_booking_mst b, pro_roll_details c where b.id=c.po_breakdown_id and c.entry_form in(58,84,110,180) and c.booking_without_order=1 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0  $barcode_cond");

		$order_to_sample_data=array();
		foreach($order_to_sample_sql as $row)
		{
			$order_to_sample_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["buyer_id"]=$row[csf("buyer_id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["id"]=$row[csf("id")];
			$order_to_sample_data[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
		}

	}

	$total_roll_wight=0;
	$total_roll_qtyInPcs=0;
	$issue_details_arr=array();
	$j=1;
	$dia_type = "";
	foreach($sql_update as $val)
	{
		$inserted_roll_arr[]=$val[csf('roll_id')];
		$subcon_cond="";
		if(in_array($val[csf('barcode_no')],$subcontact_roll)){ $subcon_cond="disabled";}

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
				$jobNo = $sales_arr[$val[csf('order_id')]]["po_job_no"];
				$job_no_full = $sales_arr[$val[csf('order_id')]]["po_job_no"];
				$JobBuyer = $sales_arr[$val[csf('order_id')]]["po_buyer"];
				$buyer_id = $sales_arr[$val[csf('order_id')]]["po_buyer"];
				//$file_no = $job_arr[$sales_booking]["file_no"];
				//$Jobyear = $job_arr[$sales_booking_no]['year'];
				$Jobyear = $salesBookinginfo[$sales_booking]['year'];
				$int_ref=$po_details_arr[$sales_booking]['int_ref'];
			}
			else
			{
				$po_number = $sales_arr[$val[csf('order_id')]]["po_number"];
				$jobNo = $sales_arr[$val[csf('order_id')]]["job_no"];
				$job_no_full = $sales_arr[$val[csf('order_id')]]["job_no"];
				$JobBuyer = $sales_arr[$val[csf('order_id')]]["buyer_id"];
				$buyer_id = $sales_arr[$val[csf('order_id')]]["buyer_id"];

				//$file_no = $job_arr[$sales_booking]["file_no"];
				$Jobyear = $sales_arr[$val[csf('order_id')]]["year"];
				$int_ref='';
			}
		}
		else
		{
			$po_number = $po_details_array[$val[csf('order_id')]]['po_number'];
			$jobNo = $po_details_array[$val[csf('order_id')]]['job_no'];
			$job_no_full = $po_details_array[$val[csf("order_id")]]['job_no_full'];
			$Jobyear = $po_details_array[$val[csf('order_id')]]['year'];
			$JobBuyer = $po_details_array[$val[csf('order_id')]]['buyer_name'];
			$file_no = $po_details_array[$val[csf('order_id')]]['file_no'];
			$int_ref = $po_details_array[$val[csf('order_id')]]['int_ref'];
			$buyer_id = $po_details_array[$val[csf("order_id")]]['buyer_id'];

			//echo "100".$booking_no;die;
			if($val[csf('booking_without_order')]==1)
			{
				$jobNo = "";
				$job_no_full = "";
				$po_number ="";
				$Jobyear ="";
				$file_no ="";
				$int_ref='';
				$buyer_id = $order_to_sample_data[$val[csf("barcode_no")]]["buyer_id"];
			}
		}
		$dia_type = $fabric_typee[$roll_details_array[$val[csf('barcode_no')]]['dia_width_type']];
		?>
        <tr id="tr_1" align="center" valign="middle">
            <td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" onClick="totalCalculation('<? echo $j; ?>');" checked="checked"  <? echo $subcon_cond;?> > &nbsp; &nbsp;<? echo $j; ?></td>
            <td width="40" id="rollId_<? echo $j; ?>"> <? echo $val[csf('roll_no')]; ?></td>
            <td width="70" id="barcode_<? echo $j; ?>"> <? echo $val[csf('barcode_no')]; ?></td>
            <td width="80" id="systemId_<? echo $j; ?>"> <? echo $body_part[$val[csf('body_part_id')]]; ?></td>
            <td width="120" id="progBookId_<? echo $j; ?>" style="word-break: break-all;"> <? echo $composition_arr[$val[csf('febric_description_id')]]; ?></td>
            <td width="50" id="basis_<? echo $j; ?>"> <? echo $val[csf('gsm')]; ?></td>
            <td width="50" id="knitSource_<? echo $j; ?>"> <? echo $val[csf('width')]; ?></td>
            <td width="100" id="prodDate_<? echo $j; ?>" style="word-break: break-all;"> <? echo $color;//$color_arr[$val[csf('color_id')]]; ?></td>
            <td width="60" id="prodId_<? echo $j; ?>">
            <?
            echo $dia_type;//$fabric_typee[$val[csf('width_dai_type')]];
            ?>
            </td>
            <td width="60" id="rollWgt_<? echo $j; ?>" name="rollWgt[]" align="right"><? echo $val[csf('roll_wgt_curr')]; ?></td>
            <td width="60" id="qtyInPcs_<? echo $j; ?>" name="qtyInPcs[]" align="right"><? echo $val[csf('qc_pass_qnty_pcs')]*1; ?></td>
            <td width="60" id="collarCuffSize_<? echo $j; ?>" name="collarCuffSize[]" align="right" title="<? echo $val[csf('barcode_no')];?>"><? echo $roll_details_array[$val[csf('barcode_no')]]['coller_cuff_size'];?></td>
            <td width="100" id="job_<? echo $j; ?>"><? echo $jobNo; ?></td>
            <td width="50" id="year_<? echo $j; ?>" align="center"><? echo $Jobyear; ?></td>
            <td width="65" id="buyer_<? echo $j; ?>"><? echo $buyer_name_array[$buyer_id]; ?></td>
            <td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $po_number; ?></td>
            <td width="80" id="intRef_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $int_ref; ?></td>
            <td width="70" id="file_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $file_no; ?></td>
            <?
            if($val[csf('booking_without_order')]==1)
            {
                $ots_booking_no=$order_to_sample_data[$val[csf("barcode_no")]]["booking_no"];
                if($ots_booking_no!='')
                {
                    $booking_no=$ots_booking_no;
                }
                else
                {
                    $booking_no=$roll_details_array[$val[csf("barcode_no")]]['booking_no'];
                }
            }
            else
            {
                if($order_to_sample_data[$val[csf("barcode_no")]]["po_breakdown_id"]!="")
                {
                    $splite_roll_booking_no=$splite_roll_arr2[$val[csf("barcode_no")]]['booking_no'];
                    $ots_booking_no=$order_to_sample_data[$val[csf("barcode_no")]]["booking_no"];
                    if($ots_booking_no!='')
                    {
                        $booking_no=$ots_booking_no;
                    }
                    else if($splite_roll_booking_no!='')
                    {
                        $booking_no=$splite_roll_booking_no;
                    }
                    else
                    {
                        $booking_no=$roll_details_array[$val[csf("barcode_no")]]['booking_no'];
                    }

                }
                else
                {
                	if($is_sales != 1)
                	{

                    	$booking_no = implode(",",array_unique(explode(",",chop($po_details_array[$val[csf('order_id')]]['booking_no'],","))));
                	}
                }
            }
            ?>
            <td width="90" id="cons_<? echo $j; ?>" style="word-break:break-all;" align="left">
            <?
            if($val[csf("knitting_source")]==1)
            {
                $knitting_com=$company_name_array[$val[csf("knitting_company")]];
            }
            else if($val[csf("knitting_source")]==3)
            {
                $knitting_com=$supplier_arr[$val[csf("knitting_company")]];
            }
            echo $knitting_com;
            ?>
            </td>
            <td width="60" id="mc_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$val[csf('barcode_no')]]['machine_no_id']; ?></td>
            <td width="90" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $booking_no; ?></td>
            <td id="gsm_<? echo $j; ?>">
                <?
                $total_roll_wight+=$val[csf('roll_wgt_curr')];
                $total_roll_qtyInPcs+=$val[csf('qc_pass_qnty_pcs')]*1;
                $receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
                echo $receive_basis[$val[csf("receive_basis")]];
                ?>
            </td>
	        <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="<? echo $val[csf('id')]; ?>" />
	        <input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $j; ?>" value="<? echo $val[csf('update_roll_id')]; ?>" />
	        <input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $val[csf('roll_id')]; ?>" />
	        <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $val[csf("body_part_id")]; ?>"/>
	        <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $val[csf("color_id")]; ?>" />
	        <input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
	        <input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $val[csf("prod_id")]; ?>" />
	        <input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $val[csf("roll_wgt_curr")]; ?>"/>
	        <input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $j; ?>" value="<? echo $val[csf("qc_pass_qnty_pcs")]*1; ?>"/>
	        <input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $val[csf("width")]; ?>"/>
	        <input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $val[csf("gsm")]; ?>"/>
	        <input type="hidden" name="fabricId[]" id="fabricId_<? echo $j; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
	        <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $val[csf("receive_basis")]; ?>"/>
	        <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $val[csf("knitting_source")]; ?>"/>
	        <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $val[csf("knitting_company")]; ?>"/>
	        <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $val[csf('barcode_no')]; ?>"/>
	        <input type="hidden" name="isSales[]" id="isSales_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['is_sales']; ?>"/>
	        <input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $val[csf("order_id")]; ?>" />
	        <input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $buyer_id ?>"/>
	        <?
	        if($val[csf("booking_without_order")]==1)
	        {
	            ?>
	            <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $booking_no; ?>"/>
	            <input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? ?>"/>
	            <?
	        }
	        else
	        {
	            ?>
	            <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $booking_no; ?>"/>
	            <input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $job_no_full; ?>"/>
	            <?
	        }
	        ?>
	        <input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $j; ?>" value="<? echo $val[csf("booking_without_order")]; ?>"/>
	    </tr>
	    <?
	    $j++;
	}
	//unsaved data below here=====================================================================================


	//N.B. Issue to process Source for Heat Settings [1=>grey roll issue, 2=> roll receive for batch]
	$variable_set_source_arr =  sql_select("select distribute_qnty from variable_settings_production where variable_list=85 and company_name=$company_id and status_active=1 and is_deleted=0 order by id desc");
	$variable_set_source = $variable_set_source_arr[0][csf("distribute_qnty")]*1;

	if($variable_set_source==1)
	{
		//$barcode_arr
		if (!empty($barcode_arr))
		{
			if (count($barcode_arr) > 0) {
				$barcode_NOs = implode(",", $barcode_arr);
				$all_barcode_no_cond = "";
				$barCond = "";
				if ($db_type == 2 && count($barcode_arr) > 999) {
					$barcode_arr_chunk = array_chunk($barcode_arr, 999);
					foreach ($barcode_arr_chunk as $chunk_arr) {
						$chunk_arr_value = implode(",", $chunk_arr);
						$barCond .= " c.barcode_no in($chunk_arr_value) or ";
					}

					$all_barcode_no_cond .= " and (" . chop($barCond, 'or ') . ")";
				} else {
					$all_barcode_no_cond = " and c.barcode_no in($barcode_NOs)";
				}
			}
		}

		//For Heat settings roll should service receive from service before grey batch receive according to variable setup
		$issue_to_process_barcode_sql =  sql_select("SELECT c.barcode_no, c.is_rcv_done from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=63 and c.entry_form=63 and a.process_id in (33,100,476) and c.is_sales=1 $all_barcode_no_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.barcode_no asc, c.is_rcv_done desc");

		foreach ($issue_to_process_barcode_sql as $val)
		{
			$issue_to_process_arr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
			if($val[csf("is_rcv_done")]==0)
			{
				$not_receive_from_process_arr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
			}
		}
	}

	if(count($unsaved_sql)>0)
	{
		$dia_type="";
		foreach($unsaved_sql as $inf)
		{
			//N.B (variable set to grey issue for heat settings AND issue to process found AND receive from issue to process) || variable set to grey receive
			if(($variable_set_source==1 && $issue_to_process_arr[$inf[csf("barcode_no")]]!="" && $not_receive_from_process_arr[$inf[csf("barcode_no")]]=="") || $variable_set_source==2 || $variable_set_source==0)
			{
				$color='';
				$color_ids=explode(",",$roll_details_array[$inf[csf('barcode_no')]]['color_id']);
				foreach($color_ids as $color_id)
				{
					if($color_id>0) $color.=$color_arr[$color_id].",";
				}
				$color=chop($color,',');

				$buyer_id = $order_to_sample_data[$inf[csf("barcode_no")]]["buyer_id"];
				$dia_type = $fabric_typee[$roll_details_array[$inf[csf('barcode_no')]]['dia_width_type']];

				/*$is_sales = $inf[csf('is_sales')];
				if($is_sales == 1)
				{
					$sales_booking=$sales_arr[$inf[csf('order_id')]]["sales_booking_no"];
					$within_group 	= $sales_arr[$inf[csf('order_id')]]["within_group"];
					if($within_group==1)
					{
						$po_number = $sales_arr[$inf[csf('order_id')]]["po_number"];
						$jobNo = $sales_arr[$inf[csf('order_id')]]["po_job_no"];
						$job_no_full = $sales_arr[$inf[csf('order_id')]]["po_job_no"];
						$JobBuyer = $sales_arr[$inf[csf('order_id')]]["po_buyer"];
						$buyer_id = $sales_arr[$inf[csf('order_id')]]["po_buyer"];
						//$file_no = $job_arr[$sales_booking]["file_no"];
						//$Jobyear = $job_arr[$sales_booking_no]['year'];
						$Jobyear = $salesBookinginfo[$sales_booking]['year'];
						$int_ref=$po_details_arr[$sales_booking]['int_ref'];
					}
					else
					{
						$po_number = $sales_arr[$inf[csf('order_id')]]["po_number"];
						$jobNo = $sales_arr[$inf[csf('order_id')]]["job_no"];
						$job_no_full = $sales_arr[$inf[csf('order_id')]]["job_no"];
						$JobBuyer = $sales_arr[$inf[csf('order_id')]]["buyer_id"];
						$buyer_id = $sales_arr[$inf[csf('order_id')]]["buyer_id"];

						//$file_no = $job_arr[$sales_booking]["file_no"];
						$Jobyear = $sales_arr[$inf[csf('order_id')]]["year"];
						$int_ref='';
					}
				}
				else
				{
					$po_number = $po_details_array[$inf[csf('order_id')]]['po_number'];
					$jobNo = $po_details_array[$inf[csf('order_id')]]['job_no'];
					$job_no_full = $po_details_array[$inf[csf("order_id")]]['job_no_full'];
					$Jobyear = $po_details_array[$inf[csf('order_id')]]['year'];
					$JobBuyer = $po_details_array[$inf[csf('order_id')]]['buyer_name'];
					$file_no = $po_details_array[$inf[csf('order_id')]]['file_no'];
					$int_ref = $po_details_array[$inf[csf('order_id')]]['int_ref'];
					$buyer_id = $po_details_array[$inf[csf("order_id")]]['buyer_id'];

					//echo "100".$booking_no;die;
					if($inf[csf('booking_without_order')]==1)
					{
						$jobNo = "";
						$job_no_full = "";
						$po_number ="";
						$Jobyear ="";
						$file_no ="";
						$int_ref='';
						$buyer_id = $order_to_sample_data[$inf[csf("barcode_no")]]["buyer_id"];
					}
				}*/
				?>
				<tr id="tr_1" align="center" valign="middle">
					<td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" > &nbsp; &nbsp;<? echo $j; ?></td>
					<td width="40" id="rollId_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('barcode_no')]]['roll_no']; ?></td>
					<td width="70" id="barcode_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('barcode_no')]]['barcode_no']; ?></td>
					<td width="80" id="systemId_<? echo $j; ?>"> <? echo $body_part[$roll_details_array[$inf[csf('barcode_no')]]['body_part_id']]; ?></td>
					<td width="120" id="progBookId_<? echo $j; ?>"> <? echo $composition_arr[$roll_details_array[$inf[csf('barcode_no')]]['deter_id']]; ?></td>
					<td width="50" id="basis_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('barcode_no')]]['gsm']; ?></td>
					<td width="50" id="knitSource_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('barcode_no')]]['width']; ?></td>
					<td width="100" id="prodDate_<? echo $j; ?>" style="word-break: break-all;"> <? echo $color; ?></td>
					<td width="60" id="prodId_<? echo $j; ?>"><p><? echo $dia_type;?></p></td>
					<td width="60" id="rollWgt_<? echo $j; ?>" name="rollWgt[]" align="right"><? echo $roll_details_array[$inf[csf('barcode_no')]]['qnty']; ?></td>
					<td width="60" id="qtyInPcs_<? echo $j; ?>" name="qtyInPcs[]" align="right"><? echo $roll_details_array[$inf[csf('barcode_no')]]['qty_in_pcs']; ?></td>
					<td width="60" id="collarCuffSize_<? echo $j; ?>" name="collarCuffSize[]" align="right"><? echo $roll_details_array[$inf[csf('barcode_no')]]['coller_cuff_size']; ?></td>
					<?
					if($inf[csf('booking_without_order')]==1)
					{
						?>
						<td width="50" id="job_<? echo $j; ?>"><? ?></td>
						<td width="50" id="year_<? echo $j; ?>" align="center"><? ?></td>
						<td width="65" id="buyer_<? echo $j; ?>"><? echo $buyer_name_array[$buyer_id]; ?></td>
						<td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><?  ?></td>
						<td width="80" id="intRef_<? echo $j; ?>" style="word-break:break-all;" align="center"><?  ?></td>
						<td width="70" id="file_<? echo $j; ?>" style="word-break:break-all;" align="center"><? ?></td>
						<?
					}
					else
					{
						?>
						<td width="50" id="job_<? echo $j; ?>"><? echo $po_details_array[$inf[csf('po_breakdown_id')]]['job_no']; ?></td>
						<td width="50" id="year_<? echo $j; ?>" align="center"><? echo $po_details_array[$inf[csf('po_breakdown_id')]]['year']; ?></td>
						<td width="65" id="buyer_<? echo $j; ?>"><? echo $po_details_array[$inf[csf('po_breakdown_id')]]['buyer_name']; ?></td>
						<td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo $po_details_array[$inf[csf('po_breakdown_id')]]['po_number']; ?></td>
						<td width="80" id="intRef_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo ''; ?></td>
						<td width="70" id="file_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo $po_details_array[$inf[csf('po_breakdown_id')]]['file_no']; ?></td>
						<?
					}

					?>

					<td width="90" id="cons_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$inf[csf('barcode_no')]]['knitting_company']; ?></td>
					<td width="60" id="mc_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$val[csf('barcode_no')]]['machine_no_id']; ?></td>
					<td width="90" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$inf[csf('barcode_no')]]['booking_no']; ?></td>
					<td id="gsm_<? echo $j; ?>">

					<?
					$total_roll_wight+=$roll_details_array[$inf[csf('barcode_no')]]['qnty'];
					$total_roll_qtyInPcs+=$roll_details_array[$inf[csf('barcode_no')]]['qty_in_pcs'];
					$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
					echo $receive_basis[$roll_details_array[$inf[csf('barcode_no')]]['receive_basis']]; ?></td>
					<input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="0" />
					<input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $j; ?>" value="0" />
					<input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $inf[csf('roll_id')]; ?>" />
					<input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['roll_no']; ?>" />
					<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['body_part_id']; ?>"/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['color_id']; ?>" />
					<input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['deter_id']; ?>"/>
					<input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $inf[csf('prod_id')]; //echo $roll_details_array[$inf[csf('barcode_no')]]['prod_id']; ?>" />

					<?
					if($inf[csf('booking_without_order')]==1)
					{
						?>
						<input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $inf[csf('po_breakdown_id')]; ?>" />
						<input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['buyer_id']; ?>"/>
						<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<?  ?>"/>
						<?
					}
					else
					{
						?>
						<input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $inf[csf('po_breakdown_id')]; ?>" />
						<input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $po_details_array[$inf[csf('po_breakdown_id')]]['buyer_id']; ?>"/>
						<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $po_details_array[$inf[csf('po_breakdown_id')]]['job_no_full']; ?>"/>
						<?
					}
					?>


					<input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['qnty']; ?>"/>
					<input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['qty_in_pcs']; ?>"/>
					<input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['width']; ?>"/>
					<input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['gsm']; ?>"/>
					<input type="hidden" name="fabricId[]" id="fabricId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['dtls_id']; ?>"/>
					<input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['receive_basis']; ?>"/>
					<input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['knitting_source']; ?>"/>
					<input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['knitting_company_id']; ?>"/>

					<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['booking_no']; ?>"/>
					<input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['barcode_no']; ?>"/>
					<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $j; ?>" value="<? echo $inf[csf('booking_without_order')]; ?>"/>
					<input type="hidden" name="isSales[]" id="isSales_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['is_sales']; ?>"/>
				</tr>
				<?
				$j++;
			}
		}
	}
	?>
	<table cellpadding="0" cellspacing="0" width="1360" border="1" id="scanning_tbl" rules="all" class="rpt_table">
		<tfoot>
			<tr>
				<th colspan="9">Total</th>
				<th id="total_calculate_qty_id"><? echo number_format($total_roll_wight,2); ?></th>
				<th id="total_calculate_qtyInPcs_id"><? echo $total_roll_qtyInPcs; ?></th>
				<th colspan="11"></th>

			</tr>
		</tfoot>
	</table>
	<?
	exit();
}

if($action=="check_challan_no")
{
	$data_array = sql_select("SELECT  b.id, a.issue_number, a.challan_no, a.order_id, a.issue_purpose, b.trans_id, b.distribution_method,
	b.program_no, b.no_of_roll, b.roll_no, b.roll_po_id, b.roll_wise_issue_qnty, b.prod_id, b.issue_qnty, b.color_id, b.yarn_lot, b.yarn_count, b.store_name, b.rack, b.self, b.stitch_length, b.remarks, b.body_part_id, c.roll_id,c.barcode_no, c.qnty,c.po_breakdown_id,c.is_sales, c.booking_without_order, c.qc_pass_qnty_pcs
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form=61 and c.entry_form=61 and c.is_returned!=1 and a.issue_number='$data' and c.status_active=1 and c.is_deleted=0  ");

	$inserted_roll=sql_select("select c.barcode_no from pro_grey_batch_dtls a,inv_receive_mas_batchroll b, pro_roll_details c  where a.mst_id=b.id and a.id=c.dtls_id and b.id=c.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=62 and c.entry_form=62 and b.challan_no='$data'");

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
	$machine_arr = return_library_array( "select id,machine_no from lib_machine_name", "id", "machine_no");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

	$inserted_roll=sql_select("select c.barcode_no from pro_grey_batch_dtls a,inv_receive_mas_batchroll b, pro_roll_details c  where a.mst_id=b.id and a.id=c.dtls_id and b.id=c.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=62 and c.entry_form=62 and b.challan_no='$data'");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[$inf[csf('barcode_no')]]=$inf[csf('barcode_no')];
	}

	if(count($inserted_roll_arr)>0) $roll_cond=" and c.barcode_no not in (".implode(",",array_unique($inserted_roll_arr)).") ";


	$sql = sql_select("SELECT b.id, a.company_id, a.issue_number, a.challan_no, a.order_id, a.issue_purpose, b.trans_id, b.distribution_method,
	b.program_no, b.no_of_roll, b.roll_no, b.roll_po_id, b.roll_wise_issue_qnty, b.prod_id, b.issue_qnty, b.color_id, b.yarn_lot, b.yarn_count, b.store_name, b.rack, b.self, b.stitch_length, b.remarks, b.body_part_id, c.roll_id,c.barcode_no, c.qnty,c.po_breakdown_id,c.is_sales, c.booking_without_order, c.qc_pass_qnty_pcs, c.booking_no
	from inv_issue_master a, inv_grey_fabric_issue_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and a.entry_form=61 and c.entry_form=61 and c.is_returned!=1 and a.issue_number='$data' and c.status_active=1 and c.is_deleted=0 $roll_cond ");


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
		$company_id =$row[csf("company_id")];
	}

	//N.B. Issue to process Source for Heat Settings [1=>grey roll issue, 2=> roll receive for batch]
	$variable_set_source_arr =  sql_select("select distribute_qnty from variable_settings_production where variable_list=85 and company_name=$company_id and status_active=1 and is_deleted=0 order by id desc");
	$variable_set_source = $variable_set_source_arr[0][csf("distribute_qnty")]*1;


	if($variable_set_source==1)
	{
		$issue_barcode = chop($issue_barcode,',');
		$all_issue_barcode_arr = explode(",", $issue_barcode);
		$heat_set_rcv_count=0;

		if ($all_barcodeNo != "") {
			$all_issue_barcode_arr = array_filter($all_issue_barcode_arr);
			if (count($all_issue_barcode_arr) > 0) {
				$barcod_NOs = implode(",", $all_issue_barcode_arr);
				$all_barcode_no_cond = "";
				$barCond = "";
				if ($db_type == 2 && count($all_issue_barcode_arr) > 999) {
					$all_issue_barcode_arr_chunk = array_chunk($all_issue_barcode_arr, 999);
					foreach ($all_issue_barcode_arr_chunk as $chunk_arr) {
						$chunk_arr_value = implode(",", $chunk_arr);
						$barCond .= " c.barcode_no in($chunk_arr_value) or ";
					}

					$all_barcode_no_cond .= " and (" . chop($barCond, 'or ') . ")";
				} else {
					$all_barcode_no_cond = " and c.barcode_no in($barcod_NOs)";
				}
			}
		}

		//For Heat settings business roll should service receive from service before grey batch receive according to variable setup
		$issue_to_process_barcode_sql =  sql_select("SELECT c.barcode_no, c.is_rcv_done from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=63 and c.entry_form=63 and a.process_id in (33,100,476) and c.is_sales=1 $all_barcode_no_cond and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by c.barcode_no asc, c.is_rcv_done desc");

		foreach ($issue_to_process_barcode_sql as $val)
		{
			$issue_to_process_arr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
			if($val[csf("is_rcv_done")]==0)
			{
				$not_receive_from_process_arr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
			}
		}
	}

	$issue_po_id_arr = array_filter(array_unique($issue_po_id_arr));
	$issue_po_id = implode(",",$issue_po_id_arr);

	if(count($issue_po_id_arr)>0)
	{
		$dia_with=sql_select("SELECT booking_no, po_id, width_dia_type, id as plan_id, dtls_id from ppl_planning_entry_plan_dtls where status_active=1 and is_deleted=0 and po_id in (".$issue_po_id.")");

		$dia_with_arr=array();
		foreach($dia_with as $row)
		{
			// $dia_with_arr[$row[csf('plan_id')]]=$row[csf('width_dia_type')];
			$dia_with_arr[$row[csf('dtls_id')]]=$row[csf('width_dia_type')];
		}
	}


	$issue_barcode=chop($issue_barcode,",");

	$barcode_cond="";
	if($issue_barcode!="") $barcode_cond=" and c.barcode_no in($issue_barcode)";

	if($barcode_cond!="")
	{

		$booking_sql=sql_select("SELECT a.booking_id, a.booking_no, c.barcode_no, c.entry_form, a.receive_basis,c.coller_cuff_size from inv_receive_master a, pro_roll_details c where a.id=c.mst_id and a.entry_form=2 and c.entry_form=2 $barcode_cond
		union all
		select c.po_breakdown_id as booking_id, c.booking_no, c.barcode_no, c.entry_form, c.receive_basis,c.coller_cuff_size from pro_roll_details c where  c.entry_form=58 and c.booking_without_order=1 and c.roll_split_from>0 $barcode_cond");
		$booking_data=array();
		foreach($booking_sql as $row)
		{
			$booking_data[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			$booking_data[$row[csf("barcode_no")]]["booking_no"]=$row[csf("booking_no")];
			$booking_data[$row[csf("barcode_no")]]["coller_cuff_size"]=$row[csf("coller_cuff_size")];

			if($row[csf("entry_form")] == 2 || $row[csf("receive_basis")] == 2)
			{
				$program_from_barcode[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
			}

			$booking_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];

		}

		$booking_arr = array_filter($booking_arr);
		if(!empty($booking_arr))
		{
			$all_non_booking_cond = "'".implode("','", $booking_arr)."'";
			$dia_width_non_ord = return_library_array("select a.dia_width, a.booking_no from wo_non_ord_samp_booking_dtls a where a.status_active = 1 and a.booking_no in ($all_non_booking_cond)","booking_no","dia_width");
		}

		$sql_split_roll="SELECT c.roll_id,c.barcode_no from pro_roll_split c where c.entry_form=75 and c.status_active=1 and c.is_deleted=0 $barcode_cond";
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
			$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("body_part_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
			$roll_details_array[$row[csf("barcode_no")]]['deter_id']=$row[csf("febric_description_id")];
			$roll_details_array[$row[csf("barcode_no")]]['gsm']=$row[csf("gsm")];
			$roll_details_array[$row[csf("barcode_no")]]['width']=$row[csf("width")];

			/*if($row[csf("receive_basis")] == 2)
			{
				$dia_width_type =  $dia_with_arr[$program_from_barcode[$row[csf("barcode_no")]]["booking_id"]];
			}
			else if($row[csf("receive_basis")] == 1)
			{
				$dia_width_type = $dia_width_non_ord[$booking_data[$row[csf("barcode_no")]]["booking_no"]];
			}*/
			$dia_width_type =  $dia_with_arr[$program_from_barcode[$row[csf("barcode_no")]]["booking_id"]];


			$roll_details_array[$row[csf("barcode_no")]]['dia_width']=$dia_width_type;
			// $roll_details_array[$row[csf("barcode_no")]]['dia_width']=$dia_width_type;
			$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
			$roll_details_array[$row[csf("barcode_no")]]['color_range_id']=$row[csf("color_range_id")];
			$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
			$roll_details_array[$row[csf("barcode_no")]]['qty_in_pcs']=$row[csf("qc_pass_qnty_pcs")]*1;
			$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$row[csf("receive_basis")];
			$roll_details_array[$row[csf("barcode_no")]]['barcode_no']=$row[csf("barcode_no")];
			$roll_details_array[$row[csf("barcode_no")]]['machine_no_id']=$machine_arr[$row[csf("machine_no_id")]];

			$split_barcode_no=$splite_roll_arr[$row[csf('barcode_no')]];
			if($split_barcode_no==$row[csf("barcode_no")])
			{
				$splite_roll_arr2[$split_barcode_no]['booking_no']=$booking_data[$row[csf("barcode_no")]]["booking_no"];
			}
			$roll_details_array[$row[csf("barcode_no")]]['booking_id']=$booking_data[$row[csf("barcode_no")]]["booking_id"];
			$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$booking_data[$row[csf("barcode_no")]]["booking_no"];
			$roll_details_array[$row[csf("barcode_no")]]['coller_cuff_size']=$booking_data[$row[csf("barcode_no")]]["coller_cuff_size"];

			$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$row[csf("knitting_source")];
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
			$roll_details_array[$row[csf("barcode_no")]]['buyer_id']=$row[csf("buyer_id")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_without_order']=$row[csf("booking_without_order")];

			$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
			$roll_details_array[$row[csf("barcode_no")]]['prod_id']=$row[csf("prod_id")];
			$roll_details_array[$row[csf("barcode_no")]]['dtls_id']=$row[csf("dtls_id")];

			$roll_details_array[$row[csf("barcode_no")]]['is_sales']=$row[csf("is_sales")];

			if($row[csf("knitting_source")]==1)
			{
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
			}
			else if($row[csf("knitting_source")]==3)
			{
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
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

		$po_sql = sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, b.file_no, b.grouping, a.style_ref_no, b.id as po_id, c.booking_no FROM wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls c on b.id = c.po_break_down_id and c.booking_type in (1,4) WHERE a.job_no=b.job_no_mst $po_id_cond group by a.job_no_prefix_num, a.job_no, a.buyer_name, a.insert_date, b.po_number, b.file_no, b.grouping, a.style_ref_no, b.id, c.booking_no");
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
			$po_details_array[$row[csf("po_id")]]['file_no']=$row[csf("file_no")];
			$po_details_array[$row[csf("po_id")]]['int_ref']=$row[csf("grouping")];
			$po_details_array[$row[csf("po_id")]]['booking_no'] .=$row[csf("booking_no")].",";
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
		where a.booking_no=b.booking_no and b.po_break_down_id=c.id  and d.job_no=c.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_no in($all_sales_booking_nos)
		group by b.job_no,a.buyer_id,b.booking_no, c.grouping, d.style_ref_no");
		foreach($data_array_info as $row)
		{
			$po_details_arr[$row[csf("booking_no")]]['style_ref_no']=$row[csf("style_ref_no")];
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

	if(count($sql)>0)
	{
		$issue_details_arr=array();
		$j=1;
		foreach($sql as $val)
		{
			//N.B (variable set to grey issue for heat settings AND issue to process found AND receive from issue to process) || variable set to grey receive
			if( ($variable_set_source==1 && $issue_to_process_arr[$val[csf("barcode_no")]]=="" ) || ($variable_set_source==1 && $issue_to_process_arr[$val[csf("barcode_no")]]!="" && $not_receive_from_process_arr[$val[csf("barcode_no")]]=="") || $variable_set_source==2 || $variable_set_source==0)
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
						//$year = $job_arr[$sales_booking_no]['year'];
						if($sales_arr[$sales_row[csf('id')]]["booking_without_order"] == 0)
						{
							$year = $sales_arr[$sales_row[csf('id')]]["booking_date"];
						}
						$int_ref=$po_details_arr[$sales_booking_no]['int_ref'];
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
					$job_no = $po_details_array[$val[csf('po_breakdown_id')]]['job_no'];
					$order_no = $po_details_array[$val[csf('po_breakdown_id')]]['po_number'];
					$buyer_name = $po_details_array[$val[csf('po_breakdown_id')]]['buyer_id'];
					$year = $po_details_array[$val[csf('po_breakdown_id')]]['year'];
					$booking_no = implode(",",array_unique(explode(",",chop($po_details_array[$val[csf('po_breakdown_id')]]['booking_no'],","))));

					$file_no = $po_details_array[$val[csf('po_breakdown_id')]]['file_no'];
					$int_ref = $po_details_array[$val[csf('po_breakdown_id')]]['int_ref'];


					if($val[csf('booking_without_order')]==1)
					{
						$job_no = "";
						$order_no ="";
						$year ="";
						$file_no ="";
						$int_ref='';
						$buyer_name = $order_to_sample_data[$val[csf("barcode_no")]]["buyer_id"];
						$order_to_sample_data[$val[csf("barcode_no")]]["id"];
						$order_to_sample_data[$val[csf("barcode_no")]]["booking_no"];

						$booking_no=$roll_details_array[$val[csf('barcode_no')]]['booking_no'];
						$ots_booking_no=$order_to_sample_data[$val[csf("barcode_no")]]["booking_no"];

						if($ots_booking_no){
							$booking_no = $ots_booking_no;
						}
					}
				}
				$color='';
				$color_ids=explode(",",$roll_details_array[$val[csf('barcode_no')]]['color_id']);
				foreach($color_ids as $color_id)
				{
					if($color_id>0) $color.=$color_arr[$color_id].",";
				}
				$color=chop($color,',');
				?>
				<tr id="tr_<? echo $j; ?>" align="center" valign="middle">
					<td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked" onClick="totalCalculation('<? echo $j; ?>');"> &nbsp; &nbsp;<? echo $j; ?></td>
					<td width="40" id="rollId_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('barcode_no')]]['roll_no']; ?></td>
					<td width="70" id="barcode_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('barcode_no')]]['barcode_no']; ?></td>
					<td width="80" id="systemId_<? echo $j; ?>"> <? echo $body_part[$val[csf('body_part_id')]];//echo $body_part[$roll_details_array[$val[csf('barcode_no')]]['body_part_id']]; ?></td>
					<td width="120" id="progBookId_<? echo $j; ?>"> <? echo $composition_arr[$roll_details_array[$val[csf('barcode_no')]]['deter_id']]; ?></td>
					<td width="50" id="basis_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('barcode_no')]]['gsm']; ?></td>
					<td width="50" id="knitSource_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('barcode_no')]]['width']; ?></td>
					<td width="100" id="prodDate_<? echo $j; ?>"><p> <? echo $color;//$color_arr[$roll_details_array[$val[csf('roll_id')]]['color_id']]; ?></p></td>


					<td width="60" id="diaWidth_<? echo $j; ?>"><? echo $fabric_typee[$roll_details_array[$val[csf("barcode_no")]]['dia_width']] ?></td>


					<td width="60" id="rollWgt_<? echo $j; ?>" name="rollWgt[]" align="right"><? echo $val[csf('qnty')]; //$roll_details_array[$val[csf('roll_id')]]['qnty']; ?></td>
					<td width="60" id="qtyInPcs_<? echo $j; ?>" name="qtyInPcs[]" align="right"><? echo $val[csf('qc_pass_qnty_pcs')]*1; //$roll_details_array[$val[csf('roll_id')]]['qnty']; ?></td>
					<td width="60" id="collarCuffSize_<? echo $j; ?>" name="collarCuffSize[]" align="right"><? echo $roll_details_array[$val[csf('barcode_no')]]['coller_cuff_size']; ?></td>


					<td width="100" id="job_<? echo $j; ?>"><? echo $job_no ?></td>
					<td width="50" id="year_<? echo $j; ?>" align="center"><? echo $year; ?></td>
					<td width="65" id="buyer_<? echo $j; ?>"><? echo $buyer_name_array[$buyer_name]; ?></td>
					<td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $order_no;?></td>
					<td width="80" id="intRef_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $int_ref;?></td>
					<td width="70" id="file_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $file_no; ?></td>

					<td width="90" id="cons_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$val[csf('barcode_no')]]['knitting_company']; ?></td>
					<td width="60" id="mc_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$val[csf('barcode_no')]]['machine_no_id']; ?></td>
					<td width="90" id="comps_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $booking_no; ?></td>
					<td id="gsm_<? echo $j; ?>">
						<?
						$total_roll_wight+=$val[csf('qnty')];
						$total_roll_qtyInPcs+=$val[csf('qc_pass_qnty_pcs')]*1;
						$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");
						echo $receive_basis[$roll_details_array[$val[csf('barcode_no')]]['receive_basis']]; ?>
					</td>

					<input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="0" />
					<input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $j; ?>" value="0" />
					<input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $val[csf('roll_id')]; ?>" />
					<input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['roll_no']; ?>" />
					<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $val[csf('body_part_id')];//$roll_details_array[$val[csf('barcode_no')]]['body_part_id']; ?>"/>
					<input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['color_id']; ?>" />
					<input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['deter_id']; ?>"/>
					<input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $val[csf('prod_id')];//echo $roll_details_array[$val[csf('barcode_no')]]['prod_id']; ?>" />
					<input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $val[csf('po_breakdown_id')]; ?>" />
					<input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $buyer_name; ?>"/>
					<?
					if($val[csf('booking_without_order')]==1)
					{
						?>
						<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<?  ?>"/>
						<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $booking_no; ?>"/>
						<?
					}
					else
					{
						?>
						<input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $po_details_array[$val[csf('po_breakdown_id')]]['job_no_full']; ?>"/>
						<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? //echo $book_no; ?>"/>
						<?
					}
					?>
					<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $j; ?>" value="<? echo $val[csf('booking_without_order')]; ?>"/>
					<input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $val[csf('qnty')];//$roll_details_array[$val[csf('roll_id')]]['qnty']; ?>"/>
					<input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $j; ?>" value="<? echo $val[csf('qc_pass_qnty_pcs')]*1; ?>"/>
					<input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['width']; ?>"/>
					<input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['gsm']; ?>"/>
					<input type="hidden" name="fabricId[]" id="fabricId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['dtls_id']; ?>"/>
					<input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['receive_basis']; ?>"/>
					<input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['knitting_source']; ?>"/>
					<input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['knitting_company_id']; ?>"/>
					<input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['barcode_no']; ?>"/>

					<input type="hidden" name="isSales[]" id="isSales_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['is_sales']; ?>"/>
				</tr>
				<?
				$j++;
				$heat_set_rcv_count++;
			}
		}

		if($heat_set_rcv_count==0)
		{
			echo "Grey Roll Receive From Process not found";
		}
		?>
		<table cellpadding="0" cellspacing="0" width="1410" border="1" id="scanning_tbl" rules="all" class="rpt_table">
			<tfoot>
				<tr>
					<th colspan="9">Total</th>
					<th id="total_calculate_qty_id"><? echo number_format($total_roll_wight,2); ?></th>
					<th id="total_calculate_qtyInPcs_id"><? echo $total_roll_qtyInPcs; ?></th>
                    <th colspan="11"></th>
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
		echo create_drop_down( "cbo_knitting_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "--Select Knit Company--", 1, "",1 );
	}
	else
	{
		echo create_drop_down( "cbo_knitting_company", 152, $blank_array,"",1, "--Select Knit Company--", 1, "",1 );
	}
	exit();
}

if($action=="load_php_form_update")
{
	$sql=sql_select("select  a.id,a.recv_number_prefix_num,a.recv_number,a.receive_date, a.challan_no, a.recv_number,a.company_id,a.batch_no, a.receive_basis,a.dyeing_source,a.dyeing_company,a.receive_date
		from  inv_receive_mas_batchroll a
		where  a.id=$data ");
	//echo $sql;die;
	foreach($sql as $val)
	{
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n";
		echo "document.getElementById('txt_batch_no').value  = '".($val[csf("batch_no")])."';\n";
		echo "document.getElementById('txt_delivery_date').value  = '".change_date_format($val[csf("receive_date")])."';\n";
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("dyeing_source")])."';\n";
		echo "load_drop_down( 'requires/grey_feb_receive_batch_entry_controller', '".$val[csf("dyeing_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n";
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("dyeing_company")])."';\n";
		echo "document.getElementById('cbo_basis').value  = '".($val[csf("receive_basis")])."';\n";
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";

		exit();
	}
}

if($action=="load_php_form")
{
	$sql=sql_select("select  a.issue_number, a.challan_no, a.order_id,a.company_id, a.batch_no,a.issue_purpose,a.issue_basis,a.knit_dye_source,a.knit_dye_company,a.issue_number_prefix_num,a.issue_date
		from inv_issue_master a
		where  a.entry_form=61 and a.issue_number='$data' ");//and a.knit_dye_source=1
	foreach($sql as $val)
	{
		echo "document.getElementById('txt_issue_challan_no').value  = '".($val[csf("issue_number")])."';\n";
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n";
		echo "document.getElementById('txt_batch_no').value  = '".($val[csf("batch_no")])."';\n";
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("knit_dye_source")])."';\n";
		echo "load_drop_down( 'requires/grey_feb_receive_batch_entry_controller', '".$val[csf("knit_dye_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n";
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("knit_dye_company")])."';\n";
		echo "document.getElementById('cbo_basis').value  = '".($val[csf("issue_purpose")])."';\n";
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
		echo "document.getElementById('txt_challan_no').value  = '';\n";
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
							<th>Working Company</th>
							<th>Delivery Date Range</th>
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
								echo create_drop_down( "cbo_lc_company_name", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, "fn_disable_com(1)" );
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
								$search_by_arr=array(1=>"System No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_lc_company_name').value, 'create_challan_search_list_view', 'search_div', 'grey_feb_receive_batch_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$lc_company_id = $data[5];

	if($lc_company_id!=0 && $lc_company_id!="" )
	{
		$lc_company_cond = "and a.company_id=$lc_company_id";
	}

	if($company_id!=0 && $company_id!="" )
	{
		$working_company_cond = "and a.knit_dye_company=$company_id";
	}

	if($company_id==0 && $lc_company_id == 0) { echo "Please Select Working Company or Lc Company."; die; }

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
		$year_field=" YEAR(insert_date) as year";
	}
	else if($db_type==2)
	{
		$year_field=" to_char(insert_date,'YYYY') as year";
	}
	else $year_field="";

	$sql="select  a.id,a.issue_number, a.challan_no, a.order_id,a.company_id,a.issue_purpose,a.issue_basis,a.knit_dye_source,a.knit_dye_company,
	a.issue_number_prefix_num,a.issue_date,$year_field
	from inv_issue_master a
	where a.entry_form=61 $working_company_cond $lc_company_cond  $search_field_cond $date_cond ";
	//and a.knit_dye_source=1
	$result = sql_select($sql);
	foreach ($result as $row)
	{
		$issue_id_arr[] = $row[csf("id")];
		$issue_number_arr[] = "'".$row[csf("issue_number")]."'";

	}

	$iss_qty_arr=array();
	$challan_barcode=array();
	$inserted_barcode=array();
	if(!empty($issue_number_arr)){
		$issue_cond = (!empty($issue_id_arr))?" and a.id in(".implode(",",$issue_id_arr).")":"";
		$data_array=sql_select("SELECT a.id, a.issue_number, c.barcode_no, c.qnty FROM inv_issue_master a, pro_roll_details c
			WHERE a.id=c.mst_id and c.entry_form=61 and a.entry_form=61 and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $issue_cond");
		foreach($data_array as $val)
		{
			$challan_barcode[$val[csf('issue_number')]][]=$val[csf('barcode_no')];
			$iss_qty_arr[$val[csf('id')]]+=$val[csf('qnty')];
		}

		$challan_cond = (!empty($issue_number_arr))?" and b.challan_no in(".implode(",",$issue_number_arr).")":"";
		$inserted_roll=sql_select("select b.challan_no,a.barcode_no from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=62 and b.entry_form=62 $challan_cond");
		foreach($inserted_roll as $b_id)
		{
			$inserted_barcode[$b_id[csf('challan_no')]][]=$b_id[csf('barcode_no')];
		}
	}


	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="140">Company</th>
			<th width="140">Working Company</th>
			<th width="70">System No</th>
			<th width="60">Year</th>
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
				if(count($challan_barcode[$row[csf('issue_number')]])-count($inserted_barcode[$row[csf('issue_number')]])>0)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$knit_comp="&nbsp;";
					if($row[csf('knit_dye_source')]==1) $knit_comp=$company_arr[$row[csf('knit_dye_company')]];
					else $knit_comp=$supllier_arr[$row[csf('knit_dye_company')]];

					$iss_qty=$iss_qty_arr[$row[csf('id')]];
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('issue_number')]; ?>','<? echo $row[csf('id')]; ?>');">
						<td width="40"><? echo $i; ?></td>
						<td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
						<td width="140"><p><? echo $knit_comp; ?></p></td>
						<td width="70"><p>&nbsp;<? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
						<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
						<td width="120"><p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?>&nbsp;</p></td>
						<td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
						<td align="center" width="75"><? echo change_date_format($row[csf('issue_date')]); ?></td>
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
								<? echo create_drop_down( "cbo_company_id", 170,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>
							</td>
							<td align="center">
								<?  echo create_drop_down( "cbo_dyeing_source", 100, $knitting_source, "", 1, "-- Select --", $selected, "load_drop_down( 'grey_feb_receive_batch_entry_controller', this.value+'**'+$('#cbo_company_id').val(), 'load_drop_down_knit_com', 'dyeing_company_td' );","","1,3" ); ?>
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
								<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_check_company();show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_receive_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_dyeing_source').value+'_'+document.getElementById('cbo_dyeing_company').value, 'create_update_search_list_view', 'search_div', 'grey_feb_receive_batch_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$sql="SELECT a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date, $year_field,sum(b.qc_pass_qnty) as qc_pass_qnty, c.color_id
	from inv_receive_mas_batchroll a,pro_roll_details b,pro_grey_batch_dtls c
	where a.id=b.mst_id and a.id=c.mst_id and c.id=b.dtls_id and a.entry_form=62 and c.is_deleted=0 and c.status_active=1 and a.is_deleted=0 and a.status_active=1 $company_condi $receiv_cond $date_cond $year_cond $knit_company_con
	group by a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source,a.dyeing_company, a.receive_date,a.insert_date, c.color_id order by a.recv_number_prefix_num, a.receive_date";

	$result = sql_select($sql);
	foreach ($result as $key => $row)
	{
		$recv_data_arr[$row[csf('recv_number')]]['id']=$row[csf('id')];
		$recv_data_arr[$row[csf('recv_number')]]['recv_number']=$row[csf('recv_number')];
		$recv_data_arr[$row[csf('recv_number')]]['recv_number_prefix_num']=$row[csf('recv_number_prefix_num')];
		$recv_data_arr[$row[csf('recv_number')]]['receive_date']=$row[csf('receive_date')];
		$recv_data_arr[$row[csf('recv_number')]]['company_id']=$row[csf('company_id')];
		$recv_data_arr[$row[csf('recv_number')]]['dyeing_source']=$row[csf('dyeing_source')];
		$recv_data_arr[$row[csf('recv_number')]]['dyeing_company']=$row[csf('dyeing_company')];
		$recv_data_arr[$row[csf('recv_number')]]['year']=$row[csf('year')];
		$recv_data_arr[$row[csf('recv_number')]]['challan_no']=$row[csf('challan_no')];
		$recv_data_arr[$row[csf('recv_number')]]['color_id'].=$row[csf('color_id')].',';
		$recv_data_arr[$row[csf('recv_number')]]['qc_pass_qnty']+=$row[csf('qc_pass_qnty')];
	}

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
			<th width="100">Fabric Color</th>
			<th width="140">Dyeing Company</th>
			<th width="80">Receive date</th>
			<th width="100">Recv Qty</th>
			<th width="">Issue Challan</th>
		</thead>
	</table>
	<div style="width:970px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($recv_data_arr as $recv_number => $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				$knit_comp="&nbsp;";
				if($row['dyeing_source']==1)
					$knit_comp=$company_arr[$row['dyeing_company']];
				else
					$knit_comp=$supllier_arr[$row['dyeing_company']];
				$all_color_id_arr=array_unique(explode(",", chop($row['color_id'],",")));
				$color_name = "";
                foreach ($all_color_id_arr as $cid)
                {
                    $color_name .= ($color_name =="") ? $color_arr[$cid] :  ",". $color_arr[$cid];
                }
                $color_name =implode(",",array_filter(array_unique(explode(",", $color_name))));
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $recv_number; ?>','<? echo $row['id']; ?>','<? echo $row['challan_no']; ?>','<? echo change_date_format($row['receive_date']); ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="140"><p><? echo $company_arr[$row['company_id']]; ?></p></td>
					<td width="80"><p>&nbsp;<? echo $row['recv_number_prefix_num']; ?></p></td>
					<td width="70" align="center"><p><? echo $row['year']; ?></p></td>
					<td width="120"><p><? echo $knitting_source[$row['dyeing_source']]; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $color_name; ?>&nbsp;</p></td>
					<td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
					<td width="80" align="center"><? echo change_date_format($row['receive_date']); ?></td>
					<td width="100" align="right"><p><? echo number_format($row['qc_pass_qnty'],2); ?>&nbsp;</p></td>
					<td width="" align="right"><p><? echo $row['challan_no']; ?>&nbsp;</p></td>
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

if($action=="fabric_details_tg_print")
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
	$mc_sql=sql_select( "select id, machine_no, dia_width, gauge from lib_machine_name where status_active=1");
	foreach($mc_sql as $row)
	{
		$machine_arr[$row[csf('id')]]['no']=$row[csf('machine_no')];
		$machine_arr[$row[csf('id')]]['dia']=$row[csf('dia_width')];
		$machine_arr[$row[csf('id')]]['gauge']=$row[csf('gauge')];
	}
	$brand_details=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");


	$sql_update=sql_select("SELECT b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id, b.febric_description_id,b.gsm,b.width,count(b.roll_id) as roll_id, b.buyer_id,b.order_id,b.color_id,a.challan_no,c.id as update_roll_id, c.po_breakdown_id,c.barcode_no, sum(c.qnty) as roll_wgt,c.roll_no,c.is_sales,c.booking_without_order, sum(c.qc_pass_qnty_pcs) as qc_pass_qnty_pcs from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62 and a.id=$update_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id, b.febric_description_id,b.gsm,b.width, b.buyer_id,b.order_id,b.color_id,a.challan_no,c.id , c.po_breakdown_id,c.barcode_no, c.roll_no,c.is_sales,c.booking_without_order ");
	
	// echo "SELECT b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id, b.febric_description_id,b.gsm,b.width,count(b.roll_id) as roll_id, b.buyer_id,b.order_id,b.color_id,a.challan_no,c.id as update_roll_id, c.po_breakdown_id,c.barcode_no, sum(c.qnty) as roll_wgt,c.roll_no,c.is_sales,c.booking_without_order, sum(c.qc_pass_qnty_pcs) as qc_pass_qnty_pcs from pro_grey_batch_dtls b,inv_receive_mas_batchroll a,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.entry_form=62 and a.id=$update_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id, b.febric_description_id,b.gsm,b.width, b.buyer_id,b.order_id,b.color_id,a.challan_no,c.id , c.po_breakdown_id,c.barcode_no, c.roll_no,c.is_sales,c.booking_without_order ";die;
	$main_data_arr = array();
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

		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['knitting_company'] = $row[csf("knitting_company")];
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['is_sales'] = $row[csf("is_sales")];
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['knitting_source'] = $row[csf("knitting_source")];
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['barcode_no'] .= $row[csf("barcode_no")].',';
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['booking_without_order'] = $row[csf("booking_without_order")];
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['po_breakdown_id'] = $row[csf("po_breakdown_id")];
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['roll_no'] += $row[csf("roll_no")];
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['roll_wgt'] += $row[csf("roll_wgt")];
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['qc_pass_qnty_pcs'] += $row[csf("qc_pass_qnty_pcs")];
		$main_data_arr[$row[csf("color_id")]][$row[csf("body_part_id")]][$row[csf("febric_description_id")]][$row[csf("gsm")]][$row[csf("width")]]['no_of_roll'] += $row[csf("roll_id")];
	}

	// echo "<pre>";print_r($main_data_arr);

	if(!empty($barcode_arr)){

		$data_array=sql_select("SELECT a.id,a.receive_basis, a.booking_no,a.booking_id, c.barcode_no,b.stitch_length,b.yarn_count,b.yarn_lot,b.brand_id,
			b.machine_no_id,b.color_range_id,a.entry_form,c.coller_cuff_size, c.qc_pass_qnty_pcs FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) and c.barcode_no in(".implode(",",$barcode_arr).") order by c.id desc");
			// echo "SELECT a.id,a.receive_basis, a.booking_no,a.booking_id, c.barcode_no,b.stitch_length,b.yarn_count,b.yarn_lot,b.brand_id,
			// b.machine_no_id,b.color_range_id,a.entry_form,c.coller_cuff_size FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and a.booking_without_order in(0,1) and c.barcode_no in(".implode(",",$barcode_arr).") order by c.id desc";
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
			$roll_details_array[$row[csf("barcode_no")]]['coller_cuff_size'] =$row[csf("coller_cuff_size")];
			$roll_details_array_new[$row[csf("coller_cuff_size")]]['qnty'] += $row[csf("qc_pass_qnty_pcs")];
			

			if($row[csf("entry_form")] == 2 && $row[csf("receive_basis")] == 2)
			{
				$booking_no_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
			}

		}
		// echo "<pre>";print_r($roll_details_array_new);
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
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1730" class="rpt_table" >
			<thead>
				<tr style="font-size:13px">
					<th width="30">SL</th>
					<th width="150">Order /File</th>
					<th width="100">Buyer/Job</th>
					<th width="150">Booking No/Internal reff</th>
					<th width="90">Body Part</th>
					<th width="130">Item Description</th>
					<th width="90">Color Range</th>
					<th width="50">Stich Length</th>
					<th width="70">Fin. GSM</th>
					<th width="60">Fin. Dia</th>
					<th width="70">M/C Dia</th>
					<th width="70">M/C Gauge</th>
					<th width="120">Color</th>
					<th width="50">No of Roll</th>
					<th width="50">UOM</th>
					<th width="40">Count</th>
					<th width="80">Brand</th>
					<th width="80">Yarn Lot</th>
					<th width="80">Size</th>
					<th width="60">Rcv Qty. In Pcs</th>
					<th>Rcv Qty</th>
				</tr>
			</thead>
			<?
			$i=1;
			$tot_qc_pass_qnty_pcs=0;
			$tot_qty=0;
			$tot_roll=0;
			$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");

			foreach($main_data_arr as $k_color_id=>$v_color_id)
			{
				$color_tot_qc_pass_qnty_pcs=0;
				$color_tot_qty=0;
				$color_tot_roll=0;
				foreach($v_color_id as $k_body_part_id=>$v_body_part_id)
				{
					$body_tot_qc_pass_qnty_pcs=0;
					$body_tot_qty=0;
					$body_tot_roll=0;
					foreach($v_body_part_id as $k_febric_des_id=>$v_febric_des_data)
					{
						foreach($v_febric_des_data as $k_gsm=>$v_gsm)
						{
							foreach($v_gsm as $k_width=>$row)
							{
								//var_dump($row);
								$is_sales = $row["is_sales"];
								$barcode_nos = array_unique(explode(",",chop($row['barcode_no'],",")));
								//var_dump($barcode_nos);

								if($is_sales == 1)
								{
									$without_order=$row['booking_without_order'];
									$within_group = $sales_arr[$row["po_breakdown_id"]]["within_group"];
									$booking_no = $sales_arr[$row["po_breakdown_id"]]["sales_booking_no"];
									$order_file='O :'.$sales_arr[$row["po_breakdown_id"]]["po_number"].'<hr> F :';
									if($without_order == 2)
									{
										$buyer_id=return_field_value("buyer_id","wo_non_ord_samp_booking_mst","booking_no='$booking_no'");
										$order_file="";
										$order_file='O :'.'<hr> F :';
										$buyer_job="";
										$buyer_job='B :'.$buyer_array[$buyer_id].'<hr> J :';
										$int_ref='';
									}
									else
									{
										if($within_group == 1)
										{
											$buyer_job='B :'.$buyer_array[$job_arr[$booking_no]["buyer_name"]].'<hr> J :'.$job_arr[$booking_no]["job_no_full"];
											$int_ref = $job_arr[$booking_no]["int_ref"];
										}
										else
										{
											$buyer_job='B :'.$buyer_array[$job_arr[$row["po_breakdown_id"]]["buyer_name"]].'<hr> J :'.$job_arr[$row["po_breakdown_id"]]["job_no_full"];
											$int_ref='';
										}
									}
								}
								else
								{
									$order_file="";
									$buyer_job="";
									foreach ($barcode_nos as $barcode_no)
									{
										if($barcode_ref_arr[$barcode_no]['booking_without_order'] ==0 || $barcode_ref_arr[$barcode_no]['booking_without_order'] =='')
										{
											$order_file .='O :'.$job_arr[$row['po_breakdown_id']]['po_no'].'<hr> F :'.$job_arr[$row['po_breakdown_id']]['file_no'].',';

											$buyer_job .='B :'.$buyer_array[$job_arr[$row["po_breakdown_id"]]["buyer_name"]].'<hr> J :'.$job_arr[$row["po_breakdown_id"]]["job_no_full"].',';
										}
									}

									$int_ref=$job_arr[$row['po_breakdown_id']]['int_ref'];
								}
								$yarn_lots='';
								$brand_names='';
								$yarn_counts='';
								$machine_no_ids='';
								$stitch_lengths='';
								$color_range_name='';
								$coller_cuff_size='';
								foreach ($barcode_nos as $barcode_no)
								{

									if($roll_details_array[$barcode_no]['receive_basis']== 2 && $roll_details_array[$barcode_no]['entry_form'] ==2)
									{
										$program_no = $roll_details_array[$barcode_no]['booking_no'];
										if($barcode_ref_arr[$barcode_no]['booking_without_order'] == 1)
										{
											$booking_number = $order_to_sample_data[$barcode_no]["booking_no"];
										}
										else
										{
											$booking_number = $booking_array[$program_no]['booking_no'];
										}


										$book_program_str =  "B: ".$booking_number;
									}
									else if($roll_details_array[$barcode_no]['receive_basis']== 1 && $roll_details_array[$barcode_no]['entry_form'] ==2)
									{
										if($barcode_ref_arr[$barcode_no]['booking_without_order'] == 1)
										{
											if($order_to_sample_data[$barcode_no]["booking_no"] =="")
											{
												$booking_number = $roll_details_array[$barcode_no]['booking_no'];
											}
											else
											{
												$booking_number = $order_to_sample_data[$barcode_no]["booking_no"];
											}
										}
										else
										{
											$booking_number = $roll_details_array[$barcode_no]['booking_no'];
										}

										$book_program_str = "B: ".$booking_number;
									}


									$yarn_lots .= $roll_details_array[$barcode_no]['yarn_lot'].',';
									$brand_names .= $brand_details[$roll_details_array[$barcode_no]['brand_id']].',';
									$yarn_counts .= $yarn_count_details[$roll_details_array[$barcode_no]['yarn_count']].',';
									$machine_no_ids .= $roll_details_array[$barcode_no]['machine_no_id'].',';
									$stitch_lengths .= $roll_details_array[$barcode_no]['stitch_length'].',';
									$color_range_name .= $color_range[$roll_details_array[$barcode_no]['color_range_id']].',';
									$coller_cuff_size .= $roll_details_array[$barcode_no]['coller_cuff_size'].',';
								}
								?>
								<tr style="font-size:13px">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="150" style="word-break:break-all;" title="<? echo "==".$row['po_breakdown_id'];?>" align="center"><? echo $order_file; ?></td>
									<td width="100" style="word-break:break-all;" align="center"><? echo $buyer_job; ?><? //echo $int_ref; ?></td>
									<td width="150" align="center" style="word-break:break-all;">
										<? echo $book_program_str."<hr/>IR:".$int_ref;	 ?>
									</td>
									<td width="90" style="word-break:break-all;" align="center">
										<? echo $body_part[$k_body_part_id]; ?>
									</td>
									<td width="130" align="center" style="word-break:break-all;"><? echo $composition_arr[$k_febric_des_id]; ?></td>
									<td width="90" align="center" style="word-break:break-all;">
										<?
											echo implode(",",array_unique(explode(",",chop($color_range_name,","))));
										?>
									</td>
									<td width="50" style="word-break:break-all;" align="center">
										<?
											echo implode(",",array_unique(explode(",",chop($stitch_lengths,","))));
										?>
									</td>
									<td width="70" style="word-break:break-all;" align="center"><? echo $k_gsm; ?></td>
									<td width="60" style="word-break:break-all;" align="center"><? echo $k_width; ?></td>
									<td width="70" style="word-break:break-all;" align="center">
										<?
											$machine_no_arr= array_unique(explode(",",chop($machine_no_ids,",")));
											$machine_daya='';
											$machine_gauge='';
											foreach ($machine_no_arr as $machine_no)
											{
												$machine_daya .= $machine_arr[$machine_no]['dia'].',';
												$machine_gauge .= $machine_arr[$machine_no]['gauge'].',';
											}
											
											$machine_daya_str = implode(",",array_unique(explode(",",chop($machine_daya,","))));
											$machine_gauge_str = implode(",",array_unique(explode(",",chop($machine_gauge,","))));
											echo trim($machine_daya_str, ",");
										?>
									</td>
									<td width="70" style="word-break:break-all;" align="center"><? echo trim($machine_gauge_str, ",");  ?></td>
									<td width="120" style="word-break:break-all;" align="center"><? echo $color_arr[$k_color_id]; ?><? //echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
									<td width="50" style="word-break:break-all;" align="right"><? echo $row["no_of_roll"]; ?></td>
									<td width="50" style="word-break:break-all;" align="center"><? //echo $row[csf('gsm')]; ?></td>
									<td width="40" style="word-break:break-all;" align="center">
										<?
											echo implode(",",array_unique(explode(",",chop($yarn_counts,","))));
										?>
									</td>
									<td width="80" style="word-break:break-all;" align="center">
										<?
											echo implode(",",array_unique(explode(",",chop($brand_names,","))));
										?>
									</td>
									<td width="80" align="center" style="word-break:break-all;">
										<?
											echo implode(",",array_unique(explode(",",chop($yarn_lots,","))));
										?>
									</td>
									<td width="80" align="center" style="word-break:break-all;">
										<?
											$size_arr = array_unique(explode(",",chop($coller_cuff_size,",")));
											// echo "<pre>";
											// print_r($size_arr);
											// echo "</pre>";
											$inner_row = 0;
											foreach($size_arr as $size)
											{
												?><table><tr><td><? echo $size;?></td></tr></table><?
											}
										?>
									</td>
									<td width="60" align="right" style="word-break:break-all;">
											<table>
											<? 
											
											foreach($size_arr as $size)
											{
												?>
												<tr>
													<td>
														<? echo $roll_details_array_new[$size]['qnty']; ?>
													</td>
												</tr>
												<?
												$tot_qc_pass_qnty_pcs += $roll_details_array_new[$size]['qnty'];
												$body_tot_qc_pass_qnty_pcs += $roll_details_array_new[$size]['qnty'];
												$color_tot_qc_pass_qnty_pcs += $roll_details_array_new[$size]['qnty'];
											} 
											
											?>
											
										<? //echo number_format($row['qc_pass_qnty_pcs'],2); ?>
										</table>
									</td>
									<td align="right" style="word-break:break-all;"><? echo number_format($row['roll_wgt'],2); ?></td>
								</tr>
								<?
								$color_tot_roll+=$row['no_of_roll'];
								// $color_tot_qc_pass_qnty_pcs+=$row['qc_pass_qnty_pcs'];
								$color_tot_qty+=$row['roll_wgt'];

								$body_tot_roll+=$row['no_of_roll'];
								// $body_tot_qc_pass_qnty_pcs+=$row['qc_pass_qnty_pcs'];
								$body_tot_qty+=$row['roll_wgt'];

								$tot_roll+=$row['no_of_roll'];
								// $tot_qc_pass_qnty_pcs+=$row['qc_pass_qnty_pcs'];
								$tot_qty+=$row['roll_wgt'];
								$i++;
							}
						}
					}
					?>
					<tr style="font-size:13px; background:#F0F0F4" >
						<td align="right" colspan="13"><strong>Body Part Total:</strong></td>
						<td align="right" width="50"><strong><? echo $body_tot_roll; ?></strong></td>
						<td align="right" width="50"></td>
						<td align="right" width="40"></td>
						<td align="right" width="40"></td>
						<td align="right" width="40"></td>
						<td align="right" width="80"></td>
						<td align="right" width="60"><strong><? echo number_format($body_tot_qc_pass_qnty_pcs,2,'.',''); ?></strong></td>
						<td align="right"><strong><? echo number_format($body_tot_qty,2,'.',''); ?></strong></td>
					</tr>
					<?
				}
				?>
				<tr style="font-size:13px; background:#F0F0F4" >
					<td align="right" colspan="13"><strong>Color Total:</strong></td>
					<td align="right" width="50"><strong><? echo $color_tot_roll; ?></strong></td>
					<td align="right" width="50"></td>
					<td align="right" width="40"></td>
					<td align="right" width="40"></td>
					<td align="right" width="40"></td>
					<td align="right" width="80"></td>
					<td align="right" width="60"><strong><? echo number_format($color_tot_qc_pass_qnty_pcs,2,'.',''); ?></strong></td>
					<td align="right"><strong><? echo number_format($color_tot_qty,2,'.',''); ?></strong></td>
				</tr>
				<?
			}
			?>
			<tr style="font-size:13px; background:#A6A6A6">
				<td align="right" colspan="13"><strong>Grand Total:</strong></td>
				<td align="right" width="50"><strong><? echo $tot_roll; ?></strong></td>
				<td align="right" width="50"></td>
				<td align="right" width="40"></td>
				<td align="right" width="40"></td>
				<td align="right" width="40"></td>
				<td align="right" width="80"></td>
				<td align="right" width="60"><strong><? echo number_format($tot_qc_pass_qnty_pcs,2,'.',''); ?></strong></td>
				<td align="right"><strong><? echo number_format($tot_qty,2,'.',''); ?></strong></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(71, $company, "1210px",""."30px"); ?>
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

?>

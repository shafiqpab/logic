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
			echo create_drop_down( "cbo_service_company", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name",1, "-- Select --", $company_id, "fnc_reset_form(2)","" );
		}
		else if($data[0]==3)
		{	
			echo create_drop_down( "cbo_service_company", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "fnc_reset_form(2)" );
		}
		else
		{
			echo create_drop_down( "cbo_service_company", 152, $blank_array,"",1, "-- Select --", 0, "fnc_reset_form(2)" );
		}
		exit();
	}
	if ($action=="load_drop_down_buyer")
	{
		echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	} 



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

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
		
		//$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FIFP', date("Y",time()), 5, "select recv_number_prefix,recv_number_prefix_num from inv_receive_mas_batchroll where company_id=$cbo_company_id and entry_form=91 and $year_cond=".date('Y',time())." order by id desc","recv_number_prefix","recv_number_prefix_num"));
		$id= return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_RCV_MAS_BATC_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'FIFP',91,date("Y",time()) ));
			//$id=return_next_id( "id", "inv_receive_mas_batchroll", 1 ) ;

		$field_array="id,recv_number_prefix,recv_number_prefix_num,recv_number,receive_basis, entry_form, company_id, dyeing_source, dyeing_company, receive_date,gate_pass_no,do_no,car_no, inserted_by, insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$cbo_basis.",91,".$cbo_company_id.",".$cbo_service_source.",".$cbo_service_company.",".$txt_issue_date.",".$txt_gate_no.",".$txt_do_no.",".$txt_car_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		//
		//$dtls_id=return_next_id("id", "pro_grey_batch_dtls", 1);
		
		$field_array_dtls="id, mst_id, batch_id, prod_id, body_part_id, febric_description_id, gsm, width, color_id, width_dai_type, process_id, batch_wgt, roll_no, batch_issue_qty, buyer_id, job_no, order_id, fin_dia, fin_gsm, remarks,
		outbound_batchname,booking_no,rate,booking_date,booking_without_order,booking_dtls_id,inserted_by,insert_date";
		$all_detailsId='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			$process_id="cboProcess_".$j;
			$deterId="determinationId_".$j;
			$buyerId="buyerId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$batchWgt="batchWgt_".$j;
			$jobNo="jobNo_".$j;
			$colorId="colorId_".$j;
			$gsm="gsm_".$j;
			
			$dia="dia_".$j;
			$batchId="batchId_".$j;
			$bodyparyId="bodypartId_".$j;
			$txtRollNo="txtRollNo_".$j;
			$issueQty="txtIssueQty_".$j;
			$remarks="txtRemarks_".$j;
			$widthTypeId="widthTypeId_".$j;
			$finDia="finDia_".$j;
			$finGsm="finGsm_".$j;
			$outBoundBatchNo="outBoundBatchNo_".$j;
			$bookingNo="bookingNo_".$j;
			$bookingType="bookingType_".$j;
			$bookingDate="bookingDate_".$j;
			$woRate="woRate_".$j;
			$bookingDtls="bookingDtls_".$j;
			$trId="tr_".$j;
			if(str_replace("'","",$$remarks)!='') $remarks=$$remarks;else $remarks="";
			if($$bookingType == 2)
			{
				$booking_without_order = "1";
			}else{
				$booking_without_order = "";
			}

			if($db_type ==0){
				$booking_date = date('Y-M-d',strtotime($$bookingDate));
			}else{
				$booking_date = date('d-M-Y',strtotime($$bookingDate));
			}

			if($$issueQty!="")
			{
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$dtls_id.",".$id.",'".$$batchId."','".$$productId."','".$$bodyparyId."','".$$deterId."','".$$gsm."','".$$dia."','".$$colorId."','".$$widthTypeId."','".$$process_id."','".$$batchWgt."','".$$txtRollNo."','".$$issueQty."','".$$buyerId."','".$$jobNo."','".$$orderId."','".$$finDia."','".$$finGsm."','".$remarks."','".$$outBoundBatchNo."','".$$bookingNo."','".$$woRate."','".$booking_date."','".$booking_without_order."','".$$bookingDtls."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$all_detailsId.=$$trId."__".$dtls_id.",";
				//$dtls_id = $dtls_id+1;
			}
		}
		//echo "5**insert into pro_grey_batch_dtls ($field_array_dtls) values $data_array_dtls";die;
		$rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);
		$rID2=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
       	//echo "$rID == $rID2";die;
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($all_detailsId,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($all_detailsId,0,-1);
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
		
		$field_array="dyeing_source*dyeing_company*receive_date*gate_pass_no*do_no*car_no*updated_by*update_date";
		$data_array=$cbo_service_source."*".$cbo_service_company."*".$txt_issue_date."*".$txt_gate_no."*".$txt_do_no."*".$txt_car_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		//$dtls_id=return_next_id("id", "pro_grey_batch_dtls", 1);
		$field_array_dtls="id, mst_id, batch_id, prod_id, body_part_id, febric_description_id, gsm, width, color_id, width_dai_type, process_id, batch_wgt, roll_no, batch_issue_qty, buyer_id, job_no, order_id, fin_dia, fin_gsm, remarks,
		outbound_batchname,booking_no,rate,booking_date,booking_without_order,booking_dtls_id inserted_by, insert_date";
		$field_array_updatedtls="process_id*batch_issue_qty*roll_no*fin_dia*fin_gsm*remarks*outbound_batchname*updated_by*update_date";		
		
		//booking_no,rate,booking_date,booking_without_order,booking_dtls_id,

		$all_detailsId=''; 
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			$process_id="cboProcess_".$j;
			$deterId="determinationId_".$j;
			$buyerId="buyerId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$batchWgt="batchWgt_".$j;
			$jobNo="jobNo_".$j;
			$colorId="colorId_".$j;
			$gsm="gsm_".$j;
			
			$dia="dia_".$j;
			$batchId="batchId_".$j;
			$bodyparyId="bodypartId_".$j;
			$txtRollNo="txtRollNo_".$j;
			$issueQty="txtIssueQty_".$j;
			$remarks="txtRemarks_".$j;
			$widthTypeId="widthTypeId_".$j;
			$update_dtls="dtlsId_".$j;
			$finDia="finDia_".$j;
			$finGsm="finGsm_".$j;
			$outBoundBatchNo="outBoundBatchNo_".$j;

			$bookingNo="bookingNo_".$j;
			$bookingType="bookingType_".$j;
			$bookingDate="bookingDate_".$j;
			$woRate="woRate_".$j;
			$bookingDtls="bookingDtls_".$j;
			if(str_replace("'","",$$remarks)!='') $remarks=$$remarks;else $remarks="";
			//booking_no,rate,booking_date,booking_without_order,booking_dtls_id,
			//"','".$$bookingNo."','".$$woRate."','".$booking_date."','".$booking_without_order."','".$$bookingDtls

			$trId="tr_".$j;
			if($$issueQty!="")
			{
				if($$update_dtls!="")
				{
					$dtlsId_arr[]=str_replace("'","",$$update_dtls);
					$data_array_update_dtls[str_replace("'","",$$update_dtls)]=explode("*",($$process_id."*'".$$issueQty."'*'".$$txtRollNo."'*'".$$finDia."'*'".$$finGsm."'*'".$remarks."'*'".$$outBoundBatchNo."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$all_detailsId.=$$trId."__".str_replace("'","",$$update_dtls).",";
				}
				else
				{
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$dtls_id.",".$update_id.",'".$$batchId."','".$$productId."','".$$bodyparyId."','".$$deterId."','".$$gsm."','".$$dia."','".$$colorId."','".$$widthTypeId."','".$$process_id."','".$$batchWgt."','".$$txtRollNo."','".$$issueQty."','".$$buyerId."','".$$jobNo."','".$$orderId."','".$$finDia."','".$$finGsm."','".$remarks."','".$$outBoundBatchNo."','".$$bookingNo."','".$$woRate."','".$booking_date."','".$booking_without_order."','".$$bookingDtls."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					$all_detailsId.=$$trId."__".$dtls_id.",";
					//$dtls_id = $dtls_id+1;
				}
				
				$mstUpdate_id_array=array();
				$sql_dtls="Select id from pro_grey_batch_dtls where mst_id=".str_replace("'",'',$update_id)." and status_active=1 and is_deleted=0";
				$nameArray=sql_select( $sql_dtls );
				foreach($nameArray as $row)
				{
					$mstUpdate_id_array[]=$row[csf('id')];
				}
			
				$curr_issueQty=str_replace("'","",$$issueQty);
				$color_Id=str_replace("'","",$$colorId);
				$bodypary_Id=str_replace("'","",$$bodyparyId);
				$deter_Id=str_replace("'","",$$deterId);
				$batch_Wgt=str_replace("'","",$$batchWgt);
				$bookingDtlsId=str_replace("'","",$$bookingDtls);
				$sql_prev="select a.recv_number,b.batch_issue_qty as batch_issue_qty from inv_receive_mas_batchroll a,pro_grey_batch_dtls b where a.id=b.mst_id and a.entry_form=91 and a.status_active=1 and b.status_active=1 and b.booking_no='".$$bookingNo."' and b.booking_dtls_id=".$bookingDtlsId." and b.color_id='".$color_Id."' and body_part_id=".$bodypary_Id." and febric_description_id=".$deter_Id." " ;// body_part_id, febric_description_id
				$Prev_nameArray=sql_select( $sql_prev );
				$prev_tot_batch_issue_qty=0;$recv_number_arr=array();
				foreach($Prev_nameArray as $row)
				{
					$prev_tot_batch_issue_qty+=$row[csf('batch_issue_qty')];
					$recv_number_arr[$row[csf('recv_number')]]=$row[csf('recv_number')];
				}
				//$tot_prev=$prev_tot_batch_issue_qty+$curr_issueQty;
				
				if(($prev_tot_batch_issue_qty>$batch_Wgt)){
					echo "Previous**".str_replace("'","",$$bookingNo)."**".implode(", ",$recv_number_arr)."**".$prev_tot_batch_issue_qty."**".$$batchWgt;
					die;
				}
				//echo "10**".$prev_tot_batch_issue_qty."**".$batch_Wgt."**".$prev_tot_batch_issue_qty;die;
			}
		}
		
		if(implode(',',$dtlsId_arr)!="")
		{
			$distance_delete_id=array_diff($mstUpdate_id_array,$dtlsId_arr);
		}
		else
		{
			$distance_delete_id=$mstUpdate_id_array;
		}
		//echo "10**".print_r($distance_delete_id);die;
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if(implode(',',$distance_delete_id)!="")
		{
			foreach($distance_delete_id as $id_val)
			{
				//$rID=true;
				$rID3=sql_update("pro_grey_batch_dtls",$field_array_del,$data_array_del,"id","".$id_val."",1);
				//if($rID) $flag=1; else $flag=0;
			}
		}
		//print_r($field_array_up);

		
		$rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
		$rID2=true; $rID3=true;
		if($data_array_dtls!="")
		{
			$rID2=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
		}
		//echo "10**insert into pro_grey_batch_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		//echo bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr );die;
		if(count($data_array_update_dtls)>0)
		{
			$rID3=execute_query(bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_updatedtls, $data_array_update_dtls, $dtlsId_arr ));
		}

		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_issue_no)."**".substr($all_detailsId,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_issue_no)."**".substr($all_detailsId,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if ($db_type == 0)
		{
			mysql_query("BEGIN");
		}
		$booking_dtls_arr=array();$booking_nos="";
		$all_detailsId="";
		for($j=1;$j<=$tot_row;$j++)
		{
			$bookingNo="bookingNo_".$j;
			//$bookingDtls="bookingDtls_".$j;
			$booking_No=str_replace("'","",$$bookingNo);
			$booking_nos.="'".$booking_No."'".",";
			$issueQty="txtIssueQty_".$j;
			$update_dtls="dtlsId_".$j;
			$trId="tr_".$j;
			$all_detailsId.=$$trId."__".str_replace("'","",$$update_dtls).",";
			
			
		}
		$booking_nos=rtrim($booking_nos,',');
		$booking_nos=implode(",",array_unique(explode(",",$booking_nos)));
		$sql = "select min(a.recv_number) as recv_number from inv_receive_mas_batchroll a,pro_grey_batch_dtls b where a.id=b.mst_id  and a.entry_form in(92) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.booking_no in($booking_nos)";
		$data_array = sql_select($sql);
		$recv_number=$data_array[0][csf('recv_number')];
		if ($recv_number!="")
		{
			echo "13**Fabric Service Recv. Found=" .$recv_number;
			die;
		}
		//,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."'
		$rID=execute_query( "update  inv_receive_mas_batchroll set status_active=0,is_deleted=1,update_date='".$pc_date_time."',updated_by=".$_SESSION['logic_erp']['user_id']."  where  id =$update_id ",1);
		$rID2=execute_query( "update  pro_grey_batch_dtls set status_active=0,is_deleted=1,update_date='".$pc_date_time."',updated_by=".$_SESSION['logic_erp']['user_id']."   where  mst_id =$update_id ",1);
			
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_issue_no)."**".substr($all_detailsId,0,-1);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);  
				echo "2**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_issue_no)."**".substr($all_detailsId,0,-1);
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		disconnect($con);
		die;
		
	}
}


if($action=="grey_item_details_update")
{
	$data=explode("_",$data); 
	$datas=explode("*",$data[0]);
	$floor_name_array=return_library_array( "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1", "id", "floor_name");
	
	$sql=sql_select("select b.id, b.mst_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.buyer_id, b.job_no, b.order_id, b.width_dai_type, b.color_range_id, b.batch_id,a.dyeing_source, a.batch_no, b.color_id, b.process_id, b.batch_wgt, b.batch_issue_qty, b.outbound_batchname, b.fin_dia, b.fin_gsm, b.roll_no,a.receive_basis, b.booking_no,b.booking_dtls_id, b.booking_date,b.booking_without_order,b.rate, b.remarks 
		from pro_grey_batch_dtls b, inv_receive_mas_batchroll a where b.mst_id=$datas[0] and b.mst_id=a.id and a.entry_form=91 and b.status_active=1 and b.is_deleted=0 order by b.id"  );

	// echo "select b.id, b.mst_id, b.prod_id, b.body_part_id, b.febric_description_id, b.gsm, b.width, b.buyer_id, b.job_no, b.order_id, b.width_dai_type, b.color_range_id, b.batch_id, a.batch_no, b.color_id, b.process_id, b.batch_wgt, b.batch_issue_qty, b.outbound_batchname, b.fin_dia, b.fin_gsm, b.roll_no,a.receive_basis, b.booking_no,b.booking_dtls_id, b.booking_date,b.booking_without_order,b.rate, b.ramarks 
	// 	from pro_grey_batch_dtls b, inv_receive_mas_batchroll a where b.mst_id=$datas[0] and b.mst_id=a.id and a.entry_form=91 and b.status_active=1 and b.is_deleted=0 order by b.id";die;
	
	$body_part_ids="";$order_ids="";$prod_ids="";$color_ids="";$booking_nos="";$febric_description_ids="";$booking_dtls_id="";
	foreach($sql as $row)
	{
		$booking_without_order_status=$row[csf('booking_without_order')];
		$body_part_ids.=$row[csf('body_part_id')].',';
		$order_ids.=$row[csf('order_id')].',';
		$prod_ids.=$row[csf('prod_id')].',';
		$color_ids.=$row[csf('color_id')].',';
		$booking_dtls_id.=$row[csf('booking_dtls_id')].',';
		$booking_nos.="'".$row[csf('booking_no')]."'".',';
		$febric_description_ids.="'".$row[csf('febric_description_id')]."'".',';
		
	}

	$body_part_id_all=chop($body_part_ids,",");
	$order_id_all=chop($order_ids,",");
	$prod_id_all=chop($prod_ids,",");
	$color_id_all=chop($color_ids,",");
	$booking_no_all=chop($booking_nos,",");
	$booking_dtls_id=chop($booking_dtls_id,",");
	$febric_description_id_all=chop($febric_description_ids,",");
	//-----

	$color_arr = return_library_array("select id, color_name from lib_color where id in($color_id_all)","id","color_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$order_arr = return_library_array("select id, po_number from wo_po_break_down where id in($order_id_all)","id","po_number");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($febric_description_id_all)";
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

	
	$feb_des_data = sql_select("select b.id as dtls_id, a.wo_no as booking_no,b.fabric_description as fab_des_id,b.fabric_source
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c where a.id = b.wo_id and b.fab_booking_no = c.booking_no and a.company_id = $data[1] and  a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and c.status_active =1 and a.wo_no in($booking_no_all) and b.fabric_description in($febric_description_id_all)  
		union all
		select b.id as dtls_id, a.booking_no, b.fab_des_id,b.fabric_source
		from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c 
		where a.id = b.mst_id and a.fab_booking_id =  c.id and a.company_id = $data[1]  and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and c.status_active =1 and a.booking_no in($booking_no_all) and b.fab_des_id in($febric_description_id_all)");
	$feb_description_datas="";
	foreach ($feb_des_data as $value) 
	{
		$feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["fabric_source"]= $value[csf("fabric_source")];
		$feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["feb_des_id"]= $value[csf("fab_des_id")];
		$feb_description_datas.=$value[csf("fab_des_id")].',';
	}

	$feb_description_data=chop($feb_description_datas,',');
	if($feb_description_data!=""){$feb_description_data_cond=" and a.id in($feb_description_data)";}else{$feb_description_data_cond="";}
	if($feb_description_data!=""){$feb_description_data_cond_2=" and c.id in($feb_description_data)";}else{$feb_description_data_cond_2="";}


	$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13 and id in($prod_id_all)",'id','product_name_details');

	if($febric_description_id_all!="")
	{
		$sql_order_feb=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 and c.id in($febric_description_id_all) $feb_description_data_cond_2 order by c.id");
		foreach($sql_order_feb as $row)
		{
			$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
		}
	}

	if($datas[1]==1 && $febric_description_id_all!="")
	{
		$sql_non_order=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in($febric_description_id_all) $feb_description_data_cond order by a.id");
		
		//echo "select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $feb_description_data_cond order by a.id";
		foreach($sql_non_order as $row)
		{
			$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
		}
	}

	//==============================================

	$previousIssueArrNew = array();
	$previousIssueRes=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id  from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
		where a.mst_id = b.id and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91  and a.booking_dtls_id in($booking_dtls_id) ");
		//echo "select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id  from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
		//where a.mst_id = b.id  and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91  and a.booking_dtls_id in($booking_dtls_id) and a.mst_id=$datas[0]";
	foreach($previousIssueRes as $row2)
	{
		$previousIssueArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];

	}



	$total_row=count($sql);
	$current_row_array=array();
	$i=1;
	foreach($sql as $val)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
				/*if($data[2]==3)
				{
					$batch_name=$val[csf("outbound_batchname")];
					$gsm=$val[csf("fin_gsm")];
					$dia=$val[csf("fin_dia")];
				} 
				else 
				{*/
					$batch_name=$val[csf("batch_no")];
					$gsm=$val[csf("gsm")];
					$dia=$val[csf("width")];
				//}
					$balance = $val[csf("batch_wgt")] - $previousIssueArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];
					//echo  $val[csf("batch_wgt")].'<br/>'. $previousIssueArrNew[$val[csf('booking_no')]][$val[csf('booking_dtls_id')]];

					$feb_des_id = $feb_des_array[$val[csf("booking_no")]][$val[csf("booking_dtls_id")]]["feb_des_id"];
					$feb_des_source = $feb_des_array[$val[csf("booking_no")]][$val[csf("booking_dtls_id")]]["fabric_source"];

					if($val[csf("order_id")] != "")
					{
						$fabric_details = $composition_arr[$val[csf("febric_description_id")]];
					}
					else
					{

						if($feb_des_id == "")
						{
							$fabric_details = $composition_arr[$val[csf("febric_description_id")]];
						}
						else
						{
							if($feb_des_source == 1)
							{
								$fabric_details = $fabric_description[$feb_des_id];
							}else{
								$fabric_details = $fabric_description2[$feb_des_id];
							}
						//$fabric_details = $composition_arr[$val[csf("febric_description_id")]];
						}
					}
					
					if($val[csf("dyeing_source")]==1 && $val[csf("process_id")]==35)
					{
						$chk_field="readonly";
					}
					else
					{
						$chk_field="";
					}

					?>
					<tr id="tr_<? echo $i; ?>" align="center" valign="middle">
						<td width="30" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
						<? 
						if($val[csf("receive_basis")] != 2)
						{
							?>
							<td width="90" id="batchNo_<? echo $i; ?>"><?  echo  $batch_name; ?></td>
							<td width="60" id="prodId_<? echo $i; ?>"><? echo $val[csf("prod_id")]; ?></td>
							<?
						}
						?>
						<td style="word-break:break-all;" width="90" align="right"><input type="text" id="txtBatchNo_<? echo $i; ?>" name="txtBatchNo[]"  style=" width:70px" class="text_boxes" value="<? echo $val[csf("outbound_batchname")];?>"  <? echo $chk_field;?> /></td>
						<td style="word-break:break-all;" width="90" id="bodyPart_<? echo $i; ?>"><? echo $body_part[$val[csf("body_part_id")]]; ?></td>
						<td style="word-break:break-all;" width="130" id="cons_<? echo $i; ?>" align="left"><? echo $fabric_details;//$composition_arr[$val[csf("febric_description_id")]]; ?></td>
						<td style="word-break:break-all;" width="50" id="gsm_<? echo $i; ?>"><? echo  $gsm; ?></td>
						<td style="word-break:break-all;" width="50" id="dia_<? echo $i; ?>"><? echo $dia;?></td>
						<td style="word-break:break-all;" width="70" id="color_<? echo $i; ?>"><? echo $color_arr[$val[csf("color_id")]]; ?></td>
						<td style="word-break:break-all;" width="70" id="diaType_<? echo $i; ?>"><? echo $fabric_typee[$val[csf("width_dai_type")]]; ?></td>
						<td width="100" align="right" id="">
							<? 
							echo create_drop_down( "cboProcess_$i", 120, $conversion_cost_head_array,"", 1, "-- Select Process --",$val[csf("process_id")] , "","1","","","","","","","cboProcess[]" ); 
							?>
						</td>
						<td width="70" align="right" id="batchWeight_<? echo $i; ?>"><? echo $val[csf("batch_wgt")]; ?></td>
						<td width="80" align="right" id=""><input type="text" id="txtRollNo_1" name="txtRollNo[]"  style=" width:70px" class="text_boxes_numeric" value="<? echo $val[csf("roll_no")]; ?>"/></td> 
						<td width="80" id="issueQtyTd_<? echo $i; ?>" align="right" id=""><input type="text" id="txtIssueQty_<? echo $i; ?>" name="txtIssueQty[]"  style=" width:70px" class="text_boxes_numeric" title="<? echo $val[csf("batch_issue_qty")];?>" value="<? echo $val[csf("batch_issue_qty")]; ?>" placeholder="<? echo  $balance;?>" onKeyUp="fnc_calculate(this.id)"/></td>
						<td style="word-break:break-all;" width="100" id="bookingNoShow_<? echo $i; ?>"><? echo $val[csf("booking_no")]; ?></td>
						<td style="word-break:break-all;" width="60" id="buyer_<? echo $i; ?>"><? echo $buyer_arr[$val[csf("buyer_id")]]; ?></td>
						<td style="word-break:break-all;" width="80" id="job_<? echo $i; ?>"><? echo $val[csf("job_no")]; ?></td>
						<td style="word-break:break-all;" width="80" id="order_<? echo $i; ?>" align="left"><? echo $order_arr[$val[csf("order_id")]]; ?></td>
						<td style="word-break:break-all;" width="80" id="txtRemarks_<?php echo $i; ?>" align="left"><? echo $order_arr[$val[csf("remarks")]]; ?>
							<input type="text" id="txtRemarks_<?php echo $i; ?>" name="txtRemarks[]" style=" width:80px" class="text_boxes" value="<?php echo $val[csf("remarks")]; ?>">
						</td>
						<td id="button_1" align="center">
							<!-- <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:20px" class="formbuttonplasminus" value="+" onClick="fn_add_row(<? echo $i; ?>);" /> --> &nbsp;<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:20px" class="formbuttonplasminus" value="-" onClick="fnc_remove_row(<? echo $i; ?>);" />
							<input type="hidden" name="recvBasis[]" id="recvBasis_<? echo $i; ?>"/>
							<input type="hidden" name="progBookPiId[]" id="progBookPiId_<? echo $i; ?>"/>
							<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $val[csf("prod_id")]; ?>"/>
							<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $val[csf("order_id")]; ?>"/>
                            <input type="hidden" name="processId[]" id="processId_<? echo $i; ?>" value="<? echo $val[csf("process_id")]; ?>"/>
							<input type="hidden" name="batchWgt[]" id="batchWgt_<? echo $i; ?>" value="<? echo $val[csf("batch_wgt")]; ?>"/>
							<input type="hidden" name="colorId[]" id="colorId_<? echo $i; ?>" value="<? echo $val[csf("color_id")]; ?>"/>
							<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $val[csf("id")]; ?>"/>
							<input type="hidden" name="batchId[]" id="batchId_<? echo $i; ?>" value="<? echo $val[csf("batch_id")]; ?>"/>
							<input type="hidden" name="bodypartId[]" id="bodypartId_<? echo $i; ?>" value="<? echo $val[csf("body_part_id")]; ?>"/>
							<input type="hidden" name="buyerId[]" id="buyerId_<? echo $i; ?>" value="<? echo $val[csf("buyer_id")]; ?>"/>
							<input type="hidden" name="determinationId[]" id="determinationId_<? echo $i; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
							<input type="hidden" name="widthTypeId[]" id="widthTypeId_<? echo $i; ?>" value="<? echo $val[csf("width_dai_type")]; ?>"/>
							<input type="hidden" name="finDia[]" id="finDia_<? echo $i; ?>" value="<? echo $val[csf("fin_dia")]; ?>"/>
							<input type="hidden" name="finGsm[]" id="finGsm_<? echo $i; ?>" value="<? echo $val[csf("fin_gsm")]; ?>"/>
							<input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $i; ?>" value="<? echo $val[csf("booking_no")]; ?>"/>
							<input type="hidden" name="woRate[]" id="woRate_<? echo $i; ?>" value="<? echo $val[csf("rate")]; ?>"/>
							<?  $bookingType =  ($val[csf("booking_without_order")] == 1) ? "2" : "";?> 
							<input type="hidden" name="bookingType[]" id="bookingType_<? echo $i; ?>" value="<? echo $bookingType; ?>"/>
                             <input type="hidden" name="privCurrentQty[]" id="privCurrentQty_<? echo $i; ?>"/>
                             <input type="hidden" name="batchtQty[]" id="batchtQty_<? echo $i; ?>"/>

							<input type="hidden" name="bookingDate[]" id="bookingDate_<? echo $i; ?>" value="<? echo $val[csf("booking_date")]; ?>"/>
							<input type="hidden" name="bookingDtls[]" id="bookingDtls_<? echo $i; ?>" value="<? echo $val[csf("booking_dtls_id")]; ?>"/>
							<input type="hidden" name="remarks[]" id="remarks_<?php echo $i; ?>" value="<?php echo $val[csf("remarks")]; ?>"/>
						</td>
					</tr>
					<?
					$i++;
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
			//return;
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
							$search_by_arr=array(1=>"Issue No",2=>"Batch No",3=>"Booking No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 100, $search_by_arr,"", 0, "--Select--", 1,$dd,0 );
							?>
						</td>     
						<td align="center" id="search_by_td">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 						
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_service_source; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_challan_search_list_view', 'search_div', 'fabric_issue_to_finishing_process_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_challan_search_list_view")
{
	
	$data = explode("_",$data);
	
	$search_string="%".trim($data[0])."";
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$source_id =$data[5];
	$chalan_year =$data[6];
	
	

	/*	
	if($search_by==2)
	{
		
		 $batch_cond="and batch_no='".$data[0]."'";	
		$sql_batch= "select id,batch_no  from pro_batch_create_mst where entry_form=0 and status_active=1 and is_deleted=0 and company_id=$company_id $batch_cond order by id";
		
		 $nameBatch=sql_select( $sql_batch );
		 $batch_ids='';
		 foreach($nameBatch as $row)
		 {
			 if($batch_ids=='') $batch_ids=$row[csf('id')];else $batch_ids.=",".$row[csf('id')];
		 }
		 
		 if($data[0]!='')
		 {
			 $fin_proc_batch="and batch_id in($batch_ids)";
			$sql_batch= "select mst_id as mst_id,batch_id  from pro_grey_batch_dtls where  status_active=1 and is_deleted=0   $fin_proc_batch order by id";
			 $nameBatch=sql_select( $sql_batch );
			 $mst_ids='';
			 foreach($nameBatch as $row)
			 {
				 if($mst_ids=='') $mst_ids=$row[csf('mst_id')];else $mst_ids.=",".$row[csf('mst_id')];
			 }
			  $mst_ids=implode(",",array_unique(explode(",",$mst_ids)));
			  $mstIds="and id in($mst_ids)";
		 }
		}*/
	//echo $search_by;
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}

		$search_field_cond="";
		if(trim($data[0])!="")
		{
			if($search_by==1) $search_field_cond="and a.recv_number like '$search_string'";
			if($search_by==2) $search_field_cond="and b.outbound_batchname like '$search_string'";
			if($search_by==3) $search_field_cond="and b.booking_no like '$search_string'";
		}

		if($db_type==0) 
		{
			$year_field="YEAR(a.insert_date) as year,";
			$year_cond=" and YEAR(a.insert_date)= '$chalan_year'";
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.insert_date,'YYYY') as year,";
			$year_cond=" and to_char(a.insert_date,'YYYY') = '$chalan_year'";
		}
		else $year_field="";//defined Later
	
	
	//$sql = "select id, $year_field recv_number_prefix_num, recv_number, dyeing_source, dyeing_company, receive_date from inv_receive_mas_batchroll where entry_form=91 and status_active=1 and is_deleted=0 and company_id=$company_id and dyeing_source=$source_id $search_field_cond $date_cond  order by id"; 

	$sql = "select a.id, $year_field a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, b.booking_without_order  
	from inv_receive_mas_batchroll  a,pro_grey_batch_dtls b where a.company_id=$company_id and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 and a.dyeing_source=$source_id $search_field_cond $date_cond  $year_cond
	group by a.id, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date,b.booking_without_order  
	order by a.id";

	//echo $sql;//die;
	$result = sql_select($sql);

	//$company_arr=return_library_array( "select id, company_name from lib_company where id=$company_id",'id','company_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	//$subcon_batch_arr=return_library_array( "select b.id as id, b.outbound_batchname as batch_no from  inv_receive_mas_batchroll a,pro_grey_batch_dtls b where a.id=b.mst_id", "id", "batch_no"  );
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="100">Issue No</th>
			<th width="100">Batch No</th>
			<th width="50">Year</th>
			<th width="140">Service Source</th>
			<th width="160">Service Company</th>
			<th>Issue date</th>
		</thead>
	</table>
	<div style="width:700px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="680" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	

				$dye_comp="&nbsp;";
				if($row[csf('dyeing_source')]==1)
					$dye_comp=$company_arr[$row[csf('dyeing_company')]]; 
				else
					$dye_comp=$supllier_arr[$row[csf('dyeing_company')]];

				if($db_type==2)
				{
					$group_con="LISTAGG(CAST(a.batch_no AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY a.batch_no) AS batch_no";
					$group_con2="LISTAGG(CAST(a.outbound_batchname AS VARCHAR2(4000)),',') WITHIN GROUP (ORDER BY a.outbound_batchname) AS batch_no";
				}
				else
				{
					$group_con="group_concat(distinct a.batch_no) AS batch_no";
					$group_con2="group_concat(distinct a.outbound_batchname) AS batch_no";
				}
				if($source_id==1)
				{
					$batch_no=return_field_value("$group_con","pro_batch_create_mst a,pro_grey_batch_dtls b ","a.id=b.batch_id and b.mst_id=".$row[csf('id')]."","batch_no");
				}
				else
				{
					$batch_no=return_field_value("$group_con2","pro_grey_batch_dtls a "," a.mst_id=".$row[csf('id')]."","batch_no");

				}


					//echo $batch_arr[csf('id')]['batch'];
				$batch_no=implode(",",array_unique(explode(",",$batch_no))); 
					//echo $row[csf('id')];
					//echo $batch_no=$batch_arr[$row[csf('id')]]; //'<? echo $row[csf('booking_without_order')]; 
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')].'*'.$row[csf('booking_without_order')]; ?>');"> 
					<td width="40"><? echo $i; ?></td>
					<td width="100"><p>&nbsp;<? echo $row[csf('recv_number')]; ?></p></td>
					<td width="100"><p>&nbsp;<? echo $batch_no; ?></p></td>
					<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="140"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
					<td width="160"><p><? echo $dye_comp; ?>&nbsp;</p></td>
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
	$data = explode("*",$data);
	//$sql = "select id, company_id, recv_number, dyeing_source, dyeing_company, receive_date,receive_basis,gate_pass_no,do_no,car_no from inv_receive_mas_batchroll where id=$data[0] and entry_form=91";

	$sql = "SELECT a.id, a.company_id, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, a.receive_basis, a.gate_pass_no, a.do_no, a.car_no, b.booking_no  
	from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.id=$data[0] and a.entry_form=91
	group by a.id, a.company_id, a.recv_number, a.dyeing_source, a.dyeing_company, a.receive_date, a.receive_basis, a.gate_pass_no, a.do_no, a.car_no, b.booking_no";

	//echo $sql;
	$res = sql_select($sql);	
	foreach($res as $row)
	{		
		echo "$('#txt_issue_no').val('".$row[csf("recv_number")]."');\n";
		echo "$('#txt_batch_no').val('".$row[csf("booking_no")]."');\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_service_source').attr('disabled','true')".";\n";
		echo "$('#txt_issue_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#cbo_service_source').val(".$row[csf("dyeing_source")].");\n";
		if($row[csf("receive_basis")] == 1 || $row[csf("receive_basis")] ==2)
		{
			echo "$('#cbo_basis').val(".$row[csf("receive_basis")].");\n";
		}else{
			echo "$('#cbo_basis').val(1);\n";
		}
		echo "$('#cbo_basis').attr('disabled','true')".";\n";
		if($row[csf("receive_basis")] ==2)
		{
			echo "$('.wo_dtls_td').css('display','none')".";\n";
		}
		echo "$('#txt_gate_no').val('".$row[csf("gate_pass_no")]."');\n";
		echo "$('#txt_do_no').val('".$row[csf("do_no")]."');\n";
		echo "$('#txt_car_no').val('".$row[csf("car_no")]."');\n";

		echo "load_drop_down( 'requires/fabric_issue_to_finishing_process_controller', ".$row[csf("dyeing_source")]."+'**'+".$row[csf("company_id")].", 'load_drop_down_knitting_com', 'dyeing_company_td' );\n";
		echo "$('#cbo_service_company').val(".$row[csf("dyeing_company")].");\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";
	}
	exit();	
}




if ($action=="batch_number_popup")
{
	echo load_html_head_contents("Batch Number Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $cbo_service_source;die;
	?>
	<script>
		function js_set_value(id,batch_no,color_id,withorder,service_source)
		{
			$('#hidden_batch_id').val(id);
			$('#hidden_batch_no').val(batch_no);
			$('#hidden_color_id').val(color_id);
			$('#hidden_booking_withorder').val(withorder);
			$('#hidden_service_source').val(service_source);
			parent.emailwindow.hide();
		}
	</script>
</head>

<body>
	<div align="center" style="width:910px;">
		<form name="searchbatchnofrm"  id="searchbatchnofrm">
			<fieldset style="width:910px; margin-left:10px">
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
							<input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes" value="">
							<input type="hidden" name="hidden_service_source" id="hidden_service_source" class="text_boxes" value="">
							<input type="hidden" name="hidden_booking_withorder" id="hidden_booking_withorder" class="text_boxes" value="">
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+<? echo $cbo_service_source; ?>, 'create_batch_search_list_view', 'search_div', 'fabric_issue_to_finishing_process_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$service_source =$data[5];
	
	$po_arr=array();
	$po_data=sql_select("select id, po_number, job_no_mst from wo_po_break_down");	
	foreach($po_data as $row)
	{
		$po_arr[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_arr[$row[csf('id')]]['job_no']=$row[csf('job_no_mst')];
	}
	

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
	
	
	
	if($db_type==0)
	{
		$order_id_arr=return_library_array( "select mst_id, group_concat(po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','order_id');
	}
	else
	{
		$order_id_arr=return_library_array( "select mst_id, LISTAGG(cast(po_id as VARCHAR2(4000)), ',') WITHIN GROUP (ORDER BY po_id) as po_id from pro_batch_create_dtls where status_active=1 and is_deleted=0 group by mst_id",'mst_id','po_id');
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	
	
	
	//echo $sql;//die; 
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="910" class="rpt_table" >
		<thead>
			<th width="40">SL</th>
			<th width="90">Batch No</th>
			<th width="80">Extention No</th>
			<th width="80">Batch Date</th>
			<th width="80">Batch Qnty</th>
			<th width="115">Booking No</th>
			<th width="110">Color</th>
			<th width="130">Batch Source</th>
			<th>Po No</th>
		</thead>
	</table>
	<div style="width:910px; overflow-y:scroll; max-height:250px;" id="buyer_list_view" align="center">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="890" class="rpt_table" id="tbl_list_search" >
			<?
		/*if($service_source==1)
		{*/
			$sql = "select id, batch_no, extention_no, batch_date, batch_weight, booking_no, color_id, batch_against, booking_without_order, re_dyeing_from from pro_batch_create_mst where entry_form=0 and batch_for=1 and batch_against<>4 and company_id=$company_id and status_active=1 and is_deleted=0 and re_dyeing_from=0 $search_field_cond $date_cond"; 
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
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>',<? echo $selectResult[csf('color_id')]; ?>,<? echo $selectResult[csf('booking_without_order')]; ?>,1)"> 
					<td width="40" align="center"><? echo $i; ?></td>	
					<td width="90"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
					<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?>&nbsp;</p></td>
					<td width="80" align="center"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
					<td width="80" align="right"><? echo $selectResult[csf('batch_weight')]; ?>&nbsp;</td> 
					<td width="115"><p><? echo $selectResult[csf('booking_no')]; ?>&nbsp;</p></td>
					<td width="110"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
					<td width="130"><p>In-house</p></td>
					<td><p><? echo $po_no; ?>&nbsp;</p></td>	
				</tr>
				<?
				$i++;
			}
	//}
		//die;
	//============================For Outbound Subcontact=========================================================================================	
	/*if($service_source==3)
	{	
		if($start_date!="" && $end_date!="")
		{
			if($db_type==0)
			{
				$date_cond_out="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond_out="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			}
		}
		else
		{
			$date_cond_out="";
		}
		
		if(trim($data[0])!="")
		{
			if($search_by==0)
				$search_field_cond_out="and b.outbound_batchname like '$search_string'";
			else if($search_by==1)
				$search_field_cond_out="and b.booking_no like '$search_string'";
			else
				$search_field_cond_out="and b.color_id in(select id from lib_color where color_name like '$search_string')";
		}
		else
		{
			$search_field_cond_out="";
		}
		
		
		 $sql_out = "select a.id as mst_id, b.outbound_batchname as batch_no, 0 as extention_no, a.receive_date as batch_date, b.batch_issue_qty as batch_weight, b.booking_no, b.color_id, null batch_against, 0 as booking_without_order, null re_dyeing_from, b.id, b.order_id 
		from  inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.entry_form=92 and a.dyeing_source=3 and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $search_field_cond_out $date_cond_out";
		$nameArray_out=sql_select( $sql_out );
	   	foreach ($nameArray_out as $selectResult)
		{
			$po_no=''; $job_array=array();
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			
			$po_no=$po_arr[$selectResult[csf("order_id")]]['po_no'];
		
			
			
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>',<? echo $selectResult[csf('color_id')]; ?>,<? echo $selectResult[csf('booking_without_order')]; ?>,2)"> 
				<td width="40" align="center"><? echo $i; ?></td>	
				<td width="90"><p><? echo $selectResult[csf('batch_no')]; ?></p></td>
				<td width="80"><p><? if($selectResult[csf('extention_no')]!=0) echo $selectResult[csf('extention_no')]; ?>&nbsp;</p></td>
				<td width="80" align="center"><? echo change_date_format($selectResult[csf('batch_date')]); ?></td>
				<td width="80" align="right"><? echo $selectResult[csf('batch_weight')]; ?>&nbsp;</td> 
				<td width="115"><p><? echo $selectResult[csf('booking_no')]; ?>&nbsp;</p></td>
				<td width="110"><p><? echo $color_arr[$selectResult[csf('color_id')]]; ?></p></td>
                <td width="130"><p>Subcon Outbound</p></td>
				<td><p><? echo $po_no; ?>&nbsp;</p></td>	
			</tr>
			<?
			$i++;
		}	
			
			
	}*/

	?>
</table>
</div>
<?
exit();
}
if ($action=="service_booking_popup")
{
	echo load_html_head_contents("Booking Search","../../../", 1, 1, $unicode);
	extract($_REQUEST);


	$preBookingNos = "'".implode("','",array_filter(array_unique(explode("_",chop($bookingnos,"_")))))."'";
	//echo $cbo_service_source;
	?>

	<script>
	var service_sourceId='<? echo $cbo_service_source; ?>';
	//alert(service_sourceId);
			function fnc_dtls_popup(booking_id,bookingNo,type,service_source,page_link,title)
			{
			
			var txt_process_id = $('#selected_batchDtls').val();
			page_link=page_link+"&bookingNo="+bookingNo+"&type="+type+"&booking_id="+booking_id+"&txt_process_id="+txt_process_id;
			emailwindow=dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=1020px,height=300px,center=1,resize=1,scrolling=0','../../')
	
			emailwindow.onclose=function(){
				var theform=this.contentDoc.forms[0];
				//var job_no=this.contentDoc.getElementById("job_no");
				//var year=this.contentDoc.getElementById("cbo_job_year");
				var process_id=this.contentDoc.getElementById("hidden_id").value;	 //Access form field with id="emailfield"
				var process_name=this.contentDoc.getElementById("hidden_name").value;
				//var process_seq=this.contentDoc.getElementById("hidden_process_seq").value;
				//alert(service_source+'='+process_id);
				//$('#selected_batchDtls').val(process_id);
				//$('#txt_process_name').val(process_name);
				//$('#txt_process_seq').val(process_seq);
			
				
				//function js_set_value(booking_no)
				//{
				//alert(process_id);
				if(process_id!="") 
				{
				document.getElementById('selected_batchDtls').value=process_id; //return;
				//document.getElementById('selected_booking').value=bookingNo; //return;
				document.getElementById('booking_no').value=bookingNo; //return;
				document.getElementById('booking_id').value=booking_id; //return;
				
			    parent.emailwindow.hide();
				}
				//}
				
			}
		//}
		}
		//if(service_sourceId!=1)
		//{
			function js_set_value(booking_no)
			{
			//alert(booking_no);
			document.getElementById('selected_booking').value=booking_no; //return;
		 	 parent.emailwindow.hide();
			}
		//}
</script>

</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="1300" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<tr>
					<td align="center" width="100%">
						<table  cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
                        	 <input type="text" id="selected_batchDtls" class="text_boxes" style="width:70px" value="<? echo $txt_batch_dtls;?>">
                              <input type="text" id="booking_no" class="text_boxes" style="width:70px" value="">
                              <input type="text" id="booking_id" class="text_boxes" style="width:70px">
                             
                             
							<thead>
								<th  colspan="11">
									<?
									echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",1 );
									?>
								</th>
							</thead>
							<thead>                  
								<th width="150">Company Name</th>
								<th width="150">Supplier Name</th>
								<th width="150">Buyer  Name</th>
								<th width="100">Job  No</th>
								<th width="100">Order No</th>
								<th width="100">Internal Ref.</th>
								<th width="100">File No</th>
								<th width="100">Style No.</th>
								<th width="100">Booking No</th>
								<th width="200">Date Range</th>
								<th></th>           
							</thead>
							<tr>
								<td> <input type="hidden" id="selected_booking">
									<? 
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", "".$company_id."", "load_drop_down( 'fabric_issue_to_finishing_process_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );",1);
									?>
								</td>
								<td>
									<?php 
									if($cbo_service_source==3)
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and b.party_type in (21,24,25) group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									else
									{
										echo create_drop_down( "cbo_supplier_name", 152, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name", 1, "-- Select --", "".$supplier_id."", "",1 );
									}
									?>
								</td>
								<td id="buyer_td">
									<? 
									echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
									?>
								</td>
								<td>
									<input name="txt_job_prifix" id="txt_job_prifix" class="text_boxes" style="width:70px">
								</td> 


								<td>
									<input name="txt_order_number" id="txt_order_number" class="text_boxes" style="width:70px">
								</td> 
								<td>
									<input name="txt_internal_ref" id="txt_internal_ref" class="text_boxes" style="width:70px">
								</td> 
								<td>
									<input name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:70px">
								</td> 



								<td>
									<input name="txt_style" id="txt_style" class="text_boxes" style="width:70px">
								</td>
								<td>
									<input name="txt_booking_prifix" id="txt_booking_prifix" class="text_boxes" style="width:70px">
								</td> 
								<td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px">
								</td> 
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_job_prifix').value+'_'+document.getElementById('txt_booking_prifix').value+'_'+document.getElementById('cbo_search_category').value+'_'+document.getElementById('cbo_supplier_name').value+'_'+document.getElementById('txt_style').value+'_'+<? echo $preBookingNos;?>+'_'+document.getElementById('txt_order_number').value+'_'+document.getElementById('txt_internal_ref').value+'_'+document.getElementById('txt_file_no').value+'_'+<? echo $cbo_service_source;?>, 'create_booking_search_list_view', 'search_div', 'fabric_issue_to_finishing_process_controller','setFilterGrid(\'tbl_list_search\',-1)')" style="width:100px;" /></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle"><? echo load_month_buttons(1);  ?>
						</td>
					</tr>
         <!-- <tr>
           <td align="center" valign="top" id="search_div"> 
           </td>
       </tr>  -->
   </table>    
   <div style="width:100%; margin-top:5px" id="search_div" align="left"></div>
    <!-- <div style="width:100%; margin-top:5px" id="search_div" align="left">
       <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
     </div>-->
   
</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}



if ($action=="create_booking_search_list_view")
{

	$data=explode('_',$data);
    //echo $data[10].'--'.$data[11].'----'.$data[12];
	if ($data[0]!=0) $company="  a.company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
    if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";//{ echo "Please Select Buyer First."; die; }
    if ($data[7]!=0) $supplier=" and a.supplier_id='$data[7]'"; else $supplier="";
    $service_source=$data[13];
	//echo  $service_source.'DD';
    if($db_type==0)
    {
    	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
    }
    
    if($db_type==2)
    {
    	if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and a.booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
    }
    //echo $data[8];
    if($data[6]==1)
    {
    	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num='$data[5]'    "; else  $booking_cond="";
    	if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder=" and a.wo_no_prefix_num='$data[5]'    "; else  $booking_cond_nonOrder="";
    	if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder_knit_dye=" and a.prefix_num='$data[5]'    "; else  $booking_cond_nonOrder_knit_dye="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num='$data[4]'  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no='$data[8]'  "; else  $style_cond=""; 
    }
    if($data[6]==4 || $data[6]==0)
    {
    	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
    	if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder=" and a.wo_no_prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond_nonOrder="";
    	if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder_knit_dye=" and a.prefix_num like '%$data[5]%'  $booking_year_cond  "; else  $booking_cond_nonOrder_knit_dye="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]%'  $year_cond  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==2)
    {
    	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond="";
    	if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder=" and a.wo_no_prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond_nonOrder="";
    	if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder_knit_dye=" and a.prefix_num like '$data[5]%'  $booking_year_cond  "; else  $booking_cond_nonOrder_knit_dye="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '$data[4]%'  $year_cond  "; else  $job_cond=""; 
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '$data[8]%'  "; else  $style_cond=""; 
    }
    
    if($data[6]==3)
    {
    	if (str_replace("'","",$data[5])!="") $booking_cond=" and a.booking_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $booking_cond="";
    	if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder=" and a.wo_no_prefix_num like '%$data[5]'  $booking_year_cond  "; else  $booking_cond_nonOrder="";
    	if (str_replace("'","",$data[5])!="") $booking_cond_nonOrder_knit_dye=" and a.prefix_num like '%$data[5]'  $booking_year_cond  "; else  $booking_cond_nonOrder_knit_dye="";
    	if (str_replace("'","",$data[4])!="") $job_cond=" and c.job_no_prefix_num like '%$data[4]'  $year_cond  "; else  $job_cond="";
    	if (str_replace("'","",$data[8])!="") $style_cond=" and c.style_ref_no like '%$data[8]'  "; else  $style_cond="";  
    } 

    if ($data[9]!="")
    {
    	foreach(explode(",", $data[9]) as $bok){
    		$bookingnos .= "'".$bok."',";
    	}
    	$bookingnos = chop($bookingnos,",");
		if( $service_source!=1)
		{
    	$preBookingNos_1 = " and a.booking_no not in (".$bookingnos.")";
    	$preBookingNos_2 = " and a.wo_no not in (".$bookingnos.")";
		}
    }
    if ($data[10]!="")
    {
    	$po_number_cond = " and d.po_number = '$data[10]'";  	
    }
    if ($data[11]!="")
    {    	
    	$internal_ref_cond = " and d.grouping = '$data[11]'";
    }
    if ($data[12]!="")
    {
    	$file_cond = " and d.file_no = '$data[12]'";
    }


    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
    $comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
    $suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
    $po_no=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
    
    $arr=array (2=>$comp,3=>$conversion_cost_head_array,4=>$buyer_arr,7=>$po_no,8=>$item_category,9=>$fabric_source,10=>$suplier);
    
    /*$sql= "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no_prefix_num,c.style_ref_no, c.job_no, a.po_break_down_id, a.process, a.item_category, a.fabric_source, a.supplier_id, 1 as type ,sum(b.wo_qnty) as wo_qnty
    from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c 
    where a.booking_no = b.booking_no and $company $buyer $booking_date $style_cond and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and b.job_no=c.job_no  $booking_cond $job_cond  $supplier and b.process in (25,26,31,33,35,36,37,60,62,63,64,65,66,67,68,69,70,71,73,82,83,84,85,89,90,91,93,94,129,135,136,145,156) 
    $preBookingNos_1
    group by a.id,a.booking_no_prefix_num, a.booking_no, c.job_no, a.booking_date, a.company_id, a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source, c.job_no_prefix_num,c.style_ref_no, a.supplier_id,a.process
    union all
    select a.id,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, 0 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id, 2 as type,sum(b.wo_qty)  as wo_qnty  
    from wo_non_ord_aop_booking_mst a , wo_non_ord_aop_booking_dtls b
    where a.id = b.wo_id and $company $buyer $booking_date  and  a.status_active=1 and a.is_deleted=0  $booking_cond_nonOrder  $supplier $preBookingNos_2 
    group by a.id,a.wo_no_prefix_num , a.wo_no, a.booking_date, a.company_id, a.buyer_id,a.aop_source, a.supplier_id
    order by booking_no_prefix_num desc";*/


    if($data[10]!="" || $data[11]!="" || $data[12]!="")
    {
    	$sql= "SELECT a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no_prefix_num,c.style_ref_no, c.job_no, a.po_break_down_id, a.process, a.item_category, a.fabric_source, a.supplier_id, 1 as type ,sum(b.wo_qnty) as wo_qnty,d.file_no,d.po_number,d.grouping  
    	from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c ,wo_po_break_down d 
    	where a.booking_no = b.booking_no  and b.po_break_down_id=d.id  and $company $buyer $booking_date $style_cond $file_cond $po_number_cond $internal_ref_cond and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and b.job_no=c.job_no  $booking_cond $job_cond  $supplier and b.process in (1,2,25,26,31,32,33,35,36,37,38,60,62,63,64,65,66,67,68,69,70,71,73,82,83,84,85,88,89,90,91,93,94,129,135,136,145,156,127,155,154,159,199,196,34,195,193,169,160,202,209,210,211,212,219,220,224,225,226,227,171,213,214,241) 
    	$preBookingNos_1
    	group by a.id,a.booking_no_prefix_num, a.booking_no, c.job_no, a.booking_date, a.company_id, a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source, c.job_no_prefix_num,c.style_ref_no, a.supplier_id,a.process,d.file_no,d.po_number,d.grouping";
    }
    else{ 
    	 $sql= "SELECT a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no_prefix_num,c.style_ref_no, c.job_no, a.po_break_down_id, a.process, a.item_category, a.fabric_source, a.supplier_id, 1 as type ,sum(b.wo_qnty) as wo_qnty,d.file_no,d.po_number,d.grouping  
    	from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c ,wo_po_break_down d 
    	where a.booking_no = b.booking_no  and b.po_break_down_id=d.id  and $company $buyer $booking_date $style_cond $file_cond $po_number_cond $internal_ref_cond and  a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and b.job_no=c.job_no  $booking_cond $job_cond  $supplier and b.process in (1,2,25,26,31,32,33,35,36,37,38,60,62,63,64,65,66,67,68,69,70,71,73,82,83,84,85,88,89,90,91,93,94,129,135,136,145,156,127,155,154,159,196,34,195,193,199,169,160,202,209,210,211,212,219,220,224,225,226,227,171,213,214,241) 
    	$preBookingNos_1
    	group by a.id,a.booking_no_prefix_num, a.booking_no, c.job_no, a.booking_date, a.company_id, a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source, c.job_no_prefix_num,c.style_ref_no, a.supplier_id,a.process,d.file_no,d.po_number,d.grouping 
    	union all
    	SELECT a.id,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, 0 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id, 2 as type,sum(b.wo_qty)  as wo_qnty  ,null as file_no,null as po_number,null as grouping  
    	from wo_non_ord_aop_booking_mst a , wo_non_ord_aop_booking_dtls b
    	where a.id = b.wo_id and $company $buyer $booking_date  and  a.status_active=1 and a.is_deleted=0  $booking_cond_nonOrder  $supplier $preBookingNos_2 
    	group by a.id,a.wo_no_prefix_num , a.wo_no, a.booking_date, a.company_id, a.buyer_id,a.aop_source, a.supplier_id

    	union all
    	SELECT a.id,a.prefix_num as booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, b.process_id as process, 0 as item_category, 1 as  fabric_source, a.supplier_id, 2 as type,sum(b.wo_qty) as wo_qnty ,null as file_no,null as po_number,null as grouping 
    	from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b where a.id = b.mst_id  and b.status_active = 1 and b.is_deleted = 0 and $company $buyer $booking_date $booking_cond_nonOrder_knit_dye $supplier $preBookingNos_1 group by a.id,a.prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id,  a.supplier_id, b.process_id 	order by booking_no_prefix_num desc";
    }
   // echo $sql;
		
		$result = sql_select($sql);
		$woQtyArr=array();
		foreach($result as $row)
		{
			if($row[csf('wo_qnty')] ==0 || $row[csf('wo_qnty')]=="")
			{
				$row[csf('wo_qnty')]=0;
			}else $row[csf('wo_qnty')]=$row[csf('wo_qnty')];
			
			$woQtyArr[$row[csf('booking_no')]]+=$row[csf('wo_qnty')];
		}
	
		
		
	
    $issueQtyArr=return_library_array( "select b.booking_no,sum(b.batch_issue_qty) as wo_qnty
    	from inv_receive_mas_batchroll a, pro_grey_batch_dtls b
    	where a.id = b.mst_id
    	and a.entry_form = 91 and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.booking_no is not null  
    	group by b.booking_no",'booking_no','wo_qnty');

    /*   onClick="js_set_value(<? echo $selectResult[csf('id')]; ?>,'<? echo $selectResult[csf('batch_no')]; ?>',<? echo $selectResult[csf('color_id')]; ?>,<? echo $selectResult[csf('booking_without_order')]; ?>,2)"*/

    
    //echo  create_list_view("list_view", "Booking No,Booking Date,Company,Process,Buyer,Job No.,Style No.,PO number,Fabric Nature,Fabric Source,Supplier", "70,80,80,100,100,70,110,150,80,80","1070","320",0, $sql , "js_set_value", "id,booking_no,job_no,type", "", 1, "0,0,company_id,process,buyer_id,0,0,po_break_down_id,item_category,fabric_source,supplier_id", $arr , "booking_no_prefix_num,booking_date,company_id,process,buyer_id,job_no_prefix_num,style_ref_no,po_break_down_id,item_category,fabric_source,supplier_id", '','','0,3,0,0,0,0,0,0,0,0,0','','');

    ?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1270" class="rpt_table">
    	<thead>
    		<tr>
    			<th width="40">SL No.</th>
    			<th width="150">Booking No</th>
    			<th width="80">Booking Date</th>
    			<th width="80">Company</th>
    			<th width="100">Buyer</th>
    			<th width="70">Job No</th>

    			<th width="70">Internal Ref.</th>
    			<th width="70">File No</th>


    			<th width="110">Style No.</th>
    			<th width="150">PO number</th>
    			<th width="150">Fabric Nature</th>
    			<th width="">Supplier</th>
    		</tr>
    	</thead>
    </table>
    <div style="width:1288px; max-height:400px; overflow-y:scroll;" >	 
    	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1270" class="rpt_table" id="tbl_list_search" >  
    		<tbody>
    			<?
    			//$result = sql_select($sql);
	        	//echo $sql;
	        	//print_r($result); 
				
	    		$i=1; $total_woQty=0;$total_issueQty =0;$balance=0;
	            foreach($result as $row)
	            { 
	            	$total_issueQty= $issueQtyArr[$row[csf('booking_no')]];
	            	//$total_woQty+=$row[csf('wo_qnty')];
					//echo $service_source.'DDDD';
					
					$total_woQty=$woQtyArr[$row[csf('booking_no')]];
	            	$balance = $total_woQty - $total_issueQty;

	            	//$balance=11053-4259
	            	//echo "#".$row[csf('wo_qnty')] ."-". $issueQty;
					
    				if($balance > 0)
    				{

    					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
    					$booking_format_arr=explode("-", $row[csf('booking_no')]);
    					$booking_format=$booking_format_arr[1];
    					$process=$row[csf('process')];
						//echo $process.'d';
					 $popup_open="fnc_dtls_popup('".$row[csf('id')]."','".$row[csf('booking_no')]."','".$row[csf('type')]."','".$service_source."','fabric_issue_to_finishing_process_controller.php?action=fabric_search_popup','Fabric Batch Search');";
					
						if($service_source==1)
						{
    					?>
    					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="<? echo $popup_open;?>"> 
                        <?
						}
						else
						{ ?>
                         <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')]."_".$row[csf('booking_no')]."_".$row[csf('job_no')]."_".$row[csf('type')]."_".$row[csf('booking_date')]."_".$balance."_".$booking_format."_".$process; ?>');"> 
                        <?
						}
						?>
    						<td width="40"><? echo $i; ?></td>
    						<td width="150"><p><? echo $row[csf('booking_no')]; ?></p></td>
    						<td width="80"><p><? echo change_date_format($row[csf('booking_date')]); ?></p></td>
    						<td width="80"><p><? echo $comp[$row[csf('company_id')]]; ?></p></td>

    						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
    						<td width="70"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>


    						<td width="70"><p><? echo $row[csf('grouping')]; ?></p></td>
    						<td width="70"><p><? echo $row[csf('file_no')]; ?></p></td>

    						<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
    						<td width="150"><p><? echo $row[csf('po_number')]; ?></p></td>
    						<td width="150"><p><? echo $item_category[$row[csf('item_category')]]; ?></p></td>

    						<td align="center"><? 							
							if($service_source==1)
							{
								echo $comp[$row[csf('supplier_id')]];
							} 
							else 
							{
								echo $suplier[$row[csf('supplier_id')]];
							} ?></td>
    					</tr>
    					<?
    					$i++;

    				}
    			}
    			?>
    		</tbody>
    	</table>
    </div>
    <script type="text/javascript">
    	setFilterGrid("tbl_list_search",-1);
    </script>
    <?	

    exit();
}
if ($action=="fabric_search_popup")
{ 	
echo load_html_head_contents("Item Info", "../../../", 1, 1, '', '1', '');
extract($_REQUEST);
	//echo $bookingNo.'='.$type;
	// echo $po_ids.'XZZZZ';
	$booking_type=$type;
	if($type == 1)
	{
		$sql_book="select a.po_break_down_id,a.wo_qnty,a.gmts_color_id from wo_booking_dtls a where  a.booking_no='$bookingNo'  and a.wo_qnty>0 and a.status_active=1 and a.is_deleted=0";
		 $results_book = sql_select($sql_book);
		 $po_id_conds="";
	
		 foreach($results_book as $row)
		 {
				$po_id_conds.=$row[csf('po_break_down_id')].',';
				$wo_qty_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['wo_qnty']=$row[csf('wo_qnty')];
		 }
		 $po_id=rtrim($po_id_conds,',');
		 $po_ids=implode(",",array_unique(explode(",", $po_id)));//load_unload_id pro_fab_subprocess
		 $sql_batch="select a.batch_no,a.id as batch_id,a.color_id,a.process_id,b.id as dtls_id,b.item_description,b.body_part_id,b.prod_id,b.po_id,b.width_dia_type,b.batch_qnty ,d.detarmination_id as deter_id from pro_batch_create_mst a,pro_batch_create_dtls b,pro_fab_subprocess c,product_details_master d where a.id=b.mst_id  and a.id=c.batch_id and c.batch_id=b.mst_id and d.id=b.prod_id and c.load_unload_id=2 and a.entry_form=0 and b.po_id in($po_ids) and b.batch_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
		 $results_batch = sql_select($sql_batch);
		
	//product_details_master detarmination_id
		 foreach($results_batch as $row)
		 {
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['item_desc']=$row[csf('item_description')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['process_id']=$row[csf('process_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['width_dia_type']=$row[csf('width_dia_type')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['po_id']=$row[csf('po_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].'!!';
		 }
	 
		/*$sql= "SELECT a.id as aid,c.id as dtls_id, a.body_part_id as body_part_id,a.color_type_id as color_type_id,a.fabric_description,a.gsm_weight,a.width_dia_type,a.construction,a.lib_yarn_count_deter_id,b.id as bid,b.cons_process as cons_process, c.id as id,c.job_no,c.po_break_down_id,c.booking_no as booking_no,c.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id,c.dia_width as dia_width, c.wo_qnty as wo_qnty,c.rate,c.amount as amount,c.gmts_color_id as gmts_color_id, c.fabric_color_id, c.process,c.uom,null as buyer_id,d.batch_qnty 
		from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b,wo_booking_dtls c,pro_batch_create_dtls d 
		where a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.fabric_description and b.id=c.pre_cost_fabric_cost_dtls_id and d.po_id=c.po_break_down_id  and c.booking_no='$bookingNo' and c.wo_qnty>0 and a.status_active=1 and a.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/
	}
	else
	{
		
		/*$sql ="SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate, 35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom,b.fabric_description as fab_des_id, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and b.fabric_description = c.id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and a.wo_no = '$bookingNo'  and b.wo_qty>0
		union all
		SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id,b.gmts_color as gmts_color_id,  a.prefix_num as booking_no_prefix_num, a.booking_no, c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty,  b.gsm as gsm_weight,b.dia as dia_width,b.rate, b.process_id as process, 0 as item_category, b.fabric_source, a.supplier_id,b.uom,b.fab_des_id, 2 as type from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c where a.id = b.mst_id and a.fab_booking_id =  c.id  and b.status_active = 1 and b.is_deleted = 0 and a.booking_no = '$bookingNo' and b.wo_qty>0";*/
		
 	}
	
	
	
	//$color_arr = return_library_array("select id, color_name from lib_color ","id","color_name");
	$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	//$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");

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
//echo $booking_type.'DS';
	if($booking_type == 1)
	{
		
		$sql= "SELECT a.id as aid,c.id as dtls_id, a.body_part_id as body_part_id,a.color_type_id as color_type_id,a.fabric_description,a.gsm_weight,a.width_dia_type,a.construction,a.lib_yarn_count_deter_id,b.id as bid,b.cons_process as cons_process, c.id as id,c.job_no,c.po_break_down_id,c.booking_no as booking_no,c.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id,c.dia_width as dia_width, c.wo_qnty as wo_qnty,c.rate,c.amount as amount,c.gmts_color_id as gmts_color_id, c.fabric_color_id, c.process,c.uom,null as buyer_id 
		from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b,wo_booking_dtls c  
		where a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.fabric_description and b.id=c.pre_cost_fabric_cost_dtls_id  and c.booking_no='$bookingNo' and c.wo_qnty>0 and a.status_active=1 and a.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else
	{
		/*$sql ="select a.id,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, 0 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id, 2 as type  from wo_non_ord_aop_booking_mst a where and a.wo_no = '$booking_no'  and  a.status_active=1 and a.is_deleted=0 order by booking_no_prefix_num desc";*/
		/*$sql = "select a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id,
		null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate,
		35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and  a.status_active=1 and a.is_deleted=0  and a.wo_no = '$booking_no'";*/


		$sql ="SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate, 35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom,b.fabric_description as fab_des_id, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and b.fabric_description = c.id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and a.wo_no = '$bookingNo'  and b.wo_qty>0
		union all
		SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id,b.gmts_color as gmts_color_id,  a.prefix_num as booking_no_prefix_num, a.booking_no, c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty,  b.gsm as gsm_weight,b.dia as dia_width,b.rate, b.process_id as process, 0 as item_category, b.fabric_source, a.supplier_id,b.uom,b.fab_des_id, 2 as type from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c where a.id = b.mst_id and a.fab_booking_id =  c.id  and b.status_active = 1 and b.is_deleted = 0 and a.booking_no = '$bookingNo' and b.wo_qty>0";

 	}
	//echo $sql; 
	//=========================================================
 $results = sql_select($sql);
 $fab_des_id_conds="";$po_id_conds="";
 foreach($results as $row)
 {
 	$fab_des_id_conds.=$row[csf('fab_des_id')].',';
 	$po_id_conds.=$row[csf('po_break_down_id')].',';
	$po_arr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
	$booking_process_arr[$row[csf("po_break_down_id")]]=$row[csf("process")];
	$fab_source_arr[$row[csf("po_break_down_id")]]=$row[csf("fabric_source")];
	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row[csf("lib_yarn_count_deter_id")]];
		$deter_id=$row[csf("lib_yarn_count_deter_id")];
 	}
 	else
 	{

 		if($row[csf('fabric_source')]==1)
 		{
 			$fabric_des=  $fabric_description[$row[csf('fab_des_id')]]; 
			$deter_id=$row[csf("fab_des_id")];
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row[csf('fab_des_id')]]; 
 		}
 		//$buyer_id_non_ord=$row[csf('buyer_id')];
 		//$buyer_name_non_ord=$buyer_name_array[$row[csf('buyer_id')]];

 	}
	 
	  $booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$deter_id]=$row[csf("color_type_id")];
 }
 $fab_des_id_cond=chop($fab_des_id_conds,',');
 $po_id_cond=chop($po_id_conds,',');
 if($fab_des_id_cond!=""){$fab_des_id_qry_cond="and a.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond="";}
 if($po_id_cond!=""){$po_id_qry_cond="and b.id in($po_id_cond)";}else{$po_id_qry_cond="";}

	//die; 
	$sql_product=sql_select("select id,product_name_details,detarmination_id from product_details_master where item_category_id=13");
	foreach($sql_product as $row)
 	{
		$lib_product[$row[csf('id')]]=$row[csf('product_name_details')];
		$lib_product_detemin[$row[csf('id')]]=$row[csf('detarmination_id')];
	}
 if($booking_type!=1 && $fab_des_id_cond!="")
 {
 	$sql_1=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id");

	//echo "select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id";
 	foreach($sql_1 as $row)
 	{
 		$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
 	}
 	//$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
 	if($fab_des_id_cond!=""){$fab_des_id_qry_cond_2="and c.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond_2="";}

 	$sql_2=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond_2 order by c.id");
 	foreach($sql_2 as $row)
 	{
 		$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
 	}	
 }

  	//=========================================================
 if($booking_type==1 && $po_id_cond!="")
 {
 	$data_array_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $po_id_qry_cond");


 	$po_details_array=array();
 	foreach($data_array_sql as $row)
 	{
 		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
 		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
 		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
 		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
 		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
 		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
 	}
 }
	//=========================================================


 $previousIssueArrNew = array();
 $previousIssueRes=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id,a.order_id,a.body_part_id,a.color_id,a.febric_description_id as deter_id from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
 	where a.mst_id = b.id and a.booking_no ='$bookingNo' and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91");
 foreach($previousIssueRes as $row2)
 {
 	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row2[csf("deter_id")]];
		$deter_id=$row2[csf("deter_id")];
 	}
 	else
 	{
 		 $fab_source=$fab_source_arr[$row2[csf("order_id")]];
		if($fab_source==1)
 		{
 			$fabric_des=  $fabric_description[$row2[csf('deter_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row2[csf('deter_id')]]; 
 		}
		$deter_id=$row2[csf("deter_id")];
 	}
	
	$previousIssueArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];
	$previousIssueArrNew2[$row2[csf('order_id')]][$row2[csf('body_part_id')]][$row2[csf('color_id')]][$deter_id]+=$row2[csf('batch_issue_qty')];

 }
//print_r($previousIssueArrNew2);
  $po_arr_ids=implode(',', $po_arr);
 $batchDataArrNew = array();
 $sql_po_batch = "select a.id,b.batch_no,b.color_id,b.id as batch_id,b.booking_no_id,b.booking_no,b.booking_without_order,b.extention_no,a.po_id,a.prod_id,a.item_description,a.body_part_id, a.width_dia_type,(a.batch_qnty) as batch_qnty from pro_batch_create_dtls a, pro_batch_create_mst b,pro_fab_subprocess c where a.mst_id=b.id and c.batch_id=b.id  and c.load_unload_id=2 and a.po_id in($po_arr_ids) and c.status_active=1 and b.status_active=1 and a.status_active=1 order by a.id ";//group by  b.batch_no,b.color_id,b.id,a.po_id,a.prod_id,a.id,a.item_description,a.body_part_id,a.width_dia_type,b.booking_no_id,b.booking_no,b.booking_without_order,b.extention_no
$result_po_batch=sql_select($sql_po_batch);
	foreach ($result_po_batch as $row)
	{
		$fab_des=explode(",",$row[csf('item_description')]);
		$fab_dia=$fab_des[3];
		$fab_gsm=$fab_des[2];
		//$fab_cons=$fab_des[0];
		$fab_cons_commp=$fab_des[0].', '.$fab_des[1];
		$process_id=$booking_process_arr[$row[csf("po_id")]];
		$detemin_id=$lib_product_detemin[$row[csf('prod_id')]];
		$color_type_id=$booking_color_type_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]][$detemin_id];
		//$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_id']= $row[csf('id')];
		//$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_no']= $row[csf('batch_no')];
		//echo $color_type_id.'DD';
		if($color_type_id==5 || $color_type_id==7)//Aop/Aop Stripe
		{
		 $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$detemin_id]['batch_qnty'] += $row[csf('batch_qnty')];
		 $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$detemin_id]['batch_id'] = $row[csf('batch_id')];
		 $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$detemin_id]['batch_no'] = $row[csf('batch_no')];
		}
		
		
	}
	//print_r($po_batch_data_arr);
 
	?>
    <script>
	$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function set_all() {
			var old_seq = document.getElementById('txt_seq').value;
			var old = document.getElementById('txt_row_id').value;
			if (old != "") {
				old = old.split(",");
				if(old_seq!=""){
					oldArr = old_seq.split(",");
				}

				for (var k = 0; k < old.length; k++) {
					if(old_seq!=""){
						idSeq = oldArr[k].split("_");
						$('#txt_sequence'+idSeq[0]).val(idSeq[1]);
						$('#txt_sequence'+old[k]).val(oldArr[k]);
					}

					js_set_value(old[k]);
				}
			}
		}

		function js_set_value(str) {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
             if(currentRowColor=='yellow')
             {
             var mandatory=$('#txt_mandatory' + str).val();
             var process_name=$('#txt_individual' + str).val();
             if(mandatory==1)
             {
             alert(process_name+" Subprocess is Mandatory. So You can't De-select");
             return;
             }
         }*/

         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
         }

         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         	id += selected_id[i] + ',';
         	name += selected_name[i] + ',';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hidden_id').val(id);
         $('#hidden_name').val(name);
     }

     function window_close(){

     	var old = document.getElementById('hidden_id').value;
		//alert(old);
     	if (old != "") {
     		old = old.split(",");
     		var seq='';
     		for (var k = 0; k < old.length; k++) {
     			if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     		}
     	}
     	//$('#hidden_process_seq').val(seq);
			//var oldArr = old_seq.split(",");


			parent.emailwindow.hide();
		}
	</script>
    <body>
    <div>
    <input type="hidden" name="hidden_id" id="hidden_id" class="text_boxes" value="">
	<input type="hidden" name="hidden_seq" id="hidden_seq" class="text_boxes" value="">
	<input type="hidden" name="hidden_name" id="hidden_name" class="text_boxes" value="">
  	<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="1000">
			<caption><strong>Select Item</strong> </caption>
			<thead align="center">
				<tr>
					<th width="30">SL</th>
					<th width="100">Batch No</th>
					<th width="100">Body part</th>
					<th width="">Const./Compo</th>
					<th width="50">GSM </th>
					<th width="50">DIA</th>
					<th width="100">Color</th>
					<th width="100">Dia Width/Type</th>
					<th width="100">Process</th>
					<th width="80">Wo Qty</th>
					<th width="80">Batch Qty</th> 
				</tr>
			</thead>
			<tbody id="tbl_list_search">
            <?
				$batch_no_chk=array();
				$i=1;$hidden_process_id = explode(",", $txt_process_id);
				foreach($results as $row)
				{
				
					 if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$balance = number_format($row[csf("wo_qnty")],4,'.','') - $previousIssueArrNew[$bookingNo][$row[csf('dtls_id')]];
					$balance_qty = $row[csf("wo_qnty")]-$previousIssueArrNew[$bookingNo][$row[csf('dtls_id')]];
				    $cons_process=$row[csf("cons_process")];
				
					if($booking_type == 1)
					{
						$fabric_des = $composition_arr[$row[csf("lib_yarn_count_deter_id")]];
						$deter_id=$row[csf("lib_yarn_count_deter_id")];
					}
					else
					{
				
						if($row[csf('fabric_source')]==1)
						{
							$fabric_des=  $fabric_description[$row[csf('fab_des_id')]]; 
						}
						else
						{
							$fabric_des =  $fabric_description2[$row[csf('fab_des_id')]]; 
						}
						$deter_id=$row[csf("fab_des_id")];
						$buyer_id_non_ord=$row[csf('buyer_id')];
						$buyer_name_non_ord=$buyer_name_array[$row[csf('buyer_id')]];
				
					}
					$color_typeid=0;
					$color_typeid=$booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$deter_id];
					
					if($row[csf("body_part_id")]=='') $row[csf("body_part_id")]=0;else $row[csf("body_part_id")]=$row[csf("body_part_id")];
					$batch_qnty=0;$color_previousIssue_qty=0;
					$batch_qnty=$po_batch_data_qty_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$deter_id]['batch_qnty'];
					$batch_id=$po_batch_data_qty_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$deter_id]['batch_id'];
					$batch_no=$po_batch_data_qty_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$deter_id]['batch_no'];
					if($batch_qnty==0 || $batch_qnty=="") $batch_qnty=0;else $batch_qnty=$batch_qnty;
					$color_previousIssue_qty=$previousIssueArrNew2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$deter_id];
					$priv_current_qty=$color_previousIssue_qty+$balance_qty;
					//echo $priv_current_qty.'='.$batch_qnty.'='.$color_typeid.'<br>';
					//$batch_id=$po_batch_data_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['batch_id'];
					//$batch_no=$po_batch_data_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['batch_no'];
						//echo $batch_no.'GG'.$cons_process;
					$dtl_data=$row[csf("dtls_id")].'_'.$batch_id;
					
					if($booking_type == 1)
					{
						if ($row[csf("gmts_color_id")]=="") 
						{
							$fabric_color=$row[csf("fabric_color_id")];
						}
						else
						{
							$fabric_color=$row[csf("gmts_color_id")];
						}
					}
					else
					{
						$fabric_color=$row[csf("gmts_color_id")];
					}	
					
					
					
					if (in_array($dtl_data, $hidden_process_id))
					{
					if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
					}
					//echo $cons_process.'='.$batch_no.',';
					if($cons_process==35)
					{
					
					if($batch_no!="" && $cons_process==35){ //AOP
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td align="center"  width="30" valign="middle"><? echo $i; ?>
                        	<input type="hidden" name="txt_individual_id"  style="width:100px"id="txt_individual_id<?php echo $i ?>" value="<? echo $dtl_data; ?>"/> 
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $dtl_data; ?>"/>
                        </td>
						<td align="center"  width="100"><? echo $batch_no; ?></td>
						<td align="center"  width="100"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
						<td align="center" style="word-wrap: break-word;"><? echo $fabric_des; ?>&nbsp;</td>
						<td align="center"  width="50"><? echo $row[csf("gsm_weight")]; ?>&nbsp;</td>
						<td  align="center" width="50" ><? echo $row[csf("dia_width")]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $color_arr[$fabric_color]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $fabric_typee[$row[csf("width_dia_type")]]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?>&nbsp;</td>
                        <td align="center"  width="80"><? echo number_format($row[csf("wo_qnty")],2); ?>&nbsp;</td>
                        <td align="center" width="80" ><? echo number_format($batch_qnty,2); ?>&nbsp;</td>
                        </tr>
                   <? 
				   			$i++;
							$batch_no_chk[$batch_no]=$batch_no;
					 }
					} //AOP chk end
					else
					{ ?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td align="center"  width="30" valign="middle"><? echo $i; ?>
                        	<input type="hidden" name="txt_individual_id"  style="width:100px"id="txt_individual_id<?php echo $i ?>" value="<? echo $dtl_data; ?>"/> 
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $dtl_data; ?>"/>
                        </td>
						<td align="center"  width="100"><? echo $batch_no; ?></td>
						<td align="center"  width="100"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
						<td align="center" style="word-wrap: break-word;"><? echo $fabric_des; ?>&nbsp;</td>
						<td align="center"  width="50"><? echo $row[csf("gsm_weight")]; ?>&nbsp;</td>
						<td  align="center" width="50" ><? echo $row[csf("dia_width")]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $color_arr[$fabric_color]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $fabric_typee[$row[csf("width_dia_type")]]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $conversion_cost_head_array[$row[csf("cons_process")]]; ?>&nbsp;</td>
                        <td align="center"  width="80"><? echo number_format($row[csf("wo_qnty")],2); ?>&nbsp;</td>
                        <td align="center" width="80" ><? echo number_format($batch_qnty,2); ?>&nbsp;</td>
                        </tr>
                   <? 
				   			$i++;
							$batch_no_chk[$batch_no]=1;
					 }
					
				   	
				 }
				// echo count($batch_no_chk).'DDDDDDDDD';
				 	  if(count($batch_no_chk)<=0)
						{
						echo "<p align='center'><b style='color:red;'>No data found. Batch and dyeing production is mandatory to populate data</b></p>";die;
						}
				    ?>
                <input type="hidden" name="txt_row_id" id="txt_row_id" value="<?php echo $process_row_id; ?>"/>
				<input type="hidden" name="txt_seq" id="txt_seq" value="<?php echo $process_seq; ?>"/>

            </tbody>
            </table>	
	
     		<table width="1050" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all"
							onClick="check_all_data()"/>
							Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="window_close()"
							class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
   	 </div>
      </body>
       <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
       <script>
			set_all();
		</script>
      </html>
    <?
	exit();
}
if ($action=="fabric_search_popup2")
{ 	
echo load_html_head_contents("Item Info", "../../../", 1, 1, '', '1', '');
extract($_REQUEST);
	//echo $bookingNo.'='.$type;
	// echo $po_ids.'XZZZZ';
	if($type == 1)
	{
		$sql_book="select a.po_break_down_id,a.wo_qnty,a.gmts_color_id from wo_booking_dtls a where  a.booking_no='$bookingNo'  and a.wo_qnty>0 and a.status_active=1 and a.is_deleted=0";
		 $results_book = sql_select($sql_book);
		 $po_id_conds="";
	
		 foreach($results_book as $row)
		 {
				$po_id_conds.=$row[csf('po_break_down_id')].',';
				$wo_qty_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['wo_qnty']=$row[csf('wo_qnty')];
		 }
		 $po_id=rtrim($po_id_conds,',');
		 $po_ids=implode(",",array_unique(explode(",", $po_id)));//load_unload_id pro_fab_subprocess
		 $sql_batch="select a.batch_no,a.id as batch_id,a.color_id,a.process_id,b.id as dtls_id,b.item_description,b.body_part_id,b.prod_id,b.po_id,b.width_dia_type,b.batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form=0 and b.po_id in($po_ids) and b.batch_qnty>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
		 $results_batch = sql_select($sql_batch);
		
	
		 foreach($results_batch as $row)
		 {
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['item_desc']=$row[csf('item_description')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['process_id']=$row[csf('process_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['width_dia_type']=$row[csf('width_dia_type')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['po_id']=$row[csf('po_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['dtls_id'].=$row[csf('dtls_id')].'!!';
		 }
	 
		/*$sql= "SELECT a.id as aid,c.id as dtls_id, a.body_part_id as body_part_id,a.color_type_id as color_type_id,a.fabric_description,a.gsm_weight,a.width_dia_type,a.construction,a.lib_yarn_count_deter_id,b.id as bid,b.cons_process as cons_process, c.id as id,c.job_no,c.po_break_down_id,c.booking_no as booking_no,c.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id,c.dia_width as dia_width, c.wo_qnty as wo_qnty,c.rate,c.amount as amount,c.gmts_color_id as gmts_color_id, c.fabric_color_id, c.process,c.uom,null as buyer_id,d.batch_qnty 
		from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b,wo_booking_dtls c,pro_batch_create_dtls d 
		where a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.fabric_description and b.id=c.pre_cost_fabric_cost_dtls_id and d.po_id=c.po_break_down_id  and c.booking_no='$bookingNo' and c.wo_qnty>0 and a.status_active=1 and a.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/
	}
	else
	{
		
		/*$sql ="SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate, 35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom,b.fabric_description as fab_des_id, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and b.fabric_description = c.id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and a.wo_no = '$bookingNo'  and b.wo_qty>0
		union all
		SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id,b.gmts_color as gmts_color_id,  a.prefix_num as booking_no_prefix_num, a.booking_no, c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty,  b.gsm as gsm_weight,b.dia as dia_width,b.rate, b.process_id as process, 0 as item_category, b.fabric_source, a.supplier_id,b.uom,b.fab_des_id, 2 as type from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c where a.id = b.mst_id and a.fab_booking_id =  c.id  and b.status_active = 1 and b.is_deleted = 0 and a.booking_no = '$bookingNo' and b.wo_qty>0";*/
		
 	}
	
	
	
	 $color_arr = return_library_array("select id, color_name from lib_color ","id","color_name");
	?>
    <script>
	$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function set_all() {
			var old_seq = document.getElementById('txt_seq').value;
			var old = document.getElementById('txt_row_id').value;
			if (old != "") {
				old = old.split(",");
				if(old_seq!=""){
					oldArr = old_seq.split(",");
				}

				for (var k = 0; k < old.length; k++) {
					if(old_seq!=""){
						idSeq = oldArr[k].split("_");
						$('#txt_sequence'+idSeq[0]).val(idSeq[1]);
						$('#txt_sequence'+old[k]).val(oldArr[k]);
					}

					js_set_value(old[k]);
				}
			}
		}

		function js_set_value(str) {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
             if(currentRowColor=='yellow')
             {
             var mandatory=$('#txt_mandatory' + str).val();
             var process_name=$('#txt_individual' + str).val();
             if(mandatory==1)
             {
             alert(process_name+" Subprocess is Mandatory. So You can't De-select");
             return;
             }
         }*/

         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
         }

         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         	id += selected_id[i] + ',';
         	name += selected_name[i] + ',';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hidden_id').val(id);
         $('#hidden_name').val(name);
     }

     function window_close(){

     	var old = document.getElementById('hidden_id').value;
		//alert(old);
     	if (old != "") {
     		old = old.split(",");
     		var seq='';
     		for (var k = 0; k < old.length; k++) {
     			if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     		}
     	}
     	//$('#hidden_process_seq').val(seq);
			//var oldArr = old_seq.split(",");


			parent.emailwindow.hide();
		}
	</script>
    <body>
    <div>
    <input type="hidden" name="hidden_id" id="hidden_id" class="text_boxes" value="">
	<input type="hidden" name="hidden_seq" id="hidden_seq" class="text_boxes" value="">
	<input type="hidden" name="hidden_name" id="hidden_name" class="text_boxes" value="">
  	<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="1000">
			<caption><strong>Select Item</strong> </caption>
			<thead align="center">
				<tr>
					<th width="30">SL</th>
					<th width="100">Batch No</th>
					<th width="100">Body part</th>
					<th width="">Const./Compo</th>
					<th width="50">GSM </th>
					<th width="50">DIA</th>
					<th width="100">Color</th>
					<th width="100">Dia Width/Type</th>
					<th width="100">Process</th>
					<th width="80">Wo Qty</th>
					<th width="80">Batch Qty</th> 
				</tr>
			</thead>
			<tbody id="tbl_list_search">
            <?
				$i=1;$hidden_process_id = explode(",", $txt_process_id);
				foreach($batch_wise_arr as $batchId=>$batchData)
				{
				 foreach($batchData as $bodyId=>$bodyData)
				 {
					 foreach($bodyData as $prodId=>$row)
					 {
					 if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$item_desc=explode(",",$row[("item_desc")]);
					$const_comp=$item_desc[0].','.$item_desc[1];//
					$process_id=explode(",",$row[("process_id")]);
					$process_ids="";
					$dtls_ids=rtrim($row[("dtls_id")],'!!');
					$dtlsids=implode("!!",array_unique(explode("!!",$dtls_ids)));
					$dtl_data=$batchId.'_'.$bodyId.'_'.$prodId.'_'.$dtlsids;
					foreach($process_id as $pid)
					{
						if($process_ids=="") $process_ids=$conversion_cost_head_array[$pid];else $process_ids.=",".$conversion_cost_head_array[$pid];
					}
					$wo_qnty=$wo_qty_arr[$row[('po_id')]][$row[('color_id')]]['wo_qnty'];
					
					if (in_array($dtl_data, $hidden_process_id)) {
									if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
								}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
						<td align="center"  width="30" valign="middle"><? echo $i; ?>
                        	<input type="hidden" name="txt_individual_id"  style="width:100px"id="txt_individual_id<?php echo $i ?>" value="<? echo $dtl_data; ?>"/> 
                            <input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $dtl_data; ?>"/>
                        </td>
						<td align="center"  width="100"><? echo $row[("batch_no")]; ?></td>
						<td align="center"  width="100"><? echo $body_part[$bodyId]; ?></td>
						<td align="center" style="word-wrap: break-word;"><? echo $const_comp; ?>&nbsp;</td>
						<td align="center"  width="50"><? echo $item_desc[2]; ?>&nbsp;</td>
						<td  align="center" width="50" ><? echo $item_desc[2]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $color_arr[$row[("color_id")]]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $fabric_typee[$row[("width_dia_type")]]; ?>&nbsp;</td>
                        <td align="center"  width="100"><? echo $process_ids; ?>&nbsp;</td>
                        <td align="center"  width="80"><? echo number_format($wo_qnty,2); ?>&nbsp;</td>
                        <td align="center" width="80" ><? echo number_format($row[("batch_qnty")],2); ?>&nbsp;</td>
                        </tr>
                   <? 
				   			$i++;
				   		}  
				 	}
				 }
				    ?>
                <input type="hidden" name="txt_row_id" id="txt_row_id" value="<?php echo $process_row_id; ?>"/>
				<input type="hidden" name="txt_seq" id="txt_seq" value="<?php echo $process_seq; ?>"/>

            </tbody>
            </table>	
	
     		<table width="1050" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all"
							onClick="check_all_data()"/>
							Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="window_close()"
							class="formbutton" value="Close" style="width:100px"/>
						</div>
					</div>
				</td>
			</tr>
		</table>
   	 </div>
      </body>
       <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
       <script>
			set_all();
		</script>
      </html>
    <?
	exit();
}
if($action=="check_batch_no")
{
	$data=explode("**",$data);
	if($data[2]==3)
	{
		$sql="select b.id, b.outbound_batchname as batch_no, b.color_id, 0 as booking_without_order from inv_receive_mas_batchroll a, pro_grey_batch_dtls b 
		where a.id=b.mst_id and b.outbound_batchname='".trim($data[0])."' and a.company_id='".$data[1]."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.entry_form=92 order by id desc";
	}
	else
	{
		$sql="select id, batch_no,color_id,booking_without_order from pro_batch_create_mst where batch_no='".trim($data[0])."' and company_id='".$data[1]."' and is_deleted=0 and status_active=1 and entry_form=0 order by id desc";
	}
	//echo $sql;
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo $data_array[0][csf('id')]."**".$data_array[0][csf('batch_no')]."**".$data_array[0][csf('color_id')]."**".$data_array[0][csf('booking_without_order')];
	}
	else
	{
		echo "0";
	}
	exit();	
}

if($action=="check_booking_no")
{
	$data=explode("**",$data);
	$book_ref = trim($data[0]);
	$company_id = trim($data[1]);
	$supplier_id = trim($data[2]);
	$sql = "select a.id, a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, c.job_no_prefix_num,c.style_ref_no, c.job_no, a.po_break_down_id, a.process, a.item_category, a.fabric_source, a.supplier_id, 1 as type ,sum(b.wo_qnty) as wo_qnty
	from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master c 
	where a.booking_no = b.booking_no and   a.booking_type=3 and  a.status_active=1 and a.is_deleted=0 and b.job_no=c.job_no  
	and b.process in (25,26,31,33,35,36,37,60,62,63,64,65,66,67,68,69,70,71,73,82,83,84,85,88,89,90,91,93,94,129,135,136,145,156,159,34,195,169,160,210,211,212,219,220,224,225,226,227,213,214,241) and a.booking_no ='$book_ref' and a.company_id = '$company_id' and a.supplier_id = '$supplier_id'
	group by a.id,a.booking_no_prefix_num, a.booking_no, c.job_no, a.booking_date, a.company_id, a.buyer_id, a.po_break_down_id, a.item_category, a.fabric_source, c.job_no_prefix_num,c.style_ref_no, a.supplier_id,a.process

	union all
	select a.id,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, 0 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id, 2 as type,0 as wo_qnty 
	from wo_non_ord_aop_booking_mst a , wo_non_ord_aop_booking_dtls b
	where a.id = b.wo_id  and  a.status_active=1 and a.is_deleted=0  and  a.wo_no = '$book_ref' and a.company_id = '$company_id' and a.supplier_id = '$supplier_id'
	group by a.id,a.wo_no_prefix_num , a.wo_no, a.booking_date, a.company_id, a.buyer_id,a.aop_source, a.supplier_id
	order by booking_no_prefix_num desc";
	
	//echo $sql;
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo $data_array[0][csf('id')]."**".$data_array[0][csf('booking_no')]."**".$data_array[0][csf('type')]."**".$data_array[0][csf('booking_date')];
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
	$company_data=sql_select("select id, company_name, company_short_name");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}


	$sql = "select  b.batch_id,b.prod_id, b.order_id, b.color_id, b.body_part_id, b.febric_description_id, b.process_id, b.fin_dia, b.fin_gsm, b.roll_id, 0 as barcode_no, b.batch_issue_qty as qnty, b.roll_no , b.booking_no, b.booking_dtls_id, b.outbound_batchname, b.booking_without_order, b.gsm, b.width, c.fabric_color_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, wo_booking_dtls c where a.id=b.mst_id and a.id=$update_id and c.id = b.booking_dtls_id and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
   //echo $sql;die;

	$results=sql_select($sql);
	$body_part_ids="";$roll_ids="";$order_ids="";$prod_ids="";$color_ids="";$booking_nos="";$febric_description_ids="";
	foreach($results as $row)
	{
		$booking_without_order_status=$row[csf('booking_without_order')];
		$body_part_ids.=$row[csf('body_part_id')].',';
		$roll_ids.=$row[csf('roll_id')].',';
		$order_ids.=$row[csf('order_id')].',';
		$prod_ids.=$row[csf('prod_id')].',';
		$color_ids.=$row[csf('color_id')].',';
		$color_ids.=$row[csf('fabric_color_id')].',';
		$booking_nos.="'".$row[csf('booking_no')]."'".',';
		$febric_description_ids.="'".$row[csf('febric_description_id')]."'".',';
		
	}

	$body_part_id_all=chop($body_part_ids,",");
	$roll_id_all=chop($roll_ids,",");
	$order_id_all=chop($order_ids,",");
	$prod_id_all=chop($prod_ids,",");
	$color_id_all=chop($color_ids,",");
	$booking_no_all=chop($booking_nos,",");
	$febric_description_id_all=chop($febric_description_ids,",");



	$color_arr=return_library_array( "select id, color_name from lib_color where id in($color_id_all)",'id','color_name');
	//$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	
	$supplier_data = sql_select("select id, supplier_name,address_1 from lib_supplier");

	foreach($supplier_data as $row)
	{
		$supplier_arr[$row[csf('id')]]["name"]=$row[csf('supplier_name')];
		$supplier_arr[$row[csf('id')]]["address"]=$row[csf('address_1')];
	}

	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	//$batch_arr=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no"  );
	//$subcon_batch_arr=return_library_array( "select b.id as id, b.outbound_batchname as batch_no from  inv_receive_mas_batchroll a,pro_grey_batch_dtls b where a.id=b.mst_id", "id", "batch_no"  );
	
	$dataArray=sql_select("select process_id,receive_date,dyeing_source,dyeing_company,batch_id from  inv_receive_mas_batchroll where id=$update_id");
	$dyeing_source=$dataArray[0][csf('dyeing_source')];

	
	$job_array=array();
	$job_sql="select a.buyer_name, a.job_no, a.style_ref_no, b.id, b.po_number, c.internal_ref from wo_po_details_master a, wo_po_break_down b left join wo_order_entry_internal_ref c on b.job_no_mst=c.job_no where a.job_no=b.job_no_mst and b.id in($order_id_all)";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['internal_ref']=$row[csf('internal_ref')];
		
	}

	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13 and id in($prod_id_all)");

	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}

	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($febric_description_id_all)";
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
		<table width="1000" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">  
					<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
					foreach ($nameArray as $result)
					{ 
						if($result[csf('plot_no')]!='') echo $result[csf('plot_no')].', ';
						if($result[csf('level_no')]!='') echo $result[csf('level_no')].', ';
						if($result[csf('road_no')]!='') echo $result[csf('road_no')].', ';
						if($result[csf('block_no')]!='') echo $result[csf('block_no')].', ';
						if($result[csf('city')]!='') echo $result[csf('city')].', ';
						if($result[csf('zip_code')]!='') echo $result[csf('zip_code')].', ';
						if($result[csf('province')]!='') echo $result[csf('province')].', ';
						if($result[csf('country_id')]!=0) echo "&nbsp;".$country_arr[$result[csf('country_id')]].'<br>';
						if($result[csf('email')]!='') echo "Email No: ".$result[csf('email')].', ';
							if($result[csf('website')]!='') echo "Website: ".$result[csf('website')];
							}
							?> 
						</td>  
					</tr>
					<tr>
						<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[3]; ?> Challan</u></strong></td>
					</tr>
					<tr>
						<td width="100"><strong>Company:</strong> </td>
						<td width="150">
							<? echo $company_array[$company]['name'] ;  ?>
						</td>
						<td width="120"><strong>Service Source :</strong></td>
						<td width="185"><? echo $knitting_source[$dataArray[0][csf('dyeing_source')]]; ?></td>
						<td width="125"><strong>Service Company:</strong></td><td width="175">
							<?
							//if ($dataArray[0][csf('dyeing_source')]==1) echo $company_array[$dataArray[0][csf('dyeing_company')]]['name']; else if ($dataArray[0][csf('dyeing_source')]==3) echo $supplier_arr[$dataArray[0][csf('dyeing_company')]]["name"]; 
							$dyeing_company=$dataArray[0][csf('dyeing_company')];
							if ($dataArray[0][csf('dyeing_source')]==1) 
							{
								echo $company_array[$dyeing_company]['name']; 
							}
						   else   echo $supplier_arr[$dyeing_company]["name"]; 
							?>
						</td>
					</tr>
					<tr>
						<td><strong>Issue Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
						<td><strong>Bar Code:</strong></td><td colspan="1" id="barcode_img_id"></td>
						<? if($dataArray[0][csf('dyeing_source')]==3){?>
						<td><strong>Location:</strong></td><td><? echo $supplier_arr[$dataArray[0][csf('dyeing_company')]]["address"];?></td>
						<?}?>

					</tr>

				</table>
				<br>
				<table cellspacing="0" width="1120"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<tr>
							<th width="30">SL</th>
							<th width="140">Booking No</th>
							<th width="50">No. of Roll </th>
							<th width="100">Batch No </th>
							<th width="120">Body Part</th>
							<th width="170">Const./Compo</th>
							<th width="50">Fin. Gsm</th>
							<th width="50">Fin. Dia</th>
							<th width="80">GMT. Color</th>
							<th width="80">Fabric Color</th>
							<th width="80">Process</th>
							<th width="60">Wgt.</th>
							<th>Buyer/Style/Job/Order/Internal Ref.No</th>
						</tr>

					</thead>
					<tbody>
						<?

				//================

				//echo $body_part_id_all;
		  		//echo 'hello world'; die;
						$bodypart_arr=array();$barcode_arr=array();
				//0.1477 sec
						$data_array=sql_select("SELECT  c.roll_id,b.body_part_id,c.barcode_no  FROM pro_grey_prod_entry_dtls b, pro_roll_details c WHERE  b.id=c.dtls_id  and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and b.order_id in($order_id_all) and b.prod_id in($prod_id_all)");


			   //and b.body_part_id in($body_part_id_all) and c.roll_id in($roll_id_all)		   
			   //die;
						foreach($data_array as $inf)
						{
							$bodypart_arr[$inf[csf('barcode_no')]]['bodypart']=$inf[csf('body_part_id')];
							$barcode_arr[$inf[csf('roll_id')]]['barcode_no']=$inf[csf('barcode_no')];
						}

				//0.0112 sec
						$feb_des_data = sql_select("select b.id as dtls_id, a.wo_no as booking_no,b.fabric_description as fab_des_id,b.fabric_source
							from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b where a.id = b.wo_id and a.company_id = $company and  a.status_active=1 and a.is_deleted=0  and b.status_active = 1 and a.wo_no in($booking_no_all) and b.fabric_description in($febric_description_id_all)
							union all
							select b.id as dtls_id, a.booking_no, b.fab_des_id,b.fabric_source
							from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b
							where a.id = b.mst_id and a.company_id = $company and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and a.booking_no in($booking_no_all) and b.fab_des_id in($febric_description_id_all)");

						$fab_des_ids_all="";
						foreach ($feb_des_data as $value) 
						{
							$feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["fabric_source"]= $value[csf("fabric_source")];
							$feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["feb_des_id"]= $value[csf("fab_des_id")];
							$fab_des_ids_all.=$value[csf("fab_des_id")].',';

						}

						$fab_des_id_all=chop($fab_des_ids_all,",");
						if($fab_des_id_all!=""){$fab_des_id_cond="and a.id in($fab_des_id_all)";}else{$fab_des_id_cond="";}

						if($booking_without_order_status!=0 && $fab_des_id_all!="")
						{

							$sql_non_order=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in($febric_description_id_all) $fab_des_id_cond order by a.id");

							foreach($sql_non_order as $row)
							{
								$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
							}
						}
						$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13 and id in($prod_id_all)",'id','product_name_details');
						if($fab_des_id_all!=""){$fab_des_id_cond_2="and c.id in($fab_des_id_all)";}else{$fab_des_id_cond_2="";}

						if($fab_des_id_all!="")
						{
							$sql_order_feb=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 and c.id in($febric_description_id_all) $fab_des_id_cond_2  order by c.id");
							foreach($sql_order_feb as $row)
							{
								$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
							}
						}

				//============================

						$result=sql_select($sql);

						$i=1;
						foreach($result as $row)
						{

							$gsm=$product_array[$row[csf("prod_id")]]['gsm'];
							$dia_width=$product_array[$row[csf("prod_id")]]['dia_width'];
							if($dia_width!='')
							{
								$dia_width=$dia_width;	
							}
							else
							{
								$dia_width=$row[csf("fin_dia")];
							}
							if($gsm!='')
							{
								$gsm=$gsm;	
							}
							else
							{
								$gsm=$row[csf("fin_gsm")];
							}
							?>
							<tr>
								<td align="center" valign="middle"><? echo $i; ?></td>
								<td align="center" ><? echo $row[csf("booking_no")]; ?></td>
								<td align="center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
								<td align="center"><? echo $row[csf('outbound_batchname')]; ?>&nbsp;</td>

								<td style="word-break:break-all;">
									<?

									if ($dataArray[0][csf('dyeing_source')]==3 || $dataArray[0][csf('dyeing_source')]==1) echo $body_part[$row[csf('body_part_id')]]; 
									else echo $body_part[$bodypart_arr[$row[csf('barcode_no')]]['bodypart']]; 
									?>&nbsp;</td>
									<td style="word-break:break-all;">
										<? 

										$feb_des_id = $feb_des_array[$row[csf("booking_no")]][$row[csf("booking_dtls_id")]]["feb_des_id"];
										$feb_des_source = $feb_des_array[$row[csf("booking_no")]][$row[csf("booking_dtls_id")]]["fabric_source"];
							//echo "order = ".$row[csf('order_id')]." , des id = ".$feb_des_id." , source = ".$feb_des_source;
										if($row[csf('order_id')] )
										{

											if ($dataArray[0][csf('dyeing_source')]==3 || $dataArray[0][csf('dyeing_source')]==1) echo $composition_arr[$row[csf("febric_description_id")]]; else echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; 
										}
										else
										{

											if($feb_des_id == "")
											{
												$fabric_details = $composition_arr[$val[csf("febric_description_id")]];
											}
											else
											{
												if($feb_des_source == 1)
												{
													$fabric_details = $fabric_description[$feb_des_id];
												}else{
													$fabric_details = $fabric_description2[$feb_des_id];
												}

											}
											echo chop($fabric_details,",");

										}

										?>&nbsp;
									</td>
                                    
									<td style="word-break:break-all;"><? echo $row[csf("gsm")]; // $gsm;?>&nbsp;</td>
									<td style="word-break:break-all;"><? echo $row[csf("width")]; //$dia_width; ?>&nbsp;</td>

									<td style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]];?>&nbsp;</td>
									<td style="word-break:break-all;"><? echo $color_arr[$row[csf("fabric_color_id")]]; ?>&nbsp;</td>
									<td style="word-break:break-all;"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?>&nbsp;</td>
									<td style="word-break:break-all;" align="right"><? if($row[csf("qnty")]!=0){echo $row[csf("qnty")];} ?></td>
									<td style="word-break:break-all;">
									<? //echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer_id']]."<hr/>".$job_array[$row[csf('order_id')]]['style_ref_no']."<hr/>".$job_array[$row[csf('order_id')]]['job']."<hr/>".$job_array[$row[csf('order_id')]]['po']; ?>
                                        <table cellspacing="0" width="100%"  border="1" rules="all" style="border:hidden;">
                                            <tr><td><? echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer_id']]; ?>&nbsp;</td></tr>
                                            <tr><td><? echo $job_array[$row[csf('order_id')]]['style_ref_no']; ?>&nbsp;</td></tr>
                                            <tr><td><? echo $job_array[$row[csf('order_id')]]['job']; ?>&nbsp;</td></tr>
                                            <tr><td><? echo $job_array[$row[csf('order_id')]]['po']; ?>&nbsp;</td></tr>
                                            <tr><td><? echo $job_array[$row[csf('order_id')]]['internal_ref']; ?>&nbsp;</td></tr>
                                        </table>
                                    </td>
								</tr>
								<?
								$total_weight+=$row[csf("qnty")];
								$total_roll+=$row[csf('roll_no')];
								$i++;
							}

							?>
						</tbody>
						<tfoot>
							<tr>
								<th>&nbsp;</th>
								<th>Total:</th>
								<th><? echo $total_roll;?></th>

								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th align="right">Total:</th>
								<th align="right"><? if($total_weight!=0){echo $total_weight;} ?></th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
					</table>
				</div>
				<? echo signature_table(145, $company, "900px"); ?>
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


if($action=="subprocess_issue_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_issue_no=$data[3];
	$update_id=$data[1];

	$company_array=array();
	$company_data=sql_select("select a.id, a.company_name, a.company_short_name, b.location_name from lib_company a left join lib_location b on a.id = b.company_id");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
		$company_array[$row[csf('id')]]['location']=$row[csf('location_name')];
		$company_array2[$row[csf('id')]]=$row[csf('company_name')];
	}



	$sql = "select  b.batch_id,b.prod_id, b.order_id, b.color_id, b.body_part_id, b.febric_description_id, b.process_id, b.fin_dia,b.booking_without_order, b.fin_gsm,b.gsm,b.width, b.roll_id, 0 as barcode_no, b.batch_issue_qty as qnty, roll_no, b.outbound_batchname,b.booking_no,b.booking_dtls_id, b.remarks, c.fabric_color_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, wo_booking_dtls c where a.id=b.mst_id and a.id=$update_id and b.booking_dtls_id=c.id and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.batch_issue_qty>0";

	//echo $sql;
	$results=sql_select($sql);

	
	$body_part_ids="";$roll_ids="";$order_ids="";$prod_ids="";$color_ids="";$booking_nos="";$febric_description_ids="";$barcode_nos="";
	foreach($results as $row)
	{
		$booking_without_order_status=$row[csf('booking_without_order')];
		$body_part_ids.=$row[csf('body_part_id')].',';
		$roll_ids.=$row[csf('roll_id')].',';
		$order_ids.=$row[csf('order_id')].',';
		$prod_ids.=$row[csf('prod_id')].',';
		$color_ids.=$row[csf('color_id')].',';
		$color_ids.=$row[csf('fabric_color_id')].',';
		$booking_nos.="'".$row[csf('booking_no')]."'".',';
		//$febric_description_ids.="'".$row[csf('febric_description_id')]."'".',';
		$febric_description_ids.=$row[csf('febric_description_id')].',';
		$barcode_nos.="'".$row[csf('barcode_no')]."'".',';
		
	}

	$body_part_id_all=chop($body_part_ids,",");
	$roll_id_all=chop($roll_ids,",");
	$order_id_all=chop($order_ids,",");
	$prod_id_all=chop($prod_ids,",");
	$color_id_all=chop($color_ids,",");
	$booking_no_all=chop($booking_nos,",");
	$febric_description_id_all=chop($febric_description_ids,",");
	$barcode_no_all=chop($barcode_nos,",");

	$color_arr=return_library_array( "select id, color_name from lib_color where id in($color_id_all)",'id','color_name');
	

	//$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$supplier_data = sql_select("select id, supplier_name,address_1 from lib_supplier");

	foreach($supplier_data as $row)
	{
		$supplier_arr[$row[csf('id')]]["name"]=$row[csf('supplier_name')];
		$supplier_arr[$row[csf('id')]]["address"]=$row[csf('address_1')];
	}

	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	
	$dataArray=sql_select("select process_id,receive_date,dyeing_source,dyeing_company,batch_id,gate_pass_no,do_no,car_no from  inv_receive_mas_batchroll where id=$update_id");
	$dyeing_source=$dataArray[0][csf('dyeing_source')];
	$job_array=array();

	$job_sql="select a.buyer_name, a.job_no, a.style_ref_no, b.id, b.po_number, c.internal_ref from wo_po_details_master a, wo_po_break_down b left join wo_order_entry_internal_ref c on b.job_no_mst=c.job_no where a.job_no=b.job_no_mst and b.id in($order_id_all)";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['internal_ref']=$row[csf('internal_ref')];
	}

	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13 and id in($prod_id_all)");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}
	
	$composition_arr=array();
	 $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($febric_description_id_all)";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			//$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			//$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
	}
	$feb_des_data = sql_select("select b.id as dtls_id, a.wo_no as booking_no,b.fabric_description as fab_des_id,b.fabric_source
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b where a.id = b.wo_id and a.company_id = $company and  a.status_active=1 and a.is_deleted=0  and b.status_active = 1
		union all
		select b.id as dtls_id, a.booking_no, b.fab_des_id,b.fabric_source
		from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b
		where a.id = b.mst_id and a.company_id = $company and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and a.booking_no in($booking_no_all) and b.fab_des_id in($febric_description_id_all)");
	$fab_des_ids_all="";
	foreach ($feb_des_data as $value) 
	{
		$feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["fabric_source"]= $value[csf("fabric_source")];
		$feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["feb_des_id"]= $value[csf("fab_des_id")];
		$fab_des_ids_all.=$value[csf("fab_des_id")].',';
	}
	$fab_des_id_all=chop($fab_des_ids_all,",");
	if($fab_des_id_all!=""){$fab_des_id_cond="and a.id in($fab_des_id_all)";}else{$fab_des_id_cond="";}
	if($fab_des_id_all!=""){$fab_des_id_cond_2="and c.id in($fab_des_id_all)";}else{$fab_des_id_cond_2="";}

	if($booking_without_order_status!=0 && $fab_des_id_all!="")
	{
		$sql_non_order=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.id in($febric_description_id_all) $fab_des_id_cond order by a.id");


		foreach($sql_non_order as $row)
		{
			$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
		}
	}
	
	$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13 and id in($prod_id_all)",'id','product_name_details');
	if($fab_des_id_all!="")
	{
		$sql_order_feb=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 and c.id in($febric_description_id_all) $fab_des_id_cond_2 order by c.id");

		foreach($sql_order_feb as $row)
		{
			$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
		}
	}

	
		//print_r($feb_des_array);


	$uomFromBookingRes = sql_select("select a.uom, a.id, a.booking_no
		from wo_booking_dtls a where a.is_deleted = 0 and a.status_active = 1 and a.booking_no in($booking_no_all) 
		union all 
		select b.uom, b.id,b.wo_no as booking_no
		from wo_non_ord_aop_booking_dtls b where b.is_deleted = 0 and b.status_active = 1 and b.wo_no in($booking_no_all)"); 
	foreach ($uomFromBookingRes as $val) 
	{
		$uomFromBookingArr[$val[csf("booking_no")]][$val[csf("id")]]["uom"] = $unit_of_measurement[$val[csf("uom")]];
	}

	
	?>
	<div>
		<table width="1220" cellspacing="0">
			<tr>
				<td colspan="6" align="center" style="font-size:22px"><strong><? echo $company_array[$company]['name']; ?></strong></td>

			</tr>
			<tr class="form_caption">
                <td colspan="6" align="center" style="font-size:14px">  
					<?
                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
                    foreach ($nameArray as $result)
                    { 
						if($result[csf('plot_no')]!='') echo $result[csf('plot_no')].', ';
						if($result[csf('level_no')]!='') echo $result[csf('level_no')].', ';
						if($result[csf('road_no')]!='') echo $result[csf('road_no')].', ';
						if($result[csf('block_no')]!='') echo $result[csf('block_no')].', ';
						if($result[csf('city')]!='') echo $result[csf('city')].', ';
						if($result[csf('zip_code')]!='') echo $result[csf('zip_code')].', ';
						if($result[csf('province')]!='') echo $result[csf('province')].', ';
						if($result[csf('country_id')]!=0) echo "&nbsp;".$country_arr[$result[csf('country_id')]].'.<br>';
						if($result[csf('email')]!='') echo "Email : ".$result[csf('email')].', ';
						if($result[csf('website')]!='') echo "Website : ".$result[csf('website')];
                    }
                    ?> 
                    <b style="float:right;"> Print Time : <? echo $date=date("F j, Y, g:i a"); ?></b> 
                </td> 
			</tr>
            <tr>
                <!--<td colspan="6" align="center" style="font-size:18px"><strong><u><? //echo $data[3]; ?> Challan</u></strong></td>-->
                <td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $conversion_cost_head_array[$results[0][csf("process_id")]]; ?> Issue Challan </u></strong></td>
            </tr>
            <tr>
                <td width="100"><strong>Challan No. : </strong> </td>
                <td width="120">
                    <? echo $data[3];//$company_array[$company]['name'] ;  ?>
                </td>
                <td width="120"><strong>Service Source : </strong></td>
                <td width="185"><? echo $knitting_source[$dataArray[0][csf('dyeing_source')]]; ?></td>
                <td width="125"><strong>Service Company : </strong></td><td width="175">
                    <?
                    $dyeing_company=$dataArray[0][csf('dyeing_company')];
                    if ($dataArray[0][csf('dyeing_source')]==1) 
                        echo $company_array2[$dyeing_company]; 
                    else //($dataArray[0][csf('dyeing_source')]==3)
                        echo $supplier_arr[$dataArray[0][csf('dyeing_company')]]["name"]; 
                    ?>
                </td>
            </tr>
			<tr>
				<td><strong>Issue Date : </strong></td><td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
				<td><strong>Bar Code : </strong></td><td colspan="1" id="barcode_img_id"></td>
				<? if($dataArray[0][csf('dyeing_source')]==3)
				{
					?>
					<td><strong>Location : </strong></td><td><? echo $supplier_arr[$dataArray[0][csf('dyeing_company')]]["address"]?></td>
					<? 
				}
				?>

			</tr>
			<tr>
				<td><strong>Gate Pass No : </strong></td><td width="185px"><?  echo $dataArray[0][csf('gate_pass_no')]; ?></td>
				<td><strong>DO No : </strong></td><td width="175px"><?  echo $dataArray[0][csf('do_no')]; ?></td> 
				<td><strong>Car No : </strong></td><td width="175px"><?  echo $dataArray[0][csf('car_no')]; ?></td> 
			</tr>
		</table>
		<br>
		<table cellspacing="0" width="1220"  border="1" rules="all" class="rpt_table" >
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th width="30">SL</th>
					<th width="140">Booking No</th>
					<th width="50">No. of Roll</th>
					<th width="100">Batch No </th>
					<th width="120">Body Part</th>
					<th width="170">Const./Compo</th>
					<th width="50">Fin. Gsm</th>
					<th width="50">Fin. Dia</th>
					<th width="80">GMT. Color</th>
					<th width="80">Fabric Color</th>
					<th width="80">Process</th>
					<th width="60">Wgt.</th>
					<th width="50">UOM</th>
					<th>Buyer/Style/Job/Order/ Internal Ref.No</th>
					<th width="150">Remarks</th>
				</tr>

			</thead>
			<tbody>
				<?
				
				$bodypart_arr=array();$barcode_arr=array();
				$data_array=sql_select("SELECT  c.roll_id,b.body_part_id,c.barcode_no,b.no_of_roll,b.uom  FROM pro_grey_prod_entry_dtls b, pro_roll_details c WHERE  b.id=c.dtls_id  and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.roll_id in($roll_id_all) and c.barcode_no in($barcode_no_all)");
				foreach($data_array as $inf)
				{
					$bodypart_arr[$inf[csf('barcode_no')]]['bodypart']=$inf[csf('body_part_id')];
					$barcode_arr[$inf[csf('roll_id')]]['barcode_no']=$inf[csf('barcode_no')];
					$barcode_arr[$inf[csf('roll_id')]]['no_of_roll']=$inf[csf('no_of_roll')];
					$barcode_arr[$inf[csf('roll_id')]]['uom']=$inf[csf('uom')];
				}
				
				$result=sql_select($sql);
				$i=1;
				foreach($result as $row)
				{
					$feb_des_id = $feb_des_array[$row[csf("booking_no")]][$row[csf("booking_dtls_id")]]["feb_des_id"];
					$feb_des_source = $feb_des_array[$row[csf("booking_no")]][$row[csf("booking_dtls_id")]]["fabric_source"];
					?>
					<tr>
						<td align="center" valign="middle"><? echo $i; ?></td>
						<td align="center"><? echo $row[csf("booking_no")]; ?></td>
						<td align="center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
						<td align="center"><? echo $row[csf('outbound_batchname')]; ?>&nbsp;</td>

						<td style="word-break:break-all;">
							<?

							//if ($dataArray[0][csf('dyeing_source')]==3) echo $body_part[$row[csf('body_part_id')]]; 
							//else echo $body_part[$bodypart_arr[$row[csf('barcode_no')]]['bodypart']]; 
							echo $body_part[$row[csf('body_part_id')]]; 
							?>&nbsp;</td>
							<td style="word-break:break-all;" title="DeterId=<? echo $row[csf("febric_description_id")];?>">
								<?
							//echo "order = ".$row[csf('order_id')]." , des id = ".$row[csf("febric_description_id")]." , source = ".$dataArray[0][csf('dyeing_source')];
								if($row[csf('order_id')] )
								{

									if ($dataArray[0][csf('dyeing_source')]==3) 
									{ 
									echo $composition_arr[$row[csf("febric_description_id")]];
									}
									else {
										 echo $composition_arr[$row[csf("febric_description_id")]];
									}//echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']]; 
								}
								else
								{

									if($feb_des_id == "")
									{
										$fabric_details = $composition_arr[$row[csf("febric_description_id")]];
									}
									else
									{
										if($feb_des_source == 1)
										{
											$fabric_details = $fabric_description[$feb_des_id];
										}else{
											$fabric_details = $fabric_description2[$feb_des_id];
										}

									}
									echo chop($fabric_details,",");

								}
								?>
								&nbsp;
							</td>
							<td style="word-break:break-all;"><? echo $row[csf('gsm')]; ?>&nbsp;</td>
							<td style="word-break:break-all;"><? echo $row[csf('width')]; ?>&nbsp;</td>

							<td style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]];?>&nbsp;</td>
							<td style="word-break:break-all;"><? echo $color_arr[$row[csf("fabric_color_id")]];?>&nbsp;</td>
							<td style="word-break:break-all;"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?>&nbsp;</td>
							<td style="word-break:break-all;" align="right"><? if($row[csf("qnty")]!=0){ echo $row[csf("qnty")];} ?></td>
							<td style="word-break:break-all;" align="center">
								<? echo $uomFromBookingArr[$row[csf("booking_no")]][$row[csf("booking_dtls_id")]]["uom"]; ?>
							</td>
							<td style="word-break:break-all;">
							<? //echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer_id']]."<hr/>".$job_array[$row[csf('order_id')]]['style_ref_no']."<hr/>".$job_array[$row[csf('order_id')]]['job']."<hr/>".$job_array[$row[csf('order_id')]]['po']."<hr/>".$job_array[$row[csf('order_id')]]['internal_ref']; ?>
                            	<table cellspacing="0" width="100%"  border="1" rules="all" style="border:hidden;">
                                	<tr><td><? echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer_id']]; ?>&nbsp;</td></tr>
                                    <tr><td><? echo $job_array[$row[csf('order_id')]]['style_ref_no']; ?>&nbsp;</td></tr>
                                    <tr><td><? echo $job_array[$row[csf('order_id')]]['job']; ?>&nbsp;</td></tr>
                                    <tr><td><? echo $job_array[$row[csf('order_id')]]['po']; ?>&nbsp;</td></tr>
                                    <tr><td><? echo $job_array[$row[csf('order_id')]]['internal_ref']; ?>&nbsp;</td></tr>
                                </table>
                            </td>
                            <td style="word-break: break-word">
                            	<?php echo $row[csf('remarks')]; ?>
                            </td>
                            
						</tr>
						<?
						$total_weight+=$row[csf("qnty")];
						$total_roll+=$row[csf('roll_no')];
						$i++;
					}

					?>
				</tbody>
				<tfoot>
					<tr>
						<th>&nbsp;</th>
						<th>Total:</th>
						<th><? echo $total_roll;?></th>

						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align="right">Total:</th>
						<!--  <th>&nbsp;</th> -->
						<th align="right"><? if($total_weight!=0){echo $total_weight;} ?></th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
		</div>
		<? echo signature_table(145, $company, "1050px"); ?>
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
if($action=="subprocess_issue_print_3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$update_id=$data[1];
	$issue_number=str_replace(",","','",$data[3]);
	$txt_issue_no=$data[3];
	$serviceSource=$data[4];
	$serviceCompany=$data[5];

	$company_array=array();
	$company_data=sql_select("select a.id, a.company_name, a.company_short_name, b.location_name from lib_company a left join lib_location b on a.id = b.company_id");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
		$company_array[$row[csf('id')]]['location']=$row[csf('location_name')];
		$company_array2[$row[csf('id')]]=$row[csf('company_name')];
	}


	//if ($order_wise_booking_all!="") {
		$sql = "select  a.recv_number,b.batch_id,b.prod_id, b.order_id, b.color_id, b.body_part_id, b.febric_description_id, b.process_id, b.fin_dia,b.booking_without_order, b.fin_gsm,b.gsm,b.width, b.roll_id, 0 as barcode_no, b.batch_issue_qty as qnty, b.roll_no, b.outbound_batchname,b.booking_no,b.booking_dtls_id, c.fabric_color_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, wo_booking_dtls c where a.id=b.mst_id and c.id=b.booking_dtls_id and a.recv_number in('".$issue_number."') and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by receive_date";

		// echo $sql;
	//}
	/*if ($non_order_booking_all!="") {
		$sql_nonOrder = "select  a.recv_number,b.batch_id,b.prod_id, b.order_id, b.color_id, b.body_part_id, b.febric_description_id, b.process_id, b.fin_dia,b.booking_without_order, b.fin_gsm,b.gsm,b.width, b.roll_id, 0 as barcode_no, b.batch_issue_qty as qnty, roll_no, b.outbound_batchname,b.booking_no,b.booking_dtls_id from inv_receive_mas_batchroll a, pro_grey_batch_dtls b where a.id=b.mst_id and a.recv_number in('".$issue_number."') and b.booking_no in($non_order_booking_all) and  a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	}*/

	

	//echo $sql;
	$results=sql_select($sql);
	
	$body_part_ids="";$roll_ids="";$order_ids="";$prod_ids="";$color_ids="";$booking_nos="";$febric_description_ids="";$barcode_nos="";
	foreach($results as $row)
	{
		$booking_without_order_status=$row[csf('booking_without_order')];
		$body_part_ids.=$row[csf('body_part_id')].',';
		$roll_ids.=$row[csf('roll_id')].',';
		if ($row[csf('order_id')]!="") {
			$order_ids.=$row[csf('order_id')].',';
		}
		$prod_ids.=$row[csf('prod_id')].',';
		if ($row[csf('color_id')]!="") {
			$color_ids.=$row[csf('color_id')].',';
			$color_ids.=$row[csf('fabric_color_id')].',';
		}
		$booking_nos.="'".$row[csf('booking_no')]."'".',';
		$febric_description_ids.="'".$row[csf('febric_description_id')]."'".',';
		$barcode_nos.="'".$row[csf('barcode_no')]."'".',';
		
	}

	$body_part_id_all=chop($body_part_ids,",");
	$roll_id_all=chop($roll_ids,",");
	$order_id_all=chop($order_ids,",");
	$prod_id_all=chop($prod_ids,",");
	$color_id_all=chop($color_ids,",");
	$booking_no_all=chop($booking_nos,",");
	$febric_description_id_all=chop($febric_description_ids,",");
	$barcode_no_all=chop($barcode_nos,",");

	$color_arr=return_library_array( "select id, color_name from lib_color where id in($color_id_all)",'id','color_name');
	//$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$supplier_data = sql_select("select id, supplier_name,address_1 from lib_supplier");

	foreach($supplier_data as $row)
	{
		$supplier_arr[$row[csf('id')]]["name"]=$row[csf('supplier_name')];
		$supplier_arr[$row[csf('id')]]["address"]=$row[csf('address_1')];
	}

	$buyer_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	
	$dataArray=sql_select("select process_id,receive_date,dyeing_source,dyeing_company,batch_id,gate_pass_no,do_no,car_no from  inv_receive_mas_batchroll where recv_number in('".$issue_number."')");

	$dyeing_source=$dataArray[0][csf('dyeing_source')];
	$job_array=array();

	$job_sql="select a.buyer_name, a.job_no, a.style_ref_no, b.id, b.po_number, c.internal_ref from wo_po_details_master a, wo_po_break_down b left join wo_order_entry_internal_ref c on b.job_no_mst=c.job_no where a.job_no=b.job_no_mst and b.id in($order_id_all)";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['buyer_id']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['internal_ref']=$row[csf('internal_ref')];
	}

	$product_array=array();
	$product_sql = sql_select("select id, detarmination_id, gsm, dia_width, unit_of_measure from product_details_master where item_category_id=13 and id in($prod_id_all)");
	foreach($product_sql as $row)
	{
		$product_array[$row[csf("id")]]['gsm']=$row[csf("gsm")];
		$product_array[$row[csf("id")]]['dia_width']=$row[csf("dia_width")];
		$product_array[$row[csf("id")]]['deter_id']=$row[csf("detarmination_id")];
		$product_array[$row[csf("id")]]['uom']=$row[csf("unit_of_measure")];
	}
	
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id in($febric_description_id_all)";
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
	
	

	$feb_des_data = sql_select("select b.id as dtls_id, a.wo_no as booking_no,b.fabric_description as fab_des_id,b.fabric_source
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b where a.id = b.wo_id and a.company_id = $company and a.wo_no in($booking_no_all) and  a.status_active=1 and a.is_deleted=0  and b.status_active = 1
		union all
		select b.id as dtls_id, a.booking_no, b.fab_des_id,b.fabric_source
		from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b
		where a.id = b.mst_id and a.company_id = $company and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and a.booking_no in($booking_no_all) and b.fab_des_id in($febric_description_id_all)");
	$fab_des_ids_all="";
	foreach ($feb_des_data as $value) 
	{
		$feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["fabric_source"]= $value[csf("fabric_source")];
		$feb_des_array[$value[csf("booking_no")]][$value[csf("dtls_id")]]["feb_des_id"]= $value[csf("fab_des_id")];
		$fab_des_ids_all.=$value[csf("fab_des_id")].',';
	}
	$fab_des_id_all=chop($fab_des_ids_all,",");
	if($fab_des_id_all!=""){$fab_des_id_cond="and a.id in($fab_des_id_all)";}else{$fab_des_id_cond="";}
	if($fab_des_id_all!=""){$fab_des_id_cond_2="and c.id in($fab_des_id_all)";}else{$fab_des_id_cond_2="";}

	if($fab_des_id_all!="")
	{
		$sql_non_order=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0  order by a.id");


		foreach($sql_non_order as $row)
		{
			$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
		}
	}
	
	$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13 and id in($prod_id_all)",'id','product_name_details');
	if($fab_des_id_all!="")
	{
		$sql_order_feb=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 and c.id in($febric_description_id_all) $fab_des_id_cond_2 order by c.id");

		foreach($sql_order_feb as $row)
		{
			$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
		}
	}

	
		//print_r($feb_des_array);


	$uomFromBookingRes = sql_select("select a.uom, a.id, a.booking_no
		from wo_booking_dtls a where a.is_deleted = 0 and a.status_active = 1 and a.booking_no in($booking_no_all) 
		union all 
		select b.uom, b.id,b.wo_no as booking_no
		from wo_non_ord_aop_booking_dtls b where b.is_deleted = 0 and b.status_active = 1 and b.wo_no in($booking_no_all)"); 
	foreach ($uomFromBookingRes as $val) 
	{
		$uomFromBookingArr[$val[csf("booking_no")]][$val[csf("id")]]["uom"] = $unit_of_measurement[$val[csf("uom")]];
	}

	
	?>
	<div style="height:auto;">

		<table width="1500" cellspacing="0" id="summaryTbl">
			<tr>
				<td colspan="2"></td>
				<td colspan="2" style="font-size:32px; text-align: center;"><strong><span><? echo $company_array[$company]['name']; ?></span></strong> </td>
				<td colspan="2">
				<span><b  style="float:right; font-size:22px;"> Print Time : <? echo $date=date("F j, Y, g:i a"); ?></b> </span></td>
			</tr>
			<tr class="form_caption">
                <td colspan="6" align="center" style="font-size:24px">  <span style="margin-left: -170px; margin-bottom: 150px;">
					<?
                    $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
                    foreach ($nameArray as $result)
                    { 
						if($result[csf('plot_no')]!='') echo $result[csf('plot_no')].', ';
						if($result[csf('level_no')]!='') echo $result[csf('level_no')].', ';
						if($result[csf('road_no')]!='') echo $result[csf('road_no')].', ';
						if($result[csf('block_no')]!='') echo $result[csf('block_no')].', ';
						if($result[csf('city')]!='') echo $result[csf('city')].', ';
						if($result[csf('zip_code')]!='') echo $result[csf('zip_code')].', ';
						if($result[csf('province')]!='') echo $result[csf('province')].', ';
						if($result[csf('country_id')]!=0) echo "&nbsp;".$country_arr[$result[csf('country_id')]].'.<br>';
						//if($result[csf('email')]!='') echo "Email : ".$result[csf('email')].', ';
						//if($result[csf('website')]!='') echo "Website : ".$result[csf('website')];
                    }
                    ?> 
                    </span>
                </td> 
			</tr>
            <tr>
                <!--<td colspan="6" align="center" style="font-size:18px"><strong><u><? //echo $data[3]; ?> Challan</u></strong></td>-->
                <!-- <td colspan="6" align="center" style="font-size:18px"><strong><u><? //echo $conversion_cost_head_array[$results[0][csf("process_id")]]; ?> Issue Challan </u></strong></td> -->
            </tr>
            <tr>
                <td width="100"><strong>Company: </strong> </td>
                <td width="150">
                    <? echo $company_array[$company]['name'];  ?>
                </td>
                <td width="120"><strong>Service Source : </strong></td>
                <td width="185"><? echo $knitting_source[$serviceSource]; ?></td>
                <td width="125"><strong>Service Company : </strong></td><td width="175">
                    <?
                    //$dyeing_company=$dataArray[0][csf('dyeing_company')];
                    if ($serviceSource==1) 
                        echo $company_array2[$serviceCompany]; 
                    else //($dataArray[0][csf('dyeing_source')]==3)
                        echo $supplier_arr[$serviceCompany]["name"];
                    ?>
                </td>
            </tr>
            <tr>
            	<td colspan="6">&nbsp;</td>
            </tr>
			<tr>
				
				<? 

				if($serviceSource==1)
				{
					 $nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
                    foreach ($nameArray as $result)
                    { 
                    	?>
                    	<td><strong>Location : </strong></td><td colspan="2">
                    		<?
						if($result[csf('plot_no')]!='') echo $result[csf('plot_no')].', ';
						if($result[csf('level_no')]!='') echo $result[csf('level_no')].', ';
						if($result[csf('road_no')]!='') echo $result[csf('road_no')].', ';
						if($result[csf('block_no')]!='') echo $result[csf('block_no')].', ';
						if($result[csf('city')]!='') echo $result[csf('city')].', ';
						if($result[csf('zip_code')]!='') echo $result[csf('zip_code')].', ';
						if($result[csf('province')]!='') echo $result[csf('province')].', ';
						if($result[csf('country_id')]!=0) echo "&nbsp;".$country_arr[$result[csf('country_id')]].'.<br>';
                    }
                    ?>
						</td>
                    <?
				}
				else if($serviceSource==3)
				{
					?>
					<td><strong>Location : </strong></td><td colspan="2"><? echo $supplier_arr[$serviceCompany]["address"]; ?></td>
					<? 
				}
				?>

			</tr>
			
		</table>
		<br>

		<table cellspacing="0" width="1500"  border="1" rules="all" class="rpt_table main_tbl">
			
			<thead bgcolor="#dddddd" align="center">
				<tr>
					<th width="30">SL</th>
					<th width="150">Challan No</th>
					<th width="150">Booking No</th>
					<th width="50">No. of Roll</th>
					<th width="150">Batch No </th>
					<th width="150">Body Part</th>
					<th width="200">Const./Compo</th>
					<th width="50">Fin. Gsm</th>
					<th width="50">Fin. Dia</th>
					<th width="80">GMT. Color</th>
					<th width="80">Fabric Color</th>
					<th width="120">Process</th>
					<th width="60">Weight</th>
					<!-- <th width="50">UOM</th> -->
					<th>Buyer/Style/Job/Internal Ref.No/Order</th> 
				</tr>

			</thead>

			<tbody>
				<?
				
				$bodypart_arr=array();$barcode_arr=array();
				$data_array=sql_select("SELECT  c.roll_id,b.body_part_id,c.barcode_no,b.no_of_roll,b.uom  FROM pro_grey_prod_entry_dtls b, pro_roll_details c WHERE  b.id=c.dtls_id  and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.roll_id in($roll_id_all) and c.barcode_no in($barcode_no_all)");

				foreach($data_array as $inf)
				{
					$bodypart_arr[$inf[csf('barcode_no')]]['bodypart']=$inf[csf('body_part_id')];
					$barcode_arr[$inf[csf('roll_id')]]['barcode_no']=$inf[csf('barcode_no')];
					$barcode_arr[$inf[csf('roll_id')]]['no_of_roll']=$inf[csf('no_of_roll')];
					$barcode_arr[$inf[csf('roll_id')]]['uom']=$inf[csf('uom')];
				}
				
				$result=sql_select($sql);
				//echo "string";die;
				//$result_nonOrder=sql_select($sql_nonOrder);

				$i=1;

				foreach($result as $row)
				{
					$feb_des_id = $feb_des_array[$row[csf("booking_no")]][$row[csf("booking_dtls_id")]]["feb_des_id"];
					$feb_des_source = $feb_des_array[$row[csf("booking_no")]][$row[csf("booking_dtls_id")]]["fabric_source"];
					?>
					<tr id="mainTr">
						<td align="center" valign="middle"><? echo $i; ?></td>
						<td align="center"><? echo $row[csf("recv_number")]; ?></td>
						<td align="center"><? echo $row[csf("booking_no")]; ?></td>
						<td align="center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
						<td align="center"><? echo $row[csf('outbound_batchname')]; ?>&nbsp;</td>
						<td id="bodyPart" align="center" style="word-wrap: break-word;">
							<?
							if ($dataArray[0][csf('dyeing_source')]==3) 
								echo $body_part[$row[csf('body_part_id')]]; 
							else echo $body_part[$row[csf('body_part_id')]]; //echo $body_part[$bodypart_arr[$row[csf('barcode_no')]]['bodypart']];
							
							?>&nbsp;
						
						</td>
						<td style="word-wrap: break-word;">
							<?
						//echo "order = ".$row[csf('order_id')]." , des id = ".$feb_des_id." , source = ".$feb_des_source;
							if($row[csf('order_id')] )
							{

								if ($dataArray[0][csf('dyeing_source')]==3){ echo $composition_arr[$row[csf("febric_description_id")]];} else{ 
								echo $composition_arr[$row[csf("febric_description_id")]]; 
								//echo $composition_arr[$product_array[$row[csf("prod_id")]]['deter_id']];
								} 
							}
							else
							{

								if($feb_des_id == "")
								{
									$fabric_details = $composition_arr[$row[csf("febric_description_id")]];
								}
								else
								{
									if($feb_des_source == 1)
									{
										$fabric_details = explode(",", $fabric_description[$feb_des_id]);
										$fabric_details = $fabric_details[1];
									}else{
										$fabric_details = $fabric_description2[$feb_des_id];
									}

								}
								echo chop($fabric_details,",");
							}
							?>
							&nbsp;
						</td>
						<td align="center" style="word-wrap: break-word;"><? echo $row[csf('gsm')]; ?>&nbsp;</td>
						<td align="center" style="word-wrap: break-word;"><? echo $row[csf('width')]; ?>&nbsp;</td>
						<td align="center" style="word-wrap: break-word;"><? echo $color_arr[$row[csf("color_id")]];?>&nbsp;</td>
						<td align="center" style="word-wrap: break-word;"><? echo $color_arr[$row[csf("fabric_color_id")]];?>&nbsp;</td>
						<td align="center" style="word-wrap: break-word;"><? echo $conversion_cost_head_array[$row[csf("process_id")]]; ?>&nbsp;</td>
						<td align="center" style="word-wrap: break-word;" align="right"><? if($row[csf("qnty")]!=0){ echo $row[csf("qnty")];} ?></td>
						<!-- <td style="word-break:break-all;" align="center">
							<? //echo $uomFromBookingArr[$row[csf("booking_no")]][$row[csf("booking_dtls_id")]]["uom"]; ?>
						</td> -->
						<td align="center" style="word-wrap: break-word;">
						<? //echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer_id']]."<hr/>".$job_array[$row[csf('order_id')]]['style_ref_no']."<hr/>".$job_array[$row[csf('order_id')]]['job']."<hr/>".$job_array[$row[csf('order_id')]]['po']."<hr/>".$job_array[$row[csf('order_id')]]['internal_ref']; ?>
							<table id="subTable" cellspacing="0" width="100%"  border="1" rules="all" style="border:hidden;">
                            	<tr><td align="center"><? echo $buyer_array[$job_array[$row[csf('order_id')]]['buyer_id']]; ?>&nbsp;</td></tr>
                                <tr><td align="center"><? echo $job_array[$row[csf('order_id')]]['style_ref_no']; ?>&nbsp;</td></tr>
                                <tr><td align="center"><? echo $job_array[$row[csf('order_id')]]['job']; ?>&nbsp;</td></tr>
                                <tr><td align="center"><? echo $job_array[$row[csf('order_id')]]['internal_ref']; ?>&nbsp;</td></tr>
                                <tr><td align="center"><? echo $job_array[$row[csf('order_id')]]['po']; ?>&nbsp;</td></tr>
                            </table>
                        	
                        </td>
					</tr>
					<?
					$total_weight+=$row[csf("qnty")];
					$total_roll+=$row[csf('roll_no')];
					$i++;
				}
				?>
				<tr id="trTotal">
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>Total:</th>
					<th><? echo $total_roll;?></th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th align="right">Total:</th>
					<!--  <th>&nbsp;</th> -->
					<th align="center"><? if($total_weight!=0){echo $total_weight;} ?></th>
					<!-- <th>&nbsp;</th> -->
					<th>&nbsp;</th>
				</tr>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="13" id="singnatureId">
							<? echo signature_table(145, $company, "1500px"); ?>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
				<style>
				table.main_tbl tfoot
				{
					border-top: 1px solid #000;
				 	border-left: 2px solid #FFFFFF;
				 	border-right: 2px solid #FFFFFF;
				 	border-bottom: 2px solid #FFFFFF;
				}
				/* #signatureTblId{
				  	font-size: 50px;
				  }*/
				/* thead{
				  	border: 26px;
				  }*/
				 
				/*@media print {
				  tr:nth-of-type(10n){
				    page-break-after: always;

				  }
				}*/
			.signatureDiv {
				/*font-size: 9px;
				color: #f00;
				text-align: center;*/
				}

				/*@media print {
					.signatureDiv {
					position: fixed;
					bottom: 0;
					}
				}*/
				@media print {
						#mainTr{
					  	font-size: 26px;
					  }
					  #subTable{
					  	font-size: 26px;
					  }
					  thead{
					  	font-size: 26px;
					  	border: 15px;
					  }
					  #summaryTbl{
					  	font-size: 26px;
					  }
					  #trTotal{
					  	font-size: 26px;
					  }
					 /* #singnatureId{
					  	font-size: 26px;
					  }*/
					
				  
				}
				
				</style> 
				<!-- <div class="signatureDiv">
		     		<? //echo signature_table(145, $company, "1250px"); ?>
		    	</div>  -->
		
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


if($action == "populate_wo_data")
{
	$book_data = explode("*",$data);
	$booking_id = $book_data[0];
	$booking_no = $book_data[1];
	$booking_type = $book_data[2];
	$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	//$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");

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
//echo $booking_type.'DS';
	if($booking_type == 1)
	{
		
		$sql= "SELECT a.id as aid,c.id as dtls_id, a.body_part_id as body_part_id,a.color_type_id as color_type_id,a.fabric_description,a.gsm_weight,a.width_dia_type,a.construction,a.lib_yarn_count_deter_id,b.id as bid,b.cons_process as cons_process, c.id as id,c.job_no,c.po_break_down_id,c.booking_no as booking_no,c.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id,c.dia_width as dia_width, c.wo_qnty as wo_qnty,c.rate,c.amount as amount,c.gmts_color_id as gmts_color_id, c.fabric_color_id, c.process,c.uom,null as buyer_id 
		from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b,wo_booking_dtls c  
		where a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.fabric_description and b.id=c.pre_cost_fabric_cost_dtls_id  and c.booking_no='$booking_no' and c.wo_qnty>0 and a.status_active=1 and a.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else
	{
		/*$sql ="select a.id,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, 0 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id, 2 as type  from wo_non_ord_aop_booking_mst a where and a.wo_no = '$booking_no'  and  a.status_active=1 and a.is_deleted=0 order by booking_no_prefix_num desc";*/
		/*$sql = "select a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id,
		null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate,
		35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and  a.status_active=1 and a.is_deleted=0  and a.wo_no = '$booking_no'";*/


		$sql ="SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate, 35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom,b.fabric_description as fab_des_id, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and b.fabric_description = c.id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and a.wo_no = '$booking_no'  and b.wo_qty>0
		union all
		SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id,b.gmts_color as gmts_color_id,  a.prefix_num as booking_no_prefix_num, a.booking_no, c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty,  b.gsm as gsm_weight,b.dia as dia_width,b.rate, b.process_id as process, 0 as item_category, b.fabric_source, a.supplier_id,b.uom,b.fab_des_id, 2 as type from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c where a.id = b.mst_id and a.fab_booking_id =  c.id  and b.status_active = 1 and b.is_deleted = 0 and a.booking_no = '$booking_no' and b.wo_qty>0";

 	}
	//echo $sql; 
	//=========================================================
 $results = sql_select($sql);
 $fab_des_id_conds="";$po_id_conds="";
 foreach($results as $row)
 {
 	$fab_des_id_conds.=$row[csf('fab_des_id')].',';
 	$po_id_conds.=$row[csf('po_break_down_id')].',';
	$po_arr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
	$booking_process_arr[$row[csf("po_break_down_id")]]=$row[csf("process")];
	$fab_source_arr[$row[csf("po_break_down_id")]]=$row[csf("fabric_source")];
	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row[csf("lib_yarn_count_deter_id")]];
 	}
 	else
 	{

 		if($row[csf('fabric_source')]==1)
 		{
 			$fabric_des=  $fabric_description[$row[csf('fab_des_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row[csf('fab_des_id')]]; 
 		}
 		//$buyer_id_non_ord=$row[csf('buyer_id')];
 		//$buyer_name_non_ord=$buyer_name_array[$row[csf('buyer_id')]];

 	}
	 
	  $booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$fabric_des]=$row[csf("color_type_id")];
 }
 $fab_des_id_cond=chop($fab_des_id_conds,',');
 $po_id_cond=chop($po_id_conds,',');
 if($fab_des_id_cond!=""){$fab_des_id_qry_cond="and a.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond="";}
 if($po_id_cond!=""){$po_id_qry_cond="and b.id in($po_id_cond)";}else{$po_id_qry_cond="";}

	//die; 
	$sql_product=sql_select("select id,product_name_details,detarmination_id from product_details_master where item_category_id=13");
	foreach($sql_product as $row)
 	{
		$lib_product[$row[csf('id')]]=$row[csf('product_name_details')];
		$lib_product_detemin[$row[csf('id')]]=$row[csf('detarmination_id')];
	}
 if($booking_type!=1 && $fab_des_id_cond!="")
 {
 	$sql_1=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id");

	//echo "select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id";
 	foreach($sql_1 as $row)
 	{
 		$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
 	}
 	//$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
 	if($fab_des_id_cond!=""){$fab_des_id_qry_cond_2="and c.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond_2="";}

 	$sql_2=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond_2 order by c.id");
 	foreach($sql_2 as $row)
 	{
 		$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
 	}	
 }

  	//=========================================================
 if($booking_type==1 && $po_id_cond!="")
 {
 	$data_array_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $po_id_qry_cond");


 	$po_details_array=array();
 	foreach($data_array_sql as $row)
 	{
 		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
 		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
 		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
 		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
 		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
 		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
 	}
 }
	//=========================================================


 $previousIssueArrNew = array();
 $previousIssueRes=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id,a.order_id,a.body_part_id,a.color_id,a.febric_description_id as deter_id from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
 	where a.mst_id = b.id and a.booking_no ='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91");
 foreach($previousIssueRes as $row2)
 {
 	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row2[csf("deter_id")]];
 	}
 	else
 	{
 		 $fab_source=$fab_source_arr[$row2[csf("order_id")]];
		if($fab_source==1)
 		{
 			$fabric_des=  $fabric_description[$row2[csf('deter_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row2[csf('deter_id')]]; 
 		}
 	}
	
	$previousIssueArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];
	$previousIssueArrNew2[$row2[csf('order_id')]][$row2[csf('body_part_id')]][$row2[csf('color_id')]][$fabric_des]+=$row2[csf('batch_issue_qty')];

 }
//print_r($previousIssueArrNew2);
  $po_arr_ids=implode(',', $po_arr);
 $batchDataArrNew = array();
 $sql_po_batch = "select a.id,b.batch_no,b.color_id,b.id as batch_id,b.booking_no_id,b.booking_no,b.booking_without_order,b.extention_no,a.po_id,a.prod_id,a.item_description,a.body_part_id, a.width_dia_type,(a.batch_qnty) as batch_qnty from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id and a.po_id in($po_arr_ids) ";
$result_po_batch=sql_select($sql_po_batch);
	foreach ($result_po_batch as $row)
	{
		$fab_des=explode(",",$row[csf('item_description')]);
		$fab_dia=$fab_des[3];
		$fab_gsm=$fab_des[2];
		//$fab_cons=$fab_des[0];
		$fab_cons_commp=$fab_des[0].', '.$fab_des[1];
		$process_id=$booking_process_arr[$row[csf("po_id")]];
		$detemin_id=$lib_product_detemin[$row[csf('prod_id')]];
		$color_type_id=$booking_color_type_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]][$fab_cons_commp];
		$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_no']= $row[csf('batch_no')];
		$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_id']= $row[csf('batch_id')];
		//$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_id']= $row[csf('id')];
		
		//echo $color_type_id.'DD';
		if($color_type_id==5 || $color_type_id==7)//Aop/Aop Stripe
		{
		 $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$fab_cons_commp]['batch_qnty'] += $row[csf('batch_qnty')];
		//  $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$fab_cons_commp]['batch_id'] = $row[csf('batch_id')];
		
		}
		
		
	}
	//print_r($po_batch_data_arr);
 $result = sql_select($sql); $fabric_des="";

 foreach($result as $row)
 {

 	$balance = number_format($row[csf("wo_qnty")],4,'.','') - $previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];
	$balance_qty = $row[csf("wo_qnty")]-$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];


 	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row[csf("lib_yarn_count_deter_id")]];
 	}
 	else
 	{

 		if($row[csf('fabric_source')]==1)
 		{
 			$fabric_des=  $fabric_description[$row[csf('fab_des_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row[csf('fab_des_id')]]; 
 		}
 		$buyer_id_non_ord=$row[csf('buyer_id')];
 		$buyer_name_non_ord=$buyer_name_array[$row[csf('buyer_id')]];

 	}
	$color_typeid=0;
	$color_typeid=$booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$fabric_des];
	
	if($row[csf("body_part_id")]=='') $row[csf("body_part_id")]=0;else $row[csf("body_part_id")]=$row[csf("body_part_id")];
	$batch_qnty=0;$color_previousIssue_qty=0;
	$batch_qnty=$po_batch_data_qty_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$fabric_des]['batch_qnty'];
	if($batch_qnty==0 || $batch_qnty=="") $batch_qnty=0;else $batch_qnty=$batch_qnty;
	$color_previousIssue_qty=$previousIssueArrNew2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$fabric_des];
	$priv_current_qty=$color_previousIssue_qty+$balance_qty;
	//echo $priv_current_qty.'='.$batch_qnty.'='.$color_typeid.'<br>';
	$batch_id=$po_batch_data_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['batch_id'];
	$batch_no=$po_batch_data_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['batch_no'];
	//	echo $batch_no.'GG'.$batch_id;
	
	if($booking_type == 1)
	{
		if ($row[csf("gmts_color_id")]=="") 
		{
			$fabric_color=$row[csf("fabric_color_id")];
		}
		else
		{
			$fabric_color=$row[csf("gmts_color_id")];
		}
	}
	else
	{
		$fabric_color=$row[csf("gmts_color_id")];
	}	
	
		//$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$composition_arr[$row[csf("lib_yarn_count_deter_id")]]."**".$color_arr[$row[csf("gmts_color_id")]]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($row[csf("wo_qnty")],4,'.','')."**".$row[csf("gmts_color_id")]."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."#";
	//	echo $priv_current_qty.'='.$batch_qnty.'<br>';
		/*if($priv_current_qty<=$batch_qnty)
		{
			//echo ",B";
			$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$fabric_des."**".$color_arr[$fabric_color]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($row[csf("wo_qnty")],4,'.','')."**".$fabric_color."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_name']."**".$po_details_array[$row[csf("po_break_down_id")]]['job_no']."**".$po_details_array[$row[csf("po_break_down_id")]]['po_number']."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_id']."**".$buyer_id_non_ord."**".$buyer_name_non_ord."**".$batch_no."#";
		}
		else if(!$batch_qnty)
		{*/
			//echo ",B";
			$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$fabric_des."**".$color_arr[$fabric_color]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($row[csf("wo_qnty")],4,'.','')."**".$fabric_color."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_name']."**".$po_details_array[$row[csf("po_break_down_id")]]['job_no']."**".$po_details_array[$row[csf("po_break_down_id")]]['po_number']."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_id']."**".$buyer_id_non_ord."**".$buyer_name_non_ord."**".$batch_no."**".$priv_current_qty."**".$batch_qnty."**".$batch_id."#";
		//}
		//else $wo_data="";
 }
 

 
 echo substr($wo_data,0,-1);

}
if($action == "populate_wo_data2")
{
	$book_data = explode("*",$data);
	$booking_id = $book_data[0];
	$booking_no = $book_data[1];
	$booking_type = $book_data[2];
	$book_dtls = $book_data[3];
	//$batch_dtls_id = $book_data[4];
	$booking_dtls_data=explode(",",$book_dtls);
	//print_r($batch_dtls_data);
	//$batch_dtls_data
	$batch_ids="";$booking_dtls_ids="";
	foreach($booking_dtls_data as $dtl_val)
	{
		$book_val=explode("_",$dtl_val);
		$booking_dtls_id=$book_val[0];
		$batcht_id=$book_val[1];
		
		
		if($booking_dtls_ids=="") $booking_dtls_ids=$booking_dtls_id; else $booking_dtls_ids.=','.$booking_dtls_id;
		if($batch_ids=="") $batch_ids=$batcht_id; else $batch_ids.=','.$batcht_id;
		//if($prod_ids=="") $prod_ids=$prod_id; else $prod_ids.=','.$prod_id;
		//echo $batchDtls_id.', ';
	}
	//end
	$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	//$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");

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
//echo $booking_type.'DS';
	if($booking_type == 1)
	{
		
		$sql= "SELECT a.id as aid,c.id as dtls_id, a.body_part_id as body_part_id,a.color_type_id as color_type_id,a.fabric_description,a.gsm_weight,a.width_dia_type,a.construction,a.lib_yarn_count_deter_id,b.id as bid,b.cons_process as cons_process, c.id as id,c.job_no,c.po_break_down_id,c.booking_no as booking_no,c.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id,c.dia_width as dia_width, c.wo_qnty as wo_qnty,c.rate,c.amount as amount,c.gmts_color_id as gmts_color_id, c.fabric_color_id, c.process,c.uom,null as buyer_id 
		from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b,wo_booking_dtls c  
		where a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.fabric_description and b.id=c.pre_cost_fabric_cost_dtls_id  and c.booking_no='$booking_no' and c.id in($booking_dtls_ids)  and c.wo_qnty>0 and a.status_active=1 and a.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else
	{
		/*$sql ="select a.id,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, 0 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id, 2 as type  from wo_non_ord_aop_booking_mst a where and a.wo_no = '$booking_no'  and  a.status_active=1 and a.is_deleted=0 order by booking_no_prefix_num desc";*/
		/*$sql = "select a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id,
		null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate,
		35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and  a.status_active=1 and a.is_deleted=0  and a.wo_no = '$booking_no'";*/


		$sql ="SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate, 35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom,b.fabric_description as fab_des_id, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and b.fabric_description = c.id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and a.wo_no = '$booking_no'  and b.wo_qty>0
		union all
		SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id,b.gmts_color as gmts_color_id,  a.prefix_num as booking_no_prefix_num, a.booking_no, c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty,  b.gsm as gsm_weight,b.dia as dia_width,b.rate, b.process_id as process, 0 as item_category, b.fabric_source, a.supplier_id,b.uom,b.fab_des_id, 2 as type from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c where a.id = b.mst_id and a.fab_booking_id =  c.id  and b.status_active = 1 and b.is_deleted = 0 and a.booking_no = '$booking_no' and b.wo_qty>0";

 	}
	//echo $sql; 
	//=========================================================
 $results = sql_select($sql);
 $fab_des_id_conds="";$po_id_conds="";
 foreach($results as $row)
 {
 	$fab_des_id_conds.=$row[csf('fab_des_id')].',';
 	$po_id_conds.=$row[csf('po_break_down_id')].',';
	$po_arr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
	$booking_process_arr[$row[csf("po_break_down_id")]]=$row[csf("process")];
	$fab_source_arr[$row[csf("po_break_down_id")]]=$row[csf("fabric_source")];
	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row[csf("lib_yarn_count_deter_id")]];
		$deter_id=$row[csf("lib_yarn_count_deter_id")];
 	}
 	else
 	{

 		if($row[csf('fabric_source')]==1)
 		{
 			$fabric_des=  $fabric_description[$row[csf('fab_des_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row[csf('fab_des_id')]]; 
 		}
		$deter_id=$row[csf("fab_des_id")];
 		//$buyer_id_non_ord=$row[csf('buyer_id')];
 		//$buyer_name_non_ord=$buyer_name_array[$row[csf('buyer_id')]];

 	}
	 
	  $booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$deter_id]=$row[csf("color_type_id")];
 }
 $fab_des_id_cond=chop($fab_des_id_conds,',');
 $po_id_cond=chop($po_id_conds,',');
 if($fab_des_id_cond!=""){$fab_des_id_qry_cond="and a.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond="";}
 if($po_id_cond!=""){$po_id_qry_cond="and b.id in($po_id_cond)";}else{$po_id_qry_cond="";}

	//die; 
	$sql_product=sql_select("select id,product_name_details,detarmination_id from product_details_master where item_category_id=13");
	foreach($sql_product as $row)
 	{
		$lib_product[$row[csf('id')]]=$row[csf('product_name_details')];
		$lib_product_detemin[$row[csf('id')]]=$row[csf('detarmination_id')];
	}
 if($booking_type!=1 && $fab_des_id_cond!="")
 {
 	$sql_1=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id");

	//echo "select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id";
 	foreach($sql_1 as $row)
 	{
 		$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
 	}
 	//$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
 	if($fab_des_id_cond!=""){$fab_des_id_qry_cond_2="and c.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond_2="";}

 	$sql_2=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond_2 order by c.id");
 	foreach($sql_2 as $row)
 	{
 		$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
 	}	
 }

  	//=========================================================
 if($booking_type==1 && $po_id_cond!="")
 {
 	$data_array_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $po_id_qry_cond");


 	$po_details_array=array();
 	foreach($data_array_sql as $row)
 	{
 		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
 		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
 		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
 		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
 		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
 		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
 	}
 }
	//=========================================================


 $previousIssueArrNew = array();
 $previousIssueRes=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id,a.order_id,a.body_part_id,a.color_id,a.febric_description_id as deter_id from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
 	where a.mst_id = b.id and a.booking_no ='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91");
	
 foreach($previousIssueRes as $row2)
 {
 	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row2[csf("deter_id")]];
		$deter_id=$row2[csf("deter_id")];
 	}
 	else
 	{
 		 $fab_source=$fab_source_arr[$row2[csf("order_id")]];
		if($fab_source==1)
 		{
 			$fabric_des=  $fabric_description[$row2[csf('deter_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row2[csf('deter_id')]]; 
 		}
		$deter_id=$row2[csf("deter_id")];
 	}
	
	$previousIssueArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];
	$previousIssueArrNew2[$row2[csf('order_id')]][$row2[csf('body_part_id')]][$row2[csf('color_id')]][$deter_id]+=$row2[csf('batch_issue_qty')];

 }
 
//print_r($previousIssueArrNew2);
  $po_arr_ids=implode(',', $po_arr);
 $batchDataArrNew = array();
 $sql_po_batch = "select a.id,b.batch_no,b.color_id,b.id as batch_id,b.booking_no_id,b.booking_no,b.booking_without_order,b.extention_no,a.po_id,a.prod_id,a.item_description,a.body_part_id, a.width_dia_type,(a.batch_qnty) as batch_qnty from pro_batch_create_dtls a, pro_batch_create_mst b ,pro_fab_subprocess c where a.mst_id=b.id  and c.batch_id=b.id and c.load_unload_id=2 and a.po_id in($po_arr_ids) and a.status_active=1 and b.status_active=1 and c.status_active=1 order by a.id";
$result_po_batch=sql_select($sql_po_batch);
	foreach ($result_po_batch as $row)
	{
		$fab_des=explode(",",$row[csf('item_description')]);
		$fab_dia=$fab_des[3];
		$fab_gsm=$fab_des[2];
		//$fab_cons=$fab_des[0];
		$fab_cons_commp=$fab_des[0].', '.$fab_des[1];
		$process_id=$booking_process_arr[$row[csf("po_id")]];
		$detemin_id=$lib_product_detemin[$row[csf('prod_id')]];
		$color_type_id=$booking_color_type_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]][$detemin_id];
		//$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_id']= $row[csf('id')];
		$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_no']= $row[csf('batch_no')];
		$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_id']= $row[csf('batch_id')];
		//echo $color_type_id.'DD';
		if($color_type_id==5 || $color_type_id==7)//Aop/Aop Stripe
		{
		 $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$detemin_id]['batch_qnty'] += $row[csf('batch_qnty')];
		//  $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$fab_cons_commp]['batch_id'] = $row[csf('batch_id')];
		}
		
		
	}
	//print_r($po_batch_data_arr);
 $result = sql_select($sql); $fabric_des="";

 foreach($result as $row)
 {

 	//$balance = number_format($row[csf("wo_qnty")],4,'.','') - $previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];
	$balance_qty = $row[csf("wo_qnty")]-$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];


 	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row[csf("lib_yarn_count_deter_id")]];
		$deter_id=$row[csf("lib_yarn_count_deter_id")];
 	}
 	else
 	{

 		if($row[csf('fabric_source')]==1)
 		{
 			$fabric_des=  $fabric_description[$row[csf('fab_des_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row[csf('fab_des_id')]]; 
 		}
		$deter_id=$row[csf("fab_des_id")];
 		$buyer_id_non_ord=$row[csf('buyer_id')];
 		$buyer_name_non_ord=$buyer_name_array[$row[csf('buyer_id')]];

 	}
	$color_typeid=0;
	$color_typeid=$booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$deter_id];
	
	if($row[csf("body_part_id")]=='') $row[csf("body_part_id")]=0;else $row[csf("body_part_id")]=$row[csf("body_part_id")];
	$batch_qnty=0;$color_previousIssue_qty=0;
	$batch_qnty=$po_batch_data_qty_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$deter_id]['batch_qnty'];
	if($batch_qnty==0 || $batch_qnty=="") $batch_qnty=0;else $batch_qnty=$batch_qnty;
	
	$color_previousIssue_qty=$previousIssueArrNew2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$deter_id];
	$priv_current_qty=$color_previousIssue_qty+$balance_qty;
	//echo $priv_current_qty.'='.$batch_qnty.'='.$color_typeid.'<br>';
	$batch_id=$po_batch_data_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['batch_id'];
	$batch_no=$po_batch_data_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['batch_no'];
	//	echo $batch_no.'GG'.$batch_id;
	if($row[csf("process")]==35) //AOP
	{
		$balance = number_format($batch_qnty,4,'.','') - $previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];
		$batch_qnty=$batch_qnty;
	}
	else { //For All
		$balance = number_format($row[csf("wo_qnty")],4,'.','') - $previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];
		$batch_qnty=$row[csf("wo_qnty")];
	}
	
	if($booking_type == 1)
	{
		if ($row[csf("gmts_color_id")]=="") 
		{
			$fabric_color=$row[csf("fabric_color_id")];
		}
		else
		{
			$fabric_color=$row[csf("gmts_color_id")];
		}
	}
	else
	{
		$fabric_color=$row[csf("gmts_color_id")];
	}	
	
		//$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$composition_arr[$row[csf("lib_yarn_count_deter_id")]]."**".$color_arr[$row[csf("gmts_color_id")]]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($row[csf("wo_qnty")],4,'.','')."**".$row[csf("gmts_color_id")]."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."#";
	//	echo $priv_current_qty.'='.$batch_qnty.'<br>';
		/*if($priv_current_qty<=$batch_qnty)
		{
			//echo ",B";
			$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$fabric_des."**".$color_arr[$fabric_color]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($row[csf("wo_qnty")],4,'.','')."**".$fabric_color."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_name']."**".$po_details_array[$row[csf("po_break_down_id")]]['job_no']."**".$po_details_array[$row[csf("po_break_down_id")]]['po_number']."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_id']."**".$buyer_id_non_ord."**".$buyer_name_non_ord."**".$batch_no."#";
		}
		else if(!$batch_qnty)
		{*/
			//echo ",B";
			$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$fabric_des."**".$color_arr[$fabric_color]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($batch_qnty,4,'.','')."**".$fabric_color."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_name']."**".$po_details_array[$row[csf("po_break_down_id")]]['job_no']."**".$po_details_array[$row[csf("po_break_down_id")]]['po_number']."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_id']."**".$buyer_id_non_ord."**".$buyer_name_non_ord."**".$batch_no."**".$priv_current_qty."**".$batch_qnty."**".$batch_id."#";
		//}
		//else $wo_data="";
 }
 

 
 echo substr($wo_data,0,-1);

}

if($action == "populate_wo_data2_old")
{
	$book_data = explode("*",$data);
	$booking_id = $book_data[0];
	$booking_no = $book_data[1];
	$booking_type = $book_data[2];
	$book_dtls = $book_data[3];
	//$batch_dtls_id = $book_data[4];
	$booking_dtls_data=explode(",",$book_dtls);
	//print_r($batch_dtls_data);
	//$batch_dtls_data
	$batch_ids="";$booking_dtls_ids="";
	foreach($booking_dtls_data as $dtl_val)
	{
		$book_val=explode("_",$dtl_val);
		$booking_dtls_id=$book_val[0];
		$batcht_id=$book_val[1];
		
		
		if($booking_dtls_ids=="") $booking_dtls_ids=$booking_dtls_id; else $booking_dtls_ids.=','.$booking_dtls_id;
		if($batch_ids=="") $batch_ids=$batcht_id; else $batch_ids.=','.$batcht_id;
		//if($prod_ids=="") $prod_ids=$prod_id; else $prod_ids.=','.$prod_id;
		//echo $batchDtls_id.', ';
	}
	//end
	$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	//$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");

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
//echo $booking_type.'DS';
	if($booking_type == 1)
	{
		
		$sql= "SELECT a.id as aid,c.id as dtls_id, a.body_part_id as body_part_id,a.color_type_id as color_type_id,a.fabric_description,a.gsm_weight,a.width_dia_type,a.construction,a.lib_yarn_count_deter_id,b.id as bid,b.cons_process as cons_process, c.id as id,c.job_no,c.po_break_down_id,c.booking_no as booking_no,c.pre_cost_fabric_cost_dtls_id as pre_cost_fabric_cost_dtls_id,c.dia_width as dia_width, c.wo_qnty as wo_qnty,c.rate,c.amount as amount,c.gmts_color_id as gmts_color_id, c.fabric_color_id, c.process,c.uom,null as buyer_id 
		from  wo_pre_cost_fabric_cost_dtls a,wo_pre_cost_fab_conv_cost_dtls b,wo_booking_dtls c  
		where a.job_no=b.job_no and a.job_no=c.job_no and a.id=b.fabric_description and b.id=c.pre_cost_fabric_cost_dtls_id  and c.booking_no='$booking_no' and c.id in($booking_dtls_ids)  and c.wo_qnty>0 and a.status_active=1 and a.is_deleted=0 
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	else
	{
		/*$sql ="select a.id,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no, a.booking_date, a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id, 0 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id, 2 as type  from wo_non_ord_aop_booking_mst a where and a.wo_no = '$booking_no'  and  a.status_active=1 and a.is_deleted=0 order by booking_no_prefix_num desc";*/
		/*$sql = "select a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id,
		null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate,
		35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and  a.status_active=1 and a.is_deleted=0  and a.wo_no = '$booking_no'";*/


		$sql ="SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id, b.printing_color_id as gmts_color_id ,a.wo_no_prefix_num as booking_no_prefix_num, a.wo_no as booking_no,c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty as wo_qnty,b.aop_gsm as gsm_weight,b.aop_dia as dia_width, b.rate, 35 as process, 0 as item_category, a.aop_source as fabric_source, a.supplier_id,b.uom,b.fabric_description as fab_des_id, 2 as type 
		from wo_non_ord_aop_booking_mst a,wo_non_ord_aop_booking_dtls b ,wo_non_ord_samp_booking_dtls c
		where a.id = b.wo_id and b.fab_booking_no = c.booking_no and b.fabric_description = c.id and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 and a.wo_no = '$booking_no'  and b.wo_qty>0
		union all
		SELECT a.id,b.id as dtls_id,c.body_part as body_part_id,c.color_type_id,b.gmts_color as gmts_color_id,  a.prefix_num as booking_no_prefix_num, a.booking_no, c.lib_yarn_count_deter_id,  a.company_id, a.buyer_id, null as job_no_prefix_num,null as style_ref_no, null as job_no, null as po_break_down_id,b.wo_qty,  b.gsm as gsm_weight,b.dia as dia_width,b.rate, b.process_id as process, 0 as item_category, b.fabric_source, a.supplier_id,b.uom,b.fab_des_id, 2 as type from wo_non_ord_knitdye_booking_mst a,  wo_non_ord_knitdye_booking_dtl b, wo_non_ord_samp_booking_dtls c where a.id = b.mst_id and a.fab_booking_id =  c.id  and b.status_active = 1 and b.is_deleted = 0 and a.booking_no = '$booking_no' and b.wo_qty>0";

 	}
	//echo $sql; 
	//=========================================================
 $results = sql_select($sql);
 $fab_des_id_conds="";$po_id_conds="";
 foreach($results as $row)
 {
 	$fab_des_id_conds.=$row[csf('fab_des_id')].',';
 	$po_id_conds.=$row[csf('po_break_down_id')].',';
	$po_arr[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
	$booking_process_arr[$row[csf("po_break_down_id")]]=$row[csf("process")];
	$fab_source_arr[$row[csf("po_break_down_id")]]=$row[csf("fabric_source")];
	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row[csf("lib_yarn_count_deter_id")]];
 	}
 	else
 	{

 		if($row[csf('fabric_source')]==1)
 		{
 			$fabric_des=  $fabric_description[$row[csf('fab_des_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row[csf('fab_des_id')]]; 
 		}
 		//$buyer_id_non_ord=$row[csf('buyer_id')];
 		//$buyer_name_non_ord=$buyer_name_array[$row[csf('buyer_id')]];

 	}
	 
	  $booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$fabric_des]=$row[csf("color_type_id")];
 }
 $fab_des_id_cond=chop($fab_des_id_conds,',');
 $po_id_cond=chop($po_id_conds,',');
 if($fab_des_id_cond!=""){$fab_des_id_qry_cond="and a.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond="";}
 if($po_id_cond!=""){$po_id_qry_cond="and b.id in($po_id_cond)";}else{$po_id_qry_cond="";}

	//die; 
	$sql_product=sql_select("select id,product_name_details,detarmination_id from product_details_master where item_category_id=13");
	foreach($sql_product as $row)
 	{
		$lib_product[$row[csf('id')]]=$row[csf('product_name_details')];
		$lib_product_detemin[$row[csf('id')]]=$row[csf('detarmination_id')];
	}
 if($booking_type!=1 && $fab_des_id_cond!="")
 {
 	$sql_1=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id");

	//echo "select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id";
 	foreach($sql_1 as $row)
 	{
 		$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
 	}
 	//$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
 	if($fab_des_id_cond!=""){$fab_des_id_qry_cond_2="and c.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond_2="";}

 	$sql_2=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond_2 order by c.id");
 	foreach($sql_2 as $row)
 	{
 		$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
 	}	
 }

  	//=========================================================
 if($booking_type==1 && $po_id_cond!="")
 {
 	$data_array_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $po_id_qry_cond");


 	$po_details_array=array();
 	foreach($data_array_sql as $row)
 	{
 		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
 		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
 		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
 		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
 		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
 		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
 	}
 }
	//=========================================================


 $previousIssueArrNew = array();
 $previousIssueRes=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id,a.order_id,a.body_part_id,a.color_id,a.febric_description_id as deter_id from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
 	where a.mst_id = b.id and a.booking_no ='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91");
	
 foreach($previousIssueRes as $row2)
 {
 	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row2[csf("deter_id")]];
 	}
 	else
 	{
 		 $fab_source=$fab_source_arr[$row2[csf("order_id")]];
		if($fab_source==1)
 		{
 			$fabric_des=  $fabric_description[$row2[csf('deter_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row2[csf('deter_id')]]; 
 		}
 	}
	
	$previousIssueArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];
	$previousIssueArrNew2[$row2[csf('order_id')]][$row2[csf('body_part_id')]][$row2[csf('color_id')]][$fabric_des]+=$row2[csf('batch_issue_qty')];

 }
//print_r($previousIssueArrNew2);
  $po_arr_ids=implode(',', $po_arr);
 $batchDataArrNew = array();
 $sql_po_batch = "select a.id,b.batch_no,b.color_id,b.id as batch_id,b.booking_no_id,b.booking_no,b.booking_without_order,b.extention_no,a.po_id,a.prod_id,a.item_description,a.body_part_id, a.width_dia_type,(a.batch_qnty) as batch_qnty from pro_batch_create_dtls a, pro_batch_create_mst b ,pro_fab_subprocess c where a.mst_id=b.id  and c.batch_id=b.id and c.load_unload_id=2 and a.po_id in($po_arr_ids) and a.status_active=1 and b.status_active=1 and c.status_active=1 order by a.id";
$result_po_batch=sql_select($sql_po_batch);
	foreach ($result_po_batch as $row)
	{
		$fab_des=explode(",",$row[csf('item_description')]);
		$fab_dia=$fab_des[3];
		$fab_gsm=$fab_des[2];
		//$fab_cons=$fab_des[0];
		$fab_cons_commp=$fab_des[0].', '.$fab_des[1];
		$process_id=$booking_process_arr[$row[csf("po_id")]];
		$detemin_id=$lib_product_detemin[$row[csf('prod_id')]];
		$color_type_id=$booking_color_type_arr[$row[csf("po_id")]][$row[csf("body_part_id")]][$row[csf("color_id")]][$fab_cons_commp];
		//$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_id']= $row[csf('id')];
		$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_no']= $row[csf('batch_no')];
		$po_batch_data_arr[$row[csf('po_id')]][$row[csf('color_id')]]['batch_id']= $row[csf('batch_id')];
		//echo $color_type_id.'DD';
		if($color_type_id==5 || $color_type_id==7)//Aop/Aop Stripe
		{
		 $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$fab_cons_commp]['batch_qnty'] += $row[csf('batch_qnty')];
		//  $po_batch_data_qty_arr[$row[csf('po_id')]][$row[csf('body_part_id')]][$row[csf('color_id')]][$fab_cons_commp]['batch_id'] = $row[csf('batch_id')];
		}
		
		
	}
	//print_r($po_batch_data_arr);
 $result = sql_select($sql); $fabric_des="";

 foreach($result as $row)
 {

 	//$balance = number_format($row[csf("wo_qnty")],4,'.','') - $previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];
	$balance_qty = $row[csf("wo_qnty")]-$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];


 	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row[csf("lib_yarn_count_deter_id")]];
 	}
 	else
 	{

 		if($row[csf('fabric_source')]==1)
 		{
 			$fabric_des=  $fabric_description[$row[csf('fab_des_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row[csf('fab_des_id')]]; 
 		}
 		$buyer_id_non_ord=$row[csf('buyer_id')];
 		$buyer_name_non_ord=$buyer_name_array[$row[csf('buyer_id')]];

 	}
	$color_typeid=0;
	$color_typeid=$booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$fabric_des];
	
	if($row[csf("body_part_id")]=='') $row[csf("body_part_id")]=0;else $row[csf("body_part_id")]=$row[csf("body_part_id")];
	$batch_qnty=0;$color_previousIssue_qty=0;
	$batch_qnty=$po_batch_data_qty_arr[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$fabric_des]['batch_qnty'];
	if($batch_qnty==0 || $batch_qnty=="") $batch_qnty=0;else $batch_qnty=$batch_qnty;
	
	$color_previousIssue_qty=$previousIssueArrNew2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$fabric_des];
	$priv_current_qty=$color_previousIssue_qty+$balance_qty;
	//echo $priv_current_qty.'='.$batch_qnty.'='.$color_typeid.'<br>';
	$batch_id=$po_batch_data_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['batch_id'];
	$batch_no=$po_batch_data_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['batch_no'];
	//	echo $batch_no.'GG'.$batch_id;
	if($row[csf("process")]==35) //AOP
	{
		$balance = number_format($batch_qnty,4,'.','') - $previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];
		$batch_qnty=$batch_qnty;
	}
	else { //For All
		$balance = number_format($row[csf("wo_qnty")],4,'.','') - $previousIssueArrNew[$booking_no][$row[csf('dtls_id')]];
		$batch_qnty=$row[csf("wo_qnty")];
	}
	
	if($booking_type == 1)
	{
		if ($row[csf("gmts_color_id")]=="") 
		{
			$fabric_color=$row[csf("fabric_color_id")];
		}
		else
		{
			$fabric_color=$row[csf("gmts_color_id")];
		}
	}
	else
	{
		$fabric_color=$row[csf("gmts_color_id")];
	}	
	
		//$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$composition_arr[$row[csf("lib_yarn_count_deter_id")]]."**".$color_arr[$row[csf("gmts_color_id")]]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($row[csf("wo_qnty")],4,'.','')."**".$row[csf("gmts_color_id")]."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."#";
	//	echo $priv_current_qty.'='.$batch_qnty.'<br>';
		/*if($priv_current_qty<=$batch_qnty)
		{
			//echo ",B";
			$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$fabric_des."**".$color_arr[$fabric_color]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($row[csf("wo_qnty")],4,'.','')."**".$fabric_color."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_name']."**".$po_details_array[$row[csf("po_break_down_id")]]['job_no']."**".$po_details_array[$row[csf("po_break_down_id")]]['po_number']."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_id']."**".$buyer_id_non_ord."**".$buyer_name_non_ord."**".$batch_no."#";
		}
		else if(!$batch_qnty)
		{*/
			//echo ",B";
			$wo_data .= $row[csf("width_dia_type")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("gsm_weight")]."**".$row[csf("dia_width")]."**".$fabric_des."**".$color_arr[$fabric_color]."**".$row[csf("job_no")]."**".$row[csf("po_break_down_id")]."**".number_format($batch_qnty,4,'.','')."**".$fabric_color."**".$row[csf("body_part_id")]."**".$row[csf("lib_yarn_count_deter_id")]."**".$row[csf("rate")]."**".$row[csf("process")]."**".$fabric_typee[$row[csf("width_dia_type")]]."**".$row[csf("dtls_id")]."**".$balance."**".$previousIssueArrNew[$booking_no][$row[csf('dtls_id')]]."**".$row[csf('uom')]."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_name']."**".$po_details_array[$row[csf("po_break_down_id")]]['job_no']."**".$po_details_array[$row[csf("po_break_down_id")]]['po_number']."**".$po_details_array[$row[csf("po_break_down_id")]]['buyer_id']."**".$buyer_id_non_ord."**".$buyer_name_non_ord."**".$batch_no."**".$priv_current_qty."**".$batch_qnty."**".$batch_id."#";
		//}
		//else $wo_data="";
 }
 

 
 echo substr($wo_data,0,-1);

}

if($action == "populate_wo_data2_2")
{
	$book_data = explode("*",$data);
	$booking_id = $book_data[0];
	$booking_no = $book_data[1];
	$booking_type = $book_data[2];
	$batch_dtls = $book_data[3];
	//$batch_dtls_id = $book_data[4];
	$batch_dtls_data=explode(",",$batch_dtls);
	//print_r($batch_dtls_data);
	//$batch_dtls_data
	$batch_ids="";$bpart_ids="";$prod_ids="";$batch_dtls_ids="";
	foreach($batch_dtls_data as $dtl_val)
	{
		$batch_val=explode("_",$dtl_val);
		$batch_id=$batch_val[0];
		$bpart_id=$batch_val[1];
		$prod_id=$batch_val[2];
		$batchDtls_id=$batch_val[3];
		
		if($batch_ids=="") $batch_ids=$batch_id; else $batch_ids.=','.$batch_id;
		if($bpart_ids=="") $bpart_ids=$bpart_id; else $bpart_ids.=','.$bpart_id;
		if($prod_ids=="") $prod_ids=$prod_id; else $prod_ids.=','.$prod_id;
		//echo $batchDtls_id.', ';
		
		
		$batch_dtls_arr=array_unique(explode("!!",$batchDtls_id));
		foreach($batch_dtls_arr as $dtl_id)
		{
			if($batch_dtls_ids=="") $batch_dtls_ids=$dtl_id; else $batch_dtls_ids.=','.$dtl_id;
		}
		
	}
	//echo $batch_dtls_ids.'x';
	//die;
	$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	//$color_arr=return_library_array("select id,color_name from  lib_color where status_active=1 and is_deleted=0 order by color_name","id","color_name");

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
//echo $booking_type.'DS';
	$sql_book="select a.po_break_down_id,a.wo_qnty,a.gmts_color_id from wo_booking_dtls a where  a.booking_no='$booking_no'  and a.wo_qnty>0 and a.status_active=1 and a.is_deleted=0";
		 $results_book = sql_select($sql_book);
		 $po_id_conds="";
	
		 foreach($results_book as $row)
		 {
				$po_id_conds.=$row[csf('po_break_down_id')].',';
				$wo_qty_arr[$row[csf('po_break_down_id')]][$row[csf('gmts_color_id')]]['wo_qnty']=$row[csf('wo_qnty')];
		 }
		 $po_id=rtrim($po_id_conds,',');
		 $po_ids=implode(",",array_unique(explode(",", $po_id)));//load_unload_id pro_fab_subprocess
		 $prod_ids=implode(",",array_unique(explode(",", $prod_ids)));
		 $batch_ids=implode(",",array_unique(explode(",", $batch_ids)));
		 $bpart_ids=implode(",",array_unique(explode(",", $bpart_ids)));
		 $batch_dtls_ids=implode(",",array_unique(explode(",", $batch_dtls_ids)));
			
		 $sql_batch="select a.batch_no,a.id as batch_id,a.color_id,a.process_id,b.id as dtls_id,b.item_description,b.body_part_id,b.prod_id,b.po_id,b.width_dia_type,b.batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.id in($batch_ids) and b.prod_id in($prod_ids) and b.body_part_id in($bpart_ids) and b.po_id in($po_ids) and b.id in($batch_dtls_ids) and b.batch_qnty>0 and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id";
		 $results_batch = sql_select($sql_batch);
		$batch_wise_arr=array();
		 foreach($results_batch as $row)
		 {
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['batch_no']=$row[csf('batch_no')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['item_desc']=$row[csf('item_description')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['process_id']=$row[csf('process_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['prod_id']=$row[csf('prod_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['width_dia_type']=$row[csf('width_dia_type')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['batch_qnty']+=$row[csf('batch_qnty')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['color_id']=$row[csf('color_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['po_id']=$row[csf('po_id')];
			$batch_wise_arr[$row[csf('batch_id')]][$row[csf('body_part_id')]][$row[csf('prod_id')]]['dtls_id']=$row[csf('dtls_id')];
		 }
	//echo $sql; 
	//=========================================================
 
 $fab_des_id_cond=chop($fab_des_id_conds,',');
 $po_id_cond=chop($po_id_conds,',');
 if($fab_des_id_cond!=""){$fab_des_id_qry_cond="and a.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond="";}
 if($po_id_cond!=""){$po_id_qry_cond="and b.id in($po_id_cond)";}else{$po_id_qry_cond="";}

	//die; 
	$sql_product=sql_select("select id,product_name_details,detarmination_id from product_details_master where item_category_id=13");
	foreach($sql_product as $row)
 	{
		$lib_product[$row[csf('id')]]=$row[csf('product_name_details')];
		$lib_product_detemin[$row[csf('id')]]=$row[csf('detarmination_id')];
	}
 if($booking_type!=1 && $fab_des_id_cond!="")
 {
 	$sql_1=sql_select("select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id");

	//echo "select a.id,a.body_part,a.fabric_description,fabric_color,a.gmts_color,a.gsm_weight,a.dia_width from wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls a where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond order by a.id";
 	foreach($sql_1 as $row)
 	{
 		$fabric_description[$row[csf('id')]]=$body_part[$row[csf('body_part')]].",".$row[csf('fabric_description')].",".$row[csf('gsm_weight')].",".$row[csf('dia_width')];
 	}
 	//$lib_product= return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
 	if($fab_des_id_cond!=""){$fab_des_id_qry_cond_2="and c.id in($fab_des_id_cond)";}else{$fab_des_id_qry_cond_2="";}

 	$sql_2=sql_select("select c.id,c.from_prod_id from inv_item_transfer_mst a,inv_transaction b,inv_item_transfer_dtls c where a.id= b.mst_id and a.id=c.mst_id and a.transfer_criteria=6 and c.item_category=13  and a.status_active=1 and a.is_deleted=0 $fab_des_id_qry_cond_2 order by c.id");
 	foreach($sql_2 as $row)
 	{
 		$fabric_description2[$row[csf('id')]]=$lib_product[$row[csf('from_prod_id')]];	
 	}	
 }

  	//=========================================================
 if($booking_type==1 && $po_id_cond!="")
 {
 	$data_array_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $po_id_qry_cond");
	//echo "SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active = 1 and a.is_deleted = 0 and b.status_active=1 and b.is_deleted=0 $po_id_qry_cond".$booking_type;


 	$po_details_array=array();
 	foreach($data_array_sql as $row)
 	{
 		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
 		$po_details_array[$row[csf("po_id")]]['buyer_name']=$buyer_name_array[$row[csf("buyer_name")]];
 		$po_details_array[$row[csf("po_id")]]['buyer_id']=$row[csf("buyer_name")];
 		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
 		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
 		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
 	}
 }
	//=========================================================


 $previousIssueArrNew = array();
 $previousIssueRes=sql_select("select a.batch_issue_qty,a.booking_no, a.booking_dtls_id,a.id,a.order_id,a.body_part_id,a.color_id,a.febric_description_id as deter_id from pro_grey_batch_dtls a, inv_receive_mas_batchroll b  
 	where a.mst_id = b.id and a.booking_no ='$booking_no' and a.status_active=1 and a.is_deleted=0 and b.entry_form = 91");
 foreach($previousIssueRes as $row2)
 {
 	if($booking_type == 1)
 	{
 		$fabric_des = $composition_arr[$row2[csf("deter_id")]];
 	}
 	else
 	{
 		 $fab_source=$fab_source_arr[$row2[csf("order_id")]];
		if($fab_source==1)
 		{
 			$fabric_des=  $fabric_description[$row2[csf('deter_id')]]; 
 		}
 		else
 		{
 			$fabric_des =  $fabric_description2[$row2[csf('deter_id')]]; 
 		}
 	}
	
	$previousIssueArrNew[$row2[csf('booking_no')]][$row2[csf('booking_dtls_id')]]+=$row2[csf('batch_issue_qty')];
	$previousIssueArrNew2[$row2[csf('order_id')]][$row2[csf('body_part_id')]][$row2[csf('color_id')]][$fabric_des]+=$row2[csf('batch_issue_qty')];

 }
//print_r($previousIssueArrNew2);
  
$m=1;$wo_data="";
 foreach($batch_wise_arr as $batch_id=>$batchData)
 {
	foreach($batchData as $bpart_id=>$bpartData)
    {
	 foreach($bpartData as $prod_id=>$row)
     {
	$row[("wo_qnty")]=$wo_qty_arr[$row[('po_id')]][$row[('color_id')]]['wo_qnty'];
 	$balance = number_format($row[("wo_qnty")],4,'.','') - $previousIssueArrNew[$booking_no][$row[('dtls_id')]];
	$balance_qty = $row[("wo_qnty")]-$previousIssueArrNew[$booking_no][$row[('dtls_id')]];

	$item_desc=explode(",",$row[("item_desc")]);
	$const_comp=$item_desc[0].','.$item_desc[1];//
	$row[("gsm_weight")]=$item_desc[2];
	$row[("dia_width")]=$item_desc[3];
	$fabric_color=$row[("color_id")];
	$fabric_des=$const_comp;
	$batch_no=$row[("batch_no")];
	$batch_qnty=$row[("batch_qnty")];
	$row[("lib_yarn_count_deter_id")]=$lib_product_detemin[$prod_id];
	
	//$process_id=explode(",",$row[("process_id")]);
	
	
	
 	if($booking_type == 1)
 	{
		$buyer_id_non_ord=$row[('buyer_id')];
 		$buyer_name_non_ord=$buyer_name_array[$row[('buyer_id')]];
	}
	else
	{
		$buyer_id_non_ord=$row[('buyer_id')];
 		$buyer_name_non_ord=$buyer_name_array[$row[('buyer_id')]];
	}
	$row[("process")]=35;
	
	//$color_typeid=0;
	//$color_typeid=$booking_color_type_arr[$row[csf("po_break_down_id")]][$row[csf("body_part_id")]][$row[csf("gmts_color_id")]][$fabric_des];
	
	
	//$color_previousIssue_qty=$previousIssueArrNew2[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('gmts_color_id')]][$fabric_des];
	//$priv_current_qty=$color_previousIssue_qty+$balance_qty;
	//echo $priv_current_qty.'='.$batch_qnty.'='.$color_typeid.'<br>';
	//$batch_id=$po_batch_data_arr[$row[('po_id')]][$row[('color_id')]]['batch_id'];
	//$batch_no=$po_batch_data_arr[$row[('po_id')]][$row[('color_id')]]['batch_no'];
	//	echo $batch_no.'GG'.$batch_id;
	
	
	
	$wo_data .= $row[("width_dia_type")]."**".$body_part[$bpart_id]."**".$row[("gsm_weight")]."**".$row[("dia_width")]."**".$fabric_des."**".$color_arr[$fabric_color]."**".$row[("job_no")]."**".$row[("po_id")]."**".number_format($row[("wo_qnty")],4,'.','')."**".$fabric_color."**".$bpart_id."**".$row[("lib_yarn_count_deter_id")]."**".$row[("rate")]."**".$row[("process")]."**".$fabric_typee[$row[("width_dia_type")]]."**".$row[("dtls_id")]."**".$batch_qnty."**".$previousIssueArrNew[$booking_no][$row[('dtls_id')]]."**".$row[('uom')]."**".$po_details_array[$row[("po_id")]]['buyer_name']."**".$po_details_array[$row[("po_id")]]['job_no']."**".$po_details_array[$row[("po_id")]]['po_number']."**".$po_details_array[$row[("po_id")]]['buyer_id']."**".$buyer_id_non_ord."**".$buyer_name_non_ord."**".$batch_no."**".$priv_current_qty."**".$batch_qnty."**".$batch_id."#";
	
	//$m++;
		
 	}
 }
}
 

 
 echo substr($wo_data,0,-1);

}
if($action=="issue_multy_number_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length; 
			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var attrData=$('#tr_' +i).attr('onclick');
				var splitArr = attrData.split("'");
				js_set_value( splitArr[1] );
			}
		}
		
		
		
		var selected_id=Array();
		var selected_name=Array();
		
		function js_set_value(mrr)
		{
			var splitArr = mrr.split("_");
 		$("#hidden_return_number").val(splitArr[0]); // mrr number
 		$("#hidden_return_id").val(splitArr[2]);

 		toggle( document.getElementById( 'tr_' + splitArr[0] ), '#FFFFCC' );

 		if( jQuery.inArray(splitArr[3], selected_id ) == -1 ) {
 			selected_id.push( splitArr[3]);
 			selected_name.push(splitArr[1]);

 		}
 		else {
 			for( var i = 0; i < selected_id.length; i++ ) {
 				if( selected_id[i] == splitArr[3]) break;
 			}
 			selected_id.splice( i, 1 );
 			selected_name.splice( i, 1 );
 		}

 		var id = ''; var name = '';
 		for( var i = 0; i < selected_id.length; i++ ) {
 			id += selected_id[i] + ',';
 			name += selected_name[i] + ',';
 		}

 		id = id.substr( 0, id.length - 1 );
 		name = name.substr( 0, name.length - 1 );

 		$('#hidden_return_id').val(id);
 		$('#hidden_return_number').val(name);




 	}
 </script>

</head>

<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="850" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>  
						<th width="120" class="must_entry_caption">Service Company</th>              	 
						<th width="180">Search By</th>
						<th width="250" align="center" id="search_by_td_up">Subcon Issue Number</th>
						<th width="220">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>           
					</tr>
				</thead>
				<tbody>
					<tr>                    
						<td align="center">
							<?  
							if ($source==1) {
								echo create_drop_down( "cbo_service_company_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$service_company order by comp.company_name","id,company_name",1, "-- Select --", $service_company, "","" );
							}
							else if($source==3)
							{
								echo create_drop_down( "cbo_service_company_name", 120, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 and  a.id=$service_company group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "" );
							}
							else
							{
								echo create_drop_down( "cbo_service_company_name", 120, $blank_array,"",1, "-- Select --", 0, "" );
							}
							?>
						</td>
						<td align="center">
							<?  
							$search_by = array(1=>'Subcon Issue Number');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 140, $search_by,"",0, "--Select--", "",1,0 );
							?>
						</td>
						<td width="" align="center" id="search_by_td">				
							<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td>    
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" />&nbsp;&nbsp;&nbsp;
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" />
						</td> 
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_service_company_name').value+'_'+<? echo $source; ?>+'_'+<? echo $service_company; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_multy_issue_search_list_view', 'search_div', 'fabric_issue_to_finishing_process_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />	 			
						</td>
					</tr>
					<tr>                  
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
							<!-- Hidden field here-->
							<!-- END-->
						</td>
					</tr>    
				</tbody>
			</tr>         
		</table>    
		<div align="center" style="margin-top:10px" valign="top" id="search_div"> </div> 

	</form>
</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_multy_issue_search_list_view")
{
	echo '<input type="hidden" id="hidden_return_number" value="" /><input type="hidden" id="hidden_return_id" value="" />';

	
	$ex_data = explode("_",$data);
	$search_by = $ex_data[0];
	$search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$cbo_service_company_name = $ex_data[5];
	$source = $ex_data[6];
	$working_company = $ex_data[7];
	$year = $ex_data[8];

	if($search_by==1)
	{
		if($search_common!="") $search_field_cond="and a.recv_number like '%$search_common'";
	}


	if( $txt_date_from!="" && $txt_date_to!="" ) 
	{
			if($db_type==0)
			{
				$date_cond="and a.receive_date between '".change_date_format(trim($txt_date_from), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($txt_date_to), "yyyy-mm-dd", "-")."'";
			}
			else
			{
				$date_cond="and a.receive_date between '".change_date_format(trim($txt_date_from),'','',1)."' and '".change_date_format(trim($txt_date_to),'','',1)."'";
			}
		}
		else
		{
			$date_cond="";
		}


		if($db_type==0) 
		{
			$year_field="YEAR(a.receive_date) as year,";
		}
		else if($db_type==2) 
		{
			$year_field="to_char(a.receive_date,'YYYY') as year,";
		}
		else $year_field="";//defined Later

		if ($txt_date_from=="" && $txt_date_to=="") {
			if($db_type==0) 
			{
				$year_cond=" and YEAR(a.receive_date)= '$year'";
			}
			else if($db_type==2) 
			{
				$year_cond=" and to_char(a.receive_date,'YYYY') = '$year'";
			}
		}
		
	

	//if(str_replace("'","",$return_to==0)){echo "<p style='font-size:25px; color:#F00'>Please Select Supplier.</p>";die;}
	//else{$supplier_con=" and a.supplier_id=$return_to";}
	
	
 	$sql = "select a.id, $year_field a.recv_number_prefix_num,a.recv_number, a.dyeing_source, a.company_id,a.dyeing_company, a.receive_date, b.booking_without_order,sum(b.batch_issue_qty) as batch_issue_qty
	from inv_receive_mas_batchroll  a,pro_grey_batch_dtls b where a.company_id=$company and a.dyeing_company=$cbo_service_company_name and a.entry_form=91 and a.status_active=1 and a.is_deleted=0 and a.id = b.mst_id and b.status_active=1 and b.is_deleted=0 and a.dyeing_source=$source $search_field_cond $date_cond  $year_cond
	group by a.id, a.insert_date, a.recv_number_prefix_num, a.recv_number, a.dyeing_source,a.company_id, a.dyeing_company, a.receive_date,b.booking_without_order  
	order by a.id";

	/*$sql = "select a.id, $year_field a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id,a.issue_date, a.item_category, a.received_id,a.received_mrr_no, sum(b.cons_quantity)as cons_quantity,a.is_posted_account
	from inv_issue_master a, inv_transaction b
	where a.id=b.mst_id and b.transaction_type=3 and a.status_active=1 and a.item_category=1 and b.item_category=1 and a.entry_form=8  $supplier_con $sql_cond group by a.id, a.issue_number_prefix_num, a.issue_number, a.company_id, a.supplier_id, a.issue_date, a.item_category, a.received_id, a.received_mrr_no, a.insert_date,a.is_posted_account order by a.id";*/
	//echo $sql;
	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(2=>$company_arr,3=>$supplier_arr);
	echo create_list_view("list_view", "Challan/System No, Year, Company Name, Issue Date, Issue Qty","100,40,150,100","550","230",0, $sql , "js_set_value", "recv_number,dyeing_source,id", "1", 1, "0,0,company_id,0,0", $arr, "recv_number,year,company_id,receive_date,batch_issue_qty","","","0,0,0,3,1","",1) ;	//
	//issue_number_prefix_num,year,company_id,supplier_id,issue_date,cons_quantity,received_mrr_no


	exit();
}

?>

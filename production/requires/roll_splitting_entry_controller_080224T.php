<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

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
		| pro_roll_split
		| data preparing here
		|--------------------------------------------------------------------------
		|
		*/
		$id = return_next_id_by_sequence("PRO_ROLL_SPLIT_PK_SEQ", "pro_roll_split", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_ROLL_SPLIT_PK_SEQ", "pro_roll_split",$con,1,$hidden_company_id,'RS',75,date("Y",time()),13 ));
		
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$hidden_company_id.",".$hidden_rollId.",".$hidden_roll_mst.",".$hidden_po_breakdown_id.",".$hidden_roll_wgt.",".$hidden_barcode.",75,".$hidden_table_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$hidden_original_pcs.")";
		//echo "5**".$data_array;die;
		
		$dtls_sql=sql_select("select mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,gsm,width,roll_wgt,buyer_id,job_no, order_id,color_id,inserted_by,insert_date from pro_grey_batch_dtls where id=$hidden_dtls_id");
		
		foreach($dtls_sql as $inf)
		{
			$knitting_source=$inf[csf('knitting_source')];	
			$knitting_company=$inf[csf('knitting_company')];
			$booking_no=$inf[csf('booking_no')];
			$receive_basis=$inf[csf('receive_basis')];
			$prod_id=$inf[csf('prod_id')];
			$body_part_id=$inf[csf('body_part_id')];
			$febric_description_id=$inf[csf('febric_description_id')];
			$gsm=$inf[csf('gsm')];
			$width=$inf[csf('width')];
			$buyer_id=$inf[csf('buyer_id')];
			$job_no=$inf[csf('job_no')];
			$order_id=$inf[csf('order_id')];
			$color_id=$inf[csf('color_id')];
		}
		
		$maxRollNo=return_field_value("max(roll_no) as roll_no","pro_roll_details","entry_form in(2,22,62) and po_breakdown_id=$hidden_po_breakdown_id and booking_without_order=$booking_without_order",'roll_no'); 
		//echo "10**".$maxRollNo;die;	

		$barcode_year=date("y");
		$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","pro_roll_details","barcode_year=$barcode_year","suffix_no");
		if(str_replace("'","",$hidden_entry_form)<10)
			$hidden_entry_form=str_pad(str_replace("'","",$hidden_entry_form),2,"0",STR_PAD_LEFT);
		else
			$hidden_entry_form=str_replace("'","",$hidden_entry_form);

		$barcodeNos='';
		$prod_id_array=array();
		$prod_data_array=array();
		$prod_new_array=array();
		$company_id=str_replace("'","",$cbo_company_id);
		$z=1; 
		$batch_weight=0; $txt_batch_no='';
		$total_split_qty=0;
		
		//if(str_replace("'","",$booking_without_order)==0) $booking_number=
		$splitted_barcode_arr=array();
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			//$barcodeNo="barcodeNo_".$j;
			//$rollNo="roll_no_".$j;
			$rollNo=$maxRollNo+1;
			$maxRollNo+=1;
			$update_roll_id="update_roll_id_".$j;
			$rollWgt="rollWgt_".$j;
			$roll_reject_qty=0;
			$qtyInPcs="qtyInPcs_".$j;
			
			$id_dtls = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),13 ));
			$barcode_no=$barcode_year."".$hidden_entry_form."".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);
			
			/*
			|--------------------------------------------------------------------------
			| pro_grey_batch_dtls
			| data preparing here
			|--------------------------------------------------------------------------
			|
			*/
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$id_dtls.",".$hidden_mst_id.",".$id_roll.",'".$knitting_source."','".$knitting_company."','".$booking_no."','".$receive_basis."','".$prod_id."','".$body_part_id."','".$febric_description_id."','".$gsm."','".$width."','".$$rollWgt."','".$buyer_id."','".$job_no."','".$order_id."','".$color_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$qtyInPcs."')";
			
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data preparing here
			|--------------------------------------------------------------------------
			|
			*/
			if($data_array_roll!="") $data_array_roll.=",";
			$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no[2].",".$barcode_no.",".$hidden_mst_id.",".$id_dtls.",".$hidden_po_breakdown_id.",62,'".$$rollWgt."','".$$rollWgt."','".$roll_reject_qty."',".$hidden_rollId.",'".$rollNo."',".$hidden_table_id.",".$booking_without_order.",'".$booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$qtyInPcs."',".$hidden_is_sales.")";
			
			$total_split_qty+=str_replace("'","",$$rollWgt);
			$total_split_qtyInPcs+=str_replace("'","",$$qtyInPcs);
			$barcodeNos.=$barcode_no."__".$id_dtls."__".$id_roll.",";

			$splitted_barcode_arr[$barcode_no]["barcode_year"] = $barcode_year;
			$splitted_barcode_arr[$barcode_no]["barcode_suffix_no"] = $barcode_suffix_no[2];
			$splitted_barcode_arr[$barcode_no]["roll_wgt"] = $$rollWgt;
			$splitted_barcode_arr[$barcode_no]["roll_wgt"] = $$rollWgt;
			$splitted_barcode_arr[$barcode_no]["qty_in_pcs"] = $$qtyInPcs;			
		}
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll.$order_id;die;

		// adjust roll issue data
		$get_roll_issue_details = sql_select("select * from pro_roll_details where barcode_no=$hidden_barcode and entry_form=61 and status_active=1 and is_deleted=0 and is_returned <>1");
		foreach ($splitted_barcode_arr as $barcode => $barcode_row)
		{
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data preparing here
			| $data_array_roll_issue
			|--------------------------------------------------------------------------
			|
			*/
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			if($data_array_roll_issue!="") $data_array_roll_issue.=",";
			$data_array_roll_issue .="(".$id_roll.",".$barcode_row["barcode_year"].",".$barcode_row["barcode_suffix_no"].",".$barcode.",".$get_roll_issue_details[0][csf("mst_id")].",".$get_roll_issue_details[0][csf("dtls_id")].",".$get_roll_issue_details[0][csf("po_breakdown_id")].",61,'".$barcode_row["roll_wgt"]."','".$barcode_row["roll_wgt"]."','',".$hidden_rollId.",'".$rollNo."',".$hidden_rollId.",".$get_roll_issue_details[0][csf("booking_without_order")].",'".$get_roll_issue_details[0][csf("booking_no")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$barcode_row["qty_in_pcs"]."','".$get_roll_issue_details[0][csf("is_sales")]."')";
		}

		$update_roll_wgt=str_replace("'","",$txt_original_wgt) - $total_split_qty;
		$update_qtyInPcs=str_replace("'","",$txt_original_pcs) - $total_split_qtyInPcs;
		
		/*
		|--------------------------------------------------------------------------
		| pro_roll_split
		| data inserting here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="id,system_number_prefix,system_number_prefix_num,system_number,company_id,roll_id,roll_no,order_id,roll_wgt,barcode_no,entry_form,split_from_id,inserted_by,insert_date,qty_in_pcs";
		$rID=sql_insert("pro_roll_split",$field_array,$data_array,0);
		if($rID)
			$flag=1;
		else
			$flag=0;
		
		/*
		|--------------------------------------------------------------------------
		| pro_grey_batch_dtls
		| data inserting here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1) 
		{
			$field_array_dtls="id, mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,gsm,width,roll_wgt,buyer_id,job_no,order_id,color_id,inserted_by,insert_date,qty_in_pcs";
			$rID2=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
			if($rID2)
				$flag=1;
			else
				$flag=0;
		}
		
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1) 
		{
			$field_array_roll="id,barcode_year,barcode_suffix_no,barcode_no,mst_id,dtls_id,po_breakdown_id,entry_form,qnty,qc_pass_qnty, reject_qnty,roll_id,roll_no,roll_split_from,booking_without_order,booking_no,inserted_by,insert_date,qc_pass_qnty_pcs,is_sales";
			$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($rID3)
				$flag=1;
			else
				$flag=0;
		}
		
		/*
		|--------------------------------------------------------------------------
		| pro_grey_batch_dtls
		| data updating here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1) 
		{
			$field_array_dtls_update="roll_wgt*qty_in_pcs*updated_by*update_date";
			$data_array_dtls_update="".$update_roll_wgt."*".$update_qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID4=sql_update("pro_grey_batch_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$hidden_dtls_id,1);
			if($rID4)
				$flag=1;
			else
				$flag=0; 
		}
		
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data updating here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1) 
		{
			$field_array_roll_update="qnty*qc_pass_qnty*qc_pass_qnty_pcs*updated_by*update_date";
			$data_array_roll_update="".$update_roll_wgt."*".$update_roll_wgt."*".$update_qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID5=sql_update("pro_roll_details",$field_array_roll_update,$data_array_roll_update,"id",$hidden_table_id,1);
			if($rID5)
				$flag=1;
			else
				$flag=0; 
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1) 
		{
			$field_array_roll_issue="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, roll_id,roll_no,roll_split_from,booking_without_order,booking_no, inserted_by,insert_date,qc_pass_qnty_pcs,is_sales";
			$rID6=sql_insert("pro_roll_details",$field_array_roll_issue,$data_array_roll_issue,0);
			if($rID6)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		| 
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_iss_roll_update="qnty*qc_pass_qnty*qc_pass_qnty_pcs*updated_by*update_date";
		$data_array_iss_roll_update="".$update_roll_wgt."*".$update_roll_wgt."*".$update_qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if($flag==1) 
		{
			$rID7=sql_update("pro_roll_details",$field_array_roll_update,$data_array_roll_update,"id",$get_roll_issue_details[0][csf("id")],1);
			if($rID7)
				$flag=1;
			else
				$flag=0; 
		}
		//echo "10**".$flag;
		//echo "<br>insert into pro_grey_batch_dtls ($field_array_dtls)  values $data_array_dtls";
		//oci_rollback($con);die;
		
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
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id);
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
			if($flag==1) 
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id);
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
		//echo "10**".$hidden_is_sales;die;
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		// =======================================
		for($k=1;$k<=$tot_row;$k++)
		{
			$barcodeNo="barcodeNo_".$k;
			$all_barcodeNo.=$$barcodeNo.",";
		}
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
						$barCond.=" b.barcode_no in($chunk_arr_value) or ";
					}

					$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
				}
				else
				{
					$all_barcode_no_cond=" and b.barcode_no in($barcod_NOs)";
				}
			}
		}
		$split_barcodeNxtProcessed = sql_select("SELECT a.batch_no as issue_number , b.entry_form, b.barcode_no, b.qnty from  pro_batch_create_mst a, pro_roll_details b
			where a.id = b.mst_id and b.entry_form = 64 $all_barcode_no_cond and b.status_active = 1 and b.is_deleted = 0");
		if($split_barcodeNxtProcessed[0][csf("barcode_no")] !="")
		{
			foreach ($split_barcodeNxtProcessed as $val)
			{
				$actual_wgt_arr[$val[csf("barcode_no")]] = $val[csf("qnty")];
			}
		}
		// =======================================

		$dtls_sql=sql_select("select mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,gsm,width,roll_wgt,buyer_id,job_no,order_id,color_id,inserted_by,insert_date,qty_in_pcs from pro_grey_batch_dtls where id=$hidden_dtls_id");
		foreach($dtls_sql as $inf)
		{
			$knitting_source=$inf[csf('knitting_source')];	
			$knitting_company=$inf[csf('knitting_company')];
			$booking_no=$inf[csf('booking_no')];
			$receive_basis=$inf[csf('receive_basis')];
			$prod_id=$inf[csf('prod_id')];
			$body_part_id=$inf[csf('body_part_id')];
			$febric_description_id=$inf[csf('febric_description_id')];
			$gsm=$inf[csf('gsm')];
			$width=$inf[csf('width')];
			$buyer_id=$inf[csf('buyer_id')];
			$job_no=$inf[csf('job_no')];
			$order_id=$inf[csf('order_id')];
			$color_id=$inf[csf('color_id')];
		}
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		//$id_dtls=return_next_id( "id", "pro_grey_batch_dtls", 1 ) ;

		$barcode_year=date("y");  
		//$barcode_suffix_no=return_field_value("max(barcode_suffix_no) as suffix_no","pro_roll_details","barcode_year=$barcode_year","suffix_no");
		if(str_replace("'","",$hidden_entry_form)<10)
			$hidden_entry_form=str_pad(str_replace("'","",$hidden_entry_form),2,"0",STR_PAD_LEFT);
		else
			$hidden_entry_form=str_replace("'","",$hidden_entry_form);
		
		$maxRollNo=return_field_value("max(roll_no) as roll_no","pro_roll_details","entry_form in(2,22,62) and po_breakdown_id=$hidden_po_breakdown_id and booking_without_order=$booking_without_order",'roll_no'); 
		$barcodeNos='';
		$batch_weight=0;
		$txt_batch_no='';$prev_split_barcode_no="";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			//$rollNo="roll_no_".$j;
			$update_roll_id="update_roll_id_".$j;
			$rollWgt="rollWgt_".$j;
			$barcodeNo="barcodeNo_".$j;
			$update_dtls_id="update_dtls_id_".$j;
			$roll_reject_qty=0;
			$qtyInPcs="qtyInPcs_".$j;

			if(str_replace("'","",$$update_roll_id)!="")
			{
				if ($actual_wgt_arr[str_replace("'","", $$barcodeNo)] !="") 
				{
					if( number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","")  != number_format($$rollWgt,2,".",""))
					{
						echo "30**Sorry! This barcode (". $$barcodeNo .") is batch. actual weight ". number_format($actual_wgt_arr[str_replace("'","", $$barcodeNo)],2,".","") ." doesn't match with current ".$$rollWgt ."";
						disconnect($con);
						die;
					}
				}

				$prev_split_barcode_no.=$$barcodeNo.",";
				$total_split_qty+=str_replace("'","",$$rollWgt);
				$total_split_qtyInPcs+=str_replace("'","",$$qtyInPcs);
				
				$update_roll_arr[]=$$update_roll_id;
				$update_dtls_arr[]=$$update_dtls_id;
				
				$data_array_roll_update[$$update_roll_id]=explode("*",($$rollWgt."*".$$rollWgt."*".$$qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				
				$data_array_dtls_update[$$update_dtls_id]=explode("*",($$rollWgt."*".$$qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				//$data_array_dtls_update[$$update_dtls_id]=explode("*",($$rollWgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				$update_issue_barcode[$$barcodeNo."_".$$rollWgt."_".$$qtyInPcs] = $$barcodeNo."_".$$rollWgt."_".$$qtyInPcs;
				$barcodeNos.=$$barcodeNo."__".$$update_dtls_id."__".$$update_roll_id.",";
			}
			else
			{
				$id_dtls = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),13 ));
				$barcode_no=$barcode_year."".$hidden_entry_form."".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);
				$rollNo=$maxRollNo+1;
				$maxRollNo+=1;
				
				/*
				|--------------------------------------------------------------------------
				| pro_grey_batch_dtls
				| data preparing for
				| $data_array_dtls
				|--------------------------------------------------------------------------
				|
				*/
				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$hidden_mst_id.",".$id_roll.",'".$knitting_source."','".$knitting_company."','".$booking_no."','".$receive_basis."','".$prod_id."','".$body_part_id."','".$febric_description_id."','".$gsm."','".$width."','".$$rollWgt."','".$buyer_id."','".$job_no."','".$order_id."','".$color_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$qtyInPcs."')";
				
				/*
				|--------------------------------------------------------------------------
				| pro_roll_details
				| data preparing here for
				| $data_array_roll
				|--------------------------------------------------------------------------
				|
				*/
				$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no[2].",".$barcode_no.",".$hidden_mst_id.",".$id_dtls.",".$hidden_po_breakdown_id.",62,'".$$rollWgt."','".$$rollWgt."','".$roll_reject_qty."',".$hidden_rollId.",'".$rollNo."',".$hidden_table_id.",".$booking_without_order.",'".$booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$qtyInPcs."',".$hidden_is_sales.")";
				
				$total_split_qty+=str_replace("'","",$$rollWgt);
				$total_split_qtyInPcs+=str_replace("'","",$$qtyInPcs);
				$barcodeNos.=$barcode_no."__".$id_dtls."__".$id_roll.",";
				//$id_dtls = $id_dtls+1;
				//$id_roll = $id_roll+1;
				$splitted_barcode_arr[$barcode_no]["barcode_year"] = $barcode_year;
				$splitted_barcode_arr[$barcode_no]["barcode_suffix_no"] = $barcode_suffix_no[2];
				$splitted_barcode_arr[$barcode_no]["roll_wgt"] = $$rollWgt;	
				$splitted_barcode_arr[$barcode_no]["qty_in_pcs"] = $$qtyInPcs;
			}
		}
		
		/*
		|--------------------------------------------------------------------------
		| batch found validation here
		|--------------------------------------------------------------------------
		*/
		$nxtProcessedBarcodeRes = sql_select("SELECT a.batch_no , b.entry_form, b.barcode_no
		from  pro_batch_create_mst a, pro_roll_details b
		where a.id = b.mst_id and b.entry_form = 64 and b.barcode_no=$hidden_barcode
		and b.status_active = 1 and b.is_deleted = 0");
	
		foreach ($nxtProcessedBarcodeRes as $val) 
		{
			if($val[csf("entry_form")] == 64)
			{
				echo "30**Batch found against these barcode. Batch No : ".$val[csf("batch_no")];disconnect($con);
				die;
			}
		}

		/*$prev_split_barcode=chop($prev_split_barcode_no,",");
		if($prev_split_barcode!="")
		{
			$split_barcodeNxtProcessed = sql_select("SELECT a.batch_no as issue_number , b.entry_form, b.barcode_no
			from  pro_batch_create_mst a, pro_roll_details b
			where a.id = b.mst_id and b.entry_form = 64 and b.barcode_no in ($prev_split_barcode)
			and b.status_active = 1 and b.is_deleted = 0");
		
			foreach ($split_barcodeNxtProcessed as $val) 
			{
				if($val[csf("entry_form")] == 64)
				{
					echo "30**Batch found against these barcode. Batch No : ".$val[csf("issue_number")];disconnect($con);
					die;
				}
			}
		}*/
		// echo "10**string";die;


		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data preparing here for
		| $data_array_roll_issue
		| roll issue
		|--------------------------------------------------------------------------
		|
		*/
		$get_roll_issue_details = sql_select("select * from pro_roll_details where barcode_no=$hidden_barcode and entry_form=61 and is_returned <>1 and status_active=1 and is_deleted=0");
		foreach ($splitted_barcode_arr as $barcode => $barcode_row)
		{
			$id_roll_iss = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$data_array_roll_issue .="(".$id_roll_iss.",".$barcode_row["barcode_year"].",".$barcode_row["barcode_suffix_no"].",".$barcode.",".$get_roll_issue_details[0][csf("mst_id")].",".$get_roll_issue_details[0][csf("dtls_id")].",".$get_roll_issue_details[0][csf("po_breakdown_id")].",61,'".$barcode_row["roll_wgt"]."','".$barcode_row["roll_wgt"]."','',".$hidden_rollId.",'".$rollNo."',".$hidden_rollId.",".$get_roll_issue_details[0][csf("booking_without_order")].",'".$get_roll_issue_details[0][csf("booking_no")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$barcode_row["qty_in_pcs"]."','".$get_roll_issue_details[0][csf("is_sales")]."')";
		}
		//echo "10**";print_r($update_issue_barcode);die;

		if(count(array_filter($update_issue_barcode)) > 0)
		{
			$field_array_up_roll=explode("*","qnty*qc_pass_qnty_pcs*updated_by*update_date");
			$sql_up.= "UPDATE pro_roll_details SET ";
			for ($len=0; $len<count($field_array_up_roll); $len++)
			{
				$sql_up.=" ".$field_array_up_roll[$len]." = CASE barcode_no ";

				foreach ($update_issue_barcode as $val) 
				{
					$barWgtArr = explode("_", $val);
					$up_iss_barcode = $barWgtArr[0];
					$up_iss_qnty = $barWgtArr[1];
					$up_iss_pcs = $barWgtArr[2];
					
					if($field_array_up_roll[$len] == "qnty"){
						$update_value = $up_iss_qnty;
					}
					elseif($field_array_up_roll[$len] == "qc_pass_qnty_pcs"){
						$update_value = $up_iss_pcs;
					}
					elseif($field_array_up_roll[$len] == "updated_by")
					{
						$update_value = $_SESSION['logic_erp']['user_id'];
					}
					elseif($field_array_up_roll[$len] == "update_date")
					{
						$update_value = "'".$pc_date_time."'";
					}

					$sql_up .= " when ".$up_iss_barcode."  then ".$update_value;
					$issue_update_barcode_arr[$up_iss_barcode] =  $up_iss_barcode;
				}

				if ($len!=(count($field_array_up_roll)-1)) $sql_up.=" END, "; else $sql_up.=" END ";
			}
			$sql_up.=" where entry_form=61 and is_returned<>1 and  barcode_no in (".implode(",",$issue_update_barcode_arr).")"; 
			$issue_roll_update_sql = $sql_up;
		}

		$deleted_all_id=str_replace("'","",$deleted_all_id);
		//echo "10**";
		if($deleted_all_id!="")
		{
			$deleted_ids=explode(",",$deleted_all_id);
			foreach($deleted_ids as $ids)
			{
				$id_detals=explode("**",$ids);
				$deleted_roll_id[]=$id_detals[0];				
				$deleted_detls_id[]=$id_detals[1];
				$deleted_barcode_no[]=$id_detals[2];
				
				$data_array_roll_deleted[$id_detals[0]]=explode("*",($_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
				$data_array_dtls_deleted[$id_detals[1]]=explode("*",($_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
			}
		}
		
		$update_roll_arr[]=str_replace("'","",$hidden_table_id);
		$update_dtls_arr[]=str_replace("'","",$hidden_dtls_id);
		$update_roll_wgt=str_replace("'","",$txt_original_wgt) - $total_split_qty;
		$update_qtyInPcs=str_replace("'","",$txt_original_pcs) - $total_split_qtyInPcs;
		
		$data_array_roll_update[str_replace("'","",$hidden_table_id)]=explode("*",($update_roll_wgt."*".$update_roll_wgt."*".$update_qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		$data_array_dtls_update[str_replace("'","",$hidden_dtls_id)]=explode("*",($update_roll_wgt."*".$update_roll_wgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		
		$flag=1;
		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data deleting here
		|--------------------------------------------------------------------------
		|
		*/
		if(count($data_array_roll_deleted)>0)
		{
			// here deleted child recv by batch, entry_form=62

			$field_array_roll_deleted="updated_by*update_date*status_active*is_deleted";
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_deleted, $data_array_roll_deleted, $deleted_roll_id ));
			if($flag==1) 
			{
				if($rollUpdate)
					$flag=1;
				else
					$flag=0; 
			} 

			// here deleted issue child, entry_form=61

			//echo "10**"."UPDATE pro_roll_details set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where entry_form=61 and barcode_no in(".implode(",", $deleted_barcode_no).")";
			//oci_rollback($con); die;

			$iss_deleted=execute_query("UPDATE pro_roll_details set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where entry_form=61 and barcode_no in(".implode(",", $deleted_barcode_no).")");
			if($flag==1) 
			{
				if($iss_deleted)
					$flag=1;
				else
					$flag=0; 
			}
		}
		//echo '10**test';oci_rollback($con);die;
		/*
		|--------------------------------------------------------------------------
		| pro_grey_batch_dtls
		| data deleting here
		|--------------------------------------------------------------------------
		|
		*/
		if(count($data_array_dtls_deleted)>0)
		{
			$field_array_dtls_deleted="updated_by*update_date*status_active*is_deleted";
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_dtls_deleted, $data_array_dtls_deleted, $deleted_detls_id ));
			if($flag==1) 
			{
				if($rollUpdate)
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
		if(count($data_array_roll_update)>0)
		{
			$field_array_roll_update="qnty*qc_pass_qnty*qc_pass_qnty_pcs*updated_by*update_date";
			//echo bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $update_roll_arr );
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $update_roll_arr ));
			if($flag==1) 
			{
				if($rollUpdate)
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
		if(count($data_array_dtls_update)>0)
		{
			$field_array_dtls_update="roll_wgt*qty_in_pcs*updated_by*update_date";
			$dtlsUpdate=execute_query(bulk_update_sql_statement( "pro_grey_batch_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $update_dtls_arr ));
			if($flag==1) 
			{
				if($dtlsUpdate)
					$flag=1;
				else
					$flag=0; 
			} 
		}
		

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		| by batch
		|--------------------------------------------------------------------------
		|
		*/
		if($data_array_roll!="")
		{
			$field_array_roll="id,barcode_year,barcode_suffix_no,barcode_no,mst_id, dtls_id,po_breakdown_id,entry_form,qnty,qc_pass_qnty,reject_qnty, roll_id,roll_no,roll_split_from,booking_without_order,booking_no,inserted_by,insert_date,qc_pass_qnty_pcs,is_sales";
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
		if($data_array_dtls!="")
		{
			$field_array_dtls="id, mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,gsm,width,roll_wgt,buyer_id,job_no,order_id,color_id,inserted_by,insert_date,qty_in_pcs";
			$rID5=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
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
		| data inserting for
		| roll issue
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1 && $data_array_roll_issue!="") 
		{
			$field_array_roll_issue="id, barcode_year,barcode_suffix_no,barcode_no,mst_id,dtls_id,po_breakdown_id,entry_form,qnty,qc_pass_qnty, reject_qnty,roll_id,roll_no,roll_split_from,booking_without_order,booking_no,inserted_by,insert_date,qc_pass_qnty_pcs,is_sales";
			//echo "10**insert into pro_roll_details (".$field_array_roll_issue.") values ".$data_array_roll_issue;die;
			$rID6=sql_insert("pro_roll_details",$field_array_roll_issue,$data_array_roll_issue,0);
			if($rID6)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data updating for
		| parent barcode issue
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1) 
		{
			$field_array_iss_roll_update="qnty*qc_pass_qnty*qc_pass_qnty_pcs*updated_by*update_date";
			$data_array_iss_roll_update="".$update_roll_wgt."*".$update_roll_wgt."*".$update_qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID7=sql_update("pro_roll_details",$field_array_iss_roll_update,$data_array_iss_roll_update,"id",$get_roll_issue_details[0][csf("id")],1);
			if($rID7)
				$flag=1;
			else
				$flag=0; 
		}

		//child barcode issue update
		if($flag ==1)
		{
			$rID8 = execute_query($issue_roll_update_sql);
			if($rID8)
				$flag=1;
			else
				$flag=0; 
		}
		
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
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id);
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
			if($flag==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id);
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

if($action=="mrr_popup")
{
	echo load_html_head_contents("Receive Info", "../../", 1, 1,'','','');
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
						<th>Company Name</th>
						<th>System No</th>
						<th>Barcode No</th>
						<th id="search_by_td_up" width="180">Insert Date</th>
						<th>
							
							<input type="hidden" name="hidden_system_id" id="hidden_system_id">  
						</th> 
					</thead>
					<tr class="general">
						<td>
							<? 
							echo create_drop_down( "cbo_company_id", 151, "select comp.id, comp.company_name from lib_company comp 
								where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, 
								"--Select Company--", 0, "" );
								?>
							</td>
							
							<td align="center" >				
								<input type="text" style="width:130px" class="text_boxes"  name="txt_system_no" id="txt_system_no" />	
							</td>
							<td align="center" >				
								<input type="text" style="width:130px" class="text_boxes"  name="txt_barcode_no" id="txt_barcode_no" />	
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td> 						
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_barcode_no').value, 'create_challan_search_list_view', 'search_div', 'roll_splitting_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

//done_zs
if($action=="create_challan_search_list_view")
{
	$data = explode("_",$data);
	$system_id=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[0];
	$barcode_no=$data[4];

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.insert_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.insert_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	
	if(trim($system_id)!="")
		$search_field_cond=" and a.system_number_prefix_num=$system_id ";
	if(trim($barcode_no)!="")
		$search_field_cond.=" and a.barcode_no='$barcode_no'";
	if(trim($company_id)==0)
	{
		echo "Please insert Company First";
		die;
	}

	$sql = "select a.id, system_number,a.roll_no,a.split_from_id,a.insert_date,a.company_id,a.order_id,a.barcode_no,a.roll_wgt, b.booking_without_order, b.booking_no, a.qty_in_pcs from pro_roll_split a, pro_roll_details b where a.split_from_id=b.id and b.entry_form=62 and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond order by id"; 
	//echo $sql;//die;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="110">System No</th>
			<th width="120">Company Name</th>
			<th width="120">Order No</th>
			<th width="120">Booking No</th>
			<th width="90">Barcode No</th>
			<th width="50">Roll No</th>
			<th>Insert date</th>
		</thead>
	</table>
	<div style="width:760px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$order_no="";
				$booking_number="";	
				if($row[csf('booking_without_order')]==1)
				{
					$booking_number=$row[csf('booking_no')];
				}
				else
				{
					$order_no=$order_arr[$row[csf('order_id')]];
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('system_number')]."_".$row[csf('id')]."_".$row[csf('barcode_no')]."_".$row[csf('split_from_id')]."_".$row[csf('roll_wgt')]."_".$row[csf('qty_in_pcs')]; ?>');"> 
					<td width="30"><? echo $i; ?></td>
					<td width="110"><p>&nbsp;<? echo $row[csf('system_number')]; ?></p></td>
					<td width="120"><p><? echo $company_arr[$row[csf('company_id')]]; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $order_no; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $booking_number; ?>&nbsp;</p></td>
					<td width="90"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
					<td width="50"><p><? echo $row[csf('roll_no')]; ?>&nbsp;</p></td>
					<td align="center"><? echo change_date_format($row[csf('insert_date')]); ?></td>
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

//done_zs
if($action=="roll_details_update")
{
	//$data=explode("_",$data);
	$sql=sql_select("SELECT a.id,a.barcode_no,a.roll_no,a.qc_pass_qnty,a.qc_pass_qnty_pcs,b.id as dtls_id from  pro_roll_details a,  pro_grey_batch_dtls b where b.id=a.dtls_id  and a.roll_split_from=$data and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.id" );
	
	$split_barcode_arr = array();
	foreach ($sql as $bar_row)
	{
		$split_barcode_arr[$bar_row[csf('barcode_no')]] = $bar_row[csf('barcode_no')];
	}

	$sql_split_batch = sql_select("SELECT c.barcode_no FROM pro_roll_details c WHERE c.entry_form=64 AND c.status_active=1 AND c.is_deleted=0 AND c.barcode_no IN(".implode(",", $split_barcode_arr).")");
	$split_batch_barcode=array();
	foreach($sql_split_batch as $row)
	{
		$split_batch_barcode[$row[csf("barcode_no")]] = $row[csf("barcode_no")];
	}

	$i=1;
	foreach($sql as $row)
	{
		$disable="";
		if(!empty($split_batch_barcode[$row[csf("barcode_no")]]))
		{
			$disable="disabled";
		}
		?>
		<tr id="tr_<? echo $i;  ?>" align="center" valign="middle">
			<td width="40" id="txtSl_<? echo $i;  ?>"><? echo $i;  ?></td>
			<td width="100" >
				<input type="text" name="roll_no[]" id="rollno_<? echo $i;  ?>" style="width:80px" class="text_boxes_numeric" onBlur="check_roll_no(<? echo $i;  ?>)" value="<? echo $row[csf('roll_no')] ;  ?>" disabled/>
			</td>
			<td width="60" >
				<input type="text" name="rollWgt[]" id="rollWgt_<? echo $i;  ?>" style="width:50px" class="text_boxes_numeric"   onBlur="check_qty(<? echo $i;  ?>)" value="<? echo $row[csf('qc_pass_qnty')] ;  ?>" <?echo $disable;?>/>
			</td>
            
			<td width="60" >
				<input type="text" name="qtyInPcs[]" id="qtyInPcs_<? echo $i;  ?>" style="width:50px" class="text_boxes_numeric" onBlur="check_qty_in_pcs(<? echo $i;  ?>)" value="<? echo $row[csf('qc_pass_qnty_pcs')]*1;  ?>" <? echo $readonly; ?>  <?echo $disable;?>/>
				<input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $i;  ?>" style="width:50px" class="text_boxes_numeric"  value="<? echo $row[csf('qc_pass_qnty_pcs')]*1;  ?>" <? echo $readonly; ?> />
			</td>
			<td width="180" >
				<input type="text" name="barcodeNo[]" id="barcodeNo_<? echo $i;  ?>" style="width:150px" class="text_boxes" value="<? echo $row[csf('barcode_no')] ;  ?>"  placeholder="Display" readonly/>
			</td>
			<td id="button_1" align="center">
				<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i;  ?>)" />
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i;  ?>);" />
				<input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $i;  ?>" value="<? echo $row[csf('id')] ;  ?>"/>
				<input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<? echo $i;  ?>" value="<? echo $row[csf('dtls_id')] ;  ?>"/>
			</td>
			<td>
            	<input id="chkBundle_<? echo $i; ?>" type="checkbox" name="chkBundle"  >
			</td>
		</tr>
		<?
		$i++;	
	}
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	if($service_source>0) $disable=1; else $disable=0;  
	?> 
	<script>
		function js_set_value(data)
		{
			$('#hidden_barcode_nos').val(data);
			parent.emailwindow.hide();
		}
	</script>
    </head>
    <body>
        <div align="center" style="width:1030px;">
            <form name="searchwofrm"  id="searchwofrm" autocomplete="off">
                <fieldset style="width:1030px; margin-left:2px">
                    <legend>Enter search words</legend>           
                    <table cellpadding="0" cellspacing="0" width="550" border="1" rules="all" class="rpt_table">
                        <thead>
                            <th class="must_entry_caption">Company</th>
                            <th>Job Year</th>
                            <th>Job No</th>
                            <th id="search_by_td_up" width="120">Order No</th>
                            <th>Barcode No</th>
                            <th>
                                <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                                <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
                            </th> 
                        </thead>
                        <tr class="general">
                            <td>
                                <? 
                                echo create_drop_down( "cbo_company_id", 151, "select comp.id, comp.company_name from lib_company comp 
                                    where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name","id,company_name", 1, 
                                    "--Select Company--", 0, "" );
                                    ?>
                                </td>
                                <td align="center">	
                                    <?
                                    $selected_year=date("Y");
                                    echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
                                    ?>
                                </td>
                                <td align="center">	
                                    <input type="text" style="width:80px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />	
                                </td> 
                                <td align="center" id="search_by_td">				
                                    <input type="text" style="width:130px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />	
                                </td> 			
                                <td><input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" /></td>    			
                                <td align="center">
                                    <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('barcode_no').value+'_'+document.getElementById('cbo_year').value, 'create_barcode_search_list_view', 'search_div', 'roll_splitting_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                                </td>
                            </tr>
                        </table>
                        <div style="width:100%; margin-top:5px;" id="search_div" ></div>
                    </fieldset>
                </form>
        </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
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
	
	if($db_type==0) 
	{
		$year_field=" YEAR(a.insert_date) as year";
		$year_job=" YEAR(e.insert_date) as year_job";
		$year_job_search=" and YEAR(e.insert_date)=$data[4]";
		
	}
	else if($db_type==2) 
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";
		$year_job=" to_char(e.insert_date,'YYYY') as year_job";
		$year_job_search="  and to_char(e.insert_date,'YYYY')=$data[4]";
	}
	
	if($company_id==0) { echo "Please Select Company First"; die;}
	
	$search_field_cond="";
	if(trim($data[0])!="")
	{
		$search_field_cond.="and d.po_number like '%".trim($data[0])."%' $year_job_search";
	}
	
	if(trim($data[1])!="")
	{
		$search_field_cond.="and e.job_no_prefix_num=$data[1] $year_job_search";
	}
	if($barcode_no!="")
	{
		$barcode_cond="and c.barcode_no='$barcode_no'";
	}
	

	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no,system_number from pro_roll_split where  status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]['bcode']=$row[csf('barcode_no')];
		$scanned_barcode_arr[$row[csf('barcode_no')]]['system_number']=$row[csf('system_number')];
	}
	$system_number=$scanned_barcode_arr[$barcode_no]['system_number'];
	$chk_scanned_barcode=$scanned_barcode_arr[$barcode_no]['bcode'];
	if($chk_scanned_barcode!='' && $barcode_no!='')
	{
		echo "<div style='color:red;font-size:22px'>Roll Splitting Found= &nbsp;".$system_number."</div>"; die;
	}

	$splited_child = sql_select("SELECT c.roll_split_from, c.barcode_no as child_barcode, b.barcode_no as mother_barcode from pro_roll_details c left join wo_po_break_down d on c.po_breakdown_id=d.id and booking_without_order=0 left join wo_po_details_master e on e.job_no=d.job_no_mst, pro_roll_details b where c.roll_split_from=b.id and c.entry_form=62 and c.status_active=1 and c.is_deleted=0 $search_field_cond $barcode_cond");

	foreach ($splited_child as $val) 
	{
		$scanned_barcode_arr[$val[csf('child_barcode')]]['bcode']=$val[csf('child_barcode')];
		$splited_child_arr[$val[csf('child_barcode')]]=$val[csf('mother_barcode')];
	}

	if(!empty($splited_child_arr) && $barcode_no!='')
	{
		echo "<div style='color:red;font-size:22px'>Splitted from another mother roll= &nbsp;".$splited_child_arr[$barcode_no]."</div>"; die;
	}

	$barcodeData=sql_select( "select barcode_no from pro_roll_details where  entry_form=64 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}
	unset($barcodeData);
	//if($db_type==0) $year= "";
	$po_details_sql="select a.job_no_prefix_num,$year_field,b.po_number ";
	
	/*$sql="SELECT e.job_no_prefix_num,$year_job,a.id,a.recv_number_prefix_num,a.recv_number,$year_field,a.company_id,c.roll_split_from,a.dyeing_source,
	a.dyeing_company,a.receive_date, c.barcode_no, c.roll_no, c.qc_pass_qnty, d.po_number, d.pub_shipment_date, d.job_no_mst,c.booking_no,
	c. booking_without_order, c.qc_pass_qnty_pcs
	FROM inv_receive_mas_batchroll a,  pro_roll_details c, wo_po_break_down d, wo_po_details_master e 
	WHERE a.id=c.mst_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form=62 and e.job_no=d.job_no_mst and c.entry_form=62 
	and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond";*/ 

	$sql="SELECT e.job_no_prefix_num,$year_job,a.id,a.recv_number_prefix_num,a.recv_number,$year_field,a.company_id,c.roll_split_from,a.dyeing_source,
	a.dyeing_company,a.receive_date, c.barcode_no, c.roll_no, c.qc_pass_qnty, d.po_number, d.pub_shipment_date, d.job_no_mst,c.booking_no,
	c. booking_without_order, c.qc_pass_qnty_pcs
	FROM inv_receive_mas_batchroll a,  pro_roll_details c 
	left join wo_po_break_down d on c.po_breakdown_id=d.id and booking_without_order=0 and c.is_sales=0
	left join wo_po_details_master e on e.job_no=d.job_no_mst and c.is_sales=0
	WHERE a.id=c.mst_id and a.company_id=$company_id and a.entry_form=62 and c.entry_form=62 
	and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.barcode_no>0 $search_field_cond $barcode_cond"; 

	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1010" class="rpt_table" align="center">
		<thead>
			<th width="30">SL</th>
			<th width="50">Receive No</th>
			<th width="50">Year</th>
			<th width="70">Receive date</th>
			<th width="50">Job No</th>
			<th width="50">Job Year</th>
			<th width="140">Order No</th>
			<th width="120">Booking No</th>
			<th width="70">Shipment Date</th>
			<th width="80">Barcode No</th>
			<th width="50">Roll No</th>
			<th width="50">Roll Qty.</th>
            <th width="50">Qty. In Pcs</th>
			<th>Roll Type</th>
		</thead>
	</table>
	<div style="width:1010px; max-height:210px; overflow-y:scroll" id="list_container_batch" >	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="990" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($result as $row)
			{  
				$chk_scanned_barcode=$scanned_barcode_arr[$row[csf('barcode_no')]]['bcode'];
				if($chk_scanned_barcode=="")
				{
					
					if($row[csf('booking_without_order')]==1)
					{
						$ponumber='';
						$job_no='';
						$job_year='';
						$shipment_date='';
						$booking_no=$row[csf('booking_no')];
					}
					else
					{
						$ponumber=$row[csf('po_number')];
						$job_no=$row[csf('job_no_prefix_num')];
						$job_year=$row[csf('year_job')];
						$shipment_date=change_date_format($row[csf('pub_shipment_date')]);
						$booking_no='';
					}
					
					$split_roll="";
					if($row[csf('roll_split_from')]!=0) $split_roll=" Splitted ";
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('barcode_no')]; ?>)"> 
						<td width="30">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="50" align="center"><p><? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td width="50" align="center"><p><? echo $job_no; ?></p></td>
						<td width="50" align="center"><p><? echo $job_year; ?></p></td>
						<td width="140"><p><? echo $ponumber; ?></p></td>
						<td width="120"><p><? echo $booking_no; ?></p></td>
						<td width="70" align="center"><? echo $shipment_date; ?></td>
						<td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="50"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
                        <td width="50" align="right"><? echo $row[csf('qc_pass_qnty_pcs')]*1; ?></td>
						<td  align="center"><? echo $split_roll; ?></td>
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

//done_zs
if($action=="load_barcode_mst_form")
{  
	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yean_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id", "yarn_count");
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$batch_details=sql_select("select a.id,a.barcode_no,a.mst_id,a.dtls_id,a.po_breakdown_id,a.roll_no,a.roll_id,a.qc_pass_qnty,a.qc_pass_qnty_pcs, b.roll_wgt,a.booking_without_order, a.booking_no
		from pro_roll_details a,pro_grey_batch_dtls b where a.dtls_id=b.id and entry_form=62 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.barcode_no='".$data."'");
		

	/*$data_array=sql_select("SELECT a.id,c.entry_form,a.booking_without_order,a.booking_id,a.booking_no,b.id as grey_id, a.company_id,a.knitting_company,a.knitting_source, b.prod_id, b.body_part_id, b.febric_description_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.id='".$batch_details[0][csf('roll_id')]."' order by c.entry_form asc");*/
	$data_array=sql_select("SELECT a.id,c.entry_form,a.booking_without_order,a.booking_id,a.booking_no,b.id as grey_id, a.company_id,a.knitting_company,a.knitting_source, b.prod_id, b.body_part_id, b.febric_description_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.qc_pass_qnty_pcs, c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22) and c.entry_form in(2,22) and c.status_active=1 and c.is_deleted=0 and c.barcode_no='".$batch_details[0][csf('barcode_no')]."' order by c.entry_form asc");
	
	
	foreach($data_array as $val)
	{
		$fabric_description=$constructtion_arr[$val[csf("febric_description_id")]].", ".$composition_arr[$val[csf("febric_description_id")]];
		echo "document.getElementById('txt_lot').value = '".($val[csf("yarn_lot")])."';\n"; 
		echo "document.getElementById('txt_fabric_description').value = '".($fabric_description)."';\n"; 
		
		$yean_count="";
		foreach(explode(",",$val[csf("yarn_count")]) as $y_id)
		{
			if($yean_count=="")	$yean_count=$yean_count_arr[$y_id];
			else                   $yean_count.=",".$yean_count_arr[$y_id];
		}
		
		echo "document.getElementById('txt_count').value = '".($yean_count)."';\n"; 
		
		if($val[csf("knitting_source")]==1)  
		{
			echo "$('#txt_knitting_com').val('".$company_arr[$val[csf("knitting_company")]]."');\n"; 
		}
		else
		{
			echo "$('#txt_knitting_com').val('".$supplier_arr[$val[csf("knitting_company")]]."');\n"; 
		}
		
		echo "$('#txt_bar_code_num').val('".$batch_details[0][csf("barcode_no")]."');\n"; 
		echo "document.getElementById('booking_without_order').value  = '".($batch_details[0][csf("booking_without_order")])."';\n"; 
		echo "document.getElementById('txt_company_name').value  = '".($company_arr[$val[csf("company_id")]])."';\n";

		echo "document.getElementById('hidden_po_breakdown_id').value = '".($batch_details[0][csf("po_breakdown_id")])."';\n";  
		
		if($val[csf("is_sales")] == 1)
		{
			$data_array_sales=sql_select("SELECT job_no, buyer_id, within_group, po_buyer,po_job_no FROM fabric_sales_order_mst where id=".$val[csf("po_breakdown_id")]);

			echo "document.getElementById('po_booking_td').innerHTML='Sales Order No';\n";
			echo "document.getElementById('txt_order_no').value = '".$data_array_sales[0][csf("job_no")]."';\n";
			if($data_array_sales[0][csf("within_group")]==1)
			{
				echo "document.getElementById('txt_job_no').value = '".$data_array_sales[0][csf('po_job_no')]."';\n";  
				echo "document.getElementById('txt_buyer').value  = '".$buyer_name_array[$data_array_sales[0][csf('po_buyer')]]."';\n";
			}
			else
			{
				echo "document.getElementById('txt_job_no').value = '';\n"; 
				echo "document.getElementById('txt_buyer').value  = '".$buyer_name_array[$data_array_po[0][csf('buyer_name')]]."';\n";
			}
		}
		else
		{
			if($batch_details[0][csf("booking_without_order")]==1)
			{
				echo "document.getElementById('po_booking_td').innerHTML='Booking No';\n";
				echo "document.getElementById('txt_order_no').value = '".$batch_details[0][csf("booking_no")]."';\n";  
				echo "document.getElementById('txt_job_no').value = '';\n";  
				echo "document.getElementById('txt_buyer').value  = '';\n";
				//echo "document.getElementById('hidden_po_breakdown_id').value = '".($val[csf("booking_id")])."';\n"; 
			}
			else
			{
				$data_array_po=sql_select("SELECT a.job_no, a.buyer_name, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id=".$batch_details[0][csf("po_breakdown_id")]."");
				echo "document.getElementById('po_booking_td').innerHTML='Order No';\n";
				echo "document.getElementById('txt_order_no').value = '".$data_array_po[0][csf('po_number')]."';\n"; 
				echo "document.getElementById('txt_job_no').value = '".$data_array_po[0][csf('job_no')]."';\n";    
				echo "document.getElementById('txt_buyer').value  = '".$buyer_name_array[$data_array_po[0][csf('buyer_name')]]."';\n";
				
			}
		}

		echo "document.getElementById('txt_original_wgt').value = '".($batch_details[0][csf("qc_pass_qnty")])."';\n";
		echo "document.getElementById('txt_original_pcs').value = '".($batch_details[0][csf("qc_pass_qnty_pcs")]*1)."';\n";
		echo "document.getElementById('hidden_original_pcs').value = '".($batch_details[0][csf("qc_pass_qnty_pcs")]*1)."';\n";
		echo "document.getElementById('hidden_company_id').value = '".($val[csf("company_id")])."';\n";    
		echo "document.getElementById('hidden_roll_mst').value  = '".($batch_details[0][csf("roll_no")])."';\n";
		echo "document.getElementById('hidden_entry_form').value  = '".($val[csf("entry_form")])."';\n";
		echo "document.getElementById('hidden_rollId').value  = '".($batch_details[0][csf("roll_id")])."';\n";  
		echo "document.getElementById('hidden_table_id').value = '".($batch_details[0][csf("id")])."';\n"; 
		
		echo "document.getElementById('hidden_dtls_id').value = '".($batch_details[0][csf("dtls_id")])."';\n"; 
		echo "document.getElementById('hidden_mst_id').value = '".($batch_details[0][csf("mst_id")])."';\n"; 
		echo "document.getElementById('hidden_barcode').value = '".($data)."';\n"; 
		echo "document.getElementById('hidden_roll_wgt').value  = '".($batch_details[0][csf('qc_pass_qnty')])."';\n";  
		echo "document.getElementById('txt_original_roll').value = '".($batch_details[0][csf('roll_no')])."';\n";
		echo "document.getElementById('hidden_is_sales').value = '".($val[csf('is_sales')])."';\n";

		echo "load_drop_down('requires/roll_splitting_entry_controller', document.getElementById('hidden_company_id').value, 'load_print_button', 'button_list');\n";
		exit();
	}
}

if($action=="load_print_button")
{
	$print_report_format_arr=return_library_array("select template_name, format_id from lib_report_template where  module_id=6 and report_id=146 and is_deleted=0 and status_active=1 and template_name=$data", "template_name", "format_id");
									
	$report_id=explode(",",$print_report_format_arr[$data]);
	//	print_r($report_id);

	foreach($report_id as $res)
	{
		if($res==317){			
		echo "<input type='button' value='Barcode 128 v2' id='barcode_generation_128' class='formbutton' onClick='fnc_bundle_report(2)'/>";
		}elseif($res==334){			
			echo "<input type='button' value='Barcode Generation' id='barcode_generation' class='formbutton' onClick='fnc_bundle_report(1)'/>";
		}elseif($res==331){
			echo "<input type='button' value='Barcode 128 v3' id='btn_barcode_128v3' name='btn_barcode_128v3' class='formbutton' onClick='fnc_bundle_report(3)'/>";
		}elseif($res==72){
			echo "<input type='button' id='btn_barcode_direct6' name='btn_barcode_direct6' value='Direct Print 6' class='formbutton' onClick='fnc_bundle_report(4)'/>";
		}
		elseif($res==810){
			echo "<input type='button' id='btn_barcode_direct6' name='btn_barcode_direct7' value='Barcode CCL' class='formbutton' onClick='fnc_bundle_report(5)'/>";
		}
		elseif($res==880){
			echo "<input type='button' value='Barcode 128 v3 NZ' id='btn_barcode_v128_nz' name='btn_barcode_v128_nz' class='formbutton' onClick='fnc_bundle_report(6)'/>";
		}
	}
	exit();
}

if($action=="report_barcode_generation")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$roll_id=sql_select("select roll_id,po_breakdown_id  from pro_roll_details where id in($data)");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}
	$sql="select a.company_id,a.receive_basis,a.booking_id,a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.color_id, b.febric_description_id,b.insert_date from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	$result=sql_select($sql);
	$party_name=''; $prod_date=''; $order_id=''; $buyer_name=''; $grey_dia=''; $tube_type=''; $program_no=''; $yarn_lot=''; $yarn_count=''; $brand=''; $gsm=''; $finish_dia='';
	foreach($result as $row)
	{
		if($row[csf('knitting_source')]==1)
		{
			$party_name=return_field_value("company_short_name","lib_company", "id=".$row[csf('knitting_company')]);
		}
		else if($row[csf('knitting_source')]==3)
		{
			$party_name=return_field_value("short_name","lib_supplier", "id=".$row[csf('knitting_company')]);
		}
		
		$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		
		$order_id=$row[csf('order_id')];
		$gsm=$row[csf('gsm')];
		$finish_dia=$row[csf('width')];
		$color=$color_arr[$row[csf('color_id')]];
		$stitch_length=$row[csf('stitch_length')];
		$yarn_lot=$row[csf('yarn_lot')];
		$brand=$brand_arr[$row[csf('brand_id')]];
		$yarn_count='';
		$count_id=explode(",",$row[csf('yarn_count')]);
		foreach($count_id as $val)
		{
			if($val>0)
			{
				if($yarn_count=="") $yarn_count=$count_arr[$val]; else $yarn_count.=",".$count_arr[$val];
			}
		}

		$machine_data=sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='".$row[csf('machine_no_id')]."'");
		$machine_name=$machine_data[0][csf('machine_no')];
		$machine_dia_width=$machine_data[0][csf('dia_width')];
		$machine_gauge=$machine_data[0][csf('gauge')];
		
		//$buyer_name=return_field_value("short_name","lib_buyer", "id=".$row[csf('buyer_id')]);
		
		$comp='';
		if($row[csf('febric_description_id')]==0 || $row[csf('febric_description_id')]=="")
		{
			$comp=return_field_value("item_description","product_details_master","id=".$row[csf('prod_id')]);
		}
		else
		{
			$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('febric_description_id')]);
			
			if($determination_sql[0][csf('construction')]!="")
			{
				$comp=$determination_sql[0][csf('construction')].", ";
			}
			
			foreach( $determination_sql as $d_row )
			{
				$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
			}
		}
		
		if($row[csf('receive_basis')]==2)
		{
			$program_data=sql_select("select width_dia_type, machine_dia from ppl_planning_info_entry_dtls where id='".$row[csf('booking_id')]."'");
			$program_no=$row[csf('booking_id')];
			$grey_dia=$program_data[0][csf('machine_dia')]; 
			$tube_type=$fabric_typee[$program_data[0][csf('width_dia_type')]]; 
		}
	}
	//echo "select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	$po_array=array();
	$all_po_id=implode(",",array_unique($order_id_arr));
	$po_sql=sql_select("select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($all_po_id)");
	foreach($po_sql as $row)
	{
		$po_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no_prefix_num')]; 
		$po_array[$row[csf('id')]]['grouping']=$row[csf('grouping')]; 
		$po_array[$row[csf('id')]]['file_no']=$row[csf('file_no')];
		$po_array[$row[csf('id')]]['buyer_id']=$buyer_name_array[$row[csf('buyer_name')]]; 
	}
	
	$i=1; $barcode_array=array();
	$query="select id, roll_no, po_breakdown_id, barcode_no, qc_pass_qnty as qnty,booking_without_order from pro_roll_details where id in($data)";
	$res=sql_select($query);
	echo '<table width="800" border="0"><tr>';
	foreach($res as $row)
	{
		$file_no='';
		$po_number='';
		$job_no='';
		$buyer_name='';
		$reff_no="";
		if($row[csf('booking_without_order')]==0)
		{
			$file_no=$po_array[$row[csf('po_breakdown_id')]]['file_no'];
			$po_number=$po_array[$row[csf('po_breakdown_id')]]['po_no'];
			$job_no=$po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$buyer_name=$po_array[$row[csf('po_breakdown_id')]]['buyer_id'];
			$reff_no=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
		}
		
		
		$barcode_array[$i]=$row[csf('barcode_no')];
		$txt="&nbsp;&nbsp;".$row[csf('barcode_no')]."; ".$party_name." Job No.".$job_no.";<br>";
		$txt .="&nbsp;&nbsp;M/C: ".$machine_name."; M/C Dia X Gauge-".$machine_dia_width."X".$machine_gauge.";<br>";
		$txt .="&nbsp;&nbsp;Date: ".$prod_date.";<br>";
		$txt .="&nbsp;&nbsp;Buyer: ".$buyer_name.", Order No: ". $po_number.";<br>";
		$txt .="&nbsp;&nbsp;".$comp."<br>";
		$txt .="&nbsp;&nbsp;G/Dia: ".$grey_dia."; SL: ".trim($stitch_length)."; ".trim($tube_type)."; F/Dia: ".trim($finish_dia).";<br>";
		$txt .="&nbsp;&nbsp;GSM: ".$gsm."; ";
		$txt .=$yarn_count."; Lot: ".$yarn_lot.";<br>";
		$txt .="&nbsp;&nbsp;Prg: ".$program_no."; Roll Wt: ".number_format($row[csf('qnty')],2,'.','')." Kg;<br>";
		$txt .="&nbsp;&nbsp;Custom Roll No: ". $row[csf('roll_no')].";";
		if(trim($color)!="") $txt .=" Color: ".trim($color).";";
			
		echo '<td style="padding-left:7px;padding-top:10px;padding-bottom:5px"><div id="div_'.$i.'"></div>'.$txt.'</td>';//border:dotted;
		if($i%3==0) echo '</tr><tr>';
		$i++;
	}
	echo '</tr></table>';
	?>
	
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array =<? echo json_encode($barcode_array); ?>;
		function generateBarcode( td_no, valuess )
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
			
			$("#div_"+td_no).show().barcode(value, btype, settings);
		}

		for (var i in barcode_array) 
		{
			generateBarcode(i,barcode_array[i]);
		}
	</script>
	<?
	exit();
}

if($action=="report_barcode_generation_128")
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');

	$userid=$_SESSION['logic_erp']['user_id'];
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=1", 'lot', 'brand');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');	

	$roll_id=sql_select("select roll_id, po_breakdown_id from pro_roll_details where id in($data)");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}

 	$system_no=return_field_value("system_number_prefix_num", "pro_roll_split", "roll_id IN(".implode(",", array_unique($roll_id_arr)).") and entry_form=75");
 	//echo $system_no.'syudgyud';

	$sql="select a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id, b.operator_name from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";

	$result = sql_select($sql);	
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row) 
	{
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$receive_date=$row[csf('receive_date')];
		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $row[csf('operator_name')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];

		$brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val) {
			if ($val!="") $brand .= $brand_arr[$brand_id_arr[$val]] . ",";
		}
		$brand = chop($brand, ',');
		//$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		if ($row[csf("receive_basis")] == 2) 
		{
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];

			$row[csf("within_group")] = $planning_data[0][csf('within_group')];
			
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
		} 
		else 
		{
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
		}
		$program_no = $row[csf('booking_id')];

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") 
		{
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} 
		else 
		{
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				//$comp = $determination_sql[0][csf('construction')] . ", ";
				$constuction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) 
	{
		if ($row[csf("receive_basis")] == 4)
		{
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $row[csf("booking_id")] . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
		} 
		else 
		{
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
		}
	} 
	else 
	{
		$is_salesOrder = 0;
		if ($row[csf("receive_basis")] == 2) 
		{
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $row[csf("booking_id")]);
		}
		if ($is_salesOrder == 1)
		{
			$po_sql = sql_select("select a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id in($order_id)");
			foreach ($po_sql as $row)
			{
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		} 
		else 
		{
			$po_sql = sql_select("select a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.po_number,d.booking_no_prefix_num from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.booking_type=1 and d.is_short=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($order_id)");
			foreach ($po_sql as $row1) 
			{
				$po_array[$row1[csf('id')]]['no'] = $row1[csf('po_number')];
				$po_array[$row1[csf('id')]]['job_no'] = $row1[csf('job_no')];
				$po_array[$row1[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row1[csf('id')]]['booking_no'] = $row1[csf('booking_no_prefix_num')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row1[csf('buyer_name')]);
			}
		}
	}
	//print_r($po_array);
	$i = 1;
	$barcode_array = array();
	$query = "select a.id, a.inserted_by, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade, c.shift_name, d.recv_number_prefix_num from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no left join pro_grey_prod_entry_dtls c on a.dtls_id=c.id left join inv_receive_master d on c.mst_id=d.id where a.id in($data)";
	$res = sql_select($query);

	$pdf=new PDF_Code128('P','mm',array(80,65));
	$pdf->AddPage();
	$pdf->SetFont('Times','',10);

	$i=2; $j=1; $k=0; $br=0; $n=0;
	foreach ($res as $row) 
	{

		$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=1; $k=0;
		}

		$pdf->Code128($i+1,$j,$row[csf("barcode_no")],50,8);
		$pdf->SetXY($i, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")]. ",Dt:".change_date_format($receive_date). ",Pg:".$program_no. ",S:".$shift_name[$row[csf('shift_name')]]);

		$pdf->SetXY($i, $j+14);
		$pdf->Write(0, $company_short_name.":" . $po_array[$row[csf('po_breakdown_id')]]['booking_no'].",M/C:" . $machine_name . "," . $machine_dia_width . "X" . $machine_gauge. ",RW:" . number_format($row[csf('qnty')], 2, '.', ''));

		$pdf->SetXY($i, $j+18);
		$pdf->Write(0, $buyer_name . ",PO:" . $order_no);

		$pdf->SetXY($i, $j+22);
		$pdf->Write(0, "Clr:" .substr($color, 0, 35));

		$pdf->SetXY($i, $j+26);
		$pdf->Write(0, "Ct:".$yarn_count.",Lt:".$yarn_lot);

		$pdf->SetXY($i, $j+30);
		$pdf->Write(0, "Br:". $brand.",".$constuction);

		$pdf->SetXY($i, $j+34);
		$pdf->Write(0, substr($comp, 0, 45));

		$pdf->SetXY($i, $j+38);
		$pdf->Write(0, "G/F Dia:" . $grey_dia. "," . trim($finish_dia).",GSM:". $gsm.",SL:" . trim($stitch_length));

		$pdf->SetXY($i, $j+42);
		$pdf->Write(0, "RS:".$system_no. ",CRL No:" . $row[csf('roll_no')] .",ID:" .$user_arr[$row[csf('inserted_by')]]);

		$k++;
		$br++;
	}

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='splittingafterissue_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_one_128_v4")  // Barcode 128 v3 button production and split same, Tipu
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');

	$data = explode("***", $data);
	$userid=$_SESSION['logic_erp']['user_id'];
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=1", 'lot', 'brand');
	///print_r($brand_id_arr['6112018']);die;
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	$roll_id=sql_select("select roll_id, po_breakdown_id from pro_roll_details where id in($data[0])");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}

 	$system_no=return_field_value("system_number_prefix_num", "pro_roll_split", "roll_id IN(".implode(",", array_unique($roll_id_arr)).") and entry_form=113");

	$sql="SELECT a.recv_number,a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id, b.operator_name, b.body_part_id, b.shift_name, b.floor_id from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$body_part_id = '';
	$finish_dia = '';
	$shift_names = '';
	$recv_number = '';
	foreach ($result as $row) 
	{
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$receive_date=$row[csf('receive_date')];
		$recv_number=$row[csf('recv_number')];
		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		$shift_names = $shift_name[$row[csf('shift_name')]];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $row[csf('operator_name')];
		$body_part_id = $row[csf('body_part_id')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];

		$floor_name = return_field_value("floor_name", "lib_prod_floor", "id=" . $row[csf('floor_id')]);
		$brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val) {
			if ($val!="") $brand .= $brand_arr[$brand_id_arr[$val]] . ",";
		}
		$brand = chop($brand, ',');
		//$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) 
		{
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		$program_dye_type = '';
		if ($row[csf("receive_basis")] == 2) 
		{
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("SELECT a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg, b.batch_no, b.dye_type from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];
			//$program_batch_no = $planning_data[0][csf('batch_no')];
			$program_dye_type = $planning_data[0][csf('dye_type')];

			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
		} 
		else 
		{
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
		}

		/*if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);*/

		$determination_sql = sql_select("SELECT a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

		if ($determination_sql[0][csf('construction')] != "") {
			//$comp = $determination_sql[0][csf('construction')] . ", ";
			$constuction = $determination_sql[0][csf('construction')];
		}

		$booking_id=$row[csf('booking_id')];
		$y_requisition = sql_select("SELECT prod_id, no_of_cone, requisition_date, yarn_qnty from ppl_yarn_requisition_entry where knit_id=$booking_id and status_active = '1' and is_deleted = '0'");
		$y_prod_arr=array();
		foreach ($y_requisition as $value) 
		{
			$y_prod_arr[$value[csf('prod_id')]]=$value[csf('prod_id')];
		}
		$y_prod=implode(",", $y_prod_arr);
		$count_ids=implode(",", $count_id);

		$dataArray_prod = sql_select("SELECT lot, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color from product_details_master where status_active=1 and is_deleted=0 and id in($y_prod) and yarn_count_id in($count_ids) order by yarn_count_id");
		$comp = '';
		foreach ($dataArray_prod as $key => $rows) 
		{
			if ($rows[csf('yarn_comp_percent2nd')] != 0)
			{
				$comp = $composition[$rows[csf('yarn_comp_type1st')]]. "" . $composition[$rows[csf('yarn_comp_type2nd')]].',';
			}
			else
			{
				$comp .= $composition[$rows[csf('yarn_comp_type1st')]]. "" . $composition[$rows[csf('yarn_comp_type2nd')]].',';
			}
		}

		
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	$op_sql_data = sql_select("select id, first_name, id_card_no from lib_employee where id=$operator_name and status_active=1 and is_deleted=0");
	$op_data_arr=array();
	foreach ($op_sql_data as $row2) 
	{
		$op_data_arr[$row2[csf('id')]]['op_name']=$row2[csf('first_name')];
		$op_data_arr[$row2[csf('id')]]['op_card_no']=$row2[csf('id_card_no')];
	}

	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) 
	{
		if ($row[csf("receive_basis")] == 4) 
		{
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $row[csf("booking_id")] . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
		} 
		else 
		{
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
		}
	} 
	else 
	{
		$is_salesOrder = 0;
		if ($row[csf("receive_basis")] == 2) 
		{
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $row[csf("booking_id")]);
			//echo "SELECT is_sales FROM ppl_planning_info_entry_dtls WHERE id=" . $row[csf('booking_id')] . "";
		}
		if ($is_salesOrder == 1) 
		{
			$po_sql = sql_select("SELECT a.id, a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no, a.customer_buyer as buyer_id, a.sales_booking_no from fabric_sales_order_mst a where a.id in($order_id)");
			
			foreach ($po_sql as $row)
			{
				$po_array[$row[csf('id')]]['booking_no'] = $row[csf('sales_booking_no')];
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		}
	}
	// echo "<pre>";print_r($po_array);

	$coller_cuff_size_sql="SELECT c.coller_cuff_size, c.qc_pass_qnty_pcs, c.barcode_no
    from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.id in($data[0])";
    // echo $coller_cuff_size;
    $coller_cuff_size_res = sql_select($coller_cuff_size_sql);
    $coller_cuff_size_arr = array();
    foreach ($coller_cuff_size_res as $row)
	{
		$coller_cuff_size_arr[$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
		$coller_cuff_size_arr[$row[csf('barcode_no')]]['qc_pass_qnty_pcs'] = $row[csf('qc_pass_qnty_pcs')];
	}

	$body_part_type = return_field_value("body_part_type", "lib_body_part", "id=$body_part_id and body_part_type in(40,50) and status_active=1 and is_deleted=0" );

	$coller_cuff_query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size, b.fabric_grade from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.barcode_no in($data[2]) and A.ENTRY_FORM=2";
	$coller_cuff_data = sql_select($coller_cuff_query);
	$qc_pass_qnty_pcs="";$collerCuff_size="";
	foreach ($coller_cuff_data as $row) 
	{
		// $qc_pass_qnty_pcs=$row[csf('qnty_pcs')];
		$collerCuff_size=$row[csf('coller_cuff_size')];
	}

	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.inserted_by, a.insert_date, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade,c.shift_name,d.recv_number_prefix_num, d.recv_number, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no left join pro_grey_prod_entry_dtls c on a.dtls_id=c.id left join inv_receive_master d on c.mst_id=d.id where a.id in($data[0])";

	$res = sql_select($query);


	$pdf=new PDF_Code128('P','mm',array(59,44)); // Convert Pixel to Millimeter array(mm,mm)
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();
	$pdf->SetFont('Times','',8);


	$i=2; $j=1; $k=0; $br=0; $n=0;
	foreach ($res as $row) 
	{
		$order_no = $po_array[$order_id]['prefix'];
		$booking_no = $po_array[$order_id]['booking_no'];
		// $coller_cuff_size = $po_array[$row[csf('barcode_no')]]['coller_cuff_size'];
		// $qc_pass_qnty_pcs = $po_array[$row[csf('barcode_no')]]['qc_pass_qnty_pcs'];
		$recv_number2=explode("-", $recv_number);
		$recv_number3=$recv_number2[2]."-".$recv_number2[3];
		$insert_time = date('H:i', strtotime($row[csf('insert_date')]));

		$op_name=$op_data_arr[$operator_name]['op_name'];
		$op_card_no=$op_data_arr[$operator_name]['op_card_no'];

		$coller_cuff_size="";$qc_pass_qnty_pcs='';
		if ($body_part_type == 40)
		{
			$coller_cuff_size = $collerCuff_size;
			$qc_pass_qnty_pcs = $row[csf('qnty_pcs')];
		}
		elseif ($body_part_type == 50)
		{
			$coller_cuff_size =$collerCuff_size;
			$qc_pass_qnty_pcs =$row[csf('qnty_pcs')];
		}

		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=1; $k=0;
		}


		$pdf->Code128($i+1,$j,$row[csf("barcode_no")],50,8);
		$pdf->SetXY($i, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")]. ",Dt:".change_date_format($receive_date). ",S:".$shift_names.",".$company_short_name." ".$insert_time);

		$pdf->SetXY($i, $j+14);
		//." B: " . $po_array[$row[csf('po_breakdown_id')]]['booking_no']
		$pdf->Write(0, $machine_name . "," . $machine_dia_width . "X" . $machine_gauge. ",RW:" . number_format($row[csf('qnty')], 2, '.', '') . ",S-" . $coller_cuff_size. ",P-" . number_format($qc_pass_qnty_pcs));

		$pdf->SetXY($i, $j+18);
		$pdf->Write(0, $buyer_name . ",B/N:" . $booking_no . ",Pg:" .$program_no.",FSO:". $order_no);//24

		$pdf->SetXY($i, $j+22);
		$pdf->Write(0, "Clr:" .substr($color, 0, 35).",".$program_dye_type);

		$pdf->SetXY($i, $j+26);
		$pdf->Write(0, "Ct:".$yarn_count.",Lt:".$yarn_lot);

		$pdf->SetXY($i, $j+30);
		$pdf->Write(0, substr($comp, 0, 45));

		$pdf->SetXY($i, $j+34);
		$pdf->Write(0, $constuction.", Br:".$brand);

		$pdf->SetXY($i, $j+38);
		$pdf->Write(0, "F/D:" . trim($finish_dia).",". $tube_type.",GSM:". $gsm.",SL:" . trim($stitch_length));

		$pdf->SetXY($i, $j+41);
		$pdf->Write(0, "Op-". $op_name .",ID-" .$op_card_no.",P/ID-".$recv_number3.", P.F- ".$floor_name);

		$k++;
		$br++;
	}

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='splittingafterissue_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if($action=="report_barcode_text_file")
{
	//echo $data;

	$issueSql = sql_select("Select po_breakdown_id From pro_roll_details WHERE id=$data and status_active=1 and is_deleted=0 and entry_form = 62");

	$receiveByBatchOrderNo = "";
	foreach($issueSql as $row)
	{
		if($receiveByBatchOrderNo=="")
		{
			$receiveByBatchOrderNo .= $row[csf('po_breakdown_id')];
		}else{
			$receiveByBatchOrderNo .= ",".$row[csf('po_breakdown_id')];
		}
		
	}

	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$roll_id_sql=sql_select("select roll_id,po_breakdown_id  from pro_roll_details where id in($data)");
	$roll_id_arr=array();
	foreach($roll_id_sql as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		//$order_id_arr[]=$val[csf('po_breakdown_id')];
	}
	
	$mother_roll_sql=sql_select("select id,mst_id,dtls_id  from pro_roll_details where id in(".implode(",",$roll_id_arr).")");
	$roll_id_arr=array();
	foreach($mother_roll_sql as $val)
	{
		$mst_id=	$val[csf('mst_id')];
		$dtls_id=	$val[csf('dtls_id')];
		//$order_id_arr[]=$val[csf('po_breakdown_id')];
	}
	
	
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$sql_yarn_info=sql_select("select a.prod_id,b.brand,b.yarn_type,b.yarn_comp_type1st,b.yarn_comp_type2nd,b.yarn_comp_percent1st,b.yarn_comp_percent2nd,b.lot,b.yarn_count_id from pro_material_used_dtls a,product_details_master b  where a.prod_id=b.id and  a.mst_id=".$mst_id." and a.dtls_id=".$dtls_id." and a.entry_form=2 
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	
	$yarn_information_string=""; $all_yarn_type='';
	foreach($sql_yarn_info as $p_val)
	{
		$costing_yarn_composition='';
		$costing_yarn_band=$brand_arr[$p_val[csf('brand')]];
		$costing_yarn_lot=trim($p_val[csf('lot')]);
		$costing_yarn_count=$count_arr[$p_val[csf('yarn_count_id')]];
		$costing_yarn_composition=$composition[$p_val[csf('yarn_comp_type1st')]] . " " . $p_val[csf('yarn_comp_percent1st')] . "%";
		if ($p_val[csf('yarn_comp_type2nd')] != 0) $costing_yarn_composition .= " " . $composition[$p_val[csf('yarn_comp_type2nd')]] . " " . $p_val[csf('yarn_comp_percent2nd')] . "%";
		$yarn_information_string.=$costing_yarn_band." ".$costing_yarn_lot." ".$costing_yarn_count." ".$costing_yarn_composition. "\r\n";
		$all_yarn_type.=",".$yarn_type[$p_val[csf('yarn_type')]];
		
	}

	$sql = "select a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.shift_name, b.insert_date,b.operator_name, b.color_range_id, b.floor_id,b.body_part_id  from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$dtls_id";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$shiftName = '';
	$colorRange = '';
	$productionId = '';
	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$yarn_type_data = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);

		$booking_no = $row[csf('booking_no')];
		$booking_id = $row[csf('booking_id')];
		$operator_name = $operator_name_arr[$row[csf('operator_name')]];
		$floor_name = $floor_name_arr[$row[csf('floor_id')]];

		$booking_without_order = $row[csf('booking_without_order')];
		$productionId = $row[csf('recv_number')];

		//$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		//$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('location_id')]);
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$shiftName = $shift_name[$row[csf('shift_name')]];
		$colorRange = $color_range[$row[csf('color_range_id')]];

		//$color=$color_arr[$row[csf('color_id')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');
		if (trim($color) != "") {
			//$color=", ".$color;
			//$color="".$color;
		}

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];
			}
		}

		if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 4) {
			$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			//$machine_dia_width=$machine_data[0][csf('dia_width')];
			//$machine_gauge=$machine_data[0][csf('gauge')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
			$machine_brand = $row[csf('brand')];
			if($row[csf('receive_basis')]==1)
			{
				
				$sql_precost_tube=sql_select("select  b.width_dia_type,b.color_type_id from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.body_part_id=".$row[csf('body_part_id')]." and b.lib_yarn_count_deter_id=".$row[csf('febric_description_id')]."");
				foreach($sql_precost_tube as $t_val)
				{
					$tube_type = $fabric_typee[$t_val[csf('width_dia_type')]];
					$color_type_name = $color_type[$t_val[csf('color_type_id')]];
				}
			//	echo $sql_precost_tube;die;
				//$grey_dia = $program_data[0][csf('machine_dia')];
				//$tube_type = $fabric_typee[$program_data[0][csf('width_dia_type')]];
			}
		} 
		else if ($row[csf('receive_basis')] == 2) //Knitting Plan
		{
			$program_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg, b.machine_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$program_no = $row[csf('booking_id')];
			$grey_dia = $program_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$program_data[0][csf('width_dia_type')]];
			$machine_dia_width = $program_data[0][csf('machine_dia')];
			$machine_gauge = $program_data[0][csf('machine_gg')];
			//$machine_no_arr
			$machine_brand = $machine_brand_arr[$row[csf('machine_no_id')]];
			$machine_name = $machine_no_arr[$row[csf('machine_no_id')]];
			//$machine_name=explode(",",$program_data[0][csf('machine_id')]);
			$row[csf("within_group")] = $program_data[0][csf('within_group')];
		}

		//$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('location_id')]);
		//$buyer_name=return_field_value("short_name","lib_buyer", "id=".$row[csf('buyer_id')]);
		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id='" . $row[csf('buyer_id')] . "'");
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
				$construction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$composi .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
	}
	
	//echo "select a.job_no,a.job_no_prefix_num,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)";
	//echo $booking_without_order."Unable to open file!";die;
	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) {
		//echo $booking_without_order."Unable to open file!";die;
		//$booking_no_prefix=return_field_value("booking_no_prefix_num","wo_non_ord_samp_booking_mst", "booking_no='".$booking_no."'");
		if ($row[csf("receive_basis")] == 4) {

			$fb_sales_sql = "select id,job_no_prefix_num,job_no,style_ref_no,within_group from fabric_sales_order_mst where id = " . $row[csf('booking_id')];
			$fb_salesResult = sql_select($fb_sales_sql);
			$booking_no_prefix = $fb_salesResult[0][csf('job_no_prefix_num')];
			$full_booking_no = $fb_salesResult[0][csf('job_no')];
			$style_ref_no = $fb_salesResult[0][csf('style_ref_no')];
			$sales_id = $fb_salesResult[0][csf('id')];

			//$booking_no_prefix=return_field_value("job_no_prefix_num","fabric_sales_order_mst", "id='".$row[csf("booking_id")]."'");
			//$full_booking_no=return_field_value("job_no","fabric_sales_order_mst", "id='".$row[csf("booking_id")]."'");
			$no_arr = explode("-", $full_booking_no);
			array_shift($no_arr); //remove 1st index
			$full_booking_no = implode("-", $no_arr);
			//$style_ref_no=return_field_value("style_ref_no","fabric_sales_order_mst", "id='".$row[csf("booking_id")]."'");
			//$sales_id=return_field_value("id","fabric_sales_order_mst", "id='".$row[csf("booking_id")]."'");
			$po_array[$sales_id]['style_ref'] = $style_ref_no;

		} else {
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$full_booking_no = $booking_no;
			
			$sql_color_type=sql_select("select color_type_id from wo_non_ord_samp_booking_dtls where booking_no='".$booking_no."' and body_part=".$row[csf('body_part_id')]." and lib_yarn_count_deter_id =".$row[csf('febric_description_id')]." and status_active=1 and is_deleted=0  ");
			foreach($sql_color_type as $n_val)
			{
				$color_type_arr[]= $color_type[$n_val[csf('color_type_id')]];
			}
			$color_type_name=implode(",",array_unique($color_type_arr));
		}
	} else {
		//echo "Unable to open file2!";die;

		$is_salesOrder = 0;
		if ($row[csf("receive_basis")] == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id='" . $row[csf("booking_id")] . "'");
			$booking_no = return_field_value("b.booking_no as booking_no", "ppl_planning_info_entry_dtls a,ppl_planning_info_entry_mst b", " b.id=a.mst_id and a.id='" . $booking_id . "'", "booking_no");
		}

		if ($is_salesOrder == 1) {
			//echo "select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and id in($order_id)";
			if ($row[csf("within_group")] == 1) {
				$po_sql = sql_select("select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,b.buyer_id,a.within_group from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and a.id in($order_id)");
			} else {
				$po_sql = sql_select("select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,a.buyer_id,a.within_group from fabric_sales_order_mst a where a.id in($order_id)");
			}
			foreach ($po_sql as $row) {
				$no_arr = explode("-", $row[csf('job_no')]);
				array_shift($no_arr); //remove 1st index
				$full_booking_no = implode("-", $no_arr);

				$po_no_arr = explode("-", $row[csf('po_number')]);
				array_shift($po_no_arr); //remove 1st index
				$po_no_arr = implode("-", $po_no_arr);
				$po_array[$row[csf('id')]]['no'] = $po_no_arr;//$row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $full_booking_no;
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_id')];
			}

			//print_r($po_array);

		} else {
			$order_id=chop($order_id,",");

			if($receiveByBatchOrderNo!="")
			{
				$order_id = $order_id.",".$receiveByBatchOrderNo;
			}
			
			$po_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)"); //$order_id

			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
			}
		}
	}


	$user_id=$_SESSION['logic_erp']['user_id'];
	$within_group = $row[csf("within_group")];
	foreach (glob('norsel_bundle_'.$userid."*.zip") as $filename) {
		 @unlink($filename);
	}
	//echo $within_group;die;
	//exit;
	$i = 1;
	$zip = new ZipArchive();            // Load zip library
	//$filename = str_replace(".sql", ".zip", "files/".$_SESSION['logic_erp']['user_id']."/norsel_bundle.sql");            // Zip name
	$filename = str_replace(".sql",".zip",'norsel_bundle_'.$userid.".sql");			// Zip name
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {        // Opening zip file to load files
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}

	$i = 1;
	$year = date("y");
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty,a.reject_qnty, b.batch_wgt from pro_roll_details a left join pro_grey_batch_dtls b on a.id = b.roll_id where a.id in($data) order by a.barcode_no asc";
	//echo $query;die;
	$res = sql_select($query);
	$split_data_arr = array();
	$created_files=array();
	
	//if (!file_exists("files/".$_SESSION['logic_erp']['user_id']."/")) {
	//	mkdir("files/".$_SESSION['logic_erp']['user_id']."/", 0777, true);
	//}

	foreach ($res as $row) {
		$split_roll_id = $row[csf('id')];
	//	$roll_split_query = sql_select("select a.barcode_no, a.qnty, a.id, a.roll_split_from from pro_roll_details a where a.roll_id = $split_roll_id and a.roll_split_from != 0");
		$file_name = "NORSEL-IMPORT_".$userid."_" . $i;
		$created_files[]=$file_name.".txt";
		$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");
		$txt = "Norsel_imp\r\n1\r\n";
		if ($booking_without_order == 1) {
			$txt .= $party_name . "\r\n";
			$txt .= $booking_no_prefix . "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";
			$full_job_no = $full_booking_no;
			//$txt .=$party_name." Booking No.".$booking_no_prefix." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
		} else {
			$txt .= $party_name . "\r\n";
			$txt .= $po_array[$row[csf('po_breakdown_id')]]['prefix'] . "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";
			$full_job_no = $po_array[$row[csf('po_breakdown_id')]]['job_no'];
			//$txt .=$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
			//$txt .=$party_name." Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix']." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";


		}
		//print_r($roll_split_query);
		/*if (!empty($roll_split_query)) {
			$qnty = number_format($roll_split_query[0]['qnty'], 2, '.', '');
			$barcode = $roll_split_query[0]['barcode_no'];
		} else {*/
			$qnty = number_format($row[csf('QNTY')], 2, '.', '');
			$barcode = $row[csf('barcode_no')];
		//}
		$txt .= $barcode . "\r\n";
		//$txt .="Barcode No: ".$row[csf('barcode_no')]."\r\n";
		$txt .= $barcode . "\r\n";
		$txt .= "" . $prod_date . "\r\n";
		$txt .= $buyer_name . "\r\n";
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['no'] . "\r\n";
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['file_no'] . "\r\n";
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['grouping'] . "\r\n";
		$txt .= $comp . "\r\n";
		$dia_length_tube=trim($grey_dia);
		if($dia_length_tube!="") $dia_length_tube.="/";
		$dia_length_tube.=trim($finish_dia) . " " . trim($stitch_length) . " " . trim($tube_type) . "\r\n";
		//$txt .= "" . trim($grey_dia) . "/" . trim($finish_dia) . " " . trim($stitch_length) . " " . trim($tube_type) . "\r\n";
		$txt.=$dia_length_tube;
		$txt .= "" . $gsm . "\r\n";
		$txt .= $yarn_count . "\r\n";//.$brand." Lot:".$yarn_lot."\r\n";
		$txt .= $brand . "\r\n";
		$txt .= $yarn_lot . "\r\n";
		$txt .= "" . $program_no . "\r\n";
		$txt .= $qnty . "Kg\r\n";
		$txt .= $shiftName . "\r\n";
		$txt .= "" . $row[csf('roll_no')] . "\r\n";
		$txt .= trim($color) . "\r\n";
		$txt .= "" . trim($colorRange) . "\r\n";
		$txt .= "" . $po_array[$row[csf('po_breakdown_id')]]['style_ref'] . "\r\n";
		//if ($booking_without_order != 1) {
		$txt .= "" . $booking_no . "\r\n";
		//} else
			//$txt .= " \r\n";
		$txt .= "" . $operator_name . "\r\n";
		$txt .= "" . $productionId . "\r\n";
		if ($within_group == 1) {
			$txt .= "" . return_field_value("short_name", "lib_buyer", "id=" . $po_array[$row[csf('po_breakdown_id')]]['buyer_name']) . "\r\n";
		} else {
			$txt .= $buyer_name . "\r\n";
		}
		$txt .= "" . $machine_brand . "\r\n";
		$txt .= "" . $yarn_type[$yarn_type_data] . "\r\n";

		$txt .= "" . $construction . "\r\n";
		$txt .= "" . $composi . "\r\n";
		$txt .= "" . $floor_name . "\r\n";
		$txt .= $machine_name . "\r\n";
		$txt .= $machine_dia_width . "X" . $machine_gauge . "\r\n";
		$txt .= $full_job_no . "\r\n";
		$dia_tube=trim($grey_dia);
		if($dia_tube!="") $dia_tube.="/";
		$dia_tube.=trim($finish_dia) ." ". trim($tube_type) . "\r\n";
		$txt.=$dia_tube;
		//$txt .= "" . trim($grey_dia) . "/" . trim($finish_dia) . " " . trim($tube_type) . "\r\n";
		$txt .= trim($stitch_length) . "\r\n";
		$txt .= "Rej. Qty.:" . $row[csf('reject_qnty')] . "\r\n";
		$txt.=$yarn_information_string;
		$txt.=ltrim($all_yarn_type,","). "\r\n";
		$txt .=$location_name. "\r\n";
		$txt .=$color_type_name. "\r\n";
		fwrite($myfile, $txt);
		fclose($myfile);

		$i++;
	}
	//echo $txt;die;  $file_name = "NORSEL-IMPORT_".$userid."_" . $i;
	foreach (glob("NORSEL-IMPORT_".$userid."*.txt") as $filenames)
	{			
		$zip->addFile($file_folder.$filenames);		
	}
	$zip->close();

	foreach (glob("NORSEL-IMPORT_".$userid."*.txt") as $filename) 
	{			
		@unlink($filename);
	}

	echo 'norsel_bundle_'.$userid; //str_replace(".zip","",$file_folder);//"norsel_bundle";
	exit();
}

if($action=="check_barcode_no")
{
	$child_split =sql_select("select c.barcode_no as child_barcode, b.barcode_no as mother_barcode from pro_roll_details c, pro_roll_details b where c.roll_split_from=b.id and c.entry_form=62 and c.status_active=1 and c.is_deleted=0  and c.barcode_no=".$data);

	$sql="select barcode_no from pro_roll_split where entry_form=75 and status_active=1 and is_deleted=0 and barcode_no=".$data."";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo 1;die;
	}
	else if(!empty($child_split))
	{
		echo "3_".$child_split[0][csf("mother_barcode")];die;
	}
	else
	{
		//$barcodeData=sql_select( "select barcode_no from pro_roll_details where  entry_form=64 and  barcode_no=".$data." and  status_active=1 and is_deleted=0");
		$barcodeData=sql_select( "SELECT a.barcode_no, b.batch_no, b.extention_no from pro_roll_details a, pro_batch_create_mst b where a.mst_id=b.id and b.entry_form=0 and  a.entry_form=64 and a.barcode_no=".$data." and  a.status_active=1 and a.is_deleted=0");
		if(count($barcodeData)>0)
		{
			$batch_number .= "Batch No: ".$barcodeData[0][csf("batch_no")];
			if($barcodeData[0][csf("extention_no")]){
				$batch_number .= ", Extention no: ".$barcodeData[0][csf("extention_no")];
			}
			echo "0_".$batch_number;die;
		}
		else
		{
			echo 2;die;
		}
	}
	exit();	
}

if ($action == "direct_print_barcode_6")
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');

	$data = explode("***", $data);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');


	$roll_id=sql_select("select roll_id, po_breakdown_id from pro_roll_details where id in($data[0])");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}

	$sql = "SELECT a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name, b.shift_name, b.body_part_id, c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';$yarn_type_cond = '';
	//$yarn_type = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$operator_name = '';
	foreach ($result as $row) {
		$company_name = return_field_value("company_name", "lib_company", "id=" . $row[csf('company_id')]);

		if ($row[csf('knitting_source')] == 1) {
			$party_name_full = return_field_value("company_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name_full = return_field_value("supplier_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$booking_id=$row[csf("booking_id")];
		$recieve_basis=$row[csf("receive_basis")];
		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		$insert_time = date("H:i", strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		//$order_id = $row[csf('order_id')];
		$order_id .= $row[csf('po_breakdown_id')].",";
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];

		$operator_name = $operator_name_arr[$row[csf('operator_name')]];
		$shift_name_id = $row[csf('shift_name')];
		$body_part_id = $row[csf('body_part_id')];

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		//$yarn_type = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];

			}
		}

		if ($row[csf("receive_basis")] == 2) {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg,a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];
			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$bookingNo = $planning_data[0][csf('booking_no')];
		} else {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

			if ($row[csf("within_group")] == 1)
			$buyer_name_full = return_field_value("company_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name_full = return_field_value("buyer_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$comp = '';$yarn_type_cond = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
			//$yarn_typeId = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id,b.type_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

				$yarn_type_cond=$yarn_type[$yarn_typeId];
			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
	}

	// yarn Type start booking_id

	if ($recieve_basis == 1) {
		if ($booking_without_order == 0) {
			$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_id=$booking_id and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by  c.yarn_type";
		} else {
			$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_no='$booking_no' and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by c.yarn_type";
		}
	} else if ($recieve_basis == 2) {
		$reqsition_sql = sql_select("select  requisition_no from ppl_yarn_requisition_entry where knit_id='$booking_id'");
		$reqsition_number = "";
		foreach ($reqsition_sql as $inf) {
			if (trim($reqsition_number) != "") {
				$reqsition_number .= ",'" . $inf[csf('requisition_no')] . "'";
			} else {
				$reqsition_number = "'" . $inf[csf('requisition_no')] . "'";
			}
		}

		$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.requisition_no in($reqsition_number) and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by  c.yarn_type";
	}
	$sql_yarn_result = sql_select($sql_yarn);
	//echo $sql_yarn;
	$yarn_typeCond="";
	foreach ($sql_yarn_result as $row)
	{
		$yarn_typeCond.=$yarn_type[$row[csf('yarn_type')]].',';
	}
	$yarntypeCond=rtrim($yarn_typeCond,',');
	$yarn_type_cond=implode(",",array_unique(explode(",",$yarntypeCond)));

	$order_id=implode(",",array_unique(explode(",",chop($order_id,","))));

	//echo $yarn_type_cond.'SS';
	//Yarn Type
	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) {
		if ($row[csf("receive_basis")] == 4) {
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $row[csf("booking_id")] . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
			$bookingNo = $sales_info[0][csf('sales_booking_no')];
		} else {
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$non_internal_ref= return_field_value("grouping", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$bookingNo = $booking_no;
		}
	} else {
		$is_salesOrder = 0;
		if ($recieve_basis == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $booking_id);
		}
		if ($is_salesOrder == 1) {
			$po_sql = sql_select("select a.id, a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no, customer_buyer from fabric_sales_order_mst a where a.id in ($order_id) ");

			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no')]; //CRM ID: 22402
				$bookingNo = $row[csf('sales_booking_no')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('customer_buyer')]);
			}
		} else {
			$po_sql = sql_select("select a.job_no, a.job_no_prefix_num, b.id,b.grouping, b.po_number from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.id in($order_id) group by a.job_no,a.job_no_prefix_num,b.id,b.grouping,b.po_number");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
			}
			//$order_no = $po_array[$order_id]['no'];
		}
	}

	$body_part_type = return_field_value("body_part_type", "lib_body_part", "id=$body_part_id and body_part_type in(40,50) and status_active=1 and is_deleted=0" );

	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0])";
	$res = sql_select($query);
	$pdf=new PDF_Code128('P','mm', array(65,55));
	$pdf->AddPage();
	$pdf->SetFont('Calibri','',12);

	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);

	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row) {


		$operatorName = substr($operator_name, 0, 13);

		if ($is_salesOrder == 1) {
			$poNO=$po_array[$row[csf('po_breakdown_id')]]['job_no'];;
			$order_no = $bookingNo;
		}
		else
		{
			$poNO=$po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		}



		if ($row[csf("receive_basis")] == 1 && $booking_without_order=1)
		{
			$internal_ref=$non_internal_ref;
		}
		else
		{
			if($po_array[$row[csf('po_breakdown_id')]]['grouping']!="") $internal_ref=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
			else $internal_ref="";
		}

		$qnty_pcs='';$coller_cuff_size="";
		if ($body_part_type == 40 || $body_part_type == 50)
		{
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
			$coller_cuff_size = "SZ: " . $row[csf('coller_cuff_size')];
		}

		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}


		$bookingNoArr=explode("-", $bookingNo);
		$bookingNos=$bookingNoArr[1]."-".$bookingNoArr[2]."-".$bookingNoArr[3];


		if ($operatorName != "") $operatorName = "; OP : " . $operatorName;

		$pdf->SetFont('Calibri','',11);
		$pdf->SetXY($i, $j);
		$pdf->Write(0, "" . substr($company_name,0,45) );

		$pdf->SetFont('Calibri','',10);

		$pdf->SetXY($i, $j+3.5);
		$pdf->Write(0, "D:" . $prod_date ."; B:" . $buyer_name);

		$pdf->SetFont('Calibri','',8);

		$pdf->SetXY($i, $j+6.5);
		$pdf->Write(0, "Tx Ref.:" . $poNO."; Sft:".$shift_name[$shift_name_id]."; Tm:".$insert_time);

		$pdf->SetXY($i, $j+9.5);
		$pdf->Write(0, "" . substr($party_name_full,0,45) );

		$pdf->SetXY($i, $j+12.5);
		$pdf->Write(0, substr($comp,0,45) );

		$pdf->SetFont('Calibri','',9);

		$pdf->SetXY($i, $j+16);
		$pdf->Write(0, "F/GSM: " . $gsm.";Clr: " .substr($color, 0, 25));

		$pdf->SetXY($i, $j+19);
		$pdf->Write(0, "C:".$yarn_count . "; L: " . $yarn_lot);
		//$pdf->Write(0, "C: " . substr($gsm, 0, 25) .";Clr: " .substr($color, 0, 25));

		$pdf->SetFont('Calibri','',8);
		$pdf->SetXY($i, $j+22.5);
		$pdf->Write(0, "Br: " .  $brand .";T: " . $yarn_type_cond);

		$pdf->SetFont('Calibri','',9);

		$pdf->SetXY($i, $j+25.5);
		$pdf->Write(0, "M/C: " . $machine_name . "; DiaXGG-" . $machine_dia_width."X".$machine_gauge . "; B-" . $bookingNos);//24


		$pdf->SetXY($i, $j+28.5);
		$pdf->Write(0, "F/Dia: " . trim($finish_dia).";D/Type: " .trim($tube_type));

		$pdf->SetXY($i, $j+31.5);
		$pdf->Write(0, "SL: " . trim($stitch_length).";Prg: " .$program_no . $operatorName);


		$pdf->SetXY($i, $j+34.5);
		$pdf->Write(0, "Roll No: " . $row[csf('roll_no')] ."; Roll Wt: " . number_format($row[csf('qnty')], 2, '.', ''). " Kg");

		$pdf->SetFont('Calibri','',10);
		$pdf->SetXY($i, $j+37.5);
		$pdf->Write(0, $row[csf("barcode_no")]."; ".$qnty_pcs."; ".$coller_cuff_size);


		$pdf->Code128($i+1,$j+40.5,$row[csf("barcode_no")],50,8);

		$k++;
		$br++;
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_128_v3_nz")
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');

	$data = explode("***", $data);
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');


	$roll_id=sql_select("select roll_id, po_breakdown_id from pro_roll_details where id in($data[0])");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}

	$sql = "SELECT a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name, b.shift_name, b.body_part_id, c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';$yarn_type_cond = '';
	//$yarn_type = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	$operator_name = '';
	foreach ($result as $row) {
		$company_name = return_field_value("company_name", "lib_company", "id=" . $row[csf('company_id')]);

		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		if ($row[csf('knitting_source')] == 1) {
			$party_name_full = return_field_value("company_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name_full = return_field_value("supplier_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$booking_id=$row[csf("booking_id")];
		$recieve_basis=$row[csf("receive_basis")];
		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		$insert_time = date("H:i", strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		//$order_id = $row[csf('order_id')];
		$order_id .= $row[csf('po_breakdown_id')].",";
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];

		$operator_name = $operator_name_arr[$row[csf('operator_name')]];
		$shift_name_id = $row[csf('shift_name')];
		$body_part_id = $row[csf('body_part_id')];

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];
		$brand = $brand_arr[$row[csf('brand_id')]];
		$yarn_count = '';
		//$yarn_type = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];

			}
		}

		if ($row[csf("receive_basis")] == 2) {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg,a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];
			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$bookingNo = $planning_data[0][csf('booking_no')];
		} else {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

			if ($row[csf("within_group")] == 1)
			$buyer_name_full = return_field_value("company_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name_full = return_field_value("buyer_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$comp = '';$yarn_type_cond = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
			//$yarn_typeId = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id,b.type_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

				$yarn_type_cond=$yarn_type[$yarn_typeId];
			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
	}

	// yarn Type start booking_id

	if ($recieve_basis == 1) {
		if ($booking_without_order == 0) {
			$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_id=$booking_id and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by  c.yarn_type";
		} else {
			$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_no='$booking_no' and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by c.yarn_type";
		}
	} else if ($recieve_basis == 2) {
		$reqsition_sql = sql_select("select  requisition_no from ppl_yarn_requisition_entry where knit_id='$booking_id'");
		$reqsition_number = "";
		foreach ($reqsition_sql as $inf) {
			if (trim($reqsition_number) != "") {
				$reqsition_number .= ",'" . $inf[csf('requisition_no')] . "'";
			} else {
				$reqsition_number = "'" . $inf[csf('requisition_no')] . "'";
			}
		}

		$sql_yarn = "select  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.requisition_no in($reqsition_number) and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by  c.yarn_type";
	}
	$sql_yarn_result = sql_select($sql_yarn);
	//echo $sql_yarn;
	$yarn_typeCond="";
	foreach ($sql_yarn_result as $row)
	{
		$yarn_typeCond.=$yarn_type[$row[csf('yarn_type')]].',';
	}
	$yarntypeCond=rtrim($yarn_typeCond,',');
	$yarn_type_cond=implode(",",array_unique(explode(",",$yarntypeCond)));

	$order_id=implode(",",array_unique(explode(",",chop($order_id,","))));

	//echo $yarn_type_cond.'SS';
	//Yarn Type
	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) {
		if ($recieve_basis == 4) {
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $booking_id . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
			$bookingNo = $sales_info[0][csf('sales_booking_no')];
		} else {
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$non_internal_ref= return_field_value("grouping", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$bookingNo = $booking_no;
		}
	} else {
		$is_salesOrder = 0;
		if ($recieve_basis == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $booking_id);
		}
		if ($is_salesOrder == 1) {
			$po_sql = sql_select("SELECT a.id, a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no, customer_buyer, a.style_ref_no from fabric_sales_order_mst a where a.id in ($order_id) ");

			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no')]; //CRM ID: 22402
				$po_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$bookingNo = $row[csf('sales_booking_no')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('customer_buyer')]);
			}
		} else {
			$po_sql = sql_select("SELECT a.job_no, a.job_no_prefix_num, b.id,b.grouping, b.po_number, a.style_ref_no, from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.id in($order_id) group by a.job_no,a.job_no_prefix_num,b.id,b.grouping,b.po_number");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				$po_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			}
			//$order_no = $po_array[$order_id]['no'];
		}
	}

	$body_part_type = return_field_value("body_part_type", "lib_body_part", "id=$body_part_id and body_part_type in(40,50) and status_active=1 and is_deleted=0" );

	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size, a.insert_date, a.inserted_by from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0])";
	$res = sql_select($query);

	$pdf=new PDF_Code128('P','mm', array(65,50));
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);

	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);

	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row) {


		$operatorName = substr($operator_name, 0, 13);

		if ($is_salesOrder == 1) {
			$poNO=$po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$styleRefNo = $po_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
			$order_no = $bookingNo;
		}
		else
		{
			$poNO=$po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		}



		if ($recieve_basis == 1 && $booking_without_order=1)
		{
			$internal_ref=$non_internal_ref;
		}
		else
		{
			if($po_array[$row[csf('po_breakdown_id')]]['grouping']!="") $internal_ref=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
			else $internal_ref="";
		}

		$qnty_pcs='';$coller_cuff_size="";
		if ($body_part_type == 40 || $body_part_type == 50)
		{
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
			$coller_cuff_size = "SZ: " . $row[csf('coller_cuff_size')];
		}

		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}


		$bookingNoArr=explode("-", $bookingNo);
		$bookingNos=$bookingNoArr[1]."-".$bookingNoArr[2]."-".$bookingNoArr[3];


		//$time = date("h:i:a");
		$insert_time = date("H:i:a", strtotime($row[csf('insert_date')]));
		if ($operator_name != "") $operator_names = ";OP:" . $operator_name;

		$pdf->SetXY($i, $j);
		$pdf->Write(0, substr($party_name,0,45)."; P:" . $program_no."; ".change_date_format($row[csf('insert_date')])."; Sh:".$shift_name[$shift_name_id] );
		
		$pdf->SetXY($i, $j+3.5);
		$pdf->Write(0, "M/C: " . $machine_name ."; " . $machine_dia_width . "X" . $machine_gauge."; " . trim($finish_dia)."; " .trim($tube_type));

		$pdf->SetXY($i, $j+6.5);
		$pdf->Write(0, 'Time : '.$insert_time."; User Name:" . substr($user_arr[$row[csf('inserted_by')]],0,35));

		$pdf->SetXY($i, $j+9.5);
		$pdf->Code128($i+1,$j+9.5,$row[csf("barcode_no")],50,7);

		$pdf->SetXY($i, $j+19);
		$pdf->Write(0, "Roll: " . $row[csf('roll_no')] ."; W: " . number_format($row[csf('qnty')], 2, '.', '')."kg; ".$row[csf("barcode_no")]);

		$pdf->SetXY($i, $j+22.5);
		$pdf->Write(0, $bookingNo .";B: " . substr($buyer_name, 0, 25));

		$pdf->SetXY($i, $j+25.5);
		$pdf->Write(0, "Style: " . substr($styleRefNo, 0, 35));

		$pdf->SetXY($i, $j+28.5);
		$pdf->Write(0, "GSM: " . $gsm.";CLR: " .substr($color, 0, 25));

		$pdf->SetXY($i, $j+31.5);
		$pdf->Write(0, "S.L: " . trim($stitch_length));

		$pdf->SetXY($i, $j+34.5);
		$pdf->Write(0, substr($comp,0,45));

		$pdf->SetXY($i, $j+37.5);
		$pdf->Write(0, "Y/C:".$yarn_count . "; Y/L: " . $yarn_lot);

		$pdf->SetXY($i, $j+40.5);
		$pdf->Write(0, "Y/T:".$yarn_type_cond . "; Y/B: " . $brand);

		$k++;
		$br++;
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_ccl")
{
	require('../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=1", 'lot', 'brand');

	$roll_id=sql_select("select roll_id, po_breakdown_id from pro_roll_details where id in($data[0])");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}


	$sql = "SELECT a.id as rcv_id,a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name, b.shift_name, b.body_part_id, c.po_breakdown_id, b.id as dtls_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$tube_type = '';
	$program_no = '';
	$booking_no = '';
	$booking_without_order = '';
	//$yarn_lot = '';
	$yarn_count = '';$yarn_type_cond = '';
	//$yarn_type = '';
	//$brand = '';
	$gsm = '';
	$finish_dia = '';
	$operator_name = '';
	$color_range_name = '';
	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		if ($row[csf('knitting_source')] == 1) {
			$party_name_full = return_field_value("company_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name_full = return_field_value("supplier_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}
		$booking_id=$row[csf("booking_id")];
		$recieve_basis=$row[csf("receive_basis")];
		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		$insert_time = date("H:i", strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$prod_time = date("h:i:a", strtotime($row[csf('insert_date')]));

		//$order_id = $row[csf('order_id')];
		$order_id .= $row[csf('po_breakdown_id')].",";
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$color_range_name = $color_range[$row[csf('color_range_id')]];


		$operator_name = $operator_name_arr[$row[csf('operator_name')]];
		$operator_id = $row[csf('operator_name')];
		$shift_name_id = $row[csf('shift_name')];
		$body_part_id = $row[csf('body_part_id')];

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		//$yarn_lot = $row[csf('yarn_lot')];
		/* $brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val) {
			if ($val!="") $brand .= $brand_arr[$brand_id_arr[$val]] . ",";
		}
		$brand = chop($brand, ','); */

		//$brand = $brand_arr[$row[csf('brand_id')]];

		$yarn_count = '';
		//$yarn_type = '';
		$count_id = explode(",", $row[csf('yarn_count')]);
		foreach ($count_id as $val) {
			if ($val > 0) {
				if ($yarn_count == "") $yarn_count = $count_arr[$val]; else $yarn_count .= "," . $count_arr[$val];

			}
		}

		if ($row[csf("receive_basis")] == 2) {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("select a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg,a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];
			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$bookingNo = $planning_data[0][csf('booking_no')];
		} else {
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

			if ($row[csf("within_group")] == 1)
			$buyer_name_full = return_field_value("company_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name_full = return_field_value("buyer_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


			$cons = '';$comp = '';$yarn_type_cond = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
			//$yarn_typeId = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {

			$lib_fabric_composition=return_library_array( "select id,fabric_composition_name from lib_fabric_composition where status_active=1", "id", "fabric_composition_name");

			// echo "select a.construction, b.copmposition_id,b.type_id, b.percent, a.fabric_composition_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')];
			$determination_sql = sql_select("select a.construction, b.copmposition_id,b.type_id, b.percent, a.fabric_composition_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);
			//$yarn_typeId = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
			//echo "select yarn_type from product_details_master where  id=".$row[csf('prod_id')]." ";
			//echo "select a.construction, b.copmposition_id,b.type_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')];
			//echo $yarn_typeId.'DD';
				$yarn_type_cond=$yarn_type[$yarn_typeId];
			if ($determination_sql[0][csf('construction')] != "") {
				$cons = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				//$comp = $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$comp = $lib_fabric_composition[$d_row[csf("fabric_composition_id")]];

				//$yarn_type_cond .= $yarn_type[$d_row[csf('type_id')]];
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);


	}

	if ($result[0][csf("dtls_id")] != "") {

		$rcv_id = $result[0][csf("rcv_id")];
		$update_sql = sql_select("select id,prod_id,used_qty,rate,amount,yarn_percentage,porcess_loss from pro_material_used_dtls where mst_id=$rcv_id and dtls_id =".$result[0][csf('dtls_id')]);
		$update_data_arr = array();
		foreach ($update_sql as $val) {

			$check_arr[] = $val[csf('prod_id')];
		}
	}

	// yarn Type start booking_id

	if ($recieve_basis == 1) {
		if ($booking_without_order == 0) {
			$sql_yarn = "SELECT c.id, c.brand, c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_id=$booking_id and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by c.id, c.brand, c.yarn_type";
		} else {
			$sql_yarn = "SELECT c.id, c.brand, c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_no='$booking_no' and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by c.id, c.brand,c.yarn_type";
		}
	} else if ($recieve_basis == 2) {
		$reqsition_sql = sql_select("select  requisition_no from ppl_yarn_requisition_entry where knit_id='$booking_id'");
		$reqsition_number = "";
		foreach ($reqsition_sql as $inf) {
			if (trim($reqsition_number) != "") {
				$reqsition_number .= ",'" . $inf[csf('requisition_no')] . "'";
			} else {
				$reqsition_number = "'" . $inf[csf('requisition_no')] . "'";
			}
		}

		$sql_yarn = "SELECT c.id, c.brand,c.yarn_type, c.lot from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.requisition_no in($reqsition_number) and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 group by c.id, c.brand, c.yarn_type, c.lot order by c.id";
	}
	//echo $sql_yarn;
	$sql_yarn_result = sql_select($sql_yarn);
	
	$yarn_typeCond="";
	$brand='';
	foreach ($sql_yarn_result as $row)
	{
		$yarn_typeCond.=$yarn_type[$row[csf('yarn_type')]].',';

		if (in_array($row[csf("id")], $check_arr))
		{
			if ($row!="") 
			{
				$brand .= $brand_arr[$row[csf('brand')]] . ",";
				$yarn_lot .= $row[csf('lot')] . ",";
			}
			
		}
	}
	$brand = implode(",",array_unique(explode(",",chop($brand, ','))));
	$yarn_lot = implode(",",array_unique(explode(",",chop($yarn_lot, ','))));

	$yarntypeCond=rtrim($yarn_typeCond,',');
	$yarn_type_cond=implode(",",array_unique(explode(",",$yarntypeCond)));

	$order_id=implode(",",array_unique(explode(",",chop($order_id,","))));

	//echo $yarn_type_cond.'SS';
	//Yarn Type
	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order == 1) {
		if ($row[csf("receive_basis")] == 4) {
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $row[csf("booking_id")] . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
			$bookingNo = $sales_info[0][csf('sales_booking_no')];
		} else {
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$non_internal_ref= return_field_value("grouping", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$bookingNo = $booking_no;
		}
	} else {
		$is_salesOrder = 0;
		if ($recieve_basis == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $booking_id);
		}
		if ($is_salesOrder == 1) {


			$po_sql = sql_select("select a.id, a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,a.style_ref_no, customer_buyer,a.booking_id from fabric_sales_order_mst a where a.id in ($order_id) ");
			$booking_id_arr = array();
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$bookingNo = $row[csf('sales_booking_no')];
				$bookingId = $row[csf('booking_id')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('customer_buyer')]);

				array_push($booking_id_arr,$row[csf('booking_id')]);

			}

			$booking_sql_rslt = sql_select("select a.booking_mst_id, a.po_break_down_id,b.grouping from wo_booking_dtls a, wo_po_break_down b where a.po_break_down_id = b.id ".where_con_using_array($booking_id_arr,0,'a.booking_mst_id')." group by a.booking_mst_id, a.po_break_down_id,b.grouping");
			foreach ($booking_sql_rslt as $row)
			{
				$booking_po_array[$row[csf('booking_mst_id')]]['grouping'] = $row[csf('grouping')];
			}

		} else {
			$po_sql = sql_select("select a.job_no, a.job_no_prefix_num, b.id,b.grouping, b.po_number,a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id) group by a.job_no,a.job_no_prefix_num,b.id,b.grouping,b.po_number");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
				$po_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			}
			//$order_no = $po_array[$order_id]['no'];
		}
	}

	$body_part_type = return_field_value("body_part_type", "lib_body_part", "id=$body_part_id and body_part_type in(40,50) and status_active=1 and is_deleted=0" );

	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0])";
	$res = sql_select($query);
	$pdf=new PDF_Code128('P','mm', array(60,50));
	//$pdf=new PDF_Code128('L','mm', array(50,62));
	//$pdf=new PDF_Code128('L','mm', array(60,62));
	$pdf->AddPage();
	$pdf->SetFont('Arial','',10);

	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);

	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row) {


		$operatorName = $operator_name;

		if ($is_salesOrder == 1) {
			$poNO=$po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$order_no = $bookingNo;
		}
		else
		{
			$poNO=$po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		}

		$style_ref_no=$po_array[$row[csf('po_breakdown_id')]]['style_ref_no'];

		if ($row[csf("receive_basis")] == 1 && $booking_without_order=1)
		{
			$internal_ref=$non_internal_ref;
		}
		else
		{
			if ($is_salesOrder == 1)
			{
				$internal_ref=$booking_po_array[$bookingId]['grouping'];
			}
			else
			{
				if($po_array[$row[csf('po_breakdown_id')]]['grouping']!="") $internal_ref=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
				else $internal_ref="";
			}
		}

		$qnty_pcs='';$coller_cuff_size="";
		if ($body_part_type == 40 || $body_part_type == 50)
		{
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
			$coller_cuff_size = "SZ: " . $row[csf('coller_cuff_size')];
		}

		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}


		$bookingNoArr=explode("-", $bookingNo);
		$bookingNos=$bookingNoArr[1]."-".$bookingNoArr[2]."-".$bookingNoArr[3];

		$time = date("h:i:a");

		//if ($operatorName != "") $operatorName_id = $operatorName.'['.$operator_id.']';


		//$pdf->SetFont('Calibri','',11);
		$pdf->SetFont('Arial','B',10);
		$pdf->Code128($i+5,$j,$row[csf("barcode_no")],50,8);

		$pdf->SetFont('Arial','B',10);
		$pdf->SetXY($i+5, $j+11.2);
		$pdf->Write(0, $row[csf("barcode_no")].' Time : '.$time);

		$pdf->SetXY($i, $j+15.2);
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, "Dt: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, $prod_date );
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, ", M/N: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($machine_name, 0, 15) );
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, ", Shift: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($shift_name[$shift_name_id], 0, 10) );
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, ", KC: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($party_name, 0, 15) );

		$pdf->SetXY($i, $j+19.2);
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, "Buyer: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($buyer_name, 0, 8) );
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, ", Int.B: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($internal_ref, 0, 15));
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, ", P: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, $program_no);

		$pdf->SetXY($i, $j+23.2);
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($cons, 0, 20).', '.$machine_dia_width."X".$machine_gauge.', '.trim($finish_dia).', '.$gsm.', '.trim($tube_type)  );

		$pdf->SetXY($i, $j+27.2);
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, "F.Comp: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($comp, 0, 35));

		$pdf->SetXY($i, $j+31.2);
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($color_range_name, 0, 25) );
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, ", YT: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($yarn_type_cond, 0, 25) );

		$pdf->SetXY($i, $j+35.2);
		$pdf->SetFont('Arial','B',6);
		$pdf->Write(0, "St/L: ");
		$pdf->Write(0, substr($stitch_length, 0, 25) );
		$pdf->SetFont('Arial','B',6);
		$pdf->Write(0, ", B: ");
		$pdf->SetFont('Arial','B',6);
		$pdf->Write(0, substr($brand, 0, 25) );

		$pdf->SetXY($i, $j+39.2);
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, "YC: ");
		$pdf->Write(0, substr($yarn_count, 0, 25));
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, ", L: ");
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($yarn_lot, 0, 20));

		$pdf->SetXY($i, $j+43.2);
		$pdf->SetFont('Arial','B',7);
		$pdf->Write(0, substr($operatorName, 0, 25) );
		$pdf->SetFont('Arial','B',8);
		$pdf->SetXY($i+35, $j+43.2);
		$pdf->Write(0, " R.WT: ");
		$pdf->SetFont('Arial','B',8);
		$pdf->Write(0, number_format($row[csf('qnty')], 2, '.', ''). " Kg" );

		$k++;
		$br++;
	}

	foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

?>

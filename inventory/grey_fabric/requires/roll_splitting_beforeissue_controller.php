<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$hidden_dtls_id=str_replace("'","",$hidden_dtls_id);

	if($db_type==0)
	{
		$issue_number_ref = " a.issue_number";
		$batch_no_ref = " a.batch_no";
		$trans_no_ref_mother = " a.transfer_system_id";
		$recv_number_ref = " a.recv_number";
	}
	else
	{
		$issue_number_ref = " cast(a.issue_number as varchar(4000))";
		$batch_no_ref = " cast(a.batch_no as varchar(4000))";
		$trans_no_ref_mother = " cast(a.transfer_system_id as varchar(4000))";
		$recv_number_ref = " cast(a.recv_number as varchar(4000))";
	}
	$nxtProcessedBarcodeRes = sql_select("select $issue_number_ref as issue_number, b.entry_form
		from inv_issue_master a, pro_roll_details b
		where a.id = b.mst_id and a.entry_form = 61 and b.entry_form = 61
		and b.status_active = 1 and b.is_deleted = 0 and b.barcode_no in ($txt_bar_code_num) and b.is_returned != 1
		union all
		select a.recv_number as issue_number , b.entry_form
		from  inv_receive_mas_batchroll a, pro_roll_details b
		where a.id = b.mst_id and a.entry_form = 62 and b.entry_form = 62
		and b.status_active = 1 and b.is_deleted = 0 and b.barcode_no in ($txt_bar_code_num)
		union all
		select $batch_no_ref as issue_number, b.entry_form
		from  pro_batch_create_mst a, pro_roll_details b
		where a.id = b.mst_id and b.entry_form = 64 and b.barcode_no in ($txt_bar_code_num)
		and b.status_active = 1 and b.is_deleted = 0
		union all
		select $trans_no_ref_mother as issue_number, b.entry_form
		from  inv_item_transfer_mst a, pro_roll_details b
		where a.id = b.mst_id and a.entry_form in (82,83,110,180,183,133) and b.entry_form in (82,83,110,180,183,133)
		and b.status_active = 1 and b.is_deleted = 0 and b.barcode_no in ($txt_bar_code_num)
		and b.id <> $hidden_table_id
		and b.re_transfer =0
		union all
		select $recv_number_ref as issue_number, b.entry_form
		from inv_receive_master a, pro_roll_details b
		where a.id = b.mst_id and a.entry_form=84 and b.entry_form=84
		and b.id <> $hidden_table_id
		and b.status_active = 1 and b.is_deleted=0 and b.barcode_no in ($txt_bar_code_num) and b.re_transfer=0
		");

	foreach ($nxtProcessedBarcodeRes as $val)
	{
		if($val[csf("entry_form")] == 61){
			echo "30**Issue Found against these barcode. Issue No : ".$val[csf("issue_number")];
			die;
		}else if($val[csf("entry_form")] == 62){
			echo "30**Receive by Batch found against these barcode. Receive No : ".$val[csf("issue_number")];
			die;
		}else if($val[csf("entry_form")] == 64){
			echo "30**Batch found against these barcode. Batch No : ".$val[csf("issue_number")];
			die;
		}
		else if($val[csf("entry_form")] == 84){
			echo "30**Issue Return found against these barcode. Batch No : ".$val[csf("issue_number")];
			die;
		}
		else {
			echo "30**Transfer found against these barcode. Transfer No : ".$val[csf("issue_number")];
			die;
		}
	}

	//$no_of_total_barcode[$txt_bar_code_num]=$txt_bar_code_num;

	$production_dtlsId=return_field_value("dtls_id","pro_roll_details"," barcode_no=$txt_bar_code_num and status_active=1 and is_deleted=0 and entry_form in(2,22)","dtls_id");
	$production_other_barcodes = sql_select("SELECT barcode_no from pro_roll_details where dtls_id=$production_dtlsId and entry_form in(2,22) and status_active=1 and is_deleted=0");
	foreach ($production_other_barcodes as $val)
	{
		$no_of_total_barcode[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
	}

	// echo "10**".$production_dtlsId;die;

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		$hidden_program_no = str_replace("'","",$hidden_program_no);

		// ================== Original Wgt Validation Start =======================

		$sql="SELECT c.entry_form, c.qnty,c.qc_pass_qnty
		FROM pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE b.id=c.dtls_id and c.entry_form in(2,22,58) and  b.trans_id<>0 and c.status_active=1 and c.is_deleted=0 and c.barcode_no=$txt_bar_code_num order by c.entry_form asc";
		// echo "10**".$sql;die;
		$data_array=sql_select($sql);
		$original_wgt=str_replace("'","",$txt_original_wgt);
		foreach($data_array as $val)
		{
			if($val[csf("entry_form")]==22 || $val[csf("entry_form")]==2)
			{
				if ( number_format($val[csf("qnty")],2,'.','') != number_format($original_wgt,2,'.','') )
				{
					echo "30**Roll wgt does not match with actual wgt.\nOriginal Wgt: ".number_format($val[csf("qnty")],2,'.','');
					disconnect($con);
					die;
				}
			}
			else
			{
				if ( number_format($val[csf("qc_pass_qnty")],2,'.','') != number_format($original_wgt,2,'.','') )
				{
					echo "30**Roll wgt does not match with actual wgt.\nOriginal Wgt: ".number_format($val[csf("qc_pass_qnty")],2,'.','');
					disconnect($con);
					die;
				}
			}
		}
		// echo "10**string".$val[csf("qnty")];die;

		// ================== Original Wgt Validation End =======================

		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if($db_type==0)
			$year_cond="YEAR(insert_date)";
		else if($db_type==2)
			$year_cond="to_char(insert_date,'YYYY')";
		else
			$year_cond=""; //defined Later

		$id = return_next_id_by_sequence("PRO_ROLL_SPLIT_PK_SEQ", "pro_roll_split", $con);
		$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_ROLL_SPLIT_PK_SEQ", "pro_roll_split",$con,1,$hidden_company_id,'RRS',113,date("Y",time()),13 ));

		/*
		|--------------------------------------------------------------------------
		| pro_roll_split
		| data preparing here
		|--------------------------------------------------------------------------
		|
		*/
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',".$hidden_company_id.",".$hidden_rollId.",".$hidden_roll_mst.",".$hidden_po_breakdown_id.",".$hidden_roll_wgt.",".$hidden_barcode.",113,".$hidden_table_id.",".$txt_remarks.",".$hidden_original_pcs.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


		if(str_replace("'","",$hidden_entry_form)==83 || str_replace("'","",$hidden_entry_form)==133 || str_replace("'","",$hidden_entry_form)==110 || str_replace("'","",$hidden_entry_form)==180 || str_replace("'","",$hidden_entry_form)==183 || str_replace("'","",$hidden_entry_form)==82)
		{
			$dtls_sql=sql_select("select  b.is_transfer ,b.transfer_criteria ,b.from_roll_id  from inv_item_transfer_dtls a, pro_roll_details b where b.dtls_id=a.id and b.entry_form in(82,83,133,110,180,183) and a.id=$hidden_dtls_id and b.barcode_no = $hidden_barcode");
		}
		else
		{
			$dtls_sql=sql_select("select a.id, a.mst_id, a.trans_id, a.prod_id, a.body_part_id, a.febric_description_id, gsm, a.width, a.no_of_roll, a.order_id, a.grey_receive_qnty, a.reject_fabric_receive, a.rate, a.amount, a.uom, a.yarn_lot, a.yarn_count, a.brand_id, a.shift_name, a.floor_id, a.machine_no_id, a.room, a.rack, a.self, a.bin_box, a.color_id, a.color_range_id, a.stitch_length,a.kniting_charge, a.yarn_rate, a.inserted_by, a.insert_date,b.is_transfer ,b.transfer_criteria, b.from_roll_id from pro_grey_prod_entry_dtls a,pro_roll_details b where b.dtls_id=a.id and b.entry_form in(22,58,2,84) and a.id=$hidden_dtls_id and b.barcode_no = $hidden_barcode");
		}

		foreach($dtls_sql as $inf)
		{
			if(str_replace("'","",$hidden_entry_form)==2 || str_replace("'","",$hidden_entry_form)==22 || str_replace("'","",$hidden_entry_form)==58 || str_replace("'","",$hidden_entry_form)==84)
			{
				$trans_id=$inf[csf('trans_id')];
				$prod_id=$inf[csf('prod_id')];
				$body_part_id=$inf[csf('body_part_id')];
				$febric_description_id=$inf[csf('febric_description_id')];
				$gsm=$inf[csf('gsm')];
				$width=$inf[csf('width')];
				$order_id=$inf[csf('order_id')];
				$rate=$inf[csf('rate')];
				$amount=$inf[csf('amount')];
				$uom=$inf[csf('uom')];
				$yarn_lot=$inf[csf('yarn_lot')];
				$yarn_count=$inf[csf('yarn_count')];
				$brand_id=$inf[csf('brand_id')];
				$shift_id=$inf[csf('shift_name')];
				$floor_id=$inf[csf('floor_id')];
				$machine_no_id=$inf[csf('machine_no_id')];
				$room=$inf[csf('room')];
				$rack=$inf[csf('rack')];
				$self=$inf[csf('self')];
				$bin_box=$inf[csf('bin_box')];
				$color_id=$inf[csf('color_id')];
				$color_range_id=$inf[csf('color_range_id')];
				$stitch_length=$inf[csf('stitch_length')];
				$kniting_charge=$inf[csf('kniting_charge')];
				$yarn_rate=$inf[csf('yarn_rate')];
			}
			$transfer_criteria=$inf[csf('transfer_criteria')];
			$is_transfer=$inf[csf('is_transfer')];
			$from_roll_id=$inf[csf('from_roll_id')];
		}

		$maxRollNo=return_field_value("max(roll_no) as roll_no","pro_roll_details","entry_form in(2,22,62,58,83,84,133,110,180,183) and po_breakdown_id=$hidden_po_breakdown_id and booking_without_order=$booking_without_order",'roll_no');

		$barcode_year=date("y");
		$hidden_transfer_mother_roll =str_replace("'","",$hidden_transfer_mother_roll);
		$hidden_entry_form =str_replace("'","",$hidden_entry_form);

		if(!empty($hidden_transfer_mother_roll))
			$grey_entry_form=$hidden_transfer_mother_roll;
		elseif($hidden_entry_form==58)
			$grey_entry_form=2;
		else
			$grey_entry_form=$hidden_entry_form;

		if($grey_entry_form<10)
			$grey_entry_form=str_pad($grey_entry_form,2,"0",STR_PAD_LEFT);

		$barcodeNos='';
		$prod_id_array=array();
		$prod_data_array=array();
		$prod_new_array=array();
		$company_id=str_replace("'","",$cbo_company_id); $z=1;
		$batch_weight=0;
		$txt_batch_no='';
		$total_split_qty=0;
		$total_split_qtyInPcs=0;

		$field_array_dtls="id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length,kniting_charge, yarn_rate, inserted_by, insert_date";

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| $data_array_roll preparing here
		|--------------------------------------------------------------------------
		|
		*/
		$splitted_barcode_arr=array();
		for($j=1;$j<=$tot_row;$j++)
		{
			$rollNo=$maxRollNo+1;
			$maxRollNo+=1;
			$update_roll_id="update_roll_id_".$j;
			$rollWgt="rollWgt_".$j;
			$rfidNo="rfidNo_".$j;
			$qtyInPcs="qtyInPcs_".$j;
			$roll_reject_qty=0;
			$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),13 ));
			$barcode_no=$barcode_year."".$grey_entry_form."".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);

			/*if($hidden_entry_form == 58)
			{
				$booking_no = $hidden_program_no ;
			}
			else
			{
				if(str_replace("'","",$booking_without_order)==1) $booking_no=str_replace("'","",$txt_order_no);
			}*/

			$booking_no = $hidden_program_no;
			if($data_array_roll!="") $data_array_roll.=",";
			$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no[2].",".$barcode_no.",".$hidden_mst_id.",".$hidden_dtls_id.",".$hidden_po_breakdown_id.",'".$hidden_entry_form."','".$$rollWgt."','".$$rollWgt."','".$roll_reject_qty."',".$hidden_rollId.",'".$rollNo."',".$hidden_table_id.",".$booking_without_order.",'".$booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$is_transfer."','".$transfer_criteria."','".$from_roll_id."',".$hidden_is_sales.",'".$$rfidNo."','".$$qtyInPcs."',".$hidden_re_transfer.")";

			$total_split_qty+=str_replace("'","",$$rollWgt);
			$total_split_qtyInPcs+=str_replace("'","",$$qtyInPcs);
			$barcodeNos.=$barcode_no."__".$id_dtls."__".$id_roll.",";

			$splitted_barcode_arr[$barcode_no]["barcode_year"] = $barcode_year;
			$splitted_barcode_arr[$barcode_no]["barcode_suffix_no"] = $barcode_suffix_no[2];
			$splitted_barcode_arr[$barcode_no]["roll_wgt"] = $$rollWgt;
			$splitted_barcode_arr[$barcode_no]["rfidNo"] = $$rfidNo;
			$splitted_barcode_arr[$barcode_no]["roll_no"] = $rollNo;
			$splitted_barcode_arr[$barcode_no]["qty_in_pcs"] = $$qtyInPcs;

			$no_of_total_barcode[$barcode_no]=$barcode_no;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_grey_prod_delivery_dtls
		| $data_array_dtls preparing here
		|
		| pro_roll_details
		| $data_array_roll_issue
		|--------------------------------------------------------------------------
		|
		*/
		//$get_roll_issue_details = sql_select("select * from pro_roll_details where barcode_no=$hidden_barcode and entry_form!=".$hidden_entry_form." and status_active=1 and is_deleted=0 order by entry_form asc");
		$get_roll_issue_details = sql_select("select * from pro_roll_details where barcode_no=$hidden_barcode and id!=".$hidden_table_id." and status_active=1 and is_deleted=0 order by entry_form asc");
		$update_table_id_arr[]=$hidden_table_id;
		foreach ($splitted_barcode_arr as $barcode => $barcode_row)
		{
			foreach($get_roll_issue_details as $data)
			{
				if($data[csf("entry_form")] == 56)
				{
					$get_original_roll_delivery_details = sql_select("select * from pro_grey_prod_delivery_dtls where barcode_num=$hidden_barcode and mst_id = ".$data[csf("mst_id")]." and status_active=1 and is_deleted=0");
					$deliv_dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);

					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls .= "(" . $deliv_dtls_id . "," . $get_original_roll_delivery_details[0][csf("mst_id")] . ",56," . $get_original_roll_delivery_details[0][csf("grey_sys_id")] . ",'" . $get_original_roll_delivery_details[0][csf("sys_dtls_id")] . "','" . $get_original_roll_delivery_details[0][csf("product_id")] . "','" . $get_original_roll_delivery_details[0][csf("order_id")] . "','" . $get_original_roll_delivery_details[0][csf("determination_id")] . "','" . $get_original_roll_delivery_details[0][csf("roll_id")] . "','" . $barcode . "','" . $barcode_row["roll_wgt"] . "','" . $barcode_row["qty_in_pcs"] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$update_deliv_table_id_arr[]=$get_original_roll_delivery_details[0][csf("id")];
					$dtls_id = $deliv_dtls_id;
				}
				else
				{
					$dtls_id = $data[csf("dtls_id")];
				}

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if($data_array_roll_issue!="")
					$data_array_roll_issue.=",";
				$data_array_roll_issue .="(".$id_roll.",".$barcode_row["barcode_year"].",".$barcode_row["barcode_suffix_no"].",".$barcode.",".$data[csf("mst_id")].",".$dtls_id.",".$data[csf("po_breakdown_id")].",".$data[csf("entry_form")].",'".$barcode_row["roll_wgt"]."','".$barcode_row["roll_wgt"]."','',".$data[csf('roll_id')].",'".$barcode_row["roll_no"]."',".$hidden_rollId.",".$data[csf("booking_without_order")].",'".$data[csf("booking_no")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$data[csf("company_id")]."','".$data[csf("is_transfer")]."','".$data[csf("transfer_criteria")]."','".$data[csf("entry_form_check")]."','".$data[csf("rate")]."','".$data[csf("amount")]."','".$data[csf("from_roll_id")]."','".$data[csf("receive_basis")]."','".$data[csf("is_sales")]."','".$data[csf("re_transfer")]."','".$data[csf("po_ids")]."','".$data[csf("batch_no")]."','".$data[csf("is_returned")]."','".$barcode_row["qty_in_pcs"]."','".$data[csf("is_extra_roll")]."','".$data[csf("reprocess")]."','".$data[csf("prev_reprocess")]."','".$data[csf("coller_cuff_size")] ."','".$barcode_row["rfidNo"]."','".$data[csf("roll_used")]."')";
				$update_table_id_arr[]=$data[csf("id")];
			}
		}

		/*
		|--------------------------------------------------------------------------
		| PRO_QC_RESULT_MST
		| PRO_QC_RESULT_DTLS
		| $data_array_qc_result preparing here
		|--------------------------------------------------------------------------
		|
		*/
		$get_qc_result_mst = sql_select("SELECT a.id, a.pro_dtls_id, a.roll_maintain, a.barcode_no, a.roll_id, a.roll_no, a.qc_name, a.roll_width, a.roll_weight, a.roll_length, a.reject_qnty, a.qc_date, a.total_penalty_point, a.total_point, a.fabric_grade, a.comments, a.ready_to_approve, a.is_approved, a.inserted_by, a.insert_date, a.update_by, a.update_date, a.roll_status, a.length_percent, a.width_percent, a.twisting_percent, a.actual_dia, a.actual_gsm, a.is_tab, a.entry_form, a.knitting_density, a.qc_mc_name, a.fabric_shade, b.id as dtls_id, b.defect_name, b.defect_count, b.found_in_inch, b.found_in_inch_point, b.penalty_point, b.department, b.form_type, b.defect_name2, b.defect_name3 from pro_qc_result_mst a, pro_qc_result_dtls b where a.id=b.mst_id and a.barcode_no=$hidden_barcode and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach ($splitted_barcode_arr as $barcode => $barcode_row)
		{
			foreach($get_qc_result_mst as $qcData)
			{
				if($id_check[$barcode] == "")
				{
					$id_check[$barcode]=$barcode;
					//$get_original_roll_delivery_details = sql_select("select * from pro_grey_prod_delivery_dtls where barcode_num=$hidden_barcode and mst_id = ".$qcData[csf("id")]." and status_active=1 and is_deleted=0");
					$qc_id = return_next_id_by_sequence("PRO_QC_RESULT_MST_SEQ", "pro_qc_result_mst", $con);

					// echo $qcData[csf("id")].'='.$barcode.'='.$qcData[csf("pro_dtls_id")].'==<br>';
					if($data_array_qc_result!="") $data_array_qc_result.=",";
					$data_array_qc_result .= "(" . $qc_id . ",'" . $qcData[csf("pro_dtls_id")] . "','" . $qcData[csf("roll_maintain")] . "'," . $barcode . "," . $qcData[csf("roll_id")] . "," . $barcode_row["roll_no"] . ",'" . $qcData[csf("qc_name")] . "','" . $qcData[csf("roll_width")] . "','" .  $barcode_row["roll_wgt"] . "','" . $qcData[csf("roll_length")] . "','" . $qcData[csf("reject_qnty")] . "','" . $qcData[csf("qc_date")] . "','" . $qcData[csf("total_penalty_point")] . "','" . $qcData[csf("total_point")] . "','" . $qcData[csf("fabric_grade")] . "','" . $qcData[csf("comments")] . "','" . $qcData[csf("ready_to_approve")] . "','" . $qcData[csf("is_approved")] . "','" . $qcData[csf("roll_status")] . "','" . $qcData[csf("length_percent")] . "','" . $qcData[csf("width_percent")] . "','" . $qcData[csf("twisting_percent")] . "','" . $qcData[csf("actual_dia")] . "','" . $qcData[csf("actual_gsm")] . "','" . $qcData[csf("is_tab")] . "','" . $qcData[csf("entry_form")] . "','" . $qcData[csf("knitting_density")] . "','" . $qcData[csf("qc_mc_name")] . "','" . $qcData[csf("fabric_shade")] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
				// echo $qcData[csf("dtls_id")].'**<br>';
				$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
				if($data_array_qc_result_dtls!="")
					$data_array_qc_result_dtls.=",";
				$data_array_qc_result_dtls .="(".$dtls_id.",".$qc_id.",'".$qcData[csf("defect_name")]."','".$qcData[csf("defect_count")]."','".$qcData[csf("found_in_inch")]."','".$qcData[csf("found_in_inch_point")]."','".$qcData[csf('penalty_point')]."','".$qcData[csf("department")]."','".$qcData[csf("form_type")]."','".$qcData[csf("defect_name2")]."','".$qcData[csf("defect_name3")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}
		// echo '10**checking';die;
		//echo "10**insert into pro_roll_details (".$field_array_roll_issue.") values ".$data_array_roll_issue;oci_rollback($con); die;
		$update_roll_wgt=str_replace("'","",$txt_original_wgt)-$total_split_qty;
		$update_qtyInPcs=str_replace("'","",$txt_original_pcs)-$total_split_qtyInPcs;
		$rID=$rID3=$rID5=$rID6=$rID7=1;

		/*
		|--------------------------------------------------------------------------
		| pro_roll_split
		| data inserting here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		$field_array="id,system_number_prefix,system_number_prefix_num,system_number,company_id,roll_id,roll_no,order_id,roll_wgt,barcode_no,entry_form,split_from_id,remarks,qty_in_pcs,inserted_by,insert_date";
		$rID=sql_insert("pro_roll_split", $field_array, $data_array,0);
		if($rID)
			$flag=1;
		else
			$flag=0;

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details data
		| inserting here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, roll_id,roll_no,roll_split_from,booking_without_order,booking_no, inserted_by, insert_date ,is_transfer ,transfer_criteria ,from_roll_id,is_sales,rf_id,qc_pass_qnty_pcs,re_transfer";
		// echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll";die;
		$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		if($flag==1)
		{
			if($rID3)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data updating here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1)
		{
			$field_array_roll_update="qnty*qc_pass_qnty_pcs*qc_pass_qnty*updated_by*update_date";
			$data_array_roll_update="".$update_roll_wgt."*".$update_qtyInPcs."*".$update_roll_wgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			// echo '10**'.$data_array_roll_update.'='.'<pre>';print_r($update_table_id_arr);die;
			//$rID5=sql_update("pro_roll_details",$field_array_roll_update,$data_array_roll_update,"id",$hidden_table_id,1);
			$rID5=sql_multirow_update("pro_roll_details",$field_array_roll_update,$data_array_roll_update,"id",implode(",",$update_table_id_arr),0);
			if($rID5)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1 && $data_array_roll_issue!='')
		{
			$field_array_roll_issue="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, roll_id,roll_no,roll_split_from,booking_without_order ,booking_no, inserted_by, insert_date,company_id, is_transfer, transfer_criteria , entry_form_check, rate, amount, from_roll_id, receive_basis, is_sales, re_transfer, po_ids, batch_no, is_returned , qc_pass_qnty_pcs, is_extra_roll, reprocess, prev_reprocess, coller_cuff_size,rf_id,roll_used";
			//echo "10**insert into pro_roll_details ($field_array_roll_issue) values $data_array_roll_issue";die;
			$rID6=sql_insert("pro_roll_details",$field_array_roll_issue, $data_array_roll_issue,0);
			if($rID6)
				$flag=1;
			else
				$flag=0;
		}
		//echo "10**".$flag;die;

		/*
		|--------------------------------------------------------------------------
		| pro_grey_prod_delivery_dtls
		| data inserting and updating here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if($flag == 1 && $data_array_dtls != '')
		{
			$field_delivery_array_dtls = "id,mst_id,entry_form,grey_sys_id,sys_dtls_id,product_id,order_id,determination_id,roll_id,barcode_num, current_delivery,qty_in_pcs,inserted_by,insert_date";
			//echo "10**insert into pro_grey_prod_delivery_dtls ($field_delivery_array_dtls) values $data_array_dtls";die;
			$rID7 = sql_insert("pro_grey_prod_delivery_dtls", $field_delivery_array_dtls, $data_array_dtls, 0);

			$field_array_deliv_update = "current_delivery*qty_in_pcs*updated_by*update_date";
			$data_array_deliv_update = "".$update_roll_wgt."*".$update_qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID8 = sql_multirow_update("pro_grey_prod_delivery_dtls",$field_array_deliv_update,$data_array_deliv_update,"id",implode(",",$update_deliv_table_id_arr),0);
			if($rID7)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_qc_result_mst
		| roll weight data updating here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if($flag == 1 && $data_array_qc_result != '')
		{
			$field_array_qc_mst_update = "roll_weight*update_by*update_date";
			$data_array_qc_mst_update = "".$update_roll_wgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$qc_mst_qnty_update=sql_update("pro_qc_result_mst",$field_array_qc_mst_update,$data_array_qc_mst_update,"barcode_no",$txt_bar_code_num,1);
			if($qc_mst_qnty_update)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_qc_result_mst
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1 && $data_array_qc_result!='')
		{
			$field_array_qc_result = "id, pro_dtls_id, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, total_penalty_point, total_point, fabric_grade, comments, ready_to_approve, is_approved, roll_status, length_percent, width_percent, twisting_percent, actual_dia, actual_gsm, is_tab, entry_form, knitting_density, qc_mc_name, fabric_shade, inserted_by, insert_date";
			// echo "10**insert into pro_qc_result_mst ($field_array_qc_result) values $data_array_qc_result";die;
			$rID8 = sql_insert("pro_qc_result_mst", $field_array_qc_result, $data_array_qc_result, 0);
			if($rID8)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_qc_result_dtls
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1 && $data_array_qc_result_dtls!='')
		{
			$field_array_qc_result_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point, department, form_type, defect_name2, defect_name3, inserted_by, insert_date";
			// echo "10**insert into pro_qc_result_dtls ($field_array_qc_result_dtls) values $data_array_qc_result_dtls";die;
			$rID9 = sql_insert("pro_qc_result_dtls", $field_array_qc_result_dtls, $data_array_qc_result_dtls, 0);
			if($rID9)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_grey_prod_entry_dtls
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$no_of_roll = count($no_of_total_barcode);
		$field_array_production_dtls_update="no_of_roll*updated_by*update_date";
		$data_array_production_dtls_update="".$no_of_roll."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$grey_production_dtls_id_update=sql_update("pro_grey_prod_entry_dtls",$field_array_production_dtls_update,$data_array_production_dtls_update,"id",$production_dtlsId,1);

		if($grey_production_dtls_id_update)
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}
		//echo "10**".$flag;die;

		//echo "10**".$data_array_qc_mst_update.'='.$txt_bar_code_num."<br>";
		//print_r($no_of_total_barcode);
		//die;

		// echo "10**".$rID."**".$rID3."**".$rID5."**".$rID6."**".$rID7."**".$rID8."**".$rID9."**".$flag."==".$grey_production_dtls_id_update."==".$qc_mst_qnty_update; oci_rollback($con); die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id."**".$production_dtlsId);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**".$new_mrr_number[0]."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id."**".$production_dtlsId);
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
	else if ($operation==1) // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$hidden_mother_barcode=$hidden_barcode;

		$sql_split_mother_barcode=sql_select("SELECT max(id) as id from pro_roll_split where barcode_no =$hidden_mother_barcode and status_active=1 and is_deleted=0 and entry_form=113");
		$sql_split_mother_max_id= $sql_split_mother_barcode[0][csf("id")];
		if(str_replace("'","",$update_id) != $sql_split_mother_max_id)
		{
			echo "30**$hidden_mother_barcode Found in another Split. Update Not Allowed.";disconnect($con);
			die;
		}

		for($j=1;$j<=$tot_row;$j++)
		{
			$update_roll_id="update_roll_id_".$j;
			$barcodeNo="barcodeNo_".$j;

			if(str_replace("'","",$$update_roll_id)!="")
			{
				$prev_split_check.=$$barcodeNo.",";
			}
		}
		$prev_split_check=chop($prev_split_check,",");
		if($prev_split_check!="")
		{
			if($db_type==0)
			{
				$issue_number_ref = " a.issue_number";
				$batch_no_ref = " a.batch_no";
				$trans_no_ref = " a.transfer_system_id";
				$recv_number_ref = " a.recv_number";
			}else{
				$issue_number_ref = " cast(a.issue_number as varchar(4000))";
				$batch_no_ref = " cast(a.batch_no as varchar(4000))";
				$trans_no_ref = " cast(a.transfer_system_id as varchar(4000))";
				$recv_number_ref = " cast(a.recv_number as varchar(4000))";
			}
			$nxtProcessedBarcodeRes = sql_select("SELECT $issue_number_ref as issue_number, b.entry_form, b.barcode_no, b.qnty
				from  inv_issue_master a, pro_roll_details b
				where a.id = b.mst_id and a.entry_form = 61 and b.entry_form = 61
				and b.status_active = 1 and b.is_deleted = 0 and b.barcode_no in ($prev_split_check) and b.is_returned != 1
				union all
				select a.recv_number as issue_number , b.entry_form, b.barcode_no, b.qnty
				from  inv_receive_mas_batchroll a, pro_roll_details b
				where a.id = b.mst_id and a.entry_form = 62 and b.entry_form = 62
				and b.status_active = 1 and b.is_deleted = 0 and b.barcode_no in ($prev_split_check)
				union all
				select $batch_no_ref as issue_number , b.entry_form, b.barcode_no, b.qnty
				from  pro_batch_create_mst a, pro_roll_details b
				where a.id = b.mst_id and b.entry_form = 64 and b.barcode_no in ($prev_split_check)
				and b.status_active = 1 and b.is_deleted = 0
				union all
				select $trans_no_ref as issue_number , b.entry_form, b.barcode_no, b.qnty
				from  inv_item_transfer_mst a, pro_roll_details b
				where a.id = b.mst_id and a.entry_form in (82,83,110,180,183,133) and b.entry_form in (82,83,110,180,183,133)
				and b.status_active = 1 and b.is_deleted = 0 and b.barcode_no in ($prev_split_check) and b.roll_split_from = 0
				union all
				select $recv_number_ref as issue_number, b.entry_form,b.barcode_no, b.qnty
				from inv_receive_master a, pro_roll_details b
				where a.id = b.mst_id and a.entry_form = 84 and b.entry_form = 84
				and b.status_active = 1 and b.is_deleted = 0 and b.barcode_no in ($prev_split_check) and b.roll_split_from = 0
				");

			foreach ($nxtProcessedBarcodeRes as $val)
			{
				$next_process_found_barcode_arr[$val[csf("barcode_no")]]=$val[csf("barcode_no")];
				$next_process_found_wgt_arr[$val[csf("barcode_no")]]=$val[csf("qnty")];
			}

			$sql_split_barcode="SELECT barcode_no, roll_wgt from pro_roll_split where barcode_no in($prev_split_check) and status_active=1 and is_deleted=0 and entry_form=113";
			$split_barcode_data=sql_select($sql_split_barcode);

			foreach ($split_barcode_data as $row)
			{
				$next_process_found_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
				$next_process_found_wgt_arr[$row[csf("barcode_no")]]=$row[csf("roll_wgt")];
			}
		}
		// echo "<pre>";print_r($next_process_found_wgt_arr);die;

		$dtls_sql=sql_select("select a.id, a.mst_id, a.trans_id, a.prod_id, a.body_part_id, a.febric_description_id, gsm, a.width, a.no_of_roll, a.order_id, a.grey_receive_qnty, a.reject_fabric_receive, a.rate, a.amount, a.uom, a.yarn_lot, a.yarn_count, a.brand_id, a.shift_name, a.floor_id, a.machine_no_id, a.room, a.rack, a.self, a.bin_box, a.color_id, a.color_range_id, a.stitch_length,a.kniting_charge, a.yarn_rate, a.inserted_by, a.insert_date,b.is_transfer ,b.transfer_criteria ,b.from_roll_id
		from pro_grey_prod_entry_dtls a, pro_roll_details b
		where b.dtls_id=a.id and b.entry_form in(22,58,2,84) and a.id=$hidden_dtls_id and b.barcode_no = $hidden_barcode");
		foreach($dtls_sql as $inf)
		{
			$trans_id=$inf[csf('trans_id')];
			$prod_id=$inf[csf('prod_id')];
			$body_part_id=$inf[csf('body_part_id')];
			$febric_description_id=$inf[csf('febric_description_id')];
			$gsm=$inf[csf('gsm')];
			$width=$inf[csf('width')];
			$order_id=$inf[csf('order_id')];
			$rate=$inf[csf('rate')];
			$amount=$inf[csf('amount')];
			$uom=$inf[csf('uom')];
			$yarn_lot=$inf[csf('yarn_lot')];
			$yarn_count=$inf[csf('yarn_count')];
			$brand_id=$inf[csf('brand_id')];
			$shift_id=$inf[csf('shift_name')];
			$floor_id=$inf[csf('floor_id')];
			$machine_no_id=$inf[csf('machine_no_id')];
			$room=$inf[csf('room')];
			$rack=$inf[csf('rack')];
			$self=$inf[csf('self')];
			$bin_box=$inf[csf('bin_box')];
			$color_id=$inf[csf('color_id')];
			$color_range_id=$inf[csf('color_range_id')];
			$stitch_length=$inf[csf('stitch_length')];
			$kniting_charge=$inf[csf('kniting_charge')];
			$yarn_rate=$inf[csf('yarn_rate')];
			$transfer_criteria=$inf[csf('transfer_criteria')];
			$is_transfer=$inf[csf('is_transfer')];
			$from_roll_id=$inf[csf('from_roll_id')];
		}

		$field_array_dtls="id, mst_id, trans_id, prod_id, body_part_id, febric_description_id, gsm, width, no_of_roll, order_id, grey_receive_qnty, reject_fabric_receive, rate, amount, uom, yarn_lot, yarn_count, brand_id, shift_name, floor_id, machine_no_id, room, rack, self, bin_box, color_id, color_range_id, stitch_length,kniting_charge, yarn_rate, inserted_by, insert_date";

		$hidden_program_no=str_replace("'","",$hidden_program_no);
		$hidden_transfer_mother_roll =str_replace("'","",$hidden_transfer_mother_roll);
		$hidden_entry_form=str_replace("'","",$hidden_entry_form);
		if(!empty($hidden_transfer_mother_roll)) $grey_entry_form=$hidden_transfer_mother_roll;
		elseif($hidden_entry_form==58) 	$grey_entry_form=2;
		else $grey_entry_form=$hidden_entry_form;
		$barcode_year=date("y");
		if(str_replace("'","",$grey_entry_form)<10) $grey_entry_form=str_pad($grey_entry_form,2,"0",STR_PAD_LEFT);
		$maxRollNo=return_field_value("max(roll_no) as roll_no","pro_roll_details","entry_form in(2,22,62,58,83,84,133,110,180,183) and po_breakdown_id=$hidden_po_breakdown_id and booking_without_order=$booking_without_order",'roll_no');
		$barcodeNos='';
		$batch_weight=0; $txt_batch_no='';$total_split_qtyInPcs=0;
		$splitted_barcode_arr=array();$prev_split_barcode_no="";
		for($j=1;$j<=$tot_row;$j++)
		{
			$update_roll_id="update_roll_id_".$j;
			$rollWgt="rollWgt_".$j;
			$qtyInPcs="qtyInPcs_".$j;
			$rfidNo="rfidNo_".$j;
			$rollno="roll_no_".$j;
			$hideRollWgt="hideRollWgt_".$j;
			$hiddenQtyInPcs="hiddenQtyInPcs_".$j;
			$barcodeNo="barcodeNo_".$j;
			$update_dtls_id="update_dtls_id_".$j;
			$roll_reject_qty=0;
			// $total_split_qtyInPcs=0;

			if(str_replace("'","",$$update_roll_id)!="")
			{
				if($next_process_found_wgt_arr[$$barcodeNo]!="" && $next_process_found_wgt_arr[$$barcodeNo] != str_replace("'","",$$rollWgt))
				{
					echo "30**Child Barcode Splitted/Issue Found Qty Mismatch, Barcode No: ".$$barcodeNo;die;
				}

				if ($next_process_found_barcode_arr[$$barcodeNo]=="") // Update only without next process child barcode
				{
					$update_roll_arr[]=$$update_roll_id;
					$data_array_roll_update[$$update_roll_id]=explode("*",($$rollWgt."*".$$rollWgt."*".$qty_In_Pcs."*".$$rfidNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					$up_roll_qnty[$$barcodeNo]=str_replace("'","",$$rollWgt);
					$prev_split_barcode_no.=$$barcodeNo.","; // Without next process child barcode
					$up_hide_roll_qnty[$$barcodeNo]=str_replace("'","",$$hideRollWgt);

					$prev_qc_barcode_no_arr[$$barcodeNo]['roll_wgt']=$$rollWgt;
					$prev_qc_barcode_no_arr[$$barcodeNo]['roll_no']=$$rollno;
					$prev_qc_barcode_no_arr[$$barcodeNo]['barcodeNo']=$$barcodeNo;

					$up_hide_rfidNo[$$barcodeNo]=str_replace("'","",$$rfidNo);
					$up_hide_rfidNo[str_replace("'","",$hidden_barcode)]=str_replace("'","",$hidden_mother_rf_id);

					$up_qtyInPcs[$$barcodeNo]=str_replace("'","",$$qtyInPcs)*1;
					$up_hidden_qtyInPcs[$$barcodeNo]=str_replace("'","",$$hiddenQtyInPcs);
				}

				$barcodeNos.=$$barcodeNo."__".$$update_dtls_id."__".$$update_roll_id.",";

				$no_of_total_barcode[$$barcodeNo]=$$barcodeNo;

				$total_split_qty+=str_replace("'","",$$rollWgt);
				$total_split_qtyInPcs+=str_replace("'","",$$qtyInPcs)*1;

				$qty_In_Pcs = str_replace("'","",$$qtyInPcs)*1;

				// echo $$rollWgt.'<br>';
			}
			else
			{
				$rollNo=$maxRollNo+1;
				$maxRollNo+=1;
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$id_dtls = return_next_id_by_sequence("PRO_GREY_PROD_DTLS_PK_SEQ", "pro_grey_prod_entry_dtls", $con);
				$barcode_suffix_no = explode("*", return_next_id_by_sequence("ROLL_BARCODE_SUFFIX_NO_SEQ", "pro_roll_details",$con,1,0,'',2,date("Y",time()),13 ));
				$barcode_no=$barcode_year."".$grey_entry_form."".str_pad($barcode_suffix_no[2],7,"0",STR_PAD_LEFT);

				/*if($hidden_entry_form == 58)
				{
					$booking_no = $hidden_program_no;
				}
				else
				{
					if(str_replace("'","",$booking_without_order)==1) $booking_no=str_replace("'","",$txt_order_no);
				}*/
				$booking_no = $hidden_program_no;

				$data_array_roll.="(".$id_roll.",".$barcode_year.",".$barcode_suffix_no[2].",".$barcode_no.",".$hidden_mst_id.",".$hidden_dtls_id.",".$hidden_po_breakdown_id.",".$hidden_entry_form.",'".$$rollWgt."','".$$rollWgt."','".$roll_reject_qty."',".$hidden_rollId.",'".$rollNo."',".$hidden_table_id.",".$booking_without_order.",'".$booking_no."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$is_transfer."','".$transfer_criteria."','".$from_roll_id."',".$hidden_is_sales.",'".$$rfidNo."','".$$qtyInPcs."')";

				$total_split_qty+=str_replace("'","",$$rollWgt);
				$total_split_qtyInPcs+=str_replace("'","",$$qtyInPcs);

				$barcodeNos.=$barcode_no."__".$id_dtls."__".$id_roll.",";
				$splitted_barcode_arr[$barcode_no]["barcode_year"] = $barcode_year;
				$splitted_barcode_arr[$barcode_no]["barcode_suffix_no"] = $barcode_suffix_no[2];
				$splitted_barcode_arr[$barcode_no]["roll_wgt"] = $$rollWgt;
				$splitted_barcode_arr[$barcode_no]["rfidNo"] = $$rfidNo;
				$splitted_barcode_arr[$barcode_no]["roll_no"] = $rollNo;
				$splitted_barcode_arr[$barcode_no]["qty_in_pcs"] = $$qtyInPcs;
				$splitted_barcode_arr[$barcode_no]["barcodeNo"] = $barcode_no;

				$no_of_total_barcode[$barcode_no]=$barcode_no;
			}
		}

		// echo "10**"; echo '<pre>'; print_r($prev_qc_barcode_no_arr);die;
		// echo "10**"; echo $txt_original_wgt.'='.$total_split_qty;die;
		$update_roll_arr[]=str_replace("'","",$hidden_table_id);
		$update_roll_wgt=str_replace("'","",$txt_original_wgt)-$total_split_qty;
		$update_qtyInPcs=str_replace("'","",$txt_original_pcs)*1 - $total_split_qtyInPcs;
		$update_qtyInPcs = $update_qtyInPcs*1;
		$up_roll_qnty[str_replace("'","",$hidden_barcode)]=str_replace("'","",$update_roll_wgt);
		$up_qtyInPcs[str_replace("'","",$hidden_barcode)]=str_replace("'","",$update_qtyInPcs)*1;

		$data_array_roll_update[str_replace("'","",$hidden_table_id)]=explode("*",($update_roll_wgt."*".$update_roll_wgt."*".$update_qtyInPcs."*".$hidden_mother_rf_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

		$prev_split_barcode=chop($prev_split_barcode_no,",");


		// echo "10**".$prev_split_barcode;die;
		//echo "10**failed ";die;
		//$prev_split_barcode=chop($prev_split_barcode_no,",");

		//echo "10**";print_r($up_qtyInPcs);die;
		$get_roll_issue_details = sql_select("select * from pro_roll_details where barcode_no =$hidden_barcode and id!=".$hidden_table_id." and status_active=1 and is_deleted=0 order by id asc");
		if($prev_split_barcode_no!="") $hidden_barcode=$prev_split_barcode_no.str_replace("'","",$hidden_barcode);
		/*$prev_get_roll_issue_details = sql_select("select * from pro_roll_details where barcode_no in($hidden_barcode) and entry_form != ".$hidden_entry_form." and status_active=1 and is_deleted=0 order by entry_form asc");*/

		$prev_get_roll_issue_details = sql_select("select * from pro_roll_details where barcode_no in($hidden_barcode) and id != ".$hidden_table_id." and status_active=1 and is_deleted=0 order by id asc");

		foreach($prev_get_roll_issue_details as $row)
		{
			$update_roll_arr[]=$row[csf("id")];
			$data_array_roll_update[$row[csf("id")]]=explode("*",($up_roll_qnty[$row[csf("barcode_no")]]."*".$up_roll_qnty[$row[csf("barcode_no")]]."*".$up_qtyInPcs[$row[csf("barcode_no")]]."*'".$up_hide_rfidNo[$row[csf("barcode_no")]]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		//echo "10**";print_r($data_array_roll_update);die;

		$deleted_all_id=str_replace("'","",$deleted_all_id);
		if($deleted_ids!="")
		{
			$deleted_ids=explode(",",$deleted_all_id);
			foreach($deleted_ids as $ids)
			{
				$id_detals=explode("**",$ids);
				$deleted_roll_id[]=$id_detals[0];
				$data_array_roll_deleted[$id_detals[0]]=explode("*",($_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
			}
		}
		//$get_roll_issue_details = sql_select("select * from pro_roll_details where barcode_no in ($hidden_barcode) and entry_form!=".$hidden_entry_form." and status_active=1 and is_deleted=0 order by entry_form asc");

		$update_table_id_arr[]=$hidden_table_id;
		foreach ($splitted_barcode_arr as $barcode => $barcode_row)
		{
			foreach($get_roll_issue_details as $data)
			{
				if($data[csf("entry_form")] == 56)
				{
					$get_original_roll_delivery_details = sql_select("select * from pro_grey_prod_delivery_dtls where barcode_num= ".$data[csf("barcode_no")]. " and mst_id = ".$data[csf("mst_id")]." and status_active=1 and is_deleted=0");
					$deliv_dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls .= "(" . $deliv_dtls_id . "," . $get_original_roll_delivery_details[0][csf("mst_id")] . ",56," . $get_original_roll_delivery_details[0][csf("grey_sys_id")] . ",'" . $get_original_roll_delivery_details[0][csf("sys_dtls_id")] . "','" . $get_original_roll_delivery_details[0][csf("product_id")] . "','" . $get_original_roll_delivery_details[0][csf("order_id")] . "','" . $get_original_roll_delivery_details[0][csf("determination_id")] . "','" . $get_original_roll_delivery_details[0][csf("roll_id")] . "','" . $barcode . "','" . $barcode_row["roll_wgt"] . "','" . $barcode_row["qty_in_pcs"] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					$update_deliv_table_id_arr[]=$get_original_roll_delivery_details[0][csf("id")];
					$dtls_id = $deliv_dtls_id;
				}
				else
				{
					$dtls_id = $data[csf("dtls_id")];
				}

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if($data_array_roll_issue!="") $data_array_roll_issue.=",";
				$data_array_roll_issue .="(".$id_roll.",".$barcode_row["barcode_year"].",".$barcode_row["barcode_suffix_no"].",".$barcode.",".$data[csf("mst_id")].",".$dtls_id.",".$data[csf("po_breakdown_id")].",".$data[csf("entry_form")].",'".$barcode_row["roll_wgt"]."','".$barcode_row["roll_wgt"]."','',".$data[csf('roll_id')].",'".$barcode_row["roll_no"]."',".$hidden_rollId.",".$data[csf("booking_without_order")].",'".$data[csf("booking_no")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$data[csf("company_id")]."','".$data[csf("is_transfer")]."','".$data[csf("transfer_criteria")]."','".$data[csf("entry_form_check")]."','".$data[csf("rate")]."','".$data[csf("amount")]."','".$data[csf("from_roll_id")]."','".$data[csf("receive_basis")]."','".$data[csf("is_sales")]."','".$data[csf("re_transfer")]."','".$data[csf("po_ids")]."','".$data[csf("batch_no")]."','".$data[csf("is_returned")]."','".$barcode_row["qty_in_pcs"]."','".$data[csf("is_extra_roll")]."','".$data[csf("reprocess")]."','".$data[csf("prev_reprocess")]."','".$data[csf("coller_cuff_size")]."','".$barcode_row["rfidNo"]."','".$data[csf("roll_used")]."')";
				$update_roll_arr[]=$data[csf("id")];
				$data_array_roll_update[$data[csf("id")]]=explode("*",($update_roll_wgt."*".$update_roll_wgt."*".$update_qtyInPcs."*'".$data[csf("rf_id")]."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		// echo "10**";echo "<pre>"; print_r($data_array_roll_update);die;
		$prev_qc_barcode_and_splitted_barcode_arr = array_merge($prev_qc_barcode_no_arr, $splitted_barcode_arr);
		//echo '<pre>'; print_r($prev_qc_barcode_and_splitted_barcode_arr);die;

		/*
		|--------------------------------------------------------------------------
		| PRO_QC_RESULT_MST
		| PRO_QC_RESULT_DTLS
		| $data_array_qc_result preparing here
		|--------------------------------------------------------------------------
		|
		*/
		$get_qc_result_mst = sql_select("SELECT a.id, a.pro_dtls_id, a.roll_maintain, a.barcode_no, a.roll_id, a.roll_no, a.qc_name, a.roll_width, a.roll_weight, a.roll_length, a.reject_qnty, a.qc_date, a.total_penalty_point, a.total_point, a.fabric_grade, a.comments, a.ready_to_approve, a.is_approved, a.inserted_by, a.insert_date, a.update_by, a.update_date, a.roll_status, a.length_percent, a.width_percent, a.twisting_percent, a.actual_dia, a.actual_gsm, a.is_tab, a.entry_form, a.knitting_density, a.qc_mc_name, a.fabric_shade, b.id as dtls_id, b.defect_name, b.defect_count, b.found_in_inch, b.found_in_inch_point, b.penalty_point, b.department, b.form_type, b.defect_name2, b.defect_name3 from pro_qc_result_mst a, pro_qc_result_dtls b where a.id=b.mst_id and a.barcode_no=$hidden_mother_barcode and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach ($prev_qc_barcode_and_splitted_barcode_arr as $key => $barcode_row)
		{
			// echo $key.'<br>';
			foreach($get_qc_result_mst as $qcData)
			{
				if($id_check[$barcode_row["barcodeNo"]] == "")
				{
					$id_check[$barcode_row["barcodeNo"]]=$barcode_row["barcodeNo"];

					$qc_id = return_next_id_by_sequence("PRO_QC_RESULT_MST_SEQ", "pro_qc_result_mst", $con);

					// echo $qcData[csf("id")].'='.$key.'='.$qcData[csf("pro_dtls_id")].'='.$barcode_row["roll_wgt"].'==<br>';
					if($data_array_qc_result!="") $data_array_qc_result.=",";
					$data_array_qc_result .= "(" . $qc_id . "," . $qcData[csf("pro_dtls_id")] . "," . $qcData[csf("roll_maintain")] . "," . $barcode_row["barcodeNo"] . "," . $qcData[csf("roll_id")] . "," . $barcode_row["roll_no"] . ",'" . $qcData[csf("qc_name")] . "'," . $qcData[csf("roll_width")] . "," .  $barcode_row["roll_wgt"] . "," . $qcData[csf("roll_length")] . ",'" . $qcData[csf("reject_qnty")] . "','" . $qcData[csf("qc_date")] . "'," . $qcData[csf("total_penalty_point")] . "," . $qcData[csf("total_point")] . ",'" . $qcData[csf("fabric_grade")] . "','" . $qcData[csf("comments")] . "','" . $qcData[csf("ready_to_approve")] . "','" . $qcData[csf("is_approved")] . "','" . $qcData[csf("roll_status")] . "','" . $qcData[csf("length_percent")] . "','" . $qcData[csf("width_percent")] . "','" . $qcData[csf("twisting_percent")] . "','" . $qcData[csf("actual_dia")] . "','" . $qcData[csf("actual_gsm")] . "','" . $qcData[csf("is_tab")] . "','" . $qcData[csf("entry_form")] . "','" . $qcData[csf("knitting_density")] . "','" . $qcData[csf("qc_mc_name")] . "','" . $qcData[csf("fabric_shade")] . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}

				// echo $qcData[csf("dtls_id")].'**<br>';
				$dtls_id = return_next_id_by_sequence("PRO_QC_RESULT_DTLS_SEQ", "pro_qc_result_dtls", $con);
				if($data_array_qc_result_dtls!="")
					$data_array_qc_result_dtls.=",";
				$data_array_qc_result_dtls .="(".$dtls_id.",".$qc_id.",'".$qcData[csf("defect_name")]."','".$qcData[csf("defect_count")]."','".$qcData[csf("found_in_inch")]."','".$qcData[csf("found_in_inch_point")]."','".$qcData[csf('penalty_point')]."','".$qcData[csf("department")]."','".$qcData[csf("form_type")]."','".$qcData[csf("defect_name2")]."','".$qcData[csf("defect_name3")]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}
		// echo '10**checking<pre>'.print_r($data_array_qc_result);die;

		/*
		|--------------------------------------------------------------------------
		| pro_qc_result_mst
		| pro_qc_result_dtls
		| data delete here
		|--------------------------------------------------------------------------
		|
		*/
		$prev_qc_data = sql_select("SELECT b.id as dtls_id from pro_qc_result_mst a, pro_qc_result_dtls b where a.id=b.mst_id and a.barcode_no in($prev_split_barcode) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($prev_qc_data as $value)
		{
			$prev_qc_dtls_id.=$value[csf('dtls_id')].",";
		}
		$prev_qc_dtls_ids=chop($prev_qc_dtls_id,",");

		if($prev_qc_dtls_ids != "")
		{
			// echo "delete from pro_qc_result_mst where barcode_no in($prev_split_barcode)<br>";
			$delete_qc_mst=execute_query("delete from pro_qc_result_mst where barcode_no in($prev_split_barcode)",0);
			// echo "10**delete from pro_qc_result_dtls where id in($prev_qc_dtls_ids)";die;
			$delete_qc_dtls=execute_query("delete from pro_qc_result_dtls where id in($prev_qc_dtls_ids)",0);
		}
		// echo '10**checking';die;

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data updating here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		$flag=1;
		if(count($data_array_roll_deleted)>0)
		{
			$field_array_roll_deleted="updated_by*update_date*status_active*is_deleted";
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_deleted, $data_array_roll_deleted, $deleted_roll_id ));
			if($flag==1)
			{
				if($rollUpdate)
					$flag=1;
				else
					$flag=10;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data updating here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if(count($data_array_roll_update)>0)
		{
			$field_array_roll_update="qnty*qc_pass_qnty*qc_pass_qnty_pcs*rf_id*updated_by*update_date";
			//echo "10**".bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $update_roll_arr );oci_rollback($con);die;
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $update_roll_arr ));
			if($flag==1)
			{
				if($rollUpdate)
					$flag=1;
				else
					$flag=20;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if($data_array_roll!="")
		{
			$field_array_roll="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, roll_id,roll_no,roll_split_from,booking_without_order ,booking_no, inserted_by, insert_date ,is_transfer ,transfer_criteria ,from_roll_id,is_sales,rf_id,qc_pass_qnty_pcs";
			// echo "10**insert into pro_roll_details ($field_array_roll) values $data_array_roll";die;
			$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=30;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data inserting here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1 && $data_array_roll_issue!='')
		{
			$field_array_roll_issue="id, barcode_year,barcode_suffix_no,barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,qc_pass_qnty, reject_qnty, roll_id,roll_no,roll_split_from,booking_without_order ,booking_no, inserted_by, insert_date,company_id, is_transfer, transfer_criteria, entry_form_check, rate, amount, from_roll_id, receive_basis, is_sales, re_transfer, po_ids, batch_no, is_returned, qc_pass_qnty_pcs, is_extra_roll, reprocess, prev_reprocess, coller_cuff_size,rf_id,roll_used";
			//echo "10**insert into pro_roll_details (".$field_array_roll_issue.") values ".$data_array_roll_issue;die;
			$rID6=sql_insert("pro_roll_details",$field_array_roll_issue,$data_array_roll_issue,0);
			if($rID6) $flag=1; else $flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_grey_prod_delivery_dtls
		| data inserting and
		| updating here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1 && $data_array_dtls!='')
		{
			$field_delivery_array_dtls = "id,mst_id,entry_form,grey_sys_id,sys_dtls_id,product_id,order_id,determination_id,roll_id,barcode_num, current_delivery,qty_in_pcs,inserted_by,insert_date";
			$rID7 = sql_insert("pro_grey_prod_delivery_dtls", $field_delivery_array_dtls, $data_array_dtls, 0);

			$field_array_deliv_update = "current_delivery*qty_in_pcs*updated_by*update_date";
			$data_array_deliv_update="".$update_roll_wgt."*".$update_qtyInPcs."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID8=sql_multirow_update("pro_grey_prod_delivery_dtls",$field_array_deliv_update,$data_array_deliv_update,"id",implode(",",$update_deliv_table_id_arr),0);

			if($rID7)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_roll_split
		| data updating here
		| ok
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag==1 && str_replace("'","",$update_id)!="")
		{
			$field_array_mst_update="remarks*updated_by*update_date";
			$data_array_mst_update="".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rIDmst=sql_update("pro_roll_split",$field_array_mst_update,$data_array_mst_update,"id",$update_id,1);
			if($rIDmst)
				$flag=1;
			else
				$flag=0;
		}


		/*
		|--------------------------------------------------------------------------
		| pro_qc_result_mst
		| roll weight data updating here
		|--------------------------------------------------------------------------
		|
		*/
		if ($flag==1 && $data_array_qc_result!="")
		{
			$field_array_qc_mst_update="roll_weight*update_by*update_date";
			$data_array_qc_mst_update="".$update_roll_wgt."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$qc_mst_update=sql_update("pro_qc_result_mst",$field_array_qc_mst_update,$data_array_qc_mst_update,"barcode_no",$txt_bar_code_num,1);
			if($qc_mst_update)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_qc_result_mst
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1 && $data_array_qc_result!='')
		{
			$field_array_qc_result = "id, pro_dtls_id, roll_maintain, barcode_no, roll_id, roll_no, qc_name, roll_width, roll_weight, roll_length, reject_qnty, qc_date, total_penalty_point, total_point, fabric_grade, comments, ready_to_approve, is_approved, roll_status, length_percent, width_percent, twisting_percent, actual_dia, actual_gsm, is_tab, entry_form, knitting_density, qc_mc_name, fabric_shade, inserted_by, insert_date";
			// echo "10**insert into pro_qc_result_mst ($field_array_qc_result) values $data_array_qc_result";die;
			$rID9 = sql_insert("pro_qc_result_mst", $field_array_qc_result, $data_array_qc_result, 0);
			if($rID9)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_qc_result_dtls
		| data inserting here
		|--------------------------------------------------------------------------
		|
		*/
		if($flag==1 && $data_array_qc_result_dtls!='')
		{
			$field_array_qc_result_dtls = "id, mst_id, defect_name, defect_count, found_in_inch, found_in_inch_point, penalty_point, department, form_type, defect_name2, defect_name3, inserted_by, insert_date";
			//echo "10**insert into pro_qc_result_dtls ($field_array_qc_result_dtls) values $data_array_qc_result_dtls";die;
			$rID10 = sql_insert("pro_qc_result_dtls", $field_array_qc_result_dtls, $data_array_qc_result_dtls, 0);
			if($rID10)
				$flag=1;
			else
				$flag=0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_grey_prod_entry_dtls
		| data updating here
		|--------------------------------------------------------------------------
		|
		*/
		$no_of_roll = count($no_of_total_barcode);
		$field_array_dtls_update="no_of_roll*updated_by*update_date";
		$data_array_dtls_update="".$no_of_roll."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$grey_production_dtls_id_update=sql_update("pro_grey_prod_entry_dtls",$field_array_dtls_update,$data_array_dtls_update,"id",$production_dtlsId,1);

		if($grey_production_dtls_id_update)
		{
			$flag=1;
		}
		else
		{
			$flag=0;
		}

		//echo "10**".$flag;die;
		//echo "10**".$rollUpdate."**".$rID4."**".$rID6."**".$rID7."**".$rID8."**".$rIDmst."**".$rID9."**".$rID10."**".$flag."==".$qc_mst_update."==".$grey_production_dtls_id_update; oci_rollback($con); die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id)."**".$production_dtlsId;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'", '', $update_id)."**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**".str_replace("'", '', $txt_system_no)."**".substr($barcodeNos,0,-1)."**".str_replace("'","",$hidden_table_id)."**".$production_dtlsId;
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
	echo load_html_head_contents("Receive Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(id)
		{
			// alert(id);return;
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
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_system_no').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_barcode_no').value, 'create_challan_search_list_view', 'search_div', 'roll_splitting_beforeissue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

	$company_id =$data[0];
	$system_id=trim($data[1]);
	$start_date =$data[2];
	$end_date =$data[3];
	$barcode_no=$data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and a.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			// $date_cond = "and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";

			$date_cond = "and a.insert_date between '" . date('d-M-Y 12:00:00A', strtotime($start_date)) . "' and '" . date('d-M-Y 11:59:59', strtotime($end_date)).'PM' . "'";
		}
	} else {
		$date_cond = "";
	}

	$sql_sales_order = sql_select("select id,job_no as sales_order_no,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales_order as $row) {
		$sales_order_arr[$row[csf('id')]]["sales_order_no"] = $row[csf('sales_order_no')];
		$sales_order_arr[$row[csf('id')]]["sales_booking_no"] = $row[csf('sales_booking_no')];
	}

	if(trim($system_id)!="") $systm_cond=" and a.system_number like '%$system_id%' ";
	if(trim($barcode_no)!="") $search_field_cond.=" and a.barcode_no='$barcode_no'";
	if(trim($barcode_no)!="") $search_field_cond2.=" and b.barcode_no='$barcode_no'";
	if(trim($company_id)==0) { echo "Please insert Company First"; die;}

	$sql = "select a.id, system_number,a.roll_no,a.split_from_id,a.insert_date,a.company_id,a.order_id,a.barcode_no,a.roll_wgt, b.booking_without_order, b.booking_no, b.is_sales, b.po_breakdown_id, a.qty_in_pcs from pro_roll_split a, pro_roll_details b where  a.split_from_id=b.id and b.entry_form in(22,58,2,82,83,84,133,110,180,183) and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id $search_field_cond $date_cond $systm_cond order by system_number";
	//echo $sql;//die;
	$result = sql_select($sql);

	//this block for getting dtlsID for entry from 2 for Direct Print sticker
	$barcodeNos="";
	foreach ($result as $row)
	{
		$barcodeNos .=$row[csf('barcode_no')].",";
	}
	$barcodeNos=chop($barcodeNos,",");
	$production_dtlsId_arr=return_library_array( "select barcode_no, dtls_id from pro_roll_details where barcode_no in($barcodeNos) and entry_form in(2)",'barcode_no','dtls_id');
	//END this block for getting dtlsID for entry from 2 for Direct Print sticker


	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down",'id','po_number');
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="740" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="110">System No</th>
			<th width="120">Company Name</th>
			<th width="120">Order/FSO No</th>
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
				$is_sales=$row[csf('is_sales')];
				$order_no="";
				$booking_number="";
				if($row[csf('booking_without_order')]==1)
				{
					if($is_sales==1)
					{
						$booking_number=$sales_order_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
						$order_no=$sales_order_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
					}
					else
					{
						$booking_number=$row[csf('booking_no')];
					}
				}
				else
				{
					if($is_sales==1){
						$booking_number=$sales_order_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
						$order_no=$sales_order_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
					}else{
						$order_no=$order_arr[$row[csf('order_id')]];
					}
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('system_number')]."_".$row[csf('id')]."_".$row[csf('barcode_no')]."_".$row[csf('split_from_id')]."_".$row[csf('roll_wgt')]."_".$is_sales."_".$row[csf('qty_in_pcs')]."_".$production_dtlsId_arr[$row[csf('barcode_no')]]; ?>');">
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

if($action=="roll_details_update")
{
	//$data=explode("_",$data);

	//$sql=sql_select("select a.id,a.barcode_no,a.roll_no,a.qc_pass_qnty,b.id as dtls_id from  pro_roll_details a,  pro_grey_prod_entry_dtls b where b.id=a.dtls_id  and a.roll_split_from=$data and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  order by a.id" );

	$sql=sql_select("select a.id,a.barcode_no,a.roll_no,a.qc_pass_qnty, a.dtls_id, a.rf_id, a.qc_pass_qnty_pcs from  pro_roll_details a where a.roll_split_from=$data and  a.status_active=1 and a.is_deleted=0 order by a.id" );

	foreach ($sql as $val)
	{
		$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
	}

	$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
	$nxProcessedBarcode = array();
	if($splited_barcode)
	{
		$nxtProcessSql = sql_select("select a.id,a.barcode_no,a.roll_no from  pro_roll_details a where a.barcode_no in (".$splited_barcode.") and a.entry_form in (61,62,64,82,83,110,180,183,133) and a.status_active=1 and a.is_deleted=0 and a.roll_split_from = 0");
		foreach ($nxtProcessSql as $val2)
		{
			$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
		}

		$nxtProcessSql2 = sql_select("SELECT a.id, a.barcode_no, a.roll_wgt as original_wgt from PRO_ROLL_SPLIT a where a.barcode_no in (".$splited_barcode.") and a.entry_form =113 and a.status_active=1 and a.is_deleted=0");
		foreach ($nxtProcessSql2 as $val2)
		{
			$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
			$nxProcessedBarcodeWriginal_wgt[$val2[csf("barcode_no")]] = $val2[csf("original_wgt")];
		}
	}

	$i=1;
	foreach($sql as $row)
	{
		if($nxProcessedBarcode[$row[csf("barcode_no")]]) $readonly = "disabled"; else $readonly = "";

		if ($nxProcessedBarcodeWriginal_wgt[$row[csf("barcode_no")]]!="") {
			$qc_pass_qnty=$nxProcessedBarcodeWriginal_wgt[$row[csf("barcode_no")]];
		}
		else
		{
			$qc_pass_qnty=$row[csf('qc_pass_qnty')];
		}
		?>
		<tr id="tr_<? echo $i;  ?>" align="center" valign="middle">
			<td width="40" id="txtSl_<? echo $i;  ?>"><? echo $i;  ?></td>
			<td width="100" >
				<input type="text" name="roll_no[]" id="rollno_<? echo $i;  ?>" style="width:80px" class="text_boxes_numeric" onBlur="check_roll_no(<? echo $i;  ?>)" value="<? echo $row[csf('roll_no')] ;  ?>" disabled/>
			</td>
			<td width="60" >
				<input type="text" name="rollWgt[]" id="rollWgt_<? echo $i;  ?>" style="width:50px" class="text_boxes_numeric"   onBlur="check_qty(<? echo $i;  ?>)" value="<? echo $qc_pass_qnty ;  ?>" <? echo $readonly; ?> />
				<input type="hidden" name="hideRollWgt[]" id="hideRollWgt_<? echo $i;  ?>" style="width:50px" class="text_boxes_numeric"  value="<? echo $qc_pass_qnty ;  ?>" <? echo $readonly; ?> />
			</td>

			<td width="60" >
				<input type="text" name="qtyInPcs[]" id="qtyInPcs_<? echo $i;  ?>" style="width:50px" class="text_boxes_numeric" onBlur="check_qty_in_pcs(<? echo $i;  ?>)" value="<? echo $row[csf('qc_pass_qnty_pcs')] ;  ?>" <? echo $readonly; ?> />
				<input type="hidden" name="hiddenQtyInPcs[]" id="hiddenQtyInPcs_<? echo $i;  ?>" style="width:50px" class="text_boxes_numeric"  value="<? echo $row[csf('qc_pass_qnty_pcs')] ;  ?>" <? echo $readonly; ?> />
			</td>

			<td width="180" >
				<input type="text" name="barcodeNo[]" id="barcodeNo_<? echo $i;  ?>" style="width:150px" class="text_boxes" value="<? echo $row[csf('barcode_no')] ;  ?>"  placeholder="Display" readonly/>
			</td>
			<td width="100" >
            	<input type="text" name="rfidNo[]" id="rfidNo_<? echo $i;  ?>" style="width:100px" class="text_boxes"  value="<? echo $row[csf('rf_id')] ;  ?>" placeholder="Write/Scan" />
            </td>

			<td id="button_1" align="center">
				<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr(<? echo $i;  ?>)" />
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px;display:none" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i;  ?>);" />
				<input type="hidden" name="updateRollId[]" id="updateRollId_<? echo $i;  ?>" value="<? echo $row[csf('id')] ;  ?>"/>
				<input type="hidden" name="updateDtlsId[]" id="updateDtlsId_<? echo $i;  ?>" value="<? echo $row[csf('dtls_id')] ;  ?>"/>
			</td>
			<td> <input id="chkBundle_<? echo $i; ?>" type="checkbox" name="chkBundle"  >
			</td>
		</tr>
		<?
		$i++;
	}
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
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
	<div align="center" style="width:920px;">
		<form name="searchwofrm"  id="searchwofrm" autocomplete="off">
			<fieldset style="width:920px; margin-left:2px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="1000" border="1" rules="all" class="rpt_table">
					<thead>
						<th class="must_entry_caption">Company</th>
						<th>Job Year</th>
						<th>Job No</th>
						<th id="search_by_td_up" width="120">Order No</th>
						<th>Barcode No</th>
						<th>Sales Order No</th>
						<th>Booking Type</th>
						<th>Booking No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:80px" class="formbutton" />
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							echo create_drop_down( "cbo_company_id", 131, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3)  $company_cond order by comp.company_name","id,company_name", 1, "--Select Company--", 0, "" );
							?>
						</td>
						<td align="center">
							<?
							$selected_year=date("Y");
							echo create_drop_down( "cbo_year", 60, $year,"", 1, "--Select Year--", $selected_year, "",0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_job_no" id="txt_job_no" />
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />
						</td>
						<td><input type="text" name="barcode_no" id="barcode_no" style="width:100px" class="text_boxes" /></td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_sales_order_no" id="txt_sales_order_no" />
						</td>
						<td align="center">
							<?
							echo create_drop_down( "cbo_booking_type", 80, array('1'=>'With Order','2'=>'Without Order'),"", 0, "", $selected_year, "",0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:100px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" placeholder="Full Booking No" />
							<input type="checkbox" name="chkIsSales" id="chkIsSales"/> <label for="chkIsSales">Is sales
							order </label>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('barcode_no').value+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_sales_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('chkIsSales').checked+'_'+document.getElementById('cbo_booking_type').value, 'create_barcode_search_list_view', 'search_div', 'roll_splitting_beforeissue_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
						</td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px; margin-left:10px" id="search_div" ></div>
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

	//$search_string="%".trim($data[0])."%";
	$order_no=trim($data[0]);
	$job_no=trim($data[1]);
	$company_id =trim($data[2]);
	$barcode_no =trim($data[3]);
	$date =trim($data[4]);
	$sales_order_no = trim($data[5]);
	$booking_no =trim($data[6]);
	$is_sales = trim($data[7]);
	$booking_type = trim($data[8]);
	//print_r($data);

	if($company_id==0) { echo "Please Select Company First"; die;}

	$search_cond="";

	if( $order_no == "" && $job_no == ""  && $booking_no == "" && $sales_order_no =="" && $barcode_no =="")
	{
		echo "<b>Please search Atleast with one barcode</b>";
		die;
	}

	if(($booking_type == "2" && $booking_no != "") && ($order_no != "" || $job_no!= ""))
	{
		echo "Data Not Found";die;
	}


	if($order_no != "" || $job_no!= "" || ($booking_type == "1" && $booking_no != ""))
	{
		if($db_type==0)
		{
			$year_job_search=" and YEAR(a.insert_date)=$date";
			$year_job=" YEAR(a.insert_date) as year_job";

		}
		else if($db_type==2)
		{
			$year_job_search="  and to_char(a.insert_date,'YYYY')=$date";
			$year_job=" to_char(a.insert_date,'YYYY') as year_job";
		}

		if($order_no!="") $search_cond .=" and b.po_number like '%$order_no%'";
		if($job_no!="") $search_cond.=" and a.job_no_prefix_num = '$job_no'";
		if($booking_no!="") $search_cond .=" and c.booking_no like '%$booking_no%'";

		$po_book_ref_sql =  sql_select("select b.id as po_id, c.id as booking_id, 0 as booking_without_order, b.pub_shipment_date,a.job_no,c.booking_no, $year_job, po_number from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
				where a.company_name= $company_id $year_job_search $search_cond and a.job_no = b.job_no_mst and b.id = c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				order by c.id");
	}
	else if($booking_type == "2" && $booking_no != "")
	{
		if($db_type==0)
		{
			$year_job_search=" and YEAR(c.insert_date)=$date";

		}
		else if($db_type==2)
		{
			$year_job_search="  and to_char(c.insert_date,'YYYY')=$date";
		}

		if($booking_no!="") $search_cond =" and c.booking_no like '%$booking_no%'";
		$po_book_ref_sql =  sql_select("select null as po_id, c.id as booking_id, 1 as booking_without_order,c.booking_no
		from  wo_non_ord_samp_booking_mst c
		where c.company_id= $company_id $search_cond $year_job_search
		order by c.id");
	}

	/*if(count($po_book_ref_sql)==0){
		echo "Data Not Found";die;
	}*/

	foreach ($po_book_ref_sql as $val)
	{
		if($val[csf("booking_without_order")] ==0)
		{
			$po_booking_id_arr[$val[csf("po_id")]] = $val[csf("po_id")];
			$po_booking_type[$val[csf("booking_without_order")]] = $val[csf("booking_without_order")];
			$booking_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];

			$job_ref_arr[$val[csf("booking_id")]]['year_job']  =   $val[csf("year_job")];
			$job_ref_arr[$val[csf("booking_id")]]['pub_shipment_date']  =   $val[csf("pub_shipment_date")];
			$job_ref_arr[$val[csf("booking_id")]]['job_no']  =   $val[csf("job_no")];

			$po_ref_arr[$val[csf("po_id")]]['year_job']  =   $val[csf("year_job")];
			$po_ref_arr[$val[csf("po_id")]]['pub_shipment_date']  =   $val[csf("pub_shipment_date")];
			$po_ref_arr[$val[csf("po_id")]]['job_no']  =   $val[csf("job_no")];
			$po_ref_arr[$val[csf("po_id")]]['po_number']  =   $val[csf("po_number")];
		}
		else
		{
			$po_booking_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			$job_ref_arr[$val[csf("booking_id")]]['booking_no']  =   $val[csf("booking_no")];
			$po_booking_type[$val[csf("booking_without_order")]] = $val[csf("booking_without_order")];
			$booking_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
	}

	$po_booking_id_arr = array_filter(array_unique($po_booking_id_arr));
	if(count($po_booking_id_arr)>0)
	{
		$all_po_booking_ids = implode(",", $po_booking_id_arr);
		$poCond = $all_po_booking_cond = "";

		if($db_type==2 && count($po_booking_id_arr)>999)
		{
			$po_booking_id_arr_chunk=array_chunk($po_booking_id_arr,999) ;
			foreach($po_booking_id_arr_chunk as $chunk_arr)
			{
				$poCond.=" a.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_po_booking_cond.=" and (".chop($poCond,'or ').")";

		}
		else
		{
			$all_po_booking_cond=" and a.po_breakdown_id in($all_po_booking_ids)";
		}

		$booking_without_order_flag = implode(",", $po_booking_type);
		$all_po_booking_cond = $all_po_booking_cond. " and a.booking_without_order=".$booking_without_order_flag;
	}



	$booking_id_arr = array_filter(array_unique($booking_id_arr));
	if(count($booking_id_arr)>0)
	{
		$all_booking_ids = implode(",", $booking_id_arr);
		$bookCond = $all_booking_ids_cond = "";

		if($db_type==2 && count($po_booking_id_arr)>999)
		{
			$po_booking_id_arr_chunk=array_chunk($po_booking_id_arr,999) ;
			foreach($po_booking_id_arr_chunk as $chunk_arr)
			{
				$bookCond.=" a.booking_id in(".implode(",",$chunk_arr).") or ";
			}

			$all_booking_ids_cond.=" and (".chop($bookCond,'or ').")";

		}
		else
		{
			$all_booking_ids_cond=" and a.booking_id in($all_booking_ids)";
		}


		$booking_without_order_flag = implode(",", $po_booking_type);
		if($booking_without_order_flag == 1){
			$book_type_cond = "and a.booking_without_order=1";
		}
		else
		{
			$book_type_cond = " and a.booking_without_order<>1";
		}

		$all_booking_ids_cond = $all_booking_ids_cond . $book_type_cond;
	}


	if($barcode_no!="") $barcode_cond="and a.barcode_no='$barcode_no'";

	$fabricLibraryData = sql_select("select auto_update from variable_settings_production where company_name =$company_id and variable_list in(15) and item_category_id=13 and is_deleted=0 and status_active=1");

    $fabric_store_auto_update=$fabricLibraryData[0][csf("auto_update")];

    if($fabric_store_auto_update == 1)
	{
		$addi_entry_form = "2";
	}
	else
	{
		$addi_entry_form = "58";
	}

	if($sales_order_no != ''|| $is_sales == 'true')
	{
		if($db_type==0)
		{
			$sales_year_cond=" and YEAR(a.insert_date)=$data[4]";
		}
		else if($db_type==2)
		{
			$sales_year_cond="  and to_char(a.insert_date,'YYYY')=$data[4]";
		}
		if($sales_order_no!="") $sales_order_no_cond=" and a.job_no_prefix_num=$sales_order_no";
		$sales_flag = "1";
		$sales_order_sql  = sql_select("select a.id as sales_id,a.job_no,sales_booking_no as booking_no, a.within_group, a.po_buyer,a.po_job_no,a.booking_id, a.booking_without_order from fabric_sales_order_mst a where a.company_id = $company_id $sales_year_cond $sales_order_no_cond  $all_booking_ids_cond");

		foreach ($sales_order_sql as $val)
		{
			$sales_id_arr[$val[csf("sales_id")]] = $val[csf("sales_id")];
			$sales_order_ref_arr[$val[csf("sales_id")]]['booking_no'] = $val[csf("booking_no")];
			$sales_order_ref_arr[$val[csf("sales_id")]]['booking_id'] = $val[csf("booking_id")];
			$sales_order_ref_arr[$val[csf("sales_id")]]['within_group'] = $val[csf("within_group")];
			$sales_order_ref_arr[$val[csf("sales_id")]]['booking_without_order'] = $val[csf("booking_without_order")];
			$sales_order_ref_arr[$val[csf("sales_id")]]['job_no'] = $val[csf("job_no")];
		}

		$sales_id_arr = array_filter(array_unique($sales_id_arr));
		if(count($sales_id_arr)>0)
		{
			$all_sales_ids = implode(",", $sales_id_arr);
			$poCond = $all_sales_id_cond = "";

			if($db_type==2 && count($sales_id_arr)>999)
			{
				$sales_id_arr_chunk=array_chunk($sales_id_arr,999) ;
				foreach($sales_id_arr_chunk as $chunk_arr)
				{
					$poCond.=" a.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
				}

				$all_sales_id_cond.=" and (".chop($poCond,'or ').")";

			}
			else
			{
				$all_sales_id_cond=" and a.po_breakdown_id in($all_sales_ids)";
			}

			$barcode_sql = sql_select("select a.barcode_no, a.po_breakdown_id,a.booking_without_order, a.is_sales, a.entry_form from pro_roll_details a where entry_form in (22,$addi_entry_form,133) and a.re_transfer = 0 and a.status_active = 1 and a.is_deleted = 0 and a.is_sales=1 $barcode_cond $all_sales_id_cond");
		}
	}
	else
	{
		$barcode_sql = sql_select("select a.barcode_no, a.po_breakdown_id,a.booking_without_order, a.is_sales, a.entry_form from pro_roll_details a where entry_form in (22,$addi_entry_form,82,83,110,180,183) and a.re_transfer = 0 and a.status_active = 1 and a.is_deleted = 0 $barcode_cond $all_po_booking_cond");
	}

	if(count($barcode_sql)==0)
	{
		echo "<b>Barcode Not Found</b>";die;
	}

	foreach ($barcode_sql as $val)
	{
		$barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		$barcode_ref_arr[$val[csf("barcode_no")]]['po_breakdown_id'] = $val[csf("po_breakdown_id")];
		$barcode_ref_arr[$val[csf("barcode_no")]]['booking_without_order'] = $val[csf("booking_without_order")];
		$barcode_ref_arr[$val[csf("barcode_no")]]['is_sales'] = $val[csf("is_sales")];
		$barcode_ref_arr[$val[csf("barcode_no")]]['entry_form'] = $val[csf("entry_form")];

		if($val[csf("is_sales")] == 1)
		{
			$other_ref_fso[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}
		else
		{
			if($val[csf("booking_without_order")] ==1)
			{
				$other_ref_non_book[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}
			else
			{
				$other_ref_po[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}
		}
	}

	//If search with only FSO / Barcode

	if(count($po_book_ref_sql)==0)
	{
		$other_ref_fso = array_filter(array_unique($other_ref_fso));
		if($db_type==0)
		{
			$year_cond=" and YEAR(a.insert_date)=$data[4]";
		}
		else if($db_type==2)
		{
			$year_cond="  and to_char(a.insert_date,'YYYY')=$data[4]";
		}

		if(count($other_ref_fso)>0)
		{
			$other_sales_order_sql  = sql_select("select a.id as sales_id,a.job_no,sales_booking_no as booking_no, a.within_group, a.po_buyer,a.po_job_no,a.booking_id, a.booking_without_order from fabric_sales_order_mst a where a.company_id = $company_id $year_cond and a.id in (".implode(',', $other_ref_fso).") ");

			foreach ($other_sales_order_sql as $val)
			{
				$sales_order_ref_arr[$val[csf("sales_id")]]['booking_no'] = $val[csf("booking_no")];
				$sales_order_ref_arr[$val[csf("sales_id")]]['booking_id'] = $val[csf("booking_id")];
				$sales_order_ref_arr[$val[csf("sales_id")]]['within_group'] = $val[csf("within_group")];
				$sales_order_ref_arr[$val[csf("sales_id")]]['booking_without_order'] = $val[csf("booking_without_order")];
				$sales_order_ref_arr[$val[csf("sales_id")]]['job_no'] = $val[csf("job_no")];
			}
		}

		$other_ref_non_book = array_filter(array_unique($other_ref_non_book));

		if(count($other_ref_non_book)>0)
		{
			$other_ref_non_book_sql =  sql_select("select null as po_id, a.id as booking_id, 1 as booking_without_order,a.booking_no from  wo_non_ord_samp_booking_mst a where a.company_id= $company_id $year_cond and a.id in (".implode(',', $other_ref_non_book).") order by a.id");

			foreach ($other_ref_non_book_sql as $val)
			{
				$job_ref_arr[$val[csf("booking_id")]]['booking_no']  =   $val[csf("booking_no")];
			}
		}

		$other_ref_po = array_filter(array_unique($other_ref_po));

		if(count($other_ref_po)>0)
		{
			if($db_type==0)
			{
				$year_job=" YEAR(a.insert_date) as year_job";

			}
			else if($db_type==2)
			{
				$year_job=" to_char(a.insert_date,'YYYY') as year_job";
			}
			$other_ref_po_sql = sql_select("select b.id as po_id, c.id as booking_id, 0 as booking_without_order, b.pub_shipment_date,a.job_no,c.booking_no, $year_job, po_number from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c
				where a.company_name= $company_id  and a.job_no = b.job_no_mst and b.id = c.po_break_down_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id in (".implode(',', $other_ref_po).")
				order by c.id");

			foreach ($other_ref_po_sql as $val)
			{
				$po_ref_arr[$val[csf("po_id")]]['year_job']  =   $val[csf("year_job")];
				$po_ref_arr[$val[csf("po_id")]]['pub_shipment_date']  =   $val[csf("pub_shipment_date")];
				$po_ref_arr[$val[csf("po_id")]]['job_no']  =   $val[csf("job_no")];
				$po_ref_arr[$val[csf("po_id")]]['po_number']  =   $val[csf("po_number")];
			}
		}

	}

	$barcode_arr = array_filter(array_unique($barcode_arr));
	if(count($barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $barcode_arr);
		$barCond = $all_barcode_nos_cond = "";

		if($db_type==2 && count($barcode_arr)>999)
		{
			$barcode_arr_chunk=array_chunk($barcode_arr,999) ;
			foreach($barcode_arr_chunk as $chunk_arr)
			{
				$barCond.=" c.barcode_no in(".implode(",",$chunk_arr).") or ";
			}

			$all_barcode_nos_cond.=" and (".chop($barCond,'or ').")";

		}
		else
		{
			$all_barcode_nos_cond=" and c.barcode_no in($all_barcode_nos)";
		}
	}

	//Split Barcode Checked
	$scanned_barcode_arr=array();
	/* $barcodeData=sql_select( "select c.barcode_no from pro_roll_split c where  c.status_active=1 and c.is_deleted=0 and c.entry_form=113 $all_barcode_nos_cond");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	} */

	$barcodeData=sql_select("select c.barcode_no, c.system_number from pro_roll_split c, pro_roll_details b where c.split_from_id=b.id and c.entry_form=113 and c.status_active=1 and c.is_deleted=0 and b.re_transfer=0 $all_barcode_nos_cond");

	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	// Issued Barcode Checked
	$barcodeData=sql_select( "select c.barcode_no from pro_roll_details c where  c.entry_form=61 and c.status_active=1 and c.is_returned!=1 and c.is_deleted=0 $all_barcode_nos_cond");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}



	if($db_type==0)
	{
		$year_field=" YEAR(a.insert_date) as year";

	}
	else if($db_type==2)
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";
	}

	$result = sql_select("select a.id,$year_field, a.recv_number_prefix_num, a.recv_number, a.receive_basis,a.booking_id,a.booking_no, a.company_id,c.is_sales, c.roll_split_from, a.knitting_source , a.knitting_company, a.receive_date, c.barcode_no, c.roll_no, c.qc_pass_qnty from inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id = b.mst_id and b.id = c.dtls_id and a.id = c.mst_id and c.status_active = 1 and c.is_deleted =0 and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and c.entry_form in (2,22) $all_barcode_nos_cond");



	foreach ($result as  $val)
	{
		if($val[csf("receive_basis")] == 2)
		{
			$program_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
	}

	$program_arr = array_filter(array_unique($program_arr));
	if(count($program_arr)>0)
	{
		$all_program_nos = implode(",", $program_arr);
		$barCond = $all_program_nos_cond = "";

		if($db_type==2 && count($program_arr)>999)
		{
			$program_arr_chunk=array_chunk($program_arr,999) ;
			foreach($program_arr_chunk as $chunk_arr)
			{
				$barCond.=" b.id in(".implode(",",$chunk_arr).") or ";
			}

			$all_program_nos_cond.=" and (".chop($barCond,'or ').")";

		}
		else
		{
			$all_program_nos_cond=" and b.id in($all_program_nos)";
		}

		$program_booking_sql = sql_select("select a.booking_no,b.id as program_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id = b.mst_id and a.status_active = 1 and b.status_active =1 $all_program_nos_cond");

		foreach ($program_booking_sql as $val)
		{
			$program_booking_ref[$val[csf("program_no")]] = $val[csf("booking_no")];
		}
	}


	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" align="center">
		<thead>
			<th width="30">SL</th>
			<th width="50">Receive No</th>
			<th width="50">Year</th>
			<th width="70">Receive date</th>
			<th width="80">Job No</th>
			<th width="50">Job Year</th>
			<th width="140">Order/FSO No</th>
			<th width="120">Booking No</th>
			<th width="60">Within Group</th>
			<th width="70">Shipment Date</th>
			<th width="80">Barcode No</th>
			<th width="50">Roll No</th>
			<th width="50">Roll Qty.</th>
			<th>Roll Type</th>
		</thead>
	</table>
	<div style="width:1000px; max-height:210px; overflow-y:scroll" id="list_container_batch" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{

					/*if($row[csf("receive_basis")] == 2)
					{
						$booking_no = $program_booking_ref[$row[csf("booking_id")]];
					}
					else if($row[csf("receive_basis")] == 1)
					{
						$booking_no =$row[csf("booking_no")];
					}*/

					$po_breakdown_id = $barcode_ref_arr[$row[csf("barcode_no")]]['po_breakdown_id'];
					$booking_without_order = $barcode_ref_arr[$row[csf("barcode_no")]]['booking_without_order'];
					$present_entry_form = $barcode_ref_arr[$row[csf("barcode_no")]]['entry_form'];


					if($row[csf("is_sales")] == 1 )
					{
						$within_group = $sales_order_ref_arr[$po_breakdown_id]['within_group'];
						$booking_id = $sales_order_ref_arr[$po_breakdown_id]['booking_id'];
						$sales_booking_without_order = $sales_order_ref_arr[$po_breakdown_id]['booking_without_order'];
						$ponumber = $sales_order_ref_arr[$po_breakdown_id]['job_no'];
						$within_group_con=($within_group == 1)?"Yes":"No";
						if($within_group =='1')
						{
							if($sales_booking_without_order =="1")
							{
								$booking_no = $sales_order_ref_arr[$po_breakdown_id]['booking_no'];
							}
							else
							{
								$job_year =  $job_ref_arr[$booking_id]['year_job'];
								$shipment_date = $job_ref_arr[$booking_id]['pub_shipment_date'];
								$job_no = $job_ref_arr[$booking_id]['job_no'];
								$booking_no = $sales_order_ref_arr[$po_breakdown_id]['booking_no'];
							}

						}
						else
						{
							$booking_no = $sales_order_ref_arr[$po_breakdown_id]['booking_no'];
						}


					}
					else
					{
						if($booking_without_order ==1)
						{
							//$barcode_ref_arr[$row[csf("barcode_no")]]['po_breakdown_id'];
							$booking_no = $job_ref_arr[$po_breakdown_id]['booking_no'];

						}
						else
						{
							$job_year = $po_ref_arr[$po_breakdown_id]['year_job'];
							$shipment_date = $po_ref_arr[$po_breakdown_id]['pub_shipment_date'];
							$job_no = $po_ref_arr[$po_breakdown_id]['job_no'];
							$ponumber = $po_ref_arr[$po_breakdown_id]['po_number'];

							if($present_entry_form ==58 && $row[csf("receive_basis")] ==2)
							{
								$booking_no = $program_booking_ref[$row[csf("booking_id")]];
							}
						}

					}

					$split_roll="";
					if($row[csf('roll_split_from')]!=0) $split_roll=" Splitted ";
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($sales_order_no != ''|| $is_sales == 'true') $sales_order= 1; else $sales_order=$row[csf("is_sales")]*1;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('barcode_no')].'_'.$sales_order; ?>')">
						<td width="30">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="50" align="center"><p><? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td width="80" align="center"><p><? echo $job_no; ?></p></td>
						<td width="50" align="center"><p><? echo $job_year; ?></p></td>
						<td width="140"><p><? echo $ponumber; ?></p></td>
						<td width="120"><p><? echo $booking_no; ?></p></td>
						<td width="60"><p><? echo $within_group_con; ?></p></td>
						<td width="70" align="center"><? echo $shipment_date; ?></td>
						<td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="50"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
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
/*
	echo "select a.barcode_no from pro_roll_details a where entry_form in (2,22,58,82,83,110,180,183,133) and a.re_transfer = 0 and a.status_active = 1 and a.is_deleted = 0 $all_po_booking_cond"; */



	/*if($db_type==0)
	{
		$year_field=" YEAR(a.insert_date) as year";
		$year_job=" YEAR(e.insert_date) as year_job";
		$year_job_search=" and YEAR(f.insert_date)=$data[4]";

	}
	else if($db_type==2)
	{
		$year_field=" to_char(a.insert_date,'YYYY') as year";
		$year_job=" to_char(e.insert_date,'YYYY') as year_job";
		$year_job_search="  and to_char(f.insert_date,'YYYY')=$data[4]";
	}



	$search_field_cond2="";
	if($order_no!="") $search_field_cond2=" and d.po_number like '%$order_no%'";
	if($job_no!="") $search_field_cond2.=" and e.job_no_prefix_num like '%$job_no%'";
	if($barcode_no!="") $search_field_cond2="and c.barcode_no='$barcode_no'";
	if($sales_order_no!="") $search_field_cond2=" and f.job_no_prefix_num=$sales_order_no";

	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_split where  status_active=1 and is_deleted=0 and entry_form=113");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	$barcodeData=sql_select( "select barcode_no from pro_roll_details where  entry_form=61 and status_active=1 and is_returned!=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	if($sales_order_no != ''|| $is_sales == 'true'){
		$sales_order = 1;
		if($booking_no!="") $search_field_cond2=" and f.sales_booking_no like '%$booking_no%'";
		$sql="SELECT a.id,a.recv_number_prefix_num,a.recv_number,$year_field,a.company_id,c.roll_split_from,a.knitting_source , a.knitting_company ,a.receive_date, c.barcode_no, c.roll_no, c.qc_pass_qnty, c.booking_no, c.booking_without_order,f.sales_booking_no,f.job_no sales_order_no,f.within_group,c.is_sales FROM inv_receive_master a,pro_grey_prod_entry_dtls b, pro_roll_details c,fabric_sales_order_mst f WHERE a.id=b.mst_id  and b.id=c.dtls_id and a.id=c.mst_id and c.po_breakdown_id=f.id and a.company_id=$company_id and a.entry_form in (58,22,2) and c.entry_form  in(58,22,2) and c.status_active=1 and c.is_deleted=0 and b.trans_id<>0 and c.barcode_no>0 $search_field_cond2 $year_job_search";
	}else{
		if($booking_no!="") $search_field_cond2=" and c.booking_no like '%$booking_no%'";
		$sql="SELECT e.job_no_prefix_num,$year_job,a.id,a.recv_number_prefix_num,a.recv_number,$year_field,a.company_id,c.roll_split_from,a.knitting_source , a.knitting_company ,a.receive_date, c.barcode_no, c.roll_no, c.qc_pass_qnty, d.po_number, d.pub_shipment_date, d.job_no_mst,c.booking_no, c.booking_without_order FROM inv_receive_master a,pro_grey_prod_entry_dtls b,  pro_roll_details c, wo_po_break_down d,wo_po_details_master e WHERE a.id=b.mst_id  and b.id=c.dtls_id and a.id=c.mst_id and c.po_breakdown_id=d.id and a.company_id=$company_id and a.entry_form in (58,22,2) and e.job_no=d.job_no_mst and c.entry_form  in(58,22,2) and c.status_active=1 and c.is_deleted=0 and b.trans_id<>0 and c.barcode_no>0 $search_field_cond2 ";
	}
	//echo $sql;
	$result = sql_select($sql);
	$booking_nos = "";
	foreach ($result as $row) {
		if ($sales_order == 1 && $row[csf('within_group')] == 1) {
			$sales_within_group = true;
			$booking_nos .= "'".$row[csf('sales_booking_no')] . "',";
		} else {
			$sales_within_group = false;
		}
	}
	$po_arr = array();
	if ($sales_order == 1) {
		if ($sales_within_group == true) {
			$booking_nos = implode(",",array_unique(explode(",", rtrim($booking_nos,","))));
			$po_info = sql_select("select b.job_no job_no_mst,b.booking_no,b.job_no, c.shipment_date,d.insert_date from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and b.job_no=d.job_no and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_no in($booking_nos) group by b.job_no,b.booking_no,b.job_no,c.shipment_date,d.insert_date");
			if (!empty($po_info)) {
				foreach ($po_info as $po_row) {
					$po_arr[$po_row[csf('booking_no')]]["job_no_mst"] = $po_row[csf('job_no_mst')];
					$po_arr[$po_row[csf('booking_no')]]["job_year"] = $po_row[csf('insert_date')];
					$po_arr[$po_row[csf('booking_no')]]["shipment_date"] = $po_row[csf('shipment_date')];
				}
			}
		}
	}
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1000" class="rpt_table" align="center">
		<thead>
			<th width="30">SL</th>
			<th width="50">Receive No</th>
			<th width="50">Year</th>
			<th width="70">Receive date</th>
			<th width="80">Job No</th>
			<th width="50">Job Year</th>
			<th width="140">Order/FSO No</th>
			<th width="120">Booking No</th>
			<th width="60">Within Group</th>
			<th width="70">Shipment Date</th>
			<th width="80">Barcode No</th>
			<th width="50">Roll No</th>
			<th width="50">Roll Qty.</th>
			<th>Roll Type</th>
		</thead>
	</table>
	<div style="width:1000px; max-height:210px; overflow-y:scroll" id="list_container_batch" >
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="980" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					$within_group_con=($row[csf('within_group')] == 1)?"Yes":"No";
					$within_group = $row[csf('within_group')];
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
						if ($sales_order == 1) {
							$booking_no=$row[csf('sales_booking_no')];
							$ponumber=$row[csf('sales_order_no')];
							if ($within_group == 1) {
								$job_no = $po_arr[$booking_no]["job_no_mst"];
								$shipment_date=change_date_format($po_arr[$booking_no]["shipment_date"]);
								$job_year = date("Y", strtotime($po_arr[$booking_no]["job_year"]));
							} else {
								$job_no = '';
								$shipment_date = '';
								$job_year = '';
							}
						}else{
							$ponumber=$row[csf('po_number')];
							$job_no=$row[csf('job_no_prefix_num')];
							$job_year=$row[csf('year_job')];
							$shipment_date=change_date_format($row[csf('pub_shipment_date')]);
							$booking_no='';
						}
					}

					$split_roll="";
					if($row[csf('roll_split_from')]!=0) $split_roll=" Splitted ";
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if($sales_order_no != ''|| $is_sales == 'true') $sales_order= 1; else $sales_order=0;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $row[csf('barcode_no')].'_'.$sales_order; ?>')">
						<td width="30">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="50" align="center"><p><? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
						<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
						<td width="80" align="center"><p><? echo $job_no; ?></p></td>
						<td width="50" align="center"><p><? echo $job_year; ?></p></td>
						<td width="140"><p><? echo $ponumber; ?></p></td>
						<td width="120"><p><? echo $booking_no; ?></p></td>
						<td width="60"><p><? echo $within_group_con; ?></p></td>
						<td width="70" align="center"><? echo $shipment_date; ?></td>
						<td width="80"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="50"><? echo $row[csf('roll_no')]; ?></td>
						<td width="50" align="right"><? echo number_format($row[csf('qc_pass_qnty')],2); ?></td>
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
*/
	exit();
}

if($action=="load_barcode_mst_form")
{
	$data = explode("_",$data);
	$barcode=trim($data[0]);
	$sales_order=trim($data[1]);
	$system_no=trim($data[2]);

	$company_arr = return_library_array("select id, company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$yean_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id", "yarn_count");

	$mother_rf_id=return_field_value("rf_id","pro_roll_details"," barcode_no='".$barcode."' and status_active=1 and is_deleted=0 and entry_form=2","rf_id");
	echo "document.getElementById('hidden_mother_rf_id').value = '".($mother_rf_id)."';\n";

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";

	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	/*$batch_details=sql_select("SELECT a.id,a.entry_form, b.company_id, b.to_company, b.transfer_criteria, a.barcode_no,a.mst_id,a.dtls_id,a.po_breakdown_id,a.roll_no,a.roll_id, a.qnty, a.booking_without_order, a.qc_pass_qnty_pcs, b.to_company, b.transfer_criteria
		from pro_roll_details a,  inv_item_transfer_mst b
		where b.id=a.mst_id and a.entry_form in(82,83,133,183,110,180) and re_transfer =0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no='".$barcode."'");*/


		$batch_details=sql_select("SELECT a.id,a.entry_form, b.company_id, b.to_company, b.transfer_criteria, a.barcode_no,a.mst_id,a.dtls_id,a.po_breakdown_id,a.roll_no,a.roll_id, a.qnty, a.booking_without_order, a.qc_pass_qnty_pcs, a.re_transfer
		from pro_roll_details a,  inv_item_transfer_mst b
		where b.id=a.mst_id and a.entry_form in(82,83,133,183,110,180) and re_transfer =0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no='".$barcode."'
		union all
		SELECT a.id,a.entry_form, b.company_id, 0 as to_company, 0 as transfer_criteria, a.barcode_no, a.mst_id, a.dtls_id, a.po_breakdown_id, a.roll_no, a.roll_id, a.qnty, a.booking_without_order, a.qc_pass_qnty_pcs, a.re_transfer
		from pro_roll_details a, inv_receive_master b
		where b.id=a.mst_id and a.entry_form in(84) and a.re_transfer =0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no='".$barcode."'");



	if( empty($batch_details) )
	{
		//echo "select a.id,a.entry_form, a.barcode_no,a.mst_id,a.dtls_id,a.po_breakdown_id,a.roll_no,a.roll_id, a.qc_pass_qnty, b.grey_receive_qnty, a.booking_without_order, a.qc_pass_qnty_pcs from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in(22,58,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.trans_id<>0 and a.barcode_no='".$barcode."'"; die;

		$batch_details=sql_select("SELECT a.id,a.entry_form, a.barcode_no, a.mst_id,a.dtls_id,a .po_breakdown_id, a.roll_no,a.roll_id, a.qnty, a.qc_pass_qnty, b.grey_receive_qnty, a.booking_without_order, a.qc_pass_qnty_pcs, a.re_transfer  from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.entry_form in(22,58,2) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  b.trans_id<>0 and a.barcode_no='".$barcode."'");

		if( $batch_details[0][csf('entry_form')]==22 || $batch_details[0][csf('entry_form')]==2)
			$mother_roll_id=$batch_details[0][csf('id')];
		else
			$mother_roll_id=$batch_details[0][csf('id')];//$batch_details[0][csf('roll_id')];
	}
	else
	{
		$mother_roll_id=$batch_details[0][csf('roll_id')];
	}


	if ($system_no!="")
	{
		$remarksData=return_field_value("remarks","pro_roll_split"," id=$system_no and status_active=1 and is_deleted=0","remarks");
	}

	if ($sales_order == 1)
	{
		$is_sales_order=1;
		$sql="SELECT a.id,c.entry_form,a.booking_without_order,a.booking_id,a.booking_no,b.id as grey_id, a.company_id,a.knitting_company,a.knitting_source, b.prod_id, b.body_part_id, b.febric_description_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.is_sales, c.booking_no as roll_booking_no, c.qc_pass_qnty_pcs, f.sales_booking_no,f.within_group,f.job_no sales_order_no, f.buyer_id, f.po_buyer, f.po_job_no
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst f
		WHERE a.id=b.mst_id and b.id=c.dtls_id and f.id=c.po_breakdown_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.id='".$mother_roll_id."' order by c.entry_form asc";
	}
	else
	{
		$sql="SELECT a.id,c.entry_form,a.booking_without_order,a.booking_id,a.booking_no,b.id as grey_id, a.company_id,a.knitting_company,a.knitting_source, b.prod_id, b.body_part_id, b.febric_description_id, b.yarn_lot, b.yarn_count, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.is_sales,c.booking_no as roll_booking_no, c.qc_pass_qnty_pcs
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.id='".$mother_roll_id."' order by c.entry_form asc";

	}
	// echo $sql;die;
	$data_array=sql_select($sql); //txt_original_wgt

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

		echo "$('#txt_bar_code_num').attr('disabled', 'disabled');\n";
		echo "document.getElementById('booking_without_order').value  = '".($batch_details[0][csf("booking_without_order")])."';\n";

		if ($batch_details[0][csf('entry_form')] == 82 && $batch_details[0][csf('transfer_criteria')] == 1)
		{
			$company = $batch_details[0][csf('to_company')];
		}
		elseif ($batch_details[0][csf('entry_form')] == 83 || $batch_details[0][csf('entry_form')] == 133 || $batch_details[0][csf('entry_form')] == 183 || $batch_details[0][csf('entry_form')] == 110 || $batch_details[0][csf('entry_form')] == 180 || $batch_details[0][csf('entry_form')] == 84)
		{
			$company = $batch_details[0][csf('company_id')];
		}
		else{
			$company = $val[csf("company_id")];
		}
		echo "document.getElementById('txt_company_name').value  = '".($company_arr[$company])."';\n";

		/*$remarksData=return_field_value("remarks","pro_roll_split"," company_id='".$val[csf("company_id")]."' and barcode_no='".$batch_details[0][csf("barcode_no")]."' and split_from_id='".$mother_roll_id."' and status_active=1 and is_deleted=0","remarks");*/
		echo "$('#txt_remarks').val('".$remarksData."');\n";

		echo "document.getElementById('hidden_program_no').value = '".($val[csf("roll_booking_no")])."';\n";
		echo "document.getElementById('hidden_po_breakdown_id').value = '".($batch_details[0][csf("po_breakdown_id")])."';\n";

		//if($val[csf("booking_without_order")]==1)
		if($batch_details[0][csf("booking_without_order")] ==1)
		{
			echo "document.getElementById('po_booking_td').innerHTML='Booking No';\n";
			echo "document.getElementById('txt_order_no').value = '".$val[csf("roll_booking_no")]."';\n";
			echo "document.getElementById('txt_job_no').value = '';\n";
			echo "document.getElementById('txt_buyer').value  = '';\n";
		}
		else
		{
			if($is_sales_order==1)
			{
				$data_array_sales=sql_select("select within_group, a.po_job_no, buyer_id, job_no as sales_order_no, po_buyer from fabric_sales_order_mst a where a.id =".$batch_details[0][csf("po_breakdown_id")]);

				echo "document.getElementById('po_booking_td').innerHTML='Order/FSO No';\n";
				if($data_array_sales[0][csf('within_group')] == 1)
				{
					echo "document.getElementById('txt_order_no').value = '".$data_array_sales[0][csf('sales_order_no')]."';\n";
					echo "document.getElementById('txt_job_no').value = '".$data_array_sales[0][csf('po_job_no')]."';\n";
					echo "document.getElementById('txt_buyer').value  = '".$buyer_name_array[$data_array_sales[0][csf('po_buyer')]]."';\n";
				}
				else
				{
					echo "document.getElementById('txt_order_no').value = '".$data_array_sales[0][csf('sales_order_no')]."';\n";
					echo "document.getElementById('txt_job_no').value  = '';\n";
					echo "document.getElementById('txt_buyer').value  = '".$buyer_name_array[$data_array_sales[0][csf('buyer_id')]]."';\n";
				}
			}else{
				$data_array_po=sql_select("SELECT a.job_no, a.buyer_name, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id=".$batch_details[0][csf("po_breakdown_id")]."");
				echo "document.getElementById('po_booking_td').innerHTML='Order No';\n";
				echo "document.getElementById('txt_order_no').value = '".$data_array_po[0][csf('po_number')]."';\n";
				echo "document.getElementById('txt_job_no').value = '".$data_array_po[0][csf('job_no')]."';\n";
				echo "document.getElementById('txt_buyer').value  = '".$buyer_name_array[$data_array_po[0][csf('buyer_name')]]."';\n";
			}

		}

		if($val[csf("entry_form")]==22 || $val[csf("entry_form")]==2)
		{
			echo "document.getElementById('txt_original_wgt').value = '".($batch_details[0][csf("qnty")])."';\n";
			echo "document.getElementById('hidden_roll_wgt').value  = '".($batch_details[0][csf("qnty")])."';\n";

			echo "document.getElementById('txt_original_pcs').value = '".($val[csf("qc_pass_qnty_pcs")])."';\n";
			echo "document.getElementById('hidden_original_pcs').value = '".($val[csf("qc_pass_qnty_pcs")])."';\n";
		}
		else
		{
			echo "document.getElementById('txt_original_wgt').value = '".($batch_details[0][csf("qc_pass_qnty")])."';\n";
			echo "document.getElementById('hidden_roll_wgt').value  = '".($batch_details[0][csf('qc_pass_qnty')])."';\n";

			echo "document.getElementById('txt_original_pcs').value = '".($batch_details[0][csf("qc_pass_qnty_pcs")])."';\n";
			echo "document.getElementById('hidden_original_pcs').value = '".($batch_details[0][csf("qc_pass_qnty_pcs")])."';\n";
		}

		echo "document.getElementById('hidden_company_id').value = '".($company)."';\n";
		echo "document.getElementById('hidden_roll_mst').value  = '".($batch_details[0][csf("roll_no")])."';\n";
		echo "document.getElementById('hidden_entry_form').value  = '".($batch_details[0][csf("entry_form")])."';\n";
		echo "document.getElementById('hidden_rollId').value  = '".($batch_details[0][csf("roll_id")])."';\n";
		echo "document.getElementById('hidden_table_id').value = '".($batch_details[0][csf("id")])."';\n";

		echo "document.getElementById('hidden_dtls_id').value = '".($batch_details[0][csf("dtls_id")])."';\n";
		echo "document.getElementById('hidden_mst_id').value = '".($batch_details[0][csf("mst_id")])."';\n";
		echo "document.getElementById('hidden_barcode').value = '".($barcode)."';\n";
		echo "document.getElementById('hidden_is_sales').value = '".($sales_order)."';\n";
		echo "document.getElementById('hidden_re_transfer').value = '".($batch_details[0][csf("re_transfer")])."';\n";

		echo "document.getElementById('txt_original_roll').value = '".($batch_details[0][csf('roll_no')])."';\n";
		if($batch_details[0][csf("entry_form")]==82 || $batch_details[0][csf("entry_form")]==83 || $batch_details[0][csf("entry_form")]==133 || $batch_details[0][csf("entry_form")]==110 || $batch_details[0][csf("entry_form")]==180 || $batch_details[0][csf("entry_form")]==183 || $batch_details[0][csf("entry_form")]==84)
		{
			echo "document.getElementById('hidden_transfer_mother_roll').value = '".($val[csf("entry_form")])."';\n";
		}
		echo "load_drop_down('requires/roll_splitting_beforeissue_controller', document.getElementById('hidden_company_id').value, 'load_print_button', 'button_list');\n";
		exit();
	}
}

if($action=="load_print_button")
{


	$print_report_format_arr=return_library_array("select template_name, format_id from lib_report_template where  module_id=6 and report_id=146 and is_deleted=0 and status_active=1 and template_name=$data", "template_name", "format_id");



	$report_id=explode(",",$print_report_format_arr[$data]);
	//	print_r($report_id);

		foreach($report_id as $res){

			if($res==317){

			echo "<input type='button' value='Barcode 128 v2' id='barcode_generation_128' class='formbutton' onClick='fnc_bundle_report(2)'/>";
			}elseif($res==334){

				echo "<input type='button' value='Barcode Generation' id='barcode_generation' class='formbutton' onClick='fnc_bundle_report(1)'/>";
			}elseif($res==322){

				echo "<input type='button' value='Barcode N' id='barcode_n' class='formbutton' onClick='fnc_bundle_report(3)'/>";
			}elseif($res==320){
				echo "<input type='button' value='Direct Print' id='direct_print' name='direct_print' class='formbutton' onClick='fnc_bundle_report(4)'/>";
			}elseif($res==331){
				echo "<input type='button' value='Barcode 128 v3' id='btn_barcode_128v3' name='btn_barcode_128v3' class='formbutton' onClick='fnc_bundle_report(5)'/>";
			}elseif($res==72){
				echo "<input type='button' value='Direct Print 6' id='btn_barcode_direct6' name='btn_barcode_direct6' class='formbutton' onClick='fnc_bundle_report(6)'/>";
			}
			elseif($res==810){
				echo "<input type='button' value='Barcode CCL' id='btn_barcode_direct7' name='btn_barcode_direct7' class='formbutton' onClick='fnc_bundle_report(7)'/>";
			}
			elseif($res==880){
				echo "<input type='button' value='Barcode 128 v3 NZ' id='btn_barcode_v128_nz' name='btn_barcode_v128_nz' class='formbutton' onClick='fnc_bundle_report(8)'/>";
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
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');

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

 	$system_no=return_field_value("system_number_prefix_num", "pro_roll_split", "roll_id IN(".implode(",", array_unique($roll_id_arr)).") and entry_form=113");

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
	//echo $program_no.'system';

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

			/*if ($row[csf("receive_basis")] == 2)
			{
				$planning_booking_sql = sql_select("select a.booking_no_prefix_num from wo_booking_mst a,ppl_planning_entry_plan_dtls b where a.booking_no=b.booking_no and b.dtls_id='" . $row[csf('booking_id')] . "'");
			}*/

			$po_sql = sql_select("select a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.po_number,d.booking_no_prefix_num from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.booking_type=1 and d.is_short=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($order_id)");
			foreach ($po_sql as $row1)
			{
				$po_array[$row1[csf('id')]]['no'] = $row1[csf('po_number')];
				$po_array[$row1[csf('id')]]['job_no'] = $row1[csf('job_no')];
				$po_array[$row1[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				// if ($row[csf("receive_basis")] == 2)
				// {
				// 	$po_array[$row1[csf('id')]]['booking_no'] = $planning_booking_prefix;
				// }
				// else
				// {
				$po_array[$row1[csf('id')]]['booking_no'] = $row1[csf('booking_no_prefix_num')];
				//}

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
		$pdf->Write(0, "RRS:".$system_no. ",CRL No:" . $row[csf('roll_no')] .",ID:" .$user_arr[$row[csf('inserted_by')]]);

		$k++;
		$br++;
	}

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='splittingbeforeissue_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_one_128_v3") // Barcode N
{
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');


	$userid=$_SESSION['logic_erp']['user_id'];
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$brand_id_arr = return_library_array("select lot, brand from product_details_master where item_category_id=1", 'lot', 'brand');
	///print_r($brand_id_arr['6112018']);die;
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

	$sql = "SELECT a.company_id,a.receive_basis,a.booking_id,a.recv_number, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name,b.body_part_id,b.grey_receive_qnty_pcs from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";

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
	$recv_number = '';
	$booking_without_order = '';
	$yarn_lot = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row)
	{

		$body_part_name = $body_part[$row[csf('body_part_id')]];
		if ($row[csf('knitting_source')] == 1)
		{
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		}
		else if ($row[csf('knitting_source')] == 3)
		{
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}

		$receive_date=$row[csf('receive_date')];
		$booking_no = $row[csf('booking_no')];
		$recv_number_ex = explode("-", $row[csf('recv_number')]);
		$recv_number = $recv_number_ex[2]."-".$recv_number_ex[3];
		$booking_without_order = $row[csf('booking_without_order')];
		$qtyInPcs = $row[csf('grey_receive_qnty_pcs')];

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		$job_no = return_field_value("job_no_mst", "wo_po_break_down", "id in(" . $row[csf("order_id")] . ")");
		//echo "SELECT job_no_mst from wo_po_break_down where id in('" . $row[csf("order_id")] . "')";die;
		//echo $job_no.'='.$row[csf("order_id")].'***';die;
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$operator_name = $row[csf('operator_name')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val)
		{
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$stitch_length = $row[csf('stitch_length')];
		$yarn_lot = $row[csf('yarn_lot')];

		$brand='';
		$lot_string = explode(",", $row[csf('yarn_lot')]);
		foreach ($lot_string as $val)
		{
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

		if ($row[csf("receive_basis")] == 2)
		{
			$machine_data = sql_select("SELECT machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("SELECT a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg,a.body_part_id from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];

			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			// $fabric_typee = array(1 => "Open Width", 2 => "Tubular", 3 => "Needle Open");
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$tube_type_nm = '';
			if($tube_type=="Open Width")
			{
				$tube_type_nm = "O";
			}
			elseif ($tube_type=="Tubular")
			{
				$tube_type_nm = "T";
			}
			else
			{
				$tube_type_nm = "NO";
			}

		}
		else
		{
			$machine_data = sql_select("select machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
			$program_no = $row[csf('booking_id')];
		}

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
		if ($row[csf("receive_basis")] == 4) {
			$sales_info = sql_select("select a.job_no_prefix_num,a.job_no,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id='" . $row[csf("booking_id")] . "'");
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $sales_info[0][csf('buyer_id')]);
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_booking_mst", "booking_no='" . $sales_info[0][csf('sales_booking_no')] . "'");
			$order_no = $sales_info[0][csf('job_no')];
		} else {
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
		}
	}
	else
	{
		$is_salesOrder = 0;
		if ($row[csf("receive_basis")] == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $row[csf("booking_id")]);
		}
		if ($is_salesOrder == 1)
		{
			$po_sql = sql_select("SELECT a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id in($order_id)");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		}
		else
		{

			if ($row[csf("receive_basis")] == 2)
			{
				$planning_booking_sql = sql_select("SELECT a.booking_no_prefix_num from wo_booking_mst a,ppl_planning_entry_plan_dtls b where a.booking_no=b.booking_no and   b.dtls_id='" . $row[csf('booking_id')] . "'");
				$planning_booking_prefix=$planning_booking_sql[0][csf('booking_no_prefix_num')];

			}

			$po_sql = sql_select("SELECT a.job_no,a.job_no_prefix_num,a.buyer_name,b.id,b.po_number,d.booking_no_prefix_num from wo_po_details_master a, wo_po_break_down b,wo_booking_dtls c,wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.booking_type=1 and d.is_short=2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.id in($order_id)");
			foreach ($po_sql as $row1) {
				$po_array[$row1[csf('id')]]['no'] = $row1[csf('po_number')];
				$po_array[$row1[csf('id')]]['job_no'] = $row1[csf('job_no')];
				$po_array[$row1[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				if ($row[csf("receive_basis")] == 2)
				{
					$po_array[$row1[csf('id')]]['booking_no'] = $planning_booking_prefix;
				}
				else
				{
					$po_array[$row1[csf('id')]]['booking_no'] = $row1[csf('booking_no_prefix_num')];
				}

				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row1[csf('buyer_name')]);
			}
		}
	}
	$i = 1;
	$barcode_array = array();
	//$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, b.fabric_grade from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0])";
	$query = "SELECT a.id,a.inserted_by, a.roll_no, a.po_breakdown_id, a.coller_cuff_size,a.qc_pass_qnty_pcs,a.barcode_no, a.qnty, b.fabric_grade,c.shift_name,d.recv_number_prefix_num from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no left join pro_grey_prod_entry_dtls c on a.dtls_id=c.id left join inv_receive_master d on c.mst_id=d.id where a.id in($data)";



	$res = sql_select($query);

	$sl_yc = "SL-".$stitch_length."::C-".substr($yarn_count,0,30);
	$body_cons = $body_part_name."::".substr($constuction,0,20);

	$pdf=new PDF_Code128('P','mm',array(60,40));
	$pdf->SetAutoPageBreak(false);
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',7);


	$i=1; $j=1; $k=0; $br=0; $n=0;
	foreach ($res as $row)
	{
		$coller_cuff_size = $row[csf('coller_cuff_size')];
		$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];
		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=1; $j=1; $k=0;
		}


		$pdf->Code128($i+3,$j,$row[csf("barcode_no")],51,7);
		$pdf->SetXY($i, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")]."R".$row[csf('roll_no')]. "    :: Pcs-".number_format($row[csf("qc_pass_qnty_pcs")], 0). "    :: WT-".number_format($row[csf('qnty')], 2, '.', ''));

		$pdf->Line(0, 13,300, 13);

		/*$pdf->SetXY($i, $j+11);
		$pdf->Write(0, "----------------------------------------");*/

		$pdf->SetXY($i, $j+14);
		$pdf->Write(0, $job_no."::" . $buyer_name ."::" . $machine_dia_width . "X" . $machine_gauge. "::" . $coller_cuff_size);

		$pdf->SetXY($i, $j+17);
		$pdf->Write(0, "D-".$finish_dia . "/". $tube_type_nm . ":: GSM-". $gsm. "::" . substr($color, 0, 23));//24

		$pdf->Line(0, 20,300, 20);

		$pdf->SetXY($i, $j+21);
		$pdf->Write(0, $body_cons);
		// $pdf->Write(0, $body_part_name."::".$constuction);

		$pdf->SetXY($i, $j+24);
		$pdf->Write(0, substr($comp, 0, 45));

		$pdf->SetXY($i, $j+27);
		$pdf->Write(0, $sl_yc);
		// $pdf->SetXY($i+$sl_width, $j+27);
		// $pdf->Write(0, "::C-".substr($yarn_count,0,30));
		$pdf->SetXY($i, $j+30);
		$pdf->Write(0, "L-".$yarn_lot);

		$pdf->Line(0, 33,300, 33);

		$pdf->SetXY($i, $j+35);
		$pdf->Write(0, $party_name);
		$pdf->SetXY($i+15, $j+35);
		$pdf->Write(0, "::KP-" . $program_no);
		$pdf->SetXY($i+35, $j+35);
		$pdf->Write(0, "::PI-" . $recv_number);

		$k++;
		$br++;
	}


	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='splittingbeforeissue_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "direct_print_barcode")
{
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	$sql = "select a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name, b.shift_name, b.body_part_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
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
	foreach ($result as $row)
	{
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

		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		// $prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
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
			//$yarn_typeId = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
			//echo "select yarn_type from product_details_master where  id=".$row[csf('prod_id')]." ";
			//echo "select a.construction, b.copmposition_id,b.type_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')];
			//echo $yarn_typeId.'DD';
				$yarn_type_cond=$yarn_type[$yarn_typeId];
			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";

				//$yarn_type_cond .= $yarn_type[$d_row[csf('type_id')]];
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	/// yarn Type start booking_id
	$booking_id=$row[csf("booking_id")];
	$recieve_basis=$row[csf("receive_basis")];
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
		if ($row[csf("receive_basis")] == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=" . $row[csf("booking_id")]);
		}
		if ($is_salesOrder == 1) {
			$po_sql = sql_select("select a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,b.buyer_id from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id in($order_id)");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		} else {
			$po_sql = sql_select("select a.job_no, a.job_no_prefix_num, b.id,b.grouping, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id) group by a.job_no,a.job_no_prefix_num,b.id,b.grouping,b.po_number");
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

	$coller_cuff_query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size, b.fabric_grade from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.barcode_no in($data[2]) and A.ENTRY_FORM=2";
	$coller_cuff_data = sql_select($coller_cuff_query);
	$qc_pass_qnty_pcs="";$collerCuff_size="";
	foreach ($coller_cuff_data as $row)
	{
		// $qc_pass_qnty_pcs=$row[csf('qnty_pcs')];
		$collerCuff_size=$row[csf('coller_cuff_size')];
	}
	// echo $qc_pass_qnty_pcs;die;

	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size, b.fabric_grade from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0])";
	$res = sql_select($query);
	$pdf=new PDF_Code128('P','mm', array(65,55));
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);

	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);

	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row)
	{
		if ($is_salesOrder == 1) {
			$bookingNo = $po_array[$row[csf('po_breakdown_id')]]['sales_booking_no'];
		}

		$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];

		if ($row[csf("receive_basis")] == 1 && $booking_without_order=1)
		{
			$internal_ref=$non_internal_ref;
		}
		else
		{
			if($po_array[$row[csf('po_breakdown_id')]]['grouping']!="") $internal_ref=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
			else $internal_ref="";
		}

		$coller_cuff_size="";$qnty_pcs='';
		if ($body_part_type == 40)
		{
			// $qnty_pcs = "Qty:" . $qc_pass_qnty_pcs. " Pcs";
			$coller_cuff_size = "Collar; SZ: " . $collerCuff_size;
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
			// $coller_cuff_size = "Collar; SZ: " . $row[csf('coller_cuff_size')];
		}
		elseif ($body_part_type == 50)
		{
			// $qnty_pcs = "Qty:" . $qc_pass_qnty_pcs. " Pcs";
			$coller_cuff_size = "Cuff; SZ: " . $collerCuff_size;
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
			// $coller_cuff_size = "Cuff; SZ: " . $row[csf('coller_cuff_size')];
		}

		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}

		/*	if ($booking_without_order == 1) {
			$txt = $row[csf('barcode_no')] . "; " . $party_name . " Booking No." . $booking_no_prefix . ";<br>";
		} else {
			$txt = $row[csf('barcode_no')] . "; " . $party_name . " Job No." . $po_array[$row[csf('po_breakdown_id')]]['prefix'] . ";<br>";
		}
		$txt .= "M/C: " . $machine_name . "; M/C Dia X Gauge-" . $machine_dia_width . "X" . $machine_gauge . ";<br>";
		$txt .= "Date: " . $prod_date . ";<br>";
		$txt .= "Buyer: " . $buyer_name . ", Order No: " . $order_no . ";<br>";
		$txt .= $comp . "<br>";
		$txt .= "G/Dia: " . $grey_dia . "; SL: " . trim($stitch_length) . "; " . trim($tube_type) . "; F/Dia: " . trim($finish_dia) . ";<br>";
		$txt .= "GSM: " . $gsm . "; ";
		$txt .= $yarn_count . "; Lot: " . $yarn_lot . ";<br>";
		$txt .= "Prg: " . $program_no . "; Roll Wt: " . number_format($row[csf('qnty')], 2, '.', '') . " Kg;<br>";
		$txt .= "Custom Roll No: " . $row[csf('roll_no')] . ";";
		if (trim($color) != "") $txt .= " Color: " . trim($color) . ";<br>";

		if (trim($row[csf('fabric_grade')]) != "") $txt .= "Grade: " . trim($row[csf('fabric_grade')]) . ";";
		if ($operator_name != "") $txt .= "OP: " . $operator_name_arr[$operator_name] . ";";*/



		//if ($operator_name != "") $operator_name .= "OP: " . $operator_name_arr[$operator_name] ;
		// if ($operator_name != "") $operator_name = "; OP:" . $operator_name_arr[$operator_name];
		if ($operator_name != "") $operator_names = ";OP:" . $operator_name;

		$pdf->SetXY($i, $j);
		$pdf->Write(0, "WC:" . substr($party_name_full,0,45) );

		$pdf->SetXY($i, $j+3.5);
		$pdf->Write(0, "D:" . $prod_date ."; B:" . $buyer_name);

		$pdf->SetXY($i, $j+6.5);
		$pdf->Write(0, $company_short_name."; Job No." . $po_array[$row[csf('po_breakdown_id')]]['prefix']."; IR:".$internal_ref."; Sft:".$shift_name[$shift_name_id]);

		$pdf->SetXY($i, $j+9.5);
		$pdf->Write(0, "Po:" . substr($order_no,0,25) );

		$pdf->SetXY($i, $j+12.5);
		$pdf->Write(0, substr($comp,0,45) );

		$pdf->SetXY($i, $j+16);
		$pdf->Write(0, "F/GSM: " . $gsm.";Clr: " .substr($color, 0, 25));

		$pdf->SetXY($i, $j+19);
		$pdf->Write(0, "C:".$yarn_count . "; L: " . $yarn_lot);
		//$pdf->Write(0, "C: " . substr($gsm, 0, 25) .";Clr: " .substr($color, 0, 25));

		$pdf->SetXY($i, $j+22.5);
		$pdf->Write(0, "Br: " .  $brand .";T: " . $yarn_type_cond);

		$pdf->SetXY($i, $j+25.5);
		$pdf->Write(0, "M/C: " . $machine_name . "; DiaXGG-" . $machine_dia_width . "X" . $machine_gauge . "; B-" . $bookingNo);//24

		$pdf->SetXY($i, $j+28.5);
		$pdf->Write(0, "F/Dia: " . trim($finish_dia).";D/Type: " .trim($tube_type));

		$pdf->SetXY($i, $j+31.5);
		$pdf->Write(0, "SL: " . trim($stitch_length).";Prg: " .$program_no . $operator_names);

		$pdf->SetXY($i, $j+34.5);
		$pdf->Write(0, "Roll No: " . $row[csf('roll_no')] ."; Roll Wt: " . number_format($row[csf('qnty')], 2, '.', ''). " Kg;" . $qnty_pcs);


		$pdf->SetXY($i, $j+37.5);
		$pdf->Write(0, $row[csf("barcode_no")].";" .$coller_cuff_size);



		$pdf->Code128($i+1,$j+40.5,$row[csf("barcode_no")],50,8);

		$k++;
		$br++;
	}

	/*foreach (glob(""."*.pdf") as $filename) {
		@unlink($filename);
	}
	$name ='knitting_barcode_'.date('j-M-Y_h-iA').'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();*/

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='splittingbeforeissue_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_128_v3_nz")
{
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');


	$data = explode("***", $data);
	$user_arr = return_library_array("select id, user_name from user_passwd", 'id', 'user_name');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');

	$sql = "SELECT a.company_id,a.receive_basis,a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm,b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id,b.operator_name, b.shift_name, b.body_part_id from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and b.id=$data[1]";
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
	foreach ($result as $row)
	{
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

		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		// $prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
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
			$machine_data = sql_select("SELECT machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$planning_data = sql_select("SELECT a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg,a.booking_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];
			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$bookingNo = $planning_data[0][csf('booking_no')];
		} else {
			$machine_data = sql_select("SELECT machine_no, dia_width, gauge from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
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
			//$yarn_typeId = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
			//echo "select yarn_type from product_details_master where  id=".$row[csf('prod_id')]." ";
			//echo "select a.construction, b.copmposition_id,b.type_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')];
			//echo $yarn_typeId.'DD';
				$yarn_type_cond=$yarn_type[$yarn_typeId];
			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";

				//$yarn_type_cond .= $yarn_type[$d_row[csf('type_id')]];
			}
		}
		$company_short_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('company_id')]);
	}

	/// yarn Type start booking_id
	$booking_id=$row[csf("booking_id")];
	$recieve_basis=$row[csf("receive_basis")];
	if ($recieve_basis == 1) {
		if ($booking_without_order == 0) {
			$sql_yarn = "SELECT  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_id=$booking_id and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by  c.yarn_type";


		} else {
			$sql_yarn = "SELECT  c.yarn_type from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and  a.booking_no='$booking_no' and a.entry_form=3 and b.prod_id=c.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.item_category=1  and b.status_active=1 and b.is_deleted=0 and a.knit_dye_source in(1,3) group by c.yarn_type";
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

			$po_sql = sql_select("SELECT a.id,a.job_no_prefix_num,a.job_no as po_number,a.sales_booking_no,b.buyer_id, a.style_ref_no from fabric_sales_order_mst a inner join wo_booking_mst b on a.sales_booking_no = b.booking_no where a.id in($order_id)");
			foreach ($po_sql as $row) {
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
				$po_array[$row[csf('id')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
				$po_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
			}
		} else {
			$po_sql = sql_select("SELECT a.job_no, a.job_no_prefix_num, b.id,b.grouping, b.po_number, a.style_ref_no,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id) group by a.job_no,a.job_no_prefix_num,b.id,b.grouping,b.po_number");
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

	$coller_cuff_query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size, b.fabric_grade from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.barcode_no in($data[2]) and A.ENTRY_FORM=2";
	$coller_cuff_data = sql_select($coller_cuff_query);
	$qc_pass_qnty_pcs="";$collerCuff_size="";
	foreach ($coller_cuff_data as $row)
	{
		// $qc_pass_qnty_pcs=$row[csf('qnty_pcs')];
		$collerCuff_size=$row[csf('coller_cuff_size')];
	}
	// echo $qc_pass_qnty_pcs;die;

	$i = 1;
	$barcode_array = array();
	$query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty, a.qc_pass_qnty_pcs as qnty_pcs, a.coller_cuff_size, a.insert_date, a.inserted_by, b.fabric_grade from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0])";
	//echo $query;die;
	$res = sql_select($query);
	$pdf=new PDF_Code128('P','mm', array(65,50));
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',8);

	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin(0);

	$i=2; $j=3; $k=0; $br=0; $n=0;
	foreach ($res as $row)
	{
		if ($is_salesOrder == 1) {
			$bookingNo = $po_array[$row[csf('po_breakdown_id')]]['sales_booking_no'];
			$styleRefNo = $po_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
		}

		$order_no = $po_array[$row[csf('po_breakdown_id')]]['no'];

		if ($recieve_basis == 1 && $booking_without_order=1)
		{
			$internal_ref=$non_internal_ref;
		}
		else
		{
			if($po_array[$row[csf('po_breakdown_id')]]['grouping']!="") $internal_ref=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
			else $internal_ref="";
		}

		$coller_cuff_size="";$qnty_pcs='';
		if ($body_part_type == 40)
		{
			$coller_cuff_size = "Collar; SZ: " . $collerCuff_size;
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
		}
		elseif ($body_part_type == 50)
		{
			$coller_cuff_size = "Cuff; SZ: " . $collerCuff_size;
			$qnty_pcs = "Qty:" . $row[csf('qnty_pcs')]. " Pcs";
		}

		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=3; $k=0;
		}

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
		$pdf->Write(0, "Style: " . $styleRefNo);

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


	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='splittingbeforeissue_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if ($action == "print_barcode_one_128_v4")  // Barcode 128 v3 button production and split same, Tipu
{
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');

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

	$sql="SELECT a.recv_number,a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.insert_date, b.color_range_id, b.operator_name, b.body_part_id, b.floor_id from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.status_active=1 and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	// echo $sql;die;
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
			$planning_data = sql_select("SELECT a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg, b.batch_no, b.dye_type, c.after_wash_gsm  from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $planning_data[0][csf('machine_dia')];
			$machine_gauge = $planning_data[0][csf('machine_gg')];
			//$program_batch_no = $planning_data[0][csf('batch_no')];
			$program_dye_type = $planning_data[0][csf('dye_type')];

			$row[csf("within_group")] = $planning_data[0][csf('within_group')];

			$program_no = $row[csf('booking_id')];
			$grey_dia = $planning_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$planning_data[0][csf('width_dia_type')]];
			$tube_typeID = $planning_data[0][csf('width_dia_type')];
			$afterWashGsm = $planning_data[0][csf('after_wash_gsm')];
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

	/*$coller_cuff_size_sql="SELECT c.coller_cuff_size, c.qc_pass_qnty_pcs, c.barcode_no
    from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.id in($data[0])";
    // echo $coller_cuff_size;
    $coller_cuff_size_res = sql_select($coller_cuff_size_sql);
    $coller_cuff_size_arr = array();
    foreach ($coller_cuff_size_res as $row)
	{
		$coller_cuff_size_arr[$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
		$coller_cuff_size_arr[$row[csf('barcode_no')]]['qc_pass_qnty_pcs'] = $row[csf('qc_pass_qnty_pcs')];
	}*/

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
		$order_no = $po_array[$row[csf('po_breakdown_id')]]['prefix'];
		$booking_no = $po_array[$row[csf('po_breakdown_id')]]['booking_no'];
		// $coller_cuff_size = $po_array[$row[csf('barcode_no')]]['coller_cuff_size'];
		// $qc_pass_qnty_pcs = $po_array[$row[csf('barcode_no')]]['qc_pass_qnty_pcs'];
		$recv_number=explode("-", $recv_number);
		$recv_number=$recv_number[2]."-".$recv_number[3];

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
			$coller_cuff_size = $collerCuff_size;
			$qc_pass_qnty_pcs = $row[csf('qnty_pcs')];
		}
		if($qc_pass_qnty_pcs==""){$qc_pass_qnty_pcs=0;}

		$ct_strLen=strlen($yarn_count);
		$totalLinelen=52;
		$avilLotLen=$totalLinelen-$ct_strLen;

		$insert_time = date('H:i', strtotime($row[csf('insert_date')]));

		$constr_strLen=strlen($constuction);
		$avilBrand=$totalLinelen-$constr_strLen;


		if ($tube_typeID==1)
		{
			$tube_type="O/W";
		}
		elseif ($tube_typeID==2)
		{
			$tube_type="Tube";
		}
		elseif ($tube_typeID==3)
		{
			$tube_type="N/O";
		}
		$finish_dia_strLen=strlen(trim($finish_dia).",". $tube_type.",GSM:". $gsm);
		$avilSL=$totalLinelen-$finish_dia_strLen;
		$all_gsm=$afterWashGsm;
		$op_name_strLen=strlen($op_name.",ID-" .$op_card_no);
		$avilop_name_strLen=$totalLinelen-$op_name_strLen;

		if($br==1)
		{
			$pdf->AddPage(); $br=0; $i=2; $j=1; $k=0;
		}


		$pdf->Code128($i+1,$j,$row[csf("barcode_no")],50,8);
		$pdf->SetXY($i, $j+10);
		$pdf->Write(0, $row[csf("barcode_no")]. ",Dt:".change_date_format($receive_date). ",S:".$shift_name[$row[csf('shift_name')]].",".$company_short_name." ".$insert_time);

		$pdf->SetXY($i, $j+13);
		//." B: " . $po_array[$row[csf('po_breakdown_id')]]['booking_no']
		$pdf->Write(0, $machine_name . "," . $machine_dia_width . "X" . $machine_gauge. ",RW:" . number_format($row[csf('qnty')], 2, '.', '') . ",S-" . $coller_cuff_size. ",P-" . number_format($qc_pass_qnty_pcs));

		$pdf->SetXY($i, $j+17);
		$pdf->Write(0, $buyer_name . ",B/N:" . $booking_no . ",Pg:" .$program_no.",FSO:". $order_no);//24

		$pdf->SetXY($i, $j+20);
		$pdf->Write(0, "Clr:" .substr($color, 0, 35).",".$program_dye_type);

		$pdf->SetXY($i, $j+23);
		//$pdf->Write(0, "Ct:".$yarn_count.",Lt:".$yarn_lot);
		$pdf->Write(0, substr($constuction, 0, 35).", Ct:".substr($yarn_count, 0, 15));

		$pdf->SetXY($i, $j+26);
		$pdf->Write(0, "Lt:".substr($yarn_lot, 0, $avilLotLen));

		$pdf->SetXY($i, $j+30);
		//$pdf->Write(0, $constuction.", Br:".$brand);
		$pdf->Write(0, substr($comp, 0, 45));

		$pdf->SetXY($i, $j+33);
		$pdf->Write(0, "Br:".substr($brand, 0, $avilBrand));
		//$pdf->Write(0, "F/D:" . trim($finish_dia).",". $tube_type.",GSM:". $gsm.",SL:" . trim($stitch_length));
		$pdf->SetXY($i, $j+37);
		$pdf->Write(0, "F/D:" . trim($finish_dia).",". $tube_type.",GSM:". $gsm."/".$all_gsm.",SL:".substr(trim($stitch_length), 0, $avilSL));

		$pdf->SetXY($i, $j+40);
		//$pdf->Write(0, "Op-". $op_name .",ID-" .$op_card_no.",P/ID-".$recv_number);
		$pdf->Write(0, "Op-". $op_name .",ID-" .$op_card_no.",P/ID-".substr($recv_number, 0, $avilop_name_strLen).", P.F- ".$floor_name);


		$k++;
		$br++;
	}

	foreach (glob("*".$userid.".pdf") as $filename) {
		@unlink($filename);
	}
	$name ='splittingbeforeissue_barcode_'.date('j-M-Y_h-iA').'_'.$userid.'.pdf';
	$pdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();
}

if($action=="report_barcode_text_file_sales")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary
	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$roll_id=sql_select("select roll_id,po_breakdown_id  from pro_roll_details where id in($data)");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$roll_id_arr[]=	$val[csf('roll_id')];
		$order_id_arr[]=$val[csf('po_breakdown_id')];
	}
	$sql="select a.company_id,a.receive_basis,a.location_id,a.booking_id,a.receive_date,a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.shift_name, b.insert_date,b.operator_name, b.color_range_id, b.floor_id,b.body_part_id from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.id in (".implode(",", array_unique($roll_id_arr)).")";
	//echo $sql;die;
	$result=sql_select($sql);
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
	$$floor_name = '';

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
		$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('location_id')]);
		//$color=$color_arr[$row[csf('color_id')]];
		$stitch_length=$row[csf('stitch_length')];
		$yarn_lot=$row[csf('yarn_lot')];
		$brand=$brand_arr[$row[csf('brand_id')]];
		$colorRange = $color_range[$row[csf('color_range_id')]];
		$floor_name = $floor_name_arr[$row[csf('floor_id')]];

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$yarn_count='';
		$count_id=explode(",",$row[csf('yarn_count')]);
		foreach($count_id as $val)
		{
			if($val>0)
			{
				if($yarn_count=="") $yarn_count=$count_arr[$val]; else $yarn_count.=",".$count_arr[$val];
			}
		}

		$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
		$machine_name = $machine_data[0][csf('machine_no')];
		// $machine_dia_width=$machine_data[0][csf('dia_width')];
		// $machine_gauge=$machine_data[0][csf('gauge')];
		$machine_dia_width = $row[csf('machine_dia')];
		$machine_gauge = $row[csf('machine_gg')];

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

	$sql_sales_job=array();
	$all_po_id=implode(",",array_unique($order_id_arr));
	$sql_sales_job=sql_select("select b.job_no as job_no_mst,b.booking_no, a.buyer_id,c.grouping, c.file_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.job_no,b.booking_no, a.buyer_id, c.grouping, c.file_no");

	foreach ($sql_sales_job as $sales_job_row) {
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["job_no_mst"] = $sales_job_row[csf('job_no_mst')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["buyer_id"] = $sales_job_row[csf('buyer_id')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["grouping"] = $sales_job_row[csf('grouping')];
		$sales_job_arr[$sales_job_row[csf('booking_no')]]["file_no"] = $sales_job_row[csf('file_no')];
	}
	// echo "<pre>";
	// print_r($sales_job_arr);
	foreach (glob(""."*.zip") as $filename)
	{
		@unlink($filename);
	}

	$i=1;

	// echo $data."..............";
	// echo "select a.id, a.sales_booking_no, a.within_group, a.job_no sales_order_no, a.buyer_id,a.location_id,c.barcode_no from fabric_sales_order_mst a, pro_roll_details c where a.id=c.po_breakdown_id and c.id in ($data)";
	$i=1; $year=date("y");
	$query="select a.id, a.sales_booking_no, a.within_group, a.job_no sales_order_no, a.buyer_id,a.location_id,c.barcode_no,c.roll_no,c.qnty from fabric_sales_order_mst a, pro_roll_details c where a.id=c.po_breakdown_id and c.id in($data)";
	$res=sql_select($query);
	foreach($res as $row)
	{
		$file_no='';
		$po_number='';
		$job_no='';
		$buyer_name='';
		$reff_no="";

		if($row[csf('within_group')] == 1){
			$job_no  	=$sales_job_arr[$row[csf('sales_booking_no')]]["job_no_mst"];
			$buyer_name =$sales_job_arr[$row[csf('sales_booking_no')]]["buyer_id"];
			$reff_no 	=$sales_job_arr[$row[csf('sales_booking_no')]]["grouping"];
			$file_no 	=$sales_job_arr[$row[csf('sales_booking_no')]]["file_no"];
		}else{
			$job_no  	= "";
			$buyer_name =$row[csf('buyer_id')];
			$reff_no 	="";
			$file_no 	="";
		}
		$po_number 	=$row[csf('sales_order_no')];

		if($row[csf('booking_without_order')]==0)
		{
			//$file_no=$po_array[$row[csf('po_breakdown_id')]]['file_no'];
			//$po_number=$po_array[$row[csf('po_breakdown_id')]]['po_no'];
			//$reff_no=$po_array[$row[csf('po_breakdown_id')]]['grouping'];
		}

		//echo $i."--";
		$file_name="NORSEL-IMPORT_".$i;
		$myfile = fopen($file_name.".txt", "w") or die("Unable to open file!");
		$txt ="Norsel_imp\r\n1\r\n";
		$txt .=$party_name."\r\n";
		$txt .="Job No.".$job_no."\r\n";
		$txt .="M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
		$txt .= $row[csf('barcode_no')]."\r\n";
		$txt .="ID: ".$row[csf('barcode_no')]."\r\n";
		$txt .="D:".$prod_date."\r\n";
		$txt .="T:".$prod_time."\r\n";
		$txt .= $buyer_name_array[$buyer_name]."\r\n";
		$txt .="Order No:". $po_number."\r\n";
		$txt .=$comp."\r\n";
		$txt .="G/F-Dia:".trim($grey_dia)."/".trim($finish_dia)." ".trim($stitch_length)." ".trim($tube_type)."\r\n";
		$txt .="File No:".$file_no."\r\n";
		$txt .="Ref.No:".$reff_no."\r\n";
		$txt .="GSM:".$gsm."\r\n";
		$txt .= $yarn_count."\r\n";
		$txt .= $brand."\r\n";
		$txt .= "Lot:".$yarn_lot."\r\n";
		$txt .="Prg: ".$program_no."\r\n";
		$txt .="Roll Wt:".number_format($row[csf('qnty')],2,'.','')." Kg "."\r\n";
		$txt .="Roll Sl. ". $row[csf('roll_no')]."\r\n";
		$txt .= trim($color) . "\r\n";
		$txt .= "" . trim($colorRange) . "\r\n";
		$txt .= "" . $floor_name . "\r\n";
		$txt .=$location_name."\r\n";

		//Wt:".number_format($row[csf('qnty')],2,'.','')." Kg "."\r\n";
		//$txt .= "Prod Date: ".$prod_date;

		fwrite($myfile, $txt);
		fclose($myfile);

		$i++;
	}
	//echo "===".$filename; die;
	//$filename="norsel";
	$zip = new ZipArchive();			// Load zip library
	$filename = str_replace(".sql",".zip",'norsel_bundle.sql');			// Zip name
	if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE)
	{		// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>"; echo $error;
	}

	foreach (glob(""."*.txt") as $filenames)
	{
		$zip->addFile($file_folder.$filenames);
	}
	$zip->close();

	foreach (glob(""."*.txt") as $filename)
	{
		@unlink($filename);
	}
	echo "norsel_bundle";
	exit();
}

if($action=="report_barcode_text_file")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$userid=$_SESSION['logic_erp']['user_id'];
	$roll_id=sql_select("select mst_id, dtls_id ,id,roll_id,po_breakdown_id  from pro_roll_details where id in($data) and status_active=1 and is_deleted=0");
	$roll_id_arr=array();
	foreach($roll_id as $val)
	{
		$mst_id=$val[csf('mst_id')];
		$dtls_id=$val[csf('dtls_id')];
		if((trim($val[csf('roll_id')])*1)!=0)
		{
			$roll_id_arr[]=	$val[csf('roll_id')];
		}
		else
		{
			$roll_id_arr[]=	$val[csf('id')];
		}
		$order_id_arr[]=$val[csf('po_breakdown_id')];
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

	$sql = "select a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.shift_name, b.insert_date,b.operator_name, b.color_range_id, b.floor_id,b.body_part_id  from inv_receive_master a, pro_grey_prod_entry_dtls b,pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.id in (".implode(",",$roll_id_arr).")";
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

		} else if ($row[csf('receive_basis')] == 2) //Knitting Plan
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
			$po_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
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
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty,a.reject_qnty, b.batch_wgt from pro_roll_details a left join pro_grey_batch_dtls b on a.id = b.roll_id where a.id in($data) and a.status_active=1 and a.is_deleted=0 order by a.barcode_no asc";
	//echo $query;die;
	$res = sql_select($query);
	$split_data_arr = array();
	$created_files=array();

	//if (!file_exists("files/".$_SESSION['logic_erp']['user_id']."/")) {
	//	mkdir("files/".$_SESSION['logic_erp']['user_id']."/", 0777, true);
	//}

	foreach ($res as $row) {

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

		$qnty = number_format($row[csf('QNTY')], 2, '.', '');
		$barcode = $row[csf('barcode_no')];

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

	/* $sql="select barcode_no from pro_roll_split where entry_form=113 and status_active=1 and is_deleted=0 and barcode_no=".$data."";
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo 1;die;
	}
	else
	{
		$barcodeData=return_field_value("a.issue_number issue_number","inv_issue_master a,pro_roll_details b", "a.id=b.mst_id and a.entry_form = 61 and b.entry_form=61 and  b.barcode_no=".$data." and b.is_returned!=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0","issue_number");

		if($barcodeData != "")
		{
			echo "0**".$barcodeData;die;
		}
		else
		{
			echo 2;die;
		}
	}
	exit();	 */

	// when transfer but acknowledgement not create (when acknowledgement then re_transfer 0)
	$barcode_check_sql=sql_select("SELECT a.id, a.barcode_no
	from pro_roll_details a
	where a.entry_form in(84,82,83,110,180,183,133) and a.status_active=1 and a.is_deleted=0 and a.barcode_no=$data and a.re_transfer=0
	union all
	SELECT a.id, a.barcode_no from pro_roll_details a, pro_grey_prod_entry_dtls b where a.dtls_id =b.id and entry_form in (2,22,58) and b.trans_id <> 0 and a.barcode_no=$data and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.re_transfer=0");
	if(empty($barcode_check_sql))
	{
		echo "3**".$data;die;
	}

	$existing_sql=sql_select("SELECT a.id, a.barcode_no, a.system_number, a.company_id, a.split_from_id, a.roll_wgt, b.is_sales, a.qty_in_pcs from pro_roll_split a, pro_roll_details b where a.split_from_id=b.id and a.entry_form=113 and a.status_active=1 and a.is_deleted=0 and a.barcode_no=".$data." and b.re_transfer=0");

	$production_dtlsId_arr=return_library_array( "select barcode_no, dtls_id from pro_roll_details where barcode_no in($data) and entry_form in(2)",'barcode_no','dtls_id');

	$barcodeData=return_field_value("a.issue_number issue_number","inv_issue_master a,pro_roll_details b", "a.id=b.mst_id and a.entry_form = 61 and b.entry_form=61 and  b.barcode_no=".$data." and b.is_returned!=1 and  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0","issue_number");

	if($barcodeData != "")
	{
		echo "0**".$barcodeData;die;
	}
	else if(!empty($existing_sql))
	{
		echo "1**".$existing_sql[0][csf("system_number")]."**".$existing_sql[0][csf("id")]."**".$existing_sql[0][csf("barcode_no")]."**".$existing_sql[0][csf("split_from_id")]."**".$existing_sql[0][csf("roll_wgt")]."**".$existing_sql[0][csf("is_sales")]."**".$existing_sql[0][csf("qty_in_pcs")]."**".$production_dtlsId_arr[$existing_sql[0][csf("barcode_no")]]."**".$existing_sql[0][csf("company_id")];die;
	}
	else
	{
		echo 2;die;
	}
	exit();
}

if($action=="check_sales_order")
{
	$is_sales=return_field_value("is_sales","pro_roll_details", "barcode_no='".$data."'");
	if($is_sales == 1)
	{
		echo 1;
		die;
	}
	else
	{
		echo $is_sales*1; // multiphy with 1 to make blank value to 0;
		die;
	}
	exit();
}

if ($action == "direct_print_barcode_6")
{
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');

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

if ($action == "print_barcode_ccl")
{
	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');

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

		$update_sql = sql_select("select id,prod_id,used_qty,rate,amount,yarn_percentage,porcess_loss from pro_material_used_dtls where mst_id=$rcv_id and dtls_id =".$result[0][csf("dtls_id")]);
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

if ($action == "auto_create_and_print_pdf_file")
{
	$data = explode("***", $data);
	//echo $data[0];die;
	$userid=$_SESSION['logic_erp']['user_id'];

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	$machine_library = sql_select("select id, machine_no, brand, norsel_printer_api, norsel_printer from lib_machine_name");
	foreach ($machine_library as $val)
	{
		$machine_no_arr[$val[csf("id")]] = $val[csf("machine_no")];
		$machine_brand_arr[$val[csf("id")]] = $val[csf("brand")];
	}

	$user_api_data = sql_select("select norsel_printer_api, norsel_printer from user_passwd where id=$userid and status_active=1 ");
	$norsel_printer_api = $user_api_data[0][csf("norsel_printer_api")];
	$norsel_printer = $user_api_data[0][csf("norsel_printer")];

	$operator_name_arr = return_library_array("select id, first_name from lib_employee", 'id', 'first_name');
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	$company_sql = sql_select("select id, company_name, company_short_name  from lib_company ");

	foreach ($company_sql as $val)
	{
		$companys_name_arr[$val[csf("id")]]['full_name']=$val[csf('company_name')];
		$companys_name_arr[$val[csf("id")]]['short_name']=$val[csf('company_short_name')];
	}

	$supplier_sql = sql_select("select id, supplier_name, short_name  from lib_supplier ");

	foreach ($supplier_sql as $val)
	{
		$supplier_name_arr[$val[csf("id")]]['full_name']=$val[csf('supplier_name')];
		$supplier_name_arr[$val[csf("id")]]['short_name']=$val[csf('short_name')];
	}

	$sql_yarn_info=sql_select("SELECT a.prod_id,b.brand,b.yarn_type,b.yarn_comp_type1st,b.yarn_comp_type2nd,b.yarn_comp_percent1st,b.yarn_comp_percent2nd,b.lot,b.yarn_count_id from pro_material_used_dtls a,product_details_master b  where a.prod_id=b.id and  a.dtls_id=".$data[1]." and a.entry_form=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");//a.mst_id=".$data[4]." and

	$yarn_information_string=""; $all_yarn_type=''; $yarn_information_string_without_compo=''; $yarn_counter=4;
	$yarn_dtls_arr=array();
	foreach($sql_yarn_info as $p_val)
	{
		$costing_yarn_composition='';
		$costing_yarn_band=$brand_arr[$p_val[csf('brand')]];
		$costing_yarn_lot=trim($p_val[csf('lot')]);
		$costing_yarn_count=$count_arr[$p_val[csf('yarn_count_id')]];
		$costing_yarn_composition=$composition[$p_val[csf('yarn_comp_type1st')]] . " " . $p_val[csf('yarn_comp_percent1st')] . "%";
		if ($p_val[csf('yarn_comp_type2nd')] != 0) $costing_yarn_composition .= " " . $composition[$p_val[csf('yarn_comp_type2nd')]] . " " . $p_val[csf('yarn_comp_percent2nd')] . "%";
		$yarn_information_string.=$costing_yarn_band." ".$costing_yarn_lot." ".$costing_yarn_count." ".$costing_yarn_composition. "\r\n";
		$yarn_information_string_without_compo.=$costing_yarn_band." ".$costing_yarn_lot." ".$costing_yarn_count. "\r\n";
		$all_yarn_type.=",".$yarn_type[$p_val[csf('yarn_type')]];
		$yarn_counter--;
	}

	//echo $yarn_information_string_without_compo."**".$yarn_information_string;die;
	/* $sql_yarn_infos=sql_select("SELECT b.brand,b.yarn_type,b.yarn_comp_type1st,b.yarn_comp_type2nd,b.yarn_comp_percent1st,b.yarn_comp_percent2nd, b.lot,b.yarn_count_id from product_details_master b  where item_category_id=1 and b.status_active=1 and b.is_deleted=0");

	$yarn_dtls_arr=array();
	foreach($sql_yarn_infos as $p_val)
	{
		$yarn_dtls_arr[$p_val[csf('yarn_count_id')]] = $composition[$p_val[csf('yarn_comp_type1st')]] . " " . $p_val[csf('yarn_comp_percent1st')] . "%";
	} */

	$sql = "SELECT a.company_id, a.recv_number, a.knitting_location_id, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company,a.yarn_issue_challan_no, b.order_id, b.prod_id, b.gsm, b.width, b.yarn_lot, b.yarn_count, b.brand_id, b.machine_no_id, b.stitch_length, b.machine_dia, b.machine_gg, b.color_id, b.febric_description_id, b.shift_name, b.insert_date,b.operator_name, b.color_range_id, b.body_part_id, b.floor_id, b.yarn_prod_id, c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c where a.id=b.mst_id and b.id=c.dtls_id and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and b.id=$data[1] and b.status_active=1";

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

	$bodyPartId	= '';
	$receive_basis	= '';
	$within_group	= '';

	foreach ($result as $row)
	{
		if ($row[csf('knitting_source')] == 1)
		{
			$party_name_short = $companys_name_arr[$row[csf('knitting_company')]]['short_name'];
			$party_name_full = $companys_name_arr[$row[csf('knitting_company')]]['full_name'];
		}
		else if ($row[csf('knitting_source')] == 3)
		{
			$party_name_short = $supplier_name_arr[$row[csf('knitting_company')]]['short_name'];
			$party_name_full = $supplier_name_arr[$row[csf('knitting_company')]]['full_name'];
		}
		//$yarn_type_data = return_field_value("yarn_type", "product_details_master", "id=" . $row[csf('prod_id')]);
		$yarn_type_data="";
		if($row[csf('yarn_prod_id')] !="")
		{
			$yarnTypeSql = sql_select("SELECT yarn_type from product_details_master where id in (".$row[csf('yarn_prod_id')].")");
			foreach ($yarnTypeSql as $tval)
			{
				$yarn_type_data .= $yarn_type[$tval[csf('yarn_type')]]."__";
			}
		}

		$yarn_type_data = implode(",",array_unique(explode(",",chop($yarn_type_data,"__"))));

		$company_id = $row[csf('company_id')];
		$company_full_name = $companys_name_arr[$company_id]['full_name'];
		$company_short_name = $companys_name_arr[$company_id]['short_name'];

		$receive_basis = $row[csf('receive_basis')];
		$within_group = $row[csf('within_group')];



		$booking_no = $row[csf('booking_no')];
		$booking_id = $row[csf('booking_id')];
		$operator_name = $operator_name_arr[$row[csf('operator_name')]];
		$floor_name = $floor_name_arr[$row[csf('floor_id')]];
		$yarn_issue_challan_no=$row[csf('yarn_issue_challan_no')];
		$booking_without_order = $row[csf('booking_without_order')];
		$productionId = $row[csf('recv_number')];
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$prod_time = date("h-i a", strtotime($row[csf('insert_date')]));
		$location_name=return_field_value("location_name","lib_location", "id=".$row[csf('knitting_location_id')]);
		//$order_id = $row[csf('order_id')];
		$order_id .= $row[csf('po_breakdown_id')].",";
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$shiftName = $shift_name[$row[csf('shift_name')]];

		$colorRange = $color_range[$row[csf('color_range_id')]];
		$bodyPartId	= $body_part[$row[csf('body_part_id')]];


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

		if ($row[csf('receive_basis')] == 0 || $row[csf('receive_basis')] == 1 || $row[csf('receive_basis')] == 4)
		{
			$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
			$machine_name = $machine_data[0][csf('machine_no')];
			$machine_dia_width = $row[csf('machine_dia')];
			$machine_gauge = $row[csf('machine_gg')];
			$machine_brand = $row[csf('brand')];
			if($row[csf('receive_basis')]==1)
			{
				$sql_precost_tube=sql_select("SELECT  b.width_dia_type,b.color_type_id from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$booking_no' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.body_part_id=".$row[csf('body_part_id')]." and b.lib_yarn_count_deter_id=".$row[csf('febric_description_id')]."");
				foreach($sql_precost_tube as $t_val)
				{
					$tube_type = $fabric_typee[$t_val[csf('width_dia_type')]];
					$color_type_name = $color_type[$t_val[csf('color_type_id')]];
				}
			}

		}
		else if ($row[csf('receive_basis')] == 2) //Knitting Plan
		{
			$program_data = sql_select("SELECT a.within_group, b.width_dia_type, b.machine_dia, b.machine_gg, b.machine_id, b.batch_no,b.tube_ref_no, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id='" . $row[csf('booking_id')] . "'");
			$program_no = $row[csf('booking_id')];
			$grey_dia = $program_data[0][csf('machine_dia')];
			$tube_type = $fabric_typee[$program_data[0][csf('width_dia_type')]];
			$machine_dia_width = $program_data[0][csf('machine_dia')];
			$machine_gauge = $program_data[0][csf('machine_gg')];
			$batch_no = $program_data[0][csf('batch_no')];
			$tube_ref_no = $program_data[0][csf('tube_ref_no')];
			$plan_remarks = $program_data[0][csf('remarks')];
			//$machine_no_arr
			$machine_brand = $machine_brand_arr[$row[csf('machine_no_id')]];
			$machine_name = $machine_no_arr[$row[csf('machine_no_id')]];
			//$machine_name=explode(",",$program_data[0][csf('machine_id')]);
			$row[csf("within_group")] = $program_data[0][csf('within_group')];

			//$norsel_printer_api = $machine_norsel_data[$row[csf('machine_no_id')]]["norsel_printer_api"];
			//$norsel_printer = $machine_norsel_data[$row[csf('machine_no_id')]]["norsel_printer"];
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id='" . $row[csf('buyer_id')] . "'");
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$comp = $composition_short_names='';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "")
		{
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		}
		else
		{
			$determination_sql = sql_select("SELECT a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where b.status_active=1 and a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "")
			{
				$comp = $determination_sql[0][csf('construction')] . ", ";
				$construction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row)
			{
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$composi .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$composition_short_names .= $composition_shortArr[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
	}

	$order_id=implode(",",array_unique(explode(",",chop($order_id,","))));

	$po_array = array();
	$booking_no_prefix = '';

	if ($booking_without_order == 1)
	{
		if ($receive_basis == 4)
		{
			$fb_sales_sql = "SELECT id,job_no_prefix_num,job_no,style_ref_no,within_group from fabric_sales_order_mst where id = " . $row[csf('booking_id')];
			$fb_salesResult = sql_select($fb_sales_sql);
			$booking_no_prefix = $fb_salesResult[0][csf('job_no_prefix_num')];
			$full_booking_no = $fb_salesResult[0][csf('job_no')];
			$style_ref_no = $fb_salesResult[0][csf('style_ref_no')];
			$sales_id = $fb_salesResult[0][csf('id')];
			$no_arr = explode("-", $full_booking_no);
			array_shift($no_arr); //remove 1st index
			$full_booking_no = implode("-", $no_arr);
			$po_array[$sales_id]['style_ref'] = $style_ref_no;

		}
		else
		{
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$full_booking_no = $booking_no;

			$internal_reference_no = return_field_value("grouping", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");

			$sql_color_type=sql_select("SELECT color_type_id from wo_non_ord_samp_booking_dtls where booking_no='".$booking_no."' and body_part=".$row[csf('body_part_id')]." and lib_yarn_count_deter_id =".$row[csf('febric_description_id')]." and status_active=1 and is_deleted=0  ");
			foreach($sql_color_type as $n_val)
			{
				$color_type_arr[]= $color_type[$n_val[csf('color_type_id')]];
			}
			$color_type_name=implode(",",array_unique($color_type_arr));
		}

		if ($receive_basis == 2) // Knitting Plan
		{
			//$booking_program=$booking_no;
			$booking_no = return_field_value("b.booking_no as booking_no", "ppl_planning_info_entry_dtls a,ppl_planning_info_entry_mst b", " b.id=a.mst_id and a.id='" . $booking_id . "'", "booking_no");
			$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
			$full_booking_no = $booking_no;

			$internal_reference_no = return_field_value("grouping", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");

			$sql_color_type=sql_select("SELECT color_type_id from wo_non_ord_samp_booking_dtls where booking_no='".$booking_no."' and body_part=".$row[csf('body_part_id')]." and lib_yarn_count_deter_id =".$row[csf('febric_description_id')]." and status_active=1 and is_deleted=0  ");
			foreach($sql_color_type as $n_val)
			{
				$color_type_arr[]= $color_type[$n_val[csf('color_type_id')]];
			}
			$color_type_name=implode(",",array_unique($color_type_arr));
		}
	}
	else
	{
		$is_salesOrder = 0;
		if ($receive_basis == 2)
		{
			$booking_no2 =$booking_no;
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id='" . $row[csf("booking_id")] . "'");
			$booking_no = return_field_value("b.booking_no as booking_no", "ppl_planning_info_entry_dtls a,ppl_planning_info_entry_mst b", " b.id=a.mst_id and a.id='" . $booking_id . "'", "booking_no");
			$cound_feed = sql_select("select seq_no,count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=" . $row[csf("booking_id")] . " and status_active=1 and is_deleted=0 order by seq_no");

			$fidder_count=4;
			foreach ($cound_feed as $feed_row)
			{
				$count_feed_arr[] = $feed_row[csf("seq_no")]."*".$feed_row[csf("count_id")]."*".$feed_row[csf("feeding_id")];
				$fidder_count--;
			}

			$sql_color_type=sql_select("select color_type_id from ppl_planning_entry_plan_dtls where dtls_id='".$booking_no2."' and body_part_id=".$row[csf('body_part_id')]."  and status_active=1 and is_deleted=0  ");
			foreach($sql_color_type as $n_val)
			{
				$color_type_arr[]= $color_type[$n_val[csf('color_type_id')]];
			}

			$color_type_name=implode(",",array_unique($color_type_arr));

		}

		if ($is_salesOrder == 1)
		{
			if ($within_group == 1)
			{
				$po_sql = sql_select("SELECT a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,b.buyer_id,a.within_group from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no=b.booking_no and a.within_group=1 and a.id in($order_id) union all select a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,b.buyer_id,a.within_group from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b where a.sales_booking_no=b.booking_no and a.within_group=1 and a.id in($order_id)");
			}
			else
			{
				$po_sql = sql_select("SELECT a.id, a.job_no as po_number, a.style_ref_no, a.job_no_prefix_num, a.sales_booking_no,a.buyer_id,a.within_group from fabric_sales_order_mst a where a.id in($order_id)");
			}
			foreach ($po_sql as $row)
			{
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
		}
		else
		{
			$order_id=chop($order_id,",");
			$po_sql = sql_select("SELECT a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.id=b.job_id and b.id in($order_id)");

			foreach ($po_sql as $row)
			{
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


	//echo $within_group;die;
	//exit;

	$i = 1;
	$year = date("y");

	$query = "SELECT a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty,a.reject_qnty, b.fabric_grade, a.qc_pass_qnty_pcs, a.coller_cuff_size from pro_roll_details a left join pro_qc_result_mst b on a.barcode_no=b.barcode_no where a.id in($data[0]) and a.entry_form=58";
	//a.id in($data[0])
	//echo $query;die;
	$res = sql_select($query);
	$split_data_arr = array();
	$created_files=array();


	//header('Content-type: application/json');
    $response = array();

	foreach ($res as $row)
	{
		$split_roll_id = $row[csf('id')];

		if ($booking_without_order == 1)
		{
			$full_job_no = $full_booking_no;
			$book_or_po_no_prefix = $booking_no_prefix;
		}
		else
		{
			$full_job_no = $po_array[$row[csf('po_breakdown_id')]]['job_no'];
			$book_or_po_no_prefix = $po_array[$row[csf('po_breakdown_id')]]['prefix'];
			$internal_reference_no = $po_array[$row[csf('po_breakdown_id')]]['grouping'];
		}

		$machine_dia_gause = $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge;
		$qnty = number_format($row[csf('qnty')], 2, '.', '');
		$barcode = $row[csf('barcode_no')];


		$dia_length_tube=trim($grey_dia);
		if($dia_length_tube!="") $dia_length_tube.="/";
		$dia_length_tube.=trim($finish_dia) . " " . trim($stitch_length) . " " . trim($tube_type) . " " . $row[csf('coller_cuff_size')] . "";


		if ($within_group == 1)
		{
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $po_array[$row[csf('po_breakdown_id')]]['buyer_name']);
		}
		else
		{
			$buyer_name= $buyer_name;
		}

		$dia_width_gauge = $machine_dia_width . "X" . $machine_gauge;

		$dia_tube="";
		$dia_tube.=trim($finish_dia) ." ". trim($tube_type) ;

		$response = array(
		    //"0" => $norsel_printer,//"ptr1",
		    //"norsel_ip" => $norsel_ip,
			"1"=> $barcode,
			"2"=> "QR CODE PRINT",
		    "3" => $party_name_short,
		    "4" => $book_or_po_no_prefix,
		    "5" => $machine_dia_gause,
		    "6" => $barcode,
		    "7" => $prod_date,
		    "8" => $buyer_name,
		    "9" => $po_array[$row[csf('po_breakdown_id')]]['no'],
		    "10" => $po_array[$row[csf('po_breakdown_id')]]['file_no'],
		    "11" => $po_array[$row[csf('po_breakdown_id')]]['grouping'],
		   	"12" => $comp ,
		    "13" => $dia_length_tube ,
		    "14" => $gsm,
		    "15" => $yarn_count,
		    "16" => $brand,
		    "17" => $yarn_lot,
		    "18" => $program_no,
		    "19" => $qnty."Kg",
		    "20" => $shiftName,
		    "21" => $row[csf('roll_no')],
		    "22" => $color,
		    "23" => $colorRange,
		    "24" => $po_array[$row[csf('po_breakdown_id')]]['style_ref'],
		    "25" => $booking_no,
		    "26" => $operator_name,
		    "27" => $productionId,
		    "28" => $batch_no,
		    "29" => $tube_ref_no,
		    "30" => $machine_brand,
		    "31" => $yarn_type_data,//$yarn_type[$yarn_type_data],
		    "32" => $construction,
		    "33" => $composi ,
		    "34" => $floor_name,
		    "35" => $machine_name,
		    "36" => $dia_width_gauge,
		    "37" => $full_job_no,
		    "38" => $dia_tube,
		    "39" => trim($stitch_length),
		    "40" => $row[csf('reject_qnty')],
		    "41" => $yarn_information_string,
		    "42" => ltrim($all_yarn_type,","),
		    "43" => $location_name,
		    "44" => $color_type_name,
		    "45" => $bodyPartId,
		    "46" => $yarn_information_string_without_compo,
		    "47" => $yarn_issue_challan_no,
		    "48" => $row[csf('qc_pass_qnty_pcs')],
		    "49" => $row[csf('coller_cuff_size')],
		    "50" => $internal_reference_no,
		    "51" => $party_name_full,
		    "52" => $company_full_name,
		    "53" => $company_short_name,
			"54" => $prod_time,
			"55" => $composition_short_names,
			"56" => $plan_remarks
    	);
	}

	$reference_array = array(
		1=>'Barcode',
		2=>'Qrcode',
		3=>"party_name",
		4=>"book_or_po_no_prefix",
		5=>"machine_dia_gause",
		6=>"barcode_no",
		7=>"prod_date",
		8=>"buyer_name",
		9=>"po_no",
		10=>"file_no",
		11=>"grouping",
		12=>"comp",
		13=>"dia_length_tube_size",
		14=>"gsm",
		15=>"yarn_count",
		16=>"brand",
		17=>"yarn_lot",
		18=>"program_no",
		19=>"qnty",
		20=>"shiftName",
		21=>"roll_no",
		22=>"color",
		23=>"colorRange",
		24=>"style_ref",
		25=>"booking_no",
		26=>"operator_name",
		27=>"productionId",
		28=>"batch_no",
		29=>"tube_ref_no",
		30=>"machine_brand",
		31=>"yarn_type",
		32=>"construction",
		33=>"composi",
		34=>"floor_name",
		35=>"machine_name",
		36=>"dia_width_gauge",
		37=>"full_job_no",
		38=>"dia_tube",
		39=>"stitch_length",
		40=>"reject_qty",
		41=>"yarn_information_string",
		42=>"all_yarn_type",
		43=>"location_name",
		44=>"color_type_name",
		45=>"bodyPartId",
		46=>"yarn_information_string_without_compo",
		47=>"yarn_issue_challan_no",
		48=>"qc_pass_qnty_pcs",
		49=>"coller_cuff_size",
		50=>"internal_reference_no"
	);

	require('../../../ext_resource/pdf/code128.php');
	define('FPDF_FONTPATH', '../../../ext_resource/pdf/fpdf/font/');

	ob_start();

	$sqlResult = sql_select("SELECT a.HEIGHT,a.WIDTH,a.BOTTOM_PADDING,a.LEFT_PADDING,a.RIGHT_PADDING,a.TOP_PADDING, a.LINE_SPACE,a.ORIENTATION, a.FONT_COLOR,a.LINE_BREAK,a.FONT,b.FIELD_NAME, b.FIELD_ID, b.SERIAL_NUMBER, b.FONT_SIZE, b.FONT_WEIGHT from pdf_formate_mst a, pdf_formate_details b where a.id=b.mst_id and a.entry_form=2 and a.id=(select max(id) from pdf_formate_mst where entry_form=2)  order by b.SERIAL_NUMBER ASC");

	$mb = ($sqlResult[0]['BOTTOM_PADDING']) ? $sqlResult[0]['BOTTOM_PADDING'] : "1";
	$ml = ($sqlResult[0]['LEFT_PADDING']) ? $sqlResult[0]['LEFT_PADDING'] : "2";
	$mr = ($sqlResult[0]['RIGHT_PADDING']) ? $sqlResult[0]['RIGHT_PADDING'] : "1";
	$mt = ($sqlResult[0]['TOP_PADDING']) ? $sqlResult[0]['TOP_PADDING'] : "1";
	$width = ($sqlResult[0]['WIDTH']) ? $sqlResult[0]['WIDTH'] : "72";
	$height = ($sqlResult[0]['HEIGHT']) ? $sqlResult[0]['HEIGHT'] : "60";
	$ls = ($sqlResult[0]['LINE_SPACE']) ? $sqlResult[0]['LINE_SPACE'] : "10";
	$orientation = ($sqlResult[0]['ORIENTATION']) ? $sqlResult[0]['ORIENTATION'] : "P";
	$line_break = ($sqlResult[0]['LINE_BREAK']) ? $sqlResult[0]['LINE_BREAK'] :31;
	$font = ($sqlResult[0]['FONT']) ? $sqlResult[0]['FONT'] :'Arial';


	$pdf=new PDF_Code128($orientation,'mm', array($width,$height));
	$pdf->AddPage();
	$pdf->SetAutoPageBreak(false);
	$pdf->SetRightMargin($mr);

	$line_space = $mt;
	foreach($sqlResult as $row)
	{

		$field_id_arr = explode(",", $row['FIELD_ID']);
		$field_name_arr = explode(",", $row['FIELD_NAME']);
		$line_data_arr = array();
		$barcode = false;
		foreach ($field_id_arr as  $j=>$val)
		{
			if($val == 1){$barcode = $response[$val];}
			else{$line_data_arr[]= $field_name_arr[$j] . $response[$val];}
		}

		if($barcode == false){
			$line_string_array = str_split(implode(',',$line_data_arr),$line_break);
			foreach ($line_string_array as  $strval)
			{
				$pdf->SetXY($ml, $line_space);
				$pdf->SetFont($font,$row['FONT_WEIGHT'],$row['FONT_SIZE']);
				$pdf->Write($ml, $strval);
				$line_space+=$ls;
			}
		}
		else{
		  $pdf->SetXY($ml, $line_space);
		  $pdf->Code128($ml+2,$line_space,$barcode,50,8);
		  $line_space+=6;
		  $line_space+=$ls;
		}
	}

	$REAL_FILE_NAME = 'knitting_barcode_'.$user_id.'.pdf';
	$pdf->Output( $REAL_FILE_NAME, 'F');
	echo 'requires/'.$REAL_FILE_NAME;

	exit();

}

?>
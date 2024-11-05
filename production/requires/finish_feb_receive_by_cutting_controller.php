
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
		/*$new_mrr_number=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'FRBC', date("Y",time()), 5, "select recv_number_prefix, recv_number_prefix_num from  inv_receive_mas_batchroll where company_id=$cbo_company_id and entry_form=72 and $year_cond=".date('Y',time())." order by id desc ", "recv_number_prefix","recv_number_prefix_num"));
		$id=return_next_id( "id", "inv_receive_mas_batchroll", 1 ) ;*/
			
			
		$new_mrr_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_mas_batchroll",$con,1,$cbo_company_id,'FRBC',72,date("Y",time()),0 ));
		$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_mas_batchroll", $con);
			
			
				 
		$field_array="id,recv_number_prefix,recv_number_prefix_num 	,recv_number,entry_form,receive_date,company_id,dyeing_source,dyeing_company,challan_no,batch_no,reqsn_no,receive_purpose,inserted_by,insert_date";
		$data_array="(".$id.",'".$new_mrr_number[1]."',".$new_mrr_number[2].",'".$new_mrr_number[0]."',72,".$txt_delivery_date.",".$cbo_company_id.",".$cbo_knitting_source.",".$cbo_knitting_company.",".$txt_challan_no.",'".str_replace($txt_batch_no)."','".str_replace($txt_reqsn_no)."',".$cbo_basis.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		$field_array_dtls="id, mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,
		gsm,width,roll_wgt,job_no, order_id,color_id,batch_id,width_dia_type, inserted_by, insert_date";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form,roll_id, roll_no,barcode_no,qnty, inserted_by, insert_date,is_sales,booking_no,booking_without_order";
		
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		
		$barcodeNos='';
		for($j=1;$j<=$tot_row;$j++)
		{ 	
		   $activeId="activeId_".$j;
		   if($$activeId==1)
		   {
			$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			
			$rollId="rollId_".$j;
			$batchId="batchId_".$j;
			$widthType="widthType_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$knittingSource="knittingSource_".$j;
			$knittingComp="knittingComp_".$j;
			$receiveBasis="receiveBasis_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollwgt_".$j;
			$rolldia="rolldia_".$j;
			$bookingNo="bookingNo_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$IsSalesId = "IsSalesId_".$j;
			$bwoNo = "bwoNo_".$j;
			$booking_without_order_status = "booking_without_order_status_".$j;

			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$id.",".$dtls_id.",'".$$orderId."',72,'".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rollwgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "','" . $$bwoNo . "','" . $$booking_without_order_status . "')";
			
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$id.",".$$rollId.",'".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."',
			'".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rolldia."','".$$rollwgt."','".$$job_no."',
			'".$$orderId."','".$$colorId."','".$$batchId."','".$$widthType."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($barcodeNos!="") $barcodeNos.= ",";
			$barcodeNos.=$dtls_id."#".$id_roll."#".$j;
			//$id_roll=$id_roll+1;
			//$dtls_id = $dtls_id+1;
		   }
		}
	
		$rID=sql_insert("inv_receive_mas_batchroll",$field_array,$data_array,0);
		$rID2=sql_insert(" pro_roll_details",$field_array_roll,$data_array_roll,0);
		$rID3=sql_insert("pro_grey_batch_dtls",$field_array_dtls,$data_array_dtls,0);
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3)
			{
				mysql_query("COMMIT");  
				echo "0**".$id."**".$new_mrr_number[0]."**".$barcodeNos;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2 && $rID3)
			{
				oci_commit($con);  
				echo "0**".$id."**".$new_mrr_number[0]."**".$barcodeNos;
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
		$field_array="receive_date*batch_no*reqsn_no*updated_by*update_date";
		$data_array="".$txt_delivery_date."*'".str_replace($txt_batch_no)."'*'".str_replace($txt_reqsn_no)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		
		$field_array_dtls="updated_by*update_date*status_active*is_deleted";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id,entry_form,roll_id,roll_no,barcode_no,qnty, inserted_by, insert_date,is_sales,booking_no,booking_without_order";
		$field_array_insert="id, mst_id,roll_id,knitting_source,knitting_company,booking_no,receive_basis,prod_id,body_part_id,febric_description_id,
		gsm,width,roll_wgt,job_no, order_id,color_id,batch_id,width_dai_type, inserted_by, insert_date";
		
		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		//$dtls_id = return_next_id( "id", "pro_grey_batch_dtls", 1 );
		

		$barcodeNos='';
		for($j=1;$j<=$tot_row;$j++)
		{ 
		
		    $activeId="activeId_".$j;
			$updateDetailsId="updateDetailsId_".$j;
			$rolltableId="rolltableId_".$j;
			
			if($$activeId==0 )
			{
				if(str_replace("'","",$$updateDetailsId)!=0)
				{
				$updateDetailsId_arr[]=$$updateDetailsId;
				$updateRoll_arr[]=$$rolltableId;
				//$remove_detls_arr[$$updateDetailsId]=explode("*",("".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1"));
				}
			}
			
		   if($$activeId==1 && $$updateDetailsId==0)
			{
			
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			$dtls_id = return_next_id_by_sequence("PRO_GREY_BATCH_DTLS_PK_SEQ", "pro_grey_batch_dtls", $con);
			
			$rollId="rollId_".$j;
			$batchId="batchId_".$j;
			$widthType="widthType_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$knittingSource="knittingSource_".$j;
			$knittingComp="knittingComp_".$j;
			$receiveBasis="receiveBasis_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollwgt_".$j;
			$rolldia="rolldia_".$j;
			$bookingNo="bookingNo_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$IsSalesId = "IsSalesId_".$j;
			$bwoNo = "bwoNo_".$j;
			$booking_without_order_status = "booking_without_order_status_".$j;
			
			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$update_id.",".$dtls_id.",'".$$orderId."',72,'".$$rollId."','".$$rollNo."','".$$barcodeNo."',
			'".$$rollwgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "','" . $$bwoNo . "','" . $$booking_without_order_status . "')";
			if($data_array_dtls!="") $data_array_dtls.=",";
			$data_array_dtls.="(".$dtls_id.",".$update_id.",".$$rollId.",'".$$knittingSource."','".$$knittingComp."','".$$bookingNo."','".$$receiveBasis."',
			'".$$productId."','".$$bodyPart."','".$$deterId."','".$$rollGsm."','".$$rolldia."','".$$rollwgt."','".$$job_no."',
			'".$$orderId."','".$$colorId."','".$$batchId."','".$$widthType."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($barcodeNos!="") $barcodeNos.= ",";
			$barcodeNos.=$dtls_id."#".$id_roll."#".$j;
			//$id_roll=$id_roll+1;
			//$dtls_id =$dtls_id+1;
			
			
			}
		}
		
		$flag=0;
		$rID=sql_update("inv_receive_mas_batchroll",$field_array,$data_array,"id",$update_id,0);
		if($rID) $flag=1; else $flag=50;
		$rID2=true; $rID3=true; $statusChange=true;
		if(count($updateDetailsId_arr)>0)
		{
			$rID2=execute_query("delete from pro_grey_batch_dtls where id in (".implode(",",$updateDetailsId_arr).")");
			if($flag==1) { if($rID2) $flag=1; else $flag=0; }
			$rID3=execute_query("delete from pro_roll_details where id in (".implode(",",$updateRoll_arr).") and entry_form=72");
			if($flag==1) { if($rID3) $flag=1; else $flag=0; }
		}



		if($data_array_roll!="")
		{
		$rID4=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		if($flag==1) { if($rID4) $flag=1; else $flag=0; }
		}
		if($data_array_dtls!="")
		{
		$rID5=sql_insert("pro_grey_batch_dtls",$field_array_insert,$data_array_dtls,1);
		if($flag==1) { if($rID5) $flag=1; else $flag=0; }
		}
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".$barcodeNos;
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
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_no)."**".$barcodeNos;
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
}

if($action=="grey_item_details_update")
{
	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$data_array=sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
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
	}

	$job_arr=array();
	$sql_job=sql_select("select b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date from wo_booking_dtls b,wo_po_break_down c, wo_po_details_master e where b.po_break_down_id=c.id and c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_type in(1,4) group by b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
		$job_arr[$job_row[csf('booking_no')]]['year'] 				= date("Y", strtotime($job_row[csf("insert_date")]));
		$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
	}
	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
 $data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id , c.roll_id, c.roll_no, c.po_breakdown_id,c.qnty,c.is_sales,c.booking_no as roll_booking_no,c.booking_without_order FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0");

	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['company_id']=$row[csf("company_id")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$body_part[$row[csf("body_part_id")]];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$receive_basis_arr[$row[csf("receive_basis")]];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis_id']=$row[csf("receive_basis")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
		//$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
		
		if(str_replace("'","",$row[csf("entry_form")])==68)
		{
			$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("recv_number")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_id']=$row[csf("id")];	
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];	
		}
		else
		{
			$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_id']=$row[csf("booking_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("id")];
		}
		$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
		$roll_details_array[$row[csf("barcode_no")]]['dia_width_type']=$fabric_typee[$row[csf("dia_width_type")]];
		$roll_details_array[$row[csf("barcode_no")]]['dia_width_id']=$row[csf("dia_width_type")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("body_part_id")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part']=$body_part[$row[csf("body_part_id")]];
		$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
		$roll_details_array[$row[csf("barcode_no")]]['batch_name']=$batch_name_array[$row[csf("batch_id")]];
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
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
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
	}
	
	   $sql_update=sql_select("select  b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id,b.batch_id,
	   b.febric_description_id,b.gsm,b.width,b.roll_wgt,c.roll_id,b.buyer_id,b.order_id,b.color_id,a.challan_no,c.barcode_no,c.id as rolltable_id,c.is_sales,c.booking_no as roll_booking_no,c.booking_without_order 
	   from pro_grey_batch_dtls b,inv_receive_mas_batchroll a, pro_roll_details c
	   where a.id=b.mst_id and a.id=$data and b.id=c.dtls_id and a.is_deleted=0 and c.entry_form=72 and a.entry_form=72 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 
	   and c.status_active=1 and c.is_deleted=0");
        $issue_details_arr=array();
		$j=1;
 		foreach($sql_update as $val)
		{
          $inserted_roll_arr[]=$val[csf('roll_id')];
          $is_sales = $val[csf('is_sales')];
	        $sales_booking_no 	= $sales_arr[$val[csf('order_id')]]["sales_booking_no"];
			$within_group 		= $sales_arr[$val[csf('order_id')]]["within_group"];
			if ($is_sales == 1) {
				if($within_group == 1){
					$order_no 	= $sales_arr[$val[csf('order_id')]]["sales_order_no"];
					$job_no_mst = $job_arr[$sales_booking_no]['job_no_mst'];
					$buyer_name = $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
					$year 		= $job_arr[$sales_booking_no]["year"];
				}else{
					$order_no 	= $sales_arr[$val[csf('order_id')]]["sales_order_no"];
					$job_no_mst = "";
					$buyer_name = $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
					$year 		= $job_arr[$sales_booking_no]["year"];
				}
			}else{
				if ($val[csf('booking_without_order')]==0) 
				{
					$order_no   = $po_details_array[$val[csf('order_id')]]['po_number'];
					$job_no_mst = $po_details_array[$val[csf('order_id')]]['job_no'];
					$buyer_name = $po_details_array[$val[csf('order_id')]]['buyer_name'];
					$year 		= $po_details_array[$val[csf('order_id')]]['year'];
				}
				
			}
		  ?>
          <tr id="tr_1" align="center" valign="middle">
                <td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked"> &nbsp; &nbsp;<? echo $j; ?></td>
                <td width="80" id="barcode_<? echo $j; ?>"> <? echo $val[csf('barcode_no')]; ?></td>
                <td width="50" id="rollNo_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('barcode_no')]]['roll_no']; ?></td>
                <td width="60" id="batchNo_<? echo $j; ?>"> <? echo $batch_name_array[$roll_details_array[$val[csf('barcode_no')]]['batch_id']]; ?></td>
                <td width="90" id="bodypart_<? echo $j; ?>"> <? echo $body_part[$val[csf('body_part_id')]]; ?></td>
                <td width="110" id="cons_<? echo $j; ?>"> <? echo $constructtion_arr[$val[csf('febric_description_id')]]." ".$composition_arr[$val[csf('febric_description_id')]]; ?></td>
                <td width="50" id="gsm_<? echo $j; ?>"> <? echo $val[csf('gsm')]; ?></td>
                <td width="50" id="dia_<? echo $j; ?>"> <? echo $val[csf('width')]; ?></td>
                <td width="70" id="color_<? echo $j; ?>"> <? echo $color_arr[$val[csf('color_id')]]; ?></td>
                <td width="70" id="widthType_<? echo $j; ?>"><? //echo $val[csf('id')]; ?></td>
                <td width="60" id="rollWgt_<? echo $j; ?>" align="right"><? echo $val[csf('roll_wgt')]; $grand_total += $val[csf('roll_wgt')];?></td>

                <td width="50" id="job_<? echo $j; ?>"><? echo $job_no_mst; ?></td>
                <td width="50" id="year_<? echo $j; ?>" align="center"><? if($val[csf('booking_without_order')]==0){echo $year;} ?></td>
                <td width="65" id="buyer_<? echo $j; ?>"><? echo $buyer_name; ?></td>
                <td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo $order_no; ?></td>

                <td width="80" id="knitcom_<? echo $j; ?>" style="word-break:break-all;" align="left">
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
                ?></td>
                <td width="100" id="bookProgram_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $val[csf("booking_no")]; ?></td>
                <td width="" id="basis_<? echo $j; ?>"> <? echo  $roll_details_array[$val[csf('barcode_no')]]['receive_basis']; ?> 
                 
                <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="<? echo $val[csf('id')]; ?>" />       
                <input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $val[csf('roll_id')]; ?>" />
                <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $val[csf("body_part_id")]; ?>"/>
                <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $val[csf("color_id")]; ?>" />
                <input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $val[csf("prod_id")]; ?>" />
                <input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $val[csf("order_id")]; ?>" />
                <input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $val[csf("roll_wgt")]; ?>"/>
                <input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $val[csf("width")]; ?>"/>
                <input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $val[csf("gsm")]; ?>"/>
                <input type="hidden" name="deterId_[]" id="deterId_<? echo $j; ?>" value="<? echo $val[csf("febric_description_id")]; ?>"/>
                <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $val[csf("receive_basis")]; ?>"/>
                <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $val[csf("knitting_source")]; ?>"/>
                <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $val[csf("knitting_company")]; ?>"/>
                <input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $po_details_array[$val[csf("order_id")]]['job_no_full']; ?>"/>
                <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $val[csf('booking_no')]; ?>"/>
                <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $val[csf('barcode_no')]; ?>"/>
                <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $j; ?>" value="<? echo $val[csf('rolltable_id')]; ?>"/>
                <input type="hidden" name="batchId[]" id="batchId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['batch_id']; ?>"/>
               <input type="hidden" name="widthType[]" id="widthType_<? echo $j; ?>"
                value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['dia_width_id']; ?>"/>
                <input type="hidden" name="bwoNo[]" id="bwoNo_<? echo $j; ?>" value="<? echo $val[csf('roll_booking_no')]; ?>"/>

                <input type="hidden" name="booking_without_order_status[]" id="booking_without_order_status_<? echo $j; ?>" value="<? echo $val[csf('booking_without_order')]; ?>"/>

    		 </td> 
           </tr>
           

            <?
            $j++;
		}
	    if(count($inserted_roll_arr)>0)  $roll_cond=" and c.roll_id not in (".implode(",",$inserted_roll_arr).") ";
		
		$sql = sql_select("select  b.id, a.issue_number, a.challan_no, a.order_id, a.issue_purpose,c.roll_id,c.barcode_no,c.roll_no
		 from inv_issue_master a,  inv_finish_fabric_issue_dtls b, pro_roll_details c
		 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=71 and c.entry_form=71 and  a.issue_number='".$val[csf('challan_no')]."' $roll_cond ");
		if(	count($sql)>0)
		{
 		foreach($sql as $inf)
		{
			
		?>
          <tr id="tr_1" align="center" valign="middle">
                <td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" > &nbsp; &nbsp;<? echo $j; ?>
                </td>
                <td width="80" id="barcode_<? echo $j; ?>"> <? echo $inf[csf('barcode_no')]; ?></td>
                <td width="50" id="rollNo_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('barcode_no')]]['roll_no']; ?></td>
                <td width="60" id="batchNo_<? echo $j; ?>"> <? echo $batch_name_array[$roll_details_array[$inf[csf('barcode_no')]]['batch_id']]; ?></td>
                <td width="90" id="bodypart_<? echo $j; ?>"> <? echo $body_part[$roll_details_array[$inf[csf('barcode_no')]]['body_part_id']]; ?></td>
                <td width="110" id="cons_<? echo $j; ?>">
                 <? echo $constructtion_arr[$roll_details_array[$inf[csf('barcode_no')]]['deter_d']]." ".$composition_arr[$roll_details_array[$inf[csf('barcode_no')]]['deter_d']]; ?></td>
                <td width="50" id="gsm_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('barcode_no')]]['gsm']; ?></td>
                <td width="50" id="dia_<? echo $j; ?>"> <? echo $roll_details_array[$inf[csf('barcode_no')]]['width']; ?></td>
                <td width="70" id="color_<? echo $j; ?>"> <? echo $color_arr[$roll_details_array[$inf[csf('barcode_no')]]['color_id']]; ?></td>
                <td width="70" id="widthType_<? echo $j; ?>"><? //echo $inf[csf('id')]; ?></td>
                <td width="60" id="rollWgt_<? echo $j; ?>" align="right">
				<? 
					echo $roll_details_array[$inf[csf('barcode_no')]]['qnty'];
				 	$grand_total += $roll_details_array[$inf[csf('barcode_no')]]['qnty'];
				?>
                 </td>  
                <td width="50" id="job_<? echo $j; ?>"><? echo $po_details_array[$roll_details_array[$inf[csf('barcode_no')]]['po_breakdown_id']]['job_no']; ?></td>
                <td width="50" id="year_<? echo $j; ?>" align="center"><? echo $po_details_array[$roll_details_array[$inf[csf('barcode_no')]]['po_breakdown_id']]['year']; ?></td>
                <td width="65" id="buyer_<? echo $j; ?>"><? echo $po_details_array[$roll_details_array[$inf[csf('barcode_no')]]['po_breakdown_id']]['buyer_name']; ?></td>
                <td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo $po_details_array[$roll_details_array[$inf[csf('barcode_no')]]['po_breakdown_id']]['po_number']; ?></td>
                <td width="80" id="knitcom_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$inf[csf('barcode_no')]]['knitting_company']; ?></td>
                <td width="100" id="bookProgram_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$inf[csf('barcode_no')]]['booking_no']; ?></td>
                <td width="" id="basis_<? echo $j; ?>"> <?  echo $roll_details_array[$inf[csf('barcode_no')]]['receive_basis']; ?> 
                
    <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="0" />                          
    <input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $inf[csf('roll_id')]; ?>" />
    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['roll_no']; ?>" />
    <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['body_part_id']; ?>"/>
    <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['color_id']; ?>" />
    <input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['prod_id']; ?>" />
    <input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['po_breakdown_id']; ?>" />
 
    <input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['qnty']; ?>"/>
    <input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['width']; ?>"/>
    <input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['gsm']; ?>"/>
    <input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['deter_d']; ?>"/>
    <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['receive_basis_id']; ?>"/>
    <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['knitting_source_id']; ?>"/>
    <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['knitting_company_id']; ?>"/>
    <input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $po_details_array[$roll_details_array[$inf[csf('barcode_no')]]['po_breakdown_id']]['job_no_full']; ?>"/>
     <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['booking_no']; ?>"/>
    <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $inf[csf('barcode_no')]; ?>"/>
    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $j; ?>" value=""/>
    <input type="hidden" name="batchId[]" id="batchId_<? echo $j; ?>" value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['batch_id']; ?>"/>
    <input type="hidden" name="widthType[]" id="widthType_<? echo $j; ?>"
                value="<? echo $roll_details_array[$inf[csf('barcode_no')]]['dia_width_id']; ?>"/>
   			 </td> 
          </tr>
            <?
			$j++;
		}
		?>
        <tr>
            <td colspan="9" align="right">&nbsp;</td>
            <td  align="right"><strong>Total :</strong>  </td>
            <td align="right" >&nbsp;<?php echo  $grand_total;?></td>
            <td colspan="8" align="right">&nbsp;</td>
       </tr>
        
        
        <?php
	}
}

//need for this
if($action=="grey_item_details")
{
	$datas = explode("_",$data);
	$data = $datas[0];
	$is_sales = $datas[1];

	$buyer_name_array=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$data_array=sql_select("SELECT a.job_no_prefix_num,a.job_no, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
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
	}

	$job_arr=array();
	$sql_job=sql_select("select b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date from wo_booking_dtls b,wo_po_break_down c, wo_po_details_master e where b.po_break_down_id=c.id and c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.booking_type in(1,4) group by b.booking_no,e.buyer_name, e.job_no_prefix_num,e.insert_date");

	foreach ($sql_job as $job_row) {
		$job_arr[$job_row[csf('booking_no')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
		$job_arr[$job_row[csf('booking_no')]]['year'] 				= date("Y", strtotime($job_row[csf("insert_date")]));
		$job_arr[$job_row[csf('booking_no')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
	}
	$sales_arr=array();
	$sql_sales=sql_select("select id,job_no,within_group,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
	foreach ($sql_sales as $sales_row) {
		$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 		= $sales_row[csf('job_no')];
		$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
		$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
	}
	
	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$inserted_roll=sql_select("select a.barcode_no from pro_roll_details a where a.status_active=1 and a.is_deleted=0 and a.entry_form=72 ");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[]=$inf[csf('barcode_no')];
	}
     $data_array=sql_select("SELECT a.id, a.entry_form,a.recv_number, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id,c.is_sales, b.body_part_id,b.trans_id, b.fabric_description_id, b.gsm, b.width,b.batch_id, b.color_id,c.barcode_no,b.dia_width_type, c.id,c.roll_id, c.roll_no, c.po_breakdown_id,c.qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0");
	 
	
	$roll_details_array=array(); $barcode_array=array(); 
	foreach($data_array as $row)
	{
		$roll_details_array[$row[csf("barcode_no")]]['mst_id']=$row[csf("id")];
		$roll_details_array[$row[csf("barcode_no")]]['company_id']=$row[csf("company_id")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$body_part[$row[csf("body_part_id")]];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis']=$receive_basis_arr[$row[csf("receive_basis")]];
		$roll_details_array[$row[csf("barcode_no")]]['receive_basis_id']=$row[csf("receive_basis")];
		$roll_details_array[$row[csf("barcode_no")]]['receive_date']=change_date_format($row[csf("receive_date")]);
		if(str_replace("'","",$row[csf("entry_form")])==68)
		{
			$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("recv_number")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_id']=$row[csf("id")];	
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("roll_id")];	
		}
		else
		{
			$roll_details_array[$row[csf("barcode_no")]]['booking_no']=$row[csf("booking_no")];
			$roll_details_array[$row[csf("barcode_no")]]['booking_id']=$row[csf("booking_id")];
			$roll_details_array[$row[csf("barcode_no")]]['roll_id']=$row[csf("id")];
		}
		$roll_details_array[$row[csf("barcode_no")]]['color_id']=$row[csf("color_id")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
		$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
		$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
		$roll_details_array[$row[csf("barcode_no")]]['dia_width_type']=$fabric_typee[$row[csf("dia_width_type")]];
		$roll_details_array[$row[csf("barcode_no")]]['dia_width_id']=$row[csf("dia_width_type")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part_id']=$row[csf("body_part_id")];
		$roll_details_array[$row[csf("barcode_no")]]['body_part']=$body_part[$row[csf("body_part_id")]];
		$roll_details_array[$row[csf("barcode_no")]]['batch_id']=$row[csf("batch_id")];
		$roll_details_array[$row[csf("barcode_no")]]['is_sales']=$row[csf("is_sales")];
		$roll_details_array[$row[csf("barcode_no")]]['batch_name']=$batch_name_array[$row[csf("batch_id")]];
		
		if($row[csf("knitting_source")]==1)
		{
			$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_name_array[$row[csf("knitting_company")]];
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
		
		$roll_details_array[$row[csf("barcode_no")]]['roll_no']=$row[csf("roll_no")];
		//$roll_details_array[$row[csf("barcode_no")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
		$roll_details_array[$row[csf("barcode_no")]]['qnty']=number_format($row[csf("qnty")],2,'.','');
		$barcode_array[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}
	
	//echo $roll_details_array[17020000734]['batch_id']."jahid<br>";
		//print_r($roll_details_array);die;
		$sql = sql_select("select  c.roll_id,c.barcode_no,b.batch_id,prod_id,b.issue_qnty,b.knitting_company,b.booking_no,b.basis,c.po_breakdown_id,c.booking_no as roll_booking_no,c.booking_without_order 
		 from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_roll_details c
		 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=71 and c.entry_form=71 and  a.issue_number='$data'  and  c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
		
		if(	count($sql)>0)
		{
        $issue_details_arr=array();
		$j=1;
		$sum_qty="";
 		foreach($sql as $val)
		{
		 if(!in_array($val[csf('barcode_no')],$inserted_roll_arr))
		 {
		 	$sum_qty			=$sum_qty+$roll_details_array[$val[csf('barcode_no')]]['qnty'];
		 	$sales_booking_no 	= $sales_arr[$val[csf('po_breakdown_id')]]["sales_booking_no"];
			$within_group 		= $sales_arr[$val[csf('po_breakdown_id')]]["within_group"];
			if ($is_sales == 1) {
				if($within_group == 1){
					$order_no 	= $sales_arr[$val[csf('po_breakdown_id')]]["sales_order_no"];
					$job_no_mst = $job_arr[$sales_booking_no]['job_no_mst'];
					$buyer_name = $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
					$year 		= $job_arr[$sales_booking_no]["year"];
				}else{
					$order_no 	= $sales_arr[$val[csf('po_breakdown_id')]]["sales_order_no"];
					$job_no_mst = "";
					$buyer_name = $buyer_name_array[$job_arr[$sales_booking_no]["buyer_name"]];
					$year 		= $job_arr[$sales_booking_no]["year"];
				}
			}else{
				if ($val[csf('booking_without_order')]==0) 
				{
					$job_no_mst = $po_details_array[$val[csf('po_breakdown_id')]]['job_no'];
					$order_no   = $po_details_array[$val[csf('po_breakdown_id')]]['po_number'];
					$buyer_name = $po_details_array[$val[csf('po_breakdown_id')]]['buyer_name'];
					$year 		= $po_details_array[$val[csf('po_breakdown_id')]]['year'];
				}
				
			}
	    ?>
          <tr id="tr_<? echo $j; ?>" align="center" valign="middle">
                <td width="50" id="sl_<? echo $j; ?>"><input type="checkbox" id="checkRow_<? echo $j; ?>" name="checkRow[]" checked="checked"> &nbsp; &nbsp;<? echo $j; ?></td>
                <td width="80" id="barcode_<? echo $j; ?>"> <? echo $val[csf('barcode_no')]; ?></td>
                <td width="50" id="rollNo_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('barcode_no')]]['roll_no']; ?></td>
                <td width="60" id="batchNo_1" style="word-break:break-all;"><? echo $batch_name_array[$roll_details_array[$val[csf('barcode_no')]]['batch_id']]; ?></td>
                <td width="90" id="bodypart_1<? echo $j; ?>" style="word-break:break-all;"> <? echo $body_part[$roll_details_array[$val[csf('barcode_no')]]['body_part_id']]; ?></td>
                <td width="110" id="cons_1<? echo $j; ?>" style="word-break:break-all;"> <? echo $constructtion_arr[$roll_details_array[$val[csf('barcode_no')]]['deter_d']]," ".$composition_arr[$roll_details_array[$val[csf('barcode_no')]]['deter_d']]; ?></td>
                <td width="50" id="gsm_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('barcode_no')]]['gsm']; ?></td>
                <td width="50" id="dia_<? echo $j; ?>"> <? echo $roll_details_array[$val[csf('barcode_no')]]['width']; ?></td>
                <td width="70" id="color_<? echo $j; ?>" style="word-break:break-all;"> <? echo $color_arr[$roll_details_array[$val[csf('barcode_no')]]['color_id']]; ?></td>
                <td width="70" id="widthType_<? echo $j; ?>"><?  echo $roll_details_array[$val[csf('barcode_no')]]['dia_width_type']; ?></td>
                <td width="60" id="rollWgt_<? echo $j; ?>" align="right"><? echo $roll_details_array[$val[csf('barcode_no')]]['qnty']; ?></td>  
                
                <td width="50" id="job_<? echo $j; ?>"><? echo $job_no_mst; ?></td>
                <td width="50" id="year_<? echo $j; ?>" align="center"><? if($val[csf('booking_without_order')]==0){echo $year;} ?></td>
                <td width="65" id="buyer_<? echo $j; ?>"><? echo $buyer_name; ?></td>
                <td width="80" id="order_<? echo $j; ?>" style="word-break:break-all;" align="center"><? echo $order_no; ?></td>

                <td width="80" id="knitcom_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$val[csf('barcode_no')]]['knitting_company']; ?></td>
                <td width="100" id="bookProgram_<? echo $j; ?>" style="word-break:break-all;" align="left"><? echo $roll_details_array[$val[csf('barcode_no')]]['booking_no']; ?></td>
                <td width="" id="basis_<? echo $j; ?>" style="word-break:break-all;">
                <? echo $roll_details_array[$val[csf('barcode_no')]]['receive_basis']; ?> 
                 
    <input type="hidden" name="updateDetaisId[]" id="updateDetaisId_<? echo $j; ?>" value="0" />                          
    <input type="hidden" name="rollId[]" id="rollId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['roll_id']; ?>" />
    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['roll_no']; ?>" />
    <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['body_part_id']; ?>"/>
    <input type="hidden" name="colorId[]" id="colorId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['color_id']; ?>" />
    <input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['deter_d']; ?>"/>
    <input type="hidden" name="productId[]" id="productId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['prod_id']; ?>" />
    <input type="hidden" name="orderId[]" id="orderId_<? echo $j; ?>" value="<? echo $val[csf('po_breakdown_id')]; ?>" />
    <input type="hidden" name="buyerId[]" id="buyerId_<? echo $j; ?>" value="<? echo $po_details_array[$val[csf('po_breakdown_id')]]['buyer_id']; ?>"/>  
    <input type="hidden" name="rolWgt[]" id="rolWgt_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['qnty']; ?>"/>
    <input type="hidden" name="rollDia[]" id="rollDia_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['width']; ?>"/>
    <input type="hidden" name="rollGsm[]" id="rollGsm_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['gsm']; ?>"/>
    <input type="hidden" name="deterId[]" id="deterId_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['dtls_id']; ?>"/>
    <input type="hidden" name="receiveBasis[]" id="receiveBasis_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['receive_basis_id']; ?>"/>
    <input type="hidden" name="knittingSource[]" id="knittingSource_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['knitting_source_id']; ?>"/>
    <input type="hidden" name="knittingComp[]" id="knittingComp_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['knitting_company_id']; ?>"/>
    <input type="hidden" name="jobNo[]" id="jobNo_<? echo $j; ?>" value="<? echo $po_details_array[$val[csf('po_breakdown_id')]]['job_no_full']; ?>"/>
     <input type="hidden" name="bookingNo[]" id="bookingNo_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['booking_no']; ?>"/>
     <input type="hidden" name="barcodNumber[]" id="barcodNumber_<? echo $j; ?>" value="<? echo $val[csf('barcode_no')]; ?>"/>
     <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $j; ?>" value=""/>
     <input type="hidden" name="batchId[]" id="batchId_<? echo $j; ?>" title="<? echo $val[csf('barcode_no')]; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['batch_id']; ?>"/>
     <input type="hidden" name="widthType[]" id="widthType_<? echo $j; ?>" value="<? echo $roll_details_array[$val[csf('barcode_no')]]['dia_width_id']; ?>"/>
     <input type="hidden" name="IsSalesId[]" id="IsSalesId_<? echo $j; ?>" value="<?php echo $roll_details_array[$val[csf('barcode_no')]]['is_sales']; ?>"/>
     <input type="hidden" name="bwoNo[]" id="bwoNo_<? echo $j; ?>" value="<?php echo $val[csf('roll_booking_no')]; ?>"/>
     <input type="hidden" name="booking_without_order_status[]" id="booking_without_order_status_<? echo $j; ?>" value="<?php echo $val[csf('booking_without_order')]; ?>"/>

                               
            </tr>
            <?
			$j++;
		  }
		}
		//echo $sum_qty;
		?>
		<tr>
			<td colspan="10" align="right"> Total: </td>
			<td  align="right"> <? echo number_format($sum_qty,2);?></td> 
			<td colspan="7" align="right"> </td>
		</tr>

		<?
	}
	
	//print_r($issue_details_arr); buyer_id
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
    $batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	$sql=sql_select("select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no,a.reqsn_no, a.recv_number,a.company_id,a.batch_no, a.receive_purpose,
	a.dyeing_source,a.dyeing_company,a.receive_date
	from  inv_receive_mas_batchroll a
	where  a.id=$data and a.status_active=1 and is_deleted=0 ");

	foreach($sql as $val)
	{
		echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
		echo "document.getElementById('txt_reqsn_no').value  = '".($batch_arr[$val[csf("reqsn_no")]])."';\n"; 
		echo "document.getElementById('txt_batch_id').value  = '".($val[csf("batch_no")])."';\n"; 
		echo "document.getElementById('txt_batch_id').value  = '".($val[csf("batch_no")])."';\n"; 
		echo "document.getElementById('txt_delivery_date').value  = '".change_date_format($val[csf("receive_date")])."';\n"; 
		echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("dyeing_source")])."';\n"; 
		echo "load_drop_down( 'requires/finish_feb_receive_by_cutting_controller', '".$val[csf("dyeing_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n"; 
		echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("dyeing_company")])."';\n";  
		echo "document.getElementById('cbo_basis').value  = '".($val[csf("receive_purpose")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
	
	}
}

if($action=="load_php_form")
{
	$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst",'id','batch_no');
	$sql=sql_select("select  a.issue_number, a.challan_no, a.order_id,a.company_id, a.batch_no,a.req_no, a.issue_purpose,a.issue_basis,
	a.knit_dye_source,a.knit_dye_company,a.issue_number_prefix_num,a.issue_date from inv_issue_master a
	where  a.entry_form=71 and   a.issue_number='$data'  ");
	foreach($sql as $val)
	{
	echo "document.getElementById('cbo_company_id').value  = '".($val[csf("company_id")])."';\n"; 
	echo "document.getElementById('txt_batch_no').value  = '".($batch_arr[$val[csf("batch_no")]])."';\n"; 
	echo "document.getElementById('txt_batch_id').value  = '".($val[csf("batch_no")])."';\n"; 
	echo "document.getElementById('txt_reqsn_no').value  = '".($val[csf("req_no")])."';\n"; 
	echo "document.getElementById('cbo_knitting_source').value  = '".($val[csf("knit_dye_source")])."';\n"; 
	echo "load_drop_down( 'requires/finish_feb_receive_by_cutting_controller', '".$val[csf("knit_dye_source")]."_".$val[csf("company_id")]."', 'load_drop_down_knitting_com', 'knitting_com');\n"; 
	echo "document.getElementById('cbo_knitting_company').value  = '".($val[csf("knit_dye_company")])."';\n";  
	echo "document.getElementById('cbo_basis').value  = '".($val[csf("issue_purpose")])."';\n"; 
	echo "document.getElementById('update_id').value  = '".($val[csf("id")])."';\n";
	
	}
}

if($action=="challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 

	<script>
	
		function js_set_value(data,id,is_sales)
		{
			$('#hidden_challan_no').val(data);
			$('#hidden_challan_id').val(id);
			$('#hidden_is_sales').val(is_sales);
			parent.emailwindow.hide();
		}
	
    </script>

</head>

<body>
<div align="center" style="width:760px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:760px; margin-left:2px">
		<legend>Enter search words</legend>           
            <table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
                    <th>Delivery Date Range</th>
                    <th>Search By</th>
                    <th id="search_by_td_up" width="180">Please Enter Challan No</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">  
                        <input type="hidden" name="hidden_challan_id" id="hidden_challan_id">  
                        <input type="hidden" name="hidden_is_sales" id="hidden_is_sales">  
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 150,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
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
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value, 'create_challan_search_list_view', 'search_div', 'finish_feb_receive_by_cutting_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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



if($action=="create_challan_search_list_view")
{
	
	$data = explode("_",$data);
	$search_string="%".trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	if($company_id==0) { echo "Please Select Company First."; die; }
	
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
	if($search_by==1) $search_field_cond="and issue_number_prefix_num like '$search_string'";
	}
	
	if($db_type==0) 
	{
	$year_field=" YEAR(insert_date) as year";
		
	}
	else if($db_type==2) 
	{
	$year_field=" to_char(insert_date,'YYYY') as year";

	}
	else $year_field="";//defined Later
	
	$data_array=sql_select("SELECT c.barcode_no,c.is_sales,a.issue_number FROM inv_issue_master a,pro_roll_details c 
	WHERE  a.id=c.mst_id and c.entry_form=71 and a.entry_form=71  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0");
	$challan_barcode=array();
	$inserted_barcode=array();
	foreach($data_array as $val)
	{
	$challan_barcode[$val[csf('issue_number')]][]=$val[csf('barcode_no')];
	$challan_is_sales[$val[csf('issue_number')]]['is_sales']=$val[csf('is_sales')];
	}

	$inserted_roll=sql_select("select b.challan_no,a.barcode_no from pro_roll_details a,inv_receive_mas_batchroll b where a.mst_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.entry_form=72 and b.entry_form=72");
	foreach($inserted_roll as $b_id)
	{
	$inserted_barcode[$b_id[csf('challan_no')]][]=$b_id[csf('barcode_no')];	
	}
	$sql="select  a.id,a.issue_number, a.challan_no, a.order_id,a.company_id,a.issue_purpose,a.issue_basis,a.knit_dye_source,a.knit_dye_company,
	a.issue_number_prefix_num,a.issue_date,$year_field
	from inv_issue_master a
	where  a.entry_form=71  and  company_id=$company_id $search_field_cond $date_cond ";
	
	//echo $sql;die;
	$result = sql_select($sql);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="80">System No</th>
            <th width="70">Year</th>
            <th width="120">Knitting Source</th>
            <th width="140">Knitting Company</th>
            <th>Issue date</th>
        </thead>
	</table>
	<div style="width:740px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            { 
			 if(count($challan_barcode[$row[csf('issue_number')]])-count($inserted_barcode[$row[csf('issue_number')]])>0)
			 {
			 	$is_sales=$challan_is_sales[$row[csf('issue_number')]]['is_sales'];
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				$knit_comp="&nbsp;";
                if($row[csf('knit_dye_source')]==1) $knit_comp=$company_arr[$row[csf('knit_dye_company')]]; 
				else $knit_comp=$supllier_arr[$row[csf('knit_dye_company')]];
			
        	?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('issue_number')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $is_sales; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="80"><p>&nbsp;<? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
                    <td width="70" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                    <td align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
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
	echo load_html_head_contents("Challan Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
?> 
	<script>
		function js_set_value(data,id,challan)
		{
		$('#hidden_receive_no').val(data);
		$('#hidden_update_id').val(id);
		$('#hidden_challan_no').val(challan);
		parent.emailwindow.hide();
		}
    </script>

</head>

<body>
<div align="center" style="width:780px;" >
	<form name="searchwofrm"  id="searchwofrm">
		<fieldset style="width:780px; margin-left:2px">
		<legend>Receive Number Popup</legend>           
            <table cellpadding="0" cellspacing="0" width="780" border="1" rules="all" class="rpt_table" align="center">
                <thead>
                    <th>Company</th>
                    <th>Receive No</th>
                    <th id="" width="250">Receive Date</th>
                    <th>
                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_receive_no" id="hidden_receive_no">  
                        <input type="hidden" name="hidden_update_id" id="hidden_update_id">
                        <input type="hidden" name="hidden_challan_no" id="hidden_challan_no">    
                    </th> 
                </thead>
                <tr class="general">
                    <td align="center">
                    	 <? echo create_drop_down( "cbo_company_id", 170,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name",'id,company_name', 1, '-- Select Company --',0,"",0); ?>        
                    </td>
                    <td align="center">
                    <input type="text" style="width:140px" class="text_boxes"  name="txt_receive_number" id="txt_receive_number" />
                    
					</td>
                    <td align="center">	
                    	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:90px" readonly>To
					  	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:90px" readonly>
                    </td>     
                  						
            		<td align="center">
                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_receive_number').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value, 'create_update_search_list_view', 'search_div', 'finish_feb_receive_by_cutting_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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



if($action=="create_update_search_list_view")
{
	
	$data = explode("_",$data);
	//$search_string="%".trim($data[0]);
	$receive_number=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[0];
	$year_id =$data[4];
	if($company_id==0) { echo "Please Select Company First."; die; }
	
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
	if(trim($receive_number)!="")
	{
	
		if($db_type==0) 
		{
			$receiv_cond="and a.recv_number_prefix_num=$receive_number and YEAR(insert_date)=$year_id ";
			
		}
		else if($db_type==2) 
		{
			$receiv_cond="and a.recv_number_prefix_num=$receive_number and to_char(a.insert_date,'YYYY')=$year_id ";
	
		}
	
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
	
	$sql="select  a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source, a.dyeing_company, a.receive_date, sum(b.qnty) as roll_qty,$year_field from  inv_receive_mas_batchroll a, pro_roll_details b where a.id=b.mst_id and b.entry_form=72 and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.entry_form=72 and a.company_id=$company_id $receiv_cond $date_cond group by a.id,a.recv_number_prefix_num,a.recv_number, a.challan_no, a.recv_number,a.company_id, a.dyeing_source, a.dyeing_company, a.receive_date, a.insert_date order by  a.recv_number_prefix_num, a.receive_date";
			
	$result = sql_select($sql);
//print_r($result);
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supllier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table">
        <thead>
            <th width="40">SL</th>
            <th width="140">Company</th>
            <th width="70">Receive No</th>
            <th width="60">Year</th>
            <th width="120">Dyeing Source</th>
            <th width="140">Dyeing Company</th>
            <th width="80">Receive date</th>
            <th width="">Quantity</th>
        </thead>
	</table>
	<div style="width:780px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search">  
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
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('recv_number')]; ?>','<? echo $row[csf('id')]; ?>','<? echo $row[csf('challan_no')]; ?>');"> 
                    <td width="40"><? echo $i; ?></td>
                    <td width="140"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
                    <td width="70"><p>&nbsp;<? echo $row[csf('recv_number_prefix_num')]; ?></p></td>
                    <td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
                    <td width="120"><p><? echo $knitting_source[$row[csf('dyeing_source')]]; ?>&nbsp;</p></td>
                    <td width="140"><p><? echo $knit_comp; ?>&nbsp;</p></td>
                    <td align="center" width="80"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                    <td align="right"><? echo $row[csf('roll_qty')]; ?></td>
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




if($action=="roll_receive_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_system_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
    $batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$mstData=sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");
	$job_array=array();
	$job_sql="select a.job_no_prefix_num,a.buyer_name, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
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
    <div style="width:1330px;">
    	<table width="1330" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px"><strong><u>Finish Fabric Roll Receive By Cutting</u></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Receive Challan: <? echo $txt_system_no;?></u></strong></td>
			</tr>
        </table> 
        <br>
        <?
		$dataarray=sql_select("select a.recv_number,a.challan_no, a.dyeing_source,a.dyeing_company,a.receive_date,a.batch_no,a.reqsn_no,a.receive_purpose
		from  inv_receive_mas_batchroll a
		where a.id=$update_id and a.entry_form=72 and a.company_id=$company ");

		?>
		<table width="1000" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="120">Challan No</td>
                <td width="170">:&nbsp;<? echo $dataarray[0][csf('challan_no')]; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="120">Company</td>
                <td width="170">:&nbsp;<? echo $company_data[$company]['name']; ?></td>
                <td style="font-size:16px; font-weight:bold;" width="120">Dyeing Source</td>
                <td width="170">:&nbsp;<? echo $knitting_source[$dataarray[0][csf('dyeing_source')]]; ?></td>
                
			</tr>
            <tr>
				<td style="font-size:16px; font-weight:bold;">Dyeing Company</td>
                <td width="170">:&nbsp;<? if($dataarray[0][csf('dyeing_source')]==1) $dyecom=$company_array[$dataarray[0][csf('dyeing_company')]]['name'];
				                          else                                       $dyecom=$supplier_arr[$dataarray[0][csf('dyeing_company')]];
										  echo $dyecom 
									   ?>
				</td>
                <td style="font-size:16px; font-weight:bold;">Receive Date</td>
                <td width="170">:&nbsp;<? echo change_date_format($dataarray[0][csf('receive_date')]); ?></td>
                <td style="font-size:16px; font-weight:bold;">Purpose</td>
                <td width="170">:&nbsp;<? echo $yarn_issue_purpose[$dataarray[0][csf('receive_purpose')]]; ?></td>
			</tr>
            <tr>
                <td style="font-size:16px; font-weight:bold;"><!--Batch No --></td>
                <td width="170"><!--:&nbsp;<?//echo $dataarray[0][csf('batch_no')]; ?>--></td>
                <td style="font-size:16px; font-weight:bold;"><!--Reqsn No--></td>
                <td width="170"><!--:&nbsp;<?//echo $dataarray[0][csf('reqsn_no')]; ?>--></td>
            	<td width="" id="barcode_img_id" align="left" colspan="2"></td>
            </tr>
		</table>
       <?
	  $sql_update=sql_select("select  b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id,c.roll_no,b.batch_id,
	   b.febric_description_id,b.gsm,b.width,b.roll_wgt,b.roll_id,b.buyer_id,b.order_id,b.color_id,a.challan_no,c.barcode_no,c.id as rolltable_id
	   from pro_grey_batch_dtls b,inv_receive_mas_batchroll a, pro_roll_details c
	   where a.id=b.mst_id and a.id=$update_id and b.id=c.dtls_id and a.is_deleted=0 and c.entry_form=72 and a.entry_form=72 and a.status_active=1
	   and b.status_active=1 and b.is_deleted=0 
	   and c.status_active=1 and c.is_deleted=0");
	?>
    <br>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
            <thead>
                <tr>
                   <th width="50">SL</th>
                        <th width="90">Barcode No</th>
                        <th width="60">Batch No</th>
                        <th width="90">Body Part</th>
                        <th width="110">Const./ Composition</th>
                        <th width="50">Gsm</th>
                        <th width="50">Dia</th>
                        <th width="70">Color</th>
                        <th width="50">Job <br> Buyer</th>
                        <th width="80">Order No</th>
                        <th width="80">Kniting Com</th>
                        <th width="100">Program/ Booking /Pi No</th>
                        <th width="">Basis</th>
                        <th width="50">Roll No</th>
                        <th width="60">Roll Wgt.</th>
                    </thead>
                </tr>
            </thead>
               <?
			   $i=1;
			   foreach($sql_update as $row)
			   {
				   $knit_company="";
				   	if($row[csf("knitting_source")]==1)
					{
						$knit_company=$company_array[$row[csf('knitting_company')]]['name'];
					}
					else if($row[csf("knitting_source")]==3)
					{
						$knit_company=$supplier_arr[$row[csf("knitting_company")]];
					}
				   ?>
           			 <tr>
                        <td width="30"><? echo $i; ?></td>
                        <td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        
                        <td width="40" align="center"><? echo $batch_name_array[$row[csf('batch_id')]]; ?></td>
                        <td width="40" align="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                        <td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                        <td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
                        <td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="60"><? echo $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']]."<br>".$job_array[$row[csf('order_id')]]['job']; ?></td>
                        <td width="90" style="word-break:break-all;"><? echo $job_array[$row[csf('order_id')]]['po']; ?></td>
         			    <td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
                        <td width="100" style="word-break:break-all;"><? echo $row[csf('booking_no')]; ?></td>
                 
                        <td width="100"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
         
                        <td width="40" align="center" style="word-break:break-all;"><? echo $row[csf('roll_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('roll_wgt')],2); ?></td>
                    </tr>
               <?
			   $tot_qty+=$row[csf('roll_wgt')];
			   $i++;
			   }
			   ?>
                <tr> 
                <td align="right" colspan="14"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
          </table>
    
	</div>
    <? echo signature_table(44, $company, "1330px"); ?>
   	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
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
		generateBarcode('<? echo $txt_system_no; ?>');
	</script>
<?
exit();
}


if($action=="fabric_details_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	
	$company=$data[0];
	$txt_system_no=$data[1];
	$update_id=$data[2];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
    $batch_name_array=return_library_array( "select id, batch_no from  pro_batch_create_mst", "id", "batch_no");
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier","id","short_name");
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$mstData=sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");
	$job_array=array();
	$job_sql="select a.job_no_prefix_num,a.buyer_name, a.job_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result=sql_select($job_sql);
	foreach($job_sql_result as $row)
	{
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
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
    <div style="width:1330px;">
    	<table width="1330" cellspacing="0" align="center" border="0">
			<tr>
				<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px"><strong><u>Receive Challan: <? echo $txt_system_no;?></u></strong></td>
			</tr>
        </table> 
       
       <?
	  $sql_update=sql_select("select  b.id,b.knitting_company,b.knitting_source,b.booking_no,b.receive_basis,b.prod_id,b.body_part_id,c.roll_no,b.batch_id,
	   b.febric_description_id,b.gsm,b.width,b.roll_wgt,b.roll_id,b.buyer_id,b.order_id,b.color_id,a.challan_no,c.barcode_no,c.id as rolltable_id
	   from pro_grey_batch_dtls b,inv_receive_mas_batchroll a, pro_roll_details c
	   where a.id=b.mst_id and a.id=$update_id and b.id=c.dtls_id and a.is_deleted=0 and c.entry_form=72 and a.entry_form=72 and a.status_active=1
	   and b.status_active=1 and b.is_deleted=0 
	   and c.status_active=1 and c.is_deleted=0");
	?>
    <br>
     <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
            <thead>
                <tr>
                   <th width="50">SL</th>
                        <th width="90">Barcode No</th>
                        <th width="60">Batch No</th>
                        <th width="90">Body Part</th>
                        <th width="110">Const./ Composition</th>
                        <th width="50">Gsm</th>
                        <th width="50">Dia</th>
                        <th width="70">Color</th>
                        <th width="50">Job <br> Buyer</th>
                        <th width="80">Order No</th>
                        <th width="80">Kniting Com</th>
                        <th width="100">Program/ Booking /Pi No</th>
                        <th width="">Basis</th>
                        <th width="50">Roll No</th>
                        <th width="60">Roll Wgt.</th>
                    </thead>
                </tr>
            </thead>
               <?
			   $i=1;
			   foreach($sql_update as $row)
			   {
				   $knit_company="";
				   	if($row[csf("knitting_source")]==1)
					{
						$knit_company=$company_array[$row[csf('knitting_company')]]['name'];
					}
					else if($row[csf("knitting_source")]==3)
					{
						$knit_company=$supplier_arr[$row[csf("knitting_company")]];
					}
				   ?>
           			 <tr>
                        <td width="30"><? echo $i; ?></td>
                        <td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        
                        <td width="40" align="center"><? echo $batch_name_array[$row[csf('batch_id')]]; ?></td>
                        <td width="40" align="center"><? echo $body_part[$row[csf('body_part_id')]]; ?></td>
                        <td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                        <td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
                        <td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
                        <td width="70" style="word-break:break-all;"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                        <td width="60"><? echo $buyer_array[$job_array[$row[csf('po_breakdown_id')]]['buyer_name']]."<br>".$job_array[$row[csf('order_id')]]['job']; ?></td>
                        <td width="90" style="word-break:break-all;"><? echo $job_array[$row[csf('order_id')]]['po']; ?></td>
         			    <td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
                        <td width="100" style="word-break:break-all;"><? echo $row[csf('booking_no')]; ?></td>
                 
                        <td width="100"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
         
                        <td width="40" align="center" style="word-break:break-all;"><? echo $row[csf('roll_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('roll_wgt')],2); ?></td>
                    </tr>
               <?
			   $tot_qty+=$row[csf('roll_wgt')];
			   $i++;
			   }
			   ?>
                <tr> 
                <td align="right" colspan="14"><strong>Total</strong></td>
                <td align="right"><? echo number_format($tot_qty,2,'.',''); ?></td>
			</tr>
          </table>
    
	</div>
    <? echo signature_table(74, $company, "1330px"); 
exit();
}

?>

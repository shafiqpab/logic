<?php
ini_set('precision', 8);
ini_set("display_errors", 0);
require_once('../../includes/common.php');
$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0 ", "id", "company_short_name",$con);
$user_id=112299001;
$current_date = change_date_format(date("Y-m-d",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
$previous_date= change_date_format(date('Y-m-d', strtotime('-1 day', strtotime($current_date))),'','',1);
//$previous_date='05-Sep-2022';$current_date='05-Sep-2022';

$from_date=$previous_date;
$to_date=$previous_date;
$companyStr=implode(',',array_keys($company_library));


function fnc_tempengine($table_name, $user_id, $entry_form, $ref_value_arr)
{
	global $con ;
	$numeless=count($ref_value_arr);
	$psql = "BEGIN PRC_TEMP_BARCODE_INSERT(:user_id,:type,:po_arr); END;";
	$stmt = oci_parse($con,$psql);
	oci_bind_by_name($stmt,":user_id",$user_id);
	oci_bind_by_name($stmt,":type",$entry_form);
	oci_bind_array_by_name($stmt, ":po_arr", $ref_value_arr, $numeless, -1, SQLT_INT);
	oci_execute($stmt); 
	oci_commit($con);
	disconnect($con);
}


function fnc_tempenginepo($table_name, $user_id, $entry_form, $ref_from, $ref_id_arr,  $ref_str_arr)
{
	global $con ;
	$numeless=count($ref_id_arr);
	$psql = "BEGIN PRC_TEMPENGINE(:in_user_id,:in_ref_from,:in_entry_form,:in_ref_id_arr, :in_ref_table); END;";//:in_ref_str_arr, 
	$stmt = oci_parse($con,$psql);
	oci_bind_by_name($stmt,":in_user_id",$user_id);
	oci_bind_by_name($stmt,":in_entry_form",$entry_form);
	oci_bind_by_name($stmt,":in_ref_from",$ref_from);
	
	oci_bind_array_by_name($stmt, ":in_ref_id_arr", $ref_id_arr, $numeless, -1, SQLT_INT);
	//oci_bind_array_by_name($stmt, ":in_ref_str_arr", $ref_str_arr, $numeless, -1, SQLT_CHR);
	
	oci_bind_by_name($stmt,":in_ref_table",$table_name);
	oci_execute($stmt); 
	//echo "jahid";
	oci_commit($con);
	disconnect($con);
}
//------------------------------------------------------------------------------------------------------


	// $cbo_company_id=1;
	// $cbo_value_with=2;
	// $txt_date_from=$previous_date;
	// $txt_date_to=$previous_date;
	// $cbo_store_wise=2;

	$action ="report_generate";
	$cbo_company_id =	"'1'";
	$cbo_within_group =	"'0'";
	$cbo_pocompany_id =	"''";
	$cbo_buyer_id =	"''";
	$cbo_year =	"'0'";
	$txt_booking_no =	"''";
	$txt_booking_id =	"''";
	$txt_order_no =	"''";
	$hide_order_id =	"''";
	$txt_date_from =	"'12-Sep-2022'";
	$txt_date_to =	"'12-Sep-2022'";
	$cbo_value_with =	"'2'";
	$cbo_get_upto =	"'0'";
	$txt_days =	"''";
	$cbo_get_upto_qnty =	"'0'";
	$txt_qnty =	"''";
	$cbo_store_wise =	"'2'";
	$cbo_store_name =	"'0'";
	$report_title =	"Style Wise Grey Fabric Stock Report-Sales";
 

		$started = microtime(true);
		$company_name  	= str_replace("'","",$cbo_company_id);
		$within_group  	= str_replace("'","",$cbo_within_group);
		$pocompany_id 	= str_replace("'","",$cbo_pocompany_id);
		$po_buyer_id 	= str_replace("'","",$cbo_buyer_id);
		$cbo_year 		= str_replace("'","",$cbo_year);
		$booking_no 	= str_replace("'","",$txt_booking_no);
		$booking_id 	= str_replace("'","",$txt_booking_id);
		$order_no 		= trim(str_replace("'","",$txt_order_no));
		$order_id 		= str_replace("'","",$hide_order_id);
		$cbo_value_with = str_replace("'","",$cbo_value_with);
	
		$date_from 		= str_replace("'","",$txt_date_from);
		$date_to 		= str_replace("'","",$txt_date_to);
	
		$cbo_store_wise = str_replace("'","",$cbo_store_wise);
		$cbo_store_name = str_replace("'","",$cbo_store_name);
	
		$get_upto 		= str_replace("'","",$cbo_get_upto);
		$txt_days 		= str_replace("'","",$txt_days);
		$get_upto_qnty 	= str_replace("'","",$cbo_get_upto_qnty);
		$txt_qnty 		= str_replace("'","",$txt_qnty);  
	
		$store_arr 	 	= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
		$company_arr 	= return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
		$buyer_arr   	= return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0", 'id','short_name');
		$season_arr  	= return_library_array( "select id, season_name from LIB_BUYER_SEASON",'id','season_name');
		$yarn_count_arr = return_library_array("select id,yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_arr 		= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	
	
		if($pocompany_id!=0 || $pocompany_id!=""){
	
			if($within_group==1)
			{
				$pocompany_cond="and d.po_company_id in($pocompany_id)";
				$pocompany_cond2="and f.po_company_id in($pocompany_id)";
			}else {
				$pocompany_cond="and d.company_id in($pocompany_id)";
				$pocompany_cond2="and f.po_company_id in($pocompany_id)";
			}
		} else {
			$pocompany_cond="";
		}
	
		if($cbo_store_wise==1){
			if($cbo_store_name)
			{
				$store_cond = " and e.store_id=$cbo_store_name";
				$store_cond2 = " and a.store_id=$cbo_store_name";
			}
	
	
		}
	
		if($po_buyer_id!=0 || $po_buyer_id!="")
		{
			if($within_group==1)
			{
				$buyer_id_cond=" and d.po_buyer in (".str_replace("'","",$po_buyer_id).")";
				$buyer_id_cond2=" and f.po_buyer in (".str_replace("'","",$po_buyer_id).")";
			}else {
	
				$buyer_id_cond=" and d.buyer_id in (".str_replace("'","",$po_buyer_id).")";
				$buyer_id_cond2=" and f.buyer_id in (".str_replace("'","",$po_buyer_id).")";
			}
		}
	
		$date_cond="";
		if($date_from!="" && $date_to!="")
		{
			if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
			else if($db_type==2) $start_date=change_date_format($date_from,"","",1);
	
			if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
			else if($db_type==2) $end_date=change_date_format($date_to,"","",1);
	
			$date_cond=" and e.transaction_date <= '$end_date'";
		}
	
		if ($order_id=='') $order_no_cond=""; else $order_no_cond="and d.id='$order_id'";
		if ($order_id=='') $toOrderIdCond=""; else $toOrderIdCond="and d.po_breakdown_id='$order_id'";
	
		if ($order_no=='') $sales_order_no_cond=""; else $sales_order_no_cond=" and d.job_no like '%$order_no%'";
		if ($order_no=='') $sales_to_order_no_cond=""; else $sales_to_order_no_cond=" and f.job_no like '%$order_no%'";
	
	
		if($date_from=="")
		{
			if ($db_type == 0)
			{
				if($cbo_year>0)
				{
					$sales_order_year_condition=" and YEAR(d.insert_date)=$cbo_year";
					$sales_order_year_condition2=" and YEAR(f.insert_date)=$cbo_year";
				}
			}
			else if ($db_type == 2)
			{
				if($cbo_year>0)
				{
					$sales_order_year_condition=" and to_char(d.insert_date,'YYYY')=$cbo_year";
					$sales_order_year_condition2=" and to_char(f.insert_date,'YYYY')=$cbo_year";
				}
			}
		} else {
			$sales_order_year_condition="";
		}
	
		if ($program_no=="") $program_no_cond=""; else $program_no_cond="and b.booking_no='$program_no'";
	
		if ($booking_no!="")
		{
			$booking_no_cond=" and d.sales_booking_no like '%$booking_no'";
			$booking_no_cond2=" and f.sales_booking_no like '%$booking_no'";
		} else {
			$booking_no_cond=$booking_no_cond2="";
		}
	
		if($within_group !=0)
		{
			$within_group_cond = " and d.within_group='$within_group' ";
			$within_group_cond2 = " and f.within_group='$within_group' ";
		}
		else
		{
			$within_group_cond = $within_group_cond2="";
		}
	
		$con = connect();
		execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id." and ENTRY_FORM=868");
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USERID = ".$user_id." and TYPE=868");
		oci_commit($con);
	
		$booking_type_arr=array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");
		
		$sql = "SELECT b.qnty as receive_qty,b.po_breakdown_id as po_id, c.prod_id,c.febric_description_id,c.gsm,c.color_range_id,c.color_id,c.stitch_length, c.width,d.company_id,d.buyer_id, d.style_ref_no, d.job_no, d.job_no_prefix_num, d.sales_booking_no, d.booking_id,d.within_group,d.po_company_id as lc_company_id,d.po_buyer,d.po_job_no,d.booking_without_order,d.booking_type,d.booking_entry_form,e.transaction_date,e.store_id,b.barcode_no
		from inv_receive_master a,inv_transaction e,pro_grey_prod_entry_dtls c,pro_roll_details b,fabric_sales_order_mst d
		where a.receive_basis in(2,4,10) and a.item_category=13 and a.id=e.mst_id and e.status_active=1 and e.is_deleted=0 and e.transaction_type=1 and e.id=c.trans_id and c.id=b.dtls_id and b.po_breakdown_id=d.id $within_group_cond and b.entry_form in(58,2) and c.trans_id>0 and d.company_id=$company_name and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $order_no_cond $booking_no_cond $program_no_cond $sales_order_year_condition $date_cond $pocompany_cond $buyer_id_cond $testCond $store_cond $sales_order_no_cond";
	
		$all_po_arr=$receive_barcodes=array();
		$masterData=sql_select($sql);
		if(empty($masterData))
		{
			
			$trans_in_row = sql_select("SELECT a.company_id,a.to_order_id as po_id,b.from_prod_id as prod_id, e.color_range,d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no, d.po_company_id as lc_company_id,d.po_buyer, d.po_job_no, d.booking_without_order, d.booking_type, d.booking_entry_form , c.detarmination_id,c.gsm
				from inv_item_transfer_mst a,inv_item_transfer_dtls b left join ppl_planning_info_entry_dtls e on b.to_program = e.id, fabric_sales_order_mst d , product_details_master c
				where a.id=b.mst_id and a.to_order_id=d.id and b.from_prod_id = c.id and a.status_active=1 and b.status_active=1 and d.status_active=1 and a.entry_form=133 and a.transfer_criteria=4 and a.company_id = $company_name $order_no_cond $booking_no_cond $date_cond $sales_order_no_cond
				group by a.company_id,a.to_order_id,b.from_prod_id, e.color_range, d.sales_booking_no, d.buyer_id, d.style_ref_no, d.within_group, d.job_no,d.po_company_id, d.po_buyer,d.po_job_no, d.booking_without_order, d.booking_type,d.booking_entry_form,c.detarmination_id,c.gsm");
	
			foreach($trans_in_row as $row)
			{
				$poids .= $row[csf("po_id")].",";
				$prod_id .= $row[csf("prod_id")].",";
	
				$salesData[$row[csf("po_id")]]['working_company_id'] = $row[csf("company_id")];
				$salesData[$row[csf("po_id")]]['booking_no'] = $row[csf("sales_booking_no")];
				$salesData[$row[csf("po_id")]]['style_ref_no'] = $row[csf("style_ref_no")];
				$salesData[$row[csf("po_id")]]['within_group'] = $row[csf("within_group")];
				$salesData_color_range[$row[csf("po_id")]][$row[csf("detarmination_id")]][$row[csf("gsm")]]['color_range_id'] .= $row[csf("color_range")].",";
				$salesData[$row[csf("po_id")]]['lc_company_id'] = $row[csf("lc_company_id")];
				$salesData[$row[csf("po_id")]]['fso_no'] = $row[csf("job_no")];
	
				// within group yes
				if($row[csf("within_group")]==1)
				{
					$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("po_buyer")];
					$salesData[$row[csf("po_id")]]['job_no'] = $row[csf("po_job_no")];
				} else {
					$salesData[$row[csf("po_id")]]['buyer_id'] = $row[csf("buyer_id")];
					$salesData[$row[csf("po_id")]]['job_no'] = "";
				}
	
				if($row[csf('booking_type')] == 4)
				{
					if($row[csf('booking_without_order')] == 1)
					{
						$bookingType = "Sample Without Order";
					}
					else
					{
						$bookingType =  "Sample With Order";
					}
				}
				else
				{
					$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
				}
				$salesData[$row[csf("po_id")]]['booking_type'] = $bookingType;
			}
			unset($trans_in_row);
	
		}
		else
		{
			$prodWiseSalesDataStatus = $prodWiseOpening=array();
			$poarr=$barcodearr=array();
			foreach($masterData as $row)
			{
				$prod_id .= $row[csf("prod_id")].",";
				$barcode_no = $row[csf('barcode_no')];
				$poid = $row[csf("po_id")];
	
				$all_po_arr[$poid] = $poid;
				$determinationids .= ",".$row[csf('febric_description_id')];
				$receive_barcodes[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	
				$salesData[$poid]['booking_id'] = $row[csf("booking_id")];
				$salesData[$poid]['working_company_id'] = $row[csf("company_id")];
				$salesData[$poid]['booking_no'] = $row[csf("sales_booking_no")];
				$salesData[$poid]['style_ref_no'] = $row[csf("style_ref_no")];
				$salesData[$poid]['within_group'] = $row[csf("within_group")];
				$salesData[$poid]['lc_company_id'] = $row[csf("lc_company_id")];
				$salesData[$poid]['fso_no'] = $row[csf("job_no")];
	
				// within group yes
				if($row[csf("within_group")]==1)
				{
					$salesData[$poid]['buyer_id'] = $row[csf("po_buyer")];
					$salesData[$poid]['job_no'] = $row[csf("po_job_no")];
				} else {
					$salesData[$poid]['buyer_id'] = $row[csf("buyer_id")];
					$salesData[$poid]['job_no'] = "";
				}
	
				if($row[csf('booking_type')] == 4)
				{
					if($row[csf('booking_without_order')] == 1)
					{
						$bookingType = "Sample Without Order";
					}
					else
					{
						$bookingType =  "Sample With Order";
					}
				}
				else
				{
					$bookingType =  $booking_type_arr[$row[csf('booking_entry_form')]];
				}
				$salesData[$poid]['booking_type'] = $bookingType;
	
				$date_frm=date('Y-m-d',strtotime($start_date));
				$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
				if($row[csf("color_range_id")]!=""){
					if($transaction_date >= $date_frm){
						$prodWiseSalesDataStatus[$poid][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*1**".$row[csf("color_id")]."_";
					}else{
						if($transaction_date < $date_frm){
							$prodWiseSalesDataStatus[$poid][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] .= $row[csf('febric_description_id')]."*".$row[csf('gsm')]."*".$row[csf('width')]."*".$row[csf("receive_qty")]."*".$row[csf("store_id")]."*1*2**".$row[csf("color_id")]."_";
							$receiveOpening[$poid][$row[csf("prod_id")]][$row[csf("color_range_id")]][$row[csf("stitch_length")]] += $row[csf("receive_qty")];
						}
					}
				}
			}
		}
	
	
	
		$trans_in_sql = "SELECT a.from_order_id,d.po_breakdown_id as po_id,e.transaction_date,e.store_id,b.from_prod_id,c.dia_width,c.gsm,c.detarmination_id,d.qnty as transfer_in_qnty,d.barcode_no,f.company_id,f.buyer_id,f.style_ref_no, f.job_no, f.job_no_prefix_num, f.sales_booking_no, f.booking_id,f.within_group,f.po_company_id as lc_company_id,f.po_buyer,f.po_job_no,f.booking_without_order,f.booking_type,f.booking_entry_form
		from inv_item_transfer_mst a,inv_transaction e,inv_item_transfer_dtls b,product_details_master c,pro_roll_details d,fabric_sales_order_mst f
		where a.entry_form=133 and a.status_active=1 and a.transfer_criteria=4 and a.id=e.mst_id and e.id=b.to_trans_id and b.from_prod_id=c.id and b.id=d.dtls_id and d.po_breakdown_id=f.id and b.status_active=1 $toOrderIdCond $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2 and d.entry_form=133 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sales_to_order_no_cond";
	
		$trans_in_data = sql_select($trans_in_sql);
		foreach($trans_in_data as $row)
		{
			$barcode_no = $row[csf('barcode_no')];
			$poid = $row[csf("po_id")];
	
			$all_po_arr[$poid] = $poid;
	
			$salesData[$poid]['booking_id'] = $row[csf("booking_id")];
			$salesData[$poid]['working_company_id'] = $row[csf("company_id")];
			$salesData[$poid]['booking_no'] = $row[csf("sales_booking_no")];
			$salesData[$poid]['style_ref_no'] = $row[csf("style_ref_no")];
			$salesData[$poid]['within_group'] = $row[csf("within_group")];
			$salesData[$poid]['lc_company_id'] = $row[csf("lc_company_id")];
			$salesData[$poid]['fso_no'] = $row[csf("job_no")];
	
			// within group yes
			if($row[csf("within_group")]==1)
			{
				$salesData[$poid]['buyer_id'] = $row[csf("po_buyer")];
				$salesData[$poid]['job_no'] = $row[csf("po_job_no")];
			} else {
				$salesData[$poid]['buyer_id'] = $row[csf("buyer_id")];
				$salesData[$poid]['job_no'] = "";
			}
	
			if($row[csf('booking_type')] == 4)
			{
				if($row[csf('booking_without_order')] == 1)
				{
					$bookingType = "Sample Without Order";
				}
				else
				{
					$bookingType = "Sample With Order";
				}
			}
			else
			{
				$bookingType = $booking_type_arr[$row[csf('booking_entry_form')]];
			}
	
			$salesData[$poid]['booking_type'] = $bookingType;
			$receive_barcodes[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		}

		$empty_arr = array();
		fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 868, 1, $all_po_arr, $empty_arr);
	
		
		fnc_tempengine("TMP_BARCODE_NO", $user_id, 868, $receive_barcodes);

		
		$composition_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
		$data_array=sql_select($sql_deter);
	
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$constructionArr[$row[csf('id')]]=$constructionArr[$row[csf('id')]];
					list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
					$copmpositionArr[$row[csf('id')]]=$cps;
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					$constructionArr[$row[csf('id')]]=$row[csf('construction')];
					list($cst,$cps)=explode(',',$composition_arr[$row[csf('id')]]);
					$copmpositionArr[$row[csf('id')]]=$cps;
				}
			}
		}
	
		if($within_group==1)
		{
			$booking_year_condition="";
			if ($db_type == 0)
			{
	
				if($cbo_year>0)
				{
					$booking_year_condition=" and YEAR(a.insert_date)=$cbo_year";
				}
			}
			else if ($db_type == 2)
			{
				if($cbo_year>0)
				{
					$booking_year_condition=" and to_char(a.insert_date,'YYYY')=$cbo_year";
				}
			}
		}
	
		$salesSql ="SELECT a.id,sum(b.grey_qty) as fso_qty, sum(b.finish_qty) as booking_qty,a.po_job_no
		from fabric_sales_order_dtls b,fabric_sales_order_mst a, GBL_TEMP_ENGINE c
		where b.mst_id=a.id and a.id=c.poid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		group by a.id,a.company_id,a.buyer_id, a.style_ref_no, a.job_no, a.job_no_prefix_num, a.sales_booking_no, a.booking_id,a.within_group,a.po_job_no";
	
		$sales_result = sql_select($salesSql);
	
		foreach ($sales_result as $row) {
			$salesData[$row[csf('id')]]['fso_qty'] = $row[csf('fso_qty')];
			$salesData[$row[csf('id')]]['booking_qty'] = $row[csf('booking_qty')];
			$po_jobs = explode(",",$row[csf('po_job_no')]);
			foreach ($po_jobs as $po_job) {
				if($po_job!=""){
					$po_job_arr[$row[csf('po_job_no')]] = "'".$po_job."'";
				}
			}
		}
		
		$production_sql = "SELECT a.color_range_id,b.barcode_no,a.yarn_lot,a.yarn_count,b.po_breakdown_id,a.prod_id,a.color_id,a.stitch_length from pro_grey_prod_entry_dtls a,pro_roll_details b,TMP_BARCODE_NO c
		where a.trans_id=0 and a.status_active=1 and a.id=b.dtls_id and b.barcode_no=c.barcode_no and b.entry_form in(2)";
		$production_data = sql_select($production_sql);
		foreach ($production_data as $production_row) {
			$barcode_color_range[$production_row[csf("barcode_no")]] = $production_row[csf("color_range_id")];
			$barcode_color_ids[$production_row[csf("barcode_no")]] = $production_row[csf("color_id")];
			$stitch_length_arr[$production_row[csf("barcode_no")]] = $production_row[csf("stitch_length")];
	
			$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["yarn_lot"][$production_row[csf("yarn_lot")]] = $production_row[csf("yarn_lot")];
			$yarn_info[$production_row[csf("po_breakdown_id")]][$production_row[csf("prod_id")]][$production_row[csf("color_range_id")]][$production_row[csf("stitch_length")]]["yarn_count"][$production_row[csf("yarn_count")]] = $production_row[csf("yarn_count")];
		}
		
		$trans_out_sql = "select c.po_breakdown_id as from_order_id,e.transaction_date,b.from_prod_id,d.qnty as transfer_out_qnty,d.barcode_no 
		from GBL_TEMP_ENGINE g,fabric_sales_order_mst f, order_wise_pro_details c,pro_roll_details d,inv_item_transfer_dtls b,inv_transaction e,inv_item_transfer_mst a 
		where g.ref_val=f.id and f.id=c.po_breakdown_id and c.dtls_id=d.dtls_id and d.dtls_id=b.id and b.trans_id=e.id and e.mst_id=a.id  and c.trans_id=e.id	and a.status_active=1 and a.entry_form=133 and b.status_active=1 and e.status_active=1 and e.is_deleted=0 and e.transaction_type=6 and d.entry_form=133 
		and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=133  $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2";
		
		$trans_out_data = sql_select($trans_out_sql);
		
		foreach($trans_out_data as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
	
			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$transOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_out_qnty")];
				
			}else{
				if($transaction_date < $date_frm){
					$openingTransOutQnty[$row[csf('from_order_id')]][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_out_qnty")];
				}
			}
		}
	
		$issue_sql = "SELECT d.po_breakdown_id,e.transaction_date,d.booking_no as prog_no,b.prod_id,d.barcode_no,d.qnty as issue_qty from inv_issue_master a,inv_transaction e,inv_grey_fabric_issue_dtls b,pro_roll_details d,fabric_sales_order_mst f,GBL_TEMP_ENGINE g
		where a.status_active=1 and a.is_deleted=0 and a.id=e.mst_id and e.id=b.trans_id and b.id=d.dtls_id and a.item_category=13 and a.entry_form=61 and d.entry_form=61 and e.transaction_type=2	and e.status_active=1 and e.is_deleted=0 and d.po_breakdown_id=f.id and f.id=g.ref_val and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.is_returned<>1 $date_cond $store_cond $pocompany_cond2 $booking_no_cond2 $buyer_id_cond2 $sales_order_year_condition2 $within_group_cond2";
	
		$sql_iss=sql_select($issue_sql);
	
		$knit_issue_arr=array();
		foreach($sql_iss as $row)
		{
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
	
			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			if($transaction_date >= $date_frm){
				$knit_issue_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_qty'] += $row[csf('issue_qty')];
			}else{
				if($transaction_date < $date_frm){
					$opening_issue[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$color_ranges][$stitch_lengths]['issue_qty'] += $row[csf('issue_qty')];
				}
			}
		}
		unset($sql_iss);
		 
	
	
		foreach($trans_in_data as $row)
		{
			$prod_id .= $row[csf("from_prod_id")].",";
			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($row[csf('transaction_date')]));
			$color_ranges = $barcode_color_range[$row[csf("barcode_no")]];
			$stitch_lengths = $stitch_length_arr[$row[csf("barcode_no")]];
			$color_ids = $barcode_color_ids[$row[csf("barcode_no")]];
			$poid = $row[csf("po_id")];
			if($transaction_date >= $date_frm){
				$prodWiseSalesDataStatus[$poid][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*1*".$row[csf("from_order_id")]."*".$color_ids."_";
			}else{
				if($transaction_date < $date_frm){
					$prodWiseSalesDataStatus[$poid][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] .= $row[csf('detarmination_id')]."*".$row[csf('gsm')]."*".$row[csf('dia_width')]."*".$row[csf("transfer_in_qnty")]."*".$row[csf("store_id")]."*3*2*".$row[csf("from_order_id")]."*".$color_ids."_";
					$transferInOpening[$poid][$row[csf('from_prod_id')]][$color_ranges][$stitch_lengths] += $row[csf("transfer_in_qnty")];
				}
			}
			$all_po_arr[$poid] = $poid;
		}
		
		unset($trans_out_data);
		unset($trans_in_data);
	
		$transaction_date_array=array();
		$sql_date="SELECT c.po_breakdown_id,a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date
		from inv_transaction a,order_wise_pro_details c,GBL_TEMP_ENGINE e
		where a.id=c.trans_id and c.PO_BREAKDOWN_ID=e.ref_val and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=13 $store_cond2 group by c.po_breakdown_id,a.prod_id";
		//$all_prod_id_cond
	
		$sql_date_result=sql_select($sql_date);
		foreach( $sql_date_result as $row )
		{
			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
			$transaction_date_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
		}
		unset($sql_date_result);
	
		execute_query("DELETE FROM TMP_BARCODE_NO WHERE USERID = ".$user_id." and ENTRY_FORM=868");
		execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id." and entry_form=868");
		oci_commit($con);
	 
		$table_width = ($cbo_store_wise==1)?"3200":"3110";
		
		 
					$i=1;
					$tot_recv_qty=0;
					
					foreach($prodWiseSalesDataStatus as $poId=>$prodArr)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	
						foreach ($prodArr as $prodId=>$colorRange)
						{
							foreach ($colorRange as $crange=>$stitchLength)
							{
	
								$opening=$iss_qty=$trans_out_qty=0;
								foreach ($stitchLength as $slength=>$row)
								{
	
									$yarn_lot = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["yarn_lot"]));
									$yarn_count = implode(",",array_unique($yarn_info[$poId][$prodId][$crange][$slength]["yarn_count"]));
	
									$all_prodData = explode("_",chop($row,"_"));
									$recv_qnty=$trans_in_qty=$opening_recv=$opening_trans=0;
									$color_ids="";$color_names="";
									foreach ($all_prodData as $prodData) {
										$data = explode("*",$prodData);
										if($data[5] == 1){
											if($data[6] == 1){
												$recv_qnty += $data[3]*1;
											}
										}
	
										if($data[5] == 3){
											if($data[6] == 1){
												$trans_in_qty += $data[3]*1;
											}
	
											$from_order_id = $data[7];
	
											$yarn_lot = implode(",",array_unique($yarn_info[$from_order_id][$prodId][$crange][$slength]["yarn_lot"]));
											$yarn_count = implode(",",array_unique($yarn_info[$from_order_id][$prodId][$crange][$slength]["yarn_count"]));
										}
										$detarmination_id = $data[0];
										$store_id = $data[4];
										$color_ids .= $color_arr[$data[8]]."**";
									}
									$yarn_lot = implode(",",array_filter(array_unique(explode(",", $yarn_lot))));
	
									$color_ids_arr = array_filter(array_unique(explode("**",rtrim($color_ids,","))));
									foreach ($color_ids_arr as $color) {
										$color_names .= trim($color).", ";
									}
	
									$issue_return_qnty  = $inssue_return_array[$poId][$prodId][$crange][$slength]['issue_return_qty'];
									$iss_qty 			= $knit_issue_arr[$poId][$prodId][$crange][$slength]['issue_qty'];
	
									$opening_receive  = $receiveOpening[$poId][$prodId][$crange][$slength];
									$opening_trans_in = $transferInOpening[$poId][$prodId][$crange][$slength];
	
									$opening_title = "Receive=".number_format($opening_receive,2) ."+". number_format($opening_trans_in,2)."\nIssue=".number_format($opening_issue[$poId][$prodId][$crange][$slength]['issue_qty'],2) ."+". number_format($openingTransOutQnty[$poId][$prodId][$crange][$slength],2);
	
									$opening = ($opening_receive+$opening_trans_in)-($opening_issue[$poId][$prodId][$crange][$slength]['issue_qty']+$openingTransOutQnty[$poId][$prodId][$crange][$slength]);
	
									// roll wise $recv_ret_qty page did not developed yet
									//echo $recv_qnty."+".$issue_return_qnty."+".$trans_in_qty;
									$recv_tot_qty  = ($recv_qnty+$issue_return_qnty+$trans_in_qty);
									$trans_out_qty = $transOutQnty[$poId][$prodId][$crange][$slength];
									$iss_tot_qty   = ($iss_qty+$trans_out_qty);
									//echo $opening."+".$recv_tot_qty."-".$iss_tot_qty;
									$stock_qty 	   = $opening+($recv_tot_qty-$iss_tot_qty);
									//$stock_qty     = number_format($stock_qty,2,".","");
									//echo $stock_qty.",";
									if($stock_qty < .001)
									{
										$stock_qty = 0;
									}
	
									$daysOnHand = datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['max_date'],'','',1),date("Y-m-d"));
									$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$poId][$prodId]['min_date'],'','',1),date("Y-m-d"));
	
									$product_category 	= $job_info[$salesData[$poId]['job_no']]["product_category"];
									$product_dept 		= $job_info[$salesData[$poId]['job_no']]["product_dept"];
									$season 			= $season_arr[$job_info[$salesData[$poId]['job_no']]["season"]];
									$style_ref_no 		= $job_info[$salesData[$poId]['job_no']]["style_ref_no"];
	
	
									if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qty == $txt_qnty) || $get_upto_qnty == 0))
									{
	
										if($stock_qty > 0 && $cbo_value_with==2)
										{
											$tot_opening  		+= $opening;
											$tot_recv_qty 		+= $recv_qnty;
											$tot_iss_ret_qty 	+= $issue_return_qnty;
											$tot_trans_in_qty 	+= $trans_in_qty;
											$grand_tot_recv_qty += $recv_tot_qty;
	
											$tot_iss_qty 		+= $iss_qty;
											$tot_rec_ret_qty 	+= $recv_ret_qty;
											$tot_trans_out_qty 	+= $trans_out_qty;
											$grand_tot_iss_qty 	+= ($iss_qty+$trans_out_qty);
											$grand_stock_qty 	+= $stock_qty;
	
											?>
											 
											<?
											$i++;
										}
										else if($stock_qty>=0 && $cbo_value_with==1)
										//else if( $cbo_value_with==1)
										{
											$tot_opening  		+= $opening;
											$tot_recv_qty 		+= $recv_qnty;
											$tot_iss_ret_qty 	+= $issue_return_qnty;
											$tot_trans_in_qty 	+= $trans_in_qty;
											$grand_tot_recv_qty += $recv_tot_qty;
	
											$tot_iss_qty 		+= $iss_qty;
											$tot_rec_ret_qty 	+= $recv_ret_qty;
											$tot_trans_out_qty 	+= $trans_out_qty;
											$grand_tot_iss_qty 	+= ($iss_qty+$trans_out_qty);
											$grand_stock_qty 	+= $stock_qty;
											?>
											 
											<?
											$i++;
										}
										$temp_tr[$poId] = $poId;
									}
								}
							}
						}
					}
					?>
			 
			<? 
			
	 
		
			$filename="reference_wise_finish_stock_data/tmp/grand_stock_qty.txt";
			$create_new_doc = fopen($filename, 'w');
			$is_created = fwrite($create_new_doc,$grand_stock_qty);
			fclose($filename);

	?>
 
	

 
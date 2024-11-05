<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
$user_id = $_SESSION['logic_erp']['user_id'];

require_once('../../../includes/common.php');
function pre($array){
	echo "<pre>";
	print_r($array);
	echo "</pre>";
}
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
   
//--------------------------------------------------------------------------------------------------------------------
if ($action=="load_drop_down_buyer")
{  
	echo create_drop_down( "cbo_buyer_name", 150, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "" );     	 
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id in('$data') 
	order by location_name","id,location_name", 1, "-- Select Location --", $selected,"",0 );    	 
}

 
 
if($action=="report_generate")
{
	$process = array(&$_POST);
	// pre($process);die;
	extract(check_magic_quote_gpc( $process ));

	$company_id=str_replace("'","",$cbo_company_name);
	$location_id=str_replace("'","",$cbo_location);
	$buyer_id=str_replace("'","",$cbo_buyer_name);
	$common_id=str_replace("'","",$txt_search_common);
	$search_by=str_replace("'","",$cbo_search_by); 
	$form_date=str_replace("'","",$txt_date_from); 
	$to_date=str_replace("'","",$txt_date_to);  

	// ============================================================================================================
	//												Library 
	// ============================================================================================================ 
 	$buyer_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  ); 
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  ); 

  
	// ============================================================================================================
	//												Conditions 
	// ============================================================================================================
	$cut_sql_cond  = ""; 
	$prod_sql_cond  = ""; 
	$cut_sql_cond .= ($company_id==0) ? "" : " and a.working_company_id=$company_id";
	$cut_sql_cond .= ($location_id==0) ? "" : " and a.location_id=$location_id";
	$cut_sql_cond .= ($buyer_id==0) ? "" : " and d.buyer_name=$buyer_id";
	$cut_sql_cond .=  ($form_date && $to_date) ?" and a.entry_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : ""; 
	
	$search_string = "'%" . trim($common_id) . "%'";
	if ($search_by && $common_id) 
	{  
		if( $search_by == 1)
		{
			$cut_sql_cond .= "and d.style_ref_no like  $search_string";
		}else if ( $search_by == 2)
		{
			$cut_sql_cond .= "and d.job_no_prefix_num = $txt_search_common";
		}else if ($search_by == 3)
		{
			$cut_sql_cond .= "and e.po_number =$txt_search_common";
		}
	} 

	// $prod_sql_cond .= ($company_id==0) ? "" : " and b.serving_company=$cbo_company_name";
	$prod_sql_cond .=  ($form_date && $to_date) ?" and b.production_date between '".change_date_format($form_date,'dd-mm-yyyy','-',1)."' and '".change_date_format($to_date,'dd-mm-yyyy','-',1)."'"  : "";

	// ============================================================================================================
	//												Cutting Data
	// ============================================================================================================	
	$cutting_sql = "SELECT d.id as job_id, d.buyer_name,d.style_ref_no as style,e.po_number,e.id as po_id,b.color_id,c.size_qty as cut_qty from ppl_cut_lay_mst a,ppl_cut_lay_dtls b,ppl_cut_lay_bundle c,wo_po_details_master d,wo_po_break_down e where a.id=b.mst_id and a.job_no=d.job_no and b.id=c.dtls_id and c.order_id=e.id and d.id=e.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $cut_sql_cond order by d.id,e.id";
	// echo $cutting_sql; die;
	$cutting_sql_res = sql_select($cutting_sql);  
	if (count($cutting_sql_res) == 0 ) {
		echo "<h1 style='color:red; font-size: 17px;text-align:center;margin-top:20px;'> ** Cutting Data Not Found ** </h1>" ;
		die();
	}
	// pre($cutting_sql_res);die; 
	$po_id_arr = array();
	$cut_array = array();
	foreach ($cutting_sql_res as  $v) 
	{
		$po_id_arr[$v['PO_ID']] = $v['PO_ID'];  
		/* $cut_array[$v['PO_ID']][$v['COLOR_ID']]['STYLE'] = $v['STYLE'];
		$cut_array[$v['PO_ID']][$v['COLOR_ID']]['BUYER'] = $v['BUYER_NAME']; */
		$cut_array[$v['BUYER_NAME']][$v['JOB_ID']][$v['COLOR_ID']][$v['PO_ID']]['STYLE'] 		= $v['STYLE'];   	
		$cut_array[$v['BUYER_NAME']][$v['JOB_ID']][$v['COLOR_ID']][$v['PO_ID']]['PO_NUMBER'] 	= $v['PO_NUMBER'];   	
		$cut_array[$v['BUYER_NAME']][$v['JOB_ID']][$v['COLOR_ID']][$v['PO_ID']]['CUT_QTY'] 		+=$v['CUT_QTY'];   
	}
	// pre($cut_array);die;
 
	// ============================================================================================================
	//												Delete Order Id From TEMP ENGINE
	// ============================================================================================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 56 and ref_from =1");
	oci_commit($con);  

	// ============================================================================================================
	//												Insert order_id into TEMP ENGINE
	// ============================================================================================================
	fnc_tempengine("gbl_temp_engine", $user_id, 56, 1,$po_id_arr, $empty_arr);  
	oci_commit($con);  

    // ============================================================================================================
	//												Order Qty
	// ============================================================================================================
	
	$po_sql= "SELECT a.po_break_down_id as po_id,a.color_number_id as color,a.order_quantity as po_qty FROM  wo_po_color_size_breakdown a,gbl_temp_engine tmp  WHERE a.po_break_down_id=tmp.ref_val and a.status_active=1 and a.is_deleted=0 and tmp.entry_form=56 and tmp.ref_from=1 and tmp.user_id=$user_id ";  
	// echo $po_sql;
	$po_sql_res = sql_select($po_sql);   
	$po_array = array();
	foreach ($po_sql_res as $v) 
	{  	
        $po_array[$v['PO_ID']][$v['COLOR']]['PO_QTY'] += $v['PO_QTY'];   
	}
	// pre($po_array);
	// ============================================================================================================
	//												Production Data
	// ============================================================================================================
	
	$prod_sql= "SELECT a.po_break_down_id as po_id,a.color_number_id as color, b.production_type as prod_type,b.embel_name,c.production_qnty as prod_qty FROM  wo_po_color_size_breakdown a,pro_garments_production_mst b,pro_garments_production_dtls c,gbl_temp_engine tmp  WHERE a.po_break_down_id=tmp.ref_val and a.po_break_down_id=b.po_break_down_id and b.id=c.mst_id and a.id=c.color_size_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.production_type in (2,3,4) $prod_sql_cond and tmp.entry_form=56 and tmp.ref_from=1 and tmp.user_id=$user_id ";  
	// echo $prod_sql; die;
	$prod_sql_res = sql_select($prod_sql); 
	$prod_array = array();

	foreach ($prod_sql_res as $v) 
	{  
		$prod_array[$v['PO_ID']][$v['COLOR']][$v['PROD_TYPE']][$v['EMBEL_NAME']] += $v['PROD_QTY'];   
	}  
	 

	// ============================================================================================================
	//												Booking
	// ============================================================================================================	
	$booking_sql = "SELECT b.po_break_down_id as po_id,b.grey_fab_qnty as booking_qty,b.booking_type,c.uom,b.gmts_color_id as color from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c, gbl_temp_engine tmp where a.id=b.booking_mst_id and c.id=b.pre_cost_fabric_cost_dtls_id and b.po_break_down_id=tmp.ref_val and a.entry_form in(118,88,108) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_type in(1,6) and tmp.entry_form=56 and tmp.ref_from=1 and tmp.user_id=$user_id";
	// echo '10**'.$booking_sql; die('***');
	$booking_res = sql_select($booking_sql);  
	$booking_arr = array();
	$color_wise_uom_arr = array();
	foreach ($booking_res as $v) 
	{    
		$booking_arr[$v['PO_ID']][$v['COLOR']][$v['UOM']]['BOOKING_QTY'] += $v['BOOKING_QTY']; 
		$color_wise_uom_arr[$v['PO_ID']][$v['COLOR']] = $v['UOM'];  	
	} 
	// pre($booking_arr);die;

	// ============================================================================================================
	//												Consumtion
	// ============================================================================================================
	$cons_sql = "SELECT a.po_break_down_id as po_id,a.requirment as cons,a.color_number_id as color,b.uom,b.body_part_id from wo_pre_cos_fab_co_avg_con_dtls a,wo_pre_cost_fabric_cost_dtls b,gbl_temp_engine tmp where a.po_break_down_id=tmp.ref_val and b.id=a.pre_cost_fabric_cost_dtls_id and  a.requirment > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and tmp.entry_form=56 and tmp.ref_from=1 and tmp.user_id=$user_id";
	// echo $cons_sql;die;
	$cons_sql_res = sql_select($cons_sql);  
	// pre($cons_sql_res);die;
	
	$body_part_cons_arr = array(); 
	foreach ($cons_sql_res as  $v) 
	{ 
		$body_part_cons_arr[$v['PO_ID']][$v['COLOR']][$v['UOM']][$v['BODY_PART_ID']]['CONS'] += $v['CONS'];
		$body_part_cons_arr[$v['PO_ID']][$v['COLOR']][$v['UOM']][$v['BODY_PART_ID']] ['ROW'] ++;
	} 

	// Consumption Calculation
	$cons_arr = array(); 
	foreach ($body_part_cons_arr as $po_id => $po_arr) 
	{
		foreach ($po_arr as $color_id => $color_arr) 
		{
			foreach ($color_arr as $uom => $uom_arr) 
			{
				foreach ($uom_arr as $body_part_id => $v) 
				{
					$avg_cons = $v['CONS'] / $v['ROW'];
					$cons_arr[$po_id][$color_id][$uom]['TOTAL_CONS'] += $avg_cons ;
				}
			}
		}
	}
	// pre($cons_arr); die;
	// ============================================================================================================
	//												Delete Order Id From TEMP ENGINE
	// ============================================================================================================
	$con = connect();
	execute_query("delete from gbl_temp_engine where user_id=$user_id and entry_form = 56 and ref_from =1");
	oci_commit($con);  
	disconnect($con);
	ob_start();
	$width = 1440;
	?>
	<style type="text/css">      
	 /* 	table tr td{word-break: break-all;word-wrap: break-word;}
		table tr th{
			padding: 3px 1px; 
		}  */
		.rotate-header { 
				width:30px;
				height:auto;
				color: #444;
				font-size: 13px !important;
				font-weight: bold !important;
				text-align: center !important;
				line-height: 12px !important;
				text-wrap:normal; 
				vertical-align:middle;
				display: inline-block; 
				-webkit-transform: rotate(-90deg);
				-moz-transform: rotate(-90deg);
				padding:5px !important; 
				margin:0px !important;
				writing-mode:rl-tb;
				z-index:-999;
				box-sizing: border-box;
		}
		 
		.break_all
		{
			word-wrap: break-word;
			word-break: break-all;padding:0px !important; margin:0px !important;
		} 
		td{
			word-break: break-word;
		}
    </style> 
	<div align="center">
		<fieldset style="width:<? echo $width+20;?>px;" > 
			<table width="100%" cellspacing="0"> 
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="tbl_caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="22" align="center" style="font-size:14px; font-weight:bold" >Cutting Summary Report</td>
					</tr>  
				</thead>
			</table>	
			<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">  
				<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
					<thead class="form_caption" >	
						<? $content.=ob_get_flush(); ?>		 
						<tr height="150">	 	 	 	 	 	
							<th width="40">Sl No.</th>
							<th width="70">Buyer</th>
							<th width="80">Style</th>
							<th width="80">PO</th>
							<th width="120">Color</th>
							<th width="60">Order Qty</th>
							<th width="60">Total Cutting</th>
							<th style="vertical-align:bottom" width="40"> <div class="rotate-header">Print Sent </div></th>
							<th style="vertical-align:bottom" width="40"> <div class="rotate-header">Print Receive </div></th>
							<th style="vertical-align:bottom" width="40"> <div class="rotate-header">Embroidery Sent </div></th> 
							<th style="vertical-align:bottom" width="40"> <div class="rotate-header">Embroidery Receive </div></th> 
							<th style="vertical-align:bottom" width="40"> <div class="rotate-header">Special Work Sent </div></th>
							<th style="vertical-align:bottom" width="40"> <div class="rotate-header">Special Work Receive </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Total Input </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric Consumption(Kg) </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric Consumption(Mtr) </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric Consumption(Yds) </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric Booking(Kg) </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric Booking(Mtr) </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric Booking(Yds) </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric used(Kg) </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric used(Mtr) </div></th>
							<th style="vertical-align:bottom" width="65"> <div class="rotate-header">Fabric used(Yds) </div></th>
							<th width="60" >Remarks</th> 
						</tr>	
					</thead>
				</table>
				<div style="width:<?= $width+20;?>px; max-height:400px; overflow-y:scroll;" id="scroll_body">
					<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body" width="<?= $width; ?>" rules="all" align="left">
						<tbody>
							<?
							$i = $gt_ttl_cut_qty = $gt_ttl_po_qty = $gt_ttl_print_sent = $gt_ttl_print_rcve = $gt_ttl_embro_sent = $gt_ttl_embro_rcve = $gt_ttl_sp_work_sent = $gt_ttl_sp_work_rcve = $gt_ttl_input= $gt_ttl_cons_kg = $gt_ttl_cons_mtr = $gt_ttl_cons_yds = $gt_ttl_booking_kg = $gt_ttl_booking_mtr = $gt_ttl_booking_yds =$gt_ttl_used_kg = $gt_ttl_used_mtr = $gt_ttl_used_yds = 0; ; 
							foreach ($cut_array as $buyer_id => $buyer_arr) 
							{
								$buyer_ttl_cut_qty = $buyer_ttl_po_qty = $buyer_ttl_print_sent = $buyer_ttl_print_rcve = $buyer_ttl_embro_sent = $buyer_ttl_embro_rcve = $buyer_ttl_sp_work_sent = $buyer_ttl_sp_work_rcve = $buyer_ttl_input= $buyer_ttl_cons_kg = $buyer_ttl_cons_mtr = $buyer_ttl_cons_yds = $buyer_ttl_booking_kg = $buyer_ttl_booking_mtr = $buyer_ttl_booking_yds =$buyer_ttl_used_kg = $buyer_ttl_used_mtr = $buyer_ttl_used_yds = 0;
								foreach ($buyer_arr as $job_id => $job_arr) 
								{
									$style_ttl_cut_qty = $style_ttl_po_qty = $style_ttl_print_sent = $style_ttl_print_rcve = $style_ttl_embro_sent = $style_ttl_embro_rcve = $style_ttl_sp_work_sent = $style_ttl_sp_work_rcve = $style_ttl_input= $style_ttl_cons_kg = $style_ttl_cons_mtr = $style_ttl_cons_yds = $style_ttl_booking_kg = $style_ttl_booking_mtr = $style_ttl_booking_yds =$style_ttl_used_kg = $style_ttl_used_mtr = $style_ttl_used_yds = 0;
									foreach ($job_arr as $color => $color_arr) 
									{
										$clr_ttl_cut_qty = $clr_ttl_po_qty = $clr_ttl_print_sent = $clr_ttl_print_rcve = $clr_ttl_embro_sent = $clr_ttl_embro_rcve = $clr_ttl_sp_work_sent = $clr_ttl_sp_work_rcve = $clr_ttl_input= $clr_ttl_cons_kg = $clr_ttl_cons_mtr = $clr_ttl_cons_yds = $clr_ttl_booking_kg = $clr_ttl_booking_mtr = $clr_ttl_booking_yds =$clr_ttl_used_kg = $clr_ttl_used_mtr = $clr_ttl_used_yds = 0;
										foreach ($color_arr as $po_id => $v) 
										{ 
											$i++;  

											$uom = $color_wise_uom_arr[$po_id][$color];
											$prod_arr = $prod_array[$po_id][$color];
											$print_sent = $prod_arr[2][1] ?? 0;
											$print_rcve = $prod_arr[3][1] ?? 0;
											$embro_sent = $prod_arr[2][2] ?? 0;
											$embro_rcve = $prod_arr[3][2] ?? 0;
											$sp_work_sent = $prod_arr[2][4] ?? 0;
											$sp_work_rcve = $prod_arr[3][4] ?? 0;
											$input = $prod_arr[4][0] ?? 0;
											$po_qty = $po_array[$po_id][$color]['PO_QTY'] ?? 0;
											$total_cut =$v['CUT_QTY'] ?? 0;

											$consumption_kg = $cons_arr[$po_id][$color][12]['TOTAL_CONS'] ?? 0;
											$consumption_mtr = $cons_arr[$po_id][$color][23]['TOTAL_CONS'] ?? 0;
											$consumption_yds = $cons_arr[$po_id][$color][27]['TOTAL_CONS'] ?? 0;
											/* print_r($booking_arr); 
											echo "[$po_id][$color][12]['BOOKING_QTY']";die; */
											$fabric_booking_kg = $booking_arr[$po_id][$color][12]['BOOKING_QTY'] ?? 0 ; 
											$fabric_booking_mtr = $booking_arr[$po_id][$color][23]['BOOKING_QTY'] ?? 0 ;  
											$fabric_booking_yds = $booking_arr[$po_id][$color][27]['BOOKING_QTY'] ?? 0 ; 

											$fabric_used_kg = $total_cut*($consumption_kg/12) ?? 0 ;  
											$fabric_used_mtr = $total_cut*($consumption_mtr/12) ?? 0 ;  
											$fabric_used_yds = $total_cut*($consumption_yds/12) ?? 0 ; 

											// Color Total Calculation
											$clr_ttl_po_qty 		+= $po_qty;
											$clr_ttl_cut_qty 		+= $total_cut;
											$clr_ttl_print_sent 	+= $print_sent;
											$clr_ttl_print_rcve 	+= $print_rcve;
											$clr_ttl_embro_sent 	+= $embro_sent;
											$clr_ttl_embro_rcve 	+= $embro_rcve;
											$clr_ttl_sp_work_sent 	+= $sp_work_sent;
											$clr_ttl_sp_work_rcve 	+= $sp_work_rcve;
											$clr_ttl_input			+= $input;
											$clr_ttl_cons_kg 		+= $consumption_kg;
											$clr_ttl_cons_mtr 		+= $consumption_mtr;
											$clr_ttl_cons_yds 		+= $consumption_yds;
											$clr_ttl_booking_kg 	+= $fabric_booking_kg;
											$clr_ttl_booking_mtr 	+= $fabric_booking_mtr;
											$clr_ttl_booking_yds 	+= $fabric_booking_yds;
											$clr_ttl_used_kg 		+= $fabric_used_kg;
											$clr_ttl_used_mtr		+= $fabric_used_mtr;
											$clr_ttl_used_yds 		+= $fabric_used_yds;

											// Style Total Calculation
											$style_ttl_po_qty 		+= $po_qty;
											$style_ttl_cut_qty 		+= $total_cut;
											$style_ttl_print_sent 	+= $print_sent;
											$style_ttl_print_rcve 	+= $print_rcve;
											$style_ttl_embro_sent 	+= $embro_sent;
											$style_ttl_embro_rcve 	+= $embro_rcve;
											$style_ttl_sp_work_sent += $sp_work_sent;
											$style_ttl_sp_work_rcve += $sp_work_rcve;
											$style_ttl_input		+= $input;
											$style_ttl_cons_kg 		+= $consumption_kg;
											$style_ttl_cons_mtr 	+= $consumption_mtr;
											$style_ttl_cons_yds 	+= $consumption_yds;
											$style_ttl_booking_kg 	+= $fabric_booking_kg;
											$style_ttl_booking_mtr 	+= $fabric_booking_mtr;
											$style_ttl_booking_yds 	+= $fabric_booking_yds;
											$style_ttl_used_kg 		+= $fabric_used_kg;
											$style_ttl_used_mtr		+= $fabric_used_mtr;
											$style_ttl_used_yds 	+= $fabric_used_yds;
											// Buyer Total Calculation
											$buyer_ttl_po_qty 		+= $po_qty;
											$buyer_ttl_cut_qty 		+= $total_cut;
											$buyer_ttl_print_sent 	+= $print_sent;
											$buyer_ttl_print_rcve 	+= $print_rcve;
											$buyer_ttl_embro_sent 	+= $embro_sent;
											$buyer_ttl_embro_rcve 	+= $embro_rcve;
											$buyer_ttl_sp_work_sent += $sp_work_sent;
											$buyer_ttl_sp_work_rcve += $sp_work_rcve;
											$buyer_ttl_input		+= $input;
											$buyer_ttl_cons_kg 		+= $consumption_kg;
											$buyer_ttl_cons_mtr 	+= $consumption_mtr;
											$buyer_ttl_cons_yds 	+= $consumption_yds;
											$buyer_ttl_booking_kg 	+= $fabric_booking_kg;
											$buyer_ttl_booking_mtr 	+= $fabric_booking_mtr;
											$buyer_ttl_booking_yds 	+= $fabric_booking_yds;
											$buyer_ttl_used_kg 		+= $fabric_used_kg;
											$buyer_ttl_used_mtr		+= $fabric_used_mtr;
											$buyer_ttl_used_yds 	+= $fabric_used_yds;
											// Grand Total Calculation
											$gt_ttl_po_qty 		+= $po_qty;
											$gt_ttl_cut_qty 	+= $total_cut;
											$gt_ttl_print_sent 	+= $print_sent;
											$gt_ttl_print_rcve 	+= $print_rcve;
											$gt_ttl_embro_sent 	+= $embro_sent;
											$gt_ttl_embro_rcve 	+= $embro_rcve;
											$gt_ttl_sp_work_sent+= $sp_work_sent;
											$gt_ttl_sp_work_rcve+= $sp_work_rcve;
											$gt_ttl_input		+= $input;
											$gt_ttl_cons_kg 	+= $consumption_kg;
											$gt_ttl_cons_mtr 	+= $consumption_mtr;
											$gt_ttl_cons_yds 	+= $consumption_yds;
											$gt_ttl_booking_kg 	+= $fabric_booking_kg;
											$gt_ttl_booking_mtr += $fabric_booking_mtr;
											$gt_ttl_booking_yds += $fabric_booking_yds;
											$gt_ttl_used_kg 	+= $fabric_used_kg;
											$gt_ttl_used_mtr	+= $fabric_used_mtr;
											$gt_ttl_used_yds 	+= $fabric_used_yds;
											

											if ($i % 2 == 0)  $bgcolor = "#E9F3FF";  else $bgcolor = "#FFFFFF";
											?>
												<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
													<td width="40"> <?= $i; ?> </td>
													<td width="70"><?= $buyer_library[$buyer_id] ?></td>
													<td width="80"><p><?= $v['STYLE'] ?></p></td>
													<td width="80"><p><?= $v['PO_NUMBER'] ?></p></td>
													<td width="120"><?= $color_library[$color] ?></td>
													<td width="60" align="right"><?= $po_qty ?></td>
													<td width="60" align="right"><?= $total_cut ?></td>
													<td width="40" align="right"><?= $print_sent ?></td> 
													<td width="40" align="right"><?= $print_rcve ?></td> 
													<td width="40" align="right"><?= $embro_sent ?></td> 
													<td width="40" align="right"><?= $embro_rcve ?></td> 
													<td width="40" align="right"><?= $sp_work_sent ?></td> 
													<td width="40" align="right"><?= $sp_work_rcve ?></td> 
													<td width="65" align="right"><?= $input ?></td>
													<td width="65" align="right"><?= number_format($consumption_kg,2) ?></td>
													<td width="65" align="right"><?= number_format($consumption_mtr,2) ?></td>
													<td width="65" align="right"><?= number_format($consumption_yds,2) ?></td>
													<td width="65" align="right"><?= number_format($fabric_booking_kg,2) ?></td>
													<td width="65" align="right"><?= number_format($fabric_booking_mtr,2) ?></td>
													<td width="65" align="right"><?= number_format($fabric_booking_yds,2) ?></td>
													<td width="65" align="right" title="Total Cut*(Fabric Cons(Kg) /12)"><?= number_format($fabric_used_kg,2) ?></td>
													<td width="65" align="right" title="Total Cut*(Fabric Cons(Mtr) /12)"><?= number_format($fabric_used_mtr,2) ?></td>
													<td width="65" align="right" title="Total Cut*(Fabric Cons(Yds) /12)"><?= number_format($fabric_used_yds,2) ?></td>
													<td width="60"></td>
												</tr> 
											<?
										}
										?>
											<!-- Color Total -->
											<tr bgcolor="#C2DEDC">
												<th align="right" colspan="5">Color Total &nbsp;</th>
												<th align="right"><?= $clr_ttl_po_qty ?></th>
												<th align="right"><?= $clr_ttl_cut_qty ?></th> 
												<th align="right"><?= $clr_ttl_print_sent ?></th> 
												<th align="right"><?= $clr_ttl_print_rcve ?></th> 
												<th align="right"><?= $clr_ttl_embro_sent ?></th> 
												<th align="right"><?= $clr_ttl_embro_rcve ?></th> 
												<th align="right"><?= $clr_ttl_sp_work_sent ?></th> 
												<th align="right"><?= $clr_ttl_sp_work_rcve ?></th>
												<th align="right"><?= $clr_ttl_input ?></th>
												<th align="right"><?= number_format($clr_ttl_cons_kg,2) ?></th>
												<th align="right"><?= number_format($clr_ttl_cons_mtr,2) ?></th>
												<th align="right"><?= number_format($clr_ttl_cons_yds,2) ?></th>
												<th align="right"><?= number_format($clr_ttl_booking_kg,2) ?></th>
												<th align="right"><?= number_format($clr_ttl_booking_mtr,2) ?></th>
												<th align="right"><?= number_format($clr_ttl_booking_yds,2) ?></th>
												<th align="right" title="Total Cut*(Fabric Cons(Kg) /12)"><?= number_format($clr_ttl_used_kg,2) ?></th>
												<th align="right" title="Total Cut*(Fabric Cons(Mtr) /12)"><?= number_format($clr_ttl_used_mtr,2) ?></th>
												<th align="right" title="Total Cut*(Fabric Cons(Yds) /12)"><?= number_format($clr_ttl_used_yds,2) ?></th>
												<th></th>
											</tr> 
										<?
									}
									?>
										<!-- Style Total -->
										<tr bgcolor="#FCBAAD">
											<th align="right" colspan="5">Style Total &nbsp;</th>
											<th align="right"><?= $style_ttl_po_qty ?></th>
											<th align="right"><?= $style_ttl_cut_qty ?></th> 
											<th align="right"><?= $style_ttl_print_sent ?></th> 
											<th align="right"><?= $style_ttl_print_rcve ?></th> 
											<th align="right"><?= $style_ttl_embro_sent ?></th> 
											<th align="right"><?= $style_ttl_embro_rcve ?></th> 
											<th align="right"><?= $style_ttl_sp_work_sent ?></th> 
											<th align="right"><?= $style_ttl_sp_work_rcve ?></th>
											<th align="right"><?= $style_ttl_input ?></th>
											<th align="right"><?= number_format($style_ttl_cons_kg,2) ?></th>
											<th align="right"><?= number_format($style_ttl_cons_mtr,2) ?></th>
											<th align="right"><?= number_format($style_ttl_cons_yds,2) ?></th>
											<th align="right"><?= number_format($style_ttl_booking_kg,2) ?></th>
											<th align="right"><?= number_format($style_ttl_booking_mtr,2) ?></th>
											<th align="right"><?= number_format($style_ttl_booking_yds,2) ?></th>
											<th align="right" title="Total Cut*(Fabric Cons(Kg) /12)"><?= number_format($style_ttl_used_kg,2) ?></th>
											<th align="right" title="Total Cut*(Fabric Cons(Mtr) /12)"><?= number_format($style_ttl_used_mtr,2) ?></th>
											<th align="right" title="Total Cut*(Fabric Cons(Yds) /12)"><?= number_format($style_ttl_used_yds,2) ?></th>
											<th></th>
										</tr>  
									<?
								}
								?>
									<!-- Buyer Total -->
									<tr bgcolor="#F2E8C6">
										<th align="right" colspan="5">Buyer Total &nbsp;</th>
										<th align="right"><?= $buyer_ttl_po_qty ?></th>
										<th align="right"><?= $buyer_ttl_cut_qty ?></th> 
										<th align="right"><?= $buyer_ttl_print_sent ?></th> 
										<th align="right"><?= $buyer_ttl_print_rcve ?></th> 
										<th align="right"><?= $buyer_ttl_embro_sent ?></th> 
										<th align="right"><?= $buyer_ttl_embro_rcve ?></th> 
										<th align="right"><?= $buyer_ttl_sp_work_sent ?></th> 
										<th align="right"><?= $buyer_ttl_sp_work_rcve ?></th>
										<th align="right"><?= $buyer_ttl_input ?></th>
										<th align="right"><?= number_format($buyer_ttl_cons_kg,2) ?></th>
										<th align="right"><?= number_format($buyer_ttl_cons_mtr,2) ?></th>
										<th align="right"><?= number_format($buyer_ttl_cons_yds,2) ?></th>
										<th align="right"><?= number_format($buyer_ttl_booking_kg,2) ?></th>
										<th align="right"><?= number_format($buyer_ttl_booking_mtr,2) ?></th>
										<th align="right"><?= number_format($buyer_ttl_booking_yds,2) ?></th>
										<th align="right" title="Total Cut*(Fabric Cons(Kg) /12)"><?= number_format($buyer_ttl_used_kg,2) ?></th>
										<th align="right" title="Total Cut*(Fabric Cons(Mtr) /12)"><?= number_format($buyer_ttl_used_mtr,2) ?></th>
										<th align="right" title="Total Cut*(Fabric Cons(Yds) /12)"><?= number_format($buyer_ttl_used_yds,2) ?></th>
										<th></th>
									</tr>  
								<?		
							}
							?>
							<!-- Grand Total -->
							<tr bgcolor="#E9B384">
								<th align="right" colspan="5">Grand Total &nbsp;</th>
								<th align="right"><?= $gt_ttl_po_qty ?></th>
								<th align="right"><?= $gt_ttl_cut_qty ?></th> 
								<th align="right"><?= $gt_ttl_print_sent ?></th> 
								<th align="right"><?= $gt_ttl_print_rcve ?></th> 
								<th align="right"><?= $gt_ttl_embro_sent ?></th> 
								<th align="right"><?= $gt_ttl_embro_rcve ?></th> 
								<th align="right"><?= $gt_ttl_sp_work_sent ?></th> 
								<th align="right"><?= $gt_ttl_sp_work_rcve ?></th>
								<th align="right"><?= $gt_ttl_input ?></th>
								<th align="right"><?= number_format($gt_ttl_cons_kg,2) ?></th>
								<th align="right"><?= number_format($gt_ttl_cons_mtr,2) ?></th>
								<th align="right"><?= number_format($gt_ttl_cons_yds,2) ?></th>
								<th align="right"><?= number_format($gt_ttl_booking_kg,2) ?></th>
								<th align="right"><?= number_format($gt_ttl_booking_mtr,2) ?></th>
								<th align="right"><?= number_format($gt_ttl_booking_yds,2) ?></th>
								<th align="right" title="Total Cut*(Fabric Cons(Kg) /12)"><?= number_format($gt_ttl_used_kg,2) ?></th>
								<th align="right" title="Total Cut*(Fabric Cons(Mtr) /12)"><?= number_format($gt_ttl_used_mtr,2) ?></th>
								<th align="right" title="Total Cut*(Fabric Cons(Yds) /12)"><?= number_format($gt_ttl_used_yds,2) ?></th>
								<th></th>
							</tr>
						</tbody>
					</table> 
				</div> 
			</div>
		</fieldset>
	</div>
	
	<? 
	
	foreach (glob($user_id."_*.xls") as $filename)
	{		
		@unlink($filename);
	}
	$name=$user_id."_".time().".xls";
	$create_new_excel = fopen($name, 'w');	
	$is_created = fwrite($create_new_excel,ob_get_contents());
	//$new_link=create_delete_report_file( $html, 1, 1, "../../../" );
	echo "####".$name;
	exit();
}
?>

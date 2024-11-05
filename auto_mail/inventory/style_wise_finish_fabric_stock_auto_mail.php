<?
date_default_timezone_set("Asia/Dhaka");

//Note: File patha => inventory\reports\finish_fabric_store\requires\style_wise_finish_fabric_stock_controller.php

header('Content-type:text/html; charset=utf-8');
session_start();

	include('../../includes/common.php');
	extract($_REQUEST);
	// require('../../mailer/class.phpmailer.php');
	require('../setting/mail_setting.php');
	require('../../ext_resource/mpdf60/mpdf.php');
	
	$time_stamp=time();
	if($view_date){$time_stamp = strtotime($view_date);}
	$txt_date_from = date("d-M-Y",$time_stamp);
	$txt_date_from = date("'d-M-Y'", strtotime('-1 day', strtotime($txt_date_from)));

	 //1,2,3,4,8
	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$short_company_arr 	= return_library_array("select id, company_short_name from lib_company where status_active=1","id","company_short_name");


	$action="report_generate";

	//$cbo_company_id='3';
	//$txt_date_from="'01-Mar-2023'";
	$cbo_buyer_id='0';
	$cbo_year='0';
	$cbo_report_type=1;
	$cbo_search_by='1';
	$cbo_value_range_by='2';
	$txt_search_comm='';
	$cbo_store_name='0';
	$cbo_shipment_status='0';
	$report_title='Style Wise Finish Fabric Status';


//--------------------------------------------------------------------------------------------

if($action=="report_generate")
{


	$cbo_report_type 	= str_replace("'","",$cbo_report_type);
	$cbo_search_by 		= str_replace("'","",$cbo_search_by);
	$txt_search_comm 	= str_replace("'","",$txt_search_comm);
	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_value_range_by = str_replace("'","",$cbo_value_range_by);
	$cbo_store_name = str_replace("'","",$cbo_store_name);
	$cbo_shipment_status = str_replace("'","",$cbo_shipment_status);
	if($cbo_shipment_status>0){$shiping_status_cond=" and b.shiping_status in ($cbo_shipment_status)";}

	//var_dump($cbo_value_range_by);

	if(str_replace("'","",$cbo_buyer_id)!="" && str_replace("'","",$cbo_buyer_id)!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_id";
	$job_no=str_replace("'","",$txt_job_no);
	$search_cond='';
	if($cbo_search_by==1)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5)
	{
		if ($txt_search_comm=="") $search_cond.=""; else $search_cond.=" and b.grouping LIKE '$txt_search_comm'";
	}
	else
	{
		$search_cond.="";
	}

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date=""; else $receive_date= " and c.transaction_date <=".$txt_date_from."";


	if( $date_from=="") $today_receive_date=""; else $today_receive_date= " c.transaction_date=".$txt_date_from."";

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";

	$date_from=str_replace("'","",$txt_date_from);
	if( $date_from=="") $receive_date_trans=""; else $receive_date_trans= " and a.transfer_date <=".$txt_date_from."";
	if($db_type==0)
	{
		$prod_id_cond=" group_concat(b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and year(a.insert_date)='$cbo_year_val'"; else $year_cond="";
	}
	else if($db_type==2)
	{
		$prod_id_cond=" listagg(cast(b.from_prod_id as varchar2(4000)),',') within group (order by b.from_prod_id)";
		if($cbo_year_val!=0) $year_cond="and to_char(a.insert_date,'YYYY')='$cbo_year_val'";  else $year_cond="";
	}

	if($db_type==0)
	{
		$select_fld= "year(a.insert_date)as year";
	}
	else if($db_type==2)
	{
		$select_fld= "TO_CHAR(a.insert_date,'YYYY') as year";
	}
	//$company_arr[3]=$company_arr[3];
	foreach($company_arr as $cbo_company_id=>$company_name)
	{
		ob_start();

		if($cbo_report_type==1) // Knit Finish Start
		{
			// Knit Finish Start
			$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
			//$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=2", "id", "product_name_details");
			$product_arr=return_library_array("select a.id, b.construction from product_details_master a, lib_yarn_count_determina_mst b where a.item_category_id=2 and a.detarmination_id=b.id", "id", "construction");

			if($cbo_store_name>0){$storeCond="and c.store_id=$cbo_store_name";}else{$storeCond="";}

			$sql_rcv="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no,b.shiping_status, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id, c.store_id, c.body_part_id, b.grouping,
			(case when d.entry_form in (7,37,66,68) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,
			(case when d.entry_form in (52,126) and $today_receive_date then d.quantity else 0 end) as today_issue_rtn_qnty,
			(case when d.entry_form in (14,15,134,306) and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in_qnty,
			(case when d.entry_form in (7,37,66,68)  then d.quantity else 0 end) as receive_qnty,
			(case when d.entry_form in (52,126) then d.quantity else 0 end) as issue_rtn_qnty,
			(case when d.entry_form in (14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trans_in_qnty,

			(case when d.entry_form in (18,71) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty,
			(case when d.entry_form in (46) and $today_receive_date then d.quantity else 0 end) as today_rcv_rtn_qnty,
			(case when d.entry_form in(14,15,134,306) and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trns_out_qnty,
			(case when d.entry_form in (46) then d.quantity else 0 end) as issue_rcv_rtn_qnty,
			(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty

			from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d
			where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id  and a.company_name=$cbo_company_id and d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $shiping_status_cond $storeCond $receive_date $buyer_id_cond $search_cond $year_cond
			order by a.buyer_name,b.grouping,a.job_no, d.color_id,c.transaction_date";
			//echo $sql_rcv;
			$style_wise_arr=$color_id_arr=$prod_id_arr=$po_id_arr=array();

			$sql_rcv_res=sql_select($sql_rcv);
			foreach ($sql_rcv_res as $row)
			{
				$color_id_arr[$row[csf('color_id')]]=$row[csf('color_id')];
				$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];
				$prod_id_arr[$row[csf('prod_id')]]=$row[csf('prod_id')];
				$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
				$job_no_arr[$row[csf('job_no')]] = "'".$row[csf('job_no')]."'";

				$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
				//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
				//$fab_desc_tmp=$fab_desc_tmp1[0];

				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no']=$row[csf('job_no')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['job_no_pre']=$row[csf('job_no_prefix_num')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['year']=$row[csf('year')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['buyer_name']=$row[csf('buyer_name')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['style_ref_no']=$row[csf('style_ref_no')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['grouping'].=$row[csf('grouping')].",";
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_id'].=$row[csf('po_id')].',';
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['po_no'].=$row[csf('po_no')].',';
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['receive_qnty']+=$row[csf('receive_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rtn_qnty']+=$row[csf('issue_rtn_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trans_in_qnty']+=$row[csf('trans_in_qnty')];

				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_rcv_rtn_qnty']+=$row[csf('issue_rcv_rtn_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['trns_out_qnty']+=$row[csf('trns_out_qnty')];

				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_rtn_qnty'] += $row[csf('today_issue_rtn_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trans_in_qnty'] += $row[csf('today_trans_in_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_issue_qnty']+=$row[csf('today_issue_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_rcv_rtn_qnty']+=$row[csf('today_rcv_rtn_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['today_trns_out_qnty']+=$row[csf('today_trns_out_qnty')];
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['prod_ids'].=$row[csf('prod_id')].',';
				$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['body_part_id']=$row[csf('body_part_id')];

				if($row[csf('store_id')]*1!="" || $row[csf('store_id')]*1>0)
					$store_ids_arr[$row[csf('store_id')]] = $row[csf('store_id')];

				$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["receive_qnty"] += $row[csf('receive_qnty')];
				$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rtn_qnty"] += $row[csf('issue_rtn_qnty')];
				$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trans_in_qnty"] += $row[csf('trans_in_qnty')];
				$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_rcv_rtn_qnty"] += $row[csf('issue_rcv_rtn_qnty')];
			}
			/*echo "<pre>";
			print_r($style_wise_arr);
			die;*/
			$colorIds = implode(",", $color_id_arr);
			$prodIds = implode(",", $prod_id_arr);
			$poIds = implode(",", $po_id_arr);

			$prodIds 	= ($prodIds 	!= "") ? "and c.prod_id in ( $prodIds )" : "";
			$poIds 		= ($poIds 		!= "") ? "and c.po_breakdown_id in($poIds)" : "";

			// ======================================= FOR ISSUE QNTY ==============================================
			$sql_issue="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id, c.body_part_id,
			(case when d.entry_form in (18,71) then d.quantity else 0 end) as issue_qnty
			from wo_po_details_master a, wo_po_break_down b, inv_transaction c, order_wise_pro_details d,inv_finish_fabric_issue_dtls f
			where  a.company_name=$cbo_company_id and a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.id=d.trans_id   and f.id=d.dtls_id and   d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and f.status_active=1 and f.is_deleted=0 $shiping_status_cond $storeCond $receive_date $buyer_id_cond $search_cond $year_cond
			order by a.buyer_name,a.job_no, d.color_id,c.transaction_date";

			$style_wise_arr2=array();
			$sql_issue_res=sql_select($sql_issue);
			foreach ($sql_issue_res as $row)
			{
				//$fab_desc_tmp1=explode(",",$product_arr[$row[csf('prod_id')]]);
				//$fab_desc_tmp=$fab_desc_tmp1[0];

				$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
				$style_wise_arr2[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]['issue_qnty']+=$row[csf('issue_qnty')];
				$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["issue_qnty"] += $row[csf('issue_qnty')];
			}

			// =================================== ISSUE TRANS IN-OUT QNTY ===============================
			$sql_trans="SELECT a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no,b.po_number as po_no, d.po_breakdown_id as po_id, $select_fld, d.color_id, d.prod_id,c.store_id, c.body_part_id,
			(case when d.entry_form in(14,15,134,306) and d.trans_type=6 then d.quantity else 0 end) as trns_out_qnty,
			(case when d.entry_form in(14,15,134,306) and d.trans_type=5 then d.quantity else 0 end) as trns_in_qnty
			from wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d, inv_transaction c,  inv_item_transfer_dtls e 
			where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and c.id = d.trans_id and c.mst_id=e.mst_id and d.dtls_id=e.id and a.company_name=$cbo_company_id and d.entry_form in (14,15,134,306) and c.item_category=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $shiping_status_cond $storeCond $receive_date $buyer_id_cond $search_cond $year_cond
			order by a.buyer_name,a.job_no, d.color_id,c.transaction_date";
			//echo $sql_trans;
			$sql_trans_res = sql_select($sql_trans);
			$trns_out_qnty_arr = array();
			foreach ($sql_trans_res as $row)
			{
				$fab_desc_tmp=$product_arr[$row[csf('prod_id')]];
				//$fab_desc_tmp  = $fab_desc_tmp1[0];
				$trns_out_qnty_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]+=$row[csf('trns_out_qnty')];
				$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trns_out_qnty"] += $row[csf('trns_out_qnty')];
				$trns_in_qnty_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp]+=$row[csf('trns_in_qnty')];
				$store_wise_stock_arr[$row[csf('job_no')]][$row[csf('color_id')]][$fab_desc_tmp][$row[csf('store_id')]]["trns_in_qnty"] += $row[csf('trns_in_qnty')];
			}

			$booking_qnty=array();
			$job_no_arr = array_filter($job_no_arr);
			if(!empty($job_no_arr))
			{
				$job_no_arr = array_filter($job_no_arr);
				if($db_type==2 && count($job_no_arr)>999)
				{
					$job_no_arr_chunk=array_chunk($job_no_arr,999) ;
					foreach($job_no_arr_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$jobCond.="  b.job_no in($chunk_arr_value) or ";
					}

					$all_job_no_cond.=" and (".chop($jobCond,'or ').")";
				}
				else
				{
					$all_job_no_cond=" and b.job_no in(".implode(",",$job_no_arr).")";
				}

				$sql_booking=sql_select("SELECT b.po_break_down_id as po_id,b.fabric_color_id,b.job_no,b.construction,b.fabric_color_id, b.fin_fab_qnty,b.is_short,c.construction short_construction, c.body_part_id
				from wo_booking_mst a, wo_booking_dtls b
				left join wo_pre_cost_fabric_cost_dtls c on b.pre_cost_fabric_cost_dtls_id=c.id and c.status_active=1 and c.is_deleted=0
				where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 $all_job_no_cond ");
				//$all_po_id_cond

				foreach( $sql_booking as $row)
				{
					$construction = ($row[csf('is_short')]==1)?$row[csf('short_construction')]:$row[csf('construction')];
					$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$construction]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
					$job_order_no_arr[$row[csf('job_no')]][] = $row[csf('po_id')];
				}
				unset($sql_booking);
			}

			if($colorIds!=""){
				$colorIds_arr = array_filter($color_id_arr);
				if(!empty($colorIds_arr))
				{
					$colorIds_arr = array_filter($colorIds_arr);
					if($db_type==2 && count($colorIds_arr)>999)
					{
						$colorIds_arr_chunk=array_chunk($colorIds_arr,999) ;
						foreach($colorIds_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$colorCond.="  id in($chunk_arr_value) or ";
						}

						$all_color_no_cond.="  (".chop($colorCond,'or ').")";
					}
					else
					{
						$all_color_no_cond="  id in(".implode(",",$color_id_arr).")";
					}
				}
				$color_arr=return_library_array( "select id,color_name from lib_color where $all_color_no_cond", "id", "color_name"  );
			}


			$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
			$body_part_library=return_library_array( "select id, body_part_full_name from lib_body_part", "id", "body_part_full_name"  );
			$store_width    = 120;
			$table_width 	= 2260+count($store_ids_arr)*$store_width;

			//check job wise all po shipment status..............
			$sql_check_shipmentStatus=sql_select("select b.id,b.po_number,b.shiping_status from  pro_ex_factory_mst a, wo_po_break_down b where b.id=a.po_break_down_id and a.status_active=1 and a.is_deleted=0");
			foreach ($sql_check_shipmentStatus as $row) {
				$shipingStatus_arr[$row[csf('id')]]['shiping_status']=$row[csf('shiping_status')];
			}
			?>

				<table cellpadding="0" cellspacing="0" width="1960">
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="<? echo $table_width; ?>px" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<tr background="#CCC">
							<th width="30" rowspan="2">SL</th>
							<th width="100" rowspan="2">Buyer</th>
							<th width="60" rowspan="2">Job</th>
							<th width="50" rowspan="2">Year</th>
							<th width="110" rowspan="2">Style</th>
							<th width="100" rowspan="2">Internal Ref.</th>
							<th width="60" rowspan="2">Order Status</th>
							<th width="100" rowspan="2">Shipment Status</th>
							<th width="110" rowspan="2">Fin. Fab. Color</th>
							<th width="120" rowspan="2">Fabric Type</th>
							<th width="80" rowspan="2">Req. Qty</th>
							<th width="240" colspan="3">Today Recv.</th>
							<th width="240" title="Rec.+Issue Ret.+Trans. in" colspan="3">Total Received</th>
							<th width="80" title="Req.-Totat Rec." rowspan="2">Received Balance</th>
							<th width="240" colspan="3">Today Issue</th>
							<th width="240" title="Issue+Rec. Ret.+Trans. out" colspan="3">Total Issued</th>
							<th width="100" title="Total Rec.- Total Issue" rowspan="2">Stock</th>
							<?
							foreach ($store_ids_arr as $store_id) 
							{
								?>
								<th width="<? echo $store_width; ?>" title="Store ID = <? echo $store_id; ?>" rowspan="2"><? echo $store_arr[$store_id]; ?></th>
								<?
							}
							?>
						</tr>
						<tr>
							<th width="80">Receive</th>
							<th width="80">Issue Return</th>
							<th width="80">Transfer In</th>
							<th width="80">Receive</th>
							<th width="80">Issue Return</th>
							<th width="80">Transfer In</th>
							<th width="80">Issue</th>
							<th width="80">Receive Return</th>
							<th width="80">Transfer Out</th>
							<th width="80">Issue</th>
							<th width="80">Receive Return</th>
							<th width="80">Transfer Out</th>
						</tr>
					</thead>
		
						<?
						$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$total_today_issue=0;$total_today_recv=0;
						$fin_color_array=array(); $fin_color_data_arr=array();
						foreach ($style_wise_arr  as $job_key=>$job_val)
						{
							foreach ($job_val  as $color_key=>$color_val)
							{
								foreach ($color_val  as $desc_key=>$val)
								{
									$prod_ids=rtrim($val['prod_ids'],',');
									$prod_ids=implode(",",array_filter(array_unique(explode(",",$prod_ids))));
									$color_id=$row[csf("color_id")];
									$fab_desc_type=$desc_key;
									$po_nos=rtrim($val['po_no'],',');
									$po_nos=implode(",",array_filter(array_unique(explode(",",$po_nos))));
									$grouping=implode(",",array_filter(array_unique(explode(",",chop($val['grouping'],",")))));
									$poids=rtrim($val['po_id'],',');

									$po_ids=array_filter(array_unique(explode(",",$poids)));
									$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
									/*foreach($po_ids as $po_id)
									{
										$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
									}*/
									$order_no_arr=array_filter(array_unique($job_order_no_arr[$job_key]));
									$order_nos="";
									foreach($order_no_arr as $po_id)
									{
										$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$desc_key]['fin_fab_qnty'];
									}
									$order_nos = implode(",", $order_no_arr); //N.B. this $order_nos only for showing required quantity

									$po_ids=implode(",",$po_ids);
									$today_recv=$val[("today_receive_qnty")];
									$today_rtn_qnty=$val[("today_issue_rtn_qnty")];
									$today_trans_in_qnty=$val[("today_trans_in_qnty")];


									$today_issue=$val[("today_issue_qnty")];
									$today_issue_rcv_rtn_qnty=$val[("today_rcv_rtn_qnty")];
									$today_issue_trns_out_qnty=$val[("today_trns_out_qnty")];

									$rec_qty = $val[("receive_qnty")];
									$rec_ret_qty = $val[("issue_rtn_qnty")];
									//$rec_trns_qty = $val[("trans_in_qnty")];
									$rec_trns_qty = $trns_in_qnty_arr[$job_key][$color_key][$desc_key];

									$rec_qty_cal=($rec_qty+$rec_ret_qty+$rec_trns_qty);

									$iss_qty = $style_wise_arr2[$job_key][$color_key][$desc_key]['issue_qnty'];
									$iss_ret_qty=$val[("issue_rcv_rtn_qnty")];

									$iss_trns_qty = $trns_out_qnty_arr[$job_key][$color_key][$desc_key];
									$iss_qty_cal=($iss_qty+$iss_ret_qty+$iss_trns_qty);


									$popup_ref_data = $val[("buyer_name")]."_".$val[("job_no_pre")]."_".$val[("year")]."_".$val[("style_ref_no")]."_".$grouping."_".$desc_key;
									$stock_check=$rec_qty_cal-$iss_qty_cal;

									$totRev=$rec_qty+$rec_ret_qty+$rec_trns_qty;
									$totIssue=$iss_qty+$iss_ret_qty+$iss_trns_qty;
									if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
									{
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td><? echo $i; ?></td>
											<td ><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
											<td align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
											<td  align="center"><p><? echo $val[("year")]; ?></p></td>
											<td><p><? echo $val[("style_ref_no")]; ?></p></td>
											<td><p><? echo $grouping; ?></p></td>

											<td align="center" title="<? echo 'PO No-'.$po_nos;?>">
												<?
												$po_ids_exp=explode(",", $po_ids);
												$poId_count=0; $partial_shipSts_count=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$partial_shipSts_count++;
													}
													$poId_count++;
												}
												if($partial_shipSts_count==$poId_count){
													?>
													<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
													<?
												}
												else
												{
													?>
													<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
													<?
												}
												?>
											</td>
											<td title="<? echo $po_ids;?>"><p>
												<? 
													$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
													foreach ($po_ids_exp as $row) {
														if($po_id_shipingStatus_arr[$row]==3)
														{
															$full_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==2){
															$partial_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==1){
															$panding_shipSts_countx++;
														}
														$poId_countx++;
													}
													if($full_shipSts_countx==$poId_countx){
														$ShipingStatus="Full Delivery/Closed";
													}
													else if ($partial_shipSts_countx==$poId_countx) {
														$ShipingStatus="Partial Delivery";
													}
													else if ($panding_shipSts_countx==$poId_countx) {
														$ShipingStatus="Full Pending";
													}
													else
													{
														$ShipingStatus="Partial Delivery";
													}
													echo $ShipingStatus;
												?></p></td>
											<td title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
											<td title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
											<td align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
												<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
												</p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

											<td align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
											<td align="right">
												<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
												</p>
											</td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
											</td>

											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

											<td align="right" title="<? echo "Receive: $totRev " ."-". " Issue: $totIssue"; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
											</td>

											<?
											$store_receive=0;
											foreach ($store_ids_arr as $store_id) 
											{
												$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
												$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
												$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_in_qnty"],2,".","");

												$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
												$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
												$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

												$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
												$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
												?>
												<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
												<?
												$store_wise_total_stock[$store_id] += $store_balance;
											}
											?>
										</tr>
										<?
										$i++;

										$total_order_qnty+=$val[("po_quantity")];
										$total_req_qty+=$book_qty;

										$total_rec_qty+=$rec_qty;
										$total_rec_ret_qty+=$rec_ret_qty;
										$total_rec_trns_qty+=$rec_trns_qty;

										$total_rec_bal+=$rec_bal;

										$total_issue_qty+=$iss_qty;
										$total_issue_ret_qty+=$iss_ret_qty;
										$total_issue_trns_qty+=$iss_trns_qty;

										$total_stock+=$stock;

										$total_possible_cut_pcs+=$possible_cut_pcs;
										$total_actual_cut_qty+=$actual_qty;
										$total_rec_return_qnty+=$receive_ret_qnty;
										$total_issue_ret_qnty+=$issue_ret_qnty;

										$total_today_issue+=$today_issue;
										$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
										$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

										$total_today_recv+=$today_recv;
										$total_today_rtn_qnty+=$today_rtn_qnty;
										$total_today_trans_in_qnty+=$today_trans_in_qnty;
									}
									else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
									{
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td ><? echo $i; ?></td>
											<td ><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
											<td align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
											<td align="center"><p><? echo $val[("year")]; ?></p></td>
											<td><p><? echo $val[("style_ref_no")]; ?></p></td>
											<td><p><? echo $grouping; ?></p></td>

											<td align="center" title="<? echo 'PO No-'.$po_nos;?>">
												<?
												$po_ids_exp=explode(",", $po_ids);
												$poId_count=0; $partial_shipSts_count=0;
												foreach ($po_ids_exp as $row) {
													if($po_id_shipingStatus_arr[$row]==3)
													{
														$partial_shipSts_count++;
													}
													$poId_count++;
												}
												if($partial_shipSts_count==$poId_count){
													?>
													<a style="color:red;" href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
													<?
												}
												else
												{
													?>
													<a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a>
													<?
												}
												?>
											</td>
											<td title="<? echo $po_ids;?>"><p>
												<? 
													$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
													foreach ($po_ids_exp as $row) {
														if($po_id_shipingStatus_arr[$row]==3)
														{
															$full_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==2){
															$partial_shipSts_countx++;
														}
														else if($po_id_shipingStatus_arr[$row]==1){
															$panding_shipSts_countx++;
														}
														$poId_countx++;
													}
													if($full_shipSts_countx==$poId_countx){
														$ShipingStatus="Full Delivery/Closed";
													}
													else if ($partial_shipSts_countx==$poId_countx) {
														$ShipingStatus="Partial Delivery";
													}
													else if ($panding_shipSts_countx==$poId_countx) {
														$ShipingStatus="Full Pending";
													}
													else
													{
														$ShipingStatus="Partial Delivery";
													}
													echo $ShipingStatus;
												?></p></td>
											<td title="<? echo $color_key;?>"><p><? echo $color_arr[$color_key]; ?></p></td>
											<td title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo $desc_key; ?> </p></td>
											<td align="right" title="<? echo $job_key."==".$color_key."==".$desc_key."==".$po_ids;?>"><p>
													<a href='#report_details' onClick="openmypage_req_qnty('<? echo $order_nos; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo $job_key; ?>','<? echo $order_nos; ?>','<? echo $desc_key; ?>','<? echo 1; ?>','req_qnty_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</a>
											</p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','today_total_rec_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_recv,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_rtn_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_rtn_qnty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_trans_in_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_trans_in_qnty,2,'.',''); ?></a></p></td>

											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','total_receive_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_ret_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_ret_qty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','receive_trns_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($rec_trns_qty,2,'.',''); ?></a></p></td>

											<td align="right"><p><? $rec_bal=$book_qty-$rec_qty_cal; echo number_format($rec_bal,2,'.','');?></p></td>
											<td align="right">
												<p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a>
												</p>
											</td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_rcv_rtn_qnty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','today_issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($today_issue_trns_out_qnty,2,'.',''); ?></a></p>
											</td>

											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','total_issue_popup','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_receive_return','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_ret_qty,2,'.',''); ?></a></p></td>
											<td align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 5; ?>','issue_popup_transfer_out','<? echo $popup_ref_data; ?>');"><? echo number_format($iss_trns_qty,2,'.',''); ?></a></p></td>

											<td width="100" align="right" title="<? echo "Receive: $totRev " ."-". " Issue: $totIssue"; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $prod_ids; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 6; ?>','knit_stock_popup','<? echo $popup_ref_data; ?>');"><? $stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></a></p>
											</td>

											<?
											$store_receive=0;
											foreach ($store_ids_arr as $store_id) 
											{
												$store_receive 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["receive_qnty"],2,".","");
												$store_issue_rtn 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rtn_qnty"],2,".","");
												$store_trans_in 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_in_qnty"],2,".","");

												$store_rcv_rtn 		 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_rcv_rtn_qnty"],2,".","");
												$store_trns_out 	 = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["trns_out_qnty"],2,".","");
												$store_issue_qnty    = number_format($store_wise_stock_arr[$job_key][$color_key][$desc_key][$store_id]["issue_qnty"],2,".","");

												$store_balance = number_format(($store_receive+$store_issue_rtn+$store_trans_in) - ($store_rcv_rtn+$store_trns_out+$store_issue_qnty),2,".","");
												$store_balance_title = "Receive=$store_receive, Transfer In=$store_trans_in, Issue Return=$store_issue_rtn \nIssue=$store_issue_qnty, Transfer Out=$store_trns_out, Receive Return=$store_rcv_rtn";
												?>
												<td width="<? echo $store_width; ?>" title="<? echo $store_balance_title; ?>" align="right"><? echo $store_balance; ?></td>
												<?
												$store_wise_total_stock[$store_id] += $store_balance;
											}
											?>
										</tr>
										<?
										$i++;

										$total_order_qnty+=$val[("po_quantity")];
										$total_req_qty+=$book_qty;

										$total_rec_qty+=$rec_qty;
										$total_rec_ret_qty+=$rec_ret_qty;
										$total_rec_trns_qty+=$rec_trns_qty;

										$total_rec_bal+=$rec_bal;

										$total_issue_qty+=$iss_qty;
										$total_issue_ret_qty+=$iss_ret_qty;
										$total_issue_trns_qty+=$iss_trns_qty;

										$total_stock+=$stock;

										$total_possible_cut_pcs+=$possible_cut_pcs;
										$total_actual_cut_qty+=$actual_qty;
										$total_rec_return_qnty+=$receive_ret_qnty;
										$total_issue_ret_qnty+=$issue_ret_qnty;

										$total_today_issue+=$today_issue;
										$total_today_issue_rcv_rtn_qnty+=$today_issue_rcv_rtn_qnty;
										$total_today_issue_trns_out_qnty+=$today_issue_trns_out_qnty;

										$total_today_recv+=$today_recv;
										$total_today_rtn_qnty+=$today_rtn_qnty;
										$total_today_trans_in_qnty+=$today_trans_in_qnty;
									}
								}
							}
						}
						?>

					</tr>
					<tfoot>
						<tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>Total</th>
						<th align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
						<th align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?>10</th>
						<th align="right" id="value_total_today_rtn_qnty"><? echo number_format($total_today_rtn_qnty,2,'.',''); ?></th>
						<th align="right" id="value_total_today_trans_in_qnty"><? echo number_format($total_today_trans_in_qnty,2,'.',''); ?></th>
						<th align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>
						<th align="right" id="value_total_rec_ret_qty"><? echo number_format($total_rec_ret_qty,2,'.',''); ?></th>
						<th align="right" id="value_total_rec_trns_qty"><? echo number_format($total_rec_trns_qty,2,'.',''); ?></th>

						<th align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>

						<th  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
						<th  id="value_total_today_issue_rcv_rtn_qty"><? echo number_format($total_today_issue_rcv_rtn_qnty,2,'.',''); ?></th>
						<th  id="value_recv_total_today_issue_trns_out_qnty"><? echo number_format($total_today_issue_trns_out_qnty,2,'.',''); ?></th>
						<th align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
						<th align="right" id="value_total_issue_ret_qty"><? echo number_format($total_issue_ret_qty,2,'.',''); ?></th>
						<th align="right" id="value_total_issue_trns_qty"><? echo number_format($total_issue_trns_qty,2,'.',''); ?></th>

						<th align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
						<?
						foreach ($store_ids_arr as $store_id) {
							?>
							<th width="<? echo $store_width; ?>" id="value_total_store_qty"><? echo number_format($store_wise_total_stock[$store_id],2,".",""); ?></th>
							<?
						}
						?>
						</tr>
					</tfoot>
				</table>

			<?
		}
		//Knit end
		else if($cbo_report_type==2) // Woven Finish Start
		{
			$product_array=array();
			$sql_product="select id, color, detarmination_id,dia_width,weight,product_name_details from product_details_master where item_category_id=3 and status_active=1 and is_deleted=0";
			$sql_product_result=sql_select($sql_product);
			foreach( $sql_product_result as $row )
			{
				$product_array[$row[csf('product_name_details')]]['color']=$row[csf('color')];
				$product_array[$row[csf('product_name_details')]]['dia_width']=$row[csf('dia_width')];
				$product_array[$row[csf('product_name_details')]]['weight']=$row[csf('weight')];
				$product_array[$row[csf('product_name_details')]]['yarn_did']=$row[csf('detarmination_id')];
			}

			$issue_qnty=array();
			$sql_issue=sql_select(" select b.po_breakdown_id,b.color_id, sum(b.quantity) as issue_qnty  from inv_issue_master a,inv_transaction c, order_wise_pro_details b where a.id=c.mst_id and c.prod_id=b.prod_id  and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.issue_purpose in(4,9) and b.entry_form=19 group by b.po_breakdown_id,b.color_id");
			foreach( $sql_issue as $row_iss )
			{
				$issue_qnty[$row_iss[csf('po_breakdown_id')]][$row_iss[csf('color_id')]]['issue_qnty']=$row_iss[csf('issue_qnty')];
			} //var_dump($issue_qnty);

			$booking_qnty=array();
			//$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
			$sql_booking=sql_select("select b.po_break_down_id as po_id, b.fabric_color_id, b.job_no, (b.fin_fab_qnty) as fin_fab_qnty, c.lib_yarn_count_deter_id,b.gsm_weight,b.dia_width from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.job_no = c.job_no and b.pre_cost_fabric_cost_dtls_id = c.id and a.item_category in(3,14) and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 ");
			foreach( $sql_booking as $row)
			{
				$booking_qnty[$row[csf('job_no')]][$row[csf('po_id')]][$row[csf('fabric_color_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('dia_width')]][$row[csf('gsm_weight')]]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			}
			unset($sql_booking);

			$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
			$product_arr=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details"  );
			$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
			$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
			$bodypart_arr=return_library_array( "select id,body_part_full_name from lib_body_part", "id", "body_part_full_name"  );

			?>
			<fieldset style="width:1550px;">
				<table cellpadding="0" cellspacing="0" width="1610">
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="14" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="14" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
					</tr>
					<tr  class="form_caption" style="border:none;">
						<td align="center" width="100%" colspan="14" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
					</tr>
				</table>
				<table width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<tr>
							<th width="30">SL</th>
							<th width="100">Buyer</th>
							<th width="60">Job</th>
							<th width="50">Year</th>
							<th width="110">Style</th>

							<th width="60">Order Status</th>
							<th width="100">Shipment Status</th>
							<th width="110">Fin. Fab. Color</th>
							<th width="100">Body Part</th>
							<th width="220">Fab. Desc.</th>

							<th width="80">Req. Qty</th>
							<th width="80">Today Recv.</th>
							<th width="80" title="Rec.+Issue Ret.+Trans. in">Total Received</th>

							<th width="80" title="Req.-Totat Rec.">Received Balance</th>
							<th width="80">Today Issue</th>
							<th width="80" title="Issue+Rec. Ret.+Trans. out">Total Issued</th>
							<th width="" title="Total Rec.- Total Issue">Stock</th>
						</tr>
					</thead>
				</table>
				<div style="width:1540px; max-height:350px; overflow-y:scroll;" id="scroll_body">
					<table width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
						<?
						if($db_type==0) $prod_cond="group_concat(e.prod_id) as prod_id";
						else if($db_type==2) $prod_cond="listagg(cast(e.prod_id as varchar2(4000)),',') within group (order by e.prod_id) as prod_id";

						$sql_query="SELECT a.company_name, a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number as po_no,d.color_id,d.prod_id,c.balance_qnty, $select_fld, e.product_name_details as prod_desc,b.shiping_status,c.body_part_id,c.transaction_type, c.batch_id,c.pi_wo_batch_no,  

						(case when d.entry_form in (17) then d.quantity else 0 end) as receive_qnty,
						(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 then d.quantity else 0 end) as finish_fabric_transfer_in,					
						(case when d.entry_form in (209) and c.transaction_type=4 and d.trans_type=4 then d.quantity else 0 end) as issue_rtn,

						(case when d.entry_form in (17) and $today_receive_date then d.quantity else 0 end) as today_receive_qnty,					
						(case when d.entry_form in (258) and c.transaction_type=5 and d.trans_type=5 and $today_receive_date then d.quantity else 0 end) as today_trans_in,
						(case when d.entry_form in (209)  and c.transaction_type=4 and d.trans_type=4  and $today_receive_date then d.quantity else 0 end) as today_issue_rtn,

						(case when d.entry_form in (19) then d.quantity else 0 end) as issue_qnty,
						(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 then d.quantity else 0 end) as finish_fabric_transfer_out,
						(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 then d.quantity else 0 end) as recv_rtn,

						(case when d.entry_form in (202) and c.transaction_type=3 and d.trans_type=3 and $today_receive_date then d.quantity else 0 end) as today_recv_rtn,
						(case when d.entry_form in (258) and c.transaction_type=6 and d.trans_type=6 and $today_receive_date then d.quantity else 0 end) as today_trans_out,
						(case when d.entry_form in (19) and $today_receive_date then d.quantity else 0 end) as today_issue_qnty

						from  wo_po_details_master a, wo_po_break_down b,inv_transaction c,  order_wise_pro_details d,product_details_master e 
						where a.job_no=b.job_no_mst and d.po_breakdown_id=b.id and  d.trans_id=c.id and d.prod_id=e.id and c.prod_id=e.id and d.entry_form in (17,19,258,209,202) and d.trans_id!=0 and d.entry_form in (17,19,258,209,202)  and c.item_category=3 and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $receive_date $buyer_id_cond $shiping_status_cond $search_cond $year_cond
						order by a.job_no,d.color_id,c.transaction_date";
						// echo $sql_query;
						$style_wise_arr=array();
						$nameArray=sql_select($sql_query);
						foreach ($nameArray as $row)
						{

							if($row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2)
							{
								$recv_issue_batchId=$row[csf("batch_id")];
							}
							else
							{
								$recv_issue_batchId=$row[csf("pi_wo_batch_no")];
							}


							$po_id_shipingStatus_arr[$row[csf('po_id')]]=$row[csf('shiping_status')];

							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['batch_id'].=$recv_issue_batchId.',';

							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['job_no']=$row[csf('job_no')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['job_no_pre']=$row[csf('job_no_prefix_num')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['year']=$row[csf('year')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['buyer_name']=$row[csf('buyer_name')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['style_ref_no']=$row[csf('style_ref_no')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['po_id'].=$row[csf('po_id')].',';
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['po_no'].=$row[csf('po_no')].',';
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['receive_qnty']+=$row[csf('receive_qnty')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['rec_trns_qnty']+=$row[csf('rec_trns_qnty')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['issue_qnty']+=$row[csf('issue_qnty')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['issue_rtn']+=$row[csf('issue_rtn')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['recv_rtn']+=$row[csf('recv_rtn')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['issue_trns_qnty']+=$row[csf('issue_trns_qnty')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['balance_qnty']+=$row[csf('balance_qnty')];

							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['today_receive_qnty']+=$row[csf('today_receive_qnty')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['today_issue_qnty']+=$row[csf('today_issue_qnty')];

							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['today_trans_in']+=$row[csf('today_trans_in')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['today_trans_out']+=$row[csf('today_trans_out')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['today_issue_rtn']+=$row[csf('today_issue_rtn')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['today_recv_rtn']+=$row[csf('today_recv_rtn')];

							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['finish_fabric_transfer_in']+=$row[csf('finish_fabric_transfer_in')];
							$style_wise_arr[$row[csf('job_no')]][$row[csf('color_id')]][$row[csf('prod_desc')]][$row[csf('body_part_id')]]['finish_fabric_transfer_out']+=$row[csf('finish_fabric_transfer_out')];
						}

						$i=1;
						foreach ($style_wise_arr  as $job_key=>$job_val)
						{
							foreach ($job_val  as $color_key=>$color_val)
							{
								foreach ($color_val  as $desc_key=>$desc_val)
								{
									foreach ($desc_val  as $bodypart_key=>$val)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$dzn_qnty=0;
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										if($costing_per_id_library[$job_key]==1) $dzn_qnty=12;
										else if($costing_per_id_library[$job_key]==3) $dzn_qnty=12*2;
										else if($costing_per_id_library[$job_key]==4) $dzn_qnty=12*3;
										else if($costing_per_id_library[$job_key]==5) $dzn_qnty=12*4;
										else $dzn_qnty=1;

										$color_id=$row[csf("color_id")];
										//$fab_desc_type=$product_arr[$desc_key];
										$fab_desc_type=$desc_key;
										$po_nos=rtrim($val['po_no'],',');
										$po_nos=implode(",",array_unique(explode(",",$po_nos)));
										$poids=rtrim($val['po_id'],',');

										$po_ids=array_unique(explode(",",$poids));
										$book_qty=0;$issue_ret_qnty=0;$receive_ret_qnty=0;
										foreach($po_ids as $po_id)
										{
											//echo $job_key."=".$po_id."=".$color_key."=".$product_array[$desc_key]['yarn_did']."=".$product_array[$desc_key]['dia_width']."=".$product_array[$desc_key]['fin_fab_qnty']."<br>";
											$book_qty+=$booking_qnty[$job_key][$po_id][$color_key][$product_array[$desc_key]['yarn_did']][$product_array[$desc_key]['dia_width']][$product_array[$desc_key]['weight']]['fin_fab_qnty'];
												//echo $job_key.'ii'.$po_id;
											$issue_ret_qnty+=$iss_return_qnty[$job_key][$po_id][$color_key][$desc_key]['issue_ret_qnty'];
											$receive_ret_qnty+=$rec_return_qnty[$job_key][$po_id][$color_key][$desc_key]['rec_ret_qnty'];
										}

											//echo $job_key.'ii';
										$po_ids=implode(",",$po_ids);
										$today_recv=$val[("today_receive_qnty")]+$val[("today_trans_in")]+$val[("today_issue_rtn")];
										$today_issue=$val[("today_issue_qnty")]+$val[("today_trans_out")]+$val[("today_recv_rtn")];
										$rec_qty=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);
										//$rec_qty_cal=($val[("receive_qnty")]+$val[("finish_fabric_transfer_in")]+$val[("issue_rtn")]);

										$iss_qty=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
										//$iss_qty_cal=($val[("issue_qnty")]+$val[("finish_fabric_transfer_out")]+$val[("recv_rtn")]);
										$stock_check=($rec_qty-$iss_qty);

										//----batch---
										$batchids=rtrim($val['batch_id'],',');
										$batch_ids=array_unique(explode(",",$batchids));
										$batch_ids=implode(",",$batch_ids);
										//----------

										if($cbo_value_range_by==2 &&  number_format($stock_check,2,'.','')>0.00)
										{

											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
												<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
												<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
												<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

												<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
												<td width="100" title="<? echo $po_ids;?>"><p>
													<? 
														$po_ids_exp=explode(",", $po_ids);
														$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
														foreach ($po_ids_exp as $row) 
														{
															if($po_id_shipingStatus_arr[$row]==3)
															{
																$full_shipSts_countx++;
															}
															else if($po_id_shipingStatus_arr[$row]==2){
																$partial_shipSts_countx++;
															}
															else if($po_id_shipingStatus_arr[$row]==1){
																$panding_shipSts_countx++;
															}
															$poId_countx++;
														}
														if($full_shipSts_countx==$poId_countx){
															$ShipingStatus="Full Delivery/Closed";
														}
														else if ($partial_shipSts_countx==$poId_countx) {
															$ShipingStatus="Partial Delivery";
														}
														else if ($panding_shipSts_countx==$poId_countx) {
															$ShipingStatus="Full Pending";
														}
														else
														{
															$ShipingStatus="Partial Delivery";
														}
														echo $ShipingStatus;
													?></p></td>
												<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
												<td width="100"><p><? echo $bodypart_arr[$bodypart_key]; ?></p></td>
												<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
												<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>

												<td width="80" align="right"><p><?
												$rec_bal=$book_qty-$rec_qty;
												//$rec_bal=$val[("balance_qnty")];
												echo number_format($rec_bal,2,'.','');

												?></p></td>
												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>

												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>

												<td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><?
												//$stock=$rec_qty_cal-$iss_qty_cal; old
												$stock=($rec_qty-$iss_qty); //new $rec_qty
												echo number_format($stock,2,'.','');
												?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>

											</tr>
											<?
											$i++;

											$total_req_qty+=$book_qty;
											$total_rec_qty+=$rec_qty;
											$total_rec_bal+=$rec_bal;
											$total_issue_qty+=$iss_qty;
											$total_stock+=$stock;
											$total_possible_cut_pcs+=$possible_cut_pcs;
											$total_actual_cut_qty+=$actual_qty;
											$total_rec_return_qnty+=$receive_ret_qnty;
											$total_issue_ret_qnty+=$issue_ret_qnty;
											$total_today_issue+=$today_issue;
											$total_today_recv+=$today_recv;
										}
										else if($cbo_value_range_by==1 || $cbo_value_range_by==0)
										{
											?>
											<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
												<td width="30"><? echo $i; ?></td>
												<td width="100"><p><? echo $buyer_arr[$val[("buyer_name")]]; ?></p></td>
												<td width="60" align="center"><p><? echo $val[("job_no_pre")]; ?></p></td>
												<td width="50" align="center"><p><? echo $val[("year")]; ?></p></td>
												<td width="110"><p><? echo $val[("style_ref_no")]; ?></p></td>

												<td width="60" align="center" title="<? echo 'PO No-'.$po_nos;?>"><a href='#report_details'  onClick="openmypage_ex_factory('<? echo $po_ids; ?>','1');">View</a></td>
												<td width="100" title="<? echo $po_ids;?>"><p>
													<? 
														$po_ids_exp=explode(",", $po_ids);
														$poId_countx=0; $partial_shipSts_countx=0;$panding_shipSts_countx=0;$full_shipSts_countx=0;
														foreach ($po_ids_exp as $row) 
														{
															if($po_id_shipingStatus_arr[$row]==3)
															{
																$full_shipSts_countx++;
															}
															else if($po_id_shipingStatus_arr[$row]==2){
																$partial_shipSts_countx++;
															}
															else if($po_id_shipingStatus_arr[$row]==1){
																$panding_shipSts_countx++;
															}
															$poId_countx++;
														}
														if($full_shipSts_countx==$poId_countx){
															$ShipingStatus="Full Delivery/Closed";
														}
														else if ($partial_shipSts_countx==$poId_countx) {
															$ShipingStatus="Partial Delivery";
														}
														else if ($panding_shipSts_countx==$poId_countx) {
															$ShipingStatus="Full Pending";
														}
														else
														{
															$ShipingStatus="Partial Delivery";
														}
														echo $ShipingStatus;
													?></p></td>
												<td width="110"><p><? echo $color_arr[$color_key]; ?></p></td>
												<td width="100"><p><? echo $bodypart_arr[$bodypart_key]; ?></p></td>
												<td width="220" title="<? echo 'Prod. ID='.$desc_key;?>"><p><? echo trim($fab_desc_type, " , " ); ?></p></td>
												<td width="80" align="right"><p><? echo number_format($book_qty,2,'.',''); ?>&nbsp;</p></td>

												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_receive_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($today_recv,2,'.',''); ?>&nbsp;</p></td>


												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 1; ?>','woven_receive_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($rec_qty,2,'.',''); ?></a></p></td>


												<td width="80" align="right"><p><?
												$rec_bal=$book_qty-$rec_qty;
												//$rec_bal=$val[("balance_qnty")];
												echo number_format($rec_bal,2,'.','');

												?></p></td>
												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 0; ?>','woven_today_issue_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($today_issue,2,'.',''); ?></a></p></td>

												<td width="80" align="right"><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from; ?>','<? echo 2; ?>','woven_issue_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><? echo number_format($iss_qty,2,'.',''); ?></a><? //echo number_format($iss_qty,2,'.',''); ?></p></td>


												<td width="" align="right" title="<? echo "Receive: ".$val[("receive_qnty")]."##".$issue_ret_qnty."##".$val[("rec_trns_qnty")]."Issue: ".$val[("issue_qnty")]."##".$receive_ret_qnty."##".$val[("issue_trns_qnty")]; ?>" ><p><a href='#report_details' onClick="openmypage('<? echo $po_ids; ?>','<? echo $desc_key; ?>','<? echo $color_key; ?>','<? echo $date_from;; ?>','<? echo 6; ?>','woven_knit_stock_popup','','','<? echo "btn1"; ?>','<? echo $batch_ids; ?>');"><?
												//$stock=$rec_qty_cal-$iss_qty_cal; old
												$stock=($rec_qty-$iss_qty); //new $rec_qty
												echo number_format($stock,2,'.','');
												?></a><? //$stock=$rec_qty_cal-$iss_qty_cal; echo number_format($stock,2,'.',''); ?></p></td>

											</tr>
											<?
											$i++;

											$total_req_qty+=$book_qty;
											$total_rec_qty+=$rec_qty;
											$total_rec_bal+=$rec_bal;
											$total_issue_qty+=$iss_qty;
											$total_stock+=$stock;
											$total_possible_cut_pcs+=$possible_cut_pcs;
											$total_actual_cut_qty+=$actual_qty;
											$total_rec_return_qnty+=$receive_ret_qnty;
											$total_issue_ret_qnty+=$issue_ret_qnty;
											$total_today_issue+=$today_issue;
											$total_today_recv+=$today_recv;
										}
									}
								}
							}
						}
						?>
					</table>
					<table width="1520" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
						<tfoot>
							<th width="30"></th>
							<th width="100"></th>
							<th width="60"></th>
							<th width="50"></th>
							<th width="110">&nbsp;</th>

							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="110">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="220">Total</th>
							<th width="80" align="right" id="value_total_req_qty"><? echo number_format($total_req_qty,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_today_rec_qty"><? echo number_format($total_today_recv,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_rec_qty"><? echo number_format($total_rec_qty,2,'.',''); ?></th>

							<th width="80" align="right" id="value_total_rec_bal"><? echo number_format($total_rec_bal,2,'.',''); ?></th>
							<th width="80"  id="value_recv_today_issue_qty"><? echo number_format($total_today_issue,2,'.',''); ?></th>
							<th width="80" align="right" id="value_total_issue_qty"><? echo number_format($total_issue_qty,2,'.',''); ?></th>
							<th width="" align="right" id="value_total_stock"><? echo number_format($total_stock,2,'.',''); ?></th>
						</tfoot>
					</table>
				</div>
			</fieldset>
			<?
		}

		$html = ob_get_contents();
		ob_clean();

		foreach (glob("../tmp/style_wish_knit_finish_fabric_stock"."*.pdf") as $filename) {			
			@unlink($filename);
		}
		$pdfObj = ['mode' => 'utf-8', 'format' => [190, 436]];

		$att_file_arr=array();
		$mpdf = new mPDF($pdfObj);
		$mpdf->WriteHTML($html,2);
		$REAL_FILE_NAME = 'style_wish_knit_finish_fabric_stock_'.$cbo_company_id .'_'. date('d-M-Y_h-iA',strtotime(str_replace("'","",$txt_date_from))) . '.pdf';
		$mpdf->Output('../tmp/' . $REAL_FILE_NAME, 'F');
		$att_file_arr[]='../tmp/'.$REAL_FILE_NAME.'**'.$REAL_FILE_NAME;


		$mail_item=122;
		$to="";	
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and a.company_id=".$cbo_company_id." and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";//and 
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if($row[csf('email_address')]){$toMailArr[]=$row[csf('email_address')]; }
		}
		
		$to=implode(',',$toMailArr);
		$subject = "Style Wise Knit Finish Fabric Status";
		$message="<b>Sir,</b><br>Please check Style Wise Knit Finish Fabric Status"."<br>";
		
		

		$header=mailHeader();
		//$to="reza@logicsoftbd.com";

		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo  $message."<br>".$html;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail,$att_file_arr);}
		}

	}//end company loof;
	 

	


	exit();
}


 
 
?>

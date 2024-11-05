
<?
include('../../../includes/common.php');

$user_id=1;
function fnc_tempenginepo($table_name, $user_id, $entry_form, $ref_from, $ref_id_arr,  $ref_str_arr="")
{
	global $con ;
	
	$numeless=count($ref_id_arr);
	//echo $con.'='.$user_id.'='.$entry_form.'='.$ref_from.'='.$ref_id_arr;
	//print_r($ref_id_arr);
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


$action="report_generate";

if($action=="report_generate")
{ 	
	$ftmlStorArr=array(48=>48,82=>82,83=>83,86=>86);	
	$uhmStorArr=array(18=>18,47=>47,80=>80,81=>81);	
	$urmiStorArr=array(49=>49,85=>85,88=>88,95=>95,97=>97,7=>7,87=>87,89=>89);	
	$attStorArr=array(64=>64,84=>84,96=>96,98=>98);	
	
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
	$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
	//$previous_date='05-Sep-2022';$current_date='05-Sep-2022';
	
	$cbo_company_id= "'4,1,3,8,2'";
	$cbo_store_name= implode(',',$urmiStorArr);
	 
	$cbo_buyer_id=	'';
	
	$txt_book_no=	"''";
	$txt_book_id=	"''";
	$cbo_year=	"'0'";
	$txt_job_no=	"''";
	$txt_job_id=	"''";
	$txt_pi_no=	"''";
	$hdn_pi_id=	"''";
	$cbo_pay_mode=	"'0'";
	$cbo_supplier_id=	"'0'";
	$cbo_value_with=	"'1'";
	$cbo_get_upto=	"'0'";
	$txt_days=	"''";
	$cbo_get_upto_qnty=	"'0'";
	$txt_qnty=	"''";
	$txt_date_from=	$previous_date;
	$txt_date_to=	$previous_date;
	$cbo_report_type=	"1";
	
	
	
	$report_type 		= str_replace("'","",$cbo_report_type);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$book_no 			= trim(str_replace("'","",$txt_book_no));
	$book_id 			= str_replace("'","",$txt_book_id);
	$job_no 			= trim(str_replace("'","",$txt_job_no));
	$txt_pi_no 			= trim(str_replace("'","",$txt_pi_no));
	$hdn_pi_id 			= trim(str_replace("'","",$hdn_pi_id));

	$txt_file_no 		= str_replace("'","",$txt_file_no);
	$txt_ref_no 		= str_replace("'","",$txt_ref_no);
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_pay_mode 		= str_replace("'","",$cbo_pay_mode);
	$cbo_supplier_id 	= str_replace("'","",$cbo_supplier_id);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$date_from 		 	= str_replace("'","",$txt_date_from);
	$date_to 		 	= str_replace("'","",$txt_date_to);
	$cbo_value_with 	= str_replace("'","",$cbo_value_with);

	$get_upto 			= str_replace("'","",$cbo_get_upto);
	$txt_days 			= str_replace("'","",$txt_days);
	$get_upto_qnty 		= str_replace("'","",$cbo_get_upto_qnty);
	$txt_qnty 			= str_replace("'","",$txt_qnty);

	if($cbo_store_name > 0){
		$store_cond = " and b.store_id in ($cbo_store_name)";
		$store_cond_2 = " and c.store_id in ($cbo_store_name)";
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_id=$buyer_id";
	}

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and YEAR(f.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(f.insert_date,'YYYY')=$job_year";
	}

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)$start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond   = " and b.transaction_date <= '$end_date'";
		$date_cond_2 = " and c.transaction_date <= '$end_date'";
	}

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$supplier_arr 	= return_library_array("select id,short_name from lib_supplier where status_active=1","id","short_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$season_arr 	= return_library_array( "select id, season_name from lib_buyer_season where status_active=1 and is_deleted=0",'id','season_name');
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_arr 		= return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	/*if($txt_pi_no != "")
	{
		$pi_search_sql = sql_select("select a.id, a.pi_number, b.work_order_no, b.booking_without_order from com_pi_master_details a, com_pi_item_details b where a.id = b.pi_id and a.pi_basis_id = 1 and b.item_category_id = 2 and a.importer_id=$cbo_company_id and a.pi_number='$txt_pi_no' and a.status_active=1 and b.status_active=1");
		foreach ($pi_search_sql as $val)
		{
			$search_book_arr[$val[csf("work_order_no")]] = $val[csf("work_order_no")];
		}
	}*/

	$pi_no_cond="";
	if ($hdn_pi_id=="")
	{
		$pi_no_cond="";
	}
	else
	{
		$pi_no_cond=" and a.booking_id = '$hdn_pi_id' and a.receive_basis=1 ";
		$pi_no_trans_cond = " and a.id = 0";
	}

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";
	if ($book_no=="") $booking_no_cond=""; else $booking_no_cond=" and d.booking_no_prefix_num='$book_no'";
	if($cbo_supplier_id ==0) $supplier_cond = ""; else $supplier_cond = " and d.supplier_id = ".$cbo_supplier_id;
	if($cbo_pay_mode ==0) $pay_mode_cond = ""; else $pay_mode_cond = " and d.pay_mode = ".$cbo_pay_mode;

	if($job_no != "" || $book_no!="" || $cbo_supplier_id !=0 || $buyer_id!=0 || $cbo_pay_mode !=0)
	{
		$serch_ref_sql_1 = "select c.booking_no from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where c.status_active=1 and e.status_active=1 and f.job_no=e.job_no_mst and c.booking_type in (1,4) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $booking_no_cond $year_cond $pay_mode_cond $supplier_cond ";

		$concate="";
		if($job_no == "")
		{
			$concate = " union all ";
			$serch_ref_sql_2 = " select d.booking_no from wo_non_ord_samp_booking_mst d where d.booking_type = 4 and d.company_id in ($cbo_company_id) $booking_no_cond $pay_mode_cond $supplier_cond $buyer_id_cond ";
		}
		$serch_ref_sql = $serch_ref_sql_1.$concate.$serch_ref_sql_2;

		$serch_ref_result = sql_select($serch_ref_sql);

		foreach ($serch_ref_result as $val)
		{
			$search_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
		}
		if(empty($search_book_arr))
		{
			echo "<p style='font-weight:bold;text-align:center;font-size:20px;'>Booking No not found</p>";
			die;
		}
	}

	if(!empty($search_book_arr))
	{
		$search_book_nos="'".implode("','",$search_book_arr)."'";
		$search_book_arr = explode(",", $search_book_nos);

		$all_book_nos_cond=""; $bookCond="";
		if($db_type==2 && count($search_book_arr)>999)
		{
			$all_search_book_arr_chunk=array_chunk($search_book_arr,999) ;
			foreach($all_search_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bookCond.="  e.booking_no in($chunk_arr_value) or ";
			}

			$all_book_nos_cond.=" and (".chop($bookCond,'or ').")";
		}
		else
		{
			$all_book_nos_cond=" and e.booking_no in($search_book_nos)";
		}
	}

	if($report_type==2)
	{
		$rcv_select = " b.floor_id, b.room, b.rack, b.self,";
		$rcv_group = " b.floor_id, b.room, b.rack, b.self,";
	}

	$rcv_sql = "SELECT b.id,e.booking_no, e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company,a.booking_id as wo_pi_prod_id,a.booking_no as wo_pi_prod_no, b.transaction_date, b.prod_id, b.store_id, $rcv_select c.body_part_id,c.fabric_description_id, c.gsm, c.width, f.color as color_id, b.cons_uom,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as dia_width_type, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as po_breakdown_id, b.cons_quantity as quantity,b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and d.entry_form=37 and d.po_breakdown_id <>0, pro_batch_create_mst e, product_details_master f
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and e.status_active=1 and b.pi_wo_batch_no=e.id and b.prod_id=f.id $store_cond $date_cond  $all_book_nos_cond $pi_no_cond
	group by b.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, $rcv_group c.body_part_id, c.fabric_description_id, c.gsm, c.width, f.color ,b.cons_uom,c.dia_width_type,b.cons_quantity, b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no order by a.company_id"; //and e.booking_no in('UHM-Fb-21-00038','UHM-Fb-21-00032')
	//echo $rcv_sql;die;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val[csf("dia_width_type")])));

		if($report_type==2)
		{
			$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
		}

		if($transaction_date >= $date_frm)
		{
			$data_array[$val[csf("cons_uom")]][$val[csf("booking_no")]][$ref_str] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*".$val[csf("receive_basis")]."*".$val[csf("wo_pi_prod_no")]."*".$dia_width_type_ref."*".$val[csf("lc_sc_no")]."*"."1*1__";
		}
		else
		{
			$data_array[$val[csf("cons_uom")]][$val[csf("booking_no")]][$ref_str] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*".$val[csf("receive_basis")]."*".$val[csf("wo_pi_prod_no")]."*".$dia_width_type_ref."*".$val[csf("lc_sc_no")]."*"."1*2__";
		}
		$all_prod_id[$val[csf("prod_id")]] = $val[csf("prod_id")];

		if($val[csf("booking_without_order")] == 0)
		{
			$all_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			$po_array[$val[csf("booking_no")]][$ref_str]["po_no"] .= $val[csf("po_breakdown_id")].",";
		}

		$book_str = explode("-", $val[csf("booking_no")]);

		if($val[csf("booking_without_order")] == 1 || $book_str[1] =="SMN")
		{
			$all_samp_book_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
		}
		$booking_no_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
		$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];

		$rate_arr_booking_and_product_wise[$val[csf("booking_no")]][$val[csf("prod_id")]][$val[csf("store_id")]]["quantity"] += $val[csf("quantity")];
		$rate_arr_booking_and_product_wise[$val[csf("booking_no")]][$val[csf("prod_id")]][$val[csf("store_id")]]["amount"] += $val[csf("order_amount")];
	}
	/*echo "<pre>";
	print_r($data_array);die;*/

	if($report_type == 2)
	{
		$trans_in_select = " c.floor_id, c.room, c.rack, c.self,";
		$trans_in_group = " c.floor_id, c.room, c.rack, c.self,";
	}

	if ($hdn_pi_id=="")
	{
		$trans_in_sql = "SELECT c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id,c.store_id, $trans_in_select d.detarmination_id, d.gsm, d.dia_width as width, d.color as color_id, c.cons_uom, sum(c.cons_quantity) as quantity,c.order_rate, c.order_amount, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as po_breakdown_id
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type = 5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e
		where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1  and a.entry_form in (14,15,306) $store_cond_2 $date_cond_2 $all_book_nos_cond
		group by c.transaction_date, c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, $trans_in_group d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount order by c.company_id";
		 //echo $trans_in_sql;die;
		$trans_in_data = sql_select($trans_in_sql);
		foreach ($trans_in_data as  $val)
		{

			$date_frm=date('Y-m-d',strtotime($start_date));
			$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
			$ref_str="";

			if($report_type == 2)
			{
				$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
			}
			else
			{
				$ref_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
			}

			if($transaction_date >= $date_frm)
			{
				$data_array[$val[csf("cons_uom")]][$val[csf("booking_no")]][$ref_str] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*"."*".""."*".""."*"."*5*1__";
			}
			else
			{
				$data_array[$val[csf("cons_uom")]][$val[csf("booking_no")]][$ref_str] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*"."*".""."*".""."*"."*5*2__";
			}

			$all_prod_id[$val[csf("prod_id")]] = $val[csf("prod_id")];

			if($val[csf("booking_without_order")] == 0)
			{
				$all_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				$po_array[$val[csf("booking_no")]][$ref_str]["po_no"] .= $val[csf("po_breakdown_id")].",";
			}

			$book_str = explode("-", $val[csf("booking_no")]);
			if($val[csf("booking_without_order")] == 1 || $book_str[1] == "SMN")
			{
				$all_samp_book_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
			}
			$booking_no_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
			$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];

			$rate_arr_booking_and_product_wise[$val[csf("booking_no")]][$val[csf("prod_id")]][$val[csf("store_id")]]["quantity"] += $val[csf("quantity")];
			$rate_arr_booking_and_product_wise[$val[csf("booking_no")]][$val[csf("prod_id")]][$val[csf("store_id")]]["amount"] += $val[csf("order_amount")];
		}
	}

	if(!empty($data_array))	
	{
		$con = connect();
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
		//$r_id4=execute_query("delete from tmp_poid where userid=$user_id");
		//$r_id5=execute_query("delete from tmp_batch_id where userid=$user_id");
		//$r_id6=execute_query("delete from tmp_prod_id where userid=$user_id");
		$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (990,991,992)");
		if($r_id3 && $r_id6)
		{
			oci_commit($con);
		}
	}

	$all_po_id_arr = array_filter($all_po_id_arr);
	$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{
		/*$all_po_ids=implode(",",$all_po_id_arr);
		$all_po_id_cond=""; $poCond="";
		$all_po_id_cond_2=""; $poCond_2="";
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$poCond.="  e.id in($chunk_arr_value) or ";
				$poCond_2.="  b.po_break_down_id in($chunk_arr_value) or ";
			}

			$all_po_id_cond.=" and (".chop($poCond,'or ').")";
			$all_po_id_cond_2.=" and (".chop($poCond_2,'or ').")";
		}
		else
		{
			$all_po_id_cond=" and e.id in($all_po_ids)";
			$all_po_id_cond_2=" and b.po_break_down_id in($all_po_ids)";
		}*/
		fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 991, 1,$all_po_id_arr, $empty_arr);//PO ID

		/* foreach ($all_po_id_arr as  $poval) {
			$rID2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$poval)");
			
		}
		if($rID2)
		{
		    oci_commit($con);
		} */

		/*$sql_min= "select e.id, MIN(e.pub_shipment_date) pub_shipment_date from  wo_po_break_down e where e.status_active!=0 $all_po_id_cond group by e.id";
		//echo $sql_min;
		$data_array_min=sql_select($sql_min);
		foreach ($data_array_min as $sql_min)
		{
			$min_date_arr[$sql_min[csf("id")]]["min_date"] =change_date_format($sql_min[csf('pub_shipment_date')],'dd-mm-yyyy','-');
		}

		
		$sql_max= "select e.id, MAX(e.pub_shipment_date) pub_shipment_date from  wo_po_break_down e where status_active!=0 $all_po_id_cond  group by e.id";
		$data_array_max=sql_select($sql_max);
		foreach ($data_array_max as $row_max)
		{
			$max_date_arr[$row_max[csf("id")]]["min_date"] =change_date_format($row_max[csf('pub_shipment_date')],'dd-mm-yyyy','-');
		}*/

		$ship_date_array = sql_select("SELECT g.booking_no, MIN(e.pub_shipment_date) min_shipment_date, MAX(e.pub_shipment_date) max_shipment_date from  wo_po_break_down e, wo_booking_dtls g, GBL_TEMP_ENGINE f where e.status_active!=0 and e.id=g.po_break_down_id and g.status_active=1 and g.booking_type in (1,4) and e.id=f.ref_val and f.user_id=$user_id and f.entry_form=990 group by g.booking_no");

		foreach ($ship_date_array as $sql_min) {
			$min_date_arr[$sql_min[csf("booking_no")]]["min_date"]=change_date_format($sql_min[csf('min_shipment_date')],'dd-mm-yyyy','-');
			$max_date_arr[$sql_min[csf("booking_no")]]["min_date"]=change_date_format($sql_min[csf('max_shipment_date')],'dd-mm-yyyy','-');
		}

		$booking_sql = sql_select("SELECT a.body_part_id,c.booking_no,a.lib_yarn_count_deter_id, c.fabric_color_id, c.gmts_color_id, c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name, f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise, f.total_set_qnty, f.job_quantity, c.fin_fab_qnty, a.uom, c.rate, d.supplier_id, c.po_break_down_id
		from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and e.job_id=f.id and c.booking_type =1 and c.booking_mst_id = d.id and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=990
		union all
		select b.body_part_id,c.booking_no,b.lib_yarn_count_deter_id, c.fabric_color_id, c.gmts_color_id,c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name, f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise,f.total_set_qnty, f.job_quantity, c.fin_fab_qnty, b.uom, c.rate, d.supplier_id,c.po_break_down_id
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f, GBL_TEMP_ENGINE g
		where b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and e.job_id=f.id and a.fabric_description = b.id and c.booking_type =4 and c.booking_mst_id=d.id  and c.po_break_down_id = e.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=990"); // $all_po_id_cond

		foreach ($booking_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_name")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_name")];
			$book_po_ref[$val[csf("booking_no")]]["job_no"] 		.= $val[csf("job_no")].",";
			$book_po_ref[$val[csf("booking_no")]]["client_id"] 		= $val[csf("client_id")];
			$book_po_ref[$val[csf("booking_no")]]["season"] 		.= $val[csf("season_buyer_wise")].",";
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	.= $val[csf("style_ref_no")].",";
			$book_po_ref[$val[csf("booking_no")]]["booking_no"] 	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"] 	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["pay_mode"] 		= $pay_mode[$val[csf("pay_mode")]];
			$book_po_ref[$val[csf("booking_no")]]["fs_date"] 		= $min_date_arr[$val[csf("booking_no")]]["min_date"];
			$book_po_ref[$val[csf("booking_no")]]["ls_date"] 		= $max_date_arr[$val[csf("booking_no")]]["min_date"];
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] == 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}

			$job_qnty_arr[$val[csf("job_no")]]["qnty"] = $val[csf("job_quantity")]*$val[csf("total_set_qnty")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["qnty"] += $val[csf("fin_fab_qnty")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["color_type"] .= $color_type[$val[csf("color_type")]].",";

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["amount"] += $val[csf("fin_fab_qnty")]*$val[csf("rate")];

			$bookingType="";
			if($val[csf('booking_type')] == 4)
			{
				$bookingType = "Sample With Order";
			}
			else
			{
				$bookingType = $booking_type_arr[$val[csf('entry_form')]];
			}
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] = $bookingType;
		}
	}
	//echo "<pre>";
	//print_r($book_po_ref);

	if(!empty($all_samp_book_arr))
	{
		/*$all_samp_book_nos_cond=""; $sampBookCond="";
		if($db_type==2 && count($all_samp_book_arr)>999)
		{
			$all_samp_book_arr_chunk=array_chunk($all_samp_book_arr,999) ;
			foreach($all_samp_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$sampBookCond.="  a.booking_no in($chunk_arr_value) or ";
			}

			$all_samp_book_nos_cond.=" and (".chop($sampBookCond,'or ').")";
		}
		else
		{
			$all_samp_book_nos_cond=" and a.booking_no in(".implode(",",$all_samp_book_arr).")";
		}
		*/

		foreach ($all_samp_book_arr as $s_book) {
			$rID2=execute_query("insert into tmp_booking_no (userid, booking_no) values ($user_id,".$s_book.")");
		}
		if($rID2)
		{
			oci_commit($con);
		}

		$non_samp_sql = sql_select("select a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.fabric_color, b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des, b.finish_fabric, b.rate from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b, tmp_booking_no c where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type =4 and a.booking_no=c.booking_no and c.userid=$user_id"); //and a.id in ($all_samp_book_ids)  $all_samp_book_nos_cond

		
		foreach ($non_samp_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["booking_no"]   	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"]  	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_id")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_id")];
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	= $val[csf("style_des")];
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] 	= "Sample WithOut Order";
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] 	== 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["qnty"] += $val[csf("finish_fabric")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["color_type"] .= $color_type[$val[csf("color_type_id")]].",";

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["amount"] += $val[csf("finish_fabric")]*$val[csf("rate")];
		}
		unset($non_samp_sql);
	}

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		/*
		$batch_ids= implode(",",$batch_id_arr);
		$all_batch_ids_cond=""; $batchCond="";
		if($db_type==2 && count($batch_id_arr)>999)
		{
			$batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
			foreach($batch_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.="  e.id in($chunk_arr_value) or ";
			}
			$all_batch_ids_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_ids_cond=" and e.id in($batch_ids)";
		}
		*/

		fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 991, 1,$batch_id_arr, $empty_arr);//PO ID

		/* foreach ($batch_id_arr as $batchID) {
			$rID3=execute_query("insert into tmp_batch_id (userid, batch_id) values ($user_id,".$batchID.")");
		}
		if($rID3)
		{
			oci_commit($con);
		} */

	}

	if($report_type == 2)
	{
		$issue_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$issRtnSql = "select c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.batch_no, e.booking_no, e.booking_without_order from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no = e.id and c.prod_id=f.id and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 ";  //$all_batch_ids_cond
	$issRtnData = sql_select($issRtnSql);
	foreach ($issRtnData as $val)
	{
		if($report_type == 2)
		{
			$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$issRtnRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("gsm")]."*".$val[csf("width")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			if($val[csf("knit_dye_source")] == 1)
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["inside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
			}
			else
			{
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return"] += $val[csf("quantity")];
				$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["outside_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
			}
		}
		else
		{
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening"] += $val[csf("quantity")];
			$issue_return_data[$val[csf("booking_no")]][$issRtnRef_str]["opening_amount"] +=$val[csf("quantity")]*$val[csf("order_rate")];
		}
	}

	if($report_type == 2)
	{
		$issue_select = " c.floor_id, c.room, c.rack, c.self,";
		$issue_group = " c.floor_id, c.room, c.rack, c.self,";
	}

	/*$issue_sql = sql_select("select a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(g.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c  left join inv_mrr_wise_issue_details f on c.id = f.issue_trans_id and f.entry_form=18 and f.status_active =1 left join inv_transaction g on f.recv_trans_id = g.id , product_details_master d, pro_batch_create_mst e  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_group c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(g.order_rate,2)");*/

	$issue_sql = sql_select("select a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g  where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_group c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2)");

	foreach ($issue_sql as $val)
	{
		$issRef_str="";
		if($report_type == 2)
		{
			$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$issRef_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		
		if($transaction_date >= $date_frm)
		{
			if($val[csf("issue_purpose")] == 9)
			{
				if($val[csf("knit_dye_source")] == 1)
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_inside"] += $val[csf("cons_quantity")];
				}
				else
				{
					$issue_data[$val[csf("booking_no")]][$issRef_str]["cutting_outside"] += $val[csf("cons_quantity")];
				}
			}
			else
			{
				$issue_data[$val[csf("booking_no")]][$issRef_str]["other_issue"] += $val[csf("cons_quantity")];
			}
			$issue_data[$val[csf("booking_no")]][$issRef_str]["issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue"] += $val[csf("cons_quantity")];
			$issue_data[$val[csf("booking_no")]][$issRef_str]["opening_issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}
	/*echo "<pre>";
	print_r($issue_data);
	die;*/
	if($report_type == 2){
		$rcv_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$rcvRtnSql = sql_select("select c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");

	foreach ($rcvRtnSql as $val)
	{
		if($report_type == 2)
		{
			$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$rcvRtn_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
		}
		

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$val[csf("booking_no")]][$rcvRtn_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}

	if($report_type == 2)
	{
		$trans_out_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$transOutSql = sql_select("select c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e, GBL_TEMP_ENGINE g where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2  and e.id=g.ref_val and g.user_id=$user_id and g.entry_form=991 and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)");

	foreach ($transOutSql as $val)
	{
		if($report_type == 2)
		{
			$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")]."*".$val[csf("floor_id")]."*".$val[csf("room")]."*".$val[csf("rack")]."*".$val[csf("self")];
		}
		else
		{
			$transOut_str = $val[csf("prod_id")]."*".$val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("gsm")]."*".$val[csf("dia_width")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
		}

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$val[csf("booking_no")]][$transOut_str]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
	}

	//if($all_po_id_cond_2!="")
	if(!empty($all_po_id_arr))
	{
		//$consumption_sql = sql_select("select c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition, b.color_number_id, a.costing_per, sum(b.requirment) as requirment, count(b.gmts_sizes) as gmts_sizes from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 $all_po_id_cond_2 group by c.job_no,c.body_part_id, c.lib_yarn_count_deter_id, c.construction, c.composition,b.color_number_id,a.costing_per");

		$consumption_sql = sql_select("SELECT c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition, b.color_number_id, a.costing_per,  sum(b.requirment) as requirment, count(b.gmts_sizes) as gmts_sizes from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b, GBL_TEMP_ENGINE g where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 and c.color_size_sensitive !=3 and b.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=990 group by c.job_no,c.body_part_id, c.lib_yarn_count_deter_id, c.construction, c.composition,b.color_number_id, a.costing_per 
		union all 
		select c.job_no,c.body_part_id,c.lib_yarn_count_deter_id, c.construction, c.composition, d.contrast_color_id as color_number_id, a.costing_per, sum(b.requirment) as requirment, count(b.gmts_sizes) as gmts_sizes from wo_pre_cost_mst a, wo_pre_cost_fabric_cost_dtls c, wo_pre_cos_fab_co_avg_con_dtls b ,wo_pre_cos_fab_co_color_dtls d, GBL_TEMP_ENGINE g where a.job_no = c.job_no and b.job_no=c.job_no and c.id = b.pre_cost_fabric_cost_dtls_id and c.id = d.pre_cost_fabric_cost_dtls_id and b.pre_cost_fabric_cost_dtls_id=d.pre_cost_fabric_cost_dtls_id and b.color_number_id= d.gmts_color_id and d.status_active=1 and c.color_size_sensitive=3 and c.fab_nature_id=2 and c.status_active =1 and b.status_active=1 and b.po_break_down_id=g.ref_val and g.user_id=$user_id and g.entry_form=990 group by c.job_no,c.body_part_id, c.lib_yarn_count_deter_id, c.construction, c.composition, d.contrast_color_id, a.costing_per");  //$all_po_id_cond_2

		foreach ($consumption_sql as $val)
		{
			if($val[csf("costing_per")] == 1){
				$multipy_with = 1;
			}elseif ($val[csf("costing_per")] == 2) {
				$multipy_with = 12;
			}elseif ($val[csf("costing_per")] == 3) {
				$multipy_with = .5;
			}elseif ($val[csf("costing_per")] == 4) {
				$multipy_with = .3333;
			}elseif ($val[csf("costing_per")] == 5) {
				$multipy_with = .25;
			}

			$consumption_arr[$val[csf("job_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("color_number_id")]] += $multipy_with*($val[csf("requirment")]/$val[csf("gmts_sizes")]);
		}
		unset($consumption_sql);
	}

    /*echo "<pre>";
    print_r($consumption_arr);
    die;*/

    $composition_arr=array();
    $sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ";
    $data_deter=sql_select($sql_deter);

    if(count($data_deter)>0)
    {
    	foreach( $data_deter as $row )
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

    if(!empty($all_prod_id))
    {
    	/*$all_prod_ids=implode(",",$all_prod_id);
    	$all_prod_id_cond=""; $prodCond="";
    	if($db_type==2 && count($all_prod_id)>999)
    	{
    		$all_prod_id_chunk=array_chunk($all_prod_id,999) ;
    		foreach($all_prod_id_chunk as $chunk_arr)
    		{
    			$chunk_arr_value=implode(",",$chunk_arr);
    			$prodCond.="  a.prod_id in($chunk_arr_value) or ";
    		}

    		$all_prod_id_cond.=" and (".chop($prodCond,'or ').")";
    	}
    	else
    	{
    		$all_prod_id_cond=" and a.prod_id in($all_prod_ids)";
    	}
		*/

		fnc_tempenginepo("GBL_TEMP_ENGINE", $user_id, 992, 1,$all_prod_id, $empty_arr);
		/* foreach ($all_prod_id as $prodVal) 
		{
			$rID4=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,$prodVal)");
		}

		if($rID4)
		{
			oci_commit($con);
		} */

    	$transaction_date_array=array();
    	//if($all_prod_id_cond!=""){
		if(!empty($all_prod_id)){
    		$sql_date="SELECT c.booking_no, a.prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date from inv_transaction a,pro_batch_create_mst c, GBL_TEMP_ENGINE g where a.pi_wo_batch_no=c.id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.item_category=2 and a.prod_id=g.ref_val and g.user_id=$user_id and g.entry_form=992 group by c.booking_no,a.prod_id"; //$all_prod_id_cond

    		$sql_date_result=sql_select($sql_date);
    		foreach( $sql_date_result as $row )
    		{
    			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['min_date']=$row[csf('min_date')];
    			$transaction_date_array[$row[csf('booking_no')]][$row[csf('prod_id')]]['max_date']=$row[csf('max_date')];
    		}
    		unset($sql_date_result);
    	}
    }

	$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id");
	//$r_id4=execute_query("delete from tmp_poid where userid=$user_id");
	//$r_id5=execute_query("delete from tmp_batch_id where userid=$user_id");
	//$r_id6=execute_query("delete from tmp_prod_id where userid=$user_id");
	$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (990,991,992)");
	if($r_id3 && $r_id6)
	{
		oci_commit($con);
	}
	

    $floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
	/*echo "<pre>";
	print_r($data_array);
	die;*/
	if($report_type == 2){
		$table_width = "5870";
		$col_span = "31";
	}else{
		$table_width = "5670";
		$col_span = "29";
	}
	ob_start();
	 
				$i=1;
				foreach ($data_array as $uom => $uom_data)
				{
					$uom_total_booking_qty=$uom_total_opening_qnty=$uom_total_recv_qnty=$uom_total_inside_return=$uom_total_outside_return=$uom_total_trans_in_qty=$uom_total_tot_receive=$uom_total_total_issue=$uom_total_total_issue_amount=$uom_total_stock_qnty=$uom_total_stock_amount=0;
					foreach ($uom_data as $booking_no => $book_data)
					{
						foreach ($book_data as $prodStr => $row)
						{
						
							

							$ref_qnty_arr = explode("__", $row);
							$recv_qnty=$trans_out_qty=$trans_in_qty=$opening_recv=$opening_trans=0;
							$recv_amount=$opening_recv_amount=$trans_in_amount=$opening_trans_amount=0;
							$dia_width_types="";$pi_no=""; $lc_sc_no="";
							foreach ($ref_qnty_arr as $ref_qnty)
							{
								$ref_qnty = explode("*", $ref_qnty);
								if($ref_qnty[6] == 1)
								{
									if($ref_qnty[7]==1){
										$recv_qnty += $ref_qnty[0];
										$recv_amount += $ref_qnty[0]*$ref_qnty[1];
									}else{
										$opening_recv +=$ref_qnty[0];
										$opening_recv_amount +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								if($ref_qnty[6] == 5)
								{
									if($ref_qnty[7]==1){
										$trans_in_qty += $ref_qnty[0];
										$trans_in_amount += $ref_qnty[0]*$ref_qnty[1];
									}else{
										$opening_trans +=$ref_qnty[0];
										$opening_trans_amount +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								$dia_width_types .=$ref_qnty[4].",";

								if($ref_qnty[2]==1)
								{
									$pi_no .= $ref_qnty[3].",";
								}

								$lc_sc_no .= $ref_qnty[5].",";
							}

							$po_number 	= implode(",",array_unique(explode(",",chop($po_array[$booking_no][$prodStr]["po_no"],","))));
							$pi_no 	= implode(",",array_unique(explode(",",chop($pi_no,","))));
							$lc_sc_no 	= implode(",",array_unique(explode(",",chop($lc_sc_no,","))));
							$prodStrArr 	= explode("*", $prodStr);

							//echo $booking_no.'<br>';
							$company_name 	= $book_po_ref[$booking_no]["company_name"];
							// echo $company_name.'<br>';
							$buyer_name 	= $book_po_ref[$booking_no]["buyer_name"];
							$supplier 		= $book_po_ref[$booking_no]["supplier"];
							$first_date 	= $book_po_ref[$booking_no]["fs_date"];
							$last_date 		= $book_po_ref[$booking_no]["ls_date"];
							$job_arr 		= array_filter(array_unique(explode(",",chop($book_po_ref[$booking_no]["job_no"],","))));
							$job_quantity 	= ""; $consump_per_dzn="";
							foreach ($job_arr as $job)
							{
								$job_quantity += $job_qnty_arr[$job]["qnty"];
								$consump_per_dzn += $consumption_arr[$job][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]];
							}
							$job_nos = implode(",", $job_arr);

							$client_arr = array_unique(explode(",",chop($book_po_ref[$booking_no]["client_id"],",")));
							$client_nos="";
							foreach ($client_arr as $client_id)
							{
								$client_nos .= $buyer_arr[$client_id].",";
							}

							$season = array_unique(explode(",",chop($book_po_ref[$booking_no]["season"],",")));
							$season_nos="";
							foreach ($season as $s_id)
							{
								$season_nos .= $season_arr[$s_id].",";
							}

							$style_ref_no = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["style_ref_no"],","))));;
							$pay_mode_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no]["pay_mode"],","))));

							$booking_date = $book_po_ref[$booking_no]["booking_date"];
							$booking_type = $book_po_ref[$booking_no]["booking_type"];

							$dia_width_type_arr = array_filter(array_unique(explode(",",chop($dia_width_types,","))));

							$dia_width_type="";
							foreach ($dia_width_type_arr as $width_type)
							{
								$dia_width_type .= $fabric_typee[$width_type].",";
							}
							$dia_width_type = chop($dia_width_type,",");

							$booking_qnty 	= $book_po_ref[$booking_no][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]]["qnty"];
							$booking_amount = $book_po_ref[$booking_no][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]]["amount"];
							if($booking_qnty >0){
								$booking_rate 	= $booking_amount/$booking_qnty;
							}else{
								$booking_rate=0;
							}

							$color_type_nos = implode(",",array_unique(explode(",",chop($book_po_ref[$booking_no][$prodStrArr[2]][$prodStrArr[3]][$prodStrArr[6]]["color_type"],","))));

						

							if($report_type ==2)
							{
								$issRtnRef_str = $prodStrArr[0]."*".$prodStrArr[1]."*".$prodStrArr[2]."*".$prodStrArr[3]."*".$prodStrArr[4]."*".$prodStrArr[5]."*".$prodStrArr[6]."*".$prodStrArr[7]."*".$prodStrArr[8]."*".$prodStrArr[9]."*".$prodStrArr[10]."*".$prodStrArr[11];
							}
							else
							{
								$issRtnRef_str = $prodStrArr[0]."*".$prodStrArr[1]."*".$prodStrArr[2]."*".$prodStrArr[3]."*".$prodStrArr[4]."*".$prodStrArr[5]."*".$prodStrArr[6]."*".$prodStrArr[7];
							}
							
							


							$inside_return 			= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return"];
							$inside_return_amount 	= $issue_return_data[$booking_no][$issRtnRef_str]["inside_return_amount"];
							$outside_return 		= $issue_return_data[$booking_no][$issRtnRef_str]["outside_return"];
							$outside_return_amount  = $issue_return_data[$booking_no][$issRtnRef_str]["outside_return_amount"];
							$opening_iss_return 	= $issue_return_data[$booking_no][$issRtnRef_str]["opening"];
							$opening_iss_return_amount = $issue_return_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$tot_receive 			= $recv_qnty + $trans_in_qty + $inside_return + $outside_return;
							$tot_receive_amount 	= $recv_amount + $trans_in_amount + $inside_return_amount + $outside_return_amount;

							$tot_receive_rate=0;
							if($tot_receive>0)
							{
								$tot_receive_rate 	= $tot_receive_amount/$tot_receive;
							}
							$booking_balance_qnty 	= $booking_qnty- $tot_receive;
							$booking_balance_amount = $booking_balance_qnty*$booking_rate;

							$cutting_inside 		= $issue_data[$booking_no][$issRtnRef_str]["cutting_inside"];
							$cutting_outside 		= $issue_data[$booking_no][$issRtnRef_str]["cutting_outside"];
							$other_issue 			= $issue_data[$booking_no][$issRtnRef_str]["other_issue"];
							$issue_amount 			= $issue_data[$booking_no][$issRtnRef_str]["issue_amount"];
							$opening_issue 			= $issue_data[$booking_no][$issRtnRef_str]["opening_issue"];
							$opening_issue_amount 	= $issue_data[$booking_no][$issRtnRef_str]["opening_issue_amount"];

							$rcv_return_opening_qnty = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_qnty"];
							$rcv_return_opening_amount = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_amount"];
							$rcv_return_qnty  		= $rcv_return_data[$booking_no][$issRtnRef_str]["qnty"];
							$rcv_return_amount  	= $rcv_return_data[$booking_no][$issRtnRef_str]["amount"];

							$trans_out_amount  		= $trans_out_data[$booking_no][$issRtnRef_str]["amount"];
							$trans_out_qnty  		= $trans_out_data[$booking_no][$issRtnRef_str]["qnty"];
							$trans_out_opening_qnty = $trans_out_data[$booking_no][$issRtnRef_str]["opening_qnty"];
							$trans_out_opening_amount = $trans_out_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$total_issue  			= $cutting_inside + $cutting_outside + $other_issue + $rcv_return_qnty + $trans_out_qnty;
							

							$opening_title 	= "Receive:".$opening_recv ." + Transfer In:". $opening_trans ." + Issue Return:" . $opening_iss_return . "\n";
							$opening_title 	.= "Issue:".$opening_issue ." + Transfer Out:". $trans_out_opening_qnty ." + Receive Return:" . $rcv_return_opening_qnty;
							$opening_qnty 	= ($opening_recv + $opening_trans + $opening_iss_return) - ($opening_issue + $rcv_return_opening_qnty +$trans_out_opening_qnty);

							$stock_qnty 	= $opening_qnty + ($tot_receive - $total_issue);
							$stock_title 	= "Opening:".$opening_qnty ." + (Receive:". $tot_receive ."- Issue:". $total_issue.")";

							$booking_and_product_wise_quantity = $rate_arr_booking_and_product_wise[$booking_no][$prodStrArr[0]][$prodStrArr[1]]["quantity"];
							$booking_and_product_wise_amount = $rate_arr_booking_and_product_wise[$booking_no][$prodStrArr[0]][$prodStrArr[1]]["amount"];
							if($booking_and_product_wise_amount>0 && $booking_and_product_wise_quantity>0)
							{
								$booking_and_product_wise_rate = $booking_and_product_wise_amount/$booking_and_product_wise_quantity;
							}
							else
							{
								$booking_and_product_wise_rate = 0;
							}
							$tot_receive_rate =$booking_and_product_wise_rate;

							$opening_amount = ($opening_recv_amount+$opening_trans_amount) -($opening_issue_amount + $rcv_return_opening_amount);

							if($opening_qnty>0)
							{
								
							}

							if($tot_receive_rate ==0)
							{
								$tot_receive_rate =$opening_rate;
							}

							$tot_issue_rate = $tot_receive_rate;
							$total_issue_amount = $total_issue * $tot_issue_rate;

						

							if(number_format($stock_qnty,2,".","") == "-0.00")
							{
								$stock_qnty=0;
							}

							$stock_rate = $tot_receive_rate;
							$stock_amount = $stock_qnty * $stock_rate;

							$daysOnHand = datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStrArr[0]]['max_date'],'','',1),date("Y-m-d"));
							$ageOfDays 	= datediff("d",change_date_format($transaction_date_array[$booking_no][$prodStrArr[0]]['min_date'],'','',1),date("Y-m-d"));

							//$possible_cut_piece = ($consump_per_dzn/12) * ($recv_qnty + $trans_in_qty);
							if(($consump_per_dzn/12) > 0)
							{
								$possible_cut_piece = $stock_qnty/($consump_per_dzn/12);
							}

							if ((($get_upto == 1 && $ageOfDays > $txt_days) || ($get_upto == 2 && $ageOfDays < $txt_days) || ($get_upto == 3 && $ageOfDays >= $txt_days) || ($get_upto == 4 && $ageOfDays <= $txt_days) || ($get_upto == 5 && $ageOfDays == $txt_days) || $get_upto == 0) && (($get_upto_qnty == 1 && $stock_qnty > $txt_qnty) || ($get_upto_qnty == 2 && $stock_qnty < $txt_qnty) || ($get_upto_qnty == 3 && $stock_qnty >= $txt_qnty) || ($get_upto_qnty == 4 && $stock_qnty <= $txt_qnty) || ($get_upto_qnty == 5 && $stock_qnty == $txt_qnty) || $get_upto_qnty == 0))
							{
								
								if($cbo_value_with==1)
								{
								
									
									$buyer_data_arr[qty][$buyer_name][$prodStrArr[7]]+=number_format($stock_qnty,2,".","")*1;
									$buyer_data_arr[val][$buyer_name][$prodStrArr[7]]+=number_format($stock_amount,2,".","")*1;

									$uom_total_stock_qnty+=number_format($stock_qnty,2,".","")*1;
									$uom_total_stock_amount+=number_format($stock_amount,2,".","")*1;

								}
							}
						}
					}

					$UOMWiseStockTotalArr[qty][$unit_of_measurement[$prodStrArr[7]]]=round($uom_total_stock_qnty,2);
					$UOMWiseStockTotalArr[val][$unit_of_measurement[$prodStrArr[7]]]=round($uom_total_stock_amount,2);
				
				}
				?>
			 
	<?
	
 
	$data = json_encode($UOMWiseStockTotalArr);
	$filename="tmp/urmi.txt";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$data);
	fclose($filename);
	
	$data = json_encode($buyer_data_arr);
	$filename="tmp/urmi_buyer.txt";
	$create_new_doc = fopen($filename, 'w');
	fwrite($create_new_doc,$data);
	fclose($filename);
}



?>
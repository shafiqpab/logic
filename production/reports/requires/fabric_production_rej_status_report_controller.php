<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

if($db_type==0)
{
	$fabric_desc_details=return_library_array( "select job_no, group_concat(distinct(fabric_description)) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}
else
{
	$fabric_desc_details=return_library_array( "select job_no, LISTAGG(cast(fabric_description as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as fabric_description from wo_pre_cost_fabric_cost_dtls group by job_no", "job_no", "fabric_description");
}

$product_details=return_library_array( "select id, product_name_details from product_details_master", "id", "product_name_details");
$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

$costing_per_id_library=array(); $costing_date_library=array();
$costing_sql=sql_select("select job_no, costing_per, costing_date from wo_pre_cost_mst");
foreach($costing_sql as $row)
{
	$costing_per_id_library[$row[csf('job_no')]]=$row[csf('costing_per')]; 
	$costing_date_library[$row[csf('job_no')]]=$row[csf('costing_date')];
}

$receive_basis=array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan");//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
 	if($template==1)
	{
		$type = str_replace("'","",$cbo_type);
		$company_name= str_replace("'","",$cbo_company_name);
		
		//if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
		if(str_replace("'","",$cbo_buyer_name)==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		}
		
		$cbo_discrepancy=str_replace("'","",trim($cbo_discrepancy));
		
		if($cbo_discrepancy==0) $discrepancy_td_color=""; else $discrepancy_td_color="#FF4F4F";
		
		$txt_search_string=str_replace("'","",$txt_search_string);
		
		if(trim($txt_search_string)!="") $search_string="%".trim($txt_search_string)."%"; else $search_string="%%";
		
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		
		if($start_date!="" && $end_date!="")
		{
			$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else
			$str_cond="";
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		$job_no_cond="";
		if(trim($txt_job_no)!="")
		{
			$job_no=trim($txt_job_no); 
			$job_no_cond=" and a.job_no_prefix_num=$job_no";
		}
		
		$cbo_year=str_replace("'","",$cbo_year);
		if(trim($cbo_year)!=0) 
		{
			if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond="";
		}
		else $year_cond="";
		
		$cbo_shipping_status=str_replace("'","",$cbo_shipping_status);
		if(trim($cbo_shipping_status)==0) $shipping_status="%%"; else $shipping_status=$cbo_shipping_status;
		
		$txt_fab_color=str_replace("'","",$txt_fab_color);
		if(trim($txt_fab_color)!="") $fab_color="%".trim($txt_fab_color)."%"; else $fab_color="%%";
		
		$start_date_po=str_replace("'","",trim($txt_date_from_po));
		$end_date_po=str_replace("'","",trim($txt_date_to_po));
		
		if($end_date_po=="") 
			$end_date_po=$start_date_po; 
		else 
			$end_date_po=$end_date_po;
		
		if($start_date_po!="" && $end_date_po!="")
		{
			if($db_type==0)
			{
				$str_cond_insert=" and b.insert_date between '".$start_date_po."' and '".$end_date_po." 23:59:59'";
			}
			else
			{
				$str_cond_insert=" and b.insert_date between '".$start_date_po."' and '".$end_date_po." 11:59:59 PM'";
			}
		}
		else
			$str_cond_insert="";
		
		if($txt_fab_color=="")
		{
			$color_cond="";	
			$color_cond_prop="";	
		}
		else
		{
			if($db_type==0)
			{
				$color_id=return_field_value("group_concat(id) as color_id","lib_color","color_name like '$fab_color'","color_id");
			}
			else
			{
				$color_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as color_id","lib_color","color_name like '$fab_color'","color_id");
			}
			if($color_id=="") 
			{
				$color_cond_search=""; 
				$color_cond_prop=""; 
			}
			else
			{
				$color_cond_search=" and b.fabric_color_id in ($color_id)";
				$color_cond_prop=" and color_id in ($color_id)";
			}
		}
		
		$dataArrayYarn=array(); $dataArrayYarnIssue=array(); $greyPurchaseQntyArray=array();
		$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
		$resultYarn=sql_select($yarn_sql);
		foreach($resultYarn as $yarnRow)
		{
			$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')].",";
		}
		
		$sql_yarn_iss="select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
				sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
				sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
				from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
		$dataArrayIssue=sql_select($sql_yarn_iss);
		foreach($dataArrayIssue as $row_yarn_iss)
		{
			$dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]].=$row_yarn_iss[csf('yarn_count_id')]."**".$row_yarn_iss[csf('yarn_comp_type1st')]."**".$row_yarn_iss[csf('yarn_comp_percent1st')]."**".$row_yarn_iss[csf('yarn_comp_type2nd')]."**".$row_yarn_iss[csf('yarn_comp_percent2nd')]."**".$row_yarn_iss[csf('yarn_type')]."**".$row_yarn_iss[csf('issue_qnty')]."**".$row_yarn_iss[csf('return_qnty')].",";
		}
		
		$sql_grey_purchase="select c.po_breakdown_id, sum(c.quantity) as grey_purchase_qnty from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
		foreach($dataArrayGreyPurchase as $greyRow)
		{
			$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_purchase_qnty')];
		}
		
		$trans_qnty_arr=array(); $grey_receive_qnty_arr=array(); $grey_issue_qnty_arr=array();$grey_receive_return_qnty_arr=array(); $grey_issue_return_qnty_arr=array();
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN entry_form ='45' and trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,

								sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
								sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_roll_wise,
								sum(CASE WHEN entry_form ='51' and trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return,
								
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,11,13,16,45,51,61) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans']=$row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans']=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue')]+$row[csf('grey_issue_roll_wise')];
			
			$grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive_return')];//add by reza;
			$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue_return')];//add by reza;
		}
		
		$trans_qnty_fin_arr=array(); $finish_receive_qnty_arr=array(); $finish_purchase_qnty_arr=array(); $finish_issue_qnty_arr=array(); $finish_recv_rtn_qnty_arr=array(); $finish_issue_rtn_qnty_arr=array();
		$dataArrayTrans=sql_select("select po_breakdown_id, color_id, 
								sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
								sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise,
								sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS finish_purchase,
								sum(CASE WHEN entry_form ='18' THEN quantity ELSE 0 END) AS finish_issue,
								sum(CASE WHEN entry_form ='71' THEN quantity ELSE 0 END) AS finish_issue_roll_wise,
								sum(CASE WHEN entry_form ='46' and trans_type=3 THEN quantity ELSE 0 END) AS recv_rtn_qnty,
								sum(CASE WHEN entry_form ='52' and trans_type=4 THEN quantity ELSE 0 END) AS iss_retn_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,15,18,37,46,52,66,71) group by po_breakdown_id, color_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']=$row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')];
			$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_roll_wise')];
			$finish_issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_issue')]+$row[csf('finish_issue_roll_wise')];
			
			$finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('recv_rtn_qnty')];
			$finish_issue_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('iss_retn_qnty')];
		}
		
		$sql_fin_purchase="select c.po_breakdown_id, c.color_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id, c.color_id";
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]=$finRow[csf('finish_purchase')];
		}

		if($db_type==0)
		{
			$po_color_arr=return_library_array( "select po_breakdown_id, group_concat(distinct(color_id)) as color_id from order_wise_pro_details where entry_form in(7,18,37) and color_id<>0 $color_cond_prop group by po_breakdown_id", "po_breakdown_id", "color_id");
		}
		else
		{
			$po_color_arr=return_library_array( "select po_breakdown_id, LISTAGG(cast(color_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY color_id) color_id from order_wise_pro_details where entry_form in(7,18,37) and color_id<>0 $color_cond_prop group by po_breakdown_id", "po_breakdown_id", "color_id");
		}
		
		$batch_qnty_arr=return_library_array( "select b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.batch_against<>2 and a.entry_form=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.po_id", "po_id", "batch_qnty");
		
		//$dye_qnty_arr=return_library_array( "select b.po_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id='$key' and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.po_id", "po_id", "dye_qnty");
		
		$dye_qnty_arr=array();
		$sql_dye="select b.po_id, a.color_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.po_id, a.color_id";
		$resultDye=sql_select($sql_dye);
		foreach($resultDye as $dyeRow)
		{
			$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('color_id')]]=$dyeRow[csf('dye_qnty')];
		}
		
		$dataArrayWo=array();
		$sql_wo="select b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search group by b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id";
		$resultWo=sql_select($sql_wo);
		foreach($resultWo as $woRow)
		{
			$dataArrayWo[$woRow[csf('po_break_down_id')]].=$woRow[csf('id')]."**".$woRow[csf('booking_no')]."**".$woRow[csf('insert_date')]."**".$woRow[csf('item_category')]."**".$woRow[csf('fabric_source')]."**".$woRow[csf('company_id')]."**".$woRow[csf('booking_type')]."**".$woRow[csf('booking_no_prefix_num')]."**".$woRow[csf('job_no')]."**".$woRow[csf('is_short')]."**".$woRow[csf('is_approved')]."**".$woRow[csf('fabric_color_id')]."**".$woRow[csf('req_qnty')]."**".$woRow[csf('grey_req_qnty')].",";
		}
		
		$tot_order_qnty=0; $tot_mkt_required=0; $tot_yarn_issue_qnty=0; $tot_balance=0; $tot_fabric_req=0; $tot_grey_recv_qnty=0; $tot_grey_balance=0; $tot_grey_available=0; 
		$tot_grey_issue=0; $tot_batch_qnty=0; $tot_color_wise_req=0; $tot_dye_qnty=0; $tot_fabric_recv=0; $tot_fabric_purchase=0; $tot_fabric_balance=0; $tot_issue_to_cut_qnty=0;
		$tot_fabric_available=0; $tot_fabric_left_over=0;
		
		$buyer_name_array= array(); $grey_required_array= array(); $yarn_issue_array= array(); $grey_issue_array= array(); 
		$fin_fab_Requi_array= array(); $fin_fab_recei_array= array(); $issue_to_cut_array= array(); $yarn_balance_array= array(); 
		$grey_balance_array= array(); $fin_balance_array= array(); $knitted_array=array(); $dye_qnty_array=array(); $batch_qnty_array=array();

		if($type==1)
		{
			$table_width="3930"; $colspan="13";
			 $sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and b.po_number like '$search_string' and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $job_no_cond $str_cond $str_cond_insert $year_cond order by b.pub_shipment_date, b.id";	
		}
		else
		{
			$table_width="3680"; $colspan="10";
			if($db_type==0)
			{
				$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, group_concat(b.id) as po_id, group_concat(b.po_number) as po_number, sum(b.po_quantity) as po_qnty, sum(b.plan_cut) as plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.style_ref_no like '$search_string' and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $job_no_cond $str_cond $str_cond_insert $year_cond group by a.job_no";
			}
			else
			{
				$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, LISTAGG(cast(b.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id, LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_number, sum(b.po_quantity) as po_qnty, sum(b.plan_cut) as plan_cut from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.style_ref_no like '$search_string' and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $buyer_id_cond $job_no_cond $str_cond $str_cond_insert $year_cond group by a.job_no, a.company_name, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty";	
			}
		}
		//echo $sql;die;
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");
		ob_start();
		?>
        <fieldset style="width:<? echo $table_width+30; ?>px;">	
            <table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+26; ?>" style="font-size:16px"><strong><?php echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+26; ?>" style="font-size:16px"><strong><? if($start_date!="" && $end_date!="") echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header_1">
                <thead>
                    <tr>
                        <th colspan="<? echo $colspan; ?>">Order Details</th>
                        <th colspan="7">Yarn Status</th>
                        <th colspan="8">Grey Fabric Status</th>
                        <th colspan="11">Finish Fabric Status</th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="125">Main Fabric Booking No</th>
                        <th width="125">Sample Fabric Booking No</th>
                        <th width="100">Job Number</th>
                        <th width="120">Order Number</th>
                        <th width="80">Buyer Name</th>
                        <th width="130">Style Ref.</th>
                        <th width="140">Item Name</th>
                        <th width="100">Order Qnty</th>
                        <th width="80">Shipment Date</th>
                        <?
						if($type==1)
						{
						?>
                            <th width="80">PO Received Date</th>
                            <th width="80">PO Entry Date</th>
                            <th width="100">Shipping Status
                                <select name="cbo_shipping_status" id="cbo_shipping_status" class="combo_boxes" style="width:85%" onchange="fn_report_generated(2);">
                                    <?
                                    foreach($shipment_status as $key=>$value)
                                    {
                                    ?>
                                        <option value=<? echo $key; if ($key==$cbo_shipping_status){?> selected <?php }?>><? echo "$value" ?> </option>
                                    <?
                                    }
                                    ?>
                                </select> 
                            </th>
                        <?
						}
						?>
                        <th width="70">Count</th>
                        <th width="110">Composition</th>
                        <th width="80">Type</th>
                        <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                        <th width="100">Issued</th>
                        <th width="100">Net Transfer</th>
                        <th width="100">Balance<br/><font style="font-size:9px; font-weight:100">(Grey Req-Yarn Issue)</font></th>
                        <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="100">Grey Production</th>
                        <th width="100">Grey Recv./ Purchase</th>
                        <th width="100">Net Transfer</th>
                        <th width="100">Grey Available</th>
                        <th width="100">Grey Balance</th>
                        <th width="100">Grey Issue</th>
                        <th width="100">Batch Qnty</th>
                        <th width="100">Fabric Color
                        	<input type="text" name="txt_fab_color" onkeyup="show_inner_filter(event);" value="<? echo str_replace("'","",$txt_fab_color); ?>" id="txt_fab_color" class="text_boxes" style="width:85px" />
                        </th>
                        <th width="100">Required</th>
                        <th width="100">Dye Qnty</th>
                        <th width="100">Fabric Production</th>
                        <th width="100">Fabric Recv./ Purchase</th>
                        <th width="100">Net Transfer</th>
                        <th width="100">Finish Available</th>
                        <th width="100">Balance</th>
                        <th width="100">Issue to Cutting </th>
                        <th width="100">Fabric Stock/ Left Over</th>
                        <th>Fabric Description</th>
                    </tr>
                </thead>
            </table>
            <?
				$colspan_excel=$colspan+26;
				$html="<table>
							 <tr>
								<th colspan='$colspan_excel' align='center'>".$company_library[$company_name]."</th>
							 </tr>";
				if($start_date!="" && $end_date!="")
				{		 
					$html.="<tr>
								<th colspan='$colspan_excel' align='center'>From ". change_date_format($start_date). " To ". change_date_format($end_date)."</th>
						 	</tr>";
				}
				
				$html.="</table>
					<table border='1' rules='all'>
						<thead>
							<tr>
								<th colspan='$colspan'>Order Details</th>
								<th colspan='7'>Yarn Status</th>
								<th colspan='8'>Grey Fabric Status</th>
								<th colspan='11'>Finish Fabric Status</th>
							</tr>
							<tr>
								<th>SL</th>
								<th>Main Fabric Booking No</th>
								<th>Sample Fabric Booking No</th>
								<th>Job Number</th>
								<th>Order Number</th>
								<th>Buyer Name</th>
								<th>Style Ref.</th>
								<th>Item Name</th>
								<th>Order Qnty</th>
								<th>Shipment Date</th>";
				
				if($type==1)
				{				
					$html.="<th>PO Received Date</th>
							<th>Po Entry Date</th>
							<th>Shipping Status</th>";
				}
				
				$html.="<th>Count</th>
						<th>Composition</th>
						<th>Type</th>
						<th>Required<br/><font style='font-size:9px; font-weight:100'>(As Per Pre-Cost)</font></th>
						<th>Issued</th>
						<th>Net Transfer</th>
						<th>Balance<br/><font style='font-size:9px; font-weight:100'>(Grey Req-Yarn Issue)</font></th>
						<th>Required<br/><font style='font-size:9px; font-weight:100'>(As Per Booking)</font></th>
						<th>Grey Production</th>
						<th>Grey Recv./ Purchase</th>
						<th>Net Transfer</th>
						<th>Grey Available</th>
						<th>Grey Balance</th>
						<th>Grey Issue</th>
						<th>Batch Qnty</th>
						<th>Fabric Color</th>
						<th>Required</th>
						<th>Dye Qnty</th>
						<th>Fabric Production</th>
						<th>Fabric Recv./ Purchase</th>
						<th>Net Transfer</th>
						<th>Finish Available</th>
						<th>Balance</th>
						<th>Issue to Cutting</th>
						<th>Fabric Stock/ Left Over</th>
						<th>Fabric Description</th
					</tr>
				</thead>
				";	
						
				$html_short="<table width='1400'>
									 <tr>
										<th colspan='14' align='center'>".$company_library[$company_name]."</th>
									 </tr>
									 <tr>
										<th colspan='14' align='center'>From ". change_date_format($start_date). " To ". change_date_format($end_date)."</th>
									 </tr>
								</table>
								<table class='rpt_table' border='1' rules='all' width='100%'>
									<thead>
										<th>SL</th>
										<th>Main Fabric<br/> Booking No</th>
										<th>Sample Fabric<br/> Booking No</th>
										<th>Order Number</th>
										<th>Buyer Name</th>
										<th>Order Qnty.</th>
										<th>Shipment Date</th>
										<th>Yarn Issue</th>
										<th>Grey Req<br/> (As per Booking)</th>
										<th>Grey Knitted</th>
										<th>Fabric Color</th>
										<th>Dyeing Qnty</th>
										<th>Finish Fabric Qnty</th>
										<th>Issue to Cutting</th>
									</thead>
									";
			
			?>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:400px" id="scroll_body">
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <? 
				$nameArray=sql_select($sql);
				$k=1; $i=1; 
				if($type==1)
				{
					foreach($nameArray as $row)
					{
						
						$template_id=$template_id_arr[$row[csf('po_id')]];
						
						$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];

						$yarn_data_array=array(); $mkt_required_array=array(); $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array(); $s=1;
						$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
						foreach($dataYarn as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$copm_one_id=$yarnRow[1];
							$percent_one=$yarnRow[2];
							$copm_two_id=$yarnRow[3];
							$percent_two=$yarnRow[4];
							$type_id=$yarnRow[5];
							$qnty=$yarnRow[6];
							
							$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
							$mkt_required_array[$s]=$mkt_required;
							$job_mkt_required+=$mkt_required;
							
							$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][$s]=$yarn_type[$type_id];
							
							if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}

							$yarn_data_array['comp'][]=$compos;
							
							$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							
							$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
							$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
							
							$s++;
						}
						
						$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$row[csf('po_id')]],0,-1));
						foreach($dataYarnIssue as $yarnIssueRow)
						{
							$yarnIssueRow=explode("**",$yarnIssueRow);
							$yarn_count_id=$yarnIssueRow[0];
							$yarn_comp_type1st=$yarnIssueRow[1];
							$yarn_comp_percent1st=$yarnIssueRow[2];
							$yarn_comp_type2nd=$yarnIssueRow[3];
							$yarn_comp_percent2nd=$yarnIssueRow[4];
							$yarn_type_id=$yarnIssueRow[5];
							$issue_qnty=$yarnIssueRow[6];
							$return_qnty=$yarnIssueRow[7];
							
							if($yarn_comp_percent2nd!=0)
							{
								$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
							}
							else
							{
								$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
							}
					
							$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
							
							$net_issue_qnty=$issue_qnty-$return_qnty;
							$yarn_issued+=$net_issue_qnty;
							if(!in_array($desc,$yarn_desc_array))
							{
								$yarn_iss_qnty_array['not_req']+=$net_issue_qnty;
							}
							else
							{
								$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
							}
						}

						$grey_purchase_qnty=$greyPurchaseQntyArray[$row[csf('po_id')]]-$grey_receive_return_qnty_arr[$row[csf('po_id')]];
						$grey_recv_qnty=$grey_receive_qnty_arr[$row[csf('po_id')]];
						$grey_fabric_issue=$grey_issue_qnty_arr[$row[csf('po_id')]]-$grey_issue_return_qnty_arr[$row[csf('po_id')]];
						
						if(($cbo_discrepancy==1 && $grey_recv_qnty>$yarn_issued) || ($cbo_discrepancy==0))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$buyer_name_array[$row[csf('buyer_name')]]=$buyer_short_name_library[$row[csf('buyer_name')]];
							
							$booking_array=array(); $color_data_array=array();
							$required_qnty=0; $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel='';
							$dataArray=array_filter(explode(",",substr($dataArrayWo[$row[csf('po_id')]],0,-1)));
							if(count($dataArray)>0)
							{
								foreach($dataArray as $woRow)
								{
									$woRow=explode("**",$woRow);
									$id=$woRow[0];
									$booking_no=$woRow[1];
									$insert_date=$woRow[2];
									$item_category=$woRow[3];
									$fabric_source=$woRow[4];
									$company_id=$woRow[5];
									$booking_type=$woRow[6];
									$booking_no_prefix_num=$woRow[7];
									$job_no=$woRow[8];
									$is_short=$woRow[9];
									$is_approved=$woRow[10];
									$fabric_color_id=$woRow[11];
									$req_qnty=$woRow[12];
									$grey_req_qnty=$woRow[13];
									
									$required_qnty+=$grey_req_qnty;
		
									if(!in_array($id,$booking_array))
									{
										$system_date=date('d-M-Y', strtotime($insert_date));
										
										if($booking_type==4)
										{
											$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
											$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
										}
										else
										{
											if($is_short==1) $pre="S"; else $pre="M"; 
											
											$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
											$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
										}
										
										$booking_array[]=$id;
									}
									$color_data_array[$fabric_color_id]+=$req_qnty;
								}
							}
							else
							{
								$main_booking.="No Booking";
								$main_booking_excel.="No Booking";
								$sample_booking.="No Booking";
								$sample_booking_excel.="No Booking";
							}
							
							if($main_booking=="")
							{
								$main_booking.="No Booking";
								$main_booking_excel.="No Booking";
							}
							
							if($sample_booking=="") 
							{
								$sample_booking.="No Booking";
								$sample_booking_excel.="No Booking";
							}
							
							$finish_color=array_unique(explode(",",$po_color_arr[$row[csf('po_id')]]));
							foreach($finish_color as $color_id)
							{
								if($color_id>0)
								{ 
									$color_data_array[$color_id]+=0;
								}
							}
							
							$yarn_issue_array[$row[csf('buyer_name')]]+=$yarn_issued;
							
							$grey_required_array[$row[csf('buyer_name')]]+=$required_qnty;

							$net_trans_yarn=$trans_qnty_arr[$row[csf('po_id')]]['yarn_trans'];
							$yarn_issue_array[$row[csf('buyer_name')]]+=$net_trans_yarn;
							
							$balance=$required_qnty-($yarn_issued+$net_trans_yarn);
							
							$yarn_balance_array[$row[csf('buyer_name')]]+=$balance;
							
							$knitted_array[$row[csf('buyer_name')]]+=$grey_recv_qnty+$grey_purchase_qnty;
							
							$net_trans_knit=$trans_qnty_arr[$row[csf('po_id')]]['knit_trans'];
							$knitted_array[$row[csf('buyer_name')]]+=$net_trans_knit;
							
							$grey_balance=$required_qnty-($grey_recv_qnty+$net_trans_knit+$grey_purchase_qnty);
							
							$grey_balance_array[$row[csf('buyer_name')]]+=$grey_balance;
							
							$grey_issue_array[$row[csf('buyer_name')]]+=$grey_fabric_issue;
							
							$batch_qnty=$batch_qnty_arr[$row[csf('po_id')]];
							$batch_qnty_array[$row[csf('buyer_name')]]+=$batch_qnty;

							$tot_order_qnty+=$order_qnty_in_pcs;
							$tot_mkt_required+=$job_mkt_required;
							$tot_yarn_issue_qnty+=$yarn_issued;
							$tot_fabric_req+=$required_qnty;
							$tot_balance+=$balance;
							$tot_grey_recv_qnty+=$grey_recv_qnty;
							$tot_grey_purchase_qnty+=$grey_purchase_qnty;
							$tot_grey_balance+=$grey_balance;
							$tot_grey_issue+=$grey_fabric_issue;
							$tot_batch_qnty+=$batch_qnty;
							
							$grey_available=$grey_recv_qnty+$grey_purchase_qnty+$net_trans_knit;
							$tot_grey_available+=$grey_available;
					
							if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';
							
							$po_entry_date=date('d-m-Y', strtotime($row[csf('insert_date')]));
							$costing_date=$costing_date_library[$row[csf('job_no')]];
							
							$tot_color=count($color_data_array);	
							
							if($tot_color>0)
							{
								$z=1;
								foreach($color_data_array as $key=>$value)
								{
									if($z==1) 
									{
										$display_font_color="";
										$font_end="";
									}
									else 
									{
										$display_font_color="<font style='display:none' color='$bgcolor'>";
										$font_end="</font>";
									}
									
									if($z==1)
									{
										$html.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='center'>".$row[csf('job_no')]."</td>
												<td align='left'>".$row[csf('po_number')]."</td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('style_ref_no')]."</td>
												<td align='left'>".$gmts_item."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>
												<td align='center'>".change_date_format($row[csf('po_received_date')])."</td>
												<td align='center'>".$po_entry_date."</td>
												<td>".$shipment_status[$row[csf('shiping_status')]]."</td>";
										
										$html_short.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$row[csf('po_number')]."</td>
													<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>";			
										
									}
									else
									{
										$html.="<tr bgcolor='".$bgcolor."'>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>";
												
										$html_short.="<tr bgcolor='".$bgcolor."'>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>";		
									}
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
										<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
										<td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
										<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
										<td width="100" align="center"><? echo $display_font_color.$row[csf('job_no')].$font_end; ?></td>
										<td width="120">
                                        	<p>
												<a href='#report_details' onclick="progress_comment_popup('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('po_id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>');"><? echo $display_font_color.$row[csf('po_number')].$font_end;  ?></a>
                                        	</p>
                                        </td>
										<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$row[csf('buyer_name')]].$font_end; ?></p></td>
										<td width="130"><p><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></p></td>
										<td width="140"><p><? echo $display_font_color.$gmts_item.$font_end; ?></p></td>
										<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
										<td width="80" align="center"><? echo $display_font_color.change_date_format($row[csf('pub_shipment_date')]).$font_end; ?></td>
										<td width="80" align="center"><? echo $display_font_color.change_date_format($row[csf('po_received_date')]).$font_end; ?></td>
										<td width="80" align="center"><? echo $display_font_color.$po_entry_date.$font_end; ?></td>
										<td width="100" align="center"><? echo $display_font_color.$shipment_status[$row[csf('shiping_status')]].$font_end; ?></td>
										<td width="70">
											<? 
												 $html.="<td>"; $d=1;
												 foreach($yarn_data_array['count'] as $yarn_count_value)
												 {
													if($d!=1)
													{
														echo $display_font_color."<hr/>".$font_end;
														if($z==1) $html.="<hr/>";
													}
													
													echo $display_font_color.$yarn_count_value.$font_end;
													if($z==1) $html.=$yarn_count_value;
												 $d++;
												 }
												 
												 $html.="</td><td>";
											?>
										</td>
										<td width="110" style="word-break:break-all;">
											<p>
												<? 
													 $d=1;
													 foreach($yarn_data_array['comp'] as $yarn_composition_value)
													 {
														if($d!=1)
														{
															echo $display_font_color."<hr/>".$font_end;
															if($z==1) $html.="<hr/>";
														}
														echo $display_font_color.$yarn_composition_value.$font_end;
														if($z==1) $html.=$yarn_composition_value;
													 $d++;
													 }
													 
													 $html.="</td><td>";
												?>
											</p>
										</td>
										<td width="80">
											<p>
												<? 
													 $d=1;
													 foreach($yarn_data_array['type'] as $yarn_type_value)
													 {
														if($d!=1)
														{
															echo $display_font_color."<hr/>".$font_end;
															if($z==1) $html.="<hr/>";
														}
														
														echo $display_font_color.$yarn_type_value.$font_end; 
														if($z==1) $html.=$yarn_type_value;
													 $d++;
													 }
													 
													 $html.="</td><td>"; 
												?>
											</p>
										</td>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
													echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
													$d=1; 
													foreach($mkt_required_array as $mkt_required_value)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}
														
														$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
														
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
													<?
													$html.=number_format($mkt_required_value,2);
													$d++;
													}
												}
												
												$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>";
											?>
										</td>
										<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
											<? 
												if($z==1)
												{
													echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
													$d=1;
													foreach($yarn_desc_array as $yarn_desc)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
															$html_short.="<hr/>";
														}
														
														$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
														$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
														
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
														<?
														$html.=number_format($yarn_iss_qnty,2);
														$html_short.=number_format($yarn_iss_qnty,2);
														$d++;
													}
													
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
														$html_short.="<hr/>";
													}
													
													$yarn_desc=join(",",$yarn_desc_array);
													
													$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
													
													$html.=number_format($iss_qnty_not_req,2);
													$html_short.=number_format($iss_qnty_not_req,2);
													?>
													<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
												<?
												}
												?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
										<? 
											if($z==1) 
											{
											?>
                                            	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
                                            <?	
												$html.=number_format($net_trans_yarn,2); 
												$tot_net_trans_yarn_qnty+=$net_trans_yarn;
											}
										?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
										<? 
											if($z==1) 
											{
												echo number_format($balance,2,'.','');
												$html.=number_format($balance,2); 
											}
										?>
										</td>
										<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_short.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
										<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>">
										<? 
											if($z==1) 
											{
												echo number_format($required_qnty,2,'.',''); 
												$html.=number_format($required_qnty,2);
												$html_short.=number_format($required_qnty,2);
											}
										?>
										</td>
										<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="</td><td>"; ?>
										<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($grey_recv_qnty,2);
													$html_short.=number_format($grey_recv_qnty,2);
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($grey_purchase_qnty,2);
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
										<? 
											if($z==1) 
											{
											?>
                                            	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
                                            <?
												$html.=number_format($net_trans_knit,2); 
												$tot_net_trans_knit_qnty+=$net_trans_knit;
											}
										?>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
										<?
											if($z==1) 
											{
												echo number_format($grey_available,2,'.','');
												$html.=number_format($grey_available,2);
											}
										?>
										</td>
										<? $html.="</td><td>"; $html_short.="</td>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
													echo number_format($grey_balance,2,'.',''); 
													$html.=number_format($grey_balance,2);
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); ?></a>
												<?
													$html.=number_format($grey_fabric_issue,2);
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','batch_qnty','')"><? echo number_format($batch_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($batch_qnty,2);
												}
											?>
										</td>
										<? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="<td bgcolor='#FF9BFF'>"; ?>
										<td width="100" align="center" bgcolor="#FF9BFF">
											<p>
												<? 
													if($key==0)
													{
														echo "-";
														$html.="-"; $html_short.="-";
													}
													else
													{ 
														echo $color_array[$key]; 
														$html.=$color_array[$key]; $html_short.=$color_array[$key];
													}
												
												?>
											</p>
										</td>
										<? $html.="</td><td>"; $html_short.="</td>"; ?>
										<td width="100" align="right">
											<? 
												echo number_format($value,2,'.','');
												$html.=number_format($value,2);
												
												$fin_fab_Requi_array[$row[csf('buyer_name')]]+=$value;
												$tot_color_wise_req+=$value; 
											?>
										</td>
										<? 
											$html.="</td><td>"; $html_short.="<td>"; 
											
											$fab_recv_qnty=$finish_receive_qnty_arr[$row[csf('po_id')]][$key];
											$fab_purchase_qnty=$finish_purchase_qnty_arr[$row[csf('po_id')]][$key]-$finish_recv_rtn_qnty_arr[$row[csf('po_id')]][$key];
											$issue_to_cut_qnty=$finish_issue_qnty_arr[$row[csf('po_id')]][$key]-$finish_issue_rtn_qnty_arr[$row[csf('po_id')]][$key];
											$dye_qnty=$dye_qnty_arr[$row[csf('po_id')]][$key];
										?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','dye_qnty','<? echo $key; ?>')"><? echo number_format($dye_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($dye_qnty,2);
												$html_short.=number_format($dye_qnty,2);
												
												$dye_qnty_array[$row[csf('buyer_name')]]+=$dye_qnty;
												$tot_dye_qnty+=$dye_qnty; 
											?>
										</td>
										<? $html.="</td><td>"; $html_short.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_recv_qnty,2);
												$html_short.=number_format($fab_recv_qnty,2);
												
												$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_recv_qnty;
												$tot_fabric_recv+=$fab_recv_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format(($fab_purchase_qnty),2,'.',''); ?></a>
											<?
												$html.=number_format(($fab_purchase_qnty),2);
												
												$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
												$tot_fabric_purchase+=($fab_purchase_qnty);
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? 
												$net_trans_finish=$trans_qnty_fin_arr[$row[csf('po_id')]][$key]['trans'];
												$fin_fab_recei_array[$row[csf('buyer_name')]]+=$net_trans_finish;
											?>
                                            	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','finish_trans','<? echo $key; ?>')"><? echo number_format($net_trans_finish,2,'.','');  ?></a>
                                            <?	
												$html.=number_format($net_trans_finish,2);
												$tot_net_trans_finish_qnty+=$net_trans_finish; 
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$fabric_available=$fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish;
												echo number_format($fabric_available,2,'.',''); 
												$html.=number_format($fabric_available,2);
												$tot_fabric_available+=$fabric_available;
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$fabric_balance=$value-($fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish);
												echo number_format($fabric_balance,2,'.',''); 
												$html.=number_format($fabric_balance,2);
												
												$fin_balance_array[$row[csf('buyer_name')]]+=$fabric_balance;
												$tot_fabric_balance+=$fabric_balance;
											?>
										</td>
										<? $html.="</td><td>"; $html_short.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($issue_to_cut_qnty,2);
												$html_short.=number_format($issue_to_cut_qnty,2);
												
												$issue_to_cut_array[$row[csf('buyer_name')]]+=$issue_to_cut_qnty;
												$tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$fabric_left_over=$fabric_available-$issue_to_cut_qnty;
												echo number_format($fabric_left_over,2,'.',''); 
												$html.=number_format($fabric_left_over,2);
												$tot_fabric_left_over+=$fabric_left_over;
											?>
										</td>
										<td> 
											<p>
												<? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
											</p>
										</td>
									</tr>
								<?	
									if($z==1) $html.="</td><td>".join(",<br>",array_unique($fabric_desc))."</td></tr>"; else $html.="</td></tr>"; 
									$html_short.="</td></tr>"; 
								$z++;
								$k++;
								}
							}
							else
							{ 
								$html.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='center'>".$row[csf('job_no')]."</td>
												<td align='left'>".$row[csf('po_number')]."</td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('style_ref_no')]."</td>
												<td align='left'>".$gmts_item."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>
												<td align='center'>".change_date_format($row[csf('po_received_date')])."</td>
												<td align='center'>".$po_entry_date."</td>
												<td>".$shipment_status[$row[csf('shiping_status')]]."</td>";
								
								$html_short.="<tr bgcolor='".$bgcolor."'>
											<td align='left'>".$i."</td>
											<td align='left'>".$main_booking_excel."</td>
											<td align='left'>".$sample_booking_excel."</td>
											<td align='left'>".$row[csf('po_number')]."</td>
											<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
											<td align='right'>".$order_qnty_in_pcs."</td>
											<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>";
														
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="125"><? echo $main_booking; ?></td>
									<td width="125"><? echo $sample_booking; ?></td>
									<td width="100" align="center"><? echo $row[csf('job_no')]; ?></td>
									<td width="120">
                                    	<p>
                                            <a href='#report_details' onclick="progress_comment_popup('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('po_id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>');"><? echo $display_font_color.$row[csf('po_number')].$font_end;  ?></a>
                                            
                                    	</p>
                                        
                                    </td>
									<td width="80"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
									<td width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
									<td width="140"><p><? echo $gmts_item; ?></p></td>
									<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
									<td width="80" align="center"><? echo $po_entry_date; ?></td>
									<td width="100" align="center"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
									<td width="70">
										<? 
											 $html.="<td>"; $d=1;
											 foreach($yarn_data_array['count'] as $yarn_count_value)
											 {
												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
												}
												
												echo $yarn_count_value; 
												$html.=$yarn_count_value;
												
											 $d++;
											 }
											 
											 $html.="</td><td>";
										?>
									</td>
									<td width="110" style="word-break:break-all;">
										<p>
											<? 
												 $d=1;
												 foreach($yarn_data_array['comp'] as $yarn_composition_value)
												 {
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}
													
													echo $yarn_composition_value; 
													$html.=$yarn_composition_value;
													
												 $d++;
												 }
												 
												 $html.="</td><td>";
											?>
										</p>
									</td>
									<td width="80">
										<p>
											<?
												 $d=1;
												 foreach($yarn_data_array['type'] as $yarn_type_value)
												 {
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}
													
													echo $yarn_type_value; 
													$html.=$yarn_type_value;
													
												 $d++;
												 }
												 
												 $html.="</td><td>"; 
											?>
										</p>
									</td>
									<td width="100" align="right">
										<? 
											echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
											$d=1;
											foreach($mkt_required_array as $mkt_required_value)
											{
												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
												}
												
												$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
												
												?>
												<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
											<?
											$html.=number_format($mkt_required_value,2);
											$d++;
											}
											
											$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>";
										?>
									</td>
									<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
										<? 
											echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
											$d=1;
											foreach($yarn_desc_array as $yarn_desc)
											{
												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
													$html_short.="<hr/>";
												}
												
												$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
												$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
												
												?>
												<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
												<?
												$html.=number_format($yarn_iss_qnty,2);
												$html_short.=number_format($yarn_iss_qnty,2);
												$d++;
											}
											
											if($d!=1)
											{
												echo "<hr/>";
												$html.="<hr/>";
												$html_short.="<hr/>";
											}
											
											$yarn_desc=join(",",$yarn_desc_array);
											
											$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
											
											$html.=number_format($iss_qnty_not_req,2);
											$html_short.=number_format($iss_qnty_not_req,2);
											?>
											<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
									</td>
									<? $html.="</td><td>"; ?>
									<td width="100" align="right">
                                         <a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
										<? 
											$html.=number_format($net_trans_yarn,2); 
											$tot_net_trans_yarn_qnty+=$net_trans_yarn;
										?>
									</td>
									<td width="100" align="right">
										<? 
											echo number_format($balance,2,'.','');
										?>
									</td>
									<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); ?></td>
									<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); ?></a></td>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a></td>
									<td width="100" align="right">
                                    	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
										<? 
											$tot_net_trans_knit_qnty+=$net_trans_knit;
										?>
									</td>
                                    <td width="100" align="right">
										<?
                                            echo number_format($grey_available,2,'.','');
                                        ?>
                                    </td>
									<td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); ?></td>
									<td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); ?></a>
									</td>
									<td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','batch_qnty','')"><? echo number_format($batch_qnty,2,'.',''); ?></a></td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
									<td> 
										<p>
											<? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo join(",<br>",array_unique($fabric_desc)); ?>
										</p>
									</td>
								</tr>
								<?	
									$html.="</td><td>".number_format($balance,2)."</td>
									<td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
									<td bgcolor='$discrepancy_td_color'>".number_format($grey_recv_qnty,2)."</td>
									<td>".number_format($grey_purchase_qnty,2)."</td>
									<td>".number_format($net_trans_knit,2)."</td>
									<td>".number_format($grey_available,2)."</td>
									<td>".number_format($grey_balance,2)."</td>
									<td>".number_format($grey_fabric_issue,2)."</td>
									<td>".number_format($batch_qnty,2)."</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td>".join(",<br>",array_unique($fabric_desc))."</td>
									</tr>
									";
									
									$html_short.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
									<td>".number_format($grey_recv_qnty,2)."</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									</tr>
									";
							$k++;
							}
						$i++;	
						}
					}// end main query
				}
				else
				{
					foreach($nameArray as $row)
					{
						$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						
						$gmts_item='';
						$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
						foreach($gmts_item_id as $item_id)
						{
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
						}
						
						$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$dzn_qnty=$dzn_qnty*$row[csf('ratio')];

						$yarn_data_array=array(); $mkt_required_array=array(); $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array(); $s=1;
						$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
						foreach($dataYarn as $yarnRow)
						{
							$yarnRow=explode("**",$yarnRow);
							$count_id=$yarnRow[0];
							$copm_one_id=$yarnRow[1];
							$percent_one=$yarnRow[2];
							$copm_two_id=$yarnRow[3];
							$percent_two=$yarnRow[4];
							$type_id=$yarnRow[5];
							$qnty=$yarnRow[6];
							
							$mkt_required=$plan_cut_qnty*($qnty/$dzn_qnty);
							$mkt_required_array[$s]=$mkt_required;
							$job_mkt_required+=$mkt_required;
							
							$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][$s]=$yarn_type[$type_id];
							
							if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}

							$yarn_data_array['comp'][]=$compos;
							
							$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							
							$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
							$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
							
							$s++;
						}
						
						$grey_purchase_qnty=0; $grey_recv_qnty=0; $grey_fabric_issue=0; $booking_data='';
						$job_po_id=explode(",",$row[csf('po_id')]);
						foreach($job_po_id as $po_id)
						{
							$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$po_id],0,-1));
							foreach($dataYarnIssue as $yarnIssueRow)
							{
								$yarnIssueRow=explode("**",$yarnIssueRow);
								$yarn_count_id=$yarnIssueRow[0];
								$yarn_comp_type1st=$yarnIssueRow[1];
								$yarn_comp_percent1st=$yarnIssueRow[2];
								$yarn_comp_type2nd=$yarnIssueRow[3];
								$yarn_comp_percent2nd=$yarnIssueRow[4];
								$yarn_type_id=$yarnIssueRow[5];
								$issue_qnty=$yarnIssueRow[6];
								$return_qnty=$yarnIssueRow[7];
								
								if($yarn_comp_percent2nd!=0)
								{
									$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." %"." ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
								}
								else
								{
									$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." %"." ".$composition[$yarn_comp_type2nd];
								}
						
								$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
								
								$net_issue_qnty=$issue_qnty-$return_qnty;
								$yarn_issued+=$net_issue_qnty;
								if(!in_array($desc,$yarn_desc_array))
								{
									$yarn_iss_qnty_array['not_req']+=$net_issue_qnty;
								}
								else
								{
									$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
								}
							}
							
							$grey_purchase_qnty+=$greyPurchaseQntyArray[$po_id]-$grey_receive_return_qnty_arr[$po_id];
							$grey_recv_qnty+=$grey_receive_qnty_arr[$po_id];
							$grey_fabric_issue+=$grey_issue_qnty_arr[$po_id]-$grey_issue_return_qnty_arr[$po_id];
							
							$booking_data.=implode(",",array_filter(explode(",",substr($dataArrayWo[$po_id],0,-1)))).",";
						}
						
						if(($cbo_discrepancy==1 && $grey_recv_qnty>$yarn_issued) || ($cbo_discrepancy==0))
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$buyer_name_array[$row[csf('buyer_name')]]=$buyer_short_name_library[$row[csf('buyer_name')]];
						
							$booking_array=array(); $color_data_array=array();
							$required_qnty=0; $main_booking=''; $sample_booking=''; $main_booking_excel=''; $sample_booking_excel='';
							$dataArray=explode(",",substr($booking_data,0,-1));
							if(count($dataArray)>0)
							{
								foreach($dataArray as $woRow)
								{
									$woRow=explode("**",$woRow);
									$id=$woRow[0];
									$booking_no=$woRow[1];
									$insert_date=$woRow[2];
									$item_category=$woRow[3];
									$fabric_source=$woRow[4];
									$company_id=$woRow[5];
									$booking_type=$woRow[6];
									$booking_no_prefix_num=$woRow[7];
									$job_no=$woRow[8];
									$is_short=$woRow[9];
									$is_approved=$woRow[10];
									$fabric_color_id=$woRow[11];
									$req_qnty=$woRow[12];
									$grey_req_qnty=$woRow[13];
									
									$required_qnty+=$grey_req_qnty;
		
									if(!in_array($id,$booking_array))
									{
										$system_date=date('d-M-Y', strtotime($insert_date));
										
										if($booking_type==4)
										{
											$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
											$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
										}
										else
										{
											if($is_short==1) $pre="S"; else $pre="M"; 
											
											$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
											$main_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
										}
										
										$booking_array[]=$id;
									}
									$color_data_array[$fabric_color_id]+=$req_qnty;
								}
							}
							else
							{
								$main_booking.="No Booking";
								$main_booking_excel.="No Booking";
								$sample_booking.="No Booking";
								$sample_booking_excel.="No Booking";
							}
							
							if($main_booking=="")
							{
								$main_booking.="No Booking";
								$main_booking_excel.="No Booking";
							}
							
							if($sample_booking=="") 
							{
								$sample_booking.="No Booking";
								$sample_booking_excel.="No Booking";
							}
							
							$yarn_issue_array[$row[csf('buyer_name')]]+=$yarn_issued;
							$grey_required_array[$row[csf('buyer_name')]]+=$required_qnty;
							
							$net_trans_yarn=0; $net_trans_knit=0; $batch_qnty=0;
							foreach($job_po_id as $val)
							{
								$finish_color=array_unique(explode(",",$po_color_arr[$val]));
								foreach($finish_color as $color_id)
								{
									if($color_id>0)
									{ 
										$color_data_array[$color_id]+=0;
									}
								}
								
								$net_trans_yarn+=$trans_qnty_arr[$val]['yarn_trans'];
								$net_trans_knit+=$trans_qnty_arr[$val]['knit_trans'];
								
								$batch_qnty+=$batch_qnty_arr[$val];
							}
							
							$yarn_issue_array[$row[csf('buyer_name')]]+=$net_trans_yarn;
							$balance=$required_qnty-($yarn_issued+$net_trans_yarn);
							
							$yarn_balance_array[$row[csf('buyer_name')]]+=$balance;
							
							$knitted_array[$row[csf('buyer_name')]]+=$grey_recv_qnty+$grey_purchase_qnty;
							
							$knitted_array[$row[csf('buyer_name')]]+=$net_trans_knit;
							$grey_balance=$required_qnty-($grey_recv_qnty+$net_trans_knit+$grey_purchase_qnty);
							
							$grey_balance_array[$row[csf('buyer_name')]]+=$grey_balance;
							
							$grey_issue_array[$row[csf('buyer_name')]]+=$grey_fabric_issue;
							
							$batch_qnty_array[$row[csf('buyer_name')]]+=$batch_qnty;
							
							$tot_order_qnty+=$order_qnty_in_pcs;
							$tot_mkt_required+=$job_mkt_required;
							$tot_yarn_issue_qnty+=$yarn_issued;
							$tot_fabric_req+=$required_qnty;
							$tot_balance+=$balance;
							$tot_grey_recv_qnty+=$grey_recv_qnty;
							$tot_grey_purchase_qnty+=$grey_purchase_qnty;
							$tot_grey_balance+=$grey_balance;
							$tot_grey_issue+=$grey_fabric_issue;
							$tot_batch_qnty+=$batch_qnty;
							
							$grey_available=$grey_recv_qnty+$grey_purchase_qnty+$net_trans_knit;
							$tot_grey_available+=$grey_available;
					
							if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';
							
							$po_entry_date=date('d-m-Y', strtotime($row[csf('insert_date')]));
							$costing_date=$costing_date_library[$row[csf('job_no')]];
							
							$tot_color=count($color_data_array);	
							
							if($tot_color>0)
							{
								$z=1;
								foreach($color_data_array as $key=>$value)
								{
									if($z==1) 
									{
										$display_font_color="";
										$font_end="";
									}
									else 
									{
										$display_font_color="<font style='display:none' color='$bgcolor'>";
										$font_end="</font>";
									}
									
									if($z==1)
									{
										$html.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='center'>".$row[csf('job_no')]."</td>
												<td align='left'>".$row[csf('po_number')]."</td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('style_ref_no')]."</td>
												<td align='left'>".$gmts_item."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>View</td>";
										
										$html_short.="<tr bgcolor='".$bgcolor."'>
													<td align='left'>".$i."</td>
													<td align='left'>".$main_booking_excel."</td>
													<td align='left'>".$sample_booking_excel."</td>
													<td align='left'>".$row[csf('po_number')]."</td>
													<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
													<td align='right'>".$order_qnty_in_pcs."</td>
													<td align='left'>View</td>";			
										
									}
									else
									{
										$html.="<tr bgcolor='".$bgcolor."'>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>
												<td></td>";
												
										$html_short.="<tr bgcolor='".$bgcolor."'>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>
														<td></td>";		
									}
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
										<td width="40"><? echo $display_font_color.$i.$font_end; ?></td>
										<td width="125"><? echo $display_font_color.$main_booking.$font_end; ?></td>
										<td width="125"><? echo $display_font_color.$sample_booking.$font_end; ?></td>
										<td width="100" align="center"><? echo $display_font_color.$row[csf('job_no')].$font_end; ?></td>
										<td width="120"><p><? echo $display_font_color.$row[csf('po_number')]. $font_end; ?></p></td>
										<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$row[csf('buyer_name')]].$font_end; ?></p></td>
										<td width="130"><p><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></p></td>
										<td width="140"><p><? echo $display_font_color.$gmts_item; ?></p></td>
										<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
										<td width="80" align="center"><? echo $display_font_color; ?><a href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','Shipment_date','')"><? echo "View"; ?></a><? echo $font_end; ?></td>
										<td width="70">
											<? 
												 $html.="<td>"; $d=1;
												 foreach($yarn_data_array['count'] as $yarn_count_value)
												 {
													if($d!=1)
													{
														echo $display_font_color."<hr/>".$font_end;
														if($z==1) $html.="<hr/>";
													}
													
													echo $display_font_color.$yarn_count_value.$font_end;
													if($z==1) $html.=$yarn_count_value;
												 $d++;
												 }
												 
												 $html.="</td><td>";
											?>
										</td>
										<td width="110" style="word-break:break-all;">
											<p>
												<? 
													 $d=1;
													 foreach($yarn_data_array['comp'] as $yarn_composition_value)
													 {
														if($d!=1)
														{
															echo $display_font_color."<hr/>".$font_end;
															if($z==1) $html.="<hr/>";
														}
														echo $display_font_color.$yarn_composition_value.$font_end;
														if($z==1) $html.=$yarn_composition_value;
													 $d++;
													 }
													 
													 $html.="</td><td>";
												?>
											</p>
										</td>
										<td width="80">
											<p>
												<?
													 $d=1;
													 foreach($yarn_data_array['type'] as $yarn_type_value)
													 {
														if($d!=1)
														{
															echo $display_font_color."<hr/>".$font_end;
															if($z==1) $html.="<hr/>";
														}
														
														echo $display_font_color.$yarn_type_value.$font_end; 
														if($z==1) $html.=$yarn_type_value;
													 $d++;
													 }
													 
													 $html.="</td><td>"; 
												?>
											</p>
										</td>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
													echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
													$d=1;
													foreach($mkt_required_array as $mkt_required_value)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}
														
														$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
														
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
													<?
													$html.=number_format($mkt_required_value,2);
													$d++;
													}
												}
												
												$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>";
											?>
										</td>
										<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
											<? 
												if($z==1)
												{
													echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
													$d=1;
													foreach($yarn_desc_array as $yarn_desc)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
															$html_short.="<hr/>";
														}
														
														$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
														$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
														
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
														<?
														$html.=number_format($yarn_iss_qnty,2);
														$html_short.=number_format($yarn_iss_qnty,2);
														$d++;
													}
													
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
														$html_short.="<hr/>";
													}
													
													$yarn_desc=join(",",$yarn_desc_array);
													
													$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
													
													$html.=number_format($iss_qnty_not_req,2);
													$html_short.=number_format($iss_qnty_not_req,2);
													?>
													<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
												<?
												}
												?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1) 
												{
												?>
                                                	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
												<?
													$html.=number_format($net_trans_yarn,2); 
													$tot_net_trans_yarn_qnty+=$net_trans_yarn;
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
										<? 
											if($z==1) 
											{
												echo number_format($balance,2,'.','');
												$html.=number_format($balance,2); 
											}
										?>
										</td>
										<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; $html_short.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
										<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>">
										<? 
											if($z==1) 
											{
												echo number_format($required_qnty,2,'.',''); 
												$html.=number_format($required_qnty,2);
												$html_short.=number_format($required_qnty,2);
											}
										?>
										</td>
										<? $html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="</td><td>"; ?>
										<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($grey_recv_qnty,2);
													$html_short.=number_format($grey_recv_qnty,2);
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($grey_purchase_qnty,2);
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1) 
												{
												?>
                                                    <a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
                                                <?
													$html.=number_format($net_trans_knit,2); 
													$tot_net_trans_knit_qnty+=$net_trans_knit;
												}
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
                                                if($z==1) 
                                                {
                                                    echo number_format($grey_available,2,'.','');
                                                    $html.=number_format($grey_available,2);
                                                }
                                            ?>
										</td>
										<? $html.="</td><td>"; $html_short.="</td>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
													echo number_format($grey_balance,2,'.',''); 
													$html.=number_format($grey_balance,2);
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); ?></a>
												<?
													$html.=number_format($grey_fabric_issue,2);
												}
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','batch_qnty','')"><? echo number_format($batch_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($batch_qnty,2);
												}
											?>
										</td>
										<? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="<td bgcolor='#FF9BFF'>"; ?>
										<td width="100" align="center" bgcolor="#FF9BFF">
											<p>
												<? 
													if($key==0)
													{
														echo "-";
														$html.="-"; $html_short.="-";
													}
													else
													{ 
														echo $color_array[$key]; 
														$html.=$color_array[$key]; $html_short.=$color_array[$key];
													}
												
												?>
											</p>
										</td>
										<? $html.="</td><td>"; $html_short.="</td>"; ?>
										<td width="100" align="right">
											<? 
												echo number_format($value,2,'.','');
												$html.=number_format($value,2);
												
												$fin_fab_Requi_array[$row[csf('buyer_name')]]+=$value;
												$tot_color_wise_req+=$value; 
											?>
										</td>
										<? 
											$html.="</td><td>"; $html_short.="<td>"; 
											$fab_recv_qnty=0; $fab_purchase_qnty=0; $issue_to_cut_qnty=0; $dye_qnty=0;
											foreach($job_po_id as $val)
											{
												$fab_recv_qnty+=$finish_receive_qnty_arr[$val][$key];
												$fab_purchase_qnty+=$finish_purchase_qnty_arr[$val][$key]-$finish_recv_rtn_qnty_arr[$val][$key];
												$issue_to_cut_qnty+=$finish_issue_qnty_arr[$val][$key]-$finish_issue_rtn_qnty_arr[$val][$key];
												$dye_qnty+=$dye_qnty_arr[$val][$key];
											}
										?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','dye_qnty','<? echo $key; ?>')"><? echo number_format($dye_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($dye_qnty,2);
												$html_short.=number_format($dye_qnty,2);
												
												$dye_qnty_array[$row[csf('buyer_name')]]+=$dye_qnty;
												$tot_dye_qnty+=$dye_qnty; 
											?>
										</td>
										<? $html.="</td><td>"; $html_short.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_recv_qnty,2);
												$html_short.=number_format($fab_recv_qnty,2);
												
												$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_recv_qnty;
												$tot_fabric_recv+=$fab_recv_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_purchase_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_purchase_qnty,2);
												$html_short.=number_format($fab_purchase_qnty,2);
												
												$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
												$tot_fabric_purchase+=$fab_purchase_qnty;
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$net_trans_finish=0; 
												$job_po_id=explode(",",$row[csf('po_id')]);
												foreach($job_po_id as $val)
												{
													$net_trans_finish+=$trans_qnty_fin_arr[$val][$key]['trans'];
												} 
											?>
                                            	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','finish_trans','<? echo $key; ?>')"><? echo number_format($net_trans_finish,2,'.','');  ?></a>
                                            <?	
												$html.=number_format($net_trans_finish,2); 
												$fin_fab_recei_array[$row[csf('buyer_name')]]+=$net_trans_finish;
												$tot_net_trans_finish_qnty+=$net_trans_finish;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$fabric_available=$fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish;
												echo number_format($fabric_available,2,'.',''); 
												$html.=number_format($fabric_available,2);
												$tot_fabric_available+=$fabric_available;
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$fabric_balance=$value-($fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish);
												echo number_format($fabric_balance,2,'.',''); 
												$html.=number_format($fabric_balance,2);
												
												$fin_balance_array[$row[csf('buyer_name')]]+=$fabric_balance;
												$tot_fabric_balance+=$fabric_balance;
											?>
										</td>
										<? $html.="</td><td>"; $html_short.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($issue_to_cut_qnty,2);
												$html_short.=number_format($issue_to_cut_qnty,2);
												
												$issue_to_cut_array[$row[csf('buyer_name')]]+=$issue_to_cut_qnty;
												$tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
											?>
										</td>
										<? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$fabric_left_over=$fabric_available-$issue_to_cut_qnty;
												echo number_format($fabric_left_over,2,'.',''); 
												$html.=number_format($fabric_left_over,2);
												$tot_fabric_left_over+=$fabric_left_over;
											?>
										</td>
										<td> 
											<p>
												<? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
											</p>
										</td>
									</tr>
								<?	
									if($z==1) $html.="</td><td>".join(",<br>",array_unique($fabric_desc))."</td></tr>"; else $html.="</td></tr>"; 
									$html_short.="</td></tr>"; 
								$z++;
								$k++;
								}
							}
							else
							{
								$html.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='center'>".$row[csf('job_no')]."</td>
												<td align='left'>".$row[csf('po_number')]."</td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('style_ref_no')]."</td>
												<td align='left'>".$gmts_item."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>View</td>";
								
								$html_short.="<tr bgcolor='".$bgcolor."'>
											<td align='left'>".$i."</td>
											<td align='left'>".$main_booking_excel."</td>
											<td align='left'>".$sample_booking_excel."</td>
											<td align='left'>".$row[csf('po_number')]."</td>
											<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
											<td align='right'>".$order_qnty_in_pcs."</td>
											<td align='left'>View</td>";
														
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="125"><? echo $main_booking; ?></td>
									<td width="125"><? echo $sample_booking; ?></td>
									<td width="100" align="center"><? echo $row[csf('job_no')]; ?></td>
									<td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
									<td width="80"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
									<td width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
									<td width="140"><p><? echo $gmts_item; ?></p></td>
									<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
									<td width="80" align="center"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','Shipment_date','')"><? echo "View"; ?></a></td>
									<td width="70">
										<? 
											 $html.="<td>"; $d=1;
											 foreach($yarn_data_array['count'] as $yarn_count_value)
											 {
												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
												}
												
												echo $yarn_count_value; 
												$html.=$yarn_count_value;
												
											 $d++;
											 }
											 
											 $html.="</td><td>";
										?>
									</td>
									<td width="110" style="word-break:break-all;">
										<p>
											<? 
												 $d=1;
												 foreach($yarn_data_array['comp'] as $yarn_composition_value)
												 {
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}
													
													echo $yarn_composition_value; 
													$html.=$yarn_composition_value;
													
												 $d++;
												 }
												 
												 $html.="</td><td>";
											?>
										</p>
									</td>
									<td width="80">
										<p>
											<?
												 $d=1;
												 foreach($yarn_data_array['type'] as $yarn_type_value)
												 {
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}
													
													echo $yarn_type_value; 
													$html.=$yarn_type_value;
													
												 $d++;
												 }
												 
												 $html.="</td><td>"; 
											?>
										</p>
									</td>
									<td width="100" align="right">
										<? 
											echo "<font color='$bgcolor' style='display:none'>".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
											$d=1;
											foreach($mkt_required_array as $mkt_required_value)
											{
												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
												}
												
												$yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
												
												?>
												<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_req','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($mkt_required_value,2,'.','');?></a>
											<?
											$html.=number_format($mkt_required_value,2);
											$d++;
											}
											
											$html.="</td><td bgcolor='$discrepancy_td_color'>"; $html_short.="<td>";
										?>
									</td>
									<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
										<? 
											echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
											$d=1;
											foreach($yarn_desc_array as $yarn_desc)
											{
												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
													$html_short.="<hr/>";
												}
												
												$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
												$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
												
												?>
												<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
												<?
												$html.=number_format($yarn_iss_qnty,2);
												$html_short.=number_format($yarn_iss_qnty,2);
												$d++;
											}
											
											if($d!=1)
											{
												echo "<hr/>";
												$html.="<hr/>";
												$html_short.="<hr/>";
											}
											
											$yarn_desc=join(",",$yarn_desc_array);
											
											$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
											
											$html.=number_format($iss_qnty_not_req,2);
											$html_short.=number_format($iss_qnty_not_req,2);
											?>
											<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
											
									</td>
									<? $html.="</td><td>"; ?>
									<td width="100" align="right">
                                    	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
										<? 
											$html.=number_format($net_trans_yarn,2); 
											$tot_net_trans_yarn_qnty+=$net_trans_yarn;
										?>
									</td>
									<td width="100" align="right">
										<? 
											echo number_format($balance,2,'.','');
										?>
									</td>
									<td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); ?></td>
									<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); ?></a></td>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a></td>
									<td width="100" align="right">
                                    	 <a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
										<? 
											$tot_net_trans_knit_qnty+=$net_trans_knit;
										?>
									</td>
                                    <td width="100" align="right">
										<?
                                            echo number_format($grey_available,2,'.','');
                                        ?>
                                    </td>
									<td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); ?></td>
									<td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); ?></a>
									</td>
									<td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','batch_qnty','')"><? echo number_format($batch_qnty,2,'.',''); ?></a></td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
									<td width="100">&nbsp;</td>
                                    <td width="100">&nbsp;</td>
									<td> 
										<p>
											<? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo join(",<br>",array_unique($fabric_desc)); ?>
										</p>
									</td>
								</tr>
								<?	
									$html.="</td><td>".number_format($balance,2)."</td>
									<td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
									<td bgcolor='$discrepancy_td_color'>".number_format($grey_recv_qnty,2)."</td>
									<td>".number_format($grey_purchase_qnty,2)."</td>
									<td>".number_format($net_trans_knit,2)."</td>
									<td>".number_format($grey_available,2)."</td>
									<td>".number_format($grey_balance,2)."</td>
									<td>".number_format($grey_fabric_issue,2)."</td>
									<td>".number_format($batch_qnty,2)."</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td>".join(",<br>",array_unique($fabric_desc))."</td>
									</tr>
									";
									
									$html_short.="</td><td bgcolor='$bgcolor_grey_td'>".number_format($required_qnty,2)."</td>
									<td>".number_format($grey_recv_qnty,2)."</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									</tr>
									";
							$k++;
							}
						$i++;	
						}
					}// end main query
				}
				?>
                </table>
            </div>
            <?
				$html.="<tfoot>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th>Total</th>
							<th align='right'>".number_format($tot_order_qnty,0)."</th>
							<th></th>";
							
				if($type==1)
				{
					$html.="<th></th>
							<th></th>
							<th></th>";
				}
							
				$html.="<th></th>
							<th></th
							<th></th>
							<th align='right'>".number_format($tot_mkt_required,2)."</th>
							<th align='right'>".number_format($tot_yarn_issue_qnty,2)."</th>
							<th align='right'>".number_format($tot_net_trans_yarn_qnty,2)."</th>
							<th align='right'>".number_format($tot_balance,2)."</th>
							<th align='right'>".number_format($tot_fabric_req,2)."</th>
							<th align='right'>".number_format($tot_grey_recv_qnty,2)."</th>
							<th align='right'>".number_format($tot_grey_purchase_qnty,2)."</th>
							<th align='right'>".number_format($tot_net_trans_knit_qnty,2)."</th>
							<th align='right'>".number_format($tot_grey_available,2)."</th>
							<th align='right'>".number_format($tot_grey_balance,2)."</th>
							<th align='right'>".number_format($tot_grey_issue,2)."</th>
							<th align='right'>".number_format($tot_batch_qnty,2)."</th>
							<th></th
							<th align='right'>".number_format($tot_color_wise_req,2)."</th>
							<th align='right'>".number_format($tot_dye_qnty,2)."</th>
							<th align='right'>".number_format($tot_fabric_recv,2)."</th>
							<th align='right'>".number_format($tot_fabric_purchase,2)."</th>
							<th align='right'>".number_format($tot_net_trans_finish_qnty,2)."</th>
							<th align='right'>".number_format($tot_fabric_available,2)."</th>
							<th align='right'>".number_format($tot_fabric_balance,2)."</th>
							<th align='right'>".number_format($tot_issue_to_cut_qnty,2)."</th>
							<th align='right'>".number_format($tot_fabric_left_over,2)."</th>
							<th></th
						</tfoot>
					</table>
					<br />
					";
					
				$html_short.="<tfoot>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th>Total</th>
								<th align='right'>".number_format($tot_order_qnty,0)."</th>
								<th></th>
								<th align='right'>".number_format($tot_yarn_issue_qnty,2)."</th>
								<th align='right'>".number_format($tot_fabric_req,2)."</th>
								<th align='right'>".number_format($tot_grey_recv_qnty,2)."</th>
								<th></th
								<th align='right'>".number_format($tot_dye_qnty,2)."</th>
								<th align='right'>".number_format($tot_fabric_recv,2)."</th>
								<th align='right'>".number_format($tot_issue_to_cut_qnty,2)."</th>
							</tfoot>
						</table>
						<br />
						";
					
			?>
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <tfoot>
                    <tr>
                        <th width="40"></th>
                        <th width="125"></th>
                        <th width="125"></th>
                        <th width="100"></th>
                        <th width="120"></th>
                        <th width="80"></th>
                        <th width="130"></th>
                        <th width="140">Total</th>
                        <th width="100" id="tot_order_qnty"><? echo number_format($tot_order_qnty,0); ?></th>
                        <th width="80"></th>
                        <?
						if($type==1)
						{
						?>
                            <th width="80"></th>
                            <th width="80"></th>
                            <th width="100"></th>
                        <?
						}
						?>
                        <th width="70"></th>
                        <th width="110"></th>
                        <th width="80"></th>
                        <th width="100" id="value_tot_yarn_rec"><? echo number_format($tot_mkt_required,2); ?></th>
                        <th width="100" id="value_tot_yarn_issue"><? echo number_format($tot_yarn_issue_qnty,2); ?></th>
                        <th width="100" id="value_tot_net_trans_yarn"><? echo number_format($tot_net_trans_yarn_qnty,2); ?></th>
                        <th width="100" id="value_tot_yarn_balance"><? echo number_format($tot_balance,2); ?></th>
                        <th width="100" id="value_tot_grey_rec"><? echo number_format($tot_fabric_req,2); ?></th>
                        <th width="100" id="value_tot_grey_knit"><? echo number_format($tot_grey_recv_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_purchase"><? echo number_format($tot_grey_purchase_qnty,2); ?></th>
                        <th width="100" id="value_tot_net_trans_knit"><? echo number_format($tot_net_trans_knit_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_available"><? echo number_format($tot_grey_available,2); ?></th>
                        <th width="100" id="value_tot_grey_knit_bala"><? echo number_format($tot_grey_balance,2); ?></th>
                        <th width="100" id="value_tot_grey_issue"><? echo number_format($tot_grey_issue,2); ?></th>
                        <th width="100" id="value_tot_batch"><? echo number_format($tot_batch_qnty,2); ?></th>
                        <th width="100"></th>
                        <th width="100" id="value_tot_fin_rec"><? echo number_format($tot_color_wise_req,2); ?></th>
                        <th width="100" id="value_tot_dye_qnty"><? echo number_format($tot_dye_qnty,2); ?></th>
                        <th width="100" id="value_tot_fini_receive"><? echo number_format($tot_fabric_recv,2); ?></th>
                        <th width="100" id="value_tot_fabric_purchase"><? echo number_format($tot_fabric_purchase,2); ?></th>
                        <th width="100" id="value_tot_net_trans_finish"><? echo number_format($tot_net_trans_finish_qnty,2); ?></th>
                        <th width="100" id="value_tot_fabric_available"><? echo number_format($tot_fabric_available,2); ?></th>
                        <th width="100" id="value_tot_fini_balance"><? echo number_format($tot_fabric_balance,2); ?></th>
                        <th width="100" id="value_tot_fini_cut_issue"><? echo number_format($tot_issue_to_cut_qnty,2); ?></th>
                        <th width="100" id="value_tot_fabric_left_over"><? echo number_format($tot_fabric_left_over,2); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
            <br />
			<?
                $html.="<table align='center'>
                        <tr valign='top'>
                        <td>
                            <table border=1 rules='all' align='center'>
                            <thead>
                            <tr align='center'>
                                <th colspan='16'>Buyer Level Summary</th>
                            </tr>
                            <tr>
                                <th>SL</th>
                                <th>Buyer Name</th>
                                <th>Grey Req</th>
                                <th>Yarn Issue + Net Transfer</th>
                                <th>Yarn Balance</th>
                                <th>Grey Fabric Available</th>
                                <th>Knit Balance</th>
                                <th>Gery To Dye</th>
                                <th>Batch Qnty</th>
                                <th>Batch Balance</th>
                                <th>Total Dye Qnty</th>
                                <th>Dyeing Balance</th>
                                <th>Finish Fabric Req</th>
                                <th>Finish Fabric Available</th>
                                <th>Finish Fabric Balance</th>
                                <th>Issue To Cut</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                ";
            ?>
            <table align="center">
                <tr valign="top">
                    <td>
                        <div id="data_panel1" align="center" style="width:100%">
                            <input type="button" value="Print" class="formbutton" name="print" id="print" onclick="new_window()" style="width:100px" />
                        </div> 
                        <div id="buyer_summary" style="border:none">
                            <table width="1620" class="rpt_table" border="0" rules="all" align="center">
                                <thead>
                                    <tr align="center" id="company_id_td" style="visibility:hidden; border:none">
                                        <th colspan="16" style="border:none">
                                            <font size="3"><strong>Company Name: <?php echo $company_library[$company_name]; ?></strong></font>
                                        </th>
                                    </tr>
                                    <tr align="center" id="date_td" style="visibility:hidden;border:none">
                                         <th colspan="16" style="border:none"><font size="3"><? echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></font></th>
                                    </tr>
                                    <tr align="center">
                                        <th colspan="16">Buyer Level Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="40">SL</th>
                                        <th width="130">Buyer Name</th>
                                        <th width="100">Grey Req</th>
                                        <th width="100">Yarn Issue + Net Transfer</th> 
                                        <th width="100">Yarn Balance</th>
                                        <th width="100">Grey Fabric Available</th>
                                        <th width="100">Knit Balance</th>
                                        <th width="100">Gery To Dye</th>
                                        <th width="100">Batch Qnty</th>
                                        <th width="100">Batch Balance</th>
                                        <th width="100">Total Dye Qnty</th>
                                        <th width="100">Dyeing Balance</th>
                                        <th width="100">Finish Fabric Req</th>
                                        <th width="100">Fininish Fabric Available</th>
                                        <th width="100">Finish Fabric Balance</th>
                                        <th>Issue To Cut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?
                                $b_sl=1;
                                $buyer_number=asort($buyer_name_array);
                                foreach($buyer_name_array as $key=>$value)
                                {
                                    if ($b_sl%2==0)  
                                        $bgcolor="#E9F3FF";
                                    else
                                        $bgcolor="#FFFFFF";	
                                    
                                    $batch_bl=$grey_required_array[$key]-$batch_qnty_array[$key];
                                    $dye_bl=$grey_required_array[$key]-$dye_qnty_array[$key];
                                    
                                    $html.="<tr bgcolor='$bgcolor'>
                                            <td align='right'>".$b_sl."</td>
                                            <td align='right'>".$value."</td>
                                            <td align='right'>".number_format($grey_required_array[$key],2)."</td>
                                            <td align='right'>".number_format($yarn_issue_array[$key],2)."</td>
                                            <td align='right'>".number_format($yarn_balance_array[$key],2)."</td>
                                            <td align='right'>".number_format($knitted_array[$key],2)."</td>
                                            <td align='right'>".number_format($grey_balance_array[$key],2)."</td>
                                            <td align='right'>".number_format($grey_issue_array[$key],2)."</td>
                                            <td align='right'>".number_format($batch_qnty_array[$key],2)."</td>
                                            <td align='right'>".number_format($batch_bl,2)."</td>
                                            <td align='right'>".number_format($dye_qnty_array[$key],2)."</td>
                                            <td align='right'>".number_format($dye_bl,2)."</td>
                                            <td align='right'>".number_format($fin_fab_Requi_array[$key],2)."</td>
                                            <td align='right'>".number_format($fin_fab_recei_array[$key],2)."</td>
                                            <td align='right'>".number_format($fin_balance_array[$key],2)."</td>
                                            <td align='right'>".number_format($issue_to_cut_array[$key],2)."</td>
                                            </tr>
                                        ";
                                    
                                    ?>
                                    <tr bgcolor="<? echo $bgcolor; ?>">
                                        <td width="40"><? echo $b_sl ;?></td>
                                        <td width="130"><? echo $value ;?></td>
                                        <td width="100" align="right"><? echo number_format($grey_required_array[$key],2); $grey_required_array_tot+=$grey_required_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($yarn_issue_array[$key],2); $yarn_issue_array_tot+=$yarn_issue_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($yarn_balance_array[$key],2); $yarn_balance_array_tot+=$yarn_balance_array[$key];?></td>
                                        <td width="100" align="right"><? echo number_format($knitted_array[$key],2); $knitted_array_tot+=$knitted_array[$key];?></td>
                                        <td width="100" align="right"><? echo number_format($grey_balance_array[$key],2); $grey_balance_array_tot+=$grey_balance_array[$key];?></td>
                                        <td width="100" align="right"><? echo number_format($grey_issue_array[$key],2);$grey_issue_array_tot+=$grey_issue_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($batch_qnty_array[$key],2);$batch_qnty_array_tot+=$batch_qnty_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($batch_bl,2); $batch_bl_tot+=$batch_bl; ?></td>
                                        <td width="100" align="right"><? echo number_format($dye_qnty_array[$key],2); $dye_qnty_array_tot+=$dye_qnty_array[$key];?></td>
                                        <td width="100" align="right"><? echo number_format($dye_bl,2);$dye_bl_tot+=$dye_bl;?></td>
                                        <td width="100" align="right"><? echo number_format($fin_fab_Requi_array[$key],2); $fin_fab_Requi_array_tot+=$fin_fab_Requi_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($fin_fab_recei_array[$key],2); $fin_fab_recei_array_tot+=$fin_fab_recei_array[$key]; ?></td>
                                        <td width="100" align="right"><? echo number_format($fin_balance_array[$key],2); $fin_balance_array_tot+=$fin_balance_array[$key];?></td>
                                        <td align="right"><? echo number_format($issue_to_cut_array[$key],2);$issue_to_cut_tot+=$issue_to_cut_array[$key]; ?></td>
                                    </tr>
                                <?
                                $b_sl++;
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="40" colspan="2" align="right">Total</th>
                                        <th width="100" align="right"><? echo number_format($grey_required_array_tot,2);?></th>
                                        <th width="100" align="right"><? echo number_format($yarn_issue_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($yarn_balance_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($knitted_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($grey_balance_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($grey_issue_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($batch_qnty_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($batch_bl_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($dye_qnty_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($dye_bl_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($fin_fab_Requi_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($fin_fab_recei_array_tot,2) ;?></th>
                                        <th width="100" align="right"><? echo number_format($fin_balance_array_tot,2) ;?></th>
                                        <th width="" align="right"><? echo number_format($issue_to_cut_tot,2) ;?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </td>
                    <?
                        $html.="</tbody>
                                <tfoot>
                                <tr>
                                <th colspan='2' align='right'>Total</th>
                                <th align='right'>".number_format($grey_required_array_tot,2)."</th>
                                <th align='right'>".number_format($yarn_issue_array_tot,2)."</th>
                                <th align='right'>".number_format($yarn_balance_array_tot,2)."</th>
                                <th align='right'>".number_format($knitted_array_tot,2)."</th>
                                <th align='right'>".number_format($grey_balance_array_tot,2)."</th>
                                <th align='right'>".number_format($grey_issue_array_tot,2)."</th>
                                <th align='right'>".number_format($batch_qnty_array_tot,2)."</th>
                                <th align='right'>".number_format($batch_bl_tot,2)."</th>
                                <th align='right'>".number_format($dye_qnty_array_tot,2)."</th>
                                <th align='right'>".number_format($dye_bl_tot,2)."</th>
                                <th align='right'>".number_format($fin_fab_Requi_array_tot,2)."</th>
                                <th align='right'>".number_format($fin_fab_recei_array_tot,2)."</th>
                                <th align='right'>".number_format($fin_balance_array_tot,2)."</th>
                                <th align='right'>".number_format($issue_to_cut_tot,2)."</th>
                                </tr>
                                </tfoot>
                            </table>
                            </td>
                            <td width='90'></td>
                            <td>
                                <table border=1 rules='all'>
                                    <thead>
                                    <tr>
                                        <th colspan='3'>Summary</th>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <th>Particulars</th>
                                        <th>Total Qnty</th>
                                        <th>% On Required</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Yarn Required</td>
                                        <td align='right'>".number_format($tot_fabric_req,2)."</td>
                                        <td align='right'></td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Yarn Issued To Knitting</td>
                                        <td align='right'>".number_format($tot_yarn_issue_qnty,2)."</td>
                                        <td align='right'>".number_format($tot_yarn_issue_qnty/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Yarn Balance</td>
                                        <td align='right'>".number_format($tot_fabric_req-$tot_yarn_issue_qnty,2)."</td>
                                        <td align='right'>".number_format(((($tot_fabric_req-$tot_yarn_issue_qnty)/$tot_fabric_req)*100),2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Grey Fabric Required</td>
                                        <td align='right'>".number_format($tot_fabric_req,2)."</td>
                                        <td align='right'></td>
                                    </tr>
									<tr bgcolor='#FFFFFF'>
                                       <td>Total Grey Fabric Available</td>
                                       <td align='right'>".number_format($tot_grey_recv_qnty+$tot_grey_purchase_qnty+$tot_net_trans_knit_qnty,2)."</td>
                                       <td align='right'>".number_format(($tot_grey_recv_qnty+$tot_grey_purchase_qnty+$tot_net_trans_knit_qnty)/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Grey Fabric Issued To Dye</td>
                                        <td align='right'>".number_format($tot_grey_issue,2)."</td>
                                        <td align='right'>".number_format($tot_grey_issue/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Grey Fabric Issue Balance</td>
                                        <td align='right'>".number_format($tot_fabric_req-$tot_grey_issue,2)."</td>
                                        <td align='right'>".number_format(((($tot_fabric_req-$tot_grey_issue)/$tot_fabric_req)*100),2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Batch Qnty</td>
                                        <td align='right'>".number_format($tot_batch_qnty,2)."</td>
                                        <td align='right'>&nbsp;</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Batch Balance To Grey Required</td>
                                        <td align='right'>".number_format($tot_fabric_req-$tot_batch_qnty,2)."</td>
                                        <td align='right'>&nbsp;</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Dye Qnty</td>
                                        <td align='right'>".number_format($tot_dye_qnty,2)."</td>
                                        <td align='right'>".number_format($tot_dye_qnty/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Dye Balance To Grey Required</td>
                                        <td align='right'>".number_format($tot_fabric_req-$tot_dye_qnty,2)."</td>
                                        <td align='right'>".number_format(($tot_fabric_req-$tot_dye_qnty)/$tot_fabric_req*100,2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Finish Fabric Required</td>
                                        <td align='right'>".number_format($tot_color_wise_req,2)."</td>
                                        <td align='right'></td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Finish Fabric Receive</td>
                                        <td align='right'>".number_format($tot_fabric_recv+$tot_fabric_purchase+$tot_net_trans_finish_qnty,2)."</td>
                                        <td align='right'>".number_format($tot_fabric_recv+$tot_fabric_purchase+$tot_net_trans_finish_qnty/$tot_color_wise_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Finish Fabric Balance</td>
                                        <td align='right'>".number_format($tot_color_wise_req-($tot_fabric_recv+$tot_fabric_purchase+$tot_net_trans_finish_qnty),2)."</td>
                                        <td align='right'>".number_format(((($tot_color_wise_req-($tot_fabric_recv+$tot_fabric_purchase+$tot_net_trans_finish_qnty))/$tot_color_wise_req)*100),2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Finish Fabric Issued To Cut</td>
                                        <td align='right'>".number_format($tot_issue_to_cut_qnty,2)."</td>
                                        <td align='right'>".number_format($tot_issue_to_cut_qnty/$tot_color_wise_req*100,2)."%</td>
                                    </tr>
                                    </tbody>
                                </table>
                            <td>
                            </tr>
                            </table>
                            ";
                        ?>
                    <td width="90"></td>
                    <td>
                        <table width="600" class="rpt_table" border="1" rules="all">
                            <thead>
                                <tr>
                                    <th colspan="3">Summary</th>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                    <th width="300">Particulars</th>
                                    <th width="170">Total Qnty</th>
                                    <th>% On Required</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Yarn Required</td>
                                   <td align="right"><? echo number_format($tot_fabric_req,2); ?></td>
                                   <td>&nbsp;</td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Yarn Issued To Knitting</td>
                                   <td align="right"> <? echo number_format($tot_yarn_issue_qnty,2); ?></td>
                                   <td align="right"><? echo number_format($tot_yarn_issue_qnty/$tot_fabric_req*100,2)."%"; ?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                   <td>Total Yarn Balance</td>
                                   <td align="right"><? echo number_format($tot_fabric_req-$tot_yarn_issue_qnty,2); ?></td>
                                   <td align="right"><? echo number_format(((($tot_fabric_req-$tot_yarn_issue_qnty)/$tot_fabric_req)*100),2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                    <td>Total Grey Fabric Required</td>
                                   <td align="right"><? echo number_format($tot_fabric_req,2); ?></td>
                                   <td>&nbsp;</td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Grey Fabric Available</td>
                                   <td align="right"> <? echo number_format($tot_grey_recv_qnty+$tot_grey_purchase_qnty+$tot_net_trans_knit_qnty,2); ?></td>
                                   <td align="right"><? echo number_format(($tot_grey_recv_qnty+$tot_grey_purchase_qnty+$tot_net_trans_knit_qnty)/$tot_fabric_req*100,2)."%";?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Grey Fabric Issued To Dye</td>
                                   <td align="right"> <? echo number_format($tot_grey_issue,2); ?></td>
                                   <td align="right"><? echo number_format($tot_grey_issue/$tot_fabric_req*100,2)."%"; ?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                   <td>Total Grey Fabric Issue Balance</td>
                                   <td align="right"><? echo number_format($tot_fabric_req-$tot_grey_issue,2); ?></td>
                                   <td align="right"><? echo number_format(((($tot_fabric_req-$tot_grey_issue)/$tot_fabric_req)*100),2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="#FFFFFF">
                                    <td>Total Batch Qnty</td>
                                    <td align="right"> <? echo number_format($tot_batch_qnty,2); ?></td>
                                    <td align="right">&nbsp;</td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                    <td>Total Batch Balance To Grey Required</td>
                                    <td align="right"> <? $tot_batch_balance=$tot_fabric_req-$tot_batch_qnty; echo number_format($tot_batch_balance,2); ?></td>
                                    <td align="right">&nbsp;</td>
                                </tr>
                                <tr bgcolor="#FFFFFF">
                                    <td>Total Dye Qnty</td>
                                    <td align="right"> <? echo number_format($tot_dye_qnty,2); ?></td>
                                    <td align="right"><? echo number_format($tot_dye_qnty/$tot_fabric_req*100,2)."%"; ?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                    <td>Total Dye Balance To Grey Required</td>
                                    <td align="right"> <? $tot_dye_balance=$tot_fabric_req-$tot_dye_qnty; echo number_format($tot_dye_balance,2); ?></td>
                                    <td align="right"><? echo number_format($tot_dye_balance/$tot_fabric_req*100,2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Finish Fabric Required</td>
                                   <td align="right"><? echo number_format($tot_color_wise_req,2); ?></td>
                                   <td>&nbsp;</td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Finish Fabric Available</td>
                                   <td align="right"><? echo number_format($tot_fabric_recv+$tot_fabric_purchase+$tot_net_trans_finish_qnty,2); ?></td>
                                   <td align="right"><? echo number_format($tot_fabric_recv+$tot_fabric_purchase+$tot_net_trans_finish_qnty/$tot_color_wise_req*100,2)."%"; ?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                   <td>Total Finish Fabric Balance</td>
                                   <td align="right"><? echo number_format($tot_color_wise_req-($tot_fabric_recv+$tot_fabric_purchase+$tot_net_trans_finish_qnty),2); ?></td>
                                   <td align="right"><? echo number_format(((($tot_color_wise_req-($tot_fabric_recv+$tot_fabric_purchase+$tot_net_trans_finish_qnty))/$tot_color_wise_req)*100),2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Finish Fabric Issued To Cut</td>
                                   <td align="right"><? echo number_format($tot_issue_to_cut_qnty,2); ?></td>
                                   <td align="right"><? echo number_format($tot_issue_to_cut_qnty/$tot_color_wise_req*100,2)."%"; ?></td>
                                </tr>
                            </tbody>
                       </table>
                    </td>
                </tr>
            </table>
        </fieldset>
	<?
	}
	

	foreach (glob("../../../ext_resource/tmp_report/$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename="../../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$filename_short="../../../ext_resource/tmp_report/".$user_name."_".$name."short.xls";
	$create_new_doc = fopen($filename, 'w');
	$create_new_doc_short = fopen($filename_short, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	$is_created_short = fwrite($create_new_doc_short,$html_short);
	$filename="../../ext_resource/tmp_report/".$user_name."_".$name.".xls";
	$filename_short="../../ext_resource/tmp_report/".$user_name."_".$name."short.xls";
	echo "$total_data####$filename####$filename_short";
	exit();
 	
}

if($action=="Shipment_date")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<div align="center">
<fieldset style="width:670px">
	<table border="1" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" width="640">
		<thead>
        	<tr>
            	<th colspan="6">Order Details</th>
            </tr>
            <tr>
                <th width="130">PO No</th>
                <th width="120">PO Qnty</th>
                <th width="90">Shipment Date</th>
                <th width="90">PO Receive Date</th>
                <th width="90">PO Entry Date</th>
                <th>Shipping Status</th>
        	</tr>
        </thead>
		<?
        $i=1; $total_order_qnty=0;
        $sql="select a.job_no, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in(".str_replace("'","",$order_id).") order by b.pub_shipment_date, b.id";
        $result=sql_select($sql);
        foreach($result as $row)
        {
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
				
			$order_qnty=$row[csf('po_qnty')]*$row[csf('ratio')];
			$total_order_qnty+=$order_qnty;
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
                <td width="130"><p><? echo $row[csf('po_number')]; ?></p> </td>
                <td width="120" align="right"><? echo number_format($order_qnty,0);; ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                <td width="90" align="center"><? echo date('d-m-Y', strtotime($row[csf('insert_date')])); ?></td>
				<td><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
            </tr>
		<?
        $i++;
        }
        ?>
        <tfoot>
            <th>Total</th>
        	<th><? echo number_format($total_order_qnty,2);?></th>
            <th></th>
         	<th></th>
          	<th></th>
            <th></th>
        </tfoot>
    </table>
</fieldset>  
</div> 
<?
exit();
}

if($action=="yarn_req")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:850px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:845px; margin-left:10px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="8"><b>Required Qnty Info</b></th>
				</thead>
				<thead>
                    <th width="40">SL</th>
                    <th width="120">Order No.</th>
                    <th width="120">Buyer Name</th>
                    <th width="90">Cons/Dzn</th>
                    <th width="110">Order Qnty</th>
                    <th width="110">Plan Cut Qnty</th>
                    <th width="110">Required Qnty</th>
                    <th>Shipment Date</th>
                </thead>
             </table>
             <div style="width:830px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="810" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $tot_req_qnty=0;
					$sql="select a.buyer_name, a.job_no, a.total_set_qnty as ratio, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut, sum(c.cons_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id in($order_id) and c.count_id='$yarn_count' and c.copm_one_id='$yarn_comp_type1st' and c.percent_one='$yarn_comp_percent1st' and c.copm_two_id='$yarn_comp_type2nd' and c.percent_two='$yarn_comp_percent2nd' and c.type_id='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, a.buyer_name, a.job_no, a.total_set_qnty, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
						$dzn_qnty=0; $required_qnty=0; $order_qnty=0; 
						if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
						else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
						else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
						else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						$order_qnty=$row[csf('po_quantity')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')];
						$required_qnty=$plan_cut_qnty*($row[csf('qnty')]/$dzn_qnty);
                        $tot_req_qnty+=$required_qnty;
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('po_number')]; ?></p></td>
                            <td width="120"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
                            <td width="90" align="right"><p><? echo number_format($row[csf('qnty')],2); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($order_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($plan_cut_qnty,0); ?></p></td>
                            <td width="110" align="right"><p><? echo number_format($required_qnty,2); ?></p></td>
                            <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th align="right" colspan="6">Total</th>
                        <th align="right"><? echo number_format($tot_req_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="yarn_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:870px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:865px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Issue Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
                $i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0;
				$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and c.item_category_id=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					if($row[csf('knit_dye_source')]==1) 
					{
						$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					}
					else if($row['knit_dye_source']==3) 
					{
						$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
					}
					else
						$issue_to="&nbsp;";
						
                    $yarn_issued=$row[csf('issue_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
                        <td width="90"><p><? echo $issue_to; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')];?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knit_dye_source')]!=3)
								{
									echo number_format($yarn_issued,2);
									$total_yarn_issue_qnty+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knit_dye_source')]==3)
								{ 
									echo number_format($yarn_issued,2); 
									$total_yarn_issue_qnty_out+=$yarn_issued;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2);?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="80">Challan No</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Yarn Type</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                <?
                $total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
                $sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and c.item_category_id=1 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
				
					if($row[csf('knitting_source')]==1) 
					{
						$return_from=$company_library[$row[csf('knitting_company')]]; 
					}
					else if($row['knitting_source']==3) 
					{
						$return_from=$supplier_details[$row[csf('knitting_company')]];
					}
					else
						$return_from="&nbsp;";
						
                    $yarn_returned=$row[csf('returned_qnty')];
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
                        <td width="90"><p><? echo $return_from; ?></p></td>
                        <td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                        <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
                        <td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td width="80"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
                        <td align="right" width="90">
							<? 
								if($row[csf('knitting_source')]!=3)
								{
									echo number_format($yarn_returned,2);
									$total_yarn_return_qnty+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                        <td align="right">
							<? 
								if($row[csf('knitting_source')]==3)
								{ 
									echo number_format($yarn_returned,2); 
									$total_yarn_return_qnty_out+=$yarn_returned;
								}
								else echo "&nbsp;";
                            ?>
                        </td>
                    </tr>
                <?
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2);?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2);?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="yarn_issue_not")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$brand_array=return_library_array( "select id, brand_name from lib_brand", "id", "brand_name"  );
	$yarn_desc_array=explode(",",$yarn_count);
	//print_r($yarn_desc_array);
?>
<script>

	function print_window()
	{
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:970px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
            	<thead>
					<th colspan="10"><b>Yarn Issue</b></th>
				</thead>
				<thead>
                    <th width="105">Issue Id</th>
                    <th width="90">Issue To</th>
                    <th width="105">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="75">Issue Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Issue Qnty (In)</th>
                    <th>Issue Qnty (Out)</th>
				</thead>
                <?
				$i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0; $yarn_desc_array_for_return=array();
				$sql_yarn_iss="select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id in ($order_id) and a.entry_form in(3,9) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
				$dataArrayIssue=sql_select($sql_yarn_iss);
				foreach($dataArrayIssue as $row_yarn_iss)
				{
					if($row_yarn_iss[csf('yarn_comp_percent2nd')]!=0)
					{
						$compostion_not_req=$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." %"." ".$composition[$row_yarn_iss[csf('yarn_comp_type2nd')]]." ".$row_yarn_iss[csf('yarn_comp_percent2nd')]." %";
					}
					else
					{
						$compostion_not_req=$composition[$row_yarn_iss[csf('yarn_comp_type1st')]]." ".$row_yarn_iss[csf('yarn_comp_percent1st')]." %"." ".$composition[$row_yarn_iss[csf('yarn_comp_type2nd')]];
					}
			
					$desc=$yarn_count_details[$row_yarn_iss[csf('yarn_count_id')]]." ".$compostion_not_req." ".$yarn_type[$row_yarn_iss[csf('yarn_type')]];
					
					$yarn_desc_for_return=$row_yarn_iss[csf('yarn_count_id')]."__".$row_yarn_iss[csf('yarn_comp_type1st')]."__".$row_yarn_iss[csf('yarn_comp_percent1st')]."__".$row_yarn_iss[csf('yarn_comp_type2nd')]."__".$row_yarn_iss[csf('yarn_comp_percent2nd')]."__".$row_yarn_iss[csf('yarn_type')];
					
					$yarn_desc_array_for_return[$desc]=$yarn_desc_for_return;
					
					if(!in_array($desc,$yarn_desc_array))
					{
						$sql="select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='".$row_yarn_iss[csf('yarn_count_id')]."' and c.yarn_comp_type1st='".$row_yarn_iss[csf('yarn_comp_type1st')]."' and c.yarn_comp_percent1st='".$row_yarn_iss[csf('yarn_comp_percent1st')]."' and c.yarn_comp_type2nd='".$row_yarn_iss[csf('yarn_comp_type2nd')]."' and c.yarn_comp_percent2nd='".$row_yarn_iss[csf('yarn_comp_percent2nd')]."' and c.yarn_type='".$row_yarn_iss[csf('yarn_type')]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result=sql_select($sql);
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							if($row[csf('knit_dye_source')]==1) 
							{
								$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
							}
							else if($row['knit_dye_source']==3) 
							{
								$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
							}
							else
								$issue_to="&nbsp;";
								
							$yarn_issued=$row[csf('issue_qnty')];
							
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="105"><p><? echo $row[csf('issue_number')]; ?></p></td>
								<td width="90"><p><? echo $issue_to; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                                <td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td align="right" width="90">
									<? 
										if($row[csf('knit_dye_source')]!=3)
										{
											echo number_format($yarn_issued,2,'.','');
											$total_yarn_issue_qnty+=$yarn_issued;
										}
										else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<? 
										if($row[csf('knit_dye_source')]==3)
										{ 
											echo number_format($yarn_issued,2,'.',''); 
											$total_yarn_issue_qnty_out+=$yarn_issued;
										}
										else echo "&nbsp;";
									?>
								</td>
							</tr>
						<?
						$i++;
						}
					}
				}
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out,2,'.',''); ?></td>
                </tr>
                <tr style="font-weight:bold">
                    <td align="right" colspan="9">Issue Total</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty+$total_yarn_issue_qnty_out,2,'.',''); ?></td>
                </tr>
                <thead>
                    <th colspan="10"><b>Yarn Return</b></th>
                </thead>
                <thead>
                	<th width="105">Return Id</th>
                    <th width="90">Return From</th>
                    <th width="105">Booking No</th>
                    <th width="70">Challan No</th>
                    <th width="75">Return Date</th>
                    <th width="70">Brand</th>
                    <th width="60">Lot No</th>
                    <th width="180">Yarn Description</th>
                    <th width="90">Return Qnty (In)</th>
                    <th>Return Qnty (Out)</th>
               </thead>
                <?
				$total_yarn_return_qnty=0; $total_yarn_return_qnty_out=0;
				foreach($yarn_desc_array_for_return as $key=>$value)
				{
					if(!in_array($key,$yarn_desc_array))
					{
						$desc=explode("__",$value);
						$yarn_count=$desc[0];
						$yarn_comp_type1st=$desc[1];
						$yarn_comp_percent1st=$desc[2];
						$yarn_comp_type2nd=$desc[3];
						$yarn_comp_percent2nd=$desc[4];
						$yarn_type_id=$desc[5];
						
						$sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, d.brand_id";
						$result=sql_select($sql);
						foreach($result as $row)
						{
							if ($i%2==0)  
								$bgcolor="#E9F3FF";
							else
								$bgcolor="#FFFFFF";	
						
							if($row[csf('knitting_source')]==1) 
							{
								$return_from=$company_library[$row[csf('knitting_company')]]; 
							}
							else if($row['knitting_source']==3) 
							{
								$return_from=$supplier_details[$row[csf('knitting_company')]];
							}
							else
								$return_from="&nbsp;";
								
							$yarn_returned=$row[csf('returned_qnty')];
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="105"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="90"><p><? echo $return_from; ?></p></td>
								<td width="105"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
								<td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                                <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td width="70"><p><? echo $brand_array[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
								<td width="60"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="180"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td align="right" width="90">
									<? 
										if($row[csf('knitting_source')]!=3)
										{
											echo number_format($yarn_returned,2,'.','');
											$total_yarn_return_qnty+=$yarn_returned;
										}
										else echo "&nbsp;";
									?>
								</td>
								<td align="right">
									<? 
										if($row[csf('knitting_source')]==3)
										{ 
											echo number_format($yarn_returned,2,'.',''); 
											$total_yarn_return_qnty_out+=$yarn_returned;
										}
										else echo "&nbsp;";
									?>
								</td>
							</tr>
						<?
						$i++;
						}
					}
				}
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Balance</td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty-$total_yarn_return_qnty,2,'.',''); ?></td>
                    <td align="right"><? echo number_format($total_yarn_issue_qnty_out-$total_yarn_return_qnty_out,2,'.',''); ?></td>
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="9">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty+$total_yarn_issue_qnty_out)-($total_yarn_return_qnty+$total_yarn_return_qnty_out),2);?></th>
                    </tr>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="grey_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	
?>
<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_receive_qnty_in","value_receive_qnty_out","value_receive_qnty_tot"],
						   col: [7,8,9],
						   operation: ["sum","sum","sum"],
						   write_method: ["innerHTML","innerHTML","innerHTML"]
						}
					}
	$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1,tableFilters);
	});
		
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		$('#tbl_list_search tr:first').hide(); 
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
</script>	
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="12"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Receive Id</th>
                    <th width="95">Receive Basis</th>
                    <th width="110">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="60">Machine No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Inhouse Production</th>
                    <th width="80">Outside Production</th>
                    <th width="80">Production Qnty</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 
					
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="60"><p>&nbsp;<? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right">Total</th>
                    <th width="80" align="right" id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                    <th width="80" align="right" id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                    <th width="80" align="right" id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>	
        </div>
	</fieldset>
  
	
	<?
exit();
}

if($action=="grey_purchase")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1037px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Receive Id</th>
                    <th width="95">Receive Basis</th>
                     <th width="160">Product Details</th>
                    <th width="110">Booking/PI/ Production No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Inhouse Production</th>
                    <th width="80">Outside Production</th>
                    <th width="80">Production Qnty</th>
                    <th width="65">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_receive_qnty=0; $receive_data_arr=array();
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
					
                    $sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, a.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_receive_qnty+=$row[csf('quantity')];
						
						$knit_com='';
						if ($row[csf('knitting_source')]==1) $knit_com=$company_library[$row[csf('knitting_company')]]; 
						else if ($row[csf('knitting_source')]==3) $knit_com=$supplier_details[$row[csf('knitting_company')]];
						else $knit_com="&nbsp;";
						
						$recv_data_arr[$row[csf('id')]]['source']=$row[csf('knitting_source')];
						$recv_data_arr[$row[csf('id')]]['com']=$knit_com;
						$recv_data_arr[$row[csf('id')]]['basis']=$receive_basis_arr[$row[csf('receive_basis')]];
						$recv_data_arr[$row[csf('id')]]['booking']=$row[csf('booking_no')];
						$recv_data_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<? 
                                	if($row[csf('knitting_source')]==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_receive_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? echo $knit_com; ?>&nbsp;</p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_receive_qnty_in,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty_out,2,'.',''); ?></th>
                        <th align="right"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset> 
    
<!-- Grey Received Return Info -->   
    
	<fieldset style="width:1037px; margin-top:10px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Grey Return Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Return Id</th>
                    <th width="95">Return Basis</th>
                     <th width="160">Product Details</th>
                    <th width="110">Booking/PI/ Production No</th>
                    <th width="75">Return Date</th>
                    <th width="80">Inhouse Return</th>
                    <th width="80">Outside Return</th>
                    <th width="80">Return Qnty</th>
                    <th width="65">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_return_qnty=0;
					$sql="select a.issue_number, a.issue_date, a.received_id, a.challan_no, b.prod_id, sum(c.quantity) as quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=45 and c.entry_form=45 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, a.received_id, a.challan_no, b.prod_id";
				    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_return_qnty+=$row[csf('quantity')];
						
						$source=$recv_data_arr[$row[csf('received_id')]]['source'];
						$knit_com=$recv_data_arr[$row[csf('received_id')]]['com'];
						$receive_basis=$recv_data_arr[$row[csf('received_id')]]['basis'];
						$booking_no=$recv_data_arr[$row[csf('received_id')]]['booking'];
						$challan_no=$recv_data_arr[$row[csf('received_id')]]['challan_no'];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('rtr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="rtr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis; ?></p></td>
                            <td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="110"><p><? echo $booking_no; ?>&nbsp;</p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td align="right" width="80">
								<? 
                                	if($source!=3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_return_qnty_in+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80">
								<? 
                                	if($source==3)
									{
										echo number_format($row[csf('quantity')],2,'.','');
										$total_return_qnty_out+=$row[csf('quantity')];
									}
									else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="65"><p><? echo $challan_no; ?>&nbsp;</p></td>
                            <td><p><? echo $knit_com; ?>&nbsp;</p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="6" align="right">Total</th>
                            <th align="right"><? echo number_format($total_return_qnty_in,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty_out,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty,2,'.',''); ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    	<tr>
                            <th colspan="6" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_receive_qnty_in-$total_return_qnty_in,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_receive_qnty_out-$total_return_qnty_out,2,'.',''); ?></th>
                            <th align="right"><? echo number_format($total_receive_qnty-$total_return_qnty,2,'.',''); ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>    
<?
exit();
}

if($action=="batch_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="5"><b>Batch Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="100">Batch Date</th>
                    <th width="170">Batch No</th>
                    <th width="150">Batch Color</th>
                    <th>Batch Qnty</th>
				</thead>
             </table>
             <div style="width:667px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_batch_qnty=0;
                    $sql="select a.batch_no, a.batch_date, a.color_id, sum(b.batch_qnty) as quantity from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($order_id) and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.batch_date, a.color_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_batch_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="100" align="center"><? echo change_date_format($row[csf('batch_date')]); ?></td>
                            <td width="170"><p><? echo $row[csf('batch_no')]; ?></p></td>
                            <td width="150"><p><? echo $color_array[$row[csf('color_id')]]; ?></p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="4" align="right">Total</th>
                        <th align="right"><? echo number_format($total_batch_qnty,2); ?></th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="grey_issue")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Grey Issue Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Issue Id</th>
                        <th width="100">Issue Purpose</th>
                        <th width="120">Issue To</th>
                        <th width="105">Booking No</th>
                        <th width="80">Batch No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qnty (In)</th>
                        <th>Issue Qnty (Out)</th>
                    </tr>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $issue_to='';
                    $sql="select a.id,a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=16 and c.entry_form=16 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    { 
						$issue_id_arr[]=$row[csf('id')];
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knit_dye_source')]==1) 
                        {
                            $issue_to=$company_library[$row[csf('knit_dye_company')]]; 
                        }
                        else if($row['knit_dye_source']==3) 
                        {
                            $issue_to=$supplier_details[$row[csf('knit_dye_company')]];
                        }
                        else
                            $issue_to="&nbsp;";
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="105"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                    if($row[csf('knit_dye_source')]!=3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    if($row[csf('knit_dye_source')]==3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_issue_qnty_out+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_issue_qnty_out,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format($total_issue_qnty+$total_issue_qnty_out,2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>

<!-- Grey Issue Return Info -->
   
    <fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                <thead>
                	<tr>
                        <th colspan="9"><b>Grey Issue Return Info</b></th>
                    </tr>
                    <tr>
                        <th width="40">SL</th>
                        <th width="120">Return Id</th>
                        <th width="100">Return Purpose</th>
                        <th width="120">Issue To</th>
                        <th width="105">Booking No</th>
                        <th width="80">Batch No</th>
                        <th width="80">Return Date</th>
                        <th width="100">Return Qnty (In)</th>
                        <th>Return Qnty (Out)</th>
                    </tr>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <? 
                    $i=1; $issue_to='';
					$sql="select a.id, a.recv_number, a.receive_date, a.receive_purpose, a.knitting_source, a.knitting_company, a.booking_no, a.batch_id, sum(c.quantity) as quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=51 and c.entry_form=51 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.receive_purpose, a.knitting_source, a.knitting_company, a.booking_no, a.batch_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $issue_to=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $issue_to=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $issue_to="&nbsp;";
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('rtr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="rtr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('receive_purpose')]]; ?></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="105"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="80"><p><? echo $batch_details[$row[csf('batch_id')]]; ?>&nbsp;</p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100" align="right">
								<?
                                    if($row[csf('knitting_source')]!=3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_return_qnty+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    if($row[csf('knitting_source')]==3)
                                    {
                                        echo number_format($row[csf('quantity')],2);
                                        $total_return_qnty_out+=$row[csf('quantity')];
                                    }
                                    else echo "&nbsp;";
                                ?>
                            </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_return_qnty,2); ?></th>
                            <th align="right"><? echo number_format($total_return_qnty_out,2); ?></th>
                        </tr>
                        <tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right" colspan="2"><? echo number_format((($total_issue_qnty+$total_issue_qnty_out)-($total_return_qnty+$total_return_qnty_out)),2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
      
<?
exit();
}

if($action=="dye_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no as machine_name from lib_machine_name","id","machine_name");
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Dyeing Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="70">System Id</th>
                    <th width="80">Process End Date</th>
                    <th width="100">Batch No</th>
                    <th width="70">Dyeing Source</th>
                    <th width="120">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th width="190">Fabric Description</th>
                    <th>Machine Name</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
					$i=1; $total_dye_qnty=0; $dye_company='';
					$sql="select a.batch_no, b.item_description as febric_description, sum(b.batch_qnty) as quantity, c.id, c.company_id, c.process_end_date, c.machine_id from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id='$color' and c.load_unload_id=2 and c.entry_form=35 and b.po_id in($order_id) and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.batch_no, b.item_description, c.id, c.company_id, c.process_end_date, c.machine_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
						$dye_company=$company_library[$row[csf('company_id')]]; 
                        $total_dye_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="70"><p><? echo $row[csf('id')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('process_end_date')]); ?>&nbsp;</td>
                            <td width="100"><p><? echo $row[csf('batch_no')];//$batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="70"><? echo "Inhouse";//echo $knitting_source[$row[csf('dyeing_source')]]; ?></td>
                            <td width="120"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td width="190"><p><? echo $row[csf('febric_description')]; ?></p></td>
                            <td><p>&nbsp;<? echo $machine_arr[$row[csf('machine_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="6" align="right">Total</th>
                        <th align="right"><? echo number_format($total_dye_qnty,2); ?></th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>  
<?
exit();
}

if($action=="fabric_receive")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=7 and c.entry_form=7 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
                    
                        $total_fabric_recv_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>   
<?
exit();
}

if($action=="fabric_purchase")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:885px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:880px; margin-left:3px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Rec. Date</th>
                    <th width="80">Rec. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Receive Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company=''; $recv_data_arr=array();
                    $sql="select a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=37 and c.entry_form=37 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.receive_basis<>9 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
                    
                        $total_fabric_recv_qnty+=$row[csf('quantity')];
						
						//$recv_data_arr[$row[csf('id')]]['sor']=$knitting_source[$row[csf('knitting_source')]];
						//$recv_data_arr[$row[csf('id')]]['com']=$dye_company;
						//$recv_data_arr[$row[csf('id')]]['basis']=$receive_basis_arr[$row[csf('receive_basis')]];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="80"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="7" align="right">Total</th>
                        <th align="right"><? echo number_format($total_fabric_recv_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
    
<!--Fabric Receive return info--> 
   
	<fieldset style="width:880px; margin-left:3px; margin-top:10px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Fabric Receive Return Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="120">System Id</th>
                    <th width="75">Ret. Date</th>
                    <th width="80">Ret. Basis</th>
                    <th width="90">Batch No</th>
                    <th width="90">Dyeing Source</th>
                    <th width="100">Dyeing Company</th>
                    <th width="90">Return Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <? 
					$sql_prod="select a.id, a.receive_basis, a.knitting_source, a.knitting_company from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(7,37) and c.entry_form in(7,37) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, a.receive_basis, a.knitting_source, a.knitting_company";
                    $resultProd=sql_select($sql_prod);
        			foreach($resultProd as $row)
                    {
						if($row[csf('knitting_source')]==1) 
                        {
                            $dye_company=$company_library[$row[csf('knitting_company')]]; 
                        }
                        else if($row['knitting_source']==3) 
                        {
                            $dye_company=$supplier_details[$row[csf('knitting_company')]];
                        }
                        else
                            $dye_company="&nbsp;";
							
						$recv_data_arr[$row[csf('id')]]['sor']=$knitting_source[$row[csf('knitting_source')]];
						$recv_data_arr[$row[csf('id')]]['com']=$dye_company;
						$recv_data_arr[$row[csf('id')]]['basis']=$receive_basis_arr[$row[csf('receive_basis')]];
					}
					
                    $i=1; $total_fabric_return_qnty=0;
                    $sql="select a.issue_number, a.issue_date, a.issue_basis, a.knit_dye_source, a.knit_dye_company, a.received_id, b.batch_id_from_fissuertn as batch_id, b.prod_id, sum(c.quantity) as quantity from inv_issue_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=46 and c.entry_form=46 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.issue_basis, a.received_id, a.knit_dye_source, a.knit_dye_company, b.batch_id_from_fissuertn, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

                        $source=$recv_data_arr[$row[csf('received_id')]]['sor'];
						$dye_company=$recv_data_arr[$row[csf('received_id')]]['com'];
						$basis=$recv_data_arr[$row[csf('received_id')]]['basis'];
						$batch=$batch_details[$row[csf('batch_id')]];
                    
                        $total_fabric_return_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('rtr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="rtr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="80"><? echo $basis; ?></td>
                            <td width="90"><p><? echo $batch; ?></p></td>
                            <td width="90"><? echo $source; ?></td>
                            <td width="100"><p><? echo $dye_company; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                    	<tr>
                            <th colspan="7" align="right">Total</th>
                            <th align="right"><? echo number_format($total_fabric_return_qnty,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    	<tr>
                            <th colspan="7" align="right">Grand Total</th>
                            <th align="right"><? echo number_format($total_fabric_recv_qnty-$total_fabric_return_qnty,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>    
       
<?
exit();
}

if($action=="issue_to_cut")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>	
	<div style="width:740px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:740px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue To Cutting Info</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue No</th>
                    <th width="80">Challan No</th>
                    <th width="80">Issue Date</th>
                    <th width="100">Batch No</th>
                    <th width="90">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_issue_to_cut_qnty=0; $issue_data_arr=array();
                    $sql="select a.id,a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=18 and c.entry_form=18 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.challan_no, b.batch_id, b.prod_id,a.id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
						
						$issue_data_arr[$row[csf('id')]]=$batch_details[$row[csf('batch_id')]];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="110"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="100"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
    
    <!-- Issue To Cutting return Info -->
    
    <fieldset style="width:740px; margin-left:7px; margin-top:10px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="730" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="7"><b>Issue Return Info</b></th>
				</thead>
				<thead>
                	<th width="40">SL</th>
                    <th width="110">Issue No</th>
                    <th width="80">Challan No</th>
                    <th width="80">Return Date</th>
                    <th width="100">Batch No</th>
                    <th width="90">Return Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
                    $i=1; $total_issue_to_cut_ret_qnty=0;
					$sql="select a.id,a.recv_number, a.receive_date, a.challan_no, b.prod_id, a.issue_id, sum(c.quantity) as quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form=52 and c.entry_form=52 and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.challan_no, b.prod_id,a.id,a.issue_id";
					$result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_ret_qnty+=$row[csf('quantity')];
						$batch=$issue_data_arr[$row[csf('issue_id')]];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('rtr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="rtr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="80"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="100"><p><? echo $batch; ?></p></td>
                            <td width="90" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                      <tr>
                        <th colspan="5" align="right">Total</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_ret_qnty,2); ?></th>
                        <th>&nbsp;</th>
                      </tr>
                        
                      <tr>
                        <th colspan="5" align="right">Grand Total</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty-$total_issue_to_cut_ret_qnty,2); ?></th>
                        <th>&nbsp;</th>
                      </tr>
                    </tfoot>
                </table>
            </div>	
        </div>
	</fieldset>
       
<?
exit();
}

if($action=="yarn_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=1 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=11 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="knit_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, sum(c.quantity) as transfer_qnty, d.product_name_details from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=13 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=13 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=13 and c.po_breakdown_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="finish_trans")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$po_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number"  );
?>
<script>

	function print_window()
	{
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
	}	
	
</script>	
	<div style="width:675px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="6">Transfer In</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">From Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $i=1; $total_trans_in_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=5 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.from_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('from_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_in_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_in_qnty,2);?></td>
                </tr>
                <thead>
                	<tr>
                    	<th colspan="6">Transfer Out</th>
                    </tr>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="115">Transfer Id</th>
                        <th width="80">Transfer Date</th>
                        <th width="100">To Order</th>
                        <th width="170">Item Description</th>
                        <th>Transfer Qnty</th>
                    </tr>
				</thead>
                <?
                $total_trans_out_qnty=0;
				$sql="select a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details, sum(c.quantity) as transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and a.item_category=2 and a.transfer_criteria=4 and c.trans_type=6 and c.entry_form=15 and c.po_breakdown_id in ($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id, c.prod_id, a.transfer_system_id, a.transfer_date, a.challan_no, a.to_order_id, b.from_prod_id, d.product_name_details";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="115"><p><? echo $row[csf('transfer_system_id')]; ?></p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('transfer_date')]); ?></td>
                        <td width="100"><p><? echo $po_arr[$row[csf('to_order_id')]]; ?></p></td>
                        <td width="170"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('transfer_qnty')],2); ?> </td>
                    </tr>
                <?
					$total_trans_out_qnty+=$row[csf('transfer_qnty')];
                $i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="right">Total</td>
                    <td align="right"><? echo number_format($total_trans_out_qnty,2); ?></td>
                </tr>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Net Transfer</th>
                    <th><? echo number_format($total_trans_in_qnty-$total_trans_out_qnty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

?>
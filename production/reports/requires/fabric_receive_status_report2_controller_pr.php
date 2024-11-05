<? 
header('Content-type:text/html; charset=utf-8');
session_start();

//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');
require_once('../../../includes/class3/class.fabrics.php');


$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
// finish fab order to order transfer problem
$color_array=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
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
	echo "Report Under Construction. if you need Please input one job no.";
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
		
		$file_no = str_replace("'","",$txt_file_no);
		$internal_ref = str_replace("'","",$txt_internal_ref);
		if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and b.file_no='".trim($file_no)."' "; 
		if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and b.grouping='".trim($internal_ref)."' "; 
		//echo $internal_ref_cond.'='.$file_no_cond;
		
		
		$txt_search_string=str_replace("'","",$txt_search_string);
		
		
		if(trim($txt_search_string)!="") $search_string="%".trim($txt_search_string)."%"; else $search_string="%%";
		if(trim($txt_search_string)!="")
		{
			if($type==1)
			{
				if($db_type==0)
				{
					$po_style_src_cond=return_field_value("group_concat(id) as po_id","wo_po_break_down","po_number like '$search_string'","po_id");
				}
				else if($db_type==2)
				{
					$po_style_src_cond=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as po_id","wo_po_break_down","po_number like '$search_string'","po_id");
				}
			}
			else
			{
				if($db_type==0)
				{
					$po_style_src_cond=return_field_value("group_concat(b.id) as po_id","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and a.style_ref_no like '$search_string'","po_id");
				}
				else if($db_type==2)
				{
					$po_style_src_cond=return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and a.style_ref_no like '$search_string'","po_id");
				}
			}
		}
		else
		{
			$po_style_src_cond="";
		}
		
		$txt_job_no=str_replace("'","",$txt_job_no);
		
		$cbo_year=str_replace("'","",$cbo_year);
		if(trim($cbo_year)!=0) 
		{
			if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$cbo_year";
			else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
			else $year_cond="";
		}
		else $year_cond="";
		
		$job_src_cond="";
		if(trim($txt_job_no)!="")
		{
			if($db_type==0)
			{
				$job_src_cond=return_field_value("group_concat(b.id) as po_id","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and a.job_no_prefix_num='$txt_job_no' $year_cond","po_id");
			}
			else if($db_type==2)
			{
				$job_src_cond=return_field_value("LISTAGG(b.id, ',') WITHIN GROUP (ORDER BY b.id) as po_id","wo_po_details_master a,wo_po_break_down b","a.job_no=b.job_no_mst and a.job_no_prefix_num='$txt_job_no' $year_cond","po_id");
			}
		}
		
		$job_no_cond="";
		if(trim($txt_job_no)!="")
		{
			$job_no=trim($txt_job_no); 
			$job_no_cond=" and a.job_no_prefix_num=$job_no";
		}
		
		if($po_style_src_cond!="" || $job_src_cond!="")
		{
			
			if($po_style_src_cond!="") $po_all_src=$po_style_src_cond; 
			else if($job_src_cond!="") $po_all_src=$job_src_cond; else $po_all_src="";
			
			$yarn_iss_po_cond=" and a.po_breakdown_id in ($po_all_src)";
			$yarn_allo_po_cond=" and a.po_break_down_id in ($po_all_src)";
			$grey_purchase_po_cond=" and c.po_breakdown_id in ($po_all_src)";
			$grey_delivery_po_cond=" and order_id in ($po_all_src)";
			$fin_delivery_po_cond=" and a.order_id in ($po_all_src)";
			$trans_po_cond=" and po_breakdown_id in ($po_all_src)";
			$fin_purchase_po_cond=" and c.po_breakdown_id in ($po_all_src)";
			$po_color_po_cond=" and po_breakdown_id in ($po_all_src)";
			$batch_po_cond=" and b.po_id in ($po_all_src)";
			$dye_po_cond=" and b.po_id in ($po_all_src)";
			$wo_po_cond=" and b.po_break_down_id in ($po_all_src)";
			$sql_po_cond=" and b.id in ($po_all_src)";
			$cons_po_cond=" and b.po_break_down_id in ($po_all_src)";
			$country_po_cond=" and po_break_down_id in ($po_all_src)";
			$tna_po_cond=" and po_number_id in( $po_all_src )";
		}
		else
		{
			$yarn_iss_po_cond="";
			$yarn_allo_po_cond="";
			$grey_purchase_po_cond="";
			$grey_delivery_po_cond="";
			$fin_delivery_po_cond="";
			$trans_po_cond="";
			$fin_purchase_po_cond="";
			$po_color_po_cond="";
			$batch_po_cond="";
			$dye_po_cond="";
			$wo_po_cond="";
			$sql_po_cond="";
			$cons_po_cond="";
			$country_po_cond="";
			$tna_po_cond="";
		}
		
		//echo $po_style_src_cond; die;
		$start_date=str_replace("'","",trim($txt_date_from));
		$end_date=str_replace("'","",trim($txt_date_to));
		
		/*if($start_date!="" && $end_date!="")
		{
			$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else
			$str_cond="";*/
			
		$start_date_rec=str_replace("'","",trim($txt_date_from_rec));
		$end_date_rec=str_replace("'","",trim($txt_date_to_rec));
		
		if($start_date_rec!="" && $end_date_rec!="")
		{
			$date_rec_cond="and b.po_received_date between '$start_date_rec' and '$end_date_rec'";
		}
		else
			$date_rec_cond="";	
		
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
		/*echo "<br/>";
		echo ini_get('max_execution_time');
		echo "<br/>";
		echo ini_get('memory_limit'). "<br/>";
		ini_set('memory_limit','4072M');
		echo ini_get('memory_limit'). "<br/>";
		echo (memory_get_usage()/1024)/1024;
		echo "<br/>";*/
	
				
				//die;
		
		$dataArrayYarn=array(); $dataArrayYarnIssue=array();
		$yarn_sql="select job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id, sum(avg_cons_qnty) as qnty from wo_pre_cost_fab_yarn_cost_dtls where status_active=1 and is_deleted=0 group by job_no, count_id, copm_one_id, percent_one, copm_two_id, percent_two, type_id";
		$resultYarn=sql_select($yarn_sql);
		foreach($resultYarn as $yarnRow)
		{
			$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_two')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('qnty')].",";
		}
		//print_r($dataArrayYarn);
		
		$sql_yarn_iss="select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
				sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
				sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
				from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 $yarn_iss_po_cond group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
		$dataArrayIssue=sql_select($sql_yarn_iss);
		foreach($dataArrayIssue as $row_yarn_iss)
		{
			$dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]].=$row_yarn_iss[csf('yarn_count_id')]."**".$row_yarn_iss[csf('yarn_comp_type1st')]."**".$row_yarn_iss[csf('yarn_comp_percent1st')]."**".$row_yarn_iss[csf('yarn_comp_type2nd')]."**".$row_yarn_iss[csf('yarn_comp_percent2nd')]."**".$row_yarn_iss[csf('yarn_type')]."**".$row_yarn_iss[csf('issue_qnty')]."**".$row_yarn_iss[csf('return_qnty')].",";
		}
		unset($dataArrayIssue);
		$yarnAllocationArr=array(); //$yarnAllocationJobArr=array();
		$sql_yarn_allocation="select a.po_break_down_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
				sum(a.qnty) AS allocation_qty
				from inv_material_allocation_dtls a, product_details_master b where a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $yarn_allo_po_cond group by a.po_break_down_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
		$dataArrayAllocation=sql_select($sql_yarn_allocation);
		foreach($dataArrayAllocation as $allocationRow)
		{
			$yarnAllocationArr[$allocationRow[csf('po_break_down_id')]].=$allocationRow[csf('yarn_count_id')]."**".$allocationRow[csf('yarn_comp_type1st')]."**".$allocationRow[csf('yarn_comp_percent1st')]."**".$allocationRow[csf('yarn_comp_type2nd')]."**".$allocationRow[csf('yarn_comp_percent2nd')]."**".$allocationRow[csf('yarn_type')]."**".$allocationRow[csf('allocation_qty')].",";
		}
		unset($dataArrayAllocation);
		
		$greyPurchaseQntyArray=array();
		$sql_grey_purchase="select c.po_breakdown_id, 
		sum(CASE WHEN a.receive_basis<>9 THEN c.quantity ELSE 0 END) AS grey_purchase_qnty,
		sum(CASE WHEN a.receive_basis=9 THEN c.quantity ELSE 0 END) AS grey_production_qnty
		from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form=22 and c.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $grey_purchase_po_cond group by c.po_breakdown_id";//and a.receive_basis<>9 sum(c.quantity) as grey_purchase_qnty
		$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
		foreach($dataArrayGreyPurchase as $greyRow)
		{
			$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]['purchase']=$greyRow[csf('grey_purchase_qnty')];
			$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]['production']=$greyRow[csf('grey_production_qnty')];
		}
		unset($dataArrayGreyPurchase);
		$greyDeliveryArray=array();
		$sql_grey_delivery="select order_id, sum(current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 $grey_delivery_po_cond group by order_id";
		$data_grey_delivery=sql_select($sql_grey_delivery);
		foreach($data_grey_delivery as $greyDel)
		{
			$greyDeliveryArray[$greyDel[csf('order_id')]]=$greyDel[csf('grey_delivery_qty')];
		}
		unset($data_grey_delivery);
		//var_dump($greyDeliveryArray);
		$finDeliveryArray=array();
		$sql_fin_delivery="select a.order_id, b.color, sum(a.current_delivery) as fin_delivery_qty from pro_grey_prod_delivery_dtls a, product_details_master b where a.product_id=b.id and a.entry_form in(54,67) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $fin_delivery_po_cond group by a.order_id, b.color";
		
		$data_fin_delivery=sql_select($sql_fin_delivery);
		foreach($data_fin_delivery as $finDel)
		{
			$finDeliveryArray[$finDel[csf('order_id')]][$finDel[csf('color')]]=$finDel[csf('fin_delivery_qty')];
		}
		unset($data_fin_delivery);
		
		$trans_qnty_arr=array(); $grey_receive_qnty_arr=array(); $grey_issue_qnty_arr=array(); $grey_receive_return_qnty_arr=array(); $grey_issue_return_qnty_arr=array();
								
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN entry_form ='45' and trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,

								sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
								sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_rollwise,
								sum(CASE WHEN entry_form ='51' and trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return,
								
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN entry_form ='80' and trans_type=6 THEN quantity ELSE 0 END) AS trans_out_sample_knit,
								sum(CASE WHEN entry_form ='81' and trans_type=5 THEN quantity ELSE 0 END) AS trans_in_sample_knit
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,11,13,16,45,51,61,80,81) $trans_po_cond group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans']=$row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans']=($row[csf('transfer_in_qnty_knit')]+$row[csf('trans_in_sample_knit')])-($row[csf('transfer_out_qnty_knit')]+$row[csf('trans_out_sample_knit')]);
			
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue')]+$row[csf('grey_issue_rollwise')];
			
			$grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive_return')];
			$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue_return')];
		}
		unset($dataArrayTrans);
		
		$trans_qnty_fin_arr=array(); $finish_receive_qnty_arr=array(); $finish_purchase_qnty_arr=array(); $finish_issue_qnty_arr=array(); $finish_recv_rtn_qnty_arr=array(); $finish_issue_rtn_qnty_arr=array();
		
		$dataArrayTrans=sql_select("select po_breakdown_id, color_id, 
								sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
								sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_rollwise,
								sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS finish_purchase,
								sum(CASE WHEN entry_form ='68' THEN quantity ELSE 0 END) AS finish_purchase_rollwise,
								sum(CASE WHEN entry_form ='18' THEN quantity ELSE 0 END) AS finish_issue,
								sum(CASE WHEN entry_form ='71' THEN quantity ELSE 0 END) AS finish_issue_roll_wise,
								sum(CASE WHEN entry_form ='46' and trans_type=3 THEN quantity ELSE 0 END) AS recv_rtn_qnty,
								sum(CASE WHEN entry_form ='52' and trans_type=4 THEN quantity ELSE 0 END) AS iss_retn_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,15,18,37,46,52,66,68,71) $trans_po_cond group by po_breakdown_id, color_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']=$row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')];
			$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_rollwise')];
			//$finish_purchase_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_purchase')];
			$finish_issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_issue')]+$row[csf('finish_issue_roll_wise')];
			
			$finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('recv_rtn_qnty')];
			$finish_issue_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('iss_retn_qnty')];
		}
		unset($dataArrayTrans);
		$sql_fin_purchase="select c.po_breakdown_id, c.color_id, 
		sum(CASE WHEN a.receive_basis<>9 THEN c.quantity ELSE 0 END) AS finish_purchase,
		sum(CASE WHEN a.receive_basis=9 THEN c.quantity ELSE 0 END) AS fin_production_qnty

		 from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(37,68) and c.entry_form in(37,68) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $fin_purchase_po_cond group by c.po_breakdown_id, c.color_id";//and a.receive_basis<>9 sum(c.quantity) as finish_purchase
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]['purchase']=$finRow[csf('finish_purchase')];
			$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]][$finRow[csf('color_id')]]['production']=$finRow[csf('fin_production_qnty')];
		}
		unset($dataArrayFinPurchase);
		//var_dump($finish_purchase_qnty_arr); 
		
		if($db_type==0)
		{
			$po_color_arr=return_library_array( "select po_breakdown_id, group_concat(distinct(color_id)) as color_id from order_wise_pro_details where entry_form in(7,18,37,66,68,71) and color_id<>0 $color_cond_prop $po_color_po_cond group by po_breakdown_id", "po_breakdown_id", "color_id");
		}
		else
		{
			$po_color_arr=return_library_array( "select po_breakdown_id, LISTAGG(cast(color_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY color_id) color_id from order_wise_pro_details where entry_form in(7,18,37,66,68,71) and color_id<>0 $color_cond_prop $po_color_po_cond group by po_breakdown_id", "po_breakdown_id", "color_id");
		}
		
		$batch_qnty_arr=array();
		$sql_batch="select a.color_id, b.po_id, sum(b.batch_qnty) as batch_qnty from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.entry_form!=36 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_po_cond group by a.color_id, b.po_id";
		$resultBatch=sql_select($sql_batch);
		foreach($resultBatch as $batchRow)
		{
			$batch_qnty_arr[$batchRow[csf('po_id')]][$batchRow[csf('color_id')]]=$batchRow[csf('batch_qnty')];
		}
		unset($resultBatch);
		//var_dump($batch_qnty_arr); die;
		//$dye_qnty_arr=return_library_array( "select b.po_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and a.color_id='$key' and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.po_id", "po_id", "dye_qnty");
		
		$dye_qnty_arr=array();
		$sql_dye="select b.po_id, a.color_id, sum(b.batch_qnty) as dye_qnty from pro_batch_create_mst a,pro_batch_create_dtls b, pro_fab_subprocess c where a.id=b.mst_id and a.id=c.batch_id and c.load_unload_id=2 and c.entry_form=35 and a.batch_against<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $dye_po_cond group by b.po_id, a.color_id";
		$resultDye=sql_select($sql_dye);
		foreach($resultDye as $dyeRow)
		{
			$dye_qnty_arr[$dyeRow[csf('po_id')]][$dyeRow[csf('color_id')]]=$dyeRow[csf('dye_qnty')];
		}
		unset($resultDye);
		
		$dataArrayWo=array();
		$sql_wo="select b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id, sum(b.fin_fab_qnty) as req_qnty, sum(b.grey_fab_qnty) as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $color_cond_search $wo_po_cond group by b.po_break_down_id, a.id, a.booking_no, a.insert_date, a.item_category, a.fabric_source, a.company_id, a.booking_type, a.booking_no_prefix_num, a.job_no, a.is_short, a.is_approved, b.fabric_color_id";
		$resultWo=sql_select($sql_wo);
		foreach($resultWo as $woRow)
		{
			$dataArrayWo[$woRow[csf('po_break_down_id')]].=$woRow[csf('id')]."**".$woRow[csf('booking_no')]."**".$woRow[csf('insert_date')]."**".$woRow[csf('item_category')]."**".$woRow[csf('fabric_source')]."**".$woRow[csf('company_id')]."**".$woRow[csf('booking_type')]."**".$woRow[csf('booking_no_prefix_num')]."**".$woRow[csf('job_no')]."**".$woRow[csf('is_short')]."**".$woRow[csf('is_approved')]."**".$woRow[csf('fabric_color_id')]."**".$woRow[csf('req_qnty')]."**".$woRow[csf('grey_req_qnty')].",";
		}
		unset($resultWo);
		$tna_tsk_arr=array(50,60,61,73);
		$tna_plan_actual_arr=array();
		$tna_sql="select po_number_id";
		$i=1;
		if($db_type==0)
		{
			foreach( $tna_tsk_arr as $dval)    	
			{
				$tna_sql .=", max(CASE WHEN CONCAT(task_number) = '".$dval."' THEN concat(task_finish_date,'_',actual_finish_date)  END ) as status_$dval ";
			}
		}
		else if ($db_type==2)
		{
			foreach( $tna_tsk_arr as $dval)    	
			{
				$tna_sql .=", max(CASE WHEN task_number = '".$dval."' THEN task_finish_date || '_' || actual_finish_date END ) as status_$dval ";
			}
		}
		$tna_sql .=" from tna_process_mst where is_deleted=0 and status_active=1 $tna_po_cond group by po_number_id";
		//echo $tna_sql;
		$tna_sql_result = sql_select($tna_sql);
		foreach($tna_sql_result as $tnaVal)
		{
			foreach( $tna_tsk_arr as $dval)
			{
				$tna_date=explode('_',$tnaVal[csf('status_'.$dval)]);
				$plan_fin_date=""; $actual_fin_date="";
				if($tna_date[0]=="" || $tna_date[0]=='0000-00-00') $plan_fin_date=""; else $plan_fin_date=date("Y-m-d",strtotime($tna_date[0]));
				if($tna_date[1]=="" || $tna_date[1]=='0000-00-00') $actual_fin_date=""; else $actual_fin_date=date("Y-m-d",strtotime($tna_date[1]));
				$tna_plan_actual_arr[$tnaVal[csf('po_number_id')]][$dval]['plan']=$plan_fin_date;
				$tna_plan_actual_arr[$tnaVal[csf('po_number_id')]][$dval]['actual']=$actual_fin_date;
			}
		}
		unset($tna_sql_result);
		
		$contry_ship_qty_arr=array();
		if($db_type==0) $all_country_date="group_concat(country_ship_date)";
		else if($db_type==2) $all_country_date="listagg((cast(country_ship_date as varchar2(4000))),',') within group (order by country_ship_date)";
		if ($start_date=="" && $end_date=="") $country_ship_date_cond=""; else $country_ship_date_cond="and country_ship_date between '$start_date' and '$end_date'";	
		$country_ship_qty_sql="select po_break_down_id, $all_country_date as country_ship_date, sum(order_quantity) as ship_qty from  wo_po_color_size_breakdown where  status_active=1 and is_deleted=0 $country_ship_date_cond $country_po_cond group by po_break_down_id";
		$country_ship_qty_sql_result=sql_select($country_ship_qty_sql);
		foreach($country_ship_qty_sql_result as $row )
		{
			$country_ship_date=implode(",",array_unique(explode(",",$row[csf('country_ship_date')])));
			$contry_ship_qty_arr[$row[csf('po_break_down_id')]]['ship_qty']=$row[csf('ship_qty')];
			$contry_ship_qty_arr[$row[csf('po_break_down_id')]]['ship_date']=$country_ship_date;
		}
		unset($country_ship_qty_sql_result);
		//var_dump($contry_ship_qty_arr);
		$tot_order_qnty=0; $tot_mkt_required=0; $tot_yarn_issue_qnty=0; $tot_balance=0; $tot_fabric_req=0; $tot_grey_recv_qnty=0; $tot_grey_balance=0; $tot_grey_available=0; 
		$tot_grey_issue=0; $tot_batch_qnty=0; $tot_color_wise_req=0; $tot_dye_qnty=0; $tot_fabric_recv=0; $tot_fabric_purchase=0; $tot_fabric_balance=0; $tot_issue_to_cut_qnty=0;
		$tot_fabric_available=0; $tot_fabric_left_over=0; $tot_fabric_left_over_excel=0; $tot_fabric_recv_excel=0;$tot_batch_qnty_excel=0;$tot_grey_prod_balance=0;$total_grey_del_store=0; $tot_net_trans_knit_qnty=0; $tot_country_ship_qty=0;
		
		$buyer_name_array= array(); $order_qty_array= array(); $grey_required_array= array(); $yarn_issue_array= array(); $grey_issue_array= array(); 
		$fin_fab_Requi_array= array(); $fin_fab_recei_array= array(); $issue_to_cut_array= array(); $yarn_balance_array= array(); 
		$grey_balance_array= array(); $fin_balance_array= array(); $knitted_array=array(); $dye_qnty_array=array(); $batch_qnty_array=array(); $issue_toCut_array=array();
		if ($start_date=="" && $end_date=="") $country_date_cond=""; else $country_date_cond="and c.country_ship_date between '$start_date' and '$end_date'";
		
		if($type==1)
		{
			$table_width="6230"; $colspan="19";
			$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio,b.grouping,b.file_no, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.is_confirmed from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and a.company_name='$company_name' and b.id=c.po_break_down_id and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $buyer_id_cond $str_cond  $country_date_cond $str_cond_insert $date_rec_cond $year_cond $job_no_cond $sql_po_cond $internal_ref_cond $file_no_cond group by a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty, b.grouping,b.file_no, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.is_confirmed order by b.pub_shipment_date, b.id";	
		
		}
		else
		{
			$table_width="5970"; $colspan="16";
			if($db_type==0)
			{
				$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, group_concat(b.id) as po_id, group_concat(b.grouping) as grouping, group_concat(b.file_no) as file_no, group_concat(b.po_number) as po_number, group_concat(b.is_confirmed) as is_confirmed, sum(b.po_quantity) as po_qnty, sum(b.plan_cut) as plan_cut from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name='$company_name' and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $buyer_id_cond $str_cond $country_date_cond $str_cond_insert $date_rec_cond $year_cond $job_no_cond $sql_po_cond $internal_ref_cond $file_no_cond group by a.job_no";
			}
			else
			{
				$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, 
				LISTAGG(cast(b.id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_id, 
				LISTAGG(cast(b.po_number as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.id) as po_number, 
				LISTAGG(cast(b.grouping as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.grouping) as grouping, 
				LISTAGG(cast(b.file_no as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.file_no) as file_no,
				LISTAGG(cast(b.is_confirmed as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.is_confirmed) as is_confirmed,
				sum(b.po_quantity) as po_qnty, sum(b.plan_cut) as plan_cut from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name='$company_name' and b.shiping_status like '$shipping_status' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $buyer_id_cond $str_cond  $country_date_cond $str_cond_insert $date_rec_cond $year_cond $job_no_cond $sql_po_cond $internal_ref_cond $file_no_cond group by a.job_no, a.company_name, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty";	
			}
		}
		
		$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");
		//echo $sql;die;
		//echo (memory_get_usage()/1024)/1024;
		//echo "<br/>";
		$condition= new condition();
		 $condition->company_name("=$company_name");
		 if(str_replace("'","",$cbo_buyer_name)>0){
			  $condition->buyer_name("=$cbo_buyer_name");
		 }
		 if(str_replace("'","",$txt_job_no) !=''){
			  $condition->job_no_prefix_num("=$txt_job_no");
		 }
		 if( str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
			  $condition->country_ship_date(" between '$start_date' and '$end_date'");
		 }
		 /*if(str_replace("'","",$txt_search_string)!='')
		 {
			 if($type==1) $condition->po_number("=$txt_search_string"); 
			 else if ($type==2) $condition->style_ref_no("=$txt_search_string"); 
		 }*/
		 
		//$yarn= new yarn($condition);
		$condition->init();
		$yarn= new yarn($condition);
		echo $yarn->getQuery(); //die;
		$yarn_qty_arr=$yarn->getOrderCountCompositionColorAndTypeWiseYarnQtyArray();
		//$yarn_qty_arr_job=$yarn->getJobWiseYarnQtyArray();
		$yarn_des_data=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyArray();
		$yarn_des_data_job=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyArray();
		
		$fabric= new fabric($condition);
		echo $fabric->getQuery(); die;
		$fabric_costing_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
		//echo (memory_get_usage()/1024)/1024;
		//echo "<br/>";
		
		
		ob_start();
		?>
        <fieldset style="width:<? echo $table_width+30; ?>px;">	
            <table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+43; ?>" style="font-size:16px"><strong><?php echo $company_library[$company_name]; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="<? echo $colspan+43; ?>" style="font-size:16px"><strong><? if($start_date!="" && $end_date!="") echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header_1">
                <thead>
                    <tr>
                        <th rowspan="2" width="40">SL</th>
                        <th colspan="<? echo $colspan; ?>">Order Details</th>
                        <th colspan="9">Yarn Status</th>
                        <th colspan="5">Knitting Production</th>
                        <th colspan="7">Grey Fabric Store</th>
                        <th colspan="4">Deying Production</th>
                        <th colspan="5">Finish Fabric Production</th>
                        <th colspan="9">Finish Fabric Store</th>
                        <th rowspan="2" width="100">N/A<!--Finish Fab. Cons/DZN--></th>
                        <th rowspan="2">Fabric Description</th>
                    </tr>
                    <tr>
                        <th width="125">Main Fabric Booking No</th>
                        <th width="125">Sample Fabric Booking No</th>
                        <th width="100">Job Number</th>
                        <th width="120">Order Number</th>
                        <th width="90">Order Status</th>
                        <th width="80">Buyer Name</th>
                        <th width="130">Style Ref.</th>
                        <th width="100">File No.</th>
                        <th width="100">Internal Ref.</th>
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
                        <th width="80">Country Ship Date</th>
                        <th width="100">Country Ship Qty.</th>
                        <th width="100" title="Total Grey Req. Qty/ Plancut Qty. (Pcs.)">Avg Grey Cons./Pcs</th>
                        <th width="100" title="Total Fin. Req. Qty/ Plancut Qty. (Pcs.)">Avg. Finish Cons./Pcs</th>
                        <th width="70">Count</th>
                        <th width="110">Composition</th>
                        <th width="80">Type</th>
                        <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                        <th width="100">Allocated</th>
                        <th width="100">Yet to Allocate</th>
                        <th width="100">Issued</th>
                        <th width="100">Net Transfer</th>
                        <th width="100">Balance<br/><font style="font-size:9px; font-weight:100">(Grey Req-(Yarn Issue+Net Transfer))</font></th>
                        
                        <th width="100">Required<br/><font style="font-size:9px; font-weight:100">(As Per Booking)</font></th>
                        <th width="100">Knitting Production</th>
                        <th width="100">Knitting Balance<br/><font style="font-size:9px; font-weight:100">(Grey Req-Prod)</font></th>
                        <th width="100">Grey Fab Delv. To Store</th>
                        <th width="100">Grey in Knit Floor</th>
                        
                        <th width="100">Grey Recv.-Production</th>
                        <th width="100">Grey Recv.-Purchase</th>
                        <th width="100">Net Return</th>
                        <th width="100">Net Transfer</th>
                        <th width="100">Fabric Available</th>
                        <th width="100">Receive Balance</th>
                        <th width="100">Grey Issue</th>
                        
                        <th width="100">Fabric Color
                        	<input type="text" name="txt_fab_color" onkeyup="show_inner_filter(event);" value="<? echo str_replace("'","",$txt_fab_color); ?>" id="txt_fab_color" class="text_boxes" style="width:85px" />
                        </th>
                        <th width="100">Batch Qnty</th>
                        <th width="100">Dye Qnty</th>
                        <th width="100">Balance Qnty</th>
                        
                        <th width="100">Req. Qty (As Per Booking)</th>
                        <th width="100">Production Qty</th>
                        <th width="100">Balance Qty</th>
                        <th width="100">Finish Fab. Delv. To Store</th>
                        <th width="100">Fabric in Prod. Floor</th>
                        
                        <th width="100">Received - Prod.</th>
                        <th width="100">Received - Purchase</th>
                        <th width="100">Net Return</th>
                        <th width="100">Net Transfer</th>
                        <th width="100">Fabric Available</th>
                        <th width="100">Receive Balance</th>
                        <th width="100">Issue to Cutting </th>
                        <th width="100">Yet to Issue</th>
                        <th width="100">Fabric Stock/ Left Over</th>
                    </tr>
                </thead>
            </table>
            <? 
			$html="";
			$colspan_excel=$colspan+40;
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
			$html.="</table><table border='1' rules='all'>
					<thead>
						<tr>
							<th rowspan='2' width='40'>SL</th>
							<th colspan='$colspan'>Order Details</th>
							<th colspan='9'>Yarn Status</th>
							<th colspan='5'>Knitting Production</th>
							<th colspan='7'>Grey Fabric Store</th>
							<th colspan='4'>Deying Production</th>
							<th colspan='5'>Finish Fabric Production</th>
							<th colspan='9'>Finish Fabric Store</th>
							<th rowspan='2'>N/A</th>
							<th rowspan='2'>Fabric Description</th>
						</tr>
						<tr>
							<th>Main Fabric Booking No</th>
							<th>Sample Fabric Booking No</th>
							<th>Job Number</th>
							<th>Order Number</th>
							<th>Order Status</th>
							<th>Buyer Name</th>
							<th>Style Ref.</th>
							<th>File No.</th>
							<th>Internal Ref.</th>
							<th>Item Name</th>
							<th>Order Qnty</th>
							<th>Shipment Date</th>";
			
			if($type==1)
			{				
				$html.="<th>PO Received Date</th>
						<th>Po Entry Date</th>
						<th>Shipping Status</th>";
			}
			
			$html.="
					<th>Country Ship Date</th>
					<th>Country Ship Qty.</th>
					<th>Avg Grey Cons./Pcs</th>
					<th>Avg. Finish Cons./Pcs</th>
					<th>Count</th>
					<th>Composition</th>
					<th>Type</th>
					<th>Required<br/><font style='font-size:9px; font-weight:100'>(As Per Pre-Cost)</font></th>
					<th>Allocated</th>
					<th>Yet to Allocate</th>
					<th>Issued</th>
					<th>Net Transfer</th>
					<th>Balance<br/><font style='font-size:9px; font-weight:100'>(Grey Req-(Yarn Issue+Net Transfer))</font></th>
					
					<th>Required<br/><font style='font-size:9px; font-weight:100'>(As Per Booking)</font></th>
					<th>Knitting Production</th>
					<th>Knitting Balance<br/><font style='font-size:9px; font-weight:100'>(Grey Req-Prod)</font></th>
					<th>Grey Fab Delv. To Store</th>
					<th>Grey in Knit Floor</th>
					
					<th>Grey Recv.-Production</th>
					<th>Grey Recv.-Purchase</th>
					<th>Net Return</th>
					<th>Net Transfer</th>
					<th>Fabric Available</th>
					<th>Receive Balance</th>
					<th>Grey Issue</th>
					
					<th>Fabric Color</th>
					<th>Batch Qnty</th>
					<th>Dye Qnty</th>
					<th>Balance Qnty</th>
					
					<th>Req. Qty (As Per Booking)</th>
					<th>Production Qty</th>
					<th>Balance Qty</th>
					<th>Finish Fab. Delv. To Store</th>
					<th>Fabric in Prod. Floor</th>
					
					<th>Received - Prod.</th>
					<th>Received - Purchase</th>
					<th>Net Return</th>
					<th>Net Transfer</th>
					<th>Fabric Available</th>
					<th>Receive Balance</th>
					<th>Issue to Cutting </th>
					<th>Yet to Issue</th>
					<th>Fabric Stock/ Left Over</th>
				</tr>
			</thead>";	
					
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
							</thead>";
				?>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:400px" id="scroll_body">
                <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
                <? 
				//echo $sql;
				$nameArray=sql_select($sql);
				/*echo (memory_get_usage()/1024)/1024;
		echo "<br/>";*/
		$m=1;
				/*foreach($nameArray as $row)
					{
						echo $m."<br/>";
						$m++;
					}
					die;*/
				//print_r($fabric_costing_arr);
				//die;
				$k=1; $i=1; 
				if($type==1)
				{
					foreach($nameArray as $row)
					{
						
						$template_id=$template_id_arr[$row[csf('po_id')]];
						
						$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
						$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
						$order_qty_array[$row[csf('buyer_name')]]+=$order_qnty_in_pcs;
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

				
						$yarn_data_array=array(); $mkt_required_array=array(); $yarn_allocation_arr=array(); $yetTo_allocate_arr=array(); $req_for_allocate_arr=array(); $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array(); 
						$s=1;
						/*$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
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
							
							//$yarnAllocationQty=$yarnAllocationArr[$row[csf('po_id')]];
							//$yarn_allocation_arr[$s]=$yarnAllocationQty;//[$allocationRow[csf('item_id')]]
							//$yetTo_allocate=$mkt_required-$yarnAllocationArr[$row[csf('po_id')]];
							//$yetTo_allocate_arr[$s]=$yetTo_allocate;
							//$job_yarnAllocationQty+=$yarnAllocationQty;
							//$job_yetTo_allocate+=$yetTo_allocate;
							
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
							$des_for_allocation=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							
							$req_for_allocate_arr[$des_for_allocation]=$mkt_required;
							
							$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
							$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
							
							$s++;
						}*/
						$yarn_descrip_data=$yarn_des_data[$row[csf('po_id')]];
						//print_r($yarn_des_data[$row[csf('po_id')]]);
						$qnty=0;
						foreach($yarn_descrip_data as $count=>$count_value)
						{
						 foreach($count_value as $Composition=>$composition_value)
                               {
							
							foreach($composition_value as $percent=>$percent_value)
                               {	
							  foreach($percent_value as $y_type=>$type_value)
                                {
							//$yarnRow=explode("**",$yarnRow);
							$count_id=$count;//$yarnRow[0];
							$copm_one_id=$Composition;//$yarnRow[1];
							$percent_one=$percent;//$yarnRow[2];
							//$copm_two_id=$yarnRow[3];
							//$percent_two=$yarnRow[4];
							$type_id=$y_type;//$yarnRow[5];
							$qnty=$type_value;//$yarnRow[6];
							
							$mkt_required=$qnty;//$plan_cut_qnty*($qnty/$dzn_qnty);
							$mkt_required_array[$s]=$mkt_required;
							
							//$yarnAllocationQty=$yarnAllocationArr[$row[csf('po_id')]];
							//$yarn_allocation_arr[$s]=$yarnAllocationQty;//[$allocationRow[csf('item_id')]]
							//$yetTo_allocate=$mkt_required-$yarnAllocationArr[$row[csf('po_id')]];
							//$yetTo_allocate_arr[$s]=$yetTo_allocate;
							//$job_yarnAllocationQty+=$yarnAllocationQty;
							//$job_yetTo_allocate+=$yetTo_allocate;
							
							$job_mkt_required+=$mkt_required;
							
							
							$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][$s]=$yarn_type[$type_id];
							
							/*if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}*/
							/*if($percent_one!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];	
							}
							else
							{*/
								
								$compos=$composition[$copm_one_id]." ".$percent_one."%".$composition[$copm_two_id];
							//}

							$yarn_data_array['comp'][]=$compos;
							
							$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							$des_for_allocation=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							
							$req_for_allocate_arr[$des_for_allocation]=$mkt_required;
							
							$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
							$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
							
							$s++;
								 }
								}
							   }
							  
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
						
						$grey_purchase_qnty=$greyPurchaseQntyArray[$row[csf('po_id')]]['purchase'];
						$grey_production_qnty=$greyPurchaseQntyArray[$row[csf('po_id')]]['production'];
						
						$grey_issue_rtn=$grey_issue_return_qnty_arr[$row[csf('po_id')]];
						$grey_rec_rtn=$grey_receive_return_qnty_arr[$row[csf('po_id')]];
						
						$grey_net_return=$grey_issue_rtn-$grey_rec_rtn;
						
						$grey_recv_qnty=$grey_receive_qnty_arr[$row[csf('po_id')]];
						$grey_fabric_issue=$grey_issue_qnty_arr[$row[csf('po_id')]];
						
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
											//$sample_booking.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
											$sample_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('3','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font></a><br>";
											$sample_booking_excel.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")"."</font><br>";
										}
										else
										{
											if($is_short==1) $pre="S"; else $pre="M"; 
											//$main_booking.="<font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font><br>";
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
							
							foreach($batch_qnty_arr as $pId=>$batch_val)
							{
								foreach($batch_val as $colorId=>$batchVal)
								{
									if($colorId>0)
									{
										$color_data_array[$colorId]+=0;
									}
								}
							}
							//var_dump($color_data_array); 
							$yarn_issue_array[$row[csf('buyer_name')]]+=$yarn_issued;
							
							$grey_required_array[$row[csf('buyer_name')]]+=$required_qnty;

							$net_trans_yarn=$trans_qnty_arr[$row[csf('po_id')]]['yarn_trans'];
							
							$yarn_issue_array[$row[csf('buyer_name')]]+=$net_trans_yarn;
							
							//$balance=$mkt_required_value-($yarn_issued+$net_trans_yarn);
							$balance=$required_qnty-($yarn_issued+$net_trans_yarn);
							//$yetTo_allocate=$balance-$yarnAllocationQty;
							
							$dataYarnAllocation=explode(",",substr($yarnAllocationArr[$row[csf('po_id')]],0,-1));
							$job_yetTo_allocate=0; $job_yarnAllocationQty=0; $yetTo_allocate=0; $yarnAllocationQty=0;
							foreach($dataYarnAllocation as $yarnAllRow)
							{
								$yarnAlloRow=explode("**",$yarnAllRow);
								$yarn_count_id=$yarnAlloRow[0];
								$yarn_comp_type1st=$yarnAlloRow[1];
								$yarn_comp_percent1st=$yarnAlloRow[2];
								$yarn_comp_type2nd=$yarnAlloRow[3];
								$yarn_comp_percent2nd=$yarnAlloRow[4];
								$yarn_type_id=$yarnAlloRow[5];
								$yarnAllocationQty=$yarnAlloRow[6];
								
								if($yarn_comp_percent2nd!=0)
								{
									$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
								}
								else
								{
									$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
								}
						
								$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
								$req_allocation=$req_for_allocate_arr[$desc];
								$job_yarnAllocationQty+=$yarnAllocationQty;
								//$yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
								//$job_yetTo_allocate+=$yetTo_allocate;
								
								if(!in_array($desc,$yarn_desc_array))
								{
									$yarn_allocation_arr['not_req']+=$yarnAllocationQty;
									//$yetTo_allocate+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
								}
								else
								{
									$yarn_allocation_arr[$desc]+=$yarnAllocationQty;
									//$yetTo_allocate+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
								}
							}
							//$yarnAllocationArr
							
							$yarn_balance_array[$row[csf('buyer_name')]]+=$balance;
							
							
							$net_trans_knit=$trans_qnty_arr[$row[csf('po_id')]]['knit_trans'];
							//$knitted_array[$row[csf('buyer_name')]]+=$net_trans_knit;
							$grey_available=0;
							$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit+$grey_net_return;
							
							$grey_balance=$required_qnty-$grey_available;//-($grey_recv_qnty+$net_trans_knit+$grey_purchase_qnty);
							$grey_prod_balance=$required_qnty-$grey_recv_qnty;
							$grey_del_store=$greyDeliveryArray[$row[csf('po_id')]];
							$total_grey_del_store+=$grey_del_store;
							
							$grey_balance_array[$row[csf('buyer_name')]]+=$grey_balance;
							
							$grey_issue_array[$row[csf('buyer_name')]]+=$grey_fabric_issue;
							
							$tot_order_qnty+=$order_qnty_in_pcs;
							$tot_mkt_required+=$job_mkt_required;
							
							$tot_yarnAllocationQty+=$job_yarnAllocationQty;
							
							
							$tot_yarn_issue_qnty+=$yarn_issued;
							$tot_fabric_req+=$required_qnty;
							$tot_balance+=$balance;
							$tot_grey_recv_qnty+=$grey_recv_qnty;
							$tot_grey_production_qnty+=$grey_production_qnty;
							$tot_grey_purchase_qnty+=$grey_purchase_qnty;
							$tot_grey_balance+=$grey_balance;
							$tot_grey_prod_balance+=$grey_prod_balance;
							$tot_grey_issue+=$grey_fabric_issue;
							
							$tot_grey_available+=$grey_available;
							//$required_qnty;
							$yarn_iss_plan_date_fin=""; $yarn_iss_actual_date_fin="";
							$yarn_iss_plan_date_fin=$tna_plan_actual_arr[$row[csf('po_id')]][50]['plan'];
							$yarn_iss_actual_date_fin=$tna_plan_actual_arr[$row[csf('po_id')]][50]['actual'];
							
							$gray_prod_plan_date_fin=""; $gray_prod_actual_date_fin="";
							$gray_prod_plan_date_fin=$tna_plan_actual_arr[$row[csf('po_id')]][60]['plan'];
							$gray_prod_actual_date_fin=$tna_plan_actual_arr[$row[csf('po_id')]][60]['actual'];
							
							$dye_prod_plan_date_fin=""; $dye_prod_actual_date_fin="";
							$dye_prod_plan_date_fin=$tna_plan_actual_arr[$row[csf('po_id')]][61]['plan'];
							$dye_prod_actual_date_fin=$tna_plan_actual_arr[$row[csf('po_id')]][61]['actual'];
							
							$fin_fab_plan_date_fin=""; $fin_fab_actual_date_fin="";
							$fin_fab_plan_date_fin=$tna_plan_actual_arr[$row[csf('po_id')]][73]['plan'];
							$fin_fab_actual_date_fin=$tna_plan_actual_arr[$row[csf('po_id')]][73]['actual'];
							
							$yarn_color_td="";
							if($yarn_iss_plan_date_fin<$yarn_iss_actual_date_fin) $yarn_color_td='#FF0000';
							
							//if($gray_prod_plan_date_fin<$gray_prod_actual_date_fin) $gray_prod_color_td='#FF0000'; else if($gray_prod_plan_date_fin>=$gray_prod_actual_date_fin) $gray_prod_color_td='#008000'; else $gray_prod_color_td="";
							//if($dye_prod_plan_date_fin<$dye_prod_actual_date_fin) $dye_prod_color_td='#FF0000'; else if($dye_prod_plan_date_fin>=$dye_prod_actual_date_fin) $dye_prod_color_td='#008000'; else $dye_prod_color_td="";
							//if($fin_fab_plan_date_fin<$fin_fab_actual_date_fin) $fin_prod_color_td='#FF0000'; else if($fin_fab_plan_date_fin>=$fin_fab_actual_date_fin) $fin_prod_color_td='#008000'; else $fin_prod_color_td="";
							
							$current_date=date("Y-m-d");
							//$fin_fab_plan_date_fin=date("Y-m-d",strtotime($fin_fab_plan_date_fin));
							//echo $dye_prod_plan_date_fin.'='.$dye_prod_actual_date_fin.'='.$current_date.'<br>';
							if($gray_prod_plan_date_fin=="" || $gray_prod_plan_date_fin=="0000-00-00")
							{
								$gray_prod_color_td="";	
							}
							else if($current_date>$gray_prod_plan_date_fin && ($gray_prod_actual_date_fin=="" || $gray_prod_actual_date_fin=="0000-00-00"))
							{
								$gray_prod_color_td="#FF0000";
							}
							else if(!($gray_prod_actual_date_fin=="" || $gray_prod_actual_date_fin=="0000-00-00") && $gray_prod_actual_date_fin>$gray_prod_plan_date_fin)
							{
								$gray_prod_color_td="#33CCFF";
							}
							else if(($gray_prod_actual_date_fin<=$gray_prod_plan_date_fin) && ($gray_prod_plan_date_fin!="" || $gray_prod_plan_date_fin!="0000-00-00"))
							{
								$gray_prod_color_td="#008000";
							}
							else
							{
								$gray_prod_color_td="";	
							}
							
							
							if($dye_prod_plan_date_fin=="" || $dye_prod_plan_date_fin=="0000-00-00")
							{
								$dye_prod_color_td="";	
							}
							else if($current_date>$dye_prod_plan_date_fin && ($dye_prod_actual_date_fin=="" || $dye_prod_actual_date_fin=="0000-00-00"))
							{
								$dye_prod_color_td="#FF0000";
							}
							else if(!($dye_prod_actual_date_fin=="" || $dye_prod_actual_date_fin=="0000-00-00") && $dye_prod_actual_date_fin>$dye_prod_plan_date_fin)
							{
								$dye_prod_color_td="#33CCFF";
							}
							else if(($dye_prod_actual_date_fin<=$dye_prod_plan_date_fin) && ($dye_prod_plan_date_fin!="" || $dye_prod_plan_date_fin!="0000-00-00"))
							{
								$dye_prod_color_td="#008000";
							}
							else
							{
								$dye_prod_color_td="";	
							}
							//echo $dye_prod_color_td;
							if($current_date>$fin_fab_plan_date_fin && ($fin_fab_actual_date_fin=="" || $fin_fab_actual_date_fin=="0000-00-00"))// 
							{
								$fin_prod_color_td="#FF0000";
							}
							else if(!($fin_fab_actual_date_fin=="" || $fin_fab_actual_date_fin=="0000-00-00") && $fin_fab_actual_date_fin>$fin_fab_plan_date_fin)
							{
								$fin_prod_color_td="#33CCFF";
							}
							else if($fin_fab_actual_date_fin<=$fin_fab_plan_date_fin)
							{
								$fin_prod_color_td="#008000";
							}
							else
							{
								$fin_prod_color_td="";	
							}
							//echo $fin_prod_color_td;
							
							
							if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';
							
							$po_entry_date=date('d-m-Y', strtotime($row[csf('insert_date')]));
							$costing_date=$costing_date_library[$row[csf('job_no')]];
							
							$tot_color=count($color_data_array);	
							//echo $tot_color.'kkk';
							$contry_ship_date=""; $country_ship_qty=0; $grey_cons=0; $fin_cons=0;
							$country_date_all=explode(",",$contry_ship_qty_arr[$row[csf('po_id')]]['ship_date']);
							foreach($country_date_all as $date_all)
							{
								if($contry_ship_date=="") $contry_ship_date=change_date_format($date_all); else $contry_ship_date.=',<br>'.change_date_format($date_all);
							}
							
							$country_ship_qty=$contry_ship_qty_arr[$row[csf('po_id')]]['ship_qty']*$row[csf('ratio')];
							$tot_country_ship_qty+=$country_ship_qty;
							//echo $country_ship_qty.'=='.$row[csf('po_id')];
							$grey_cons=$fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]/$plan_cut_qnty;//$reqArr[$row[csf('po_id')]]['grey']/$dzn_qnty;
							$fin_cons=$fabric_costing_arr['knit']['finish'][$row[csf('po_id')]]/$plan_cut_qnty;//$reqArr[$row[csf('po_id')]]['finish']/$dzn_qnty;
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
									$batch_qnty=$batch_qnty_arr[$row[csf('po_id')]][$key];
									$batch_qnty_array[$row[csf('buyer_name')]]+=$batch_qnty;
									$tot_batch_qnty+=$batch_qnty;
									
									$fin_delivery_qty=$finDeliveryArray[$row[csf('po_id')]][$key];
									
									if($z==1)
									{ 
										
										$html.="<tr bgcolor='".$bgcolor."'>
												<td align='left'>".$i."</td>
												<td align='left'>".$main_booking_excel."</td>
												<td align='left'>".$sample_booking_excel."</td>
												<td align='center'>".$row[csf('job_no')]."</td>
												<td align='left'>".$row[csf('po_number')]."</td>
												<td align='left'>".$order_status[$row[csf('is_confirmed')]]."</td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('style_ref_no')]."</td>
												<td align='left'>".$row[csf('file_no')]."</td>
												<td align='left'>".$row[csf('grouping')]."</td>
												<td align='left'>".$gmts_item."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>
												<td align='center'>".change_date_format($row[csf('po_received_date')])."</td>
												<td align='center'>".$po_entry_date."</td>
												<td>".$shipment_status[$row[csf('shiping_status')]]."</td>
												<td align='center'>".$contry_ship_date."</td>
												<td align='right'>".$country_ship_qty."</td>
												<td align='right'>".$grey_cons."</td>
												<td align='right'>".$fin_cons."</td>";
												
											
										
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
                                        <td width="90" align="center"><? echo $display_font_color.$order_status[$row[csf('is_confirmed')]].$font_end; ?></td>
										<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$row[csf('buyer_name')]].$font_end; ?></p></td>
										<td width="130"><p><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></p></td>
                                        <td width="100"><p><? echo $row[csf('file_no')]; ?></p></td>
                                        <td width="100"><p><? echo $row[csf('grouping')]; ?></p></td>
										<td width="140"><p><? echo $display_font_color.$gmts_item.$font_end; ?></p></td>
										<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
										<td width="80" align="center"><? echo $display_font_color.change_date_format($row[csf('pub_shipment_date')]).$font_end; ?></td>
										<td width="80" align="center"><? echo $display_font_color.change_date_format($row[csf('po_received_date')]).$font_end; ?></td>
										<td width="80" align="center"><? echo $display_font_color.$po_entry_date.$font_end; ?></td>
										<td width="100" align="center"><? echo $display_font_color.$shipment_status[$row[csf('shiping_status')]].$font_end; ?></td>
                                        <td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>
                                       <? if($country_ship_qty>0)
									   {
										   ?>
                                        <td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $row[csf('po_id')]; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('job_no')]; ?>','country_order_dtls_popup')"><? if($z==1) echo number_format($country_ship_qty,0,'.',''); ?></a></td>
                                        <? }
                                        else
                                        {
                                        ?>
                                        <td width="100" align="right"> <? if($z==1) echo number_format($country_ship_qty,0,'.',''); ?></td>
                                        <? } ?>
                                        
                                        <td width="100" align="right" title="<? echo $fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]."/".$plan_cut_qnty; ?>"><? if($z==1) echo number_format($grey_cons,5,'.',''); ?></td>
                                        <td width="100" align="right" title="<? echo $fabric_costing_arr['knit']['finish'][$row[csf('po_id')]]."/".$plan_cut_qnty; ?>"><? if($z==1) echo number_format($fin_cons,5,'.',''); ?></td>
                                        
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
													//else $html.="kausar";
													echo $display_font_color.$yarn_count_value.$font_end;
													if($z==1) $html.=$yarn_count_value;
												 $d++;
												 }
												 
												 $html.="</td><td>";
											?>
										</td>
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
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
											</div>
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
                                        <td width="100" align="right">
											<? 
												if($z==1)
												{
													echo "<font color='$bgcolor' style='display:none'>".number_format($job_yarnAllocationQty,2,'.','')."</font>\n";
													$d=1;
													foreach($yarn_desc_array as $yarn_desc)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}
														
														$yarn_allo_qnty=$yarn_allocation_arr[$yarn_desc];
														$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
														
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_allo_qnty,2,'.','');?></a>
														<?
														$html.=number_format($yarn_allo_qnty,2);
														$d++;
													}
													
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}
													
													$yarn_desc=join(",",$yarn_desc_array);
													
													$allo_qnty_not_req=$yarn_allocation_arr['not_req'];
													
													$html.=number_format($allo_qnty_not_req,2);
													//$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
													?>
													<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_allocation_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($allo_qnty_not_req,2);?></a>
												<?
												
												}
												$html.="</td><td>";
											?>
										</td>
                                         <td width="100" align="right">
											<?
												if($z==1)
												{
													$job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
													echo "<font color='$bgcolor' style='display:none'>".number_format($job_yetTo_allocate,2,'.','')."</font>\n";
													$tot_yetTo_allocate+=$job_yetTo_allocate;
													echo number_format($job_yetTo_allocate,2,'.','');
													$html.=number_format($job_yetTo_allocate,2);
												}
												$html.="</td><td>";
											?>
										</td>
                                        <td width="100" align="right" bgcolor="<? echo $yarn_color_td; ?>">
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
														}
														
														$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
														$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
														
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
														<?
														$html.=number_format($yarn_iss_qnty,2);
														$d++;
													}
													
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}
													
													$yarn_desc=join(",",$yarn_desc_array);
													
													$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
													
													$html.=number_format($iss_qnty_not_req,2);
													$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
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
										<td width="100" align="right" title="Grey Req.-(Yarn Issue+Net Transfer)"> 
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
                                        <td width="100" align="right" bgcolor="<? echo $gray_prod_color_td; ?>">
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
                                        <? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
										<td width="100" align="right" title="(Grey Req-Prod)">
										<? 
											if($z==1) 
											{
												echo number_format($grey_prod_balance,2,'.','');
												$html.=number_format($grey_prod_balance,2);
											}
										?>
										</td>
                                        <? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
										<td width="100" align="right">
										<? 
											if($z==1) 
											{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_delivery_to_store','')"><? echo number_format($grey_del_store,2,'.',''); ?></a>
												<?
												$html.=number_format($grey_del_store,2); 
											}
										?>
										</td>
                                        <? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
                                        <td width="100" align="right">
										<? 
											$greyKnitFloor=0;
											if($z==1) 
											{
												$greyKnitFloor=$grey_recv_qnty-$grey_del_store;
												echo number_format($greyKnitFloor,2,'.',''); 
												$tot_greyKnitFloor+=$greyKnitFloor;
												$html.=number_format($greyKnitFloor,2); 
											}
										?>
										</td>
										<? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
                                        <td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($grey_production_qnty,2);
												}
											?>
										</td>
                                        <? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
                                        <td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'0'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a>
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
													<? echo number_format($grey_net_return,2,'.',''); ?>
												<?
													$html.=number_format($grey_net_return,2);
													$tot_net_gray_return+=$grey_net_return;
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
                                        <td width="100" align="right" title="Grey Rcvd (Prod.) + Grey Rcvd (Purchase) + Net Transfer">
										<? 
											//$grey_available=0;
											//$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
											if($z==1) 
											{
												echo number_format($grey_available,2,'.','');
												$html.=number_format($grey_available,2); 
												$knitted_array[$row[csf('buyer_name')]]+=$grey_available;
												//$tot_net_trans_knit_qnty+=$net_trans_knit;
											}
										?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right" title="Required (As per Booking) - Fabric Available">
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
												}echo "-";
											?>
										</td>
										<? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; ?>
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
												$batch_color_qnty=$batch_qnty_arr[$row[csf('po_id')]][$key];
												$html.=number_format($batch_color_qnty,2);
												$tot_batch_qnty_excel+=$batch_color_qnty;
												
											?>
												<a href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.$key; ?>','batch_qnty','')"><? echo number_format($batch_color_qnty,2,'.',''); ?></a>
										</td>
                                       <? 
											$html.="</td><td>"; $html_short.="<td>"; 
											
											$fab_recv_qnty=$finish_receive_qnty_arr[$row[csf('po_id')]][$key];
											$fab_production_qnty=$finish_purchase_qnty_arr[$row[csf('po_id')]][$key]['production'];
											$fab_purchase_qnty=$finish_purchase_qnty_arr[$row[csf('po_id')]][$key]['purchase'];
											$issue_to_cut_qnty=$finish_issue_qnty_arr[$row[csf('po_id')]][$key];
											
											$fab_rec_return=$finish_recv_rtn_qnty_arr[$row[csf('po_id')]][$key];
											$fab_issue_return=$finish_issue_rtn_qnty_arr[$row[csf('po_id')]][$key];
											$fab_net_return=$fab_issue_return-$fab_rec_return;
											
											$dye_qnty=$dye_qnty_arr[$row[csf('po_id')]][$key];
										?>
										<td width="100" align="right" bgcolor="<? echo $dye_prod_color_td; ?>">
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
											<? 
												$dyeing_balance=$batch_color_qnty-$dye_qnty;
												echo number_format($dyeing_balance,2,'.',''); 
												$html.=number_format($dyeing_balance,2);
												//$tot_dye_qnty+=$dyeing_balance; 
												$tot_dye_qnty_balance+=$dyeing_balance;  
											?>
										</td>
                                       <td width="100" align="right">
											<? 
												$html.="</td><td>";
												echo number_format($value,2,'.','');
												$html.=number_format($value,2);
												
												$fin_fab_Requi_array[$row[csf('buyer_name')]]+=$value;
												$tot_color_wise_req+=$value; 
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right" bgcolor="<? echo $fin_prod_color_td; ?>">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_recv_qnty,2);
												$html_short.=number_format($fab_recv_qnty,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_recv_qnty;
												$tot_fabric_recv+=$fab_recv_qnty;
												$tot_fabric_recv_excel+=$fab_recv_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<?
												$finish_balance=$value-$fab_recv_qnty;
											 	echo number_format($finish_balance,2,'.','');
												$html.=number_format($finish_balance,2);
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$finish_balance;
												$tot_fabric_recv_balance+=$finish_balance;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
												<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.$key; ?>','finish_delivery_to_store','')"><? echo number_format($fin_delivery_qty,2,'.',''); ?></a>
												<?
												$html.=number_format($fin_delivery_qty,2);
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fin_delivery_qty;
												$tot_fin_delivery_qty+=$fin_delivery_qty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
										 	<? 
												$finProdFloor=$fab_recv_qnty-$fin_delivery_qty;
												echo number_format($finProdFloor,2,'.',''); 
												$html.=number_format($finProdFloor,2);
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fin_delivery_qty;
												$tot_finProdFloor+=$finProdFloor;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'9'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_production_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_production_qnty,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
												$tot_fabric_production+=$fab_production_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'0'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_purchase_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_purchase_qnty,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
												$tot_fabric_purchase+=$fab_purchase_qnty;
											?>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<? echo number_format($fab_net_return,2,'.',''); ?>
											<?
												$html.=number_format($fab_net_return,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
												$tot_fab_net_return+=$fab_net_return;
											?>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right" >
											<? 
												$net_trans_finish=$trans_qnty_fin_arr[$row[csf('po_id')]][$key]['trans'];
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$net_trans_finish;
											?>
                                            	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','finish_trans','<? echo $key; ?>')"><? echo number_format($net_trans_finish,2,'.','');  ?></a>
                                            <?	
												$html.=number_format($net_trans_finish,2);
												$tot_net_trans_finish_qnty+=$net_trans_finish; 
												$fabric_balance=$value-($fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish+$fab_net_return);
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right" title="Fin. Req.-(Received (Prod.) + Received (Purchase) + Net Transfer)">
											<?
												$fabric_available=$fab_production_qnty+$fab_purchase_qnty+$net_trans_finish+$fab_net_return;
												$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fabric_available;
												echo number_format($fabric_available,2,'.',''); 
												$html.=number_format($fabric_available,2);
												$tot_fabric_available+=$fabric_available;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<?
												$fabric_receive_bal=$value-$fabric_available;
												echo number_format($fabric_receive_bal,2,'.',''); 
												$fin_balance_array[$row[csf('buyer_name')]]+=$fabric_receive_bal;
												$html.=number_format($fabric_receive_bal,2);
												$tot_fabric_rec_bal+=$fabric_receive_bal;
											?>
										</td>
										<? $html.="</td><td>"; $html_short.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($issue_to_cut_qnty,2);
												$html_short.=number_format($issue_to_cut_qnty,2);
												$issue_toCut_array[$row[csf('buyer_name')]]+=$issue_to_cut_qnty;
												$tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right"><? $yet_to_cut_qty=$value-$issue_to_cut_qnty; echo number_format($yet_to_cut_qty,2,'.',''); $tot_yet_to_cut+=$yet_to_cut_qty; $html.=number_format($yet_to_cut_qty,2); ?></a>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$fabric_left_over=$fabric_available-$issue_to_cut_qnty;
												echo number_format($fabric_left_over,2,'.',''); 
												$html.=number_format($fabric_left_over,2);
												$tot_fabric_left_over+=$fabric_left_over;
												$tot_fabric_left_over_excel+=$fabric_left_over;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<?
												//$fabric_left_over=($fab_recv_qnty+$fabric_available)-$issue_to_cut_qnty;
												//echo number_format($fabric_left_over,2,'.',''); 
												//$html.=number_format($fabric_left_over,2);
												//$tot_fabric_left_over+=$fabric_left_over;
											?>
										</td>
										<td> 
											<p>
												<? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
											</p>
										</td>
                                    </tr>
								<?	
									if($z==1) $html.="</td><td>".join(",<br>",array_unique($fabric_desc))."</td></tr>"; else $html.="</td><td>&nbsp;</td></tr>"; 
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
												<td align='left'>".$order_status[$row[csf('is_confirmed')]]."</td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('style_ref_no')]."</td>
												<td align='left'>".$row[csf('file_no')]."</td>
												<td align='left'>".$row[csf('grouping')]."</td>
												
												<td align='left'>".$gmts_item."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>".change_date_format($row[csf('pub_shipment_date')])."</td>
												<td align='center'>".change_date_format($row[csf('po_received_date')])."</td>
												<td align='center'>".$po_entry_date."</td>
												<td>".$shipment_status[$row[csf('shiping_status')]]."</td>
												<td align='center'>".$contry_ship_date."</td>
												<td align='right'>".$country_ship_qty."</td>
												<td align='right'>".$grey_cons."</td>
												<td align='right'>".$fin_cons."</td>";
								
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
                                            <a href='#report_details' onclick="progress_comment_popup('<? echo $row[csf('job_no')]; ?>','<? echo $row[csf('po_id')]; ?>','<? echo $template_id; ?>');"><? echo $display_font_color.$row[csf('po_number')].$font_end;  ?></a>
                                            
                                    	</p>
                                    </td>
                                    <td width="90" align="center"><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
									<td width="80"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
									<td width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                    <td width="100"><p><? echo $row[csf('file_no')]; ?></p></td>
                                    <td width="100"><p><? echo $row[csf('grouping')]; ?></p></td>
									<td width="140"><p><? echo $gmts_item; ?></p></td>
									<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
									<td width="80" align="center"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
									<td width="80" align="center"><? echo $po_entry_date; ?></td>
									<td width="100" align="center"><? echo $shipment_status[$row[csf('shiping_status')]]; ?></td>
                                    <td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>
                                    
                                    <? if($country_ship_qty>0)
									   {
										   ?>
                                        <td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $row[csf('po_id')]; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('job_no')]; ?>','country_order_dtls_popup')"><? echo number_format($country_ship_qty,0,'.',''); ?></a></td>
                                        <? }
                                        else
                                        {
                                        ?>
                                        <td width="100" align="right"> <? if($z==1) echo number_format($country_ship_qty,0,'.',''); ?></td>
                                        <? } ?>
                                        
                                    <td width="100" align="right"><? echo number_format($grey_cons,5,'.',''); ?></td>
                                    <td width="100" align="right"><? echo number_format($fin_cons,5,'.',''); ?></td>
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
									<td width="110">
										<div style="word-wrap:break-word; width:110px">
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
										</div>
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
                                    <td width="100" align="right">
										<? 
                                            if($z==1)
                                            {
                                                $d=1; 
                                                foreach($yarn_allocation_arr as $yarn_allocation_value)
                                                {
                                                    if($d!=1)
                                                    {
                                                        echo "<hr/>";
                                                        $html.="<hr/>";
                                                    }
                                                    $yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
                                                    ?>
                                                    <a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($yarn_allocation_value,2,'.','');?></a>
                                                <?
                                                $html.=number_format($yarn_allocation_value,2);
                                                $d++;
                                                }
                                            }
											$html.="</td><td bgcolor='$discrepancy_td_color'>";
                                        ?>
                                    </td>
                                    <td width="100" align="right">
										<? 
                                            if($z==1)
                                            {
												$job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
												echo number_format($job_yetTo_allocate,2,'.','');
												$tot_yetTo_allocate+=$job_yetTo_allocate;
												$html.=number_format($job_yetTo_allocate,2);
                                            }
											$html.="</td><td bgcolor='$discrepancy_td_color'>";
                                        ?>
                                    </td>
                                    <td width="100" align="right" bgcolor="<? echo $yarn_color_td; ?>">
										<? 
											echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
											$d=1;
											foreach($yarn_desc_array as $yarn_desc)
											{
												if($d!=1)
												{
													echo "<hr/>";
													$html.="<hr/>";
												}
												
												$yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
												$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
												
												?>
												<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_iss_qnty,2,'.','');?></a>
												<?
												$html.=number_format($yarn_iss_qnty,2);
												$d++;
											}
											
											if($d!=1)
											{
												echo "<hr/>";
												$html.="<hr/>";
											}
											
											$yarn_desc=join(",",$yarn_desc_array);
											
											$iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
											
											$html.=number_format($iss_qnty_not_req,2);
											$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
											?>
											<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_issue_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($iss_qnty_not_req,2);?></a>
									</td>
									<? $html.="</td><td>"; $html_short.="</td>"; ?>
                                    <td width="100" align="right">
                                         <a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','yarn_trans','')"><? echo number_format($net_trans_yarn,2,'.','');  ?></a>
										<? 
											$html.=number_format($net_trans_yarn,2); 
											$tot_net_trans_yarn_qnty+=$net_trans_yarn;
										?>
									</td>
                                    <? $html.="</td><td>"; ?>
									<td width="100" align="right" title="(Grey Req-(Yarn Issue+Net Transfer))">
										<? 
											echo number_format($balance,2,'.','');
											$html.=number_format($balance,2); 
										?>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); $html.=number_format($required_qnty,2); ?></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right" bgcolor="<? echo $gray_prod_color_td; ?>"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); $html.=number_format($grey_recv_qnty,2);?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right">
										<? 
											echo number_format($grey_prod_balance,2,'.','');
											$html.=number_format($grey_prod_balance,2);
										?>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_del_store,2,'.',''); $html.=number_format($grey_del_store,2);?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); $html.=number_format($grey_production_qnty,2); ?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $html.=number_format($grey_purchase_qnty,2); ?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"></td>
                                    <? $html.="</td><td>"; ?>
									<td width="100" align="right">
                                    	<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a><? $tot_net_trans_knit_qnty+=$net_trans_knit; $html.=number_format($net_trans_knit,2); ?>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right">
										<? $grey_available=0; $grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
										echo number_format($grey_available,2,'.',''); $html.=number_format($grey_available,2); ?>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); $html.=number_format($grey_balance,2); ?></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); $html.=number_format($grey_fabric_issue,2); ?></a>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100">&nbsp;<? $html.="&nbsp;"; ?></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','batch_qnty','')"><? /*echo number_format($batch_color_qnty,2,'.','');*/ $html.="&nbsp;"; ?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" bgcolor="<? echo $dye_prod_color_td; ?>">&nbsp;</td>
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
									$tot_batch_qnty_excel+=$batch_qnty;
									$html.="</td>
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
						$order_qty_array[$row[csf('buyer_name')]]+=$order_qnty_in_pcs;
						
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

						$yarn_data_array=array(); $mkt_required_array=array(); $yarn_allocation_arr=array(); $yetTo_allocate_arr=array(); $req_for_allocate_arr=array();  $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array(); $s=1;
						/*$dataYarn=explode(",",substr($dataArrayYarn[$row[csf('job_no')]],0,-1));
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
							
							//$yarn_allocationQty=$yarnAllocationJobArr[$row[csf('job_no')]];
							//$yarn_allocation_arr[$s]=$yarn_allocationQty;//[$allocationRow[csf('item_id')]]
							//$yetTo_allocate=$mkt_required-$yarnAllocationJobArr[$row[csf('job_no')]];
							//$yetTo_allocate_arr[$s]=$yetTo_allocate;
							//$job_yarnAllocationQty=+$yarn_allocationQty;
							//$job_yetTo_allocate+=$yetTo_allocate;
							
							//$tot_yarnAllocationQty+=$job_yarnAllocationQty;
							//$tot_yetTo_allocate+=$job_yetTo_allocate;//

							//$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
							//$yarn_data_array['type'][$s]=$yarn_type[$type_id];
							
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
							$des_for_allocation=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							
							$req_for_allocate_arr[$des_for_allocation]=$mkt_required;
							
							$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
							$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
							
							$s++;
						}*/
						$yarn_descrip_data=$yarn_des_data_job[$row[csf('job_no')]];
						$qnty=0;
						foreach($yarn_descrip_data as $count=>$count_value)
						{
						  foreach($count_value as $Composition=>$composition_value)
                           {
							 foreach($composition_value as $percent=>$percent_value)
                               {	
							   
							  foreach($percent_value as $y_type=>$type_value)
                                {
							
							//$yarnRow=explode("**",$yarnRow);
							$count_id=$count;//$yarnRow[0];
							$copm_one_id=$Composition;//$yarnRow[1];
							$percent_one=$percent;//$yarnRow[2];
							//$copm_two_id=$yarnRow[3];
							//$percent_two=$yarnRow[4];
							$type_id=$y_type;//$yarnRow[5];
							$qnty=$type_value;//$yarnRow[6];
							
							$mkt_required=$qnty;//$plan_cut_qnty*($qnty/$dzn_qnty);
							$mkt_required_array[$s]=$mkt_required;
							$job_mkt_required+=$mkt_required;
							
							//$yarn_allocationQty=$yarnAllocationJobArr[$row[csf('job_no')]];
							//$yarn_allocation_arr[$s]=$yarn_allocationQty;//[$allocationRow[csf('item_id')]]
							//$yetTo_allocate=$mkt_required-$yarnAllocationJobArr[$row[csf('job_no')]];
							//$yetTo_allocate_arr[$s]=$yetTo_allocate;
							//$job_yarnAllocationQty=+$yarn_allocationQty;
							//$job_yetTo_allocate+=$yetTo_allocate;
							
							//$tot_yarnAllocationQty+=$job_yarnAllocationQty;
							//$tot_yetTo_allocate+=$job_yetTo_allocate;

							$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
							$yarn_data_array['type'][$s]=$yarn_type[$type_id];
							
							/*if($percent_two!=0)
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id]." ".$percent_two." %";
							}
							else
							{
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
							}*/
							
							/*if($percent_one!=0)
							{
								
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
								
							}
							else
							{*/
								//$compos=$composition[$copm_one_id]." 100%"." ".$composition[$copm_two_id]." ".$percent_two."";
							//}
							$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];

							$yarn_data_array['comp'][]=$compos;
							
							$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							$des_for_allocation=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
							
							$req_for_allocate_arr[$des_for_allocation]=$mkt_required;
							
							$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
							$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
							
							 $s++;
								}
							   }
							 
							} 
						}
						
						$grey_production_qnty=0; $grey_purchase_qnty=0; $grey_net_return=0; $grey_recv_qnty=0; $grey_fabric_issue=0; $booking_data=''; $job_yarnAllocationQty=0; $grey_del_store=0; $n=1;
						$job_po_id=explode(",",$row[csf('po_id')]); //$job_yetTo_allocate=0;
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
							
							$dataYarnAllocation=explode(",",substr($yarnAllocationArr[$po_id],0,-1));
							foreach($dataYarnAllocation as $yarnAllRow)
							{
								$yarnAlloRow=explode("**",$yarnAllRow);
								$yarn_count_id=$yarnAlloRow[0];
								$yarn_comp_type1st=$yarnAlloRow[1];
								$yarn_comp_percent1st=$yarnAlloRow[2];
								$yarn_comp_type2nd=$yarnAlloRow[3];
								$yarn_comp_percent2nd=$yarnAlloRow[4];
								$yarn_type_id=$yarnAlloRow[5];
								$yarnAllocationQty=$yarnAlloRow[6];
								
								if($yarn_comp_percent2nd!=0)
								{
									$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
								}
								else
								{
									$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
								}
						
								$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
								$req_allocation=$req_for_allocate_arr[$desc];
								//$yetTo_allocate=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
								//$job_yetTo_allocate+=$yetTo_allocate;
								$job_yarnAllocationQty+=$yarnAllocationQty;
								if(!in_array($desc,$yarn_desc_array))
								{
									$yarn_allocation_arr['not_req']+=$yarnAllocationQty;
									//$yetTo_allocate_arr['not_req']+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
								}
								else
								{
									$yarn_allocation_arr[$desc]+=$yarnAllocationQty;
									//$yetTo_allocate_arr[$desc]+=$req_for_allocate_arr[$desc]-$yarnAllocationQty;
								}
							}
							
							
							$grey_production_qnty+=$greyPurchaseQntyArray[$po_id]['production'];
							$grey_purchase_qnty+=$greyPurchaseQntyArray[$po_id]['purchase'];
							
							$grey_issue_rtn=$grey_issue_return_qnty_arr[$po_id];
							$grey_rec_rtn=$grey_receive_return_qnty_arr[$po_id];
							$grey_net_return+=$grey_issue_rtn-$grey_rec_rtn;
							
							$grey_recv_qnty+=$grey_receive_qnty_arr[$po_id];
							
							$grey_fabric_issue+=$grey_issue_qnty_arr[$po_id];
							$grey_del_store+=$greyDeliveryArray[$po_id];
							
							$booking_data.=implode(",",array_filter(explode(",",substr($dataArrayWo[$po_id],0,-1)))).",";
							$n++;
						}
						$total_grey_del_store+=$grey_del_store;
						
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
											if($booking_no!="")
											{
												$main_booking.="<a href='##' style='color:#000' onclick=\"generate_worder_report('".$is_short."','".$booking_no."','".$company_id."','".$row[csf('po_id')]."','".$item_category."','".$fabric_source."','".$job_no."','".$is_approved."')\"><font style='font-weight:bold' color='$wo_color;'>".$booking_no."(".$system_date.")".$pre."</font></a><br>";
											}
											else $main_booking.="No Booking";
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
							}
							
							$yarn_issue_array[$row[csf('buyer_name')]]+=$net_trans_yarn;
							$balance=$required_qnty-($yarn_issued+$net_trans_yarn);
							//$yetTo_allocate=$balance-$job_yarnAllocationQty;
							//$job_yetTo_allocate+=$yetTo_allocate;
							$yarn_balance_array[$row[csf('buyer_name')]]+=$balance;
							$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit+$grey_net_return;
							$knitted_array[$row[csf('buyer_name')]]+=$grey_available;
							
							//$knitted_array[$row[csf('buyer_name')]]+=$net_trans_knit;
							$grey_prod_balance=$required_qnty-$grey_recv_qnty;
							$grey_balance=$required_qnty-$grey_available;
							$tot_grey_prod_balance+=$grey_prod_balance;
							
							$grey_balance_array[$row[csf('buyer_name')]]+=$grey_balance;
							
							$grey_issue_array[$row[csf('buyer_name')]]+=$grey_fabric_issue;
							
							//$batch_qnty_array[$row[csf('buyer_name')]]+=$batch_qnty;
							
							$tot_order_qnty+=$order_qnty_in_pcs;
							$tot_mkt_required+=$job_mkt_required;
							$tot_yarnAllocationQty+=$job_yarnAllocationQty;
							
							$tot_yarn_issue_qnty+=$yarn_issued;
							$tot_fabric_req+=$required_qnty;
							$tot_balance+=$balance;
							$tot_grey_recv_qnty+=$grey_recv_qnty;
							$tot_grey_production_qnty+=$grey_production_qnty;
							$tot_grey_purchase_qnty+=$grey_purchase_qnty;
							$tot_grey_balance+=$grey_balance;
							$tot_grey_issue+=$grey_fabric_issue;
							//$tot_batch_qnty+=$batch_qnty;
							
							
							$tot_grey_available+=$grey_available;
					
							if($required_qnty>$job_mkt_required) $bgcolor_grey_td='#FF0000'; $bgcolor_grey_td='';
							
							$po_entry_date=date('d-m-Y', strtotime($row[csf('insert_date')]));
							$costing_date=$costing_date_library[$row[csf('job_no')]];
							
							$contry_ship_date=""; $country_ship_qty=0; $grey_cons=0; $fin_cons=0;
							$job_po_id=explode(",",$row[csf('po_id')]); //$job_yetTo_allocate=0;
							foreach($job_po_id as $po_id)
							{
								$country_date_all=explode(",",$contry_ship_qty_arr[$po_id]['ship_date']);
								foreach($country_date_all as $date_all)
								{
									if($contry_ship_date=="") $contry_ship_date=change_date_format($date_all); else $contry_ship_date.=',<br>'.change_date_format($date_all);
								}
								
								$country_ship_qty+=$contry_ship_qty_arr[$po_id]['ship_qty'];
								$tot_country_ship_qty+=$country_ship_qty;
							}
							//echo $country_ship_qty.'=='.$row[csf('po_id')];
							$grey_cons=$reqArr[$row[csf('job_no')]]['grey']/$dzn_qnty;
							$fin_cons=$reqArr[$row[csf('job_no')]]['finish']/$dzn_qnty;
							
							$tot_color=count($color_data_array);	
							//echo $tot_color;
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
												<td align='left'></td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('style_ref_no')]."</td>
												<td align='left'>".$row[csf('file_no')]."</td>
												<td align='left'>".$row[csf('grouping')]."</td>
												<td align='left'>".$gmts_item."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>View</td>
												<td align='center'>".$contry_ship_date."</td>
												<td align='right'>".$country_ship_qty."</td>
												<td align='right'>".$grey_cons."</td>
												<td align='right'>".$fin_cons."</td>";
										
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
                                        <td width="90" align="center"><? //echo $order_status[$row[csf('is_confirmed')]]; ?></td>
										<td width="80"><p><? echo $display_font_color.$buyer_short_name_library[$row[csf('buyer_name')]].$font_end; ?></p></td>
										<td width="130"><p><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></p></td>
                                        <td width="100"><p><? echo $display_font_color.$row[csf('file_no')].$font_end; ?></p></td>
                                        <td width="100"><p><? echo $display_font_color.$row[csf('grouping')].$font_end; ?></p></td>
										<td width="140"><p><? echo $display_font_color.$gmts_item; ?></p></td>
										<td width="100" align="right"><? if($z==1) echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
										<td width="80" align="center"><? echo $display_font_color; ?><a href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','Shipment_date','')"><? echo "View"; ?></a><? echo $font_end; ?></td>
                                        <td width="80"><p><? echo $display_font_color.$contry_ship_date.$font_end; ?></p></td>
                                        <td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $row[csf('po_id')]; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('job_no')]; ?>','country_order_dtls_popup')"><? if($z==1) echo number_format($country_ship_qty,0,'.',''); ?></a></td>
                                        <td width="100" align="right"><? if($z==1) echo number_format($grey_cons,5,'.',''); ?></td>
                                        <td width="100" align="right"><? if($z==1) echo number_format($fin_cons,5,'.',''); ?></td>
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
										<td width="110">
											<div style="word-wrap:break-word; width:110px">
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
											</div>
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
                                        <td width="100" align="right">
											<? 
												if($z==1)
												{
													echo "<font color='$bgcolor' style='display:none'>".number_format($job_yarnAllocationQty,2,'.','')."</font>\n";
													$d=1;
													foreach($yarn_desc_array as $yarn_desc)
													{
														if($d!=1)
														{
															echo "<hr/>";
															$html.="<hr/>";
														}
														
														$yarn_allo_qnty=$yarn_allocation_arr[$yarn_desc];
														$yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
														
														?>
														<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup[0]; ?>','<? echo $yarn_desc_for_popup[1]; ?>','<? echo $yarn_desc_for_popup[2]; ?>','<? echo $yarn_desc_for_popup[3]; ?>','<? echo $yarn_desc_for_popup[4]; ?>','<? echo $yarn_desc_for_popup[5]; ?>')"><? echo number_format($yarn_allo_qnty,2,'.','');?></a>
														<?
														$html.=number_format($yarn_allo_qnty,2);
														$d++;
													}
													
													if($d!=1)
													{
														echo "<hr/>";
														$html.="<hr/>";
													}
													
													$yarn_desc=join(",",$yarn_desc_array);
													
													$allo_qnty_not_req=$yarn_allocation_arr['not_req'];
													
													$html.=number_format($allo_qnty_not_req,2);
													//$html_short.=number_format($iss_qnty_not_req+$yarn_issued,2);
													?>
													<a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_allocation_not','<? echo $yarn_desc; ?>','','','','','')"><? echo number_format($allo_qnty_not_req,2);?></a>
												<?
												}
												$html.="</td><td>"; 
											?>
										</td>
                                         <td width="100" align="right">
											<?
												if($z==1)
												{
													//echo $job_yarnAllocationQty;
													$job_yetTo_allocate=$required_qnty-$job_yarnAllocationQty;
													echo "<font color='$bgcolor' style='display:none'>".number_format($job_yetTo_allocate,2,'.','')."</font>\n";
													$tot_yetTo_allocate+=$job_yetTo_allocate;
													echo number_format($job_yetTo_allocate,2,'.','');
													$html.=number_format($job_yetTo_allocate,2);
												}
												$html.="</td><td>"; 
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
                                        <? $html.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
                                        <td width="100" align="right">
											<? 
												if($z==1)
												{
													echo number_format($grey_prod_balance,2,'.',''); 
													$html.=number_format($grey_prod_balance,2);
												}
											?>
										</td>
                                        <? $html.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
										<td width="100" align="right">
										<? 
											if($z==1) 
											{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_delivery_to_store','')"><? echo number_format($grey_del_store,2,'.',''); ?></a>
												<?
												$html.=number_format($grey_del_store,2);
											}
										?>
										</td>
                                        <? $html.="</td><td bgcolor='$discrepancy_td_color'>"; ?>
                                        <td width="100" align="right">
										<? 
											$greyKnitFloor=0;
											if($z==1) 
											{
												$greyKnitFloor=$grey_recv_qnty-$grey_del_store;
												echo number_format($greyKnitFloor,2,'.',''); 
												$html.=number_format($greyKnitFloor,2); 
												$tot_greyKnitFloor+=$greyKnitFloor;
											}
										?>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'9'; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); ?></a>
												<?
													$html.=number_format($grey_production_qnty,2);
												}
											?>
										</td>
                                        <? $html.="</td><td bgcolor='$bgcolor_grey_td'>"; ?>
										<td width="100" align="right">
											<? 
												if($z==1)
												{
												?>
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'0'; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); ?></a>
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
													<? echo number_format($grey_net_return,2,'.',''); ?>
												<?
													$html.=number_format($grey_net_return,2);
													$tot_net_gray_return+=$grey_net_return;
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
                                        <td width="100" align="right" title="Grey Rcvd (Prod.) + Grey Rcvd (Purchase) + Net Transfer">
										<? 
											//
											//$grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
											if($z==1) 
											{
												echo number_format($grey_available,2,'.','');
												$html.=number_format($grey_available,2); 
											}
										?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right" title="Required (As per Booking) - Fabric Available">
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
										<? $html.="</td><td bgcolor='#FF9BFF'>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; ?>
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
										<? $html.="</td><td>";
										
										$batch_color_qnty=0; $fab_recv_qnty=0; $fab_production_qnty=0; $fab_purchase_qnty=0; $issue_to_cut_qnty=0; $dye_qnty=0; $fin_delivery_qty=0; $fab_net_return=0; $fab_rec_return=0; $fab_issue_return=0;
										foreach($job_po_id as $val)
										{
											$batch_color_qnty+=$batch_qnty_arr[$val][$key];
											
											//$tot_batch_qnty+=$batch_color_qnty;
											
											$fab_recv_qnty+=$finish_receive_qnty_arr[$val][$key];
											$fab_production_qnty+=$finish_purchase_qnty_arr[$val][$key]['production'];
											$fab_purchase_qnty+=$finish_purchase_qnty_arr[$val][$key]['purchase'];
											$issue_to_cut_qnty+=$finish_issue_qnty_arr[$val][$key];
											
											$fab_rec_return=$finish_recv_rtn_qnty_arr[$val][$key];
											$fab_issue_return=$finish_issue_rtn_qnty_arr[$val][$key];
											$fab_net_return+=$fab_issue_return-$fab_rec_return;
											//$dye_qnty+=$dye_qnty_arr[$val][$key];
											$dye_qnty+=$dye_qnty_arr[$val][$key];
											$fin_delivery_qty+=$finDeliveryArray[$val][$key];
										}
										?>
                                        <td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.$key; ?>','batch_qnty','')"><? echo number_format($batch_color_qnty,2,'.',''); 
											$html.=number_format($batch_color_qnty,2);
											$batch_qnty_array[$row[csf('buyer_name')]]+=$batch_color_qnty;
											$tot_batch_qnty+=$batch_color_qnty;
											$tot_batch_qnty_excel+=$batch_color_qnty;
											?></a>
										</td>
										<? $html.="</td><td>"; $html_short.="</td><td bgcolor='#FF9BFF'>"; ?>                                        
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
											<?
												$dyeing_balance=$batch_color_qnty-$dye_qnty;
												echo number_format($dyeing_balance,2);
												$html.=number_format($dyeing_balance,2);
												$html_short.=number_format($dyeing_balance,2);
												
												//$dye_qnty_array[$row[csf('buyer_name')]]+=$dyeing_balance;
												//$tot_dye_qnty+=$dyeing_balance;
												$tot_dye_qnty_balance+=$dyeing_balance;  
											?>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<? 
												echo number_format($value,2,'.','');
												$html.=number_format($value,2);
												
												$fin_fab_Requi_array[$row[csf('buyer_name')]]+=$value;
												$tot_color_wise_req+=$value; 
											?>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','fabric_receive','<? echo $key; ?>')"><? echo number_format($fab_recv_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_recv_qnty,2);
												$html_short.=number_format($fab_recv_qnty,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_recv_qnty;
												$tot_fabric_recv+=$fab_recv_qnty;
												$tot_fabric_recv_excel+=$fab_recv_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; $html_short.="</td><td>"; ?>
                                        <td width="100" align="right">
											<?
												$finish_balance=$value-$fab_recv_qnty;
											 	echo number_format($finish_balance,2,'.','');
												$html.=number_format($finish_balance,2);
												$html_short.=number_format($finish_balance,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$finish_balance;
												$tot_fabric_recv_balance+=$finish_balance;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
													<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.$key; ?>','finish_delivery_to_store','')"><? echo number_format($fin_delivery_qty,2,'.',''); ?></a>
												<?
												$html.=number_format($fin_delivery_qty,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fin_delivery_qty;
												$tot_fin_delivery_qty+=$fin_delivery_qty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
										 	<? 
												$finProdFloor=$fab_recv_qnty-$fin_delivery_qty;
												echo number_format($finProdFloor,2,'.',''); 
												$html.=number_format($finProdFloor,2);
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$finProdFloor;
												$tot_finProdFloor+=$finProdFloor;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'9'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_production_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_production_qnty,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
												$tot_fabric_production+=$fab_production_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; $html_short.="</td><td>"; ?>
                                        <td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')].'_'.'0'; ?>','fabric_purchase','<? echo $key; ?>')"><? echo number_format($fab_purchase_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($fab_purchase_qnty,2);
												$html_short.=number_format($fab_purchase_qnty,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
												$tot_fabric_purchase+=$fab_purchase_qnty;
											?>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<? echo number_format($fab_net_return,2,'.',''); ?>
											<?
												$html.=number_format($fab_net_return,2);
												//$html_short.=number_format($fab_purchase_qnty,2);
												
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fab_purchase_qnty;
												$tot_fab_net_return+=$fab_net_return;
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
												//$fin_fab_recei_array[$row[csf('buyer_name')]]+=$net_trans_finish;
												$tot_net_trans_finish_qnty+=$net_trans_finish;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right">
											<?
												$fabric_available=$fab_production_qnty+$fab_purchase_qnty+$net_trans_finish+$fab_net_return;
												$fin_fab_recei_array[$row[csf('buyer_name')]]+=$fabric_available;
												echo number_format($fabric_available,2,'.',''); 
												$html.=number_format($fabric_available,2);
												$tot_fabric_available+=$fabric_available;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
										<td width="100" align="right">
											<?
												$fabric_receive_bal=$value-$fabric_available;
												echo number_format($fabric_receive_bal,2,'.',''); 
												$fin_balance_array[$row[csf('buyer_name')]]+=$fabric_receive_bal;
												$html.=number_format($fabric_receive_bal,2);
												$tot_fabric_rec_bal+=$fabric_receive_bal;
											?>
										</td>
										<? $html.="</td><td>"; $html_short.="</td><td>"; ?>
										<td width="100" align="right">
											<a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','issue_to_cut','<? echo $key; ?>')"><? echo number_format($issue_to_cut_qnty,2,'.',''); ?></a>
											<?
												$html.=number_format($issue_to_cut_qnty,2);
												$html_short.=number_format($issue_to_cut_qnty,2);
												
												$issue_toCut_array[$row[csf('buyer_name')]]+=$issue_to_cut_qnty;
												$tot_issue_to_cut_qnty+=$issue_to_cut_qnty;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100" align="right"><? $yet_to_cut_qty=$value-$issue_to_cut_qnty; echo number_format($yet_to_cut_qty,2,'.',''); $tot_yet_to_cut+=$yet_to_cut_qty; $html.=number_format($yet_to_cut_qty,2); ?></a>
										</td>
										<? $html.="</td><td>"; ?>
                                        <td width="100" align="right" title="Rec-Issue">
											<?
												$fabric_left_over=$fabric_available-$issue_to_cut_qnty;
												echo number_format($fabric_left_over,2,'.',''); 
												$html.=number_format($fabric_left_over,2);
												$tot_fabric_left_over+=$fabric_left_over;
												$tot_fabric_left_over_excel+=$fabric_left_over;
											?>
										</td>
                                        <? $html.="</td><td>"; ?>
                                        <td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
										<td> 
											<p>
												<? $fabric_desc=explode(",",$fabric_desc_details[$row[csf('job_no')]]); echo $display_font_color.join(",<br>",array_unique($fabric_desc)).$font_end; ?>
											</p>
										</td>
									</tr>
								 <?	
									if($z==1) $html.="</td><td>".join(",<br>",array_unique($fabric_desc))."</td></tr>"; else $html.="</td>&nbsp;<td></td></tr>"; 
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
												<td align='left'></td>
												<td>".$buyer_short_name_library[$row[csf('buyer_name')]]."</td>
												<td align='left'>".$row[csf('style_ref_no')]."</td>
												<td align='left'>".$row[csf('file_no')]."</td>
												<td align='left'>".$row[csf('grouping')]."</td>
												<td align='left'>".$gmts_item."</td>
												<td align='right'>".$order_qnty_in_pcs."</td>
												<td align='left'>View</td>
												<td align='center'>".$contry_ship_date."</td>
												<td align='right'>".$country_ship_qty."</td>
												<td align='right'>".$grey_cons."</td>
												<td align='right'>".$fin_cons."</td>";
								
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
                                    <td width="90" align="center"><? //echo $order_status[$row[csf('is_confirmed')]]; ?></td>
									<td width="80"><p><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></p></td>
									<td width="130"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                    <td width="100"><p><? echo $row[csf('file_no')]; ?></p></td>
                                    <td width="100"><p><? echo $row[csf('grouping')]; ?></p></td>
									<td width="140"><p><? echo $gmts_item; ?></p></td>
									<td width="100" align="right"><? echo number_format($order_qnty_in_pcs,0,'.',''); ?></td>
									<td width="80" align="center"><a href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','Shipment_date','')"><? echo "View"; ?></a></td>
                                    <td width="80"><p><? echo $contry_ship_date; ?></p></td>
                                    <td width="100" align="right"><a href="##" onClick="country_order_dtls('<? echo $row[csf('po_id')]; ?>','<? echo $start_date; ?>','<? echo $end_date; ?>','<? echo $row[csf('buyer_name')]; ?>','<? echo $row[csf('job_no')]; ?>','country_order_dtls_popup')"><? echo number_format($country_ship_qty,0,'.',''); ?></a></td>
                                    <td width="100" align="right"><? echo number_format($grey_cons,5,'.',''); ?></td>
                                    <td width="100" align="right"><? echo number_format($fin_cons,5,'.',''); ?></td>
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
									<td width="110">
										<div style="word-wrap:break-word; width:110px">
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
										</div>
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
                                    <td width="100" align="right">
										<? 
                                            if($z==1)
                                            {
                                                $d=1; 
                                                foreach($yarn_allocation_arr as $yarn_allocation_value)
                                                {
                                                    if($d!=1)
                                                    {
                                                        echo "<hr/>";
                                                        $html.="<hr/>";
                                                    }
                                                    $yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
                                                    ?>
                                                    <a href="##" onclick="openmypage('<? echo $row[csf('po_id')]; ?>','yarn_allocation_pop','<? echo $yarn_desc_for_popup_req[0]; ?>','<? echo $yarn_desc_for_popup_req[1]; ?>','<? echo $yarn_desc_for_popup_req[2]; ?>','<? echo $yarn_desc_for_popup_req[3]; ?>','<? echo $yarn_desc_for_popup_req[4]; ?>','<? echo $yarn_desc_for_popup_req[5]; ?>')"><? echo number_format($yarn_allocation_value,2,'.','');?></a>
                                                <?
                                                $html.=number_format($yarn_allocation_value,2);
                                                $d++;
                                                }
                                            }
											$html.="</td><td bgcolor='$discrepancy_td_color'>";
                                        ?>
                                    </td>
                                    <td width="100" align="right">
										<? 
                                            if($z==1)
                                            {
                                                $d=1; 
                                                foreach($yetTo_allocate_arr as $yetTo_allocate_value)
                                                {
                                                    if($d!=1)
                                                    {
                                                        echo "<hr/>";
                                                        $html.="<hr/>";
                                                    }
                                                    $yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
                                                    echo number_format($yetTo_allocate_value,2,'.','');
													$html.=number_format($yetTo_allocate_value,2);
													$d++;
                                                }
                                            }
											$html.="</td><td bgcolor='$discrepancy_td_color'>";
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
                                    <? $html.="</td><td>"; ?>
									<td width="100" align="right">
										<? 
											echo number_format($balance,2,'.','');
											$html.=number_format($balance,2);
										?>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right" bgcolor="<? echo $bgcolor_grey_td; ?>"> <? echo number_format($required_qnty,2,'.',''); $html.=number_format($required_qnty,2); ?></td>
                                    <? $html.="</td><td>"; ?>
									<td width="100" align="right" bgcolor="<? echo $discrepancy_td_color; ?>"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_receive','')"><? echo number_format($grey_recv_qnty,2,'.',''); $html.=number_format($grey_recv_qnty,2); ?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><? echo number_format($grey_balance,2,'.',''); $html.=number_format($grey_balance,2); ?></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_del_store,2,'.',''); $html.=number_format($grey_del_store,2); ?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100">&nbsp; <? $html.="&nbsp;"; ?></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_production_qnty,2,'.',''); $html.=number_format($grey_production_qnty,2); ?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_purchase','')"><? echo number_format($grey_purchase_qnty,2,'.',''); $html.=number_format($grey_purchase_qnty,2); ?></a></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_net_return','')"><? echo number_format($grey_net_return,2,'.',''); $html.=number_format($grey_net_return,2); ?></a></td>
                                    <? $html.="</td><td>"; ?>
									<td width="100" align="right">
                                    	 <a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','knit_trans','')"><? echo number_format($net_trans_knit,2,'.','');  ?></a>
										<? 
											$html.=number_format($net_trans_knit,2);
											$tot_net_trans_knit_qnty+=$net_trans_knit;
										?>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right">
										<? $grey_available=0; $grey_available=$grey_production_qnty+$grey_purchase_qnty+$net_trans_knit;
										echo number_format($grey_available,2,'.',''); $html.=number_format($grey_available,2); ?>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right">
										<?
                                            echo number_format($grey_balance,2,'.','');
											$html.=number_format($grey_balance,2);
                                        ?>
                                    </td>
									<? $html.="</td><td>"; ?>
									<td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','grey_issue','')"><? echo number_format($grey_fabric_issue,2,'.',''); $html.=number_format($grey_fabric_issue,2); ?></a>
									</td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100">&nbsp;<? $html.="&nbsp;"; ?></td>
                                    <? $html.="</td><td>"; ?>
                                    <td width="100" align="right"><a  href="##" onclick="open_febric_receive_status_order_wise_popup('<? echo $row[csf('po_id')]; ?>','batch_qnty','')"><? $html.="&nbsp;";//echo number_format($batch_color_qnty,2,'.',''); ?></a></td>
                                    <? $html.="</td><td>"; ?>
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
								<?	$tot_batch_qnty_excel+=$batch_qnty;
									$html.="</td>
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
							
				$html.="<th>&nbsp;</th>
						<th align='right'>".number_format($tot_country_ship_qty,2)."</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th align='right'>".number_format($tot_mkt_required,2)."</th>
						<th align='right'>".number_format($tot_yarnAllocationQty,2)."</th>
						<th align='right'>".number_format($tot_yetTo_allocate,2)."</th>
						<th align='right'>".number_format($tot_yarn_issue_qnty,2)."</th>
						<th align='right'>".number_format($tot_net_trans_yarn_qnty,2)."</th>
						<th align='right'>".number_format($tot_balance,2)."</th>
						
						
						<th align='right'>".number_format($tot_fabric_req,2)."</th>
						<th align='right'>".number_format($tot_grey_recv_qnty,2)."</th>
						<th align='right'>".number_format($tot_grey_prod_balance,2)."</th>
						<th align='right'>".number_format($total_grey_del_store,2)."</th>
						<th align='right'>".number_format($tot_greyKnitFloor,2)."</th>
						
						<th align='right'>".number_format($tot_grey_production_qnty,2)."</th>
						<th align='right'>".number_format($tot_grey_purchase_qnty,2)."</th>
						<th align='right'>".number_format($tot_net_gray_return,2)."</th>
						<th align='right'>".number_format($tot_net_trans_knit_qnty,2)."</th>
						<th align='right'>".number_format($tot_grey_available,2)."</th>
						<th align='right'>".number_format($tot_grey_balance,2)."</th>
						<th align='right'>".number_format($tot_grey_issue,2)."</th>
						<th>&nbsp;</th>
						
						<th align='right'>".number_format($tot_batch_qnty,2)."</th>
						<th align='right'>".number_format($tot_dye_qnty,2)."</th>
						<th align='right'>".number_format($tot_dye_qnty_balance,2)."</th>
						<th align='right'>".number_format($tot_color_wise_req,2)."</th>
						<th align='right'>".number_format($tot_fabric_recv,2)."</th>
						<th align='right'>".number_format($tot_fabric_recv_balance,2)."</th>
						<th align='right'>".number_format($tot_fin_delivery_qty,2)."</th>
						<th align='right'>".number_format($tot_finProdFloor,2)."</th>
						
						<th align='right'>".number_format($tot_fabric_production,2)."</th>
						<th align='right'>".number_format($tot_fabric_purchase,2)."</th>
						<th align='right'>".number_format($tot_fab_net_return,2)."</th>
						<th align='right'>".number_format($tot_net_trans_finish_qnty,2)."</th>
						<th align='right'>".number_format($tot_fabric_available,2)."</th>
						<th align='right'>".number_format($tot_fabric_rec_bal,2)."</th>
						<th align='right'>".number_format($tot_issue_to_cut_qnty,2)."</th>
						<th align='right'>".number_format($tot_yet_to_cut,2)."</th>
						<th align='right'>".number_format($tot_fabric_left_over,2)."</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
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
                        <th width="40">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="125">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="120">&nbsp;</th>
                        <th width="90">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="130">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="140">Total</th>
                        <th width="100" id="value_tot_order_qnty"><? echo number_format($tot_order_qnty,0); ?></th>
                        <th width="80">&nbsp;</th>
                        <?
						if(str_replace("'","",$cbo_type)==1)
						{
						?>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                        <?
						}
						?>
                        <th width="80">&nbsp;</th>
                        <th width="100" id="value_tot_country_ship_qty"><? echo number_format($tot_country_ship_qty,2); ?></th>
                        <th width="100">&nbsp;</th>
                        <th width="100">&nbsp;</th>
                        <th width="70">&nbsp;</th>
                        <th width="110">&nbsp;</th>
                        <th width="80">&nbsp;</th>
                        <th width="100" id="value_tot_mkt_required"><? echo number_format($tot_mkt_required,2); ?></th>
                        <th width="100" id="value_tot_yarnAllocationQty"><? echo number_format($tot_yarnAllocationQty,2); ?></th>
                        <th width="100" id="value_tot_yetTo_allocate"><? echo number_format($tot_yetTo_allocate,2); ?></th>
                        <th width="100" id="value_tot_yarn_issue"><? echo number_format($tot_yarn_issue_qnty,2); ?></th>
                        <th width="100" id="value_tot_net_trans_yarn"><? echo number_format($tot_net_trans_yarn_qnty,2); ?></th>
                        <th width="100" id="value_tot_yarn_balance"><? echo number_format($tot_balance,2); ?></th>
                        
                        
                        <th width="100" id="value_tot_fabric_req"><? echo number_format($tot_fabric_req,2); ?></th>
                        <th width="100" id="value_tot_grey_recv_qnty"><? echo number_format($tot_grey_recv_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_prod_balance"><? echo number_format($tot_grey_prod_balance,2); ?></th>
                        <th width="100" id="value_tot_net_del_store"><? echo number_format($total_grey_del_store,2); ?></th>
                        <th width="100" id="value_tot_greyKnitFloor"><? echo number_format($tot_greyKnitFloor,2); ?></th>
                        
                        <th width="100" id="value_tot_grey_production_qnty"><? echo number_format($tot_grey_production_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_purchase_qnty"><? echo number_format($tot_grey_purchase_qnty,2); ?></th>
                        <th width="100" id="value_tot_net_gray_return"><? echo number_format($tot_net_gray_return,2); ?></th>
                        <th width="100" id="value_tot_net_trans_knit_qnty"><? echo number_format($tot_net_trans_knit_qnty,2); ?></th>
                        <th width="100" id="value_tot_grey_available"><? echo number_format($tot_grey_available,2); ?></th>
                        <th width="100" id="value_tot_grey_balance"><? echo number_format($tot_grey_balance,2); ?></th>
                        <th width="100" id="value_tot_grey_issue"><? echo number_format($tot_grey_issue,2); ?></th>
                        <th width="100">&nbsp;</th>
                        
                        <th width="100" id="value_tot_batch"><? echo number_format($tot_batch_qnty,2); ?></th>
                        <th width="100" id="value_tot_dye_qnty"><? echo number_format($tot_dye_qnty,2); ?></th>
                        <th width="100" id="value_tot_dye_qnty_balance"><? echo number_format($tot_dye_qnty_balance,2); ?></th>
                        <th width="100" id="value_tot_fini_req"><? echo number_format($tot_color_wise_req,2); ?></th>
                        <th width="100" id="value_tot_fini_receive"><? echo number_format($tot_fabric_recv,2); ?></th>
                        <th width="100" id="value_tot_fabric_recv_balance"><? echo number_format($tot_fabric_recv_balance,2); ?></th>
                        <th width="100" id="value_tot_fin_delivery_qty"><? echo number_format($tot_fin_delivery_qty,2); ?></th>
                        <th width="100" id="value_tot_finProdFloor"><? echo number_format($tot_finProdFloor,2); ?></th>
                        
                        <th width="100" id="value_tot_fabric_production"><? echo number_format($tot_fabric_production,2); ?></th>
                        <th width="100" id="value_tot_fabric_purchase"><? echo number_format($tot_fabric_purchase,2); ?></th>
                        <th width="100" id="value_tot_fab_net_return"><? echo number_format($tot_fab_net_return,2); ?></th>
                        <th width="100" id="value_tot_trans_finish_qnty"><? echo number_format($tot_net_trans_finish_qnty,2); ?></th>
                        <th width="100" id="value_tot_fabric_available"><? echo number_format($tot_fabric_available,2); ?></th>
                        <th width="100" id="value_tot_fabric_rec_bal"><? echo number_format($tot_fabric_rec_bal,2); ?></th>
                        <th width="100" id="value_tot_issue_to_cut_qnty"><? echo number_format($tot_issue_to_cut_qnty,2); ?></th>
                        <th width="100" id="value_tot_yet_to_cut"><? echo number_format($tot_yet_to_cut,2); ?></th>
                        <th width="100" id="value_tot_fabric_left_over"><? echo number_format($tot_fabric_left_over,2); ?></th>
                        <th width="100">&nbsp;</th>
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
                                <th colspan='17'>Buyer Level Summary</th>
                            </tr>
                            <tr>
                                <th>SL</th>
                                <th>Buyer Name</th>
								<th>Order Qty.</th>
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
                            <table width="1720" class="rpt_table" border="0" rules="all" align="center">
                                <thead>
                                    <tr align="center" id="company_id_td" style="visibility:hidden; border:none">
                                        <th colspan="17" style="border:none">
                                            <font size="3"><strong>Company Name: <?php echo $company_library[$company_name]; ?></strong></font>
                                        </th>
                                    </tr>
                                    <tr align="center" id="date_td" style="visibility:hidden;border:none">
                                         <th colspan="17" style="border:none"><font size="3"><? echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></font></th>
                                    </tr>
                                    <tr align="center">
                                        <th colspan="17">Buyer Level Summary</th>
                                    </tr>
                                    <tr>
                                        <th width="40">SL</th>
                                        <th width="130">Buyer Name</th>
                                        <th width="100">Order Qty.</th>
                                        <th width="100">Grey Req</th>
                                        <th width="100">Yarn Issue + Net Transfer</th> 
                                        <th width="100">Yarn Balance</th>
                                        <th width="100">Grey Fabric Available</th>
                                        <th width="100">Grey Receive Balance</th>
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
                                    
                                    $batch_bl=$grey_issue_array[$key]-$batch_qnty_array[$key];
                                    $dye_bl=$batch_qnty_array[$key]-$dye_qnty_array[$key];
                                    
                                    $html.="<tr bgcolor='$bgcolor'>
                                            <td align='right'>".$b_sl."</td>
                                            <td align='right'>".$value."</td>
											<td align='right'>".number_format($order_qty_array[$key],2)."</td>
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
                                        <td width="100" align="right"><? echo number_format($order_qty_array[$key],2); $order_qty_array_tot+=$order_qty_array[$key]; ?></td>
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
                                        <td align="right"><? echo number_format($issue_toCut_array[$key],2); $issue_toCut_array_tot+=$issue_toCut_array[$key]; ?></td>
                                    </tr>
                                <?
                                $b_sl++;
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th width="40" colspan="2" align="right">Total</th>
                                        <th width="100" align="right"><? echo number_format($order_qty_array_tot,2);?></th>
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
                                        <th align="right"><? echo number_format($issue_toCut_array_tot,2) ;?></th>
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
										<th align='right'>".number_format($order_qty_array_tot,2)."</th>
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
										<th align='right'>".number_format($issue_toCut_array_tot,2)."</th>
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
                                       <td align='right'>".number_format($tot_grey_available,2)."</td>
                                       <td align='right'>".number_format(($tot_grey_available)/$tot_fabric_req*100,2)."%</td>
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
                                        <td align='right'>".number_format($tot_fabric_available,2)."</td>
                                        <td align='right'>".number_format($tot_fabric_available/$tot_color_wise_req*100,2)."%</td>
                                    </tr>
                                    <tr style='background-color:#CFF; font-weight:bold'>
                                        <td>Total Finish Fabric Balance</td>
                                        <td align='right'>".number_format($tot_color_wise_req-($tot_fabric_available),2)."</td>
                                        <td align='right'>".number_format(((($tot_color_wise_req-($tot_fabric_available))/$tot_color_wise_req)*100),2)."%</td>
                                    </tr>
                                    <tr bgcolor='#FFFFFF'>
                                        <td>Total Finish Fabric Issued To Cut</td>
                                        <td align='right'>".number_format($tot_issue_to_cut_qnty,2)."</td>
                                        <td align='right'>".number_format($tot_issue_to_cut_qnty/$tot_color_wise_req*100,2)."%</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
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
                                   <td align="right"><? $per_yarn_issued=$tot_yarn_issue_qnty/$tot_fabric_req*100; echo number_format($per_yarn_issued,2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Yarn Net Transfer</td>
                                   <td align="right"> <? echo number_format($tot_net_trans_yarn_qnty,2); ?></td>
                                   <td align="right"><? $per_yarn_transfer=$tot_net_trans_yarn_qnty/$tot_fabric_req*100; echo number_format($per_yarn_transfer,2)."%"; ?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                   <td>Total Yarn Balance</td>
                                   <td align="right"><? echo number_format($tot_fabric_req-($tot_yarn_issue_qnty+$tot_net_trans_yarn_qnty),2); ?></td>
                                   <td align="right"><? $per_yarn_balance=($per_yarn_issued+ $per_yarn_transfer); echo number_format($per_yarn_balance,2)."%"; ?></td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                    <td>Total Grey Fabric Required</td>
                                   <td align="right"><? echo number_format($tot_fabric_req,2); ?></td>
                                   <td>&nbsp;</td>
                                </tr>
                                <tr bgcolor="<? echo "#FFFFFF"; ?>">
                                   <td>Total Grey Fabric Available</td>
                                   <td align="right"> <? echo number_format($tot_grey_available,2); ?></td>
                                   <td align="right"><? echo number_format(($tot_grey_available)/$tot_fabric_req*100,2)."%";?></td>
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
                                   <td align="right"><? echo number_format($tot_fabric_available,2); ?></td>
                                   <td align="right"><? echo number_format(($tot_fabric_available)/$tot_color_wise_req*100,2)."%";?></td>
                                </tr>
                                <tr style="background-color:#CFF; font-weight:bold">
                                   <td>Total Finish Fabric Balance</td>
                                   <td align="right"><? echo number_format($tot_color_wise_req-($tot_fabric_available),2); ?></td>
                                   <td align="right"><? echo number_format(((($tot_color_wise_req-($tot_fabric_available))/$tot_color_wise_req)*100),2)."%"; ?></td>
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
	echo "$total_data####$filename####$filename_short####$html";
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
					
					if($yarn_count!=0) $yarn_count_cond="and c.count_id='$yarn_count'";else $yarn_count_cond="";
					if($yarn_comp_type1st!=0) $yarn_comp_type1st_cond="and c.copm_one_id='$yarn_comp_type1st'";else $yarn_comp_type1st_cond="";
					if($yarn_comp_percent1st!=0 || $yarn_comp_percent1st!='') $yarn_comp_percent1st_cond="and c.percent_one='$yarn_comp_percent1st'";else $yarn_comp_percent1st_cond="";
					if($yarn_comp_type2nd!=0 || $yarn_comp_type2nd!="") $yarn_comp_type2nd_cond="and c.copm_two_id='$yarn_comp_type2nd'";else $yarn_comp_type2nd_cond="";
					if($yarn_type_id!=0 ) $yarn_type_id_cond="and c.type_id='$yarn_type_id'";else $yarn_type_id_cond="";
					
                    $i=1; $tot_req_qnty=0;
					 $sql="select a.buyer_name, a.job_no, a.total_set_qnty as ratio, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut, sum(c.cons_qnty) as qnty from wo_po_details_master a, wo_po_break_down b, wo_pre_cost_fab_yarn_cost_dtls c where a.job_no=b.job_no_mst and a.job_no=c.job_no and b.id in($order_id)  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $yarn_count_cond $yarn_comp_type1st_cond $yarn_comp_percent1st_cond $yarn_comp_type2nd_cond  $yarn_comp_type2nd_cond group by b.id, a.buyer_name, a.job_no, a.total_set_qnty, b.po_number, b.po_quantity, b.pub_shipment_date, b.plan_cut";
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
        	<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $yarn_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=50 and po_number_id in ($order_id) and is_deleted=0 and status_active=1"; 
				
				$tna_sql=sql_select($yarn_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
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
						
					$issue_to="";
					if($row[csf('knit_dye_source')]==1) 
					{
						$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
					}
					else
					{
						$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
					}
					
						
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
					
					$return_from="";
					if($row[csf('knitting_source')]==1) 
					{
						$return_from=$company_library[$row[csf('knitting_company')]]; 
					}
					else
					{
						$return_from=$supplier_details[$row[csf('knitting_company')]];
					}
						
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
        	<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $yarn_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=50 and po_number_id in ($order_id) and is_deleted=0 and status_active=1"; 
				
				$tna_sql=sql_select($yarn_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
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
						
							$issue_to="";
							if($row[csf('knit_dye_source')]==1) 
							{
								$issue_to=$company_library[$row[csf('knit_dye_company')]]; 
							}
							else 
							{
								$issue_to=$supplier_details[$row[csf('knit_dye_company')]];
							}
								
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
						
						$sql="select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot, c.yarn_type, c.id as prod_id, c.product_name_details, c.brand from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, c.lot, c.yarn_type, c.product_name_details, c.brand";
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
								<td width="70"><p><? echo $brand_array[$row[csf('brand')]]; ?>&nbsp;</p></td>
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
        	<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $gray_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=60 and po_number_id in ($order_id) and is_deleted=0 and status_active=1"; 
				
				$tna_sql=sql_select($gray_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
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

if($action=="grey_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
?>
<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_delivery_qnty"],
						   col: [7],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
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
	<div style="width:720px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:720px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="8"><b>Grey Delivery To Store Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Challan No</th>
                    <th width="75">Delivery Date</th>
                    <th width="115">Production ID</th>
                    <th width="180">Product Details</th>
                    <th width="50">GSM</th>
                    <th width="50">Dia</th>
                    <th>Delivery Qnty</th>
				</thead>
             </table>
             <div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 
					
                   // $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";
				   
				   //select order_id, sum(current_delivery) as grey_delivery_qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 $grey_delivery_po_cond group by order_id
				    $sql="select a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, sum(b.current_delivery) as delivery_qty from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.entry_form in (53,56) and b.order_id in ($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    
                        $total_delivery_qnty+=$row[csf('delivery_qty')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="75"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
                            <td width="115"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
                            <td width="180"><p><? echo $product_arr[$row[csf('product_id')]]; ?>&nbsp;</p></td>
                            <td width="50"><? echo $row[csf('gsm')]; ?></td>
                            <td width="50"><? echo $row[csf('dia')]; ?></td>
                            <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="75">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="180">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50" align="right">Total</th>
                    <th align="right" id="value_delivery_qnty"><? echo number_format($total_delivery_qnty,2,'.',''); ?></th>
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
	$order_id=explode('_',$order_id);
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
                    <th width="125">Receive Id</th>
                    <th width="95">Receive Basis</th>
                     <th width="150">Product Details</th>
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
					if($order_id[1]==9) $receive_basis_cond=" and a.receive_basis=9"; else if($order_id[1]==0) $receive_basis_cond=" and a.receive_basis<>9";
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
					
                   $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $receive_basis_cond and a.entry_form=22 and c.entry_form=22 and c.po_breakdown_id in($order_id[0]) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id";
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
                            <td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
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
                            <td><p><? if ($row[csf('knitting_source')]==1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')]==3) echo $supplier_details[$row[csf('knitting_company')]]; ?>&nbsp;</p></td>
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
<?
exit();
}

if($action=="batch_qnty")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode('_',$order_id);
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
                    $sql="select a.batch_no, a.batch_date, a.color_id, sum(b.batch_qnty) as quantity from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.po_id in($ex_data[0]) and a.color_id='$ex_data[1]' and a.status_active=1 and a.batch_against<>2 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.batch_no, a.batch_date, a.color_id";
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
                        <th width="100">Issue To</th>
                        <th width="115">Booking No</th>
                        <th width="90">Batch No</th>
                        <th width="80">Issue Date</th>
                        <th width="100">Issue Qnty (In)</th>
                        <th>Issue Qnty (Out)</th>
                    </tr>
				</thead>
             </table>
             <div style="width:877px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="860" cellpadding="0" cellspacing="0">
                    <?
					/*$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
								sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_rollwise,
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,11,13,16,61) $trans_po_cond group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans']=$row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')];
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans']=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
			$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue')]+$row[csf('grey_issue_rollwise')];
		}*/
					
                    $i=1; $issue_to='';
                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=16 and c.entry_form=16 and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no";
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
                    
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                            <td width="100"><p><? echo $issue_to; ?></p></td>
                            <td width="115"><? echo $row[csf('booking_no')]; ?>&nbsp;</td>
                            <td width="90"><p><? echo $batch_details[$row[csf('batch_no')]]; ?>&nbsp;</p></td>
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
        <table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $dye_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=61 and po_number_id in ($order_id) and is_deleted=0 and status_active=1"; 
				
				$tna_sql=sql_select($dye_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
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
        	<table border="1" class="rpt_table" rules="all" width="300" cellpadding="0" cellspacing="0" align="center">
            	<thead>
                	<tr>
						<th colspan="3"><b>TNA Details</b></th>
                    </tr>
                    <tr>
                    	<th>Date Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
				</thead>
                <tbody>
                <? $fin_tna="select task_start_date, task_finish_date, actual_start_date, actual_finish_date from tna_process_mst where task_number=73 and po_number_id in ($order_id) and is_deleted=0 and status_active=1"; 
				
				$tna_sql=sql_select($fin_tna);
				?>
                    <tr bgcolor="#E9F3FF">
                    	<td>Plan</td>
                        <td><? if($tna_sql[0][csf('task_start_date')]=="" || $tna_sql[0][csf('task_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('task_finish_date')]=="" || $tna_sql[0][csf('task_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('task_finish_date')]); ?></td>
					</tr>
                	<tr bgcolor="#FFFFFF">
                    	<td>Actual</td>
                        <td><? if($tna_sql[0][csf('actual_start_date')]=="" || $tna_sql[0][csf('actual_start_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_start_date')]); ?></td>
                        <td><? if($tna_sql[0][csf('actual_finish_date')]=="" || $tna_sql[0][csf('actual_finish_date')]=='0000-00-00') echo ""; else echo change_date_format($tna_sql[0][csf('actual_finish_date')]); ?></td>
					</tr>
                </tbody>
            </table>
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
	$order_id=explode('_',$order_id);
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
					if($order_id[1]==9) $receive_basis_cond=" and a.receive_basis=9"; else if($order_id[1]==0) $receive_basis_cond=" and a.receive_basis<>9";
                    $i=1;
                    $total_fabric_recv_qnty=0; $dye_company='';
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(37,68) and c.entry_form in(37,68) and c.po_breakdown_id in($order_id[0]) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $receive_basis_cond group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.knitting_company, b.batch_id, b.prod_id";// and a.receive_basis<>9
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

if($action=="finish_delivery_to_store")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_data=explode('_',$order_id);
	$color_arr = return_library_array("select id, color_name from lib_color","id","color_name");
?>
<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_delivery_qnty"],
						   col: [8],
						   operation: ["sum"],
						   write_method: ["innerHTML"]
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
	<div style="width:720px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:720px;">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="9"><b>Finish Delivery To Store Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Challan No</th>
                    <th width="70">Delivery Date</th>
                    <th width="115">Production ID</th>
                    <th width="160">Product Details</th>
                    <th width="50">GSM</th>
                    <th width="50">Dia</th>
                    <th width="70">Color</th>
                    <th>Delivery Qnty</th>
				</thead>
             </table>
             <div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_delivery_fin_qnty=0;
					
				    $sql="select a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, sum(b.current_delivery) as delivery_qty, c.product_name_details, c.color from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b, product_details_master c where a.id=b.mst_id and b.product_id=c.id and a.entry_form=54 and b.order_id in ($ex_data[0]) and c.color='$ex_data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.sys_number_prefix_num, a.sys_number, a.delevery_date, b.grey_sys_number, b.product_id, b.construction, b.composition, b.gsm, b.dia, c.product_name_details, c.color";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('sys_number')]; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row[csf('delevery_date')]); ?></p></td>
                            <td width="115"><p><? echo $row[csf('grey_sys_number')]; ?></p></td>
                            <td width="160"><p><? echo $row[csf('product_name_details')]; ?>&nbsp;</p></td>
                            <td width="50"><? echo $row[csf('gsm')]; ?></td>
                            <td width="50"><? echo $row[csf('dia')]; ?></td>
                            <td width="70"><? echo $color_arr[$row[csf('color')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
                        </tr>
                    <?
					$total_delivery_fin_qnty+=$row[csf('delivery_qty')];
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="160">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="70" align="right">Total</th>
                    <th align="right" id="value_delivery_qnty"><? echo number_format($total_delivery_fin_qnty,2,'.',''); ?></th>
                </tfoot>
            </table>	
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
                	<th width="50">SL</th>
                    <th width="120">Issue No</th>
                    <th width="100">Challan No</th>
                    <th width="80">Issue Date</th>
                    <th width="120">Batch No</th>
                    <th width="110">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:738px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
                    <?
					
                    $i=1; $total_issue_to_cut_qnty=0;
                    $sql="select a.issue_number, a.issue_date, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.color_id='$color' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date, a.challan_no, b.batch_id, b.prod_id";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	
                    
                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                            <td width="120"><p><? echo $batch_details[$row[csf('batch_id')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
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

if($action=="yarn_allocation_pop")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
                    	<th width="40">SL</th>
                        <th width="100">Lot</th>
                        <th width="70">Count</th>
                        <th width="200">Composition</th>
                        <th width="130">Supplier</th>
                        <th>Allocated Qty</th>
                    </tr>
				</thead>
                <?
				$sql="select a.po_break_down_id, sum(a.qnty) as allocation_qty, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id from inv_material_allocation_dtls a, product_details_master c where a.item_id=c.id and a.po_break_down_id in ($order_id) and c.yarn_count_id='$yarn_count' and c.yarn_comp_type1st='$yarn_comp_type1st' and c.yarn_comp_percent1st='$yarn_comp_percent1st' and c.yarn_comp_type2nd='$yarn_comp_type2nd' and c.yarn_comp_percent2nd='$yarn_comp_percent2nd' and c.yarn_type='$yarn_type_id' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 group by a.po_break_down_id, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id";
				
                $total_allocation_qty=0; $i=1;
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    ?>
                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="40"><? echo $i; ?></td>
                    	<td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
                        <td width="70"><? echo $yarn_count_details[$row[csf('yarn_count_id')]]; ?></td>
                        <td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
                        <td width="130"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?></p></td>
                        <td align="right"><? echo number_format($row[csf('allocation_qty')],2); ?> </td>
                    </tr>
                <?
					$total_allocation_qty+=$row[csf('allocation_qty')];
					$i++;
                }
                ?>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Allocation</th>
                    <th><? echo number_format($total_allocation_qty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="yarn_allocation_not")
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
			<table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                    	<th width="40">SL</th>
                        <th width="100">Lot</th>
                        <th width="70">Count</th>
                        <th width="200">Composition</th>
                        <th width="130">Supplier</th>
                        <th>Allocated Qty</th>
                    </tr>
				</thead>
                <?
				$i=1; $total_yarn_issue_qnty=0; $total_yarn_issue_qnty_out=0; $yarn_desc_array_for_return=array();
				$sql_yarn_iss="select b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type from inv_material_allocation_dtls a, product_details_master b where a.item_id=b.id and a.po_break_down_id in ($order_id) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 group by b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
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
						$sql="select a.po_break_down_id, sum(a.qnty) as allocation_qty, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id from inv_material_allocation_dtls a, product_details_master c where a.item_id=c.id and a.po_break_down_id in ($order_id) and c.yarn_count_id='".$row_yarn_iss[csf('yarn_count_id')]."' and c.yarn_comp_type1st='".$row_yarn_iss[csf('yarn_comp_type1st')]."' and c.yarn_comp_percent1st='".$row_yarn_iss[csf('yarn_comp_percent1st')]."' and c.yarn_comp_type2nd='".$row_yarn_iss[csf('yarn_comp_type2nd')]."' and c.yarn_comp_percent2nd='".$row_yarn_iss[csf('yarn_comp_percent2nd')]."' and c.yarn_type='".$row_yarn_iss[csf('yarn_type')]."' and a.status_active=1 and a.is_deleted=0 and a.item_category=1 group by a.po_break_down_id, c.lot, c.yarn_type, c.yarn_count_id, c.product_name_details, c.supplier_id";

						$result=sql_select($sql);
						foreach($result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40"><? echo $i; ?></td>
								<td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
								<td width="70"><? echo $yarn_count_details[$row[csf('yarn_count_id')]]; ?></td>
								<td width="200"><p><? echo $row[csf('product_name_details')]; ?></p></td>
								<td width="130"><p><? echo $supplier_details[$row[csf('supplier_id')]]; ?></p></td>
								<td align="right"><? echo number_format($row[csf('allocation_qty')],2); ?> </td>
							</tr>
						<?
							$total_allocation_qty+=$row[csf('allocation_qty')];
							$i++;
						}
					}
				}
				?>
                <tfoot>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total Allocation</th>
                    <th><? echo number_format($total_allocation_qty,2);?></th>
                </tfoot>
            </table>	
		</div>
	</fieldset>  
<?
exit();
}

if($action=="country_order_dtls_popup")
{
	echo load_html_head_contents("Country Order Dtls Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$order_arr=return_library_array( "select id, po_number from wo_po_break_down where id in ($po_id)", "id", "po_number");
	$country_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	?>
	<script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_div').innerHTML+'</body</html>');
			d.close();
		}	
	</script>	
	<fieldset style="width:670px; margin-left:3px">
        <div style="width:670px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="report_div" align="center">
            <table rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <tr> 
                	<td colspan="4" align="center"><strong> Country Wise Order Details </strong></td>
                </tr>
                <tr> 
                    <td width="130"><strong>Job No. :&nbsp; <? echo $job_no; ?>;</strong></td>
                    <td width="150"><strong> Order:&nbsp;<? echo $order_arr[$po_id]; ?>;</strong></td>
                    <td width="150"><strong> Buyer:&nbsp;<? echo $buyer_library[$buyer_id]; ?>;</strong></td>
                    <td><!--<strong> Country Ship Date:&nbsp;<? //echo change_date_format($country_date); ?></strong>--></td>
                </tr>
            </table>
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Country</th>
                    <th width="100">Cut Off</th>
                    <th width="90">Order Qty</th>
                    <th width="60">Avg Exc. %</th>
                    <th width="90">Plan Cut Qty.</th>
                    <th width="60">Avg Rate</th>
                    <th>Order Value</th>
                </thead>
                <tbody>
                <?
				if ($start_date=="" && $end_date=="") $country_ship_date_cond=""; else $country_ship_date_cond="and country_ship_date between '$start_date' and '$end_date'";
				$contry_sql="select country_id, cutup, sum(order_quantity) as po_qty, sum(plan_cut_qnty) as plan_cut_qty, sum(order_total) as order_value from wo_po_color_size_breakdown where po_break_down_id in ($po_id) and status_active=1 and is_deleted=0 $country_ship_date_cond group by country_id, cutup";
				$contry_sql_result=sql_select($contry_sql); $i=1;
				foreach($contry_sql_result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
					
					$avg_ex_per=0; $avg_rate=0;
					$avg_ex_per=(($row[csf('plan_cut_qty')]-$row[csf('po_qty')])/$row[csf('po_qty')])*100;
					$avg_rate=($row[csf('order_value')]/$row[csf('po_qty')]);
					?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
						<td width="30"><p><? echo $i; ?></p></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $country_arr[$row[csf('country_id')]]; ?></div></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><? echo $cut_up_array[$row[csf('cutup')]]; ?></div></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('po_qty')]); ?></p></td>
						<td width="60" align="right"><p><? echo number_format($avg_ex_per,3).' %'; ?></p></td>
						<td width="90" align="right"><p><? echo number_format($row[csf('plan_cut_qty')]); ?></p></td>
						<td width="60" align="right"><p><? echo number_format($avg_rate,4); ?></p></td>
                        <td align="right"><p><? echo number_format($row[csf('order_value')],2); ?></p></td>
					</tr>
					<?
					$tot_po_qty+=$row[csf('po_qty')];
					$tot_plan_cut_qty+=$row[csf('plan_cut_qty')];
					$tot_order_value+=$row[csf('order_value')];
					$i++;
				}
				?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="3" align="right">Total</td>
						<td align="right"><? echo number_format($tot_po_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($tot_plan_cut_qty,2); ?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo number_format($tot_order_value,2); ?></td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}

?>
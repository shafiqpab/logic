<? 
header('Content-type:text/html; charset=utf-8');
session_start();
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');

//$condition= new condition();
//$fabric= new fabric($condition);

$user_name=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name = str_replace("'","",$cbo_company_name);


	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_name=$cbo_buyer_name";
	}

	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));

	$cbo_year=str_replace("'","",$cbo_year_selection);

	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) 
		{			
			$year_cond=" and YEAR(d.insert_date)=$cbo_year";
		}
		else if($db_type==2) 
		{
			$year_cond=" and to_char(d.insert_date,'YYYY')=$cbo_year";
		}else {
			$year_cond="";
		}
	}
	else {
		$year_cond="";
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if(trim($txt_job_no)!="")
	{
		$job_no=trim($txt_job_no); 
		$job_no_cond=" and d.job_no_prefix_num=$job_no";
	}

	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$booking_no_cond="";
	if(trim($txt_booking_no)!="")
	{
		$booking_no=trim($txt_booking_no); 

		$booking_no_cond=" and a.booking_no_prefix_num =$booking_no";

	}

	if ($start_date=="" && $end_date=="") $country_date_cond=""; else $country_date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";

	if(trim($txt_style)!="")
	{
		$style = trim(str_replace("'","",$txt_style)); 
		$style_cond=" and d.style_ref_no like '%$style%'";
	}


	//$tempJobCond = "and d.job_no = 'BPKW-18-00748' ";

	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	}
	if(str_replace("'","",$txt_job_no) !=''){
		  $condition->job_no_prefix_num("=$txt_job_no");
	}
	if(str_replace("'","",$txt_style) !=''){
		  $condition->style_ref_no("=$txt_style");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
		  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish();
	//echo $fabric->getQuery(); die;
	//print_r($fabric_costing_arr);

	if($booking_no_cond)
	{
		$bookingInfosql ="SELECT d.booking_no,e.job_no from wo_booking_mst a, wo_booking_dtls d, wo_pre_cos_fab_co_avg_con_dtls e where a.booking_no = d.booking_no and d.pre_cost_fabric_cost_dtls_id=e.pre_cost_fabric_cost_dtls_id and d.po_break_down_id=e.po_break_down_id and d.color_size_table_id=e.color_size_table_id  and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $booking_no_cond group by d.booking_no, e.job_no";

		//echo $bookingInfosql;
		$resultbookingInfo=sql_select($bookingInfosql);
		$jobData =array(); 
		foreach($resultbookingInfo as $row)
		{	
			if($duplicate_chk[$row[csf('job_no')]]=='')
			{
				$duplicate_chk[$row[csf('job_no')]] = $row[csf('job_no')];
				array_push($jobData,$row[csf('job_no')]);
			}
		}
		
		unset($resultbookingInfo);	
	}

	$job_data_cond="";
	if($jobData)
	{
		$job_data_cond=" ".where_con_using_array($jobData,1,'d.job_no')." ";
	}


	$sql="select d.company_name, d.buyer_name, d.job_no_prefix_num, d.job_no, d.style_ref_no, b.id as po_id,b.po_quantity as po_qnty, b.pub_shipment_date from wo_po_details_master d, wo_po_break_down b where d.job_no=b.job_no_mst and d.company_name='$company_name' $country_date_cond $year_cond $job_no_cond $buyer_id_cond $style_cond $tempJobCond $job_data_cond and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0 and b.status_active=1 group by d.company_name, d.job_no_prefix_num, d.job_no, d.style_ref_no,d.buyer_name,b.id, b.po_number, b.po_quantity, b.pub_shipment_date order by d.job_no,b.pub_shipment_date, b.id"; 

	//echo $sql;
	    $nameArray=sql_select($sql);

		if(count($nameArray)>0)
		{
			foreach($nameArray as $row)
			{
				$tot_rows++;				

				$job_alldata_arr[$row[csf("job_no")]]['company_name'] = $row[csf("company_name")];
				$job_alldata_arr[$row[csf("job_no")]]['buyer_name']   = $row[csf("buyer_name")];
				$job_alldata_arr[$row[csf("job_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
				$job_alldata_arr[$row[csf("job_no")]]['po_id'].= $row[csf("po_id")].",";
				$job_alldata_arr[$row[csf("job_no")]]['pub_shipment_date'] = $row[csf("pub_shipment_date")];
				$job_alldata_arr[$row[csf("job_no")]]['job_qnty'] += $row[csf("po_qnty")];

				$po_wise_job_arr[$row[csf("po_id")]] = $row[csf("job_no")];
				
				$jobNo .= "'".$row[csf("job_no")]."',";
				$poIds.=$row[csf("po_id")].",";
			}							
		}
		else
		{
			echo "3**".'Data Not Found'; die;
		}
		unset($nameArray);	

		if($jobNo!="")
		{
			$jobNo = implode(",",array_unique(explode(",",chop($jobNo,',')))); 
			$job_no_cond=""; 
			$job_no_mst_cond=""; 

			if($db_type==2 && $tot_rows>1000)
			{
				$job_no_cond=" and (";
				$job_no_mst_cond=" and (";
				
				$jobNoArr=array_chunk(explode(",",$jobNo),999);
				foreach($jobNoArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_no_cond.=" d.job_no in($ids) or ";	
					$job_no_mst_cond.=" d.job_no_mst in($ids) or ";	
				}
				$job_no_cond=chop($job_no_cond,'or ');
				$job_no_mst_cond=chop($job_no_mst_cond,'or ');
				$job_no_cond.=")";
				$job_no_mst_cond.=")";
			}
			else
			{
				$job_no_cond=" and d.job_no in ($jobNo)";
				$job_no_mst_cond=" and d.job_no_mst in ($jobNo)";
			}
		}
		// po id condition
		if($poIds!="")
		{
			$poIds = implode(",",array_unique(explode(",",chop($poIds,',')))); 
			$po_id_cond=""; 
			$po_id_bokking_cond=""; 

			if($db_type==2 && $tot_rows>1000)
			{
				$po_id_cond=" and (";
				
				$poIdArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_id_cond.=" c.po_breakdown_id in($ids) or ";	
					$po_id_bokking_cond.=" e.po_break_down_id in($ids) or ";	
					
				}
				$po_id_cond=chop($po_id_cond,'or ');
				$po_id_cond.=")";

				$po_id_bokking_cond=chop($po_id_bokking_cond,'or ');
				$po_id_bokking_cond.=")";
			}
			else
			{
				$po_id_cond=" and c.po_breakdown_id in ($poIds)";
				$po_id_bokking_cond=" and e.po_break_down_id in ($poIds)";
			}
		}

		if($job_no_cond!="" &&  $po_id_bokking_cond!="")
		{
			$booking_sql ="select sum(d.fin_fab_qnty) as fin_fab_qnty,d.booking_no,e.job_no from wo_booking_dtls d, wo_pre_cos_fab_co_avg_con_dtls e where d.pre_cost_fabric_cost_dtls_id=e.pre_cost_fabric_cost_dtls_id and d.po_break_down_id=e.po_break_down_id and d.color_size_table_id=e.color_size_table_id $po_id_bokking_cond $job_no_cond and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 group by d.booking_no, e.job_no";

			$resultBooking=sql_select($booking_sql);
			$bookingData =array(); 
			foreach($resultBooking as $row)
			{	
				$bookingData[$row[csf('job_no')]]['booking_no'] .= $row[csf('booking_no')].",";			
				//$bookingData[$row[csf('job_no')]]['finish_fab_required'] = $row[csf('fin_fab_qnty')];
			}
			
			unset($resultBooking);	
		}
	


		if($job_no_cond!="")
		{		
			$jobNo = implode(",",array_unique(explode(",",chop($jobNo,',')))); 
			$gmtsitem_ratio_array=array();		
			$grmts_sql = sql_select("select d.job_no,d.gmts_item_id,d.set_item_ratio from wo_po_details_mas_set_details d where d.job_no in ($jobNo)");
			foreach($grmts_sql as $row)
			{
				$gmtsitem_ratio_array[$row[csf('job_no')]][$row[csf('gmts_item_id')]]=$row[csf('set_item_ratio')];
			}

			$precost_mst = sql_select("select d.costing_per,d.job_no from wo_pre_cost_mst d where d.status_active = 1 and d.is_deleted = 0 $job_no_cond"); 
			
			$order_price_per_dzn_arr = array();

			foreach($precost_mst as $row)
			{				
				if($row[csf("costing_per")]==1){
					$order_price_per_dzn=12;
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
					//$costing_for=" DZN";
				}
				else if($row[csf("costing_per")]==2){
					$order_price_per_dzn=1;
					//$costing_for=" PCS";
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
				}
				else if($row[csf("costing_per")]==3){
					$order_price_per_dzn=24;
					//$costing_for=" 2 DZN";
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
				}
				else if($row[csf("costing_per")]==4){
					$order_price_per_dzn=36;
					//$costing_for=" 3 DZN";
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
				}
				else if($row[csf("costing_per")]==5){
					$order_price_per_dzn=48;
					//$costing_for=" 4 DZN";
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
				}
			}

			$sql_y=sql_select("select c.item_number_id,c.order_quantity ,c.plan_cut_qnty,d.id,f.job_no,f.color,f.count_id,f.copm_one_id,f.percent_one,f.copm_two_id,f.percent_two,f.type_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,e.requirment   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id $job_no_cond $po_id_bokking_cond and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1");

			//and f.job_no='RpC-19-00001'
			$dataArrayYarn=array(); 
			foreach($sql_y as $sql_y_r)
			{	
				$set_item_ratio=$gmtsitem_ratio_array[$sql_y_r[csf('job_no')]][$sql_y_r[csf('item_number_id')]];
				$order_price_per_dzn = $order_price_per_dzn_arr[$sql_y_r[csf('job_no')]]['costing_per'];

				$cons_qnty = def_number_format((($sql_y_r[csf("requirment")]*$sql_y_r[csf("cons_ratio")]/100)*($sql_y_r[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))),5,"");

				$yarnRequiredArray[$sql_y_r[csf('job_no')]][$sql_y_r[csf("count_id")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("copm_two_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("percent_two")]][$sql_y_r[csf("color")]][$sql_y_r[csf("rate")]]["req_quantity"]+=$cons_qnty;

				$countSummaryArr[$sql_y_r[csf("count_id")]][$sql_y_r[csf("type_id")]]+=$cons_qnty;

			}
			unset($resultYarn);

			//echo "<pre>";
			//print_r($dataArrayYarn);

			//die();			
			$yarn_sql = sql_select("select min(d.id) as id, d.job_no, d.count_id, d.copm_one_id, d.percent_one, d.copm_two_id, d.percent_two, d.color,d.type_id, min(d.cons_ratio) as cons_ratio, sum(d.cons_qnty) as cons_qnty, d.rate, sum(d.amount) as amount from wo_pre_cost_fab_yarn_cost_dtls d where d.status_active=1 and d.is_deleted=0 $job_no_cond group by d.job_no,d.count_id, d.copm_one_id, d.percent_one, d.copm_two_id, d.percent_two, d.color,d.type_id, d.rate");

			$s = 1;
			foreach($yarn_sql as $yarnRow)
			{
				$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('percent_two')]."**".$cons_qnty."**".$yarnRow[csf('color')]."**".$yarnRow[csf('rate')].",";

				$yarnKey = $yarnRow[csf('count_id')]."__".$yarnRow[csf('type_id')]."__".$yarnRow[csf('copm_one_id')]."__".$yarnRow[csf('copm_two_id')]."__".$yarnRow[csf('percent_one')]."__".$yarnRow[csf('percent_two')];

				$yarn_desc_array[$s]=$yarnKey;

				$jobWiseYarnKeys[$yarnRow[csf('job_no')]].= $yarnKey.",";
				
					$job_wise_yarn_req_quantity[$yarnRow[csf('job_no')]] += $yarnRequiredArray[$yarnRow[csf('job_no')]][$yarnRow[csf('count_id')]][$yarnRow[csf('type_id')]][$yarnRow[csf('copm_one_id')]][$yarnRow[csf('copm_two_id')]][$yarnRow[csf('percent_one')]][$yarnRow[csf('percent_two')]][$yarnRow[csf('color')]][$yarnRow[csf('rate')]]["req_quantity"];

				$s++;
			}

			$sql_work_order = "select d.job_no, d.buyer_id,d.yarn_count, d.yarn_comp_type1st,nvl(d.yarn_comp_type2nd,0) as yarn_comp_type2nd, d.yarn_comp_percent1st,nvl(d.yarn_comp_percent2nd,0) as yarn_comp_percent2nd,d.yarn_type, d.color_name, d.req_quantity, d.supplier_order_quantity,d.rate from wo_non_order_info_mst a, wo_non_order_info_dtls d where a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.id=d.mst_id and d.item_category_id=1 $job_no_cond";

			$resultYarnwork=sql_select($sql_work_order);
			$yearnWorkOrderData = array();
			foreach($resultYarnwork as $yarnworkRow)
			{
				$yarnKey = $yarnworkRow[csf('yarn_count')]."__".$yarnworkRow[csf('yarn_type')]."__".$yarnworkRow[csf('yarn_comp_type1st')]."__".$yarnworkRow[csf('yarn_comp_type2nd')]."__".$yarnworkRow[csf('yarn_comp_percent1st')]."__".$yarnworkRow[csf('yarn_comp_percent2nd')];

				$yearnWorkOrderData[$yarnworkRow[csf('job_no')]][$yarnKey]['work_order_qty'] += $yarnworkRow[csf('supplier_order_quantity')];

				$jobYarnKey[$yarnKey]=$yarnworkRow[csf('job_no')];

			}
			unset($resultYarnwork);
		}

		/*echo "<pre>";
		print_r($yearnWorkOrderData);*/

		$sql_yarn_rcv = "select a.id,b.prod_id,b.cons_quantity,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd from inv_receive_master a, inv_transaction b, wo_non_order_info_mst c , wo_non_order_info_dtls d,product_details_master e where a.id=b.mst_id and b.pi_wo_batch_no=c.id and c.id=d.mst_id $job_no_cond and b.item_category=1 and b.transaction_type=1 and a.entry_form=1 and a.receive_basis in(1,2) and b.prod_id=e.id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.item_category_id=1 and e.status_active=1 and e.is_deleted=0 group by a.id,b.prod_id,b.cons_quantity,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd

		union all 

		select a.id,b.prod_id,b.cons_quantity,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd 
		from inv_receive_master a, inv_transaction b, wo_non_order_info_dtls d,product_details_master e, com_pi_item_details f
		where a.id=b.mst_id and a.booking_id=f.pi_id $job_no_cond and b.item_category=1 
		and b.transaction_type=1 and a.entry_form=1 and a.receive_basis in(1) and b.prod_id=e.id and a.company_id=$company_name
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
		and d.is_deleted=0 and e.item_category_id=1 and e.status_active=1 and e.is_deleted=0 and d.id=f.work_order_dtls_id
		group by a.id,b.prod_id,b.cons_quantity,d.job_no,d.buyer_id,
		d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd";

		$yarnRcvArray=sql_select($sql_yarn_rcv);

		if(count($yarnRcvArray)>0)
		{
			foreach($yarnRcvArray as $row)
			{			
				$yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

				$yearnRcvData[$row[csf('job_no')]][$yarnKey]['rcv_quantity'] += $row[csf('cons_quantity')];	
				$yearnRcvData[$row[csf('job_no')]][$yarnKey]['prod_id'] .= $row[csf('prod_id')].",";	

				$prodId .= $row[csf("prod_id")].",";	
			}
		}
		unset($yarnRcvArray);

		/*echo "<pre>";
		print_r($yearnRcvData);

		die();*/

		if($prodId!="")
		{
			$prodId= implode(",",array_unique(explode(",",chop($prodId,',')))); 
			$prod_id_cond=""; 
			if($db_type==2 && $tot_rows>1000)
			{
				$prod_id_cond=" and (";
				
				$prodIdArr=array_chunk(explode(",",$prodId),999);
				foreach($prodIdArr as $ids)
				{
					$ids=implode(",",$ids);
					$prod_id_cond.=" b.prod_id in($ids) or ";	
				}
				$prod_id_cond=chop($prod_id_cond,'or ');
				$prod_id_cond.=")";
			}
			else
			{
				$prod_id_cond=" and b.prod_id in ($prodId)";
			}
		}	
			
		$sql_yarn_iss ="select a.knit_dye_source,c.prod_id,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type, sum(CASE WHEN c.entry_form ='3' THEN c.quantity ELSE 0 END) AS issue_qnty from inv_issue_master a,inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and c.issue_purpose!=2 $po_id_cond group by a.knit_dye_source,c.prod_id,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type";

		$yarnIssueArray=sql_select($sql_yarn_iss);
		
		if(count($yarnIssueArray)>0)
		{
			$s = 1;
			foreach($yarnIssueArray as $row)
			{			
					$yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

					$jobno = $po_wise_job_arr[$row[csf('po_breakdown_id')]];

					if(in_array($yarnKey,$yarn_desc_array))
					{
						if($row[csf('knit_dye_source')]==1)
						{
							$yearnIssueData[$jobno][$yarnKey]['inhouse']['issue_quantity'] += $row[csf('issue_qnty')];					
						}else{
							$yearnIssueData[$jobno][$yarnKey]['out_bound']['issue_quantity'] += $row[csf('issue_qnty')];
						}


					}else{
						if($row[csf('knit_dye_source')]==1)
						{

							$yearnIssueData[$jobno]['not_req']['inhouse']['issue_quantity'] += $row[csf('issue_qnty')];
						}else{
			
							$yearnIssueData[$jobno]['not_req']['out_bound']['issue_quantity'] += $row[csf('issue_qnty')];
						}	
					}

					$s++;		
			}
		}
		unset($yarnIssueArray);

		$sql_yarn_iss_rtn="select a.knitting_source,c.po_breakdown_id,c.prod_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type, sum(CASE WHEN c.entry_form ='9' THEN c.quantity ELSE 0 END) AS issue_rtn_qty from inv_receive_master a,inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and c.issue_purpose!=2 $po_id_cond group by a.knitting_source,c.po_breakdown_id, c.prod_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type";

		$yarnIssueRtnArray=sql_select($sql_yarn_iss_rtn);

		if(count($yarnIssueRtnArray)>0)
		{
			foreach($yarnIssueRtnArray as $row)
			{			
				$yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];
				$jobno = $po_wise_job_arr[$row[csf('po_breakdown_id')]];
				
				if(in_array($yarnKey,$yarn_desc_array))
				{
					if($row[csf('knitting_source')]==1)
					{
						$yearnIssueRtnData[$jobno][$yarnKey]['inhouse']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];		
					}else if($row[csf('knit_dye_source')]==3){
						$yearnIssueRtnData[$jobno][$yarnKey]['out_bound']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];
					}	
				}else{

					if($row[csf('knitting_source')]==1)
					{
						$yearnIssueRtnData[$jobno]['not_req']['inhouse']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];
					}else{
						$yearnIssueRtnData[$jobno]['not_req']['out_bound']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];
					}
				}	
			}
		}
		unset($yarnIssueRtnArray);

		$greyPurchaseQntyArray=array();
		$sql_grey_production="select a.knitting_source,c.po_breakdown_id, 
		sum(CASE WHEN a.receive_basis not in(9,10) THEN c.quantity ELSE 0 END) AS grey_purchase_qnty,
		sum(CASE WHEN a.receive_basis in (9,10) THEN c.quantity ELSE 0 END) AS grey_production_qnty
		from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22,58) and c.entry_form in (22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_id_cond group by a.knitting_source,c.po_breakdown_id";//and a.receive_basis<>9 sum(c.quantity) as grey_purchase_qnty

		$grey_production_data_arr = sql_select($sql_grey_production);

		foreach ($grey_production_data_arr as $row) {				
			$jobno = $po_wise_job_arr[$row[csf("po_breakdown_id")]];

			if($row[csf('knitting_source')]==1)
			{
				$greyProductionData[$jobno]['inhouse']['grey_production_qnty'] += $row[csf('grey_production_qnty')];
			}else{
				$greyProductionData[$jobno]['out_bound']['grey_production_qnty'] += $row[csf('grey_production_qnty')];
			}

			$grey_purchase_qnty[$jobno]['grey_purchase_qnty'] += $row[csf('grey_purchase_qnty')];

		}
		unset($grey_production_data_arr);
		
		$sqlgrey_issue_data=sql_select("select c.po_breakdown_id, 								
		sum(CASE WHEN c.entry_form ='45' and c.trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,
		sum(CASE WHEN c.entry_form ='16' THEN c.quantity ELSE 0 END) AS grey_issue,
		sum(CASE WHEN c.entry_form ='61' THEN c.quantity ELSE 0 END) AS grey_issue_rollwise,
		sum(CASE WHEN c.entry_form in(51,84) and c.trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return			
		from order_wise_pro_details c where c.status_active=1 and c.is_deleted=0 and c.entry_form in(16,45,51,61,84) $po_id_cond group by c.po_breakdown_id");
			
		foreach($sqlgrey_issue_data as $row)
		{
			$jobno = $po_wise_job_arr[$row[csf("po_breakdown_id")]];

			$greayIssueDataArr[$jobno]+= $row[csf('grey_issue')]+$row[csf('grey_issue_rollwise')];
			$greayIssueRtnDataArr[$jobno]+=$row[csf('grey_issue_return')];

			$grey_receive_return_qnty_arr[$jobno]=$row[csf('grey_receive_return')];			
		}
		unset($sqlgrey_issue_data);

		$sql_fin_recv="select po_breakdown_id,
		sum(CASE WHEN c.entry_form ='7' THEN c.quantity ELSE 0 END) AS finish_production,
		sum(CASE WHEN c.entry_form ='66' THEN c.quantity ELSE 0 END) AS finish_production_rollwise,
		sum(CASE WHEN c.entry_form ='37' THEN c.quantity ELSE 0 END) AS finish_receive,
		sum(CASE WHEN c.entry_form ='68' THEN c.quantity ELSE 0 END) AS finish_receive_rollwise,
		sum(CASE WHEN c.entry_form ='18' THEN c.quantity ELSE 0 END) AS finish_issue,
		sum(CASE WHEN c.entry_form ='71' THEN c.quantity ELSE 0 END) AS finish_issue_roll_wise,
		sum(CASE WHEN c.entry_form ='46' and c.trans_type=3 THEN c.quantity ELSE 0 END) AS recv_rtn_qnty,
		sum(CASE WHEN c.entry_form ='52' and c.trans_type=4 THEN c.quantity ELSE 0 END) AS iss_retn_qnty,
		sum(CASE WHEN c.entry_form ='15' and c.trans_type=5 THEN c.quantity ELSE 0 END) AS transfer_in_qnty,
		sum(CASE WHEN c.entry_form ='15' and c.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty
		from order_wise_pro_details c where c.status_active=1 and c.is_deleted=0 and c.entry_form in(7,15,18,37,46,52,66,68,71) $po_id_cond  group by c.po_breakdown_id";

		$dataArrayfin_recv=sql_select($sql_fin_recv);
		foreach($dataArrayfin_recv as $row)
		{
			//$po_wise_job_arr[$row[csf("po_breakdown_id")]]= job_no;
			$jobno = $po_wise_job_arr[$row[csf("po_breakdown_id")]];

			$trans_qnty_fin_arr[$jobno]['transfer_in'] +=$row[csf('transfer_in_qnty')];
			$trans_qnty_fin_arr[$jobno]['transfer_out'] +=$row[csf('transfer_out_qnty')];

			$finish_receive_qnty_arr[$jobno] +=$row[csf('finish_receive')]+$row[csf('finish_receive_rollwise')];

			$finish_recv_rtn_qnty_arr[$jobno] +=$row[csf('recv_rtn_qnty')];

			$finish_purchase_qnty_arr[$jobno] +=$row[csf('finish_purchase')];

			$finish_issue_qnty_arr[$jobno] +=$row[csf('finish_issue')]+$row[csf('finish_issue_roll_wise')];

			$finish_issue_rtn_qnty_arr[$jobno] +=$row[csf('iss_retn_qnty')];
		}
		unset($dataArrayfin_recv);

		ob_start();

	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>
	<fieldset style="width:100%">	
	    
	    <table style="  " width="2500" cellspacing="0" cellpadding="0">
	        <tbody><tr>
	           <td colspan="25" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[str_replace("'", "", $company_name)]; ?></strong></td>
	        </tr>
	        <tr>
	           <td colspan="25" style="font-size:16px" width="100%" align="center">
	           	<strong>
	           		<? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date, 'dd-mm-yyyy') . " To " . change_date_format($end_date, 'dd-mm-yyyy') . ""; ?>
	            </strong>
	       		</td>
	        </tr>
	    	</tbody>
		</table>
	  
	    <table class="rpt_table" border="1" rules="all" width="2500" cellpadding="0" cellspacing="0" id="table_header_1">
		  <thead>
		    <tr>
		      <th rowspan="2" width="100">Main Fabric Booking No</th>
		      <th rowspan="2" width="100">Job Number</th>
		      <th rowspan="2" width="100">Buyer Name</th>
		      <th rowspan="2" width="100">Style Ref.</th>
		      <th rowspan="2" width="100">Job Qnty</th>
		      <th rowspan="2" width="100">Shipment Date</th>
		      <th rowspan="2" width="100">Count</th>
		      <th rowspan="2" width="100">Yarn Composition</th>
		      <th rowspan="2" width="100">Yarn Type</th>
		      <th rowspan="2" width="100">Yarn Required</th>
		      <th rowspan="2" width="100">Yarn Work Order Qty</th>
		      <th rowspan="2" width="100">Yarn Received Qty</th>
		      <th colspan="2" width="100">Yarn issue for knitting</th>
		      <th rowspan="2" width="100">Issue Balance</th>
		      <th colspan="2" width="100">Grey Production Qty</th>

		      <th rowspan="2" width="100">Grey Purchase Qty</th>

		      <th rowspan="2" width="100">Knitting Balance Qty</th>
		      <th rowspan="2" width="100">Grey Fabric Issue to Dyeing</th>

		      <th rowspan="2" width="100">Knit Finished fabric require Qty</th>

		      <th rowspan="2" width="100">Finished fabric Receive Qty</th>
		      <th rowspan="2" width="100">Finished fabric Receive Balance</th>
		      <th rowspan="2" width="100">Knit Issue To Cutting</th>
		      <th rowspan="2" width="100">Finished Fabric Stock</th>
		    </tr>
		    <tr>
		      <th width="100">In House</th>
		      <th width="100">Out Bound</th>
		      <th width="100">In House</th>
		      <th width="100">Out Bound</th>
		    </tr>

		  </thead>
		</table>
		
		<div style="width:2520px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		<table width="2500" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" >
			<tbody>
			<? 	
			//$knitting_balance = 0;
			$i = 0;
			$total_jobWiseGreyIssueQty = 0;
			$total_fin_req = 0;
			$total_finishRcvQty = 0;
			$total_finRecvBalance = 0;
			$total_finIssueToCutting  = 0;
			$total_finStock=0;
			$total_req_quantity=0;
			$total_work_order_qty=0; 
			$total_yarn_rcv_qty=0;
			$total_yarn_inhouse_issue_qty = 0;
			$total_yarn_out_bound_issue_qty=0;
			$total_issue_balance=0;
			foreach($job_alldata_arr as $jobNo=>$row)
			{	
				$yarnDataString = $dataArrayYarn[$jobNo];
				$yarnDataString = chop($yarnDataString,",");
				$yarnDatArr = explode(",", $yarnDataString);
				$rowSpan=count($yarnDatArr);

				$grey_inhouse_production_qty = $greyProductionData[$jobNo]['inhouse']['grey_production_qnty'];
				$grey_out_bound_production_qty = $greyProductionData[$jobNo]['out_bound']['grey_production_qnty'];
				$total_grey_inhouse_production_qty += $grey_inhouse_production_qty;
				$total_grey_out_bound_production_qty += $grey_out_bound_production_qty;

				$grey_purchase_qnty = $grey_purchase_qnty[$jobNo]['grey_purchase_qnty'];
				$total_grey_purchase_qnty += $grey_purchase_qnty;
				$jobWiseGreyIssueQty = ($greayIssueDataArr[$jobNo]-$greayIssueRtnDataArr[$jobNo]);

				$knitting_balance = $job_wise_yarn_req_quantity[$jobNo] - ($greyProductionData[$jobNo]['inhouse']['grey_production_qnty']+$greyProductionData[$jobNo]['out_bound']['grey_production_qnty']+$grey_purchase_qnty[$jobNo]['grey_purchase_qnty']);
				
				//$fin_fab_req = $bookingData[$jobNo]['finish_fab_required'];
				$fab_knit_fin_req=array_sum($fabric_costing_arr['knit']['finish'][$jobNo]);
				$fab_woven_fin_req=array_sum($fabric_costing_arr['woven']['finish'][$jobNo]);
				$fin_fab_req=$fab_knit_fin_req+$fab_woven_fin_req;

				$finishRcvQty = $finish_receive_qnty_arr[$jobNo] - $finish_recv_rtn_qnty_arr[$jobNo];
				$finRecvBalance = ($fin_fab_req-$finishRcvQty);

				$finIssueToCutting  = ($finish_issue_qnty_arr[$jobNo]-$finish_issue_rtn_qnty_arr[$jobNo]);
				$finStock = ($finishRcvQty-$finIssueToCutting);

				$total_jobWiseGreyIssueQty += ($greayIssueDataArr[$jobNo]-$greayIssueRtnDataArr[$jobNo]);
				$total_fin_req += $fin_fab_req;
				$total_finishRcvQty += $finishRcvQty;
				$total_finRecvBalance +=$finRecvBalance;
				$total_finIssueToCutting +=$finIssueToCutting;
				$total_finStock += $finStock;
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo chop($bookingData[$jobNo]['booking_no'],",");?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo $jobNo;?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo $buyer_short_name_arr[$row['buyer_name']];?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo $row['style_ref_no'];?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo $row['job_qnty'];?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo change_date_format($row['pub_shipment_date']);?></td>
				<? 
				$start=1;
				
				foreach($yarnDatArr as $yarnDataStr)
				{
					$yarnData = explode("**", $yarnDataStr);
					$countId = $yarnData[0];
					$yarn_type_id = $yarnData[1];
					$copm_one_id = $yarnData[2];
					$copm_two_id = $yarnData[3];
					$percent_one = $yarnData[4];
					$percent_two = $yarnData[5];
					//$yarn_required_qty =$yarnData[6];
					$colorId =$yarnData[7];
					$rate =$yarnData[8];
					

					if($countId!="")
					{
						$compos = $composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
					}else{
						$compos = "";
					}
					//echo $jobNo."--".$countId."--".$copm_one_id."--".$percent_one."--".$yarn_type_id."--".$colorId."|";	
					$yarnKey = $countId."__".$yarn_type_id."__".$copm_one_id."__".$copm_two_id."__".$percent_one."__".$percent_two;
					
					$yarn_prod_id = $yearnRcvData[$jobNo][$yarnKey]['prod_id'];

					$req_quantity = $yarnRequiredArray[$jobNo][$countId][$yarn_type_id][$copm_one_id][$copm_two_id][$percent_one][$percent_two][$colorId][$rate]["req_quantity"];
					//$tempJobYarnRequired[$jobNo] += $req_quantity;
					//echo $jobNo."==".$yarnKey."<br>";
					$work_order_qty = $yearnWorkOrderData[$jobNo][$yarnKey]['work_order_qty'];
					
					$yarn_rcv_qty = $yearnRcvData[$jobNo][$yarnKey]['rcv_quantity'];
					
					$yarn_inhouse_issue_qty = ($yearnIssueData[$jobNo][$yarnKey]['inhouse']['issue_quantity']-$yearnIssueRtnData[$jobNo][$yarnKey]['inhouse']['issue_rtn_qty']);
					$yarn_out_bound_issue_qty = ($yearnIssueData[$jobNo][$yarnKey]['out_bound']['issue_quantity']-$yearnIssueRtnData[$jobNo][$yarnKey]['out_bound']['issue_rtn_qty']);

					$yarn_inhouse_not_req_issue_qty = ($yearnIssueData[$jobNo]['not_req']['inhouse']['issue_quantity']-$yearnIssueRtnData[$jobNo]['not_req']['inhouse']['issue_rtn_qty']);
					$yarn_out_bound_not_req_issue_qty = ($yearnIssueData[$jobNo]['not_req']['out_bound']['issue_quantity']-$yearnIssueRtnData[$jobNo]['not_req']['out_bound']['issue_rtn_qty']);

					$total_none_required_yarn = ($yarn_inhouse_not_req_issue_qty+$yarn_out_bound_not_req_issue_qty);

					$none_required_issue_balance = (0-$total_none_required_yarn);

					$issue_balance = ($req_quantity-($yarn_inhouse_issue_qty+$yarn_out_bound_issue_qty));

					$total_req_quantity += $req_quantity;
					$total_work_order_qty += $work_order_qty;
					$total_yarn_rcv_qty += $yarn_rcv_qty;
					$total_issue_balance += $issue_balance;
	
					$jobwisepoId = implode(",",array_unique(explode(",",chop($job_alldata_arr[$jobNo]['po_id'],","))));
					$jobwise_grey_prodid = implode(",",array_unique(explode(",",chop($jobWisegreyprodid[$jobNo],",")))); 

					if($start!=1){echo "<tr>";}
					?>
					  <td width="100" class="word_wrap_break">
					  	<? echo $yarn_count_details[$countId]; 
				  		if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
				  		{
				  			echo "<br> None Required Yarn";
				  		}
					  	?>
					  </td>
					  <td width="100" class="word_wrap_break"><? echo $compos;?></td>
					  <td width="100" class="word_wrap_break"><? echo $yarn_type[$yarn_type_id]; ?></td>
					  <td width="100" class="word_wrap_break"><? echo number_format($req_quantity,2,'.','');?>
					  	<?
					  	if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
				  		{
				  			echo "<hr>";
				  			echo "0";
				  		}
				  		?>
					  </td>
					  <td width="100" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo."__".$yarnKey; ?>','<? echo $jobwisepoId;?>','work_order_popup','1060px');"><? echo number_format($work_order_qty,2,'.','');?></a>

					  	<?
					  	if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
				  		{
				  			echo "<hr>";
				  			echo "0";
				  		}
				  		?>
					   </td>
					   <td width="100" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo."__".$yarn_prod_id."__".$yarnKey; ?>','<? echo $jobwisepoId;?>','yarn_rcv_qty_popup','1360px');">
					  	<? echo number_format($yarn_rcv_qty,2,'.','');?></a>

					  	<?
					  	if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
				  		{
				  			echo "<hr>";
				  			echo "0";
				  		}
				  		?>
					  </td>
					  
					  <td width="100" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo."__".$yarn_prod_id."__".$yarnKey; ?>','<? echo $jobwisepoId;?>','yarn_inhouse_issue_knitting_popup','1200px');"><? echo number_format($yarn_inhouse_issue_qty,2,'.','');?></a>

					  	<?
					  		if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
					  		{
					  			$total_yarn_inhouse_issue_qty += ($yarn_inhouse_issue_qty+$yarn_inhouse_not_req_issue_qty);
					  			?>
					  			<hr>
					  			<a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."**".$jobNo."**".$jobWiseYarnKeys[$jobNo]; ?>','<? echo $jobwisepoId;?>','inhouse_yarn_issue_not_required_popup','1000px');"><? echo number_format($yarn_inhouse_not_req_issue_qty,2,'.','');?>
					  			</a>
					  		<?
					  		}else{
					  			$total_yarn_inhouse_issue_qty += $yarn_inhouse_issue_qty;
					  		}					  		
					  	?>
					  </td>

					  <td width="100" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo."__".$yarn_prod_id."__".$yarnKey; ?>','<? echo $jobwisepoId;?>','yarn_out_bound_issue_knitting_popup','1200px');">
					  	<? echo number_format($yarn_out_bound_issue_qty,2,'.','');?></a>

					  	<?
					  		if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
					  		{
					  			$total_yarn_out_bound_issue_qty += ($yarn_out_bound_issue_qty+$yarn_out_bound_not_req_issue_qty);
					  			?>
					  			<hr>
					  			<a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."**".$jobNo."**".$jobWiseYarnKeys[$jobNo]; ?>','<? echo $jobwisepoId;?>','outbound_yarn_issue_not_required_popup','1000px');"><? echo number_format($yarn_out_bound_not_req_issue_qty,2,'.','');?>
					  			</a>
					  			<?
					  		}else{
					  			$total_yarn_out_bound_issue_qty += $yarn_out_bound_issue_qty;
					  		}					  		
					  	?>

					  </td>


					  <td width="100" class="word_wrap_break">
					  	<? 
					  	echo number_format($issue_balance,2,'.','');

					  	if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
				  		{
				  			echo "<hr>";
				  			echo $none_required_issue_balance;
				  			$total_issue_balance = ($total_issue_balance+$none_required_issue_balance);
				  		}else{
				  			$total_issue_balance = $total_issue_balance;
				  		}
					  	?>
					  </td>
					 

					<? if($start==1){ ?>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo;?>','<? echo $jobwisepoId;?>','grey_inhouse_production_popup','1000px');"><? echo number_format($grey_inhouse_production_qty,2,'.','');?></a></td>
						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo; ?>','<? echo $jobwisepoId;?>','grey_outbound_production_popup','1000px');"><? echo number_format($grey_out_bound_production_qty,2,'.','');?></a></td>

						   <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break">
						   	<a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo;?>','<? echo $jobwisepoId;?>','grey_fabric_purchase_popup','1000px');"><? echo number_format($grey_purchase_qnty,2,'.','');?></a>
						   </td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? 
						  	$knitting_balance = $job_wise_yarn_req_quantity[$jobNo]-($grey_inhouse_production_qty+$grey_out_bound_production_qty+$grey_purchase_qnty);
						  	$total_knitting_balance += $knitting_balance;
						  	echo number_format($knitting_balance,2,'.','');?>
						  </td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo; ?>','<? echo $jobwisepoId; ?>','grey_issue_popup','750px');"><? echo number_format($jobWiseGreyIssueQty,2,'.','');?></a></td> 

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo number_format($fin_fab_req,2,'.','');?></td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break" >
						  	
						  	<a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo; ?>','<? echo $jobwisepoId; ?>','finish_fabrcv_popup','1230px');"><? echo number_format($finishRcvQty,2,'.','');?></a>
						  		
						  </td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo number_format($finRecvBalance,2,'.','');?></td>
						  
						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break">
						  	
						  	<a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo; ?>','<? echo $jobwisepoId; ?>','issue_to_cut_popup','950px');"><? echo number_format($finIssueToCutting,2,'.','');?></a>
						  	
						  </td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo number_format($finStock,2,'.','');?></td>
						</tr>
					<?
					} else { 
						echo "</tr>";	
					}
					$start++;
				}
				$i++;

			}
			?>
			</tbody>
		</table>
		</div>

		<table  class="rpt_table" rules="all" style="  " width="2500" cellspacing="0" cellpadding="0" border="1">
			<tfoot>
				<tr>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">Total &nbsp;</p></th>
				  <th width="100" style="text-align: left;" id="value_html_req_quantity"><p class="word_wrap_break"><? echo number_format($total_req_quantity,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_work_order_qty"><p class="word_wrap_break"><? echo number_format($total_work_order_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_yarn_rcv_qty" ><p class="word_wrap_break"><? echo number_format($total_yarn_rcv_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_inhouse_issue_qty" ><p class="word_wrap_break"><? echo number_format($total_yarn_inhouse_issue_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_out_bound_issue_qty" ><p class="word_wrap_break"><? echo number_format($total_yarn_out_bound_issue_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_issue_balance" ><p class="word_wrap_break"><? echo number_format($total_issue_balance,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_inhouse_production_qty" ><p class="word_wrap_break"><? echo number_format($total_grey_inhouse_production_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_out_bound_production_qty" ><p class="word_wrap_break"><? echo number_format($total_grey_out_bound_production_qty,2,'.','');?></p></th>
				  
				  <th width="100" style="text-align: left;" id="value_html_knitting_balance" ><? echo number_format($total_grey_purchase_qnty,2,'.','');?></th>

				  <th width="100" style="text-align: left;" id="value_html_knitting_balance" ><p class="word_wrap_break"><? echo number_format($total_knitting_balance,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_jobwise_grey_issue_qty" ><p class="word_wrap_break"><? echo number_format($total_jobWiseGreyIssueQty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;"><p class="word_wrap_break"><? echo number_format($total_fin_req,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_finish_rcv_qty" ><p class="word_wrap_break"><? echo number_format($total_finishRcvQty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_finrecv_balance" ><p class="word_wrap_break"><? echo number_format($total_finRecvBalance,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_finissue_to_cutting" ><p class="word_wrap_break"><? echo number_format($total_finIssueToCutting,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_fin_stock"><p class="word_wrap_break"><? echo number_format($total_finStock,2,'.','');?></p></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>

	<br>

	<div style=" width: 820px;">

		<div style=" width: 400px; float: left; padding-right: 10px; " id="summary">
			<table width="400" class="rpt_table" border="1" rules="all">
				<thead>
					<tr>
						<th colspan="3">Summary</th>
					</tr>
					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<th width="300">Particulars</th>
						<th width="170">Total Qnty</th>
						
					</tr>
				</thead>
				<tbody>  
					
					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Yarn Required</td>
						<td align="right"><? echo number_format($total_req_quantity,2,'.','');?></td>
					</tr>

					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Yarn Received</td>
						<td align="right"><? echo number_format($total_yarn_rcv_qty,2,'.','');?></td>
						
					</tr>

					<tr style="background-color:#CFF; font-weight:bold">
						<td>Total Yarn Received Balance</td>
						<td align="right"><? echo number_format(($total_req_quantity-$total_yarn_rcv_qty),2,'.','');?></td>
					</tr>

					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Yarn Issued To Knitting</td>
						<td align="right"> <? echo number_format(($total_yarn_inhouse_issue_qty+$total_yarn_out_bound_issue_qty),2,'.',''); ?></td>
					</tr>
					
					<tr style="background-color:#CFF; font-weight:bold">
						<td>Total Yarn Issue Balance</td>
						<td align="right"><? echo number_format($total_issue_balance,2,'.','');?></td>
					</tr>


					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Grey Fabric Required</td>
						<td align="right"><? echo number_format($total_req_quantity,2,'.','');?></td>
					</tr>

					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Grey Fabric Available</td>
						<td align="right"><? echo number_format(($total_grey_inhouse_production_qty+$total_grey_out_bound_production_qty+$total_grey_purchase_qnty),2,'.','');?></td>
					</tr>

					<tr bgcolor="<? echo "#FFFFFF"; ?>" style="background-color:#CFF; font-weight:bold">
						<td>Total Grey Fabric Balance</td>
						<td align="right">
							<? 
							$grey_abailable = ($total_grey_inhouse_production_qty+$total_grey_out_bound_production_qty+$total_grey_purchase_qnty);
							echo number_format(($total_req_quantity-$grey_abailable),2,'.','');
							?>
						</td>
					</tr>

					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Grey Fabric Issued To Dye</td>
						<td align="right"><? echo number_format($total_jobWiseGreyIssueQty,2,'.','');?></td>
					</tr>

					<tr bgcolor="<? echo "#FFFFFF"; ?>" style="background-color:#CFF; font-weight:bold">
						<td>Total Grey Fabric Issue Balance</td>
						<td align="right"><? echo number_format(($total_req_quantity-$total_jobWiseGreyIssueQty),2,'.','');?></td>
					</tr>					

					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Finish Fabric Required</td>
						<td align="right"><? echo number_format($total_fin_req,2,'.','');?></td>
					</tr>

					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Finish Fabric Available</td>
						<td align="right"><? echo number_format($total_finishRcvQty,2,'.','');?></td>
					</tr>

					<tr style="background-color:#CFF; font-weight:bold">
						<td>Total Finish Fabric Received Balance</td>
						<td align="right"><? echo number_format(($total_fin_req-$total_finishRcvQty),2,'.','');?></td>
					</tr>

					<!-- <tr bgcolor="<? //echo "#FFFFFF"; ?>">
						<td>Total fabric Purchase Required</td>
						<td align="right">&nbsp;</td>
					</tr>

					<tr bgcolor="<? //echo "#FFFFFF"; ?>">
						<td>Total fabric Purchase Rcv</td>
						<td align="right">&nbsp;</td>
					</tr> 
					
					<tr bgcolor="<? //echo "#FFFFFF"; ?>" style="background-color:#CFF; font-weight:bold">
						<td>Total fabric Purchase Balance</td>
						<td align="right">&nbsp;</td>
					</tr>

					-->

					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Finish Fabric Issued To Cut</td>
						<td align="right"><? echo number_format($total_finIssueToCutting,2,'.','');?></td>
					</tr>
					
					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<td>Total Finish Fabric Stock In Hand</td>
						<td align="right"><? echo number_format($total_finStock,2,'.','');?></td>
					</tr>	

				</tbody>
		    </table>
	    </div>

		<div style=" width: 400px; float: left;" id="summary_yarn_count">			
			<table width="400" class="rpt_table" border="1" rules="all">
				<thead>
					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<th width="150">Yarn Count</th>
						<th width="150">Yarn Type</th>
						<th width="170">Req.Qty</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ($countSummaryArr as $countId=>$yarn_typeArr) {
						foreach ($yarn_typeArr as $yarnTypeId=>$data) { 
							$yarn_require_qty = $countSummaryArr[$countId][$yarnTypeId];
							$total_yarn_require_qty += $yarn_require_qty;
						 	?>
								<tr bgcolor="<? echo "#FFFFFF"; ?>">
									<td><? echo $yarn_count_details[$countId];?></td>
									<td><? echo $yarn_type[$yarnTypeId];?></td>
									<td align="right"><? echo number_format($yarn_require_qty,2,'.','');?></td>
								</tr>
							<?
						}
					}
					?>
					<tr style="background-color:#CFF; font-weight:bold">
						<td colspan="2" align="center">TTL</td>
						<td align="right"><? echo number_format($total_yarn_require_qty,2,'.','');?></td>
					</tr>
				</tbody>
		    </table>
	    </div>	
	</div>
	
	<?
 	$html = ob_get_contents();
    ob_clean();

    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

if($action=="report_generate2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_name = str_replace("'","",$cbo_company_name);


	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_name=$cbo_buyer_name";
	}

	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));

	$cbo_year=str_replace("'","",$cbo_year_selection);

	if(trim($cbo_year)!=0) 
	{
		if($db_type==0) 
		{			
			$year_cond=" and YEAR(d.insert_date)=$cbo_year";
		}
		else if($db_type==2) 
		{
			$year_cond=" and to_char(d.insert_date,'YYYY')=$cbo_year";
		}else {
			$year_cond="";
		}
	}
	else {
		$year_cond="";
	}

	$txt_job_no=str_replace("'","",$txt_job_no);
	$job_no_cond="";
	if(trim($txt_job_no)!="")
	{
		$job_no=trim($txt_job_no); 
		$job_no_cond=" and d.job_no_prefix_num=$job_no";
	}

	$txt_booking_no=str_replace("'","",$txt_booking_no);
	$booking_no_cond="";
	if(trim($txt_booking_no)!="")
	{
		$booking_no=trim($txt_booking_no); 

		$booking_no_cond=" and a.booking_no_prefix_num =$booking_no";

	}

	if ($start_date=="" && $end_date=="") $country_date_cond=""; else $country_date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";

	if(trim($txt_style)!="")
	{
		$style = trim(str_replace("'","",$txt_style)); 
		$style_cond=" and d.style_ref_no like '%$style%'";
	}


	//$tempJobCond = "and d.job_no = 'BPKW-18-00748' ";

	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
		  $condition->buyer_name("=$cbo_buyer_name");
	}
	if(str_replace("'","",$txt_job_no) !=''){
		  $condition->job_no_prefix_num("=$txt_job_no");
	}
	if(str_replace("'","",$txt_style) !=''){
		  $condition->style_ref_no("=$txt_style");
	}
	if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
		  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
	}
	$condition->init();
	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish();
	//echo $fabric->getQuery(); die;
	//print_r($fabric_costing_arr);

	if($booking_no_cond)
	{
		$bookingInfosql ="SELECT d.booking_no,e.job_no from wo_booking_mst a, wo_booking_dtls d, wo_pre_cos_fab_co_avg_con_dtls e where a.booking_no = d.booking_no and d.pre_cost_fabric_cost_dtls_id=e.pre_cost_fabric_cost_dtls_id and d.po_break_down_id=e.po_break_down_id and d.color_size_table_id=e.color_size_table_id  and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 $booking_no_cond group by d.booking_no, e.job_no";

		//echo $bookingInfosql;
		$resultbookingInfo=sql_select($bookingInfosql);
		$jobData =array(); 
		foreach($resultbookingInfo as $row)
		{	
			if($duplicate_chk[$row[csf('job_no')]]=='')
			{
				$duplicate_chk[$row[csf('job_no')]] = $row[csf('job_no')];
				array_push($jobData,$row[csf('job_no')]);
			}
		}
		
		unset($resultbookingInfo);	
	}

	$job_data_cond="";
	if($jobData)
	{
		$job_data_cond=" ".where_con_using_array($jobData,1,'d.job_no')." ";
	}


	$sql="select d.company_name, d.buyer_name, d.job_no_prefix_num, d.job_no, d.style_ref_no, b.id as po_id,b.po_quantity as po_qnty, b.pub_shipment_date from wo_po_details_master d, wo_po_break_down b where d.job_no=b.job_no_mst and d.company_name='$company_name' $country_date_cond $year_cond $job_no_cond $buyer_id_cond $style_cond $tempJobCond $job_data_cond and d.is_deleted=0 and d.status_active=1 and b.is_deleted=0 and b.status_active=1 group by d.company_name, d.job_no_prefix_num, d.job_no, d.style_ref_no,d.buyer_name,b.id, b.po_number, b.po_quantity, b.pub_shipment_date order by d.job_no,b.pub_shipment_date, b.id"; 

	//echo $sql;
	    $nameArray=sql_select($sql);

		if(count($nameArray)>0)
		{
			foreach($nameArray as $row)
			{
				$tot_rows++;				

				$job_alldata_arr[$row[csf("job_no")]]['company_name'] = $row[csf("company_name")];
				$job_alldata_arr[$row[csf("job_no")]]['buyer_name']   = $row[csf("buyer_name")];
				$job_alldata_arr[$row[csf("job_no")]]['style_ref_no'] = $row[csf("style_ref_no")];
				$job_alldata_arr[$row[csf("job_no")]]['po_id'].= $row[csf("po_id")].",";
				$job_alldata_arr[$row[csf("job_no")]]['pub_shipment_date'] = $row[csf("pub_shipment_date")];
				$job_alldata_arr[$row[csf("job_no")]]['job_qnty'] += $row[csf("po_qnty")];

				$po_wise_job_arr[$row[csf("po_id")]] = $row[csf("job_no")];
				
				$jobNo .= "'".$row[csf("job_no")]."',";
				$poIds.=$row[csf("po_id")].",";
			}							
		}
		else
		{
			echo "3**".'Data Not Found'; die;
		}
		unset($nameArray);	

		if($jobNo!="")
		{
			$jobNo = implode(",",array_unique(explode(",",chop($jobNo,',')))); 
			$job_no_cond=""; 
			$job_no_mst_cond=""; 

			if($db_type==2 && $tot_rows>1000)
			{
				$job_no_cond=" and (";
				$job_no_mst_cond=" and (";
				
				$jobNoArr=array_chunk(explode(",",$jobNo),999);
				foreach($jobNoArr as $ids)
				{
					$ids=implode(",",$ids);
					$job_no_cond.=" d.job_no in($ids) or ";	
					$job_no_mst_cond.=" d.job_no_mst in($ids) or ";	
				}
				$job_no_cond=chop($job_no_cond,'or ');
				$job_no_mst_cond=chop($job_no_mst_cond,'or ');
				$job_no_cond.=")";
				$job_no_mst_cond.=")";
			}
			else
			{
				$job_no_cond=" and d.job_no in ($jobNo)";
				$job_no_mst_cond=" and d.job_no_mst in ($jobNo)";
			}
		}
		// po id condition
		if($poIds!="")
		{
			$poIds = implode(",",array_unique(explode(",",chop($poIds,',')))); 
			$po_id_cond=""; 
			$po_id_bokking_cond=""; 

			if($db_type==2 && $tot_rows>1000)
			{
				$po_id_cond=" and (";
				
				$poIdArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdArr as $ids)
				{
					$ids=implode(",",$ids);
					$po_id_cond.=" c.po_breakdown_id in($ids) or ";	
					$po_id_bokking_cond.=" e.po_break_down_id in($ids) or ";	
					
				}
				$po_id_cond=chop($po_id_cond,'or ');
				$po_id_cond.=")";

				$po_id_bokking_cond=chop($po_id_bokking_cond,'or ');
				$po_id_bokking_cond.=")";
			}
			else
			{
				$po_id_cond=" and c.po_breakdown_id in ($poIds)";
				$po_id_bokking_cond=" and e.po_break_down_id in ($poIds)";
			}
		}

		if($job_no_cond!="" &&  $po_id_bokking_cond!="")
		{
			$booking_sql ="select sum(d.fin_fab_qnty) as fin_fab_qnty,d.booking_no,e.job_no from wo_booking_dtls d, wo_pre_cos_fab_co_avg_con_dtls e where d.pre_cost_fabric_cost_dtls_id=e.pre_cost_fabric_cost_dtls_id and d.po_break_down_id=e.po_break_down_id and d.color_size_table_id=e.color_size_table_id $po_id_bokking_cond $job_no_cond and d.booking_type=1 and d.status_active=1 and d.is_deleted=0 group by d.booking_no, e.job_no";

			$resultBooking=sql_select($booking_sql);
			$bookingData =array(); 
			foreach($resultBooking as $row)
			{	
				$bookingData[$row[csf('job_no')]]['booking_no'] .= $row[csf('booking_no')].",";			
				//$bookingData[$row[csf('job_no')]]['finish_fab_required'] = $row[csf('fin_fab_qnty')];
			}
			
			unset($resultBooking);	
		}
	


		if($job_no_cond!="")
		{		
			$jobNo = implode(",",array_unique(explode(",",chop($jobNo,',')))); 
			$gmtsitem_ratio_array=array();		
			$grmts_sql = sql_select("select d.job_no,d.gmts_item_id,d.set_item_ratio from wo_po_details_mas_set_details d where d.job_no in ($jobNo)");
			foreach($grmts_sql as $row)
			{
				$gmtsitem_ratio_array[$row[csf('job_no')]][$row[csf('gmts_item_id')]]=$row[csf('set_item_ratio')];
			}

			$precost_mst = sql_select("select d.costing_per,d.job_no from wo_pre_cost_mst d where d.status_active = 1 and d.is_deleted = 0 $job_no_cond"); 
			
			$order_price_per_dzn_arr = array();

			foreach($precost_mst as $row)
			{				
				if($row[csf("costing_per")]==1){
					$order_price_per_dzn=12;
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
					//$costing_for=" DZN";
				}
				else if($row[csf("costing_per")]==2){
					$order_price_per_dzn=1;
					//$costing_for=" PCS";
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
				}
				else if($row[csf("costing_per")]==3){
					$order_price_per_dzn=24;
					//$costing_for=" 2 DZN";
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
				}
				else if($row[csf("costing_per")]==4){
					$order_price_per_dzn=36;
					//$costing_for=" 3 DZN";
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
				}
				else if($row[csf("costing_per")]==5){
					$order_price_per_dzn=48;
					//$costing_for=" 4 DZN";
					$order_price_per_dzn_arr[$row[csf('job_no')]]['costing_per'] = $order_price_per_dzn;
				}
			}

			$sql_y=sql_select("select c.item_number_id,c.order_quantity ,c.plan_cut_qnty,d.id,f.job_no,f.color,f.count_id,f.copm_one_id,f.percent_one,f.copm_two_id,f.percent_two,f.type_id,f.cons_ratio,f.cons_qnty,f.avg_cons_qnty,f.rate,f.amount,e.requirment   from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and a.job_no=d.job_no and a.job_no=e.job_no and a.job_no=f.job_no and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id $job_no_cond $po_id_bokking_cond and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1");

			//and f.job_no='RpC-19-00001'
			$dataArrayYarn=array(); 
			foreach($sql_y as $sql_y_r)
			{	
				$set_item_ratio=$gmtsitem_ratio_array[$sql_y_r[csf('job_no')]][$sql_y_r[csf('item_number_id')]];
				$order_price_per_dzn = $order_price_per_dzn_arr[$sql_y_r[csf('job_no')]]['costing_per'];

				$cons_qnty = def_number_format((($sql_y_r[csf("requirment")]*$sql_y_r[csf("cons_ratio")]/100)*($sql_y_r[csf("plan_cut_qnty")]/($order_price_per_dzn*$set_item_ratio))),5,"");

				$yarnRequiredArray[$sql_y_r[csf('job_no')]][$sql_y_r[csf("count_id")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("copm_two_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("percent_two")]][$sql_y_r[csf("color")]][$sql_y_r[csf("rate")]]["req_quantity"]+=$cons_qnty;

				$countSummaryArr[$sql_y_r[csf("count_id")]][$sql_y_r[csf("type_id")]][$sql_y_r[csf("copm_one_id")]][$sql_y_r[csf("copm_two_id")]][$sql_y_r[csf("percent_one")]][$sql_y_r[csf("percent_two")]]["req_quantity"]+=$cons_qnty;

				//$countSummaryArr[$sql_y_r[csf("count_id")]][$sql_y_r[csf("type_id")]]+=$cons_qnty;

			}
			unset($resultYarn);

			// echo "<pre>";
			// print_r($countSummaryArr);

			//die();			
			$yarn_sql = sql_select("select min(d.id) as id, d.job_no, d.count_id, d.copm_one_id, d.percent_one, d.copm_two_id, d.percent_two, d.color,d.type_id, min(d.cons_ratio) as cons_ratio, sum(d.cons_qnty) as cons_qnty, d.rate, sum(d.amount) as amount from wo_pre_cost_fab_yarn_cost_dtls d where d.status_active=1 and d.is_deleted=0 $job_no_cond group by d.job_no,d.count_id, d.copm_one_id, d.percent_one, d.copm_two_id, d.percent_two, d.color,d.type_id, d.rate");

			$s = 1;
			foreach($yarn_sql as $yarnRow)
			{
				$dataArrayYarn[$yarnRow[csf('job_no')]].=$yarnRow[csf('count_id')]."**".$yarnRow[csf('type_id')]."**".$yarnRow[csf('copm_one_id')]."**".$yarnRow[csf('copm_two_id')]."**".$yarnRow[csf('percent_one')]."**".$yarnRow[csf('percent_two')]."**".$cons_qnty."**".$yarnRow[csf('color')]."**".$yarnRow[csf('rate')].",";

				$yarnKey = $yarnRow[csf('count_id')]."__".$yarnRow[csf('type_id')]."__".$yarnRow[csf('copm_one_id')]."__".$yarnRow[csf('copm_two_id')]."__".$yarnRow[csf('percent_one')]."__".$yarnRow[csf('percent_two')];

				$yarn_desc_array[$s]=$yarnKey;

				$jobWiseYarnKeys[$yarnRow[csf('job_no')]].= $yarnKey.",";
				
					$job_wise_yarn_req_quantity[$yarnRow[csf('job_no')]] += $yarnRequiredArray[$yarnRow[csf('job_no')]][$yarnRow[csf('count_id')]][$yarnRow[csf('type_id')]][$yarnRow[csf('copm_one_id')]][$yarnRow[csf('copm_two_id')]][$yarnRow[csf('percent_one')]][$yarnRow[csf('percent_two')]][$yarnRow[csf('color')]][$yarnRow[csf('rate')]]["req_quantity"];

				$s++;
			}

			$sql_work_order = "select d.job_no, d.buyer_id,d.yarn_count, d.yarn_comp_type1st,nvl(d.yarn_comp_type2nd,0) as yarn_comp_type2nd, d.yarn_comp_percent1st,nvl(d.yarn_comp_percent2nd,0) as yarn_comp_percent2nd,d.yarn_type, d.color_name, d.req_quantity, d.supplier_order_quantity,d.rate from wo_non_order_info_mst a, wo_non_order_info_dtls d where a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.id=d.mst_id and d.item_category_id=1 $job_no_cond";

			$resultYarnwork=sql_select($sql_work_order);
			$yearnWorkOrderData = array();
			foreach($resultYarnwork as $yarnworkRow)
			{
				$yarnKey = $yarnworkRow[csf('yarn_count')]."__".$yarnworkRow[csf('yarn_type')]."__".$yarnworkRow[csf('yarn_comp_type1st')]."__".$yarnworkRow[csf('yarn_comp_type2nd')]."__".$yarnworkRow[csf('yarn_comp_percent1st')]."__".$yarnworkRow[csf('yarn_comp_percent2nd')];

				$yearnWorkOrderData[$yarnworkRow[csf('job_no')]][$yarnKey]['work_order_qty'] += $yarnworkRow[csf('supplier_order_quantity')];

				$jobYarnKey[$yarnKey]=$yarnworkRow[csf('job_no')];

			}
			unset($resultYarnwork);
		}

		/*echo "<pre>";
		print_r($yearnWorkOrderData);*/

		$sql_yarn_rcv = "select a.id,b.prod_id,b.cons_quantity,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd from inv_receive_master a, inv_transaction b, wo_non_order_info_mst c , wo_non_order_info_dtls d,product_details_master e where a.id=b.mst_id and b.pi_wo_batch_no=c.id and c.id=d.mst_id $job_no_cond and b.item_category=1 and b.transaction_type=1 and a.entry_form=1 and a.receive_basis in(1,2) and b.prod_id=e.id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.item_category_id=1 and e.status_active=1 and e.is_deleted=0 group by a.id,b.prod_id,b.cons_quantity,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd

		union all 

		select a.id,b.prod_id,b.cons_quantity,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd 
		from inv_receive_master a, inv_transaction b, wo_non_order_info_dtls d,product_details_master e, com_pi_item_details f
		where a.id=b.mst_id and a.booking_id=f.pi_id $job_no_cond and b.item_category=1 
		and b.transaction_type=1 and a.entry_form=1 and a.receive_basis in(1) and b.prod_id=e.id and a.company_id=$company_name
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 
		and d.is_deleted=0 and e.item_category_id=1 and e.status_active=1 and e.is_deleted=0 and d.id=f.work_order_dtls_id
		group by a.id,b.prod_id,b.cons_quantity,d.job_no,d.buyer_id,
		d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd";

		$yarnRcvArray=sql_select($sql_yarn_rcv);

		if(count($yarnRcvArray)>0)
		{
			foreach($yarnRcvArray as $row)
			{			
				$yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

				$yearnRcvData[$row[csf('job_no')]][$yarnKey]['rcv_quantity'] += $row[csf('cons_quantity')];	
				$yearnRcvData[$row[csf('job_no')]][$yarnKey]['prod_id'] .= $row[csf('prod_id')].",";	

				$prodId .= $row[csf("prod_id")].",";	
			}
		}
		unset($yarnRcvArray);

		/*echo "<pre>";
		print_r($yearnRcvData);

		die();*/

		if($prodId!="")
		{
			$prodId= implode(",",array_unique(explode(",",chop($prodId,',')))); 
			$prod_id_cond=""; 
			if($db_type==2 && $tot_rows>1000)
			{
				$prod_id_cond=" and (";
				
				$prodIdArr=array_chunk(explode(",",$prodId),999);
				foreach($prodIdArr as $ids)
				{
					$ids=implode(",",$ids);
					$prod_id_cond.=" b.prod_id in($ids) or ";	
				}
				$prod_id_cond=chop($prod_id_cond,'or ');
				$prod_id_cond.=")";
			}
			else
			{
				$prod_id_cond=" and b.prod_id in ($prodId)";
			}
		}
			
		$sql_yarn_iss ="SELECT a.id,a.knit_dye_source,c.prod_id,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type, sum(CASE WHEN c.entry_form ='3' THEN c.quantity ELSE 0 END) AS issue_qnty,a.issue_basis from inv_issue_master a,inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and c.issue_purpose!=2 $po_id_cond group by a.id,a.knit_dye_source,c.prod_id,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,a.issue_basis";
		//echo $sql_yarn_iss;
		$yarnIssueArray=sql_select($sql_yarn_iss);
		
		if(count($yarnIssueArray)>0)
		{
			$s = 1;
			foreach($yarnIssueArray as $row)
			{	
					$yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

					$jobno = $po_wise_job_arr[$row[csf('po_breakdown_id')]];

					if(in_array($yarnKey,$yarn_desc_array))
					{
						if($row[csf('knit_dye_source')]==1)
						{
							$yearnIssueData[$jobno][$yarnKey]['inhouse']['issue_quantity'] += $row[csf('issue_qnty')];					
						}else{
							$yearnIssueData[$jobno][$yarnKey]['out_bound']['issue_quantity'] += $row[csf('issue_qnty')];
						}


					}else{
						if($row[csf('knit_dye_source')]==1)
						{
							$yearnIssueData[$jobno]['not_req']['inhouse']['issue_quantity'] += $row[csf('issue_qnty')];	
							
							$yearnIssueInfoDtlsInhouseData[$row[csf('id')]]['not_req']['inhouse']['issue_qnty'] += $row[csf('issue_qnty')];	
							$yearnIssueInfoDtlsInhouseData[$row[csf('id')]]['not_req']['inhouse']['prod_id'] = $row[csf('prod_id')];	

						}else{
							$yearnIssueData[$jobno]['not_req']['out_bound']['issue_quantity'] += $row[csf('issue_qnty')];
							$yearnIssueInfoDtlsOutboundData[$row[csf('id')]]['not_req']['out_bound']['issue_qnty'] += $row[csf('issue_qnty')];	
							$yearnIssueInfoDtlsOutboundData[$row[csf('id')]]['not_req']['out_bound']['prod_id'] = $row[csf('prod_id')];
						}
						
						$yearnIssueInforData[$row[csf('po_breakdown_id')]]['not_req']['issue_id'] .= $row[csf('id')].',';	
						$yearnIssueInfoDtlsData[$row[csf('id')]]['not_req']['yarnKey'] = $yarnKey;	
						
					}

					$s++;		
			}
		}
		unset($yarnIssueArray);
		//echo "<pre>";print_r($yearnIssueInfoDtlsInhouseData);echo "</pre>";

		$sql_yarn_iss_rtn="select a.id as rtn_id,a.knitting_source,c.po_breakdown_id,c.prod_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type, sum(CASE WHEN c.entry_form ='9' THEN c.quantity ELSE 0 END) AS issue_rtn_qty from inv_receive_master a,inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and c.issue_purpose!=2 $po_id_cond group by a.id,a.knitting_source,c.po_breakdown_id, c.prod_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type";

		//echo $sql_yarn_iss_rtn;

		$yarnIssueRtnArray=sql_select($sql_yarn_iss_rtn);

		if(count($yarnIssueRtnArray)>0)
		{
			foreach($yarnIssueRtnArray as $row)
			{			
				$yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];
				$jobno = $po_wise_job_arr[$row[csf('po_breakdown_id')]];
				
				if(in_array($yarnKey,$yarn_desc_array))
				{
					if($row[csf('knitting_source')]==1)
					{
						$yearnIssueRtnData[$jobno][$yarnKey]['inhouse']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];		
					}else if($row[csf('knit_dye_source')]==3){
						$yearnIssueRtnData[$jobno][$yarnKey]['out_bound']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];
					}	
				}else{

					if($row[csf('knitting_source')]==1)
					{
						$yearnIssueRtnData[$jobno]['not_req']['inhouse']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];

						$yearnIssueInhouseRtnData[$jobno][$row[csf('prod_id')]]['not_req']['inhouse']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];
						$yearnIssueInhouseRtnData[$jobno][$row[csf('prod_id')]]['not_req']['inhouse']['rtn_id'] .= $row[csf('rtn_id')].',';

					}else{
						$yearnIssueRtnData[$jobno]['not_req']['out_bound']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];
						$yearnIssueoutboundRtnData[$jobno][$row[csf('prod_id')]]['not_req']['out_bound']['issue_rtn_qty'] += $row[csf('issue_rtn_qty')];
						$yearnIssueoutboundRtnData[$jobno][$row[csf('prod_id')]]['not_req']['out_bound']['rtn_id'] .= $row[csf('rtn_id')].',';
					}
				}	
			}
		}
		unset($yarnIssueRtnArray);
		//echo "<pre>";print_r($yearnIssueRtnQtyData);echo "</pre>";

		$greyPurchaseQntyArray=array();
		$sql_grey_production="select a.knitting_source,c.po_breakdown_id, 
		sum(CASE WHEN a.receive_basis not in(9,10) THEN c.quantity ELSE 0 END) AS grey_purchase_qnty,
		sum(CASE WHEN a.receive_basis in (9,10) THEN c.quantity ELSE 0 END) AS grey_production_qnty
		from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22,58) and c.entry_form in (22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_id_cond group by a.knitting_source,c.po_breakdown_id";//and a.receive_basis<>9 sum(c.quantity) as grey_purchase_qnty

		$grey_production_data_arr = sql_select($sql_grey_production);

		foreach ($grey_production_data_arr as $row) {				
			$jobno = $po_wise_job_arr[$row[csf("po_breakdown_id")]];

			if($row[csf('knitting_source')]==1)
			{
				$greyProductionData[$jobno]['inhouse']['grey_production_qnty'] += $row[csf('grey_production_qnty')];
			}else{
				$greyProductionData[$jobno]['out_bound']['grey_production_qnty'] += $row[csf('grey_production_qnty')];
			}

			$grey_purchase_qnty[$jobno]['grey_purchase_qnty'] += $row[csf('grey_purchase_qnty')];

		}
		unset($grey_production_data_arr);
		
		$sqlgrey_issue_data=sql_select("select c.po_breakdown_id, 								
		sum(CASE WHEN c.entry_form ='45' and c.trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,
		sum(CASE WHEN c.entry_form ='16' THEN c.quantity ELSE 0 END) AS grey_issue,
		sum(CASE WHEN c.entry_form ='61' THEN c.quantity ELSE 0 END) AS grey_issue_rollwise,
		sum(CASE WHEN c.entry_form in(51,84) and c.trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return			
		from order_wise_pro_details c where c.status_active=1 and c.is_deleted=0 and c.entry_form in(16,45,51,61,84) $po_id_cond group by c.po_breakdown_id");
			
		foreach($sqlgrey_issue_data as $row)
		{
			$jobno = $po_wise_job_arr[$row[csf("po_breakdown_id")]];

			$greayIssueDataArr[$jobno]+= $row[csf('grey_issue')]+$row[csf('grey_issue_rollwise')];
			$greayIssueRtnDataArr[$jobno]+=$row[csf('grey_issue_return')];

			$grey_receive_return_qnty_arr[$jobno]=$row[csf('grey_receive_return')];			
		}
		unset($sqlgrey_issue_data);

		$sql_fin_recv="select po_breakdown_id,
		sum(CASE WHEN c.entry_form ='7' THEN c.quantity ELSE 0 END) AS finish_production,
		sum(CASE WHEN c.entry_form ='66' THEN c.quantity ELSE 0 END) AS finish_production_rollwise,
		sum(CASE WHEN c.entry_form ='37' THEN c.quantity ELSE 0 END) AS finish_receive,
		sum(CASE WHEN c.entry_form ='68' THEN c.quantity ELSE 0 END) AS finish_receive_rollwise,
		sum(CASE WHEN c.entry_form ='18' THEN c.quantity ELSE 0 END) AS finish_issue,
		sum(CASE WHEN c.entry_form ='71' THEN c.quantity ELSE 0 END) AS finish_issue_roll_wise,
		sum(CASE WHEN c.entry_form ='46' and c.trans_type=3 THEN c.quantity ELSE 0 END) AS recv_rtn_qnty,
		sum(CASE WHEN c.entry_form ='52' and c.trans_type=4 THEN c.quantity ELSE 0 END) AS iss_retn_qnty,
		sum(CASE WHEN c.entry_form ='15' and c.trans_type=5 THEN c.quantity ELSE 0 END) AS transfer_in_qnty,
		sum(CASE WHEN c.entry_form ='15' and c.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty
		from order_wise_pro_details c where c.status_active=1 and c.is_deleted=0 and c.entry_form in(7,15,18,37,46,52,66,68,71) $po_id_cond  group by c.po_breakdown_id";

		$dataArrayfin_recv=sql_select($sql_fin_recv);
		foreach($dataArrayfin_recv as $row)
		{
			//$po_wise_job_arr[$row[csf("po_breakdown_id")]]= job_no;
			$jobno = $po_wise_job_arr[$row[csf("po_breakdown_id")]];

			$trans_qnty_fin_arr[$jobno]['transfer_in'] +=$row[csf('transfer_in_qnty')];
			$trans_qnty_fin_arr[$jobno]['transfer_out'] +=$row[csf('transfer_out_qnty')];

			$finish_receive_qnty_arr[$jobno] +=$row[csf('finish_receive')]+$row[csf('finish_receive_rollwise')];

			$finish_recv_rtn_qnty_arr[$jobno] +=$row[csf('recv_rtn_qnty')];

			$finish_purchase_qnty_arr[$jobno] +=$row[csf('finish_purchase')];

			$finish_issue_qnty_arr[$jobno] +=$row[csf('finish_issue')]+$row[csf('finish_issue_roll_wise')];

			$finish_issue_rtn_qnty_arr[$jobno] +=$row[csf('iss_retn_qnty')];
		}
		unset($dataArrayfin_recv);

		ob_start();

	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>
	<fieldset style="width:100%">	
	    
	    <table style="  " width="2700" cellspacing="0" cellpadding="0">
	        <tbody><tr>
	           <td colspan="25" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[str_replace("'", "", $company_name)]; ?></strong></td>
	        </tr>
	        <tr>
	           <td colspan="25" style="font-size:16px" width="100%" align="center">
	           	<strong>
	           		<? if ($start_date != "" && $end_date != "") echo "From " . change_date_format($start_date, 'dd-mm-yyyy') . " To " . change_date_format($end_date, 'dd-mm-yyyy') . ""; ?>
	            </strong>
	       		</td>
	        </tr>
	    	</tbody>
		</table>
	  
	    <table class="rpt_table" border="1" rules="all" width="2700" cellpadding="0" cellspacing="0" id="table_header_1">
		  <thead>
		    <tr>
		      <th rowspan="2" width="100">Main Fabric Booking No</th>
		      <th rowspan="2" width="100">Job Number</th>
		      <th rowspan="2" width="100">Buyer Name</th>
		      <th rowspan="2" width="100">Style Ref.</th>
		      <th rowspan="2" width="100">Job Qnty</th>
		      <th rowspan="2" width="100">Shipment Date</th>
		      <th rowspan="2" width="100">Count</th>
		      <th rowspan="2" width="300">Yarn Composition</th>
		      <th rowspan="2" width="100">Yarn Type</th>
		      <th rowspan="2" width="100">Yarn Required</th>
		      <th rowspan="2" width="100">Yarn Work Order Qty</th>
		      <th rowspan="2" width="100">Yarn Received Qty</th>
		      <th colspan="2" width="100">Yarn issue for knitting</th>
		      <th rowspan="2" width="100">Issue Balance</th>
		      <th colspan="2" width="100">Grey Production Qty</th>

		      <th rowspan="2" width="100">Grey Purchase Qty</th>

		      <th rowspan="2" width="100">Knitting Balance Qty</th>
		      <th rowspan="2" width="100">Grey Fabric Issue to Dyeing</th>

		      <th rowspan="2" width="100">Knit Finished fabric require Qty</th>

		      <th rowspan="2" width="100">Finished fabric Receive Qty</th>
		      <th rowspan="2" width="100">Finished fabric Receive Balance</th>
		      <th rowspan="2" width="100">Knit Issue To Cutting</th>
		      <th rowspan="2" width="100">Finished Fabric Stock</th>
		    </tr>
		    <tr>
		      <th width="100">In House</th>
		      <th width="100">Out Bound</th>
		      <th width="100">In House</th>
		      <th width="100">Out Bound</th>
		    </tr>

		  </thead>
		</table>
		
		<div style="width:2720px; overflow-y:scroll; max-height:400px;" id="scroll_body">
		<table width="2700" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" >
			<tbody>
			<? 	
			//$knitting_balance = 0;
			$i = 0;
			$total_jobWiseGreyIssueQty = 0;
			$total_fin_req = 0;
			$total_finishRcvQty = 0;
			$total_finRecvBalance = 0;
			$total_finIssueToCutting  = 0;
			$total_finStock=0;
			$total_req_quantity=0;
			$total_work_order_qty=0; 
			$total_yarn_rcv_qty=0;
			$total_yarn_inhouse_issue_qty = 0;
			$total_yarn_out_bound_issue_qty=0;
			$total_issue_balance=0;
			foreach($job_alldata_arr as $jobNo=>$row)
			{	
				$yarnDataString = $dataArrayYarn[$jobNo];
				$yarnDataString = chop($yarnDataString,",");
				$yarnDatArr = explode(",", $yarnDataString);
				$rowSpan=count($yarnDatArr);

				$grey_inhouse_production_qty = $greyProductionData[$jobNo]['inhouse']['grey_production_qnty'];
				$grey_out_bound_production_qty = $greyProductionData[$jobNo]['out_bound']['grey_production_qnty'];
				$total_grey_inhouse_production_qty += $grey_inhouse_production_qty;
				$total_grey_out_bound_production_qty += $grey_out_bound_production_qty;

				$grey_purchase_qnty = $grey_purchase_qnty[$jobNo]['grey_purchase_qnty'];
				$total_grey_purchase_qnty += $grey_purchase_qnty;
				$jobWiseGreyIssueQty = ($greayIssueDataArr[$jobNo]-$greayIssueRtnDataArr[$jobNo]);

				$knitting_balance = $job_wise_yarn_req_quantity[$jobNo] - ($greyProductionData[$jobNo]['inhouse']['grey_production_qnty']+$greyProductionData[$jobNo]['out_bound']['grey_production_qnty']+$grey_purchase_qnty[$jobNo]['grey_purchase_qnty']);
				
				//$fin_fab_req = $bookingData[$jobNo]['finish_fab_required'];
				$fab_knit_fin_req=array_sum($fabric_costing_arr['knit']['finish'][$jobNo]);
				$fab_woven_fin_req=array_sum($fabric_costing_arr['woven']['finish'][$jobNo]);
				$fin_fab_req=$fab_knit_fin_req+$fab_woven_fin_req;

				$finishRcvQty = $finish_receive_qnty_arr[$jobNo] - $finish_recv_rtn_qnty_arr[$jobNo];
				$finRecvBalance = ($fin_fab_req-$finishRcvQty);

				$finIssueToCutting  = ($finish_issue_qnty_arr[$jobNo]-$finish_issue_rtn_qnty_arr[$jobNo]);
				$finStock = ($finishRcvQty-$finIssueToCutting);

				$total_jobWiseGreyIssueQty += ($greayIssueDataArr[$jobNo]-$greayIssueRtnDataArr[$jobNo]);
				$total_fin_req += $fin_fab_req;
				$total_finishRcvQty += $finishRcvQty;
				$total_finRecvBalance +=$finRecvBalance;
				$total_finIssueToCutting +=$finIssueToCutting;
				$total_finStock += $finStock;
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo chop($bookingData[$jobNo]['booking_no'],",");?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo $jobNo;?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo $buyer_short_name_arr[$row['buyer_name']];?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo $row['style_ref_no'];?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo $row['job_qnty'];?></td>
				  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo change_date_format($row['pub_shipment_date']);?></td>
				<? 
				$start=1;
				
				foreach($yarnDatArr as $yarnDataStr)
				{
					$yarnData = explode("**", $yarnDataStr);
					$countId = $yarnData[0];
					$yarn_type_id = $yarnData[1];
					$copm_one_id = $yarnData[2];
					$copm_two_id = $yarnData[3];
					$percent_one = $yarnData[4];
					$percent_two = $yarnData[5];
					//$yarn_required_qty =$yarnData[6];
					$colorId =$yarnData[7];
					$rate =$yarnData[8];
					
					

					if($countId!="")
					{
						$compos = $composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
					}else{
						$compos = "";
					}
					//echo $jobNo."--".$countId."--".$copm_one_id."--".$percent_one."--".$yarn_type_id."--".$colorId."|";	
					$yarnKey = $countId."__".$yarn_type_id."__".$copm_one_id."__".$copm_two_id."__".$percent_one."__".$percent_two;
					
					$yarn_prod_id = $yearnRcvData[$jobNo][$yarnKey]['prod_id'];
				
					$req_quantity = $yarnRequiredArray[$jobNo][$countId][$yarn_type_id][$copm_one_id][$copm_two_id][$percent_one][$percent_two][$colorId][$rate]["req_quantity"];
					//$tempJobYarnRequired[$jobNo] += $req_quantity;
					//echo $jobNo."==".$yarnKey."<br>";
					$work_order_qty = $yearnWorkOrderData[$jobNo][$yarnKey]['work_order_qty'];
					
					$yarn_rcv_qty = $yearnRcvData[$jobNo][$yarnKey]['rcv_quantity'];
					
					$yarn_inhouse_issue_qty = ($yearnIssueData[$jobNo][$yarnKey]['inhouse']['issue_quantity']-$yearnIssueRtnData[$jobNo][$yarnKey]['inhouse']['issue_rtn_qty']);
					$yarn_out_bound_issue_qty = ($yearnIssueData[$jobNo][$yarnKey]['out_bound']['issue_quantity']-$yearnIssueRtnData[$jobNo][$yarnKey]['out_bound']['issue_rtn_qty']);

					$yarn_inhouse_not_req_issue_qty = ($yearnIssueData[$jobNo]['not_req']['inhouse']['issue_quantity']-$yearnIssueRtnData[$jobNo]['not_req']['inhouse']['issue_rtn_qty']);
					$yarn_out_bound_not_req_issue_qty = ($yearnIssueData[$jobNo]['not_req']['out_bound']['issue_quantity']-$yearnIssueRtnData[$jobNo]['not_req']['out_bound']['issue_rtn_qty']);

					$total_none_required_yarn = ($yarn_inhouse_not_req_issue_qty+$yarn_out_bound_not_req_issue_qty);

					$none_required_issue_balance = (0-$total_none_required_yarn);

					$issue_balance = ($req_quantity-($yarn_inhouse_issue_qty+$yarn_out_bound_issue_qty));

					$total_req_quantity += $req_quantity;
					$total_work_order_qty += $work_order_qty;
					$total_yarn_rcv_qty += $yarn_rcv_qty;
					$total_issue_balance += $issue_balance;
	
					$jobwisepoIds = implode(",",array_unique(explode(",",chop($job_alldata_arr[$jobNo]['po_id'],","))));
					$jobwise_grey_prodid = implode(",",array_unique(explode(",",chop($jobWisegreyprodid[$jobNo],",")))); 
					$jobwisepoId = array_unique(explode(",",chop($job_alldata_arr[$jobNo]['po_id'],",")));

			

				
					//echo "<pre>"; print_r($jobwisepoId);echo "</pre>";
					if($start!=1){echo "<tr>";}
					?>
					  <td width="100" class="word_wrap_break">
					  	<? echo $yarn_count_details[$countId]; 
				  		if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
				  		{
							echo "<hr>";
							foreach ($jobwisepoId as $val) 
							{
								//echo "<hr> ".$val;
								$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));

								foreach ($yarniseId as $key=>$row) 
								{
									if(!in_array($row,$Id_chk1))
									{
										$Id_chk1[]=$row;
										$yarnDtlsData = $yearnIssueInfoDtlsData[$row]['not_req']['yarnKey'];
										$yarnDtls = explode('__',$yarnDtlsData);
										//echo "<hr> ".$val;
										if($yarn_count_details[$yarnDtls[0]])
										{
											echo "<hr> <p style='background-color: #f2d7d5'>".$yarn_count_details[$yarnDtls[0]]."</p>";
										}
										else
										{
											echo "<hr> <p style='background-color: #f2d7d5'>&nbsp;</p>";
										}
									}
									
								}
								
							}
				  			
				  		}
					  	?>
					  </td>
					  <td width="300" class="word_wrap_break">
						<p title="<? echo $compos;?>">
						<? 
						echo substr($compos,0,50)."</p>";
						if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
						{
							echo "<hr>";
							foreach ($jobwisepoId as $val) 
							{
								
								$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));

								foreach ($yarniseId as $key=>$row) 
								{
									if(!in_array($row,$Id_chk2))
									{
										$Id_chk2[]=$row;
										$yarnDtlsData = $yearnIssueInfoDtlsData[$row]['not_req']['yarnKey'];
										$yarnDtls = explode('__',$yarnDtlsData);
										$compos = $composition[$yarnDtls[2]]." ".$yarnDtls[4]." %"." ".$composition[$yarnDtls[3]];
										?>
										<p>
											<?
											if($compos)
											{
												echo "<hr> <p title='$compos' style='background-color: #f2d7d5'>".substr($compos,0,50)."</p>";
											}
											else
											{
												echo "<hr> <p style='background-color: #f2d7d5'>&nbsp;</p>";
											}
									}
								}
							}
						}
						?>
					</td>
					  <td width="100" class="word_wrap_break">
						<? echo $yarn_type[$yarn_type_id]; 
						if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
						{
							echo "<hr>";
							foreach ($jobwisepoId as $val) 
							{
								
								$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));

								foreach ($yarniseId as $key=>$row) 
								{
									if(!in_array($row,$Id_chk3))
									{
										$Id_chk3[]=$row;
										$yarnDtlsData = $yearnIssueInfoDtlsData[$row]['not_req']['yarnKey'];
										$yarnDtls = explode('__',$yarnDtlsData);
										if($yarn_type[$yarnDtls[1]])
										{
											echo "<hr> <p style='background-color: #f2d7d5'>".$yarn_type[$yarnDtls[1]]."</p>";
										}
										else
										{
											echo "<hr> <p style='background-color: #f2d7d5'>&nbsp;</p>";
										}
									}
									
								}
								
							}
							
						}
					  ?></td>
					  <td width="100" class="word_wrap_break">
						<? echo number_format($req_quantity,2,'.','');
						
						if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
						{
							echo "<hr>";
							foreach ($jobwisepoId as $val) 
							{
								
								$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));

								foreach ($yarniseId as $key=>$row) 
								{
									if(!in_array($row,$Id_chk11))
									{
										$Id_chk11[]=$row;
										$yarnDtlsData = $yearnIssueInfoDtlsData[$row]['not_req']['yarnKey'];
										$yarnDtls = explode('__',$yarnDtlsData);
										echo "<hr> <p style='background-color: #f2d7d5'>0.00</p>";
										
									}
									
								}
								
							}
							
						}
				  		?>
					  </td>
					  <td width="100" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo."__".$yarnKey; ?>','<? echo $jobwisepoIds;?>','work_order_popup','1060px');"><? echo number_format($work_order_qty,2,'.','');?></a>

					  	<?
						if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
						{
							echo "<hr>";
							foreach ($jobwisepoId as $val) 
							{
								
								$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));

								foreach ($yarniseId as $key=>$row) 
								{
									if(!in_array($row,$Id_chk22))
									{
										$Id_chk22[]=$row;
										$yarnDtlsData = $yearnIssueInfoDtlsData[$row]['not_req']['yarnKey'];
										$yarnDtls = explode('__',$yarnDtlsData);
										echo "<hr> <p style='background-color: #f2d7d5'>0.00</p>";
										
									}
									
								}
								
							}
							
						}
				  		?>
					   </td>
					   <td width="100" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo."__".$yarn_prod_id."__".$yarnKey; ?>','<? echo $jobwisepoIds;?>','yarn_rcv_qty_popup','1360px');">
					  	<? echo number_format($yarn_rcv_qty,2,'.','');?></a>

					  	<?
					  	if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
						{
							echo "<hr>";
							foreach ($jobwisepoId as $val) 
							{
								
								$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));

								foreach ($yarniseId as $key=>$row) 
								{
									if(!in_array($row,$Id_chk33))
									{
										$Id_chk33[]=$row;
										$yarnDtlsData = $yearnIssueInfoDtlsData[$row]['not_req']['yarnKey'];
										$yarnDtls = explode('__',$yarnDtlsData);
										echo "<hr> <p style='background-color: #f2d7d5'>0.00</p>";
										
									}
									
								}
								
							}
							
						}
				  		?>
					  </td>
					  
					  <td width="100" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo."__".$yarn_prod_id."__".$yarnKey; ?>','<? echo $jobwisepoIds;?>','yarn_inhouse_issue_knitting_popup','1200px');"><? echo number_format($yarn_inhouse_issue_qty,2,'.','');?></a>

					  	<?
					  		if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
					  		{
								$total_yarn_inhouse_issue_qty += ($yarn_inhouse_issue_qty+$yarn_inhouse_not_req_issue_qty);
								//$yarn_inhouse_issue_qnty =0;

								foreach ($jobwisepoId as $val) 
								{
									
									$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));
	  
									foreach ($yarniseId as $key=>$row) 
									{
										if(!in_array($row,$Id_chk4))
										{
											$Id_chk4[]=$row;
											$yarnIssueQty = $yearnIssueInfoDtlsInhouseData[$row]['not_req']['inhouse']['issue_qnty'];
											$yarnIssueProdId = $yearnIssueInfoDtlsInhouseData[$row]['not_req']['inhouse']['prod_id'];
											$issue_rtn_inhouse_qty = $yearnIssueInhouseRtnData[$jobNo][$yarnIssueProdId]['not_req']['inhouse']['issue_rtn_qty'];
											$issue_rtn_inhouse_id = $yearnIssueInhouseRtnData[$jobNo][$yarnIssueProdId]['not_req']['inhouse']['rtn_id'];
											$issue_rtn_inhouse_id = implode(",",array_unique(explode(",",chop($issue_rtn_inhouse_id,","))));
											$yarnIssueInhouseReqQty=($yarnIssueQty-$issue_rtn_inhouse_qty);
											echo "<hr> <p style='background-color: #f2d7d5'>"; 
											?>
												<a href='#report_details' onClick="openmy_popup_page_two('<? echo $company_name."**".$jobNo."**".$jobWiseYarnKeys[$jobNo]; ?>','<? echo $row;?>','<? echo $yarnIssueProdId;?>','<? echo $issue_rtn_inhouse_id;?>','inhouse_yarn_issue_not_required_popup_two','1000px');"><? echo number_format($yarnIssueInhouseReqQty,2,'.','');?>
					  							</a></p>
											<?
											
										}
										
									}
									
								}
							
					  		}else{
					  			$total_yarn_inhouse_issue_qty += $yarn_inhouse_issue_qty;
					  		}					  		
					  	?>
					  </td>

					  <td width="100" class="word_wrap_break" ><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo."__".$yarn_prod_id."__".$yarnKey; ?>','<? echo $jobwisepoId;?>','yarn_out_bound_issue_knitting_popup','1200px');">
					  	<? echo number_format($yarn_out_bound_issue_qty,2,'.','');?></a>

					  	<?
					  		if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
					  		{
					  			$total_yarn_out_bound_issue_qty += ($yarn_out_bound_issue_qty+$yarn_out_bound_not_req_issue_qty);
								foreach ($jobwisepoId as $val) 
								{
									
									$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));
									$yarnIssueQty = 0;
									$yarnIssueProdId = '';
									foreach ($yarniseId as $key=>$row) 
									{
										if(!in_array($row,$Id_chk5))
										{
											$Id_chk5[]=$row;
											$yarnIssueQty = $yearnIssueInfoDtlsOutboundData[$row]['not_req']['out_bound']['issue_qnty'];
											$yarnIssueProdId = $yearnIssueInfoDtlsOutboundData[$row]['not_req']['out_bound']['prod_id'];
											$issue_rtn_outbound_qty = $yearnIssueoutboundRtnData[$jobNo][$yarnIssueProdId]['not_req']['out_bound']['issue_rtn_qty'];
											$issue_rtn_ioutbound_id = $yearnIssueoutboundRtnData[$jobNo][$yarnIssueProdId]['not_req']['out_bound']['rtn_id'];
											$issue_rtn_ioutbound_id = implode(",",array_unique(explode(",",chop($issue_rtn_ioutbound_id,","))));
											$yarnIssueoutboundReqQty=($yarnIssueQty-$issue_rtn_outbound_qty);
											echo "<hr> <p style='background-color: #f2d7d5'>"; 
											?>
												<a href='#report_details' onClick="openmy_popup_page_two('<? echo $company_name."**".$jobNo."**".$jobWiseYarnKeys[$jobNo]; ?>','<? echo $row;?>','<? echo $yarnIssueProdId;?>','<? echo $issue_rtn_ioutbound_id;?>','outbound_yarn_issue_not_required_popup_two','1000px');"><? echo number_format($yarnIssueoutboundReqQty,2,'.','');?>
												</a></p>
											<?
											
										}
										
									}
									
								}
					  		}else{
					  			$total_yarn_out_bound_issue_qty += $yarn_out_bound_issue_qty;
					  		}					  		
					  	?>

					  </td>


					  <td width="100" class="word_wrap_break" >
					  	<? 
					  	echo number_format($issue_balance,2,'.','');

					  	if($start==$rowSpan && $yarn_inhouse_not_req_issue_qty>0)
				  		{
							foreach ($jobwisepoId as $val) 
							{
								
								$yarniseId = array_unique(explode(",",chop($yearnIssueInforData[$val]['not_req']['issue_id'],",")));
	
								foreach ($yarniseId as $key=>$row) 
								{
									if(!in_array($row,$Id_chk44))
									{
										$Id_chk44[]=$row;
										$yarnIssueInhouseQty = $yearnIssueInfoDtlsInhouseData[$row]['not_req']['inhouse']['issue_qnty'];
										$yarnIssueOutboundQty = $yearnIssueInfoDtlsOutboundData[$row]['not_req']['out_bound']['issue_qnty'];
										$yarnIssueProdId = $yearnIssueInfoDtlsInhouseData[$row]['not_req']['inhouse']['prod_id'];
										$issue_rtn_inhouse_qty = $yearnIssueInhouseRtnData[$jobNo][$yarnIssueProdId]['not_req']['inhouse']['issue_rtn_qty'];
										
										$issue_rtn_outbound_qty = $yearnIssueoutboundRtnData[$jobNo][$yarnIssueProdId]['not_req']['out_bound']['issue_rtn_qty'];

										$yarnIssueInhouseReqQty=($yarnIssueInhouseQty-$issue_rtn_inhouse_qty);
										$yarnIssueoutboundReqQty=($yarnIssueOutboundQty-$issue_rtn_outbound_qty);
										$total_none_required_issue_yarn = ($yarnIssueInhouseReqQty+$yarnIssueoutboundReqQty);

										$none_required_yarn_issue_balance = (0-$total_none_required_issue_yarn);
										echo "<hr> <p style='background-color: #f2d7d5; vertical-align: baseline;'>"; 
										echo number_format($none_required_yarn_issue_balance,2,'.','')."</p>";
									}
								}
							}

							// echo "<hr> <p style='background-color: #f2d7d5; vertical-align: baseline;'>"; 
							// echo number_format($none_required_issue_balance,2,'.','')."</p>";
				  			$total_issue_balance = ($total_issue_balance+$none_required_issue_balance);
				  		}else{
				  			$total_issue_balance = $total_issue_balance;
				  		}
					  	?>
					  </td>
					 

					<? if($start==1){ ?>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo;?>','<? echo $jobwisepoId;?>','grey_inhouse_production_popup','1000px');"><? echo number_format($grey_inhouse_production_qty,2,'.','');?></a></td>
						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo; ?>','<? echo $jobwisepoId;?>','grey_outbound_production_popup','1000px');"><? echo number_format($grey_out_bound_production_qty,2,'.','');?></a></td>

						   <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break">
						   	<a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo;?>','<? echo $jobwisepoId;?>','grey_fabric_purchase_popup','1000px');"><? echo number_format($grey_purchase_qnty,2,'.','');?></a>
						   </td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? 
						  	$knitting_balance = $job_wise_yarn_req_quantity[$jobNo]-($grey_inhouse_production_qty+$grey_out_bound_production_qty+$grey_purchase_qnty);
						  	$total_knitting_balance += $knitting_balance;
						  	echo number_format($knitting_balance,2,'.','');?>
						  </td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo; ?>','<? echo $jobwisepoId; ?>','grey_issue_popup','750px');"><? echo number_format($jobWiseGreyIssueQty,2,'.','');?></a></td> 

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo number_format($fin_fab_req,2,'.','');?></td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break" >
						  	
						  	<a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo; ?>','<? echo $jobwisepoId; ?>','finish_fabrcv_popup','1230px');"><? echo number_format($finishRcvQty,2,'.','');?></a>
						  		
						  </td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo number_format($finRecvBalance,2,'.','');?></td>
						  
						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break">
						  	
						  	<a href='#report_details' onClick="openmy_popup_page('<? echo $company_name."__".$jobNo; ?>','<? echo $jobwisepoId; ?>','issue_to_cut_popup','950px');"><? echo number_format($finIssueToCutting,2,'.','');?></a>
						  	
						  </td>

						  <td rowspan="<? echo $rowSpan;?>" width="100" valign="middle" class="word_wrap_break"><? echo number_format($finStock,2,'.','');?></td>
						</tr>
					<?
					} else { 
						echo "</tr>";	
					}
					$start++;
				}
				$i++;

			}
			?>
			</tbody>
		</table>
		</div>

		<table  class="rpt_table" rules="all" style="  " width="2700" cellspacing="0" cellpadding="0" border="1">
			<tfoot>
				<tr>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="300"><p class="word_wrap_break">&nbsp;</p></th>
				  <th width="100"><p class="word_wrap_break">Total &nbsp;</p></th>
				  <th width="100" style="text-align: left;" id="value_html_req_quantity"><p class="word_wrap_break"><? echo number_format($total_req_quantity,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_work_order_qty"><p class="word_wrap_break"><? echo number_format($total_work_order_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_yarn_rcv_qty" ><p class="word_wrap_break"><? echo number_format($total_yarn_rcv_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_inhouse_issue_qty" ><p class="word_wrap_break"><? echo number_format($total_yarn_inhouse_issue_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_out_bound_issue_qty" ><p class="word_wrap_break"><? echo number_format($total_yarn_out_bound_issue_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_issue_balance" ><p class="word_wrap_break"><? echo number_format($total_issue_balance,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_inhouse_production_qty" ><p class="word_wrap_break"><? echo number_format($total_grey_inhouse_production_qty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_out_bound_production_qty" ><p class="word_wrap_break"><? echo number_format($total_grey_out_bound_production_qty,2,'.','');?></p></th>
				  
				  <th width="100" style="text-align: left;" id="value_html_knitting_balance" ><? echo number_format($total_grey_purchase_qnty,2,'.','');?></th>

				  <th width="100" style="text-align: left;" id="value_html_knitting_balance" ><p class="word_wrap_break"><? echo number_format($total_knitting_balance,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_jobwise_grey_issue_qty" ><p class="word_wrap_break"><? echo number_format($total_jobWiseGreyIssueQty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;"><p class="word_wrap_break"><? echo number_format($total_fin_req,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_finish_rcv_qty" ><p class="word_wrap_break"><? echo number_format($total_finishRcvQty,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_finrecv_balance" ><p class="word_wrap_break"><? echo number_format($total_finRecvBalance,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_finissue_to_cutting" ><p class="word_wrap_break"><? echo number_format($total_finIssueToCutting,2,'.','');?></p></th>
				  <th width="100" style="text-align: left;" id="value_html_fin_stock"><p class="word_wrap_break"><? echo number_format($total_finStock,2,'.','');?></p></th>
				</tr>
			</tfoot>
		</table>
	</fieldset>

	<br>

	<div style=" width: 820px;">

		<div style=" width: 620px; float: left;" id="summary_yarn_count">			
			<table width="620" class="rpt_table" border="1" rules="all">
				<thead>
					<tr>
						<th width="620" colspan="5">Requirement as per Budget</th>
					</tr>

					<tr bgcolor="<? echo "#FFFFFF"; ?>">
						<th width="100">Yarn Count</th>
						<th width="150">Yarn Type</th>
						<th width="200">Yarn Composition</th>
						<th width="170">Req.Qty</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					
					$i=0;
					$y_count=0;
					foreach ($countSummaryArr as $k_count => $v_count) 
					{
						foreach ($v_count as $k_type => $v_type) 
						{
							foreach ($v_type as $k_copm_one => $v_copm_one) 
							{
								foreach ($v_copm_one as $k_copm_two => $v_copm_two) 
								{
									foreach ($v_copm_two as $k_percent_one => $v_percent_one) 
									{
										foreach ($v_percent_one as $k_percent_two => $data) 
										{
											    $y_count +=count($k_count);
												$total_yarn_require_qty += $data['req_quantity'];
												$compos = $composition[$k_copm_one]." ".$k_percent_one." %"." ".$composition[$k_copm_two];
											?>
												<tr bgcolor="<? echo "#FFFFFF"; ?>">
													<td><? echo $yarn_count_details[$k_count];?></td>
													<td><? echo $yarn_type[$k_type];?></td>
													<td><? echo $compos;?></td>
													<td align="right"><? echo number_format($data['req_quantity'],2,'.','');?></td>
												</tr>
											<?
										}
									}
								}
							}
						}
						$i++;
						
					}
					?>
					<tr style="background-color:#CFF; font-weight:bold">
						<td colspan="3" align="right">TTL Yarn (<? echo $y_count;?>) :</td>
						<td align="right"><? echo number_format($total_yarn_require_qty,2,'.','');?></td>
					</tr>
				</tbody>
		    </table>
	    </div>	
	</div>
	
	<?
 	$html = ob_get_contents();
    ob_clean();

    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html####$filename";
    exit();
}

// Work order popup
if($action == "work_order_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$data_array = explode("__", $data);

	$company_id = $data_array[0];
	$job_no = $data_array[1];
	$countId = $data_array[2];
	$yarn_type_id = $data_array[3];
	$copm_one_id = $data_array[4];
	$copm_two_id = $data_array[5];
	$percent_one = $data_array[6];
	$percent_two = $data_array[7];	

	$sql_work_order = "select a.wo_number,a.wo_date,d.job_no, d.buyer_id,d.yarn_count, d.yarn_comp_type1st,d.yarn_comp_type2nd, d.yarn_comp_percent1st,d.yarn_comp_percent2nd, d.yarn_type, d.color_name, d.req_quantity, d.supplier_order_quantity,d.rate,d.remarks from  wo_non_order_info_mst a, wo_non_order_info_dtls d where a.status_active=1 and a.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.id=d.mst_id and d.item_category_id=1 and d.job_no='$job_no' and d.yarn_count=$countId and d.yarn_type=$yarn_type_id and d.yarn_comp_type1st=$copm_one_id and d.yarn_comp_type2nd=$copm_two_id and d.yarn_comp_percent1st=$percent_one";

	$work_order_data = sql_select($sql_work_order);
	?>
    <script>
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$(".flt").show();
	}
		
	</script>	
	<fieldset style="width:1020px; margin-left:3px">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">

			<table style="  " width="1020" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td colspan="11" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[str_replace("'", "", $company_id)]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="11" style="font-size:16px" width="100%" align="center"><strong>Yarn Work Order Statement</strong>
						</td>
					</tr>
				</tbody>
			</table>

			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
                    <th width="40">SL</th>
                    <th width="110">WO No</th>
                    <th width="100">WO Date</th>
                    <th width="150">Supplier</th>
                    <th width="200">Yarn Details</th>
                    <th width="50">Color</th>
                    <th width="50">Count</th>
                    <th width="50">Qty</th>
                    <th width="50">Rate</th>
                    <th width="50">Amount</th>
                    <th width="">Remarks</th>
				</thead>
				
            </table>
            <div style="width:1040px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="table_body">
                <tbody>
               			<?
               			$i=1;
               			foreach ($work_order_data as $row) {
	               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               			$compos = $composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
	               			
	               			$total_req_quantity += $row[csf('supplier_order_quantity')];
	               			$total_amount += ($row[csf('rate')]*$row[csf('supplier_order_quantity')]);
	               			?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40" align="center"><? echo $i; ?></td>
	                            <td width="110"><p><? echo $row[csf('wo_number')]; ?></p></td>
	                            <td width="100"><p><? echo change_date_format($row[csf('wo_date')]);?></p></td>
	                            <td width="150"><? echo $buyer_short_name_arr[$row[csf('buyer_id')]]; ?></td>
	                            <td width="200"><p><? echo $compos; ?></p></td>
	                            <td width="50"><p><? echo $color_arr[$row[csf('color_name')]]; ?></p></td>
	                            <td width="50"><p><? echo $yarn_count_details[$row[csf('yarn_count')]]; ?></p></td>
	                            <td width="50" align="center"><p><? echo number_format($row[csf('supplier_order_quantity')],2,'.',''); ?></p></td>
	                            <td width="50" align="right"><p><? echo number_format($row[csf('rate')],2,'.',''); ?></p></td>
	                            <td width="50" align="right"><p><? echo number_format(($row[csf('rate')]*$row[csf('supplier_order_quantity')]),2,'.',''); ?></p></td>
	                            <td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
	                        </tr>
	                    	<?
	                    	$i++;
                    	}
                        ?>
						
                </tbody>
            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="40">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="200">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">Total &nbsp;</th>
                    <th width="50" align="center"><? echo number_format($total_req_quantity,2,'.',''); ?></th>
                    <th width="50">&nbsp;</th>
                    <th width="50" align="right"><? echo number_format($total_amount,2,'.',''); ?></th>
                    <th width="">&nbsp;</th>
				</tfoot>
            </table>
        </div>
    </fieldset>
     <script>setFilterGrid('table_body',-1);</script>
    <?
	exit();
}
// yarn received qty popup
if($action == "yarn_rcv_qty_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$data_array = explode("__", $data);

	$company_id = $data_array[0];
	$job_no = $data_array[1];
	$yarn_prod_id = $data_array[2];
	$yarn_prod_id = chop($yarn_prod_id,",");
	$countId = $data_array[3];
	$yarn_type_id = $data_array[4];
	$copm_one_id = $data_array[5];
	$copm_two_id = $data_array[6];
	$percent_one = $data_array[7];
	$percent_two = $data_array[8];


	$sql_yarn_rcv = "select a.id,a.recv_number,a.receive_date,a.challan_no,a.receive_basis,a.booking_id, a.receive_basis,b.prod_id,b.cons_quantity,b.order_rate,c.wo_number,c.supplier_id,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd,e.lot from inv_receive_master a, inv_transaction b, wo_non_order_info_mst c , wo_non_order_info_dtls d,product_details_master e where a.id=b.mst_id and b.pi_wo_batch_no=c.id and c.id=d.mst_id and d.job_no='$job_no' and b.item_category=1 and b.transaction_type=1 and a.entry_form=1 and a.receive_basis in(2) and b.prod_id=e.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.item_category_id=1 and e.status_active=1 and e.is_deleted=0 
		and e.id in($yarn_prod_id) and e.yarn_count_id=$countId and e.yarn_type=$yarn_type_id and e.yarn_comp_type1st=$copm_one_id and e.yarn_comp_type2nd=$copm_two_id and e.yarn_comp_percent1st=$percent_one
	group by a.id,a.recv_number,a.receive_date,a.challan_no,a.receive_basis,a.booking_id, a.receive_basis,b.prod_id,b.cons_quantity,b.order_rate,c.wo_number,c.supplier_id,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd,e.lot

	union all 

	select a.id,a.recv_number,a.receive_date,a.challan_no,a.receive_basis,a.booking_id, a.receive_basis,b.prod_id,b.cons_quantity,b.order_rate,c.wo_number,c.supplier_id,d.job_no,d.buyer_id,d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd,e.lot 
	from inv_receive_master a, inv_transaction b, wo_non_order_info_mst c ,wo_non_order_info_dtls d,product_details_master e, com_pi_item_details f
	where a.id=b.mst_id and a.booking_id=f.pi_id and c.id=d.mst_id and d.job_no='$job_no' and b.item_category=1 
	and b.transaction_type=1 and a.entry_form=1 and a.receive_basis in(1) and b.prod_id=e.id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.item_category_id=1 and e.status_active=1 and e.is_deleted=0 and d.id=f.work_order_dtls_id and e.id in($yarn_prod_id) and e.yarn_count_id=$countId and e.yarn_type=$yarn_type_id and e.yarn_comp_type1st=$copm_one_id and e.yarn_comp_type2nd=$copm_two_id and e.yarn_comp_percent1st=$percent_one
	group by a.id,a.recv_number,a.receive_date,a.challan_no,a.receive_basis,a.booking_id, a.receive_basis,b.prod_id,b.cons_quantity,b.order_rate,c.wo_number,c.supplier_id,d.job_no,d.buyer_id,
	d.style_no,e.yarn_count_id,e.yarn_type,e.yarn_comp_type1st,e.yarn_comp_type2nd,e.yarn_comp_percent1st,e.yarn_comp_percent2nd,e.lot";

	$yarn_rcv_data=sql_select($sql_yarn_rcv);
	?>
    <script>
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$(".flt").show();
	}		
	</script>

	<fieldset style="width:1320px; margin-left:3px">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">

			<table style="  " width="1320" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td colspan="15" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[str_replace("'", "", $company_id)]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="15" style="font-size:16px" width="100%" align="center"><strong>Yarn Received Statement</strong>
						</td>
					</tr>
				</tbody>
			</table>

			<table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
                    <th width="40">SL</th>
                    <th width="100">Recv. Date</th>
                    <th width="110">MRR No</th>
                    <th width="100">Challan No</th>
                    <th width="50">Received Basis</th>
                    <th width="100">WO/PI No</th>
                    <th width="150">Supplier</th>
                    <th width="100">Lot No</th>
                    <th width="50">Count</th>
                    <th width="200">Yarn Details</th>
                    <th width="50">Type</th>
                    <th width="50">Qty</th>
                    <th width="50">Rate</th>
                    <th width="50">Amount</th>
                    <th width="">Remarks</th>
				</thead>
				
            </table>
            <div style="width:1340px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" id="table_body">
                <tbody>
               			<?
               			$i=1;
               			foreach ($yarn_rcv_data as $row) {
	               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               			$compos = $composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
	               			
	               			$total_rcv_quantity += $row[csf('cons_quantity')];
	               			$total_amount += ($row[csf('cons_quantity')]*$row[csf('order_rate')]);
	               			?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="100"><p><? echo change_date_format($row[csf('receive_date')]); ?></p></td>
	                            <td width="110"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
	                            <td width="50"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
	                            <td width="100"><p><? echo $row[csf('wo_number')]; ?></p></td>
	                            <td width="150"><? echo $supplierArr[$row[csf('supplier_id')]]; ?></td>
	                            <td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
	                            <td width="50"><p><? echo $yarn_count_details[$row[csf('yarn_count_id')]]; ?></p></td>
	                            <td width="200"><p><? echo $compos; ?></p></td>
	                            <td width="50"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                            <td width="50" align="center"><p><? echo $row[csf('cons_quantity')]; ?></p></td>
	                            <td width="50" align="right"><p><? echo number_format($row[csf('order_rate')],2,'.',''); ?></p></td>
	                            <td width="50" align="right"><p><? echo number_format($row[csf('cons_quantity')]*$row[csf('order_rate')],2,'.',''); ?></p></td>
	                            <td align="center"><p><? echo $row[csf('remarks')]; ?></p></td>
	                        </tr>
	                    	<?
	                    	$i++;
                    	}
                        ?>
						
                </tbody>
            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1320" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="40">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="200">&nbsp;</th>
                    <th width="50">Total &nbsp;</th>
                    <th width="50"><? echo number_format($total_rcv_quantity,2,'.',''); ?></th>
                    <th width="50">&nbsp;</th>
                    <th width="50"><? echo number_format($total_amount,2,'.',''); ?></th>
                    <th width="">&nbsp;</th>
				</tfoot>
            </table>
        </div>
    </fieldset>
     <script>setFilterGrid('table_body',-1);</script>
    <?
	exit();
}

// yarn inhouse issue knitting qty popup
if($action == "yarn_inhouse_issue_knitting_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$data_array = explode("__", $data);

	$company_id = $data_array[0];
	$job_no = $data_array[1];
	$yarn_prod_id = $data_array[2];
	$yarn_prod_id = chop($yarn_prod_id,",");
	$countId = $data_array[3];
	$yarn_type_id = $data_array[4];
	$copm_one_id = $data_array[5];
	$copm_two_id = $data_array[6];
	$percent_one = $data_array[7];
	$percent_two = $data_array[8];

	if($yarn_prod_id)
	{
		$sql_requisition_no = "select a.booking_no,c.requisition_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and c.prod_id in($yarn_prod_id)";
		$yarnrequsitionData = sql_select($sql_requisition_no);

		$requisitionDataArr=array();
		foreach ($yarnrequsitionData as $row) {
			$requisitionDataArr[$row[csf('requisition_no')]] = $row[csf('booking_no')];
		}
	}	
	?>
    <script>
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$(".flt").show();
	}		
	</script>

	<fieldset style="width:1160px; margin-left:3px">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">

			<table style="  " width="1140" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td colspan="15" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[str_replace("'", "", $company_id)]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="15" style="font-size:16px" width="100%" align="center"><strong>Yarn Issue Statement - In House</strong>
						</td>
					</tr>
				</tbody>
			</table>

			<table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<th colspan="10">Yarn Issue</th>
				</thead>
				<thead>
					
	                    <th width="40">SL</th>
	                    <th width="150">Issue No</th>
	                    <th width="100">Issue Date</th>
	                    <th width="150">Issue To</th>
	                    <th width="150">Booking No</th>
	                    <th width="100">Yarn Compositon</th>
	                    <th width="100">Lot No</th>
	                    <th width="100">Yarn Type</th>
	                    <th width="150">Yarn Supplier</th>
	                    <th width="100">Issue Qnty (In)</th>
                	
				</thead>
				
            </table>
            <div style="width:1160px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_body">
                <tbody>
               			<?
               			$sql_yarn_iss ="select a.issue_number,a.issue_date,knit_dye_company,a.supplier_id,b.requisition_no,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,d.lot,
				sum(CASE WHEN c.entry_form ='3' THEN c.quantity ELSE 0 END) AS issue_qnty
				from inv_issue_master a,inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and c.issue_purpose!=2 and c.po_breakdown_id in($order_id) and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.knit_dye_source = 1 and d.yarn_count_id=$countId and d.yarn_type=$yarn_type_id and d.yarn_comp_type1st=$copm_one_id and d.yarn_comp_type2nd=$copm_two_id and d.yarn_comp_percent1st=$percent_one group by a.issue_number,a.issue_date,knit_dye_company,a.supplier_id,b.requisition_no,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,d.lot";

						$yarn_issue_data=sql_select($sql_yarn_iss);

               			$i=1;
               			foreach ($yarn_issue_data as $row) {
	               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               			$compos = $composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
	               			
	               			$total_issue_quantity += $row[csf('issue_qnty')]; 
	               			$issueTo = $companyArr[$row[csf('knit_dye_company')]];
	               			?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="150"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
	                            <td width="150"><p><? echo $issueTo; ?></p></td>
	                            <td width="150"><p><? echo $requisitionDataArr[$row[csf('requisition_no')]];?></p></td>
	                            <td width="100"><p><? echo $compos; ?></p></td>
	                            <td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
	                            <td width="100"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                            <td width="150"><p><? echo $supplierArr[$row[csf('supplier_id')]]; ?></p></td>
	                            <td width="100" align="center"><p><? echo $row[csf('issue_qnty')]; ?></p></td>
	                        </tr>
	                    	<?
	                    	$i++;
                    	}
                        ?>
						
                </tbody>
            </table>
            
            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="40">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">Total Issue &nbsp; </th>
                    <th width="100" style="text-align: right;"><? echo number_format($total_issue_quantity,2,'.','');?></th>
				</tfoot>
            </table>

            <br>

            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<th colspan="10">Yarn Issue Return</th>
				</thead>
				<thead>
					
	                    <th width="40">SL</th>
	                    <th width="150">Issue Return No</th>
	                    <th width="100">Return Date</th>
	                    <th width="150">Return From</th>
	                    <th width="150">Booking No</th>
	                    <th width="100">Yarn Compositon</th>
	                    <th width="100">Lot No</th>
	                    <th width="100">Yarn Type</th>
	                    <th width="150">Yarn Supplier</th>
	                    <th width="100">Return Qnty (In)</th>
                	
				</thead>
				
            </table>
           
			<table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_body">
                <tbody>
               			<?

					$sql_yarn_iss_rtn="select a.recv_number,a.receive_date as return_date,a.knitting_company,a.supplier_id,a.booking_id as requisition_no,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,d.lot, 
				sum(CASE WHEN c.entry_form ='9' THEN c.quantity ELSE 0 END) AS issue_rtn_qty
				from inv_receive_master a,inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and c.issue_purpose!=2 and c.po_breakdown_id in($order_id) and a.knitting_source = 1 and d.yarn_count_id=$countId and d.yarn_type=$yarn_type_id and d.yarn_comp_type1st=$copm_one_id and d.yarn_comp_type2nd=$copm_two_id and d.yarn_comp_percent1st=$percent_one group by a.recv_number,a.receive_date,a.knitting_company,a.supplier_id,a.booking_id,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,d.lot";

               			$yarn_issue_rtn_data=sql_select($sql_yarn_iss_rtn);
               			
               			$i=1;
               			foreach ($yarn_issue_rtn_data as $row) {
	               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               			$compos = $composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
	               			
	               			$total_issue_rtn_quantity += $row[csf('issue_rtn_qty')]; 
	               			$returnFrom = $companyArr[$row[csf('knitting_company')]];
	               			?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="150"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="100"><p><? echo change_date_format($row[csf('return_date')]); ?></p></td>
	                            <td width="150"><p><? echo $returnFrom; ?></p></td>
	                            <td width="150"><p><? echo $requisitionDataArr[$row[csf('requisition_no')]];?></p></td>
	                            <td width="100"><p><? echo $compos; ?></p></td>
	                            <td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
	                            <td width="100"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                            <td width="150"><p><? echo $supplierArr[$row[csf('supplier_id')]]; ?></p></td>
	                            <td width="100" align="center"><p><? echo $row[csf('issue_rtn_qty')]; ?></p></td>
	                        </tr>
	                    	<?
	                    	$i++;
                    	}
                        ?>
						
                </tbody>
                </div>
            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<tr>					
						<th width="40">&nbsp;</th>
	                    <th width="150">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="150">&nbsp;</th>
	                    <th width="150">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="150">Total Return &nbsp; </th>
	                    <th width="100" style="text-align: right;"><? echo number_format($total_issue_rtn_quantity,2,'.','');?></th>					
					</tr>
					<tr>
						<th colspan="9">Total Issue</th>
						<th style="text-align: right;"><? echo number_format(($total_issue_quantity-$total_issue_rtn_quantity),2,'.','');?></th>
					</tr>
				</tfoot>
            </table>

        
    </fieldset>
     <script>setFilterGrid('table_body',-1);</script>
    <?
	exit();
}

// yarn inhouse issue knitting qty popup
if($action == "yarn_out_bound_issue_knitting_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$data_array = explode("__", $data);

	$company_id = $data_array[0];
	$job_no = $data_array[1];
	$yarn_prod_id = $data_array[2];
	$yarn_prod_id = chop($yarn_prod_id,",");
	$countId = $data_array[3];
	$yarn_type_id = $data_array[4];
	$copm_one_id = $data_array[5];
	$copm_two_id = $data_array[6];
	$percent_one = $data_array[7];
	$percent_two = $data_array[8];

	if($yarn_prod_id!="")
	{	
		$sql_requisition_no = "select a.booking_no,c.requisition_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b, ppl_yarn_requisition_entry c where a.id=b.mst_id and b.id=c.knit_id and c.prod_id in($yarn_prod_id)";
		$yarnrequsitionData = sql_select($sql_requisition_no);

		$requisitionDataArr=array();
		foreach ($yarnrequsitionData as $row) {
			$requisitionDataArr[$row[csf('requisition_no')]] = $row[csf('booking_no')];
		}
	}
	
	?>
    <script>
	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		$(".flt").hide();
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="380px";
		$(".flt").show();
	}		
	</script>

	<fieldset style="width:1160px; margin-left:3px">
        <input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">

			<table style="  " width="1140" cellspacing="0" cellpadding="0">
				<tbody>
					<tr>
						<td colspan="15" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[str_replace("'", "", $company_id)]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="15" style="font-size:16px" width="100%" align="center"><strong>Yarn Issue Statement-Out Bound</strong>
						</td>
					</tr>
				</tbody>
			</table>

			<table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<th colspan="10">Yarn Issue</th>
				</thead>
				<thead>
					
	                    <th width="40">SL</th>
	                    <th width="150">Issue No</th>
	                    <th width="100">Issue Date</th>
	                    <th width="150">Issue To</th>
	                    <th width="150">Booking No</th>
	                    <th width="100">Yarn Compositon</th>
	                    <th width="100">Lot No</th>
	                    <th width="100">Yarn Type</th>
	                    <th width="150">Yarn Supplier</th>
	                    <th width="100">Issue Qnty (Out)</th>
                	
				</thead>
				
            </table>
            <div style="width:1160px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_body">
                <tbody>
               			<?
               			$sql_yarn_iss ="select a.issue_number,a.issue_date,knit_dye_company,a.supplier_id,b.requisition_no,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,d.lot,
				sum(CASE WHEN c.entry_form ='3' THEN c.quantity ELSE 0 END) AS issue_qnty
				from inv_issue_master a,inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and c.issue_purpose!=2 and c.po_breakdown_id in($order_id) and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and a.knit_dye_source = 3 and d.yarn_count_id=$countId and d.yarn_type=$yarn_type_id and d.yarn_comp_type1st=$copm_one_id and d.yarn_comp_type2nd=$copm_two_id and d.yarn_comp_percent1st=$percent_one group by a.issue_number,a.issue_date,knit_dye_company,a.supplier_id,b.requisition_no,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,d.lot";

               			$yarn_issue_data=sql_select($sql_yarn_iss);
               			
               			$i=1;
               			foreach ($yarn_issue_data as $row) {
	               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               			$compos = $composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
	               			
	               			$total_issue_quantity += $row[csf('issue_qnty')]; 
	               			$issueTo = $supplierArr[$row[csf('knit_dye_company')]];
	               			?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="150"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="100"><p><? echo change_date_format($row[csf('issue_date')]); ?></p></td>
	                            <td width="150"><p><? echo $issueTo; ?></p></td>
	                            <td width="150"><p><? echo $requisitionDataArr[$row[csf('requisition_no')]];?></p></td>
	                            <td width="100"><p><? echo $compos; ?></p></td>
	                            <td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
	                            <td width="100"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                            <td width="150"><p><? echo $supplierArr[$row[csf('supplier_id')]]; ?></p></td>
	                            <td width="100" align="center"><p><? echo $row[csf('issue_qnty')]; ?></p></td>
	                        </tr>
	                    	<?
	                    	$i++;
                    	}
                        ?>
						
                </tbody>
            </table>
            
            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<th width="40">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="150">Total Issue &nbsp; </th>
                    <th width="100" style="text-align: right;"><? echo number_format($total_issue_quantity,2,'.','');?></th>
				</tfoot>
            </table>

            <br>

            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<th colspan="10">Yarn Issue Return</th>
				</thead>
				<thead>
					
	                    <th width="40">SL</th>
	                    <th width="150">Issue Return No</th>
	                    <th width="100">Return Date</th>
	                    <th width="150">Return From</th>
	                    <th width="150">Booking No</th>
	                    <th width="100">Yarn Compositon</th>
	                    <th width="100">Lot No</th>
	                    <th width="100">Yarn Type</th>
	                    <th width="150">Yarn Supplier</th>
	                    <th width="100">Return Qnty (Out)</th>
                	
				</thead>
				
            </table>
           
			<table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="table_body">
                <tbody>
               			<?
               			$sql_yarn_iss_rtn="select a.recv_number,a.receive_date as return_date,a.knitting_company,a.supplier_id,a.booking_id as requisition_no,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,d.lot, 
				sum(CASE WHEN c.entry_form ='9' THEN c.quantity ELSE 0 END) AS issue_rtn_qty
				from inv_receive_master a,inv_transaction b,order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and c.issue_purpose!=2 and c.po_breakdown_id in($order_id) and a.knitting_source = 3 and d.yarn_count_id=$countId and d.yarn_type=$yarn_type_id and d.yarn_comp_type1st=$copm_one_id and d.yarn_comp_type2nd=$copm_two_id and d.yarn_comp_percent1st=$percent_one group by a.recv_number,a.receive_date,a.knitting_company,a.supplier_id,a.booking_id,c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type,d.lot";

               			$yarn_issue_rtn_outbound_data=sql_select($sql_yarn_iss_rtn);
               			
               			$i=1;
               			foreach ($yarn_issue_rtn_outbound_data as $row) {
	               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               			$compos = $composition[$row[csf('yarn_comp_type1st')]]." ".$row[csf('yarn_comp_percent1st')]." %"." ".$composition[$row[csf('yarn_comp_type2nd')]];
	               			
	               			$total_issue_rtn_quantity += $row[csf('issue_rtn_qty')]; 
	               			$returnFrom = $companyArr[$row[csf('knitting_company')]];
	               			if($row[csf('issue_rtn_qty')]!=0)
	               			{
	               			?>	               			
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="40" align="center"><? echo $i; ?></td>
								<td width="150"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="100"><p><? echo change_date_format($row[csf('return_date')]); ?></p></td>
	                            <td width="150"><p><? echo $returnFrom; ?></p></td>
	                            <td width="150"><p><? echo $requisitionDataArr[$row[csf('requisition_no')]];?></p></td>
	                            <td width="100"><p><? echo $compos; ?></p></td>
	                            <td width="100"><p><? echo $row[csf('lot')]; ?></p></td>
	                            <td width="100"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?></p></td>
	                            <td width="150"><p><? echo $supplierArr[$row[csf('supplier_id')]]; ?></p></td>
	                            <td width="100" align="center"><p><? echo $row[csf('issue_rtn_qty')]; ?></p></td>
	                        </tr>
	                    	<?
	                    	$i++;
	                    	}
                    	}
                        ?>
						
                </tbody>
                </div>
            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1140" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<tr>					
						<th width="40">&nbsp;</th>
	                    <th width="150">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="150">&nbsp;</th>
	                    <th width="150">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="150">Total Return &nbsp; </th>
	                    <th width="100" style="text-align: right;"><? echo number_format($total_issue_rtn_quantity,2,'.','');?></th>					
					</tr>
					<tr>
						<th colspan="9">Total Issue</th>
						<th style="text-align: right;"><? echo number_format(($total_issue_quantity-$total_issue_rtn_quantity),2,'.','');?></th>
					</tr>
				</tfoot>
            </table>

        
    </fieldset>
     <script>setFilterGrid('table_body',-1);</script>
    <?
	exit();
}

// grey in house production qty popup grey_inhouse_production_popup

if($action=="grey_inhouse_production_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$receive_basis =array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_production_total"],
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
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
	</script>	
	<div style="width:940px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:940px;">
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
			<table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Grey Receive Info</b></th>
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
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:958px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 
					
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and  a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, a.id order by a.id";

                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

						foreach($fab_source_id as $fsid)
						{
							if($fsid==1)
							{
								$row[csf('quantity')] = $row[csf('quantity')];
							}
							else
							{
								$row[csf('quantity')]=0;
							}
						}

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
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? echo $companyArr[$row[csf('knitting_company')]];?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right">Total</th>
                    <th width="80" align="right" id="value_production_total"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>	
        </div>
	</fieldset>   
	<?
	exit();
}

// grey outbound production qty popup
if($action=="grey_outbound_production_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$receive_basis =array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order");

	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>
	<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_receive_qnty_in"],
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
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
	</script>	
	<div style="width:940px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:940px;">
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
			<table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Grey Receive Info</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Receive Id</th>
                    <th width="95">Receive Basis</th>
                    <th width="110">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="60">Machine No</th>
                    <th width="75">Production Date</th>
                    <th width="80">Out Bound Production</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:958px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 
					
                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.po_breakdown_id in($order_id) and  a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, a.id order by a.id";

                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

						foreach($fab_source_id as $fsid)
						{
							if($fsid==1)
							{
								$row[csf('quantity')] = $row[csf('quantity')];
							}
							else
							{
								$row[csf('quantity')]=0;
							}
						}

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
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
                            <td><p><? echo $companyArr[$row[csf('knitting_company')]];?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right">Total</th>
                    <th width="80" align="right" id="value_receive_qnty"><? echo number_format($total_receive_qnty,2,'.',''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>	
        </div>
	</fieldset>   
	<?
	exit();
}

if($action=="grey_fabric_purchase_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$machine_arr = return_library_array("select id, machine_no from lib_machine_name","id","machine_no");
	$receive_basis =array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order",11=>"Service Booking Based");

	?>
	<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_receive_qnty_tot"],
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
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
	</script>	
	<div style="width:940px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:940px;">
		<div id="report_container">
        	
			<table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="10"><b>Grey Receive Info -- (Grey Fabric Purchase)</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">Receive Id</th>
                    <th width="95">Receive Basis</th>
                    <th width="110">Product Details</th>
                    <th width="100">Booking / Program No</th>
                    <th width="60">Machine No</th>
                    <th width="75">Purchase Date</th>
                    <th width="80">Purchase Qty</th>
                    <th width="70">Challan No</th>
                    <th>Kniting Com.</th>
				</thead>
             </table>
             <div style="width:958px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 

                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (22,58) and c.entry_form in (22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and  a.receive_basis not in(9,10) group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.machine_no_id, b.prod_id";

                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

                        if($row[csf('knitting_source')]==1)
                        {
                        	$knitting_company = $companyArr[$row[csf('knitting_company')]];

                        }else{
                        	$knitting_company = $supplierArr[$row[csf('knitting_company')]];
                        }
                        
				
                        $total_receive_qnty+=$row[csf('quantity')];
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="115"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="95"><p><? echo $receive_basis[$row[csf('receive_basis')]]; ?></p></td>
                            <td width="110"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
                            <td width="100"><p><? echo $row[csf('booking_no')]; ?></p></td>
                            <td width="60"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
                            <td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td align="right" width="80"><? echo number_format($row[csf('quantity')],2,'.',''); ?></td>
                            <td width="70"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td><p><? echo $knitting_company;?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="940" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="95">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="75" align="right">Total</th>
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

// grey fabric inhouse issue popup
if($action=="grey_issue_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$sqlWO="select a.fabric_source,b.po_break_down_id,a.booking_no from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.po_break_down_id in ($order_id)";
	 $resultWo=sql_select($sqlWO);
	 $fab_source_ids="";
		foreach($resultWo as $woRow)
		{
			 $fab_source_ids.=$woRow[csf('fabric_source')].',';
		}
		$fab_source=rtrim($fab_source_ids,',');
		$fab_source_id=array_unique(explode(",",$fab_source));
	?>

	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
	</script>	
		<div style="width:705px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
		<fieldset style="width:720px; margin-left:3px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="700" cellpadding="0" cellspacing="0">
	                <thead>
	                	<tr>
	                        <th colspan="7"><b>Grey Issue Info</b></th>
	                    </tr>
	                    <tr>
	                        <th width="40">SL</th>
	                        <th width="120">Issue Id</th>
	                        <th width="100">Issue Purpose</th>
	                        <th width="150">Issue To</th>
	                        <th width="80">Issue Date</th>
	                        <th width="100">Issue Qnty (In)</th>
	                        <th>Issue Qnty (Out)</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:717px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="700" cellpadding="0" cellspacing="0">
	                    <?

						$batch_color_details=return_library_array( "select  id,color_id from pro_batch_create_mst", "id", "color_id");

	                    $i=1; $issue_to='';
	                    $sql="select a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, sum(c.quantity) as quantity from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(16,61) and c.entry_form in(16,61) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.id,  a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no order by a.id";
	                    $result=sql_select($sql);
	        			foreach($result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knit_dye_source')]==1) 
	                        {
	                            $issue_to=$companyArr[$row[csf('knit_dye_company')]]; 
	                        }
	                        else if($row[csf('knit_dye_source')]==3) 
	                        {
	                            $issue_to=$supplierArr[$row[csf('knit_dye_company')]];
	                        }
	                        else
	                            $issue_to="&nbsp;";
								
								foreach($fab_source_id as $fsid)
								{
									if($fsid==1)
									{
										$row[csf('quantity')]=$row[csf('quantity')];
									}
									
								}
	                    
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="40"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
	                            <td width="100"><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
	                            <td width="150"><p><? echo $issue_to; ?></p></td>
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
	                            <th colspan="5" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_issue_qnty,2); ?></th>
	                            <th align="right"><? echo number_format($total_issue_qnty_out,2); ?></th>
	                        </tr>
	                        <tr>
	                            <th colspan="5" align="right">Grand Total</th>
	                            <th align="right" colspan="2">

	                            	<? 
	                            	$grand_total_issue_qty = ($total_issue_qnty+$total_issue_qnty_out);
	                            	echo number_format($total_issue_qnty+$total_issue_qnty_out,2);
	                            	 ?>
	                            </th>
	                        </tr>
	                    </tfoot>
	                </table>
	            </div>	

	            <br>
	            <table border="1" class="rpt_table" rules="all" width="700" cellpadding="0" cellspacing="0">
	                <thead>
	                	<tr>
	                        <th colspan="7"><b>Grey Issue Return Info</b></th>
	                    </tr>
	                    <tr>
	                        <th width="40">SL</th>
	                        <th width="120">Return Id</th>
	                        <th width="100">Return Purpose</th>
	                        <th width="150">Return From</th>
	                        <th width="80">Return Date</th>
	                        <th width="100">Return Qnty (In)</th>
	                        <th>Return Qnty (Out)</th>
	                    </tr>
					</thead>
	             </table>
	             <div style="width:717px; max-height:320px; overflow-y:scroll" id="scroll_body">
	                 <table border="1" class="rpt_table" rules="all" width="700" cellpadding="0" cellspacing="0">
	                    <?

						$batch_color_details=return_library_array( "select  id,color_id from pro_batch_create_mst", "id", "color_id");

	                    $i=1; $return_from='';

	                   $sqlgrey_issue_return = "SELECT a.recv_number,a.receive_basis,a.receive_purpose,a.knitting_source,a.knitting_company,a.receive_date as return_date,sum(c.quantity) as quantity from inv_receive_master a ,pro_grey_prod_entry_dtls b,order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(51,84) and c.entry_form in(51,84) and c.po_breakdown_id in($order_id) and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 group by a.recv_number,a.receive_basis,a.receive_purpose,a.knitting_source,a.knitting_company,a.receive_date";


	                    $return_result=sql_select($sqlgrey_issue_return);
	        			foreach($return_result as $row)
	                    {
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";	
	                    
	                        if($row[csf('knitting_source')]==1) 
	                        {
	                            $return_from=$companyArr[$row[csf('knitting_company')]]; 
	                        }
	                        else if($row['knitting_source']==3) 
	                        {
	                            $return_from=$supplierArr[$row[csf('knitting_company')]];
	                        }
	                        else
	                            $return_from="&nbsp;";
								
								foreach($fab_source_id as $fsid)
								{
									if($fsid==1)
									{
										$row[csf('quantity')]=$row[csf('quantity')];
									}
									
								}
	                    
	                    ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                            <td width="40"><? echo $i; ?></td>
	                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
	                            <td width="100" align="center"><p>Issue Return</p></td>
	                            <td width="150"><p><? echo $return_from;?></p></td>
	                            <td width="80" align="center"><? echo change_date_format($row[csf('return_date')]); ?></td>
	                            <td width="100" align="right">
									<?
	                                    if($row[csf('knitting_source')]!=3)
	                                    {
	                                        echo number_format($row[csf('quantity')],2);
	                                        $total_issue_rtn_qnty+=$row[csf('quantity')];
	                                    }
	                                    else echo "&nbsp;";
	                                ?>
	                            </td>
	                            <td align="right">
	                                <?
	                                    if($row[csf('knitting_source')]==3)
	                                    {
	                                        echo number_format($row[csf('quantity')],2);
	                                        $total_issue_rtn_qnty_out+=$row[csf('quantity')];
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
	                            <th colspan="5" align="right">Total</th>
	                            <th align="right"><? echo number_format($total_issue_rtn_qnty,2); ?></th>
	                            <th align="right"><? echo number_format($total_issue_rtn_qnty_out,2); ?></th>
	                        </tr>
	                        <tr>
	                            <th colspan="5" align="right">Grand Total</th>
	                            <th align="right" colspan="2">
	                            	<? 
	                            	$grand_total_issue_return_qty = ($total_issue_rtn_qnty+$total_issue_rtn_qnty_out);
	                            	echo number_format($total_issue_rtn_qnty+$total_issue_rtn_qnty_out,2); 
	                            	?>
	                            		
	                           </th>
	                        </tr>

	                        <tr>
	                            <th colspan="5" align="right">Balance</th>
	                            <th align="right" colspan="2"><? echo number_format($grand_total_issue_qty-$grand_total_issue_return_qty,2); ?></th>
	                        </tr>

	                    </tfoot>
	                </table>
	            </div>	

	        </div>
		</fieldset>   
	<?
	exit();
}

// in house 
if ($action == "inhouse_yarn_issue_not_required_popup") 
{
    echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $data_arr = explode("**", $data);
    $company_id = $data_arr[0];
    $job_no = $data_arr[1];
  
    $yarn_desc_array = explode(",",chop($data_arr[2],","));


   $sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and a.knit_dye_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2  group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.product_name_details, d.brand_id";
   //echo $sql;
    $result = sql_select($sql);
    ?>
    <script>

        function print_window()
        {

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

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
                <th colspan="9"><b>Yarn Issue</b></th>
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
                </thead>
                <?
                $i = 1;
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $issue_to = $companyArr[$row[csf('knit_dye_company')]];
                   
                    $yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

                    if(!in_array($yarnKey,$yarn_desc_array))
					{
	                    $yarn_issued = $row[csf('issue_qnty')];
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
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
	                                echo number_format($yarn_issued, 2, '.', '');
	                                $total_yarn_issue_qnty += $yarn_issued;
	                            ?>
	                        </td>
	                        
	                    </tr>
	                    <?
	                    $i++;
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
                    <td align="right">Issue Total</td>
                    <td align="right" style="font-weight:bold"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
                </tr>

                <thead>
                <th colspan="9"><b>Yarn Return</b></th>
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
               
                </thead>
                <?
                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
                
                $sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot,c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_type,c.yarn_comp_percent2nd, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2  group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot,c.yarn_type,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_type,c.yarn_comp_percent2nd, c.product_name_details, d.brand_id";

                $return_result = sql_select($sql);

                foreach ($return_result as $row) 
                {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $return_from = $companyArr[$row[csf('knitting_company')]];
                   
                    $yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

                    if(!in_array($yarnKey,$yarn_desc_array))
                    {    
                   	 	$yarn_returned = $row[csf('returned_qnty')];
                   	 	if($yarn_returned>0)
                   	 	{
                     	?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
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
	                                echo number_format($yarn_returned, 2, '.', '');
	                                $total_yarn_return_qnty += $yarn_returned;
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
                    <td align="right">Issue Return Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty, 2, '.', ''); ?></td>
                   
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="8">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty) - ($total_yarn_return_qnty), 2); ?></th>
                    </tr>
                </tfoot>
            </table>	
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "inhouse_yarn_issue_not_required_popup_two") 
{
    echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $data_arr = explode("**", $data);
    $company_id = $data_arr[0];
    $job_no = $data_arr[1];
  
    $yarn_desc_array = explode(",",chop($data_arr[2],","));
	
	
	$sql = "SELECT a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and a.id in ($issue_id) and c.id in ($prod_id) and b.prod_id=c.id and a.knit_dye_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2  group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.product_name_details, d.brand_id";
	
	
    //echo $sql;
    $result = sql_select($sql);
    ?>
    <script>

        function print_window()
        {

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>	
    <div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:970px; margin-left:3px">
        <div id="report_container">
            
            <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="9"><b>Yarn Issue</b></th>
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
                </thead>
                <?
                $i = 1;
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $issue_to = $companyArr[$row[csf('knit_dye_company')]];
                   
                    $yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

                    if(!in_array($yarnKey,$yarn_desc_array))
					{
	                    $yarn_issued = $row[csf('issue_qnty')];
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
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
	                                echo number_format($yarn_issued, 2, '.', '');
	                                $total_yarn_issue_qnty += $yarn_issued;
	                            ?>
	                        </td>
	                        
	                    </tr>
	                    <?
	                    $i++;
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
                    <td align="right">Issue Total</td>
                    <td align="right" style="font-weight:bold"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
                </tr>

                <thead>
                <th colspan="9"><b>Yarn Return</b></th>
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
               
                </thead>
                <?
                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
                
                $sql = "SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot,c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_type,c.yarn_comp_percent2nd, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and a.id in ($rtn_id) and c.id in ($prod_id) and b.prod_id=c.id and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2 group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot,c.yarn_type,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_type,c.yarn_comp_percent2nd, c.product_name_details, d.brand_id";
				//echo $sql;
                $return_result = sql_select($sql);

                foreach ($return_result as $row) 
                {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $return_from = $companyArr[$row[csf('knitting_company')]];
                   
                    $yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

                    if(!in_array($yarnKey,$yarn_desc_array))
                    {    
                   	 	$yarn_returned = $row[csf('returned_qnty')];
                   	 	if($yarn_returned>0)
                   	 	{
                     	?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
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
	                                echo number_format($yarn_returned, 2, '.', '');
	                                $total_yarn_return_qnty += $yarn_returned;
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
                    <td align="right">Issue Return Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty, 2, '.', ''); ?></td>
                   
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="8">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty) - ($total_yarn_return_qnty), 2); ?></th>
                    </tr>
                </tfoot>
            </table>	
        </div>
    </fieldset>  
    <?
    exit();
}

// outbound
if ($action == "outbound_yarn_issue_not_required_popup") 
{
    echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $data_arr = explode("**", $data);
    $company_id = $data_arr[0];
    $job_no = $data_arr[1];
   
    $yarn_desc_array = explode(",",chop($data_arr[2],","));

   $sql = "select a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and a.knit_dye_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2  group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.product_name_details, d.brand_id";
    $result = sql_select($sql);
    ?>
    <script>

        function print_window()
        {

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

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
                <th colspan="9"><b>Yarn Issue</b></th>
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
                </thead>
                <?
                $i = 1;
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $issue_to = $supplierArr[$row[csf('knit_dye_company')]];
                   
                    $yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

                    if(!in_array($yarnKey,$yarn_desc_array))
					{
	                    $yarn_issued = $row[csf('issue_qnty')];
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
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
	                                echo number_format($yarn_issued, 2, '.', '');
	                                $total_yarn_issue_qnty += $yarn_issued;
	                            ?>
	                        </td>
	                        
	                    </tr>
	                    <?
	                    $i++;
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
                    <td align="right">Issue Total</td>
                    <td align="right" style="font-weight:bold"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
                </tr>

                <thead>
                <th colspan="9"><b>Yarn Return</b></th>
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
               
                </thead>
                <?
                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
                
                $sql = "select a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot,c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_type,c.yarn_comp_percent2nd, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and b.po_breakdown_id in ($order_id) and b.prod_id=c.id and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2  group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot,c.yarn_type,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_type,c.yarn_comp_percent2nd, c.product_name_details, d.brand_id";

                $return_result = sql_select($sql);

                foreach ($return_result as $row) 
                {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $return_from = $supplierArr[$row[csf('knitting_company')]];
                   
                    $yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

                    if(!in_array($yarnKey,$yarn_desc_array))
                    {    
                   	 	$yarn_returned = $row[csf('returned_qnty')];
                   	 	if($yarn_returned>0)
                   	 	{
                     	?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
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
	                                echo number_format($yarn_returned, 2, '.', '');
	                                $total_yarn_return_qnty += $yarn_returned;
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
                    <td align="right">Issue Return Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty, 2, '.', ''); ?></td>
                   
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="8">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty) - ($total_yarn_return_qnty), 2); ?></th>
                    </tr>
                </tfoot>
            </table>	
        </div>
    </fieldset>  
    <?
    exit();
}

if ($action == "outbound_yarn_issue_not_required_popup_two") 
{
    echo load_html_head_contents("Report Info", "../../../../", 1, 1, '', '', '');
    extract($_REQUEST);
    $brand_array = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $data_arr = explode("**", $data);
    $company_id = $data_arr[0];
    $job_no = $data_arr[1];
   
    $yarn_desc_array = explode(",",chop($data_arr[2],","));
	
   $sql = "SELECT a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, sum(b.quantity) as issue_qnty, c.lot, c.yarn_type, c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.product_name_details, d.brand_id from inv_issue_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=2 and d.item_category=1 and d.id=b.trans_id and b.trans_type=2 and b.entry_form=3 and a.id in ($issue_id) and c.id in ($prod_id) and b.prod_id=c.id and a.knit_dye_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2  group by a.id, c.id, a.issue_number, a.issue_date, a.challan_no, a.knit_dye_source, a.knit_dye_company, a.booking_no, c.lot, c.yarn_type, c.id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.product_name_details, d.brand_id";
    $result = sql_select($sql);
    ?>
    <script>

        function print_window()
        {

            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
                    '<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

            d.close();
        }

    </script>	
    <div style="width:970px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    <fieldset style="width:970px; margin-left:3px">
        <div id="report_container">
            
            <table border="1" class="rpt_table" rules="all" width="960" cellpadding="0" cellspacing="0">
                <thead>
                <th colspan="9"><b>Yarn Issue</b></th>
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
                </thead>
                <?
                $i = 1;
                foreach ($result as $row) {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $issue_to = $supplierArr[$row[csf('knit_dye_company')]];
                   
                    $yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

                    if(!in_array($yarnKey,$yarn_desc_array))
					{
	                    $yarn_issued = $row[csf('issue_qnty')];
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
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
	                                echo number_format($yarn_issued, 2, '.', '');
	                                $total_yarn_issue_qnty += $yarn_issued;
	                            ?>
	                        </td>
	                        
	                    </tr>
	                    <?
	                    $i++;
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
                    <td align="right">Issue Total</td>
                    <td align="right" style="font-weight:bold"><? echo number_format($total_yarn_issue_qnty, 2, '.', ''); ?></td>
                </tr>

                <thead>
                <th colspan="9"><b>Yarn Return</b></th>
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
               
                </thead>
                <?
                $total_yarn_return_qnty = 0;
                $total_yarn_return_qnty_out = 0;
			
                $sql = "SELECT a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no, sum(b.quantity) as returned_qnty, c.lot,c.id as prod_id,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_type,c.yarn_comp_percent2nd, c.product_name_details, d.brand_id from inv_receive_master a, order_wise_pro_details b, product_details_master c, inv_transaction d where a.id=d.mst_id and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.trans_type=4 and b.entry_form=9 and a.id in ($rtn_id) and c.id in ($prod_id) and b.prod_id=c.id and a.knitting_source=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.issue_purpose!=2  group by a.id, c.id, a.recv_number, a.receive_date, a.challan_no, a.knitting_source, a.knitting_company, a.booking_no,c.lot,c.yarn_type,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_type,c.yarn_comp_percent2nd, c.product_name_details, d.brand_id";

                $return_result = sql_select($sql);

                foreach ($return_result as $row) 
                {
                    if ($i % 2 == 0)
                        $bgcolor = "#E9F3FF";
                    else
                        $bgcolor = "#FFFFFF";

                    $return_from = $supplierArr[$row[csf('knitting_company')]];
                   
                    $yarnKey = $row[csf('yarn_count_id')]."__".$row[csf('yarn_type')]."__".$row[csf('yarn_comp_type1st')]."__".$row[csf('yarn_comp_type2nd')]."__".$row[csf('yarn_comp_percent1st')]."__".$row[csf('yarn_comp_percent2nd')];

                    if(!in_array($yarnKey,$yarn_desc_array))
                    {    
                   	 	$yarn_returned = $row[csf('returned_qnty')];
                   	 	if($yarn_returned>0)
                   	 	{
                     	?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
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
	                                echo number_format($yarn_returned, 2, '.', '');
	                                $total_yarn_return_qnty += $yarn_returned;
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
                    <td align="right">Issue Return Total</td>
                    <td align="right"><? echo number_format($total_yarn_return_qnty, 2, '.', ''); ?></td>
                   
                </tr>
                <tfoot>    
                    <tr>
                        <th align="right" colspan="8">Total Balance</th>
                        <th align="right"><? echo number_format(($total_yarn_issue_qnty) - ($total_yarn_return_qnty), 2); ?></th>
                    </tr>
                </tfoot>
            </table>	
        </div>
    </fieldset>  
    <?
    exit();
}

if($action=="issue_to_cut_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$product_details=return_library_array( "select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", "id", "product_name_details");
	$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0", "id", "batch_no");
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
	</script>	
	<div style="width:920px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:900px; margin-left:7px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="8"><b>Issue To Cutting Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">Issue No</th>
                    <th width="150">Issue to Company</th>
                    <th width="100">Challan No</th>
                    <th width="80">Issue Date</th>
                    <th width="120">Batch No</th>
                    <th width="110">Issue Qnty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:898px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0">
                    <?
					
                    $i=1; $total_issue_to_cut_qnty=0;
                    $sql="select a.issue_number, a.issue_date,a.knit_dye_source,a.knit_dye_company, b.batch_id, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.issue_number, a.issue_date,a.knit_dye_source,a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id";
                    $result=sql_select($sql); 
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

                        if($row[csf('knit_dye_source')]==1)
                        {
                        	$issue_to = $companyArr[$row[csf('knit_dye_company')]];
                        }else{
                        	$issue_to = $supplierArr[$row[csf('knit_dye_company')]];
                        }
                    	

                        $total_issue_to_cut_qnty+=$row[csf('quantity')];
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                            <td width="50" align="center"><? echo $i; ?></td>
                            <td width="120"><p><? echo $row[csf('issue_number')]; ?></p></td>
                            <td width="150"><p><? echo $issue_to; ?></p></td>
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
                        <th colspan="6" align="right">Total Issue</th>
                        <th align="right"><? echo number_format($total_issue_to_cut_qnty,2); ?></th>
                        <th>&nbsp;</th>
                    </tfoot>
                </table>
            </div>

            <br>
            <table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="8"><b>Issue Return Info</b></th>
				</thead>
				<thead>
                	<th width="50">SL</th>
                    <th width="120">Issue Rtn No</th>
                    <th width="150">Issue Ret.Company</th>
                    <th width="100">Challan No</th>
                    <th width="80">Return Date</th>
                    <th width="120">Batch No</th>
                    <th width="110">Return Qty</th>
                    <th>Fabric Description</th>
				</thead>
             </table>
             <div style="width:898px; max-height:320px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="880" cellpadding="0" cellspacing="0">
                    <?
                    $j=1; $total_ret_qnty=0;
                    $sql_ret="select a.recv_number, a.receive_date,a.knitting_source, a.knitting_company, b.batch_id_from_fissuertn, b.prod_id, sum(c.quantity) as quantity, a.challan_no from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in (46,52) and c.entry_form in (46,52) and b.transaction_type in (3,4) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date,a.knitting_source, a.knitting_company, a.challan_no, b.batch_id_from_fissuertn, b.prod_id";
                    $result_ret=sql_select($sql_ret);
        			foreach($result_ret as $row)
                    {
                        if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    
                        $total_ret_qnty+=$row[csf('quantity')];

                        if($row[csf('knitting_source')]==1)
                        {
                        	$return_to = $companyArr[$row[csf('knitting_company')]];
                        }else {
                        	$return_to = $supplierArr[$row[csf('knitting_company')]];
                        }
                        
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trr_<? echo $j; ?>','<? echo $bgcolor;?>')" id="trr_<? echo $j;?>">
                            <td width="50" align="center"><? echo $j; ?></td>
                            <td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="150"><p><? echo $return_to; ?></p></td>
                            <td width="100"><p><? echo $row[csf('challan_no')]; ?></p></td>
                            <td width="80" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="120"><p><? echo $batch_details[$row[csf('batch_id_from_fissuertn')]]; ?></p></td>
                            <td width="110" align="right"><? echo number_format($row[csf('quantity')],2); ?></td>
                            <td><p><? echo $product_details[$row[csf('prod_id')]]; ?></p></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                    <tfoot>
                        <tr>
                            <th colspan="6" align="right">Total Return</th>
                            <th align="right"><? echo number_format($total_ret_qnty,2); ?></th>
                            <th>&nbsp;</th>
                        </tr>
                        <tr>
                            <th colspan="6" align="right">Total Issue to Cut Balance</th>
                            <th align="right"><? $tot_iss_to_cut=$total_issue_to_cut_qnty-$total_ret_qnty; echo number_format($tot_iss_to_cut,2); ?></th>
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

if($action=="finish_fabrcv_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);

	$receive_basis =array(0=>"Independent",1=>"Fabric Booking",2=>"Knitting Plan",4=>"Sales Order",11=>"Service Booking Based");
	$batch_details=return_library_array( "select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0", "id", "batch_no");
	$room_rack_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0", "floor_room_rack_id", "floor_room_rack_name");
	$product_details=return_library_array( "select id, product_name_details from product_details_master where status_active=1 and is_deleted=0", "id", "product_name_details");
	$store_arr =return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0", "id", "store_name");
	?>
	<script>

	var tableFilters = {
						   col_operation: {
						   id: ["value_html_rcv_qty"],
						   col: [9],
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
	'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
		
		$('#tbl_list_search tr:first').show();
	}	
	
	</script>	
	<div style="width:1200px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:1160px;">
		<div id="report_container">
        	
			<table border="1" class="rpt_table" rules="all" width="1150" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="14"><b>Receive Details</b></th>
				</thead>
				<thead>
                	<th width="30">SL</th>
                    <th width="115">System ID</th>
                    <th width="100">Receive Date</th>
                    <th width="100">Dyeing Company</th>
                    <th width="100">Challan No</th>
                    <th width="70">Color</th>
                    <th width="70">Batch No</th>
                    <th width="70">Rack No</th>
                    <th width="70">Grey Used</th>
                    <th width="70">Fin. Rcv. Qty.</th>
                    <th width="70">Process Loss.</th>
                    <th width="70">Fabric Des.</th>
                    <th width="70">GSM</th>
                    <th>F.Dia</th>
				</thead>
             </table>
             <div style="width:1170px; max-height:330px; overflow-y:scroll" id="scroll_body">
                 <table border="1" class="rpt_table" rules="all" width="1150" cellpadding="0" cellspacing="0" id="tbl_list_search">
                    <?
                    $i=1; $total_receive_qnty=0;
					$product_arr=return_library_array( "select id,product_name_details from product_details_master where item_category_id=13",'id','product_name_details'); 

                    $sql="select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.batch_id, b.prod_id, b.gsm, b.width,b.color_id, b.rack_no, b.grey_used_qty as grey_used_qty, sum(c.quantity) as quantity from inv_receive_master a,pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in (37,68) and c.entry_form in (37,68) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.batch_id, b.prod_id, b.gsm,b.width,b.color_id,b.rack_no, b.grey_used_qty";

                    /*
                  	$sql_fin_recv="select po_breakdown_id,
					sum(CASE WHEN c.entry_form ='7' THEN c.quantity ELSE 0 END) AS finish_production,
					sum(CASE WHEN c.entry_form ='66' THEN c.quantity ELSE 0 END) AS finish_production_rollwise,
					sum(CASE WHEN c.entry_form ='37' THEN c.quantity ELSE 0 END) AS finish_receive,
					sum(CASE WHEN c.entry_form ='68' THEN c.quantity ELSE 0 END) AS finish_receive_rollwise,
					sum(CASE WHEN c.entry_form ='18' THEN c.quantity ELSE 0 END) AS finish_issue,
					sum(CASE WHEN c.entry_form ='71' THEN c.quantity ELSE 0 END) AS finish_issue_roll_wise,
					sum(CASE WHEN c.entry_form ='46' and c.trans_type=3 THEN c.quantity ELSE 0 END) AS recv_rtn_qnty,
					sum(CASE WHEN c.entry_form ='52' and c.trans_type=4 THEN c.quantity ELSE 0 END) AS iss_retn_qnty,
					sum(CASE WHEN c.entry_form ='15' and c.trans_type=5 THEN c.quantity ELSE 0 END) AS transfer_in_qnty,
					sum(CASE WHEN c.entry_form ='15' and c.trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty
					from order_wise_pro_details c where c.status_active=1 and c.is_deleted=0 and c.entry_form in(7,15,18,37,46,52,66,68,71) $po_id_cond  group by c.po_breakdown_id";
					*/

                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

                        if($row[csf('knitting_source')]==1)
                        {
                        	$knitting_company = $companyArr[$row[csf('knitting_company')]];

                        }else{
                        	$knitting_company = $supplierArr[$row[csf('knitting_company')]];

                        }
                        
                        $total_receive_qnty+=$row[csf('quantity')];

                        $process_loss = 100 - (($row[csf('quantity')]/$row[csf('grey_used_qty')])*100);
                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                          	<td width="30" align="center"><? echo $i; ?></td>
		                    <td width="115"><? echo $row[csf('recv_number')]; ?></td>
		                    <td width="100"><? echo change_date_format($row[csf('receive_date')]); ?></td>
		                    <td width="100"><? echo $knitting_company; ?></td>
		                    <td width="100"><? echo $row[csf('challan_no')]; ?></td>
		                    <td width="70"><? echo $color_arr[$row[csf('color_id')]];?></td>
		                    <td width="70"><? echo $batch_details[$row[csf('batch_id')]]; ?></td>
		                    <td width="70"><? echo $room_rack_arr[$row[csf('rack_no')]]; ?></td>
		                    <td width="70"><? echo number_format($row[csf('grey_used_qty')],2);?></td>
		                    <td width="70"><? echo number_format($row[csf('quantity')],2); ?></td>
		                    <td width="70"><? echo number_format($process_loss,2);?></td>
		                    <td width="70"><? echo $product_details[$row[csf('prod_id')]]; ?></td>
		                    <td width="70"><? echo $row[csf('gsm')]; ?></td>
		                    <td> <? echo $row[csf('width')]; ?> </td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                 </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="1150" cellpadding="0" cellspacing="0">  
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="115">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">Total</th>
                    <th width="70" id="value_html_rcv_qty"><? echo number_format($total_receive_qnty,2); ?></th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th>&nbsp;</th>
                </tfoot>
            </table>
            <table border="1" class="rpt_table" rules="all" width="900" cellpadding="0" cellspacing="0">
				<thead>
                	<tr>
                    	<th colspan="9">Receive Return </th>
                    </tr>
                    <tr>
                    	<th width="20">SL</th>
                        <th width="115">Recieve Rtn No</th>
                        <th width="60">Return Date</th>
                        <th width="115">Recieve No</th>
                        <th width="100">Store</th>
                        <th width="100">Batch No</th>
                        <th width="170">Item Description</th>
                        <th width="100">Color</th>
                        <th>Return Qty</th>
                    </tr>
				</thead>
                <?
                $total_receive_return_qnty=0;
				
				$sql="select a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, sum(c.quantity) as quantity, d.product_name_details,c.color_id from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d, inv_receive_master e where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and e.id=a.received_id and a.entry_form in (46) and c.entry_form in (46) and c.po_breakdown_id in($order_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.issue_number, a.issue_date, a.received_mrr_no, b.store_id, b.pi_wo_batch_no, b.prod_id, d.product_name_details,c.color_id";
                $result=sql_select($sql);
				foreach($result as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
                    ?>
                     <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                    	<td width="20"><? echo $i; ?></td>
                    	<td width="115"><? echo $row[csf('issue_number')]; ?></td>
                        <td width="60" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
                        <td width="115"><? echo $row[csf('received_mrr_no')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $batch_details[$row[csf('pi_wo_batch_no')]]; ?></td>
                        <td width="170" style="word-break:break-all"><? echo $row[csf('product_name_details')]; ?></td>
                        <td width="100" style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
                        
                        <td align="right"><? echo number_format($row[csf('quantity')],2); ?> </td>
                    </tr>
                <?
					$total_receive_return_qnty+=$row[csf('quantity')];
                	$i++;
                }
                ?>
                <tr style="font-weight:bold">
                    <td colspan="8" align="right">Total Return:</td>
                    <td align="right"><? echo number_format($total_receive_return_qnty,2); ?></td>
                </tr>
                <tfoot>
                    <th colspan="8" align="right">Net Receive :</th>
                    <th><? echo number_format(($total_receive_qnty-$total_receive_return_qnty),2);?></th>
                </tfoot>
            </table>	
        </div>
	</fieldset>   
	<?
	exit();
}
?>
<? 
header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.fabrics.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action==='load_drop_down_buyer')
{
	echo create_drop_down( 'cbo_buyer_name', 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name",'id,buyer_name', 1, '-- All Buyer --', $selected, '' );
	exit();
}


if($action==='report_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name = str_replace("'",'',$cbo_company_name);
	$cbo_buyer_name   = str_replace("'",'',$cbo_buyer_name);
	$cbo_job_year     = str_replace("'",'',$cbo_job_year);
	$txt_job_no       = trim(str_replace("'",'',$txt_job_no));
	$txt_style_no     = trim(str_replace("'",'',$txt_style_no));
	$cbo_report_type  = str_replace("'",'',$cbo_report_type);
	$fabric_nature    = str_replace("'",'',$cbo_fabric_nature);
	$txt_date_from    = str_replace("'",'',$txt_date_from);
	$txt_date_to      = str_replace("'",'',$txt_date_to);

	$company_arr=return_library_array("select id, company_name from lib_company where status_active=1", 'id','company_name');
	$supplier_arr=return_library_array("select id, supplier_name from lib_supplier where status_active=1", 'id','supplier_name');
	$buyer_name_arr=return_library_array("select id, short_name from lib_buyer where status_active=1", 'id','short_name');
	
	$job_no=$style_no='';
	if ($txt_job_no != '') $job_no=" and a.job_no_prefix_num=$txt_job_no";
	if ($txt_style_no != '') $style_no=" and a.style_ref_no like '$txt_style_no%'";
	
	$company_cond=$buyer_cond=$fabric_nature_cond='';
	if ($cbo_company_name > 0) $company_cond=" and a.company_name=$cbo_company_name";
	if ($cbo_buyer_name > 0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";

	
	$fabric_nature_cond = ($fabric_nature !=0) ? " and e.fab_nature_id=$fabric_nature" : "";
	
	if ($cbo_report_type==1)
	{
		if($db_type==2 || $db_type==1) $select_year="to_char(a.insert_date,'YYYY')"; else $select_year="year(a.insert_date)";
		if ($cbo_job_year > 0) $year_cond =" and $select_year='$cbo_job_year'";
	}
	else
	{
		if($db_type==2 || $db_type==1) $select_year="to_char(c.insert_date,'YYYY')"; else $select_year="year(c.insert_date)";
		if ($cbo_job_year > 0) $year_cond =" and $select_year='$cbo_job_year'";
	}		
	

	if ($cbo_report_type == 2)
	{
		if($txt_date_from != '' && $txt_date_to != '' || $txt_job_no != '' || $txt_style_no != '')
		{
			if ($txt_job_no != '' || $txt_style_no != '')
			{
				$date_cond = '';

				// ========================= getting full shipment job ====================================
				$sqlEx="SELECT a.id as JOB_ID,a.JOB_NO, c.PO_BREAK_DOWN_ID, c.EX_FACTORY_DATE from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_delivery_mst d,wo_pre_cost_fabric_cost_dtls e where a.id=b.job_id and a.id=e.job_id and b.id=c.po_break_down_id and c.delivery_mst_id=d.id and d.company_id='$cbo_company_name' and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and b.shiping_status=3 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $date_cond $job_no $style_no $buyer_cond $year_cond $fabric_nature_cond group by a.id,a.job_no, c.po_break_down_id, c.ex_factory_date order by a.job_no, c.ex_factory_date";
				// echo $sqlEx;
				$sqlEx_res=sql_select($sqlEx);
				$tot_rows=0; $jobNos='';
				$ex_factory_date_arr=array();
				$fullship_job_id_arr = array();
				foreach($sqlEx_res as $row)
				{
					$tot_rows++;
					$jobNos.="'".$row['JOB_NO']."',";
					$ex_factory_date_arr[$row['JOB_NO']]['EX_FACTORY_DATE'] = $row['EX_FACTORY_DATE'];
					$fullship_job_id_arr[$row['JOB_ID']] = $row['JOB_ID'];
				}
				unset($sqlEx_res);
				if ($jobNos != '')
				{
					$jobNos = array_flip(array_flip(explode(',', rtrim($jobNos,','))));
					$job_no_cond = '';

					if($db_type==2 && $tot_rows>1000)
					{
						$job_no_cond = ' and (';
						$jobNoArr = array_chunk($jobNos,999);
						foreach($jobNoArr as $jobs)
						{
							$jobs = implode(',',$jobs);
							$job_no_cond .= " a.job_no in($jobs) or ";
						}
						$job_no_cond = rtrim($job_no_cond,'or ');
						$job_no_cond .= ')';
					}
					else
					{
						$jobNos = implode(',', $jobNos);
						$job_no_cond=" and a.job_no in ($jobNos)";
					}
				}
				else
				{
					echo '<div style="width:1000px; background-color:#8FB5E3; margin:0 auto; text-align:center; color:red;">Data Not Found!</div>';
					die();
				}
			}
			else 
			{
				$date_cond =" and c.ex_factory_date between '$txt_date_from' and '$txt_date_to'";			

				// ========================= getting full shipment job ====================================
				$sqlEx="SELECT a.id as JOB_ID,a.JOB_NO, c.PO_BREAK_DOWN_ID, c.EX_FACTORY_DATE from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_delivery_mst d,wo_pre_cost_fabric_cost_dtls e where a.id=b.job_id and a.id=e.job_id and b.id=c.po_break_down_id and c.delivery_mst_id=d.id and d.company_id='$cbo_company_name' and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and b.shiping_status=3 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $date_cond $job_no $style_no $buyer_cond $year_cond $fabric_nature_cond group by a.id,a.job_no, c.po_break_down_id, c.ex_factory_date order by a.job_no, c.ex_factory_date";
				// echo $sqlEx;
				$sqlEx_res=sql_select($sqlEx);
				$tot_rows=0; $jobNos='';
				$ex_factory_date_arr=array();
				$fullship_job_id_arr = array();
				foreach($sqlEx_res as $row)
				{
					$tot_rows++;
					$jobNos.="'".$row['JOB_NO']."',";
					$ex_factory_date_arr[$row['JOB_NO']]['EX_FACTORY_DATE'] = $row['EX_FACTORY_DATE'];
					$fullship_job_id_arr[$row['JOB_ID']] = $row['JOB_ID'];
				}
				unset($sqlEx_res);

				// ========================= getting non full shipment job ====================================
				$sqlEx="SELECT a.id as JOB_ID,a.JOB_NO, c.PO_BREAK_DOWN_ID, c.EX_FACTORY_DATE from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_delivery_mst d,wo_pre_cost_fabric_cost_dtls e where a.id=b.job_id and a.id=e.job_id and b.id=c.po_break_down_id and c.delivery_mst_id=d.id and d.company_id='$cbo_company_name' and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and b.shiping_status!=3 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $date_cond $job_no $style_no $buyer_cond $year_cond $fabric_nature_cond group by a.id,a.job_no, c.po_break_down_id, c.ex_factory_date order by a.job_no, c.ex_factory_date";
				// echo $sqlEx;
				$sqlEx_res=sql_select($sqlEx);
				$non_ship_job_id_arr = array();
				foreach($sqlEx_res as $row)
				{
					$non_ship_job_id_arr[$row['JOB_ID']] = $row['JOB_ID'];
				}

				$remainFullArr =  array_diff($fullship_job_id_arr, $non_ship_job_id_arr);
				// echo "<pre>"; print_r($remainFullArr);die();
				$job_ids = implode(",",$remainFullArr);

				// ========================= getting max ex-factory date ====================================
				$maxDateChkRes = sql_select("SELECT a.job_no,a.id as job_id, max(c.ex_factory_date) ex_factory_date
				from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
				where a.id = b.job_id and b.id = c.po_break_down_id and b.status_active = 1 and c.status_active=1 and b.shiping_status = 3 and a.id in ($job_ids) group by a.job_no,a.id");

				foreach ($maxDateChkRes as $md)
				{
					$maxDateChkArr[$md[csf("job_no")]]["ex_date"]= $md[csf("ex_factory_date")];
				}
				// ========================= =================== ====================================
				$sql_res_2 = sql_select("SELECT a.id,a.job_no from wo_po_details_master a where a.status_active = 1 and a.is_deleted = 0 and a.id in ($job_ids)");

				$job_id_array = array();
				foreach ($sql_res_2 as $val)
				{
					if(strtotime($maxDateChkArr[$val[csf("job_no")]]["ex_date"]) <= strtotime($txt_date_to))
					{	
						$job_id_array[$val[csf("id")]] = $val[csf("id")];
					}
				}

				// echo "<pre>"; print_r($job_id_array);die();
							
				if (count($job_id_array)!=0)
				{
					$job_no_cond = '';

					if($db_type==2 && count($job_id_array)>1000)
					{
						$job_no_cond = ' and (';
						$jobNoArr = array_chunk($job_id_array,999);
						foreach($jobNoArr as $jobs)
						{
							$jobs = implode(',',$jobs);
							$job_no_cond .= " a.id in($jobs) or ";
						}
						$job_no_cond = rtrim($job_no_cond,'or ');
						$job_no_cond .= ')';
					}
					else
					{
						$jobNos = implode(',', $job_id_array);
						$job_no_cond=" and a.id in ($jobNos)";
					}
				}
				else
				{
					echo '<div style="width:1000px; background-color:#8FB5E3; margin:0 auto;padding:8px 0; text-align:center; color:red;">Data Not Found!</div>';
					die();
				}
			}
		}
	}	
	//echo '<pre>';print_r($ex_factory_date_arr);
	// echo $job_no_cond;die;
	//$is_auto_allocation_from_requisition = return_field_value("auto_allocate_yarn_from_requis", "variable_settings_production", "company_name=$cbo_company_name and variable_list=6 and status_active=1 and is_deleted=0", "auto_allocate_yarn_from_requis");

	/*if ($is_auto_allocation_from_requisition != 1)
	{

	}*/		


	if ($cbo_report_type==1) $year_cond=$year_cond; else $year_cond='';
	$sql_main="SELECT a.ID, a.JOB_NO_PREFIX_NUM, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.PUB_SHIPMENT_DATE, b.id as PO_ID, b.PO_NUMBER, b.SHIPING_STATUS, sum(b.po_quantity*a.total_set_qnty) as ORDER_QTY, b.plan_cut as PLAN_CUT_QTY, sum(b.plan_cut*a.total_set_qnty) as PLAN_CUT_QTY_PCS, avg(b.excess_cut) as PLAN_CUT_PERCENT
		from wo_po_details_master a, wo_po_break_down b , wo_pre_cost_fabric_cost_dtls e
		where a.id=b.job_id and a.id=e.job_id and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $company_cond $buyer_cond $fabric_nature_cond $year_cond $job_no $style_no $job_no_cond 
		group by a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.pub_shipment_date, b.id, b.po_number, b.shiping_status, b.po_quantity, b.plan_cut 
		order by b.pub_shipment_date";
	// status 1=Active, 2=Inactive, 3=Cancelled	
	// echo $sql_main;die();
	$sql_main_res=sql_select($sql_main);
	$job_alldata_arr=array();$tot_rows=0;
	foreach($sql_main_res as $row)
	{
		$tot_rows++;
		$job_alldata_arr[$row['JOB_NO']]['COMPANY_NAME'] = $row['COMPANY_NAME'];
		$job_alldata_arr[$row['JOB_NO']]['BUYER_NAME']   = $row['BUYER_NAME'];
		$job_alldata_arr[$row['JOB_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
		$job_alldata_arr[$row['JOB_NO']]['PO_ID'] .= $row['PO_ID'].',';
		$job_alldata_arr[$row['JOB_NO']]['PUB_SHIPMENT_DATE'] = $row['PUB_SHIPMENT_DATE'];
		$job_alldata_arr[$row['JOB_NO']]['SHIPING_STATUS'] .= $row['SHIPING_STATUS'].',';
		// $job_alldata_arr[$row['JOB_NO']]['ORDER_QTY'] += $row['ORDER_QTY'];
		// $job_alldata_arr[$row['JOB_NO']]['PLAN_CUT_QTY'] += $row['PLAN_CUT_QTY'];
		// $job_alldata_arr[$row['JOB_NO']]['PLAN_CUT_QTY_PCS'] += $row['PLAN_CUT_QTY_PCS'];
		// $job_alldata_arr[$row['JOB_NO']]['PLAN_CUT_PERCENT'] = $row['PLAN_CUT_PERCENT'];
		$po_wise_job_arr[$row['PO_ID']] = $row['JOB_NO'];
		$jobNo .= "'".$row['JOB_NO']."',";
		$poIds .= $row['PO_ID'].',';
		$jobIds .= $row['ID'].',';
	}	
	unset($sql_main_res);
	//echo '<pre>';print_r($job_alldata_arr);

	$sql_main="SELECT a.ID, a.JOB_NO_PREFIX_NUM, a.JOB_NO, a.COMPANY_NAME, a.BUYER_NAME, a.STYLE_REF_NO, b.PUB_SHIPMENT_DATE, b.id as PO_ID, b.PO_NUMBER, b.SHIPING_STATUS, sum(b.po_quantity*a.total_set_qnty) as ORDER_QTY, b.plan_cut as PLAN_CUT_QTY, sum(b.plan_cut*a.total_set_qnty) as PLAN_CUT_QTY_PCS, avg(b.excess_cut) as PLAN_CUT_PERCENT
		from wo_po_details_master a, wo_po_break_down b
		where a.id=b.job_id and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 $company_cond $buyer_cond $year_cond $job_no $style_no $job_no_cond 
		group by a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.pub_shipment_date, b.id, b.po_number, b.shiping_status, b.po_quantity, b.plan_cut 
		order by b.pub_shipment_date";
	// status 1=Active, 2=Inactive, 3=Cancelled	
	// echo $sql_main;die();
	$sql_main_res=sql_select($sql_main);
	foreach($sql_main_res as $row)
	{
		
		$job_alldata_arr[$row['JOB_NO']]['ORDER_QTY'] += $row['ORDER_QTY'];
		$job_alldata_arr[$row['JOB_NO']]['PLAN_CUT_QTY'] += $row['PLAN_CUT_QTY'];
		$job_alldata_arr[$row['JOB_NO']]['PLAN_CUT_QTY_PCS'] += $row['PLAN_CUT_QTY_PCS'];
		$job_alldata_arr[$row['JOB_NO']]['PLAN_CUT_PERCENT'] = $row['PLAN_CUT_PERCENT'];
	}	
	unset($sql_main_res);
	//echo '<pre>';print_r($job_alldata_arr);

	$asc_date_data_arr = array();
	foreach ($job_alldata_arr as $jobkey => $value) 
	{
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['COMPANY_NAME']=$value['COMPANY_NAME'];
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['BUYER_NAME']=$value['BUYER_NAME'];
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['STYLE_REF_NO']=$value['STYLE_REF_NO'];
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['PUB_SHIPMENT_DATE']=$value['PUB_SHIPMENT_DATE'];
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['PO_ID']=$value['PO_ID'];
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['SHIPING_STATUS']=$value['SHIPING_STATUS'];

		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['ORDER_QTY']=$value['ORDER_QTY'];
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['PLAN_CUT_QTY']=$value['PLAN_CUT_QTY'];
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['PLAN_CUT_QTY_PCS']=$value['PLAN_CUT_QTY_PCS'];
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['PLAN_CUT_PERCENT']=$value['PLAN_CUT_PERCENT'];		
		$asc_date_data_arr[strtotime($ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'])][$jobkey]['DATE']=$ex_factory_date_arr[$jobkey]['EX_FACTORY_DATE'];
	}
	ksort($asc_date_data_arr);
	// echo "<pre>";print_r($asc_date_data_arr);die;


	$jobIds = chop($jobIds,",");
	$condition= new condition();     
    $condition->jobid_in($jobIds);     
    $condition->init();
    //$costPerArr=$condition->getCostingPerArr();
    $yarn= new yarn($condition);
    //echo $yarn->getQuery();die;
    $yarn_req_arr=$yarn->getJobWiseYarnQtyArray();


	$fabric= new fabric($condition);
	//echo $fabric->getQuery();die;

    $fabric_req_arr = $fabric->getQtyArray_by_job_knitAndwoven_greyAndfinish();
    //echo "<pre>";
    //print_r($fabric_req_arr["knit"]["finish"]);die;
    //getJobWiseYarnQtyArray





	// Job No Condition
	if ($jobNo != '')
	{
		$jobNo = array_flip(array_flip(explode(',', rtrim($jobNo,','))));
		$job_no_cond2 = '';
		$job_no_cond3 = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$job_no_cond2 = ' and (';
			$job_no_cond3 = ' and (';
			$jobNoArr = array_chunk($jobNo,999);
			foreach($jobNoArr as $jobs)
			{
				$jobs = implode(',',$jobs);
				$job_no_cond2 .= " a.job_no in($jobs) or ";
				$job_no_cond3 .= " d.po_job_no in($jobs) or ";
			}
			$job_no_cond2 = rtrim($job_no_cond2,'or ');
			$job_no_cond2 .= ')';
			$job_no_cond3 = rtrim($job_no_cond2,'or ');
			$job_no_cond3 .= ')';
		}
		else
		{
			$jobNo = implode(',', $jobNo);
			$job_no_cond2=" and a.job_no in ($jobNo)";
			$job_no_cond3=" and d.po_job_no in ($jobNo)";
		}
	}

	// po id condition
	if ($poIds != '')
	{
		$poIds = rtrim($poIds,',');
		$po_id_cond = '';
		$po_id_cond2 = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$po_id_cond = ' and (';
			$po_id_cond2 = ' and (';		
			$poIdArr = array_chunk(explode(',',$poIds),999);
			foreach($poIdArr as $ids)
			{
				$ids = implode(',',$ids);
				$po_id_cond .= " c.po_breakdown_id in($ids) or ";
				$po_id_cond2 .= " c.po_break_down_id in($ids) or ";
			}
			$po_id_cond = rtrim($po_id_cond,'or ');
			$po_id_cond .= ')';
			$po_id_cond2 = rtrim($po_id_cond2,'or ');
			$po_id_cond2 .= ')';
		}
		else
		{
			$po_id_cond = " and c.po_breakdown_id in ($poIds)";
			$po_id_cond2 = " and c.po_break_down_id in ($poIds)";
		}
	}
	
	// SQL fabric booking
	$sql_FabBooking="SELECT c.JOB_NO, 
		sum(CASE WHEN c.booking_type=1 and c.is_short=1 THEN c.fin_fab_qnty ELSE 0 END) AS FIN_FAB_QNTY_SHORT,
		sum(CASE WHEN c.booking_type=1 and c.is_short=1 THEN c.grey_fab_qnty ELSE 0 END) AS GREY_FAB_QNTY_SHORT,
		sum(CASE WHEN c.booking_type=1 THEN c.fin_fab_qnty ELSE 0 END) AS FIN_FAB_QNTY,
		sum(CASE WHEN c.booking_type=1 THEN c.grey_fab_qnty ELSE 0 END) AS GREY_FAB_QNTY,
		sum(CASE WHEN c.booking_type=4 THEN c.grey_fab_qnty ELSE 0 END) AS SAM_BOOKING_GREY_QNTY,
		sum(CASE WHEN c.booking_type=4 THEN c.fin_fab_qnty ELSE 0 END) AS SAM_BOOKING_FIN_QNTY
		from wo_booking_dtls c
		where c.booking_type in(1,4) and c.status_active=1 and c.is_deleted=0 $po_id_cond2
		group by c.job_no
		";
	$sql_FabBooking_res=sql_select($sql_FabBooking);
	$fabBooking_arr=array();
	foreach($sql_FabBooking_res as $row)
	{		
		$fabBooking_arr[$row['JOB_NO']]['FIN_FAB_QNTY_SHORT'] = $row['FIN_FAB_QNTY_SHORT'];
		$fabBooking_arr[$row['JOB_NO']]['GREY_FAB_QNTY_SHORT']= $row['GREY_FAB_QNTY_SHORT'];
		$fabBooking_arr[$row['JOB_NO']]['FIN_FAB_QNTY']  = $row['FIN_FAB_QNTY'];
		$fabBooking_arr[$row['JOB_NO']]['GREY_FAB_QNTY'] = $row['GREY_FAB_QNTY'];
		$fabBooking_arr[$row['JOB_NO']]['SAM_BOOKING_GREY_QNTY'] = $row['SAM_BOOKING_GREY_QNTY'];
		$fabBooking_arr[$row['JOB_NO']]['SAM_BOOKING_FIN_QNTY'] = $row['SAM_BOOKING_FIN_QNTY'];
	}	
	unset($sql_FabBooking_res);

	// AVG Fin Gray Con Process Loss SQL	
	$sql_avgFinGrayCon="SELECT c.JOB_NO, c.PRE_COST_FABRIC_COST_DTLS_ID, avg(c.cons) as AVG_FINISH_CONS, avg(c.requirment) as AVG_GRAY_CONS
		from wo_pre_cos_fab_co_avg_con_dtls c 
        where c.status_active=1 and c.is_deleted=0 $po_id_cond2 
        group by c.job_no, c.pre_cost_fabric_cost_dtls_id";
	$sql_avgFinGrayCon_res=sql_select($sql_avgFinGrayCon);
	$avgFinGrayCon_arr=array();
	foreach($sql_avgFinGrayCon_res as $row)
	{		
		$avgFinGrayCon_arr[$row['JOB_NO']]['AVG_GRAY_CONS'] += $row['AVG_GRAY_CONS'];
		$avgFinGrayCon_arr[$row['JOB_NO']]['AVG_FINISH_CONS'] += $row['AVG_FINISH_CONS'];
	}	
	unset($sql_avgFinGrayCon_res);
	//echo '<pre>';print_r($avgFinGrayConProcess_arr);
	// SQL Yarn Allocation
	$sql_alloc = "SELECT a.JOB_NO, a.BOOKING_NO, d.KNIT_ID, d.PROD_ID, d.YARN_QNTY 
		from wo_booking_mst a, ppl_planning_info_entry_mst b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d 
		where a.booking_no=b.booking_no and b.id=c.mst_id and c.id=d.knit_id $job_no_cond2 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	$sql_alloc_res=sql_select($sql_alloc);
	$yarnAllocation_arr=array();
	foreach($sql_alloc_res as $row)
	{		
		$yarnAllocation_arr[$row['JOB_NO']]['YARN_QNTY'] += $row['YARN_QNTY'];
	}
	unset($sql_alloc_res);

	// SQL Issue and issue return
	$sql_yarn_issue ="SELECT c.PO_BREAKDOWN_ID, c.PROD_ID, 
		sum(CASE WHEN c.entry_form=3 and c.trans_type=2 THEN c.quantity ELSE 0 END) AS ISSUE_QNTY
		from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
		where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.company_id=$cbo_company_name and b.transaction_type=2 and b.item_category=1 and c.entry_form=3 and c.trans_type=2 and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and c.issue_purpose!=2 $po_id_cond
		group by c.prod_id, c.po_breakdown_id";
	$sql_yarn_issue_res=sql_select($sql_yarn_issue);
	$yarnIssueReturn_arr=array();
	foreach ($sql_yarn_issue_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAKDOWN_ID']];
		$yarnIssueReturn_arr[$jobno]['ISSUE_QNTY'] += $row['ISSUE_QNTY'];
		$yarnIssue_prodId[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
	}
	unset($sql_yarn_issue_res);
	//echo '<pre>';print_r($yarnIssue_prodId);

	$sql_yarn_iss_rtn ="SELECT c.PO_BREAKDOWN_ID, c.PROD_ID, 
		sum(CASE WHEN c.entry_form=9 and c.trans_type=4 THEN c.quantity ELSE 0 END) AS ISSUE_RTN_QTY
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
		where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.company_id=$cbo_company_name and b.transaction_type=4 and b.item_category=1 and c.entry_form=9 and c.trans_type=4 and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 and c.issue_purpose!=2 $po_id_cond
		group by c.prod_id, c.po_breakdown_id";
	$sql_yarn_iss_rtn_res=sql_select($sql_yarn_iss_rtn);
	foreach ($sql_yarn_iss_rtn_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAKDOWN_ID']];
		$yarnIssueReturn_arr[$jobno]['ISSUE_RTN_QTY'] += $row['ISSUE_RTN_QTY'];
		$yarnIssueReturn_prodId[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
	}
	unset($sql_yarn_iss_rtn_res);

	// SQL yarn transfer
	$sql_yarn_transfer ="SELECT a.JOB_NO, c.FROM_PROD_ID as PROD_ID, a.PO_BREAK_DOWN_ID, sum(c.transfer_qnty) as YARN_TRANSFER_QNTY 
	from wo_booking_mst a, fabric_sales_order_mst b, inv_item_transfer_dtls c, inv_item_transfer_mst d
	where a.booking_no=b.sales_booking_no and b.job_no=c.fso_no and c.mst_id=d.id and c.item_category=1 and c.to_trans_id=0 $job_no_cond2 and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.job_no, c.from_prod_id, a.po_break_down_id";
	// echo $sql_yarn_transfer;die;
	$sql_yarn_transfer_res=sql_select($sql_yarn_transfer);
	$yarnTransfer_arr=array();
	foreach ($sql_yarn_transfer_res as $row)
	{
		$yarnTransfer_arr[$row['JOB_NO']]['YARN_TRANSFER_QNTY'] += $row['YARN_TRANSFER_QNTY'];
		$yarnTransfer_prodId[$row['JOB_NO']]['PROD_ID'] .= $row['PROD_ID'].',';
	}
	unset($sql_yarn_transfer_res);
	// echo "<pre>";print_r($yarnTransfer_arr);die;
	//echo '<pre>';print_r($yarnTransfer_prodId);

	$sql_grayFabRecQty="SELECT c.PO_BREAKDOWN_ID, c.PROD_ID,
		sum(CASE WHEN c.entry_form in (22,58) and c.trans_type=1 THEN c.quantity ELSE 0 END) AS GREY_FAB_REC_QTY 
		from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$cbo_company_name and a.entry_form in (22,58) and c.entry_form in (22,58) and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_id_cond 
		group by c.po_breakdown_id, c.prod_id";
	$sql_grayFabRecQty_res=sql_select($sql_grayFabRecQty);
	$grayFabRecQty_res_arr=array();
	foreach ($sql_grayFabRecQty_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAKDOWN_ID']];
		$grayFabRecQty_res_arr[$jobno]['GREY_FAB_REC_QTY'] += $row['GREY_FAB_REC_QTY'];
		$grayFabRecQty_prodId[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
	}
	unset($sql_grayFabRecQty_res);
	//echo '<pre>';print_r($grayFabRecQty_res_arr);

	/*$sql_greyFabRollIssue="SELECT c.PO_BREAKDOWN_ID, c.PROD_ID,
		sum(CASE WHEN c.entry_form in(16,61) and c.trans_type=2 THEN c.quantity ELSE 0 END) AS GREY_ISSUE_ROLLWISE
		from order_wise_pro_details c 
		where c.trans_type=2 and c.status_active=1 and c.is_deleted=0 $po_id_cond 
		group by c.po_breakdown_id, c.prod_id";
	$sql_greyFabRollIssue_res=sql_select($sql_greyFabRollIssue);
	$greyFabRollIssue_arr=array();		
	foreach($sql_greyFabRollIssue_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAKDOWN_ID']];
		$greyFabRollIssue_arr[$jobno]['GREY_ISSUE_ROLLWISE'] += $row['GREY_ISSUE_ROLLWISE'];
		$greyFabRollIssue_prodId[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
	}*/

	$sql_greyFabRollIssue="SELECT c.PO_BREAKDOWN_ID, c.PROD_ID,c.TRANS_TYPE,
		sum(CASE WHEN c.entry_form in(16,61) and c.trans_type=2 THEN c.quantity ELSE 0 END) AS GREY_ISSUE_ROLLWISE,
		sum(CASE WHEN c.entry_form in(83,82,183) and c.trans_type=5 THEN c.quantity ELSE 0 END) AS GREY_TRANSIN,
		sum(CASE WHEN c.entry_form in(83,82,110) and c.trans_type=6 THEN c.quantity ELSE 0 END) AS GREY_TRANSOUT
		from order_wise_pro_details c 
		where c.trans_type in (2,5,6) and c.status_active=1 and c.is_deleted=0 $po_id_cond 
		group by c.po_breakdown_id, c.prod_id, c.TRANS_TYPE";
	$sql_greyFabRollIssue_res=sql_select($sql_greyFabRollIssue);
	$greyFabRollIssue_arr=array();		
	foreach($sql_greyFabRollIssue_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAKDOWN_ID']];
		if($row['TRANS_TYPE'] == 2){
			$greyFabRollIssue_arr[$jobno]['GREY_ISSUE_ROLLWISE'] += $row['GREY_ISSUE_ROLLWISE'];
			$greyFabRollIssue_prodId[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
		}
		else if($row['TRANS_TYPE'] == 5)
		{
			$greyFabTransIn_arr[$jobno]['GREY_TRANSIN'] += $row['GREY_TRANSIN'];
			$greyFabTransIn_prodId[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
		}
		else if($row['TRANS_TYPE'] == 6)
		{
			$greyFabTransIn_arr[$jobno]['GREY_TRANSOUT'] += $row['GREY_TRANSOUT'];
			$greyFabTransIn_prodId[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
		}
	}


	unset($sql_greyFabRollIssue_res);
	//echo '<pre>';print_r($greyFabRollIssue_prodId);

	// Finish Fabric receive
	$sql_finishRecGreyUsedQty="SELECT d.JOB_NO_MST, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, b.BATCH_ID, b.PROD_ID, b.GSM, b.WIDTH, b.COLOR_ID, b.RACK_NO, b.grey_used_qty as GREY_USED_QTY, sum(CASE WHEN c.entry_form in(37,17) and c.trans_type=1 THEN c.quantity ELSE 0 END) AS FINISH_RECEIVE , sum(CASE WHEN c.entry_form in(37,17) and c.trans_type=1 THEN c.fab_meter_qnty ELSE 0 END) AS FAB_METER_QNTY
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, wo_po_break_down d 
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$cbo_company_name and a.entry_form in (37,17) and c.entry_form in (37,17) and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active in(1,3) and d.is_deleted=0 $po_id_cond 
	group by d.job_no_mst, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.batch_id, b.prod_id, b.gsm, b.width, b.color_id, b.rack_no, b.grey_used_qty";
	// echo $sql_finishRecGreyUsedQty;
	$sql_finishRecGreyUsedQty_res=sql_select($sql_finishRecGreyUsedQty);
	$finishRecGreyUsedQty_arr=array();
	
	foreach($sql_finishRecGreyUsedQty_res as $row)
	{
		$finishRecGreyUsedQty_arr[$row['JOB_NO_MST']]['FINISH_RECEIVE'] += $row['FINISH_RECEIVE'];
		$finishRecGreyUsedQty_arr[$row['JOB_NO_MST']]['GREY_USED_QTY'] += $row['GREY_USED_QTY'];
		$finishRecGreyUsedQty_arr[$row['JOB_NO_MST']]['FAB_METER_QNTY'] += $row['FAB_METER_QNTY'];
		$finishRec_prodID[$row['JOB_NO_MST']]['PROD_ID'] .= $row['PROD_ID'].',';
		$finishRec_batchID[$row['JOB_NO_MST']]['BATCH_ID'] .= $row['BATCH_ID'].',';
	}
	unset($sql_finishRecGreyUsedQty_res);
	//echo '<pre>';print_r($finishRec_prodID);

	// ==================================================Start========================
	// Finish Fabric Roll receive (Sales Order)
	$sql_finishRollRecQty="SELECT d.PO_JOB_NO, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, b.BATCH_ID, b.PROD_ID, sum(c.QNTY) AS FINISH_ROLL_RECEIVE, C.BARCODE_NO
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, fabric_sales_order_mst d
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.id=c.mst_id and a.company_id=1 and a.entry_form in (68) and c.entry_form in (68) and a.status_active=1
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $job_no_cond3
	group by d.PO_JOB_NO, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, b.BATCH_ID, b.PROD_ID, C.BARCODE_NO order by a.recv_number"; //and D.PO_JOB_NO='BPKW-21-00336'
	// echo $sql_finishRollRecQty;die;
	$sql_finishRollRecQty_res=sql_select($sql_finishRollRecQty);

	$finish_barcode_arr=array();
	foreach($sql_finishRollRecQty_res as $row)
	{
		$finish_barcode_arr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
		$finishRecGreyUsedQty_arr[$row['PO_JOB_NO']]['FINISH_RECEIVE'] += $row['FINISH_ROLL_RECEIVE'];
	}
	// print_r($finish_barcode_arr);
	$finish_barcode_arr = array_filter($finish_barcode_arr);
	if(count($finish_barcode_arr)>0)
	{
		$all_barcode = implode(",", $finish_barcode_arr);
        $all_barcode_cond=""; $barcodeCond="";
        if($db_type==2 && count($finish_barcode_arr)>999)
        {
        	$all_finish_barcode_arr_chunk=array_chunk($finish_barcode_arr,999) ;
        	foreach($all_finish_barcode_arr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$barcodeCond.="  c.barcode_no in($chunk_arr_value) or ";
        	}

        	$all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
        }
        else
        {
        	$all_barcode_cond=" and c.barcode_no in($all_barcode)";
        }
		$roll_data_array=sql_select("SELECT C.QNTY, C.REJECT_QNTY, C.BARCODE_NO from pro_roll_details c where c.entry_form=66 and c.is_sales=1 and c.status_active=1 $all_barcode_cond group by c.qnty, c.reject_qnty, c.barcode_no");
    }
    $greyUsedRollQty_arr=array();
	foreach($roll_data_array as $row)
	{
		$greyUsedRollQty_arr[$row['BARCODE_NO']]['QNTY'] += $row['QNTY'];
		// $greyUsedRollQty_arr[$row['BARCODE_NO']]['REJECT_QNTY'] += $row['REJECT_QNTY'];
	}
	// echo '<pre>';print_r($greyUsedRollQty_arr);die;
	$finishRollRecQty_arr=array();
	foreach($sql_finishRollRecQty_res as $row)
	{
		$finishRollRecQty_arr[$row['PO_JOB_NO']]['GREY_USED_QTY']+=$greyUsedRollQty_arr[$row['BARCODE_NO']]['QNTY'];
		// $finishRollRecQty_arr[$row['PO_JOB_NO']]['REJECT_QNTY']+=$greyUsedRollQty_arr[$row['BARCODE_NO']]['REJECT_QNTY'];

		$finishRollRecQty_arr[$row['PO_JOB_NO']]['FINISH_ROLL_RECEIVE'] += $row['FINISH_ROLL_RECEIVE'];
		$finishRec_batchID[$row['PO_JOB_NO']]['BATCH_ID'] .= $row['BATCH_ID'].',';
		$finishRollRec_prodID[$row['PO_JOB_NO']]['PROD_ID'] .= $row['PROD_ID'].',';
		$finishRec_prodID[$row['PO_JOB_NO']]['PROD_ID'] .= $row['PROD_ID'].',';
	}
	unset($sql_finishRollRecQty_res);
	// echo '<pre>';print_r($finishRollRecQty_arr);die;
	// ==================================================End========================

	$sql_finish_issue="SELECT c.PO_BREAKDOWN_ID, a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID, 
		sum(CASE WHEN a.issue_purpose=9 THEN c.quantity ELSE 0 END) AS FINISH_ISS_SEW_PROD,
		sum(CASE WHEN a.issue_purpose=44 THEN c.quantity ELSE 0 END) AS FINISH_ISS_RE_PROCESS,
		sum(CASE WHEN a.issue_purpose=31 THEN c.quantity ELSE 0 END) AS FINISH_ISS_SCRAP,
		sum(CASE WHEN a.issue_purpose=4 THEN c.quantity ELSE 0 END) AS FINISH_ISS_TO_SAMPLE
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$cbo_company_name and a.entry_form in (18,71) and c.entry_form in (18,71) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_id_cond
		group by c.PO_BREAKDOWN_ID, b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id
		union all
		SELECT c.PO_BREAKDOWN_ID, a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID, 
        sum(CASE WHEN a.issue_purpose=9 THEN c.quantity ELSE 0 END) AS FINISH_ISS_SEW_PROD,
        sum(CASE WHEN a.issue_purpose=44 THEN c.quantity ELSE 0 END) AS FINISH_ISS_RE_PROCESS,
        sum(CASE WHEN a.issue_purpose=31 THEN c.quantity ELSE 0 END) AS FINISH_ISS_SCRAP,
        sum(CASE WHEN a.issue_purpose=4 THEN c.quantity ELSE 0 END) AS FINISH_ISS_TO_SAMPLE
        from inv_issue_master a, INV_WVN_FINISH_FAB_ISS_DTLS b, order_wise_pro_details c 
        where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$cbo_company_name and a.entry_form in (19) and c.entry_form in (19) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
        and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_id_cond
        group by c.PO_BREAKDOWN_ID, b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id";
	// echo $sql_finish_issue;	
	$sql_finish_issue_res=sql_select($sql_finish_issue);
	$finish_issue_arr=array();	
	foreach($sql_finish_issue_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAKDOWN_ID']];
		$finish_issue_arr[$jobno]['FINISH_ISS_SEW_PROD'] += $row['FINISH_ISS_SEW_PROD'];
		$finish_issue_arr[$jobno]['FINISH_ISS_RE_PROCESS'] += $row['FINISH_ISS_RE_PROCESS'];
		$finish_issue_arr[$jobno]['FINISH_ISS_SCRAP'] += $row['FINISH_ISS_SCRAP'];
		$finish_issue_arr[$jobno]['FINISH_ISS_TO_SAMPLE'] += $row['FINISH_ISS_TO_SAMPLE'];
		$finishIssue_prodId[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
	}
	unset($sql_finish_issue_res);
	//echo '<pre>';print_r($finish_issue_arr);die;

	// Finish fabric receive return, issue return, transfer 
	$sql_finFabRecIssueReturn="SELECT c.PO_BREAKDOWN_ID, c.PROD_ID,c.TRANS_TYPE,
		sum(CASE WHEN c.entry_form in (46,202) and c.trans_type=3 THEN c.quantity ELSE 0 END) AS FIN_RECV_RTN_QNTY,
		sum(CASE WHEN c.entry_form in(52,209) and c.trans_type=4 THEN c.quantity ELSE 0 END) AS FIN_ISS_RETN_QNTY,
		sum(CASE WHEN c.entry_form in (14,306,258) and c.trans_type=5 THEN c.quantity ELSE 0 END) AS FIN_TRANS_IN_QNTY,
        sum(CASE WHEN c.entry_form in (14,306,258) and c.trans_type=6 THEN c.quantity ELSE 0 END) AS FIN_TRANS_OUT_QNTY
		from order_wise_pro_details c 
		where c.status_active=1 and c.is_deleted=0 and c.entry_form in(46,52,14,306,258,202,209) and c.trans_type in(3,4,5,6) $po_id_cond 
		group by c.po_breakdown_id, c.prod_id,c.trans_type";
	$sql_finFabRecIssueReturn_res=sql_select($sql_finFabRecIssueReturn);
	$finFabRecIssueReturn_arr=array();
	foreach($sql_finFabRecIssueReturn_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAKDOWN_ID']];	
		$finFabRecIssueReturn_arr[$jobno]['FIN_RECV_RTN_QNTY'] += $row['FIN_RECV_RTN_QNTY'];
		$finFabRecIssueReturn_arr[$jobno]['FIN_ISS_RETN_QNTY'] += $row['FIN_ISS_RETN_QNTY'];
		
		if($row['TRANS_TYPE'] ==5 || $row['TRANS_TYPE'] ==6)
		{
			$finFabTransfer_arr[$jobno]['FIN_TRANS_IN_QNTY'] += $row['FIN_TRANS_IN_QNTY'];
			$finFabTransfer_arr[$jobno]['FIN_TRANS_OUT_QNTY'] += $row['FIN_TRANS_OUT_QNTY'];
			$finishTransfer_prodID[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
		}

		$finishRecIssueReturn_prodID[$jobno]['PROD_ID'] .= $row['PROD_ID'].',';
	}
	unset($sql_finFabRecIssueReturn_res);
	//echo '<pre>';print_r($finFabRecIssueReturn_arr);

	// Finish fabric issue return with Return Purpose
	$sql="SELECT c.PO_BREAKDOWN_ID, c.PROD_ID,c.TRANS_TYPE,
		sum(CASE WHEN c.entry_form in(52,209) and c.trans_type=4 and a.receive_purpose=1 THEN c.quantity ELSE 0 END) AS FIN_ISS_RETN_CLOSE_QNTY,
		sum(CASE WHEN c.entry_form in(52,209) and c.trans_type=4 and a.receive_purpose=2 THEN c.quantity ELSE 0 END) AS FIN_ISS_RETN_REPROCESS_QNTY
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.trans_id and c.status_active=1 and c.is_deleted=0 and c.entry_form in(52,209) and c.trans_type in(4) and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $po_id_cond 
		group by c.po_breakdown_id, c.prod_id,c.trans_type";
		// echo $sql;
	$sql_res=sql_select($sql);
	$finFabRecIssueReturnWthPurpose_arr=array();
	foreach($sql_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAKDOWN_ID']];	
		$finFabRecIssueReturnWthPurpose_arr[$jobno]['FIN_ISS_RETN_CLOSE_QNTY'] += $row['FIN_ISS_RETN_CLOSE_QNTY'];
		$finFabRecIssueReturnWthPurpose_arr[$jobno]['FIN_ISS_RETN_REPROCESS_QNTY'] += $row['FIN_ISS_RETN_REPROCESS_QNTY'];
	}
	unset($sql_res);
	//echo '<pre>';print_r($finFabRecIssueReturn_arr);


	// SQL Cutting
	$sql_cutting="SELECT c.PO_BREAK_DOWN_ID, sum(c.production_quantity) as CUTTING_QTY,c.used_qty_kg as USED_KG,c.reject_qty_kg_break_down as REJECT_BREAK_DOWN from pro_garments_production_mst c where c.company_id=$cbo_company_name and c.production_type=1 and c.status_active=1 and c.is_deleted=0 $po_id_cond2 group by a.job_no, a.buyer_name, a.style_ref_no";
	// echo $sql_cutting;
	$sql_cutting_res=sql_select($sql_cutting);
	$cutting_arr=array();
	foreach($sql_cutting_res as $row)
	{
		$jobno = $po_wise_job_arr[$row['PO_BREAK_DOWN_ID']];
		$cutting_arr[$jobno]['CUTTING_QTY'] += $row['CUTTING_QTY'];
		$cutting_arr[$jobno]['USED_KG'] += $row['USED_KG'];

		if($row['REJECT_BREAK_DOWN'] !="")
		{
			$ex_data = explode("__", $row['REJECT_BREAK_DOWN']);
			$cutting_arr[$jobno]['CUTTING_WESTAGE_KG'] += $ex_data[0]+$ex_data[1]+$ex_data[2];
		}
	}
	unset($sql_cutting_res);
	//echo '<pre>';print_r($cutting_arr);
	$table_width = 3840+80+80+80+80+80+80;
	ob_start();

	?>
	<div width="<? echo $table_width; ?>">
		<table cellpadding="0" cellspacing="0" width="<? echo $table_width; ?>">
			<tr>
			   <td align="center" width="100%"><strong style="font-size:16px"><? echo $company_library[$cbo_company_name]; ?></strong></td>
			</tr>
		</table>
	    <!-- ============================= table header ========================== -->
		<table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
			<thead>
				<tr> 
					<th style="background: #c3e6cb;" colspan="12">Basic Information</th>
					<th style="background: #f5c6cb;" colspan="6">Yarn</th>
					<th style="background: #ffeeba;" colspan="5">Greige Fabric</th>
					<th style="background: #bee5eb;" colspan="5">Finish Fabric Information</th>
					<th style="background: #f5c6cb;" colspan="11">Fabric Store</th>
					<th style="background: #ffeeba;" colspan="9">Cutting</th>
					<th style="background: #96baff;" colspan="4">Stock Store</th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="120">Company Name</th>
	                <th width="120">Buyer Name</th>
					<th width="120">Job No.</th>
	                <th width="120">Style No.</th>
	                <th width="80">Shipment Date</th>
	                <th width="80">Last Ex-Factory Date</th>
	                <th width="80">Order Qty</th>
	                <th width="80">Plan Cut Qty</th>
	                <th width="80">Plan Cutting %</th>
	                <th width="80">AVG Fin. Cons DZ</th>
	                <th width="80">AVG Grey Cons DZ</th>

	                <th width="80">Main Booking of Yarn </th>
	                <th width="80">Sample Booking Grey Qty</th>
	                <th width="80">Short Fabric Booking Yarn Qty</th>
	                <th width="80">Total Yarn Booking  Qty</th>
	                <th width="80">Yarn Issue For Knitting</th>
	                <th width="80">Yarn Issue Balance</th>

	                <th width="80">Grey Fabric Received Qty</th>
	                <th width="80">Grey Received Balance Qty</th>
	                <th width="80">Grey Fabric Issue To Dyeing</th>
	                <th width="80">Transfer Qty (Grey)</th>
	                <th width="80">Grey Fabric Stock In Hand</th>

	                <th width="80">Main Booking of Finish fabric qty</th>
	                <th width="80">Sample Finish Required Qty</th>
	                <th width="80">Short Fabric Booking Fins Qty</th>
	                <th width="80">Total Finished Fabric Req. Qty</th>
	                <th width="80">Booking Process Loss %</th>

	                <th width="80">Grey Used</th>
	                <th width="80">Finished Fabric Received Qty</th>
	                <th width="80">Actual Process Loss %</th>
	                <th width="80">Finished Fabric Receive Qty (MRT)</th>
	                <th width="80">Fabric Store Receive Balance</th>
	                <th width="80">Fabric Issue To Cutting</th>
	                <th width="80">Fabric Issue To Sample</th>
	                <th width="80">Fabric Issue To Re-Process</th>
	                <th width="80">Transfer Qty(Finish)</th>
	                <th width="80">Issue to Scrap Store Qty</th>
	                <th width="80">Fabric Stock in Hand</th>

	                <th width="80">Actual Cutting PCS</th>
	                <th width="80">Cutting Balance PCS</th>
	                <th width="80">Cutting %</th>
	                <th width="80">Actual Cutting Cons</th>
	                <th width="80">Cutting Used Fabric KG</th>
	                <th width="80">Cutting Westage KG</th>
	                <th width="80">Cutting Westage KG %</th>
	                <th width="80">Cutting In-Hand Fabric</th>

	                <th width="80">Cutting closed & Retrun to Store</th>
	                <th width="80">Cutting Return to Re-process </th>
	                <th width="80">All Stock</th>
	                <th width="80">Stock %</th>
				</tr>
			</thead>
		</table>
		<!-- ================================= table body ========================= -->
		<div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:300px" id="scroll_body">
		    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
		        <tbody>
		        	<?
		        	$i=1;
		        	$tot_order_qty=$tot_plan_cut_qty=$tot_avg_finish_cons=$tot_avg_gray_cons=$tot_sam_booking_grey_qnty=$tot_shortFabBooking_grey_fab_qnty=$tot_total_yarn_req_qty=$tot_yarn_qty=$tot_yarn_allocation_qnty=$tot_issue_qnty=$tot_issue_balance=$tot_grey_fab_rec_qty=0; 
		        	$tot_grey_rec_bal_qty=$tot_grey_issue_rollwise=$tot_gray_stockinhand=$tot_fin_fab_req=$tot_shortFabBooking_fin_fab_qnty=$tot_total_fin_fab_req_qty=$tot_grey_used_qty=0;
		        	$tot_fin_fab_rec_qty=$tot_fab_store_rec_bal=$tot_finish_issue=$tot_sample_issue=$tot_finish_iss_re_process=$tot_fab_stock_in_hand=$tot_fin_iss_retn_qnty=$tot_possible_cutt_pcs=$tot_cutting_qty=0; 
		        	$tot_cutting_bal=$tot_actual_cutt_cons=$tot_plan_cut_qty_pcs=$tot_fabric_stock_percent=$tot_cutting_percent=$tot_process_loss=$tot_plan_cut_percent=$tot_booking_process_loss_percent=0;
		        	$tot_fab_used_kg=$tot_cutting_westage_kg=$tot_cutting_westage_prsnt=$tot_cutting_in_hand_kg=$tot_return_to_store=0;

		        	foreach ($asc_date_data_arr as $datekey => $dateval)
		        	{
			        	foreach ($dateval as $job => $row)
			        	{
			        		$jobwisepoId = implode(',',array_unique(explode(',',rtrim($job_alldata_arr[$job]['PO_ID'],','))));
			        		$exShipStatus=implode(',',array_unique(explode(',',rtrim($job_alldata_arr[$job]['SHIPING_STATUS'],','))));
			        		$strVal=0;
			        		if($cbo_report_type==2) $strVal=$exShipStatus=='3'; else $strVal=$exShipStatus!='3';
			        		if ($strVal)
			        		{						     		
				        		$avg_finish_cons      = $avgFinGrayCon_arr[$job]['AVG_FINISH_CONS'];
				        		$avg_gray_cons        = $avgFinGrayCon_arr[$job]['AVG_GRAY_CONS'];

				        		$shortFabBooking_grey_fab_qnty= $fabBooking_arr[$job]['GREY_FAB_QNTY_SHORT'];
				        		$shortFabBooking_fin_fab_qnty = $fabBooking_arr[$job]['FIN_FAB_QNTY_SHORT'];	
				        		$tot_fabbooking_fin_fab_qnty  = $fabBooking_arr[$job]['FIN_FAB_QNTY'];	
				        		$tot_fabbooking_grey_fab_qnty = $fabBooking_arr[$job]['GREY_FAB_QNTY'];
				        		$sam_booking_grey_qnty        = $fabBooking_arr[$job]['SAM_BOOKING_GREY_QNTY'];

				        		//$process_loss_method=return_field_value("process_loss_method", "wo_pre_cost_fabric_cost_dtls","job_no='$job'");			        		
								$process_loss_percent=(($tot_fabbooking_grey_fab_qnty-$tot_fabbooking_fin_fab_qnty)/$tot_fabbooking_grey_fab_qnty)*100;									

				        		$yarn_allocation_qnty = $yarnAllocation_arr[$job]['YARN_QNTY'];
				        		$yarn_issue_qnty      = $yarnIssueReturn_arr[$job]['ISSUE_QNTY'];
				        		$yarn_transfer_out    = $yarnTransfer_arr[$job]['YARN_TRANSFER_QNTY'];
				        		$yarn_issue_return    = $yarnIssueReturn_arr[$job]['ISSUE_RTN_QTY'];
				        		$issue_qnty           = ($yarn_issue_qnty-$yarn_issue_return)+$yarn_transfer_out;
				        		$grey_fab_rec_qty     = $grayFabRecQty_res_arr[$job]['GREY_FAB_REC_QTY'];
				        		$grey_issue_rollwise  = $greyFabRollIssue_arr[$job]['GREY_ISSUE_ROLLWISE'];
				        		$grey_transin 		  =	$greyFabTransIn_arr[$job]['GREY_TRANSIN'];
				        		$grey_transout 		  =	$greyFabTransIn_arr[$job]['GREY_TRANSOUT'];
				        		$net_grey_transfer	  = ($greyFabTransIn_arr[$job]['GREY_TRANSIN'] - $greyFabTransIn_arr[$job]['GREY_TRANSOUT']);
				        		$finish_receive       = $finishRecGreyUsedQty_arr[$job]['FINISH_RECEIVE'];
				        		$fin_recv_rtn_qnty    = $finFabRecIssueReturn_arr[$job]['FIN_RECV_RTN_QNTY'];
				        		$fabric_meter_qnty    =	$finishRecGreyUsedQty_arr[$job]['FAB_METER_QNTY'];
				        		$grey_used_qty        = $finishRecGreyUsedQty_arr[$job]['GREY_USED_QTY']+$finishRollRecQty_arr[$job]['GREY_USED_QTY'];
								$finish_sales_roll_receive= $finishRollRecQty_arr[$job]['FINISH_ROLL_RECEIVE'];
								// $finish_sales_roll_receive= $finishRollRec_prodID[$job]['PROD_ID'];

				        		$finish_issue         = $finish_issue_arr[$job]['FINISH_ISS_SEW_PROD'];	
				        		$finish_iss_re_process= $finish_issue_arr[$job]['FINISH_ISS_RE_PROCESS'];
				        		$finish_iss_scrap 	  = $finish_issue_arr[$job]['FINISH_ISS_SCRAP'];
				        		$finish_iss_to_sample = $finish_issue_arr[$job]['FINISH_ISS_TO_SAMPLE'];
				        		$fin_iss_retn_qnty    = $finFabRecIssueReturn_arr[$job]['FIN_ISS_RETN_QNTY'];    
				        		$fin_trans_in_qnty    = $finFabTransfer_arr[$job]['FIN_TRANS_IN_QNTY'];    
				        		$fin_trans_out_qnty   = $finFabTransfer_arr[$job]['FIN_TRANS_OUT_QNTY'];    
				        		$fin_net_trans_qnty   = $fin_trans_in_qnty - $fin_trans_out_qnty;    

				        		$fin_iss_retn_close_qnty    	= $finFabRecIssueReturnWthPurpose_arr[$job]['FIN_ISS_RETN_CLOSE_QNTY'];
				        		$fin_iss_retn_reprocess_qnty    = $finFabRecIssueReturnWthPurpose_arr[$job]['FIN_ISS_RETN_REPROCESS_QNTY'];



				        		$cutting_qty          = $cutting_arr[$job]['CUTTING_QTY'];	        		
				        		$cutting_used_kg      = $cutting_arr[$job]['USED_KG'];	        		
				        		$cutting_westage_kg   = $cutting_arr[$job]['CUTTING_WESTAGE_KG'];	        		
				        		$cutting_westage_prsnt= ($cutting_westage_kg/$cutting_used_kg)*100;	        		
				        		$cutting_in_hand_fab  = $finish_issue - $cutting_used_kg - $fin_iss_retn_qnty;      	
				        		// echo $finish_issue.'**'.$cutting_used_kg.'**'.$fin_iss_retn_qnty;
		
				        		
				        		//$gray_stockinhand = $grey_fab_rec_qty-$grey_issue_rollwise;
				        		$gray_stockinhand = ($grey_fab_rec_qty+$net_grey_transfer)-$grey_issue_rollwise;
				        		//$fin_fab_rec_qty  = ($finish_receive-$fin_recv_rtn_qnty)+$finish_sales_roll_receive;
				        		$fin_fab_rec_qty  = ($finish_receive-$fin_recv_rtn_qnty);
				        		$process_loss     = 100-(($fin_fab_rec_qty*100)/$grey_used_qty);

								//$fin_fab_req          = ($row['PLAN_CUT_QTY']/12)*$avg_finish_cons;
								//$yarn_qty             = ($row['PLAN_CUT_QTY']/12)*$avg_gray_cons;

								$fin_fab_req          = array_sum($fabric_req_arr["knit"]["finish"][$job])+array_sum($fabric_req_arr["woven"]["finish"][$job]);
								$yarn_qty             = $yarn_req_arr[$job];


								$total_yarn_req_qty   = $yarn_qty+$sam_booking_grey_qnty+$shortFabBooking_grey_fab_qnty;
								//$total_fin_fab_req_qty= $fin_fab_req + $shortFabBooking_fin_fab_qnty;
								$total_fin_fab_req_qty= $fin_fab_req + $shortFabBooking_fin_fab_qnty + $fabBooking_arr[$job]['SAM_BOOKING_FIN_QNTY'];
								$issue_balance        = $total_yarn_req_qty-$issue_qnty;
								$grey_rec_bal_qty     = $issue_qnty-$grey_fab_rec_qty;

								$fab_store_rec_bal = $total_fin_fab_req_qty-$fin_fab_rec_qty;
								$fab_stock_in_hand = ($fin_fab_rec_qty+$fin_iss_retn_qnty + $fin_net_trans_qnty)-($finish_issue+$finish_iss_re_process+$finish_iss_to_sample +$finish_iss_scrap);
								//echo $fin_fab_rec_qty.'**'.$fin_iss_retn_qnty.'**'.$finish_issue;

								$fabric_stock_percent=(100*($fab_stock_in_hand+$fin_iss_retn_qnty))/$fin_fab_req;
								$possible_cutt_pcs = $row['PLAN_CUT_QTY_PCS']/$fin_fab_req*$finish_issue;

								//$cutting_bal       = $row['PLAN_CUT_QTY_PCS']-$cutting_qty;
								//$cutting_percent   = 100-(($cutting_qty*100)/$row['PLAN_CUT_QTY_PCS']);
								
								$cutting_bal       = $row['ORDER_QTY']-$cutting_qty;
								$cutting_percent   = (($cutting_qty*100)/$row['ORDER_QTY']);

								$actual_cutt_cons  = ($cutting_used_kg*12)/$cutting_qty;

				        		if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				        		?>
					        	<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('trds_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trds_<? echo $i; ?>">
					        		<td width="50" align="center"><? echo $i; ?></td>
					        		<td width="120"><p><? echo $company_arr[$row['COMPANY_NAME']]; ?></p></td>
					        		<td width="120"><p><? echo $buyer_name_arr[$row['BUYER_NAME']]; ?></p></td>
					        		<td width="120"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job; ?>','<? echo $jobwisepoId; ?>','job_no_popup','520px');"><? echo $job; ?></a></p></td>
					        		<td width="120"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
					        		<td width="80"><p><? echo change_date_format($row['PUB_SHIPMENT_DATE']); ?></p></td>

					        		<td width="80" align="center"><p><? echo change_date_format($ex_factory_date_arr[$job]['EX_FACTORY_DATE']); ?></p></td>
					        		<td width="80" align="right"><p><? echo $row['ORDER_QTY']; ?></p></td>
					        		<td width="80" align="right"><p><? echo $row['PLAN_CUT_QTY_PCS']; ?></p></td>
					        		<td width="80" align="right"><p><? echo number_format($row['PLAN_CUT_PERCENT'],2); ?></p></td>
					        		<td width="80" align="right"><p><? echo number_format($avg_finish_cons,2); ?></p></td>
					        		<td width="80" align="right"><p><? echo number_format($avg_gray_cons,2); ?></p></td>


					        		<td width="80" align="right" title="(Plan Cut Qty/12)*Avg Gray Cons"><p><? echo number_format($yarn_qty,2); ?></p></td>
					        		<td width="80" align="right"><p><? echo number_format($sam_booking_grey_qnty,2); ?></p></td>
					        		<td width="80" align="right"><p><? echo number_format($shortFabBooking_grey_fab_qnty,2); ?></p></td>
					        		<td width="80" align="right" title="Yarn Required+Sample Booking Grey Qty+Short Fabric Booking Yarn Qty"><p><? echo number_format($total_yarn_req_qty,2); ?></p></td>
					        		<td width="80" align="right" title="Issue Qty-Issue Return"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$yarnIssue_prodId[$job]['PROD_ID']."__".$yarnIssueReturn_prodId[$job]['PROD_ID']; ?>','<? echo $jobwisepoId; ?>','yarn_issue_knitting_popup','1200px');"><? echo number_format($issue_qnty,2); ?></a></p></td>
					        		<td width="80" align="right" title="Yarn Req Qty-Issue Qnty"><p><? echo number_format($issue_balance,2); ?></p></td>


					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$grayFabRecQty_prodId[$job]['PROD_ID']; ?>','<? echo $jobwisepoId;?>','grey_fabric_receive_popup','1200px');"><? echo number_format($grey_fab_rec_qty,2); ?></a></p></td>
					        		<td width="80" align="right" title="Yarn Issue Qnty-Grey Fab Rec Qty"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$yarnIssue_prodId[$job]['PROD_ID']."__".$grayFabRecQty_prodId[$job]['PROD_ID']."__".$row['BUYER_NAME']."__".$row['STYLE_REF_NO']; ?>','<? echo $jobwisepoId;?>','grey_receive_balance_popup','1200px');"><? echo number_format($grey_rec_bal_qty,2); ?></a></p></td>
					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$greyFabRollIssue_prodId[$job]['PROD_ID']; ?>','<? echo $jobwisepoId;?>','grey_fabric_issue_popup','900px');"><? echo number_format($grey_issue_rollwise,2); ?></a></p></td>
					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$greyFabTransIn_prodId[$job]['PROD_ID']; ?>','<? echo $jobwisepoId;?>','grey_fabric_transfer_popup','900px');"><? echo number_format($net_grey_transfer,2); ?></a></p></td>
					        		<td width="80" align="right" title="Grey Fab Rec Qty + Transfer Qty (grey) - Grey Fab Issue Dye"><p><? echo number_format($gray_stockinhand,2); ?></p></td>


					        		<td width="80" align="right" title="(Plan Cut Qty/12)*Avg Finish Cons"><p><? echo number_format($fin_fab_req,2); ?></p></td>	
					        		<td width="80" align="right"><p><? echo $fabBooking_arr[$job]['SAM_BOOKING_FIN_QNTY']; ?></p></td>
					        		<td width="80" align="right"><p><? echo number_format($shortFabBooking_fin_fab_qnty,2); ?></p></td>
					        		<td width="80" align="right" title="Finished Fabric Require Qty+Total Finished fabric Req. Qty"><p><? echo number_format($total_fin_fab_req_qty,2); ?></p></td>
					        		<td width="80" align="right" title="((Total Fabbooking Grey Fab Qnty-Total Fabbooking Fin Fab Qnty)/Total Fabbooking Grey Fab Qnty)*100"><p><? echo number_format($process_loss_percent,2); ?></p></td>


					        		<td width="80" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$finishRec_prodID[$job]['PROD_ID']."__".$finishRec_batchID[$job]['BATCH_ID']."__".$finishRecIssueReturn_prodID[$job]['PROD_ID']."__".$finishRollRec_prodID[$job]['PROD_ID']; ?>','<? echo $jobwisepoId;?>','finish_fabric_receive_popup','1230px');"><? echo number_format($fin_fab_rec_qty,2); ?></a></p></td>

					        		<td width="80" align="right" title="100-((Finished Fabric Received Qty*100)/Grey Used Qty)"><p><? echo number_format($process_loss,2); ?></p></td>
					        		<td width="80" align="right"><? echo number_format($fabric_meter_qnty,2); ?></td>
					        		<td width="80" align="right" title="Total Finished Fabric Require Qty-Finished Fabric Received Qty"><p><? echo number_format($fab_store_rec_bal,2); ?></p></td>
					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$finishIssue_prodId[$job]['PROD_ID']; ?>','<? echo $jobwisepoId; ?>','cutting_issue_sew_prod_popup','1200px');"><? echo number_format($finish_issue,2); ?></a></p></td>

					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$finishIssue_prodId[$job]['PROD_ID']; ?>','<? echo $jobwisepoId; ?>','cutting_issue_sample_popup','1200px');"><? echo number_format($finish_iss_to_sample,2); ?></a></p></td>

					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$finishIssue_prodId[$job]['PROD_ID']; ?>','<? echo $jobwisepoId; ?>','cutting_issue_reprocess_popup','1200px');"><? echo number_format($finish_iss_re_process,2); ?></a></p></td>

					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$finishTransfer_prodID[$job]['PROD_ID']; ?>','<? echo $jobwisepoId; ?>','finish_fabric_transfer_popup','1200px');"><? echo number_format($fin_net_trans_qnty,2); ?></a></p></td>

					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$finishIssue_prodId[$job]['PROD_ID']; ?>','<? echo $jobwisepoId; ?>','cutting_issue_scrap_popup','1200px');"><? echo number_format($finish_iss_scrap,2); ?></a></p></td>

					        		<td width="80" align="right" title="(Finished Fabric Received Qty+Return to Fabric Store+ Transfer Qnty(finish))-(Fabric Issue To Cutting+Fabric Issue To Re-Process + Fab. Issue to sample + Fab. Issue To Scrap)"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$finishRec_prodID[$job]['PROD_ID']."__".$finishRec_batchID[$job]['BATCH_ID']."__".$finishRecIssueReturn_prodID[$job]['PROD_ID']."__".$fin_recv_rtn_qnty; ?>','<? echo $jobwisepoId; ?>','fab_stock_in_hand_popup','900px');"><? echo number_format($fab_stock_in_hand,2); ?></a></p></td>


					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job; ?>','<? echo $jobwisepoId; ?>','cutting_popup','720px');"><? echo number_format($cutting_qty,0); ?></a></p></td>
					        		<td width="80" align="right" title="Order Qty Pcs-Cutting Qty"><p><? echo number_format($cutting_bal); ?></p></td>
					        		<?
					        			if ($cutting_percent > -1)
					        			{
					        				$bgColor='style="background-color: #00FF00;"';
					        			} else if ($cutting_percent <= -1 && $cutting_percent >= -2) {
					        				$bgColor='style="background-color: yellow;"';
					        			} else if ($cutting_percent < -2 && $cutting_percent >= -3) {
					        				$bgColor='style="background-color: pink;"';
					        			} else {
					        				$bgColor='style="background-color: red;"';
					        			}	
					        		?>
					        		<td width="80" align="right" title="((Actual Cutting Pcs*100)/Order Qty Pcs)" <? echo $bgColor; ?>><p><? echo number_format($cutting_percent,2); ?></p></td>
					        		<td width="80" align="right" title="(Cutting Used Fabric KG*12)/Actual Cutting PCS"><p><? echo number_format($actual_cutt_cons,2); ?></p></td>
					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job; ?>','<? echo $jobwisepoId; ?>','cutting_used_popup','790px');"><? echo number_format($cutting_used_kg,2); ?></a></p></td>
					        		<td width="80" align="right"><p>
					        			<a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job; ?>','<? echo $jobwisepoId; ?>','cutting_westage_popup','720px');"><? echo number_format($cutting_westage_kg,2); ?></a>
					        		</p></td>
					        		<td width="80" align="right"  title="Cutting westage kg/Fab. used kg*100"><p><? echo number_format($cutting_westage_prsnt,2); ?></p></td>
					        		<td width="80" align="right" title="Fab. issue to cut - fab. used kg - Return to fab. store"><p><? echo number_format($cutting_in_hand_fab,2); ?></p></td>


					        		<td width="80" align="right"><p><a href='#report_details' onClick="openmypage_popup('<? echo $cbo_company_name."__".$job."__".$finishRecIssueReturn_prodID[$job]['PROD_ID']; ?>','<? echo $jobwisepoId; ?>','return_from_cuttting_popup','1200px');"><? echo number_format($fin_iss_retn_close_qnty,2); ?></a></p></td>
					        		<td width="80" align="right"><p><?=number_format($fin_iss_retn_reprocess_qnty,2); ?></p></td>
					        		<td width="80" align="right"><p><?= number_format(($fab_stock_in_hand+$gray_stockinhand+$issue_balance),2); ?></p></td>
					        		<td width="80" align="right"><p><?= number_format(((($fab_stock_in_hand+$gray_stockinhand+$issue_balance)/$total_yarn_req_qty)*100),2);?>%</p></td>
					        	</tr>
					        	<?
					        	$i++;
					        	$tot_order_qty += $row['ORDER_QTY'];
					        	$tot_plan_cut_qty += $row['PLAN_CUT_QTY'];
					        	$tot_plan_cut_qty_pcs += $row['PLAN_CUT_QTY_PCS'];
					        	$tot_avg_finish_cons += $avg_finish_cons;
					        	$tot_avg_gray_cons += $avg_gray_cons;						
								$tot_yarn_qty += $yarn_qty;
								$tot_sam_booking_grey_qnty += $sam_booking_grey_qnty;
								$tot_shortFabBooking_grey_fab_qnty += $shortFabBooking_grey_fab_qnty;
								$tot_total_yarn_req_qty += $total_yarn_req_qty;							
								$tot_yarn_allocation_qnty += $yarn_allocation_qnty;
								$tot_issue_qnty += $issue_qnty;
								$tot_issue_balance += $issue_balance;
								$tot_grey_fab_rec_qty += $grey_fab_rec_qty;
								$tot_grey_rec_bal_qty += $grey_rec_bal_qty;
								$tot_grey_issue_rollwise += $grey_issue_rollwise;
								$tot_net_grey_transfer += $net_grey_transfer;
								$tot_gray_stockinhand += $gray_stockinhand;
								$tot_fin_fab_req += $fin_fab_req;
								$tot_shortFabBooking_fin_fab_qnty += $shortFabBooking_fin_fab_qnty;
								$tot_total_fin_fab_req_qty += $total_fin_fab_req_qty;
								$tot_grey_used_qty += $grey_used_qty;
								$tot_fin_fab_rec_qty += $fin_fab_rec_qty;
								$tot_fab_meter_qnty += $fabric_meter_qnty;
								$tot_fab_store_rec_bal += $fab_store_rec_bal;
								$tot_finish_issue += $finish_issue;
								$tot_sample_issue += $finish_iss_to_sample;
								$tot_finish_iss_re_process += $finish_iss_re_process;
								$tot_fin_net_trans_qnty += $fin_net_trans_qnty; 
								$tot_finish_iss_scrap += $finish_iss_scrap; 
								$tot_fab_stock_in_hand += $fab_stock_in_hand;
								$tot_fin_iss_retn_qnty += $fin_iss_retn_qnty;
								$tot_possible_cutt_pcs += $possible_cutt_pcs;
								$tot_cutting_qty += $cutting_qty;
								$tot_cutting_bal += $cutting_bal;
								$tot_actual_cutt_cons += $actual_cutt_cons;
								$tot_fab_used_kg +=$cutting_used_kg;
								$tot_cutting_westage_kg +=$cutting_westage_kg;
								$tot_cutting_westage_prsnt +=$cutting_westage_prsnt;
								$tot_cutting_in_hand_kg +=0;
								$tot_return_to_store +=0;
							}
						}
					}
					$tot_plan_cut_percent = ($tot_plan_cut_qty*100/$tot_order_qty)-100;
					$tot_booking_process_loss_percent = 100-(($tot_fin_fab_req*100)/$tot_yarn_qty);
					$tot_process_loss = 100-(($tot_fin_fab_rec_qty*100)/$tot_grey_used_qty);
					$tot_fabric_stock_percent = (100*($tot_fab_stock_in_hand+$tot_fin_iss_retn_qnty))/$tot_fin_fab_req;
					$tot_cutting_percent = (($tot_cutting_qty*100)/$tot_order_qty);
					$stock_percent = (($tot_fab_stock_in_hand + $tot_gray_stockinhand + $tot_issue_balance)/$tot_total_yarn_req_qty)*100;
			        ?>
		        </tbody>
		    </table>
	    </div>
	    <!-- =============================== table footer ======================== -->
	    <table class="rpt_table" border="1" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
			<tfoot>
				<tr>
					<th width="50"><p>&nbsp;</p></th>
					<th width="120"><p>&nbsp;</p></th>
	                <th width="120"><p>&nbsp;</p></th>
					<th width="120"><p>&nbsp;</p></th>
	                <th width="120"><p>&nbsp;</p></th>
	                <th width="80"><p>&nbsp;</p></th>
	        		<th width="80" align="right"><p>Total:</p></th>
	        		<th width="80" align="right" id="tot_order_qty_id"><p><? echo $tot_order_qty; ?></p></th>
	        		<th width="80" align="right" id="tot_plan_cut_qty_id"><p><? echo $tot_plan_cut_qty; ?></p></th>
	        		<th width="80" align="right" title="(Total Plan Cut Qty/Total Order Qty)*100-100"><p><? echo number_format($tot_plan_cut_percent,2);?></p></th>
	        		<th width="80" align="right"  title="(Total finish required/Total order)*12">
	        		<p>
	        			<? 
	        			//id="value_tot_avg_finish_cons"
	        			if($tot_order_qty>0){
	        				echo number_format(($tot_fin_fab_req/$tot_order_qty)*12,2); 
	        			}else{
	        				echo "0";
	        			}
	        			//number_format($tot_avg_finish_cons,2); 
	        			?>
	        		</p>
	        		</th>
	        		<th width="80" align="right"  title="(Total yarn required/Total order)*12"><p>
	        		<? 
	        			//id="value_tot_avg_gray_cons"
	        			if($tot_order_qty>0)
	        			{
	        				echo number_format(($tot_yarn_qty/$tot_order_qty)*12,2); 
	        			}else{
	        				echo "0";
	        			}
	        			//echo number_format($tot_avg_gray_cons,2); 
	        		?>
	        		</p>
	        		</th>


	        		<th width="80" align="right" id="value_tot_yarn_qty"><p><? echo number_format($tot_yarn_qty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_sam_booking_grey_qnty"><p><? echo number_format($tot_sam_booking_grey_qnty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_shortFabBooking_grey_fab_qnty"><p><? echo number_format($tot_shortFabBooking_grey_fab_qnty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_total_yarn_req_qty"><p><? echo number_format($tot_total_yarn_req_qty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_issue_qnty"><p><? echo number_format($tot_issue_qnty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_issue_balance"><p><? echo number_format($tot_issue_balance,2); ?></p></th>


	        		<th width="80" align="right" id="value_tot_grey_fab_rec_qty"><p><? echo number_format($tot_grey_fab_rec_qty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_grey_rec_bal_qty"><p><? echo number_format($tot_grey_rec_bal_qty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_grey_issue_rollwise"><p><? echo number_format($tot_grey_issue_rollwise,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_net_grey_transfer"><p><? echo number_format($tot_net_grey_transfer,2); ?> </p></th>	        		
	        		<th width="80" align="right" id="value_tot_gray_stockinhand"><p><? echo number_format($tot_gray_stockinhand,2); ?></p></th>


	        		<th width="80" align="right" id="value_tot_fin_fab_req"><p><? echo number_format($tot_fin_fab_req,2); ?></p></th>
	        		<th width="80" align="right" ><p><? ?></p></th>
	        		<th width="80" align="right" id="value_tot_shortFabBooking_fin_fab_qnty"><p><? echo number_format($tot_shortFabBooking_fin_fab_qnty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_total_fin_fab_req_qty"><p><? echo number_format($tot_total_fin_fab_req_qty,2); ?></p></th>
	        		<th width="80" align="right" title="100-((Total Finished Fabric Require Qty*100)/Total Yarn Required)"><p><? echo number_format($tot_booking_process_loss_percent,2); ?></p></th>


	        		<th width="80" align="right" id="value_tot_grey_used_qty"><p><? echo number_format($tot_grey_used_qty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_fin_fab_rec_qty"><p><? echo number_format($tot_fin_fab_rec_qty,2); ?></p></th>
	        		<th width="80" align="right" title="100-((Total Finished Fabric Received Qty*100)/Total Finished Fabric Use Qty(Grey))">
	        			<p><? echo number_format($tot_process_loss,2); ?></p>
	        		</th>
	        		<th width="80" align="right" id="value_tot_fab_meter_qnty"><p><? echo number_format($tot_fab_meter_qnty,2);?></p></th>
	        		<th width="80" align="right" id="value_tot_fab_store_rec_bal"><p><? echo number_format($tot_fab_store_rec_bal,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_finish_issue"><p><? echo number_format($tot_finish_issue,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_sample_issue"><p><? echo number_format($tot_sample_issue,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_finish_iss_re_process"><p><? echo number_format($tot_finish_iss_re_process,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_fin_net_trans_qnty"><p><? echo number_format($tot_fin_net_trans_qnty,2);?></p></th>
	        		<th width="80" align="right" id="value_tot_finish_iss_scrap"><p><? echo number_format($tot_finish_iss_scrap,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_fab_stock_in_hand"><p><? echo number_format($tot_fab_stock_in_hand,2); ?></p></th>


	        		<th width="80" align="right" id="tot_cutting_qty"><p><? echo number_format($tot_cutting_qty,2); ?></p></th>
	        		<th width="80" align="right" id="tot_cutting_bal"><p><? echo number_format($tot_cutting_bal,2); ?></p></th>
	        		<th width="80" align="right" title="((Actual Cutting Pcs*100)/Order Qty Pcs)"><p><? echo number_format($tot_cutting_percent,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_actual_cutt_cons"><p><? echo number_format($tot_actual_cutt_cons,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_fab_used_kg"><p><? echo number_format($tot_fab_used_kg,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_cutting_westage_kg"><p><? echo number_format($tot_cutting_westage_kg,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_cutting_westage_prsnt"><p><? echo number_format($tot_cutting_westage_prsnt,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_cutting_in_hand_kg"><p><? echo number_format($tot_cutting_in_hand_kg,2); ?></p></th>


	        		<th width="80" align="right" id="value_tot_fin_iss_retn_qnty"><p><? echo number_format($tot_fin_iss_retn_qnty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_cut_rtn_reprocess"><p><? //echo number_format($tot_fin_iss_retn_qnty,2); ?></p></th>
	        		<th width="80" align="right" id="value_tot_all_stock"><p><? //echo number_format($tot_fin_iss_retn_qnty,2); ?></p></th>
	        		<th width="80" align="right" id=""><p><? echo number_format($stock_percent,2); ?></p></th>
				</tr>
			</tfoot>
		</table>


		<br/>
		<table  class="rpt_table" border="1" rules="all" width="400" cellpadding="0" cellspacing="0">
			<thead>
				<tr>
			        <th colspan="2" align="center"><strong>Summary</strong></th>
			    </tr>
			    <tr>
		        	<th width="250" align="center"><strong>Particulars</strong></th>
		        	<th align="center"><strong>Total Qnty</strong></th>
		    	</tr>
			</thead>
			<tbody>
			    <tr>
			        <td width="250" align="left"><strong>Total Yarn Required</strong></td>
			        <td align="right"><strong><? echo number_format($tot_total_yarn_req_qty,2); ?></strong></td>
			    </tr>
			    <tr>
			        <td width="250" align="left"><strong>Total Yarn Issued To Knitting</strong></td>
			        <td align="right"><strong><? echo number_format($tot_issue_qnty,2); ?></strong></td>
			    </tr>
			    <tr style="background-color: #B6FFFF">
			        <td width="250" align="left"><strong>Total Yarn Issue Balance</strong></td>
			        <td align="right"><strong><? echo number_format($tot_issue_balance,2); ?></strong></td>
			    </tr>
			    <tr>
			        <td width="250" align="left"><strong>Total Grey Fabric Required</strong></td>
			        <td align="right"><strong><? echo number_format($tot_total_yarn_req_qty,2); ?></strong></td>
			    </tr>
			    <tr>
			        <td width="250" align="left"><strong>Total Grey Fabric Available</strong></td>
			        <td align="right"><strong><? echo number_format($tot_grey_fab_rec_qty,2); ?></strong></td>
			    </tr>
			    <tr style="background-color: #B6FFFF">
			        <td width="250" align="left"><strong>Total Grey Fabric Balance</strong></td>
			        <td align="right"><strong><? echo number_format($tot_yarn_qty-$tot_grey_fab_rec_qty,2); ?></strong></td>
			    </tr>
			    <tr>
			        <td width="250" align="left"><strong>Total Grey Fabric Issued To Dye</strong></td>
			        <td align="right"><strong><? echo number_format($tot_grey_issue_rollwise,2);; ?></strong></td>
			    </tr>
			    <tr style="background-color: #B6FFFF">
			        <td width="250" align="left"><strong>Total Grey Fabric Issue Balance</strong></td>
			        <td align="right"><strong><? echo number_format($tot_yarn_qty-$tot_grey_issue_rollwise,2); ?></strong></td>
			    </tr>
			    <tr>
			        <td width="250" align="left"><strong>Total Finish Fabric Required</strong></td>
			        <td align="right"><strong><? echo number_format($tot_fin_fab_req,2); ?></strong></td>
			    </tr>
			    <tr>
			        <td width="250" align="left"><strong>Total Finish Fabric Available</strong></td>
			        <td align="right"><strong><? echo number_format($tot_fin_fab_rec_qty,2); ?></strong></td>
			    </tr>
			    <tr style="background-color: #B6FFFF">
			        <td width="250" align="left"><strong>Total Finish Fabric Received Balance</strong></td>
			        <td align="right"><strong><? echo number_format($tot_fin_fab_rec_qty,2); ?></strong></td>
			    </tr>
			    <tr>
			        <td width="250" align="left"><strong>Total Finish Fabric Issued To Cut</strong></td>
			        <td align="right"><strong><? echo number_format($tot_finish_issue,2); ?></strong></td>
			    </tr>
			    <tr style="background-color: #B6FFFF">
			        <td width="250" align="left"><strong>Total Finish Fabric Stock In Hand</strong></td>
			        <td align="right"><strong><? echo number_format($tot_fab_stock_in_hand,2); ?></strong></td>
			    </tr>
		    </tbody>		
		</table>
	</div>
    <?
	foreach (glob("$user_name*.xls") as $filename) 
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();
}

if ($action === 'job_no_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);
	list($company_id, $job) = explode('__', $data);

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$table_width = 470;
	?>
	<script>
	function generate_worder_report(txt_booking_no,cbo_company_name,txt_order_no_id,cbo_fabric_natu,cbo_fabric_source,txt_job_no,id_approved_id,print_id,entry_form,type,i,fabric_nature)
	{		
		var show_yarn_rate='';
		var r=confirm("Press  \"Cancel\"  to hide  Yarn Rate\nPress  \"OK\"  to Show Yarn Rate");
		if (r==true) show_yarn_rate="1";
		else show_yarn_rate="0";
		var report_title='Main Fabric Booking';

		var data="action="+type+
		'&txt_booking_no='+"'"+txt_booking_no+"'"+
		'&cbo_company_name='+"'"+cbo_company_name+"'"+
		'&txt_order_no_id='+"'"+txt_order_no_id+"'"+
		'&cbo_fabric_natu='+"'"+cbo_fabric_natu+"'"+
		'&cbo_fabric_source='+"'"+cbo_fabric_source+"'"+
		'&id_approved_id='+"'"+id_approved_id+"'"+
		'&report_title='+report_title+
		'&txt_job_no='+"'"+txt_job_no+"'"+
		'&show_yarn_rate='+show_yarn_rate+
		'&path=../';			
		
		http.open("POST","../../../order/woven_order/requires/fabric_booking_controller.php",true);	
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = function()
		{
			if(http.readyState == 4) 
		    {
				var w = window.open("Surprise", "_blank");
				var d = w.document.open();
				d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><title></title></head><body>'+http.responseText+'</body</html>');
				d.close();	
		   }		
		}
	}
	</script>
	<div>
		<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<td colspan="11" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
					</td>
				</tr>
			</thead>
		</table>
		<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
			<thead>
				<tr>
                    <th width="100"><p>Booking Type</p></th>
                    <th width="150"><p>Booking No</p></th>               
                    <th width="100"><p>Total Finish Qty</p></th>
                    <th width="100"><p>Total Grey Qty</p></th>              	
                    <th width="100"><p>Process Loss%</p></th>
                </tr>
			</thead>				
        </table>
        <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px;" id="scroll_body">
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
                <tbody>
               	<?
               	if ($order_id != '')
               	{		            
					$sql="SELECT c.JOB_NO, C.BOOKING_NO, b.ITEM_CATEGORY, b.FABRIC_SOURCE, b.IS_APPROVED, b.ENTRY_FORM,   
					sum(CASE WHEN c.booking_type=1 THEN c.fin_fab_qnty ELSE 0 END) AS FIN_FAB_QNTY,
					sum(CASE WHEN c.booking_type=1 THEN c.grey_fab_qnty ELSE 0 END) AS GREY_FAB_QNTY
					from wo_booking_dtls c, wo_booking_mst b
					where b.booking_no=c.booking_no and c.booking_type=1 and c.status_active=1 and c.is_deleted=0 and c.po_break_down_id in($order_id) and b.company_id=$company_id and b.status_active=1 and b.is_deleted=0 and b.entry_form=86
					group by c.job_no, c.booking_no, b.item_category, b.fabric_source, b.is_approved, b.entry_form";
		        }    

               	$sql_res = sql_select($sql);	               			
               	$i=1; $total_fin_fab_qnty=$total_grey_fab_qnty=0;
           		foreach ($sql_res as $row) 
           		{
               		if(fmod($i,2)==0) $bgcolor="#E9F3FF"; 
               		else $bgcolor="#FFFFFF";    
               		$process_loss_percent=(($row['GREY_FAB_QNTY']-$row['FIN_FAB_QNTY'])/$row['GREY_FAB_QNTY'])*100;	

               		$fabric_nature = $_SESSION['fabric_nature']; 

               		$variable="<a href='#' onClick=\"generate_worder_report('".$row['BOOKING_NO']."','".$company_id."','".$order_id."','".$row['ITEM_CATEGORY']."','".$row['FABRIC_SOURCE']."','".$row['JOB_NO']."','".$row['IS_APPROVED']."','".$row_id."','".$row['ENTRY_FORM']."','show_fabric_booking_report','".$i."',".$fabric_nature.")\">  ".$row['BOOKING_NO']. "<a/>"; 
               		?>
					<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
						<td width="100" align="center"><p>Main</p></td>
                        <td width="150"><p><? echo $variable; ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row['FIN_FAB_QNTY'],2); ?></p></td>
                        <td width="100" align="right"><p><? echo number_format($row['GREY_FAB_QNTY'],2); ?></p></td>
                        <td width="100" align="center"><p><? echo number_format($process_loss_percent,2); ?></p></td>
                    </tr>
                    <?
                    $i++;
                    $total_fin_fab_qnty += $row['FIN_FAB_QNTY'];
                    $total_grey_fab_qnty += $row['GREY_FAB_QNTY'];
                }
                ?>						
                </tbody>
            </table>

            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>	
					<tr>				
	                    <th width="100"><p>&nbsp;</p></th>
	                    <th width="150"><p>Booking Total</p></th>
	                    <th width="100" align="right"><p><? echo number_format($total_fin_fab_qnty,2); ?></p></th>
	                    <th width="100" align="right"><p><? echo number_format($total_grey_fab_qnty,2); ?></p></th>
	                    <th width="100" align="right"><p>&nbsp;</p></th>
                    </tr>
				</tfoot>
            </table>
        </div>
    </div>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>    
    <?
    exit();	
}

if ($action === 'yarn_issue_knitting_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);	

	list($company_id, $job, $prod_id, $return_prod_id, $transfer_prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));
	$return_prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($return_prod_id,',')))));
	$transfer_prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($transfer_prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID','USER_NAME');
	$yarn_count_arr = return_library_array( "select ID, YARN_COUNT from lib_yarn_count", 'ID','YARN_COUNT');
	
	$table_width = 1150;
	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ['value_total_issue_quantity'],
				col: [10],
				operation: ['sum'],
				write_method: ['innerHTML']
			}
		}
		var tableFilters_2 = {
			col_operation: {
				id: ['value_total_issue_rtn_quantity'],
				col: [10],
				operation: ['sum'],
				write_method: ['innerHTML']
			}
		}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="13" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="13" style="font-size:16px" width="100%" align="center"><strong>Yarn Issue Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">				
				<thead>
					<tr>
						<th colspan="13">Yarn Issue</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="120">Issue No</th>
	                    <th width="70">Issue Date</th>
	                    <th width="120">Issue To</th>
	                    <th width="120">Booking No/ Requisition</th>
	                    <th width="50">Count</th>
	                    <th width="100">Yarn Compositon</th>	                    
	                    <th width="80">Yarn Type</th>
	                    <th width="70">Lot No</th>
	                    <th width="120">Yarn Supplier</th>                	
	                    <th width="80">Issue Qnty</th>                	
	                    <th width="120">Insert Date & Time</th>                	
	                    <th width="70">User Name</th>
                    </tr>             	
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	//echo $order_id.'**'.$prod_ids;
	               	if ($order_id != '' && $prod_ids != '')
	               	{
	               		$sql_yarn_iss ="SELECT a.ISSUE_NUMBER, a.ISSUE_BASIS, a.ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, a.SUPPLIER_ID, a.INSERT_DATE, a.inserted_by as USER_ID, a.BOOKING_NO, b.REQUISITION_NO, c.PO_BREAKDOWN_ID, d.YARN_COUNT_ID, d.YARN_COMP_TYPE1ST, d.YARN_COMP_PERCENT1ST, d.YARN_COMP_TYPE2ND, d.YARN_COMP_PERCENT2ND, d.YARN_TYPE, d.LOT, d.YARN_COUNT_ID, sum(CASE WHEN c.entry_form ='3' THEN c.quantity ELSE 0 END) AS ISSUE_QNTY
	               		from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
	               		where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.entry_form='3' and a.company_id=$company_id and b.transaction_type=2 and b.item_category=1 and c.po_breakdown_id in($order_id) and c.entry_form='3' and c.trans_type=2 and c.issue_purpose!=2 and d.id in($prod_ids) and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.is_deleted=0 and d.status_active=1 
	               		group by a.issue_number, a.issue_basis, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.supplier_id, a.insert_date, a.inserted_by, a.booking_no, b.requisition_no, c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type, d.lot, d.yarn_count_id
	               		order by a.issue_date desc";
	               	}
               		$yarn_issue_data = sql_select($sql_yarn_iss);	               			
               		$i=1;$total_issue_quantity=0;
               		foreach ($yarn_issue_data as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               		$compos = $composition[$row['YARN_COMP_TYPE1ST']]." ".$row['YARN_COMP_PERCENT1ST']." %"." ".$composition[$row['YARN_COMP_TYPE2ND']];
	               		if ($row['KNIT_DYE_SOURCE'] == 1) $issueTo = $companyArr[$row['KNIT_DYE_COMPANY']];
	               		else $issueTo = $supplierArr[$row['KNIT_DYE_COMPANY']];
	               		if ($row['ISSUE_BASIS'] == 3 || $row['ISSUE_BASIS'] == 8) $booking_requisition = $row['REQUISITION_NO'];
	               		elseif ($row['ISSUE_BASIS'] == 1 || $row['ISSUE_BASIS'] == 2) $booking_requisition = $row['BOOKING_NO'];     		
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="120"><p><? echo $row['ISSUE_NUMBER']; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row['ISSUE_DATE']); ?></p></td>
                            <td width="120"><p><? echo $issueTo; ?></p></td>
                            <td width="120"><p><? echo $booking_requisition; ?></p></td>
                            <td width="50"><p><? echo $yarn_count_arr[$row['YARN_COUNT_ID']]; ?></p></td>
                            <td width="100"><p><? echo $compos; ?></p></td>
                            <td width="80"><p><? echo $yarn_type[$row['YARN_TYPE']]; ?></p></td>
                            <td width="70"><p><? echo $row['LOT']; ?></p></td>
                            <td width="120"><p><? echo $supplierArr[$row['SUPPLIER_ID']]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['ISSUE_QNTY'],2,'.',''); ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="70" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_issue_quantity += $row['ISSUE_QNTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
	                    <th width="120"></th>
	                    <th width="120"></th>
	                    <th width="50"></th>
	                    <th width="100"></th>
	                    <th width="80"></th>
	                    <th width="70"></th>
	                    <th width="120" align="right">Total Issue:</th>
	                    <th width="80" align="right" id="value_total_issue_quantity"><? echo number_format($total_issue_quantity,2,'.','');?></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
					</tfoot>
	            </table>
            	<br>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
					<thead>
						<tr>
							<th colspan="13">Yarn Issue Return</th>
						</tr>
						<tr>	
		                    <th width="30">SL</th>
		                    <th width="120">Issue Return No</th>
		                    <th width="70">Return Date</th>
		                    <th width="120">Return From</th>
		                    <th width="120">Booking No/ Requisition</th>
		                    <th width="50">Count</th>
		                    <th width="100">Yarn Compositon</th>
		                    <th width="80">Yarn Type</th>
		                    <th width="70">Lot No</th>
		                    <th width="120">Yarn Supplier</th>
		                    <th width="80">Return Qnty</th>
		                    <th width="120">Insert Date & Time</th>
		                    <th width="70">User Name</th>
	                    </tr>
					</thead>
	            </table>
           
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body_2">
	                <tbody>
	               	<?
	               	if ($order_id != '' && $return_prod_ids != '')
	               	{
		                $sql_yarn_iss_rtn="SELECT a.RECV_NUMBER, a.RECEIVE_BASIS, a.receive_date as RETURN_DATE, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.SUPPLIER_ID, a.BOOKING_NO, a.INSERT_DATE, a.inserted_by as USER_ID, c.PO_BREAKDOWN_ID, d.YARN_COUNT_ID, d.YARN_COMP_TYPE1ST, d.YARN_COMP_PERCENT1ST, d.YARN_COMP_TYPE2ND, d.YARN_COMP_PERCENT2ND, d.YARN_TYPE, d.LOT, d.YARN_COUNT_ID, sum(CASE WHEN c.entry_form ='9' THEN c.quantity ELSE 0 END) AS ISSUE_RTN_QTY
						from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
						where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and a.company_id=$company_id and a.entry_form='9' and b.item_category=1 and b.transaction_type=4 and c.issue_purpose!=2 and c.po_breakdown_id in($order_id) and c.entry_form='9' and c.trans_type=4 and d.id in($return_prod_ids) and d.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
						group by a.recv_number, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, a.supplier_id, a.booking_no, a.insert_date, a.inserted_by, c.po_breakdown_id, d.yarn_count_id, d.yarn_comp_type1st, d.yarn_comp_percent1st, d.yarn_comp_type2nd, d.yarn_comp_percent2nd, d.yarn_type, d.lot, d.yarn_count_id
						order by a.receive_date desc";
					}

               		$sql_yarn_iss_rtn_res=sql_select($sql_yarn_iss_rtn);
               		//echo '<pre>';print_r($sql_yarn_iss_rtn_res);
               			
           			$i=1;$total_issue_rtn_quantity=0;
           			foreach ($sql_yarn_iss_rtn_res as $row)
           			{
               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

               			$compos = $composition[$row['YARN_COMP_TYPE1ST']]." ".$row['YARN_COMP_PERCENT1ST']." %"." ".$composition[$row['YARN_COMP_TYPE2ND']];

               			if ($row['KNITTING_SOURCE'] == 1) $returnFrom = $companyArr[$row['KNITTING_COMPANY']];
	               		else $returnFrom = $supplierArr[$row['KNITTING_COMPANY']];

	               		$booking_requisition = $row['BOOKING_NO'];               		

               			if($row['ISSUE_RTN_QTY'] != 0)
               			{
	               			?>             			
	                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="120"><p><? echo $row['RECV_NUMBER']; ?></p></td>
	                            <td width="70"><p><? echo change_date_format($row['RETURN_DATE']); ?></p></td>
	                            <td width="120"><p><? echo $returnFrom; ?></p></td>
	                            <td width="120"><p><? echo $booking_requisition;?></p></td>
	                            <td width="50"><p><? echo $yarn_count_arr[$row['YARN_COUNT_ID']]; ?></p></td>
	                            <td width="100"><p><? echo $compos; ?></p></td>
	                            <td width="80"><p><? echo $yarn_type[$row['YARN_TYPE']]; ?></p></td>
	                            <td width="70"><p><? echo $row['LOT']; ?></p></td>
	                            <td width="120"><p><? echo $supplierArr[$row['SUPPLIER_ID']]; ?></p></td>
	                            <td width="80" align="right"><p><? echo number_format($row['ISSUE_RTN_QTY'],2,'.',''); ?></p></td>
	                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
	                            <td width="70" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
	                        </tr>
	                    	<?
                    		$i++;
                    		$total_issue_rtn_quantity += $row['ISSUE_RTN_QTY'];
                    	}
                	}
                    ?>							
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
					<thead>
						<tr>
							<th colspan="9">Yarn Transfer Out</th>
						</tr>
						<tr>	
		                    <th width="30">SL</th>
		                    <th width="120">Transfer Id</th>
		                    <th width="70">Transfer Date</th>
		                    <th width="120">Booking No</th>
		                    <th width="100">Yarn Compositon</th>
		                    <th width="70">Lot No</th>
		                    <th width="80">Transfer Out Qnty</th>
		                    <th width="120">Insert Date & Time</th>
		                    <th width="70">User Name</th>
	                    </tr>
					</thead>
	            </table>
           
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body_3">
	                <tbody>
	               	<?
	               	//echo $order_id.'**'.$transfer_prod_ids;
	               	if ($order_id != '' && $transfer_prod_ids != '')
	               	{
						$sql_yarn_trans_out ="SELECT E.TRANSFER_SYSTEM_ID, E.TRANSFER_DATE, e.INSERT_DATE, e.inserted_by as USER_ID, A.BOOKING_NO, a.PO_BREAK_DOWN_ID, f.YARN_COUNT_ID, f.YARN_COMP_TYPE1ST, f.YARN_COMP_PERCENT1ST, 
						f.YARN_COMP_TYPE2ND, f.YARN_COMP_PERCENT2ND, f.YARN_TYPE, f.LOT, f.YARN_COUNT_ID, sum(c.transfer_qnty) as YARN_TRANSFER_QNTY 
						from wo_booking_mst a, fabric_sales_order_mst b, inv_item_transfer_dtls c, inv_item_transfer_mst d, inv_item_transfer_mst e, product_details_master f
						where a.booking_no=b.sales_booking_no and b.job_no=c.fso_no and c.mst_id=d.id and c.mst_id=e.id and c.from_prod_id=f.id and c.item_category=1 and c.to_trans_id=0 
						and a.job_no='$job' and f.id in($transfer_prod_ids) and e.company_id=$company_id and e.entry_form=10 and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
						and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0
						group by e.transfer_system_id, e.transfer_date, e.insert_date, e.inserted_by, a.booking_no, 
						a.po_break_down_id, f.yarn_count_id, f.yarn_comp_type1st, f.yarn_comp_percent1st, f.yarn_comp_type2nd, f.yarn_comp_percent2nd, f.yarn_type, f.lot, f.yarn_count_id
						order by e.transfer_system_id";
	               		// echo $sql_yarn_trans_out;
	               	}
               		$yarn_trans_out_data = sql_select($sql_yarn_trans_out);	               			
               		$i=1;$total_transfer_out_quantity=0;
               		foreach ($yarn_trans_out_data as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               		$compos = $composition[$row['YARN_COMP_TYPE1ST']]." ".$row['YARN_COMP_PERCENT1ST']." %"." ".$composition[$row['YARN_COMP_TYPE2ND']];   		
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="120"><p><? echo $row['TRANSFER_SYSTEM_ID']; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row['TRANSFER_DATE']); ?></p></td>
                            <td width="120"><p><? echo $row['BOOKING_NO']; ?></p></td>
                            <td width="100"><p><? echo $compos; ?></p></td>
                            <td width="70"><p><? echo $row['LOT']; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['YARN_TRANSFER_QNTY'],2,'.',''); ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="70" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_transfer_out_quantity += $row['YARN_TRANSFER_QNTY'];
                    }
                    ?>						
	                </tbody>
	            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<tr>
						<th width="30"></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
	                    <th width="120"></th>
	                    <th width="100"></th>
	                    <th width="70" align="right">Total:</th>
	                    <th width="80" align="right" id="value_total_issue_rtn_quantity"><? echo number_format($total_transfer_out_quantity,2,'.','');?></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
                    </tr>
					<tr>
						<th colspan="6" align="right">Total Issue:</th>
						<th align="right"><? echo number_format(($total_issue_quantity-$total_issue_rtn_quantity)+$total_transfer_out_quantity,2,'.','');?></th>
						<th colspan="2"></th>
					</tr>
				</tfoot>
            </table>
        </div>          
    </fieldset>
    <script>setFilterGrid('table_body',-1,tableFilters);</script>
    <script>setFilterGrid('table_body_2',-1,tableFilters_2);</script>
    <?
	exit();
}

if ($action === 'grey_fabric_receive_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);	
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID','USER_NAME');
	$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from product_details_master where id in($prod_ids)",'ID','PRODUCT_NAME_DETAILS');
	$table_width = 1150;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_grey_rec_qnty'],
					col: [8],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="11" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="11" style="font-size:16px" width="100%" align="center"><strong>Grey Fabric Receive Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="11">Grey Fabric Receive Details</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="130">Receive Id</th>
	                    <th width="80">Receive Date</th>
	                    <th width="80">Challan No</th>
	                    <th width="120">Kniting Com.</th>
	                    <th width="120">Receive Basis</th>
	                    <th width="120">Booking / Program No</th>               
	                    <th width="200">Product Details</th>
	                    <th width="80">Received Qty</th>              	
	                    <th width="120">Insert Date & Time</th>
	                    <th width="70">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	if ($order_id != '' && $prod_ids != '')
	               	{              	
			            $sql="SELECT a.ID, a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, a.INSERT_DATE, a.inserted_by as USER_ID, b.MACHINE_NO_ID, b.PROD_ID, sum(CASE WHEN c.entry_form in (22,58) THEN c.quantity ELSE 0 END) AS GREY_FAB_REC_QTY 
			            from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
			            where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form in (22,58) and c.entry_form in (22,58) and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
			            group by a.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, a.insert_date, a.inserted_by, b.machine_no_id, b.prod_id 
			            order by a.receive_date desc";
			        }    

	               	$sql_res = sql_select($sql);	               			
	               	$i=1; $total_grey_rec_qnty=0;
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		if ($row['KNITTING_SOURCE'] == 1) $issueTo = $companyArr[$row['KNITTING_COMPANY']];
	               		else $issueTo = $supplierArr[$row['KNITTING_COMPANY']];             		 
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['RECV_NUMBER']; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row['RECEIVE_DATE']); ?></p></td>
                            <td width="80"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                            <td width="120"><p><? echo $issueTo; ?></p></td>
                            <td width="120"><p><? echo $receive_basis_arr[$row['RECEIVE_BASIS']]; ?></p></td>
                            <td width="120"><p><? echo $row['BOOKING_NO']; ?></p></td>
                            <td width="200"><p><? echo $product_arr[$row['PROD_ID']]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['GREY_FAB_REC_QTY'],2,'.',''); ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="70" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_grey_rec_qnty += $row['GREY_FAB_REC_QTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="120"></th>
	                    <th width="120"></th>
	                    <th width="120"></th>
	                    <th width="200" align="right">Total:</th>
	                    <th width="80" align="right" id="value_total_grey_rec_qnty"><? echo number_format($total_grey_rec_qnty,2,'.','');?></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'grey_receive_balance_popup')
{
	extract($_REQUEST);
	echo load_html_head_contents('Report Info', '../../../', 1, 1,$unicode,'','');
	

	list($company, $job, $yarnIssue_prodId, $grayFabRecQty_prodId, $buyer_id, $style_ref) = explode('__', $data);
	$yarnIssue_prodIds = implode(',', array_flip(array_flip(explode(',', rtrim($yarnIssue_prodId,',')))));
	$grayFabRecQty_prodIds = implode(',', array_flip(array_flip(explode(',', rtrim($grayFabRecQty_prodId,',')))));

	$companyArr = return_library_array("select id, company_name from lib_company where status_active=1",'id','company_name');
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier where status_active=1",'id','supplier_name');
	$user_arr = return_library_array("select id, user_name from user_passwd",'id','user_name');
	$buyer_arr=return_library_array("select id, short_name from lib_buyer where status_active=1", 'id','short_name');

	$issue_receive_return_arr=array();
	//echo $yarnIssue_prodIds;

	if ($order_id != '' && $yarnIssue_prodIds != '')
	{		
		$sql_yarn_iss="SELECT a.ID, a.ISSUE_NUMBER, a.BOOKING_ID, a.LOCATION_ID, a.IS_APPROVED, a.issue_date as TRANSACTION_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, a.INSERT_DATE, a.inserted_by as USER_ID, c.PO_BREAKDOWN_ID, 2 as TYPE, 
		sum(CASE WHEN c.entry_form ='3' THEN c.quantity ELSE 0 END) AS ISSUE_QNTY 
		from inv_issue_master a, inv_transaction b, order_wise_pro_details c
		where a.id=b.mst_id and b.id=c.trans_id and a.company_id=$company and a.entry_form='3' 
		and b.item_category=1 and b.transaction_type=2 and c.po_breakdown_id in($order_id) and c.prod_id in($yarnIssue_prodIds) and c.entry_form='3' and c.trans_type=2 and c.issue_purpose!=2 
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.id, a.issue_number, a.booking_id, a.location_id, a.is_approved, a.issue_date, a.knit_dye_source, a.knit_dye_company,  a.insert_date, a.inserted_by, c.po_breakdown_id
		order by a.issue_date desc";

		$sql_yarn_iss_res = sql_select($sql_yarn_iss);
		foreach ($sql_yarn_iss_res as $row) 
		{
			if ($row['KNIT_DYE_SOURCE'] == 1)
			{			
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['TRANSACTION_DATE'] = $row['TRANSACTION_DATE'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['COMPANY_SUPPLIER'] = $companyArr[$row['KNIT_DYE_COMPANY']];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['ISSUE_QNTY'] += $row['ISSUE_QNTY'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['INSERT_DATE'] = $row['INSERT_DATE'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['USER_ID'] = $row['USER_ID'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['ID'] = $row['ID'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['BOOKING_ID'] = $row['BOOKING_ID'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['LOCATION_ID'] = $row['LOCATION_ID'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['IS_APPROVED'] = $row['IS_APPROVED'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['TYPE'] = $row['TYPE'];
			}
			else
			{
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['TRANSACTION_DATE'] = $row['TRANSACTION_DATE'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['COMPANY_SUPPLIER'] = $supplierArr[$row['KNIT_DYE_COMPANY']];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['ISSUE_QNTY'] += $row['ISSUE_QNTY'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['INSERT_DATE'] = $row['INSERT_DATE'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['USER_ID'] = $row['USER_ID'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['ID'] = $row['ID'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['BOOKING_ID'] = $row['BOOKING_ID'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['LOCATION_ID'] = $row['LOCATION_ID'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['IS_APPROVED'] = $row['IS_APPROVED'];
				$issue_receive_return_arr[$row['KNIT_DYE_COMPANY']][$row['ISSUE_NUMBER']]['TYPE'] = $row['TYPE'];
			}		
		}
	}	
	//echo '<pre>';print_r($issue_receive_return_arr); die;

	if ($order_id != '' && $yarnIssue_prodIds != '')
	{	
		$sql_yarn_iss_return="SELECT a.RECV_NUMBER, a.BOOKING_ID, a.RECEIVE_BASIS, a.receive_date as TRANSACTION_DATE, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.INSERT_DATE, a.inserted_by as USER_ID, c.PO_BREAKDOWN_ID, 4 as TYPE, 
		sum(CASE WHEN c.entry_form ='9' THEN c.quantity ELSE 0 END) AS ISSUE_RTN_QTY,
		sum(CASE WHEN c.entry_form ='9' THEN c.reject_qty ELSE 0 END) AS REJECT_YARN_REV_QTY
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.trans_id and a.company_id=$company and a.entry_form='9' and b.item_category=1 and b.transaction_type=4 and c.po_breakdown_id in($order_id) and c.prod_id in($yarnIssue_prodIds) and c.entry_form='9' and c.trans_type=4 and c.issue_purpose!=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0      
		group by a.recv_number, a.booking_id, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, a.insert_date, a.inserted_by, b.transaction_type, c.po_breakdown_id
		order by a.receive_date desc";
		$sql_yarn_iss_return_res = sql_select($sql_yarn_iss_return);
		foreach ($sql_yarn_iss_return_res as $row)
		{
			if ($row['knitting_source'] == 1)
			{			
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['TRANSACTION_DATE'] = $row['TRANSACTION_DATE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['company_supplier'] = $companyArr[$row['KNITTING_COMPANY']];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['ISSUE_RTN_QTY'] += $row['ISSUE_RTN_QTY'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['REJECT_YARN_REV_QTY'] += $row['REJECT_YARN_REV_QTY'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['INSERT_DATE'] = $row['INSERT_DATE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['USER_ID'] = $row['USER_ID'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['TYPE'] = $row['TYPE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['BOOKING_ID'] = $row['BOOKING_ID'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['RECEIVE_BASIS'] = $row['RECEIVE_BASIS'];
			}
			else
			{
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['TRANSACTION_DATE'] = $row['TRANSACTION_DATE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['COMPANY_SUPPLIER'] = $supplierArr[$row['KNITTING_COMPANY']];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['ISSUE_RTN_QTY'] += $row['ISSUE_RTN_QTY'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['REJECT_YARN_REV_QTY'] += $row['REJECT_YARN_REV_QTY'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['INSERT_DATE'] = $row['INSERT_DATE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['USER_ID'] = $row['USER_ID'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['TYPE'] = $row['TYPE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['BOOKING_ID'] = $row['BOOKING_ID'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['RECEIVE_BASIS'] = $row['RECEIVE_BASIS'];
			}		
		}
	}	
	//echo '<pre>';print_r($issue_receive_return_arr);

	if ($order_id != '' && $grayFabRecQty_prodIds != '')
	{	
	    $sql_grey_rec="SELECT a.ID, a.RECV_NUMBER, a.BOOKING_NO, a.RECEIVE_BASIS, a.receive_date as TRANSACTION_DATE, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.INSERT_DATE, a.inserted_by as USER_ID, sum(CASE WHEN c.entry_form in (22,58) THEN c.quantity ELSE 0 END) AS GREY_FAB_REC_QTY, sum(NVL(c.reject_qty, 0)) AS GREY_FAB_REJECT_QNTY, 1 as TYPE
        from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
        where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company and a.entry_form in (22,58) and c.entry_form in (22,58) and c.po_breakdown_id in($order_id) and c.prod_id in($grayFabRecQty_prodIds) and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
        group by  a.id, a.recv_number, a.booking_no, a.receive_basis, a.receive_date, a.knitting_source, a.knitting_company, a.insert_date, a.inserted_by
        order by a.receive_date desc";
	    $sql_grey_rec_res = sql_select($sql_grey_rec);
		foreach ($sql_grey_rec_res as $row)
		{
			if ($row['knitting_source'] == 1)
			{			
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['TRANSACTION_DATE'] = $row['TRANSACTION_DATE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['COMPANY_SUPPLIER'] = $companyArr[$row['KNITTING_COMPANY']];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['GREY_FAB_REC_QTY'] += $row['GREY_FAB_REC_QTY'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['GREY_FAB_REJECT_QNTY'] += $row['GREY_FAB_REJECT_QNTY'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['INSERT_DATE'] = $row['INSERT_DATE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['USER_ID'] = $row['USER_ID'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['ID'] = $row['ID'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['BOOKING_NO'] = $row['BOOKING_NO'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['RECEIVE_BASIS'] = $row['RECEIVE_BASIS'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['TYPE'] = $row['TYPE'];
			}
			else
			{
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['TRANSACTION_DATE'] = $row['TRANSACTION_DATE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['COMPANY_SUPPLIER'] = $supplierArr[$row['KNITTING_COMPANY']];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['GREY_FAB_REC_QTY'] += $row['GREY_FAB_REC_QTY'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['GREY_FAB_REJECT_QNTY'] += $row['GREY_FAB_REJECT_QNTY'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['INSERT_DATE'] = $row['INSERT_DATE'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['USER_ID'] = $row['USER_ID'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['ID'] = $row['ID'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['BOOKING_NO'] = $row['BOOKING_NO'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['RECEIVE_BASIS'] = $row['RECEIVE_BASIS'];
				$issue_receive_return_arr[$row['KNITTING_COMPANY']][$row['RECV_NUMBER']]['TYPE'] = $row['TYPE'];
			}		
		}
	}

	$table_width = 1150;
	?>

	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_grand_total_issue_qnty','value_grand_total_issue_rtn_qty','value_grand_total_grey_fab_rec_qty','grand_total_reject_yarn_rev_qty','value_grand_total_grey_fab_reject_qnty'],
					col: [4,5,6,7,8],
					operation: ['sum','sum','sum','sum','sum'],
					write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}

		function set_printHyperlink_data(data,location)
		{
			var show_val_column = "0";
        	var print_with_vat = 0;
        	var r = confirm("Press \"OK\" to open with Comments.\nPress \"Cancel\" to open without Comments.");
        	if (r == true) show_val_column = "1";
        	else show_val_column = "0";
        	var print_action='yarn_issue_print';
        	print_report(data + '*' + show_val_column + '*' + print_with_vat + '*' + location, print_action, '../../../inventory/requires/yarn_issue_controller');
		}			
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong><? echo 'Buyer:&nbsp;'.$buyer_arr[$buyer_id].'&nbsp;Style:&nbsp;'.$style_ref.'&nbsp;Job:&nbsp;'.$job; ?></strong></td>					
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="12">Grey Fabric Received Balance Info</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="140">System Id</th>
	                    <th width="100">Transaction Date</th>
	                    <th width="140">Party Name</th>
	                    <th width="80">Yarn Issue Qnty</th>
	                    <th width="80">Grey Received Qnty</th>
	                    <th width="80">Yarn Issue Return</th>               
	                    <th width="80">Reject Yarn Rcv</th>               
	                    <th width="80">Reject Fabric Rcv</th>
	                    <th width="100">Balance</th>              	
	                    <th width="140">Insert Date & Time</th>
	                    <th width="80">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?               		               			
	               	 
	               	foreach ($issue_receive_return_arr as $company_id=>$company_data)
	               	{
	               		$i=1;
	               		$total_issue_qnty=$total_issue_rtn_qty=$total_grey_fab_rec_qty=$total_reject_yarn_rev_qty=$total_grey_fab_reject_qnty=0;
	               		foreach ($company_data as $system_number => $val) 
	               		{
		               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		               		$balance = $val['ISSUE_QNTY']-($val['GREY_FAB_REC_QTY']+$val['ISSUE_RTN_QTY']+$val['REJECT_YARN_REV_QTY']+$val['GREY_FAB_REJECT_QNTY']);
		               		$hyperlink='';
		               		if ($val['TYPE']==1)
		               		{
		               			$hyperlink="<a href='#'  onclick=\"print_report('".$company."*".$val['ID']."*Knit Grey Fabric Receive*".$val['BOOKING_NO']."*".$val['RECEIVE_BASIS']."','grey_fabric_receive_print','../../../inventory/grey_fabric/requires/grey_fabric_receive_controller')\"> ".$system_number." <a/>";
		               		}
		               		else if ($val['TYPE']==2)
		               		{
		               			
		               			$hyperlink="<a href='#'  onclick=\"set_printHyperlink_data('".$company."*".$system_number."*Yarn Issue*".$val['BOOKING_ID']."*".$val['IS_APPROVED']."*".$val['ID']."','".$val['LOCATION_ID']."')\"> ".$system_number." <a/>";
		               		}
		               		else if ($val['TYPE']==4)
		               		{
		               			$hyperlink="<a href='#'  onclick=\"print_report('".$company."*".$system_number."*Yarn Issue Return*".$val['RECEIVE_BASIS']."*".$val['BOOKING_ID']."','yarn_issue_return_print','../../../inventory/requires/yarn_issue_return_controller')\"> ".$system_number." <a/>";
		               		}
		               		else $hyperlink=$system_number; 
		               		?> 
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30" align="center"><? echo $i; ?></td>								
								<td width="140"><p><? echo $hyperlink; ?></p></td>
	                            <td width="100"><p><? echo change_date_format($val['TRANSACTION_DATE']); ?></p></td>
	                            <td width="140"><p><? echo $issue_receive_return_arr[$company_id][$system_number]['COMPANY_SUPPLIER']; ?></p></td>
	                            <td width="80" align="right"><p><? if ($val['ISSUE_QNTY'] != 0) echo number_format($val['ISSUE_QNTY'],2,'.',''); else echo ""; ?></p></td>
	                            <td width="80" align="right"><p><? if ($val['GREY_FAB_REC_QTY'] != 0) echo number_format($val['GREY_FAB_REC_QTY'],2,'.',''); else echo ""; ?></p></td>
	                            <td width="80" align="right"><p><? if ($val['ISSUE_RTN_QTY'] != 0) echo number_format($val['ISSUE_RTN_QTY'],2,'.',''); else echo ""; ?></p></td>
	                            <td width="80" align="right"><p><? if ($val['REJECT_YARN_REV_QTY'] != 0) echo number_format($val['REJECT_YARN_REV_QTY'],2,'.',''); else echo ""; ?></p></td>
	                            <td width="80" align="right"><p><? if ($val['GREY_FAB_REJECT_QNTY'] != 0) echo number_format($val['GREY_FAB_REJECT_QNTY'],2,'.',''); else ""; ?></p></td>
	                            <td width="100" align="right" title="Yarn Issue Qnty-(Grey Received Qnty+Yarn Issue Return+Reject Fabric Rcv)"><p><? echo number_format($balance,2,'.',''); ?></p></td>
	                            <td width="140" align="center"><p><? echo $val['INSERT_DATE']; ?></p></td>
	                            <td width="80" align="center"><p><? echo $user_arr[$val['USER_ID']]; ?></p></td>
	                        </tr>
		                    <?
		                    $i++;
		                    $total_issue_qnty += $val['ISSUE_QNTY'];
		                    $total_issue_rtn_qty += $val['ISSUE_RTN_QTY'];
		                    $total_grey_fab_rec_qty += $val['GREY_FAB_REC_QTY'];
		                    $total_reject_yarn_rev_qty += $val['REJECT_YARN_REV_QTY'];
		                    $total_grey_fab_reject_qnty += $val['GREY_FAB_REJECT_QNTY'];
	                    }
	                    ?>
	               		<tr class="tbl_bottom">
							<td colspan="4" align="right"><strong>Sub Total:</strong></td>
							<td align="right"><? echo number_format($total_issue_qnty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($total_grey_fab_rec_qty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($total_issue_rtn_qty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($total_reject_yarn_rev_qty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($total_grey_fab_reject_qnty,2,'.',''); ?></td>
							<td align="right" title="Total Yarn Issue Qnty-(Total Grey Received Qnty+Total Yarn Issue Return+Total Reject Yarn Receive Qty+Total Reject Fabric Rcv)"><? echo number_format($total_issue_qnty-($total_grey_fab_rec_qty+$total_issue_rtn_qty+$total_reject_yarn_rev_qty+$total_grey_fab_reject_qnty),2,'.',''); ?></td>
		                    <td colspan="2"></td>
			            </tr>
	                    <?
	                    //echo $total_issue_qnty.'**';
	                    $grand_total_issue_qnty += $total_issue_qnty;
	                    $grand_total_issue_rtn_qty += $total_issue_rtn_qty;
	                    $grand_total_grey_fab_rec_qty += $total_grey_fab_rec_qty;
	                    $grand_total_reject_yarn_rev_qty += $total_reject_yarn_rev_qty;
	                    $grand_total_grey_fab_reject_qnty += $total_grey_fab_reject_qnty;
	               	}	               	        		
                    ?>					
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="140"></th>
	                    <th width="100"></th>
	                    <th width="140" align="right">Grand Total:</th>
	                    <th width="80" align="right" id="value_grand_total_issue_qnty"><? echo number_format($grand_total_issue_qnty,2,'.','');?></th>
	                    <th width="80" align="right" id="value_grand_total_issue_rtn_qty"><? echo number_format($grand_total_issue_rtn_qty,2,'.','');?></th>
	                    <th width="80" align="right" id="value_grand_total_grey_fab_rec_qty"><? echo number_format($grand_total_grey_fab_rec_qty,2,'.','');?></th>
	                    <th width="80" align="right" id="value_grand_total_reject_yarn_rev_qty"><? echo number_format($grand_total_reject_yarn_rev_qty,2,'.','');?></th>
	                    <th width="80" align="right" id="value_grand_total_grey_fab_reject_qnty"><? echo number_format($grand_total_grey_fab_reject_qnty,2,'.','');?></th>
	                    <th width="100" align="right" title="Grand Total Yarn Issue Qnty-(Grand Total Grey Received Qnty+Grand Total Yarn Issue Return+Grand Total Reject Fabric Rcv)"><? echo number_format($grand_total_issue_qnty-($grand_total_issue_rtn_qty+$grand_total_grey_fab_rec_qty+$grand_total_reject_yarn_rev_qty+$grand_total_grey_fab_reject_qnty),2,'.',''); ?></th>
	                    <th width="140"></th>
	                    <th width="80"></th>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
}

if ($action === 'grey_fabric_issue_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');

	$table_width = 850;
	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ['value_total_grey_issue_qnty','total_roll_no'],
				col: [5,6],
				operation: ['sum','sum'],
				write_method: ['innerHTML','innerHTML']
			}
		}
		var tableFilters_2 = {
			col_operation: {
				id: ['value_total_issue_rtn_quantity','total_grey_roll_no'],
				col: [4,5],
				operation: ['sum','sum'],
				write_method: ['innerHTML','innerHTML']
			}
		}	

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="9" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="9" style="font-size:16px" width="100%" align="center"><strong>Grey Issue Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="9">Grey Fabric Issue Details</th>
					</tr>
					<tr>
						<th width="30">SL</th>
	                    <th width="130">Issue ID</th>
	                    <th width="80">Issue Date</th>
	                    <th width="130">Issue Purpose</th>
	                    <th width="130">Issue To</th>
	                    <th width="80">Issue Qnty</th>
	                    <th width="80">No of Roll</th>
	                    <th width="120">Insert Date & Time</th>
	                    <th width="70">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	                $i=1; $issue_to='';
	                if ($order_id != '' && $prod_ids != '')
	                {	
		                $sql="SELECT a.ISSUE_NUMBER, a.ISSUE_DATE, a.ISSUE_PURPOSE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, a.BOOKING_NO, a.BATCH_NO, a.INSERT_DATE, a.inserted_by as USER_ID, b.ROLL_NO, sum(CASE WHEN c.entry_form in(16,61) THEN c.quantity ELSE 0 END) AS GREY_ISSUE_QNTY
		                from inv_issue_master a, inv_grey_fabric_issue_dtls b, order_wise_pro_details c 
		                where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form in(16,61)  and c.entry_form in(16,61) and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		                group by a.id, a.issue_number, a.issue_date, a.issue_purpose, a.knit_dye_source, a.knit_dye_company, a.booking_no, a.batch_no, a.insert_date, a.inserted_by, b.roll_no
		                order by a.id";
	                } 
	                $sql_res=sql_select($sql);             			
	               	$i=1; $total_grey_issue_qnty=$total_roll_no=0;
               		foreach ($sql_res as $row)
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

	               		if($row['KNIT_DYE_SOURCE']==1) $issue_to=$companyArr[$row['KNIT_DYE_COMPANY']];
	                    else if($row['KNIT_DYE_SOURCE']==3) $issue_to=$supplierArr[$row['KNIT_DYE_COMPANY']];
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['ISSUE_NUMBER']; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row['ISSUE_DATE']); ?></p></td>
                            <td width="130"><p><? echo $yarn_issue_purpose[$row['ISSUE_PURPOSE']]; ?></p></td>
                            <td width="130"><p><? echo $issue_to; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['GREY_ISSUE_QNTY'],2,'.',''); ?></p></td>
                            <td width="80" align="right"><p><? echo $row['ROLL_NO']; ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="70" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_grey_issue_qnty += $row['GREY_ISSUE_QNTY'];
	                    $total_roll_no += $row['ROLL_NO'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="80"></th>
	                    <th width="130"></th>
	                    <th width="130">Grand Total:</th>
	                    <th width="80" align="right" id="value_total_grey_issue_qnty"><? echo number_format($total_grey_issue_qnty,2,'.','');?></th>
	                    <th width="80" align="right" id="total_roll_no"><? echo $total_roll_no; ?></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
					</tfoot>
	            </table>
            	<br>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
					<thead>
						<tr>
							<th colspan="9">Grey Issue Return Info</th>
						</tr>
						<tr>				
		                    <th width="30">SL</th>
		                    <th width="130">Return ID</th>
		                    <th width="80">Return Date</th>
		                    <th width="130">Return Purpose</th>
		                    <th width="130">Return Form</th>                                	
		                    <th width="80">Return Qnty(In)</th> 
		                    <th width="80">No of Roll</th>        	
		                    <th width="120">Insert Date & Time</th>                	
		                    <th width="70">User Name</th>
	                    </tr>                	
					</thead>				
	            </table>
           
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body_2">
	                <tbody>
	               	<?
	               	$i=1; $return_from='';
	               	if ($order_id != '')
	               	{	
		               	$sql_grey_issue_return = "SELECT a.RECV_NUMBER, a.RECEIVE_BASIS, a.RECEIVE_PURPOSE, a.KNITTING_SOURCE, a.KNITTING_COMPANY, a.receive_date as RETURN_DATE, a.INSERT_DATE, a.inserted_by as USER_ID, b.NO_OF_ROLL, sum(CASE WHEN c.entry_form in(51,84) THEN c.quantity ELSE 0 END) AS GREY_ISSUE_RETURN_QNTY 
		                from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
		                where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form in(51,84) and c.entry_form in(51,84) and c.po_breakdown_id in($order_id) and c.trans_type=4 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 
		                group by a.recv_number,a.receive_basis,a.receive_purpose,a.knitting_source,a.knitting_company,a.receive_date, a.insert_date, a.inserted_by, b.no_of_roll";
	            	}
               		$sql_grey_issue_return_res = sql_select($sql_grey_issue_return);	               			
           			$i=1;$total_grey_issue_return_qnty=$total_grey_roll_no=0;
           			foreach ($sql_grey_issue_return_res as $row)
           			{
               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";		
               			 
               			if($row['KNITTING_SOURCE']==1) $return_from=$companyArr[$row['KNITTING_COMPANY']];
                        else if($row['KNITTING_SOURCE']==3) $return_from=$supplierArr[$row['KNITTING_COMPANY']];

               			if($row['grey_issue_return_qnty'] != 0)
               			{
	               			?>             			
	                        <tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="130"><p><? echo $row['RECV_NUMBER']; ?></p></td>
	                            <td width="80"><p><? echo change_date_format($row['RETURN_DATE']); ?></p></td>
	                            <td width="130"><p><? echo $yarn_issue_purpose[$row['RECEIVE_PURPOSE']]; ?></p></td>
	                            <td width="130"><p><? echo $return_from; ?></p></td>
	                            <td width="80" align="right"><p><? echo number_format($row['GREY_ISSUE_RETURN_QNTY'],2,'.',''); ?></p></td>
	                            <td width="80" align="right"><p><? echo $row['NO_OF_ROLL']; ?></p></td>
	                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
	                            <td width="70" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
	                        </tr>
	                    	<?
                    		$i++;
                    		$total_grey_issue_return_qnty += $row['GREY_ISSUE_RETURN_QNTY'];
                    		$total_grey_roll_no += $row['NO_OF_ROLL'];
                    	}
                	}
                    ?>							
	                </tbody>
	            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<tr>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="80"></th>
	                    <th width="130"></th>
	                    <th width="130">Total</th>
	                    <th width="80" align="right" id="value_total_issue_rtn_quantity"><? echo number_format($total_issue_rtn_quantity,2,'.','');?></th>
	                    <th width="80" align="right" id="total_grey_roll_no"><? echo $total_grey_roll_no; ?></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
                    </tr>
					<tr>
						<th colspan="5" align="right">Total Grey fabric Issue:</th>
						<th align="right"><? echo number_format(($total_grey_issue_qnty-$total_grey_issue_return_qnty),2,'.','');?></th>
						<th align="right"><? echo $total_roll_no-$total_grey_roll_no; ?></th>
						<th colspan="2"></th>
					</tr>
				</tfoot>
            </table>
        </div>          
    </fieldset>
    <script>setFilterGrid('table_body',-1, tableFilters);</script>
    <script>setFilterGrid('table_body_2',-1, tableFilters_2);</script>
    <?
	exit();
}

if ($action === 'grey_fabric_transfer_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$buyerArr = return_library_array("select ID,BUYER_NAME from lib_buyer",'ID','BUYER_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');

	$table_width = 850;
	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ['value_total_trans_in_qnty'],
				col: [7],
				operation: ['sum'],
				write_method: ['innerHTML']
			}
		}
		tableFilters_2 = {
			col_operation: {
				id: ['value_total_trans_out_quantity'],
				col: [7],
				operation: ['sum'],
				write_method: ['innerHTML']
			}
		}
		

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="9" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="9" style="font-size:16px" width="100%" align="center"><strong>Grey Transfer Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<?
				$sql_po_info=sql_select("SELECT a.JOB_NO, a.BUYER_NAME, a.STYLE_REF_NO 
					from wo_po_details_master a, wo_po_break_down b 
					where a.job_no=b.job_no_mst and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and a.company_name=$company_id and b.id in($order_id) 
					group by a.job_no, a.buyer_name, a.style_ref_no");
				$jobNo=$sql_po_info[0]['JOB_NO'];
				$buyerName=$buyerArr[$sql_po_info[0]['BUYER_NAME']];
				$styleRef=$sql_po_info[0]['STYLE_REF_NO'];
			?>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th style="text-align: left;" colspan="10"><? echo "Buyer:	$buyerName ". " Style: $styleRef ". " Job: $jobNo "; ?></th>
					</tr>
					<tr>
						<th colspan="10">Grey Fabric Transfer in Details</th>
					</tr>
					<tr>
						<th width="30">SL</th>
	                    <th width="130">Transfer ID</th>
	                    <th width="80">Transfer Date</th>
	                    <th width="80">Buyer</th>
	                    <th width="80">Style</th>
	                    <th width="100">From Order/ Booking</th>
	                    <th width="130">Item Description</th>
	                    <th width="80">Transfer Qnty</th>
	                    <th width="120">Insert Date & Time</th>
	                    <th width="70">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	                $i=1; $issue_to='';
	                if ($order_id != '' && $prod_ids != '')
	                {	
	                	$sql = "SELECT  X.TRANSFER_SYSTEM_ID, X.TRANSFER_DATE, X.PROD_ID, X.PRODUCT_NAME_DETAILS, X.FROM_BOOK_ORDER, X.BUYER_NAME, X.STYLE_REF_NO, sum(X.GREY_TRANSIN) as GREY_TRANSIN, X.INSERTED_BY, X.INSERT_DATE
						from (
						 select  c.id, a.transfer_system_id, a.transfer_date, c.prod_id, g.product_name_details, p.po_number as  from_book_order, j.buyer_name, j.style_ref_no,  c.quantity as grey_transin, a.inserted_by, a.insert_date
						 from order_wise_pro_details c, inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_po_break_down p, wo_po_details_master j, product_details_master g
						 where c.trans_type in (5) and c.dtls_id=b.id and c.prod_id=g.id and b.mst_id=a.id  and a.from_order_id=p.id and p.job_id=j.id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.entry_form in(83)
						 union all
						  select c.id,a.transfer_system_id, a.transfer_date, b.to_prod_id as prod_id, g.product_name_details,  d.booking_no as from_book_order, d.buyer_id as buyer_name,f.style_ref_no,  c.quantity as grey_transin, a.inserted_by, a.insert_date
						 from order_wise_pro_details c, inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_non_ord_samp_booking_mst d, wo_non_ord_samp_booking_dtls e left join sample_development_mst f on e.style_id=f.id, product_details_master g
						 where c.trans_type in (5) and c.dtls_id=b.id and b.to_prod_id=g.id and b.mst_id=a.id and a.from_order_id=d.id  and d.booking_no=e.booking_no and d.booking_type=4 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.entry_form in(183) 
						 group by c.id,a.transfer_system_id, a.transfer_date, b.to_prod_id, g.product_name_details,  d.booking_no, d.buyer_id ,f.style_ref_no,  c.quantity, a.inserted_by, a.insert_date
						 union all
						 select c.id, a.transfer_system_id, a.transfer_date, b.to_prod_id as prod_id,g.product_name_details,  p.po_number as from_book_order, j.buyer_name, j.style_ref_no, c.quantity as grey_transin, a.inserted_by, a.insert_date
						 from order_wise_pro_details c,  inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_po_break_down p, wo_po_details_master j, product_details_master g
						 where c.trans_type in (5) and c.dtls_id=b.id and b.to_prod_id=g.id and b.mst_id=a.id and b.from_order_id =p.id and p.job_id=j.id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.entry_form in(82)
						 ) x
						 group by x.transfer_system_id, x.transfer_date, x.prod_id, x.product_name_details, x.from_book_order, x.buyer_name, x.style_ref_no,  x.inserted_by, x.insert_date order by x.transfer_system_id";
						 $sql_res=sql_select($sql); 
	                }

	               	$i=1; $total_grey_issue_qnty=$total_roll_no=0;
               		foreach ($sql_res as $row)
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['TRANSFER_SYSTEM_ID']; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row['TRANSFER_DATE']); ?></p></td>
                            <td width="80"><p><? echo $buyerArr[$row['BUYER_NAME']]; ?></p></td>
                            <td width="80"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
                            <td width="100" align="right"><p><? echo $row['FROM_BOOK_ORDER']; ?></p></td>
                            <td width="130" align="right"><p><? echo $row['PRODUCT_NAME_DETAILS']; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['GREY_TRANSIN'],2,'.',''); ?></p></td>
                            
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="70" align="center"><p><? echo $user_arr[$row['INSERTED_BY']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_grey_trans_in_qnty += $row['GREY_TRANSIN'];
	                    $total_roll_no += $row['ROLL_NO'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="80"></th>

	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="100"></th>
	                    <th width="130">Total:</th>
	                    <th width="80" align="right" id="value_total_trans_in_qnty"><? echo number_format($total_grey_trans_in_qnty,2,'.','');?></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
					</tfoot>
	            </table>
            	<br>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
					<thead>
						<tr>
							<th colspan="10">Grey Transfer Out Info</th>
						</tr>
						<tr>				
		                    <th width="30">SL</th>
		                    <th width="130">Transfer ID</th>
		                    <th width="80">Transfer Date</th>
		                    <th width="80">Buyer</th>
		                    <th width="80">Style</th>
		                    <th width="100">To Order/ Booking</th>
		                    <th width="130">Item Description</th>
		                    <th width="80">Transfer Qnty</th>
		                    <th width="120">Insert Date & Time</th>
		                    <th width="70">User Name</th>
	                    </tr>                	
					</thead>				
	            </table>
           
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body_2">
	                <tbody>
	               	<?
	               	$i=1; $return_from='';
	               	if ($order_id != '')
	               	{	
		               	$sql_trans_out = "SELECT X.TRANSFER_SYSTEM_ID, X.TRANSFER_DATE, X.PROD_ID, X.PRODUCT_NAME_DETAILS, X.TO_BOOK_ORDER, X.BUYER_NAME, X.STYLE_REF_NO, sum(X.GREY_TRANSOUT) as GREY_TRANSOUT, X.INSERTED_BY, X.INSERT_DATE 
					from ( select c.id, a.transfer_system_id, a.transfer_date, c.prod_id, g.product_name_details, p.po_number as to_book_order, j.buyer_name, j.style_ref_no, c.quantity as grey_transout, a.inserted_by, a.insert_date
					from order_wise_pro_details c, inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_po_break_down p, wo_po_details_master j, product_details_master g where c.trans_type in (6) and c.dtls_id=b.id and c.prod_id=g.id and b.mst_id=a.id and a.to_order_id=p.id and p.job_id=j.id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.entry_form in(83) 
					union all 
					select c.id,a.transfer_system_id, a.transfer_date, b.from_prod_id as prod_id, g.product_name_details, d.booking_no as to_book_order, 
					d.buyer_id as buyer_name,f.style_ref_no, c.quantity as grey_transout, a.inserted_by, a.insert_date 
					from order_wise_pro_details c, inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_non_ord_samp_booking_mst d, wo_non_ord_samp_booking_dtls e left join sample_development_mst f on e.style_id=f.id, product_details_master g where c.trans_type in (6) and c.dtls_id=b.id and b.from_prod_id=g.id and b.mst_id=a.id and a.to_order_id=d.id and d.booking_no=e.booking_no and d.booking_type=4 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.entry_form in(110) group by c.id,a.transfer_system_id, a.transfer_date, b.from_prod_id, g.product_name_details, d.booking_no, d.buyer_id ,f.style_ref_no, c.quantity, a.inserted_by, a.insert_date 
					union all 
					select c.id, a.transfer_system_id, a.transfer_date, b.from_prod_id as prod_id,g.product_name_details, p.po_number as to_book_order, j.buyer_name, j.style_ref_no, c.quantity as grey_transout, a.inserted_by, a.insert_date 
					from order_wise_pro_details c, inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_po_break_down p, wo_po_details_master j, product_details_master g where c.trans_type in (6) and c.dtls_id=b.id and b.to_prod_id=g.id and b.mst_id=a.id and b.to_order_id =p.id and p.job_id=j.id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.entry_form in(82) ) x
					group by x.transfer_system_id, x.transfer_date, x.prod_id, x.product_name_details, x.to_book_order, x.buyer_name, x.style_ref_no, x.inserted_by, x.insert_date order by x.transfer_system_id ";
	            	}
               		$sql_trans_out_arr = sql_select($sql_trans_out);	               			
           			$total_grey_trans_out_qnty=0;
           			foreach ($sql_trans_out_arr as $row)
           			{
               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['TRANSFER_SYSTEM_ID']; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row['TRANSFER_DATE']); ?></p></td>
                            <td width="80"><p><? echo $buyerArr[$row['BUYER_NAME']]; ?></p></td>
                            <td width="80"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
                            <td width="100" align="right"><p><? echo $row['TO_BOOK_ORDER']; ?></p></td>
                            <td width="130" align="right"><p><? echo $row['PRODUCT_NAME_DETAILS']; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['GREY_TRANSOUT'],2,'.',''); ?></p></td>
                            
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="70" align="center"><p><? echo $user_arr[$row['INSERTED_BY']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_grey_trans_out_qnty += $row['GREY_TRANSOUT'];
                	}
                    ?>							
	                </tbody>
	            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<tr>
	                    <th width="30">&nbsp;</th>
	                    <th width="130">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="130">&nbsp;</th>
	                    <th width="80" id="value_total_trans_out_quantity">&nbsp;</th>
	                    <th width="120">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
                    </tr>
					<tr>
						<th colspan="7" align="right">Net Transfer:</th>
						<th align="right"><? echo number_format(($total_grey_trans_in_qnty-$total_grey_trans_out_qnty),2,'.','');?></th>
						<th align="right"><? echo $total_roll_no-$total_grey_roll_no; ?></th>
						<th colspan="2"></th>
					</tr>
				</tfoot>
            </table>
        </div>          
    </fieldset>
    <script>setFilterGrid('table_body',-1, tableFilters);</script>
    <script>setFilterGrid('table_body_2',-1, tableFilters_2);</script>
    
    <?
	exit();
}

if($action==='finish_fabric_receive_popup___backup-04-12-2021')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	// echo "<pre>";print_r($_REQUEST);die;
	extract($_REQUEST);

	list($company_id, $job, $finishRec_prodID, $finishRec_batchID, $finishRecIssueReturn_prodID, $finishSalesRollRec_prodID) = explode('__', $data);
	$finishRec_prodIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRec_prodID,',')))));
	$finishSalesRollRec_prodIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishSalesRollRec_prodID,',')))));
	$finishRec_batchIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRec_batchID,',')))));
	$finishRecIssueReturn_prodIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRecIssueReturn_prodID,',')))));
	//echo $prod_ids;

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');
	$color_arr = return_library_array( "select ID, COLOR_NAME from lib_color", 'id', 'COLOR_NAME');

	if ($finishRec_prodIDs != '' || $finishRec_batchIDs != '' || $finishSalesRollRec_prodIDs!='')
	{	
		if ($finishRec_prodIDs !='' && $finishRecIssueReturn_prodIDs !='') 
		{
			$all_prodIDs = $finishRec_prodIDs.','.$finishRecIssueReturn_prodIDs.','.$finishSalesRollRec_prodIDs;
		}
		else
		{
			$all_prodIDs = $finishSalesRollRec_prodIDs;
		}
		
		$batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($finishRec_batchIDs) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');
		$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from 
			product_details_master where id in(".rtrim($all_prodIDs,',').") and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');	
	}
	
	if ($order_id != '')
	{
		$sql_process="select JOB_NO, BOOKING_NO, PROCESS from wo_booking_dtls where job_no='".$job."' and po_break_down_id in($order_id) and booking_type=3 and status_active=1 and is_deleted=0";
		$sql_process_res=sql_select($sql_process);
		$process_arr=array();
		foreach ($sql_process_res as $value) {
			$process_arr[$value['BOOKING_NO']] = $value['PROCESS'];
		}
	}
	//echo '<pre>';print_r($process_arr);		

	$table_width = 1180;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_grey_used_qty','value_total_finish_rec_qty'],
					col: [7,8],
					operation: ['sum','sum'],
					write_method: ['innerHTML','innerHTML']
				}
			}
		var tableFilters_2 = {
				col_operation: {
					id: ['value_total_receive_return_qnty'],
					col: [7],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="width:<? echo $table_width; ?>px;">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">     
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="14" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="14" style="font-size:16px" width="100%" align="center"><strong>Finish Fabric Receive  Statement</strong>
						</td>
					</tr>
				</thead>
			</table>   	
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="14"><strong>Finished Fabric Receive Details</strong></th>
					</tr>
					<tr>
                		<th width="30">SL</th>
                    	<th width="115">System ID</th>
                    	<th width="80">Receive Date</th>
                    	<th width="60">Challan No</th>
                    	<th width="120">Dyeing Company</th>
                    	<th width="80">Color</th>
                    	<th width="80">Batch No</th>
                    	<th width="60">Grey Used</th>
                    	<th width="80">Fin. Rcv. Qty.</th>
                    	<th width="50">Process Loss.%</th>
                    	<th width="170">Fabric Description</th>
                    	<th width="80">Fabric Procees Type</th>
                    	<th width="115">Insert Date & Time</th>
                    	<th width="60">User Name</th>
                    </tr>	
				</thead>
            </table>
            <div style="width:<? echo $table_width+20; ?>px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
                	<tbody>
                    <?
                    $i=1; $total_grey_used_qty=$total_finish_rec_qty=$total_process_loss=0;
                    $fab_process_type='';
                    if ($finishRec_prodIDs=="")  {$finishRec_prodIDs='0';}
                    if ($finishSalesRollRec_prodIDs=="")  {$finishSalesRollRec_prodIDs='0';}
                    // echo $order_id.'=A='.$finishRec_prodIDs.'=B='.$finishRec_batchIDs.'=C='.$finishSalesRollRec_prodIDs;
                    if ($order_id != '' && $finishRec_prodIDs != '' && $finishRec_batchIDs != '' && $finishSalesRollRec_prodIDs != '')
                    {	
	                    $sql="SELECT a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, a.INSERT_DATE, a.inserted_by as USER_ID, b.BATCH_ID, b.PROD_ID, b.GSM, b.WIDTH, b.COLOR_ID, b.RACK_NO, b.grey_used_qty as GREY_USED_QTY, sum(NVL(c.quantity, 0)) as QUANTITY, 0 AS BARCODE_NO
	                    from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c 
	                    where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form in (37,68,17) and b.batch_id in($finishRec_batchIDs) and c.entry_form in (37,68,17) and c.po_breakdown_id in($order_id) and c.prod_id in($finishRec_prodIDs) and c.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	                    group by a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, a.insert_date, a.inserted_by, b.batch_id, b.prod_id, b.gsm, b.width, b.color_id, b.rack_no, b.grey_used_qty
	                    
	                    UNION ALL
 
						SELECT a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, a.insert_date, a.inserted_by as USER_ID, b.BATCH_ID, 
						 b.PROD_ID, b.GSM, b.WIDTH, b.COLOR_ID, b.RACK_NO, b.grey_used_qty as GREY_USED_QTY, sum(c.QNTY) as QUANTITY, C.BARCODE_NO
						from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, fabric_sales_order_mst d
						where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.id=c.mst_id  and a.company_id=$company_id and a.entry_form in (68) and c.entry_form in (68) and a.status_active=1
						and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and c.is_sales=1
						and b.batch_id in($finishRec_batchIDs) and b.PROD_ID in($finishSalesRollRec_prodIDs)
						group by a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, a.insert_date, a.inserted_by, b.BATCH_ID, b.PROD_ID, b.GSM, b.WIDTH, b.COLOR_ID, b.RACK_NO, b.grey_used_qty, C.BARCODE_NO";
						// order by a.receive_date desc
                	}
                    $result=sql_select($sql);
                    $finish_barcode_arr=array();
					foreach($result as $row)
					{
						$finish_barcode_arr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
					}
					// print_r($finish_barcode_arr);
					$finish_barcode_arr = array_filter($finish_barcode_arr);
					if(count($finish_barcode_arr)>0)
					{
						$all_barcode = implode(",", $finish_barcode_arr);
				        $all_barcode_cond=""; $barcodeCond="";
				        if($db_type==2 && count($finish_barcode_arr)>999)
				        {
				        	$all_finish_barcode_arr_chunk=array_chunk($finish_barcode_arr,999) ;
				        	foreach($all_finish_barcode_arr_chunk as $chunk_arr)
				        	{
				        		$chunk_arr_value=implode(",",$chunk_arr);
				        		$barcodeCond.="  c.barcode_no in($chunk_arr_value) or ";
				        	}

				        	$all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
				        }
				        else
				        {
				        	$all_barcode_cond=" and c.barcode_no in($all_barcode)";
				        }
						$roll_data_array=sql_select("SELECT C.QNTY, C.REJECT_QNTY, C.BARCODE_NO from pro_roll_details c where c.entry_form=66 and c.is_sales=1 and c.status_active=1 $all_barcode_cond group by c.qnty, c.reject_qnty, c.barcode_no");
				    }
				    $greyUsedRollQty_arr=array();
					foreach($roll_data_array as $row)
					{
						$greyUsedRollQty_arr[$row['BARCODE_NO']]['QNTY'] += $row['QNTY'];
					}
					// echo '<pre>';print_r($greyUsedRollQty_arr);die;
        			foreach($result as $row)
                    {
                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

                        if($row['KNITTING_SOURCE']==1)
                        {
                        	$knitting_company = $companyArr[$row['KNITTING_COMPANY']];

                        } else {
                        	$knitting_company = $supplierArr[$row['KNITTING_COMPANY']];
                        }
                        $grey_used_qty=$row['GREY_USED_QTY']+$greyUsedRollQty_arr[$row['BARCODE_NO']]['QNTY'];
                        $process_loss = 100 - (($row['QUANTITY']/$grey_used_qty)*100);

                        if($row['RECEIVE_BASIS']==11)
                        {
                        	$fab_process_id = $process_arr[$row['BOOKING_NO']];
                        }

                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                          	<td width="30" align="center"><? echo $i; ?></td>
		                    <td width="115"><p><? echo $row['RECV_NUMBER']; ?></p></td>
		                    <td width="80"><p><? echo change_date_format($row['RECEIVE_DATE']); ?></p></td>
		                    <td width="60"><p><? echo $row['CHALLAN_NO']; ?></p></td>
		                    <td width="120"><p><? echo $knitting_company; ?></p></td>
		                    <td width="80"><p><? echo $color_arr[$row['COLOR_ID']];?></p></td>
		                    <td width="80"><p><? echo $batch_arr[$row['BATCH_ID']]; ?></p></td>
		                    <td width="60" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
		                    <td width="80" align="right"><p><? echo number_format($row['QUANTITY'],2); ?></p></td>
		                    <td width="50" align="right"><p><? echo number_format($process_loss,2); ?></p></td>
		                    <td width="170" align="center"><p><? echo $product_arr[$row['PROD_ID']]; ?></p></td>
		                    <td width="80"><p><? echo $conversion_cost_head_array[$fab_process_id]; ?></p></td>
		                    <td width="115"><p><? echo $row['INSERT_DATE']; ?></p></td>
		                    <td width="60"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
                    	<?
                    	$i++;
                    	$total_grey_used_qty += $row['GREY_USED_QTY']+$greyUsedRollQty_arr[$row['BARCODE_NO']]['QNTY'];
                    	$total_finish_rec_qty += $row['QUANTITY'];
                    	$total_process_loss = 100 - (($total_finish_rec_qty/$total_grey_used_qty)*100);
                    }
                    ?>
                    </tbody>
                </table>                
            
	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">  
	                <tfoot>
	                    <th width="30"></th>
                    	<th width="115"></th>
                    	<th width="80"></th>
                    	<th width="60"></th>
                    	<th width="120"></th>
                    	<th width="80"></th>
                    	<th width="80" align="right">Total</th>
                    	<th width="60" align="right" id="value_total_grey_used_qty"><? echo number_format($total_grey_used_qty,2); ?></th>
                    	<th width="80" align="right" id="value_total_finish_rec_qty"><? echo number_format($total_finish_rec_qty,2); ?></th>
                    	<th width="50" align="right"><? echo number_format($total_process_loss,2); ?></th>
                    	<th width="170"></th>
                    	<th width="80"></th>
                    	<th width="115"></th>
                    	<th width="60"></th>
	                </tfoot>
	            </table>
	            <br>
            	<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
					<thead>
	                	<tr>
	                    	<th colspan="11">Finished Fabric Receive Return</th>
	                    </tr>
	                    <tr>
	                    	<th width="30">SL</th>
	                        <th width="120">Recieve Rtn No</th>
	                        <th width="80">Return Date</th>
	                        <th width="120">Recieve MRR No</th>
	                        <th width="120">Dyeing Company</th>
	                        <th width="80">Color</th>
	                        <th width="100">Batch No</th>
	                        <th width="80">Return Qty</th>
	                        <th width="200">Item Description</th>
	                        <th width="150">Insert Date & Time</th>
	                        <th width="90">User Name</th>
	                    </tr>
					</thead>
				</table>
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body_2">
	                <tbody>
                	<?
                	$i=1;$total_receive_return_qnty=0;
					//, inv_receive_master e 
					if($order_id != '' && $finishRecIssueReturn_prodIDs != '')
					{
						$sql_return="SELECT a.issue_number as RECEIVE_RETURN_NO, a.issue_date as RETURN_DATE, a.RECEIVED_MRR_NO, a.COMPANY_ID, a.INSERT_DATE, a.inserted_by as USER_ID, b.PROD_ID, b.BATCH_ID_FROM_FISSUERTN, sum(NVL(c.quantity, 0)) as QUANTITY, d.PRODUCT_NAME_DETAILS, c.COLOR_ID
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and c.prod_id=d.id and a.company_id=$company_id and a.entry_form in (46,202) and b.transaction_type=3 and c.entry_form in (46,202) and c.trans_type=3 and c.po_breakdown_id in($order_id) and d.id in($finishRecIssueReturn_prodIDs) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
						group by a.issue_number, a.issue_date, a.received_mrr_no, a.company_id, a.insert_date, a.inserted_by, b.prod_id, b.batch_id_from_fissuertn, d.product_name_details, c.color_id";
					}
                	$sql_return_res=sql_select($sql_return);
					foreach($sql_return_res as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";						
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="30"><? echo $i; ?></td>
	                    	<td width="120"><p><? echo $row['RECEIVE_RETURN_NO']; ?></p></td>
	                        <td width="80" align="center"><p><? echo change_date_format($row['RETURN_DATE']); ?></p></td>
	                        <td width="120"><p><? echo $row['RECEIVED_MRR_NO']; ?></p></td>
	                        <td width="120"><p><? echo $companyArr[$row['COMPANY_ID']]; ?></p></td>
	                        <td width="80"><p><? echo $color_arr[$row['COLOR_ID']]; ?></p></td>
	                        <td width="100"><p><? echo $batch_arr[$row['BATCH_ID_FROM_FISSUERTN']]; ?></p></td>
	                        <td width="80" align="right"><p><? echo number_format($row['QUANTITY'],2); ?></p></td>        
	                        <td width="200" align="center"><p><? echo $row['PRODUCT_NAME_DETAILS']; ?></p></td>
	                        <td width="150"><p><? echo $row['INSERT_DATE']; ?></p></td>
	                        <td width="90"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
	                    </tr>
	                	<?
	                	$i++;
						$total_receive_return_qnty += $row['QUANTITY'];	                	
	                }
	                ?>
            		</tbody>
            	</table>
        	</div>
        	<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
	                <tr style="font-weight:bold">
	                	<th width="30"></th>
                        <th width="120"></th>
                        <th width="80"></th>
                        <th width="120"></th>
                        <th width="120"></th>
                        <th width="80"></th>
	                    <th width="100" align="right">Total Return:</th>
	                    <th width="80" align="right" id="value_total_receive_return_qnty"><? echo number_format($total_receive_return_qnty,2); ?></th>
	                    <th width="200"></th>
	                    <th width="150"></th>
	                    <th width="90"></th>
	                </tr>
	                <tr>
	                    <th colspan="7" align="right">Net Receive:</th>
	                    <th width="80" align="right"><? echo number_format(($total_finish_rec_qty-$total_receive_return_qnty),2);?></th>
	                    <th width="200"></th>
	                    <th width="150"></th>
	                    <th width="90"></th>
	                </tr>
	            </tfoot>    
            </table>	
        </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>   
	<script>setFilterGrid('table_body_2',-1, tableFilters_2);</script>   
	<?
	exit();
}

if($action==='finish_fabric_receive_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	// echo "<pre>";print_r($_REQUEST);die;
	extract($_REQUEST);

	list($company_id, $job, $finishRec_prodID, $finishRec_batchID, $finishRecIssueReturn_prodID, $finishSalesRollRec_prodID) = explode('__', $data);
	// echo $finishRec_prodID.'<br>';
	$finishRec_prodIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRec_prodID,',')))));
	// echo $finishRec_prodIDs;die;
	$finishSalesRollRec_prodIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishSalesRollRec_prodID,',')))));
	$finishRec_batchIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRec_batchID,',')))));
	$finishRecIssueReturn_prodIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRecIssueReturn_prodID,',')))));
	//echo $prod_ids;

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');
	$color_arr = return_library_array( "select ID, COLOR_NAME from lib_color", 'id', 'COLOR_NAME');
	// echo $finishRec_prodIDs.'<br>';
	if ($finishRec_prodIDs != '' || $finishRec_batchIDs != '' || $finishSalesRollRec_prodIDs!='')
	{	
		if ($finishRec_prodIDs !='' || $finishRecIssueReturn_prodIDs !='') 
		{
			$all_prodIDs = $finishRec_prodIDs.','.$finishRecIssueReturn_prodIDs.','.$finishSalesRollRec_prodIDs;
			// echo $all_prodIDs.'==<br>';
		}
		else
		{
			$all_prodIDs = $finishSalesRollRec_prodIDs;
			// echo $all_prodIDs;
		}
		
		$batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($finishRec_batchIDs) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');
		$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from 
			product_details_master where id in(".rtrim($all_prodIDs,',').") and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');
		$product_gsm_arr=return_library_array( "select ID, GSM from product_details_master where id in(".rtrim($all_prodIDs,',').") and status_active=1 and is_deleted=0", 'ID', 'GSM');
		$product_dia_arr=return_library_array( "select ID, DIA_WIDTH from product_details_master where id in(".rtrim($all_prodIDs,',').") and status_active=1 and is_deleted=0", 'ID', 'DIA_WIDTH');



		$determinaArr = return_library_array("select id,construction from  lib_yarn_count_determina_mst where status_active=1 and is_deleted=0","id","construction");

		$product_determination_arr=return_library_array( "select ID, DETARMINATION_ID from 
			product_details_master where id in(".rtrim($all_prodIDs,',').") and status_active=1 and is_deleted=0", 'ID', 'DETARMINATION_ID');
	

			$composition_arr=array();
			$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
			$data_array=sql_select($sql_deter);
			if(count($data_array)>0)
			{
				foreach( $data_array as $row )
				{
					if(array_key_exists($row[csf('id')],$composition_arr))
					{
						$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
					else
					{
						$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
					}
				}
			}	
	}
	
	if ($order_id != '')
	{
		$sql_process="select JOB_NO, BOOKING_NO, PROCESS from wo_booking_dtls where job_no='".$job."' and po_break_down_id in($order_id) and booking_type=3 and status_active=1 and is_deleted=0";
		$sql_process_res=sql_select($sql_process);
		$process_arr=array();
		foreach ($sql_process_res as $value) {
			$process_arr[$value['BOOKING_NO']] = $value['PROCESS'];
		}
	}
	//echo '<pre>';print_r($process_arr);		

	$table_width = 1180;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_grey_used_qty','value_total_finish_rec_qty'],
					col: [7,8],
					operation: ['sum','sum'],
					write_method: ['innerHTML','innerHTML']
				}
			}
		var tableFilters_2 = {
				col_operation: {
					id: ['value_total_receive_return_qnty'],
					col: [7],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="width:<? echo $table_width; ?>px;">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">     
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="14" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="14" style="font-size:16px" width="100%" align="center"><strong>Finish Fabric Receive  Statement</strong>
						</td>
					</tr>
				</thead>
			</table>   	
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th colspan="14"><strong>Finished Fabric Receive Details</strong></th>
					</tr>
					<tr>
                		<th width="30">SL</th>
                    	<th width="115">System ID</th>
                    	<th width="80">Receive Date</th>
                    	<th width="60">Challan No</th>
                    	<th width="120">Dyeing Company</th>
                    	<th width="80">Color</th>
                    	<th width="80">Batch No</th>
                    	<th width="60">Grey Used</th>
                    	<th width="80">Fin. Rcv. Qty.</th>
                    	<th width="50">Process Loss.%</th>
                    	<th width="170">Fabric Description</th>
                    	<th width="80">Fabric Procees Type</th>
                    	<th width="115">Insert Date & Time</th>
                    	<th width="60">User Name</th>
                    </tr>	
				</thead>
            </table>
            <div style="width:<? echo $table_width+20; ?>px; max-height:330px; overflow-y:scroll" id="scroll_body">
                <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
                	<tbody>
                    <?
                    $i=1; $total_grey_used_qty=$total_finish_rec_qty=$total_process_loss=0;
                    $fab_process_type='';
                    if ($finishRec_prodIDs=="")  {$finishRec_prodIDs='0';}
                    if ($finishSalesRollRec_prodIDs=="")  {$finishSalesRollRec_prodIDs='0';}
                    // echo $order_id.'=A='.$finishRec_prodIDs.'=B='.$finishRec_batchIDs.'=C='.$finishSalesRollRec_prodIDs;
                    if ($order_id != '' && $finishRec_prodIDs != '' && $finishRec_batchIDs != '' && $finishSalesRollRec_prodIDs != '')
                    {	
	                    $sql="SELECT a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, a.INSERT_DATE, a.inserted_by as USER_ID, b.BATCH_ID, b.PROD_ID, b.GSM, b.WIDTH, b.COLOR_ID, b.RACK_NO, b.grey_used_qty as GREY_USED_QTY, sum(c.quantity) as QUANTITY, b.BARCODE_NO,A.ENTRY_FORM 
	                    from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c 
	                    where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form in (37,68,17) and b.batch_id in($finishRec_batchIDs) and c.entry_form in (37,68,17) and c.po_breakdown_id in($order_id) and c.prod_id in($finishRec_prodIDs) and c.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, a.INSERT_DATE, a.inserted_by, b.BATCH_ID, b.PROD_ID, b.GSM, b.WIDTH, b.COLOR_ID, b.RACK_NO, b.grey_used_qty, b.BARCODE_NO,A.ENTRY_FORM  
	                    UNION ALL
						SELECT a.RECV_NUMBER, a.RECEIVE_DATE, a.RECEIVE_BASIS, a.BOOKING_NO, a.KNITTING_SOURCE, a.CHALLAN_NO, a.KNITTING_COMPANY, a.insert_date, a.inserted_by as USER_ID, b.BATCH_ID, 
						 b.PROD_ID, b.GSM, b.WIDTH, b.COLOR_ID, b.RACK_NO, b.grey_used_qty as GREY_USED_QTY, c.QNTY as QUANTITY, C.BARCODE_NO,A.ENTRY_FORM 
						from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, fabric_sales_order_mst d
						where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.id=c.mst_id  and a.company_id=$company_id and a.entry_form in (68) and c.entry_form in (68) and a.status_active=1
						and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and c.is_sales=1
						and b.batch_id in($finishRec_batchIDs) and b.PROD_ID in($finishSalesRollRec_prodIDs)";
						// order by a.receive_date desc
						// echo $sql;
                	}// and a.RECV_NUMBER='BPKW-FFRR-21-00327'
                    $result=sql_select($sql);
                    $finish_barcode_arr=array();
					foreach($result as $row)
					{
						$finish_barcode_arr[$row['BARCODE_NO']] = $row['BARCODE_NO'];
					}
					// print_r($finish_barcode_arr);
					$finish_barcode_arr = array_filter($finish_barcode_arr);
					if(count($finish_barcode_arr)>0)
					{
						$all_barcode = implode(",", $finish_barcode_arr);
				        $all_barcode_cond=""; $barcodeCond="";
				        if($db_type==2 && count($finish_barcode_arr)>999)
				        {
				        	$all_finish_barcode_arr_chunk=array_chunk($finish_barcode_arr,999) ;
				        	foreach($all_finish_barcode_arr_chunk as $chunk_arr)
				        	{
				        		$chunk_arr_value=implode(",",$chunk_arr);
				        		$barcodeCond.="  c.barcode_no in($chunk_arr_value) or ";
				        	}

				        	$all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
				        }
				        else
				        {
				        	$all_barcode_cond=" and c.barcode_no in($all_barcode)";
				        }
						$roll_data_array=sql_select("SELECT C.QNTY, C.REJECT_QNTY, C.BARCODE_NO from pro_roll_details c where c.entry_form=66 and c.is_sales=1 and c.status_active=1 $all_barcode_cond group by c.qnty, c.reject_qnty, c.barcode_no");
						//echo "SELECT C.QNTY, C.REJECT_QNTY, C.BARCODE_NO from pro_roll_details c where c.entry_form=66 and c.is_sales=1 and c.status_active=1 $all_barcode_cond group by c.qnty, c.reject_qnty, c.barcode_no";
				    }
				    $greyUsedRollQty_arr=array();
					foreach($roll_data_array as $row)
					{
						$greyUsedRollQty_arr[$row['BARCODE_NO']]['QNTY'] += $row['QNTY'];
					}
					// echo '<pre>';print_r($greyUsedRollQty_arr);die;
					foreach($result as $row)
					{
						if($row['KNITTING_SOURCE']==1)
                        {
                        	$knitting_company = $companyArr[$row['KNITTING_COMPANY']];
                        } 
                        else 
                        {
                        	$knitting_company = $supplierArr[$row['KNITTING_COMPANY']];
                        }
                        // echo $row['BARCODE_NO'].'<br>';
                        if ($row['ENTRY_FORM']==37 || $row['ENTRY_FORM']==17) 
                        {
                        	$grey_used_qty=$row['GREY_USED_QTY'];
                        }
                        else
                        {
                        	$grey_used_qty=$greyUsedRollQty_arr[$row['BARCODE_NO']]['QNTY'];
                        }
                        // echo $grey_used_qty.'<br>';
                        if($row['RECEIVE_BASIS']==11)
                        {
                        	$fab_process_id = $process_arr[$row['BOOKING_NO']];
                        }
						
						$str_ref=$row["COLOR_ID"]."*".$row["BATCH_ID"]."*".$row["PROD_ID"];

						$data_arr[$row['RECV_NUMBER']][$str_ref]['RECEIVE_DATE']=$row['RECEIVE_DATE'];
						$data_arr[$row['RECV_NUMBER']][$str_ref]['CHALLAN_NO']=$row['CHALLAN_NO'];
						$data_arr[$row['RECV_NUMBER']][$str_ref]['KNITTING_COMPANY']=$knitting_company;
						$data_arr[$row['RECV_NUMBER']][$str_ref]['FAB_PROCESS_ID']=$fab_process_id;
						$data_arr[$row['RECV_NUMBER']][$str_ref]['INSERT_DATE']=$row['INSERT_DATE'];
						$data_arr[$row['RECV_NUMBER']][$str_ref]['USER_ID']=$row['USER_ID'];
						$data_arr[$row['RECV_NUMBER']][$str_ref]['QUANTITY']+=$row['QUANTITY'];
						$data_arr[$row['RECV_NUMBER']][$str_ref]['GREY_USED_QTY']+=$grey_used_qty;
					}
					// echo '<pre>';print_r($data_arr);die;

        			foreach($data_arr as $recv_number => $recv_number_arr)
                    {
                    	foreach($recv_number_arr as $str_ref => $row)
                    	{
                    		$string_data_arr = explode("*", $str_ref);
                            $color_id=$string_data_arr[0];
                            $batch_id=$string_data_arr[1];
                            $prod_id=$string_data_arr[2];

                        if ($i%2==0)  
                            $bgcolor="#E9F3FF";
                        else
                            $bgcolor="#FFFFFF";	

                        $grey_used_qty=$row['GREY_USED_QTY'];
                        $process_loss = 100 - (($row['QUANTITY']/$grey_used_qty)*100);

                        if($row['RECEIVE_BASIS']==11)
                        {
                        	$fab_process_id = $process_arr[$row['BOOKING_NO']];
                        }

                    	?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
                          	<td width="30" align="center"><? echo $i; ?></td>
		                    <td width="115"><p><? echo $recv_number; ?></p></td>
		                    <td width="80"><p><? echo change_date_format($row['RECEIVE_DATE']); ?></p></td>
		                    <td width="60"><p><? echo $row['CHALLAN_NO']; ?></p></td>
		                    <td width="120"><p><? echo $row['KNITTING_COMPANY']; ?></p></td>
		                    <td width="80"><p><? echo $color_arr[$color_id];?></p></td>
		                    <td width="80"><p><? echo $batch_arr[$batch_id]; ?></p></td>
		                    <td width="60" align="right"><p><? echo number_format($grey_used_qty,2); ?></p></td>
		                    <td width="80" align="right"><p><? echo number_format($row['QUANTITY'],2); ?></p></td>
		                    <td width="50" align="right"><p><? echo number_format($process_loss,2); ?></p></td>
		                    <td width="170" align="center" title="<? echo $prod_id; ?>"><p><? echo $determinaArr[$product_determination_arr[$prod_id]].','. $composition_arr[$product_determination_arr[$prod_id]] .','.$product_gsm_arr[$prod_id].','.$product_dia_arr[$prod_id]; //$product_arr[$prod_id]; ?></p></td>
		                    <td width="80"><p><? echo $conversion_cost_head_array[$row['FAB_PROCESS_ID']]; ?></p></td>
		                    <td width="115"><p><? echo $row['INSERT_DATE']; ?></p></td>
		                    <td width="60"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
                    	<?
                    	$i++;
                    	$total_grey_used_qty += $row['GREY_USED_QTY']+$greyUsedRollQty_arr[$row['BARCODE_NO']]['QNTY'];
                    	$total_finish_rec_qty += $row['QUANTITY'];
                    	$total_process_loss = 100 - (($total_finish_rec_qty/$total_grey_used_qty)*100);
                    	}
                	}
                    ?>
                    </tbody>
                </table>                
            
	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0">  
	                <tfoot>
	                    <th width="30"></th>
                    	<th width="115"></th>
                    	<th width="80"></th>
                    	<th width="60"></th>
                    	<th width="120"></th>
                    	<th width="80"></th>
                    	<th width="80" align="right">Total</th>
                    	<th width="60" align="right" id="value_total_grey_used_qty"><? echo number_format($total_grey_used_qty,2); ?></th>
                    	<th width="80" align="right" id="value_total_finish_rec_qty"><? echo number_format($total_finish_rec_qty,2); ?></th>
                    	<th width="50" align="right"><? echo number_format($total_process_loss,2); ?></th>
                    	<th width="170"></th>
                    	<th width="80"></th>
                    	<th width="115"></th>
                    	<th width="60"></th>
	                </tfoot>
	            </table>
	            <br>
            	<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
					<thead>
	                	<tr>
	                    	<th colspan="11">Finished Fabric Receive Return</th>
	                    </tr>
	                    <tr>
	                    	<th width="30">SL</th>
	                        <th width="120">Recieve Rtn No</th>
	                        <th width="80">Return Date</th>
	                        <th width="120">Recieve MRR No</th>
	                        <th width="120">Dyeing Company</th>
	                        <th width="80">Color</th>
	                        <th width="100">Batch No</th>
	                        <th width="80">Return Qty</th>
	                        <th width="200">Item Description</th>
	                        <th width="150">Insert Date & Time</th>
	                        <th width="90">User Name</th>
	                    </tr>
					</thead>
				</table>
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body_2">
	                <tbody>
                	<?
                	$i=1;$total_receive_return_qnty=0;
					//, inv_receive_master e 
					if($order_id != '' && $finishRecIssueReturn_prodIDs != '')
					{
						$sql_return="SELECT a.issue_number as RECEIVE_RETURN_NO, a.issue_date as RETURN_DATE, a.RECEIVED_MRR_NO, a.COMPANY_ID, a.INSERT_DATE, a.inserted_by as USER_ID, b.PROD_ID, b.BATCH_ID_FROM_FISSUERTN, sum(NVL(c.quantity, 0)) as QUANTITY, d.PRODUCT_NAME_DETAILS, c.COLOR_ID
						from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d
						where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=d.id and c.prod_id=d.id and a.company_id=$company_id and a.entry_form in (46,202) and b.transaction_type=3 and c.entry_form in (46,202) and c.trans_type=3 and c.po_breakdown_id in($order_id) and d.id in($finishRecIssueReturn_prodIDs) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
						group by a.issue_number, a.issue_date, a.received_mrr_no, a.company_id, a.insert_date, a.inserted_by, b.prod_id, b.batch_id_from_fissuertn, d.product_name_details, c.color_id";
					}
                	$sql_return_res=sql_select($sql_return);
					foreach($sql_return_res as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";						
	                    ?>
	                    <tr bgcolor="<? echo  $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
	                    	<td width="30"><? echo $i; ?></td>
	                    	<td width="120"><p><? echo $row['RECEIVE_RETURN_NO']; ?></p></td>
	                        <td width="80" align="center"><p><? echo change_date_format($row['RETURN_DATE']); ?></p></td>
	                        <td width="120"><p><? echo $row['RECEIVED_MRR_NO']; ?></p></td>
	                        <td width="120"><p><? echo $companyArr[$row['COMPANY_ID']]; ?></p></td>
	                        <td width="80"><p><? echo $color_arr[$row['COLOR_ID']]; ?></p></td>
	                        <td width="100"><p><? echo $batch_arr[$row['BATCH_ID_FROM_FISSUERTN']]; ?></p></td>
	                        <td width="80" align="right"><p><? echo number_format($row['QUANTITY'],2); ?></p></td>        
	                        <td width="200" align="center"><p><? echo $row['PRODUCT_NAME_DETAILS']; ?></p></td>
	                        <td width="150"><p><? echo $row['INSERT_DATE']; ?></p></td>
	                        <td width="90"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
	                    </tr>
	                	<?
	                	$i++;
						$total_receive_return_qnty += $row['QUANTITY'];	                	
	                }
	                ?>
            		</tbody>
            	</table>
        	</div>
        	<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
	                <tr style="font-weight:bold">
	                	<th width="30"></th>
                        <th width="120"></th>
                        <th width="80"></th>
                        <th width="120"></th>
                        <th width="120"></th>
                        <th width="80"></th>
	                    <th width="100" align="right">Total Return:</th>
	                    <th width="80" align="right" id="value_total_receive_return_qnty"><? echo number_format($total_receive_return_qnty,2); ?></th>
	                    <th width="200"></th>
	                    <th width="150"></th>
	                    <th width="90"></th>
	                </tr>
	                <tr>
	                    <th colspan="7" align="right">Net Receive:</th>
	                    <th width="80" align="right"><? echo number_format(($total_finish_rec_qty-$total_receive_return_qnty),2);?></th>
	                    <th width="200"></th>
	                    <th width="150"></th>
	                    <th width="90"></th>
	                </tr>
	            </tfoot>    
            </table>	
        </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>   
	<script>setFilterGrid('table_body_2',-1, tableFilters_2);</script>   
	<?
	exit();
}

if ($action === 'cutting_issue_sew_prod_popup_____backup-04-12-2021')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$color_arr = return_library_array( "select ID, COLOR_NAME from lib_color", 'ID', 'COLOR_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');
	$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');

	if ($order_id != '' && $prod_ids != '')
   	{	
       	$sql="SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, sum(NVL(c.quantity, 0)) as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=9 and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id
		union all
		SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, sum(NVL(c.quantity, 0)) as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID
		from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=9 and a.entry_form in (19) and c.entry_form in (19) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id
		order by issue_date desc";
	}

	$sql_res=sql_select($sql);
	if (count($sql_res) > 0)
	{
		foreach ($sql_res as $value)
		{
			$batch_id_arr[$value['BATCH_ID']] = $value['BATCH_ID'];
		}
		$batch_Ids = implode(",",array_keys($batch_id_arr));
		$batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');
	}

	$table_width = 1150;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_issue_to_cut_qnty'],
					col: [7],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong>Cutting Issue Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="12">Issue To Cutting Info</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="130">Issue No</th>
	                    <th width="70">Issue Date</th>
	                    <th width="120">Issue to Company</th>
	                    <th width="80">Color</th>
	                    <th width="80">Challan No</th>
	                    <th width="80">Batch No</th>	                    
	                    <th width="80">Issue Qnty</th>               
	                    <th width="180">Fabric Description</th>
	                    <th width="100">Issue Purpose</th>             	
	                    <th width="120">Insert Date & Time</th>
	                    <th width="80">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; $total_issue_to_cut_qnty=0;
	               	                     
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		if($row['KNIT_DYE_SOURCE']==1) $issue_to = $companyArr[$row['KNIT_DYE_COMPANY']];
						else $issue_to = $supplierArr[$row['KNIT_DYE_COMPANY']];
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['ISSUE_NUMBER']; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row['ISSUE_DATE']); ?></p></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row['COLOR_ID']]; ?></p></td>
                            <td width="80"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                            <td width="80"><p><? echo $batch_arr[$row['BATCH_ID']]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['ISSUE_TO_CUT_QTY'],2,'.',''); ?></p></td>
                            <td width="180" align="center"><p><? echo $product_arr[$row['PROD_ID']]; ?></p></td>
                            <td width="100"><p><? echo $yarn_issue_purpose[$row['ISSUE_PURPOSE']]; ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="80" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_issue_to_cut_qnty += $row['ISSUE_TO_CUT_QTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="70"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="80" align="right">Total:</th>
	                    <th width="80" align="right" id="value_total_issue_to_cut_qnty"><? echo number_format($total_issue_to_cut_qnty,2,'.',''); ?></th>
	                    <th width="180"></th>
	                    <th width="100"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'cutting_issue_sew_prod_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$color_arr = return_library_array( "select ID, COLOR_NAME from lib_color", 'ID', 'COLOR_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');
	$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');

	if ($order_id != '' && $prod_ids != '')
   	{	
       	$sql="SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, c.quantity as ISSUE_TO_CUT_QTY, c.COLOR_ID, 
		a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID, D.BARCODE_NO, a.ENTRY_FORM 
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c, PRO_ROLL_DETAILS d 
		where a.id=b.mst_id and b.id=c.dtls_id and a.id=d.mst_id and b.id=D.DTLS_ID and C.PO_BREAKDOWN_ID=D.PO_BREAKDOWN_ID and C.DTLS_ID=D.DTLS_ID and a.company_id=$company_id and a.issue_purpose=9 and a.entry_form in (71) and c.entry_form in (71) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		UNION ALL
       	SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, c.quantity as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID, 0 as BARCODE_NO, a.ENTRY_FORM
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=9 and a.entry_form in (18) and c.entry_form in (18) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		UNION ALL
		SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, c.quantity as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID, 0 as BARCODE_NO, a.ENTRY_FORM
		from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=9 and a.entry_form in (19) and c.entry_form in (19) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	}
	// echo $sql;
	$sql_res=sql_select($sql);
	if (count($sql_res) > 0)
	{
		$finish_barcode_arr=array();$batch_id_arr=array();
		foreach ($sql_res as $value)
		{
			// echo $value['ENTRY_FORM'].'<br>';
			$batch_id_arr[$value['BATCH_ID']] = $value['BATCH_ID'];
			if ($value['ENTRY_FORM']==71)
			{
				$finish_barcode_arr[$value['BARCODE_NO']] = $value['BARCODE_NO'];
			}
		}
		// $batch_Ids = implode(",",array_keys($batch_id_arr));
		// $batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');
	}
	// echo "<pre>";print_r($finish_barcode_arr);

	$finish_barcode_arr = array_filter($finish_barcode_arr);
	if(count($finish_barcode_arr)>0)
	{
		$all_barcode = implode(",", $finish_barcode_arr);
        $all_barcode_cond=""; $barcodeCond="";
        if($db_type==2 && count($finish_barcode_arr)>999)
        {
        	$all_finish_barcode_arr_chunk=array_chunk($finish_barcode_arr,999) ;
        	foreach($all_finish_barcode_arr_chunk as $chunk_arr)
        	{
        		$chunk_arr_value=implode(",",$chunk_arr);
        		$barcodeCond.="  c.barcode_no in($chunk_arr_value) or ";
        	}

        	$all_barcode_cond.=" and (".chop($barcodeCond,'or ').")";
        }
        else
        {
        	$all_barcode_cond=" and c.barcode_no in($all_barcode)";
        }
		$sql_batch_data=sql_select("SELECT a.id, b.batch_id,c.barcode_no,c.reprocess,c.prev_reprocess  FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(37,7,68) and c.entry_form in(37,7,68) and c.status_active=1 and c.is_deleted=0 $all_barcode_cond");
		$batch_barcode_arr=array();
		foreach ($sql_batch_data as $val)
		{
			$batch_barcode_arr[$val[csf('barcode_no')]]=$val[csf('batch_id')];
			$batch_id_arr[$val[csf('batch_id')]] = $value['batch_id'];
		}

    }
    $batch_Ids = implode(",",array_keys($batch_id_arr));
	$batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');

	foreach($sql_res as $row)
	{
		if($row['KNIT_DYE_SOURCE']==1) 
		{
			$issue_to = $companyArr[$row['KNIT_DYE_COMPANY']];
		}
		else 
		{
			$issue_to = $supplierArr[$row['KNIT_DYE_COMPANY']];
		}

		if($value['ENTRY_FORM']==71)
		{
			$batch_id=$batch_arr[$batch_barcode_arr[$row['BARCODE_NO']]];
		}
		else
		{
			$batch_id=$batch_arr[$row["BATCH_ID"]];
		}
		
		$str_ref=$row["COLOR_ID"]."*".$batch_id."*".$row["PROD_ID"];

		$data_arr[$row['ISSUE_NUMBER']][$str_ref]['ISSUE_DATE']=$row['ISSUE_DATE'];
		$data_arr[$row['ISSUE_NUMBER']][$str_ref]['CHALLAN_NO']=$row['CHALLAN_NO'];
		$data_arr[$row['ISSUE_NUMBER']][$str_ref]['ISSUE_TO']=$issue_to;
		$data_arr[$row['ISSUE_NUMBER']][$str_ref]['INSERT_DATE']=$row['INSERT_DATE'];
		$data_arr[$row['ISSUE_NUMBER']][$str_ref]['USER_ID']=$row['USER_ID'];
		$data_arr[$row['ISSUE_NUMBER']][$str_ref]['ISSUE_PURPOSE']=$row['ISSUE_PURPOSE'];
		$data_arr[$row['ISSUE_NUMBER']][$str_ref]['ISSUE_TO_CUT_QTY']+=$row['ISSUE_TO_CUT_QTY'];
	}
	// echo '<pre>';print_r($data_arr);

	$table_width = 1150;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_issue_to_cut_qnty'],
					col: [7],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong>Cutting Issue Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="12">Issue To Cutting Info</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="130">Issue No</th>
	                    <th width="70">Issue Date</th>
	                    <th width="120">Issue to Company</th>
	                    <th width="80">Color</th>
	                    <th width="80">Challan No</th>
	                    <th width="80">Batch No</th>	                    
	                    <th width="80">Issue Qnty</th>               
	                    <th width="180">Fabric Description</th>
	                    <th width="100">Issue Purpose</th>             	
	                    <th width="120">Insert Date & Time</th>
	                    <th width="80">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; $total_issue_to_cut_qnty=0;
	               	foreach($data_arr as $issue_number => $issue_number_arr)
                    {
                    	foreach($issue_number_arr as $str_ref => $row)
                    	{
                    		$string_data_arr = explode("*", $str_ref);
                            $color_id=$string_data_arr[0];
                            $batch_num=$string_data_arr[1];
                            $prod_id=$string_data_arr[2];

                    		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    		?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="30" align="center"><? echo $i; ?></td>
								<td width="130"><p><? echo $issue_number; ?></p></td>
	                            <td width="70"><p><? echo change_date_format($row['ISSUE_DATE']); ?></p></td>
	                            <td width="120"><p><? echo $issue_to; ?></p></td>
	                            <td width="80"><p><? echo $color_arr[$color_id]; ?></p></td>
	                            <td width="80"><p><? echo $row['CHALLAN_NO']; ?></p></td>
	                            <td width="80"><p><? echo $batch_num; ?></p></td>
	                            <td width="80" align="right"><p><? echo number_format($row['ISSUE_TO_CUT_QTY'],2,'.',''); ?></p></td>
	                            <td width="180" align="center"><p><? echo $product_arr[$prod_id]; ?></p></td>
	                            <td width="100"><p><? echo $yarn_issue_purpose[$row['ISSUE_PURPOSE']]; ?></p></td>
	                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
	                            <td width="80" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
	                        </tr>
		                    <?
		                    $i++;
		                    $total_issue_to_cut_qnty += $row['ISSUE_TO_CUT_QTY'];
                    	}   
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="70"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="80" align="right">Total:</th>
	                    <th width="80" align="right" id="value_total_issue_to_cut_qnty"><? echo number_format($total_issue_to_cut_qnty,2,'.',''); ?></th>
	                    <th width="180"></th>
	                    <th width="100"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'cutting_issue_sample_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$color_arr = return_library_array( "select ID, COLOR_NAME from lib_color", 'ID', 'COLOR_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');
	$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');

	if ($order_id != '' && $prod_ids != '')
   	{	
       	$sql="SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, sum(NVL(c.quantity, 0)) as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=4 and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id
		union all
		SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, sum(NVL(c.quantity, 0)) as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID
		from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=4 and a.entry_form in (19) and c.entry_form in (19) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id
		order by issue_date desc";
	}

	$sql_res=sql_select($sql);
	if (count($sql_res) > 0)
	{
		foreach ($sql_res as $value)
		{
			$batch_id_arr[$value['BATCH_ID']] = $value['BATCH_ID'];
		}
		$batch_Ids = implode(",",array_keys($batch_id_arr));
		$batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');
	}

	$table_width = 1150;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_issue_to_cut_qnty'],
					col: [7],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong>Cutting Issue Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="12">Issue To Cutting Info</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="130">Issue No</th>
	                    <th width="70">Issue Date</th>
	                    <th width="120">Issue to Company</th>
	                    <th width="80">Color</th>
	                    <th width="80">Challan No</th>
	                    <th width="80">Batch No</th>	                    
	                    <th width="80">Issue Qnty</th>               
	                    <th width="180">Fabric Description</th>
	                    <th width="100">Issue Purpose</th>             	
	                    <th width="120">Insert Date & Time</th>
	                    <th width="80">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; $total_issue_to_cut_qnty=0;
	               	                     
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		if($row['KNIT_DYE_SOURCE']==1) $issue_to = $companyArr[$row['KNIT_DYE_COMPANY']];
						else $issue_to = $supplierArr[$row['KNIT_DYE_COMPANY']];
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['ISSUE_NUMBER']; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row['ISSUE_DATE']); ?></p></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row['COLOR_ID']]; ?></p></td>
                            <td width="80"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                            <td width="80"><p><? echo $batch_arr[$row['BATCH_ID']]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['ISSUE_TO_CUT_QTY'],2,'.',''); ?></p></td>
                            <td width="180" align="center"><p><? echo $product_arr[$row['PROD_ID']]; ?></p></td>
                            <td width="100"><p><? echo $yarn_issue_purpose[$row['ISSUE_PURPOSE']]; ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="80" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_issue_to_cut_qnty += $row['ISSUE_TO_CUT_QTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="70"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="80" align="right">Total:</th>
	                    <th width="80" align="right" id="value_total_issue_to_cut_qnty"><? echo number_format($total_issue_to_cut_qnty,2,'.',''); ?></th>
	                    <th width="180"></th>
	                    <th width="100"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'cutting_issue_reprocess_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$color_arr = return_library_array( "select ID, COLOR_NAME from lib_color", 'ID', 'COLOR_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');
	$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');

	if ($order_id != '' && $prod_ids != '')
   	{	
       	$sql="SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, sum(NVL(c.quantity, 0)) as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=44 and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id
		order by a.issue_date desc";
	}

	$sql_res=sql_select($sql);
	if (count($sql_res) > 0)
	{
		foreach ($sql_res as $value)
		{
			$batch_id_arr[$value['BATCH_ID']] = $value['BATCH_ID'];
		}
		$batch_Ids = implode(",",array_keys($batch_id_arr));
		$batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');
	}

	$table_width = 1150;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_issue_to_cut_qnty'],
					col: [7],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong>Cutting Issue Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="12">Issue To Cutting Info</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="130">Issue No</th>
	                    <th width="70">Issue Date</th>
	                    <th width="120">Issue to Company</th>
	                    <th width="80">Color</th>
	                    <th width="80">Challan No</th>
	                    <th width="80">Batch No</th>	                    
	                    <th width="80">Issue Qnty</th>               
	                    <th width="180">Fabric Description</th>
	                    <th width="100">Issue Purpose</th>             	
	                    <th width="120">Insert Date & Time</th>
	                    <th width="80">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; $total_issue_to_cut_qnty=0;
	               	                     
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		if($row['KNIT_DYE_SOURCE']==1) $issue_to = $companyArr[$row['KNIT_DYE_COMPANY']];
						else $issue_to = $supplierArr[$row['KNIT_DYE_COMPANY']];
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['ISSUE_NUMBER']; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row['ISSUE_DATE']); ?></p></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row['COLOR_ID']]; ?></p></td>
                            <td width="80"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                            <td width="80"><p><? echo $batch_arr[$row['BATCH_ID']]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['ISSUE_TO_CUT_QTY'],2,'.',''); ?></p></td>
                            <td width="180" align="center"><p><? echo $product_arr[$row['PROD_ID']]; ?></p></td>
                            <td width="100"><p><? echo $yarn_issue_purpose[$row['ISSUE_PURPOSE']]; ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="80" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_issue_to_cut_qnty += $row['ISSUE_TO_CUT_QTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="70"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="80" align="right">Total:</th>
	                    <th width="80" align="right" id="value_total_issue_to_cut_qnty"><? echo number_format($total_issue_to_cut_qnty,2,'.',''); ?></th>
	                    <th width="180"></th>
	                    <th width="100"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'cutting_issue_scrap_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$supplierArr = return_library_array("select ID,SUPPLIER_NAME from lib_supplier",'ID','SUPPLIER_NAME');
	$color_arr = return_library_array( "select ID, COLOR_NAME from lib_color", 'ID', 'COLOR_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');
	$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');

	if ($order_id != '' && $prod_ids != '')
   	{	
       	$sql="SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, sum(NVL(c.quantity, 0)) as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=31 and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id
		union all
		SELECT a.ISSUE_NUMBER, a.ISSUE_PURPOSE, a.issue_date as ISSUE_DATE, a.KNIT_DYE_SOURCE, a.KNIT_DYE_COMPANY, b.BATCH_ID, b.PROD_ID, sum(NVL(c.quantity, 0)) as ISSUE_TO_CUT_QTY, c.COLOR_ID, a.CHALLAN_NO, a.INSERT_DATE, a.inserted_by as USER_ID
		from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.issue_purpose=31 and a.entry_form in (19) and c.entry_form in (19) and c.po_breakdown_id in($order_id) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
		group by b.id, a.issue_number, a.issue_purpose, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, b.batch_id, b.prod_id, a.insert_date, a.inserted_by, c.color_id
		order by issue_date desc";
	}

	$sql_res=sql_select($sql);
	if (count($sql_res) > 0)
	{
		foreach ($sql_res as $value)
		{
			$batch_id_arr[$value['BATCH_ID']] = $value['BATCH_ID'];
		}
		$batch_Ids = implode(",",array_keys($batch_id_arr));
		$batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');
	}

	$table_width = 1150;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_issue_to_cut_qnty'],
					col: [7],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="12" style="font-size:16px" width="100%" align="center"><strong>Scrap Store Issue Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="12">Issue To Cutting Info</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="130">Issue No</th>
	                    <th width="70">Issue Date</th>
	                    <th width="120">Issue to Company</th>
	                    <th width="80">Color</th>
	                    <th width="80">Challan No</th>
	                    <th width="80">Batch No</th>	                    
	                    <th width="80">Issue Qnty</th>               
	                    <th width="180">Fabric Description</th>
	                    <th width="100">Issue Purpose</th>             	
	                    <th width="120">Insert Date & Time</th>
	                    <th width="80">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; $total_issue_to_cut_qnty=0;
	               	                     
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		if($row['KNIT_DYE_SOURCE']==1) $issue_to = $companyArr[$row['KNIT_DYE_COMPANY']];
						else $issue_to = $supplierArr[$row['KNIT_DYE_COMPANY']];
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['ISSUE_NUMBER']; ?></p></td>
                            <td width="70"><p><? echo change_date_format($row['ISSUE_DATE']); ?></p></td>
                            <td width="120"><p><? echo $issue_to; ?></p></td>
                            <td width="80"><p><? echo $color_arr[$row['COLOR_ID']]; ?></p></td>
                            <td width="80"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                            <td width="80"><p><? echo $batch_arr[$row['BATCH_ID']]; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['ISSUE_TO_CUT_QTY'],2,'.',''); ?></p></td>
                            <td width="180" align="center"><p><? echo $product_arr[$row['PROD_ID']]; ?></p></td>
                            <td width="100"><p><? echo $yarn_issue_purpose[$row['ISSUE_PURPOSE']]; ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="80" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_issue_to_cut_qnty += $row['ISSUE_TO_CUT_QTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="70"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="80" align="right">Total:</th>
	                    <th width="80" align="right" id="value_total_issue_to_cut_qnty"><? echo number_format($total_issue_to_cut_qnty,2,'.',''); ?></th>
	                    <th width="180"></th>
	                    <th width="100"></th>
	                    <th width="120"></th>
	                    <th width="80"></th>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'finish_fabric_transfer_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$buyerArr = return_library_array("select ID,BUYER_NAME from lib_buyer",'ID','BUYER_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');

	$table_width = 850;
	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ['value_total_trans_in_qnty'],
				col: [7],
				operation: ['sum'],
				write_method: ['innerHTML']
			}
		}
		tableFilters_2 = {
			col_operation: {
				id: ['value_total_trans_out_quantity'],
				col: [7],
				operation: ['sum'],
				write_method: ['innerHTML']
			}
		}
		

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="9" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="9" style="font-size:16px" width="100%" align="center"><strong>Finish Transfer Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<?
				$sql_po_info=sql_select("SELECT a.JOB_NO, a.BUYER_NAME, a.STYLE_REF_NO 
					from wo_po_details_master a, wo_po_break_down b 
					where a.job_no=b.job_no_mst and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and a.company_name=$company_id and b.id in($order_id) 
					group by a.job_no, a.buyer_name, a.style_ref_no");
				$jobNo=$sql_po_info[0]['JOB_NO'];
				$buyerName=$buyerArr[$sql_po_info[0]['BUYER_NAME']];
				$styleRef=$sql_po_info[0]['STYLE_REF_NO'];
			?>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th style="text-align: left;" colspan="10"><? echo "Buyer:	$buyerName ". " Style: $styleRef ". " Job: $jobNo "; ?></th>
					</tr>
					<tr>
						<th colspan="10">Finish Fabric Transfer in Details</th>
					</tr>
					<tr>
						<th width="30">SL</th>
	                    <th width="130">Transfer ID</th>
	                    <th width="80">Transfer Date</th>
	                    <th width="80">Buyer</th>
	                    <th width="80">Style</th>
	                    <th width="100">From Order/ Booking</th>
	                    <th width="130">Item Description</th>
	                    <th width="80">Transfer Qnty</th>
	                    <th width="120">Insert Date & Time</th>
	                    <th width="70">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	                $i=1; $issue_to='';
	                if ($order_id != '' && $prod_ids != '')
	                {	
	                	$sql = "SELECT  X.TRANSFER_SYSTEM_ID, X.TRANSFER_DATE, X.PROD_ID, X.PRODUCT_NAME_DETAILS, X.FROM_BOOK_ORDER, X.BUYER_NAME, X.STYLE_REF_NO, sum(X.GREY_TRANSIN) as GREY_TRANSIN, X.INSERTED_BY, X.INSERT_DATE
						from (
						select c.id,a.transfer_system_id, a.transfer_date, b.to_prod_id as prod_id, g.product_name_details,  d.booking_no as from_book_order, d.buyer_id as buyer_name,f.style_ref_no,  c.quantity as grey_transin, a.inserted_by, a.insert_date
						from order_wise_pro_details c, inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_non_ord_samp_booking_mst d, wo_non_ord_samp_booking_dtls e left join sample_development_mst f on e.style_id=f.id, product_details_master g
						where c.trans_type in (5) and c.dtls_id=b.id and b.from_prod_id=g.id and b.mst_id=a.id and b.from_order_id=d.id  and d.booking_no=e.booking_no and d.booking_type=4 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.entry_form in(14,306,258) and a.transfer_criteria=7  
						group by c.id,a.transfer_system_id, a.transfer_date, b.to_prod_id, g.product_name_details,  d.booking_no, d.buyer_id ,f.style_ref_no,  c.quantity, a.inserted_by, a.insert_date
						union all
						select c.id, a.transfer_system_id, a.transfer_date, b.to_prod_id as prod_id,g.product_name_details,  p.po_number as from_book_order, j.buyer_name, j.style_ref_no, c.quantity as grey_transin, a.inserted_by, a.insert_date
						from order_wise_pro_details c,  inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_po_break_down p, wo_po_details_master j, product_details_master g
						where c.trans_type in (5) and c.dtls_id=b.id and b.from_prod_id=g.id and b.mst_id=a.id and b.from_order_id =p.id and p.job_id=j.id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.entry_form in(14,306,258) and a.transfer_criteria=4
						) x
						group by x.transfer_system_id, x.transfer_date, x.prod_id, x.product_name_details, x.from_book_order, x.buyer_name, x.style_ref_no,  x.inserted_by, x.insert_date order by x.transfer_system_id";
						 $sql_res=sql_select($sql); 
	                }

	               	$i=1; $total_grey_issue_qnty=$total_roll_no=0;
               		foreach ($sql_res as $row)
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['TRANSFER_SYSTEM_ID']; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row['TRANSFER_DATE']); ?></p></td>
                            <td width="80"><p><? echo $buyerArr[$row['BUYER_NAME']]; ?></p></td>
                            <td width="80"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
                            <td width="100" align="right"><p><? echo $row['FROM_BOOK_ORDER']; ?></p></td>
                            <td width="130" align="right"><p><? echo $row['PRODUCT_NAME_DETAILS']; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['GREY_TRANSIN'],2,'.',''); ?></p></td>
                            
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="70" align="center"><p><? echo $user_arr[$row['INSERTED_BY']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_grey_trans_in_qnty += $row['GREY_TRANSIN'];
	                    $total_roll_no += $row['ROLL_NO'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="130"></th>
	                    <th width="80"></th>

	                    <th width="80"></th>
	                    <th width="80"></th>
	                    <th width="100"></th>
	                    <th width="130">Total:</th>
	                    <th width="80" align="right" id="value_total_trans_in_qnty"><? echo number_format($total_grey_trans_in_qnty,2,'.','');?></th>
	                    <th width="120"></th>
	                    <th width="70"></th>
					</tfoot>
	            </table>
            	<br>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
					<thead>
						<tr>
							<th colspan="10">Finish Transfer Out Info</th>
						</tr>
						<tr>				
		                    <th width="30">SL</th>
		                    <th width="130">Transfer ID</th>
		                    <th width="80">Transfer Date</th>
		                    <th width="80">Buyer</th>
		                    <th width="80">Style</th>
		                    <th width="100">To Order/ Booking</th>
		                    <th width="130">Item Description</th>
		                    <th width="80">Transfer Qnty</th>
		                    <th width="120">Insert Date & Time</th>
		                    <th width="70">User Name</th>
	                    </tr>                	
					</thead>				
	            </table>
           
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body_2">
	                <tbody>
	               	<?
	               	$i=1; $return_from='';
	               	if ($order_id != '')
	               	{	
		               	$sql_trans_out = "SELECT  X.TRANSFER_SYSTEM_ID, X.TRANSFER_DATE, X.PROD_ID, X.PRODUCT_NAME_DETAILS, X.TO_BOOK_ORDER, X.BUYER_NAME, X.STYLE_REF_NO, sum(X.GREY_TRANSOUT) as GREY_TRANSOUT, X.INSERTED_BY, X.INSERT_DATE
						from (
						select c.id, a.transfer_system_id, a.transfer_date, b.to_prod_id as prod_id,g.product_name_details,  p.po_number as to_book_order, j.buyer_name, j.style_ref_no, c.quantity as grey_transout, a.inserted_by, a.insert_date
						from order_wise_pro_details c,  inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_po_break_down p, wo_po_details_master j, product_details_master g
						where c.trans_type in (6) and c.dtls_id=b.id and b.to_prod_id=g.id and b.mst_id=a.id and b.to_order_id =p.id and p.job_id=j.id and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($order_id) 
						and c.prod_id in($prod_ids) and c.entry_form in(14,306,258) and a.transfer_criteria=4
						union all
						select c.id,a.transfer_system_id, a.transfer_date, b.from_prod_id as prod_id, g.product_name_details,  d.booking_no as to_book_order, d.buyer_id as buyer_name,f.style_ref_no,  c.quantity as grey_transout, a.inserted_by, a.insert_date
						from order_wise_pro_details c, inv_item_transfer_dtls b, inv_item_transfer_mst a, wo_non_ord_samp_booking_mst d , wo_non_ord_samp_booking_dtls e left join sample_development_mst f on e.style_id=f.id
						, product_details_master g
						where c.trans_type in (6) and c.dtls_id=b.id and b.to_prod_id=g.id 
						and b.mst_id=a.id 
						and b.to_order_id=d.id  
						and d.booking_no=e.booking_no and d.booking_type=4 and c.status_active=1 and c.is_deleted=0 and b.status_active=1
						and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids)
						and c.entry_form in(14,306,258)
						and a.transfer_criteria=6
						group by c.id,a.transfer_system_id, a.transfer_date, b.from_prod_id, g.product_name_details,  d.booking_no, d.buyer_id ,f.style_ref_no,  c.quantity, a.inserted_by, a.insert_date
						) x
						group by x.transfer_system_id, x.transfer_date, x.prod_id, x.product_name_details, x.to_book_order, x.buyer_name, x.style_ref_no,  x.inserted_by, x.insert_date 
						order by x.transfer_system_id ";
	            	}
               		$sql_trans_out_arr = sql_select($sql_trans_out);	               			
           			$total_grey_trans_out_qnty=0;
           			foreach ($sql_trans_out_arr as $row)
           			{
               			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="130"><p><? echo $row['TRANSFER_SYSTEM_ID']; ?></p></td>
                            <td width="80"><p><? echo change_date_format($row['TRANSFER_DATE']); ?></p></td>
                            <td width="80"><p><? echo $buyerArr[$row['BUYER_NAME']]; ?></p></td>
                            <td width="80"><p><? echo $row['STYLE_REF_NO']; ?></p></td>
                            <td width="100" align="right"><p><? echo $row['TO_BOOK_ORDER']; ?></p></td>
                            <td width="130" align="right"><p><? echo $row['PRODUCT_NAME_DETAILS']; ?></p></td>
                            <td width="80" align="right"><p><? echo number_format($row['GREY_TRANSOUT'],2,'.',''); ?></p></td>
                            
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="70" align="center"><p><? echo $user_arr[$row['INSERTED_BY']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_grey_trans_out_qnty += $row['GREY_TRANSOUT'];
                	}
                    ?>							
	                </tbody>
	            </table>
            </div>
            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<tr>
	                    <th width="30">&nbsp;</th>
	                    <th width="130">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="80">&nbsp;</th>
	                    <th width="100">&nbsp;</th>
	                    <th width="130">&nbsp;</th>
	                    <th width="80" id="value_total_trans_out_quantity">&nbsp;</th>
	                    <th width="120">&nbsp;</th>
	                    <th width="70">&nbsp;</th>
                    </tr>
					<tr>
						<th colspan="7" align="right">Net Transfer:</th>
						<th align="right"><? echo number_format(($total_grey_trans_in_qnty-$total_grey_trans_out_qnty),2,'.','');?></th>
						<th align="right"><? echo $total_roll_no-$total_grey_roll_no; ?></th>
						<th colspan="2"></th>
					</tr>
				</tfoot>
            </table>
        </div>          
    </fieldset>
    <script>setFilterGrid('table_body',-1, tableFilters);</script>
    <script>setFilterGrid('table_body_2',-1, tableFilters_2);</script>
    
    <?
	exit();
}

if ($action === 'return_from_cuttting_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $prod_id) = explode('__', $data);
	$prod_ids = implode(',', array_flip(array_flip(explode(',', rtrim($prod_id,',')))));

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');
	$user_arr = return_library_array( "select ID, USER_NAME from user_passwd", 'ID', 'USER_NAME');
	$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from product_details_master where id in($prod_ids) and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');
	
	if ($order_id != '' && $prod_ids != '')
   	{	
       	$sql="SELECT a.RECV_NUMBER,  a.receive_date as RETURN_DATE, a.CHALLAN_NO, a.COMPANY_ID, a.INSERT_DATE, a.inserted_by as USER_ID, b.PI_WO_BATCH_NO, sum(NVL(c.quantity, 0)) as FINISH_ISSUE_RETURN_QTY, c.PROD_ID
		from inv_receive_master a, inv_transaction b, order_wise_pro_details c 
		where a.id=b.mst_id and b.id=c.trans_id and a.company_id=$company_id and a.entry_form in(52,209) and c.entry_form in(52,209) and c.po_breakdown_id in($order_id) and c.prod_id in($prod_ids) and c.trans_type=4  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
		group by a.recv_number, a.receive_date, a.challan_no, a.company_id, a.insert_date, a.inserted_by, b.pi_wo_batch_no, c.prod_id
		order by a.receive_date desc";
	}

	$sql_res=sql_select($sql);
	if (count($sql_res) > 0)
	{
		foreach ($sql_res as $value)
		{
			$batch_id_arr[$value['PI_WO_BATCH_NO']] = $value['PI_WO_BATCH_NO'];
		}
		$batch_Ids = implode(',',array_keys($batch_id_arr));
		$batch_arr=return_library_array( "select ID, BATCH_NO from pro_batch_create_mst where id in($batch_Ids) and status_active=1 and is_deleted=0", 'ID', 'BATCH_NO');
	}	

	$table_width = 1150;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['value_total_finish_issue_return_qty'],
					col: [6],
					operation: ['sum'],
					write_method: ['innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="10" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="10" style="font-size:16px" width="100%" align="center"><strong>Return From Cutting  Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="12">Return From Cutting Info</th>
					</tr>
					<tr>
	                    <th width="30">SL</th>
	                    <th width="150">Issue Rtn No</th>
	                    <th width="150">Issue Ret. Company</th>
	                    <th width="100">Challan No</th>
	                    <th width="100">Return Date</th>
	                    <th width="100">Batch No</th>
	                    <th width="100">Return Qty</th>	                    
	                    <th width="200">Fabric Description</th>           	
	                    <th width="120">Insert Date & Time</th>
	                    <th width="100">User Name</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; $total_finish_issue_return_qty=0;                     
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="150"><p><? echo $row['RECV_NUMBER']; ?></p></td>
                            <td width="150"><p><? echo $companyArr[$row['COMPANY_ID']]; ?></p></td>
                            <td width="100"><p><? echo $row['CHALLAN_NO']; ?></p></td>
                            <td width="100"><p><? echo change_date_format($row['RETURN_DATE']); ?></p></td>
                            <td width="100"><p><? echo $batch_arr[$row['PI_WO_BATCH_NO']]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($row['FINISH_ISSUE_RETURN_QTY'],2); ?></p></td>
                            <td width="200" align="center"><p><? echo $product_arr[$row['PROD_ID']]; ?></p></td>
                            <td width="120" align="center"><p><? echo $row['INSERT_DATE']; ?></p></td>
                            <td width="100" align="center"><p><? echo $user_arr[$row['USER_ID']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_finish_issue_return_qty += $row['FINISH_ISSUE_RETURN_QTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<th width="30"></th>
	                    <th width="150"></th>
	                    <th width="150"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="100" align="right">Total Return:</th>
	                    <th width="100" align="right" id="value_total_finish_issue_return_qty"><? echo number_format($total_finish_issue_return_qty,2,'.',''); ?></th>
	                    <th width="200"></th>
	                    <th width="120"></th>
	                    <th width="100"></th>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'fab_stock_in_hand_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job, $finishRec_prodID, $finishRec_batchID, $finishRecIssueReturn_prodID,$fin_recv_rtn_qnty) = explode('__', $data);
	$finishRec_prodIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRec_prodID,',')))));
	$finishRec_batchIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRec_batchID,',')))));
	$finishRecIssueReturn_prodIDs = implode(',', array_flip(array_flip(explode(',', rtrim($finishRecIssueReturn_prodID,',')))));
		
	if ($finishRec_prodIDs != '' && $finishRec_batchIDs != '')
	{
		$all_prodIDs = $finishRec_prodIDs.','.$finishRecIssueReturn_prodIDs;
		
		$product_arr=return_library_array( "select ID, PRODUCT_NAME_DETAILS from 
			product_details_master where id in(".rtrim($all_prodIDs,',').") and status_active=1 and is_deleted=0", 'ID', 'PRODUCT_NAME_DETAILS');		
	}	

	$companyArr = return_library_array("select ID, COMPANY_NAME from lib_company",'ID','COMPANY_NAME');	
	$rack_arr = return_library_array( "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from lib_floor_room_rack_mst where company_id=$company_id and status_active=1 and is_deleted=0", 'FLOOR_ROOM_RACK_ID', 'FLOOR_ROOM_RACK_NAME');

	$color_arr = return_library_array( "select ID, COLOR_NAME from lib_color", 'ID', 'COLOR_NAME');
	
	$sql="SELECT b.BATCH_ID, b.PROD_ID, b.RACK_NO, sum(NVL(c.quantity, 0)) as RECEIVE_QNTY 
    from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c 
    where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form in (37,68,17) and b.batch_id in($finishRec_batchIDs) and c.entry_form in (37,68,17) and c.po_breakdown_id in($order_id) and c.prod_id in($finishRec_prodIDs) and c.trans_type=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
    group by b.batch_id, b.prod_id, b.rack_no";
     
    $sql_res=sql_select($sql);    
    $finish_fabric_arr=array();
    foreach ($sql_res as $row) 
	{		
		$finish_fabric_arr[$row['BATCH_ID']]['RECEIVE_QNTY'] += $row['RECEIVE_QNTY'];
		$finish_fabric_arr[$row['BATCH_ID']]['PROD_ID']  = $row['PROD_ID'];
		$finish_fabric_arr[$row['BATCH_ID']]['RACK_NO']  = $row['RACK_NO'];		
	}
	//echo '<pre>';print_r($finish_fabric_arr); 
	$sql_issue="SELECT b.BATCH_ID, b.PROD_ID, b.RACK_NO, c.color_id as COLOR_ID, sum(NVL(c.quantity, 0)) as ISSUE_QNTY 
    from inv_issue_master a, inv_finish_fabric_issue_dtls b, order_wise_pro_details c 
    where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$company_id and a.entry_form in (18,71) and c.entry_form in (18,71) and c.po_breakdown_id in($order_id) and c.trans_type=2  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
    group by b.batch_id, b.prod_id, b.rack_no, c.color_id 
    union all
	select b.batch_id as BATCH_ID, b.PROD_ID, d.rack as RACK_NO, c.color_id as COLOR_ID,  sum( c.quantity ) as ISSUE_QNTY
	from inv_issue_master a, inv_wvn_finish_fab_iss_dtls b, order_wise_pro_details c, inv_transaction d
	where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=3 and a.entry_form in (19) and c.entry_form in (19) and c.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.issue_purpose in (9,44,31,4) and c.po_breakdown_id in($order_id) and b.trans_id=d.id and c.trans_id=d.id and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 
	group by  b.batch_id, b.prod_id, d.rack, c.color_id ";
	//and c.prod_id in($finishRecIssueReturn_prodIDs)

    $sql_issue_res=sql_select($sql_issue);
    foreach ($sql_issue_res as $row) 
	{		
		$finish_fabric_arr[$row['BATCH_ID']]['ISSUE_QNTY'] += $row['ISSUE_QNTY'];
		$finish_fabric_arr[$row['BATCH_ID']]['PROD_ID']  = $row['PROD_ID'];
		$finish_fabric_arr[$row['BATCH_ID']]['RACK_NO']  = $row['RACK_NO'];		
	}
	//echo '<pre>';print_r($finish_fabric_arr); 
	$sql_issue_return="SELECT b.BATCH_ID, b.PROD_ID, b.RACK_NO, sum(NVL(c.quantity, 0)) as ISSUE_RETURN_QTY
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c 
	where a.id=b.mst_id and b.trans_id=c.trans_id and a.company_id=$company_id and a.entry_form=52 and c.entry_form=52 and c.po_breakdown_id in($order_id) and c.prod_id in($finishRecIssueReturn_prodIDs) and c.trans_type=4  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0
	group by b.batch_id, b.prod_id, b.rack_no";
	$sql_issue_return_res=sql_select($sql_issue_return);
    foreach ($sql_issue_return_res as $row) 
	{		
		$finish_fabric_arr[$row['BATCH_ID']]['ISSUE_RETURN_QTY'] += $row['ISSUE_RETURN_QTY'];
		$finish_fabric_arr[$row['BATCH_ID']]['PROD_ID']  = $row['PROD_ID'];
		$finish_fabric_arr[$row['BATCH_ID']]['RACK_NO']  = $row['RACK_NO'];		
	}

	$sql_transfer ="SELECT d.pi_wo_batch_no as BATCH_ID, c.color_id as COLOR_ID, c.prod_id as PROD_ID, d.rack as RACK_NO, c.trans_type as TRANS_TYPE, sum(c.quantity) as QUANTITY
	from order_wise_pro_details c, inv_transaction d
	where c.status_active=1 and c.is_deleted=0 and c.entry_form in(14,306,258,202) and c.trans_type in(5,6,3) and c.po_breakdown_id in ($order_id) and c.trans_id=d.id
	group by d.pi_wo_batch_no, c.color_id,  c.prod_id, c.trans_type, d.rack";

	$sql_transfer_res=sql_select($sql_transfer);
    foreach ($sql_transfer_res as $row) 
	{	
		if($row['TRANS_TYPE'] == 5)
		{
			$finish_fabric_arr[$row['BATCH_ID']]['TRANS_IN'] += $row['QUANTITY'];
		}
		else if($row['TRANS_TYPE'] == 6)
		{
			$finish_fabric_arr[$row['BATCH_ID']]['TRANS_OUT'] += $row['QUANTITY'];
		}
		else if($row['TRANS_TYPE'] == 3)
		{
			$finish_fabric_arr[$row['BATCH_ID']]['RECIVE_RETURN'] += $row['QUANTITY'];
		}
		
		$finish_fabric_arr[$row['BATCH_ID']]['PROD_ID']  = $row['PROD_ID'];
		$finish_fabric_arr[$row['BATCH_ID']]['RACK_NO']  = $row['RACK_NO'];		
		$finish_fabric_arr[$row['BATCH_ID']]['COLOR_ID']  = $row['COLOR_ID'];	

		$trans_batch_arr[$row['BATCH_ID']]= $row['BATCH_ID'];	
	}

	if(!empty($trans_batch_arr)){
		$finishRec_batchIDs .=",".implode(",", $trans_batch_arr); 
	}

	$batch_sql=sql_select( "select id as ID, batch_no as BATCH_NO, color_id as COLOR_ID from pro_batch_create_mst where id in($finishRec_batchIDs) and status_active=1 and is_deleted=0");
	foreach ($batch_sql as $key => $value) {
		$batch_arr[$value["ID"]]["BATCH_NO"] = $value["BATCH_NO"];
		$batch_arr[$value["ID"]]["COLOR_ID"] = $value["COLOR_ID"];
	}


	$table_width = 950;
	?>
	<script>
		var tableFilters = {
			col_operation: {
				id: ['value_tot_receive','value_total_issue_qnty','value_total_issue_return_qty','value_stock'],
				col: [4,5,6,7],
				operation: ['sum','sum','sum','sum'],
				write_method: ['innerHTML','innerHTML','innerHTML','innerHTML']
			}
		}

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px" class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="8" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="8" style="font-size:16px" width="100%" align="center"><strong>Fabric Stock In Hand Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr>
						<th colspan="9">Fabric Stock In Hand Details</th>
					</tr>
					<tr>
						<th width="30">SL</th>
	                    <th width="100">Batch No</th>
	                    <th width="100">Color</th>
	                    <th width="220">Fabric Description</th>
	                    <th width="100">Fin. Rcv. Qty.</th>
	                    <th width="100">Issue Qnty</th>
	                    <th width="100">Issue Return</th>
	                    <th width="100">Stock Qty</th>
	                    <th width="100">Rack No</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	                $i=1;$total_receive_qnty=$total_issue_qnty=$total_issue_return_qty=0;
               		foreach ($finish_fabric_arr as $batch_id => $value)
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	               		$receive_quantity = $value['RECEIVE_QNTY']+$value['TRANS_IN'] -$value['RECIVE_RETURN'];
	               		$issue_quantity = $value['ISSUE_QNTY']+$value['TRANS_OUT'];
	               		?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100"><p><? echo $batch_arr[$batch_id]["BATCH_NO"]; ?></p></td>
							<td width="100"><p><? echo $color_arr[$batch_arr[$batch_id]["COLOR_ID"]]; ?></p></td>
                            <td width="220"><p><? echo $product_arr[$value['PROD_ID']]; ?></p></td>
                            <td width="100" align="right" title="<? echo 'receive='.$value['RECEIVE_QNTY'].', trans in='.$value['TRANS_IN'];?>"><p><? echo number_format($receive_quantity,2); ?></p></td>
                            <td width="100" align="right" title="<? echo 'issue='.$value['ISSUE_QNTY'].', trans out='.$value['TRANS_OUT'];?>"><p><? echo number_format($issue_quantity,2); ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($value['ISSUE_RETURN_QTY'],2); ?></p></td>
                            <td width="100" align="right" title="Fin Rcv Qty+Issue Return-Issue Qnty">
                            <p>
                            	<? echo number_format(($receive_quantity-$issue_quantity),2); ?>
                            </p>
                            </td>
                            <td width="100" align="center"><p><? echo $rack_arr[$value['RACK_NO']]; ?></p></td>
                        </tr>
	                    <?
	                    $i++;
	                    $total_receive_qnty += $value['RECEIVE_QNTY'] + $value['TRANS_IN'] -$value['RECIVE_RETURN'];
	                    $total_issue_qnty += $value['ISSUE_QNTY']+$value['TRANS_OUT'];
	                    $total_issue_return_qty += $value['ISSUE_RETURN_QTY'];

                    }
                     $value_stock = $total_receive_qnty+$total_issue_return_qty-$total_issue_qnty;
                    ?>						
	                </tbody>
	            </table>           	
            </div>
            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
				<tfoot>
					<tr>
						<th width="30"></th>
	                    <th width="100"></th>
	                    <th width="100"></th>
	                    <th width="220">Total:</th>
	                    <th width="100" align="right" id="value_tot_receive"><? echo number_format($total_receive_qnty,2,'.','');?></th>
	                    <th width="100" align="right" id="value_total_issue_qnty"><? echo number_format($total_issue_qnty,2,'.','');?></th>
	                    <th width="100" align="right" id="value_total_issue_return_qty"><? echo number_format($total_issue_return_qty,2,'.','');?></th>
	                    <th width="100" align="right" id="value_stock"><? echo number_format($value_stock,2); ?></th>
	                    <th width="100"></th>
					</tr>
				</tfoot>
            </table>
        </div>          
    </fieldset>
    <script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'cutting_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job) = explode('__', $data);
	$companyArr = return_library_array("select id, company_name from lib_company",'id','company_name');
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$locationArr=return_library_array("select id,location_name from lib_location", "id", "location_name");

	if ($order_id != '')
   	{
   		$sql="SELECT a.PO_BREAK_DOWN_ID, a.PRODUCTION_DATE, a.PRODUCTION_SOURCE, a.SERVING_COMPANY, a.LOCATION,
 		SUM(CASE WHEN a.production_source=1 THEN b.production_qnty ELSE 0 END) as IN_HOUSE_CUT_QNTY,
  		SUM(CASE WHEN a.production_source=3 THEN b.production_qnty ELSE 0 END) as OUT_BOUND_CUT_QNTY
  		from pro_garments_production_mst a, pro_garments_production_dtls b 
  		where a.id=b.mst_id and a.po_break_down_id in($order_id) and a.company_id=$company_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  
  		group by a.po_break_down_id, a.serving_company, a.location, a.production_date, a.production_source  
  		order by a.production_date";
	}
	//echo $sql;die;
	$sql_res=sql_select($sql);
	$table_width = 680;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['total_sew_qty_in','total_sew_qty_out'],
					col: [2,3],
					operation: ['sum','sum'],
					write_method: ['innerHTML','innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="5" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="5" style="font-size:16px" width="100%" align="center"><strong>Actual Cutting Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr style="font-size:12px">
                        <th width="50" rowspan="2">Sl</th>
                        <th width="120" rowspan="2">Cutting Date</th>
                        <th colspan="2">Cutting Qty</th>
                        <th width="150" rowspan="2">Cutting Company</th>
                        <th width="" rowspan="2">Location</th>
                    </tr>
                    <tr style="font-size:12px">
                        <th width="100">In-house</th>
                        <th width="100">Out-bound</th>
                 	</tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; 
	               	$total_in_quantity=0;
	               	$total_out_quantity=0;
	               	                     
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; 
	               		else $bgcolor="#FFFFFF";

	               		$source= $row['PRODUCTION_SOURCE'];
					    if($source==3) $serving_company= $supplierArr[$row['SERVING_COMPANY']];
						else $serving_company= $companyArr[$row['SERVING_COMPANY']];

	               		?>
						<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50" align="center"><?= $i; ?></td>
							<td width="120" align="center"><p><?= change_date_format($row['PRODUCTION_DATE']); ?>&nbsp;</p></td>
                            <td width="100" align="right"><?= number_format($row['IN_HOUSE_CUT_QNTY']); ?></td>
                    		<td width="100" align="right"><?= number_format($row['OUT_BOUND_CUT_QNTY']); ?></td>
                    		<td width="150"><p>&nbsp;<?= $serving_company; ?></p></td>
                    		<td width=""><p><?= $locationArr[$row['LOCATION']]; ?></p></td>
                 		</tr>	
	                    <?
	                    $i++;
	                    $total_in_quantity  += $row['IN_HOUSE_CUT_QNTY'];
	                    $total_out_quantity += $row['OUT_BOUND_CUT_QNTY'];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<tr> 
	                        <th width="50">&nbsp;</th> 
	                        <th width="120">Total&nbsp;</th> 
	                        <th width="100" id="total_sew_qty_in" align="right"><? echo number_format($total_in_quantity); ?></th>
	                        <th width="100" id="total_sew_qty_out" align="right"><? echo number_format($total_out_quantity); ?></th>
	                        <th width="150">&nbsp;</td> 
	                        <th width="">&nbsp;</td> 
                     	</tr>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}



if ($action === 'cutting_used_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job) = explode('__', $data);
	$companyArr = return_library_array("select id, company_name from lib_company",'id','company_name');
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$locationArr=return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_name_arr=return_library_array("select id, short_name from lib_buyer where status_active=1", 'id','short_name');


	if ($order_id != '')
   	{
   		$sql="SELECT  a.CUT_NO,a.PRODUCTION_DATE,a.USED_QTY_KG,a.REJECT_QTY_KG_BREAK_DOWN,sum(a.production_quantity) as CUTQNTYPCS
  		from pro_garments_production_mst a
  		where a.po_break_down_id in($order_id) and a.company_id=$company_id and a.production_type=1  and a.status_active=1 and a.is_deleted=0 group by a.cut_no,a.PRODUCTION_DATE,a.USED_QTY_KG,a.REJECT_QTY_KG_BREAK_DOWN
  		order by a.production_date";
  		/*$sql="SELECT a.cut_no,a.PRODUCTION_DATE,a.USED_QTY_KG,a.REJECT_QTY_KG_BREAK_DOWN,
		  SUM(CASE WHEN a.production_source in(1,3) THEN b.production_qnty ELSE 0 END) as CUTQNTYPCS
		 from pro_garments_production_mst a, pro_garments_production_dtls b  
		 where  a.id=b.mst_id and a.po_break_down_id in($order_id) and a.company_id=$company_id and a.production_type=1 and b.production_type=1 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0
		 group by a.cut_no,a.PRODUCTION_DATE,a.USED_QTY_KG,a.REJECT_QTY_KG_BREAK_DOWN
		  order by a.production_date";*/
	}
	// echo $sql;die;
	$sql_po_info=sql_select("SELECT a.JOB_NO, a.BUYER_NAME, a.STYLE_REF_NO 
		from wo_po_details_master a, wo_po_break_down b 
		where a.job_no=b.job_no_mst and a.is_deleted=0 and b.status_active in(1,3) and b.is_deleted=0 and a.company_name=$company_id and b.id in($order_id) 
		group by a.job_no, a.buyer_name, a.style_ref_no");
	$jobNo=$sql_po_info[0]['JOB_NO'];
	$buyerName=$buyer_name_arr[$sql_po_info[0]['BUYER_NAME']];
	$styleRef=$sql_po_info[0]['STYLE_REF_NO'];


	$sql_res=sql_select($sql);
	$data_array = array();
	foreach ($sql_res as $val) 
	{
		$data_array[$val['PRODUCTION_DATE']][$val['CUT_NO']]['qty'] += $val['CUTQNTYPCS'];
		$data_array[$val['PRODUCTION_DATE']][$val['CUT_NO']]['used_qty_kg'] += $val['USED_QTY_KG'];
		$data_array[$val['PRODUCTION_DATE']][$val['CUT_NO']]['qty_break_down'] .= $val['REJECT_QTY_KG_BREAK_DOWN']."**";
	}
	// echo "<pre>";print_r($data_array);
	$table_width = 750;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['total_cutting_pcs_quantity','total_used_quantity','total_west_quantity','total_cutpanel_quantity','total_short_quantity','total_ttl_quantity'],
					col: [3,4,5,6,7,8],
					operation: ['sum','sum','sum','sum','sum','sum'],
					write_method: ['innerHTML','innerHTML','innerHTML','innerHTML','innerHTML','innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="5" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="5" style="font-size:16px" width="100%" align="center"><strong>Actual Cutting Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr style="font-size:12px">
						<th style="text-align: left;" colspan="10"><? echo "Buyer:	$buyerName ". " Style: $styleRef ". " Job: $jobNo "; ?></th>
					</tr>
					<tr style="font-size:12px">
                        <th width="50">Sl</th>
                        <th width="60">Cutting Date</th>
                        <th width="80">Cutting No</th>
                        <th width="80">Cutting Qty PCS</th>
                        <th width="80">Used Kg</th>
                        <th width="80">Westage KG</th>
                        <th width="80">Reject Cut Panel KG</th>
                        <th width="80">Short Pcs KG</th>
                        <th width="80">TTL Reject Qty</th>
                        <th width="80">Reject %</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; 
	               	$total_in_quantity=0;
	               	$total_out_quantity=0;

	               	foreach ($data_array as $date => $date_val) 
	               	{	               	                     
	               		foreach ($date_val as $cut_no => $row) 
	               		{
		               		if($i%2==0) $bgcolor="#E9F3FF"; 
		               		else $bgcolor="#FFFFFF";


		               		$break_down_data = array_unique(array_filter(explode("**", $row['qty_break_down'])));
							$ttl_rejQnty = 0;
							$ttl_waste = 0;
							$ttl_cutpanel = 0;
							$ttl_short_pcs = 0;
							foreach ($break_down_data as $key => $value) 
							{
								$break_down_ex = explode("__", $value);

								$ttl_waste += $break_down_ex[0];
								$ttl_cutpanel += $break_down_ex[1];
								$ttl_short_pcs += $break_down_ex[2];

								$ttl_rejQnty += $break_down_ex[0]+$break_down_ex[1]+$break_down_ex[2];
							}


		               		// $ttl_rejQnty=$break_down_ex[0]+$break_down_ex[1]+$break_down_ex[2];
		               		$recjPercnQnty=($ttl_rejQnty/$row['used_qty_kg'])*100; 

		               		?>
							<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td width="50" align="center"><?= $i; ?></td>
								<td width="60" align="center"><p><?= change_date_format($date); ?>&nbsp;</p></td>
	                            <td width="80" align="right"><?= $cut_no; ?></td>
	                            <td width="80" align="right"><?= $row['qty']; ?></td>
	                    		<td width="80" align="right"><?= number_format($row['used_qty_kg']); ?></td>
	                    		<td width="80" align="right"><p>&nbsp;<?= number_format($ttl_waste,0); ?></p></td>
	                    		<td width="80" align="right"><p>&nbsp;<?= number_format($ttl_cutpanel,0); ?></p></td>
	                    		<td width="80" align="right"><p>&nbsp;<?= number_format($ttl_short_pcs,0); ?></p></td>
	                    		<td width="80" align="right"><p>&nbsp;<?= number_format($ttl_rejQnty,0); ?></p></td>
	                    		<td width="80" align="right"><p>&nbsp;<?= number_format($recjPercnQnty,2,'.',''); ?></p></td>
	                 		</tr>	
		                    <?
		                    $i++;
		                    $total_used_quantity  += $row['used_qty_kg'];
		                    $total_west_quantity += $ttl_waste;
		                    $total_cutpanel_quantity += $ttl_cutpanel;
		                    $total_short_quantity += $ttl_short_pcs;
		                    $total_cutting_pcs_quantity +=  $row['qty'];
		                    $total_ttl_quantity += $ttl_rejQnty;
		                    //$total_rej_percn_quantity += $recjPercnQnty;
	                    }
	                }
                    $total_rej_percn_quantity=($total_ttl_quantity/$total_used_quantity)*100;
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<tr> 
							<th width="50">&nbsp;</th> 
							<th width="60">&nbsp;</th> 
							<th width="80">Total&nbsp;</th> 
							<th width="80" id="total_cutting_pcs_quantity" align="right"><? echo number_format($total_cutting_pcs_quantity); ?></th>
							<th width="80" id="total_used_quantity" align="right"><? echo number_format($total_used_quantity); ?></th>
							<th width="80" id="total_west_quantity" align="right"><? echo number_format($total_west_quantity); ?></th>
							<th width="80" id="total_cutpanel_quantity" align="right"><? echo number_format($total_cutpanel_quantity); ?></th>
							<th width="80" id="total_short_quantity" align="right"><? echo number_format($total_short_quantity); ?></th>
							<th width="80" id="total_ttl_quantity" align="right"><? echo number_format($total_ttl_quantity); ?></th>
							<th width="80"  align="right"><? echo number_format($total_rej_percn_quantity,2,'.',''); ?></th>
                     	</tr>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}

if ($action === 'cutting_westage_popup')
{
	echo load_html_head_contents('Report Info', '../../../', 1, 1,'','','');
	extract($_REQUEST);

	list($company_id, $job) = explode('__', $data);
	$companyArr = return_library_array("select id, company_name from lib_company",'id','company_name');
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$locationArr=return_library_array("select id,location_name from lib_location", "id", "location_name");

	if ($order_id != '')
   	{
   		$sql="SELECT  a.cut_no,a.PRODUCTION_DATE,a.USED_QTY_KG,a.REJECT_QTY_KG_BREAK_DOWN
  		from pro_garments_production_mst a
  		where a.po_break_down_id in($order_id) and a.company_id=$company_id and a.production_type=1  and a.status_active=1 and a.is_deleted=0   
  		order by a.production_date";
	}
	// echo $sql;die;
	$sql_res=sql_select($sql);
	$table_width = 510;
	?>
	<script>
		var tableFilters = {
				col_operation: {
					id: ['total_west_quantity','total_cutpanel_quantity','total_short_quantity','total_used_quantity'],
					col: [3,4,5,6],
					operation: ['sum','sum','sum','sum'],
					write_method: ['innerHTML','innerHTML','innerHTML','innerHTML']
				}
			}
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow='auto';
			document.getElementById('scroll_body').style.maxHeight='none';
			$('.flt').hide();
			var w = window.open('Surprise', '#');
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY='scroll';
			document.getElementById('scroll_body').style.maxHeight='380px';
			$('.flt').show();
		}				
	</script>
	<fieldset style="<? echo $table_width; ?>px; margin-left:3px">
		<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<div style="width:100%" id="report_container">
			<table width="<? echo $table_width; ?>" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<td colspan="5" style="font-size:16px" width="100%" align="center"><strong><? echo $companyArr[$company_id]; ?></strong>
						</td>
					</tr>
					<tr>
						<td colspan="5" style="font-size:16px" width="100%" align="center"><strong>Actual Cutting Statement</strong>
						</td>
					</tr>
				</thead>
			</table>
			<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_header">
				<thead>
					<tr style="font-size:12px">
                        <th width="50">Sl</th>
                        <th width="60">Cutting Date</th>
                        <th width="80">Cutting No</th>
                        <th width="80">Westage KG</th>
                        <th width="80">Reject Cut Panel KG</th>
                        <th width="80">Short Pcs KG</th>
                        <th width="80">Total</th>
                    </tr>
				</thead>				
            </table>
            <div style="width:<? echo $table_width+20; ?>px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="table_body">
	                <tbody>
	               	<?
	               	$i=1; 
	               	$total_in_quantity=0;
	               	$total_out_quantity=0;
	               	                     
               		foreach ($sql_res as $row) 
               		{
	               		if($i%2==0) $bgcolor="#E9F3FF"; 
	               		else $bgcolor="#FFFFFF";

	               		$break_down_ex = explode("__", $row['REJECT_QTY_KG_BREAK_DOWN']);
	               		$total = 0;
	               		$total = $break_down_ex[0] + $break_down_ex[1] + $break_down_ex[2];
	               		?>
						<tr bgcolor="<?= $bgcolor; ?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="50" align="center"><?= $i; ?></td>
							<td width="60" align="center"><p><?= change_date_format($row['PRODUCTION_DATE']); ?>&nbsp;</p></td>
                            <td width="80" align="right"><?= $row['CUT_NO']; ?></td>
                    		<td width="80" align="right"><p>&nbsp;<?= number_format($break_down_ex[0],0); ?></p></td>
                    		<td width="80" align="right"><p>&nbsp;<?= number_format($break_down_ex[1],0); ?></p></td>
                    		<td width="80" align="right"><p>&nbsp;<?= number_format($break_down_ex[2],0); ?></p></td>
                    		<td width="80" align="right"><?= number_format($total); ?></td>
                 		</tr>	
	                    <?
	                    $i++;
	                    $total_used_quantity  += $total;
	                    $total_west_quantity += $break_down_ex[0];
	                    $total_cutpanel_quantity += $break_down_ex[1];
	                    $total_short_quantity += $break_down_ex[2];
                    }
                    ?>						
	                </tbody>
	            </table>

	            <table border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="report_table_footer">
					<tfoot>
						<tr> 
	                        <th width="50">&nbsp;</th> 
	                        <th width="60">&nbsp;</th> 
	                        <th width="80">Total&nbsp;</th> 
	                        <th width="80" id="total_west_quantity" align="right"><? echo number_format($total_west_quantity); ?></th>
	                        <th width="80" id="total_cutpanel_quantity" align="right"><? echo number_format($total_cutpanel_quantity); ?></th>
	                        <th width="80" id="total_short_quantity" align="right"><? echo number_format($total_short_quantity); ?></th>
	                        <th width="80" id="total_used_quantity" align="right"><? echo number_format($total_used_quantity); ?></th>
                     	</tr>
					</tfoot>
	            </table>
	        </div>
	    </div>
	</fieldset>
	<script>setFilterGrid('table_body',-1, tableFilters);</script>
    <?
	exit();
}


?>
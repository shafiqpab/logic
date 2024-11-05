<?
session_start();
//ini_set('memory_limit','3072M');
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------

$user_id = $_SESSION['logic_erp']["user_id"];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if (!function_exists('wherenot_con_using_array')) 
{
	function wherenot_con_using_array($arrayData,$dataType=0,$table_coloum)
	{
		$chunk_list_arr=array_chunk($arrayData,999);
		if(count($chunk_list_arr)<1){return " and ".$table_coloum." in(0)";}
		$p=1;
		foreach($chunk_list_arr as $process_arr)
		{
			if($dataType==0){
				if($p==1){$sql .=" and (".$table_coloum." not in(".implode(',',$process_arr).")"; }
				else {$sql .=" or ".$table_coloum." not in(".implode(',',$process_arr).")";}
			}
			else{
				if($p==1){$sql .=" and (".$table_coloum." not in('".implode("','",$process_arr)."')"; }
				else {$sql .=" or ".$table_coloum." not in('".implode("','",$process_arr)."')";}
			}
			$p++;
		}
		
		$sql.=") ";
		return $sql;
	}
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1", "id", "yarn_count");


	$company_name=str_replace("'","",$cbo_company_name);
	$without_zero=str_replace("'","",$cbo_without_zero);
	$future_date = date("j-M-Y",strtotime(str_replace("'", "", $txt_date)));
	$ft_date = str_replace("'", "", $txt_date);
	$current_date = date("j-M-Y");
	$next_date = date('d-M-Y', strtotime("+1 day", strtotime($current_date)));
	
	/*==========================================================================================/
	/										main query 											/
	/==========================================================================================*/

	// $sql="SELECT a.JOB_NO,b.id as PO_ID, d.COUNT_ID,d.COPM_ONE_ID,d.COPM_TWO_ID from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_yarn_cost_dtls d where a.id=b.job_id  and a.id=c.job_id and b.job_id=c.job_id and b.job_id=d.job_id  and c.job_id=d.job_id and a.id=d.job_id and c.id=d.fabric_cost_dtls_id and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.shiping_status in(1,2) and d.count_id !=0 and d.copm_one_id !=0  order by d.count_id ";// and a.job_no='FAL-20-00339'


	$sql = "SELECT a.JOB_NO,b.id as PO_ID,f.count_id AS COUNT_ID,f.copm_one_id AS COPM_ONE_ID from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and a.company_name=$company_name and b.shiping_status in(1,2)";
	// echo $sql;die;
	$result=sql_select($sql);
	if(count($result)==0)
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
	}
	
	$dataArray = array();
	$job_no_array = array();
	$po_id_array = array();
	foreach($result as $row)
	{
		$comp = "";
		if ($row['COPM_ONE_ID'] != 0 || $row['COPM_ONE_ID'] != "") 
		{
            $comp = strtolower(trim($composition[$row['COPM_ONE_ID']]));
            // $comp .= $composition[$row['COPM_ONE_ID']];

			$dataArray[$comp][$count_array[$row['COUNT_ID']]]['count']=$row['COUNT_ID']; 
			$dataArray[$comp][$count_array[$row['COUNT_ID']]]['comp']=$row['COPM_ONE_ID']; 

			$job_no_array[$row['JOB_NO']] = $row['JOB_NO'];
			$po_id_array[$row['PO_ID']] = $row['PO_ID'];
        }

        /*if ($row['COPM_TWO_ID'] != 0) 
        {
            $comp .= $composition[$row['COPM_TWO_ID']] . "";
        }*/
	}
	unset($result);
	// echo "<pre>";print_r($dataArray);
	
	$poIds = implode(",", $po_id_array);
	$po_id_list_arr=array_chunk($po_id_array,999);
	$poCond = " and ";
	$p=1;
	foreach($po_id_list_arr as $poids)
    {
    	if($p==1) 
		{
			$poCond .="  ( b.po_break_down_id in(".implode(',',$poids).")"; 
		}
        else
        {
          $poCond .=" or b.po_break_down_id in(".implode(',',$poids).")";
      	}
        $p++;
    }
    $poCond .=")";
	// echo $poCond;die();
	
	/*==========================================================================================/
	/										yarn transaction 											/
	/==========================================================================================*/
	$sql_trns = "SELECT b.id as prod_id,b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST,b.AVAILABLE_QNTY, b.ALLOCATED_QNTY,b.CURRENT_STOCK
    from product_details_master b
    where b.status_active=1 and b.is_deleted=0  and b.company_id=$company_name and b.item_category_id=1";
    // echo $sql_trns;die();
    $trns_res = sql_select($sql_trns);
    $trans_qty_array = array();
	foreach ($trns_res as $row) 
	{    
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        	$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['available_qnty']+=$row['AVAILABLE_QNTY']; 
        	$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['allocated_qnty']+=$row['ALLOCATED_QNTY'];
        	$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['current_stock']+=$row['CURRENT_STOCK'];

			// $dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['count']=$row['YARN_COUNT_ID']; 
			// $dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['comp']=$row['YARN_COMP_TYPE1ST']; 
        }
	}
	unset($sql_trns);
	/*==========================================================================================/
	/									yarn receive as pre close PI							/
	/==========================================================================================*/
	/* $sql_receive = "SELECT a.id as trans_id,c.id as pi_id, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST,
	(case when a.transaction_type=1 and a.transaction_date<='$current_date' then a.cons_quantity else 0 end) as ALL_RCV,
	
	(case when a.transaction_type=1 and a.transaction_date='$current_date' then a.cons_quantity else 0 end) as TODAY_RCV
	from inv_transaction a, product_details_master b,COM_PI_MASTER_DETAILS c,COM_PI_ITEM_DETAILS d,wo_non_order_info_mst e,wo_non_order_info_dtls f,wo_non_order_info_dtls_plan g
	where b.id=a.prod_id and a.PI_WO_BATCH_NO = c.id and c.id=d.pi_id  and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.yarn_count_id !=0 and b.yarn_comp_type1st !=0 and b.item_category_id=1 and a.receive_basis in(1,2) and c.ref_closing_status=1 and e.id=d.work_order_id and f.id=d.work_order_dtls_id and f.id=g.po_dtls_id ";

	// echo $sql_receive;die;
	$recv_res = sql_select($sql_receive);
	$chk_array = array();
	$close_pi_array = array();
	foreach ($recv_res as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
			
			if($chk_array[$row['TRANS_ID']]=="")
			{
				$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['all_close_pi_rcv']+=$row['ALL_RCV'];
				$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['today_close_pi_plan_rcv']+=$row['TODAY_RCV']; 
				$chk_array[$row['TRANS_ID']] = $row['TRANS_ID'];
				
				// $dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['count']=$row['YARN_COUNT_ID']; 
				// $dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['comp']=$row['YARN_COMP_TYPE1ST']; 
				// $a+=$row['ALL_RCV'];
			}
			$close_pi_array[$row['PI_ID']] = $row['PI_ID'];
        }
	}
	// echo "<pre>";print_r($dataArray);die;
	unset($recv_res); */


	
	// ==================================== yarn pi qty ================================
	$sql = "SELECT a.id as WO_ID,e.id AS delv_plan_id,b.YARN_COUNT,b.YARN_COMP_TYPE1ST,b.YARN_COMP_TYPE2ND,e.delivery_plan_date,c.SUPPLIER_ID ,(case when e.delivery_plan_date between '$next_date' and '$future_date' then e.PLAN_QNTY else 0 end) as QUANTITY,(case when e.delivery_plan_date <= '$current_date' then e.PLAN_QNTY else 0 end) as ALL_QUANTITY,(case when e.delivery_plan_date = '$current_date' then e.PLAN_QNTY else 0 end) as TODAY_RCVABL,c.id as PI_ID,c.ref_closing_status from wo_non_order_info_mst a,wo_non_order_info_dtls b,com_pi_master_details c,com_pi_item_details d,wo_non_order_info_dtls_plan e where a.id=b.mst_id and a.id=d.work_order_id and b.id=d.work_order_dtls_id and c.id=d.pi_id and b.id=e.po_dtls_id and a.entry_form=144 and c.item_category_id=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.yarn_inhouse_date is not null and b.delivery_end_date is not null and a.company_name=$company_name  and b.yarn_comp_type1st !=0  and b.yarn_count !=0 ";// and b.YARN_COUNT=9 and b.yarn_comp_type1st=95 
	// echo $sql;die();	
	$yarn_pi_qty_array = array();
	$wo_id_array = array();
	$plan_wo_id_array = array();
	$piWiseWoIdArray = array();
	$sqlRes = sql_select($sql);
	foreach ($sqlRes as $row) 
	{
		$comp = "";
		if (($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") && $row['REF_CLOSING_STATUS']==0) 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
			if($chkArray2[$row['DELV_PLAN_ID']][$row['DELIVERY_PLAN_DATE']]=="") // omit duplicate plan qty
			{
				$yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['pi_qty'] += $row['QUANTITY']; 
				$yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['today_rcvabl'] += $row['TODAY_RCVABL']; 
				$yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['all_pi_qty'] += $row['ALL_QUANTITY']; 
				$chkArray2[$row['DELV_PLAN_ID']][$row['DELIVERY_PLAN_DATE']] = $row['DELV_PLAN_ID'];
				
			}
			// echo $row['ALL_QUANTITY']."<br>";
	        // $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]]['del_dt'] = $row['DELIVERY_PLAN_DATE']; 
	        // $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]]['del_end_dt'] = $row['DEL_END_DT']; 
	        $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['count'] = $row['YARN_COUNT']; 
	        $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['comp'] = $row['YARN_COMP_TYPE1ST']; 
        }
		$wo_id_array[$row['WO_ID']] = $row['WO_ID'];
		$plan_wo_id_array[$row['PI_ID']] = $row['PI_ID'];
		$plan_wo_id_array[$row['WO_ID']] = $row['WO_ID'];		
		$piWiseWoIdArray[$comp][$count_array[$row['YARN_COUNT']]][$row['SUPPLIER_ID']][$row['PI_ID']] = $row['WO_ID'];
	}
	unset($sqlRes);
	// echo "<pre>";print_r($piWiseWoIdArray);die();
	// ================================ calculate pipe line data ==============================
	$dateObj = new DateTime('today');
	$today = $dateObj->format('d-m-Y');
	$futerDate = date('d-m-Y',strtotime($future_date));
	$futureDate = new DateTime($futerDate);
	// print_r($futureDate);die();
	// $futureDate = $futureDateObj->format('d-m-Y');
	foreach ($yarn_pi_qty_array as $piId => $piData) 
	{
		foreach ($piData as $woId => $woData) 
		{
			foreach ($woData as $comp => $compData) 
			{
				foreach ($compData as $countId => $countData) 
				{
					foreach ($countData as $delv_date => $row) 
					{						
						// $del_start_dt 	= new DateTime($delv_date);

						if((strtotime($ft_date) >= strtotime($delv_date)) && (strtotime($next_date) <= strtotime($delv_date)))
						{								
							$dataArray[$comp][$countId]['pipe_line_qty'] += $row['pi_qty'];
							// echo $piId."=".$woId."=".$comp."=".$countId."=".$row['pi_qty']."<br>";
						}

						if(strtotime($current_date) == strtotime($delv_date))
						{								
							$dataArray[$comp][$countId]['today_rcvabl'] += $row['today_rcvabl'];
							// echo $piId."=".$woId."=".$comp."=".$countId."=".$row['pi_qty']."<br>";
						}
						$dataArray[$comp][$countId]['all_plan_qty'] += $row['all_pi_qty'];
						// echo $piId."=".$woId."=".$comp."=".$countId."=".$row['all_pi_qty']."<br>";
						$dataArray[$comp][$countId]['count'] = $row['count'];
						$dataArray[$comp][$countId]['comp'] = $row['comp'];
						// echo $comp."==".$countId."==".$pipe_line_qty."<br>";
						
					}
				}
			}
		}
	}
    // echo "<pre>";print_r($dataArray);die();
	unset($yarn_pi_qty_array);
	// ==================================== yarn wo qty ================================
	// $wo_id_cond = implode(",",$wo_id_array);
	$wo_id_cond = wherenot_con_using_array($wo_id_array,0,"a.id");
	$sql = "SELECT a.id as WO_ID,b.YARN_COUNT,b.YARN_COMP_TYPE1ST,b.YARN_COMP_TYPE2ND,e.delivery_plan_date, a.SUPPLIER_ID ,(case when e.delivery_plan_date between '$next_date' and '$future_date' then e.PLAN_QNTY else 0 end) as QUANTITY,(case when e.delivery_plan_date <= '$current_date' then e.PLAN_QNTY else 0 end) as ALL_QUANTITY,(case when e.delivery_plan_date = '$current_date' then e.PLAN_QNTY else 0 end) as TODAY_RCVABLE from wo_non_order_info_mst a,wo_non_order_info_dtls b,wo_non_order_info_dtls_plan e where a.id=b.mst_id and b.id=e.po_dtls_id and a.entry_form=144  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.yarn_inhouse_date is not null and b.delivery_end_date is not null and a.company_name=$company_name  and b.yarn_comp_type1st !=0  and b.yarn_count !=0  $wo_id_cond";
	// echo $sql;die;
		
	$yarn_wo_qty_array = array();
	$sqlRes = sql_select($sql);
	foreach ($sqlRes as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
	        $yarn_wo_qty_array[$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['wo_qty'] += $row['QUANTITY']; 
	        $yarn_wo_qty_array[$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['today_rcvable'] += $row['TODAY_RCVABLE']; 
	        $yarn_wo_qty_array[$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['all_wo_qty'] += $row['ALL_QUANTITY']; 
	        // $yarn_wo_qty_array[$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]]['del_dt'] = $row['DELIVERY_PLAN_DATE']; 
	        // $yarn_wo_qty_array[$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]]['del_end_dt'] = $row['DEL_END_DT']; 
	        $yarn_wo_qty_array[$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['count'] = $row['YARN_COUNT']; 
	        $yarn_wo_qty_array[$row['WO_ID']][$comp][$count_array[$row['YARN_COUNT']]][$row['DELIVERY_PLAN_DATE']]['comp'] = $row['YARN_COMP_TYPE1ST']; 
			$plan_wo_id_array[$row['WO_ID']] = $row['WO_ID'];

			$piWiseWoIdArray[$comp][$count_array[$row['YARN_COUNT']]][$row['SUPPLIER_ID']][$row['WO_ID']] = $row['WO_ID'];

        }
	}
	unset($sqlRes);
	
	foreach ($yarn_wo_qty_array as $woId => $woData) 
	{
		foreach ($woData as $comp => $compData) 
		{
			foreach ($compData as $countId => $countData) 
			{
				foreach ($countData as $delv_date => $row) 
				{						
					// $del_start_dt 	= new DateTime($delv_date);

					if((strtotime($ft_date) >= strtotime($delv_date)) && (strtotime($next_date) <= strtotime($delv_date)))
					{								
						$dataArray[$comp][$countId]['pipe_line_qty'] += $row['wo_qty'];
					}

					if(strtotime($current_date) == strtotime($delv_date))
					{								
						$dataArray[$comp][$countId]['today_rcvabl'] += $row['today_rcvabl'];
						// echo $piId."=".$woId."=".$comp."=".$countId."=".$row['pi_qty']."<br>";
					}
					$dataArray[$comp][$countId]['all_plan_qty'] += $row['all_wo_qty'];
					$dataArray[$comp][$countId]['count'] = $row['count'];
					$dataArray[$comp][$countId]['comp'] = $row['comp'];
					// echo $comp."==".$countId."==".$pipe_line_qty."<br>";
					
				}
			}
		}
	}
	// echo "<pre>";print_r($plan_wo_id_array);die();
	$wo_id_cond = where_con_using_array($plan_wo_id_array,0,"b.booking_id");

	/*==========================================================================================/
	/											yarn receive									/
	/==========================================================================================*/
	$sql_receive = "SELECT b.id as rcv_id,b.booking_id as PI_ID,b.SUPPLIER_ID,f.yarn_count_id,f.yarn_comp_type1st, (case when a.transaction_date<='$current_date' then a.cons_quantity else 0 end) AS ALL_RCV, (case when a.transaction_date='$current_date' then a.cons_quantity else 0 end) AS TODAY_RCV
	from inv_transaction a , inv_receive_master b, product_details_master f where a.mst_id=b.id and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.company_id=$company_name  and f.yarn_count_id>0 and b.receive_basis in(1,2) and a.prod_id=f.id  $wo_id_cond";//and f.yarn_count_id=9 and f.yarn_comp_type1st=95

	// echo $sql_receive;die;
	$recv_res = sql_select($sql_receive);
	$chk_array = array();
	$piWiseyarnRcvQtyArray = array();
	$woIdArray = array();
	// $piWiseWoIdArray = array();
	foreach ($recv_res as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
			// if($row['REF_CLOSING_STATUS']==0)
			// {
			// 	if($chk_array[$row['TRANS_ID']]=="")
			// 	{
					$piWiseyarnRcvQtyArray[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['SUPPLIER_ID']][$row['PI_ID']]['all_plan_rcv']+=$row['ALL_RCV'];
					// $dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['all_plan_rcv']+=$row['ALL_RCV'];
					$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['today_plan_rcv']+=$row['TODAY_RCV']; 
					$chk_array[$row['TRANS_ID']] = $row['TRANS_ID'];
					// echo $row['PI_ID']."=".$row['ALL_RCV']."<br>";
					// $a+=$row['ALL_RCV'];
			// 	}
			// }
				
			
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['count']=$row['YARN_COUNT_ID']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['comp']=$row['YARN_COMP_TYPE1ST']; 
			$woIdArray[$row['WO_ID']] = $row['WO_ID'];		
			// $piWiseWoIdArray[$row['PI_ID']] = $row['WO_ID'];
        }
	}
	// echo "<pre>";print_r($piWiseyarnRcvQtyArray);die;
	unset($recv_res);
	// echo $a;die;
	// $piWiseWoIdArray

	// $piWiseWoIdArray[$comp][$count_array[$row['YARN_COUNT']]][$row['SUPPLIER_ID']][$row['PI_ID']] = $row['WO_ID'];
	$wo_chk_arr = array();
	foreach ($piWiseWoIdArray as $compkey => $compdata) 
	{
		foreach ($compdata as $countkey => $count_data) 
		{
			foreach ($count_data as $supkey => $sup_data) 
			{
				foreach ($sup_data as $pikey => $wo_id) 
				{
					// if($wo_chk_arr[$supkey][$pikey]=="")
					// {
						
						$rcvQty = $piWiseyarnRcvQtyArray[$compkey][$countkey][$supkey][$pikey]['all_plan_rcv'];
						
						if(!$rcvQty)
						{
							$rcvQty = $piWiseyarnRcvQtyArray[$compkey][$countkey][$supkey][$wo_id]['all_plan_rcv'];
							// echo $rcvQty."<br>";
						}
						// echo $compkey."=".$countkey."=".$supkey."=".$pikey."=".$rcvQty."<br>";
						$dataArray[$compkey][$countkey]['all_plan_rcv']+=$rcvQty;
						$wo_chk_arr[$supkey][$pikey] = $wo_id;
					// }
					
				}

			}
		}
	}
	
	// ====================================== wo qty =====================================
	if(count($woIdArray)>0)
	{
		$wo_id_cond = wherenot_con_using_array($woIdArray,0,"d.id");
	}
	/* $sql_receive = "SELECT e.id as wo_id,a.id as trans_id, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST,
	(case when a.transaction_type=1 and a.transaction_date<'$current_date' then a.cons_quantity else 0 end) as ALL_RCV,
	
	(case when a.transaction_type=1 and a.transaction_date='$current_date' then a.cons_quantity else 0 end) as TODAY_RCV
	from inv_transaction a, product_details_master b,WO_NON_ORDER_INFO_MST e, WO_NON_ORDER_INFO_DTLS f, WO_NON_ORDER_INFO_DTLS_PLAN g
	where b.id=a.prod_id and f.id=g.PO_DTLS_ID and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.yarn_count_id !=0 and b.yarn_comp_type1st !=0 and b.item_category_id=1 and a.receive_basis=2 $wo_id_cond"; */
	// echo $sql_receive;die();
	// $btbRes = sql_select($sql_receive);


	// ============================= rcv return against work order ==============================
	if(count($close_pi_array)>0)
	{
		$close_pi_cond = wherenot_con_using_array($close_pi_array,0,"c.booking_id");
	}
	$wo_id_cond = where_con_using_array($plan_wo_id_array,0,"c.booking_id");

	$sql = "SELECT c.BOOKING_ID as WO_ID,f.yarn_count_id,f.yarn_comp_type1st, (case when a.transaction_date<='$current_date' then a.cons_quantity else 0 end) AS RCV_RTN_QTY, (case when a.transaction_date='$current_date' then a.cons_quantity else 0 end) AS TODAY_RCV_RTN_QTY
	from inv_transaction a , inv_issue_master b, inv_receive_master c, product_details_master f where a.mst_id=b.id  and a.prod_id=f.id and b.received_id=c.id and a.transaction_type=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0  and f.yarn_count_id>0 and c.RECEIVE_BASIS in(1,2) and b.company_id=$company_name $close_pi_cond $wo_id_cond";
	// echo $sql;die;
	$yarn_res = sql_select($sql);
	// ==========================
	foreach ($yarn_res as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "")
		{
			$comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['all_plan_rcv'] -= $row['RCV_RTN_QTY']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['today_plan_rcv'] -= $row['TODAY_RCV_RTN_QTY']; 
		}
	}

	// ============================= rcv return as per PI ==============================
	$sql = "SELECT b.received_id, b.PI_ID,f.yarn_count_id,f.yarn_comp_type1st, (case when a.transaction_date<'$current_date' then a.cons_quantity else 0 end) AS RCV_RTN_QTY, (case when a.transaction_date='$current_date' then a.cons_quantity else 0 end) AS TODAY_RCV_RTN_QTY
	from inv_transaction a , inv_issue_master b, product_details_master f where a.mst_id=b.id and a.transaction_type=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.company_id=$company_name and f.yarn_count_id>0 and a.prod_id=f.id and b.PI_ID is not null";
	// echo $sql;die();
	$yarn_res = sql_select($sql);
	// ==========================
	foreach ($yarn_res as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "")
		{
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['all_plan_rcv'] -= $row['RCV_RTN_QTY']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['today_plan_rcv'] -= $row['TODAY_RCV_RTN_QTY']; 
		}
	}
	unset($yarn_res);
	// echo "<pre>";print_r($dataArray);die;
	/*==========================================================================================/
	/										yarn receive 										/
	/==========================================================================================*/
	$sql_receive = "SELECT b.id as prod_id, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST,
	sum(case when a.transaction_type in (1,4,5) and a.transaction_date<='$current_date' then a.cons_quantity else 0 end) as RCV_TOTAL_OPENING,
	
	sum(case when a.transaction_type in (1,4,5) and a.transaction_date='$current_date' then a.cons_quantity else 0 end) as TODAY_RCV
	from inv_transaction a, product_details_master b
	where b.id=a.prod_id and a.transaction_type in (1,4,5) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.yarn_count_id !=0 and b.yarn_comp_type1st !=0 and b.item_category_id=1
	group by b.id, b.yarn_count_id, b.yarn_comp_type1st";
	// echo $sql_receive;
	$recv_res = sql_select($sql_receive);
	// $receive_qty_array = array();
	foreach ($recv_res as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        	$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['rcv_qty']+=$row['RCV_TOTAL_OPENING']; 
        	$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['today_yarn_rcv']+=$row['TODAY_RCV']; 
			
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['count']=$row['YARN_COUNT_ID']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['comp']=$row['YARN_COMP_TYPE1ST']; 
        }
	}
	unset($recv_res);
	// echo "<pre>";print_r($dataArray);
	/*==========================================================================================/
	/										yarn issue 											/
	/==========================================================================================*/
	$sql_issue = "SELECT b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, b.YARN_COMP_TYPE2ND,
    (case when a.transaction_type in (2,3,6) and a.transaction_date<='$current_date' then a.cons_quantity else 0 end) as ISSUE_TOTAL_OPENING,	
    (case when a.transaction_type=3 and a.transaction_date='$current_date' then a.cons_quantity else 0 end) as RCV_RTN_QTY
    from inv_transaction a,product_details_master b
    where a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and b.yarn_count_id !=0 and b.yarn_comp_type1st !=0 and b.item_category_id=1 and a.transaction_type in (2,3,6)";
    // echo $sql_issue;die();
    $issue_res = sql_select($sql_issue);
    $issue_qty_array = array();
	foreach ($issue_res as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        	$issue_qty_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['issue_qty']+=$row['ISSUE_TOTAL_OPENING'];
        	$issue_qty_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['rcv_rtn_qty']+=$row['RCV_RTN_QTY'];
        	// if($count_check[$row['YARN_COMP_TYPE1ST']][$row['YARN_COUNT_ID']])
        	// {} 
			// $issue_qty_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['count']=$row['YARN_COUNT_ID']; 
			// $issue_qty_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['comp']=$row['YARN_COMP_TYPE1ST']; 

			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['count']=$row['YARN_COUNT_ID']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['comp']=$row['YARN_COMP_TYPE1ST']; 
        }

       /* if ($row['YARN_COMP_TYPE2ND'] != 0) 
        {
            $comp .= $composition[$row['YARN_COMP_TYPE2ND']] . "";
        }*/
	}
	unset($issue_res);

	/*==========================================================================================/
	/									get non closing booking  id								/
	/==========================================================================================*/
	$sql = "SELECT DISTINCT a.id from WO_NON_ORD_SAMP_BOOKING_MST a, WO_NON_ORD_SAMP_BOOKING_DTLS b, SAMPLE_DEVELOPMENT_DTLS c where a.id=b.booking_mst_id and b.style_id=c.sample_mst_id and b.ENTRY_FORM_ID=140 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.IS_COMPLETE_PROD is null and a.booking_type=4 and a.IS_APPROVED=1";
	$res = sql_select($sql);
	$closing_req_id_arr = array();
	foreach ($res as $v) 
	{
		$closing_req_id_arr[$v['ID']] = $v['ID'];
	}

	/*==========================================================================================/
	/						yarn issue qty from smple without order								/
	/==========================================================================================*/
	if(count($closing_req_id_arr)>0)
	{
		$booking_id_cond = where_con_using_array($closing_req_id_arr,0,"d.id");
	}
	$sqlIssue = "SELECT d.id as BOOKING_ID, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, b.YARN_COMP_TYPE2ND,
     a.return_qnty as QTY,a.cons_quantity as ISSUE_QTY
    from inv_transaction a, inv_issue_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_name and b.yarn_count_id !=0 and b.yarn_comp_type1st !=0 and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and d.item_category=2 and d.booking_type=4 and c.issue_basis=1 and c.issue_purpose=8 and a.transaction_type=2 and d.IS_APPROVED=1 $booking_id_cond";// and a.return_qnty>0
    // echo $sqlIssue;die();
    $issueRes = sql_select($sqlIssue);
    $smn_data_array = array();
	$non_order_booking_id_array = array();
	$non_order_issue_booking_id_array = array();
	foreach ($issueRes as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        	$smn_data_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['return_qnty'] += $row['QTY']; 
        	$smn_data_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['issue_qty'] += $row['ISSUE_QTY'];  

			$smn_data_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['count_id'] = $row['YARN_COUNT_ID'];
			$smn_data_array[$comp][$count_array[$row['YARN_COUNT_ID']]]['copm_one_id'] = $row['YARN_COMP_TYPE1ST'];

        	$non_order_booking_id_array[$row['BOOKING_ID']] = $row['BOOKING_ID']; 

			if($row['ISSUE_QTY'])
			{
				$non_order_issue_booking_id_array[$comp][$count_array[$row['YARN_COUNT_ID']]][$row['BOOKING_ID']] = $row['BOOKING_ID'];  
			}
        }
	}
	unset($issueRes);
	// echo "<pre>";print_r($non_order_issue_booking_id_array);die();
	/* if(count($non_order_booking_id_array))
	{
		$booking_id_list_arr=array_chunk($non_order_booking_id_array,999);
		$bidCond = " and ";
		$p=1;
		foreach($po_id_list_arr as $boids)
		{
			if($p==1) 
			{
				$bidCond .="  ( d.id in(".implode(',',$boids).")"; 
			}
			else
			{
			  $bidCond .=" or d.id in(".implode(',',$boids).")";
			}
			$p++;
		}
		$bidCond .=")";
	} */
	if(count($non_order_booking_id_array))
	{
		$bidCond = where_con_using_array($non_order_booking_id_array,0,"d.id");
	}
	/*==========================================================================================/
	/					yarn issue rtn qty from smple without order								/
	/==========================================================================================*/
	$sqlRcv = "SELECT b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, b.YARN_COMP_TYPE2ND,
     a.cons_quantity as QTY
    from inv_transaction a, inv_receive_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_name and b.yarn_count_id !=0 and b.yarn_comp_type1st !=0 and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and c.entry_form=9 and d.item_category=2 and d.booking_type=4 and c.receive_basis=1 and c.booking_without_order=1 and a.transaction_type=4 and d.IS_APPROVED=1 $bidCond";//$bidCond
    // echo $sqlRcv;die();
    $receiveRes = sql_select($sqlRcv);
    $non_order_rev_rtn_qty_array = array();
	foreach ($receiveRes as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        	$non_order_rev_rtn_qty_array[$comp][$count_array[$row['YARN_COUNT_ID']]] += $row['QTY']; 
        }
	}
	unset($issueRes);
	/*==========================================================================================/
	/										get non order sample data							/
	/==========================================================================================*/

	// $sql_smn = "SELECT a.id as BOOKING_ID,d.COPMPOSITION_ID,d.COUNT_ID,b.GREY_FABRIC from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,lib_yarn_count_determina_mst c, lib_yarn_count_determina_dtls d where a.booking_no=b.booking_no and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and b.lib_yarn_count_deter_id > 0 and c.id=d.mst_id and c.id=b.lib_yarn_count_deter_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.copmposition_id>0 and d.count_id>0 and a.is_approved=1";
	if(count($closing_req_id_arr)>0)
	{
		$booking_id_cond = where_con_using_array($closing_req_id_arr,0,"a.id");
	}
	$sql_smn = "SELECT a.id as BOOKING_ID,b.COPM_ONE_ID,b.count_id,b.CONS_QNTY from wo_non_ord_samp_booking_mst a,SAMPLE_DEVELOPMENT_YARN_DTLS b where a.id=b.booking_mst_id and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and b.COUNT_ID > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and a.is_approved=1 $booking_id_cond";

	// echo $sql_smn;die();
	$smnRes = sql_select($sql_smn);
	$smn_booking_data_array = array();
	foreach ($smnRes as $val) 
	{
		$comp = "";
		if ($val['COPM_ONE_ID'] != 0 || $val['COPM_ONE_ID'] != "") 
		{
            $comp = strtolower(trim($composition[$val['COPM_ONE_ID']]));
			$smn_data_array[$comp][$count_array[$val['COUNT_ID']]]['req_qty'] += $val['CONS_QNTY'];
			$smn_data_array[$comp][$count_array[$val['COUNT_ID']]]['count_id'] = $val['COUNT_ID'];
			$smn_data_array[$comp][$count_array[$val['COUNT_ID']]]['copm_one_id'] = $val['COPM_ONE_ID'];
		}
	}
	// echo "<pre>";print_r($smn_data_array);die();
	$smn_booking_qty_array = array();
	foreach ($smn_data_array as $comp_name => $compData) 
	{
		foreach ($compData as $count_name => $row) 
		{
			$issueQty = 0;
			// $issueQty = $non_order_issue_qty_array[$comp_name][$count_name]['issue_qty'];
			$bal = $row['req_qty'] - $row['issue_qty'];
			// echo $row ."-". $issueQty."<br>";
			$smn_booking_qty_array[$comp_name][$count_name] += $bal;
			$dataArray[$comp_name][$count_name]['count']=$row['count_id']; 
			$dataArray[$comp_name][$count_name]['comp']=$row['copm_one_id']; 
		}
	}
	// echo "<pre>";print_r($smn_booking_qty_array);die();

	//================================ excess yarn qty ================================
	$excsPoCond = str_replace("b.po_break_down_id", "b.po_break_down_id", $poCond);
    $sqlExcess = "SELECT b.po_break_down_id as PO_ID, e.COUNT_ID,e.COPM_ONE_ID, b.grey_fab_qnty as QTY,e.CONS_RATIO FROM wo_booking_mst a,wo_booking_dtls b,WO_PRE_COST_FAB_YARN_COST_DTLS e, WO_PRE_COST_FABRIC_COST_DTLS f where a.booking_no=b.booking_no and a.is_short=1  and b.PRE_COST_FABRIC_COST_DTLS_id=f.id and f.id=e.FABRIC_COST_DTLS_ID  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_name and a.booking_type=1 $excsPoCond";
    // echo $sqlExcess;die();
    $bookingRes = sql_select($sqlExcess);
    $excess_qty_array = array();
    foreach ($bookingRes as $val) 
    {
    	$comp = "";
		if ($val['COPM_ONE_ID'] != 0 || $val['COPM_ONE_ID'] != "") 
		{
			$comp = strtolower(trim($composition[$val['COPM_ONE_ID']]));
			$excess_yearn_kg = $val['QTY'] * ($val['CONS_RATIO']/100);
        	$excess_qty_array[$val['PO_ID']][$comp][$count_array[$val['COUNT_ID']]]['excess_qty'] += $excess_yearn_kg;
        }
    }
    unset($bookingRes);
    // echo "<pre>";print_r($excess_qty_array);die();
	/*==========================================================================================/
	/										yarn req. qty										/
	/==========================================================================================*/
	/* $condition= new condition();     
    $condition->po_id_in($poIds);     
    $condition->init();

    $yarn= new yarn($condition);     
    $yarnReqQtyArr=$yarn->getOrderCountAndCompositionWiseYarnQtyArray();
    // echo $yarn->getQuery(); die; 
    // echo "<pre>";print_r($yarnReqQtyArr);die();
    $req_qty_array = array();

    foreach ($yarnReqQtyArr as $poId => $poData) 
    {
        foreach ($poData as $countId => $countData) 
        {
            foreach ($countData as $compId => $row) 
            {
                $req_qty_array[$poId][strtolower(trim($composition[$compId]))][$count_array[$countId]] += $row;
            }
        }        
    } */
    // unset($yarnReqQtyArr);
    // echo "<pre>";print_r($yarnReqQtyArr);die();

	/*==========================================================================================/
	/										get pipe line qty									/
	/==========================================================================================*/
	$sqlYarn = "SELECT c.booking_id as PI_ID, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST, b.YARN_COMP_TYPE2ND, a.cons_quantity AS RCV_QTY	
	from inv_transaction a , inv_receive_master c, product_details_master b where a.mst_id=c.id and b.id=a.prod_id and a.transaction_type in(1) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_name and b.yarn_count_id !=0 and b.yarn_comp_type1st !=0 and b.item_category_id=1 and c.receive_basis=1";// and b.YARN_COMP_TYPE1ST = 451 and b.YARN_COUNT_ID = 188
	// echo $sqlYarn;die();
	$yarn_res = sql_select($sqlYarn);
	$yarnRcvQtyArray = array();
	$piWiseyarnRcvQtyArray = array();
	foreach ($yarn_res as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
        	$yarnRcvQtyArray[$comp][$row['YARN_COUNT_ID']]['rcv_qty'] += $row['RCV_QTY']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['count']=$row['YARN_COUNT_ID']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['comp']=$row['YARN_COMP_TYPE1ST'];
        	$piWiseyarnRcvQtyArray[$row['PI_ID']][$comp][$count_array[$row['YARN_COUNT_ID']]]['rcv_qty'] += $row['RCV_QTY'];
        }

        /*if ($row['YARN_COMP_TYPE2ND'] != 0) 
        {
            $comp .= $composition[$row['YARN_COMP_TYPE2ND']] . "";
        }*/ 
	}
	unset($yarn_res);
	// ========================= to be demand ======================
	$sql = "SELECT b.id as po_id,a.PLAN_DATE, a.COUNT_ID,a.COMPOSITION_ID,(case when a.plan_date <= '$current_date' then a.PLAN_QTY else 0 end) as all_plan_qty,(case when a.plan_date between '$next_date' and '$future_date' then a.PLAN_QTY else 0 end) as PLAN_QTY,(case when a.plan_date = '$current_date' then a.PLAN_QTY else 0 end) as today_plan_qty from TNA_PLAN_TARGET a,wo_po_break_down b,wo_po_details_master c where a.po_id=b.id and b.job_id=c.id and c.company_name=$company_name and a.TASK_ID=48 and a.status_Active=1 and a.is_deleted=0 and b.status_Active=1 and b.is_deleted=0 and b.shiping_status!=3 and a.PLAN_QTY>0";
	// echo $sql;die;
	$res = sql_select($sql);
	$to_be_demand_data = array();
	$to_be_demand_data = array();
	$po_id_arr = array();
	foreach ($res as $v) 
	{
		$comp = strtolower(trim($composition[$v['COMPOSITION_ID']]));

		$to_be_demand_data[$comp][$count_array[$v['COUNT_ID']]]['plan_qty'] += $v['PLAN_QTY'];
		$to_be_demand_data[$comp][$count_array[$v['COUNT_ID']]]['all_plan_qty'] += $v['ALL_PLAN_QTY'];
		$to_be_demand_data[$comp][$count_array[$v['COUNT_ID']]]['today_plan_qty'] += $v['TODAY_PLAN_QTY'];
		
		$dataArray[$comp][$count_array[$v['COUNT_ID']]]['count']=$v['COUNT_ID']; 
		$dataArray[$comp][$count_array[$v['COUNT_ID']]]['comp']=$v['COMPOSITION_ID']; 
		$po_id_arr[$v['PO_ID']] = $v['PO_ID'];
		
	}
	// echo "<pre>";print_r($to_be_demand_data);die();

	
	/*==========================================================================================/
	/										yarn allocation										/
	/==========================================================================================*/
	// $po_id_cond = where_con_using_array($po_id_arr,0,"b.po_break_down_id");
	$po_id_cond = where_con_using_array($po_id_array,0,"b.po_break_down_id");
	$sql_alloc = "SELECT b.po_break_down_id as PO_ID, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST, c.YARN_COMP_TYPE2ND,b.QNTY,a.allocation_date from inv_material_allocation_mst a,inv_material_allocation_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and b.item_category=1 and c.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.company_id=$company_name and c.yarn_comp_type1st !=0 and c.yarn_count_id !=0 and a.allocation_date <= '$current_date' and b.IS_SALES!=2 $po_id_cond";//  and c.YARN_COMP_TYPE1ST = 451 and c.YARN_COUNT_ID = 188  and b.po_break_down_id=19677
	// echo $sql_alloc;die();
	$alloc_res = sql_select($sql_alloc);
    $alloc_qty_array = array();
    $po_wise_alloc_qty_array = array();
	foreach ($alloc_res as $row) 
	{
		$comp = "";
		if ($row['YARN_COMP_TYPE1ST'] != 0 || $row['YARN_COMP_TYPE1ST'] != "") 
		{			
            $comp = strtolower(trim($composition[$row['YARN_COMP_TYPE1ST']]));
			if(strtotime($row['ALLOCATION_DATE']) == strtotime($current_date))
			{
				$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['today_alloc_qty'] += $row['QNTY']; 
			}
			else
			{
				// $dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['alloc_qty'] += $row['QNTY']; 
			}
        	$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['alloc_qty'] += $row['QNTY']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['count'] = $row['YARN_COUNT_ID']; 
			$dataArray[$comp][$count_array[$row['YARN_COUNT_ID']]]['comp'] = $row['YARN_COMP_TYPE1ST']; 

        	$po_wise_alloc_qty_array[$row['PO_ID']][$comp][$count_array[$row['YARN_COUNT_ID']]]['alloc_qty'] += $row['QNTY']; 
        }

        /*if ($row['YARN_COMP_TYPE2ND'] != 0) 
        {
            $comp .= $composition[$row['YARN_COMP_TYPE2ND']] . "";
        }*/
	}
	// echo "<pre>";print_r($dataArray);die();
	unset($alloc_res);

	/*==========================================================================================/
	/							get deman and opening backlog									/
	/==========================================================================================*/
	// echo "<pre>";print_r($yarnReqQtyArr);die();po_wise_alloc_qty_array
	
	/* $opening_backlog_array = array();
	foreach ($req_qty_array as $poId => $poData) 
	{
		if($tnaDateArray[$poId]['start_date'] !="" && $tnaDateArray[$poId]['end_date'] !="")
		{
			$startDate = new DateTime($tnaDateArray[$poId]['start_date']);
			$endDate   = new DateTime($tnaDateArray[$poId]['end_date']);
			foreach ($poData as $comp => $compData) 
			{
				foreach ($compData as $count_id => $row) 
				{						
					 if($row>0)
					 {
						// $budgetQty = $req_qty_array[$comp][$count_id]['req_qty'];
						$budgetQty = $row+$excess_qty_array[$poId][$comp][$count_id]['excess_qty'];
						$allocQty = 0;
						$allocQty = $po_wise_alloc_qty_array[$poId][$comp][$count_id]['alloc_qty'];
						// $allocQty = $row['alloc_qty'];
	
						if(strtotime($today) <= strtotime($startDate->format('d-m-Y')))
						{
							// echo $allocQty;
							$dataArray[$comp][$count_id]['opening_bklg'] += 0 - $allocQty;
							 //echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($endDate->format('d-m-Y')) )
						{
							$dataArray[$comp][$count_id]['opening_bklg'] += $budgetQty - $allocQty;
							// echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($startDate->format('d-m-Y')) && strtotime($today) <= strtotime($endDate->format('d-m-Y'))) 
						{		        	
							$daysDif  = $endDate->diff($startDate)->format('%a')+1;
	
							$todayDaysDif= $startDate->diff($dateObj)->format('%a');
	
							$dataArray[$comp][$count_id]['opening_bklg'] += (($budgetQty/$daysDif)*$todayDaysDif) - $allocQty;
							// echo $comp."==".$count_id."==((".$budgetQty."/".$daysDif.")*".$todayDaysDif.") - ".$allocQty."<br>";
						}
					}
				}
			}
		}
		
	} */
	unset($yarnReqQtyArr);
	// echo "<pre>";print_r($dataArray);die();
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;}
	</style>

    <div style="margin:0 auto;width:1120px;">
    <fieldset style="width:1120px;">
    	<table width="1050">
            <tr class="form_caption">
                <td colspan="13" align="center"><strong>Yarn Demand - Supply Matrix Report</strong></td>
            </tr>
            <tr class="form_caption">
                <td colspan="13" align="center"><strong><? echo $company_arr[$company_name];?></strong>
                <br>
                <strong>
                <? echo change_date_format(str_replace("'","",$txt_date)); ?>
                </strong>
                
                </td>
            </tr>
        </table>
        <table  class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
             	<tr>
	                <th width="30">SL</th>
	                <th width="200">Composition</th>
	                <th width="60">Count</th>
	                <th width="60">Total Stock</th>
	                <th width="60">Free Stock </th>               
	                <th width="60">Returnable Qty</th>               
	                <th width="60">Opening Backlog</th>  
	                <th width="60">Today Plan</th>            
	                <th width="60">Today Allocation</th>
	                <th width="60">To Be Demand</th> 
	                <th width="60">Purchase Backlog</th>
	                <th width="60">Today Receivable</th>
	                <th width="60">Today Rcvd</th>
	                <th width="60">Receivable</th>
	                <th >Closing Backlog</th>
                 </tr>
            </thead>
        </table>
        
        <div style="width:1070px; max-height:330px; overflow-y:auto" id="scroll_body">
			<table class="rpt_table" width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
	            <?
	            $i=1;
	            $gr_tot_stock 			= 0;
	            $gr_free_stock 			= 0;
	            $gr_rtnabl_qty 			= 0;
	            $gr_opn_bklg_stock 		= 0;
	            $gr_demand_stock 		= 0;
	            $gr_pipe_line_stock 	= 0;
	            $gr_close_bklg_stock 	= 0;
	            ksort($dataArray);
				foreach ($dataArray as $comp_key => $comp_data) 
				{
					ksort($comp_data);
					foreach ($comp_data as $count_name => $row) 
					{
						// echo $row['all_plan_rcv']."<br>";
						$rcvQty = $row['rcv_qty'];
						// $issueQty = $row['issue_qty'];
						$issueQty = $issue_qty_array[$comp_key][$count_name]['issue_qty'];
						$rcv_rtn_qty = $issue_qty_array[$comp_key][$count_name]['rcv_rtn_qty'];
						$today_yarn_rcv = $row['today_yarn_rcv'] - $rcv_rtn_qty;
						// $opningBal = $rcvQty - $issueQty;
						// echo $rcvQty ."-". $issueQty."<br>";
						$opningBal = $row['current_stock'];
						$allocQty = $row['alloc_qty'];
						$allcoStock = $allocQty - $issueQty;
						// $freeStock = $opningBal - $allcoStock;
						$freeStock = $row['available_qnty'];//+$row['today_alloc_qty'];
						// echo $freeStock ."=". $rcvQty ."-". $issueQty ."-". $allocQty."==$i<br>";
						$smnQty = $smn_booking_qty_array[$comp_key][$count_name];
						// $openigBklog = $row['opening_bklg']+$smnQty;
						$openigBklog = 0;
						if($to_be_demand_data[$comp_key][$count_name]['all_plan_qty']>0)
						{
							$openigBklog = $to_be_demand_data[$comp_key][$count_name]['all_plan_qty'] - $row['alloc_qty'];//($row['alloc_qty']+$smnQty);
							
						}
						$openigBklog += $smnQty;
						$openigBklogTitle = "Plan Qty=".$to_be_demand_data[$comp_key][$count_name]['all_plan_qty'] ."- Allocation Qty=". $row['alloc_qty']."+ Sample Qty (reqired=".$smn_data_array[$comp_key][$count_name]['req_qty']." - issue )=".$smnQty;
						// echo $comp_key."==".$count_name."==".$to_be_demand_data[$comp_key][$count_name]['all_plan_qty']."-".$row['alloc_qty']."+".$smnQty."<br>";
						$to_be_demand = $to_be_demand_data[$comp_key][$count_name]['plan_qty'];
						$today_plan_qty = $to_be_demand_data[$comp_key][$count_name]['today_plan_qty'];
						$pipe_line = $row['pipe_line_qty'];
						$today_rcvabl = $row['today_rcvabl'];
						$non_issue = $smn_data_array[$comp_key][$count_name]['return_qnty'];
						$non_rcv_rtn = $non_order_rev_rtn_qty_array[$comp_key][$count_name];
						$retun_abl_qty = $non_issue - $non_rcv_rtn;
						$row_status=0;
						if(($opningBal>=1 || $freeStock>=1 || $to_be_demand>=1 || $pipe_line >=1)&& $without_zero==0)
						{
							$row_status=1;
						}
						if($openigBklog>=1 && $without_zero==1)
						{
							$row_status=1;
						}
						if($to_be_demand>=1 && $without_zero==2)
						{
							$row_status=1;
						}
						if( $pipe_line >=1 && $without_zero==3)
						{
							$row_status=1;
						}
						$purchase_bklog_qty = 0;
						if($row['all_plan_qty'])
						{
							// $purchase_bklog_qty = $row['all_plan_qty'] - ($row['all_plan_rcv']-$row['all_close_pi_rcv']);
							$purchase_bklog_qty = $row['all_plan_qty'] - $row['all_plan_rcv'];
						}
						// echo $comp_key."==".$count_name."==".$row['all_plan_qty'] ."-". $row['all_plan_rcv']."-".$row['all_close_pi_rcv']."<br>";

						if($row_status==1)
						{
							if($comp_key !="" && $count_name !="")
							{
								$closing_backlog = ($freeStock + $pipe_line + $retun_abl_qty)-($openigBklog + $to_be_demand);
								// $closing_backlog = ($openigBklog + $to_be_demand)-($freeStock + $pipe_line + $retun_abl_qty);
								$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
								?>				
				                <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
									<td width="30"><? echo $i; ?></td>
									<td width="200" class="wrd_brk" title="<? echo $row['comp'];?>"><? echo ucwords($comp_key);?></td>
									<td width="60"  class="wrd_brk" title="<? echo $row['count'];?>" align="center"><? echo $count_name; ?></td>
									<td width="60"  class="wrd_brk" align="right" title="Rcv=<? echo $rcvQty;?> - Issue=<? echo $issueQty;?>"><? echo number_format($opningBal,0);?></td>
									<td width="60"  class="wrd_brk" align="right">
										<? $under_score = ($freeStock==0) ? "_" : ""; ?>
										<a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('open_free_stock_popup','Free Stock popup',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','930px')">
											<? echo number_format($freeStock,0);?>
										</a>
									</td>
									<td width="60"  class="wrd_brk" align="right">
										<? $under_score = ($retun_abl_qty==0) ? "_" : ""; ?>
										<a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('open_returnable_popup','Returnable Qty popup',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','760px')">
											<? echo number_format($retun_abl_qty,0);?>
										</a>
									</td>
									<td width="60"  class="wrd_brk" align="right" title="<?=$openigBklogTitle;?>">
										<? $under_score = ($openigBklog==0) ? "_" : ""; ?>
										<a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('open_opening_backlog_popup','opening backlog popup',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','930px')">
											<? echo number_format($openigBklog,0);?>
										</a>
									</td>
									<td width="60" align="right"><?=number_format($today_plan_qty,0);?></td>

									<td width="60" align="right">
										<? $under_score = ($row['today_alloc_qty']==0) ? "_" : ""; ?>
										<!-- <a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('today_allocation_popup','Today allocation popup',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','830px')"> -->
											<?=number_format($row['today_alloc_qty'],0);?>
										<!-- </a> -->
									</td>

									<td width="60"  class="wrd_brk" align="right">
										<? $under_score = ($to_be_demand==0) ? "" : ""; ?>
										<a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('open_deman_popup','To be demand popup',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','1030px')">
											<? echo number_format($to_be_demand,0);?>
										</a>
									</td>

									<td width="60" align="right" title="plan=<?=$row['all_plan_qty'].' - rcv='.$row['all_plan_rcv'];?>">
										<? $under_score = ($purchase_bklog_qty==0) ? "_" : ""; ?>
										<!-- <a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('purchase_backlog_popup','Purchase backlog popup',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','830px')"> -->
											<?=number_format($purchase_bklog_qty,0);?>
										<!-- </a> -->
									</td>
									
									<td width="60" align="right"><?=number_format($today_rcvabl,0);?></td>

									<td width="60" align="right">
										<? $under_score = ($today_yarn_rcv==0) ? "_" : ""; ?>
										<!-- <a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('today_yarn_rcv_popup','Today yarn receive popup',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','830px')"> -->
											<?=number_format($today_yarn_rcv,0);?>
										<!-- </a> -->
									</td>

									<td width="60" class="wrd_brk" align="right">
										<? $under_score = ($pipe_line==0) ? "" : ""; ?>
										<a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('open_pipeline_popup','Receivable',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','930px')">
											<? echo number_format($pipe_line,0);?>
										</a>
									</td>
									<td class="wrd_brk" align="right">
										<? $under_score = ($closing_backlog==0) ? "_" : ""; ?>
										<a href="javascript:void(0)" onclick="open_popup<? echo $under_score;?>('open_closing_backlog_popup','Closing backlog popup',<? echo $company_name;?>,<? echo $row['comp'];?>,<? echo $row['count'];?>,'<? echo $future_date;?>','450px','630px')">
											<? echo number_format($closing_backlog,0);?>
										</a>
									</td>	

								</tr>	
								<?
								$i++;
					            $gr_tot_stock 			+= $opningBal;
					            $gr_free_stock 			+= $freeStock;
					            $gr_rtnabl_qty 			+= $retun_abl_qty;
					            $gr_opn_bklg_stock 		+= $openigBklog;
					            $gr_demand_stock 		+= $to_be_demand;
					            $gr_pipe_line_stock 	+= $pipe_line;
					            $gr_close_bklg_stock 	+= $closing_backlog;
					        }
				        }
					}
				}
				unset($dataArray);
					?>	
				</tbody>
            </table>
        </div>
        <table class="rpt_table"  width="1050" id="report_table_footer" cellpadding="0" cellspacing="0" border="1" rules="all">
       
          	<tfoot>
            	<th width="30"></th>
             	<th width="200"></th>
             	<th width="60">Total</th>
             	<th id="gr_stock" width="60" class="wrd_brk"><? echo number_format($gr_tot_stock,0);?></th>
             	<th id="gr_free_stock" width="60" class="wrd_brk"><? echo number_format($gr_free_stock,0);?></th>
             	<th id="gr_returnable_qty" width="60" class="wrd_brk"><? echo number_format($gr_rtnabl_qty,0);?></th>
             	<th id="gr_backlog" width="60" class="wrd_brk"><? echo number_format($gr_opn_bklg_stock,0);?></th>
				<th id="gr_today_plan" width="60"></th>
				<th id="gr_today_allocation" width="60"></th>
             	<th id="gr_demand" width="60" class="wrd_brk"><? echo number_format($gr_demand_stock,0);?></th>
				<th id="gr_purc_bklog" width="60"></th>
				<th id="gr_today_rcvable" width="60"></th>
				<th id="gr_today_rcv" width="60"></th>
             	<th id="gr_pipe_line" width="60" class="wrd_brk"><? echo number_format($gr_pipe_line_stock,0);?></th>
             	<th id="gr_closing_backlog" class="wrd_brk"><? echo number_format($gr_close_bklg_stock,0);?></th>
          	</tfoot>
        </table>
    </fieldset>
    </div>
	<?
   
	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename****$report_type"; 
	exit();
}

if($action=="open_free_stock_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$brand_array = return_library_array("select id,brand_name from  lib_brand", "id", "brand_name");
	$supplier_array = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	
	/*==========================================================================================/
	/										yarn receive 										/
	/==========================================================================================*/
	$sql_receive = "SELECT  a.SUPPLIER_ID,b.LOT,a.transaction_date as RCV_DATE,
	sum(case when a.transaction_type in (1,4,5) and a.transaction_date<= '$current_date' then a.cons_quantity else 0 end) as RCV_TOTAL_OPENING
	from inv_transaction a , product_details_master b where b.id=a.prod_id and a.transaction_type in (1,4,5) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and b.yarn_count_id=$count_id and b.yarn_comp_type1st=$comp_id and b.item_category_id=1 group by a.SUPPLIER_ID,b.lot,a.transaction_date order by a.transaction_date";
	// echo $sql_receive;die();
	$recv_res = sql_select($sql_receive);
	$dataArray = array();
	foreach ($recv_res as $row) 
	{
        $dataArray[$row['SUPPLIER_ID']][$row['LOT']]['qty'] += $row['RCV_TOTAL_OPENING']; 
        $dataArray[$row['SUPPLIER_ID']][$row['LOT']]['lst_qty'] = $row['RCV_TOTAL_OPENING']; 
        $dataArray[$row['SUPPLIER_ID']][$row['LOT']]['date'] = $row['RCV_DATE']; 
	}
	unset($recv_res);
	/*==========================================================================================/
	/										yarn issue  										/
	/==========================================================================================*/
	$sql_issue = "SELECT  a.SUPPLIER_ID,b.LOT,a.transaction_date as ISSUE_DATE,
	sum(case when a.transaction_type in (2,3,6) and a.transaction_date<='$current_date' then a.cons_quantity else 0 end) as ISSUE_TOTAL_OPENING
	from inv_transaction a , product_details_master b where b.id=a.prod_id and a.transaction_type in (2,3,6) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$company_id and b.yarn_count_id=$count_id and b.yarn_comp_type1st=$comp_id and b.item_category_id=1 group by a.SUPPLIER_ID,b.lot,a.transaction_date order by a.transaction_date";
	// echo $sql_issue;die();
	$issue_res = sql_select($sql_issue);
	$issueDataArray = array();
	foreach ($issue_res as $row) 
	{
        $issueDataArray[$row['SUPPLIER_ID']][$row['LOT']]['qty'] += $row['ISSUE_TOTAL_OPENING']; 
        $issueDataArray[$row['SUPPLIER_ID']][$row['LOT']]['lst_qty'] = $row['ISSUE_TOTAL_OPENING']; 
        $issueDataArray[$row['SUPPLIER_ID']][$row['LOT']]['date'] = $row['ISSUE_DATE']; 
	}
	unset($issue_res);
	// echo "<pre>";print_r($issueDataArray);
	/*==========================================================================================/
	/										yarn transaction 											/
	/==========================================================================================*/
	$sql_trns = "SELECT b.id as prod_id,b.SUPPLIER_ID,b.LOT,b.available_qnty AS AVAILABLE_QNTY,b.ALLOCATED_QNTY
    from product_details_master b
    where b.status_active=1 and b.is_deleted=0  and b.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1";
    // echo $sql_trns;die();
    $trns_res = sql_select($sql_trns);
    $trans_qty_array = array();
	foreach ($trns_res as $row) 
	{
		$trans_qty_array[$row['SUPPLIER_ID']][$row['LOT']]['available_qnty'] += $row['AVAILABLE_QNTY'];        
		$trans_qty_array[$row['SUPPLIER_ID']][$row['LOT']]['allocated_qnty'] += $row['ALLOCATED_QNTY'];        
	}
	unset($sql_trns);

	/*==========================================================================================/
	/										yarn allocation										/
	/==========================================================================================*/
	$sql_alloc = "SELECT c.LOT,d.BRAND_ID,b.QNTY from inv_material_allocation_mst a,inv_material_allocation_dtls b, product_details_master c,inv_transaction d where a.id=b.mst_id and b.item_id=c.id and b.item_category=1 and c.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.company_id=$company_id and c.yarn_comp_type1st =$comp_id and c.yarn_count_id =$count_id and c.id=d.prod_id and a.allocation_date <= '$current_date'";
	// echo $sql_alloc;die();
	$alloc_res = sql_select($sql_alloc);
    $yarn_alloc_qty_array = array();
    $po_wise_alloc_qty_array = array();
	foreach ($alloc_res as $row) 
	{
        $yarn_alloc_qty_array[$row['BRAND_ID']][$row['LOT']] += $row['QNTY']; 
	}
	unset($alloc_res);
	//================================ getting rowspan ===========================
	$rowspan = array();
    foreach ($dataArray as $supplier_id => $supplierData) 
    {
        foreach ($supplierData as $lot => $row) 
        {
        	$availableQnty = $trans_qty_array[$supplier_id][$lot]['available_qnty'];
        	// $totalStock = $trans_qty_array[$supplier_id][$lot]['allocated_qnty'];
        	$issueQty = $issueDataArray[$supplier_id][$lot]['qty'];
        	$totalStock = $row['qty'] - $issueQty;
	        $freeStock = $availableQnty;
	        if($totalStock>=1 || $freeStock>=1)
	        {
            	$rowspan[$supplier_id]++;
            }
        }
    }

	?>
	<div id="data_panel" align="center" style="width:900px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 900px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
                <thead>
                    <tr>
                        <th width="100">Supplier</th>
                        <th width="100">Lot</th>
                        <th width="100">Total Rcv qty</th>
                        <th width="100">Total Stock Qty</th>
                        <th width="100">Free Stock Qty</th>
                        <th width="100">Last Rcv Date</th>
                        <th width="100">Last Rcv Qty</th>
                        <th width="100">White</th>
                        <th width="100">Color</th>
                    </tr>
                </thead>
            </table>
            <div style="width:920px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
	                <tbody>
	                    <?
	                    // echo "<pre>";print_r($dataArray);die();
	                    $i=0;
	                    $gr_rcv_qty 	= 0;
	                    $gr_stock_qty 	= 0;
	                    $gr_free_qty 	= 0;
	                    $gr_lst_qty 	= 0;
	                    foreach ($dataArray as $supplier_id => $supplierData) 
	                    {
	                    	$r=0;
		                    $byr_rcv_qty 	= 0;
		                    $byr_stock_qty 	= 0;
		                    $byr_free_qty 	= 0;
		                    $byr_lst_qty 	= 0;
	                        foreach ($supplierData as $lot => $row) 
	                        {  	                        	
	                        	$available_qnty = $trans_qty_array[$supplier_id][$lot]['available_qnty'];
	                        	// $total_Stock = $trans_qty_array[$supplier_id][$lot]['allocated_qnty'];
	                        	$issue_qty = $issueDataArray[$supplier_id][$lot]['qty'];
	                        	// $opningBal = $row['qty'] - $issueQty;
	                        	// $allcoStock = $allocQty - $issueQty;
	                        	$total_Stock = $row['qty'] - $issue_qty;
	                        	// echo $rcvQty ."-". $issueQty."<br>";
	                        	// echo $lot."==".$row['qty']."-".$issue_qty."<br>";
	                        	$freeStock = $available_qnty;
	                        	if($total_Stock>=1 || $freeStock>=1)
	                        	{	                        	
		                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
		                            ?>                        
		                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
		                            	<? if($r==0){?>                            
		                                <td title="<? echo $supplier_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$supplier_id];?>"><? echo $supplier_array[$supplier_id]; ?></td>
		                                <? }?>
		                                <td width="100"><p><? echo $lot; ?></p></td>
		                                <td width="100" align="right"><? echo number_format($row['qty'],0);?></td>
		                                <td width="100" align="right"><? echo number_format($total_Stock,0);?></td>
		                                <td width="100" align="right"><? echo number_format($freeStock,0);?></td>
		                                <td width="100" align="center"><? echo date('d-M',strtotime($row['date']));?></td>
		                                <td width="100" align="right"><? echo number_format($row['lst_qty'],0);?></td>
		                                <td width="100" align="right"></td>
		                                <td width="100" align="right"></td>
		                            </tr>
		                            <?
		                            $i++;
		                            $r++;
				                    $byr_rcv_qty 	+= $row['qty'];
				                    $byr_stock_qty 	+= $total_Stock;
				                    $byr_free_qty 	+= $freeStock;
				                    $byr_lst_qty 	+= $row['lst_qty'];

				                    $gr_rcv_qty 	+= $row['qty'];
				                    $gr_stock_qty 	+= $total_Stock;
				                    $gr_free_qty 	+= $freeStock;
				                    $gr_lst_qty 	+= $row['lst_qty'];
				                }
	                        }
	                        if($byr_rcv_qty>=1 || $byr_stock_qty>=1 || $byr_free_qty>=1 || $byr_lst_qty>=1)
	                        {
		                        ?>
		                        <tr style="font-size:12px;font-weight:bold;background:#ccc;">
		                        	<td colspan="2" align="right">Brand Total</td>
		                        	<td align="right"><? echo number_format($byr_rcv_qty,0);?></td>
		                        	<td align="right"><? echo number_format($byr_stock_qty,0);?></td>
		                        	<td align="right"><? echo number_format($byr_free_qty,0);?></td>
		                        	<td align="right"></td>
		                        	<td align="right"><? echo number_format($byr_lst_qty,0);?></td>
		                        	<td align="right"></td>
		                        	<td align="right"></td>
		                        </tr>
		                        <? 
	                        }                       
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="100"><? echo number_format($gr_rcv_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_stock_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_free_qty,0);?></th>
            			<th width="100"></th>
            			<th width="100"><? echo number_format($gr_lst_qty,0);?></th>
            			<th width="100"></th>
            			<th width="100"></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}

if($action=="open_returnable_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$brand_array = return_library_array("select id,brand_name from  lib_brand", "id", "brand_name");	

	/*==========================================================================================/
	/									get non closing booking  id								/
	/==========================================================================================*/
	$sql = "SELECT DISTINCT a.id from WO_NON_ORD_SAMP_BOOKING_MST a, WO_NON_ORD_SAMP_BOOKING_DTLS b, SAMPLE_DEVELOPMENT_DTLS c where a.id=b.booking_mst_id and b.style_id=c.sample_mst_id and b.ENTRY_FORM_ID=140 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.IS_COMPLETE_PROD is null and a.booking_type=4 and a.IS_APPROVED=1 and a.company_id=$company_id";
	// echo $sql;
	$res = sql_select($sql);
	$closing_req_id_arr = array();
	foreach ($res as $v) 
	{
		$closing_req_id_arr[$v['ID']] = $v['ID'];
	}
	
	/*==========================================================================================/
	/						yarn issue qty from smple without order								/
	/==========================================================================================*/
	if(count($closing_req_id_arr)>0)
	{
		$booking_id_cond = where_con_using_array($closing_req_id_arr,0,"d.id");
	}
	$sqlIssue = "SELECT c.id as ISSUE_ID,c.issue_number_prefix_num as ISSUE_NO, b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST,a.cons_quantity as ISSUE_QTY,a.RETURN_QNTY
    from inv_transaction a, inv_issue_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and d.item_category=2 and d.booking_type=4 and c.issue_basis=1 and c.issue_purpose=8 and a.transaction_type=2 and d.IS_APPROVED=1 $booking_id_cond";
    // echo $sqlIssue;die();
    $issueRes = sql_select($sqlIssue);
    $dataArray = array();
	foreach ($issueRes as $row) 
	{            
        $dataArray[$row['ISSUE_NO']]['issue_qty'] 		+= $row['ISSUE_QTY'];
        $dataArray[$row['ISSUE_NO']]['return_qnty'] 	+= $row['RETURN_QNTY'];
        $dataArray[$row['ISSUE_NO']]['yarn_count_id'] 	= $row['YARN_COUNT_ID'];
        $dataArray[$row['ISSUE_NO']]['yarn_comp_id'] 	= $row['YARN_COMP_TYPE1ST'];
        $dataArray[$row['ISSUE_NO']]['issue_id'] 		= $row['ISSUE_ID'];
	}
	unset($issueRes);

	/*==========================================================================================/
	/					yarn issue rtn qty from smple without order								/
	/==========================================================================================*/
	$sqlRcv = "SELECT c.ISSUE_ID,a.cons_quantity as RETURN_QTY
    from inv_transaction a, inv_receive_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and c.entry_form=9 and d.item_category=2 and d.booking_type=4 and c.receive_basis=1 and c.booking_without_order=1 and a.transaction_type=4 $booking_id_cond";
    // echo $sqlRcv;die();
    $receiveRes = sql_select($sqlRcv);
    $non_order_rev_rtn_qty_array = array();
	foreach ($receiveRes as $row) 
	{            
        $non_order_rev_rtn_qty_array[$row['ISSUE_ID']] += $row['RETURN_QTY'];         
	}
	unset($issueRes);

	?>
	<div id="data_panel" align="center" style="width:750px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 730px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="730">
                <thead>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="100">Yarn Issue</th>
                        <th width="100">Composition</th>
                        <th width="100">Count</th>
                        <th width="100">Issue Qty</th>
                        <th width="100">Returnable Qty</th>
                        <th width="100">Return Qty</th>
                        <th width="100">Balance</th>
                    </tr>
                </thead>
            </table>
            <div style="width:750px;overflow-y:auto;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="730">
	                <tbody>
	                    <?
	                    // echo "<pre>";print_r($dataArray);die();
	                    $i=1;
	                    $gr_issue_qty 	= 0;
	                    $gr_rtnabl_qty 	= 0;
	                    $gr_rtn_qty 	= 0;
	                    $gr_bal_qty 	= 0;
	                    foreach ($dataArray as $issue_no => $row) 
	                    {	  
	                    	$issue_rtn = $non_order_rev_rtn_qty_array[$row['issue_id']];   
	                    	$balance = $row['return_qnty'] - $issue_rtn;                 	
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
                            ?>                        
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">    
                                <td width="30"><? echo $i;?></td>                       
                                <td width="100" align="center"><? echo $issue_no; ?></td>
                                <td width="100"><? echo $composition[$row['yarn_comp_id']]; ?></td>
                                <td width="100" align="center"><? echo $count_array[$row['yarn_count_id']];?></td>
                                <td width="100" align="right"><? echo number_format($row['issue_qty'],0);?></td>
                                <td width="100" align="right"><? echo number_format($row['return_qnty'],0);?></td>
                                <td width="100" align="right">
                                	<a href="javascript:void(0)" onclick="open_return_qty_popup('issue_return_qty_popup','Return Qty popup',<? echo $company_id;?>,<? echo $row['issue_id'];?>,<? echo $count_id;?>,<? echo $comp_id;?>,'400px','660px')">
                                		<? echo number_format($issue_rtn,0);?>
                                	</a>
                                </td>
                                <td width="100" align="right"><? echo number_format($balance,0);?></td>
                            </tr>
                            <?
                            $i++;
		                    $gr_issue_qty 	+= $row['issue_qty'];
		                    $gr_rtnabl_qty 	+= $row['return_qnty'];
		                    $gr_rtn_qty 	+= $issue_rtn;
		                    $gr_bal_qty 	+= $balance;                        
	                                               
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="730">
            	<tfoot>
            		<tr>
            			<th width="30"></th>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Total</th>
            			<th width="100"><? echo number_format($gr_issue_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_rtnabl_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_rtn_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_bal_qty,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>  
	    <script type="text/javascript">
	        function open_return_qty_popup(action,title,company_id,issue_id,count_id,comp_id,height,width)
	        {
	            emailwindow = dhtmlmodal.open('EmailBox', 'iframe', 'yarn_demand_supply_matrix_report_controller.php?company_id=' + company_id + '&issue_id=' + issue_id + '&count_id=' + count_id + '&comp_id=' + comp_id + '&action=' + action, title, 'width='+width+'px,height='+height+'px,center=1,resize=0,scrolling=0', '../../../');
	        }
	    </script>
    </div>
	<?
}

if($action=="issue_return_qty_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");

	$sqlRtn = "SELECT c.recv_number_prefix_num as RETURN_NO,c.RECEIVE_DATE,b.YARN_COUNT_ID, b.YARN_COMP_TYPE1ST,sum(a.cons_quantity) as RETURN_QTY,sum(a.cons_reject_qnty) as REJ_QTY
    from inv_transaction a, inv_receive_master c,product_details_master b
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and c.issue_id=$issue_id and b.item_category_id=1 and a.transaction_date<='$current_date' and b.yarn_count_id=$count_id and b.yarn_comp_type1st=$comp_id and c.entry_form=9 and c.receive_basis=1 and c.booking_without_order=1 and a.transaction_type=4 group by c.recv_number_prefix_num,c.receive_date,b.yarn_count_id, b.yarn_comp_type1st";
    // echo $sqlRtn;die();
    $rtnRes = sql_select($sqlRtn);

	?>
	<div id="data_panel" align="center" style="width:650px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 630px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="630">
                <thead>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="100">Return No</th>
                        <th width="100">Return Date</th>
                        <th width="100">Composition</th>
                        <th width="100">Count</th>
                        <th width="100">Return Qty</th>
                        <th width="100">Reject Qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:650px;overflow-y:auto;max-height:300px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="630">
	                <tbody>
	                    <?
	                    $i=1;
	                    $gr_rtn_qty 	= 0;
	                    $gr_rej_qty 	= 0;
	                    foreach ($rtnRes as $row) 
	                    {
                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
                            ?>                        
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">                         
                                <td width="30"><? echo $i; ?></td>	                               
                                <td width="100" align="center"><? echo $row['RETURN_NO']; ?></td>
                                <td width="100" align="center"><? echo change_date_format($row['RECEIVE_DATE']);?></td>
                                <td width="100" align=""><? echo $composition[$row['YARN_COMP_TYPE1ST']];?></td>
                                <td width="100" align="center"><? echo $count_array[$row['YARN_COUNT_ID']];?></td>
                                <td width="100" align="right"><? echo number_format($row['RETURN_QTY'],0);?></td>
                                <td width="100" align="right"><? echo number_format($row['REJ_QTY'],0);?></td>
                            </tr>
                            <?
                            $i++;

		                    $gr_rtn_qty 	+= $row['RETURN_QTY'];
		                    $gr_rej_qty 	+= $row['REJ_QTY'];	                                       
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="630">
            	<tfoot>
            		<tr>
            			<th width="30"></th>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Total</th>
            			<th width="100"><? echo number_format($gr_rtn_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_rej_qty,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}


if($action=="open_opening_backlog_popup_bk")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$buyer_array = return_library_array("select id,buyer_name from  lib_buyer", "id", "buyer_name");
	$season_array = return_library_array("select id,SEASON_NAME from lib_buyer_season", "id", "season_name");
	//================================= getting costing per ===========================
	$costing_per_sql=sql_select("SELECT JOB_NO,COSTING_PER from wo_pre_cost_mst");
    $costing_per_arr=array();
    foreach($costing_per_sql as $cost_val)
    {
        $costing_per_arr[$cost_val['JOB_NO']] = $cost_val['COSTING_PER'];
    }
    unset($costing_per_sql);
    //=================================== geting item ratio ==========================
    $gmtsitemRatioArray = array();
    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
    {
        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
    }
    unset($gmtsitemRatioSql);
    // ===================================== main query ===================================
	$sql = "SELECT a.JOB_NO,a.buyer_name AS BUYER_NAME ,a.season_buyer_wise as SEASON_BUYER_WISE,b.id AS PO_ID,b.grouping as INT_REF,c.item_number_id AS ITEM_NUMBER_ID,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,e.cons AS CONS,e.requirment AS REQUIRMENT,f.count_id AS COUNT_ID,f.copm_one_id AS COPM_ONE_ID,f.cons_qnty AS CONS_QNTY,f.avg_cons_qnty AS AVG_CONS_QNTY,f.cons_ratio AS CONS_RATIO from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and f.count_id=$count_id and f.copm_one_id=$comp_id and a.company_name=$company_id and b.shiping_status in(1,2) order by b.grouping";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$dataArray = array();
	$poDataArray = array();
	$po_id_array = array();
	foreach ($sql_res as $key => $row) 
	{
		$costingPer = $costing_per_arr[$row['JOB_NO']];
		if($costingPer==1) $pcs_value=1*12;
        else if($costingPer==2) $pcs_value=1*1;
        else if($costingPer==3) $pcs_value=2*12;
        else if($costingPer==4) $pcs_value=3*12;
        else if($costingPer==5) $pcs_value=4*12;
        $gmtsitemRatio = $gmtsitemRatioArray[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
        $consRatio=$row['CONS_RATIO'];
        $requirment=$row['REQUIRMENT'];
        $consQnty=$requirment*$consRatio/100;
        $reqQty = ($row['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value);

        $dataArray[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['req_qty'] += $reqQty;
        $dataArray[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['season_buyer_wise'] = $row['SEASON_BUYER_WISE'];
        $poDataArray[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']][$row['PO_ID']]['req_qty'] += $reqQty;

        $po_id_array[$row['PO_ID']] = $row['PO_ID'];
	}
	unset($sql_res);

	$poIds = implode(",", $po_id_array);
    $po_id_list_arr=array_chunk($po_id_array,999);

    if($poIds !="")
    {
        $tnaPoCond = " and "; $yarnPoCond = " and ";$bookingPoCond = " and ";
        $p=1;
        foreach($po_id_list_arr as $pi_process)
        {
            if($p==1) $tnaPoCond .="  ( a.po_number_id in(".implode(',',$pi_process).")"; 
            else  $tnaPoCond .=" or a.po_number_id in(".implode(',',$pi_process).")";

            if($p==1) $yarnPoCond .="  ( b.po_break_down_id in(".implode(',',$pi_process).")"; 
            else  $yarnPoCond .=" or b.po_break_down_id in(".implode(',',$pi_process).")"; 

            if($p==1) $bookingPoCond .="  ( b.po_break_down_id in(".implode(',',$pi_process).")"; 
            else  $bookingPoCond .=" or b.po_break_down_id in(".implode(',',$pi_process).")";       
            
            $p++;
        }
        $tnaPoCond .=")";$yarnPoCond .=")";$bookingPoCond .=")";
    }

    //================================ yarn allocation ================================
    $sql_alloc = "SELECT b.po_break_down_id as PO_ID,d.BUYER_NAME,d.SEASON_BUYER_WISE, e.grouping as INT_REF, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST, c.YARN_COMP_TYPE2ND,b.QNTY from inv_material_allocation_mst a,inv_material_allocation_dtls b, product_details_master c,wo_po_details_master d,wo_po_break_down e where a.id=b.mst_id and b.item_id=c.id and b.item_category=1 and c.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$comp_id and d.id=e.job_id and e.id=b.po_break_down_id and d.company_name=$company_id and e.shiping_status in(1,2) and a.allocation_date <= '$current_date'";
	// echo $sql_alloc;die();
	$alloc_res = sql_select($sql_alloc);
    $alloc_qty_array = array();
    $po_wise_alloc_qty_array = array();
	foreach ($alloc_res as $row) 
	{
        $alloc_qty_array[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['alloc_qty'] += $row['QNTY']; 
        $po_wise_alloc_qty_array[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']][$row['PO_ID']]['alloc_qty'] += $row['QNTY']; 
	}
	unset($alloc_res);
    //================================ excess qty ================================
    $sqlExcess = "SELECT c.BUYER_NAME, c.SEASON_BUYER_WISE, d.grouping as INT_REF,d.id as PO_ID, b.grey_fab_qnty as QTY,e.CONS_RATIO FROM wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,wo_po_break_down d,WO_PRE_COST_FAB_YARN_COST_DTLS e, WO_PRE_COST_FABRIC_COST_DTLS f where a.booking_no=b.booking_no and c.id=d.job_id and b.po_break_down_id=d.id and a.is_short=1  and b.PRE_COST_FABRIC_COST_DTLS_id=f.id and f.id=e.FABRIC_COST_DTLS_ID  and e.count_id=$count_id  and e.COPM_ONE_ID=$comp_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingPoCondv and d.shiping_status in(1,2) and c.company_name=$company_id and a.booking_type=1";
    // echo $sqlExcess;die();
    $bookingRes = sql_select($sqlExcess);
    $excess_qty_array = array();
    $po_wise_excess_qty_array = array();
    foreach ($bookingRes as $val) 
    {
		$excess_yearn_kg = $val['QTY'] * ($val['CONS_RATIO']/100);
        $excess_qty_array[$val['BUYER_NAME']][$val['SEASON_BUYER_WISE']][$val['INT_REF']]['excess_qty'] += $excess_yearn_kg;
        $po_wise_excess_qty_array[$val['BUYER_NAME']][$val['SEASON_BUYER_WISE']][$val['INT_REF']][$val['PO_ID']]['excess_qty'] += $excess_yearn_kg;
    }
    unset($bookingRes);

    //===================================== smn data ====================================
    $sql_smn = "SELECT a.BOOKING_NO,a.BUYER_ID,b.GREY_FABRIC from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,lib_yarn_count_determina_mst c, lib_yarn_count_determina_dtls d where a.booking_no=b.booking_no and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and b.lib_yarn_count_deter_id > 0 and c.id=d.mst_id and c.id=b.lib_yarn_count_deter_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.copmposition_id=$comp_id and d.count_id=$count_id and a.company_id=$company_id and a.is_approved=1";
	// echo $sql_smn;die();
	$smnRes = sql_select($sql_smn);
	$smn_booking_data_array = array();
	$booking_arr = array();
	foreach ($smnRes as $val) 
	{
		$smn_booking_data_array[$val['BUYER_ID']][$val['BOOKING_NO']] += $val['GREY_FABRIC'];
		$dataArray[$val['BUYER_ID']][$val['BOOKING_NO']][$val['BOOKING_NO']]['req_qty'] += $val['GREY_FABRIC'];
		$booking_arr[$val['BOOKING_NO']] = $val['BOOKING_NO'];
	}
	// echo "<pre>";print_r($dataArray);die;
	//====================================== smn issue qty =======================================
	$sqlIssue = "SELECT d.BOOKING_NO,d.BUYER_ID,a.cons_quantity as QTY
    from inv_transaction a, inv_issue_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and d.item_category=2 and d.booking_type=4 and c.issue_basis=1 and c.issue_purpose=8 and a.transaction_type=2";
    // echo $sqlIssue;die();
    $issueRes = sql_select($sqlIssue);
    $non_order_issue_qty_array = array();
	foreach ($issueRes as $row) 
	{
		if($booking_arr[$row['BOOKING_NO']]=="")
		{
			$dataArray[$row['BUYER_ID']][$row['BOOKING_NO']][$row['BOOKING_NO']]['smn_backlog'] += $row['QTY'];
		}
        $non_order_issue_qty_array[$row['BUYER_ID']][$row['BOOKING_NO']] += $row['QTY']; 
	}
	unset($issueRes);
	// echo "<pre>";print_r($dataArray);die;

	//===================================== calculation ========================
	$smn_backlog_qty = array();
	foreach ($smn_booking_data_array as $buyer => $buyerData) 
	{
		foreach ($buyerData as $bno => $row) 
		{
			$issue = $non_order_issue_qty_array[$buyer][$bno];
			$bal = $row - $issue;
			// echo $row ."-". $issue."<br>";
			$smn_backlog_qty[$buyer][$bno] += $bal;
		}
	}

    //================================ getting tna data ==========================
	$sqltna = "SELECT A.PO_NUMBER_ID,A.TASK_NUMBER,MAX(A.TASK_START_DATE) AS START_DATE,MAX(A.TASK_FINISH_DATE) AS END_DATE,MAX(A.ACTUAL_START_DATE) AS ACTUAL_START_DATE,MAX(A.ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE from tna_process_mst a,wo_po_break_down b where a.po_number_id=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.task_start_date is not null and b.po_quantity>0 and a.task_type=1 and a.task_number=48 and a.task_start_date is not null $tnaPoCond group by a.po_number_id,a.task_number";
	// echo $sqltna;die();
	$tnaRes = sql_select($sqltna);
    $tnaDateArray = array();
    foreach ($tnaRes as $val) 
    {
        $tnaDateArray[$val['PO_NUMBER_ID']]['start_date']          = $val['START_DATE'];
        $tnaDateArray[$val['PO_NUMBER_ID']]['end_date']            = $val['END_DATE'];
    }
    unset($tnaRes);

    // echo "<pre>";print_r($poDataArray);die();
    // ============================ calculate to be deman ========================
    $dateObj = new DateTime('today');
	$today = $dateObj->format('d-m-Y');
	$futerDate = date('d-m-Y',strtotime($future_date));
	$futureDate = new DateTime($futerDate);
	$opening_bklg_array = array();
    foreach ($poDataArray as $buyer => $buyerData) 
    {
		foreach($buyerData as $seasonId => $seasonData)
		{
			foreach ($seasonData as $intRef => $refData) 
			{
				foreach ($refData as $poId => $val) 
				{
					if($tnaDateArray[$poId]['start_date'] !="" && $tnaDateArray[$poId]['end_date'] !="")
					{
						$startDate = new DateTime($tnaDateArray[$poId]['start_date']);
						$endDate   = new DateTime($tnaDateArray[$poId]['end_date']);
						$budgetQty = $val['req_qty']+$po_wise_excess_qty_array[$buyer][$seasonId][$intRef][$poId]['excess_qty'];
						$allocQty = 0;
						$allocQty = $po_wise_alloc_qty_array[$buyer][$seasonId][$intRef][$poId]['alloc_qty'];
	
						if(strtotime($today) <= strtotime($startDate->format('d-m-Y')))
						{
							$dataArray[$buyer][$seasonId][$intRef]['opening_bklg'] += 0 - $allocQty;
							 //echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($endDate->format('d-m-Y')) )
						{
							$dataArray[$buyer][$seasonId][$intRef]['opening_bklg'] += $budgetQty - $allocQty;
							// echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($startDate->format('d-m-Y')) && strtotime($today) <= strtotime($endDate->format('d-m-Y'))) 
						{		        	
							$daysDif  = $endDate->diff($startDate)->format('%a')+1;
	
							$todayDaysDif= $startDate->diff($dateObj)->format('%a');
	
							$dataArray[$buyer][$seasonId][$intRef]['opening_bklg'] += (($budgetQty/$daysDif)*$todayDaysDif) - $allocQty;
							// echo $comp."==".$count_id."==((".$budgetQty."/".$daysDif.")*".$todayDaysDif.") - ".$allocQty."<br>";
						}
					}	    			
				}
			}
		}
    }

	//================================ getting rowspan ===========================
	$rowspan = $rowspanSeason = array();
    foreach ($dataArray as $buyer_id => $buyerData) 
    {
        foreach ($buyerData as $seasonId => $seasonData) 
        {
			foreach ($seasonData as $intRef => $row) 
			{
				if($row['req_qty']>0 || $row['excess_qty']>0 || $row['alloc_qty']>0 || $row['smn_backlog']>0) 
				{
					$rowspan[$buyer_id]++;
					$rowspanSeason[$buyer_id][$seasonId]++;
				}
			}
        }
    }

	?>
	<div id="data_panel" align="center" style="width:800px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 800px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
                <thead>
                    <tr>
                        <th width="100">Buyer</th>
                        <th width="100">Season</th>
                        <th width="100">Ref No</th>
                        <th width="100">Job Req qty</th>
                        <th width="100">Excess Qty</th>
                        <th width="100">Alloc Qty</th>
                        <th width="100">Alloc Bal</th>
                        <th >Alloc Backlog</th>
                    </tr>
                </thead>
            </table>
            <div style="width:820px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
	                <tbody>
	                    <?
	                    $i=0;
	                    $gr_req_qty 	= 0;
	                    $gr_excs_qty 	= 0;
	                    $gr_allo_qty 	= 0;
	                    $gr_allo_bal 	= 0;
	                    $gr_upto_allo_qty = 0;
	                    foreach ($dataArray as $buyer_id => $buyerData) 
	                    {
	                    	$r=0;
		                    $byr_req_qty 	= 0;
		                    $byr_excs_qty 	= 0;
		                    $byr_allo_qty 	= 0;
		                    $byr_allo_bal 	= 0;
		                    $byr_upto_allo_qty = 0;
							// ksort($buyerData);
	                        foreach ($buyerData as $seasonId => $seasonData) 
							{
								$z=0;
								ksort($seasonData);
								foreach ($seasonData as $int_ref => $row) 
								{
									$excess_qty = $excess_qty_array[$buyer_id][$seasonId][$int_ref]['excess_qty'];
									$alloc_qty = $alloc_qty_array[$buyer_id][$seasonId][$int_ref]['alloc_qty'];
									if($row['req_qty']>0 || $excess_qty>0 || $alloc_qty>0 || $row['smn_backlog']>0) 
									{ 
										$smn_backlog = $smn_backlog_qty[$buyer_id][$int_ref];
										$alloc_bal = $row['req_qty'] + $excess_qty - $alloc_qty;
										$opening_bklg = $row['opening_bklg'];
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
										?>                        
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
											<? if($r==0){?>                            
											<td title="<? echo $buyer_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$buyer_id];?>"><? echo $buyer_array[$buyer_id]; ?></td>
											<? }?>
											<? if($z==0){?>                            
											<td width="100" valign="middle" rowspan="<? echo $rowspanSeason[$buyer_id][$seasonId];?>"><? echo $season_array[$seasonId]; ?></td>
											<? }?>
											<td width="100"><? echo $int_ref; ?></td>
											<td width="100" align="right"><? echo number_format($row['req_qty'],0);?></td>
											<td width="100" align="right"><? echo number_format($excess_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_bal,0);?></td>
											<td  align="right"><? echo number_format(($opening_bklg+$smn_backlog-$row['smn_backlog']),0);?></td>
										</tr>
										<?
										$i++;
										$r++;
										$z++;
										$byr_req_qty 	+= $row['req_qty'];
										$byr_excs_qty 	+= $excess_qty;
										$byr_allo_qty 	+= $alloc_qty;
										$byr_allo_bal 	+= $alloc_bal;
										$byr_opening_bklg += $opening_bklg+$smn_backlog-$row['smn_backlog'];
		
										$gr_req_qty 	+= $row['req_qty'];
										$gr_excs_qty 	+= $excess_qty;
										$gr_allo_qty 	+= $alloc_qty;
										$gr_allo_bal 	+= $alloc_bal;
										$gr_opening_bklg += $opening_bklg+$smn_backlog-$row['smn_backlog'];
									}
								}
	                        }
	                        ?>
	                        <tr style="font-size:12px;font-weight:bold;background:#ccc;">
	                        	<td colspan="3" align="right">Buyer Total</td>
	                        	<td align="right"><? echo number_format($byr_req_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_excs_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_allo_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_allo_bal,0);?></td>
	                        	<td align="right"><? echo number_format($byr_opening_bklg,0);?></td>
	                        </tr>
	                        <?                        
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="100"><? echo number_format($gr_req_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_excs_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_bal,0);?></th>
            			<th width="100"><? echo number_format($gr_opening_bklg,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}

if($action=="open_opening_backlog_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$buyer_array = return_library_array("select id,buyer_name from  lib_buyer", "id", "buyer_name");
	$season_array = return_library_array("select id,SEASON_NAME from lib_buyer_season", "id", "season_name");
	//================================= getting costing per ===========================
	$costing_per_sql=sql_select("SELECT JOB_NO,COSTING_PER from wo_pre_cost_mst");
    $costing_per_arr=array();
    foreach($costing_per_sql as $cost_val)
    {
        $costing_per_arr[$cost_val['JOB_NO']] = $cost_val['COSTING_PER'];
    }
    unset($costing_per_sql);
    //=================================== geting item ratio ==========================
    $gmtsitemRatioArray = array();
    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
    {
        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
    }
    unset($gmtsitemRatioSql);
    // ===================================== main query ===================================
	$sql = "SELECT a.JOB_NO,a.buyer_name AS BUYER_NAME ,a.season_buyer_wise as SEASON_BUYER_WISE,b.id AS PO_ID,b.grouping as INT_REF,c.item_number_id AS ITEM_NUMBER_ID,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,e.cons AS CONS,e.requirment AS REQUIRMENT,f.count_id AS COUNT_ID,f.copm_one_id AS COPM_ONE_ID,f.cons_qnty AS CONS_QNTY,f.avg_cons_qnty AS AVG_CONS_QNTY,f.cons_ratio AS CONS_RATIO from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and f.count_id=$count_id and f.copm_one_id=$comp_id and a.company_name=$company_id and b.shiping_status in(1,2) order by b.grouping";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$dataArray = array();
	$poDataArray = array();
	$po_id_array = array();
	foreach ($sql_res as $key => $row) 
	{
		$costingPer = $costing_per_arr[$row['JOB_NO']];
		if($costingPer==1) $pcs_value=1*12;
        else if($costingPer==2) $pcs_value=1*1;
        else if($costingPer==3) $pcs_value=2*12;
        else if($costingPer==4) $pcs_value=3*12;
        else if($costingPer==5) $pcs_value=4*12;
        $gmtsitemRatio = $gmtsitemRatioArray[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
        $consRatio=$row['CONS_RATIO'];
        $requirment=$row['REQUIRMENT'];
        $consQnty=$requirment*$consRatio/100;
        $reqQty = ($row['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value);

        $dataArray[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['req_qty'] += $reqQty;
        $dataArray[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['season_buyer_wise'] = $row['SEASON_BUYER_WISE'];
        $poDataArray[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']][$row['PO_ID']]['req_qty'] += $reqQty;

        $po_id_array[$row['PO_ID']] = $row['PO_ID'];
	}
	unset($sql_res);

	$poIds = implode(",", $po_id_array);
    $po_id_list_arr=array_chunk($po_id_array,999);

    if($poIds !="")
    {
        $tnaPoCond = " and "; $yarnPoCond = " and ";$bookingPoCond = " and ";
        $p=1;
        foreach($po_id_list_arr as $pi_process)
        {
            if($p==1) $tnaPoCond .="  ( a.po_number_id in(".implode(',',$pi_process).")"; 
            else  $tnaPoCond .=" or a.po_number_id in(".implode(',',$pi_process).")";

            if($p==1) $yarnPoCond .="  ( b.po_break_down_id in(".implode(',',$pi_process).")"; 
            else  $yarnPoCond .=" or b.po_break_down_id in(".implode(',',$pi_process).")"; 

            if($p==1) $bookingPoCond .="  ( b.po_break_down_id in(".implode(',',$pi_process).")"; 
            else  $bookingPoCond .=" or b.po_break_down_id in(".implode(',',$pi_process).")";       
            
            $p++;
        }
        $tnaPoCond .=")";$yarnPoCond .=")";$bookingPoCond .=")";
    }

    //================================ getting tna data ==========================
	$po_id_cond = where_con_using_array($po_id_array,0,"b.id");
	$sqltna = "SELECT A.PO_ID,a.PLAN_QTY from tna_plan_target a,wo_po_break_down b where a.PO_ID=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.PLAN_QTY>0 and a.task_type=1 and a.TASK_ID=48 and a.PLAN_DATE is not null and a.COUNT_ID=$count_id  and a.COMPOSITION_ID=$comp_id and a.plan_date <= '$current_date'";//  $po_id_cond
	// echo $sqltna;die();
	$tnaRes = sql_select($sqltna);
    $tnaDateArray = array();
	$plan_po_id_arr = array();
    foreach ($tnaRes as $val) 
    {
		$tnaDateArray[$val['PO_ID']] += $val['PLAN_QTY'];
		$plan_po_id_arr[$val['PO_ID']] = $val['PO_ID'];
    }
    unset($tnaRes);

    // echo "<pre>";print_r($poDataArray);die();

    //================================ yarn allocation ================================
	$plan_po_id_cond = where_con_using_array($plan_po_id_arr,0,"b.po_break_down_id");
    $sql_alloc = "SELECT b.po_break_down_id as PO_ID,d.BUYER_NAME,d.SEASON_BUYER_WISE, e.grouping as INT_REF, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST, c.YARN_COMP_TYPE2ND,b.QNTY from inv_material_allocation_mst a,inv_material_allocation_dtls b, product_details_master c,wo_po_details_master d,wo_po_break_down e where a.id=b.mst_id and b.item_id=c.id and b.item_category=1 and c.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$comp_id and d.id=e.job_id and e.id=b.po_break_down_id and d.company_name=$company_id and e.shiping_status in(1,2) and a.allocation_date <= '$current_date' and b.IS_SALES!=2 ";// $plan_po_id_cond
	// echo $sql_alloc;die();
	$alloc_res = sql_select($sql_alloc);
    $alloc_qty_array = array();
    $po_wise_alloc_qty_array = array();
	foreach ($alloc_res as $row) 
	{
        $alloc_qty_array[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['alloc_qty'] += $row['QNTY']; 
        $po_wise_alloc_qty_array[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']][$row['PO_ID']]['alloc_qty'] += $row['QNTY']; 
	}
	unset($alloc_res);
    //================================ excess qty ================================
    $sqlExcess = "SELECT c.BUYER_NAME, c.SEASON_BUYER_WISE, d.grouping as INT_REF,d.id as PO_ID, b.grey_fab_qnty as QTY,e.CONS_RATIO FROM wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,wo_po_break_down d,WO_PRE_COST_FAB_YARN_COST_DTLS e, WO_PRE_COST_FABRIC_COST_DTLS f where a.booking_no=b.booking_no and c.id=d.job_id and b.po_break_down_id=d.id and a.is_short=1  and b.PRE_COST_FABRIC_COST_DTLS_id=f.id and f.id=e.FABRIC_COST_DTLS_ID  and e.count_id=$count_id  and e.COPM_ONE_ID=$comp_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bookingPoCondv and d.shiping_status in(1,2) and c.company_name=$company_id and a.booking_type=1";
    // echo $sqlExcess;die();
    $bookingRes = sql_select($sqlExcess);
    $excess_qty_array = array();
    $po_wise_excess_qty_array = array();
    foreach ($bookingRes as $val) 
    {
		$excess_yearn_kg = $val['QTY'] * ($val['CONS_RATIO']/100);
        $excess_qty_array[$val['BUYER_NAME']][$val['SEASON_BUYER_WISE']][$val['INT_REF']]['excess_qty'] += $excess_yearn_kg;
        $po_wise_excess_qty_array[$val['BUYER_NAME']][$val['SEASON_BUYER_WISE']][$val['INT_REF']][$val['PO_ID']]['excess_qty'] += $excess_yearn_kg;
    }
    unset($bookingRes);
	

	/*==========================================================================================/
	/									get non closing booking  id								/
	/==========================================================================================*/
	$sql = "SELECT DISTINCT a.id from WO_NON_ORD_SAMP_BOOKING_MST a, WO_NON_ORD_SAMP_BOOKING_DTLS b, SAMPLE_DEVELOPMENT_DTLS c where a.id=b.booking_mst_id and b.style_id=c.sample_mst_id and b.ENTRY_FORM_ID=140 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.IS_COMPLETE_PROD is null and a.booking_type=4 and a.IS_APPROVED=1 and a.company_id=$company_id";
	$res = sql_select($sql);
	$closing_req_id_arr = array();
	foreach ($res as $v) 
	{
		$closing_req_id_arr[$v['ID']] = $v['ID'];
	}
	if(count($closing_req_id_arr)>0)
	{
		$booking_id_cond = where_con_using_array($closing_req_id_arr,0,"a.id");
	}
    //===================================== smn data ====================================
    // $sql_smn = "SELECT a.BOOKING_NO,a.BUYER_ID,b.GREY_FABRIC from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,lib_yarn_count_determina_mst c, lib_yarn_count_determina_dtls d where a.booking_no=b.booking_no and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and b.lib_yarn_count_deter_id > 0 and c.id=d.mst_id and c.id=b.lib_yarn_count_deter_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.copmposition_id=$comp_id and d.count_id=$count_id and a.company_id=$company_id and a.is_approved=1";

    $sql_smn = "SELECT a.BOOKING_NO,a.BUYER_ID,b.CONS_QNTY from wo_non_ord_samp_booking_mst a,SAMPLE_DEVELOPMENT_YARN_DTLS b where a.id=b.booking_mst_id and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and b.COUNT_ID > 0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.COPM_ONE_ID=$comp_id and b.count_id=$count_id and a.company_id=$company_id and a.is_approved=1 $booking_id_cond";
	// echo $sql_smn;die();
	$smnRes = sql_select($sql_smn);
	$smn_booking_data_array = array();
	foreach ($smnRes as $val) 
	{
		$smn_booking_data_array[$val['BUYER_ID']][$val['BOOKING_NO']] += $val['CONS_QNTY'];
		$dataArray[$val['BUYER_ID']][$val['BOOKING_NO']][$val['BOOKING_NO']]['smn_req_qty'] += $val['CONS_QNTY'];
	}
	// echo "<pre>";print_r($dataArray);die;
	//====================================== smn issue qty =======================================
	if(count($closing_req_id_arr)>0)
	{
		$booking_id_cond = where_con_using_array($closing_req_id_arr,0,"d.id");
	}
	$sqlIssue = "SELECT d.BOOKING_NO,d.BUYER_ID,a.cons_quantity as QTY
    from inv_transaction a, inv_issue_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and d.item_category=2 and d.booking_type=4 and c.issue_basis=1 and c.issue_purpose=8 and a.transaction_type=2 and d.IS_APPROVED=1 $booking_id_cond";
    // echo $sqlIssue;die();
    $issueRes = sql_select($sqlIssue);
    $non_order_issue_qty_array = array();
	foreach ($issueRes as $row) 
	{
        $non_order_issue_qty_array[$row['BUYER_ID']][$row['BOOKING_NO']] += $row['QTY']; 
        $dataArray[$row['BUYER_ID']][$row['BOOKING_NO']][$row['BOOKING_NO']]['issue'] += $row['QTY'];
	}
	// echo "<pre>";print_r($dataArray);die();
	unset($issueRes);
	//===================================== calculation ========================
	$smn_backlog_qty = array();
	foreach ($smn_booking_data_array as $buyer => $buyerData) 
	{
		foreach ($buyerData as $bno => $row) 
		{
			$issue = $non_order_issue_qty_array[$buyer][$bno];
			$bal = $row - $issue;
			// echo $row ."-". $issue."<br>";
			$smn_backlog_qty[$buyer][$bno] += $bal;
		}
	}
    // ============================ calculate to be deman ========================
    $dateObj = new DateTime('today');
	$today = $dateObj->format('d-m-Y');
	$futerDate = date('d-m-Y',strtotime($future_date));
	$futureDate = new DateTime($futerDate);
	$opening_bklg_array = array();
    foreach ($poDataArray as $buyer => $buyerData) 
    {
		foreach($buyerData as $seasonId => $seasonData)
		{
			foreach ($seasonData as $intRef => $refData) 
			{
				foreach ($refData as $poId => $val) 
				{
					$allocQty = 0;
					$allocQty = $po_wise_alloc_qty_array[$buyer][$seasonId][$intRef][$poId]['alloc_qty'];
					$dataArray[$buyer][$seasonId][$intRef]['opening_bklg'] += $tnaDateArray[$poId] - $allocQty;
					$dataArray[$buyer][$seasonId][$intRef]['plan_qty'] += $tnaDateArray[$poId];
					/* if($tnaDateArray[$poId]['start_date'] !="" && $tnaDateArray[$poId]['end_date'] !="")
					{
						$startDate = new DateTime($tnaDateArray[$poId]['start_date']);
						$endDate   = new DateTime($tnaDateArray[$poId]['end_date']);
						$budgetQty = $val['req_qty']+$po_wise_excess_qty_array[$buyer][$seasonId][$intRef][$poId]['excess_qty'];
						$allocQty = 0;
						$allocQty = $po_wise_alloc_qty_array[$buyer][$seasonId][$intRef][$poId]['alloc_qty'];
	
						if(strtotime($today) <= strtotime($startDate->format('d-m-Y')))
						{
							$dataArray[$buyer][$seasonId][$intRef]['opening_bklg'] += 0 - $allocQty;
							 //echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($endDate->format('d-m-Y')) )
						{
							$dataArray[$buyer][$seasonId][$intRef]['opening_bklg'] += $budgetQty - $allocQty;
							// echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($startDate->format('d-m-Y')) && strtotime($today) <= strtotime($endDate->format('d-m-Y'))) 
						{		        	
							$daysDif  = $endDate->diff($startDate)->format('%a')+1;
	
							$todayDaysDif= $startDate->diff($dateObj)->format('%a');
	
							$dataArray[$buyer][$seasonId][$intRef]['opening_bklg'] += (($budgetQty/$daysDif)*$todayDaysDif) - $allocQty;
							// echo $comp."==".$count_id."==((".$budgetQty."/".$daysDif.")*".$todayDaysDif.") - ".$allocQty."<br>";
						}
					} */	    			
				}
			}
		}
    }
	// echo "<pre>";print_r($dataArray);die; 
	//================================ getting rowspan ===========================
	$rowspan = $rowspanSeason = array();
    foreach ($dataArray as $buyer_id => $buyerData) 
    {
        foreach ($buyerData as $seasonId => $seasonData) 
        {
			foreach ($seasonData as $intRef => $row) 
			{			
				$excess_qty = $excess_qty_array[$buyer_id][$seasonId][$intRef]['excess_qty'];
				$alloc_qty = $alloc_qty_array[$buyer_id][$seasonId][$intRef]['alloc_qty'];

				if($row['issue']>0 || $row['smn_req_qty']>0  || $row['plan_qty']>0  || $alloc_qty>0 || $excess_qty>0) 
				{
					$rowspan[$buyer_id]++;
					$rowspanSeason[$buyer_id][$seasonId]++;
				}
			}
        }
    }

	?>
	<div id="data_panel" align="center" style="width:900px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 900px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
                <thead>
                    <tr>
                        <th width="100">Buyer</th>
                        <th width="100">Season</th>
                        <th width="100">Ref No</th>
                        <th width="100">Job Req qty</th>
                        <th width="100">Excess Qty</th>
                        <th width="100">PLan Qty</th>
                        <th width="100">Alloc Qty</th>
                        <th width="100">Alloc Bal</th>
                        <th width="100">Alloc Backlog</th>
                    </tr>
                </thead>
            </table>
            <div style="width:920px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
	                <tbody>
	                    <?
	                    $i=0;
	                    $gr_req_qty 	= 0;
	                    $gr_opening_bklg 	= 0;
	                    $gr_excs_qty 	= 0;
	                    $gr_plan_qty 	= 0;
	                    $gr_allo_qty 	= 0;
	                    $gr_allo_bal 	= 0;
	                    $gr_upto_allo_qty = 0;
	                    foreach ($dataArray as $buyer_id => $buyerData) 
	                    {
	                    	$r=0;
		                    $byr_req_qty 	= 0;
		                    $byr_opening_bklg 	= 0;
		                    $byr_smpl_req_qty 	= 0;
		                    $byr_excs_qty 	= 0;
		                    $byr_plan_qty 	= 0;
		                    $byr_allo_qty 	= 0;
		                    $byr_allo_bal 	= 0;
		                    $byr_upto_allo_qty = 0;
							// ksort($buyerData);
	                        foreach ($buyerData as $seasonId => $seasonData) 
							{
								$z=0;
								ksort($seasonData);
								foreach ($seasonData as $int_ref => $row) 
								{
									$excess_qty = $excess_qty_array[$buyer_id][$seasonId][$int_ref]['excess_qty'];
									$alloc_qty = $alloc_qty_array[$buyer_id][$seasonId][$int_ref]['alloc_qty'];
									if($row['issue']>0 || $row['smn_req_qty']>0 || $row['plan_qty']>0 || $alloc_qty>0 || $excess_qty>0) 
									{ 
										// $smn_backlog = $smn_backlog_qty[$buyer_id][$int_ref];
										$smn_backlog = 0;
										$smn_backlog = $row['smn_req_qty'] - $row['issue'];
										// echo $int_ref.'=='.$smn_backlog."==".$row['req_qty'] ."-". $row['issue']."<br>";
										$alloc_bal = $row['req_qty']+$row['smn_req_qty'] + $excess_qty - $alloc_qty;
										$opening_bklg = $row['opening_bklg'];
										$plan_qty = $row['plan_qty'];
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
										?>                        
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
											<? if($r==0){?>                            
											<td title="<? echo $buyer_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$buyer_id];?>"><? echo $buyer_array[$buyer_id]; ?></td>
											<? }?>
											<? if($z==0){?>                            
											<td width="100" valign="middle" rowspan="<? echo $rowspanSeason[$buyer_id][$seasonId];?>"><? echo $season_array[$seasonId]; ?></td>
											<? }?>
											<td width="100"><? echo $int_ref; ?></td>
											<td width="100" align="right"><? echo number_format(($row['req_qty']+$row['smn_req_qty']),0);?></td>
											<td width="100" align="right"><? echo number_format($excess_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($plan_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_bal,0);?></td>
											<td width="100" align="right"><? echo number_format(($opening_bklg+$smn_backlog),0);?></td>
										</tr>
										<?
										$i++;
										$r++;
										$z++;
										$byr_req_qty 	+= $row['req_qty']+$row['smn_req_qty'];
										$byr_smpl_req_qty 	+= $row['smn_req_qty'];
										$byr_excs_qty 	+= $excess_qty;
										$byr_plan_qty 	+= $plan_qty;
										$byr_allo_qty 	+= $alloc_qty;
										$byr_allo_bal 	+= $alloc_bal;
										$byr_opening_bklg += $opening_bklg+$smn_backlog;
		
										$gr_req_qty 	+= $row['req_qty']+$row['smn_req_qty'];
										$gr_excs_qty 	+= $excess_qty;
										$gr_plan_qty 	+= $plan_qty;
										$gr_allo_qty 	+= $alloc_qty;
										$gr_allo_bal 	+= $alloc_bal;
										$gr_opening_bklg += $opening_bklg+$smn_backlog;
									}
								}
	                        }
							if($byr_smpl_req_qty>0 || $byr_plan_qty>0)
							{
								?>
								<tr style="font-size:12px;font-weight:bold;background:#ccc;">
									<td colspan="3" align="right">Buyer Total</td>
									<td align="right"><? echo number_format($byr_req_qty,0);?></td>
									<td align="right"><? echo number_format($byr_excs_qty,0);?></td>
									<td align="right"><? echo number_format($byr_plan_qty,0);?></td>
									<td align="right"><? echo number_format($byr_allo_qty,0);?></td>
									<td align="right"><? echo number_format($byr_allo_bal,0);?></td>
									<td align="right"><? echo number_format($byr_opening_bklg,0);?></td>
								</tr>
								<?
							}                      
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="100"><? echo number_format($gr_req_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_excs_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_plan_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_bal,0);?></th>
            			<th width="100"><? echo number_format($gr_opening_bklg,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}

if($action=="open_deman_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");
	$next_date = date('d-M-Y', strtotime("+1 day", strtotime($current_date)));
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$buyer_array = return_library_array("select id,buyer_name from  lib_buyer", "id", "buyer_name");
	$season_array = return_library_array("select id,SEASON_NAME from lib_buyer_season", "id", "season_name");
	//================================= getting costing per ===========================
	$costing_per_sql=sql_select("SELECT JOB_NO,COSTING_PER from wo_pre_cost_mst");
    $costing_per_arr=array();
    foreach($costing_per_sql as $cost_val)
    {
        $costing_per_arr[$cost_val['JOB_NO']] = $cost_val['COSTING_PER'];
    }
    unset($costing_per_sql);
    //=================================== geting item ratio ==========================
    $gmtsitemRatioArray = array();
    $gmtsitemRatioSql=sql_select('SELECT a.job_no AS JOB_NO,b.gmts_item_id AS GMTS_ITEM_ID ,b.set_item_ratio AS SET_ITEM_RATIO from wo_po_details_master a, wo_po_details_mas_set_details b where 1=1 and a.id=b.job_id');
    foreach($gmtsitemRatioSql as $gmtsitemRatioSqlRow)
    {
        $gmtsitemRatioArray[$gmtsitemRatioSqlRow['JOB_NO']][$gmtsitemRatioSqlRow['GMTS_ITEM_ID']]=$gmtsitemRatioSqlRow['SET_ITEM_RATIO'];    
    }
    unset($gmtsitemRatioSql);
    // ===================================== main query ===================================
	$sql = "SELECT a.JOB_NO,a.buyer_name AS BUYER_NAME ,b.id AS PO_ID,a.SEASON_BUYER_WISE,b.grouping as INT_REF,c.item_number_id AS ITEM_NUMBER_ID,c.order_quantity AS ORDER_QUANTITY,c.plan_cut_qnty AS PLAN_CUT_QNTY,e.cons AS CONS,e.requirment AS REQUIRMENT,f.count_id AS COUNT_ID,f.copm_one_id AS COPM_ONE_ID,f.cons_qnty AS CONS_QNTY,f.avg_cons_qnty AS AVG_CONS_QNTY,f.cons_ratio AS CONS_RATIO from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_yarn_cost_dtls f where 1=1 and a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and  c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_cost_dtls_id and e.cons !=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 and f.count_id=$count_id and f.copm_one_id=$comp_id and a.company_name=$company_id and b.shiping_status in(1,2) order by b.grouping";
	// echo $sql;die();
	$sql_res = sql_select($sql);
	$dataArray = array();
	$poDataArray = array();
	$po_id_array = array();
	foreach ($sql_res as $key => $row) 
	{
		$costingPer = $costing_per_arr[$row['JOB_NO']];
		if($costingPer==1) $pcs_value=1*12;
        else if($costingPer==2) $pcs_value=1*1;
        else if($costingPer==3) $pcs_value=2*12;
        else if($costingPer==4) $pcs_value=3*12;
        else if($costingPer==5) $pcs_value=4*12;
        $gmtsitemRatio = $gmtsitemRatioArray[$row['JOB_NO']][$row['ITEM_NUMBER_ID']];
        $consRatio=$row['CONS_RATIO'];
        $requirment=$row['REQUIRMENT'];
        $consQnty=$requirment*$consRatio/100;
        $reqQty = ($row['PLAN_CUT_QNTY']/$gmtsitemRatio)*($consQnty/$pcs_value);

        $dataArray[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['req_qty'] += $reqQty;
        $poDataArray[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']][$row['PO_ID']]['req_qty'] += $reqQty;

        $po_id_array[$row['PO_ID']] = $row['PO_ID'];
	}
	unset($sql_res);

	$poIds = implode(",", $po_id_array);
    $po_id_list_arr=array_chunk($po_id_array,999);

    if($poIds !="")
    {
        $tnaPoCond = " and "; $yarnPoCond = " and ";$bookingPoCond = " and ";
        $p=1;
        foreach($po_id_list_arr as $pi_process)
        {
            if($p==1) $tnaPoCond .="  ( a.po_number_id in(".implode(',',$pi_process).")"; 
            else  $tnaPoCond .=" or a.po_number_id in(".implode(',',$pi_process).")";

            if($p==1) $yarnPoCond .="  ( b.po_break_down_id in(".implode(',',$pi_process).")"; 
            else  $yarnPoCond .=" or b.po_break_down_id in(".implode(',',$pi_process).")"; 

            if($p==1) $bookingPoCond .="  ( b.po_break_down_id in(".implode(',',$pi_process).")"; 
            else  $bookingPoCond .=" or b.po_break_down_id in(".implode(',',$pi_process).")";       
            
            $p++;
        }
        $tnaPoCond .=")";$yarnPoCond .=")";$bookingPoCond .=")";
    }

    //================================ yarn allocation ================================
    $sql_alloc = "SELECT d.BUYER_NAME,d.SEASON_BUYER_WISE,e.grouping as INT_REF, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST, c.YARN_COMP_TYPE2ND,(case when a.allocation_date <= '$current_date' then b.QNTY else 0 end) as QNTY,
	(case when a.allocation_date = '$current_date' then b.QNTY else 0 end) as TODAY_ALLOC_QTY from inv_material_allocation_mst a,inv_material_allocation_dtls b, product_details_master c,wo_po_details_master d,wo_po_break_down e where a.id=b.mst_id and b.item_id=c.id and b.item_category=1 and c.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$comp_id and d.id=e.job_id and e.id=b.po_break_down_id and d.company_name=$company_id and e.shiping_status in(1,2) and a.allocation_date <= '$current_date' and b.IS_SALES!=2";
	// echo $sql_alloc;die();
	$alloc_res = sql_select($sql_alloc);
    // $alloc_qty_array = array();
    $alloc_qty_array = array();
	foreach ($alloc_res as $row) 
	{
        $alloc_qty_array[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['alloc_qty'] += $row['QNTY']; 
        $alloc_qty_array[$row['BUYER_NAME']][$row['SEASON_BUYER_WISE']][$row['INT_REF']]['today_alloc_qty'] += $row['TODAY_ALLOC_QTY']; 
	}
	unset($alloc_res);
    //================================ excess qty ================================
    $sqlExcess = "SELECT c.BUYER_NAME,c.SEASON_BUYER_WISE,d.grouping as INT_REF, b.grey_fab_qnty as QTY,e.CONS_RATIO FROM wo_booking_mst a,wo_booking_dtls b,wo_po_details_master c,wo_po_break_down d,WO_PRE_COST_FAB_YARN_COST_DTLS e, WO_PRE_COST_FABRIC_COST_DTLS f where a.booking_no=b.booking_no and c.id=d.job_id and b.po_break_down_id=d.id and a.is_short=1  and b.PRE_COST_FABRIC_COST_DTLS_id=f.id and f.id=e.FABRIC_COST_DTLS_ID  and e.count_id=$count_id  and e.COPM_ONE_ID=$comp_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 $bookingPoCondv and d.shiping_status in(1,2) and c.company_name=$company_id and a.booking_type=1";
    // echo $sqlExcess;die();
    $bookingRes = sql_select($sqlExcess);
    $excess_qty_array = array();
    foreach ($bookingRes as $val) 
    {
		$excess_yearn_kg = $val['QTY'] * ($val['CONS_RATIO']/100);
        $excess_qty_array[$val['BUYER_NAME']][$val['SEASON_BUYER_WISE']][$val['INT_REF']]['excess_qty'] += $excess_yearn_kg;
    }
    unset($bookingRes);
    // echo "<pre>";print_r($excess_qty_array);die();
    //================================ getting tna data ==========================
	$po_id_cond = where_con_using_array($po_id_array,0,"b.id");
	$sqltna = "SELECT A.PO_ID,A.TASK_ID,a.PLAN_DATE,a.PLAN_QTY from tna_plan_target a,wo_po_break_down b where a.PO_ID=b.id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and a.PLAN_QTY>0 and a.task_type=1 and a.TASK_ID=48 and a.PLAN_DATE is not null and a.COUNT_ID=$count_id  and a.COMPOSITION_ID=$comp_id and a.plan_date between '$next_date' and '$future_date'  $po_id_cond";
	// $sqltna = "SELECT a.PLAN_DATE, a.COUNT_ID,a.COMPOSITION_ID,(case when a.plan_date < '$current_date' then a.PLAN_QTY else 0 end) as all_plan_qty,(case when a.plan_date between '$current_date' and '$future_date' then a.PLAN_QTY else 0 end) as PLAN_QTY from TNA_PLAN_TARGET a,wo_po_break_down b,wo_po_details_master c where a.po_id=b.id and b.job_id=c.id and c.company_name=$company_id and a.TASK_ID=48 and a.status_Active=1 and a.is_deleted=0 and b.status_Active=1 and b.is_deleted=0 and b.shiping_status!=3 and a.PLAN_QTY>0 $po_id_cond";
	// echo $sqltna;die();
	$tnaRes = sql_select($sqltna);
    $tnaDateArray = array();
	// $chk
    foreach ($tnaRes as $val) 
    {
        $tnaDateArray[$val['PO_ID']] += $val['PLAN_QTY'];
    }
    unset($tnaRes);
	// echo "<pre>";print_r($tnaDateArray);die;
    // ============================ calculate to be deman ========================
    $dateObj = new DateTime('today');
	$today = $dateObj->format('d-m-Y');
	$futerDate = date('d-m-Y',strtotime($future_date));
	$futureDate = new DateTime($futerDate);

    foreach ($poDataArray as $buyer => $buyerData) 
    {
    	foreach ($buyerData as $seasonId => $seasonData) 
    	{
			foreach ($seasonData as $intRef => $refData) 
			{
				foreach ($refData as $poId => $val) 
				{
					$dataArray[$buyer][$seasonId][$intRef]['to_be_demand'] += $tnaDateArray[$poId];
					$dataArray[$buyer][$seasonId][$intRef]['poid'] = $poId;
					    			
				}
			}
		}
    }
	// echo "<pre>";print_r($dataArray);die;
	//================================ getting rowspan ===========================
	$rowspan = array();
    foreach ($dataArray as $buyer_id => $buyerData) 
    {
        foreach ($buyerData as $seasonId => $seasonData) 
        {
			foreach ($seasonData as $intRef => $row) 
			{
				if($row['to_be_demand']>0)
				{
					$rowspan[$buyer_id]++;
					$rowspanSeason[$buyer_id][$seasonId]++;
				}
			}
		}
    }

	?>
	<div id="data_panel" align="center" style="width:900px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 1000px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="1000">
                <thead>
                    <tr>
                        <th width="100">Buyer</th>
                        <th width="100">Season</th>
                        <th width="100">Ref No</th>
                        <th width="100">Job Req qty</th>
                        <th width="100">Excess Qty</th>
                        <th width="100">Alloc Qty</th>
                        <th width="100">Today Alloc Qty</th>
                        <th width="100">Total Alloc Qty</th>
                        <th width="100">Alloc Bal</th>
                        <th width="100"><p>Up to <? echo change_date_format($future_date);?> Demand Qty</p></th>
                    </tr>
                </thead>
            </table>
            <div style="width:1020px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="1000">
	                <tbody>
	                    <?
	                    $i=0;
	                    $gr_req_qty 	= 0;
	                    $gr_excs_qty 	= 0;
	                    $gr_allo_qty 	= 0;
	                    $gr_today_allo_qty 	= 0;
	                    $gr_total_allo_qty 	= 0;
	                    $gr_allo_bal 	= 0;
	                    $gr_upto_allo_qty = 0;
	                    foreach ($dataArray as $buyer_id => $buyerData) 
	                    {
	                    	$r=0;
		                    $byr_req_qty 	= 0;
		                    $byr_excs_qty 	= 0;
		                    $byr_allo_qty 	= 0;
		                    $byr_today_allo_qty 	= 0;
		                    $byr_total_allo_qty 	= 0;
		                    $byr_allo_bal 	= 0;
		                    $byr_upto_allo_qty = 0;
	                        foreach ($buyerData as $seasonId => $seasonData) 
	                        {   $z=0;
								foreach ($seasonData as $int_ref => $row) 
								{  
									if($row['to_be_demand']>0)
									{
										$excess_qty = $excess_qty_array[$buyer_id][$seasonId][$int_ref]['excess_qty'];
										$alloc_qty = $alloc_qty_array[$buyer_id][$seasonId][$int_ref]['alloc_qty'];
										$today_alloc_qty = $alloc_qty_array[$buyer_id][$seasonId][$int_ref]['today_alloc_qty'];
										$total_alloc_qty = $alloc_qty+$today_alloc_qty;
										// $alloc_bal = ($row['req_qty'] + $row['excess_qty'] - $row['alloc_qty'];
										$alloc_bal = ($row['req_qty'] + $excess_qty) - $total_alloc_qty;
										$upto_allo_qty = $row['to_be_demand'];
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
										?>                        
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
											<? if($r==0){?>                            
											<td title="<? echo $buyer_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$buyer_id];?>"><? echo $buyer_array[$buyer_id]; ?></td>
											<? }?>
											<? if($z==0){?>                            
											<td width="100" valign="middle" rowspan="<? echo $rowspanSeason[$buyer_id][$seasonId];?>"><? echo $season_array[$seasonId]; ?></td>
											<? }?>
											<td width="100" title="PO ID=<?=$row['poid']?>"><? echo $int_ref; ?></td>
											<td width="100" align="right"><? echo number_format($row['req_qty'],0);?></td>
											<td width="100" align="right"><? echo number_format($excess_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($today_alloc_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($total_alloc_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_bal,0);?></td>
											<td width="100" align="right"><? echo number_format($upto_allo_qty,0);?></td>
										</tr>
										<?
										$i++;
										$r++;
										$z++;
										$byr_req_qty 	+= $row['req_qty'];
										$byr_excs_qty 	+= $excess_qty;
										$byr_allo_qty 	+= $alloc_qty;
										$byr_today_allo_qty 	+= $today_alloc_qty;
										$byr_total_allo_qty 	+= $alloc_qty+$today_alloc_qty;
										$byr_allo_bal 	+= $alloc_bal;
										$byr_upto_allo_qty += $upto_allo_qty;

										$gr_req_qty 	+= $row['req_qty'];
										$gr_excs_qty 	+= $excess_qty;
										$gr_allo_qty 	+= $alloc_qty;
										$gr_today_allo_qty 	+= $today_alloc_qty;
										$gr_total_allo_qty 	+= $alloc_qty+$today_alloc_qty;
										$gr_allo_bal 	+= $alloc_bal;
										$gr_upto_allo_qty += $upto_allo_qty;
									}
								}
							}
							if($byr_upto_allo_qty>0)
							{
								?>
								<tr style="font-size:12px;font-weight:bold;background:#ccc;">
									<td colspan="3" align="right">Buyer Total</td>
									<td align="right"><? echo number_format($byr_req_qty,0);?></td>
									<td align="right"><? echo number_format($byr_excs_qty,0);?></td>
									<td align="right"><? echo number_format($byr_allo_qty,0);?></td>
									<td align="right"><? echo number_format($byr_today_allo_qty,0);?></td>
									<td align="right"><? echo number_format($byr_total_allo_qty,0);?></td>
									<td align="right"><? echo number_format($byr_allo_bal,0);?></td>
									<td align="right"><? echo number_format($byr_upto_allo_qty,0);?></td>
								</tr>
								<?
							}                    
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="1000">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="100"><? echo number_format($gr_req_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_excs_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_today_allo_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_total_allo_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_bal,0);?></th>
            			<th width="100"><? echo number_format($gr_upto_allo_qty,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}

if($action=="open_pipeline_popup_bkup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$supplier_array = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");

	//========================================== getting LC info =======================================
	$sqlBtb = "SELECT f.id as PI_ID,a.LC_NUMBER,a.SUPPLIER_ID,a.LC_DATE,e.yarn_inhouse_date as DEL_START_DT,e.delivery_end_date as DEL_END_DT,c.QUANTITY from com_btb_lc_master_details a, com_btb_lc_pi b,com_pi_item_details c,wo_non_order_info_mst d,wo_non_order_info_dtls e,com_pi_master_details f where a.id=b.com_btb_lc_master_details_id and c.pi_id=b.pi_id and c.work_order_id=d.id and d.id=e.mst_id and f.id=c.pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.yarn_count=$count_id and e.yarn_comp_type1st=$comp_id and c.count_name=$count_id  and c.yarn_composition_item1=$comp_id and a.importer_id=$company_id and a.item_category_id=1 and a.item_category_id=f.item_category_id and f.ref_closing_status=0 and d.entry_form=144 and e.yarn_inhouse_date is not null and e.delivery_end_date is not null";
	// echo $sqlBtb;die();
	$btbRes = sql_select($sqlBtb);
	$dataArray = array();
	$piWisedataArray = array();
	foreach ($btbRes as $val) 
	{
		$dataArray[$val['SUPPLIER_ID']][$val['LC_NUMBER']]['lc_date'] = $val['LC_DATE'];
		// $dataArray[$val['SUPPLIER_ID']][$val['LC_NUMBER']]['pi_qty'] += $val['QUANTITY'];

		$piWisedataArray[$val['SUPPLIER_ID']][$val['LC_NUMBER']][$val['PI_ID']]['del_start_dt'] = $val['DEL_START_DT'];
		$piWisedataArray[$val['SUPPLIER_ID']][$val['LC_NUMBER']][$val['PI_ID']]['del_end_dt'] = $val['DEL_END_DT'];
		$piWisedataArray[$val['SUPPLIER_ID']][$val['LC_NUMBER']][$val['PI_ID']]['pi_qty'] += $val['QUANTITY'];
	}

	//========================================== yarn receive based on PI =======================================
	$sqlYarn = "SELECT c.PI_ID,d.LC_NUMBER,d.SUPPLIER_ID, a.cons_quantity AS RCV_QTY	
	from inv_transaction a , inv_receive_master b, com_pi_item_details c,com_btb_lc_master_details d,com_btb_lc_pi e,product_details_master f where a.mst_id=b.id and b.booking_id=c.pi_id and c.pi_id=e.pi_id and d.id=e.com_btb_lc_master_details_id and a.transaction_type in(1) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.company_id=$company_id and f.yarn_count_id=$count_id and f.yarn_comp_type1st=$comp_id and c.count_name=$count_id  and C.yarn_composition_item1=$comp_id and f.yarn_count_id>0 and c.item_category_id=1 and b.receive_basis=1 and a.prod_id=f.id";
	// echo $sqlYarn;die();
	$yarn_res = sql_select($sqlYarn);
	$yarnRcvQtyArray = array();
	$piWiseyarnRcvQtyArray = array();
	foreach ($yarn_res as $row) 
	{
        // $yarnRcvQtyArray[$row['SUPPLIER_ID']][$row['LC_NUMBER']]['rcv_qty'] += $row['RCV_QTY']; 
        $piWiseyarnRcvQtyArray[$row['SUPPLIER_ID']][$row['LC_NUMBER']][$row['PI_ID']]['rcv_qty'] += $row['RCV_QTY']; 
	}
	unset($yarn_res);
	// echo "<pre>";print_r($piWiseyarnRcvQtyArray);die();
	//=============================== calculate pipe line qty ==========================
	$dateObj = new DateTime('today');
	$today = $dateObj->format('d-m-Y');
	$futerDate = date('d-m-Y',strtotime($future_date));
	$futureDate = new DateTime($futerDate);
	$pipe_line_qty_array = array();
	$pi_and_rcv_qty_array = array();
	foreach ($piWisedataArray as $supId => $supData) 
	{
		foreach ($supData as $lcNo => $lcData) 
		{
			foreach ($lcData as $pi_id => $row) 
			{
				$rcvQty = $piWiseyarnRcvQtyArray[$supId][$lcNo][$pi_id]['rcv_qty'];
				// echo $rcvQty = $piWiseyarnRcvQtyArray[$supId][$lcNo][$pi_id]['rcv_qty']."==".$row['pi_qty']."==".$row['del_start_dt']."==".$row['del_end_dt']."<br>";
				if($rcvQty < $row['pi_qty'])
				{
					$del_start_dt 	= new DateTime($row['del_start_dt']);
					$del_end_dt 	= new DateTime($row['del_end_dt']);
					$pipe_line_qty  = 0;
					$piQty 			= $row['pi_qty'];
					if((strtotime($futureDate->format('d-m-Y')) >= strtotime($del_start_dt->format('d-m-Y'))) && (strtotime($futureDate->format('d-m-Y')) <= strtotime($del_end_dt->format('d-m-Y'))))
					{
						$woDaysDiff 	= $del_end_dt->diff($del_start_dt)->format('%a')+1;
						$daysDiff 		= $futureDate->diff($del_start_dt)->format('%a')+1;
						$rcvPerDay 		= $piQty/$woDaysDiff;
						$totRcv 		= $rcvPerDay * $daysDiff;							
						$pipe_line_qty 	= $totRcv - $rcvQty;
						// echo "string1<br>";
					}
					elseif ((strtotime($futureDate->format('d-m-Y')) >= strtotime($del_start_dt->format('d-m-Y'))) && (strtotime($futureDate->format('d-m-Y')) > strtotime($del_end_dt->format('d-m-Y')))) 
					{
						$woDaysDiff 	= $del_end_dt->diff($del_start_dt)->format('%a')+1;
						// $daysDiff 		= $futureDate->diff($del_start_dt)->format('%a')+1;
						$rcvPerDay 		= $piQty/$woDaysDiff;
						$totRcv 		= $rcvPerDay * $woDaysDiff;
						$pipe_line_qty 	= $totRcv - $rcvQty;
						// echo "string2<br>";
					}
					$pipe_line_qty_array[$supId][$lcNo]['pipe_line_qty'] += $pipe_line_qty;
					// echo $comp."==".$countId."==".$pipe_line_qty."<br>";
					$pi_and_rcv_qty_array[$supId][$lcNo]['pi_qty'] += $row['pi_qty'];
					$pi_and_rcv_qty_array[$supId][$lcNo]['rcv_qty'] += $rcvQty;
				}
			}
		}
	}

	//================================ getting rowspan ===========================
	$rowspan = array();
    foreach ($dataArray as $sup_id => $supData) 
    {
        foreach ($supData as $lcNo => $row) 
        {
            $rowspan[$sup_id]++;
        }
    }
	?>
	<div id="data_panel" align="center" style="width:700px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 700px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="700">
                <thead>
                    <tr>
                        <th width="100">Supplier Name</th>
                        <th width="100">L/C No</th>
                        <th width="100">L/C Date</th>
                        <th width="100">L/C Qty</th>
                        <th width="100">Rcvd Qty</th>
                        <th width="100">Balance Qty</th>
                        <th width="100">Up to <? echo change_date_format($future_date);?> Rcvd Qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:720px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="700">
	                <tbody>
	                    <?
	                    $i=0;
	                    $gr_lc_qty 			= 0;
	                    $gr_rcv_qty 		= 0;
	                    $gr_bal_qty 		= 0;
	                    $gr_upto_rcv_qty 	= 0;
	                    foreach ($dataArray as $supp_id => $supData) 
	                    {
	                    	$r=0;
		                    $sup_lc_qty 		= 0;
		                    $sup_rcv_qty 		= 0;
		                    $sup_bal_qty 		= 0;
		                    $sup_upto_rcv_qty 	= 0;
	                        foreach ($supData as $lc_no => $row) 
	                        {  
	                        	if($pi_and_rcv_qty_array[$supp_id][$lc_no]['pi_qty']>0)
	                        	{
		                        	$pi_qty = $pi_and_rcv_qty_array[$supp_id][$lc_no]['pi_qty'];
		                        	$rcv_qty = $pi_and_rcv_qty_array[$supp_id][$lc_no]['rcv_qty'];
		                        	$bal_qty = $pi_qty - $rcv_qty;
		                        	$pipe_line_qty = $pipe_line_qty_array[$supp_id][$lc_no]['pipe_line_qty'];

		                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
		                            ?>                        
		                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
		                            	<? if($r==0){?>                            
		                                <td title="<? echo $supp_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$supp_id];?>"><? echo $supplier_array[$supp_id]; ?></td>
		                                <? }?>
		                                <td width="100"><? echo $lc_no; ?></td>
		                                <td width="100" align="center"><? echo date('d-M',strtotime($row['lc_date']));?></td>
		                                <td width="100" align="right"><? echo number_format($pi_qty,0);?></td>
		                                <td width="100" align="right"><? echo number_format($rcv_qty,0);?></td>
		                                <td width="100" align="right"><? echo number_format($bal_qty,0);?></td>
		                                <td width="100" align="right"><? echo number_format($pipe_line_qty,0);?></td>
		                            </tr>
		                            <?
		                            $i++;
		                            $r++;
				                    $sup_lc_qty 	+= $pi_qty;
				                    $sup_rcv_qty 	+= $rcv_qty;
				                    $sup_bal_qty 	+= $bal_qty;
				                    $sup_upto_rcv_qty+= $pipe_line_qty;
				                    
				                    $gr_lc_qty 		+= $pi_qty;
				                    $gr_rcv_qty 	+= $rcv_qty;
				                    $gr_bal_qty 	+= $bal_qty;
				                    $gr_upto_rcv_qty+= $pipe_line_qty;
				                }
	                        }
	                        if($sup_lc_qty>0)
	                        {
	                        ?>
	                        <tr style="font-size:12px;font-weight:bold;background:#ccc;">
	                        	<td colspan="3" align="right">Supplier Total</td>
	                        	<td align="right"><? echo number_format($sup_lc_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_rcv_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_bal_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_upto_rcv_qty,0);?></td>
	                        </tr>
	                        <?   
	                        }                     
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="700">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="100"><? echo number_format($gr_lc_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_rcv_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_bal_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_upto_rcv_qty,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}

if($action=="open_pipeline_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);	

	$dateObj = new DateTime('today');
	$today = $dateObj->format('d-m-Y');
	$current_date = $dateObj->format('d-M-Y');
	$next_date = date('d-M-Y', strtotime("+1 day", strtotime($current_date)));
	$futerDate = date('d-m-Y',strtotime($future_date));
	$futureDate = new DateTime($futerDate);

	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$supplier_array = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");

	//========================================== getting LC info =======================================	
	$sqlBtb = "SELECT a.id as delv_plan_id, d.id as wo_id, c.id, f.id as PI_ID,f.SUPPLIER_ID,a.delivery_plan_date as DEL_START_DT,c.QUANTITY,(case when a.delivery_plan_date between '$next_date' and '$future_date' THEN a.PLAN_QNTY ELSE 0 END) as PLAN_QTY,(case when a.delivery_plan_date <= '$current_date' THEN a.PLAN_QNTY ELSE 0 END) as ALL_PLAN_QTY,f.ref_closing_status from com_pi_item_details c,wo_non_order_info_mst d,com_pi_master_details f,wo_non_order_info_dtls e ,wo_non_order_info_dtls_plan a  where c.work_order_id=d.id and d.id=e.mst_id and f.id=c.pi_id and e.id=a.po_dtls_id AND e.id = c.work_order_dtls_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.yarn_count=$count_id and e.yarn_comp_type1st=$comp_id and c.count_name=$count_id  and c.yarn_composition_item1=$comp_id and d.company_name=$company_id AND f.item_category_id = 1 and d.entry_form=144 ";//and f.ref_closing_status=0
	// echo $sqlBtb;die(); 
	$btbRes = sql_select($sqlBtb);
	$piIDArray = array();
	foreach ($btbRes as $val) 
	{
		if($val['REF_CLOSING_STATUS']==0)
		{
			$piIDArray[$val['PI_ID']] = $val['PI_ID'];
		}
	}

	$piIDCond = where_con_using_array($piIDArray,0,"f.id");

	// ======================================== getting pi qty ==============================		
	$sql = "SELECT c.id, f.id as PI_ID,f.SUPPLIER_ID,c.QUANTITY,f.ref_closing_status from com_pi_item_details c,com_pi_master_details f  where c.pi_id=f.id and c.status_active=1 and c.is_deleted=0 and c.count_name=$count_id  and c.yarn_composition_item1=$comp_id  AND f.item_category_id = 1 and f.ref_closing_status=0 $piIDCond";
	// echo $sql;die;
	$sqlRes = sql_select($sql);
	$pi_qty_array = array();
	foreach ($sqlRes as $v) 
	{
		$pi_qty_array[$v['SUPPLIER_ID']][$v['PI_ID']] += $v['QUANTITY'];
	}
	// echo "<pre>";print_r($pi_qty_array);die();
	// =====================================================================================

	$dataArray = array();
	$piWisedataArray = array();
	$piArray = array();
	$woIdArray = array();
	$piWiseWoIdArray = array();
	$plan_wo_id_array = array();
	$chkArray = array();
	$chkArray2 = array();
	$chkArray3 = array();
	$dtls_id_chk_array = array();
	foreach ($btbRes as $val) 
	{
		if($val['REF_CLOSING_STATUS']==0)
		{
			$dataArray[$val['SUPPLIER_ID']][$val['PI_ID']]['lc_date'] = $val['LC_DATE'];
			if(!in_array($val['ID'], $chkArray))
			{
				if($chkArray3[$val['SUPPLIER_ID']][$val['PI_ID']]=="")
				{
					// $piWisedataArray[$val['SUPPLIER_ID']][$val['PI_ID']][$val['DEL_START_DT']]['pi_qty'] += $val['QUANTITY'];
					$piWisedataArray[$val['SUPPLIER_ID']][$val['PI_ID']][$val['DEL_START_DT']]['pi_qty'] += $pi_qty_array[$val['SUPPLIER_ID']][$val['PI_ID']];
					$chkArray[$val['ID']] = $val['ID'];
					// echo $val['SUPPLIER_ID']."==".$val['PI_ID']."<br>";
					$chkArray3[$val['SUPPLIER_ID']][$val['PI_ID']]= $val['PI_ID'];
				}
			}

			if($chkArray2[$val['DELV_PLAN_ID']][$val['DEL_START_DT']]=="") // omit duplicate plan qty
			{
				$piWisedataArray[$val['SUPPLIER_ID']][$val['PI_ID']][$val['DEL_START_DT']]['plan_qty'] += $val['PLAN_QTY'];
				$piWisedataArray[$val['SUPPLIER_ID']][$val['PI_ID']][$val['DEL_START_DT']]['all_plan_qty'] += $val['ALL_PLAN_QTY'];
				$chkArray2[$val['DELV_PLAN_ID']][$val['DEL_START_DT']] = $val['DELV_PLAN_ID'];
				// echo $val['PI_ID']."==".$val['ALL_PLAN_QTY']."<br>";
			}
			$piArray[$val['PI_ID']] = $val['PI_ID'];
		}
		$woIdArray[$val['WO_ID']] = $val['WO_ID'];		
		$piWiseWoIdArray[$val['SUPPLIER_ID']][$val['PI_ID']] = $val['WO_ID'];
		$plan_wo_id_array[$val['PI_ID']] = $val['PI_ID'];
		$plan_wo_id_array[$val['WO_ID']] = $val['WO_ID'];
	}
	unset($btbRes);
	// echo "<pre>";print_r($piWisedataArray);die();
	// ====================================== wo qty =====================================
	if(count($woIdArray)>0)
	{
		$wo_id_cond = wherenot_con_using_array($woIdArray,0,"d.id");
	}
	$sqlBtb = "SELECT e.id as dtls_id, d.id as pi_id, d.WO_NUMBER,d.WO_DATE,d.SUPPLIER_ID,a.delivery_plan_date as DEL_START_DT,a.PLAN_QNTY as QUANTITY,e.SUPPLIER_ORDER_QUANTITY as pi_qty,(case when a.delivery_plan_date between '$next_date' and '$future_date' THEN a.PLAN_QNTY ELSE 0 END) as PLAN_QTY,(case when a.delivery_plan_date <= '$current_date' THEN a.PLAN_QNTY ELSE 0 END) as ALL_PLAN_QTY from wo_non_order_info_mst d,wo_non_order_info_dtls e , wo_non_order_info_dtls_plan a where d.id=e.mst_id and e.id=a.po_dtls_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.yarn_count=$count_id and e.yarn_comp_type1st=$comp_id and e.YARN_COUNT=$count_id  and e.YARN_COMP_TYPE1ST=$comp_id and d.company_name=$company_id and d.entry_form=144 $wo_id_cond";
	// echo $sqlBtb;die();
	$btbRes = sql_select($sqlBtb);
	$lc_info_array = array();
	$dtls_id_chk_array = array();
	// echo "<pre>";print_r($btbRes);die();
	foreach ($btbRes as $v) 
	{
		// echo $v['SUPPLIER_ID'];
		$dataArray[$v['SUPPLIER_ID']][$v['PI_ID']]['lc_date'] = $v['WO_DATE'];
		$dataArray[$v['SUPPLIER_ID']][$v['PI_ID']]['lc_number'] = $v['WO_NUMBER'];

		$lc_info_array[$v['SUPPLIER_ID']][$v['PI_ID']]['lc_date'] = $v['WO_DATE'];
		$lc_info_array[$v['SUPPLIER_ID']][$v['PI_ID']]['lc_number'] = $v['WO_NUMBER'];

		if($dtls_id_chk_array[$v['DTLS_ID']]=="")
		{
			$piWisedataArray[$v['SUPPLIER_ID']][$v['PI_ID']][$v['DEL_START_DT']]['pi_qty'] += $v['PI_QTY'];
			$dtls_id_chk_array[$v['DTLS_ID']] = $v['DTLS_ID'];
		}
		// $piWisedataArray[$v['SUPPLIER_ID']][$v['PI_ID']][$v['DEL_START_DT']]['pi_qty'] += $v['QUANTITY'];
		$piWisedataArray[$v['SUPPLIER_ID']][$v['PI_ID']][$v['DEL_START_DT']]['plan_qty'] += $v['PLAN_QTY'];
		$piWisedataArray[$v['SUPPLIER_ID']][$v['PI_ID']][$v['DEL_START_DT']]['all_plan_qty'] += $v['ALL_PLAN_QTY'];
		// echo $v['PI_ID']."==".$v['ALL_PLAN_QTY']."<br>";
		$plan_wo_id_array[$v['PI_ID']] = $v['PI_ID'];
	}
	unset($btbRes);

	// echo "<pre>";print_r($dataArray);die();
	$piIds = implode(",", $piArray);
    $pi_id_list_arr=array_chunk($piArray,999);

    if($piIds !="")
    {
        $piCond = " and "; 
        $p=1;
        foreach($pi_id_list_arr as $pi_process)
        {
            if($p==1) $piCond .="  ( b.pi_id in(".implode(',',$pi_process).")"; 
            else  $piCond .=" or b.pi_id in(".implode(',',$pi_process).")";    
            
            $p++;
        }
        $piCond .=")";
    }

    $sqlLc = "SELECT a.LC_NUMBER, a.SUPPLIER_ID, a.LC_DATE,b.PI_ID from com_btb_lc_master_details a,com_btb_lc_pi b where a.id = b.com_btb_lc_master_details_id $piCond"; 
    // echo $sqlLc;die();
    $lcRes = sql_select($sqlLc);
   
    foreach ($lcRes as $val) 
    {
    	$lc_info_array[$val['SUPPLIER_ID']][$val['PI_ID']]['lc_number'] = $val['LC_NUMBER'];
    	$lc_info_array[$val['SUPPLIER_ID']][$val['PI_ID']]['lc_date'] = $val['LC_DATE'];
    }
    unset($lcRes);
    //========================================== yarn receive =======================================

	$plan_wo_id_cond = where_con_using_array($plan_wo_id_array,0,"b.booking_id");
	$sqlYarn = "SELECT b.id as rcv_id,b.booking_id as PI_ID,b.SUPPLIER_ID, (case when a.transaction_date<='$current_date' then a.cons_quantity else 0 end) AS RCV_QTY, (case when a.transaction_date='$current_date' then a.cons_quantity else 0 end) AS TODAY_RCV_QTY
	from inv_transaction a , inv_receive_master b, product_details_master f where a.mst_id=b.id and a.transaction_type=1 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.company_id=$company_id and f.yarn_count_id=$count_id and f.yarn_comp_type1st=$comp_id and f.yarn_count_id>0 and b.receive_basis in(1,2) and a.prod_id=f.id $plan_wo_id_cond";// $plan_wo_id_cond
	// echo $sqlYarn;die();
	$yarn_res = sql_select($sqlYarn);
	$yarnRcvQtyArray = array();
	$piWiseyarnRcvQtyArray = array();
	foreach ($yarn_res as $row) 
	{
        // $yarnRcvQtyArray[$row['SUPPLIER_ID']][$row['LC_NUMBER']]['rcv_qty'] += $row['RCV_QTY']; 
        $piWiseyarnRcvQtyArray[$row['SUPPLIER_ID']][$row['PI_ID']]['rcv_qty'] += $row['RCV_QTY']; 
        $piWiseyarnRcvQtyArray[$row['SUPPLIER_ID']][$row['PI_ID']]['today_rcv_qty'] += $row['TODAY_RCV_QTY']; 
		// $s+=$row['RCV_QTY'];
		// echo $row['SUPPLIER_ID']."=".$row['PI_ID']."=".$row['RCV_QTY']."<br>";
	}
	// echo number_format($s);die;
	unset($yarn_res);
	// echo "<pre>";print_r($piWiseyarnRcvQtyArray);die();


	// ============================= rcv return against work order ==============================
	$sql = "SELECT c.BOOKING_ID as WO_ID,b.SUPPLIER_ID, (case when a.transaction_date<='$current_date' then a.cons_quantity else 0 end) AS RCV_RTN_QTY, (case when a.transaction_date='$current_date' then a.cons_quantity else 0 end) AS TODAY_RCV_RTN_QTY
	from inv_transaction a , inv_issue_master b, inv_receive_master c, product_details_master f where a.mst_id=b.id  and a.prod_id=f.id and b.received_id=c.id and a.transaction_type=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.company_id=$company_id and f.yarn_count_id=$count_id and f.yarn_comp_type1st=$comp_id and f.yarn_count_id>0 and c.RECEIVE_BASIS=2";
	// echo $sql;die;
	$yarn_res = sql_select($sql);
	// ==========================
	foreach ($yarn_res as $row) 
	{
        $piWiseyarnRcvQtyArray[$row['SUPPLIER_ID']][$row['WO_ID']]['rcv_qty'] -= $row['RCV_RTN_QTY']; 
        $piWiseyarnRcvQtyArray[$row['SUPPLIER_ID']][$row['WO_ID']]['today_rcv_qty'] -= $row['TODAY_RCV_RTN_QTY']; 
	}

	// ============================= rcv return as per PI ==============================
	$sql = "SELECT b.received_id, b.PI_ID,b.SUPPLIER_ID, (case when a.transaction_date<='$current_date' then a.cons_quantity else 0 end) AS RCV_RTN_QTY, (case when a.transaction_date='$current_date' then a.cons_quantity else 0 end) AS TODAY_RCV_RTN_QTY
	from inv_transaction a , inv_issue_master b, product_details_master f where a.mst_id=b.id and a.transaction_type=3 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.company_id=$company_id and f.yarn_count_id=$count_id and f.yarn_comp_type1st=$comp_id and f.yarn_count_id>0 and a.prod_id=f.id";
	// echo $sql;die();
	$yarn_res = sql_select($sql);
	// ==========================
	foreach ($yarn_res as $row) 
	{
        $piWiseyarnRcvQtyArray[$row['SUPPLIER_ID']][$row['PI_ID']]['rcv_qty'] -= $row['RCV_RTN_QTY']; 
        $piWiseyarnRcvQtyArray[$row['SUPPLIER_ID']][$row['PI_ID']]['today_rcv_qty'] -= $row['TODAY_RCV_RTN_QTY']; 
	}
	unset($yarn_res);

	//=============================== calculate pipe line qty ==========================
	
	$pipe_line_qty_array = array();
	$pi_and_rcv_qty_array = array();
	foreach ($piWisedataArray as $supId => $supData) 
	{
		foreach ($supData as $pi_id => $pi_data) 
		{
			foreach ($pi_data as $delv_date => $row) 
			{
				if((strtotime($future_date) >= strtotime($delv_date)) && (strtotime($next_date) <= strtotime($delv_date)))
				{								
					$pi_and_rcv_qty_array[$supId][$pi_id]['plan_qty'] += $row['plan_qty'];
					// echo $supId."==".$pi_id."==".$delv_date."==".$row['plan_qty']."<br>";
				}
				// echo $supId."==".$pi_id."==".$delv_date."==".$row['plan_qty']."<br>";
				$pi_and_rcv_qty_array[$supId][$pi_id]['pi_qty'] += $row['pi_qty'];				
				$pi_and_rcv_qty_array[$supId][$pi_id]['all_plan_qty'] += $row['all_plan_qty'];	
				// $x+= $row['all_plan_qty'];			
			}
		}
	}
	// echo $x;die;
	// echo "<pre>";print_r($pi_and_rcv_qty_array);die();
	//================================ getting rowspan ===========================
	$rowspan = array();
    foreach ($dataArray as $sup_id => $supData) 
    {
        foreach ($supData as $lcNo => $row) 
        {
        	if($pi_and_rcv_qty_array[$sup_id][$lcNo]['pi_qty']>0)
	        {
            	$rowspan[$sup_id]++;
            }
        }
    }

	?>
	<div id="data_panel" align="center" style="width:900px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 900px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
                <thead>
                    <tr>
                        <th width="100">Supplier Name</th>
                        <th width="100">LC/WO No</th>
                        <th width="100">LC/WO Date</th>
                        <th width="80">LC/WO Qty</th>
                        <th width="80">Rcvd Qty</th>
                        <th width="80">Purchase Backlog Qty</th>
                        <th width="80">Today Rcvd Qty</th>
                        <th width="80">Total Rcvd Qty</th>
                        <th width="80">Balance Qty</th>
                        <th width="80">Up to <? echo change_date_format($future_date);?> Rcvd Qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:920px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
	                <tbody>
	                    <?
	                    $i=0;
	                    $gr_lc_qty 			= 0;
	                    $gr_rcv_qty 		= 0;
	                    $gr_pur_bklog_qty 	= 0;
	                    $gr_today_rcv_qty 	= 0;
	                    $gr_bal_qty 		= 0;
	                    $gr_total_rcv_qty 	= 0;
	                    $gr_upto_rcv_qty 	= 0;
	                    foreach ($dataArray as $supp_id => $supData) 
	                    {
	                    	$r=0;
		                    $sup_lc_qty 		= 0;
		                    $sup_rcv_qty 		= 0;
		                    $sup_pur_bklog_qty	= 0;
		                    $sup_today_rcv_qty	= 0;
		                    $sup_bal_qty 		= 0;
		                    $sup_total_rcv_qty 	= 0;
		                    $sup_upto_rcv_qty 	= 0;
							$wo_chk_arr = array();
	                        foreach ($supData as $pi_id => $row) 
	                        {  
	                        	if($pi_and_rcv_qty_array[$supp_id][$pi_id]['pi_qty']>0)
	                        	{
	                        		$lc_no = $lc_info_array[$supp_id][$pi_id]['lc_number'];
	                        		$lc_date = $lc_info_array[$supp_id][$pi_id]['lc_date'];
		                        	// $pi_qty = ($lc_no !="") ? $pi_and_rcv_qty_array[$supp_id][$pi_id]['pi_qty'] : 0;
		                        	$pi_qty = $pi_and_rcv_qty_array[$supp_id][$pi_id]['pi_qty'];
		                        	// $plan_qty = ($lc_no !="") ? $pi_and_rcv_qty_array[$supp_id][$pi_id]['plan_qty'] : 0;
		                        	$plan_qty = $pi_and_rcv_qty_array[$supp_id][$pi_id]['plan_qty'];
		                        	// $all_plan_qty = ($lc_no !="") ? $pi_and_rcv_qty_array[$supp_id][$pi_id]['all_plan_qty'] : 0;
		                        	$all_plan_qty = $pi_and_rcv_qty_array[$supp_id][$pi_id]['all_plan_qty'];
		                        	$rcv_qty = $piWiseyarnRcvQtyArray[$supp_id][$pi_id]['rcv_qty'];
									// echo $pi_id."==".$rcv_qty."<br>";
									if($wo_chk_arr[$piWiseWoIdArray[$supp_id][$pi_id]]=="")
									{
										if(!$rcv_qty) // take qty when wo attach to pi after receive
										{
											
											$rcv_qty = $piWiseyarnRcvQtyArray[$supp_id][$piWiseWoIdArray[$supp_id][$pi_id]]['rcv_qty'];
											// echo $rcv_qty."=".$pi_id."<br>";
										}
										$wo_chk_arr[$piWiseWoIdArray[$supp_id][$pi_id]] = $piWiseWoIdArray[$supp_id][$pi_id];
									}
		                        	$today_rcv_qty = $piWiseyarnRcvQtyArray[$supp_id][$pi_id]['today_rcv_qty'];
									if(!$today_rcv_qty)
									{
										$today_rcv_qty = $piWiseyarnRcvQtyArray[$supp_id][$piWiseWoIdArray[$supp_id][$pi_id]]['today_rcv_qty'];
									}
									$total_rcv_qty = $rcv_qty+$today_rcv_qty;
		                        	$bal_qty = $pi_and_rcv_qty_array[$supp_id][$pi_id]['pi_qty'] - ($rcv_qty+$today_rcv_qty);
		                        	$pipe_line_qty = $plan_qty;
									$pur_bklog_qty = $all_plan_qty - $rcv_qty;
									// echo $supp_id."=".$all_plan_qty ."-". $rcv_qty."<br>";
		                        	// if($lc_no !="")
		                        	// {
			                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
			                            ?>                        
			                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
			                            	<? if($r==0){?>                            
			                                <td title="<? echo $supp_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$supp_id];?>"><? echo $supplier_array[$supp_id]; ?></td>
			                                <? }?>
			                                <td width="100" title="<?=$pi_id;?>"><? echo $lc_no; ?></td>
			                                <td width="100" align="center"><? echo ($lc_date !="") ? date('d-M',strtotime($lc_date)) : "";?></td>
			                                <td width="80" align="right"><? echo number_format($pi_qty,0);?></td>
			                                <td width="80" align="right"><? echo number_format($rcv_qty,0);?></td>
			                                <td width="80" align="right" title="plan=<?=$all_plan_qty .'- , rcv='. $rcv_qty;?>"><? echo number_format($pur_bklog_qty,0);?></td>
			                                <td width="80" align="right"><? echo number_format($today_rcv_qty,0);?></td>
			                                <td width="80" align="right"><? echo number_format($total_rcv_qty,0);?></td>
			                                <td width="80" align="right"><? echo number_format($bal_qty,0);?></td>
			                                <td width="80" align="right"><? echo number_format($pipe_line_qty,0);?></td>
			                            </tr>
			                            <?
			                            $i++;
			                            $r++;
					                    $sup_lc_qty 	+= $pi_qty;
					                    $sup_rcv_qty 	+= $rcv_qty;
					                    $sup_pur_bklog_qty 	+= $pur_bklog_qty;
					                    $sup_today_rcv_qty 	+= $today_rcv_qty;
					                    $sup_bal_qty 	+= $bal_qty;
					                    $sup_total_rcv_qty+= $total_rcv_qty;
					                    $sup_upto_rcv_qty+= $pipe_line_qty;
					                    
					                    $gr_lc_qty 		+= $pi_qty;
					                    $gr_rcv_qty 	+= $rcv_qty;
					                    $gr_pur_bklog_qty 	+= $pur_bklog_qty;
					                    $gr_today_rcv_qty 	+= $today_rcv_qty;
					                    $gr_bal_qty 	+= $bal_qty;
					                    $gr_total_rcv_qty+= $total_rcv_qty;
					                    $gr_upto_rcv_qty+= $pipe_line_qty;
					                // }
				                }
	                        }
	                        if($sup_lc_qty>0 || $sup_rcv_qty>0 || $sup_bal_qty>0 || $sup_upto_rcv_qty>0)
	                        {
	                        ?>
	                        <tr style="font-size:12px;font-weight:bold;background:#ccc;">
	                        	<td colspan="3" align="right">Supplier Total</td>
	                        	<td align="right"><? echo number_format($sup_lc_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_rcv_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_pur_bklog_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_today_rcv_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_total_rcv_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_bal_qty,0);?></td>
	                        	<td align="right"><? echo number_format($sup_upto_rcv_qty,0);?></td>
	                        </tr>
	                        <?   
	                        }                     
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="900">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="80"><? echo number_format($gr_lc_qty,0);?></th>
            			<th width="80"><? echo number_format($gr_rcv_qty,0);?></th>
            			<th width="80"><? echo number_format($gr_pur_bklog_qty,0);?></th>
            			<th width="80"><? echo number_format($gr_today_rcv_qty,0);?></th>
            			<th width="80"><? echo number_format($gr_total_rcv_qty,0);?></th>
            			<th width="80"><? echo number_format($gr_bal_qty,0);?></th>
            			<th width="80"><? echo number_format($gr_upto_rcv_qty,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}

if($action=="open_closing_backlog_popup_bkup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$current_date = date("j-M-Y");
	// ===================================== date range custom function ===============================================
	function create_date_range($start_date, $end_date, $step = '+1 day', $output_format = 'd-m-Y' ) 
	{
	    $dates = array();
	    $current = strtotime($start_date);
	    $end_date = strtotime($end_date);

	    while( $current <= $end_date ) {

	        $dates[] = date($output_format, $current);
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}
	$date_range_array = create_date_range(date('d-m-Y'),$future_date);
	// echo "<pre>";print_r($date_range_array);
	/*==========================================================================================/
	/										main query 											/
	/==========================================================================================*/
	$sql="SELECT b.id as PO_ID from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_yarn_cost_dtls d where a.id=b.job_id  and a.id=c.job_id and b.job_id=c.job_id and b.job_id=d.job_id  and c.job_no=d.job_no and a.job_no=d.job_no and c.id=d.fabric_cost_dtls_id and a.company_name='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.shiping_status in(1,2) and d.count_id = $count_id and d.copm_one_id=$comp_id";
	// echo $sql;die();
	$result=sql_select($sql);
	if(count($result)==0)
	{
		// echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
	}
	$po_id_array = array();
	foreach($result as $row)
	{
		$po_id_array[$row['PO_ID']] = $row['PO_ID'];
	}
	unset($result);

	$poIds = implode(",", $po_id_array);
	$po_id_list_arr=array_chunk($po_id_array,999);
	$poCond = " and ";
	$p=1;
	foreach($po_id_list_arr as $poids)
    {
    	if($p==1) 
		{
			$poCond .="  ( b.po_break_down_id in(".implode(',',$poids).")"; 
		}
        else
        {
          $poCond .=" or b.po_break_down_id in(".implode(',',$poids).")";
      	}
        $p++;
    }
    $poCond .=")";
	/*==========================================================================================/
	/										yarn receive 										/
	/==========================================================================================*/
	/*$sql_receive = "SELECT  
	sum(case when a.transaction_type in (1,4) and a.transaction_date< '$current_date' then a.cons_quantity else 0 end) as RCV_TOTAL_OPENING, 
	sum(case when a.transaction_type in (1) and c.receive_purpose<>5 and a.transaction_date <'$current_date'  then a.cons_quantity else 0 end) as PURCHASE, 
	sum(case when a.transaction_type=4 and c.knitting_source=1 and a.transaction_date <'$current_date' then a.cons_quantity else 0 end) as RCV_INSIDE_RETURN, 
	sum(case when a.transaction_type=4 and c.knitting_source!=1 and a.transaction_date <'$current_date'  then a.cons_quantity else 0 end) as RCV_OUTSIDE_RETURN 
	from inv_transaction a , inv_receive_master c, product_details_master b where a.mst_id=c.id and b.id=a.prod_id and a.transaction_type in (1,4) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id=$count_id and b.yarn_comp_type1st=$comp_id and b.item_category_id=1";
	// echo $sql_receive;die();
	$recv_res = sql_select($sql_receive);
	$yarn_rcv_qty = 0;
	foreach ($recv_res as $row) 
	{
        $yarn_rcv_qty += $row['RCV_TOTAL_OPENING']; 
	}
	unset($recv_res);*/
	/*==========================================================================================/
	/										yarn issue 											/
	/==========================================================================================*/
	/*$sql_issue = "SELECT 
    sum(case when a.transaction_type in (2,3) and a.transaction_date<'$current_date' then a.cons_quantity else 0 end) as ISSUE_TOTAL_OPENING,
    sum(case when a.transaction_type=2 and c.knit_dye_source=1 and c.issue_purpose<>5 and a.transaction_date<'$current_date' then a.cons_quantity else 0 end) as ISSUE_INSIDE,
    sum(case when a.transaction_type=2 and c.knit_dye_source!=1 and c.issue_purpose<>5 and a.transaction_date  <'$current_date' then a.cons_quantity else 0 end) as ISSUE_OUTSIDE,
    sum(case when a.transaction_type=3 and c.entry_form=8 and a.transaction_date < '$current_date' then a.cons_quantity else 0 end) as RCV_RETURN
    from inv_transaction a, inv_issue_master c,product_details_master b
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id=$count_id and b.yarn_comp_type1st=$comp_id and b.item_category_id=1";
    // echo $sql_issue;die();
    $issue_res = sql_select($sql_issue);
    $yarn_issue_qty = 0;
	foreach ($issue_res as $row) 
	{
        $yarn_issue_qty += $row['ISSUE_TOTAL_OPENING']; 
	}
	unset($issue_res);*/
	/*==========================================================================================/
	/										yarn allocation										/
	/==========================================================================================*/
	$sql_alloc = "SELECT b.po_break_down_id as PO_ID,b.QNTY from inv_material_allocation_mst a,inv_material_allocation_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and b.item_category=1 and c.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$comp_id and c.company_id=$company_id";
	// echo $sql_alloc;die();
	$alloc_res = sql_select($sql_alloc);
    $po_wise_alloc_qty_array = array();
    $alloc_qty = 0;
	foreach ($alloc_res as $row) 
	{
        $alloc_qty += $row['QNTY']; 
        $po_wise_alloc_qty_array[$row['PO_ID']]['alloc_qty'] += $row['QNTY']; 
	}
	unset($alloc_res);
	/*==========================================================================================/
	/										yarn transaction 											/
	/==========================================================================================*/
	$sql_trns = "SELECT b.available_qnty as QTY
    from  product_details_master b
    where b.is_deleted=0  and b.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1";
    // echo $sql_trns;die();
    $trns_res = sql_select($sql_trns);
    $available_qnty = 0;
	foreach ($trns_res as $row) 
	{
		$available_qnty += $row['QTY'];
	}
	unset($sql_trns);
	/*==========================================================================================/
	/										get tna data										/
	/==========================================================================================*/
	$tnaPoCond = str_replace("b.po_break_down_id", "a.po_number_id", $poCond);
	$sqltna = "SELECT A.PO_NUMBER_ID,A.TASK_NUMBER,MAX(A.TASK_START_DATE) AS START_DATE,MAX(A.TASK_FINISH_DATE) AS END_DATE,MAX(A.ACTUAL_START_DATE) AS ACTUAL_START_DATE,MAX(A.ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE FROM TNA_PROCESS_MST A,WO_PO_BREAK_DOWN B WHERE A.PO_NUMBER_ID=B.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  and A.TASK_START_DATE is not null AND B.PO_QUANTITY>0 AND A.TASK_TYPE=1 and a.TASK_NUMBER=48 $tnaPoCond group by A.PO_NUMBER_ID,A.TASK_NUMBER";
	// echo $sqltna;die();
	$tnaRes = sql_select($sqltna);
    $tnaDateArray = array();
    foreach ($tnaRes as $val) 
    {
        $tnaDateArray[$val['PO_NUMBER_ID']]['start_date'] = $val['START_DATE'];
        $tnaDateArray[$val['PO_NUMBER_ID']]['end_date']   = $val['END_DATE'];
    }
    unset($tnaRes);

    /*==========================================================================================/
	/				get yarn returnable qty when issue qty from smple without order				/
	/==========================================================================================*/
	$sqlIssue = "SELECT a.return_qnty as QTY
    from inv_transaction a, inv_issue_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and d.item_category=2 and d.booking_type=4 and c.issue_basis=1 and c.issue_purpose=8 and a.transaction_type=2 and a.return_qnty>0";
    // echo $sqlIssue;die();
    $issueRes = sql_select($sqlIssue);
    $non_order_returnable_qnty=0;
	foreach ($issueRes as $row) 
	{
        $non_order_returnable_qnty += $row['QTY']; 
	}
	unset($issueRes);

	/*==========================================================================================/
	/					yarn issue rtn qty from smple without order								/
	/==========================================================================================*/
	$sqlRcvRtn = "SELECT a.cons_quantity as QTY
    from inv_transaction a, inv_receive_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and c.entry_form=9 and d.item_category=2 and d.booking_type=4 and c.receive_basis=1 and c.booking_without_order=1 and a.transaction_type=4";
    // echo $sqlRcvRtn;die();
    $receiveRes = sql_select($sqlRcvRtn);
    $non_order_rcv_rtn_qty =0;
	foreach ($receiveRes as $row) 
	{
        	$non_order_rcv_rtn_qty += $row['QTY']; 
	}
	unset($issueRes);

	/*==========================================================================================/
	/						get bookikg qty from smple without order							/
	/==========================================================================================*/
    $sql_smn = "SELECT a.BOOKING_NO,a.BUYER_ID,b.GREY_FABRIC from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,lib_yarn_count_determina_mst c, lib_yarn_count_determina_dtls d where a.booking_no=b.booking_no and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and b.lib_yarn_count_deter_id > 0 and c.id=d.mst_id and c.id=b.lib_yarn_count_deter_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.copmposition_id=$comp_id and d.count_id=$count_id and a.company_id=$company_id and a.is_approved=1";
	// echo $sql_smn;die();
	$smnRes = sql_select($sql_smn);
	$non_order_booking_qty = 0;
	foreach ($smnRes as $val) 
	{
		$non_order_booking_qty += $val['GREY_FABRIC'];
	}
	/*==========================================================================================/
	/							get issue qty from smple without order							/
	/==========================================================================================*/
	$sqlIssue = "SELECT d.BOOKING_NO,d.BUYER_ID,a.cons_quantity as QTY
    from inv_transaction a, inv_issue_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and d.item_category=2 and d.booking_type=4 and c.issue_basis=1 and c.issue_purpose=8 and a.transaction_type=2 and d.is_approved=1";
    // echo $sqlIssue;die();
    $issueRes = sql_select($sqlIssue);
    $non_order_issue_qty = 0;
	foreach ($issueRes as $row) 
	{
        $non_order_issue_qty += $row['QTY']; 
	}
	unset($issueRes);

    /*==========================================================================================/
	/										yarn req. qty										/
	/==========================================================================================*/
	$condition= new condition();     
    $condition->po_id_in($poIds);     
    $condition->init();

    $yarn= new yarn($condition);     
    $yarnReqQtyArr=$yarn->getOrderCountAndCompositionWiseYarnQtyArray();
    /*$yarnReqQtyArray = array();
    foreach ($po_id_array as $po => $val) 
    {
    	$yarnReqQtyArray[$po] = $yarnReqQtyArr[$po][$count_id][$comp_id];
    }
    echo "<pre>";print_r($yarnReqQtyArray);die('ok');*/
    
	/*==========================================================================================/
	/							get yarn deman and opening backlog								/
	/==========================================================================================*/
	$dateObj = new DateTime('today');
	$today = $dateObj->format('d-m-Y');	
	$futerDate = date('d-m-Y',strtotime($future_date));
	$futureDate = new DateTime($futerDate);
	$opening_backlog_qty = 0;
	$tot_will_be_allocate = 0;
	$to_be_demand_array = array();
	foreach ($yarnReqQtyArr as $poId => $poData) 
	{
		foreach ($poData as $countId => $countData) 
		{
			foreach ($countData as $compId => $row) 
			{
				if($countId==$count_id && $compId==$comp_id)
				{
					if($tnaDateArray[$poId]['start_date'] !="" && $tnaDateArray[$poId]['end_date'] !="")
					{
						$startDate = new DateTime($tnaDateArray[$poId]['start_date']);
						$endDate   = new DateTime($tnaDateArray[$poId]['end_date']);
						
						$budgetQty = $row;
						$allocQty = 0;
						$allocQty = $po_wise_alloc_qty_array[$poId]['alloc_qty'];

						if(strtotime($today) <= strtotime($startDate->format('d-m-Y')))
						{
							$opening_backlog_qty += 0 - $allocQty;
							 //echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($endDate->format('d-m-Y')) )
						{
							$opening_backlog_qty += $budgetQty - $allocQty;
							// echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($startDate->format('d-m-Y')) && strtotime($today) <= strtotime($endDate->format('d-m-Y'))) 
						{		        	
							$daysDif  = $endDate->diff($startDate)->format('%a')+1;

							$todayDaysDif= $startDate->diff($dateObj)->format('%a');

							$opening_backlog_qty += (($budgetQty/$daysDif)*$todayDaysDif) - $allocQty;
							// echo $comp."==".$count_id."==((".$budgetQty."/".$daysDif.")*".$todayDaysDif.") - ".$allocQty."<br>";
						}
						
						// ===================== getting to be deman =================================
						// $tna_date_range = create_date_range($tnaDateArray[$poId]['start_date'],$tnaDateArray[$poId]['end_date']);
						// print_r($tna_date_range);die();
						if($tnaDateArray[$poId]['start_date'] !="")
						{							
							if (strtotime($startDate->format('d-m-Y')) == strtotime($today) && strtotime($endDate->format('d-m-Y')) == strtotime($today)) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$tna_date_range = create_date_range($startDate->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) == strtotime($today) &&  strtotime($endDate->format('d-m-Y')) >= strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $futureDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$futureDate->format('d-m-Y'));
							}
							elseif(strtotime($startDate->format('d-m-Y')) == strtotime($today) && strtotime($endDate->format('d-m-Y')) < strtotime($futureDate->format('d-m-Y')))
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) > strtotime($today) && strtotime($endDate->format('d-m-Y')) == strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $futureDate->diff($startDate)->format('%a')+1;
								$tna_date_range = create_date_range($startDate->format('d-m-Y'),$futureDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) > strtotime($today) && strtotime($endDate->format('d-m-Y')) < strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$tna_date_range = create_date_range($startDate->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) < strtotime($today) && strtotime($endDate->format('d-m-Y')) >= strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $futureDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$futureDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) < strtotime($today) && strtotime($endDate->format('d-m-Y')) < strtotime($futureDate->format('d-m-Y')) && strtotime($endDate->format('d-m-Y')) > strtotime($today)) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($endDate->format('d-m-Y')) == strtotime($today)) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($endDate->format('d-m-Y')) < strtotime($today)) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= 0;
							}
							elseif (strtotime($startDate->format('d-m-Y')) > strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= 0;
							}
							elseif (strtotime($startDate->format('d-m-Y')) > strtotime($today) && strtotime($endDate->format('d-m-Y')) > strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $futureDate->diff($startDate)->format('%a')+1;
								$tna_date_range = create_date_range($startDate->format('d-m-Y'),$futureDate->format('d-m-Y'));
							}
							// $tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
							// $futerDaysDif  	= $futureDate->diff($startDate)->format('%a')+1;
							$will_be_allocate_pre_day = $budgetQty/$tnaDaysDif;
							$will_be_allocate = $will_be_allocate_pre_day * $futerDaysDif;
							foreach ($tna_date_range as $key => $val) 
							{
								$to_be_demand_array[$val] += $will_be_allocate_pre_day;
							}
							// $dataArray[$comp][$count_id]['to_be_demand'] += $will_be_allocate;
							//echo $poId."==".$comp."==".$count_id."==".$will_be_allocate_pre_day."*".$futerDaysDif."==".$budgetQty."/".$tnaDaysDif."<br>";
							
							$tot_will_be_allocate += $will_be_allocate;
							// echo $will_be_allocate."<br>";
						}
						
					}
				}
			}
		}
		
	}
	// echo $tot_will_be_allocate;
	// echo "<pre>";print_r($to_be_demand_array);
	/*==========================================================================================/
	/										get pipe line qty									/
	/==========================================================================================*/
	$sqlYarn = "SELECT c.booking_id as PI_ID, a.cons_quantity AS RCV_QTY	
	from inv_transaction a , inv_receive_master c, product_details_master b where a.mst_id=c.id and b.id=a.prod_id and a.transaction_type in(1) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id=$count_id and b.yarn_comp_type1st=$comp_id and b.item_category_id=1 and c.receive_basis=1";
	// echo $sqlYarn;die();
	$yarn_res = sql_select($sqlYarn);
	$yarnRcvQty = 0;
	$piWiseyarnRcvQtyArray = array();
	foreach ($yarn_res as $row) 
	{
        $yarnRcvQty += $row['RCV_QTY']; 
        $piWiseyarnRcvQtyArray[$row['PI_ID']]['rcv_qty'] += $row['RCV_QTY']; 
	}
	unset($yarn_res);
	// ==================================== yarn pi qty ================================
	$sql = "SELECT a.id as WO_ID,b.yarn_inhouse_date as DEL_START_DT,b.delivery_end_date as DEL_END_DT,d.QUANTITY,c.id as PI_ID from wo_non_order_info_mst a,wo_non_order_info_dtls b,com_pi_master_details c,com_pi_item_details d where a.id=b.mst_id and a.id=d.work_order_id and b.id=d.work_order_dtls_id and c.id=d.pi_id and a.entry_form=144 and c.item_category_id=1 and c.ref_closing_status=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.yarn_inhouse_date is not null and b.delivery_end_date is not null and a.company_name=$company_id and b.yarn_count=$count_id and b.yarn_comp_type1st=$comp_id ";
	// echo $sql;die();	
	$yarn_pi_qty_array = array();
	$sqlRes = sql_select($sql);
	foreach ($sqlRes as $row) 
	{
        $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']]['pi_qty'] += $row['QUANTITY']; 
        $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']]['del_start_dt'] = $row['DEL_START_DT']; 
        $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']]['del_end_dt'] = $row['DEL_END_DT']; 
	}
	unset($sqlRes);

	// ================================ calculate pipe line data ==============================	
	$counter = 1;
	$pipe_line_qty_array = array();
	$tot_pipe_line_qty  = 0;
	foreach ($yarn_pi_qty_array as $piId => $piData) 
	{
		foreach ($piData as $woId => $row) 
		{			
			$rcvQty = $piWiseyarnRcvQtyArray[$piId]['rcv_qty'];
			if($rcvQty < $row['pi_qty'])
			{
				$del_start_dt 	= new DateTime($row['del_start_dt']);
				$del_end_dt 	= new DateTime($row['del_end_dt']);
				$pipe_line_qty  = 0;
				
				$piQty 			= $row['pi_qty'];
				// $del_date_range = create_date_range($row['del_start_dt'],$row['del_end_dt']);
				if((strtotime($futureDate->format('d-m-Y')) >= strtotime($del_start_dt->format('d-m-Y'))) && (strtotime($futureDate->format('d-m-Y')) <= strtotime($del_end_dt->format('d-m-Y'))))
				{
					$woDaysDiff 	= $del_end_dt->diff($del_start_dt)->format('%a')+1;
					$daysDiff 		= $futureDate->diff($del_start_dt)->format('%a')+1;
					$rcvPerDay 		= $piQty/$woDaysDiff;
					$totRcv 		= $rcvPerDay * $daysDiff;							
					$pipe_line_qty 	= $totRcv - $rcvQty;
					$del_date_range = create_date_range($del_start_dt->format('d-m-Y'),$futureDate->format('d-m-Y'));
					$counter++;
				}
				elseif ((strtotime($futureDate->format('d-m-Y')) >= strtotime($del_start_dt->format('d-m-Y'))) && (strtotime($futureDate->format('d-m-Y')) > strtotime($del_end_dt->format('d-m-Y')))) 
				{
					$woDaysDiff 	= $del_end_dt->diff($del_start_dt)->format('%a')+1;
					$daysDiff 		= $futureDate->diff($del_start_dt)->format('%a')+1;
					$rcvPerDay 		= $piQty/$woDaysDiff;
					$totRcv 		= $rcvPerDay * $woDaysDiff;
					$pipe_line_qty 	= $totRcv - $rcvQty;
					$del_date_range = create_date_range($del_start_dt->format('d-m-Y'),$futureDate->format('d-m-Y'));
					$counter++;
					// echo "string2<br>".strtotime($del_end_dt->format('d-m-Y'));
				}
				$tot_pipe_line_qty  += $pipe_line_qty;
				// $dataArray[$comp][$countId]['pipe_line_qty'] += $pipe_line_qty;
				// echo $pipe_line_qty."==".$daysDiff."==".$rcvPerDay."<br>";

				foreach ($del_date_range as $key => $val) 
				{
						$pipe_line_qty_array[$val] += $rcvPerDay;					
				}
			}
			
		}
	}
	// echo $tot_pipe_line_qty."==".$counter;
	// echo "<pre>";print_r($pipe_line_qty_array);die;

	?>
	<div id="data_panel" align="center" style="width:600px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 600px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="600">
                <thead>
                    <tr>
                        <th width="100">Date</th>
                        <th width="100">Free Stock</th>
                        <th width="100">Opening Backlog</th>
                        <th width="100">To Be Demand</th>
                        <th width="100">Pipe Line</th>
                        <th width="100">Closing Backlog</th>
                    </tr>
                </thead>
            </table>
            <div style="width:620px;overflow-y:auto;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="600">
	                <tbody>
	                    <?
	                    $j=1;
	                    $non_order_rtnableQty = $non_order_returnable_qnty - $non_order_rcv_rtn_qty;
	                    $non_order_backlog = $non_order_booking_qty - $non_order_issue_qty;
	                    $opening_backlog_qty = $opening_backlog_qty+$non_order_backlog;
	                    $closing_backlog_array = array();
	                    $pipeLineQty = 0;
	                    $totPipeLineQty = 0;
	                    $tot_days = count($date_range_array);

	                    foreach ($date_range_array as $key => $date) 
	                    {
	                    	$pipeLineQty = ($tot_days==1) ? $tot_pipe_line_qty : $tot_pipe_line_qty - ($pipe_line_qty_array[$date]*($tot_days-1));
	                    	if(strtotime($current_date)==strtotime($date))
                        	{
                        	 	$totPipeLineQty = $pipeLineQty;
                        	}
                        	else
                        	{
                        	 	$totPipeLineQty = $pipe_line_qty_array[$date];
                        	}

	                    	// $opningBal = $yarn_rcv_qty - $yarn_issue_qty;
	                    	// $allcoStock = $alloc_qty - $yarn_issue_qty;
                      //   	$freeStock = ($opningBal - $allcoStock) + $non_order_rtnableQty;
                        	// $closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_array[$date] + $opening_backlog_qty);
                        	if($j==1)
	                    	{
	                    		$freeStock = $available_qnty + $non_order_rtnableQty;
	                    		$closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_array[$date] + $opening_backlog_qty);
	                    	}
	                    	else
	                    	{
	                    		$freeStock = 0;
	                    		$closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_array[$date] + $closing_backlog);
	                    		// echo $freeStock ."+". $totPipeLineQty.") - (".$to_be_demand_array[$date] ."+". $closing_backlog.")<br>";
	                    	}
                        	$closing_backlog_array[$key+1] = $closing_backlog;
                        	$j++;
                        }

	                    $i=1;
	                    $gr_opn_bklg_qty 	= 0;
	                    $gr_demand_qty 		= 0;
	                    $gr_pipeline_qty 	= 0;
	                    $gr_closing_qty 	= 0;
	                    $pipeLineQty = 0;
	                    $totPipeLineQty = 0;
	                    $toBeDemandQty = 0;
	                    $totToBeDemandQty = 0;
	                    foreach ($date_range_array as $key => $date) 
	                    { 
	                    	$pipeLineQty = ($tot_days==1) ? $tot_pipe_line_qty : $tot_pipe_line_qty - ($pipe_line_qty_array[$date]*($tot_days-1));
	                    	if(strtotime($current_date)==strtotime($date))
                        	{
                        	 	$totPipeLineQty = $pipeLineQty;
                        	}
                        	else
                        	{
                        	 	$totPipeLineQty = $pipe_line_qty_array[$date];
                        	}

                        	/*$toBeDemandQty = ($i==1) ? $tot_will_be_allocate : $tot_will_be_allocate - $to_be_demand_array[$date];
	                    	if(strtotime($current_date)==strtotime($date))
                        	{
                        	 	$totToBeDemandQty = $toBeDemandQty;
                        	}
                        	else
                        	{
                        	 	$totToBeDemandQty = $to_be_demand_array[$date];
                        	}*/

	                    	// $opningBal = $yarn_rcv_qty - $yarn_issue_qty;
	                    	// $allcoStock = $alloc_qty - $yarn_issue_qty;
                      //   	$freeStock = ($opningBal - $allcoStock) + $non_order_rtnableQty;
                        	// $closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_array[$date] + $opening_backlog_qty);
                        	if($i==1)
	                    	{
	                    		$freeStock = $available_qnty + $non_order_rtnableQty;
	                    		$closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_array[$date] + $opening_backlog_qty);
	                    	}
	                    	else
	                    	{
	                    		$freeStock = 0;
	                    		$closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_array[$date] + $closing_backlog_array[$key]);
	                    		// echo $closing_backlog ."= (".$freeStock ."+". $totPipeLineQty.") - (".$to_be_demand_array[$date] ."+". $closing_backlog_array[$key].")<br>";
	                    	}

                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
                            ?>                        
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
                            	                          
                                <td width="100" align="center"><? echo date('d-M',strtotime($date)); ?></td>
                                <td width="100" align="right">
                                	<? echo ($i==1) ? number_format($freeStock,0) : ''; ?>
                                </td>
                                <td width="100" align="right"><? echo ($i==1) ? number_format($opening_backlog_qty,0) : number_format($closing_backlog_array[$key],0); ?></td>
                                <td width="100" align="right"><? echo number_format($to_be_demand_array[$date],0);?></td>
                                <td width="100" align="">
                                	<?  echo ($i==1) ? "Under Development due to formula revised" : "";//echo number_format($totPipeLineQty,0);?>
                                </td>
                                <td width="100" align="right"><? echo number_format($closing_backlog,0);?></td>
                            </tr>
                            <?
                            $i++;
		                    
		                    $gr_opn_bklg_qty 	+= ($i==1) ? $opening_backlog_qty : $closing_backlog_array[$key];
		                    $gr_demand_qty 		+= $to_be_demand_array[$date];
		                    $gr_pipeline_qty 	+= $totPipeLineQty;
		                    $gr_closing_qty 	+= $closing_backlog;
	                                           
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="600">
            	<tfoot>
            		<tr>
            			<th width="100">Grand Total </th>
            			<th width="100"></th>
            			<th width="100"><? //echo number_format($gr_opn_bklg_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_demand_qty,0);?></th>
            			<th width="100"><? //echo number_format($gr_pipeline_qty,0);?></th>
            			<th width="100"><? //echo number_format($gr_closing_qty,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}

if($action=="open_closing_backlog_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_array = return_library_array("select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 and id=$count_id", "id", "yarn_count");
	$current_date = date("j-M-Y");
	// ===================================== date range custom function ===============================================
	function create_date_range($start_date, $end_date, $step = '+1 day', $output_format = 'd-m-Y' ) 
	{
	    $dates = array();
	    $current = strtotime($start_date);
	    $end_date = strtotime($end_date);

	    while( $current <= $end_date ) {

	        $dates[] = date($output_format, $current);
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}
	$date_range_array = create_date_range(date('d-m-Y'),$future_date);
	// echo "<pre>";print_r($date_range_array);die;
	/*==========================================================================================/
	/										main query 											/
	/==========================================================================================*/
	$sql="SELECT b.id as PO_ID from wo_po_details_master a, wo_po_break_down b,wo_pre_cost_fabric_cost_dtls c,wo_pre_cost_fab_yarn_cost_dtls d where a.id=b.job_id  and a.id=c.job_id and b.job_id=c.job_id and b.job_id=d.job_id  and c.job_id=d.job_id and a.id=d.job_id and c.id=d.fabric_cost_dtls_id and a.company_name='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.shiping_status in(1,2) and d.count_id = $count_id and d.copm_one_id=$comp_id";
	// echo $sql;die();
	$result=sql_select($sql);
	if(count($result)==0)
	{
		// echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';die();
	}
	$po_id_array = array();
	foreach($result as $row)
	{
		$po_id_array[$row['PO_ID']] = $row['PO_ID'];
	}
	unset($result);

	$poIds = implode(",", $po_id_array);
	$po_id_list_arr=array_chunk($po_id_array,999);
	$poCond = " and ";
	$p=1;
	foreach($po_id_list_arr as $poids)
    {
    	if($p==1) 
		{
			$poCond .="  ( b.po_break_down_id in(".implode(',',$poids).")"; 
		}
        else
        {
          $poCond .=" or b.po_break_down_id in(".implode(',',$poids).")";
      	}
        $p++;
    }
    $poCond .=")";
	/*==========================================================================================/
	/										yarn allocation										/
	/==========================================================================================*/
	$sql_alloc = "SELECT b.po_break_down_id as PO_ID,b.QNTY from inv_material_allocation_mst a,inv_material_allocation_dtls b, product_details_master c where a.id=b.mst_id and b.item_id=c.id and b.item_category=1 and c.item_category_id=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$comp_id and c.company_id=$company_id";
	// echo $sql_alloc;die();
	$alloc_res = sql_select($sql_alloc);
    $po_wise_alloc_qty_array = array();
    $alloc_qty = 0;
	foreach ($alloc_res as $row) 
	{
        $alloc_qty += $row['QNTY']; 
        $po_wise_alloc_qty_array[$row['PO_ID']]['alloc_qty'] += $row['QNTY']; 
	}
	unset($alloc_res);
	/*==========================================================================================/
	/										yarn transaction 											/
	/==========================================================================================*/
	$sql_trns = "SELECT b.available_qnty as QTY
    from  product_details_master b
    where b.is_deleted=0  and b.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1";
    // echo $sql_trns;die();
    $trns_res = sql_select($sql_trns);
    $available_qnty = 0;
	foreach ($trns_res as $row) 
	{
		$available_qnty += $row['QTY'];
	}
	unset($sql_trns);
	/*==========================================================================================/
	/										get tna data										/
	/==========================================================================================*/
	$tnaPoCond = str_replace("b.po_break_down_id", "a.po_number_id", $poCond);
	$sqltna = "SELECT A.PO_NUMBER_ID,A.TASK_NUMBER,MAX(A.TASK_START_DATE) AS START_DATE,MAX(A.TASK_FINISH_DATE) AS END_DATE,MAX(A.ACTUAL_START_DATE) AS ACTUAL_START_DATE,MAX(A.ACTUAL_FINISH_DATE) AS ACTUAL_FINISH_DATE FROM TNA_PROCESS_MST A,WO_PO_BREAK_DOWN B WHERE A.PO_NUMBER_ID=B.ID AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  and A.TASK_START_DATE is not null AND B.PO_QUANTITY>0 AND A.TASK_TYPE=1 and a.TASK_NUMBER=48 $tnaPoCond group by A.PO_NUMBER_ID,A.TASK_NUMBER";
	// echo $sqltna;die();
	$tnaRes = sql_select($sqltna);
    $tnaDateArray = array();
    foreach ($tnaRes as $val) 
    {
        $tnaDateArray[$val['PO_NUMBER_ID']]['start_date'] = $val['START_DATE'];
        $tnaDateArray[$val['PO_NUMBER_ID']]['end_date']   = $val['END_DATE'];
    }
    unset($tnaRes);

    /*==========================================================================================/
	/				get yarn returnable qty when issue qty from smple without order				/
	/==========================================================================================*/
	$sqlIssue = "SELECT a.return_qnty as QTY
    from inv_transaction a, inv_issue_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and d.item_category=2 and d.booking_type=4 and c.issue_basis=1 and c.issue_purpose=8 and a.transaction_type=2 and a.return_qnty>0";
    // echo $sqlIssue;die();
    $issueRes = sql_select($sqlIssue);
    $non_order_returnable_qnty=0;
	foreach ($issueRes as $row) 
	{
        $non_order_returnable_qnty += $row['QTY']; 
	}
	unset($issueRes);

	/*==========================================================================================/
	/					yarn issue rtn qty from smple without order								/
	/==========================================================================================*/
	$sqlRcvRtn = "SELECT a.cons_quantity as QTY
    from inv_transaction a, inv_receive_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and c.entry_form=9 and d.item_category=2 and d.booking_type=4 and c.receive_basis=1 and c.booking_without_order=1 and a.transaction_type=4";
    // echo $sqlRcvRtn;die();
    $receiveRes = sql_select($sqlRcvRtn);
    $non_order_rcv_rtn_qty =0;
	foreach ($receiveRes as $row) 
	{
        	$non_order_rcv_rtn_qty += $row['QTY']; 
	}
	unset($issueRes);

	/*==========================================================================================/
	/						get bookikg qty from smple without order							/
	/==========================================================================================*/
    $sql_smn = "SELECT a.BOOKING_NO,a.BUYER_ID,b.GREY_FABRIC from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b,lib_yarn_count_determina_mst c, lib_yarn_count_determina_dtls d where a.booking_no=b.booking_no and a.booking_type=4 and a.item_category=2 and a.entry_form_id=140 and b.lib_yarn_count_deter_id > 0 and c.id=d.mst_id and c.id=b.lib_yarn_count_deter_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.copmposition_id=$comp_id and d.count_id=$count_id and a.company_id=$company_id and a.is_approved=1";
	// echo $sql_smn;die();
	$smnRes = sql_select($sql_smn);
	$non_order_booking_qty = 0;
	foreach ($smnRes as $val) 
	{
		$non_order_booking_qty += $val['GREY_FABRIC'];
	}
	/*==========================================================================================/
	/							get issue qty from smple without order							/
	/==========================================================================================*/
	$sqlIssue = "SELECT d.BOOKING_NO,d.BUYER_ID,a.cons_quantity as QTY
    from inv_transaction a, inv_issue_master c,product_details_master b,wo_non_ord_samp_booking_mst d
    where a.mst_id=c.id and a.prod_id=b.id and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id =$count_id and b.yarn_comp_type1st =$comp_id and b.item_category_id=1 and a.transaction_date<='$current_date' and d.id=c.booking_id and d.entry_form_id=140 and d.item_category=2 and d.booking_type=4 and c.issue_basis=1 and c.issue_purpose=8 and a.transaction_type=2 and d.is_approved=1";
    // echo $sqlIssue;die();
    $issueRes = sql_select($sqlIssue);
    $non_order_issue_qty = 0;
	foreach ($issueRes as $row) 
	{
        $non_order_issue_qty += $row['QTY']; 
	}
	unset($issueRes);

	// ========================= to be demand ======================
	$sql = "SELECT a.PLAN_DATE,(case when a.plan_date <= '$current_date' then a.PLAN_QTY else 0 end) as all_plan_qty,(case when a.plan_date between '$current_date' and '$future_date' then a.PLAN_QTY else 0 end) as PLAN_QTY from TNA_PLAN_TARGET a,wo_po_break_down b,wo_po_details_master c where a.po_id=b.id and b.job_id=c.id and c.company_name=$company_id and a.TASK_ID=48 and a.status_Active=1 and a.is_deleted=0 and b.status_Active=1 and b.is_deleted=0 and b.shiping_status!=3 and a.PLAN_QTY>0 and a.COUNT_ID=$count_id and a.COMPOSITION_ID=$comp_id";
	// echo $sql;die;
	$res = sql_select($sql);
	$to_be_demand_data = array();
	foreach ($res as $v) 
	{
		$date = date('d-m-Y',strtotime($v['PLAN_DATE']));
		$to_be_demand_data[$date]['plan_qty'] += $v['PLAN_QTY'];
		$to_be_demand_data[$date]['all_plan_qty'] += $v['ALL_PLAN_QTY'];
		
	}
	// echo "<pre>";print_r($to_be_demand_data);die();

    /*==========================================================================================/
	/										yarn req. qty										/
	/==========================================================================================*/
	$condition= new condition();     
    $condition->po_id_in($poIds);     
    $condition->init();

    $yarn= new yarn($condition);     
    $yarnReqQtyArr=$yarn->getOrderCountAndCompositionWiseYarnQtyArray();
    /*$yarnReqQtyArray = array();
    foreach ($po_id_array as $po => $val) 
    {
    	$yarnReqQtyArray[$po] = $yarnReqQtyArr[$po][$count_id][$comp_id];
    }
    echo "<pre>";print_r($yarnReqQtyArray);die('ok');*/
    
	/*==========================================================================================/
	/							get yarn deman and opening backlog								/
	/==========================================================================================*/
	//echo "<pre>";print_r($yarnReqQtyArr);die('ok');
	$dateObj = new DateTime('today');
	$today = $dateObj->format('d-m-Y');	
	$futerDate = date('d-m-Y',strtotime($future_date));
	$futureDate = new DateTime($futerDate);
	$opening_backlog_qty = 0;
	$tot_will_be_allocate = 0;
	$to_be_demand_array = array();
	foreach ($yarnReqQtyArr as $poId => $poData) 
	{
		foreach ($poData as $countId => $countData) 
		{
			foreach ($countData as $compId => $row) 
			{
				if($countId==$count_id && $compId==$comp_id)
				{
					if($tnaDateArray[$poId]['start_date'] !="" && $tnaDateArray[$poId]['end_date'] !="")
					{
						$startDate = new DateTime($tnaDateArray[$poId]['start_date']);
						$endDate   = new DateTime($tnaDateArray[$poId]['end_date']);
						
						$budgetQty = $row;
						$allocQty = 0;
						$allocQty = $po_wise_alloc_qty_array[$poId]['alloc_qty'];

						if(strtotime($today) <= strtotime($startDate->format('d-m-Y')))
						{
							$opening_backlog_qty += 0 - $allocQty;
							 //echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($endDate->format('d-m-Y')) )
						{
							$opening_backlog_qty += $budgetQty - $allocQty;
							// echo $poId."==".$comp."==".$count_id."==(".$budgetQty."-".$allocQty.")<br>";
						}
						elseif (strtotime($today) > strtotime($startDate->format('d-m-Y')) && strtotime($today) <= strtotime($endDate->format('d-m-Y'))) 
						{		        	
							$daysDif  = $endDate->diff($startDate)->format('%a')+1;

							$todayDaysDif= $startDate->diff($dateObj)->format('%a');

							$opening_backlog_qty += (($budgetQty/$daysDif)*$todayDaysDif) - $allocQty;
							// echo $comp."==".$count_id."==((".$budgetQty."/".$daysDif.")*".$todayDaysDif.") - ".$allocQty."<br>";
						}
						
						// ===================== getting to be deman =================================
						// $tna_date_range = create_date_range($tnaDateArray[$poId]['start_date'],$tnaDateArray[$poId]['end_date']);
						// print_r($tna_date_range);die();
						if($tnaDateArray[$poId]['start_date'] !="")
						{							
							if (strtotime($startDate->format('d-m-Y')) == strtotime($today) && strtotime($endDate->format('d-m-Y')) == strtotime($today)) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$tna_date_range = create_date_range($startDate->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) == strtotime($today) &&  strtotime($endDate->format('d-m-Y')) >= strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $futureDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$futureDate->format('d-m-Y'));
							}
							elseif(strtotime($startDate->format('d-m-Y')) == strtotime($today) && strtotime($endDate->format('d-m-Y')) < strtotime($futureDate->format('d-m-Y')))
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) > strtotime($today) && strtotime($endDate->format('d-m-Y')) == strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $futureDate->diff($startDate)->format('%a')+1;
								$tna_date_range = create_date_range($startDate->format('d-m-Y'),$futureDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) > strtotime($today) && strtotime($endDate->format('d-m-Y')) < strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$tna_date_range = create_date_range($startDate->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) < strtotime($today) && strtotime($endDate->format('d-m-Y')) >= strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $futureDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$futureDate->format('d-m-Y'));
							}
							elseif (strtotime($startDate->format('d-m-Y')) < strtotime($today) && strtotime($endDate->format('d-m-Y')) < strtotime($futureDate->format('d-m-Y')) && strtotime($endDate->format('d-m-Y')) > strtotime($today)) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($endDate->format('d-m-Y')) == strtotime($today)) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $endDate->diff($dateObj)->format('%a')+1;
								$tna_date_range = create_date_range($dateObj->format('d-m-Y'),$endDate->format('d-m-Y'));
							}
							elseif (strtotime($endDate->format('d-m-Y')) < strtotime($today)) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= 0;
							}
							elseif (strtotime($startDate->format('d-m-Y')) > strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= 0;
							}
							elseif (strtotime($startDate->format('d-m-Y')) > strtotime($today) && strtotime($endDate->format('d-m-Y')) > strtotime($futureDate->format('d-m-Y'))) 
							{
								$tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
								$futerDaysDif  	= $futureDate->diff($startDate)->format('%a')+1;
								$tna_date_range = create_date_range($startDate->format('d-m-Y'),$futureDate->format('d-m-Y'));
							}
							// $tnaDaysDif  	= $endDate->diff($startDate)->format('%a')+1;
							// $futerDaysDif  	= $futureDate->diff($startDate)->format('%a')+1;
							$will_be_allocate_pre_day = $budgetQty/$tnaDaysDif;
							$will_be_allocate = $will_be_allocate_pre_day * $futerDaysDif;
							if($futerDaysDif>0)
							{
								foreach ($tna_date_range as $key => $val) 
								{
									$to_be_demand_array[$val] += $will_be_allocate_pre_day;
								}
							}
							// $dataArray[$comp][$count_id]['to_be_demand'] += $will_be_allocate;
							//echo $poId."==".$compId."==".$count_id."==".$will_be_allocate_pre_day."*".$futerDaysDif."==".$budgetQty."/".$tnaDaysDif."<br>";
							
							$tot_will_be_allocate += $will_be_allocate;
							// echo $will_be_allocate."<br>";
						}
						
					}
				}
			}
		}
		
	}
	// echo $tot_will_be_allocate;
	// echo "<pre>";print_r($to_be_demand_array);die;

	/*==========================================================================================/
	/										get pipe line qty									/
	/==========================================================================================*/
	$sqlYarn = "SELECT c.booking_id as PI_ID, a.cons_quantity AS RCV_QTY	
	from inv_transaction a , inv_receive_master c, product_details_master b where a.mst_id=c.id and b.id=a.prod_id and a.transaction_type in(1) and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.company_id=$company_id and b.yarn_count_id=$count_id and b.yarn_comp_type1st=$comp_id and b.item_category_id=1 and c.receive_basis=1";
	// echo $sqlYarn;die();
	$yarn_res = sql_select($sqlYarn);
	$yarnRcvQty = 0;
	$piWiseyarnRcvQtyArray = array();
	foreach ($yarn_res as $row) 
	{
        $yarnRcvQty += $row['RCV_QTY']; 
        $piWiseyarnRcvQtyArray[$row['PI_ID']]['rcv_qty'] += $row['RCV_QTY']; 
	}
	unset($yarn_res);
	// ==================================== yarn pi qty ================================
	$sql = "SELECT a.id as WO_ID,b.yarn_inhouse_date as DEL_START_DT,b.delivery_end_date as DEL_END_DT,d.QUANTITY,c.id as PI_ID from wo_non_order_info_mst a,wo_non_order_info_dtls b,com_pi_master_details c,com_pi_item_details d where a.id=b.mst_id and a.id=d.work_order_id and b.id=d.work_order_dtls_id and c.id=d.pi_id and a.entry_form=144 and c.item_category_id=1 and c.ref_closing_status=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and b.yarn_inhouse_date is not null and b.delivery_end_date is not null and a.company_name=$company_id and b.yarn_count=$count_id and b.yarn_comp_type1st=$comp_id ";
	// echo $sql;die();	
	$yarn_pi_qty_array = array();
	$sqlRes = sql_select($sql);
	foreach ($sqlRes as $row) 
	{
        $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']]['pi_qty'] += $row['QUANTITY']; 
        $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']]['del_start_dt'] = $row['DEL_START_DT']; 
        $yarn_pi_qty_array[$row['PI_ID']][$row['WO_ID']]['del_end_dt'] = $row['DEL_END_DT']; 
	}
	unset($sqlRes);

	// ================================ calculate pipe line data ==============================	
	$counter = 1;
	$pipe_line_qty_array = array();
	$pipe_line_qty_till_today = 0;
	$tot_pipe_line_qty  = 0;
	$total_rcv_qty = 0;
	foreach ($yarn_pi_qty_array as $piId => $piData) 
	{
		foreach ($piData as $woId => $row) 
		{	
			$rcvQty = 0;		
			$rcvQty = $piWiseyarnRcvQtyArray[$piId]['rcv_qty'];
			if($rcvQty < $row['pi_qty'])
			{
				$del_start_dt 	= new DateTime($row['del_start_dt']);
				$del_end_dt 	= new DateTime($row['del_end_dt']);
				$pipe_line_qty  = 0;
				
				$piQty 			= $row['pi_qty'];
				// $del_date_range = create_date_range($row['del_start_dt'],$row['del_end_dt']);
				if((strtotime($futureDate->format('d-m-Y')) >= strtotime($del_start_dt->format('d-m-Y'))) && (strtotime($futureDate->format('d-m-Y')) <= strtotime($del_end_dt->format('d-m-Y'))))
				{
					$woDaysDiff 	= $del_end_dt->diff($del_start_dt)->format('%a')+1;
					$daysDiff 		= $futureDate->diff($del_start_dt)->format('%a')+1;
					$rcvPerDay 		= $piQty/$woDaysDiff;
					$totRcv 		= $rcvPerDay * $daysDiff;							
					$pipe_line_qty 	= $totRcv - $rcvQty;
					$del_date_range = create_date_range($del_start_dt->format('d-m-Y'),$futureDate->format('d-m-Y'));
					$counter++;
				}
				elseif ((strtotime($futureDate->format('d-m-Y')) > strtotime($del_start_dt->format('d-m-Y'))) && (strtotime($futureDate->format('d-m-Y')) > strtotime($del_end_dt->format('d-m-Y')))) 
				{
					$woDaysDiff 	= $del_end_dt->diff($del_start_dt)->format('%a')+1;
					$daysDiff 		= $del_end_dt->diff($del_start_dt)->format('%a')+1;
					$rcvPerDay 		= $piQty/$woDaysDiff;
					$totRcv 		= $rcvPerDay * $woDaysDiff;
					$pipe_line_qty 	= $totRcv - $rcvQty;
					$del_date_range = create_date_range($del_start_dt->format('d-m-Y'),$del_end_dt->format('d-m-Y'));
					$counter++;
					// echo "string2<br>".strtotime($del_end_dt->format('d-m-Y'));
				}
				$tot_pipe_line_qty  += $pipe_line_qty;
				// $dataArray[$comp][$countId]['pipe_line_qty'] += $pipe_line_qty;
				// echo $rcvQty."==".$daysDiff."==".$rcvPerDay."==".$del_start_dt->format('d-m-Y')."==".$del_end_dt->format('d-m-Y')."<br>";
				$k = 1;
				foreach ($del_date_range as $key => $val) 
				{
					if(strtotime($val) <= strtotime($today))
					{
						$pipe_line_qty_till_today += $rcvPerDay;
					}
					$pipe_line_qty_array[$val] += $rcvPerDay;		
					$k++;			
				}
				$total_rcv_qty += $rcvQty;
				$pipe_line_qty_till_today = $pipe_line_qty_till_today - $rcvQty;
			}
			
		}
	}
	// echo $tot_pipe_line_qty."==".$pipe_line_qty_till_today;
	// echo "<pre>";print_r($pipe_line_qty_array);die;

	$sqlBtb = "SELECT a.id as delv_plan_id, d.id as wo_id, c.id, f.id as PI_ID,f.SUPPLIER_ID,a.delivery_plan_date as DEL_START_DT,c.QUANTITY,(case when a.delivery_plan_date between '$current_date' and '$future_date' THEN a.PLAN_QNTY ELSE 0 END) as PLAN_QTY,(case when a.delivery_plan_date <= '$current_date' THEN a.PLAN_QNTY ELSE 0 END) as ALL_PLAN_QTY,f.ref_closing_status from com_pi_item_details c,wo_non_order_info_mst d,com_pi_master_details f,wo_non_order_info_dtls e ,wo_non_order_info_dtls_plan a  where c.work_order_id=d.id and d.id=e.mst_id and f.id=c.pi_id and e.id=a.po_dtls_id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.yarn_count=$count_id and e.yarn_comp_type1st=$comp_id and c.count_name=$count_id  and c.yarn_composition_item1=$comp_id and d.company_name=$company_id  and d.entry_form=144 ";//and f.ref_closing_status=0
	// echo $sqlBtb;die();
	$btbRes = sql_select($sqlBtb);
	$piWisedataArray = array();
	$woIdArray = array();
	$chkArray2 = array();
	foreach ($btbRes as $val) 
	{
		$date = date('d-m-Y',strtotime($val['DEL_START_DT']));
		if($val['REF_CLOSING_STATUS']==0)
		{
			if($chkArray2[$val['DELV_PLAN_ID']][$val['DEL_START_DT']]=="") // omit duplicate plan qty
			{
				$piWisedataArray[$date]['plan_qty'] += $val['PLAN_QTY'];
			}
		}
		$woIdArray[$val['WO_ID']] = $val['WO_ID'];
	}
	unset($btbRes);
	// echo "<pre>";print_r($piWisedataArray);die();
	// ====================================== wo qty =====================================
	if(count($woIdArray)>0)
	{
		$wo_id_cond = wherenot_con_using_array($woIdArray,0,"d.id");
	}
	$sqlBtb = "SELECT d.id as pi_id, d.WO_NUMBER,d.WO_DATE,d.SUPPLIER_ID,a.delivery_plan_date as DEL_START_DT,a.PLAN_QNTY as QUANTITY,(case when a.delivery_plan_date between '$current_date' and '$future_date' THEN a.PLAN_QNTY ELSE 0 END) as PLAN_QTY,(case when a.delivery_plan_date <= '$current_date' THEN a.PLAN_QNTY ELSE 0 END) as ALL_PLAN_QTY from wo_non_order_info_mst d,wo_non_order_info_dtls e , wo_non_order_info_dtls_plan a where d.id=e.mst_id and e.id=a.po_dtls_id and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.yarn_count=$count_id and e.yarn_comp_type1st=$comp_id and e.YARN_COUNT=$count_id  and e.YARN_COMP_TYPE1ST=$comp_id and d.company_name=$company_id and d.entry_form=144 $wo_id_cond";
	// echo $sqlBtb;die();
	$btbRes = sql_select($sqlBtb);
	foreach ($btbRes as $v) 
	{
		$date = date('d-m-Y',strtotime($v['DEL_START_DT']));
		$piWisedataArray[$date]['plan_qty'] += $v['PLAN_QTY'];
	}
	unset($btbRes);

	// echo "<pre>";print_r($piWisedataArray);die();

	?>
	<div id="data_panel" align="center" style="width:600px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 600px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="600">
                <thead>
                    <tr>
                        <th width="100">Date</th>
                        <th width="100">Free Stock</th>
                        <th width="100">Opening Backlog</th>
                        <th width="100">To Be Demand</th>
                        <!-- <th width="100">Pipe Line</th> -->
                        <th width="100">Receivable</th>
                        <th width="100">Closing Backlog</th>
                    </tr>
                </thead>
            </table>
            <div style="width:620px;overflow-y:auto;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="600">
	                <tbody>
	                    <?
	                    $j=1;
	                    $non_order_rtnableQty = $non_order_returnable_qnty - $non_order_rcv_rtn_qty;
	                    $non_order_backlog = $non_order_booking_qty - $non_order_issue_qty;
	                    $opening_backlog_qty = $opening_backlog_qty+$non_order_backlog;
	                    $closing_backlog_array = array();
	                    $pipeLineQty = 0;
	                    $totPipeLineQty = 0;
	                    $tot_days = count($date_range_array);

	                    foreach ($date_range_array as $key => $date) 
	                    {
	                    	// $pipeLineQty = ($tot_days==1) ? $tot_pipe_line_qty : $tot_pipe_line_qty - ($pipe_line_qty_array[$date]*($tot_days-1));
	                    	$pipeLineQty = ($j==1) ? $pipe_line_qty_till_today : $pipe_line_qty_array[$date];
	                    	if(strtotime($current_date)==strtotime($date))
                        	{
                        	 	// $totPipeLineQty = $pipeLineQty;
                        	 	$totPipeLineQty = $piWisedataArray[$date]['plan_qty'];
                        	}
                        	else
                        	{
                        	 	$totPipeLineQty = $piWisedataArray[$date]['plan_qty'];
                        	}

	                    	// $opningBal = $yarn_rcv_qty - $yarn_issue_qty;
	                    	// $allcoStock = $alloc_qty - $yarn_issue_qty;
                      //   	$freeStock = ($opningBal - $allcoStock) + $non_order_rtnableQty;
                        	// $closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_array[$date] + $opening_backlog_qty);
                        	if($j==1)
	                    	{
	                    		$freeStock = $available_qnty;
	                    		// $closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_data[$date]['plan_qty'] + $opening_backlog_qty);
								$closing_backlog = ($freeStock + $totPipeLineQty) - ($opening_backlog_qty + $to_be_demand_data[$date]['plan_qty']);
								// echo $i."=".$closing_backlog ."= (".$opening_backlog_qty ."+". $to_be_demand_data[$date]['plan_qty'].") - (".$freeStock ."+". $totPipeLineQty.")<br>";
	                    	}
	                    	else
	                    	{
	                    		$freeStock = 0;
	                    		// $closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_data[$date]['plan_qty'] + $closing_backlog);
								$closing_backlog = ($freeStock + $totPipeLineQty) - ($closing_backlog + $to_be_demand_data[$date]['plan_qty']) ;

	                    		// echo $i."=".$closing_backlog ."= (".$closing_backlog ."+". $to_be_demand_data[$date]['plan_qty'].") - (".$freeStock ."+". $totPipeLineQty.")<br>";
	                    	}
                        	$closing_backlog_array[$key+1] = $closing_backlog;
                        	$j++;
                        }
						// echo "<pre>";print_r($closing_backlog_array);die;
	                    $i=1;
	                    $gr_opn_bklg_qty 	= 0;
	                    $gr_demand_qty 		= 0;
	                    $gr_pipeline_qty 	= 0;
	                    $gr_closing_qty 	= 0;
	                    $pipeLineQty = 0;
	                    $totPipeLineQty = 0;
	                    $toBeDemandQty = 0;
	                    $totToBeDemandQty = 0;
	                    foreach ($date_range_array as $key => $date) 
	                    { 
	                    	// $pipeLineQty = ($tot_days==1) ? $tot_pipe_line_qty : $tot_pipe_line_qty - ($pipe_line_qty_array[$date]*($tot_days-1));
	                    	$pipeLineQty = ($i==1) ? $pipe_line_qty_till_today : $pipe_line_qty_array[$date];
	                    	/* if(strtotime($current_date)==strtotime($date))
                        	{
                        	 	$totPipeLineQty = $pipeLineQty;
                        	}
                        	else
                        	{
                        	 	$totPipeLineQty = $pipe_line_qty_array[$date];
                        	} */
							$totPipeLineQty = $piWisedataArray[$date]['plan_qty'];

                        	/*$toBeDemandQty = ($i==1) ? $tot_will_be_allocate : $tot_will_be_allocate - $to_be_demand_array[$date];
	                    	if(strtotime($current_date)==strtotime($date))
                        	{
                        	 	$totToBeDemandQty = $toBeDemandQty;
                        	}
                        	else
                        	{
                        	 	$totToBeDemandQty = $to_be_demand_array[$date];
                        	}*/

	                    	// $opningBal = $yarn_rcv_qty - $yarn_issue_qty;
	                    	// $allcoStock = $alloc_qty - $yarn_issue_qty;
                      //   	$freeStock = ($opningBal - $allcoStock) + $non_order_rtnableQty;
                        	// $closing_backlog = ($freeStock + $totPipeLineQty) - ($to_be_demand_array[$date] + $opening_backlog_qty);
                        	if($i==1)
	                    	{
	                    		// $freeStock = $available_qnty + $non_order_rtnableQty;
	                    		$freeStock = $available_qnty;// + $non_order_rtnableQty;
	                    		// echo $available_qnty ."+". $non_order_rtnableQty."<br>";
	                    		// $closing_backlog = ($to_be_demand_array[$date] + $opening_backlog_qty)-($freeStock + $totPipeLineQty) ;

								$closing_backlog = ($freeStock + $totPipeLineQty) - ($opening_backlog_qty + $to_be_demand_data[$date]['plan_qty']);

								// echo $i."=".$closing_backlog ."= (".$opening_backlog_qty ."+". $to_be_demand_data[$date]['plan_qty'].") - (".$freeStock."+". $totPipeLineQty.")<br>";
	                    	}
	                    	else
	                    	{
	                    		$freeStock = 0;
	                    		// $closing_backlog = ($to_be_demand_array[$date] + $closing_backlog_array[$key]) - ($freeStock + $totPipeLineQty) ;

								$closing_backlog = ($closing_backlog_array[$key] + $to_be_demand_data[$date]['plan_qty']) - ($freeStock + $totPipeLineQty);

	                    		// echo $i."=".$closing_backlog ."= (".$closing_backlog_array[$key] ."+". $to_be_demand_data[$date]['plan_qty'].") - (".$freeStock ."+". $totPipeLineQty.")<br>";
	                    	}
							// echo "(".$freeStock ."+". $totPipeLineQty ."+". $retun_abl_qty.") - (".$opening_backlog_qty ."+". $to_be_demand_data[$date].")<br>";
							

                            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
                            ?>                        
                            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
                            	                          
                                <td width="100" align="center"><? echo date('d-M',strtotime($date)); ?></td>
                                <td width="100" align="right">
                                	<? echo ($i==1) ? number_format($freeStock,0) : ''; ?>
                                </td>
                                <td width="100" align="right"><? echo ($i==1) ? number_format($opening_backlog_qty,0) : number_format($closing_backlog_array[$key],0); ?></td>
                                <td width="100" align="right"><? echo number_format($to_be_demand_data[$date]['plan_qty'],0);?></td>
                                <td width="100" align="right">
                                	<? echo number_format($totPipeLineQty,0);?>
                                </td>
                                <td width="100" align="right"><? echo number_format($closing_backlog,0);?></td>
                            </tr>
                            <?
                            $i++;
		                    
		                    $gr_opn_bklg_qty 	+= ($i==1) ? $opening_backlog_qty : $closing_backlog_array[$key];
		                    $gr_demand_qty 		+= $to_be_demand_data[$date]['plan_qty'];
		                    $gr_pipeline_qty 	+= $totPipeLineQty;
		                    $gr_closing_qty 	+= $closing_backlog;
	                                           
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="600">
            	<tfoot>
            		<tr>
            			<th width="100">Grand Total </th>
            			<th width="100"></th>
            			<th width="100"><? //echo number_format($gr_opn_bklg_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_demand_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_pipeline_qty,0);?></th>
            			<th width="100"><? //echo number_format($gr_closing_qty,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}


if($action=="today_allocation_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");

	?>
	<div id="data_panel" align="center" style="width:800px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 800px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
                <thead>
                    <tr>
                        <th width="100">Buyer</th>
                        <th width="100">Season</th>
                        <th width="100">Ref No</th>
                        <th width="100">Job Req qty</th>
                        <th width="100">Excess Qty</th>
                        <th width="100">Alloc Qty</th>
                        <th width="100">Today Alloc</th>
                        <th width="100">Alloc Bal</th>
                    </tr>
                </thead>
            </table>
            <div style="width:820px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
	                <tbody>
	                    <?
	                    $i=0;
	                    $gr_req_qty 	= 0;
	                    $gr_excs_qty 	= 0;
	                    $gr_allo_qty 	= 0;
	                    $gr_allo_bal 	= 0;
	                    $gr_upto_allo_qty = 0;
	                    foreach ($dataArray as $buyer_id => $buyerData) 
	                    {
	                    	$r=0;
		                    $byr_req_qty 	= 0;
		                    $byr_excs_qty 	= 0;
		                    $byr_allo_qty 	= 0;
		                    $byr_allo_bal 	= 0;
		                    $byr_upto_allo_qty = 0;
							// ksort($buyerData);
	                        foreach ($buyerData as $seasonId => $seasonData) 
							{
								$z=0;
								ksort($seasonData);
								foreach ($seasonData as $int_ref => $row) 
								{
									$excess_qty = $excess_qty_array[$buyer_id][$seasonId][$int_ref]['excess_qty'];
									$alloc_qty = $alloc_qty_array[$buyer_id][$seasonId][$int_ref]['alloc_qty'];
									if($row['req_qty']>0 || $excess_qty>0 || $alloc_qty>0 || $row['issue']>0 || $row['smn_req_qty']>0) 
									{ 
										// $smn_backlog = $smn_backlog_qty[$buyer_id][$int_ref];
										$smn_backlog = 0;
										$smn_backlog = $row['smn_req_qty'] - $row['issue'];
										// echo $int_ref.'=='.$smn_backlog."==".$row['req_qty'] ."-". $row['issue']."<br>";
										$alloc_bal = $row['req_qty']+$row['smn_req_qty'] + $excess_qty - $alloc_qty;
										$opening_bklg = $row['opening_bklg'];
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
										?>                        
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
											<? if($r==0){?>                            
											<td title="<? echo $buyer_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$buyer_id];?>"><? echo $buyer_array[$buyer_id]; ?></td>
											<? }?>
											<? if($z==0){?>                            
											<td width="100" valign="middle" rowspan="<? echo $rowspanSeason[$buyer_id][$seasonId];?>"><? echo $season_array[$seasonId]; ?></td>
											<? }?>
											<td width="100"><? echo $int_ref; ?></td>
											<td width="100" align="right"><? echo number_format(($row['req_qty']+$row['smn_req_qty']),0);?></td>
											<td width="100" align="right"><? echo number_format($excess_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($a,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_bal,0);?></td>
										</tr>
										<?
										$i++;
										$r++;
										$z++;
										$byr_req_qty 	+= $row['req_qty']+$row['smn_req_qty'];
										$byr_excs_qty 	+= $excess_qty;
										$byr_allo_qty 	+= $alloc_qty;
										$byr_allo_bal 	+= $alloc_bal;
										$byr_opening_bklg += $opening_bklg+$smn_backlog;
		
										$gr_req_qty 	+= $row['req_qty']+$row['smn_req_qty'];
										$gr_excs_qty 	+= $excess_qty;
										$gr_allo_qty 	+= $alloc_qty;
										$gr_allo_bal 	+= $alloc_bal;
										$gr_opening_bklg += $opening_bklg+$smn_backlog;
									}
								}
	                        }
	                        ?>
	                        <tr style="font-size:12px;font-weight:bold;background:#ccc;">
	                        	<td colspan="3" align="right">Buyer Total</td>
	                        	<td align="right"><? echo number_format($byr_req_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_excs_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_allo_qty,0);?></td>
	                        	<td align="right"><? echo number_format($a,0);?></td>
	                        	<td align="right"><? echo number_format($byr_allo_bal,0);?></td>
	                        </tr>
	                        <?                        
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="100"><? echo number_format($gr_req_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_excs_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_qty,0);?></th>
            			<th width="100"><? echo number_format($a,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_bal,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}


if($action=="today_yarn_rcv_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");

	?>
	<div id="data_panel" align="center" style="width:800px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 800px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
                <thead>
                    <tr>
                        <th width="100">Supplier Name</th>
                        <th width="100">L/C NO</th>
                        <th width="100">L/C Date</th>
                        <th width="100">L/C qty</th>
                        <th width="100">Rcv Qty</th>
                        <th width="100">Today Rcv</th>
                        <th width="100">Bal Qty</th>
                    </tr>
                </thead>
            </table>
            <div style="width:820px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
	                <tbody>
	                    <?
	                    $i=0;
	                    $gr_req_qty 	= 0;
	                    $gr_excs_qty 	= 0;
	                    $gr_allo_qty 	= 0;
	                    $gr_allo_bal 	= 0;
	                    $gr_upto_allo_qty = 0;
	                    foreach ($dataArray as $buyer_id => $buyerData) 
	                    {
	                    	$r=0;
		                    $byr_req_qty 	= 0;
		                    $byr_excs_qty 	= 0;
		                    $byr_allo_qty 	= 0;
		                    $byr_allo_bal 	= 0;
		                    $byr_upto_allo_qty = 0;
							// ksort($buyerData);
	                        foreach ($buyerData as $seasonId => $seasonData) 
							{
								$z=0;
								ksort($seasonData);
								foreach ($seasonData as $int_ref => $row) 
								{
									$excess_qty = $excess_qty_array[$buyer_id][$seasonId][$int_ref]['excess_qty'];
									$alloc_qty = $alloc_qty_array[$buyer_id][$seasonId][$int_ref]['alloc_qty'];
									if($row['req_qty']>0 || $excess_qty>0 || $alloc_qty>0 || $row['issue']>0 || $row['smn_req_qty']>0) 
									{ 
										// $smn_backlog = $smn_backlog_qty[$buyer_id][$int_ref];
										$smn_backlog = 0;
										$smn_backlog = $row['smn_req_qty'] - $row['issue'];
										// echo $int_ref.'=='.$smn_backlog."==".$row['req_qty'] ."-". $row['issue']."<br>";
										$alloc_bal = $row['req_qty']+$row['smn_req_qty'] + $excess_qty - $alloc_qty;
										$opening_bklg = $row['opening_bklg'];
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
										?>                        
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
											<? if($r==0){?>                            
											<td title="<? echo $buyer_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$buyer_id];?>"><? echo $buyer_array[$buyer_id]; ?></td>
											<? }?>
											<? if($z==0){?>                            
											<td width="100" valign="middle" rowspan="<? echo $rowspanSeason[$buyer_id][$seasonId];?>"><? echo $season_array[$seasonId]; ?></td>
											<? }?>
											<td width="100"><? echo $int_ref; ?></td>
											<td width="100" align="right"><? echo number_format(($row['req_qty']+$row['smn_req_qty']),0);?></td>
											<td width="100" align="right"><? echo number_format($excess_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_bal,0);?></td>
										</tr>
										<?
										$i++;
										$r++;
										$z++;
										$byr_req_qty 	+= $row['req_qty']+$row['smn_req_qty'];
										$byr_excs_qty 	+= $excess_qty;
										$byr_allo_qty 	+= $alloc_qty;
										$byr_allo_bal 	+= $alloc_bal;
										$byr_opening_bklg += $opening_bklg+$smn_backlog;
		
										$gr_req_qty 	+= $row['req_qty']+$row['smn_req_qty'];
										$gr_excs_qty 	+= $excess_qty;
										$gr_allo_qty 	+= $alloc_qty;
										$gr_allo_bal 	+= $alloc_bal;
										$gr_opening_bklg += $opening_bklg+$smn_backlog;
									}
								}
	                        }
	                        ?>
	                        <tr style="font-size:12px;font-weight:bold;background:#ccc;">
	                        	<td colspan="3" align="right">Buyer Total</td>
	                        	<td align="right"><? echo number_format($byr_req_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_excs_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_allo_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_allo_bal,0);?></td>
	                        </tr>
	                        <?                        
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="100"><? echo number_format($gr_req_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_excs_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_bal,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}


if($action=="purchase_backlog_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$current_date = date("j-M-Y");

	?>
	<div id="data_panel" align="center" style="width:800px">
        <script>
            function new_window()
            {
                var w = window.open("Surprise", "#");
                var d = w.document.open();
                d.write(document.getElementById('details_reports2').innerHTML);
                d.close();
            }
        </script>
    	<input type="button" value="Print" id="print" class="formbutton" style="width:100px;margin: 5px;" onclick="new_window()" />
    </div>
    <div class="main" style="width: 800px;" id="details_reports2">
      	<h3>Composition : <? echo $composition[$comp_id];?>,&nbsp;&nbsp;Count : <? echo $count_array[$count_id];?></h3>
        <div class="main_part">
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
                <thead>
                    <tr>
                        <th width="100">Supplier Name</th>
                        <th width="100">L/C NO</th>
                        <th width="100">L/C Date</th>
                        <th width="100">L/C qty</th>
                        <th width="100">Rcv Qty</th>
                        <th width="100">Today Rcv</th>
                        <th width="100">Bal Qty</th>
                        <th width="100">Purchase Backlog before current date</th>
                    </tr>
                </thead>
            </table>
            <div style="width:820px;overflow-y:scroll;max-height:350px;" id="scroll_body">
            	<table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
	                <tbody>
	                    <?
	                    $i=0;
	                    $gr_req_qty 	= 0;
	                    $gr_excs_qty 	= 0;
	                    $gr_allo_qty 	= 0;
	                    $gr_allo_bal 	= 0;
	                    $gr_upto_allo_qty = 0;
	                    foreach ($dataArray as $buyer_id => $buyerData) 
	                    {
	                    	$r=0;
		                    $byr_req_qty 	= 0;
		                    $byr_excs_qty 	= 0;
		                    $byr_allo_qty 	= 0;
		                    $byr_allo_bal 	= 0;
		                    $byr_upto_allo_qty = 0;
							// ksort($buyerData);
	                        foreach ($buyerData as $seasonId => $seasonData) 
							{
								$z=0;
								ksort($seasonData);
								foreach ($seasonData as $int_ref => $row) 
								{
									$excess_qty = $excess_qty_array[$buyer_id][$seasonId][$int_ref]['excess_qty'];
									$alloc_qty = $alloc_qty_array[$buyer_id][$seasonId][$int_ref]['alloc_qty'];
									if($row['req_qty']>0 || $excess_qty>0 || $alloc_qty>0 || $row['issue']>0 || $row['smn_req_qty']>0) 
									{ 
										// $smn_backlog = $smn_backlog_qty[$buyer_id][$int_ref];
										$smn_backlog = 0;
										$smn_backlog = $row['smn_req_qty'] - $row['issue'];
										// echo $int_ref.'=='.$smn_backlog."==".$row['req_qty'] ."-". $row['issue']."<br>";
										$alloc_bal = $row['req_qty']+$row['smn_req_qty'] + $excess_qty - $alloc_qty;
										$opening_bklg = $row['opening_bklg'];
										$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
										?>                        
										<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trs_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trs_<? echo $i; ?>">   
											<? if($r==0){?>                            
											<td title="<? echo $buyer_id;?>" width="100" valign="middle" rowspan="<? echo $rowspan[$buyer_id];?>"><? echo $buyer_array[$buyer_id]; ?></td>
											<? }?>
											<? if($z==0){?>                            
											<td width="100" valign="middle" rowspan="<? echo $rowspanSeason[$buyer_id][$seasonId];?>"><? echo $season_array[$seasonId]; ?></td>
											<? }?>
											<td width="100"><? echo $int_ref; ?></td>
											<td width="100" align="right"><? echo number_format(($row['req_qty']+$row['smn_req_qty']),0);?></td>
											<td width="100" align="right"><? echo number_format($excess_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_qty,0);?></td>
											<td width="100" align="right"><? echo number_format($alloc_bal,0);?></td>
											<td width="100" align="right"><? echo number_format($a,0);?></td>
										</tr>
										<?
										$i++;
										$r++;
										$z++;
										$byr_req_qty 	+= $row['req_qty']+$row['smn_req_qty'];
										$byr_excs_qty 	+= $excess_qty;
										$byr_allo_qty 	+= $alloc_qty;
										$byr_allo_bal 	+= $alloc_bal;
										$byr_opening_bklg += $opening_bklg+$smn_backlog;
		
										$gr_req_qty 	+= $row['req_qty']+$row['smn_req_qty'];
										$gr_excs_qty 	+= $excess_qty;
										$gr_allo_qty 	+= $alloc_qty;
										$gr_allo_bal 	+= $alloc_bal;
										$gr_opening_bklg += $opening_bklg+$smn_backlog;
									}
								}
	                        }
	                        ?>
	                        <tr style="font-size:12px;font-weight:bold;background:#ccc;">
	                        	<td colspan="3" align="right">Buyer Total</td>
	                        	<td align="right"><? echo number_format($byr_req_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_excs_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_allo_qty,0);?></td>
	                        	<td align="right"><? echo number_format($byr_allo_bal,0);?></td>
	                        	<td align="right"><? echo number_format($a,0);?></td>
	                        </tr>
	                        <?                        
	                    }
	                    ?>
	                </tbody>
	            </table>
	        </div>
            <table valign="middle" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="800">
            	<tfoot>
            		<tr>
            			<th width="100"></th>
            			<th width="100"></th>
            			<th width="100">Grand Total </th>
            			<th width="100"><? echo number_format($gr_req_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_excs_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_qty,0);?></th>
            			<th width="100"><? echo number_format($gr_allo_bal,0);?></th>
            			<th width="100"><? echo number_format($a,0);?></th>
            		</tr>
            	</tfoot>
            </table>
        </div>
    </div>
	<?
}
?>
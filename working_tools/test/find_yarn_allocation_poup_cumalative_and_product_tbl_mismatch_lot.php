<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

//die("You are not authenticated");

/*
if($user_id!=1)
{
	die('You are not authenticated');
	disconnect($con);
}*/

$action="synchronize_allocation";


if ($action=="synchronize_allocation")
{
	extract($_REQUEST);
	$con = connect();

	if($db_type==0) 
	{
		mysql_query("BEGIN");
	} 
 
	$process = array( &$_POST );      
	extract(check_magic_quote_gpc( $process ));

	//$company_id=str_replace("'","",$company_id);
	//$product_id = implode(",",explode(",",str_replace("'","",$product_id))); //371384,
    $company_id=17;
	$product_id = "57350";//"63101,57350,57356,57358,57359"
 
	//echo "10**";
	$prod_cond 		= ($product_id!="")?" and a.item_id in (".$product_id.")":"";

	$company_cond 	= ($company_id>0)?" and b.company_id=$company_id":"";

	//$booking_no_cond = " and a.booking_no='RpC-Fb-23-00008'";
	
	if ($db_type == 0)  
	{
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,a.booking_no, sum(qnty) as allocate_qty, sum(a.qnty) as allocate_qty,a.allocation_date,a.is_sales,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and round(b.current_stock,2)>0 and ( round(b.current_stock,2) !=round(b.available_qnty,2) and  round(b.allocated_qnty,2)>0 ) and b.status_active=1 and b.is_deleted=0 $company_cond $prod_cond group by a.item_id,a.job_no,a.po_break_down_id,a.booking_no,a.allocation_date,a.is_sales,b.dyed_type,a.id,a.mst_id";
	}
	else
	{
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,c.booking_without_order, a.booking_no, sum(a.qnty) as allocate_qty,a.allocation_date,a.is_sales,b.dyed_type is_dyied_yarn	from inv_material_allocation_mst c, inv_material_allocation_dtls a,product_details_master b where c.id=a.mst_id and a.item_id=b.id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 $company_cond $prod_cond $booking_no_cond group by a.item_id,a.job_no,a.po_break_down_id,a.booking_no,c.booking_without_order,a.allocation_date,a.is_sales,b.dyed_type,a.id,a.mst_id order by a.id";
		//and a.qnty>0 and round(b.current_stock,2)>0 and ( round(b.current_stock,2) !=round(b.available_qnty,2) and  round(b.allocated_qnty,2)>0 )
	}

	//echo "10**".$sql_allocation;  die(); 
	$result_allocation = sql_select($sql_allocation);

	//print_r($result_allocation);
	//die();

	$po_break_down_arr = $job_arr =  array();
	foreach ($result_allocation as $row) 
	{
		$job_no = preg_replace('/\s+/', '', $row[csf("job_no")]);
		$expBookinNo = explode("-", $row[csf("booking_no")]);
		$item_arr[] = $row[csf("item_id")];

		if($expBookinNo[1] == 'SMN')
		{
			$row[csf("po_break_down_id")] = '';
			$smn_booking_arr[] = "'".$row[csf("booking_no")]."'";
		}
		else
		{
			$row[csf("po_break_down_id")]=$row[csf("po_break_down_id")];
		}	
		
		if($row[csf("is_sales")]==1)
		{
			$sales_job_arr[$row[csf("po_break_down_id")]] = $row[csf("job_no")];
			$is_sales_arr[$row[csf("po_break_down_id")]] = $row[csf("is_sales")];
		}
		else
		{
			if($job_no!="")
			{
				$job_arr[$job_no] = "'".$job_no."'";
			}

			if($row[csf("po_break_down_id")]!="")
			{
				$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
			}
		}

		$booking_arr=explode(",",$row[csf("booking_no")]);
		foreach ($booking_arr as $booking)
		{
			$booking_allocation_arr[$row[csf("po_break_down_id")]][$row[csf("item_id")]][$booking] += $row[csf("allocate_qty")]*1;

			$albooking_arr[$row[csf("item_id")]][$job_no][]=$booking;

			$allcation_job_data[$row[csf("item_id")]][$job_no]['po_id']=$row[csf("po_break_down_id")];
			$allcation_job_data[$row[csf("item_id")]][$job_no]['booking_no']=$booking;
		}
	}

	if($db_type==2 && count($item_arr)>1000)
	{
		$prod_id_cond=" and (";
		$prod_id_cond2=" and (";
		$prod_id_cond3=" and (";
		$prod_id_cond4=" and (";

		$proIdArr=array_chunk(array_unique($item_arr),999);
		foreach($proIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$prod_id_cond.=" a.id in($ids) or ";
			$prod_id_cond2.=" c.prod_id in($ids) or ";
			$prod_id_cond3.=" b.prod_id in($ids) or ";
			$prod_id_cond4.=" d.product_id in($ids) or ";
		}

		$prod_id_cond=chop($prod_id_cond,'or ');
		$prod_id_cond.=")";

		$prod_id_cond2=chop($prod_id_cond2,'or ');
		$prod_id_cond2.=")";

		$prod_id_cond3=chop($prod_id_cond2,'or ');
		$prod_id_cond3.=")";

		$prod_id_cond4=chop($prod_id_cond2,'or ');
		$prod_id_cond4.=")";
	}
	else
	{
		$prod_id_cond=" and a.id in (".implode(",",array_unique($item_arr)).")";
		$prod_id_cond2=" and c.prod_id in (".implode(",",array_unique($item_arr)).")";
		$prod_id_cond3=" and b.prod_id in (".implode(",",array_unique($item_arr)).")";
		$prod_id_cond4=" and d.product_id in (".implode(",",array_unique($item_arr)).")";
	}

    $sql_products = "select a.id, a.company_id, a.lot,a.current_stock, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,a.dyed_type from product_details_master a where a.item_category_id=1 and a.company_id=$company_id $prod_id_cond and a.status_active=1 and a.is_deleted=0";

	$product_array=array();
	$productData = sql_select($sql_products);
	foreach( $productData as $prod_val)
	{
		$product_array[$prod_val[csf('id')]]['lot']						= $prod_val[csf('lot')];	
		$product_array[$prod_val[csf('id')]]['dyed_type']				= $prod_val[csf('dyed_type')];	
		$product_array[$prod_val[csf('id')]]['current_stock']			= $prod_val[csf('current_stock')];
		$product_array[$prod_val[csf('id')]]['allocated_qnty']			= $prod_val[csf('allocated_qnty')];
		$product_array[$prod_val[csf('id')]]['available_qnty']			= $prod_val[csf('available_qnty')];		
	}

    //echo "<pre>";
	//print_r($product_array); die();

	if(!empty($job_arr))
	{
		$sql_cond = " and a.job_no_mst in(".implode(",",$job_arr).")";
	}
	else
	{
		$sql_cond = (!empty($po_break_down_arr))?" and a.id in(".implode(",",$po_break_down_arr).")":"";
	}

	$po_number_arr = array();
	$po_sql = sql_select("select a.id,a.job_no_mst,b.buyer_name, a.file_no,a.grouping,a.po_number,shiping_status from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no $sql_cond and a.status_active=1 and a.is_deleted=0");
	foreach ($po_sql as $row) {
		$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
		$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
		$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
		$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
		$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
		$po_number_arr[$row[csf("id")]]['shiping_status'] = $row[csf("shiping_status")];
	}


	if(!empty($sales_job_arr))
    {
	    $sales_job_cond = " and a.job_no in('".implode("','",$sales_job_arr)."')";
	}

	$jobsql = "select a.id, a.job_no,a.buyer_id,a.sales_booking_no, a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $sales_job_cond";
	$jobData = sql_select($jobsql);
	$job_no_array = array();
	foreach ($jobData as $row) 
	{
		$sales_order_arr[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$sales_order_arr[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
		$sales_order_arr[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
	}

	$planning_array = array();
	//$plan_rqsn_sql="select b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id req_id,c.requisition_no,c.prod_id,c.yarn_qnty req_qnty,b.is_sales,b.po_id,e.order_id,e.order_requisition_qty from (select a.po_id,a.booking_no,a.is_sales,a.dtls_id,a.program_qnty,d.program_qnty total_prog_qnty from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls d where a.dtls_id=d.id and a.status_active=1 and d.status_active=1 group by a.po_id,a.booking_no,a.is_sales,a.dtls_id,a.program_qnty,d.program_qnty) b,ppl_yarn_requisition_entry c left join ppl_yarn_requisition_breakdown e on c.requisition_no=e.requisition_id and c.prod_id=e.item_id and c.knit_id=e.program_id  where b.dtls_id=c.knit_id and c.status_active=1 $prod_id_cond2"; 

	$plan_rqsn_sql="select a.booking_no,a.is_sales,a.dtls_id,a.po_id,b.id req_id,b.requisition_no,b.prod_id,b.yarn_qnty req_qnty,c.order_id,c.order_requisition_qty from ppl_planning_entry_plan_dtls a,ppl_yarn_requisition_entry b left join ppl_yarn_requisition_breakdown c on b.requisition_no=c.requisition_id and b.prod_id=c.item_id and b.knit_id=c.program_id where a.dtls_id=b.knit_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_id_cond3"; 
	//echo $plan_rqsn_sql ; die();


	$planData = sql_select($plan_rqsn_sql);
	
	$booking_checked = $booking_po_checked = $plan_requisition_data_array= $plan_requisition_array = array();
	foreach ($planData as $row) 
	{
		if( $row[csf('order_id')] !=null )
		{
			if($booking_po_checked[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('dtls_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]=="")
			{
				$booking_po_checked[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('dtls_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('prod_id')];

				$plan_requisition_data_array[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]['po_wise']+= $row[csf('order_requisition_qty')];
			}
		}
		//else // gross level requisition and sales order  
		//{
			if($booking_checked[$row[csf('booking_no')]][$row[csf('dtls_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]=="")
			{
				$booking_checked[$row[csf('booking_no')]][$row[csf('dtls_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('prod_id')];

				$plan_requisition_data_array[$row[csf('booking_no')]][0][$row[csf('requisition_no')]][$row[csf('prod_id')]]['booking_wise'] = $row[csf('req_qnty')];
			}
		//}
		
		$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]][] = $row[csf('booking_no')];

		$expRbookinNo = explode("-", $row[csf("booking_no")]);

		if($expRbookinNo[1] == 'SMN')
		{
			$plan_requisition_array[$row[csf('booking_no')]][0][$row[csf('prod_id')]]['requisition_ids'] .= $row[csf("req_id")].",";
			$plan_requisition_array[$row[csf('booking_no')]][0][$row[csf('prod_id')]]['requisition_nos'] .= $row[csf("requisition_no")].",";
		}
		else
		{
			$plan_requisition_array[$row[csf('booking_no')]][$row[csf("po_id")]][$row[csf('prod_id')]]['requisition_ids'] .= $row[csf("req_id")].",";
			$plan_requisition_array[$row[csf('booking_no')]][$row[csf("po_id")]][$row[csf('prod_id')]]['requisition_nos'] .= $row[csf("requisition_no")].",";
		}

		$plan_requisition_array[$row[csf('booking_no')]][$row[csf("po_id")]][$row[csf('prod_id')]]['program_nos'] .= $row[csf("dtls_id")].",";		
	}

	//echo "<pre>";
	//print_r($plan_requisition_array); die();

	$wo_sql="SELECT d.id wo_dtls_id, d.job_no, (d.fab_booking_no || d.booking_no) as booking_no, d.product_id,d.yarn_wo_qty FROM wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d WHERE     c.id = d.mst_id AND c.status_active = 1 AND c.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 $prod_id_cond4";
	
	$wo_result= sql_select($wo_sql);

	$wo_data_arr = array();
	foreach($wo_result as $row)
	{
		$wo_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('product_id')]]['qnty'] += $row[csf('yarn_wo_qty')];
		$wo_data_arr[$row[csf('job_no')]][$row[csf('booking_no')]][$row[csf('product_id')]]['wo_dtls_id'] .= $row[csf('wo_dtls_id')].",";
	}

	
	/*echo "<pre>";
	print_r($wo_data_arr);
	die();*/

	$sql_issue = "select a.id,a.issue_basis,a.issue_purpose,a.booking_id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no,c.is_sales from inv_issue_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,7,12,15,38,46) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $prod_id_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,a.issue_basis,a.issue_purpose,a.booking_id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no,c.is_sales";
	//echo $sql_issue; die();

	$result_issue = sql_select($sql_issue);
	$issue_array_req = $issue_array = $issue_basis_arr = $booking_arr= array();

	foreach ($result_issue as $row) 
	{
		$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("job_no")];
		$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
		$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];

		$booking_id =  $row[csf("booking_id")];
		$po_id = $row[csf("po_breakdown_id")];
		$is_sales = $is_sales_arr[$po_id];
		$issue_basis = $row[csf("issue_basis")];
		$issue_purpose= $row[csf("issue_purpose")];

		if($is_sales==1)
		{
			$job_no = $sales_job_arr[$po_id];
		}
		else
		{
			$job_no = $po_number_arr[$po_id]['job_no'];
		}

		if ( ($issue_basis == 3 || $issue_basis == 8) &&  $issue_purpose==1 )
		{
			$issue_requisiton_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
		}

		if($row[csf('dyed_type')] == 1)
		{
			if( ($issue_basis == 3 || $issue_basis == 8) &&  $issue_purpose==1 )
			{
				$booking_arr = $albooking_arr[$row[csf('prod_id')]][$job_no];
				$booking_arr = array_unique($booking_arr);

				$alocation_po = $allcation_job_data[$row[csf("prod_id")]][$job_no]['po_id'];
				$alocation_booking_no = $allcation_job_data[$row[csf("prod_id")]][$job_no]['booking_no'];
				
				foreach ($booking_arr as $booking_no) 
				{
					if( $job_no != "" && ($alocation_po=="" && $alocation_booking_no=="" ) ) 
					{
						$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];						
					}
					else
					{
						$issue_array[$job_no][$booking_no][$po_id][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
					}
				}
                
				$requisition_wise_issue_array[$row[csf("po_breakdown_id")]][$row[csf("requisition_no")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
			}
			else if( ($issue_basis == 1) && ( $issue_purpose==2 || $issue_purpose== 7 || $issue_purpose== 12 || $issue_purpose== 15 || $issue_purpose== 38 || $issue_purpose== 46)) 
			{
			
				$is_sales = $is_sales_arr[$row[csf("job_no")]];
				$alocation_po = $allcation_job_data[$row[csf("prod_id")]][$job_no]['po_id'];
				$alocation_booking_no = $allcation_job_data[$row[csf("prod_id")]][$job_no]['booking_no'];

				if($is_sales==1)
				{
					$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
				}else{
					$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
				}

				$booking_no = return_field_value("fab_booking_no","wo_yarn_dyeing_dtls","mst_id ='".$booking_id."' and job_no ='".$job_no."' and is_deleted=0 and status_active=1 group by fab_booking_no","fab_booking_no");
                
                if( $job_no != "" && ($alocation_po=="" && $alocation_booking_no=="" ) ) 
				{
					$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];						
				}
				else
				{
					$issue_array[$job_no][$booking_no][$po_id][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				}
			}
		}
		else
		{
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			
			if($issue_basis == 3 || $issue_basis==1 || $issue_basis == 8)
			{
				if($issue_basis==1)
				{
					if( ($issue_basis == 1) && ( $issue_purpose==2 || $issue_purpose== 7 || $issue_purpose== 12 || $issue_purpose== 15 || $issue_purpose== 38 || $issue_purpose== 46)) 
					{
						$booking_no = return_field_value("fab_booking_no","wo_yarn_dyeing_dtls","mst_id ='".$booking_id."' and job_no ='".$job_no."' and is_deleted=0 and status_active=1 group by fab_booking_no","fab_booking_no");

						if( $booking_no!="" && $po_id!="") 
						{
							$issue_array[$job_no][$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
						}
						else
						{
							$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
						}							
					}		
				}
				else
				{
					$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
					$booking_arr = array_unique($booking_arr);
					foreach ($booking_arr as $booking) 
					{
						if($booking != "")
						{
							$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
							$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
							
							$issue_array_req_no[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking][$row[csf("id")]]['requisition_no'] .= $row[csf("requisition_no")].",";
						}
					}

					$requisition_wise_issue_array[$booking][$row[csf("po_breakdown_id")]][$row[csf("requisition_no")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
					$booking_requisition_wise_issue_array[$booking][$row[csf("requisition_no")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
				}
			}
		}
	}

	//echo "<pre>";
	//print_r($issue_array_req_no); die();

    if($db_type==2 && count($smn_booking_arr)>1000)
	{
		$smn_booking_cond=" and (";

		$smnBookingArr=array_chunk(array_unique($smn_booking_arr),999);
		foreach($smnBookingArr as $bookingNo)
		{
			$smnBookingNo= implode(",",$bookingNo);
		}

		$smn_booking_cond=chop($smn_booking_cond,'or ');
		$smn_booking_cond.=")";
	}
	else
	{
		$smn_booking_cond=" and d.id in (".implode(",",array_unique($smn_booking_arr)).")";
	}

    $smn_issue_sql = "SELECT x.issue_id,x.issue_basis,x.issue_purpose,x.requisition_no,
         x.booking_id, x.booking_no,x.prod_id, SUM (x.cons_quantity) AS issue_qty
    FROM (SELECT DISTINCT a.company_id,a.id as issue_id,a.issue_basis,a.issue_purpose,a.booking_id,d.booking_no,b.requisition_no,b.prod_id,b.cons_quantity,b.id FROM inv_issue_master a,inv_transaction b, ppl_yarn_requisition_entry c,ppl_planning_entry_plan_dtls d WHERE  a.id = b.mst_id AND b.requisition_no = c.requisition_no AND c.knit_id = d.dtls_id AND b.transaction_type = 2 AND b.item_category = 1 AND a.issue_purpose = 1 AND d.booking_no like '%SMN%' AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 $prod_id_cond3 $prod_id_cond2 ) x GROUP BY x.issue_id,x.issue_basis,x.issue_purpose,x.requisition_no,x.booking_id, x.booking_no,x.prod_id
    UNION ALL
    SELECT y.issue_id,y.issue_basis,y.issue_purpose,y.requisition_no,
         y.booking_id,y.booking_no,y.prod_id, SUM (y.cons_quantity) AS issue_qty
    FROM (SELECT DISTINCT a.company_id,a.id as issue_id,a.issue_basis, a.issue_purpose,a.booking_id,d.booking_no,b.requisition_no,b.prod_id,b.cons_quantity,b.id FROM inv_issue_master   a,inv_transaction  b, wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls d WHERE a.id = b.mst_id AND a.booking_no = c.ydw_no AND c.id = d.mst_id AND b.transaction_type = 2 AND b.item_category = 1 AND a.issue_purpose = 2 AND d.booking_no like '%SMN%' AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 $prod_id_cond3 $prod_id_cond4) y GROUP BY y.issue_id,y.issue_basis,y.issue_purpose,y.requisition_no,y.booking_no,y.booking_id,y.prod_id";
    
    //echo $smn_issue_sql; die();
    $smn_issue_result=sql_select($smn_issue_sql); 
    $issueIdArr = array();
    foreach($smn_issue_result as $row)
    {
    	$issue_basis = $row[csf("issue_basis")];
    	$issue_purpose= $row[csf("issue_purpose")];

    	$issueIdArr[$row[csf("issue_id")]] = $row[csf("issue_id")];
    	$smn_issue_array[$row[csf("booking_no")]][$row[csf("prod_id")]]["issue_qty"] +=$row[csf("issue_qty")]; //sample requsition and wo issue

    	if ( ($issue_basis == 3 || $issue_basis == 8) &&  $issue_purpose==1 )
		{
			$issue_requisiton_no_arr[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
		}
    }

	//echo "10**<pre>";
	//print_r($issue_array);
	//echo "</pre>";
	//die();

	$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = $requsition_wise_issure_rtn_qty = array();

	$sql_return = "Select a.booking_id,b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and c.trans_type=4 and c.prod_id=d.id and a.entry_form=9 and b.id=c.trans_id and b.item_category=1  and c.issue_purpose in(1,2,7,12,15,38,46) and a.receive_basis not in (2) $prod_id_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.booking_id,b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type";

	$result_return = sql_select($sql_return);
	foreach ($result_return as $row) 
	{
		$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];
		$po_id = $row[csf("po_breakdown_id")];

		if($row[csf('dyed_type')] == 1)
		{
			$is_sales = $is_sales_arr[$po_id];

			if($is_sales==1)
			{
				$job_no = $sales_job_arr[$po_id];
				$issue_arr[$po_id][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}
			else
			{
				$job_no = $po_number_arr[$po_id]['job_no'];
				$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}
				
			$booking_arr = $albooking_arr[$row[csf('prod_id')]][$job_no];
			$booking_arr = array_unique($booking_arr);
			foreach ($booking_arr as $booking_no) 
			{
				if($booking_no != "")
				{
					$job_wose_issue_return_array[$job_no][$po_id][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
				}
				else
				{
					$job_wose_issue_return_array[$job_no][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
				}
			}				
		}
		else
		{
			if($issue_basis == 3 || $issue_basis == 8)
			{
				$booking_no = return_field_value("booking_no","ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b","a.requisition_no ='".$row[csf('booking_id')]."' and a.knit_id =b.dtls_id group by booking_no","booking_no");

				$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
				$issue_return_po_array[$issue_id][$row[csf('prod_id')]] .= $row[csf('po_breakdown_id')].",";
				$issue_return_req_array[$booking_no][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] += $row[csf("issue_return_qty")];	

				$requsition_wise_issure_rtn_qty[$booking_no][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]] +=$row[csf("issue_return_qty")];
				$booking_requsition_wise_issure_rtn_qty[$booking_no][$row[csf('prod_id')]] +=$row[csf("issue_return_qty")];
			}
			else
			{
				if($issue_basis==1)
				{
					if( ( $row[csf("issue_purpose")]==2 || $row[csf("issue_purpose")]==7 || $row[csf("issue_purpose")]==15 || $row[csf("issue_purpose")]==38 || $row[csf("issue_purpose")]==46))
					{
						$booking_no = 0;
						$issue_return_array[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
					}
				}
				else
				{
					$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
				}
			}
		}			
	}

	//echo "<pre>";
	//print_r($booking_requsition_wise_issure_rtn_qty); die();

	//for smaple without order issue return qty
	$issueIdcond = "";
	if(!empty($issueIdArr))
	{
		$issueIdcond = "and b.issue_id in(".implode(',', $issueIdArr).")";

		$sqlRtnQty = "Select a.receive_basis as RECEIVE_BASIS,a.booking_id as BOOKING_ID, b.issue_id as ISSUE_ID, b.prod_id as PROD_ID, sum(b.order_qnty) as RETURN_QTY from inv_receive_master a, inv_transaction b, product_details_master d where a.id=b.mst_id and b.prod_id=d.id and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.receive_basis not in (2) and a.status_active=1 and b.status_active=1 $prod_id_cond3 ".$issueIdcond." group by a.receive_basis,a.booking_id,b.issue_id, b.prod_id"; 
		//echo $sqlRtnQty;
		$sqlRtnQtyRslt = sql_select($sqlRtnQty);
		
		$sampleReturnQty = array();
		foreach($sqlRtnQtyRslt as $row)
		{
			if($row['RECEIVE_BASIS']==3)
			{
				$smn_booking_no = return_field_value("b.booking_no as BOOKING_NO","ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b","a.knit_id=b.dtls_id and a.requisition_no ='".$row[csf("booking_id")]."' and a.prod_id ='".$row[csf("prod_id")]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","BOOKING_NO");
			}

			$sampleReturnQty[$smn_booking_no][$row['PROD_ID']] += $row['RETURN_QTY'];
		}

		//echo "<pre>";
		//print_r($sampleReturnQty);
		//die();
    }		
		
	//echo "10**";
	$i = 1;
	$prod_arr = array();

	if(!empty($result_allocation))
	{
		//echo "10**test"; die();
		$balance = $balance_new = $issue_qty = $return_qty = 0;
		$item_arr = $mst_data_update_arr = array();
		foreach ($result_allocation as $row) 
		{
			$prod_id = $row[csf("item_id")];
			$dtls_id = $row[csf("id")];
			if(empty($item_arr))
			{
				$item_arr[] = $prod_id;
			}
			else
			{
				if(!in_array($prod_id, $item_arr))
				{
					$i = 1;
					$balance = $balance_new = 0;
					unset($item_arr);
					$item_arr[] = $prod_id;
				}
			}

			if($row[csf("po_break_down_id")]==""){
				$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
			}else{
				$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
			}

			if($row[csf("po_break_down_id")]=="" || $row[csf("booking_no")]==""){
				$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
			}else{
				$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
			}

			//print_r($issue_basis);
			if($row[csf("booking_without_order")]==1) // sample issue grey and yied
			{
                $issue_qty += $smn_issue_array[$row[csf("booking_no")]][$prod_id]["issue_qty"];//sample 
			}
			else if($row[csf("is_dyied_yarn")] == 1)
			{
				if ( $row[csf("job_no")]!='' && $row[csf("po_break_down_id")] ="" && $row[csf("booking_no")] ="") // old data 
				{
                     $issue_qty += $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
				}
				else
				{
					$issue_qty += $issue_array[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
				}
			}
			else
			{
				$issue_qty=$issue_qty_wo=0;
				foreach ($issue_basis as $basis) 
				{
					if($basis==3 || $basis==1 || $basis==8)
					{
						if($basis==1)
						{
                            if( $row[csf("booking_no")]!="" && $row[csf("po_break_down_id")]!="")
							{
							    $issue_qty_wo +=$issue_array[$row[csf('job_no')]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
							}
							else
							{
								$issue_qty_wo +=$issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
							}
						}
						else
						{
							if($row[csf("booking_no")] != "")
							{
								$booking_nos = explode(",",$row[csf("booking_no")]);
								$issue_qty=0;
								foreach ($booking_nos as $booking_row) 
								{
									$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];

									$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
								}
							}
						}
					}
					else
					{
						$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id]["issue_qty"];
					}
				}
			}
			
			$within_group=$sales_order_arr[$row[csf('job_no')]]['within_group'];
			$sales_booking_no=$sales_order_arr[$row[csf('job_no')]]['sales_booking_no'];

			if($sales_booking_no!="")
			{
				$return_qty = 0;			
			}
			else
			{
				if($row[csf("is_dyied_yarn")] == 1)
				{
					if( $row[csf("po_break_down_id")]!="" )
					{
						$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$row[csf("po_break_down_id")]][$prod_id];
					}
					else
					{
						$job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];
					}
								
					//for smaple without order issue return qty
					if($row[csf("job_no")] == '')
					{
						$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id]+$sampleReturnQty[$row[csf("booking_no")]][$prod_id];
					}
				}
				else
				{
					$return_qty=0;
					foreach ($issue_basis as $basis) 
					{
					    //echo $basis . "*";
						//$return_qty=0;
						if( $basis==3 || $basis==8 )
						{
							$booking_nos = explode(",",$row[csf("booking_no")]);
							foreach ($booking_nos as $booking_row) 
							{
								$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_id"]);
								foreach ($issue_ids as $issue_id)
								{
									$return_qty += $issue_return_req_array[$booking_row][$row[csf('po_break_down_id')]][$prod_id][$issue_id];
								}
							}										
						}
						else
						{
						    if($basis==1) // booking basis-- work order
							{
								$booking_row = 0;
								$return_qty += $issue_return_array[$booking_row][$row[csf("po_break_down_id")]][$prod_id];
							}
							else
							{
								$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
							}
							//echo $return_qty."<br>";
						}
					}
								
					//for smaple without order issue return qty
					if($row[csf("job_no")] == '')
					{
						$return_qty = $return_qty+$sampleReturnQty[$row[csf("booking_no")]][$prod_id];
					}
				}
			}

			$issue_qty = $issue_qty+$issue_qty_wo;

			$booking_no = $row[csf("booking_no")]; 

			$balance = $balance + (number_format($row[csf("allocate_qty")], 2,".","") + number_format($return_qty, 2,".","")) - number_format($issue_qty, 2,".","");

			$prod_update_arr[$prod_id]["cumul_balance"] = $balance;	

		}

	}
	else
	{
		echo "Data Not found in allocation tables";
	}

    echo "<pre>";
    print_r($prod_update_arr);
    die();

	$field_array_product_update = "allocated_qnty*available_qnty*updated_by*update_date";

	if(!empty($data_array_product_update))
	{
		$rID3 =execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product_update,$data_array_product_update,$prID_array),0);
		if ($rID3) $flag = 1; else $flag = 0;
	}
	
	//echo "10**".$rID ."&&". $rID2 ."&&". $rID3."&&". $rID4."&&". $po_requisition_upd; die;

	echo "10**".$flag."test"; oci_rollback($con); die();
	
	if($db_type==0)
	{
		if( $flag == 1 )
		{
			mysql_query("COMMIT");
			echo "0**Data Synchronize is completed successfully";
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**Data Synchronize is not completed successfully";
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag == 1)
		{
			oci_commit($con);
			echo "0**Data Synchronize is completed successfully";
		}
		else
		{
			oci_rollback($con);
			echo "10**Data Synchronize is not completed successfully**$data_array_prod_update";
		}
	}

	disconnect($con);
	die;
}

?>

<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$permitted_user_id_arr = array(1);

if(!in_array($user_id, $permitted_user_id_arr))
{
	die('You are not authenticated');
	disconnect($con);
}

$action="synchronize_allocation";


if ($action=="synchronize_allocation")
{
	extract($_REQUEST);
	$con = connect();

	if($db_type==0)
	{
		mysql_query("BEGIN");
	}

	//$process = array( &$_POST );
	//extract(check_magic_quote_gpc( $process ));

	$company_id=$_REQUEST["company_id"];
	$product_id_arr = explode(",",$_REQUEST["product_id"]);
	$product_ids = implode(",", $product_id_arr);

	$prod_cond 		= ($product_ids!="")?" and a.item_id in (".$product_ids.")":"";
	//$company_cond 	= ($company_id>0)?" and b.company_id=$company_id":"";
	
	if ($db_type == 0) 
	{
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,group_concat(booking_no) booking_no, sum(qnty) as allocate_qty, sum(a.qnty) as allocate_qty,a.allocation_date,a.is_sales,b.dyed_type is_dyied_yarn,c.booking_without_order from inv_material_allocation_dtls a,inv_material_allocation_mst c, product_details_master b where a.mst_id=c.id and a.item_id=c.item_id and a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and round(b.current_stock,2)>0 and ( round(b.current_stock,2) !=round(b.available_qnty,2) and  round(b.allocated_qnty,2)>0 ) and b.status_active=1 and b.is_deleted=0 $prod_cond group by a.item_id,a.job_no,a.po_break_down_id,a.allocation_date,a.is_sales,b.dyed_type,a.id,a.mst_id,,c.booking_without_order";
	}
	else
	{
		if($prod_cond!="")
		{
			$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,a.booking_no, sum(a.qnty) as allocate_qty,a.allocation_date,a.is_sales,b.dyed_type is_dyied_yarn,c.booking_without_order from inv_material_allocation_dtls a,inv_material_allocation_mst c,product_details_master b where a.mst_id=c.id and a.item_id=c.item_id and a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_cond  group by a.item_id,a.job_no,a.po_break_down_id,a.booking_no,a.allocation_date,a.is_sales,b.dyed_type,a.id,a.mst_id,c.booking_without_order";//and a.qnty>0 and round(b.allocated_qnty,2)>0
		}else{
			exit("prod id can not empty,plz put some prod id into the url");
		}	
	}
	//echo "10**".$sql_allocation;  die(); 
	$result_allocation = sql_select($sql_allocation);

	//print_r($result_allocation);
	//die();

	$po_break_down_arr = $job_arr = $smn_booking_arr = array();
	foreach ($result_allocation as $row) 
	{
		$is_dyied_yarn = $row[csf("is_dyied_yarn")];
		$job_no = preg_replace('/\s+/', '', $row[csf("job_no")]);

		if($row[csf("is_dyied_yarn")] == 1)
		{
			$job_arr[$row[csf("job_no")]] = "'".$row[csf("job_no")]."'";
		} 
		else 
		{
			$po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
		}
		
		$item_arr[] = $row[csf("item_id")];

		$expBookinNo = explode("-", $row[csf("booking_no")]);

		if($expBookinNo[1] == 'SMN')
		{
			$smn_booking_arr[$row[csf("booking_no")]] = "'".$row[csf("booking_no")]."'";	
		}
		else
		{
			$allbooking_with_po_arr[$row[csf("item_id")]][$job_no][]=$row[csf("booking_no")];
		}

		//$booking_allocation_arr[$row[csf("po_break_down_id")]][$row[csf("item_id")]][$row[csf("booking_no")]] += $row[csf("allocate_qty")]*1;

		if($row[csf("is_sales")] == 1)
		{
			$sales_po_break_down_arr[$row[csf("po_break_down_id")]] = $row[csf("po_break_down_id")];
		}

		$expBookinNo = explode("-", $row[csf("booking_no")]);
		if($expBookinNo[1] == 'SMN')
		{
			$smn_booking_arr[$row[csf("booking_no")]] = "'".$row[csf("booking_no")]."'";
		}
	}



	if($db_type==2 && count($item_arr)>1000)
	{
		$prod_id_cond=" and (";
		$prod_id_cond2=" and (";
		$prod_id_cond3=" and (";

		$proIdArr=array_chunk(array_unique($item_arr),999);
		foreach($proIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$prod_id_cond.=" id in($ids) or ";
			$prod_id_cond2.=" c.prod_id in($ids) or ";
			$prod_id_cond3.=" b.prod_id in($ids) or ";
		}

		$prod_id_cond=chop($prod_id_cond,'or ');
		$prod_id_cond.=")";

		$prod_id_cond2=chop($prod_id_cond2,'or ');
		$prod_id_cond2.=")";

		$prod_id_cond3=chop($prod_id_cond2,'or ');
		$prod_id_cond3.=")";
	}
	else
	{
		$item_arr = array_unique($item_arr);
		$prod_id_cond=" and id in (".implode(",",$item_arr).")";
		$prod_id_cond2=" and c.prod_id in (".implode(",",$item_arr).")";
		$prod_id_cond3=" and b.prod_id in (".implode(",",$item_arr).")";
	}

	$sql_products = "select a.id, a.company_id, a.lot,a.product_name_details,a.current_stock, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,dyed_type from product_details_master a where a.item_category_id=1 $prod_id_cond and a.status_active=1 and a.is_deleted=0";

	$product_array=array();
	$productData = sql_select($sql_products);
	foreach( $productData as $prod_val){
		$product_array[$prod_val[csf('id')]]['lot']						= $prod_val[csf('lot')];
		$product_array[$prod_val[csf('id')]]['product_name_details']	= $prod_val[csf('product_name_details')];
		$product_array[$prod_val[csf('id')]]['current_stock']			= $prod_val[csf('current_stock')];
		$product_array[$prod_val[csf('id')]]['allocated_qnty']			= $prod_val[csf('allocated_qnty')];
		$product_array[$prod_val[csf('id')]]['available_qnty']			= $prod_val[csf('available_qnty')];
		$product_array[$prod_val[csf('id')]]['dyed_type']				= $prod_val[csf('dyed_type')];
	}

	$sql_cond = !empty($job_arr)?" and a.job_no_mst in(".implode(",",$job_arr).")":" and a.id in(".implode(",",$po_break_down_arr).")";
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

	$all_sales_ids=array_unique($sales_po_break_down_arr);
	if($db_type==2 && count($all_sales_ids)>999)
	{
		$sales_cond=" and (";
		$salesIdsArr=array_chunk($all_sales_ids,999);
		foreach($salesIdsArr as $ids)
		{
			$ids=rtrim(implode(",",$ids),", ");
			$sales_cond.=" a.id in($ids) or";
		}
		$sales_cond=chop($sales_cond,'or ');
		$sales_cond.=")";
	}
	else
	{
		$poIds=rtrim(implode(",",$all_sales_ids),", ");
		$sales_cond=" and a.id in($poIds)";
	}

	$job_no_array = array();
	$jobsql = "select a.id,a.job_no,a.buyer_id,a.sales_booking_no,a.style_ref_no,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $sales_cond";
	$jobData = sql_select($jobsql);
	foreach ($jobData as $row) 
	{
		$sales_order_arr[$row[csf('id')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$sales_order_arr[$row[csf('id')]]['job']=$row[csf('job_no')];
		$sales_order_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$sales_order_arr[$row[csf('id')]]['within_group']=$row[csf('within_group')];
	}

	$planning_array = array();
	$plan_sql="select b.po_id,b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id req_id,c.requisition_no,c.prod_id,sum(c.yarn_qnty) req_qnty from (select a.po_id,a.booking_no,a.dtls_id,a.program_qnty,d.program_qnty total_prog_qnty from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls d where a.dtls_id=d.id and a.status_active=1 and d.status_active=1 group by a.po_id,a.booking_no,a.dtls_id,a.program_qnty,d.program_qnty) b,ppl_yarn_requisition_entry c where b.dtls_id=c.knit_id and c.status_active=1 $prod_id_cond2 group by b.po_id,b.booking_no,b.dtls_id,b.program_qnty,b.total_prog_qnty,c.id,c.requisition_no,c.prod_id";
	//echo $plan_sql; die;

	$planData = sql_select($plan_sql);
	$requisition_qnty_array=array();
	foreach ($planData as $row) {
		$total_prog_qnty = $row[csf('total_prog_qnty')];
		$po_program_qnty = $row[csf('program_qnty')];
		$perc 			 = ($po_program_qnty / $total_prog_qnty) * 100;		
		$req_qnty 		 = ($row[csf('req_qnty')] / 100)*$perc;
		
		if($req_qnty>0)
		{
			$requisition_qnty_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]+=$req_qnty;
			$planning_array[$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('requisition_no')]][$row[csf('prod_id')]]['req_qnty'] += $req_qnty;
			$planning_array[$row[csf('po_id')]][$row[csf('booking_no')]][$row[csf('prod_id')]]['requisition_no'] .= $row[csf('requisition_no')].",";
			$planning_array[$row[csf('po_id')]][$row[csf('prod_id')]]['req_id'] .= $row[csf('req_id')].",";
			$planning_array[$row[csf('po_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]][] = $row[csf('booking_no')];
			$requisition_no_arr[$row[csf('requisition_no')]] = $row[csf('requisition_no')];
		}	

	}

    //echo "<pre>";
	//print_r($requisition_qnty_array);
	//die;

   $requisition_cond = !empty($requisition_no_arr)?" and c.requisition_no in(".implode(",",$requisition_no_arr).")":"";

    $sql_demand_reqsn_dtls = sql_select("select c.mst_id, c.dtls_id, c.requisition_id, c.requisition_no, c.prod_id, c.yarn_demand_qnty, c.cone_qty, c.ctn_qty, c.remarks from ppl_yarn_demand_reqsn_dtls c where c.status_active=1 and c.is_deleted=0 $prod_id_cond2 $requisition_cond");

    $req_demands_data=array();
	foreach ($sql_demand_reqsn_dtls as $row)
	{
		$reqNo = $row[csf("requisition_no")];
		$prodId = $row[csf("prod_id")];
		$dtlsId = $row[csf("dtls_id")];

		$req_demands_data[$reqNo][$prodId][$dtlsId]['mst_id'] 			= $row[csf("mst_id")];
	 	$req_demands_data[$reqNo][$prodId][$dtlsId]['demand_qty'] 		= $row[csf("yarn_demand_qnty")];
	 	$req_demands_data[$reqNo][$prodId][$dtlsId]['requisition_id'] 	= $row[csf("requisition_id")];
	 	$req_demands_data[$reqNo][$prodId][$dtlsId]['requisition_no'] 	= $row[csf("requisition_no")];		 	
	 	$req_demands_data[$reqNo][$prodId][$dtlsId]['cone_qty'] 		= $row[csf("cone_qty")];
	 	$req_demands_data[$reqNo][$prodId][$dtlsId]['ctn_qty'] 			= $row[csf("ctn_qty")];
	 	$req_demands_data[$reqNo][$prodId][$dtlsId]['remarks'] 			= $row[csf("remarks")];

	}

	$issue_array = $job_wose_issue_array = $issue_basis_arr = array();
	$sql_issue = "select a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no,c.is_sales from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and c.prod_id=d.id and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,7,15,38,46) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $prod_id_cond2  and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,a.issue_basis,a.issue_purpose,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no,c.is_sales";
	//echo $sql_issue; die();

	//echo "<pre>";
	//print_r($booking_allocation_arr);
	//die();

	$result_issue = sql_select($sql_issue);
	$issue_array_req=$booking_arr=array();
	foreach ($result_issue as $row) 
	{
		$issue_job[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("job_no")];
		$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
		$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];
		$po_id = $row[csf("po_breakdown_id")];
		$is_sales = $row[csf("is_sales")];

		if($is_sales==1)
		{
			$job_no = $sales_job_arr[$po_id]['job_no'];
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
		}
		else
		{
			$job_no = $po_number_arr[$po_id]['job_no'];
			$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
		}

		if($row[csf('dyed_type')] == 1) // Except SMN booking
		{
			$booking_arr = $allbooking_with_po_arr[$row[csf('prod_id')]][$job_no];
			$booking_arr = array_unique($booking_arr);

			if(!empty($booking_arr))
			{
				foreach ($booking_arr as $booking_no)
				{
					$expBookinNo = explode("-", $booking_no);
					
					if(($expBookinNo[1] != 'SMN'))
					{
						if( $booking_no != "" )
						{
							$issue_array[$job_no][$booking_no][$po_id][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
						}else{
						
							$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
						}
					}

					$issue_array_req[$po_id][$row[csf("prod_id")]][$booking_no]["issue_id"][] = $row[csf("id")];
				}
			}
			else
			{
				foreach ($job_arr as $job_no)
				{
					$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
					$issue_array_req[$row[csf("prod_id")]][$job_no]["issue_id"][] = $row[csf("id")];
				}
			}			
		}
		else
		{
			$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			if($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")]==1 || $row[csf("issue_basis")] == 8)
			{
				if($row[csf("issue_basis")]==1)
				{
					$booking=0;
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];					
				}
				else
				{
					$booking_arr = $planning_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
					$booking_arr = array_unique($booking_arr);
					foreach ($booking_arr as $booking) {
						$expBookinNo = explode("-", $booking); // Except SMN booking
						if ($expBookinNo[1] != 'SMN' )
						{
							if($booking != "")
							{
								$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
								$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
								$issue_array_issue_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking][$row[csf("id")]][] = $row[csf("requisition_no")];
							}
						}
					}
					$requisition_wise_issue_array[$row[csf("po_breakdown_id")]][$row[csf("requisition_no")]][$row[csf("prod_id")]] += $row[csf("issue_qty")];
				}
			}
			else
			{			
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
			}
		}

		$job_wose_issue_array[$po_number_arr[$row[csf("po_breakdown_id")]]['job_no']] += $row[csf("issue_qty")];
	}
	/*
	echo "10**<pre>";
	print_r($issue_array);
	echo "</pre>";
	die(); */

	$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();

	$sql_return = "Select a.booking_id,a.booking_no,b.requisition_no,b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type,c.is_sales, sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where a.id=b.mst_id and c.trans_type=4 and c.prod_id=d.id and a.entry_form=9 and b.id=c.trans_id and b.item_category=1  and c.issue_purpose in(1,2,7,12,15,38,46) and a.receive_basis not in (2) $prod_id_cond2 and a.status_active=1 and b.status_active=1 and c.status_active=1 group by a.booking_id,a.booking_no,b.requisition_no,b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type,c.is_sales";

	/* echo "10**<pre>";
	print_r($allbooking_with_po_arr);
	echo "</pre>";
	die();  */

	//echo $sql_return; die;
	$result_return = sql_select($sql_return);
	foreach ($result_return as $row) 
	{
		$issue_basis = $issue_basis_arr[$row[csf("issue_id")]]["basis"];
		$po_id = $row[csf("po_breakdown_id")];
		$is_sales = $row[csf("is_sales")];

		if($row[csf('dyed_type')] == 1)
		{
			if($is_sales==1)
			{
				$job_no = $sales_job_arr[$po_id]['job_no'];
				$issue_arr[$po_id][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}
			else
			{
				$job_no = $po_number_arr[$po_id]['job_no'];
				$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}

			$booking_arr = $allbooking_with_po_arr[$row[csf('prod_id')]][$job_no];

			$booking_arr = array_unique($booking_arr);
			if(!empty($booking_arr))
			{
				foreach ($booking_arr as $booking_no)
				{
					if($booking_no != "")
					{
						$job_wose_issue_return_array[$job_no][$booking_no][$po_id][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
					}else{
						$job_wose_issue_return_array[$job_no][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
					}
				}
			}
			else
			{
				foreach ($job_arr as $jobNo) // for old dyed yarn structure 
				{
					$job_wose_issue_return_array[$job_no][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
				}
			}
			
		}
		else
		{
			if($issue_basis == 3 || $issue_basis == 8)
			{
				if($issue_basis == 3)
				{
					$booking_no = return_field_value("booking_no","ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b","a.requisition_no ='".$row[csf('booking_id')]."' and a.prod_id=".$row[csf('prod_id')]." and a.knit_id =b.dtls_id group by booking_no","booking_no");
				}
				else
				{
					$booking_no = return_field_value("booking_no","ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b","a.requisition_no ='".$row[csf('requisition_no')]."' and a.prod_id=".$row[csf('prod_id')]." and a.knit_id =b.dtls_id group by booking_no","booking_no");
				}

				$issue_return_po_array[$row[csf('issue_id')]][$row[csf('prod_id')]] .= $row[csf('po_breakdown_id')].",";
				$rqsn_issue_return_issue_id[$booking_no][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]].= $row[csf('issue_id')].",";

				$issue_return_req_array[$booking_no][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('issue_id')]] += $row[csf("issue_return_qty")];
			}
			else
			{
				if($issue_basis==1)
				{
					if( ( $row[csf("issue_purpose")]==2 || $row[csf("issue_purpose")]==7 || $row[csf("issue_purpose")]==15 || $row[csf("issue_purpose")]==38 || $row[csf("issue_purpose")]==46))
					{
						$booking_id = $row[csf("booking_id")];

						if($row[csf("is_sales")]==1)
						{
							$job_no = $sales_job_arr[$row[csf("po_breakdown_id")]]['job_no'];
						}
						else
						{
							$job_no = $po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
						}

						$booking_no = return_field_value("fab_booking_no","wo_yarn_dyeing_dtls","mst_id ='".$booking_id."' and job_no ='".$job_no."' and is_deleted=0 and status_active=1 group by fab_booking_no","fab_booking_no");

						$booking_no = trim($booking_no); // Remove both side whitespace

						if($booking_no!="")
						{
							$issue_return_array[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
						}
						else
						{
							$booking_no = 0; // for old data picup
							$issue_return_array[$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
						}
					}
				}
				else
				{
					$issue_return_array[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] += $row[csf("issue_return_qty")];
				}
			}
		}
	}
	//echo "10**";

	//echo "<pre>";
	//print_r($issue_return_req_array); die;

	/* echo "<pre>";
	print_r($issue_array_req); die; */



	$smn_booking_cond = (!empty($smn_booking_arr))? " and d.booking_no in (".implode(",",$smn_booking_arr).")" : "";

	$smn_sql="SELECT sum(x.cons_quantity) as issue_qty,x.issue_id, x.booking_no,x.po_id,x.requisition_no,x.prod_id from(
	SELECT distinct a.company_id,a.id as issue_id,a.buyer_id, a.issue_basis, a.issue_purpose, d.booking_no,d.po_id,b.cons_quantity, b.id,b.prod_id,c.requisition_no
	from inv_issue_master a, inv_transaction b, ppl_yarn_requisition_entry c, ppl_planning_entry_plan_dtls d where a.id=b.mst_id and b.requisition_no=c.requisition_no and c.knit_id=d.dtls_id and b.prod_id=c.prod_id and b.transaction_type=2 and b.item_category=1 and  a.issue_purpose=1 and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $smn_booking_cond $prod_id_cond2 ) x group by x.company_id,x.issue_id, x.buyer_id, x.issue_basis, x.issue_purpose, x.booking_no,x.po_id,x.requisition_no,x.prod_id
	union all
	SELECT sum(y.cons_quantity) as issue_qty,y.issue_id, y.booking_no,null as po_id,null as requisition_no,y.prod_id from(
	SELECT distinct a.company_id,a.id as issue_id, a.buyer_id, a.issue_basis, a.issue_purpose, d.booking_no, b.cons_quantity, b.id,b.prod_id
	from inv_issue_master a, inv_transaction b,wo_yarn_dyeing_mst c, wo_yarn_dyeing_dtls d where a.id=b.mst_id and a.booking_no=c.ydw_no and d.product_id=b.prod_id and c.id=d.mst_id and b.transaction_type=2 and b.item_category=1 and  a.issue_purpose=2 and  a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $smn_booking_cond $prod_id_cond3 ) y group by y.company_id,y.issue_id, y.buyer_id, y.issue_basis, y.issue_purpose, y.booking_no,y.prod_id";								
	//echo $smn_sql; die;
	$smn_res=sql_select($smn_sql);
	$sample_issue_booking_qnty = array();
	foreach ($smn_res as $srow) 
	{
		$sample_issue_booking_qnty[$srow[csf('booking_no')]][$srow[csf('prod_id')]]+=$srow[csf('issue_qty')];
		$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$srow[csf('booking_no')]]["issue_id"][] = $row[csf("issue_id")];
		$issue_array_issue_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$srow[csf('booking_no')]][$row[csf("issue_id")]][] = $row[csf("requisition_no")];

		//$issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]

		$issueIdArr[$srow[csf("issue_id")]] = $srow[csf("issue_id")];
	}
	//echo "<pre>";
	//print_r($sample_issue_booking_qnty); die();

	//for smaple without order issue return qty
	$issueIdcond = "";
	if(!empty($issueIdArr))
	{
		$issueIdcond = "and b.issue_id in(".implode(',', $issueIdArr).")";

		$sqlRtnQty = "Select a.receive_basis as RECEIVE_BASIS,a.booking_id as BOOKING_ID, b.issue_id as ISSUE_ID,b.requisition_no as REQUISITION_NO, b.prod_id as PROD_ID, b.order_qnty as RETURN_QTY from inv_receive_master a, inv_transaction b, product_details_master d where a.id=b.mst_id and b.prod_id=d.id and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.receive_basis not in (2) and a.status_active=1 and b.status_active=1 ".$prod_id_cond3." ".$issueIdcond.""; 
		//echo $sqlRtnQty; die;
		$sqlRtnQtyRslt = sql_select($sqlRtnQty);
		
		$sampleReturnQty = array();
		foreach($sqlRtnQtyRslt as $row)
		{
			if($row['RECEIVE_BASIS']==3)
			{
				$smn_booking_no = return_field_value("b.booking_no as BOOKING_NO","ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b","a.knit_id=b.dtls_id and a.requisition_no ='".$row["BOOKING_ID"]."' and a.prod_id ='".$row["PROD_ID"]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","BOOKING_NO");
			}
			else{
				$smn_booking_no = return_field_value("b.booking_no as BOOKING_NO","ppl_yarn_requisition_entry a,ppl_planning_entry_plan_dtls b","a.knit_id=b.dtls_id and a.requisition_no ='".$row["REQUISITION_NO"]."' and a.prod_id ='".$row["PROD_ID"]."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0","BOOKING_NO");
			}

			$sampleReturnQty[$smn_booking_no][$row['PROD_ID']] += $row['RETURN_QTY'];
			
		}	
	}

	//echo "<pre>";
	//print_r($sampleReturnQty); die;
	
	$i = 1;
	$balance = 0;
	$prod_arr = array();

	if(!empty($result_allocation))
	{	
		$balance = $balance_new = $issue_qty = $return_qty = 0;
		$item_arr = $mst_data_update_arr = array();
		foreach ($result_allocation as $row) 
		{
			$prod_id = $row[csf("item_id")];
			$dtls_id = $row[csf("id")];
			if(empty($item_arr)){
				$item_arr[] = $prod_id;
			}else{
				if(!in_array($prod_id, $item_arr)){
					$i = 1;
					$balance = $balance_new = 0;
					unset($item_arr);
					$item_arr[] = $prod_id;
				}
			}

			if($row[csf("po_break_down_id")]=="" || $row[csf("booking_no")]==""){
				$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
			}else{
				$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
			}

			/*echo "10**ttt";
			echo "<pre>";
			print_r($issue_arr)."test";*/

			if($row[csf("is_dyied_yarn")] == 1)
			{
				$booking_arr = $allbooking_with_po_arr[$prod_id][$row[csf("job_no")]];
				$booking_arr = array_unique($booking_arr);

				if(!empty($booking_arr[0]))
				{
					foreach ($booking_arr as $booking_no)
					{
						$expBookinNo = explode("-", $booking_no);
						
						if(($expBookinNo[1] != 'SMN'))
						{
							if( $booking_no != "" )
							{
								$issue_qty += $issue_array[$row[csf("job_no")]][$booking_no][$po_id][$prod_id]["issue_qty"];
							}else{
							
								$issue_qty += $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
							}
						}
						
					}
				}
				else // old structure of dyed yarn
				{
					foreach ($job_arr as $job_no)
					{
						$jobNo = $row[csf("job_no")];
						$issue_qty = $issue_array[$jobNo][$prod_id]["issue_qty"];
					}
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
							$booking_row = 0;
							$issue_qty_wo += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_row]["issue_qty"];
						}
						else
						{
							if($row[csf("booking_no")] != "")
							{
								$issue_qty += $issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]]["issue_qty"];
								$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]]["issue_id"]);
								$requisitions="";
								foreach ($issue_ids as $issue_id) 
								{
									$requisitions_arr = $issue_array_issue_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]][$issue_id];
									foreach ($requisitions_arr as $r_row) {
										$requisitions[$r_row]= $r_row;
									}
								}
							}
						}
					}
					else
					{
						$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
					}
				}

				$expBookinNo = explode("-", $row[csf("booking_no")]);
				if($expBookinNo[1] == 'SMN')
				{
					$issue_qty = $issue_qty+$sample_issue_booking_qnty[$row[csf("booking_no")]][$prod_id];
				}
			}
			
			$within_group=$sales_order_arr[$row[csf('po_breakdown_id')]]['within_group'];
			$sales_booking_no=$sales_order_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];

			if($sales_booking_no!="")
			{
				$return_qty = 0;			
			}
			else
			{

				if($row[csf("is_dyied_yarn")] == 1)
				{
					if( $row[csf("booking_no")]!="" )
					{
						$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$prod_id];
					}
					else
					{
						$return_qty = $job_wose_issue_return_array[$row[csf("job_no")]][$prod_id];
					}

					//for smaple without order issue return qty
					$expBookinNo = explode("-", $row[csf("booking_no")]);
					if($expBookinNo[1] == 'SMN')
					{
						$return_qty = $return_qty+$sampleReturnQty[$row[csf("booking_no")]][$prod_id];
					}
				}
				else
				{
					$return_qty=0;
					$expBookinNo = explode("-", $row[csf("booking_no")]);
					foreach ($issue_basis as $basis)
					{
						if( $basis==3 || $basis==8 )
						{
							if($expBookinNo[1] != 'SMN')
							{
								$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$row[csf("booking_no")]]["issue_id"]);
								foreach ($issue_ids as $issue_id)
								{
									$return_qty += $issue_return_req_array[$row[csf("booking_no")]][$row[csf('po_break_down_id')]][$prod_id][$issue_id];
								}
							}
						}
						else
						{
							if($basis==1) // booking basis-- work order
							{
								if($expBookinNo[1] != 'SMN')
								{
									$return_qty += $issue_return_array[$row[csf("booking_no")]][$row[csf("po_break_down_id")]][$prod_id];
								}
							}
							else
							{
								$return_qty += $issue_return_array[$row[csf("po_break_down_id")]][$prod_id];
							}
						}
					}

					//for smaple without order issue return qty
					if($expBookinNo[1] == 'SMN')
					{
						$return_qty = $return_qty+$sampleReturnQty[$row[csf("booking_no")]][$prod_id];
					}
				}
			}

			$issue_qty = $issue_qty+$issue_qty_wo;

			$booking_no = implode(", ",array_unique(explode(",", $row[csf("booking_no")])));

			$balance = $balance + (number_format($row[csf("allocate_qty")], 2,".","") + number_format($return_qty, 2,".","")) - number_format($issue_qty, 2,".","");
			$issue_qty_new = number_format($issue_qty, 2,".","");
			$return_qty_new = number_format($return_qty, 2,".","");


			//echo $requisitions;
			
			//$requisitions = array_unique(explode(",",rtrim($requisitions,",")));
			//echo "<pre>";
			//print_r($requisitions);
			

			$requisition_nos = "";
			$total_requisition_qnty=0;
			$requisiton_arr=array();

			foreach ($requisitions as $req_no) 
			{
				//echo $row[csf("po_break_down_id")]."==".$booking_no."==".$req_no."==".$prod_id."<br>";  
				$total_requisition_qnty += $planning_array[$row[csf("po_break_down_id")]][$booking_no][$req_no][$prod_id]['req_qnty'];

				$requisition_issue_qnty = $requisition_wise_issue_array[$row[csf("po_break_down_id")]][$req_no][$prod_id];


				$req_qnty = $requisition_qnty_array[$row[csf('po_break_down_id')]][$req_no][$prod_id];
				$requisition_nos .= "Req. no=".$req_no.", Req. Qnty=".number_format($req_qnty,2).",Issue=".number_format($requisition_issue_qnty,2).",\n";
				$requisiton_arr[$dtls_id][$booking_no][$row[csf("po_break_down_id")]][$req_no] += $req_qnty;
				$dtls_data_update_arr[$dtls_id]["po_requisition_qty"] = $requisiton_arr;
				if($req_qnty<$requisition_issue_qnty){
					$inc_requisition_qnty = number_format($requisition_issue_qnty-$req_qnty,2,".","");
				}
			}

			//echo $booking_no."==".$req_no."==".$total_requisition_qnty."<br>"; 

			$requisition_qnty = $total_requisition_qnty;
			$requisition_nos=rtrim($requisition_nos,", ");

			if($issue_qty_new > 0)
			{
				if($requisition_qnty < $issue_qty_new)
				{
					$new_allocation = number_format(($issue_qty_new-$return_qty_new),2,".","");
				}else
				{
					$new_allocation = number_format(($requisition_qnty-$return_qty_new),2,".","");
				}
			}
			else
			{
				$new_allocation = number_format($row[csf("allocate_qty")],2,".","");
			}

			$balance_new = $balance_new + ($new_allocation + $return_qty_new) - $issue_qty_new;

			$dtls_data_update_arr[$dtls_id]["qnty"] += $new_allocation;
			$dtls_data_update_arr[$dtls_id]["pre_qnty"] += $row[csf("allocate_qty")];
			$dtls_data_update_arr[$dtls_id]["po_break_down_id"] = $row[csf("po_break_down_id")];
			$dtls_data_update_arr[$dtls_id]["job_no"] = $row[csf("job_no")];
			$dtls_data_update_arr[$dtls_id]["prod_id"] = $prod_id;
			$dtls_data_update_arr[$dtls_id]["mst_id"] = $row[csf("mst_id")];
			$dtls_data_update_arr[$dtls_id]["booking_no"] = $booking_no;
			$dtls_data_update_arr[$dtls_id]["issue_qty_new"] = $issue_qty_new;
			$dtls_data_update_arr[$dtls_id]["return_qty_new"] = $return_qty_new;
			$dtls_data_update_arr[$dtls_id]["requisition_ids"] = rtrim($planning_array[$row[csf("po_break_down_id")]][$prod_id]['req_id'],", ");
			$dtls_data_update_arr[$dtls_id]["requisition_nos"] = rtrim($planning_array[$row[csf('po_break_down_id')]][$booking_no][$prod_id]['requisition_no'],", ");
			$dtls_data_update_arr[$dtls_id]["requisition_qty"] = $requisition_qnty;

			$dtls_balance_arr[$prod_id]["balance"] = $balance;
			$i++;
		}

		/*
		echo "10**";
		echo "<pre>";
		print_r($dtls_data_update_arr); die();*/
		 
		$balance1=$balance=0;
		$item_arr=$req_a=$req_issue_qnty=array();
		foreach ($dtls_data_update_arr as $dtl_id => $dtl_row) 
		{
			$current_stock = $product_array[$dtl_row["prod_id"]]['current_stock'];
			$mst_id   = $dtl_row["mst_id"];
			$booking_no   = $dtl_row["booking_no"];
			$balance = $dtls_balance_arr[$dtl_row["prod_id"]]["balance"];
			
			//if($dtls_balance_arr[$dtl_row["prod_id"]]["balance"] >=$txt_value_from && $dtls_balance_arr[$dtl_row["prod_id"]]["balance"] <=$txt_value_to)
			//{
				$dtl_qnty = $dtl_row["qnty"];
				$pre_qnty = $dtl_row["pre_qnty"];

				$requisitions 	 = array_unique(explode(",",$dtl_row["requisition_ids"]));
				$requisition_nos = array_unique(explode(",",$dtl_row["requisition_nos"]));
				$requisition_qty = $dtl_row["requisition_qty"];

				$po_requisition_qty = $dtl_row["po_requisition_qty"];

				if($current_stock >= $balance)
				{
					if(empty($item_arr)){
						$item_arr[] = $dtl_row["prod_id"];
						$balance1 = ($dtl_row["issue_qty_new"] - $dtl_row["return_qty_new"]);
					}else{
						if(!in_array($dtl_row["prod_id"], $item_arr)){
							$balance1 = ($dtl_row["issue_qty_new"] - $dtl_row["return_qty_new"]);
							unset($item_arr);
							$item_arr[] = $dtl_row["prod_id"];
						}else{
							$balance1 = ($dtl_row["issue_qty_new"] - $dtl_row["return_qty_new"]);
						}
					}

					if($dtl_row["issue_qty_new"] > $requisition_qty)
					{
						$allocation = ($dtl_row["issue_qty_new"] - $dtl_row["return_qty_new"]);
						$allocation = number_format($dtl_qnty,2,".","");
						//echo "10**UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id;<br />";
						$dtls_id_array[]=$dtl_id;
						$data_array_dtls_update[$dtl_id]=explode("*",("".$allocation."*'999'*'".$pc_date_time."'"));
						//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");
						foreach ($requisition_nos as $req_no) 
						{
							$req_issue_qnty = $requisition_wise_issue_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
							$po_req_qnty = $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
							$requisition_return_qnty = $issue_return_req_array[$dtl_row["po_break_down_id"]][$dtl_row["prod_id"]][$req_no];
							//$req_a[$req_no][$dtl_row["prod_id"]] += $po_req_qnty + ($req_issue_qnty-$po_req_qnty);
							
							$expBookinNo = explode("-", $booking_no); // Except SMN booking
							if ($expBookinNo[1] == 'SMN' )
							{
								$requisition_return_qnty = number_format($sampleReturnQty[$booking_no][$dtl_row["prod_id"]],2);
							}
							else
							{
								$requisition_return_qnty = $issue_return_req_array[$dtl_row["po_break_down_id"]][$dtl_row["prod_id"]][$req_no][$issueId];
							}

							$req_a[$req_no][$dtl_row["prod_id"]] += ($req_issue_qnty-$requisition_return_qnty);
						}
					}
					else
					{
						$allocation = number_format(($dtl_row["issue_qty_new"] - $dtl_row["return_qty_new"]),2,".","");
						//echo "10**UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id;<br />";
						$dtls_id_array[]=$dtl_id;
						$data_array_dtls_update[$dtl_id]=explode("*",("".$allocation."*'999'*'".$pc_date_time."'"));
						//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");
						foreach ($requisition_nos as $req_no) {
							$req_issue_qnty = $requisition_wise_issue_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];
							$po_req_qnty = $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];

							$expBookinNo = explode("-", $booking_no); // Except SMN booking
							
							if ($expBookinNo[1] == 'SMN' )
							{
								$requisition_return_qnty = number_format($sampleReturnQty[$booking_no][$dtl_row["prod_id"]],2);
							}
							else
							{
								$requisition_return_qnty = $issue_return_req_array[$dtl_row["po_break_down_id"]][$dtl_row["prod_id"]][$req_no][$issueId];
							}
							
							$req_a[$req_no][$dtl_row["prod_id"]] += ($req_issue_qnty-$requisition_return_qnty);
						}
					}

				}
				else
				{
					if($dtl_row["issue_qty_new"] > 0){
						$allocation = number_format(($dtl_row["issue_qty_new"]-$dtl_row["return_qty_new"]),2,".","");
						$balance1 += $allocation;
					}else{
						$allocation = 0;
					}

					$dtls_id_array[] = $dtl_id;
					$data_array_dtls_update[$dtl_id]=explode("*",("".$allocation."*'999'*'".$pc_date_time."'"));

					foreach ($requisition_nos as $req_no) {
						$req_issue_qnty = $requisition_wise_issue_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]]*1;
						if($req_issue_qnty > 0){
							$po_req_qnty = $requisition_qnty_array[$dtl_row["po_break_down_id"]][$req_no][$dtl_row["prod_id"]];							
							$requisition_return_qnty += $issue_return_req_array[$dtl_row["po_break_down_id"]][$dtl_row["prod_id"]][$req_no][$issueId];
							//$req_a[$req_no][$dtl_row["prod_id"]] += $po_req_qnty + ($req_issue_qnty-$po_req_qnty);
							$req_a[$req_no][$dtl_row["prod_id"]] += ($req_issue_qnty-$requisition_return_qnty);
						}
					}

					//echo "10**UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id and mst_id=$mst_id; <br />";
					//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_dtls set qnty=$allocation where id=$dtl_id");
				}

				$mst_data_update_arr[$dtl_row["mst_id"]]["qnty"] += $allocation;
				$mst_data_update_arr[$dtl_row["mst_id"]]["prod_id"] = $dtl_row["prod_id"];
				$mst_data_update_arr[$dtl_row["mst_id"]]["balance"] = $balance1;
				$mst_data_update_arr[$dtl_row["mst_id"]]["current_stock"] = $product_array[$dtl_row["prod_id"]]['current_stock'];
				$mst_data_update_arr[$dtl_row["mst_id"]]["qnty_break_down"] .= number_format($allocation,2,".","") . "_" . $dtl_row["po_break_down_id"] . "_" . $dtl_row["job_no"] . ",";
			//}
		}

		$prod_arr=array();
		foreach ($mst_data_update_arr as $mst_id => $mst_row) 
		{
			$prod_id = $mst_row["prod_id"];
			$current_stock = $mst_row["current_stock"];
			$qnty = number_format($mst_row["qnty"],2,".","");
			$balance = $mst_row["balance"];
			$qnty_break_down = trim($mst_row["qnty_break_down"],", ");

			if($current_stock >= $balance)
			{
				$allocation = $qnty;
				//echo "10**UPDATE inv_material_allocation_mst set qnty='$allocation',qnty_break_down='$qnty_break_down' where id=$mst_id; <br />";
				//$update_allocation_mst=execute_query("UPDATE inv_material_allocation_mst set qnty='$allocation',qnty_break_down='$qnty_break_down' where id=$mst_id");

				$mst_id_array[]=$mst_id;
				$data_array_mst_update[$mst_id]=explode("*",("".$qnty."*'".$qnty_break_down."'*'999'*'".$pc_date_time."'"));

				$prod_arr[$prod_id]["current_stock"] = $current_stock;
				$prod_arr[$prod_id]["allocated"] = number_format($balance,2,".","");
				$prod_arr[$prod_id]["available"] = number_format(($current_stock-$balance),2,".","");
			}
			else
			{
				if($current_stock < $balance){
					//echo "10**UPDATE inv_material_allocation_mst set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id;<br />";
					//$update_allocation_dtls=execute_query("UPDATE inv_material_allocation_mst set qnty='$qnty',qnty_break_down='$qnty_break_down' where id=$mst_id");

					$mst_id_array[]=$mst_id;
					$data_array_mst_update[$mst_id]=explode("*",("".$qnty."*'".$qnty_break_down."'*'999'*'".$pc_date_time."'"));

					$prod_arr[$prod_id]["current_stock"] = $current_stock;
					$prod_arr[$prod_id]["allocated"] = 0;
					$prod_arr[$prod_id]["available"] = number_format($current_stock,2,".","");
				}
			}
		}

		//echo "<pre>";
		//print_r($req_a); 

		foreach ($req_a as $req_no => $product) 
		{
			if($req_no!="")
			{
				foreach ($product as $product_id => $req_qnty) 
				{
					//echo "10**UPDATE ppl_yarn_requisition_entry set yarn_qnty=".number_format($req_qnty,2,".","")." where requisition_no=$req_no and prod_id=$product_id;"; die();
					execute_query("UPDATE ppl_yarn_requisition_entry set yarn_qnty=".number_format($req_qnty,2,".","")." where requisition_no=$req_no and prod_id=$product_id");
					//$req_no_array[]=$req_no;
					//$data_array_requisition_update[$req_no]=explode("*",("".$req_qnty."*'999'*'".$pc_date_time."'"));

					foreach ($req_demands_data[$req_no][$product_id] as $demand_dtls_id => $data) 
					{	//echo "<pre>";
						//print_r($data);
					}
					
				}
			}
		} 

		foreach ($prod_arr as $pr_id => $prod_row) 
		{
			//echo "10**UPDATE product_details_master set allocated_qnty=0,available_qnty='".$prod_row['current_stock']."' where id=$pr_id;<br />";
			//$update_allocation_mst=execute_query("UPDATE product_details_master set allocated_qnty='".$prod_row['allocated']."',available_qnty='".$prod_row['available']."' where id=$pr_id");
			$prID_array[]=$pr_id;
			$data_array_product_update[$pr_id]=explode("*",("0*".$prod_row['current_stock']."*'999'*'".$pc_date_time."'"));
		}
	}
	else
	{
		echo "Data Not found in allocation tables";
	}
	
	/*
	echo "10**";
	echo "<pre>";
	print_r($data_array_dtls_update);
	die();*/

	//echo "10**". bulk_update_sql_statement("inv_material_allocation_mst","id",$field_array_mst_update,$data_array_mst_update,$mst_id_array); die();

	//$rID4=execute_query(bulk_update_sql_statement("ppl_yarn_requisition_entry","requisition_no",$field_array_requisiton_update,$data_array_requisition_update,$req_no_array),1);

	$field_array_mst_update 	= "qnty*qnty_break_down*updated_by*update_date";
	$field_array_dtls_update 	= "qnty*updated_by*update_date";
	$field_array_product_update = "allocated_qnty*available_qnty*updated_by*update_date";
	//$field_array_requisiton_update="yarn_qnty*updated_by*update_date";
	$rID  =execute_query(bulk_update_sql_statement("inv_material_allocation_mst","id",$field_array_mst_update,$data_array_mst_update,$mst_id_array),1);
	$rID2 =execute_query(bulk_update_sql_statement("inv_material_allocation_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$dtls_id_array),1);
	$rID3 =execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product_update,$data_array_product_update,$prID_array),1);
	

	//echo "10**".$rID ."&&". $rID2 ."&&". $rID3; die;
	
	if($db_type==0)
	{
		if($rID && $rID2 && $rID3 )
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
		if($rID && $rID2 && $rID3 )
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

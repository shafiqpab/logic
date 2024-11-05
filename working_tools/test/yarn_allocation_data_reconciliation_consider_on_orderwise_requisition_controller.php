<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$employee_id = $_SESSION['logic_erp']['employee_id'];

//die("You are not authenticated");

if($employee_id!='SUPERADMIN')
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

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_id=str_replace("'","",$company_id);
	//$company_cond 	= ($company_id>0)?" and b.company_id=$company_id":"";

	//$prodCond = " and a.prod_id in (643686,627405,628633)";

	$orderwise_requisition_mismatch_sql = "SELECT distinct(item_id),
	LISTAGG (a.knit_id, ',') WITHIN GROUP (ORDER BY a.knit_id)
             AS prog_no,
    a.id as requisition_pk_id, 
	LISTAGG(requisition_id, ',') WITHIN GROUP (ORDER BY requisition_id) AS requisition_no,
	LISTAGG(order_id, ',') WITHIN GROUP (ORDER BY order_id) AS order_id,b.booking_no,
	a.yarn_qnty,SUM(order_requisition_qty) AS order_requisition_qty
	FROM ppl_yarn_requisition_entry a,ppl_yarn_requisition_breakdown b
	WHERE a.prod_id=b.item_id  AND a.requisition_no=b.requisition_id AND b.booking_no NOT LIKE '%SMN%'
	AND a.status_active=1 AND a.is_deleted=0 AND b.status_active=1 AND b.is_deleted=0 AND TO_CHAR(a.INSERT_DATE,'YYYY') = 2021 $prodCond
	group by b.item_id,a.id,b.booking_no,a.yarn_qnty
	HAVING SUM(b.order_requisition_qty)!=a.yarn_qnty"; //and a.prod_id=627405 and a.knit_id in (66045,66154) and b.booking_no='FKTL-Fb-21-00095'

	echo $orderwise_requisition_mismatch_sql;

	$result_requisition = sql_select($orderwise_requisition_mismatch_sql);

	$requsition_array = array();
	$planning_array = array();
	foreach ($result_requisition as $row) 
	{			
		$booking_no = $row[csf("booking_no")];
		$item_id = $row[csf("item_id")];
		$requisition_pk_id = $row[csf("requisition_pk_id")];
		$requisition_no = $row[csf("requisition_no")];	
		$prog_no = $row[csf("prog_no")];

		$expRequsitionNo = explode(",", $row[csf("requisition_no")]);	
		$requisition_no = $expRequsitionNo[0];
		$expProgNo = explode(",", $row[csf("prog_no")]);
		$prog_no  = $expProgNo[0];	

		$expOrderIds = explode(",", $row[csf("order_id")]);	
		foreach ($expOrderIds as $po_id) 
		{
			$requsition_array[$requisition_pk_id][$prog_no][$requisition_no][$booking_no][$po_id][$item_id]['prog_no'] = $prog_no;
			$requsition_array[$requisition_pk_id][$prog_no][$requisition_no][$booking_no][$po_id][$item_id]['requisition_no'] = $requisition_no;

			$requsition_array[$requisition_pk_id][$prog_no][$requisition_no][$booking_no][$po_id][$item_id]['requisition_pk_id'] = $row[csf("requisition_pk_id")];

			$planning_po_array[$prog_no][$booking_no][$po_id] = $po_id;
			$planning_booking_array[$po_id][$requisition_no][$item_id][] = $booking_no;
		}

		$item_arr[] = $row[csf("item_id")];
		$requisition_no_arr[$requisition_no] = $requisition_no;
	}

	/*
	echo "<pre>";
	print_r($requsition_array);
	die();*/
	

	if($db_type==2 && count($item_arr)>1000)
	{
		$prod_id_cond=" and (";
		$prod_id_cond2=" and (";
		$item_id_cond=" and (";

		$proIdArr=array_chunk(array_unique($item_arr),999);
		foreach($proIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$prod_id_cond.=" id in($ids) or ";
			$prod_id_cond2.=" c.prod_id in($ids) or ";
			$item_id_cond.=" a.item_id in($ids) or ";

		}

		$prod_id_cond=chop($prod_id_cond,'or ');
		$prod_id_cond.=")";

		$prod_id_cond2=chop($prod_id_cond2,'or ');
		$prod_id_cond2.=")";

		$item_id_cond=chop($item_id_cond,'or ');
		$item_id_cond.=")";
	}
	else
	{
		$prod_id_cond=" and id in (".implode(",",array_unique($item_arr)).")";
		$prod_id_cond2=" and c.prod_id in (".implode(",",array_unique($item_arr)).")";
		$item_id_cond=" and a.item_id in (".implode(",",array_unique($item_arr)).")";
	}
	
	$sql_products = "select a.id, a.company_id, a.lot,a.product_name_details,a.current_stock, a.allocated_qnty, a.available_qnty, a.avg_rate_per_unit,dyed_type from product_details_master a where a.item_category_id=1 $prod_id_cond and a.status_active=1 and a.is_deleted=0";
	//echo $sql_products; die();

	$product_array=array();
	$productData = sql_select($sql_products);
	foreach( $productData as $prod_val)
	{
		$product_array[$prod_val[csf('id')]]['lot']						= $prod_val[csf('lot')];
		$product_array[$prod_val[csf('id')]]['product_name_details']	= $prod_val[csf('product_name_details')];
		$product_array[$prod_val[csf('id')]]['current_stock']			= $prod_val[csf('current_stock')];
		$product_array[$prod_val[csf('id')]]['allocated_qnty']			= $prod_val[csf('allocated_qnty')];
		$product_array[$prod_val[csf('id')]]['available_qnty']			= $prod_val[csf('available_qnty')];
		$product_array[$prod_val[csf('id')]]['dyed_type']				= $prod_val[csf('dyed_type')];
	}

	if($db_type==2 && count($requisition_no_arr)>1000)
	{
		$requisition_no_cond=" and (";

		$reqnoArr=array_chunk(array_unique($requisition_no_arr),999);
		foreach($issueIdArr as $reqNos)
		{
			$reqNos=implode(",",$reqNos);
			$requisition_no_cond.=" b.requisition_no in($reqNos) or ";
		}

		$requisition_no_cond=chop($issue_id_cond,'or ');
		$requisition_no_cond.=")";
	}
	else
	{
		$requisitionNoArr = array_unique($requisition_no_arr);
		$requisition_no_cond=" and b.requisition_no in (".implode(",",$requisitionNoArr).")";
	}

	$issue_array = $job_wose_issue_array = $issue_basis_arr = array();
	$sql_issue = "select a.id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, sum(c.quantity) as issue_qty,b.job_no from inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and c.prod_id=d.id and a.issue_basis=3 and a.issue_purpose in(1) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 $prod_id_cond2  $requisition_no_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 group by a.id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type,b.job_no order by c.po_breakdown_id";

	//echo $sql_issue; die();

	$result_issue = sql_select($sql_issue);
	$issue_array_req=$booking_arr=array();
	foreach ($result_issue as $row) 
	{
		$booking_arr = $planning_booking_array[$row[csf('po_breakdown_id')]][$row[csf('requisition_no')]][$row[csf('prod_id')]];
		$booking_arr = array_unique($booking_arr);
		
		foreach ($booking_arr as $booking) 
		{
			if($booking != "")
			{

				$requisition_wise_issue_array[$row[csf("id")]][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking][$row[csf("requisition_no")]] += $row[csf("issue_qty")];

				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
				$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
				
				$issue_array_issue_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking][$row[csf("id")]][] = $row[csf("requisition_no")];
				$issue_po_arr[$booking][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]] = $row[csf("po_breakdown_id")];	
				$issue_id_arr[] = $row[csf("id")];				
			}
		}							
	}

	//echo "<pre>";
	//print_r($issue_array_req);
	//die();

	if($db_type==2 && count($issue_id_arr)>1000)
	{
		$issue_id_cond=" and (";

		$issueIdArr=array_chunk(array_unique($issue_id_arr),999);
		foreach($issueIdArr as $ids)
		{
			$ids=implode(",",$ids);
			$issue_id_cond.=" b.issue_id in($ids) or ";
		}

		$issue_id_cond=chop($issue_id_cond,'or ');
		$issue_id_cond.=")";
	}
	else
	{
		$issueIdArr=array_unique($issue_id_arr);
		$issue_id_cond=" and b.issue_id in (".implode(",",$issueIdArr).")";
	}

	$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();
	$sql_return = "Select b.issue_id,c.po_breakdown_id,c.prod_id,sum(c.quantity) as issue_return_qty from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and b.transaction_type=4 and c.trans_type=4 and a.entry_form=9 and b.item_category=1 and a.receive_basis=3 and c.issue_purpose in(1) $prod_id_cond2 $issue_id_cond and a.status_active=1 and b.status_active=1 and c.status_active=1 group by b.issue_id,c.po_breakdown_id,c.prod_id order by c.po_breakdown_id";

	//echo $sql_return; die();

	$result_return = sql_select($sql_return);
	foreach ($result_return as $row) 
	{
		$issue_return_req_array[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$row[csf('issue_id')]] = $row[csf("issue_return_qty")];
	}

	//echo "<pre>";
	//print_r($issue_return_req_array);
	//die();

	if ($db_type == 0) {
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,group_concat(booking_no) booking_no, sum(qnty) as allocate_qty, sum(a.qnty) as allocate_qty,a.allocation_date,a.is_sales,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id AND a.booking_no NOT LIKE '%SMN%' and a.status_active=1 and a.is_deleted=0 and and a.qnty>0 and b.status_active=1 and b.is_deleted=0  $item_id_cond group by a.item_id,a.job_no,a.po_break_down_id,a.allocation_date,a.is_sales,b.dyed_type,a.id,a.mst_id";
	}else{
		$sql_allocation = "select a.id,a.mst_id,a.item_id,a.job_no, a.po_break_down_id,listagg(cast(a.booking_no as varchar2(4000)), ',') within group (order by a.booking_no) as booking_no, sum(a.qnty) as allocate_qty,a.allocation_date,a.is_sales,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id AND a.booking_no NOT LIKE '%SMN%' and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and b.status_active=1 and b.is_deleted=0 $item_id_cond  group by a.item_id,a.job_no,a.po_break_down_id,a.allocation_date,a.is_sales,b.dyed_type,a.id,a.mst_id order by a.item_id,a.po_break_down_id";

	}

	//echo $sql_allocation; die();
	
	$balance = 0;	
	$issue_qty=0;
	$return_qty = 0;

	$prod_arr = array();
	$result_allocation = sql_select($sql_allocation);
	if(!empty($result_allocation))
	{
		//echo "10**test"; die();
		$balance = $balance_new = $issue_qty = $return_qty = 0;
		$item_arr = $mst_data_update_arr = array();
		foreach ($result_allocation as $row) 
		{
			$prod_id = $row[csf("item_id")];
			$dtls_id = $row[csf("id")];	
		
			$booking_nos = explode(",",$row[csf("booking_no")]);

			$issue_qty = 0;
			if($row[csf("booking_no")] != "")
			{
				foreach ($booking_nos as $booking_no) 
				{
					$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_no]["issue_id"]);

					$requisitions="";
					$return_qty = 0;
					foreach ($issue_ids as $issue_id) 
					{					
						$requisitions_arr = $issue_array_issue_req[$row[csf("po_break_down_id")]][$prod_id][$booking_no][$issue_id];
						foreach ($requisitions_arr as $r_row) 
						{
							$issue_qty +=$requisition_wise_issue_array[$issue_id][$row[csf("po_break_down_id")]][$prod_id][$booking_no][$r_row];
							$requisitions .= $r_row.",";
						}

						$return_qty += $issue_return_req_array[$row[csf('po_break_down_id')]][$prod_id][$issue_id];
					}
				}
			}
			
			$issue_qty = number_format($issue_qty, 2,".","");
			$return_qty = number_format($return_qty, 2,".","");

			$requisition_balance = number_format(($issue_qty-$return_qty), 2,".","");	

			if( $requisition_balance > 0 )
			{
				if( number_format($row[csf("allocate_qty")],2,".","") >$requisition_balance )
				{
					$new_allocation = number_format($row[csf("allocate_qty")],2,".","");
					$increment_allocated=0;
				}
				else
				{
					$new_allocation = number_format($requisition_balance,2,".","");		
					$increment_allocated = (number_format($requisition_balance,2,".","")-number_format($row[csf("allocate_qty")],2,".",""));				
				}
			}
			else
			{
				$new_allocation = number_format($row[csf("allocate_qty")],2,".","");
				$increment_allocated=0;

			}
	
			//$balance = $balance + (number_format($row[csf("allocate_qty")], 2,".","") + $return_qty - $issue_qty);

			$booking_no = implode(", ",array_unique(explode(",", $row[csf("booking_no")])));

			$dtls_data_update_arr[$dtls_id]["qnty"] += $new_allocation;
			$dtls_data_update_arr[$dtls_id]["pre_qnty"] += $row[csf("allocate_qty")];
			$dtls_data_update_arr[$dtls_id]["po_break_down_id"] = $row[csf("po_break_down_id")];
			$dtls_data_update_arr[$dtls_id]["job_no"] = $row[csf("job_no")];
			$dtls_data_update_arr[$dtls_id]["prod_id"] = $prod_id;
			$dtls_data_update_arr[$dtls_id]["mst_id"] = $row[csf("mst_id")];
			$dtls_data_update_arr[$dtls_id]["booking_no"] = $booking_no;
			$dtls_data_update_arr[$dtls_id]["issue_qty_new"] = $issue_qty;
			$dtls_data_update_arr[$dtls_id]["return_qty_new"] = $return_qty;

			$issueRequisitionsArr = array_unique(explode(",",rtrim($requisitions,", ")));

			$issuePo = $issue_po_arr[$booking_no][$row[csf("po_break_down_id")]][$prod_id];

			if($issuePo==$row[csf("po_break_down_id")]) // issue po == allocaiton po?
			{
				$orderWiseRequisitionQty = array();
				foreach ($requsition_array as $requisitionPK=> $prog_arr) 
				{										
					foreach($prog_arr as $progNo=>$requisitionArr)
					{
						foreach($requisitionArr as $requisitionNo=>$bookingArr)
						{
							if(in_array($requisitionNo, $issueRequisitionsArr)) // issue requisition check
							{
								$program_po = $planning_po_array[$progNo][$booking_no][$row[csf("po_break_down_id")]];
								
								if($program_po==$issuePo) // program po == allocation 
								{
									if(number_format(($issue_qty-$return_qty), 2,".","")>=0)
									{
										$orderWiseRequisition[$booking_no][$progNo][$requisitionNo][$prod_id][$issuePo] = number_format(($issue_qty-$return_qty), 2,".","");
									}
									else
									{
										$orderWiseRequisition[$booking_no][$progNo][$requisitionNo][$prod_id][$issuePo] = 0;
									}

								}
							}
						}					
					}


					$requisitionsWiseTotalQty[$requisitionPK][$progNo][$requisitionNo][$prod_id] += $orderWiseRequisition[$booking_no][$progNo][$requisitionNo][$prod_id][$issuePo];
				}
			}

			
			$newAllocated = ($product_array[$prod_id]['allocated_qnty']+$increment_allocated);
			$dtls_balance_arr[$prod_id]["allocated_qnty"] = $newAllocated;
			
			$i++;
		}

	}
	else
	{
		echo "Data Not found in allocation tables";
	}

	echo "<pre>";
	print_r($orderWiseRequisition);
	echo "<br>==";
	print_r($requisitionsWiseTotalQty);
	echo "<br>==";
	print_r($dtls_data_update_arr);
	echo "<br>==";
	print_r($dtls_balance_arr);

	die();

	$field_array_mst_update 	= "qnty*qnty_break_down*updated_by*update_date";
	$field_array_dtls_update 	= "qnty*updated_by*update_date";
	$field_array_product_update = "allocated_qnty*available_qnty*updated_by*update_date";
	//$field_array_requisiton_update="yarn_qnty*updated_by*update_date";
	$rID  =execute_query(bulk_update_sql_statement("inv_material_allocation_mst","id",$field_array_mst_update,$data_array_mst_update,$mst_id_array),1);
	$rID2 =execute_query(bulk_update_sql_statement("inv_material_allocation_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$dtls_id_array),1);
	$rID3 =execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_product_update,$data_array_product_update,$prID_array),1);
	

	//echo "10**".$rID ."&&". $rID2 ."&&". $rID3 ."&&". $rID4; die;
	
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

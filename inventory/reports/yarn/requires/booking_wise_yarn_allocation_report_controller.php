<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------

//load drop down supplier
if ($action == "load_drop_down_supplier")
{
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select")
{
	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	exit();
}

if ($action == "generate_report")
{
	$started = microtime(true);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
    $con = connect();

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$companyArr[0] = "All Company";
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
	$yarnTestArr = return_library_array("select prod_id, lot_number from inv_yarn_test_mst where status_active=1 and is_deleted=0", 'prod_id', 'lot_number');

	if ($db_type == 0)
	{
		$from_date = change_date_format($from_date, 'yyyy-mm-dd');
		$to_date = change_date_format($to_date, 'yyyy-mm-dd');
	}
	else if ($db_type == 2)
	{
		
		$from_date = change_date_format($from_date, '', '', 1);
		$to_date = change_date_format($to_date, '', '', 1);
	}
	else
	{
		$from_date = "";
		$to_date = "";
	}

	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);
	
	//===========

	    $buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

		$search_cond = "";
		if ($cbo_yarn_type == 0)
			$search_cond .= "";
		else
			$search_cond .= " and b.yarn_type in ($cbo_yarn_type)";
		if ($txt_count == "")
			$search_cond .= "";
		else
			$search_cond .= " and b.yarn_count_id in($txt_count)";
		if ($txt_lot_no == "")
			$search_cond .= "";
		else
			$search_cond .= " and trim(b.lot)='" . trim($txt_lot_no) . "'";

		if ($cbo_supplier == 0)
			$search_cond .= "";
		else
			$search_cond .= "  and b.supplier_id in($cbo_supplier)";
		if ($txt_composition == "")
			$search_cond .= "";
		else
			$search_cond .= " and b.yarn_comp_type1st in (" .$txt_composition_id .")";
		if ($cbo_company_name == 0)
			$search_cond .= "";
		else
			$search_cond .= " and b.company_id=$cbo_company_name";
		if ($cbo_dyed_type == 0)
			$search_cond .= "";
		else
			$search_cond .= " and b.dyed_type=$cbo_dyed_type";

		if ($cbo_company_name == 0) {
			$company_cond = "";
		} else {
			$company_cond = " and b.company_id=$cbo_company_name";
		}

		if($db_type == 0) 
		{

			$sql_allocation = "select a.item_id,a.job_no, a.po_break_down_id,group_concat(booking_no) booking_no, sum(qnty) as allocate_qty, sum(a.qnty) as allocate_qty,a.insert_date,a.allocation_date,a.is_sales,b.company_id, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.lot, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and b.status_active=1 and b.is_deleted=0 $search_cond $company_cond group by b.company_id,a.item_id,b.lot, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.allocated_qnty, b.available_qnty,b.avg_rate_per_unit,b.dyed_type,a.job_no,a.po_break_down_id,a.insert_date,a.allocation_date,a.is_sales";
		}
		else
		{	
			//$sql_allocation = "select b.company_id, b.id, a.item_id,a.job_no, a.po_break_down_id,listagg(cast(a.booking_no as varchar2(4000)), ',') within group (order by a.booking_no) as booking_no, d.booking_date, sum(a.qnty) as allocate_qty,a.insert_date,a.allocation_date,a.is_sales, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.lot, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit,b.dyed_type is_dyied_yarn,c.booking_without_order from inv_material_allocation_dtls a left join wo_booking_mst d on a.booking_no=d.booking_no and a.status_active=1 and d.is_deleted=0,inv_material_allocation_mst c,product_details_master b where a.mst_id=c.id and c.item_id=a.item_id and a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and b.status_active=1 and b.is_deleted=0 $search_cond $company_cond group by b.company_id,b.id,a.item_id,b.lot, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.allocated_qnty, b.available_qnty,b.avg_rate_per_unit,b.dyed_type,c.booking_without_order,a.job_no,a.po_break_down_id,d.booking_date,a.insert_date,a.allocation_date,a.is_sales";

			$sql_allocation = "select b.company_id, b.id, a.item_id,a.job_no, a.po_break_down_id,a.booking_no, d.booking_date as booking_date, sum(a.qnty) as allocate_qty,a.insert_date,a.allocation_date,a.is_sales, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.lot, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit,b.dyed_type is_dyied_yarn,c.booking_without_order from inv_material_allocation_dtls a left join wo_booking_mst d on a.booking_no = d.booking_no,inv_material_allocation_mst c,product_details_master b where a.mst_id=c.id and c.item_id=a.item_id and a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and b.status_active=1 and b.is_deleted=0 $search_cond $company_cond group by b.company_id,b.id,a.item_id,b.lot, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.allocated_qnty, b.available_qnty,b.avg_rate_per_unit,b.dyed_type,c.booking_without_order,a.job_no,a.po_break_down_id,a.booking_no,a.insert_date,a.allocation_date,a.is_sales,d.booking_date order by a.item_id";

		}
        // echo $sql_allocation; die();
        // and a.insert_date>='" . $to_date . "'
		
		$r_id1=execute_query("delete from tmp_poid where userid=$user_id",0);
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id",0);
		$r_id4=execute_query("delete from tmp_prod_id where userid=$user_id",0);
		$r_id5=execute_query("delete from tmp_issue_id where user_id=$user_id ",0);
		
		oci_commit($con);
		$po_ids_sale = "";

		$sale_po_id_arr = sql_select("select a.id, b.po_break_down_id from fabric_sales_order_mst a, wo_booking_mst b where a.booking_id = b.id and a.company_id = $cbo_company_name");
		$sale_id_wise_po_id = array();
		foreach ($sale_po_id_arr as $row) {
			$sale_id_wise_po_id[$row["ID"]]['PO_BREAKDOWN_ID'] = explode(",",$row['PO_BREAK_DOWN_ID'])[0];
		}
		$result_allocation = sql_select($sql_allocation);    
		foreach ($result_allocation as $row) 
		{
			if($row[csf("booking_without_order")] == 1)
			{
				$row[csf("po_break_down_id")] = '';

				if($smn_booking_check[$row[csf("booking_no")]]=="")
			    {
				    $smn_booking_check[$row[csf("booking_no")]]=$row[csf("booking_no")];
				    $r_id6=execute_query("insert into tmp_booking_no (userid, booking_no,type) values ($user_id,'".$row[csf("booking_no")]."',4)",0);
				    if($r_id6) 
					{
						$r_id6=1;
					} 
					else 
					{
						$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id",0);
						echo "insert into tmp_booking_no (userid, booking_no,type) values ($user_id,'".$row[csf("booking_no")]."',4)";
						oci_rollback($con);die;
					}
			    }
			}
			else
			{
			    $row[csf("po_break_down_id")]= $row[csf("po_break_down_id")];
			    
			    if($row[csf("po_break_down_id")]!="")
			    {
                    if($po_id_check[$row[csf("po_break_down_id")]]=="")
			        {
				        $po_id_check[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
						if($row['IS_SALES'] == 1)
						{
							$r_id7=execute_query("insert into tmp_poid (userid, poid) values ($user_id,".$sale_id_wise_po_id[$row[csf("po_break_down_id")]]['PO_BREAKDOWN_ID'].")",0);
						}
						else
						{
							$r_id7=execute_query("insert into tmp_poid (userid, poid) values ($user_id,".$row[csf("po_break_down_id")].")",0);
						}
				        
				        if($r_id7) 
						{
							$r_id7=1;
						} 
						else 
						{
							$r_id3=execute_query("delete from tmp_poid where userid=$user_id",0);
							echo "insert into tmp_poid (userid, poid) values ($user_id,".$row[csf("po_break_down_id")].")";
							oci_rollback($con);die;
						}
			        }
			    }
                
                $all_main_booking_no_arr[$row[csf("po_break_down_id")]][$row[csf("item_id")]]= $row[csf("booking_no")];
		    }

            if($item_id_check[$row[csf("item_id")]]=="")
			{
				$item_id_check[$row[csf("item_id")]]=$row[csf("item_id")];
				$r_id7=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,".$row[csf("item_id")].")",0);
				if($r_id7) 
				{
					$r_id7=1;
				} 
				else 
				{
					$r_id3=execute_query("delete from tmp_prod_id where userid=$user_id",0);
					echo "insert into tmp_prod_id (userid, prod_id) values ($user_id,".$row[csf("item_id")].")";
					oci_rollback($con);die;
				}
			}	

			if($row['IS_SALES']==1)
			{
				$po_ids_sale .= $row['PO_BREAK_DOWN_ID'].",";
			}
		}
		$po_ids_sale = trim($po_ids_sale, ",");
		$job_ref = array();
		$grouping_sql = sql_select("select a.job_no, b.grouping, b.po_number from FABRIC_SALES_ORDER_MST a, WO_PO_BREAK_DOWN b where a.PO_JOB_NO = b.JOB_NO_MST and a.id in ($po_ids_sale)");
		foreach($grouping_sql as $row)
		{
			$job_ref[$row['JOB_NO']]['GROUPING'] = $row['GROUPING'];
			$job_ref[$row['JOB_NO']]['PO_NUMBER'] = $row['PO_NUMBER'];
		}

		$issue_date_sql_first = sql_select("select a.JOB_NO, d.REQUISITION_NO, d.TRANSACTION_DATE, e.ISSUE_DATE, d.prod_id from FABRIC_SALES_ORDER_MST a, PPL_PLANNING_ENTRY_PLAN_DTLS b, PPL_YARN_REQUISITION_ENTRY c, INV_TRANSACTION d, INV_ISSUE_MASTER e where a.id=b.po_id and b.DTLS_ID = c.knit_id and d.REQUISITION_NO = c.REQUISITION_NO and d.PROD_ID = c.PROD_ID and d.TRANSACTION_TYPE = 2 and b.COMPANY_ID = $cbo_company_name and d.mst_id = e.id and a.id in ($po_ids_sale) order by e.ISSUE_DATE desc");
		$issue_date_arr = array();
		foreach($issue_date_sql_first as $row)
		{
			$issue_date_arr[$row['JOB_NO']][$row['PROD_ID']]['FIRST_ISSUE_DATE'] = $row['ISSUE_DATE'];
		}
		$issue_date_sql_last = sql_select("select a.JOB_NO, d.REQUISITION_NO, d.TRANSACTION_DATE, e.ISSUE_DATE, d.prod_id from FABRIC_SALES_ORDER_MST a, PPL_PLANNING_ENTRY_PLAN_DTLS b, PPL_YARN_REQUISITION_ENTRY c, INV_TRANSACTION d, INV_ISSUE_MASTER e where a.id=b.po_id and b.DTLS_ID = c.knit_id and d.REQUISITION_NO = c.REQUISITION_NO and d.PROD_ID = c.PROD_ID and d.TRANSACTION_TYPE = 2 and b.COMPANY_ID = $cbo_company_name and d.mst_id = e.id and a.id in ($po_ids_sale) order by e.ISSUE_DATE asc");
		foreach($issue_date_sql_last as $row)
		{
			$issue_date_arr[$row['JOB_NO']][$row['PROD_ID']]['LAST_ISSUE_DATE'] = $row['ISSUE_DATE'];
		}
		// echo "<pre>";
		// print_r($issue_date_arr); 
		// echo $issue_date_sql;

		oci_commit($con);
        
		$po_sql = sql_select("select a.id,a.job_no_mst,a.shipment_date,a.shiping_status,b.buyer_name, a.file_no,a.grouping,a.po_number from tmp_poid c,wo_po_break_down a,wo_po_details_master b where c.poid=a.id and c.userid=$user_id and a.job_id=b.id and a.status_active=1 and a.is_deleted=0");
		//echo $po_sql;die();
		$po_number_arr = array();
		foreach ($po_sql as $row) 
		{
			$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
			$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
			$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
			$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
			$po_number_arr[$row[csf("id")]]['shipment_date'] = $row[csf("shipment_date")];
            $po_number_arr[$row[csf("id")]]['shiping_status'] = $row[csf("shiping_status")];
			$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
		}
		unset($po_sql);	
		
		$sql_issue_al = "select a.id,a.issue_basis,a.issue_purpose,a.booking_id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, c.quantity as issue_qty,b.job_no,c.is_sales from tmp_poid f,tmp_prod_id e, inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d  where e.prod_id=d.id and e.userid=$user_id and f.poid = c.po_breakdown_id and f.userid=$user_id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and c.prod_id=d.id and a.issue_basis in (1,3,8) and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,7,12,15,38,46) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $company_cond";
		$result_issue_al = sql_select($sql_issue_al);
        //echo $sql_issue_al; die();
		$issue_array_req = $issue_array = $issue_basis_arr = $issue_id_check= array();
		foreach ($result_issue_al as $row) 
		{
			if($issue_id_check[$row[csf("id")]]=="")
			{
				$issue_id_check[$row[csf("id")]]=$row[csf("id")];
				$r_id8=execute_query("insert into tmp_issue_id (user_id, issue_id,type) values ($user_id,".$row[csf("id")].",1)",0); // type 1 main fabric booking issue
				if($r_id8) 
				{
					$r_id8=1;
				} 
				else 
				{
					$r_id3=execute_query("delete from tmp_issue_id where user_id=$user_id",0);
					echo "insert into tmp_issue_id (user_id, issue_id,type) values ($user_id,".$row[csf("id")].",1)";
					oci_rollback($con);die;
				}
			}

			$booking_id =  $row[csf("booking_id")];
		    $po_id = $row[csf("po_breakdown_id")];
		    $is_sales = $is_sales_arr[$row[csf("job_no")]];
		    $issue_basis = $row[csf("issue_basis")];
		    $issue_purpose = $row[csf("issue_purpose")];   

			$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
			$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];
                        
		    if($is_sales==1)
			{
				$job_no = $sales_job_arr[$row[csf("po_breakdown_id")]];
				$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}
			else
			{
				$job_no = $po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
				$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}

			if($row[csf('dyed_type')] == 1)
			{
				if(  ( $issue_basis == 3  || $issue_basis == 8 ) &&  $issue_purpose==1 ) 
				{				    
				    $booking_no = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
					if( $job_no != "" && ($row[csf("po_breakdown_id")]=="" && $booking_no=="" ) ) 
				    {
					    $issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];					
				    }
				    else
				    {
					    $issue_array[$job_no][$booking_no][$po_id][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				    }
					
				}
				else if( ($issue_basis == 1) && ( $issue_purpose==2 || $issue_purpose== 7 || $issue_purpose== 12 || $issue_purpose== 15 || $issue_purpose== 38 || $issue_purpose== 46)) 
				{
                    $booking_no = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];// $fab_booking_no_arr[$job_no][$row[csf("prod_id")]];
					if( $job_no != "" && ($row[csf("po_breakdown_id")]=="" && $booking_no=="" ) ) 
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

				if($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")]==1 || $row[csf("issue_basis")] == 8)
				{
					if($row[csf("issue_basis")]==1)
					{

						if( ($issue_basis == 1) && ( $issue_purpose==2 || $issue_purpose== 7 || $issue_purpose== 12 || $issue_purpose== 15 || $issue_purpose== 38 || $issue_purpose== 46)) 
						{
							$booking_no = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];//$fab_booking_no_arr[$job_no][$row[csf("prod_id")]];

							if( $booking_no!="" && $po_id!="")
							{
								$issue_array[$job_no][$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
							}else{
								$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
							}
							
						}		
					}
					else
					{
						$booking = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf('prod_id')]];//$planning_array[$row[csf("po_breakdown_id")]][$row[csf('requisition_no')]][$row[csf('prod_id')]];//$albooking_arr[$row[csf('prod_id')]];
						//$booking_arr = array_unique($booking_arr);
						//foreach ($booking_arr as $booking) 
						//{
							if($booking != "")
							{
								$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
								$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
							}
						//}
					}
				}
				else
				{
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
				}
			}
		}
		unset($result_issue_al);
        
       // echo "<pre>";
		//print_r($issue_array_req);
		//die();
        

        $smn_issue_sql = "SELECT x.issue_id,x.booking_no,x.prod_id, x.cons_quantity AS issue_qty
        FROM (SELECT DISTINCT a.id as issue_id,d.booking_no,b.prod_id,b.cons_quantity,b.id FROM tmp_booking_no e, tmp_prod_id f, inv_issue_master a,inv_transaction b, ppl_yarn_requisition_entry c,ppl_planning_entry_plan_dtls d WHERE  f.prod_id=b.prod_id and f.userid=$user_id  AND e.booking_no=d.booking_no AND e.userid=$user_id  AND c.knit_id = d.dtls_id AND a.id = b.mst_id AND b.requisition_no = c.requisition_no AND b.prod_id=c.prod_id AND b.transaction_type = 2 AND b.item_category = 1 AND a.issue_purpose = 1 AND d.booking_no like '%SMN%' AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 $company_cond ) x 
        UNION ALL
        SELECT y.issue_id,y.booking_no,y.prod_id, y.cons_quantity AS issue_qty
        FROM (SELECT DISTINCT a.id as issue_id,(d.booking_no || d.fab_booking_no) as booking_no ,b.prod_id,b.cons_quantity,b.id FROM tmp_booking_no e, tmp_prod_id f, inv_issue_master  a,inv_transaction  b, wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls d WHERE f.prod_id=b.prod_id AND f.userid=$user_id AND (e.booking_no=d.booking_no or e.booking_no=d.fab_booking_no) AND e.userid=$user_id AND a.booking_no = c.ydw_no AND c.id = d.mst_id   AND a.id = b.mst_id AND b.transaction_type = 2 AND b.item_category = 1 AND a.issue_purpose = 2 AND (d.booking_no like '%SMN%' or d.fab_booking_no like '%SMN%') AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 $company_cond ) y ";
  
        $smn_issue_result=sql_select($smn_issue_sql); 
        $issueIdArr = array();
        foreach($smn_issue_result as $row)
        {
    	    if($issue_id_check[$row[csf("issue_id")]]=="")
			{
				$issue_id_check[$row[csf("issue_id")]]=$row[csf("issue_id")];
				$r_id9=execute_query("insert into tmp_issue_id (user_id, issue_id,type) values ($user_id,".$row[csf("issue_id")].",4)",0); // type 4 sample booking issue
				if($r_id9) $r_id9=1; else {echo "insert into tmp_issue_id (user_id, issue_id,type) values ($user_id,".$row[csf("issue_id")].",4)";oci_rollback($con);die;}
			}

    	    $smn_issue_array[$row[csf("booking_no")]][$row[csf("prod_id")]]["issue_qty"] +=$row[csf("issue_qty")]; //sample requsition and wo issue

    	    $smn_booking_array[$row[csf("issue_id")]][$row[csf("prod_id")]]=$row[csf("booking_no")];
        }

        //echo $smn_issue_sql; die();
        //echo "Execution Time: " . (microtime(true) - $started) . "S"; die; //Execution Time: 58.157845973969S
        unset($smn_issue_result);   
 
		$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();

		$sql_return = "Select a.booking_id,b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type, c.quantity as issue_return_qty from tmp_poid h,tmp_prod_id g, tmp_issue_id e, inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where e.issue_id=b.issue_id and e.user_id=$user_id and g.prod_id=b.prod_id and g.userid=$user_id and h.poid=c.po_breakdown_id and h.userid=$user_id and e.type=1 and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and b.transaction_type=4 and c.trans_type=4 and c.prod_id=d.id and a.entry_form=9 and b.id=c.trans_id and b.item_category=1  and c.issue_purpose in(1,2,7,12,15,38,46) and a.receive_basis not in (2)and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $company_cond";

		// echo $sql_return; die();
        $result_return = sql_select($sql_return);
		$booking_no = "";
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

				if($row[csf("po_breakdown_id")] != "")
				{
					$job_wose_issue_return_array[$job_no][$po_id][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
				}
				else
				{
					$job_wose_issue_return_array[$job_no][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
				}			
			}
			else
			{
				if($issue_basis == 3 || $issue_basis == 8)
				{
					$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
					$issue_return_po_array[$issue_id][$row[csf('prod_id')]] .= $row[csf('po_breakdown_id')].",";
                    
                    $booking_no = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf('prod_id')]];
					
					$issue_return_req_array[$booking_no][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] += $row[csf("issue_return_qty")];
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

		//echo "Execution Time: " . (microtime(true) - $started) . "S"; die;//Execution Time: 57.65793299675S

		unset($result_return);     
        //echo "<pre>";
		//print_r($issue_return_req_array); die();

		//for smaple without order issue return qty
		$sqlRtnQty = "Select a.receive_basis as RECEIVE_BASIS,a.booking_id as BOOKING_ID, b.issue_id as ISSUE_ID, b.prod_id as PROD_ID, b.order_qnty as RETURN_QTY from tmp_issue_id c, inv_receive_master a, inv_transaction b where c.issue_id=b.issue_id and c.user_id=$user_id and c.type=4 and a.id=b.mst_id and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.receive_basis not in (2) and a.status_active=1 and b.status_active=1"; 
		//echo $sqlRtnQty; die();
		$sqlRtnQtyRslt = sql_select($sqlRtnQty);
		
		$sampleReturnQty = array();
		foreach($sqlRtnQtyRslt as $row)
		{
            $smn_booking_no = $smn_booking_array[$row['ISSUE_ID']][$row['PROD_ID']];
			$sampleReturnQty[$smn_booking_no][$row['PROD_ID']] += $row['RETURN_QTY'];
		}
		//echo $sqlRtnQty; die();
		//echo "Execution Time: " . (microtime(true) - $started) . "S"; die; //Execution Time: 60.433177947998S
		unset($sqlRtnQtyRslt);

		//echo "<pre>";
		//print_r($sampleReturnQty);
		//die();	

		//$cbo_company_name
		// $sql_lc_sc="select b.wo_po_break_down_id as PO_ID, a.contract_no as LC_SC_NO, a.contract_value AS LC_SC_VALUE, a.internal_file_no as INTERNAL_FILE_NO 
		// from com_sales_contract a, com_sales_contract_order_info b 
		// where a.id=b.com_sales_contract_id and a.beneficiary_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		// union all
		// select b.wo_po_break_down_id as PO_ID, a.export_lc_no as LC_SC_NO, a.lc_value AS LC_SC_VALUE, a.internal_file_no as INTERNAL_FILE_NO  
		// from com_export_lc a, com_export_lc_order_info b 
		// where a.id=b.com_export_lc_id and a.beneficiary_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		
		// $sql_lc_sc_result=sql_select($sql_lc_sc);
		// $lc_sc_data=array();
		// foreach($sql_lc_sc_result as $val)
		// {
		// 	$lc_sc_data[$val["PO_ID"]]["LC_SC_NO"]=$val["LC_SC_NO"];
		// 	$lc_sc_data[$val["PO_ID"]]["LC_SC_VALUE"]=$val["LC_SC_VALUE"];
		// 	$lc_sc_data[$val["PO_ID"]]["INTERNAL_FILE_NO"]=$val["INTERNAL_FILE_NO"];
		// }
		// unset($sql_lc_sc_result);
		//echo "<pre>";print_r($lc_sc_data);die;

		$report_type = str_replace("'","",$report_type);

		$r_id1=execute_query("delete from tmp_poid where userid=$user_id",0);
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id",0);
		$r_id4=execute_query("delete from tmp_prod_id where userid=$user_id",0);
		$r_id5=execute_query("delete from tmp_issue_id where user_id=$user_id",0);
		oci_commit($con);

		$buyer_sale_arr=return_library_array("SELECT JOB_NO,PO_BUYER from FABRIC_SALES_ORDER_MST where status_active = 1 and company_id = $cbo_company_name", "JOB_NO", "PO_BUYER");
		
		//$table_width = 2500;
		// echo "<pre>";
		// print_r($sale_id_wise_po_id);

		//echo "Execution Time: " . (microtime(true) - $started) . "S"; die;
        ob_start();
		?>
		<style type="text/css">
				table tr th, table tr td{word-wrap: break-word;word-break: break-all;}
		</style>

		<div align="center">
			<table width="2500" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px"  rules="all" id="table_header_1">
				<thead> 

					<tr class="form_caption" style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" >Booking Wise Yarn Allocation Report</td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>

					<tr>
						<th width="25">SL</th>
						<th width="100">Company</th>
						<th width="100">Product ID</th>
						<th width="100">Lot</th>
						<th width="100">Count</th>
						<th width="100">Composition</th>
						<th width="100">Type</th>
						<th width="100">Color</th>
						<th width="100">Supplier</th>
						<th width="130">Booking No.</th>
						<th width="100">Booking Date</th>
						<th width="130">Job NO.</th>
						<th width="130">Order No.</th>
                        <th width="130">Shipment Status</th>
                        <th width="100">Shipment Date</th>
						<th width="100">Buyer</th>
						<th width="100">IR/IB</th>
						<th width="100">Allocaiton Date</th>
                        <th width="100">Last Allocaiton Date</th>
                        <th width="100">First Issue Date</th>
                        <th width="100">Last Issue Date</th>
						<th width="70">Allocated Qty</th>
						<th width="70">Issue Qty</th>
						<th width="70">Issue Rtn Qty</th>
						<th width="70">Balance</th>
						<th width="100">Age Up To All/Date</th>
						<?
                        if($report_type==2)
                        {
                        ?>
                        <th width="100">Allocation Ageing</th>
                        <? 
                        }
                        ?>
                        <th width="100">SC/LC No</th>
                        <th width="100">SC/LC Value</th>
                        <!-- <th width="100">Internal File No</th> -->
					</tr>
				</thead>
			</table>
			
            <div style="width:2500px; overflow-y:scroll; max-height:250px" id="scroll_body">  
                <table width="2500" border="1" cellpadding="2" style="font:'Arial Narrow';"  cellspacing="0" class="rpt_table" rules="all" id="table_body">
					<?
					$i = 1;
					$balance = 0;
					$grand_total_allocate_qty = 0;
					$grand_total_issue_qty = 0;
					$grand_total_issue_rtn_qty = 0;
					$grand_total_balance = 0;
					$prodStock = array();
					
					// echo "<pre>";
					// print_r($po_number_arr);die;
                    
					if(!empty($result_allocation))
					{
						foreach ($result_allocation as $row) 
						{
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$prod_id = $row[csf("item_id")];

                            $ageOfDays = datediff("d", $row[csf("insert_date")], date("Y-m-d"));

							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
							if ($row[csf("yarn_comp_type2nd")] != 0)
								$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
						
							if($row[csf("po_break_down_id")]=="" || $row[csf("booking_no")]=="")
							{
								$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
							}else
							{
								$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
								//echo $issue_basis."tst".$row[csf("po_break_down_id")]; die();
							}

							if($row[csf("booking_without_order")]==1) // sample issue grey and yied
			                {
                                $issue_qty += $smn_issue_array[$row[csf("booking_no")]][$prod_id]["issue_qty"];//sample 
			                }
			                else if($row[csf("is_dyied_yarn")] == 1)
			                {
			                	$po_id = $row[csf("po_break_down_id")];

				                if ( $row[csf("job_no")]!='' && $row[csf("po_break_down_id")] ="" && $row[csf("booking_no")] ="") // old data 
				                {
                                    $issue_qty += $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
				                }
				                else
				                {
					                $issue_qty += $issue_array[$row[csf("job_no")]][$row[csf("booking_no")]][$po_id][$prod_id]["issue_qty"];
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
											}else{
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
												}
											}
										}
									}
									else
									{
										$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
									}
								}
							}

							$within_group=$sales_order_arr[$row[csf('job_no')]]['within_group'];
							$sales_booking_no=$sales_order_arr[$row[csf('job_no')]]['sales_booking_no'];

							if($sales_booking_no!="")
							{ 
								$return_qty = 0;
								if($within_group==1)
								{
									//$buyer_id=return_field_value("buyer_id as buyer_id","wo_booking_mst","booking_no ='".$sales_booking_no."' and is_deleted=0 and status_active=1","buyer_id");
									$buyername=$buy_name_arr[$buyer_id];
									$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
                                    $shiping_status = $po_number_arr[$row[csf("po_break_down_id")]]['shiping_status'];
								}
								else
								{
									$buyer_id=$sales_order_arr[$row[csf('job_no')]]['buyer_id'];
									$buyername=$buy_name_arr[$buyer_id];
									$po_number="";
                                    $shiping_status = "";
								}
								$shipment_date = "";
							}
							else
							{
								$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
								$shiping_status = $po_number_arr[$row[csf("po_break_down_id")]]['shiping_status'];
                                $shipment_date = $po_number_arr[$row[csf("po_break_down_id")]]['shipment_date'];
                                $buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];

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
									    	$booking_no = $row[csf("booking_no")];
											$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_no]["issue_id"]);
											foreach ($issue_ids as $issue_id)
											{
												$return_qty += $issue_return_req_array[$booking_no][$row[csf('po_break_down_id')]][$prod_id][$issue_id];
											}										    										
									    }
									    else
									    {
										    if($basis==1) // booking basis-- work order
										    {
											    $booking_no = 0;
											    $return_qty += $issue_return_array[$booking_no][$row[csf("po_break_down_id")]][$prod_id];
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

							if($row['IS_SALES'] == 1)
							{
								$buyername = $buy_name_arr[$buyer_sale_arr[$row['JOB_NO']]];
								$po_number=$po_number_arr[$sale_id_wise_po_id[$row["PO_BREAK_DOWN_ID"]]["PO_BREAKDOWN_ID"]]['po'];
								$shiping_status = $po_number_arr[$sale_id_wise_po_id[$row["PO_BREAK_DOWN_ID"]]["PO_BREAKDOWN_ID"]]['shiping_status'];
                                $shipment_date = $po_number_arr[$sale_id_wise_po_id[$row["PO_BREAK_DOWN_ID"]]["PO_BREAKDOWN_ID"]]['shipment_date'];
							}

							$rcv_rtn_qty=0;
							$allocate_qty = $row[csf("allocate_qty")];

							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="25" align="center"><? echo $i; ?></td>
								<td width="100" align="center"><p><? echo $companyArr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
								<td width="100"><? echo $row[csf("item_id")]; ?></td>
								<td width="100"><? echo $row[csf("lot")]; ?></td>
								<td width="100" align="center"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></p></td>
								<td width="100"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
								<td width="100"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
								<td width="100"><p><?echo $supplierArr[$row[csf("supplier_id")]];?></p></td>
								<td width="130"><p><? echo $row[csf("booking_no")]; ?></p></td>
								<td width="100" align="center"><? echo change_date_format($row[csf("booking_date")]);?></td>
								<td width="130">
									<div style="word-wrap:break-word; width:90px;text-align: center;">
										<? echo $row[csf("job_no")];?>
									</div> 
								</td>
								<td width="130" title='<? echo $row[csf("po_break_down_id")]; ?>' align="center"><p><?
										if($row['IS_SALES']==1)
										{
											echo $job_ref[$row['JOB_NO']]['PO_NUMBER'];
										}
										else
										{
											echo $po_number;
										}
								?></p></td>
                                <td width="130" title='<? echo $row[csf("po_break_down_id")]; ?>' align="center"><p><? echo $shipment_status[$shiping_status]; ?></p></td>
                                <td width="100" align="center"><? echo change_date_format($shipment_date); ?></td>
								<td width="100" align="center"><p><? echo $buyername; ?></p></td>
								<td width="100" align="center"><p><?
									if($row['IS_SALES']==1)
									{
										echo $job_ref[$row["JOB_NO"]]['GROUPING'];
									}
								?></p></td>
								<td width="100" align="center"><? echo change_date_format($row[csf("allocation_date")]); ?></td>
                                <td width="100" align="center">&nbsp;<? echo change_date_format($row[csf("insert_date")]);?></td>
                                <td width="100" align="center">&nbsp;<? echo change_date_format($issue_date_arr[$row["JOB_NO"]][$row[csf("item_id")]]['FIRST_ISSUE_DATE']);?></td>
                                <td width="100" align="center">&nbsp;<? echo change_date_format($issue_date_arr[$row["JOB_NO"]][$row[csf("item_id")]]['LAST_ISSUE_DATE']);?></td>
								<td width="70" align="right">
									<?
									echo number_format($allocate_qty, 2);
									?>
								</td>
								<td width="70" align="right" title="<? echo $issue_qty .'=='. $issue_qty_wo; ?>"><? echo number_format(($issue_qty+$issue_qty_wo), 2); ?></td>
								<td width="70" align="right"><? echo number_format($return_qty, 2); ?></td>
								<td width="70" align="right">
									<?
									$balance = ($row[csf("allocate_qty")] + number_format($return_qty,2,".","")) - (number_format(($issue_qty+$issue_qty_wo),2,".","") + number_format($rcv_rtn_qty,2,".",""));
									echo number_format($balance, 2);
									?>
								</td>
								<td width="100" align="right"><? echo $ageOfDays; ?></td>
								<?
								if($report_type==2)
                                {?>
                                <td width="100" align="right">&nbsp;<? echo $aginOfDays = ($balance<0.01)?0:$ageOfDays;?></td>
                                <?
                                } 
                                ?>
                                <td width="100" align="center"><? echo $lc_sc_data[$row[csf("po_break_down_id")]]["LC_SC_NO"]; ?></td>
                                <td width="100" align="right"><? echo number_format($lc_sc_data[$row[csf("po_break_down_id")]]["LC_SC_VALUE"],2); ?></td>
                                <!-- <td width="100" align="center"><?// echo $lc_sc_data[$row[csf("po_break_down_id")]]["INTERNAL_FILE_NO"]; ?></td> -->
							</tr>
							<?
							$i++;
							
							$sub_total_allocated_qty += $allocate_qty;
							$sub_total_issue_qty += ($issue_qty+$issue_qty_wo);
							$sub_total_issue_rtn_qty += $return_qty;
							$sub_total_balance_qty += ($allocate_qty+$return_qty)-($issue_qty+$issue_qty_wo);

							$grand_total_allocate_qty += $allocate_qty;
							$grand_total_issue_qty += ($issue_qty+$issue_qty_wo);
							$grand_total_issue_rtn_qty += $return_qty;
							$grand_total_balance += ($allocate_qty+$return_qty)-($issue_qty+$issue_qty_wo);

							$issue_qty=$return_qty=$issue_qty_wo=0;												
						}
					}
					else
					{
						echo "<tr colspan='17'><th style='text-align:center;'>No Data Found</th></tr>";
					}
					?>
				</table>
            </div>

			<table width="2500" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">			
                <tr class="tbl_bottom">
					<td width="25">&nbsp;</td> 
					<td width="100">&nbsp;</td>  
					<td width="100">&nbsp;</td>  
					<td width="100">&nbsp;</td>  
					<td width="100">&nbsp;</td>  
					<td width="100">&nbsp;</td>  
					<td width="100">&nbsp;</td>  
					<td width="100">&nbsp;</td>
					<td width="100">&nbsp;</td>  
					<td width="130">&nbsp;</td>  
					<td width="100">&nbsp;</td>  
					<td width="130">&nbsp;</td>  
					<td width="130">&nbsp;</td>
                    <td width="130">&nbsp;</td>  
					<td width="100">&nbsp;</td>  
					<td width="100">&nbsp;</td>  
					<td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>   
                    <td width="100">&nbsp;</td>   
                    <td width="100">&nbsp;</td>   
				    <td width="100"align="right">Grand Total</td>
				    <td width="70" style="word-break: break-all; text-align:right;" id="value_total_allocation_qty_x" ><?= number_format($grand_total_allocate_qty,2);?></td>                                        
				    <td width="70" style="word-break: break-all;text-align:right;" id="value_total_issue_qty_x" ><?= number_format($grand_total_issue_qty,2)?></td>
				    <td width="70" style="word-break: break-all;text-align:right;" id="value_total_issue_return_qty_x"><?= number_format($grand_total_issue_rtn_qty,2)?></td>
				    <td width="70" align="right" style="word-break: break-all;" id="value_total_balance_x"><?= number_format($grand_total_balance,2)?></td>
				    <td width="100">&nbsp;</td>
				    <?
                    if($report_type==2)
                    {
                    ?>
                    	<td width="100">&nbsp;</td> 
                    <? 
                    }
                    ?> 				    
                    <td width="100">&nbsp;</td> 
                    <td width="100">&nbsp;</td> 
                    <!-- <td width="100">&nbsp;</td>   -->
                </tr>
			</table>			
		</div>
	<?
	//===========
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}

if ($action == "generate_report_only_excel")
{
	$started = microtime(true);
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
    $con = connect();

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$companyArr[0] = "All Company";
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$yarnTestQalityCommentsArr = return_library_array("select prod_id, yarn_quality_coments from inv_yarn_test_mst where  status_active=1 and is_deleted=0", 'prod_id', 'yarn_quality_coments');
	$yarnTestArr = return_library_array("select prod_id, lot_number from inv_yarn_test_mst where status_active=1 and is_deleted=0", 'prod_id', 'lot_number');

	if ($db_type == 0)
	{
		$from_date = change_date_format($from_date, 'yyyy-mm-dd');
		$to_date = change_date_format($to_date, 'yyyy-mm-dd');
	}
	else if ($db_type == 2)
	{
		
		$from_date = change_date_format($from_date, '', '', 1);
		$to_date = change_date_format($to_date, '', '', 1);
	}
	else
	{
		$from_date = "";
		$to_date = "";
	}

	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);
	
	//===========

	    $buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

		$search_cond = "";
		if ($cbo_yarn_type == 0)
			$search_cond .= "";
		else
			$search_cond .= " and b.yarn_type in ($cbo_yarn_type)";
		if ($txt_count == "")
			$search_cond .= "";
		else
			$search_cond .= " and b.yarn_count_id in($txt_count)";
		if ($txt_lot_no == "")
			$search_cond .= "";
		else
			$search_cond .= " and trim(b.lot)='" . trim($txt_lot_no) . "'";

		if ($cbo_supplier == 0)
			$search_cond .= "";
		else
			$search_cond .= "  and b.supplier_id in($cbo_supplier)";
		if ($txt_composition == "")
			$search_cond .= "";
		else
			$search_cond .= " and b.yarn_comp_type1st in (" .$txt_composition_id .")";
		if ($cbo_company_name == 0)
			$search_cond .= "";
		else
			$search_cond .= " and b.company_id=$cbo_company_name";
		if ($cbo_dyed_type == 0)
			$search_cond .= "";
		else
			$search_cond .= " and b.dyed_type=$cbo_dyed_type";

		if ($cbo_company_name == 0) {
			$company_cond = "";
		} else {
			$company_cond = " and b.company_id=$cbo_company_name";
		}

		if($db_type == 0) 
		{
			$sql_allocation = "select a.item_id,a.job_no, a.po_break_down_id,group_concat(booking_no) booking_no, sum(qnty) as allocate_qty, sum(a.qnty) as allocate_qty,a.insert_date,a.allocation_date,a.is_sales,b.company_id, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.lot, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit,b.dyed_type is_dyied_yarn from inv_material_allocation_dtls a,product_details_master b where a.item_id=b.id and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and b.status_active=1 and b.is_deleted=0 $search_cond group by b.company_id,a.item_id,b.lot, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.allocated_qnty, b.available_qnty,b.avg_rate_per_unit,b.dyed_type,a.job_no,a.po_break_down_id,a.insert_date,a.allocation_date,a.is_sales";
		}
		else
		{	
		
			$sql_allocation = "select b.company_id, b.id, a.item_id,a.job_no, a.po_break_down_id,a.booking_no, null as booking_date, sum(a.qnty) as allocate_qty,a.insert_date,a.allocation_date,a.is_sales, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.lot, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit,b.dyed_type is_dyied_yarn,c.booking_without_order from inv_material_allocation_dtls a,inv_material_allocation_mst c,product_details_master b where a.mst_id=c.id and c.item_id=a.item_id and a.item_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.qnty>0 and b.status_active=1 and b.is_deleted=0 $search_cond group by b.company_id,b.id,a.item_id,b.lot, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.allocated_qnty, b.available_qnty,b.avg_rate_per_unit,b.dyed_type,c.booking_without_order,a.job_no,a.po_break_down_id,a.booking_no,a.insert_date,a.allocation_date,a.is_sales";

		}
        //echo $sql_allocation; die(); //and a.insert_date>='" . $to_date . "'
		
		$r_id1=execute_query("delete from tmp_poid where userid=$user_id",0);
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id",0);
		$r_id4=execute_query("delete from tmp_prod_id where userid=$user_id",0);
		$r_id5=execute_query("delete from tmp_issue_id where user_id=$user_id ",0);
		oci_commit($con);

		$result_allocation = sql_select($sql_allocation);      
		foreach ($result_allocation as $row) 
		{
			if($row[csf("booking_without_order")] == 1)
			{
				$row[csf("po_break_down_id")] = '';

				if($smn_booking_check[$row[csf("booking_no")]]=="")
			    {
				    $smn_booking_check[$row[csf("booking_no")]]=$row[csf("booking_no")];
				    $r_id6=execute_query("insert into tmp_booking_no (userid, booking_no,type) values ($user_id,'".$row[csf("booking_no")]."',4)",0);
				    if($r_id6) 
					{
						$r_id6=1;
					} 
					else 
					{
						$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id",0);
						echo "insert into tmp_booking_no (userid, booking_no,type) values ($user_id,'".$row[csf("booking_no")]."',4)";
						oci_rollback($con);die;
					}
			    }
			}
			else
			{
			    $row[csf("po_break_down_id")]= $row[csf("po_break_down_id")];
			    
			    if($row[csf("po_break_down_id")]!="")
			    {
                    if($po_id_check[$row[csf("po_break_down_id")]]=="")
			        {
				        $po_id_check[$row[csf("po_break_down_id")]]=$row[csf("po_break_down_id")];
				        $r_id7=execute_query("insert into tmp_poid (userid, poid) values ($user_id,".$row[csf("po_break_down_id")].")",0);
				        if($r_id7) 
						{$r_id7=1;} 
						else 
						{
							$r_id1=execute_query("delete from tmp_poid where userid=$user_id",0);
							echo "insert into tmp_poid (userid, poid) values ($user_id,".$row[csf("po_break_down_id")].")";
							oci_rollback($con);die;
						}
			        }
			    }
                
                $all_main_booking_no_arr[$row[csf("po_break_down_id")]][$row[csf("item_id")]]= $row[csf("booking_no")];
                //$fab_booking_no_arr[$row[csf("job_no")]][$row[csf("item_id")]]= $row[csf("booking_no")];		    
		    }

            if($item_id_check[$row[csf("item_id")]]=="")
			{
				$item_id_check[$row[csf("item_id")]]=$row[csf("item_id")];
				$r_id7=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_id,".$row[csf("item_id")].")",0);
				if($r_id7) {$r_id7=1;} 
				else 
				{
					$r_id4=execute_query("delete from tmp_prod_id where userid=$user_id",0);
					echo "insert into tmp_prod_id (userid, prod_id) values ($user_id,".$row[csf("item_id")].")";
					oci_rollback($con);die;
				}
			}	
		} 	

		oci_commit($con);
        
		$po_sql = sql_select("select a.id,a.job_no_mst,a.shipment_date,a.shiping_status,b.buyer_name, a.file_no,a.grouping,a.po_number from tmp_poid c,wo_po_break_down a,wo_po_details_master b where c.poid=a.id and c.userid=$user_id and a.job_id=b.id and a.status_active=1 and a.is_deleted=0");
		//echo $po_sql;die();
		$po_number_arr = array();
		foreach ($po_sql as $row) 
		{
			$po_number_arr[$row[csf("id")]]['po'] = $row[csf("po_number")];
			$po_number_arr[$row[csf("id")]]['file'] = $row[csf("file_no")];
			$po_number_arr[$row[csf("id")]]['ref'] = $row[csf("grouping")];
			$po_number_arr[$row[csf("id")]]['job_no'] = $row[csf("job_no_mst")];
			$po_number_arr[$row[csf("id")]]['shipment_date'] = $row[csf("shipment_date")];
            $po_number_arr[$row[csf("id")]]['shiping_status'] = $row[csf("shiping_status")];
			$po_number_arr[$row[csf("job_no_mst")]]['buyer_name'] = $row[csf("buyer_name")];
		}
		unset($po_sql);
				
		$sql_issue_al = "select a.id,a.issue_basis,a.issue_purpose,a.booking_id,b.requisition_no,c.po_breakdown_id,c.prod_id,d.dyed_type, c.quantity as issue_qty,b.job_no,c.is_sales from tmp_poid f,tmp_prod_id e, inv_issue_master a, inv_transaction b, order_wise_pro_details c,product_details_master d  where e.prod_id=d.id and e.userid=$user_id and f.poid = c.po_breakdown_id and f.userid=$user_id and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and c.prod_id=d.id and a.issue_basis in (1,3) and (a.issue_purpose in(1,2) or a.issue_purpose in(1,2,7,12,15,38,46) and b.job_no is not null) and c.trans_type=2 and a.entry_form=3 and b.item_category=1 and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $company_cond";
		$result_issue_al = sql_select($sql_issue_al);
        //echo $sql_issue_al; die();
		$issue_array_req = $issue_array = $issue_basis_arr = $issue_id_check= array();
		foreach ($result_issue_al as $row) 
		{
			if($issue_id_check[$row[csf("id")]]=="")
			{
				$issue_id_check[$row[csf("id")]]=$row[csf("id")];
				$r_id8=execute_query("insert into tmp_issue_id (user_id, issue_id,type) values ($user_id,".$row[csf("id")].",1)",0); // type 1 main fabric booking issue
				if($r_id8) $r_id8=1; else {echo "insert into tmp_issue_id (user_id, issue_id,type) values ($user_id,".$row[csf("id")].",1)";oci_rollback($con);die;}
			}

			$booking_id =  $row[csf("booking_id")];
		    $po_id = $row[csf("po_breakdown_id")];
		    $is_sales = $is_sales_arr[$row[csf("job_no")]];
		    $issue_basis = $row[csf("issue_basis")];
		    $issue_purpose = $row[csf("issue_purpose")];   

			$issue_basis_arr[$row[csf("id")]]["basis"] = $row[csf("issue_basis")];
			$issue_basis_arr[$row[csf("id")]]["issue_id"] = $row[csf("id")];
                        
		    if($is_sales==1)
			{
				$job_no = $sales_job_arr[$row[csf("po_breakdown_id")]];
				$issue_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}
			else
			{
				$job_no = $po_number_arr[$row[csf("po_breakdown_id")]]['job_no'];
				$issue_arr[$job_no][$row[csf("prod_id")]][] = $row[csf("issue_basis")];
			}

			if($row[csf('dyed_type')] == 1)
			{
				if(  ( $issue_basis == 3  || $issue_basis == 8 ) &&  $issue_purpose==1 ) 
				{				    
				    $booking_no = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
					if( $job_no != "" && ($row[csf("po_breakdown_id")]=="" && $booking_no=="" ) ) 
				    {
					    $issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];					
				    }
				    else
				    {
					    $issue_array[$job_no][$booking_no][$po_id][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
				    }
					
				}
				else if( ($issue_basis == 1) && ( $issue_purpose==2 || $issue_purpose== 7 || $issue_purpose== 12 || $issue_purpose== 15 || $issue_purpose== 38 || $issue_purpose== 46)) 
				{
                    $booking_no = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];
					if( $job_no != "" && ($row[csf("po_breakdown_id")]=="" && $booking_no=="" ) ) 
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

				if($row[csf("issue_basis")] == 3 || $row[csf("issue_basis")]==1 || $row[csf("issue_basis")] == 8)
				{
					if($row[csf("issue_basis")]==1)
					{

						if( ($issue_basis == 1) && ( $issue_purpose==2 || $issue_purpose== 7 || $issue_purpose== 12 || $issue_purpose== 15 || $issue_purpose== 38 || $issue_purpose== 46)) 
						{
							$booking_no = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]];//$fab_booking_no_arr[$job_no][$row[csf("prod_id")]];

							if( $booking_no!="" && $po_id!="")
							{
								$issue_array[$job_no][$booking_no][$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
							}else{
								$issue_array[$job_no][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
							}
							
						}		
					}
					else
					{
						$booking = $all_main_booking_no_arr[$row[csf("po_breakdown_id")]][$row[csf('prod_id')]];
						if($booking != "")
						{
							$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_qty"] += $row[csf("issue_qty")];
							$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]][$booking]["issue_id"][] = $row[csf("id")];
						}
					}
				}
				else
				{
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_qty"] += $row[csf("issue_qty")];
					$issue_array_req[$row[csf("po_breakdown_id")]][$row[csf("prod_id")]]["issue_id"][] = $row[csf("id")];
				}
			}
		}
		unset($result_issue_al);
        
        $smn_issue_sql = "SELECT x.issue_id,x.booking_no,x.prod_id, x.cons_quantity AS issue_qty
        FROM (SELECT DISTINCT a.id as issue_id,d.booking_no,b.prod_id,b.cons_quantity,b.id FROM tmp_booking_no e, tmp_prod_id f, inv_issue_master a,inv_transaction b, ppl_yarn_requisition_entry c,ppl_planning_entry_plan_dtls d WHERE  f.prod_id=b.prod_id and f.userid=$user_id  AND e.booking_no=d.booking_no AND e.userid=$user_id  AND c.knit_id = d.dtls_id AND a.id = b.mst_id AND b.requisition_no = c.requisition_no AND b.prod_id=c.prod_id AND b.transaction_type = 2 AND b.item_category = 1 AND a.issue_purpose = 1 AND d.booking_no like '%SMN%' AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 $company_cond ) x 
        UNION ALL
        SELECT y.issue_id,y.booking_no,y.prod_id, y.cons_quantity AS issue_qty
        FROM (SELECT DISTINCT a.id as issue_id,(d.booking_no || d.fab_booking_no) as booking_no ,b.prod_id,b.cons_quantity,b.id FROM tmp_booking_no e, tmp_prod_id f, inv_issue_master  a,inv_transaction  b, wo_yarn_dyeing_mst c,wo_yarn_dyeing_dtls d WHERE f.prod_id=b.prod_id AND f.userid=$user_id AND (e.booking_no=d.booking_no or e.booking_no=d.fab_booking_no) AND e.userid=$user_id AND a.booking_no = c.ydw_no AND c.id = d.mst_id   AND a.id = b.mst_id AND b.transaction_type = 2 AND b.item_category = 1 AND a.issue_purpose = 2 AND (d.booking_no like '%SMN%' or d.fab_booking_no like '%SMN%') AND a.status_active = 1 AND b.status_active = 1 AND c.status_active = 1 AND d.status_active = 1 $company_cond ) y ";
  
        $smn_issue_result=sql_select($smn_issue_sql); 
        $issueIdArr = array();
        foreach($smn_issue_result as $row)
        {
    	    if($issue_id_check[$row[csf("issue_id")]]=="")
			{
				$issue_id_check[$row[csf("issue_id")]]=$row[csf("issue_id")];
				$r_id9=execute_query("insert into tmp_issue_id (user_id, issue_id,type) values ($user_id,".$row[csf("issue_id")].",4)",0); // type 4 sample booking issue
				if($r_id9) $r_id9=1; else {echo "insert into tmp_issue_id (user_id, issue_id,type) values ($user_id,".$row[csf("issue_id")].",4)";oci_rollback($con);die;}
			}

    	    $smn_issue_array[$row[csf("booking_no")]][$row[csf("prod_id")]]["issue_qty"] +=$row[csf("issue_qty")]; //sample requsition and wo issue

    	    $smn_booking_array[$row[csf("issue_id")]][$row[csf("prod_id")]]=$row[csf("booking_no")];
        }

        //echo $smn_issue_sql; die();
        //echo "Execution Time: " . (microtime(true) - $started) . "S"; die; //Execution Time: 58.157845973969S
        unset($smn_issue_result);   

		$issue_return_array = $issue_return_req_array = $job_wose_issue_return_array = array();

		$sql_return = "Select a.booking_id,b.issue_id,c.po_breakdown_id,c.prod_id,c.issue_purpose,d.dyed_type, c.quantity as issue_return_qty from tmp_poid h,tmp_prod_id g, tmp_issue_id e, inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d where e.issue_id=b.issue_id and e.user_id=$user_id and g.prod_id=b.prod_id and g.userid=$user_id and h.poid=c.po_breakdown_id and h.userid=$user_id and e.type=1 and a.id=b.mst_id and b.id=c.trans_id and b.prod_id=c.prod_id and b.transaction_type=4 and c.trans_type=4 and c.prod_id=d.id and a.entry_form=9 and b.id=c.trans_id and b.item_category=1  and c.issue_purpose in(1,2,7,12,15,38,46) and a.receive_basis not in (2)and a.status_active=1 and b.status_active=1 and c.status_active=1 and d.status_active=1 $company_cond";

		//echo $sql_return; die();
        $result_return = sql_select($sql_return);
		$booking_no = "";
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
							
				if($po_id != "")
				{
					$job_wose_issue_return_array[$job_no][$po_id][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
				}
				else
				{
					$job_wose_issue_return_array[$job_no][$row[csf('prod_id')]] += $row[csf("issue_return_qty")];
				}							
			}
			else
			{
				if($issue_basis == 3 || $issue_basis == 8)
				{
					$issue_id = $issue_basis_arr[$row[csf("issue_id")]]["issue_id"];
					$issue_return_po_array[$issue_id][$row[csf('prod_id')]] .= $row[csf('po_breakdown_id')].",";
                    
                    $booking_no = $all_main_booking_no_arr[$row[csf('po_breakdown_id')]][$row[csf('prod_id')]];
					$issue_return_req_array[$booking_no][$row[csf('po_breakdown_id')]][$row[csf('prod_id')]][$issue_id] += $row[csf("issue_return_qty")];
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

		//echo "Execution Time: " . (microtime(true) - $started) . "S"; die;//Execution Time: 57.65793299675S

		unset($result_return);     
        //echo "<pre>";
		//print_r($issue_return_req_array); die();

		//for smaple without order issue return qty
		$sqlRtnQty = "Select a.receive_basis as RECEIVE_BASIS,a.booking_id as BOOKING_ID, b.issue_id as ISSUE_ID, b.prod_id as PROD_ID, b.order_qnty as RETURN_QTY from tmp_issue_id c, inv_receive_master a, inv_transaction b where c.issue_id=b.issue_id and c.user_id=$user_id and c.type=4 and a.id=b.mst_id and a.entry_form=9 and b.item_category=1 and b.transaction_type=4 and a.receive_basis not in (2) and a.status_active=1 and b.status_active=1"; 
		//echo $sqlRtnQty; die();
		$sqlRtnQtyRslt = sql_select($sqlRtnQty);
		
		$sampleReturnQty = array();
		foreach($sqlRtnQtyRslt as $row)
		{
            $smn_booking_no = $smn_booking_array[$row['ISSUE_ID']][$row['PROD_ID']];
			$sampleReturnQty[$smn_booking_no][$row['PROD_ID']] += $row['RETURN_QTY'];
		}
		//echo $sqlRtnQty; die();
		//echo "Execution Time: " . (microtime(true) - $started) . "S"; die; //Execution Time: 60.433177947998S
		unset($sqlRtnQtyRslt);

		//echo "<pre>";
		//print_r($sampleReturnQty);
		//die();
      		
		$report_type = str_replace("'","",$report_type);

		$r_id1=execute_query("delete from tmp_poid where userid=$user_id",0);
		$r_id3=execute_query("delete from tmp_booking_no where userid=$user_id",0);
		$r_id4=execute_query("delete from tmp_prod_id where userid=$user_id",0);
		$r_id5=execute_query("delete from tmp_issue_id where user_id=$user_id ",0);
		oci_commit($con);

		//$table_width = 2500;

		//echo "Execution Time: " . (microtime(true) - $started) . "S"; die;
        //ob_start();
        $html = "";
		
		$html .= '<table width="2500" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:Arial Narrow; font-size:14px"  rules="all" id="table_header_1">
				<thead> 
					<tr class="form_caption" style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" >Booking Wise Yarn Allocation Report</td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							Company Name :'. $companyArr[str_replace("'", "", $cbo_company_name)].
						'</td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">';
							if ($from_date != "" && $to_date != "")  $html .= 'From'  . change_date_format($from_date, 'dd-mm-yyyy') . ' To ' . change_date_format($to_date, 'dd-mm-yyyy') .
						'</td>
					</tr>
					<tr>
						<th width="25">SL</th>
						<th width="100">Company</th>
						<th width="100">Product ID</th>
						<th width="100">Lot</th>
						<th width="100">Count</th>
						<th width="100">Composition</th>
						<th width="100">Type</th>
						<th width="100">Color</th>
						<th width="100">Supplier</th>
						<th width="130">Booking No.</th>
						<th width="100">Booking Date</th>
						<th width="130">Job NO.</th>
						<th width="130">Order No.</th>
                        <th width="130">Shipment Status</th>
                        <th width="100">Shipment Date</th>
						<th width="100">Buyer</th>
						<th width="100">Allocaiton Date</th>';                        
                        if($report_type==2)
                        {
                        $html .='<th width="100">Last Allocaiton Date</th>';
                        }
						$html .='<th width="70">Allocated Qty</th>
						<th width="70">Issue Qty</th>
						<th width="70">Issue Rtn Qty</th>
						<th width="70">Balance</th>
						<th width="100">Age Up To All/Date</th>';						
                        if($report_type==2)
                        {
                        $html .='<th width="100">Allocation Ageing</th>';
                        }
                        $html .='
                        <th width="100">SC/LC No</th>
                        <th width="100">SC/LC Value</th>
                        <th width="100">Internal File No</th>
					</tr>
				</thead>
		</table>';	
        
       $html .=' <table width="2502" border="1" cellpadding="2" style="font:Arial Narrow"  cellspacing="0" class="rpt_table" rules="all" id="table_body">';
			
			$i = 1;
			$balance = 0;
			$grand_total_allocate_qty = 0;
			$grand_total_issue_qty = 0;
			$grand_total_issue_rtn_qty = 0;
			$grand_total_balance = 0;
			$prodStock = array();
            
			if(!empty($result_allocation))
			{
				foreach ($result_allocation as $row) 
				{
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$prod_id = $row[csf("item_id")];

                    $ageOfDays = datediff("d", $row[csf("insert_date")], date("Y-m-d"));

					$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
					if ($row[csf("yarn_comp_type2nd")] != 0)
						$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
				
					if($row[csf("po_break_down_id")]=="" || $row[csf("booking_no")]=="")
					{
						$issue_basis = array_unique($issue_arr[$row[csf('job_no')]][$prod_id]);
					}else
					{
						$issue_basis = array_unique($issue_arr[$row[csf("po_break_down_id")]][$prod_id]);
						//echo $issue_basis."tst".$row[csf("po_break_down_id")]; die();
					}

					if($row[csf("booking_without_order")]==1) // sample issue grey and yied
	                {
                        $issue_qty += $smn_issue_array[$row[csf("booking_no")]][$prod_id]["issue_qty"];//sample 
	                }
	                else if($row[csf("is_dyied_yarn")] == 1)
	                {
	                	$po_id = $row[csf("po_break_down_id")];

		                if ( $row[csf("job_no")]!='' && $row[csf("po_break_down_id")] ="" && $row[csf("booking_no")] ="") // old data 
		                {
                            $issue_qty += $issue_array[$row[csf("job_no")]][$prod_id]["issue_qty"];
		                }
		                else
		                {
			                $issue_qty += $issue_array[$row[csf("job_no")]][$row[csf("booking_no")]][$po_id][$prod_id]["issue_qty"];
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
									}else{
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
										}
									}
								}
							}
							else
							{
								$issue_qty += $issue_array[$row[csf('job_no')]][$prod_id]["issue_qty"];
							}
						}
					}

					$within_group=$sales_order_arr[$row[csf('job_no')]]['within_group'];
					$sales_booking_no=$sales_order_arr[$row[csf('job_no')]]['sales_booking_no'];

					if($sales_booking_no!="")
					{ 
						$return_qty = 0;
						if($within_group==1)
						{
							//$buyer_id=return_field_value("buyer_id as buyer_id","wo_booking_mst","booking_no ='".$sales_booking_no."' and is_deleted=0 and status_active=1","buyer_id");
							$buyername=$buy_name_arr[$buyer_id];
							$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
                            $shiping_status = $po_number_arr[$row[csf("po_break_down_id")]]['shiping_status'];
						}
						else
						{
							$buyer_id=$sales_order_arr[$row[csf('job_no')]]['buyer_id'];
							$buyername=$buy_name_arr[$buyer_id];
							$po_number="";
                            $shiping_status = "";
						}
						$shipment_date = "";
					}
					else
					{
						$po_number=$po_number_arr[$row[csf("po_break_down_id")]]['po'];
						$shiping_status = $po_number_arr[$row[csf("po_break_down_id")]]['shiping_status'];
                        $shipment_date = $po_number_arr[$row[csf("po_break_down_id")]]['shipment_date'];
                        $buyername=$buy_name_arr[$po_number_arr[$row[csf("job_no")]]['buyer_name']];

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
							    	$booking_no = $row[csf("booking_no")];
									$issue_ids = array_unique($issue_array_req[$row[csf("po_break_down_id")]][$prod_id][$booking_no]["issue_id"]);
									foreach ($issue_ids as $issue_id)
									{
										$return_qty += $issue_return_req_array[$booking_no][$row[csf('po_break_down_id')]][$prod_id][$issue_id];
									}										    										
							    }
							    else
							    {
								    if($basis==1) // booking basis-- work order
								    {
									    $booking_no = 0;
									    $return_qty += $issue_return_array[$booking_no][$row[csf("po_break_down_id")]][$prod_id];
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

					$allocate_qty = $row[csf("allocate_qty")];

					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					
					$html .='<tr>
						<td>'.$i. '</td>
						<td>'.$companyArr[$row[csf("company_id")]].'&nbsp;</td>
						<td>'.$row[csf("item_id")].'</td>
						<td>'.$row[csf("lot")].'</td>
						<td>'.$yarn_count_arr[$row[csf("yarn_count_id")]].'</td>
						<td>&nbsp;</td>
						<td>'.$yarn_type[$row[csf("yarn_type")]].'&nbsp;</td>
						<td>'.$color_name_arr[$row[csf("color")]].'</td>
						<td>'.$supplierArr[$row[csf("supplier_id")]].'</td>
						<td>'.$row[csf("booking_no")].'</td>
						<td>'.change_date_format($row[csf("booking_date")]).'</td>
						<td>'.$row[csf("job_no")].'</td>
						<td>'.$po_number.'</td>
                        <td>'.$shipment_status[$shiping_status].'</td>
                        <td>'.change_date_format($shipment_date).'</td>
						<td>'.$buyername.'</td>
						<td>'.change_date_format($row[csf("allocation_date")]).'</td>';                       
						if($report_type==2)
                        {
                        $html .='<td>&nbsp;'.change_date_format($row[csf("insert_date")]).'</td>';
                        } 
                        
						$html .='<td>'.number_format($allocate_qty, 2).'</td>
						<td>'.number_format(($issue_qty+$issue_qty_wo), 2).'</td>
						<td>'.number_format($return_qty, 2).'</td>
						<td>'.$balance = ($row[csf("allocate_qty")] + number_format($return_qty,2,".","")) - (number_format(($issue_qty+$issue_qty_wo),2,".","") + number_format($rcv_rtn_qty,2,".",""));
							echo number_format($balance, 2).'</td>
						<td>'.$ageOfDays.'</td>';
						
						if($report_type==2)
                        {
                        $html .='<td>&nbsp;'.$aginOfDays = ($balance<0.01)?0:$ageOfDays.'</td>';                        
                        } 
                        $html .='<td>'.$lc_sc_data[$row[csf("po_break_down_id")]]["LC_SC_NO"].'</td>
                        <td>'.number_format($lc_sc_data[$row[csf("po_break_down_id")]]["LC_SC_VALUE"],2).'</td>
                        <td>'.$lc_sc_data[$row[csf("po_break_down_id")]]["INTERNAL_FILE_NO"].'</td>
					</tr>';
					
					$i++;
					
					$sub_total_allocated_qty += $allocate_qty;
					$sub_total_issue_qty += ($issue_qty+$issue_qty_wo);
					$sub_total_issue_rtn_qty += $return_qty;
					$sub_total_balance_qty += ($allocate_qty+$return_qty)-($issue_qty+$issue_qty_wo);

					$grand_total_allocate_qty += $allocate_qty;
					$grand_total_issue_qty += ($issue_qty+$issue_qty_wo);
					$grand_total_issue_rtn_qty += $return_qty;
					$grand_total_balance += ($allocate_qty+$return_qty)-($issue_qty+$issue_qty_wo);

					$issue_qty=$return_qty=$issue_qty_wo=0;												
				}
			}
			else
			{
				$html .='<tr colspan="27"><th>No Data Found</th></tr>';
			}
		$html .='</table>';

	    $html .='<table width="2502" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer"><tr class="tbl_bottom">
					<td>&nbsp;</td> 
					<td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>
					<td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>
                    <td>&nbsp;</td>  
					<td>&nbsp;</td>  
					<td>&nbsp;</td>';  
                    if($report_type==2)
                    {
                    $html .='<td>&nbsp;</td>'; 
                    }
				    $html .='<td>Grand Total</td>
				    <td>&nbsp;</td>                                        
				    <td>&nbsp;</td>
				    <td>&nbsp;</td>
				    <td>&nbsp;</td>
				    <td>&nbsp;</td>';				    
                    if($report_type==2)
                    {
                    $html .='<td>&nbsp;</td>';
                    }				    
                    $html .='<td>&nbsp;</td>
                    <td>&nbsp;</td> 
                    <td>&nbsp;</td>  
                </tr>
		</table>';			
	
	//===========
	//$html = ob_get_contents();
	//ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename**$report_type";
	exit();
}

if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	var selected_id = new Array(); var selected_name = new Array();

	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

		tbl_row_count = tbl_row_count-1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
		}
	}

	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function set_all()
	{
		var old=document.getElementById('txt_pre_composition_row_id').value;
		if(old!="")
		{
			old=old.split(",");
			for(var k=0; k<old.length; k++)
			{
				js_set_value( old[k] )
			}
		}
	}

	function js_set_value( str )
	{

		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_individual_id' + str).val() );
			selected_name.push( $('#txt_individual' + str).val() );

		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i, 1 );
		}

		var id = ''; var name = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			name += selected_name[i] + ',';
		}

		id = id.substr( 0, id.length - 1 );
		name = name.substr( 0, name.length - 1 );

		$('#hidden_composition_id').val(id);
		$('#hidden_composition').val(name);
	}
	</script>
	</head>
	<fieldset style="width:390px">
		<legend>Yarn Receive Details</legend>
		<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
		<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="2">
						<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
					</th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="">Composition Name</th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?
		$i = 1;

		$result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
		$pre_composition_id_arr=explode(",",$pre_composition_id);
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";


			if(in_array($row[csf("id")],$pre_composition_id_arr))
			{
				if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
				<td width="50">
					<? echo $i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
				</td>
				<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
		</table>
		</div>
		<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
		set_all();
	</script>
	<?
}


?>

<style>
	a {
		color: #0254EB
	}
	a:visited {
		color: #0254EB
	}
	a.morelink {
		text-decoration:none;
		outline: none;
	}
	.morecontent span {
		display: none;
	}
	.comment {
		width: 400px;
		background-color: #f0f0f0;
		margin: 10px;
	}
</style>
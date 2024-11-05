<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer"){
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name 	=str_replace("'","",$cbo_company_name);

	$cbo_buyer_name 	=str_replace("'","",$cbo_buyer_name);	
	$txt_job_no 		=str_replace("'","",$txt_job_no);
	$txt_style_ref 		=str_replace("'","",$txt_style_ref);
	$txt_ord_no 		=str_replace("'","",$txt_ord_no);

	$txt_lot_no 		=str_replace("'","",$txt_lot_no);
	$txt_date_from 		=str_replace("'","",$txt_date_from);
	$txt_date_to 		=str_replace("'","",$txt_date_to);	

	if(trim($txt_lot_no)!="") $lot_sql_cond=" and d.lot like '%$txt_lot_no%'";

	if($db_type==0){
		$date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2){
		$date_from=change_date_format($txt_date_from,'','',1);
		$date_to=change_date_format($txt_date_to,'','',1);
	}

	if($date_from!="" && $date_to!="")
	{
		$dateCond = "and a.receive_date between '$date_from' and '$date_to'";
	}


	if($cbo_buyer_name>0 || $txt_job_no!="" || $txt_style_ref!="" || $txt_ord_no!="")
	{
		if($cbo_buyer_name==0) $buyer_sql_cond=""; else $buyer_sql_cond="  and a.buyer_name='$cbo_buyer_name'";
		if(trim($txt_job_no)!="") $job_sql_cond=" and a.job_no like '%$txt_job_no%'";
		if(trim($txt_style_ref)!="") $style_sql_cond=" and a.style_ref_no like '%$txt_style_ref%'";
		if(trim($txt_ord_no)!="") $po_sql_con=" and b.po_number like '%$txt_ord_no%'";

		 $sql_job = "select a.job_no, a.buyer_name, a.style_ref_no,b.id,b.po_number,c.booking_no,c.copmposition from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_sql_cond $job_sql_cond $style_sql_cond $po_sql_con group by a.job_no, a.buyer_name, a.style_ref_no,b.id,b.po_number,c.booking_no,c.copmposition";

		$sql_job_result = sql_select($sql_job);
		foreach ($sql_job_result as $row) {
		    $po_ids .= $row[csf('id')].",";

		    /*$job_array[$row[csf('id')]]['po_id'] = $row[csf('id')];
		    $job_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
		    $job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
		    $job_array[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')]; */
		}

		$poIds = chop($po_ids,",");
						
		if($poIds!="")
		{
			$poIdArr=array_unique(explode(",",$poIds));
			if($db_type==2 && count($poIdArr)>999)
			{
				$po_cond=" and (";
				$po_cond2=" and (";
				$poIdArr=array_chunk($poIdArr,999);
				foreach($poIdArr as $poid)
				{
					$poids=implode(",",$poid);
					$po_cond.="c.po_breakdown_id in($poids) or ";
					$po_cond2.="d.po_break_down_id in($poids) or ";
				}
				
				$po_cond=chop($po_cond,'or ');
				$po_cond2=chop($po_cond2,'or ');
				$po_cond.=")";
				$po_cond2.=")";
			}
			else
			{
				$po_cond=" and c.po_breakdown_id in (".implode(",",$poIdArr).")";
				$po_cond2=" and d.po_break_down_id in (".implode(",",$poIdArr).")";
			}
		}
	}
	if($db_type==0)
	{
		$po_breakdown_id_str="group_concat(c.po_breakdown_id) as po_breakdown_id";
	}
	else if($db_type==2)
	{
		$po_breakdown_id_str="listagg(c.po_breakdown_id,',') within group (order by c.po_breakdown_id) as po_breakdown_id";
	}


	$sql = "select a.receive_date,a.company_id,a.recv_number,a.receive_basis,a.issue_id,a.booking_id,a.booking_no,a.booking_without_order,a.knitting_source, a.knitting_company, a.challan_no,a.remarks,b.id as trans_id, b.supplier_id, sum(c.quantity) as issue_rtn_qnty,sum(c.reject_qty) as reject_qnty, d.yarn_type,d.yarn_count_id,d.yarn_comp_type1st,d.yarn_comp_percent1st,d.id as product_id,d.lot,$po_breakdown_id_str 
	 from inv_receive_master a, inv_transaction b, order_wise_pro_details c, product_details_master d 
	 where a.id=b.mst_id and a.item_category=1 and a.entry_form=9 and a.company_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.item_category=1 and b.transaction_type=4 and b.prod_id=d.id and a.receive_basis in(1,2,3,4,8) and b.status_active=1 and b.is_deleted=0 and c.trans_id=b.id and c.trans_type=4 and c.entry_form=9 and c.prod_id=d.id $dateCond $lot_sql_cond  $po_cond 
	 group by a.receive_date,a.company_id,a.recv_number,a.receive_basis,a.issue_id,a.booking_id,a.booking_no,a.booking_without_order,a.knitting_source, a.knitting_company,a.challan_no,a.remarks,b.id,d.yarn_type,b.supplier_id,d.yarn_count_id,d.yarn_comp_type1st,d.yarn_comp_percent1st,d.id,d.lot order by a.receive_date,a.recv_number";
	// echo $sql;
	$result = sql_select($sql);

    foreach ($result as $row) 
    {
        if($row[csf("booking_no")]!="")
        {
            $all_booking_no_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
        }

        if($row[csf("receive_basis")] == 1)
        {
        	$booking_ids.=$row[csf("booking_id")].",";
        }  

        if( $row[csf("receive_basis")] == 3 &&  $row[csf("booking_no")]!="" ){
        	$requisition_nos.=$row[csf("booking_no")].",";
        } 

        if($row[csf("receive_basis")] == 4)
        {
        	$sales_order_ids.=$row[csf("booking_id")].",";
        }        
        
        $buyer_ids.=$row[csf("buyer_id")].",";
        $product_ids.=$row[csf("product_id")].",";
        $yarn_count_ids.=$row[csf("yarn_count_id")].",";          
        $knitting_company_ids.=$row[csf("knitting_company")].",";
        $trans_ids.=$row[csf("trans_id")].",";
        $issue_ids.="'".$row[csf("issue_id")]."',";
        $po_ids.=$row[csf("po_breakdown_id")].",";
    }


    //echo $location_ids; die;       
    $buyer_ids =chop($buyer_ids,",");
    $knitting_company_ids =chop($knitting_company_ids,",");
    $product_ids =chop($product_ids,",");
    $yarn_count_ids =chop($yarn_count_ids,",");
    $requisition_nos =chop($requisition_nos,",");
    $trans_ids =chop($trans_ids,",");
    $issue_ids =chop($issue_ids,",");
    $booking_ids = chop($booking_ids,",");
    $sales_order_ids = chop($sales_order_ids,",");
    $po_ids = chop($po_ids,",");
	if($db_type==0)
	{
		$po_number_str="group_concat(po_number) as po_number";
	}
	else if($db_type==2)
	{
		$po_number_str="listagg(cast(po_number as varchar2(4000)),',') within group (order by po_number) as po_number";
	}
	$po_no_sql = sql_select("select job_no_mst, $po_number_str from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids) group by job_no_mst"); 
	foreach ($po_no_sql as $row) {
		$po_no_arr[$row[csf('job_no_mst')]]['po_number'] = $row[csf('po_number')];
	}

    //$buyer_ids=implode(",",array_filter(array_unique(explode(",",$buyer_ids))));
    //$knitting_company_ids=implode(",",array_filter(array_unique(explode(",",$knitting_company_ids))));
    //$product_ids=implode(",",array_filter(array_unique(explode(",",$product_ids))));
    $yarn_count_ids=implode(",",array_filter(array_unique(explode(",",$yarn_count_ids))));
    

	if($requisition_nos!="")
	{
		$requisitionNumbersArr=array_unique(explode(",",$requisition_nos));

		if($db_type==2 && count($requisitionNumbersArr)>999)
		{
			$requisition_cond=" and (";
			$requisitionNumbersArr=array_chunk($requisitionNumbersArr,999);
			foreach($requisitionNumbersArr as $requisitionNumber)
			{
				$requisitionNumbers=implode(",",$requisitionNumber);
				$requisition_cond.="a.requisition_no in($requisitionNumbers) or ";
			}
			
			$requisition_cond=chop($requisition_cond,'or ');
			$requisition_cond.=")";
		}
		else
		{
			$requisition_cond=" and a.requisition_no in (".implode(",",$requisitionNumbersArr).")";
		}
	} // program no

	if($requisition_cond!="")
	{
		$requsition_sql = "select a.requisition_no,a.prod_id,b.buyer_id,b.booking_no,b.po_id from ppl_yarn_requisition_entry a ,ppl_planning_entry_plan_dtls b where a.knit_id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $requisition_cond group by buyer_id,booking_no,a.requisition_no,a.prod_id,b.po_id";
		$requsition_result = sql_select($requsition_sql);

		$requsion_data_arr = array();
		foreach ($requsition_result as $row) {

			$booking_nos.="'".$row[csf("booking_no")]."',";

			$requsion_data_arr[$row[csf("requisition_no")]][$row[csf("prod_id")]]["booking_no"] = $row[csf("booking_no")];
			$requsion_data_arr[$row[csf("requisition_no")]][$row[csf("prod_id")]]["buyer_id"] = $row[csf("buyer_id")];
		}

		$booking_nos =chop($booking_nos,",");
		$booking_nos=implode(",",array_filter(array_unique(explode(",",$booking_nos))));

		if($booking_nos!="")
		{
			$booking_sql = "select job_no,booking_no from wo_booking_dtls d where d.status_active=1 and d.is_deleted=0 and d.booking_no in($booking_nos) $po_cond2 group by job_no,booking_no";

			$booking_result = sql_select($booking_sql);
			$booking_data_array = array();
		    foreach ($booking_result as $row) 
		    {
		        $booking_data_array[$row[csf('booking_no')]]['job_no'] .= $row[csf('job_no')].",";
		    } 
		}
	}

	// Booking basis
	if($booking_ids!="")
	{
		$bookingIdsArr=array_unique(explode(",",$booking_ids));

		if($db_type==2 && count($bookingIdsArr)>999)
		{
			$booking_cond=" and (";
			$bookingIdsArr=array_chunk($bookingIdsArr,999);
			foreach($bookingIdsArr as $bookingid)
			{
				$bookingids=implode(",",$bookingid);
				$booking_cond.="b.id in($bookingids) or ";
			}
			
			$booking_cond=chop($booking_cond,'or ');
			$booking_cond.=")";
		}
		else
		{
			$booking_cond=" and b.id in (".implode(",",$bookingIdsArr).")";
		}


		$workorder_booking_sql = "select b.id, b.ydw_no, a.job_no, sum(a.yarn_wo_qty) as qnty from wo_yarn_dyeing_mst b, wo_yarn_dyeing_dtls a where b.id=a.mst_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  $booking_cond group by b.id, b.ydw_no, a.job_no"; 
		$work_order_result = sql_select($workorder_booking_sql);
		$yarn_booking_array = array();
		foreach ($work_order_result as $row) 
		{
		    $yarn_booking_array[$row[csf('id')]]['booking_no'] = $row[csf('ydw_no')];
		    $yarn_booking_array[$row[csf('id')]]['job_no'] .= $row[csf('job_no')].",";
		    $all_job_arr[$row[csf('job_no')]] = $row[csf('job_no')];
		}

		$all_JOB_cond=""; $JobCond=""; 
	    $all_job_arr = array_filter($all_job_arr);
	    $all_job_ids="'".implode("','",$all_job_arr)."'";

	    if($db_type==2 && count($all_job_arr)>999)
	    {
	        $all_job_chunk_arr=array_chunk($all_job_arr,999) ;
	        foreach($all_job_chunk_arr as $chunk_arr)
	        {
	            $chunk_arr_value="'".implode("','",$chunk_arr)."'";   
	            $JobCond.=" a.job_no in($chunk_arr_value) or ";  
	        }
	        
	        $all_JOB_cond.=" and (".chop($JobCond,'or ').")";
	    }
	    else
	    {
	        $all_JOB_cond=" and a.job_no in($all_job_ids)";    
	    }

		$sql_job = "select a.job_no, a.buyer_name from wo_po_details_master a where a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 $all_JOB_cond group by a.job_no, a.buyer_name";

		$sql_job_result = sql_select($sql_job);
		$job_data_array = array();
		foreach ($sql_job_result as $row) {
		    $job_data_array[$row[csf('job_no')]]['buyer_id'] = $row[csf('buyer_name')];
		}
	} // work order booking no


	if($sales_order_ids!="") // Sales order basis
	{
		$sales_order_idsArr=array_unique(explode(",",$sales_order_ids));

		if($db_type==2 && count($sales_order_idsArr)>999)
		{
			$sales_ord_cond=" and (";
			$sales_order_idsArr=array_chunk($sales_order_idsArr,999);
			foreach($sales_order_idsArr as $sales_order_id)
			{
				$sales_orders=implode(",",$sales_order_id);
				$sales_ord_cond.="a.id in($sales_orders) or ";
			}
			
			$sales_ord_cond=chop($sales_ord_cond,'or ');
			$sales_ord_cond.=")";
		}
		else
		{
			$sales_ord_cond=" and a.id in (".implode(",",$sales_order_idsArr).")";
		}

		$sales_sql = "select a.id,a.job_no,a.po_job_no,a.sales_booking_no,a.buyer_id,a.po_buyer,a.within_group from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 $sales_ord_cond";

		$sales_result = sql_select($sales_sql);
    	$sales_data_array = array();
    	foreach ($sales_result as $row) {

    		if($row[csf("within_group")]==1)
    		{
    			$sales_data_array[$row[csf("id")]]['job_no'] = $row[csf("po_job_no")];
    			$sales_data_array[$row[csf("id")]]['buyer_id'] = $row[csf("po_buyer")];
    		}else{
    			$sales_data_array[$row[csf("id")]]['job_no'] = $row[csf("job_no")];
    			$sales_data_array[$row[csf("id")]]['buyer_id'] = $row[csf("buyer_id")];
    		}

    		$sales_data_array[$row[csf("id")]]['booking_no'] = $row[csf("sales_booking_no")];
    		
    	}
		
	}  //end sales no


	if($issue_ids!="")
	{
		$issueidsArr=array_unique(explode(",",$issue_ids));

		if($db_type==2 && count($issueidsArr)>999)
		{
			$issueid_cond=" and (";
			$issueidsArr=array_chunk($issueidsArr,999);
			foreach($issueidsArr as $issueid)
			{
				$issueids=implode(",",$issueid);
				$issueid_cond.="a.id in($issueids) or ";
			}
			
			$issueid_cond=chop($issueid_cond,'or ');
			$issueid_cond.=")";
		}
		else
		{
			$issueid_cond=" and a.id in (".implode(",",$issueidsArr).")";
		}

		$issue_sql = "select a.id,sum(b.cons_quantity) as issue_qnty from inv_issue_master a, inv_transaction b where a.item_category=1 and a.entry_form=3 and a.company_id=$cbo_company_name and b.prod_id in($product_ids) and a.id=b.mst_id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 $issueid_cond and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id";
    	
    	$issue_result = sql_select($issue_sql);
    	$issue_qty_array = array();
    	foreach ($issue_result as $row) {
    		$issue_qty_array[$row[csf("id")]] = $row[csf("issue_qnty")];
    	}

    	if($product_ids!="")
    	{
			$allocation_sql = "select job_no,booking_no,po_break_down_id,item_id,qnty from inv_material_allocation_dtls d where d.item_category=1 and d.status_active=1 and d.is_deleted=0 and d.item_id in($product_ids) $po_cond2";
			//echo $allocation_sql;//die;
    		$allocation_result = sql_select($allocation_sql);
    		$allocation_qty_array = array();
    		foreach ($allocation_result as $row) {
    			$allocation_qty_array[$row[csf("job_no")]][$row[csf("booking_no")]][$row[csf("item_id")]] += $row[csf("qnty")];
    		}
		}
		//echo "<pre>";
		//print_r($allocation_qty_array);die;

    	$company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"); 
		//print_r($company_arr );
		 $buyer_arr = return_library_array("select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0", "id", "buyer_name");
		 $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");

		//  echo "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_name' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";
		 $working_company_arr = return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_name' and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id","supplier_name");

    	if($yarn_count_ids!="")
    	{
    		$count_arr = return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and id in($yarn_count_ids) and is_deleted=0", 'id', 'yarn_count');
    	}

	} // program no

	// demand basis
	 $demand_sql = "SELECT b.RECV_NUMBER as return_num,
	 a.KNITTING_SOURCE, a.KNITTING_COMPANY, c.REQUISITION_NO, e.booking_no, f.JOB_NO, f.BUYER_ID, f.SALES_BOOKING_NO,
	 sum(g.QNTY) as allocation_qnty
	 from PPL_YARN_DEMAND_ENTRY_MST a , INV_RECEIVE_MASTER b, PPL_YARN_DEMAND_REQSN_DTLS c, PPL_YARN_REQUISITION_ENTRY d, PPL_PLANNING_ENTRY_PLAN_DTLS e, FABRIC_SALES_ORDER_MST f, INV_MATERIAL_ALLOCATION_MST g
	 where b.BOOKING_NO = a.DEMAND_SYSTEM_NO
		 and c.mst_id = a.id
		 and c.REQUISITION_NO = d.REQUISITION_NO
		 and e.DTLS_ID = d.knit_id
		 and e.po_id = f.id
		 and g.JOB_NO = f.job_no
		 and a.status_active = 1
		 and b.status_active = 1
		 and c.status_active = 1
		 and d.status_active = 1
		 and e.status_active = 1
		 and f.status_active = 1
		 and g.status_active = 1
		 and b.COMPANY_ID = $cbo_company_name
		-- and b.recv_number = 'FAL-YIR-23-00097'
	 group by
		 b.RECV_NUMBER,
	 a.KNITTING_SOURCE, a.KNITTING_COMPANY, c.REQUISITION_NO, e.booking_no, f.JOB_NO, f.BUYER_ID, f.SALES_BOOKING_NO";
	$demand_arr = sql_select($demand_sql);
	$demand_dtls_arr = array();
	foreach($demand_arr as $row)
	{
		$demand_dtls_arr[$row['RETURN_NUM']]['KNITTING_SOURCE'] = $row['KNITTING_SOURCE'];
		$demand_dtls_arr[$row['RETURN_NUM']]['KNITTING_COMPANY'] = $row['KNITTING_COMPANY'];
		$demand_dtls_arr[$row['RETURN_NUM']]['REQUISITION_NO'] = $row['REQUISITION_NO'];
		$demand_dtls_arr[$row['RETURN_NUM']]['BOOKING_NO'] = $row['BOOKING_NO'];
		$demand_dtls_arr[$row['RETURN_NUM']]['JOB_NO'] = $row['JOB_NO'];
		$demand_dtls_arr[$row['RETURN_NUM']]['BUYER_ID'] = $row['BUYER_ID'];
		$demand_dtls_arr[$row['RETURN_NUM']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
		$demand_dtls_arr[$row['RETURN_NUM']]['ALLOCATION_QNTY'] = $row['ALLOCATION_QNTY'];
	}

	//  echo "<pre>";
	//  print_r($demand_dtls_arr);

	ob_start();
	?>
	<style type="text/css">
			.nsbreak{word-break: break-all;}
	</style>

	<fieldset>
		<table width="2345">
			<tr class="form_caption">
				<td colspan="16" align="center">
				Daily Yarn Issue Return Status Report
				<br/>
				<? if(($txt_date_from && $txt_date_to)!=""){ echo $txt_date_from . ' To ' . $txt_date_to; } ?>
				</td>
			</tr>
		</table>

		<table style="margin-top:10px" id="table_header_1" class="rpt_table" width="2445" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
		        <tr>
		            <th width="35">SL</th>
		            <th width="150">Return Number</th> 
		            <th width="140">Return Date</th>
		            <th width="130">Company</th>
		            <th width="100">Basis</th>
		            <th width="100">Return Source</th>
		            <th width="130">Working Company</th>
		            <th width="110">Buyer Name</th>
		            <th width="150">Job No</th>
		            <th width="100">Booking No</th>
		            <th width="100">Order No</th>
		            <th width="130">Return Challan</th> 
		            <th width="120">Composition</th> 
		            <th width="100">Yarn Count</th> 
		            <th width="110">Supplier Name</th> 
		            <th width="100">Yarn Lot</th>
		            <th width="100">Allocation Qty</th>
		            <th width="100">Issue Quantity</th>	
		            <th width="100">Return Quantity</th>
		            <th width="100">Reject  Quantity</th>		         
		            <th>Remarks</th> 
		        </tr>
			</thead>
		</table>

		<div style="width:2465px; max-height:400px; overflow-y:scroll" id="scroll_body">
		
			<table class="rpt_table nsbreak" width="2445" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
				<tbody>
					<?
					$i = 1;
					
					foreach ($result as $row) 
					{
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$issue_qty = $issue_qty_array[$row[csf('issue_id')]];
						
						if($row[csf('receive_basis')]==1) // Booking Basis
						{
							//$booking_no = $yarn_booking_array[$row[csf('booking_id')]]['booking_no'];
							$booking_no = $row[csf("booking_no")];
							$job_numbers = chop($yarn_booking_array[$row[csf('booking_id')]]['job_no']," , ");

							$job_array = explode(",", $job_numbers);
							
							foreach ($job_array as $job_no) 
							{								
								$allocation_qty += $allocation_qty_array[$job_no][$booking_no][$row[csf("product_id")]];
								
								$buyer_id = $job_data_array[$job_no]['buyer_id'];
							}
							$knitting_src = $knitting_source[$row[csf('knitting_source')]];
							$knitting_cmp =  $row[csf('knitting_company')];


						}
						else if ($row[csf('receive_basis')]==2) // Independent
						{
							$job_numbers = "";
							$buyer_id ="";
							$booking_no="";
							$allocation_qty =0;
							$knitting_src = $knitting_source[$row[csf('knitting_source')]];
							$knitting_cmp =  $row[csf('knitting_company')];

						}
						else if($row[csf('receive_basis')]==3) // Requsition
						{
							$allocation_qty =0;
							$booking_no = $requsion_data_arr[$row[csf("booking_no")]][$row[csf("product_id")]]["booking_no"];
							$buyer_id = $requsion_data_arr[$row[csf("booking_no")]][$row[csf("product_id")]]["buyer_id"];
							
							$job_numbers = chop($booking_data_array[$booking_no]['job_no']," , ");

							$job_array = array_unique(explode(",", $job_numbers));
							foreach ($job_array as $job_no) {								
								$allocation_qty += $allocation_qty_array[$job_no][$booking_no][$row[csf("product_id")]];
							}
							$knitting_src = $knitting_source[$row[csf('knitting_source')]];
							$knitting_cmp =  $row[csf('knitting_company')];
							
						}
						else if($row[csf('receive_basis')]==4) // Sales Order basis
						{
							$job_numbers = $sales_data_array[$row[csf('booking_id')]]['job_no'];
    						$buyer_id = $sales_data_array[$row[csf('booking_id')]]['buyer_id'];
    						$booking_no = $sales_data_array[$row[csf('booking_id')]]['booking_no'];
    						$allocation_qty = $sales_data_array[$job_numbers][$booking_no][$row[csf("product_id")]];
							$knitting_src = $knitting_source[$row[csf('knitting_source')]];
							$knitting_cmp =  $row[csf('knitting_company')];

						}
						else if($row[csf('receive_basis')]==8) // demand basis
						{
							$job_numbers = $demand_dtls_arr[$row[csf('recv_number')]]['JOB_NO'];
    						$buyer_id = $demand_dtls_arr[$row[csf('recv_number')]]['BUYER_ID'];
    						$booking_no = $demand_dtls_arr[$row[csf('recv_number')]]['BOOKING_NO'];
    						$allocation_qty = $allocation_qty_array[$job_numbers][$booking_no][$row[csf("product_id")]];
							$knitting_src =  $knitting_source[$demand_dtls_arr[$row['RECV_NUMBER']]['KNITTING_SOURCE']];
							$knitting_cmp =  $demand_dtls_arr[$row['RECV_NUMBER']]['KNITTING_COMPANY'];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" title="" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="35" align="center"><? echo $i; ?></td>
							<td width="150" align="center"><? echo $row[csf('recv_number')];?></td>
							<td width="140" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td width="130"><? echo $company_arr[$row[csf('company_id')]]; ?> </td>
							<td width="100"><? echo $issue_basis[$row[csf('receive_basis')]]; ?></td>
							<td width="100">
							<? 							
								echo $knitting_src; 
							?>
							</td>
							<td width="130">
							<? if ($row[csf('knitting_source')] == 1) {
									echo $company_arr[$knitting_cmp]; 
								} else {
									echo $working_company_arr[$knitting_cmp]; 
								}
							?>
							</td>
							<td width="110">
							<? 
								if ($row[csf('receive_basis')]== 1 || $row[csf('receive_basis')]== 4) {
									echo $supplier_arr[$row[csf('supplier_id')]];
								} else {
									echo $buyer_arr[$buyer_id];
								}
							?>
							</td>
							<td width="150"><? echo $job_numbers; ?></td>
							<td width="100"><? echo $booking_no; ?></td>
							<td width="100"><? echo $po_no_arr[$job_numbers]['po_number']; ?></td>
							<td width="130" align="center"><? echo $row[csf('challan_no')]; ?></td>
							<td width="120"><?  echo $composition[$row[csf('yarn_comp_type1st')]].$row[csf('yarn_comp_percent1st')].'%'; ?></td>
			                <td width="100"><? echo $count_arr[$row[csf('yarn_count_id')]]; ?></td>
			                <td width="110"><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></td>
							<td width="100"><? echo $row[csf('lot')]; ?></td>
							<td width="100" align="right"><? echo number_format($allocation_qty, 2) ?></td>
							<td width="100" align="right"><? echo number_format($issue_qty, 2) ?></td>
			                <td width="100" align="right"><? echo number_format($row[csf('issue_rtn_qnty')], 2) ?></td>
							<td width="100" align="right"><? echo number_format($row[csf('reject_qnty')], 2) ?></td>
							<td>&nbsp;<? echo $row[csf('remarks')]; ?></td>  
						</tr>
						<?
						$grand_allocation_qty += $allocation_qty;
						
						$grand_issue_qty += $issue_qty;
						$grand_issue_rtn_qnty += $row[csf('issue_rtn_qnty')];
						$grand_reject_qnty += $row[csf('reject_qnty')];

						$i++;
					}
					?>
				</tbody>
			</table>
			
			<table class="rpt_table" width="2435" cellpadding="0" cellspacing="0" border="1" rules="all">
		        <tfoot>
		            <th width="35"></th>
		            <th width="150"></th>
		            <th width="140"></th>
		            <th width="130"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="130"></th>
		            <th width="110"></th>
		            <th width="150"></th>
		            <th width="100"></th>
		            <th width="100"></th>
		            <th width="130"></th> 
		            <th width="120"> </th> 
		            <th width="100"></th> 
		            <th width="110"></th> 
		            <th width="100" align="right"><b>Total:</b></th>
		            <th width="100" align="right" id="value_total_alocation_qty"><b><? echo number_format($grand_allocation_qty, 2) ?></b></th>
		            <th width="100" align="right" id="value_total_issue_qty"><b><? echo number_format($grand_issue_qty, 2) ?></b></th>
		            <th width="100" align="right" id="value_total_return_qty"><b><? echo number_format($grand_issue_rtn_qnty, 2) ?></b></th>
		            <th width="100" align="right" id="value_total_reject_qty"><b><? echo number_format($grand_reject_qnty, 2) ?></b></th>
		            <th></th> 
		        </tfoot>
			</table>
		</div>	
	</fieldset> 
	<?
	foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data****$filename****1";
    exit();
}

if($action=="report_generate_party_wise")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));

    $cbo_company_name 	=str_replace("'","",$cbo_company_name);
    $cbo_buyer_name 	=str_replace("'","",$cbo_buyer_name);
    $txt_job_no 		=str_replace("'","",$txt_job_no);
    $txt_style_ref 		=str_replace("'","",$txt_style_ref);
    $txt_ord_no 		=str_replace("'","",$txt_ord_no);
    $txt_lot_no 		=str_replace("'","",$txt_lot_no);
    $txt_date_from 		=str_replace("'","",$txt_date_from);
    $txt_date_to 		=str_replace("'","",$txt_date_to);
    $sql_cond = "";
    if(trim($txt_lot_no) != "") $sql_cond .=" and b.lot like '%$txt_lot_no%'";


    $po_cond = "";
    if($cbo_buyer_name > 0) $po_cond .="  and a.buyer_name='$cbo_buyer_name'";
    if(trim($txt_job_no) != "") $po_cond .=" and a.job_no like '%$txt_job_no%'";
    if(trim($txt_style_ref) !="") $po_cond .=" and a.style_ref_no like '%$txt_style_ref%'";
    if(trim($txt_ord_no) !="") $po_cond .=" and b.po_number like '%$txt_ord_no%'";
    $jobDataArr = array(); $job_id = array(); $jobDataArr = [];
    if($po_cond != "") {
        $sql_job = "select a.job_no, a.style_ref_no,b.id,b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond group by a.job_no, a.style_ref_no,b.id,b.po_number";
        $sql_job_result = sql_select($sql_job);
        foreach ($sql_job_result as $value) {
            array_push($job_id, $value[csf('job_no')]);
            $jobDataArr[$value[csf('job_no')]]['style'] = $value[csf('style_ref_no')];
            $jobDataArr[$value[csf('job_no')]]['po_number'] = $value[csf('po_number')];
        }
        $orderInfoReq = sql_select("select d.requisition_no as REQUISITION_NO from wo_po_details_master a, wo_po_break_down b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d, ppl_planning_entry_plan_dtls e where a.job_no = b.job_no_mst and c.id = d.knit_id and c.id = e.dtls_id and b.id = e.po_id $po_cond ");
//        echo "select d.requisition_no as REQUISITION_NO from wo_po_details_master a, wo_po_break_down b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d, ppl_planning_entry_plan_dtls e where a.job_no = b.job_no_mst and c.id = d.knit_id and c.id = e.dtls_id and b.id = e.po_id $po_cond ";
        if(count($orderInfoReq) > 0){
            $requID = array();
            foreach ($orderInfoReq as $requ){
                if($requ['REQUISITION_NO'] != ""){
                    array_push($requID, $requ['REQUISITION_NO']);
                }
            }
            $jobNoUnique = array_chunk(array_unique($requID),999, true);
            $counter = false;
            $job_no_cond = "";
            foreach ($jobNoUnique as $key => $value){
                if($counter){
                    $job_no_cond .= " or a.requisition_no in (".implode(",", $value).")";
                }else{
                    $job_no_cond .= " and a.requisition_no in (".implode(",", $value).")";
                }
                $counter = true;
            }
        }
    }
    if($db_type==0)
    {
        if($txt_date_from !="" && $txt_date_to !=""){
            $sql_cond .=" and a.transaction_date between '".change_date_format($txt_date_from, "yyyy-mm-dd")."' and '".change_date_format($txt_date_to,"yyyy-mm-dd")."' ";
        }
        $select_transaction_date="DATE_FORMAT(a.transaction_date,'%d-%m-%Y') as ISSUE_DATE";
    }else {
        if ($txt_date_from != "" && $txt_date_to != ""){
            $sql_cond .= " and a.transaction_date between '" . date("j-M-Y", strtotime($txt_date_from)) . "' and '" . date("j-M-Y", strtotime($txt_date_to)) . "' ";
        }
        $select_transaction_date=" to_char(a.transaction_date,'DD-MM-YYYY') as ISSUE_DATE";
    }
    $company_arr = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
    $party_arr = return_library_array("select a.id, a.supplier_name from lib_supplier a, lib_supplier_tag_company b	where a.id=b.supplier_id and b.tag_company=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and a.id in(select supplier_id from lib_supplier_party_type where party_type=91) order by supplier_name", "id", "supplier_name");
    $supplier_arr = return_library_array("select id, supplier_name from lib_supplier where status_active=1 and is_deleted=0", "id", "supplier_name");
	$yarn_type_arr = return_library_array("select YARN_TYPE_ID, YARN_TYPE_SHORT_NAME from LIB_YARN_TYPE where status_active=1 and is_deleted=0", "YARN_TYPE_ID", "YARN_TYPE_SHORT_NAME");
	

    $sqlIssue="select c.recv_number as RECV_NUMBER, a.id as TRANS_ID, $select_transaction_date, a.cons_quantity as RETURN_QTY,
       e.yarn_count as YARN_COUNT, d.brand_name as BRAND_NAME, b.yarn_type, f.color_name as COLOR_NAME, b.id as PROD_ID, c.challan_no as CHALLAN_NO,
       b.item_group_id as ITEM_GROUP_ID, b.sub_group_name as SUB_GROUP_NAME, b.item_description as ITEM_DESCRIPTION, c.issue_id as ISSUE_ID,
       b.product_name_details as PRODUCT_NAME_DETAILS, b.item_code as ITEM_CODE, b.item_size as ITEM_SIZE, c.knitting_company as KNITTING_COMPANY,  
       b.yarn_comp_percent1st as YARN_COMP_PERCENT1ST, b.yarn_comp_percent2nd as YARN_COMP_PERCENT2ND, b.yarn_comp_type1st as YARN_COMP_TYPE1ST,
       b.yarn_comp_type2nd as YARN_COMP_TYPE2ND, b.lot as LOT, h.buyer_job_no as BUYER_JOB_NO, c.knitting_source as KNITTING_SOURCE
    from inv_transaction a,
         product_details_master b left join lib_brand d on d.id = b.brand left join lib_yarn_count e on e.id = b.yarn_count_id left join lib_color f on f.id = b.color,
         inv_receive_master c left join inv_issue_master h on h.id = c.issue_id
	where a.prod_id = b.id and a.mst_id=c.id and c.entry_form = 9 and a.company_id=$cbo_company_name and a.transaction_type in (4) and a.status_active=1 
	  and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $sql_cond $job_no_cond order by c.id desc";
//    echo $sqlIssue; die;
    $div_width="1560px";
    $table_width=1540;
    $sql_issue_result=sql_select($sqlIssue);
    $issueReturnDataArr = array(); $job_no_alt = array(); $issue_id = array();

    foreach ($sql_issue_result as $key => $issueReturnData){
        $parcent1st = "";
        if($issueReturnData["YARN_COMP_PERCENT1ST"] > 0){
            $parcent1st = $issueReturnData["YARN_COMP_PERCENT1ST"]."%";
        }
        $parcent2nd = "";
        if($issueReturnData["YARN_COMP_PERCENT2ND"] > 0 ){
            $parcent2nd = $issueReturnData["YARN_COMP_PERCENT2ND"]."%";
        }
        $compositionStr = $composition[$issueReturnData["YARN_COMP_TYPE1ST"]].' '.$parcent1st.' '.$composition[$issueReturnData["YARN_COMP_TYPE2ND"]].' '.$parcent2nd;
        $item_key = $issueReturnData['RECV_NUMBER']."*##*".$compositionStr."*##*".$issueReturnData["COLOR"]."*##*".$issueReturnData["YARN_COUNT"]."*##*".$issueReturnData["BRAND_NAME"]."*##*".$issueReturnData["LOT"];
        if($issueReturnData['KNITTING_SOURCE'] == 1){
            $partyKey = $company_arr[$issueReturnData['KNITTING_COMPANY']];
        }else{
            $partyKey = $supplier_arr[$issueReturnData['KNITTING_COMPANY']];
        }
        if($issueReturnData["BUYER_JOB_NO"] != ""){
            array_push($job_no_alt, $issueReturnData["BUYER_JOB_NO"]);
        }
        if($issueReturnData["ISSUE_ID"] > 0){
            array_push($issue_id, $issueReturnData["ISSUE_ID"]);
        }
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['date'] = $issueReturnData["ISSUE_DATE"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['count'] = $issueReturnData["YARN_COUNT"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['brand'] = $issueReturnData["BRAND_NAME"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['yarn_type'] = $yarn_type_arr[$issueReturnData["YARN_TYPE"]];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['composition'] = $compositionStr;
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['lot'] = $issueReturnData["LOT"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['return_qty'] += $issueReturnData["RETURN_QTY"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['job_no'] = $issueReturnData["BUYER_JOB_NO"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['color'] = $issueReturnData["COLOR_NAME"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['challan'] = $issueReturnData["CHALLAN_NO"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['recv_number'] = $issueReturnData["RECV_NUMBER"];
        $issueReturnDataArr[$partyKey]['item_data'][$item_key]['issue_id'] = $issueReturnData["ISSUE_ID"];
    }
	// echo "<pre>";
	// print_r($issueReturnDataArr); die;
	//    print_r($issue_id);

    $jobNoUnique = array_chunk(array_unique($job_no_alt),999, true);
    $counter = false;
    $job_no_cond = "";
    foreach ($jobNoUnique as $key => $value){
        if($counter){
            $job_no_cond .= " or a.job_no in ('".implode("','", $value)."')";
        }else{
            $job_no_cond .= " and a.job_no in ('".implode("','", $value)."')";
        }
        $counter = true;
    }
    if(count($jobNoUnique) > 0) {
        $orderInfo = sql_select("select a.style_ref_no, b.po_number, a.job_no, a.buyer_name from wo_po_details_master a, wo_po_break_down b where a.job_no = b.job_no_mst $job_no_cond");
        foreach ($orderInfo as $value){
            $jobDataArr[$value[csf('job_no')]]['po_number'] = $value[csf('po_number')];
            $jobDataArr[$value[csf('job_no')]]['style'] = $value[csf('style_ref_no')];
        }
    }
    $issueIdUnique = array_chunk(array_unique($issue_id),999, true);
    $counter = false;
    $issue_id_cond = "";
    foreach ($issueIdUnique as $key => $value){
        if($counter){
            $issue_id_cond .= " or mst_id in ('".implode("','", $value)."')";
        }else{
            $issue_id_cond .= " and mst_id in ('".implode("','", $value)."')";
        }
        $counter = true;
    }
    $orderInfoArrReq = []; $program_arr = [];
    if(count($issue_id) > 0){
        $valid_issue = array();
//        echo "SELECT requisition_no as REQUISITION_NO, id as ID from inv_transaction where status_active = 1 and is_deleted = 0 $issue_id_cond";
        $req_no = sql_select("SELECT requisition_no as REQUISITION_NO, id as ID from inv_transaction where status_active = 1 and is_deleted = 0 $issue_id_cond");
        foreach ($req_no as $value){
            if($value['REQUISITION_NO'] != ""){
                array_push($valid_issue, $value['ID']);
            }
        }
        $orderInfoReq = sql_select("select a.style_ref_no, f.mst_id as issue_id, b.po_number, a.job_no, a.buyer_name, d.requisition_no from wo_po_details_master a, wo_po_break_down b, ppl_planning_info_entry_dtls c, ppl_yarn_requisition_entry d, ppl_planning_entry_plan_dtls e, inv_transaction f where a.job_no = b.job_no_mst and c.id = d.knit_id and c.id = e.dtls_id and b.id = e.po_id and d.requisition_no = f.requisition_no and f.id in (".implode(",", $valid_issue).")");
        foreach ($orderInfoReq as $value){
            $orderInfoArrReq[$value[csf('issue_id')]]['po_number'] = $value[csf('po_number')];
            $orderInfoArrReq[$value[csf('issue_id')]]['style'] = $value[csf('style_ref_no')];
        }
        $program_arr = return_library_array("select a.id, c.mst_id from ppl_planning_info_entry_dtls a, ppl_yarn_requisition_entry b, inv_transaction c where a.id = b.knit_id and b.requisition_no = c.requisition_no and c.id in (".implode(",", $valid_issue).")", "mst_id", "id");
    }
    ob_start();
    ?>
    <div style="width:<? echo $div_width; ?>">
        <fieldset style="width:<? echo $div_width; ?>">
            <table width="<? echo $table_width; ?>" border="0" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="" align="left">
                <tr class="form_caption" style="border:none;">
                    <td colspan="13" align="center" style="border:none;font-size:16px; font-weight:bold; padding: 0px 2px;" ><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr>
                <?
                 if ($txt_date_from != "" && $txt_date_to != ""){
                ?>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; font-size:14px; padding: 3px 2px;">
                        <strong>Date Range : <? echo $txt_date_from; ?> To <? echo $txt_date_to; ?></strong>
                    </td>
                </tr>
                 <?
                 }
                 ?>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; padding: 4px 2px;">
                        <strong style="font-size:16px;">Yarn Return Received Details</strong>
                    </td>
                </tr>
            </table>
            <br/>
            <table width="<? echo $table_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"  align="left">
                <thead>
                <tr>
                    <th style="padding: 0px 2px;" width="30">SL</th>
                    <th style="padding: 0px 2px;" width="160">Party</th>
                    <th style="padding: 0px 2px;" width="110">Challan No.</th>
                    <th style="padding: 0px 2px;" width="110">Program No.</th>
                    <th style="padding: 0px 2px;" width="80">Date</th>
                    <th style="padding: 0px 2px;" width="160">Style No.</th>
                    <th style="padding: 0px 2px;" width="110">Order No.</th>
                    <th style="padding: 0px 2px;" width="120">Color</th>
                    <th style="padding: 0px 2px;" width="80">Yarn Count</th>
                    <th style="padding: 0px 2px;" width="100">Brand</th>
                    <th style="padding: 0px 2px;" width="100">Yarn Type</th>
                    <th style="padding: 0px 2px;" width="160">Composition</th>
                    <th style="padding: 0px 2px;" width="80">Yarn Lot</th>
                    <th style="padding: 0px 2px;">Qty. In KG</th>

                </tr>
                </thead>
            </table>
            <br/>
            <div style="width:<? echo $div_width; ?>; overflow-y: scroll; max-height:260px;" id="scroll_body">
                <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="1" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                    <tbody>
                    <?
                    $qtyGrandTotal = 0; $counter = 0;
                    foreach ($issueReturnDataArr as $party => $itemData){
                        $total = 0;
                        $counter++;
                        $rowspan = 0;
                        foreach ($itemData['item_data'] as $key => $value ){
                            $qty = $value['return_qty'];
                            $total += $qty;
                            if($counter%2==0) $bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
                            if($rowspan > 0){
                                ?>
                                <tr bgcolor="<?=$bgcolor?>">
                                    <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$value['recv_number']?></td>
                                    <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr[$value['issue_id']]?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$value['date']?></td>
                                    <?
                                    if($value['job_no'] != ""){
                                    ?>
                                        <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$jobDataArr[$value['job_no']]['style']?></td>
                                        <td style="vertical-align: middle; padding: 0px 2px;" width="110"><?=$jobDataArr[$value['job_no']]['po_number']?></td>
                                    <?
                                    }else{
                                    ?>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$orderInfoArrReq[$value['issue_id']]['style']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="110"><?=$orderInfoArrReq[$value['issue_id']]['po_number']?></td>
                                    <?
                                    }
                                    ?>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="120"><?=$value['color']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$value['count']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$value['brand']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$value['yarn_type']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$value['composition']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$value['lot']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                                </tr>

                                <?
                            }else{
                            ?>
                                <tr bgcolor="<?=$bgcolor?>">
                                    <td width="30" align="center" style="vertical-align: middle;padding: 0px 2px;" rowspan="<?=count($itemData['item_data'])+1?>"><?=$counter?></td>
                                    <td width="160" style="vertical-align: middle; padding: 0px 2px;" rowspan="<?=count($itemData['item_data'])+1?>"><?=$party?></td>
                                    <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$value['recv_number']?></td>
                                    <td width="110" style="vertical-align: middle; padding: 0px 2px;" ><?=$program_arr[$value['issue_id']]?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$value['date']?></td>
                                    <?
                                    if($value['job_no'] != ""){
                                        ?>
                                        <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$jobDataArr[$value['job_no']]['style']?></td>
                                        <td style="vertical-align: middle; padding: 0px 2px;" width="110"><?=$jobDataArr[$value['job_no']]['po_number']?></td>
                                        <?
                                    }else{
                                        ?>
                                        <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$orderInfoArrReq[$value['issue_id']]['style']?></td>
                                        <td style="vertical-align: middle; padding: 0px 2px;" width="110"><?=$orderInfoArrReq[$value['issue_id']]['po_number']?></td>
                                        <?
                                    }
                                    ?>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="120"><?=$value['color']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80" align="center"><?=$value['count']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$value['brand']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="100"><?=$value['yarn_type']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="160"><?=$value['composition']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" width="80"><?=$value['lot']?></td>
                                    <td style="vertical-align: middle; padding: 0px 2px;" align="right"><?=number_format($qty, 2)?></td>
                                </tr>
                                <?
                            }
                            $rowspan++;
                        }
                        ?>
                        <tr bgcolor="#d3d3d3">
                            <td style="padding: 0px 2px;" colspan="11" align="right"><strong>Sub Total</strong></td>
                            <td  style="padding: 0px 2px;" align="right"><strong><?=number_format($total, 2)?></strong></td>
                        </tr>

                        <?
                        $qtyGrandTotal += $total;
                    }
                    ?>
                    <tr><td colspan="12"></td></tr>
                    <tr bgcolor="#ffffe0">
                        <td style="padding: 0px 2px;" colspan="13" align="right"><strong>Grand Total</strong></td>
                        <td  style="padding: 0px 2px;" align="right" ><strong><?=number_format($qtyGrandTotal, 2)?></strong></td>
                    </tr>
                    <tr><td colspan="13"></td></tr>
                    </tbody>
                </table>
            </div>
        </fieldset>
    </div>

    <?
    foreach (glob("$user_id*.xls") as $filename) {
        if (@filemtime($filename) < (time() - $seconds_old))
            @unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = $user_id . "_" . $name . ".xls";
    echo "$total_data****$filename****2";
    exit();
}
?>
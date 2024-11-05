<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('setting/mail_setting.php');

	$user_maill_arr=return_library_array("select id,USER_EMAIL from USER_PASSWD","id","USER_EMAIL");

	function fridaydayCount($from, $to, $day = 5) 
	{
		$from = new DateTime($from);
		$to   = new DateTime($to);

		$wF = $from->format('w');
		$wT = $to->format('w');
		if ($wF < $wT)       $isExtraDay = $day >= $wF && $day <= $wT;
		else if ($wF == $wT) $isExtraDay = $wF == $day;
		else                 $isExtraDay = $day >= $wF || $day <= $wT;

		return floor($from->diff($to)->days / 7) + $isExtraDay;
	}
	function saturdaydayCount($from, $to, $day = 6) 
	{
		$from = new DateTime($from);
		$to   = new DateTime($to);

		$wF = $from->format('w');
		$wT = $to->format('w');
		if ($wF < $wT)       $isExtraDay = $day >= $wF && $day <= $wT;
		else if ($wF == $wT) $isExtraDay = $wF == $day;
		else                 $isExtraDay = $day >= $wF || $day <= $wT;

		return floor($from->diff($to)->days / 7) + $isExtraDay;
	}

	list($sysId,$mailId)=explode('__',$data);
	$sysId=str_replace('*',',',$sysId);
	$mailArr[]=str_replace('*',',',$mailId);
	 

	$company_library =return_library_array( "select id, company_name from lib_company where  id in(1,2,3,4,5,6) and status_active=1 and is_deleted=0", "id", "company_name");
	
	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),1))),'','',1);
	$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),2))),'','',1);
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d", $strtotime),0))),'','',1);
	$prev_fifteen_date = change_date_format(date('Y-m-d H:i:s', strtotime('-15 day', strtotime($current_date))),'','',1); 
	$actual_date="is null";
	$date_cond=" and a.pi_date between '$prev_fifteen_date' and '$current_date'";

	 
	//echo $date_cond;
	$sql_lib_company = "select id, company_name from lib_company where status_active=1 and is_deleted=0";
	$sql_lib_company_res=sql_select($sql_lib_company);
	//foreach($sql_lib_company_res as $com)
	foreach($company_library as $compid=>$compname)
	{
		//$compid = 3;
		$company_arr=return_library_array("select id, company_name from lib_company",'id','company_name');
		$supplier_arr=return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$buyer_arr=return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");
		$designation_array=return_library_array( "select id, custom_designation from lib_designation", "id","custom_designation");
		$dept_array=return_library_array( "select id, department_name from lib_department", "id","department_name");
		$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id","user_full_name");
		$ammended_arr=return_library_array( "SELECT a.btb_lc_no,  max(a.amendment_date) as amendment_date FROM com_btb_lc_amendment a GROUP BY a.btb_lc_no", "btb_lc_no","amendment_date");
		
		//echo $compid; echo '<pre>';
		if($db_type==0)	$select_pi_cat = " group_concat(b.item_category_id)";
		else $select_pi_cat = " listagg(b.item_category_id,',') within group(order by b.item_category_id)";

		$sql_bk="SELECT a.ID, a.IMPORTER_ID, a.SUPPLIER_ID, a.PI_DATE, a.PI_NUMBER, a.APPROVED, $select_pi_cat as ITEM_CATEGORY_IDS, a.INSERTED_BY, a.INSERT_DATE, a.REMARKS,
		a.NET_TOTAL_AMOUNT		from com_pi_master_details a, com_pi_item_details b 
		where a.id=b.pi_id and a.importer_id=$compid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.ready_to_approved=1
		$date_cond   and a.entry_form not in (1,0)  
		group by a.id, a.importer_id, a.supplier_id, a.pi_date, a.pi_number, a.approved, a.inserted_by, a.insert_date, a.remarks, a.net_total_amount";

		$sql="SELECT  a.id, a.priority_id, a.importer_id, a.supplier_id, a.pi_date, a.pi_number, a.approved, LISTAGG(b.item_category_id, ',') WITHIN GROUP( ORDER BY  b.item_category_id ) AS item_category_ids, 
			a.inserted_by,    a.insert_date,    a.remarks,    a.net_total_amount,    c.com_btb_lc_master_details_id,    d.insert_date    AS insert_date1,   d.lc_number
			FROM    com_pi_master_details a,com_pi_item_details b, teamerp.com_btb_lc_pi c, teamerp.com_btb_lc_master_details d
			WHERE a.id = b.pi_id    AND c.pi_id = a.id    AND d.id = c.com_btb_lc_master_details_id     AND a.status_active = 1    AND a.is_deleted = 0
			AND a.importer_id in($compid)  $date_cond   
			AND b.status_active = 1    AND c.status_active=1    AND b.is_deleted = 0    AND a.ready_to_approved = 1   AND a.entry_form NOT IN ( 1, 0 )
			GROUP BY    a.id,  a.priority_id,  a.importer_id,    a.supplier_id,    a.pi_date,    a.pi_number,    a.approved,    a.inserted_by,    a.insert_date,   a.remarks,
			a.net_total_amount,    c.com_btb_lc_master_details_id,    d.insert_date,    d.lc_number";
		//echo $sql;die;
		$sql_res=sql_select($sql);

		foreach ($sql_res as $row) {
			$pi_id .= $row['ID'].',';
		}
		$pi_ids = rtrim($pi_id,',');
		
	    $sql_buyer_style = "SELECT b.PI_ID, a.priority_id, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, d.id as WORK_ORDER_DTLS_ID, d.amount as WO_QTY, 1 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_dtls_id=d.id and d.job_no=e.job_no and e.job_no=f.job_no_mst and e.company_name = $compid and a.status_active=1 
		and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and b.item_category_id=1 
		and d.item_category_id=1 and a.id in($pi_ids)
	    union all
	    SELECT b.PI_ID, a.priority_id, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, d.id as WORK_ORDER_DTLS_ID, d.amount as WO_QTY, 2 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_non_order_info_mst c, wo_non_order_info_dtls d, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_id=c.id and c.id=d.mst_id and d.job_no=e.job_no and e.job_no=f.job_no_mst and c.company_name=$compid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 
	    and b.item_category_id=1 and d.item_category_id=1 and c.wo_basis_id=3 and c.entry_form=284 and a.id in($pi_ids)
	    union all 
	    SELECT b.PI_ID, a.priority_id, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 3 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$compid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(4,12,25) and a.id in($pi_ids)
	    union all
	    SELECT b.PI_ID, a.priority_id, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 4 as TYPE
	    from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c, wo_po_details_master e, wo_po_break_down f
	    where a.id=b.pi_id and b.work_order_no=c.booking_no and c.job_no=e.job_no and e.job_no=f.job_no_mst and c.po_break_down_id=f.id and e.company_name=$compid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id in(2,3,13,14) and a.id in($pi_ids)
	    union all
	    SELECT b.PI_ID, a.priority_id, e.BUYER_NAME, e.STYLE_REF_NO, e.JOB_NO, f.id as ORDER_ID, c.id as WORK_ORDER_DTLS_ID, c.amount as WO_QTY, 5 as TYPE 
	    from com_pi_master_details a, com_pi_item_details b, wo_yarn_dyeing_dtls c, wo_po_details_master e, wo_po_break_down f 
	    where a.id=b.pi_id and b.work_order_dtls_id=c.id and c.job_no=e.job_no  and e.job_no=f.job_no_mst and e.company_name=$compid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.item_category_id=24 and a.id in($pi_ids)";
		

	    $sql_buyer_style_res=sql_select($sql_buyer_style); // and a.pi_id=6201
		
	    $buyer_style_arr=array();
	    $work_order_qty_arr=array();
	    $pi_id_arr=array();
	    $order_ids='';
	    $tot_rows=0;
	    foreach($sql_buyer_style_res as $row)
	    {
	        $tot_rows++;
	        if($buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['BUYER_NAME']]=$row['BUYER_NAME'];
	            $buyer_style_arr[$row['PI_ID']]['BUYER_NAME'].=$row['BUYER_NAME'].', ';
	        }
	        if($buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=='')
	        {
	            $buyer_style_arr[$row['PI_ID']][$row['STYLE_REF_NO']]=$row['STYLE_REF_NO'];
	            $buyer_style_arr[$row['PI_ID']]['STYLE_REF_NO'].=$row['STYLE_REF_NO'].', ';
	        }
	        if($work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=='')
	        {
	            $work_order_qty_arr[$row['PI_ID']][$row['WORK_ORDER_DTLS_ID']]=$row['WORK_ORDER_DTLS_ID'];
	            $work_order_qty_arr[$row['PI_ID']]['WORK_ORDER_DTLS_ID'] += $row['WO_QTY'];
	        }
	       
	        $pi_id_arr[$row['ORDER_ID']]=$row['PI_ID'];	        
	        if ($row['ORDER_ID'] != '') $order_ids.=$row['ORDER_ID'].',';
	    }
	    //echo '<pre>'; print_r($work_order_qty_arr);die;

	    if ($order_ids != '')
	    {
	        $orderIds = array_flip(array_flip(explode(',', rtrim($order_ids,','))));
	        $order_id_cond = '';

	        if($db_type==2 && $tot_rows>1000)
	        {
	            $order_id_cond = ' and (';
	            $orderNoArr = array_chunk($orderIds,999);
	            foreach($orderNoArr as $ids)
	            {
	                $ids = implode(',',$ids);
	                $order_id_cond .= " a.wo_po_break_down_id in($ids) or ";
	            }
	            $order_id_cond = rtrim($order_id_cond,'or ');
	            $order_id_cond .= ')';
	        }
	        else
	        {
	            $orderIds = implode(',', $orderIds);
	            $order_id_cond=" and a.wo_po_break_down_id in ($orderIds)";
	        }
	    }
	    //echo $order_id_cond;
	    $sql_lcSc="SELECT a.wo_po_break_down_id as ORDER_ID, b.export_lc_no as LS_SC_NO, b.internal_file_no as FILE_NO, max(b.last_shipment_date) as LAST_SHIPMENT_DATE, 1 as TYPE from com_export_lc_order_info a, com_export_lc b where a.com_export_lc_id=b.id and a.status_active=1 and b.status_active=1 
	    $order_id_cond
	    group by a.wo_po_break_down_id, b.export_lc_no, b.internal_file_no 
	    union all
	    select a.wo_po_break_down_id as ORDER_ID, b.contract_no as LS_SC_NO, b.internal_file_no as FILE_NO, max(b.last_shipment_date) as LAST_SHIPMENT_DATE, 2 as TYPE from com_sales_contract_order_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.status_active=1 and b.status_active=1 $order_id_cond
	    group by a.wo_po_break_down_id, b.contract_no, b.internal_file_no";
		
	    $sql_lcSc_res=sql_select($sql_lcSc);
	    $lcSc_arr=array();
	    foreach ($sql_lcSc_res as $row) 
	    {
	        $lcSc_arr[$pi_id_arr[$row['ORDER_ID']]]['LAST_SHIPMENT_DATE']=$row['LAST_SHIPMENT_DATE'];
	        $lcSc_arr[$pi_id_arr[$row['ORDER_ID']]]['LS_SC_NO']=$row['LS_SC_NO'];
	        $lcSc_arr[$pi_id_arr[$row['ORDER_ID']]]['FILE_NO']=$row['FILE_NO'];
	    }


	    $user_name_array=array();
		//$allowed_days_arr=array('Merchandising & Marketing-BFL'=164,'Management'=13,'Commercial'=2);

		$allowed_days_arr=array(164=>"1",
								13=>"1", 
								2=>"1",
								137=>"1",
								0=>"1",
								13=>"1",
								63=>"1",
								14=>"1",
								74=>"1",
								85=>"1",
								87=>"1",
								151=>"1",
								5555=>"3");
		
		$app_sql="SELECT  a.id,  a.user_name,  a.user_full_name,  a.designation,  b.page_id,  b.entry_form,  b.company_id, a.department_id,c.department_name
					FROM user_passwd a, electronic_approval_setup b, lib_department c
					WHERE b.user_id = a.id AND a.department_id = c.id(+) AND b.entry_form = 27  AND a.status_active = 1 AND b.is_deleted = 0 AND b.company_id = $compid";
		//echo $app_sql;
		$userData=sql_select($app_sql);
		foreach($userData as $user_row)
		{
			$BTB_ID=5555;
			$user_name_array[$user_row['ID']]['USER_ID']=$user_row['ID'];
			$user_name_array[$user_row['ID']]['COMPANY_ID']=$user_row['COMPANY_ID'];
			$user_name_array[$user_row['ID']]['NAME']=$user_row['USER_NAME'];
			$user_name_array[$user_row['ID']]['FULL_NAME']=$user_row['USER_FULL_NAME'];
			$user_name_array[$user_row['ID']]['DESIGNATION']=$designation_array[$user_row['DESIGNATION']];	
			$user_name_array[$user_row['ID']]['DEPARTMENT_NAME']=$user_row['DEPARTMENT_NAME'];
			$user_name_array[$user_row['ID']]['DEPARTMENT_ID']=$user_row['DEPARTMENT_ID'];
			$user_name_array[$user_row['ID']]['ALLOW_DAY']=$allowed_days_arr[$user_row['DEPARTMENT_ID']];
			$user_name_array[$BTB_ID]['ALLOW_DAY']=$allowed_days_arr[$BTB_ID];
			
		}
		  //echo '<pre>';print_r($user_name_array);

		$sql_electronic_app="select USER_ID, APPROVED_BY, SEQUENCE_NO from electronic_approval_setup where company_id=$compid and entry_form=27 and page_id=867 and is_deleted=0 order by sequence_no";
		$sql_electronic_app_res=sql_select($sql_electronic_app);
		$max_electronic_setup=0;
		$electronic_user_arr=array();
	    foreach ($sql_electronic_app_res as $val)
	    { 
	    	$electronic_user_arr[$val['USER_ID']] = $val['USER_ID'];   	
	    	$max_electronic_setup++;
	    }
		//echo '<pre>';print_r($electronic_user_arr);
	   // echo $max_electronic_setup;
	  
	   

	    $user_approval_array=array(); $max_approval_date_array=array();
		$sql_approval="select ENTRY_FORM, MST_ID, APPROVED_BY, APPROVED_DATE, TRUNC(approved_date) as Appv_date, CURRENT_APPROVAL_STATUS from approval_history where entry_form=27 and mst_id in($pi_ids) order by id asc"; // and current_approval_status=1
		$sql_approval_res=sql_select($sql_approval);
		foreach ($sql_approval_res as $row)
		{
			$user_approval_array[$row['MST_ID']][$row['ENTRY_FORM']][$row['APPROVED_BY']]=$row['APPROVED_DATE'];
			$user_appv_date_array[$row['MST_ID']][$row['ENTRY_FORM']][$row['APPROVED_BY']]=$row['APPV_DATE'];
		}

		//echo $pi_ids.'system';

		$sql_app="select max(id) as ID, max(booking_id) as PI_ID from fabric_booking_approval_cause where page_id=867 and entry_form=27 and booking_id in ($pi_ids) and approval_type=0 and status_active=1 and is_deleted=0";
		$sql_app_res=sql_select($sql_app);		
		foreach ($sql_app_res as $val) {
			$approv_cause_id .=$val['ID'].',';
		}
		$approv_cause_ids = rtrim($approv_cause_id,',');

		$app_cause=sql_select("select BOOKING_ID, USER_ID, APPROVAL_CAUSE from fabric_booking_approval_cause where id in ($approv_cause_ids) and status_active=1 and is_deleted=0");
		$approv_pi_arr=array();
		foreach ($app_cause as $val) {
			$approv_pi_arr[$val['BOOKING_ID']][$val['USER_ID']]=$val['APPROVAL_CAUSE'];
		}

		$data_file=sql_select("select IMAGE_LOCATION, MASTER_TBLE_ID from common_photo_library where form_name='proforma_invoice' and is_deleted=0 and file_type=2");
		$file_arr=array();
		foreach($data_file as $row)
		{
			$file_arr[$row['MASTER_TBLE_ID']]['FILE']=$row['IMAGE_LOCATION'];
		}
		unset($data_file);
		
		$sql_btb="SELECT
					a.id                  AS pi_id,
					a.item_category_id    AS item_category_id,
					d.id                  AS btb_id,
					d.btb_system_id       AS btb_system_id,
					d.application_date    AS application_date,
					d.lc_date,
					d.lc_number           AS btb_lc_number,
					d.last_shipment_date,
					d.remarks,
					d.insert_date as btb_insert_date
				FROM
					com_pi_master_details      a,
					com_btb_lc_pi              c,
					com_btb_lc_master_details  d
				WHERE
						a.id = c.pi_id
					AND c.com_btb_lc_master_details_id = d.id
					AND c.status_active = 1
					AND c.is_deleted = 0
					AND d.status_active = 1
					AND d.is_deleted = 0
					AND a.net_total_amount > 0
					AND a.status_active = 1
					AND a.is_deleted = 0";
		$sql_btb_result=sql_select($sql_btb);
		//echo $sql_btb;
		
		foreach($sql_btb_result as $rows)
		{
			$btb_arr[$rows[csf('pi_id')]]['application_date']=$rows[csf('application_date')];
			$btb_arr[$rows[csf('pi_id')]]['btb_lc_number']=$rows[csf('btb_lc_number')];
			$btb_arr[$rows[csf('pi_id')]]['remarks']=$rows[csf('remarks')];
			$btb_arr[$rows[csf('pi_id')]]['last_shipment_date']=$rows[csf('last_shipment_date')];
			$btb_arr[$rows[csf('pi_id')]]['lc_date']=$rows[csf('lc_date')];
			$btb_arr[$rows[csf('pi_id')]]['btb_insert_date']=$rows[csf('btb_insert_date')];
		}
		$table_width=1900+$max_electronic_setup*180;
		ob_start();
		?>
		
        	<table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                   <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                </tr>
                <tr>
                   <td align="center" width="100%" colspan="20" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$compid)]; ?></strong></td>
                </tr>
            </table>	
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $table_width; ?>" class="rpt_table" >
				<thead>
                	<tr>
	                    <th width="30" rowspan="2">SL</th>
	                    <th width="100" rowspan="2" >PI No</th>
						<th width="80" rowspan="2" >PI Priority</th>
	                    <th width="70" rowspan="2">PI Date</th>
	                    <th width="100" rowspan="2">Supplier Name</th>
	                    <th width="60" rowspan="2">View File</th>
	                    <th width="100" rowspan="2">Factory Name</th>
	                    <th width="120" rowspan="2">Buyer</th>
	                    <th width="120" rowspan="2">Style</th>
	                    <th width="100" rowspan="2">PI Insert By</th>						
	                    <th width="120" rowspan="2">PI Insert Date</th>
						<th width="100" rowspan="2">PI Date Variance</th>
	                    <th width="80" rowspan="2">Total PI Value</th>
	                    <?
						foreach($electronic_user_arr as $val)
						{			
							?>
							<th width="160" colspan="2"><?= $user_name_array[$val]['DESIGNATION']; ?>(<?= $user_name_array[$val]['FULL_NAME']; ?>)(<?= $user_name_array[$val]['DEPARTMENT_ID']; ?>)</th>
							<?
						}
						?>
						<th width="80" rowspan="2">BTB TO BE Date</th>
						<th width="80" rowspan="2">BTB Opening Date</th>
						<th width="80" rowspan="2">BTB Ammended Date</th>
						<th width="80" rowspan="2">BTB Tagging Date</th>
						<th width="60" rowspan="2"> BTB Delays </th>
						<th width="80" rowspan="2"> Taken Days </th>
						<th width="160" rowspan="2"> BTB Comments </th>
                    </tr>
                    <tr>
                    	<?
						for($k=1;$k<=$max_electronic_setup;$k++)
						{			
							?>
							<th width="110">Approved Date & Time</th>
							<th width="50">Delays</th>
							
							<?
						}
						?>
                    </tr>
                </thead>
                <tbody>
                    	<? 								 
						$i=1;
						
						$tot_work_order_qty=0;
						$tot_pi_qty=0;
						$tot_surplus_deficit=0;
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						foreach ($sql_res as $row) 
						{	
							$btb_no=$btb_arr[$row[csf('id')]]['btb_lc_number'];
							$priority=$row['PRIORITY_ID'];
							$ammend_date = $ammended_arr[$btb_no]; 
							$btb_dt_check = $btb_arr[$row[csf('id')]]['lc_date'];
							if($btb_dt_check=="")
							{
								$btb_dt_check = $ammend_date;
							}
							else 
							{
								$btb_dt_check =$btb_dt_check;
							}
							if($btb_dt_check=="")
							{
														
								$work_order_qty = $work_order_qty_arr[$row['ID']]['WORK_ORDER_DTLS_ID'];
								$surplus_deficit = $work_order_qty-$row['NET_TOTAL_AMOUNT'];
								$approval_date=$user_approval_array[$row['ID']][$approved_no_array[27][$row[csf('id')]]][$val][27];								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30"><?= $i; ?></td>
									<td width="100" style='color:#000' align="center" style="word-wrap: break-word;"><a href='##' onClick="print_report('<?= $company_name.'*'.$row['ID'].'*'.implode(',',array_unique(explode(",",$row['ITEM_CATEGORY_IDS'])));?>','print', '../../commercial/import_details/requires/pi_print_urmi')"><font color="blue"><b><p style="word-break: break-all;"><?= $row["PI_NUMBER"];?></p></b></font></a></td>			          
									<td width="80" align="center"><p><?=$priority_array[$row['PRIORITY_ID']]; ?></p></td>
									<td width="70" align="center"><p><?= change_date_format($row['PI_DATE']); ?></p></td>
									<td width="100"><p><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
									<td width="60" align="center"><p>
										<a href="javascript:void()" onClick="downloiadFile('<?= $row['ID']; ?>');">
									<? if ($file_arr[$row['ID']]['FILE'] != '') echo 'View File'; ?></a>
									</p></td>
									<td width="100"><p><?= $company_arr[$row['IMPORTER_ID']]; ?></p></td>
									<td width="120"><p>
										<? 
										$buyer_id=chop($buyer_style_arr[$row['ID']]['BUYER_NAME'],',');
										$buyer_name=array_unique(explode(',', $buyer_id));
										$comma_separate_buyer="";
										foreach ($buyer_name as $key => $val) 
										{
											if ($comma_separate_buyer=="") 
											{
											   $comma_separate_buyer.=$buyer_arr[$val];
											}
											else
											{
												$comma_separate_buyer.=','.$buyer_arr[$val];
											}
										}
										echo $comma_separate_buyer;
										
										$y=1;
									
										foreach($electronic_user_arr as $rows)
										{
											
											$approved_date = $user_appv_date_array[$row['ID']][27][$rows];
											$actual_approved_date = $user_appv_date_array[$row['ID']][27][$rows];
											$allow_day = $user_name_array[$rows]['ALLOW_DAY'];
											
											$pi_date_insert=$row['INSERT_DATE'];
											//$pi_date_insert=date_format($row['INSERT_DATE'],"Y/m/d");
											//$approved_date= date('d-M-y', strtotime("+".$allow_day." day", strtotime($approved_date)));
											
											$app_date_serial[$y]['pi_date']= $pi_date_insert;
											$app_date_serial[$y]['actual_date']= $actual_approved_date;
											$app_date_serial[$y]['allow_day']= $allow_day;
											$app_date_serial[$y]['cal_date']= $approved_date;									
											$app_date_serial[$y]['user']= $rows;
											
											$y++;
										}
										//echo '<pre>';print_r($app_date_serial);echo '<pre>';
									
										?>
									</p></td>
									<td width="120"><p><?= chop($buyer_style_arr[$row['ID']]['STYLE_REF_NO'],','); ?></p></td>
									<td width="100"><p><?= $user_arr[$row['INSERTED_BY']]; ?></p></td>
									<td width="120"><p><?= $row['INSERT_DATE']; ?></p></td>
									<td width="100" align="center"><p>
										<?
											$pidate=change_date_format($row['PI_DATE']);
											$dt_pi = new DateTime($pidate);
											$date_pi = $dt_pi->format('m/d/Y');
											$date1=date_create($date_pi);											
											$date2=date_create($pi_date_insert);

											$diff=date_diff($date2,$date1);
											echo $diff->format("%R%a")." Days";
																						

										?>
									</p></td>
									<td width="80" align="right"><p><?= number_format($row['NET_TOTAL_AMOUNT'],2); ?></p></td>
									<?
									
									
									$k=1;
									foreach($electronic_user_arr as $val)
									{	
										$approved_dateTime = $user_approval_array[$row['ID']][27][$val];
										?>
										<td width="110" align="center"><?= $approved_dateTime; ?></td>
										<?
										$pi_date_insert=$row['INSERT_DATE'];
										//$pi_date_insert=date_format($row['INSERT_DATE'],"Y/m/d");
										
										$approved_date = $user_appv_date_array[$row['ID']][27][$val];
										$allow_day = $user_name_array[$val]['ALLOW_DAY'];
										$app_cause=$approv_pi_arr[$row['ID']][$val];

										if($k==1)
										{
											//$tday  = $days." K= ".$k;
											//$k++;
											if($app_date_serial[$k]['actual_date']=="")
											{
												$date_diff_1 = abs(strtotime($pc_date_time) - strtotime($pi_date_insert));
												$friday = fridaydayCount($pc_date_time, $pi_date_insert, 5);// Count Friday
										
												$dt = new DateTime($pc_date_time);
												$date = $dt->format('Y/m/d');
												$date1=date_create($date);											
												$date2=date_create($pi_date_insert);
												$btb_allow_days = $allow_day+$friday;
												
												if($date1>=$date2)
												{
													$diff=date_diff($date2,$date1);
													$tday= $diff->format("%R%a")-$btb_allow_days." Days";									
												}
												else
												{
													$diff=date_diff($date1,$date2);
													$tday= $diff->format("%R%a")-$btb_allow_days." Days";
												}
											}
											else
											{
												$date_diff_1 = abs(strtotime($app_date_serial[$k]['cal_date']) - strtotime($pi_date_insert));
												$friday = fridaydayCount($pi_date_insert, $app_date_serial[$k]['cal_date'], 5);// Count Friday
												$btb_allow_days = $allow_day+$friday;
												
												$dt = new DateTime($app_date_serial[$k]['cal_date']);
												$date = $dt->format('Y/m/d');
												$date1=date_create($date);											
												$date2=date_create($pi_date_insert);
												
												if($date1>=$date2)
												{
													$diff=date_diff($date2,$date1);
													$tday= $diff->format("%R%a")-$btb_allow_days." Days";									
												}
												else
												{
													$diff=date_diff($date1,$date2);
													$tday=$diff->format("%R%a")-$btb_allow_days." Days";
												}
											}
											?>
												<td width="50" bgcolor="<? if($tday>0) {echo "RED";} else  {echo "#FFFFFF";} ?>" align="center" title="<? echo "allow_day : ".$allow_day." Months : ".$months." days : ".$days." Appv Date : ".$approved_date." Friday count : ".$friday." Saturday count : ".$sutday." PI Inser Date ".$pi_date_insert." Date Diff ".$diff->format("%R%a"); ?>">
													<? echo $tday;?>
												</td>
											<?
										}
										if($k>1)
										{
											$sutday=0;
											
											//echo "In2";
											if($app_date_serial[$k]['actual_date']=="")
											{
												//$date_diff = abs(strtotime($pc_date_time) - strtotime($pi_date_insert));
												
												$friday = fridaydayCount($pi_date_insert, $pc_date_time, 5);// Count Friday
												if($user_name_array[$val]['DEPARTMENT_ID']==151)
												{
													$sutday = fridaydayCount($pi_date_insert, $pc_date_time, 6);// Count Friday
												}
												else
												{
													$sutday=0;
												}
												
												$btb_allow_days = $allow_day+$friday+$sutday;
												
												$dt = new DateTime($pc_date_time);
												$date = $dt->format('m/d/Y');
												$date1=date_create($date);											
												$date2=date_create($pi_date_insert);
												
												if($date1>=$date2)
												{
													$diff=date_diff($date2,$date1);
													$tday= $diff->format("%R%a")-$btb_allow_days." Days";									
												}
												else
												{
													$diff=date_diff($date1,$date2);
													$tday=$diff->format("%R%a")-$btb_allow_days." Days";
												}
											}
											else
											{

												$calc_date = $app_date_serial[$k-1]['cal_date'];
												//$pi_date_insert
												//$friday = fridaydayCount($pi_date_insert, $app_date_serial[$k-1]['cal_date'], 5);// Count Friday
												$friday = fridaydayCount($calc_date, $app_date_serial[$k]['cal_date'], 5);// Count Friday
												if($user_name_array[$val]['DEPARTMENT_ID']==151)
												{
													$sutday = saturdaydayCount($calc_date, $app_date_serial[$k]['cal_date'], 6);
												}
												else
												{
													$sutday=0;
												}
												
												$btb_allow_days = $allow_day+$friday+$sutday;
												
												$dt = new DateTime($app_date_serial[$k]['cal_date']);
												$date = $dt->format('m/d/Y');
												$date1=date_create($date);											
												//$date2=date_create($pi_date_insert);
												$date2=date_create($calc_date);
												
												if($date1>=$date2)
												{
													$diff=date_diff($date2,$date1);
													$tday= $diff->format("%R%a")-$btb_allow_days." Days";									
												}
												else
												{
													$diff=date_diff($date1,$date2);
													$tday=$diff->format("%R%a")-$btb_allow_days." Days";
												}
												
											}
											?>
												<td width="50" bgcolor="<? if($tday>0) {echo "RED";} else  {echo "#FFFFFF";} ?>" align="center" title="<? echo "allow_day : ".$allow_day." Months : ".$months." days : ".$days." Appv Date : ".$approved_date." Friday count : ".$friday." Saturday count : ".$sutday." Last Approval Date ".$calc_date; ?>">
													<? echo $tday;?>
												</td>
											<?
											$m++;
										}
										$k++;
										
									}
									if($priority==1){$priority_days = 7;}elseif($priority==2){$priority_days = 3;}else{$priority_days = 1;}// Set priority days Calculation
									?>
									<td width="80" align="center"><p><? echo date('d-m-Y', strtotime($pi_date_insert. ' + '.$priority_days.' days')); //echo $btb_arr[$row[csf('id')]]['lc_date']; ?></p></td>
									<td width="80" align="center"><p><? echo $btb_arr[$row[csf('id')]]['lc_date']; ?></p></td>
									<td width="80" align="center"><p><? $btb_no=$btb_arr[$row[csf('id')]]['btb_lc_number']; echo $ammend_date = $ammended_arr[$btb_no]; ?></p></td>
									<td width="80" align="center"><p><?   //echo $btb_arr[$row[csf('id')]]['btb_insert_date']; ?></p></td>
									<?		
											$print="";
											
											//if($priority==1){$priority_days = 7;}elseif($priority==2){$priority_days = 3;}else{$priority_days = 1;}// Set priority days Calculation
											
											$btb_dt = $btb_arr[$row[csf('id')]]['lc_date'];
											//$btb_dt = $btb_arr[$row[csf('id')]]['btb_insert_date'];
											 $last_approval_date = $app_date_serial[$k]['cal_date'];
											
											$btb_allow_day = $user_name_array['5555']['ALLOW_DAY'];
											
											$btb_approved_date= date('d-M-y', strtotime("+".$btb_allow_day." day", strtotime($btb_dt)));
											if($ammend_date=="")
											{
												if($btb_dt=="")
												{
													$lc_date_diff = abs(strtotime($pc_date_time) - strtotime($app_date_serial[$k]['cal_date']));
													$friday_lc = fridaydayCount($app_date_serial[$k]['cal_date'], $pc_date_time, 5);// Count Friday
													$sutday_lc = saturdaydayCount($app_date_serial[$k]['cal_date'], $pc_date_time, 6);// Count Saturday
													
													$lc_date_diff = abs(strtotime($pc_date_time) - strtotime($app_date_serial[$k]['cal_date']));
													$lc_days = $lc_days-$btb_allow_day-$friday_lc-$sutday_lc;
													$btb_allow_days = $btb_allow_day+$friday_lc+$sutday_lc;
												}
												else
												{
													$friday_lc = fridaydayCount($app_date_serial[$k]['cal_date'], $btb_dt, 5);// Count Friday
													$sutday_lc = saturdaydayCount($app_date_serial[$k]['cal_date'], $btb_dt, 6);// Count Saturday
													
													$lc_date_diff = abs(strtotime($btb_dt) - strtotime($app_date_serial[$k]['cal_date']));
													$lc_days = $lc_days-$btb_allow_day-$friday_lc-$sutday_lc;
													$btb_allow_days = $btb_allow_day+$friday_lc+$sutday_lc;
												}
											}
											else
											{
												$friday_lc = fridaydayCount($app_date_serial[$k]['cal_date'], $ammend_date, 5);// Count Friday
												$sutday_lc = saturdaydayCount($app_date_serial[$k]['cal_date'], $ammend_date, 6);// Count Saturday
												
												$lc_date_diff = abs(strtotime($ammend_date) - strtotime($app_date_serial[$k]['cal_date']));
												$lc_days = $lc_days-$btb_allow_day-$friday_lc-$sutday_lc;
												$btb_allow_days = $btb_allow_day+$friday_lc+$sutday_lc;
											}
											$lc_years = floor($lc_date_diff / (365*60*60*24));
											$lc_months = floor(($lc_date_diff - $lc_years * 365*60*60*24) / (30*60*60*24));
											$lc_days = floor(($lc_date_diff - $lc_years * 365*60*60*24 - $lc_months*30*60*60*24)/ (60*60*24));
											
											$msg = "K=". $k ."  ".$lc_months." Mon ".($lc_days)." Days - friday ".$friday_lc." - Saturday ".$sutday_lc." - allowed days ".$btb_allow_day;
											
											//$lc_days = $lc_days-$btb_allow_day;
											
											if($ammend_date=="")
											{
												if($btb_dt=="")
												{
													$dt = new DateTime($pc_date_time);
													$date = $dt->format('m/d/Y');
													$date1=date_create($date);											
													$date2=date_create($last_approval_date);
													
													if($date1>=$date2)
													{
														$diff=date_diff($date2,$date1);
														$print= $diff->format("%R%a")-$btb_allow_days." Days";											
													}
													else
													{
														//$print="Wrong input";
													}
												}
												else
												{
													$dt = new DateTime($btb_dt);
													$date = $dt->format('m/d/Y');
													$date1=date_create($date);											
													$date2=date_create($last_approval_date);
													
													if($date1>=$date2)
													{
														$diff=date_diff($date2,$date1);
														$print= $diff->format("%R%a")-$btb_allow_days." Days";									
													}
													else
													{
														//$print="Wrong input";
													}
												}
											}
											else
											{
												$dt = new DateTime($ammend_date);
												$date = $dt->format('m/d/Y');
												$date1=date_create($date);											
												$date2=date_create($last_approval_date);
												
												if($date1>=$date2)
												{
													$diff=date_diff($date2,$date1);
													$print=$diff->format("%R%a")-$btb_allow_days." Days";									
												}
												else
												{
													//$print="Wrong input";
												}
											}
											
									?>
									<td width="60" bgcolor="<? if($print>0) {echo "RED";} else  {echo "#FFFFFF";} ?>" title="<? echo $msg; ?>" align="center">
										<? 
											if($print<>"")echo $print;else "Wrong input";
										?>
									</td>
									<td width="80" align="center"><p style="font-size:18px;" title="<? echo "PI Date :".$pi_date_insert." BTB Dt: ".$btb_dt." Diff :";?>" align="center">
										<? 
										
										if($btb_dt=="")
										{
											if($pc_date_time>$pi_date_insert)
											{	
												$new_date = date("m/d/Y",strtotime($pc_date_time));
												$dt = new DateTime($new_date);
												$date = $dt->format('m/d/Y');
												$date1=date_create($date);											
												$date2=date_create($pi_date_insert);
												
												if($date1>$date2)
												{
													$diff=date_diff($date2,$date1);
													$print= $diff->format("%R%a");
														echo $print=$print." Days";
												}
												else
												{
													echo "  ";
												}
												/*
												$date_diff_taken = abs(strtotime($pc_date_time) - strtotime($pi_date_insert));
												
												$years_taken = floor($date_diff_taken / (365*60*60*24));
												$months_taken = floor(($date_diff_taken - $years_taken * 365*60*60*24) / (30*60*60*24));
												$days_taken = floor(($date_diff_taken - $years_taken * 365*60*60*24 - $months_taken*30*60*60*24)/ (60*60*24));
												//echo  $months." Mon ".$days." Days";
												
												if($months_taken!=0) 
												{
													echo $months_taken." M ".$days_taken." D";												
												}
												else  
												{
													echo $days_taken;
												}*/
												
											}
											else
											{
												echo "  ";
											}
										}
										else
										{
											if($btb_dt>$pi_date_insert)
											{
												/*$date_diff_taken = abs(strtotime($btb_dt) - strtotime($pi_date_insert));
												
												$years_taken = floor($date_diff_taken / (365*60*60*24));
												$months_taken = floor(($date_diff_taken - $years_taken * 365*60*60*24) / (30*60*60*24));
												$days_taken = floor(($date_diff_taken - $years_taken * 365*60*60*24 - $months_taken*30*60*60*24)/ (60*60*24));
												//echo  $months." Mon ".$days." Days";
											
												if($months_taken!=0) 
												{
													echo $months_taken." M ".$days_taken." D";												
												}
												else  
												{
													echo $days_taken;
												}*/
												
												$dt = new DateTime($btb_dt);
												$date = $dt->format('m/d/Y');
												$date1=date_create($date);											
												$date2=date_create($pi_date_insert);
												
												if($date1>$date2)
												{
													$diff=date_diff($date2,$date1);
													echo $diff->format("%R%a")." Days";											
												}
												else
												{
													echo "  "; //Wrong Input
												}
											}	
											else
											{
												echo "  ";
											}										
										}
										?>
										</p>
									</td>
									<td width="160" align="left"><p><? echo $btb_arr[$row[csf('id')]]['remarks']; ?></p></td>
								</tr>
								<?
								$i++;
								$tot_work_order_qty += $work_order_qty;
								$tot_pi_qty += $row['NET_TOTAL_AMOUNT'];
								$tot_surplus_deficit += $surplus_deficit;
							}
						}
		                ?>    								
                </tbody> 
				<tfoot>					
					<tr>
						<th width="30">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="120"></th>
						<th width="100"><p>Total :</p></th>
						<th width="80"><p><? echo number_format($tot_pi_qty,2); ?></p></th>
						<?
						for($k=1;$k<=$max_electronic_setup;$k++)
						{			
							?>
							<th width="110"><p></p></th>
							<th width="50"><p></p></th>
							
							<?
						}
						?>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="160">&nbsp;</th>
					</tr>
				</tfoot>
			</table>

		<?
		
		$to="";$message="";
		$sql_mail="SELECT distinct a.company_id, c.email_address,    a.mail_item,    c.user_id,    c.user_type
				FROM   mail_group_mst a, mail_group_child b, user_mail_address  c
				WHERE  b.mail_group_mst_id = a.id   AND b.mail_user_setup_id = c.id and a.company_id=$compid AND a.MAIL_TYPE=1";
		//echo $sql_mail;
		$i=0;
		$mail_sql_res=sql_select($sql_mail);
		foreach($mail_sql_res as $row)
		{
			if(($row[csf('email_address')] != 'mizan@team.com.bd') || ($row[csf('email_address')] != 'nursat.reza@team.com.bd'))
			{
				if ($to=="")  
					$to=$row[csf('email_address')]; 
				else $to=$to.", ".$row[csf('email_address')]; 
			}
		}
		
		
		$header=mailHeader();
		
		$subject="PI Tracking And Approval Delays  Report from last 15 days for ".$company_library[$compid];
		$message=ob_get_contents();
		ob_clean();
			
			$att_file_arr=array();
			
			//$filename="PI_Tracking_and_delays_report_".$company_library[$compid].".xls";
			$filename="PI_Tracking_and_delays_report_".$company_library[$compid].".xls";
			$create_new_doc = fopen($filename, 'w');
			$is_created = fwrite($create_new_doc,$message);
			$att_file_arr[]=$filename.'**'.$filename;
			
			$mail_body = "Please see the attached file for tracking PI of ".$company_library[$compid];
				
			//echo $mail_body;echo '<pre>';echo '<pre>';
			//echo $to;echo '<pre>';echo '<pre>';
			//echo $to;echo '<pre>';echo '<pre>';
			//echo $company_library[$compid]."_id: ".$compid;
			
			/*South End Sweater Co. Ltd._id: 6
				Brothers Fashion Ltd._id: 3
				4A Yarn Dyeing Ltd_id: 4
				C. B. M. International Limited_id: 5
				Mars Stitch Ltd._id: 2
				Gramtech Knit Dyeing Finishing & Garments Industries Ltd_id: 1 
			*/
			
			
			$to='al-amin@team.com.bd, azam.khan@team.com.bd, murshed.alam@team.com.bd, subhasish.biswas@team.com.bd';
			
			if($compid==1)
			{
				$to=$to.", ".'raihan.uddin@team.com.bd, minhajul.arefin@gramtechknit.com, ie.shahadat@gramtechknit.com, rasel@gramtechknit.com, uzzal.dakua@gramtechknit.com, noman.rejwan@gramtechknit.com, mizan.rahman@gramtechknit.com, shahriar@gramtechknit.com, tipu@team.com.bd, rupon@gramtechknit.com, azizul.haq@team.com.bd';
				if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
			}
			elseif($compid==2){
				$to=$to.", ".'rayhan.rahman@marsstitchltd.com, nahiyan.talukdar@marsstitchltd.com,​ jakir.hossain@marsstitchltd.com, jamir.khan@team.com.bd, md.shafiuzzaman@team.com.bd, tauhidul.islam@team.com.bd, md.mahbubul@team.com.bd, abdur.rouf@team.com.bd, rahman.sohel@team.com.bd, nahiyan.talukdar@team.com.bd, hamimulla.abid@team.com.bd, azmal.huda@team.com.bd, mir.forhad@team.com.bd, tuhin.Rasul@team.com.bd, azmal.huda@team.com.bd, shah.alam@marsstitchltd.com, ibrahim@team.com.bd, majharul.anwar@marsstitchltd.com, jakir.hossain@marsstitchltd.com, rayhan.rahman@marsstitchltd.com';
				if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
			}
			elseif($compid==3){
				$to=$to.", ".'bfl_merchandisers@brothersfashion-bd.com, mir.forhad@team.com.bd, kutub@brothersfashion-bd.com, tanveer.hasan@team.com.bd';
				if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
				//echo $message;
			}
			elseif($compid==4){
				$to=$to.", ".'tuhin.Rasul@team.com.bd, allmerchant@4ajacket.com, mir.forhad@team.com.bd, allmarchant@4ajacket.com, zahedul@4ajacket.com, sajib@team.com.bd,
				allmerchant@4ajacket.com,zahedul@4ajacket.com,imtiaz@4ajacket.com,enamul.haque@4ajacket.com,zillur.frp@4ajacket.com,ashraful@4ajacket.com,anwar.hossain@4ajacket.com,abdur.rahim@4ajacket.com,store3@4ajacket.com,dider@4ajacket.com,
				hafizur.rahman@team.com.bd,sajib@team.com.bd,badsha@4ajacket.com,zillur.frp@4ajacket.com,ashraful@4ajacket.com,hafizur.rahman@team.com.bd';
				if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
			}
			elseif($compid==5){
				$to=$to.", ".' cbm_merchandisers@cbm-international.com, mir.forhad@team.com.bd,tuhin.Rasul@team.com.bd,anwar@cbm-international.com, joy@team.com.bd';
				if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
			}
			elseif($compid==6){
				$to=$to.", ".'md.alimujjaman@team.com.bd, mir.forhad@team.com.bd, tuhin.Rasul@team.com.bd, khairul@southendsweater.com, shakawat@southendsweater.com, hasan@southendsweater.com, shibly@southendsweater.com, abdullah.numan@southendsweater.com, md.musa@southendsweater.com, enam@southendsweater.com, md.alimujjaman@team.com.bd, rakib.hasan@southendsweater.com, shohel.ie@southendsweater.com';
				if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
			}
			else{
				$to=$to.", ".'al-amin@team.com.bd, , shofiq@team.com.bd';
				if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
			}
			
			/*
			//active live
			if($compid==3)
			{
				//if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
				echo $message;
			}
			//die;
			*/
	} 
	// End Company
	
	//  allmerchandiser@marsstitchltd.com; cbm_merchandisers@cbm-international.com; bfl_merchandisers@brothersfashion-bd.com; allmerchant@4ajacket.com ; merchandiser@gramtechknit.com
	
?>

</body>
</html>
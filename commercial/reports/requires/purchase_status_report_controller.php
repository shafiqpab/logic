<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="itemCategory_popup")
{

	extract($_REQUEST);
	echo load_html_head_contents("Item Category", "../../../", 1, 1,'','','');

	$result = sql_select("select category_id, short_name from lib_item_category_list where status_active=1 and is_deleted=0 order by short_name");
	?>
	<script>

	$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1);
	});

	function js_set_value( row_id )
	{
		var item_category_id = $('#item_category_id_' + row_id).val()
		var item_category_= $('#item_category_' + row_id).val()
		$('#hidden_item_category_id').val(item_category_id);
		$('#hidden_item_category').val(item_category_);
		parent.emailwindow.hide();
	}
	
	</script>

	</head>
	<body>
	<div style="text-align:center;">
        <fieldset style="width:370px;margin-left:5px">
            <input type="hidden" name="hidden_item_category_id" id="hidden_item_category_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_item_category" id="hidden_item_category" class="text_boxes" value="">
            <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="370" class="rpt_table" >
                <thead>
                    <th width="50" style="text-align:center;">SL</th>
                    <th style="text-align:center">Item Category Name</th>
                </thead>
            </table>
            <div style="width:370px; overflow-y:scroll; max-height:300px;" id="pi_number_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table" id="tbl_list_search" >
				<?
                $i=1;
                foreach($result as $row)
				{
					($i%2==0) ? $bgcolor="#E9F3FF":$bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>)">
                        <td width="50" style="text-align:center"><? echo $i; ?></td>
                        <td style="text-align:left"><p><? echo $row[csf('short_name')];?></p>
                        </td>                       
					</tr>
					<input type="hidden" name="item_category_id_<?php echo $i ?>" id="item_category_id_<?php echo $i ?>" value="<? echo $row[csf('category_id')]; ?>"/>
                    <input type="hidden" name="item_category_<?php echo $i ?>" id="item_category_<?php echo $i ?>" value="<? echo $row[csf('short_name')]; ?>"/>
					<?
					$i++;
                }
                ?>
            </table>
            </div>
            </form>
        </fieldset>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_department_id=str_replace("'","",$cbo_department_id);
	$item_category_id=str_replace("'","", $item_category_id);
	$requisition_no=str_replace("'","", $requisition_no);
	$date_from=str_replace("'","", $txt_date_from);
	$date_to=str_replace("'","", $txt_date_to);
     
	//Purchase Requisition
	$purces_req_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_id."' and module_id=6 and report_id=39 and is_deleted=0 and status_active=1");	
    $purse_format_ids=explode(",",$purces_req_print_report_format);
    $print_btns=$purse_format_ids[0];

	//General Item Receive
	$general_rec_print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$cbo_company_id."' and module_id=6 and report_id=194 and is_deleted=0 and status_active=1");	
    $item_format_ids=explode(",",$general_rec_print_report_format);
    $print_btn=$item_format_ids[0];
	
	$department_cond=$item_category_cond=$requisition_no_cond='';
	if ($cbo_company_id >0) $company_cond=" and a.company_id=$cbo_company_id";
	if ($cbo_department_id >0) $department_cond=" and a.department_id in($cbo_department_id)";
	if ($item_category_id >0) $item_category_cond=" and b.item_category in($item_category_id)";
	if ($requisition_no != '') $requisition_no_cond=" and a.requ_prefix_num=$requisition_no";

	$date_cond='';
	if($date_from != '' && $date_to != '')
	{
		if ($db_type==0) $date_cond=" and a.requisition_date between '$date_from' and '$date_to'";
		else $date_cond=" and a.requisition_date between '$date_from' and '$date_to'";
	}

	$sql_requ="SELECT a.id as REQU_ID, a.requ_no as REQU_NO, d.id as WO_ID, a.requ_prefix_num as REQU_PREFIX_NUM, a.manual_req as MANUAL_REQ, a.requisition_date as REQUISITION_DATE, a.delivery_date as DELIVERY_DATE, a.store_name as STORE_NAME, a.pay_mode as PAY_MODE, a.department_id as DEPARTMENT_ID, b.item_category as ITEM_CATEGORY, b.id as REQ_DTSL_ID, b.product_id as PROD_ID, b.quantity as QUANTITY, b.rate as rate, b.cons_uom as CONS_UOM, b.amount as AMOUNT, a.is_approved as IS_APPROVED, a.company_id as COMPANY_ID, a.location_id as LOCATION_ID, a.remarks as REMARKS, b.remarks as DTLS_REMARKS, a.source as SOURCE, d.delivery_date as WO_DEL_DATE, d.wo_number as WO_NUMBER, c.rate as WO_RATE, c.supplier_order_quantity as WO_QTY, c.amount as WO_AMOUNT, f.pi_id as PI_ID
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	left join wo_non_order_info_dtls c on b.id=c.requisition_dtls_id and c.status_active=1 and c.is_deleted=0  
	left join wo_non_order_info_mst d on d.id=c.mst_id and d.status_active=1 and d.is_deleted=0
	left join com_pi_item_details f on d.id=f.work_order_id and f.item_category_id not in (0,2,3,12,13,14,24,25,28,30) and f.status_active=1 and f.is_deleted=0 
	where a.id=b.mst_id and a.entry_form=69 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category not in (0,2,3,12,13,14,24,25,28,30) $company_cond $department_cond $item_category_cond $requisition_no_cond $date_cond order by a.id desc";
	//echo $sql_requ;
	$sql_requ_res=sql_select($sql_requ);
	$tot_rows=0;
	$requ_ids=$wo_ids=$pi_ids=$prod_ids="";
	$requisition_arr=array();
	$check_requisition_arr=array();
	foreach ($sql_requ_res as $row) 
	{
		if ($check_requisition_arr[$row['REQU_ID']][$row['PROD_ID']]=='')
		{
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQU_ID']=$row['REQU_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQU_NO']=$row['REQU_NO'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQU_PREFIX_NUM']=$row['REQU_PREFIX_NUM'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['MANUAL_REQ']=$row['MANUAL_REQ'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQUISITION_DATE']=$row['REQUISITION_DATE'];		
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['DELIVERY_DATE']=$row['DELIVERY_DATE'];		
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['STORE_NAME']=$row['STORE_NAME'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['PAY_MODE']=$row['PAY_MODE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['IS_APPROVED']=$row['IS_APPROVED'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['DEPARTMENT_ID']=$row['DEPARTMENT_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['ITEM_CATEGORY']=$row['ITEM_CATEGORY'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQ_DTSL_ID'].=$row['REQ_DTSL_ID'].',';
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['PROD_ID'].=$row['PROD_ID'].',';
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['QUANTITY']+=$row['QUANTITY'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['RATE']=$row['RATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['AMOUNT']+=$row['AMOUNT'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['CONS_UOM']=$row['CONS_UOM'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['LOCATION_ID']=$row['LOCATION_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REMARKS']=$row['REMARKS'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['DTLS_REMARKS']=$row['DTLS_REMARKS'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['SOURCE']=$row['SOURCE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_RATE']=$row['WO_RATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_DEL_DATE']=$row['WO_DEL_DATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_NUMBER']=$row['WO_NUMBER'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_QTY']+=$row['WO_QTY'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_AMOUNT']+=$row['WO_AMOUNT'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_RATE']=$row['WO_RATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_IDS'].=$row['WO_ID'].",";
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['PI_IDS'].=$row['PI_ID'].",";
			$check_requisition_arr[$row['REQU_ID']][$row['PROD_ID']]=$row['REQU_ID'].'**'.$row['PROD_ID'];
			
			if ($row['PROD_ID'] != "") $prod_ids.=$row['PROD_ID'].',';
			if ($row['REQU_ID'] != "") $requ_ids.=$row['REQU_ID'].',';
			if ($row['WO_ID'] != "")  $wo_ids.=$row['WO_ID'].',';
			if ($row['PI_ID'] != "")  $pi_ids.=$row['PI_ID'].',';

			$tot_rows++;
		}
	}
	//echo '<pre>';print_r($requisition_arr);die;

	$requ_Ids=implode(',',array_flip(array_flip(explode(',', rtrim($requ_ids,',')))));
	$wo_ids=implode(',',array_flip(array_flip(explode(',', rtrim($wo_ids,',')))));
	$pi_ids=implode(',',array_flip(array_flip(explode(',', rtrim($pi_ids,',')))));

	$booking_reid_cond=$booking_woid_cond=$booking_piid_cond="";
	$booking_reid_cond="a.booking_id in($requ_Ids)";
	if ($wo_ids != "") $booking_woid_cond="or a.booking_id in($wo_ids)";
	if ($pi_ids != "") $booking_piid_cond="or a.booking_id in($pi_ids)";
	//echo $booking_piid_cond;die;

	if ($prod_ids != '')
    {
        $prodIds = array_flip(array_flip(explode(',', rtrim($prod_ids,','))));
        $prod_id_cond = '';
        $prod_id_cond2 = '';

        if($db_type==2 && $tot_rows>1000)
        {
            $prod_id_cond = ' and (';
            $prod_id_cond2 = ' and (';
            $prodIdArr = array_chunk($prodIds,999);
            foreach($prodIdArr as $ids)
            {
                $ids = implode(',',$ids);
                $prod_id_cond .= " id in($ids) or ";
                $prod_id_cond2 .= " b.prod_id in($ids) or ";
            }
            $prod_id_cond = rtrim($prod_id_cond,'or ');
            $prod_id_cond2 = rtrim($prod_id_cond2,'or ');
            $prod_id_cond .= ')';
            $prod_id_cond2 .= ')';
        }
        else
        {
            $prodIds = implode(',', $prodIds);
            $prod_id_cond=" and id in ($prodIds)";
            $prod_id_cond2=" and b.prod_id in ($prodIds)";
        }

        $sql_prod_res=sql_select("select id as ID, item_group_id as ITEM_GROUP_ID, item_description as ITEM_DESCRIPTION from product_details_master where status_active=1 and is_deleted=0 $prod_id_cond");
        $product_arr=array();
        foreach ($sql_prod_res as $val) {
        	$product_arr[$val['ID']]['ITEM_GROUP_ID']=$item_group_arr[$val['ITEM_GROUP_ID']];
        	$product_arr[$val['ID']]['ITEM_DESCRIPTION']=$val['ITEM_DESCRIPTION'];
        }
    }

    // Approval Part
    $sql_approval_res=sql_select("select mst_id as REQU_ID, approved_date as APPROVED_DATE, APPROVED_BY as approved_by from approval_history where mst_id in($requ_Ids) and entry_form=1 and current_approval_status=1");
    $approval_arr=array();
    foreach ($sql_approval_res as $val) {
    	$approval_arr[$val['REQU_ID']]['APPROVED_DATE']=$val['APPROVED_DATE'];
    	$approval_arr[$val['REQU_ID']]['APPROVED_BY']=$user_arr[$val['APPROVED_BY']];
    }

    // MRR Part
    $sql_mrr="SELECT a.ID as MRR_ID, a.booking_id as BOOKING_ID, a.recv_number as RECV_NUMBER, a.receive_date as RECEIVE_DATE, a.challan_no as CHALLAN_NO, a.challan_date as CHALLAN_DATE, a.supplier_id as SUPPLIER_ID, b.order_qnty as MRR_QTY, b.prod_id as PROD_ID, b.order_rate as MRR_RATE, b.order_amount as MRR_AMT, a.company_id as COMPANY_ID, a.receive_basis as RECEIVE_BASIS, a.variable_setting as VARIABLE_SETTING 
	from inv_receive_master a, inv_transaction b 
	where a.id=b.mst_id and a.company_id=$cbo_company_id and b.item_category not in (0,2,3,12,13,14,24,25,28,30) and a.receive_basis in(1,2,7) and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $prod_id_cond2 and ($booking_reid_cond $booking_woid_cond $booking_piid_cond) order by a.receive_date desc";
	//echo $sql_mrr;die;
    $sql_mrr_res=sql_select($sql_mrr);
    $mrr_arr=array();
    $mrr_arr_check=array();
    $row_span_arr=array();
    foreach ($sql_mrr_res as $val) 
	{
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['RECV_NUMBER']=$val['RECV_NUMBER'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['RECEIVE_DATE']=$val['RECEIVE_DATE'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['CHALLAN_NO']=$val['CHALLAN_NO'];
		$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['CHALLAN_DATE']=$val['CHALLAN_DATE'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['SUPPLIER_ID']=$val['SUPPLIER_ID'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['MRR_QTY']=$val['MRR_QTY'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['MRR_RATE']=$val['MRR_RATE'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['MRR_AMT']=$val['MRR_AMT'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['COMPANY_ID']=$val['COMPANY_ID'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['MRR_ID']=$val['MRR_ID'];
    	$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['RECEIVE_BASIS']=$val['RECEIVE_BASIS'];
		$mrr_arr[$val['BOOKING_ID']][$val['RECEIVE_BASIS']][$val['PROD_ID']][$val['RECV_NUMBER']]['VARIABLE_SETTING']="";
    }
    //echo '<pre>';print_r($mrr_arr);die;

	$table_width=2950;
	ob_start();
	
	?>
    <div style="width:<? echo $table_width+30; ?>px; margin-left:5px">

            <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" align="left">
                <tr>
                   <td align="left" width="100%" colspan="24" style="font-size:18px"><strong>Details Report</strong></td>
                </tr>
            </table>
            <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th colspan="16">Requisiton Details</th>
                        <th colspan="3">Approval</th>
                        <th colspan="5">WO Details</th>
                        <th colspan="8">MRR details</th>
                    </tr>
                    <tr>
                    	<!--Requisiton info tot collum 14 width 1350--> 
                        <th width="30">SL</th>
                        <th width="150">Store Name</th>
                        <th width="60">Pay Mode</th>
                        <th width="120">Department</th>
                        <th width="120">Item Category</th>
                        <th width="60">ERP Req. No</th>                      
                        <th width="100">Manual Req. No</th>                      
                        <th width="80">Req. Date</th>
                        <th width="80">Delivery Date</th>
                        <th width="100">Item Group</th>
                        <th width="180">Item Description</th>
                        <th width="60">UOM</th>
                        <th width="80">Req. Quantity</th>
                        <th width="60">Rate</th>
                        <th width="100">Value</th>                        
                        <th width="100">Remarks</th>                        
                        
                        <!--Approval info tot collum 3 width 240--> 
                        <th width="80">Approval Status</th>
                        <th width="80">Last Approval Person</th>
                        <th width="120">Last Approval Date</th>

						  <!--WO Details 400--> 
						<th width="120">WO ERP</th>
                        <th width="80">Delivery Date</th>
                        <th width="80">WO qty</th>
                        <th width="80">Rate</th>
                        <th width="80">Value</th>
                        
                        <!--MRR info tot collum 7 width 640--> 
                        <th width="80">MRR Qnty</th>
                        <th width="60">MRR Rate</th>
                        <th width="100">MRR Value</th>
                        <th width="120">MRR No</th>
                        <th width="80">MRR Date</th>
                        <th width="80">Challan No</th>
						<th width="80">Challan Date</th>
                        <th>Supplier Name</th>
                    </tr>
                </thead>
            </table>

            <div style="width:<? echo $table_width+20; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
		 		<table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body"> 
		 		    <?
		 		    $i=1;
		 		    $tot_requ_qnty=$tot_requ_value=0;
					$tot_mrr_qnty=$tot_mrr_value=0;
					$wo_id_arr=$pi_id_arr=array();
		 		    foreach ($requisition_arr as $key => $prod_data)
		 		    {
		 		    	foreach ($prod_data as $prod_id => $row)
		 		    	{	
		 		    		if ($row['IS_APPROVED']==0) $is_approved="Un-Approved";
		 		    		else $is_approved="Approved";
		 		    		$approval_person=$approval_arr[$row['REQU_ID']]['APPROVED_BY'];		
		 		    		$approval_date=$approval_arr[$row['REQU_ID']]['APPROVED_DATE'];
		 		    		$row_span=$row_span_arr[$row['REQU_ID']][$prod_id];
							
							// echo $key."__".$prod_id."__".$mrr_arr[$key][$prod_id]['RECV_NUMBER']."<br>";
							 //echo $mrr_arr[$key][$row['PROD_ID']]['RECV_NUMBER']."dd";
							 
							 
		 		    		$tot_requ_qnty+=$row['QUANTITY'];
							$tot_requ_value+=$row['AMOUNT'];
							$wo_value=$row['WO_RATE']*$row['SUPPLIER_ORDER_QUANTITY'];
							$wo_rate=0;
							if ($row['WO_AMOUNT']!=0 && $row['WO_QTY'] != 0){
								$wo_rate=$row['WO_AMOUNT']/$row['WO_QTY'];
							}
							
							if(chop($row['WO_IDS'],",")!="") $wo_id_arr=array_unique(explode(",",chop($row['WO_IDS'],",")));
							if(chop($row['PI_IDS'],",")!="") $pi_id_arr=array_unique(explode(",",chop($row['PI_IDS'],",")));
							
		 		    		?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                    	<td width="30" align="center" ><? echo $i; ?></td>
		                        <td width="150" ><p><? echo $store_arr[$row['STORE_NAME']]; ?>&nbsp;</p></td>
		                        <td width="60" ><p><? echo $pay_mode[$row['PAY_MODE']]; ?>&nbsp;</p></td>
		                        <td width="120" ><p><? echo $department_arr[$row['DEPARTMENT_ID']]; ?>&nbsp;</p></td>
		                        <td width="120" ><p><? echo $item_category[$row['ITEM_CATEGORY']]; ?>&nbsp;</p></td>
		                        <td width="60" align="center" ><p> <a href="##" onClick="general_purchase_req_report('<? echo $print_btns;?>','<? echo $row['COMPANY_ID'];?>','<? echo $row['REQU_ID'];?>','','','','<? echo $row['LOCATION_ID'];?>','<? echo $row['REMARKS'];?>','<? echo $row['SOURCE'];?>')"><? echo $row['REQU_PREFIX_NUM']; ?>&nbsp;</a></p></td>
		                        <td width="100" align="center" ><p><? echo $row['MANUAL_REQ']; ?>&nbsp;</p></td>
		                        <td width="80" align="center" ><p><? echo change_date_format($row['REQUISITION_DATE']); ?>&nbsp;</p></td>
		                        <td width="80" align="center" ><p><? echo change_date_format($row['DELIVERY_DATE']); ?>&nbsp;</p></td>
		                        <td width="100" ><p><? echo $product_arr[$prod_id]['ITEM_GROUP_ID']; ?>&nbsp;</p></td>
		                        <td width="180" ><p><? echo $product_arr[$prod_id]['ITEM_DESCRIPTION']; ?>&nbsp;</p></td>
		                        <td width="60" align="center" ><p><? echo $unit_of_measurement[$row['CONS_UOM']]; ?>&nbsp;</p></td>
		                        <td width="80" align="right" ><p><? echo number_format($row['QUANTITY'],2); ?></p></td>
		                        <td width="60" align="right" ><p><? echo number_format($row['AMOUNT']/$row['QUANTITY'],2); ?></p></td>
		                        <td width="100" align="right" ><p><? echo number_format($row['AMOUNT'],2); ?></p></td>
		                        <td width="100" ><p>&nbsp;<? echo $row['DTLS_REMARKS']; ?></p></td>

		                        <td width="80" ><p>&nbsp;<? echo $is_approved; ?></p></td>
		                        <td width="80" ><p>&nbsp;<? echo $approval_person; ?></p></td>
		                        <td width="120" align="center" ><p><? echo $approval_date; ?></p></td>

		                        <td width="120" align="center"  ><p>&nbsp;<? echo  $row['WO_NUMBER'];?></p></td>
		                        <td width="80" align="center"  ><p>&nbsp;<? echo change_date_format($row['WO_DEL_DATE']);?></p></td>
		                        <td width="80"align="right" ><p>&nbsp;<? echo number_format($row['WO_QTY'],2);?></p></td>
		                        <td width="80"  align="right" ><p>&nbsp;<? echo number_format($wo_rate,2); ?></p></td>
		                        <td width="80"  align="right"><p>&nbsp;<? echo number_format($row['WO_AMOUNT'],2); ?></p></td>
		                        <?
								$mrr_qnty=$mrr_rate=$mrr_amt=0;$mrr_num=$rcv_date=$challan_no=$challan_date=$supplier_name=""; 
								foreach ($mrr_arr[$row['REQU_ID']][7][$prod_id] as $key => $val)
								{									
									$mrr_qnty+=$val['MRR_QTY'];
									$mrr_amt+=$val['MRR_AMT'];
									$mrr_num.=$val['RECV_NUMBER'].",";
									$rcv_date=$val['RECEIVE_DATE'];
									if($val['CHALLAN_NO']) $challan_no.=$val['CHALLAN_NO'].",";
									if($val['CHALLAN_DATE']) $challan_date.=change_date_format($val['CHALLAN_DATE']).",";
									if ($val['SUPPLIER_ID']) $supplier_name.= $supplier_arr[$val['SUPPLIER_ID']].",";								
								}

								if(count($wo_id_arr)>0)
								{
									foreach($wo_id_arr as $wo_id)
									{
										foreach ($mrr_arr[$wo_id][2][$prod_id] as $key => $val)
										{
											$mrr_qnty+=$val['MRR_QTY'];
											$mrr_amt+=$val['MRR_AMT'];
											$mrr_num.=$val['RECV_NUMBER'].",";
											$rcv_date=$val['RECEIVE_DATE'];
											if($val['CHALLAN_NO']) $challan_no.=$val['CHALLAN_NO'].",";
											if($val['CHALLAN_DATE']) $challan_date.=change_date_format($val['CHALLAN_DATE']).",";
											if ($val['SUPPLIER_ID']) $supplier_name.= $supplier_arr[$val['SUPPLIER_ID']].",";
										}
									}
								}
								
								if(count($pi_id_arr)>0)
								{
									foreach($pi_id_arr as $pi_id)
									{
										foreach ($mrr_arr[$pi_id][1][$prod_id] as $key => $val)
										{
											$mrr_qnty+=$val['MRR_QTY'];
											$mrr_amt+=$val['MRR_AMT'];
											$mrr_num.=$val['RECV_NUMBER'].",";
											$rcv_date=$val['RECEIVE_DATE'];
											if($val['CHALLAN_NO']) $challan_no.=$val['CHALLAN_NO'].",";
											if($val['CHALLAN_DATE']) $challan_date.=change_date_format($val['CHALLAN_DATE']).",";
											if ($val['SUPPLIER_ID']) $supplier_name.= $supplier_arr[$val['SUPPLIER_ID']].",";											
										}
									}
								}

								if($mrr_amt!=0 && $mrr_qnty!=0) $mrr_rate=$mrr_amt/$mrr_qnty;
								?>
                                <td width="80" align="right"><p><? echo number_format($mrr_qnty,2); ?></p></td>
                                <td width="60" align="right"><p><? echo number_format($mrr_rate,2); ?></p></td>
                                <td width="100" align="right"><p><? echo number_format($mrr_amt,2); ?></p></td>
                                <td width="120"><p>&nbsp;<? echo chop($mrr_num,","); ?></a></p></td>
                                <td width="80" align="center"><p><? echo change_date_format($rcv_date); ?></p></td>
                                <td width="80"><p>&nbsp;<? echo chop($challan_no,","); ?></a></p></td>
								<td width="80"><p>&nbsp;<? echo trim($challan_date,','); ?></a></p></td>
                                <td><p>&nbsp;<? echo rtrim($supplier_name,","); ?></a></p></td>
                            </tr>
                            <?
                            $tot_mrr_qnty+=$mrr_qnty;
                            $tot_mrr_value+=$mrr_amt;
							$i++;						
						}												
					}		
					?>
            	</table>
            </div>
            <table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                <tfoot>
                    <th width="30">&nbsp;</th>
                    <th width="150">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="180">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="80" align="right"><? echo number_format($tot_requ_qnty,2); ?></th>
                    <th width="60">&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($tot_requ_value,2); ?></th>
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="120">&nbsp;</th>

                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>

                    <th width="80" align="right"><? echo number_format($tot_mrr_qnty,2); ?></th>
                    <th width="60">&nbsp;</th>
                    <th width="100" align="right"><? echo number_format($tot_mrr_value,2); ?></th>
                    <th width="120">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
					<th width="80">&nbsp;</th>
                    <th >&nbsp;</th>
                </tfoot>
		</table>

    </div>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}

?>

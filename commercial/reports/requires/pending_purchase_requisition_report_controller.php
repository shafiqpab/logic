<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="item_group_popup")
{
	echo load_html_head_contents("Item Group popup", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$("#item_id").val(str);
			parent.emailwindow.hide(); 
		}
	</script>
	</head>
	<body>
	<div  style="width:930px" >
	<fieldset style="width:930px">
		<form name="order_popup_1"  id="order_popup_1">
			<?
			if ($category!=0) {$item_category_list=" and item_category='$category'";}
			$sql="SELECT id,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter from lib_item_group where is_deleted=0 and status_active=1 $item_category_list";
			$arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$cal_parameter);
			echo create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Cal Parameter", "150,100,200,80,50,50,50","900","320",0, $sql, "js_set_value", "id,item_name", "'load_php_popup_to_form'", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,cal_parameter", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter", "item_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0' );
			?>
		<input type="hidden" id="item_id" />
		</form>
	</fieldset>
	</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<? 																																		
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$user_full_name_arr=return_library_array( "select id, user_full_name from user_passwd",'id','user_full_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$lib_supplier=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_category_id=str_replace("'","", $cbo_category_id);
	$txt_item_group=str_replace("'","", $txt_item_group);
	$txt_item_group_id=str_replace("'","", $txt_item_group_id);
	$txt_item_code=str_replace("'","", $txt_item_code);
	$txt_description=str_replace("'","", $txt_description);
	$txt_requisition_no=str_replace("'","", $txt_requisition_no);
	$cbo_req_status=str_replace("'","", $cbo_req_status);
	$date_from=str_replace("'","", $txt_date_from);
	$date_to=str_replace("'","", $txt_date_to);
	
	$search_cond='';
	if ($cbo_company_id!='') $search_cond.=" and a.company_id in($cbo_company_id)";
	if ($cbo_category_id!=0) $search_cond.=" and b.item_category=$cbo_category_id";
	if ($txt_item_group_id!='') $search_cond.=" and c.item_group_id=$txt_item_group_id";
	if ($txt_item_code != '') $search_cond.=" and LOWER(c.item_code) like LOWER('%$txt_item_code%')";
	if ($txt_description != '') $search_cond.=" and LOWER(c.item_description) like LOWER('%$txt_description%')";
	if ($txt_requisition_no != '') $search_cond.=" and a.requ_prefix_num=$txt_requisition_no";

	if($date_from != '' && $date_to != '')
	{
		if ($db_type==0){$search_cond.= " and a.requisition_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";}
		else{$search_cond.= " and a.requisition_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";}
	}

	$sql_requ="SELECT a.id as REQU_ID, a.requ_no as REQU_NO, a.company_id as COMPANY_ID, a.requisition_date as REQUISITION_DATE, a.store_name as STORE_NAME, a.inserted_by as INSERTED_BY, a.ready_to_approve as READY_TO_APPROVE, a.is_approved as IS_APPROVED, b.item_category as ITEM_CATEGORY, b.id as REQ_DTSL_ID, b.product_id as PRODUCT_ID, b.quantity as QUANTITY, b.cons_uom as CONS_UOM, b.remarks as REMARKS, c.item_description as ITEM_DESCRIPTION, c.item_code as ITEM_CODE, c.item_group_id as ITEM_GROUP_ID
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b, product_details_master c 
	where a.id=b.mst_id and b.product_id=c.id and a.entry_form=69 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $search_cond order by a.id, a.company_id desc";

	$sql_requ_res=sql_select($sql_requ);
    $requDataArr = array();

	foreach ($sql_requ_res as $row) 
	{
		$requ_Ids.=$row['REQU_ID'].',';
		$req_dtsl_ids.=$row['REQ_DTSL_ID'].',';
		$prod_Ids.=$row['PRODUCT_ID'].',';
	}
	if ($requ_Ids != '')
    {
        $requ_Ids = array_flip(array_flip(explode(',', rtrim($requ_Ids,','))));
        $requ_ids_cond = '';

        if($db_type==2 && count($requ_Ids)>1000)
        {
            $requ_ids_cond = ' and (';
            $requ_IdArr = array_chunk($requ_Ids,999);
            foreach($requ_IdArr as $ids)
            {
                $ids = implode(',',$ids);
                $requ_ids_cond .= " mst_id in($ids) or ";
            }
            $requ_ids_cond = rtrim($requ_ids_cond,'or ');
            $requ_ids_cond .= ')';
        }
        else
        {
            $requ_Ids = implode(',', $requ_Ids);
            $requ_ids_cond=" and mst_id in ($requ_Ids)";
        }
    }

    if ($req_dtsl_ids != '')
    {
        $req_dtsl_ids = array_flip(array_flip(explode(',', rtrim($req_dtsl_ids,','))));
        $requ_dtlsids_cond = '';

        if($db_type==2 && count($req_dtsl_ids)>1000)
        {
            $requ_dtlsids_cond = ' and (';
            $requ_IdArr = array_chunk($req_dtsl_ids,999);
            foreach($requ_IdArr as $ids)
            {
                $ids = implode(',',$ids);
                $requ_dtlsids_cond .= " b.requisition_dtls_id in($ids) or ";
            }
            $requ_dtlsids_cond = rtrim($requ_dtlsids_cond,'or ');
            $requ_dtlsids_cond .= ')';
        }
        else
        {
            $req_dtsl_ids = implode(',', $req_dtsl_ids);
            $requ_dtlsids_cond=" and b.requisition_dtls_id in ($req_dtsl_ids)";
        }
    }

    if ($prod_Ids != '')
    {
        $prod_Ids = array_flip(array_flip(explode(',', rtrim($prod_Ids,','))));
        $prod_ids_cond = '';

        if($db_type==2 && count($prod_Ids)>1000)
        {
            $prod_ids_cond = ' and (';
            $requ_IdArr = array_chunk($prod_Ids,999);
            foreach($requ_IdArr as $ids)
            {
                $ids = implode(',',$ids);
                $prod_ids_cond .= " prod_id in($ids) or ";
            }
            $prod_ids_cond = rtrim($prod_ids_cond,'or ');
            $prod_ids_cond .= ')';
        }
        else
        {
            $prod_Ids = implode(',', $prod_Ids);
            $prod_ids_cond=" and prod_id in ($prod_Ids)";
        }
    }
	// WO Part
	if($db_type==0)
	{
		$sql_wo="SELECT b.requisition_dtls_id as REQUISITION_DTLS_ID, group_concat(a.wo_number) as WO_NUMBER, group_concat(a.ready_to_approved) as READY_TO_APPROVED, group_concat(a.gross_rate) as GROSS_RATE, group_concat(a.gross_amount) as gross_amount, group_concat(a.currency_id) as CBO_CURRENCY, group_concat(a.is_approved) as IS_APPROVED, sum(b.supplier_order_quantity) as WO_QUANTITY, a.supplier_id as SUPPLIER_ID 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		where b.mst_id=a.id and a.entry_form in(145,146,147) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $requ_dtlsids_cond group by b.requisition_dtls_id, a.supplier_id ";
	}
	else
	{
		$sql_wo="SELECT b.requisition_dtls_id as REQUISITION_DTLS_ID, listagg(cast(a.wo_number  as varchar(4000)),',') within group(order by a.wo_number ) as WO_NUMBER, listagg(cast(a.ready_to_approved  as varchar(4000)),',') within group(order by a.ready_to_approved) as READY_TO_APPROVED, listagg(cast(b.gross_rate as varchar(4000)),',') within group(order by a.wo_number ) as GROSS_RATE, listagg(cast(b.gross_amount as varchar(4000)),',') within group(order by a.wo_number ) as GROSS_AMOUNT, listagg(cast(a.currency_id as varchar(4000)),',') within group(order by a.wo_number ) as CBO_CURRENCY, listagg(cast(a.is_approved  as varchar(4000)),',') within group(order by  a.is_approved) as IS_APPROVED, sum(b.supplier_order_quantity) as WO_QUANTITY, a.supplier_id as SUPPLIER_ID 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b
		where b.mst_id=a.id and a.entry_form in(145,146,147) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $requ_dtlsids_cond group by b.requisition_dtls_id, a.supplier_id ";
	}
//    echo $sql_wo;

	$sql_wo_res=sql_select($sql_wo);
//    echo "<pre>";
//    print_r($sql_wo_res);
	$wo_arr=array();
	foreach ($sql_wo_res as $key => $row)
	{
		$wo_arr[$row['REQUISITION_DTLS_ID']]['wo_no'][$key]=$row['WO_NUMBER'];
		$wo_arr[$row['REQUISITION_DTLS_ID']]['wo_qnty'][$key]=$row['WO_QUANTITY'];
		$wo_arr[$row['REQUISITION_DTLS_ID']]['ready_to_approved'][$key]=$row['READY_TO_APPROVED'];
        $wo_arr[$row['REQUISITION_DTLS_ID']]['rate'][$key]=$row['GROSS_RATE'];
        $wo_arr[$row['REQUISITION_DTLS_ID']]['amount'][$key]=$row['GROSS_AMOUNT'];
        $wo_arr[$row['REQUISITION_DTLS_ID']]['currency'][$key]=$row['CBO_CURRENCY'];
        $wo_arr[$row['REQUISITION_DTLS_ID']]['is_approved'][$key]=$row['IS_APPROVED'];
		$wo_arr[$row['REQUISITION_DTLS_ID']]['supplier_id'][$key]=$row['SUPPLIER_ID'];
	}
    // Approval Part
    $sql_approval_res=sql_select("select mst_id as REQU_ID, approved_date as APPROVED_DATE, approved_by as APPROVED_BY from approval_history where entry_form=1 and current_approval_status=1 $requ_ids_cond");
	// echo $sql_approval_res;die;
    $approval_arr=array();
    foreach ($sql_approval_res as $val) {
    	$approval_arr[$val['REQU_ID']]['APPROVED_DATE']=$val['APPROVED_DATE'];
    	$approval_arr[$val['REQU_ID']]['APPROVED_BY']=$user_arr[$val['APPROVED_BY']];
    }
	$sql_stock = "SELECT prod_id as PROD_ID, store_id as STORE_ID, sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in (2,3,6) then cons_quantity else 0 end)) as BALANCE_QNTY
	from inv_transaction 
	where status_active=1 and is_deleted=0 $prod_ids_cond group by store_id, prod_id";
	// echo $sql_stock;die;
	$sql_stock_res=sql_select($sql_stock);
	$stock_arr=array();
	foreach ($sql_stock_res as $row) 
	{
		$stock_arr[$row['PROD_ID']][$row['STORE_ID']]['stock']=$row['BALANCE_QNTY'];
	}

	$yes_no_arr=array(0=>'No', 1=>'Yes', 2=>'No');
	$is_approvedArr=array(0=>'No', 1=>'Yes', 2=>'No',3=>'Partial Approved');
	$table_width=2740;
	ob_start();
	
	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>

    <div style="width:<? echo $table_width+30; ?>px; margin-left:5px">
		<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" align="left">
			<tr>
				<td width="100%" class="center" colspan="19" style="font-size:18px"><strong>Pending Purchase Requisition Report</strong></td>
			</tr>
		</table>
		<table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
			<thead>
                <tr>
                    <th colspan="29"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --",4); ?></th>
                </tr>
				<tr>
					<th width="30">SL</th>
					<th width="70">Company</th>
					<th width="120">Req. No</th>
					<th width="80">Req. Date</th>
					<th width="100">Item Code</th>
					<th width="100">Category</th>                      
					<th width="100">Item Group</th>
					<th width="150">Item Descriptions</th>
                    <th width="100">Product ID</th>
                    <th width="100">Remarks</th>
					<th width="80">Req. Qty</th>
					<th width="80">WO Qty.</th>
					<th width="80">Cum. Req. Balance</th>
                    <th width="60">Wo Rate</th>
                    <th width="50">Currency</th>
                    <th width="130">Wo Value</th>
                    <th width="100">WO No</th>
					<th width="100">Supplier</th>
					<th width="100">Store</th>                        
					<th width="80">In-Hand</th>
					<th width="80">UOM</th>
					<th width="100">Req. Insert User</th>
					<th width="150">User Name</th>
					<th width="60">WO Ready To Approve</th>				
					<th width="85">WO Approval Status</th>
					<th width="70">Req. Ready To Approve</th>				
					<th width="85">Req. Approval Status</th>			
					<th width="130">Req. Approve Date</th>
					<th>Req. Approved By</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $table_width+18; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
			<table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body"> 
				<?
				$i=1;
				$tot_requ_qnty=$tot_requ_value=0;
				$tot_mrr_qnty=$tot_mrr_value=0;
				foreach ($sql_requ_res as $row)
				{
					$wo_qnty=array_sum($wo_arr[$row['REQ_DTSL_ID']]['wo_qnty']);
//                    echo $wo_qnty;
					$req_balance=$row['QUANTITY']-$wo_qnty;
					$row_status=1;
					if($cbo_req_status==0 || $cbo_req_status==3)
					{
						if($req_balance!=0){$row_status=1;}else{$row_status=0;}
					}
					else if($cbo_req_status==1)
					{
						if($wo_qnty==''){$row_status=1;}else{$row_status=0;}
					}
					else if($cbo_req_status==2)
					{
						if($wo_qnty!='' && $req_balance!=0){$row_status=1;}else{$row_status=0;}
					}
					else if($cbo_req_status==4)
					{
						if($req_balance==0){$row_status=1;}else{$row_status=0;}
					}
					else if($cbo_req_status==5)
					{
						$row_status=1;
					}

					if($row_status==1)
					{
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" class="wrd_brk center"><? echo $i; ?></td>
							<td width="70" class="wrd_brk center"><p><? echo $company_arr[$row['COMPANY_ID']]; ?>&nbsp;</p></td>
							<td width="120" class="wrd_brk "><p><? echo $row['REQU_NO']; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk center"><p><? echo change_date_format($row['REQUISITION_DATE']); ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk "><p><? echo $row['ITEM_CODE']; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $item_category[$row['ITEM_CATEGORY']]; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $item_group_arr[$row['ITEM_GROUP_ID']]; ?>&nbsp;</p></td>
							<td width="150" class="wrd_brk"><p><? echo $row['ITEM_DESCRIPTION']; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk" align="center"><p><? echo $row['PRODUCT_ID']; ?></p></td>
							<td width="100" class="wrd_brk"><p><? echo $row['REMARKS']; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk right"><p><? echo number_format($row['QUANTITY'],2); ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk right"><p><? echo number_format($wo_qnty,2); ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk right"><p><? echo number_format($req_balance,2); ?>&nbsp;</p></td>
                            <?
                            $rate = array_unique($wo_arr[$row['REQ_DTSL_ID']]['rate']);
                            $currencyId = array_unique($wo_arr[$row['REQ_DTSL_ID']]['currency']);
                            $amount = $wo_arr[$row['REQ_DTSL_ID']]['amount'];
                            $rateConcate = '';
                            $currencyConcate = '';
                            $amountConcate = '';
                            foreach (array_values($rate) as $key => $rateVal){
                                $rateConcate .= number_format($rateVal, 2).((count($rate) - 1) == $key ? '' : ', ');
                            }
                            foreach (array_values($currencyId) as $key => $currencyIdVal){
                                $currencyConcate .= $currency[$currencyIdVal].((count($currencyId) - 1) == $key ? '' : ', ');
                            }
                            foreach (array_values($amount) as $key => $amountVal){
                                $amountConcate .= number_format($amountVal, 2).((count($amount) - 1) == $key ? '' : ', ');
                            }
                            ?>
                            <td width="60" class="wrd_brk right"><p><? echo $rateConcate; ?>&nbsp;</p></td>
                            <td width="50" class="wrd_brk center"><p><? echo $currencyConcate; ?>&nbsp;</p></td>
                            <td width="130" class="wrd_brk right"><p><? echo $amountConcate; ?>&nbsp;</p></td>
                            <td width="100" class="wrd_brk"><p><? echo implode(', ',array_unique($wo_arr[$row['REQ_DTSL_ID']]['wo_no']));?>&nbsp;</p></td>
                            <?
                            $supId =  array_unique($wo_arr[$row['REQ_DTSL_ID']]['supplier_id']);
                            $suppliersConcate = '';
                            foreach (array_values($supId) as $key => $suppliers_id){
                                $suppliersConcate .= $lib_supplier[$suppliers_id].((count($supId) - 1) == $key ? '' : ',');
                            }
                            ?>
                            <td width="100" class="wrd_brk"><p><?=$suppliersConcate?></p></td>
							<td width="100" class="wrd_brk"><p><? echo $store_arr[$row['STORE_NAME']]; ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk right"><p><? echo number_format($stock_arr[$row['PRODUCT_ID']][$row['STORE_NAME']]['stock'],2); ?>&nbsp;</p></td>
							<td width="80" class="wrd_brk center"><p><? echo $unit_of_measurement[$row['CONS_UOM']]; ?>&nbsp;</p></td>
							<td width="100" class="wrd_brk"><p><? echo $user_arr[$row['INSERTED_BY']]; ?>&nbsp;</p></td>
							<td width="150" class="wrd_brk"><p><? echo $user_full_name_arr[$row['INSERTED_BY']]; ?>&nbsp;</p></td>
							<td width="60" class="wrd_brk">
								<p>
									<? 
//										$ready_to_approved_arr=explode(',',$wo_arr[$row['REQ_DTSL_ID']]['ready_to_approved']);
										$ready_to_approved_info='';
										foreach($wo_arr[$row['REQ_DTSL_ID']]['ready_to_approved'] as $val)
										{
											$ready_to_approved_info.=$yes_no_arr[$val].', ';
										}
										echo rtrim($ready_to_approved_info,', ');
									?>&nbsp;
								</p>
							</td>
							<td width="85" class="wrd_brk">
								<p>
									<? 
//										$is_approved_arr=explode(',',$wo_arr[$row['REQ_DTSL_ID']]['is_approved']);
										$is_approved_info='';
										foreach($wo_arr[$row['REQ_DTSL_ID']]['is_approved'] as $val)
										{
											$is_approved_info.=$is_approvedArr[$val].', ';
										}
										echo rtrim($is_approved_info,', ');
									?>&nbsp;
								</p>
							</td>
							<td width="70" class="wrd_brk"><p><? echo $yes_no_arr[$row['READY_TO_APPROVE']]; ?>&nbsp;</p></td>
							<td width="85" class="wrd_brk"><p><? echo $is_approvedArr[$row['IS_APPROVED']]; ?>&nbsp;</p></td>
							<td width="130" class="wrd_brk center"><p>&nbsp;<? echo $approval_arr[$row['REQU_ID']]['APPROVED_DATE'];	?></p></td>
							<td class="wrd_brk"><p><? echo $approval_arr[$row['REQU_ID']]['APPROVED_BY'];?></p></td>
							<?		                        
						$i++;							
					}												
				}		
				?>
			</table>
		</div>
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

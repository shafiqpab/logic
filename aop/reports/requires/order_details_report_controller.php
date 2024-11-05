<? 
header('Content-type:text/html; charset=utf-8');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
include('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$party_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$companyArr=return_library_array( "select id, company_name from lib_company",'id','company_name');
					
if ($action=="load_drop_down_buyer_buyer")
{
    $data=explode("_",$data);
    if($data[1]==1)
    {
        echo create_drop_down( "txt_buyer_buyer_no", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", "", "");
    }
    else
    {
       echo '<input name="txt_buyer_buyer_no" id="txt_buyer_buyer_no" class="text_boxes" style="width:110px"  placeholder="Write">';
    }   
    exit();  
} 

if ($action=="load_drop_down_buyer")
{
    $data=explode("_",$data);

    if($data[1]==1)
    {
        echo create_drop_down( "cbo_party_name", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Party --", $data[2], "");
    }
    else
    {
        echo create_drop_down( "cbo_party_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $data[2], "" );
    }   
    exit();  
} 

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id)
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    <input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$year_job = str_replace("'","",$year);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year_job";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year_job";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
    
    if($cbo_buyer_name==0) $buyer_cond=""; else $buyer_cond=" and a.party_id='$cbo_buyer_name'";
    
    $buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	
    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref from subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.entry_form=278 and a.company_id='$company_id' and a.within_group='$within_group' $buyer_cond group by a.party_id, a.subcon_job, a.job_no_prefix_num, a.insert_date, b.order_no, b.cust_style_ref  order by a.subcon_job DESC";	
	
    ?>
    <table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="80">Job no</th>
            <th width="70">Year</th>
            <th width="130">Buyer</th>
            <th width="110">Style</th>
            <th width="120">Po number</th>
       </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
    <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
     <? $data_array=sql_select($sql);
        $i=1;
		 foreach($data_array as $row)
		 {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		    ?>
			<tr bgcolor="<? echo $bgcolor; ?>"  onClick="js_set_value('<? echo $row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer;">
				<td width="30"><? echo $i; ?></td>
				<td align="center"  width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
				<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
				<td width="130"><? echo $buyer[$row[csf('party_id')]]; ?></td>
				<td width="110"><p><? echo $row[csf('cust_style_ref')]; ?></p></td>
				<td width="120"><p><? echo $row[csf('order_no')]; ?></p></td>
			</tr>
			<? $i++; 
			} 
		?>
    </table>
    </div>
	<script> setFilterGrid("table_body2",-1); </script>
    <?
	disconnect($con);
	exit();
}

if($action=="order_no_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script type="text/javascript">
		function js_set_value(id)
		{ 
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" /> 
	<?
	$buyer = str_replace("'","",$buyer_name);
	$year = str_replace("'","",$year);
	$buyer = str_replace("'","",$buyer_name);
	$job_no = str_replace("'","",$job_no);

    if($db_type==0) 
	{
		$year_field="year(a.insert_date) as year"; 
		 if(trim($year)!=0) $year_field_cond="and YEAR(a.insert_date)=$year";  else $year_field_cond="";
	}
    else if($db_type==2) 
	{
		$year_field="to_char(a.insert_date,'YYYY') as year";
		 if(trim($year)!=0) $year_field_cond=" and to_char(a.insert_date,'YYYY')=$year"; else $year_field_cond="";
	}
    else 
	{
		$year_field="";
		$year_field_cond="";
	}
	
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num=$job_no";
	//echo $buyer;die;
	
	if(trim($buyer)==0) $sub_buyer_name_cond=""; else $sub_buyer_name_cond=" and a.party_id=$buyer";
	
	 $sql="select distinct a.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b where b.job_no_mst=a.subcon_job and a.id=b.mst_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond and a.entry_form=278 and a.within_group='$within_group' and a.is_deleted =0 group by a.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date order by a.id DESC";	
	
	$buyer=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );
	?>
	<table width="500" border="1" rules="all" class="rpt_table">
        <thead>
            <tr>
                <th width="30">SL</th>
                <th width="100">Order Number</th>
                <th width="50">Job no</th>
                <th width="80">Buyer</th>
                <th width="40">Year</th>
            </tr>
        </thead>
    </table>
    <div style="max-height:340px; overflow:auto;">
        <table id="table_body2" width="500" border="1" rules="all" class="rpt_table">
			<? 
			$rows=sql_select($sql);
            $i=1;
            foreach($rows as $data)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value('<? echo $data[csf('id')].'_'.$data[csf('po_number')]; ?>')" style="cursor:pointer;">
                    <td width="30"><? echo $i; ?></td>
                    <td width="100"><p><? echo $data[csf('po_number')]; ?></p></td>
                    <td width="50"><p><? echo $data[csf('job_prefix')]; ?></p></td>
                    <td width="80"><p><? echo $buyer[$data[csf('buyer_name')]]; ?></p></td>
                    <td width="40" align="center"><p><? echo $data[csf('year')]; ?></p></td>
				</tr>
				<? $i++; 
			} ?>
		</table>
	</div>
	<script> setFilterGrid("table_body2",-1); </script>
	<?
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
    extract(check_magic_quote_gpc( $process )); 
	//'cbo_company_id*cbo_within_group*cbo_party_name************',"../../")+'&type='+type;
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_reference_no=str_replace("'","",$txt_reference_no);
	$txt_buyer_buyer_no=str_replace("'","",$txt_buyer_buyer_no);
	$txt_buyer_po=str_replace("'","",$txt_buyer_po);
	$txt_buyer_style=str_replace("'","",$txt_buyer_style);
	//$cbo_report_type=str_replace("'","",$cbo_report_type);
	$cbo_delivery_status=str_replace("'","",$cbo_delivery_status);
	$cbo_basedon=str_replace("'","",$cbo_basedon);
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'"; else $within_group_cond="";
	if($cbo_party_name!=0) $party_cond=" and a.party_id='$cbo_party_name'"; else $buyer_id_cond="";
	if($txt_job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($txt_job_no) ";
	if($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
	//if($txt_order_id!='') $order_id_cond=" and b.id in ($txt_order_id)"; else $order_id_cond="";
	if($txt_reference_no!='') $reference_no_cond=" and a.aop_reference like '%$txt_reference_no%'"; else $reference_no_cond="";
	if($cbo_within_group==1)
	{
		if($txt_buyer_buyer_no!=0) $buyer_buyer_cond=" and b.buyer_buyer='$txt_buyer_buyer_no'"; else $buyer_buyer_cond="";
		if ($txt_buyer_style!='') $buyer_style_cond=" and b.buyer_style_ref like '%$txt_buyer_style%'"; else $buyer_style_cond="";
	} 
	else
	{
		if ($txt_buyer_buyer_no!='') $buyer_buyer_cond=" and b.buyer_buyer like '%$txt_buyer_buyer_no%'"; else $buyer_buyer_cond="";
		if ($txt_style_ref!='') $style_ref_cond=" and b.cust_style_ref like '%$txt_style_ref%'"; else $style_ref_cond="";
	}
	if ($txt_buyer_po!='') $buyer_po_cond=" and b.buyer_po_no like '$txt_buyer_po'"; else $buyer_po_cond="";
	if ($cbo_delivery_status!=0) $delivery_status_cond=" and b.delivery_status='$cbo_delivery_status'"; else $delivery_status_cond="";
	
	$orderRec_cond=""; $woDate_cond=""; $deliveryDate_cond="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
		}
		
		if($cbo_basedon==1) $orderRec_cond=" and a.receive_date between '$start_date' and '$end_date'";
		//else if($cbo_basedon==2) $woDate_cond=" and a.booking_date between '$start_date' and '$end_date'";
		else if($cbo_basedon==3) $deliveryDate_cond=" and a.product_date between '$start_date' and '$end_date'";
	}
	
	$deliveryOrderId=""; $deliveryOrderId_cond="";
	if($cbo_basedon==3 && str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$deliverySql="select b.order_id from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=307 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $deliveryDate_cond";
		$deliverySqlRes=sql_select($deliverySql);
		foreach($deliverySqlRes as $row)
		{
			if($deliveryOrderId=='') $deliveryOrderId=$row[csf('order_id')]; else $deliveryOrderId.=','.$row[csf('order_id')];
		}
		unset($deliverySqlRes);
		
		$deliveryOrderIds=implode(",",array_filter(array_unique(explode(",",$deliveryOrderId))));
		$deliveryOrder_ids=count(explode(",",$deliveryOrderIds));
		if($db_type==2 && $deliveryOrder_ids>1000)
		{
			$deliveryOrderId_cond=" and (";
			$deliveryOrderIdsArr=array_chunk(explode(",",$deliveryOrderIds),999);
			foreach($deliveryOrderIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$deliveryOrderId_cond.=" b.id in($ids) or"; 
			}
			$deliveryOrderId_cond=chop($deliveryOrderId_cond,'or ');
			$deliveryOrderId_cond.=")";
		}
		else $deliveryOrderId_cond=" and b.id in($deliveryOrderIds)";
	}
	
	$job_sql = "SELECT a.id, a.subcon_job, a.within_group, a.party_id, a.delivery_date, a.aop_reference, a.receive_date, a.aop_order_type, a.currency_id, b.id as job_dtls_id, b.order_no, b.order_quantity, b.amount, b.order_uom, b.buyer_po_id, b.buyer_style_ref, b.buyer_buyer, b.aop_color_id, b.construction, b.gsm, b.fin_dia,a.remarks
	from subcon_ord_mst a, subcon_ord_dtls b
	where a.subcon_job=b.job_no_mst and a.company_id=$cbo_company_id and a.status_active=1 
	and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=278 $within_group_cond $party_cond $job_no_cond $order_no_cond $reference_no_cond $buyer_buyer_cond $style_ref_cond $buyer_style_cond $buyer_po_cond $orderRec_cond $delivery_status_cond $deliveryOrderId_cond order by a.id DESC";
	//echo $job_sql; die;
	$job_sqlResult=sql_select($job_sql);
	
	$monthSummery_arr=array(); $buyerSummery=array(); $inhouseDtlsArr=array(); $subconDtlsArr=array(); $subcondtlsid=""; $orderDtls_arr=array();
	foreach($job_sqlResult as $row)
	{
		if($subcondtlsid=='') $subcondtlsid="'".$row[csf('job_dtls_id')]."'"; else $subcondtlsid.=",'".$row[csf('job_dtls_id')]."'";
		$monthNYear=date("Y-m",strtotime($row[csf("delivery_date")]));
		$orderDtls_arr[$row[csf('job_dtls_id')]]['month']=$monthNYear;
		$orderDtls_arr[$row[csf('job_dtls_id')]]['buyer']=$row[csf('buyer_buyer')];
		if($row[csf('within_group')]==1)
		{
			$buyerSummery[$row[csf('buyer_buyer')]]+=$row[csf('order_quantity')];
			$monthSummery_arr[$monthNYear]['yes']+=$row[csf('order_quantity')];
			$inhouseDtlsArr[$row[csf('job_dtls_id')]]['data']=$row[csf('receive_date')].'__'.$row[csf('party_id')].'__'.$row[csf('subcon_job')].'__'.$row[csf('aop_order_type')].'__'.$row[csf('buyer_buyer')].'__'.$row[csf('aop_reference')].'__'.$row[csf('order_no')].'__'.$row[csf('buyer_style_ref')].'__'.$row[csf('currency_id')].'__'.$row[csf('aop_color_id')].'__'.$row[csf('construction')].'__'.$row[csf('gsm')].'__'.$row[csf('fin_dia')].'__'.$row[csf('order_quantity')].'__'.$row[csf('remarks')];
		}
		
		if($row[csf('within_group')]==2)
		{
			//$buyerSummery[$row[csf('buyer_buyer')]]+=$row[csf('order_quantity')];
			$monthSummery_arr[$monthNYear]['no']+=$row[csf('order_quantity')];
			$subconDtlsArr[$row[csf('job_dtls_id')]]['data']=$row[csf('receive_date')].'__'.$row[csf('party_id')].'__'.$row[csf('subcon_job')].'__'.$row[csf('aop_order_type')].'__'.$row[csf('buyer_buyer')].'__'.$row[csf('aop_reference')].'__'.$row[csf('order_no')].'__'.$row[csf('buyer_style_ref')].'__'.$row[csf('currency_id')].'__'.$row[csf('aop_color_id')].'__'.$row[csf('construction')].'__'.$row[csf('gsm')].'__'.$row[csf('fin_dia')].'__'.$row[csf('order_quantity')].'__'.$row[csf('remarks')];
		}
	}
	asort($monthSummery_arr);
	unset($job_sqlResult);
	
	$jobdtlsId_cond="";
	
	$subcondtlsids=implode(",",array_filter(array_unique(explode(",",$subcondtlsid))));
	$subcondtls_ids=count(explode(",",$subcondtlsids));
	if($db_type==2 && $subcondtls_ids>1000)
	{
		$jobdtlsId_cond=" and (";
		$subcondtlsidsArr=array_chunk(explode(",",$subcondtlsids),999);
		foreach($subcondtlsidsArr as $ids)
		{
			$ids=implode(",",$ids);
			$jobdtlsId_cond.=" b.order_id in($ids) or"; 
		}
		$jobdtlsId_cond=chop($jobdtlsId_cond,'or ');
		$jobdtlsId_cond.=")";
	}
	else $jobdtlsId_cond=" and b.order_id in( $subcondtlsids)";
	
	$delSql="select a.within_group, b.order_id, b.product_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.entry_form=307 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $jobdtlsId_cond $deliveryDate_cond";
	//echo $delSql;
	$delSqlRes=sql_select($delSql);
	$deliveryDtls_arr=array(); 
	$deliveryInhouseSumm_arr=array();
	$deliverySubconSumm_arr=array();
	foreach($delSqlRes as $row)
	{
		$monthyr=$buyer_buyer=0;
		$monthyr=$orderDtls_arr[$row[csf('order_id')]]['month'];
		//$buyer_buyer=$orderDtls_arr[$row[csf('order_id')]]['buyer_buyer'];
		$buyer_buyer=$orderDtls_arr[$row[csf('order_id')]]['buyer'];
		$deliveryDtls_arr[$row[csf('order_id')]]+=$row[csf('product_qnty')];
		$deliveryInhouseSumm_arr[$monthyr]+=$row[csf('product_qnty')];
		if($row[csf('within_group')]==1)
		{
			$deliverySubconSumm_arr[$buyer_buyer]+=$row[csf('product_qnty')];
		}
		
	}
	//echo $row[csf('order_id')];
	//echo "<pre>"; 
	//print_r($orderDtls_arr); die;
	
	unset($delSqlRes);
	
	$colorArr=return_library_array( "select id,color_name from lib_color", "id", "color_name");	
	ob_start();
	?>
    <div>
    	<table width="1680" cellspacing="0" cellpadding="0" border="0" rules="all" >
            <tr class="form_caption">
                <td colspan="19" align="center" style="border:none;font-size:16px; font-weight:bold"><?=$report_title; ?></td>
            </tr>
            <tr class="form_caption">
                <td colspan="19" align="center"><?=$companyArr[str_replace("'","",$cbo_company_id)]; ?><br>
                	</b><? echo ($start_date == '0000-00-00' || $start_date == '' ? '' : change_date_format($start_date)).' To '; echo ($end_date == '0000-00-00' || $end_date == '' ? '' : change_date_format($end_date)); ?> </b>
                </td>
            </tr>
        </table>
        <table width="960">
            <tr>
                <td valign="top" width="500">
                    <table border="1" class="rpt_table" rules="all" width="500" cellpadding="0" cellspacing="0">
                        <thead>
                        	<tr>
                            	<th colspan="5">Order Qty. Month Summery</th>
                            </tr>
                            <tr>
                                <th width="110">Delivery Month</th>
                                <th width="100">In-House</th>
                                <th width="90">Subcontract</th>
                                <th width="100">Total Ord Qty</th>
                                <th>Total Del Qty</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:500px; max-height:180px; overflow-y:scroll" id="scroll_body1">
                 		<table border="1" class="rpt_table" rules="all" width="480" cellpadding="0" cellspacing="0" id="tblmonthsumm">
							<?
                            $m=1;
                            foreach($monthSummery_arr as $yearMonth=>$mdata)
                            {
                                if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rowOrdQty=0; $deliQty=0;
                                $rowOrdQty=$mdata['yes']+$mdata['no'];
								$deliQty=$deliveryInhouseSumm_arr[$yearMonth];
                                ?>
                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('trm_<?=$m; ?>','<?=$bgcolor;?>')" id="trm_<?=$m;?>">
                                    <td width="110" style="word-break:break-all"><?=date("M-Y",strtotime($yearMonth)); ?></td>
                                    <td width="100" align="right"><?=number_format($mdata['yes'],2); ?></td>
                                    <td width="90" align="right"><?=number_format($mdata['no'],2); ?></td>
                                    <td width="100" align="right"><?=number_format($rowOrdQty,2); ?></td>
                                    <td align="right"><?=number_format($deliQty,2); ?></td>
                                </tr>
                                <?
                                $m++;
								$suminhouse+=$mdata['yes'];
								$sumsubcon+=$mdata['no'];
								$sumordqty+=$rowOrdQty;
								$sumdeliqty+=$deliQty;
                            }
                            ?>
                            <tfoot>
                                <th align="right">Total:</th>
                                <th align="right"><?=number_format($suminhouse,2); ?></th>
                                <th align="right"><?=number_format($sumsubcon,2); ?></th>
                                <th align="right"><?=number_format($sumordqty,2); ?></th>
                                <th align="right"><?=number_format($sumdeliqty,2); ?></th>
                            </tfoot>
                        </table>
                    </div>
                </td>
                <td width="30">&nbsp;</td>
                <td valign="top" width="430">
                	<table border="1" class="rpt_table" rules="all" width="430" cellpadding="0" cellspacing="0">
                        <thead>
                        	<tr>
                                <th colspan="4">Order Qty. Buyer Summery (Inhouse)</th>
                            </tr>
                            <tr>
                                <th width="110">Buyer</th>
                                <th width="100">In-House</th>
                                <th width="100">Total Ord Qty</th>
                                <th>Total Del Qty</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:430px; max-height:180px; overflow-y:scroll" id="scroll_body2">
                 		<table border="1" class="rpt_table" rules="all" width="410" cellpadding="0" cellspacing="0" id="tbl_buyersumm">
							<?
                            $b=1;
                            foreach($buyerSummery as $buyerid=>$bqty)
                            {
                                if ($b%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rowbOrdQty=0; $bdeliqty=0;
                                $rowbOrdQty=$bqty;
								$bdeliqty=$deliverySubconSumm_arr[$buyerid];
                                ?>
                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('trb_<?=$b; ?>','<?=$bgcolor;?>')" id="trb_<?=$b;?>">
                                    <td width="110" style="word-break:break-all"><?=$party_arr[$buyerid]; ?></td>
                                    <td width="100" align="right"><?=number_format($bqty,2); ?></td>
                                    <td width="100" align="right"><?=number_format($rowbOrdQty,2); ?></td>
                                    <td align="right"><?=number_format($bdeliqty,2); ?></td>
                                </tr>
                                <?
                                $b++;
								$sumbuyerinhouse+=$bqty;
								$sumbuyerordqty+=$rowbOrdQty;
								$sumbuyerdeliQty+=$bdeliqty;
                            }
                            ?>
                            <tfoot>
                                <th align="right">Total:</th>
                                <th align="right"><?=number_format($sumbuyerinhouse,2); ?></th>
                                <th align="right"><?=number_format($sumbuyerordqty,2); ?></th>
                                <th align="right"><?=number_format($sumbuyerdeliQty,2); ?></th>
                            </tfoot>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        <br />
        <? if(count($inhouseDtlsArr)>0) { ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1780" class="rpt_table">
            <thead>
                <tr>
                	<td colspan="20" align="center" bgcolor="#FFFF55" style="font-size:16px"><b>Inhouse Details (Within Group Yes)</b></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="70">Order Recv Date</th>
                    <th width="110">Party</th>
                    <th width="100">Aop Job NO</th>
                    <th width="80">Order Type</th>
                    <th width="100">Po Buyer</th>
                    <th width="100">AOP Ref. No</th>
                    <th width="100">WO No</th>
                    <th width="100">Po Style No</th>
                    <th width="70">Currency</th>
                    
                    <th width="100">AOP Color</th>
                    <th width="150">Constraction</th>
                    <th width="70">GSM</th>
                    <th width="70">F. Dia</th>
                    <th width="80">Order Qty</th>
                    <th width="80">Total Delivery</th>
                    <th width="80">Del. Balance</th>
                    <th width="80">Delivery  %</th>
                    <th width="80">Balance %</th>
                    <th >Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1780px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1760" class="rpt_table" id="table_body">
            <?
			$a=1;
			foreach($inhouseDtlsArr as $orddtlsid=>$inhousdata)
			{
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$receive_date=$party_id=$subcon_job=$aop_order_type=$buyer_buyer=$aop_reference=$order_no=$buyer_style_ref=$currency_id=$aop_color_id=$construction=$gsm=$fin_dia=$order_qty=$deliveryQty=$deliBal=$deliPer=$balancePer='';
				$exinhouse=explode("__",$inhousdata['data']);
				
				$receive_date=$exinhouse[0];
				$party_id=$exinhouse[1];
				$subcon_job=$exinhouse[2];
				$aop_order_type=$exinhouse[3];
				$buyer_buyer=$exinhouse[4];
				$aop_reference=$exinhouse[5];
				$order_no=$exinhouse[6];
				$buyer_style_ref=$exinhouse[7];
				$currency_id=$exinhouse[8];
				$aop_color_id=$exinhouse[9];
				$construction=$exinhouse[10];
				$gsm=$exinhouse[11];
				$fin_dia=$exinhouse[12];
				$order_qty=$exinhouse[13];
				$remarks=$exinhouse[14];
				
				$deliveryQty=$deliveryDtls_arr[$orddtlsid];
				$deliBal=$order_qty-$deliveryQty;
				$deliPer=($deliveryQty/$order_qty)*100;
				$balancePer=100-$deliPer;
				
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tri_<?=$a; ?>','<?=$bgcolor; ?>')" id="tri_<?=$a; ?>"> 
                	<td width="30" align="center"><?=$a; ?></td>
                    <td width="70" style="word-break:break-all"><?=change_date_format($receive_date); ?></td>
                    <td width="110" style="word-break:break-all"><?=$companyArr[$party_id]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$subcon_job; ?></td>
                    <td width="80" style="word-break:break-all"><?=$aop_orde_type[$aop_order_type]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$party_arr[$buyer_buyer]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$aop_reference; ?></td>
                    <td width="100" style="word-break:break-all"><?=$order_no; ?></td>
                    <td width="100" style="word-break:break-all"><?=$buyer_style_ref; ?></td>
                    <td width="70" style="word-break:break-all"><?=$currency[$currency_id]; ?></td>
                    
                    <td width="100" style="word-break:break-all"><?=$colorArr[$aop_color_id]; ?></td>
                    <td width="150" style="word-break:break-all"><?=$construction; ?></td>
                    <td width="70" style="word-break:break-all"><?=$gsm; ?></td>
                    <td width="70" style="word-break:break-all"><?=$fin_dia; ?></td>
                    <td width="80" align="right"><?=number_format($order_qty, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($deliveryQty, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($deliBal, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($deliPer, 4,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($balancePer, 4,'.',''); ?></td>
                    <td align="center"><?=$remarks; ?></td>
                </tr>
                <?
				$a++;
				$inordQtyTotal+=$order_qty;
				$indeliQtyTotal+=$deliveryQty;
				$indelBalQtyTotal+=$deliBal;
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1780" class="tbl_bottom">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                
                <td width="100">&nbsp;</td>
                <td width="150">Inhouse Total:</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80" id="value_tdinqty"><?=number_format($inordQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdindeliqty"><?=number_format($indeliQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($indelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <? } if(count($subconDtlsArr)>0) {?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1780" class="rpt_table">
            <thead>
                <tr>
                	<td colspan="20" align="center" bgcolor="#FFFF55" style="font-size:16px"><b>Sub-contract (Within Group No)</b></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="70">Order Recv Date</th>
                    <th width="110">Party</th>
                    <th width="100">Aop Job NO</th>
                    <th width="80">Order Type</th>
                    <th width="100">Po Buyer</th>
                    <th width="100">AOP Ref. No</th>
                    <th width="100">WO No</th>
                    <th width="100">Po Style No</th>
                    <th width="70">Currency</th>
                    
                    <th width="100">AOP Color</th>
                    <th width="150">Constraction</th>
                    <th width="70">GSM</th>
                    <th width="70">F. Dia</th>
                    <th width="80">Order Qty</th>
                    <th width="80">Total Delivery</th>
                    <th width="80">Del. Balance</th>
                    <th width="80">Delivery  %</th>
                    <th width="80">Balance %</th>
                    <th >Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:1780px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1760" class="rpt_table" id="table_body">
            <?
			$a=1;
			foreach($subconDtlsArr as $orddtlsid=>$inhousdata)
			{
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$receive_date=$party_id=$subcon_job=$gmts_type=$buyer_buyer=$aop_reference=$order_no=$buyer_style_ref=$currency_id=$aop_color_id=$construction=$gsm=$fin_dia=$order_qty=$deliveryQty=$deliBal=$deliPer=$balancePer='';
				$exinhouse=explode("__",$inhousdata['data']);
				
				$receive_date=$exinhouse[0];
				$party_id=$exinhouse[1];
				$subcon_job=$exinhouse[2];
				$gmts_type=$exinhouse[3];
				$buyer_buyer=$exinhouse[4];
				$aop_reference=$exinhouse[5];
				$order_no=$exinhouse[6];
				$buyer_style_ref=$exinhouse[7];
				$currency_id=$exinhouse[8];
				$aop_color_id=$exinhouse[9];
				$construction=$exinhouse[10];
				$gsm=$exinhouse[11];
				$fin_dia=$exinhouse[12];
				$order_qty=$exinhouse[13];
				$remarks=$exinhouse[14];
				
				$deliveryQty=$deliveryDtls_arr[$orddtlsid];
				$deliBal=$order_qty-$deliveryQty;
				$deliPer=($deliveryQty/$order_qty)*100;
				$balancePer=100-$deliPer;
				
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tri_<?=$a; ?>','<?=$bgcolor; ?>')" id="tri_<?=$a; ?>"> 
                	<td width="30" align="center"><?=$a; ?></td>
                    <td width="70" style="word-break:break-all"><?=change_date_format($receive_date); ?></td>
                    <td width="110" style="word-break:break-all"><?=$party_arr[$party_id]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$subcon_job; ?></td>
                    <td width="80" style="word-break:break-all"><?=$aop_orde_type[$gmts_type]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$party_arr[$buyer_buyer]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$aop_reference; ?></td>
                    <td width="100" style="word-break:break-all"><?=$order_no; ?></td>
                    <td width="100" style="word-break:break-all"><?=$buyer_style_ref; ?></td>
                    <td width="70" style="word-break:break-all"><?=$currency[$currency_id]; ?></td>
                    
                    <td width="100" style="word-break:break-all"><?=$colorArr[$aop_color_id]; ?></td>
                    <td width="150" style="word-break:break-all"><?=$construction; ?></td>
                    <td width="70" style="word-break:break-all"><?=$gsm; ?></td>
                    <td width="70" style="word-break:break-all"><?=$fin_dia; ?></td>
                    <td width="80" align="right"><?=number_format($order_qty, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($deliveryQty, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($deliBal, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($deliPer, 4,'.',''); ?></td>
                    <td align="right"><?=number_format($balancePer, 4,'.',''); ?></td>
                    <td align="center"><?=$remarks; ?></td>
                </tr>
                <?
				$a++;
				$inordQtyTotal+=$order_qty;
				$indeliQtyTotal+=$deliveryQty;
				$indelBalQtyTotal+=$deliBal;
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1780" class="tbl_bottom">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                
                <td width="100">&nbsp;</td>
                <td width="150">Sub-Contract Total:</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80" id="value_tdinqty"><?=number_format($inordQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdindeliqty"><?=number_format($indeliQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($indelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <? } ?>
	</div>
	<?	
	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
        //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');    
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****$cbo_within_group"; 
    exit();
}

if($action=="product_qty_pop_up")
{
    echo load_html_head_contents("Production Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="7">Production Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Sys ID</th>
                        <th width="70">Prod. Date</th>
                        <th width="110">Order No</th>
                        <th width="70">Batch</th>
                        <th width="300">Item Description</th>
                        <th>Qty</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql= "select a.product_no,a.prefix_no_num,a.product_date,b.batch_id, b.fabric_description,b.product_qnty,b.order_id,c.batch_no from  subcon_production_mst a, subcon_production_dtls b ,pro_batch_create_mst c where a.id=b.mst_id and a.entry_form=291 and b.batch_id=c.id and b.order_id='$expData[0]' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("product_date")]);?> </td> 
                    <td align="center" width="110"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="70"><? echo $row[csf("batch_no")]; ?></td>
                    <td width="300"><? echo $row[csf("fabric_description")]; ?> </td> 
                    <td align="right"><? echo number_format($row[csf("product_qnty")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("product_qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}

if($action=="qc_qty_pop_up")
{
    echo load_html_head_contents("QC Details", "../../../", 1, 1,$unicode,'','');
    //echo $order_id;//die;
    $expData=explode('_',$order_id);
    ?>
    <fieldset style="width:820px">
        <div style="width:100%;" align="center">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th colspan="7">QC Details</th>
                    </tr>
                    <tr>
                        <th width="30">SL</th>
                        <th width="60">Sys ID</th>
                        <th width="70">QC Date</th>
                        <th width="110">Order No</th>
                        <th width="70">Batch</th>
                        <th width="300">Item Description</th>
                        <th>Qty</th>
                    </tr>
                </thead>
            </table>
        </div>  
        <div style="width:100%; max-height:230px; overflow-y:scroll" align="left">
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1" >
                <?
                $supplier_array=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
                $po_arr=return_library_array( "select id, order_no from subcon_ord_dtls",'id','order_no');
                $color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
                $i=0;
                $sql= "select a.product_no,a.prefix_no_num,a.product_date,b.batch_id, b.fabric_description,b.product_qnty,b.order_id,c.batch_no from  subcon_production_mst a, subcon_production_dtls b ,pro_batch_create_mst c where a.id=b.mst_id and a.entry_form=294 and b.batch_id=c.id and b.order_id='$expData[0]' and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
                //echo $sql;
                $material_sql= sql_select($sql);
                foreach( $material_sql as $row )
                {
                    $i++;
                    if ($i%2==0)  $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
                    ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><? echo $row[csf("prefix_no_num")];?> </td>
                    <td width="70"><? echo change_date_format($row[csf("product_date")]);?> </td> 
                    <td align="center" width="110"><? echo $po_arr[$row[csf("order_id")]]; ?></td>
                    <td align="center" width="70"><? echo $row[csf("batch_no")]; ?></td>
                    <td width="300"><? echo $row[csf("fabric_description")]; ?> </td> 
                    <td align="right"><? echo number_format($row[csf("product_qnty")],2); ?></td>
                </tr>
                <? 
                $tot_qty+=$row[csf("product_qnty")];
                } ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right">Total: </td>
                    <td align="right"><p><? echo number_format($tot_qty,2); ?></p></td>
                </tr>
            </table>
        </div> 
    </fieldset>
 </div> 
    <?
    exit();
}
?>
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
        echo create_drop_down( "txt_buyer_buyer_no", 120, "select id, buyer_name from lib_buyer where status_active=1","id,buyer_name", 1, "-- Select Buyer --", "", "");
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
	
    $sql="select a.party_id, a.subcon_job, a.job_no_prefix_num,  b.order_no, b.cust_style_ref from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.entry_form=278  and a.company_id='$company_id' $buyer_cond group by a.party_id, a.subcon_job, a.job_no_prefix_num,  b.order_no, b.cust_style_ref  order by a.subcon_job ASC";	
	
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
	
	 $sql="select distinct a.id, b.job_no_mst as job_no ,a.party_id as buyer_name, b.order_no as po_number, a.job_no_prefix_num as job_prefix, $year_field from subcon_ord_mst a, subcon_ord_dtls b where b.job_no_mst=a.subcon_job and a.id=b.mst_id and a.company_id=$company_id $sub_buyer_name_cond $job_no_cond and a.entry_form=278  and a.is_deleted =0 group by a.id, a.party_id, b.job_no_mst, b.order_no, a.job_no_prefix_num, a.company_id, a.insert_date";	
	
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
	
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_party_name=str_replace("'","",$cbo_party_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_order_id=str_replace("'","",$txt_order_id);
	$txt_reference_no=str_replace("'","",$txt_reference_no);
	$txt_buyer_buyer_no=str_replace("'","",$txt_buyer_buyer_no);
	$txt_buyer_po=str_replace("'","",$txt_buyer_po);
	$txt_buyer_style=str_replace("'","",$txt_buyer_style);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$type=str_replace("'","",$type);
	//echo $type; die;
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	if($cbo_within_group!=0) $within_group_cond=" and a.within_group='$cbo_within_group'"; else $within_group_cond="";
	if($cbo_party_name!=0) $party_cond=" and a.party_id='$cbo_party_name'"; else $buyer_id_cond="";
	if($txt_job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($txt_job_no) ";
	if($txt_order_no!='') $order_no_cond=" and b.order_no like '%$txt_order_no%'"; else $order_no_cond="";
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
		
		$orderRec_cond=" and c.subcon_date between '$start_date' and '$end_date'";
		
	}//Company / Suppiler	Job NO	Buyer	AOP Ref. No	AOP Order No / WO No	Style No	currency	AOP Color	Contraction	GSM	Dia
	
		$job_sql = "SELECT  a.subcon_job, a.within_group, a.party_id,a.aop_reference,a.gmts_type,a.currency_id,b.order_no,b.buyer_po_id, b.buyer_style_ref, b.buyer_buyer, b.aop_color_id, b.construction, b.gsm, b.fin_dia, b.order_quantity, c.subcon_date,
		sum(case when c.subcon_date<'".$start_date."' and c.trans_type=1 and c.entry_form =279 then d.quantity else 0 end) as opening_receive_quantity,
		sum(case when c.trans_type=1 and c.entry_form =279 and c.subcon_date  between '".$start_date."' and '".$end_date."' then d.quantity else 0 end) as total_receive_quantity,
		sum(case when c.subcon_date<'".$start_date."' and c.trans_type=2 and c.entry_form =280 then d.quantity else 0 end) as opening_issue_quantity,
		sum(case when c.trans_type=2 and c.entry_form =280 and c.subcon_date  between '".$start_date."' and '".$end_date."' then d.quantity else 0 end) as total_issue_quantity
		from subcon_ord_mst a, subcon_ord_dtls b,sub_material_mst c ,sub_material_dtls d 
		where a.subcon_job=b.job_no_mst and c.embl_job_no=b.job_no_mst and c.id=d.mst_id and d.job_dtls_id=b.id   and a.company_id=$cbo_company_id and a.status_active=1 
		and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in (278) and c.trans_type in (1,2) and c.entry_form in (279,280) $within_group_cond $party_cond $job_no_cond $order_no_cond $reference_no_cond $buyer_buyer_cond $style_ref_cond $buyer_style_cond $buyer_po_cond $orderRec_cond
		group by a.subcon_job, a.within_group, a.party_id,a.aop_reference,a.gmts_type,a.currency_id,b.order_no,b.buyer_po_id, b.buyer_style_ref, b.buyer_buyer, b.aop_color_id, b.construction, b.gsm, b.fin_dia, b.order_quantity, c.subcon_date";
	//echo $job_sql; die;
	$job_sqlResult=sql_select($job_sql);	
	$monthSummery_arr=array(); 
	$buyerSummery=array(); 
	$inhouseDtlsArr=array(); 
	$subconDtlsArr=array(); 
	$issuebuyerSummery=array();
	$order_monthSummery_arr=array();
	$order_buyerSummery=array();
	foreach($job_sqlResult as $row)
	{
			$monthNYear=date("Y-m",strtotime($row[csf("subcon_date")]));
			if($row[csf('within_group')]==1)
			{
				
				//$buyerSummery[$row[csf('buyer_buyer')]]['order_quantity']+=$row[csf('order_quantity')];
				$order_monthSummery_arr[$monthNYear]['yes']+=$row[csf('order_quantity')];
				$order_buyerSummery[$row[csf('buyer_buyer')]]+=$row[csf('order_quantity')];
				$buyerSummery[$row[csf('buyer_buyer')]]+=$row[csf('total_receive_quantity')];
				$issuebuyerSummery[$row[csf('buyer_buyer')]]+=$row[csf('total_issue_quantity')];
				$monthSummery_arr[$monthNYear]['yes']+=$row[csf('total_receive_quantity')];
				$issuemonthSummery_arr[$monthNYear]['yes']+=$row[csf('total_issue_quantity')];
			}
			if($row[csf('within_group')]==2)
			{
				$order_monthSummery_arr[$monthNYear]['no']+=$row[csf('order_quantity')];
				$monthSummery_arr[$monthNYear]['no']+=$row[csf('total_receive_quantity')];
				$issuemonthSummery_arr[$monthNYear]['yes']+=$row[csf('total_issue_quantity')];
			}
		
		
		if($row[csf('within_group')]==1)
		{
			
			
			$inhouseDtlsArr[]['data']=$row[csf('party_id')].'__'.$row[csf('party_id')].'__'.$row[csf('subcon_job')].'__'.$row[csf('gmts_type')].'__'.$row[csf('buyer_buyer')].'__'.$row[csf('aop_reference')].'__'.$row[csf('order_no')].'__'.$row[csf('buyer_style_ref')].'__'.$row[csf('currency_id')].'__'.$row[csf('aop_color_id')].'__'.$row[csf('construction')].'__'.$row[csf('gsm')].'__'.$row[csf('fin_dia')].'__'.$row[csf('order_quantity')].'__'.$row[csf('opening_receive_quantity')].'__'.$row[csf('total_receive_quantity')].'__'.$row[csf('opening_issue_quantity')].'__'.$row[csf('total_issue_quantity')];
		}
		if($row[csf('within_group')]==2)
		{
			$subconDtlsArr[]['data']=$row[csf('party_id')].'__'.$row[csf('party_id')].'__'.$row[csf('subcon_job')].'__'.$row[csf('gmts_type')].'__'.$row[csf('buyer_buyer')].'__'.$row[csf('aop_reference')].'__'.$row[csf('order_no')].'__'.$row[csf('buyer_style_ref')].'__'.$row[csf('currency_id')].'__'.$row[csf('aop_color_id')].'__'.$row[csf('construction')].'__'.$row[csf('gsm')].'__'.$row[csf('fin_dia')].'__'.$row[csf('order_quantity')].'__'.$row[csf('opening_receive_quantity')].'__'.$row[csf('total_receive_quantity')].'__'.$row[csf('opening_issue_quantity')].'__'.$row[csf('total_issue_quantity')];
		}
	}
	
	asort($monthSummery_arr);
	unset($job_sqlResult);
	
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
                            	<th colspan="5">Month Summery</th>
                            </tr>
                            <tr>
                                <th width="110">Month</th>
                                <th width="100">In-House</th>
                                <th width="90">Subcontract</th>
                                <th width="100">Total Rcv Qty</th>
                                <th>Total Issue Qty</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:500px; max-height:180px; overflow-y:scroll" id="scroll_body1">
                 		<table border="1" class="rpt_table" rules="all" width="480" cellpadding="0" cellspacing="0" id="tblmonthsumm">
							<?
                            $m=1;
							
							//echo "<pre>";
							//print_r($monthSummery_arr);
							
                            foreach($monthSummery_arr as $yearMonth=>$mdata)
                            {
								
								//echo "<pre>";
								//print_r($yearMonth);
                                if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                $rowOrdQty=0;$rowissueQty=0;
                                $rowOrdQty=$mdata['yes']+$mdata['no'];
								$rowissueQty=$issuemonthSummery_arr[$yearMonth]['yes']+$issuemonthSummery_arr[$yearMonth]['no'];
                                ?>
                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('trm_<?=$m; ?>','<?=$bgcolor;?>')" id="trm_<?=$m;?>">
                                    <td width="110" style="word-break:break-all"><?=date("M-Y",strtotime($yearMonth)); ?></td>
                                    <td width="100" align="right"><?=number_format($order_monthSummery_arr[$yearMonth]['yes'],2); ?></td>
                                    <td width="90" align="right"><?=number_format($order_monthSummery_arr[$yearMonth]['no'],2); ?></td>
                                    <td width="100" align="right"><?=number_format($rowOrdQty,2); ?></td>
                                    <td align="right"><?=number_format($rowissueQty,2); ?></td>
                                </tr>
                                <?
                                $m++;
								$suminhouse+=$order_monthSummery_arr[$yearMonth]['yes'];
								$sumsubcon+=$order_monthSummery_arr[$yearMonth]['no'];
								$sumordqty+=$rowOrdQty;
								$sumdeliqty+=$rowissueQty;
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
                                <th colspan="4">Buyer Summery (Inhouse)</th>
                            </tr>
                            <tr>
                                <th width="110">Buyer</th>
                                <th width="100">In-House</th>
                                <th width="100">Total Rcv</th>
                                <th>Total Issue</th>
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
                                $rowbOrdQty=0; $bissueqty=0;
                                $rowbOrdQty=$bqty;
								$bissueqty=$issuebuyerSummery[$buyerid];
								
								
                                ?>
                                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('trb_<?=$b; ?>','<?=$bgcolor;?>')" id="trb_<?=$b;?>">
                                    <td width="110" style="word-break:break-all"><?=$party_arr[$buyerid]; ?></td>
                                    <td width="100" align="right"><?=number_format($order_buyerSummery[$buyerid],2); ?></td>
                                    <td width="100" align="right"><?=number_format($rowbOrdQty,2); ?></td>
                                    <td align="right"><?=number_format($bissueqty,2); ?></td>
                                </tr>
                                <?
                                $b++;
								$sumbuyerinhouse+=$order_buyerSummery[$buyerid];
								$sumbuyerordqty+=$rowbOrdQty;
								$sumbuyerdeliQty+=$bissueqty;
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
        <?
		if($type==1)
		{
		 if(count($inhouseDtlsArr)>0) { ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table">
            <thead>
                <tr>
                	<td colspan="19" align="center" bgcolor="#a0b7ce" style="font-size:16px"><b>Inhouse Details (Within Group Yes)</b></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Party</th>
                    <th width="100">Aop Job NO</th>
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
                    <th width="80">M / C Type</th>
                    <th width="80">Pre Receive</th>
                    <th width="80">Today Receive</th>
                    <th width="80">Total Receive</th> 
                    <th width="80">Receive %</th>
                    <th>Balance</th>
                </tr>
            </thead>
        </table>
        <div style="width:1680px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1660" class="rpt_table" id="table_body">
            <?
			$a=1;
			foreach($inhouseDtlsArr as $orddtlsid=>$inhousdata)
			{
				
				//echo "<pre>";
				//print_r($inhousdata);
				
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
				$opening_receive_quantity=$exinhouse[14];
				$total_receive_quantity=$exinhouse[15];
				$all_total_receive_quantity=$exinhouse[14]+$exinhouse[15];
				$receiveBal=$order_qty-$all_total_receive_quantity;
				$receivePer=($all_total_receive_quantity/$order_qty)*100;
				//$balancePer=100-$deliPer;
				
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tri_<?=$a; ?>','<?=$bgcolor; ?>')" id="tri_<?=$a; ?>"> 
                	<td width="30" align="center"><?=$a; ?></td>
                    <td width="110" style="word-break:break-all"><?=$companyArr[$party_id]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$subcon_job; ?></td>
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
                    <td width="80" align="right"><? ?></td>
                    <td width="80" align="right"><?=number_format($opening_receive_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($total_receive_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($all_total_receive_quantity, 4,'.',''); ?></td> 
                    <td width="80" align="right"><?=number_format($receivePer, 4,'.',''); ?></td>
                    <td align="right"><?=number_format($receiveBal, 4,'.',''); ?></td>
                </tr>
                <?
				$a++;
				$inordQtyTotal+=$order_qty;
				$indeliQtyTotal+=$opening_receive_quantity;
				$indelBalQtyTotal+=$total_receive_quantity;
				$allindelBalQtyTotal+=$all_total_receive_quantity;
				$receiveBalTotal+=$receiveBal;
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="tbl_bottom">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
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
                <td width="80">&nbsp;</td>
                <td width="80" id="value_tdindeliqty"><?=number_format($indeliQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($indelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdallinbalqty"><?=number_format($allindelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80"></td>
                <td id="value_receiveBalTotal"><?=number_format($receiveBalTotal, 2,'.',''); ?></td>
            </tr>
        </table>
        
        <? } 
		 if(count($subconDtlsArr)>0) { ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table">
            <thead>
                <tr>
                	<td colspan="19" align="center" bgcolor="#a0b7ce" style="font-size:16px"><b>Sub-contract (Within Group No)</b></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Party</th>
                    <th width="100">Aop Job NO</th>
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
                    <th width="80">M / C Type</th>
                    <th width="80">Pre Receive</th>
                    <th width="80">Today Receive</th>
                    <th width="80">Total Receive</th> 
                    <th width="80">Receive %</th>
                    <th>Balance</th>
                </tr>
            </thead>
        </table>
        <div style="width:1680px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1660" class="rpt_table" id="table_body">
            <?
			$a=1;
			$inordQtyTotal=0; $indeliQtyTotal=0; $indelBalQtyTotal=0; $allindelBalQtyTotal=0; $receiveBalTotal=0;
			foreach($subconDtlsArr as $orddtlsid=>$inhousdata)
			{
				
				//echo "<pre>";
				//print_r($inhousdata);
				
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
				$opening_receive_quantity=$exinhouse[14];
				$total_receive_quantity=$exinhouse[15];
				$all_total_receive_quantity=$exinhouse[14]+$exinhouse[15];
				$receiveBal=$order_qty-$all_total_receive_quantity;
				$receivePer=($all_total_receive_quantity/$order_qty)*100;
				$balancePer=100-$deliPer;
				
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tri_<?=$a; ?>','<?=$bgcolor; ?>')" id="tri_<?=$a; ?>"> 
                	<td width="30" align="center"><?=$a; ?></td>
                    <td width="110" style="word-break:break-all"><?=$companyArr[$party_id]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$subcon_job; ?></td>
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
                    <td width="80" align="right"><? ?></td>
                    <td width="80" align="right"><?=number_format($opening_receive_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($total_receive_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($all_total_receive_quantity, 4,'.',''); ?></td> 
                    <td width="80" align="right"><?=number_format($receivePer, 4,'.',''); ?></td>
                    <td align="right"><?=number_format($receiveBal, 4,'.',''); ?></td>
                </tr>
                <?
				$a++;
				$inordQtyTotal+=$order_qty;
				$indeliQtyTotal+=$opening_receive_quantity;
				$indelBalQtyTotal+=$total_receive_quantity;
				$allindelBalQtyTotal+=$all_total_receive_quantity;
				$receiveBalTotal+=$receiveBal;
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="tbl_bottom">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
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
                <td width="80">&nbsp;</td>
                <td width="80" id="value_tdindeliqty"><?=number_format($indeliQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($indelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdallinbalqty"><?=number_format($allindelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80"></td>
                <td id="value_receiveBalTotal"><?=number_format($receiveBalTotal, 2,'.',''); ?></td>
            </tr>
        </table>
        
        <? } 
		}
		
		if($type==2)
		{
		 if(count($inhouseDtlsArr)>0) { ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table">
            <thead>
                <tr>
                	<td colspan="19" align="center" bgcolor="#a0b7ce" style="font-size:16px"><b>Inhouse Details (Within Group Yes)</b></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Party</th>
                    <th width="100">Aop Job NO</th>
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
                    <th width="80">M / C Type</th>
                    <th width="80">Pre Issue</th>
                    <th width="80">Today Issue</th>
                    <th width="80">Total Issue</th> 
                    <th width="80">Issue %</th>
                    <th>Balance</th>
                </tr>
            </thead>
        </table>
        <div style="width:1680px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1660" class="rpt_table" id="table_body">
            <?
			$a=1;
			foreach($inhouseDtlsArr as $orddtlsid=>$inhousdata)
			{
				
				//echo "<pre>";
				//print_r($inhousdata);
				
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
				$opening_receive_quantity=$exinhouse[14];
				$total_receive_quantity=$exinhouse[15];
				$opening_issue_quantity=$exinhouse[16];
				$total_issue_quantity=$exinhouse[17];
				
				
				$all_total_issue_quantity=$exinhouse[16]+$exinhouse[17];
				$issueBal=$order_qty-$all_total_issue_quantity;
				$issuePer=($all_total_issue_quantity/$order_qty)*100;
				$balancePer=100-$deliPer;
				
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tri_<?=$a; ?>','<?=$bgcolor; ?>')" id="tri_<?=$a; ?>"> 
                	<td width="30" align="center"><?=$a; ?></td>
                    <td width="110" style="word-break:break-all"><?=$companyArr[$party_id]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$subcon_job; ?></td>
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
                    <td width="80" align="right"><? ?></td>
                    <td width="80" align="right"><?=number_format($opening_issue_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($total_issue_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($all_total_issue_quantity, 4,'.',''); ?></td> 
                    <td width="80" align="right"><?=number_format($issuePer, 4,'.',''); ?></td>
                    <td align="right"><?=number_format($issueBal, 4,'.',''); ?></td>
                </tr>
                <?
				$a++;
				$inordQtyTotal+=$order_qty;
				$indeliQtyTotal+=$opening_issue_quantity;
				$indelBalQtyTotal+=$total_issue_quantity;
				$allindelBalQtyTotal+=$all_total_issue_quantity;
				$receiveBalTotal+=$issueBal;
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="tbl_bottom">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
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
                <td width="80">&nbsp;</td>
                <td width="80" id="value_tdindeliqty"><?=number_format($indeliQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($indelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdallinbalqty"><?=number_format($allindelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80"></td>
                <td id="value_receiveBalTotal"><?=number_format($receiveBalTotal, 2,'.',''); ?></td>
            </tr>
        </table>
        
        <? }
		 if(count($subconDtlsArr)>0) { ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table">
            <thead>
                <tr>
                	<td colspan="19" align="center" bgcolor="#a0b7ce" style="font-size:16px"><b>Sub-contract (Within Group No)</b></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Party</th>
                    <th width="100">Aop Job NO</th>
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
                    <th width="80">M / C Type</th>
                    <th width="80">Pre Issue</th>
                    <th width="80">Today Issue</th>
                    <th width="80">Total Issue</th> 
                    <th width="80">Issue %</th>
                    <th>Balance</th>
                </tr>
            </thead>
        </table>
        <div style="width:1680px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1660" class="rpt_table" id="table_body">
            <?
			$a=1;
			$inordQtyTotal=$indeliQtyTotal=$indelBalQtyTotal=$allindelBalQtyTotal=$receiveBalTotal=0;
			foreach($subconDtlsArr as $orddtlsid=>$inhousdata)
			{
				
				//echo "<pre>";
				//print_r($inhousdata);
				
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
				$opening_receive_quantity=$exinhouse[14];
				$total_receive_quantity=$exinhouse[15];
				$opening_issue_quantity=$exinhouse[16];
				$total_issue_quantity=$exinhouse[17];
				
				
				$all_total_issue_quantity=$exinhouse[16]+$exinhouse[17];
				$issueBal=$order_qty-$all_total_issue_quantity;
				$issuePer=($all_total_issue_quantity/$order_qty)*100;
				$balancePer=100-$deliPer;
				
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tri_<?=$a; ?>','<?=$bgcolor; ?>')" id="tri_<?=$a; ?>"> 
                	<td width="30" align="center"><?=$a; ?></td>
                    <td width="110" style="word-break:break-all"><?=$companyArr[$party_id]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$subcon_job; ?></td>
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
                    <td width="80" align="right"><? ?></td>
                    <td width="80" align="right"><?=number_format($opening_issue_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($total_issue_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($all_total_issue_quantity, 4,'.',''); ?></td> 
                    <td width="80" align="right"><?=number_format($issuePer, 4,'.',''); ?></td>
                    <td align="right"><?=number_format($issueBal, 4,'.',''); ?></td>
                </tr>
                <?
				$a++;
				$inordQtyTotal+=$order_qty;
				$indeliQtyTotal+=$opening_issue_quantity;
				$indelBalQtyTotal+=$total_issue_quantity;
				$allindelBalQtyTotal+=$all_total_issue_quantity;
				$receiveBalTotal+=$issueBal;
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="tbl_bottom">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
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
                <td width="80">&nbsp;</td>
                <td width="80" id="value_tdindeliqty"><?=number_format($indeliQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($indelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdallinbalqty"><?=number_format($allindelBalQtyTotal, 2,'.',''); ?></td>
                <td width="80"></td>
                <td id="value_receiveBalTotal"><?=number_format($receiveBalTotal, 2,'.',''); ?></td>
            </tr>
        </table>
        
        <? }
		}
		
		if($type==3)
		{
		if(count($inhouseDtlsArr)>0) { ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2180" class="rpt_table">
            <thead>
                <tr>
                	<td colspan="23" align="center" bgcolor="#a0b7ce" style="font-size:16px"><b>Inhouse Details (Within Group Yes)</b></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Party</th>
                    <th width="100">Aop Job NO</th>
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
                    <th width="80">M / C Type</th>
                    <th width="80">Pre Receive</th>
                    <th width="80">Today Receive</th>
                    <th width="80">Total Receive</th> 
                    <th width="80">Receive %</th>
                    <th width="80">Pre Issue</th>
                    <th width="80">Today Issue</th>
                    <th width="80">Total Issue</th> 
                    <th width="80">Issue %</th>
                    <th>Balance</th>
                </tr>
            </thead>
        </table>
        <div style="width:2180px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2160" class="rpt_table" id="table_body">
            <?
			$a=1;
			foreach($inhouseDtlsArr as $orddtlsid=>$inhousdata)
			{
				
				//echo "<pre>";
				//print_r($inhousdata);
				
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
				$opening_receive_quantity=$exinhouse[14];
				$total_receive_quantity=$exinhouse[15];
				$opening_issue_quantity=$exinhouse[16];
				$total_issue_quantity=$exinhouse[17];
				
				
				$opening_receive_quantity=$exinhouse[14];
				$total_receive_quantity=$exinhouse[15];
				$all_total_receive_quantity=$exinhouse[14]+$exinhouse[15];
				$receiveBal=$order_qty-$all_total_receive_quantity;
				$receivePer=($all_total_receive_quantity/$order_qty)*100;
				
				
				$all_total_issue_quantity=$exinhouse[16]+$exinhouse[17];
				$issueBal=$order_qty-$all_total_issue_quantity;
				$issuePer=($all_total_issue_quantity/$order_qty)*100;
				$balancePer=100-$deliPer;
				
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tri_<?=$a; ?>','<?=$bgcolor; ?>')" id="tri_<?=$a; ?>"> 
                	<td width="30" align="center"><?=$a; ?></td>
                    <td width="110" style="word-break:break-all"><?=$companyArr[$party_id]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$subcon_job; ?></td>
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
                    <td width="80" align="right"><? ?></td>
                    <td width="80" align="right"><?=number_format($opening_receive_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($total_receive_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($all_total_receive_quantity, 4,'.',''); ?></td> 
                    <td width="80" align="right"><?=number_format($receivePer, 4,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($opening_issue_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($total_issue_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($all_total_issue_quantity, 4,'.',''); ?></td> 
                    <td width="80" align="right"><?=number_format($issuePer, 4,'.',''); ?></td>
                    <td align="right"><?php echo number_format($receiveBal, 4,'.',''); ?></td>
                </tr>
                <?
				$a++;
				$inreceiveQtyTotal+=$opening_receive_quantity;
				$inreceiveBalQtyTotal+=$total_receive_quantity;
				$allinreceiveBalQtyTotal+=$all_total_receive_quantity;
				$receiveBalTotal+=$receiveBal;
				
				
				$inordQtyTotal+=$order_qty;
				$inissueQtyTotal+=$opening_issue_quantity;
				$inissueBalQtyTotal+=$total_issue_quantity;
				$allinissueBalQtyTotal+=$all_total_issue_quantity;
				$issueBalTotal+=$issueBal;
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2180" class="tbl_bottom">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
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
                <td width="80">&nbsp;</td>
                <td width="80" id="value_tdindeliqty"><?=number_format($inreceiveQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($inreceiveBalQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdallinbalqty"><?=number_format($allinreceiveBalQtyTotal, 2,'.',''); ?></td>
                <td width="80"></td>
                <td width="80" id="value_tdindeliqty"><?=number_format($inissueQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($inissueBalQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdallinbalqty"><?=number_format($allinissueBalQtyTotal, 2,'.',''); ?></td>
                <td width="80"></td>
                <td id="value_receiveBalTotal"><?php echo $receiveBalTotal; ?></td>
            </tr>
        </table>
        
        <? unset($inhouseDtlsArr); }
		if(count($subconDtlsArr)>0) { ?>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2180" class="rpt_table">
            <thead>
                <tr>
                	<td colspan="23" align="center" bgcolor="#a0b7ce" style="font-size:16px"><b>Sub-contract (Within Group No)</b></td>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="110">Party</th>
                    <th width="100">Aop Job NO</th>
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
                    <th width="80">M / C Type</th>
                    <th width="80">Pre Receive</th>
                    <th width="80">Today Receive</th>
                    <th width="80">Total Receive</th> 
                    <th width="80">Receive %</th>
                    <th width="80">Pre Issue</th>
                    <th width="80">Today Issue</th>
                    <th width="80">Total Issue</th> 
                    <th width="80">Issue %</th>
                    <th>Balance</th>
                </tr>
            </thead>
        </table>
        <div style="width:2180px; max-height:300px; overflow-y:scroll" id="scroll_body" > 
        	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2160" class="rpt_table" id="table_body">
            <?
			$a=1;
			$inordQtyTotal=0; $inreceiveQtyTotal=0; $inreceiveBalQtyTotal=0; $allinreceiveBalQtyTotal=0; $inissueQtyTotal=0; $inissueBalQtyTotal=0; $allinissueBalQtyTotal=0;
			foreach($subconDtlsArr as $orddtlsid=>$inhousdata)
			{
				
				//echo "<pre>";
				//print_r($inhousdata);
				
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
				$opening_receive_quantity=$exinhouse[14];
				$total_receive_quantity=$exinhouse[15];
				$opening_issue_quantity=$exinhouse[16];
				$total_issue_quantity=$exinhouse[17];
				
				
				$opening_receive_quantity=$exinhouse[14];
				$total_receive_quantity=$exinhouse[15];
				$all_total_receive_quantity=$exinhouse[14]+$exinhouse[15];
				$receiveBal=$order_qty-$all_total_receive_quantity;
				$receivePer=($all_total_receive_quantity/$order_qty)*100;
				
				
				$all_total_issue_quantity=$exinhouse[16]+$exinhouse[17];
				$issueBal=$order_qty-$all_total_issue_quantity;
				$issuePer=($all_total_issue_quantity/$order_qty)*100;
				$balancePer=100-$deliPer;
				
				?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tri_<?=$a; ?>','<?=$bgcolor; ?>')" id="tri_<?=$a; ?>"> 
                	<td width="30" align="center"><?=$a; ?></td>
                    <td width="110" style="word-break:break-all"><?=$companyArr[$party_id]; ?></td>
                    <td width="100" style="word-break:break-all"><?=$subcon_job; ?></td>
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
                    <td width="80" align="right"><? ?></td>
                    <td width="80" align="right"><?=number_format($opening_receive_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($total_receive_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($all_total_receive_quantity, 4,'.',''); ?></td> 
                    <td width="80" align="right"><?=number_format($receivePer, 4,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($opening_issue_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($total_issue_quantity, 2,'.',''); ?></td>
                    <td width="80" align="right"><?=number_format($all_total_issue_quantity, 4,'.',''); ?></td> 
                    <td width="80" align="right"><?=number_format($issuePer, 4,'.',''); ?></td>
                    <td align="right"><? ?></td>
                </tr>
                <?
				$a++;
				$inreceiveQtyTotal+=$opening_receive_quantity;
				$inreceiveBalQtyTotal+=$total_receive_quantity;
				$allinreceiveBalQtyTotal+=$all_total_receive_quantity;
				//$receiveBalTotal+=$receiveBal;
				
				
				$inordQtyTotal+=$order_qty;
				$inissueQtyTotal+=$opening_issue_quantity;
				$inissueBalQtyTotal+=$total_issue_quantity;
				$allinissueBalQtyTotal+=$all_total_issue_quantity;
				$issueBalTotal+=$issueBal;
			}
			?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="2180" class="tbl_bottom">
            <tr>
                <td width="30">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
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
                <td width="80">&nbsp;</td>
                <td width="80" id="value_tdindeliqty"><?=number_format($inreceiveQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($inreceiveBalQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdallinbalqty"><?=number_format($allinreceiveBalQtyTotal, 2,'.',''); ?></td>
                <td width="80"></td>
                <td width="80" id="value_tdindeliqty"><?=number_format($inissueQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdinbalqty"><?=number_format($inissueBalQtyTotal, 2,'.',''); ?></td>
                <td width="80" id="value_tdallinbalqty"><?=number_format($allinissueBalQtyTotal, 2,'.',''); ?></td>
                <td width="80"></td>
                <td id="value_receiveBalTotal"><??></td>
            </tr>
        </table>
        
        <? }
		}
		
		?>
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
    echo "$html****$filename****$cbo_within_group****$type"; 
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
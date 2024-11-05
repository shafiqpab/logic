<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

if($action=='report_generate')
{
    $process = array( &$_POST );
  
	extract(check_magic_quote_gpc( $process ));

    $txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_conv_rate=str_replace("'","",$txt_conv_rate);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$cbo_ship_status=str_replace("'","",$cbo_ship_status);
	
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_style=str_replace("'","",$txt_style);

    $type=str_replace("'","",$type);

    $ship_date_cond="";
    if($txt_date_from!="" && $txt_date_to!="")
	{
		$ship_date_cond=" and b.pub_shipment_date between '$txt_date_from' and '$txt_date_to' ";
	}

    $job_no_cond="";

    if(trim($txt_job_no)!="") $job_no_cond.=" and a.job_no_prefix_num  in($txt_job_no)";
    if(trim($txt_ref_no)!="") $job_no_cond.=" and b.grouping='$txt_ref_no'";
    if(trim($cbo_ship_status)>0) $job_no_cond.=" and b.shiping_status='$cbo_ship_status'";	
    if($db_type==0) $select_job_year="year(a.insert_date) as job_year"; else $select_job_year="to_char(a.insert_date,'YYYY') as job_year";	

    if(trim($txt_style)!="") $job_no_cond.=" and a.style_ref_no like('%$txt_style%')";

    // Order Entry
    $sql_po="SELECT a.buyer_name, a.job_no, a.season_buyer_wise,a.job_no_prefix_num, $select_job_year, a.style_ref_no, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date, a.avg_unit_price, b.shiping_status
	from wo_po_details_master a, wo_po_break_down b
    where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $job_no_cond $ship_date_cond
    group by a.buyer_name, a.job_no, a.season_buyer_wise,a.job_no_prefix_num, to_char(a.insert_date,'YYYY') , a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.po_quantity , b.plan_cut,b.grouping,b.file_no, b.pub_shipment_date, a.avg_unit_price, b.shiping_status order by a.job_no, b.pub_shipment_date, b.id";
    //echo $sql_po;
	$sql_po_result=sql_select($sql_po);
    $style_wise_po_count = array();
	$result_data_arr=$result_job_wise=array();$all_po_id=""; $JobArr=array();
    foreach($sql_po_result as $row)
	{
        if($all_po_id=="") $all_po_id=$row[csf("po_id")]; else $all_po_id.=",".$row[csf("po_id")];
        
        $result_data_arr[$row[csf("job_no")]]["po_id"].=$row[csf("po_id")].',';
		$result_data_arr[$row[csf("job_no")]]["season"]=$row[csf("season_buyer_wise")];
		//$result_data_arr[$row[csf("job_no")]]["file_no"].=$row[csf("file_no")].',';
		$result_data_arr[$row[csf("job_no")]]["buyer_name"]=$row[csf("buyer_name")];
		$result_data_arr[$row[csf("job_no")]]["job_no"]=$row[csf("job_no")];
		$result_data_arr[$row[csf("job_no")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
		$result_data_arr[$row[csf("job_no")]]["job_year"]=$row[csf("job_year")];
		$result_data_arr[$row[csf("job_no")]]["style_ref_no"]=$row[csf("style_ref_no")];
		$result_data_arr[$row[csf("job_no")]]["ratio"]=$row[csf("ratio")];
		$result_data_arr[$row[csf("job_no")]]["ref_no"]=$row[csf("grouping")];
		$result_data_arr[$row[csf("job_no")]]["file_no"]=$row[csf("file_no")];
		$result_data_arr[$row[csf("job_no")]]["po_number"]=$row[csf("po_number")];
		$result_data_arr[$row[csf("job_no")]]["po_qnty"]+=$row[csf("po_qnty")]*$row[csf("ratio")];
		$result_data_arr[$row[csf("job_no")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$result_data_arr[$row[csf("job_no")]]["avg_unit_price"]+=$row[csf("avg_unit_price")];
        $result_data_arr[$row[csf("job_no")]]["total_po_count"]++;
		$result_data_arr[$row[csf("job_no")]]["shiping_status"]=$row[csf("shiping_status")];
        $result_job_wise[$row[csf("job_no")]].=$row[csf("po_id")].",";
        $JobArr[]="'".$row[csf('job_no')]."'";
        $job_no=$row[csf('job_no')];
		$style_wise_po_count[$row[csf('job_no')]]++;
    }

    //var_dump($all_po_id);
     //echo "<pre>";print_r($style_wise_po_count);

    if($all_po_id==''){echo "<h2 style='color:#FE4B4B;'>Data not found</h2>";exit();}
    $all_po_id=implode(",",array_unique(explode(",",$all_po_id)));
    
    $poIds=chop($all_po_id,','); $po_cond_for_in=""; $po_cond_for_in2=""; $po_cond_for_in3=""; $po_cond_for_in4="";
    $po_ids=count(array_unique(explode(",",$all_po_id)));
    if($db_type==2 && $po_ids>1000)
    {
        $po_cond_for_in=" and (";
        $po_cond_for_in2=" and (";
        $po_cond_for_in3=" and (";
        
        $poIdsArr=array_chunk(explode(",",$poIds),999);
        foreach($poIdsArr as $ids)
        {
            $ids=implode(",",$ids);
            $po_cond_for_in.=" b.po_break_down_id in($ids) or"; 
            $po_cond_for_in2.=" a.po_breakdown_id in($ids) or"; 
            $po_cond_for_in3.=" b.order_id in($ids) or"; 
        }
        $po_cond_for_in=chop($po_cond_for_in,'or ');
        $po_cond_for_in.=")";
        $po_cond_for_in2=chop($po_cond_for_in2,'or ');
        $po_cond_for_in2.=")";
        $po_cond_for_in3=chop($po_cond_for_in3,'or ');
        $po_cond_for_in3.=")";
    }
    else
    {
        $po_cond_for_in=" and b.po_break_down_id in($poIds)";
        $po_cond_for_in2=" and a.po_breakdown_id  in($poIds)";
        $po_cond_for_in3=" and b.order_id in($poIds)";
    }

    // Main Fabric Booking V2 and Short Fabric Booking
    $sql_wo=sql_select("SELECT a.booking_no,a.booking_type,b.po_break_down_id,
    (CASE WHEN A.IS_SHORT=2 and a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS main_grey_req_qnty, 
    (CASE WHEN A.IS_SHORT=2 and a.fabric_source=1 and a.item_category=2 THEN b.fin_fab_qnty ELSE 0 END) AS main_fin_fab_req_qnty,
    (CASE WHEN A.IS_SHORT=1 and a.fabric_source=1 and a.item_category=2 THEN b.grey_fab_qnty ELSE 0 END) AS short_grey_req_qnty, 
    (CASE WHEN A.IS_SHORT=1 and a.fabric_source=1 and a.item_category=2 THEN b.fin_fab_qnty ELSE 0 END) AS short_fin_fab_req_qnty
    from wo_booking_mst a, wo_booking_dtls b  where a.booking_no=b.booking_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in ");
    $booking_req_arr=array();
    foreach ($sql_wo as $brow)
    {
        if($brow[csf("main_grey_req_qnty")]>0)
        {
            $booking_req_arr[$brow[csf("po_break_down_id")]]['main_grey']+=$brow[csf("main_grey_req_qnty")];
        }
        if($brow[csf("main_fin_fab_req_qnty")]>0)
        {
            $booking_req_arr[$brow[csf("po_break_down_id")]]['main_fin']+=$brow[csf("main_fin_fab_req_qnty")];
        }
        if($brow[csf("short_grey_req_qnty")]>0)
        {
            $booking_req_arr[$brow[csf("po_break_down_id")]]['short_grey']+=$brow[csf("short_grey_req_qnty")];
        }		
        if($brow[csf("short_fin_fab_req_qnty")]>0)
        {
            $booking_req_arr[$brow[csf("po_break_down_id")]]['short_fin']+=$brow[csf("short_fin_fab_req_qnty")];
        }
        if($brow[csf("booking_type")]==1)
        {
            $booking_req_arr[$brow[csf("po_break_down_id")]]['booking_no'].=$brow[csf("booking_no")].',';
        }
    }
    //var_dump($booking_req_arr);

    // Garments Delivery Entry
    $sql_res=sql_select("SELECT b.po_break_down_id as po_id, c.job_no_mst ,
	sum(CASE WHEN b.entry_form=0 THEN b.ex_factory_qnty ELSE 0 END) as exfac_qnty
	from  pro_ex_factory_mst b, wo_po_break_down c  where b.po_break_down_id=c.id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $po_cond_for_in group by b.po_break_down_id, c.job_no_mst");
	$ex_factory_qty_arr=array(); $exfact_wise_po_count = array();
    foreach($sql_res as $row)
	{
		$ex_factory_qty_arr[$row[csf('po_id')]]['exfac_qnty']=$row[csf('exfac_qnty')];
		$ex_factory_qty_arr[$row[csf('po_id')]]['exfac_po']=$row[csf('po_id')];
		$exfact_wise_po_count[$row[csf('job_no_mst')]]++;
	}
   
    //echo "<pre>";print_r($exfact_wise_po_count);
    
    // Yarn Issue
    $yarnDataArr=sql_select("SELECT a.po_breakdown_id, 
	sum(case when a.entry_form=3 and c.entry_form=3  then a.quantity else 0 end) as yarn_issue_qnty,
	sum(case when a.entry_form=3 and c.entry_form=3  then a.quantity*b.cons_rate else 0 end) as yarn_issue_value
	from order_wise_pro_details a, inv_transaction b, inv_issue_master c 
	where a.trans_id=b.id and b.mst_id=c.id and a.trans_type=2 and b.transaction_type=2 and b.item_category=1 and c.issue_purpose in(1,4) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $po_cond_for_in2 group by a.po_breakdown_id");

    $yarn_issue_arr=array();
    foreach($yarnDataArr as $row)
    {
        $yarn_issue_arr[$row[csf("po_breakdown_id")]]["yarn_issue_qnty"]=$row[csf("yarn_issue_qnty")];
        $yarn_issue_arr[$row[csf("po_breakdown_id")]]["yarn_issue_value"]=$row[csf("yarn_issue_value")];
    }
    //var_dump($yarn_issue_arr);
    //echo "<pre>";print_r($yarn_issue_arr);

    $tbl_width=1990;
    ob_start();
    ?>

    <div style="width:100%">
        <table width="<? echo $tbl_width;?>">
            <tr>
                <td align="center" width="100%" colspan="15" class="form_caption"><? echo $company_library[str_replace("'","",$cbo_company_name)].'<br/>';
				if($txt_date_from!="") echo  $txt_date_from.' To '.$txt_date_to;
				 ?></td>
            </tr>
        </table>

        <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
            <thead>
                <tr style="font-size:13px">
                   	<th width="80">Internal Ref</th>
                   	<th width="100">Style No</th>
                   	<th width="50">Job No</th>
                   	<th width="50">Job Year</th>
                   	<th width="80">Avg Unit Price [Order Entry]</th>
                   	<th width="80">Job Qty.<br/> (Pcs)</th>
                   	<th width="100">Ex-Factory Qty (Pcs)</th>
                   	<th width="80">Avg Unit Price [Export Invoice]</th>
                   	<th width="80">Export Invoice Qty (Pcs)</th>
                   	<th width="90">Fab.Req. Finish (Main Booking)</th>
                   	<th width="80">Fab.Req. Grey (Main Booking)</font></th>
                   	<th width="80">Fab.Req. Finish (Short)</th>
                   	<th width="80">Fab.Req. Grey (Short)</th>
                   	<th width="80">Fab.Req. Finish (Main+Short)</th>
                   	<th width="80">Fab.Req. Grey (Main+Short)</th>
                   	<th width="80">Net Yarn Issued Qty</th>
                   	<th width="80">Yarn Issued Value</th>
                   	<th width="80">Grey Fabric Rcv</th>

                   	<th width="80">Knitting Bill Qty</th>
                   	<th width="80">Knitting Bill Value</th>
                   	<th width="80">Finish Fabric Deli. To Store</th>
                   	<th width="80">Dyeing Bill Qty</th>
                   	<th width="80">Dyeing Bill Value</th>
                   	<th>Order Status</th>
                </tr>
            </thead>
       	</table>

        <div style="width:<? echo $tbl_width+20;?>px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body">
        <table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2">
        <?
        $i=1;
        foreach($result_data_arr as $job_no=>$val)
        {
            $po_id=rtrim($val["po_id"],',');
            $po_ids = array_unique(explode(",",$po_id));
            //var_dump($po_ids);

            $ex_factory_qty=$main_finish_fabric_req_qnty=$main_grey_fabric_req_qnty=$short_finish_fabric_req_qnty=$short_grey_fabric_req_qnty=$knit_gray_roll_rec_qty=$finish_delevToStore_qnty=$yarn_issue_qnty=$yarn_issue_rtn_qnty=$yarn_issue_value=$yarn_issue_rtn_value=$invoice_qty=$invoice_rate=$invoice_total_po=$knitting_bill_in_qty=$knitting_bill_in_value=$knitting_bill_out_qty=$knitting_bill_out_value=$dyeing_bill_in_qty=$dyeing_bill_in_value=$dyeing_bill_out_qty=$dyeing_bill_out_value=0;

            foreach ($po_ids as $pId)
            {
                $ex_factory_qty+=$ex_factory_qty_arr[$pId]['exfac_qnty'];
                $main_finish_fabric_req_qnty+=$booking_req_arr[$pId]['main_fin'];
                $main_grey_fabric_req_qnty+=$booking_req_arr[$pId]['main_grey'];
                $short_finish_fabric_req_qnty+=$booking_req_arr[$pId]['short_fin'];
				$short_grey_fabric_req_qnty+=$booking_req_arr[$pId]['short_grey'];
            }
            
            if ($val["avg_unit_price"]>0 && $val["total_po_count"]>0) 
			{
                $po_avg_rate=$val["avg_unit_price"]/$val["total_po_count"];
            }
            else{
                $po_avg_rate=0;
            }
        ?>
            <tr bgcolor = "<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:13px">
                <td width="80" align="center"><p><? echo $val["ref_no"]; ?></p></td>
                <td width="100" align="center"><p><? echo $val["style_ref_no"]; ?></p></td>
                <td width="50" align="center"><p><? echo $val["job_no_prefix_num"]; ?></p></td> 
                <td width="50" align="center"><p><? echo $val['job_year']; ?></p></td>
                <td width="80" align="right"><p><? echo number_format($po_avg_rate,2); ?></p></td>
                <td width="80" align="right"><? echo number_format($val["po_qnty"],2);?></td>
                <td width="100" align="right"><? echo number_format($ex_factory_qty,2); ?></td>
                <td width="80" align="right" title=""><p></p></td>
                <td width="80" align="right"></td>
                <td width="90" align="right"><? echo number_format($main_finish_fabric_req_qnty,2); ?></td>
                <td width="80" align="right"><? echo number_format($main_grey_fabric_req_qnty,2); ?></td>
                <td width="80" align="right"><? echo number_format($short_finish_fabric_req_qnty,2); ?></td>
                <td width="80" align="right"><? echo number_format($short_grey_fabric_req_qnty,2); ?></td>
                <td width="80" align="right"><? $fin_main_sort=$main_finish_fabric_req_qnty+$short_finish_fabric_req_qnty; echo number_format($fin_main_sort,2); ?></td>
                <td width="80" align="right"><? $grey_main_sort=$main_grey_fabric_req_qnty+$short_grey_fabric_req_qnty; echo number_format($grey_main_sort,2) ?></td>
                <td width="80" align="right"></td>
                <td width="80" align="right"></td>
                <td width="80" align="right"></td>
                <td width="80" align="right"></td>
                <td width="80" align="right"></td>
                <td width="80" align="right"></td>
                <td width="80" align="right"></td>
                <td width="" align="center"></td>
            </tr>
            <?

            $tot_order_qty+=$val["po_qnty"];
            $tot_ex_factory_qty+=$ex_factory_qty;
            $tot_main_finish_fabric_req_qnty+=$main_finish_fabric_req_qnty;
            $tot_main_grey_fabric_req_qnty+=$main_grey_fabric_req_qnty;
            $tot_short_finish_fabric_req_qnty+=$short_finish_fabric_req_qnty;
			$tot_short_grey_fabric_req_qnty+=$short_grey_fabric_req_qnty;
            $tot_fin_main_sort+=$fin_main_sort;
			$tot_grey_main_sort+=$grey_main_sort;

            $i++;
            }
            ?>

        </table>
        </div>

        <table width="<? echo $tbl_width;?>" class="tbl_bottom" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr style="font-size:13px">
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>   
                <td width="50">&nbsp;</td>
                <td width="50">&nbsp;</td>             
                 <td width="80">Total:</td>
                <td width="80" align="right" id="td_order_qty"><? echo number_format($tot_order_qty); ?></td>
                <td width="100" align="right" id="td_ex_factory_qty"><? echo number_format($tot_ex_factory_qty,2); ?></td> 
                <td width="80"></td>
                <td width="80" align="right" id="td_invoice_qty"><? echo number_format($tot_invoice_qty,2); ?></td>
                <td width="90" align="right" id="td_main_finish_fabric_req_qnty"><? echo number_format($tot_main_finish_fabric_req_qnty,2); ?></td>
                <td width="80" align="right" id="td_main_grey_fabric_req_qnty"><? echo number_format($tot_main_grey_fabric_req_qnty,2); ?></td>
                <td width="80" align="right" id="td_short_finish_fabric_req_qnty"><? echo number_format($tot_short_finish_fabric_req_qnty,2); ?></td>
                <td width="80" align="right" id="td_short_grey_fabric_req_qnty"><? echo number_format($tot_short_grey_fabric_req_qnty,2); ?></td>
                <td width="80" align="right" id="td_fin_main_sort"><? echo number_format($tot_fin_main_sort,2); ?></td>
                <td width="80" align="right" id="td_grey_main_sort"><? echo number_format($tot_grey_main_sort,2); ?></td>
                <td width="80" align="right" id="td_total_issued"><? echo number_format($tot_total_issued,2); ?></td>
                <td width="80" align="right" id="td_total_issued_value"><? echo number_format($tot_total_issued_value,2); ?></td>
                <td width="80" align="right" id="td_knit_gray_roll_rec_qty"><? echo number_format($tot_knit_gray_roll_rec_qty,2); ?></td>
                <td width="80" align="right" id="td_knitting_bill_qty"><? echo number_format($tot_knitting_bill_qty,2); ?></td>
                <td width="80" align="right" id="td_knitting_bill_value"><? echo number_format($tot_knitting_bill_value,2); ?></td>
                <td width="80" align="right" id="td_finish_delevToStore_qnty"><? echo number_format($tot_finish_delevToStore_qnty,2); ?></td>
                <td width="80" align="right" id="td_dyeing_bill_qty"><? echo number_format($tot_dyeing_bill_qty,2); ?></td> 
                <td width="80" align="right" id="td_dyeing_bill_value"><? echo number_format($tot_dyeing_bill_value,2); ?></td>
                <td width=""></td>
            </tr>
       	</table>
 
    </div>
    <?
	$html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html****$filename****1****$type";
    exit();
}
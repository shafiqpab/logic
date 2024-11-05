<?
session_start();
include('../../../../includes/common.php');

extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$user_id=$_SESSION['logic_erp']['user_id'];
$_SESSION['page_permission']=$permission;

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 80, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Dealing Merchant-", $selected, "" );
	exit();
}

if ($action=="cbo_factory_merchant")
{
	echo create_drop_down( "cbo_factory_merchant", 80, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select-", $selected, "" );
	exit();
}

if($action=="load_report_format")
{
	// echo $sql="select format_id from lib_report_template where template_name in(".$data.") and module_id=11 and report_id in(236) and is_deleted=0 and status_active=1"; die;

	$print_report_format=return_field_value("format_id","lib_report_template","template_name in('".$data."') and module_id=11 and report_id in(236) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);
	exit();
}


if ($action=="get_defult_date")
{
	$report_date_catagory=return_field_value("report_date_catagory", "variable_order_tracking", "company_name in ($data)  and variable_list=42 and status_active=1 and is_deleted=0");
	if($report_date_catagory=="")
	{
		$report_date_catagory=1;
	}
	echo $report_date_catagory;
	die;
}

if ($action=="load_drop_down_agent")
{
	echo create_drop_down( "cbo_agent", 130, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21))  order by buyer_name","id,buyer_name", 1, "-- Select Agent --", $selected, "" );
	exit();
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 100, "select id,location_name from lib_location where status_active=1 and is_deleted=0 and company_id='$data'
	order by location_name","id,location_name", 1, "-- Select Location --", $selected, "",0 );
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_agent=str_replace("'","",$cbo_agent);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$cbo_year=str_replace("'","",$cbo_year);
	
	$cbo_team_name=str_replace("'","",$cbo_team_name);
	$cbo_team_member=str_replace("'","",$cbo_team_member);
	$cbo_factory_merchant=str_replace("'","",$cbo_factory_merchant);
	$cbo_product_category=str_replace("'","",$cbo_product_category);
	$cbo_category_by=str_replace("'","",$cbo_category_by);
	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$buyer_full_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$company_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	
	$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');
	$teamArr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
	$teamMemberArr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
	$season_arr=return_library_array( "select id,season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
	$costing_per_arr=return_library_array( "select job_no, costing_per from  wo_pre_cost_mst where status_active =1 and is_deleted=0",'job_no','costing_per');
	$commission_budget_arr=return_library_array( "select job_no, commission from  wo_pre_cost_dtls where 1=1 status_active =1 and is_deleted=0",'job_no','commission');
	
	if($cbo_style_owner==0) $styleOwnerCond=""; else $styleOwnerCond=" and a.style_owner in ($cbo_style_owner)";
	if($cbo_buyer_name==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	
	if($cbo_agent==0) $agentCond=""; else $agentCond=" and a.client_id=$cbo_agent";
	if(trim($txt_style_ref)=='') $styleRefCond=""; else $styleRefCond=" and a.style_ref_no='".trim($txt_style_ref)."'";
	
	if($cbo_team_name==0) $teamCond=""; else $teamCond=" and a.team_leader=$cbo_team_name";
	if($cbo_team_member==0) $memberCond=""; else $memberCond=" and a.dealing_marchant=$cbo_team_member";
	if($cbo_factory_merchant==0) $factory_merchantCond=""; else $factory_merchantCond=" and a.factory_marchant=$cbo_factory_merchant";
	if($cbo_product_category==0) $product_categoryCond=""; else $product_categoryCond=" and a.product_category=$cbo_product_category";
	
	$year_cond=""; $date_diff="";
	if($db_type==0)
	{
		$date=date('Y-m-d');
		$start_date=change_date_format($date_from,'yyyy-mm-dd','-');
		$end_date=change_date_format($date_to,'yyyy-mm-dd','-');
		if(trim($cbo_year)!=0) $yearCond=" and YEAR(a.insert_date)=$cbo_year";
		$date_diff="DATEDIFF(c.country_ship_date, '$date')";
    }
	else if($db_type==2)
	{
		$date=date('d-m-Y');
		$start_date=change_date_format($date_from,'','-',1);
		$end_date=change_date_format($date_to,'','-',1);
		if(trim($cbo_year)!=0) $yearCond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
		$date_diff="(c.country_ship_date - to_date('$date','dd-mm-yyyy'))";
    }
	$date_cond=""; 
	if($cbo_category_by==1)
	{
		if ($start_date!="" && $end_date!="") $date_cond="and c.country_ship_date between '$start_date' and  '$end_date'";
	}
	else if($cbo_category_by==2)
	{
		if ($start_date!="" && $end_date!="") $date_cond=" and b.pub_shipment_date between '$start_date' and  '$end_date'";
	}
	else if($cbo_category_by==3)
	{
		if ($start_date!="" && $end_date!="") $date_cond=" and b.shipment_date between '$start_date' and  '$end_date'";
	}
	else if($cbo_category_by==4)
	{
		if ($start_date!="" && $end_date!="")
		{
			if($db_type==0) $date_cond=" and date(b.insert_date) between '$start_date' and  '$end_date'";
			else if($db_type==2)  $date_cond=" and TRUNC(b.insert_date) between '$start_date' and  '$end_date'";
		}
	}
	//echo $date_cond.'=='.$cbo_category_by;

	/*if ($start_date!="" && $end_date!="")
	{
		$year="";
		$sy = date('Y',strtotime($start_date));
		$ey = date('Y',strtotime($end_date));
		$dif_y=$ey-$sy;
		for($i=1; $i<$dif_y; $i++)
		{
			$year.= $sy+$i.",";
		}
		$tot_year= $sy;
		if($year !="")
		{
			$tot_year.=",".$year;
		}
		if($ey!=$sy)
		{
			if($year=="") $tot_year.=",".$ey;
			else $tot_year.=$ey;
		}
		$year_cond="and a.year_id in($tot_year)";
	}
    $target_basic_qnty=array();
	$total_target_basic_qnty=0;
    $sm = date('m',strtotime($start_date));
	$em = date('m',strtotime($end_date));*/


	$data_sql="select a.id, a.company_name, a.style_owner, a.buyer_name, a.client_id, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.style_description, a.season_buyer_wise, a.product_dept, a.product_category, a.order_uom,a.GMTS_ITEM_ID, a.set_smv, a.team_leader, a.dealing_marchant, a.factory_marchant, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.is_confirmed, b.pub_shipment_date, b.po_received_date, b.original_po_qty, $date_diff as date_diff, b.unit_price,
	
	c.shiping_status, c.country_id, c.country_ship_date, c.order_quantity as qtyPcs, c.order_rate, c.order_total
	
	from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
	where a.id=b.job_id and b.id=c.po_break_down_id and a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $styleOwnerCond $buyer_id_cond $agentCond $styleRefCond $yearCond $teamCond $memberCond $factory_merchantCond $product_categoryCond $date_cond order by b.id, c.country_ship_date asc";
	
	//echo $data_sql;
	$data_sql_res=sql_select($data_sql); $tot_rows=0; $poIds=''; $jobNos="";
	$summary_data_arr=array(); $details_data_arr=array(); $month_count_arr=array(); $buyer_total_arr=array();
	foreach( $data_sql_res as $row)
	{
		$tot_rows++;
		$poIds.=$row[csf("po_id")].",";
		$jobNos.="'".$row[csf("job_no")]."',";
		
		if($cbo_category_by==1) $sdate=$row[csf('country_ship_date')]; else $sdate=$row[csf('pub_shipment_date')];
		$monthID=date("m",strtotime($sdate));
		$month_id = ltrim($monthID, '0');
		$yearID=date("Y",strtotime($sdate));
		
		$summary_data_arr[$row[csf('buyer_name')]][$yearID][$month_id][$row[csf('is_confirmed')]]['poqty']+=$row[csf('qtyPcs')];
		$summary_data_arr[$row[csf('buyer_name')]][$yearID][$month_id][$row[csf('is_confirmed')]]['amount']+=$row[csf('order_total')];
		
		$month_count_arr[$yearID][$month_id]['year']=$yearID;
		$month_count_arr[$yearID][$month_id]['month']=$month_id;
		
		$buyer_total_arr[$yearID][$month_id]['poqty']+=$row[csf('qtyPcs')];
		$buyer_total_arr[$yearID][$month_id]['amount']+=$row[csf('order_total')];
		
		$grand_total_confirm_po+=$row[csf('qtyPcs')];
		$grand_total_confirm_amount+=$row[csf('order_total')];
		
		$details_data_arr[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('shiping_status')]]['str']=$row[csf('company_name')].'__'.$row[csf('style_owner')].'__'.$row[csf('buyer_name')].'__'.$row[csf('client_id')].'__'.$row[csf('job_no_prefix_num')].'__'.$row[csf('job_no')].'__'.$row[csf('style_ref_no')].'__'.$row[csf('style_description')].'__'.$row[csf('season_buyer_wise')].'__'.$row[csf('product_dept')].'__'.$row[csf('product_category')].'__'.$row[csf('order_uom')].'__'.$row[csf('set_smv')].'__'.$row[csf('team_leader')].'__'.$row[csf('dealing_marchant')].'__'.$row[csf('factory_marchant')].'__'.$row[csf('ratio')].'__'.$row[csf('po_number')].'__'.$row[csf('is_confirmed')].'__'.$row[csf('pub_shipment_date')].'__'.$row[csf('po_received_date')].'__'.$row[csf('unit_price')].'__'.$row['GMTS_ITEM_ID'];
		//$country_str_arr[$row[csf('po_id')]]['str'].=$row[csf('country_id')].'***'.$row[csf('country_ship_date')].'***'.$row[csf('shiping_status')].'***'.$row[csf('date_diff')].',';
		$details_data_arr[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('shiping_status')]]['qty']+=$row[csf('qtyPcs')];
		$details_data_arr[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('shiping_status')]]['amt']+=$row[csf('order_total')];
		$details_data_arr[$row[csf('po_id')]][$row[csf('country_id')]][$row[csf('country_ship_date')]][$row[csf('shiping_status')]]['date_diff']=$row[csf('date_diff')];
	}
	unset($data_sql_res);
	
	$poIds=chop($poIds,',');
	$jobNos=chop($jobNos,',');
	$jobCount=count(array_unique(explode(",",$jobNos)));
	$poCount=count(array_unique(explode(",",$poIds)));
	
	$jobNos=implode(",",array_unique(explode(",",$jobNos)));
	$poIds=implode(",",array_unique(explode(",",$poIds)));
	
	$exPoIds_cond=""; $bpoIds_cond=""; $lpoIds_cond=""; 
	if($db_type==2 && $poCount>1000)
	{
		$exPoIds_cond=" and (";
		$bpoIds_cond=" and (";
		$lpoIds_cond=" and (";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$exPoIds_cond.=" po_break_down_id in($ids) or ";
			$bpoIds_cond.=" b.po_break_down_id in($ids) or ";
			$lpoIds_cond.=" po_id in($ids) or ";
		}
			
		$exPoIds_cond=chop($exPoIds_cond,'or ');
		$exPoIds_cond.=")";
		
		$bpoIds_cond=chop($bpoIds_cond,'or ');
		$bpoIds_cond.=")";
		
		$lpoIds_cond=chop($lpoIds_cond,'or ');
		$lpoIds_cond.=")";
	}
	else
	{
		$exPoIds_cond=" and po_break_down_id in ($poIds)";
		$bpoIds_cond=" and b.po_break_down_id in ($poIds)";
		$lpoIds_cond=" and po_id in ($poIds)";
	}
	
	$exfactory_data_arr=array();
	$ex_sql="select po_break_down_id, country_id, MAX(ex_factory_date) as ex_factory_date,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_return_qnty
	
	from pro_ex_factory_mst where 1=1 $exPoIds_cond and status_active=1 and is_deleted=0 group by po_break_down_id, country_id";
	$exfactory_data=sql_select($ex_sql);
	foreach($exfactory_data as $exrow)
	{
		$exfactory_data_arr[$exrow[csf('po_break_down_id')]][$exrow[csf('country_id')]]['ex_factory_qnty']+=$exrow[csf('ex_factory_qnty')]-$exrow[csf('ex_factory_return_qnty')];
		$exfactory_data_arr[$exrow[csf('po_break_down_id')]][$exrow[csf('country_id')]]['ex_factory_date']=$exrow[csf('ex_factory_date')];
	}
	unset($exfactory_data);
	
	$total_month=0;
	foreach($month_count_arr as $year_id=>$year_val)
	{
		foreach($year_val as $month_id=>$month_val)
		{
			$total_month=$total_month+1;
		}
	}
	
	$table_width=700+(490*$total_month);
	$col_span=8+($total_month*7);
	ob_start();

	?>
    <div id="print_Div">
        <fieldset style="width:<? echo $table_width+10; ?>px;">
            <table id="" class="" width="<? echo $table_width;  ?>" cellspacing="0" >
                <tr class="" style="border:none; ">
                    <td colspan="2" align="left"><input type="button" id="summary_print_button" class="formbutton" value="Print" style="width:70px;" onClick="print_report_part_by_part( 'print_Div', '#summary_print_button')"/></td>
                    <td colspan="<? echo $col_span; ?>" align="center" style="font-size:18px">Order Booking Summary</td>
                </tr>
            </table>
            <table id="table_header" class="rpt_table" width="<? echo $table_width;  ?>" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr >
                        <th width="30" rowspan="2">SL</th>
                        <th width="80" rowspan="2">Buyer</th>
                        <?
                        foreach($month_count_arr as $year_id=>$year_val)
                        {
                            foreach($year_val as $month_id=>$month_val)
                            {
                                ?>
                                <th colspan="7"><? echo $months[$month_val['month']]."   ".$month_val['year']; ?></th>
                                <?
                            }
                        }
                        ?>
                        <th width="590" colspan="8">Total</th>
                    </tr>
                    <tr>
                        <?
                        foreach($month_count_arr as $year_id=>$year_val)
                        {
                            foreach($year_val as $month_id=>$month_val)
                            {
                                ?>
                                <th width="70">Proj. Qty</th>
                                <th width="70">Proj. Amt </th>
                                <th width="70">Conf. Qty</th>
                                <th width="70">Conf. Amt</th>
                                <th width="70">Total Qty</th>
                                <th width="70">Total Amt</th>
                                <th width="60">%</th>
                                <?
                            }
                        }
                        ?>
                        <th width="80">Proj. Qty</th>
                        <th width="80">Proj. Amt </th>
                        <th width="80">Conf. Qty</th>
                        <th width="80">Conf. Amt</th>
                        <th width="80">Total Qty</th>
                        <th width="80">Total Amt</th>
                        <th width="50">%</th>
                        <th>Avg Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i=1; $monthTot_arr=array();
                    foreach($summary_data_arr as $buy_id=>$buy_val)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        //$summary_data_arr[$row[csf('buyer_name')]][$yearID][$month_id][$row[csf('is_confirmed')]]['poqty']
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
                            <td><? echo $i; ?></td>
                            <td align="center"> <? echo $buyer_short_name_arr[$buy_id]; ?>  </td>
                            <?
                            foreach($month_count_arr as $year_id=>$year_val)
                            {
                                foreach($year_val as $month_id=>$month_val)
                                {
                                    $projected_qty=$projected_amt=$confirm_qty=$confirm_amt=0;
                                    
                                    $projected_qty=$buy_val[$year_id][$month_id][2]['poqty'];
                                    $projected_amt=$buy_val[$year_id][$month_id][2]['amount'];
                                    
                                    $confirm_qty=$buy_val[$year_id][$month_id][1]['poqty'];
                                    $confirm_amt=$buy_val[$year_id][$month_id][1]['amount'];
                                    
                                    $rowQty=$rowAmt=$rowPer=0;
                                    $rowQty=$projected_qty+$confirm_qty;
                                    $rowAmt=$projected_amt+$confirm_amt;
                                    $rowPer=$rowAmt/$buyer_total_arr[$year_id][$month_id]['amount'];
                                    ?>
                                    <td width="70" align="right"><? echo number_format($projected_qty,0); ?></td>
                                    <td width="70" align="right"><? echo number_format($projected_amt,2); ?> </td>
                                    <td width="70" align="right"><? echo number_format($confirm_qty,0); ?></td>
                                    <td width="70" align="right"><? echo number_format($confirm_amt,2); ?></td>
                                    <td width="70" align="right"><? echo number_format($rowQty,0); ?></td>
                                    <td width="70" align="right"><? echo number_format($rowAmt,2); ?></td>
                                    <td width="60" align="center"><? echo number_format($rowPer*100,2); ?></td>
                                    <?
                                    $monthTot_arr[$year_id][$month_id][2]['qty']+=$projected_qty;
                                    $monthTot_arr[$year_id][$month_id][2]['amt']+=$projected_amt;
                                    $monthTot_arr[$year_id][$month_id][1]['qty']+=$confirm_qty;
                                    $monthTot_arr[$year_id][$month_id][1]['amt']+=$confirm_amt;
                                    
                                    $rowTot_projectQty+=$projected_qty;
                                    $rowTot_projectAmt+=$projected_amt;
                                    
                                    $rowTot_confirmQty+=$confirm_qty;
                                    $rowTot_confirmAmt+=$confirm_amt;
                                    
                                    $gProjectQty+=$projected_qty;
                                    $gProjectAmt+=$projected_amt;
                                    $gConfirmQty+=$confirm_qty;
                                    $gConfirmAmt+=$confirm_amt;
                                }
                            }
                            
                            $rowtotQty=0; $rowtotAmt=0;
                            $rowtotQty=$rowTot_projectQty+$rowTot_confirmQty; 
                            $rowtotAmt=$rowTot_projectAmt+$rowTot_confirmAmt;
                            ?>
                            <td width="80" align="right"><? echo number_format($rowTot_projectQty,0); ?></td>
                            <td width="80" align="right"><? echo number_format($rowTot_projectAmt,2); ?> </td>
                            <td width="80" align="right"><? echo number_format($rowTot_confirmQty,0); ?></td>
                            <td width="80" align="right"><? echo number_format($rowTot_confirmAmt,2); ?></td>
                            <td width="80" align="right"><? echo number_format($rowtotQty,0); ?></td>
                            <td width="80" align="right"><? echo number_format($rowtotAmt,2); ?></td>
                            <td width="50" align="center"><? echo number_format(($rowtotAmt/$grand_total_confirm_amount)*100,2); ?></td>
                            <td width="60" align="right"><? echo number_format(($rowtotAmt/$rowtotQty),2); ?></td>
                        </tr>
                        <?
                        $rowTot_projectQty=$rowTot_projectAmt=$rowTot_confirmQty=$rowTot_confirmAmt=0;
                        $i++;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Total:</th>
                        <?
                        foreach($month_count_arr as $year_id=>$year_val)
                        {
                            foreach($year_val as $month_id=>$month_val)
                            {
                                ?>
                                <th width="70" align="right"><? echo number_format($monthTot_arr[$year_id][$month_id][2]['qty'],0); ?></th>
                                <th width="70" align="right"><? echo number_format($monthTot_arr[$year_id][$month_id][2]['amt'],2); ?></th>
                                <th width="70" align="right"><? echo number_format($monthTot_arr[$year_id][$month_id][1]['qty'],0); ?></th>
                                <th width="70" align="right"><? echo number_format($monthTot_arr[$year_id][$month_id][1]['amt'],2); ?></th>
                                <th width="70" align="right"><? echo number_format($buyer_total_arr[$year_id][$month_id]['poqty'],0); ?></th>
                                <th width="70" align="right"><? echo number_format($buyer_total_arr[$year_id][$month_id]['amount'],2); ?></th>
                                <th width="60" align="right">&nbsp;</th>
                                <?
                            }
                        }
                        ?>
                        
                        <th width="80" align="right"><? echo  number_format($gProjectQty,0); ?></th>
                        <th width="80" align="right"><? echo  number_format($gProjectAmt,2); ?></th>
                        <th width="80" align="right"><? echo  number_format($gConfirmQty,0); ?></th>
                        <th width="80" align="right"><? echo  number_format($gConfirmAmt,2); ?></th>
                        <th width="80" align="right"><? echo  number_format($gProjectQty+$gConfirmQty,0); ?></th>
                        <th width="80" align="right"><? echo  number_format($gProjectAmt+$gConfirmAmt,2); ?></th>
                        <th width="50" align="right">&nbsp;</th>
                        <th align="right"> <? echo  number_format(($gProjectAmt+$gConfirmAmt)/($gProjectQty+$gConfirmQty),2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <br>
    <?
	if($reporttype==1)
	{
		?>
		<div id="content_report_panel">
			<table width="2900" cellspacing="0" >
				<tr class="" style="border:none; ">
					<td align="center" style="font-size:18px"><? echo $report_title; ?></td>
				</tr>
			</table>
			<table>
				<tr>
					<td bgcolor="orange" height="15" width="30"></td>
					<td>Maximum 10 Days Remaing To Ship</td>
					<td bgcolor="green" height="15" width="30">&nbsp;</td>
					<td>On Time Shipment</td>
					<td bgcolor="#2A9FFF" height="15" width="30"></td>
					<td>Delay shipment</td>
					<td bgcolor="red" height="15" width="30"></td>
					<td>Shipment Date Over & Pending</td>
				</tr>
			</table>
			<table width="2900" border="1" class="rpt_table" rules="all">
				<thead>
					<tr style="font-size:12px">
						<th width="30">SL</th>
						<th width="65">LC Company</th>
						<th width="65">Style Owner</th>
						<th width="60">Buyer</th>
						<th width="60">Client</th>
						<th width="100">Job No</th>
						<th width="70">Season</th>
						<th width="80">Pord. Dept.</th>
						<th width="80">Prod. Catg.</th>
						<th width="100">Style Ref</th>
						<th width="110">Style Des</th>
						<th width="110">Order No</th>
						<th width="60">Order Status</th>
						<th width="80">PO Rec. Date</th>
						<th width="100">Country</th>
						<? $category_type=""; if($cbo_category_by==1) $category_type="Country Ship Date"; else $category_type="Pub Ship Date"; ?>
						<th width="80">Country Ship Date</th>
						<th width="40">Lead Time</th>
						<th width="100">GRMS Item</th>
						<th width="60">SMV</th>
						<th width="100">Total SMV</th>
						<th width="90">Order Qty</th>
						<th width="30">Uom</th>
						<th width="80">Order Qty(Pcs)</th>
						<th width="60">Per Unit Price</th>
						<th width="90">Order Value</th>
						<th width="70">Commission</th>
						<th width="90">Net Order Value</th>
						<th width="80">Ex-Fac Qty (Pcs)</th>
						<th width="90">Ex-Fac Value</th>
						<th width="80">Ex-factory Bal. (Pcs)</th>
						<th width="90">Ex-factory Bal. Value</th>
						<th width="50">Days in Hand</th>
						<th width="80">Shipping Status</th>
						<th width="100">Team Name</th>
						<th width="100">Dealing Merchant</th>
						<th>Factory Merchant</th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:400px; overflow-y:scroll; width:2900px"  align="left" id="scroll_body">
			<table width="2878" border="1" class="rpt_table" rules="all" id="table_body">
				<?
				$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");
				
				$i=1;
				foreach ($details_data_arr as $po_id=>$postrrow)
				{
					foreach ($postrrow as $country_id=>$cstrrow)
					{
						foreach ($cstrrow as $country_ship_date=>$cdatestrrow)
						{
							foreach ($cdatestrrow as $shiping_status=>$strrow)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$expodata=explode("__",$strrow['str']);
								$company_name=$style_owner=$buyer_name=$client_id=$job_no_prefix_num=$job_no=$style_ref_no=$style_description=$season_buyer_wise=$productDept=$productCategory=$order_uom=$set_smv=$team_leader=$dealing_marchant=$factory_marchant=$ratio=$po_number=$is_confirmed=$pub_shipment_date=$po_received_date=$unit_price='';
								
								$company_name=$expodata[0];
								$style_owner=$expodata[1];
								$buyer_name=$expodata[2];
								$client_id=$expodata[3];
								$job_no_prefix_num=$expodata[4];
								$job_no=$expodata[5];
								$style_ref_no=$expodata[6];
								$style_description=$expodata[7];
								$season_buyer_wise=$expodata[8];
								$productDept=$expodata[9];
								$productCategory=$expodata[10];
								$order_uom=$expodata[11];
								$set_smv=$expodata[12];
								$team_leader=$expodata[13];
								$dealing_marchant=$expodata[14];
								$factory_marchant=$expodata[15];
								$ratio=$expodata[16];
								$po_number=$expodata[17];
								$is_confirmed=$expodata[18];
								$pub_shipment_date=$expodata[19];
								$po_received_date=$expodata[20];
								$unit_price=$expodata[21];
								
								
								$cdate_diff=$strrow['date_diff'];
								
								$poQty=$poAmt=$smvTot=$poQtySet=0;
								$poQty=$strrow['qty'];
								$poAmt=$strrow['amt'];
								
								if($is_confirmed==2) $color_font="#F00"; else $color_font="#000";
								
								$ex_factory_date=$exfactory_data_array[$po_id][$country_id]['ex_factory_date'];
								$date_diff_3=datediff( "d", $ex_factory_date , $country_ship_date);
								$date_diff_4=datediff( "d", $ex_factory_date , $country_ship_date);
								
								$smvTot= $set_smv*$poQty;
								$poQtySet=$poQty/$ratio;
								
								$costing_per_pcs=$commission=$net_order_value=$ex_factory_qty=$ex_factory_value=$short_access_qty=$short_access_value=0;
								$costing_per=$costing_per_arr[$job_no];
								if($costing_per ==1) $costing_per_pcs=1*12;
								else if($costing_per==2) $costing_per_pcs=1*1;
								else if($costing_per==3) $costing_per_pcs=2*12;
								else if($costing_per==4) $costing_per_pcs=3*12;
								else if($costing_per==5) $costing_per_pcs=4*12;
								
								$commission=($poQtySet/$costing_per_pcs)*$commission_budget_arr[$job_no];
								$net_order_value=$poAmt-$commission;
								
								$ex_factory_qty=$exfactory_data_arr[$po_id][$country_id]['ex_factory_qnty'];
								$ex_factory_value=$ex_factory_qty*($unit_price/$ratio);
								
								$short_access_qty=$poQty-$ex_factory_qty;
								$short_access_value=$short_access_qty*($unit_price/$ratio);
								
								$shipment_performance=0;
								if($shiping_status==1 && $cdate_diff>10 )
								{
									$color=""; $shipment_performance=0;
									$number_of_order['yet']+=1;
								}
								else if($shiping_status==1 && ($cdate_diff<=10 && $cdate_diff>=0))
								{
									$color="orange"; $shipment_performance=0;
									$number_of_order['yet']+=1;
								}
								else if($shiping_status==1 && $cdate_diff<0)
								{
									$color="red"; $shipment_performance=0;
									$number_of_order['yet']+=1;
								}
								//=====================================
								if($shiping_status==2 && $cdate_diff>10 ) $color="";
								else if($shiping_status==2 && ($cdate_diff<=10 && $cdate_diff>=0)) $color="orange";
								else if($shiping_status==2 &&  $cdate_diff<0) $color="red";
								else if($shiping_status==2 &&  $cdate_diff>=0)
								{
									$number_of_order['ontime']+=1;
									$shipment_performance=1;
								}
								else if($shiping_status==2 &&  $cdate_diff<0)
								{
									$number_of_order['after']+=1;
									$shipment_performance=2;
								}
								//========================================
								if($shiping_status==3 && $date_diff_3 >=0 ) $color="green";
								else if($shiping_status==3 &&  $date_diff_3<0) $color="#2A9FFF";
								else if($shiping_status==3 && $date_diff_4>=0 )
								{
									$number_of_order['ontime']+=1;
									$shipment_performance=1;
								}
								else if($shiping_status==3 && $date_diff_4<0)
								{
									$number_of_order['after']+=1;
									$shipment_performance=2;
								}
								
								if ($shipment_performance==0)
								{
									$po_qnty['yet']+=($poQty*$ratio);
									$po_value['yet']+=100;
								}
								else if ($shipment_performance==1)
								{
									$po_qnty['ontime']+=$ex_factory_qty;
									$po_value['ontime']+=((100*$ex_factory_qty)/($poQty*$ratio));
									$po_qnty['yet']+=(($poQty*$ratio)-$ex_factory_qty);
								}
								else if ($shipment_performance==2)
								{
									$po_qnty['after']+=$ex_factory_qty;
									$po_value['after']+=((100*$ex_factory_qty)/($poQty*$ratio));
									$po_qnty['yet']+=(($poQty*$ratio)-$ex_factory_qty);
								}
								
								$gmts_item_id_arr=array();
								foreach(explode(',',$expodata[22]) as $itemid){
									$gmts_item_id_arr[$itemid]=$garments_item[$itemid];
								}
								$gmts_item_id_str=implode(',',$gmts_item_id_arr);
								
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" >
									<td width="30" align="center" bgcolor="<? echo $color; ?>"><? echo $i; ?></td>
									<td width="65" style="word-wrap: break-word;word-break: break-all;"><? echo $company_short_name_arr[$company_name]; ?></td>
									<td width="65" style="word-wrap: break-word;word-break: break-all;"><? echo $company_short_name_arr[$style_owner]; ?></td>
									<td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_short_name_arr[$buyer_name]; ?></td>
									<td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $buyer_short_name_arr[$client_id]; ?></td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $job_no; ?></td>
									<td width="70" style="word-wrap: break-word;word-break: break-all;"><? echo $season_arr[$season_buyer_wise]; ?></td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $product_dept[$productDept]; ?></td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $product_category[$productCategory]; ?></td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $style_ref_no; ?></td>
									<td width="110" style="word-wrap: break-word;word-break: break-all;"><? echo $style_description; ?>&nbsp;</td>
									<td width="110" style="word-wrap: break-word;word-break: break-all;"><font style="color:<? echo $color_font; ?>"><? echo $po_number; ?></font></td>
									<td width="60" style="word-wrap: break-word;word-break: break-all;"><? echo $order_status[$is_confirmed]; ?></td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo change_date_format($po_received_date); ?>&nbsp;</td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $country_name_arr[$country_id]; ?></td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo change_date_format($country_ship_date); ?>&nbsp;</td>
									<td width="40" align="right"><? echo $leadTime=datediff('d',$po_received_date,$country_ship_date); ?></td>
									<td width="100"><p><?= $gmts_item_id_str ?></p></td>
									<td width="60" align="right"><? echo number_format($set_smv,2); ?></td>
									<td width="100" align="right"><? echo number_format($smvTot,2); ?></td>
									<td width="90" align="right"><? echo number_format($poQtySet,0); ?></td>
									<td width="30" style="word-wrap: break-word;word-break: break-all;"><? echo $unit_of_measurement[$order_uom];?></td>
									<td width="80" align="right"><? echo number_format($poQty,0); ?></td>
									<td width="60" align="right"><? echo number_format($unit_price,4);?></td>
									<td width="90" align="right"><? echo number_format($poAmt,0); ?></td>
									<td width="70" align="right"><? echo number_format($commission,2); ?></td>
									<td width="90" align="right" title="Order Number : <? echo $po_number; ?>"><? echo number_format ($net_order_value,2); ?></td>
									<td width="80" align="right"><? echo number_format($ex_factory_qty,0); ?></td>
									<td width="90" align="right"><? echo number_format($ex_factory_value,2); ?></td>
									<td width="80" align="right"><? echo number_format($short_access_qty,0); ?></td>
									<td width="90" align="right"><? echo number_format($short_access_value,2); ?></td>
									<td width="50" align="right" bgcolor="<? echo $color; ?>"><? if($shiping_status==1 || $shiping_status==2) echo $cdate_diff; else if($shiping_status==3) echo $date_diff_3; ?></td>
									<td width="80" style="word-wrap: break-word;word-break: break-all;"><? if($shiping_status==0) $shiping_status=1; echo $shipment_status[$shiping_status]; ?></td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $teamArr[$team_leader]; ?></td>
									<td width="100" style="word-wrap: break-word;word-break: break-all;"><? echo $teamMemberArr[$dealing_marchant]; ?></td>
									<td style="word-wrap: break-word;word-break: break-all;"><? echo $teamMemberArr[$factory_marchant]; ?></td>
								</tr>
								<?
								$i++;
								
								$gtotSmv+=$smvTot;
								$gPoQtyPcs+=$poQty;
								$gPoValue+=$poAmt;
								$gCommission+=$commission;
								$gNetPoValue+=$net_order_value;
								$gexQtyPcs+=$ex_factory_qty;
								$gexValue+=$ex_factory_value;
								$gexBalPcs+=$short_access_qty;
								$gexBalValue+=$short_access_value;
							}
						}
					}
				}
				?>
			</table>
			</div>
			<table width="2900" id="report_table_footer" border="1" class="rpt_table" rules="all">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="65">&nbsp;</th>
						<th width="65">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="110">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">Total:</th>
						<th width="80">&nbsp;</th>
						<th width="40">&nbsp;</th>
						<th width="100"></th>
						<th width="60">&nbsp;</th>
						<th width="100" id="td_tsmv"><? echo $gtotSmv; ?></th>
						<th width="90">&nbsp;</th>
						<th width="30">&nbsp;</th>
						<th width="80" id="td_pQtyPcs"><? echo $gPoQtyPcs; ?></th>
						<th width="60">&nbsp;</th>
						<th width="90" id="td_poValue"><? echo $gPoValue; ?></th>
						<th width="70" id="td_commission"><? echo $gCommission; ?></th>
						<th width="90" id="td_netPoValue"><? echo $gNetPoValue; ?></th>
						<th width="80" id="td_exQtyPcs"><? echo $gexQtyPcs; ?></th>
						<th width="90" id="td_exValue"><? echo $gexValue; ?></th>
						<th width="80" id="td_exBalPcs"><? echo $gexBalPcs; ?></th>
						<th width="90" id="td_exBalValue"><? echo $gexBalValue; ?></th>
						<th width="50">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
            <br>
			<!--<div id="shipment_performance" align="left" >
                <fieldset style="width:600px;">
                    <table width="600" border="1" cellpadding="0" cellspacing="1" class="rpt_table" rules="all" >
                        <thead>
                            <tr><th colspan="4"> <font size="4">Shipment Performance</font></th></tr>
                            <tr>
                            	<th>Particulars</th>
                                <th>No of PO</th>
                                <th>PO Qty</th>
                                <th> %</th>
                            </tr>
                        </thead>
                        <tr bgcolor="#E9F3FF">
                            <td>On Time Shipment</td>
                            <td><? //echo $number_of_order['ontime']; ?></td>
                            <td align="right"><?// echo number_format($po_qnty['ontime'],0); ?></td>
                            <td align="right"><? //echo number_format(((100*$po_qnty['ontime'])/$gPoQtyPcs),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td> Delivery After Shipment Date</td>
                            <td><? //echo $number_of_order['after']; ?></td>
                            <td align="right"><? //echo number_format($po_qnty['after'],0); ?></td>
                            <td align="right"><? //echo number_format(((100*$po_qnty['after'])/$gPoQtyPcs),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Yet To Shipment </td>
                            <td><? //echo $number_of_order['yet']; ?></td>
                            <td align="right"><? //echo number_format($po_qnty['yet'],0); ?></td>
                            <td align="right"><? //echo number_format(((100*$po_qnty['yet'])/$gPoQtyPcs),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td align="right"><? //echo number_format($po_qnty['yet']+$po_qnty['ontime']+$po_qnty['after'],0); ?></td>
                            <td align="right"><? //echo number_format(((100*$po_qnty['yet'])/$gPoQtyPcs)+((100*$po_qnty['after'])/$gPoQtyPcs)+((100*$po_qnty['ontime'])/$gPoQtyPcs),2); ?></td>
                        </tr>
                    </table>
                </fieldset>
			</div>-->
		</div>
		<?
	}
	foreach (glob("$user_id*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename****$report_type";
	exit();
}
?>
<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../includes/common.php');
//$con=connect();
$company_id=3;
$txt_demand_date = '16-Oct-2019';

    $txt_demand_date = change_date_format($txt_demand_date, 'yyyy-mm-dd', '-', 1);

    //return_field_value("select SALES_YEAR_STARTED from VARIABLE_ORDER_TRACKING where VARIABLE_LIST=12 and company_name=$company_id","SALES_YEAR_STARTED");
    //echo $start_date; die;	
    //select SALES_YEAR_STARTED from VARIABLE_ORDER_TRACKING where VARIABLE_LIST=12;


    if (strtotime($txt_demand_date) < strtotime(date("Y", strtotime($txt_demand_date)) . "-06-30"))
        $start_date = (date("Y", strtotime($txt_demand_date)) - 1) . "-07-01";
    else
        $start_date = date("Y", strtotime($txt_demand_date)) . "-07-01";
    $start_date = change_date_format($start_date, 'yyyy-mm-dd', '-', 1);



//    if( $company_id>0 )
//		return_field_value("SALES_YEAR_STARTED","VARIABLE_ORDER_TRACKING","VARIABLE_LIST=12 and company_name=$company_id");
    //echo $start_date;die;

    for ($i = 0; $i <= 8; $i++) {
        $cdate = add_date($txt_demand_date, -$i);
        if (date("D", strtotime($cdate)) == "Sat") {
            $weekstdate = change_date_format($cdate, 'yyyy-mm-dd', '-', 1);
            break;
        }
    }
    $month_st_date = change_date_format(date("Y-m", strtotime($txt_demand_date)) . "-01", 'yyyy-mm-dd', '-', 1);

    $month_query_cond2 = "and to_char(b.pub_shipment_date ,'YYYY-MM-DD') like '$month_query'";
    $com_cond = "";
    if ($company_id > 0)
        $com_cond = " and A.company_name=$company_id";
        $sql = "select a.company_name,b.id, b.shiping_status, b.po_quantity, a.total_set_qnty, b.plan_cut, (b.unit_price/a.total_set_qnty) as order_rate,b.pub_shipment_date 
        from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and b.pub_shipment_date between '" . $start_date . "' and '" . $txt_demand_date . "' and b.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status!=3 $com_cond"; //and a.job_no_prefix_num like '$txt_job_number' 
    echo $sql;
    $result = sql_select($sql);
    //$buyer_order_quantity=0; $buyer_order_val=0;$tot_buyer_order_quantity=0;
    foreach ($result as $row) {
        if ($row[csf('shiping_status')] == 2) {
            $buyer_ex_quantity = 0;
            $partial_ex_factory[$row[csf('id')]] = $row[csf('id')];
        }
        $po_quantity = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
        if (date("Y-m-d", strtotime($row[csf('pub_shipment_date')])) == date("Y-m-d", strtotime($txt_demand_date))) {

            $company_order_qnty_day[$row[csf('id')]]['order_qnty'] = $po_quantity;
            $company_order_qnty_day[$row[csf('id')]]['order_rate'] = $row[csf('order_rate')];
            $company_order_qnty_day[$row[csf('id')]]['company_name'] = $row[csf('company_name')];
        }
		//echo date("Y-m-d", strtotime($weekstdate));
        if (date("Y-m-d", strtotime($row[csf('pub_shipment_date')])) >= date("Y-m-d", strtotime($weekstdate))) {
            $company_order_qnty_week[$row[csf('id')]]['order_qnty'] = $po_quantity;
            $company_order_qnty_week[$row[csf('id')]]['order_rate'] = $row[csf('order_rate')];
            $company_order_qnty_week[$row[csf('id')]]['company_name'] = $row[csf('company_name')];
        }
        if (date("Y-m-d", strtotime($row[csf('pub_shipment_date')])) >= date("Y-m-d", strtotime($month_st_date))) {
            $company_order_qnty_month[$row[csf('id')]]['order_qnty'] = $po_quantity;
            $company_order_qnty_month[$row[csf('id')]]['order_rate'] = $row[csf('order_rate')];
            $company_order_qnty_month[$row[csf('id')]]['company_name'] = $row[csf('company_name')];
        }
        $po_quantity = $row[csf('po_quantity')] * $row[csf('total_set_qnty')];
        $company_order_qnty[$row[csf('id')]]['order_qnty'] = $po_quantity;
        $company_order_qnty[$row[csf('id')]]['order_rate'] = $row[csf('order_rate')];
        $company_order_qnty[$row[csf('id')]]['company_name'] = $row[csf('company_name')];
    }

    //printe_r($company_order_qnty_week); die;

    if (count($partial_ex_factory) > 0)
        $sql_summary_ex_factory = return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where po_break_down_id in (" . implode(",", $partial_ex_factory) . ") and status_active=1 and is_deleted=0 group by po_break_down_id", 'po_break_down_id', 'ex_factory_qnty');

    foreach ($company_order_qnty as $poid => $podtls) 
    {

        $podtls['order_qnty'] = $podtls['order_qnty'] - $sql_summary_ex_factory[$poid];

        if ($company_order_qnty_day[$poid]['order_qnty'] != '') 
        {
            $company_order_day_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_day_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
        }
        if ($company_order_qnty_week[$poid]['order_qnty'] != '') 
        {
            $company_order_qnty_week_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_qnty_week_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
        }
        if ($company_order_qnty_month[$poid]['order_qnty'] != '') 
        {
            $company_order_qnty_month_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_qnty_month_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
        }

        $company_order_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
        $company_order_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
    }
    ob_start();
    
    ?>
<!--<div id="scroll_body" style="width:100%" >-->
<table border="1" rules="all" class="rpt_table" width="500"style="align:right" >
        <thead>
            <tr>
                <th colspan="2"><? echo date("M-d", strtotime($txt_demand_date)); ?></th>
                <th colspan="3">Daily Pending</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="150"> Company Name </th>
                <th width="130">Pending PO Value </th>
                <th width="140">Pending PO Qnty.</th>
                <th>FOB</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_po_val = 0;
    $tot_po_qnty = 0;
    foreach ($company_order_day_summ as $company => $cdata) {
        $i++;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td><p><? echo $company_details[$company]; ?></p></td>
                    <td align="right"><? echo number_format($cdata['order_val'], 2);
        $tot_po_val += $cdata['order_val']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 0);
        $tot_po_qnty += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_val'] / $cdata['order_qnty']), 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th colspan="2" align="right">Total</th><th><? echo number_format($tot_po_val, 2); ?></th><th><? echo number_format($tot_po_qnty, 2); ?></th><th><? echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
    </tfoot>
    </table>


    <Br /><Br />
    
    <table border="1" rules="all" class="rpt_table" width="500">
        <thead>
            <tr>
                <th colspan="2"><? echo date("M d", strtotime($weekstdate)) . "-" . date("d", strtotime($txt_demand_date)); ?></th><th colspan="3">WTD Pending</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="150"> Company Name </th>
                <th width="130">Pending PO Value </th>
                <th width="140">Pending PO Qnty.</th>
                <th>FOB </th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $tot_po_val = 0;
    $tot_po_qnty = 0;
    foreach ($company_order_qnty_week_summ as $company => $cdata) {
        $i++;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td><p><? echo $company_details[$company]; ?></p></td>
                    <td align="right"><? echo number_format($cdata['order_val'], 2);
        $tot_po_val += $cdata['order_val']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 0);
        $tot_po_qnty += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_val'] / $cdata['order_qnty']), 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th colspan="2" align="right">Total</th><th><? echo number_format($tot_po_val, 2); ?></th><th><? echo number_format($tot_po_qnty, 2); ?></th><th><? echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
    </tfoot>
    </table>

    <Br /><Br />
    <table border="1" rules="all" class="rpt_table" width="500">
        <thead>
            <tr>
                <th colspan="2"><? echo date("M d", strtotime($month_st_date)) . "-" . date("M d", strtotime($txt_demand_date)); ?></th><th colspan="3">MTD Pending</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="150"> Company Name </th>
                <th width="130">Pending PO Value </th>
                <th width="140">Pending PO Qnty.</th>
                <th>FOB </th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $tot_po_val = 0;
    $tot_po_qnty = 0;
    foreach ($company_order_qnty_month_summ as $company => $cdata) {
        $i++;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td><p><? echo $company_details[$company]; ?></p></td>
                    <td align="right"><? echo number_format($cdata['order_val'], 2);
        $tot_po_val += $cdata['order_val']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 0);
        $tot_po_qnty += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_val'] / $cdata['order_qnty']), 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th colspan="2" align="right">Total</th><th><? echo number_format($tot_po_val, 2); ?></th><th><? echo number_format($tot_po_qnty, 2); ?></th><th><? echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
    </tfoot>
    </table>

    <Br /><Br />
    <table border="1" rules="all" class="rpt_table" width="500">
        <thead>
            <tr>
                <th colspan="2"><? echo date("M d", strtotime($start_date)) . "-" . date("M d", strtotime($txt_demand_date)); ?></th><th colspan="3">YTD Pending</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="150"> Company Name </th>
                <th width="130">Pending PO Value </th>
                <th width="140">Pending PO Qnty.</th>
                <th>FOB </th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $tot_po_val = 0;
    $tot_po_qnty = 0;
    foreach ($company_order_summ as $company => $cdata) {
        $i++;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td><p><? echo $company_details[$company]; ?></p></td>
                    <td align="right"><? echo number_format($cdata['order_val'], 2);
        $tot_po_val += $cdata['order_val']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 0);
        $tot_po_qnty += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_val'] / $cdata['order_qnty']), 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th colspan="2" align="right">Total</th><th><? echo number_format($tot_po_val, 2); ?></th><th><? echo number_format($tot_po_qnty, 2); ?></th><th><? echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
    </tfoot>
    </table>
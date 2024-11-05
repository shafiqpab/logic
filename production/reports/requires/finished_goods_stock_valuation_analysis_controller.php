 <?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	$data=str_replace("'","", $data);
	echo create_drop_down( "cbo_buyer_id", 200, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "",0 );     	 	
	exit();    	 
}

$companyArr = return_library_array("SELECT id,company_name from lib_company","id","company_name"); 
$buyerArr = return_library_array("SELECT id,short_name from lib_buyer","id","short_name"); 
$locationArr = return_library_array("SELECT id,location_name from lib_location","id","location_name"); 
$floorArr = return_library_array("SELECT id,floor_name from lib_prod_floor","id","floor_name"); 
//$lineArr = return_library_array("select id, line_name from lib_sewing_line","id","line_name"); 
$prod_reso_arr=return_library_array( "SELECT id, line_number from prod_resource_mst",'id','line_number');

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id = str_replace("'", "",$cbo_company_id);
	$buyer_id 	= str_replace("'", "",$cbo_buyer_id);
	//  ================== making query condition =========================
	if($buyer_id==0) $buyer_cond=""; else $buyer_cond="and a.buyer_name=$buyer_id";
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and a.production_date between $txt_date_from and $txt_date_to";

	// echo $txt_date_to;die();
	$date_from = str_replace("'", "", $txt_date_from);
	$date_to = str_replace("'", "", $txt_date_to);

    $constant_date = "01-JUL-2020";

    if(strtotime($date_from) < strtotime($constant_date))
    {
        echo "<div style='text-align:center;font-weight:bold;color:red;'>Start date not less than 01 July,2020.</div>";
        disconnect($con);
        die();
    }


	function getMonthsInRange($startDate, $endDate) 
	{
		$months = array();
		while (strtotime($startDate) <= strtotime($endDate)) 
		{
		    // $months[] = array('year' => date('Y', strtotime($startDate)), 'month' => date('m', strtotime($startDate)), );
		    $months[strtoupper(date('M-Y', strtotime($startDate)))] = strtoupper(date('M-Y', strtotime($startDate)));
		    $startDate = date('01 M Y', strtotime($startDate.'+ 1 month')); // Set date to 1 so that new month is returned as the month changes.		    
		}

		return $months;
	}
	$month_range_arr = getMonthsInRange($date_from,$date_to);
	// echo "<pre>";print_r($month_range_arr);die();
	$cur_year = date("Y");
	$prev_year = date("Y",strtotime("-1 year",strtotime ($date_from)));
	$last_date = date('t-M-Y',strtotime ("-1 month",strtotime ($date_from)));
	$cur_month = date("m",strtotime($date_from));
	$cur_month2 = date("m",strtotime($date_to));
	// if($cur_month=='06'){ $last_date = $date_to;}
	if($cur_month <'07' )
	{
		$prev_date = date('d-M-Y',strtotime($prev_year.'-07-01'));
	}
	else
	{
		$prev_date = date('d-M-Y',strtotime($cur_year.'-07-01'));
	}
	// echo $prev_date;
    $prev_date = "01-Jul-2020";
    $tot_month_range_arr = getMonthsInRange($prev_date,$last_date);
    $month_range_days_arr = getMonthsInRange($prev_date,$date_to);
    $cur_month_range_arr = getMonthsInRange($date_from,$date_to);
    // echo "<pre>";print_r($cur_month_range_arr);die();

	/* ===================================================================================================/
    / 												get poly qty									 	  /
    /==================================================================================================== */
    $sql = "SELECT a.BUYER_NAME,b.id as PO_ID,(b.unit_price / a.total_set_qnty) AS UNIT_PRICE,
    sum( case when c.production_date between '$date_from' and '$date_to' then d.production_qnty else 0 end) as CUR_PROD_QTY,
    sum( case when c.production_date between '$prev_date' and '$last_date' then d.production_qnty else 0 end) as PREV_PROD_QTY,to_char(c.production_date,'MON-YYYY') as MON_YEAR 
    from wo_po_details_master a,wo_po_break_down b,pro_garments_production_mst c,pro_garments_production_dtls d,wo_po_color_size_breakdown e 
    where c.serving_company in($company_id) and c.id=d.mst_id and e.id=d.color_size_break_down_id and a.id=e.job_id and b.id=e.po_break_down_id and a.id=b.job_id and b.id=c.po_break_down_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and c.production_quantity is not null and c.production_date between '$prev_date' and '$date_to' and c.production_type=11 $buyer_cond and e.status_active=1 and e.is_deleted=0 and d.status_active=1 group by a.buyer_name,c.production_date,b.unit_price,b.id,a.total_set_qnty order by c.production_date";
    // echo $sql;die();
    $sql_res = sql_select($sql);

    if(count($sql_res)<1)
    {
        echo "<div style='text-align:center;font-weight:bold;color:red;'>Data not found. Please try again.</div>";
        disconnect($con);
        die();
    }
    $qty_array = array();
    $po_qty_array = array();
    $buyer_array = array();
    $prev_stock_array = array();
    foreach ($sql_res as $val) 
    {
        $qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['cur_poly_qty'] += $val['CUR_PROD_QTY'];
    	$qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['cur_poly_val'] += $val['CUR_PROD_QTY']*$val['UNIT_PRICE'];
        $qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['prev_poly_qty'] += $val['PREV_PROD_QTY'];
        $po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['cur_po_poly_qty'] += $val['CUR_PROD_QTY'];
        $po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['prev_po_poly_qty'] += $val['PREV_PROD_QTY'];
    	$po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['unit_price'] = $val['UNIT_PRICE'];
    	$buyer_array[$val['BUYER_NAME']] = $val['BUYER_NAME'];


        $prev_stock_array[$val['BUYER_NAME']][strtoupper(date('M-Y',strtotime($last_date)))]['prev_poly_qty'] += $val['PREV_PROD_QTY'];
        $prev_stock_array[$val['BUYER_NAME']][strtoupper(date('M-Y',strtotime($last_date)))]['prev_poly_val'] += $val['PREV_PROD_QTY']*$val['UNIT_PRICE'];
    }
    // echo "<pre>";print_r($prev_stock_array);die();
    /* ===================================================================================================/
    / 												get shipment qty								 	  /
    /==================================================================================================== */
    $buyer_cond_ex = str_replace("a.buyer_name", "c.buyer_name", $buyer_cond);
    $sql = "SELECT c.BUYER_NAME,(d.unit_price / c.total_set_qnty) AS UNIT_PRICE,d.id as PO_ID,sum( case when b.ex_factory_date between '$date_from' and '$date_to' then b.ex_factory_qnty else 0 end) as CUR_EX_QTY,sum( case when b.ex_factory_date between '$prev_date' and '$last_date' then b.ex_factory_qnty else 0 end) as PREV_EX_QTY,to_char(b.ex_factory_date,'MON-YYYY') as MON_YEAR from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,wo_po_details_master c,wo_po_break_down d where c.id=d.job_id and d.id=b.po_break_down_id and a.delivery_company_id in($company_id) $buyer_cond_ex and a.id=b.delivery_mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.ex_factory_qnty is not null and b.ex_factory_date between '$prev_date' and '$date_to' group by c.buyer_name,d.unit_price,d.id,b.ex_factory_date,c.total_set_qnty";
    // echo $sql;
    $sql_res = sql_select($sql);
    $ex_qty_array = array();
    // $ex_po_qty_array = array();
    foreach ($sql_res as $val) 
    {
        $ex_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['cur_ex_qty'] += $val['CUR_EX_QTY'];
    	$ex_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['prev_ex_qty'] += $val['PREV_EX_QTY'];
        
        $ex_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['cur_ex_val'] += ($val['CUR_EX_QTY']*$val['UNIT_PRICE']);
        // echo $val['CUR_EX_QTY']."*".$val['UNIT_PRICE']."==".$val['CUR_EX_QTY']*$val['UNIT_PRICE']."<br>";
        
        $ex_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['prev_ex_val'] += $val['PREV_EX_QTY']*$val['UNIT_PRICE'];

        $po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['cur_po_ex_qty'] += $val['CUR_EX_QTY'];
        $po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['prev_po_ex_qty'] += $val['PREV_EX_QTY'];
        $po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['unit_price'] = $val['UNIT_PRICE'];
    	$buyer_array[$val['BUYER_NAME']] = $val['BUYER_NAME'];

        $prev_stock_array[$val['BUYER_NAME']][strtoupper(date('M-Y',strtotime($last_date)))]['prev_ex_qty'] += $val['PREV_EX_QTY'];
        $prev_stock_array[$val['BUYER_NAME']][strtoupper(date('M-Y',strtotime($last_date)))]['prev_ex_val'] += $val['PREV_EX_QTY']*$val['UNIT_PRICE'];
    }
    // echo "<pre>";print_r($ex_qty_array);die();
    /* ===================================================================================================/
    / 											get leftover rcv qty								 	  /
    /==================================================================================================== */
    $buyer_cond_left = str_replace("a.buyer_name", "c.buyer_name", $buyer_cond);
    $sql = "SELECT c.BUYER_NAME,(d.unit_price / c.total_set_qnty) AS UNIT_PRICE,d.id as PO_ID,sum( case when a.leftover_date between '$date_from' and '$date_to' then b.total_left_over_receive else 0 end) as CUR_LFT_QTY,sum( case when a.leftover_date between '$prev_date' and '$last_date' then b.total_left_over_receive else 0 end) as PREV_LFT_QTY,to_char(a.leftover_date,'MON-YYYY') as MON_YEAR from pro_leftover_gmts_rcv_mst a,pro_leftover_gmts_rcv_dtls b,wo_po_details_master c,wo_po_break_down d where a.working_company_id in($company_id) $buyer_cond_left and a.id=b.mst_id and b.po_break_down_id=d.id and c.id=d.job_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.total_left_over_receive is not null and a.leftover_date between '$prev_date' and '$date_to' and a.goods_type=1 group by c.buyer_name,d.unit_price,d.id,a.leftover_date,c.total_set_qnty";
    // echo $sql;
    $sql_res = sql_select($sql);
    $leftover_qty_array = array();
    // $leftover_po_qty_array = array();
    foreach ($sql_res as $val) 
    {
        $leftover_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['cur_lft_qty'] += $val['CUR_LFT_QTY'];
    	$leftover_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['cur_lft_val'] += $val['CUR_LFT_QTY']*$val['UNIT_PRICE'];
    	$leftover_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']]['prev_lft_qty'] += $val['PREV_LFT_QTY'];
        $po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['cur_po_left_qty'] += $val['CUR_LFT_QTY'];
        $po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['prev_po_left_qty'] += $val['PREV_LFT_QTY'];
        $po_qty_array[$val['BUYER_NAME']][$val['MON_YEAR']][$val['PO_ID']]['unit_price'] = $val['UNIT_PRICE'];
    	$buyer_array[$val['BUYER_NAME']] = $val['BUYER_NAME'];

        $prev_stock_array[$val['BUYER_NAME']][strtoupper(date('M-Y',strtotime($last_date)))]['prev_lft_qty'] += $val['PREV_LFT_QTY'];
        $prev_stock_array[$val['BUYER_NAME']][strtoupper(date('M-Y',strtotime($last_date)))]['prev_lft_val'] += $val['PREV_LFT_QTY']*$val['UNIT_PRICE'];
    }

     // echo "<pre>";print_r($prev_stock_array);die();

    $month_wise_tot_days = array();
    foreach ($month_range_days_arr as $mon_key => $val) 
    {
        $month_wise_tot_days[$mon_key] = date('t',strtotime($mon_key));
    }
    // echo "<pre>";print_r($po_qty_array);die();

     // ======================= calculate previous stock and stock value ========================
    $month_wise_stock_arr = array();
    foreach ($po_qty_array as $buyer_id => $buyer_data) 
    {
        foreach ($buyer_data as $month_id => $month_data) 
        {
            foreach ($month_data as $po_id => $row) 
            {
                $unit_price = $row['unit_price'];
                $prev_po_poly_qty = $row['prev_po_poly_qty'];
                $cur_po_poly_qty = $row['cur_po_poly_qty'];

                $cur_po_ex_qty = $row['cur_po_ex_qty'];
                $prev_po_ex_qty = $row['prev_po_ex_qty'];

                $cur_po_left_qty = $row['cur_po_left_qty'];
                $prev_po_left_qty = $row['prev_po_left_qty'];

                $month_wise_stock_arr[$buyer_id][$month_id]['stock'] += $cur_po_poly_qty - ($cur_po_ex_qty+$cur_po_left_qty);
                $month_wise_stock_arr[$buyer_id][$month_id]['stock_val'] += ($cur_po_poly_qty - ($cur_po_ex_qty+$cur_po_left_qty))*$unit_price;                                        

            }
        }
    }

    // echo "<pre>";print_r($month_wise_stock_arr);die();  
          

	$colspan = 7*count($month_range_arr);
	$tbl_width = 100+(count($month_range_arr)*700);
	ob_start();
	?>
    <fieldset style="width:<? echo $tbl_width+20;?>px">
        <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
            	<td colspan="<? echo $colspan;?>" align="center"><strong>Finished Goods Stock Valuation Analysis Report</strong></td> 
            </tr>
            <tr class="form_caption">
            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo $companyArr[$company_id]; ?></strong></td> 
            </tr>
            <!-- <tr class="form_caption">
            	<td colspan="<? echo $colspan;?>" align="center"><strong><? echo "Date:  ".change_date_format( str_replace("'","",trim($txt_date_from)) )." To ".change_date_format( str_replace("'","",trim($txt_date_to)) ); ?></strong></td> 
            </tr> -->
        </table>
        <table id="table_header" class="rpt_table" width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
                <tr height="30">
                    <th rowspan="2" width="100">Buyer</th>
                    <? foreach ($month_range_arr as $mon_key => $val) 
                    {
                    	?>
                    	<th colspan="7" width=""><? echo date('F',strtotime($val));?></th>
                    	<?
                    }
                    ?>
                </tr>
                <tr>
                	<? foreach ($month_range_arr as $mon_key => $val) 
                    {
                    	?>
                    	<th width="100">Monthly Poly Qty</th>
                    	<th width="100">Monthly Shipment Qty</th>
                    	<th width="100">Monthly Shipment Value</th>
                    	<th width="100" title="[{(Poly of Individual Days + Previous Inhand Total) - (Shipment of individual days + Individual days leftover Receive Qty)} / {( number of days)}]">Monthly Avg FG Stock</th>
                    	<th width="100">Monthly Avg FG Value</th>
                    	<th width="100">Total Prev Stock</th>
                    	<th width="100">Total Prev Value</th>
                    	<?
                    }
                    ?>
                </tr>
            </thead>
            <tbody>
            	<? 
            		// print_r($month_wise_days);
                    $i = 1;
            		$grnd_total_array = array();
            		foreach ($buyer_array as $buyer_key => $buyer_val) 
                    {
                        $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF" ;
                    	?>
                    	<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
	            			<td><? echo $buyerArr[$buyer_key];?></td>
	            			<?
                            $prev_poly_qty = 0;
                            $prev_ex_qty = 0;
                            $prev_ex_val = 0;
                            $prev_lft_qty = 0;
                            $prev_avg_stock = 0;
                            $prev_avg_stock_val = 0;
                            $cur_poly_qty = 0;
                            $cur_poly_val = 0;
                            $cur_ex_qty = 0;
                            $cur_ex_val = 0;
                            $cur_lft_qty = 0;
                            $cur_lft_val = 0;
                            $prev_inhand_tot = 0;
                            $prev_stock_val = 0;
                            $avg_fg_stock = 0;                          
                            $avg_fg_stock_val = 0;                          
                            $curr_stock_qty2 = 0;
                            $curr_stock_val2 = 0;
                            $pre_poly_stock_qty = 0;
                            $pre_poly_stock_val = 0;
                            $prev_in_hand = 0;
                            $prev_in_hand_val = 0;
                            $prev_lft_val = 0;
                            $curr_stock_qty = 0;
                            $curr_stock_val = 0;
                            $m=1;
	            			foreach ($month_range_arr as $mon_key => $val) 
	            			{
                                $prev_month = strtoupper(date('M-Y',strtotime ("-1 month",strtotime ($mon_key))));

                                // previous stock qty and val before start date
                                $prev_poly_qty = $prev_stock_array[$buyer_key][$prev_month]['prev_poly_qty'];
                                $prev_poly_val = $prev_stock_array[$buyer_key][$prev_month]['prev_poly_val'];
                                $prev_ex_qty = $prev_stock_array[$buyer_key][$prev_month]['prev_ex_qty'];
                                $prev_ex_val = $prev_stock_array[$buyer_key][$prev_month]['prev_ex_val'];
                                $prev_lft_qty = $prev_stock_array[$buyer_key][$prev_month]['prev_lft_qty'];
                                $prev_lft_val = $prev_stock_array[$buyer_key][$prev_month]['prev_lft_val'];

                                // previous month stock qty and val
                                $curr_stock_qty = $month_wise_stock_arr[$buyer_key][$prev_month]['stock']+$curr_stock_qty2;
                                $curr_stock_val = $month_wise_stock_arr[$buyer_key][$prev_month]['stock_val']+$curr_stock_val2;

                                // echo "$mon_key==".$month_wise_stock_arr[$buyer_key][$prev_month]['stock']."+".$curr_stock_qty2."<br>";

                                $curr_stock_qty2 = $curr_stock_qty;
                                $curr_stock_val2 = $curr_stock_val;
                               
                                $cur_poly_qty = $qty_array[$buyer_key][$mon_key]['cur_poly_qty'];
                                $cur_poly_val = $qty_array[$buyer_key][$mon_key]['cur_poly_val'];
                                $cur_ex_qty = $ex_qty_array[$buyer_key][$mon_key]['cur_ex_qty'];
                                $cur_ex_val = $ex_qty_array[$buyer_key][$mon_key]['cur_ex_val'];
                                $cur_lft_qty = $leftover_qty_array[$buyer_key][$mon_key]['cur_lft_qty'];
                                $cur_lft_val = $leftover_qty_array[$buyer_key][$mon_key]['cur_lft_val'];

                                $prev_in_hand += $prev_poly_qty - ($prev_ex_qty+$prev_lft_qty);
                                $prev_in_hand_val += $prev_poly_val - ($prev_ex_val+$prev_lft_val);

                                if($m==1)
                                {
                                    $avg_fg_stock = (($cur_poly_qty + $prev_in_hand) - ($cur_ex_qty + $cur_lft_qty))/30;
                                    $avg_fg_stock_val = (($cur_poly_val + $prev_in_hand_val) - ($cur_ex_val + $cur_lft_val))/30;
                                    $prev_inhand_tot = $prev_in_hand;
                                    $prev_stock_val = $prev_in_hand_val;

                                    $prev_avg_stock = $avg_fg_stock;
                                    $prev_avg_stock_val = $avg_fg_stock_val;
                                }
                                else
                                {

                                    $prev_inhand_tot = $curr_stock_qty+$prev_in_hand;
                                    $prev_stock_val = $curr_stock_val+$prev_in_hand_val;

                                    $avg_fg_stock = (($cur_poly_qty + $prev_inhand_tot) - ($cur_ex_qty + $cur_lft_qty))/30;
                                    $avg_fg_stock_val = (($cur_poly_val + $prev_stock_val) - ($cur_ex_val + $cur_lft_val))/30;
                                    // $avg_fg_stock = (($cur_poly_qty + $curr_stock_qty) - ($cur_ex_qty + $cur_lft_qty))/30;
                                    // $avg_fg_stock_val = (($cur_poly_val + $curr_stock_val) - ($cur_ex_val + $cur_lft_val))/30;
                                }

                                // if($avg_fg_stock=="")
                                // {
                                //     $avg_fg_stock = $prev_avg_stock;
                                //     $avg_fg_stock_val = $prev_avg_stock_val;
                                // }
                                
                                // $pre_stock = 0;
                                // if($m==1){$pre_stock = $prev_in_hand;}else{$pre_stock = $curr_stock_qty+$prev_in_hand;}
                                // echo "$mon_key==((".$cur_poly_qty." + ". $pre_stock.") - (".$cur_ex_qty." + ".$cur_lft_qty."))/30<br>";
                                // echo "$mon_key==((".$cur_poly_val." + ".$prev_in_hand_val.") - (".$cur_ex_val." + ".$cur_lft_val."))/30<br>";
                                
                               
		            			?>
		                    	<td align="right"><? echo number_format($cur_poly_qty,0);?></td>
		                    	<td align="right"><? echo number_format($cur_ex_qty,0);?></td>
		                    	<td align="right"><? echo number_format($cur_ex_val,2);?></td>
		                    	<td align="right" title="[{(Poly of Individual Days + Previous Inhand Total) - (Shipment of individual days + Individual days leftover Receive Qty)} / {( number of days)}]"><? echo number_format($avg_fg_stock,0);?></td>
		                    	<td align="right" title="Based on FOB price"><? echo number_format($avg_fg_stock_val,2);?></td>
		                    	<td align="right" title="Previous total poly - Total shipment"><? echo number_format($prev_inhand_tot,0);?></td>
		                    	<td align="right" title="Stock qty x FOB price of indivisual PO"><? echo number_format($prev_stock_val,2);?></td>
		                    	<?
                                $m++;
		                    	$grnd_total_array[$mon_key]['cur_poly_qty'] += $cur_poly_qty;
		                    	$grnd_total_array[$mon_key]['prev_poly_qty'] += $prev_poly_qty;
		                    	$grnd_total_array[$mon_key]['cur_ex_qty'] += $cur_ex_qty;
                                $grnd_total_array[$mon_key]['prev_ex_qty'] += $prev_ex_qty;
                                $grnd_total_array[$mon_key]['cur_ex_val'] += $cur_ex_val;
                                $grnd_total_array[$mon_key]['prev_ex_val'] += $prev_ex_val;
                                $grnd_total_array[$mon_key]['avg_fg_stock'] += $avg_fg_stock;
                                $grnd_total_array[$mon_key]['prev_tot_stock'] += $prev_inhand_tot;
                                $grnd_total_array[$mon_key]['avg_fg_stock_val'] += $avg_fg_stock_val;
		                    	$grnd_total_array[$mon_key]['prev_stock_val'] += $prev_stock_val;
		                    }
                            $i++;
                            $m++;
		                    ?>
	                	</tr>
	                    <?
                    }
                    ?>
            </tbody>
            <tfoot>
            	<tr>
            		<th>Total</th>
            		<? foreach ($month_range_arr as $mon_key => $val) 
                    {
                    	?>
                    	<th align="right"><? echo number_format($grnd_total_array[$mon_key]['cur_poly_qty'],0);?></th>
                    	<th align="right"><? echo number_format($grnd_total_array[$mon_key]['cur_ex_qty'],0);?></th>
                    	<th align="right"><? echo number_format($grnd_total_array[$mon_key]['cur_ex_val'],2);?></th>
                    	<th align="right"><? echo number_format($grnd_total_array[$mon_key]['avg_fg_stock'],0);?></th>
                    	<th align="right"><? echo number_format($grnd_total_array[$mon_key]['avg_fg_stock_val'],2);?></th>
                    	<th align="right"><? echo number_format($grnd_total_array[$mon_key]['prev_tot_stock'],0);?></th>
                    	<th align="right"><? echo number_format($grnd_total_array[$mon_key]['prev_stock_val'],2);?></th>
                    	<?
                    }
                    ?>
            	</tr>
            </tfoot>
        </table>
    </fieldset>
	<?    
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename,'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data####$filename";
	exit();
		
}

 
 
?>
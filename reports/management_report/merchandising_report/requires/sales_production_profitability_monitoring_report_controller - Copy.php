<?
include('../../../../includes/common.php');

session_start();
extract($_REQUEST);
if ($_SESSION['logic_erp']['user_id'] == "") {
    header("location:login.php");
    die;
}
$date = date('Y-m-d');
$company_details = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
$txt_prod_date = str_replace("'", "", $txt_prod_date);

if ($txt_prod_date == '') {
    $action = "report_generate";
    $txt_prod_date = date("d-m-Y", time());
    $mail = 1;
}


if ($action == "report_generate") {
    $company_id = str_replace("'", "", $cbo_company_id);

    $txt_prod_date = change_date_format($txt_prod_date, 'yyyy-mm-dd', '-', 1);
   $txt_prod_date_cal = change_date_format($txt_prod_date, 'yyyy-mm-dd', '-', 1);


    if (strtotime($txt_prod_date) < strtotime(date("Y", strtotime($txt_prod_date)) . "-06-30"))
        $start_date = (date("Y", strtotime($txt_prod_date)) - 1) . "-07-01";
    else
        $start_date = date("Y", strtotime($txt_prod_date)) . "-07-01";
    $start_date = change_date_format($start_date, 'yyyy-mm-dd', '-', 1);


	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 

    for ($i = 0; $i <= 8; $i++) {
        $cdate = add_date($txt_prod_date, -$i);
        if (date("D", strtotime($cdate)) == "Sat") {
            $weekstdate = change_date_format($cdate, 'yyyy-mm-dd', '-', 1);
            break;
        }
    }
	//echo $weekstdate.',';
    $month_st_date = change_date_format(date("Y-m", strtotime($txt_prod_date)) . "-01", 'yyyy-mm-dd', '-', 1);
	$month_end_date = change_date_format(date("Y-m", strtotime($txt_prod_date)) . "-31", 'yyyy-mm-dd', '-', 1);
//echo $month_st_date;
    //$month_query_cond2 = "and to_char(a.delivery_date ,'YYYY-MM-DD') like '$month_query'";
	//echo $month_st_date;
	$exchange_rate=set_conversion_rate( 2, $month_st_date );
	
	 $com_cond_cm = "";
    if ($company_id > 0)
		$com_cond_cm = " and a.company_id=$company_id";
		
		 $sql_fin_cm="select a.company_id,a.applying_period_date as cm_date,
		 (CASE WHEN a.applying_period_date='$month_st_date' then a.asking_profit ELSE 0 END) as asking_profit,
		  (CASE WHEN a.applying_period_date='$month_st_date' then a.asking_avg_rate ELSE 0 END) as asking_avg_rate,
		 sum(CASE WHEN a.applying_period_to_date='$month_end_date' then a.monthly_cm_expense ELSE 0 END) as tot_mon_cm,
		 sum(CASE WHEN a.applying_period_date between '" . $start_date . "' and '" . $txt_prod_date . "' then a.monthly_cm_expense ELSE 0 END) as ytd_tot_cm_exp
  		 from  lib_standard_cm_entry a  where   a.status_active=1 and a.is_deleted=0 $com_cond_cm group by a.company_id,a.applying_period_date,a.asking_profit,a.asking_avg_rate order by a.applying_period_date";
		$result_cm= sql_select($sql_fin_cm);
	  $tod_day_cal=0;
	foreach( $result_cm as $row)
	{
		 		$period_date=date("d-M-Y", strtotime($row[csf('cm_date')]));
				$ytd_tot_cm_exp=$row[csf('ytd_tot_cm_exp')]/$exchange_rate;
				$tot_mon_cm=$row[csf('tot_mon_cm')]/$exchange_rate;
				$financial_cm_arr2[$row[csf('company_id')]]['exp'] += $ytd_tot_cm_exp;
				$financial_cm_arr[$row[csf('company_id')]]['exp'] +=$tot_mon_cm;
				$financial_cm_arr3[$row[csf('company_id')]][$period_date]['asking_profit']= $row[csf('asking_profit')];
				$financial_cm_arr3[$row[csf('company_id')]][$period_date]['asking_avg_rate']= $row[csf('asking_avg_rate')];
				
	}
	//print_r($financial_cm_arr3);
	$com_cond_cal = ""; 
    if ($company_id > 0)
        $com_cond_cal = " and a.comapny_id=$company_id";
  $sql_capacity="select a.comapny_id,count(b.date_calc) as tot_rows,b.date_calc, sum(b.capacity_pcs) as  tot_capacity_pcs,
 sum(CASE WHEN b.date_calc='$txt_prod_date' then b.capacity_pcs ELSE 0 END) as today_qty,
  sum(CASE WHEN b.date_calc>='$weekstdate' then b.capacity_pcs ELSE 0 END) as week_qty,
  sum(CASE WHEN b.date_calc>='$month_st_date' then b.capacity_pcs ELSE 0 END) as mon_qty
  from lib_capacity_calc_mst a , lib_capacity_calc_dtls b where a.id=b.mst_id   and b.date_calc between '" . $start_date . "' and '" . $txt_prod_date . "' and b.day_status=1 and a.status_active=1 and a.is_deleted=0 $com_cond_cal group by a.comapny_id,b.date_calc";
	$result_capacity= sql_select($sql_capacity);
	// $tod_day_cal=0;
	foreach( $result_capacity as $row)
	{
		  	$tod_day_cal=count($row[csf('date_calc')]);
		     if (date("Y-m-d", strtotime($row[csf('date_calc')])) == date("Y-m-d", strtotime($txt_prod_date)))
			 {
				$capacity_cal_arr[$row[csf('comapny_id')]]['pcs'] += $row[csf('today_qty')];
				$capacity_cal_arr[$row[csf('comapny_id')]]['days'] = $row[csf('working_day')];
			 }
			 if (date("Y-m-d", strtotime($row[csf('date_calc')])) >= date("Y-m-d", strtotime($weekstdate))) 
			 {
		 		$capacity_cal_arr2[$row[csf('comapny_id')]]['pcs'] += $row[csf('week_qty')];
				$capacity_cal_arr2[$row[csf('comapny_id')]]['days'] += $tod_day_cal;
			 }
			 if (date("Y-m-d", strtotime($row[csf('date_calc')])) >= date("Y-m-d", strtotime($month_st_date))) 
			 {
		 		$capacity_cal_arr3[$row[csf('comapny_id')]]['pcs'] += $row[csf('mon_qty')];
				$capacity_cal_arr3[$row[csf('comapny_id')]]['days'] += $tod_day_cal;
			 }
		 		$capacity_cal_arr4[$row[csf('comapny_id')]]['pcs'] += $row[csf('tot_capacity_pcs')];
				$capacity_cal_arr4[$row[csf('comapny_id')]]['days'] += count($row[csf('date_calc')]);
	}
	//echo $weekstdate;
	$sql_work="select a.comapny_id,b.date_calc, c.month_id,
 sum(CASE WHEN b.date_calc='$txt_prod_date' then c.working_day ELSE 0 END) as working_day,
  sum(CASE WHEN b.date_calc between '" . $start_date . "' and '" . $txt_prod_date . "' then c.working_day ELSE 0 END) as ytd_working_day
  from lib_capacity_calc_mst a , lib_capacity_calc_dtls b,lib_capacity_year_dtls c where a.id=b.mst_id  and a.id=c.mst_id and b.mst_id=c.mst_id   and b.date_calc between '" . $start_date . "' and '" . $txt_prod_date . "' and b.day_status=1 and a.status_active=1 and a.is_deleted=0 $com_cond_cal group by a.comapny_id,b.date_calc,c.month_id,c.working_day";
	$result_work= sql_select($sql_work);
	// $tod_day_cal=0;
	foreach( $result_work as $row)
	{
				$capacity_cal_arr_days[$row[csf('comapny_id')]][$row[csf('month_id')]]['days'] += $row[csf('working_day')];
				$capacity_cal_arr_days2[$row[csf('comapny_id')]][$row[csf('month_id')]]['days'] += $row[csf('ytd_working_day')];
	}
	//print_r( $capacity_cal_arr_days);
	  $com_cond_out = "";
    if ($company_id > 0)
        $com_cond_out = " and a.company_id=$company_id";
	$sql_output= "SELECT b.id,a.company_id as company_name,sum(a.production_quantity) as prod_qty,a.production_date,c.job_no,c.total_set_qnty ,(b.unit_price) as order_rate
FROM pro_garments_production_mst a,  wo_po_break_down b,wo_po_details_master c WHERE b.job_no_mst=c.job_no and b.id=a.po_break_down_id and a.production_type=5  and a.production_date between '" . $start_date . "' and '" . $txt_prod_date . "' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_cond_out group by b.id,a.company_id,a.production_date,c.job_no,c.total_set_qnty,b.unit_price order by a.company_id  ";
  $result_out = sql_select($sql_output);
  $sewing_po_arr=array();
  foreach($result_out as $row) //Sewing Input
  {
	
          $costing_per=$costing_per_arr[$row[csf('job_no')]];
		 if($costing_per==1) $dzn_qnty=12;
		 else if($costing_per==3) $dzn_qnty=12*2;
		 else if($costing_per==4) $dzn_qnty=12*3;
		 else if($costing_per==5) $dzn_qnty=12*4;
		 else $dzn_qnty=1;
		 $sewing_qnty = $row[csf('prod_qty')];
		  $dzn_qnty=$dzn_qnty*$row[csf('total_set_qnty')];
		 $cm_sewing_val=$sewing_qnty*($row[csf('order_rate')]);
		 $cm_value_in=($tot_cost_arr[$row[csf('job_no')]]/$dzn_qnty)*$sewing_qnty;
		 
		  $sewing_po_arr[$row[csf('id')]] = $row[csf('id')];
        
        if (date("Y-m-d", strtotime($row[csf('production_date')])) == date("Y-m-d", strtotime($txt_prod_date))) {

            $company_order_qnty_day2[$row[csf('company_name')]]['order_qnty'] += $sewing_qnty;
			$company_order_qnty_day2[$row[csf('company_name')]]['order_value'] += $sewing_qnty*$row[csf('order_rate')];
            $company_order_qnty_day2[$row[csf('company_name')]]['order_rate'] = $row[csf('order_rate')];
            $company_order_qnty_day2[$row[csf('company_name')]]['company_name'] = $row[csf('company_name')];
			$company_order_qnty_day2[$row[csf('company_name')]]['cm_value'] += $cm_value_in;
			//echo "DDF";
        }
		//echo date("Y-m-d", strtotime($weekstdate));
        if (date("Y-m-d", strtotime($row[csf('production_date')])) >= date("Y-m-d", strtotime($weekstdate))) {
			//echo date("Y-m-d", strtotime($weekstdate));
            $company_order_qnty_week2[$row[csf('company_name')]]['order_qnty'] += $sewing_qnty;
			$company_order_qnty_week2[$row[csf('company_name')]]['order_value'] += $sewing_qnty*$row[csf('order_rate')];
            $company_order_qnty_week2[$row[csf('company_name')]]['order_rate'] = $row[csf('order_rate')];
            $company_order_qnty_week2[$row[csf('company_name')]]['company_name'] = $row[csf('company_name')];
			 $company_order_qnty_week2[$row[csf('company_name')]]['cm_value'] += $cm_value_in;
        }
        if (date("Y-m-d", strtotime($row[csf('production_date')])) >= date("Y-m-d", strtotime($month_st_date))) {
			//echo date("Y-m-d", strtotime($month_st_date));
            $company_order_qnty_month2[$row[csf('company_name')]]['order_qnty'] += $sewing_qnty;
			$company_order_qnty_month2[$row[csf('company_name')]]['order_value'] += $sewing_qnty*$row[csf('order_rate')];
            $company_order_qnty_month2[$row[csf('company_name')]]['order_rate'] = $row[csf('order_rate')];
            $company_order_qnty_month2[$row[csf('company_name')]]['company_name'] = $row[csf('company_name')];
			$company_order_qnty_month2[$row[csf('company_name')]]['cm_value'] += $cm_value_in;
        }
       // $sewing_qnty = $sewing_qnty;
        $company_order_qnty2[$row[csf('company_name')]]['order_qnty'] += $sewing_qnty;
		$company_order_qnty2[$row[csf('company_name')]]['order_value'] += $sewing_qnty*$row[csf('order_rate')];
       $company_order_qnty2[$row[csf('company_name')]]['order_rate'] = $row[csf('order_rate')];
        $company_order_qnty2[$row[csf('company_name')]]['company_name'] = $row[csf('company_name')]; 
		$company_order_qnty2[$row[csf('company_name')]]['cm_value'] += $cm_value_in;  
		
  }
 // print_r($company_order_qnty2);
  
/*  if (count($sewing_po_arr) > 0)
        $sql_summary_sewing = return_library_array("SELECT po_break_down_id,sum(production_quantity) AS prod_qty from pro_garments_production_mst  where po_break_down_id in (" . implode(",", $sewing_po_arr) . ")  and production_type=5  and status_active=1 and is_deleted=0 group by po_break_down_id", 'po_break_down_id', 'prod_qty');*/
//print_r( $sql_summary_sewing);
    /*foreach ($company_order_qnty2 as $poid => $podtls) 
    {

       $podtls['order_qnty'] = $podtls['order_qnty'];// - $sql_summary_ex_factory[$poid];
		$sewing_value=$podtls['order_qnty']*$podtls['order_rate'];
        if ($company_order_qnty_day2[$poid]['order_qnty'] != '') 
        {
         // echo $podtls['order_qnty'].', ';
		    $company_order_day_summ2[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_day_summ2[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
			$company_order_day_summ2[$podtls['company_name']]['cm_value'] += $podtls['cm_value'];//*$sewing_value;
        }
        if ($company_order_qnty_week2[$poid]['order_qnty'] != '') 
        {
            $company_order_qnty_week_summ2[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_qnty_week_summ2[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
			 $company_order_qnty_week_summ2[$podtls['company_name']]['cm_value'] += $podtls['cm_value'];//*$sewing_value;
        }
        if ($company_order_qnty_month2[$poid]['order_qnty'] != '') 
        {
			//echo $podtls['order_qnty'];
            $company_order_qnty_month_summ2[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_qnty_month_summ2[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
			$company_order_qnty_month_summ2[$podtls['company_name']]['cm_value'] += $podtls['cm_value'];//*$sewing_value;;
        }

        $company_order_summ2[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
       $company_order_summ2[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
		$company_order_summ2[$podtls['company_name']]['cm_value'] += $podtls['cm_value'];//*$sewing_value;
    }*/
	//print_r( $company_order_summ2);
/*$sql = "SELECT distinct c.id,a.company_id as company_name,b.shiping_status,b.ex_factory_date,
  (CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END)-(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,d.total_set_qnty ,(c.unit_price/d.total_set_qnty) as order_rate
FROM pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,  wo_po_break_down c,wo_po_details_master d WHERE a.id=b.delivery_mst_id and c.job_no_mst=d.job_no and c.id=b.po_break_down_id  and b.ex_factory_date between '" . $start_date . "' and '" . $txt_prod_date . "'  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $com_cond order by a.company_id"; */
    $com_cond = "";
    if ($company_id > 0)
        $com_cond = " and D.company_name=$company_id";
 $sql = "SELECT   C.ID,D.company_name as company_name,B.ex_factory_date,
  sum(CASE WHEN B.ENTRY_FORM!=85 THEN B.EX_FACTORY_QNTY ELSE 0 END)-sum(CASE WHEN B.ENTRY_FORM=85 THEN B.EX_FACTORY_QNTY ELSE 0 END) as EX_FACTORY_QNTY,D.TOTAL_SET_QNTY ,(C.UNIT_PRICE/D.TOTAL_SET_QNTY) as ORDER_RATE
FROM PRO_EX_FACTORY_MST B,  WO_PO_BREAK_DOWN C,WO_PO_DETAILS_MASTER D WHERE  C.JOB_NO_MST=D.JOB_NO and C.ID=B.PO_BREAK_DOWN_ID  and B.ex_factory_date between '" . $start_date . "' and '" . $txt_prod_date . "'  and b.status_active=1 and b.is_deleted=0 $com_cond group by C.id,D.company_name,B.ex_factory_date,C.unit_price,D.total_set_qnty  order by D.company_name"; //and a.job_no_prefix_num like '$txt_job_number' 
    $result = sql_select($sql);
    //$buyer_order_quantity=0; $buyer_order_val=0;$tot_buyer_order_quantity=0;
	//$company_order_qnty_week=array();
    foreach ($result as $row)
	 {
       
       		 $ex_factory_qnty = $row[csf('ex_factory_qnty')];
        if (date("Y-m-d", strtotime($row[csf('ex_factory_date')])) == date("Y-m-d", strtotime($txt_prod_date))) {

            $company_order_qnty_day[$row[csf('company_name')]]['order_qnty'] += $ex_factory_qnty;
            $company_order_qnty_day[$row[csf('company_name')]]['order_rate'] = $row[csf('order_rate')];
			$company_order_qnty_day[$row[csf('company_name')]]['order_value'] += $ex_factory_qnty*$row[csf('order_rate')];
            $company_order_qnty_day[$row[csf('company_name')]]['company_name'] = $row[csf('company_name')];
			
			
			//echo "DDF";
        }
		//echo date("Y-m-d", strtotime($weekstdate));
        if (date("Y-m-d", strtotime($row[csf('ex_factory_date')])) >= date("Y-m-d", strtotime($weekstdate))) {
			//echo date("Y-m-d", strtotime($weekstdate)).', ';
            $company_order_qnty_week[$row[csf('company_name')]]['order_qnty'] += $ex_factory_qnty;
			$company_order_qnty_week[$row[csf('company_name')]]['order_value'] += $ex_factory_qnty*$row[csf('order_rate')];
            $company_order_qnty_week[$row[csf('company_name')]]['order_rate'] = $row[csf('order_rate')];
            $company_order_qnty_week[$row[csf('company_name')]]['company_name'] = $row[csf('company_name')];
			
        }
        if (date("Y-m-d", strtotime($row[csf('ex_factory_date')])) >= date("Y-m-d", strtotime($month_st_date))) {
			//echo date("Y-m-d", strtotime($month_st_date));
            $company_order_qnty_month[$row[csf('company_name')]]['order_qnty'] += $ex_factory_qnty;
			$company_order_qnty_month[$row[csf('company_name')]]['order_value'] += $ex_factory_qnty*$row[csf('order_rate')];
            $company_order_qnty_month[$row[csf('company_name')]]['order_rate'] = $row[csf('order_rate')];
            $company_order_qnty_month[$row[csf('company_name')]]['company_name'] = $row[csf('company_name')];
			
        }
       // $ex_factory_qnty = $ex_factory_qnty;
        $company_order_qnty[$row[csf('company_name')]]['order_qnty'] += $ex_factory_qnty;
        $company_order_qnty[$row[csf('company_name')]]['order_value'] += $ex_factory_qnty*$row[csf('order_rate')];
		$company_order_qnty[$row[csf('company_name')]]['order_rate'] = $row[csf('order_rate')];
        $company_order_qnty[$row[csf('company_name')]]['company_name'] = $row[csf('company_name')];
    }
	//print_r( $company_order_qnty);
 //print_r($company_order_qnty_weekff);

  /*  if (count($partial_ex_factory) > 0)
        $sql_summary_ex_factory = return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where po_break_down_id in (" . implode(",", $partial_ex_factory) . ") and status_active=1 and is_deleted=0 group by po_break_down_id", 'po_break_down_id', 'ex_factory_qnty');*/

    /*foreach ($company_order_qnty as $poid => $podtls) 
    {

       $podtls['order_qnty'] = $podtls['order_qnty'];// - $sql_summary_ex_factory[$poid];

        if ($company_order_qnty_day[$poid]['order_qnty'] != '') 
        {
           
		    $company_order_day_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_day_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
        }
        if ($company_order_qnty_week[$poid]['order_qnty'] != '') 
        {
           // echo $podtls['order_qnty'].', ';
		   // $company_order_qnty_weekff[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
			$company_order_qnty_week_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_qnty_week_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
        }
        if ($company_order_qnty_month[$poid]['order_qnty'] != '') 
        {
			//echo $podtls['order_qnty'];
            $company_order_qnty_month_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
            $company_order_qnty_month_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
        }

        $company_order_summ[$podtls['company_name']]['order_qnty'] += $podtls['order_qnty'];
        $company_order_summ[$podtls['company_name']]['order_val'] += $podtls['order_qnty'] * $podtls['order_rate'];
    }*/
	//print_r( $company_order_qnty_week_summ);
    ob_start();
    
    ?>
<div style="width:1490px">
<table border="1" rules="all" class="rpt_table" width="1470"style="align:right" >
<tr>
<td  width="500">
	<table border="1" rules="all" class="rpt_table" width="500"style="align:right" >
        <thead>
            <tr>
                <th colspan="2"><?
				 $mon=(date("m", strtotime($txt_prod_date)))*1;
				 $month_id=$mon;
				 $diff_day=datediff('d',date("Y-m-d", strtotime($txt_prod_date)),date("Y-m-d", strtotime($txt_prod_date)));
				 echo date("M-d", strtotime($txt_prod_date)); ?></th>
                <th colspan="5" title="<? echo 'Days: '.$diff_day;?>">Daily Sales</th>
            </tr>
            <tr>
            <th colspan="6"> Ex Factory Value/Qty/Price</th>
            </tr>
            <tr>
                <th width="20">SL</th>
                <th width="120"> Plant </th>
                <th width="100"> Prod. Capacity </th>
                <th width="100"> Value </th>
                <th width="100">Qnty.</th>
                <th>FOB</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_mon_prod_val = 0;
    $tot_exfact_qnty_day = 0;  $tot_prod_val_day = 0;$tot_exfact_val_day = 0;
    foreach ($company_order_qnty_day as $company => $cdata) 
	{
		$asking_avg_rate=$financial_cm_arr3[$company][$month_st_date]['asking_avg_rate'];
		$tot_mon_cap_qty_day=($capacity_cal_arr[$company]['pcs']*$asking_avg_rate);
		
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trdtd1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trdtd1st_<? echo $i; ?><? echo $d; ?>">                  <td><? echo $d; ?></td>
                    <td><p><? echo $company_details[$company]; ?></p></td>
        			<td align="right" title="<? echo 'Asking Avg Rate : '.$asking_avg_rate?>"><? echo number_format($tot_mon_cap_qty_day, 2);$tot_prod_val_day += $tot_mon_cap_qty_day; ?></td>
                    <td align="right"><? echo number_format($cdata['order_value'], 2); $tot_exfact_val_day += $cdata['order_value']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 2); $tot_exfact_qnty_day += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_value'] / $cdata['order_qnty']), 2);$tot_fob += $cdata['order_value'] / $cdata['order_qnty'];  ?></td>
                </tr>
            <? 
			  $d++;
			} ?>
        </tbody>
        <tfoot>
        <th  align="right">Total</th>
        <th>&nbsp;</th>
        <th  align="right"><? echo number_format($tot_prod_val_day, 2); ?></th>
        <th  align="right"><? echo number_format($tot_exfact_val_day, 2); ?></th>
        <th  align="right"><? echo number_format($tot_exfact_qnty_day, 2); ?></th>
        <th  align="right"><? // echo number_format($tot_po_qnty, 2); ?></th>
       
    </tfoot>
    </table>
</td>
<td height="10px">&nbsp; </td>
<td width="420"> 
<table border="1" rules="all" class="rpt_table" width="420"style="align:right" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="5">Daily Production </th>
            </tr>
            <tr>
            <th colspan="6"> Produced Value & CM %</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="120"> Value </th>
                <th width="120">Qty. </th>
                <th width="50">FOB</th>
                <th width="50">Profit%</th>
                <th>CM%</th>
            </tr>
        </thead>
        <tbody>
    <?
	//Daily Production
    $d = 1;
    $i = 0;
    $tot_produced_val = 0;
    $tot_produced_qnty = 0;
	//print_r($company_order_day_summ2);
    foreach ($company_order_qnty_day2 as $company => $cdata)
	 {
        $i++;
		$work_day=$capacity_cal_arr_days[$company][$month_id]['days'];
		$tot_mon_target_opex=$financial_cm_arr[$company]['exp'];
		$tot_target_opex=($tot_mon_target_opex/$work_day)*$diff_day;
		$cm_value=$cdata['cm_value'];
		$profit_cm_dif=$cm_value-$tot_target_opex;
		$profit_percent=($profit_cm_dif/$cdata['order_value'])*100;
		$cm_percent=($cm_value/$cdata['order_value'])*100;
		
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trdtd2st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trdtd2st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td align="right"><? echo number_format($cdata['order_value'], 2);$tot_produced_val += $cdata['order_value']; ?></td>
                     <td align="right"><? echo number_format($cdata['order_qnty'], 2); $tot_produced_qnty += $cdata['order_qnty']; ?></td>
                     <td align="right"><? echo number_format(($cdata['order_value'] / $cdata['order_qnty']), 2); ?></td>
                     <td title="CM Value-Target Opex/Produced Value*100"><p><? echo number_format($profit_percent,2); ?></p></td>
                     <td align="right" title="CM Value/Produced Value*100"><? echo number_format($cm_percent, 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th align="right"></th>
         <th  align="right"><? echo number_format($tot_produced_val, 2); ?></th>
         <th  align="right"><? echo number_format($tot_produced_qnty, 2); ?></th>
         <th align="right"></th>
        <th><? //echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
        <th><? //echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
    </tfoot>
    </table>
</td>
<td height="10px">&nbsp; </td>
<td width="550"> 
<table border="1" rules="all" class="rpt_table" width="550"style="align:right" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="5">Daily Profitability</th>
            </tr>
            <tr>
            <th colspan="5"> OPEX vs Produced CM vs Profit</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="120">Target OPEX </th>
                <th width="100">CM Value</th>
                <th  width="120">Profit to be % Margin</th>
                <th>Profit /Loss on Target OPEX</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_profit_mon_target_opex= 0; $tot_profit_target_opex = 0; $tot_profit_per = 0;
    $tot_profit_target_cm = 0;  $tot_profit_cm_value = 0;$total_profit_per = 0;$total_profit_loss = 0;
    foreach ($company_order_qnty_day2 as $company => $cdata) 
	{
        $i++;
		//echo $company;
		 $work_day=$capacity_cal_arr_days[$company][$month_id]['days'];
		$tot_mon_target_opex=$financial_cm_arr[$company]['exp'];
		$tot_target_opex=($tot_mon_target_opex/$work_day)*$diff_day;
		$cm_value=$cdata['cm_value'];
		$ask_profit=$financial_cm_arr3[$company][$month_st_date]['asking_profit'];//financial_cm_arr3
		$profit_per=($cdata['order_value']*$ask_profit)/100;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trdtd3st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trdtd3st_<? echo $i; ?><? echo $d; ?>">                  <td><? echo $d++; ?></td>
                    <td align="right"><? echo number_format($tot_target_opex,2); $tot_profit_target_opex += $tot_target_opex; ?></td>
                    <td align="right"><? echo number_format($cm_value, 2); $tot_profit_cm_value += $cm_value;?></td>
                    <td align="right" title="Sewing Value*Asking Profit %(  <? echo $ask_profit;?> )"><? echo number_format($profit_per, 2);$total_profit_per += $profit_per; ?></td>
                    <td align="right"  title="CM Value-Target Opex"><p><? $tot_profit_loss_diff=$cm_value-$tot_target_opex;
					if($tot_profit_loss_diff<0)
					{ 
						  $tot_profit_loss_diff_dd="(".number_format(abs($tot_profit_loss_diff),2).")"; 
						  $bg_color_td_d="red";
					}
					else
					{
						$tot_profit_loss_diff_dd=number_format($tot_profit_loss_diff,2);
						  $bg_color_td_d="green";
					}
					echo "<font color='$bg_color_td_d'>$tot_profit_loss_diff_dd</font>";
					//echo $tot_profit_loss_diff_dd;
					 $total_profit_loss += $tot_profit_loss_diff;
					 
					 if($total_profit_loss<0)
						{ 
							  $total_profit_loss_dd="(".number_format(abs($total_profit_loss),2).")"; 
							  $bg_color_td_tot_d="red";
						}
						else
						{
							$total_profit_loss_dd=number_format($total_profit_loss,2);
							$bg_color_td_tot_d="green";
						}
					$tot_sum_dd= "<font color='$bg_color_td_tot_d'>".$total_profit_loss_dd."</font>";



					 ?></p></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th align="right"></th>
        <th align="right"><? echo number_format($tot_profit_target_opex, 2); ?></th>
         <th  align="right"><? echo number_format($tot_profit_cm_value, 2); ?></th>
        <th align="right"><? echo number_format(($total_profit_per), 2); ?></th>
        <th align="right"><? echo $tot_sum_dd; ?></th>
    </tfoot>
    </table>
</td>
</tr>
</table>

    <Br /><Br />
    <!--Week---- wtd-->
    <table border="1" rules="all" class="rpt_table" width="1470"style="align:right" >
<tr>
<td  width="500">
	<table border="1" rules="all" class="rpt_table" width="500"style="align:right" >
        <thead>
            <tr>
                <th colspan="2"><? 
				$diff_week=datediff('d',date("Y-m-d", strtotime($weekstdate)),date("Y-m-d", strtotime($txt_prod_date)));
				echo date("M d", strtotime($weekstdate)) . "-" . date("d", strtotime($txt_prod_date)); ?></th>
                <th colspan="5" title="<? echo 'Days: '.$diff_week;?>">WTD Sales</th>
            </tr>
            <tr>
            <th colspan="7"> Ex Factory Value/Qty/Price</th>
            </tr>
            <tr>
                <th width="20">SL</th>
                <th width="120"> Plant </th>
               
                <th width="100"> Prod. Capacity </th>
                <th width="100"> Value </th>
                <th width="100">Qnty.</th>
                <th>FOB</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_mon_prod_val_week = 0;
    $tot_prod_val_week = 0;  $tot_exfact_val_week = 0;$tot_exfact_qty_week = 0;
    foreach ($company_order_qnty_week as $company => $cdata) {
        $i++;
		$asking_avg_rate=$financial_cm_arr3[$company][$month_st_date]['asking_avg_rate'];
		$tot_mon_cap_qty_week=$capacity_cal_arr2[$company]['pcs']*$asking_avg_rate;
		//$tot_prod_days_week=$capacity_cal_arr[$company]['tot_day'];
		//$tot_prod_cap_qty_week=$tot_mon_cap_qty_week/$tot_prod_days_week;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trwtd1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trwtd1st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td><p><? echo $company_details[$company]; ?></p></td>
                    
        			<td align="right"><? echo number_format($tot_mon_cap_qty_week, 2);$tot_prod_val_week += $tot_mon_cap_qty_week; ?></td>
                    <td align="right"><? echo number_format($cdata['order_value'], 2); $tot_exfact_val_week += $cdata['order_value']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 2); $tot_exfact_qty_week += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_value'] / $cdata['order_qnty']), 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th  align="right">Total</th>
      	<th><? //echo number_format($tot_prod_val_week, 2); ?></th>
        <th><? echo number_format($tot_prod_val_week, 2); ?></th>
        <th><? echo number_format($tot_exfact_val_week, 2); ?></th>
        <th><? echo number_format($tot_exfact_qty_week, 2); ?></th>
        
        <th><? //echo number_format($tot_po_qnty, 2); ?></th>
       
    </tfoot>
    </table>
</td>
<td height="10px">&nbsp; </td>
<td width="420"> 
<table border="1" rules="all" class="rpt_table" width="420"style="align:right" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="5">WTD Production</th>
            </tr>
            <tr>
           	<th colspan="6"> Produced Value & CM %</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="120"> Value </th>
                <th width="120">Qty. </th>
                <th width="50">FOB</th>
                <th width="50">Profit%</th>
                <th>CM%</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_week_val_day = 0;
    $tot_week_qnty_day = 0;
    foreach ($company_order_qnty_week2 as $company => $cdata)
	 {
        $i++;
		
		$work_day=$capacity_cal_arr_days[$company][$month_id]['days'];
		$tot_mon_target_opex=$financial_cm_arr[$company]['exp'];
		$tot_target_opex=($tot_mon_target_opex/$work_day)*$diff_week;
		$cm_value=$cdata['cm_value'];
		$profit_cm_dif=$cm_value-$tot_target_opex;
		$profit_percent=($profit_cm_dif/$cdata['order_value'])*100;
		$cm_percent=($cm_value/$cdata['order_value'])*100;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trwtd2st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trwtd2st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td align="right"><? echo number_format($cdata['order_value'], 2); $tot_week_val_day += $cdata['order_value']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 2); $tot_week_qnty_day += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_value'] / $cdata['order_qnty']), 2); ?></td>
                    <td><p><? echo number_format($profit_percent,2); ?></p></td>
                    <td align="right"><? echo number_format($cm_percent, 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
           <th align="right"></th>
            <th><? echo number_format($tot_week_val_day, 2); ?></th>
        	<th><? echo number_format($tot_week_qnty_day, 2); ?></th>
        	
        	 <th align="right"></th>
        	<th><? //echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
        	<th><? //echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
    </tfoot>
    </table>
</td>
<td height="10px">&nbsp; </td>
<td width="550"> 
<table border="1" rules="all" class="rpt_table" width="550"style="align:right" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="5">WTD Profitability</th>
            </tr>
            <tr>
            <th colspan="6"> OPEX vs Produced CM VS. Profit</th>
            </tr>
            <tr>
                <th width="30">SL</th>
               
                <th width="120">Target OPEX </th>
                <th width="100">CM Value</th>
                <th  width="120">Profit to be % Margin</th>
                <th>Profit /Loss on Target OPEX</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_prod_target_week_val = 0;
    $tot_prod_target_week = 0; $tot_week_prod_cm_value = 0;$total_profit_per_week = 0;
    foreach ($company_order_qnty_week2 as $company => $cdata)
	{
        $i++;
		
		$work_day=$capacity_cal_arr_days[$company][$month_id]['days'];
		$tot_mon_target_opex=$financial_cm_arr[$company]['exp'];
		$tot_target_opex=($tot_mon_target_opex/$work_day)*$diff_week;
		$cm_value=$cdata['cm_value'];
		$ask_profit=$financial_cm_arr3[$company][$month_st_date]['asking_profit'];
		$profit_per=($cdata['order_value']*$ask_profit)/100;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trwtd3st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trwtd3st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                   
                    <td align="right"><? echo number_format($tot_target_opex, 2); $tot_prod_target_week += $tot_target_opex; ?></td>
                    <td align="right"><? echo number_format($cm_value, 2); $tot_week_prod_cm_value += $cm_value; ?></td>
                    <td align="right" title="<? echo $ask_profit;?>"><? echo number_format($profit_per, 2);$total_profit_per_week += $profit_per; ?></td>
                    <td align="right"  title="CM Value-Target Opex"><p><? $tot_profit_loss_diff_w=$cm_value-$tot_target_opex;
					
					if($tot_profit_loss_diff_w<0)
					{ 
						  $tot_profit_loss_diff_ww="(".number_format(abs($tot_profit_loss_diff_w),2).")"; 
						  $bg_color_td_w="red";
					}
					else
					{
						$tot_profit_loss_diff_ww=number_format($tot_profit_loss_diff_w,2);
						 $bg_color_td_w="green";
					}
					//echo $tot_profit_loss_diff_ww;
					echo "<font color='$bg_color_td_w'>$tot_profit_loss_diff_ww</font>";
					 $total_profit_loss_w += $tot_profit_loss_diff_w;
					 
					 if($total_profit_loss_w<0)
						{ 
							  $total_profit_loss_ww="(".number_format(abs($total_profit_loss_w),2).")"; 
							  $bg_color_td_tot_w="red";
						}
						else
						{
							$total_profit_loss_ww=number_format($total_profit_loss_w,2);
							$bg_color_td_tot_w="green";
						}
						$tot_sum_ww= "<font color='$bg_color_td_tot_w'>".$total_profit_loss_ww."</font>";
					  ?></p></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th  align="right"></th>
        
        <th align="right"><? echo number_format($tot_prod_target_week, 2); ?></th>
        <th align="right"><? echo number_format($tot_week_prod_cm_value, 2); ?></th>
        <th align="right"><? echo number_format($total_profit_per_week, 2); ?></th>
        <th align="right"><? echo $tot_sum_ww; ?></th>
    </tfoot>
    </table>
</td>
</tr>
</table>

    <Br /><Br /> 
     <!-- Month Mtd-->
    <table border="1" rules="all" class="rpt_table" width="1470"style="align:right" >
    <tr>
    <td  width="500">
	<table border="1" rules="all" class="rpt_table" width="500"style="align:right" >
        <thead>
            <tr>
                <th colspan="2"><?  
				$diff_mon=datediff('d',date("Y-m-d", strtotime($month_st_date)),date("Y-m-d", strtotime($txt_prod_date)));
				echo date("M d", strtotime($month_st_date)) . "-" . date("M d", strtotime($txt_prod_date)); ?></th>
                <th colspan="5" title="<? echo 'Days: '.$diff_mon;?>">MTD Sales</th>
            </tr>
             <tr>
            <th colspan="7"> Ex Factory Value/Qty/Price</th>
            </tr>
            <tr>
                <th width="20">SL</th>
                <th width="120"> Plant </th>
               
                <th width="100"> Prod. Capacity </th>
                <th width="100"> Value </th>
                <th width="100">Qnty.</th>
                <th>FOB</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
 	 $tot_mon_cap_qty_mon=0;$tot_prod_cap_val_mon=0;
    $tot_exfact_qnty = 0; $tot_exfact_val = 0;
    foreach ($company_order_qnty_month as $company => $cdata) {
        $i++;
		$asking_avg_rate=$financial_cm_arr3[$company][$month_st_date]['asking_avg_rate'];
		$tot_mon_cap_qty_mon=$capacity_cal_arr3[$company]['pcs']*$asking_avg_rate;
		
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trmtd1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trmtd1st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td><p><? echo $company_details[$company]; ?></p></td>
        			<td align="right"><? echo number_format($tot_mon_cap_qty_mon, 2);$tot_prod_cap_qty_mon += $tot_mon_cap_qty_mon; ?></td>
                    <td align="right"><? echo number_format($cdata['order_value'], 2); $tot_exfact_val += $cdata['order_value']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 2); $tot_exfact_qnty += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_value'] / $cdata['order_qnty']), 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th  align="right">Total</th>
        <th align="right"><? //echo number_format($tot_mon_cap_qty_mon, 2); ?></th>
        <th align="right"><? echo number_format($tot_prod_cap_qty_mon, 2); ?></th>
        <th align="right"><? echo number_format(($tot_exfact_val), 2); ?></th>
         <th align="right"><? echo number_format($tot_exfact_qnty, 2); ?></th>
        <th align="right"><? //echo number_format($tot_po_qnty, 2); ?></th>
       
    </tfoot>
    </table>
</td>
<td height="10px">&nbsp; </td>
<td width="420"> 
<table border="1" rules="all" class="rpt_table" width="420"style="align:right" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="5">MTD Production</th>
            </tr>
            <tr>
           		<th colspan="6"> Produced Value & CM %</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="120"> Value </th>
                <th width="120">Qty. </th>
                <th width="50">FOB</th>
                <th width="50">Profit%</th>
                <th>CM%</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_mtd_val_week = 0;
    $tot_mtd_qnty_week = 0;
    foreach ($company_order_qnty_month2 as $company => $cdata) {
        $i++;
		$work_day=$capacity_cal_arr_days[$company][$month_id]['days'];
		$tot_mon_target_opex=$financial_cm_arr[$company]['exp'];
		$tot_target_opex=($tot_mon_target_opex/$work_day)*$diff_mon;
		$cm_value=$cdata['cm_value'];
		$profit_cm_dif=$cm_value-$tot_target_opex;
		$profit_percent=($profit_cm_dif/$cdata['order_value'])*100;
		$cm_percent=($cm_value/$cdata['order_value'])*100;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trmtd2st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trmtd2st_<? echo $i; ?><? echo $d; ?>">
                     <td><? echo $d++; ?></td>
                     <td align="right"><? echo number_format($cdata['order_value'], 2);$tot_mtd_val_week += $cdata['order_value']; ?></td>
                     <td align="right"><? echo number_format($cdata['order_qnty'], 2); $tot_mtd_qnty_week += $cdata['order_qnty']; ?></td>
                     <td align="right"><? echo number_format(($cdata['order_value']/$cdata['order_qnty']), 2); ?></td>
                     <td><p><? echo number_format($profit_percent,2); ?></p></td>
                     <td align="right"><? echo number_format($cm_percent, 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th align="right"></th>
        <th><? echo number_format($tot_mtd_val_week, 2); ?></th>
        <th><? echo number_format($tot_mtd_qnty_week, 2); ?></th>
         <th align="right"></th>
        <th><? //echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
        <th><? //echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
    </tfoot>
    </table>
</td>
<td height="10px">&nbsp; </td>
<td width="550"> 
<table border="1" rules="all" class="rpt_table" width="550"style="align:right" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="5">MTD Profitability</th>
            </tr>
            <tr>
          	  <th colspan="6"> OPEX vs Produced CM vs Profit</th>
            </tr>
            <tr>
                <th width="30">SL</th>
               
                <th width="120">Target OPEX </th>
                <th width="100">CM Value</th>
                <th  width="120">Profit to be % Margin</th>
                <th>Profit /Loss on Target OPEX</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_mtd_produced_val_mon = 0;
    $tot_mtd_produced_cm_mon = 0; $tot_mtd_profit_margin_mon = 0; $tot_mtd_profit_loss_mon = 0; $tot_mtd_profit_per_mon = 0;
    foreach ($company_order_qnty_month2 as $company => $cdata) {
        $i++;
		
			$work_day=$capacity_cal_arr_days[$company][$month_id]['days'];
			$tot_mon_target_opex=$financial_cm_arr[$company]['exp'];
			$tot_target_opex_mtd=($tot_mon_target_opex/$work_day)*$diff_mon;
			$cm_value_profit=$cdata['cm_value'];
			$ask_profit_mtd=$financial_cm_arr3[$company][$month_st_date]['asking_profit'];
			$profit_per_mon_mtd=($cdata['order_value']*$ask_profit_mtd)/100;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trmtd3st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trmtd3st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                 
                    <td align="right"><? echo number_format($tot_target_opex_mtd, 2); $tot_mtd_produced_val_mon += $tot_target_opex_mtd; ?></td>
                    <td align="right"><? echo number_format($cm_value_profit, 0); $tot_mtd_produced_cm_mon += $cm_value_profit; ?></td>
                    <td align="right" title="<? echo 'Asking Profit '.$ask_profit_mtd;?>"><? echo number_format($profit_per_mon_mtd, 2); $tot_mtd_profit_per_mon += $profit_per_mon_mtd;  ?></td>
                    <td align="right"  title="CM Value-Target Opex"><? $tot_profit_loss_diff_mtd_mon=$cm_value_profit-$tot_target_opex_mtd;
					
					if($tot_profit_loss_diff_mtd_mon<0)
					{ 
						  $tot_profit_loss_diff_mtd_mon_mm="(".number_format(abs($tot_profit_loss_diff_mtd_mon),2).")"; 
						  $bg_color_td_m="red";
					}
					else
					{
						$tot_profit_loss_diff_mtd_mon_mm=number_format($tot_profit_loss_diff_mtd_mon,2);
						  $bg_color_td_m="green";
					}
					//echo $tot_profit_loss_diff_mtd_mon_mm;
					echo "<font color='$bg_color_td_m'>$tot_profit_loss_diff_mtd_mon_mm</font>";
					 $tot_mtd_profit_loss_mon += $tot_profit_loss_diff_mtd_mon;
					 
					 if($tot_mtd_profit_loss_mon<0)
						{ 
							  $tot_mtd_profit_loss_mon_opex="(".number_format(abs($tot_mtd_profit_loss_mon),2).")"; 
							  $bg_color_td_tot_m="red";

						}
						else
						{
							$tot_mtd_profit_loss_mon_opex=number_format($tot_mtd_profit_loss_mon,2);
							$bg_color_td_tot_m="green";
						}
					$tot_sum_mm= "<font color='$bg_color_td_tot_m'>".$tot_mtd_profit_loss_mon_opex."</font>";

					 ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th align="right"></th>
        <th align="right"><? echo number_format($tot_mtd_produced_val_mon, 2); ?></th>
        <th align="right"><? echo number_format($tot_mtd_produced_cm_mon, 2); ?></th>
        <th align="right"><? echo number_format($tot_mtd_profit_per_mon, 2); ?></th>
        <th align="right"><? echo $tot_sum_mm; ?></th>
    </tfoot>
    </table>
</td>

</tr>
</table>

    <Br /><Br />
    <!--YTD-->
    <table border="1" rules="all" class="rpt_table" width="1470"style="align:right" >
    <tr>
    <td  width="500">
	<table border="1" rules="all" class="rpt_table" width="500"style="align:right" >
        <thead>
            <tr>
                <th colspan="2"><?  
				
					$date_start=change_date_format(date("d-m-Y", strtotime($start_date)));//.' to '.date("Y-m-d",strtotime($txt_prod_date));
					$date_end=change_date_format(date("d-m-Y",strtotime($txt_prod_date)));
					$tot_month = datediff( 'm', $date_start,$date_end);
					for($i=0; $i<= $tot_month; $i++ )
					{
						$next_month=month_add($date_start,$i);
						$month_arr.=(date("m",strtotime($next_month))*1).',';
					}
					$month_arr_id=rtrim($month_arr,',');
					$month_arr_id=explode(",",$month_arr_id);
					//print_r($month_arr_id );
				$diff_m=datediff('m',$start_date,$txt_prod_date);
				//echo $diff_m.'GG';
				$diff_yr=datediff('d',date("Y-m-d", strtotime($start_date)),date("Y-m-d",strtotime($txt_prod_date)));
				echo date("M d", strtotime($start_date)) . "-" . date("M d", strtotime($txt_prod_date)); ?></th>
                <th colspan="5" title="<? echo 'Days: '.$diff_yr;?>">YTD Sales</th>
            </tr>
              <tr>
            <th colspan="7"> Ex Factory Value/Qty/Price</th>
            </tr>
            <tr>
                <th width="20">SL</th>
                <th width="120"> Plant </th>
                <th width="100" style="display:none"> Mon. Prod. Capacity </th>
                <th width="100"> Prod. Capacity </th>
                <th width="100"> Value </th>
                <th width="100">Qnty.</th>
                <th>FOB</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_prod_mon_qty_year = 0; $tot_prod_qty_year = 0;
    $tot_exfact_qnty = 0; $tot_exfact_val = 0; $tot_po_qnty = 0;
    foreach ($company_order_qnty as $company => $cdata) {
        $i++;
		$asking_avg_rate=$financial_cm_arr3[$company][$month_st_date]['asking_avg_rate'];
		$tot_mon_cap_qty_year=$capacity_cal_arr4[$company]['pcs']*$asking_avg_rate;
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trytd1st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trytd1st_<? echo $i; ?><? echo $d; ?>">                  <td><? echo $d++; ?></td>
                    <td><p><? echo $company_details[$company]; ?></p></td>
        			<td align="right"><? echo number_format($tot_mon_cap_qty_year, 2);$tot_prod_qty_year += $tot_mon_cap_qty_year; ?></td>
                    <td align="right"><? echo number_format($cdata['order_value'], 2); $tot_exfact_val += $cdata['order_value']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 2); $tot_exfact_qnty += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_value'] / $cdata['order_qnty']), 2); ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th  align="right">Total</th>
         <th><? //echo number_format($tot_prod_mon_qty_year, 2); ?></th>
        <th><? echo number_format($tot_prod_qty_year, 2); ?></th>
        <th><? echo number_format(($tot_exfact_val), 2); ?></th>
        <th><? echo number_format($tot_exfact_qnty, 2); ?></th>
        <th><? //echo number_format($tot_po_qnty, 2); ?></th>
    </tfoot>
    </table>
</td>
<td height="10px">&nbsp; </td>
<td width="420"> 
<table border="1" rules="all" class="rpt_table" width="420"style="align:right" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="5">YTD Production</th>
            </tr>
            <tr>
           <th colspan="6"> Produced Value & CM %</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="120"> Value </th>
                <th width="120">Qty. </th>
                <th width="50">FOB</th>
                <th width="50">Profit%</th>
                <th>CM%</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_ytd_sewing_val = 0;
    $tot_ytd_sewing_qnty = 0;
    foreach ($company_order_qnty2 as $company => $cdata)
	 {
        $i++;
		$work_day_ytd=0;
		foreach($month_arr_id as $mid)
		{
			$work_day_ytd+=$capacity_cal_arr_days2[$company][$mid]['days'];
		}
		$tot_mon_target_opex_ytd=$financial_cm_arr2[$company]['exp'];
		$tot_target_opex_ytd=($tot_mon_target_opex_ytd/$work_day_ytd)*$diff_yr;
		$cm_value_ytd=$cdata['cm_value'];
		$profit_cm_dif_ytd=$cm_value_ytd-$tot_target_opex_ytd;
		$profit_percent_ytd=($profit_cm_dif_ytd/$cdata['order_value'])*100;
		$cm_percent_ytd=($cm_value_ytd/$cdata['order_value'])*100;
		
		/*//$work_day=$capacity_cal_arr_days[$company][$month_id]['days'];
		$tot_mon_target_opex=$financial_cm_arr[$company]['exp'];
		$tot_target_opex=($tot_mon_target_opex/$work_day)*$diff_mon;
		$cm_value=$cdata['cm_value'];
		$profit_cm_dif=$cm_value-$tot_target_opex;
		$profit_percent=($profit_cm_dif/$cdata['order_value'])*100;
		$cm_percent=($cm_value/$cdata['order_value'])*100;*/
		
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trytd2st_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trytd2st_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                    <td align="right"><? echo number_format($cdata['order_value'], 2);$tot_ytd_sewing_val += $cdata['order_value']; ?></td>
                    <td align="right"><? echo number_format($cdata['order_qnty'], 2); $tot_ytd_sewing_qnty += $cdata['order_qnty']; ?></td>
                    <td align="right"><? echo number_format(($cdata['order_value']/ $cdata['order_qnty']), 2); ?></td>
                     <td align="right"><? echo number_format($profit_percent_ytd, 2); ?></td>
                     <td><p><? echo number_format($cm_percent_ytd,2); ?></p></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th align="right">&nbsp;</th>
        <th><? echo number_format($tot_ytd_sewing_val, 2); ?></th>
        <th><? echo number_format($tot_ytd_sewing_qnty, 2); ?></th>
        <th><? //echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
        <th><? //echo number_format(($tot_po_val / $tot_po_qnty), 2); ?></th>
         <th  align="right">&nbsp;</th>
    </tfoot>
    </table>
</td>
<td height="10px">&nbsp; </td>
<td width="550"> 
<table border="1" rules="all" class="rpt_table" width="550"style="align:right" >
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th colspan="5">YTD Profitability</th>
            </tr>
             <tr>
            <th colspan="6"> OPEX vs Produced CM vs Profit</th>
            </tr>
            <tr>
                <th width="30">SL</th>
                <th width="120">Target OPEX </th>
                <th width="100">CM Value</th>
                <th  width="120">Profit to be % Margin</th>
                <th>Profit /Loss on Target OPEX</th>
            </tr>
        </thead>
        <tbody>
    <?
    $d = 1;
    $i = 0;
    $tot_ytd_target_sewing_val_opex = 0;$tot_ytd_profit_loss_mon_opex = 0;
    $tot_ytd_sewing_profit_cm_opex = 0; $tot_ytd_sewing_profit_mergin_cm_opex = 0;
    foreach ($company_order_qnty2 as $company => $cdata) {
        $i++;
		$work_day_ytd_opex=0;
		foreach($month_arr_id as $mid)
		{
			$work_day_ytd_opex+=$capacity_cal_arr_days2[$company][$mid]['days'];
		}
		$tot_ytd_target_opex=$financial_cm_arr2[$company]['exp'];
		$tot_ytd_target_opex_ytd=($tot_ytd_target_opex/$work_day_ytd_opex)*$diff_yr;
		$cm_value_profit_ytd=$cdata['cm_value'];
		$ask_profit_ytd=$financial_cm_arr3[$company][$month_st_date]['asking_profit'];
		$profit_per_ytd=($cdata['order_value']*$ask_profit_ytd)/100;
		
        ?> 
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('trytdst3_<? echo $i; ?><? echo $d; ?>', '<? echo $bgcolor; ?>')" id="trytdst3_<? echo $i; ?><? echo $d; ?>">
                    <td><? echo $d++; ?></td>
                   
                    <td align="right" title="Month Expense/Working Day*All Days"><? echo number_format($tot_ytd_target_opex_ytd, 2); $tot_ytd_target_sewing_val_opex += $tot_ytd_target_opex_ytd; ?></td>
                    <td align="right"><? echo number_format($cm_value_profit_ytd, 2); $tot_ytd_sewing_profit_cm_opex += $cm_value_profit_ytd; ?></td>
                    <td align="right" title="<? echo 'Asking Profit '.$ask_profit_ytd;?>"><? echo number_format($profit_per_ytd, 2);$tot_ytd_sewing_profit_mergin_cm_opex += $profit_per_ytd; ?></td>
                    <td align="right" title="CM Value-Target Opex"><? 
					$tot_profit_loss_diff_ytd=($cm_value_profit_ytd)-($tot_ytd_target_opex_ytd);
					if($tot_profit_loss_diff_ytd<0)
					{ 
						  $tot_profit_loss_diff_ytd_yy="(".number_format(abs($tot_profit_loss_diff_ytd),2).")"; 
						  $bg_color_td_y="red";
					}
					else
					{
						$tot_profit_loss_diff_ytd_yy=number_format($tot_profit_loss_diff_ytd,2);
						$bg_color_td_y="green";
					}
					//echo $tot_profit_loss_diff_ytd_yy;
						echo "<font color='$bg_color_td_y'>$tot_profit_loss_diff_ytd_yy</font>";
					 $tot_ytd_profit_loss_ytd_opex += $tot_profit_loss_diff_ytd;
					 
					 if($tot_ytd_profit_loss_ytd_opex<0)
						{ 
							  $total_ytd_profit_loss_ytd_opex="(".number_format(abs($tot_ytd_profit_loss_ytd_opex),2).")"; 
							   $bg_color_td_tot_y="red";
						}
						else
						{
							$total_ytd_profit_loss_ytd_opex=number_format($tot_ytd_profit_loss_ytd_opex,2);
							$bg_color_td_tot_y="green";
						}
						$tot_sum_yy= "<font color='$bg_color_td_tot_y'>".$total_ytd_profit_loss_ytd_opex."</font>";
					   ?></td>
                </tr>
            <? } ?>
        </tbody>
        <tfoot>
        <th align="right">&nbsp;</th>
        <th><? echo number_format($tot_ytd_target_sewing_val_opex, 2); ?></th>
        <th><? echo number_format($tot_ytd_sewing_profit_cm_opex, 2); ?></th>
        <th><? echo number_format($tot_ytd_sewing_profit_mergin_cm_opex, 2); ?></th>
        <th align="right"><? echo $tot_sum_yy; ?></th>
    </tfoot>
    </table>
</td>
</tr>
</table>
</div>
    <?
    $d++;

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
        //if( @filemtime($filename) < (time()-$seconds_old) )
        @unlink($filename);
    }
    //---------end------------//

    $name = time();
    $filename = $user_id . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    if ($mail == 1) {
        // Mail Fnc add here
    }

    echo "$html####$filename";
    exit();
}  // end if($type=="sewing_production_summary")
?>
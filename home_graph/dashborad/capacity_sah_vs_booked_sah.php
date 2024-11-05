 <?
  session_start();
  include('../../includes/common.php');
  echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);

  ?>

 <script src="../../chart/highcharts_v2.js"></script>

 <?php

  $com_library = return_library_array("select ID, COMPANY_NAME from  LIB_COMPANY", "id", "COMPANY_NAME");


  extract($_REQUEST);
  $m = base64_decode($m);
  list($cbo_company_name, $location, $tval) = explode("__", $cp);
  if ($m != 'capacity_sah_vs_booked_sah') {
    exit();
  }

  $month_prev = date("1-M-Y", strtotime("-3 months", time()));
  $month_next = date("d-M-Y", strtotime("8 months", time()));
  $daysinmonth = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($month_next)), date('Y', strtotime($month_next)));
  $month_next = date("$daysinmonth-M-Y", strtotime("8 months", time()));




  $chart_month_array = array();
  for ($i = 0; $i <= 11; $i++) {
    $chart_month_array[$i] = date("M y", strtotime("$i months", strtotime($month_prev)));
    $month_array[$i] = date("M-y", strtotime("$i months", strtotime($month_prev)));
    $short_month_full_year_array[$i] = date("m-Y", strtotime("$i months", strtotime($month_prev)));
  }
  $month_str = implode("','", $chart_month_array);


  //conf & proj booked.........................................................

  $dateCon = "and ((c.TASK_FINISH_DATE between '$month_prev' and '$month_next') or (c.TASK_START_DATE  between '$month_prev' and '$month_next' ))";

  $sql_con_po = "SELECT b.id,c.TASK_START_DATE, c.TASK_FINISH_DATE, ( (c.TASK_FINISH_DATE-c.TASK_START_DATE)+1 ) as PLAN_LEAD_TIME,a.set_smv, a.total_set_qnty, b.id as po_id,B.PO_NUMBER, b.pub_shipment_date as shipment_date, b.po_total_price,
(CASE WHEN b.is_confirmed=1 THEN b.po_quantity ELSE 0 END) as confirm_qty,
(CASE WHEN b.is_confirmed=2 THEN b.po_quantity ELSE 0 END) as projected_qty,
(CASE WHEN b.is_confirmed=1 THEN b.po_total_price ELSE 0 END) as confirm_value,
(CASE WHEN b.is_confirmed=2 THEN b.po_total_price ELSE 0 END) as projected_value
FROM wo_po_details_master a, wo_po_break_down b,TNA_PROCESS_MST c
WHERE a.job_no = b.job_no_mst and a.job_no=c.JOB_NO and b.id=c.PO_NUMBER_ID and c.TASK_NUMBER=86 and c.TASK_TYPE=1 AND a.company_name=$cbo_company_name  $dateCon and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0"; // and b.id=41672
  //echo $sql_con_po; die;


  $po_arr = array();
  $sql_data_po = sql_select($sql_con_po);
  foreach ($sql_data_po as $row_po) {

    $shipment_date = strtotime($row_po[csf("shipment_date")]);
    $TASK_START_DATE = strtotime($row_po['TASK_START_DATE']);
    $TASK_FINISH_DATE = strtotime($row_po['TASK_FINISH_DATE']);

    foreach ($short_month_full_year_array as $my) {
      list($m, $y) = explode('-', $my);

      $daysinmonth = cal_days_in_month(CAL_GREGORIAN, $m, $y);
      $first_date = strtotime("01-$m-$y");
      $last_date = strtotime("$daysinmonth-$m-$y");

      $month_lead_time = 0;
      if ($TASK_START_DATE >= $first_date && $TASK_FINISH_DATE <= $last_date) {
        $month_lead_time = datediff('d', $row_po['TASK_START_DATE'], $row_po['TASK_FINISH_DATE']);
      } else if ($TASK_START_DATE >= $first_date && $TASK_START_DATE <= $last_date  && $TASK_FINISH_DATE >= $last_date) {
        $month_lead_time = datediff('d', $row_po['TASK_START_DATE'], date('d-m-Y', $last_date));
      } else if ($TASK_START_DATE < $first_date &&  $TASK_FINISH_DATE <= $last_date &&  $TASK_FINISH_DATE >= $first_date) {
        $month_lead_time = datediff('d', date('d-m-Y', $first_date), $row_po['TASK_FINISH_DATE']);
      } else if ($TASK_START_DATE < $first_date && $TASK_FINISH_DATE >= $first_date && $TASK_FINISH_DATE >= $last_date) {
        $month_lead_time = datediff('d', date('d-m-Y', $first_date), date('d-m-Y', $last_date));
      }


      $tna_confirm_qty = ($row_po[csf("confirm_qty")] / $row_po['PLAN_LEAD_TIME']) * $month_lead_time;
      $tna_projected_qty = ($row_po[csf("projected_qty")] / $row_po['PLAN_LEAD_TIME']) * $month_lead_time;

      $po_arr['booked_sah_con'][$my] += round($tna_confirm_qty * $row_po[csf("set_smv")] / 60);
      $po_arr['booked_sah_proj'][$my] += round($tna_projected_qty * $row_po[csf("set_smv")] / 60);
    } //month loof

  }

  $booked_sah_con = implode(',', $po_arr['booked_sah_con']);
  $booked_sah_proj = implode(',', $po_arr['booked_sah_proj']);

  //.........................................................conf & proj booked;


  //produced.........................................................;
  $item_smv_array = array();
  $sql_item = "select b.ID, a.set_break_down, c.GMTS_ITEM_ID, c.set_item_ratio, c.SMV_PCS, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
  $resultItem = sql_select($sql_item);
  foreach ($resultItem as $itemData) {
    $item_smv_array[$itemData['ID']][$itemData['GMTS_ITEM_ID']]['SMV_PCS'] = $itemData['SMV_PCS'];
  }
  //echo $sql_item;die;


  $pro_dat_con = " and PRODUCTION_DATE between '$month_prev' and '$month_next'";
  $pro_sql = "select PRODUCTION_DATE, PO_BREAK_DOWN_ID, ITEM_NUMBER_ID, sum(PRODUCTION_QUANTITY) as PRODUCTION_QUANTITY from pro_garments_production_mst where company_id=$cbo_company_name and status_active=1 and is_deleted=0 and production_type=5 $pro_dat_con   group by PRODUCTION_DATE,PO_BREAK_DOWN_ID,ITEM_NUMBER_ID";
  $pro_sql_res = sql_select($pro_sql);
  //echo $pro_sql;die;

  $producedQntyArr = array();
  foreach ($pro_sql_res as $row) {
    $production_date = date("M-y", strtotime($row["PRODUCTION_DATE"]));
    $item_smv = $item_smv_array[$row['PO_BREAK_DOWN_ID']][$row['ITEM_NUMBER_ID']]['SMV_PCS'];
    $producedQntyArr[$production_date] += round(($row["PRODUCTION_QUANTITY"] * $item_smv) / 60);
  }


  foreach ($month_array as $My) {
    $produced_qty_Arr[$My] = $producedQntyArr[$My] * 1;
  }

  $produced_qty = implode(',', $produced_qty_Arr);

  //.........................................................produced;



  // $allocation_lib_arr=array();
  // $allocationData=sql_select("select a.YEAR, b.MONTH_ID, sum(b.capacity_month_min) as CAPA_MIN from lib_capacity_calc_mst a, lib_capacity_year_dtls b where a.id=b.mst_id and a.comapny_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 group by a.year, b.MONTH_ID");
  // foreach($allocationData as $row)
  // {
  //   $allocation_lib_arr[$row['YEAR']][$row['MONTH_ID']]=$row['CAPA_MIN']*1;
  // } 


  $sql_data_smv = sql_select("select a.id,a.comapny_id, a.YEAR, a.avg_machine_line, a.BASIC_SMV, a.EFFI_PERCENT, c.MONTH_ID, c.CAPACITY_MONTH_MIN, c.WORKING_DAY from lib_capacity_calc_mst a, lib_capacity_year_dtls c where a.id=c.mst_id and a.comapny_id=$cbo_company_name ");


  $allocation_lib_arr = array();
  foreach ($sql_data_smv as $row) {
    $basic_smv_arr[$row[csf("comapny_id")]][$row['YEAR']] = $row['BASIC_SMV'];

    $allocation_lib_arr[$row['YEAR']][$row['MONTH_ID']]['clock_hrs'] = (($row["CAPACITY_MONTH_MIN"] / $row['EFFI_PERCENT']) * 100);
    $allocation_lib_arr[$row['YEAR']][$row['MONTH_ID']]['efficency'] = $row['EFFI_PERCENT'];
    $allocation_lib_arr[$row['YEAR']][$row['MONTH_ID']]['basic_smv'] = $row['BASIC_SMV'];

    $allocation_lib_arr[$row['YEAR']][$row['MONTH_ID']]['line'] = $row[csf("avg_machine_line")];
    $allocation_lib_arr[$row['YEAR']][$row['MONTH_ID']]['working_day'] = $row['WORKING_DAY'];

    $no_of_line = $dtls_arr[$row[csf("id")]][$row['MONTH_ID']]; ///$row[csf("working_day")];
    $allocation_lib_arr[$row['YEAR']][$row['MONTH_ID']]['tot_line'] = $no_of_line / $row['WORKING_DAY'];
  }




  $working_hour_arr = array();
  $workingData = sql_select("select APPLYING_PERIOD_DATE, WORKING_HOUR from lib_standard_cm_entry where status_active=1 and is_deleted=0 and company_id=$cbo_company_name and APPLYING_PERIOD_DATE >= '$month_prev'");

  foreach ($workingData as $row) {
    $working_hour_arr[date("Y", strtotime($row['APPLYING_PERIOD_DATE']))][(int)(date("m", strtotime($row['APPLYING_PERIOD_DATE'])))] = $row['WORKING_HOUR'];
  }


  $capacity_data_arr = array();
  foreach ($short_month_full_year_array as $mY) {
    list($m, $y) = explode('-', $mY);
    $capacity_data_arr[$mY] = round((($allocation_lib_arr[$y][(int)$m]['clock_hrs'] / 60) * $allocation_lib_arr[$y][(int)$m]['efficency']) / 100);
  }
  $capacity_data = implode(',', $capacity_data_arr);

  ?>

 <style>
   #container {
     height: 100vh;
   }
 </style>

 <h2 style="margin:0 auto;width:180px;"><?= $com_library[$cbo_company_name]; ?></h2>
 <div id="container"></div>


 <script>
   Highcharts.chart('container', {

     chart: {
       type: 'column',
     },

     title: {
       text: 'Capacity SAH VS Booked SAH'
     },

     xAxis: {
       categories: ['<?= $month_str; ?>'],
     },

     yAxis: {
       allowDecimals: true,
       min: 0,
       title: {
         text: 'SAH'
       }
     },

     tooltip: {
       crosshairs: true,
       formatter: function() {
         return '<b>' + this.x + '</b><br/>' +
           this.series.name + ': ' + Highcharts.numberFormat(this.y, 0, '.', ',') + '<br/>';
         //  + 'Total: ' + Highcharts.numberFormat(this.point.stackTotal,0, '.', ',');
       }
     },


     plotOptions: {
       column: {
         stacking: 'normal',
         pointPadding: 0,
         borderWidth: 0,
         groupPadding: 0.1,
         shadow: false,
         dataLabels: {
           enabled: true,
           formatter: function() {
             return Highcharts.numberFormat(this.y, 0, '.', ',')
           },
           rotation: -90,
           y: 10,
           style: {
             fontSize: '12px',
             textShadow: false,
             color: '#FFFFFF',
             textOutline: false,
           }
         }
       }
     },


     series: [{
         name: 'Capacity SAH',
         data: [<?= $capacity_data; ?>],
         stack: '3',
         color: 'ORANGE',
       }, {
         name: 'Confirm Booked SAH',
         data: [<?= $booked_sah_con; ?>],
         stack: '1',
         color: 'blue'

       }, {
         name: 'Projected Booked SAH',
         data: [<?= $booked_sah_proj; ?>],
         stack: '1',
         color: 'red'

       },
       {
         name: 'Produced',
         data: [<?= $produced_qty; ?>],
         stack: '2',
         color: 'green',

       }
     ]

   });
 </script>
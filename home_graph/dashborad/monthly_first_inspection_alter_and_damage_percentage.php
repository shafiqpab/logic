<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Form Will Create Dash Board.
Functionality	:	
JS Functions	:
Created by		:	Nayem
Creation date 	: 	26-08-2021
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
require_once('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, $multi_select, 1);

// $sweater_reject_type=array( 1=>"Needle Line", 2=>"Drop Stitches", 3=>"Selvedge Broken", 4=>"Hole", 5=>"Misplatting", 6=>"Lycra Visible", 7=>"Lycra Missing", 8=>"Yarn Ply Missing", 9=>"Broken Stitch", 10=>"Loose Thread", 11=>"Mixed Dye Lot", 12=>"Size Yarn Mistake", 13=>"Knot", 14=>"Slub / Neps", 15=>"Uneven Dyeing", 16=>"Stripeness", 17=>"Fibre Contamination", 18=>"Oil Spot  ", 19=>"Dirty Spot", 20=>"Tuck Stitches", 21=>"Design Mistake", 22=>"Measurement Discripencies", 23=>"Others");

$sweater_reject_type=array( 1=>"Needle Drop", 2=>"Double Line", 3=>"Puckering Yarn", 4=>"Side Needle Drop", 5=>"Color Shading", 6=>"Wrong Measurement", 7=>"Defective Needle", 8=>"Tention Tight Loose", 9=>"Starting c/s", 10=>"Wrong Needle", 11=>"Dirty Fashion", 12=>"Yarn Thin & Thick", 13=>"Nylon Visible");
//--------------------------------------------------------------------------------------------------------------------
if($action=="opendate_popup")
{
	?>
    </head>
	<body>
        <div align="center" style="width:100%;" >
            <form name="keydate_1"  id="keydate_1" autocomplete="off">
                <table width="290" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                    <thead>
                         <tr>
                            <th width="50%">Month</th>
                            <th>Year</th>
                         </tr>
                  	</thead>
                    <tr>
                    	<td align="center">
                            <? echo create_drop_down( "cbo_month", 110, $months ,"", 0,"-All-", date("m", time()), "",0,"" ); ?>
                        </td>
                    	<td align="center">
                            <? echo create_drop_down( "cbo_year", 90, create_year_array(),"", 0,"-All-", date("Y", time()), "",0,"" ); ?>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="2" align="center"><input type="button" name="button2" class="formbutton" value="Close" onClick="parent.emailwindow.hide()" style="width:70px;" /></td>
                    </tr>
                 </table>
             </form>
             </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}

if($_REQUEST['m']=='bW9udGhseV9maXJzdF9pbnNwZWN0aW9uX2FsdGVyX2FuZF9kYW1hZ2VfcGVyY2VudGFnZQ=='){
	
	list($Company,$location)=explode('__',$_REQUEST['cp']);
	list($date_month,$date_year)=explode('__',$_REQUEST['date_data']);
	$company_library =return_library_array( "select id, company_name from lib_company where id=$Company", "id", "company_name");
    
    $date_mk=$date_year.'-'.$date_month;
    if($db_type==0)
    {
        $startDate = date("Y-m-d",strtotime($date_mk));
        $endDate = date("Y-m-t",strtotime($date_mk));
    }
    else
    {
        $startDate = date("d-M-Y", strtotime($date_mk));
        $endDate = date("t-M-Y", strtotime($date_mk));
    }

    $sql_cond=" and a.company_id='$Company' ";
    $sql_cond.=" and a.location_id='$location' ";
    $sql_cond.=" and a.cutting_qc_date between '$startDate' and '$endDate'";
    
    // $data_sql="SELECT a.id, a.job_no as JOB_NO, b.id as DTLS_ID, b.mst_id as MST_ID, b.production_qnty as PRODUCTION_QNTY, b.reject_qty as REJECT_QTY, b.replace_qty as REPLACE_QTY, b.defect_qty as DEFECT_QTY,e.id as BUNDLE_ID,e.size_qty as SIZE_QTY from pro_gmts_cutting_qc_mst a, pro_garments_production_dtls b, ppl_cut_lay_mst c, ppl_cut_lay_dtls d, ppl_cut_lay_bundle e where a.id=b.delivery_mst_id and b.production_type=52 and a.cutting_no=c.cutting_no and c.id=d.mst_id and c.id=e.mst_id and d.id=e.dtls_id and b.bundle_no=e.bundle_no  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sql_cond group by a.id, a.job_no, b.id, b.mst_id, b.production_qnty, b.reject_qty, b.replace_qty, b.defect_qty, e.id, e.size_qty order by b.id asc";
    $data_sql="SELECT a.id, a.job_no as JOB_NO, b.id as DTLS_ID, b.mst_id as MST_ID, b.production_qnty as PRODUCTION_QNTY, b.reject_qty as REJECT_QTY, b.barcode_no as BARCODE_NO, b.defect_qty as DEFECT_QTY from pro_gmts_cutting_qc_mst a, pro_garments_production_dtls b where a.id=b.delivery_mst_id and b.production_type=52 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by a.id, a.job_no, b.id, b.mst_id, b.production_qnty, b.reject_qty, b.barcode_no, b.defect_qty order by b.id asc";
    // echo $data_sql;
    $data_result=sql_select($data_sql);
    $gmt_mst_arr=array();$job_arr=array();$chk_arr=array();$chk_arr1=array();
    $total_bundle_qty=$total_production_qnty=$total_defect_qty=$total_reject_qty=$total_replace_qty=0;
    foreach($data_result as $row )
    {
        $gmt_mst_arr[$row['MST_ID']]=$row['MST_ID'];
        $job_arr[$row['JOB_NO']]="'".$row['JOB_NO']."'";

        /*if(!in_array($row['DTLS_ID'],$chk_arr))
        {
            $chk_arr[]=$row['DTLS_ID'];
            $total_production_qnty+=$row['PRODUCTION_QNTY'];
            $total_defect_qty+=$row['DEFECT_QTY'];
            $total_reject_qty+=$row['REJECT_QTY'];
            $total_replace_qty+=$row['REPLACE_QTY'];
        }*/
        /*if(!in_array($row['BUNDLE_ID'],$chk_arr1))
        {
            $chk_arr1[]=$row['BUNDLE_ID'];
            $total_production_qnty+=$row['PRODUCTION_QNTY'];
            $total_defect_qty+=$row['DEFECT_QTY'];
            $total_reject_qty+=$row['REJECT_QTY'];
            $total_replace_qty+=$row['REPLACE_QTY'];
            $total_bundle_qty+=$row['SIZE_QTY'];
        }*/
        if(!in_array($row['BARCODE_NO'],$chk_arr))
        {
          $chk_arr[]=$row['BARCODE_NO'];
          $total_production_qnty+=$row['PRODUCTION_QNTY'];
          $total_defect_qty+=$row['DEFECT_QTY'];
          $total_reject_qty+=$row['REJECT_QTY'];
          $total_replace_qty+=$row['REPLACE_QTY'];
          $total_bundle_qty+=$row['PRODUCTION_QNTY']+$row['DEFECT_QTY']+$row['REJECT_QTY'];
        }
    }

    $style_count=sql_select("SELECT count(style_ref_no) as STYLE_NO from wo_po_details_master where job_no in (".implode(',', $job_arr).")");

    $sql_defect="SELECT defect_point_id, sum(defect_qty) as DEFECT_QTY from pro_gmts_prod_dft where status_active=1 and is_deleted=0 and mst_id in (".implode(',', $gmt_mst_arr).") and production_type=52 and defect_type_id in(3,4) and status_active=1 and is_deleted=0 group by defect_point_id";
    $sql_defect_result=sql_select($sql_defect);
    $defect_data=array();
    foreach ($sql_defect_result as  $value) 
    {
        $defect_data[$value[csf('defect_point_id')]]+=$value['DEFECT_QTY'];  
    }

    ?>	
    <!-- <script src="../../chart/highcharts_v2.js"></script> -->
    <script src="../../js/highchart/highcharts.js"></script>
    <script src="../../js/highchart/highcharts-3d.js"></script>
    <script src="../../js/highchart/exporting.js"></script>

    <div style="margin:10px 0 0 10px; width:99%; text-align:center">
    	<h2><? echo $company_library[$Company];?></h2>
    	<h3>Dashboard (Alter%  &  Damage%)</h3>
    	<h3>Section: 1st Inspection</h3>
    	<h3>For The Month Of <? echo $months[$date_month].'-'.$date_year;?></h3>
    </div>
	
    <div style="margin:10px 0 0 10px; width:99%">
        <div style="width:48%; height:300px; float:left; position:relative; ">
            <table width="400" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<thead>
                    <tr>
                        <th width="30">Sl</th>
                        <th width="180">Perameters</th>
                        <th width="220" colspan='2'>Particulars</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Total Style No.</td>
                        <td colspan='2' style='text-align:center'><?echo $style_count[0]['STYLE_NO'];?></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Total QC Qty [Pcs]</td>
                        <td colspan='2' style='text-align:center'><?echo $total_bundle_qty;?></td>
                    </tr>
                    <tr style="background-color:#8DAFDA;">
                        <td style='text-align:center'><strong>Sl</strong></td>
                        <td style='text-align:center'><strong>Particulars</strong></td>
                        <td style='text-align:center'><strong>Qty [Pcs]</strong></td>
                        <td style='text-align:center'><strong>Perc. %</strong></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Total QC Pass Qty</td>
                        <td style='text-align:right;'><?echo $total_production_qnty;?></td>
                        <td style='text-align:right;'><?
                            echo number_format(($total_production_qnty/$total_bundle_qty)*100,2);
                            $total_production_qnty_prcnt=number_format(($total_production_qnty/$total_bundle_qty)*100,2);
                        ?></td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Total Alter Qty</td>
                        <td style='text-align:right;'><?echo $total_defect_qty;?></td>
                        <td style='text-align:right;'><?
                            echo number_format(($total_defect_qty/$total_bundle_qty)*100,2);
                            $total_defect_qty_prcnt=number_format(($total_defect_qty/$total_bundle_qty)*100,2);
                        ?></td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Total Damage Qty</td>
                        <td style='text-align:right;'><?echo $total_reject_qty;?></td>
                        <td style='text-align:right;'><?
                            echo number_format(($total_reject_qty/$total_bundle_qty)*100,2);
                            $total_reject_qty_prcnt=number_format(($total_reject_qty/$total_bundle_qty)*100,2);
                        ?></td>
                    </tr>
                </tbody>
            </table>
		</div>
        <div style="width:48%; height:300px; float:left; position:relative; ">
            <div id="chartContainer" style="height: 240px; width: 100%;"></div>
		</div>
	</div>
    <table width="1300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
        <thead>
            <tr><th colspan='<?echo count($sweater_reject_type)+1;?>'>Type Of Defects</th></tr>
            <tr>
                <th>Total No. of Defect</th>
                <?
                foreach($sweater_reject_type as $val)
                {
                  ?>
                      <th><?echo $val;?></th>
                  <?
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style='text-align:center'><?
                    $total_defect_qty_result=$total_defect_qty+$total_reject_qty;
                    echo $total_defect_qty_result;
                    ?></td>
                <?
                foreach($sweater_reject_type as $key=>$val)
                {
                  ?>
                      <td style='text-align:center'><?echo $defect_data[$key];?></td>
                  <?
                }
                ?>
            </tr>
            <tr>
                <td></td>
                <?
                foreach($sweater_reject_type as $key=>$val)
                {
                  $defect_val[$key]=fn_number_format($defect_data[$key]/$total_defect_qty_result,2);
                  ?>
                      <td style='text-align:center'><strong><?echo fn_number_format($defect_data[$key]/$total_defect_qty_result,2)." %";?></strong></td>
                  <?
                }
                ?>
            </tr>
        </tbody>
    </table>
    <div id="chartContainer2" style="height: 340px; width: 100%;"></div>
	<?
}
?>
<script>

Highcharts.chart('chartContainer', {
  chart: {
    type: 'column',
    options3d: {
        enabled: true,
        alpha: 10,
        beta: 10,
        depth: 100
    }
  },
  title: {
    text: 'Total Summary of Graph'
  },
  accessibility: {
    announceNewData: {
      enabled: true
    }
  },
  xAxis: {
    type: 'category'
  },
  yAxis: {
    title: {
      text: 'Percentage'
    }
  },
  legend: {
    enabled: false
  },
  plotOptions: {
    series: {
      borderWidth: 0,
      dataLabels: {
        enabled: true,
        format: '{point.y:.1f}%'
      }
    }
  },
  tooltip: {
    pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b><br/>'
  },
  series: [
    {
      data: [
        {
          name: "Total QC Pass Qty",
          y: <? echo $total_production_qnty_prcnt;?>,
          color: 'rgb(34,139,34)'
        },
        {
          name: "Total Alter Qt",
          y: <? echo $total_defect_qty_prcnt;?>,
          color: 'rgb(255,255,0)'
        },
        {
          name: "Total Damage Qty",
          y: <? echo $total_reject_qty_prcnt;?>,
          color: 'rgb(255,0,0)'
        }
      ]
    }
  ],
  
});

Highcharts.chart('chartContainer2', {
  chart: {
    type: 'column'
  },
  title: {
    text: 'Defect %'
  },
  xAxis: {
    type: 'category',
    labels: {
      rotation: -45,
      style: {
        fontSize: '13px',
        fontFamily: 'Verdana, sans-serif'
      }
    }
  },
  yAxis: {
    min: 0,
    title: {
      text: 'Percentage'
    }
  },
  legend: {
    enabled: false
  },
  tooltip: {
    pointFormat: '<b>{point.y:.1f} %</b>'
  },
  series: [{
    colorByPoint: true,
    data: [
      ['Needle Drop', <?echo $defect_val[1];?>],
      ['Double Line', <?echo $defect_val[2];?>],
      ['Puckering Yarn', <?echo $defect_val[3];?>],
      ['Side Needle Drop', <?echo $defect_val[4];?>],
      ['Color Shading', <?echo $defect_val[5];?>],
      ['Wrong Measurement', <?echo $defect_val[6];?>],
      ['Defective Needle', <?echo $defect_val[7];?>],
      ['Tention Tight Loose', <?echo $defect_val[8];?>],
      ['Starting c/s', <?echo $defect_val[9];?>],
      ['Wrong Needle', <?echo $defect_val[10];?>],
      ['Dirty Fashion', <?echo $defect_val[11];?>],
      ['Yarn Thin & Thick', <?echo $defect_val[12];?>],
      ['Nylon Visible', <?echo $defect_val[13];?>],
      // ['Slub / Neps', <?echo $defect_val[14];?>],
      // ['Uneven Dyeing', <?echo $defect_val[15];?>],
      // ['Stripeness', <?echo $defect_val[16];?>],
      // ['Fibre Contamination', <?echo $defect_val[17];?>],
      // ['Oil Spot', <?echo $defect_val[18];?>],
      // ['Dirty Spot', <?echo $defect_val[19];?>],
      // ['Tuck Stitches', <?echo $defect_val[20];?>],
      // ['TDesign Mistake', <?echo $defect_val[21];?>],
      // ['Measurement Discripencies', <?echo $defect_val[22];?>],
      // ['Others', <?echo $defect_val[23];
      ?>]
    ],
    dataLabels: {
      enabled: true,
      rotation: -90,
      color: '#FFFFFF',
      align: 'right',
      format: '{point.y:.1f}', // one decimal
      y: 10, // 10 pixels down from the top
      style: {
        fontSize: '13px',
        fontFamily: 'Verdana, sans-serif'
      }
    }
  }]
});

</script>


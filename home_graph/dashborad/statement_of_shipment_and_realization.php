<?
session_start();
include('../../includes/common.php');
echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, 0,0);
extract($_REQUEST);

if($action=='year_popup')
{
    echo load_html_head_contents("Graph", "../../", "", $popup, $unicode, 0,0);
    $data=explode("_",$data);
    $company=$data[0];
    $location=$data[1];
    ?>
    <script>
        function fnc_close()
        {
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
    <div align="center">
    <br>
        <fieldset style="width:200px;">
            <table align="center" width="200" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="130" colspan="2">&nbsp; </td>
                </tr>
                <tr>
                    <td width="30"><b>Year : </b></td>
                    <td width="100"><input type="text" style="width:100px;" name="txt_year" id="txt_year" class="text_boxes_numeric" maxlength="4"  value="<? echo date('Y'); ?>" /></td>
                </tr>
                <tr>
                    <td width="130" colspan="2">&nbsp; </td>
                </tr>
            </table>
                
            <div style="width:200px;" align="center">
                <input type="button" name="close" class="formbutton" value="Generate" id="main_close" onClick="fnc_close();" style="width:100px" />
            </div>
        </fieldset>
    </div>
    </body>           
    <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
    </html> 
    <script>
        
    $(document).ready(function() {
        $('#txt_year').select();
    });


    </script> 
    <?
    exit();
}
?>

<?
if($action =='report_generate')
{
    function add_month($orgDate,$mon)
    {
        $cd = strtotime($orgDate);
        $retDAY = date('Y-m-d', mktime(0,0,0,date('m',$cd)+$mon,1,date('Y',$cd)));
        return $retDAY;
    }
    extract($_REQUEST);
    $m= base64_decode($m);
    // list($company,$location,$floor,$working_company)=explode('__',$cp);
    if($company!=0)
    {
        $str_comp=" AND A.COMPANY_ID=$company";	
        $str_comp2=" AND COMPANY_ID=$company";
        $str_comp3=" AND C.COMPANY_NAME=$company";
        $str_comp4=" AND DM.DELIVERY_COMPANY_ID = $company";
    };

        $companyArr = return_library_array("SELECT ID,COMPANY_NAME FROM LIB_COMPANY WHERE STATUS_ACTIVE=1 AND IS_DELETED=0","ID","COMPANY_NAME");
    ?>
    <script src="../../chart/highcharts_v2.js"></script>
    <?
    $tot_month = datediff( 'm', $from_date,$to_date);
    $tot_month=($tot_month)?$tot_month:11;

    $f_date=$txt_year.'-01-01';
	$t_date=$txt_year.'-12-31';
	$from_date=date('Y-m-d', strtotime($f_date));
	$to_date=date('Y-m-d', strtotime($t_date));
    $firstDate = date("d-M-Y",strtotime($from_date));
    $lastDate = date("d-M-Y", strtotime($to_date));	
    
    $month_prev=date("Y-m-d",strtotime($from_date));
    $month_prev=date("Y-m-d",strtotime($to_date));
    $month_prev=($from_date)?$from_date:$month_prev;
    $month_next=($from_date)?$to_date:$month_next;
    

    $date_cond=" AND A.DELIVERY_DATE BETWEEN '$firstDate' AND '$lastDate' ";
    $date_cond2=" AND PRODUCTION_DATE BETWEEN '$firstDate' AND '$lastDate'";
    //$date_cond3=" and a.ex_factory_date between '$firstDate' and '$lastDate'";
    $date_cond4=" AND M.EX_FACTORY_DATE BETWEEN '$firstDate' AND '$lastDate'";
    $date_cond5=" AND B.EX_FACTORY_DATE BETWEEN '$firstDate' AND '$lastDate'";

    $start_yr=date("Y",strtotime($month_prev));
    $end_yr=date("Y",strtotime($month_next));
    for($e=0;$e<=$tot_month;$e++)
    {
        $tmp=add_month(date("Y-m-d",strtotime($month_prev)),$e);
        $yr_mon_part[$e]=date("Y-m",strtotime($tmp));
    }


    $i=1;

    $sql = "SELECT  M.ID,DM.DELIVERY_COMPANY_ID,DM.DELIVERY_LOCATION_ID,M.EX_FACTORY_DATE, M.EX_FACTORY_QNTY AS EX_QNTY, A.BUYER_NAME,D.COLOR_SIZE_BREAK_DOWN_ID,M.DELIVERY_MST_ID AS CHALLAN_ID,
    C.COUNTRY_SHIP_DATE,M.PO_BREAK_DOWN_ID,D.PRODUCTION_QNTY, C.ORDER_RATE*D.PRODUCTION_QNTY AS EX_VALUE,C.ID AS COLOR_SIZE,DM.DELIVERY_DATE
    FROM PRO_EX_FACTORY_DELIVERY_MST DM,PRO_EX_FACTORY_MST M, WO_PO_DETAILS_MASTER A, WO_PO_COLOR_SIZE_BREAKDOWN C,
    PRO_EX_FACTORY_DTLS D
    WHERE DM.ID = M.DELIVERY_MST_ID
    AND A.JOB_NO = C.JOB_NO_MST 
    AND M.ID = D.MST_ID $str_comp4 $date_cond4
    AND D.COLOR_SIZE_BREAK_DOWN_ID = C.ID
    AND DM.DELIVERY_COMPANY_ID <> 0 AND M.ENTRY_FORM <> 85 AND DM.DELIVERY_COMPANY_ID IS NOT NULL
    AND DM.STATUS_ACTIVE = 1 AND DM.IS_DELETED = 0 AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0
    AND M.STATUS_ACTIVE = 1 AND M.IS_DELETED = 0
    ORDER BY M.ID";
    //   echo $sql;
    $result_sql=sql_select($sql);
    foreach($result_sql as $row)
    {
        $result_array[$row["DELIVERY_COMPANY_ID"]][$row["DELIVERY_LOCATION_ID"]][$row["BUYER_NAME"]]['EX_QNTY'] += $row["PRODUCTION_QNTY"];
        $result_array[$row["DELIVERY_COMPANY_ID"]][$row["DELIVERY_LOCATION_ID"]][$row["BUYER_NAME"]]['EX_FACTORY_DATE'] = $row["EX_FACTORY_DATE"];
        $result_array[$row["DELIVERY_COMPANY_ID"]][$row["DELIVERY_LOCATION_ID"]][$row["BUYER_NAME"]]['EX_VALUE'] += $row["EX_VALUE"];
        $result_array[$row["DELIVERY_COMPANY_ID"]][$row["DELIVERY_LOCATION_ID"]][$row["BUYER_NAME"]]['PO_ID'] .= $row["PO_BREAK_DOWN_ID"].",";
        $result_array[$row["DELIVERY_COMPANY_ID"]][$row["DELIVERY_LOCATION_ID"]][$row["BUYER_NAME"]]['COLOR_SIZE'] .= $row["COLOR_SIZE"].",";
    }
    $retun_sql = "SELECT B.PO_BREAK_DOWN_ID AS PO_ID,B.DELIVERY_MST_ID,C.COLOR_SIZE_BREAK_DOWN_ID, C.PRODUCTION_QNTY AS RETURN_QNTY, D.ORDER_RATE
    FROM PRO_EX_FACTORY_MST B, PRO_EX_FACTORY_DTLS C,  WO_PO_COLOR_SIZE_BREAKDOWN D
    WHERE B.ID = C.MST_ID AND C.COLOR_SIZE_BREAK_DOWN_ID = D.ID $date_cond5
    AND B.ENTRY_FORM = 85
    AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0
    AND C.STATUS_ACTIVE = 1 AND C.IS_DELETED = 0";
   // echo $retun_sql;
    $return_sql_result=sql_select($retun_sql);
    $ex_return_qty_arr=array();
    foreach($return_sql_result as $row)
    {
        $ex_return_value =  $row['RETURN_QNTY']*$row['ORDER_RATE'];
        $ex_return_qty_arr[$row['PO_ID']][$row['COLOR_SIZE_BREAK_DOWN_ID']]['RETURN_QTY']=$row['RETURN_QNTY'];
        $ex_return_qty_arr[$row['PO_ID']][$row['COLOR_SIZE_BREAK_DOWN_ID']]['RETURN_VALUE']=$ex_return_value;
        $ex_return_qty_arr[$row['PO_ID']]['COLOR_SIZE_LIST'].=$row['COLOR_SIZE_BREAK_DOWN_ID'].",";
    }
    $result=sql_select($sql);
    $exFactoryQty=0;  $confPoVal=0; $projPoVal=0; $exFactoryQty=0; $exFactoryVal=0;
    foreach($result_array as $company_id => $company_data)
    {
        foreach($company_data as $location_id => $location_data)
        {
            $y = 1;
            foreach($location_data as $buyer_id => $row)
            {
                $po_arr =  array_filter(array_unique(explode(",",chop($row["PO_ID"],","))));
                $ex_return_qnty = 0;$ex_return_value=0;
                foreach($po_arr as $po_id)
                {
                    $color_size_arr = array_filter(array_unique(explode(",",chop($ex_return_qty_arr[$po_id]["COLOR_SIZE_LIST"],","))));
                    foreach($color_size_arr as $color_size_id)
                    {   
                        $ex_return_qnty +=  $ex_return_qty_arr[$po_id][$color_size_id]['RETURN_QTY'];
                        $ex_return_value +=  $ex_return_qty_arr[$po_id][$color_size_id]['RETURN_VALUE'];
                    }
                }
                $month_no=date("m",strtotime($row['EX_FACTORY_DATE']));
                $year=date("y",strtotime($row['EX_FACTORY_DATE']));		
                $all_graph_data[$year][$month_no]['EX_QTY']+=$row["EX_QNTY"]-$ex_return_qnty;
                $all_graph_data[$year][$month_no]['EX_VAL']+=$row["EX_VALUE"]-$ex_return_value;
            }
        }
    }
    // $sql_2="SELECT ID, COMPANY_ID, PO_BREAK_DOWN_ID, PRODUCTION_DATE, PRODUCTION_QUANTITY  FROM PRO_GARMENTS_PRODUCTION_MST WHERE  PRODUCTION_TYPE='8' AND STATUS_ACTIVE=1 AND IS_DELETED=0 $STR_COMP2 $DATE_COND2  ORDER BY PRODUCTION_DATE ASC";
    // // echo $sql_2;
    // $packing_finish=sql_select($sql_2);
    // $packing_finish_qty=0;
    // foreach($packing_finish as $row)
    // {
    //     $month_no=date("m",strtotime($row['PRODUCTION_DATE']));
    //     $year=date("y",strtotime($row['PRODUCTION_DATE']));		
    //     $all_graph_data[$year][$month_no]['PACKING_QTY']+=$row['PRODUCTION_QUANTITY'];
    // }
    $html='<tbody>'; 
    $totExFactoryVal=0;$totExFactoryQty=0;$totPackingQty=0;			
    foreach($yr_mon_part as $key=>$val)
    {
        $month=date("M",strtotime($val));	
        $month_num=date("m",strtotime($val));			
        $year_no=date("y",strtotime($val));	

        if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

        $html.='<tr bgcolor="'.$bgcolor.'" onClick="change_color('."'tr_".$i."','".$bgcolor."'".')" id="tr_'.$i.'">';
        $html.='<td>'.date("M",strtotime($val))."'".date("y",strtotime($val)).'</td>
                <td align="right">'.number_format($all_graph_data[$year_no][$month_num]['EX_VAL'],0).'</td>
                <td align="right">'.number_format($all_graph_data[$year_no][$month_num]['EX_QTY'],0).'</td>
                <td align="right">'.number_format($all_graph_data[$year_no][$month_num]['EX_VALll'],0).'</td>
                <td align="right">'.number_format($all_graph_data[$year_no][$month_num]['PACKING_QTY'],0).'</td>';			
        $html.='</tr>';
        
        $totExFactoryVal+=$all_graph_data[$year_no][$month_num]['EX_VAL'];
        $totExFactoryQty+=$all_graph_data[$year_no][$month_num]['EX_QTY'];  
        $totExFactoryQty+=$all_graph_data[$year_no][$month_num]['EX_QTY3'];  
        $totExFactoryQty+=$all_graph_data[$year_no][$month_num]['EX_QTY4'];  
        $totPackingQty +=$all_graph_data[$year_no][$month_num]['packing_qty'];	

        if($i==1)
        {
            $month_list .='"'.$month."'".$year_no.'"';
            $ex_factory_val.=number_format($all_graph_data[$year_no][$month_num]['EX_VAL'],0,'','') ;
            $ex_factory_qty.=number_format($all_graph_data[$year_no][$month_num]['EX_QTY'],0,'','') ;
            $packing_qty.=number_format($all_graph_data[$year_no][$month_num]['packing_qty'],0,'','') ;
            $i++;
        }
        else
        {
            $month_list .=',"'.$month."'".$year_no.'"';
            $ex_factory_val.=",".number_format($all_graph_data[$year_no][$month_num]['EX_VAL'],0,'','') ;
            $ex_factory_qty.=",".number_format($all_graph_data[$year_no][$month_num]['EX_QTY'],0,'','') ;
            $packing_qty.=",".number_format($all_graph_data[$year_no][$month_num]['PACKING_QTY'],0,'','') ;
        }
        $i++;
    }
        $html.='</tr></tbody><tfoot><th>Total</th>'; 
        $html.='<th align="right">'.number_format($totExFactoryVal,0).'</th>
                <th align="right">'.number_format($totExFactoryQty,0).'</th>
                <th align="right">'.number_format($totExFactoryVal,0).'</th>
                <th align="right">'.number_format($totPackingQty,0).'</th>';
                        
    ?>
    <table width="1050" cellpadding="0" cellspacing="0">
        <tr>
            <td height="30" valign="middle" align="center" colspan="2">
                <font size="2" color="#4D4D4D"> <strong><span id="caption_text"></span></strong></font>
            </td>
            <td colspan="2" rowspan="2" valign="top" align="center"> 
                <div style="margin-left:5px; margin-top:45px">
                    <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" width="360" id="tableQty">
                        <thead>
                            <th width="55">Month</th>
                            <th width="60">Shipment Summary</th>
                            <th width="55">PCS</th>
                            <th width="60">Realized Value</th>
                            <th width="60">Due Value</th>
                        </thead>
                        <? echo $html; ?>
                    </table>                  
                </div>
            </td>
        </tr>
        <tr>
            <td width="8" bgcolor=" "></td>
            <td align="center" height="400" width="750">
                <h2><b><? if($company!=0){echo $companyArr[$company];}else{ echo "All Company";}?></b></h2>
                <figure class="highcharts-figure">
                    <div id="container"  style="width:750px; height:400px; background-color:#FFFFFF"></div>
                </figure>
            </td>
        </tr>
        <tr>
            <td height="8" colspan="2" bgcolor=" "></td>
            <td width="8" bgcolor=""></td>
            <td></td>
        </tr>
    
    </table>
    <div>
    <!-- <canvas id="myChart" style="width:100%;max-width:600px"></canvas> -->
    </div>

    <script>
        Highcharts.chart('container', {
        title: {
        text: 'Shipment and Realization Chart'
        },
        tooltip: {
        formatter: function() {
        return '<b>'+ this.series.name +'</b>: '+ this.point.y ;
        }
        },
        xAxis: {
        gridLineWidth: 1,
        alternateGridColor: '#F7F7F7',
        categories: [<?=$month_list;?>]
        },
        labels: {
        items: [{
        html: '',
        style: {
        left: '20px',
        top: '8px',
        color: ( // theme
        Highcharts.defaultOptions.title.style &&
        Highcharts.defaultOptions.title.style.color
        ) 
        }
        }]
        },
        series: [{
        type: 'column',
        name: 'Ex-Factory Qty.',
        data: [<?=$ex_factory_qty;?>],
        color: '#4d88ff'
        }, {
        type: 'column',
        name: 'Ex-Factory Val.',
        data: [<?=$ex_factory_val;?>],
        color: '#cc3300'	
        }, {
        type: 'spline',
        name: 'Finishing Production',
        data: [<?=$packing_qty;?>],
        color: '#42f554',
        marker: {
        lineWidth: 2,
        lineColor: Highcharts.getOptions().colors[3],
        fillColor: 'white'
        }
        }]
        });
    </script>
    <?
}

?>
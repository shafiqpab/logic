 <?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$lib_company=return_library_array( "select id,company_name from lib_company", "id", "company_name");

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id = str_replace("'", "",$cbo_company_id);
    $com_id_arr = explode(",",$company_id);
	//  ================== making query condition =========================
	if($buyer_id==0) $buyer_cond=""; else $buyer_cond="and a.buyer_name=$buyer_id";
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond=""; else $date_cond=" and a.production_date between $txt_date_from and $txt_date_to";
	if(str_replace("'","",trim($txt_date_from))=="" && str_replace("'","",trim($txt_date_to))=="") $date_cond_resource=""; else $date_cond_resource=" and b.pr_date between $txt_date_from and $txt_date_to";

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

    // =========================== get resource data ===========================
    $sql="SELECT a.id,a.company_id,b.pr_date, b.man_power, b.operator, b.helper, b.working_hour,b.target_per_hour,b.smv_adjust,b.smv_adjust_type from  prod_resource_dtls b,prod_resource_mst a  where a.id=b.mst_id and a.company_id in($company_id) $date_cond_resource and a.is_deleted=0 and b.is_deleted=0";
    // echo $sql;
    $res = sql_select($sql);
    $resource_data_array = array();
    foreach ($res as $v) 
    {
        if(str_replace("'","",$v['SMV_ADJUST_TYPE'])==1) $total_adjustment=$v['SMV_ADJUST'];
		if(str_replace("'","",$v['SMV_ADJUST_TYPE'])==2) $total_adjustment=($v['SMV_ADJUST'])*(-1);
        $resource_data_array[date('M-Y',strtotime($v['PR_DATE']))][$v['COMPANY_ID']] += $total_adjustment + $v['MAN_POWER']*$v['WORKING_HOUR']*60;
    }
	
    // ======================================== prod data ================================
    $sql="SELECT d.style_ref_no, a.po_break_down_id,a.item_number_id,a.serving_company, a.production_date,a.production_type,b.production_qnty from PRO_GARMENTS_PRODUCTION_DTLS b,PRO_GARMENTS_PRODUCTION_MST a, WO_PO_BREAK_DOWN c,WO_PO_DETAILS_MASTER d where b.mst_id=a.id and a.po_break_down_id=c.id and c.job_id=d.id and a.production_type in(5,80) $date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.serving_company in($company_id) order by a.production_date";
    // echo $sql;die;
    $res = sql_select($sql);

    if(count($res)<1)
    {
        echo "<div style='text-align:center;font-weight:bold;color:red;'>Data not found. Please try again.</div>";
        die();
    }
    $data_array = array();
    $style_count_array = array();
    $style_count_chk_array = array();
    foreach ($res as $v) 
    {
        $data_array[$v['SERVING_COMPANY']][date('M-Y',strtotime($v['PRODUCTION_DATE']))][$v['PRODUCTION_TYPE']]['qty'] += $v['PRODUCTION_QNTY'];
        if($style_count_chk_array[$v['SERVING_COMPANY']][date('M-Y',strtotime($v['PRODUCTION_DATE']))]!=$v["STYLE_REF_NO"])
        {
            $style_count_array[$v['SERVING_COMPANY']][date('M-Y',strtotime($v['PRODUCTION_DATE']))]++;
            $style_count_chk_array[$v['SERVING_COMPANY']][date('M-Y',strtotime($v['PRODUCTION_DATE']))]=$v["STYLE_REF_NO"];
        }
        $po_id_arr[$v['PO_BREAK_DOWN_ID']] = $v['PO_BREAK_DOWN_ID'];
    }
    // echo "<pre>";print_r($data_array);

    $po_id_cond = where_con_using_array($po_id_arr,0,"b.id");
    
    // ====================================== smv data ================================
    $smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($company_id) and variable_list=25 and   status_active=1 and is_deleted=0");
    if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
    // echo $smv_source;die;
    if($smv_source==3)
	{
		$sql_item="SELECT b.id, a.TOTAL_SMV, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_id_cond";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
		    $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('TOTAL_SMV')];
		}
	}
	else
	{
		$sql_item="SELECT b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, smv_pcs, smv_pcs_precost from wo_po_details_master a,wo_po_break_down b, wo_po_details_mas_set_details c where a.id=b.job_id and b.job_id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $po_id_cond";
		$resultItem=sql_select($sql_item);
		foreach($resultItem as $itemData)
		{
			if($smv_source==1)
			{
			    $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs')];
			}
			if($smv_source==2)
			{
			    $item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('smv_pcs_precost')];
			}
		}
	}
	//  print_r($item_smv_array);

    foreach ($res as $v) 
    {
        $data_array[$v['SERVING_COMPANY']][date('M-Y',strtotime($v['PRODUCTION_DATE']))][$v['PRODUCTION_TYPE']]['prod_min'] += $v['PRODUCTION_QNTY']*$item_smv_array[$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']];
        $data_array[$v['SERVING_COMPANY']][date('M-Y',strtotime($v['PRODUCTION_DATE']))][$v['PRODUCTION_TYPE']]['smv'] += $item_smv_array[$v['PO_BREAK_DOWN_ID']][$v['ITEM_NUMBER_ID']];
    }

    // ============================= sewing defect data ==============================
    $sql = "SELECT a.SERVING_COMPANY,A.PRODUCTION_DATE,a.PRODUCTION_TYPE,  B.DEFECT_QTY AS DEFECT_QTY
    FROM pro_garments_production_mst a, pro_gmts_prod_dft b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.defect_type_id !=3  and B.DEFECT_QTY>0  and a.PRODUCTION_TYPE=5 and a.serving_company in($company_id) $date_cond";
    // echo $sql; die;
    $res = sql_select($sql); 
    $defect_data_array = array();
    $dtls_id_array = array();
    foreach ($res as $v) 
	{
		$defect_data_array[$v['SERVING_COMPANY']][date('M-Y',strtotime($v['PRODUCTION_DATE']))][$v['PRODUCTION_TYPE']]['defect_qty'] += $v['DEFECT_QTY'];		
    }

    // ============================= sewing defect data ==============================
    $sql = "SELECT a.SERVING_COMPANY,A.PRODUCTION_DATE,a.PRODUCTION_TYPE,  B.DEFECT_QTY AS DEFECT_QTY
    FROM pro_garments_production_mst a, pro_gmts_prod_dft b where a.id=b.mst_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.defect_type_id !=3  and B.DEFECT_QTY>0  and a.PRODUCTION_TYPE=80 and a.serving_company in($company_id) $date_cond";
    // echo $sql; die;
    $res = sql_select($sql);
    foreach ($res as $v) 
	{
		$defect_data_array[$v['SERVING_COMPANY']][date('M-Y',strtotime($v['PRODUCTION_DATE']))][$v['PRODUCTION_TYPE']]['defect_qty'] += $v['DEFECT_QTY'];		
    }
    // echo "<pre>"; print_r($defect_data_array);

	$colspan = 13*count($com_id_arr);
	$tbl_width = 660*count($com_id_arr);
	ob_start();
	?>
    <fieldset style="width:<? echo $tbl_width+20;?>px">
        <table width="<? echo $tbl_width;?>" cellpadding="0" cellspacing="0"> 
            <tr class="form_caption">
            	<td colspan="<? echo $colspan;?>" align="center"><strong>Production and DHU Comparison Report</strong></td> 
            </tr>
        </table>
        <?
        $color_arr = array(0=>"#7FC7D9",1=>"#B19470",2=>"#86A7FC",3=>"#E6A4B4",4=>"#FFBB64",5=>"#C499F3");
        $i=0;        
        $month_wise_data_arr = array();
        foreach ($data_array as $com_id => $com_data) 
        {
            ?>
            <table id="table_header" class="rpt_table" width="660" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                        <td style="background-color: <?=$color_arr[$i];?>; text-align:center;padding:4px 0;font-weight:bold;" colspan="13"><?=$lib_company[$com_id];?></td>
                    </tr>   
                    <tr>                   
                        <th width="60">Month</th>
                        <th width="100" colspan="2">Prod Qty</th>
                        <th width="50">Total Style</th>
                        <th width="50">Availble Min.</th>
                        <th width="50">Availble SMV</th>
                        <th width="50">Availble Effi.%</th>
                        <th width="100" colspan="2">Sewing DHU%</th>
                        <th width="100" colspan="2">Finish DHU%</th>
                        <th width="100" colspan="2">Total DHU%</th>
                    </tr>                  
                </thead>
                <tbody>
                    <?
                    $gr_tot_prod_arr = array();
                    $gr_tot_sew_dhu_arr = array();
                    $gr_tot_fin_dhu_arr = array();
                    $gr_tot_dhu_arr = array();
                    $check_company_arr = array();
                    $prev_qty = 0;
                    // ksort($data_array);
                    $m=0;
                    foreach ($com_data as $month_key => $m_value) 
                    {                       
                        ?>
                        <tr>
                        <? 
                            $avg_smv = $m_value[5]['smv']/$style_count_array[$com_id][$month_key];
                            $prod_min = $m_value[5]['prod_min'];
                            $effi_min = $resource_data_array[$month_key][$com_id];
                            $avail_effi =  ($prod_min/$effi_min)*100;
                            $sewing_dft = ($defect_data_array[$com_id][$month_key][5]['defect_qty']/$m_value[5]['qty'])*100;
                            $fin_dft = ($defect_data_array[$com_id][$month_key][80]['defect_qty']/$m_value[80]['qty'])*100;
                            $tot_dft = $sewing_dft+$fin_dft;
                        
                            $dif_qty = 0;
                            $sew_dhu_dif = 0;
                            $fin_dhu_dif = 0;
                            $tot_dhu_dif = 0;
                            if($prev_qty>0)
                            {
                                $dif_qty = $m_value[5]['qty'] - $prev_qty ;
                                if($dif_qty>0)
                                {
                                    $up_down = "&#x25B2;";
                                    $font_color = "green";
                                }
                                else
                                {
                                    $up_down = "&#x25BC;";
                                    $font_color = "red";
                                }
                            }
                            if($prev_sewing_dft>0)
                            {
                                
                                $sew_dhu_dif = $sewing_dft - $prev_sewing_dft ;
                                if($sew_dhu_dif>0)
                                {
                                    $sew_up_down = "&#x25BC;";
                                    $sew_font_color = "red";
                                }
                                else
                                {
                                    
                                    $sew_up_down = "&#x25B2;";
                                    $sew_font_color = "green";
                                }
                            }
                            if($prev_fin_dft>0)
                            {    
                                $fin_dhu_dif = $fin_dft - $prev_fin_dft ;
                                if($fin_dhu_dif>0)
                                {
                                    $fin_up_down = "&#x25BC;";
                                    $fin_font_color = "red";
                                }
                                else
                                {
                                    $fin_up_down = "&#x25B2;";
                                    $fin_font_color = "green";
                                }
                            }
                            if($total_dft>0)
                            {                                
                                $tot_dhu_dif = $tot_dft - $total_dft ;
                                if($tot_dhu_dif>0)
                                {
                                    $tot_up_down = "&#x25BC;";
                                    $tot_font_color = "red";
                                }
                                else
                                {
                                    $tot_up_down = "&#x25B2;";
                                    $tot_font_color = "green";
                                }
                               
                            }
                            // echo $m_value[$com_id][5] ."-". $prev_qty."<br>" ;
                            ?>
                            
                            <td><?=$month_key;?></td>
                            <td width="50" align="right"><?=number_format($m_value[5]['qty'],0);?></td>
                            <td width="50" align="right" style="color: <?= $font_color;?>;"><?=number_format($dif_qty,0);?> <?=$up_down;?></td>
                            <td align="right"><?=$style_count_array[$com_id][$month_key];?></td>
                            <td align="right"><?=$resource_data_array[$month_key][$com_id];?></td>
                            <td align="right"><?=number_format($avg_smv,2);?></td>
                            <td align="right"><?=number_format($avail_effi,2);?></td>
                            <td width="50" align="right"><?=number_format($sewing_dft,2);?></td>
                            <td width="50" align="right" style="color: <?= $sew_font_color;?>;"><?=number_format($sew_dhu_dif,2);?><?=$sew_up_down;?></td>
                            <td width="50" align="right"><?=number_format($fin_dft,2);?></td>
                            <td width="50" align="right" style="color: <?= $fin_font_color;?>;"><?=number_format($fin_dhu_dif,2);?><?=$fin_up_down;?></td>
                            <td width="50" align="right"><?=number_format($tot_dft,2);?></td>
                            <td width="50" align="right" style="color: <?= $tot_font_color;?>;"><?=number_format($tot_dhu_dif,2);?><?=$tot_up_down;?></td>
                            <?
                            $m++;
                            $prev_qty = $m_value[5]['qty'];
                            $prev_sewing_dft = $sewing_dft;
                            $prev_fin_dft = $fin_dft;
                            $total_dft = $tot_dft;
                            $month_wise_data_arr[$com_id][$month_key]['tot_sewing'] += $m_value[5]['qty'];
                            $month_wise_data_arr[$com_id][$month_key]['tot_fin'] += $m_value[5]['qty'];
                            $month_wise_data_arr[$com_id][$month_key]['tot_dft'] += $tot_dft;
                            $gr_tot_prod[$com_id]['tot_prod'] += $m_value[5]['qty'];
                            $gr_tot_sew_dhu[$com_id]['sewing_dft'] += $sewing_dft;
                            $gr_tot_fin_dhu[$com_id]['fin_dft'] += $fin_dft;
                            $gr_tot_dhu[$com_id]['tot_dft'] += $tot_dft;
                            $month_arr[$month_key] = $month_key;
                      
                        ?>
                        </tr>
                        <?                     
                       
                    }
                    ?>
                    
                </tbody>
                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th><?=number_format($gr_tot_prod[$com_id]['tot_prod'],0)?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><?=number_format($gr_tot_sew_dhu[$com_id]['sewing_dft'],2)?></th>
                        <th></th>
                        <th><?=number_format($gr_tot_fin_dhu[$com_id]['fin_dft'],2)?></th>
                        <th></th>
                        <th><?=number_format($gr_tot_dhu[$com_id]['tot_dft'],2)?></th>
                        <th></th>
                                                 
                    </tr>
                    <tr>
                        <th>Avg/Month</th>
                        <th><?=number_format(($gr_tot_prod[$com_id]['tot_prod']/$m),0)?></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><?=number_format(($gr_tot_sew_dhu[$com_id]['sewing_dft']/$m),2)?></th>
                        <th></th>
                        <th><?=number_format(($gr_tot_fin_dhu[$com_id]['fin_dft']/$m),2)?></th>
                        <th></th>
                        <th><?=number_format(($gr_tot_dhu[$com_id]['tot_dft']/$m),2)?></th>
                        <th></th>                                                
                    </tr>
                </tfoot>
            </table>   
            <?
            $i++;
            }
        ?>         
           
    </fieldset>
    	 
    <script src="../../js/highchart/highcharts.js"></script>
    <script src="../../js/highchart/highcharts-3d.js"></script>
    <script src="../../js/highchart/exporting.js"></script>
    <div>
    <?
    // echo "<pre>";print_r($month_wise_data_arr);
    $month =  "'".implode("','",$month_arr)."'";
    $i=1;
    $width = 96/count($com_id_arr);
    foreach ($data_array as $com_id => $com_data) 
    { 
        $prod_qty = "";
        $dhu_qty = "";
        foreach ($com_data as $month_key=>$v) 
        {
            $prod_qty.= ($prod_qty=="") ?  $v[5]['qty'] : ",".$v[5]['qty'];
            $dhu_qty.= ($dhu_qty=="") ?  number_format($month_wise_data_arr[ $com_id][$month_key]['tot_dft'],2,'.','') : ",".number_format($month_wise_data_arr[ $com_id][$month_key]['tot_dft'],2,'.','');
        }
        // echo $dhu_qty;die;
        ?>
        <div id="chart_container_<?=$i;?>" style="width: <?=$width;?>%; float:left;"></div>
        <script>
           var month = '<?=json_encode($month_arr);?>';
            Highcharts.chart('chart_container_<?=$i;?>', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: 'Production and DHU Comparison',
                    align: 'center',
                    fontWeight: '800'
                },
                subtitle: {
                    text: '<?=$lib_company[$com_id];?>',
                    align: 'center',
                    fontWeight: 'bold',
                    color: 'red'
                },
                xAxis: {
                    categories: [<?=$month;?>],
                    crosshair: true,
                    accessibility: {
                        description: 'Month'
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Production Qnty and DHU Percent'
                    }
                },
                tooltip: {
                    valueSuffix: ' '
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    }
                },
                series: [
                    {
                        name: 'Production',
                        data: [<?=$prod_qty;?>]
                    },
                    {
                        name: 'DHU%',
                        data: [0.04,7.42,0.11,0.00]
                    }
                ]
            });

        </script>
	    <?    
        $i++;
    }
    ?>
    </div>
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
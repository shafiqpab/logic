<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
// ----------------------------------------------------------------------------------------------------------------
if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company = str_replace("'","",$cbo_company_name);
	$year = str_replace("'","",$cbo_year);
	$month_to = str_replace("'","",$cbo_to_month);
	$production_running = str_replace("'","",$txt_production_running);
	
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year);
	$end_date=$year."-".$month_to."-$num_days";

	if ($month_to>9) 
	{
		//echo $month_to.'=string';die;
		$end_date2=$year."-".$month_to."-01";
	}
	else {
		//echo $month_to.'=string2';die;
		$end_date2=$year."-0".$month_to."-01";
	}
	// echo $end_date2;die;
	//$end_date2=$year."-0".$month_to."-01";
    $start_date = date('Y-m-d', strtotime($end_date2.'-1 month'));
    // echo $start_date = date('Y-m-d', strtotime($end_date2.'-1 month')).'='.$end_date;
    //echo $end_date2.'='.$start_date.'='.date("j-M-Y",strtotime($start_date));die;

	if(trim($year)!=0) $year_cond=" $year_field_by=$year"; else $year_cond="";
	if($db_type==0) 
	{
		$date_cond=" and a.receive_date between '$start_date' and '$end_date'";
		$order_by_cond="DATE_FORMAT(a.receive_date, '%Y%m')";
	}
	if($db_type==2) 
	{
		//echo "sdsdsd";die;
		$date_cond=" and a.receive_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$order_by_cond="to_char(a.receive_date,'YYYY-MM')";
	}
	// echo $date_cond;die;
	// ========================================================================================================
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$body_part_type_library=return_library_array("select id,body_part_type from lib_body_part where STATUS=1 and status_active=1 and IS_DELETED=0", "id", "body_part_type");
	$machine_library=return_library_array("select id, machine_no from lib_machine_name where company_id=$company and status_active=1 and is_deleted=0 and category_id=1", "id", "machine_no");
	// echo count($machine_library);

	$sql_result="SELECT A.RECEIVE_DATE, B.GREY_RECEIVE_QNTY AS GREY_RECEIVE_QNTY, B.GREY_RECEIVE_QNTY_PCS AS GREY_RECEIVE_QNTY_PCS, B.BODY_PART_ID, C.ID AS MC_ID, C.MACHINE_NO, C.MACHINE_GROUP, C.PROD_CAPACITY, C.DIA_WIDTH, C.GAUGE
	from inv_receive_master a, pro_grey_prod_entry_dtls b, lib_machine_name c
	where a.id=b.mst_id and b.machine_no_id=c.id and a.entry_form=2 and a.item_category=13 
	and b.machine_no_id!=0 and a.knitting_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.category_id=1 and a.knitting_company=$company $date_cond";//  order by A.RECEIVE_DATE
	 // echo $sql_result;die;
	$sql_result_arr=sql_select($sql_result);
	// print_r($sql_result_arr);
	$data_arr=array();$date_data_arr_pcs=array();$date_total_arr=array();
	foreach ($sql_result_arr as $row)
	{
		// echo $end_date2.'='.$row['RECEIVE_DATE'].'<br>';
		//compare the dates
		if(strtotime($end_date2) <= strtotime($row['RECEIVE_DATE']))
		{
		   	//convert the date back to underscore format if needed when printing it out.
		   	// echo '1 is small='.strtotime($end_date2).','.date('d_m_y',strtotime($end_date2)).'<br>';
			$data_arr[$row['MC_ID']][RECEIVE_DATE]=$row['RECEIVE_DATE'];
			$data_arr[$row['MC_ID']][MACHINE_NO]=$row['MACHINE_NO'];
			$data_arr[$row['MC_ID']][MACHINE_GROUP]=$row['MACHINE_GROUP'];
			
			$data_arr[$row['MC_ID']][DIA_WIDTH]=$row['DIA_WIDTH'];
			$data_arr[$row['MC_ID']][GAUGE]=$row['GAUGE'];
			$data_arr[$row['MC_ID']][PRODUCTION_QTY]+=$row['GREY_RECEIVE_QNTY'];			

			$to_month_summary_production_qty+=$row['GREY_RECEIVE_QNTY'];
			
			if ($mc_id_check[$row['MC_ID']]=="") 
			{
				$mc_id_check[$row['MC_ID']]=$row['MC_ID'];
				$to_month_total_mc+=count($row['MC_ID']);
				$data_arr[$row['MC_ID']][PROD_CAPACITY]+=$row['PROD_CAPACITY'];
			}
			if($body_part_type_library[$row['BODY_PART_ID']]==40 || $body_part_type_library[$row['BODY_PART_ID']]==50) // Flat Knit or Cuff
			{
				// $to_month_summary_flat_qty+=$row['GREY_RECEIVE_QNTY_PCS'];
				$to_month_summary_flat_qty+=$row['GREY_RECEIVE_QNTY'];
			}
		}
		else
		{
		   	//echo '2 is small='.strtotime($row['RECEIVE_DATE']).','.date('d_m_y',strtotime($row['RECEIVE_DATE'])).'<br>';
			$previ_month_summary_production_qty+=$row['GREY_RECEIVE_QNTY'];			
			if ($pre_mc_id_check[$row['MC_ID']]=="") 
			{
				$pre_mc_id_check[$row['MC_ID']]=$row['MC_ID'];
				$previ_month_to_total_mc+=count($row['MC_ID']);
			}
			if($body_part_type_library[$row['BODY_PART_ID']]==40 || $body_part_type_library[$row['BODY_PART_ID']]==50) // Flat Knit or Cuff
			{
				// $previ_month_summary_flat_qty+=$row['GREY_RECEIVE_QNTY_PCS'];
				$previ_month_summary_flat_qty+=$row['GREY_RECEIVE_QNTY'];
			}
		}
	}
	
	// echo '<pre>';print_r($data_arr);die;
	// ========================================================================================================
	ob_start();

	$dtls_div_width=1030;
	$dtls_table_width=1010;
	?>
	<style type="text/css">
		.word_break {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
    <div align="">
        <fieldset style="width:1030px;">
            <div  align="center"> <strong> <? echo $company_library[$company]; ?> </strong></div>
            <!-- Summary Batch Start-->
            <div>
                <table cellpadding="0"  width="<? echo $dtls_table_width;?>" cellspacing="0"  class="rpt_table" rules="all" border="1">
					<thead>
						<tr>
							<th>Particular</th>
							<th colspan="2">Knitting Production Summary Report <?=date('M', strtotime($end_date));?></th>
							<th colspan="2">Summary Report <?=date('M', strtotime($end_date2.'-1 month'));?></th>
							<th rowspan="2">Increase/Decrease-KG</th>
							<th rowspan="2">%</th>
						</tr>
						<tr>
							<th></th>
							<th align="right">Qty./Kg</th>
							<th align="right">Remarks</th>
							<th align="right">Qty./Kg</th>
							<th align="right">Remarks</th>
						</tr>
					</thead>
					<tbody>
		                <tr bgcolor="#E9F3FF">
							<td>Total Knitting Production</td>
							<td align="right"><?=number_format($to_month_summary_production_qty,2,'.','');?></td>
							<td align="right"></td>

							<td align="right"><?=number_format($previ_month_summary_production_qty,2,'.','');?></td>
							<td align="right"></td>
							<td align="right"><?=$increase_decrease=number_format($to_month_summary_production_qty-$previ_month_summary_production_qty,2,'.','');?></td>
							<td align="right"><?=number_format($increase_decrease/$to_month_summary_production_qty*100,2,'.','');?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Flat Knit Production</td>
							<td align="right"><?=number_format($to_month_summary_flat_qty,2,'.','');?></td>
							<td align="right"></td>

							<td align="right"><?=number_format($previ_month_summary_flat_qty,2,'.','');?></td>
							<td align="right"></td>
							<td align="right"><?=$flat_knit_increase_decrease=number_format($to_month_summary_flat_qty-$previ_month_summary_flat_qty,2,'.','');?></td>
							<td align="right"><? 
								if ($previ_month_summary_flat_qty>0) {
								echo number_format($flat_knit_increase_decrease/$previ_month_summary_flat_qty*100,2,'.','');
								} ?>	
							</td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Avg. Knitting Production Per Day</td>
							<td align="right"><?=$to_month_production_per_day=number_format($to_month_summary_production_qty/$production_running,2,'.','');?></td>
							<td align="center"><?=$production_running;?> Day Production Running</td>
							
							<td align="right"><?=$previ_month_production_per_day=number_format($previ_month_summary_production_qty/$production_running,2,'.','');?></td>
							<td align="center"><?=$production_running;?> Day Production Running</td>
							<td align="right"><?=$per_day_increase_decrease=number_format($to_month_production_per_day-$previ_month_production_per_day,2,'.','');?></td>
							<td align="right"><?=number_format($per_day_increase_decrease/$to_month_production_per_day*100,2,'.','');?></td>
						</tr>
						<tr bgcolor="#FFFFFF">
							<td>Avg. Knitting Production Per Day Per Machine</td>
							<td align="right" title="Avg.Knitting Production Per Day / Total Knitting M/C"><?=$to_per_day_per_mc=number_format($to_month_production_per_day/count($machine_library),2,'.','');?></td>
							<td align="center"><?=$to_month_total_mc?> Knitting Machine</td>
							
							<td align="right" title="Avg.Knitting Production Per Day / Total Knitting M/C"><?=$previ_per_day_per_mc=number_format($previ_month_production_per_day/count($machine_library),2,'.','');?></td>
							<td align="center"><?=$previ_month_to_total_mc?> Knitting Machine</td>
							<td align="right"><?=$per_day_increase_decrease_mc=number_format($to_per_day_per_mc-$previ_per_day_per_mc,2,'.','');?></td>
							<td align="right"><? 
								if ($previ_per_day_per_mc>0) {
									echo number_format($per_day_increase_decrease_mc/$previ_per_day_per_mc*100,2,'.','');
								} ?>
							</td>
						</tr>
						<tr bgcolor="#E9F3FF">
							<td>Idle Machine</td>
							<td align="right" title="Total knitting M/C:<?=count($machine_library);?>"><?=count($machine_library)-$to_month_total_mc;?></td>
							<td align="right"></td>

							<td align="right"><?=count($machine_library)-$previ_month_to_total_mc;?></td>
							<td align="right"></td>
							<td align="right"></td>
							<td align="right"></td>
						</tr>
					</tbody>
				</table>
            </div>
            <!-- Summary Batch End-->

            <br>

            <!-- Details Start-->
            <div>
                <table class="rpt_table" width="<? echo $dtls_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <thead>
                        <tr>
                            <th width="30" class="word_break">SL</th>
                            <th width="120" class="word_break">M/C No</th>
                            <th width="120" class="word_break">M/c Type</th>
                            <th width="120" class="word_break">M/c Dia GG</th>
                            <th width="120" class="word_break">Avg. Production Target Per Day</th>
                            <th width="120" class="word_break">Avg. Monthly Target</th>
                            <th width="120" class="word_break">Total Production </th>
                            <th width="120" class="word_break">Avg. Production Target Achievement</th>
                            <th width="" class="word_break">Avg. Production Per Day</th>
                        </tr>
                    </thead>
                </table>
                <div style=" max-height:350px; width:<?echo $dtls_div_width;?>px; overflow-y:scroll;" id="scroll_body_inbound">
                    <table class="rpt_table" id="table_body2" width="<? echo $dtls_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                        <tbody>
                            <?
                            $j=1;
                            foreach($data_arr as $mc_id => $row)
                            {
								if($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                ?>
	                            <tr bgcolor="<? echo $bgcolor; ?>" id="tr2_<? echo $j; ?>" onClick="change_color('tr2_<? echo $j; ?>','<? echo $bgcolor; ?>')">
                                    <td width="30" class="word_break"><? echo $j; ?></td>
                                    <td width="120" class="word_break"><?= $row[MACHINE_NO];?></td>
                                    <td width="120" class="word_break"><?= $row[MACHINE_GROUP];?></td>
                                    <td width="120" class="word_break"><?= $row[DIA_WIDTH].'x'.$row[GAUGE];?></td>
                                    <td width="120" class="word_break" align="right" title="MC Capacity"><?= $capacity=number_format($row[PROD_CAPACITY],2,'.','');?></td>
                                    <td width="120" class="word_break" align="right" title="MC Capacity*Production Running"><?= $monthly_target=number_format($row[PROD_CAPACITY]*$production_running,2,'.','');?></td>
                                    <td width="120" class="word_break" align="right"><?= number_format($row[PRODUCTION_QTY],2,'.','');?></td>
                                    <td width="120" class="word_break" align="right" title="Production Qty/Avg Monthly Target"><? 
	                                    if ($monthly_target>0) 
	                                    {
	                                    	echo $target_achievement=number_format($row[PRODUCTION_QTY]/$monthly_target*100,2,'.','').'%';
	                                    } ?>
                                    </td>
                                    <td width="" class="word_break" align="right" title="Production Qty/Production Running"><?= $production_per_day=number_format($row[PRODUCTION_QTY]/$production_running,2,'.','');?></td>
                                </tr>
                                <?
                                $j++;
                                $tot_capacity+=$capacity;
                                $tot_monthly_target+=$monthly_target;
                                $tot_production_qnty+=$row[PRODUCTION_QTY];
                                $tot_target_achievement+=$target_achievement;
                                $tot_production_per_day+=$production_per_day;
		                    }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="rpt_table" width="<? echo $dtls_table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                    <tfoot>
                        <tr>
                        	<th width="30">&nbsp;</th>  
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="120">TOTAL (KG)</th>
                            <th width="120"><?= number_format($tot_capacity,2,'.',''); ?></th>
                            <th width="120"><?= number_format($tot_monthly_target,2,'.',''); ?></th>
                            <th width="120"><?= number_format($tot_production_qnty,2,'.',''); ?></th>
                            <th width="120" title="<?=$tot_production_qnty.'/'.$tot_monthly_target;?>"><?= number_format($tot_production_qnty/$tot_monthly_target*100,2,'.','').'%'; ?></th>
                            <th width=""><?= number_format($tot_production_qnty/$production_running,2,'.',''); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- Details End-->
        </fieldset>
    </div>
	<?

    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
        @unlink($filename);
    }
    $name=time();
    $filename=$user_name."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}

?>
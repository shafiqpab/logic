<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );

if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";

if($action=="report_generate")
{ 
	extract($_REQUEST);
    
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');

    $company_id 	= str_replace("'", "", $cbo_company_id);
	$start_year 	= str_replace("'","",$cbo_start_year);
	$end_year 		= str_replace("'","",$cbo_end_year);
	$start_month 	= str_replace("'","",$cbo_start_month);
	$end_month 		= str_replace("'","",$cbo_end_month); 
    $type 			= str_replace("'", "", $type);

    $daysinmonth=cal_days_in_month(CAL_GREGORIAN, $end_month, $start_year);
	$s_date=$start_year."-".$start_month."-"."01";
	$e_date=$end_year."-".$end_month."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}

	$tot_month = datediff( 'm', $s_date,$e_date);
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($s_date,$i);
		$month_arr[]=date("Y-m",strtotime($next_month));
		$fullMonthArr[]=date("M-Y",strtotime($next_month));
	}
	// echo "<pre>";print_r($fullMonthArr);
	// echo $s_date."==".$e_date;

    $sql_cond = "";
    $sql_cond .= ($company_id!=0) ? " and d.serving_company=$company_id" : "";
    $sql_cond .= ($s_date!="") ? " and d.production_date between '$s_date' and '$e_date'" : "";
    $qc_date .= ($s_date!="") ? " and a.cutting_qc_date between '$s_date' and '$e_date'" : "";

    $sql = "SELECT a.buyer_name,a.job_no,a.style_ref_no as style,a.gauge,to_char(d.production_date,'MON-YYYY') as pdate,e.production_qnty as qc_pass_qty,e.defect_qty,e.reject_qty from wo_po_details_master a,wo_po_break_down b, wo_po_color_size_breakdown c,pro_garments_production_mst d,pro_garments_production_dtls e where a.id=b.job_id and b.id=c.po_break_down_id and a.id=c.job_id and b.id=d.po_break_down_id and c.id=e.color_size_break_down_id and d.id=e.mst_id and d.production_type=52 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.is_rescan=0 $sql_cond order by d.production_date";
    // echo $sql;die();
    $res = sql_select($sql);
    if(count($res)==0)
    {
    	echo "<div style='text-align:center;color:red;font-size:20px;'>Data not found!</div>";
    	die();
    }
    $guage_wise_data_array = array();
    $yearly_data_array = array();
    $style_array = array();
    $style_job_array = array();
    $job_array = array();
    $particular_data_array = array();
    foreach ($res as $val) 
    {
    	$gauge = "";
    	/*if($val['GAUGE']==2 || $val['GAUGE']==3 || $val['GAUGE']==4)
    	{
    		$gauge = "Course Guage";
    	}
    	elseif ($val['GAUGE']==6) 
    	{
    		$gauge = "Fine Guage";
    	}*/

    	if($val['GAUGE']==2 || $val['GAUGE']==3 || $val['GAUGE']==4 || $val['GAUGE']==1 || $val['GAUGE']==5 || $val['GAUGE']==8 || $val['GAUGE']==9 || $val['GAUGE']==10)
    	{
    		$gauge = "Course Guage";
    	}
    	elseif ($val['GAUGE']==6 || $val['GAUGE']==7 || $val['GAUGE']==11) 
    	{
    		$gauge = "Fine Guage";
    	}

    	$guage_wise_data_array[$val['PDATE']][$gauge]['qc_pass_qty'] += $val['QC_PASS_QTY'];
    	$guage_wise_data_array[$val['PDATE']][$gauge]['defect_qty'] += $val['DEFECT_QTY'];
    	$guage_wise_data_array[$val['PDATE']][$gauge]['reject_qty'] += $val['REJECT_QTY'];

    	$style_array[$val['STYLE']] = $val['STYLE'];
    	$job_array[$val['JOB_NO']] = $val['JOB_NO'];
    	$style_job_array[$val['STYLE']] = $val['JOB_NO'];
    }

    // echo "<pre>";print_r($guage_wise_data_array);die();
   

    $style_cond = where_con_using_array($style_array,1,"a.style_ref");
    $job_cond = where_con_using_array($job_array,1,"a.job_no");

    // ========================= getting smv ===========================
    $smv_arr = return_library_array( "SELECT a.style_ref, b.total_smv from ppl_gsd_entry_mst a, ppl_gsd_entry_dtls b where a.id=b.mst_id and b.lib_sewing_id=20 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $style_cond",'style_ref','total_smv'); // lib_sewing_id 20 for live, 591 for dev
    // print_r($smv_arr);

    // ========================= getting bundle qty ===========================
    $bundle_sql = "SELECT c.gauge,a.job_no,to_char(a.cutting_qc_date,'MON-YYYY') as qc_date,a.loss_min, sum(b.bundle_qty) as bundle_qty from pro_gmts_cutting_qc_mst a, pro_gmts_cutting_qc_dtls b, wo_po_details_master c,wo_po_break_down d where a.id=b.mst_id and c.id=d.job_id and d.id=b.order_id and c.job_no=a.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and d.status_active=1 and b.is_rescan=0 $job_cond $qc_date group by c.gauge,a.job_no,a.cutting_qc_date,a.loss_min";
    // echo $bundle_sql;
    $bundle_res = sql_select($bundle_sql);
    $bundle_qty_arr = array();
    $yearly_bundle_qty_arr = array();
    foreach ($bundle_res as $val) 
    {
    	$gauge = "";
    	/*if($val['GAUGE']==2 || $val['GAUGE']==3 || $val['GAUGE']==4)
    	{
    		$gauge = "Course Guage";
    	}
    	elseif ($val['GAUGE']==6) 
    	{
    		$gauge = "Fine Guage";
    	}*/

    	if($val['GAUGE']==2 || $val['GAUGE']==3 || $val['GAUGE']==4 || $val['GAUGE']==1 || $val['GAUGE']==5 || $val['GAUGE']==8 || $val['GAUGE']==9 || $val['GAUGE']==10)
    	{
    		$gauge = "Course Guage";
    	}
    	elseif ($val['GAUGE']==6 || $val['GAUGE']==7 || $val['GAUGE']==11) 
    	{
    		$gauge = "Fine Guage";
    	}

    	$bundle_qty_arr[$val['QC_DATE']][$gauge] += $val['BUNDLE_QTY'];
    	$yearly_bundle_qty_arr[$val['QC_DATE']]['qty'] += $val['BUNDLE_QTY'];
    	$yearly_bundle_qty_arr[$val['QC_DATE']]['loss_min'] += $val['LOSS_MIN'];
    }

    // print_r($bundle_qty_arr);

    if($type==2) // for yearly button
    {
	    foreach ($res as $val) 
	    {
	    	$yearly_data_array[$val['PDATE']]['qc_pass_qty'] += $val['QC_PASS_QTY'];
	    	$yearly_data_array[$val['PDATE']]['defect_qty'] += $val['DEFECT_QTY'];
	    	$yearly_data_array[$val['PDATE']]['reject_qty'] += $val['REJECT_QTY'];
	    	$yearly_data_array[$val['PDATE']]['prod_min'] += $val['QC_PASS_QTY']*$smv_arr[$val['STYLE']];
	    }
	}
	// echo "<pre>";print_r($yearly_data_array);
   // ========================== for chart =======================
    if($type==1)
    {
	    $month_name_arr = array();
	    $month_total_defect = array();
	    $month_total_reject = array();
	    foreach ($guage_wise_data_array as $month => $month_data) 
	    {
	    	foreach ($month_data as $guage => $value) 
	    	{
		    	$qcQty = $bundle_qty_arr[$month][$guage];
		    	$month_name_arr[$month] = $month;
		    	$month_total_defect[$month] = ($qcQty) ? number_format((($value['defect_qty']/$qcQty)*100),2) : 0;
		    	$month_total_reject[$month] = ($qcQty) ? number_format((($value['reject_qty']/$qcQty)*100),2) : 0;
		    	// echo $value['defect_qty']."/".$qcQty."dfgfdgd<br>";
		    }
	    }

	     // echo "<pre>";print_r($month_total_defect);die();
	}

	ob_start();
	if($type==1)
	{
		?>
		<fieldset style="width: 610px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Monthly Guage Wise Inspection Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($s_date); ?>To<?=change_date_format($e_date); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="590"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="80">Guage</th>
	             			<th width="80">QC Qty.</th>
	             			<th width="80">QC Pass Qty.</th>
	             			<th width="80">Alter Qty.</th>
	             			<th width="80">Alter%</th>
	             			<th width="80">Damage Qty.</th>
	             			<th width="80">Damage%</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:610px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="590"  align="center" id="table_body">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$tot_qc_pass_qty = 0;
		             		$tot_qc_qty = 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
		             		foreach ($guage_wise_data_array as $pdate => $date_val) 
		             		{
			             		$date_wise_qc_pass_qty = 0;
			             		$date_wise_qc_qty = 0;
			             		$date_wise_defect_qty = 0;
			             		$date_wise_reject_qty = 0;
			             		?>
			             		<tr style="font-weight: bold;background: #dccdcd;font-size: 20px;">
			             			<td colspan="8">Month : <?=date('F-Y',strtotime($pdate));?></td>
			             		</tr>
			             		<?
		             			foreach ($date_val as $guage => $row) 
		             			{
		             				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		             				$qc_qty = $bundle_qty_arr[$pdate][$guage];
		             				$prod_min = $qc_qty*$smv_arr[$style];
		             				$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
		             				$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
				             		?>
				             		<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
				             			<td width="30"><?=$i;?></td>
				             			<td width="80"><?=$guage;?></td>
				             			<td width="80" align="right"><?=number_format($qc_qty,0);;?></td>
				             			<td width="80" align="right"><?=number_format($row['qc_pass_qty'],0);?></td>
				             			<td width="80" align="right"><?=number_format($row['defect_qty'],0);?></td>
				             			<td width="80" align="right"><?=number_format($defect_prsnt,2);?></td>
				             			<td width="80" align="right"><?=number_format($row['reject_qty'],0);?></td>
				             			<td width="80" align="right"><?=number_format($reject_prsnt,2);?></td>
				             		</tr>
				             		<?
				             		$i++;				             		
				             		$date_wise_qc_qty +=$qc_qty;
				             		$date_wise_qc_pass_qty += $row['qc_pass_qty'];
				             		$date_wise_defect_qty +=$row['defect_qty'];
				             		$date_wise_reject_qty +=$row['reject_qty'];
				             		
				             		$tot_qc_qty +=$qc_qty;
				             		$tot_qc_pass_qty += $row['qc_pass_qty'];
				             		$tot_defect_qty +=$row['defect_qty'];
				             		$tot_reject_qty +=$row['reject_qty'];
				             	}
				             	$date_wise_dft_prsnt = ($date_wise_qc_qty) ? $date_wise_defect_qty/$date_wise_qc_qty : 0;
				             	$date_wise_rej_prsnt = ($date_wise_reject_qty) ? $date_wise_defect_qty/$date_wise_reject_qty : 0;
				             	?>
				             	<tr style="background: #cddcdc;font-weight: bold;text-align: right;">
			             			<td width="30"></td>
			             			<td width="80">Total :</td>
			             			<td width="80"><?=number_format($date_wise_qc_qty,0); ?></td>
			             			<td width="80"><?=number_format($date_wise_qc_pass_qty,0); ?></td>
			             			<td width="80"><?=number_format($date_wise_defect_qty,0); ?></td>
			             			<td width="80"><?=number_format($date_wise_dft_prsnt,2); ?></td>
			             			<td width="80"><?=number_format($date_wise_reject_qty,0); ?></td>
			             			<td width="80"><?=number_format($date_wise_rej_prsnt,2); ?></td>
			             		</tr>
				             	<?
				            }
			             	$tot_dft_prsnt = ($tot_qc_qty) ? $tot_defect_qty/$tot_qc_qty : 0;
			             	$tot_rej_prsnt = ($tot_reject_qty) ? $tot_defect_qty/$tot_reject_qty : 0;
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="590"  align="center">
	             	<tfoot>
	             		<tr>
	             			<th width="30">.</th>
	             			<th width="80">Grand Total</th>
	             			<th width="80"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_dft_prsnt,2); ?></th>
	             			<th width="80"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_rej_prsnt,2); ?></th>
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
		<?
	}
	elseif ($type==2) 
	{		
		?>
		<fieldset style="width: 1000px;margin: 0 auto;">
			<div class="title-part" style="margin: 0 auto;text-align: center;font-size: 20px;">
				<h2>Monthly Guage Wise Inspection Report</h2>
				<h2>Company : <?=$company_arr[$company_id]; ?></h2>
				<h2>Date : <?=change_date_format($s_date); ?>To<?=change_date_format($e_date); ?> </h2>
			</div>
			<div class="report-container-part">
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="980"  align="center">
	             	<thead>
	             		<tr>
	             			<th width="30">Sl.</th>
	             			<th width="70">Month</th>
	             			<th width="80">QC Qty.</th>
	             			<th width="80">Prod min</th>
	             			<th width="80">Working Min</th>
	             			<th width="80">Loss Min</th>
	             			<th width="80">Effi%</th>
	             			<th width="80">QC Pass Qty.</th>
	             			<th width="80">Alter Qty.</th>
	             			<th width="80">Alter%</th>
	             			<th width="80">Damage Qty.</th>
	             			<th width="80">Damage%</th>
	             			<th width="80">Working Day</th>
	             		</tr>
	             	</thead>
	             </table>
	             <div style=" max-height:300px; width:1000px; overflow-y:scroll;" id="scroll_body">
					<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="980"  align="center" id="table_body_1">
		             	<tbody>
		             		<?
		             		$i=1;
		             		$tot_prod_min = 0;
		             		$tot_working_min = 0;
		             		$tot_loss_min = 0;
		             		$tot_qc_pass_qty = 0;
		             		$tot_qc_qty = 0;
		             		$tot_defect_qty = 0;
		             		$tot_reject_qty = 0;
		             		foreach ($yearly_data_array as $pdate => $row) 
		             		{
	             				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
	             				$qc_qty = $yearly_bundle_qty_arr[$pdate]['qty'];
	             				$loss_min = $yearly_bundle_qty_arr[$pdate]['loss_min'];
	             				$prod_min = $qc_qty*$smv_arr[$style];
	             				$defect_prsnt = ($row['defect_qty']/$qc_qty)*100;
	             				$reject_prsnt = ($row['reject_qty']/$qc_qty)*100;
			             		?>
			             		<tr bgcolor="<? echo $bgcolor;?>" id="tr_<? echo $i;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')">
			             			<td width="30"><?=$i;?></td>
			             			<td width="70" align="left"><?=date('F',strtotime($pdate));?></td>
			             			<td width="80" align="right"><?=number_format($qc_qty,0);;?></td>
			             			<td width="80" align="right"><?=number_format($row['prod_min'],2);?></td>
			             			<td width="80" align="right"><?=number_format($a,2);?></td>
			             			<td width="80" align="right"><?=number_format($loss_min,2);?></td>
			             			<td width="80" align="right"><?=number_format($a,2);?></td>
			             			<td width="80" align="right"><?=number_format($row['qc_pass_qty'],0);?></td>
			             			<td width="80" align="right"><?=number_format($row['defect_qty'],0);?></td>
			             			<td width="80" align="right"><?=number_format($defect_prsnt,2);?></td>
			             			<td width="80" align="right"><?=number_format($row['reject_qty'],0);?></td>
			             			<td width="80" align="right"><?=number_format($reject_prsnt,2);?></td>
			             			<td width="80" align="right"><?=number_format($a,0);?></td>
			             		</tr>
			             		<?
			             		$i++;

			             		$tot_prod_min += $row['prod_min'];
		             			$tot_working_min = 0;
		             			$tot_loss_min += $loss_min;
			             		$tot_qc_qty +=$qc_qty;
			             		$tot_qc_pass_qty += $row['qc_pass_qty'];
			             		$tot_defect_qty +=$row['defect_qty'];
			             		$tot_reject_qty +=$row['reject_qty'];
				            }
			             	$tot_dft_prsnt = ($tot_qc_qty) ? $tot_defect_qty/$tot_qc_qty : 0;
			             	$tot_rej_prsnt = ($tot_reject_qty) ? $tot_defect_qty/$tot_reject_qty : 0;
				            ?>
		             	</tbody>
		            </table>	             	
	            </div>
				<table cellspacing="0" border="1" class="rpt_table"   rules="all" width="980"  align="center">
	             	<tfoot>
	             		<tr>
	             			<th width="30">.</th>
	             			<th width="70">Grand Total</th>
	             			<th width="80"><?=number_format($tot_qc_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_prod_min,2); ?></th>
	             			<th width="80"><?=number_format($tot_working_min,2);?></th>
	             			<th width="80"><?=number_format($tot_loss_min,2);?></th>
	             			<th width="80"><?=number_format($a,2);?></th>
	             			<th width="80"><?=number_format($tot_qc_pass_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_defect_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_dft_prsnt,2); ?></th>
	             			<th width="80"><?=number_format($tot_reject_qty,0); ?></th>
	             			<th width="80"><?=number_format($tot_rej_prsnt,2); ?></th>
	             			<th width="80"><?=number_format($a,0); ?></th>
	             		</tr>
	             	</tfoot>
	            </table>	 
			</div>
		</fieldset>
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
	echo "$total_data####$filename####$type####".implode("__",$month_name_arr)."####".implode("__",$month_total_defect)."####".implode("__",$month_total_reject);
	exit(); 
}





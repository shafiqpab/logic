<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All -", $selected, "" );     
	
	exit();
}

 
	// <!-- <div id="report_container2"></div> -->
	
 


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_id=str_replace("'","",$cbo_company_id);
	$buyer_id=str_replace("'","",$cbo_buyer_name);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);	
	$txt_job_no=str_replace("'","",$txt_job_no); 
	$txt_order_no=str_replace("'","",$txt_order_no); 
	$txt_internal_ref_no=str_replace("'","",$txt_internal_ref_no); 
	$txt_cutting_per=str_replace("'","",$txt_cutting_per)*1; 
		
	if($date_from!="" && $date_to!=""){
		$start_date=change_date_format($date_from,"","",1);
		$end_date=change_date_format($date_to,"","",1);
		$where_con=" and d.plan_date between '$start_date' and '$end_date'"; 
	}

	if($buyer_id!=0){$where_con.=" and A.BUYER_NAME=$buyer_id"; }
	if($txt_job_no!=""){$where_con.=" and A.JOB_NO like('%$txt_job_no')"; }
	if($txt_order_no!=""){$where_con.=" and B.PO_NUMBER like('%$txt_order_no')"; }

	if($txt_internal_ref_no!=""){$where_con.=" and (b.GROUPING) like('%$txt_internal_ref_no%')"; }

	
	$company_lib=return_library_array( "select id, company_short_name from lib_company where is_deleted=0 and status_active=1", "id", "company_short_name");
	$buyer_lib=return_library_array( "select id, short_name from lib_buyer where is_deleted=0 and status_active=1", "id", "short_name");
	$floor_lib=return_library_array( "select id, floor_name from lib_prod_floor  where is_deleted=0 and status_active=1 and  PRODUCTION_PROCESS=1 ",'id','floor_name');

	$line_lib=return_library_array( "select id, line_name from lib_sewing_line where is_deleted=0 and status_active=1",'id','line_name');



	// $sql="SELECT C.COMPANY_ID,C.PLAN_ID,C.LINE_ID,d.PLAN_QNTY,B.PLAN_CUT,B.PUB_SHIPMENT_DATE,C.START_DATE,C.END_DATE,A.JOB_NO,A.ID AS JOB_ID,B.ID AS PO_ID,B.PO_NUMBER,B.GROUPING,A.BUYER_NAME,A.STYLE_REF_NO,D.PLAN_DATE
	// from wo_po_details_master a, wo_po_break_down b, ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d  where a.id=b.job_id and b.id=c.po_break_down_id and c.plan_id=d.plan_id and  c.company_id in($company_id) $where_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by C.COMPANY_ID,d.plan_date ";

	$sql="SELECT C.COMPANY_ID,C.PLAN_ID,C.LINE_ID,d.PLAN_QNTY,B.PLAN_CUT,B.PUB_SHIPMENT_DATE,C.START_DATE,C.END_DATE,A.JOB_NO,A.ID AS JOB_ID,B.ID AS PO_ID,B.PO_NUMBER,B.GROUPING,A.BUYER_NAME,A.STYLE_REF_NO,D.PLAN_DATE from wo_po_details_master a, wo_po_break_down b, PPL_SEWING_PLAN_BOARD_POWISE e,ppl_sewing_plan_board c,ppl_sewing_plan_board_dtls d  where a.id=b.job_id  AND b.id = e.PO_BREAK_DOWN_ID  and e.plan_id = c.plan_id and c.plan_id=d.plan_id and  c.company_id in($company_id) $where_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by C.COMPANY_ID,d.plan_date ";
	  //echo $sql;die;

	$sql_result=sql_select($sql);
	if(count($sql_result)==0)
	{
		echo " No Data Found.";die;
	}
	
	
	$dataArr=array(); $po_id_arr=array(); $job_id_arr=array(); $plan_id_arr=array(); $line_id_arr=array();
	foreach($sql_result as $row)
	{
		
		// $dateKey=date("d M-Y",strtotime($row['PLAN_DATE']));
			
		$dataArr[$row['PO_ID']]['buyer_name']=$row['BUYER_NAME'];
		$dataArr[$row['PO_ID']]['job_no']=$row['JOB_NO'];
		$dataArr[$row['PO_ID']]['po_number']=$row['PO_NUMBER'];
		$dataArr[$row['PO_ID']]['grouping']=$row['GROUPING'];
		$dataArr[$row['PO_ID']]['style_ref_no']=$row['STYLE_REF_NO'];
		$dataArr[$row['PO_ID']]['plan_cut']=$row['PLAN_CUT'];
		$dataArr[$row['PO_ID']]['pub_shipment_date']=$row['PUB_SHIPMENT_DATE'];
		$dataArr[$row['PO_ID']]['line_id']=$row['LINE_ID'];
		$dataArr[$row['PO_ID']]['plan_qnty']+=$row['PLAN_QNTY'];
		$dataArr[$row['PO_ID']]['start_date']=$row['START_DATE'];
		$dataArr[$row['PO_ID']]['end_date']=$row['END_DATE'];
		$dataArr[$row['PO_ID']]['company_id']=$row['COMPANY_ID'];
		// $dataArr[$row['PO_ID']]['plan_date']=$row['PLAN_DATE'];

		//$planArr[$row['PO_ID']][$dateKey]['PLAN_DATE']=$row['PLAN_DATE'];


		$line_id_arr[$row['PO_ID']][$row['LINE_ID']]=$line_lib[$row['LINE_ID']] ;
	
		// $m=date("ym",strtotime($row['PLAN_DATE']))*1;
		// $d=date("d",strtotime($row['PLAN_DATE']))*1;
		// $tempMonthArr[$m][$d]=$dateKey;

		$plan_id_arr[$row['PLAN_ID']]=$row['PLAN_ID'];
		$po_id_arr[$row['PO_ID']]=$row['PO_ID'];
		$job_id_arr[$row['JOB_ID']]=$row['JOB_ID'];
	
	}
	//  echo "<pre>";print_r($planArr);die;
	
		// unset($sql_result);
		

		$con = connect();
		execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_name and entry_form =78 and ref_from in(1,2)");
		oci_commit($con);
		fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 78, 1,$po_id_arr, $empty_arr);


		if($date_from!="" && $date_to!=""){
			$start_date=change_date_format($date_from,"","",1);
			$end_date=change_date_format($date_to,"","",1);
			$date_con=" and a.PRODUCTION_DATE between '$start_date' and '$end_date'"; 
		}

	
	//Production data..............................................
	$prod_sql="SELECT a.PO_BREAK_DOWN_ID,a.FLOOR_ID,a.PRODUCTION_TYPE,a.PRODUCTION_QUANTITY from PRO_GARMENTS_PRODUCTION_MST a,GBL_TEMP_ENGINE g where  a.po_break_down_id=g.ref_val and g.user_id=$user_name  and g.entry_form =78 and g.ref_from=1 and a.PRODUCTION_TYPE=1 and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and a.company_id in($company_id)
	$date_con";
	// echo $prod_sql;die;
	$prod_array=array();
	foreach (sql_select($prod_sql) as  $value) 
	{
		$prod_array[$value['PO_BREAK_DOWN_ID']]['cut_qty']+=$value['PRODUCTION_QUANTITY'];
		$prod_array[$value['PO_BREAK_DOWN_ID']]['floor_id']=$value['FLOOR_ID'];
		
	}

	//fnc_tempengine("GBL_TEMP_ENGINE", $user_name, 78, 2,$job_id_arr, $empty_arr);

	// $style_type_sql="SELECT a.COLOR_TYPE_ID,b.PO_BREAK_DOWN_ID,c.CUTTING_ID from WO_PRE_COST_FABRIC_COST_DTLS a,WO_PRE_COS_FAB_CO_AVG_CON_DTLS b, LIB_FABRIC_CUTTING_PLANNING  c, GBL_TEMP_ENGINE g where a.id =b.PRE_COST_FABRIC_COST_DTLS_ID  and a.COLOR_TYPE_ID=c.STYLE_ID and b.PO_BREAK_DOWN_ID=g.ref_val and g.user_id=$user_name  and g.entry_form =78 and g.ref_from=1  and  a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and  b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and  c.STATUS_ACTIVE=1 and c.IS_DELETED=0  and  a.company_id in($company_id) group by a.COLOR_TYPE_ID,b.PO_BREAK_DOWN_ID,c.CUTTING_ID";
 	 //echo $style_type_sql;die;


	  

	  $cp_lib=return_library_array( "select STYLE_ID,CUTTING_ID from LIB_FABRIC_CUTTING_PLANNING where STATUS_ACTIVE=1 and IS_DELETED=0",'STYLE_ID','CUTTING_ID');


	//   $style_type_sql="SELECT a.EMB_NAME as COLOR_TYPE_ID,b.PO_BREAK_DOWN_ID , 0 as TYPE from WO_PRE_COST_EMBE_COST_DTLS a,WO_PRE_COS_EMB_CO_AVG_CON_DTLS b, GBL_TEMP_ENGINE g where a.id=b.PRE_COST_EMB_COST_DTLS_ID  and b.PO_BREAK_DOWN_ID<>0 and a.EMB_NAME in(1,2,3) and  a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and  b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and b.PO_BREAK_DOWN_ID=g.ref_val and g.user_id=$user_name  and g.entry_form =78 and g.ref_from=1   group by a.EMB_NAME,b.PO_BREAK_DOWN_ID
	//  union all
	//  SELECT a.COLOR_TYPE_ID,b.PO_BREAK_DOWN_ID , 1 as TYPE from WO_PRE_COST_FABRIC_COST_DTLS a,WO_PRE_COS_FAB_CO_AVG_CON_DTLS b, GBL_TEMP_ENGINE g  where a.id =b.PRE_COST_FABRIC_COST_DTLS_ID    and  a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and  b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and b.PO_BREAK_DOWN_ID=g.ref_val and g.user_id=$user_name  and g.entry_form =78 and g.ref_from=1    group by a.COLOR_TYPE_ID,b.PO_BREAK_DOWN_ID ";

	$style_type_sql="SELECT a.EMB_NAME as COLOR_TYPE_ID,b.PO_BREAK_DOWN_ID , 0 as TYPE from WO_PRE_COST_EMBE_COST_DTLS a,WO_PRE_COS_EMB_CO_AVG_CON_DTLS b, GBL_TEMP_ENGINE g where a.id=b.PRE_COST_EMB_COST_DTLS_ID  and b.PO_BREAK_DOWN_ID<>0 and a.EMB_NAME in(1,2,3) and  a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and  b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and b.PO_BREAK_DOWN_ID=g.ref_val and g.user_id=$user_name  and g.entry_form =78 and g.ref_from=1
	union all
	SELECT a.COLOR_TYPE_ID,b.PO_BREAK_DOWN_ID , 1 as TYPE from WO_PRE_COST_FABRIC_COST_DTLS a,WO_PRE_COS_FAB_CO_AVG_CON_DTLS b, GBL_TEMP_ENGINE g  where a.id =b.PRE_COST_FABRIC_COST_DTLS_ID    and  a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and  b.STATUS_ACTIVE=1 and b.IS_DELETED=0  and b.PO_BREAK_DOWN_ID=g.ref_val and g.user_id=$user_name  and g.entry_form =78 and g.ref_from=1";

	 //echo $style_type_sql; 

	$style_type_arr=array();
	$day_count_arr=array();
	$po_wise_color_type_arr = array();
	foreach (sql_select($style_type_sql) as  $v) 
	{	
		
		
		$po_wise_color_type_arr[$v['TYPE']][$v['PO_BREAK_DOWN_ID']][$v['COLOR_TYPE_ID']] = $v['COLOR_TYPE_ID'];
		$day_count_arr[$PO_BREAK_DOWN_ID][0]=1;
		
		if($v['TYPE']==1){
			$v['CUTTING_ID'] = (($v['COLOR_TYPE_ID'] == 1 || $v['COLOR_TYPE_ID'] == 34 || $v['COLOR_TYPE_ID'] == 88 || $v['COLOR_TYPE_ID'] == 68 || $v['COLOR_TYPE_ID'] == 63 || $v['COLOR_TYPE_ID'] == 71 || $v['COLOR_TYPE_ID'] == 6 || $v['COLOR_TYPE_ID'] == 51 || $v['COLOR_TYPE_ID'] == 66 || $v['COLOR_TYPE_ID'] == 86 || $v['COLOR_TYPE_ID'] == 76) && $v['TYPE'] == 1)?$cp_lib[1]:0;

			$v['CUTTING_ID'] = (($v['COLOR_TYPE_ID'] == 47 || $v['COLOR_TYPE_ID'] == 48 || $v['COLOR_TYPE_ID'] == 44 || $v['COLOR_TYPE_ID'] == 2) && $v['TYPE'] == 1)?$cp_lib[2]:0;

			$style_type_arr[$v['PO_BREAK_DOWN_ID']][$v['COLOR_TYPE_ID']]=$color_type[$v['COLOR_TYPE_ID']] ;
			$day_count_arr[$v['PO_BREAK_DOWN_ID']][$v['COLOR_TYPE_ID']]=$v['CUTTING_ID'];	
		}
		

	}


	foreach ($po_wise_color_type_arr[0] as $PO_BREAK_DOWN_ID => $rowArr) {
		
		if(in_array(1,$rowArr) && in_array(2,$rowArr)){
			$style_type_arr[$PO_BREAK_DOWN_ID][]="Printed & Embroidery";
			$day_count_arr[$PO_BREAK_DOWN_ID][5]=$cp_lib[5];
		}
		else if(in_array(1,$rowArr)){
			$style_type_arr[$PO_BREAK_DOWN_ID][]="Printed";
			$day_count_arr[$PO_BREAK_DOWN_ID][3]=$cp_lib[3];
		}
		else if(in_array(2,$rowArr)){
			$style_type_arr[$PO_BREAK_DOWN_ID][]="Embroidery";
			$day_count_arr[$PO_BREAK_DOWN_ID][4]=$cp_lib[4];
		}

		else if(in_array(3,$rowArr)){
			$style_type_arr[$PO_BREAK_DOWN_ID][]="Wash";
			$day_count_arr[$PO_BREAK_DOWN_ID][6]=$cp_lib[6];
		}

		
		
	}



	 //echo "<pre>";print_r($po_wise_color_type_arr[0]);die;
	 
	 
	$planDataArr=array();
	foreach($sql_result as $row)
	{
		$max_day = max($day_count_arr[$row['PO_ID']])*1;
		$strtotime = strtotime($row['PLAN_DATE']. ' - '.$max_day.' days');
		$dateKey= date('d M-Y', $strtotime);
		$monthArr[$strtotime]=$dateKey;
		$planDataArr['plan_qnty'][$row['PO_ID']][$dateKey]=$row['PLAN_QNTY'];
	}
 	
 
 
	ksort($monthArr);
 		

	execute_query("DELETE from GBL_TEMP_ENGINE where user_id=$user_name and entry_form =78 and ref_from in(1,2)");
	oci_commit($con);
	disconnect($con);		


	$width=(count($monthArr)*50)+2030;

	ob_start();	
	?>
		<div style="margin:0 auto; width:<?=$width+25;?>px;">
			<table width="100%" border="0">
				<thead>
					<tr><td align="center" colspan="<?=(count($monthArr)+25);?>" style="font-size:18px; font-weight:bold; text-decoration:underline;">Sewing Plan Wise Cutting Plan Report</td></tr>
					<tr><td align="center" colspan="<?=(count($monthArr)+25);?>"><b>Ship Date Range : <?=change_date_format($date_from);?> to <?=change_date_format($date_to);?></b></td></tr>
					<tr><td align="center" colspan="<?=(count($monthArr)+25);?>">Report Generate on : <?=date("d-m-Y, h:i A");?></td></tr>
				</thead>
			</table>

			<table width="<?=$width;?>" id="table_header_1"cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="100">Company</th>
					<th width="100">Buyer</th>
					<th width="100">Job</th>
					<th width="100">Order No</th>
					<th width="100">Internal Ref.</th>
					<th width="100">Style Name</th>
					<th width="80">Ship Date</th>
					<th width="80">Style Type</th>
					<th width="80">Order Qty</th>
					<th width="80">Cutting Qty</th>
					<th width="80">Cutting Balance</th>
					<th width="80">Line Plan Qty</th>
					<th width="100">Line No.</th>
					<th width="80">Cutting Start Date</th>
					<th width="80">Cutting Closed Date</th>
					<th width="100" >Cutting Floor</th>
					<th width="80">Cutting Plan Qty</th>
					<th width="80">Daily Cutting Target</th>
				
					<? foreach($monthArr as $date){ ?><th width="50"><?=$date;?></th> <? } ?>
					<th width="80" >Total</th>
				</thead>
			</table>
		</div>	

		<div style="width:<?=$width+18;?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
			<table width="<?=$width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="tbl_list_search">
				<tbody>
					<?
						$i=1;
						foreach($dataArr as $po_id=>$row)
						{		
							$max_days = max($day_count_arr[$po_id]);
							//  echo $max_days;
								$cut_blance=$row['plan_cut'] - $prod_array[$po_id]['cut_qty'];
								$bgcolor = ($i%2==0)?"#E9F3FF":"#FFFFFF";

								$max_day = max($day_count_arr[$po_id])*1;
								$row['start_date'] = date('d M-Y', strtotime($row['start_date']. ' - '.$max_day.' days'));
								$row['end_date'] = date('d M-Y', strtotime($row['end_date']. ' - '.$max_day.' days'));


								?>
								<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
									<td width="30"><?=$i;?></td>
									<td width="100"><?= $company_lib[$row['company_id']]?></td>
									<td width="100"><?= $buyer_lib[$row['buyer_name']]?></td>
									<td width="100"><?= $row['job_no']?></td>
									<td width="100" align="center"><?=$row['po_number']?></td>
									<td width="100"><?= $row['grouping']?></td>
									<td width="100"><?= $row['style_ref_no']?></td>
									<td width="80" align="center"><?= change_date_format($row['pub_shipment_date']);?></td>
									<td width="80" align="left"><p><?= implode(',',$style_type_arr[$po_id]);?></p></td>		
									<td width="80" align="right"><?= $row['plan_cut']?></td>
									<td width="80" align="right"><?= $prod_array[$po_id]['cut_qty'];?></td>				
									<td width="80" align="right"><?= $cut_blance;?></td>
									<td width="80" align="right"><?= array_sum($planDataArr['plan_qnty'][$po_id]); ?></td>
									<td width="100" title="Plan id:"><p><?=implode(',',$line_id_arr[$po_id])?><p></td>
									<td width="80" align="center"><?= change_date_format($row['start_date']);?></td>
									<td width="80" align="center"><?= change_date_format($row['end_date']);?></td>
									<td width="100"><p><?=$floor_lib[$prod_array[$po_id]['floor_id']];?></p></td>
									<td width="80" align="right"><?= $cuttingPlanQty = round((array_sum($planDataArr['plan_qnty'][$po_id])*$txt_cutting_per/100)+array_sum($planDataArr['plan_qnty'][$po_id])); ?></td>
									<td width="80" align="right"><?= round($cuttingPlanQty/count($planDataArr['plan_qnty'][$po_id]));?></td>
									<? 
										$plan_total=0;
										foreach($monthArr as $dateKey)
										{ 		
											$planDataArr['plan_qnty'][$po_id][$dateKey] =(($planDataArr['plan_qnty'][$po_id][$dateKey]*$txt_cutting_per)/100) + $planDataArr['plan_qnty'][$po_id][$dateKey];
											
											?>
												<td width="50" align="right"><?= round($planDataArr['plan_qnty'][$po_id][$dateKey]);?></td>
											<? 
												$plan_total += $planDataArr['plan_qnty'][$po_id][$dateKey];	
										
										} 											
												
									?>				
									
									
									<td width="80" align="right"><?= round($plan_total);?></td>
									
								</tr>
								<? 
								$i++;	
						}	
						

					?>
				</tbody>
			</table>
		</div>


	<?
	$html=ob_get_contents();
	ob_clean();

		foreach (glob("$user_name*.xls") as $filename) 
		{
			@unlink($filename);
		}
		
		$filename=$user_name."_".time().".xls";
		$create_new_doc = fopen($filename, 'w');
		fwrite($create_new_doc,$html);
		
		echo "$html####$filename";
	exit();

}

	
      
 
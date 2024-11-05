<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 0, "- All Buyer -", $selected, "" );
	exit();
}


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_month=str_replace("'","",$cbo_month);
 	
	
	$companyArr=return_library_array( "select id, COMPANY_NAME from lib_company", "id", "COMPANY_NAME"  );
	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name" );
	//--------------------------------------------------------------------------------------------------------------------
	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month, $cbo_year);
	$s_date=$cbo_year."-".$cbo_month."-"."01";
	$e_date=$cbo_year."-".$cbo_month."-".$daysinmonth;
	
	$next_date = date('Y-m-d', strtotime('1 month', strtotime($e_date)));
	
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
		$next_date=change_date_format($next_date,'yyyy-mm-dd','-',1);
	}

	//echo $next_date;die;
	
	$where_con='';
	if($cbo_year && $cbo_month){
		$where_con.="AND (d.PLAN_DATE BETWEEN '$s_date' AND '$e_date')";
	}
	
	if($cbo_company_id!=''){$where_con.=" AND A.COMPANY_ID in($cbo_company_id)";}
	if($cbo_buyer_name!=''){$where_con.=" AND b.BUYER_NAME in($cbo_buyer_name)";}
	
	//$sql="select A.COMPANY_ID,B.BUYER_NAME,B.STYLE_REF_NO,b.SET_SMV, a.LINE_ID,a.START_DATE,a.END_DATE, B.JOB_NO,a.PLAN_QNTY,a.SHIP_DATE,c.PO_QUANTITY from PPL_SEWING_PLAN_BOARD a,WO_PO_DETAILS_MASTER b , WO_PO_BREAK_DOWN c  where A.PO_BREAK_DOWN_ID=c.id and B.JOB_NO=C.JOB_NO_MST and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 $where_con ";
	   
	$sql="SELECT a.PLAN_ID,A.COMPANY_ID,a.START_DATE,a.END_DATE, B.BUYER_NAME, B.STYLE_REF_NO, b.SET_SMV, a.LINE_ID, B.JOB_NO, a.SHIP_DATE, c.PO_QUANTITY, SUM (d.PLAN_QNTY) AS PLAN_QNTY FROM PPL_SEWING_PLAN_BOARD a, WO_PO_DETAILS_MASTER b, WO_PO_BREAK_DOWN c, PPL_SEWING_PLAN_BOARD_DTLS d WHERE A.PO_BREAK_DOWN_ID = c.id AND A.PLAN_ID = d.PLAN_ID AND B.JOB_NO = C.JOB_NO_MST AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 $where_con GROUP BY a.PLAN_ID,A.COMPANY_ID,a.START_DATE,a.END_DATE, B.BUYER_NAME, B.STYLE_REF_NO, b.SET_SMV, a.LINE_ID,  B.JOB_NO, a.SHIP_DATE, c.PO_QUANTITY" ;
		  //echo $sql;die;  
	   
	   
	
	$sqlResult=sql_select($sql);
	$dataArr=array();
	foreach( $sqlResult as $rows)
	{
			$day_diff=datediff( 'd', $rows[START_DATE],$rows[END_DATE]);
			$dataArr[STYLE][$rows[COMPANY_ID]][$rows[BUYER_NAME]][$rows[STYLE_REF_NO]]=1;
			$dataArr[LINE][$rows[COMPANY_ID]][$rows[BUYER_NAME]][$rows[LINE_ID]]=1;
			$dataArr[DAY][$rows[COMPANY_ID]][$rows[BUYER_NAME]]+=$day_diff;
			$dataArr[PCS][$rows[COMPANY_ID]][$rows[BUYER_NAME]]+=$rows[PLAN_QNTY];
			$dataArr[MIN][$rows[COMPANY_ID]][$rows[BUYER_NAME]]+=$rows[PLAN_QNTY]*$rows[SET_SMV];
			$dataArr[PO_MIN][$rows[COMPANY_ID]][$rows[BUYER_NAME]]+=$rows[PO_QUANTITY]*$rows[SET_SMV];
		
	}
	unset($sqlResult);		
		
	$where_con='';
	if($cbo_year && $cbo_month){
		$where_con="AND (d.PLAN_DATE BETWEEN '$e_date' AND '$next_date')";
	}
	if($cbo_company_id!=''){$where_con.=" AND A.COMPANY_ID in($cbo_company_id)";}
	if($cbo_buyer_name!=''){$where_con.=" AND b.BUYER_NAME in($cbo_buyer_name)";}
	
	
	$advanceSql="SELECT a.PLAN_ID,A.COMPANY_ID, B.BUYER_NAME, B.STYLE_REF_NO, b.SET_SMV, a.LINE_ID, B.JOB_NO, a.SHIP_DATE, c.PO_QUANTITY, SUM (d.PLAN_QNTY) AS PLAN_QNTY FROM PPL_SEWING_PLAN_BOARD a, WO_PO_DETAILS_MASTER b, WO_PO_BREAK_DOWN c, PPL_SEWING_PLAN_BOARD_DTLS d WHERE A.PO_BREAK_DOWN_ID = c.id AND A.PLAN_ID = d.PLAN_ID AND B.JOB_NO = C.JOB_NO_MST AND a.IS_DELETED = 0 AND a.STATUS_ACTIVE = 1 AND b.IS_DELETED = 0 AND b.STATUS_ACTIVE = 1 AND c.IS_DELETED = 0 AND c.STATUS_ACTIVE = 1 $where_con GROUP BY a.PLAN_ID,A.COMPANY_ID, B.BUYER_NAME, B.STYLE_REF_NO, b.SET_SMV, a.LINE_ID,  B.JOB_NO, a.SHIP_DATE, c.PO_QUANTITY" ;
	 //echo $advanceSql;die;
	$advanceSqlResult=sql_select($advanceSql);
	foreach( $advanceSqlResult as $row)
	{
			$dataArr[ADVANCEMIN][$row[COMPANY_ID]][$row[BUYER_NAME]]+=$row[PLAN_QNTY]*$row[SET_SMV];
	}
	
	//print_r($dataArr[ADVANCEMIN]);
	unset($advanceSqlResult);		

		
		$width=800;
		ob_start();	
		?>
		
		<div style="width:<?=$width+22; ?>px; margin:0 auto;">
    	<fieldset style="width:100%;">
            <table width="<?=$width; ?>" align="left">
                <tr class="form_caption">
                    <td colspan="<?=$spnTitle; ?>" align="center"><strong>Monthly Buyer Wise Planning Report</strong></td>
                </tr>
                <tr>
                    <td colspan="<?=$spnTitle; ?>"><strong><?=$months[$cbo_month].'-'.$cbo_year;?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="left">
                <thead>
                    <tr>
                        <th width="130">Fctory Name</th>
                        <th width="100">Buyer</th>
                        <th width="80">Production Lead Time(Avg) day</th>
                        <th width="80">No of Styling</th>
                        <th width="80">Total Pcs</th>
                        <th width="80">Total Produce Minutes</th>
                        <th width="80">Target Average Production (Minutes)</th>
                        <th width="80">Shipment Minutes</th>
                        <th>Advance Production (Minutes)</th>
                     </tr>
                    
                </thead>
            </table>
            <div style="width:<?=$width+18; ?>px; max-height:400px; overflow-y:scroll; float:left;" id="scroll_body">
            <table class="rpt_table" width="<?=$width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
                <?
					$i=1;
					foreach($dataArr[DAY] as $company_id=>$dataRows)
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						?>

						<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
							<td width="130" rowspan="<?= count($dataArr[DAY][$company_id]);?>"><?= $companyArr[$company_id];?></td>
						<?
						$sub_production_lead=0; $sub_tot_style=0; $sub_pcs_qty=0; 
						$sub_product_min=0; $sub_order_product_min=0; $sub_advance_product_min=0;
						
						$f=0;
						foreach($dataRows as $buyer_id=>$days)
						{
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
							$tot_line=count($dataArr[LINE][$company_id][$buyer_id]);
							$tot_style=count($dataArr[STYLE][$company_id][$buyer_id]);
							$pcs_qty=$dataArr[PCS][$company_id][$buyer_id];
							$product_min=$dataArr[MIN][$company_id][$buyer_id];
							$order_product_min=$dataArr[PO_MIN][$company_id][$buyer_id];
							$advance_product_min=$dataArr[ADVANCEMIN][$company_id][$buyer_id];
							$target_average_product_min=($product_min/$days);
							
							
							//sub................
							$sub_production_lead+=($value/$tot_line);
							$sub_tot_style+=$tot_style;
							$sub_pcs_qty+=$pcs_qty;
							$sub_product_min+=$product_min;
							$sub_order_product_min+=$order_product_min;
							$sub_advance_product_min+=$advance_product_min;
							$sub_target_average_product_min+=$target_average_product_min;
							//grand................
							$grand_production_lead+=($value/$tot_line);
							$grand_tot_style+=$tot_style;
							$grand_pcs_qty+=$pcs_qty;
							$grand_product_min+=$product_min;
							$grand_order_product_min+=$order_product_min;
							$grand_advance_product_min+=$advance_product_min;
							$grand_target_average_product_min+=$target_average_product_min;
							
							
							if($f==1){
							?>
							 <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
                            <? } ?>
								<td width="100"><?=$buyer_arr[$buyer_id];?></td>
								<td width="80" align="right" title="<?= 'Days:'.$days.'; Line:'.$tot_line;?>"><?=number_format($days/$tot_line);?></td>
								<td width="80" align="right"><?=$tot_style;?></td>
								<td width="80" align="right"><?=$pcs_qty;?></td>
								<td width="80" align="right"><?=number_format($product_min,2);?></td>
								<td width="80" align="right"><?=number_format($target_average_product_min,2);?></td>
								<td width="80" align="right"><?=number_format($order_product_min,2);?></td>
								<td align="right"><?=number_format($advance_product_min,2);?></td>
							 </tr>
							<?
							$f=1;
							$i++;
						}
						?>
                         <tr style="background:#999">
                            <td colspan="2" align="right">Total</td>
                            <td align="right"><?=$sub_production_lead;?></td>
                            <td align="right"><?=$sub_tot_style;?></td>
                            <td align="right"><?=$sub_pcs_qty;?></td>
                            <td align="right"><?=number_format($sub_product_min,2);?></td>
                            <td align="right"><?=number_format($sub_target_average_product_min,2);?></td>
                            <td align="right"><?=number_format($sub_order_product_min,2);?></td>
                            <td align="right"><?=number_format($sub_advance_product_min,2);?></td>
                         </tr>
                     
                     <?                

					}
				?>
                <tfoot>
                     <tr>
                        <th colspan="2">Grand Total</th>
                        <th align="right"><?=$grand_production_lead;?></th>
                        <th align="right"><?=$grand_tot_style;?></th>
                        <th align="right"><?=$grand_pcs_qty;?></th>
                        <th align="right"><?=number_format($grand_product_min,2);?></th>
                        <th align="right"><?=number_format($grand_target_average_product_min,2);?></th>
                        <th align="right"><?=number_format($grand_order_product_min,2);?></th>
                        <th align="right"><?=number_format($grand_advance_product_min,2);?></th>
                     </tr>
                 </tfoot>
            </table>
            </div>
            
            
            
        </fieldset>
    </div>
    
		<?
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="".$user_name."_".$name.".xls";
	echo "$total_data####$filename####$type";
	exit();
}
?>
      
 
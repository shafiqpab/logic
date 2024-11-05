<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$job_smv_arr=return_library_array( "select job_no, set_smv from wo_po_details_master",'job_no','set_smv');
$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst",'comapny_id','basic_smv');
$asking_avg_rate_arr=return_library_array( "select company_id, asking_avg_rate from lib_standard_cm_entry",'company_id','asking_avg_rate');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo $cbo_year_from;die;

	$company_name=str_replace("'","",$cbo_company_name);
	
	$date_cond='';
	if(str_replace("'","",$cbo_year_from)!=0 && str_replace("'","",$cbo_month_from)!=0)
	{
		$start_year=str_replace("'","",$cbo_year_from);
		$start_month=str_replace("'","",$cbo_month_from);
		$start_date=$start_year."-".$start_month."-01";
		
		$end_year=str_replace("'","",$cbo_year_to);
		$end_month=str_replace("'","",$cbo_month_to);
		$end_date=$end_year."-".$end_month."-31";
		
		
		if($db_type==0) $date_cond=" and c.country_ship_date between '$start_date' and '$end_date'";
		if($db_type==2) $date_cond=" and c.country_ship_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";
		
		
		/*if($db_type==0) $date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		if($db_type==2) $date_cond=" and b.pub_shipment_date between '".date("j-M-Y",strtotime(str_replace("'","",$start_date)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$end_date)))."'";*/
	}
	
	/*$data_array="select
					 a.job_no_prefix_num, 
					 a.job_no, 
					 a.company_name, 
					 a.buyer_name,
					 a.set_smv,
					 a.style_ref_no,
					 a.job_quantity, 
					 a.total_set_qnty, 
					 b.id, 
					 b.is_confirmed, 
					 b.po_number, 
					 b.po_quantity as po_quantity, 
					 (b.po_quantity*a.total_set_qnty) as po_quantity_pcs, 
					 b.pub_shipment_date, 
					 b.po_received_date, 
					 b.unit_price, 
					 b.order_total, 
					 b.t_year, 
					 b.t_month
			from 
					wo_po_details_master a, 
					wo_po_break_down b 
			where  
					a.job_no=b.job_no_mst   
					and a.company_name like '$company_name' 
					$date_cond  
					and a.status_active=1
					and a.is_deleted=0 
					and b.status_active=1
					and b.is_deleted=0
					
			order by 
					b.pub_shipment_date desc";*/
					
					
					
					
			$data_array="select
					 a.job_no_prefix_num, 
					 a.job_no, 
					 a.company_name, 
					 a.buyer_name,
					 a.set_smv,
					 a.style_ref_no,
					 a.job_quantity, 
					 a.total_set_qnty, 
					 b.id, 
					 b.is_confirmed, 
					 b.po_number, 
					 b.po_quantity as po_quantity, 
					 c.order_quantity as po_quantity_pcs, 
					 c.country_ship_date as pub_shipment_date,
					 c.order_total, 
					 b.po_received_date, 
					 b.unit_price, 
					 b.t_year, 
					 b.t_month
			from 
					wo_po_details_master a, 
					wo_po_break_down b,
					wo_po_color_size_breakdown c 
			where  
					a.job_no=b.job_no_mst 
					and c.po_break_down_id=b.id   
					and a.company_name like '$company_name' 
					$date_cond  
					and a.status_active=1
					and a.is_deleted=0 
					and b.status_active=1
					and b.is_deleted=0
					and c.status_active=1
					and c.is_deleted=0
					
			order by 
					c.country_ship_date desc";
		
		//echo $data_array;die;
		//'and a.buyer_name=4 ';
		$result_po=sql_select($data_array);
	$row_data_dtls=array();
	$tmp_arr=array();
		foreach($result_po as $row)
		{
			
			$row_data_dtls[date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$row[csf("buyer_name")]]['qnty'] +=$row[csf("po_quantity_pcs")];
			$row_data_dtls[date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$row[csf("buyer_name")]]['value'] +=$row[csf("order_total")];
			$tmp_arr[date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$row[csf("buyer_name")]]+=$row[csf("order_total")];
			$row_data_dtls[date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$row[csf("buyer_name")]]['smv']+=($job_smv_arr[$row[csf("job_no")]])*$row[csf('po_quantity_pcs')];
			
			$order_total+= $row[csf("order_total")];
			$quantity_tot+=$row[csf("po_quantity_pcs")];
			
			if( $job_smv_arr[$row[csf("job_no")]] !=0)
			{
				$booked_basic_qnty=($row[csf("po_quantity_pcs")]*($job_smv_arr[$row[csf("job_no")]]))/$basic_smv_arr[$row[csf("company_name")]];
				$row_data_dtls[date("Y-m",strtotime($row[csf("pub_shipment_date")]))][$row[csf("buyer_name")]]['boking_basic_qty']+=$booked_basic_qnty;
				$booked_basic_qnty_tot+=$booked_basic_qnty;
			}
		}
		//var_dump($row_data_dtls);die;
		
		//month, buyer
		
		
		ob_start();
?>
	<div style="width:1200px;">
    <fieldset style="width:1200px;">
        <table width="1150">
            <tr class="form_caption">
                <td colspan="10" align="center" style="font-size:16px;">SMV vs Price Analysis Report</td>
            </tr>
            <tr class="form_caption">
                <td colspan="10" align="center" style="font-size:16px;"><? echo $company_library[$company_name]; ?></td>
            </tr>
        </table>
        <table id="" class="rpt_table" width="1170" cellpadding="0" cellspacing="0" border="1" rules="all">
            <thead>
            	<tr>
                	<th width="50">SL</th>
                    <th width="100">Company Name</th>
                    <th width="100">Buyer Name </th>
                    <th width="100">Avg.SMV</th>
                    <th width="100">Order Qty(pcs)</th>
                    <th width="100">EQV. Basic Qty</th>
                    <th width="100">Order Value(USD)</th>
                    <th width="100">Avg.Rate(USD)</th>
                    <th width="100">Avg. Basic Rate(USD) </th>
                    <th width="100">Stnd. Rate(USD) </th>
                    <th  width="100">Stnd. Devn(USD)</th>
                    <th >Comments</th>
                </tr>
            </thead>
        </table>
        <div style=" max-height:400px; overflow-y:scroll; width:1200px"  align="left" id="scroll_body">
        <table id="table_body" class="rpt_table" width="1170" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<tbody>
            <?
			$result=sql_select($data_array);
			$i=0;$k=1;$check_month=array();$check_company=array();$check_buyer=array();$m=1;
			 
			foreach($tmp_arr as $date_key=>$buyer_id_arr)
			{
				arsort($buyer_id_arr);
				//var_dump($date_key);die;
				if(!in_array($date_key,$check_month))
				{
					if($k!=1)
					{
					?>
                    <tr>
                        <td colspan="3" align="right" style="font-weight:bold;">Total</td>
                        <td align="right" style="font-weight:bold;"><? $smv_sub_to=($to_smv/$total_po_qty); echo number_format($smv_sub_to,2); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format($total_po_qty,0); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format($total_eqv_basic_qty,2); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format($total_po_val,2); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format(( $total_po_val/$total_po_qty),2); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format(($total_po_val/$total_eqv_basic_qty),2);?></td>
                        <td align="right" style="font-weight:bold;"><? //echo $i; ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format(($total_po_val/$total_eqv_basic_qty)-$avg_rate,2); ?></td>
                        <td ><? echo $i; ?></td>
                    </tr>
                    <?
					}
					?>
                    <tr>
                    	<td colspan="12" align="center" style="font-weight:bold; font-size:16px;" >
						<?
						$month_value=explode("-",$date_key);
						echo $months[$month_value[1]*1]; 
						?>
                        </td>
                    </tr>
                    <?
					$check_month[]=$date_key;
					$k++;
					$to_smv=0;$total_po_qty=0;$total_po_val=0;$total_eqv_basic_qty=0;$total_avg_rate=0;$i=0;$smv_sub_to=0;
				}
				
				
				
				foreach($buyer_id_arr as $buyer_key=>$row_value)
				{
					if ($m%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					$i++;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" >
							<td width="50" align="center"><? echo $m; ?></td>
							<td width="100"><? echo  $company_library[$company_name]; ?></td>
							<td width="100"><? echo $buyer_library[$buyer_key]; ?></td>
							<td width="100" align="right"><? $smv=$row_data_dtls[$date_key][$buyer_key]["smv"]/$row_data_dtls[$date_key][$buyer_key]["qnty"]; echo number_format($smv,2);  $to_smv+=$row_data_dtls[$date_key][$buyer_key]["smv"];?></td>
							<td width="100" align="right"><? $po_qty_pce=$row_data_dtls[$date_key][$buyer_key]["qnty"]; echo number_format($po_qty_pce,0); $total_po_qty+=$po_qty_pce; ?></td>
							<td width="100" align="right"><? $eqv_basic_qty=$row_data_dtls[$date_key][$buyer_key]["boking_basic_qty"];  echo number_format($eqv_basic_qty,2); $total_eqv_basic_qty+=$eqv_basic_qty; ?></td>
							<td width="100" align="right"><? $po_grp_value=$row_data_dtls[$date_key][$buyer_key]["value"]; echo number_format($po_grp_value,2); $total_po_val+=$po_grp_value; ?></td>
							<td width="100" align="right"><? $avg_val=$po_grp_value/$po_qty_pce; echo number_format($avg_val,2); $total_avg_rate+=$avg_val; ?></td>
							<td width="100"  align="right"><? $avg_basic_rate=$po_grp_value/$row_data_dtls[$date_key][$buyer_key]["boking_basic_qty"]; echo number_format($avg_basic_rate,2); $total_avg_basic+=$avg_basic_rate;?></td>
							<td width="100" align="right"><?  echo number_format($asking_avg_rate_arr[$company_name],2); $avg_rate=$asking_avg_rate_arr[$company_name];?></td>
							<td  align="right" width="100" title="<? echo (($po_grp_value/$row_data_dtls[$date_key][$buyer_key]["boking_basic_qty"])-$asking_avg_rate_arr[$company_name]); ?>"><? $stnd_dive=(($po_grp_value/$row_data_dtls[$date_key][$buyer_key]["boking_basic_qty"])-$asking_avg_rate_arr[$company_name]); echo number_format($stnd_dive,2);$total_stnd_div += $stnd_dive; ?></td>
                            <?
							if(number_format($stnd_dive,2)>0)
							{
								?>
								<td  align="center"> <? echo "Over Standard";  ?></td>
								<?
							}
							else if(number_format($stnd_dive,2)<0)
							{
								?>
                                <td bgcolor="#FF0000" align="center"> <? echo "Bellow Standard";  ?></td>
                                
                                <?
							} 
							else if(number_format($stnd_dive,2)==0)
							{
								?>
                                <td align="center"> <? echo "As Per Standard";  ?></td>
                                <?
							}
							?>
						</tr>
						<?	
				$m++;
				}
			}
			?>
                     <tr>
                        <td colspan="3" align="right" style="font-weight:bold;">Total</td>
                        <td align="right" style="font-weight:bold;"><? $smv_sub_to=($to_smv/$total_po_qty); echo number_format($smv_sub_to,2); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format($total_po_qty,0); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format($total_eqv_basic_qty,2); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format($total_po_val,2); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format(( $total_po_val/$total_po_qty),2); ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format(($total_po_val/$total_eqv_basic_qty),2);?></td>
                        <td align="right" style="font-weight:bold;"><? //echo $i; ?></td>
                        <td align="right" style="font-weight:bold;"><? echo number_format(($total_po_val/$total_eqv_basic_qty)-$avg_rate,2); ?></td>
                        <td ><? //echo $i; ?></td>
                    </tr>
            </tbody>
        </table>
        </div>
    </fieldset>
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename";
	exit();
}
disconnect($con);
?>
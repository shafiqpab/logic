<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Monthly Capacity and order qty Report
Functionality	         :
JS Functions	         :
Created by		         :	Aziz
Creation date 	         :  20 Oct,2023
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         : From this version oracle conversion is start
*/

include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'] ;
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$companyShortArr = return_library_array("select id,company_short_name from lib_company where status_active=1 and is_deleted=0","id","company_short_name");
//$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 0, "-- All Buyer --", $selected, "load_drop_down( 'requires/fabric_wise_fab_req_report_controller', this.value, 'load_drop_down_season', 'season_td'); " );     	 
	exit();
}
if($action=="load_report_format")
{
	//echo $sql="select format_id from lib_report_template where template_name in(".$data.") and module_id=11 and report_id in(44) and is_deleted=0 and status_active=1"; die;

	$print_report_format=return_field_value("format_id","lib_report_template","template_name in(".$data.") and module_id=11 and report_id in(44) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);
	exit();
}

if($action=="report_generate")
{
	 
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year_name=str_replace("'","",$cbo_year_name);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	$buyer_id=str_replace("'","",$cbo_buyer);
	$cbo_year=str_replace("'","",$cbo_year);
	 
	if($buyer_id!=0) $buyers_cond="and a.buyer_name in($buyer_id)";else $buyers_cond="";

	if($cbo_company_name==0) $company_conds=""; else $company_conds=" and a.company_name in($cbo_company_name) ";
	//if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner in($cbo_style_owner) ";

	$sql_data_smv=sql_select("select comapny_id,year, basic_smv from lib_capacity_calc_mst where year between $cbo_year_name and $cbo_end_year_name");
	foreach( $sql_data_smv as $row)
	{
		$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
	}

	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}
	// $order_status1 =""; $order_status2 ="";
	// if($cbo_order_status !=0)
	// {
	// 	$order_status1 = "and d.is_confirmed=$cbo_order_status";
	// 	$order_status2 = "and b.is_confirmed=$cbo_order_status";
	// }
	$year_cond="";
	if($cbo_year)
	{
		$year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year";
	}
	
	$tot_month = datediff( 'm', $s_date,$e_date);
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($s_date,$i);
		$month_arr[]=date("Y-m",strtotime($next_month));
	}
	
	$sql_con_po="SELECT a.job_no,a.total_set_qnty,a.buyer_name,a.company_name, a.team_leader,a.set_smv,b.id as po_id, b.po_received_date,b.pub_shipment_date,b.po_quantity as po_qty, (b.po_quantity*a.total_set_qnty) as po_quantity, b.unit_price, b.is_confirmed,b.po_total_price, c.buyer_name as buyer_full FROM wo_po_details_master a, wo_po_break_down b, lib_buyer c WHERE a.id=b.job_id  AND b.pub_shipment_date between '$s_date' and '$e_date' and a.status_active=1 and b.is_confirmed=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.buyer_name=c.id    $company_conds $buyers_cond $year_cond order by b.pub_shipment_date,a.buyer_name asc";
	$sql_data_po=sql_select($sql_con_po); $buyer_sum_arr=array(); $team_ledr_arr=array(); $jobSmvArr_chk=array();$po_qnty_as_avg_smv_buyer=array();
	foreach( $sql_data_po as $row_po)
	{
		$date_key=date("F-y",strtotime($row_po[csf("pub_shipment_date")]));
		$conf_comp_mon_arr[$date_key][$row_po[csf("company_name")]]+=$row_po[csf("po_total_price")];
		$company_NameArr[$row_po[csf("company_name")]]=$row_po[csf("company_name")];
		$buyer_sum_arr[$row_po[csf("buyer_name")]]=$row_po[csf("buyer_full")];
		$buyer_mon_arr[$date_key][$row_po[csf("buyer_name")]]+=$row_po[csf("po_total_price")];
		$poIdArr[$row_po[csf("po_id")]]=$row_po[csf("po_id")];
		 
	}
	
	$con = connect();
 	execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=142");
	 fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 142, 1, $poIdArr, $empty_arr);//PO ID Ref from=1

	 $sql_po_prod=sql_select("SELECT a.company_name as COMPANY_ID,a.LOCATION_NAME,b.id as POID,b.PO_TOTAL_PRICE,b.PUB_SHIPMENT_DATE,b.UNIT_PRICE,c.PRODUCTION_DATE,c.EMBEL_NAME,c.PRODUCTION_TYPE,g.REF_FROM, b.job_no_mst as JOB_NO,
	(case when  c.production_type=4   then c.PRODUCTION_QUANTITY else 0 end) as INPUT_PROD,
	(case when  c.production_type=5     then c.PRODUCTION_QUANTITY else 0 end) as OUT_PROD
	from wo_po_details_master a,wo_po_break_down b, pro_garments_production_mst c,gbl_temp_engine g where a.id=b.job_id and b.id=c.po_break_down_id   
	and b.id=g.ref_val and c.po_break_down_id=g.ref_val  and g.user_id = ".$user_id." and g.ref_from in(1) and g.entry_form=142  
	 and c.PRODUCTION_QUANTITY>0 and b.is_confirmed=1 and  b.status_active=1 and b.is_deleted=0 and c.production_type in(5) and  c.status_active=1 and c.is_deleted=0   order by b.PUB_SHIPMENT_DATE asc");

	 
	 foreach ($sql_po_prod as $val) 
	{
		$datekey=date("F-y",strtotime($val["PUB_SHIPMENT_DATE"]));
		$sew_outQty=$val['OUT_PROD'];
		$sew_val=$sew_outQty*$val['UNIT_PRICE'];
		$company_NameArr[$val['COMPANY_ID']]=$val['COMPANY_ID'];
		//$conf_comp_mon_arr[$datekey][$val['COMPANY_ID']]+=$val[("PO_TOTAL_PRICE")]-$sew_val;
			
	}

	
	if($type==1)// Show 2
	{
		$grand_tot_qty=$grand_tot_val=0;
		// foreach($month_arr as $month){
		// list($y,$m)=explode('-',$month); $m=$m*1;

		// $cQtyData='[';
		// $cValData='[';
		// foreach($po_qnty_as_buyer[$month] as $buyer=>$qty):
		// 	$cQtyData.="{name: '".$buyer_short_arr[$buyer]."',y: $qty},";
		// 	$cValData.="{name: '".$buyer_short_arr[$buyer]."',y: ".$po_val_as_buyer[$month][$buyer]."},";
		// endforeach;
		// $cQtyData=rtrim($cQtyData,',');
		// $cValData=rtrim($cValData,',');
		// $cQtyData.=']';
		// $cValData.=']';
$tot_buyer_count=count($buyer_sum_arr);
$recv_width=100+(80*$tot_buyer_count);

//$con = connect();
execute_query("DELETE FROM GBL_TEMP_ENGINE WHERE USER_ID = ".$user_id."  and ENTRY_FORM=142");
oci_commit($con);
disconnect($con); 

ob_start();
	 ?>
<div  style="width:100%">
			<div style="430px; text-align:center; margin:5px;">
				  <h2>Buyer Wise Order Received Value</h2>
			 </div>
			<table cellspacing="0" width="<?=$recv_width;?>"  border="1" rules="all" class="rpt_table" >
				<thead>
				<tr>
					<th width="100" rowspan="2">Month</th>
					<th width="80" colspan="<?=count($buyer_sum_arr);?>" align="center">Buyer</th>
				</tr>
					<tr>
					<?
					foreach ($buyer_sum_arr as $buyer_id => $val) 
					{?>
					<th width="80" title="<?=$buyer_id;?>"><?=$val;?></th>
					<?
					}
					?>
					</tr>
				</thead>
				<?
				$buyer_new_order_val_arr=array();
				foreach($buyer_mon_arr as $mon_key=>$monData)
				{
					foreach($monData as $buyer=>$val)
					{
						$buyer_new_order_val_arr[$buyer]+=$val;
					}
				}
				?>
				<tbody>
				<tr>
				<td width="100" align="center"><b>Total</b></td>
					<?
					foreach ($buyer_sum_arr as $buyer_id => $val) 
					{?>
					<td width="80" align="right" title="<?=$buyer_id;?>"><b><?=number_format($buyer_new_order_val_arr[$buyer_id],2);?></b></td>
					<?
					}
					?>
			</tr>

			<?
				$i=1; $tot_qty=$tot_val=$tot_avg_smv=$tot_tot_sah=0;
				foreach($buyer_mon_arr as $mon_key=>$monData)
				{
					//print_r($monData);			
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $y.$m.$i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y.$m.$i; ?>">
						<td width="100" align="center"><? echo $mon_key; ?></td>
						<?
						 foreach($buyer_sum_arr as $buyer=>$val)
						 {
						?>
							<td align="right" width="80"><p><? echo number_format($buyer_mon_arr[$mon_key][$buyer],2); ?></p></td>
						<?
						 }
						?>
						 
					</tr>
				<?
				$i++;
				 
				}
			 
			?>
			   </tbody>
			</table>
			<!-- Confirmed Order Sewing Balance part-->
			<br>
			<div style="100%; text-align:center; margin:5px;">
				  <?
			
				$tot_comp_count=count($company_NameArr);
				$conf_width=180+(80*$tot_comp_count);
				  ?>
			<table cellspacing="0" width="<?=$conf_width;?>"  border="1" rules="all" class="rpt_table" >
			<tr>
			<td>
			<table cellspacing="0" width="<?=$conf_width;?>" style="margin:5px;"  border="1" rules="all" class="rpt_table" >
			<caption><h2>Confirmed Order FOB Value(Sewing Balance)</h2> </caption>
				<thead>
				<tr>
					<th width="100" rowspan="2">Month</th>
					<th width="80" colspan="<?=count($company_NameArr)+1;?>" align="center">Company</th>
				</tr>
				<tr>
				<?
				foreach ($company_NameArr as $company_id => $val) 
				{?>
				<th width="80" title="<?=$company_id;?>"><?=$companyShortArr[$company_id];?></th>
				<?
				}
				?>
				<th width="80" align="center">Total</th>
				</tr>
				</thead>
				<?
				$company_conf_order_val_arr=array();
				foreach($company_NameArr as $mon_key=>$monData)
				{
					foreach($monData as $company=>$val)
					{
						$company_conf_order_val_arr[$company]+=$val;
					}
				}
				?>
				<tbody>
			<?
				$j=1; $tot_qty=$tot_val=$tot_avg_smv=$tot_tot_sah=0;//conf_comp_mon_arr
			 //	print_r($conf_comp_mon_arr);
				foreach($conf_comp_mon_arr as $mon_key=>$ComMonData)
				{
					 
					$bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
				?>
					<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('trConf_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trConf_<? echo $j; ?>">
						<td width="100" align="center"><? echo $mon_key; ?></td>
						<?
						$company_val_wiseArr=array();$company_val=0;
						 foreach($company_NameArr as $com_id=>$val)
						 {
							$company_val_wiseArr[$com_id]+=$conf_comp_mon_arr[$mon_key][$com_id]-$sew_val;
							$company_val+=$conf_comp_mon_arr[$mon_key][$com_id]-$sew_val;
							$company_mon_wiseArr[$mon_key]+=$conf_comp_mon_arr[$mon_key][$com_id]-$sew_val;
						?>
							<td align="right" width="80" title="Fob Value-Sew Out Value"><p><?  echo fn_number_format($conf_comp_mon_arr[$mon_key][$com_id]-$sew_val,2); ?></p></td>
						<?
						
						 }
						?>
						 <td align="right" width="80"><p><? echo fn_number_format($company_val,2); ?></p></td>
					</tr>
				<?
				$j++;
				}
				//$nameArray=sql_select( "select sum(capacity_in_value) as capacity_in_value  from  variable_settings_commercial where company_name in($cbo_company_name) and variable_list=5 order by id" );
//c.WORKING_DAY,c.CAPACITY_MONTH_MIN,c.CAPACITY_MONTH_PCS,c.BASIC_SMV,c.AVG_RATE
				  $CalculationArray=sql_select("SELECT  a.COMAPNY_ID,a.YEAR,c.WORKING_DAY,c.CAPACITY_MONTH_MIN,c.MONTH_ID,c.CAPACITY_MONTH_PCS,a.BASIC_SMV, a.AVG_RATE  as AVG_RATE FROM lib_capacity_calc_mst a, lib_capacity_calc_dtls b, lib_capacity_year_dtls c WHERE a.id = c.mst_id AND a.id = b.mst_id AND b.mst_id = c.mst_id and b.month_id=c.month_id and a.year between $cbo_year_name and $cbo_end_year_name  and c.month_id between 1 and 12 and b.day_status=1 AND a.comapny_id in ($cbo_company_name) AND a.year = $cbo_year_name  AND b.DATE_CALC between '$s_date' and '$e_date' and  c.capacity_month_pcs>0 GROUP BY  c.MONTH_ID,a.YEAR,c.WORKING_DAY,c.CAPACITY_MONTH_MIN,a.AVG_RATE,c.CAPACITY_MONTH_PCS,a.BASIC_SMV,a.COMAPNY_ID ORDER BY c.MONTH_ID");
				 
				 
					foreach($CalculationArray as $row)
					{
						$datekey=date("F-y",strtotime($row[("YEAR")].'-'.$row[("MONTH_ID")]));
						//$datekey=date("F-y",strtotime($row["DATE_CALC"]));
						$capacity_arr[$datekey][$row[("WORKING_DAY")]]['CAPACITY_MONTH_MIN']+=$row[("CAPACITY_MONTH_MIN")];
						$capacity_arr[$datekey][$row[("WORKING_DAY")]]['AVG_RATE']+=$row[("AVG_RATE")];
						$capacity_arr[$datekey][$row[("WORKING_DAY")]]['BASIC_SMV']+=$row[("BASIC_SMV")];
					//	echo $row[("BASIC_SMV")].'='.$row[("AVG_RATE")].'<br>';
						$Capacity_companyArr[$row[("COMAPNY_ID")]]=$row[("COMAPNY_ID")];
						//$capacity_month_pcs_arr[$m]+=$row[csf('capacity')];
				
					}
					  /* echo "<pre>";
					 print_r($capacity_month_pcs_arr);   */
			?>
			   </tbody>
			   <tfoot>
			   <tr>
				<td width="100" align="center"><b>Total</b></td>
					<?
					$tot_com_val=0;
					foreach ($company_NameArr as $company_id => $val) 
					{
						$tot_com_val+=$company_val_wiseArr[$company_id];
						?>
					<td width="80" align="right" title="<?=$company_id;?>"><b><?=fn_number_format($company_val_wiseArr[$company_id],2);?></b></td>
					<?
					}
					?>
					<td width="100" align="right"><b><?=fn_number_format($tot_com_val,2);?></b></td>
				</tr>
			   </tfoot>
			  </table>
			  </td>
			  <td valign="top">
				<?
				$capacity_head_arr=array(1=>"Working Days",2=>"AVG SMV",3=>"AVG FOB",4=>"Month",5=>"Working Hour",6=>"Capacity, Minutes",7=>"Order In-hand, Minutes",8=>"In Hand Minutes %");
				$capacity_width=count($capacity_head_arr)*80;
				?>
			  <table cellspacing="0" width="<?=$capacity_width;?>"  style="margin:5px;"  border="1" rules="all" class="rpt_table" >
			<caption><h2> &nbsp;&nbsp;</h2> </caption>
				<thead>
				 
					<tr>
					<?
					foreach ($capacity_head_arr as $head_td => $val) 
					{?>
					<th width="80" title="<?=$head_td;?>"><?=$val;?></th>
					<?
					}
					?>
					</tr>
				</thead>
				<?
				 
				?>
				<tbody>
				 

			<?
			$fullMonthArr=array();$capacityInhandMinMMArr=array();
				$j=1; $tot_qty=$tot_val=$tot_avg_smv=$tot_tot_sah=0;
				foreach($capacity_arr as $mon_key=>$monData)
				{
					//print_r($monData);
					foreach($monData as $work_day=>$val)
					{
				
					$bgcolor=($j%2==0)?"#E9F3FF":"#FFFFFF";
					$tot_company=count($Capacity_companyArr);
					//echo $tot_company.'='.$val['BASIC_SMV'].'='.$val['AVG_RATE'].'<br>';
					$avg_smv=$val['BASIC_SMV']/$tot_company;$avg_fob=$val['AVG_RATE']/$tot_company;$capcity_min=$val['CAPACITY_MONTH_MIN'];
					$company_sew_val_mon=$company_mon_wiseArr[$mon_key];
				

					$order_in_hand=($company_sew_val_mon/$avg_fob)*$avg_smv/1000000;
					$capcity_min_mm=($capcity_min/1000000);
					 
				?>
					<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('trwork_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="trwork_<? echo $j; ?>">
						<td width="100" align="center"><? echo $work_day; ?></td>
						<td width="100" align="right"><? echo fn_number_format($avg_smv,2); ?></td>
						<td width="100" align="right"><? echo fn_number_format($avg_fob,2); ?></td>
						<td width="100" align="center"><? echo $mon_key; ?></td>
						<td width="100" align="center">11</td>
						<td width="100" align="right" title="Capacity Min/1000000"><? echo fn_number_format($capcity_min_mm,1).' M'; ?></td>
						<td width="100" align="right" title="Sew Bal/Avg Fob*Avg SMV/1000000"><? echo fn_number_format($order_in_hand,1).' M'; ?></td>
						<td align="right" title="Capacity Min/Order In Hand*100" width="80"><p><? echo fn_number_format($order_in_hand/$capcity_min_mm*100,2); ?></p></td>
						<?
					//$fullMonthArr[]=date("M-Y",strtotime($mon_key));
					$inhand_min_per=fn_number_format(($order_in_hand/$capcity_min_mm)*100,2);
						
						//$momAll=date("M-Y",strtotime($mon_key));
						if($inhand_min_per>0)
						{
							//echo $inhand_min_per.'='.$mon_key.'<br>';
							//$fullMonthArr[date("M-Y",strtotime($mon_key))]=date("M-Y",strtotime($mon_key));
							$fullMonthArr[]=strtotime($mon_key);
						}

					if($capcity_min_mm>0 && $inhand_min_per>0)
					{
						$capacityMinArr[]=fn_number_format($capcity_min_mm,2);
					}
					if($order_in_hand>0 && $inhand_min_per>0)
					{
					$capacityInhandMinMMArr[]=fn_number_format($order_in_hand,2);
					}
						?>
						 
					</tr>
				<?
				$j++;
					}
				}
				 
			?>
			   </tbody>
			  </table>
			  </td>
				</tr>
				</table>
			</div>

		  
		  
</div>
		<?
		 asort($fullMonthArr); $fullMonth_allArr=array();
		 foreach($fullMonthArr as $dateKey=>$val)
		 {
			 // echo $val.'<br>';
			 $fullMonth_allArr[date("M-Y",$val)]=date("M-Y",$val);
			 //$fullMonthArr[strtotime($mon_key)]=strtotime($mon_key);
		 }
		 $CapacityMintStr = implode(',',$capacityMinArr);
		 $monthStr = implode("','",$fullMonth_allArr);
		// echo $monthStr.'=';
		 $capacityMinMM_Str= implode(",",$capacityInhandMinMMArr);
	}
	//print_r($monthStr);
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);

	$html.='<br><div id="container" style="width:'.$capacity_width.'px;border:1px solid #CCC;"></div>';
	$html.='<br><div id="container_min" style="width:'.$capacity_width.'px;border:1px solid #CC6;"></div>';

	echo "$html####$filename####$CapacityMintStr####'$monthStr'####$capacityMinMM_Str";

	exit();
}
?>
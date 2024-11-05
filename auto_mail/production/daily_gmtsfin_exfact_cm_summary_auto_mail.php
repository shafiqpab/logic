<?php
	date_default_timezone_set("Asia/Dhaka");
	require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
	require_once('setting/mail_setting.php');
	
	
	function removeNullZero($ste)
	{
		if($ste>0)return $ste;
		else return '';
	}
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$type=str_replace("'","",$type);
	$cbo_working_company=str_replace("'","",$cbo_working_company_id);
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name");
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
	if(str_replace("'","",$cbo_location)==0){$location_con="";}else{$location_con=" and a.location=$cbo_location";}
	
	$companyKey=array_keys($company_library);
	$sales_year_started=return_field_value("sales_year_started"," variable_order_tracking"," company_name=".$companyKey[0]." and variable_list=12"); 
	//echo $sales_year_started;die;
	if($sales_year_started > date("m",time())){
		$back_month=(12-$sales_year_started)+date("m",time());
	}
	else{
		$back_month=(0-$sales_year_started)+date("m",time());
	}
	
	//$back_month=6;	
	
	$this_month_start_date=date("Y-m",time())."-"."01";
	if($db_type==0)
	{
		$prevMonthEffectiveDate = date("Y-m-d", strtotime("-$back_month months", strtotime($this_month_start_date)));
	}
	else
	{
		$prevMonthEffectiveDate = change_date_format(date("Y-m-d", strtotime("-$back_month months", strtotime($this_month_start_date))),'','',1);
	}
	
	$company_library=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0 and CORE_BUSINESS=1", "id", "company_name"  );
	$company_short_library=return_library_array( "select id, company_short_name from lib_company where  status_active=1 and is_deleted=0 and CORE_BUSINESS=1", "id", "company_short_name"  );
	
	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),0)));
		$prev_date = date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
		$previous_saturday = date( 'Y-m-d', strtotime( 'previous Saturday'));
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_date(date("Y-m-d",time()),0))),'','',1);
		$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
		$previous_saturday = change_date_format(date( 'Y-m-d H:i:s', strtotime( 'previous Saturday')),'','',1);
	}
	
//------Daily Ex Factory Report as on today.........................................................	
	
	$date = new DateTime('first Saturday of this month');
	$thisMonth = $date->format('m');
	$firstMonth = $date->format('W');
	$previousWeek = date('W',strtotime("Sunday Last Week"));
	$currentWeek = date('W',strtotime("Sunday This Week"));
	
/*	$weekDateArr=array();
	while ($date->format('m') === $thisMonth) {
		$weekDateArr[$date->format('W')]= $date->format('d-m-Y');
		$date->modify('next Saturday');
	}
	
	$previous_saturday=$weekDateArr[$currentWeek];
*/	
	//---------------------------------
	$saturday = strtotime("last saturday");
	$saturday = date('w', $saturday)==date('w') ? $saturday+7*86400 : $saturday;
	$this_week_start = date("Y-m-d",$saturday);
	//---------------------	
	$previous_saturday=	$this_week_start;
	
	/*$companyKey=array_keys($company_library);
	$sales_year_started=return_field_value("sales_year_started"," variable_order_tracking"," company_name=".$companyKey[0]." and variable_list=12"); 
	//echo $sales_year_started;die;
	if($sales_year_started > date("m",time())){
		$back_month=(12-$sales_year_started)+date("m",time());
	}
	else{
		$back_month=(0-$sales_year_started)+date("m",time());
	}
	
	//$back_month=6;	
	
	$this_month_start_date=date("Y-m",time())."-"."01";
	if($db_type==0)
	{
		$prevMonthEffectiveDate = date("Y-m-d", strtotime("-$back_month months", strtotime($this_month_start_date)));
	}
	else
	{
		$prevMonthEffectiveDate = change_date_format(date("Y-m-d", strtotime("-$back_month months", strtotime($this_month_start_date))),'','',1);
	}*/
	
	$sql_res=sql_select("select b.po_break_down_id as po_id, 
	sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty 
	from pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
	$ex_factory_qty_arr=array();
	foreach($sql_res as $row)
	{
		$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('return_qnty')];
	}
		
		//$str_cond="and a.ex_factory_date between '$previous_saturday' and  '$current_date' ";
		$str_cond="and a.ex_factory_date between '$prevMonthEffectiveDate' and  '$current_date' ";
		
		$sql= "select c.job_no,a.ex_factory_date,b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty,c.company_name,a.ex_factory_qnty as ex_factory_qnty from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d  
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no   $str_cond  and  a.entry_form!=85  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.delivery_mst_id= d.id order by a.ex_factory_date ASC ";//c.job_no; and c.job_no in ('AST-20-00337','AST-20-00371') and (a.ex_factory_date='31-Dec-2020' or a.ex_factory_date='27-Dec-2020')		
		//echo $sql;die;
		
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			
			$exfactreturn_qty=$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty'];
			
			$dateKey=date("Y-m-d",strtotime($row[csf("ex_factory_date")]));
			$monthDateKey=date("M-Y",strtotime($row[csf("ex_factory_date")]));
			
			
			$result_data_arr[$row[csf("company_name")]][$dateKey]['ex_factory_qnty'] +=$row[csf("ex_factory_qnty")]-$exfactreturn_qty;
			$result_data_arr[$row[csf("company_name")]][$dateKey]['ex_factory_value'] +=($row[csf("ex_factory_qnty")]*$row[csf("unit_price")]);
			$month_data_arr[$row[csf("company_name")]][$monthDateKey]['qty'] +=$row[csf("ex_factory_qnty")]-$exfactreturn_qty;
			$month_data_arr[$row[csf("company_name")]][$monthDateKey]['val'] +=($row[csf("ex_factory_qnty")]*$row[csf("unit_price")]);
		
		 $costing_per=$costing_per_arr[$row[csf("job_no")]];
				 
				 if($costing_per==1) $dzn_qnty=12;
				 else if($costing_per==3) $dzn_qnty=12*2;
				 else if($costing_per==4) $dzn_qnty=12*3;
				 else if($costing_per==5) $dzn_qnty=12*4;
				 else $dzn_qnty=1;
				 $dozn_qnty=0;			
				 $dozn_qnty=($tot_cost_arr[$row[csf("job_no")]]/$dzn_qnty)*($row[csf("ex_factory_qnty")]/$row[csf("total_set_qnty")]);
				 $result_data_arr2[$row[csf("company_name")]][$dateKey]['exfact_cm']+=$dozn_qnty;
				 $month_data_arr2[$row[csf("company_name")]][$monthDateKey]['exfact_cm']+=$dozn_qnty;
			
		}
		//var_dump($result_data_arr);
		
		
	//echo $previous_saturday;	
	//Cum. up to current date...........................................................
	$cum_up_to_current_arr=array();
	$today = date('d',strtotime($previous_saturday));
	foreach($company_short_library as $compid=>$company_name){
		for($i=1;$i<$today;$i++){ 
			$dateKey = date('Y-m-d', strtotime($previous_saturday . " -$i day"));
			
			$cum_up_to_current_arr['qty'][$compid]+=$result_data_arr[$compid][$dateKey]['ex_factory_qnty'];
			$cum_up_to_current_arr['val'][$compid]+=$result_data_arr[$compid][$dateKey]['ex_factory_value'];
			$cum_up_to_current_arr['exfact_cm'][$compid]+=$result_data_arr2[$compid][$dateKey]['exfact_cm'];
		}
	}
	
	//echo $previous_saturday;die;
	//$tot_day = datediff( 'd', $this_month_start_date,$current_date);
	
	
	$tot_day = datediff( 'd', $previous_saturday,$current_date);
	
	 //echo $tot_day;die;
	
    $company_count=count($company_short_library);
	$tbl_width=140+($company_count*160)+160;
	ob_start();
	$flag=0;
?>
<b style="color:#FF0000; font-size:18px"> This is auto generated e-mail. Please Don't reply.</b><br/>
<br/>
<td style="font-size:14px;">Dear Sir/Madam,</td><br/>
<td style="font-size:14px;">The following summary report is sent for your kind attention.</td><br/><td style="font-size:14px;">Feel free to contact with MIS Team for any query.</td><br/>
<td style="font-size:14px;">Thanks,</td><br/>
<td style="font-size:14px;">System Admin</td><br/>

<b style="font-size:28px;">Asrotex Group</b><br/>
<b style="font-size:18px; color:#00F">Ex-Factory Value & CM as on <? echo date('dS F-Y', time());?> </b> <b style="font-size:18px; color:#B00000">[ N.B. CM Value based on Pre-Cost CM ]</b>
<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="1" border="1" rules="all">
    <thead>
    <tr style="font-size:12px" bgcolor="#CCCCFF">
        <th rowspan="2" width="140">Details <br /><small style="font-size:10px;">[Week Start on Saturday]</small></th>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<th colspan='3'>$company_name</th>";
			}
		?>
        <th colspan="3">BU Total</th>	
    </tr>    
    <tr style="font-size:12px" bgcolor="#CCCCFF">    
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<th width='60'>Ex-Fact Val</th><th width='60'>Ex-Fact CM</th><th width='30'>CM %</th>";
			}
		?>
        <th width="60">Ex-Fact Val</th>
        <th width="60">Ex-Fact CM</th>
        <th width="30">CM %</th>
    </tr>
    </thead>
    
	<?php 
	
	$com_ex_factory_qnty_arr=array();
	$com_ex_factory_value_arr=array();
	
	for($i=0;$i<$tot_day;$i++){ 
	
	$dsDate = date('dS F', strtotime($previous_saturday . " +$i day"));
	$dateKey = date('Y-m-d', strtotime($previous_saturday . " +$i day"));
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	
	
	?>
    <tr bgcolor="<? echo $bgcolor;?>" style="font-size:12px">
    	<td align="left"><? echo $dsDate;?></td>
        <? 
		foreach($company_short_library as $compid=>$company_name){
			$ex_factory_value=$result_data_arr[$compid][$dateKey]['ex_factory_value'];
			$ex_factory_cm=$result_data_arr2[$compid][$dateKey]['exfact_cm'];
			//$ex_factory_cm_per=$result_data_arr[$compid][$dateKey]['cm %'];
			
			$com_ex_factory_value_arr[$compid]+=$ex_factory_value;
			$com_ex_factory_cm_arr[$compid]+=$ex_factory_cm;
			//$com_ex_factory_cm_per_arr[$compid]+=$ex_factory_cm_per;
			
			$day_ex_factory_value_arr[$dateKey]+=$ex_factory_value;
			$day_ex_factory_cm_arr[$dateKey]+=$ex_factory_cm;
			//$day_ex_factory_cm_per_arr[$dateKey]+=$ex_factory_cm_per;
			
			echo "<td width='60' align='right'>".removeNullZero(number_format($ex_factory_value))."</td> 
				  <td width='60' align='right'>".removeNullZero(number_format($ex_factory_cm))."</td>
				  <td width='30' align='right'>".removeNullZero(number_format($ex_factory_cm/$ex_factory_value*100))."</td>";
			}
		?>
    	<td align="right"><? echo removeNullZero(number_format($day_ex_factory_value_arr[$dateKey]));?></td>
        <td align="right"><? echo removeNullZero(number_format($day_ex_factory_cm_arr[$dateKey]));?></td>
        <td align="right"><? echo removeNullZero(number_format($day_ex_factory_cm_arr[$dateKey]/$day_ex_factory_value_arr[$dateKey]*100));?></td>
    </tr>
    <?php } ?>
    
    <tr bgcolor="#FFFFAA" style="font-size:12px">
    	<td><b>Current Week Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<td width='60' align='right'><b>".removeNullZero(number_format($com_ex_factory_value_arr[$compid]))."</b></td>
				  <td width='60' align='right'><b>".removeNullZero(number_format($com_ex_factory_cm_arr[$compid]))."</b></td>
				  <td width='30' align='right'><b>".removeNullZero(number_format($com_ex_factory_cm_arr[$compid]/$com_ex_factory_value_arr[$compid]*100))."</b></td>";
			}
		?>
    	<td align="right"><b><? echo removeNullZero(number_format(array_sum($com_ex_factory_value_arr)));?></b></td>
    	<td align="right"><b><? echo removeNullZero(number_format(array_sum($com_ex_factory_cm_arr)));?></b></td>
        <td align="right"><b><? echo removeNullZero(number_format(array_sum($com_ex_factory_cm_arr)/array_sum($com_ex_factory_value_arr)*100));?></b></td>
    </tr>
    
    
    <tr bgcolor="#5BFFAD" style="font-size:12px">
    	<td><b>Previous Week Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<td width='60' align='right'><b>".removeNullZero(number_format($cum_up_to_current_arr['val'][$compid]))."</b></td>
				  <td width='60' align='right'><b>".removeNullZero(number_format($cum_up_to_current_arr['exfact_cm'][$compid]))."</b></td>
				  <td width='30' align='right'><b>".removeNullZero(number_format($cum_up_to_current_arr['exfact_cm'][$compid]/$cum_up_to_current_arr['val'][$compid]*100))."</b></td>";
			}
		?>
    	<td align="right"><b><? echo removeNullZero(number_format(array_sum($cum_up_to_current_arr['val'])));?></b></td>
    	<td align="right"><b><? echo removeNullZero(number_format(array_sum($cum_up_to_current_arr['exfact_cm'])));?></b></td>
        <td align="right"><b><? echo removeNullZero(number_format(array_sum($cum_up_to_current_arr['exfact_cm'])/array_sum($cum_up_to_current_arr['val'])*100));?></b></td>
    </tr>
    
	<?php
	for($i=1;$i<=$back_month;$i++){ 
	$monthDateKey = date("M-Y", strtotime("-$i months", strtotime($current_date)));
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

	?>
    <tr bgcolor="<? echo $bgcolor;?>" style="font-size:12px">
    	<td align="left"><? echo $monthDateKey;?></td>
        <? 
		foreach($company_short_library as $compid=>$company_name){
			$month_ex_factory_qnty=$month_data_arr[$compid][$monthDateKey]['qty'];
			$month_ex_factory_value=$month_data_arr[$compid][$monthDateKey]['val'];
			$month_ex_factory_cm=$month_data_arr2[$compid][$monthDateKey]['exfact_cm'];
			
			$all_mon_ex_factory_qnty_arr[$compid]+=$month_ex_factory_qnty;
			$all_mon_ex_factory_value_arr[$compid]+=$month_ex_factory_value;
			$all_mon_ex_factory_cm_arr[$compid]+=$month_ex_factory_cm;
			
			$mon_ex_factory_qnty_arr[$monthDateKey]+=$month_ex_factory_qnty;
			$mon_ex_factory_value_arr[$monthDateKey]+=$month_ex_factory_value;
			$mon_ex_factory_cm_arr[$monthDateKey]+=$month_ex_factory_cm;
			
			echo "<td width='60' align='right'>".removeNullZero(number_format($month_ex_factory_value))."</td>
				  <td width='60' align='right'>".removeNullZero(number_format($month_ex_factory_cm))."</td>
				  <td width='30' align='right'>".removeNullZero(number_format($month_ex_factory_cm/$month_ex_factory_value*100))."</td>";
			}
		?>
    	<td align="right"><? echo removeNullZero(number_format($mon_ex_factory_value_arr[$monthDateKey]));?></td>
    	<td align="right"><? echo removeNullZero(number_format($mon_ex_factory_cm_arr[$monthDateKey]));?></td>
        <td align="right"><? echo removeNullZero(number_format($mon_ex_factory_cm_arr[$monthDateKey]/$mon_ex_factory_value_arr[$monthDateKey]*100));?></td>
    </tr>
    <?php } ?>
    
    
    <tr bgcolor="#FFD8B0" style="font-size:12px">
    	<td><b>Grand Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name)
		{
echo "<td width='60' align='right'><b>".removeNullZero(number_format($com_ex_factory_value_arr[$compid]+$cum_up_to_current_arr['val'][$compid]+$all_mon_ex_factory_value_arr[$compid]))."</b></td>
	  <td width='60' align='right'><b>".removeNullZero(number_format($com_ex_factory_cm_arr[$compid]+$cum_up_to_current_arr['exfact_cm'][$compid]+$all_mon_ex_factory_cm_arr[$compid]))."</b></td>
	  <td width='30' align='right'><b>".removeNullZero(number_format(($com_ex_factory_cm_arr[$compid]+$cum_up_to_current_arr['exfact_cm'][$compid]+$all_mon_ex_factory_cm_arr[$compid])/($com_ex_factory_value_arr[$compid]+$cum_up_to_current_arr['val'][$compid]+$all_mon_ex_factory_value_arr[$compid])*100))."</b></td>";
		}
		?>
    <td align="right"><b><? echo removeNullZero(number_format(array_sum($com_ex_factory_value_arr)+array_sum($cum_up_to_current_arr['val'])+array_sum($all_mon_ex_factory_value_arr)));?></b></td>
    <td align="right"><b><? echo removeNullZero(number_format(array_sum($com_ex_factory_cm_arr)+array_sum($cum_up_to_current_arr['exfact_cm'])+array_sum($all_mon_ex_factory_cm_arr)));?></b></td>
    <td align="right"><b><? echo removeNullZero(number_format((array_sum($com_ex_factory_cn_arr)+array_sum($cum_up_to_current_arr['exfact_cm'])+array_sum($all_mon_ex_factory_cm_arr))/(array_sum($com_ex_factory_value_arr)+array_sum($cum_up_to_current_arr['val'])+array_sum($all_mon_ex_factory_value_arr))*100));?></b></td>
        
    </tr>
    
</table>

<?	
	
//------Daily Gmts Finish Report as on today.........................................................	
	/*
	$date = new DateTime('first Saturday of this month');
	$thisMonth = $date->format('m');
	$firstMonth = $date->format('W');
	$previousWeek = date('W',strtotime("Sunday Last Week"));
	//$currentWeek = date('W',strtotime("Sunday This Week"));
	
	$weekDateArr=array();
	while ($date->format('m') === $thisMonth) {
		$weekDateArr[$date->format('W')]= $date->format('d-m-Y');
		$date->modify('next Saturday');
	}
	
	//$previous_saturday=$weekDateArr[$previousWeek];
	
	//echo $previous_saturday;die;
	//$previous_saturday='2-Jan-2021';
	
	//print_r($weekDateArr); */
	
	 
	
	
	
		//$str_cond="and a.production_date between '$previous_saturday' and  '$current_date' ";
		$str_cond="and a.production_date between '$prevMonthEffectiveDate' and  '$current_date' ";
		
/*	$job_array=array(); 
		$job_sql="SELECT a.id, a.unit_price,b.buyer_name,b.company_name,a.po_quantity, b.job_no, b.total_set_qnty,b.set_smv from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active in(1,3) and b.is_deleted=0 and b.status_active=1 ";
		
		//$job_sql="select a.job_no, a.total_set_qnty,b.id, b.unit_price,c.smv_pcs,c.set_item_ratio from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($cbo_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
		
		
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$job_array[$row[csf("job_no")]]['unit_price']=($row[csf("unit_price")]/$row[csf("total_set_qnty")]);
			$job_array[$row[csf("job_no")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("job_no")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$job_array[$row[csf("job_no")]]['set_smv']=$row[csf("set_smv")];
			$job_array_summary[$row[csf("company_name")]][$row[csf("buyer_name")]]['po_qty']+=$row[csf("po_quantity")];
			//$job_array[$row[csf("id")]][$row[csf("set_item_ratio")]]['smv_pcs']=$row[csf("smv_pcs")];
		}*/	
		
		/*$sql= "select a.ex_factory_date,b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price, c.company_name,a.ex_factory_qnty as ex_factory_qnty from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d  
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no   $str_cond  and  a.entry_form!=85  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.delivery_mst_id= d.id order by a.ex_factory_date ASC ";//c.job_no
	//echo $sql;die;*/
	
	/*$sql= "SELECT a.production_date, a.po_break_down_id as po_breakdown_id,a.item_number_id,a.company_id, 
sum(CASE WHEN production_type =8 THEN production_quantity END) AS finish_qnty, 
sum(CASE WHEN production_type =8 and production_source=1 THEN production_quantity END) AS finish_qnty_inhouse, 
sum(CASE WHEN production_type =8 and production_source=3 THEN production_quantity END) AS finish_qnty_outbound, 
sum(CASE WHEN production_type =8 THEN carton_qty END) AS carton_qty 
from pro_garments_production_mst a, wo_po_break_down b 
where a.po_break_down_id=b.id $str_cond and a.is_deleted=0 and a.status_active=1 and b.status_active in(1,3) 
group by production_date, po_break_down_id, item_number_id, company_id order by production_date asc";//c.job_no
	//echo $sql;die;*/
	
	$sql= "SELECT c.job_no, a.production_date, a.po_break_down_id as po_breakdown_id,a.company_id,(b.unit_price/c.total_set_qnty) as unit_price,c.total_set_qnty,
CASE WHEN production_type =8 THEN production_quantity END AS finish_qnty, 
CASE WHEN production_type =8 and production_source=1 THEN production_quantity END AS finish_qnty_inhouse, 
CASE WHEN production_type =8 and production_source=3 THEN production_quantity END AS finish_qnty_outbound, 
CASE WHEN production_type =8 THEN carton_qty END AS carton_qty 
from pro_garments_production_mst a, wo_po_break_down b ,wo_po_details_master c
where  a.po_break_down_id=b.id $str_cond and a.is_deleted=0 
and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active in(1,3) 
order by production_date asc";//c.job_no 'AST-20-00337',and c.job_no in ('AST-20-00337','AST-20-00371') and (a.production_date='25-Jan-2021' or a.production_date='27-Dec-2020')
	//echo $sql;die;
	
		$sql_result=sql_select($sql);
	
		foreach($sql_result as $row)
		{
			$dateKey=date("Y-m-d",strtotime($row[csf("production_date")]));
			$monthDateKey=date("M-Y",strtotime($row[csf("production_date")]));
			
			$result_data_arr[$row[csf("company_id")]][$dateKey]['finish_value']+=($row[csf("finish_qnty")]*$row[csf("unit_price")]);
			$result_data_arr[$row[csf("company_id")]][$dateKey]['finish_qnty']+=$row[csf("finish_qnty")];
			
			$month_data_arr[$row[csf("company_id")]][$monthDateKey]['finish_value'] +=($row[csf("finish_qnty")]*$row[csf("unit_price")]);
			$month_data_arr[$row[csf("company_id")]][$monthDateKey]['finish_qnty'] +=$row[csf("finish_qnty")];
			
			
		//$finish_qnty=$row[csf("finish_qnty")];
		//$job_no=$job_array[$row[csf("job_no")]]['job_no'];
				 //$total_set_qnty=$job_array[$row[csf("job_no")]]['total_set_qnty'];
				 
				 $costing_per=$costing_per_arr[$row[csf("job_no")]];
				 
				 if($costing_per==1) $dzn_qnty=12;
				 else if($costing_per==3) $dzn_qnty=12*2;
				 else if($costing_per==4) $dzn_qnty=12*3;
				 else if($costing_per==5) $dzn_qnty=12*4;
				 else $dzn_qnty=1;
				 $dozn_qnty=0;			
				 $dozn_qnty=($tot_cost_arr[$row[csf("job_no")]]/$dzn_qnty)*($row[csf("finish_qnty")]/$row[csf("total_set_qnty")]);
				 $result_data_arr[$row[csf("company_id")]][$dateKey]['cm']+=$dozn_qnty;
				 $month_data_arr[$row[csf("company_id")]][$monthDateKey]['cm'] +=$dozn_qnty;
				
				//echo $tot_cost_arr[$job_no].'/'.$dozn_qnty.')*'.$finish_qnty.';';
				
				 //$cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$finish_qnty;
				 //$cm_value_in=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_in;
				 //$cm_value_out=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_out;	 
		}
		
		//var_dump($result_data_arr);
		
	//echo $previous_saturday;	
	//Cum. up to current date...........................................................
	$cum_up_to_current_arr=array();
	$today = date('d',strtotime($previous_saturday));
	foreach($company_short_library as $compid=>$company_name){
		for($i=1;$i<$today;$i++){ 
			$dateKey = date('Y-m-d', strtotime($previous_saturday . " -$i day"));
			$cum_up_to_current_arr['finish_val'][$compid]+=$result_data_arr[$compid][$dateKey]['finish_value'];
			$cum_up_to_current_arr['finish_qty'][$compid]+=$result_data_arr[$compid][$dateKey]['finish_qnty'];
			$cum_up_to_current_arr['finish_cm'][$compid]+=$result_data_arr[$compid][$dateKey]['cm'];//($tot_cost_arr[$job_no]/$dozn_qnty)*$result_data_arr[$compid][$dateKey]['finish_qnty'];
//$cum_up_to_current_arr['finish_cm_per'][$compid]+=$cum_up_to_current_arr['finish_cm'][$compid]/$cum_up_to_current_arr['finish_val'][$compid]*100;
		}
		
	}

	
	//echo $previous_saturday;die;
	//$tot_day = datediff( 'd', $this_month_start_date,$current_date);
	
	
	$tot_day = datediff( 'd', $previous_saturday,$current_date);
	
	 //echo $tot_day;die;
	
    $company_count=count($company_short_library);
	$tbl_width=140+($company_count*160)+160;
	//ob_start();
	//$flag=0;
?>
<br/>
<!--<b style="font-size:26px;">Asrotex Group</b><br/>-->
<b style="font-size:18px; color:#00F">Finish Gmts Value & CM as on <? echo date('dS F-Y', time());?></b><b style="font-size:18px; color:#B00000">[ N.B. CM Value based on Pre-Cost CM ]</b>
<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="1" border="1" rules="all">
    <thead>
    <tr style="font-size:12px" bgcolor="#CCCCFF">
        <th rowspan="2" width="140" style="font-size:12px">Details <br /><small style="font-size:10px;">[Week Start on Saturday]</small></th>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<th colspan='3'>$company_name</th>";
			}
		?>
        <th colspan="3">BU Total</th>	
    </tr>    
    <tr style="font-size:12px" bgcolor="#CCCCFF">    
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<th width='60'>Fin Value</th> <th width='60'>Finish CM</th><th width='30'>CM %</th>";
			}
			
			
		?>
        <th width="60">Fin Value</th>
        <th width="60">Finish CM</th>
        <th width="30">CM %</th>
    </tr>
    </thead>
    
	<?php 
	$com_finish_value_arr=array();
	$com_finish_qnty_arr=array();
	//$com_finish_cm_arr=array();
	
	for($i=0;$i<$tot_day;$i++){ 
	
	$dsDate = date('dS F', strtotime($previous_saturday . " +$i day"));
	$dateKey = date('Y-m-d', strtotime($previous_saturday . " +$i day"));
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	
	
	?>
    <tr bgcolor="<? echo $bgcolor;?>" style="font-size:12px">
    	<td align="left"><? echo $dsDate;?></td>
        <? 
		foreach($company_short_library as $compid=>$company_name)
		{
			$gmts_finish_value=$result_data_arr[$compid][$dateKey]['finish_value'];
			$gmts_finish_qnty=$result_data_arr[$compid][$dateKey]['finish_qnty'];			
			$gmts_finish_cm=$result_data_arr[$compid][$dateKey]['cm'];//($tot_cost_arr[$job_no]/$dozn_qnty)*$result_data_arr[$compid][$dateKey]['finish_qnty'];
		    $gmts_finish_cm_per=$gmts_finish_cm/$gmts_finish_value*100;
			
			$com_finish_value_arr[$compid]+=$gmts_finish_value; 
			$com_finish_qnty_arr[$compid]+=$gmts_finish_qnty;
			$com_finish_cm_arr[$compid]+=$gmts_finish_cm;
			$com_finish_cm_per_arr[$compid]+=$gmts_finish_cm_per;
			
			$day_finish_value_arr[$dateKey]+=$gmts_finish_value;
			$day_finish_qnty_arr[$dateKey]+=$gmts_finish_qnty;
			$day_finish_cm_arr[$dateKey]+=$gmts_finish_cm;
			$day_finish_cm_per_arr[$dateKey]+=$gmts_finish_cm_per;
			
			/*echo "<td width='70' align='right'>".number_format($gmts_finish_value)."</td> <td width='70' align='right'>".number_format($gmts_finish_qnty)."</td> <td width='70' align='right'>".number_format($gmts_finish_cm)."</td>";*/
			echo "<td width='60' align='right'>".removeNullZero(number_format($gmts_finish_value))."</td> 
				  <td width='60' align='right'>".removeNullZero(number_format($gmts_finish_cm))."</td> 
				  <td width='30' align='right'>".removeNullZero(number_format($gmts_finish_cm/$gmts_finish_value*100))."</td>";
			}
		?>
        <td align="right"><? echo removeNullZero(number_format($day_finish_value_arr[$dateKey]));?></td>    	
        <td align="right"><? echo removeNullZero(number_format($day_finish_cm_arr[$dateKey]));?></td>
        <td align="right"><? echo removeNullZero(number_format($day_finish_cm_arr[$dateKey]/$day_finish_value_arr[$dateKey]*100));?></td>
    	
    </tr>
    <?php } ?>
    
    <tr bgcolor="#FFFFAA" style="font-size:12px">
    	<td><b>Current Week Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<td width='60' align='right'><b>".removeNullZero(number_format($com_finish_value_arr[$compid]))."</b></td>
				  <td width='60' align='right'><b>".removeNullZero(number_format($com_finish_cm_arr[$compid]))."</b></td>
				  <td width='30' align='right'><b>".removeNullZero(number_format($com_finish_cm_arr[$compid]/$com_finish_value_arr[$compid]*100))."</b></td>";
			}
		?>
        <td align="right"><b><? echo removeNullZero(number_format(array_sum($com_finish_value_arr)));?></b></td>
        <td align="right"><b><? echo removeNullZero(number_format(array_sum($com_finish_cm_arr)));?></b></td>
        <td align="right"><b><? echo removeNullZero(number_format(array_sum($com_finish_cm_arr)/array_sum($com_finish_value_arr)*100));?></b></td>
    	
    </tr>
    
    
    <tr bgcolor="#5BFFAD" style="font-size:12px">
    	<td><b>Previous Week Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<td width='60' align='right'><b>".removeNullZero(number_format($cum_up_to_current_arr['finish_val'][$compid]))."</b></td>
				  <td width='60' align='right'><b>".removeNullZero(number_format($cum_up_to_current_arr['finish_cm'][$compid]))."</b></td>
				  <td width='30' align='right'><b>".removeNullZero(number_format($cum_up_to_current_arr['finish_cm'][$compid]/$cum_up_to_current_arr['finish_val'][$compid]*100))."</b></td>";
			}
		?>
    	<td align="right"><b><? echo removeNullZero(number_format(array_sum($cum_up_to_current_arr['finish_val'])));?></b></td>
        <td align="right"><b><? echo removeNullZero(number_format(array_sum($cum_up_to_current_arr['finish_cm'])));?></b></td>
        <td align="right"><b><? echo removeNullZero(number_format(array_sum($cum_up_to_current_arr['finish_cm'])/array_sum($cum_up_to_current_arr['finish_val'])*100));?></b></td>
    </tr>
    
	<?php
	
	for($i=1;$i<=$back_month;$i++){ 
	$monthDateKey = date("M-Y", strtotime("-$i months", strtotime($current_date)));
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";

	?>
    <tr bgcolor="<? echo $bgcolor;?>" style="font-size:12px">
    	<td align="left"><? echo $monthDateKey;?></td>
        <? 
		
	
		foreach($company_short_library as $compid=>$company_name){
			$month_finish_value=$month_data_arr[$compid][$monthDateKey]['finish_value'];
			$month_finish_qnty=$month_data_arr[$compid][$monthDateKey]['finish_qnty'];
			$month_finish_cm=0;
			$month_finish_cm= $month_data_arr[$compid][$monthDateKey]['cm'];//($tot_cost_arr[$job_no]/$dozn_qnty)*$month_data_arr[$compid][$monthDateKey]['finish_qnty'];
			$val .=$month_finish_cm.',';
			$month_finish_cm_per=$month_finish_cm/$month_finish_value*100;
			
			$all_mon_finish_value_arr[$compid]+=$month_finish_value;
			$all_mon_finish_qnty_arr[$compid]+=$month_finish_qnty;
			$all_mon_finish_cm_arr[$compid]+=$month_finish_cm;
			$all_mon_finish_cm_per_arr[$compid]+=$month_finish_cm_per;
			
			
			$mon_finish_value_arr[$monthDateKey]+=$month_finish_value;
			$mon_finish_qnty_arr[$monthDateKey]+=$month_finish_qnty;
			$mon_finish_cm_arr[$monthDateKey]+=$month_finish_cm;
			$mon_finish_cm_per_arr[$monthDateKey]+=$month_finish_cm_per;
			
			echo "<td width='60' align='right'>".removeNullZero(number_format($month_finish_value))."</td>
				  <td width='60' align='right'>".removeNullZero(number_format($month_finish_cm))."</td>
				  <td width='30' align='right'>".removeNullZero(number_format($month_finish_cm/$month_finish_value*100))."</td>";
			}
			?>
        <td align="right"><? echo removeNullZero(number_format($mon_finish_value_arr[$monthDateKey]));?></td>
        <td align="right"><? echo removeNullZero(number_format($mon_finish_cm_arr[$monthDateKey]));?></td>
        <td align="right"><? echo removeNullZero(number_format($mon_finish_cm_arr[$monthDateKey]/$mon_finish_value_arr[$monthDateKey]*100));?></td>
    	
    </tr>
    	
    <?php }

		
	 ?>
    
    
    <tr bgcolor="#FFD8B0" style="font-size:12px">
    	<td><b>Grand Total</b></td>
        
        <? 
		foreach($company_short_library as $compid=>$company_name){
echo "<td width='60' align='right'><b>".removeNullZero(number_format($com_finish_value_arr[$compid]+$cum_up_to_current_arr['finish_val'][$compid]+$all_mon_finish_value_arr[$compid]))."</b></td>
	  <td width='60' align='right'><b>".removeNullZero(number_format($com_finish_cm_arr[$compid]+$cum_up_to_current_arr['finish_cm'][$compid]+$all_mon_finish_cm_arr[$compid]))."</b></td>
	  <td width='30' align='right'><b>".removeNullZero(number_format(($com_finish_cm_arr[$compid]+$cum_up_to_current_arr['finish_cm'][$compid]+$all_mon_finish_cm_arr[$compid])/($com_finish_value_arr[$compid]+$cum_up_to_current_arr['finish_val'][$compid]+$all_mon_finish_value_arr[$compid])*100))."</b></td>";
			}
		?>
       <td align="right"><b><? echo removeNullZero(number_format(array_sum($com_finish_value_arr)+array_sum($cum_up_to_current_arr['finish_val'])+array_sum($all_mon_finish_value_arr)));?></b></td>
       <td align="right"><b><? echo removeNullZero(number_format(array_sum($com_finish_cm_arr)+array_sum($cum_up_to_current_arr['finish_cm'])+array_sum($all_mon_finish_cm_arr)));?></b></td>
       <td align="right"><b><? echo removeNullZero(number_format((array_sum($com_finish_cm_arr)+array_sum($cum_up_to_current_arr['finish_cm'])+array_sum($all_mon_finish_cm_arr))/(array_sum($com_finish_value_arr)+array_sum($cum_up_to_current_arr['finish_val'])+array_sum($all_mon_finish_value_arr))*100));?></b></td>
    	
    </tr>
    
    
</table>

<?
	
	//$to="mis.ho@asrotex.com";
/*	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=3 and b.mail_user_setup_id=c.id and a.company_id=$compid";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
	$subject="Task Starting Reminder as per TNA Schedule.";
*/	 
	/*$message="";
	$message=ob_get_contents();
	 ob_clean();
	$header=mail_header();
	if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
	echo $message;*/
	$header=mailHeader();
	$subject="Finish Gmts And Ex-Factory Value & CM Summary";
	$message=ob_get_contents();
	
	/*$to='mis.ho@asrotex.com,
	     mis.zisan@asrotex.com,
		 mis.samad@asrotex.com,
		 ashraful.islam@asrotex.com,
		 aziz.ahmed@asrotex.com,
		 ahsanzaman@asrotex.com';*/
		 
	$to='mis.ho@asrotex.com';
	ob_clean();
	//echo $message;
	echo sendMailMailer( $to, $subject, $message, $from_mail );
		
//-------------------------------------------------------------------------------end;

?>

 
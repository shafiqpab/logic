
<?php

	date_default_timezone_set("Asia/Dhaka");
	
	require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
	require_once('setting/mail_setting.php');
	
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
		
		$sql= "select a.ex_factory_date,b.id as po_id, (b.unit_price/c.total_set_qnty) as unit_price, c.company_name,a.ex_factory_qnty as ex_factory_qnty from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c,pro_ex_factory_delivery_mst d  
		where a.po_break_down_id=b.id and b.job_no_mst=c.job_no   $str_cond  and  a.entry_form!=85  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.delivery_mst_id= d.id order by a.ex_factory_date ASC ";//c.job_no
		
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
		}
	}
	
	//echo $previous_saturday;die;
	//$tot_day = datediff( 'd', $this_month_start_date,$current_date);
	
	
	$tot_day = datediff( 'd', $previous_saturday,$current_date);
	
	 //echo $tot_day;die;
	
    $company_count=count($company_short_library);
	$tbl_width=150+($company_count*145)+140;
	ob_start();
	$flag=0;
?>
<b style="font-size:26px;">Asrotex Group</b><br/>
<b style="font-size:18px; color:#FF0080">Daily Ex Factory Report as on  <? echo date('dS F-Y', time());?></b>
<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="1" border="1" rules="all">
    <thead>
    <tr style="font-size:12px" bgcolor="#EBEBEB">
        <th rowspan="2" width="150">Details <br /><small style="font-size:10px; color:#FF0000;">[Week Start on Saturday]</small></th>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<th colspan='2'>$company_name</th>";
			}
		?>
        <th colspan="2">BU Total</th>	
    </tr>    
    <tr style="font-size:12px" bgcolor="#EBEBEB">    
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<th width='70'>Qty</th><th width='70'>Value</th>";
			}
		?>
        <th width="70">Qty</th>
        <th width="70">Value</th>
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
			$ex_factory_qnty=$result_data_arr[$compid][$dateKey]['ex_factory_qnty'];
			$ex_factory_value=$result_data_arr[$compid][$dateKey]['ex_factory_value'];
			
			$com_ex_factory_qnty_arr[$compid]+=$ex_factory_qnty;
			$com_ex_factory_value_arr[$compid]+=$ex_factory_value;
			
			$day_ex_factory_qnty_arr[$dateKey]+=$ex_factory_qnty;
			$day_ex_factory_value_arr[$dateKey]+=$ex_factory_value;
			
			echo "<td width='70' align='right'>".number_format($ex_factory_qnty)."</td><td width='70' align='right'>".number_format($ex_factory_value)."</td>";
			}
		?>
    	<td align="right"><? echo number_format($day_ex_factory_qnty_arr[$dateKey]);?></td>
    	<td align="right"><? echo number_format($day_ex_factory_value_arr[$dateKey]);?></td>
    </tr>
    <?php } ?>
    
    <tr bgcolor="#FFFFAA" style="font-size:12px">
    	<td><b>Current Week Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<td width='70' align='right'><b>".number_format($com_ex_factory_qnty_arr[$compid])."</b></td><td width='70' align='right'><b>".number_format($com_ex_factory_value_arr[$compid])."</b></td>";
			}
		?>
    	<td align="right"><b><? echo number_format(array_sum($com_ex_factory_qnty_arr));?></b></td>
    	<td align="right"><b><? echo number_format(array_sum($com_ex_factory_value_arr));?></b></td>
    </tr>
    
    
    <tr bgcolor="#5BFFAD" style="font-size:12px">
    	<td><b>Previous Week Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<td width='70' align='right'><b>".number_format($cum_up_to_current_arr['qty'][$compid])."</b></td><td width='70' align='right'><b>".number_format($cum_up_to_current_arr['val'][$compid])."</b></td>";
			}
		?>
    	<td align="right"><b><? echo number_format(array_sum($cum_up_to_current_arr['qty']));?></b></td>
    	<td align="right"><b><? echo number_format(array_sum($cum_up_to_current_arr['val']));?></b></td>
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
			
			$all_mon_ex_factory_qnty_arr[$compid]+=$month_ex_factory_qnty;
			$all_mon_ex_factory_value_arr[$compid]+=$month_ex_factory_value;
			
			$mon_ex_factory_qnty_arr[$monthDateKey]+=$month_ex_factory_qnty;
			$mon_ex_factory_value_arr[$monthDateKey]+=$month_ex_factory_value;
			
			echo "<td width='70' align='right'>".number_format($month_ex_factory_qnty)."</td><td width='70' align='right'>".number_format($month_ex_factory_value)."</td>";
			}
		?>
    	<td align="right"><? echo number_format($mon_ex_factory_qnty_arr[$monthDateKey]);?></td>
    	<td align="right"><? echo number_format($mon_ex_factory_value_arr[$monthDateKey]);?></td>
    </tr>
    <?php } ?>
    
    
    <tr bgcolor="#FFD8B0" style="font-size:12px">
    	<td><b>Grand Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name)
		{
echo "<td width='70' align='right'><b>".number_format($com_ex_factory_qnty_arr[$compid]+$cum_up_to_current_arr['qty'][$compid]+$all_mon_ex_factory_qnty_arr[$compid])."</b></td>
	  <td width='70' align='right'><b>".number_format($com_ex_factory_value_arr[$compid]+$cum_up_to_current_arr['val'][$compid]+$all_mon_ex_factory_value_arr[$compid])."</b></td>";
		}
		?>
    <td align="right"><b><? echo number_format(array_sum($com_ex_factory_qnty_arr)+array_sum($cum_up_to_current_arr['qty'])+array_sum($all_mon_ex_factory_qnty_arr));?></b></td>
    <td align="right"><b><? echo number_format(array_sum($com_ex_factory_value_arr)+array_sum($cum_up_to_current_arr['val'])+array_sum($all_mon_ex_factory_value_arr));?></b></td>
        
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
	
		//$str_cond="and a.production_date between '$previous_saturday' and  '$current_date' ";
		$str_cond="and a.production_date between '$prevMonthEffectiveDate' and  '$current_date' ";
		
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
	
	$sql= "SELECT a.production_date, a.po_break_down_id as po_breakdown_id,a.company_id,(b.unit_price/c.total_set_qnty) as unit_price, 
CASE WHEN production_type =8 THEN production_quantity END AS finish_qnty, 
CASE WHEN production_type =8 and production_source=1 THEN production_quantity END AS finish_qnty_inhouse, 
CASE WHEN production_type =8 and production_source=3 THEN production_quantity END AS finish_qnty_outbound, 
CASE WHEN production_type =8 THEN carton_qty END AS carton_qty 
from pro_garments_production_mst a, wo_po_break_down b ,wo_po_details_master c
where a.po_break_down_id=b.id $str_cond and a.is_deleted=0
and b.job_no_mst=c.job_no and a.status_active=1 and b.status_active in(1,3) 
order by production_date asc";//c.job_no
	//echo $sql;die;
	
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			
			$dateKey=date("Y-m-d",strtotime($row[csf("production_date")]));
			$monthDateKey=date("M-Y",strtotime($row[csf("production_date")]));
			
			$result_data_arr[$row[csf("company_id")]][$dateKey]['finish_qnty'] +=$row[csf("finish_qnty")];
			$result_data_arr[$row[csf("company_id")]][$dateKey]['finish_value'] +=($row[csf("finish_qnty")]*$row[csf("unit_price")]);
			$month_data_arr[$row[csf("company_id")]][$monthDateKey]['finish_qty'] +=$row[csf("finish_qnty")];
			$month_data_arr[$row[csf("company_id")]][$monthDateKey]['finish_val'] +=($row[csf("finish_qnty")]*$row[csf("unit_price")]);
		
		}
		//var_dump($result_data_arr);
		
	//echo $previous_saturday;	
	//Cum. up to current date...........................................................
	$cum_up_to_current_arr=array();
	$today = date('d',strtotime($previous_saturday));
	foreach($company_short_library as $compid=>$company_name){
		for($i=1;$i<$today;$i++){ 
			$dateKey = date('Y-m-d', strtotime($previous_saturday . " -$i day"));
			$cum_up_to_current_arr['finish_qty'][$compid]+=$result_data_arr[$compid][$dateKey]['finish_qnty'];
			$cum_up_to_current_arr['finish_val'][$compid]+=$result_data_arr[$compid][$dateKey]['finish_value'];
		}
	}
	
	//echo $previous_saturday;die;
	//$tot_day = datediff( 'd', $this_month_start_date,$current_date);
	
	
	$tot_day = datediff( 'd', $previous_saturday,$current_date);
	
	 //echo $tot_day;die;
	
    $company_count=count($company_short_library);
	$tbl_width=150+($company_count*145)+140;
	//ob_start();
	//$flag=0;
?>
<br/>
<!--<b style="font-size:26px;">Asrotex Group</b><br/>-->
<b style="font-size:18px; color:#8000FF">Daily Finish Gmts Production as on  <? echo date('dS F-Y', time());?></b>
<table cellspacing="0" width="<? echo $tbl_width;?>" cellpadding="1" border="1" rules="all">
    <thead>
    <tr style="font-size:12px" bgcolor="#EBEBEB">
        <th rowspan="2" width="150" style="font-size:12px">Details <br /><small style="font-size:10px; color:#FF0000;">[Week Start on Saturday]</small></th>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<th colspan='2'>$company_name</th>";
			}
		?>
        <th colspan="2">BU Total</th>	
    </tr>    
    <tr style="font-size:12px" bgcolor="#EBEBEB">    
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<th width='70'>Qty</th><th width='70'>Value</th>";
			}
		?>
        <th width="70">Qty</th>
        <th width="70">Value</th>
    </tr>
    </thead>
    
	<?php 
	
	$com_finish_qnty_arr=array();
	$com_finish_value_arr=array();
	
	for($i=0;$i<$tot_day;$i++){ 
	
	$dsDate = date('dS F', strtotime($previous_saturday . " +$i day"));
	$dateKey = date('Y-m-d', strtotime($previous_saturday . " +$i day"));
	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
	
	
	?>
    <tr bgcolor="<? echo $bgcolor;?>" style="font-size:12px">
    	<td align="left"><? echo $dsDate;?></td>
        <? 
		foreach($company_short_library as $compid=>$company_name){
			$gmts_finish_qnty=$result_data_arr[$compid][$dateKey]['finish_qnty'];
			$gmts_finish_value=$result_data_arr[$compid][$dateKey]['finish_value'];
			
			$com_finish_qnty_arr[$compid]+=$gmts_finish_qnty;
			$com_finish_value_arr[$compid]+=$gmts_finish_value;
			
			$day_finish_qnty_arr[$dateKey]+=$gmts_finish_qnty;
			$day_finish_value_arr[$dateKey]+=$gmts_finish_value;
			
			echo "<td width='70' align='right'>".number_format($gmts_finish_qnty)."</td><td width='70' align='right'>".number_format($gmts_finish_value)."</td>";
			}
		?>
    	<td align="right"><? echo number_format($day_finish_qnty_arr[$dateKey]);?></td>
    	<td align="right"><? echo number_format($day_finish_value_arr[$dateKey]);?></td>
    </tr>
    <?php } ?>
    
    <tr bgcolor="#FFFFAA" style="font-size:12px">
    	<td><b>Current Week Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<td width='70' align='right'><b>".number_format($com_finish_qnty_arr[$compid])."</b></td>
			      <td width='70' align='right'><b>".number_format($com_finish_value_arr[$compid])."</b></td>";
			}
		?>
    	<td align="right"><b><? echo number_format(array_sum($com_finish_qnty_arr));?></b></td>
    	<td align="right"><b><? echo number_format(array_sum($com_finish_value_arr));?></b></td>
    </tr>
    
    
    <tr bgcolor="#5BFFAD" style="font-size:12px">
    	<td><b>Previous Week Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
			echo "<td width='70' align='right'><b>".number_format($cum_up_to_current_arr['finish_qty'][$compid])."</b></td>
				  <td width='70' align='right'><b>".number_format($cum_up_to_current_arr['finish_val'][$compid])."</b></td>";
			}
		?>
    	<td align="right"><b><? echo number_format(array_sum($cum_up_to_current_arr['finish_qty']));?></b></td>
    	<td align="right"><b><? echo number_format(array_sum($cum_up_to_current_arr['finish_val']));?></b></td>
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
			$month_finish_qnty=$month_data_arr[$compid][$monthDateKey]['finish_qty'];
			$month_finish_value=$month_data_arr[$compid][$monthDateKey]['finish_val'];
			
			$all_mon_finish_qnty_arr[$compid]+=$month_finish_qnty;
			$all_mon_finish_value_arr[$compid]+=$month_finish_value;
			
			$mon_finish_qnty_arr[$monthDateKey]+=$month_finish_qnty;
			$mon_finish_value_arr[$monthDateKey]+=$month_finish_value;
			
			echo "<td width='70' align='right'>".number_format($month_finish_qnty)."</td>
			      <td width='70' align='right'>".number_format($month_finish_value)."</td>";
			}
		?>
    	<td align="right"><? echo number_format($mon_finish_qnty_arr[$monthDateKey]);?></td>
    	<td align="right"><? echo number_format($mon_finish_value_arr[$monthDateKey]);?></td>
    </tr>
    <?php } ?>
    
    
    <tr bgcolor="#FFD8B0" style="font-size:12px">
    	<td><b>Grand Total</b></td>
        <? foreach($company_short_library as $compid=>$company_name){
	echo "<td width='70' align='right'><b>".number_format($com_finish_qnty_arr[$compid]+$cum_up_to_current_arr['finish_qty'][$compid]+$all_mon_finish_qnty_arr[$compid])."</b></td>
	      <td width='70' align='right'><b>".number_format($com_finish_value_arr[$compid]+$cum_up_to_current_arr['finish_val'][$compid]+$all_mon_finish_value_arr[$compid])."</b></td>";
			}
		?>
    	<td align="right"><b><? echo number_format(array_sum($com_finish_qnty_arr)+array_sum($cum_up_to_current_arr['finish_qty'])+array_sum($all_mon_finish_qnty_arr));?></b></td>
    	<td align="right"><b><? echo number_format(array_sum($com_finish_value_arr)+array_sum($cum_up_to_current_arr['finish_val'])+array_sum($all_mon_finish_value_arr));?></b></td>
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
	$subject="Gmts Finish & Ex-Factory Summary";
	$message=ob_get_contents();
	
	$to='mis.ho@asrotex.com,
	     mis.zisan@asrotex.com';
		
		 
	//$to='mis.ho@asrotex.com';
	ob_clean();
	//echo $message;
	echo sendMailMailer( $to, $subject, $message, $from_mail );
		
//-------------------------------------------------------------------------------end;

?>

 
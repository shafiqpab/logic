<?php
	date_default_timezone_set("Asia/Dhaka");
	//require_once('../mailer/class.phpmailer.php');
	require_once('../includes/common.php');
	require_once('setting/mail_setting.php');
	require_once('cm_value.php');

	
	//$company_library=return_library_array( "select id, company_short_name from lib_company where  status_active=1 and is_deleted=0 and core_business=1", "id", "company_short_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	
	$com_sql="select a.group_name,b.id,b.company_short_name from lib_group a,lib_company b where  a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and b.core_business=1 and a.id=b.group_id";
	$com_sql_result=sql_select($com_sql);
	foreach($com_sql_result as $row)
	{
		$company_library[$row[csf('id')]]=$row[csf('company_short_name')];
		$company_id_arr[$row[csf('id')]]=$row[csf('id')];
		$group=$row[csf('group_name')];
	}
	
	
	
	for($i=0; $i<=3; $i++ )
	{
		$next_month=month_add(date("Y-m-d",time()),$i);
		$month_arr[]=date("M-Y",strtotime($next_month));
	}

	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, date("m",strtotime('01-'.end($month_arr))), date("Y",strtotime('01-'.end($month_arr))));	
	
	$start_date='01-'.$month_arr[0];
	$end_date=$daysinmonth.'-'.end($month_arr);



	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime($start_date));
		$next_date = date("Y-m-d H:i:s",strtotime($end_date));
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($start_date)),'','',1);
		$next_date = change_date_format(date("Y-m-d H:i:s",strtotime($end_date)),'','',1);
	}



//capacity cal...................................................................................................
	$capacity_sql="select a.comapny_id,b.date_calc,b.capacity_min ,b.capacity_pcs from lib_capacity_calc_mst a, lib_capacity_calc_dtls  b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.date_calc between '$current_date' and '$next_date' and a.comapny_id in(".implode(',',$company_id_arr).")";
	$capacity_sql_result=sql_select($capacity_sql);
	foreach($capacity_sql_result as $row)
	{
		$my=date("M-Y",strtotime($row[csf('date_calc')]));
		$capacity_data_arr[$row[csf('comapny_id')]][$my]+=($row[csf('capacity_min')]/60);
		$company_capacity_data_arr[$row[csf('comapny_id')]]+=($row[csf('capacity_min')]/60);
		$month_capacity_data_arr[$my]+=($row[csf('capacity_min')]/60);
		
	}



//Order...................................................................................................
	$capacity_sql="select a.company_name,a.job_no,a.total_set_qnty,a.set_smv,b.id,b.po_number,b.is_confirmed,b.pub_shipment_date,b.po_quantity,b.po_total_price  from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and b.status_active=1 and b.is_deleted=0   and b.pub_shipment_date between '$current_date' and '$next_date' and a.company_name in(".implode(',',$company_id_arr).")";
	$capacity_sql_result=sql_select($capacity_sql);
	foreach($capacity_sql_result as $row)
	{
		$my=date("M-Y",strtotime($row[csf('pub_shipment_date')]));
		$order_data_arr[qty][$row[csf('company_name')]][$my][$row[csf('is_confirmed')]]+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		$order_data_arr[val][$row[csf('company_name')]][$my][$row[csf('is_confirmed')]]+=($row[csf('po_total_price')]);
		$order_data_arr[sha][$row[csf('company_name')]][$my][$row[csf('is_confirmed')]]+=($row[csf('po_quantity')]*$row[csf('set_smv')])/60;
		$job_data_arr[$row[csf('company_name')]][$my][$row[csf('job_no')]]=$row[csf('job_no')];

		//company
		$company_order_data_arr[qty][$row[csf('is_confirmed')]][$row[csf('company_name')]]+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		$company_order_data_arr[val][$row[csf('is_confirmed')]][$row[csf('company_name')]]+=($row[csf('po_total_price')]);
		$company_order_data_arr[sha][$row[csf('is_confirmed')]][$row[csf('company_name')]]+=($row[csf('po_quantity')]*$row[csf('set_smv')])/60;
		$company_job_data_arr[$row[csf('company_name')]][$row[csf('job_no')]]=$row[csf('job_no')];
		
		//Month
		$month_order_data_arr[qty][$row[csf('is_confirmed')]][$my]+=($row[csf('po_quantity')]*$row[csf('total_set_qnty')]);
		$month_order_data_arr[val][$row[csf('is_confirmed')]][$my]+=($row[csf('po_total_price')]);
		$month_order_data_arr[sha][$row[csf('is_confirmed')]][$my]+=($row[csf('po_quantity')]*$row[csf('set_smv')])/60;
		$month_job_data_arr[$my][$row[csf('job_no')]]=$row[csf('job_no')];
		
		
		$job_arr[$row[csf('job_no')]]=$row[csf('job_no')];
		$po_arr[$row[csf('id')]]=$row[csf('id')];
		$po_number_arr[$row[csf('id')]]=$row[csf('po_number')];
		$month_po_status_arr[$row[csf('company_name')]][$my][$row[csf('id')]]=$row[csf('is_confirmed')];
	}
 
	$order_cm_val_arr=cm_value($company_id_arr,$po_arr);
	$booked_CM_arr=array();$month_booked_CM_arr=array();
	foreach($month_po_status_arr as $company_id=>$my_po_status_arr){
		foreach($my_po_status_arr as $my=>$po_status_arr){
			foreach($po_status_arr as $po_id=>$po_status){
				$booked_CM_arr[$company_id][$po_status][$my]+=$order_cm_val_arr[$po_id];
				$month_booked_CM_arr[$po_status][$my]+=$order_cm_val_arr[$po_id];
				
				//$po_wise_cm[$my][$po_id.$po_number_arr[$po_id]]+=$order_cm_val_arr[$po_id];
			}
		}
	}
 
	//var_dump($po_wise_cm['Aug-2019']);die;
	
	//print_r($booked_CM_arr);

	//pre cost...................................................................................................
	$pre_cost_sql="select job_no from wo_pre_cost_mst where job_no in('".implode("','",$job_arr)."')";
	$pre_cost_sql_result=sql_select($pre_cost_sql);
	foreach($pre_cost_sql_result as $row)
	{
		$pre_cost_job_arr[$row[csf('job_no')]]=1;
	}


	foreach($job_data_arr as $company_name=>$month_data_arr){
		foreach($month_data_arr as $my_key=>$job_no_arr){
			foreach($job_no_arr as $job_no){
				if($pre_cost_job_arr[$job_no]==1){
					$pre_cost_job_data_arr[$company_name][$my_key][$job_no]=$job_no;
					$company_pre_cost_job_data_arr[$company_name][$job_no]=$job_no;
					$month_pre_cost_job_data_arr[$my_key][$job_no]=$job_no;
				}
			}
		}
	}
	







//Fabric booking Auto mail..............................................................................
//$company_library = array(1=>$company_library[1]);
	

ob_start();
?>

<table rules="all" border="1">
   <tbody>
   <tr>
       <td colspan="8" align="center">
           <h2 style="padding:0; margin:0;"><? echo $group;?></h2>
           <strong>Monthly Capacity Vs Booked</strong>
       </td>
   </tr>
	<?   
    foreach($company_library as $company_id=>$company_name)
    {
		$company_id_arr[$company_id]=$company_id;
    ?>    
    <tr bgcolor="#999999">
        <th width="60">B/U</th>
        <th>Details</th>
        <? foreach($month_arr as $my){
        echo "<th width='100'>$my</th>";
        } ?>
        <th width="100">Month TTL</th>
        <th width="60">Ave U/Price</th>
    </tr>
    <tr>
        <td rowspan="18" align="center"><? echo $company_name;?></td>
        <td bgcolor="#FFFF00">Capacity SAH</td>
        <? foreach($month_arr as $my){
        echo "<td bgcolor='#FFFF00' align='right'>".fn_number_format($capacity_data_arr[$company_id][$my])."</td>";
        } ?>
        <td bgcolor="#FFFF00" align="right"><? echo fn_number_format($company_capacity_data_arr[$company_id],0);?></td>
        <td rowspan="18" align="center"><? echo fn_number_format((round($company_order_data_arr[val][2][$company_id]+$company_order_data_arr[val][1][$company_id])/round($company_order_data_arr[qty][2][$company_id]+$company_order_data_arr[qty][1][$company_id])),3);?></td>
    </tr>
	<tr>
        <td>Booked Qty (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($order_data_arr[qty][$company_id][$my][2])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[qty][2][$company_id]);?></td>
    </tr>
	<tr>
        <td>Booked Qty (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($order_data_arr[qty][$company_id][$my][1])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[qty][1][$company_id]);?></td>
    </tr>
	<tr bgcolor="#C5D9F1">
        <td>Total Booked Qty</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(array_sum($order_data_arr[qty][$company_id][$my]))."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[qty][2][$company_id]+$company_order_data_arr[qty][1][$company_id]);?></td>
    </tr>
	<tr>
        <td>Booked Value (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($order_data_arr[val][$company_id][$my][2])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[val][2][$company_id]);?></td>
    </tr>
	<tr>
        <td>Booked Value (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($order_data_arr[val][$company_id][$my][1])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[val][1][$company_id]);?></td>
    </tr>
	<tr bgcolor="#CCC0DA">
        <td>Total Booked Value</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(array_sum($order_data_arr[val][$company_id][$my]))."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[val][2][$company_id]+$company_order_data_arr[val][1][$company_id]);?></td>
    </tr>
	<tr>
        <td>Booked CM (Projected)</td>
        <? foreach($month_arr as $my){
        	echo "<td align='right'>".fn_number_format($booked_CM_arr[$company_id][2][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($booked_CM_arr[$company_id][2]));?></td>
    </tr>
	<tr>
        <td>Booked CM (Confirmed)</td>
        <? foreach($month_arr as $my){
        	echo "<td align='right'>".fn_number_format($booked_CM_arr[$company_id][1][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($booked_CM_arr[$company_id][1]));?></td>
    </tr>
	<tr bgcolor="#C2D69A">
        <td>Total Booked CM</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(($booked_CM_arr[$company_id][1][$my]+$booked_CM_arr[$company_id][2][$my]))."</td>";
        } ?>
        <td align="right"><? echo fn_number_format((array_sum($booked_CM_arr[$company_id][1])+array_sum($booked_CM_arr[$company_id][2])));?></td>
    </tr>
	<tr>
        <td>Booked SAH (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($order_data_arr[sha][$company_id][$my][2])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[sha][2][$company_id]);?></td>
    </tr>
	<tr>
        <td>Booked SAH (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($order_data_arr[sha][$company_id][$my][1])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[sha][1][$company_id]);?></td>
    </tr>
	<tr bgcolor="#FFC000">
        <td>Total Booked SAH</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(array_sum($order_data_arr[sha][$company_id][$my]))."</td>";
        } ?>
        <td align="right"><? echo fn_number_format($company_order_data_arr[sha][2][$company_id]+$company_order_data_arr[sha][1][$company_id]);?></td>
    </tr>
	<tr>
        <td>Booked % (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(($order_data_arr[sha][$company_id][$my][2]/$capacity_data_arr[$company_id][$my])*100,2)."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(($company_order_data_arr[sha][2][$company_id]/$company_capacity_data_arr[$company_id])*100,2);?></td>
    </tr>
	<tr>
        <td>Booked % (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(($order_data_arr[sha][$company_id][$my][1]/$capacity_data_arr[$company_id][$my])*100,2)."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(($company_order_data_arr[sha][1][$company_id]/$company_capacity_data_arr[$company_id])*100,2);?></td>
    </tr>
	<tr bgcolor="#FAC090">
        <td>Total Booked %</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format((($order_data_arr[sha][$company_id][$my][2]+$order_data_arr[sha][$company_id][$my][1])/$capacity_data_arr[$company_id][$my])*100,2)."</td>";
        } ?>
        <td align="right"><? echo fn_number_format((($company_order_data_arr[sha][2][$company_id]+$company_order_data_arr[sha][1][$company_id])/$company_capacity_data_arr[$company_id])*100,2);?></td>
    </tr>
	<tr bgcolor="#FDE9D9">
        <td>TTL Booked Style/Job</td>
        <? 
		$totJob=0;
		foreach($month_arr as $my){
		$totJob+=count($job_data_arr[$company_id][$my]);
        echo "<td align='right'>".fn_number_format(count($job_data_arr[$company_id][$my]))."</td>";
        } ?>
        <td align="right">(
		<? 
			echo count($company_job_data_arr[$company_id]).'+'.($totJob-count($company_job_data_arr[$company_id]));
			echo ')='.$totJob;
		?>
        </td>
    </tr>
	<tr bgcolor="#FDE9D9">
        <td>TTL Budgeted Style/Job</td>
        <? 
		$totPreCostJob=0;
		foreach($month_arr as $my){
       	$totPreCostJob+=count($pre_cost_job_data_arr[$company_id][$my]);
	    echo "<td align='right'>".fn_number_format(count($pre_cost_job_data_arr[$company_id][$my]))."</td>";
        } ?>
        <td align="right">(
		<? echo count($company_pre_cost_job_data_arr[$company_id]).'+'.($totPreCostJob-count($company_pre_cost_job_data_arr[$company_id]));
		
		echo ')='.$totPreCostJob;
		?>
        </td>
    </tr>
  <? } ?>   
     
  
  
  
  
  
  
  
    <tr bgcolor="#999999">
        <th width="60">All Unit</th>
        <th>Details</th>
        <? foreach($month_arr as $my){
        echo "<th width='100'>$my</th>";
        } ?>
        <th width="100">Month TTL</th>
        <th width="60">Ave U/Price</th>
    </tr>
    <tr>
        <td rowspan="18" align="center">Grand Total</td>
        <td bgcolor="#FFFF00">Capacity SAH</td>
        <? foreach($month_arr as $my){
        echo "<td bgcolor='#FFFF00' align='right'>".fn_number_format($month_capacity_data_arr[$my])."</td>";
        } ?>
        <td bgcolor="#FFFF00" align="right"><? echo fn_number_format(array_sum($month_capacity_data_arr));?></td>
        <td rowspan="18" align="center"><? 
		echo fn_number_format((array_sum($company_order_data_arr[val][2])+array_sum($company_order_data_arr[val][1]))
		/(array_sum($company_order_data_arr[qty][2])+array_sum($company_order_data_arr[qty][1])),3);
		?></td>
    </tr>
	<tr>
        <td>Booked Qty (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[qty][2][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[qty][2]));?></td>
    </tr>
	<tr>
        <td>Booked Qty (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[qty][1][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[qty][1]));?></td>
    </tr>
	<tr bgcolor="#C5D9F1">
        <td>Total Booked Qty</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[qty][1][$my]+$month_order_data_arr[qty][2][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[qty][1])+array_sum($month_order_data_arr[qty][2]));?></td>
    </tr>
	<tr>
        <td>Booked Value (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[val][2][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[val][2]));?></td>
    </tr>
	<tr>
        <td>Booked Value (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[val][1][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[val][1]));?></td>
    </tr>
	<tr bgcolor="#CCC0DA">
        <td>Total Booked Value</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[val][1][$my]+$month_order_data_arr[val][2][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[val][1])+array_sum($month_order_data_arr[val][2]));?></td>
    </tr>
	<tr>
        <td>Booked CM (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_booked_CM_arr[2][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_booked_CM_arr[2]));?></td>
    </tr>
	<tr>
        <td>Booked CM (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_booked_CM_arr[1][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_booked_CM_arr[1]));?></td>
    </tr>
	<tr bgcolor="#C2D69A">
        <td>Total Booked CM</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(($month_booked_CM_arr[1][$my]+$month_booked_CM_arr[2][$my]))."</td>";
        } ?>
        <td align="right"><? echo fn_number_format((array_sum($month_booked_CM_arr[1])+array_sum($month_booked_CM_arr[2])));?></td>
    </tr>
	<tr>
        <td>Booked SAH (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[sha][2][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[sha][2]));?></td>
    </tr>
	<tr>
        <td>Booked SAH (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[sha][1][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[sha][1]));?></td>
    </tr>
	<tr bgcolor="#FFC000">
        <td>Total Booked SAH</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format($month_order_data_arr[sha][1][$my]+$month_order_data_arr[sha][2][$my])."</td>";
        } ?>
        <td align="right"><? echo fn_number_format(array_sum($month_order_data_arr[sha][1])+array_sum($month_order_data_arr[sha][2]));?></td>
    </tr>
	<tr>
        <td>Booked % (Projected)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(($month_order_data_arr[sha][2][$my]/$month_capacity_data_arr[$my])*100,2)."</td>";
        } ?>
        <td align="right"><? echo fn_number_format((array_sum($month_order_data_arr[sha][2])/array_sum($month_capacity_data_arr))*100,2);?></td>
    </tr>
	<tr>
        <td>Booked % (Confirmed)</td>
        <? foreach($month_arr as $my){
        echo "<td align='right'>".fn_number_format(($month_order_data_arr[sha][1][$my]/$month_capacity_data_arr[$my])*100,2)."</td>";
        } ?>
        <td align="right"><? echo fn_number_format((array_sum($month_order_data_arr[sha][1])/array_sum($month_capacity_data_arr))*100,2);?></td>
    </tr>
	<tr bgcolor="#FAC090">
        <td>Total Booked %</td>
        <? foreach($month_arr as $my){
		echo "<td align='right'>".fn_number_format((($month_order_data_arr[sha][1][$my]+$month_order_data_arr[sha][2][$my])/$month_capacity_data_arr[$my])*100,2)."</td>";
        } ?>
        <td align="right"><? echo fn_number_format((array_sum($month_order_data_arr[sha][1])+array_sum($month_order_data_arr[sha][2]))/array_sum($month_capacity_data_arr)*100,2);?></td>
    </tr>
	<tr bgcolor="#FDE9D9">
        <td>TTL Booked Style/Job</td>
        <? 
		$totJob=0;
		foreach($month_arr as $my){
		$totJob+=count($month_job_data_arr[$my]);
        echo "<td align='right'>".fn_number_format(count($month_job_data_arr[$my]))."</td>";
        } ?>
        <td align="right">(
		<? 
			echo count($month_job_data_arr[$my]).'+'.($totJob-count($month_job_data_arr[$my]));
			echo ')='.$totJob;
		
		?>
        </td>
    </tr>
	<tr bgcolor="#FDE9D9">
        <td>TTL Budgeted Style/Job</td>
        <? 
		$totPreCostJob=0;
		foreach($month_arr as $my){
       	$totPreCostJob+=count($month_pre_cost_job_data_arr[$my]);
	    echo "<td align='right'>".fn_number_format(count($month_pre_cost_job_data_arr[$my]))."</td>";
        } ?>
        <td align="right">(
		<? 
			echo count($month_pre_cost_job_data_arr[$my]).'+'.($totPreCostJob-count($month_pre_cost_job_data_arr[$my]));
			echo ')='.$totPreCostJob;
		?>
        </td>
    </tr>
  
  
  
  
         
</tbody>

</table>



<?
	$message=ob_get_contents();
	ob_clean();


	$to="";
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=22 and b.mail_user_setup_id=c.id AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.company_id in(".implode(',',$company_id_arr).")";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	$header=mailHeader();
	
	$subject="Monthly Capacity Vs Booked";

	//if($to!=""){echo send_mail_mailer( $to, $subject, $message, $from_mail );}
	if($_REQUEST['isview']==1){
        $mail_item=22;
        if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
        echo $message;
    }
    else{
  
        if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
    }

	
?>






</body>
</html>
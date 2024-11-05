<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create Knit Garments Order Entry
Functionality	:	
JS Functions	:
Created by		:	Monzu 
Creation date 	: 	13-10-2012
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$date=date('Y-m-d');

if($type=="report_generate")
{
	$data=explode("_",$data);
	//print_r($data);
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	if($data[1]==0) $txt_week_from="%%"; else $txt_week_from=$data[1];
	if($data[2]==0) $txt_week_to="%%"; else $txt_week_to=$data[2];
	
	if(trim($data[3])!="") $start_date=$data[3];
	if(trim($data[4])!="") $end_date=$data[4];
	
	$cbo_year_selection=$data[5];
	
	$year_short = substr($cbo_year_selection, -2, 2);
	
	
	if($db_type==0)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-');
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-');
	}
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($end_date,'yyyy-mm-dd','-',1);
	}
	
	$month_array=array();
	
	if ($start_date!="" && $end_date!="")
	{
		$month="";
		$sm = date('m',strtotime($start_date));
		$em = date('m',strtotime($end_date));
		
		$dif_m=$em-$sm;
		
		for($i=0; $i<=$dif_m; $i++)
		{
		 	$month_array[]= $sm+$i; 
		}
	}
	
	$count=count($month_array);
	
	
	
	function week_of_year($year,$week_start_day)
	{
		$week_array=array();
		$week=0;
		for($i=1;$i<=12; $i++)
		{
			$month=str_pad($i, 2, '0', STR_PAD_LEFT);
			$year=$year;
			$first_date_of_year=$year."-01-01";
			$first_day_of_year=date('l', strtotime($first_date_of_year));
			if($i==1)
			{
				if(date('l', strtotime($first_day_of_year))==$week_start_day)
				{
					$week=0;
				}
				else
				{
					$week=1;
				}
			}
			$days_in_month = cal_days_in_month(0, $month, $year) ;
			
			foreach (range(1, $days_in_month) as $day) 
			{
				$test_date = $year."-".$month."-" . str_pad($day, 2, '0', STR_PAD_LEFT);
				global $db_type;
				if($db_type==2)
				{
				$test_date=change_date_format($test_date,'dd-mm-yyyy','-',1);
				}
				
				if(date('l', strtotime($test_date))==$week_start_day)
				{
				  $week++;
				}
				$week_day=date('l', strtotime($test_date));
				$week_array[$test_date]=$week;
				
			}
		}
		return $week_array ;
	}
	
	
	
	$weekarr=week_of_year($cbo_year_selection,"Sunday");

//echo $weekarr.'___'.$ey;die;
//echo "<pre>";
//print_r($weekarr);die;

	$week_for_header=array();
	
	$sql_week_header=sql_select("select week from week_of_year where week between '$txt_week_from' and  '$txt_week_to' group by week order by week");
	foreach ($sql_week_header as $row_week_header)
	{
		$week_for_header[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	
	$week_count=count($week_for_header);
	
	
	$update_history_array=array();

	$sql_update_history=sql_select("Select po_id,order_status,max(previous_po_qty) as previous_po_qty,shipment_date,update_date from wo_po_update_log where shipment_date between '$start_date'and '$end_date' group by po_id,order_status,shipment_date,update_date order by po_id");
	
	foreach ($sql_update_history as $row_update_history)
	{
		$update_week=$weekarr[$row_update_history[csf("update_date")]];
		$ship_month = number_format(date('m',strtotime($row_update_history[csf('shipment_date')])));
		//echo $ship_month."__";
		
		if($db_type==2)
		{
				$update_week=$weekarr[change_date_format($row_update_history[csf("update_date")],'dd-mm-yyyy','-',1)];
				//echo $update_week."__";
		}
		$update_history_array[$update_week][$row_update_history[csf("po_id")]][$ship_month][$row_update_history[csf('update_date')]][previous_po_qty]+=$row_update_history[csf("previous_po_qty")];	
	}
	
	//echo "<pre>";
	//print_r($update_history_array);die;
	

	

ob_start();
?>

    <table cellspacing="0" width="<? echo $week_count*$count*350+100;  ?>px"  border="1" rules="all" class="rpt_table" >
        <thead align="center">
            <tr>
            	<th width="110" align="center">Purticulars</th>
				<?
                foreach($week_for_header as $week_key => $week_value)
                {
                ?>
                    <th width="<? echo $count*350;  ?>px" colspan="<?  echo $count*4;  ?>" align="center">
                    Week-
                    <? 
                    	echo $week_key;
                    ?>
                    </th>
                <?
                }
                ?>
            </tr>
            <tr>
                <th width="110" align="center">Purticulars</th>
					<?
                    foreach($week_for_header as $week_key => $week_value)
                    {
						foreach($month_array as $val)
                		{
                    ?>
                			<th width="350" colspan="4" align="center">
							<?  
								$monthNum = $val;
								$monthName = date("F", mktime(0, 0, 0, $monthNum, 10));
								echo $monthName."'".$year_short;   
							?>
                            </th>
					<?
						}
                    }
                    ?>
            </tr>
            <tr>
                <th width="110" align="center">SL NO</th>
					<?
                    foreach($week_for_header as $week_key => $week_value)
                    {
						foreach($month_array as $val)
                		{
                    ?>
                			<th width="75" align="center">Status</th>
                            <th width="100" align="center">Order No</th>
                            <th width="100" align="center">Qty.</th>
                            <th width="75" align="center">Update Date</th>
					<?
						}
                    }
                    ?>
            </tr>
        </thead>
        <tbody>
        	<?php
        $i=0;
        $total_po_qty=0;
        $total_value=0;
        
		$sql_mst="Select po_no,po_id,order_status,max(previous_po_qty) as previous_po_qty,shipment_date,update_date from wo_po_update_log where shipment_date between '$start_date'and '$end_date' group by po_id,order_status,shipment_date,update_date,po_no order by po_id";				
		$nameArray_mst=sql_select($sql_mst);
		$tot_rows=count($nameArray_mst);
		foreach($nameArray_mst as $row)
		{
            $i++;
            if ($i%2==0)  
                $bgcolor="#E9F3FF";
            else
                $bgcolor="#FFFFFF";
				
				?>
            <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="110" align="center"><? echo $i; ?></td>
					<?
                    foreach($week_for_header as $week_key => $week_value)
                    {
						foreach($month_array as $val)
                		{
                    ?>
                			<td width="75">
								<? 
									echo $order_status[$row[csf("order_status")]];
								?>
                            </td>
                            <td width="100">
                            	<? 
									echo $row[csf("po_no")];
								?>
                            </td>
                            <td width="100" align="right">
                            	<? 
									echo $update_history_array[$week_value][$row[csf("po_id")]][$val][$row[csf('update_date')]][previous_po_qty];
								?>
                            </td>
                            <td width="75" align="center">
                            	<? 
									echo change_date_format($row[csf("update_date")]);
								?>
                            </td>
					<?
						}
                    }
                    ?>
            </tr>
            
            <?	
		}
        ?> 
        </tbody>
        <tfoot>
        </tfoot>
    </table>
            
     </div>
        
<?
	$html = ob_get_contents();
	 
	foreach (glob(""."*.xls") as $filename) 
	{			
	   @unlink($filename);
	}
	$name="weekcapabooking".".xls";	
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
		
}
?>
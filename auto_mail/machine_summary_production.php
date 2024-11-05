<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
require_once('../mailer/class.phpmailer.php');

$comp_lib = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name");

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);
$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
$current_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
 
foreach($comp_lib as $company_id=>$company_name){

$machine_data = sql_select("select COMPANY_ID,ID,MACHINE_GROUP,DIA_WIDTH,MACHINE_NO,PROD_CAPACITY,  GAUGE from lib_machine_name where status_active = 1 AND  is_deleted = 0 AND COMPANY_ID=$company_id");
$machine_arr=array();
foreach ($machine_data as $row) {
	$machine_arr[MACHINE_NO][$row[ID]]=$row[MACHINE_NO];
	$machine_arr[GROUP_DIA_GAUGE][$row[ID]]=$row[MACHINE_GROUP].','.$row[DIA_WIDTH].'/'.$row[GAUGE];
	$machine_arr[PROD_CAPACITY][$row[ID]]=$row[PROD_CAPACITY];
	
	$machine_details[$row[ID]]['no']=$row[MACHINE_NO];

}
	
	$knit_pro_sql="SELECT a.COMPANY_ID,b.MACHINE_NO_ID,b.SHIFT_NAME,b.MACHINE_DIA,b.WIDTH,b.GREY_RECEIVE_QNTY
  FROM inv_receive_master a,PRO_GREY_PROD_ENTRY_DTLS b
 WHERE   a.id=b.mst_id  and  a.status_active = 1 AND a.is_deleted = 0 and  b.status_active = 1 AND b.is_deleted = 0 and a.receive_basis!=4  and a.knitting_source=1  and a.entry_form=2  and a.item_category=13 
       AND a.RECEIVE_DATE between '$current_date' and '$current_date' and a.KNITTING_COMPANY=$company_id ";
	
	
	$knit_pro_sql_result = sql_select($knit_pro_sql);
	$dataArr=array();$shift_wise_machine_pro=array();$total_machine_capacity=0;
	foreach ($knit_pro_sql_result as $row) {
		$dataArr[$row[MACHINE_NO_ID]]=$row;
		$shift_wise_machine_pro[$row[MACHINE_NO_ID]][$row[SHIFT_NAME]]+=$row[GREY_RECEIVE_QNTY];
		$machine_id_arr[$row[MACHINE_NO_ID]]=$row[MACHINE_NO_ID];
	}
	$width=(count($shift_name)*+180)+550;

//..................................................................

$shift_data=sql_select("select start_time, end_time,shift_name from shift_duration_entry where status_active=1");
foreach($shift_data as $row)
{
	$shift_details[$row[csf('shift_name')]]['from_hr_min']=number_format($row[csf('start_time')],2);
	$shift_details[$row[csf('shift_name')]]['to_hr_min']=number_format($row[csf('end_time')],2);
	//for machine shift date
	if(strtotime($row[csf('start_time')]) >= strtotime($row[csf('end_time')]))
	{
		if($db_type==0)
		{
			$machineTo_date=date('Y-m-d',strtotime('+1 day', strtotime($current_date)));
		}
		else
		{
			$machineTo_date=change_date_format(date('Y-m-d',strtotime('+1 day', strtotime($current_date))),'','',1);
		}
	}
}



$machine_data=sql_select("select to_date,machine_entry_tbl_id, from_hour, from_minute, to_hour, to_minute, machine_idle_cause from  pro_cause_of_machine_idle where status_active=1 and machine_entry_tbl_id in(".implode(",",$machine_id_arr).") and from_date between '$current_date' and '$machineTo_date' and to_date between '$current_date' and '$machineTo_date'  ");


		foreach($machine_data as $row)
		{
			if($row[csf('from_hour')]!='')
			{
				$machineID=$row[csf('machine_entry_tbl_id')];
				$fromtime=$row[csf('from_hour')].':'.$row[csf('from_minute')];
				$totime=$row[csf('to_hour')].':'.$row[csf('to_minute')];
				$machine_no = $machine_details[$machineID]['no'];

				foreach ($shift_name as $key=>$shift)
				{
					$shift_from = strtotime($shift_details[$key]['from_hr_min']);
					$shift_to = strtotime($shift_details[$key]['to_hr_min']);
					
					if($key != 3 && strtotime($current_date) == strtotime($row[csf('to_date')]))
					{
						if(strtotime($fromtime)>=$shift_from && strtotime($totime)<=$shift_to)
						{
							$machineCauseArr[$machine_no][$key][$row[csf('machine_idle_cause')]]=$cause_type[$row[csf('machine_idle_cause')]];
						}
					}
					else
					{  
			
						if(strtotime($fromtime)>=$shift_from && strtotime($totime)<=$shift_to && $key==3)
						{
							$machineCauseArr[$machine_no][$key][$row[csf('machine_idle_cause')]]=$cause_type[$row[csf('machine_idle_cause')]];
						}

						if(strtotime($fromtime)>=$shift_from && strtotime($totime)>=$shift_to && $key==3)
						{
							$machineCauseArr[$machine_no][3][$row[csf('machine_idle_cause')]]=$cause_type[$row[csf('machine_idle_cause')]];
						}
					}
				}
			}
		}


	ob_start();
?>

    <table width="<?= $width;?>" align="center">
        <tr>
            <th align="center" colspan="<? echo (count($shift_name)*2)+6;?>">
                <div style="font-size:24px;"><?= $company_name;?></div>
                <div>Machine Summary Below 80 % production</div>
                <div>Date:<?= $current_date;?></div>
            </th>
        </tr>
    </table>
    <table class="rpt_table" width="<?= $width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" align="center" id="summary_tab">
        <thead bgcolor="#CCCCCC">
            <tr>
                <th rowspan="2" width="30">SL</th>
                <th rowspan="2" width="100">M/C No</th>
                <th rowspan="2">Group,Dia/ Width,Gauge</th>
                <th rowspan="2" width="80">Capacity</th>
                <th colspan="<? echo count($shift_name)*2;?>">SHIFT NAME</th>
                <th rowspan="2" width="80">Shift Total (kg)</th>
                <th rowspan="2" width="80" title="(Shift Total/Machine Capacity)*100">Capacity Achieve %</th>
            </tr>
            <tr>
                <?
                foreach($shift_name as $key=>$val)
                {
                 ?>
                    <th width="80"><? echo $val;?></th>
                    <th width='100'>Idle Cause</th>
                 <?
                }
                ?>
            </tr>
        </thead>
      <?
       $i=1;$flag=0;
	  $total_machine_capacity_arr=array();$shift_wise_pro=array();
	  foreach($dataArr as $machine_id=>$row){
		$capacityAchievePercent=(array_sum($shift_wise_machine_pro[$machine_id])/$machine_arr[PROD_CAPACITY][$machine_id])*100;
		if($capacityAchievePercent>=80){continue;}
		$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
		$total_machine_capacity_arr[$machine_id]=$machine_arr[PROD_CAPACITY][$machine_id];
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_sm<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_sm<? echo $i; ?>">
			<td align="center"><? echo $i; ?></td>
			<td><? echo $machine_arr[MACHINE_NO][$machine_id]; ?></td>
			<td><? echo $machine_arr[GROUP_DIA_GAUGE][$machine_id]; ?></td>
			<td align="right"><? echo $machine_arr[PROD_CAPACITY][$machine_id];?></td>
			<?
			
			$totPro=0;
			foreach($shift_name as $key=>$val)
			{
				$shift_wise_pro[$key]+=$shift_wise_machine_pro[$machine_id][$key];
				?>
				<td align="right"><? echo number_format($shift_wise_machine_pro[$machine_id][$key],2); ?></td>
				<td align="right"><p><? echo implode(',',$machineCauseArr[$machine_id][$key]); ?></p></td>
				<?
				
			}
			?>
			<td align="right"><? echo number_format(array_sum($shift_wise_machine_pro[$machine_id]),2);?></td>
			<td align="right"><? echo fn_number_format($capacityAchievePercent,2);?></td>
		</tr>
		<?
	   	$flag=1;
		$i++;
		}
        ?>
        <tfoot bgcolor="#CCCCCC">
            <th></th>
            <th></th>
            <th>Total</th>
            <th align="right"><? echo array_sum($total_machine_capacity_arr);?></th>

            <?
           
            foreach($shift_name as $key=>$val)
            {
                ?>
                <th align="right"><? echo number_format($shift_wise_pro[$key],2); ?></th>
                <th></th>
                <?
            }
            ?>
            <th align="right"><? echo number_format(array_sum($shift_wise_pro),2);?></th>
            <th align="right"><? echo  fn_number_format((array_sum($shift_wise_pro)/array_sum($total_machine_capacity_arr))*100,2);?></th>
        </tfoot>
    </table>


<?
		$message="";
		$message=ob_get_contents();
		ob_clean();
	
		$to="";
		$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=31 and b.mail_user_setup_id=c.id and a.company_id=$company_id AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$sql_result=sql_select($sql);
		foreach($sql_result as $row)
		{
			if ($to=="")  $to=$row[EMAIL_ADDRESS]; else $to=$to.", ".$row[EMAIL_ADDRESS]; 
		}

		$subject="Machine Summary Below 80 % production on ".$current_date;
		$header=mail_header();
		// if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message );}
		// echo $message;
		if($_REQUEST['isview']==1){
			echo $message;
		}
		else{
			if($to!="" && $flag==1){echo send_mail_mailer( $to, $subject, $message );}
		}
	
	}


?>

<!-- if is comment -->
<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("TNA DATA", "", 1, 1,'','','');

$start_date=$_GET['start'];
$end_date=$_GET['end'];
$dateCondition="'$start_date' and '$end_date'";	
	
//TNA Data ..................................................
	$sql = "select G.COMPANY_SHORT_NAME as BU,H.TEAM_LEADER_NAME ,d.BUYER_NAME,b.JOB_NO,E.STYLE_REF_NO as STYLE_NAME,a.PO_NUMBER,PO_RECEIVED_DATE,b.SHIPMENT_DATE,trunc(b.SHIPMENT_DATE-a.PO_RECEIVED_DATE) as PO_LEAD_TIME,c.TASK_SHORT_NAME as TASK_NAME,
b.TASK_FINISH_DATE as PLAN_FINISH_DATE,b.ACTUAL_FINISH_DATE,trunc(B.TASK_FINISH_DATE-nvl(ACTUAL_FINISH_DATE,sysdate)) as Exe_Days,F.COMMENTS
from TNA_PROCESS_MST b,LIB_TNA_TASK c,LIB_BUYER d,WO_PO_DETAILS_MASTER e,LIB_COMPANY g,LIB_MARKETING_TEAM h, WO_PO_BREAK_DOWN a left join TNA_PROGRESS_COMMENTS f on a.id=F.ORDER_ID
where E.JOB_NO=A.JOB_NO_MST and a.ID=b.PO_NUMBER_ID and b.TASK_NUMBER=c.TASK_NAME and d.ID=e.BUYER_NAME
and A.PUB_SHIPMENT_DATE between $dateCondition
and E.COMPANY_NAME=G.ID
and E.TEAM_LEADER=H.ID
and H.ID=4
and a.IS_CONFIRMED=1
and B.TASK_NUMBER in (10,12,31,32,48,50,60,61,70,71,73,80,84,85,86,88,101,110)
and A.STATUS_ACTIVE=1 and A.IS_DELETED=0
order by c.TASK_SEQUENCE_NO,b.JOB_NO,E.COMPANY_NAME";

	$sql_result = sql_select($sql);

	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr bgcolor="#FFFF99">
                     	<th style="font-size:24px" align="center" colspan="13">ASROTEX GROUP</th>
                	 </tr>
                    <tr bgcolor="#66FF99">
                    	<td align="center" colspan="13">TNA Data From <? echo $start_date; ?> To <? echo $end_date;?></td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <th>SL</th>
                        <th>BU</th>
                        <th>Team Leader</th>                        
                        <th>Buyer</th>
                        <th>Job No.</th>
                        <th>Style</th>
                        <th>PO No.</th>
                        <th>PO Rcv Date</th>
                        <th>Pub-Ship Date</th>
                        <th>PO Lead Time</th>
                        <th>Task Name</th>
                        <th>Plan Fin Date</th>
                        <th>Actual Fin Date</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr style="font-size:14px" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="25" align="center"><? echo $i;?></td>
	                        <td width="50"><? echo $row[BU];?></td>
	                        <td width="100" align="left"><? echo $row[TEAM_LEADER_NAME];?></td>	                        
	                        <td width="70" align="left"><?  echo $row[BUYER_NAME];?></td>
                            <td width="60" align="center"><?  echo $row[JOB_NO];?></td>
                            <td width="60" align="left"><?  echo $row[STYLE_NAME];?></td>
                            <td width="80" align="left"><?  echo $row[PO_NUMBER];?></td>
                            <td width="90" align="center"><? echo $row[PO_RECEIVED_DATE];?></td>
                            <td width="90" align="center"><? echo $row[SHIPMENT_DATE];?></td>
                            <td width="50" align="center"><? echo $row[PO_LEAD_TIME];?></td>
                            <td width="90" align="left"><? echo $row[TASK_NAME];?></td>
                            <td width="100" align="center"><? echo $row[PLAN_FINISH_DATE];?></td>
                            <td width="100" align="center"><? echo $row[ACTUAL_FINISH_DATE];?></td>
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>



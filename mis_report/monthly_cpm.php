<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("Monthly CPM", "", 1, 1,'','','');	
	
//CPM ..................................................
	$sql = "select b.COMPANY_SHORT_NAME,A.APPLYING_PERIOD_DATE as FROM_DATE,
a.MONTHLY_CM_EXPENSE as MONTHLY_CM_EXP,a.NO_FACTORY_MACHINE as NO_OF_MC,a.WORKING_HOUR,
(A.NO_FACTORY_MACHINE*A.WORKING_HOUR*26*60) as TOTAL_MIN,A.COST_PER_MINUTE as COST_MIN_TK,

case when
		A.COMPANY_ID=1 and A.APPLYING_PERIOD_DATE >'01-Jun-2019' and A.APPLYING_PERIOD_DATE <'01-Jan-2020'  then
			TO_CHAR(A.COST_PER_MINUTE/79/58*100) when
		A.COMPANY_ID=1 and A.APPLYING_PERIOD_DATE >'01-Dec-2019' then
			TO_CHAR(A.COST_PER_MINUTE/82/55*100) when
		
		A.COMPANY_ID=2 and A.APPLYING_PERIOD_DATE >'01-Jun-2019' and A.APPLYING_PERIOD_DATE <'01-Jan-2020' then
			TO_CHAR(A.COST_PER_MINUTE/79/57*100) when
		A.COMPANY_ID=2 and A.APPLYING_PERIOD_DATE >'01-Dec-2019' then
			TO_CHAR(A.COST_PER_MINUTE/82/59*100) when
		
		A.COMPANY_ID=3 and A.APPLYING_PERIOD_DATE >'01-Jun-2019' and A.APPLYING_PERIOD_DATE <'01-Jan-2020' then
			TO_CHAR(A.COST_PER_MINUTE/79/60*100) when
		A.COMPANY_ID=3 and A.APPLYING_PERIOD_DATE >'01-Dec-2019' then
			TO_CHAR(A.COST_PER_MINUTE/82/60*100) end as COST_MIN_USD
		 
from LIB_STANDARD_CM_ENTRY a, lib_company b where A.COMPANY_ID=b.id and a.status_active=1 and a.is_deleted=0 and a.APPLYING_PERIOD_DATE >'01-Jun-2019'
order by b.COMPANY_NAME,A.APPLYING_PERIOD_DATE";

	$sql_result = sql_select($sql);

	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
               		 <tr bgcolor="#FFFF99">
                     	<th style="font-size:24px" align="center" colspan="9">ASROTEX GROUP</th>
                	 </tr>
                    <tr bgcolor="#66FF99">
                    	<td align="center" colspan="9">BU WISE MONTHLY CPM [2019-2020]</td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <th>SL</th>
                        <th>BU</th>
                        <th>For Month</th>                        
                        <th>Monthly Exp.</th>
                        <th>No.Of MC</th>
                        <th>WH</th>
                        <th>TTL Min.</th>
                        <th>Cost/Min(Tk)</th>
                        <th>Cost/Min($)</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($sql_result as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="25" align="center"><? echo $i;?></td>
	                        <td width="50"><? echo $row[COMPANY_SHORT_NAME];?></td>
	                        <td width="90" align="left"><? echo date('M-y', strtotime($row[FROM_DATE]));?></td>	                        
	                        <td width="100" align="right"><?  echo number_format($row[MONTHLY_CM_EXP],0).'<br>';?></td>
                            <td width="90" align="center"><? echo $row[NO_OF_MC];?></td>
                            <td width="35" align="center"><? echo $row[WORKING_HOUR];?></td>
                            <td width="100" align="right"><? echo number_format($row[TOTAL_MIN]).'<br>';?></td>
                            <td width="100" align="right"><? echo $row[COST_MIN_TK];?></td>
                            <td width="100" align="right"><? echo number_format($row[COST_MIN_USD],3).'<br>';?></td>
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>



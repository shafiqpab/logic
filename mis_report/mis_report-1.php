<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("Monthly CAPACITY", "", 1, 1,'','','');	
	
//CPM ..................................................
	$sql = "select
CASE
    WHEN  
      A.COMAPNY_ID=1 THEN 'AST' when
       A.COMAPNY_ID=2 THEN 'ASTL' when
        A.COMAPNY_ID=3 THEN 'FKTL'
      END AS COMPANY,
      
A.YEAR as YEAR,
 
CASE
    WHEN  
      B.MONTH_ID=1 THEN 'Jan' when
      B.MONTH_ID=2 THEN 'Feb' when
      B.MONTH_ID=3 THEN 'Mar' when
      B.MONTH_ID=4 THEN 'Apr' when
      B.MONTH_ID=5 THEN 'May' when
      B.MONTH_ID=6 THEN 'Jun' when
      B.MONTH_ID=7 THEN 'Jul' when
      B.MONTH_ID=8 THEN 'Aug' when
      B.MONTH_ID=9 THEN 'Sep' when
      B.MONTH_ID=10 THEN 'Oct' when
      B.MONTH_ID=11 THEN 'Nov' when
      B.MONTH_ID=12 THEN 'Dec'    
  END AS MONTH,
  
 B.WORKING_DAY as WOD,A.BASIC_SMV,A.EFFI_PERCENT,B.CAPACITY_MONTH_MIN as CAPACITY_MIN,trunc(B.CAPACITY_MONTH_MIN/60) as CAPACITY_SAH, 
 B.CAPACITY_MONTH_PCS as CAPACITY_BASIC_QTY,
 
 case
    when
 A.COMAPNY_ID=1 THEN
     To_char(2500000) when
 A.COMAPNY_ID=2 THEN
     To_char(3000000) when
 A.COMAPNY_ID=3 THEN
    To_char(8500000)
 END AS CAPACITY_VALUE,
 
 case
    when
 A.COMAPNY_ID=1 THEN
     trunc (2500000/B.CAPACITY_MONTH_PCS,3) when
 A.COMAPNY_ID=2 THEN
     trunc (3000000/B.CAPACITY_MONTH_PCS,3) when
 A.COMAPNY_ID=3 THEN
     trunc (8500000/B.CAPACITY_MONTH_PCS,3)
 END AS UNIT_PRICE
         
from LIB_CAPACITY_CALC_MST a, LIB_CAPACITY_YEAR_DTLS b
 
where A.ID=B.MST_ID and 
A.YEAR in (2020) and 
b.CAPACITY_MONTH_MIN is not null and
A.STATUS_ACTIVE=1 and A.IS_DELETED=0
order by a.year,A.COMAPNY_ID,B.MONTH_ID";

	$sql_result = sql_select($sql);

	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr bgcolor="#FFFF99">
                     	<th style="font-size:24px" align="center" colspan="11">ASROTEX GROUP</th>
                	 </tr>
                    <tr bgcolor="#66FF99">
                    	<td align="center" colspan="11">BU WISE MONTHLY CAPACITY [2019-2020]</td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <th>SL</th>
                        <th>BU</th>
                        <th>Year</th>                        
                        <th>Month</th>
                        <th>WOD</th>
                        <th>Basic SMV</th>
                        <!--<th>Eff %</th>-->
                        <th>Capacity Min</th>
                        <th>Capacity SAH</th>
                        <th>Capacity Qty[Basic]</th>
                        <th>Capacity Value</th>
                        <th>Target Price</th>
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
	                        <td width="50"><? echo $row[COMPANY];?></td>
	                        <td width="40" align="center"><? echo $row[YEAR];?></td>	                        
	                        <td width="50" align="left"><?  echo $row[MONTH];?></td>
                            <td width="45" align="center"><?  echo $row[WOD];?></td>
                            <td width="45" align="center"><?  echo $row[BASIC_SMV];?></td>
                            <?php /*?><td width="45" align="center"><?  echo $row[EFFI_PERCENT];?></td><?php */?>
                            <td width="90" align="right"><? echo number_format($row[CAPACITY_MIN],0).'<br>';?></td>
                            <td width="90" align="right"><? echo number_format($row[CAPACITY_SAH],0).'<br>';?></td>
                            <td width="90" align="right"><? echo number_format($row[CAPACITY_BASIC_QTY],0).'<br>';?></td>
                            <td width="90" align="right"><? echo number_format($row[CAPACITY_VALUE],0).'<br>';?></td>
                            <td width="60" align="right"><? echo number_format($row[UNIT_PRICE],2).'<br>';?></td>
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>



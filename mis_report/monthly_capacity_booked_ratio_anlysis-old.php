<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("Monthly CAPACITY", "", 1, 1,'','','');	
	

//$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );


//CPM ..................................................

$sql2="select C.COMPANY_SHORT_NAME as BU,a.T_YEAR as YEAR,
TO_CHAR(A.PUB_SHIPMENT_DATE,'Mon') as MONTH,
sum(b.SET_SMV*a.PO_QUANTITY/60) as BOOKED_SAH,
sum(b.SET_SMV/e.BASIC_SMV*a.PO_QUANTITY) as ORDER_QTY_BASIC,
sum(a.PO_TOTAL_PRICE ) as ORDER_VALUE
from WO_PO_BREAK_DOWN a,WO_PO_DETAILS_MASTER b, LIB_COMPANY c, LIB_BUYER d, LIB_CAPACITY_CALC_MST e
where A.JOB_NO_MST=B.JOB_NO 
and B.COMPANY_NAME=C.ID
and B.BUYER_NAME=D.ID
and E.COMAPNY_ID=c.id
and E.YEAR=A.T_YEAR
and A.STATUS_ACTIVE=1 and A.IS_DELETED=0
and a.PUB_SHIPMENT_DATE between '01-Jul-2019' and '30-Jun-2020'
group by C.COMPANY_SHORT_NAME,a.T_YEAR,A.PUB_SHIPMENT_DATE
order by C.COMPANY_SHORT_NAME";

//array
$sql_result2 = sql_select($sql2);
foreach($sql_result2 as $row)
{
	$key=$row[BU].$row[YEAR].$row[MONTH];
	
	$dataArr[BOOKED_SAH][$key]+= $row[BOOKED_SAH];
	$dataArr[ORDER_QTY_BASIC][$key]+= $row[ORDER_QTY_BASIC];
	$dataArr[ORDER_VALUE][$key]+= $row[ORDER_VALUE];	
	$dataArr[BK_SAH_PERCENTAGE][$key]+= $row[BOOKED_SAH]/$row[CAPACITY_SAH]*100;
	$dataArr[BK_QTY_PERCENTAGE][$key]+= $row[ORDER_QTY_BASIC]/$row[CAPACITY_BASIC_QTY]*100;
	$dataArr[BK_VAL_PERCENTAGE][$key]+= $row[ORDER_VALUE]/$row[CAPACITY_VALUE]*100;	
	$dataArr[BK_AVG_PRICE][$key]+= $row[ORDER_VALUE]/$row[ORDER_QTY_BASIC];		
}

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
  
 B.WORKING_DAY as WOD,A.BASIC_SMV,trunc(B.CAPACITY_MONTH_MIN/60) as CAPACITY_SAH,
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
                     	<th style="font-size:24px" align="center" colspan="17">ASROTEX GROUP</th>
                	 </tr>
                    <tr bgcolor="#66FF99">
                    	<td align="center" colspan="17">BU WISE MONTHLY CAPACITY [2019-2020]</td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <th>SL</th>
                        <th>BU</th>
                        <th>Year</th>                        
                        <th>Month</th>
                        <th>WOD</th>
                        <th>Basic SMV</th>                       
                        <th>Capacity SAH</th>
                        <th>Cap. Qty [Basic]</th>
                        <th>Capacity Value</th>                        
                        <th>Booked  SAH</th>
                        <th>Bkd Qty [Basic]</th>
                        <th>Booked Value</th>
                        <th>Bkd % (SAH)</th>   
                        <th>Bkd % (Qty)</th>
                        <th>Bkd % (Value)</th>
                        <th>Trgt Price (Ave)</th>
                        <th>Bkd Price (Ave)</th>                     
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($sql_result as $row)
					{
						$key=$row[COMPANY].$row[YEAR].$row[MONTH];
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="25" align="center"><? echo $i;?></td>
	                        <td width="50"><? echo $row[COMPANY];?></td>
	                        <td width="40" align="center"><? echo $row[YEAR];?></td>	                        
	                        <td width="50" align="left"><?  echo $row[MONTH];?></td>
                            <td width="45" align="center"><?  echo $row[WOD];?></td>
                            <td width="45" align="center"><?  echo $row[BASIC_SMV];?></td                                                     
                            ><td width="75" align="right"><? echo number_format($row[CAPACITY_SAH],0).'<br>';?></td>
                            <td width="75" align="right"><? echo number_format($row[CAPACITY_BASIC_QTY],0).'<br>';?></td>
                            <td width="75" align="right"><? echo number_format($row[CAPACITY_VALUE],0).'<br>';?></td>                            
                            <td width="75" align="right"><? echo number_format($dataArr[BOOKED_SAH][$key],0);?></td>    
                        	<td width="75" align="right"><? echo number_format($dataArr[ORDER_QTY_BASIC][$key],0);?></td>
                        	<td width="75" align="right"><? echo number_format($dataArr[ORDER_VALUE][$key],0);?></td> 
                            <td width="60" align="right"><? echo number_format($dataArr[BK_SAH_PERCENTAGE][$key],0);?></td>
                            <td width="60" align="right"><? echo number_format($dataArr[BK_QTY_PERCENTAGE][$key],0);?></td>
                            <td width="60" align="right"><? echo number_format($dataArr[BK_VAL_PERCENTAGE][$key],0);?></td>
                            <td width="70" align="right"><? echo number_format($row[UNIT_PRICE],2).'<br>';?></td>
	                    	<td width="70" align="right"><? echo number_format($dataArr[BK_AVG_PRICE][$key],2);?></td>                                             
                        
                        </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>

<?php 

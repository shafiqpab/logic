<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');

extract($_REQUEST);
echo load_html_head_contents("Monthly CAPACITY", "", 1, 1,'','','');
if($action=="report_generate")
{
//echo $cbo_company_name;	
$dateCondition="'01-JuL-2019' and '30-Jun-2020'";
//$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );
if(str_replace("'","",$cbo_company_name)==0)$compcond="";else $compcond="and B.COMPANY_NAME=$cbo_company_name";
if(str_replace("'","",$cbo_company_name)==0)$compcond2="";else $compcond2="and a.COMPANY_ID=$cbo_company_name";
if(str_replace("'","",$cbo_company_name)==0)$compcond3="";else $compcond3="and c.COMPANY_NAME=$cbo_company_name";
if(str_replace("'","",$cbo_company_name)==0)$compcond4="";else $compcond4="and a.COMAPNY_ID=$cbo_company_name";
//echo  $compcond2;
// For Asrotex
//Booked data ..................................................
$sql2="select C.COMPANY_SHORT_NAME as COMPANY,a.T_YEAR as YEAR,
TO_CHAR(A.PUB_SHIPMENT_DATE,'Mon') as MONTH,
round(sum((b.SET_SMV*a.PO_QUANTITY)/60)) as BOOKED_SAH,
round(sum(a.PO_QUANTITY*b.TOTAL_SET_QNTY)) as BKD_QTY_PCS,
round(sum((b.SET_SMV/e.BASIC_SMV)*a.PO_QUANTITY)) as BKD_BASIC_QTY,
round(sum(a.PO_TOTAL_PRICE)) as BOOKED_VALUE
from WO_PO_BREAK_DOWN a,WO_PO_DETAILS_MASTER b, LIB_COMPANY c, LIB_BUYER d, LIB_CAPACITY_CALC_MST e
where A.JOB_NO_MST=B.JOB_NO
and B.COMPANY_NAME=C.ID
and B.BUYER_NAME=D.ID
and E.COMAPNY_ID=c.id
and E.YEAR=A.T_YEAR
and A.STATUS_ACTIVE=1 and A.IS_DELETED=0
--and B.COMPANY_NAME=1
and a.PUB_SHIPMENT_DATE between $dateCondition $compcond
group by C.COMPANY_SHORT_NAME,a.T_YEAR, TO_CHAR(A.PUB_SHIPMENT_DATE,'Mon')
order by C.COMPANY_SHORT_NAME";

$sql_result2 = sql_select($sql2);

//Booked data array
foreach($sql_result2 as $row)
{
	$key=$row[COMPANY].$row[YEAR].$row[MONTH];
	
	$dataArr[BOOKED_SAH][$key]+= $row[BOOKED_SAH];
	$dataArr[BKD_QTY_PCS][$key]+= $row[BKD_QTY_PCS];
	$dataArr[BKD_BASIC_QTY][$key]+= $row[BKD_BASIC_QTY];
	$dataArr[BOOKED_VALUE][$key]+= $row[BOOKED_VALUE];
	$dataArr[AVE_PRICE_BASIC][$key]+= $row[BOOKED_VALUE]/$row[BKD_BASIC_QTY];
		
}
//Target CM ..................................................
$sql3="select b.COMPANY_SHORT_NAME as COMPANY,
TO_CHAR(a.APPLYING_PERIOD_DATE,'YYYY') as YEAR,
TO_CHAR(a.APPLYING_PERIOD_DATE,'Mon') as MONTH,
case when
a.APPLYING_PERIOD_DATE between '01-Jul-2019' and '31-Dec-2019' then
round((a.MONTHLY_CM_EXPENSE+a.MONTHLY_CM_EXPENSE*a.ASKING_PROFIT/100)/79)
else
round((a.MONTHLY_CM_EXPENSE+a.MONTHLY_CM_EXPENSE*a.ASKING_PROFIT/100)/82) end as TARGET_CM
from LIB_STANDARD_CM_ENTRY a, LIB_COMPANY b
 where APPLYING_PERIOD_DATE between $dateCondition $compcond2
 and a.COMPANY_ID=b.ID
 --and a.COMPANY_ID=1
and a.STATUS_ACTIVE=1 
order by COMPANY_ID, APPLYING_PERIOD_DATE";

$sql_result3 = sql_select($sql3);

//Target CM array
foreach($sql_result3 as $row)
{
	$key=$row[COMPANY].$row[YEAR].$row[MONTH];
		
	$dataArr[TARGET_CM][$key]+= $row[TARGET_CM];
	
}

//Booked CM ..................................................

/*$sql4="select 
CASE
    WHEN  
      C.COMPANY_NAME=1 THEN 'AST' when
      C.COMPANY_NAME=2 THEN 'ASTL' when
      C.COMPANY_NAME=3 THEN 'FKTL' when
      C.COMPANY_NAME=4 THEN 'SFL'
      END AS COMPANY,
D.T_YEAR as YEAR, 
TO_CHAR(d.PUB_SHIPMENT_DATE,'Mon') as MONTH,
round(sum(d.PO_QUANTITY *c.TOTAL_SET_QNTY)) as PO_QTY_PCS,
round(sum(d.PLAN_CUT *c.TOTAL_SET_QNTY)) as PLN_CUT_QTY_PCS,
 sum((case when
B.COSTING_PER=1 then
((a.PRICE_DZN-NVL(a.COMMISSION,0))-(a.TOTAL_COST-NVL(a.CM_COST,0)-NVL(a.COMM_COST,0)-NVL(a.COMMISSION,0))-NVL(a.COMM_COST,0))/12
when
B.COSTING_PER=2 then
((a.PRICE_DZN-NVL(a.COMMISSION,0))-(a.TOTAL_COST-NVL(a.CM_COST,0)-NVL(a.COMM_COST,0)-NVL(a.COMMISSION,0))-NVL(a.COMM_COST,0))
when
B.COSTING_PER=3 then
((a.PRICE_DZN-NVL(a.COMMISSION,0))-(a.TOTAL_COST-NVL(a.CM_COST,0)-NVL(a.COMM_COST,0)-NVL(a.COMMISSION,0))-NVL(a.COMM_COST,0))/24
when
B.COSTING_PER=4 then
((a.PRICE_DZN-NVL(a.COMMISSION,0))-(a.TOTAL_COST-NVL(a.CM_COST,0)-NVL(a.COMM_COST,0)-NVL(a.COMMISSION,0))-NVL(a.COMM_COST,0))/36
when
B.COSTING_PER=5 then
((a.PRICE_DZN-NVL(a.COMMISSION,0))-(a.TOTAL_COST-NVL(a.CM_COST,0)-NVL(a.COMM_COST,0)-NVL(a.COMMISSION,0))-NVL(a.COMM_COST,0))/48
 end)*(d.PO_QUANTITY*c.TOTAL_SET_QNTY)) as BOOKED_CM_VALUE
from WO_PRE_COST_DTLS a, WO_PRE_COST_MST b, WO_PO_DETAILS_MASTER c,WO_PO_BREAK_DOWN d
where 
d.PUB_SHIPMENT_DATE between $dateCondition
and A.JOB_NO=B.JOB_NO
and B.JOB_NO=C.JOB_NO
and c.JOB_NO=d.JOB_NO_MST
and D.STATUS_ACTIVE=1 and D.IS_DELETED=0 
and C.STATUS_ACTIVE=1 and C.IS_DELETED=0
group by COMPANY_NAME,T_YEAR,TO_CHAR(d.PUB_SHIPMENT_DATE,'Mon')
order by C.COMPANY_NAME,d.T_YEAR,TO_CHAR(d.PUB_SHIPMENT_DATE,'Mon')";*/

$sql4="select 
CASE
    WHEN  
      C.COMPANY_NAME=1 THEN 'AST' when
      C.COMPANY_NAME=2 THEN 'ASTL' when
      C.COMPANY_NAME=3 THEN 'FKTL' when
      C.COMPANY_NAME=4 THEN 'SFL'
      END AS COMPANY,
D.T_YEAR as YEAR, 
TO_CHAR(d.PUB_SHIPMENT_DATE,'Mon') as MONTH,
round(sum(d.PO_QUANTITY *c.TOTAL_SET_QNTY)) as PO_QTY_PCS,
round(sum(d.PLAN_CUT *c.TOTAL_SET_QNTY)) as PLN_CUT_QTY_PCS,
 sum((case when
B.COSTING_PER=1 then
(((a.PRICE_DZN/12)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION/12)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-(((a.TOTAL_COST/12)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))-((a.CM_COST/12)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))
-((a.COMM_COST/12)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION/12)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-((a.COMM_COST/12)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))
when
B.COSTING_PER=2 then
(((a.PRICE_DZN)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-(((a.TOTAL_COST)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))-((a.CM_COST)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))
-((a.COMM_COST)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-((a.COMM_COST)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))
when
B.COSTING_PER=3 then
(((a.PRICE_DZN/24)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION/24)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-(((a.TOTAL_COST/24)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))-((a.CM_COST/24)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))
-((a.COMM_COST/24)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION/24)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-((a.COMM_COST/24)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))
when
B.COSTING_PER=4 then
(((a.PRICE_DZN/36)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION/36)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-(((a.TOTAL_COST/36)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))-((a.CM_COST/36)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))
-((a.COMM_COST/36)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION/36)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-((a.COMM_COST/36)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))
when
B.COSTING_PER=5 then
(((a.PRICE_DZN/48)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION/48)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-(((a.TOTAL_COST/48)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))-((a.CM_COST/48)*(d.PLAN_CUT *c.TOTAL_SET_QNTY))
-((a.COMM_COST/48)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))-((a.COMMISSION/48)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY)))
-((a.COMM_COST/48)*(d.PO_QUANTITY *c.TOTAL_SET_QNTY))
 end)) as BOOKED_CM_VALUE
from WO_PRE_COST_DTLS a, WO_PRE_COST_MST b, WO_PO_DETAILS_MASTER c,WO_PO_BREAK_DOWN d
where 
d.PUB_SHIPMENT_DATE between $dateCondition $compcond3
and A.JOB_NO=B.JOB_NO
and B.JOB_NO=C.JOB_NO
and c.JOB_NO=d.JOB_NO_MST
and D.STATUS_ACTIVE=1 and D.IS_DELETED=0 
and C.STATUS_ACTIVE=1 and C.IS_DELETED=0
group by COMPANY_NAME,T_YEAR,TO_CHAR(d.PUB_SHIPMENT_DATE,'Mon')
order by C.COMPANY_NAME,d.T_YEAR,TO_CHAR(d.PUB_SHIPMENT_DATE,'Mon')";

$sql_result4 = sql_select($sql4);

//Booked CM array
foreach($sql_result4 as $row)
{
	$key=$row[COMPANY].$row[YEAR].$row[MONTH];
	$dataArr[PLN_CUT_QTY_PCS][$key]+= $row[PLN_CUT_QTY_PCS];	
	$dataArr[BOOKED_CM_VALUE][$key]+= $row[BOOKED_CM_VALUE];	
}

//Capacity data ..................................................
$sql = "select
CASE
    WHEN  
      A.COMAPNY_ID=1 THEN 'AST' when
       A.COMAPNY_ID=2 THEN 'ASTL' when
        A.COMAPNY_ID=3 THEN 'FKTL' when
		A.COMAPNY_ID=4 THEN 'SFL'
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
B.WORKING_DAY as WOD,A.BASIC_SMV,trunc(B.CAPACITY_MONTH_MIN/60) as CAPACITY_SAH,B.CAPACITY_MONTH_PCS as CAPACITY_QTY,
CASE
    WHEN 
 A.COMAPNY_ID=1 THEN
     2500000 when
 A.COMAPNY_ID=2 THEN
    3000000 when
 A.COMAPNY_ID=3 THEN
8500000 when
 A.COMAPNY_ID=4 THEN
1500000
 END AS CAPACITY_VALUE, 
CASE
    WHEN 
 A.COMAPNY_ID=1 THEN
     TO_CHAR(2500000/B.CAPACITY_MONTH_PCS,'9,999.99') when
 A.COMAPNY_ID=2 THEN
    TO_CHAR(3000000/B.CAPACITY_MONTH_PCS,'9,999.99') when
 A.COMAPNY_ID=3 THEN
    TO_CHAR(8500000/B.CAPACITY_MONTH_PCS,'9,999.99') when
A.COMAPNY_ID=4 THEN
    TO_CHAR(1500000/B.CAPACITY_MONTH_PCS,'9,999.99')
 END AS AVG_UNIT_PRICE
from LIB_CAPACITY_CALC_MST a, LIB_CAPACITY_YEAR_DTLS b
where A.ID=B.MST_ID and 
A.YEAR in (2019,2020) and A.STATUS_ACTIVE=1 and A.IS_DELETED=0 $compcond4

and concat(concat(A.YEAR,'-'),TO_CHAR(TO_DATE(B.MONTH_ID,'MM'),'Mon')) in
('2019-Jul','2019-Aug','2019-Sep','2019-Oct','2019-Nov','2019-Dec','2020-Jan','2020-Feb','2020-Mar','2020-Apr','2020-May','2020-Jun')
order by A.COMAPNY_ID,A.YEAR,B.MONTH_ID";
//echo $sql;die;//-and A.COMAPNY_ID=1
	$sql_result = sql_select($sql);

	?>
    <script>
		function toggle( x, origColor )
		{
			var newColor = 'lime';
			if ( x.style )
			{
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function change_color( str, color) 
		{
			toggle( document.getElementById( str ), color );
		}
	</script>    
    
	<div style="width:1210px; margin-bottom:5px;" align="left">
		<fieldset>
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                     	<th style="font-size:24px" align="center" colspan="20">ASROTEX GROUP</th>
                	 </tr>
                    <tr>
                    	<td style="font-size:14px" align="center" colspan="20"> <strong>BU WISE CAPACITY VS BOOKED RATIO ANALYSIS FOR FISCAL YEAR-2020 </strong></td>
                    </tr>
                   <tr>
                    	<td style="font-size:14px" align="center" colspan="20"><strong> AS ON [ <? $d=strtotime("now"); echo date('d-M-Y h:i:sA',$d);?> ]</strong></td>
                    </tr>
                    <tr>
                    	<td style="font-size:10px" bgcolor="#FFFF00" colspan="6" align="center"> <strong> BASIC INFO </strong></td>              
                    	<td style="font-size:14px" bgcolor="#00FF99" colspan="5" align="center"> <strong> CAPACITY INFO </strong></td>
                        <td style="font-size:14px" bgcolor="#00CCFF" colspan="5" align="center"> <strong> BOOKED INFO </strong></td>
                        <td style="font-size:14px" bgcolor="#FF9900" colspan="4" align="center"> <strong> BOOKED % </strong></td>
                    </tr>
                    <tr style="font-size:14px" bgcolor="#CCCCCC">
                        <th bgcolor="#FFFF99">SL</th>
                        <th bgcolor="#FFFF99">BU</th>
                        <th bgcolor="#FFFF99">Year</th>                        
                        <th bgcolor="#FFFF99">Month</th>
                        <th bgcolor="#FFFF99">WOD</th>
                        <th bgcolor="#FFFF99">Basic SMV</th>                       
                        <th bgcolor="#99FF99">Capacity SAH</th>
                        <th bgcolor="#99FF99">Cap. Qty [Basic]</th>
                        <th bgcolor="#99FF99">Capacity Value</th>
                        <th bgcolor="#99FF99">Trgt CM Value</th>
                        <th bgcolor="#99FF99">Trgt Price [Basic]</th>                        
                        <th bgcolor="#99CCFF">Booked  SAH</th>
                        <th bgcolor="#99CCFF">Bkd Qty [Pcs]</th>
                        <th bgcolor="#99CCFF">Booked <br/>Value</th>
                        <!--<th bgcolor="#99CCFF">Pl-Cut Qty [Pcs]</th>-->
                        <th bgcolor="#99CCFF">Bkd CM Value</th>
                        <th bgcolor="#99CCFF">Bkd Price [Basic]</th> 
                        <th bgcolor="FFCC00">Bkd % (SAH)</th>   
                        <th bgcolor="#FFCC00">Bkd % (Qty)</th>
                        <th bgcolor="#FFCC00">Bkd % (Value)</th>
                        <th bgcolor="#FFCC00">Bkd % (CM)</th>                       
                   </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($sql_result as $row)
					{
						$key=$row[COMPANY].$row[YEAR].$row[MONTH];
						$targetCM[TARGET_CM_CALC][$key]+=($dataArr[TARGET_CM][$key]*75/100)+($dataArr[TARGET_CM][$key]*25/100)/26*$row[WOD];
						
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr style="font-size:16px" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="25" align="center"><? echo $i;?></td>
	                        <td width="45"><? echo $row[COMPANY];?></td>
	                        <td width="40" align="center"><? echo $row[YEAR];?></td>	                        
	                        <td width="45" align="center"><?  echo $row[MONTH];?></td>
                            <td width="40" align="center"><?  echo $row[WOD];?></td>
                            <td width="40" align="center"><?  echo $row[BASIC_SMV];?></td                                                     
                            ><td width="70" align="right"><? echo fn_number_format($row[CAPACITY_SAH],0).'<br>';?></td>
                            <td width="80" align="right"><? echo fn_number_format($row[CAPACITY_QTY],0).'<br>';?></td>
                            <td width="75" align="right"><? echo fn_number_format($row[CAPACITY_VALUE],0).'<br>';?></td>
                         <?php /*?><td width="75" align="right"><? echo fn_number_format($dataArr[TARGET_CM][$key],0).'<br>';?></td><?php */?>
                         <td width="80" align="right"><? echo fn_number_format(($dataArr[TARGET_CM][$key]*75/100)+(($dataArr[TARGET_CM][$key]*25/100)/26*$row[WOD]),0).'<br>';?></td>
                            <td width="70" align="center"><? echo fn_number_format($row[AVG_UNIT_PRICE],2).'<br>';?></td>                            
                            <td width="70" align="right"><? echo fn_number_format($dataArr[BOOKED_SAH][$key],0);?></td>    
                        	<td width="80" align="right"><? echo fn_number_format($dataArr[BKD_QTY_PCS][$key],0);?></td>
                        	<td width="80" align="right"><? echo fn_number_format($dataArr[BOOKED_VALUE][$key],0);?></td>
                           <?php /*?> <td width="70" align="right"><? echo fn_number_format($dataArr[PLN_CUT_QTY_PCS][$key],0);?></td><?php */?>
                            <td width="80" align="right"><? echo fn_number_format($dataArr[BOOKED_CM_VALUE][$key],0);?></td>
                            <td width="65" align="center"><? echo fn_number_format($dataArr[AVE_PRICE_BASIC][$key],2);?></td>
                            <td width="50" align="right"><? echo fn_number_format((($dataArr[BOOKED_SAH][$key]/$row[CAPACITY_SAH])*100),0);?></td>
                            <td width="50" align="right"><? echo fn_number_format((($dataArr[BKD_QTY_PCS][$key]/$row[CAPACITY_QTY])*100),0);?></td>
                            <td width="50" align="right"><? echo fn_number_format((($dataArr[BOOKED_VALUE][$key]/$row[CAPACITY_VALUE])*100),0);?></td>                      
                            <td width="50" align="right"><? echo fn_number_format((($dataArr[BOOKED_CM_VALUE][$key])/$targetCM[TARGET_CM_CALC][$key])*100,0);?></td>                              
	                    	                                            
                        
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
}
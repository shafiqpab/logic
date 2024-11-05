<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("Sample Gate Pass", "", 1, 1,'','','');	
	

$company=('3');

$start_date=$_GET['start'];
$end_date=$_GET['end'];
$dateCondition="'$start_date' and '$end_date'";

	$sql = "select 
	case when
	b.COMPANY_ID=1 then 'AST' when
	b.COMPANY_ID=2 then 'ASTL' when
	b.COMPANY_ID=3 then 'FKTL' when
	b.COMPANY_ID=4 then 'SFL' end as BU,	
	b.SYS_NUMBER as GATE_PASS_NO,b.OUT_DATE AS GATE_PASS_DATE, c.SAMPLE_NAME as SAMPLE_NAME,a.ITEM_DESCRIPTION as ITEM_DESCRIPTION,
a.QUANTITY,a.BUYER_ORDER as BUYER_PO_NO,Y.BUYER_NAME as BUYER_NAME,a.REMARKS
from INV_GATE_PASS_DTLS a
LEFT JOIN INV_GATE_PASS_MST b on (b.id=a.MST_ID)
LEFT JOIN LIB_SAMPLE c on (a.SAMPLE_ID=c.id)
LEFT JOIN WO_PO_BREAK_DOWN  d on (a.BUYER_ORDER_ID=d.ID)
LEFT JOIN WO_PO_DETAILS_MASTER k on (d.JOB_NO_MST=k.JOB_NO)
LEFT JOIN LIB_BUYER  y on (K.BUYER_NAME=y.id)

  where 
     b.OUT_DATE between $dateCondition
     and b.COMPANY_ID = $company
	 and a.status_active=1 and a.is_deleted=0
	 and b.status_active=1 and b.is_deleted=0
     and a.SAMPLE_ID>0
     and a.SAMPLE_ID!=35
order by COMPANY_ID";

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
                    	<td align="center" colspan="11">Buyer Wise Garments Sample Gate Pass Report</td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <th>SL</th>
                        <th>BU</th>
                        <th>GATE PASS NO</th>
                        <th>GATE PASS DATE</th>                        
                        <th>SAMPLE NAME</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>QTY [Pcs]</th>                        
                        <th>BUYER PO NO</th>
                        <th>BUYER NAME</th>
                        <th>REMARKS</th>                       
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
                            <td width="50" align="center"><? echo $i;?></td>
                            <td width="50"><? echo $row[BU];?></td>
	                        <td width="150"><? echo $row[GATE_PASS_NO];?></td>                            
	                        <td width="100" align="center"><? echo $row[GATE_PASS_DATE];?></td>	                        
	                        <td width="150" align="left"> &nbsp;<?  echo $row[SAMPLE_NAME];?></td>
                            <td width="100" align="left"> &nbsp;<?  echo $row[ITEM_DESCRIPTION];?></td>
                            <td width="70" align="center"><?  echo $row[QUANTITY];?></td>
                            <td width="70" align="left"> &nbsp;<?  echo  $row[BUYER_PO_NO];?></td>   
                            <td width="100" align="left"> &nbsp;<?  echo $row[BUYER_NAME];?></td>
                            <td width="60" align="left"> &nbsp;<?  echo $row[REMARKS];?></td>                              
	                    </tr>
						<?
						
	                    $i++;
						
							$ttl_sample += $row['QUANTITY'];
	                }
	                ?>
                         <tr bgcolor="#CCCC99"> 
                         <td colspan="5" align="center"> <strong> Total <strong></td>
                                          
                            <td align="center"> <strong> <?php echo number_format(round($ttl_sample,0));?> <strong> </td> 
                           <td> </td> 
                           <td> </td>                                               
                           <td> </td>     
                        </tr>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>



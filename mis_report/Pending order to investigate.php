<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("Pending Order To Investigate", "", 1, 1,'','','');	
	
//CPM ..................................................
	$sql = "select 
case when
b.COMPANY_NAME=1 then 'AST' when
b.COMPANY_NAME=2 then 'ASTL' when
b.COMPANY_NAME=3 then 'FKTL' end as BU,
c.BUYER_NAME,a.JOB_NO_MST,a.PO_NUMBER,a.PUB_SHIPMENT_DATE as PUB_SHIP_DATE,A.EXTENDED_SHIP_DATE as EXT_SHIP_DATE,
case when
B.ORDER_UOM=1 then 'Pcs' when
B.ORDER_UOM=58 then 'Set' end as UOM,
(A.PO_QUANTITY*B.TOTAL_SET_QNTY) as PO_QTY,
round(A.PO_TOTAL_PRICE) as PO_VALUE,
case when
A.SHIPING_STATUS=1 then 'Pending' when
A.SHIPING_STATUS=2 then 'Pertial Delv' when
A.SHIPING_STATUS=3 then 'Full Delv' end as DELV_STATUS,
Case when
A.STATUS_ACTIVE=1 then 'Active' when
A.STATUS_ACTIVE=2 then 'Deleted' when
A.STATUS_ACTIVE=3 then 'Cancelled' end as ACTIVE_STATUS,
case when
A.DELAY_FOR=1 then  'Sample Approval Delay' when
A.DELAY_FOR=2 then 'Lab Dip Approval Delay' when
A.DELAY_FOR=3 then 'Trims Approval Delay' when
A.DELAY_FOR=4 then 'Yarn In-House Delay' when
A.DELAY_FOR=5 then 'Knitting Delay' when
A.DELAY_FOR=6 then 'Dyeing Delay' when
A.DELAY_FOR=7 then 'Fabric In-House Delay' when
A.DELAY_FOR=8 then 'Trims In-House Delay' when
A.DELAY_FOR=9 then 'Print/Emb Delay' when
A.DELAY_FOR=10 then 'Line Insufficient' when
A.DELAY_FOR=11 then 'Worker Insufficient' when
A.DELAY_FOR=12 then 'Bulk Prod. Approval Delay' when
A.DELAY_FOR=13 then 'Traget Falilure' when
A.DELAY_FOR=14 then 'Inspection Fail' when
A.DELAY_FOR=15 then 'Production Problem' when
A.DELAY_FOR=16 then 'Quality Problem'  end as PENDING_REASONS

from WO_PO_BREAK_DOWN a,WO_PO_DETAILS_MASTER b, LIB_BUYER c
where 
A.JOB_NO_MST=B.JOB_NO
and B.BUYER_NAME=c.id
and a.DELAY_FOR is not null and a.update_date between '01-Jul-2019' and '30-Nov-2019'
order by BU,PUB_SHIP_DATE";

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
                    	<td align="center" colspan="13">BU WISE PENDING ORDER LIST TO INVESTIGATE</td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <th>SL</th> 
                        <th>BU</th>                          
                        <th>Buyer</th>                        
                        <th>Job No</th>
                        <th>PO No</th>
                        <th>Pub-Ship Date</th>
                        <th>Ext.Ship Date</th>
                        <th>UOM</th>
                        <th>PO Qty(Pcs)</th>
                        <th>PO Value</th>
                        <th>Delv. Status</th>
                        <th>Active Status</th>
                        <th>Pending Reasons</th>
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
                            <td width="60" align="center"><? echo $row[BU];?></td>
	                        <td width="110"><? echo $row[BUYER_NAME];?></td>
	                        <td width="90" align="left"><? echo $row[JOB_NO_MST];?></td>	                        
	                        <td width="100" align="left"><?  echo $row[PO_NUMBER];?></td>
                            <td width="100" align="center"><?  echo $row[PUB_SHIP_DATE];?></td>
                            <td width="100" align="center"><?  echo $row[EXT_SHIP_DATE];?></td>
                            <td width="90" align="center"><? echo $row[UOM];?></td>
                            <td width="100" align="right"><? echo number_format($row[PO_QTY]).'<br>';?></td>
                            <td width="100" align="right"><? echo number_format($row[PO_VALUE]).'<br>';?></td>
                            <td width="90" align="center"><? echo $row[DELV_STATUS];?></td>
                            <td width="90" align="center"><? echo $row[ACTIVE_STATUS];?></td>
                            <td width="100" align="left"><? echo $row[PENDING_REASONS];?></td>
                            
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>



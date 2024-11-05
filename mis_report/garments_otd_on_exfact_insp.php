<?php
//http://erp3.asrotex.com/mis_report/garments_otd_on_exfact_insp.php?start=09-Jan-2020&end=20-Jan-2020
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("Garments OTD", "", 1, 1,'','','');	

$company='1,2';
//$dateCondition="'09-Jan-2020' and '15-Jan-2020'";

$start_date=$_GET['start'];
$end_date=$_GET['end'];
$dateCondition="'$start_date' and '$end_date'";
//$start_date='09-Jan-2020';
//$end_date='15-Jan-2020';


// For Last Ex-Factory Date
$sql="select PO_BREAK_DOWN_ID,TO_CHAR(max(EX_FACTORY_DATE),'dd-Mon-yy') as EX_FACTORY_DATE,sum(EX_FACTORY_QNTY) as EX_FACT_QTY  from PRO_EX_FACTORY_MST 
where status_active=1 and is_deleted=0 group by PO_BREAK_DOWN_ID";
$sql_result = sql_select($sql);
foreach($sql_result as $row)
{
	$last_exfact_Arr[$row[PO_BREAK_DOWN_ID]]=$row[EX_FACTORY_DATE];
	$exfact_qty_Arr[$row[PO_BREAK_DOWN_ID]]=$row[EX_FACT_QTY];
}


// For Last Ex-Factory Date
$sql="select PO_NUMBER_ID,TASK_FINISH_DATE  from TNA_PLAN_ACTUAL_HISTORY 
where TASK_NUMBER=101";
$sql_result = sql_select($sql);
$tna_history_data_arr=array();
foreach($sql_result as $row)
{
	$tna_history_data_arr[$row[PO_NUMBER_ID]]=$row[TASK_FINISH_DATE];
}




// For Last Inspection Date & Qty
$sql="select PO_BREAK_DOWN_ID,TO_CHAR(max(INSPECTION_DATE),'dd-Mon-yy') LAST_INSP_DATE,sum(INSPECTION_QNTY) as INSP_QTY,INSPECTION_STATUS from PRO_BUYER_INSPECTION where INSPECTION_STATUS=1 and STATUS_ACTIVE=1 and IS_DELETED=0
group by PO_BREAK_DOWN_ID,INSPECTION_STATUS";
$sql_result = sql_select($sql);
foreach($sql_result as $row)
{
	$ins_date_Arr[$row[PO_BREAK_DOWN_ID]]=$row[LAST_INSP_DATE];
	$ins_qty_Arr[$row[PO_BREAK_DOWN_ID]]=$row[INSP_QTY];
}
// Inspection TNA Plan finish date
$sql="select PO_NUMBER_ID,TASK_NUMBER,TASK_START_DATE,TO_CHAR(TASK_FINISH_DATE,'dd-Mon-yy')as TASK_FINISH_DATE,ACTUAL_START_DATE,ACTUAL_FINISH_DATE from TNA_PROCESS_MST where TASK_NUMBER=101 and STATUS_ACTIVE=1";
$sql_result = sql_select($sql);
foreach($sql_result as $row)
{
	$ins_plan_date_Arr[$row[PO_NUMBER_ID]]=$row[TASK_FINISH_DATE];
}

//OTD Details ..................................................
$sql = "select d.COMPANY_SHORT_NAME as BU,e.BUYER_NAME,a.JOB_NO_MST,a.ID,a.PO_NUMBER,
a.PO_QUANTITY*b.TOTAL_SET_QNTY as PO_QTY_PCS,
round(sum(f.INSPECTION_QNTY)) as INSP_QTY,
a.PO_QUANTITY*b.TOTAL_SET_QNTY- sum(f.INSPECTION_QNTY) as INSP_BAL_QTY,
b.COMPANY_NAME,
TO_CHAR(a.PUB_SHIPMENT_DATE,'dd-Mon-yy') as PUB_SHIP_DATE,
TO_CHAR(a.EXTENDED_SHIP_DATE,'dd-Mon-yy') as EXT_SHIP_DATE,
TO_CHAR(case when a.EXTENDED_SHIP_DATE is null
    then a.PUB_SHIPMENT_DATE 
    else a.EXTENDED_SHIP_DATE  end,'dd-Mon-yy') as TARGET_SHIP_DATE,    
	case when a.SHIPING_STATUS=1 then 'Full Pending' 
    when a.SHIPING_STATUS=2 then 'Partial Ship' 
    when a.SHIPING_STATUS=3 then 'Full Ship' end as SHIPPING_STATUS,
	case when
max(c.EX_FACTORY_DATE) <=
(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end) and a.SHIPING_STATUS=3 then 'On Time' when
max(c.EX_FACTORY_DATE) >
	(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end) and a.SHIPING_STATUS=3 then 'Delay'
else null end as OTD_ON_EX_FACT

from WO_PO_BREAK_DOWN a
	LEFT  JOIN WO_PO_DETAILS_MASTER b on (a.JOB_NO_MST=b.JOB_NO)
	LEFT JOIN PRO_EX_FACTORY_MST c on (c.PO_BREAK_DOWN_ID=a.id)
	LEFT  JOIN LIB_COMPANY d on (b.COMPANY_NAME =d.id)
	LEFT  JOIN LIB_BUYER e on (b.BUYER_NAME=e.id)
	LEFT  JOIN PRO_BUYER_INSPECTION f on (f.PO_BREAK_DOWN_ID=a.id and F.INSPECTION_STATUS=1 and f.STATUS_ACTIVE=1 and f.IS_DELETED=0)
where 
	(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end)  between $dateCondition
and b.COMPANY_NAME IN($company)
and a.STATUS_ACTIVE=1 and A.IS_DELETED=0
--and B.JOB_NO='FKTL-18-01188'
--and B.JOB_NO='AST-19-00303'
group by d.COMPANY_SHORT_NAME,e.BUYER_NAME,a.JOB_NO_MST,a.ID,a.PO_NUMBER,a.PO_QUANTITY,b.TOTAL_SET_QNTY,b.COMPANY_NAME,a.PUB_SHIPMENT_DATE,
a.EXTENDED_SHIP_DATE,a.SHIPING_STATUS
order by COMPANY_SHORT_NAME,TARGET_SHIP_DATE desc";

$sql_result = sql_select($sql);
	
//Summary
/*$sql2="select d.COMPANY_SHORT_NAME as BU,e.BUYER_NAME,
TO_CHAR(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end + 4,'iw') as WEEK_NO,
count(a.PO_NUMBER) as TTL_PO, b.COMPANY_NAME
from WO_PO_BREAK_DOWN a
LEFT OUTER JOIN WO_PO_DETAILS_MASTER b on (a.JOB_NO_MST=b.JOB_NO)
--LEFT OUTER JOIN PRO_EX_FACTORY_MST c on (c.PO_BREAK_DOWN_ID=a.id)
LEFT OUTER JOIN LIB_COMPANY d on (b.COMPANY_NAME =d.id)
LEFT OUTER JOIN LIB_BUYER e on (b.BUYER_NAME=e.id)
where 
(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end) between $dateCondition
and b.COMPANY_NAME IN($company)
and a.STATUS_ACTIVE=1 and A.IS_DELETED=0
--and B.JOB_NO='FKTL-18-01188'
--and B.JOB_NO='AST-19-00303'
group by d.COMPANY_SHORT_NAME,e.BUYER_NAME,TO_CHAR(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end + 4,'iw'), b.COMPANY_NAME
order by COMPANY_SHORT_NAME,WEEK_NO asc";*/

$sql2="select d.COMPANY_SHORT_NAME as BU,e.BUYER_NAME,
count(a.PO_NUMBER) as TTL_PO, b.COMPANY_NAME
from WO_PO_BREAK_DOWN a
LEFT OUTER JOIN WO_PO_DETAILS_MASTER b on (a.JOB_NO_MST=b.JOB_NO)
--LEFT OUTER JOIN PRO_EX_FACTORY_MST c on (c.PO_BREAK_DOWN_ID=a.id)
LEFT OUTER JOIN LIB_COMPANY d on (b.COMPANY_NAME =d.id)
LEFT OUTER JOIN LIB_BUYER e on (b.BUYER_NAME=e.id)
where 
(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end) between $dateCondition
and b.COMPANY_NAME IN($company)
and a.STATUS_ACTIVE=1 and A.IS_DELETED=0
--and B.JOB_NO='FKTL-18-01188'
--and B.JOB_NO='AST-19-00303'
group by d.COMPANY_SHORT_NAME,e.BUYER_NAME,b.COMPANY_NAME
order by COMPANY_SHORT_NAME asc";

$sql_result2 = sql_select($sql2);

?>
	<script>
		function toggle( x, origColor )
		{
			var newColor = 'yellow';
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
    <div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr bgcolor="#FFFF99">
                     	<th style="font-size:24px" align="center" colspan="17">ASROTEX GROUP</th>
                	 </tr>
                    <tr bgcolor="#66FF99">
                    	<td align="center" colspan="17">Garments On Time Delivery Status <b>[Week-02]<b></td>
                    </tr>
                    <tr bgcolor="#33FFCC" style="font-size:14px">
                    	<td align="center" colspan="17">Target Ship Date : <? echo $start_date; ?> To <? echo $end_date;?> <strong style="color: #00C">[ Data Generated : <? $d=strtotime("now"); echo date('d-M-Y h:i:s a',$d);?> ] </strong></td>
                    </tr>
                    <tr bgcolor="#CCCCCC" style="font-size:14px">
                        <th>SL</th>
                        <th>BU</th>
                        <th>Buyer</th>                        
                        <th>JOB NO</th>                       
                        <th>PO NO</th>
                        <!--<th>PO ID</th>-->
                        <th>PO Qty [Pcs]</th>
                        <th>Pub-Ship Date</th>
                        <th>Ext-Ship <br/> Date</th>
                        <th>Trgt_Ship Date</th>
                        <th>Insp Plan Date</th>
                        <th>Last Insp Date</th>
                        <th>Insp. Pass Qty</th> 
                        <!--<th>Insp. Bal Qty</th>-->
                        <th>Last Ex-Fact Date</th>
                        <th>Ex-Fact Qty</th>                          
                        <th>Shipping Status</th>
                        <th>OTD On Insp</th>
                        <th>OTD On Ex-Fact</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($sql_result as $row)
					{
						
						if(strtotime($ins_date_Arr[$row[ID]])<= strtotime($row['TARGET_SHIP_DATE']) && ($ins_qty_Arr[$row[ID]]) > 0 )
						{
							$row['OTD_ON_INSP_ARR']='On Time';
						}
						else if(strtotime($ins_date_Arr[$row[ID]])> strtotime($row['TARGET_SHIP_DATE']) && ($ins_qty_Arr[$row[ID]]) > 0)
						{
							$row['OTD_ON_INSP_ARR']='Delay';
						}
						
						if($tna_history_data_arr[$row[ID]]){$text_color="#F00";}else{$text_color="";}
						
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr style="font-size:14px" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="25" align="center"><? echo $i;?></td>
	                        <td width="50">&nbsp;<? echo $row[BU];?></td>
	                        <td width="100" align="left">&nbsp;<? echo $row['BUYER_NAME'];?></td>	                        
	                        <td width="100" align="left">&nbsp;<?  echo $row['JOB_NO_MST'];?></td>                            
                            <td width="45" align="left">&nbsp;<?  echo $row['PO_NUMBER'];?></td>
                            <?php /*?><td width="45" align="left">&nbsp;<?  echo $row['ID'];?></td><?php */?>
                            <td width="60" align="right"><?  echo number_format($row['PO_QTY_PCS'],0);?></td>
                            <td width="80" align="center"><?  echo $row['PUB_SHIP_DATE'];?></td>                            
                            <td width="80" align="center"><? echo $row['EXT_SHIP_DATE'];?></td>
                            <td width="80" align="center"><? echo $row['TARGET_SHIP_DATE'];?></td>
                            <td width="80" align="center" style="color:<?= $text_color;?>"><? echo $ins_plan_date_Arr[$row[ID]];?></td>
                            <td width="80" align="center"><? echo $ins_date_Arr[$row[ID]];?></td>
                            <td width="60" align="right"><?  echo number_format($ins_qty_Arr[$row[ID]],0);?></td>
                            <?php /*?><td width="60" align="right"><?  echo number_format($row['PO_QTY_PCS']-$ins_qty_Arr[$row[ID]],0);?></td>   <?php */?>                          
                            <td width="80" align="center"><? echo $last_exfact_Arr[$row[ID]];?></td>
                            <td width="60" align="right"><? echo number_format($exfact_qty_Arr[$row[ID]],0);?></td>                             
                            <td width="85" align="left">&nbsp;<? echo $row['SHIPPING_STATUS'];?></td>
                            <td width="80" align="left">&nbsp;<? echo $row['OTD_ON_INSP_ARR'];?></td>
                            <td width="80" align="left">&nbsp;<? echo $row['OTD_ON_EX_FACT'];?></td>                     
	                    </tr>
						<?
						
	                    $i++;
						
						
						
						if($row['OTD_ON_EX_FACT'] == 'On Time')
						{
							$data_otdpo_exfac_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['OTD_PO_EXFACT'] += 1;
						}
						
						if($row['OTD_ON_INSP_ARR']=='On Time')
						{
							$data_otdpo_insp_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['OTD_ON_INSP_ARR'][$row['ID']] = 1;
						}
						
						
						
										
	                }
	                ?>	                
	                </tbody>                    
	            </table>             
        </fieldset>
       
    </div>

    <div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="" rules="all" id="">
                <thead>
                		<tr bgcolor="#FFFF99">
                     	<th style="font-size:16px" align="center" colspan="8">Garments OTD Summary</th>
                    </tr>
                    <tr>
                    	<th bgcolor="#FFCC99" style="font-size:16px" align="center" colspan="4">Basic Information</th>
                        <th width="110" bgcolor="#FF9999"style="font-size:16px" align="center" colspan="2">On Inspection</th>
                     	<th width="110" bgcolor="#FF9900" style="font-size:16px" align="center" colspan="2">On Ex-Factory</th>                        
                    </tr>
                    <tr bgcolor="#CCCCCC" style="font-size:12px">
                        <th>SL</th>
                        <th>BU</th>
                        <th>Buyer</th>                        
                        <!--<th>Week NO</th>-->
                        <th>TTL PO</th>  
                        <th>OTD PO</th>
                        <th>OTD %</th>                   
                        <th>OTD PO</th>
                        <th>OTD %</th>                   
                    </tr>
                </thead>
	            <tbody>
	                <?
					$data_otdpo_arr=array();
					foreach($sql_result as $row)
					{
/*						if($row['OTD_ON_EX_FACT'] == 'On Time')
						{
							$data_otdpo_exfac_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['OTD_PO_EXFACT'] += 1;
						}
						
						if($row['OTD_ON_INSP_ARR']='On Time')
						{
							$data_otdpo_insp_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['OTD_ON_INSP_ARR'] += 1;
						}
*/						
					}
					
					$data_arr=array();
					foreach($sql_result2 as $row)
					{
						$data_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['BU']=$row['BU'];
						$data_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['BUYER_NAME']=$row['BUYER_NAME'];
						$data_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['WEEK_NO']=$row['WEEK_NO'];
						$data_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['TTL_PO']=$row['TTL_PO'];
						$data_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['OTD_PO']=$row['OTD_PO'];					
					}
					
					$i= 1;
					$gTotal_ttl_po=0;
					$gTotal_otd_po_exfact=0;
					$gTotal_otd_po_insp=0;
					foreach($data_arr as $companyId=>$companyArr)
					{
						$comTotal_ttl_po=0;
						$comTotal_otd_po_exfact=0;
						$comTotal_otd_po_insp=0;
						foreach($companyArr as $buyerId=>$row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$row['OTD_PO_EXFACT']='';
							if(!empty($data_otdpo_exfac_arr[$companyId][$buyerId]['OTD_PO_EXFACT']))
							{
								$row['OTD_PO_EXFACT']=$data_otdpo_exfac_arr[$companyId][$buyerId]['OTD_PO_EXFACT'];
							}
							
							$row['OTD_ON_INSP_ARR']='';
							if(!empty($data_otdpo_insp_arr[$companyId][$buyerId]['OTD_ON_INSP_ARR']))
							{
								$row['OTD_ON_INSP_ARR']=count($data_otdpo_insp_arr[$companyId][$buyerId]['OTD_ON_INSP_ARR']);
							}
							
							
							//persentage calculate
							$row['OTD_PERSENTAGE_EXFACT']= number_format(($row['OTD_PO_EXFACT']*1)/$row['TTL_PO']*100,0);
							$row['OTD_PERSENTAGE_INSP']= number_format(($row['OTD_ON_INSP_ARR']*1)/$row['TTL_PO']*100,0);
							
							?>                            
							<tr style="font-size:14px" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="25" align="center"><? echo $i;?></td>
								<td width="50"><? echo $row[BU];?></td>
								<td width="150" align="left"><? echo $row['BUYER_NAME'];?></td>	                        
								<?php /*?><td width="80" align="center"><?  echo $row['WEEK_NO'];?></td><?php */?>
								<td width="60" align="center"><?  echo $row['TTL_PO'];?></td>
                                <td width="60" align="center"><? echo $row['OTD_ON_INSP_ARR']; ?></td>
								<td bgcolor="#CCFFCC" width="60" align="center"><? echo $row['OTD_PERSENTAGE_INSP'];?></td>                                   
								<td width="60" align="center"><? echo $row['OTD_PO_EXFACT']; ?></td>
								<td bgcolor="#CCFFCC" width="60" align="center"><? echo $row['OTD_PERSENTAGE_EXFACT']?$row['OTD_PERSENTAGE_EXFACT']:'';?></td>                             
								                                          
							</tr>
							<?
							$i++;
							$comTotal_ttl_po += $row['TTL_PO'];
							$comTotal_otd_po_exfact += $row['OTD_PO_EXFACT'];
							$comTotal_otd_po_insp += $row['OTD_ON_INSP_ARR'];
							
							$gTotal_ttl_po += $row['TTL_PO'];
							$gTotal_otd_po_exfact += $row['OTD_PO_EXFACT'];
							$gTotal_otd_po_insp += $row['OTD_ON_INSP_ARR'];
						}
						?>
                        <tr bgcolor="#CCCC99">
                            <td colspan="3" align="center"> <strong> <? echo $row[BU];?> Total <strong> </td>
                            <td align="center"> <strong> <?php echo $comTotal_ttl_po;?> <strong> </td>
                            <td align="center"> <strong> <?php echo $comTotal_otd_po_insp;?> <strong> </td>
                            <td align="center"> <strong> <?php echo round($comTotal_otd_po_insp/$comTotal_ttl_po*100,0);?> <strong> </td>                             
                            <td align="center"> <strong> <?php echo $comTotal_otd_po_exfact;?> <strong> </td>
                            <td align="center"> <strong> <?php echo round($comTotal_otd_po_exfact/$comTotal_ttl_po*100,0);?> <strong> </td>                     
                              
                        </tr>
                        <?php
	                }
	                ?>
                    <tr bgcolor="#CCCCCC">
                        <td colspan="3" align="center"> <strong> Grand Total <strong></td>
                        <td align="center"> <strong> <?php echo $gTotal_ttl_po; ?> <strong> </td>
                        <td align="center"> <strong> <?php echo $gTotal_otd_po_insp; ?> <strong> </td>
                        <td align="center"> <strong> <?php echo round($gTotal_otd_po_insp/$gTotal_ttl_po*100,0); ?> <strong> </td>                         
                        <td align="center"> <strong> <?php echo $gTotal_otd_po_exfact; ?> <strong> </td>
                        <td align="center"> <strong> <?php echo round($gTotal_otd_po_exfact/$gTotal_ttl_po*100,0);?> <strong> </td>                                            
                             
                    </tr>
	                </tbody>
	            </table>
        </fieldset>
    </div>
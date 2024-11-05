<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("Garments OTD", "", 1, 1,'','','');

//OTD Details ..................................................
$sql = "select d.COMPANY_SHORT_NAME as BU,e.BUYER_NAME,a.JOB_NO_MST,a.ID,a.PO_NUMBER,
round(sum(a.PO_QUANTITY*b.TOTAL_SET_QNTY)) as PO_QTY_PCS,b.COMPANY_NAME,
TO_CHAR(a.PUB_SHIPMENT_DATE,'dd-Mon-yy')as PUB_SHIP_DATE,TO_CHAR(a.EXTENDED_SHIP_DATE,'dd-Mon-yy')as EXT_SHIP_DATE,
TO_CHAR(case when a.EXTENDED_SHIP_DATE is null 
    then a.PUB_SHIPMENT_DATE 
    else a.EXTENDED_SHIP_DATE  end,'dd-Mon-yy') as TARGET_SHIP_DATE
from WO_PO_BREAK_DOWN a
LEFT OUTER JOIN WO_PO_DETAILS_MASTER b on (a.JOB_NO_MST=b.JOB_NO)
LEFT OUTER JOIN LIB_COMPANY d on (b.COMPANY_NAME =d.id)
LEFT OUTER JOIN LIB_BUYER e on (b.BUYER_NAME=e.id)
where 
(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end) between '12-Dec-2019' and '19-Dec-2019'
and b.COMPANY_NAME IN(1)
and B.JOB_NO='AST-19-00119'
and a.STATUS_ACTIVE=1 and A.IS_DELETED=0
group by d.COMPANY_SHORT_NAME,e.BUYER_NAME,a.JOB_NO_MST,a.ID,a.PO_NUMBER,b.COMPANY_NAME,a.PUB_SHIPMENT_DATE,a.EXTENDED_SHIP_DATE,a.SHIPING_STATUS
order by COMPANY_SHORT_NAME,TARGET_SHIP_DATE";

$sql_result = sql_select($sql);
	
//Summary
$sql2="select d.COMPANY_SHORT_NAME as BU,e.BUYER_NAME,
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
else a.EXTENDED_SHIP_DATE  end) between '12-Dec-2019' and '19-Dec-2019'
and b.COMPANY_NAME IN(1)
and a.STATUS_ACTIVE=1 and A.IS_DELETED=0
group by d.COMPANY_SHORT_NAME,e.BUYER_NAME,TO_CHAR(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end + 4,'iw'), b.COMPANY_NAME
order by COMPANY_SHORT_NAME,WEEK_NO asc";

$sql_result2 = sql_select($sql2);

//Ex-Factory Data
$sql3 = "select d.COMPANY_SHORT_NAME as BU,a.ID,
max(TO_CHAR(c.EX_FACTORY_DATE,'dd-Mon-yy')) as LAST_SHIP_DATE,
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
else null end as OTD_STATUS
from WO_PO_BREAK_DOWN a
LEFT OUTER JOIN WO_PO_DETAILS_MASTER b on (a.JOB_NO_MST=b.JOB_NO)
LEFT OUTER JOIN PRO_EX_FACTORY_MST c on (c.PO_BREAK_DOWN_ID=a.id)
LEFT OUTER JOIN LIB_COMPANY d on (b.COMPANY_NAME =d.id)
LEFT OUTER JOIN LIB_BUYER e on (b.BUYER_NAME=e.id)
where 
(case when
a.EXTENDED_SHIP_DATE is null then a.PUB_SHIPMENT_DATE 
else a.EXTENDED_SHIP_DATE  end) between '12-Dec-2019' and '19-Dec-2019'
and b.COMPANY_NAME IN(1)
and B.JOB_NO='AST-19-00119'
and a.STATUS_ACTIVE=1 and A.IS_DELETED=0
group by d.COMPANY_SHORT_NAME,e.BUYER_NAME,a.JOB_NO_MST,a.ID,a.PO_NUMBER,b.COMPANY_NAME,a.PUB_SHIPMENT_DATE,a.EXTENDED_SHIP_DATE,a.SHIPING_STATUS
order by COMPANY_SHORT_NAME";

$sql_result3 = sql_select($sql3);
//Ex-Factory Data array
foreach($sql_result3 as $row)
{
	$key=$row[BU].$row[ID];
	
	$dataArr['LAST_SHIP_DATE'][$key]+= $row['LAST_SHIP_DATE'];
	$dataArr['SHIPPING_STATUS'][$key]+= $row['SHIPPING_STATUS'];
	$dataArr['OTD_STATUS'][$key]+= $row['OTD_STATUS'];	
		
}

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
                     	<th style="font-size:24px" align="center" colspan="11">ASROTEX GROUP</th>
                	 </tr>
                    <tr bgcolor="#66FF99">
                    	<td align="center" colspan="11">Garments On Time Delivery Status <strong>[Week-51]<strong></td>
                    </tr>
                    <tr bgcolor="#33FFCC">
                    	<td align="center" colspan="11">[Trgt Ship Date From 12-Dec-2019 To 18-Dec-2019] <strong>[Data Generate on <? $d=strtotime("now"); echo date('d-M-Y h:i:sA',$d);?>] </strong></td>
                    </tr>
                    <tr bgcolor="#CCCCCC" style="font-size:14px">
                        <th>SL</th>
                        <th>BU</th>
                        <th>Buyer</th>                        
                        <th>JOB NO</th>                       
                        <th>PO NO</th>
                        <th>PO QTY [Pcs]</th>                                           
                        <th>Pub-Ship Date</th>
                        <th>Ext-Ship <br/> Date</th>
                        <th>Trgt_Ship Date</th>
                        <th>Last Ex-Fact Date</th>                        
                        <th>Shipping Status</th>
                        <th>OTD Status</th>
                    </tr>
                </thead>
	            <tbody>
	                <?
					$i= 1;
					foreach($sql_result as $row)
					
					{
						$key=$row[BU].$row[ID];
						
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
	                    <tr style="font-size:14px" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="25" align="center"><? echo $i;?></td>
	                        <td width="50"><? echo $row[BU];?></td>
	                        <td width="100" align="left"><? echo $row[BUYER_NAME];?></td>	                        
	                        <td width="100" align="left"><?  echo $row[JOB_NO_MST];?></td>                            
                            <td width="45" align="left"><?  echo $row[PO_NUMBER];?></td>
                            <td width="45" align="right"><?  echo fn_number_format($row[PO_QTY_PCS],0);?></td>
                            <td width="85" align="center"><?  echo $row[PUB_SHIP_DATE];?></td>                            
                            <td width="85" align="center"><? echo $row[EXT_SHIP_DATE];?></td>
                            <td width="85" align="center"><? echo $row[TARGET_SHIP_DATE];?></td>                            
                            <td width="85" align="center"><? echo $dataArr['LAST_SHIP_DATE'][$key];?></td>                            
                            <td width="90" align="left"><? echo $dataArr[SHIPPING_STATUS][$key];?></td>
                            <td width="90" align="left"><? echo $dataArr[OTD_STATUS][$key];?></td>                     
	                    </tr>
						<?
						
	                    $i++;
						
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
                     	<th style="font-size:16px" align="center" colspan="11">Garments OTD Summary</th>
                    </tr>
                    <tr>
                    	<th bgcolor="#FFCC99" style="font-size:16px" align="center" colspan="4">Basic Information</th>
                     	<th bgcolor="#FF9900" style="font-size:16px" align="center" colspan="3">On Ex-Factory Date</th>
                        <th bgcolor="#FF9999"style="font-size:16px" align="center" colspan="3">On Inspection Date</th>
                    </tr>
                    <tr bgcolor="#CCCCCC" style="font-size:14px">
                        <th>SL</th>
                        <th>BU</th>
                        <th>Buyer</th>                        
                        <th>Week NO</th>
                        <th>TTL PO</th>  
                        <th>OTD PO</th>
                        <th>OTD %</th>                        
                        <th>TTL PO</th>  
                        <th>OTD PO</th>
                        <th>OTD %</th>                   
                    </tr>
                </thead>
	            <tbody>
	                <?
					$data_otdpo_arr=array();
					foreach($sql_result as $row)
					{
						if($row['OTD_STATUS'] == 'On Time')
						{
							$data_otdpo_arr[$row['COMPANY_NAME']][$row['BUYER_NAME']]['OTD_PO'] += 1;
						}
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
					$gTotal_otd_po=0;
					foreach($data_arr as $companyId=>$companyArr)
					{
						$comTotal_ttl_po=0;
						$comTotal_otd_po=0;
						foreach($companyArr as $buyerId=>$row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$row['OTD_PO']='';
							if(!empty($data_otdpo_arr[$companyId][$buyerId]['OTD_PO']))
							{
								$row['OTD_PO']=$data_otdpo_arr[$companyId][$buyerId]['OTD_PO'];
							}
							
							//persentage calculate
							$row['OTD_PERSENTAGE']= number_format(($row['OTD_PO']*1)/$row['TTL_PO']*100,0);
							
							?>
							<tr style="font-size:14px" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="25" align="center"><? echo $i;?></td>
								<td width="50"><? echo $row[BU];?></td>
								<td width="150" align="left"><? echo $row['BUYER_NAME'];?></td>	                        
								<td width="80" align="center"><?  echo $row['WEEK_NO'];?></td>
								<td width="60" align="center"><?  echo $row['TTL_PO'];?></td> 
								<td width="60" align="center"><? echo $row['OTD_PO']; ?></td>
								<td width="60" align="center"><? echo $row['OTD_PERSENTAGE'];?></td>                                
                                <td width="60" align="center"><?  echo $row['TTL_PO'];?></td> 
								<td width="60" align="center"><? echo $row['OTD_PO']; ?></td>
								<td width="60" align="center"><? echo $row['OTD_PERSENTAGE'];?></td>                                            
							</tr>
							<?
							$i++;
							$comTotal_ttl_po += $row['TTL_PO'];
							$comTotal_otd_po += $row['OTD_PO'];
							
							$gTotal_ttl_po += $row['TTL_PO'];
							$gTotal_otd_po += $row['OTD_PO'];
						}
						?>
                        <tr bgcolor="#33FFFF">
                            <td colspan="4" align="center"> <strong> <? echo $row[BU];?> Total <strong> </td>
                            <td align="center"> <strong> <?php echo $comTotal_ttl_po; ?> <strong> </td>
                            <td align="center"> <strong> <?php echo $comTotal_otd_po; ?> <strong> </td>
                            <td align="center"> <strong> <?php echo round($comTotal_otd_po/$comTotal_ttl_po*100,0); ?> <strong> </td>                            
                            <td align="center"> <strong> <?php echo $comTotal_ttl_po; ?> <strong> </td>
                            <td align="center"> <strong> <?php echo $comTotal_otd_po; ?> <strong> </td>
                            <td align="center"> <strong> <?php echo round($comTotal_otd_po/$comTotal_ttl_po*100,0); ?> <strong> </td>   
                        </tr>
                        <?php
	                }
	                ?>
                    <tr bgcolor="#33CCFF">
                        <td colspan="4" align="center"> <strong> Grand Total <strong></td>
                        <td align="center"> <strong> <?php echo $gTotal_ttl_po; ?> <strong> </td>
                        <td align="center"> <strong> <?php echo $gTotal_otd_po; ?> <strong> </td>
                        <td align="center"> <strong> <?php echo round($gTotal_otd_po/$gTotal_ttl_po*100,0); ?> <strong> </td>                                            
                        <td align="center"> <strong> <?php echo $gTotal_ttl_po; ?> <strong> </td>
                        <td align="center"> <strong> <?php echo $gTotal_otd_po; ?> <strong> </td>
                        <td align="center"> <strong> <?php echo round($gTotal_otd_po/$gTotal_ttl_po*100,0); ?> <strong> </td>      
                    </tr>
	                </tbody>
	            </table>
        </fieldset>
    </div>
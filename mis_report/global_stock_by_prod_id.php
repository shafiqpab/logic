<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("GLOBAL STOCK AS PROD-ID", "", 1, 1,'','','');	
	

$prod_id=$_GET['prod_id'];

//Global stock by Product ID ..................................................
	$sql = "select b.id as PRODUCT_ID,b.ITEM_DESCRIPTION,b.DETARMINATION_ID,b.GSM,b.DIA_WIDTH,c.id as PO_ID,c.PO_NUMBER,c.PO_QUANTITY,c.JOB_NO_MST,sum(case when trans_type in(1,4,5) then quantity else 0 end) as RCV_QTY, sum(case when trans_type in(2,3,6) then quantity else 0 end) as ISSUE_QTY,
 sum((case when trans_type in(1,4,5) then quantity else 0 end)-(case when trans_type in(2,3,6) then quantity else 0 end)) as BAL_QTY,b.current_stock as GLOBAL_STOCK
 from ORDER_WISE_PRO_DETAILS a, PRODUCT_DETAILS_MASTER b, WO_PO_BREAK_DOWN c
 where a.status_active=1 and a.is_deleted=0 and b.item_category_id=13 and a.prod_id=$prod_id and a.entry_form in(2,22,13,16,45,51,58,61,80,81,82,83,84) and a.trans_id>0
 and a.PO_BREAKDOWN_ID=c.id and a.prod_id=b.id
 group by a.PO_BREAKDOWN_ID,b.current_stock,b.id,c.po_number,c.PO_QUANTITY,c.id,b.ITEM_DESCRIPTION,b.DETARMINATION_ID,b.GSM,b.DIA_WIDTH,c.JOB_NO_MST
having sum((case when trans_type in(1,4,5) then quantity else 0 end)-(case when trans_type in(2,3,6) then quantity else 0 end))<>0";

	$sql_result = sql_select($sql);

	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr bgcolor="#FFFF99">
                     	<th style="font-size:24px" align="center" colspan="14">ASROTEX GROUP</th>
                	 </tr>
                    <tr bgcolor="#66FF99">
                    	<td align="center" colspan="14">Global Stock By Product ID</td>
                    </tr>
                    <tr style="font-size:14px" bgcolor="#CCCCCC">
                        <th>SL</th>
                        <th>Product ID</th>
                        <th>Product Details</th>
                        <th>Detarmin ID</th>
                        <th>GSM</th>
                        <th>Dia</th>
                        <th>PO ID</th>                        
                        <th>PO Number</th>
                        <th>Job Number</th>
                        <th>PO Qty</th>
                        <th>Rcv Qty</th>
                        <th>Issue Qty</th>                       
                        <th>Balance Qty</th>
                        <th>Global Stock</th>                      
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
	                        <td width="80" align="center"><? echo $row[PRODUCT_ID];?></td>
                            <td width="200" align="left"><? echo $row[ITEM_DESCRIPTION];?></td>
                            <td width="70" align="center"><? echo $row[DETARMINATION_ID];?></td>
                            <td width="60" align="center"><? echo $row[GSM];?></td>
                            <td width="60" align="center"><? echo $row[DIA_WIDTH];?></td>	
	                        <td width="60" align="center"><? echo $row[PO_ID];?></td>	                        
	                        <td width="150" align="left"><?  echo $row[PO_NUMBER];?></td>
                            <td width="120" align="left"><?  echo $row[JOB_NO_MST];?></td>
                            <td width="80" align="right"><?  echo $row[PO_QUANTITY];?></td>
                            <td width="90" align="right"><? echo $row[RCV_QTY];?></td>
                            <td width="90" align="right"><? echo $row[ISSUE_QTY];?></td>                           
                            <td width="90" align="right"><? echo number_format($row[BAL_QTY],2);?></td>
                            <td width="90" align="right"><? echo number_format($row[GLOBAL_STOCK],2);?></td>                           
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>



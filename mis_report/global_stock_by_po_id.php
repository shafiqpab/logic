<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
extract($_REQUEST);
echo load_html_head_contents("GLOBAL STOCK AS PO-ID", "", 1, 1,'','','');	
	
$po_id=$_GET['po_id'];

//Global stock by PO ID ..................................................
	$sql = "select c.id as PO_ID,c.po_number, c.JOB_NO_MST,prod_id, PRODUCT_NAME_DETAILS, sum(case when trans_type in(1,4,5) then quantity else 0 end) as rcv_qty, sum(case when trans_type in(2,3,6) then quantity else 0 end) as issue_qty,
 sum((case when trans_type in(1,4,5) then quantity else 0 end)-(case when trans_type in(2,3,6) then quantity else 0 end)) as bal_qty,CURRENT_STOCK as global_stock
 from ORDER_WISE_PRO_DETAILS a, PRODUCT_DETAILS_MASTER b,WO_PO_BREAK_DOWN c
 where a.status_active=1 and a.is_deleted=0 and a.prod_id=b.id and b.item_category_id=13 and a.PO_BREAKDOWN_ID=$po_id and a.entry_form in(2,22,13,16,45,51,58,61,80,81,82,83,84) and a.trans_id>0 
 and a.PO_BREAKDOWN_ID=c.id
 --and b.current_stock>0
 group by po_number,prod_id,PRODUCT_NAME_DETAILS,CURRENT_STOCK,c.id, c.JOB_NO_MST";

	$sql_result = sql_select($sql);

	?>
	<div style="width:<? echo $width;?>px; margin-bottom:5px;" align="left">
		<fieldset>
            <table class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr bgcolor="#FFFF99">
                     	<th style="font-size:24px" align="center" colspan="12">ASROTEX GROUP</th>
                	 </tr>
                    <tr bgcolor="#66FF99">
                    	<td align="center" colspan="12">Global Stock By PO/Order ID</td>
                    </tr>
                    <tr bgcolor="#CCCCCC">
                        <th>SL</th>
                        <th>PO ID</th>
                        <th>PO Number</th> 
                        <th>Job Number</th>                       
                        <th>Product ID</th>
                        <th>Product Details</th>
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
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="25" align="center"><? echo $i;?></td>
	                        <td width="80" align="center"><? echo $row[PO_ID];?></td>
	                        <td width="100" align="center"><? echo $row[PO_NUMBER];?></td>	
                            <td width="120" align="center"><? echo $row[JOB_NO_MST];?></td>                        
	                        <td width="80" align="left"><?  echo $row[PROD_ID];?></td>
                            <td width="200" align="left"><?  echo $row[PRODUCT_NAME_DETAILS];?></td>
                            <td width="80" align="right"><?  echo number_format($row[RCV_QTY],2);?></td>
                            <td width="80" align="right"><?  echo number_format($row[ISSUE_QTY],2);?></td>                           
                            <td width="80" align="right"><? echo number_format($row[BAL_QTY],2);?></td>
                            <td width="80" align="right"><? echo number_format($row[GLOBAL_STOCK],2);?></td>                           
	                    </tr>
						<?
						
	                    $i++;
	                }
	                ?>
	                
	                </tbody>
	            </table>
             
        </fieldset>
        
    </div>



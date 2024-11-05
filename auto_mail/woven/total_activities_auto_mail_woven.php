<?php
date_default_timezone_set("Asia/Dhaka");

include('../../includes/common.php');
//require_once('../../mailer/class.phpmailer.php');
include('../setting/mail_setting.php');

$file = 'mail_log.txt';
$current = file_get_contents($file);
$current .= "TOTAL ACTIVITIES-Mail :: Date & Time: ".date("d-m-Y H:i:s",time())."\n";
file_put_contents($file, $current);



$company_library =return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library =return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$supplier_library =return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");


	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
		$previous_3month_date = date('Y-m-d H:i:s', strtotime('-92 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
		$previous_3month_date = change_date_format(date('Y-m-d H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 
	}

	
	if($db_type==0){
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_b	=" and ((b.insert_date between '".$previous_date."' and '".$current_date."') or  ( b.UPDATE_DATE between '".$previous_date."' and '".$current_date."')) ";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_d	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_b	=" and  ((b.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM') or  ( b.UPDATE_DATE between '".$previous_date."' and '".$current_date." 11:59:59 PM'))";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_d	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}

	function fn_remove_zero($int,$format){
		return $int>0?number_format($int,$format):'';
		
	}


$is_insert_date=0;


//$company_library=array(3=>$company_library[3]); $previous_date='1-Jul-2021';




foreach($company_library as $compid=>$compname)/// Yesterday Total Activities
{
	
	ob_start();
	?>
    
    <table width="920">
        <tr>
            <td valign="top" align="center">
                <strong><font size="+2">Total Activities of ( Date :<?  echo date("d-m-Y", strtotime($previous_date));  ?>)</font></strong>
            </td>
        </tr>
        <tr>
            <td valign="top" align="center"><strong><? echo $company_library[$compid]; ?></strong></td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="11" align="center"><strong>Order Received</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                        <td rowspan="2" width="120" align="center"><strong>Buyer</strong></td>
                        <td rowspan="2" width="80" align="center"><strong>Avg. Lead Time</strong></td>
                        <td colspan="3" align="center"><strong>Confirm Order</strong></td>
                        <td colspan="3" align="center"><strong>Projected Order</strong></td>
                        <td colspan="3" align="center"><strong>Total</strong></td>
                    </tr>
                    <tr bgcolor="#EEE">
                        <td width="85" align="center"><strong>Qty.(Pcs)</strong></td>
                        <td width="85" align="center"><strong>Value</strong></td>
                        <td width="80" align="center"><strong>Avg. Rate</strong></td>
                        <td width="85" align="center"><strong>Qty.(Pcs)</strong></td>
                        <td width="85" align="center"><strong>Value</strong></td>
                        <td width="80" align="center"><strong>Avg. Rate</strong></td>
                        <td width="85" align="center"><strong>Qty.(Pcs)</strong></td>
                        <td width="85" align="center"><strong>Value</strong></td>
                        <td width="85" align="center"><strong>Avg. Rate</strong></td>
                    </tr>
                    <?
					if($is_insert_date==0){$str_cond_b=" and b.po_received_date between '$previous_date' and '$current_date'";}
					$sql="select b.pub_shipment_date,b.po_received_date,b.id,b.is_confirmed,a.buyer_name,sum(a.total_set_qnty*b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name = '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b group by b.id,b.pub_shipment_date,b.po_received_date,a.buyer_name,b.is_confirmed"; //and b.id=28856
					
					
					$nameArray_mst2=sql_select($sql);
					$tot_rows2=count($nameArray_lc_sc);
					$totalQty=array();$buyer_lead_time_arr=array();$buyer_data_arr=array();$totalPoArr=array();
					foreach($nameArray_mst2 as $row2)
					{
						
						$daysOnHand=0;
						if($db_type==2)
						{
							$pub_shipment_date= change_date_format($row2[csf('pub_shipment_date')],'','',1);	
							$daysOnHand = datediff("d",change_date_format($row2[csf('po_received_date')],'','',1),$pub_shipment_date);
						}
						else
						{
							$pub_shipment_date= change_date_format($row2[csf('pub_shipment_date')]);	
							$daysOnHand = datediff("d",change_date_format($row2[csf('po_received_date')]),$pub_shipment_date);
						}	
						
						$buyer_lead_time_arr[$row2[csf('buyer_name')]]+=$daysOnHand;
						
						$buyer_data_arr[$row2[csf('buyer_name')]][$row2[csf('is_confirmed')]]['po_qty']+=$row2[csf('po_quantity')];
						$buyer_data_arr[$row2[csf('buyer_name')]][$row2[csf('is_confirmed')]]['po_val']+=$row2[csf('po_total_price')];

						$totalQty[$row2[csf('is_confirmed')]]['po_qty']+=$row2[csf('po_quantity')];
						$totalQty[$row2[csf('is_confirmed')]]['po_val']+=$row2[csf('po_total_price')];
						$totalPoArr[$row2[csf('buyer_name')]][$row2[csf('id')]]=$row2[csf('id')];
					
					}
					//var_dump($buyer_data_arr);
					
					
						
					if($is_insert_date==0){$str_cond_b=" and b.po_received_date between '$previous_date' and '$current_date'";}
						
						$total_qnty=array(); $total_value=array(); 
						
                    	$sql_mst="select a.buyer_name as buyer_id,c.buyer_name from wo_po_details_master a, wo_po_break_down b,lib_buyer c where a.job_no=b.job_no_mst and a.buyer_name=c.id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b group by a.buyer_name,c.buyer_name order by c.buyer_name";				
						$nameArray_mst=sql_select($sql_mst);
						$tot_rows2=count($nameArray_mst);
						$flag=0;
						foreach($nameArray_mst as $row)
						{
							$conf_proj_qty=0;$conf_proj_value=0;
							$i++;
					?>
                    <tr>
                        <td><? echo $buyer_library[$row[csf('buyer_id')]]; ?></td>
                        <td align="center" title="Lead Time: <? echo $buyer_lead_time_arr[$row[csf('buyer_id')]]; ?>">
							<? //echo $buyer_lead_time_arr[$row[csf('buyer_id')]]; ?>
                            <? echo number_format($buyer_lead_time_arr[$row[csf('buyer_id')]]/count($totalPoArr[$row[csf('buyer_id')]]));?>
                        </td>
                        <?php
                        for($m=1; $m<=2; $m++){
						?>
                            <td align="right">
                            <?
                                $conf_proj_qty = $buyer_data_arr[$row[csf('buyer_id')]][$m]['po_qty'];
                               	echo fn_remove_zero($conf_proj_qty,2);
                            ?>
                            </td>
                            <td align="right">
                            <?
                                $conf_proj_value = $buyer_data_arr[$row[csf('buyer_id')]][$m]['po_val']; 
                                echo fn_remove_zero($conf_proj_value,2);
                            ?>
                            </td>                        
                            <td align="right">
                                <?
                                    $avg_rate= $buyer_data_arr[$row[csf('buyer_id')]][$m]['po_val']/$buyer_data_arr[$row[csf('buyer_id')]][$m]['po_qty'];
                                    echo fn_remove_zero($avg_rate,2);
                                ?>
                            </td>
                        <? 
						}
						
						$conf_proj_qty = $buyer_data_arr[$row[csf('buyer_id')]][1]['po_qty']+$buyer_data_arr[$row[csf('buyer_id')]][2]['po_qty'];
						$conf_proj_value = $buyer_data_arr[$row[csf('buyer_id')]][1]['po_val']+$buyer_data_arr[$row[csf('buyer_id')]][2]['po_val'];
						
						?>
                        
                        <td align="right"><? echo fn_remove_zero($conf_proj_qty,2);  ?></td>
                        <td align="right"><? echo fn_remove_zero($conf_proj_value,2);  ?></td>
                        <td align="right">
							<?
								$avg_rate_tot= $conf_proj_value/$conf_proj_qty; 
								echo fn_remove_zero($avg_rate_tot,2);  
                            ?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					
					if($tot_rows2==0)
					{
					?>
						<tr><td colspan="11" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th colspan="2">Total</th>
                            <?php
								for($i=1; $i<=2; $i++)
                       			{
							?>
                        		<th align="right">
								<?
									$grand_qty+=$totalQty[$i]['po_qty'];   
									echo fn_remove_zero($totalQty[$i]['po_qty'],2);   
								?>
                                </th>
                            	<th align="right">
								<?
									$grand_value+=$totalQty[$i]['po_val'];    
									echo fn_remove_zero($totalQty[$i]['po_val'],2);   
								?>
                                </th>
                            	<th align="right">
								<?
									$tot_rate=$totalQty[$i]['po_val']/$totalQty[$i]['po_qty'];
									echo fn_remove_zero($tot_rate,2);
                                ?>
                                </th>
                            <?	} ?>
                            <th align="right"><? echo fn_remove_zero($totalQty[1]['po_qty']+$totalQty[2]['po_qty'],2);  ?></th>
                            <th align="right"><? echo fn_remove_zero($totalQty[1]['po_val']+$totalQty[2]['po_val'],2);  ?></th>
                            <th align="right">
                            	<?
									$grand_rate=($totalQty[1]['po_val']+$totalQty[2]['po_val'])/($totalQty[1]['po_qty']+$totalQty[2]['po_qty']);
									echo fn_remove_zero($grand_rate,2);
									$grand_qty=0;
									$grand_value=0;
								?>
                            </th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="5" height="30" align="center"><strong>Export LC/Sales Contract Receive</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="250" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>LC/SC</strong></td>
                        <td width="250" align="center"><strong>LC/SC No</strong></td>
                        <td width="200" align="center"><strong>Value</strong></td>
                    </tr>
                    <?
					if($is_insert_date==0){
						$str_cond_lc_date=" and lc_date between '$previous_date' and '$current_date'";
						$str_cond_sc_date=" and contract_date between '$previous_date' and '$current_date'";
						$str_cond='';
					}
					
					
					
					
						$i=0; $tot_lc_value=0;
						
                    	$sql_lc_sc="SELECT sum(lc_value) as lc_sc_value, buyer_name, 1 as type, export_lc_no as no from com_export_lc where beneficiary_name=$compid and status_active=1 and is_deleted=0 $str_cond $str_cond_lc_date group by buyer_name,export_lc_no
						union all
						SELECT sum(contract_value) as lc_sc_value, buyer_name, 2 as type, contract_no as no from com_sales_contract where beneficiary_name=$compid and status_active=1 and is_deleted=0 $str_cond $str_cond_sc_date group by buyer_name,contract_no order by buyer_name";
									
						$nameArray_lc_sc=sql_select($sql_lc_sc);
						$tot_rows13=count($nameArray_lc_sc);
						
						foreach($nameArray_lc_sc as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td align="center"><? if($row[csf('type')] == 1) echo "LC"; else echo "SC"; ?></td>
                        <td><?   echo $row[csf('no')]; ?></td>
                        <td align="right">
							<?
								$value= $row[csf('lc_sc_value')];
								echo number_format($value,2); 
								$tot_lc_value += $value;  
							?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows13==0)
					{
					?>
						<tr><td colspan="5" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th align="right"><?  echo  number_format($tot_lc_value,2);  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="7" height="30" align="center"><strong>Back to Back Open</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="150" align="center"><strong>Item Category</strong></td>
                        <td width="180" align="center"><strong>Supplier</strong></td>
                        <td width="150" align="center"><strong>Value</strong></td>
                        <td width="180" align="center"><strong>LC Number </strong></td>
                        <td width="100" align="center"><strong>Catg Total</strong></td>
                        <td align="center"><strong>Supplier Total</strong></td>
                    </tr>
                    <?
						
					if($is_insert_date==0){
						$str_cond=" and lc_date between '$previous_date' and '$current_date'";
					}
					
						
					$backToBackArr=array();$catTotal=array();$supTotal=array();
						
						
						$i=0;$tot_bb_value=0;
                    	$sql_back_back="Select supplier_id, sum(lc_value) as lc_value, item_category_id, lc_number from com_btb_lc_master_details where importer_id=$compid and status_active=1 and is_deleted=0 $str_cond group by supplier_id,item_category_id,lc_number";  //echo $sql_back_back;
						$nameArray_back_back=sql_select($sql_back_back);
						foreach($nameArray_back_back as $row)
						{
							$backToBackArr[$item_category[$row[csf('item_category_id')]]][$supplier_library[$row[csf('supplier_id')]]][$row[csf('lc_number')]]=$row;//Assinding of item and supplier;
						
							$catTotal[$item_category[$row[csf('item_category_id')]]][]=$row[csf('lc_value')];
							$supTotal[$item_category[$row[csf('item_category_id')]]][$supplier_library[$row[csf('supplier_id')]]][]=$row[csf('lc_value')];
						
						}

						//echo count($supTotal['Dyes']['ABC']);
						
						
						$nameArray_back_back=sql_select($sql_back_back);
												
						ksort($backToBackArr);//var_dump($backToBackArr);
						$tot_rows14=count($nameArray_back_back);
						foreach($backToBackArr as $item_name=>$suppliyerData)
						{	ksort($suppliyerData);$itemFlag=0;
							foreach($suppliyerData as $upplyer_name=>$lc_data)
							{	$supFlag=0;
								foreach($lc_data as $row)
								{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $item_name; ?></td>
                        <td><? echo $upplyer_name; ?></td>
                        <td align="right">
							<?
								$value= $row[csf('lc_value')];
								echo number_format($value,2); 
								$tot_bb_value += $value;  
							?>
                        </td>
                        <td><? echo $row[csf('lc_number')]; ?></td>
						<? 
                        if($itemFlag==0){
                            echo '
                            <td align="right" rowspan="'.count($catTotal[$item_name]).'">'.number_format(array_sum($catTotal[$item_name]),2).'</td>
                            <td align="right" rowspan="'.count($supTotal[$item_name][$upplyer_name]).'">'.number_format(array_sum($supTotal[$item_name][$upplyer_name]),2).'</td>
							</tr>
                            ';
                        }
                        else if($supFlag==0){
                            echo '
                            <td align="right" rowspan="'.count($supTotal[$item_name][$upplyer_name]).'">'.number_format(array_sum($supTotal[$item_name][$upplyer_name]),2).'</td>
                            </tr>
                            ';
                        }
                        else
                        {
                            echo'</tr>';	
                        }
						
					
					$itemFlag=1;
					$supFlag=1;
					$flag=1;
								}
							}
							
						}
					
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th>&nbsp;</th>
                            <th align="right"><?  echo  number_format($tot_bb_value,2);  ?></th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="9" height="30" align="center"><strong>Fabric  Received</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30"><strong>SL</strong></td>
                        <td width="130" align="center"><strong>Supplier Name</strong></td>
                        <td width="80" align="center"><strong>Qty. (Yds)</strong></td>
                        <td width="80" align="center"><strong>Qty. (Kg)</strong></td>
                        <td width="60" align="center"><strong>Receive Currency</strong></td>
                        <td width="80" align="center"><strong>Avg. Rate</strong></td>
                        <td width="100" align="center"><strong>Amount</strong></td>
                        <td width="80" align="center"><strong>Avg. Rate(Tk.)</strong></td>
                        <td align="center"><strong>Amount(Tk.)</strong></td>
                    </tr>
                    <?
						$i=0; $tot_quantity=0; $tot_value=0;$tot_value_tk=0;
						
					if($is_insert_date==0){
						$str_cond_a=" and a.receive_date between '$previous_date' and '$current_date'";
					}
						
                    	$sql_rec="SELECT a.SUPPLIER_ID,a.CURRENCY_ID,
						 SUM (CASE WHEN b.UOM = 12 THEN d.quantity ELSE 0 END) AS QTY_KG,
						 SUM (CASE WHEN b.UOM = 27 THEN d.quantity ELSE 0 END) AS QTY_YDS,
						 sum(e.avg_rate_per_unit) as AVG_RATE, 
						 sum(c.cons_amount/a.exchange_rate) as CONS_AMOUNT ,
						 sum(e.avg_rate_per_unit) as AVG_RATE_TK, 
						 sum(c.cons_amount) as CONS_AMOUNT_TK
						FROM inv_receive_master        a,
							 pro_finish_fabric_rcv_dtls b,
							 inv_transaction           c,
							 order_wise_pro_details    d,
							 product_details_master e
					   WHERE     a.id = b.mst_id and e.id=c.prod_id
							 AND b.trans_id = c.id
							 AND c.id = d.trans_id
							 AND b.id = d.dtls_id
							 AND a.entry_form = 17
							 AND d.entry_form = 17
							 AND a.item_category = 3
							 AND a.status_active = 1
							 AND a.is_deleted = 0
							 AND a.company_id = $compid
							 $str_cond_a
					GROUP BY a.supplier_id,a.CURRENCY_ID";	
					$nameArray_rec=sql_select($sql_rec);
					// echo $sql_rec; 
						
					$tot_rows3=count($fabRecDataArr);
					$totalDataArr=array();
					foreach($nameArray_rec as $supplier_id=>$row)
					{
						$totalDataArr[QTY_KG]+=$row[QTY_KG];
						$totalDataArr[QTY_YDS]+=$row[QTY_YDS];
						$totalDataArr[CONS_AMOUNT]+=$row[CONS_AMOUNT];
						$totalDataArr[CONS_AMOUNT_TK]+=$row[CONS_AMOUNT_TK];
					
					$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><?=$supplier_library[$row[SUPPLIER_ID]]; ?></td>
                        <td align="right"><?=$row[QTY_KG]; ?></td>
                        <td align="right"><?=$row[QTY_YDS]; ?></td>
                        <td align="center"><?=$currency[$row[CURRENCY_ID]]; ?></td>
                        <td align="right"><?=$row[AVG_RATE]; ?></td>
                        <td align="right"><?=$row[CONS_AMOUNT]; ?></td>
                        <td align="right"><?=$row[AVG_RATE_TK]; ?></td>
                        <td align="right"><?=$row[CONS_AMOUNT_TK]; ?></td>
                    </tr>
                    <?	
						$flag=1;
					}
					if($tot_rows3==0)
					{
					?>
						<tr><td colspan="9" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                            <th colspan="2">Total</th>
                            <th align="right"><?=$totalDataArr[QTY_KG];?></th>
                            <th align="right"><?=$totalDataArr[QTY_YDS];?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><?=$totalDataArr[CONS_AMOUNT];?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><?=$totalDataArr[CONS_AMOUNT_TK];?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>

        <tr>
            <td valign="top" align="left">
                 <table cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="6" height="30" align="center"><strong>Fabric Issued</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="30"><strong>SL</strong></td>
                        <td width="200" align="center"><strong>Purpose</strong></td>
                        <td width="80" align="center"><strong>Qty.(Yds)</strong></td>
                        <td width="80" align="center"><strong>Qty.(Kg)</strong></td>
                        <td width="100" align="center"><strong>Value</strong></td>
                        <td width="80" align="center"><strong>Avg. Rate(Tk.)</strong></td>
                    </tr>
                    <?
						
					if($is_insert_date==0){
						$str_cond_a=" and a.issue_date between '$previous_date' and '$current_date'";
					}
						
						
						$i=0; $tot_quantity=0; $tot_value=0;
						
                    	$sql_issue="SELECT a.ISSUE_PURPOSE,
						 SUM (CASE WHEN e.unit_of_measure = 12 THEN d.quantity ELSE 0 END)
							 AS QTY_KG,
						 SUM (CASE WHEN e.unit_of_measure = 27 THEN d.quantity ELSE 0 END)
							 AS QTY_YDS,
						 SUM (e.avg_rate_per_unit)
							 AS AVG_RATE_TK,
						 SUM (c.cons_amount)
							 AS CONS_AMOUNT_TK
					FROM inv_issue_master           a,
						 inv_wvn_finish_fab_iss_dtls b,
						 inv_transaction            c,
						 order_wise_pro_details     d,
						 product_details_master     e
				   WHERE     a.id = b.mst_id
						 AND b.trans_id = c.id
						 AND c.id = d.trans_id
						 AND b.id = d.dtls_id
						 AND d.prod_id = e.id
						 AND a.entry_form = 19
						 AND a.item_category = 3
						 AND a.status_active = 1
						 AND a.is_deleted = 0
						 AND a.company_id = $compid
						 $str_cond_a
				GROUP BY a.ISSUE_PURPOSE";				
						$nameArray_issue=sql_select($sql_issue);
						
						$tot_rows4=count($nameArray_issue);
						
					foreach($nameArray_issue as $row)
					{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $yarn_issue_purpose[$row[ISSUE_PURPOSE]]; ?></td>
                        <td align="right"><?=number_format($row[QTY_YDS],2);?></td>                        
                        <td align="right"><?=number_format($row[QTY_KG],2);?></td>                        
                        <td align="right"><?=number_format($row[CONS_AMOUNT_TK],2);?></td>                        
                        <td align="right"><?=number_format($row[AVG_RATE_TK],2);?></td>                        
                    </tr>
                    <?	
						$flag=1;
					}
					if($tot_rows4==0)
					{
					?>
						<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th>&nbsp;</th>
                            <th align="right"><? echo number_format($tot_quantity ,2)  ?></th>
                            <th align="right"><?  echo  number_format($tot_value,2);  ?></th>
                            <th align="right">&nbsp;</th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        
           
        
        
        

        <tr>
            <td valign="top" align="left">
                 <table width="60%" cellpadding="0" cellspacing="0" rules="all" border="1">
                    <tr>
                        <td colspan="3" height="30" align="center"><strong>Fabric Issue to Cutting</strong></td>
                    </tr>
                    
                    <tr>
                        <td width="30" align="center"><strong>SL</strong></td>
                        <td width="325" align="center"><strong>Buyer</strong></td>
                        <td width="250" align="center"><strong>In house</strong></td>
                        <td width="250" align="center"><strong>Sub Con</strong></td>
                        <td width="250" align="center"><strong>Total</strong></td>
                    </tr>
                    <?
                        
						if($is_insert_date==0){
							$str_cond_a_issue_date="and a.issue_date between '$previous_date' and '$current_date'";
							$str_cond_b='';
						}
						
						$i=0;
						$fbIssueDataArr=array();
						$sql_fab_issue="select a.buyer_id,sum(b.issue_qnty) as issue_qnty,a.knit_dye_source from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.company_id=$compid and a.entry_form=18 and a.knit_dye_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b $str_cond_a_issue_date group by a.buyer_id,a.knit_dye_source";		
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						foreach($nameArray_fab_issue as $row)
                        {
							$fbIssueDataArr[$row[csf('buyer_id')]][$row[csf('knit_dye_source')]]+=$row[csf('issue_qnty')];
						}
						
						$in_house_qty=$sub_con_qty=$grand_total_qty=0;
						foreach($fbIssueDataArr as $buyer_id=>$row)
                        {
                          $in_house_qty += $row[1];
                          $sub_con_qty += $row[3];   
                          $grand_total_qty += $row[1]+$row[3];   
                          
						  $i++;
                    ?>
                    <tr>
                        <td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$buyer_id]; ?></td>
                        <td align="right"><? echo $row[1]; ?></td>
                        <td align="right"><? echo $row[3]; ?></td>
                        <td align="right"><? echo $row[1]+$row[3]; ?></td>
                    </tr>
                    <?	
                        $flag=1;
                        }
                    ?> 
                    <tr>
                        <tfoot>
                            <th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($in_house_qty ,2)  ?></th>
                            <th align="right"><? echo number_format($sub_con_qty,2)  ?></th>
                            <th align="right"><? echo number_format($grand_total_qty ,2)  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>



        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="11" height="30" align="center"><strong>Cutting Production</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td align="center"><strong>Buyer</strong></td>
                        <td width="90" align="center"><strong>In-house Self Order</strong></td>
                        <td width="90" align="center"><strong>In-house Sub-con Order</strong></td>
                        <td width="90" align="center"><strong>Out Side Self Order</strong></td>
                        <td width="90" align="center"><strong>Total In-house</strong></td>
                        <td width="90" align="center"><strong>Total Self Order</strong></td>
                        <td width="70" align="center"><strong>Reject Qty.</strong></td>
                        <td width="90" align="center"><strong>Total Cutting </strong></td>
                        <td width="50" align="center"><strong>Reject %</strong></td>
                    </tr>
                    <?
					$i=0;
					if($is_insert_date==0){
						$str_cond_a="and a.production_date between '$previous_date' and '$current_date'";
					}
					
					$sql_cutting = "select a.production_source,c.buyer_name, a.production_quantity, a.reject_qnty from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id=$compid and a.production_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $str_cond_a";
						
					$nameArray_cuttign=sql_select($sql_cutting);
					$rejectQtyArr=array();$cuttingDataArr=array();
					foreach($nameArray_cuttign as $row)
					{
						$cuttingDataArr[$row[csf('buyer_name')]][$row[csf('production_source')]]+=$row[csf('production_quantity')];
						$rejectQtyArr[$row[csf('buyer_name')]][$row[csf('production_source')]]+=$row[csf('reject_qnty')];
					}
					
					
					
					if($is_insert_date==0){
						$str_cond_a="and a.production_date between '$previous_date' and '$current_date'";
					}
					
					$sql_sub="select  a.production_qnty, a.reject_qnty,c.party_id, 2 as production_source from  subcon_gmts_prod_dtls a, subcon_ord_dtls b,subcon_ord_mst c  where a.order_id=b.id and b.job_no_mst=c.subcon_job and a.status_active=1 and a.is_deleted=0 and a.production_type = 1 $str_cond_a";
					$sqlResult =sql_select($sql_sub);					
					foreach($sqlResult as $row)
					{
						$cuttingDataArr[$row[csf('party_id')]][$row[csf('production_source')]]+=$row[csf('production_qnty')];
						$rejectQtyArr[$row[csf('party_id')]][$row[csf('production_source')]]+=$row[csf('reject_qnty')];
					
					}

					
					$grand_inhouseSelfOrder=0;$grand_inhouseSubconOrder=0;$grand_outSideSelfOrder=0;		
					$grand_totalInhouse=0;$grand_totalSelfOrder=0;$grand_totalCutting=0;$grand_totalReject=0;
					foreach($cuttingDataArr as $buyer_id=>$row)
					{
						$totalInhouse=$row[1]+$row[2];	
						$totalSelfOrder=$row[1]+$row[3];
						$totalCutting=$row[1]+$row[2]+$row[3];
						$totalReject=$rejectQtyArr[$buyer_id][1]+$rejectQtyArr[$buyer_id][2]+$rejectQtyArr[$buyer_id][3];
						
						
						$grand_inhouseSelfOrder+=$row[1];	
						$grand_inhouseSubconOrder+=$row[2];	
						$grand_outSideSelfOrder+=$row[3];	
						$grand_totalInhouse+=$totalInhouse;	
						$grand_totalSelfOrder+=$totalSelfOrder;
						$grand_totalCutting+=$totalCutting;
						$grand_totalReject+=$totalReject;
						
						
						
						
						$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$buyer_id]; ?></td>
                        <td align="right"><? echo $row[1];?></td>                 
                        <td align="right"><? echo $row[2];?></td>                 
                        <td align="right"><? echo $row[3];?></td>                 
                        <td align="right"><? echo $totalInhouse;?></td>                 
                        <td align="right"><? echo $totalSelfOrder;?></td>                 
                        <td align="right"><? echo $totalReject;?></td>                 
                        <td align="right"><? echo $totalCutting;?></td>                 
                        <td align="right"><? echo number_format(($totalReject/$totalCutting)*100,4); ?></td>                 
                    </tr>
                    <?	
					$flag=1;
					}
					
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo $grand_inhouseSelfOrder;?></th>
                            <th align="right"><? echo $grand_inhouseSubconOrder; ?></th>
                            <th align="right"><? echo $grand_outSideSelfOrder; ?></th>
                            <th align="right"><? echo $grand_totalInhouse; ?></th>
                            <th align="right"><? echo $grand_totalSelfOrder; ?></th>
                            <th align="right"><? echo $grand_totalReject; ?></th>
                            <th align="right"><? echo $grand_totalCutting; ?></th>
                            <th align="right"><? echo number_format(($grand_totalReject/$grand_totalCutting)*100,4);?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>


        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="8" height="30" align="center"><strong>Sewing Completed</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="200" align="center"><strong>Buyer</strong></td>
                        <td width="150" align="center"><strong>Good Qty. (Pcs)</strong></td>
                        <td width="125" align="center"><strong>Reject Qty.</strong></td>
                        <td width="125" align="center"><strong>Alter Qty.</strong></td>
                        <td width="125" align="center"><strong>Spot Qty.</strong></td>
                        <td width="125" align="center"><strong>Total</strong></td>
                        <td width="125" align="center"><strong>FOB Value</strong></td>
                    </tr>
                    <?
						$pro_qnty=array();$rej_qnty=array();$alter_qnty=array();
						$spot_qnty=array();$total_qnty=array();$fob_val=array();
						
						if($is_insert_date==0){
							$str_cond_a="and a.production_date between '$previous_date' and '$current_date'";
						}
						
						
						$sql = "select c.buyer_name, a.production_quantity, a.reject_qnty, a.alter_qnty, a.spot_qnty, b.unit_price from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id =$compid and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $str_cond_a ";  //and b.id=29329and b.shiping_status=1
						

                        $tot_production_quantity=0;$tot_reject_qnty=0;$tot_alter_qnty=0;
                        $tot_spot_qnty=0;$tot_all=0;$tot_fob_val=0;

						$sew_sql = sql_select($sql);
						foreach($sew_sql as $sew_array)
						{
							$pro_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("production_quantity")];
							$rej_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("reject_qnty")];
							$alter_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("alter_qnty")];
							$spot_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("spot_qnty")];
							$total_qnty[$sew_array[csf("buyer_name")]]+=$sew_array[csf("production_quantity")]+$sew_array[csf("reject_qnty")]+$sew_array[csf("alter_qnty")]+$sew_array[csf("spot_qnty")];
							$fob_val[$sew_array[csf("buyer_name")]]+=($sew_array[csf("production_quantity")]+$sew_array[csf("reject_qnty")]+$sew_array[csf("alter_qnty")]+$sew_array[csf("spot_qnty")])*$sew_array[csf("unit_price")];
						}
					
					
						$i=0;
						foreach($pro_qnty as $buyer_id=>$row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$buyer_id]; ?></td>
                        <td align="right">
							<?
								echo number_format($pro_qnty[$buyer_id],2);
							   $tot_production_quantity += $pro_qnty[$buyer_id]; 
                            ?>
                        </td>
                        <td align="right">
							<?
                               echo number_format($rej_qnty[$buyer_id],2);
                               $tot_reject_qnty += $rej_qnty[$buyer_id]; 
                            ?>
                        </td>                 
                        <td align="right">
                            <?
                                echo number_format($alter_qnty[$buyer_id],2);
								$tot_alter_qnty += $alter_qnty[$buyer_id]; 
                            ?>
                        </td>
                        <td align="right">
							<?
								echo number_format($spot_qnty[$buyer_id],2);
								$tot_spot_qnty += $spot_qnty[$buyer_id]; 
							?>
                        </td>
                        <td align="right">
							<?
								$total= $total_qnty[$buyer_id];
								echo number_format($total,2);
								$tot_all += $total; 
							?>
                        </td>
                        <td align="right">
							<?
								$fob= $fob_val[$buyer_id];
								echo number_format($fob,2);
								$tot_fob_val += $fob; 
							?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					?> 
                    <tr bgcolor="#EEE">
                        <td>&nbsp;</td>
                        <td align="center"><b>Total</b></td>
                        <td align="right"><b><? echo number_format($tot_production_quantity,2)  ?></b></td>
                        <td align="right"><b><? echo number_format($tot_reject_qnty,2);  ?></b></td>
                        <td align="right"><b><? echo number_format($tot_alter_qnty,2); ?></b></td>
                        <td align="right"><b><? echo number_format($tot_spot_qnty,2); ?></b></td>
                        <td align="right"><b><? echo number_format($tot_all,2); ?></b></td>
                        <td align="right"><b><? echo number_format($tot_fob_val,2); ?></b></td>
                    </tr>
                    <tr>
                    	<tfoot>
                    		<th>&nbsp;</th>
                            <th>In %</th>
                            <th align="right">
								<?
									$production_quantity_per= ($tot_production_quantity/$tot_all)*100;
									echo number_format($production_quantity_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$reject_qnty_per= ($tot_reject_qnty/$tot_all)*100; 
									echo number_format($reject_qnty_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$alter_qnty_per= ($tot_alter_qnty/$tot_all)*100;  
									echo number_format($alter_qnty_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$spot_qnty_per= ($tot_spot_qnty/$tot_all)*100;   
									echo number_format($spot_qnty_per,2)  
								?>
                            </th>
                            <th align="right">
								<? 
									//echo number_format($tot_all,2)  
								?>
                            </th>
                            <th align="right">
								<? 
									//echo number_format($tot_all,2)  
								?>
                            </th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="70%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="4" height="30" align="center"><strong>Garments Finishing</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="300" align="center"><strong>Buyer</strong></td>
                        <td width="170" align="center"><strong>Qty. (Pcs)</strong></td>
                        <td width="180" align="center"><strong>Number of Carton</strong></td>
                    </tr>
                    <?
						$i=0;
						if($is_insert_date==0){
							$str_cond_a="and a.production_date between '$previous_date' and '$current_date'";
						}
						
						$sql_fab_issue="select c.buyer_name, sum(a.production_quantity) as production_quantity, sum(a.carton_qty) as carton_qty from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id =$compid and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $str_cond_a group by c.buyer_name";	//and b.shiping_status=1
									
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						$tot_rows10=count($nameArray_fab_issue);
						
                        $tot_prod_qty=0;$tot_carton_qty=0;
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
							if($is_insert_date==0){
								$str_cond_a="and a.production_date between '$previous_date' and '$current_date'";
							}
							
							$final_qty=return_field_value("sum(a.production_quantity)", "pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c", "a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id  =$compid and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status=1 $str_cond_a");
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td align="right">
							<?
                               echo number_format($row[csf('production_quantity')],2); 
							   $tot_prod_qty += $row[csf('production_quantity')]; 
                            ?>
                        </td>
                        <td align="right">
							<?
                               echo number_format($row[csf('carton_qty')],2); 
							   $tot_carton_qty += $row[csf('carton_qty')]; 
                            ?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows10==0)
					{
					?>
						<tr><td colspan="4" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($tot_prod_qty,2);  ?></th>
                            <th align="right"><? echo number_format($tot_carton_qty,2);  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="7" height="30" align="center"><strong>Final Inspection</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="150" align="center"><strong>Job No</strong></td>
                        <td width="250" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>Order No</strong></td> 
                        <td width="100" align="center"><strong>Inspection Qty</strong></td> 
                        <td width="125" align="center"><strong>Shipment Date</strong></td>     
                        <td width="150" align="center"><strong>Inspection Status</strong></td>                        
                    </tr>
                    <?
						$i=0;
						
						if($is_insert_date==0){
							$str_cond="and a.inspection_date between '$previous_date' and '$current_date'";
						}
                    	
						$sql_fab_issue="select a.inspection_status,c.job_no,c.buyer_name,b.po_number,a.inspection_qnty,b.shipment_date from pro_buyer_inspection a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.id in(SELECT MAX(id) FROM pro_buyer_inspection where inspection_status in(1,2,3) $str_cond GROUP BY po_break_down_id) and c.company_name =$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";	
						
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						$tot_rows11=count($nameArray_fab_issue);
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
							
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $row[csf('job_no')]; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td><? echo $row[csf('po_number')]; ?></td>
                        <td align="right"><? echo $row[csf('inspection_qnty')]; ?></td>
                        <td align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                        <td align="center"><? echo $inspection_status[$row[csf('inspection_status')]]; ?></td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows11==0)
					{
					?>
						<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                 </table>
            </td>
        </tr>
        
        
        <tr>
            <td valign="top" align="left">
                 <table width="70%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="4" height="30" align="center"><strong>Ex-factory Done</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="400" align="center"><strong>Buyer</strong></td>
                        <td width="200" align="center"><strong>Delv. Qty. (Pcs)</strong></td>
                        <td width="200" align="center"><strong>FOB Value</strong></td>
                    </tr>
                    <?
						$ex_fac_qty=array();
						$ex_fac_val=array();
						$tot_ex_factory_qnty=0;$tot_ex_factory_val=0;
						
						if($is_insert_date==0){
							$str_cond_a="and a.ex_factory_date between '$previous_date' and '$current_date'";
						}
						
						$ex_sql = sql_select("select c.buyer_name,a.ex_factory_qnty,b.unit_price from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $str_cond_a");
						
						foreach($ex_sql as $ex_array)
						{
							$ex_fac_qty[$ex_array[csf("buyer_name")]]+=$ex_array[csf("ex_factory_qnty")];
							$ex_fac_val[$ex_array[csf("buyer_name")]]+=$ex_array[csf("ex_factory_qnty")]*$ex_array[csf("unit_price")];
						}
						
						
						if($is_insert_date==0){
							$str_cond_a="and a.ex_factory_date between '$previous_date' and '$current_date'";
						}
						$i=0;
                    	$sql_fab_issue="select c.buyer_name from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $str_cond_a group by c.buyer_name";				
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						
						$tot_rows12=count($nameArray_fab_issue);
						
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
							
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td align="right">
							<?
								echo number_format($ex_fac_qty[$row[csf("buyer_name")]],2);
							   $tot_ex_factory_qnty += $ex_fac_qty[$row[csf("buyer_name")]]; 
                            ?>
                        </td>
                        <td align="right">
							<?
								echo number_format($ex_fac_val[$row[csf("buyer_name")]],2);
							   $tot_ex_factory_val += $ex_fac_val[$row[csf("buyer_name")]]; 
                            ?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows12==0)
					{
					?>
						<tr><td colspan="4" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($tot_ex_factory_qnty,2);  ?></th>
                            <th align="right"><? echo number_format($tot_ex_factory_val,2);  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
 
        <tr>
        	<td>
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="7" height="30" align="center"><strong>Full Shipment</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="200" align="center"><strong>Buyer Name</strong></td>
                        <td width="120" align="center"><strong>Job No</strong></td>
                        <td width="200" align="center"><strong>PO No</strong></td>
                        <td width="100" align="center"><strong>Plan Ship Qty</strong></td>
                        <td width="100" align="center"><strong>Actual Ship Qty</strong></td>
                        <td align="right"><strong>Value</strong></td>
                    </tr>
                    <?
                    	
						if($is_insert_date==0){
							$str_cond_b="and b.pub_shipment_date between '$previous_date' and '$current_date'";
						}
						
						$sql_full_ship="select b.id,b.plan_cut,b.po_quantity,b.po_total_price,b.job_no_mst,b.po_number,c.buyer_name from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.shiping_status = 3 and b.shiping_status=3 and c.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $str_cond_b group by b.job_no_mst, b.id,b.plan_cut,b.po_quantity,b.po_total_price,b.po_number,c.buyer_name";
						
						
						$fullShipArray=sql_select($sql_full_ship);
						$tot_full_ship=count($fullShipArray);
						$tot_plan_cut=0; $tot_po_quantity=0; $tot_po_total_price=0;
						foreach($fullShipArray as $row){
						$i++;
						$tot_plan_cut+=$row[csf('plan_cut')];
						$tot_po_quantity+=$row[csf('po_quantity')];
						$tot_po_total_price+=$row[csf('po_total_price')];
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td><? echo $row[csf('job_no_mst')]; ?></td>
                        <td><? echo $row[csf('po_number')]; ?></td>
                        <td align="right"><? echo $row[csf('plan_cut')]; ?></td>
                        <td align="right"><? echo $row[csf('po_quantity')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('po_total_price')],2); ?></td>
                    </tr>
                    <?
						$flag=1;
						}
					if($tot_full_ship==0)
					{
					?>
						<tr><td colspan="7" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?>
                    
                    <tr>
                    	<tfoot bgcolor="#EEE">
                            <th colspan="4">Total</th>
                            <th align="right"><? echo number_format($tot_plan_cut);  ?></th>
                            <th align="right"><? echo number_format($tot_po_quantity);  ?></th>
                            <th align="right"><? echo number_format($tot_po_total_price,2);  ?></th>
                        </tfoot>
                    </tr>
                    
                </table>
           </td>
        </tr>
         
        <tr>
        	<td>
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="8" height="30" align="center"><strong>Leftover Garments After Shipment</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td align="center" width="150"><strong>Buyer Name</strong></td>
                        <td align="center" width="130"><strong>Job No</strong></td>
                        <td align="center" width="150"><strong>Style</strong></td>
                        <td align="center" width="120"><strong>PO No</strong></td>
                        <td align="center" width="100"><strong>Fin Qty</strong></td>
                        <td align="center" width="100"><strong>Ex-Fac Qty</strong></td>
                        <td align="center"><strong>Leftover Qty</strong></td>
                    </tr>
                    <?
						if($is_insert_date==0){
							$str_cond_d="and d.production_date between '$previous_date' and '$current_date'";
						}
						
						
						$sql_leftover="select sum(a.ex_factory_qnty) as ex_factory_qnty,b.job_no_mst,b.po_number,c.buyer_name,c.style_ref_no,sum(d.production_quantity) as finish_quantity from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c,pro_garments_production_mst d where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.po_break_down_id=d.po_break_down_id and a.shiping_status = 3 and d.production_type=8 and b.shiping_status=3 and c.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=3 and c.is_deleted=0 and c.status_active=1 $str_cond_d group by b.job_no_mst,b.po_number,c.buyer_name,style_ref_no";
						
						 //echo $sql_leftover;
						$leftoverArray=sql_select($sql_leftover);
						$tot_leftover=count($leftoverArray);
						$i=1;
						foreach($leftoverArray as $row){
						
						$leftover_qty=($row[csf('finish_quantity')]-$row[csf('ex_factory_qnty')]);
						if($leftover_qty){
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                        <td><? echo $row[csf('style_ref_no')]; ?></td>
                        <td><? echo $row[csf('po_number')]; ?></td>
                        <td align="right"><? echo $row[csf('finish_quantity')]; ?></td>
                        <td align="right"><? echo $row[csf('ex_factory_qnty')]; ?></td>
                        <td align="right"><? echo $leftover_qty; ?></td>
                    </tr>
                    <?
                    	$i++;
                    		$flag=1;
							}
						}
					if($tot_leftover==0)
					{
					?>
						<tr><td colspan="8" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?>
                    
                </table>
           </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="5" height="30" align="center"><strong>PC Received</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="100" align="center"><strong>LC/SC</strong></td>
                        <td width="250" align="center"><strong>LC/SC No</strong></td>
                        <td width="250" align="center"><strong>Loan No</strong></td>
                        <td width="200" align="center"><strong>Amount</strong></td>
                    </tr>
                    <?
						$i=0; $tot_loan_amount=0;
						
						if($is_insert_date==0){
							$str_cond_a_loan_date="and a.loan_date between '$previous_date' and '$current_date'";
							$str_cond_b='';
						}
						
						
						$sql_pre_export="select c.export_type,c.lc_sc_id,max(b.loan_number) as loan_number,sum(c.amount) loan_amount from com_pre_export_finance_mst a, com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c where a.id=b.mst_id and b.id=c.pre_export_dtls_id and a.beneficiary_id=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b $str_cond_a_loan_date group by c.export_type, c.lc_sc_id";
						
						$nameArray_pre_export=sql_select($sql_pre_export);
						$tot_rows15=count($nameArray_pre_export);
						foreach($nameArray_pre_export as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td align="center"><? if($row[csf('export_type')] == 1) echo "LC"; else echo "SC"; ?></td>
                        <td align="center">
							<? 
								if($row[csf('export_type')] == 1) 
								{
									$lc_no=return_field_value("export_lc_no", "com_export_lc", "id='$row[lc_sc_id]' and status_active=1 and is_deleted=0");
									echo $lc_no;
								}
								else
								{
									$sales_cont_no=return_field_value("contract_no", "com_sales_contract", "id='$row[lc_sc_id]' and status_active=1 and is_deleted=0"); 
									echo $sales_cont_no;
								}
							?>
                        </td>
                        <td><?   echo $row[csf('loan_number')]; ?></td>
                        
                        <td align="right">
							<?
								$value= $row[csf('loan_amount')];
								echo number_format($value,2); 
								$tot_loan_amount += $value;  
							?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows15==0)
					{
					?>
						<tr><td colspan="5" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th align="right"><?  echo  number_format($tot_loan_amount,2);  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="6" height="30" align="center"><strong>Export Proceed Realized</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="200" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>LC/SC</strong></td>
                        <td width="150" align="center"><strong>LC/SC No</strong></td>
                        <td width="150" align="center"><strong>Realized</strong></td>
                        <td width="200" align="center"><strong>Short Realized</strong></td>
                    </tr>
                    <?
						$i=0; $tot_realized=0; $tot_short_realized=0;
						
						if($is_insert_date==0){
							$str_cond_a_received_date="and a.received_date between '$previous_date' and '$current_date'";
							$str_cond_b='';
						}
						
						$sql_realization_invoice="select a.buyer_id,c.is_lc,c.lc_sc_id,b.type,sum(b.document_currency) as tot_document_currency from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_invoice_ship_mst c where a.id=b.mst_id and a.invoice_bill_id=c.id and a.benificiary_id=$compid and a.is_invoice_bill=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b $str_cond_a_received_date group by a.buyer_id,c.is_lc,c.lc_sc_id,b.type";
						
						
						
						$nameArray_realization_invoice=sql_select($sql_realization_invoice);
						$tot_rows16=count($nameArray_realization_invoice);
						foreach($nameArray_realization_invoice as $row_invoice)
						{
								$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><?   echo $buyer_library[$row_invoice[csf('buyer_id')]]; ?></td>
                        <td align="center"><? if($row_invoice[csf('is_lc')] == 1) echo "LC"; else echo "SC"; ?></td>
                        <td>
							<? 
								if($row_invoice[csf('is_lc')] == 1) 
								{
									$lc_no=return_field_value("export_lc_no", "com_export_lc", "id='$row_invoice[lc_sc_id]' and status_active=1 and is_deleted=0");
									echo $lc_no;
								}
								else
								{
									$sales_cont_no=return_field_value("contract_no", "com_sales_contract", "id='$row_invoice[lc_sc_id]' and status_active=1 and is_deleted=0"); 
									echo $sales_cont_no;
								}
							?>
                        </td>
                        <td align="right">
							<? 
								if($row_invoice[csf('type')] == 1) 
								{
									echo number_format($row_invoice[csf('tot_document_currency')],2);
									$tot_realized+= $row_invoice[csf('tot_document_currency')];
								}
							?>
                        </td>
                        <td align="right">
							<? 
								if($row_invoice[csf('type')] == 0) 
								{
									echo number_format($row_invoice[csf('tot_document_currency')],2);
									$tot_short_realized+= $row_invoice[csf('tot_document_currency')];
								}
							?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
						
						
						if($is_insert_date==0){
							$str_cond_a_received_date="and a.received_date between '$previous_date' and '$current_date'";
							$str_cond_b='';
						}
						
						$sql_realization_bill="select a.buyer_id,c.is_lc,c.lc_sc_id,b.type,sum(b.document_currency) as tot_document_currency from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and a.invoice_bill_id=c.doc_submission_mst_id and a.benificiary_id=$compid and a.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b $str_cond_a_received_date group by a.buyer_id,c.is_lc,c.lc_sc_id,b.type";
						
						
						$nameArray_realization_bill=sql_select($sql_realization_bill);
						$tot_rows17=count($nameArray_realization_bill);
						foreach($nameArray_realization_bill as $row_bill)
						{
							$i++;
					?>
                    <tr>
                        <td align="center"><? echo $i; ?></td>
                        <td><?   echo $buyer_library[$row_bill[csf('buyer_id')]]; ?></td>
                        <td align="center"><? if($row_bill[csf('is_lc')] == 1) echo "LC"; else echo "SC"; ?></td>
                        <td>
							<? 
								if($row_bill[csf('is_lc')] == 1) 
								{
									$lc_no=return_field_value("export_lc_no", "com_export_lc", "id='$row_bill[lc_sc_id]' and status_active=1 and is_deleted=0");
									echo $lc_no;
								}
								else
								{
									$sales_cont_no=return_field_value("contract_no", "com_sales_contract", "id='$row_bill[lc_sc_id]' and status_active=1 and is_deleted=0"); 
									echo $sales_cont_no;
								}
							?>
                        </td>
                        <td align="right">
							<? 
								if($row_bill[csf('type')] == 1) 
								{
									echo number_format($row_bill[csf('tot_document_currency')],2);
									$tot_realized+= $row_bill[csf('tot_document_currency')];
								}
							?>
                        </td>
                        <td align="right">
							<? 
								if($row_bill[csf('type')] == 0) 
								{
									echo number_format($row_bill[csf('tot_document_currency')],2); 
									$tot_short_realized+= $row_bill[csf('tot_document_currency')];
								}
							?>
                        </td>
                    </tr>
					<?	
                   	$flag=1;
				    }
					$tot_count=$tot_rows16+$tot_rows17;
					if($tot_count==0)
					{
					?>
						<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th colspan="4" align="center">Total :</th>
                            <th align="right"><?  echo number_format($tot_realized,2);   ?></th>
                            <th align="right"><?  echo number_format($tot_short_realized,2);   ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
    </table>
<?
	
    	$message=ob_get_contents();
    	ob_clean();
		
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=76 and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and a.company_id=$compid";
		$mail_sql=sql_select($sql);
		
		$toArr=array();
		foreach($mail_sql as $row)
		{
			$toArr[$row[csf('email_address')]]=$row[csf('email_address')];
		}
		
		$to=implode(',',$toArr);
		$subject="Total Activities (Woven) of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
		$header=mailHeader();
		 if($_REQUEST['isview']==1){
			if($to){echo $to;}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[76]."</b>]<br>";
			}
			echo $message;
		}
		else{
			if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail );

		}	
		
		
		
		
		
		
		
		// //2=>Yesterday Total Activities
		// $to="";
		// $sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=76 and b.mail_user_setup_id=c.id and a.company_id=$compid  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		// $mail_sql2=sql_select($sql2);
		// foreach($mail_sql2 as $row)
		// {
		// 	if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		// }
		
		// $subject="Total Activities (Woven) of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
    	// $message="";
    	// $message=ob_get_contents();
    	// ob_clean();
		// $header=mailHeader();
		// if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		// echo $message;



}


?> 
<?php
date_default_timezone_set("Asia/Dhaka");

include('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
include('setting/mail_setting.php');

$file = 'mail_log.txt';
$current = file_get_contents($file);
$current .= "Total Production Activities Mail :: Date & Time: ".date("d-m-Y H:i:s",time())."\n";
file_put_contents($file, $current);
$is_insert_date_active=0;

$sql="select id,team_member_name,member_contact_no from lib_mkt_team_member_info where  status_active =1 and is_deleted=0";
$data_array=sql_select($sql);
foreach( $data_array as $row )
{ 
	$dealing_merchant_arr[$row[csf("id")]]=$row[csf("team_member_name")].'<br>'.$row[csf("member_contact_no")];
}

 
$team_leader_name_arr = return_library_array( "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0", "id", "team_leader_name");
$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$party_library = return_library_array( "select id, other_party_name from lib_other_party where  status_active=1 and is_deleted=0", "id", "other_party_name");
$supplier_library = return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$user_arr = return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$country_arr = return_library_array( "select id, country_name from lib_country", "id", "country_name");


 
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		$current_date =$_REQUEST['view_date']? date("Y-m-d H:i:s", strtotime($_REQUEST['view_date'])):$current_date;
		
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
		$previous_3month_date = change_date_format(date('Y-m-d H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 

		$current_date=$previous_date;
	
	
	if($db_type==0){
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_d	=" and d.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_d	=" and d.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}


		
		$str_cond_f	=" and a.receive_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_g	=" and a.product_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_h=" and f.process_end_date between '".$previous_date."' and '".$current_date."'";
	
	
	function fn_remove_zero($int,$format){
		return $int>0?number_format($int,$format):'';
		
	}

//$company_library=array(9=>$company_library[9]);

foreach($company_library as $compid=>$compname)/// Total Activities
{
	
	ob_start();
	?>
    
    <table width="920">
        <tr>
            <td valign="top" align="center">
                <strong><font size="+2">Total  Production Activities of ( Date :<?  echo date("d-m-Y", strtotime($previous_date));  ?>)<?=($is_insert_date_active==0)?"":"[Insert Date Wise]";?></font></strong>
            </td>
        </tr>
        <tr>
            <td valign="top" align="center">
                <strong><? echo $company_library[$compid];  ?></strong>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="11" align="center"><strong>Order Received</strong></td>
                 	</tr>
                    <tr>
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

					if($is_insert_date_active==0){
						$str_cond_b=" and b.PUB_SHIPMENT_DATE between '".$previous_date."' and '".$current_date."'";
					}
					
					$sql="select b.pub_shipment_date,b.po_received_date,b.id,b.is_confirmed,a.buyer_name,sum(a.total_set_qnty*b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name = '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b group by b.id,b.pub_shipment_date,b.po_received_date,a.buyer_name,b.is_confirmed"; //and b.id=28856
					$nameArray_mst2=sql_select($sql);
					$buyer_lead_time_arr=array(); 
					$buyer_data_arr=array();
					$totalQty=array(); 
					
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
					
					}
					//var_dump($buyer_data_arr);
					
					$total_qnty=array(); 
					$total_value=array(); 
					$sql_mst="select a.buyer_name as buyer_id,c.buyer_name from wo_po_details_master a, wo_po_break_down b,lib_buyer c where a.job_no=b.job_no_mst and a.buyer_name=c.id and a.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b group by a.buyer_name,c.buyer_name order by c.buyer_name";				
					$nameArray_mst=sql_select($sql_mst);
					$tot_rows2=count($nameArray_mst);
					$flag=0;$conf_proj_qty=0;
					$conf_proj_value=0;
					foreach($nameArray_mst as $row)
					{
							
					$i++;
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td><? echo $buyer_library[$row[csf('buyer_id')]]; ?></td>
                        <td align="center" title="Lead Time: <? echo $buyer_lead_time_arr[$row[csf('buyer_id')]]; ?>">
							<? echo $buyer_lead_time_arr[$row[csf('buyer_id')]]; ?>
                        </td>
                        <?php
                        for($m=1; $m<=2; $m++){ 
						?>
                            <td align="right">
                            <?
                               $conf_proj_qty = $buyer_data_arr[$row[csf('buyer_id')]][$m]['po_qty'];
							   echo fn_remove_zero($conf_proj_qty,0);
                                
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
                                    $avg_rate = $buyer_data_arr[$row[csf('buyer_id')]][$m]['po_val']/$buyer_data_arr[$row[csf('buyer_id')]][$m]['po_qty'];
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
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="7" height="40" align="center"><strong>Yarn Received</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="35"><strong>SL</strong></td>
                        <td width="150" align="center"><strong>Supplier Name</strong></td>
                        <td><strong>Yarn Description</strong></td>
                        <td width="100" align="center"><strong>Received Qty</strong></td>
                        <td width="100" align="center"><strong>Com. to Com. Transfer Qty</strong></td>
                        <td width="110" align="center"><strong>Value</strong></td>
                        <td width="100"  align="center"><strong>Avg. Rate</strong></td>
                    </tr>
                    <?
						
						if($is_insert_date_active==0){
							$str_cond_a=" and a.TRANSACTION_DATE between '".$previous_date."' and '".$current_date."'";
						}

						$i=0; $tot_quantity=0; $tot_value=0;$tot_transfer_qty=0;
						
                    	$sql_rec="select 
						c.currency_id,
						d.supplier_name,
						b.product_name_details,
						sum(case when a.transaction_type=1 then a.cons_quantity else 0 end) as cons_quantity,
						sum(case when a.transaction_type=5 then a.cons_quantity else 0 end) as transfer_qty,
						sum(b.avg_rate_per_unit) as avg_rate_per_unit, 
						SUM (CASE WHEN a.cons_amount > 0 and c.exchange_rate>0 THEN (a.cons_amount / c.exchange_rate)  ELSE 0 END) as cons_amount ,
						sum(a.cons_amount) as cons_amount_tk 
						from inv_transaction a, product_details_master b,inv_receive_master c ,lib_supplier d
						where d.id=a.supplier_id and c.id=a.mst_id and b.id=a.prod_id and c.entry_form in(1,2) and a.company_id=$compid and a.item_category=1 and a.transaction_type in(1,5) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a 
						group by c.currency_id,d.supplier_name,a.prod_id,b.product_name_details				
						order by d.supplier_name,b.product_name_details asc";				
						
						 //echo $sql_rec;die;
						
						$nameArray_rec=sql_select($sql_rec);
						$tot_rows3=count($nameArray_rec);
						foreach($nameArray_rec as $row)
						{
							
							$i++;
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $row[csf('supplier_name')]; ?></td>
                        <td><? echo $row[csf('product_name_details')]; ?></td>
                        
                        <td align="right">
                        <?
                           $tot_quantity += $row[csf('cons_quantity')]; 
                            echo number_format($row[csf('cons_quantity')],2); 
                        ?>
                        </td>
                        <td align="right">
                        <?
                           $tot_transfer_qty += $row[csf('transfer_qty')]; 
                            echo number_format($row[csf('transfer_qty')],2); 
                        ?>
                        </td>
                        <td align="right">
                            <? 
                                $value= $row[csf('cons_amount')];
								echo number_format($value,2); 
								$tot_value += $value;  
                            ?>
                        </td>
                        <td align="right">
							<?
								$rate= $row[csf('cons_amount')]/$row[csf('cons_quantity')];
								echo number_format($rate,2);
							?>
                        </td>                        
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows3==0)
					{
					?>
						<tr><td colspan="7" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th>&nbsp;</th>
                            <th align="right"><? echo number_format($tot_quantity ,2)  ?></th>
                            <th align="right"><? echo number_format($tot_transfer_qty ,2)  ?></th>
                            <th align="right"><?  echo  number_format($tot_value,2);  ?></th>
                            <th align="right">&nbsp;</th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>

 
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="7" height="40" align="center"><strong>Yarn Issued</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="50"><strong>SL</strong></td>
                        <td width="220" align="center"><strong>Yarn Description</strong></td>
                        <td width="200" align="center"><strong>Purpose</strong></td>
                        <td width="125" align="center"><strong>Qty. (Kg)</strong></td>
                        <td width="125" align="center"><strong>Value</strong></td>
                        <td width="80" align="center"><strong>Avg. Rate(Tk.)</strong></td>
                        <td align="center"><strong>RTN Qty</strong></td>
                    </tr>
                    <?
						if($is_insert_date_active==0){
							$str_cond_a=" and a.TRANSACTION_DATE between '".$previous_date."' and '".$current_date."'";
						}

						$i=0; $tot_quantity=0; $tot_value=0;
						
                    	$sql_issue="select c.issue_purpose, b.product_name_details,sum(a.cons_quantity) as cons_quantity,sum(b.avg_rate_per_unit) as avg_rate_per_unit,sum(a.cons_amount) as cons_amount,sum(a.RETURN_QNTY) as RETURN_QTY from inv_transaction a, product_details_master b, inv_issue_master c where b.id=a.prod_id and c.id=a.mst_id and a.company_id=$compid and a.item_category=1 and a.transaction_type in(2,3,6) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $str_cond_a group by c.issue_purpose,a.prod_id,b.product_name_details";				
						
						//echo $sql_issue;die;
						
						$nameArray_issue=sql_select($sql_issue);
						
						$tot_rows4=count($nameArray_issue);
						
						$tot_ret_qty=0;
						foreach($nameArray_issue as $row)
						{
							
							$tot_ret_qty+=$row[RETURN_QTY];
							
							$i++;
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $row[csf('product_name_details')]; ?></td>
                        <td><? echo $yarn_issue_purpose[$row[csf('issue_purpose')]]; ?></td>
                        <td align="right">
                        <?
                           $tot_quantity += $row[csf('cons_quantity')]; 
                           echo number_format($row[csf('cons_quantity')],2); 
                        ?>
                        </td>                        
                        <td align="right">
                            <? 
                                $value= $row[csf('cons_amount')];
								echo number_format($value,2); 
								$tot_value += $value;  
                            ?>
                        </td>
                        <td align="right">
							<?
								$rate= $value/$row[csf('cons_quantity')];
								echo number_format($rate,2);
							?>
                        </td>
                        <td align="right"><?=$row[RETURN_QTY];?></td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows4==0)
					{
					?>
						<tr><td colspan="7" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
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
                            <th align="right"><?= number_format($tot_ret_qty,2);  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="7" height="40" align="center"><strong>Fabric Production (Knitting)</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td><strong>Working Company</strong></td>
                        <td width="180" align="center"><strong>Source</strong></td>
                        <td width="125" align="center"><strong>Total Prod.</strong></td>
                        <td width="125" align="center"><strong>QC Pass Qty.</strong></td>
                        <td width="125" align="center"><strong>Reject Qty.</strong></td>
                        <td width="125" align="center"><strong>Reject %</strong></td>
                    </tr>
                    <?
				$knit_sales_buyer_sammary=array();
				$knit_sales_buyer_sammary=array();
				$service_buyer_data=array();
				$knit_buyer_samary=array();
				
				if($is_insert_date_active==0){
					$str_cond_g	=" and a.product_date between '".$previous_date."' and '".$current_date."'";					
				}
				
				
				$sql_inhouse_sub_summ="select a.party_id, sum(b.product_qnty) as qntysubshift, sum(b.reject_qnty) as reject_qnty  from subcon_production_mst a, subcon_production_dtls b
                                    where a.id=b.mst_id and a.product_type=2 
                                    and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$compid $str_cond_g
                                    group by a.party_id";
									//echo $sql_inhouse_sub_summ;die;
									
									$nameArray_inhouse_subcon_summ=sql_select( $sql_inhouse_sub_summ);
									$tot_qty_sub_summ=$tot_qty_sub_summ_rej=0;
									foreach($nameArray_inhouse_subcon_summ as $rows)
									{
										$tot_qty_sub_summ+=$rows[csf('qntysubshift')];
										$tot_qty_sub_summ_rej+=$rows[csf('reject_qnty')];
									}		
		
		if($is_insert_date_active==0){
			$str_cond_f	=" and a.receive_date between '".$previous_date."' and '".$current_date."'";					
		}
		
		
		$sql_sales_prod="select c.buyer_id, 
											sum(case when b.machine_no_id>0 then b.grey_receive_qnty end ) as knit_sales_in , sum(case when b.machine_no_id>0 then b.reject_fabric_receive end) as reject_fabric_receive
											from inv_receive_master a, pro_grey_prod_entry_dtls b,fabric_sales_order_mst c where  c.id=a.booking_id and c.within_group=2 and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis=4 and a.knitting_source=1 and a.knitting_company=$compid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond_f group by c.buyer_id ";
									$result_sales_prod=sql_select( $sql_sales_prod);
							
									foreach($result_sales_prod as $row)
									{
										$knit_sales_buyer_sammary[$row[csf('buyer_id')]]['knit_sales_in']+= $row[csf('knit_sales_in')];
										$knit_sales_buyer_sammary[$row[csf('buyer_id')]]['knit_sales_in_rej']+= $row[csf('reject_fabric_receive')];
									}
									unset($result_sales_prod);		
		
		
		
	$sql_service_samary=sql_select("select a.buyer_id, sum(b.grey_receive_qnty) as service_qty,sum(b.reject_fabric_receive) as reject_fabric_receive
										from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=22 and a.receive_basis=11 and a.item_category=13 and a.id=b.mst_id  and a.knitting_company=$compid  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $str_cond_f group by a.buyer_id");
									
									foreach($sql_service_samary as $row)
									{
										$service_buyer_data[$row[csf("buyer_id")]]=$row[csf("service_qty")];
										$service_buyer_data_rej[$row[csf("buyer_id")]]=$row[csf("reject_fabric_receive")];
									}
									unset($sql_service_samary);
						//echo $sql_sample_samary;die;		
						
	$sql_sample_sam="select a.buyer_id, a.booking_no,
									sum(case when a.booking_without_order=1 and b.machine_no_id>0  then b.grey_receive_qnty end ) as sample_qty,
									sum(case when a.booking_without_order=1 and b.machine_no_id>0  then b.reject_fabric_receive end ) as reject_fabric_receive
									from inv_receive_master a, pro_grey_prod_entry_dtls b where a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and a.receive_basis!=4  and a.knitting_company=$compid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order=1 $str_cond_f group by a.buyer_id,a.booking_no ";
									$sql_sample_samary=sql_select( $sql_sample_sam);
									
									foreach($sql_sample_samary as $inf)
									{
										$booking_no=explode("-",$inf[csf('booking_no')]);
										$without_booking_no=$booking_no[1];
										if($without_booking_no=='SMN')
										{
											$knit_buyer_samary[$inf[csf('buyer_id')]]['with_out_qty']+= $inf[csf('sample_qty')];
											$knit_buyer_samary[$inf[csf('buyer_id')]]['with_out_qty_rej']+= $inf[csf('reject_fabric_receive')];
										}
										else if($without_booking_no=='SM')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_qty']+= $row[csf('sample_qty')];
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_qty_rej']+= $row[csf('reject_fabric_receive')];
										}
									}
									unset($sql_sample_samary);
									 $sql_sample_sam_with="select a.buyer_id, a.booking_no,
									sum(case when a.booking_without_order=0 and b.machine_no_id>0  then b.grey_receive_qnty end ) as with_ord_sample_qty,
									sum(case when a.booking_without_order=0 and b.machine_no_id>0  then b.reject_fabric_receive end ) as reject_fabric_receive
									from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f where c.po_breakdown_id=e.id and a.entry_form=2 and a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.knitting_source=1 and a.receive_basis!=4  and a.knitting_company=$compid and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_without_order in(0)  $str_cond_f group by a.buyer_id,a.booking_no ";
									$sql_sample_samary_with=sql_select( $sql_sample_sam_with);
							// $subcon_buyer_samary_with=array();
									foreach($sql_sample_samary_with as $row)
									{
										$booking_no=explode("-",$row[csf('booking_no')]);
										$without_booking_no=$booking_no[1];
										if($without_booking_no=='SM')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_qty']+= $row[csf('with_ord_sample_qty')];
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_qty_rej']+= $row[csf('reject_fabric_receive')];
										}
										else if($without_booking_no=='SMN')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_out_qty']+= $row[csf('with_ord_sample_qty')];
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_out_qty_rej']+= $row[csf('reject_fabric_receive')];
										}
									}
							// print_r($knit_buyer_samary);
									unset($sql_sample_samary_with);

								
						//SMN
								 $sql_qty="Select a.buyer_id,a.booking_no, 
								 sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.quantity end ) as qtyinhouse, 
								 sum(case when a.knitting_source=1 and b.machine_no_id>0 then c.reject_qty end ) as reject_qty_inhouse, 
								 sum(case when a.knitting_source=3 then c.quantity end ) as qtyoutbound, 
								 sum(case when a.knitting_source=3 then c.reject_qty end ) as reject_qty_outbound 
								 
								 
								 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c,wo_po_break_down e, wo_po_details_master f where a.item_category=13 and a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=e.id and e.job_no_mst=f.job_no and c.entry_form=2 and c.trans_type=1 and a.receive_basis!=4 and a.knitting_company=$compid   $str_cond_f and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.buyer_id,a.booking_no ";
							   //echo $sql_qty; die;
									$k=1;
									$sql_result=sql_select( $sql_qty);
									foreach($sql_result as $row)
									{
										$booking_no=explode("-",$row[csf('booking_no')]);
										$without_booking_no=$booking_no[1];
										if($without_booking_no!='SMN' || $without_booking_no!='SM')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['in_qty']+= $row[csf('qtyinhouse')];
											$knit_buyer_samary[$row[csf('buyer_id')]]['out_qty']+= $row[csf('qtyoutbound')];
										
											$knit_buyer_samary[$row[csf('buyer_id')]]['in_qty_rej']+= $row[csf('reject_qty_inhouse')];
											$knit_buyer_samary[$row[csf('buyer_id')]]['out_qty_rej']+= $row[csf('reject_qty_outbound')];
										
										
										}
										else if($without_booking_no=='SMN')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_out_qty']+=$row[csf('qtyinhouse')]+$row[csf('qtyoutbound')];
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_out_qty_rej']+=$row[csf('qtyinhouse')]+$row[csf('reject_qty_outbound')];
										}
										else if($without_booking_no=='SM')
										{
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_qty']+=$row[csf('qtyinhouse')]+$row[csf('qtyoutbound')];
											$knit_buyer_samary[$row[csf('buyer_id')]]['with_qty_rej']+=$row[csf('reject_qty_inhouse')]+$row[csf('reject_qty_outbound')];
										}

									}				
						
						
						
						
        $tot_qtyoutbound=$tot_without_ord_qty=$tot_with_ord_qty=$tot_qtyinhouse=$tot_qty_sales_summ=0;           							
        $tot_qtyoutbound_rej=$tot_without_ord_qty_rej=$tot_with_ord_qty_rej=$tot_qtyinhouse_rej=$tot_qty_sales_summ_rej=0;           							
		foreach($knit_buyer_samary as $buyer_id=>$rows)
		{
			$tot_qtyoutbound+=$rows[('out_qty')]+$service_buyer_data[$buyer_id];
			$tot_without_ord_qty+=$rows[('with_out_qty')];
			$tot_with_ord_qty+=$rows[('with_qty')];
			$tot_qtyinhouse+=$rows[('in_qty')];
			
			
			$tot_qtyoutbound_rej+=$rows[('out_qty_rej')]+$service_buyer_data_rej[$buyer_id];
			$tot_without_ord_qty_rej+=$rows[('with_out_qty_rej')];
			$tot_with_ord_qty_rej+=$rows[('with_qty_rej')];
			$tot_qtyinhouse_rej+=$rows[('in_qty_rej')];
			
		}

		foreach($knit_sales_buyer_sammary as $buyer_id=>$rows)
		{
			$tot_qty_sales_summ+=$rows[('knit_sales_in')];
			$tot_qty_sales_summ_rej+=$rows[('knit_sales_in_rej')];
		}

		
		
					
						
						
				$i=0; $sub_tot_production=0; $tot_grey_receive_qnty=0; $tot_reject_fabric_receive=0;
				$total_production_sammary=array(1=>'Inhouse (Self Order)',2=>'Outbound-Subcon',3=>'Sample With Order',4=>'Sample Without Order',5=>'Inbound Subcontract',6=>'Fabric Sales Order');						
						
						
						foreach($total_production_sammary as $type_id=>$val)
						{
							
						  
						   if($type_id==1) //Inhouse
						   {
							$tot_production_qty=$tot_qtyinhouse;
							$tot_production_qty_rej=$tot_qtyinhouse_rej;
						   }
						   else  if($type_id==2) //OutBound
						   {
							$tot_production_qty=$tot_qtyoutbound;
							$tot_production_qty_rej=$tot_qtyoutbound_rej;
						   }
						   else  if($type_id==3) //With Order
						   {
							$tot_production_qty=$tot_with_ord_qty;
							$tot_production_qty_rej=$tot_with_ord_qty_rej;
						   }
						   else  if($type_id==4) //Without Order
						   {
							$tot_production_qty=$tot_without_ord_qty;
							$tot_production_qty_rej=$tot_without_ord_qty_rej;
						   }
						   else  if($type_id==5) //SubCon Order
						   {
							$tot_production_qty=$tot_qty_sub_summ;
							$tot_production_qty_rej=$tot_qty_sub_summ_rej;
						   }
						   else  if($type_id==6) //Sales Order
						   {
							$tot_production_qty=$tot_qty_sales_summ;
							$tot_production_qty_rej=$tot_qty_sales_summ_rej;
						   }
							$companyArr=$company_library;
							//$companyArr=($row[csf('knitting_source')]==1)?$company_library:$supplier_library;
							
							$i++;
							$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $companyArr[$compid];//echo $companyArr[$row[csf('knitting_company')]]; ?></td>
                        <td><? echo $val; ?></td>
                        <td align="right">
							<?
								$tot_production = $tot_production_qty+$tot_production_qty_rej; 
								echo number_format($tot_production,2); 
								$sub_tot_production += $tot_production; 
                            ?>
                        </td>
                        <td align="right">
							<?
                               echo number_format($tot_production_qty,2); 
                               $tot_grey_receive_qnty += $tot_production_qty; 
                            ?>
                        </td>                 
                        <td align="right">
                            <? 
                                echo number_format($tot_production_qty_rej,2);
								$tot_reject_fabric_receive += $tot_production_qty_rej; 
                            ?>
                        </td>
                        <td align="right">
							<?
								$reject_percent= $tot_production_qty_rej/$tot_production;
								echo number_format($reject_percent,4);
							?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
						
						
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($sub_tot_production ,2)  ?></th>
                            <th align="right"><? echo number_format($tot_grey_receive_qnty ,2)  ?></th>
                            <th align="right"><? echo number_format($tot_reject_fabric_receive,2);  ?></th>
                            <th align="right">
							<? 
                                $tot_reject_percent= $tot_reject_fabric_receive/$sub_tot_production; 
                                echo  number_format($tot_reject_percent,4);  
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
                        <thead>
                            <tr>
                                <th colspan="10"> Dyeing Completed</th>
                            </tr>
                            <tr>
                                <th width="35">SL</th>
                                <th>Working Company</th>
                                <th>Source</th>
                                <th>RFT</th>
                                <th>Adding</th>
                                <th>Re-Process</th>
                                <th>Total Production</th>
                                <th>% of Total</th>
                            </tr>
                        </thead>
                        <tbody>
                                <?
								
								if($is_insert_date_active==0){
									$str_cond_h=" and f.process_end_date between '".$previous_date."' and '".$current_date."'";					
								}
								
								
								$date_con_smry=" and a.production_date between '$previous_date' and '$current_date'";
								$workingCompanyCondSmry=" and a.service_company=$compid"; 
								$workingCompany_name_cond2=" and a.working_company_id=$compid"; 
								
                                $sql_qty = " (select a.working_company_id,a.company_id,a.batch_no, f.batch_ext_no,f.result,f.batch_id,
                                  sum(case when f.service_source=1 then  a.batch_weight end) as batch_weight,
                                  SUM(case when f.service_source=1 then b.batch_qnty end) AS production_qty_inhouse,
                                  SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
                                  SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 then b.batch_qnty end) AS prod_qty_sample_without_order, 
                                  SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 then b.batch_qnty end) AS prod_qty_sample_with_order,
                                  SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty  
                                  from pro_batch_create_dtls b, wo_po_break_down c, wo_po_details_master d, pro_fab_subprocess f,  pro_batch_create_mst a 
                                  where f.batch_id=a.id $workingCompany_name_cond2  $str_cond_h and a.entry_form=0 and  a.id=b.mst_id and f.batch_id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and b.po_id=c.id and d.job_no=c.job_no_mst and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and  f.status_active=1 and f.is_deleted=0  and f.result=1  
                                  group by a.working_company_id,a.company_id,a.batch_no,f.batch_ext_no,f.result,f.batch_id) 
                                 union ( select a.working_company_id,a.company_id,a.batch_no, f.batch_ext_no,f.result,f.batch_id,
                                  sum(case when f.service_source=1 then  a.batch_weight end) as batch_weight,
                                  SUM(case when f.service_source=1 then b.batch_qnty end) AS production_qty_inhouse,
                                  SUM(case when f.service_source=3 then b.batch_qnty end) AS production_qty_outbound,
                                  SUM(case WHEN a.batch_against=3 and a.booking_without_order=1 then b.batch_qnty end) AS prod_qty_sample_without_order, 
                                  SUM(case WHEN a.batch_against=3 and a.booking_without_order=0 then b.batch_qnty end) AS prod_qty_sample_with_order,
                                  SUM(case WHEN b.is_sales=1 then b.batch_qnty end) AS fabric_sales_order_qty 
                                  from pro_batch_create_dtls b, pro_batch_create_mst a, pro_fab_subprocess f,wo_non_ord_samp_booking_mst h 
                                  where  h.booking_no=a.booking_no $companyCond  $workingCompany_name_cond2 and f.batch_id=a.id and f.batch_id=b.mst_id and a.entry_form=0  and a.id=b.mst_id and f.entry_form=35 and f.load_unload_id=2  and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and  f.status_active=1 and f.is_deleted=0 $str_cond_h and f.result=1 
                                  group by a.working_company_id,a.company_id,a.batch_no, f.batch_ext_no,f.result,f.batch_id ) ";
                  //echo $sql_qty;
              
                                $sql_result=sql_select( $sql_qty);
                                
								foreach($sql_result as $row)
                                {
                                    $batch_id_arr[$row[csf('batch_id')]]=$row[csf('batch_id')];

								}
								$batch_id_list_arr=array_chunk($batch_id_arr,999);
								$sql_con='';
								foreach($batch_id_list_arr as $id_process)
								{
									if($sql_con==''){$sql_con .= " and (batch_id in(".implode(",",$id_process).")";} 
									else{$sql_con .=" or batch_id in(".implode(",",$id_process).")";}
								}
								$sql_con.=")";
								$sql="select batch_id,batch_id from pro_recipe_entry_mst where status_active=1 and is_deleted=0 and entry_form=60 $sql_con";
								$adding_batch_array=return_library_array( $sql,'batch_id','batch_id');	
								
								
								
								
								$production_qty_inhouse=0;
                                $production_qty_outbound=0;
                                $prod_qty_sample_without_order=0;
                                $prod_qty_sample_with_order=0;
                                $fabric_sales_order_qty=0;
                                $rft_production_qty_inhouse=0;
                                $rft_production_qty_outbound=0;
                                $rft_prod_qty_sample_without_order=0;
                                $rft_prod_qty_sample_with_order=0;
                                $rft_fabric_sales_order_qty=0;
								
                                $batchIDs="";
                                foreach($sql_result as $row)
                                {
									
									$production_qty_inhouse+=$row[csf('production_qty_inhouse')];
                                    $production_qty_outbound+=$row[csf('production_qty_outbound')];
                                    $prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
                                    $prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
                                    $fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
									
									if($row[csf('batch_ext_no')]>0 && $row[csf('result')]==1){
										$reprocess_production_qty_inhouse+=$row[csf('production_qty_inhouse')];
										$reprocess_production_qty_outbound+=$row[csf('production_qty_outbound')];
										$reprocess_prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
										$reprocess_prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
										$reprocess_fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
									}
									
									if($adding_batch_array[$row[csf('batch_id')]]=='' and $row[csf('batch_ext_no')]<1 && $row[csf('result')]==1){
										$rft_production_qty_inhouse+=$row[csf('production_qty_inhouse')];
										$rft_production_qty_outbound+=$row[csf('production_qty_outbound')];
										$rft_prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
										$rft_prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
										$rft_fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
									}
									
									
									if($adding_batch_array[$row[csf('batch_id')]]==$row[csf('batch_id')] && $row[csf('result')]==1){
										$adding_production_qty_inhouse+=$row[csf('production_qty_inhouse')];
										$adding_production_qty_outbound+=$row[csf('production_qty_outbound')];
										$adding_prod_qty_sample_without_order+=$row[csf('prod_qty_sample_without_order')];
										$adding_prod_qty_sample_with_order+=$row[csf('prod_qty_sample_with_order')];
										$adding_fabric_sales_order_qty+=$row[csf('fabric_sales_order_qty')];
									}
									
                                }
                                
								
							/*	
								if($subcn_batch_ids!='')
                                {
									$sql_subcontact_qty = sql_select("select a.id,a.company_id,a.batch_id,a.batch_no,a.batch_ext_no,a.result,sum(b.production_qty) as production_qty_subcontact
					from pro_fab_subprocess a,pro_fab_subprocess_dtls b,pro_batch_create_mst c,pro_batch_create_dtls d  where a.id=b.mst_id and a.batch_id=c.id and c.id=d.mst_id $companyCondSmry $workingCompanyCondSmry $date_con_smry and a.entry_form=36 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.load_unload_id in(2) and a.result=1 group by a.id,a.batch_id,a.batch_no,a.company_id,a.batch_ext_no,a.result order by a.id");
				
                                
									$production_qty_subcontact=0;$rft_production_qty_subcontact=0;
									foreach($sql_subcontact_qty as $row)
									{
										$production_qty_subcontact+=$row[csf('production_qty_subcontact')];
										if($row[csf('batch_ext_no')]<1 && $row[csf('result')]==1){
											$rft_production_qty_subcontact+=$row[csf('production_qty_subcontact')];
										}
									}
                                }*/
								
								
								
							   
							   
							   
							    $k=1;$total_summary_prod_qty=0;
                                $total_production_sammary=array(1=>'Inhouse (Self Order)',3=>'Sample With Order',4=>'Sample Without Order',5=>'Inbound Subcontract',6=>'Fabric Sales Order');//2=>'Outbound-Subcon',
                                $total_prod_sammaryQty=$production_qty_inhouse+$production_qty_outbound+$prod_qty_sample_with_order+$prod_qty_sample_without_order+$production_qty_subcontact+$fabric_sales_order_qty;
                                
                                $grnd_tot_production_qty=0;
                                $grnd_total_prod_per=0;
                                
								$tot_rft=0;
								$tot_adding=0;
								$tot_reprocess=0;
								
                                foreach($total_production_sammary as $type_id=>$val)
                                {
                                   $bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
                                   if($type_id==1) //Inhouse
                                   {
                                    $tot_production_qty=$production_qty_inhouse;
	                                $rft=$rft_production_qty_inhouse;
	                                $adding=$adding_production_qty_inhouse;
	                                $reprocess=$reprocess_production_qty_inhouse;
                                   }
                                   else  if($type_id==2) //OutBound
                                   {
                                    $tot_production_qty=$production_qty_outbound;
                                    $rft=$rft_production_qty_outbound;
                                    $adding=$adding_production_qty_outbound;
                                    $reprocess=$reprocess_production_qty_outbound;
                                   }
                                   else  if($type_id==3) //With Order
                                   {
                                    $tot_production_qty=$prod_qty_sample_with_order;
                                    $rft=$rft_prod_qty_sample_with_order;
                                    $adding=$adding_prod_qty_sample_with_order;
                                    $reprocess=$reprocess_prod_qty_sample_with_order;
                                   }
                                   else  if($type_id==4) //Without Order
                                   {
                                    $tot_production_qty=$prod_qty_sample_without_order;
                                    $rft=$rft_prod_qty_sample_without_order;
                                    $adding=$adding_prod_qty_sample_without_order;
                                    $reprocess=$reprocess_prod_qty_sample_without_order;
                                   }
                                   else  if($type_id==5) //SubCon Order
                                   {
                                    $tot_production_qty=$production_qty_subcontact;
                                    $rft=$rft_production_qty_subcontact;
                                    $adding=$adding_production_qty_subcontact;
                                    $reprocess=$reprocess_production_qty_subcontact;
                                   }
                                   else  if($type_id==6) //Sales Order
                                   {
                                    $tot_production_qty=$fabric_sales_order_qty;
                                    $rft=$rft_fabric_sales_order_qty;
                                    $adding=$adding_fabric_sales_order_qty;
                                    $reprocess=$reprocess_fabric_sales_order_qty;
                                   }
                                   $total_prod_per=number_format($tot_production_qty/$total_prod_sammaryQty,6,'.','');
                                   ?>
                                   <tr bgcolor="<? echo $bgcolor; ?>">
                                    <td align="center"><? echo $k; ?></td>
                                    <td><? echo $company_library[$compid]; ?></td>
                                    <td><? echo $val; ?></td>
                                    <td align="right"><? echo $rft; ?></td>
                                    <td align="right"><? echo $adding; ?></td>
                                    <td align="right"><? echo $reprocess; ?></td>
                                    <td align="right"><? echo number_format($tot_production_qty,2,'.',''); ?></td>
                                    <td align="right"><? echo  number_format(($total_prod_per*100),4,'.',''); ?>&nbsp;</td>
                                   </tr>
                                   <?	
									$tot_rft+=$rft;
									$tot_adding+=$adding;
									$tot_reprocess+=$reprocess;
									
									$total_summary_prod_qty+=$tot_production_qty;
									$grnd_tot_production_qty+=$tot_production_qty;
									$grnd_total_prod_per+=$total_prod_per;
                                $k++;
                                }
                                ?>
                                <tr bgcolor="#CCCCCC" style="font-weight:bold;">
                                    <td colspan="3" align="right">Total</td>
                                    <td align="right"><? echo $tot_rft;?></td>
                                    <td align="right"><? echo $tot_adding;?></td>
                                    <td align="right"><? echo $tot_reprocess;?></td>
                                    <td align="right"><? echo number_format($grnd_tot_production_qty,2,'.',''); ?></td>
                                    <td align="right"><? echo number_format(($grnd_total_prod_per*100),4,'.',''); ?>&nbsp;</td>
                                </tr>
                    </tbody>
                        
                    </table>                
                
                 
            </td>
        </tr>
         
        <tr>
            <td valign="top" align="left">
                 <table width="80%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="4" height="40" align="center"><strong>Finish Fabric Production</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td><strong>Working Company</strong></td>
                        <td width="200" align="center"><strong>Source</strong></td>
                        <td width="200" align="center"><strong>Total Prod.</strong></td>
                    </tr>
                    <?
						$i=0;
						
						if($is_insert_date_active==0){
							$str_cond_b=" and a.RECEIVE_DATE between '".$previous_date."' and '".$current_date."'";					
						}
						
						
						$sql_finish="select a.knitting_company,a.knitting_source,sum(b.receive_qnty) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.company_id=$compid and a.entry_form=7 and a.knitting_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.knitting_company,a.knitting_source";				
						$nameArray_finish=sql_select($sql_finish);
						
						$tot_rows7=count($nameArray_finish);
						$sub_receive_qnty=0;
						foreach($nameArray_finish as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $company_library[$row[csf('knitting_company')]]; ?></td>
                        <td><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                        <td align="right">
							<?
                               echo number_format($row[csf('receive_qnty')],2); 
							   $sub_receive_qnty += $row[csf('receive_qnty')]; 
                            ?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows7==0)
					{
					?>
						<tr><td colspan="4" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($sub_receive_qnty ,2)  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>

        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="7" height="40" align="center"><strong>Fabric Issued to Cutting and Cutting Production</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td><strong>Working Company</strong></td>
                        <td width="200" align="center"><strong>Source</strong></td>
                        <td width="125" align="center"><strong>Fab. Issued (Kg)</strong></td>
                        <td width="125" align="center"><strong>Qty. (Pcs)</strong></td>
                        <td width="125" align="center"><strong>Reject Qty.</strong></td>
                        <td width="125" align="center"><strong>Reject %</strong></td>
                    </tr>
                    <?
						$i=0;
						//$previous_date='1-Jul-2020';
						
						$production_date_con = " and a.production_date between '".$previous_date."' and '".$current_date."'";
						$sql="select a.serving_company,a.production_source,sum(a.production_quantity) as production_quantity,sum(a.reject_qnty) as reject_qnty from pro_garments_production_mst a where a.serving_company='".$compid."' and a.production_type=1 and a.status_active=1 and a.is_deleted=0 $production_date_con group by a.serving_company,a.production_source ";//$str_cond_a
						$production_quantity_arr=sql_select($sql);
						/*foreach($production_quantity_arr as $row)
						{
							$cutting_pro_arr['pro_qty'][$row[csf('production_source')]]=$row[csf('production_quantity')];	
							$cutting_pro_arr['rej_qty'][$row[csf('production_source')]]=$row[csf('reject_qnty')];	
						}*/
						
						
						$issue_date_con	=" and a.issue_date between '".$previous_date."' and '".$current_date."'";
						$sql_fab_issue="select a.knit_dye_company,a.knit_dye_source,sum(b.issue_qnty) as issue_qnty from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.company_id =$compid and a.entry_form=18 and a.knit_dye_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $issue_date_con  group by a.knit_dye_company,a.knit_dye_source";
						//echo $sql_fab_issue;	
						// $str_cond_b	
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						foreach($nameArray_fab_issue as $row)
						{
							$fab_issue_arr[$row[csf('knit_dye_company')]][$row[csf('knit_dye_source')]]=$row[csf('issue_qnty')];	
						}
						
						
						$tot_rows8=count($nameArray_fab_issue);
						$tot_reject=$tot_issue_qnty=$tot_cutting=0;
						foreach($production_quantity_arr as $row)
						{
							
							$companyArr=$company_library;
							//$companyArr=($row[csf('knit_dye_source')]==1)?$company_library:$supplier_library;
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $companyArr[$compid];//echo $companyArr[$row[csf('knit_dye_company')]]; ?></td>
                        <td><? echo $knitting_source[$row[csf('production_source')]]; ?></td>
                        <td align="right">
							<?
							   $issue_qnty=$fab_issue_arr[$row[csf('serving_company')]][$row[csf('production_source')]];
							   echo fn_remove_zero($issue_qnty,2); 
							   $tot_issue_qnty += $issue_qnty; 
                            ?>
                        </td>
                        <td align="right">
							<?
							   $cutting =  $row[csf('production_quantity')];
							   echo fn_remove_zero($cutting,2); 
                               $tot_cutting += $cutting; 
                            ?>
                        </td>                 
                        <td align="right">
                            <?
								$reject =  $row[csf('reject_qnty')];
								echo fn_remove_zero($reject,2);
								$tot_reject += $reject; 
							?>
                        </td>
                        <td align="right">
							<?
								$reject_per= $reject/$cutting*100;
								echo fn_remove_zero($reject_per,4);
							?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows8==0)
					{
					?>
						<tr><td colspan="7" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($tot_issue_qnty ,2)  ?></th>
                            <th align="right"><?  echo  number_format($tot_cutting,2);  ?></th>
                            <th align="right">
								<?
									echo fn_remove_zero($tot_reject,2);  
								?>
                            </th>
                            <th>&nbsp;</th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>

        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="10" height="40" align="center"><strong>Sewing Completed</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td><strong>Working Company</strong></td>
                        <td width="130" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>Good Qty. (Pcs)</strong></td>
                        <td width="70" align="center"><strong>Reject Qty.</strong></td>
                        <td width="70" align="center"><strong>Alter Qty.</strong></td>
                        <td width="70" align="center"><strong>Spot Qty.</strong></td>
                        <td width="100" align="center"><strong>Total</strong></td>
                        <td width="100" align="center"><strong>FOB Value</strong></td>
                        <td width="100" align="center"><strong>Earning CM</strong></td>
                    </tr>
                    <?
								
						
						$pro_qnty=array(); $rej_qnty=array(); $alter_qnty=array();
						$spot_qnty=array(); $total_qnty=array(); $fob_val=array();
                        $tot_production_quantity=0; $tot_reject_qnty=0;
                        $tot_alter_qnty=0; $tot_spot_qnty=0; $tot_all=0; $tot_fob_val=0; $tot_earningCM=0;
						
						$production_date_con = " and a.production_date between '".$previous_date."' and '".$current_date."'";
						$sql = "select a.serving_company,c.buyer_name, a.production_quantity, a.reject_qnty, a.alter_qnty, a.spot_qnty, b.unit_price,d.cm_cost from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c,
						wo_pre_cost_dtls d
						
						 where c.job_no=d.job_no and a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.serving_company =$compid and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status in(1,2,3) $production_date_con"; //$str_cond_a
						//echo $sql;

						$sew_sql = sql_select($sql);
						foreach($sew_sql as $sew_array)
						{
							$pro_qnty[$sew_array[csf("serving_company")]][$sew_array[csf("buyer_name")]]+=$sew_array[csf("production_quantity")];
							$rej_qnty[$sew_array[csf("serving_company")]][$sew_array[csf("buyer_name")]]+=$sew_array[csf("reject_qnty")];
							$alter_qnty[$sew_array[csf("serving_company")]][$sew_array[csf("buyer_name")]]+=$sew_array[csf("alter_qnty")];
							$spot_qnty[$sew_array[csf("serving_company")]][$sew_array[csf("buyer_name")]]+=$sew_array[csf("spot_qnty")];
							$total_qnty[$sew_array[csf("serving_company")]][$sew_array[csf("buyer_name")]]+=$sew_array[csf("production_quantity")]+$sew_array[csf("reject_qnty")]+$sew_array[csf("alter_qnty")]+$sew_array[csf("spot_qnty")];
							
							$fob_val[$sew_array[csf("serving_company")]][$sew_array[csf("buyer_name")]]+=($sew_array[csf("production_quantity")]+$sew_array[csf("reject_qnty")]+$sew_array[csf("alter_qnty")]+$sew_array[csf("spot_qnty")])*$sew_array[csf("unit_price")];
							
							$earningCM_arr[$sew_array[csf("serving_company")]][$sew_array[csf("buyer_name")]]+=($sew_array[csf("production_quantity")]+$sew_array[csf("reject_qnty")]+$sew_array[csf("alter_qnty")]+$sew_array[csf("spot_qnty")])*$sew_array[csf("cm_cost")];
						}
					
					
						$i=0;
						foreach($pro_qnty as $working_com_id=>$working_com_row)
						{
							foreach($working_com_row as $buyer_id=>$row)
							{
							
							$i++;
					?>
                        <tr>
                            <td align="center"><? echo $i; ?></td>
                            <td><? echo $companyArr[$compid];//echo $company_library[$working_com_id]; ?></td>
                            <td><? echo $buyer_library[$buyer_id]; ?></td>
                            <td align="right">
                                <?
                                    echo fn_remove_zero($pro_qnty[$working_com_id][$buyer_id],2);
                                   $tot_production_quantity += $pro_qnty[$working_com_id][$buyer_id]; 
                                ?>
                            </td>
                            <td align="right">
                                <?
                                   echo fn_remove_zero($rej_qnty[$working_com_id][$buyer_id],2);
                                   $tot_reject_qnty += $rej_qnty[$working_com_id][$buyer_id]; 
                                ?>
                            </td>                 
                            <td align="right">
                                <?
                                    echo fn_remove_zero($alter_qnty[$working_com_id][$buyer_id],2);
                                    $tot_alter_qnty += $alter_qnty[$working_com_id][$buyer_id]; 
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    echo fn_remove_zero($spot_qnty[$working_com_id][$buyer_id],2);
                                    $tot_spot_qnty += $spot_qnty[$working_com_id][$buyer_id]; 
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    $total= $total_qnty[$working_com_id][$buyer_id];
                                    echo fn_remove_zero($total,2);
                                    $tot_all += $total; 
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    $fob= $fob_val[$working_com_id][$buyer_id];
                                    echo fn_remove_zero($fob,2);
                                    $tot_fob_val += $fob; 
                                ?>
                            </td>
                            <td align="right">
                                <?
                                    $earningCM= $earningCM_arr[$working_com_id][$buyer_id];
                                    echo fn_remove_zero($earningCM,2);
                                    $tot_earningCM += $earningCM; 
                                ?>
                            </td>
                        </tr>
                    	<?	
							$flag=1;
							}
						}
					?> 
                    <tr bgcolor="#EEE">
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td align="center"><b>Total</b></td>
                        <td align="right"><b><? echo number_format($tot_production_quantity,2)  ?></b></td>
                        <td align="right"><b><? echo number_format($tot_reject_qnty,2);  ?></b></td>
                        <td align="right"><b><? echo number_format($tot_alter_qnty,2); ?></b></td>
                        <td align="right"><b><? echo number_format($tot_spot_qnty,2); ?></b></td>
                        <td align="right"><b><? echo number_format($tot_all,2); ?></b></td>
                        <td align="right"><b><? echo number_format($tot_fob_val,2); ?></b></td>
                        <td align="right"><b><? echo number_format($tot_earningCM,2); ?></b></td>
                    </tr>
                    <tr>
                    	<tfoot>
                    		<th>&nbsp;</th>
                    		<th>&nbsp;</th>
                            <th>In %</th>
                            <th align="right">
								<?
									$production_quantity_per= $tot_production_quantity/$tot_all*100;
									echo number_format($production_quantity_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$reject_qnty_per= $tot_reject_qnty/$tot_all*100; 
									echo number_format($reject_qnty_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$alter_qnty_per= $tot_alter_qnty/$tot_all*100;  
									echo number_format($alter_qnty_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$spot_qnty_per= $tot_spot_qnty/$tot_all*100;   
									echo number_format($spot_qnty_per,2)  
								?>
                            </th>
                            <th align="right">
								<? 
									echo number_format($tot_all/$tot_all*100,2)  
								?>
                            </th>
                            <th align="right">
								<? 
									//echo number_format($tot_all,2)  
								?>
                            </th>
                            <td>&nbsp;</td>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="7" height="40" align="center"><strong>Garments Poly & Finishing</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td><strong>Working Company</strong></td>
                        <td width="200" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>Poly Qty (Pcs)</strong></td>
                        <td width="100" align="center"><strong>Packing Qty(Pcs)</strong></td>
                        <td width="100" align="center"><strong>Number of Carton</strong></td>
                        <td width="100" align="center"><strong>%</strong></td>                    
                    </tr>
                    <?
						
						
						$i=0;
						$production_date_con = " and a.production_date between '".$previous_date."' and '".$current_date."'";

						$final_qty=return_field_value("sum(a.production_quantity)", "pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c", "a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.serving_company=$compid and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status<>3 $production_date_con");	//$str_cond_a					
						
						
						$sql_production_sql="select a.serving_company,c.buyer_name, 
						sum(case when a.production_type=8 then  a.production_quantity end) as production_quantity,
						sum(case when a.production_type=11 then  a.production_quantity end) as poly_qty,
						sum(case when a.production_type=8 then  a.carton_qty end) as carton_qty
						
						from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.serving_company=$compid and a.production_type in(8,11) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status <> 3 $production_date_con group by a.serving_company,c.buyer_name";	//$str_cond_a
									
						//echo $sql_production_sql;
						
						$sql_production_sql_result=sql_select($sql_production_sql);
						$tot_rows10=count($sql_production_sql_result);
						
						$tot_prod_qty=0; $tot_carton_qty=0; $tot_percent_qty=0;$tot_poly_qty=0;
						foreach($sql_production_sql_result as $row)
						{
						$i++;
						?>
                        <tr>
                            <td align="center"><? echo $i; ?></td>
                            <td><? echo $companyArr[$compid]; ?></td>
                            <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                            <td align="right">
                                <?
                                  echo number_format($row[csf('poly_qty')],2); 
                                  $tot_poly_qty += $row[csf('poly_qty')]; 
                                ?>
                            </td>
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
                            <td align="right">
                                <?
                                   $tot_percent=($row[csf('production_quantity')]*100)/$final_qty;
                                   echo number_format($tot_percent,4); 
                                   $tot_percent_qty += $tot_percent; 
                                ?>
                            </td>
                        </tr>
                    	<?	
						$flag=1;
						}
					if($tot_rows10==0)
					{
					?>
						<tr><td colspan="7" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($tot_poly_qty,2); ?></th>
                            <th align="right"><? echo number_format($tot_prod_qty,2); ?></th>
                            <th align="right"><? echo number_format($tot_carton_qty,2); ?></th>
                            <th align="right"><? echo number_format($tot_percent_qty,2);?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="8" height="40" align="center"><strong>Final Inspection</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="150" align="center"><strong>Job No</strong></td>
                        <td width="200" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>Order No</strong></td> 
                        <td width="100" align="center"><strong>Order Qty (Pcs)</strong></td> 
                        <td width="100" align="center"><strong>Inspection Qty</strong></td> 
                        <td width="80" align="center"><strong>Shipment Date</strong></td>     
                        <td align="center"><strong>Inspection Status</strong></td>                        
                    </tr>
                    <?
						$i=0;
						$inspection_date_con = " and a.inspection_date between '".$previous_date."' and '".$current_date."'";

						
                    	$sql_final_ins="select a.inspection_status,c.job_no,c.buyer_name,b.po_number,a.inspection_qnty,b.shipment_date,(c.total_set_qnty*b.po_quantity) as po_quantity_pcs from pro_buyer_inspection a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.id in(SELECT MAX(id) FROM pro_buyer_inspection where inspection_status in(1,2,3) $inspection_date_con GROUP BY po_break_down_id) and a.inspection_company=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";	//$str_cond
						//echo $sql_final_ins;die;
						
						$sql_final_ins_result=sql_select($sql_final_ins);
						
						$tot_rows11=count($sql_final_ins_result);
						
						foreach($sql_final_ins_result as $row)
						{
							
							$i++;
							
							?>
							<tr>
								<td align="center"><? echo $i; ?></td>
								<td><? echo $row[csf('job_no')]; ?></td>
								<td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
								<td><? echo $row[csf('po_number')]; ?></td>
								<td align="right"><? echo $row[csf('po_quantity_pcs')]; ?></td>
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
						<tr><td colspan="8" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
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
                 		<td colspan="4" height="40" align="center"><strong>Ex-factory Done</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="400" align="center"><strong>Buyer</strong></td>
                        <td width="200" align="center"><strong>Delv. Qty. (Pcs)</strong></td>
                        <td width="200" align="center"><strong>FOB Value</strong></td>
                    </tr>
                    <?
						$ex_fac_qty=array();
						$ex_fac_val=array();
						
						$ex_factory_date_con = " and a.ex_factory_date between '".$previous_date."' and '".$current_date."'";
						$ex_sql = sql_select("select c.buyer_name,a.ex_factory_qnty,b.unit_price from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $ex_factory_date_con");//$str_cond_a
						foreach($ex_sql as $ex_array)
						{
							$ex_fac_qty[$ex_array[csf("buyer_name")]]+=$ex_array[csf("ex_factory_qnty")];
							$ex_fac_val[$ex_array[csf("buyer_name")]]+=$ex_array[csf("ex_factory_qnty")]*$ex_array[csf("unit_price")];
						}
						
						
						
                        $tot_ex_factory_qnty=0; $tot_ex_factory_val=0;
						
						$i=0;
                    	$sql_fab_issue="select c.buyer_name from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name=$compid and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $ex_factory_date_con group by c.buyer_name";//$str_cond_a				
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
                 		<td colspan="8" height="40" align="center"><strong>Full Shipment</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="400" align="center"><strong>Buyer Name</strong></td>
                        <td width="200" align="center"><strong>Job No</strong></td>
                        <td width="200" align="center"><strong>PO No</strong></td>
                        <td width="200" align="center"><strong>PO Qty (Pcs)</strong></td>
                        <td width="200" align="center"><strong>Ex Factory Qty</strong></td>
                        <td width="200" align="center"><strong>Short</strong></td>
                        <td width="200" align="center"><strong>Excess</strong></td>
                    </tr>
                    <?
						
//$previous_date='4-Aug-2018';$current_date='4-Aug-2018';						
						
					$fullExfactoryQty="select po_break_down_id,ex_factory_qnty from pro_ex_factory_mst  where po_break_down_id in(select b.po_break_down_id from pro_ex_factory_delivery_mst a, pro_ex_factory_mst b 
where a.id=b.delivery_mst_id and a.company_id=$compid and b.shiping_status = 3 and b.ex_factory_date between '".$previous_date."' and '".$current_date."' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
group by b.po_break_down_id)";
					$exfactoryPO=array();$totalExfactoryQtyArr=array();
					$fullExfArray=sql_select($fullExfactoryQty);
					foreach($fullExfArray as $rows){
						$totalExfactoryQtyArr[$rows[csf('po_break_down_id')]]+=$rows[csf('ex_factory_qnty')];	
						$exfactoryPO[$rows[csf('po_break_down_id')]]=$rows[csf('po_break_down_id')];	
					}


					$fullExfactoryPoQtySql="select c.buyer_name,c.job_no,b.po_number,b.id, sum(c.total_set_qnty*b.po_quantity) as po_quantity_pcs from wo_po_break_down b,wo_po_details_master c
 where b.job_no_mst=c.job_no and c.company_name=$compid and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.id in(".implode(',',$exfactoryPO).") group by c.buyer_name,c.job_no,b.po_number,b.id";
						
						$po_id_arr=array();
						$leftoverArray=array();
						$fullShipArray=sql_select($fullExfactoryPoQtySql);
						$tot_full_ship=count($fullShipArray);
						foreach($fullShipArray as $row){
						$leftoverArray[]=$row;
						$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td><? echo $row[csf('job_no_mst')]; ?></td>
                        <td><? echo $row[csf('po_number')]; ?></td>
                        <td align="right"><? echo $row[csf('po_quantity_pcs')]; ?></td>
                        <td align="right"><? echo $totalExfactoryQtyArr[$row[csf('id')]]; ?></td>
                        <td align="right"><? echo $row[csf('po_quantity_pcs')]-$totalExfactoryQtyArr[$row[csf('id')]]; ?></td>
                        <td align="right"><? echo $totalExfactoryQtyArr[$row[csf('id')]]-$row[csf('po_quantity_pcs')]; ?></td>
                    </tr>
                    <?
						$flag=1;
						}
					if($tot_full_ship==0)
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
        	<td>
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="8" height="40" align="center"><strong>Leftover Garments After Shipment</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td align="center" width="150"><strong>Buyer Name</strong></td>
                        <td align="center" width="130"><strong>Job No</strong></td>
                        <td align="center" width="150"><strong>Style</strong></td>
                        <td align="center" width="120"><strong>PO No</strong></td>
                        <td align="center" width="100"><strong>Sewing Qty</strong></td>
                        <td align="center" width="100"><strong>Ex-Fac Qty</strong></td>
                        <td align="center"><strong>Leftover Qty</strong></td>
                    </tr>
                    <?
$sewing_quantity_arr = return_library_array( "select po_break_down_id,sum(production_quantity) as sewing_quantity from pro_garments_production_mst where  status_active =1 and is_deleted=0 and production_type=5 and po_break_down_id in(".implode(',',$exfactoryPO).") group by po_break_down_id", "po_break_down_id", "sewing_quantity");
						
						
						$i=1;
						foreach($leftoverArray as $row){
						
						$leftover_qty=$sewing_quantity_arr[$row[csf('id')]]-$totalExfactoryQtyArr[$row[csf('id')]];
						
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td align="center"><? echo $row[csf('job_no_mst')]; ?></td>
                        <td><? echo $row[csf('style_ref_no')]; ?></td>
                        <td><? echo $row[csf('po_number')]; ?></td>
                        <td align="right"><? echo $sewing_quantity_arr[$row[csf('id')]]; ?></td>
                        <td align="right"><? echo $totalExfactoryQtyArr[$row[csf('id')]]; ?></td>
                        <td align="right"><? echo $leftover_qty; ?></td>
                    </tr>
                    <?
                    	$i++;
                    		$flag=1;
						
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
        
    </table>
<?

		$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=14 AND a.MAIL_TYPE=1 and b.mail_user_setup_id=c.id and a.company_id=$compid and c.STATUS_ACTIVE=1";
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Total Production Activities of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		$header=mailHeader();
		//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		
		if($_REQUEST['isview']==1){
			$mail_item=14;
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo $message;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		}

}



?> 
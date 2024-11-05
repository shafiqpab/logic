<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
//require_once('mailer/class.phpmailer.php');

extract($_REQUEST);
$m= base64_decode($m);





$sql="select id,team_member_name,member_contact_no from lib_mkt_team_member_info where  status_active =1 and is_deleted=0";
$data_array=sql_select($sql);
foreach( $data_array as $row )
{ 
	$dealing_merchant_arr[$row[csf("id")]]=$row[csf("team_member_name")].'<br>'.$row[csf("member_contact_no")];
}

 
$team_leader_name_arr = return_library_array( "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0", "id", "team_leader_name");
$company_library 	=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library 		=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$party_library 		=return_library_array( "select id, other_party_name from lib_other_party where  status_active=1 and is_deleted=0", "id", "other_party_name");
$supplier_library 	=return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$user_arr 			=return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$country_arr 		=return_library_array( "select id, country_name from lib_country", "id", "country_name");



//$next_date=add_date(date("Y-m-d"),1);
//$prev_date=add_date(date("Y-m-d"),-1);

	if($db_type==0)
	{
		$current_date = date("Y-m-d",strtotime($date_data));
		$previous_date= $current_date; 
		$previous_3month_date = $current_date; 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($date_data)),'','',1);
		$previous_date= $current_date;
		$previous_3month_date = $current_date; 
	}


	
	if($db_type==0){
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 23:59:59'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date." 23:59:59'";
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date." 23:59:59'";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date." 23:59:59'";
		$str_cond_d	=" and a.insert_date between '".$previous_date."' and '".$current_date." 23:59:59'";
		$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_d	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
		$str_cond_e	=" and approved_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}

	function fn_remove_zero($int,$format){
		return $int>0?number_format($int,$format):'';
		
	}

$is_insert_date=0;


//echo $previous_date;die;

//$str_cond_b = "and b.insert_date between '1-Jan-2017' and '15-Sep-2017 11:59:59 PM' ";

//echo $str_cond_b;die;

if($cp){$company_library=array($cp=>$company_library[$cp]);}



foreach($company_library as $compid=>$compname)/// Total Activities
{
	
	ob_start();
	?>
    
    <table width="920">
        <tr>
            <td valign="top" align="center">
                <strong><font size="+2">Total Activities of ( Date :<?  echo date("d-m-Y", strtotime($date_data));  ?>)</font></strong>
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
                    <tr>
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

				
					
					
					$buyer_data_arr=array();$buyer_lead_time_arr=array();$totalQty=array();$totalPoArr=array();
					$sql="select b.pub_shipment_date,b.po_received_date,b.id,b.is_confirmed,a.buyer_name,sum(a.total_set_qnty*b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name = '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b group by b.id,b.pub_shipment_date,b.po_received_date,a.buyer_name,b.is_confirmed"; //and b.id=28856
					$nameArray_mst2=sql_select($sql);
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
					
					
					
						
						
						
						$total_qnty=array(); $total_value=array(); 
						
                    	$sql_mst="select a.buyer_name as buyer_id,c.buyer_name from wo_po_details_master a, wo_po_break_down b,lib_buyer c where a.job_no=b.job_no_mst and a.buyer_name=c.id and a.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b group by a.buyer_name,c.buyer_name order by c.buyer_name";				
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
                        <td align="center" title="Lead Time: <? echo number_format($buyer_lead_time_arr[$row[csf('buyer_id')]]/count($totalPoArr[$row[csf('buyer_id')]])); ?>">
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
                 		<td colspan="5" height="40" align="center"><strong>Export LC/Sales Contract Receive</strong></td>
                 	</tr>
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="250" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>LC/SC</strong></td>
                        <td width="250" align="center"><strong>LC/SC No</strong></td>
                        <td width="200" align="center"><strong>Value</strong></td>
                    </tr>
                    <?
					
						$i=0; $tot_lc_value=0;
						
                    	$sql_lc_sc="SELECT sum(lc_value) as lc_sc_value, buyer_name, 1 as type, export_lc_no as no from com_export_lc where beneficiary_name like '$compid' and status_active=1 and is_deleted=0 $str_cond group by buyer_name,export_lc_no
						union all
						SELECT sum(contract_value) as lc_sc_value, buyer_name, 2 as type, contract_no as no from com_sales_contract where beneficiary_name like '$compid' and status_active=1 and is_deleted=0 $str_cond group by buyer_name,contract_no order by buyer_name";
									
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
                    	<tfoot>
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
                 		<td colspan="5" height="40" align="center"><strong>Back to Back Open</strong></td>
                 	</tr>
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="250" align="center"><strong>Item Category</strong></td>
                        <td width="200" align="center"><strong>Supplier</strong></td>
                        <td width="150" align="center"><strong>Value</strong></td>
                        <td width="200" align="center"><strong>LC Type</strong></td>
                    </tr>
                    <?
						$i=0;$tot_bb_value=0;
						
                    	$sql_back_back="Select supplier_id, sum(lc_value) as lc_value, item_category_id, lc_type_id from com_btb_lc_master_details where importer_id like '$compid' and status_active=1 and is_deleted=0 $str_cond group by supplier_id,item_category_id,lc_type_id";
						
						$nameArray_back_back=sql_select($sql_back_back);
						$tot_rows14=count($nameArray_back_back);
						foreach($nameArray_back_back as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $item_category[$row[csf('item_category_id')]]; ?></td>
                        <td><? echo $supplier_library[$row[csf('supplier_id')]]; ?></td>
                        <td align="right">
							<?
								$value= $row[csf('lc_value')];
								echo number_format($value,2); 
								$tot_bb_value += $value;  
							?>
                        </td>
                        <td><? echo $lc_type[$row[csf('lc_type_id')]]; ?></td>
                        
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows14==0)
					{
					?>
						<tr><td colspan="5" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th>&nbsp;</th>
                            <th align="right"><?  echo  number_format($tot_bb_value,2);  ?></th>
                            <th>&nbsp;</th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="6" height="40" align="center"><strong>Yarn Received</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50"><strong>SL</strong></td>
                        <td width="200" align="center"><strong>Supplier Name</strong></td>
                        <td width="275" align="center"><strong>Yarn Description</strong></td>
                        <td width="125" align="center"><strong>Qty. (Kg)</strong></td>
                        <td width="125" align="center"><strong>Value</strong></td>
                        <td width="125" align="center"><strong>Avg. Rate(Tk.)</strong></td>
                    </tr>
                    <?
						$i=0; $tot_quantity=0; $tot_value=0;
						
						
                    	$sql_rec="select a.supplier_id,b.product_name_details,sum(a.cons_quantity) as cons_quantity,sum(b.avg_rate_per_unit) as avg_rate_per_unit, sum(a.cons_amount) as cons_amount from inv_transaction a, product_details_master b where b.id=a.prod_id and a.company_id like '$compid' and a.item_category=1 and a.transaction_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a group by a.supplier_id,a.prod_id,b.product_name_details";				
						$nameArray_rec=sql_select($sql_rec);
						$tot_rows3=count($nameArray_rec);
						foreach($nameArray_rec as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $supplier_library[$row[csf('supplier_id')]]; ?></td>
                        <td><? echo $row[csf('product_name_details')]; ?></td>
                        
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
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows3==0)
					{
					?>
						<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot>
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
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="6" height="40" align="center"><strong>Yarn Issued</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50"><strong>SL</strong></td>
                        <td width="275" align="center"><strong>Yarn Description</strong></td>
                        <td width="200" align="center"><strong>Purpose</strong></td>
                        <td width="125" align="center"><strong>Qty. (Kg)</strong></td>
                        <td width="125" align="center"><strong>Value</strong></td>
                        <td width="125" align="center"><strong>Avg. Rate(Tk.)</strong></td>
                    </tr>
                    <?
						$i=0; $tot_quantity=0; $tot_value=0;
						
                    	$sql_issue="select c.issue_purpose, b.product_name_details,sum(a.cons_quantity) as cons_quantity,sum(b.avg_rate_per_unit) as avg_rate_per_unit,sum(a.cons_amount) as cons_amount from inv_transaction a, product_details_master b, inv_issue_master c where b.id=a.prod_id and c.id=a.mst_id and a.company_id like '$compid' and a.item_category=1 and a.transaction_type=2 and c.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $str_cond_a group by c.issue_purpose,a.prod_id,b.product_name_details";				
						$nameArray_issue=sql_select($sql_issue);
						
						$tot_rows4=count($nameArray_issue);
						
						foreach($nameArray_issue as $row)
						{
							
							$i++;
					?>
                    <tr>
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
                    	<tfoot>
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
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="6" height="40" align="center"><strong>Knitting Production</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="275" align="center"><strong>Source</strong></td>
                        <td width="200" align="center"><strong>Total Prod.</strong></td>
                        <td width="125" align="center"><strong>QC Pass Qty.</strong></td>
                        <td width="125" align="center"><strong>Reject Qty.</strong></td>
                        <td width="125" align="center"><strong>Reject %</strong></td>
                    </tr>
                    <?
						$i=0; $sub_tot_production=0; $tot_grey_receive_qnty=0; $tot_reject_fabric_receive=0;
						
                    	$sql_knit="select a.knitting_source,sum(b.grey_receive_qnty) as grey_receive_qnty,sum(b.reject_fabric_receive) as reject_fabric_receive from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.company_id like '$compid' and a.entry_form=2 and a.knitting_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.knitting_source";				
						$nameArray_knit=sql_select($sql_knit);
						
						$tot_rows5=count($nameArray_knit);
						
						foreach($nameArray_knit as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
                        <td align="right">
							<?
								$tot_production = $row[csf('grey_receive_qnty')]+$row[csf('reject_fabric_receive')]; 
                               echo number_format($tot_production,2); 
							   $sub_tot_production += $tot_production; 
                            ?>
                        </td>
                        <td align="right">
							<?
                               echo number_format($row[csf('grey_receive_qnty')],2); 
                               $tot_grey_receive_qnty += $row[csf('grey_receive_qnty')]; 
                            ?>
                        </td>                 
                        <td align="right">
                            <? 
                                echo number_format($row[csf('reject_fabric_receive')],2);
								$tot_reject_fabric_receive += $row[csf('reject_fabric_receive')]; 
                            ?>
                        </td>
                        <td align="right">
							<?
								$reject_percent= $row[csf('reject_fabric_receive')]/$tot_production;
								echo number_format($reject_percent,4);
							?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows5==0)
					{
					?>
						<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot>
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
                 <table width="60%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="3" height="40" align="center"><strong>Dyeing Completed</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="325" align="center"><strong>Source</strong></td>
                        <td width="250" align="center"><strong>Qty. (Kg)</strong></td>
                    </tr>
                    <?
						$i=0;
						
                    	$sql_dyeing="select a.load_unload_id,sum(b.batch_weight) as batch_weight from pro_fab_subprocess a, pro_batch_create_mst b where b.id=a.batch_id and a.company_id like '$compid' and a.load_unload_id=2 and a.entry_form=35 and a.result=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a group by a.load_unload_id";					
						$nameArray_dyeing=sql_select($sql_dyeing);
						
						$tot_rows6=count($nameArray_dyeing);
						
						foreach($nameArray_dyeing as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $loading_unloading[$row[csf('load_unload_id')]]; ?></td>
                        <td align="right">
							<?
                               echo number_format($row[csf('batch_weight')],2); 
							   $tot_receive_qnty += $row[csf('batch_weight')]; 
                            ?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows6==0)
					{
					?>
						<tr><td colspan="3" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($tot_receive_qnty ,2)  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        
        
        <tr>
            <td valign="top" align="left">
                 <table width="60%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="3" height="40" align="center"><strong>Finish Fabric Production</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="325" align="center"><strong>Source</strong></td>
                        <td width="250" align="center"><strong>Total Prod.</strong></td>
                    </tr>
                    <?
						$i=0;
						
                    	$sql_finish="select a.knitting_source,sum(b.receive_qnty) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.company_id like '$compid' and a.entry_form=7 and a.knitting_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.knitting_source";				
						$nameArray_finish=sql_select($sql_finish);
						
						$tot_rows7=count($nameArray_finish);
						
						foreach($nameArray_finish as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
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
						<tr><td colspan="3" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot>
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
                 		<td colspan="6" height="40" align="center"><strong>Fabric Issued to Cutting and Cutting Production</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="275" align="center"><strong>Source</strong></td>
                        <td width="200" align="center"><strong>Fab. Issued (Kg)</strong></td>
                        <td width="125" align="center"><strong>Qty. (Pcs)</strong></td>
                        <td width="125" align="center"><strong>Reject Qty.</strong></td>
                        <td width="125" align="center"><strong>Reject %</strong></td>
                    </tr>
                    <?
						$i=0;
						
                    	$sql_fab_issue="select a.knit_dye_source,sum(b.issue_qnty) as issue_qnty,a.company_id,a.knit_dye_source from inv_issue_master a,inv_finish_fabric_issue_dtls b where a.id=b.mst_id and a.company_id like '$compid' and a.entry_form=18 and a.knit_dye_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b  group by a.knit_dye_source,a.company_id";		
$nameArray_fab_issue=sql_select($sql_fab_issue);
						
						$tot_rows8=count($nameArray_fab_issue);
						
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $knitting_source[$row[csf('knit_dye_source')]]; ?></td>
                        <td align="right">
							<?
                               echo number_format($row[csf('issue_qnty')],2); 
							   $tot_issue_qnty += $row[csf('issue_qnty')]; 
                            ?>
                        </td>
                        <td align="right">
							<?
								$cutting=return_field_value("sum(a.production_quantity)", "pro_garments_production_mst a", "a.company_id='".$row[csf('company_id')]."' and a.production_source='".$row[csf('knit_dye_source')]."' and a.production_type=1  and a.status_active=1 and a.is_deleted=0 $str_cond_d");
							   
							   echo number_format($cutting,2); 
                               $tot_cutting += $cutting; 
                            ?>
                        </td>                 
                        <td align="right">
                            <?
								$reject=return_field_value("sum(a.reject_qnty)", "pro_garments_production_mst a", "a.company_id='$row[company_id]'  and a.production_source='$row[knit_dye_source]' and a.production_type=1  and a.status_active=1 and a.is_deleted=0 $str_cond_d"); 
                                echo number_format($reject,2);
								$tot_reject += $reject; 
							
							?>
                        </td>
                        <td align="right">
							<?
								$reject_per= $reject/$cutting;
								echo number_format($reject_per,4);
							?>
                        </td>
                    </tr>
                    <?	
						$flag=1;
						}
					if($tot_rows8==0)
					{
					?>
						<tr><td colspan="6" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($tot_issue_qnty ,2)  ?></th>
                            <th align="right"><?  echo  number_format($tot_cutting,2);  ?></th>
                            <th align="right">
								<?
									echo  number_format($tot_reject,2);  
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
                 		<td colspan="8" height="40" align="center"><strong>Sewing Completed</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="200" align="center"><strong>Buyer</strong></td>
                        <td width="150" align="center"><strong>Good Qty. (Pcs)</strong></td>
                        <td width="125" align="center"><strong>Reject Qty.</strong></td>
                        <td width="125" align="center"><strong>Alter Qty.</strong></td>
                        <td width="125" align="center"><strong>Spot Qty.</strong></td>
                        <td width="125" align="center"><strong>Total</strong></td>
                        <td width="125" align="center"><strong>FOB Value</strong></td>
                    </tr>
                    <?
						$pro_qnty=array();
						$rej_qnty=array();
						$alter_qnty=array();
						$spot_qnty=array();
						$total_qnty=array();
						$fob_val=array();
						
						if($is_insert_date==0){$str_cond_a=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."'";}
						$sql = "select c.buyer_name, a.production_quantity, a.reject_qnty, a.alter_qnty, a.spot_qnty, b.unit_price from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id=$compid and a.production_type=5 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status=1 $str_cond_a";

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
                    <tr>
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
									$production_quantity_per= $tot_production_quantity/$tot_all;
									echo number_format($production_quantity_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$reject_qnty_per= $tot_reject_qnty/$tot_all; 
									echo number_format($reject_qnty_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$alter_qnty_per= $tot_alter_qnty/$tot_all;  
									echo number_format($alter_qnty_per,2)  
								?>
                            </th>
                            <th align="right">
								<?
									$spot_qnty_per= $tot_spot_qnty/$tot_all;   
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
                 		<td colspan="5" height="40" align="center"><strong>Garments Finishing</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="300" align="center"><strong>Buyer</strong></td>
                        <td width="170" align="center"><strong>Qty. (Pcs)</strong></td>
                        <td width="180" align="center"><strong>Number of Carton</strong></td>
                        <td width="200" align="center"><strong>%</strong></td>                    
                    </tr>
                    <?
						$i=0;
						if($is_insert_date==0){$str_cond_a=" and a.PRODUCTION_DATE between '".$previous_date."' and '".$current_date."'";}
                    	$sql_fab_issue="select c.buyer_name, sum(a.production_quantity) as production_quantity, sum(a.carton_qty) as carton_qty from pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id=$compid and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status=1 $str_cond_a group by c.buyer_name";
						
						//echo $sql_fab_issue;die;	
									
						$nameArray_fab_issue=sql_select($sql_fab_issue);
						
						$tot_rows10=count($nameArray_fab_issue);
						
						foreach($nameArray_fab_issue as $row)
						{
							
							$i++;
							$final_qty=return_field_value("sum(a.production_quantity)", "pro_garments_production_mst a,wo_po_break_down b,wo_po_details_master c", "a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.company_id=$compid and a.production_type=8 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.shiping_status=1 $str_cond_a");
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
						<tr><td colspan="5" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($tot_prod_qty,2);  ?></th>
                            <th align="right"><? echo number_format($tot_carton_qty,2);  ?></th>
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
                 		<td colspan="7" height="40" align="center"><strong>Final Inspection</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="150" align="center"><strong>Job No</strong></td>
                        <td width="250" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>Order No</strong></td> 
                        <td width="100" align="center"><strong>Inspection Qty</strong></td> 
                        <td width="125" align="center"><strong>Shipment Date</strong></td>     
                        <td width="150" align="center"><strong>Inspection Status</strong></td>                        
                    </tr>
                    <?
						$i=0;
						
						
                    	$sql_fab_issue="select a.inspection_status,c.job_no,c.buyer_name,b.po_number,a.inspection_qnty,b.shipment_date from pro_buyer_inspection a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.id in(SELECT MAX(id) FROM pro_buyer_inspection where inspection_status in(1,2,3) $str_cond GROUP BY po_break_down_id) and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";	
						
						
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
                 		<td colspan="4" height="40" align="center"><strong>Ex-factory Done</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="400" align="center"><strong>Buyer</strong></td>
                        <td width="200" align="center"><strong>Delv. Qty. (Pcs)</strong></td>
                        <td width="200" align="center"><strong>FOB Value</strong></td>
                    </tr>
                    <?
						$ex_fac_qty=array();
						$ex_fac_val=array();
						
						$ex_sql = sql_select("select c.buyer_name,a.ex_factory_qnty,b.unit_price from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $str_cond_a");
						foreach($ex_sql as $ex_array)
						{
							$ex_fac_qty[$ex_array[csf("buyer_name")]]+=$ex_array[csf("ex_factory_qnty")];
							$ex_fac_val[$ex_array[csf("buyer_name")]]+=$ex_array[csf("ex_factory_qnty")]*$ex_array[csf("unit_price")];
						}
						
						
						$i=0;
                    	$sql_fab_issue="select c.buyer_name from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status in(1,2,3) and c.is_deleted=0 and c.status_active=1 $str_cond_a group by c.buyer_name";				
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
                    	<tfoot>
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($tot_ex_factory_qnty,2);  ?></th>
                            <th align="right"><? echo number_format($tot_ex_factory_val,2);  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>
        <!-- ADD HERE EX-Factory Completed Yesterday (Buyer Name,Job No, PO No) --->
        <!-- This part add by Reza start---------------- -->
        <tr>
        	<td>
                 <table width="70%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="4" height="40" align="center"><strong>Full Shipment</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="400" align="center"><strong>Buyer Name</strong></td>
                        <td width="200" align="center"><strong>Job No</strong></td>
                        <td width="200" align="center"><strong>PO No</strong></td>
                    </tr>
                    <?
						$sql_full_ship="select b.job_no_mst,b.po_number,c.buyer_name from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.shiping_status = 3 and b.shiping_status=3 and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $str_cond_d group by b.job_no_mst,b.po_number,c.buyer_name";
						
						
						$fullShipArray=sql_select($sql_full_ship);
						$tot_full_ship=count($fullShipArray);
						foreach($fullShipArray as $row){
						$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                        <td><? echo $row[csf('job_no_mst')]; ?></td>
                        <td><? echo $row[csf('po_number')]; ?></td>
                    </tr>
                    <?
						$flag=1;
						}
					if($tot_full_ship==0)
					{
					?>
						<tr><td colspan="4" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?>
                    
                </table>
           </td>
        </tr>
        <!-- full shipment end ------------------------->
        <tr>
        	<td>
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="8" height="40" align="center"><strong>Leftover After Shipment</strong></td>
                 	</tr>
                    
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td align="center" width="150"><strong>Buyer Name</strong></td>
                        <td align="center" width="130"><strong>Job No</strong></td>
                        <td align="center" width="150"><strong>Style</strong></td>
                        <td align="center" width="120"><strong>PO No</strong></td>
                        <td align="center" width="100"><strong>Fin Qty</strong></td>
                        <td align="center" width="100"><strong>Ex-Fac Qty</strong></td>
                        <td align="center"><strong>Leftover Qty</strong></td>
                    </tr>
                    <?
					//$str_cond_d=" and a.insert_date between '01-Feb-2015' and '01-Mar-2015 11:59:59 PM'";
                    	$sql_leftover="select sum(a.ex_factory_qnty) as ex_factory_qnty,b.job_no_mst,b.po_number,c.buyer_name,c.style_ref_no,sum(d.production_quantity) as finish_quantity from pro_ex_factory_mst a,wo_po_break_down b,wo_po_details_master c,pro_garments_production_mst d where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and a.po_break_down_id=d.po_break_down_id and a.shiping_status = 3 and d.production_type=8 and b.shiping_status=3 and c.company_name like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=3 and c.is_deleted=0 and c.status_active=1 $str_cond_d group by b.job_no_mst,b.po_number,c.buyer_name,style_ref_no";
						
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
        <!-- This part add by Reza end---------------- -->
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="5" height="40" align="center"><strong>PC Received</strong></td>
                 	</tr>
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="100" align="center"><strong>LC/SC</strong></td>
                        <td width="250" align="center"><strong>LC/SC No</strong></td>
                        <td width="250" align="center"><strong>Loan No</strong></td>
                        <td width="200" align="center"><strong>Amount</strong></td>
                    </tr>
                    <?
						$i=0; $tot_loan_amount=0;
						
                    	$sql_pre_export="select c.export_type,c.lc_sc_id,max(b.loan_number) as loan_number,sum(c.amount) loan_amount from com_pre_export_finance_mst a, com_pre_export_finance_dtls b, com_pre_export_lc_wise_dtls c where a.id=b.mst_id and b.id=c.pre_export_dtls_id and a.beneficiary_id like '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by c.export_type, c.lc_sc_id";
						
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
                    	<tfoot>
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
                 		<td colspan="6" height="40" align="center"><strong>Export Proceed Realized</strong></td>
                 	</tr>
                    <tr>
                    	<td width="50" align="center"><strong>SL</strong></td>
                        <td width="200" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>LC/SC</strong></td>
                        <td width="150" align="center"><strong>LC/SC No</strong></td>
                        <td width="150" align="center"><strong>Realized</strong></td>
                        <td width="200" align="center"><strong>Short Realized</strong></td>
                    </tr>
                    <?
						$i=0; $tot_realized=0; $tot_short_realized=0;
						
                    	$sql_realization_invoice="select a.buyer_id,c.is_lc,c.lc_sc_id,b.type,sum(b.document_currency) as tot_document_currency from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_invoice_ship_mst c where a.id=b.mst_id and a.invoice_bill_id=c.id and a.benificiary_id like '$compid' and a.is_invoice_bill=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.buyer_id,c.is_lc,c.lc_sc_id,b.type";
						
						
						
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
						$sql_realization_bill="select a.buyer_id,c.is_lc,c.lc_sc_id,b.type,sum(b.document_currency) as tot_document_currency from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_invo c where a.id=b.mst_id and a.invoice_bill_id=c.doc_submission_mst_id and a.benificiary_id like '$compid' and a.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b group by a.buyer_id,c.is_lc,c.lc_sc_id,b.type";
						
						
						
						
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
                    	<tfoot>
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

		echo $message;
}



?> 
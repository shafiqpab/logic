<?php
date_default_timezone_set("Asia/Dhaka");


include('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
include('setting/mail_setting.php');



// $sql="select id,team_member_name,member_contact_no from lib_mkt_team_member_info where  status_active =1 and is_deleted=0";
// $data_array=sql_select($sql);
// foreach( $data_array as $row )
// { 
// 	$dealing_merchant_arr[$row[csf("id")]]=$row[csf("team_member_name")].'<br>'.$row[csf("member_contact_no")];
// }

 
//$team_leader_name_arr = return_library_array( "select id,team_leader_name from lib_marketing_team where  status_active =1 and is_deleted=0", "id", "team_leader_name");
$company_library 	=return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 order by  id asc", "id", "company_name");
$buyer_library 		=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
//$party_library 		=return_library_array( "select id, other_party_name from lib_other_party where  status_active=1 and is_deleted=0", "id", "other_party_name");
$supplier_library 	=return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
//$user_arr 			=return_library_array( "select id,user_full_name from user_passwd where valid=1","id","user_full_name");
//$country_arr 		=return_library_array( "select id, country_name from lib_country", "id", "country_name");



	if($db_type==0)
	{
		$current_date = date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0)));
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))); 
		$previous_3month_date = date('Y-m-d H:i:s', strtotime('-92 day', strtotime($current_date))); 
	}
	else
	{
		$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s",time()),0))),'','',1);
		if($_REQUEST['view_date']){
			$current_date = change_date_format(date("Y-m-d H:i:s",strtotime($_REQUEST['view_date'])),'','',1);
		}
		$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
		$previous_3month_date = change_date_format(date('Y-m-d H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 
	}
	// echo $current_date."=>".$previous_date;die;
// $current_date='22-Aug-2021';
// $previous_date='21-Aug-2021';
	
	if($db_type==0){
		$str_cond	=" and insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_a	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_b	=" and b.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_c	=" and c.insert_date between '".$previous_date."' and '".$current_date."'";
		$str_cond_d	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
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

//$current_date='18-Nov-2019';
//$previous_date='18-Nov-2019';

$company_library=array(20=>$company_library[20]);
foreach($company_library as $compid=>$compname)/// Total Activities
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
					//echo $str_cond_b=" and b.po_received_date between '$previous_date' and '$current_date'";
					
					
					if($is_insert_date==0){$str_cond_b=" and b.po_received_date between '$previous_date' and '$current_date'";}
					
					$sql="select b.pub_shipment_date,b.po_received_date,b.id,b.is_confirmed,a.buyer_name,sum(a.total_set_qnty*b.po_quantity) as po_quantity, sum(b.po_total_price) as po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name = '$compid' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and b.shiping_status=1 $str_cond_b group by b.id,b.pub_shipment_date,b.po_received_date,a.buyer_name,b.is_confirmed"; //and b.id=28856
					
					
					
					$nameArray_mst2=sql_select($sql);
					$tot_rows2=count($nameArray_lc_sc);
					$totalQty=array();$buyer_lead_time_arr=array();$buyer_data_arr=array();
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
							<? echo $buyer_lead_time_arr[$row[csf('buyer_id')]]; ?>
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
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="9" height="30" align="center"><strong>Yarn Received</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30"><strong>SL</strong></td>
                        <td width="130" align="center"><strong>Supplier Name</strong></td>
                        <td width="220" align="center"><strong>Yarn Description</strong></td>
                        <td width="100" align="center"><strong>Qty. (Kg)</strong></td>
                        <td width="60" align="center"><strong>Receive Currency</strong></td>
                        <td width="80" align="center"><strong>Avg. Rate</strong></td>
                        <td width="110" align="center"><strong>Amount</strong></td>
                        <td width="80" align="center"><strong>Avg. Rate(Tk.)</strong></td>
                        <td align="center"><strong>Amount(Tk.)</strong></td>
                    </tr>
                    <?
						$i=0; $tot_quantity=0; $tot_value=0;$tot_value_tk=0;
						
					if($is_insert_date==0){
						$str_cond_a=" and a.transaction_date between '$previous_date' and '$current_date'";
					}
						
						
                    	$sql_rec="select 
						c.currency_id,
						d.supplier_name,
						b.product_name_details,
						sum(a.cons_quantity) as cons_quantity,
						sum(b.avg_rate_per_unit) as avg_rate_per_unit, 
						sum(a.cons_amount/c.exchange_rate) as cons_amount ,
						sum(a.cons_amount) as cons_amount_tk 
						
						
						from inv_transaction a, product_details_master b,inv_receive_master c ,lib_supplier d
						where d.id=a.supplier_id and c.id=a.mst_id and b.id=a.prod_id and c.entry_form=1 and a.company_id=$compid and a.item_category=1 and a.transaction_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_a 
						group by c.currency_id,d.supplier_name,a.prod_id,b.product_name_details				
						order by d.supplier_name,b.product_name_details asc";				
						
						 //echo $sql_rec;
						
						$nameArray_rec=sql_select($sql_rec);
						$tot_rows3=count($nameArray_rec);
						foreach($nameArray_rec as $row)
						{
							
							$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $row[csf('supplier_name')]; ?></td>
                        <td><? echo $row[csf('product_name_details')]; ?></td>
                        
                        <td align="right">
                        <?
                           $tot_quantity += $row[csf('cons_quantity')]; 
                            echo number_format($row[csf('cons_quantity')],2); 
                        ?>
                        </td>
                        <td align="center"><? echo $currency[$row[csf('currency_id')]]; ?></td>                        
                        <td align="right">
							<?
								$rate= $row[csf('cons_amount')]/$row[csf('cons_quantity')];
								echo number_format($rate,2);
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
								$rate= $row[csf('cons_amount_tk')]/$row[csf('cons_quantity')];
								echo number_format($rate,2);
							?>
                        </td>
                        <td align="right">
                            <? 
                                $value_tk= $row[csf('cons_amount_tk')];
								echo number_format($value_tk,2); 
								$tot_value_tk += $value_tk;  
                            ?>
                        
                        </td>                        
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
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th>&nbsp;</th>
                            <th align="right"><? echo number_format($tot_quantity ,2)  ?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><?  echo  number_format($tot_value,2);  ?></th>
                            <th align="right">&nbsp;</th>
                            <th align="right"><?  echo  number_format($tot_value_tk,2);  ?></th>
                        </tfoot>
                    </tr>
                 </table>
            </td>
        </tr>

        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="6" height="30" align="center"><strong>Yarn Issued</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30"><strong>SL</strong></td>
                        <td align="center"><strong>Yarn Description</strong></td>
                        <td width="200" align="center"><strong>Purpose</strong></td>
                        <td width="125" align="center"><strong>Qty. (Kg)</strong></td>
                        <td width="125" align="center"><strong>Value</strong></td>
                        <td width="125" align="center"><strong>Avg. Rate(Tk.)</strong></td>
                    </tr>
                    <?
						
					if($is_insert_date==0){
						$str_cond_a=" and a.transaction_date between '$previous_date' and '$current_date'";
					}
						
						
						$i=0; $tot_quantity=0; $tot_value=0;
						
                    	$sql_issue="select c.issue_purpose, b.product_name_details,sum(a.cons_quantity) as cons_quantity,sum(b.avg_rate_per_unit) as avg_rate_per_unit,sum(a.cons_amount) as cons_amount from inv_transaction a, product_details_master b, inv_issue_master c where b.id=a.prod_id and c.id=a.mst_id and a.company_id=$compid and a.item_category=1 and a.transaction_type=2 and c.entry_form=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $str_cond_a group by c.issue_purpose,a.prod_id,b.product_name_details";				
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
                 <table width="100%" cellpadding="0" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="11" height="30" align="center"><strong>Knitting Production</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td align="center"><strong>Buyer</strong></td>
                        <td width="90" align="center"><strong>In-house Self Order</strong></td>
                        <td width="90" align="center"><strong>In-house Sub-con Order</strong></td>
                        <td width="90" align="center"><strong>Out Side Self Order</strong></td>
                        <td width="90" align="center"><strong>Total In-house</strong></td>
                        <td width="90" align="center"><strong>Total Self Order</strong></td>
                        <td width="90" align="center"><strong>QC Pass Qty.</strong></td>
                        <td width="70" align="center"><strong>Reject Qty.</strong></td>
                        <td width="90" align="center"><strong>Total Production</strong></td>
                        <td width="50" align="center"><strong>Reject %</strong></td>
                    </tr>
                    <?
						
					if($is_insert_date==0){
						$str_cond_a_rec_date=" and a.receive_date between '$previous_date' and '$current_date'";
						$str_cond_b='';
					}
						
						$i=0; $sub_tot_production=0; $tot_grey_receive_qnty=0; $tot_reject_fabric_receive=0;
						
                    	$sql_knit="select a.buyer_id,a.knitting_source,sum(b.grey_receive_qnty) as grey_receive_qnty,sum(b.reject_fabric_receive) as reject_fabric_receive from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.company_id=$compid and a.entry_form=2 and a.knitting_source in(1,3) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b $str_cond_a_rec_date group by a.buyer_id,a.knitting_source";		
						$nameArray_knit=sql_select($sql_knit);
						$tot_rows22=count($nameArray_knit);
						$knitDataArr=array();$knitRejDataArr=array();
						foreach($nameArray_knit as $row)
						{
							$knitDataArr[$row[csf('buyer_id')]][$row[csf('knitting_source')]]+=$row[csf('grey_receive_qnty')];
							$knitRejDataArr[$row[csf('buyer_id')]][$row[csf('knitting_source')]]+=$row[csf('reject_fabric_receive')];
						}
						
                    	
					if($is_insert_date==0){
						$str_cond_a_pro_date=" and a.product_date between '$previous_date' and '$current_date'";
						$str_cond_b='';
					}
						
						$sql_knit_sub="select a.party_id as buyer_id,a.knitting_source,sum(b.product_qnty) as product_qnty,sum(b.reject_qnty) as reject_qnty from subcon_production_mst a, subcon_production_dtls b where a.id=b.mst_id and a.company_id=$compid and a.knitting_source=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $str_cond_b $str_cond_a_pro_date group by a.party_id,a.knitting_source";		
						$nameArray_knit_sub=sql_select($sql_knit_sub);
						
						$knitSubDataArr=array();$knitSubRejDataArr=array();
						
						foreach($nameArray_knit_sub as $row)
						{
							$knitSubDataArr[$row[csf('buyer_id')]][$row[csf('knitting_source')]]+=$row[csf('product_qnty')];
							$knitSubRejDataArr[$row[csf('buyer_id')]][$row[csf('knitting_source')]]+=$row[csf('reject_qnty')];
						}
						
						
						
						$total_in_house_self_order=0;$total_in_house_sub_con_order=0;$total_out_side_self_order=0;	
						$total_in_house_qty=0;$total_self_qty=0;$total_qc_pass_qty=0;$total_rej_qty=0;	
						$total_production_qty=0;
						
						foreach($knitDataArr as $buyer_id=>$row)
						{
							$buyer_total_in_house_qty=$knitDataArr[$buyer_id][1]+$knitSubDataArr[$buyer_id][3];
							$buyer_total_self_qty=$knitDataArr[$buyer_id][1]+$knitDataArr[$buyer_id][3];	
							$buyer_total_qc_pass_qty=$knitDataArr[$buyer_id][1]+$knitDataArr[$buyer_id][3]+$knitSubDataArr[$buyer_id][1];
							$buyer_total_rej_qty=$knitRejDataArr[$buyer_id][1]+$knitRejDataArr[$buyer_id][3]+$knitSubRejDataArr[$buyer_id][1];	
							$buyer_total_production_qty=$buyer_total_qc_pass_qty+$buyer_total_rej_qty;
							$buyer_raj_parcent=($buyer_total_rej_qty/$buyer_total_production_qty)*100;
							

							$total_in_house_self_order+=$knitDataArr[$buyer_id][1];	
							$total_in_house_sub_con_order+=$knitSubDataArr[$buyer_id][1];	
							$total_out_side_self_order+=$knitDataArr[$buyer_id][3];	
							$total_in_house_qty+=$buyer_total_in_house_qty;	
							$total_self_qty+=$buyer_total_self_qty;	
							$total_qc_pass_qty+=$buyer_total_qc_pass_qty;	
							$total_rej_qty+=$buyer_total_rej_qty;	
							$total_production_qty+=$buyer_total_production_qty;

						$i++;
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><? echo $buyer_library[$buyer_id]; ?></td>
                        <td align="right"><? echo number_format($knitDataArr[$buyer_id][1]); ?></td>
                        <td align="right"><? echo number_format($knitSubDataArr[$buyer_id][1]); ?></td>
                        <td align="right"><? echo number_format($knitDataArr[$buyer_id][3]); ?></td>
                        <td align="right"><? echo number_format($buyer_total_in_house_qty); ?></td>
                        <td align="right"><? echo number_format($buyer_total_self_qty); ?></td>
                        <td align="right"><? echo number_format($buyer_total_qc_pass_qty); ?></td>
                        <td align="right"><? echo number_format($buyer_total_rej_qty); ?></td>
                        <td align="right"><? echo number_format($buyer_total_production_qty); ?></td>
                        <td align="right"><? echo number_format($buyer_raj_parcent,4); ?></td>
                    </tr>
                    <?	
						$flag=1;
						}
						if($tot_rows22==0)
					{
					?>
						<tr><td colspan="11" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
					<?	
					}
					?> 
                    <tr>
                    	<tfoot bgcolor="#EEE">
                    		<th>&nbsp;</th>
                            <th>Total</th>
                            <th align="right"><? echo number_format($total_in_house_self_order);?></th>
                            <th align="right"><? echo number_format($total_in_house_sub_con_order);?></th>
                            <th align="right"><? echo number_format($total_out_side_self_order); ?></th>
                            <th align="right"><? echo number_format($total_in_house_qty); ?></th>
                            <th align="right"><? echo number_format($total_self_qty); ?></th>
                            <th align="right"><? echo number_format($total_qc_pass_qty); ?></th>
                            <th align="right"><? echo number_format($total_rej_qty); ?></th>
                            <th align="right"><? echo number_format($total_production_qty); ?></th>
                            <th align="right">
								<? 
									$tot_reject_percent= ($total_rej_qty/$total_production_qty)*100; 
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
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="7" height="30" align="center"><strong>Inspection</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="250" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>Inspection Qty</strong></td> 
                        <td width="125" align="center"><strong>QC Pass Qty.</strong></td>     
                        <td width="150" align="center"><strong>Reject Qty.</strong></td>                        
                        <td width="150" align="center"><strong>Alter Qty</strong></td>                        
                    </tr>
                    <?
						$i=0;
						
						if($is_insert_date==0){
							$str_cond_c="and b.CUTTING_QC_DATE between '$previous_date' and '$current_date'";
						}
                    	
						$sql_insfaction="select a.BUYER_NAME,sum(c.BUNDLE_QTY) as BUNDLE_QTY,sum(c.QC_PASS_QTY) as QC_PASS_QTY,sum(c.REJECT_QTY) as REJECT_QTY,sum(c.DEFECT_QTY) DEFECT_QTY from WO_PO_DETAILS_MASTER a,PRO_GMTS_CUTTING_QC_MST b, PRO_GMTS_CUTTING_QC_DTLS c where a.JOB_NO=b.JOB_NO and b.id = c.mst_id and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and b.COMPANY_ID=$compid $str_cond_c group by a.BUYER_NAME";

                        //echo $sql_insfaction_sql;die;
						
						$sql_insfaction_res=sql_select($sql_insfaction);
						$tot_rows11=count($sql_insfaction_res);
						foreach($sql_insfaction_res as $row)
						{
							
							$i++;
							
					?>
                    <tr>
                    	<td align="center"><? echo $i; ?></td>
                        <td><?= $buyer_library[$row['BUYER_NAME']]; ?></td>
                        <td><?= $row['BUNDLE_QTY']; ?></td>
                        <td align="right"><?= $row['QC_PASS_QTY']; ?></td>
                        <td align="right"><?= $row['REJECT_QTY']; ?></td>
                        <td align="right"><?= $row['DEFECT_QTY']; ?></td>
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
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="4" height="30" align="center"><strong>Wash Done</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="250" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>In-house Self Order</strong></td> 
                        <td width="125" align="center"><strong>Out Side Self Order</strong></td>                         
                    </tr>
                    <?
						$i=0;
						
						if($is_insert_date==0){
							$str_cond_b="and b.PRODUCTION_DATE between '$previous_date' and '$current_date'";
						}
                    	
						$sql_wash="select 1 as PRODUCTION_SOURCE,c.BUYER_NAME,b.QCPASS_QTY AS PRODUCTION_QNTY from SUBCON_EMBEL_PRODUCTION_MST a, SUBCON_EMBEL_PRODUCTION_DTLS b,WO_PO_DETAILS_MASTER c where a.id=b.mst_id and b.job_no=c.job_no and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 $str_cond_b  and a.COMPANY_ID=$compid
						union all
						SELECT b.PRODUCTION_SOURCE,c.BUYER_NAME,a.PRODUCTION_QNTY FROM pro_garments_production_mst b, pro_garments_production_dtls a,wo_po_details_master c, wo_po_break_down d  WHERE b.id = a.mst_id and c.id=d.job_id and d.id=b.PO_BREAK_DOWN_ID and  b.production_type = 3 and a.production_type = 3 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0  AND d.status_active = 1 AND d.is_deleted = 0  $str_cond_b and b.SERVING_COMPANY=$compid";
						//echo $sql_wash;die;
						$sql_wash_res=sql_select($sql_wash);
						$tot_rows11=count($sql_wash_res);
						$dataWashArr=array();
						foreach($sql_wash_res as $row)
						{
							$dataWashArr[$row['BUYER_NAME']][$row['PRODUCTION_SOURCE']]+=$row['PRODUCTION_QNTY'];
						}

						foreach($dataWashArr as $buyer_id=>$row)
						{
							$i++;
						?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td><?= $buyer_library[$buyer_id]; ?></td>
							<td align="right"><?= $row[1]; ?></td>
							<td align="right"><?= $row[3]; ?></td>
						</tr>
						<?	
						$flag=1;
						}
					if($tot_rows11==0)
					{
					?>
						<tr><td colspan="4" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
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
                 		<td colspan="4" height="30" align="center"><strong>Iron Complete</strong></td>
                 	</tr>
                    
                    <tr bgcolor="#EEE">
                    	<td width="30" align="center"><strong>SL</strong></td>
                        <td width="250" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>In-house Self Order</strong></td> 
                        <td width="125" align="center"><strong>Out Side Self Order</strong></td>                         
                    </tr>
                    <?
						$i=0;
						
						if($is_insert_date==0){
							$str_cond_b="and b.PRODUCTION_DATE between '$previous_date' and '$current_date'";
						}
                    	
						$sql_iron="SELECT b.PRODUCTION_SOURCE,c.BUYER_NAME,a.PRODUCTION_QNTY FROM pro_garments_production_mst b, pro_garments_production_dtls a,wo_po_details_master c, wo_po_break_down d  WHERE b.id = a.mst_id and c.id=d.job_id and d.id=b.PO_BREAK_DOWN_ID and  b.production_type = 7 and a.production_type = 7 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0  AND d.status_active = 1 AND d.is_deleted = 0  $str_cond_b and b.SERVING_COMPANY=$compid";
						//echo $sql_wash;die;
						$sql_iron_res=sql_select($sql_iron);
						$tot_rows11=count($sql_iron_res);
						$dataIronArr=array();
						foreach($sql_iron_res as $row)
						{
							$dataIronArr[$row['BUYER_NAME']][$row['PRODUCTION_SOURCE']]+=$row['PRODUCTION_QNTY'];
						}

						foreach($dataIronArr as $buyer_id=>$row)
						{
							$i++;
						?>
						<tr>
							<td align="center"><? echo $i; ?></td>
							<td><?= $buyer_library[$buyer_id]; ?></td>
							<td align="right"><?= $row[1]; ?></td>
							<td align="right"><?= $row[3]; ?></td>
						</tr>
						<?	
						$flag=1;
						}
					if($tot_rows11==0)
					{
					?>
						<tr><td colspan="4" align="center"><font size="+1"; color="#FF0000"><strong>NO ENTRY FOUND</strong></font></td></tr>
					
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
   
    </table>
<?
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();

		$mail_item=113;
		$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id=$compid and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		
		
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Total Activities[Sweater] of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
		$header=mailHeader();
		//echo $to;die;
		

		if($_REQUEST['isview']==1){
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo  $message;
		}
		else{
			if($to!="" && $flag==1){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		}

}



?> 
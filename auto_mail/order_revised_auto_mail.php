<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');


$user_library=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
//$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
$bank_arr=return_library_array( "select id, bank_name from lib_bank where is_deleted=0 and status_active=1",'id','bank_name');
$country_arr=return_library_array( "select id, country_name from lib_country where is_deleted=0 and status_active=1",'id','country_name');



$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),1))),'','',1);
$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),2))),'','',1);
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
 
	
//$prev_date="1-Jan-2016";	


$flag=0;
foreach($company_library as $compid=>$compname)/// Order Received
{



ob_start();	
	?>
    
    <table width="1750">
        <tr>
            <td valign="top" align="center">
                <h2> Company Name: <? echo $compname; ?></h2>
            </td>
        </tr>
        <tr>
            <td valign="top" align="left">
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="22" align="center"><strong>P.O Qunatity Decrease/Increase Status</strong></td>
                 	</tr>
                    
                 	<tr>
                 		<td colspan="22" bgcolor="#006600"><strong>Confirmed Order Revision Status</strong></td>
                 	</tr>
                    
                    
                    <tr>
                        <td width="30" align="center"><strong>SL</strong></td>
						<td  width="80" align="center"><strong>Sales Con NO</strong></td>
						<td  width="70" align="center"><strong>Lien Bank</strong></td>
						<td  width="70" align="center"><strong>Bank file No</strong></td>
						
                        <td  width="70" align="center"><strong>Job No.</strong></td>
                        <td width="100" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>Style</strong></td>
                        <td width="80" align="center"><strong>Particulars</strong></td>
                        <td width="80" align="center"><strong>Ord. Status</strong></td>
                        <td width="110" align="center"><strong>PO No</strong></td>
                        <td width="90" align="center"><strong>PO Rcv Date</strong></td>
                        <td width="90" align="center"><strong>Fac. Rcv Date</strong></td>
                        <td width="80" align="center"><strong>Ship Date</strong></td>

						<td width="80" align="center"><strong>Color</strong></td>
						<td width="80" align="center"><strong>Country</strong></td>
						
						<td width="90" align="center"><strong>Order Qty</strong></td>

						<td width="50" align="center"><strong>FOB</strong></td>
						<td width="50" align="center"><strong>SMV</strong></td>
						<td width="50" align="center"><strong>CM</strong></td>
		

						
                        
                        <td width="50" align="center"><strong>Rate</strong></td>
                        <td width="110" align="center"><strong>Order Value</strong></td>
                        <td align="center"><strong>Update By</strong></td>
                    </tr>
                    <?
                     
                   $sql = "
				   select 
					   b.ID, 
					   a.JOB_NO, 
					   a.buyer_name,
					   b.updated_by, 
					   b.po_number, 
					   b.po_quantity, 
					   b.pub_shipment_date as cur_ship_date, 
					   b.unit_price, 
					   a.dealing_marchant, 
					   c.shipment_date as pre_ship_date, 
					   c.previous_po_qty, 
					   a.style_ref_no, 
					   a.SET_SMV,
					   b.po_received_date as curr_rcv_date, 
					   b.factory_received_date, 
					   c.po_no, 
					   c.po_received_date as pre_rcv_date, 
					   c.shipment_date as pre_ship_date,
					   c.order_status as pre_order_status,
					   c.fac_receive_date as pre_factory_received_date,
					   b.is_confirmed as order_status,
					   c.avg_price,
					   c.prev_update_by as pre_update_by 
				   from 
					   wo_po_details_master a, 
					   wo_po_break_down b, 
					   wo_po_update_log c 
				   where 
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.po_quantity!=c.previous_po_qty
						   and b.is_confirmed=1
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.unit_price!=c.avg_price
						   and b.is_confirmed=1
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.is_confirmed!=c.order_status
						   and b.is_confirmed=1
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.po_number!=c.po_no
						   and b.is_confirmed=1
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.po_received_date!=c.po_received_date
						   and b.is_confirmed=1
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.pub_shipment_date!=c.shipment_date
						   and b.is_confirmed=1
					   )
				   order by a.dealing_marchant,a.job_no"; // 
				   
					$result = sql_select( $sql );
					$po_id_arr=array();$job_id_arr=array();
					foreach ($result as $row) {
						$po_id_arr[$row['ID']]=$row['ID'];
						$job_id_arr[$row['JOB_NO']]=$row['JOB_NO'];
					}

					//..................................................................................................
					$sales_sql = "select a.CONTRACT_NO,a.LIEN_BANK,a.BANK_FILE_NO,c.JOB_NO_MST from COM_SALES_CONTRACT a,com_sales_contract_order_info b,wo_po_break_down c where a.id=b.COM_SALES_CONTRACT_ID and c.id=b.WO_PO_BREAK_DOWN_ID ".where_con_using_array($po_id_arr,0,'b.WO_PO_BREAK_DOWN_ID')."";
					 //echo $sales_sql;
					$sales_sql_res = sql_select( $sales_sql );
					$sales_data_arr=array();
					foreach ($sales_sql_res as $row) {
						$sales_data_arr['CONTRACT_NO'][$row['JOB_NO_MST']][$row['CONTRACT_NO']]=$row['CONTRACT_NO'];
						$sales_data_arr['LIEN_BANK'][$row['JOB_NO_MST']][$row['LIEN_BANK']]=$bank_arr[$row['LIEN_BANK']];
						$sales_data_arr['BANK_FILE_NO'][$row['JOB_NO_MST']][$row['BANK_FILE_NO']]=$row['BANK_FILE_NO'];
					}

					//...................................................................................................
					$color_siz_sql ="select b.COLOR_NAME,a.PO_BREAK_DOWN_ID,a.COUNTRY_ID from WO_PO_COLOR_SIZE_BREAKDOWN a, lib_color b where b.id=a.COLOR_NUMBER_ID ".where_con_using_array($po_id_arr,0,'a.PO_BREAK_DOWN_ID')."";
					$color_siz_sql_res = sql_select( $color_siz_sql );
					$color_data_arr=array();
					foreach ($color_siz_sql_res as $row) {
						$color_data_arr['COLOR_NAME'][$row['PO_BREAK_DOWN_ID']][$row['COLOR_NAME']]=$row['COLOR_NAME'];
						$color_data_arr['COUNTRY_NAME'][$row['PO_BREAK_DOWN_ID']][$row['COUNTRY_ID']]=$country_arr[$row['COUNTRY_ID']];
					}
 
					//..................................................................................................
					$pre_cost_sql ="select b.JOB_NO, b.CM_COST,b.PRICE_PCS_OR_SET from WO_PRE_COST_DTLS b where b.is_deleted=0 and b.status_active=1 ".where_con_using_array($job_id_arr,1,'b.JOB_NO')."";
					//echo $pre_cost_sql;
					$pre_cost_sql_res = sql_select( $pre_cost_sql );
					$pre_data_arr=array();
					foreach ($pre_cost_sql_res as $row) {
						$pre_data_arr['CM_COST'][$row['JOB_NO']]=$row['CM_COST'];
						$pre_data_arr['PRICE_PCS_OR_SET'][$row['JOB_NO']]=$row['PRICE_PCS_OR_SET'];
					}




					$result_rows=count($result);
					$new_subacc_code=array();
					$new_job=array();
					$new_buyer=array();
					$new_style=array();
					$sum_qty=0;
					$sum_val=0;
					$grant_sum_qty = 0;
					$grant_sum_val = 0;
					$i=0;$m =1;
					
					foreach( $result as $row) 
					{
						$i++;
						if(!in_array($row[csf('dealing_marchant')],$new_subacc_code))
						{ 
							if($m!=1)
							{ 
							?>
							<tr bgcolor="#FFFFFF" style="font-weight:bold;">
								<td colspan="13" align="right">Group Total</td>
                                <td align="right"><? echo number_format($sum_qty,0); ?></td>
								<td align="right">&nbsp;</td>
								<td align="right"><? echo number_format($sum_val,2); $sum_qty=0;  $sum_val=0;  ?></td>

								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
								<td align="right">&nbsp;</td>
							 </tr> 
							 <?
							}	
						 
							$new_subacc_code[]=$row[csf('dealing_marchant')];
					?>
                            <tr bgcolor="#FCF3FE">
                                    <td colspan="22" width="100%"><strong>Dealing Merchant : <? echo $supplier_library[$row[csf('dealing_marchant')]];?></strong></td>
                             </tr>
                             <?  
						}
					?>
                    <tr> 
                        <td align="center" rowspan="3"><? echo $i; ?></td>
                        
						<td align="center" rowspan="3"><?=implode(',',$sales_data_arr['CONTRACT_NO'][$row[csf('job_no')]]);?></td>
						<td align="center" rowspan="3"><?=implode(',',$sales_data_arr['LIEN_BANK'][$row[csf('job_no')]]);?></td>
						<td align="center" rowspan="3"><?=implode(',',$sales_data_arr['BANK_FILE_NO'][$row[csf('job_no')]]);?></td>

						<td align="center" rowspan="3"><? if(!in_array($row[csf('job_no')],$new_job)){ $new_job[]=$row[csf('job_no')]; echo $row[csf('job_no')];} else echo ""; ?></td>
                        <td rowspan="3"><? if(!in_array($row[csf('buyer_name')],$new_buyer)){ $new_buyer[]=$row[csf('buyer_name')]; echo $buyer_library[$row[csf('buyer_name')]];} else echo ""; ?></td>
                        <td rowspan="3"><? if(!in_array($row[csf('style_ref_no')],$new_style)){ $new_style[]=$row[csf('style_ref_no')]; echo $row[csf('style_ref_no')];} else echo ""; ?></td>
                        
                        <td>Current</td>
                        <td align="center"><? echo $order_status[$row[csf('order_status')]]; ?></td>
                        <td><? echo $row[csf('po_number')]; ?></td>
                        <td align="center"><? echo change_date_format($row[csf('curr_rcv_date')]); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('factory_received_date')]); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('cur_ship_date')]); ?></td>


						<td align="right"><?=implode(',',$color_data_arr['COLOR_NAME'][$row['ID']]);?></td>
						<td align="right"><?=implode(',',$color_data_arr['COUNTRY_NAME'][$row['ID']]);?></td>

                        <td align="right"><? echo number_format($row[csf('po_quantity')],0); ?></td>


						<td align="right"><?=$pre_data_arr['PRICE_PCS_OR_SET'][$row[csf('job_no')]];?></td>
						<td align="right"><?=$row['SET_SMV'];?></td>
						<td align="right"><?=$pre_data_arr['CM_COST'][$row[csf('job_no')]];?></td>


                        <td align="right"><? echo number_format($row[csf('unit_price')],2); ?></td>
                        <td align="right"><? $current_val=$row[csf('po_quantity')]*$row[csf('unit_price')]; echo number_format($current_val,2); ?></td>
                        <td><? echo $user_library[$row[csf('updated_by')]]; ?></td>
                    </tr>
                    <tr> 
                        <td>Previous</td>
                        <td align="center"><? echo $order_status[$row[csf('pre_order_status')]]; ?></td>
                        <td><? echo $row[csf('po_no')]; ?></td>
                        <td align="center"><? echo change_date_format($row[csf('pre_rcv_date')]); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('pre_factory_received_date')]); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('pre_ship_date')]); ?></td>

						<td align="right"><?=implode(',',$color_data_arr['COLOR_NAME'][$row['ID']]);?></td>
						<td align="right"><?=implode(',',$color_data_arr['COUNTRY_NAME'][$row['ID']]);?></td>
                        <td align="right"><? echo number_format($row[csf('previous_po_qty')],0); ?></td>

						<td align="right"><?=$pre_data_arr['PRICE_PCS_OR_SET'][$row[csf('job_no')]];?></td>
						<td align="right"><?=$row['SET_SMV'];?></td>
						<td align="right"><?=$pre_data_arr['CM_COST'][$row[csf('job_no')]];?></td>

                        <td align="right"><? echo number_format($row[csf('avg_price')],2); ?></td>
                        <td align="right"><? $prev_val=$row[csf('previous_po_qty')]*$row[csf('avg_price')]; echo number_format($prev_val,2); ?></td>
                        <td><? echo $user_library[$row[csf('pre_update_by')]]; ?></td>
                    </tr>
                    <tr> 
                        <td><b>Changed By</b></td>
                        <td align="center"><b><? if($row[csf('order_status')]!=$row[csf('pre_order_status')])echo $order_status[$row[csf('order_status')]]; ?></b></td>
                        <td><b><? if($row[csf('po_number')]!=$row[csf('po_no')])echo $row[csf('po_number')]; else ""; ?></b></td>
                        <td align="center"><b><? if(change_date_format($row[csf('curr_rcv_date')])!=change_date_format($row[csf('pre_rcv_date')]))echo change_date_format($row[csf('curr_rcv_date')]); else ""; ?></b></td>
                        <td align="center"><b><? if(change_date_format($row[csf('factory_received_date')])!=change_date_format($row[csf('pre_factory_received_date')]))echo change_date_format($row[csf('factory_received_date')]); ?></b></td>
                        
                        
                        <td align="center"><b><? 
						if(change_date_format($row[csf('cur_ship_date')])!=change_date_format($row[csf('pre_ship_date')])) echo change_date_format($row[csf('cur_ship_date')]); else ""; ?></b></td>
                        
						<td align="right"></td>
						<td align="right"></td>
                        <td align="right"><b><? if($row[csf('po_quantity')]!=$row[csf('previous_po_qty')])echo number_format($row[csf('po_quantity')]-$row[csf('previous_po_qty')],0); else ""; ?></b></td>
						<td align="right"></td>
						<td align="right">0</td>
						<td align="right"></td>
                       	<td align="right"><b><? 
						if($row[csf('unit_price')]!=$row[csf('avg_price')])echo number_format($row[csf('unit_price')]-$row[csf('avg_price')],2); else "";
						?></b></td>
                        <td align="right"><b><? if($current_val!=$prev_val)echo number_format($current_val-$prev_val,2); else ""; ?></b></td>
                        <td><? if($row[csf('updated_by')]!=$row[csf('pre_update_by')]) echo $user_library[$row[csf('updated_by')]];else echo ""; ?></td>
                    </tr>
                    <?
						$sum_qty+=$row[csf('po_quantity')]-$row[csf('previous_po_qty')];
						$sum_val+=$current_val-$prev_val;
						
						$grant_sum_qty += $row[csf('po_quantity')]-$row[csf('previous_po_qty')];
                        $grant_sum_val += $current_val-$prev_val;
								 
						if($i==$result_rows)
						{
						?>
						<tr bgcolor="#FFFFFF" style="font-weight:bold;">
							<td colspan="13" align="right">Group Total</td>
                            <td align="right"><? echo number_format($sum_qty,0); $sum_qty=0;?></td>
							<td align="right">&nbsp;</td>
							<td align="right"><? echo number_format($sum_val,2); $sum_val=0;?></td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
						 </tr> 
						 <?
						}
						$m++;
						$flag=1;
					}
					?>
                    	<tr class="tbl_bottom" bgcolor="#CCCCCC">
                            <td colspan="13" align="right"><b>Confirmed Total</b></td>
                            <td align="right"><b><? echo number_format($grant_sum_qty);?></b></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><b><? echo number_format($grant_sum_val,2);?></b></td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td align="right">&nbsp;</td>
							<td>&nbsp;</td>
                     	</tr>
                    
                 </table>
                 
             <!-- End of Confirmed.........................................................................-->   
                 <br />
				<table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                 	<tr>
                 		<td colspan="22" bgcolor="#FF9900"><strong>Projected Order Revision Status</strong></td>
                 	</tr>
                    <tr>
                        <td width="30" align="center"><strong>SL</strong></td>
						<td  width="80" align="center"><strong>Sales Con NO</strong></td>
						<td  width="70" align="center"><strong>Lien Bank</strong></td>
						<td  width="70" align="center"><strong>Bank file No</strong></td>

                        <td  width="70" align="center"><strong>Job No.</strong></td>
                        <td width="100" align="center"><strong>Buyer</strong></td>
                        <td width="100" align="center"><strong>Style</strong></td>
                        <td width="80" align="center"><strong>Particulars</strong></td>
                        <td width="80" align="center"><strong>Ord. Status</strong></td>
                        <td width="110" align="center"><strong>PO No</strong></td>
                        <td width="90" align="center"><strong>PO Rcv Date</strong></td>
                        <td width="90" align="center"><strong>Fac. Rcv Date</strong></td>
                        <td width="80" align="center"><strong>Ship Date</strong></td>
                        
						<td width="80" align="center"><strong>Color</strong></td>
						<td width="80" align="center"><strong>Country</strong></td>
						
						<td width="90" align="center"><strong>Order Qty</strong></td>

						<td width="50" align="center"><strong>FOB</strong></td>
						<td width="50" align="center"><strong>SMV</strong></td>
						<td width="50" align="center"><strong>CM</strong></td>


                        <td width="50" align="center"><strong>Rate</strong></td>
                        <td width="110" align="center"><strong>Order Value</strong></td>
                        <td align="center"><strong>Update By</strong></td>
                    </tr>
                    <?
                    $i=0;$m =1; 
                   $sql = "
				   select 
					   b.id, 
					   a.job_no, 
					   a.buyer_name,
					   b.updated_by, 
					   b.po_number, 
					   b.po_quantity, 
					   b.pub_shipment_date as cur_ship_date, 
					   b.unit_price, 
					   a.dealing_marchant, 
					   c.shipment_date as pre_ship_date, 
					   c.previous_po_qty, 
					   a.style_ref_no,
					   a.SET_SMV,
					   b.po_received_date as curr_rcv_date, 
					   b.factory_received_date, 
					   c.po_no, 
					   c.po_received_date as pre_rcv_date, 
					   c.shipment_date as pre_ship_date,
					   c.order_status as pre_order_status,
					   c.fac_receive_date as pre_factory_received_date,
					   b.is_confirmed as order_status,
					   c.avg_price,
					   c.prev_update_by as pre_update_by 
				   from 
					   wo_po_details_master a, 
					   wo_po_break_down b, 
					   wo_po_update_log c 
				   where 
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.po_quantity!=c.previous_po_qty
						   and b.is_confirmed=2
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.unit_price!=c.avg_price
						   and b.is_confirmed=2
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.is_confirmed!=c.order_status
						   and b.is_confirmed=2
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.po_number!=c.po_no
						   and b.is_confirmed=2
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.po_received_date!=c.po_received_date
						   and b.is_confirmed=2
					   )
					   or
					   (
						   a.job_no=b.job_no_mst 
						   and b.id=c.po_id 
						   and a.company_name like '$compid' 
						   and a.is_deleted=0 
						   and a.status_active=1 
						   and b.is_deleted=0 
						   and b.status_active=1 
						   and c.update_date between'$prev_date' and '$current_date'
						   and b.pub_shipment_date!=c.shipment_date
						   and b.is_confirmed=2
					   )
				   order by a.dealing_marchant,a.job_no"; // 
				   $result = sql_select( $sql );
				   $po_id_arr=array();$job_id_arr=array();
				   foreach ($result as $row) {
					   $po_id_arr[$row['ID']]=$row['ID'];
					   $job_id_arr[$row['JOB_NO']]=$row['JOB_NO'];
				   }

				   //..................................................................................................
				   $sales_sql = "select a.CONTRACT_NO,a.LIEN_BANK,a.BANK_FILE_NO,c.JOB_NO_MST from COM_SALES_CONTRACT a,com_sales_contract_order_info b,wo_po_break_down c where a.id=b.COM_SALES_CONTRACT_ID and c.id=b.WO_PO_BREAK_DOWN_ID ".where_con_using_array($po_id_arr,0,'b.WO_PO_BREAK_DOWN_ID')."";
					//echo $sales_sql;
				   $sales_sql_res = sql_select( $sales_sql );
				   $sales_data_arr=array();
				   foreach ($sales_sql_res as $row) {
					   $sales_data_arr['CONTRACT_NO'][$row['JOB_NO_MST']][$row['CONTRACT_NO']]=$row['CONTRACT_NO'];
					   $sales_data_arr['LIEN_BANK'][$row['JOB_NO_MST']][$row['LIEN_BANK']]=$bank_arr[$row['LIEN_BANK']];
					   $sales_data_arr['BANK_FILE_NO'][$row['JOB_NO_MST']][$row['BANK_FILE_NO']]=$row['BANK_FILE_NO'];
				   }

				   //...................................................................................................
				   $color_siz_sql ="select b.COLOR_NAME,a.PO_BREAK_DOWN_ID,a.COUNTRY_ID from WO_PO_COLOR_SIZE_BREAKDOWN a, lib_color b where b.id=a.COLOR_NUMBER_ID ".where_con_using_array($po_id_arr,0,'a.PO_BREAK_DOWN_ID')."";
				   $color_siz_sql_res = sql_select( $color_siz_sql );
				   $color_data_arr=array();
				   foreach ($color_siz_sql_res as $row) {
					   $color_data_arr['COLOR_NAME'][$row['PO_BREAK_DOWN_ID']][$row['COLOR_NAME']]=$row['COLOR_NAME'];
					   $color_data_arr['COUNTRY_NAME'][$row['PO_BREAK_DOWN_ID']][$row['COUNTRY_ID']]=$country_arr[$row['COUNTRY_ID']];
				   }

				   //..................................................................................................
				   $pre_cost_sql ="select b.JOB_NO, b.CM_COST,b.PRICE_PCS_OR_SET from WO_PRE_COST_DTLS b where b.is_deleted=0 and b.status_active=1 ".where_con_using_array($job_id_arr,1,'b.JOB_NO')."";
				   //echo $pre_cost_sql;
				   $pre_cost_sql_res = sql_select( $pre_cost_sql );
				   $pre_data_arr=array();
				   foreach ($pre_cost_sql_res as $row) {
					   $pre_data_arr['CM_COST'][$row['JOB_NO']]=$row['CM_COST'];
					   $pre_data_arr['PRICE_PCS_OR_SET'][$row['JOB_NO']]=$row['PRICE_PCS_OR_SET'];
				   }





				   
					$new_subacc_code=array();
					$new_job=array();
					$new_buyer=array();
					$new_style=array();
					$sum_qty=0;
					$sum_val=0;
					$grant2_sum_qty = 0;
					$grant2_sum_val = 0;
					
					$result_rows=count($result);
					foreach( $result as $row) 
					{
						$i++;
						if(!in_array($row[csf('dealing_marchant')],$new_subacc_code))
						{
							if($m!=1)
							{
							?>
								<tr bgcolor="#FFFFFF" style="font-weight:bold;">
								<td colspan="13" align="right">Group Total</td>
                                <td align="right"><? echo number_format($sum_qty,0);   ?></td>
								<td align="right">&nbsp;</td>
								<td align="right"><? echo number_format($sum_val,2);  $sum_qty=0;  $sum_val=0;  ?></td>
                                <td align="right">&nbsp;</td>
							 </tr> 
							 <?
							}	
						 
							$new_subacc_code[]=$row[csf('dealing_marchant')];
					?>
                            <tr bgcolor="#FCF3FE">
                                        <td colspan="18" width="100%"><strong>Dealing Merchant : <? echo $supplier_library[$row[csf('dealing_marchant')]];?></strong></td>
                             </tr>
                             <?  
						}
					?>
                    <tr> 
                        <td align="center" rowspan="3"><? echo $i; ?></td>
                        
						<td align="center" rowspan="3"><?=implode(',',$sales_data_arr['CONTRACT_NO'][$row[csf('job_no')]]);?></td>
						<td align="center" rowspan="3"><?=implode(',',$sales_data_arr['LIEN_BANK'][$row[csf('job_no')]]);?></td>
						<td align="center" rowspan="3"><?=implode(',',$sales_data_arr['BANK_FILE_NO'][$row[csf('job_no')]]);?></td>

                        <td align="center" rowspan="3"><? if(!in_array($row[csf('job_no')],$new_job)){ $new_job[]=$row[csf('job_no')]; echo $row[csf('job_no')];} else echo ""; ?></td>
                        <td rowspan="3"><? if(!in_array($row[csf('buyer_name')],$new_buyer)){ $new_buyer[]=$row[csf('buyer_name')]; echo $buyer_library[$row[csf('buyer_name')]];} else echo ""; ?></td>
                        <td rowspan="3"><? if(!in_array($row[csf('style_ref_no')],$new_style)){ $new_style[]=$row[csf('style_ref_no')]; echo $row[csf('style_ref_no')];} else echo ""; ?></td>
                        
                        <td>Current</td>
                        <td align="center"><? echo $order_status[$row[csf('order_status')]]; ?></td>
                        <td><? echo $row[csf('po_number')]; ?></td>
                        <td align="center"><? echo change_date_format($row[csf('curr_rcv_date')]); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('factory_received_date')]); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('cur_ship_date')]); ?></td>

						<td align="right"><?=implode(',',$color_data_arr['COLOR_NAME'][$row['ID']]);?></td>
						<td align="right"><?=implode(',',$color_data_arr['COUNTRY_NAME'][$row['ID']]);?></td>
                        <td align="right"><? echo number_format($row[csf('po_quantity')],0); ?></td>

						<td align="right"><?=$pre_data_arr['PRICE_PCS_OR_SET'][$row[csf('job_no')]];?></td>
						<td align="right"><?=$row['SET_SMV'];?></td>
						<td align="right"><?=$pre_data_arr['CM_COST'][$row[csf('job_no')]];?></td>

                        <td align="right"><? echo number_format($row[csf('unit_price')],2); ?></td>
                        <td align="right"><? $current_val=$row[csf('po_quantity')]*$row[csf('unit_price')]; echo number_format($current_val,2); ?></td>
                        <td><? echo $user_library[$row[csf('updated_by')]]; ?></td>
                    </tr>
                    <tr> 
                        <td>Previous</td>
                        <td align="center"><? echo $order_status[$row[csf('pre_order_status')]]; ?></td>
                        <td><? echo $row[csf('po_no')]; ?></td>
                        <td align="center"><? echo change_date_format($row[csf('pre_rcv_date')]); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('pre_factory_received_date')]); ?></td>
                        <td align="center"><? echo change_date_format($row[csf('pre_ship_date')]); ?></td>

						<td align="right"><?=implode(',',$color_data_arr['COLOR_NAME'][$row['ID']]);?></td>
						<td align="right"><?=implode(',',$color_data_arr['COUNTRY_NAME'][$row['ID']]);?></td>
                        <td align="right"><? echo number_format($row[csf('previous_po_qty')],0); ?></td>

						<td align="right"><?=$pre_data_arr['PRICE_PCS_OR_SET'][$row[csf('job_no')]];?></td>
						<td align="right"><?=$row['SET_SMV'];?></td>
						<td align="right"><?=$pre_data_arr['CM_COST'][$row[csf('job_no')]];?></td>


                        <td align="right"><? echo number_format($row[csf('avg_price')],2); ?></td>
                        <td align="right"><? $prev_val=$row[csf('previous_po_qty')]*$row[csf('avg_price')]; echo number_format($prev_val,2); ?></td>
                        <td><? echo $user_library[$row[csf('pre_update_by')]]; ?></td>
                    </tr>
                    <tr> 
                        <td><b>Changed By</b></td>
                        <td align="center"><b><? if($row[csf('order_status')]!=$row[csf('pre_order_status')])echo $order_status[$row[csf('order_status')]]; ?></b></td>
                        <td><b><? if($row[csf('po_number')]!=$row[csf('po_no')])echo $row[csf('po_number')]; else ""; ?></b></td>
                        <td align="center"><b><? if(change_date_format($row[csf('curr_rcv_date')])!=change_date_format($row[csf('pre_rcv_date')]))echo change_date_format($row[csf('curr_rcv_date')]); else ""; ?></b></td>
                        <td align="center"><b><? if(change_date_format($row[csf('factory_received_date')])!=change_date_format($row[csf('pre_factory_received_date')]))echo change_date_format($row[csf('factory_received_date')]); ?></b></td>
                        
                        
                        <td align="center"><b><? 
						if(change_date_format($row[csf('cur_ship_date')])!=change_date_format($row[csf('pre_ship_date')])) echo change_date_format($row[csf('cur_ship_date')]); else ""; ?></b></td>
                        
                        <td align="right"></td>
						<td align="right"></td>
                        <td align="right"><b><? if($row[csf('po_quantity')]!=$row[csf('previous_po_qty')])echo number_format($row[csf('po_quantity')]-$row[csf('previous_po_qty')],0); else ""; ?></b></td>
						<td align="right"></td>
						<td align="right"></td>
						<td align="right"></td>
                       	<td align="right"><b><? 
						if($row[csf('unit_price')]!=$row[csf('avg_price')])echo number_format($row[csf('unit_price')]-$row[csf('avg_price')],2); else "";
						?></b></td>
                        <td align="right"><b><? if($current_val!=$prev_val)echo number_format($current_val-$prev_val,2); else ""; ?></b></td>
                        <td><? if($row[csf('updated_by')]!=$row[csf('pre_update_by')]) echo $user_library[$row[csf('updated_by')]];else echo ""; ?></td>
                    </tr>
                    <?
						$sum_qty+=$row[csf('po_quantity')]-$row[csf('previous_po_qty')];
						$sum_val+=$current_val-$prev_val;
						
						$grant2_sum_qty += $row[csf('po_quantity')]-$row[csf('previous_po_qty')];
                        $grant2_sum_val += $current_val-$prev_val;
								 
						if($i==$result_rows)
						{
						?>
						<tr bgcolor="#FFFFFF" style="font-weight:bold;">
							<td colspan="13" align="right">Group Total</td>
                            <td align="right"><? echo number_format($sum_qty,0);   ?></td>
							<td align="right">&nbsp;</td>
							<td align="right"><? echo number_format($sum_val,2);?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
						 </tr> 
						 <?
						}
						$m++;
						$flag=1;
					}
					
					?>
                    	<tr class="tbl_bottom" bgcolor="#CCCCCC">
                            <td colspan="13" align="right"><b>Projected Total</b></td>
                            <td align="right"><b><? echo number_format($grant2_sum_qty);?></b></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><b><? echo number_format($grant2_sum_val,2);?></b></td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                     	</tr>
                    	<tr class="tbl_bottom">
                            <td colspan="13" align="right"><b>Grand Total</b></td>
                            <td align="right"><b><? echo number_format($grant_sum_qty+$grant2_sum_qty);?></b></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><b><? echo number_format($grant_sum_val+$grant2_sum_val,2);?></b></td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                     	</tr>
                 </table>                 

            </td>
        </tr>
    </table>
    
<?
		$sum_qty=0;
		$sum_val=0;
		$grant_sum_qty=0;
		$grant_sum_val=0;

		$message=ob_get_contents();
		ob_clean();


		
	$mailArr=array();	
	$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=7 and b.mail_user_setup_id=c.id and a.company_id=$compid and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$mailArr[$row['EMAIL_ADDRESS']]=$row['EMAIL_ADDRESS']; 
	}
	$to=implode(',',$mailArr);
 	$subject = "Daily Revised Order";
	


	$header=mailHeader();

	if($_REQUEST['isview']==1){
		$mail_item=7;
		if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
		echo $message;
	}
	else{
		if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);
	}




}
	
	




?> 
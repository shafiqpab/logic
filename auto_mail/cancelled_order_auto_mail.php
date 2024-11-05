<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$user_library=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
//$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1); 
$select_fill="to_char(b.update_date,'DD-MM-YYYY HH12:MI:SS')";
	
	
//$prev_date="4-Nov-2017";
//$company_library=array(3=>'Test Company');


foreach($company_library as $compid=>$compname)
{
$flag=0;
ob_start();	
	?>
    
    <table width="100%">
        <tr>
            <td align="center">
                <strong style="font-size:24px;"> <? echo $compname; ?></strong>
            </td>
        </tr>
        <tr>
            <td align="center"><strong>Cancelled Order List Of  ( Date : <? echo date('d-m-Y',strtotime($prev_date));?> )</strong></td>
        </tr>
        <tr>
            <td valign="top" align="left">
            
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                    <tr>
                        <th width="30" rowspan="2"><strong>SL</strong></th>
                        <th width="70" rowspan="2"><strong>PO Status</strong></th>
                        <th width="110" rowspan="2"><strong>Job No</strong></th>
                        <th width="70" rowspan="2"><strong>PO No</strong></th>
                        <th width="70" rowspan="2"><strong>Buyer</strong></th>
                        <th width="80" rowspan="2"><strong>PO Recv. Date</strong></th>
                        <th width="80" rowspan="2"><strong>Shipment Date</strong></th>
                        <th width="70" rowspan="2"><strong>PO Qnty</strong></th>
                        <th width="70" rowspan="2"><strong>Unit Price</strong></th>
                        <th width="100" rowspan="2"><strong>PO Value (USD)</strong></th>
                        <th width="80" rowspan="2"><strong>Cancelled By</strong></th>
                        <th width="100" rowspan="2"><strong>Cancell Date & Time</strong></th>
                        <th width="250" colspan="3"><strong>Execution Status</strong></th>
                        <th rowspan="2"><strong>Remarks</strong></th>
                        <th rowspan="2"><strong>Dealing Merchant</strong></th>
                    </tr>
                    <tr>
                        <th width="160"><strong>Particulars</strong></th>
                        <th width="40"><strong>Qty/ Value</strong></th>
                        <th width="50"><strong>UOM</strong></th>
                    </tr>
                    
                   </thead>
                    <?
                  $sql = "
						select 
							a.buyer_name,
							a.dealing_marchant,
							b.id,
							b.job_no_mst, 
							b.is_confirmed, 
							b.po_number,
							b.po_received_date,
							b.pub_shipment_date,
							b.po_quantity,
							b.unit_price,
							b.updated_by,
							$select_fill as update_date,
							b.details_remarks
						from 
							wo_po_details_master a, 
							wo_po_break_down b 
						where 
							a.job_no=b.job_no_mst 
							and a.company_name = '$compid' 
							and b.status_active=3 
							and b.update_date between '".$prev_date."' and '".$current_date."'
							and a.is_deleted=0
							and a.status_active=1
							";
						$result = sql_select( $sql );
					$i=1;
					foreach( $result as $row) 
					{
						$po_data_arr[]=array(
							'id'=>$row[csf('id')],
							'is_confirmed'=>$row[csf('is_confirmed')],
							'job_no_mst'=>$row[csf('job_no_mst')],
							'po_number'=>$row[csf('po_number')],
							'po_received_date'=>$row[csf('po_received_date')],
							'pub_shipment_date'=>$row[csf('pub_shipment_date')],
							'po_quantity'=>$row[csf('po_quantity')],
							'unit_price'=>$row[csf('unit_price')],
							'value'=>$row[csf('po_quantity')]*$row[csf('unit_price')],
							'updated_by'=>$row[csf('updated_by')],
							'update_date'=>$row[csf('update_date')],
							'buyer_name'=>$row[csf('buyer_name')],
							'dealing_marchant'=>$row[csf('dealing_marchant')],
							'details_remarks'=>$row[csf('details_remarks')]
						);
						$po_arr[$row[csf('id')]]=$row[csf('id')];	
					}
					
					$po_string =  implode(',',$po_arr);
//------------------------------------
$sqls="SELECT a.currency_id,a.exchange_rate,b.booking_type,b.po_break_down_id,b.grey_fab_qnty,b.amount,b.uom FROM wo_booking_mst a,wo_booking_dtls b WHERE b.po_break_down_id IN($po_string) and a.booking_no=b.booking_no and b.status_active=1 and b.is_deleted=0";
$gf_qnty= sql_select($sqls);
foreach($gf_qnty as $row) 
{
	if(($row[csf('booking_type')]==1 || $row[csf('booking_type')]==1) && $row[csf('grey_fab_qnty')]){
		$Particulars_arr[$row[csf('po_break_down_id')]]['Fab. Booking Qty(greay qty)']+=$row[csf('grey_fab_qnty')];	
		$uom_arr[$row[csf('po_break_down_id')]]['Fab. Booking Qty(greay qty)']='Kg';	
		}


	if(($row[csf('booking_type')]==2 || $row[csf('booking_type')]==5) && $row[csf('amount')]){
		$Particulars_arr[$row[csf('po_break_down_id')]]['Trims Booking Value']+=$row[csf('amount')]/$row[csf('exchange_rate')];	
		$uom_arr[$row[csf('po_break_down_id')]]['Trims Booking Value']='USD';	
		}

	if($row[csf('booking_type')]==3 && $row[csf('amount')]){
		$Particulars_arr[$row[csf('po_break_down_id')]]['Service Booking Value']+=$row[csf('amount')]/$row[csf('exchange_rate')];	
		$uom_arr[$row[csf('po_break_down_id')]]['Service Booking Value']='USD';	
		}

}

//---------------------------------------- yarn issue - return	
/*$pq_qty= sql_select("SELECT b.transaction_type,a.po_breakdown_id,a.quantity as qty,b.cons_uom FROM order_wise_pro_details a, inv_transaction b WHERE a.trans_id=b.id and a.po_breakdown_id IN($po_string)  and b.item_category=1  and a.trans_type=1 and a.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0") ;
foreach($pq_qty as $row) 
	{
		if($row[csf('transaction_type')]==2 && $row[csf('qty')]){
			$Particulars_arr[$row[csf('po_breakdown_id')]]['Net Yarn Issue Qty']+=$row[csf('qty')];	
			$uom_arr[$row[csf('po_breakdown_id')]]['Net Yarn Issue Qty']=$unit_of_measurement[$row[csf('cons_uom')]];	
		}
	
	}*/


$sql_yarn_iss="select a.po_breakdown_id,
				sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
				sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
				from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and a.po_breakdown_id IN($po_string) and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by a.po_breakdown_id";
		$dataArrayIssue=sql_select($sql_yarn_iss);
		foreach($dataArrayIssue as $row)
		{
			if($row[csf('issue_qnty')]){
			$Particulars_arr[$row[csf('po_breakdown_id')]]['Net Yarn Issue Qty']=$row[csf('issue_qnty')]-$row[csf('return_qnty')];
			$uom_arr[$row[csf('po_breakdown_id')]]['Net Yarn Issue Qty']="Kg";
			}
		}


//-----------------Knitting Prod Qty	

/*$pq_qty= sql_select("select b.po_breakdown_id,b.quantity as qty,a.uom from pro_grey_prod_entry_dtls a,order_wise_pro_details b where a.id=b.dtls_id and b.po_breakdown_id in($po_string) and b.trans_type=1 and b.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0") ;
foreach($pq_qty as $row) 
	{
		if($row[csf('qty')]){
			$Particulars_arr[$row[csf('po_breakdown_id')]]['Knitting Prod Qty']+=$row[csf('qty')];
			$uom_arr[$row[csf('po_breakdown_id')]]['Knitting Prod Qty']=$unit_of_measurement[$row[csf('uom')]];	
		}
	
	}
	
*/	
	
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
								sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit,
								sum(CASE WHEN entry_form ='45' and trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive
								
								
								from order_wise_pro_details where  po_breakdown_id in($po_string) and status_active=1 and is_deleted=0 and entry_form in(2,13,45) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
			$grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive_return')];
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
		}


		$sql_grey_purchase="select c.po_breakdown_id, sum(c.quantity) as grey_purchase_qnty from inv_receive_master a,pro_grey_prod_entry_dtls b, order_wise_pro_details c where c.po_breakdown_id in($po_string) and a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=22 and c.entry_form=22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayGreyPurchase=sql_select($sql_grey_purchase);
		foreach($dataArrayGreyPurchase as $greyRow)
		{
			$greyPurchaseQntyArray[$greyRow[csf('po_breakdown_id')]]=$greyRow[csf('grey_purchase_qnty')];
		}

$po_expload_arr=explode(',',$po_string);
foreach($po_expload_arr as $pi)
{
	$net_trans_knit=$trans_qnty_arr[$pi];
	$grey_purchase_qnty=$greyPurchaseQntyArray[$pi]-$grey_receive_return_qnty_arr[$pi];
	$grey_recv_qnty=$grey_receive_qnty_arr[$pi];
	
	$grey_available=$grey_recv_qnty+$grey_purchase_qnty+$net_trans_knit;
	
	if($grey_available)
	{
		$Particulars_arr[$pi]['Grey Available']=$grey_available;
		$uom_arr[$pi]['Grey Available']='Kg';
	}
		
}
 

	
//------------------Fabric Dyeing Qty	
	
$fd_qty= sql_select("select b.po_id,b.batch_qnty as qty from pro_fab_subprocess a,pro_batch_create_dtls b where a.batch_id=b.mst_id and b.po_id in($po_string) and a.load_unload_id=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0") ;
foreach($fd_qty as $row) 
	{
		if($row[csf('qty')]){
			$Particulars_arr[$row[csf('po_id')]]['Fabric Dyeing Qty']+=$row[csf('qty')];
			$uom_arr[$row[csf('po_id')]]['Fabric Dyeing Qty']='Kg';	
		}
	
	}
	


//----------------------finish fab


		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty,
								sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
								sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise,
								sum(CASE WHEN entry_form ='46' and trans_type=3 THEN quantity ELSE 0 END) AS recv_rtn_qnty
								
								from order_wise_pro_details where po_breakdown_id in($po_string) and status_active=1 and is_deleted=0 and entry_form in(15,7,66,46) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			$trans_qnty_fin_arr[$row[csf('po_breakdown_id')]]=$row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')];
			$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_roll_wise')];
			$finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('recv_rtn_qnty')];
			}
	
		$sql_fin_purchase="select c.po_breakdown_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where c.po_breakdown_id in($po_string) and a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			$finish_purchase_qnty_arr[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase')];
		}
$po_expload_arr=explode(',',$po_string);
foreach($po_expload_arr as $pi){
	$fab_recv_qnty=$finish_receive_qnty_arr[$pi];	
	$fab_purchase_qnty=$finish_purchase_qnty_arr[$pi]-$finish_recv_rtn_qnty_arr[$pi];
	$net_trans_finish=$trans_qnty_fin_arr[$pi];
	
	$fabric_available=$fab_recv_qnty+$fab_purchase_qnty+$net_trans_finish;		
	if($grey_available){
	$Particulars_arr[$pi]['Finish Fabric available']=$fabric_available;
	$uom_arr[$pi]['Finish Fabric available']='Kg';
	}

}

//-----------------------------------------------
	$trims_in_house_val=sql_select("select a.rate,b.po_breakdown_id,b.quantity,c.exchange_rate,c.currency_id  from  inv_receive_master c,product_details_master d,inv_trims_entry_dtls a , order_wise_pro_details b where b.po_breakdown_id IN($po_string) and a.mst_id=c.id and a.trans_id=b.trans_id and a.prod_id=d.id and b.trans_type=1 and b.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
foreach($trims_in_house_val as $row)
{
	if($row[csf('currency_id')]==1){
		$Particulars_arr[$row[csf('po_breakdown_id')]]['Trims In-house Value']+=($row[csf('quantity')]*$row[csf('rate')])/$row[csf('exchange_rate')];	
	}
	else
	{
		$Particulars_arr[$row[csf('po_breakdown_id')]]['Trims In-house Value']+=($row[csf('quantity')]*$row[csf('rate')]);	
	}
	$uom_arr[$row[csf('po_breakdown_id')]]['Trims In-house Value']='USD';	
}



	
//-----------------------------------------------
$pq_qty= sql_select("SELECT production_type,po_break_down_id,production_quantity as qty FROM pro_garments_production_mst WHERE po_break_down_id IN($po_string) and status_active=1 and is_deleted=0 order by production_type");
foreach($pq_qty as $row) 
	{
		
/*		if($row[csf('production_type')]==2 && $row[csf('qty')]){
			$Particulars_arr[$row[csf('po_break_down_id')]]['Finish Fab. Prod Qty']+=$row[csf('qty')];
			$uom_arr[$row[csf('po_break_down_id')]]['Finish Fab. Prod Qty']='Kg';
		}
*/		
		if($row[csf('production_type')]==1 && $row[csf('qty')]){
			$Particulars_arr[$row[csf('po_break_down_id')]]['Cutting Prod Qty']+=$row[csf('qty')];
			$uom_arr[$row[csf('po_break_down_id')]]['Cutting Prod Qty']='Pcs';	
		}
		
	
		if($row[csf('production_type')]==3 && $row[csf('qty')]){
			$Particulars_arr[$row[csf('po_break_down_id')]]['Emb. Production Qty']+=$row[csf('qty')];
			$uom_arr[$row[csf('po_break_down_id')]]['Emb. Production Qty']='Pcs';	
		}
	
		if($row[csf('production_type')]==5 && $row[csf('qty')]){
			$Particulars_arr[$row[csf('po_break_down_id')]]['Sewing Prod Qty']+=$row[csf('qty')];
			$uom_arr[$row[csf('po_break_down_id')]]['Sewing Prod Qty']='Pcs';	
		}
	
	}
	
	
	
	


//----------------------------------------lc	
$pq_qty= sql_select("select a.export_lc_no,b.wo_po_break_down_id from com_export_lc a,com_export_lc_order_info b where b.wo_po_break_down_id in($po_string) and a.id=b.com_export_lc_id and a.status_active = 1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.export_lc_no,b.wo_po_break_down_id") ;
foreach($pq_qty as $row) 
	{
		if($row[csf('export_lc_no')]){
			$Particulars_arr[$row[csf('wo_po_break_down_id')]]['Att.with LC'].=$row[csf('export_lc_no')].',';
		}
	
	}

//----------------------------------------sc	
$pq_qty= sql_select("select a.contract_no,b.wo_po_break_down_id from com_sales_contract a, com_sales_contract_order_info b where a.id=b.com_sales_contract_id and b.wo_po_break_down_id in($po_string) and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.contract_no,b.wo_po_break_down_id") ;
foreach($pq_qty as $row) 
	{
		if($row[csf('contract_no')]){
			$Particulars_arr[$row[csf('wo_po_break_down_id')]]['Att.with SC'].=$row[csf('contract_no')].',';
		}
	
	}
	

					
					foreach( $po_data_arr as $row) 
					{
					$rowspan = count($Particulars_arr[$row['id']]);
					?>
							
                    <tr>
                        <td width="30" rowspan="<? echo $rowspan;?>" align="center"><? echo $i;?></td>
                        <td width="70" rowspan="<? echo $rowspan;?>" align="center"><? echo $order_status[$row['is_confirmed']];?></td>
                        <td width="110" rowspan="<? echo $rowspan;?>" align="center"><? echo $row['job_no_mst'];?></td>
                        <td width="70" rowspan="<? echo $rowspan;?>"><? echo $row['po_number'];?></td>
                        <td width="70" rowspan="<? echo $rowspan;?>"><? echo $buyer_library[$row['buyer_name']];?></td>
                        <td width="80" rowspan="<? echo $rowspan;?>" align="center"><? echo change_date_format($row['po_received_date']);?></td>
                        <td width="80" rowspan="<? echo $rowspan;?>" align="center"><? echo change_date_format($row['pub_shipment_date']);?></td>
                        <td width="70" rowspan="<? echo $rowspan;?>" align="right"><? echo $row['po_quantity'];?></td>
                        <td width="70" rowspan="<? echo $rowspan;?>" align="right"><? echo $row['unit_price'];?></td>
                        <td width="100" rowspan="<? echo $rowspan;?>" align="right"><? echo $row['value'];?></td>
                        <td width="80" rowspan="<? echo $rowspan;?>" align="center"><? echo $user_library[$row['updated_by']];?></td>
                        <td width="80" rowspan="<? echo $rowspan;?>" align="center"><? echo $row['update_date'];?></td>
						<?
								$sm=0;
								foreach($Particulars_arr[$row['id']] as $pc=>$pcv){
									if($sm==0){
								?>
								<td width="160"><? echo $pc;?></td>
								<td width="40" align="right"><? if($pc=='Att.with LC' || $pc=='Att.with SC'){echo trim($pcv,',');}else{echo number_format($pcv,2);}?></td>
								<td width="50" align="center"><? echo $uom_arr[$row['id']][$pc];?></td>
								<td rowspan="<? echo $rowspan;?>"><? echo $row['details_remarks'];?></td>
								<td rowspan="<? echo $rowspan;?>"><? echo $supplier_library[$row['dealing_marchant']];?></td>
							</tr>
							<?
								$sm++;
								}
								else
								{
								?>
                              <tr> 
                                <td width="160"><? echo $pc;?></td>
                                <td width="40" align="right"><? if($pc=='Att.with LC' || $pc=='Att.with SC'){echo trim($pcv,',');}else{echo number_format($pcv,2);}?></td>
                                <td width="50" align="center"><? echo $uom_arr[$row['id']][$pc];?></td>
                              </tr>
                                <?	
								}
                            }
							if(count($Particulars_arr[$row['id']])<1)
							{
								echo '<td colspan="3" align="center"><span style="color:#f00;">No Subsequent Entry Found</span></td><td>'.$row['details_remarks'].'</td><td>'.$supplier_library[$row['dealing_marchant']].'</td></tr>';	
							}
							
                            ?>
                        
                    <?
						$i++;
						$flag=1;
						}
					?>
                    
                    
                 </table>
                 
            </td>
        </tr>
    </table>
    
<?
		$sum_qty=0;
		$sum_val=0;
		$grant_sum_qty=0;
		$grant_sum_val=0;
		$po_data_arr=array(); 

		
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=8 and b.mail_user_setup_id=c.id and a.company_id=$compid  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1";
	$mail_sql=sql_select($sql);
	$toArr=array();
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
 	$subject = "Cancelled Order List";
	
	$message="";
	$message=ob_get_contents();
	
	$header=mailHeader();
	ob_clean();
	//if($to!="")echo sendMailMailer( $to, $subject, $message, $from_mail);

	if($_REQUEST['isview']==1){
		$mail_item=8;
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

$flag=0;


 
}
	
	
//delete order ------------
//$prev_date="1-Oct-2015";$current_date="5-Feb-2016";

foreach($company_library as $compid=>$compname)
{
$flag=0;
ob_start();	
	?>
    
    <table>
        <tr>
            <td align="center">
                <strong style="font-size:24px;"> <? echo $compname; ?></strong>
            </td>
        </tr>
        <tr>
            <td align="center"><strong>Deleted Order List Of ( Date : <? echo date('d-m-Y',strtotime($prev_date));?> )</strong></td>
        </tr>
        <tr>
            <td>
            
                 <table width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                   <thead>
                    <tr bgcolor="#999999">
                        <th width="30"><strong>SL</strong></th>
                        <th width="70"><strong>PO Status</strong></th>
                        <th width="100"><strong>Job No</strong></th>
                        <th width="100"><strong>Order No</strong></th>
                        <th width="70"><strong>Buyer</strong></th>
                        <th width="100">Style</th>
                        <th width="80"><strong>PO Recv. Date</strong></th>
                        <th width="80"><strong>Ship Date</strong></th>
                        <th width="100">Item</th>
                        <th width="30">SMV</th>
                        <th width="70"><strong>Order Qty.</strong></th>
                        <th width="50">UOM</th>
                        <th width="100">Order Qty (Pcs)</th>
                        <th width="100">Total SMV</th>
                        <th width="70"><strong>Unit Price</strong></th>
                        <th width="100"><strong>PO Value (USD)</strong></th>
                        <th width="80"><strong>Deleted By</strong></th>
                        <th width="100"><strong>Delete Date & Time</strong></th>
                        <th width="100"><strong>Dealing Merchant</strong></th>
                        <th><strong>Remarks</strong></th>
                        </tr>
                   </thead>
                    <?
                  $sql = "
						select 
							a.buyer_name,
							a.dealing_marchant,
							a.set_smv,
							a.set_break_down,
							a.style_ref_no,
							a.order_uom,
							b.id,
							b.job_no_mst, 
							b.is_confirmed, 
							b.po_number,
							b.po_received_date,
							b.pub_shipment_date,
							b.po_quantity,
							b.unit_price,
							b.updated_by,
							b.update_date,
							b.details_remarks
						from 
							wo_po_details_master a, 
							wo_po_break_down b 
						where 
							a.job_no=b.job_no_mst 
							and a.company_name = '$compid' 
							and b.status_active=2 
							and b.update_date between '".$prev_date."' and '".$current_date."'
							and a.is_deleted=0
							and a.status_active=1
							";
					$result = sql_select( $sql );
					$i=1; $grand_smv=0;$grand_tot_smv=0;$grand_po_qty_pcs=0;$grand_po_val_usd=0;
					foreach( $result as $row) 
					{
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
						$set_arr=explode('__',$row[csf('set_break_down')]);
						$item_sting='';
						$smv_sting='';
						$set_sting='';
						$smv_sum=0;
						$set_sum=0;
						foreach($set_arr as $set_data){
							list($item,$set,$smv)=explode('_',$set_data);
							if($item_sting=='')$item_sting.=$garments_item[$item];else $item_sting.=','.$garments_item[$item];
							if($smv_sting=='')$smv_sting.=$smv;else $smv_sting.='+'.$smv;
							if($set_sting=='')$set_sting.=$set;else $set_sting.=':'.$set;
							$smv_sum+=$smv;
							$set_sum+=$set;
							$grand_smv+=$smv;
							
						}
						$value_usd=($set_sum*$row[csf('po_quantity')])*$row[csf('unit_price')];
					?>
							
                    <tr bgcolor="<? echo $bgcolor ; ?>">
                        <td align="center"><? echo $i;?></td>
                        <td align="center"><? echo $order_status[$row[csf('is_confirmed')]];?></td>
                        <td align="center"><? echo $row[csf('job_no_mst')];?></td>
                        <td><? echo $row[csf('po_number')];?></td>
                        <td><? echo $buyer_library[$row[csf('buyer_name')]];?></td>
                        <td><? echo $row[csf('style_ref_no')]; ?></td>
                        <td align="center"><? echo change_date_format($row[csf('po_received_date')]);?></td>
                        <td align="center"><? echo change_date_format($row[csf('pub_shipment_date')]);?></td>
                        
                        <td><? echo $item_sting; ?></td>
                        <td align="right">
                        <? echo $smv_sting;
                            if($row[csf('order_uom')]!=1)echo '='.$smv_sum;
                         ?>
                        </td>
                        
                        <td align="right"><? echo $row[csf('po_quantity')];?></td>
                        <td align="center">
                            <? 
                                echo $unit_of_measurement[$row[csf('order_uom')]]; 
                                if($row[csf('order_uom')]!=1)echo '<br>('.$set_sting.')';
                            ?>
                        </td>
                        <td align="right"><? echo $set_sum*$row[csf('po_quantity')];$grand_po_qty_pcs+=$set_sum*$row[csf('po_quantity')]; ?></td>
                        <td align="right">
                            <? 
                                $tot_smv=($row[csf('set_smv')]*$row[csf('po_quantity')]); 
                                echo number_format($tot_smv,2); 
                                $grand_tot_smv+=$tot_smv; 
                            ?>
                        </td>
                        <td align="right"><? echo $row[csf('unit_price')];?></td>
                        <td align="right"><? echo $value_usd;$grand_po_val_usd+=$value_usd;?></td>
                        <td align="center"><? echo $user_library[$row[csf('updated_by')]];?></td>
                        <td align="center"><? echo $row[csf('update_date')];?></td>
						<td><? echo $supplier_library[$row[csf('dealing_marchant')]];?></td>
						<td><? echo $row[csf('details_remarks')];?></td>
                   </tr>
                    <?
						$i++;
						$flag=1;
					}
					?>
                    <tfoot bgcolor="#CCCCCC">
                        <td colspan="9">Total</td>
                        <td align="right"><? echo $grand_smv;?></td>
                        <td></td>
                        <td></td>
                        <td align="right"><? echo $grand_po_qty_pcs;?></td>
                        <td align="right"><? echo $grand_tot_smv;?></td>
                        <td></td>
                        <td align="right"><? echo $grand_po_val_usd;?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tfoot>
                 </table>
            </td>
        </tr>
    </table>

<?
		$sum_qty=0;
		$sum_val=0;
		$grant_sum_qty=0;
		$grant_sum_val=0;
		$po_data_arr=array(); 

		
	
	
	$message=ob_get_contents();
	ob_clean();

	//Mail Setup------------------------------------------------------------------------------------
	$mail_item=8;
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=$mail_item and b.mail_user_setup_id=c.id and a.company_id=$compid   and a.IS_DELETED=0 and a.STATUS_ACTIVE=1  and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	$toArr=array();
	foreach($mail_sql as $row)
	{
		$toArr[$row[csf('email_address')]]=$row[csf('email_address')];
	}
	$to=implode(',',$toArr);
 	$subject = "Delete Order List";
	$header=mailHeader();


	if($_REQUEST['isview']==1){
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
//------------------------------------------------------------------------------------Mail Setup;


//echo $message;
 
}
	




?> 
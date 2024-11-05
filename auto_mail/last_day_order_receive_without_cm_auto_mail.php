<?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require '../vendor/phpmailer/phpmailer/PHPMailerAutoload.php';
require_once('setting/mail_setting.php');

$user_library=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");
$company_library=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$buyer_library=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
//$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
$supplier_library = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");
$company_arr=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

$tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),1))),'','',1);
$day_after_tomorrow = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),2))),'','',1);
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
$prev_fifteen_date = change_date_format(date('Y-m-d H:i:s', strtotime('-15 day', strtotime($current_date))),'','',1); 		
$select_fill="to_char(b.update_date,'DD-MM-YYYY HH12:MI:SS')";
 

foreach($company_library as $compid=>$compname)
{
$flag=0;
$countRecords=0;
 
	$sql_count = " select count(*) as rows_num from  wo_po_details_master a, wo_po_break_down b 
		where a.job_no=b.job_no_mst and a.company_name = '$compid' and b.status_active=1 
		and b.insert_date between '".$prev_date."' and '".$current_date."' and a.is_deleted=0	and a.status_active=1";
	$result_count = sql_select( $sql_count );
		
	//echo $result_count['rows_num'];
	foreach( $result_count as $row) 
	{
		$num = $row[csf('rows_num')];
	}
	//echo $num; 
	if($num>0)
	{

ob_start();	
	?>
    
    <table>
        <tr>
            <td align="center">
                <strong style="font-size:24px;"> <? echo $compname; ?></strong>
            </td>
        </tr>
        <tr>
            <td align="center"><strong>Insert Order List Of ( Date : <? echo date('d-m-Y');?> )</strong></td>
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
						<th width="80"><strong>Inhouse Date</strong></th>
                        <th width="100">Item</th>
                        <th width="30">SMV</th>
                        <th width="70"><strong>Order Qty.</strong></th>
                        <th width="50">UOM</th>
                        <th width="100">Order Qty (Pcs)</th>
                        <th width="100">Total SMV</th>
						<th width="70"> CM Cost</th>
                        <th width="70"><strong>Unit Price</strong></th>
                        <th width="100"><strong>PO Value (USD)</strong></th>
                        <th width="80"><strong>Insert By</strong></th>
                        <th width="100"><strong>Insert Date & Time</strong></th>
                        <th width="100"><strong>Dealing Merchant</strong></th>
                        <th><strong>Remarks</strong></th>
                        </tr>
                   </thead>
					<?
					$cm_cost_arr=return_library_array( "select job_no,cm_cost from wo_pre_cost_dtls", "job_no", "cm_cost"  );
					
					$sql = "select 	a.buyer_name,a.dealing_marchant,a.set_smv,a.set_break_down,a.style_ref_no,a.order_uom,b.id,b.job_no_mst, b.is_confirmed, 
							b.po_number,	b.po_received_date,	b.pub_shipment_date,b.po_quantity,b.unit_price,b.inserted_by,b.insert_date,b.details_remarks
						from 
							wo_po_details_master a, wo_po_break_down b 
						where 
							a.job_no=b.job_no_mst 
							and a.company_name = '$compid' 
							and b.status_active=1 
							and b.insert_date between '".$prev_date."' and '".$current_date."'
							and a.is_deleted=0
							and a.status_active=1
							";
					$result = sql_select( $sql );
					//echo $sql; echo '<br>';
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
                        <td align="center">
								<?
									$ship=$row[csf('pub_shipment_date')];
									$po_rcv_date=$row[csf('po_received_date')];
									$dt = new DateTime($ship);
									$date = $dt->format('m/d/Y'); $date1=date_create($date);	$date2=date_create($po_rcv_date);
									
									$diff=date_diff($date2,$date1); 	$print=$diff->format("%R%a");	$days_diff =  substr($print, 1);
												
									if($days_diff>120){$day=40;}else if($days_diff>90){$day=40;}else if($days_diff>75){$day=35;}else if($days_diff>60){$day=30;}else if($days_diff>45){$day=22;}else if($days_diff>30){$day=17;}else{$day=0;}			
									$dt3 = new DateTime($ship);
									$date_ship = $dt3->format('Y-m-d');
									$date3=date_create($date_ship);
									date_sub($date3,date_interval_create_from_date_string("$day days"));
									$m_inhouse_date = date_format($date3,"d-m-Y");
									echo  $m_inhouse_date;	
								?>
						</td>
                        <td><? echo $item_sting; ?></td>
                        <td align="right">
                        <? //echo $smv_sting;
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
                                //echo number_format($tot_smv,2); 
                                $grand_tot_smv+=$tot_smv; 
                            ?>
                        </td>
						<td align="right"><? //echo $cm_cost_arr[$row[csf('job_no_mst')]];?></td>
                        <td align="right"><? echo $row[csf('unit_price')];?></td>
                        <td align="right"><? echo $value_usd;$grand_po_val_usd+=$value_usd;?></td>
                        <td align="center"><? echo $user_library[$row[csf('inserted_by')]];?></td>
                        <td align="center"><? echo $row[csf('insert_date')];?></td>
						<td><? echo $supplier_library[$row[csf('dealing_marchant')]];?></td>
						<td><? echo $row[csf('details_remarks')];?></td>
                   </tr>
                    <?
						$i++;
						$flag=1;
					}
					?>
                    <tfoot bgcolor="#CCCCCC">
                        <td colspan="10">Total</td>
                        <td align="right"><? //echo $grand_smv;?></td>
                        <td></td>
                        
						<td></td>
                        <td align="right"><? echo $grand_po_qty_pcs;?></td>
                        <td align="right"><? //echo $grand_tot_smv;?></td>
						<td></td>
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
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=8 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		//if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
		/*
		
		if($compid==1)
		{
			$to=$to.", ".'minhajul.arefin@gramtechknit.com, ie.shahadat@gramtechknit.com';
		}
		elseif($compid==2)
		{
			$to=$to.", ".'sydur@marsstitchltd.com, finishing@marsstitchltd.com, cutting@marsstitchltd.com, md.robiul@marsstitchltd.com, ibrahim@team.com.bd';
		}
		elseif($compid==3)
		{
			$to=$to.", ".'pavel@brothersfashion-bd.com, emdad@brothersfashion-bd.com, tuhin.Rasul@team.com.bd, bfl_scm@brothersfashion-bd.com, abir@brothersfashion-bd.com';
		}
		elseif($compid==4)
		{
			$to=$to.", ".'sohel@4ajacket.com, zillur.frp@4ajacket.com';
		}
		elseif($compid==5)
		{
			$to=$to.", ".'anwar@cbm-international.com, amir@cbm-international.com, nazmul@cbm-international.com, tuhin.Rasul@team.com.bd';
		}
		else
		{
			$to=$to.", ".'al-amin@team.com.bd, nursat.reza@team.com.bd';
		}
		
		*/
		
		$to=$to.", ".'al-amin@team.com.bd, nursat.reza@team.com.bd, sydur@marsstitchltd.com, finishing@marsstitchltd.com, cutting@marsstitchltd.com, md.robiul@marsstitchltd.com, ibrahim@team.com.bd';
		
		$subject = "New Order List without SMV & CM of ".$company_arr[$compid];
		$mail_body = "Please see the attached file for New Order List of ".$company_arr[$compid];
		
		$message="";
		
		$header=mailHeader();
		$message=ob_get_contents();
		ob_clean();			
		$att_file_arr=array();
		$filename="New_Order_List_".$company_arr[$compid].".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc,$message);
		$att_file_arr[]=$filename.'**'.$filename;
		
		if($compid==2)
		{	
			if($to!=""){echo sendMailMailer( $to, $subject, $mail_body, $from_mail,$att_file_arr );}
		}

		//echo $message;
	
	}
 
}


?> 
<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
//require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$company_library =return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$buyer_library =return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
$supplier_library =return_library_array( "select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);
$previous_date= change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);
$previous_3month_date = change_date_format(date('Y-m-d H:i:s', strtotime('-180 day', strtotime($current_date))),'','',1); 

 

	
	if($db_type==0){
		$str_cond	=" and a.insert_date between '".$previous_date."' and '".$current_date."'";
	}
	else
	{
		$str_cond	=" and a.insert_date between '".$previous_date."' and '".$current_date." 11:59:59 PM'";
	}





//$company_library=array(1=>$company_library[1]);

foreach($company_library as $compid=>$compname)//Knit Grey Fabric Receive
{
	
	ob_start();
?>
<table width="1000">
    <tr>
        <td valign="top" align="center">
            <strong><font size="+1">Total Receive Activities of ( Date :<?  echo date("d-m-Y", strtotime($previous_date));  ?>)</font></strong>
        </td>
    </tr>
    <tr>
        <td valign="top" align="center">
            <strong><? echo $company_library[$compid];  ?></strong>
        </td>
    </tr>
     <tr>
        <td>
          <table width="100%" cellpadding="2" cellspacing="0" rules="all" border="1">
                 	<thead>
                 		<td colspan="9" align="center"><strong>Challan  Wise Knit Grey Fabric Receive</strong></td>
                 	</thead>
                    <tr bgcolor="#EEE">
                        <th>Sl</th>
                        <th>Receive No.</th>
                        <th>Receive Date</th>
                        <th>Insert Date & Time</th>
                        <th>Challan No</th>
                        <th>Challan Qty</th>
                        <th>Receive W/O No</th>
                        <th>W/O Qty</th>
                        <th>Supplier</th>
                    </tr>
                    <?

					$sql="select a.recv_number_prefix_num,a.booking_no,a.knitting_company,a.knitting_source,a.challan_no,a.receive_date,a.insert_date,b.grey_receive_qnty,b.prod_id from inv_receive_master a,pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.entry_form=22 and a.item_category=13 and a.company_id=$compid  $str_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.receive_date";
					$dataArray=sql_select($sql);
					foreach($dataArray as $row)
					{
						$bookingArr[$row[csf('booking_no')]]=$row[csf('booking_no')];
					}
					
						
					$sql="select booking_no,sum(grey_fab_qnty) grey_fab_qnty  from wo_booking_dtls where booking_no in('".implode("','",$bookingArr)."') and is_deleted=0 and status_active=1 group by booking_no";
					$BookingDataArray=sql_select($sql);
					foreach($BookingDataArray as $row)
					{
						$greyFabQntyArr[$row[csf('booking_no')]]+=$row[csf('grey_fab_qnty')];
					}
						

					$i=0;
					foreach($dataArray as $row)
					{
						$i++;
						
						if($row[csf('knitting_source')]==1)	$knit_comp=$company_library[$row[csf('knitting_company')]]; else $knit_comp=$supplier_library[$row[csf('knitting_company')]];
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	  
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td align="center"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                        <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td align="center"><? echo date("d-m-Y h:i:s A",strtotime($row[csf('insert_date')])); ?></td>
                        <td><? echo $row[csf('challan_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('grey_receive_qnty')],2); ?></td>
                        <td align="right"><? echo $row[csf('booking_no')]; ?></td>
                        <td align="right"><? echo number_format($greyFabQntyArr[$row[csf('booking_no')]],2); ?></td>
                        <td align="right"><? echo $knit_comp; ?></td>
                    </tr>
                    <?	
					}
					?> 
                    
                 </table>
            </td>
        </tr>
</table>

<?

		$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=16 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Wise Knit Grey Fabric Receive of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		$header=mailHeader();
		
		if($_REQUEST['isview']==1){
			$mail_item=16;
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





foreach($company_library as $compid=>$compname)//Knit Finish Fabric Receive
{
	
	ob_start();
?>
<table width="1000">
    <tr>
        <td valign="top" align="center">
            <strong><font size="+1">Total Receive Activities of ( Date :<?  echo date("d-m-Y", strtotime($previous_date));  ?>)</font></strong>
        </td>
    </tr>
    <tr>
        <td valign="top" align="center">
            <strong><? echo $company_library[$compid];  ?></strong>
        </td>
    </tr>
        <tr>
            <td>
         <table width="100%" cellpadding="2" cellspacing="0" rules="all" border="1">
                 	<tr>
                 		<td colspan="11" align="center"><strong>Challan  Wise Knit Finish Fabric Receive</strong></td>
                 	</tr>
                    <tr bgcolor="#EEE">
                        <th>Sl</th>
                        <th>Receive No.</th>
                        <th>Receive Date</th>
                        <th>Insert Date & Time</th>
                        <th>Challan No</th>
                        <th>Batch</th>
                        <th>Challan Qty</th>
                        <th>Grey used Qty</th>
                        <th>Receive W/O No</th>
                        <th>W/O Qty</th>
                        <th>Supplier</th>
                    </tr>
                    <?

					$sql="select a.recv_number_prefix_num,a.booking_no,a.knitting_company,a.knitting_source,a.challan_no,a.receive_date,a.insert_date,b.receive_qnty,b.grey_used_qty,b.prod_id,c.batch_no from inv_receive_master a,pro_finish_fabric_rcv_dtls b,pro_batch_create_mst c where a.id=b.mst_id and b.batch_id=c.id and a.entry_form=37 and a.company_id=$compid and a.item_category=2 $str_cond and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 order by a.receive_date";
					$dataArray=sql_select($sql);
					$bookingArr=array();
					foreach($dataArray as $row)
					{
						$bookingArr[$row[csf('booking_no')]]=$row[csf('booking_no')];
					}
					
						
					$sql="select booking_no,sum(wo_qnty) wo_qnty  from wo_booking_dtls where booking_no in('".implode("','",$bookingArr)."') and is_deleted=0 and status_active=1 group by booking_no";
					$bookingDataArray=sql_select($sql);
					$finishFabQntyArr=array();
					foreach($bookingDataArray as $row)
					{
						$finishFabQntyArr[$row[csf('booking_no')]]+=$row[csf('wo_qnty')];
					}
						
					
					
					$i=0;
					foreach($dataArray as $row)
					{
						$i++;
						
						if($row[csf('knitting_source')]==1)	$knit_comp=$company_library[$row[csf('knitting_company')]]; else $knit_comp=$supplier_library[$row[csf('knitting_company')]];
						$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";	  
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td align="center"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                        <td align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                        <td align="center"><? echo date("d-m-Y h:i:s A",strtotime($row[csf('insert_date')])); ?></td>
                        <td><? echo $row[csf('challan_no')]; ?></td>
                        <td align="right"><? echo $row[csf('batch_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('receive_qnty')],2); ?></td>
                        <td align="right"><? echo number_format($row[csf('grey_used_qty')],2); ?></td>
                        <td align="right"><? echo $row[csf('booking_no')]; ?></td>
                        <td align="right"><? echo number_format($finishFabQntyArr[$row[csf('booking_no')]],2); ?></td>
                        <td align="right"><? echo $knit_comp; ?></td>
                    </tr>
                    <?	
					}
					?> 
                    
                 </table>
             </td>
       </tr>
</table>

<?

		$to="";
		$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=17 and b.mail_user_setup_id=c.id and a.company_id=$compid  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
		$mail_sql2=sql_select($sql2);
		foreach($mail_sql2 as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Knit Finish Fabric Receive of ( Date :".date("d-m-Y", strtotime($previous_date)).")";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		$header=mailHeader();

		
		if($_REQUEST['isview']==1){
			$mail_item=17;
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
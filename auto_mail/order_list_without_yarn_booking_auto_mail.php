 <?php
date_default_timezone_set("Asia/Dhaka");
require_once('../includes/common.php');
require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

$company_lib=return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");

$tem_leader_lib = return_library_array("select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0 order by team_leader_name","id","team_leader_name");
$tem_member_lib = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0","id","team_member_name");


$buyer_lib=return_library_array("select id,buyer_name from lib_buyer","id","buyer_name");
$user_lib=return_library_array("select id,user_full_name from user_passwd where valid=1","id","user_full_name");

$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("Y-m-d", $strtotime),0))),'','',1);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-7 day', strtotime($current_date))),'','',1); 
$prev_date2 = change_date_format(date('Y-m-d H:i:s', strtotime('-37 day', strtotime($current_date))),'','',1); 
 
if($db_type==0){
	$str_cond	=" and insert_date between '".$prev_date2."' and '".$prev_date."'";
}
else
{
	$str_cond	=" and b.insert_date between '".$prev_date2."' and '".$prev_date." 11:59:59 PM'";
}


if($db_type==0)
{	
	//$current_date = date("Y-m-d",time());
	$date_diff="(DATEDIFF($current_date, b.PUB_SHIPMENT_DATE))";
}
else
{
	$date_diff="(to_date('$current_date')-to_date(b.PUB_SHIPMENT_DATE, 'dd-MM-yy'))";
}
	
	
	//$prev_date="4-Nov-2017";
	// $company_lib=array(3=>'Test Company');


foreach($company_lib as $compid=>$compname)
{
$flag=0;
ob_start();


	  $orderSql = "select a.ID,A.TEAM_LEADER,A.DEALING_MARCHANT,A.BUYER_NAME,A.STYLE_REF_NO,C.GMTS_ITEM_ID,A.JOB_NO,B.PO_NUMBER,B.INSERT_DATE,B.PO_RECEIVED_DATE,B.PUB_SHIPMENT_DATE,(C.SET_ITEM_RATIO*B.PO_QUANTITY) as PO_QTY_PCS,b.PO_TOTAL_PRICE, B.UNIT_PRICE ,B.IS_CONFIRMED,b.INSERTED_BY  from WO_PO_DETAILS_MASTER a, WO_PO_BREAK_DOWN b,WO_PO_DETAILS_MAS_SET_DETAILS c where A.ID=B.JOB_ID and c.JOB_ID=A.ID AND B.JOB_ID=C.JOB_ID  and A.STATUS_ACTIVE=1 and A.IS_DELETED=0 and B.STATUS_ACTIVE=1 and B.IS_DELETED=0 and a.company_name = $compid $str_cond   ";
	  echo $orderSql;die;
	
	$orderSqlResult = sql_select( $orderSql );
	$i=1;
	$job_id_arr=array();$dataArr=array();
	foreach( $orderSqlResult as $row) 
	{
		
		$dataArr[$row[TEAM_LEADER]][]=$row;	
		$job_id_arr[$row[ID]]=$row[ID];	
	}
	unset($orderSqlResult);
      
//----------------------------------------Yarn Booking
	$yarnSql="select a.JOB_ID,a.JOB_NO from INV_PURCHASE_REQUISITION_DTLS a where a.status_active =1 and a.is_deleted =0 ".where_con_using_array($job_id_arr,0,'a.JOB_ID')."  group by a.JOB_ID,a.JOB_NO";
	 //echo $yarnSql;die;	
	$yarnSqlResult= sql_select($yarnSql) ;
	$yarnBookingJobArr=array();
	foreach($yarnSqlResult as $row) 
	{
		$yarnBookingJobArr[$row[JOB_ID]]=$row[JOB_ID];
	
	}
	unset($yarnSqlResult);
	
	//print_r($yarnBookingJobArr);die;
		
		 
	ob_start();
	
	?>
    
    <table width="100%">
        <tr>
            <td align="center">
                <strong style="font-size:24px;"> <? echo $compname; ?></strong>
            </td>
        </tr>
        <tr>
            <td align="center"><strong>No Yarn Booking Order List On  ( Date : <?= $prev_date;?> )</strong></td>
        </tr>
     </table>
             
      <table cellpadding="0" cellspacing="0" border="1" rules="all">
           <thead>
            <tr bgcolor="#CCCCCC">
                <th width="30">SL</th>
                <th>Team Name</th>	
                <th>Team Member</th>	
                <th>Buyer</th>
                <th>Job No</th>	
                <th>Style</th>
                <th>Item</th>	
                <th>Order No</th>	
                <th>Insert Date</th>	
                <th>PO Recv. Date</th>	
                <th>Ship Date</th>	
                <th>Order Qty (Pcs)	</th>
                <th>Unit Price</th>	
                <th>Value</th>	
                <th>Order Status</th>	
                <th>Insert By</th>
            </tr>
            
           </thead>
           <tbody>
            <?
            $i=1;
			$total_order_qty_pcs_arr=array();
			$total_order_val_arr=array();
            foreach( $dataArr as $tem_leader_id=>$temaLeaderArr) 
            {
				
				foreach( $temaLeaderArr as $row) 
				{
				 if($yarnBookingJobArr[$row[ID]]==''){
					 $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					 $total_order_qty_pcs_arr[$tem_leader_id]+=$row[PO_QTY_PCS];	
					 $total_order_val_arr[$tem_leader_id]+=$row[PO_TOTAL_PRICE];
					
					?>
							
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td align="center"><?= $i;?></td>
						<td><?= $tem_leader_lib[$row[TEAM_LEADER]];?></td>
						<td><?= $tem_member_lib[$row[DEALING_MARCHANT]];?></td>
						<td><?= $buyer_lib[$row[BUYER_NAME]];?></td>
						<td><?= $row[JOB_NO];?></td>
						<td><?= $row[STYLE_REF_NO];?></td>
						<td><?= $garments_item[$row[GMTS_ITEM_ID]];?></td>
						<td><?= $row[PO_NUMBER];?></td>
						<td><?= $row[INSERT_DATE];?></td>
						<td align="center"><?= change_date_format($row[PO_RECEIVED_DATE]);?></td>
						<td align="center"><?= change_date_format($row[PUB_SHIPMENT_DATE]);?></td>
						<td align="right"><?= $row[PO_QTY_PCS];?></td>
						<td align="right"><?= $row[UNIT_PRICE];?></td>
						<td align="right"><?= number_format($row[PO_TOTAL_PRICE],2);?></td>
						<td align="center"><?= $order_status[$row[IS_CONFIRMED]];?></td>
						<td><?= $user_lib[$row[INSERTED_BY]];?></td>
					</tr>
						
					<?
						$i++;
						$flag=1;
					   
				  }
				}
				  
				  echo "<tr bgcolor='#FFFFCC'>
				  	<td align='right' colspan='11'>Total:</td>
				  	<td align='right'>".$total_order_qty_pcs_arr[$tem_leader_id]."</td>
				  	<td></td>
				  	<td align='right'>".number_format($total_order_val_arr[$tem_leader_id],2)."</td>
				  	<td></td>
				  	<td></td>
				  </tr>";
				
              }
            ?>
				  <tr bgcolor="#EEE">
				  	<td align='right' colspan='11'>Grand Total:</td>
				  	<td align='right'><?= array_sum($total_order_qty_pcs_arr)?></td>
				  	<td></td>
				  	<td align='right'><?= number_format(array_sum($total_order_val_arr),2);?></td>
				  	<td></td>
				  	<td></td>
				  </tr>
          </tbody>  
     </table>
                 
     
<?
		
	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=38 and b.mail_user_setup_id=c.id and a.company_id=$compid AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
	
 	$subject = "No Yarn Purchase Requisition";
	
	$message="";
	$message=ob_get_contents();
	
	$header=mailHeader();
	ob_clean();
	//if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail);
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail);
	}


	//echo $message;


 
}
	
	
	




?> 
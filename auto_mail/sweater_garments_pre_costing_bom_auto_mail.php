<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

	$company_library = return_library_array( "select id, company_short_name from lib_company where status_active=1 and is_deleted=0", "id", "company_short_name"  );
	$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );


	$strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
	$current_date = change_date_format(date("Y-m-d H:i:s",strtotime(add_time(date("H:i:s", $strtotime),0))),'','',1);
	$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-50 day', strtotime($current_date))),'','',1);

 

	if($db_type==0)
	{	
		$date_diff="(DATEDIFF('".date('Y-m-d',time())."', a.COSTING_DATE))";
	}
	else
	{
		$date_diff="(to_date('".date('d-M-y',time())."', 'dd-MM-yy')- to_date(a.COSTING_DATE, 'dd-MM-yy'))";
	}


 $sql = " SELECT 
	   c.COMPANY_NAME,
	   c.STYLE_REF_NO,
	   c.BUYER_NAME,
	   c.SET_BREAK_DOWN,
	   a.JOB_NO,
 	   a.COSTING_DATE,
	   $date_diff as DATE_DIFF,
	   MIN(d.PUB_SHIPMENT_DATE) as PUB_SHIPMENT_DATE

  FROM wo_pre_cost_mst a, wo_pre_cost_dtls b ,wo_po_details_master c,wo_po_break_down d
 WHERE a.job_no = b.job_no and a.job_id=c.id and a.job_id=d.job_id and $date_diff>20
    
       AND ( b.fabric_cost IS NULL  OR b.trims_cost IS NULL  OR b.comm_cost IS NULL OR  b.fabric_cost <=0   OR b.trims_cost <=0  OR b.comm_cost <=0 )
       AND b.status_active = 1 and a.COSTING_DATE > '$previous_date'
       AND b.is_deleted = 0  -- AND b.job_no = 'SSL-20-00071'
	   group by c.COMPANY_NAME,c.STYLE_REF_NO,c.BUYER_NAME,c.SET_BREAK_DOWN,a.COSTING_DATE, a.JOB_NO
	   order by c.COMPANY_NAME,a.COSTING_DATE
	   ";




 //-- AND b.job_no = 'SSL-20-00139' or b.fabric_cost =< 0 or b.trims_cost =< 0  or b.comm_cost =< 0
	$dataArr=array();
	$data_array=sql_select($sql);
	foreach($data_array as $rows){
		$dataArr[$rows[COMPANY_NAME]][]=$rows;	
	}
	 unset($data_array);
ob_start();
	 
?>
<table border="1" rules="all">
    
    <?
	
	foreach($dataArr as $data_array){
	?>
    <tr bgcolor="#CCCCCC">
        <th>SL</th>
        <th>Company</th>
        <th>Buyer</th>
        <th>Job</th>
        <th>Style</th>
        <th>Garments Item</th>
        <th>Costing Date</th>
        <th>Shipment Date</th>
        <th>Date Diff</th>
    </tr>
	<?  $i=1;$flag=0;
    foreach($data_array as $rows){
		
		$itemStrArr=array();
		foreach(explode('__',$rows[SET_BREAK_DOWN]) as $row){
			list($item)=explode('_',$row);
			$itemStrArr[$item]=$garments_item[$item];
		}
		?>
		<tr>
			<td align="center"><?= $i++;?></td>
			<td align="center"><?= $company_library[$rows[COMPANY_NAME]];?></td>
			<td><?= $buyer_library[$rows[BUYER_NAME]];?></td>
			<td><?= $rows[JOB_NO];?></td>
			<td><?= $rows[STYLE_REF_NO];?></td>
			<td><?= implode(',',$itemStrArr);?></td>
			<td align="center"><?= $rows[COSTING_DATE];?></td>
			<td align="center"><?= $rows[PUB_SHIPMENT_DATE];?></td>
			<td align="center"><?= $rows[DATE_DIFF];?></td>
		</tr>
		<?
		$flag=1;
		}
	}
	?>
</table>



<?
		$message="";
		$message=ob_get_contents();
		ob_clean();
		
		$to="";
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=32 AND a.MAIL_TYPE=1 and c.IS_DELETED=0 and c.STATUS_ACTIVE=1 and b.mail_user_setup_id=c.id and a.company_id in(".implode(',',array_flip($company_library)).")";
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
		
		$subject="Sweater Garments Pre Costing BOM Auto Mail";
		
		
		
		$header=mailHeader();
		//if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
		if($_REQUEST['isview']==1){
			echo $message;
		}
		else{
			if($to!="" && $flag==1)echo sendMailMailer( $to, $subject, $message, $from_mail, $host, $user_name, $password, $smtp_port );
		}


?>













 
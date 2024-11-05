<?php
date_default_timezone_set("Asia/Dhaka");

require_once('../../includes/common.php');
//require_once('../../mailer/class.phpmailer.php');
require '../../vendor/autoload.php';
require_once('../setting/mail_setting.php');

$company_library = return_library_array( "select id, company_name from lib_company where status_active=1 and is_deleted=0 and id not in(8) and id=3", "id", "company_name"  );
$buyer_library = return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
$user_library = return_library_array( "select id, USER_FULL_NAME from USER_PASSWD where  status_active=1 and is_deleted=0", "id", "USER_FULL_NAME"  );

$strtotime = ($_REQUEST['view_date']) ? strtotime($_REQUEST['view_date']):time();
$current_date = change_date_format(date("Y-m-d H:i:s",$strtotime),'','',1);
$previous_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))),'','',1);



	 //$previous_date=$current_date;
	
	

	$data_con=" and b.UPDATE_DATE between '$previous_date' and '$previous_date 11:59:59 pm'";
	$sql="select a.COMPANY_NAME,a.BUYER_NAME,a.JOB_NO,b.UPDATED_BY,b.SHIPMENT_DATE AS CURRENT_SHIP_DATE,c.SHIPMENT_DATE as UPDATE_SHIP_DATE,B.PO_NUMBER,C.PO_ID,D.BOOKING_NO,b.PO_RECEIVED_DATE,c.PO_RECEIVED_DATE as PO_RECEIVED_DATE_LOG,b.UPDATE_DATE from WO_PO_DETAILS_MASTER a,WO_PO_BREAK_DOWN b,WO_PO_UPDATE_LOG c 
LEFT JOIN WO_BOOKING_MST D ON C.JOB_NO=D.JOB_NO AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0 where a.id=b.job_id and b.id=c.po_id AND a.STATUS_ACTIVE=1 AND a.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0  $data_con ORDER BY C.ID ASC";//and d.BOOKING_TYPE!=6
   //echo $sql;
	$sqlRes=sql_select($sql);
	$dataArr=array();
	foreach($sqlRes as $row)
	{
		$company_arr[$row[COMPANY_NAME]]=$company_library[$row[COMPANY_NAME]];
		$dataArr[data][$row[COMPANY_NAME]][$row[PO_ID]]=$row;
		$dataArr[booking][$row[COMPANY_NAME]][$row[PO_ID]][$row[BOOKING_NO]]=$row[BOOKING_NO];
	}

$receverMailArr=array();
ob_start();
foreach($company_library as $compid=>$compname)
{
?>
        <table border="1" rules="all">
        	<tr>
            	<th colspan="11">
                	<span style="font-size:18px"><?=$compname;?></span><br />
                	<span style="font-size:12px">Shipment Date Revision list of <?=change_date_format($previous_date);?></span>
                </th>
            </tr>
            <tr bgcolor="#CCCCCC">
            	<th>SL</th>
                <th>Buyer</th>
                <th>Job Number</th>
                <th>Fabric Booking</th>
                <th>PO Number</th>
                <th>Prev. Shipdate</th>
                <th>Curr. Shipdate</th>
                <th>Prev. Lead Time</th>
                <th>Curr. Lead Time</th>
                <th>Update Date & Time</th>
                <th>Update By</th>
            </tr>
            <? 
			$i=1;
			foreach($dataArr[data][$compid] as $po_id=>$row){
			$bgcolor=($i%2==0)?"#ffffff":"#D7E8FF";
				
			if(change_date_format($row[UPDATE_SHIP_DATE])==change_date_format($row[CURRENT_SHIP_DATE])){continue;}
			
			$prev_lead_time=datediff( "d",date("Y-m-d",strtotime($row[PO_RECEIVED_DATE])), date("Y-m-d",strtotime($row[UPDATE_SHIP_DATE])) );
			
			$title='Prev. Shipdate:'.date("Y-m-d",strtotime(change_date_format($row[UPDATE_SHIP_DATE]))).',PO Rec:'.date("Y-m-d",strtotime(change_date_format($row[PO_RECEIVED_DATE])));
			
			$curr_lead_time=datediff( "d", date("Y-m-d",strtotime(change_date_format($row[PO_RECEIVED_DATE]))), date("Y-m-d",strtotime(change_date_format($row[CURRENT_SHIP_DATE]))) );
	
				
			?>
        	<tr bgcolor="<? echo $bgcolor; ?>">
            	<td align="center"><?=$i;?></td>
                <td><?=$buyer_library[$row[BUYER_NAME]];?></td>
                <td><?=$row[JOB_NO];?></td>
                <td><p><?=implode(', ',$dataArr[booking][$compid][$po_id]);?></p></td>
                <td><?=$row[PO_NUMBER];?></td>
                <td align="center"><?=change_date_format($row[UPDATE_SHIP_DATE]);?></td>
                <td align="center"><?=change_date_format($row[CURRENT_SHIP_DATE]);?></td>
                <td align="center" title="<?=$title;?>"><?=$prev_lead_time-1;?></td>
                <td align="center"><?=$curr_lead_time;?></td>
                <td align="center"><?=$row[UPDATE_DATE];?></td>
                <td align="center"><?=$user_library[$row[UPDATED_BY]];?></td>
            </tr>
            <?
			$i++;
			}
			?>
        
        
        </table><br />
        
        <?



	$sql = "SELECT a.BRAND_IDS,a.BUYER_IDS,c.email_address as MAIL FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id  and a.mail_item=b.MAIL_ITEM_MST and a.mail_item=84 and b.mail_user_setup_id=c.id and a.company_id =".$compid."  and   A.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1  and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
	 //echo $sql;die;
	
	
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		$receverMailArr[$row[MAIL]]=$row[MAIL];
	}

	
}
	$message=ob_get_contents();
	ob_clean();


	$to=implode(',',array_unique($receverMailArr));
	
		
	$subject = "Shipment Date Revised";
	$header=mailHeader();
	
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		if($_REQUEST['isview']==1){
			$mail_item = 84;
			if($to){
				echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
			}else{
				echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
			}
			echo  $message;
		}
		else{
			if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
		}




?> 
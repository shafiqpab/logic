<?php
date_default_timezone_set("Asia/Dhaka");
if(date('d')!=1){exit('This mail will be send only date of 1st every month');}

require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');

if($db_type==0)
{
	$previous_date= date('Y-m-d', strtotime("first day of -1 month"));
	$current_date = date('Y-m-d', strtotime("last day of -1 month"));
}
else
{
	$previous_date= date('d-M-Y', strtotime("first day of -1 month"));
	$current_date = date('d-M-Y', strtotime("last day of -1 month"));
}

	$date_cond	=" login_date between '".$previous_date."' and '".$current_date."'";
	 
	function date_deffer($start,$end)
	{
		$date_a = new DateTime($start);
		$date_b = new DateTime($end);
		$interval = date_diff($date_a,$date_b);
		if($start && $end)return $interval->format('%h:%i:%s');
	}
	
		function get_hour_min_sec($sec){
		$sec=intval($sec);
		$hour=$sec/3600;
		$hour=intval($hour);
		$sec=$sec-$hour*3600;
		$min=$sec/60;
		$min=intval($min);
		$sec=$sec-$min*60;
		return $hour.':'.$min.":".$sec;
	} 
	
	$sql=" SELECT USER_ID,
		 LOGIN_DATE,
		 MIN (login_time) AS LOGIN,
		 MAX (logout_time) AS LOGOUT
	FROM login_history
	WHERE     $date_cond
	GROUP BY user_id, login_date";
	
	$resultSetArr=sql_select( $sql );
	$user_wise_data_arr=array();
	
	
	$user_name_library=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );
	
	foreach($resultSetArr as $rows)
	{
		$user_wise_data_arr[$rows[csf('USER_ID')]]+=date_deffer($rows[csf('LOGIN')],$rows[csf('LOGOUT')]);
	}
	
	
	

ob_start();

$i=1;
?>


<table cellspacing="0" cellpadding="0" border="1" rules="all" width="380" class="rpt_table" >
    <caption><b>Login History Monthly Report<br/> From <?= $previous_date;?> To <?= $current_date;?></b></caption>
        <thead>
            <th width="40">SL</th>
            <th width="200">User Name</th>
            <th width="">Duration</th>
        </thead>
         <? 
        $i=0;
        foreach ($user_wise_data_arr as $user_id => $value)
        {
            $i++;
            $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                   <td align="center"><? echo $i;?></td>
                   <td><p><?php echo $user_name_library[$user_id]; ?></p></td>
                   <td align="center"><p><?php echo get_hour_min_sec($value*3600);?></p></td>
               </tr>

            <?
        }			   
?>
    
    </table>
</div>



<br />




<?
	/*$sql2 = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=18 and b.mail_user_setup_id=c.id and a.company_id in($companyStr)";
	$mail_sql2=sql_select($sql2, '', '', '', $con);
	foreach($mail_sql2 as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}*/

 	$to='sohel@logicsoftbd.com,mahabub@soniagroup.com,jewel@soniagroup.com,zia@soniagroup.com,khales.rahman@soniagroup.com,anwar.hossain@soniagroup.com,amirul.islam@soniagroup.com';
	$subject="Monthly Login Report of ".date("M-Y", strtotime($from_date))."";
	$message="";
	$message=ob_get_contents();
	ob_clean();
	$header=mailHeader();
	//if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	//echo $message;
	if($_REQUEST['isview']==1){
		echo $message;
	}
	else{
		if($to!=""){echo sendMailMailer( $to, $subject, $message, $from_mail );}
	
	}
		
?>


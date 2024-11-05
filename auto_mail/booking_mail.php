<?
require_once('../includes/common.php');
// require_once('../mailer/class.phpmailer.php');
require_once('setting/mail_setting.php');
 
 
    $company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
    $buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');



    $strtotime = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();

    $sql="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name,a.id from  wo_booking_mst a, approval_history b, wo_po_details_master c  where a.id=b.mst_id and a.job_no=c.job_no  and b.approved_no >1 and  to_char(a.approved_date,'dd-Mon-YYYY')='$strtotime' and b.current_approval_status=1  and b.entry_form=7 order by b.approved_date";
    $sql1="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name,a.id from  wo_booking_mst a, approval_history b,wo_po_details_master c where a.id=b.mst_id  and a.job_no=c.job_no  and  to_char(b.un_approved_date,'dd-Mon-YYYY')='$strtotime' and b.current_approval_status=0  and b.entry_form=7 order by b.approved_date";

 
    // if($db_type==0)
    // {
    //     $current_date = date("Y-m-d",time()- 60 * 60 * 24);
    //     $sql="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name,a.id from  wo_booking_mst a, approval_history b, wo_po_details_master c  where a.id=b.mst_id and a.job_no=c.job_no  and b.approved_no >1 and  date(approved_date)='$current_date' and b.current_approval_status=1  and b.entry_form=7 order by b.approved_date";
    //     $sql1="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name,a.id  from  wo_booking_mst a, approval_history b,wo_po_details_master c where a.id=b.mst_id  and a.job_no=c.job_no  and  date(un_approved_date)='$current_date' and b.current_approval_status=0  and b.entry_form=7 order by b.approved_date";
    // }

    // if($db_type==2)
    // {
    //     $current_date = date("d-M-Y",time()- 60 * 60 * 24);
    //     $sql="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name,a.id from  wo_booking_mst a, approval_history b, wo_po_details_master c  where a.id=b.mst_id and a.job_no=c.job_no  and b.approved_no >1 and  to_char(approved_date,'dd-Mon-YYYY')='$current_date' and b.current_approval_status=1  and b.entry_form=7 order by b.approved_date";
    //     $sql1="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name,a.id from  wo_booking_mst a, approval_history b,wo_po_details_master c where a.id=b.mst_id  and a.job_no=c.job_no  and  to_char(un_approved_date,'dd-Mon-YYYY')='$current_date' and b.current_approval_status=0  and b.entry_form=7 order by b.approved_date";
    // }

 ob_start();
?>

<table class="rpt_table" border="1" rules="all">
    <thead>
        <tr>
            <th colspan="9">Revised Booking</th>
        </tr>
        <tr>
            <th width="30">Sl</th>
            <th width="70">Company</th>
            <th width="80">Job No</th>
            <th width="70">Buyer</th>
            <th width="80">Booking No</th>
            <th width="100">Booking Type</th>
            <th width="30">Revised No</th>
            <th width="120">Revised Date</th>
            <th width="200">Cause</th>
        </tr>
    </thead>
    <tbody>
	<?
    $i=1;
    $sql_data=sql_select($sql);
    foreach($sql_data as $sql_row)
    {
    ?>
    <tr align="center">
        <td width="30"><? echo $i; ?></td>
        <td width="70"><? echo $company_short_name_arr[$sql_row[csf('company_name')]]; ?></td>
        <td width="80"><? echo $sql_row[csf('job_no')]; ?></td>
        <td width="70"><? echo $buyer_short_name_arr[$sql_row[csf('buyer_name')]]; ?></td>
        <td width="80"><? echo $sql_row[csf('booking_no')]; ?></td>
        <td width="100">
		<? 
        if($sql_row[csf('booking_type')]==1)
        {
            $booking_type="Main Fabric Booking";
        }
        if($sql_row[csf('booking_type')]==4)
        {
            $booking_type="Sample Fabric Booking";
        }
        echo $booking_type;
        ?>
    	</td>
        <td width="30"><? echo $sql_row[csf('approved_no')]; ?></td>
        <td width="120"><? echo $sql_row[csf('approved_date')]; ?></td>
        <td width="200"><?
		if($db_type==0)
		{
			$app_no=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."'  and approval_type=1");
		}
		if($db_type==2)
		{
			$app_no=return_field_value("MAX(approval_no)","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."'  and approval_type=1");
		}
		echo $cause=return_field_value("approval_cause","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."' and approval_no='".$app_no."' and approval_type=1");
		 ?></td>
	</tr>
<?
$i++;
}
?>
	</tbody>
</table>

<br/>
<table class="rpt_table" border="0" rules="all">
    <thead>
        <tr>
            <th colspan="9">Un-Approved Booking</th>
        </tr>
        <tr>
            <th width="30">Sl</th>
            <th width="70">Company</th>
            <th width="80">Job No</th>
            <th width="70">Buyer</th>
            <th width="80">Booking No</th>
            <th width="100">Booking Type</th>
            <th width="120">Un-Approved Date</th>
            <th width="200">Cause</th>
        </tr>
    </thead>
	<tbody>
	<?
    $i=1;
    $sql_data1=sql_select($sql1);
    foreach($sql_data1 as $sql_row1)
    {
    ?>
    <tr align="center">
        <td width="30"><? echo $i; ?></td>
        <td width="70"><? echo $company_short_name_arr[$sql_row1[csf('company_name')]]; ?></td>
        <td width="80"><? echo $sql_row1[csf('job_no')]; ?></td>
        <td width="70"><? echo $buyer_short_name_arr[$sql_row1[csf('buyer_name')]]; ?></td>
        <td width="80"><? echo $sql_row1[csf('booking_no')]; ?></td>
        <td width="130">
		<? 
        if($sql_row1[csf('booking_type')]==1)
        {
            $booking_type="Main Fabric Booking";
        }
        if($sql_row1[csf('booking_type')]==4)
        {
            $booking_type="Sample Fabric Booking";
        }
        echo $booking_type;
        ?>
        </td>
        <td width="130"><? echo $sql_row1[csf('un_approved_date')]; ?></td>
        <td width="200"><?
            if($db_type==0)
            {
                $app_no=return_field_value("max(approval_no)","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."'  and approval_type=2");
            }
            if($db_type==2)
            {
                $app_no=return_field_value("MAX(approval_no)","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."'  and approval_type=2");
            }
            echo $cause1=return_field_value("approval_cause","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."' and approval_no='".$app_no."' and approval_type=2"); 
        ?></td>
    </tr>
<?
$i++;
}
?>
</tbody>
</table>
<?
    $message="";
    $message=ob_get_contents();
    ob_clean();

	$to="";	
	$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=5 and b.mail_user_setup_id=c.id and a.IS_DELETED=0 and b.IS_DELETED=0 and c.IS_DELETED=0 and A.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.STATUS_ACTIVE=1 and c.STATUS_ACTIVE=1";
	$mail_sql=sql_select($sql);
	foreach($mail_sql as $row)
	{
		if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
	}
		
	//$to="shabbir@logicsoftbd.com";
	$subject="Revised Booking Status";

	
	$header=mailHeader();

    if($_REQUEST['isview']==1){
        $mail_item=5;
        if($to){
			echo 'Mail Item:'.$form_list_for_mail[$mail_item].'=>'.$to;
		}else{
			echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>".$form_list_for_mail[$mail_item]."</b>]<br>";
		}
        echo $message;
    }
    else{
        echo sendMailMailer( $to, $subject, $message, $from_mail );
    }
		
?>
 
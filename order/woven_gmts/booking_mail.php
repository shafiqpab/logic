<?
/*--------------------------------------------Comments----------------
Version (MySql)          :  V1
Version (Oracle)         :  V1
Converted by             :  MONZU
Converted Date           :  
Purpose			         : 	This form will create Bom Process
Functionality	         :	
JS Functions	         :
Created by		         :	Monzu 
Creation date 	         : 	17-11-2014
Requirment Client        : 
Requirment By            : 
Requirment type          : 
Requirment               : 
Affected page            : 
Affected Code            :                   
DB Script                : 
Updated by 		         : 		
Update date		         : 		   
QC Performed BY	         :		
QC Date			         :	
Comments		         :
----------------------------------------------------------------------*/

//session_start();
//if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../includes/common.php');
require_once('../../mailer/class.phpmailer.php');
extract($_REQUEST);
//$_SESSION['page_permission']=$permission;
echo load_html_head_contents("Order Info","../../", 1, 1, $unicode,1,'');
?>	
<script>

</script>
</head>
<body>
<?

$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');


if($db_type==0)
{
  $current_date = date("Y-m-d",time()- 60 * 60 * 24);
  $sql="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name from  wo_booking_mst a, approval_history b, wo_po_details_master c  where a.id=b.mst_id and a.job_no=c.job_no  and b.approved_no >1 and  date(approved_date)='$current_date' and b.current_approval_status=1  and b.entry_form=7 ";
  $sql1="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name  from  wo_booking_mst a, approval_history b,wo_po_details_master c where a.id=b.mst_id  and a.job_no=c.job_no  and  date(un_approved_date)='$current_date' and b.current_approval_status=0  and b.entry_form=7 ";
}

if($db_type==2)
{
 $current_date = date("d-M-Y",time()- 60 * 60 * 24);
 $sql="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name from  wo_booking_mst a, approval_history b, wo_po_details_master c  where a.id=b.mst_id and a.job_no=c.job_no  and b.approved_no >1 and  to_char(approved_date,'mm-dd-YYYY')='$current_date' and b.current_approval_status=1  and b.entry_form=7";

 $sql1="Select a.booking_no,a.job_no,a.booking_type,b.approved_no,b.approved_date,b.un_approved_date,b.current_approval_status,c.buyer_name,c.company_name  from  wo_booking_mst a, approval_history b,wo_po_details_master c where a.id=b.mst_id  and a.job_no=c.job_no  and  to_char(un_approved_date,'mm-dd-YYYY')='$current_date' and b.current_approval_status=0  and b.entry_form=7 ";
}
 ob_start();
?>

<table class="rpt_table" border="1" rules="all">
<thead>
<tr>
<th colspan="8">Revised Booking</th>
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
<th colspan="8">Un-Approved Booking</th>
</tr>
<tr>
<th width="30">Sl</th>
<th width="70">Company</th>
<th width="80">Job No</th>
<th width="70">Buyer</th>
<th width="80">Booking No</th>
<th width="100">Booking Type</th>
<th width="120">Un-Approved Date</th>
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
</tr>
<?
$i++;
}
?>
</tbody>
</table>
<?
        $to="";
		$sql = "SELECT c.email_address FROM mail_group_mst a, mail_group_child b, user_mail_address c where b.mail_group_mst_id=a.id and a.mail_item=5 and b.mail_user_setup_id=c.id and a.company_id=1";
		
		$mail_sql=sql_select($sql);
		foreach($mail_sql as $row)
		{
			if ($to=="")  $to=$row[csf('email_address')]; else $to=$to.", ".$row[csf('email_address')]; 
		}
 		$subject="Revised Booking Status";
    	$message="";
    	$message=ob_get_contents();
    	ob_clean();
		$header=mail_header();
		
		echo send_mail_mailer( $to, $subject, $message, $from_mail );
		
		/*if (mail($to,$subject,$message,$header))
			echo "****Mail Sent.---".date("Y-m-d");
		else
			echo "****Mail Not Sent.---".date("Y-m-d");*/
?>

</body>
</html>
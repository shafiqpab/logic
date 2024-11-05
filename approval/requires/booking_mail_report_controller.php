<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 160, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}
 
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');



if($action=="report_generate")
{
  	//echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_date_from=$txt_date_from." 12:00:01 AM";
	$txt_date_to=$txt_date_to." 11:59:59 PM";
	$sql_cond="";
	if($cbo_company_name!=0) $sql_cond="and c.company_name='$cbo_company_name' ";
	if($cbo_buyer_name!=0) $sql_cond .="and c.buyer_name='$cbo_buyer_name' ";
	//if($cbo_search_type!=0) $sql_cond .="and c.buyer_name='$cbo_buyer_name' ";
	/*$date_cond="";
	if($txt_date_from!="" && $txt_date_to!="") 
	{
		if($cbo_search_type==1 || $cbo_search_type==3)
		{
			$date_cond="b.approved_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else if($cbo_search_type==2)
		{
			$date_cond="b.un_approved_date between '$txt_date_from' and '$txt_date_to' ";
		}
		else
		{
			$date_cond="";
		}
	}*/
	if($db_type==0)
	{
		$group_ap_date=" DATE_FORMAT(approved_date, '%Y%m%d')";
		$group_un_ap_date=" DATE_FORMAT(un_approved_date, '%Y%m%d')";
	} 
	else if($db_type==2)  
	{
		$group_ap_date=" to_char(approved_date, 'YYYY-MM-DD')";
		$group_un_ap_date=" to_char(un_approved_date, 'YYYY-MM-DD')";
	}
	
	if($cbo_search_type==1 ||$cbo_search_type==0)
	{
		$sql_revised=sql_select("Select a.booking_no,max(a.job_no) as job_no,max(a.booking_type) as booking_type,max(b.approved_no) as approved_no,max(b.approved_date) as approved_date,max(b.un_approved_date) as un_approved_date,max(b.current_approval_status) as current_approval_status,max(c.buyer_name) as buyer_name,max(c.company_name) as company_name
		from  wo_booking_mst a, approval_history b, wo_po_details_master c  
		where a.id=b.mst_id and a.job_no=c.job_no  and b.approved_no >1 and  approved_date between '$txt_date_from' and '$txt_date_to'  and b.entry_form=7 and b.current_approval_status=1
		group by a.booking_no , $group_ap_date 
		order by $group_ap_date");
	}
	if($cbo_search_type==2 ||$cbo_search_type==0)
	{
		$sql_unapprove=sql_select("Select a.booking_no,max(a.job_no) as job_no,max(a.booking_type) as booking_type,max(b.approved_no) as approved_no,max(b.approved_date) as approved_date,max(b.un_approved_date) as un_approved_date,max(b.current_approval_status) as current_approval_status,max(c.buyer_name) as buyer_name,max(c.company_name) as company_name 
		from  wo_booking_mst a, approval_history b,wo_po_details_master c 
		where a.id=b.mst_id  and a.job_no=c.job_no  and  un_approved_date between '$txt_date_from' and '$txt_date_to' and b.current_approval_status=0  and b.entry_form=7 
		group by a.booking_no, $group_un_ap_date
		order by $group_ap_date");
	}
	if($cbo_search_type==3 ||$cbo_search_type==0)
	{
		$sql_approve=sql_select("Select a.booking_no,max(a.job_no) as job_no,max(a.booking_type) as booking_type,max(b.approved_no) as approved_no,max(b.approved_date) as approved_date,max(b.un_approved_date) as un_approved_date,max(b.current_approval_status) as current_approval_status,max(c.buyer_name) as buyer_name,max(c.company_name) as company_name  
		from  wo_booking_mst a, approval_history b,wo_po_details_master c 
		where a.id=b.mst_id  and a.job_no=c.job_no  and  approved_date between '$txt_date_from' and '$txt_date_to' and b.current_approval_status=1  and b.entry_form=7 
		group by a.booking_no , $group_ap_date
		order by $group_ap_date");
	}
	//echo $sql_approve;die;
	
	
	$approval_no_array=return_library_array( "select booking_id, max(approval_no) as approval_no from fabric_booking_approval_cause where entry_form=7 and approval_type=1 group by booking_id",'booking_id','approval_no');
	$approval_no_1_array=return_library_array( "select booking_id, max(approval_no) as approval_no from fabric_booking_approval_cause where entry_form=7 and approval_type=2 group by booking_id",'booking_id','approval_no');
	ob_start();
?>

    <table class="rpt_table" border="1" rules="all" cellpadding="0" cellspacing="0" id="table_header" width="810" align="center">
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
                <th width="60">Revised No</th>
                <th width="120">Revised Date</th>
                <th >Cause</th>
            </tr>
        </thead>
    </table>
    <div style="width:810px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body" align="center">
    <table width="792" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="center">
        <tbody>
			<?
            $i=1;
            foreach($sql_revised as $sql_row)
            {
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				?>
				<tr align="center" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30"><p><? echo $i; ?></p></td>
                    <td width="70"><p><? echo $company_short_name_arr[$sql_row[csf('company_name')]]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $sql_row[csf('job_no')]; ?>&nbsp;</p></td>
                    <td width="70"><p><? echo $buyer_short_name_arr[$sql_row[csf('buyer_name')]]; ?>&nbsp;</p></td>
                    <td width="80"><p><? echo $sql_row[csf('booking_no')]; ?>&nbsp;</p></td>
                    <td width="100"><p>
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
                        ?>&nbsp;</p>
                    </td>
                    <td width="60"><p><? echo $sql_row[csf('approved_no')]; ?></td>
                    <td width="120"><p><? echo $sql_row[csf('approved_date')]; ?></td>
                    <td ><p>
					<?
						$app_no=$approval_no_array[$sql_row1[csf('id')]];
						$cause=return_field_value("approval_cause","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."' and approval_no='".$app_no."' and approval_type=1");
						echo $cause;
                    ?>&nbsp;</p>
                    </td>
				</tr>
				<?
				$i++;
            }
            ?>
        </tbody>
    </table>
    </div>
	<br/> 
    <table class="rpt_table" border="1" rules="all"  cellpadding="0" cellspacing="0" id="table_header2" width="780" align="center">
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
                <th width="130">Booking Type</th>
                <th width="130">Un-Approved Date</th>
                <th >Cause</th>
            </tr>
        </thead>
    </table>
    <div style="width:780px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="center">
    <table width="762" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="center">
        <tbody>
        <?
        $j=1;
        $sql_data1=sql_select($sql1);
        foreach($sql_unapprove as $sql_row1)
        {
			if ($j%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
			<tr align="center" bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="30"><p><? echo $j; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $company_short_name_arr[$sql_row1[csf('company_name')]]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $sql_row1[csf('job_no')]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $buyer_short_name_arr[$sql_row1[csf('buyer_name')]]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $sql_row1[csf('booking_no')]; ?>&nbsp;</p></td>
                <td width="130"><p>
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
                    ?>&nbsp;</p>
                </td>
                <td width="130"><p><? echo $sql_row1[csf('un_approved_date')]; ?>&nbsp;</p></td>
                <td ><p>
				<?
					$app_no=$approval_no_1_array[$sql_row1[csf('id')]];;
					$cause1=return_field_value("approval_cause","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."' and approval_no='".$app_no."' and approval_type=2");
					echo  $cause1;
                ?>&nbsp;</p>
                </td>
			</tr>
			<?
			$i++;
			$j++;
        }
        ?>
        </tbody>
    </table>
    </div>
	<br/>
    <table class="rpt_table" border="1" rules="all"  cellpadding="0" cellspacing="0" id="table_header3" width="780" align="center">
        <thead>
            <tr>
            	<th colspan="8">Approved Booking</th>
            </tr>
            <tr>
                <th width="30">Sl</th>
                <th width="70">Company</th>
                <th width="80">Job No</th>
                <th width="70">Buyer</th>
                <th width="80">Booking No</th>
                <th width="130">Booking Type</th>
                <th width="130">Un-Approved Date</th>
                <th >Cause</th>
            </tr>
        </thead>
    </table>
    <div style="width:780px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body3" align="center">
    <table width="762" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="center">
        <tbody>
        <?
        $k=1;
        foreach($sql_approve as $sql_row1)
        {
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			?>
			<tr align="center"  bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                <td width="30"><p><? echo $k; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $company_short_name_arr[$sql_row1[csf('company_name')]]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $sql_row1[csf('job_no')]; ?>&nbsp;</p></td>
                <td width="70"><p><? echo $buyer_short_name_arr[$sql_row1[csf('buyer_name')]]; ?>&nbsp;</p></td>
                <td width="80"><p><? echo $sql_row1[csf('booking_no')]; ?>&nbsp;</p></td>
                <td width="130"><p>
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
                    ?>&nbsp;</p>
                </td>
                <td width="130"><p><? echo $sql_row1[csf('un_approved_date')]; ?>&nbsp;</p></td>
                <td><p>
				<?
					$app_no=$approval_no_1_array[$sql_row1[csf('id')]];;
					$cause1=return_field_value("approval_cause","fabric_booking_approval_cause","entry_form=7 and booking_id='".$sql_row1[csf('id')]."' and approval_no='".$app_no."' and approval_type=2");
					echo  $cause1;
                ?>&nbsp;</p>
                </td>
			</tr>
			<?
			$i++;
			$k++;
        }
        ?>
        </tbody>
    </table>
    </div>
<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}



        //$to="beeresh.apu1974@gmail.com";
		/*$to="";
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
		
		echo send_mail_mailer( $to, $subject, $message, $from_mail );*/
		
		/*if (mail($to,$subject,$message,$header))
			echo "****Mail Sent.---".date("Y-m-d");
		else
			echo "****Mail Not Sent.---".date("Y-m-d");*/
?>
 